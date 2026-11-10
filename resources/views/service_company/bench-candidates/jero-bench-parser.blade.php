<script>
/**
 * jero-bench-parser.blade.php — 100% Isolated Client-Side Resume Parser Integration Bridge
 *
 * Production URL: https://parser.jero.works/jero-resume-parser-DW7x_WGm.js
 * Fallback URL:   https://parser.jero.works/jero-resume-parser.js?v=20261110
 * Master Engineering Spec: docs/V4_Jeroworks_Upgrade/v4_add_talent_audit_and_remediation_plan.md
 * Strict Isolation: Quarantined to Service Company Add/Edit Talent Flow (Rule 17)
 * Zero Strain: 100% Browser Execution (User RAM + WASM) (Rules 1, 2, 3, 4)
 *
 * Architecture:
 *   Browser (User RAM)
 *     ↓
 *   Direct dynamic import of production Cloudflare ES module (parser.jero.works)
 *     ↓
 *   Local WebAssembly / JS execution (pdf.js, mammoth, layout, compromise NLP)
 *     ↓
 *   Structured ATS JSON output
 *     ↓
 *   JeroBenchFormBridge.populate(result, isEditMode)
 *     ↓
 *   Non-destructive autofill of Bench Candidate inputs & Skills Experience Table
 *     ↓
 *   Zero server-side requests (0% Laravel/PHP, 0% MySQL, 0% Elasticsearch during parsing)
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
        console.log('[TALENT PARSER] Active parse cancelled (runId now ' + _currentParserRunId + ')');
    }

    /**
     * Reports an explicit failure per Master Engineering Contract.
     */
    function reportFailure(stage, err, file) {
        var errObj = {
            Stage: stage,
            Error: err && err.message ? err.message : String(err),
            Stack: err && err.stack ? err.stack : null,
            File: file ? (file.name + ' (' + file.size + ' bytes, ' + file.type + ')') : 'None',
            ParserURL: PRODUCTION_PARSER_URL
        };
        console.error('[TALENT PARSER FAILED]\n' +
            'Stage: ' + errObj.Stage + '\n' +
            'Error: ' + errObj.Error + '\n' +
            'Stack: ' + (errObj.Stack || 'None') + '\n' +
            'File: ' + errObj.File + '\n' +
            'Parser URL: ' + errObj.ParserURL, err);
        return errObj;
    }

    /**
     * Loads production parser module via browser dynamic import with automatic fallback.
     */
    function loadParserModule() {
        if (_parserModulePromise) {
            return _parserModulePromise;
        }

        console.log('[TALENT PARSER] Loading production parser module:', PRODUCTION_PARSER_URL);

        _parserModulePromise = new Promise(function (resolve, reject) {
            var timeoutId = setTimeout(function () {
                var timeoutErr = new Error('Parser loading timed out after ' + LOAD_TIMEOUT_MS + 'ms from ' + PRODUCTION_PARSER_URL);
                reportFailure('Module Loading (Timeout)', timeoutErr, _activeFile);
                reject(timeoutErr);
            }, LOAD_TIMEOUT_MS);

            function verifyAndResolve(mod) {
                clearTimeout(timeoutId);
                console.log('[TALENT PARSER] Parser module loaded successfully');

                var parseResumeFn = mod.parseResume || (mod.default && mod.default.parseResume);
                if (typeof parseResumeFn !== 'function') {
                    var missingFnErr = new Error('parseResume function not available on imported module');
                    reportFailure('Verify parseResume', missingFnErr, _activeFile);
                    throw missingFnErr;
                }

                console.log('[TALENT PARSER] parseResume available');
                resolve(mod);
            }

            import(PRODUCTION_PARSER_URL)
                .then(verifyAndResolve)
                .catch(function (primaryErr) {
                    console.warn('[TALENT PARSER] Primary bundle failed, falling back to canonical bundle:', FALLBACK_PARSER_URL, primaryErr);
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
     * Normalizes DOB output to YYYY-MM-DD for <input type="text" id="dob"> / flatpickr.
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
                var combined = (degreeRaw + ' ' + fieldRaw).trim();

                if (!combined) continue;

                // Smart Global Alias & Synonym Mapping (Canonical Resolution)
                // Engineering & Technology (B.Tech / B.E)
                if (/\b(b\.?tech|b\.?e\.?|bachelor of technology|bachelor of engineering|beng)\b/i.test(combined)) return '13';
                // Master of Technology / Engineering (M.Tech / M.E)
                if (/\b(m\.?tech|m\.?e\.?|master of technology|master of engineering|meng)\b/i.test(combined)) return '40';
                // Computer Applications
                if (/\b(m\.?c\.?a|master of computer applications)\b/i.test(combined)) return '42';
                if (/\b(b\.?c\.?a|bachelor of computer applications)\b/i.test(combined)) return '16';
                // Science & Computing (B.S., B.Sc., BSCS, Bachelor of Science)
                if (/\b(b\.?s\.?|b\.?sc|bachelor of science|bsc(?:\s*\(hons\))?|bscs)\b/i.test(combined)) return '12';
                // Master of Science (M.S., M.Sc., MSCS, Master of Science)
                if (/\b(m\.?s\.?|m\.?sc|master of science|msc|mscs)\b/i.test(combined)) return '39';
                // Business Administration
                if (/\b(m\.?b\.?a|pgdm|master of business administration)\b/i.test(combined)) return '41';
                if (/\b(b\.?b\.?a|b\.?m\.?s|bachelor of business administration)\b/i.test(combined)) return '5';
                // Commerce & Finance
                if (/\b(b\.?com|bachelor of commerce)\b/i.test(combined)) return '6';
                if (/\b(m\.?com|master of commerce)\b/i.test(combined)) return '35';
                // Doctorate & Ph.D.
                if (/\b(ph\.?d|doctorate|doctor of philosophy|dphil)\b/i.test(combined)) return '52';
                // Associate Degrees & Diplomas
                if (/\b(associate(?:'s)? degree|polytechnic|diploma)\b/i.test(combined)) return '22';
                // Professional & Legal
                if (/\b(chartered accountant|cpa|acca)\b/i.test(combined)) return '26';
                if (/\b(ll\.?b|juris doctor|j\.?d\.?|bachelor of laws)\b/i.test(combined)) return '23';
                if (/\b(ll\.?m|master of laws)\b/i.test(combined)) return '31';
                // Medicine & Surgery
                if (/\b(mbbs|medical doctor|m\.?d\.?)\b/i.test(combined)) return '24';
                // High School & Secondary
                if (/\b(12th|hsc|senior secondary|high school|a[- ]?levels?)\b/i.test(combined)) return '2';
                if (/\b(10th|ssc|matriculation|gcse|o[- ]?levels?)\b/i.test(combined)) return '1';

                for (var j = 0; j < options.length; j++) {
                    var opt = options[j];
                    if (!opt.value) continue;
                    var optText = opt.textContent.toLowerCase().trim();
                    if (!optText) continue;

                    if (combined === optText || degreeRaw === optText) {
                        return opt.value;
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
            if (/\bdiploma\b/i.test(textToScan)) return '22';
            if (/\b(?:12th|hsc|intermediate)\b/i.test(textToScan)) return '2';
            if (/\b(?:10th|ssc|matric)\b/i.test(textToScan)) return '1';
        }

        return null;
    }

    /**
     * Canonical Integration Bridge for Bench Candidates Form (Add & Edit Talent)
     * Non-destructive: Populates empty fields only, preserving any manual recruiter typing.
     */
    var JeroBenchFormBridge = {
        populate: function (result, isEditMode) {
            console.log('[TALENT PARSER] Form mapping started (isEditMode: ' + !!isEditMode + ')');

            if (!result || typeof result !== 'object') {
                var invalidResultErr = new Error('Cannot map non-object ATS result to talent form');
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

                // 1. Full Name (Enterprise Title Casing: 1st letter capital, rest small)
                if (cand.name) {
                    var formatTitleCase = function (str) {
                        if (!str) return '';
                        return String(str)
                            .toLowerCase()
                            .replace(/(^|[\s\-\'])([a-z])/g, function (match, separator, char) {
                                return separator + char.toUpperCase();
                            })
                            .trim();
                    };
                    var formattedCandName = formatTitleCase(cand.name);
                    safeSetVal('#full_name', formattedCandName, 'full_name');
                    safeSetVal('#name', formattedCandName);
                }

                // 2. Primary Email
                if (cand.email) {
                    safeSetVal('#email', cand.email, 'email');
                }

                // 3. Primary Mobile & Country Code
                var phoneVal = cand.mobileNumber || cand.phone;
                if (phoneVal) {
                    var digitsOnly = String(phoneVal).replace(/\D/g, '');
                    if (digitsOnly.length > 10) {
                        digitsOnly = digitsOnly.slice(-10);
                    }
                    if (window.createTalentPhoneInputs && window.createTalentPhoneInputs.primary && typeof window.createTalentPhoneInputs.primary.setNumber === 'function') {
                        try {
                            window.createTalentPhoneInputs.primary.setNumber(digitsOnly);
                        } catch (_) {}
                    }
                    safeSetVal('#phone', digitsOnly, 'phone');
                }
                if (cand.countryCode) {
                    var cc = String(cand.countryCode).replace(/^\+/, '').trim();
                    safeSetVal('#primary_country_code', cc);
                }

                // 4. Date of Birth (Normalized to YYYY-MM-DD)
                if (cand.dob) {
                    var normalizedDob = normalizeDob(cand.dob);
                    if (normalizedDob) {
                        safeSetVal('#dob', normalizedDob, 'dob');
                    }
                }

            // 5. Location Tagify Bridge
            var parsedLocData = cand.location || cand.city || (result && (result.location || result.city));
            if (parsedLocData) {
                if (typeof window.setSingleLocationTag === 'function') {
                    window.setSingleLocationTag(parsedLocData);
                } else if (typeof window.resolveAndSetCandidateLocation === 'function') {
                    window.resolveAndSetCandidateLocation(parsedLocData);
                } else {
                    var locInput = document.querySelector('#kt_tagify_work_location');
                    var tagifyLoc = (locInput && locInput.__tagify) || window.tagifylocation;
                    if (tagifyLoc && typeof tagifyLoc.addTags === 'function') {
                        try {
                            tagifyLoc.removeAllTags();
                            tagifyLoc.addTags([parsedLocData]);
                        } catch (_) {}
                    }
                }
            }

            // 5b. Nationality Auto-Detection (Client RAM In-Memory Matcher)
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

            // 6. Skills: Bench Candidates Skills Experience Table + Tagify
            var skillsList = Array.isArray(result.skills) ? result.skills : [];
            if (skillsList.length) {
                // Populate Bench Skills Table via authoritative window.addParsedBenchSkills
                if (typeof window.addParsedBenchSkills === 'function') {
                    try {
                        window.addParsedBenchSkills(skillsList);
                    } catch (skErr) {
                        console.warn('[TALENT PARSER] addParsedBenchSkills warning:', skErr);
                    }
                }

                // Also populate Tagify skills input if present
                var skillInput = document.querySelector('#kt_tagify_2');
                var tagifySkill = (skillInput && skillInput.__tagify) || window.tagifyskill;
                if (tagifySkill && typeof tagifySkill.addTags === 'function') {
                    var uniqueSkills = [];
                    var seen = new Set();
                    skillsList.forEach(function (s) {
                        var name = String(s || '').trim();
                        var lower = name.toLowerCase();
                        if (name && !seen.has(lower)) {
                            seen.add(lower);
                            uniqueSkills.push(name);
                        }
                    });
                    try {
                        if (!isEditMode) tagifySkill.removeAllTags();
                        tagifySkill.addTags(uniqueSkills);
                    } catch (_) {}
                }
            }

                // 7. Phase 9: Experience & Fresher Selection
                var expList = Array.isArray(result.experience) ? result.experience : (Array.isArray(result.work_experience) ? result.work_experience : []);
                var totalExp = calculateTotalExperience(expList, result.summary || result.career_summary || result.objective || (cand && (cand.summary || cand.objective)));

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
                    console.error('[SFHP] Bench Current Company error:', e);
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
                    console.error('[SFHP] Bench Designation error:', e);
                }

                // 7c. Experience Dropdowns (Years & Months)
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
                    console.error('[SFHP] Bench Experience error:', e);
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
                            }
                        }
                        $('#qualification').trigger('change');
                    }
                } catch (e) {
                    console.error('[SFHP] Bench Qualification error:', e);
                }

                // 9. Career Summary (strictly capped at 100 words, updates counter)
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
                            if (csEl.parentElement) {
                                var csCc = csEl.parentElement.querySelector('.cc, #summary_counter');
                                if (csCc) {
                                    var actualWordCount = summary.trim().split(/\s+/).filter(Boolean).length;
                                    csCc.textContent = actualWordCount + ' / 100';
                                }
                            }
                        }
                    }
                } catch (e) {
                    console.error('[SFHP] Bench Career summary error:', e);
                }

                // 10. Professional Links
                try {
                    var social = result.social || {};
                    if (cand.linkedin || social.linkedin) {
                        safeSetVal('#linkedin', cand.linkedin || social.linkedin);
                    }
                    if (cand.github || social.github) {
                        safeSetVal('#github', cand.github || social.github);
                    }
                    if (cand.behance || social.behance) {
                        safeSetVal('#behance', cand.behance || social.behance);
                    }
                } catch (e) {
                    console.error('[SFHP] Bench Professional links error:', e);
                }

            } catch (popErr) {
                console.error('[TALENT PARSER] SFHP Hydration error:', popErr);
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
                    var validator = window.benchFormValidator || window.candidateFormValidator;
                    if (validator && typeof validator.revalidateField === 'function') {
                        populatedFields.forEach(function (f) {
                            try { validator.revalidateField(f); } catch (_) {}
                        });
                    }
                }, 60);

                console.log('[TALENT PARSER] Form mapping completed successfully via SFHP');

                try {
                    var evt = new CustomEvent('jero:bench-resume-parsed', {
                        detail: { atsJson: result, isEditMode: isEditMode }
                    });
                    window.dispatchEvent(evt);
                } catch (_) {}
            }
        }
    };

    /**
     * Parses the given File in browser RAM using parser.jero.works WASM bundle.
     * Takes an immediate detached ArrayBuffer snapshot to prevent stream detachment.
     */
    function parseAndPopulate(file, options) {
        options = options || {};
        _activeFile = file;

        var onProgress = options.onProgress || function () {};
        var onSuccess = options.onSuccess || function () {};
        var onError = options.onError || function () {};
        var requiredSkills = options.requiredSkills || [];
        var isEditMode = !!options.isEditMode;

        console.log('[TALENT PARSER] File selected for parsing:', file ? (file.name + ' (' + file.size + ' bytes, ' + file.type + ')') : 'None');

        if (!file) {
            var missingFileErr = new Error('No resume file provided for parsing');
            reportFailure('File Selection', missingFileErr, file);
            onError(missingFileErr);
            return Promise.reject(missingFileErr);
        }

        var thisRunId = ++_currentParserRunId;
        var startTime = performance.now();
        onProgress('Initializing in-browser parser...', 5);

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
                    console.log('[TALENT PARSER] In-memory buffer snapshot created (' + rawBuf.byteLength + ' bytes)');
                }
            } catch (snapErr) {
                console.warn('[TALENT PARSER] Buffer snapshot fallback:', snapErr);
                selectedFile = file;
            }

            var mod = await loadParserModule();
            var parseResumeFn = mod.parseResume || (mod.default && mod.default.parseResume);
            if (typeof parseResumeFn !== 'function') {
                throw new Error('parseResume function not available on imported module');
            }

            if (thisRunId !== _currentParserRunId) {
                console.log('[TALENT PARSER] Parse run superseded by run ' + _currentParserRunId);
                return null;
            }

            console.log('[TALENT PARSER] Parse started in browser RAM');
            onProgress('Parsing resume in browser RAM...', 20);

            return parseResumeFn(selectedFile, {
                requiredSkills: requiredSkills,
                onProgress: function (stage, pct) {
                    if (thisRunId === _currentParserRunId) {
                        onProgress(stage, pct);
                    }
                }
            });
        })()
            .then(function (result) {
                if (thisRunId !== _currentParserRunId) {
                    console.log('[TALENT PARSER] Discarding stale parse result for run ' + thisRunId);
                    return null;
                }

                var totalMs = Math.round(performance.now() - startTime);
                console.log('[TALENT PARSER] Parse completed in ' + totalMs + 'ms');

                if (!result || typeof result !== 'object') {
                    throw new Error('parseResume() returned invalid or empty ATS JSON');
                }

                _lastParsedResult = result;

                // Form mapping via Bench Candidates Bridge
                JeroBenchFormBridge.populate(result, isEditMode);

                onSuccess(result, totalMs);
                return result;
            })
            .catch(function (err) {
                if (thisRunId !== _currentParserRunId) return null;
                reportFailure('parseResume Execution', err, selectedFile);
                onError(err);
                return null;
            });
    }

    // Expose global namespaces for Add Talent Flow (Rule 17)
    window.JeroBenchFormBridge = JeroBenchFormBridge;
    window.JeroBenchClientParser = {
        load: loadParserModule,
        parseAndPopulate: parseAndPopulate,
        calculateTotalExperience: calculateTotalExperience,
        matchQualification: matchQualification,
        normalizeDob: normalizeDob,
        cancelActiveParse: cancelActiveParse,
        getLastResult: function () { return _lastParsedResult; },
        getActiveFile: function () { return _activeFile; },
        PRODUCTION_PARSER_URL: PRODUCTION_PARSER_URL,
        FALLBACK_PARSER_URL: FALLBACK_PARSER_URL
    };

    // Pre-warm the parser module during idle time from production URL
    if (typeof window !== 'undefined') {
        var prewarm = function () {
            loadParserModule().catch(function (e) {
                console.warn('[TALENT PARSER] Idle pre-warm deferred:', e && e.message ? e.message : e);
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
