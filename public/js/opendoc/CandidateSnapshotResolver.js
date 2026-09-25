/**
 * Canonical Candidate Snapshot Resolver for Jero ATS
 * Location: public/js/opendoc/CandidateSnapshotResolver.js
 * 
 * Strict Zero-Fabrication, Scoped Candidate Context, Deep Immutability,
 * Coherent Notice Source Bundles, and Contract Validation across ALL elements.
 * 
 * Complies with Jeroworks ATS CQRS & Browser-Execution Standards (User RAM + WASM).
 */
(function (root, factory) {
  'use strict';
  var exp = factory();
  if (typeof window !== 'undefined') {
    window.CandidateSnapshotResolver = exp;
  }
  if (typeof root !== 'undefined') {
    root.CandidateSnapshotResolver = exp;
  }
  if (typeof self !== 'undefined') {
    self.CandidateSnapshotResolver = exp;
  }
  if (typeof globalThis !== 'undefined') {
    globalThis.CandidateSnapshotResolver = exp;
  }
  if (typeof module === 'object' && module && module.exports) {
    module.exports = exp;
  }
}(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

  var CURRENT_SCHEMA_VERSION = 1;
  var MAX_EXPERIENCE_YEARS = 60;
  var STRICT_EXP_REGEX = /^(\d+(\.\d{1,2})?)\s*(years?|yrs?)?$/i;
  var CANONICAL_EXP_REGEX = /^(Not Specified|Fresher|1 Year|(\d+(\.\d{1,2})?)\s*Years)$/;

  var NOTICE_MODES = Object.freeze({
    DAYS: 0,
    SERVING_NOTICE: 1,
    IMMEDIATE: 2
  });

  var INPUT_WORK_MODES = Object.freeze({
    remote: 'Remote',
    work_from_home: 'Remote',
    hybrid: 'Hybrid',
    office: 'Office',
    onsite: 'Office'
  });

  var CANONICAL_WORK_MODE_VALUES = Object.freeze({
    'Remote': true,
    'Hybrid': true,
    'Office': true,
    'Not Specified': true
  });

  // Custom Resolution Error Class
  function CandidateResolutionError(message, code, details) {
    this.name = 'CandidateResolutionError';
    this.message = message || 'Candidate resolution failed';
    this.code = code || 'UNKNOWN_ERROR';
    this.details = details || null;
    this.stack = (new Error()).stack;
  }
  CandidateResolutionError.prototype = Object.create(Error.prototype);
  CandidateResolutionError.prototype.constructor = CandidateResolutionError;

  function deepFreeze(obj) {
    if (!obj || typeof obj !== 'object') return obj;
    Object.freeze(obj);
    Object.keys(obj).forEach(function (key) {
      if (typeof obj[key] === 'object' && obj[key] !== null && !Object.isFrozen(obj[key])) {
        deepFreeze(obj[key]);
      }
    });
    return obj;
  }

  function cleanString(val) {
    if (val === null || val === undefined) return '';
    var s = String(val).trim();
    if (s === '--' || s === '—' || s === '-' || s.toLowerCase() === 'null' || s.toLowerCase() === 'undefined') return '';
    return s;
  }

  function isNumericId(val) {
    return /^\d+$/.test(String(val || '').trim());
  }

  function parseStrictInteger(val) {
    var s = cleanString(val);
    if (!/^\d+$/.test(s)) return null;
    var n = Number(s);
    return Number.isSafeInteger(n) ? n : null;
  }

  function parseStrictCandidateId(val) {
    if (typeof val === 'number') {
      return Number.isSafeInteger(val) && val > 0 ? val : null;
    }
    if (typeof val === 'string') {
      var s = val.trim();
      if (!/^\d+$/.test(s)) return null;
      var n = Number(s);
      return Number.isSafeInteger(n) && n > 0 ? n : null;
    }
    return null;
  }

  // --- Strict Field Formatters ---
  function formatExperience(val) {
    var s = cleanString(val);
    var match = s.match(STRICT_EXP_REGEX);
    if (match) {
      var n = parseFloat(match[1]);
      if (n === 0) return 'Fresher';
      if (n === 1) return '1 Year';
      return (Number.isInteger(n) ? n : n.toFixed(1)) + ' Years';
    }
    return 'Not Specified';
  }

  function formatLocation(val) {
    var s = cleanString(val);
    if (!s || isNumericId(s)) return 'Not Specified';
    var parts = s.split(',').map(function (p) { return p.trim(); }).filter(Boolean);
    var filtered = parts.filter(function (p) { return !/^india$/i.test(p); });
    return filtered.length ? filtered.slice(0, 2).join(', ') : (parts[0] || 'Not Specified');
  }

  function formatWorkMode(val) {
    var s = cleanString(val).toLowerCase().replace(/[\s_-]+/g, '_');
    return INPUT_WORK_MODES[s] || 'Not Specified';
  }

  function parseDateTuple(dateStr) {
    if (!dateStr) return null;
    var s = cleanString(dateStr);
    var m1 = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (m1) return { year: parseInt(m1[1], 10), month: parseInt(m1[2], 10), day: parseInt(m1[3], 10) };
    var m2 = s.match(/^(\d{2})[-/](\d{2})[-/](\d{4})/);
    if (m2) return { year: parseInt(m2[3], 10), month: parseInt(m2[2], 10), day: parseInt(m2[1], 10) };
    return null;
  }

  function formatDateTuple(dObj) {
    if (!dObj) return '';
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var dayStr = dObj.day < 10 ? '0' + dObj.day : '' + dObj.day;
    return dayStr + ' ' + months[dObj.month - 1] + ' ' + dObj.year;
  }

  function evaluateNoticeBundle(modeVal, npVal, lwdVal) {
    var modeInt = parseStrictInteger(modeVal);
    var npClean = cleanString(npVal);
    var lwdTuple = parseDateTuple(lwdVal);

    if (modeInt === NOTICE_MODES.IMMEDIATE || npClean.toLowerCase() === 'immediate') {
      var disp = 'Immediate';
      if (lwdTuple) disp += ' (LWD ' + formatDateTuple(lwdTuple) + ')';
      return { mode: 'IMMEDIATE', days: null, lastWorkingDate: lwdTuple ? formatDateTuple(lwdTuple) : null, display: disp };
    }
    if (modeInt === NOTICE_MODES.DAYS) {
      var daysInt = parseStrictInteger(npClean);
      if (daysInt !== null && daysInt > 0) {
        return { mode: 'DAYS', days: daysInt, lastWorkingDate: null, display: daysInt + ' Days' };
      }
    }
    if (modeInt === NOTICE_MODES.SERVING_NOTICE || lwdTuple) {
      var activeDateTuple = lwdTuple || parseDateTuple(npClean);
      if (activeDateTuple) {
        var formatted = formatDateTuple(activeDateTuple);
        return { mode: 'SERVING_NOTICE', days: null, lastWorkingDate: formatted, display: 'SNP • LWD ' + formatted };
      }
    }
    if (npClean && !isNumericId(npClean)) {
      return { mode: 'UNSPECIFIED', days: null, lastWorkingDate: null, display: npClean };
    }
    return null;
  }

  /**
   * Harvests Skills Table from Scoped DOM (0% Server Load)
   */
  function harvestSkillsFromDom(rootEl) {
    if (!rootEl) return null;
    var rows = rootEl.querySelectorAll('.skills-table.job_skill_view .skills-row, .skills-table .skills-row');
    if (!rows || !rows.length) return null;

    var harvested = [];
    for (var i = 0; i < rows.length; i++) {
      var nameEl = rows[i].querySelector('.skill-name-cell');
      var expEl = rows[i].querySelector('.skill-exp-cell');
      var starEls = rows[i].querySelectorAll('.skill-rating-cell .star-skill:not(.star-empty)');

      var sName = cleanString(nameEl ? (nameEl.innerText || nameEl.textContent) : '');
      if (!sName) continue;

      var expText = cleanString(expEl ? (expEl.innerText || expEl.textContent) : '');
      var rating = Math.min(5, Math.max(0, starEls ? starEls.length : 0));
      var expNum = 0;
      var match = expText.match(STRICT_EXP_REGEX);
      if (match) expNum = parseFloat(match[1]);

      harvested.push({
        name: sName,
        expText: expText || '--',
        exp: expNum,
        rating: rating
      });
    }
    return harvested.length ? harvested : null;
  }

  // --- Strict Field Validators ---
  var FIELD_VALIDATORS = {
    total_experience: function (val) {
      var s = cleanString(val);
      if (!s) return false;
      var match = s.match(STRICT_EXP_REGEX);
      if (!match) return false;
      var n = parseFloat(match[1]);
      return !isNaN(n) && n >= 0 && n <= MAX_EXPERIENCE_YEARS;
    },
    education: function (val) {
      var s = cleanString(val);
      return s.length > 0 && !isNumericId(s);
    },
    location: function (val) {
      var s = cleanString(val);
      return s.length > 0 && !isNumericId(s);
    },
    work_mode: function (val) {
      var s = cleanString(val).toLowerCase().replace(/[\s_-]+/g, '_');
      return INPUT_WORK_MODES.hasOwnProperty(s);
    }
  };

  function resolveFieldWithPrecedence(fieldName, sources, validator, formatter) {
    for (var i = 0; i < sources.length; i++) {
      var src = sources[i];
      if (!src || typeof src.getter !== 'function') continue;
      var rawVal = src.getter();
      var cleaned = cleanString(rawVal);
      if (!cleaned) continue;

      var isValid = validator ? validator(cleaned) : true;
      if (isValid) {
        var formatted = formatter ? formatter(cleaned) : cleaned;
        return {
          value: formatted,
          source: src.name,
          sourceType: src.type,
          trust: src.trust
        };
      }
    }
    return {
      value: 'Not Specified',
      source: 'terminal_fallback',
      sourceType: 'terminal_fallback',
      trust: 'unspecified'
    };
  }

  function validateSnapshot(snapshot) {
    if (!snapshot || typeof snapshot !== 'object') {
      throw new CandidateResolutionError('Invalid snapshot: must be an object', 'SNAPSHOT_NULL');
    }
    if (snapshot.schemaVersion !== CURRENT_SCHEMA_VERSION) {
      throw new CandidateResolutionError('Unsupported snapshot schema version', 'SNAPSHOT_SCHEMA_VERSION_UNSUPPORTED', { received: snapshot.schemaVersion, expected: CURRENT_SCHEMA_VERSION });
    }
    if (!Number.isSafeInteger(snapshot.candidateId) || snapshot.candidateId <= 0) {
      throw new CandidateResolutionError('Invalid candidateId: must be a positive integer', 'CANDIDATE_ID_INVALID', { candidateId: snapshot.candidateId });
    }
    var f = snapshot.fields;
    if (!f || typeof f !== 'object') {
      throw new CandidateResolutionError('Snapshot missing fields object', 'FIELDS_MISSING');
    }

    if (!CANONICAL_EXP_REGEX.test(f.experience)) {
      throw new CandidateResolutionError('Experience field not in canonical format', 'EXPERIENCE_NOT_CANONICAL', { experience: f.experience });
    }
    if (f.education !== 'Not Specified' && isNumericId(f.education)) {
      throw new CandidateResolutionError('Education cannot be a numeric ID', 'EDUCATION_NUMERIC_ID', { education: f.education });
    }
    if (f.location !== 'Not Specified' && isNumericId(f.location)) {
      throw new CandidateResolutionError('Location cannot be a numeric ID', 'LOCATION_NUMERIC_ID', { location: f.location });
    }
    if (!CANONICAL_WORK_MODE_VALUES[f.workMode]) {
      throw new CandidateResolutionError('Work mode not in canonical vocabulary', 'WORK_MODE_INVALID', { workMode: f.workMode });
    }

    var nd = snapshot.noticeDetails;
    if (!nd || typeof nd !== 'object' || !nd.display || typeof nd.display !== 'string') {
      throw new CandidateResolutionError('Notice details missing or invalid display', 'NOTICE_DETAILS_INVALID');
    }
    if (nd.mode === 'DAYS' && (!Number.isSafeInteger(nd.days) || nd.days <= 0)) {
      throw new CandidateResolutionError('Notice DAYS mode requires positive integer days', 'NOTICE_DAYS_INVALID');
    }
    return true;
  }

  function resolveSnapshot(context) {
    context = context || {};
    var rawData = context.rawData || {};
    var rootEl = context.rootElement || null;

    var candidateId = parseStrictCandidateId(context.candidateId || rawData.id);
    if (!candidateId) {
      throw new CandidateResolutionError('Fatal: valid positive integer candidateId is required.', 'CANDIDATE_ID_INVALID', { candidateId: context.candidateId || rawData.id });
    }

    if (rootEl) {
      var domRawId = rootEl.getAttribute('data-candidate-id');
      if (!domRawId) {
        throw new CandidateResolutionError('Candidate rootElement is missing data-candidate-id attribute.', 'ROOT_IDENTITY_MISSING');
      }
      var domId = parseStrictCandidateId(domRawId);
      if (!domId) {
        throw new CandidateResolutionError('Invalid candidate root identity: must be positive integer.', 'ROOT_IDENTITY_INVALID', { domId: domRawId });
      }
      if (domId !== candidateId) {
        throw new CandidateResolutionError('Candidate identity mismatch: rootElement ID (' + domId + ') !== context ID (' + candidateId + ').', 'CANDIDATE_ID_MISMATCH', { domId: domId, candidateId: candidateId });
      }
    }

    function getSemantic(fieldName) {
      if (!rootEl) return null;
      var target = rootEl.querySelector('[data-candidate-field="' + fieldName + '"]');
      if (!target) return null;
      return target.getAttribute('data-value') || target.innerText || target.textContent;
    }

    function getModifiedFormVal(selector) {
      if (!rootEl) return null;
      var el = rootEl.querySelector(selector);
      if (!el || el.getAttribute('data-dirty') !== 'true') return null;
      return el.value || el.innerText || '';
    }

    // 1. Candidate Name
    var candidateName = cleanString(getModifiedFormVal('input[name="full_name"]')) || cleanString(getSemantic('candidate_name')) || cleanString(rawData.full_name || rawData.name);
    if (!candidateName) {
      throw new CandidateResolutionError('Candidate full_name is required.', 'CANDIDATE_NAME_MISSING');
    }

    // 2. Current Company
    var company = resolveFieldWithPrecedence('company', [
      { name: 'live_modified_form', type: 'editable_state',           trust: 'user_modified', getter: function () { return getModifiedFormVal('input[name="current_company"]'); } },
      { name: 'semantic_dom',       type: 'presentation_state',       trust: 'view_derived',  getter: function () { return getSemantic('current_company'); } },
      { name: 'server_payload',     type: 'authoritative_projection', trust: 'server_authoritative', getter: function () { return rawData.current_company || rawData.cur_company_name || rawData.company; } }
    ], null, function (v) { return v; });

    // 3. Total Experience
    var exp = resolveFieldWithPrecedence('total_experience', [
      { name: 'live_modified_form', type: 'editable_state',           trust: 'user_modified', getter: function () { return getModifiedFormVal('input[name="total_exp"]'); } },
      { name: 'semantic_dom',       type: 'presentation_state',       trust: 'view_derived',  getter: function () { return getSemantic('total_experience'); } },
      { name: 'server_payload',     type: 'authoritative_projection', trust: 'server_authoritative', getter: function () { return rawData.experience || rawData.total_exp; } }
    ], FIELD_VALIDATORS.total_experience, formatExperience);

    // 4. Education
    var edu = resolveFieldWithPrecedence('education', [
      { name: 'live_modified_form', type: 'editable_state',           trust: 'user_modified', getter: function () {
        var opt = rootEl ? rootEl.querySelector('select[name="qualification"][data-dirty="true"] option:checked') : null;
        return opt ? opt.text : null;
      }},
      { name: 'semantic_dom',       type: 'presentation_state',       trust: 'view_derived',  getter: function () { return getSemantic('education'); } },
      { name: 'server_payload',     type: 'authoritative_projection', trust: 'server_authoritative', getter: function () { return rawData.qualification_name || rawData.qualification || rawData.education; } }
    ], FIELD_VALIDATORS.education, function (val) { return val.toUpperCase(); });

    // 5. Location
    var loc = resolveFieldWithPrecedence('location', [
      { name: 'live_modified_form', type: 'editable_state',           trust: 'user_modified', getter: function () { return getModifiedFormVal('input[name="current_location"]'); } },
      { name: 'semantic_dom',       type: 'presentation_state',       trust: 'view_derived',  getter: function () { return getSemantic('location'); } },
      { name: 'server_payload',     type: 'authoritative_projection', trust: 'server_authoritative', getter: function () {
        if (cleanString(rawData.current_location) && !isNumericId(rawData.current_location)) return rawData.current_location;
        var p = [cleanString(rawData.city_name || rawData.city), cleanString(rawData.state_name || rawData.state)].filter(function (x) { return x && !isNumericId(x); });
        return p.length ? p.join(', ') : (rawData.location || '');
      }}
    ], FIELD_VALIDATORS.location, formatLocation);

    // 6. Work Mode
    var wm = resolveFieldWithPrecedence('work_mode', [
      { name: 'live_modified_form', type: 'editable_state',           trust: 'user_modified', getter: function () {
        var opt = rootEl ? rootEl.querySelector('select[name="work_mode"][data-dirty="true"] option:checked') : null;
        return opt ? opt.text : null;
      }},
      { name: 'semantic_dom',       type: 'presentation_state',       trust: 'view_derived',  getter: function () { return getSemantic('work_mode'); } },
      { name: 'server_payload',     type: 'authoritative_projection', trust: 'server_authoritative', getter: function () { return rawData.work_mode || rawData.wm; } }
    ], FIELD_VALIDATORS.work_mode, formatWorkMode);

    // 7. Notice Period (Coherent Source Bundle Resolution)
    var canonicalNotice = null;
    var noticeTrust = 'unspecified';

    var formDirtyMode = getModifiedFormVal('input[name="serving_notice_period_mode"], select[name="serving_notice_period_mode"]');
    var formDirtyNp = getModifiedFormVal('select[name="np_id"] option:checked') || getModifiedFormVal('input[name="np_id"]');
    var formDirtyLwd = getModifiedFormVal('input[name="last_working_date"]');
    if (formDirtyMode !== null || formDirtyNp !== null || formDirtyLwd !== null) {
      canonicalNotice = evaluateNoticeBundle(formDirtyMode, formDirtyNp, formDirtyLwd);
      if (canonicalNotice) noticeTrust = 'user_modified';
    }

    if (!canonicalNotice) {
      var domNoticeVal = getSemantic('notice_period');
      if (domNoticeVal) {
        canonicalNotice = evaluateNoticeBundle(getSemantic('notice_mode'), domNoticeVal, getSemantic('notice_lwd'));
        if (canonicalNotice) noticeTrust = 'view_derived';
      }
    }

    if (!canonicalNotice) {
      canonicalNotice = evaluateNoticeBundle(rawData.serving_notice_period_mode, rawData.notice || rawData.np_id, rawData.last_working_date);
      if (canonicalNotice) noticeTrust = 'server_authoritative';
    }

    if (!canonicalNotice) {
      canonicalNotice = { mode: 'UNSPECIFIED', days: null, lastWorkingDate: null, display: 'Not Specified' };
    }

    // 8. Contact Details (Phone, Email, LinkedIn)
    var phone = cleanString(getModifiedFormVal('input[name="phone"]')) || cleanString(getSemantic('phone')) || cleanString(rawData.phone);
    var email = cleanString(getModifiedFormVal('input[name="email"]')) || cleanString(getSemantic('email')) || cleanString(rawData.email);
    var linkedin = cleanString(getModifiedFormVal('input[name="linkedin"]')) || cleanString(getSemantic('linkedin')) || cleanString(rawData.linkedin);

    // 9. Overall Candidate Rating (0 to 5)
    var rawRating = getModifiedFormVal('input[name="rating"]') || getSemantic('candidate_rating') || rawData.rating;
    var candidateRating = Math.max(0, Math.min(5, parseInt(rawRating, 10) || 0));

    // 10. Skills (Live DOM Table -> Modified Form -> Server Payload)
    var skills = harvestSkillsFromDom(rootEl);
    var skillsTrust = 'view_derived';
    if (!skills && Array.isArray(rawData.skills) && rawData.skills.length) {
      skills = rawData.skills;
      skillsTrust = 'server_authoritative';
    }
    if (!skills) {
      skills = [];
      skillsTrust = 'unspecified';
    }

    // 11. Recruiter Notes
    var recruiterNote = cleanString(rawData.recruiterNote || rawData.recruiter_note);
    if (!recruiterNote && rootEl) {
      var editorEl = rootEl.querySelector('#jw_recruiter_note_editor, .po-recruiter-note-text');
      if (editorEl) recruiterNote = cleanString(editorEl.innerText || editorEl.textContent);
    }

    // 12. Dynamic Verification Status (Resolves Phone Number omission bug)
    var validation = [];
    validation.push({ label: 'RESUME', status: rawData.resume ? 'Verified' : '-' });
    validation.push({ label: 'EMAIL ID', status: email ? 'Verified' : '-' });
    validation.push({ label: 'PHONE NUMBER', status: phone ? 'Verified' : '-' });
    var hasValidLinkedin = linkedin && !/^(na|n\/a|null|none|-|--)$/i.test(linkedin);
    validation.push({ label: 'LINKEDIN ID', status: hasValidLinkedin ? 'Verified' : '-' });
    var hasJobContext = cleanString(rawData.current_designation) || company.value !== 'Not Specified';
    validation.push({ label: 'JOB DESCRIPTION', status: hasJobContext ? 'Shared with candidate' : '-' });
    if (recruiterNote) validation.push({ label: 'Recruiter Screened', status: 'Completed' });
    var hasCtc = (parseFloat(rawData.ctc) > 0) || (parseFloat(rawData.ectc) > 0) || (parseFloat(rawData.ctc_yearly) > 0) || (parseFloat(rawData.ectc_yearly) > 0) || (parseFloat(rawData.percentage_hike) > 0);
    if (hasCtc) validation.push({ label: 'Compensation Discussed', status: 'Discussed' });

    var matchScore = safeNumber(rawData.match, 0, 100);
    if (!matchScore && rootEl) {
      var badgeEl = rootEl.querySelector('.jw-drawer-match-badge, [data-match-pct]');
      if (badgeEl) {
        var bAttr = badgeEl.getAttribute('data-match-pct') || badgeEl.innerText || '';
        var bNum = parseInt(bAttr.replace(/[^\d]/g, ''), 10);
        if (!isNaN(bNum)) matchScore = bNum;
      }
    }

    var snapshot = {
      schemaVersion: CURRENT_SCHEMA_VERSION,
      candidateId: candidateId,
      snapshotCreatedAt: (new Date()).toISOString(),
      provenance: {
        experience: exp,
        education: edu,
        noticePeriod: { value: canonicalNotice.display, source: canonicalNotice.mode !== 'UNSPECIFIED' ? 'canonical_notice_bundle' : 'terminal_fallback', trust: noticeTrust },
        location: loc,
        workMode: wm,
        company: company,
        skills: { count: skills.length, trust: skillsTrust }
      },
      noticeDetails: canonicalNotice,
      fields: {
        name: candidateName,
        designation: cleanString(rawData.current_designation || rawData.designation || rawData.jobTitle) || 'Not Specified',
        company: company.value,
        experience: exp.value,
        education: edu.value,
        noticePeriod: canonicalNotice.display,
        location: loc.value,
        workMode: wm.value,
        phone: phone,
        email: email,
        linkedin: linkedin,
        rating: candidateRating,
        match: matchScore,
        skills: skills,
        validation: validation,
        recruiterNote: recruiterNote || null,
        careerSummary: cleanString(rawData.careerSummary || rawData.careersummary || rawData.cover_letter) || null
      }
    };

    validateSnapshot(snapshot);

    if (typeof window !== 'undefined' && (window.__JERO_DEV_MODE__ || (window.localStorage && window.localStorage.getItem('debug_prescreen')))) {
      console.log('[CandidateSnapshotResolver] Frozen Snapshot #' + candidateId + ':', snapshot);
    }

    return deepFreeze(snapshot);
  }

  function safeNumber(v, min, max) {
    var n = parseInt(v, 10);
    if (isNaN(n)) return 0;
    return Math.max(min, Math.min(max, n));
  }

  return {
    CandidateResolutionError: CandidateResolutionError,
    resolveSnapshot: resolveSnapshot,
    validateSnapshot: validateSnapshot,
    harvestSkillsFromDom: harvestSkillsFromDom,
    formatExperience: formatExperience,
    formatLocation: formatLocation,
    formatWorkMode: formatWorkMode
  };
}));
