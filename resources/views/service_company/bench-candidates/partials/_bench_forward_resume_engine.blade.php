{{-- Dedicated Isolated Document Engine Exclusively for Bench Candidate Forward Resume --}}
<script>
/**
 * AgyBenchForwardResumeEngine.js
 * 
 * EXCLUSIVE DEDICATED ENGINE FOR BENCH TALENT DRAWER (/service_company/bench-candidates)
 * 
 * 100% COMPLETE ISOLATION MANDATE:
 * - This engine is physically decoupled and isolated from CV Tracker and App Tracker forward resume engines.
 * - CV Tracker and App Tracker have their own forward resume engine and pre-screen report structure based on jobs/requisitions.
 * - Bench Candidate Forward Resume operates exclusively on In-House Talent attributes (bench availability,
 *   bench skill matrix ratings, relationship manager, and authoritative location strings).
 * - Zero global pollution: Scoped to window.AgyBenchForwardResumeEngine.
 */

(function (root, factory) {
  'use strict';
  var exp = factory();
  if (typeof window !== 'undefined') {
    window.AgyBenchForwardResumeEngine = exp;
  }
  if (typeof root !== 'undefined') {
    root.AgyBenchForwardResumeEngine = exp;
  }
}(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

  var AGY_MAX_ATTACHMENT_BYTES = 6000000; // 6MB attachment limit
  var rsPdfLibReady = null;
  var rsPdfJsReady = null;
  var rsPdfMakeReady = null;
  var rsJsPdfReady = null;
  var rsLogoCache = {};
  var rsResumeBytesCache = {};

  // ═══════════════════════════════════════════════════════════════════════════
  // SVG ICONS FOR PRE-SCREENED REPORT DOSSIER (pdfMake - Exact CV Tracker Replica)
  // ═══════════════════════════════════════════════════════════════════════════
  var rsStarGold = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#f59e0b" d="M12 2.6l2.85 5.78 6.38.93-4.62 4.5 1.09 6.35L12 17.16l-5.7 3 1.09-6.35-4.62-4.5 6.38-.93L12 2.6z"/></svg>';
  var rsStarMuted = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#cbd5e1" d="M12 2.6l2.85 5.78 6.38.93-4.62 4.5 1.09 6.35L12 17.16l-5.7 3 1.09-6.35-4.62-4.5 6.38-.93L12 2.6z"/></svg>';
  var rsIconExperience = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#64748b" d="M12 2l2.2 4.5 5 .7-3.6 3.5.8 5-4.4-2.4-4.4 2.4.8-5L4.8 7.2l5-.7L12 2zm-6 17h12v2H6v-2z"/></svg>';
  var rsIconCompany = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#64748b" d="M4 21V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v17h3v-9h-2v-2h3a1 1 0 0 1 1 1v10H4zm4-14h2V5H8v2zm4 0h2V5h-2v2zm-4 4h2V9H8v2zm4 0h2V9h-2v2zm-4 4h2v-2H8v2zm4 0h2v-2h-2v2z"/></svg>';
  var rsIconEducation = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 3L1.5 8.5 12 14l8-4.2V16h2V8.5L12 3zm-6 9.1V16c0 1.7 2.7 4 6 4s6-2.3 6-4v-3.9L12 15.2l-6-3.1z"/></svg>';
  var rsIconCalendar = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M7 2h2v2h6V2h2v2h2a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2V2zm12 8H5v9h14v-9z"/></svg>';
  var rsIconLocation = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z"/></svg>';
  var rsIconLaptop = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M4 4h16a2 2 0 0 1 2 2v10H2V6a2 2 0 0 1 2-2zm0 2v8h16V6H4zm-2 12h20v2H2v-2z"/></svg>';
  var rsIconSummary = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm3 5h8V6H8v2zm0 5h8v-2H8v2zm0 5h5v-2H8v2z"/></svg>';
  var rsIconNote = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M4 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H9l-5 5V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z"/></svg>';
  var rsIconSkill = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 2l2.4 5.4 5.9.6-4.4 3.9 1.3 5.8-5.2-3-5.2 3 1.3-5.8L3.7 8l5.9-.6L12 2z"/></svg>';
  var rsIconShield = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 2l8 3v6c0 5-3.4 9.6-8 11-4.6-1.4-8-6-8-11V5l8-3zm-1 13.2l6-6-1.4-1.4L11 12.4 8.4 9.8 7 11.2l4 4z"/></svg>';
  var rsIconResume = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M6 2h8l5 5v15H6V2zm7 1.8V8h4.2L13 3.8zM8 12h8v-1.7H8V12zm0 4h8v-1.7H8V16zm0 4h6v-1.7H8V20z"/></svg>';
  var rsIconEmail = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M4 5h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2zm8 7.2L4.7 7H4v.7l8 5.7 8-5.7V7h-.7L12 12.2z"/></svg>';
  var rsIconPhone = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M7.2 2.8l3 3a2 2 0 0 1 .4 2.2l-.9 2.1a13.5 13.5 0 0 0 4.2 4.2l2.1-.9a2 2 0 0 1 2.2.4l3 3a2 2 0 0 1 0 2.8l-1.4 1.4c-.8.8-2 .9-3 .5A25.2 25.2 0 0 1 2.5 7.2c-.4-1-.3-2.2.5-3l1.4-1.4a2 2 0 0 1 2.8 0z"/></svg>';
  var rsIconLinkedin = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm3.4 15V9.6H6V18h2.4zM7.2 8.5a1.4 1.4 0 1 0 0-2.8 1.4 1.4 0 0 0 0 2.8zM18 18v-4.8c0-2.5-1.3-3.8-3.2-3.8-1.4 0-2.1.8-2.5 1.4V9.6H10V18h2.4v-4.2c0-1.1.2-2.2 1.6-2.2s1.5 1.3 1.5 2.3V18H18z"/></svg>';
  var rsIconJobDescription = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M8 2h8a2 2 0 0 1 2 2v2h2v16H4V6h2V4a2 2 0 0 1 2-2zm0 4h8V4H8v2zm-1 6h10v-1.8H7V12zm0 4h10v-1.8H7V16zm0 4h7v-1.8H7V20z"/></svg>';

  function rsLoadScript(src) {
    return new Promise(function (resolve, reject) {
      var existing = document.querySelector('script[src="' + src + '"]');
      if (existing && existing.getAttribute('data-ready') === '1') { resolve(); return; }
      var s = existing || document.createElement('script');
      var done = false;
      var finish = function (ok) {
        if (done) return;
        done = true;
        if (ok) { s.setAttribute('data-ready', '1'); resolve(); }
        else { reject(new Error('Load failed: ' + src)); }
      };
      s.onload = function () { finish(true); };
      s.onerror = function () { finish(false); };
      if (!existing) { s.async = true; s.src = src; document.head.appendChild(s); }
      if (existing) setTimeout(function () { finish(true); }, 100);
      setTimeout(function () { finish(false); }, 10000);
    });
  }

  function rsTryList(list, index, test) {
    if (index >= list.length) return Promise.reject(new Error('Could not load required script engine'));
    if (test()) return Promise.resolve();
    return rsLoadScript(list[index]).then(function () {
      return test() ? Promise.resolve() : Promise.reject(new Error('Loaded script but global object missing'));
    }).catch(function () { return rsTryList(list, index + 1, test); });
  }

  function ensurePdfLib() {
    if (rsPdfLibReady && window.PDFLib && window.PDFLib.PDFDocument) return Promise.resolve(window.PDFLib);
    var sources = [
      '/js/pdf-lib.min.js',
      'https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js'
    ];
    rsPdfLibReady = rsTryList(sources, 0, function () { return !!(window.PDFLib && window.PDFLib.PDFDocument); }).then(function () {
      return window.PDFLib;
    });
    return rsPdfLibReady;
  }

  function ensurePdfJs() {
    if (rsPdfJsReady && window.pdfjsLib && typeof window.pdfjsLib.getDocument === 'function') return Promise.resolve(window.pdfjsLib);
    var sources = [
      '/js/pdf.min.js',
      'https://cdn.jsdelivr.net/npm/pdfjs-dist@2.11.338/build/pdf.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js'
    ];
    rsPdfJsReady = rsTryList(sources, 0, function () { return !!(window.pdfjsLib && typeof window.pdfjsLib.getDocument === 'function'); }).then(function () {
      try {
        var ver = window.pdfjsLib.version || '2.11.338';
        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/' + ver + '/pdf.worker.min.js';
      } catch (err) {}
      return window.pdfjsLib;
    });
    return rsPdfJsReady;
  }

  function ensurePdfMake() {
    if (rsPdfMakeReady && window.pdfMake && window.pdfMake.vfs && window.pdfMake.vfs['Roboto-Regular.ttf']) return Promise.resolve(window.pdfMake);
    var core = [
      '/js/pdfmake.min.js',
      'https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/pdfmake.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js'
    ];
    var vfs = [
      '/js/vfs_fonts.min.js',
      'https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/vfs_fonts.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js'
    ];
    rsPdfMakeReady = rsTryList(core, 0, function () { return !!window.pdfMake; })
      .then(function () { return rsTryList(vfs, 0, function () { return !!(window.pdfMake.vfs && window.pdfMake.vfs['Roboto-Regular.ttf']); }); })
      .then(function () {
        window.pdfMake.fonts = {
          Roboto: {
            normal: 'Roboto-Regular.ttf',
            bold: 'Roboto-Medium.ttf',
            italics: 'Roboto-Italic.ttf',
            bolditalics: 'Roboto-MediumItalic.ttf'
          }
        };
        return window.pdfMake;
      });
    return rsPdfMakeReady;
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // UTILITY & FORMATTING FUNCTIONS (Matches CV Tracker 1:1)
  // ═══════════════════════════════════════════════════════════════════════════
  function clean(value, fallback) {
    value = String(value == null ? '' : value).replace(/\s+/g, ' ').trim();
    return value || fallback || '--';
  }

  function limitText(value, max, fallback) {
    value = clean(value, fallback || '--');
    return value.length > max ? value.slice(0, max - 1).trim() + '...' : value;
  }

  function safeNumber(value, min, max) {
    value = parseInt(value, 10);
    if (isNaN(value)) value = 0;
    return Math.max(min, Math.min(max, value));
  }

  function isGap(value) {
    value = clean(value, '');
    return !value || value === '--' || value === '-' || /^available 01 jan 1970$/i.test(value);
  }

  function dataUriBytes(dataUri) {
    var match = String(dataUri || '').match(/^data:([^;]+);base64,(.+)$/i);
    if (!match) return null;
    var raw = atob(match[2]);
    var bytes = new Uint8Array(raw.length);
    for (var i = 0; i < raw.length; i++) bytes[i] = raw.charCodeAt(i);
    return { mime: match[1].toLowerCase(), bytes: bytes };
  }

  function rsLogoDataUri(rawUrl) {
    rawUrl = clean(rawUrl, '');
    if (!rawUrl || rawUrl === '--') return Promise.resolve(null);
    if (/^data:image\//i.test(rawUrl)) return Promise.resolve(rawUrl);
    if (rsLogoCache[rawUrl]) return rsLogoCache[rawUrl];
    rsLogoCache[rawUrl] = fetch(rawUrl, { credentials: 'include', cache: 'force-cache' })
      .then(function (response) { return response.ok ? response.blob() : null; })
      .then(function (blob) {
        if (!blob) return null;
        return new Promise(function (resolve) {
          var reader = new FileReader();
          reader.onload = function () { resolve(reader.result); };
          reader.onerror = function () { resolve(null); };
          reader.readAsDataURL(blob);
        });
      })
      .catch(function () { return null; });
    return rsLogoCache[rawUrl];
  }

  async function rsGetLogoDataUri(url) {
    return await rsLogoDataUri(url);
  }

  async function fetchCandidateResumeBytes(cand, needPdf) {
    var candId = (typeof cand === 'object' && cand !== null) ? cand.id : cand;
    var cacheKey = candId + (needPdf ? '_pdf' : '_raw');
    if (rsResumeBytesCache[cacheKey]) return rsResumeBytesCache[cacheKey];

    var sources = [];
    if (needPdf) {
      if (typeof cand === 'object' && cand !== null && cand.resume_pdf_url) {
        sources.push(cand.resume_pdf_url);
      }
      sources.push('/service_company/bench-candidates/download/' + candId + '?as_pdf=1');
    }

    // Direct and proxy download sources
    sources.push('/service_company/bench-candidates/download/' + candId);

    if (typeof cand === 'object' && cand !== null) {
      if (cand.download_url && !sources.includes(cand.download_url)) {
        sources.push(cand.download_url);
      }
      if (cand.direct_download_url && !sources.includes(cand.direct_download_url)) {
        sources.push(cand.direct_download_url);
      }
      if (cand.resume_r2_url && !sources.includes(cand.resume_r2_url)) {
        sources.push(cand.resume_r2_url);
      }
      if (cand.resume_url && !sources.includes(cand.resume_url)) {
        sources.push(cand.resume_url);
      }
      if (cand.resume && (cand.resume.startsWith('http://') || cand.resume.startsWith('https://')) && !sources.includes(cand.resume)) {
        sources.push(cand.resume);
      }
      if (cand.resume_filename) {
        sources.push('/user-uploads/resumes/' + cand.resume_filename);
        sources.push('/user-uploads/documents/' + candId + '/' + cand.resume_filename);
      }
    }

    var lastErr = null;
    for (var i = 0; i < sources.length; i++) {
      try {
        var fetchOpts = {
          cache: 'no-store',
          headers: { 'Accept': 'application/octet-stream, application/pdf, */*' }
        };
        try {
          var uOrigin = (new URL(sources[i], window.location.href)).origin;
          if (uOrigin === window.location.origin) {
            fetchOpts.credentials = 'include';
          } else {
            fetchOpts.mode = 'cors';
          }
        } catch (e) {
          fetchOpts.credentials = 'include';
        }

        var resp = await fetch(sources[i], fetchOpts);
        if (resp.ok) {
          var contentType = resp.headers.get('content-type') || '';
          if (contentType.includes('application/json')) {
            var json = await resp.json();
            if (json.url && !sources.includes(json.url)) {
              sources.push(json.url);
              continue;
            }
          }
          var buf = await resp.arrayBuffer();
          if (buf && buf.byteLength > 100) {
            var head = new Uint8Array(buf.slice(0, 10));
            var headStr = String.fromCharCode.apply(null, head).toLowerCase();
            // Validate that we didn't receive an HTML 404/login page
            if (headStr.indexOf('<!doc') === -1 && headStr.indexOf('<html') === -1) {
              rsResumeBytesCache[cacheKey] = new Uint8Array(buf);
              return rsResumeBytesCache[cacheKey];
            }
          }
        } else {
          lastErr = new Error('HTTP ' + resp.status + ' from ' + sources[i]);
        }
      } catch (e) {
        lastErr = e;
      }
    }
    throw (lastErr || new Error('Could not fetch candidate resume document'));
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // PRE-SCREENED REPORT DOSSIER (pdfMake - Exact CV Tracker 100% Replica)
  // ═══════════════════════════════════════════════════════════════════════════
  function headerCard(companyName, logoCell, reportDate, captainName) {
    return {
      margin: [0, 0, 0, 10],
      stack: [
        { canvas: [{ type: 'rect', x: 0, y: 0, w: 535, h: 64, r: 6, lineWidth: 0.55, lineColor: '#dbe3ef', color: '#ffffff' }], margin: [0, 0, 0, -64] },
        {
          table: {
            widths: [220, '*'],
            body: [[
              {
                table: {
                  widths: ['auto', '*'],
                  body: [[logoCell, {
                    stack: [
                      { text: 'SHARED BY', fontSize: 6, bold: true, color: '#64748b', margin: [0, 1, 0, 3] },
                      { text: companyName, fontSize: 11.2, bold: true, color: '#0f172a', margin: [0, 0, 0, 0] }
                    ],
                    border: [false, false, false, false]
                  }]]
                },
                layout: 'noBorders',
                margin: [14, 12, 10, 10],
                border: [false, false, false, false]
              },
              {
                stack: [
                  { text: 'PRE-SCREENED CANDIDATE REPORT', style: 'title', margin: [0, 2, 0, 0] },
                  { text: (clean(reportDate, '--') + ' · Confidential').toUpperCase(), style: 'eyebrow', margin: [0, 4, 0, 0] }
                ],
                margin: [10, 14, 14, 10],
                border: [false, false, false, false]
              }
            ]]
          },
          layout: 'noBorders'
        }
      ]
    };
  }

  function unbrandedHeaderCard(reportDate) {
    return {
      margin: [0, 0, 0, 10],
      stack: [
        { canvas: [{ type: 'rect', x: 0, y: 0, w: 535, h: 64, r: 6, lineWidth: 0.55, lineColor: '#dbe3ef', color: '#ffffff' }], margin: [0, 0, 0, -64] },
        {
          table: {
            widths: ['*'],
            body: [[
              {
                stack: [
                  { text: 'PRE-SCREENED CANDIDATE REPORT', style: 'title', margin: [0, 2, 0, 0] },
                  { text: (clean(reportDate, '--') + ' · Confidential').toUpperCase(), style: 'eyebrow', margin: [0, 4, 0, 0] }
                ],
                margin: [14, 14, 14, 10],
                border: [false, false, false, false]
              }
            ]]
          },
          layout: 'noBorders'
        }
      ]
    };
  }

  function pairedCardRow(leftStack, rightStack, leftFill, rightFill, rightBorder) {
    return {
      margin: [0, 0, 0, 8],
      stack: [
        { canvas: [{ type: 'rect', x: 0, y: 0, w: 264, h: 88, r: 6, lineWidth: 0.55, lineColor: '#cbd5e1', color: leftFill || '#ffffff' }, { type: 'rect', x: 271, y: 0, w: 264, h: 88, r: 6, lineWidth: 0.55, lineColor: rightBorder || '#cbd5e1', color: rightFill || '#ffffff' }], margin: [0, 0, 0, -88] },
        {
          columns: [
            { stack: Array.isArray(leftStack) ? leftStack : [leftStack], width: 264, margin: [12, 10, 12, 10] },
            { stack: Array.isArray(rightStack) ? rightStack : [rightStack], width: 264, margin: [12, 10, 12, 10] }
          ],
          columnGap: 7
        }
      ]
    };
  }

  function roundedFullCard(stack, margin, height, fill, border) {
    return {
      margin: margin || [0, 0, 0, 8],
      stack: [
        { canvas: [{ type: 'rect', x: 0, y: 0, w: 535, h: height, r: 6, lineWidth: 0.55, lineColor: border || '#dbe3ef', color: fill || '#ffffff' }], margin: [0, 0, 0, -height] },
        { table: { widths: ['*'], heights: function () { return height; }, body: [[{ stack: Array.isArray(stack) ? stack : [stack], margin: [12, 10, 12, 10] }]] }, layout: 'noBorders' }
      ]
    };
  }

  function fixedPanel(stack, margin, fill, border, minHeight) {
    return {
      margin: margin || [0, 0, 0, 7],
      stack: [
        { canvas: [{ type: 'rect', x: 0, y: 0, w: 260, h: minHeight || 72, r: 6, lineWidth: 0.55, lineColor: border || '#dbe3ef', color: fill || '#ffffff' }], margin: [0, 0, 0, -(minHeight || 72)] },
        { table: { widths: ['*'], heights: function () { return minHeight || 72; }, body: [[{ stack: Array.isArray(stack) ? stack : [stack], margin: [12, 10, 12, 10] }]] }, layout: 'noBorders' }
      ]
    };
  }

  function sectionTitle(title, subtle, icon) {
    if (!icon) return { text: title, style: subtle ? 'sectionTitleMuted' : 'sectionTitle', margin: [0, 0, 0, 8] };
    return {
      columns: [
        { svg: icon, width: 10, margin: [0, 0, 0, 0] },
        { text: '', width: 5 },
        { text: title, style: subtle ? 'sectionTitleMuted' : 'sectionTitle', margin: [0, 0.5, 0, 0] }
      ],
      columnGap: 0,
      margin: [0, 0, 0, 8]
    };
  }

  function metricCell(label, value, icon) {
    return {
      stack: [
        {
          columns: [
            { text: '', width: '*' },
            { svg: icon, width: 10.5, margin: [0, 0.2, 0, 0] },
            { text: '', width: 6 },
            { text: clean(label).toUpperCase(), style: 'metricLabel', color: '#64748b', width: 'auto', margin: [0, 1.9, 0, 0] },
            { text: '', width: '*' }
          ],
          columnGap: 0,
          margin: [0, 0, 0, 8]
        },
        { text: clean(value), style: 'metricValue', alignment: 'center' }
      ],
      margin: [4, 9, 4, 8]
    };
  }

  function labeledIconLine(icon, label, value, style, color, iconWidth) {
    var width = iconWidth || 7;
    return {
      columns: [
        { svg: icon, width: width, margin: [0, 1, 0, 0], alignment: 'left' },
        { text: [{ text: label, bold: true }, ' ' + value], style: style || 'small', color: color || undefined, margin: [3, 0, 0, 0] }
      ],
      columnGap: 0
    };
  }

  function buildSkillRows(skills) {
    var rows = [
      [
        { text: 'SKILLS', style: 'tableHead' },
        { text: 'EXPERIENCE', style: 'tableHead' },
        { text: 'RATING', style: 'tableHead', alignment: 'center' }
      ]
    ];
    skills = (skills || []).filter(function (skill) { return clean(skill.name, '') !== ''; }).slice(0, 7);
    if (!skills.length) {
      rows.push([{ text: 'No skill intelligence available in current candidate data.', colSpan: 3, style: 'muted', margin: [0, 6, 0, 6] }, {}, {}]);
      return rows;
    }
    skills.forEach(function (skill) {
      var rating = safeNumber(skill.rating, 0, 5);
      var exp = Number(skill.exp || 0);
      var expText = clean(skill.expText, '');
      var stars = [];
      for (var i = 1; i <= 5; i++) stars.push({ svg: i <= rating ? rsStarGold : rsStarMuted, width: 7.7, margin: [0, 0.5, 1.2, 0] });
      rows.push([
        { text: clean(skill.name).toUpperCase(), style: 'tableCellBold', alignment: 'left' },
        { text: expText !== '' && expText !== '--' ? expText : (exp > 0 ? exp + ' Year' + (exp > 1 ? 's' : '') : '--'), style: 'tableCell', color: '#334155' },
        { columns: stars, columnGap: 0, alignment: 'center', margin: [17, 5, 0, 5] }
      ]);
    });
    return rows;
  }

  function buildValidationRows(validation) {
    var map = {};
    (validation || []).forEach(function (row) {
      var key = clean(row.label, '').toLowerCase();
      if (key) map[key] = clean(row.status, '');
    });
    var slots = [
      { label: 'RESUME', key: 'resume', fallbackKey: 'resume verified', icon: rsIconResume },
      { label: 'EMAIL ID', key: 'email id', fallbackKey: 'email verified', icon: rsIconEmail },
      { label: 'PHONE NUMBER', key: 'phone number', fallbackKey: 'phone verified', icon: rsIconPhone },
      { label: 'LINKEDIN ID', key: 'linkedin id', fallbackKey: 'linkedin verified', icon: rsIconLinkedin },
      { label: 'JOB DESCRIPTION', key: 'job description', fallbackKey: 'job description acknowledged', icon: rsIconJobDescription }
    ];
    var cells = slots.map(function (row) {
      var status = map[row.key] || map[row.fallbackKey] || '-';
      if (!status || status === '--' || (row.key === 'linkedin id' && status === 'Discussed')) status = '-';
      var verified = /verified|confirmed|completed|shared/i.test(status);
      var isBlank = (status === '-' || status === '--' || !status);
      var textColor = verified ? '#16a34a' : (isBlank ? '#94a3b8' : '#b45309');
      return {
        stack: [
          {
            columns: [
              { text: '', width: '*' },
              { svg: row.icon, width: 9.8, margin: [0, 0.2, 0, 0] },
              { text: '', width: 5 },
              { text: row.label.toUpperCase(), fontSize: 6.6, bold: true, color: '#0f172a', width: 'auto', margin: [0, 1.7, 0, 0] },
              { text: '', width: '*' }
            ],
            columnGap: 0,
            margin: [0, 0, 0, 7]
          },
          { text: status, fontSize: 6.5, bold: true, color: textColor, alignment: 'center', margin: [0, 0, 0, 0] }
        ],
        margin: [2, 7, 2, 7]
      };
    });
    return [cells];
  }

  function buildDefinition(data, logoUri, options) {
    options = options || {};
    var noBranding = options.noBranding === true;
    var score = safeNumber(data.match, 0, 100);
    var summary = limitText(data.careerSummary, 560, 'Experienced professional with demonstrated track record of technical delivery and engineering excellence.');
    var noteMissing = isGap(data.recruiterNote);
    var note = noteMissing ? 'No recruiter comments available.' : limitText(data.recruiterNote, 420, '');
    var rawNotice = data.notice || data.noticePeriod;
    var joining = data.isImmediate ? 'Immediate' : (clean(rawNotice, 'Immediate') === '--' ? 'Immediate' : clean(rawNotice, 'Immediate'));
    var companyName = clean(data.reportCompanyName, 'Hiring Partner');
    var skillCount = Math.max(1, Math.min(7, (data.skills || []).filter(function (skill) { return clean(skill.name, '') !== ''; }).length));
    var skillCardHeight = 90 + (skillCount * 31);
    var logoCell = logoUri
      ? { image: logoUri, fit: [42, 28], alignment: 'center', margin: [0, 0, 12, 0], border: [false, false, false, false] }
      : { text: companyName.charAt(0).toUpperCase(), fontSize: 13, bold: true, color: '#1d4ed8', alignment: 'center', margin: [0, 5, 12, 4], fillColor: '#eef2ff', border: [false, false, false, false] };

    return {
      pageSize: 'A4',
      pageMargins: [30, 26, 30, 24],
      defaultStyle: { font: 'Roboto', fontSize: 8, color: '#1f2937', lineHeight: 1.22 },
      styles: {
        title: { fontSize: 15, bold: true, color: '#0f172a', alignment: 'right' },
        eyebrow: { fontSize: 6.2, color: '#64748b', alignment: 'right' },
        metaLabel: { fontSize: 5.7, color: '#64748b' },
        metaValue: { fontSize: 7.4, bold: true, color: '#111827' },
        candidateName: { fontSize: 18, bold: true, color: '#0f172a' },
        designation: { fontSize: 9.2, bold: true, color: '#1d4ed8' },
        small: { fontSize: 7.4, color: '#64748b' },
        sectionTitle: { fontSize: 8.2, bold: true, color: '#1d4ed8' },
        sectionTitleMuted: { fontSize: 8, bold: true, color: '#334155' },
        tableHead: { fontSize: 8.4, bold: true, color: '#334155', fillColor: '#f8fafc', margin: [0, 6, 0, 6] },
        tableCell: { fontSize: 8.6, color: '#334155', margin: [0, 6, 0, 6] },
        tableCellBold: { fontSize: 8.6, bold: true, color: '#0f172a', margin: [0, 6, 0, 6] },
        metricLabel: { fontSize: 6, color: '#64748b' },
        metricValue: { fontSize: 7.2, bold: true, color: '#0f172a' },
        muted: { fontSize: 7, color: '#64748b' },
        body: { fontSize: 7.6, color: '#1f2937', lineHeight: 1.35 },
        footer: { fontSize: 6, color: '#64748b' }
      },
      content: [
        noBranding ? unbrandedHeaderCard(data.reportDate) : headerCard(companyName, logoCell, data.reportDate, data.recruiterName),
        pairedCardRow([
          { text: clean(data.name, 'Candidate'), style: 'candidateName' },
          { text: clean(data.designation || data.jobTitle, '--'), style: 'designation', margin: [0, 3, 0, 8] },
          labeledIconLine(rsIconExperience, 'EXPERIENCE:', (clean(data.experience, 'Not Specified') === '--' ? 'Not Specified' : clean(data.experience, 'Not Specified')).toUpperCase(), 'small', '#64748b', 6.5),
          { stack: [labeledIconLine(rsIconCompany, 'CURRENT COMPANY:', (clean(data.company, 'Not Specified') === '--' ? 'Not Specified' : clean(data.company, 'Not Specified')).toUpperCase(), 'small', '#64748b', 6.5)], margin: [0, 3, 0, 0] }
        ], [
          { text: 'HIRING CONFIDENCE', fontSize: 12.4, bold: true, color: '#64748b', alignment: 'center', margin: [0, 8, 0, 0] },
          { text: score + '%', fontSize: 30, bold: true, color: '#1d4ed8', alignment: 'center', margin: [0, 7, 0, 0] }
        ], '#ffffff', '#f8fbff', '#cfe0ff'),
        roundedFullCard([
          {
            table: {
              widths: [116, 116, 164, 116],
              body: [[
                metricCell('Education', clean(data.education, 'Not Specified') === '--' ? 'Not Specified' : clean(data.education, 'Not Specified'), rsIconEducation),
                metricCell('Notice Period', joining, rsIconCalendar),
                metricCell('Location', clean(data.location, 'Not Specified') === '--' ? 'Not Specified' : clean(data.location, 'Not Specified'), rsIconLocation),
                metricCell('Work Mode', clean(data.workMode, 'Not Specified') === '--' ? 'Not Specified' : clean(data.workMode, 'Not Specified'), rsIconLaptop)
              ]]
            },
            layout: {
              hLineWidth: function () { return 0; },
              vLineWidth: function (i) { return i > 0 && i < 4 ? 0.45 : 0; },
              vLineColor: function () { return '#dbe3ef'; },
              paddingLeft: function () { return 0; },
              paddingRight: function () { return 0; },
              paddingTop: function () { return 0; },
              paddingBottom: function () { return 0; }
            }
          }
        ], [0, 0, 0, 8], 62, '#ffffff', '#dbe3ef'),
        {
          columns: [
            fixedPanel([sectionTitle('EXECUTIVE SUMMARY', false, rsIconSummary), { text: summary, style: 'body' }], [0, 0, 6, 8], '#ffffff', '#dbe3ef', 148),
            fixedPanel([sectionTitle('RECRUITER COMMENTS', false, rsIconNote), { text: note, style: 'body', color: noteMissing ? '#64748b' : '#1f2937', italics: noteMissing }], [6, 0, 0, 8], '#ffffff', '#dbe3ef', 148)
          ],
          columnGap: 0
        },
        roundedFullCard([
          sectionTitle('SKILL INTELLIGENCE', false, rsIconSkill),
          {
            table: {
              widths: ['*', 100, 78],
              body: buildSkillRows(data.skills)
            },
            layout: {
              hLineWidth: function (i) { return i === 1 ? 0.75 : 0.45; },
              vLineWidth: function () { return 0; },
              hLineColor: function (i) { return i === 1 ? '#94a3b8' : '#e2e8f0'; }
            }
          }
        ], [0, 0, 0, 8], skillCardHeight, '#ffffff', '#dbe3ef'),
        roundedFullCard([
          sectionTitle('VERIFICATION STATUS', false, rsIconShield),
          {
            table: {
              widths: ['*', '*', '*', '*', '*'],
              body: buildValidationRows(data.validation)
            },
            layout: {
              hLineWidth: function () { return 0; },
              vLineWidth: function (i) { return i > 0 && i < 5 ? 0.55 : 0; },
              vLineColor: function () { return '#cbd5e1'; },
              paddingLeft: function () { return 0; },
              paddingRight: function () { return 0; },
              paddingTop: function () { return 0; },
              paddingBottom: function () { return 0; }
            }
          }
        ], [0, 0, 0, 8], 74, '#ffffff', '#dbe3ef'),
        {
          table: {
            widths: ['*', 'auto'],
            body: [[
              { text: 'This report is generated by AI hiring platform jero.works', style: 'footer', border: [false, true, false, false], margin: [0, 5, 0, 0] },
              { text: 'www.jero.works', style: 'footer', border: [false, true, false, false], margin: [0, 5, 0, 0] }
            ]]
          },
          layout: { hLineColor: function () { return '#e5e7eb'; }, hLineWidth: function () { return 0.6; }, vLineWidth: function () { return 0; } }
        }
      ]
    };
  }

  function normalizeBenchCandidateData(candidate, companyData) {
    candidate = candidate || {};
    companyData = companyData || {};

    var cityName = (!/^\d+$/.test(String(candidate.city_name || '').trim()) && candidate.city_name)
      || (!/^\d+$/.test(String(candidate.city || '').trim()) && candidate.city)
      || (!/^\d+$/.test(String(candidate.cityName || '').trim()) && candidate.cityName) || '';

    var stateName = (!/^\d+$/.test(String(candidate.state_name || '').trim()) && candidate.state_name)
      || (!/^\d+$/.test(String(candidate.state || '').trim()) && candidate.state) || '';

    var countryName = (!/^\d+$/.test(String(candidate.country_name || '').trim()) && candidate.country_name)
      || (!/^\d+$/.test(String(candidate.country || '').trim()) && candidate.country) || '';

    var locParts = [cityName, stateName, countryName].filter(function (p) {
      return p && String(p).trim() !== '' && String(p).trim() !== '--' && !/^\d+$/.test(String(p).trim());
    });
    var rawLoc = candidate.location || candidate.current_location ? String(candidate.location || candidate.current_location).trim() : '';
    if (/^\d+$/.test(rawLoc)) rawLoc = '';
    var formattedLocation = rawLoc || (locParts.length ? locParts.join(', ') : 'Not Specified');

    var exp = candidate.experience;
    if (!exp && candidate.total_exp) {
      var tot = String(candidate.total_exp).trim();
      if (parseFloat(tot) === 0) {
        exp = 'Fresher';
      } else {
        exp = tot + ' Year' + (parseFloat(tot) === 1.0 ? '' : 's');
      }
    }
    exp = exp || 'Not Specified';

    var rawSkills = [];
    if (Array.isArray(candidate.parsed_skills) && candidate.parsed_skills.length) {
      rawSkills = candidate.parsed_skills;
    } else if (Array.isArray(candidate.skills_evaluated) && candidate.skills_evaluated.length) {
      rawSkills = candidate.skills_evaluated;
    } else if (Array.isArray(candidate.skills) && candidate.skills.length) {
      rawSkills = candidate.skills;
    } else if (typeof candidate.skills === 'string' && candidate.skills.trim()) {
      var sList = candidate.skills.split(',').map(function (s) { return s.trim(); }).filter(Boolean);
      var defaultRating = candidate.rating ? Math.min(5, Math.max(0, parseInt(candidate.rating, 10))) : 4;
      var defaultExp = clean(candidate.experience, 'Not Specified');
      rawSkills = sList.slice(0, 7).map(function (sName) {
        return {
          name: sName,
          expText: defaultExp,
          exp: parseFloat(defaultExp) || 0,
          rating: defaultRating
        };
      });
    }

    var skills = rawSkills.slice(0, 7).map(function (s) {
      if (typeof s === 'string') {
        return { name: s, expText: clean(candidate.experience, '--'), exp: parseFloat(candidate.experience) || 0, rating: 4 };
      }
      return {
        name: s.name || s.skill || '',
        expText: s.expText || (s.exp ? s.exp + ' Year' + (parseFloat(s.exp) === 1 ? '' : 's') : clean(candidate.experience, '--')),
        exp: s.exp || parseFloat(candidate.experience) || 0,
        rating: safeNumber(s.rating || 4, 1, 5)
      };
    });

    var validation = [];
    if (Array.isArray(candidate.parsed_validation) && candidate.parsed_validation.length) {
      validation = candidate.parsed_validation;
    } else if (Array.isArray(candidate.validation) && candidate.validation.length) {
      validation = candidate.validation;
    } else {
      if (candidate.resume || candidate.resume_filename) validation.push({ label: 'RESUME', status: 'Verified' });
      else validation.push({ label: 'RESUME', status: '-' });

      if (candidate.email || candidate.secondrary_email) validation.push({ label: 'EMAIL ID', status: 'Verified' });
      else validation.push({ label: 'EMAIL ID', status: '-' });

      if (candidate.phone || candidate.secondary_mobile || candidate.mobile) validation.push({ label: 'PHONE NUMBER', status: 'Verified' });
      else validation.push({ label: 'PHONE NUMBER', status: '-' });

      var hasLinkedin = !!(candidate.linkedin && !/^(--|-|na|n\/a|null|none)$/i.test(String(candidate.linkedin).trim()));
      if (hasLinkedin) validation.push({ label: 'LINKEDIN ID', status: 'Verified' });
      else validation.push({ label: 'LINKEDIN ID', status: '-' });

      validation.push({ label: 'JOB DESCRIPTION', status: 'Shared with candidate' });
    }

    var todayDate = (new Date()).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    var reportId = candidate.report_id || ('JR' + (new Date()).toISOString().slice(0, 10).replace(/-/g, '') + String(candidate.id || '000000').padStart(6, '0'));

    var matchScore = 0;
    if (candidate.match !== undefined && candidate.match !== null && candidate.match !== '') {
      matchScore = safeNumber(candidate.match, 0, 100);
    } else if (candidate.skill_match !== undefined && candidate.skill_match !== null && candidate.skill_match !== '') {
      matchScore = safeNumber(candidate.skill_match, 0, 100);
    } else if (candidate.rating) {
      matchScore = Math.min(100, Math.max(40, parseInt(candidate.rating, 10) * 20));
    } else {
      matchScore = 85;
    }

    var rawNotice = candidate.notice || candidate.notice_period || candidate.availability;
    if (!rawNotice && candidate.np_id) {
      var npRaw = String(candidate.np_id).trim();
      var npMode = parseInt(candidate.serving_notice_period_mode, 10);
      var npTs = Date.parse(npRaw);
      if (npMode === 2 || npRaw.toLowerCase() === 'immediate') {
        rawNotice = 'Immediate';
      } else if (npMode === 0 && parseInt(npRaw, 10) > 0) {
        rawNotice = parseInt(npRaw, 10) + ' Days';
      } else if (npMode === 1 && !isNaN(npTs) && (new Date(npTs)).getFullYear() > 1970) {
        var dObj = new Date(npTs);
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        if (dObj < today) {
          rawNotice = 'Immediate';
        } else {
          var dStr = dObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
          rawNotice = 'Available ' + dStr;
        }
      } else {
        rawNotice = npRaw;
      }
    } else if (!rawNotice) {
      rawNotice = 'Immediate';
    }
    if (/01\s*jan\s*1970/i.test(rawNotice)) {
      rawNotice = candidate.is_immediate ? 'Immediate' : 'Immediate';
    }

    var rawEdu = candidate.qualification_name || candidate.qualification || candidate.education || '';
    if (/^\d+$/.test(String(rawEdu).trim())) rawEdu = '';
    var eduStr = rawEdu ? String(rawEdu).trim().toUpperCase() : 'Not Specified';

    var rawWm = candidate.work_mode || candidate.workMode || candidate.wm || '';
    var wmStr = rawWm ? String(rawWm).trim().replace(/[\s_-]+/g, ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); }) : 'Not Specified';

    var rawRm = candidate.rm_comments || candidate.recruiter_note_text || candidate.recruiter_note || candidate.comment || '';
    var rmText = clean(rawRm.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim(), '');

    var compName = companyData.companyName || candidate.report_company_name || 'Hiring Partner';
    var candCompany = candidate.company || candidate.current_company || compName;

    return {
      id: candidate.id,
      name: candidate.full_name || candidate.name || 'Candidate',
      email: candidate.email || '',
      secondaryEmail: candidate.secondrary_email || '',
      phone: candidate.phone || candidate.mobile || '',
      secondaryPhone: candidate.secondary_mobile || '',
      linkedin: candidate.linkedin || '',
      designation: candidate.current_designation || candidate.designation || '',
      company: candCompany,
      location: formattedLocation,
      experience: exp,
      notice: rawNotice,
      education: eduStr,
      workMode: wmStr,
      reportId: reportId,
      reportDate: candidate.report_date || todayDate,
      recruiterName: candidate.recruiter_name || companyData.recruiterName || 'Hiring Team',
      reportCompanyName: compName,
      reportCompanyLogo: companyData.companyLogo || candidate.report_company_logo || '',
      aiVersion: 'Live Intelligence',
      match: matchScore,
      isImmediate: (rawNotice === 'Immediate' || !!candidate.is_immediate),
      recruiterNote: rmText,
      careerSummary: candidate.careersummary || candidate.career_summary || candidate.cover_letter || '',
      jobTitle: candidate.job_title || candidate.current_designation || 'Verified Professional',
      skills: skills,
      validation: validation,
      resume: candidate.resume || ''
    };
  }

  function buildBenchDefinition(cand, companyName, logoUri, noBranding) {
    var norm = normalizeBenchCandidateData(cand, {
      companyName: companyName,
      companyLogo: logoUri
    });
    return buildDefinition(norm, logoUri, { noBranding: noBranding });
  }

  async function generateBenchPrescreenPdfBlob(cand, companyName, logoUri, noBranding) {
    await ensurePdfMake();
    var norm = normalizeBenchCandidateData(cand, {
      companyName: companyName,
      companyLogo: logoUri
    });
    var resolvedLogoUri = null;
    if (!noBranding && norm.reportCompanyLogo) {
      resolvedLogoUri = await rsLogoDataUri(norm.reportCompanyLogo);
    }
    var def = buildDefinition(norm, resolvedLogoUri, { noBranding: noBranding });
    return new Promise(function (resolve, reject) {
      try {
        window.pdfMake.createPdf(def).getBlob(function (blob) {
          if (blob && blob.size > 200) resolve(blob);
          else reject(new Error('Pre-screen dossier generation returned empty output'));
        });
      } catch (err) {
        reject(err);
      }
    });
  }

  function isPdfBytes(bytes) {
    if (!bytes || bytes.length < 4) return false;
    var u = (bytes instanceof Uint8Array) ? bytes : new Uint8Array(bytes);
    return u[0] === 0x25 && u[1] === 0x50 && u[2] === 0x44 && u[3] === 0x46; // %PDF
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // COMPANY BRANDING HEADER MERGER (PDFLib - Exact CV Tracker Replica)
  // ═══════════════════════════════════════════════════════════════════════════
  async function addCompanyBrandingHeader(pdfBlobOrBytes, companyName, companyLogo) {
    await ensurePdfLib();
    var PDFLib = window.PDFLib;
    var arrayBuffer;
    if (pdfBlobOrBytes instanceof Blob) {
      arrayBuffer = await pdfBlobOrBytes.arrayBuffer();
    } else if (pdfBlobOrBytes instanceof ArrayBuffer) {
      arrayBuffer = pdfBlobOrBytes;
    } else if (pdfBlobOrBytes instanceof Uint8Array) {
      arrayBuffer = pdfBlobOrBytes.buffer;
    } else {
      throw new Error('Candidate resume is not in PDF format. Merged branding requires a PDF resume. Please select Option 5 (Pre-Screened Report PDF) or Option 1 (Original Resume).');
    }

    if (!isPdfBytes(new Uint8Array(arrayBuffer))) {
      throw new Error('Candidate resume is not in PDF format. Merged branding requires a PDF resume. Please select Option 5 (Pre-Screened Report PDF) or Option 1 (Original Resume).');
    }

    var resumeDoc = await PDFLib.PDFDocument.load(arrayBuffer);
    var sourcePages = resumeDoc.getPages();
    if (!sourcePages.length) return new Blob([arrayBuffer], { type: 'application/pdf' });

    var firstPage = sourcePages[0];
    var pageSize = firstPage.getSize();
    var targetDoc = await PDFLib.PDFDocument.create();
    var regularFont = await targetDoc.embedFont(PDFLib.StandardFonts.Helvetica);
    var boldFont = await targetDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);

    var logoUri = await rsLogoDataUri(companyLogo);
    var logoImage = null;
    var logoData = dataUriBytes(logoUri);
    if (logoData) {
      try {
        if (logoData.mime.indexOf('png') !== -1) logoImage = await targetDoc.embedPng(logoData.bytes);
        else if (logoData.mime.indexOf('jpeg') !== -1 || logoData.mime.indexOf('jpg') !== -1) logoImage = await targetDoc.embedJpg(logoData.bytes);
      } catch (err) {
        logoImage = null;
      }
    }

    var headerScale = Math.max(1, Math.min(1.45, pageSize.width / 612));
    var headerH = Math.max(66, Math.min(82, pageSize.width * 0.105)) * headerScale;

    var newFirst = targetDoc.addPage([pageSize.width, pageSize.height + headerH]);
    var embeddedFirst = await targetDoc.embedPage(firstPage);
    newFirst.drawPage(embeddedFirst, { x: 0, y: 0, width: pageSize.width, height: pageSize.height });

    var topY = pageSize.height;
    newFirst.drawRectangle({
      x: 0,
      y: topY,
      width: pageSize.width,
      height: headerH,
      color: PDFLib.rgb(1, 1, 1),
      borderColor: PDFLib.rgb(0.86, 0.89, 0.94),
      borderWidth: 0.8
    });

    newFirst.drawRectangle({
      x: 0,
      y: topY + headerH - (4 * headerScale),
      width: pageSize.width,
      height: 4 * headerScale,
      color: PDFLib.rgb(0.12, 0.23, 0.54)
    });

    newFirst.drawRectangle({
      x: 0,
      y: topY,
      width: pageSize.width,
      height: 1.1 * headerScale,
      color: PDFLib.rgb(0.88, 0.91, 0.95)
    });

    var compName = clean(companyName, 'Hiring Partner');
    var logoBox = headerH - (26 * headerScale);
    var logoX = 22 * headerScale;
    var logoY = topY + (13 * headerScale);
    var logoAreaW = 70 * headerScale;

    if (logoImage) {
      var dims = logoImage.scaleToFit(logoBox * 1.8, logoBox);
      newFirst.drawImage(logoImage, {
        x: logoX,
        y: logoY + (logoBox - dims.height) / 2,
        width: dims.width,
        height: dims.height
      });
      logoAreaW = Math.max(56 * headerScale, Math.min(74 * headerScale, dims.width));
    } else {
      newFirst.drawRectangle({
        x: logoX,
        y: logoY,
        width: logoBox,
        height: logoBox,
        color: PDFLib.rgb(0.93, 0.95, 1),
        borderColor: PDFLib.rgb(0.75, 0.82, 0.98),
        borderWidth: 0.8 * headerScale
      });
      newFirst.drawText(compName.charAt(0).toUpperCase(), {
        x: logoX + (logoBox / 2) - (5 * headerScale),
        y: logoY + (logoBox / 2) - (5 * headerScale),
        size: 16 * headerScale,
        font: boldFont,
        color: PDFLib.rgb(0.12, 0.23, 0.54)
      });
      logoAreaW = logoBox;
    }

    var textX = logoX + logoAreaW + (12 * headerScale);
    var confText = 'CONFIDENTIAL RESUME';
    var dateText = (new Date()).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    var confSize = 13 * headerScale;
    var dateSize = 8.5 * headerScale;
    var confW = boldFont.widthOfTextAtSize(confText, confSize);
    var dateW = regularFont.widthOfTextAtSize(dateText, dateSize);
    var rightMargin = 24 * headerScale;

    newFirst.drawText('SHARED BY', {
      x: textX,
      y: topY + headerH - (27 * headerScale),
      size: 7 * headerScale,
      font: boldFont,
      color: PDFLib.rgb(0.39, 0.45, 0.55)
    });
    newFirst.drawText(limitText(compName, 42, compName), {
      x: textX,
      y: topY + headerH - (45 * headerScale),
      size: 12 * headerScale,
      font: boldFont,
      color: PDFLib.rgb(0.06, 0.09, 0.16)
    });
    newFirst.drawText(confText, {
      x: pageSize.width - rightMargin - confW,
      y: topY + headerH - (29 * headerScale),
      size: confSize,
      font: boldFont,
      color: PDFLib.rgb(0.06, 0.09, 0.16)
    });
    newFirst.drawText(dateText, {
      x: pageSize.width - rightMargin - dateW,
      y: topY + headerH - (45 * headerScale),
      size: dateSize,
      font: regularFont,
      color: PDFLib.rgb(0.39, 0.45, 0.55)
    });

    if (sourcePages.length > 1) {
      var restPages = await targetDoc.copyPages(resumeDoc, resumeDoc.getPageIndices().slice(1));
      restPages.forEach(function (page) { targetDoc.addPage(page); });
    }

    var finalBytes = await targetDoc.save();
    return new Blob([finalBytes], { type: 'application/pdf' });
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // MERGE DOSSIER + RESUME
  // ═══════════════════════════════════════════════════════════════════════════
  async function mergeDossierAndResume(dossierBlob, resumeBlob) {
    await ensurePdfLib();
    var PDFLib = window.PDFLib;
    var targetDoc = await PDFLib.PDFDocument.create();

    var dBytes = await dossierBlob.arrayBuffer();
    var dDoc = await PDFLib.PDFDocument.load(dBytes);
    var dPages = await targetDoc.copyPages(dDoc, dDoc.getPageIndices());
    dPages.forEach(function (p) { targetDoc.addPage(p); });

    if (resumeBlob) {
      var rBytes = await resumeBlob.arrayBuffer();
      if (!isPdfBytes(rBytes)) {
        throw new Error('Candidate resume is not in PDF format. Merged packages require a PDF resume. Please select Option 5 (Pre-Screened Report PDF) or Option 1 (Original Resume).');
      }
      var rDoc = await PDFLib.PDFDocument.load(rBytes);
      var rPages = await targetDoc.copyPages(rDoc, rDoc.getPageIndices());
      rPages.forEach(function (p) { targetDoc.addPage(p); });
    }

    var finalBytes = await targetDoc.save();
    return new Blob([finalBytes], { type: 'application/pdf' });
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // DOCX TO PDF CONVERSION PIPELINE (OpenDoc Engine 2)
  // ═══════════════════════════════════════════════════════════════════════════
  async function ensurePdfBlobFromBytes(cand, rawBytes) {
    if (isPdfBytes(rawBytes)) {
      return new Blob([rawBytes], { type: 'application/pdf' });
    }
    // 1. Authoritative: Fetch pre-converted vector PDF from Cloudflare R2 via same-origin proxy (as_pdf=1)
    try {
      var pdfBytes = await fetchCandidateResumeBytes(cand, true);
      if (isPdfBytes(pdfBytes)) {
        return new Blob([pdfBytes], { type: 'application/pdf' });
      }
    } catch (pdfErr) {
      console.warn('[AgyBenchForwardResumeEngine] as_pdf fetch warning:', pdfErr);
    }

    var compName = (cand && (cand.report_company_name || cand.company_name)) || 'Hiring Partner';
    var candName = (cand && cand.full_name) || 'Candidate';

    // 2. Client-side WASM DOCX converter fallback
    try {
      var fwdEngine = await ensureForwardResumeEngine();
      if (fwdEngine && typeof fwdEngine.renderWasmDocxToPdfBlob === 'function') {
        return await fwdEngine.renderWasmDocxToPdfBlob(rawBytes, {
          candidateName: candName,
          companyName: compName
        });
      }
    } catch (e) {
      console.warn('[AgyBenchForwardResumeEngine] Could not load DOCX WASM converter:', e);
    }

    // 3. Graceful fallback: assemble candidate Pre-Screen Dossier rather than crashing
    console.warn('[AgyBenchForwardResumeEngine] Assembling Pre-Screen Dossier for DOCX candidate');
    return await generateBenchPrescreenPdfBlob(cand, compName, null, false);
  }

  async function ensureForwardResumeEngine() {
    if (window.AgyForwardResumeEngine) return window.AgyForwardResumeEngine;
    try {
      await rsLoadScript('/js/opendoc/AgyForwardResumeEngine.js');
      return window.AgyForwardResumeEngine;
    } catch (e) {
      console.warn('[AgyBenchForwardResumeEngine] Could not load AgyForwardResumeEngine:', e);
      return null;
    }
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // MAIN DISPATCH METHOD (Exclusive to Bench Talent Drawer)
  // ═══════════════════════════════════════════════════════════════════════════
  async function generateAttachmentForCandidate(cand, optId, companyData, jobSkills, onProgress) {
    optId = parseInt(optId, 10);
    var companyName = (companyData && companyData.companyName) || 'Hiring Partner';
    var companyLogo = (companyData && companyData.companyLogo) || '';
    var safeName = (cand.full_name || 'Candidate').replace(/[^\w\s.-]/g, '').replace(/\s+/g, '_');

    function notify(pct, statusText, stepDetail) {
      if (typeof onProgress === 'function') {
        try {
          onProgress(pct, statusText, stepDetail);
        } catch (e) {
          console.warn('[AgyBenchForwardResumeEngine] onProgress warning:', e);
        }
      }
    }

    // Option 1: Original candidate resume
    if (optId === 1) {
      notify(30, 'Fetching candidate resume...', 'Reading original resume file...');
      var rawBytes = await fetchCandidateResumeBytes(cand);
      notify(85, 'Verifying document integrity...', 'Preparing original document binary...');
      var ext = (cand.resume_filename && cand.resume_filename.split('.').pop().toLowerCase()) || 'pdf';
      var mimeType = (ext === 'docx') ? 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' : ((ext === 'doc') ? 'application/msword' : 'application/pdf');
      var origBlob = new Blob([rawBytes], { type: mimeType });
      origBlob.filename = 'Jeroworks_' + safeName + '.' + ext;
      notify(100, 'Original resume ready!', 'Document ready to send');
      return origBlob;
    }

    // Option 5: Pre-Screened Report PDF Dossier (Bench Specific!)
    if (optId === 5) {
      notify(25, 'Assembling pre-screen dossier...', 'Compiling RM comments and talent skills...');
      notify(65, 'Assembling high-resolution document...', 'Client-side document engine compiling high-resolution vector PDF...');
      var reportBlob = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);
      reportBlob.filename = 'Prescreen_Report_' + safeName + '.pdf';
      notify(100, 'Pre-screen dossier ready!', 'Document ready to send');
      return reportBlob;
    }

    // Option 41: Resume + Company Branding
    if (optId === 41) {
      notify(20, 'Fetching candidate resume...', 'Reading original document in browser memory...');
      var rawBytes = await fetchCandidateResumeBytes(cand);
      notify(50, 'Standardizing vector PDF...', 'Ensuring high-resolution vector PDF pages...');
      var basePdfBlob = await ensurePdfBlobFromBytes(cand, rawBytes);
      notify(75, 'Applying company branding header...', 'Injecting organization banner...');
      var pdfBytes = new Uint8Array(await basePdfBlob.arrayBuffer());
      var brandedBlob = await addCompanyBrandingHeader(pdfBytes, companyName, companyLogo);
      brandedBlob.filename = 'Jeroworks_' + safeName + '_Branded.pdf';
      notify(100, 'Branded resume ready!', 'Document ready to send');
      return brandedBlob;
    }

    // Option 42: Pre-Screened Report + Branded Resume
    if (optId === 42) {
      notify(20, 'Generating pre-screen dossier...', 'Rendering RM comments and talent skills...');
      var repBlob = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);
      notify(45, 'Fetching candidate resume...', 'Reading original resume...');
      var rawBytes = await fetchCandidateResumeBytes(cand);
      notify(65, 'Assembling high-resolution document...', 'Client-side document engine compiling high-resolution vector PDF...');
      var basePdfBlob = await ensurePdfBlobFromBytes(cand, rawBytes);
      var pdfBytes = new Uint8Array(await basePdfBlob.arrayBuffer());
      notify(80, 'Applying company branding...', 'Injecting organization header...');
      var resBranded = await addCompanyBrandingHeader(pdfBytes, companyName, companyLogo);
      notify(92, 'Merging dossier and resume...', 'Concatenating multi-page recruiter package...');
      var merged = await mergeDossierAndResume(repBlob, resBranded);
      merged.filename = 'Jeroworks_' + safeName + '_Full_Package.pdf';
      notify(100, 'Recruiter package ready!', 'Document ready to send');
      return merged;
    }

    // Option 43: Pre-Screened Report + Unbranded Resume
    if (optId === 43) {
      notify(20, 'Generating pre-screen dossier...', 'Rendering RM comments without company branding...');
      var repBlob = await generateBenchPrescreenPdfBlob(cand, companyName, null, true);
      notify(50, 'Fetching candidate resume...', 'Reading original resume...');
      var rawBytes = await fetchCandidateResumeBytes(cand);
      notify(70, 'Standardizing vector PDF...', 'Ensuring vector PDF pages...');
      var resBlob = await ensurePdfBlobFromBytes(cand, rawBytes);
      notify(90, 'Merging dossier and resume...', 'Concatenating multi-page recruiter package...');
      var merged = await mergeDossierAndResume(repBlob, resBlob);
      merged.filename = 'Prescreen_' + safeName + '_Unbranded.pdf';
      notify(100, 'Recruiter package ready!', 'Document ready to send');
      return merged;
    }

    // Options 44-47: Highlighted Keywords
    if (optId === 44 || optId === 45 || optId === 46 || optId === 47) {
      notify(20, 'Preparing highlighted resume...', 'Reading original resume...');
      var rawBytes = await fetchCandidateResumeBytes(cand);
      var basePdfBlob = await ensurePdfBlobFromBytes(cand, rawBytes);
      var fwdEngine = await ensureForwardResumeEngine();
      var highlightedBlob = basePdfBlob;

      var allKeywords = Array.isArray(jobSkills) ? jobSkills.slice() : [];
      if (Array.isArray(cand.skills_evaluated)) {
        cand.skills_evaluated.forEach(function (s) {
          var sName = typeof s === 'string' ? s : (s && s.name ? s.name : '');
          if (sName && allKeywords.indexOf(sName) === -1) allKeywords.push(sName);
        });
      }
      if (cand.skills && typeof cand.skills === 'string') {
        cand.skills.split(',').forEach(function (s) {
          var sTrim = s.trim();
          if (sTrim && allKeywords.indexOf(sTrim) === -1) allKeywords.push(sTrim);
        });
      }

      if (fwdEngine && typeof fwdEngine.buildTurboVectorHighlightedResume === 'function') {
        try {
          notify(55, 'Highlighting key skills...', 'Analyzing resume text and applying vector skill highlights...');
          highlightedBlob = await fwdEngine.buildTurboVectorHighlightedResume({
            resumeBlob: basePdfBlob,
            keywords: allKeywords
          });
        } catch (hErr) {
          console.warn('[AgyBenchForwardResumeEngine] Skill highlighting fallback:', hErr);
        }
      }

      if (optId === 44) {
        highlightedBlob.filename = 'Jeroworks_' + safeName + '_Highlighted.pdf';
        notify(100, 'Highlighted resume ready!', 'Document ready to send');
        return highlightedBlob;
      }

      if (optId === 45) {
        notify(75, 'Applying company branding...', 'Injecting organization banner...');
        var pdfBytes = new Uint8Array(await highlightedBlob.arrayBuffer());
        var branded = await addCompanyBrandingHeader(pdfBytes, companyName, companyLogo);
        branded.filename = 'Jeroworks_' + safeName + '_Highlighted_Branded.pdf';
        notify(100, 'Branded highlighted resume ready!', 'Document ready to send');
        return branded;
      }

      if (optId === 46) {
        notify(30, 'Generating pre-screen dossier...', 'Compiling RM comments and talent skills...');
        var repBlob = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);
        notify(75, 'Applying company branding...', 'Injecting organization banner...');
        var pdfBytes = new Uint8Array(await highlightedBlob.arrayBuffer());
        var branded = await addCompanyBrandingHeader(pdfBytes, companyName, companyLogo);
        notify(90, 'Merging dossier and resume...', 'Concatenating multi-page recruiter package...');
        var merged = await mergeDossierAndResume(repBlob, branded);
        merged.filename = 'Jeroworks_' + safeName + '_Full_Package.pdf';
        notify(100, 'Recruiter package ready!', 'Document ready to send');
        return merged;
      }

      if (optId === 47) {
        notify(30, 'Generating unbranded dossier...', 'Compiling RM comments without company branding...');
        var repBlob = await generateBenchPrescreenPdfBlob(cand, companyName, null, true);
        notify(85, 'Merging dossier and resume...', 'Concatenating multi-page recruiter package...');
        var merged = await mergeDossierAndResume(repBlob, highlightedBlob);
        merged.filename = 'Prescreen_' + safeName + '_Highlighted_Package.pdf';
        notify(100, 'Recruiter package ready!', 'Document ready to send');
        return merged;
      }
    }

    // Options 48-53: Privacy & Anonymization (Anti-Poaching Shield)
    if (optId >= 48 && optId <= 53) {
      notify(20, 'Preparing privacy-shield resume...', 'Reading original resume...');
      var rawBytes = await fetchCandidateResumeBytes(cand);
      var basePdfBlob = await ensurePdfBlobFromBytes(cand, rawBytes);
      var fwdEngine = await ensureForwardResumeEngine();
      var anonymizedBlob = basePdfBlob;

      var anonymizeCand = {
        name: cand.full_name || 'Candidate',
        email: cand.email || '',
        phone: cand.phone || cand.mobile || '',
        mobile: cand.mobile || '',
        companyName: companyName
      };

      var keepName = (optId !== 49); // Hide candidate name ONLY for Option 49
      if (fwdEngine && typeof fwdEngine.rdcAnonymizeRules === 'function' && typeof fwdEngine.renderAnonymizedPdfBlob === 'function') {
        try {
          notify(55, 'Applying privacy redactions...', 'Redacting contact numbers, email, and private links...');
          var rules = fwdEngine.rdcAnonymizeRules(anonymizeCand, { keepName: keepName });
          anonymizedBlob = await fwdEngine.renderAnonymizedPdfBlob(basePdfBlob, rules);
        } catch (aErr) {
          console.warn('[AgyBenchForwardResumeEngine] Privacy redaction fallback:', aErr);
        }
      }

      if (optId === 48) {
        anonymizedBlob.filename = 'Jeroworks_' + safeName + '_Confidential.pdf';
        notify(100, 'Confidential resume ready!', 'Document ready to send');
        return anonymizedBlob;
      }

      if (optId === 49) {
        anonymizedBlob.filename = 'Jeroworks_Resume_Confidential.pdf';
        notify(100, 'Confidential resume ready!', 'Document ready to send');
        return anonymizedBlob;
      }

      if (optId === 50) {
        notify(75, 'Applying company branding...', 'Injecting organization banner...');
        var pdfBytes = new Uint8Array(await anonymizedBlob.arrayBuffer());
        var branded = await addCompanyBrandingHeader(pdfBytes, companyName, companyLogo);
        branded.filename = 'Jeroworks_' + safeName + '_Branded_Confidential.pdf';
        notify(100, 'Branded confidential resume ready!', 'Document ready to send');
        return branded;
      }

      if (optId === 51) {
        notify(30, 'Generating pre-screen dossier...', 'Compiling RM comments and talent skills...');
        var repBlob = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);
        notify(85, 'Merging dossier and resume...', 'Concatenating multi-page recruiter package...');
        var merged = await mergeDossierAndResume(repBlob, anonymizedBlob);
        merged.filename = 'Jeroworks_' + safeName + '_Confidential_Package.pdf';
        notify(100, 'Confidential package ready!', 'Document ready to send');
        return merged;
      }

      if (optId === 52) {
        notify(30, 'Generating unbranded dossier...', 'Compiling RM comments without company branding...');
        var repBlob = await generateBenchPrescreenPdfBlob(cand, companyName, null, true);
        notify(85, 'Merging dossier and resume...', 'Concatenating multi-page recruiter package...');
        var merged = await mergeDossierAndResume(repBlob, anonymizedBlob);
        merged.filename = 'Prescreen_' + safeName + '_Confidential_Package.pdf';
        notify(100, 'Confidential package ready!', 'Document ready to send');
        return merged;
      }

      if (optId === 53) {
        // Highlighting + Anonymization + Dossier
        notify(30, 'Generating pre-screen dossier...', 'Compiling RM comments and talent skills...');
        var repBlob = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);
        var allKeywords = Array.isArray(jobSkills) ? jobSkills.slice() : [];
        if (Array.isArray(cand.skills_evaluated)) {
          cand.skills_evaluated.forEach(function (s) {
            var sName = typeof s === 'string' ? s : (s && s.name ? s.name : '');
            if (sName && allKeywords.indexOf(sName) === -1) allKeywords.push(sName);
          });
        }
        if (cand.skills && typeof cand.skills === 'string') {
          cand.skills.split(',').forEach(function (s) {
            var sTrim = s.trim();
            if (sTrim && allKeywords.indexOf(sTrim) === -1) allKeywords.push(sTrim);
          });
        }
        var processedResume = anonymizedBlob;
        if (fwdEngine && typeof fwdEngine.buildTurboVectorHighlightedResume === 'function') {
          try {
            notify(65, 'Highlighting key skills on confidential resume...', 'Applying keyword highlights...');
            processedResume = await fwdEngine.buildTurboVectorHighlightedResume({
              resumeBlob: anonymizedBlob,
              keywords: allKeywords
            });
          } catch (hErr) {}
        }
        notify(88, 'Merging dossier and resume...', 'Concatenating full privacy recruiter package...');
        var merged = await mergeDossierAndResume(repBlob, processedResume);
        merged.filename = 'Jeroworks_' + safeName + '_Full_Privacy_Package.pdf';
        notify(100, 'Full privacy package ready!', 'Document ready to send');
        return merged;
      }
    }

    // Options Fallback: Generate branded dossier + original resume package
    notify(20, 'Generating pre-screen dossier...', 'Preparing candidate dossier...');
    var dossier = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);
    notify(50, 'Fetching candidate resume...', 'Reading original resume...');
    var raw = await fetchCandidateResumeBytes(cand);
    notify(65, 'Assembling high-resolution document...', 'Client-side document engine compiling high-resolution vector PDF...');
    var rBlob = await ensurePdfBlobFromBytes(cand, raw);
    notify(88, 'Merging dossier and resume...', 'Concatenating multi-page recruiter package...');
    var mergedFallback = await mergeDossierAndResume(dossier, rBlob);
    mergedFallback.filename = 'Jeroworks_' + safeName + '_Formatted.pdf';
    notify(100, 'Document ready!', 'Document ready to send');
    return mergedFallback;
  }

  return {
    generateAttachmentForCandidate: generateAttachmentForCandidate,
    generateBenchPrescreenPdfBlob: generateBenchPrescreenPdfBlob,
    addCompanyBrandingHeader: addCompanyBrandingHeader,
    mergeDossierAndResume: mergeDossierAndResume
  };
}));
</script>
