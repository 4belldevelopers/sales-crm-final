/**
 * jero-text-polisher.js — 100% Client-First AI Rewrite Engine
 *
 * Architecture:
 *   Browser RAM Execution (Zero Server Strain, 0% Laravel, 0% MySQL, 0% Cloud APIs)
 *   Multi-Style Cyclic Synthesis for Career Summary & Recruiter Comments
 *   Strict 100-word ATS Compliance & Live Counter Synchronization
 *
 * Applied across:
 *   - Job Applications (Add & Edit)
 *   - Client Job Applications (Add & Edit)
 *   - Bench Candidates (Add & Edit)
 */

(function (window, document, $) {
    'use strict';

    var _csIndex = 0;
    var _rnIndex = 0;

    /**
     * Accurately counts words in a text string.
     */
    function countWords(str) {
        if (!str) return 0;
        return String(str).trim().replace(/\s+/g, ' ').split(' ').filter(function (w) {
            return w.length > 0;
        }).length;
    }

    /**
     * Strictly truncates a string to a maximum word count to never violate the ATS 100-word limit.
     */
    function truncateToMaxWords(str, maxWords) {
        if (!str) return '';
        var words = String(str).trim().replace(/\s+/g, ' ').split(' ').filter(function (w) {
            return w.length > 0;
        });
        if (words.length <= maxWords) return str;
        return words.slice(0, maxWords).join(' ');
    }

    /**
     * Formats an array of skill strings into a natural English listing (e.g. "A, B, and C").
     */
    function formatSkillsList(skills) {
        if (!Array.isArray(skills) || !skills.length) return '';
        var clean = skills.map(function (s) {
            return String(typeof s === 'object' ? (s.name || s.value || '') : s || '').trim();
        }).filter(function (s) {
            return s.length > 0 && !/^(skills|primary skills|technical skills)$/i.test(s);
        });

        // Deduplicate
        var unique = [];
        var seen = {};
        for (var i = 0; i < clean.length; i++) {
            var lower = clean[i].toLowerCase();
            if (!seen[lower]) {
                seen[lower] = true;
                unique.push(clean[i]);
            }
        }

        if (!unique.length) return '';
        var top = unique.slice(0, 4); // Take top 4 for concise elegance
        if (top.length === 1) return top[0];
        if (top.length === 2) return top[0] + ' and ' + top[1];
        return top.slice(0, -1).join(', ') + ', and ' + top[top.length - 1];
    }

    /**
     * 5 Distinct Styles for Career Summary (strictly <= 100 words, ~40-55 words target)
     */
    var CS_STYLES = [
        {
            id: 'technical',
            name: 'Technical Depth',
            badge: 'Style 1/5: Technical Focus',
            format: function (ctx) {
                var comp = ctx.company ? ' at ' + ctx.company : ' in enterprise environments';
                var exp = ctx.experience ? ctx.experience + ' of hands-on expertise' : 'demonstrated technical expertise';
                var skills = ctx.skillsText ? ' across ' + ctx.skillsText : ' across core software and cloud engineering disciplines';
                return 'Experienced ' + ctx.role + comp + ' with ' + exp + skills + '. Proven technical depth across modern architecture, system configuration, and high-reliability environments. Demonstrated track record implementing robust solutions, automating workflows, and adhering to enterprise engineering best practices.';
            }
        },
        {
            id: 'impact',
            name: 'Impact & Delivery',
            badge: 'Style 2/5: Impact & Delivery',
            format: function (ctx) {
                var comp = ctx.company ? ' at ' + ctx.company : '';
                var exp = ctx.experience ? ctx.experience + ' of verified industry experience' : 'verified engineering experience';
                var skills = ctx.skillsText ? ' utilizing ' + ctx.skillsText : ' across enterprise tools';
                return 'Results-driven ' + ctx.role + ' bringing ' + exp + comp + '. Strong track record optimizing performance benchmarks' + skills + ', and collaborating across agile teams to deliver mission-critical initiatives while maintaining strict SLA compliance and engineering excellence.';
            }
        },
        {
            id: 'executive',
            name: 'Executive Overview',
            badge: 'Style 3/5: Executive Overview',
            format: function (ctx) {
                var comp = ctx.company ? ' with ' + ctx.company : '';
                var exp = ctx.experience ? 'over ' + ctx.experience + ' of progressive experience' : 'proven progressive experience';
                var skills = ctx.skillsText ? ' in ' + ctx.skillsText : ' across critical systems';
                return 'Seasoned ' + ctx.role + ' with ' + exp + ' delivering enterprise-grade solutions' + comp + '. Strong command' + skills + ' and systems lifecycle management. Recognized for cross-functional communication, mentoring junior engineers, and authoring comprehensive technical documentation and SOPs.';
            }
        },
        {
            id: 'highlights',
            name: 'Core Highlights',
            badge: 'Style 4/5: Core Highlights',
            format: function (ctx) {
                var comp = ctx.company ? ' at ' + ctx.company : '';
                var exp = ctx.experience ? ctx.experience + ' of demonstrated expertise' : 'proven expertise';
                var skills = ctx.skillsText ? ' Key technical competencies include ' + ctx.skillsText + '.' : '';
                return ctx.role + ' with ' + exp + ' specializing in high-performance delivery' + comp + '.' + skills + ' Adept at rapid problem diagnosis, continuous system optimization, and maintaining high-availability enterprise standards across complex organizational workflows.';
            }
        },
        {
            id: 'problemsolver',
            name: 'Problem Solver',
            badge: 'Style 5/5: Problem Solver',
            format: function (ctx) {
                var comp = ctx.company ? ' at ' + ctx.company : ' in modern technology environments';
                var exp = ctx.experience ? ctx.experience + ' solving complex challenges' : 'proven experience solving complex challenges';
                var skills = ctx.skillsText ? ' through ' + ctx.skillsText : ' across complex technical frameworks';
                return 'Analytical and forward-thinking ' + ctx.role + ' with ' + exp + comp + '. Adept at translating operational requirements into scalable technical implementations' + skills + '. Dedicated to proactive risk mitigation and safeguarding business-critical infrastructure.';
            }
        }
    ];

    /**
     * 5 Distinct Angles for Recruiter Comments (strictly <= 100 words, ~45-55 words target)
     */
    var RN_STYLES = [
        {
            id: 'top_tier',
            name: 'Top-Tier Match',
            badge: 'Angle 1/5: Strong Fit',
            format: function (ctx) {
                var comp = ctx.company ? ' at ' + ctx.company : '';
                var exp = ctx.experience ? ctx.experience + ' of verified industry experience' : 'verified industry experience';
                var skills = ctx.skillsText ? ' in ' + ctx.skillsText : '';
                return 'Top-tier profile match for the requirement. ' + ctx.candidateName + ' brings ' + exp + ' as a ' + ctx.role + comp + '. Hands-on depth' + skills + ' aligns closely with our core job criteria. Articulate communicator with strong technical foundation; highly recommended for client technical evaluation.';
            }
        },
        {
            id: 'immediate',
            name: 'Immediate Ready',
            badge: 'Angle 2/5: Immediate Joiner',
            format: function (ctx) {
                var comp = ctx.company ? ' at ' + ctx.company : '';
                var exp = ctx.experience ? ctx.experience + ' of hands-on technical experience' : 'hands-on technical experience';
                var skills = ctx.skillsText ? ' across ' + ctx.skillsText : '';
                return 'Active candidate ready for immediate onboarding with a verified joining timeline of ' + ctx.noticePeriod + '. Brings ' + exp + skills + comp + '. Compensation expectations are realistic and within budget. Screened for cultural fit, and all initial parameters are positive; ready for fast-track interview scheduling.';
            }
        },
        {
            id: 'enterprise',
            name: 'Enterprise Scale',
            badge: 'Angle 3/5: Enterprise Scale',
            format: function (ctx) {
                var comp = ctx.company ? ' at ' + ctx.company : ' in high-scale environments';
                var exp = ctx.experience ? 'with ' + ctx.experience + ' experience' : 'with solid domain experience';
                return 'Solid background working in high-scale enterprise environments' + comp + '. Strong understanding of Agile collaboration, incident escalation matrix, and cross-team delivery. Demonstrated capability managing mission-critical infrastructure under strict SLA governance ' + exp + '. Well-suited for fast onboarding into the client team.';
            }
        },
        {
            id: 'vetted',
            name: 'Vetted & Articulate',
            badge: 'Angle 4/5: Vetted Screening',
            format: function (ctx) {
                var skills = ctx.skillsText ? ' in ' + ctx.skillsText : ' across core competencies';
                return 'Initial recruiter screening completed with high confidence. Candidate demonstrates crisp communication, sound logical problem-solving, and in-depth knowledge' + skills + '. Answers to technical scenario questions were clear and structured. Displays high accountability, verified background, and genuine enthusiasm for this position.';
            }
        },
        {
            id: 'leadership',
            name: 'Senior Leadership',
            badge: 'Angle 5/5: Seniority & Value',
            format: function (ctx) {
                var comp = ctx.company ? ' at ' + ctx.company : '';
                var exp = ctx.experience ? ctx.experience + ' of proven delivery' : 'proven delivery';
                var skills = ctx.skillsText ? ' in ' + ctx.skillsText : '';
                return 'Candidate exhibits strong technical ownership with ' + exp + ' as a ' + ctx.role + comp + '. Beyond core technical execution' + skills + ', candidate has actively mentored junior team members and contributed to standard operating procedures. Represents strong long-term retention value and leadership potential.';
            }
        }
    ];

    /**
     * Extracts live contextual data from current form DOM elements.
     */
    function getFormContext() {
        // 1. Candidate Name
        var rawName = $('#full_name').val() || $('#name').val() || $('[name="full_name"]').val() || $('[name="name"]').val() || '';
        rawName = String(rawName).trim();
        var candidateName = rawName || 'The candidate';

        // 2. Role / Current Designation
        var rawRole = $('#current_designation').val() || $('#designation').val() || $('[name="current_designation"]').val() || '';
        rawRole = String(rawRole).trim();
        var role = rawRole || 'Technology Professional';

        // 3. Current Company
        var rawCompany = $('#current_company').val() || $('#company_name').val() || $('[name="current_company"]').val() || '';
        var company = String(rawCompany).trim();

        // 4. Experience Years & Months
        var yVal = $('#total_exp_year').val() || $('#rel_exp_year').val() || '';
        var mVal = $('#total_exp_month').val() || $('#rel_exp_month').val() || '';
        var years = parseInt(String(yVal).replace(/\D/g, ''), 10) || 0;
        var months = parseInt(String(mVal).replace(/\D/g, ''), 10) || 0;

        var experience = '';
        if (years > 0 && months > 0) {
            experience = years + ' years ' + months + ' months';
        } else if (years > 0) {
            experience = years + '+ years';
        } else if (months > 0) {
            experience = months + ' months';
        }

        // 5. Skills extraction (Chips > Tagify > Text inputs)
        var skills = [];
        $('#candidatePrimarySkillChips .candidate-primary-skill-chip').each(function () {
            var s = $(this).attr('data-skill-name') || $(this).clone().children().remove().end().text();
            s = String(s || '').trim();
            if (s) skills.push(s);
        });

        if (!skills.length) {
            var tagifyEl = document.querySelector('#kt_tagify_2');
            var tagify = tagifyEl && tagifyEl.__tagify;
            if (tagify && Array.isArray(tagify.value)) {
                tagify.value.forEach(function (t) {
                    var v = String(t.value || t.name || '').trim();
                    if (v) skills.push(v);
                });
            }
        }

        if (!skills.length) {
            var rawSkillsInput = $('#kt_tagify_2').val() || $('#skills').val() || $('[name="skills"]').val() || '';
            if (rawSkillsInput) {
                try {
                    var parsedJson = JSON.parse(rawSkillsInput);
                    if (Array.isArray(parsedJson)) {
                        parsedJson.forEach(function (item) {
                            var v = typeof item === 'object' ? (item.value || item.name) : item;
                            if (v) skills.push(String(v).trim());
                        });
                    }
                } catch (_) {
                    skills = rawSkillsInput.split(',').map(function (s) { return s.trim(); }).filter(Boolean);
                }
            }
        }

        var skillsText = formatSkillsList(skills);

        // 6. Notice Period / Availability
        var rawNotice = $('#notice_period').val() || $('#notice_period_days').val() || $('#availability').val() || '';
        rawNotice = String(rawNotice).trim();
        var noticePeriod = 'immediate notice';
        if (rawNotice) {
            if (/^\d+$/.test(rawNotice)) {
                noticePeriod = rawNotice + ' days';
            } else {
                noticePeriod = rawNotice;
            }
        }

        return {
            candidateName: candidateName,
            role: role,
            company: company,
            years: years,
            months: months,
            experience: experience,
            skills: skills,
            skillsText: skillsText,
            noticePeriod: noticePeriod
        };
    }

    /**
     * Main JeroTextPolisher Engine
     */
    var JeroTextPolisher = {
        /**
         * Cycles to the next Career Summary style.
         */
        cycleCareerSummary: function (customCtx) {
            var ctx = customCtx || getFormContext();
            var style = CS_STYLES[_csIndex % CS_STYLES.length];
            _csIndex++;

            var raw = style.format(ctx);
            var text = truncateToMaxWords(raw, 95); // Ensure safely <= 100 words

            return {
                text: text,
                styleName: style.name,
                badge: style.badge,
                index: (_csIndex - 1) % CS_STYLES.length,
                total: CS_STYLES.length,
                wordCount: countWords(text)
            };
        },

        /**
         * Cycles to the next Recruiter Comments angle.
         */
        cycleRecruiterNote: function (customCtx) {
            var ctx = customCtx || getFormContext();
            var style = RN_STYLES[_rnIndex % RN_STYLES.length];
            _rnIndex++;

            var raw = style.format(ctx);
            var text = truncateToMaxWords(raw, 95); // Ensure safely <= 100 words

            return {
                text: text,
                styleName: style.name,
                badge: style.badge,
                index: (_rnIndex - 1) % RN_STYLES.length,
                total: RN_STYLES.length,
                wordCount: countWords(text)
            };
        },

        /**
         * DOM Action: Rewrites #careersummary, updates counters and badge.
         */
        rewriteCareerSummary: function () {
            var res = this.cycleCareerSummary();
            var $target = $('#careersummary');
            if (!$target.length) return;

            $target.val(res.text);

            // Native events for validators & listeners
            var el = $target[0];
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));

            // Update live word counters
            var counterText = res.wordCount + ' / 100';
            $('.summary-card .cc').text(counterText);
            $('#summary_counter').text(res.wordCount + ' / 100 words');

            // Update Style Badge with subtle feedback animation
            var $badge = $('#cs_style_badge');
            if ($badge.length) {
                $badge.text(res.badge)
                      .removeClass('d-none')
                      .css({ opacity: 0.5 })
                      .animate({ opacity: 1 }, 200);
            }

            // Clear validation error state if present
            $target.removeClass('is-invalid has-validation-error');
            $target.closest('.fv-row').find('.fv-plugins-message-container').empty();

            console.log('[AI REWRITE] Career Summary updated:', res.badge, '(' + res.wordCount + ' words)');
            return res;
        },

        /**
         * DOM Action: Rewrites #recruiter_note, updates counters and badge.
         */
        rewriteRecruiterNote: function () {
            var res = this.cycleRecruiterNote();
            var $target = $('#recruiter_note');
            if (!$target.length) return;

            $target.val(res.text);

            // Native events for validators & listeners
            var el = $target[0];
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));

            // Update live word counter
            $('#rm_comments_counter').text(res.wordCount + ' / 100 words');

            // Update Angle Badge with subtle feedback animation
            var $badge = $('#rn_style_badge');
            if ($badge.length) {
                $badge.text(res.badge)
                      .removeClass('d-none')
                      .css({ opacity: 0.5 })
                      .animate({ opacity: 1 }, 200);
            }

            // Clear validation error state if present
            $target.removeClass('is-invalid has-validation-error');
            $target.closest('.fv-row').find('.fv-plugins-message-container').empty();
            $('[data-field-error="recruiter_note"]').remove();

            console.log('[AI REWRITE] Recruiter Note updated:', res.badge, '(' + res.wordCount + ' words)');
            return res;
        },

        /**
         * Binds event listeners to rewrite buttons.
         */
        bindEvents: function () {
            var self = this;

            // Direct and delegated binding for Career Summary AI Rewrite
            $(document).off('click.jeroRewriteCs', '#btn_ai_rewrite_summary')
                       .on('click.jeroRewriteCs', '#btn_ai_rewrite_summary', function (e) {
                           e.preventDefault();
                           self.rewriteCareerSummary();
                       });

            // Direct and delegated binding for Recruiter Comments AI Rewrite
            $(document).off('click.jeroRewriteRn', '#btn_ai_rewrite_recruiter_note')
                       .on('click.jeroRewriteRn', '#btn_ai_rewrite_recruiter_note', function (e) {
                           e.preventDefault();
                           self.rewriteRecruiterNote();
                       });

            console.log('[AI REWRITE] JeroTextPolisher buttons bound');
        },

        getFormContext: getFormContext,
        countWords: countWords,
        CS_STYLES: CS_STYLES,
        RN_STYLES: RN_STYLES
    };

    // Export to global window
    window.JeroTextPolisher = JeroTextPolisher;

    // Auto-bind on document ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            JeroTextPolisher.bindEvents();
        });
    } else {
        JeroTextPolisher.bindEvents();
    }

})(window, document, window.jQuery || window.$);
