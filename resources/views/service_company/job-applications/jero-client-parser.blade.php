<script>
/**
 * jero-client-parser.js — 100% Client-Side Resume Parser Integration Bridge
 *
 * Production URL: https://parser.jero.works/jero-resume-parser-DW7x_WGm.js
 * Fallback URL:   https://parser.jero.works/jero-resume-parser.js
 *
 * Architecture:
 *   Browser (User RAM)
 *     ↓
 *   Direct dynamic import of production Cloudflare ES module
 *     ↓
 *   Local WebAssembly / JS execution (pdf.js, mammoth, layout, compromise NLP)
 *     ↓
 *   Structured ATS JSON output (logged to console)
 *     ↓
 *   JeroCandidateFormBridge.populate(result, isEditMode)
 *     ↓
 *   Zero server-side requests (0% Laravel/PHP, 0% MySQL, 0% Elasticsearch, 0% R2 upload during parsing)
 */

(function (window, document, $) {
    'use strict';

    var PRODUCTION_PARSER_URL = 'https://parser.jero.works/jero-resume-parser-DW7x_WGm.js';
    var FALLBACK_PARSER_URL = 'https://parser.jero.works/jero-resume-parser.js?v=20261110';
    var LOAD_TIMEOUT_MS = 30000;

    var _parserModulePromise = null;
    var _activeFile = null;
    var _lastParsedResult = null;
    var _currentParserRunId = 0;

    /**
     * Cancels active parse run and increments run ID so any in-flight WASM resolution is discarded.
     */
    function cancelActiveParse() {
        _currentParserRunId++;
        _activeFile = null;
        console.log('[PARSER] Active parse cancelled (runId now ' + _currentParserRunId + ')');
    }

    /**
     * Reports an explicit failure per God-Level audit spec.
     */
    function reportFailure(stage, err, file) {
        var errObj = {
            Stage: stage,
            Error: err && err.message ? err.message : String(err),
            Stack: err && err.stack ? err.stack : null,
            File: file ? (file.name + ' (' + file.size + ' bytes, ' + file.type + ')') : 'None',
            ParserURL: PRODUCTION_PARSER_URL
        };
        console.error('[PARSER FAILED]\n' +
            'Stage: ' + errObj.Stage + '\n' +
            'Error: ' + errObj.Error + '\n' +
            'Stack: ' + (errObj.Stack || 'None') + '\n' +
            'File: ' + errObj.File + '\n' +
            'Parser URL: ' + errObj.ParserURL, err);
        return errObj;
    }

    /**
     * Phase 2: Dynamic import from production Cloudflare edge with automatic fallback
     */
    function loadParserModule() {
        if (_parserModulePromise) {
            return _parserModulePromise;
        }

        console.log('[PARSER 3] Loading production parser:', PRODUCTION_PARSER_URL);
        console.log('[PARSER] module loading:', PRODUCTION_PARSER_URL);

        _parserModulePromise = new Promise(function (resolve, reject) {
            var timeoutId = setTimeout(function () {
                var timeoutErr = new Error('Parser loading timed out after ' + LOAD_TIMEOUT_MS + 'ms from ' + PRODUCTION_PARSER_URL);
                reportFailure('Module Loading (Timeout)', timeoutErr, _activeFile);
                reject(timeoutErr);
            }, LOAD_TIMEOUT_MS);

            function verifyAndResolve(mod) {
                clearTimeout(timeoutId);
                console.log('[PARSER 4] Parser module loaded');
                console.log('[PARSER] module loaded');

                var parseResumeFn = mod.parseResume || (mod.default && mod.default.parseResume);
                if (typeof parseResumeFn !== 'function') {
                    var missingFnErr = new Error('parseResume function not available on imported module');
                    reportFailure('Verify parseResume', missingFnErr, _activeFile);
                    throw missingFnErr;
                }

                console.log('[PARSER 5] parseResume available');
                console.log('[PARSER] parseResume available');
                resolve(mod);
            }

            import(PRODUCTION_PARSER_URL)
                .then(verifyAndResolve)
                .catch(function (primaryErr) {
                    console.warn('[PARSER] Primary bundle failed, falling back to canonical bundle:', FALLBACK_PARSER_URL, primaryErr);
                    return import(FALLBACK_PARSER_URL).then(verifyAndResolve);
                })
                .catch(function (err) {
                    clearTimeout(timeoutId);
                    _parserModulePromise = null; // allow retry
                    reportFailure('Dynamic Import', err, _activeFile);
                    reject(err);
                });
        });

        return _parserModulePromise;
    }

    /**
     * Calculates total experience in years and months from experience entries.
     * Merges overlapping date intervals to avoid duplicate counting.
     * Also checks summary and duration strings as fallback.
     */
    function calculateTotalExperience(experienceList, summaryText) {
        if (!Array.isArray(experienceList)) {
            experienceList = [];
        }

        var now = new Date();
        var currentYear = now.getFullYear();
        var currentMonth = now.getMonth() + 1;

        var parseDate = function (d, isEnd) {
            if (!d) return null;
            if (/present|current|now/i.test(String(d))) {
                return { year: currentYear, month: currentMonth };
            }
            var m = String(d).match(/^(\d{4})(?:-(\d{1,2}))?/);
            if (m) {
                return {
                    year: parseInt(m[1], 10),
                    month: m[2] ? parseInt(m[2], 10) : (isEnd ? 12 : 1)
                };
            }
            return null;
        };

        var intervals = [];
        for (var i = 0; i < experienceList.length; i++) {
            var item = experienceList[i];
            var start = parseDate(item.startDate, false);
            var end = parseDate(item.endDate, true);
            if (start && end) {
                var startMonths = start.year * 12 + start.month;
                var endMonths = end.year * 12 + end.month;
                if (endMonths >= startMonths) {
                    intervals.push([startMonths, endMonths]);
                }
            }
        }

        if (!intervals.length) {
            // Check if duration text exists in summary (e.g. "4+ years of hands-on experience", "2.6 years of experience")
            var sumMatch = String(summaryText || '').match(/(\d+(?:\.\d+)?)\+?\s*(?:years?|yrs?)\b(?:\s+(?:of\s+)?(?:hands-on\s+|relevant\s+|professional\s+|total\s+|industry\s+|working\s+)?experience)?/i);
            if (sumMatch) {
                var totalFloat = parseFloat(sumMatch[1]);
                var y = Math.floor(totalFloat);
                var m = Math.round((totalFloat - y) * 12);
                return {
                    years: Math.min(Math.max(y, 0), 60),
                    months: Math.min(Math.max(m, 0), 11)
                };
            }

            // Fallback: Check duration patterns in experience entries (e.g. "8 Months", "1 Year")
            var durMonths = 0;
            for (var d = 0; d < experienceList.length; d++) {
                var expStr = (experienceList[d].title || '') + ' ' + (experienceList[d].company || '') + ' ' + (experienceList[d].dateRaw || '');
                var ym = expStr.match(/(\d+(?:\.\d+)?)\s*(?:years?|yrs?)/i);
                if (ym) durMonths += parseFloat(ym[1]) * 12;
                var mm = expStr.match(/(\d+)\s*(?:months?|mos?)/i);
                if (mm) durMonths += parseInt(mm[1], 10);
            }
            if (durMonths > 0) {
                var dy = Math.floor(durMonths / 12);
                var dm = Math.round(durMonths % 12);
                return {
                    years: Math.min(Math.max(dy, 0), 60),
                    months: Math.min(Math.max(dm, 0), 11)
                };
            }

            return { years: 0, months: 0 };
        }

        intervals.sort(function (a, b) { return a[0] - b[0]; });
        var merged = [intervals[0]];
        for (var j = 1; j < intervals.length; j++) {
            var last = merged[merged.length - 1];
            var cur = intervals[j];
            if (cur[0] <= last[1]) {
                last[1] = Math.max(last[1], cur[1]);
            } else {
                merged.push(cur);
            }
        }

        var totalMonths = 0;
        for (var k = 0; k < merged.length; k++) {
            totalMonths += (merged[k][1] - merged[k][0]);
        }

        var years = Math.floor(totalMonths / 12);
        var months = totalMonths % 12;
        return {
            years: Math.min(Math.max(years, 0), 60),
            months: Math.min(Math.max(months, 0), 11)
        };
    }

    /**
     * Cleans up designation and company name when formatted like "Title – Company, Location (Duration)"
     * or when company was misidentified as the job description text.
     */
    function cleanJobAndCompany(rawTitle, rawCompany) {
        var designation = String(rawTitle || '').trim();
        var company = String(rawCompany || '').trim();

        var isCompanySentence = company && (
            /^(provided|worked|responsible|developed|managed|assisted|handled|created|led|designed|engineered|maintaining|coordinating)\b/i.test(company) ||
            company.length > 55
        );

        if ((!company || isCompanySentence) && designation && /[–—\-\u2013\u2014\u2015\u2212\|]|\s+at\s+|\s+@\s+/i.test(designation)) {
            var parts = designation.split(/\s*[–—\-\u2013\u2014\u2015\u2212\|]\s*|\s+at\s+|\s+@\s+/i);
            if (parts.length >= 2) {
                designation = parts[0].trim();
                var candidateCompany = parts.slice(1).join(' - ');
                candidateCompany = candidateCompany.replace(/\s*\([^\)]*\)/g, '').trim();
                candidateCompany = candidateCompany.replace(/,\s*[A-Z][a-zA-Z\s]+$/, '').trim();
                if (candidateCompany) {
                    company = candidateCompany;
                }
            }
        }

        return {
            designation: designation,
            company: company
        };
    }

    /**
     * Extracts company name from experience list or candidate object.
     * Handles:
     * - candidate.currentCompany or candidate.company
     * - experience[i].company (skipping sentences like "Provided technical support...")
     * - experience[i].description[j] (e.g. "Triotree Pvt. Ltd., Noida (8 Months)")
     * - experience[i].title (e.g. "Software Engineer – Triotree Pvt. Ltd.")
     * - fallback: scans summary and document text for "<Role> at <Company>" patterns
     */
    function extractCompanyFromExperience(expList, cand, fullResult) {
        var isSentence = function (text) {
            return !text ||
                /^(provided|worked|responsible|developed|managed|assisted|handled|created|led|designed|engineered|maintaining|coordinating|building|supported|participated)\b/i.test(text) ||
                text.length > 60;
        };

        var cleanCompName = function (raw) {
            var s = String(raw || '').trim();
            s = s.replace(/\s*\([^\)]*\)/g, '').trim(); // Remove (8 Months), (Noida)
            s = s.replace(/,\s*[A-Za-z\s]+$/, '').trim(); // Remove trailing city ", Noida"
            return s;
        };

        if (cand && (cand.currentCompany || cand.company || cand.current_company)) {
            var candComp = cleanCompName(cand.currentCompany || cand.company || cand.current_company);
            if (candComp && !isSentence(candComp) && candComp.length >= 2) return candComp;
        }
        if (Array.isArray(expList) && expList.length) {
            for (var i = 0; i < expList.length; i++) {
                var item = expList[i];
                if (!item) continue;

                // A. Check item.company
                var comp = String(item.company || '').trim();
                if (comp && !isSentence(comp)) {
                    var c1 = cleanCompName(comp);
                    if (c1 && c1.length >= 2 && c1.length <= 60) return c1;
                }

                // B. Check item.description array (e.g. "Triotree Pvt. Ltd., Noida (8 Months)")
                if (Array.isArray(item.description) && item.description.length > 0) {
                    for (var d = 0; d < item.description.length; d++) {
                        var descLine = String(item.description[d] || '').trim();
                        if (!descLine) continue;
                        var looksLikeCompany = /\b(pvt|ltd|limited|inc|llc|technologies|systems|solutions|infotech|services|corp|corporation|group|software|consulting)\b/i.test(descLine) ||
                            (!isSentence(descLine) && descLine.length < 50 && /[A-Z]/.test(descLine));
                        if (looksLikeCompany) {
                            var c2 = cleanCompName(descLine);
                            if (c2 && c2.length >= 2 && c2.length <= 60 && !isSentence(c2)) {
                                return c2;
                            }
                        }
                    }
                }

                // C. Check item.title if formatted as "Role – Company" or "Role at Company"
                var title = String(item.title || '').trim();
                var titleParts = title.split(/\s*[–—\-\u2013\u2014\u2015\u2212\|]\s*|\s+at\s+|\s+@\s+/i);
                if (titleParts.length >= 2) {
                    var c3 = cleanCompName(titleParts.slice(1).join(' - '));
                    if (c3 && c3.length >= 2 && c3.length <= 60 && !isSentence(c3)) {
                        return c3;
                    }
                }
            }
        }

        // D. Defensive fallback: scan summary or rawText for standard company pattern
        if (fullResult) {
            var textToScan = (fullResult.summary || '') + ' ' + (fullResult.rawText || '') + ' ' + ((cand && (cand.summary || cand.objective)) || '');
            var compMatch = textToScan.match(/(?:at|@)\s+([A-Z][A-Za-z0-9\s.,&-]{2,55}?(?:Pvt\.?\s*Ltd\.?|Private\s+Limited|Limited|Inc\.?|LLC|Corporation|Technologies|Solutions|Consulting|Services|Enterprises|Systems))\b/i);
            if (compMatch) {
                var c4 = cleanCompName(compMatch[1]);
                if (c4 && c4.length >= 2 && c4.length <= 60 && !isSentence(c4)) {
                    return c4;
                }
            }
        }

        return '';
    }

    /**
     * Phase 10: Normalize DOB output to YYYY-MM-DD for <input type="date" id="dob">
     */
    function normalizeDob(raw) {
        if (!raw) return null;
        var str = String(raw).trim();
        if (/^\d{4}-\d{2}-\d{2}$/.test(str)) return str;
        if (/^\d{4}\/\d{2}\/\d{2}$/.test(str)) return str.replace(/\//g, '-');

        var dmyMatch = str.match(/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/);
        if (dmyMatch) {
            var p1 = parseInt(dmyMatch[1], 10);
            var p2 = parseInt(dmyMatch[2], 10);
            var yr = parseInt(dmyMatch[3], 10);
            var day, month;
            if (p1 > 12) {
                day = p1; month = p2;
            } else if (p2 > 12) {
                month = p1; day = p2;
            } else {
                day = p1; month = p2;
            }
            if (month >= 1 && month <= 12 && day >= 1 && day <= 31) {
                return yr + '-' + String(month).padStart(2, '0') + '-' + String(day).padStart(2, '0');
            }
        }

        var cleanedStr = str.replace(/(st|nd|rd|th)/gi, '');
        var timestamp = Date.parse(cleanedStr);
        if (!isNaN(timestamp)) {
            var d = new Date(timestamp);
            var y = d.getFullYear();
            var m = String(d.getMonth() + 1).padStart(2, '0');
            var dayNum = String(d.getDate()).padStart(2, '0');
            var maxBirthYear = (new Date()).getFullYear() - 14;
            if (y >= 1930 && y <= maxBirthYear) {
                return y + '-' + m + '-' + dayNum;
            }
        }

        return null;
    }

    /**
     * Fuzzy matches parsed education against available qualification dropdown options.
     * Includes defensive keyword matching across fullResult text if educationList is empty or unmatched.
     */
    function matchQualification(educationList, selectEl, fullResult) {
        var qualSelect = selectEl || document.getElementById('qualification');
        if (!qualSelect || !qualSelect.options) return null;

        var options = qualSelect.options;

        if (Array.isArray(educationList) && educationList.length) {
            for (var i = 0; i < educationList.length; i++) {
                var edu = educationList[i];
                var degreeRaw = String(edu.degree || '').toLowerCase().trim();
                var fieldRaw = String(edu.field || '').toLowerCase().trim();
                var instRaw = String(edu.institution || '').toLowerCase().trim();
                var combined = (degreeRaw + ' ' + fieldRaw + ' ' + instRaw).trim();

                if (!combined) continue;

                for (var j = 0; j < options.length; j++) {
                    var opt = options[j];
                    if (!opt.value) continue;
                    var optText = opt.textContent.toLowerCase().trim();
                    var aliases = (opt.getAttribute('data-aliases') || '').toLowerCase().split(',').map(function (a) { return a.trim(); }).filter(Boolean);
                    if (!optText) continue;

                    if (combined === optText || degreeRaw === optText) {
                        return opt.value;
                    }

                    // Check acronym & alias matching
                    if (/\bb\.?tech\b|\bb\.?e\b|\bbachelor of technology\b/i.test(degreeRaw) && /\bb\.?tech\b|\bb\.?e\b/i.test(optText)) return opt.value;
                    if (/\bm\.?tech\b|\bm\.?e\b|\bmaster of technology\b/i.test(degreeRaw) && /\bm\.?tech\b|\bm\.?e\b/i.test(optText)) return opt.value;
                    if (/\bmba\b|\bpgdm\b/i.test(degreeRaw) && /\bmba\b/i.test(optText)) return opt.value;
                    if (/\bmca\b|\bmaster of computer/i.test(degreeRaw) && /\bmca\b/i.test(optText)) return opt.value;
                    if (/\bbca\b|\bbachelor of computer/i.test(degreeRaw) && /\bbca\b/i.test(optText)) return opt.value;
                    if (/\bb\.?sc\b|\bbs\b|\bbachelor of science\b/i.test(degreeRaw) && /\bb\.?sc\b|\bbs\b/i.test(optText)) return opt.value;
                    if (/\bm\.?sc\b|\bms\b|\bmaster of science\b/i.test(degreeRaw) && /\bm\.?sc\b|\bms\b/i.test(optText)) return opt.value;
                    if (/\bbba\b|\bbms\b/i.test(degreeRaw) && /\bbba\b/i.test(optText)) return opt.value;
                    if (/\bb\.?com\b/i.test(degreeRaw) && /\bb\.?com\b/i.test(optText)) return opt.value;
                    if (/\bm\.?com\b/i.test(degreeRaw) && /\bm\.?com\b/i.test(optText)) return opt.value;
                    if (/\bph\.?d\b|\bdoctorate\b/i.test(degreeRaw) && /\bph\.?d\b|\bdoctorate\b/i.test(optText)) return opt.value;
                    if (/\bdiploma\b|\bpolytechnic\b/i.test(degreeRaw) && /\bdiploma\b|\bpolytechnic\b/i.test(optText)) return opt.value;
                    if (/\b12th\b|\bhsc\b|\bintermediate\b|\bhigh school\b/i.test(degreeRaw) && /\b12th\b/i.test(optText)) return opt.value;
                    if (/\b10th\b|\bssc\b|\bmatric/i.test(degreeRaw) && /\b10th\b/i.test(optText)) return opt.value;

                    // Check data-aliases attribute if present
                    for (var a = 0; a < aliases.length; a++) {
                        var al = aliases[a];
                        if (al.length >= 3 && (degreeRaw === al || combined.indexOf(al) !== -1)) {
                            return opt.value;
                        }
                    }

                    if (combined.indexOf(optText) !== -1 || optText.indexOf(degreeRaw) !== -1) {
                        return opt.value;
                    }
                }
            }
        }

        // Defensive fallback: scan full text for degree matches
        if (fullResult) {
            var textToScan = (fullResult.summary || '') + ' ' + (fullResult.rawText || '') + ' ' + (fullResult.text || '') + ' ' + (fullResult.career_summary || '');
            if (/\b(?:b\.?tech|b\.?e\b|bachelor of technology|bachelor of engineering)\b/i.test(textToScan)) return '13';
            if (/\b(?:m\.?tech|m\.?e\b|master of technology|master of engineering)\b/i.test(textToScan)) return '40';
            if (/\b(?:mba|pgdm)\b/i.test(textToScan)) return '41';
            if (/\bmca\b/i.test(textToScan)) return '42';
            if (/\bbca\b/i.test(textToScan)) return '16';
            if (/\b(?:b\.?sc|b\.?s\b)\b/i.test(textToScan)) return '12';
            if (/\b(?:m\.?sc|m\.?s\b)\b/i.test(textToScan)) return '39';
            if (/\bdiploma\b/i.test(textToScan)) return '24';
            if (/\b(?:12th|hsc|intermediate)\b/i.test(textToScan)) return '2';
            if (/\b(?:10th|ssc|matric)\b/i.test(textToScan)) return '1';
        }

        return null;
    }

    /**
     * Phase 5: Canonical Integration Layer — JeroCandidateFormBridge
     * Powered by Safe Form Hydration Protocol (SFHP)
     */
    var JeroCandidateFormBridge = {
        populate: function (result, isEditMode) {
            console.log('[PARSER 9] Form mapping started via SFHP (isEditMode: ' + !!isEditMode + ')');

            if (!result || typeof result !== 'object') {
                var invalidResultErr = new Error('Cannot map non-object ATS result to form');
                reportFailure('Form Mapping (Validate Object)', invalidResultErr, _activeFile);
                return;
            }

            // SFHP Invariant 1: Lock hydration to prevent premature FormValidation error avalanches
            window.__JERO_HYDRATION_IN_PROGRESS = true;
            var populatedFields = [];
            var dirtyElements = [];

            try {
                var cand = result.candidate || {};

                // SFHP Invariant 2: Non-destructive Fill-If-Empty rule
                function safeSetVal(selector, value, validationFieldName) {
                    if (value === null || value === undefined) return false;
                    var strVal = String(value).trim();
                    if (strVal === '') return false;

                    var $el = $(selector);
                    if (!$el.length) return false;

                    var cur = ($el.val() || '').trim();

                    // Rule A: In EDIT MODE, NEVER overwrite existing values
                    if (isEditMode && cur !== '') {
                        return false;
                    }

                    // Rule B: In ADD MODE, if recruiter already entered manual value or field is pre-filled (e.g. Job Title), preserve it
                    if (!isEditMode && cur !== '') {
                        console.log('[SFHP] Preserving manual/prefilled value for ' + selector + ': "' + cur + '"');
                        return false;
                    }

                    $el.val(strVal);
                    dirtyElements.push($el[0]);
                    if (validationFieldName) {
                        populatedFields.push(validationFieldName);
                    }
                    return true;
                }

                // SFHP Invariant 3: Select2 Safe Hydration Adapter
                function safeSetSelect2(selector, targetVal, validationFieldName) {
                    if (targetVal === null || targetVal === undefined) return false;
                    var $el = $(selector);
                    if (!$el.length) return false;

                    var cur = ($el.val() || '').trim();
                    if (isEditMode && cur !== '') return false;

                    // In Add mode: Experience dropdowns default to "0" (Fresher) on page load.
                    // If cur === "0", allow updating with parsed experience.
                    var isExperienceDefault = (selector === '#total_exp_year' || selector === '#rel_exp_year' || selector === '#total_exp_month' || selector === '#rel_exp_month') && (cur === '0' || cur === '');
                    if (!isEditMode && cur !== '' && !isExperienceDefault) return false;

                    var strTarget = String(targetVal).trim();
                    var optionExists = $el.find('option[value="' + strTarget + '"]').length > 0;
                    if (optionExists) {
                        $el.val(strTarget);
                        if ($.fn.select2 && $el.hasClass('select2-hidden-accessible')) {
                            $el.trigger('change.select2');
                        }
                        $el.trigger('change');
                        dirtyElements.push($el[0]);
                        if (validationFieldName) {
                            populatedFields.push(validationFieldName);
                        }
                        return true;
                    }
                    return false;
                }

                // 1. Full Name
                try {
                    if (cand.name) {
                        var rawName = String(cand.name).replace(/^(curriculum vitae|resume|profile|cv|candidate)\s*[-:]*\s*/i, '').trim();
                        if (rawName.length >= 2) {
                            var formattedName = (typeof window.formatCandidateTitleCase === 'function')
                                ? window.formatCandidateTitleCase(rawName)
                                : rawName;
                            safeSetVal('#full_name', formattedName, 'full_name');
                            safeSetVal('#name', formattedName);
                        }
                    }
                } catch (e) {
                    console.error('[SFHP] Full Name error:', e);
                }

                // 2. Primary Email
                try {
                    if (cand.email) {
                        var cleanEmail = String(cand.email)
                            .replace(/^mailto:/i, '')
                            .replace(/[.,;:\/\s]+$/, '')
                            .trim()
                            .toLowerCase();
                        safeSetVal('#email', cleanEmail, 'email');
                    }
                } catch (e) {
                    console.error('[SFHP] Email error:', e);
                }

                // 3. Primary Mobile & Country Code (Enterprise JeroPhoneBox Integration)
                try {
                    var phoneVal = cand.mobileNumber || cand.phone;
                    if (phoneVal) {
                        if (window.JeroPhoneBox && typeof window.JeroPhoneBox.setPhone === 'function') {
                            window.JeroPhoneBox.setPhone('phone', phoneVal);
                            populatedFields.push('phone');
                        } else {
                            var digitsOnly = String(phoneVal).replace(/\D/g, '');
                            if (digitsOnly.length > 10) {
                                digitsOnly = digitsOnly.slice(-10);
                            }
                            safeSetVal('#phone', digitsOnly, 'phone');
                        }
                    }
                    if (cand.countryCode) {
                        var cc = String(cand.countryCode).trim();
                        if (window.JeroPhoneBox && typeof window.JeroPhoneBox.setCountry === 'function') {
                            window.JeroPhoneBox.setCountry('phone', cc);
                        } else {
                            safeSetVal('#primary_country_code', cc.replace(/^\+/, ''));
                        }
                    }
                } catch (e) {
                    console.error('[SFHP] Phone error:', e);
                }

                // 4. Phase 10: Date of Birth (Normalized to YYYY-MM-DD)
                try {
                    if (cand.dob) {
                        var normalizedDob = normalizeDob(cand.dob);
                        if (normalizedDob) {
                            safeSetVal('#dob', normalizedDob, 'dob');
                        }
                    }
                } catch (e) {
                    console.error('[SFHP] DOB error:', e);
                }

                // 5. Phase 7: Authoritative City Population via Tagify Bridge & Custom Combobox
                // Requirement: Only display the city if it matches an authoritative system city; otherwise remain blank.
                try {
                    var parsedLocData = cand.location || cand.city || (result && (result.location || result.city));
                    if (typeof window.resolveAndSetCandidateLocation === 'function') {
                        window.resolveAndSetCandidateLocation(parsedLocData);
                    } else if (typeof window.setSingleCandidateLocation === 'function') {
                        window.setSingleCandidateLocation(parsedLocData);
                    } else {
                        var locInput = document.querySelector('#kt_tagify_work_location');
                        var tagifyLoc = (locInput && locInput.__tagify) || window.tagifylocation;
                        if (tagifyLoc && typeof tagifyLoc.removeAllTags === 'function') {
                            tagifyLoc.removeAllTags();
                        }
                    }
                } catch (e) {
                    console.error('[SFHP] Location error:', e);
                }

                // 5b. Nationality Auto-Detection (Client RAM In-Memory Matcher)
                try {
                    var detectedNationality = null;
                    if (cand.nationality) {
                        detectedNationality = cand.nationality;
                    }
                    if (!detectedNationality) {
                        var rawCc = String(cand.countryCode || '').replace(/^\+/, '').trim();
                        if (rawCc === '91') detectedNationality = 'Indian';
                        else if (rawCc === '44') detectedNationality = 'British';
                        else if (rawCc === '61') detectedNationality = 'Australian';
                        else if (rawCc === '971') detectedNationality = 'Emirati';
                        else if (rawCc === '65') detectedNationality = 'Singaporean';
                        else if (rawCc === '49') detectedNationality = 'German';
                        else if (rawCc === '33') detectedNationality = 'French';
                        else if (rawCc === '353') detectedNationality = 'Irish';
                        else if (rawCc === '31') detectedNationality = 'Dutch';
                        else if (rawCc === '966') detectedNationality = 'Saudi';
                        else if (rawCc === '1') {
                            var locStr = String(parsedLocData || '').toLowerCase();
                            if (/canada|ontario|toronto|vancouver|quebec|alberta|montreal|calgary|ottawa/.test(locStr)) {
                                detectedNationality = 'Canadian';
                            } else {
                                detectedNationality = 'American';
                            }
                        }
                    }
                    if (!detectedNationality && parsedLocData) {
                        var locLower = String(parsedLocData).toLowerCase();
                        if (/india|bengaluru|bangalore|delhi|mumbai|hyderabad|pune|chennai|kolkata|noida|gurgaon|ahmedabad/.test(locLower)) {
                            detectedNationality = 'Indian';
                        } else if (/united states|usa|california|new york|texas|seattle|san francisco|chicago|los angeles|boston|austin/.test(locLower)) {
                            detectedNationality = 'American';
                        } else if (/united kingdom|uk|london|manchester|birmingham|england|scotland|wales|edinburgh/.test(locLower)) {
                            detectedNationality = 'British';
                        } else if (/canada|toronto|vancouver|montreal|ottawa|calgary|edmonton/.test(locLower)) {
                            detectedNationality = 'Canadian';
                        } else if (/australia|sydney|melbourne|brisbane|perth|adelaide/.test(locLower)) {
                            detectedNationality = 'Australian';
                        } else if (/dubai|abu dhabi|uae|united arab emirates|sharjah/.test(locLower)) {
                            detectedNationality = 'Emirati';
                        } else if (/singapore/.test(locLower)) {
                            detectedNationality = 'Singaporean';
                        } else if (/germany|berlin|munich|frankfurt|hamburg|stuttgart/.test(locLower)) {
                            detectedNationality = 'German';
                        } else if (/france|paris|lyon|marseille/.test(locLower)) {
                            detectedNationality = 'French';
                        } else if (/ireland|dublin|cork/.test(locLower)) {
                            detectedNationality = 'Irish';
                        } else if (/netherlands|amsterdam|rotterdam|utrecht|hague/.test(locLower)) {
                            detectedNationality = 'Dutch';
                        } else if (/saudi arabia|riyadh|jeddah|dammam/.test(locLower)) {
                            detectedNationality = 'Saudi';
                        }
                    }
                    if (detectedNationality) {
                        var natOption = $('#nationality').find('option').filter(function () {
                            return $(this).val().toLowerCase() === detectedNationality.toLowerCase() ||
                                   $(this).text().toLowerCase().indexOf(detectedNationality.toLowerCase()) !== -1;
                        }).first();
                        if (natOption.length) {
                            safeSetSelect2('#nationality', natOption.val(), 'nationality');
                        }
                    }
                } catch (e) {
                    console.error('[SFHP] Nationality error:', e);
                }

                // 5c. Phase 7b: Gender Auto-Detection (Client-First High-Precision Inference Engine)
                try {
                    var detectedGender = null;

                    // Tier 1: Candidate object from parser
                    if (cand.gender && /^(male|female)$/i.test(String(cand.gender).trim())) {
                        detectedGender = /^female$/i.test(String(cand.gender).trim()) ? 'Female' : 'Male';
                    }

                    // Tier 2: Salutation / Honorific in Name
                    var rawCandName = String(cand.name || '').trim();
                    if (!detectedGender && rawCandName) {
                        if (/^\s*(mr|shri|master)\b/i.test(rawCandName)) detectedGender = 'Male';
                        else if (/^\s*(ms|mrs|miss|smt)\b/i.test(rawCandName)) detectedGender = 'Female';
                    }

                    // Tier 3: Explicit Document Search in rawText or summary
                    if (!detectedGender) {
                        var searchCorpus = (result.rawText || '') + ' ' + (result.summary || '') + ' ' + JSON.stringify(result.sections || {});
                        var explicitMatch = searchCorpus.match(/(?:gender|sex)\s*[:\-–]\s*(male|female)\b/i);
                        if (explicitMatch) {
                            detectedGender = /^female$/i.test(explicitMatch[1]) ? 'Female' : 'Male';
                        }
                    }

                    // Tier 4: Name Morphological & Dictionary Matcher
                    if (!detectedGender && rawCandName) {
                        var cleanCandName = rawCandName.replace(/^(mr|ms|mrs|miss|dr|prof|er|shri|smt)\.?\s+/i, '').trim();
                        var nameTokens = cleanCandName.split(/[\s,._-]+/).map(function (t) {
                            return t.toLowerCase().trim();
                        }).filter(Boolean);

                        // Morphological suffix rules (Indian / South Asian names)
                        for (var ti = 0; ti < nameTokens.length; ti++) {
                            var tok = nameTokens[ti];
                            if (tok.length < 3) continue;
                            if (/(narayana|prasad|kumar|endra|veer|deep|jeet|singh|nath|dhar|babu|lal|ram|kant|vrat|esh|raj|pal|anshu)$/i.test(tok)) {
                                detectedGender = 'Male';
                                break;
                            }
                            if (/(kumari|devi|bai|wati|mati|shree|priya)$/i.test(tok)) {
                                detectedGender = 'Female';
                                break;
                            }
                        }

                        if (!detectedGender) {
                            var MALE_NAMES = {
                                'satyanarayana':1,'satya':1,'rahul':1,'amit':1,'rohit':1,'suresh':1,'ramesh':1,'rajesh':1,
                                'sachin':1,'vikas':1,'vikram':1,'abhishek':1,'gaurav':1,'manish':1,'anil':1,'sunil':1,
                                'deepak':1,'arun':1,'varun':1,'sanjay':1,'ajay':1,'vijay':1,'pankaj':1,'alok':1,
                                'ashish':1,'vivek':1,'sandeep':1,'pradeep':1,'kuldeep':1,'anand':1,'arjun':1,'karan':1,
                                'rohan':1,'mohit':1,'shubham':1,'mayank':1,'ayush':1,'kartik':1,'shivam':1,'harsh':1,
                                'rishabh':1,'yash':1,'chirag':1,'kunal':1,'tanmay':1,'parth':1,'vibhanshu':1,'john':1,
                                'david':1,'michael':1,'james':1,'robert':1,'william':1,'richard':1,'thomas':1,'charles':1,
                                'daniel':1,'matthew':1,'anthony':1,'mark':1,'paul':1,'steven':1,'andrew':1,'joshua':1,
                                'kevin':1,'brian':1,'george':1,'edward':1,'timothy':1,'jason':1,'ryan':1,'jacob':1,
                                'eric':1,'stephen':1,'justin':1,'scott':1,'brandon':1,'benjamin':1,'samuel':1,'alexander':1,
                                'patrick':1,'frank':1,'jack':1,'adam':1,'nathan':1,'henry':1,'peter':1,'mohammed':1,
                                'mohammad':1,'muhammad':1,'ahmed':1,'ali':1,'omar':1,'hassan':1,'tariq':1,'mustafa':1,
                                'ibrahim':1,'khalid':1,'abdullah':1,'usman':1,'bilal':1,'hamza':1,'salman':1,'imran':1
                            };
                            var FEMALE_NAMES = {
                                'priya':1,'pooja':1,'ananya':1,'sneha':1,'neha':1,'kavita':1,'sunita':1,'anita':1,
                                'geeta':1,'rekha':1,'shweta':1,'divya':1,'deepika':1,'ritu':1,'sonia':1,'swati':1,
                                'aarti':1,'shruti':1,'aditi':1,'jyoti':1,'vandana':1,'chetna':1,'anushka':1,'monika':1,
                                'pallavi':1,'tanvi':1,'radha':1,'meera':1,'archana':1,'namrata':1,'megha':1,'shreya':1,
                                'mansi':1,'rashi':1,'simran':1,'komal':1,'kajal':1,'sakshi':1,'ishita':1,'akanksha':1,
                                'kriti':1,'parul':1,'richa':1,'payal':1,'prerna':1,'ruchi':1,'smriti':1,'mary':1,
                                'patricia':1,'jennifer':1,'linda':1,'elizabeth':1,'barbara':1,'susan':1,'jessica':1,
                                'sarah':1,'karen':1,'nancy':1,'lisa':1,'margaret':1,'sandra':1,'ashley':1,'kimberly':1,
                                'emily':1,'donna':1,'michelle':1,'carol':1,'amanda':1,'melissa':1,'deborah':1,'stephanie':1,
                                'rebecca':1,'sharon':1,'laura':1,'cynthia':1,'kathleen':1,'amy':1,'shirley':1,'angela':1,
                                'helen':1,'anna':1,'brenda':1,'pamela':1,'nicole':1,'samantha':1,'katherine':1,'christine':1,
                                'rachel':1,'catherine':1,'carolyn':1,'janet':1,'ruth':1,'maria':1,'heather':1,'diane':1,
                                'julie':1,'joyce':1,'victoria':1,'olivia':1,'kelly':1,'christina':1,'lauren':1,'evelyn':1,
                                'judith':1,'megan':1,'cheryl':1,'andrea':1,'hannah':1,'martha':1,'gloria':1,'ann':1,
                                'teresa':1,'kathryn':1,'sara':1,'alice':1,'madison':1,'doris':1,'abigail':1,'julia':1,
                                'grace':1,'amber':1,'danielle':1,'theresa':1,'sophia':1,'fatima':1,'aisha':1,'zainab':1,
                                'mariam':1,'layla':1,'noor':1,'yasmin':1,'farah':1,'amira':1
                            };

                            for (var tj = 0; tj < nameTokens.length; tj++) {
                                if (MALE_NAMES[nameTokens[tj]]) {
                                    detectedGender = 'Male';
                                    break;
                                }
                                if (FEMALE_NAMES[nameTokens[tj]]) {
                                    detectedGender = 'Female';
                                    break;
                                }
                            }
                        }
                    }

                    if (detectedGender) {
                        var $genEl = $('#gender');
                        if ($genEl.length) {
                            var curGen = ($genEl.val() || '').trim();
                            if (!isEditMode || (curGen === '' || curGen === 'Rather Not Say')) {
                                $genEl.val(detectedGender);
                                if ($.fn.select2 && $genEl.hasClass('select2-hidden-accessible')) {
                                    $genEl.trigger('change.select2');
                                }
                                $genEl.trigger('change');
                                var nativeGenEl = document.getElementById('gender');
                                if (nativeGenEl) {
                                    nativeGenEl.value = detectedGender;
                                    nativeGenEl.dispatchEvent(new Event('change', { bubbles: true }));
                                }
                                dirtyElements.push($genEl[0]);
                                populatedFields.push('gender');
                                console.log('[SFHP] Gender auto-detected and set to:', detectedGender);
                            }
                        }
                    }
                } catch (e) {
                    console.error('[SFHP] Gender detection error:', e);
                }

                // 6. Phase 8: Skills Population via Batch Bridge or Tagify Bridge
                try {
                    var skillsList = Array.isArray(result.skills) ? result.skills : [];
                    if (skillsList.length) {
                        var batchHandled = false;
                        if (typeof window.addCandidatePrimarySkillsBatch === 'function') {
                            try {
                                window.addCandidatePrimarySkillsBatch(skillsList);
                                batchHandled = true;
                            } catch (bErr) {
                                console.warn('[SFHP] addCandidatePrimarySkillsBatch warning:', bErr);
                            }
                        }
                        if (!batchHandled && typeof window.addParsedBenchSkills === 'function') {
                            try {
                                window.addParsedBenchSkills(skillsList);
                                batchHandled = true;
                            } catch (_) {}
                        }

                        // Direct Visual Chips Fallback if container is still empty
                        var chipsContainer = document.querySelector('#candidatePrimarySkillChips');
                        var emptySkillsEl = document.querySelector('#candidatePrimarySkillEmpty');
                        if (chipsContainer && (!chipsContainer.children || chipsContainer.children.length === 0)) {
                            if (emptySkillsEl) emptySkillsEl.hidden = true;
                            skillsList.forEach(function (s) {
                                var sName = String(typeof s === 'object' ? (s.name || s.value) : s || '').trim();
                                if (!sName) return;
                                var chip = document.createElement('span');
                                chip.className = 'candidate-primary-skill-chip';
                                chip.setAttribute('data-skill-name', sName);
                                var label = document.createElement('span');
                                label.textContent = sName;
                                var removeBtn = document.createElement('button');
                                removeBtn.type = 'button';
                                removeBtn.setAttribute('aria-label', 'Remove ' + sName);
                                removeBtn.title = 'Remove ' + sName;
                                removeBtn.innerHTML = '<i class="fa-solid fa-xmark" aria-hidden="true"></i>';
                                removeBtn.addEventListener('click', function () {
                                    if (typeof window.removeCandidatePrimarySkill === 'function') {
                                        window.removeCandidatePrimarySkill(sName);
                                    } else {
                                        chip.remove();
                                    }
                                });
                                chip.append(label, removeBtn);
                                chipsContainer.appendChild(chip);
                            });
                        }

                        // Sync hidden Tagify input
                        var skillInput = document.querySelector('#kt_tagify_2');
                        var tagifySkill = (skillInput && skillInput.__tagify) || window.tagifyskill;
                        if (tagifySkill && typeof tagifySkill.addTags === 'function') {
                            try {
                                var uniqueSkills = [];
                                var seenSkills = new Set();
                                if (Array.isArray(tagifySkill.value)) {
                                    tagifySkill.value.forEach(function (t) {
                                        var v = (t.value || t.name || '').toLowerCase().trim();
                                        if (v) seenSkills.add(v);
                                    });
                                }
                                skillsList.forEach(function (s) {
                                    var name = String(typeof s === 'object' ? (s.name || s.value) : s || '').trim();
                                    var lower = name.toLowerCase();
                                    if (name && !seenSkills.has(lower)) {
                                        seenSkills.add(lower);
                                        uniqueSkills.push(name);
                                    }
                                });
                                if (uniqueSkills.length) {
                                    tagifySkill.addTags(uniqueSkills);
                                }
                            } catch (_) {}
                        } else if (skillInput && !skillInput.value) {
                            skillInput.value = skillsList.map(function(s) { return typeof s === 'object' ? (s.name || s.value) : s; }).join(', ');
                        }

                        if (typeof window.refreshCandidatePrimarySkills === 'function') {
                            try { window.refreshCandidatePrimarySkills(); } catch (_) {}
                        }
                    }
                } catch (e) {
                    console.error('[SFHP] Skills error:', e);
                }

                // 7. Phase 9: Experience Dropdowns & Calculation
                var expList = Array.isArray(result.experience) ? result.experience : (Array.isArray(result.work_experience) ? result.work_experience : []);
                var totalExp = calculateTotalExperience(expList, result.summary || result.career_summary || result.objective || (cand && (cand.summary || cand.objective)));

                try {
                    safeSetSelect2('#total_exp_year', String(totalExp.years), 'total_exp_year');
                    safeSetSelect2('#total_exp_month', String(totalExp.months), 'total_exp_month');
                    safeSetSelect2('#rel_exp_year', String(totalExp.years), 'rel_exp_year');
                    safeSetSelect2('#rel_exp_month', String(totalExp.months), 'rel_exp_month');

                    // Native select fallback to ensure values and change events fire unconditionally
                    ['#total_exp_year', '#rel_exp_year'].forEach(function (sel) {
                        var el = document.querySelector(sel);
                        if (el && String(totalExp.years) !== '') {
                            el.value = String(totalExp.years);
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                    ['#total_exp_month', '#rel_exp_month'].forEach(function (sel) {
                        var el = document.querySelector(sel);
                        if (el && String(totalExp.months) !== '') {
                            el.value = String(totalExp.months);
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                } catch (e) {
                    console.error('[SFHP] Experience error:', e);
                }

                // 7a. Current Company Extraction & Population
                try {
                    var extractedCompany = extractCompanyFromExperience(expList, cand, result);
                    if (!extractedCompany) {
                        extractedCompany = cand.currentCompany || cand.company || cand.current_company || result.company || result.current_company || '';
                    }
                    if (extractedCompany) {
                        safeSetVal('#current_company', extractedCompany, 'current_company');
                        safeSetVal('#company_name', extractedCompany, 'company_name');
                        var compEl = document.querySelector('#current_company');
                        if (compEl && !compEl.value) {
                            compEl.value = extractedCompany;
                            compEl.dispatchEvent(new Event('input', { bubbles: true }));
                            compEl.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                } catch (e) {
                    console.error('[SFHP] Current Company error:', e);
                }

                // 7b. Designation: Strictly keep existing pre-filled job title untouched (per user specification)
                // Only populate if current designation is completely empty:
                try {
                    var $desigEl = $('#current_designation');
                    if ($desigEl.length && !($desigEl.val() || '').trim()) {
                        var latestJob = expList[0] || {};
                        var cleanedJob = cleanJobAndCompany(latestJob.title, latestJob.company);
                        var designationToSet = cleanedJob.designation || cand.currentDesignation || '';
                        if (designationToSet) {
                            safeSetVal('#current_designation', designationToSet, 'current_designation');
                        }
                    }
                } catch (e) {
                    console.error('[SFHP] Designation error:', e);
                }

                // 8. Education / Qualification (Select2 Safe Adapter + Custom Combobox Sync)
                try {
                    var matchedQualId = matchQualification(result.education, document.getElementById('qualification'), result);
                    if (matchedQualId) {
                        safeSetSelect2('#qualification', String(matchedQualId), 'qualification');
                        var qualSelect = document.getElementById('qualification');
                        if (qualSelect) {
                            qualSelect.value = String(matchedQualId);
                            qualSelect.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                        var $cs = $('#jero-cs-qualification');
                        if ($cs.length) {
                            var $item = $cs.find('.jero-cs-item[data-val="' + matchedQualId + '"]').first();
                            $cs.find('.jero-cs-item').removeClass('is-selected');
                            if ($item.length) {
                                $item.addClass('is-selected');
                                $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text($item.find('.jero-cs-item-text').text().trim());
                                var badge = $item.attr('data-badge') || '';
                                if (badge) {
                                    $cs.find('.jero-cs-badge').show().text(badge);
                                } else {
                                    $cs.find('.jero-cs-badge').hide().text('');
                                }
                            } else {
                                var $opt = $('#qualification').find('option[value="' + matchedQualId + '"]').first();
                                if ($opt.length) {
                                    $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text($opt.text().trim());
                                    var optBadge = $opt.attr('data-badge') || '';
                                    if (optBadge) {
                                        $cs.find('.jero-cs-badge').show().text(optBadge);
                                    } else {
                                        $cs.find('.jero-cs-badge').hide().text('');
                                    }
                                }
                            }
                        }
                        $('#qualification').trigger('change');
                    }
                } catch (e) {
                    console.error('[SFHP] Qualification error:', e);
                }

                // 9. Career Summary (strictly for career summary, limited to 100 words, NEVER fills recruiter internal note)
                try {
                    var summary = result.summary || result.career_summary || result.objective || (cand && (cand.summary || cand.objective)) || '';
                    if (summary) {
                        // Strip any trailing raw section dumps (e.g. "Professional Skills:", "PROFESSIONAL EXPERIENCE:")
                        var cleanSummary = summary.split(/(?:professional\s+(?:skills|experience)|work\s+experience|employment\s+history|roles\s+and\s+responsibilities)/i)[0].trim();
                        if (cleanSummary && cleanSummary.split(/\s+/).filter(Boolean).length >= 10) {
                            summary = cleanSummary;
                        }
                        var words = String(summary).trim().split(/\s+/).filter(Boolean);
                        if (words.length > 100) {
                            summary = words.slice(0, 100).join(' ');
                        }
                        safeSetVal('#careersummary', summary, 'careersummary');
                        var csEl = document.querySelector('#careersummary');
                        if (csEl) {
                            if (!csEl.value) csEl.value = summary;
                            try {
                                csEl.dispatchEvent(new Event('input', { bubbles: true }));
                                csEl.dispatchEvent(new Event('change', { bubbles: true }));
                            } catch (_) {}
                            var csParent = csEl.closest('.summary-card-body, .candidate-card-body, .fsc') || csEl.parentElement;
                            if (csParent) {
                                var csCc = csParent.querySelector('.cc, #summary_counter');
                                if (csCc) {
                                    var actualWordCount = summary.trim().split(/\s+/).filter(Boolean).length;
                                    csCc.textContent = actualWordCount + ' / 100';
                                }
                            }
                        }
                    }
                } catch (e) {
                    console.error('[SFHP] Career summary error:', e);
                }

                // 10. Professional Links
                try {
                    if (cand.linkedin) {
                        safeSetVal('#linkedin', cand.linkedin);
                    }
                    if (cand.github) {
                        safeSetVal('#github', cand.github);
                    }
                } catch (e) {
                    console.error('[SFHP] Professional links error:', e);
                }

            } catch (popErr) {
                console.error('[JeroCandidateFormBridge] SFHP Hydration error:', popErr);
            } finally {
                // SFHP Invariant 4: Flush input events cleanly after all fields settled
                window.__JERO_HYDRATION_IN_PROGRESS = false;

                dirtyElements.forEach(function (domEl) {
                    try {
                        domEl.dispatchEvent(new Event('input', { bubbles: true }));
                        domEl.dispatchEvent(new Event('change', { bubbles: true }));
                    } catch (_) {}
                });

                // SFHP Invariant 5: Single-pass deferred revalidation
                setTimeout(function () {
                    var validator = window.candidateFormValidator || window.benchFormValidator;
                    if (validator && typeof validator.revalidateField === 'function') {
                        populatedFields.forEach(function (f) {
                            try { validator.revalidateField(f); } catch (_) {}
                        });
                    }
                }, 60);

                console.log('[PARSER 10] Form mapping completed safely via SFHP');

                try {
                    var evt = new CustomEvent('jero:resume-parsed', {
                        detail: { atsJson: result, isEditMode: isEditMode }
                    });
                    window.dispatchEvent(evt);
                } catch (_) {}
            }
        }
    };

    /**
     * Phase 1 & 3: Selected File -> JeroClientParser -> https://parser.jero.works/ -> ATS JSON
     */
    function parseAndPopulate(file, options) {
        options = options || {};
        var thisRunId = ++_currentParserRunId;
        _activeFile = file;

        var onProgress = options.onProgress || function () {};
        var onSuccess = options.onSuccess || function () {};
        var onError = options.onError || function () {};
        var requiredSkills = options.requiredSkills || [];
        var isEditMode = !!options.isEditMode;

        // [PARSER 1] File selected
        console.log('[PARSER 1] File selected (run ' + thisRunId + '):', file ? (file.name + ' (' + file.size + ' bytes, ' + file.type + ')') : 'None');

        if (!file) {
            var missingFileErr = new Error('No resume file provided for parsing');
            reportFailure('File Selection', missingFileErr, file);
            onError(missingFileErr);
            return Promise.reject(missingFileErr);
        }

        var startTime = performance.now();
        onProgress('Initializing parser...', 5);

        // [PARSER 2] Fully detached in-memory buffer snapshot
        return (async function () {
            var selectedFile = file;
            try {
                if (typeof file.arrayBuffer === 'function') {
                    var rawBuf = await file.arrayBuffer();
                    try {
                        selectedFile = new File([rawBuf], file.name, {
                            type: file.type || 'application/pdf',
                            lastModified: file.lastModified || Date.now()
                        });
                    } catch (_) {
                        selectedFile = new Blob([rawBuf], { type: file.type || 'application/pdf' });
                        selectedFile.name = file.name;
                        selectedFile.lastModified = file.lastModified || Date.now();
                    }
                    console.log('[PARSER 2] In-memory buffer snapshot created (' + rawBuf.byteLength + ' bytes)');
                }
            } catch (snapErr) {
                console.warn('[PARSER 2] Buffer snapshot fallback:', snapErr);
                selectedFile = file;
            }

            if (thisRunId !== _currentParserRunId) {
                console.log('[PARSER] Stale run ' + thisRunId + ' cancelled before module load');
                return null;
            }

            var mod = await loadParserModule();
            var parseResumeFn = mod.parseResume || (mod.default && mod.default.parseResume);
            if (typeof parseResumeFn !== 'function') {
                throw new Error('parseResume function not available on module');
            }

            if (thisRunId !== _currentParserRunId) {
                console.log('[PARSER] Stale run ' + thisRunId + ' cancelled before parse execution');
                return null;
            }

            // [PARSER 6] Parsing started
            console.log('[PARSER] file name:', selectedFile.name);
            console.log('[PARSER] file type:', selectedFile.type);
            console.log('[PARSER] file size:', selectedFile.size);
            console.log('[PARSER 6] Parsing started (run ' + thisRunId + ')');
            console.log('[PARSER] parse started');
            onProgress('Parsing resume in browser...', 15);

            return parseResumeFn(selectedFile, {
                requiredSkills: requiredSkills,
                onProgress: function (stage, pct) {
                    if (thisRunId === _currentParserRunId) {
                        var mappedStage = (stage && stage.indexOf('Building ATS JSON') !== -1) ? 'Completed' : stage;
                        onProgress(mappedStage, pct);
                    }
                }
            });
        })()
            .then(function (result) {
                if (thisRunId !== _currentParserRunId) {
                    console.log('[PARSER] Stale run ' + thisRunId + ' ignored after parse completion (current run: ' + _currentParserRunId + ')');
                    return null;
                }

                var totalMs = Math.round(performance.now() - startTime);

                // [PARSER 7] Parsing completed
                console.log('[PARSER 7] Parsing completed in ' + totalMs + 'ms');
                console.log('[PARSER] parse completed in ' + totalMs + 'ms');

                // [PARSER 8] ATS JSON received
                console.log('[PARSER 8] ATS JSON received:');
                console.log('[PARSER] result:', result);
                try {
                    console.log(JSON.stringify(result, null, 2));
                } catch (_) {
                    console.log(result);
                }

                if (!result || typeof result !== 'object') {
                    throw new Error('parseResume() returned invalid or empty ATS JSON');
                }

                _lastParsedResult = result;

                // Form mapping via Phase 5 Canonical Form Bridge
                JeroCandidateFormBridge.populate(result, isEditMode);

                if (thisRunId !== _currentParserRunId) {
                    return null;
                }

                onSuccess(result, totalMs);
                return result;
            })
            .catch(function (err) {
                if (thisRunId !== _currentParserRunId) {
                    console.log('[PARSER] Suppressing error for cancelled run ' + thisRunId);
                    return null;
                }
                reportFailure('parseResume Execution', err, selectedFile);
                onError(err);
                return null;
            });
    }

    // Expose global namespaces
    window.JeroCandidateFormBridge = JeroCandidateFormBridge;
    window.JeroClientParser = {
        load: loadParserModule,
        parseAndPopulate: parseAndPopulate,
        cancelActiveParse: cancelActiveParse,
        calculateTotalExperience: calculateTotalExperience,
        matchQualification: matchQualification,
        normalizeDob: normalizeDob,
        getLastResult: function () { return _lastParsedResult; },
        getActiveFile: function () { return _activeFile; },
        getCurrentRunId: function () { return _currentParserRunId; },
        PRODUCTION_PARSER_URL: PRODUCTION_PARSER_URL,
        FALLBACK_PARSER_URL: FALLBACK_PARSER_URL
    };

    // Pre-warm the parser module during idle time from production URL
    if (typeof window !== 'undefined') {
        var prewarm = function () {
            loadParserModule().catch(function (e) {
                console.warn('[PARSER] Idle pre-warm deferred:', e && e.message ? e.message : e);
            });
        };
        if ('requestIdleCallback' in window) {
            window.requestIdleCallback(prewarm, { timeout: 3000 });
        } else {
            setTimeout(prewarm, 1500);
        }
    }

})(window, document, window.jQuery || window.$);


</script>
