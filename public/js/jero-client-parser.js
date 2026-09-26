/**
 * jero-client-parser.js — 100% Client-Side Resume Parser Integration Bridge
 *
 * Production URL: https://parser.jero.works/jero-resume-parser-2tURGwDx.js
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

    var PRODUCTION_PARSER_URL = 'https://parser.jero.works/jero-resume-parser-2tURGwDx.js';
    var LOAD_TIMEOUT_MS = 30000;

    var _parserModulePromise = null;
    var _activeFile = null;
    var _lastParsedResult = null;

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
     * Phase 2: Prove dynamic import from https://parser.jero.works/jero-resume-parser-2tURGwDx.js
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

            import(/* @vite-ignore */ PRODUCTION_PARSER_URL)
                .then(function (mod) {
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
     */
    function matchQualification(educationList, selectEl) {
        if (!Array.isArray(educationList) || !educationList.length) return null;
        var qualSelect = selectEl || document.getElementById('qualification');
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

                if (combined === optText || degreeRaw === optText) {
                    return opt.value;
                }

                if (/\bb\.?tech\b/i.test(degreeRaw) && /\bb\.?tech\b/i.test(optText)) return opt.value;
                if (/\bm\.?tech\b/i.test(degreeRaw) && /\bm\.?tech\b/i.test(optText)) return opt.value;
                if (/\bmba\b/i.test(degreeRaw) && /\bmba\b/i.test(optText)) return opt.value;
                if (/\bmca\b/i.test(degreeRaw) && /\bmca\b/i.test(optText)) return opt.value;
                if (/\bbca\b/i.test(degreeRaw) && /\bbca\b/i.test(optText)) return opt.value;
                if (/\bphd\b/i.test(degreeRaw) && /\bphd\b/i.test(optText)) return opt.value;
                if (/\bdoctorate\b/i.test(degreeRaw) && /\bdoctor/i.test(optText)) return opt.value;
                if (/\bdiploma\b/i.test(degreeRaw) && /\bdiploma\b/i.test(optText)) return opt.value;

                if (combined.indexOf(optText) !== -1 || optText.indexOf(degreeRaw) !== -1) {
                    return opt.value;
                }
            }
        }

        return null;
    }

    /**
     * Phase 5: Canonical Integration Layer — JeroCandidateFormBridge
     */
    var JeroCandidateFormBridge = {
        populate: function (result, isEditMode) {
            console.log('[PARSER 9] Form mapping started (isEditMode: ' + !!isEditMode + ')');

            if (!result || typeof result !== 'object') {
                var invalidResultErr = new Error('Cannot map non-object ATS result to form');
                reportFailure('Form Mapping (Validate Object)', invalidResultErr, _activeFile);
                return;
            }

            var cand = result.candidate || {};

            function setVal(selector, value) {
                if (value === null || value === undefined) return;
                var strVal = String(value).trim();
                if (strVal === '') return;

                var $el = $(selector);
                if (!$el.length) return;

                if (isEditMode) {
                    var cur = $el.val();
                    if (cur !== null && cur !== undefined && String(cur).trim() !== '') {
                        return; // Preserve existing values in edit mode
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
                setVal('#name', cand.name);
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

            // 4. Phase 10: Date of Birth (Normalized to YYYY-MM-DD)
            if (cand.dob) {
                var normalizedDob = normalizeDob(cand.dob);
                if (normalizedDob) {
                    setVal('#dob', normalizedDob);
                }
            }

            // 5. Phase 7: Location Population via Canonical Tagify Bridge
            if (cand.location) {
                var locStr = '';
                if (typeof cand.location === 'string') {
                    locStr = cand.location.trim();
                } else if (typeof cand.location === 'object') {
                    var locParts = [cand.location.city, cand.location.state, cand.location.country].filter(Boolean);
                    locStr = locParts.join(', ');
                }

                if (locStr) {
                    var locInput = document.querySelector('#kt_tagify_work_location');
                    var tagifyLoc = (locInput && locInput.__tagify) || window.tagifylocation;
                    if (tagifyLoc && typeof tagifyLoc.addTags === 'function') {
                        try {
                            tagifyLoc.removeAllTags();
                            if (tagifyLoc.settings && Array.isArray(tagifyLoc.settings.whitelist)) {
                                if (tagifyLoc.settings.whitelist.indexOf(locStr) === -1) {
                                    tagifyLoc.settings.whitelist.unshift(locStr);
                                }
                            }
                            tagifyLoc.addTags([locStr]);
                            if (tagifyLoc.dropdown && typeof tagifyLoc.dropdown.hide === 'function') {
                                tagifyLoc.dropdown.hide();
                            }
                        } catch (locErr) {
                            console.warn('[JeroCandidateFormBridge] Tagify location error, using fallback:', locErr);
                            setVal('#kt_tagify_work_location', locStr);
                        }
                    } else if (typeof window.setSingleCandidateLocation === 'function') {
                        window.setSingleCandidateLocation(locStr);
                    } else {
                        setVal('#kt_tagify_work_location', locStr);
                    }
                }
            }

            // 6. Phase 8: Skills Population via Tagify Bridge
            var skillsList = Array.isArray(result.skills) ? result.skills : [];
            if (skillsList.length) {
                var skillInput = document.querySelector('#kt_tagify_2');
                var tagifySkill = (skillInput && skillInput.__tagify) || window.tagifyskill;

                var uniqueSkills = [];
                var seen = new Set();
                if (isEditMode && tagifySkill && Array.isArray(tagifySkill.value)) {
                    tagifySkill.value.forEach(function (t) {
                        var v = (t.value || t.name || '').toLowerCase();
                        if (v) seen.add(v);
                    });
                }

                skillsList.forEach(function (s) {
                    var name = String(s || '').trim();
                    var lower = name.toLowerCase();
                    if (name && !seen.has(lower) && uniqueSkills.length < 15) {
                        seen.add(lower);
                        uniqueSkills.push(name);
                    }
                });

                if (tagifySkill && typeof tagifySkill.addTags === 'function') {
                    try {
                        if (!isEditMode) {
                            tagifySkill.removeAllTags();
                        }
                        if (uniqueSkills.length) {
                            tagifySkill.addTags(uniqueSkills);
                        }
                        if (typeof window.refreshCandidatePrimarySkills === 'function') {
                            window.refreshCandidatePrimarySkills();
                        }
                    } catch (skillErr) {
                        console.warn('[JeroCandidateFormBridge] Tagify skills error, using fallback:', skillErr);
                        setVal('#kt_tagify_2', uniqueSkills.join(', '));
                    }
                } else {
                    setVal('#kt_tagify_2', uniqueSkills.join(', '));
                }
            }

            // 7. Phase 9: Experience & Fresher Selection
            var expList = Array.isArray(result.experience) ? result.experience : [];
            if (expList.length > 0) {
                var latestJob = expList[0] || {};
                if (latestJob.company) {
                    setVal('#current_company', latestJob.company);
                }
                if (latestJob.title) {
                    setVal('#current_designation', latestJob.title);
                }

                var totalExp = calculateTotalExperience(expList);
                setVal('#total_exp_year', String(totalExp.years));
                setVal('#total_exp_month', String(totalExp.months));
                setVal('#rel_exp_year', String(totalExp.years));
                setVal('#rel_exp_month', String(totalExp.months));
            } else if (!isEditMode) {
                // Explicitly select 0 for Fresher
                setVal('#total_exp_year', '0');
                setVal('#total_exp_month', '0');
                setVal('#rel_exp_year', '0');
                setVal('#rel_exp_month', '0');
            }

            // 8. Education / Qualification
            var matchedQualId = matchQualification(result.education, document.getElementById('qualification'));
            if (matchedQualId) {
                setVal('#qualification', String(matchedQualId));
            }

            // 9. Career Summary
            var summary = result.summary || '';
            if (summary) {
                setVal('#careersummary', summary);
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances['recruiter_note']) {
                    try {
                        CKEDITOR.instances['recruiter_note'].setData(summary);
                    } catch (_) {}
                }
                $('.cc').text(summary.length + ' / 100');
            }

            // 10. Professional Links
            if (cand.linkedin) {
                setVal('#linkedin', cand.linkedin);
            }
            if (cand.github) {
                setVal('#github', cand.github);
            }

            console.log('[PARSER 10] Form mapping completed');

            try {
                var evt = new CustomEvent('jero:resume-parsed', {
                    detail: { atsJson: result, isEditMode: isEditMode }
                });
                window.dispatchEvent(evt);
            } catch (_) {}
        }
    };

    /**
     * Phase 1 & 3: Selected File -> JeroClientParser -> https://parser.jero.works/ -> ATS JSON
     */
    function parseAndPopulate(file, options) {
        options = options || {};
        _activeFile = file;

        var onProgress = options.onProgress || function () {};
        var onSuccess = options.onSuccess || function () {};
        var onError = options.onError || function () {};
        var requiredSkills = options.requiredSkills || [];
        var isEditMode = !!options.isEditMode;

        // [PARSER 1] File selected
        console.log('[PARSER 1] File selected:', file ? (file.name + ' (' + file.size + ' bytes, ' + file.type + ')') : 'None');

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

            var mod = await loadParserModule();
            var parseResumeFn = mod.parseResume || (mod.default && mod.default.parseResume);
            if (typeof parseResumeFn !== 'function') {
                throw new Error('parseResume function not available on module');
            }

            // [PARSER 6] Parsing started
            console.log('[PARSER] file name:', selectedFile.name);
            console.log('[PARSER] file type:', selectedFile.type);
            console.log('[PARSER] file size:', selectedFile.size);
            console.log('[PARSER 6] Parsing started');
            console.log('[PARSER] parse started');
            onProgress('Parsing resume in browser...', 15);

            return parseResumeFn(selectedFile, {
                requiredSkills: requiredSkills,
                onProgress: function (stage, pct) {
                    onProgress(stage, pct);
                }
            });
        })()
            .then(function (result) {
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

                onSuccess(result, totalMs);
                return result;
            })
            .catch(function (err) {
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
        calculateTotalExperience: calculateTotalExperience,
        matchQualification: matchQualification,
        normalizeDob: normalizeDob,
        getLastResult: function () { return _lastParsedResult; },
        getActiveFile: function () { return _activeFile; },
        PRODUCTION_PARSER_URL: PRODUCTION_PARSER_URL
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
