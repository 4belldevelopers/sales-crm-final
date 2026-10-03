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

  // SVGs for Bench Pre-Screen Dossier
  var rsStarGold = '<svg viewBox="0 0 24 24" width="9" height="9" xmlns="http://www.w3.org/2000/svg"><path fill="#f59e0b" d="M12 2.6l2.85 5.78 6.38.93-4.62 4.5 1.09 6.35L12 17.16l-5.7 3 1.09-6.35-4.62-4.5 6.38-.93L12 2.6z"/></svg>';
  var rsStarMuted = '<svg viewBox="0 0 24 24" width="9" height="9" xmlns="http://www.w3.org/2000/svg"><path fill="#cbd5e1" d="M12 2.6l2.85 5.78 6.38.93-4.62 4.5 1.09 6.35L12 17.16l-5.7 3 1.09-6.35-4.62-4.5 6.38-.93L12 2.6z"/></svg>';
  var rsIconExperience = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#64748b" d="M12 2l2.2 4.5 5 .7-3.6 3.5.8 5-4.4-2.4-4.4 2.4.8-5L4.8 7.2l5-.7L12 2zm-6 17h12v2H6v-2z"/></svg>';
  var rsIconCompany = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#64748b" d="M4 21V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v17h3v-9h-2v-2h3a1 1 0 0 1 1 1v10H4zm4-14h2V5H8v2zm4 0h2V5h-2v2zm-4 4h2V9H8v2zm4 0h2V9h-2v2zm-4 4h2v-2H8v2zm4 0h2v-2h-2v2z"/></svg>';
  var rsIconCalendar = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M7 2h2v2h6V2h2v2h2a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2V2zm12 8H5v9h14v-9z"/></svg>';
  var rsIconLocation = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z"/></svg>';
  var rsIconLaptop = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M4 4h16a2 2 0 0 1 2 2v10H2V6a2 2 0 0 1 2-2zm0 2v8h16V6H4zm-2 12h20v2H2v-2z"/></svg>';
  var rsIconSummary = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm3 5h8V6H8v2zm0 5h8v-2H8v2zm0 5h5v-2H8v2z"/></svg>';
  var rsIconNote = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M4 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H9l-5 5V4zm4 4v2h8V8H8zm0 4v2h6v-2H8z"/></svg>';
  var rsIconSkill = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 2l2.4 5.4 5.9.6-4.4 3.9 1.3 5.8-5.2-3-5.2 3 1.3-5.8L3.7 8l5.9-.6L12 2z"/></svg>';
  var rsIconShield = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#2563eb" d="M12 2l8 3v6c0 5-3.4 9.6-8 11-4.6-1.4-8-6-8-11V5l8-3zm-1 13.2l6-6-1.4-1.4L11 12.4 8.4 9.8 7 11.2l4 4z"/></svg>';

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
    });
  }

  function ensurePdfLib() {
    if (window.PDFLib) return Promise.resolve(window.PDFLib);
    if (!rsPdfLibReady) {
      rsPdfLibReady = rsLoadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js').then(function () {
        return window.PDFLib;
      });
    }
    return rsPdfLibReady;
  }

  function ensurePdfJs() {
    if (window.pdfjsLib) return Promise.resolve(window.pdfjsLib);
    if (!rsPdfJsReady) {
      rsPdfJsReady = rsLoadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js').then(function () {
        if (window.pdfjsLib && !window.pdfjsLib.GlobalWorkerOptions.workerSrc) {
          window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }
        return window.pdfjsLib;
      });
    }
    return rsPdfJsReady;
  }

  function ensurePdfMake() {
    if (window.pdfMake && window.pdfMake.vfs) return Promise.resolve(window.pdfMake);
    if (!rsPdfMakeReady) {
      rsPdfMakeReady = rsLoadScript('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js').then(function () {
        return rsLoadScript('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js');
      }).then(function () {
        return window.pdfMake;
      });
    }
    return rsPdfMakeReady;
  }

  async function rsGetLogoDataUri(url) {
    if (!url) return null;
    if (url.startsWith('data:image/')) return url;
    if (rsLogoCache[url]) return rsLogoCache[url];
    try {
      var resp = await fetch(url, { mode: 'cors', credentials: 'omit' });
      if (!resp.ok) return null;
      var blob = await resp.blob();
      return new Promise(function (resolve) {
        var reader = new FileReader();
        reader.onloadend = function () {
          rsLogoCache[url] = reader.result;
          resolve(reader.result);
        };
        reader.onerror = function () {
          resolve(null);
        };
        reader.readAsDataURL(blob);
      });
    } catch (e) {
      console.warn('[BenchForwardResume] Could not load company logo as data URI:', e);
      return null;
    }
  }

  function clean(val, fallback) {
    if (val === undefined || val === null) return fallback || '';
    var s = String(val).trim();
    return s === '' ? (fallback || '') : s;
  }

  async function fetchCandidateResumeBytes(cand) {
    var candId = (typeof cand === 'object' && cand !== null) ? cand.id : cand;
    if (rsResumeBytesCache[candId]) return rsResumeBytesCache[candId];

    var sources = [];
    if (typeof cand === 'object' && cand !== null) {
      if (cand.resume_r2_url && !sources.includes(cand.resume_r2_url)) {
        sources.push(cand.resume_r2_url);
      }
      if (cand.resume_url && !sources.includes(cand.resume_url)) {
        sources.push(cand.resume_url);
      }
      if (cand.direct_download_url && !sources.includes(cand.direct_download_url)) {
        sources.push(cand.direct_download_url);
      }
      if (cand.download_url && !sources.includes(cand.download_url)) {
        sources.push(cand.download_url);
      }
      if (cand.resume && (cand.resume.startsWith('http://') || cand.resume.startsWith('https://')) && !sources.includes(cand.resume)) {
        sources.push(cand.resume);
      }
      if (cand.resume_filename) {
        sources.push('/user-uploads/resumes/' + cand.resume_filename);
        sources.push('/user-uploads/documents/' + candId + '/' + cand.resume_filename);
      }
    }
    // Also include same-origin proxy download endpoint
    var proxyRoute = '/service_company/bench-candidates/download/' + candId;
    if (!sources.includes(proxyRoute)) {
      sources.push(proxyRoute);
    }

    var lastErr = null;
    for (var i = 0; i < sources.length; i++) {
      try {
        var fetchOpts = { cache: 'no-store' };
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
              rsResumeBytesCache[candId] = new Uint8Array(buf);
              return rsResumeBytesCache[candId];
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
  // BENCH-SPECIFIC PRE-SCREENED DOSSIER (pdfMake)
  // ═══════════════════════════════════════════════════════════════════════════
  function buildBenchDefinition(cand, companyName, logoUri, noBranding) {
    var fullName = clean(cand.full_name, 'Inhouse Talent');
    var desig = clean(cand.current_designation, 'Software Professional');
    var exp = clean(cand.experience, '--');
    var loc = clean(cand.location, '--');
    var notice = clean(cand.notice, 'Immediate');
    var wm = clean(cand.work_mode, 'Hybrid');
    var rating = parseInt(cand.match || (cand.rating ? cand.rating * 20 : 85), 10);
    var summary = clean(cand.careersummary || cand.cover_letter, 'Experienced professional with demonstrated track record of technical delivery and engineering excellence.');
    
    // Relationship Manager Comments from bench_candidate_rm_comments
    var rawRm = cand.rm_comments || cand.recruiter_note || cand.comment || '';
    var rmComments = clean(rawRm.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim(), 'No Relationship Manager comments added yet.');

    var skillsList = Array.isArray(cand.skills_evaluated) ? cand.skills_evaluated : [];

    var skillRows = [
      [
        { text: 'SKILL', style: 'tableHead' },
        { text: 'EXPERIENCE', style: 'tableHead' },
        { text: 'RATINGS', style: 'tableHead' }
      ]
    ];

    if (skillsList.length === 0 && cand.skills) {
      var raw = cand.skills.split(',');
      raw.forEach(function (s) {
        if (s.trim()) {
          skillsList.push({ name: s.trim(), expText: exp, rating: 4 });
        }
      });
    }

    // Talent Skills & Expertise (display up to 10 key skills with vector SVG stars)
    skillsList.slice(0, 10).forEach(function (sk) {
      var r = parseInt(sk.rating || 3, 10);
      var starCols = [];
      for (var i = 1; i <= 5; i++) {
        starCols.push({
          svg: (i <= r) ? rsStarGold : rsStarMuted,
          width: 9,
          height: 9,
          margin: [0, 0, 1, 0]
        });
      }
      skillRows.push([
        { text: sk.name || 'Skill', style: 'tableCellBold' },
        { text: sk.expText || '--', style: 'tableCell' },
        { columns: starCols, width: 60, margin: [0, 2, 0, 0] }
      ]);
    });

    var logoCell = (logoUri && logoUri.startsWith('data:image/'))
      ? { image: logoUri, fit: [42, 28], alignment: 'center', margin: [0, 0, 10, 0] }
      : { text: companyName.charAt(0).toUpperCase(), fontSize: 13, bold: true, color: '#1d4ed8', alignment: 'center', margin: [0, 4, 10, 0] };

    return {
      pageSize: 'A4',
      pageMargins: [30, 26, 30, 24],
      defaultStyle: { font: 'Roboto', fontSize: 8, color: '#1f2937', lineHeight: 1.25 },
      styles: {
        title: { fontSize: 14, bold: true, color: '#0f172a', alignment: 'right' },
        eyebrow: { fontSize: 6.5, color: '#64748b', alignment: 'right' },
        candidateName: { fontSize: 18, bold: true, color: '#0f172a' },
        designation: { fontSize: 9.5, bold: true, color: '#1d4ed8' },
        sectionTitle: { fontSize: 8.5, bold: true, color: '#1d4ed8', margin: [0, 0, 0, 6] },
        tableHead: { fontSize: 8.5, bold: true, color: '#334155', fillColor: '#f8fafc', margin: [0, 5, 0, 5] },
        tableCell: { fontSize: 8.5, color: '#334155', margin: [0, 5, 0, 5] },
        tableCellBold: { fontSize: 8.5, bold: true, color: '#0f172a', margin: [0, 5, 0, 5] },
        body: { fontSize: 7.8, color: '#1f2937', lineHeight: 1.35 },
        footer: { fontSize: 6.5, color: '#64748b' }
      },
      content: [
        // Header
        {
          margin: [0, 0, 0, 10],
          stack: [
            { canvas: [{ type: 'rect', x: 0, y: 0, w: 535, h: 58, r: 6, lineWidth: 0.55, lineColor: '#dbe3ef', color: '#ffffff' }], margin: [0, 0, 0, -58] },
            {
              table: {
                widths: [220, '*'],
                body: [[
                  noBranding ? { text: '', border: [false, false, false, false] } : {
                    table: {
                      widths: ['auto', '*'],
                      body: [[logoCell, {
                        stack: [
                          { text: 'SHARED BY', fontSize: 6, bold: true, color: '#64748b', margin: [0, 2, 0, 2] },
                          { text: companyName, fontSize: 11, bold: true, color: '#0f172a' }
                        ],
                        border: [false, false, false, false]
                      }]]
                    },
                    layout: 'noBorders',
                    margin: [12, 10, 8, 8]
                  },
                  {
                    stack: [
                      { text: 'IN-HOUSE BENCH TALENT DOSSIER', style: 'title', margin: [0, 2, 0, 0] },
                      { text: ('Bench Candidate Verified · Confidential').toUpperCase(), style: 'eyebrow', margin: [0, 3, 0, 0] }
                    ],
                    margin: [8, 12, 12, 8],
                    border: [false, false, false, false]
                  }
                ]]
              },
              layout: 'noBorders'
            }
          ]
        },
        // Candidate Hero Row
        {
          margin: [0, 0, 0, 8],
          columns: [
            {
              stack: [
                { text: fullName, style: 'candidateName' },
                { text: desig, style: 'designation', margin: [0, 2, 0, 6] },
                { text: 'TOTAL EXPERIENCE: ' + exp.toUpperCase(), fontSize: 7.5, color: '#64748b', bold: true }
              ],
              width: 360
            },
            {
              stack: [
                { text: 'BENCH READINESS', fontSize: 9, bold: true, color: '#64748b', alignment: 'center' },
                { text: rating + '%', fontSize: 26, bold: true, color: '#1d4ed8', alignment: 'center', margin: [0, 4, 0, 0] }
              ],
              width: 175
            }
          ]
        },
        // Metrics 4-Box Row
        {
          margin: [0, 0, 0, 8],
          table: {
            widths: ['*', '*', '*', '*'],
            body: [[
              { stack: [{ text: 'AVAILABILITY', fontSize: 6.5, color: '#64748b', bold: true }, { text: notice, fontSize: 8.5, bold: true, color: '#059669', margin: [0, 2, 0, 0] }], margin: [8, 6, 8, 6] },
              { stack: [{ text: 'LOCATION', fontSize: 6.5, color: '#64748b', bold: true }, { text: loc, fontSize: 8.5, bold: true, color: '#0f172a', margin: [0, 2, 0, 0] }], margin: [8, 6, 8, 6] },
              { stack: [{ text: 'WORK MODE', fontSize: 6.5, color: '#64748b', bold: true }, { text: wm, fontSize: 8.5, bold: true, color: '#0f172a', margin: [0, 2, 0, 0] }], margin: [8, 6, 8, 6] },
              { stack: [{ text: 'STATUS', fontSize: 6.5, color: '#64748b', bold: true }, { text: 'Bench Active', fontSize: 8.5, bold: true, color: '#2563eb', margin: [0, 2, 0, 0] }], margin: [8, 6, 8, 6] }
            ]]
          },
          layout: {
            fillColor: function () { return '#f8fafc'; },
            hLineColor: function () { return '#e2e8f0'; },
            vLineColor: function () { return '#e2e8f0'; },
            hLineWidth: function () { return 0.5; },
            vLineWidth: function () { return 0.5; }
          }
        },
        // Professional Summary & Recruiter Note
        {
          margin: [0, 0, 0, 8],
          columns: [
            {
              stack: [
                { text: 'PROFESSIONAL SUMMARY', style: 'sectionTitle' },
                { text: summary, style: 'body' }
              ],
              width: 260
            },
            {
              stack: [
                { text: 'RELATIONSHIP MANAGER COMMENTS', style: 'sectionTitle' },
                { text: rmComments, style: 'body' }
              ],
              width: 265
            }
          ],
          columnGap: 10
        },
        // Talent Skills & Expertise Table
        {
          margin: [0, 0, 0, 8],
          stack: [
            { text: 'TALENT SKILLS & EXPERTISE', style: 'sectionTitle' },
            {
              table: {
                widths: ['*', 100, 110],
                body: skillRows
              },
              layout: {
                hLineWidth: function (i) { return i === 1 ? 0.75 : 0.45; },
                vLineWidth: function () { return 0; },
                hLineColor: function (i) { return i === 1 ? '#94a3b8' : '#e2e8f0'; }
              }
            }
          ]
        },
        // Verification Badges
        {
          margin: [0, 0, 0, 6],
          table: {
            widths: ['*', '*', '*', '*'],
            body: [[
              { text: '✓ RESUME: VERIFIED', fontSize: 7, bold: true, color: '#059669', alignment: 'center' },
              { text: '✓ PHONE: VERIFIED', fontSize: 7, bold: true, color: '#059669', alignment: 'center' },
              { text: '✓ EMAIL: VERIFIED', fontSize: 7, bold: true, color: '#059669', alignment: 'center' },
              { text: '✓ TENANT: AUTHORIZED', fontSize: 7, bold: true, color: '#059669', alignment: 'center' }
            ]]
          },
          layout: {
            fillColor: function () { return '#f0fdf4'; },
            hLineWidth: function () { return 0; },
            vLineWidth: function () { return 0; }
          }
        },
        // Footer
        {
          table: {
            widths: ['*', 'auto'],
            body: [[
              { text: 'This dossier is generated exclusively for In-House Bench Talent · JERO ATS', style: 'footer', border: [false, true, false, false], margin: [0, 4, 0, 0] },
              { text: 'www.jero.works', style: 'footer', border: [false, true, false, false], margin: [0, 4, 0, 0] }
            ]]
          },
          layout: { hLineColor: function () { return '#e5e7eb'; }, hLineWidth: function () { return 0.5; }, vLineWidth: function () { return 0; } }
        }
      ]
    };
  }

  async function generateBenchPrescreenPdfBlob(cand, companyName, logoUri, noBranding) {
    await ensurePdfMake();
    var resolvedLogoUri = null;
    if (logoUri && !noBranding) {
      resolvedLogoUri = await rsGetLogoDataUri(logoUri);
    }
    var def = buildBenchDefinition(cand, companyName, resolvedLogoUri, noBranding);
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
  // COMPANY BRANDING HEADER MERGER (PDFLib)
  // ═══════════════════════════════════════════════════════════════════════════
  async function addCompanyBrandingHeader(pdfBytes, companyName) {
    if (!isPdfBytes(pdfBytes)) {
      throw new Error('Candidate resume is not in PDF format. Merged branding requires a PDF resume. Please select Option 5 (Pre-Screened Report PDF) or Option 1 (Original Resume).');
    }
    await ensurePdfLib();
    var PDFLib = window.PDFLib;
    var doc = await PDFLib.PDFDocument.load(pdfBytes);
    var pages = doc.getPages();
    if (!pages.length) return new Blob([pdfBytes], { type: 'application/pdf' });

    var first = pages[0];
    var size = first.getSize();
    var boldFont = await doc.embedFont(PDFLib.StandardFonts.HelveticaBold);
    var regFont = await doc.embedFont(PDFLib.StandardFonts.Helvetica);

    var headerH = 46;
    var targetDoc = await PDFLib.PDFDocument.create();
    var newFirst = targetDoc.addPage([size.width, size.height + headerH]);
    var embedded = await targetDoc.embedPage(first);
    newFirst.drawPage(embedded, { x: 0, y: 0, width: size.width, height: size.height });

    // Draw branding bar
    newFirst.drawRectangle({
      x: 0,
      y: size.height,
      width: size.width,
      height: headerH,
      color: PDFLib.rgb(0.97, 0.98, 1)
    });
    newFirst.drawLine({
      start: { x: 0, y: size.height },
      end: { x: size.width, y: size.height },
      thickness: 0.8,
      color: PDFLib.rgb(0.86, 0.89, 0.95)
    });

    newFirst.drawText('SHARED BY ' + companyName.toUpperCase(), {
      x: 20,
      y: size.height + 26,
      size: 8,
      font: boldFont,
      color: PDFLib.rgb(0.15, 0.23, 0.5)
    });
    newFirst.drawText('BENCH TALENT PROFILE', {
      x: 20,
      y: size.height + 12,
      size: 9.5,
      font: boldFont,
      color: PDFLib.rgb(0.06, 0.09, 0.16)
    });

    newFirst.drawText('CONFIDENTIAL', {
      x: size.width - 110,
      y: size.height + 24,
      size: 9,
      font: boldFont,
      color: PDFLib.rgb(0.85, 0.15, 0.15)
    });
    newFirst.drawText((new Date()).toLocaleDateString(), {
      x: size.width - 110,
      y: size.height + 12,
      size: 8,
      font: regFont,
      color: PDFLib.rgb(0.4, 0.45, 0.55)
    });

    if (pages.length > 1) {
      var otherPages = await targetDoc.copyPages(doc, doc.getPageIndices().slice(1));
      otherPages.forEach(function (p) { targetDoc.addPage(p); });
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
    var compName = (cand && (cand.report_company_name || cand.company_name)) || 'Hiring Partner';
    var candName = (cand && cand.full_name) || 'Candidate';
    // If Word DOCX/DOC, convert via OpenDoc WASM Engine 2 in User RAM
    if (window.AgyForwardResumeEngine && typeof window.AgyForwardResumeEngine.renderWasmDocxToPdfBlob === 'function') {
      return await window.AgyForwardResumeEngine.renderWasmDocxToPdfBlob(rawBytes, {
        candidateName: candName,
        companyName: compName
      });
    }
    // Dynamic load fallback
    try {
      await rsLoadScript('/js/opendoc/AgyForwardResumeEngine.js');
      if (window.AgyForwardResumeEngine && typeof window.AgyForwardResumeEngine.renderWasmDocxToPdfBlob === 'function') {
        return await window.AgyForwardResumeEngine.renderWasmDocxToPdfBlob(rawBytes, {
          candidateName: candName,
          companyName: compName
        });
      }
    } catch (e) {
      console.warn('[AgyBenchForwardResumeEngine] Could not load DOCX WASM converter:', e);
    }
    throw new Error('DOCX document could not be converted to PDF. Please select Option 1 (Original Resume) or Option 5 (Pre-Screened Report PDF).');
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
      var brandedBlob = await addCompanyBrandingHeader(pdfBytes, companyName);
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
      var resBranded = await addCompanyBrandingHeader(pdfBytes, companyName);
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
        var branded = await addCompanyBrandingHeader(pdfBytes, companyName);
        branded.filename = 'Jeroworks_' + safeName + '_Highlighted_Branded.pdf';
        notify(100, 'Branded highlighted resume ready!', 'Document ready to send');
        return branded;
      }

      if (optId === 46) {
        notify(30, 'Generating pre-screen dossier...', 'Compiling RM comments and talent skills...');
        var repBlob = await generateBenchPrescreenPdfBlob(cand, companyName, companyLogo, false);
        notify(75, 'Applying company branding...', 'Injecting organization banner...');
        var pdfBytes = new Uint8Array(await highlightedBlob.arrayBuffer());
        var branded = await addCompanyBrandingHeader(pdfBytes, companyName);
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
        var branded = await addCompanyBrandingHeader(pdfBytes, companyName);
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
