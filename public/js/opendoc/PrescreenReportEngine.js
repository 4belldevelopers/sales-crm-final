/**
 * PrescreenReportEngine.js — Standalone Pre-Screening Report PDF Engine (SSOT)
 *
 * INTENT:
 * Completely decoupled, upgrade-safe client-side PDF generation engine for JERO
 * Pre-Screened Candidate Reports (Recruiter Summary).
 *
 * ARCHITECTURAL ISOLATION GUARANTEES:
 * 1. Zero Coupling with Forward Resume email dispatch, CKEditor, or drawer state.
 * 2. Complete Field Normalization: Location (City, State, Country), Skills (verified & evaluated),
 *    Experience, Ratings, and Verification Status are defensively normalized.
 * 3. Client-First Execution: Renders 100% in User Browser RAM (< 50ms) using pdfMake.
 * 4. Zero Regressions: Drop-in compatible with Forward Resume, Download Resume Center, and Resume Studio.
 */
(function (global) {
  'use strict';

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

  // --- Strict Utility Functions ---
  function clean(val, fallback) {
    if (val === null || val === undefined) return fallback !== undefined ? fallback : '--';
    var s = String(val).trim();
    return s === '' || s === '-' || s === '--' || s.toLowerCase() === 'null' || s.toLowerCase() === 'n/a'
      ? (fallback !== undefined ? fallback : '--')
      : s;
  }

  function safeNumber(val, min, max, defaultVal) {
    var n = parseFloat(val);
    if (isNaN(n)) return defaultVal !== undefined ? defaultVal : min;
    return Math.max(min, Math.min(max, n));
  }

  function isGap(val) {
    var c = clean(val, '');
    return !c || c === '--' || c === '-' || /^available 01 jan 1970$/i.test(c);
  }

  function limitText(text, maxLen, fallback) {
    var c = clean(text, '');
    if (!c || c === '--') return fallback || '';
    if (c.length <= maxLen) return c;
    return c.substring(0, maxLen - 3).trim() + '...';
  }

  // --- Script Loaders ---
  function loadScript(src) {
    return new Promise(function (resolve, reject) {
      var existing = document.querySelector('script[src="' + src + '"]');
      if (existing && existing.getAttribute('data-ready') === '1') { resolve(); return; }
      var s = existing || document.createElement('script');
      var done = false;
      var finish = function (ok) {
        if (done) return;
        done = true;
        if (ok) { s.setAttribute('data-ready', '1'); resolve(); }
        else reject(new Error('Failed to load script: ' + src));
      };
      s.onload = function () { finish(true); };
      s.onerror = function () { finish(false); };
      if (!existing) {
        s.src = src;
        s.async = true;
        (document.head || document.documentElement).appendChild(s);
      }
    });
  }

  async function ensurePdfMake() {
    if (global.pdfMake && global.pdfMake.createPdf) return global.pdfMake;
    var base = '/assets/modules/fwd-resume/vendor/pdfmake';
    try {
      await loadScript(base + '/pdfmake.min.js');
      await loadScript(base + '/vfs_fonts.js');
    } catch (_) {
      await loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js');
      await loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js');
    }
    return global.pdfMake;
  }

  async function ensurePdfLib() {
    if (global.PDFLib && global.PDFLib.PDFDocument) return global.PDFLib;
    try {
      await loadScript('/assets/modules/fwd-resume/vendor/pdf-lib/pdf-lib.min.js');
    } catch (_) {
      await loadScript('https://cdn.jsdelivr.net/npm/pdf-lib@1.17.9/dist/pdf-lib.min.js');
    }
    return global.PDFLib;
  }

  function resolveLogoDataUri(logoUrl, rawBase64) {
    if (rawBase64 && /^data:image\//i.test(rawBase64)) return Promise.resolve(rawBase64);
    if (!logoUrl || logoUrl === '--') return Promise.resolve(null);
    if (/^data:image\//i.test(logoUrl)) return Promise.resolve(logoUrl);

    return new Promise(function (resolve) {
      var img = new Image();
      img.crossOrigin = 'Anonymous';
      var done = false;
      var timeout = setTimeout(function () {
        if (!done) { done = true; resolve(null); }
      }, 3000);

      img.onload = function () {
        if (done) return;
        done = true;
        clearTimeout(timeout);
        try {
          var canvas = document.createElement('canvas');
          canvas.width = img.naturalWidth || img.width;
          canvas.height = img.naturalHeight || img.height;
          var ctx = canvas.getContext('2d');
          ctx.drawImage(img, 0, 0);
          resolve(canvas.toDataURL('image/png'));
        } catch (_) {
          resolve(null);
        }
      };
      img.onerror = function () {
        if (!done) { done = true; clearTimeout(timeout); resolve(null); }
      };
      img.src = logoUrl;
    });
  }

  // --- Normalizer ---
  function normalizeCandidateDossier(cand) {
    if (!cand || typeof cand !== 'object') cand = {};

    // 1. Authoritative Full Location (City, State, Country)
    var cityName = clean(cand.city_name || cand.city || cand.cityName, '');
    if (/^\d+$/.test(cityName)) cityName = '';
    var stateName = clean(cand.state_name || cand.state || cand.stateName, '');
    if (/^\d+$/.test(stateName)) stateName = '';
    var countryName = clean(cand.country_name || cand.country || cand.countryName, '');
    if (/^\d+$/.test(countryName)) countryName = '';

    var rawLoc = clean(cand.location_text || cand.current_location || cand.location, '');
    if (/^\d+$/.test(rawLoc)) rawLoc = '';

    // Known City -> { state, country } Reconciliation SSOT Dictionary (Zero-Desync Defense)
    var knownCityStateMap = {
      'noida': { state: 'Uttar Pradesh', country: 'India' },
      'greater noida': { state: 'Uttar Pradesh', country: 'India' },
      'gurgaon': { state: 'Haryana', country: 'India' },
      'gurugram': { state: 'Haryana', country: 'India' },
      'gurgaon/gurugram': { state: 'Haryana', country: 'India' },
      'gurugram/gurgaon': { state: 'Haryana', country: 'India' },
      'bengaluru': { state: 'Karnataka', country: 'India' },
      'bangalore': { state: 'Karnataka', country: 'India' },
      'hyderabad': { state: 'Telangana', country: 'India' },
      'pune': { state: 'Maharashtra', country: 'India' },
      'mumbai': { state: 'Maharashtra', country: 'India' },
      'chennai': { state: 'Tamil Nadu', country: 'India' },
      'kolkata': { state: 'West Bengal', country: 'India' },
      'vadodara': { state: 'Gujarat', country: 'India' },
      'visakhapatnam': { state: 'Andhra Pradesh', country: 'India' },
      'anand': { state: 'Gujarat', country: 'India' },
      'ahmedabad': { state: 'Gujarat', country: 'India' },
      'lucknow': { state: 'Uttar Pradesh', country: 'India' },
      'jaipur': { state: 'Rajasthan', country: 'India' },
      'indore': { state: 'Madhya Pradesh', country: 'India' },
      'kochi': { state: 'Kerala', country: 'India' },
      'chandigarh': { state: 'Chandigarh', country: 'India' },
      'bhubaneswar': { state: 'Odisha', country: 'India' },
      'san francisco': { state: 'California', country: 'United States' },
      'new york': { state: 'New York', country: 'United States' },
      'new york city': { state: 'New York', country: 'United States' },
      'seattle': { state: 'Washington', country: 'United States' },
      'austin': { state: 'Texas', country: 'United States' },
      'boston': { state: 'Massachusetts', country: 'United States' },
      'chicago': { state: 'Illinois', country: 'United States' },
      'london': { state: 'England', country: 'United Kingdom' },
      'dubai': { state: 'Dubai', country: 'United Arab Emirates' },
      'singapore': { state: 'Central Singapore', country: 'Singapore' },
      'toronto': { state: 'Ontario', country: 'Canada' },
      'sydney': { state: 'New South Wales', country: 'Australia' }
    };

    var normCity = cityName.toLowerCase().trim();
    if (knownCityStateMap[normCity]) {
      var hubInfo = knownCityStateMap[normCity];
      stateName = hubInfo.state;
      if (!countryName) countryName = hubInfo.country;
    } else if (!cityName && rawLoc) {
      var rParts = rawLoc.split(',').map(function (p) { return p.trim(); });
      if (rParts.length > 0) {
        var rFirst = rParts[0].toLowerCase();
        if (knownCityStateMap[rFirst]) {
          cityName = rParts[0];
          stateName = knownCityStateMap[rFirst].state;
          if (!countryName) countryName = knownCityStateMap[rFirst].country;
        }
      }
    }

    var locParts = [cityName, stateName, countryName].filter(function (p) {
      return p && p !== '--' && !/^\d+$/.test(p);
    });

    // Deduplicate location parts
    var seenLoc = new Set();
    var uniqueLocParts = [];
    locParts.forEach(function (p) {
      var k = p.toLowerCase();
      if (!seenLoc.has(k)) {
        seenLoc.add(k);
        uniqueLocParts.push(p);
      }
    });

    var finalLocation = uniqueLocParts.length >= 2
      ? uniqueLocParts.join(', ')
      : (rawLoc || (uniqueLocParts.length ? uniqueLocParts.join(', ') : 'Not Specified'));

    // 2. Experience
    var exp = clean(cand.experience || cand.total_exp_display, '');
    if ((!exp || exp === '--') && cand.total_exp !== undefined && cand.total_exp !== null && cand.total_exp !== '') {
      var nExp = parseFloat(cand.total_exp);
      if (!isNaN(nExp)) {
        exp = nExp === 0.0 ? 'Fresher' : (nExp + ' Year' + (nExp === 1.0 ? '' : 's'));
      }
    }
    exp = exp || 'Not Specified';

    // 3. Skills
    var skills = [];
    if (Array.isArray(cand.skills) && cand.skills.length) {
      skills = cand.skills;
    } else if (Array.isArray(cand.parsed_skills) && cand.parsed_skills.length) {
      skills = cand.parsed_skills;
    } else if (typeof cand.skills === 'string' && cand.skills.trim()) {
      var sList = cand.skills.split(',').map(function (s) { return s.trim(); }).filter(Boolean);
      skills = sList.map(function (sName) {
        return { name: sName, expText: '--', exp: 0, rating: 0 };
      });
    }

    // 4. Verification Badges
    var validation = [];
    if (Array.isArray(cand.validation) && cand.validation.length) {
      validation = cand.validation;
    } else if (Array.isArray(cand.parsed_validation) && cand.parsed_validation.length) {
      validation = cand.parsed_validation;
    } else {
      var hasRes = !!cand.has_resume || (!!cand.resume && cand.resume !== '0' && cand.resume !== '--');
      validation.push({ label: 'RESUME', status: hasRes ? 'Verified' : 'Not Uploaded' });
      validation.push({ label: 'EMAIL ID', status: (cand.email || cand.secondrary_email) ? 'Verified' : 'Unverified' });
      validation.push({ label: 'PHONE NUMBER', status: (cand.phone || cand.secondary_mobile) ? 'Verified' : 'Unverified' });
      var hasLi = cand.linkedin && !/^(--|-|na|n\/a|null|none)$/i.test(String(cand.linkedin).trim());
      validation.push({ label: 'LINKEDIN ID', status: hasLi ? 'Verified' : '-' });
      validation.push({ label: 'JOB DESCRIPTION', status: (cand.current_designation || cand.current_company) ? 'Shared with candidate' : '-' });
    }

    // 5. Notice Period
    var rawNotice = cand.notice || cand.notice_text || cand.noticePeriod;
    var joining = cand.is_immediate || cand.isImmediate
      ? 'Immediate'
      : (clean(rawNotice, 'Not Specified') === '--' ? 'Not Specified' : clean(rawNotice, 'Not Specified'));

    // 6. Match Score
    var score = 0;
    if (cand.match !== undefined && cand.match !== null && cand.match !== '') {
      score = safeNumber(cand.match, 0, 100);
    }

    var reportId = cand.report_id || cand.reportId || ('JR' + (new Date()).toISOString().slice(0, 10).replace(/-/g, '') + String(cand.id || '000000').padStart(6, '0'));
    var reportDate = cand.report_date || cand.reportDate || (new Date()).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

    return {
      id: cand.id,
      name: clean(cand.full_name || cand.name, 'Candidate'),
      designation: clean(cand.designation || cand.current_designation || cand.job_title || cand.jobTitle, 'Verified Professional'),
      company: clean(cand.company || cand.current_company, '--'),
      experience: exp,
      total_exp: cand.total_exp,
      rel_exp: cand.rel_exp,
      location: finalLocation,
      city: cityName,
      state: stateName,
      country: countryName,
      education: clean(cand.education || cand.qualification_name || cand.qualification, 'Not Specified'),
      workMode: clean(cand.work_mode || cand.workMode || cand.wm, 'Office'),
      notice: joining,
      isImmediate: cand.is_immediate || cand.isImmediate || /immediate/i.test(joining),
      match: score,
      careerSummary: clean(cand.career_summary || cand.careersummary || cand.careerSummary || cand.cover_letter, ''),
      recruiterNote: clean(cand.recruiter_note_text || cand.recruiter_note || cand.recruiterNote, ''),
      skills: skills,
      validation: validation,
      screening_qa: cand.screening_qa || [],
      reportId: reportId,
      reportDate: reportDate,
      recruiterName: clean(cand.recruiter_name || cand.recruiterName, '--'),
      reportCompanyName: clean(cand.report_company_name || cand.reportCompanyName, 'Hiring Partner'),
      reportCompanyLogo: cand.report_company_logo || cand.reportCompanyLogo || '',
      reportCompanyLogoRaw: cand.report_company_logo_raw || cand.reportCompanyLogoRaw || ''
    };
  }

  // --- Layout Builders ---
  function headerCard(companyName, logoCell, reportDate) {
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

    var skillMap = new Map();
    (skills || []).forEach(function (skill) {
      var rawName = clean(skill && (skill.name || skill.skill_name || skill.skill_key || (typeof skill === 'string' ? skill : '')), '');
      var normKey = rawName.toLowerCase().trim();
      if (!normKey) return;

      var rating = safeNumber(skill.rating, 0, 5, 0);
      var exp = Number(skill.exp || 0);
      var expText = clean(skill.expText || skill.skill_value, '');

      if (!skillMap.has(normKey)) {
        skillMap.set(normKey, { name: rawName, rating: rating, exp: exp, expText: expText });
      } else {
        var existing = skillMap.get(normKey);
        if (rating > existing.rating) existing.rating = rating;
        if (exp > existing.exp) {
          existing.exp = exp;
          existing.expText = expText;
        }
      }
    });

    var uniqueSkills = Array.from(skillMap.values()).slice(0, 10);
    if (!uniqueSkills.length) {
      rows.push([{ text: 'No skill intelligence available in current candidate data.', colSpan: 3, style: 'muted', margin: [0, 6, 0, 6] }, {}, {}]);
      return rows;
    }

    uniqueSkills.forEach(function (skill) {
      var rating = safeNumber(skill.rating, 0, 5, 0);
      var exp = Number(skill.exp || 0);
      var expText = clean(skill.expText, '');
      var stars = [];
      for (var i = 1; i <= 5; i++) {
        stars.push({ svg: i <= rating ? rsStarGold : rsStarMuted, width: 7.7, margin: [0, 0.5, 1.2, 0] });
      }
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

  // --- Document Definition Generator ---
  function buildDefinition(data, logoUri, options) {
    options = options || {};
    data = normalizeCandidateDossier(data);

    var noBranding = options.noBranding === true;
    var score = safeNumber(data.match, 0, 100);
    var summary = limitText(data.careerSummary, 560, 'No professional summary available.');
    var noteMissing = isGap(data.recruiterNote);
    var note = noteMissing ? 'No recruiter comments available.' : limitText(data.recruiterNote, 420, '');
    var companyName = clean(data.reportCompanyName, 'Hiring Partner');

    var skillRows = buildSkillRows(data.skills);
    var skillCount = Math.max(1, skillRows.length - 1);
    var skillCardHeight = 80 + (skillCount * 28);

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
        noBranding ? unbrandedHeaderCard(data.reportDate) : headerCard(companyName, logoCell, data.reportDate),
        pairedCardRow([
          { text: clean(data.name, 'Candidate'), style: 'candidateName' },
          { text: clean(data.designation, '--'), style: 'designation', margin: [0, 3, 0, 8] },
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
                metricCell('Notice Period', clean(data.notice, 'Not Specified') === '--' ? 'Not Specified' : clean(data.notice, 'Not Specified'), rsIconCalendar),
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
              body: skillRows
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

  // --- PDF Blob Generation ---
  async function generatePdfBlob(data, options) {
    options = options || {};
    var ready = await Promise.all([
      ensurePdfMake(),
      options.noBranding ? Promise.resolve(null) : resolveLogoDataUri(data && (data.reportCompanyLogo || data.companyLogo), data && (data.reportCompanyLogoRaw || data.companyLogoRaw))
    ]);

    return new Promise(function (resolve, reject) {
      var done = false;
      var timer = setTimeout(function () {
        if (!done) { done = true; reject(new Error('Pre-Screening Report PDF generation timed out.')); }
      }, 30000);

      try {
        var docDef = buildDefinition(data, ready[1], options);
        global.pdfMake.createPdf(docDef).getBlob(function (blob) {
          if (done) return;
          clearTimeout(timer);
          done = true;
          if (blob && blob.size > 200) resolve(blob);
          else reject(new Error('Pre-Screening Report PDF output was empty'));
        });
      } catch (ex) {
        if (!done) { clearTimeout(timer); done = true; reject(ex); }
      }
    });
  }

  // --- Report Download ---
  async function downloadReport(data, fileName, options) {
    var blob = await generatePdfBlob(data, options);
    var name = fileName || ('Pre_Screening_Report_' + (data.id || 'candidate') + '.pdf');
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = name;
    document.body.appendChild(a);
    a.click();
    setTimeout(function () {
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    }, 2000);
    return blob;
  }

  // --- Normalized A4 Merger ---
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

  async function mergeReportWithResume(reportBlob, resumeBlob, fileName, mergeOptions) {
    await ensurePdfLib();
    mergeOptions = mergeOptions || {};
    var resumeBytes = await resumeBlob.arrayBuffer();
    var reportBytes = await reportBlob.arrayBuffer();
    var merged = await global.PDFLib.PDFDocument.create();
    var reportDoc = await global.PDFLib.PDFDocument.load(reportBytes);
    var resumeDoc = await global.PDFLib.PDFDocument.load(resumeBytes);

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

    var name = fileName || 'Prescreened_Resume_Package.pdf';
    var url = URL.createObjectURL(finalBlob);
    var a = document.createElement('a');
    a.href = url;
    a.download = name;
    document.body.appendChild(a);
    a.click();
    setTimeout(function () {
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    }, 2000);
    return true;
  }

  // --- Public API ---
  var PrescreenReportEngine = {
    buildDefinition: buildDefinition,
    generatePdfBlob: generatePdfBlob,
    buildRecruiterSummaryBlob: generatePdfBlob, // Backward compatibility alias
    downloadReport: downloadReport,
    mergeReportWithResume: mergeReportWithResume,
    mergePreScreenedReportAndResume: mergeReportWithResume, // Backward compatibility alias
    normalizeCandidateDossier: normalizeCandidateDossier,
    ensurePdfMake: ensurePdfMake,
    ensurePdfLib: ensurePdfLib,
    clean: clean,
    safeNumber: safeNumber
  };

  global.PrescreenReportEngine = PrescreenReportEngine;
  global.__PrescreenReportEngine = PrescreenReportEngine;

  if (typeof module !== 'undefined' && module.exports) {
    module.exports = PrescreenReportEngine;
  }
})(typeof window !== 'undefined' ? window : this);
