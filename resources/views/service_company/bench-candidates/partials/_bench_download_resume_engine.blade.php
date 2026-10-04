{{-- Dedicated Isolated Document Engine Exclusively for In-House Bench Talent Download Resume Center (Engine 1-B) --}}
<script>
/**
 * AgyBenchDownloadResumeEngine.js
 * 
 * EXCLUSIVE DEDICATED ENGINE FOR IN-HOUSE BENCH TALENT DOWNLOAD RESUME CENTER
 * (/service_company/bench-candidates)
 * 
 * 100% COMPLETE ISOLATION MANDATE (Rule 15 & Rule 17 Compliance):
 * - This engine is physically decoupled and isolated from CV Tracker, App Tracker, and Forward Resume engines.
 * - Operates exclusively on In-House Talent attributes (bench availability, bench skill matrix ratings,
 *   relationship manager comments, commercials, and authoritative location strings).
 * - 100% Client-Side Execution (User RAM + WASM): 0% server/DB read strain, 0% server PDF CPU/RAM strain.
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
  var rsLogoCache = {};
  var rsDownloadBytesCache = {};

  // ═══════════════════════════════════════════════════════════════════════════
  // SVG ICONS FOR IN-HOUSE EVALUATION REPORT DOSSIER (pdfMake)
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
    var ext = (cand.resume_extension || '').toLowerCase();
    var isDocx = (ext === 'docx' || ext === 'doc');

    var header = new Uint8Array(rawBytes.slice(0, 5));
    var isActuallyPdf = (header[0] === 0x25 && header[1] === 0x50 && header[2] === 0x44 && header[3] === 0x46);

    if (isActuallyPdf) {
      return new Blob([rawBytes], { type: 'application/pdf' });
    }

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

    if (typeof window.AgyForwardResumeEngine !== 'undefined' && typeof window.AgyForwardResumeEngine.docxBytesToPdfBlob === 'function') {
      try {
        var wasmPdfBlob = await window.AgyForwardResumeEngine.docxBytesToPdfBlob(rawBytes, cand.safe_name || 'Resume');
        if (wasmPdfBlob && wasmPdfBlob.size > 100) {
          return wasmPdfBlob;
        }
      } catch (wasmErr) {
        console.warn('[AgyBenchDownloadResumeEngine] Client WASM DOCX conversion error:', wasmErr);
      }
    }

    return new Blob([rawBytes], { type: 'application/pdf' });
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // IN-HOUSE EVALUATION REPORT DOSSIER BUILDER (pdfMake)
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

  function normalizeBenchCandidateData(c) {
    var rawSkills = getBenchCandidateEvaluatedSkills(c);
    var candidateRating = typeof c.rating === 'number' ? c.rating : parseFloat(c.rating) || 4;
    var candidateMatch = typeof c.match === 'number' ? c.match : (candidateRating ? Math.min(100, Math.max(40, Math.round(candidateRating * 20))) : 85);

    var cleanExp = (c.experience || '').trim();
    if (!cleanExp || cleanExp === '--') {
      var numExp = parseFloat(c.total_exp);
      cleanExp = (numExp && numExp > 0) ? numExp + ' Year' + (numExp === 1 ? '' : 's') : '--';
    }

    var cleanLoc = (c.location || c.city_display || c.city_name || c.city || '').trim();
    if (!cleanLoc || /^\d+$/.test(cleanLoc)) {
      cleanLoc = '--';
    }

    var rmComment = (c.rm_comments || c.recruiter_note || '').trim();

    return {
      id: c.id,
      name: (c.full_name || 'In-House Candidate').trim(),
      role: (c.current_designation || 'Consultant').trim(),
      experience: cleanExp,
      location: cleanLoc,
      notice: (c.notice || 'Immediate').trim(),
      commercials: (c.commercials_formatted || 'Competitive / Negotiable').trim(),
      engagementModel: (c.engagement_model || 'Contract').trim(),
      match: candidateMatch,
      reportId: c.report_id || ('JR' + new Date().toISOString().slice(0, 10).replace(/-/g, '') + String(c.id).padStart(6, '0')),
      reportDate: c.report_date || new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }),
      recruiter: (c.recruiter_name || 'Recruiter').trim(),
      recruiterNote: rmComment,
      skills: rawSkills
    };
  }

  function getBenchRatingStars(rating) {
    var stars = [];
    var r = Math.min(5, Math.max(0, Math.round(rating)));
    for (var i = 0; i < 5; i++) {
      stars.push({
        svg: i < r ? rsStarGold : rsStarMuted,
        width: 11,
        height: 11,
        margin: [0, 0, 2, 0]
      });
    }
    return { columns: stars, width: 'auto' };
  }

  function buildBenchDefinition(cand, companyName, companyLogo) {
    var c = normalizeBenchCandidateData(cand);

    // Skills table rows (up to 12 skills)
    var skillRows = [];
    skillRows.push([
      { text: 'EVALUATED SKILL / EXPERTISE', style: 'th', fillColor: '#f8fafc' },
      { text: 'VERIFIED EXPERIENCE', style: 'th', fillColor: '#f8fafc' },
      { text: 'PROFICIENCY / RATING', style: 'th', fillColor: '#f8fafc' }
    ]);

    var skillsList = c.skills.slice(0, 12);
    if (skillsList.length === 0) {
      skillsList = [{ name: c.role, expText: c.experience, rating: 4 }];
    }

    skillsList.forEach(function (s, idx) {
      var bg = idx % 2 === 0 ? '#ffffff' : '#fbfcfd';
      skillRows.push([
        { text: s.name, style: 'tdBold', fillColor: bg },
        { text: s.expText || '--', style: 'td', fillColor: bg },
        {
          columns: [
            getBenchRatingStars(s.rating),
            { text: ' (' + s.rating + '/5)', style: 'tdMuted', margin: [4, 1, 0, 0] }
          ],
          fillColor: bg
        }
      ]);
    });

    var headerColumns = [];
    if (companyLogo && companyLogo.startsWith('data:image')) {
      headerColumns.push({
        image: companyLogo,
        fit: [140, 42],
        alignment: 'left'
      });
    } else {
      headerColumns.push({
        text: (companyName || 'HIRING PARTNER').toUpperCase(),
        style: 'logoFallback'
      });
    }

    headerColumns.push({
      stack: [
        { text: 'IN-HOUSE TALENT EVALUATION REPORT', style: 'docHeaderTitle', alignment: 'right' },
        { text: 'VERIFIED BENCH CONSULTANT DOSSIER', style: 'docHeaderSub', alignment: 'right' }
      ]
    });

    var content = [
      {
        columns: headerColumns,
        margin: [0, 0, 0, 14]
      },
      {
        canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 1.5, lineColor: '#2563eb' }],
        margin: [0, 0, 0, 14]
      },
      {
        style: 'candidateCard',
        table: {
          widths: ['*'],
          body: [[
            {
              stack: [
                {
                  columns: [
                    {
                      stack: [
                        { text: c.name.toUpperCase(), style: 'candidateName' },
                        { text: c.role, style: 'candidateRole' }
                      ]
                    },
                    {
                      stack: [
                        { text: c.match + '% MATCH', style: 'matchScore', alignment: 'right' },
                        { text: 'TECHNICAL FIT', style: 'matchLabel', alignment: 'right' }
                      ],
                      width: 100
                    }
                  ],
                  margin: [0, 0, 0, 12]
                },
                {
                  canvas: [{ type: 'line', x1: 0, y1: 0, x2: 485, y2: 0, lineWidth: 0.5, lineColor: '#e2e8f0' }],
                  margin: [0, 0, 0, 10]
                },
                {
                  columns: [
                    {
                      stack: [
                        { text: 'TOTAL EXPERIENCE', style: 'metaLabel' },
                        { text: c.experience, style: 'metaValue' }
                      ]
                    },
                    {
                      stack: [
                        { text: 'LOCATION', style: 'metaLabel' },
                        { text: c.location, style: 'metaValue' }
                      ]
                    },
                    {
                      stack: [
                        { text: 'BENCH AVAILABILITY', style: 'metaLabel' },
                        { text: c.notice, style: 'metaValueHighlight' }
                      ]
                    },
                    {
                      stack: [
                        { text: 'ENGAGEMENT MODEL', style: 'metaLabel' },
                        { text: c.engagementModel, style: 'metaValue' }
                      ]
                    }
                  ],
                  margin: [0, 0, 0, 8]
                },
                {
                  columns: [
                    {
                      stack: [
                        { text: 'COMMERCIAL RATE', style: 'metaLabel' },
                        { text: c.commercials, style: 'metaValueCommercials' }
                      ]
                    },
                    {
                      stack: [
                        { text: 'DOSSIER ID', style: 'metaLabel' },
                        { text: c.reportId, style: 'metaValue' }
                      ]
                    },
                    {
                      stack: [
                        { text: 'DATE OF REPORT', style: 'metaLabel' },
                        { text: c.reportDate, style: 'metaValue' }
                      ]
                    },
                    {
                      stack: [
                        { text: 'ASSESSED BY', style: 'metaLabel' },
                        { text: c.recruiter, style: 'metaValue' }
                      ]
                    }
                  ]
                }
              ],
              fillColor: '#f8fafc',
              margin: [12, 12, 12, 12]
            }
          ]]
        },
        layout: 'noBorders',
        margin: [0, 0, 0, 14]
      },
      {
        columns: [
          { svg: rsIconSkill, width: 14, height: 14, margin: [0, 1, 6, 0] },
          { text: 'TECHNICAL SKILLS & EXPERTISE MATRIX', style: 'sectionHeader' }
        ],
        margin: [0, 0, 0, 8]
      },
      {
        table: {
          headerRows: 1,
          widths: ['*', 130, 150],
          body: skillRows
        },
        layout: {
          hLineWidth: function (i, node) { return (i === 0 || i === 1 || i === node.table.body.length) ? 0.75 : 0.5; },
          vLineWidth: function () { return 0; },
          hLineColor: function (i) { return (i === 1) ? '#cbd5e1' : '#e2e8f0'; },
          paddingLeft: function () { return 10; },
          paddingRight: function () { return 10; },
          paddingTop: function () { return 7; },
          paddingBottom: function () { return 7; }
        },
        margin: [0, 0, 0, 14]
      }
    ];

    if (c.recruiterNote && c.recruiterNote.length > 0) {
      content.push({
        columns: [
          { svg: rsIconNote, width: 14, height: 14, margin: [0, 1, 6, 0] },
          { text: 'RELATIONSHIP MANAGER ASSESSMENT & HIGHLIGHTS', style: 'sectionHeader' }
        ],
        margin: [0, 0, 0, 8]
      });
      content.push({
        table: {
          widths: ['*'],
          body: [[
            {
              text: c.recruiterNote,
              style: 'noteText',
              fillColor: '#f1f5f9',
              margin: [12, 10, 12, 10]
            }
          ]]
        },
        layout: 'noBorders',
        margin: [0, 0, 0, 14]
      });
    }

    content.push({
      columns: [
        { text: 'STRICTLY CONFIDENTIAL — PRE-SCREEN EVALUATION DOSSIER', style: 'footerText' },
        { text: (companyName || 'HIRING PARTNER') + ' • IN-HOUSE BENCH TALENT', style: 'footerText', alignment: 'right' }
      ],
      margin: [0, 10, 0, 0]
    });

    return {
      pageSize: 'A4',
      pageMargins: [40, 36, 40, 36],
      content: content,
      styles: {
        logoFallback: { fontSize: 13, bold: true, color: '#1e293b' },
        docHeaderTitle: { fontSize: 11, bold: true, color: '#0f172a' },
        docHeaderSub: { fontSize: 8, color: '#64748b', bold: true },
        candidateName: { fontSize: 16, bold: true, color: '#0f172a' },
        candidateRole: { fontSize: 10, color: '#2563eb', bold: true, margin: [0, 2, 0, 0] },
        matchScore: { fontSize: 14, bold: true, color: '#15803d' },
        matchLabel: { fontSize: 7, color: '#15803d', bold: true },
        metaLabel: { fontSize: 7, color: '#64748b', bold: true, margin: [0, 0, 0, 2] },
        metaValue: { fontSize: 8.5, bold: true, color: '#1e293b' },
        metaValueHighlight: { fontSize: 8.5, bold: true, color: '#16a34a' },
        metaValueCommercials: { fontSize: 8.5, bold: true, color: '#2563eb' },
        sectionHeader: { fontSize: 9.5, bold: true, color: '#0f172a' },
        th: { fontSize: 7.5, bold: true, color: '#475569' },
        td: { fontSize: 8, color: '#334155' },
        tdBold: { fontSize: 8, bold: true, color: '#0f172a' },
        tdMuted: { fontSize: 7.5, color: '#64748b' },
        noteText: { fontSize: 8.5, color: '#1e293b', lineHeight: 1.35 },
        footerText: { fontSize: 7, color: '#94a3b8' }
      },
      defaultStyle: { font: 'Roboto' }
    };
  }

  async function generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, isUnbranded) {
    var pdfMake = await ensurePdfMake();
    var cLogo = isUnbranded ? null : companyLogo;
    var cName = isUnbranded ? '' : companyName;
    var docDef = buildBenchDefinition(cand, cName, cLogo);
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
  // VECTOR COMPANY BRANDING INJECTION (PDF-Lib)
  // ═══════════════════════════════════════════════════════════════════════════
  async function addCompanyBrandingHeader(resumeBlob, companyName, companyLogo) {
    var PDFLib = await ensurePdfLib();
    var existingPdfBytes = await resumeBlob.arrayBuffer();
    var pdfDoc = await PDFLib.PDFDocument.load(existingPdfBytes, { ignoreEncryption: true });
    var pages = pdfDoc.getPages();
    if (!pages || pages.length === 0) return resumeBlob;

    var firstPage = pages[0];
    var pageW = firstPage.getWidth();
    var pageH = firstPage.getHeight();

    var bannerH = 46;
    var rgb = PDFLib.rgb;

    firstPage.drawRectangle({
      x: 0,
      y: pageH - bannerH,
      width: pageW,
      height: bannerH,
      color: rgb(0.97, 0.98, 0.99),
      borderColor: rgb(0.85, 0.88, 0.92),
      borderWidth: 0.75
    });

    var logoImage = null;
    if (companyLogo && companyLogo.startsWith('data:image')) {
      try {
        if (rsLogoCache[companyLogo]) {
          logoImage = rsLogoCache[companyLogo];
        } else {
          var isJpg = companyLogo.indexOf('data:image/jpeg') === 0 || companyLogo.indexOf('data:image/jpg') === 0;
          var b64Data = companyLogo.split(',')[1];
          var binStr = atob(b64Data);
          var len = binStr.length;
          var bytes = new Uint8Array(len);
          for (var i = 0; i < len; i++) {
            bytes[i] = binStr.charCodeAt(i);
          }
          logoImage = isJpg ? await pdfDoc.embedJpg(bytes) : await pdfDoc.embedPng(bytes);
          rsLogoCache[companyLogo] = logoImage;
        }
      } catch (e) {
        console.warn('[AgyBenchDownloadResumeEngine] Failed to embed branding logo:', e);
      }
    }

    if (logoImage) {
      try {
        var dims = logoImage.scale(1);
        var targetH = 26;
        var scale = targetH / dims.height;
        var drawW = Math.min(dims.width * scale, 120);
        var drawH = dims.height * (drawW / dims.width);
        firstPage.drawImage(logoImage, {
          x: 24,
          y: pageH - bannerH + (bannerH - drawH) / 2,
          width: drawW,
          height: drawH
        });
      } catch (imgErr) {
        console.warn('[AgyBenchDownloadResumeEngine] Error drawing logo image:', imgErr);
      }
    } else {
      var font = await pdfDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);
      var brandText = (companyName || 'HIRING PARTNER').toUpperCase();
      firstPage.drawText(brandText, {
        x: 24,
        y: pageH - bannerH + 18,
        size: 11,
        font: font,
        color: rgb(0.1, 0.15, 0.25)
      });
    }

    var fontSub = await pdfDoc.embedFont(PDFLib.StandardFonts.Helvetica);
    var subText = 'VERIFIED IN-HOUSE TALENT • PRESENTED BY ' + (companyName || 'HIRING PARTNER').toUpperCase();
    firstPage.drawText(subText, {
      x: pageW - 24 - (subText.length * 5.2),
      y: pageH - bannerH + 18,
      size: 7.5,
      font: fontSub,
      color: rgb(0.4, 0.45, 0.55)
    });

    var modifiedPdfBytes = await pdfDoc.save();
    return new Blob([modifiedPdfBytes], { type: 'application/pdf' });
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // TURBO VECTOR KEYWORD HIGHLIGHTING (PDF.js + PDF-Lib)
  // ═══════════════════════════════════════════════════════════════════════════
  async function buildTurboVectorHighlightedResume(params) {
    var resumeBlob = params.resumeBlob;
    var keywords = params.keywords || [];
    if (!keywords || keywords.length === 0) return resumeBlob;

    var pdfjs = await ensurePdfJs();
    var PDFLib = await ensurePdfLib();

    var pdfBytes = await resumeBlob.arrayBuffer();
    var loadingTask = pdfjs.getDocument({ data: pdfBytes.slice(0) });
    var pdfJsDoc = await loadingTask.promise;
    var numPages = pdfJsDoc.numPages;

    var kwMap = {};
    keywords.forEach(function (k) {
      var clean = (k || '').trim().toLowerCase();
      if (clean.length > 1) kwMap[clean] = true;
    });

    var pageHighlights = [];

    for (var p = 1; p <= numPages; p++) {
      var page = await pdfJsDoc.getPage(p);
      var textContent = await page.getTextContent();
      var viewport = page.getViewport({ scale: 1.0 });
      var items = textContent.items || [];
      var pageHlBoxes = [];

      for (var i = 0; i < items.length; i++) {
        var it = items[i];
        var str = (it.str || '').toLowerCase();
        if (!str.trim()) continue;

        for (var kw in kwMap) {
          if (str.indexOf(kw) !== -1) {
            var tx = it.transform;
            if (tx && tx.length >= 6) {
              var x = tx[4];
              var y = tx[5];
              var w = it.width || 40;
              var h = it.height || 10;
              pageHlBoxes.push({ x: x - 1, y: y - 2, width: w + 2, height: h + 3 });
            }
          }
        }
      }
      pageHighlights.push({ pageNum: p, boxes: pageHlBoxes });
    }

    var pdfDoc = await PDFLib.PDFDocument.load(pdfBytes, { ignoreEncryption: true });
    var libPages = pdfDoc.getPages();

    pageHighlights.forEach(function (ph) {
      if (ph.pageNum <= libPages.length) {
        var lp = libPages[ph.pageNum - 1];
        ph.boxes.forEach(function (box) {
          lp.drawRectangle({
            x: box.x,
            y: box.y,
            width: box.width,
            height: box.height,
            color: PDFLib.rgb(1.0, 0.94, 0.4),
            opacity: 0.35
          });
        });
      }
    });

    var resultBytes = await pdfDoc.save();
    return new Blob([resultBytes], { type: 'application/pdf' });
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // PII REDACTION & PRIVACY MASKING (Canvas + jsPDF)
  // ═══════════════════════════════════════════════════════════════════════════
  var rdcAnonymizeRules = {
    phone: [
      /(?:\+?\d{1,3}[-.\s]?)?\(?\d{2,4}\)?[-.\s]?\d{3,4}[-.\s]?\d{3,5}/g,
      /\b\d{10,12}\b/g
    ],
    email: [
      /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/g
    ],
    url: [
      /https?:\/\/[^\s$.?#].[^\s]*/gi,
      /www\.[^\s$.?#].[^\s]*/gi,
      /linkedin\.com\/[^\s]*/gi,
      /github\.com\/[^\s]*/gi
    ]
  };

  async function renderAnonymizedPdfBlob(resumeBlob, customRules) {
    var pdfjs = await ensurePdfJs();
    var jsPDFClass = await ensureJsPdf();
    var PDFLib = await ensurePdfLib();

    var pdfBytes = await resumeBlob.arrayBuffer();
    var loadingTask = pdfjs.getDocument({ data: pdfBytes.slice(0) });
    var pdfDoc = await loadingTask.promise;
    var numPages = pdfDoc.numPages;

    var targetRules = customRules || rdcAnonymizeRules;
    var outPdf = null;

    var scale = 2.0;

    for (var pageNum = 1; pageNum <= numPages; pageNum++) {
      var page = await pdfDoc.getPage(pageNum);
      var viewport = page.getViewport({ scale: scale });
      var textContent = await page.getTextContent();

      var canvas = document.createElement('canvas');
      canvas.width = viewport.width;
      canvas.height = viewport.height;
      var ctx = canvas.getContext('2d');

      var renderContext = {
        canvasContext: ctx,
        viewport: viewport
      };
      await page.render(renderContext).promise;

      var items = textContent.items || [];
      var redactBoxes = [];

      items.forEach(function (item) {
        var str = item.str;
        if (!str) return;

        var isMatch = false;
        for (var cat in targetRules) {
          var regexList = targetRules[cat];
          for (var r = 0; r < regexList.length; r++) {
            if (regexList[r].test(str)) {
              isMatch = true;
              break;
            }
          }
          if (isMatch) break;
        }

        if (isMatch && item.transform) {
          var tx = pdfjs.Util.transform(viewport.transform, item.transform);
          var x = tx[4];
          var y = tx[5];
          var w = item.width * scale;
          var h = (item.height || 12) * scale;
          redactBoxes.push({ x: x - 2, y: y - h - 1, w: w + 4, h: h + 4 });
        }
      });

      redactBoxes.forEach(function (b) {
        ctx.fillStyle = '#0f172a';
        ctx.fillRect(b.x, b.y, b.w, b.h);
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold ' + Math.max(9, Math.round(b.h * 0.55)) + 'px sans-serif';
        ctx.fillText('[CONFIDENTIAL]', b.x + 4, b.y + b.h - 4);
      });

      var imgData = canvas.toDataURL('image/jpeg', 0.92);
      var pdfW = (viewport.width / scale) * 0.75;
      var pdfH = (viewport.height / scale) * 0.75;

      if (pageNum === 1) {
        var JsPdfCtor = jsPDFClass.jsPDF || jsPDFClass;
        outPdf = new JsPdfCtor({
          orientation: pdfW > pdfH ? 'landscape' : 'portrait',
          unit: 'pt',
          format: [pdfW, pdfH]
        });
        outPdf.addImage(imgData, 'JPEG', 0, 0, pdfW, pdfH);
      } else {
        outPdf.addPage([pdfW, pdfH], pdfW > pdfH ? 'landscape' : 'portrait');
        outPdf.addImage(imgData, 'JPEG', 0, 0, pdfW, pdfH);
      }
    }

    if (!outPdf) return resumeBlob;
    return outPdf.output('blob');
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // MULTI-PAGE MERGING (PDF-Lib)
  // ═══════════════════════════════════════════════════════════════════════════
  async function mergeDossierAndResume(dossierBlob, resumeBlob) {
    var PDFLib = await ensurePdfLib();
    var mergedPdf = await PDFLib.PDFDocument.create();

    var dBytes = await dossierBlob.arrayBuffer();
    var dDoc = await PDFLib.PDFDocument.load(dBytes, { ignoreEncryption: true });
    var dPages = await mergedPdf.copyPages(dDoc, dDoc.getPageIndices());
    dPages.forEach(function (page) { mergedPdf.addPage(page); });

    var rBytes = await resumeBlob.arrayBuffer();
    var rDoc = await PDFLib.PDFDocument.load(rBytes, { ignoreEncryption: true });
    var rPages = await mergedPdf.copyPages(rDoc, rDoc.getPageIndices());
    rPages.forEach(function (page) { mergedPdf.addPage(page); });

    var mergedBytes = await mergedPdf.save();
    return new Blob([mergedBytes], { type: 'application/pdf' });
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // MASTER COMPILATION CONTROLLER (15 FORMATS: 1, 5, 41-54)
  // ═══════════════════════════════════════════════════════════════════════════
  async function processDownloadOption(cand, optId, companyData, onProgress) {
    var formatId = parseInt(optId, 10);
    var companyName = (companyData && companyData.companyName) || 'Hiring Partner';
    var companyLogo = (companyData && companyData.companyLogo) || '';
    var safeName = cand.safe_name || (cand.full_name ? cand.full_name.replace(/\s+/g, '_').replace(/[^\w.-]/g, '') : ('Candidate_' + cand.id));

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
      origBlob.filename = 'Jeroworks_' + safeName + '_Original.' + origExt;
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
      dossierBlob.filename = 'Jeroworks_' + safeName + '_Evaluation_Report.pdf';
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
      var branded = await addCompanyBrandingHeader(basePdfBlob, companyName, companyLogo);
      branded.filename = 'Jeroworks_' + safeName + '_Branded.pdf';
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
      var bResume42 = await addCompanyBrandingHeader(basePdfBlob, companyName, companyLogo);
      notify(85, 'Merging dossier and branded resume...', 'Concatenating recruiter package...');
      var pkg42 = await mergeDossierAndResume(repBlob42, bResume42);
      pkg42.filename = 'Jeroworks_' + safeName + '_Recruiter_Package.pdf';
      notify(100, 'Recruiter package ready!', 'Document ready for download');
      return pkg42;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 43: Recruiter Package (Unbranded) (Dossier + Original Resume)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 43) {
      notify(50, 'Generating unbranded evaluation report...', 'Compiling neutral dossier...');
      var repBlob43 = await generateBenchPrescreenPdfBlob(cand, '', null, true);
      notify(80, 'Merging unbranded dossier and resume...', 'Concatenating neutral recruiter package...');
      var pkg43 = await mergeDossierAndResume(repBlob43, basePdfBlob);
      pkg43.filename = 'Jeroworks_' + safeName + '_Recruiter_Package_Unbranded.pdf';
      notify(100, 'Unbranded package ready!', 'Document ready for download');
      return pkg43;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 44: Resume + Highlighted Keywords
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 44) {
      notify(55, 'Locating evaluated skills in resume text...', 'Extracting vector coordinates for skill keywords...');
      var skills44 = getBenchCandidateEvaluatedSkills(cand).map(function(s) { return s.name; });
      var hlResume44 = await buildTurboVectorHighlightedResume({ resumeBlob: basePdfBlob, keywords: skills44 });
      hlResume44.filename = 'Jeroworks_' + safeName + '_Highlighted.pdf';
      notify(100, 'Highlighted resume ready!', 'Document ready for download');
      return hlResume44;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 45: Resume + Highlighted Keywords + Company Branding
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 45) {
      notify(50, 'Highlighting evaluated skills in resume...', 'Applying vector highlights to keywords...');
      var skills45 = getBenchCandidateEvaluatedSkills(cand).map(function(s) { return s.name; });
      var hlResume45 = await buildTurboVectorHighlightedResume({ resumeBlob: basePdfBlob, keywords: skills45 });
      notify(75, 'Stamping company branding...', 'Injecting header banner...');
      var bHlResume45 = await addCompanyBrandingHeader(hlResume45, companyName, companyLogo);
      bHlResume45.filename = 'Jeroworks_' + safeName + '_Highlighted_Branded.pdf';
      notify(100, 'Branded highlighted resume ready!', 'Document ready for download');
      return bHlResume45;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 46 (DEFAULT ★): In-House Evaluation Report + Resume + Highlighted Keywords + Company Branding
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 46) {
      notify(40, 'Generating in-house evaluation report...', 'Compiling technical evaluation dossier with verified skills...');
      var repBlob46 = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);
      notify(60, 'Highlighting evaluated skills on resume...', 'Extracting vector bounding boxes for rated skills...');
      var skills46 = getBenchCandidateEvaluatedSkills(cand).map(function(s) { return s.name; });
      var hlResume46 = await buildTurboVectorHighlightedResume({ resumeBlob: basePdfBlob, keywords: skills46 });
      notify(80, 'Stamping company branding banner...', 'Injecting corporate branding...');
      var bHlResume46 = await addCompanyBrandingHeader(hlResume46, companyName, companyLogo);
      notify(92, 'Merging dossier and highlighted resume...', 'Assembling recruiter presentation package...');
      var pkg46 = await mergeDossierAndResume(repBlob46, bHlResume46);
      pkg46.filename = 'Jeroworks_' + safeName + '_Recruiter_Package.pdf';
      notify(100, 'Executive presentation package ready!', 'Document ready for download');
      return pkg46;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 47: Recruiter Package + Highlighted Keywords (Unbranded)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 47) {
      notify(45, 'Generating unbranded evaluation report...', 'Compiling neutral dossier...');
      var repBlob47 = await generateBenchPrescreenPdfBlob(cand, '', null, true);
      notify(65, 'Highlighting evaluated skills on resume...', 'Applying vector highlights to rated skills...');
      var skills47 = getBenchCandidateEvaluatedSkills(cand).map(function(s) { return s.name; });
      var hlResume47 = await buildTurboVectorHighlightedResume({ resumeBlob: basePdfBlob, keywords: skills47 });
      notify(85, 'Merging unbranded dossier and highlighted resume...', 'Concatenating recruiter package...');
      var pkg47 = await mergeDossierAndResume(repBlob47, hlResume47);
      pkg47.filename = 'Jeroworks_' + safeName + '_Package_Highlighted_Unbranded.pdf';
      notify(100, 'Unbranded highlighted package ready!', 'Document ready for download');
      return pkg47;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 48: Hide Contact Details (Redacts phone, email, secondary mobile, links)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 48) {
      notify(60, 'Redacting contact information...', 'Flattening canvas and masking telephone, email, and social profiles...');
      var red48 = await renderAnonymizedPdfBlob(basePdfBlob, rdcAnonymizeRules);
      red48.filename = 'Jeroworks_' + safeName + '_Redacted.pdf';
      notify(100, 'Redacted resume ready!', 'Document ready for download');
      return red48;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 49: Hide Candidate Name & Contact Details (Anti-poaching blind profile)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 49) {
      notify(60, 'Generating confidential blind profile...', 'Masking candidate name and personal contact channels...');
      var rules49 = Object.assign({}, rdcAnonymizeRules);
      if (cand.full_name && cand.full_name.trim().length > 2) {
        var nameParts = cand.full_name.trim().split(/\s+/);
        rules49.name = [new RegExp('\\b' + nameParts.join('\\s+') + '\\b', 'gi')];
      }
      var red49 = await renderAnonymizedPdfBlob(basePdfBlob, rules49);
      red49.filename = 'Jeroworks_Confidential_Blind_Profile.pdf';
      notify(100, 'Confidential blind profile ready!', 'Document ready for download');
      return red49;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 50: Hide Contact Details + Company Branding
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 50) {
      notify(50, 'Redacting contact channels on canvas...', 'Masking private communication channels...');
      var red50 = await renderAnonymizedPdfBlob(basePdfBlob, rdcAnonymizeRules);
      notify(78, 'Stamping company branding...', 'Injecting header with company logo and metadata...');
      var bRed50 = await addCompanyBrandingHeader(red50, companyName, companyLogo);
      bRed50.filename = 'Jeroworks_' + safeName + '_Redacted_Branded.pdf';
      notify(100, 'Branded redacted resume ready!', 'Document ready for download');
      return bRed50;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 51: Hide Contact Details + In-House Evaluation Report + Company Branding
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 51) {
      notify(40, 'Generating in-house evaluation report...', 'Compiling technical evaluation dossier...');
      var repBlob51 = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);
      notify(60, 'Redacting contact details on resume...', 'Masking phone, email, and URLs on high-resolution canvas...');
      var red51 = await renderAnonymizedPdfBlob(basePdfBlob, rdcAnonymizeRules);
      notify(80, 'Stamping company branding banner...', 'Injecting corporate header banner...');
      var bRed51 = await addCompanyBrandingHeader(red51, companyName, companyLogo);
      notify(92, 'Merging evaluation report and confidential resume...', 'Assembling recruiter package...');
      var pkg51 = await mergeDossierAndResume(repBlob51, bRed51);
      pkg51.filename = 'Jeroworks_' + safeName + '_Package_Redacted_Branded.pdf';
      notify(100, 'Branded confidential package ready!', 'Document ready for download');
      return pkg51;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 52: Hide Contact Details + In-House Evaluation Report (Unbranded)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 52) {
      notify(45, 'Generating unbranded evaluation report...', 'Compiling neutral dossier...');
      var repBlob52 = await generateBenchPrescreenPdfBlob(cand, '', null, true);
      notify(70, 'Redacting contact details on resume...', 'Masking phone, email, and URLs on canvas...');
      var red52 = await renderAnonymizedPdfBlob(basePdfBlob, rdcAnonymizeRules);
      notify(88, 'Merging unbranded dossier and confidential resume...', 'Concatenating neutral package...');
      var pkg52 = await mergeDossierAndResume(repBlob52, red52);
      pkg52.filename = 'Jeroworks_' + safeName + '_Package_Redacted_Unbranded.pdf';
      notify(100, 'Unbranded confidential package ready!', 'Document ready for download');
      return pkg52;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 53: Full Privacy: Hide Contact + Dossier + Keywords + Company Branding
    // (Strict Sequence: Highlight Vector Keywords -> Redact Canvas PII -> Brand -> Merge Dossier)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 53) {
      notify(30, 'Generating in-house evaluation report...', 'Building technical dossier with verified skills...');
      var repBlob53 = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);

      var skills53 = getBenchCandidateEvaluatedSkills(cand).map(function(s) { return s.name; });
      var hlResume53 = basePdfBlob;
      if (skills53.length > 0) {
        notify(50, 'Highlighting evaluated skills...', 'Applying vector highlights to rated skills...');
        try {
          hlResume53 = await buildTurboVectorHighlightedResume({ resumeBlob: basePdfBlob, keywords: skills53 });
        } catch (eHl53) {
          console.warn('[AgyBenchDownloadResumeEngine] Option 53 highlight fallback:', eHl53);
          hlResume53 = basePdfBlob;
        }
      }

      notify(70, 'Applying privacy redactions to highlighted resume...', 'Redacting contact numbers, email, and private links...');
      var red53 = await renderAnonymizedPdfBlob(hlResume53, rdcAnonymizeRules);

      notify(85, 'Stamping company branding...', 'Injecting header with company logo and metadata...');
      var bRed53 = await addCompanyBrandingHeader(red53, companyName, companyLogo);

      notify(94, 'Merging evaluation dossier and full privacy resume...', 'Assembling recruiter package...');
      var pkg53 = await mergeDossierAndResume(repBlob53, bRed53);
      pkg53.filename = 'Jeroworks_' + safeName + '_Full_Privacy_Package.pdf';
      notify(100, 'Full privacy package ready!', 'Document ready for download');
      return pkg53;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OPTION 54: Full Privacy (Unbranded): Hide Contact + Dossier + Keywords (No Logo)
    // (Strict Sequence: Highlight Vector Keywords -> Redact Canvas PII -> Merge Dossier)
    // ─────────────────────────────────────────────────────────────────────────
    if (formatId === 54) {
      notify(30, 'Generating unbranded evaluation report...', 'Compiling neutral dossier...');
      var repBlob54 = await generateBenchPrescreenPdfBlob(cand, '', null, true);

      var skills54 = getBenchCandidateEvaluatedSkills(cand).map(function(s) { return s.name; });
      var hlResume54 = basePdfBlob;
      if (skills54.length > 0) {
        notify(50, 'Highlighting evaluated skills...', 'Applying vector highlights to rated skills...');
        try {
          hlResume54 = await buildTurboVectorHighlightedResume({ resumeBlob: basePdfBlob, keywords: skills54 });
        } catch (eHl54) {
          console.warn('[AgyBenchDownloadResumeEngine] Option 54 highlight fallback:', eHl54);
          hlResume54 = basePdfBlob;
        }
      }

      notify(75, 'Applying privacy redactions to highlighted resume...', 'Redacting contact numbers, email, and private links...');
      var red54 = await renderAnonymizedPdfBlob(hlResume54, rdcAnonymizeRules);

      notify(90, 'Merging unbranded dossier and confidential resume...', 'Concatenating multi-page recruiter package...');
      var pkg54 = await mergeDossierAndResume(repBlob54, red54);
      pkg54.filename = 'Jeroworks_' + safeName + '_Full_Privacy_Unbranded.pdf';
      notify(100, 'Full privacy package (unbranded) ready!', 'Document ready for download');
      return pkg54;
    }

    // Default Fallback
    notify(50, 'Compiling document...', 'Generating default package...');
    var defaultPkg = await mergeDossierAndResume(
      await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false),
      basePdfBlob
    );
    defaultPkg.filename = 'Jeroworks_' + safeName + '_Package.pdf';
    notify(100, 'Document ready!', 'Document ready for download');
    return defaultPkg;
  }

  // ═══════════════════════════════════════════════════════════════════════════
  // DIRECT BROWSER DOWNLOAD DISPATCH (Native URL.createObjectURL)
  // ═══════════════════════════════════════════════════════════════════════════
  async function downloadCandidateFormat(cand, optId, companyData, onProgress) {
    var compiled = await processDownloadOption(cand, optId, companyData, onProgress);
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
    mergeDossierAndResume: mergeDossierAndResume
  };
}));
</script>
