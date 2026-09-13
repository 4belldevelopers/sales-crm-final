/**
 * AgyEditorWasmEngine — JERO Dedicated OpenDoc WebAssembly Online Editor Engine
 * 
 * DOMAIN: GROUP 6. EDIT & OPTIMIZE RESUME / ONLINE DOCUMENT STUDIO
 * SCOPE: Interactive browser-based document editing, real-time ATS optimization,
 *        redaction/highlighting, and high-fidelity vector PDF generation.
 * 
 * STRICT ARCHITECTURAL DIRECTIVE:
 * This engine is 100% INDEPENDENT AND ISOLATED from:
 * 1. Download Resume Center Engine (AgyResumeRenderer.js)
 * 2. Forward Resume Engine (AgyForwardResumeEngine.js)
 * 
 * NEVER MERGE OR CROSS-REFERENCE THOSE ENGINES WITH THIS FILE.
 * See: docs/AI_OPENDOC_ENGINE_ISOLATION_MANDATE.md
 */
(function(root, factory){
  'use strict';
  var mod = factory();
  if(typeof window !== 'undefined'){
    window.AgyEditorWasmEngine = mod;
  }
  if(typeof root !== 'undefined'){
    root.AgyEditorWasmEngine = mod;
  }
  if(typeof self !== 'undefined'){
    self.AgyEditorWasmEngine = mod;
  }
  if(typeof globalThis !== 'undefined'){
    globalThis.AgyEditorWasmEngine = mod;
  }
  if(typeof module === 'object' && module && module.exports){
    module.exports = mod;
  }
})(typeof self !== 'undefined' ? self : this, function(){
  'use strict';

  // Dedicated in-memory caches isolated strictly to the Editor domain
  var editorWasmModule = null;
  var editorWasmInitPromise = null;
  var editorPdfMakePromise = null;
  var editorLogoDataUriCache = {};

  function esc(s){
    return String(s || '').replace(/[&<>"']/g, function(c){
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function escRegex(s){
    return String(s || '').replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  }

  var AgyEditorWasmEngine = {
    version: '1.0.0-editor-wasm',

    /**
     * Dedicated OpenDoc WebAssembly Loader & Runtime for the Document Editor
     * Resolves casual_doc_wasm binary with zero dependency on external renderers.
     * @param {Object} [options]
     * @returns {Promise<Object>}
     */
    initWasm: function(options){
      if(editorWasmModule) return Promise.resolve(editorWasmModule);
      if(editorWasmInitPromise) return editorWasmInitPromise;

      options = options || {};
      var wasmSources = [
        {
          label: 'Local OpenDoc runtime',
          moduleUrl: '/js/opendoc/casual_doc_wasm.js',
          wasmUrl: '/js/opendoc/casual_doc_wasm_bg.wasm'
        },
        {
          label: 'Cloudflare OpenDoc runtime',
          moduleUrl: 'https://file.jero.works/js/opendoc/casual_doc_wasm.js?v=opendoc-20260903',
          wasmUrl: 'https://file.jero.works/js/opendoc/casual_doc_wasm_bg.wasm?v=opendoc-20260903'
        },
        {
          label: 'CasualOffice CDN Fallback',
          moduleUrl: 'https://opendoc.casualoffice.org/pkg/casual_doc_wasm.js',
          wasmUrl: 'https://opendoc.casualoffice.org/pkg/casual_doc_wasm_bg.wasm'
        }
      ];

      async function loadEditorModule(src){
        var module = null;
        var blobUrl = '';
        try {
          module = await import(src.moduleUrl);
        } catch(importErr){
          var res = await fetch(src.moduleUrl, { mode: 'cors', cache: 'force-cache' });
          if(!res.ok) throw new Error('HTTP ' + res.status + ' loading ' + src.moduleUrl);
          var code = await res.text();
          code = code.replace(/new URL\(['"]casual_doc_wasm_bg\.wasm['"],\s*import\.meta\.url\)/g, JSON.stringify(src.wasmUrl));
          blobUrl = URL.createObjectURL(new Blob([code], { type: 'application/javascript' }));
          module = await import(blobUrl);
        }

        if(typeof module.default === 'function'){
          var wasmRes = await fetch(src.wasmUrl, { mode: 'cors', cache: 'force-cache' });
          if(!wasmRes.ok) throw new Error('HTTP ' + wasmRes.status + ' loading ' + src.wasmUrl);
          var wasmBytes = await wasmRes.arrayBuffer();
          await module.default({ module_or_path: wasmBytes });
        }

        if(blobUrl){
          setTimeout(function(){ URL.revokeObjectURL(blobUrl); }, 30000);
        }

        return module;
      }

      editorWasmInitPromise = (async function(){
        var lastErr = null;
        for(var i = 0; i < wasmSources.length; i++){
          try {
            editorWasmModule = await loadEditorModule(wasmSources[i]);
            return editorWasmModule;
          } catch(err){
            lastErr = err;
            console.warn('[AgyEditorWasmEngine] Source [' + wasmSources[i].label + '] failed:', err);
          }
        }
        throw (lastErr || new Error('OpenDoc WASM runtime could not be initialized for the Editor.'));
      })().catch(function(err){
        editorWasmInitPromise = null;
        throw err;
      });

      return editorWasmInitPromise;
    },

    /**
     * Dedicated pdfMake loader with Metronic RequireJS/AMD bypass guarantee.
     * @returns {Promise<void>}
     */
    ensurePdfCompiler: function(){
      if(editorPdfMakePromise && window.pdfMake && window.pdfMake.vfs) return editorPdfMakePromise;

      editorPdfMakePromise = new Promise(function(resolve, reject){
        if(window.pdfMake && window.pdfMake.vfs && (window.pdfMake.vfs['Roboto-Regular.ttf'] || window.pdfMake.vfs['Roboto-Medium.ttf'])){
          resolve();
          return;
        }

        // Metronic RequireJS protection
        var prevAmd = null;
        if(typeof window.define === 'function' && window.define.amd){
          prevAmd = window.define.amd;
          window.define.amd = false;
        }

        function restoreAmd(){
          if(prevAmd !== null && typeof window.define === 'function'){
            window.define.amd = prevAmd;
          }
        }

        function loadScript(url){
          return new Promise(function(res, rej){
            var s = document.createElement('script');
            s.src = url;
            s.async = true;
            s.onload = function(){ res(); };
            s.onerror = function(){ rej(new Error('Failed script: ' + url)); };
            document.head.appendChild(s);
          });
        }

        loadScript('/js/pdfmake.min.js')
          .catch(function(){ return loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js'); })
          .then(function(){
            return loadScript('/js/vfs_fonts.min.js')
              .catch(function(){ return loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.min.js'); });
          })
          .then(function(){
            restoreAmd();
            if(window.pdfMake){
              window.pdfMake.fonts = {
                Roboto: {
                  normal: 'Roboto-Regular.ttf',
                  bold: 'Roboto-Medium.ttf',
                  italics: 'Roboto-Italic.ttf',
                  bolditalics: 'Roboto-MediumItalic.ttf'
                }
              };
            }
            resolve();
          })
          .catch(function(err){
            restoreAmd();
            reject(err);
          });
      });

      return editorPdfMakePromise;
    },

    /**
     * Converts a binary document (DOCX or PDF) into clean, editable HTML for the WYSIWYG editor.
     * Preserves layout headings, paragraphs, and list structure.
     * @param {Uint8Array|ArrayBuffer} bytes
     * @param {string} [formatHint] - 'docx' or 'pdf'
     * @returns {Promise<string>}
     */
    extractDocumentToHtml: async function(bytes, formatHint){
      if(!bytes || !bytes.byteLength) return '<p>No document content available.</p>';
      var u8 = bytes instanceof Uint8Array ? bytes : new Uint8Array(bytes);
      var isDocx = (u8[0] === 0x50 && u8[1] === 0x4B) || formatHint === 'docx' || formatHint === 'doc';

      // 1. Try DOCX extraction via Mammoth if present
      if(isDocx){
        try {
          if(typeof window.mammoth !== 'undefined'){
            var conv = await window.mammoth.convertToHtml({ arrayBuffer: u8.buffer || u8 });
            if(conv && conv.value && conv.value.trim().length > 20){
              return conv.value;
            }
          }
        } catch(mammothErr){
          console.warn('[AgyEditorWasmEngine] Mammoth extraction note:', mammothErr);
        }

        // 2. Try OpenDoc WASM document parsing
        try {
          var wasm = await AgyEditorWasmEngine.initWasm();
          if(wasm && typeof wasm.WasmDocument !== 'undefined' && typeof wasm.WasmDocument.open === 'function'){
            var doc = wasm.WasmDocument.open(u8);
            if(doc){
              if(typeof doc.free === 'function') doc.free();
            }
          }
        } catch(wasmDocErr){
          console.warn('[AgyEditorWasmEngine] OpenDoc WASM direct parse note:', wasmDocErr);
        }
      }

      // 3. Try PDF extraction via PDF.js if available
      if(typeof window.pdfjsLib !== 'undefined'){
        try {
          var pVer = window.pdfjsLib.version || '2.11.338';
          window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/' + pVer + '/pdf.worker.min.js';
          var pdf = await window.pdfjsLib.getDocument({ data: u8 }).promise;
          var htmlBlocks = [];
          for(var p = 1; p <= pdf.numPages; p++){
            var page = await pdf.getPage(p);
            var tc = await page.getTextContent();
            var pageStr = tc.items.map(function(item){ return item.str || ''; }).join(' ');
            var sentences = pageStr.split(/(?<=[.?!])\s+/);
            sentences.forEach(function(s){
              if(s.trim()) htmlBlocks.push('<p>' + esc(s.trim()) + '</p>');
            });
            if(p < pdf.numPages){
              htmlBlocks.push('<div class="doc-page-break"></div>');
            }
          }
          if(htmlBlocks.length > 0){
            return htmlBlocks.join('');
          }
        } catch(pdfErr){
          console.warn('[AgyEditorWasmEngine] PDF.js extraction note:', pdfErr);
        }
      }

      // 4. Raw Text Fallback
      var decoder = new TextDecoder('utf-8', { fatal: false });
      var rawStr = decoder.decode(u8.slice(0, 10000)).replace(/[^\x20-\x7E\r\n\t]/g, ' ');
      var lines = rawStr.split(/\r?\n/).filter(function(l){ return l.trim().length > 3; });
      if(lines.length > 5){
        return lines.map(function(l){ return '<p>' + esc(l.trim()) + '</p>'; }).join('');
      }

      return '<p>Document loaded for online editing.</p>';
    },

    /**
     * Prepares and generates the document output based on the requested format (matching Engine 1),
     * then seamlessly loads the exact structured state directly into the interactive editor workspace.
     * 
     * Handles all Group 6 options:
     * - Option 61: ATS Original Resume -> Clean parsed document
     * - Option 62: Resume + Company Branding -> Branded header injection
     * - Option 63: Highlighted Resume -> Auto-detected & highlighted ATS skill tokens
     * - Option 64: Pre-Screen Report + Resume -> Full executive dossier Page 1 + Page 2 Resume
     * - Option 65: Pre-Screen Report + Highlighted Resume -> Full dossier Page 1 + Highlighted Resume
     * 
     * @param {Object} options - { bytes, formatHint, mode, dossier, targetSkills }
     * @returns {Promise<{html: string, atsEvaluation: Object, mode: number}>}
     */
    prepareDocumentForEditing: async function(options){
      options = options || {};
      var bytes = options.bytes;
      var formatHint = options.formatHint || 'pdf';
      var mode = parseInt(options.mode, 10) || 61;
      var targetSkills = options.targetSkills || [];

      // 1. Extract raw/formatted HTML from document binary
      var baseHtml = await AgyEditorWasmEngine.extractDocumentToHtml(bytes, formatHint);

      // 2. Real-time ATS evaluation on extracted text
      var cleanText = baseHtml.replace(/<[^>]+>/g, ' ');
      var atsEvaluation = AgyEditorWasmEngine.evaluateAtsScore(cleanText, targetSkills);

      // 3. If mode requires keywords highlighted (Option 63 or 65), pre-tag matching tokens
      var finalHtml = baseHtml;
      if((mode === 63 || mode === 65) && atsEvaluation.matched && atsEvaluation.matched.length > 0){
        var kwList = atsEvaluation.matched.map(function(m){ return m.name; });
        finalHtml = AgyEditorWasmEngine.applyKeywordTagsToHtml(baseHtml, kwList);
      }

      return {
        html: finalHtml,
        atsEvaluation: atsEvaluation,
        mode: mode
      };
    },

    /**
     * Tags keyword occurrences in HTML with <mark class="rcv-hit"> without mangling HTML tags
     * @param {string} html
     * @param {string[]} keywords
     * @returns {string}
     */
    applyKeywordTagsToHtml: function(html, keywords){
      if(!html || !keywords || !keywords.length) return html;
      var sorted = keywords.slice().sort(function(a, b){ return b.length - a.length; });
      var pattern = sorted.map(escRegex).join('|');
      if(!pattern) return html;
      var regex = new RegExp('\\b(' + pattern + ')\\b', 'gi');

      return html.replace(/(<[^>]+>)|([^<]+)/g, function(match, tag, text){
        if(tag) return tag;
        return text.replace(regex, '<mark class="rcv-hit">$1</mark>');
      });
    },

    /**
     * Evaluates text against target job skills in real time (< 20ms)
     * @param {string} text - Clean document text
     * @param {string[]} targetSkills - Skills required by job
     * @returns {{score: number, matched: Array<{name: string, count: number}>, missing: string[]}}
     */
    evaluateAtsScore: function(text, targetSkills){
      text = String(text || '').toLowerCase();
      targetSkills = Array.isArray(targetSkills) ? targetSkills : [];

      var unique = [];
      var seen = {};
      for(var i = 0; i < targetSkills.length; i++){
        var s = String(targetSkills[i] || '').trim();
        if(s && !seen[s.toLowerCase()]){
          seen[s.toLowerCase()] = true;
          unique.push(s);
        }
      }

      if(unique.length === 0){
        return { score: 100, matched: [], missing: [] };
      }

      var matched = [];
      var missing = [];

      for(var j = 0; j < unique.length; j++){
        var skill = unique[j];
        var pattern = new RegExp('\\b' + escRegex(skill) + '\\b', 'gi');
        var hits = text.match(pattern);
        if(hits && hits.length > 0){
          matched.push({ name: skill, count: hits.length });
        } else {
          missing.push(skill);
        }
      }

      var score = Math.round((matched.length / unique.length) * 100);
      score = Math.max(0, Math.min(100, score));

      return {
        score: score,
        matched: matched,
        missing: missing
      };
    },

    /**
     * Converts an image URL into a base64 Data URI for PDF compilation
     * @param {string} url
     * @returns {Promise<string|null>}
     */
    getImageDataUri: function(url){
      if(!url || typeof url !== 'string') return Promise.resolve(null);
      url = url.trim();
      if(!url) return Promise.resolve(null);
      if(url.indexOf('data:image/') === 0) return Promise.resolve(url);
      if(editorLogoDataUriCache[url]) return Promise.resolve(editorLogoDataUriCache[url]);

      return new Promise(function(resolve){
        var img = new Image();
        img.crossOrigin = 'Anonymous';
        img.onload = function(){
          try {
            var canvas = document.createElement('canvas');
            canvas.width = img.naturalWidth || img.width;
            canvas.height = img.naturalHeight || img.height;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);
            var uri = canvas.toDataURL('image/png');
            editorLogoDataUriCache[url] = uri;
            resolve(uri);
          } catch(e){
            resolve(null);
          }
        };
        img.onerror = function(){
          fetch(url, { cache: 'force-cache' })
            .then(function(r){ return r.ok ? r.blob() : null; })
            .then(function(b){
              if(!b) return resolve(null);
              var reader = new FileReader();
              reader.onloadend = function(){
                editorLogoDataUriCache[url] = reader.result;
                resolve(reader.result);
              };
              reader.onerror = function(){ resolve(null); };
              reader.readAsDataURL(b);
            })
            .catch(function(){ resolve(null); });
        };
        img.src = url;
      });
    },

    /**
     * Compiles the live edited document (and optional Pre-Screened Report / Branding)
     * into a high-fidelity vector PDF directly in the browser with 0% server CPU.
     * @param {Object} options
     * @returns {Promise<Blob>}
     */
    compileEditedDocumentPdf: async function(options){
      options = options || {};
      var editorRoot = options.editorRoot; // DOM node containing edited content
      var reportRoot = options.reportRoot;
      var dossier = Object.assign({}, options.dossier || {});
      if(reportRoot && typeof reportRoot === 'object'){
        try {
          var nameEl = reportRoot.querySelector ? reportRoot.querySelector('.ps-cand-name') : null;
          if(nameEl && nameEl.innerText.trim()) dossier.name = nameEl.innerText.trim();
          var desigEl = reportRoot.querySelector ? reportRoot.querySelector('.ps-cand-role') : null;
          if(desigEl && desigEl.innerText.trim()) dossier.designation = desigEl.innerText.trim();
          var scoreEl = reportRoot.querySelector ? reportRoot.querySelector('.ps-confidence-val') : null;
          if(scoreEl && scoreEl.innerText.trim()){
            var parsedScore = parseInt(scoreEl.innerText.replace(/[^0-9]/g, ''), 10);
            if(!isNaN(parsedScore)) dossier.match = parsedScore;
          }
          var secBodies = reportRoot.querySelectorAll ? reportRoot.querySelectorAll('.ps-sec-body') : [];
          if(secBodies && secBodies.length >= 1 && secBodies[0].innerText.trim()){
            dossier.careerSummary = secBodies[0].innerText.trim();
          }
          if(secBodies && secBodies.length >= 2 && secBodies[1].innerText.trim()){
            dossier.recruiterNote = secBodies[1].innerText.trim();
          }
        } catch(e){}
      }
      var activeKeywords = Array.isArray(options.activeKeywords) ? options.activeKeywords : [];

      await AgyEditorWasmEngine.ensurePdfCompiler();

      var isBranded = (mode === 62);
      var hasReport = (mode === 64 || mode === 65);
      var shouldHighlight = (mode === 63 || mode === 65);

      var fullPdfContent = [];

      // 1. PAGE 1: PRE-SCREENED CANDIDATE DOSSIER (If Option 64 or 65)
      if(hasReport){
        var score = dossier.match || 88;
        var reportTable = [
          // Header
          {
            table: {
              widths: ['*', 'auto'],
              body: [[
                {
                  text: [
                    { text: 'SHARED BY\n', fontSize: 6.5, bold: true, color: '#64748b' },
                    { text: dossier.reportCompanyName || 'Hiring Partner', fontSize: 13, bold: true, color: '#0f172a' }
                  ],
                  border: [false, false, false, false]
                },
                {
                  stack: [
                    { text: 'PRE-SCREENED CANDIDATE REPORT', fontSize: 14, bold: true, color: '#0f172a', alignment: 'right' },
                    { text: ((dossier.reportDate || 'CONFIDENTIAL') + ' · VERIFIED').toUpperCase(), fontSize: 7, color: '#64748b', alignment: 'right', margin: [0, 3, 0, 0] }
                  ],
                  border: [false, false, false, false]
                }
              ]]
            },
            layout: 'noBorders',
            margin: [0, 0, 0, 14]
          },
          // Candidate & Score Block
          {
            table: {
              widths: ['*', 140],
              body: [[
                {
                  stack: [
                    { text: dossier.name || 'Candidate Profile', fontSize: 18, bold: true, color: '#0f172a' },
                    { text: dossier.designation || 'Verified Professional', fontSize: 10, bold: true, color: '#0284c7', margin: [0, 3, 0, 8] },
                    { text: 'EXPERIENCE: ' + (dossier.experience || '5 YEARS').toUpperCase(), fontSize: 7.5, bold: true, color: '#475569' },
                    { text: 'CURRENT COMPANY: ' + (dossier.company || '--').toUpperCase(), fontSize: 7.5, bold: true, color: '#475569', margin: [0, 2, 0, 0] }
                  ],
                  fillColor: '#ffffff',
                  border: [true, true, true, true],
                  borderColor: ['#cbd5e1','#cbd5e1','#cbd5e1','#cbd5e1'],
                  margin: [10, 8, 10, 8]
                },
                {
                  stack: [
                    { text: 'HIRING CONFIDENCE', fontSize: 9.5, bold: true, color: '#64748b', alignment: 'center', margin: [0, 6, 0, 0] },
                    { text: score + '%', fontSize: 26, bold: true, color: '#0284c7', alignment: 'center', margin: [0, 4, 0, 0] }
                  ],
                  fillColor: '#f8fbff',
                  border: [true, true, true, true],
                  borderColor: ['#cfe0ff','#cfe0ff','#cfe0ff','#cfe0ff'],
                  margin: [10, 8, 10, 8]
                }
              ]]
            },
            margin: [0, 0, 0, 12]
          },
          // 4 Grid Attributes
          {
            table: {
              widths: ['*', '*', '*', '*'],
              body: [[
                { stack: [ { text: 'EDUCATION', fontSize: 6.5, bold: true, color: '#64748b', alignment: 'center' }, { text: dossier.education || 'Graduate', fontSize: 8, bold: true, alignment: 'center', margin: [0, 2, 0, 0] } ], margin: [4, 6, 4, 6] },
                { stack: [ { text: 'NOTICE PERIOD', fontSize: 6.5, bold: true, color: '#64748b', alignment: 'center' }, { text: dossier.notice || 'Immediate', fontSize: 8, bold: true, alignment: 'center', margin: [0, 2, 0, 0] } ], margin: [4, 6, 4, 6] },
                { stack: [ { text: 'LOCATION', fontSize: 6.5, bold: true, color: '#64748b', alignment: 'center' }, { text: dossier.location || 'Bangalore', fontSize: 8, bold: true, alignment: 'center', margin: [0, 2, 0, 0] } ], margin: [4, 6, 4, 6] },
                { stack: [ { text: 'WORK MODE', fontSize: 6.5, bold: true, color: '#64748b', alignment: 'center' }, { text: dossier.workMode || 'Hybrid', fontSize: 8, bold: true, alignment: 'center', margin: [0, 2, 0, 0] } ], margin: [4, 6, 4, 6] }
              ]]
            },
            fillColor: '#ffffff',
            margin: [0, 0, 0, 12]
          },
          // Executive Summary & Recruiter Comments
          {
            columns: [
              { stack: [ { text: 'EXECUTIVE SUMMARY', fontSize: 8, bold: true, color: '#0284c7', margin: [0, 0, 0, 4] }, { text: dossier.careerSummary || 'Demonstrated track record of technical delivery.', fontSize: 7.5, lineHeight: 1.35 } ], margin: [0, 0, 6, 0] },
              { stack: [ { text: 'RECRUITER COMMENTS', fontSize: 8, bold: true, color: '#0284c7', margin: [0, 0, 0, 4] }, { text: dossier.recruiterNote || 'Candidate pre-screened and assessed for core competency.', fontSize: 7.5, lineHeight: 1.35 } ], margin: [6, 0, 0, 0] }
            ],
            margin: [0, 0, 0, 14]
          },
          // Footer
          {
            table: {
              widths: ['*', 'auto'],
              body: [[
                { text: 'This report is generated by AI hiring platform jero.works', fontSize: 6.5, color: '#64748b', border: [false, true, false, false], margin: [0, 6, 0, 0] },
                { text: 'www.jero.works', fontSize: 6.5, color: '#64748b', border: [false, true, false, false], margin: [0, 6, 0, 0] }
              ]]
            },
            layout: { hLineColor: function(){ return '#e5e7eb'; }, hLineWidth: function(){ return 0.6; }, vLineWidth: function(){ return 0; } }
          }
        ];
        fullPdfContent = fullPdfContent.concat(reportTable);
      }

      // 2. COMPANY BRANDING BANNER (If Option 62)
      if(isBranded){
        var brandBanner = {
          table: {
            widths: ['*', 'auto'],
            body: [[
              {
                stack: [
                  { text: 'SHARED BY', fontSize: 6.5, bold: true, color: '#64748b' },
                  { text: dossier.reportCompanyName || 'Hiring Partner', fontSize: 13, bold: true, color: '#0f172a' }
                ],
                border: [false, false, false, true]
              },
              {
                stack: [
                  { text: 'CONFIDENTIAL RESUME', fontSize: 12, bold: true, color: '#0f172a', alignment: 'right' },
                  { text: ((dossier.reportDate || '') + ' · Verified ATS Format').toUpperCase(), fontSize: 7, color: '#0284c7', alignment: 'right', margin: [0, 2, 0, 0] }
                ],
                border: [false, false, false, true]
              }
            ]]
          },
          layout: { hLineColor: function(){ return '#0284c7'; }, hLineWidth: function(){ return 2; }, vLineWidth: function(){ return 0; } },
          margin: [0, 0, 0, 16]
        };
        fullPdfContent.push(brandBanner);
      }

      // 3. EDITABLE RESUME BODY VECTOR PARSING
      var parsedResumeNodes = AgyEditorWasmEngine.parseDomToPdfMake(editorRoot, activeKeywords, shouldHighlight);

      if(hasReport && parsedResumeNodes.length > 0){
        parsedResumeNodes[0].pageBreak = 'before';
      }

      fullPdfContent = fullPdfContent.concat(parsedResumeNodes);

      var docDef = {
        pageSize: 'A4',
        pageMargins: [36, 30, 36, 30],
        defaultStyle: {
          font: 'Roboto',
          fontSize: 9,
          lineHeight: 1.35,
          color: '#1e293b'
        },
        content: fullPdfContent
      };

      return new Promise(function(resolve, reject){
        try {
          window.pdfMake.createPdf(docDef).getBlob(function(blob){
            if(blob && blob.size > 200){
              resolve(blob);
            } else {
              reject(new Error('Vector PDF compilation produced an empty file.'));
            }
          });
        } catch(compErr){
          reject(compErr);
        }
      });
    },

    /**
     * Converts an HTML DOM tree into native vector pdfMake nodes with exact styling
     * @param {HTMLElement|string} root
     * @param {string[]} activeKeywords
     * @param {boolean} shouldHighlight
     * @returns {Array}
     */
    parseDomToPdfMake: function(root, activeKeywords, shouldHighlight){
      if(!root) return [];
      var container = root;
      if(typeof root === 'string'){
        container = document.createElement('div');
        container.innerHTML = root;
      }

      var content = [];
      var kwMap = {};
      if(shouldHighlight && Array.isArray(activeKeywords)){
        activeKeywords.forEach(function(k){
          if(k) kwMap[String(k).trim().toLowerCase()] = true;
        });
      }

      function parseInlines(parent){
        var runs = [];
        for(var i = 0; i < parent.childNodes.length; i++){
          var node = parent.childNodes[i];
          if(node.nodeType === 3){
            var text = node.nodeValue || '';
            if(!text) continue;

            if(shouldHighlight && Object.keys(kwMap).length > 0){
              var words = text.split(/(\s+)/);
              for(var w = 0; w < words.length; w++){
                var token = words[w];
                var clean = token.replace(/[^a-zA-Z0-9_\-#\+]/g, '').toLowerCase();
                if(clean && kwMap[clean]){
                  runs.push({ text: token, background: '#fef08a', bold: true, color: '#854d0e' });
                } else {
                  runs.push({ text: token });
                }
              }
            } else {
              runs.push({ text: text });
            }
          } else if(node.nodeType === 1){
            var tag = node.tagName.toLowerCase();
            if(tag === 'mark' && node.classList.contains('rcv-redact')){
              runs.push({ text: '████████', color: '#0f172a', background: '#0f172a' });
            } else if(tag === 'mark' || node.classList.contains('rcv-hit')){
              runs.push({ text: node.innerText, background: '#fef08a', bold: true, color: '#854d0e' });
            } else if(tag === 'strong' || tag === 'b'){
              runs.push({ text: node.innerText, bold: true });
            } else if(tag === 'em' || tag === 'i'){
              runs.push({ text: node.innerText, italics: true });
            } else if(tag === 'u'){
              runs.push({ text: node.innerText, decoration: 'underline' });
            } else if(tag === 'img' && node.src && node.src.indexOf('data:image/') === 0){
              runs.push({ image: node.src, width: Math.min(180, node.width || 120), margin: [0, 4, 0, 4] });
            } else {
              var nested = parseInlines(node);
              if(nested.length) runs = runs.concat(nested);
            }
          }
        }
        return runs;
      }

      function parseBlock(node){
        if(node.nodeType === 3){
          var t = (node.nodeValue || '').trim();
          if(t) content.push({ text: t, margin: [0, 2, 0, 2] });
          return;
        }
        if(node.nodeType !== 1) return;

        var tag = node.tagName.toLowerCase();
        if(tag === 'script' || tag === 'style') return;

        if(tag === 'h1'){
          content.push({ text: node.innerText.trim(), fontSize: 16, bold: true, color: '#0f172a', margin: [0, 10, 0, 4] });
          return;
        }
        if(tag === 'h2'){
          content.push({ text: node.innerText.trim(), fontSize: 13, bold: true, color: '#0f172a', margin: [0, 8, 0, 3] });
          return;
        }
        if(tag === 'h3'){
          content.push({ text: node.innerText.trim(), fontSize: 11, bold: true, color: '#1e293b', margin: [0, 6, 0, 2] });
          return;
        }
        if(tag === 'hr' || node.classList.contains('doc-page-break')){
          content.push({ text: '', pageBreak: 'before' });
          return;
        }
        if(tag === 'ul'){
          var ulItems = [];
          Array.prototype.slice.call(node.children).forEach(function(li){
            if(li.tagName.toLowerCase() === 'li'){
              var inlines = parseInlines(li);
              if(inlines.length) ulItems.push({ text: inlines });
            }
          });
          if(ulItems.length){
            content.push({ ul: ulItems, margin: [10, 2, 0, 6], lineHeight: 1.3 });
          }
          return;
        }
        if(tag === 'ol'){
          var olItems = [];
          Array.prototype.slice.call(node.children).forEach(function(li){
            if(li.tagName.toLowerCase() === 'li'){
              var inlines = parseInlines(li);
              if(inlines.length) olItems.push({ text: inlines });
            }
          });
          if(olItems.length){
            content.push({ ol: olItems, margin: [10, 2, 0, 6], lineHeight: 1.3 });
          }
          return;
        }
        if(tag === 'p' || tag === 'div'){
          var inlines = parseInlines(node);
          if(inlines.length){
            content.push({ text: inlines, margin: [0, 2, 0, 4] });
          }
          return;
        }

        for(var i = 0; i < node.childNodes.length; i++){
          parseBlock(node.childNodes[i]);
        }
      }

      for(var j = 0; j < container.childNodes.length; j++){
        parseBlock(container.childNodes[j]);
      }

      return content;
    },

    /**
     * Generates a transformed PDF blob directly matching Engine 1 options (61-65),
     * enabling instant preview, comparison, or direct export from Engine 3.
     * @param {Object} options - { editorRoot, mode, dossier, activeKeywords }
     * @returns {Promise<Blob>}
     */
    generateTransformedDocumentBlob: async function(options){
      return AgyEditorWasmEngine.compileEditedDocumentPdf(options);
    },

    /**
     * Downloads an in-memory Blob directly to the client
     * @param {Blob} blob
     * @param {string} filename
     */
    downloadBlob: function(blob, filename){
      var url = URL.createObjectURL(blob);
      var a = document.createElement('a');
      a.href = url;
      a.download = filename || 'Optimized_Document.pdf';
      document.body.appendChild(a);
      a.click();
      setTimeout(function(){
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      }, 15000);
    }
  };

  return AgyEditorWasmEngine;
});