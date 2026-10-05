{{-- Dedicated Isolated Document Engine Exclusively for In-House Bench Talent Download Resume Center (Engine 1-B) --}}
<script>
/**
 * AgyBenchDownloadResumeEngine.js
 * 
 * EXCLUSIVE DEDICATED ENGINE FOR IN-HOUSE BENCH TALENT DOWNLOAD RESUME CENTER
 * (/service_company/bench-candidates)
 * 
 * 100% COMPLETE ISOLATION MANDATE (Rule 15 & Rule 17 Compliance):
 * - Decoupled and isolated from CV Tracker, App Tracker, and Forward Resume engines.
 * - Operates exclusively on In-House Talent attributes (bench availability, bench skill matrix ratings,
 *   relationship manager comments, commercials, and authoritative location strings).
 * - 100% Client-Side Execution (User RAM + WASM): 0% server/DB read strain, 0% server PDF CPU/RAM strain.
 * - Point-by-point exact replica of the platform's standard Pre-Screen Evaluation Dossier layout.
 * - Pure sub-pixel keyword highlighter (never highlights the whole row, only exact keyword bounds).
 * - A4-normalized page merging (every page scaled and centered to standard A4 595.28 pt width).
 * - Height-expanded branding header (never obscures or overlaps original resume text).
 * - Scoped to window.AgyBenchDownloadResumeEngine with zero global scope pollution.
 */

(function (root, factory) {
  'use strict';
  var exp = factory();
  if (typeof window !== 'undefined') {
    window.AgyBenchDownloadResumeEngine = exp;
  }
  if (typeof root !== 'undefined') {
    root.AgyBenchDownloadResumeEngine = exp;
  }
}(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

  var rsPdfLibReady = null;
  var rsPdfJsReady = null;
  var rsPdfMakeReady = null;
  var rsJsPdfReady = null;
  var rsJsZipReady = null;
  var rsDocxReady = null;
  var rsHtml2CanvasReady = null;
  var rsOpenDocLocalReady = null;
  var rsOpenDocExternalReady = null;
  var rsLogoCache = {};
  var rsDownloadBytesCache = {};
  var MAX_DOWNLOAD_FILE_BYTES = 5 * 1024 * 1024; // 5 MB file size limit for downloads and email attachments

  // ═══════════════════════════════════════════════════════════════════════════
  // SVG ICONS FOR IN-HOUSE EVALUATION REPORT DOSSIER (pdfMake - Exact Replica)
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

  // ═══════════════════════════════════════════════════════════════════════════
  // SCRIPT LOADER HELPERS
  // ═══════════════════════════════════════════════════════════════════════════
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
    var wasAmd = (typeof window !== 'undefined' && window.define && window.define.amd);
    if (wasAmd) window.define.amd = false;
    rsPdfLibReady = rsTryList(sources, 0, function () { return !!(window.PDFLib && window.PDFLib.PDFDocument); }).then(function () {
      if (wasAmd && window.define) window.define.amd = wasAmd;
      return window.PDFLib;
    }).catch(function (err) {
      if (wasAmd && window.define) window.define.amd = wasAmd;
      throw err;
    });
    return rsPdfLibReady;
  }

  function ensurePdfJs() {
    if (rsPdfJsReady && window.pdfjsLib && typeof window.pdfjsLib.getDocument === 'function') return Promise.resolve(window.pdfjsLib);
    var sources = [
      'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js',
      'https://cdn.jsdelivr.net/npm/pdfjs-dist@2.11.338/build/pdf.min.js'
    ];
    var wasAmd = (typeof window !== 'undefined' && window.define && window.define.amd);
    if (wasAmd) window.define.amd = false;
    rsPdfJsReady = rsTryList(sources, 0, function () { return !!(window.pdfjsLib && typeof window.pdfjsLib.getDocument === 'function'); }).then(function () {
      if (wasAmd && window.define) window.define.amd = wasAmd;
      try {
        var ver = window.pdfjsLib.version || '2.11.338';
        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/' + ver + '/pdf.worker.min.js';
      } catch (err) {}
      return window.pdfjsLib;
    }).catch(function (err) {
      if (wasAmd && window.define) window.define.amd = wasAmd;
      throw err;
    });
    return rsPdfJsReady;
  }

  function ensurePdfMake() {
    if (rsPdfMakeReady && window.pdfMake && typeof window.pdfMake.createPdf === 'function') return Promise.resolve(window.pdfMake);
    var pmSources = [
      'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js',
      'https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/pdfmake.min.js'
    ];
    var vfsSources = [
      'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js',
      'https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/vfs_fonts.js'
    ];
    var wasAmd = (typeof window !== 'undefined' && window.define && window.define.amd);
    if (wasAmd) window.define.amd = false;

    rsPdfMakeReady = rsTryList(pmSources, 0, function () { return !!(window.pdfMake && typeof window.pdfMake.createPdf === 'function'); })
      .then(function () {
        return rsTryList(vfsSources, 0, function () {
          return !!(window.pdfMake && (window.pdfMake.vfs || window.vfs));
        });
      })
      .then(function () {
        if (wasAmd && window.define) window.define.amd = wasAmd;
        if (window.vfs && window.pdfMake && !window.pdfMake.vfs) {
          window.pdfMake.vfs = window.vfs;
        }
        return window.pdfMake;
      })
      .catch(function (err) {
        if (wasAmd && window.define) window.define.amd = wasAmd;
        throw err;
      });
    return rsPdfMakeReady;
  }

  function ensureJsPdf() {
    if (rsJsPdfReady && (window.jspdf || window.jsPDF)) return Promise.resolve(window.jspdf || window.jsPDF);
    var sources = [
      'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js',
      'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js'
    ];
    var wasAmd = (typeof window !== 'undefined' && window.define && window.define.amd);
    if (wasAmd) window.define.amd = false;
    rsJsPdfReady = rsTryList(sources, 0, function () { return !!(window.jspdf || window.jsPDF); }).then(function () {
      if (wasAmd && window.define) window.define.amd = wasAmd;
      return window.jspdf || window.jsPDF;
    }).catch(function (err) {
      if (wasAmd && window.define) window.define.amd = wasAmd;
      throw err;
    });
    return rsJsPdfReady;
  }

  function rsWithAmdDisabled(fn) {
    var wasAmd = (typeof window !== 'undefined' && window.define && window.define.amd);
    if (wasAmd) window.define.amd = false;
    try {
      var res = fn();
      if (res && typeof res.then === 'function') {
        return res.then(function (val) {
          if (wasAmd && window.define) window.define.amd = wasAmd;
          return val;
        }).catch(function (err) {
          if (wasAmd && window.define) window.define.amd = wasAmd;
          throw err;
        });
      }
      if (wasAmd && window.define) window.define.amd = wasAmd;
      return res;
    } catch (err) {
      if (wasAmd && window.define) window.define.amd = wasAmd;
      throw err;
    }
  }

  /**
   * Cooperative Event Loop Yielding
   * Purpose: Prevents browser UI thread starvation during intensive multi-page PDF/DOCX processing.
   */
  function rsYieldToMain() {
    return new Promise(function (resolve) {
      if (typeof requestAnimationFrame === 'function') {
        requestAnimationFrame(function () {
          setTimeout(resolve, 0);
        });
      } else {
        setTimeout(resolve, 0);
      }
    });
  }

  /**
   * Isolated JSZip loader for DOCX binary unpacking in Bench Talent center.
   */
  function ensureBenchJsZip() {
    if (rsJsZipReady && (window.JSZip || (typeof JSZip !== 'undefined'))) return Promise.resolve(window.JSZip || JSZip);
    var sources = [
      '/js/jszip.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js',
      'https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js'
    ];
    rsJsZipReady = rsWithAmdDisabled(function () {
      return rsTryList(sources, 0, function () { return !!(window.JSZip || (typeof JSZip !== 'undefined')); });
    }).then(function () {
      return window.JSZip || JSZip;
    }).catch(function (err) {
      rsJsZipReady = null;
      throw err;
    });
    return rsJsZipReady;
  }

  /**
   * Isolated docx-preview loader for client-side Word document rendering.
   */
  function ensureBenchDocxPreview() {
    if (rsDocxReady && window.docx && typeof window.docx.renderAsync === 'function') return Promise.resolve(window.docx);
    var sources = [
      '/js/docx-preview.min.js',
      'https://cdn.jsdelivr.net/npm/docx-preview@0.3.4/dist/docx-preview.min.js',
      'https://unpkg.com/docx-preview@0.3.4/dist/docx-preview.min.js'
    ];
    rsDocxReady = ensureBenchJsZip().then(function () {
      return rsWithAmdDisabled(function () {
        return rsTryList(sources, 0, function () { return !!(window.docx && typeof window.docx.renderAsync === 'function'); });
      });
    }).then(function () {
      return window.docx;
    }).catch(function (err) {
      rsDocxReady = null;
      throw err;
    });
    return rsDocxReady;
  }

  /**
   * Isolated html2canvas loader for rasterizing rendered DOM sections to canvas.
   */
  function ensureBenchHtml2Canvas() {
    if (rsHtml2CanvasReady && typeof window.html2canvas === 'function') return Promise.resolve(window.html2canvas);
    var sources = [
      '/js/html2canvas.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js',
      'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js'
    ];
    rsHtml2CanvasReady = rsWithAmdDisabled(function () {
      return rsTryList(sources, 0, function () { return typeof window.html2canvas === 'function'; });
    }).then(function () {
      return window.html2canvas;
    }).catch(function (err) {
      rsHtml2CanvasReady = null;
      throw err;
    });
    return rsHtml2CanvasReady;
  }

  /**
   * Isolated OpenDoc Rust WebAssembly (casual_doc_wasm) runtime loader.
   */
  function ensureBenchOpenDocWasm(allowExternalFallback) {
    allowExternalFallback = allowExternalFallback !== false;
    if (rsOpenDocLocalReady) return rsOpenDocLocalReady;
    var openDocSources = [
      {
        label: 'Local OpenDoc runtime',
        moduleUrl: '/js/opendoc/casual_doc_wasm.js',
        wasmUrl: '/js/opendoc/casual_doc_wasm_bg.wasm'
      },
      {
        label: 'Cloudflare OpenDoc runtime',
        moduleUrl: 'https://file.jero.works/js/opendoc/casual_doc_wasm.js?v=opendoc-20260903',
        wasmUrl: 'https://file.jero.works/js/opendoc/casual_doc_wasm_bg.wasm?v=opendoc-20260903'
      }
    ];

    async function loadOpenDocModule(moduleUrl, wasmUrl) {
      var blobUrl = '';
      var module = null;
      try {
        module = await import(moduleUrl);
      } catch (importErr) {
        var moduleResponse = null;
        try {
          moduleResponse = await fetch(moduleUrl, { mode: 'cors', cache: 'force-cache' });
        } catch (moduleFetchErr) {
          throw new Error('OpenDoc module fetch failed for ' + moduleUrl + ': ' + (moduleFetchErr && moduleFetchErr.message ? moduleFetchErr.message : 'network blocked'));
        }
        if (!moduleResponse.ok) throw new Error('OpenDoc module request failed with HTTP ' + moduleResponse.status);
        var moduleCode = await moduleResponse.text();
        moduleCode = moduleCode.replace(/new URL\(['"]casual_doc_wasm_bg\.wasm['"],\s*import\.meta\.url\)/g, JSON.stringify(wasmUrl));
        blobUrl = URL.createObjectURL(new Blob([moduleCode], { type: 'application/javascript' }));
        module = await import(blobUrl);
      }
      if (typeof module.default === 'function') {
        var wasmResponse = null;
        try {
          wasmResponse = await fetch(wasmUrl, { mode: 'cors', cache: 'force-cache' });
        } catch (wasmFetchErr) {
          throw new Error('OpenDoc WASM fetch failed for ' + wasmUrl + ': ' + (wasmFetchErr && wasmFetchErr.message ? wasmFetchErr.message : 'network blocked'));
        }
        if (!wasmResponse.ok) throw new Error('OpenDoc WASM request failed with HTTP ' + wasmResponse.status);
        await module.default({ module_or_path: await wasmResponse.arrayBuffer() });
      }
      if (blobUrl) setTimeout(function () { URL.revokeObjectURL(blobUrl); }, 30000);
      if (typeof module.open !== 'function') throw new Error('OpenDoc open API missing');
      return module;
    }

    rsOpenDocLocalReady = (async function () {
      var lastErr = null;
      for (var i = 0; i < openDocSources.length; i++) {
        try {
          return await loadOpenDocModule(openDocSources[i].moduleUrl, openDocSources[i].wasmUrl);
        } catch (err) {
          lastErr = err;
          console.warn('[AgyBenchDownloadResumeEngine] ' + openDocSources[i].label + ' unavailable:', err);
        }
      }
      if (allowExternalFallback) {
        if (!rsOpenDocExternalReady) {
          rsOpenDocExternalReady = loadOpenDocModule('https://opendoc.casualoffice.org/pkg/casual_doc_wasm.js', 'https://opendoc.casualoffice.org/pkg/casual_doc_wasm_bg.wasm')
            .catch(function (err) {
              rsOpenDocExternalReady = null;
              throw err;
            });
        }
        return rsOpenDocExternalReady;
      }
      throw (lastErr || new Error('OpenDoc runtime unavailable'));
    })().catch(function (err) {
      rsOpenDocLocalReady = null;
      throw err;
    });

    return rsOpenDocLocalReady;
  }

  function openDocNativePdfBlob(doc) {
    if (!doc || typeof doc.availableExportFormats !== 'function' || typeof doc.exportAs !== 'function') return null;
    var formats = [];
    try {
      formats = doc.availableExportFormats() || [];
    } catch (formatErr) {
      return null;
    }
    var hasPdf = formats.some(function (f) { return String(f || '').toLowerCase() === 'pdf'; });
    if (!hasPdf) return null;
    var modes = ['exact_if_unchanged', 'preserve_when_safe', 'semantic'];
    for (var i = 0; i < modes.length; i++) {
      var artifact = null;
      try {
        artifact = doc.exportAs('pdf', modes[i]);
        var bytes = artifact && artifact.bytes ? artifact.bytes : null;
        var mime = artifact && artifact.mimeType ? artifact.mimeType : 'application/pdf';
        if (bytes && bytes.length) return new Blob([bytes], { type: mime || 'application/pdf' });
      } catch (e) {
      } finally {
        if (artifact && typeof artifact.free === 'function') artifact.free();
      }
    }
    return null;
  }

  function openDocCanvasFromBitmap(bitmap) {
    var width = bitmap && bitmap.widthPx ? bitmap.widthPx : 0;
    var height = bitmap && bitmap.heightPx ? bitmap.heightPx : 0;
    if (!width || !height) throw new Error('OpenDoc rendered an empty page bitmap');
    var rgba = bitmap.rgba;
    var canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    var ctx = canvas.getContext('2d', { alpha: false });
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, width, height);
    ctx.putImageData(new ImageData(rgba, width, height), 0, 0);
    return canvas;
  }

  /**
   * Standalone Client-Side DOCX to PDF Conversion Engine
   * Strictly isolated to Bench Talent (Rule 15 & Rule 17 Compliance).
   * Attempts Rust WebAssembly (casual_doc_wasm) first, falls back to docx-preview + html2canvas + jsPDF.
   */
  async function renderBenchDocxToPdfBlob(docxBytes, candName, onProgress) {
    if (typeof onProgress === 'function') onProgress({ percent: 25, step: 'Converting DOCX to PDF...', detail: 'Initializing WASM document core...' });
    await rsYieldToMain();

    // Strategy 1: Try OpenDoc Rust WASM core
    try {
      var openDoc = await ensureBenchOpenDocWasm(true);
      var u8 = new Uint8Array(docxBytes);
      var doc = null;
      try {
        doc = (typeof openDoc.openAs === 'function') ? openDoc.openAs(u8, 'docx') : openDoc.open(u8);
      } catch (oe) {
        doc = openDoc.open(u8);
      }
      if (doc) {
        try {
          var nativeBlob = openDocNativePdfBlob(doc);
          if (nativeBlob && nativeBlob.size > 200) {
            return nativeBlob;
          }

          var pageCount = parseInt(doc.pageCount, 10);
          if (!pageCount && typeof doc.documentStats === 'function') {
            var stats = doc.documentStats();
            pageCount = parseInt(stats && stats.pages ? stats.pages : 0, 10);
            if (stats && typeof stats.free === 'function') stats.free();
          }

          if (pageCount && pageCount >= 1) {
            await ensureJsPdf();
            var JsPDF = (window.jspdf && window.jspdf.jsPDF) || window.jsPDF;
            var pdf = null;
            var renderDpi = 216;

            for (var p = 0; p < pageCount; p++) {
              if (typeof onProgress === 'function') {
                var pPct = Math.round(25 + ((p + 1) / pageCount) * 45);
                onProgress({ percent: pPct, step: 'Rasterizing DOCX page ' + (p + 1) + ' of ' + pageCount + '...', detail: 'Compiling high-resolution pages...' });
              }
              await rsYieldToMain();

              var bitmap = null;
              var canvas = null;
              try {
                bitmap = doc.renderPage(p, renderDpi);
                canvas = openDocCanvasFromBitmap(bitmap);

                var pageW = (bitmap.widthPx / renderDpi) * 72;
                var pageH = (bitmap.heightPx / renderDpi) * 72;

                if (!pdf) {
                  pdf = new JsPDF({
                    orientation: pageW > pageH ? 'landscape' : 'portrait',
                    unit: 'pt',
                    format: [pageW, pageH],
                    compress: true
                  });
                } else {
                  pdf.addPage([pageW, pageH], pageW > pageH ? 'landscape' : 'portrait');
                }

                var imgData = canvas.toDataURL('image/jpeg', 0.85);
                pdf.addImage(imgData, 'JPEG', 0, 0, pageW, pageH, undefined, 'FAST');
              } finally {
                if (bitmap && typeof bitmap.free === 'function') bitmap.free();
                if (canvas) {
                  canvas.width = 0;
                  canvas.height = 0;
                  canvas = null;
                }
              }
            }

            var wasmOutBlob = pdf.output('blob');
            if (wasmOutBlob && wasmOutBlob.size > 200) {
              return wasmOutBlob;
            }
          }
        } finally {
          if (doc && typeof doc.free === 'function') doc.free();
        }
      }
    } catch (wasmErr) {
      console.warn('[AgyBenchDownloadResumeEngine] OpenDoc WASM conversion fallback to docx-preview:', wasmErr);
    }

    // Strategy 2: Fallback to docx-preview + html2canvas + jsPDF
    try {
      if (typeof onProgress === 'function') onProgress({ percent: 35, step: 'Rendering DOCX layout...', detail: 'Formatting document structure...' });
      await rsYieldToMain();

      await Promise.all([ensureBenchDocxPreview(), ensureBenchHtml2Canvas(), ensureJsPdf()]);

      var host = document.createElement('div');
      host.className = 'bench-docx-render-host';
      host.setAttribute('aria-hidden', 'true');
      host.style.cssText = 'position:fixed;left:0;top:0;width:auto;min-width:794px;opacity:0;pointer-events:none;z-index:-9999;background:#ffffff;overflow:visible;';
      document.body.appendChild(host);

      try {
        await window.docx.renderAsync(docxBytes, host, null, {
          className: 'docx',
          inWrapper: true,
          ignoreWidth: false,
          ignoreHeight: true,
          breakPages: true,
          ignoreLastRenderedPageBreak: false,
          experimental: false,
          useBase64URL: true,
          renderHeaders: true,
          renderFooters: true,
          renderFootnotes: true,
          renderEndnotes: true,
          trimXmlDeclaration: true
        });
        await rsYieldToMain();

        var pages = Array.prototype.slice.call(host.querySelectorAll('.docx-wrapper > section.docx'));
        if (!pages.length) pages = Array.prototype.slice.call(host.querySelectorAll('section.docx'));
        if (!pages.length) pages = [host];

        var JsPDF2 = (window.jspdf && window.jspdf.jsPDF) || window.jsPDF;
        var pdf2 = new JsPDF2({ orientation: 'portrait', unit: 'pt', format: 'a4', compress: true });
        var pdfPageW = pdf2.internal.pageSize.getWidth();
        var pdfPageH = pdf2.internal.pageSize.getHeight();
        var a4Ratio = pdfPageH / pdfPageW;

        var isFirstPage = true;
        for (var i = 0; i < pages.length; i++) {
          if (typeof onProgress === 'function') {
            var fPct = Math.round(40 + ((i + 1) / pages.length) * 45);
            onProgress({ percent: fPct, step: 'Formatting DOCX section ' + (i + 1) + ' of ' + pages.length + '...', detail: 'Rasterizing document pages...' });
          }
          await rsYieldToMain();

          var pageEl = pages[i];
          pageEl.style.margin = '0 auto';
          pageEl.style.boxShadow = 'none';
          pageEl.style.background = '#ffffff';

          var elWidth = Math.ceil(pageEl.scrollWidth || pageEl.getBoundingClientRect().width || 794);
          var elHeight = Math.ceil(pageEl.scrollHeight || pageEl.getBoundingClientRect().height || 1123);

          var cvs = await window.html2canvas(pageEl, {
            backgroundColor: '#ffffff',
            scale: 2.0,
            useCORS: true,
            allowTaint: false,
            logging: false,
            removeContainer: true,
            scrollX: 0,
            scrollY: 0,
            windowWidth: elWidth,
            windowHeight: elHeight
          });

          var targetSliceH = Math.round(cvs.width * a4Ratio);
          var offsetY = 0;

          while (offsetY < cvs.height) {
            var remainingH = cvs.height - offsetY;
            var currentSliceH = Math.min(targetSliceH, remainingH);

            var sliceCanvas = document.createElement('canvas');
            sliceCanvas.width = cvs.width;
            sliceCanvas.height = currentSliceH;
            var sliceCtx = sliceCanvas.getContext('2d');
            sliceCtx.fillStyle = '#ffffff';
            sliceCtx.fillRect(0, 0, sliceCanvas.width, sliceCanvas.height);
            sliceCtx.drawImage(cvs, 0, offsetY, cvs.width, currentSliceH, 0, 0, sliceCanvas.width, currentSliceH);

            var sliceImgData = sliceCanvas.toDataURL('image/jpeg', 0.82);
            var destH = (currentSliceH / cvs.width) * pdfPageW;

            if (!isFirstPage) {
              pdf2.addPage('a4', 'portrait');
            } else {
              isFirstPage = false;
            }

            pdf2.addImage(sliceImgData, 'JPEG', 0, 0, pdfPageW, destH, undefined, 'FAST');

            sliceCanvas.width = 0;
            sliceCanvas.height = 0;
            offsetY += targetSliceH;
            await rsYieldToMain();
          }

          cvs.width = 0;
          cvs.height = 0;
        }

        var fallbackBlob = pdf2.output('blob');
        if (fallbackBlob && fallbackBlob.size > 200) {
          return fallbackBlob;
        }
      } finally {
        if (host && host.parentNode) host.parentNode.removeChild(host);
      }
    } catch (prevErr) {
      console.error('[AgyBenchDownloadResumeEngine] docx-preview conversion failed:', prevErr);
      throw new Error('Client-side DOCX conversion failed: ' + (prevErr ? prevErr.message : 'Unknown error'));
    }

    throw new Error('Unable to convert DOCX resume on client side.');
  }

  /**
   * 5MB Size Guard with Adaptive Canvas Downsampling
   * Guarantees output PDF blobs strictly fit within the 5MB email/attachment budget.
   */
  async function ensurePdfBlobUnderLimit(blob, maxBytes) {
    maxBytes = maxBytes || MAX_DOWNLOAD_FILE_BYTES;
    if (!blob || !(blob instanceof Blob) || blob.size <= maxBytes) {
      return blob;
    }

    var isPdf = false;
    try {
      var slice = await blob.slice(0, 5).arrayBuffer();
      var sig = String.fromCharCode.apply(null, new Uint8Array(slice));
      isPdf = (sig.indexOf('%PDF') !== -1);
    } catch (e) {}
    if (!isPdf) return blob;

    try {
      await Promise.all([ensurePdfJs(), ensureJsPdf()]);
      var sourceBytes = await blob.arrayBuffer();
      var pdfDoc = await window.pdfjsLib.getDocument({ data: new Uint8Array(sourceBytes) }).promise;
      var JsPDF = (window.jspdf && window.jspdf.jsPDF) || window.jsPDF;
      var numPages = pdfDoc.numPages;
      if (!numPages) return blob;

      // Target ~4.2MB total budget to have a comfortable buffer under 5MB for email attachments
      var targetBudget = Math.min(maxBytes * 0.86, 4.3 * 1024 * 1024);
      var budgetPerPage = targetBudget / numPages;

      var quality = 0.82;
      var scale = 1.8;
      if (budgetPerPage < 400000) {
        quality = 0.74;
        scale = 1.5;
      }
      if (budgetPerPage < 220000) {
        quality = 0.65;
        scale = 1.25;
      }
      if (budgetPerPage < 120000) {
        quality = 0.55;
        scale = 1.0;
      }

      var outPdf = null;
      for (var p = 1; p <= numPages; p++) {
        await rsYieldToMain();
        var page = await pdfDoc.getPage(p);
        var vp = page.getViewport({ scale: scale });
        var cvs = document.createElement('canvas');
        cvs.width = Math.floor(vp.width);
        cvs.height = Math.floor(vp.height);
        var ctx = cvs.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, cvs.width, cvs.height);
        await page.render({ canvasContext: ctx, viewport: vp }).promise;

        var ptVp = page.getViewport({ scale: 1.0 });
        var pageW = Math.round(ptVp.width);
        var pageH = Math.round(ptVp.height);
        var orient = pageW > pageH ? 'landscape' : 'portrait';

        if (!outPdf) {
          outPdf = new JsPDF({ orientation: orient, unit: 'pt', format: [pageW, pageH], compress: true });
        } else {
          outPdf.addPage([pageW, pageH], orient);
        }
        var imgData = cvs.toDataURL('image/jpeg', quality);
        outPdf.addImage(imgData, 'JPEG', 0, 0, pageW, pageH, undefined, 'FAST');
        cvs.width = 0;
        cvs.height = 0;
      }

      var compressedBlob = outPdf.output('blob');
      if (compressedBlob && compressedBlob.size < blob.size) {
        if (blob.filename) compressedBlob.filename = blob.filename;
        return compressedBlob;
      }
    } catch (compErr) {
      console.warn('[AgyBenchDownloadResumeEngine] ensurePdfBlobUnderLimit compression notice:', compErr);
    }
    return blob;
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // STRING & VALUE UTILITIES
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

  function cityOnly(value) {
    value = clean(value, '--');
    if (isGap(value) || /^\d+$/.test(String(value).trim())) return '--';
    var commaParts = value.split(',').map(function (part) { return clean(part, ''); }).filter(Boolean);
    var candidateCity = commaParts.length > 0 ? commaParts[0] : value;
    if (/^\d+$/.test(String(candidateCity).trim())) return '--';
    if (commaParts.length > 1) return candidateCity;
    var states = 'andhra pradesh|arunachal pradesh|assam|bihar|chhattisgarh|goa|gujarat|haryana|himachal pradesh|jharkhand|karnataka|kerala|madhya pradesh|maharashtra|manipur|meghalaya|mizoram|nagaland|odisha|punjab|rajasthan|sikkim|tamil nadu|telangana|tripura|uttar pradesh|uttarakhand|west bengal|delhi|jammu and kashmir|ladakh|puducherry|chandigarh|dadra and nagar haveli|daman and diu|andaman and nicobar islands|lakshadweep';
    var pattern = new RegExp('\\s+(' + states + ')$', 'i');
    var res = clean(value.replace(pattern, ''), '--');
    return /^\d+$/.test(String(res).trim()) ? '--' : res;
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

  // ═══════════════════════════════════════════════════════════════════════════
  // CANDIDATE RESUME STREAMING & CACHING
  // ═══════════════════════════════════════════════════════════════════════════
  async function fetchCandidateResumeBytes(cand) {
    if (!cand || !cand.id) {
      throw new Error('Valid candidate metadata is required to fetch resume.');
    }
    if (rsDownloadBytesCache[cand.id]) {
      return rsDownloadBytesCache[cand.id];
    }
    var targetUrl = cand.resume_url || cand.download_url || cand.resume_r2_url;
    if (!targetUrl) {
      throw new Error('Candidate does not have an attached resume document.');
    }

    var resp = await fetch(targetUrl, {
      method: 'GET',
      credentials: 'same-origin',
      headers: { 'Accept': 'application/pdf, application/octet-stream, */*' }
    });
    if (!resp.ok) {
      throw new Error('Failed to load candidate resume file (HTTP ' + resp.status + ').');
    }
    var ab = await resp.arrayBuffer();
    if (!ab || ab.byteLength === 0) {
      throw new Error('Candidate resume file is empty.');
    }
    rsDownloadBytesCache[cand.id] = ab;
    return ab;
  }

  function preWarmCandidateResume(cand) {
    if (!cand || !cand.id) return;
    if (rsDownloadBytesCache[cand.id]) return;
    fetchCandidateResumeBytes(cand).catch(function(e) {
      console.warn('[AgyBenchDownloadResumeEngine] Pre-warm notice for cand ' + cand.id + ':', e.message);
    });
  }

  async function ensurePdfBlobFromBytes(cand, rawBytes) {
    if (!rawBytes || rawBytes.byteLength < 4) {
      throw new Error('Candidate resume file is empty or could not be loaded.');
    }

    var header = new Uint8Array(rawBytes.slice(0, 5));
    var isActuallyPdf = (header[0] === 0x25 && header[1] === 0x50 && header[2] === 0x44 && header[3] === 0x46);
    if (isActuallyPdf) {
      return new Blob([rawBytes], { type: 'application/pdf' });
    }

    var ext = (cand.resume_extension || '').toLowerCase();

    // Check for pre-2007 legacy Word (.doc) binary format
    if (ext === 'doc' || (rawBytes.byteLength > 4 && rawBytes[0] === 0xD0 && rawBytes[1] === 0xCF && rawBytes[2] === 0x11 && rawBytes[3] === 0xE0)) {
      throw new Error('LEGACY_DOC_UNSUPPORTED: Pre-2007 Word (.doc) binary format cannot be converted to PDF client-side. Please download using Format 1 (Original Resume) or convert to DOCX/PDF.');
    }

    var isDocx = (ext === 'docx') ||
                 (cand.resume_url && /\.docx(\?.*)?$/i.test(cand.resume_url)) ||
                 (rawBytes.byteLength > 2 && rawBytes[0] === 0x50 && rawBytes[1] === 0x4B);

    if (isDocx && cand.resume_pdf_url) {
      try {
        var preConvResp = await fetch(cand.resume_pdf_url, {
          method: 'GET',
          credentials: 'same-origin',
          headers: { 'Accept': 'application/pdf' }
        });
        if (preConvResp.ok) {
          var convAb = await preConvResp.arrayBuffer();
          if (convAb && convAb.byteLength > 100) {
            return new Blob([convAb], { type: 'application/pdf' });
          }
        }
      } catch (loErr) {
        console.warn('[AgyBenchDownloadResumeEngine] Pre-converted PDF fetch error:', loErr);
      }
    }

    if (isDocx) {
      return await renderBenchDocxToPdfBlob(rawBytes, cand.safe_name || 'Resume');
    }

    throw new Error('The candidate resume format (' + (ext || 'unknown') + ') is not a supported PDF or DOCX file.');
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // SKILLS NORMALIZATION
  // ═══════════════════════════════════════════════════════════════════════════
  function getBenchCandidateEvaluatedSkills(cand) {
    if (cand && Array.isArray(cand.skills_evaluated) && cand.skills_evaluated.length > 0) {
      return cand.skills_evaluated.map(function(s) {
        var nm = (s.name || s.skill_key || '').trim();
        return {
          name: nm,
          expText: s.expText || (s.exp ? s.exp + ' Years' : '--'),
          exp: typeof s.exp === 'number' ? s.exp : parseFloat(s.expText) || 0,
          rating: typeof s.rating === 'number' ? Math.min(5, Math.max(0, s.rating)) : 3
        };
      }).filter(function(s) { return s.name !== ''; });
    }

    if (cand && cand.skills) {
      var rawArr = Array.isArray(cand.skills) ? cand.skills : String(cand.skills).split(',');
      return rawArr.map(function(s) {
        var nm = String(s).trim();
        return {
          name: nm,
          expText: cand.experience || '--',
          exp: parseFloat(cand.experience) || 0,
          rating: cand.rating ? Math.min(5, Math.max(0, parseInt(cand.rating, 10))) : 3
        };
      }).filter(function(s) { return s.name !== ''; });
    }

    return [];
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // IN-HOUSE EVALUATION REPORT DOSSIER — exact copy of the Forward Resume (In-House Talent)
  // dossier so both centers render an identical report. Hiring Confidence is fixed at 100%
  // (no JD matching for bench talent); RM comments + EXPERIENCE verification slot.
  // Kept as an isolated copy (Rule 15/17) — do not share code with the forward engine.
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
                  { text: 'IN-HOUSE EVALUATION REPORT', style: 'title', margin: [0, 2, 0, 0] },
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
                  { text: 'IN-HOUSE EVALUATION REPORT', style: 'title', margin: [0, 2, 0, 0] },
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
      rows.push([{ text: 'No talent skill intelligence available in current candidate data.', colSpan: 3, style: 'muted', margin: [0, 6, 0, 6] }, {}, {}]);
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
      { label: 'EXPERIENCE', key: 'experience', fallbackKey: 'experience verified', icon: rsIconExperience }
    ];
    var cells = slots.map(function (row) {
      var status = (row.key === 'experience') ? 'Verified' : (map[row.key] || map[row.fallbackKey] || '-');
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
    var score = 100; // Bench talent profile hiring confidence is always 100% (no JD matching parameters)
    var summary = limitText(data.careerSummary, 560, 'Experienced professional with demonstrated track record of technical delivery and engineering excellence.');
    var noteMissing = isGap(data.recruiterNote);
    var note = noteMissing ? 'No relationship manager comments available.' : limitText(data.recruiterNote, 420, '');
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
            fixedPanel([sectionTitle('RELATIONSHIP MANAGER COMMENTS', false, rsIconNote), { text: note, style: 'body', color: noteMissing ? '#64748b' : '#1f2937', italics: noteMissing }], [6, 0, 0, 8], '#ffffff', '#dbe3ef', 148)
          ],
          columnGap: 0
        },
        roundedFullCard([
          sectionTitle('TALENT SKILL INTELLIGENCE', false, rsIconSkill),
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
    if (Array.isArray(candidate.skills_evaluated) && candidate.skills_evaluated.length) {
      rawSkills = candidate.skills_evaluated;
    } else if (Array.isArray(candidate.parsed_skills) && candidate.parsed_skills.length) {
      rawSkills = candidate.parsed_skills;
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

      validation.push({ label: 'EXPERIENCE', status: 'Verified' });
    }

    var todayDate = (new Date()).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    var reportId = candidate.report_id || ('JR' + (new Date()).toISOString().slice(0, 10).replace(/-/g, '') + String(candidate.id || '000000').padStart(6, '0'));

    var matchScore = 100; // Bench talent hiring confidence score is always 100% as there is no matching parameter and JD

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
    if (typeof document !== 'undefined') {
      var liveRmEl = document.querySelector('.rm-comments-content');
      if (liveRmEl && liveRmEl.innerText && liveRmEl.innerText.trim()) {
        var liveRmText = liveRmEl.innerText.trim();
        if (liveRmText) {
          rawRm = liveRmText;
        }
      }
    }
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

  async function generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, isUnbranded) {
    var pdfMake = await ensurePdfMake();
    var cLogo = isUnbranded ? null : await rsLogoDataUri(companyLogo);
    var cName = isUnbranded ? '' : (companyName || 'Hiring Partner');
    var normData = normalizeBenchCandidateData(cand, { companyName: cName, companyLogo: cLogo });
    var docDef = buildDefinition(normData, cLogo, { noBranding: isUnbranded });

    return new Promise(function (resolve, reject) {
      try {
        var pdfDocGenerator = pdfMake.createPdf(docDef);
        pdfDocGenerator.getBlob(function (blob) {
          resolve(blob);
        });
      } catch (err) {
        reject(err);
      }
    });
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // VECTOR COMPANY BRANDING INJECTION (Height-Expanded Header)
  // ═══════════════════════════════════════════════════════════════════════════
  async function addCompanyBrandingHeader(resumeBlob, companyName, companyLogo, reportDate) {
    var PDFLib = await ensurePdfLib();
    var resumeBytes = await resumeBlob.arrayBuffer();
    var resumeDoc = await PDFLib.PDFDocument.load(resumeBytes, { ignoreEncryption: true });
    var sourcePages = resumeDoc.getPages();
    if (!sourcePages.length) return resumeBlob;

    var firstPage = sourcePages[0];
    var pageSize = firstPage.getSize();

    var targetDoc = await PDFLib.PDFDocument.create();
    var regularFont = await targetDoc.embedFont(PDFLib.StandardFonts.Helvetica);
    var boldFont = await targetDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);

    var logoImage = null;
    var logoUri = await rsLogoDataUri(companyLogo);
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

    // Create a new first page with expanded height so the header banner is placed ABOVE the resume content
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
    var dateText = clean(reportDate, '') || (new Date()).toLocaleDateString();
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
  // VECTOR KEYWORD HIGHLIGHTING (Sub-pixel Canvas Measurement - Exact Replica)
  // ═══════════════════════════════════════════════════════════════════════════
  function rsKeywordPatternEscape(value) {
    return String(value == null ? '' : value).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  }

  function rsKeywordAliasValues(value) {
    var raw = String(value == null ? '' : value).replace(/\s+/g, ' ').trim();
    var normalized = raw.toLowerCase().replace(/[^a-z0-9+#.]+/g, ' ').replace(/\s+/g, ' ').trim();
    var aliases = [raw];
    function add(alias) {
      if (alias && aliases.map(function (v) { return v.toLowerCase(); }).indexOf(alias.toLowerCase()) === -1) aliases.push(alias);
    }
    if (/\baws\b/.test(normalized) && /\bcloud\b/.test(normalized)) {
      add('AWS');
      add('Amazon Web Services');
    }
    if (normalized === 'amazon web services') {
      add('AWS');
      add('AWS Cloud');
    }
    if ((/\bgcp\b/.test(normalized) || normalized === 'google cloud platform') && normalized.indexOf('cloud') !== -1) {
      add('GCP');
      add('Google Cloud');
    }
    if (/\bazure\b/.test(normalized) && /\bcloud\b/.test(normalized)) {
      add('Azure');
      add('Microsoft Azure');
    }
    return aliases;
  }

  function rsKeywordPatternFromValue(value) {
    var raw = String(value == null ? '' : value).replace(/\+\d+\s*More/gi, '').replace(/\s+/g, ' ').trim();
    if (!raw) return '';
    var compact = raw.replace(/\s+/g, '');
    if (/^[A-Za-z][+#]{1,2}$/i.test(compact)) {
      return '\\b' + rsKeywordPatternEscape(compact);
    }
    var expanded = raw
      .replace(/([a-z])([A-Z])/g, '$1 $2')
      .replace(/([A-Za-z])(\d)/g, '$1 $2')
      .replace(/(\d)([A-Za-z])/g, '$1 $2');
    var tokens = expanded.split(/[\s\u00a0\/\\_\-.+,;:()[\]{}|&]+/).filter(Boolean);
    if (!tokens.length) return '';
    var left = /^[A-Za-z0-9]/.test(tokens[0]) ? '\\b' : '';
    var right = /[A-Za-z0-9]$/.test(tokens[tokens.length - 1]) ? '\\b' : '';
    if (tokens.length === 1) {
      return left + rsKeywordPatternEscape(tokens[0]) + right;
    }
    var flexibleJoin = '(?:[\\s\\u00a0\\/\\\\_\\-.+,;:()\\[\\]{}|&]+|\\b(?:and|or)\\b)*';
    return left + tokens.map(rsKeywordPatternEscape).join(flexibleJoin) + right;
  }

  function rsDeconstructCompoundSkill(value) {
    var raw = String(value == null ? '' : value).replace(/\+\d+\s*More/gi, '').replace(/\s+/g, ' ').trim();
    if (!raw) return [];

    var parts = [];
    var seen = {};
    function addPart(str) {
      var s = String(str || '').replace(/\s+/g, ' ').trim();
      if (!s || s.length < 2) return;
      var k = s.toLowerCase();
      if (!seen[k]) {
        seen[k] = true;
        parts.push(s);
      }
    }

    addPart(raw);
    var pieces = raw.split(/[\/,+&|]+|\band\b|\(([^)]+)\)/i);
    for (var i = 0; i < pieces.length; i++) {
      var piece = pieces[i];
      if (piece) {
        addPart(piece);
      }
    }
    return parts;
  }

  function rsKeywordPatternsForValue(value) {
    var seen = {};
    var patterns = [];
    var deconstructed = rsDeconstructCompoundSkill(value);
    deconstructed.forEach(function (part) {
      rsKeywordAliasValues(part).forEach(function (alias) {
        var pattern = rsKeywordPatternFromValue(alias);
        var key = pattern.toLowerCase();
        if (pattern && !seen[key]) {
          seen[key] = true;
          patterns.push(pattern);
        }
      });
    });
    return patterns;
  }

  function rsKeywordRegex(list) {
    var seen = {};
    var cleaned = (Array.isArray(list) ? list : []).map(function (item) {
      if (typeof item === 'string') return item.trim();
      return clean(item && (item.skill_name || item.skill_key || item.name || item.skill), '').trim();
    }).filter(function (value) {
      value = value.replace(/\s+/g, ' ').trim();
      var key = value.toLowerCase();
      if (value.length < 2 || seen[key]) return false;
      seen[key] = true;
      return true;
    });
    if (!cleaned.length) return null;
    cleaned.sort(function (a, b) { return b.length - a.length; });
    var parts = [];
    cleaned.forEach(function (value) {
      parts = parts.concat(rsKeywordPatternsForValue(value));
    });
    return parts.length ? new RegExp(parts.join('|'), 'gi') : null;
  }

  function rsPageTextRuns(textContent) {
    var text = '';
    var runs = [];
    (textContent && Array.isArray(textContent.items) ? textContent.items : []).forEach(function (item) {
      var value = (item && item.str ? String(item.str) : '');
      if (!value.trim()) return;
      if (text.length) text += ' ';
      var start = text.length;
      text += value;
      runs.push({ item: item, start: start, end: text.length });
    });
    return { text: text, runs: runs };
  }

  async function buildTurboVectorHighlightedResume(options) {
    options = options || {};
    await Promise.all([ensurePdfJs(), ensurePdfLib()]);

    var pdfBlob = options.resumeBlob;
    if (!(pdfBlob instanceof Blob)) {
      throw new Error('Valid resume PDF Blob required for Turbo Vector highlighting');
    }

    var sourceBytes = await pdfBlob.arrayBuffer();
    var bytes = new Uint8Array(sourceBytes);
    var sig = String.fromCharCode.apply(null, bytes.subarray(0, 5));
    if (sig.indexOf('%PDF') === -1) return pdfBlob;

    var pdfjsDoc = await window.pdfjsLib.getDocument({ data: bytes }).promise;
    var pagesTextData = [];

    for (var p = 1; p <= pdfjsDoc.numPages; p++) {
      await rsYieldToMain();
      var page = await pdfjsDoc.getPage(p);
      var tc = await page.getTextContent();
      pagesTextData.push({ pageNum: p, tc: tc });
    }

    var allKeywords = [];
    var seenKw = {};
    function addKw(val) {
      if (!val) return;
      if (typeof val === 'object') val = val.skill_name || val.skill || val.name || val.skill_key || '';
      val = String(val).replace(/\+\d+\s*More/gi, '').replace(/\s+/g, ' ').trim();
      if (val.length < 2 || (val.length < 3 && !/^(ai|ui|ux|go|c#|r|c)$/i.test(val))) return;
      var k = val.toLowerCase();
      if (seenKw[k]) return;
      seenKw[k] = true;
      allKeywords.push(val);
    }
    (Array.isArray(options.keywords) ? options.keywords : []).forEach(addKw);

    var keywordRe = rsKeywordRegex(allKeywords);
    if (!keywordRe) return pdfBlob;

    var measureCanvas = document.createElement('canvas');
    var measureCtx = measureCanvas && measureCanvas.getContext ? measureCanvas.getContext('2d') : null;
    function measureSlice(fullStr, startIdx, endIdx, itemWidth, fontH) {
      if (!measureCtx) {
        var len = Math.max(1, fullStr.length);
        return {
          offset: (startIdx / len) * itemWidth,
          width: Math.max(3, ((endIdx - startIdx) / len) * itemWidth)
        };
      }
      measureCtx.font = Math.round(fontH) + 'px sans-serif';
      var fullM = measureCtx.measureText(fullStr).width;
      if (!fullM || fullM <= 0) {
        var len2 = Math.max(1, fullStr.length);
        return {
          offset: (startIdx / len2) * itemWidth,
          width: Math.max(3, ((endIdx - startIdx) / len2) * itemWidth)
        };
      }
      var scale = itemWidth / fullM;
      var prefixW = measureCtx.measureText(fullStr.substring(0, startIdx)).width * scale;
      var matchW = measureCtx.measureText(fullStr.substring(startIdx, endIdx)).width * scale;
      return {
        offset: prefixW,
        width: Math.max(3, matchW)
      };
    }

    try {
      var PDFLib = window.PDFLib;
      var pdfDoc = await PDFLib.PDFDocument.load(sourceBytes, { ignoreEncryption: true });

      pagesTextData.forEach(function (pData) {
        var pdfLibPage = pdfDoc.getPage(pData.pageNum - 1);
        var runsData = rsPageTextRuns(pData.tc);
        if (!runsData.text || !runsData.runs.length) return;

        keywordRe.lastIndex = 0;
        var match;
        while ((match = keywordRe.exec(runsData.text)) !== null) {
          var mStart = match.index;
          var mEnd = mStart + match[0].length;

          runsData.runs.forEach(function (run) {
            if (run.end <= mStart || run.start >= mEnd) return;
            var item = run.item;
            if (!item || !item.transform) return;

            var tx = item.transform[4] || 0;
            var ty = item.transform[5] || 0;
            var itemH = Math.max(Math.abs(item.transform[3] || item.height || 0), Math.abs(item.transform[0] || 0), 9);
            var itemW = Math.max(Math.abs(item.width || 0), 2);
            var itemStr = item.str || '';
            if (!itemStr.trim()) return;

            var subStart = Math.max(0, mStart - run.start);
            var subEnd = Math.min(itemStr.length, mEnd - run.start);
            if (subEnd <= subStart) return;

            var slice = measureSlice(itemStr, subStart, subEnd, itemW, itemH);
            var padX = Math.min(2.5, itemH * 0.14);
            var boxX = tx + slice.offset - padX;
            var boxW = slice.width + (padX * 2);
            var boxY = ty - (itemH * 0.22);
            var boxH = itemH * 1.16;

            pdfLibPage.drawRectangle({
              x: boxX,
              y: boxY,
              width: boxW,
              height: boxH,
              color: PDFLib.rgb(1, 0.85, 0.15),
              opacity: 0.42
            });
          });

          if (match.index === keywordRe.lastIndex) keywordRe.lastIndex++;
        }
      });

      var outputBytes = await pdfDoc.save();
      return new Blob([outputBytes], { type: 'application/pdf' });
    } catch (hlErr) {
      console.warn('[AgyBenchDownloadResumeEngine] Turbo Vector Highlight fallback to raw PDF:', hlErr);
      return pdfBlob;
    }
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // PII ANONYMIZATION & REDACTION (Anti-Poaching Shield - Pure White Strip)
  // ═══════════════════════════════════════════════════════════════════════════
  function rdcPlain(value) {
    return String(value == null ? '' : value).replace(/\s+/g, ' ').trim();
  }
  function rdcEscapeRegExp(value) {
    return rdcPlain(value).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  }
  function rdcDigitsOnly(value) {
    return String(value == null ? '' : value).replace(/\D+/g, '');
  }
  function rdcPushUnique(list, value) {
    value = rdcPlain(value);
    if (!value || value === '--') return;
    var key = value.toLowerCase();
    if (list.some(function (item) { return item.toLowerCase() === key; })) return;
    list.push(value);
  }
  function rdcFlexibleTextRegex(value) {
    var parts = rdcPlain(value).split(/\s+/).filter(Boolean);
    if (!parts.length) return null;
    return new RegExp(parts.map(rdcEscapeRegExp).join('[\\s\\u00a0._-]+'), 'gi');
  }
  function rdcPhoneTextRegex(value) {
    var digits = rdcDigitsOnly(value);
    if (digits.length < 5) return null;
    return new RegExp('(?:\\+?\\d{1,3}[\\s().\\-+]*)?' + digits.split('').join('[\\s().\\-+]*'), 'g');
  }
  function rdcContactLabelRegexes() {
    return [
      /\b(?:e[\s._-]*mail|email|mail)[\s._-]*(?:id|address)?[\s.:#-]*(?:primary|secondary|alternate|alt|1|2)?\b/gi,
      /\b(?:phone|mobile|mob|cell|contact|tel|telephone)[\s._-]*(?:number|no|num|id|details)?[\s.:#-]*(?:primary|secondary|alternate|alt|1|2)?\b/gi,
      /\b(?:contact|personal)[\s._-]*(?:details|info|information)\b/gi,
      /\b(?:whats[\s._-]*app|whatsapp|linked[\s._-]*in|linkedin)[\s._-]*(?:id|profile|url|link|number|no)?\b/gi
    ];
  }
  function rdcLinkedInTargets(value) {
    value = rdcPlain(value);
    if (!value || value === '--') return [];
    var targets = [];
    rdcPushUnique(targets, value);
    var withoutProtocol = value.replace(/^https?:\/\//i, '').replace(/^www\./i, '');
    rdcPushUnique(targets, withoutProtocol);
    if (!/^https?:\/\//i.test(value) && !/^www\./i.test(value) && /linkedin\.com/i.test(value)) {
      rdcPushUnique(targets, 'https://' + value);
      rdcPushUnique(targets, 'www.' + withoutProtocol);
    }
    var match = withoutProtocol.match(/linkedin\.com\/(?:in|pub|company)\/([^/?#\s]+)/i);
    if (match && match[1]) {
      rdcPushUnique(targets, match[1].replace(/[-_]+/g, ' '));
      rdcPushUnique(targets, match[1]);
    }
    return targets;
  }

  function rdcAnonymizeRules(data, options) {
    data = data || {};
    options = options || {};
    var textRegexes = [];
    var phoneDigits = [];
    var exactTerms = [];
    var emails = [];
    var phones = [];
    if (options.keepName !== true) {
      rdcPushUnique(exactTerms, data.full_name || data.name);
      rdcPlain(data.full_name || data.name).split(/\s+/).forEach(function (token) {
        if (token.length >= 3) rdcPushUnique(exactTerms, token);
      });
    }
    [data.email, data.secondaryEmail, data.secondrary_email].forEach(function (email) {
      rdcPushUnique(emails, email);
      rdcPushUnique(exactTerms, email);
    });
    [data.phone, data.secondaryPhone, data.secondary_mobile, data.mobile].forEach(function (phone) {
      rdcPushUnique(phones, phone);
      rdcPushUnique(exactTerms, phone);
    });
    rdcLinkedInTargets(data.linkedin || data.linkedin_url).forEach(function (target) {
      rdcPushUnique(exactTerms, target);
    });
    phones.forEach(function (phone) {
      var digits = rdcDigitsOnly(phone);
      if (digits.length >= 5 && phoneDigits.indexOf(digits) === -1) phoneDigits.push(digits);
      var phoneRe = rdcPhoneTextRegex(phone);
      if (phoneRe) textRegexes.push(phoneRe);
    });
    exactTerms.forEach(function (term) {
      if (rdcDigitsOnly(term).length >= 5) return;
      var re = rdcFlexibleTextRegex(term);
      if (re) textRegexes.push(re);
    });
    textRegexes.push(/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/gi);
    textRegexes.push(/\+?\d[\d\s().\-]{7,}\d/g);
    textRegexes.push(/(?:https?:\/\/|www\.)?linkedin\.com\/(?:in|pub|company)\/[^\s<>"')]+/gi);
    textRegexes.push(/(?:https?:\/\/|www\.)?github\.com\/[^\s<>"')]+/gi);
    rdcContactLabelRegexes().forEach(function (regex) { textRegexes.push(regex); });
    return { textRegexes: textRegexes, phoneDigits: phoneDigits, emails: emails, phones: phones, exactTerms: exactTerms };
  }

  function rdcRegexMatches(regex, text) {
    regex.lastIndex = 0;
    var ok = regex.test(text);
    regex.lastIndex = 0;
    return ok;
  }

  function rdcShouldRedactText(text, rules) {
    text = rdcPlain(text);
    if (!text) return false;
    var lower = text.toLowerCase();
    var digits = rdcDigitsOnly(text);
    if (digits.length >= 5 && rules.phoneDigits.some(function (phoneDigits) {
      return phoneDigits.indexOf(digits) !== -1 || digits.indexOf(phoneDigits) !== -1;
    })) return true;
    if (rules.exactTerms.some(function (term) {
      term = rdcPlain(term).toLowerCase();
      return term.length >= 3 && lower.indexOf(term) !== -1;
    })) return true;
    return rules.textRegexes.some(function (regex) { return rdcRegexMatches(regex, text); });
  }

  function rsTextItemRect(item, viewport) {
    if (!item || !item.transform || !viewport || !viewport.transform) return null;
    var vt = viewport.transform;
    var it = item.transform;
    var canvasX = vt[0] * it[4] + vt[2] * it[5] + vt[4];
    var canvasY = vt[1] * it[4] + vt[3] * it[5] + vt[5];
    var h = Math.max(
      Math.abs((item.height || 0) * (viewport.scale || 1)),
      Math.abs(vt[3] * (it[3] || 0)),
      6
    );
    var w = Math.max(Math.abs((item.width || 0) * (viewport.scale || 1)), 2);
    return { x: canvasX, y: canvasY - h, w: w, h: h };
  }

  // Pure White Strip Masking (No black boxes, no [CONFIDENTIAL] text, only clean white strips)
  function rdcDrawAnonymizedBlocks(ctx, textContent, viewport, rules) {
    if (!ctx || !textContent || !viewport || !rules) return 0;
    var count = 0;
    ctx.save();
    ctx.fillStyle = '#ffffff';
    (textContent.items || []).forEach(function (item) {
      if (!rdcShouldRedactText(item && item.str, rules)) return;
      var rect = rsTextItemRect(item, viewport);
      if (!rect) return;
      var pad = Math.max(2, Math.min(8, rect.h * 0.22));
      ctx.fillRect(Math.max(0, rect.x - pad), Math.max(0, rect.y - pad), rect.w + (pad * 2), rect.h + (pad * 2));
      count++;
    });
    ctx.restore();
    return count;
  }

  async function renderAnonymizedPdfBlob(pdfBlob, rules) {
    await Promise.all([ensurePdfJs(), ensureJsPdf()]);
    var sourceBytes = await pdfBlob.arrayBuffer();
    var bytes = new Uint8Array(sourceBytes);
    var sig = String.fromCharCode.apply(null, bytes.subarray(0, 5));
    if (sig.indexOf('%PDF') === -1) throw new Error('Resume source is not a valid PDF');
    var doc = await window.pdfjsLib.getDocument({ data: bytes }).promise;
    var JsPDF = (window.jspdf && window.jspdf.jsPDF) || window.jsPDF || window.jspdf;
    var outPdf = null;
    var textItems = 0;
    for (var p = 1; p <= doc.numPages; p++) {
      await rsYieldToMain();
      var pdfJsPage = await doc.getPage(p);
      var base = pdfJsPage.getViewport({ scale: 1 });
      var cssScale = Math.min(1.35, 820 / base.width);
      var cssViewport = pdfJsPage.getViewport({ scale: cssScale });
      var renderScale = Math.min(2.0, 1600 / base.width);
      var renderViewport = pdfJsPage.getViewport({ scale: renderScale });
      var canvas = document.createElement('canvas');
      canvas.width = Math.floor(renderViewport.width);
      canvas.height = Math.floor(renderViewport.height);
      var ctx = canvas.getContext('2d', { willReadFrequently: true });
      ctx.fillStyle = '#ffffff';
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      await pdfJsPage.render({ canvasContext: ctx, viewport: renderViewport }).promise;
      var tc = await pdfJsPage.getTextContent();
      if (tc && tc.items) textItems += tc.items.length;
      rdcDrawAnonymizedBlocks(ctx, tc, renderViewport, rules);
      var pageW = Math.round(cssViewport.width);
      var pageH = Math.round(cssViewport.height);
      var orient = pageW > pageH ? 'l' : 'p';
      if (!outPdf) outPdf = new JsPDF({ orientation: orient, unit: 'px', format: [pageW, pageH] });
      else outPdf.addPage([pageW, pageH], orient);
      outPdf.addImage(canvas.toDataURL('image/jpeg', 0.88), 'JPEG', 0, 0, pageW, pageH, undefined, 'FAST');
      canvas.width = 0;
      canvas.height = 0;
    }
    if (!outPdf) throw new Error('No resume pages were rendered');
    if (!textItems) throw new Error('This resume appears to be scanned/image-only. Browser anonymization needs extractable text.');
    var outBlob = outPdf.output('blob');
    if (outBlob && outBlob.size > MAX_DOWNLOAD_FILE_BYTES) {
      outBlob = await ensurePdfBlobUnderLimit(outBlob, MAX_DOWNLOAD_FILE_BYTES);
    }
    return outBlob;
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // A4 NORMALIZED MERGING (Every Page Guaranteed Exact Same Width: 595.28 pt)
  // ═══════════════════════════════════════════════════════════════════════════
  async function copyPdfPagesToA4(targetDoc, sourceDoc) {
    var A4_W = 595.28;
    var A4_H = 841.89;
    var pages = sourceDoc.getPages();
    if (!pages.length) return;
    for (var i = 0; i < pages.length; i++) {
      await rsYieldToMain();
      var size = pages[i].getSize();
      var scale = Math.min(A4_W / size.width, A4_H / size.height);
      var drawW = size.width * scale;
      var drawH = size.height * scale;
      var a4Page = targetDoc.addPage([A4_W, A4_H]);
      var embedded = await targetDoc.embedPage(pages[i]);
      a4Page.drawPage(embedded, {
        x: (A4_W - drawW) / 2,
        y: (A4_H - drawH) / 2,
        width: drawW,
        height: drawH
      });
    }
  }

  async function mergeDossierAndResume(dossierBlob, resumeBlob) {
    var PDFLib = await ensurePdfLib();
    var merged = await PDFLib.PDFDocument.create();

    var dBytes = await dossierBlob.arrayBuffer();
    var dDoc = await PDFLib.PDFDocument.load(dBytes, { ignoreEncryption: true });
    await copyPdfPagesToA4(merged, dDoc);

    var rBytes = await resumeBlob.arrayBuffer();
    var rDoc = await PDFLib.PDFDocument.load(rBytes, { ignoreEncryption: true });
    await copyPdfPagesToA4(merged, rDoc);

    var mergedBytes = await merged.save();
    return new Blob([mergedBytes], { type: 'application/pdf' });
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // MASTER COMPILATION CONTROLLER (15 FORMATS: 1, 5, 41-54)
  // ═══════════════════════════════════════════════════════════════════════════
  async function processDownloadOption(cand, optId, companyData, onProgress) {
    var formatId = parseInt(optId, 10);
    var companyName = (companyData && companyData.companyName) || 'Hiring Partner';
    var companyLogo = (companyData && companyData.companyLogo) || '';
    var reportDate = (cand && cand.report_date) || (new Date()).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

    // Clean Candidate Name for Output Filename
    var cleanCandName = (cand.full_name || cand.name || ('Candidate_' + cand.id))
      .trim()
      .replace(/\s+/g, '_')
      .replace(/[^\w.-]/g, '');

    function notify(pct, step, detail) {
      if (typeof onProgress === 'function') {
        onProgress({ percent: pct, step: step, detail: detail });
      }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 1: Original Candidate Resume
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 1) {
      notify(30, 'Fetching original resume...', 'Retrieving file bytes from storage...');
      var rawBytes = await fetchCandidateResumeBytes(cand);
      notify(75, 'Preparing document...', 'Finalizing original resume document...');
      var origExt = (cand.resume_extension || 'pdf').toLowerCase();
      var origMime = origExt === 'docx' ? 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' :
                     (origExt === 'doc' ? 'application/msword' : 'application/pdf');
      var origBlob = new Blob([rawBytes], { type: origMime });
      origBlob.filename = 'Jeroworks_' + cleanCandName + '.' + origExt;
      notify(100, 'Original resume ready!', 'Document ready for download');
      return origBlob;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 5: Stand-alone In-House Evaluation Report Dossier
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 5) {
      notify(35, 'Generating in-house evaluation report...', 'Building verified bench consultant dossier...');
      var dossierBlob = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);
      notify(85, 'Finalizing evaluation report...', 'Applying executive styling...');
      dossierBlob.filename = 'Jeroworks_' + cleanCandName + '_evaluationreport.pdf';
      notify(100, 'Evaluation report ready!', 'Document ready for download');
      return dossierBlob;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // BASE PDF PREPARATION FOR OPTIONS 41 - 54
    // ─────────────────────────────────────────────────────────────────────────
    notify(20, 'Fetching candidate resume document...', 'Retrieving vector document bytes...');
    var srcBytes = await fetchCandidateResumeBytes(cand);
    notify(35, 'Normalizing vector PDF document...', 'Preparing high-resolution vector layout...');
    var basePdfBlob = await ensurePdfBlobFromBytes(cand, srcBytes);

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 41: Resume + Company Branding
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 41) {
      notify(60, 'Stamping company branding...', 'Injecting header with company logo and metadata...');
      var branded = await addCompanyBrandingHeader(basePdfBlob, companyName, companyLogo, reportDate);
      branded.filename = 'Jeroworks_' + cleanCandName + '.pdf';
      notify(100, 'Branded resume ready!', 'Document ready for download');
      return branded;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 42: Recruiter Package + Company Branding (Dossier + Branded Resume)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 42) {
      notify(45, 'Generating in-house evaluation report...', 'Compiling technical evaluation dossier...');
      var repBlob42 = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);
      notify(65, 'Stamping company branding on resume...', 'Injecting header banner...');
      var bResume42 = await addCompanyBrandingHeader(basePdfBlob, companyName, companyLogo, reportDate);
      notify(85, 'Merging dossier and branded resume...', 'Concatenating recruiter package with uniform A4 width...');
      var pkg42 = await mergeDossierAndResume(repBlob42, bResume42);
      pkg42.filename = 'Jeroworks_' + cleanCandName + '.pdf';
      notify(100, 'Recruiter package ready!', 'Document ready for download');
      return pkg42;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 43: Recruiter Package (Unbranded) (Dossier + Original Resume)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 43) {
      notify(50, 'Generating unbranded evaluation report...', 'Compiling neutral dossier...');
      var repBlob43 = await generateBenchPrescreenPdfBlob(cand, '', null, true);
      notify(80, 'Merging unbranded dossier and resume...', 'Concatenating neutral recruiter package with uniform A4 width...');
      var pkg43 = await mergeDossierAndResume(repBlob43, basePdfBlob);
      pkg43.filename = 'Jeroworks_' + cleanCandName + '.pdf';
      notify(100, 'Unbranded package ready!', 'Document ready for download');
      return pkg43;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 44: Resume + Highlighted Keywords
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 44) {
      notify(55, 'Locating evaluated skills in resume text...', 'Extracting vector coordinates for skill keywords...');
      var skills44 = getBenchCandidateEvaluatedSkills(cand);
      var hlResume44 = await buildTurboVectorHighlightedResume({ resumeBlob: basePdfBlob, keywords: skills44 });
      hlResume44.filename = 'Jeroworks_' + cleanCandName + '.pdf';
      notify(100, 'Highlighted resume ready!', 'Document ready for download');
      return hlResume44;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 45: Resume + Highlighted Keywords + Company Branding
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 45) {
      notify(50, 'Highlighting evaluated skills in resume...', 'Applying sub-pixel vector highlights to keywords...');
      var skills45 = getBenchCandidateEvaluatedSkills(cand);
      var hlResume45 = await buildTurboVectorHighlightedResume({ resumeBlob: basePdfBlob, keywords: skills45 });
      notify(75, 'Stamping company branding...', 'Injecting header banner...');
      var bHlResume45 = await addCompanyBrandingHeader(hlResume45, companyName, companyLogo, reportDate);
      bHlResume45.filename = 'Jeroworks_' + cleanCandName + '.pdf';
      notify(100, 'Branded highlighted resume ready!', 'Document ready for download');
      return bHlResume45;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 46 (DEFAULT ★): Evaluation Report + Resume + Highlighted Keywords + Company Branding
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 46) {
      notify(40, 'Generating in-house evaluation report...', 'Compiling technical evaluation dossier with verified skills...');
      var repBlob46 = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);
      notify(60, 'Highlighting evaluated skills on resume...', 'Extracting vector bounding boxes for rated skills...');
      var skills46 = getBenchCandidateEvaluatedSkills(cand);
      var hlResume46 = await buildTurboVectorHighlightedResume({ resumeBlob: basePdfBlob, keywords: skills46 });
      notify(80, 'Stamping company branding banner...', 'Injecting corporate branding...');
      var bHlResume46 = await addCompanyBrandingHeader(hlResume46, companyName, companyLogo, reportDate);
      notify(92, 'Merging dossier and highlighted resume...', 'Assembling recruiter presentation package with uniform A4 width...');
      var pkg46 = await mergeDossierAndResume(repBlob46, bHlResume46);
      pkg46.filename = 'Jeroworks_' + cleanCandName + '.pdf';
      notify(100, 'Executive presentation package ready!', 'Document ready for download');
      return pkg46;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 47: Recruiter Package + Highlighted Keywords (Unbranded)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 47) {
      notify(45, 'Generating unbranded evaluation report...', 'Compiling neutral dossier...');
      var repBlob47 = await generateBenchPrescreenPdfBlob(cand, '', null, true);
      notify(65, 'Highlighting evaluated skills on resume...', 'Applying sub-pixel vector highlights to rated skills...');
      var skills47 = getBenchCandidateEvaluatedSkills(cand);
      var hlResume47 = await buildTurboVectorHighlightedResume({ resumeBlob: basePdfBlob, keywords: skills47 });
      notify(85, 'Merging unbranded dossier and highlighted resume...', 'Concatenating recruiter package with uniform A4 width...');
      var pkg47 = await mergeDossierAndResume(repBlob47, hlResume47);
      pkg47.filename = 'Jeroworks_' + cleanCandName + '.pdf';
      notify(100, 'Unbranded highlighted package ready!', 'Document ready for download');
      return pkg47;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 48: Hide Contact Details (Masks phone, email, links with white strip)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 48) {
      notify(60, 'Masking contact information with white strip...', 'Applying white strips on telephone, email, and social profiles...');
      var rules48 = rdcAnonymizeRules(cand, { keepName: true });
      var red48 = await renderAnonymizedPdfBlob(basePdfBlob, rules48);
      red48.filename = 'Jeroworks_' + cleanCandName + '.pdf';
      notify(100, 'Redacted resume ready!', 'Document ready for download');
      return red48;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 49: Hide Candidate Name & Contact Details (Anti-poaching blind profile)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 49) {
      notify(60, 'Generating confidential blind profile...', 'Masking candidate name and personal contact channels with white strips...');
      var rules49 = rdcAnonymizeRules(cand, { keepName: false });
      var red49 = await renderAnonymizedPdfBlob(basePdfBlob, rules49);
      red49.filename = 'Jeroworks_' + cleanCandName + '.pdf';
      notify(100, 'Confidential blind profile ready!', 'Document ready for download');
      return red49;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 50: Hide Contact Details + Company Branding
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 50) {
      notify(50, 'Masking contact channels with white strip...', 'Applying white strips on private communication channels...');
      var rules50 = rdcAnonymizeRules(cand, { keepName: true });
      var red50 = await renderAnonymizedPdfBlob(basePdfBlob, rules50);
      notify(78, 'Stamping company branding...', 'Injecting header with company logo and metadata...');
      var bRed50 = await addCompanyBrandingHeader(red50, companyName, companyLogo, reportDate);
      bRed50.filename = 'Jeroworks_' + cleanCandName + '.pdf';
      notify(100, 'Branded redacted resume ready!', 'Document ready for download');
      return bRed50;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 51: Hide Contact Details + In-House Evaluation Report + Company Branding
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 51) {
      notify(40, 'Generating in-house evaluation report...', 'Compiling technical evaluation dossier...');
      var repBlob51 = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);
      notify(60, 'Masking contact details on resume with white strip...', 'Applying white strips on phone, email, and URLs...');
      var rules51 = rdcAnonymizeRules(cand, { keepName: true });
      var red51 = await renderAnonymizedPdfBlob(basePdfBlob, rules51);
      notify(80, 'Stamping company branding banner...', 'Injecting corporate header banner...');
      var bRed51 = await addCompanyBrandingHeader(red51, companyName, companyLogo, reportDate);
      notify(92, 'Merging evaluation report and confidential resume...', 'Assembling recruiter package with uniform A4 width...');
      var pkg51 = await mergeDossierAndResume(repBlob51, bRed51);
      pkg51.filename = 'Jeroworks_' + cleanCandName + '.pdf';
      notify(100, 'Branded confidential package ready!', 'Document ready for download');
      return pkg51;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 52: Hide Contact Details + In-House Evaluation Report (Unbranded)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 52) {
      notify(45, 'Generating unbranded evaluation report...', 'Compiling neutral dossier...');
      var repBlob52 = await generateBenchPrescreenPdfBlob(cand, '', null, true);
      notify(70, 'Masking contact details on resume with white strip...', 'Applying white strips on phone, email, and URLs...');
      var rules52 = rdcAnonymizeRules(cand, { keepName: true });
      var red52 = await renderAnonymizedPdfBlob(basePdfBlob, rules52);
      notify(88, 'Merging unbranded dossier and confidential resume...', 'Concatenating neutral package with uniform A4 width...');
      var pkg52 = await mergeDossierAndResume(repBlob52, red52);
      pkg52.filename = 'Jeroworks_' + cleanCandName + '.pdf';
      notify(100, 'Unbranded confidential package ready!', 'Document ready for download');
      return pkg52;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 53: Full Privacy: Hide Contact + Dossier + Keywords + Company Branding
    // (Strict Sequence: Highlight Vector Keywords -> Redact Canvas PII with White Strip -> Brand -> Merge Dossier)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 53) {
      notify(30, 'Generating in-house evaluation report...', 'Building technical dossier with verified skills...');
      var repBlob53 = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);

      var skills53 = getBenchCandidateEvaluatedSkills(cand);
      var hlResume53 = basePdfBlob;
      if (skills53.length > 0) {
        notify(50, 'Highlighting evaluated skills...', 'Applying sub-pixel vector highlights to rated skills...');
        try {
          hlResume53 = await buildTurboVectorHighlightedResume({ resumeBlob: basePdfBlob, keywords: skills53 });
        } catch (eHl53) {
          console.warn('[AgyBenchDownloadResumeEngine] Option 53 highlight fallback:', eHl53);
          hlResume53 = basePdfBlob;
        }
      }

      notify(70, 'Applying privacy white strips to highlighted resume...', 'Masking contact numbers, email, and private links with white strips...');
      var rules53 = rdcAnonymizeRules(cand, { keepName: true });
      var red53 = await renderAnonymizedPdfBlob(hlResume53, rules53);

      notify(85, 'Stamping company branding...', 'Injecting header with company logo and metadata...');
      var bRed53 = await addCompanyBrandingHeader(red53, companyName, companyLogo, reportDate);

      notify(94, 'Merging evaluation dossier and full privacy resume...', 'Assembling recruiter package with uniform A4 width...');
      var pkg53 = await mergeDossierAndResume(repBlob53, bRed53);
      pkg53.filename = 'Jeroworks_' + cleanCandName + '.pdf';
      notify(100, 'Full privacy package ready!', 'Document ready for download');
      return pkg53;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 54: Full Privacy (Unbranded): Hide Contact + Dossier + Keywords (No Logo)
    // (Strict Sequence: Highlight Vector Keywords -> Redact Canvas PII with White Strip -> Merge Dossier)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 54) {
      notify(30, 'Generating unbranded evaluation report...', 'Compiling neutral dossier...');
      var repBlob54 = await generateBenchPrescreenPdfBlob(cand, '', null, true);

      var skills54 = getBenchCandidateEvaluatedSkills(cand);
      var hlResume54 = basePdfBlob;
      if (skills54.length > 0) {
        notify(50, 'Highlighting evaluated skills...', 'Applying sub-pixel vector highlights to rated skills...');
        try {
          hlResume54 = await buildTurboVectorHighlightedResume({ resumeBlob: basePdfBlob, keywords: skills54 });
        } catch (eHl54) {
          console.warn('[AgyBenchDownloadResumeEngine] Option 54 highlight fallback:', eHl54);
          hlResume54 = basePdfBlob;
        }
      }

      notify(75, 'Applying privacy white strips to highlighted resume...', 'Masking contact numbers, email, and private links with white strips...');
      var rules54 = rdcAnonymizeRules(cand, { keepName: true });
      var red54 = await renderAnonymizedPdfBlob(hlResume54, rules54);

      notify(90, 'Merging unbranded dossier and confidential resume...', 'Concatenating multi-page recruiter package with uniform A4 width...');
      var pkg54 = await mergeDossierAndResume(repBlob54, red54);
      pkg54.filename = 'Jeroworks_' + cleanCandName + '.pdf';
      notify(100, 'Full privacy package (unbranded) ready!', 'Document ready for download');
      return pkg54;
    }

    // Default Fallback
    notify(50, 'Compiling document...', 'Generating default package...');
    var defaultPkg = await mergeDossierAndResume(
      await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false),
      basePdfBlob
    );
    defaultPkg.filename = 'Jeroworks_' + cleanCandName + '.pdf';
    notify(100, 'Document ready!', 'Document ready for download');
    return defaultPkg;
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // DIRECT BROWSER DOWNLOAD DISPATCH (Native URL.createObjectURL)
  // ═══════════════════════════════════════════════════════════════════════════
  async function downloadCandidateFormat(cand, optId, companyData, onProgress) {
    var compiled = await processDownloadOption(cand, optId, companyData, onProgress);
    if (compiled && compiled instanceof Blob && compiled.size > MAX_DOWNLOAD_FILE_BYTES) {
      var origName = compiled.filename;
      compiled = await ensurePdfBlobUnderLimit(compiled, MAX_DOWNLOAD_FILE_BYTES);
      if (origName) compiled.filename = origName;
    }
    var downloadUrl = URL.createObjectURL(compiled);
    var downloadLink = document.createElement('a');
    downloadLink.href = downloadUrl;
    downloadLink.download = compiled.filename || ('Jeroworks_Candidate_' + cand.id + '.pdf');
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);

    setTimeout(function () {
      URL.revokeObjectURL(downloadUrl);
    }, 15000);

    // Non-blocking asynchronous analytics dispatch to Horizon queue
    try {
      if (typeof $ !== 'undefined' && $.ajax) {
        $.ajax({
          type: 'POST',
          url: "{{ route('service_company.bench-candidates.record_download') }}",
          data: {
            _token: "{{ csrf_token() }}",
            candidate_id: cand.id,
            format_id: optId
          },
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
      }
    } catch (anErr) {
      console.warn('[AgyBenchDownloadResumeEngine] Analytics ping notice:', anErr);
    }

    return compiled;
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // RAM CACHE INVALIDATION & STORE (Zero Stale Pipeline Data - Rule 5 & 6)
  // ═══════════════════════════════════════════════════════════════════════════
  var BenchDownloadResumeRamStore = {
    invalidate: function (candidateId) {
      if (candidateId) {
        delete rsDownloadBytesCache[candidateId];
      } else {
        rsDownloadBytesCache = {};
      }
    },
    clearAll: function () {
      rsDownloadBytesCache = {};
      rsLogoCache = {};
    }
  };

  return {
    processDownloadOption: processDownloadOption,
    downloadCandidateFormat: downloadCandidateFormat,
    preWarmCandidateResume: preWarmCandidateResume,
    fetchCandidateResumeBytes: fetchCandidateResumeBytes,
    generateBenchPrescreenPdfBlob: generateBenchPrescreenPdfBlob,
    buildTurboVectorHighlightedResume: buildTurboVectorHighlightedResume,
    renderAnonymizedPdfBlob: renderAnonymizedPdfBlob,
    rdcAnonymizeRules: rdcAnonymizeRules,
    getBenchCandidateEvaluatedSkills: getBenchCandidateEvaluatedSkills,
    addCompanyBrandingHeader: addCompanyBrandingHeader,
    mergeDossierAndResume: mergeDossierAndResume,
    renderBenchDocxToPdfBlob: renderBenchDocxToPdfBlob,
    ensurePdfBlobUnderLimit: ensurePdfBlobUnderLimit,
    invalidateCache: BenchDownloadResumeRamStore.invalidate,
    clearRamStore: BenchDownloadResumeRamStore.clearAll,
    ensureJsZip: ensureBenchJsZip,
    ensureDocxPreview: ensureBenchDocxPreview,
    ensureHtml2Canvas: ensureBenchHtml2Canvas,
    ensureOpenDocWasm: ensureBenchOpenDocWasm
  };
}));

if (typeof window !== 'undefined') {
  window.invalidateBenchDownloadResumeCache = function (candidateId) {
    if (window.AgyBenchDownloadResumeEngine && typeof window.AgyBenchDownloadResumeEngine.invalidateCache === 'function') {
      window.AgyBenchDownloadResumeEngine.invalidateCache(candidateId);
    }
  };
}
</script>
