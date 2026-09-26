/**
 * jero-client-parser.js — 100% Client-Side Resume Parser Integration Bridge
 *
 * Architecture:
 *   Browser (User RAM)
 *     ↓
 *   Loads https://parser.jero.works/ ES bundle (CORS allowed, immutable cache)
 *     ↓
 *   Local WebAssembly / JS execution (pdf.js, mammoth, layout, compromise NLP)
 *     ↓
 *   Structured ATS JSON output
 *     ↓
 *   Autofills Jero ATS Add/Edit Candidate Form in real time
 *     ↓
 *   Zero server-side requests (0% Laravel/PHP, 0% MySQL, 0% Elasticsearch)
 *
 * Tenancy & Concurrency:
 *   - 10,000 concurrent recruiters produce 0% CPU strain on Jero ATS backend.
 *   - Candidate data stays strictly in recruiter browser memory until form submission.
 *   - Full graceful degradation: if parser network is blocked, recruiter can manually enter details.
 */

(function (window, document, $) {
    'use strict';

    var PARSER_BASE_URL = 'https://parser.jero.works/';
    var CANONICAL_BUNDLE_URL = 'https://parser.jero.works/jero-resume-parser-2tURGwDx.js';
    var DEMO_URL = 'https://parser.jero.works/demo.js';
    var LOAD_TIMEOUT_MS = 25000;

    var _parserModulePromise = null;
    var _activeFile = null;
    var _lastParsedResult = null;

    /**
     * Discovers and dynamically imports the parser ES module from https://parser.jero.works/
     */
    function loadParserModule() {
        if (_parserModulePromise) {
            return _parserModulePromise;
        }

        _parserModulePromise = new Promise(function (resolve, reject) {
            var timeoutId = setTimeout(function () {
                reject(new Error('Parser loading timed out after ' + LOAD_TIMEOUT_MS + 'ms'));
            }, LOAD_TIMEOUT_MS);

            // Attempt dynamic bundle discovery via demo.js
            fetch(DEMO_URL, { cache: 'no-cache' })
                .then(function (res) {
                    if (!res.ok) {
                        throw new Error('HTTP ' + res.status + ' fetching demo.js');
                    }
                    return res.text();
                })
                .then(function (code) {
                    var match = code.match(/from\s+['"](\.\/jero-resume-parser-[^'"]+\.js)['"]/);
                    var bundleUrl = match && match[1]
                        ? new URL(match[1], PARSER_BASE_URL).href
                        : CANONICAL_BUNDLE_URL;
                    return import(/* @vite-ignore */ bundleUrl);
                })
                .catch(function (err) {
                    console.warn('[JeroParser] Dynamic bundle discovery failed, trying canonical bundle URL:', err);
                    return import(/* @vite-ignore */ CANONICAL_BUNDLE_URL);
                })
                .then(function (mod) {
                    clearTimeout(timeoutId);
                    if (!mod || typeof mod.parseResume !== 'function') {
                        throw new Error('Invalid parser bundle: parseResume function not found');
                    }
                    resolve(mod);
                })
                .catch(function (err) {
                    clearTimeout(timeoutId);
                    _parserModulePromise = null; // allow retry on next file pick
                    reject(err);
                });
        });

        return _parserModulePromise;
    }

    /**
     * Calculates total experience in years and months from experience entries
     * Merges overlapping date intervals to avoid duplicate counting.
     */
    function calculateTotalExperience(experienceList) {
        if (!Array.isArray(experienceList) || !experienceList.length) {
            return { years: 0, months: 0 };
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
            return { years: 0, months: 0 };
        }

        // Merge overlapping intervals
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
     * Fuzzy matches parsed education against available qualification dropdown options.
     */
    function matchQualification(educationList) {
        if (!Array.isArray(educationList) || !educationList.length) return null;
        var qualSelect = document.getElementById('qualification');
        if (!qualSelect || !qualSelect.options) return null;

        var options = qualSelect.options;

        for (var i = 0; i < educationList.length; i++) {
            var edu = educationList[i];
            var degreeRaw = String(edu.degree || '').toLowerCase().trim();
            var fieldRaw = String(edu.field || '').toLowerCase().trim();
            var combined = (degreeRaw + ' ' + fieldRaw).trim();

            if (!combined) continue;

            for (var j = 0; j < options.length; j++) {
                var opt = options[j];
                if (!opt.value) continue;
                var optText = opt.textContent.toLowerCase().trim();
                if (!optText) continue;

                // Direct exact match
                if (combined === optText || degreeRaw === optText) {
                    return opt.value;
                }

                // Standard degree token match
                if (/\bb\.?tech\b/i.test(degreeRaw) && /\bb\.?tech\b/i.test(optText)) return opt.value;
                if (/\bm\.?tech\b/i.test(degreeRaw) && /\bm\.?tech\b/i.test(optText)) return opt.value;
                if (/\bmba\b/i.test(degreeRaw) && /\bmba\b/i.test(optText)) return opt.value;
                if (/\bmca\b/i.test(degreeRaw) && /\bmca\b/i.test(optText)) return opt.value;
                if (/\bbca\b/i.test(degreeRaw) && /\bbca\b/i.test(optText)) return opt.value;
                if (/\bphd\b/i.test(degreeRaw) && /\bphd\b/i.test(optText)) return opt.value;
                if (/\bdoctorate\b/i.test(degreeRaw) && /\bdoctor/i.test(optText)) return opt.value;
                if (/\bdiploma\b/i.test(degreeRaw) && /\bdiploma\b/i.test(optText)) return opt.value;

                // Substring containment
                if (combined.indexOf(optText) !== -1 || optText.indexOf(degreeRaw) !== -1) {
                    return opt.value;
                }
            }
        }

        return null;
    }

    /**
     * Maps structured ATS JSON into Jero ATS form inputs.
     * Safe for EDIT mode: does not overwrite existing values in edit mode unless empty.
     */
    function mapAndPopulate(atsJson, isEditMode) {
        if (!atsJson || typeof atsJson !== 'object') return;

        var cand = atsJson.candidate || {};

        function shouldSet(inputEl) {
            if (!inputEl) return false;
            if (!isEditMode) return true;
            var val = $(inputEl).val();
            return val === null || val === undefined || String(val).trim() === '';
        }

        // 1. Full Name
        if (cand.name && shouldSet('#full_name')) {
            $('#full_name').val(cand.name).trigger('input').trigger('change');
        }

        // 2. Primary Email
        if (cand.email && shouldSet('#email')) {
            $('#email').val(cand.email).trigger('input').trigger('change');
        }

        // 3. Primary Mobile & Country Code
        var phoneVal = cand.mobileNumber || cand.phone;
        if (phoneVal && shouldSet('#phone')) {
            var digitsOnly = String(phoneVal).replace(/\D/g, '');
            if (digitsOnly.length > 10) {
                digitsOnly = digitsOnly.slice(-10);
            }
            $('#phone').val(digitsOnly).trigger('input').trigger('change');
        }
        if (cand.countryCode && shouldSet('#primary_country_code')) {
            var cc = String(cand.countryCode).replace(/^\+/, '').trim();
            $('#primary_country_code').val(cc);
        }

        // 4. Date of Birth
        if (cand.dob && shouldSet('#dob')) {
            $('#dob').val(cand.dob).trigger('input').trigger('change');
        }

        // 5. Current Location (Tagify)
        if (cand.location && typeof window.setSingleCandidateLocation === 'function') {
            var locStr = '';
            if (typeof cand.location === 'string') {
                locStr = cand.location.trim();
            } else if (typeof cand.location === 'object') {
                var locParts = [cand.location.city, cand.location.state, cand.location.country].filter(Boolean);
                locStr = locParts.join(', ');
            }
            if (locStr && shouldSet('#kt_tagify_work_location')) {
                window.setSingleCandidateLocation(locStr);
            }
        }

        // 6. Primary Skills (Tagify + Metronic matching ring)
        var skills = Array.isArray(atsJson.skills) ? atsJson.skills : [];
        if (skills.length && window.tagifyskill) {
            var currentTags = window.tagifyskill.value || [];
            if (!isEditMode || currentTags.length === 0) {
                window.tagifyskill.removeAllTags();
                window.tagifyskill.addTags(skills);
                if (typeof window.refreshCandidatePrimarySkills === 'function') {
                    window.setTimeout(window.refreshCandidatePrimarySkills, 0);
                }
            }
        }

        // 7. Career Summary
        var summary = atsJson.summary || '';
        if (summary && shouldSet('#careersummary')) {
            $('#careersummary').val(summary).trigger('input').trigger('change');
            $('.cc').text(summary.length + ' / 100');
        }

        // 8. Experience Details
        var expList = Array.isArray(atsJson.experience) ? atsJson.experience : [];
        if (expList.length) {
            var latestJob = expList[0] || {};
            // Current Company
            if (latestJob.company && shouldSet('#current_company')) {
                $('#current_company').val(latestJob.company).trigger('input').trigger('change');
            }
            // Current Designation
            if (latestJob.title && shouldSet('#current_designation')) {
                $('#current_designation').val(latestJob.title).trigger('input').trigger('change');
            }

            // Total Experience (Years / Months)
            var totalExp = calculateTotalExperience(expList);
            if (totalExp.years > 0 || totalExp.months > 0) {
                if (shouldSet('#total_exp_year')) {
                    $('#total_exp_year').val(totalExp.years).trigger('change');
                }
                if (shouldSet('#total_exp_month')) {
                    $('#total_exp_month').val(totalExp.months).trigger('change');
                }
                // Also default relevant experience to total experience if empty
                if (shouldSet('#rel_exp_year')) {
                    $('#rel_exp_year').val(totalExp.years).trigger('change');
                }
                if (shouldSet('#rel_exp_month')) {
                    $('#rel_exp_month').val(totalExp.months).trigger('change');
                }
            }
        }

        // 9. Qualification / Education
        var matchedQualId = matchQualification(atsJson.education);
        if (matchedQualId && shouldSet('#qualification')) {
            $('#qualification').val(matchedQualId).trigger('change');
        }

        // 10. Professional Links
        if (cand.linkedin && shouldSet('#linkedin')) {
            $('#linkedin').val(cand.linkedin).trigger('input').trigger('change');
        }
        if (cand.github && shouldSet('#github')) {
            $('#github').val(cand.github).trigger('input').trigger('change');
        }

        // Dispatch a custom window event for any observing components
        try {
            var evt = new CustomEvent('jero:resume-parsed', {
                detail: { atsJson: atsJson, isEditMode: isEditMode }
            });
            window.dispatchEvent(evt);
        } catch (_) {}
    }

    /**
     * Public API: parseAndPopulate(file, options)
     */
    function parseAndPopulate(file, options) {
        options = options || {};
        _activeFile = file;

        var onProgress = options.onProgress || function () {};
        var onSuccess = options.onSuccess || function () {};
        var onError = options.onError || function () {};
        var requiredSkills = options.requiredSkills || [];
        var isEditMode = !!options.isEditMode;

        var startTime = performance.now();

        onProgress('Initializing parser...', 5);

        return loadParserModule()
            .then(function (mod) {
                onProgress('Parsing resume in browser...', 15);
                return mod.parseResume(file, {
                    requiredSkills: requiredSkills,
                    onProgress: function (stage, pct) {
                        onProgress(stage, pct);
                    }
                });
            })
            .then(function (result) {
                var totalMs = Math.round(performance.now() - startTime);
                _lastParsedResult = result;
                mapAndPopulate(result, isEditMode);
                onSuccess(result, totalMs);
                return result;
            })
            .catch(function (err) {
                console.error('[JeroParser] Client-side parsing encountered an error:', err);
                onError(err);
                // Return null so the promise chain does not crash caller
                return null;
            });
    }

    // Expose global namespace
    window.JeroClientParser = {
        load: loadParserModule,
        parseAndPopulate: parseAndPopulate,
        calculateTotalExperience: calculateTotalExperience,
        matchQualification: matchQualification,
        getLastResult: function () { return _lastParsedResult; },
        getActiveFile: function () { return _activeFile; },
        PARSER_BASE_URL: PARSER_BASE_URL,
        CANONICAL_BUNDLE_URL: CANONICAL_BUNDLE_URL
    };

})(window, document, window.jQuery || window.$);
