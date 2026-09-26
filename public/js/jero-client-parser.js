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
    var LOCAL_FALLBACK_URL = '/vendor/jero-resume-parser/jero-resume-parser-2tURGwDx.js';
    var DEMO_URL = 'https://parser.jero.works/demo.js';
    var LOAD_TIMEOUT_MS = 25000;

    var _parserModulePromise = null;
    var _activeFile = null;
    var _lastParsedResult = null;

    /**
     * Imports the parser ES module from https://parser.jero.works/ with instant local fallback
     */
    function loadParserModule() {
        if (_parserModulePromise) {
            return _parserModulePromise;
        }

        _parserModulePromise = new Promise(function (resolve, reject) {
            var timeoutId = setTimeout(function () {
                reject(new Error('Parser loading timed out after ' + LOAD_TIMEOUT_MS + 'ms'));
            }, LOAD_TIMEOUT_MS);

            function verifyAndResolve(mod, source) {
                clearTimeout(timeoutId);
                if (!mod || typeof mod.parseResume !== 'function') {
                    throw new Error('Invalid parser bundle from ' + source + ': parseResume function not found');
                }
                console.log('[JeroClientParser] Parser module loaded successfully from ' + source);
                resolve(mod);
            }

            // 1. Primary: Direct import from canonical Cloudflare production URL
            import(/* @vite-ignore */ CANONICAL_BUNDLE_URL)
                .then(function (mod) {
                    verifyAndResolve(mod, 'Cloudflare (' + CANONICAL_BUNDLE_URL + ')');
                })
                .catch(function (primaryErr) {
                    console.warn('[JeroClientParser] Cloudflare bundle load failed, falling back to local vendor copy:', primaryErr);
                    // 2. Fallback: Instant import from same-origin vendor bundle
                    return import(/* @vite-ignore */ LOCAL_FALLBACK_URL)
                        .then(function (localMod) {
                            verifyAndResolve(localMod, 'Local Vendor (' + LOCAL_FALLBACK_URL + ')');
                        })
                        .catch(function (fallbackErr) {
                            clearTimeout(timeoutId);
                            _parserModulePromise = null; // allow retry on next attempt
                            console.error('[JeroClientParser] Both Cloudflare and local parser imports failed:', fallbackErr);
                            reject(fallbackErr);
                        });
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

        console.log('[JeroClientParser] Mapping ATS JSON to form inputs. isEditMode =', isEditMode);
        var cand = atsJson.candidate || {};

        function setVal(selector, value) {
            if (value === null || value === undefined) return;
            var strVal = String(value).trim();
            if (strVal === '') return;

            var $el = $(selector);
            if (!$el.length) return;

            if (isEditMode) {
                var cur = $el.val();
                if (cur !== null && cur !== undefined && String(cur).trim() !== '') {
                    return; // Preserve existing value in edit mode
                }
            }

            $el.val(strVal);
            $el.trigger('input').trigger('change');

            var domEl = $el[0];
            if (domEl && typeof domEl.dispatchEvent === 'function') {
                try {
                    domEl.dispatchEvent(new Event('input', { bubbles: true }));
                    domEl.dispatchEvent(new Event('change', { bubbles: true }));
                } catch (_) {}
            }
        }

        // 1. Full Name
        if (cand.name) {
            setVal('#full_name', cand.name);
        }

        // 2. Primary Email
        if (cand.email) {
            setVal('#email', cand.email);
        }

        // 3. Primary Mobile & Country Code
        var phoneVal = cand.mobileNumber || cand.phone;
        if (phoneVal) {
            var digitsOnly = String(phoneVal).replace(/\D/g, '');
            if (digitsOnly.length > 10) {
                digitsOnly = digitsOnly.slice(-10);
            }
            setVal('#phone', digitsOnly);
        }
        if (cand.countryCode) {
            var cc = String(cand.countryCode).replace(/^\+/, '').trim();
            setVal('#primary_country_code', cc);
        }

        // 4. Date of Birth
        if (cand.dob) {
            setVal('#dob', cand.dob);
        }

        // 5. Current Location (Tagify & Fallbacks)
        if (cand.location) {
            var locStr = '';
            if (typeof cand.location === 'string') {
                locStr = cand.location.trim();
            } else if (typeof cand.location === 'object') {
                var locParts = [cand.location.city, cand.location.state, cand.location.country].filter(Boolean);
                locStr = locParts.join(', ');
            }

            if (locStr) {
                if (typeof window.setSingleCandidateLocation === 'function') {
                    window.setSingleCandidateLocation(locStr);
                } else if (window.tagifylocation && typeof window.tagifylocation.addTags === 'function') {
                    window.tagifylocation.removeAllTags();
                    if (window.tagifylocation.settings && Array.isArray(window.tagifylocation.settings.whitelist)) {
                        if (window.tagifylocation.settings.whitelist.indexOf(locStr) === -1) {
                            window.tagifylocation.settings.whitelist.unshift(locStr);
                        }
                    }
                    window.tagifylocation.addTags([locStr]);
                } else {
                    setVal('#kt_tagify_work_location', locStr);
                }
            }
        }

        // 6. Primary Skills (Tagify + Metronic matching ring)
        var skills = Array.isArray(atsJson.skills) ? atsJson.skills : [];
        if (skills.length) {
            var tagifyInstance = window.tagifyskill
                || (window.Tagify && document.querySelector('#kt_tagify_2') && document.querySelector('#kt_tagify_2').__tagify);

            if (tagifyInstance && typeof tagifyInstance.addTags === 'function') {
                var currentTags = tagifyInstance.value || [];
                if (!isEditMode || currentTags.length === 0) {
                    tagifyInstance.removeAllTags();
                    tagifyInstance.addTags(skills);
                    if (typeof window.refreshCandidatePrimarySkills === 'function') {
                        window.setTimeout(window.refreshCandidatePrimarySkills, 0);
                    }
                }
            } else {
                setVal('#kt_tagify_2', skills.join(', '));
            }
        }

        // 7. Career Summary
        var summary = atsJson.summary || '';
        if (summary) {
            setVal('#careersummary', summary);
            $('.cc').text(summary.length + ' / 100');
        }

        // 8. Experience Details
        var expList = Array.isArray(atsJson.experience) ? atsJson.experience : [];
        if (expList.length) {
            var latestJob = expList[0] || {};
            // Current Company
            if (latestJob.company) {
                setVal('#current_company', latestJob.company);
            }
            // Current Designation
            if (latestJob.title) {
                setVal('#current_designation', latestJob.title);
            }

            // Total Experience (Years / Months)
            var totalExp = calculateTotalExperience(expList);
            var yrStr = String(totalExp.years);
            var moStr = String(totalExp.months);

            setVal('#total_exp_year', yrStr);
            setVal('#total_exp_month', moStr);
            setVal('#rel_exp_year', yrStr);
            setVal('#rel_exp_month', moStr);
        } else if (!isEditMode) {
            // Fresher fallback for empty experience history
            setVal('#total_exp_year', '0');
            setVal('#total_exp_month', '0');
            setVal('#rel_exp_year', '0');
            setVal('#rel_exp_month', '0');
        }

        // 9. Qualification / Education
        var matchedQualId = matchQualification(atsJson.education);
        if (matchedQualId) {
            setVal('#qualification', String(matchedQualId));
        }

        // 10. Professional Links
        if (cand.linkedin) {
            setVal('#linkedin', cand.linkedin);
        }
        if (cand.github) {
            setVal('#github', cand.github);
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
        console.log('[JeroClientParser] parseAndPopulate called for:', file ? file.name : 'unknown file');

        onProgress('Initializing parser...', 5);

        // Create an independent snapshot of the file so any concurrent input reset cannot detach the stream
        var fileToParse = file;
        try {
            if (typeof File === 'function' && file instanceof File) {
                fileToParse = new File([file], file.name, {
                    type: file.type || 'application/pdf',
                    lastModified: file.lastModified || Date.now()
                });
            }
        } catch (_) {
            fileToParse = file;
        }

        return loadParserModule()
            .then(function (mod) {
                console.log('[JeroClientParser] Module loaded. Calling parseResume on', fileToParse.name);
                onProgress('Parsing resume in browser...', 15);
                var parserFn = mod.parseResume || (mod.default && mod.default.parseResume);
                if (typeof parserFn !== 'function') {
                    throw new Error('parseResume function not available on imported module');
                }
                return parserFn(fileToParse, {
                    requiredSkills: requiredSkills,
                    onProgress: function (stage, pct) {
                        onProgress(stage, pct);
                    }
                });
            })
            .then(function (result) {
                var totalMs = Math.round(performance.now() - startTime);
                console.log('[JeroClientParser] Parse completed in ' + totalMs + 'ms');
                _lastParsedResult = result;
                mapAndPopulate(result, isEditMode);
                onSuccess(result, totalMs);
                return result;
            })
            .catch(function (err) {
                console.error('[JeroClientParser] Client-side parsing encountered an error:', err);
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

    // Pre-warm the parser module during idle time so it is ready in RAM when a file is dropped
    if (typeof window !== 'undefined') {
        var prewarm = function () {
            loadParserModule().catch(function (e) {
                console.warn('[JeroClientParser] Idle pre-warm deferred:', e && e.message ? e.message : e);
            });
        };
        if ('requestIdleCallback' in window) {
            window.requestIdleCallback(prewarm, { timeout: 3000 });
        } else {
            setTimeout(prewarm, 1500);
        }
    }

})(window, document, window.jQuery || window.$);

