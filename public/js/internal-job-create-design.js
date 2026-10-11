/** Create-only presentation: reorganize existing controls without replacing form state or request contracts.
 *  NOTE: This file is loaded synchronously (no defer) at the bottom of <body> so that it runs after
 *  the preceding inline scripts that define globals (currentStep, goToStep, __indexUrl).
 *  The document-ready guard below fires immediately when readyState is already 'interactive'
 *  or 'complete' (which can happen on fast/cached loads), and falls back to DOMContentLoaded
 *  for the first-load case. */
(function () {
    'use strict';
    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            // DOM already parsed — run immediately (handles sync-load at bottom of body)
            fn();
        }
    }
    onReady(function () {
        const root = document.getElementById('jrwrap');
        if (!root || !root.classList.contains('internal-job-create')) return;
        const content = root.querySelector('.content');
        const stepper = document.getElementById('stepper');
        const toolbar = document.getElementById('kt_toolbar');
        const header = document.createElement('header');
        header.className = 'ij-header';
        header.innerHTML = '<div class="ij-heading"><button type="button" class="ij-back-btn" aria-label="Go Back"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></button><div><h1>Add Internal Jobs</h1><p>Enter job details and publish</p></div></div><div class="ij-actions"><button type="button" class="btn ij-close" aria-label="Close and return to job listings"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg><span>Close</span></button><button type="button" class="btn ij-coin-rules"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="8" r="6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18"/><path d="M7 6h1v4"/></svg><span>Coin Rules</span></button><button type="button" class="btn btn-primary ij-next"><span>Next Page</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button></div>';
        if (toolbar) toolbar.remove();
        const wrapper = document.getElementById('kt_wrapper');
        if (wrapper) wrapper.style.paddingTop = '65px';
        content.prepend(header);
        header.insertBefore(stepper, header.querySelector('.ij-actions'));
        stepper.setAttribute('aria-label', 'Job creation steps');
        const next = header.querySelector('.ij-next');
        const closeBtn = header.querySelector('.ij-close');
        const backBtn = header.querySelector('.ij-back-btn');
        const coinRulesBtn = header.querySelector('.ij-coin-rules');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                var targetUrl = (typeof __indexUrl !== 'undefined' && __indexUrl) ? __indexUrl : '/service_company/jobs';
                window.location.assign(targetUrl);
            });
        }
        if (backBtn) {
            backBtn.addEventListener('click', function () {
                if (typeof currentStep !== 'undefined' && currentStep > 1) {
                    goToStep(currentStep - 1);
                } else {
                    var targetUrl = (typeof __indexUrl !== 'undefined' && __indexUrl) ? __indexUrl : '/service_company/jobs';
                    window.location.assign(targetUrl);
                }
            });
        }
        if (coinRulesBtn) {
            coinRulesBtn.addEventListener('click', function () {
                if (typeof showCoinRulesModal === 'function') {
                    showCoinRulesModal();
                } else {
                    showToast('Coin rules: 2 coins/day on marketplace, 1 coin/candidate contacted.', 'info');
                }
            });
        }
        const publish = root.querySelector('.submit-card .btn-primary');
        // Keep the submit-card ancestor required by the existing loading/error selector.
        const publishHost = document.createElement('div');
        publishHost.className = 'submit-card ij-publish';
        if (publish) {
            publish.className = 'btn btn-primary ij-btn-publish';
            publish.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m21.426 11.095-17-8A1 1 0 0 0 3.08 4.37l3.87 7.63-3.87 7.63a1 1 0 0 0 1.346 1.275l17-8a1 1 0 0 0 0-1.81zM6.16 13l-1.92 3.78L17.5 12 4.24 7.22 6.16 11H12v2H6.16z"/></svg><span>Publish Job</span>';
            publishHost.append(publish);
        }
        header.querySelector('.ij-actions').append(publishHost);
        next.addEventListener('click', function () { goToStep(currentStep + 1); });
        stepper.querySelectorAll('.step-v2').forEach(function (step) {
            step.setAttribute('role', 'button');
            step.tabIndex = 0;
            step.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); step.click(); }
            });
        });
        /** Reflect existing step state, including accessible status text and responsive scroll. */
        function refreshHeader() {
            stepper.querySelectorAll('.step-v2').forEach(function (step, index) {
                const number = index + 1;
                step.querySelector('.step-v2-desc').textContent = number < currentStep ? 'Completed' : number === currentStep ? 'Current Step' : 'Pending';
                if (number === currentStep) step.setAttribute('aria-current', 'step');
                else step.removeAttribute('aria-current');
            });
            const isStep4 = currentStep === 4;
            next.hidden = isStep4;
            publishHost.hidden = !isStep4;
            if (coinRulesBtn) coinRulesBtn.hidden = !isStep4;
            if (closeBtn) closeBtn.hidden = isStep4;
            const nextLabel = currentStep === 3 ? 'Review &amp; Publish' : 'Next Page';
            next.innerHTML = '<span>' + nextLabel + '</span> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
            const active = stepper.querySelector('.current');
            if (active) stepper.scrollLeft = Math.max(0, active.offsetLeft - stepper.offsetLeft - 8);
        }
        // Observe presentation changes made by the original navigation rather than replacing it.
        new MutationObserver(refreshHeader).observe(stepper, { attributes: true, subtree: true, attributeFilter: ['class'] });
        refreshHeader();
        root.querySelectorAll('.form-footer, .submit-card:not(.ij-publish), button[onclick^="saveDraft"]').forEach(function (el) {
            el.hidden = true;
            el.style.display = 'none';
        });

        const subtitles = {
            'Basic Information': 'Enter the basic details for this job opening.',
            'Job Location or Work Location': 'Select up to 15 cities where this job is based.',
            'Employment Details': 'Specify key employment details for this job opening.',
            'Compensation': 'Define the salary details for this position.',
            'Hiring Team': 'Assign the client contact and internal recruiters for this job position.',
            'Qualification / Education': 'Select up to 5 eligible educational qualifications or degrees.',
            'Additional Details': 'Configure job closing duration and preferences.',
            'Job Description': 'Provide a detailed and clear job description.',
            'Key Skills': 'Add the key skills required for this position.'
        };
        root.querySelectorAll('.section-title').forEach(function (title) {
            const label = title.textContent.trim();
            const subtitle = subtitles[label];
            if (!subtitle) return;
            const text = document.createElement('span');
            text.className = 'ij-section-copy';
            Array.from(title.childNodes).filter(function (node) { return node.nodeType === 3; }).forEach(function (node) { text.append(node); });
            const helper = document.createElement('small');
            helper.textContent = subtitle;
            text.append(helper);
            title.append(text);
        });

        /** Choice buttons are an accessible facade over the original select and its exact options. */
        function choiceSection(id, titleText, help, before) {
            if (!before) return;
            const select = document.getElementById(id);
            if (!select) return;
            const group = select.closest('.form-group-cus');
            if (!group) return;
            const card = document.createElement('section');
            card.className = 'card ij-choice-section';
            card.innerHTML = '<div class="card-body"><div class="section-title"><span class="ij-choice-icon" aria-hidden="true">' + (id === 'work_shifts' ? '◷' : '▣') + '</span><span class="ij-section-copy">' + titleText + '<small>' + help + '</small></span></div><div class="ij-choices" role="group" aria-label="' + titleText + '"></div></div>';
            card.querySelector('.ij-choice-icon').innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">' + (id === 'work_shifts' ? '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>' : '<rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V3h8v4M3 12h18"/>') + '</svg>';
            before.before(card);
            card.querySelector('.card-body').append(group);
            const choices = card.querySelector('.ij-choices');
            group.classList.add('ij-source-control');
            select.tabIndex = -1;
            const descriptions = {'Work From Office':'Based at office location', 'Remote':'Work from anywhere', 'Hybrid':'Office + Remote', 'Day Shift':'Regular daytime working hours', 'Night Shift':'Regular night working hours', 'Rotational shift':'Rotating work schedule', 'UK Timezone':'Based on UK working hours', 'US Timezone':'Based on US working hours', 'Full-time':'Permanent employment', 'Contract':'Project-based hiring', 'Freelance':'Independent contractor', 'Part Time':'Flexible working hours'};
            const orderPreferences = {
                'work_from_home': ['Remote', 'Hybrid', 'Work From Office']
            };
            let optionList = Array.from(select.options).filter(function (option) { return !option.disabled && option.value !== ''; });
            if (orderPreferences[id]) {
                const pref = orderPreferences[id];
                optionList.sort(function (a, b) {
                    const idxA = pref.indexOf(a.text.trim());
                    const idxB = pref.indexOf(b.text.trim());
                    return (idxA === -1 ? 999 : idxA) - (idxB === -1 ? 999 : idxB);
                });
            }
            optionList.forEach(function (option) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'ij-choice';
                button.dataset.value = option.value;
                const label = document.createElement('strong');
                label.textContent = option.text;
                const detail = document.createElement('small');
                detail.textContent = descriptions[option.text] || 'Select this employment option';
                button.append(label, detail);
                button.addEventListener('click', function () { select.value = option.value; select.dispatchEvent(new Event('change', { bubbles: true })); });
                choices.append(button);
            });
            function sync() {
                choices.querySelectorAll('button').forEach(function (button) { button.setAttribute('aria-pressed', String(button.dataset.value === select.value)); });
            }
            select.addEventListener('change', sync);
            if (window.jQuery) { window.jQuery(select).on('change', sync); }
            // Existing validation focuses this select; forward that focus to its visible choice.
            select.addEventListener('focus', function () {
                const target = choices.querySelector('[aria-pressed="true"]') || choices.querySelector('button');
                if (target) target.focus();
            });
            sync();
        }
        const minExpEl = document.getElementById('min_exp');
        const minSalEl = document.getElementById('minimum_salary');
        const employment = minExpEl ? minExpEl.closest('.card') : null;
        const compensation = minSalEl ? minSalEl.closest('.card') : null;
        if (employment) {
            choiceSection('employment_type', 'Position Type', 'Select the type of employment for this opening.', employment);
        }
        if (compensation) {
            choiceSection('work_from_home', 'Work Mode', 'Select the work mode for this position.', compensation);
            choiceSection('work_shifts', 'Work Shift', 'Select the work shift for this job.', compensation);
        }
        ['hmSearchDisplay', 'qualSearch'].forEach(function (id) {
            const control = document.getElementById(id);
            if (control) {
                const card = control.closest('.card');
                if (card) card.classList.add(id === 'qualSearch' ? 'ij-additional' : 'ij-team');
            }
        });
        root.querySelectorAll('label.form-label').forEach(function (label) {
            const control = label.parentElement.querySelector('input:not([type="hidden"]), select, textarea');
            if (control && control.id) label.htmlFor = control.id;
        });

        // Move existing cards in Step 2 into description grid
        const step2 = document.getElementById('step2');
        if (step2) {
            const descriptionGrid = document.createElement('div');
            descriptionGrid.className = 'ij-description-grid';
            step2.prepend(descriptionGrid);
            Array.from(step2.children).filter(function (child) { return child.classList.contains('card'); }).forEach(function (card) { descriptionGrid.append(card); });
        }
        // Keep header visible without overlapping the application shell at each viewport.
        function measureHeader() {
            const shell = document.getElementById('kt_header');
            const shellStyle = shell ? getComputedStyle(shell) : null;
            const top = shellStyle && ['fixed', 'sticky'].includes(shellStyle.position) ? Math.max(0, shell.getBoundingClientRect().bottom) : 0;
            root.style.setProperty('--ij-shell-top', top + 'px');
            root.style.setProperty('--ij-focus-offset', (top + header.offsetHeight + 16) + 'px');
        }
        measureHeader();
        window.addEventListener('resize', measureHeader);
    });
})();
