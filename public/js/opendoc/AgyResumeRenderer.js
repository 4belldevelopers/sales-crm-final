/**
 * AgyResumeRenderer.js
 * 
 * Master Client-Side Resume Rendering Engine for JERO ATS.
 * Unifies the entire WASM & PDF rendering pipeline across both:
 *   1. Download Resume Center (job-applications/showdetail)
 *   2. Forward Resume Drawer (cvtracker/fwd_resume)
 *
 * Guarantees 100% byte-for-byte, pixel-perfect identical output for all options:
 *   - Option 1:  Original Candidate Resume
 *   - Option 5:  Pre-Screened Report (pdfMake dossier)
 *   - Option 41: Resume + Company Branding Header
 *   - Option 42: Pre-Screened Report (Branded) + Original Resume Package
 *   - Option 43: Pre-Screened Report without Company Branding + Resume Package
 *   - Option 44: Resume + True Sub-Pixel Highlighted Keywords
 *   - Option 45: Resume + Highlighted Keywords + Company Branding
 *   - Option 46: Pre-Screened Report (Branded) + Highlighted Resume
 *   - Option 47: Pre-Screened Report (Unbranded) + Highlighted Resume
 *   - Option 48: Hide Contact Details (keep name)
 *   - Option 49: Hide Candidate Name & Contact Details (fully anonymous)
 *   - Option 50: Hide Contact Details + Company Branding
 *   - Option 51: Hide Contact Details + Pre-Screened Report (Branded)
 *   - Option 52: Hide Contact Details + Pre-Screened Report without Company Branding
 *   - Option 53: Hide Contact Details + Pre-Screened Report + Highlighted Keywords
 *
 * Technology Stack:
 *   - Rust WebAssembly core (casual_doc_wasm) for DOCX parsing and 288 DPI vector-raster rendering
 *   - pdf-lib for vector header branding injection and A4 page-normalized merging
 *   - pdfMake for responsive executive dossier Pre-Screened Reports
 *   - PDF.js for text geometry extraction, sub-pixel keyword measuring, and PII coordinate location
 *   - jsPDF for high-res page canvas compositing
 *
 * ZERO server-side conversion code. ZERO Mammoth lossy text fallback.
 */

(function (root, factory) {
  'use strict';
  var exp = factory();
  if (typeof window !== 'undefined') {
    window.AgyResumeRenderer = exp;
  }
  if (typeof root !== 'undefined') {
    root.AgyResumeRenderer = exp;
  }
  if (typeof self !== 'undefined') {
    self.AgyResumeRenderer = exp;
  }
  if (typeof globalThis !== 'undefined') {
    globalThis.AgyResumeRenderer = exp;
  }
  if (typeof module === 'object' && module && module.exports) {
    module.exports = exp;
  }
}(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

  // ═══════════════════════════════════════════════════════════════════════════
  // STATE & CACHES
  // ═══════════════════════════════════════════════════════════════════════════
  var rsPdfReady = null;
  var rsPdfLibReady = null;
  var rsPdfJsReady = null;
  var rsJsZipReady = null;
  var rsJsPdfReady = null;
  var rsOpenDocLocalReady = null;
  var rsOpenDocExternalReady = null;
  var rsLogoCache = {};
  var rsResumeBytesCache = {};
  var rsDocxMaxBytes = 15 * 1024 * 1024;
  var rsPdfMaxBytes = 35 * 1024 * 1024;

  // ═══════════════════════════════════════════════════════════════════════════
  // SVG ICONS FOR PRE-SCREENED REPORT DOSSIER (pdfMake)
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
        if (ok) {
          s.setAttribute('data-ready', '1');
          resolve();
        } else {
          reject(new Error('Load failed: ' + src));
        }
      };
      s.src = src;
      s.async = true;
      s.onload = function () { finish(true); };
      s.onerror = function () { finish(false); };
      if (!existing) document.head.appendChild(s);
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

  function ensurePdfMake() {
    if (rsPdfReady && window.pdfMake && window.pdfMake.vfs && window.pdfMake.vfs['Roboto-Regular.ttf']) return rsPdfReady;
    var core = ['/js/pdfmake.min.js', 'https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/pdfmake.min.js'];
    var vfs = ['/js/vfs_fonts.min.js', 'https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/vfs_fonts.min.js'];
    rsPdfReady = rsTryList(core, 0, function () { return !!window.pdfMake; })
      .then(function () { return rsTryList(vfs, 0, function () { return !!(window.pdfMake.vfs && window.pdfMake.vfs['Roboto-Regular.ttf']); }); })
      .then(function () {
        window.pdfMake.fonts = { Roboto: { normal: 'Roboto-Regular.ttf', bold: 'Roboto-Medium.ttf', italics: 'Roboto-Italic.ttf', bolditalics: 'Roboto-MediumItalic.ttf' } };
      });
    return rsPdfReady;
  }

  function ensurePdfLib() {
    if (rsPdfLibReady && window.PDFLib && window.PDFLib.PDFDocument) return rsPdfLibReady;
    var sources = ['/js/pdf-lib.min.js', 'https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js'];
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

  function ensureJsZip() {
    if (rsJsZipReady && window.JSZip) return rsJsZipReady;
    var sources = ['/js/jszip.min.js', 'https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js'];
    rsJsZipReady = rsTryList(sources, 0, function () { return !!window.JSZip; });
    return rsJsZipReady;
  }

  function ensureJsPdf() {
    if (rsJsPdfReady && window.jspdf && typeof window.jspdf.jsPDF === 'function') return rsJsPdfReady;
    var sources = ['/js/jspdf.umd.min.js', 'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js'];
    var wasAmd = (typeof window !== 'undefined' && window.define && window.define.amd);
    if (wasAmd) window.define.amd = false;
    rsJsPdfReady = rsTryList(sources, 0, function () { return !!(window.jspdf && typeof window.jspdf.jsPDF === 'function'); }).then(function () {
      if (wasAmd && window.define) window.define.amd = wasAmd;
      return window.jspdf;
    }).catch(function (err) {
      if (wasAmd && window.define) window.define.amd = wasAmd;
      throw err;
    });
    return rsJsPdfReady;
  }

  function ensurePdfJs() {
    if (rsPdfJsReady && window.pdfjsLib && typeof window.pdfjsLib.getDocument === 'function') return rsPdfJsReady;
    var sources = ['https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js'];
    rsPdfJsReady = rsTryList(sources, 0, function () { return !!(window.pdfjsLib && typeof window.pdfjsLib.getDocument === 'function'); }).then(function () {
      try {
        var ver = window.pdfjsLib.version || '2.11.338';
        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/' + ver + '/pdf.worker.min.js';
      } catch (err) {}
      return window.pdfjsLib;
    });
    return rsPdfJsReady;
  }

  function ensureOpenDocLab(allowExternalFallback) {
    allowExternalFallback = allowExternalFallback === true;
    if (rsOpenDocLocalReady) return rsOpenDocLocalReady;

    if (window.AgyWasmEngine && window.AgyWasmEngine.wasmModule && typeof window.AgyWasmEngine.wasmModule.open === 'function') {
      rsOpenDocLocalReady = Promise.resolve(window.AgyWasmEngine.wasmModule);
      return rsOpenDocLocalReady;
    }

    var openDocSources = [
      {
        label: 'Cloudflare OpenDoc runtime',
        moduleUrl: 'https://file.jero.works/js/opendoc/casual_doc_wasm.js?v=opendoc-20260903',
        wasmUrl: 'https://file.jero.works/js/opendoc/casual_doc_wasm_bg.wasm?v=opendoc-20260903'
      },
      {
        label: 'Local OpenDoc runtime',
        moduleUrl: '/js/opendoc/casual_doc_wasm.js',
        wasmUrl: '/js/opendoc/casual_doc_wasm_bg.wasm'
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
          console.warn(openDocSources[i].label + ' unavailable:', err);
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

  // ═══════════════════════════════════════════════════════════════════════════
  // UTILITY & FORMATTING FUNCTIONS
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

  function fileSafeName(value, fallback) {
    return clean(value, fallback || 'Candidate').replace(/[\\/:*?"<>|]+/g, ' ').replace(/\s+/g, ' ').trim() || (fallback || 'Candidate');
  }

  function resumeTrackingFileName(data, marker, ext) {
    ext = clean(ext || 'pdf', 'pdf').toLowerCase().replace(/[^a-z0-9]+/g, '') || 'pdf';
    return 'Jeroworks_' + fileSafeName(data && data.name, 'Candidate') + '.' + ext;
  }

  function isGap(value) {
    value = clean(value, '');
    return !value || value === '--' || value === '-' || /^available 01 jan 1970$/i.test(value);
  }

  function cityOnly(value) {
    value = clean(value, '--');
    if (isGap(value)) return '--';
    var commaParts = value.split(',').map(function (part) { return clean(part, ''); }).filter(Boolean);
    if (commaParts.length > 1) return commaParts[0];
    var states = 'andhra pradesh|arunachal pradesh|assam|bihar|chhattisgarh|goa|gujarat|haryana|himachal pradesh|jharkhand|karnataka|kerala|madhya pradesh|maharashtra|manipur|meghalaya|mizoram|nagaland|odisha|punjab|rajasthan|sikkim|tamil nadu|telangana|tripura|uttar pradesh|uttarakhand|west bengal|delhi|jammu and kashmir|ladakh|puducherry|chandigarh|dadra and nagar haveli|daman and diu|andaman and nicobar islands|lakshadweep';
    var pattern = new RegExp('\\s+(' + states + ')$', 'i');
    return clean(value.replace(pattern, ''), '--');
  }

  function matchTone(score, completeness) {
    if (completeness < 45) return { label: 'Profile Review', priority: 'REVIEW', decision: 'DATA REVIEW REQUIRED', round: 'Recruiter Validation', color: '#7c3aed', fill: '#f5f3ff', border: '#ddd6fe' };
    if (score >= 80) return { label: 'Excellent Fit', priority: 'HIGH', decision: 'RECOMMENDED FOR INTERVIEW', round: 'Technical L1', color: '#047857', fill: '#ecfdf5', border: '#a7f3d0' };
    if (score >= 60) return { label: 'Strong Fit', priority: 'HIGH', decision: 'RECOMMENDED FOR INTERVIEW', round: 'Technical L1', color: '#1d4ed8', fill: '#eff6ff', border: '#bfdbfe' };
    if (score >= 40) return { label: 'Needs Review', priority: 'MEDIUM', decision: 'RECRUITER REVIEW REQUIRED', round: 'Recruiter Review', color: '#b45309', fill: '#fffbeb', border: '#fde68a' };
    return { label: 'Review Required', priority: 'REVIEW', decision: 'REVIEW BEFORE DECISION', round: 'Recruiter Validation', color: '#be123c', fill: '#fff1f2', border: '#fecdd3' };
  }

  function dataCompleteness(data) {
    var checks = [
      data.name, data.designation || data.jobTitle, data.experience, data.company, data.location,
      data.education, data.notice, data.workMode, data.careerSummary
    ];
    var score = checks.filter(function (v) { return !isGap(v); }).length;
    if ((data.skills || []).length) score += 1;
    if ((data.validation || []).length) score += 1;
    if (!isGap(data.recruiterNote)) score += 1;
    return Math.round(score / 12 * 100);
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

  function downloadBlob(blob, fileName) {
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = fileName;
    document.body.appendChild(a);
    a.click();
    setTimeout(function () {
      URL.revokeObjectURL(url);
      if (a.parentNode) a.parentNode.removeChild(a);
    }, 1500);
  }

  async function yieldToMain() {
    if (typeof window !== 'undefined' && window.scheduler && typeof window.scheduler.yield === 'function') {
      try {
        await window.scheduler.yield();
        return;
      } catch (e) {}
    }
    await new Promise(function (resolve) { setTimeout(resolve, 0); });
  }

  async function fetchResumeBytes(resumeUrl, maxBytes) {
    resumeUrl = clean(resumeUrl, '');
    if (resumeUrl === '--') resumeUrl = '';
    if (!resumeUrl) throw new Error('Resume URL not available');
    var byteLimit = parseInt(maxBytes, 10) || rsDocxMaxBytes;
    var fetchOptions = { cache: 'no-store' };
    try {
      var resumeOrigin = (new URL(resumeUrl, window.location.href)).origin;
      if (resumeOrigin === window.location.origin) fetchOptions.credentials = 'include';
      else fetchOptions.mode = 'cors';
    } catch (err) {
      fetchOptions.credentials = 'include';
    }
    var response = await fetch(resumeUrl, fetchOptions);
    if (!response.ok) throw new Error('Resume request failed with HTTP ' + response.status);
    var size = parseInt(response.headers.get('content-length') || '0', 10);
    if (size && size > byteLimit) throw new Error('Resume is larger than the browser conversion limit');
    var bytes = await response.arrayBuffer();
    if (bytes.byteLength > byteLimit) throw new Error('Resume is larger than the browser conversion limit');
    return bytes;
  }

  async function fetchResumeBytesFromSources(resumeUrls, maxBytes) {
    var sources = (Array.isArray(resumeUrls) ? resumeUrls : [resumeUrls]).map(function (url) {
      return clean(url, '');
    }).filter(function (url, index, self) {
      return url && url !== '--' && self.indexOf(url) === index;
    });
    if (!sources.length) throw new Error('Direct resume URL not available');
    var lastErr = null;
    for (var i = 0; i < sources.length; i++) {
      try {
        return await fetchResumeBytes(sources[i], maxBytes);
      } catch (err) {
        lastErr = err;
        console.warn('Direct resume fetch failed:', sources[i], err);
      }
    }
    throw (lastErr || new Error('Direct resume fetch failed'));
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // DOCX XML PRE-PROCESSING & METADATA EXTRACTION (Matches showdetail 1:1)
  // ═══════════════════════════════════════════════════════════════════════════
  function agyExtractDocxMetadata(docXml) {
    var metadata = { tables: [], paragraphs: [] };
    if (!docXml) return metadata;

    try {
      var tbls = docXml.getElementsByTagName('w:tbl');
      if (!tbls || !tbls.length) tbls = docXml.getElementsByTagName('tbl');
      for (var t = 0; t < tbls.length; t++) {
        var tbl = tbls[t];
        var gridCols = tbl.getElementsByTagName('w:gridCol');
        if (!gridCols || !gridCols.length) gridCols = tbl.getElementsByTagName('gridCol');
        var colWidths = [];
        var totalW = 0;
        for (var c = 0; c < gridCols.length; c++) {
          var w = parseInt(gridCols[c].getAttribute('w:w') || gridCols[c].getAttribute('w') || '0', 10);
          colWidths.push(w);
          totalW += w;
        }
        var percentages = colWidths.map(function (w) {
          return totalW > 0 ? ((w / totalW) * 100).toFixed(2) + '%' : '';
        });

        var tblBorders = tbl.getElementsByTagName('w:tblBorders')[0] || tbl.getElementsByTagName('tblBorders')[0];
        var hasInsideV = false;
        var insideVColor = '#cbd5e1';
        var insideVSize = '1px';
        if (tblBorders) {
          var insideV = tblBorders.getElementsByTagName('w:insideV')[0] || tblBorders.getElementsByTagName('insideV')[0];
          if (insideV) {
            var vVal = (insideV.getAttribute('w:val') || insideV.getAttribute('val') || '').toLowerCase();
            if (vVal && vVal !== 'none' && vVal !== 'nil') {
              hasInsideV = true;
              var cHex = insideV.getAttribute('w:color') || insideV.getAttribute('color') || '';
              if (cHex && cHex !== 'auto') insideVColor = '#' + cHex;
              var sz = parseInt(insideV.getAttribute('w:sz') || insideV.getAttribute('sz') || '8', 10);
              insideVSize = Math.max(1, Math.round(sz / 8)) + 'px';
            }
          }
        }

        metadata.tables.push({
          colCount: gridCols.length,
          percentages: percentages,
          hasInsideV: hasInsideV,
          insideVColor: insideVColor,
          insideVSize: insideVSize
        });
      }

      var ps = docXml.getElementsByTagName('w:p');
      if (!ps || !ps.length) ps = docXml.getElementsByTagName('p');
      for (var p = 0; p < ps.length; p++) {
        var pNode = ps[p];
        var tabsNode = pNode.getElementsByTagName('w:tabs')[0] || pNode.getElementsByTagName('tabs')[0];
        var pInfo = { hasRightTab: false, hasLeader: false, leaderType: null };
        if (tabsNode) {
          var tabList = tabsNode.getElementsByTagName('w:tab');
          if (!tabList || !tabList.length) tabList = tabsNode.getElementsByTagName('tab');
          for (var tb = 0; tb < tabList.length; tb++) {
            var tItem = tabList[tb];
            var val = (tItem.getAttribute('w:val') || tItem.getAttribute('val') || '').toLowerCase();
            var leader = (tItem.getAttribute('w:leader') || tItem.getAttribute('leader') || '').toLowerCase();
            var pos = parseInt(tItem.getAttribute('w:pos') || tItem.getAttribute('pos') || '0', 10);
            if (val === 'right' || val === 'decimal' || pos >= 7200) {
              pInfo.hasRightTab = true;
              if (leader && leader !== 'none') {
                pInfo.hasLeader = true;
                pInfo.leaderType = leader;
              }
            }
          }
        }
        metadata.paragraphs.push(pInfo);
      }
    } catch (ex) {
      console.warn('Strategy 3 XML metadata extraction warning:', ex);
    }
    return metadata;
  }

  async function agyPreProcessDocxZip(docxBytes, options) {
    try {
      await ensureJsZip();
      var zip = await window.JSZip.loadAsync(docxBytes);
      var docFile = zip.file('word/document.xml');
      if (!docFile) return { bytes: docxBytes, metadata: null };

      var xmlText = await docFile.async('string');
      var sanitizedXml = xmlText
        .replace(/[\uF0A7\uF0A8\u25AA\u25A0\u25A1]|&#xF0A7;|&#61607;|&#xF0A8;|&#61608;/gi, '▪')
        .replace(/[\uF06F\u25CB]|&#xF06F;|&#61551;/gi, '○')
        .replace(/[\uF0D8\u27A2\u2794]|&#xF0D8;|&#61656;/gi, '➢')
        .replace(/[\uF0FC\u2713]|&#xF0FC;|&#61692;/gi, '✓')
        .replace(/[\uF076\u2714]|&#xF076;|&#61558;/gi, '✔')
        .replace(/[\uF0A4\u25C6]|&#xF0A4;/gi, '◆')
        .replace(/[\uF0A5\u25C7]|&#xF0A5;/gi, '◇')
        .replace(/[\uF02D\u2013\u2014]|&#xF02D;/gi, '–')
        .replace(/\uF0E0/g, '✉')
        .replace(/[\uF0B7\uF0B0\u2022\u25CF]|&#xF0B7;|&#61623;/gi, '•')
        .replace(/[\uE000-\uF8FF]/g, '')
        .replace(/<w:spacing\b([^>]*)\/?>/gi, function (match, attrs) {
          var newAttrs = attrs
            .replace(/\bw:line="(\d+)"/gi, function (m, val) {
              return parseInt(val, 10) > 280 ? 'w:line="260"' : m;
            })
            .replace(/\bw:after="(\d+)"/gi, function (m, val) {
              return parseInt(val, 10) > 80 ? 'w:after="40"' : m;
            })
            .replace(/\bw:before="(\d+)"/gi, function (m, val) {
              return parseInt(val, 10) > 80 ? 'w:before="0"' : m;
            });
          return '<w:spacing' + newAttrs + '/>';
        });

      var numFile = zip.file('word/numbering.xml');
      if (numFile) {
        try {
          var numXml = await numFile.async('string');
          var cleanNumXml = numXml.replace(/<w:lvl\b([^>]*)>([\s\S]*?)<\/w:lvl>/gi, function (match, lvlAttrs, lvlContent) {
            var ilvlMatch = lvlAttrs.match(/\bw:ilvl="(\d+)"/i) || lvlContent.match(/<w:ilvl\b[^>]*w:val="(\d+)"/i);
            var ilvl = ilvlMatch ? parseInt(ilvlMatch[1], 10) : 0;

            var isBullet = false;
            var newContent = lvlContent.replace(/<w:lvlText\b([^>]*?)\bw:val="([^"]*)"([^>]*?)\/?>/gi, function (m, pre, val, post) {
              if (/%\d/.test(val)) return m;
              isBullet = true;
              var mapped = '•';
              if (/[\uF0A7\uF0A8\u25AA\u25A0\u25A1]|&#xF0A7;|&#61607;|&#xF0A8;|&#61608;/i.test(val)) mapped = '▪';
              else if (/[\uF06F\u25CB]|&#xF06F;|&#61551;/i.test(val)) mapped = '○';
              else if (/[\uF0D8\u27A2\u2794>]|&#xF0D8;|&#61656;/i.test(val)) mapped = '➢';
              else if (/[\uF0FC\u2713]|&#xF0FC;|&#61692;/i.test(val)) mapped = '✓';
              else if (/[\uF076\u2714]|&#xF076;|&#61558;/i.test(val)) mapped = '✔';
              else if (/[\uF0A4\u25C6]|&#xF0A4;/i.test(val)) mapped = '◆';
              else if (/[\uF0A5\u25C7]|&#xF0A5;/i.test(val)) mapped = '◇';
              else if (/[\uF02D\-\u2013]|&#xF02D;/i.test(val)) mapped = '–';
              return '<w:lvlText' + pre + 'w:val="' + mapped + '"' + post.replace(/\/$/, '') + '/>';
            });

            if (isBullet) {
              newContent = newContent.replace(/w:ascii="[^"]*"/gi, 'w:ascii="Arial"')
                .replace(/w:hAnsi="[^"]*"/gi, 'w:hAnsi="Arial"');
            }

            var leftTwips = 360 + (ilvl * 360);
            var hangingTwips = 240;

            if (/<w:ind\b[^>]*\/?>/i.test(newContent)) {
              newContent = newContent.replace(/<w:ind\b[^>]*\/?>/gi, '<w:ind w:left="' + leftTwips + '" w:hanging="' + hangingTwips + '"/>');
            } else {
              newContent = newContent.replace(/<\/w:pPr>/i, '<w:ind w:left="' + leftTwips + '" w:hanging="' + hangingTwips + '"/></w:pPr>');
            }

            return '<w:lvl' + lvlAttrs + '>' + newContent + '</w:lvl>';
          });
          zip.file('word/numbering.xml', cleanNumXml);
        } catch (nErr) {
          console.warn('numbering.xml pre-process warning:', nErr);
        }
      }

      var stylesFile = zip.file('word/styles.xml');
      if (stylesFile) {
        try {
          var stylesXml = await stylesFile.async('string');
          var cleanStylesXml = stylesXml.replace(/<w:spacing\b([^>]*)\/?>/gi, function (match, attrs) {
            var newAttrs = attrs
              .replace(/\bw:line="(\d+)"/gi, function (m, val) {
                return parseInt(val, 10) > 280 ? 'w:line="260"' : m;
              })
              .replace(/\bw:after="(\d+)"/gi, function (m, val) {
                return parseInt(val, 10) > 80 ? 'w:after="40"' : m;
              });
            return '<w:spacing' + newAttrs + '/>';
          });
          zip.file('word/styles.xml', cleanStylesXml);
        } catch (sErr) {
          console.warn('styles.xml pre-process warning:', sErr);
        }
      }

      sanitizedXml = sanitizedXml.replace(/<w:pPr\b([^>]*)>/gi, function (match, attrs) {
        if ((match.indexOf('w:numPr') !== -1 || match.indexOf('List') !== -1) && match.indexOf('contextualSpacing') === -1) {
          return '<w:pPr' + attrs + '><w:contextualSpacing/>';
        }
        return match;
      });

      options = options || {};

      // Redact PII in DOCX XML directly if requested
      if (options.anonymizeRules && typeof rdcRedactTextValue === 'function') {
        sanitizedXml = sanitizedXml.replace(/<w:t\b([^>]*)>([\s\S]*?)<\/w:t>/gi, function (match, attrs, content) {
          return '<w:t' + attrs + '>' + rdcRedactTextValue(content, options.anonymizeRules) + '</w:t>';
        });
      }

      // Inject native OOXML yellow highlighting for skill keywords if requested
      if (Array.isArray(options.keywords) && options.keywords.length) {
        var kwList = options.keywords.map(function (k) {
          return String(k || '').trim();
        }).filter(function (k) {
          return k.length >= 2;
        }).sort(function (a, b) {
          return b.length - a.length;
        });

        if (kwList.length) {
          var kwPatterns = kwList.map(function (k) {
            var esc = k.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            var leftBound = /^\w/.test(k) ? '\\b' : '(?:^|\\s)';
            var rightBound = /\w$/.test(k) ? '\\b' : '(?=$|\\s|[.,;!?)])';
            return leftBound + esc + rightBound;
          });
          var kwRegex = new RegExp('(' + kwPatterns.join('|') + ')', 'gi');

          sanitizedXml = sanitizedXml.replace(/<w:r\b([^>]*)>([\s\S]*?)<\/w:r>/gi, function (fullRun, rAttrs, rContent) {
            if (rContent.indexOf('<w:highlight') !== -1) return fullRun;
            var tMatch = rContent.match(/<w:t\b([^>]*)>([\s\S]*?)<\/w:t>/i);
            if (!tMatch) return fullRun;

            var text = tMatch[2];
            kwRegex.lastIndex = 0;
            if (!kwRegex.test(text)) return fullRun;

            kwRegex.lastIndex = 0;
            var rPrMatch = rContent.match(/<w:rPr\b[^>]*>[\s\S]*?<\/w:rPr>/i);
            var baseRPr = rPrMatch ? rPrMatch[0] : '';
            var highlightedRPr = baseRPr
              ? (baseRPr.indexOf('</w:rPr>') !== -1 ? baseRPr.replace('</w:rPr>', '<w:highlight w:val="yellow"/></w:rPr>') : baseRPr + '<w:highlight w:val="yellow"/>')
              : '<w:rPr><w:highlight w:val="yellow"/></w:rPr>';

            var parts = [];
            var lastIdx = 0;
            var m;
            kwRegex.lastIndex = 0;
            while ((m = kwRegex.exec(text)) !== null) {
              if (m.index > lastIdx) {
                var preText = text.substring(lastIdx, m.index);
                parts.push('<w:r' + rAttrs + '>' + baseRPr + '<w:t xml:space="preserve">' + preText + '</w:t></w:r>');
              }
              parts.push('<w:r' + rAttrs + '>' + highlightedRPr + '<w:t xml:space="preserve">' + m[0] + '</w:t></w:r>');
              lastIdx = m.index + m[0].length;
            }
            if (lastIdx < text.length) {
              var postText = text.substring(lastIdx);
              parts.push('<w:r' + rAttrs + '>' + baseRPr + '<w:t xml:space="preserve">' + postText + '</w:t></w:r>');
            }
            return parts.join('');
          });
        }
      }

      var parser = new DOMParser();
      var docXml = parser.parseFromString(sanitizedXml, 'application/xml');
      var metadata = agyExtractDocxMetadata(docXml);

      zip.file('word/document.xml', sanitizedXml);
      var cleanBytes = await zip.generateAsync({ type: 'uint8array' });
      return { bytes: cleanBytes, metadata: metadata };
    } catch (err) {
      console.warn('DOCX XML pre-processing fallback to original bytes:', err);
      return { bytes: docxBytes, metadata: null };
    }
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // OPEN-DOC WASM TYPESETTING & BITMAP HELPERS (Matches showdetail 1:1)
  // ═══════════════════════════════════════════════════════════════════════════
  function openDocPagePoints(doc, index) {
    var fallback = [595.28, 841.89];
    try {
      var size = doc.pageSize(index);
      var width = size && size.widthTwip ? size.widthTwip / 20 : 0;
      var height = size && size.heightTwip ? size.heightTwip / 20 : 0;
      if (size && typeof size.free === 'function') size.free();
      if (width >= 240 && height >= 240 && width <= 1600 && height <= 2200) return [width, height];
    } catch (err) {
      console.warn('OpenDoc page size fallback used:', err);
    }
    return fallback;
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

  function openDocNativePdfBlob(doc) {
    if (!doc || typeof doc.availableExportFormats !== 'function' || typeof doc.exportAs !== 'function') return null;
    var formats = [];
    try {
      formats = doc.availableExportFormats() || [];
    } catch (formatErr) {
      return null;
    }
    var hasPdf = formats.some(function (format) {
      return String(format || '').toLowerCase() === 'pdf';
    });
    if (!hasPdf) return null;
    var modes = ['exact_if_unchanged', 'preserve_when_safe', 'semantic'];
    for (var i = 0; i < modes.length; i++) {
      var artifact = null;
      try {
        artifact = doc.exportAs('pdf', modes[i]);
        var bytes = artifact && artifact.bytes ? artifact.bytes : null;
        var mime = artifact && artifact.mimeType ? artifact.mimeType : 'application/pdf';
        if (bytes && bytes.length) return new Blob([bytes], { type: mime || 'application/pdf' });
      } catch (exportErr) {
        console.warn('OpenDoc native PDF export failed for ' + modes[i] + ':', exportErr);
      } finally {
        if (artifact && typeof artifact.free === 'function') artifact.free();
      }
    }
    return null;
  }

  function agyCollectWasmDocxWordHits(doc, tokens) {
    var allHits = [];
    if (!doc || !Array.isArray(tokens) || !tokens.length) return allHits;
    if (typeof doc.findText !== 'function' || typeof doc.selectionRects !== 'function') return allHits;

    var seen = {};
    var uniqueTokens = [];
    for (var t = 0; t < tokens.length; t++) {
      var tok = String(tokens[t] || '').trim();
      var low = tok.toLowerCase();
      if (!seen[low] && tok.length >= 2) {
        seen[low] = true;
        uniqueTokens.push(tok);
      }
    }

    uniqueTokens.forEach(function (word) {
      var first = null;
      try {
        if (typeof doc.firstPosition !== 'function') return;
        first = doc.firstPosition();
        if (!first || !first.node) return;
        var curNode = first.node;
        var curOffset = first.offset || 0;
        var matchSeen = {};

        for (var iter = 0; iter < 100; iter++) {
          var match = null;
          try {
            match = doc.findText(word, curNode, curOffset, true, false);
            if (!match || !match.__wbg_ptr || !match.found) break;
            var sNode = match.startNode;
            var sOff = match.startOffset;
            var eNode = match.endNode;
            var eOff = match.endOffset;
            if (!sNode || !eNode) break;

            var mKey = sNode + ':' + sOff;
            if (matchSeen[mKey]) break;
            matchSeen[mKey] = true;

            var rects = doc.selectionRects(sNode, sOff, eNode, eOff);
            if (rects && rects.length >= 5) {
              for (var r = 0; r < rects.length; r += 5) {
                allHits.push({
                  page: rects[r],
                  x: rects[r + 1],
                  y: rects[r + 2],
                  w: rects[r + 3],
                  h: rects[r + 4],
                  word: word
                });
              }
            }
            if (curNode === eNode && curOffset === eOff) break;
            curNode = eNode;
            curOffset = eOff;
          } catch (mErr) {
            break;
          } finally {
            if (match && typeof match.free === 'function') match.free();
          }
        }
      } catch (err) {
      } finally {
        if (first && typeof first.free === 'function') first.free();
      }
    });

    return allHits;
  }

  /**
   * Primary DOCX→PDF WASM Renderer (identical to showdetail line 7704).
   * Renders pages via 288 DPI rasterization, composites highlights if requested,
   * cleans up WASM memory deterministically, and produces a valid PDF Blob.
   */
  async function renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, options) {
    options = options || {};
    if (options.statusCallback) options.statusCallback('Initializing WASM typesetting core (20%)...');
    await yieldToMain();

    var initResults = await Promise.all([
      ensureOpenDocLab(false),
      ensureJsPdf(),
      ensureJsZip()
    ]);
    var openDoc = initResults[0];

    if (options.statusCallback) options.statusCallback('Pre-processing Word XML & font metrics (35%)...');
    await yieldToMain();

    var preOpts = Object.assign({}, options, { keywords: null });
    var preResult = await agyPreProcessDocxZip(docxBytes, preOpts);
    var renderBytes = preResult.bytes || docxBytes;

    if (options.statusCallback) options.statusCallback('Parsing OOXML in WebAssembly (50%)...');
    await yieldToMain();

    var doc = null;
    try {
      var u8 = new Uint8Array(renderBytes);
      try {
        doc = (typeof openDoc.openAs === 'function') ? openDoc.openAs(u8, 'docx') : openDoc.open(u8);
      } catch (err) {
        doc = openDoc.open(u8);
      }

      if (!doc) throw new Error('WASM engine failed to open document');

      var pageCount = parseInt(doc.pageCount, 10);
      if (!pageCount && typeof doc.documentStats === 'function') {
        var stats = doc.documentStats();
        pageCount = parseInt(stats && stats.pages ? stats.pages : 0, 10);
        if (stats && typeof stats.free === 'function') stats.free();
      }
      if (!pageCount || pageCount < 1) throw new Error('WASM engine reported 0 pages for this document');

      // Native vector PDF export check
      var nativeBlob = openDocNativePdfBlob(doc);
      if (nativeBlob) {
        if (options.statusCallback) options.statusCallback('Exporting native vector PDF (100%)...');
        return nativeBlob;
      }

      if (options.statusCallback) options.statusCallback('Extracting searchable text metrics & coordinates (55%)...');
      await yieldToMain();

      var wasmHits = agyCollectWasmDocxWordHits(doc, options.keywords || []);

      if (options.statusCallback) options.statusCallback('Rasterizing pages via WASM vector pipeline (60%)...');
      await yieldToMain();

      var jspdf = await ensureJsPdf();
      var JsPDF = jspdf.jsPDF;
      var pdf = null;
      var renderDpi = 288;

      for (var i = 0; i < pageCount; i++) {
        if (options.statusCallback) {
          options.statusCallback('Rendering WASM page ' + (i + 1) + ' of ' + pageCount + ' (' + Math.round(50 + (i / pageCount) * 45) + '%)...');
        }
        await yieldToMain();

        var bitmap = null;
        var canvas = null;
        try {
          bitmap = doc.renderPage(i, renderDpi);
          canvas = openDocCanvasFromBitmap(bitmap);

          var points = openDocPagePoints(doc, i);
          var pageW = (points && points[0]) ? points[0] : (bitmap.widthPx / renderDpi) * 72;
          var pageH = (points && points[1]) ? points[1] : (bitmap.heightPx / renderDpi) * 72;

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

          // Composite exact-width highlights onto canvas when requested (Options 44, 45, 46, 47, 53)
          if (options.burnHighlights && wasmHits && wasmHits.length) {
            var pageHits = wasmHits.filter(function (h) { return h.page === (i + 1); });
            if (pageHits.length) {
              var ctx = canvas.getContext('2d');
              ctx.save();
              ctx.fillStyle = 'rgba(251, 191, 36, 0.42)';
              var sX = canvas.width / (pageW * 20);
              var sY = canvas.height / (pageH * 20);
              pageHits.forEach(function (h) {
                ctx.fillRect(Math.round(h.x * sX), Math.round(h.y * sY), Math.max(8, Math.round(h.w * sX)), Math.round(h.h * sY));
              });
              ctx.restore();
            }
          }

          var imgData = canvas.toDataURL('image/jpeg', 0.98);
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

      if (options.statusCallback) options.statusCallback('Finalizing PDF (95%)...');
      await yieldToMain();
      var outBlob = pdf.output('blob');
      outBlob.__wasmHits = wasmHits;
      return outBlob;
    } finally {
      if (doc && typeof doc.free === 'function') {
        try { doc.free(); } catch (e) {}
      }
    }
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // VECTOR KEYWORD HIGHLIGHTING (Matches showdetail 1:1)
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

  function rsKeywordPatternsForValue(value) {
    var seen = {};
    var patterns = [];
    rsKeywordAliasValues(value).forEach(function (alias) {
      var pattern = rsKeywordPatternFromValue(alias);
      var key = pattern.toLowerCase();
      if (pattern && !seen[key]) {
        seen[key] = true;
        patterns.push(pattern);
      }
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

  /**
   * Pure vector sub-pixel keyword highlighter for PDF resumes using PDF.js & PDFLib.
   */
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
    if (sig.indexOf('%PDF') === -1) throw new Error('Resume source is not a valid PDF');

    var pdfjsDoc = await window.pdfjsLib.getDocument({ data: bytes }).promise;
    var pagesTextData = [];
    var fullResumeText = '';

    for (var p = 1; p <= pdfjsDoc.numPages; p++) {
      var page = await pdfjsDoc.getPage(p);
      var tc = await page.getTextContent();
      var pageStr = tc.items.map(function (item) { return item.str || ''; }).join(' ');
      fullResumeText += pageStr + '\n';
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

    var measureCanvas = document.createElement('canvas');
    var measureCtx = measureCanvas.getContext('2d');
    function measureSlice(fullStr, startIdx, endIdx, itemWidth, fontH) {
      measureCtx.font = Math.round(fontH) + 'px sans-serif';
      var fullM = measureCtx.measureText(fullStr).width;
      if (!fullM || fullM <= 0) {
        var len = Math.max(1, fullStr.length);
        return {
          offset: (startIdx / len) * itemWidth,
          width: Math.max(3, ((endIdx - startIdx) / len) * itemWidth)
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

    var PDFLib = window.PDFLib;
    var pdfDoc = await PDFLib.PDFDocument.load(sourceBytes);

    if (keywordRe) {
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
            var itemH = Math.max(Math.abs(item.transform[3] || item.height || 0), 9);
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
    }

    var outputBytes = await pdfDoc.save();
    return new Blob([outputBytes], { type: 'application/pdf' });
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // PII ANONYMIZATION & REDACTION (Matches showdetail 1:1)
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
      rdcPushUnique(exactTerms, data.name);
      rdcPlain(data.name).split(/\s+/).forEach(function (token) {
        if (token.length >= 3) rdcPushUnique(exactTerms, token);
      });
    }
    [data.email, data.secondaryEmail].forEach(function (email) {
      rdcPushUnique(emails, email);
      rdcPushUnique(exactTerms, email);
    });
    [data.phone, data.secondaryPhone].forEach(function (phone) {
      rdcPushUnique(phones, phone);
      rdcPushUnique(exactTerms, phone);
    });
    rdcLinkedInTargets(data.linkedin).forEach(function (target) {
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

  function rdcRedactTextValue(text, rules) {
    var original = String(text == null ? '' : text);
    var output = original;
    rules.textRegexes.forEach(function (regex) {
      regex.lastIndex = 0;
      output = output.replace(regex, function (match) { return new Array(Math.max(2, match.length) + 1).join('\u00a0'); });
      regex.lastIndex = 0;
    });
    if (output !== original) return output;
    return rdcShouldRedactText(original, rules) ? new Array(Math.max(2, original.length) + 1).join('\u00a0') : original;
  }

  function rsTextItemRect(item, viewport) {
    if (!item || !viewport || !(window.pdfjsLib && window.pdfjsLib.Util)) return null;
    var tx = window.pdfjsLib.Util.transform(viewport.transform, item.transform);
    var h = Math.max(Math.abs((item.height || 0) * viewport.scale), Math.abs(tx[3] || 0), 6);
    var w = Math.max(Math.abs((item.width || 0) * viewport.scale), 2);
    return { x: tx[4], y: tx[5] - h, w: w, h: h };
  }

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
    var JsPDF = window.jspdf.jsPDF;
    var outPdf = null;
    var textItems = 0;
    var redactions = 0;
    for (var p = 1; p <= doc.numPages; p++) {
      var pdfJsPage = await doc.getPage(p);
      var base = pdfJsPage.getViewport({ scale: 1 });
      var cssScale = Math.min(1.35, 820 / base.width);
      var cssViewport = pdfJsPage.getViewport({ scale: cssScale });
      var renderScale = Math.min(3, 2800 / base.width);
      var renderViewport = pdfJsPage.getViewport({ scale: renderScale });
      var canvas = document.createElement('canvas');
      canvas.width = Math.floor(renderViewport.width);
      canvas.height = Math.floor(renderViewport.height);
      var ctx = canvas.getContext('2d', { willReadFrequently: true });
      ctx.fillStyle = '#ffffff';
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      await pdfJsPage.render({ canvasContext: ctx, viewport: renderViewport }).promise;
      var tc = await pdfJsPage.getTextContent();
      textItems += (tc.items || []).length;
      redactions += rdcDrawAnonymizedBlocks(ctx, tc, renderViewport, rules);
      var pageW = Math.round(cssViewport.width);
      var pageH = Math.round(cssViewport.height);
      var orient = pageW > pageH ? 'l' : 'p';
      if (!outPdf) outPdf = new JsPDF({ orientation: orient, unit: 'px', format: [pageW, pageH] });
      else outPdf.addPage([pageW, pageH], orient);
      outPdf.addImage(canvas.toDataURL('image/jpeg', 0.9), 'JPEG', 0, 0, pageW, pageH, undefined, 'FAST');
    }
    if (!outPdf) throw new Error('No resume pages were rendered');
    if (!textItems) throw new Error('This resume appears to be scanned/image-only. Browser anonymization needs extractable text.');
    return outPdf.output('blob');
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // COMPANY BRANDING HEADER INJECTION (Matches showdetail 1:1)
  // ═══════════════════════════════════════════════════════════════════════════
  async function applyCompanyBrandingToPdfBlob(resumePdfBlob, brandingData) {
    await ensurePdfLib();
    var PDFLib = window.PDFLib;
    var resumeBytes = await resumePdfBlob.arrayBuffer();
    var resumeDoc = await PDFLib.PDFDocument.load(resumeBytes);
    var sourcePages = resumeDoc.getPages();
    if (!sourcePages.length) throw new Error('Resume PDF has no pages');

    var firstPage = sourcePages[0];
    var pageSize = firstPage.getSize();

    var targetDoc = await PDFLib.PDFDocument.create();
    var regularFont = await targetDoc.embedFont(PDFLib.StandardFonts.Helvetica);
    var boldFont = await targetDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);

    var logoUri = await rsLogoDataUri(brandingData && brandingData.reportCompanyLogo);
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

    var companyName = clean(brandingData && brandingData.reportCompanyName, 'Hiring Partner');
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
      newFirst.drawText(companyName.charAt(0).toUpperCase(), {
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
    var dateText = clean(brandingData && brandingData.reportDate, '') || (new Date()).toLocaleDateString();
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
    newFirst.drawText(limitText(companyName, 42, companyName), {
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
  // PRE-SCREENED REPORT DOSSIER (pdfMake - Matches showdetail 1:1)
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
      for (var i = 1; i <= 5; i++) stars.push({ svg: i <= rating ? rsStarGold : rsStarMuted, width: 6.4, margin: [0, 0.5, 1.2, 0] });
      rows.push([
        { text: clean(skill.name).toUpperCase(), style: 'tableCellBold', alignment: 'left' },
        { text: expText !== '' && expText !== '--' ? expText : (exp > 0 ? exp + ' Year' + (exp > 1 ? 's' : '') : '--'), style: 'tableCell', color: '#334155' },
        { columns: stars, columnGap: 0, alignment: 'center', margin: [20, 5, 0, 5] }
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
      var status = map[row.key] || map[row.fallbackKey] || '--';
      var verified = /verified|confirmed|completed|discussed|shared/i.test(status);
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
          { text: status, fontSize: 6.5, bold: true, color: verified ? '#16a34a' : '#b45309', alignment: 'center', margin: [0, 0, 0, 0] }
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
    var completeness = dataCompleteness(data);
    var summary = limitText(data.careerSummary, 560, 'No professional summary available.');
    var noteMissing = isGap(data.recruiterNote);
    var note = noteMissing ? 'No recruiter comments available.' : limitText(data.recruiterNote, 420, '');
    var joining = data.isImmediate ? 'Immediate' : clean(data.notice, '--');
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
        tableHead: { fontSize: 7, bold: true, color: '#334155', fillColor: '#f8fafc', margin: [0, 6, 0, 6] },
        tableCell: { fontSize: 7.2, color: '#334155', margin: [0, 6, 0, 6] },
        tableCellBold: { fontSize: 7.2, bold: true, color: '#0f172a', margin: [0, 6, 0, 6] },
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
          labeledIconLine(rsIconExperience, 'EXPERIENCE:', clean(data.experience, '--').toUpperCase(), 'small', '#64748b', 6.5),
          { stack: [labeledIconLine(rsIconCompany, 'CURRENT COMPANY:', clean(data.company, '--').toUpperCase(), 'small', '#64748b', 6.5)], margin: [0, 3, 0, 0] }
        ], [
          { text: 'HIRING CONFIDENCE', fontSize: 12.4, bold: true, color: '#64748b', alignment: 'center', margin: [0, 8, 0, 0] },
          { text: score + '%', fontSize: 30, bold: true, color: '#1d4ed8', alignment: 'center', margin: [0, 7, 0, 0] }
        ], '#ffffff', '#f8fbff', '#cfe0ff'),
        roundedFullCard([
          {
            table: {
              widths: [116, 116, 164, 116],
              body: [[
                metricCell('Education', data.education, rsIconEducation),
                metricCell('Notice Period', joining, rsIconCalendar),
                metricCell('Location', cityOnly(data.location), rsIconLocation),
                metricCell('Work Mode', data.workMode, rsIconLaptop)
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

  async function buildRecruiterSummaryBlob(data, options) {
    options = options || {};
    var ready = await Promise.all([
      ensurePdfMake(),
      options.noBranding ? Promise.resolve(null) : rsLogoDataUri(data && data.reportCompanyLogo)
    ]);
    return new Promise(function (resolve, reject) {
      try {
        window.pdfMake.createPdf(buildDefinition(data, ready[1], options)).getBlob(function (blob) {
          if (blob && blob.size > 200) resolve(blob);
          else reject(new Error('Recruiter Summary PDF output was empty'));
        });
      } catch (ex) { reject(ex); }
    });
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // A4 NORMALIZED MERGING (Matches showdetail 1:1)
  // ═══════════════════════════════════════════════════════════════════════════
  async function copyPdfPagesToA4(targetDoc, sourceDoc) {
    var A4_W = 595.28;
    var A4_H = 841.89;
    var pages = sourceDoc.getPages();
    if (!pages.length) throw new Error('PDF has no pages to merge');
    for (var i = 0; i < pages.length; i++) {
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

  async function mergePreScreenedReportAndResume(reportBlob, resumeBlob, fileName, mergeOptions) {
    await ensurePdfLib();
    mergeOptions = mergeOptions || {};
    var resumeBytes = await resumeBlob.arrayBuffer();
    var reportBytes = await reportBlob.arrayBuffer();
    var merged = await window.PDFLib.PDFDocument.create();
    var reportDoc = await window.PDFLib.PDFDocument.load(reportBytes);
    var resumeDoc = await window.PDFLib.PDFDocument.load(resumeBytes);

    if (mergeOptions.forceA4 !== false) {
      await copyPdfPagesToA4(merged, reportDoc);
      await copyPdfPagesToA4(merged, resumeDoc);
    } else {
      var reportPages = await merged.copyPages(reportDoc, reportDoc.getPageIndices());
      reportPages.forEach(function (page) { merged.addPage(page); });
      var resumePages = await merged.copyPages(resumeDoc, resumeDoc.getPageIndices());
      resumePages.forEach(function (page) { merged.addPage(page); });
    }

    var mergedBytes = await merged.save();
    var finalBlob = new Blob([mergedBytes], { type: 'application/pdf' });

    if (mergeOptions.returnBlob === true) {
      return finalBlob;
    }

    downloadBlob(finalBlob, fileName);
    return true;
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // MASTER DISPATCHER: processOption(optNum, data, options)
  // Replicates showdetail's processAgyTestOption 1:1, supporting returnBlob:true
  // ═══════════════════════════════════════════════════════════════════════════
  /**
   * Universal Resume Processor.
   * Dispatches any of the 15 ATS resume options with pixel-perfect uniformity.
   *
   * @param {number|string} optNum - Option number (1, 5, 41-53)
   * @param {object} data - Normalized candidate profile & company branding data
   * @param {object} options - Execution options:
   *   - resumeUrl: URL or array of URLs to fetch the resume file from
   *   - resumeExt: 'PDF', 'DOCX', or ''
   *   - rawBytes: ArrayBuffer or Uint8Array of resume (if already fetched)
   *   - keywords: Array of skill keywords to highlight
   *   - returnBlob: boolean (true = return Blob, false = trigger browser download)
   *   - statusCallback: function(msg) for progress reporting
   * @returns {Promise<Blob|boolean>}
   */
  async function processOption(optNum, data, options) {
    optNum = parseInt(optNum, 10);
    options = options || {};
    data = data || {};

    if (options.statusCallback) options.statusCallback('Preparing resume processing...');

    // 1. Identify format extension and fetch resume bytes
    var resumeExt = clean(options.resumeExt || data.resumeExt || '', '').toUpperCase();
    if (!resumeExt && data.resume) {
      var rParts = String(data.resume).split('?')[0].split('.');
      if (rParts.length > 1) resumeExt = rParts.pop().toUpperCase();
    }
    if (!resumeExt) resumeExt = 'PDF';

    if (resumeExt === 'DOC') {
      throw new Error('Word (.doc) binary format is not supported. Please upload as DOCX or PDF.');
    }

    var docxBytes = options.rawBytes || null;
    if (!docxBytes || !docxBytes.byteLength) {
      var sources = (Array.isArray(options.resumeUrl) ? options.resumeUrl : [options.resumeUrl || data.resumeUrl || data.resume || ''])
        .map(function (u) { return clean(u, ''); })
        .filter(function (u, idx, self) { return u && u !== '--' && self.indexOf(u) === idx; });

      var cacheKey = sources[0] || ('cand_' + (data.id || data.name));
      if (rsResumeBytesCache[cacheKey]) {
        docxBytes = rsResumeBytesCache[cacheKey];
      } else {
        if (options.statusCallback) options.statusCallback('Fetching candidate resume bytes (15%)...');
        docxBytes = await fetchResumeBytesFromSources(sources);
        if (cacheKey && docxBytes && docxBytes.byteLength) {
          rsResumeBytesCache[cacheKey] = docxBytes;
        }
      }
    }

    // Inspect magic bytes to confirm real format
    var u8 = new Uint8Array(docxBytes);
    var isPdf = (u8.length >= 4 && u8[0] === 0x25 && u8[1] === 0x50 && u8[2] === 0x44 && u8[3] === 0x46);
    if (isPdf) {
      resumeExt = 'PDF';
    } else {
      resumeExt = 'DOCX';
    }

    // Highlighting keywords
    var allKeywords = Array.isArray(options.keywords) ? options.keywords.slice() : [];
    if (Array.isArray(data.skills)) {
      data.skills.forEach(function (s) {
        var sName = typeof s === 'string' ? s : (s && s.name ? s.name : '');
        if (sName && allKeywords.indexOf(sName) === -1) allKeywords.push(sName);
      });
    }

    var pdfBlob = null;
    var outFileName = 'Jeroworks_' + fileSafeName(data.name, 'Candidate') + '.pdf';

    // ═════════════════════════════════════════════════════════════════════════
    // DISPATCHER BY OPTION ID (1, 5, 41 - 53)
    // ═════════════════════════════════════════════════════════════════════════
    if (optNum === 1) {
      // Option 1: Original Resume
      var mime = resumeExt === 'PDF' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
      pdfBlob = new Blob([docxBytes], { type: mime });
      outFileName = 'Jeroworks_' + fileSafeName(data.name, 'Candidate') + '.' + resumeExt.toLowerCase();

    } else if (optNum === 5) {
      // Option 5: Pre-Screened Report (Standalone)
      if (options.statusCallback) options.statusCallback('Compiling Pre-Screened Report dossier (50%)...');
      pdfBlob = await buildRecruiterSummaryBlob(data, {});
      outFileName = 'PRESCREEN_REPORT_' + fileSafeName(data.name, 'Candidate') + '.pdf';

    } else if (optNum === 41) {
      // Option 41: Resume + Company Branding
      var baseResumeBlob = null;
      if (resumeExt === 'PDF') {
        baseResumeBlob = new Blob([docxBytes], { type: 'application/pdf' });
      } else {
        baseResumeBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, { statusCallback: options.statusCallback });
      }
      if (options.statusCallback) options.statusCallback('Applying Company Branding Header (85%)...');
      pdfBlob = await applyCompanyBrandingToPdfBlob(baseResumeBlob, data);

    } else if (optNum === 42) {
      // Option 42: Pre-Screened Report (Branded) + Original Resume Package
      if (options.statusCallback) options.statusCallback('Generating Pre-Screened Report (30%)...');
      var reportBlob = await buildRecruiterSummaryBlob(data, {});
      var baseResumeBlob = null;
      if (resumeExt === 'PDF') {
        baseResumeBlob = new Blob([docxBytes], { type: 'application/pdf' });
      } else {
        baseResumeBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, { statusCallback: options.statusCallback });
      }
      if (options.statusCallback) options.statusCallback('Merging Report & Resume into A4 package (90%)...');
      var mergedName = resumeTrackingFileName(data, 'C', 'pdf');
      var mergedBlob = await mergePreScreenedReportAndResume(reportBlob, baseResumeBlob, mergedName, { returnBlob: true });
      if (options.returnBlob) return mergedBlob;
      downloadBlob(mergedBlob, mergedName);
      return true;

    } else if (optNum === 43) {
      // Option 43: Pre-Screened Report (Unbranded) + Original Resume Package
      if (options.statusCallback) options.statusCallback('Generating Unbranded Report (30%)...');
      var reportBlob = await buildRecruiterSummaryBlob(data, { noBranding: true });
      var baseResumeBlob = null;
      if (resumeExt === 'PDF') {
        baseResumeBlob = new Blob([docxBytes], { type: 'application/pdf' });
      } else {
        baseResumeBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, { statusCallback: options.statusCallback });
      }
      if (options.statusCallback) options.statusCallback('Merging Report & Resume into A4 package (90%)...');
      var mergedName = resumeTrackingFileName(data, 'C', 'pdf');
      var mergedBlob = await mergePreScreenedReportAndResume(reportBlob, baseResumeBlob, mergedName, { returnBlob: true });
      if (options.returnBlob) return mergedBlob;
      downloadBlob(mergedBlob, mergedName);
      return true;

    } else if (optNum === 44) {
      // Option 44: Resume + Highlighted Keywords
      if (resumeExt === 'PDF') {
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        pdfBlob = await buildTurboVectorHighlightedResume({
          resumeBlob: rawPdfBlob,
          keywords: allKeywords,
          statusCallback: options.statusCallback
        });
      } else {
        pdfBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          keywords: allKeywords,
          burnHighlights: true,
          statusCallback: options.statusCallback
        });
      }

    } else if (optNum === 45) {
      // Option 45: Resume + Highlighted Keywords + Company Branding
      var highlightedBlob = null;
      if (resumeExt === 'PDF') {
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        highlightedBlob = await buildTurboVectorHighlightedResume({
          resumeBlob: rawPdfBlob,
          keywords: allKeywords,
          statusCallback: options.statusCallback
        });
      } else {
        highlightedBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          keywords: allKeywords,
          burnHighlights: true,
          statusCallback: options.statusCallback
        });
      }
      if (options.statusCallback) options.statusCallback('Applying Company Branding Header (85%)...');
      pdfBlob = await applyCompanyBrandingToPdfBlob(highlightedBlob, data);

    } else if (optNum === 46) {
      // Option 46: Pre-Screened Report (Branded) + Resume with Highlighted Keywords
      if (options.statusCallback) options.statusCallback('Generating Pre-Screened Report (25%)...');
      var reportBlob = await buildRecruiterSummaryBlob(data, {});
      var highlightedBlob = null;
      if (resumeExt === 'PDF') {
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        highlightedBlob = await buildTurboVectorHighlightedResume({
          resumeBlob: rawPdfBlob,
          keywords: allKeywords,
          statusCallback: options.statusCallback
        });
      } else {
        highlightedBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          keywords: allKeywords,
          burnHighlights: true,
          statusCallback: options.statusCallback
        });
      }
      if (options.statusCallback) options.statusCallback('Merging Report & Highlighted Resume (90%)...');
      var mergedName = resumeTrackingFileName(data, 'C', 'pdf');
      var mergedBlob = await mergePreScreenedReportAndResume(reportBlob, highlightedBlob, mergedName, { returnBlob: true });
      if (options.returnBlob) return mergedBlob;
      downloadBlob(mergedBlob, mergedName);
      return true;

    } else if (optNum === 47) {
      // Option 47: Pre-Screened Report (Unbranded) + Resume with Highlighted Keywords
      if (options.statusCallback) options.statusCallback('Generating Unbranded Report (25%)...');
      var reportBlob = await buildRecruiterSummaryBlob(data, { noBranding: true });
      var highlightedBlob = null;
      if (resumeExt === 'PDF') {
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        highlightedBlob = await buildTurboVectorHighlightedResume({
          resumeBlob: rawPdfBlob,
          keywords: allKeywords,
          statusCallback: options.statusCallback
        });
      } else {
        highlightedBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          keywords: allKeywords,
          burnHighlights: true,
          statusCallback: options.statusCallback
        });
      }
      if (options.statusCallback) options.statusCallback('Merging Unbranded Report & Highlighted Resume (90%)...');
      var mergedName = resumeTrackingFileName(data, 'C', 'pdf');
      var mergedBlob = await mergePreScreenedReportAndResume(reportBlob, highlightedBlob, mergedName, { returnBlob: true });
      if (options.returnBlob) return mergedBlob;
      downloadBlob(mergedBlob, mergedName);
      return true;

    } else if (optNum === 48) {
      // Option 48: Hide Contact Details (keepName: true)
      var rules = rdcAnonymizeRules(data, { keepName: true });
      if (resumeExt === 'PDF') {
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        pdfBlob = await renderAnonymizedPdfBlob(rawPdfBlob, rules);
      } else {
        pdfBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          anonymizeRules: rules,
          statusCallback: options.statusCallback
        });
      }

    } else if (optNum === 49) {
      // Option 49: Hide Candidate Name & Contact Details (keepName: false)
      var rules = rdcAnonymizeRules(data, { keepName: false });
      if (resumeExt === 'PDF') {
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        pdfBlob = await renderAnonymizedPdfBlob(rawPdfBlob, rules);
      } else {
        pdfBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          anonymizeRules: rules,
          statusCallback: options.statusCallback
        });
      }
      outFileName = 'Jeroworks_Resume.pdf';

    } else if (optNum === 50) {
      // Option 50: Hide Contact Details + Company Branding
      var rules = rdcAnonymizeRules(data, { keepName: true });
      var contactHiddenBlob = null;
      if (resumeExt === 'PDF') {
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        contactHiddenBlob = await renderAnonymizedPdfBlob(rawPdfBlob, rules);
      } else {
        contactHiddenBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          anonymizeRules: rules,
          statusCallback: options.statusCallback
        });
      }
      if (options.statusCallback) options.statusCallback('Applying Company Branding Header (85%)...');
      pdfBlob = await applyCompanyBrandingToPdfBlob(contactHiddenBlob, data);

    } else if (optNum === 51) {
      // Option 51: Hide Contact Details + Pre-Screened Report (Branded)
      if (options.statusCallback) options.statusCallback('Generating Pre-Screened Report (25%)...');
      var reportBlob = await buildRecruiterSummaryBlob(data, {});
      var rules = rdcAnonymizeRules(data, { keepName: true });
      var contactHiddenBlob = null;
      if (resumeExt === 'PDF') {
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        contactHiddenBlob = await renderAnonymizedPdfBlob(rawPdfBlob, rules);
      } else {
        contactHiddenBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          anonymizeRules: rules,
          statusCallback: options.statusCallback
        });
      }
      if (options.statusCallback) options.statusCallback('Merging Report & Anonymized Resume (90%)...');
      var mergedName = 'Jeroworks_' + fileSafeName(data.name, 'Candidate') + '.pdf';
      var mergedBlob = await mergePreScreenedReportAndResume(reportBlob, contactHiddenBlob, mergedName, { returnBlob: true });
      if (options.returnBlob) return mergedBlob;
      downloadBlob(mergedBlob, mergedName);
      return true;

    } else if (optNum === 52) {
      // Option 52: Hide Contact Details + Pre-Screened Report without Company Branding
      if (options.statusCallback) options.statusCallback('Generating Unbranded Report (25%)...');
      var reportBlob = await buildRecruiterSummaryBlob(data, { noBranding: true });
      var rules = rdcAnonymizeRules(data, { keepName: true });
      var contactHiddenBlob = null;
      if (resumeExt === 'PDF') {
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        contactHiddenBlob = await renderAnonymizedPdfBlob(rawPdfBlob, rules);
      } else {
        contactHiddenBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          anonymizeRules: rules,
          statusCallback: options.statusCallback
        });
      }
      if (options.statusCallback) options.statusCallback('Merging Unbranded Report & Anonymized Resume (90%)...');
      var mergedName = 'Jeroworks_' + fileSafeName(data.name, 'Candidate') + '.pdf';
      var mergedBlob = await mergePreScreenedReportAndResume(reportBlob, contactHiddenBlob, mergedName, { returnBlob: true });
      if (options.returnBlob) return mergedBlob;
      downloadBlob(mergedBlob, mergedName);
      return true;

    } else if (optNum === 53) {
      // Option 53: Hide Contact Details + Pre-Screened Report + Highlighted Keywords
      if (options.statusCallback) options.statusCallback('Generating Pre-Screened Report (20%)...');
      var reportBlob = await buildRecruiterSummaryBlob(data, {});
      var rules = rdcAnonymizeRules(data, { keepName: true });
      var resumeBlob = null;
      if (resumeExt === 'PDF') {
        var rawPdfBlob = new Blob([docxBytes], { type: 'application/pdf' });
        var highlightedBlob = await buildTurboVectorHighlightedResume({
          resumeBlob: rawPdfBlob,
          keywords: allKeywords,
          statusCallback: options.statusCallback
        });
        resumeBlob = await renderAnonymizedPdfBlob(highlightedBlob, rules);
      } else {
        resumeBlob = await renderAgyStrategy2WasmDocxToPdfBlob(docxBytes, {
          anonymizeRules: rules,
          keywords: allKeywords,
          burnHighlights: true,
          statusCallback: options.statusCallback
        });
      }
      if (options.statusCallback) options.statusCallback('Merging Report & Anonymized Highlighted Resume (90%)...');
      var mergedName = 'Jeroworks_' + fileSafeName(data.name, 'Candidate') + '.pdf';
      var mergedBlob = await mergePreScreenedReportAndResume(reportBlob, resumeBlob, mergedName, { returnBlob: true });
      if (options.returnBlob) return mergedBlob;
      downloadBlob(mergedBlob, mergedName);
      return true;

    } else {
      throw new Error('Unsupported resume option ID: ' + optNum);
    }

    if (!(pdfBlob instanceof Blob)) {
      throw new Error('PDF output could not be generated for option ' + optNum);
    }

    if (options.returnBlob === true) {
      return pdfBlob;
    }

    if (options.statusCallback) options.statusCallback('Download ready (100%)...');
    downloadBlob(pdfBlob, outFileName);
    return true;
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // EXPORTED PUBLIC API
  // ═══════════════════════════════════════════════════════════════════════════
  return {
    processOption: processOption,
    renderWasmDocxToPdfBlob: renderAgyStrategy2WasmDocxToPdfBlob,
    applyCompanyBrandingToPdfBlob: applyCompanyBrandingToPdfBlob,
    buildRecruiterSummaryBlob: buildRecruiterSummaryBlob,
    mergePreScreenedReportAndResume: mergePreScreenedReportAndResume,
    copyPdfPagesToA4: copyPdfPagesToA4,
    buildTurboVectorHighlightedResume: buildTurboVectorHighlightedResume,
    renderAnonymizedPdfBlob: renderAnonymizedPdfBlob,
    rdcAnonymizeRules: rdcAnonymizeRules,
    fetchResumeBytesFromSources: fetchResumeBytesFromSources,
    fetchResumeBytes: fetchResumeBytes,
    ensureJsZip: ensureJsZip,
    ensureJsPdf: ensureJsPdf,
    ensurePdfLib: ensurePdfLib,
    ensurePdfMake: ensurePdfMake,
    ensurePdfJs: ensurePdfJs,
    ensureOpenDocLab: ensureOpenDocLab,
    downloadBlob: downloadBlob,
    clean: clean,
    fileSafeName: fileSafeName
  };
}));
