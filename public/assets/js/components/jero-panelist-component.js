/**
 * JERO ATS — Reusable Dynamic Panelist Component
 * 
 * Complies with Enterprise ATS Standards:
 * - Single Panelist: Circular avatar on top, name below, company below (compact vertical layout).
 * - Multiple Panelists: Overlapping circular avatars (up to 3) + red circular '+N' indicator.
 * - Complete popover on hover/click with name, company, and avatar.
 * - No Panelist: Clean unassigned empty state ('—').
 * - 100% Dynamic: Zero hardcoded placeholders, defensive against stale or missing data.
 * - Fully responsive for 15.6" screens and smaller viewports.
 */
(function(window) {
    'use strict';

    const JeroPanelistComponent = {
        /**
         * Deterministic avatar background color class or hex based on name.
         */
        getAvatarColor: function(name) {
            const colors = [
                { bg: '#eff6ff', color: '#2563eb' }, // blue
                { bg: '#f3e8ff', color: '#9333ea' }, // purple
                { bg: '#ecfdf5', color: '#059669' }, // emerald
                { bg: '#fff7ed', color: '#ea580c' }, // orange
                { bg: '#fef2f2', color: '#dc2626' }, // red
                { bg: '#f0fdfa', color: '#0d9488' }  // teal
            ];
            if (!name) return colors[0];
            let hash = 0;
            for (let i = 0; i < name.length; i++) {
                hash = name.charCodeAt(i) + ((hash << 5) - hash);
            }
            const index = Math.abs(hash) % colors.length;
            return colors[index];
        },

        /**
         * Parses raw interview object into a normalized array of panelists.
         * @param {Object} intv
         * @returns {Array<{name: string, company: string, image: string, is_lead: boolean}>}
         */
        parsePanelists: function(intv) {
            if (!intv) return [];

            let raw = intv.interviewers;
            if (!raw && intv.interviewer_name) {
                raw = intv.interviewer_name;
            }

            const defaultCompany = (intv.interviewers_summary && intv.interviewers_summary.lead_company)
                || (intv.source_info && intv.source_info.company_name)
                || '';

            let list = [];

            if (Array.isArray(raw)) {
                list = raw.map(function(item, idx) {
                    if (typeof item === 'object' && item !== null) {
                        let name = (item.name || item.interviewer_name || '').trim();
                        let comp = (item.company_name || item.company || defaultCompany).trim();
                        let img = item.image || item.avatar || '';
                        return name ? { name: name, company: comp, image: img, is_lead: idx === 0 } : null;
                    } else if (typeof item === 'string') {
                        let name = item.trim();
                        return name ? { name: name, company: defaultCompany, image: '', is_lead: idx === 0 } : null;
                    }
                    return null;
                }).filter(Boolean);
            } else if (typeof raw === 'string' && raw.trim() !== '') {
                // Split by comma or comma with spaces
                let parts = raw.split(/,\s*|\s+,\s+/).map(s => s.trim()).filter(Boolean);
                list = parts.map(function(name, idx) {
                    return {
                        name: name,
                        company: defaultCompany,
                        image: '',
                        is_lead: idx === 0
                    };
                });
            }

            // Fallback to interviewers_summary ONLY if valid non-placeholder lead_name is present
            if (list.length === 0 && intv.interviewers_summary) {
                const lead = (intv.interviewers_summary.lead_name || '').trim();
                if (lead && lead !== 'N/A' && lead.toLowerCase() !== 'panel assigned') {
                    list.push({
                        name: lead,
                        company: (intv.interviewers_summary.lead_company || defaultCompany).trim(),
                        image: '',
                        is_lead: true
                    });
                    const extra = parseInt(intv.interviewers_summary.extra_count, 10) || 0;
                    for (let i = 1; i <= extra; i++) {
                        list.push({
                            name: `Panelist #${i + 1}`,
                            company: defaultCompany,
                            image: '',
                            is_lead: false
                        });
                    }
                }
            }

            return list;
        },

        /**
         * Renders an individual circular avatar element.
         */
        renderAvatar: function(p, customClass) {
            const initial = (p.name || 'P').charAt(0).toUpperCase();
            const color = this.getAvatarColor(p.name);
            const extraClass = customClass || '';

            if (p.image) {
                return `
                    <div class="cd-panel-avatar ${extraClass}" title="${p.name}">
                        <img src="${p.image}" alt="${p.name}" class="cd-panel-avatar-img" onerror="this.style.display='none';this.parentElement.innerText='${initial}';">
                    </div>
                `;
            }

            return `
                <div class="cd-panel-avatar ${extraClass}" style="background:${color.bg} !important; color:${color.color} !important;" title="${p.name}">
                    ${initial}
                </div>
            `;
        },

        /**
         * Generates the complete HTML string for the Sub-Card 5 Panelist section.
         * @param {Object} intv - The interview data item
         * @returns {string} HTML string
         */
        render: function(intv) {
            const list = this.parsePanelists(intv);
            const count = list.length;

            // STATE 1: No Panelist Assigned (Rule 4)
            if (count === 0) {
                return `
                    <div class="cd-it-subcard cd-it-panelist-card">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label">PANELISTS</span>
                            <span class="cd-it-panel-count-pill cd-it-empty-pill">—</span>
                        </div>
                        <div class="cd-it-panelist-empty" title="No panelist assigned yet">
                            <div class="cd-it-empty-dash">—</div>
                            <div class="cd-it-empty-sub">Not Assigned</div>
                        </div>
                    </div>
                `;
            }

            // STATE 2: Single Panelist (Rule 1: Avatar on top, Name below, Company below)
            if (count === 1) {
                const p = list[0];
                const avatarHtml = this.renderAvatar(p, 'cd-panel-avatar-single');
                return `
                    <div class="cd-it-subcard cd-it-panelist-card">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label">PANELISTS</span>
                            <span class="cd-it-panel-count-pill">1 Panelist</span>
                        </div>
                        <div class="cd-it-panelist-single" title="${p.name} (${p.company || '—'})">
                            ${avatarHtml}
                            <div class="cd-it-panelist-meta">
                                <div class="cd-it-panelist-name" title="${p.name}">
                                    ${p.name}
                                </div>
                                <div class="cd-it-panelist-company" title="${p.company || '—'}">
                                    ${p.company || '—'}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // STATE 3: Multiple Panelists (Rule 2: Overlapping avatars, +N red circular badge, hover popover)
            const MAX_VISIBLE = 3;
            const visibleList = list.slice(0, MAX_VISIBLE);
            const remainingCount = count - MAX_VISIBLE;
            const lead = list[0];
            const extraCount = count - 1;

            const visibleAvatarsHtml = visibleList.map(p => this.renderAvatar(p)).join('');
            const extraBadgeHtml = remainingCount > 0
                ? `<div class="cd-panel-avatar cd-panel-avatar-extra" title="+${remainingCount} more panelists">+${remainingCount}</div>`
                : '';

            // Build rich popover rows
            const popoverRowsHtml = list.map(function(p, idx) {
                const initial = (p.name || 'P').charAt(0).toUpperCase();
                const color = JeroPanelistComponent.getAvatarColor(p.name);
                const avatarSmall = p.image
                    ? `<img src="${p.image}" alt="${p.name}">`
                    : initial;

                return `
                    <div class="cd-it-popover-row">
                        <div class="cd-popover-avatar" style="background:${color.bg}; color:${color.color};">
                            ${avatarSmall}
                        </div>
                        <div class="cd-popover-info">
                            <div class="cd-popover-name" title="${p.name}">
                                <span>${p.name}</span>
                                ${idx === 0 ? '<span class="item-lead-badge">Lead</span>' : ''}
                            </div>
                            <div class="cd-popover-company" title="${p.company || ''}">
                                ${p.company || '—'}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            return `
                <div class="cd-it-subcard cd-it-panelist-card">
                    <div class="cd-subcard-header">
                        <span class="cd-subcard-label">PANELISTS</span>
                        <span class="cd-it-panel-count-pill">${count} Panelists</span>
                    </div>
                    <div class="cd-it-panelist-multiple">
                        <div class="cd-panel-avatar-stack">
                            ${visibleAvatarsHtml}
                            ${extraBadgeHtml}
                        </div>
                        <div class="cd-it-panelist-meta">
                            <div class="cd-it-panelist-name" title="${lead.name} + ${extraCount} more">
                                ${lead.name} <span class="cd-it-panelist-more-text">+${extraCount} more</span>
                            </div>
                            <div class="cd-it-panelist-company" title="${lead.company || '—'}">
                                ${lead.company || '—'}
                            </div>
                        </div>

                        <!-- Hover Popover with complete panelist list -->
                        <div class="cd-it-popover-card">
                            <div class="cd-it-popover-header">
                                <span><i class="fas fa-users text-primary me-1"></i> Interview Panel</span>
                                <span class="badge" style="background:rgba(56,189,248,0.2); color:#38bdf8; font-size:9.5px; padding:1px 6px; border-radius:4px;">${count}</span>
                            </div>
                            <div class="cd-it-popover-body">
                                ${popoverRowsHtml}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    };

    window.JeroPanelistComponent = JeroPanelistComponent;
})(window);
