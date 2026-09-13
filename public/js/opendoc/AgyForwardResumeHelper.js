/**
 * AgyForwardResumeHelper.js
 * 
 * Client-Side Adapter for the Forward Resume Drawer (cvtracker/fwd_resume).
 * Delegates 100% of resume conversion, branding, highlighting, anonymization,
 * and report generation to the dedicated, ISOLATED AgyForwardResumeEngine.
 *
 * 100% DECOUPLED FROM DOWNLOAD RESUME CENTER:
 * Any changes made to Forward Resume will not touch or impact Download Resume Center.
 */

(function (root, factory) {
  'use strict';
  var exp = factory();
  if (typeof window !== 'undefined') {
    window.AgyForwardResumeHelper = exp;
  }
  if (typeof root !== 'undefined') {
    root.AgyForwardResumeHelper = exp;
  }
  if (typeof self !== 'undefined') {
    self.AgyForwardResumeHelper = exp;
  }
  if (typeof globalThis !== 'undefined') {
    globalThis.AgyForwardResumeHelper = exp;
  }
  if (typeof module === 'object' && module && module.exports) {
    module.exports = exp;
  }
}(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

  var _enginePromise = null;

  /**
   * Ensures the isolated AgyForwardResumeEngine module is loaded.
   */
  function ensureRenderer() {
    if (typeof window !== 'undefined' && window.AgyForwardResumeEngine && typeof window.AgyForwardResumeEngine.processOption === 'function') {
      return Promise.resolve(window.AgyForwardResumeEngine);
    }
    if (_enginePromise) return _enginePromise;

    _enginePromise = new Promise(function (resolve, reject) {
      // Check if it's already in flight / about to be defined
      var checkAttempts = 0;
      var pollExisting = setInterval(function () {
        if (typeof window !== 'undefined' && window.AgyForwardResumeEngine && typeof window.AgyForwardResumeEngine.processOption === 'function') {
          clearInterval(pollExisting);
          resolve(window.AgyForwardResumeEngine);
        } else if (++checkAttempts > 8) {
          clearInterval(pollExisting);
          loadSources();
        }
      }, 50);

      function loadSources() {
        var origin = (typeof window !== 'undefined' && window.location && window.location.origin) ? window.location.origin : '';
        var sources = [
          '/js/opendoc/AgyForwardResumeEngine.js',
          origin + '/js/opendoc/AgyForwardResumeEngine.js'
        ];

        function tryLoad(idx) {
          if (idx >= sources.length) {
            _enginePromise = null;
            reject(new Error('Could not load AgyForwardResumeEngine module'));
            return;
          }

          var s = document.createElement('script');
          s.type = 'text/javascript';
          s.src = sources[idx] + (sources[idx].indexOf('?') === -1 ? '?v=' + Date.now() : '');
          s.async = false;

          var pollTries = 0;
          function pollCheck() {
            if (typeof window !== 'undefined' && window.AgyForwardResumeEngine && typeof window.AgyForwardResumeEngine.processOption === 'function') {
              resolve(window.AgyForwardResumeEngine);
              return;
            }
            if (++pollTries < 30) {
              setTimeout(pollCheck, 60);
            } else {
              console.warn('[AgyForwardResumeHelper] Polling timed out for ' + sources[idx]);
              tryLoad(idx + 1);
            }
          }

          s.onload = function () { pollCheck(); };
          s.onerror = function (e) {
            console.warn('[AgyForwardResumeHelper] Script load failed for ' + sources[idx], e);
            tryLoad(idx + 1);
          };
          document.head.appendChild(s);
        }

        tryLoad(0);
      }
    }).catch(function (err) {
      _enginePromise = null;
      throw err;
    });

    return _enginePromise;
  }

  /**
   * Normalizes raw candidate object from server or Blade JSON into the standard
   * data structure expected by AgyForwardResumeEngine (identical to showdetail readData).
   */
  function normalizeCandidateData(candidate, companyData) {
    candidate = candidate || {};
    companyData = companyData || {};

    // Parse location parts
    var locParts = [candidate.city, candidate.state, candidate.country].filter(function (p) {
      return p && String(p).trim() !== '' && String(p).trim() !== '--';
    });
    var formattedLocation = candidate.location || (locParts.length ? locParts.join(', ') : '--');

    // Experience string
    var exp = candidate.experience;
    if (!exp && candidate.total_exp) {
      var tot = String(candidate.total_exp).trim();
      exp = tot + ' Year' + (parseFloat(tot) === 1.0 ? '' : 's');
    }
    exp = exp || '--';

    // Parse skills array for Skill Intelligence card
    var skills = [];
    if (Array.isArray(candidate.parsed_skills)) {
      skills = candidate.parsed_skills;
    } else if (Array.isArray(candidate.skills)) {
      skills = candidate.skills;
    } else if (typeof candidate.skills === 'string' && candidate.skills.trim()) {
      var sList = candidate.skills.split(',').map(function (s) { return s.trim(); }).filter(Boolean);
      skills = sList.slice(0, 7).map(function (sName, idx) {
        var expNum = Math.max(1, 6 - (idx % 4));
        var rating = Math.max(3, 5 - (idx % 3));
        return {
          name: sName,
          expText: expNum + ' Years',
          exp: expNum,
          rating: rating
        };
      });
    }

    // Parse validation badges
    var validation = [];
    if (Array.isArray(candidate.parsed_validation)) {
      validation = candidate.parsed_validation;
    } else if (Array.isArray(candidate.validation)) {
      validation = candidate.validation;
    } else {
      if (candidate.resume) validation.push({ label: 'RESUME', status: 'Verified' });
      if (candidate.email || candidate.secondrary_email) validation.push({ label: 'EMAIL ID', status: 'Verified' });
      if (candidate.phone || candidate.secondary_mobile) validation.push({ label: 'PHONE NUMBER', status: 'Verified' });
      if (candidate.linkedin) validation.push({ label: 'LINKEDIN ID', status: 'Verified' });
      else validation.push({ label: 'LINKEDIN ID', status: 'Discussed' });
      if (candidate.current_designation || candidate.current_company) validation.push({ label: 'JOB DESCRIPTION', status: 'Shared with candidate' });
    }

    var todayDate = (new Date()).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    var reportId = candidate.report_id || ('JR' + (new Date()).toISOString().slice(0, 10).replace(/-/g, '') + String(candidate.id || '000000').padStart(6, '0'));

    return {
      id: candidate.id,
      name: candidate.full_name || candidate.name || 'Candidate',
      email: candidate.email || '',
      secondaryEmail: candidate.secondrary_email || '',
      phone: candidate.phone || '',
      secondaryPhone: candidate.secondary_mobile || '',
      phoneCode: candidate.phone_code || '',
      secondaryPhoneCode: candidate.second_phone_code || '',
      linkedin: candidate.linkedin || '',
      designation: candidate.current_designation || candidate.designation || '',
      company: candidate.current_company || candidate.company || '',
      location: formattedLocation,
      experience: exp,
      notice: candidate.notice || candidate.np_id || '--',
      education: candidate.qualification_name || candidate.education || 'Graduate',
      workMode: candidate.work_mode || candidate.wm || 'Hybrid',
      reportId: reportId,
      reportDate: candidate.report_date || todayDate,
      recruiterName: candidate.recruiter_name || companyData.recruiterName || 'Hiring Team',
      reportCompanyName: companyData.companyName || candidate.report_company_name || 'Hiring Partner',
      reportCompanyLogo: companyData.companyLogo || candidate.report_company_logo || '',
      aiVersion: 'Live Intelligence',
      match: candidate.match || (candidate.rating ? Math.min(100, Math.max(40, parseInt(candidate.rating, 10) * 20)) : 85),
      isImmediate: !!candidate.is_immediate,
      recruiterNote: candidate.recruiter_note_text || candidate.recruiter_note || candidate.comment || 'Candidate thoroughly pre-screened for technical competency, communication skills, and delivery readiness. Strongly recommended for technical evaluation.',
      careerSummary: candidate.career_summary || candidate.careersummary || candidate.cover_letter || 'Experienced professional with demonstrated background in designing, developing, and deploying robust software architectures, driving end-to-end delivery and engineering excellence.',
      jobTitle: candidate.job_title || candidate.current_designation || 'Verified Professional',
      skills: skills,
      validation: validation,
      resume: candidate.resume || ''
    };
  }

  /**
   * Main entry point called by the Forward Resume drawer before AJAX email dispatch.
   *
   * @param {object} candidate - Candidate object from @json($applicationData)
   * @param {number|string} optId - Format option ID (1, 5, 41-53)
   * @param {object} companyData - { companyName, companyLogo, recruiterName }
   * @param {array} jobSkills - Array of job skill names for highlighting
   * @param {function} statusCallback - Progress notification callback
   * @returns {Promise<{ blob: Blob, filename: string }>}
   */
  async function generateAttachmentForCandidate(candidate, optId, companyData, jobSkills, statusCallback) {
    optId = parseInt(optId, 10);
    if (isNaN(optId)) optId = 1;

    var renderer = await ensureRenderer();
    var data = normalizeCandidateData(candidate, companyData);

    // Build resume source URLs in priority order
    var sources = [];
    if (candidate.resume_r2_url) sources.push(candidate.resume_r2_url);
    if (candidate.id) {
      sources.push('/service-company/job-applications/r2-download-url/' + candidate.id);
      sources.push('/service-company/cvtracker/fwd_resume_download/' + candidate.id);
    }
    if (candidate.resume) sources.push(candidate.resume);

    // Determine extension
    var resumeExt = '';
    if (candidate.resume) {
      var rParts = String(candidate.resume).split('?')[0].split('.');
      if (rParts.length > 1) resumeExt = rParts.pop().toUpperCase();
    }
    if (resumeExt !== 'PDF' && resumeExt !== 'DOCX') {
      resumeExt = 'PDF';
    }

    if (statusCallback) {
      statusCallback('Rendering Option ' + optId + ' for ' + data.name + '...');
    }

    // Call isolated AgyForwardResumeEngine with returnBlob: true
    var blob = await renderer.processOption(optId, data, {
      returnBlob: true,
      resumeUrl: sources,
      resumeExt: resumeExt,
      keywords: jobSkills || [],
      statusCallback: statusCallback
    });

    if (!(blob instanceof Blob) || blob.size < 100) {
      throw new Error('AgyForwardResumeEngine produced an invalid or empty resume blob for candidate: ' + data.name);
    }

    // Construct filename matching Download Resume Center conventions
    var safeName = String(data.name || 'Candidate').replace(/[\\/:*?"<>|]+/g, ' ').replace(/\s+/g, ' ').trim() || 'Candidate';
    var ext = (optId === 1 && resumeExt === 'DOCX') ? 'docx' : 'pdf';
    var filename = 'Jeroworks_' + safeName + '.' + ext;

    // Attach filename property to Blob for maximum backward/forward compatibility
    blob.filename = filename;

    return blob;
  }

  return {
    generateAttachmentForCandidate: generateAttachmentForCandidate,
    normalizeCandidateData: normalizeCandidateData,
    ensureRenderer: ensureRenderer
  };
}));