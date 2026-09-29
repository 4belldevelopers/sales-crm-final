<script>
/**
 * jero-bench-parser.blade.php — 100% Isolated Client-Side Resume Parser Integration Bridge
 *
 * Production URL: https://parser.jero.works/jero-resume-parser-2tURGwDx.js
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

    var PRODUCTION_PARSER_URL = 'https://parser.jero.works/jero-resume-parser-2tURGwDx.js';
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
     * Loads production parser module via browser dynamic import.
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

            import(PRODUCTION_PARSER_URL)
                .then(function (mod) {
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
                if (window.createTalentPhoneInputs && window.createTalentPhoneInputs.primary && typeof window.createTalentPhoneInputs.primary.setNumber === 'function') {
                    try {
                        window.createTalentPhoneInputs.primary.setNumber(digitsOnly);
                    } catch (_) {}
                }
                setVal('#phone', digitsOnly);
            }
            if (cand.countryCode) {
                var cc = String(cand.countryCode).replace(/^\+/, '').trim();
                setVal('#primary_country_code', cc);
            }

            // 4. Date of Birth (Normalized to YYYY-MM-DD)
            if (cand.dob) {
                var normalizedDob = normalizeDob(cand.dob);
                if (normalizedDob) {
                    setVal('#dob', normalizedDob);
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

            // 7. Experience & Current Role
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

            // 9. Career Summary (strictly capped at 100 words, updates counter)
            var summary = result.summary || '';
            if (summary) {
                var words = String(summary).trim().split(/\s+/).filter(Boolean);
                if (words.length > 100) {
                    summary = words.slice(0, 100).join(' ');
                }
                setVal('#careersummary', summary);
                var csEl = document.querySelector('#careersummary');
                if (csEl && csEl.parentElement) {
                    var csCc = csEl.parentElement.querySelector('.cc');
                    if (csCc) {
                        var actualWordCount = summary.trim().split(/\s+/).filter(Boolean).length;
                        csCc.textContent = actualWordCount + ' / 100';
                    }
                }
            }

            // 10. Professional Links
            var social = result.social || {};
            if (cand.linkedin || social.linkedin) {
                setVal('#linkedin', cand.linkedin || social.linkedin);
            }
            if (cand.github || social.github) {
                setVal('#github', cand.github || social.github);
            }
            if (cand.behance || social.behance) {
                setVal('#behance', cand.behance || social.behance);
            }

            console.log('[TALENT PARSER] Form mapping completed successfully');

            try {
                var evt = new CustomEvent('jero:bench-resume-parsed', {
                    detail: { atsJson: result, isEditMode: isEditMode }
                });
                window.dispatchEvent(evt);
            } catch (_) {}
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
        PRODUCTION_PARSER_URL: PRODUCTION_PARSER_URL
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
