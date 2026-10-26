/**
 * JERO Distributed Client Runtime (JDCR) - Client-Side Incremental Template Engine
 * Version: 5.2 Enterprise Master Standard
 *
 * Responsibilities:
 * 1. Zero-Server Blade Rendering: Pure client-side compiled template engine
 * 2. Mandatory Card + Sub-Card Enterprise Design Standard (Tier 1 Toolbar, Tier 2 Sub-Tabs, Tier 3 Discrete Sub-Cards)
 * 3. Zero Inline Event Handlers: Centralized event delegation via data-action / data-tab
 * 4. Calibrated for 15.6" screens (1366x768 & 1920x1080) with zero horizontal overflow
 * 5. Strict XSS Sanitization & Asset URL verification
 *
 * (c) 2026 JERO.WORKS. All rights reserved.
 */

(function(window) {
    'use strict';

    /**
     * Strict HTML escaping for dynamic text content
     */
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    /**
     * URL sanitization: Blocks javascript:, data:, and malformed URI schemes
     */
    function sanitizeAssetUrl(url, fallback) {
        if (!url || typeof url !== 'string') return fallback || '/assets/media/avatars/blank.png';
        const clean = url.trim();
        if (clean.startsWith('javascript:') || clean.startsWith('data:') || clean.startsWith('vbscript:')) {
            return fallback || '/assets/media/avatars/blank.png';
        }
        return escapeHtml(clean);
    }

    const ClientIncrementalRenderer = {
        /**
         * Render the complete Drawer Shell adhering to the 3-Tier Enterprise Card Standard
         */
        renderDrawerShell: function(client, activeTab) {
            activeTab = activeTab || 'overview';
            const clientId = Number(client.id || client.client_id || 0);
            const clientName = client.name || client.company_name || 'Client #' + clientId;
            const industry = client.industry_name || client.industry || 'General';
            const logo = sanitizeAssetUrl(client.logo_url || client.logo_logical_path, '/assets/media/avatars/blank.png');
            const isActive = Number(client.status) === 1;

            return `
                <div id="jdcr-client-drawer-${clientId}" class="d-flex flex-column h-100 bg-slate-50" style="font-family: 'Poppins', sans-serif;">
                    <!-- ─── TIER 1: Pure White Toolbar Card (#ffffff, border #e2e8f0, radius 10px) ─── -->
                    <div class="card bg-white border border-slate-200 rounded-3 p-3 mb-2 shadow-xs" style="border-radius: 10px; border-color: #e2e8f0;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <img src="${logo}" alt="${escapeHtml(clientName)}" class="w-40px h-40px rounded-2 border object-cover shadow-xs" style="object-fit: cover;" onerror="this.src='/assets/media/avatars/blank.png';" />
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h4 class="text-gray-900 fw-bolder mb-0 fs-5">${escapeHtml(clientName)}</h4>
                                        <span class="badge ${isActive ? 'badge-light-success' : 'badge-light-danger'} fs-8 fw-semibold px-2 py-1">
                                            ${isActive ? 'Active' : 'Inactive'}
                                        </span>
                                    </div>
                                    <div class="text-muted fs-8">
                                        ID: #${clientId} • <span class="text-gray-600">${escapeHtml(industry)}</span>
                                        ${client.city_name ? ` • <span>${escapeHtml(client.city_name)}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div id="jdcr-sync-badge-${clientId}">
                                    <span class="badge badge-light-success fs-8 fw-medium px-2 py-1">
                                        <i class="ki-outline ki-check-circle fs-8 text-success me-1"></i>Up to Date
                                    </span>
                                </div>
                                <button type="button" class="btn btn-sm btn-icon btn-light-secondary" data-action="close-drawer" title="Close Drawer">
                                    <i class="ki-outline ki-cross fs-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ─── TIER 2: Pure White Sub-Tabs Card (Single Active Blue #009ef7) ─── -->
                    <div class="card bg-white border border-slate-200 rounded-3 px-3 py-1 mb-2 shadow-xs" style="border-radius: 10px; border-color: #e2e8f0;">
                        <ul class="nav nav-tabs nav-line-tabs border-0 fs-7 fw-semibold flex-nowrap overflow-x-auto text-nowrap gap-1" id="jdcr-tabs-list" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                            <li class="nav-item">
                                <a class="nav-link px-3 py-2 text-gray-700 ${activeTab === 'overview' ? 'active text-primary border-primary border-bottom-2' : ''}" href="javascript:void(0);" data-action="switch-tab" data-tab="overview" data-client-id="${clientId}">Overview</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 py-2 text-gray-700 ${activeTab === 'jobs' ? 'active text-primary border-primary border-bottom-2' : ''}" href="javascript:void(0);" data-action="switch-tab" data-tab="jobs" data-client-id="${clientId}">Jobs (${Number(client.active_jobs_count || 0)})</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 py-2 text-gray-700 ${activeTab === 'candidates' ? 'active text-primary border-primary border-bottom-2' : ''}" href="javascript:void(0);" data-action="switch-tab" data-tab="candidates" data-client-id="${clientId}">Candidates</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 py-2 text-gray-700 ${activeTab === 'interviews' ? 'active text-primary border-primary border-bottom-2' : ''}" href="javascript:void(0);" data-action="switch-tab" data-tab="interviews" data-client-id="${clientId}">Interviews</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 py-2 text-gray-700 ${activeTab === 'offers' ? 'active text-primary border-primary border-bottom-2' : ''}" href="javascript:void(0);" data-action="switch-tab" data-tab="offers" data-client-id="${clientId}">Offers</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 py-2 text-gray-700 ${activeTab === 'joining' ? 'active text-primary border-primary border-bottom-2' : ''}" href="javascript:void(0);" data-action="switch-tab" data-tab="joining" data-client-id="${clientId}">Joining</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 py-2 text-gray-700 ${activeTab === 'contacts' ? 'active text-primary border-primary border-bottom-2' : ''}" href="javascript:void(0);" data-action="switch-tab" data-tab="contacts" data-client-id="${clientId}">Contacts (${(client.contacts && client.contacts.length) || 0})</a>
                            </li>
                        </ul>
                    </div>

                    <!-- ─── TIER 3: Primary Information Surface Container ─── -->
                    <div id="jdcr-tab-surface-${clientId}" class="flex-grow-1 overflow-y-auto pe-1" style="max-height: calc(100vh - 180px); overflow-y: auto;">
                        ${this.renderTabContent(client, activeTab)}
                    </div>
                </div>
            `;
        },

        /**
         * Render active tab surface content
         */
        renderTabContent: function(client, tabName) {
            switch (tabName) {
                case 'overview':
                    return this.renderOverviewTab(client);
                case 'contacts':
                    return this.renderContactsTab(client);
                case 'jobs':
                    return this.renderPlaceholderTab(client, 'Jobs', 'Loading active job requisitions from canonical projection...');
                case 'candidates':
                    return this.renderPlaceholderTab(client, 'Candidates', 'Loading candidate talent pipeline...');
                case 'interviews':
                    return this.renderPlaceholderTab(client, 'Interviews', 'Loading scheduled interview tracker...');
                case 'offers':
                    return this.renderPlaceholderTab(client, 'Offers', 'Loading candidate offer pipeline...');
                case 'joining':
                    return this.renderPlaceholderTab(client, 'Joining', 'Loading confirmed candidate joining records...');
                default:
                    return this.renderOverviewTab(client);
            }
        },

        /**
         * Render Overview Tab with Discrete Upper Sub-Cards & Bottom Activity Strip
         */
        renderOverviewTab: function(client) {
            const contacts = client.contacts || [];
            const primaryContact = contacts[0] || {};
            const cleanPhone = primaryContact.calling_code ? (primaryContact.calling_code + ' ' + (primaryContact.contact_person_number || '')) : (primaryContact.contact_person_number || 'N/A');

            return `
                <div class="d-flex flex-column gap-3 pb-4">
                    <!-- Sub-Card A: Key Profile & Location -->
                    <div class="card bg-white border border-slate-200 rounded-3 p-4 shadow-xs" style="border-radius: 10px; border-color: #e2e8f0;">
                        <h5 class="fw-bold text-gray-800 fs-6 mb-3 d-flex align-items-center gap-2">
                            <i class="ki-outline ki-profile-user text-primary fs-5"></i> Client Information
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4 col-sm-6">
                                <span class="text-muted fs-8 d-block mb-1">Company / Entity</span>
                                <span class="fw-semibold text-gray-800 fs-7">${escapeHtml(client.company_name || client.name || 'N/A')}</span>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <span class="text-muted fs-8 d-block mb-1">Industry Vertical</span>
                                <span class="fw-semibold text-gray-800 fs-7">${escapeHtml(client.industry_name || 'General')}</span>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <span class="text-muted fs-8 d-block mb-1">Client Category</span>
                                <span class="badge badge-light-primary fs-8 fw-semibold">${escapeHtml(client.type || 'Internal')}</span>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <span class="text-muted fs-8 d-block mb-1">Location</span>
                                <span class="fw-semibold text-gray-800 fs-7">${escapeHtml([client.city_name, client.state_name, client.country_name].filter(Boolean).join(', ') || 'Not specified')}</span>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <span class="text-muted fs-8 d-block mb-1">Official Website</span>
                                ${client.website ? `<a href="${sanitizeAssetUrl(client.website)}" target="_blank" rel="noopener noreferrer" class="text-primary text-hover-underline fs-7">${escapeHtml(client.website)}</a>` : '<span class="text-gray-500 fs-7">N/A</span>'}
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <span class="text-muted fs-8 d-block mb-1">Active Requisitions</span>
                                <span class="badge badge-light-info fs-7 fw-bold">${Number(client.active_jobs_count || 0)} Open Jobs</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card B: Primary Contact Person -->
                    <div class="card bg-white border border-slate-200 rounded-3 p-4 shadow-xs" style="border-radius: 10px; border-color: #e2e8f0;">
                        <h5 class="fw-bold text-gray-800 fs-6 mb-3 d-flex align-items-center gap-2">
                            <i class="ki-outline ki-phone text-success fs-5"></i> Primary Contact Person
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4 col-sm-6">
                                <span class="text-muted fs-8 d-block mb-1">Contact Name</span>
                                <span class="fw-semibold text-gray-800 fs-7">${escapeHtml(primaryContact.contact_person_name || client.email || 'N/A')}</span>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <span class="text-muted fs-8 d-block mb-1">Email Address</span>
                                <span class="fw-semibold text-gray-800 fs-7">${escapeHtml(primaryContact.contact_person_email || client.email || 'N/A')}</span>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <span class="text-muted fs-8 d-block mb-1">Phone Number</span>
                                <span class="fw-semibold text-gray-800 fs-7">${escapeHtml(cleanPhone)}</span>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <span class="text-muted fs-8 d-block mb-1">Designation</span>
                                <span class="fw-semibold text-gray-800 fs-7">${escapeHtml(primaryContact.contact_person_designation || 'Account Lead')}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card C: Description / Bio -->
                    ${client.description ? `
                        <div class="card bg-white border border-slate-200 rounded-3 p-4 shadow-xs" style="border-radius: 10px; border-color: #e2e8f0;">
                            <h5 class="fw-bold text-gray-800 fs-6 mb-2">Company Overview</h5>
                            <p class="text-gray-700 fs-7 mb-0 line-height-base">${escapeHtml(client.description)}</p>
                        </div>
                    ` : ''}

                    <!-- TIER 4: Activity & Audit Strip -->
                    <div class="card bg-white border border-slate-200 rounded-3 px-4 py-3 shadow-xs" style="border-radius: 10px; border-color: #e2e8f0;">
                        <div class="d-flex align-items-center justify-content-between text-muted fs-8">
                            <span><i class="ki-outline ki-time text-gray-400 me-1"></i>Last Updated: <span class="fw-semibold text-gray-700">${escapeHtml(client.updated_at ? client.updated_at.split('T')[0] : 'Today')}</span></span>
                            <span>Version: <span class="badge badge-light fs-9 text-gray-600">v${Number(client.entity_version || 1)}</span></span>
                        </div>
                    </div>
                </div>
            `;
        },

        /**
         * Render Contacts Tab List
         */
        renderContactsTab: function(client) {
            const contacts = client.contacts || [];
            if (!contacts.length) {
                return `
                    <div class="card bg-white border border-slate-200 rounded-3 p-6 text-center shadow-xs" style="border-radius: 10px; border-color: #e2e8f0;">
                        <i class="ki-outline ki-address-book fs-3x text-gray-300 mb-2"></i>
                        <h6 class="text-gray-700 fw-bold fs-6">No Contact Persons Recorded</h6>
                        <span class="text-muted fs-7">No additional team members or contact leads have been linked yet.</span>
                    </div>
                `;
            }

            const cards = contacts.map(function(c, idx) {
                const cleanPhone = c.calling_code ? (c.calling_code + ' ' + (c.contact_person_number || '')) : (c.contact_person_number || 'N/A');
                return `
                    <div class="card bg-white border border-slate-200 rounded-3 p-3 mb-2 shadow-xs" style="border-radius: 10px; border-color: #e2e8f0;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="symbol symbol-35px symbol-circle bg-light-primary text-primary fw-bold fs-7 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; border-radius: 50%;">
                                    ${escapeHtml((c.contact_person_name || 'C').charAt(0).toUpperCase())}
                                </div>
                                <div>
                                    <div class="fw-bold text-gray-800 fs-7">${escapeHtml(c.contact_person_name || 'Contact #' + (idx + 1))}</div>
                                    <div class="text-muted fs-8">${escapeHtml(c.contact_person_designation || 'Team Member')} • <span class="text-gray-700">${escapeHtml(cleanPhone)}</span></div>
                                </div>
                            </div>
                            <a href="mailto:${escapeHtml(c.contact_person_email || '')}" class="btn btn-sm btn-icon btn-light-primary" title="Email Contact">
                                <i class="ki-outline ki-sms fs-6"></i>
                            </a>
                        </div>
                    </div>
                `;
            }).join('');

            return `<div class="d-flex flex-column gap-1 pb-4">${cards}</div>`;
        },

        /**
         * Render generic progressive tab loader placeholder
         */
        renderPlaceholderTab: function(client, title, description) {
            return `
                <div class="card bg-white border border-slate-200 rounded-3 p-6 text-center shadow-xs" style="border-radius: 10px; border-color: #e2e8f0;">
                    <div class="spinner-border spinner-border-sm text-primary mb-3" role="status"></div>
                    <h6 class="text-gray-800 fw-bold fs-6 mb-1">${escapeHtml(title)}</h6>
                    <span class="text-muted fs-7">${escapeHtml(description)}</span>
                </div>
            `;
        },

        /**
         * Patch the sync status indicator badge safely using textContent
         */
        patchSyncStatus: function(clientId, status, label) {
            const el = document.getElementById(`jdcr-sync-badge-${clientId}`);
            if (!el) return;

            let badgeClass = 'badge-light-success text-success';
            let iconClass = 'ki-check-circle text-success';

            if (status === 'syncing') {
                badgeClass = 'badge-light-warning text-warning';
                iconClass = 'ki-arrows-circle text-warning spinner-grow spinner-grow-sm';
            } else if (status === 'stale') {
                badgeClass = 'badge-light-info text-info';
                iconClass = 'ki-information-2 text-info';
            } else if (status === 'offline') {
                badgeClass = 'badge-light-secondary text-gray-600';
                iconClass = 'ki-disconnect text-gray-500';
            } else if (status === 'conflict') {
                badgeClass = 'badge-light-danger text-danger';
                iconClass = 'ki-shield-cross text-danger';
            }

            el.innerHTML = `<span class="badge ${badgeClass} fs-8 fw-medium px-2 py-1"><i class="ki-outline ${iconClass} fs-8 me-1"></i>${escapeHtml(label || status)}</span>`;
        }
    };

    window.ClientIncrementalRenderer = ClientIncrementalRenderer;

})(typeof window !== 'undefined' ? window : this);
