/**
 * JERO SERVICE COMPANY — CLIENT LISTING WEB WORKER (cl-data-worker.js)
 * High-concurrency in-browser OLAP engine.
 * Executes filtering, fuzzy searching, sorting, and CSV export off the main thread (60 FPS Locked).
 */

self.dataset = [];
self.datasetMap = new Map();
self.dataVersion = 1;

self.onmessage = function(e) {
    const msg = e.data;
    if (!msg || !msg.type) return;

    switch (msg.type) {
        case 'INIT_DATASET':
            initDataset(msg.rows || [], msg.version || 1);
            break;

        case 'QUERY_DATASET':
            queryDataset(msg);
            break;

        case 'DELTA_UPSERT':
            deltaUpsert(msg.client, msg.version);
            break;

        case 'EXPORT_CSV':
            exportCsv(msg);
            break;

        case 'CHECK_HYDRATION':
            self.postMessage({
                type: 'HYDRATION_STATUS',
                isHydrated: Boolean(self.dataset && self.dataset.length > 0),
                total: self.dataset ? self.dataset.length : 0,
                version: self.dataVersion || 1
            });
            break;

        case 'PIPELINE_REVISION_UPDATE':
            pipelineRevisionUpdate(msg.clientId, msg.revision, msg.pv, msg.changed);
            break;

        default:
            break;
    }
};

function initDataset(rawRows, version) {
    const t0 = performance.now();
    self.dataVersion = version || 1;
    self.dataset = [];
    self.datasetMap.clear();

    for (let i = 0; i < rawRows.length; i++) {
        const item = normalizeRow(rawRows[i]);
        self.dataset.push(item);
        self.datasetMap.set(item.id, item);
    }

    const elapsed = performance.now() - t0;
    self.postMessage({
        type: 'INIT_COMPLETE',
        total: self.dataset.length,
        version: self.dataVersion,
        elapsed_ms: elapsed.toFixed(2)
    });
}

function normalizeRow(r) {
    const loc = r.location || {};
    const city = (r.location_main || loc.city || '').trim();
    const state = (loc.state || '').trim();
    const country = (r.country || loc.country || '').trim();
    const hasLocation = Boolean(city || state || country);

    const countryId = (r.country_id !== undefined && r.country_id !== null) ? Number(r.country_id) : (loc.country_id ? Number(loc.country_id) : null);
    const cityId = (r.city_id !== undefined && r.city_id !== null) ? Number(r.city_id) : (loc.city_id ? Number(loc.city_id) : null);
    const stateId = (r.state_id !== undefined && r.state_id !== null) ? Number(r.state_id) : (loc.state_id || loc.state ? Number(loc.state_id || loc.state) : null);
    const industryId = (r.industry_id !== undefined && r.industry_id !== null)
        ? Number(r.industry_id)
        : ((r.industry && !isNaN(Number(r.industry))) ? Number(r.industry) : null);
    const indName = (r.ind_name || (isNaN(Number(r.industry)) ? r.industry : '') || '').trim();

    const rawCountryIds = Array.isArray(r.country_ids) ? r.country_ids : [];
    const locations = Array.isArray(r.locations) ? r.locations : (Array.isArray(r.client_location) ? r.client_location : []);
    const countryIdsSet = new Set(rawCountryIds.map(Number));
    if (countryId) countryIdsSet.add(countryId);
    for (let li = 0; li < locations.length; li++) {
        if (locations[li] && locations[li].country_id) {
            countryIdsSet.add(Number(locations[li].country_id));
        }
    }
    const countryIds = Array.from(countryIdsSet);

    const jobs = r.jobs || {};
    const pipeline = r.pipeline || {};

    let createdTimestamp = 0;
    if (r.connected_on || r.date) {
        createdTimestamp = new Date(r.connected_on || r.date).getTime() || 0;
    }

    const activeJobs = Number(jobs.active || jobs.hiring || 0);

    // Canonical Status Vector (Upgraded Contract): [sourcing, screening, evaluation, negotiation, offer, joined, rejected, in_review, unique_interviewees]
    // Note: 'total' is strictly excluded from vector to eliminate redundant derived data and enforce Invariant 3.
    const pv = Array.isArray(r.pv) && r.pv.length >= 9 ? r.pv : null;
    const sourced = pv ? Number(pv[0] || 0) : Number(pipeline.sourced || 0);
    const screening = pv ? Number(pv[1] || 0) : Number(pipeline.screening || 0);
    const evaluated = pv ? Number(pv[2] || 0) : Number(pipeline.evaluated || 0);
    const negotiations = pv ? Number(pv[3] || 0) : Number(pipeline.negotiations || pipeline.negotiation || 0);
    const offers = pv ? Number(pv[4] || 0) : Number(pipeline.offers || 0);
    const joined = pv ? Number(pv[5] || 0) : Number(pipeline.joined || 0);
    const rejected = pv ? Number(pv[6] || 0) : Number(pipeline.rejected || 0);
    const inReview = pv ? Number(pv[7] || 0) : Number(pipeline.in_review || 0);
    const interviews = pv ? Number(pv[8] || 0) : Number(pipeline.interviews || pipeline.all_interview || 0);

    const activePipeline = sourced + screening + evaluated + negotiations + offers + joined;
    const applications = activePipeline + rejected + inReview;

    return {
        id: r.id,
        client_id: r.client_id || r.id,
        name: r.name || '',
        name_lower: (r.name || '').toLowerCase(),
        is_direct: Boolean(r.is_direct),
        client_type: r.client_type || (r.is_direct ? 'Direct Client' : 'Internal Client'),
        industry: indName || r.industry || '',
        industry_lower: (indName || r.industry || '').toLowerCase(),
        industry_id: industryId,
        has_location: hasLocation,
        country_name: country,
        country_name_lower: country.toLowerCase(),
        country_id: countryId,
        country_ids: countryIds,
        state_id: stateId,
        city_name: city,
        city_name_lower: city.toLowerCase(),
        city_id: cityId,
        company_size: r.company_size || '',
        is_active_hiring: activeJobs > 0,
        is_inactive: activeJobs === 0,
        revision: r.revision || 1,
        jobs: {
            active: activeJobs,
            hold: Number(jobs.hold || 0),
            closed: Number(jobs.closed || 0),
            total: Number(jobs.total || (activeJobs + Number(jobs.hold || 0) + Number(jobs.closed || 0)))
        },
        pv: [sourced, screening, evaluated, negotiations, offers, joined, rejected, inReview, interviews],
        pipeline: {
            active_pipeline: activePipeline,
            applications: applications,
            sourced: sourced,
            screening: screening,
            evaluated: evaluated,
            negotiations: negotiations,
            offers: offers,
            joined: joined,
            rejected: rejected,
            in_review: inReview,
            interviews: interviews
        },
        date: r.connected_on || r.date || '-',
        date_ago: r.days_ago || r.date_ago || '',
        connected_date: r.connected_on || r.date || '-',
        created_timestamp: createdTimestamp,
        status: activeJobs > 0 ? 'Active' : 'Inactive',
        last_activity: r.days_ago || r.last_activity || 'No Activity',
        last_activity_ago: r.days_ago || r.last_activity_ago || '',
        last_activity_utc: r.last_activity_at || null,
        last_activity_formatted: r.last_activity_formatted || null,
        last_activity_display: r.days_ago || r.last_activity_ago || r.last_activity || 'No Activity',
        raw: r
    };
}

function queryDataset(msg) {
    const t0 = performance.now();
    const qId = msg.queryId || 0;
    const f = msg.filters || {};

    const tab = (f.tab || 'all').toLowerCase();
    const search = (f.search || '').trim().toLowerCase();
    const countryId = f.country_id;
    const countryName = (f.country_name || '').trim().toLowerCase();
    const cityId = f.city_id;
    const cityName = (f.city_name || '').trim().toLowerCase();
    const unspecifiedLoc = Boolean(f.unspecified_location || countryId === 'unspecified');
    const industry = (f.industry || f.industry_id || '').toString().trim().toLowerCase();
    const activity = (f.activity || '').trim().toLowerCase();
    const sort = (f.sort || 'recent').toLowerCase();
    const page = Math.max(1, parseInt(f.page, 10) || 1);
    const perPage = Math.max(1, parseInt(f.per_page, 10) || 10);

    let filtered = [];
    let allCount = 0;
    let internalCount = 0;
    let directCount = 0;
    let hiringCount = 0;
    let inactiveCount = 0;

    for (let i = 0; i < self.dataset.length; i++) {
        const item = self.dataset[i];

        // 1. Non-tab filters define the scoped client universe
        // Search filter (multi-field substring)
        if (search) {
            const matchesSearch = item.name_lower.includes(search) ||
                                  item.industry_lower.includes(search) ||
                                  item.country_name_lower.includes(search) ||
                                  item.city_name_lower.includes(search);
            if (!matchesSearch) continue;
        }

        // Location filter
        if (unspecifiedLoc) {
            if (item.has_location) continue;
        } else if (countryId || countryName) {
            let matchCountry = false;
            if (countryId && item.country_ids && item.country_ids.some(cid => String(cid) === String(countryId))) {
                matchCountry = true;
            } else if (countryId && item.country_id && String(item.country_id) === String(countryId)) {
                matchCountry = true;
            } else if (countryName && item.country_name_lower.includes(countryName)) {
                matchCountry = true;
            } else if (countryId && isNaN(countryId) && item.country_name_lower.includes(String(countryId).toLowerCase())) {
                matchCountry = true;
            }
            if (!matchCountry) continue;

            // City drilldown
            if (cityId || cityName) {
                let matchCity = false;
                if (cityId && item.city_id && String(item.city_id) === String(cityId)) {
                    matchCity = true;
                } else if (cityName && item.city_name_lower.includes(cityName)) {
                    matchCity = true;
                } else if (cityId && isNaN(cityId) && item.city_name_lower.includes(String(cityId).toLowerCase())) {
                    matchCity = true;
                }
                if (!matchCity) continue;
            }
        }

        // Industry filter (supports ID or substring name)
        if (industry) {
            const industryNum = Number(industry);
            const matchesId = !isNaN(industryNum) && industryNum > 0 && item.industry_id === industryNum;
            const matchesName = item.industry_lower && item.industry_lower.includes(industry);
            if (!matchesId && !matchesName) continue;
        }

        // Activity filter
        if (activity === 'hiring') {
            if (item.jobs.active <= 0) continue;
        } else if (activity === 'pipeline') {
            const pipeSum = item.pipeline.sourced + item.pipeline.evaluated + item.pipeline.interviews;
            if (pipeSum <= 0) continue;
        } else if (activity === 'dormant') {
            const actSum = item.jobs.active + item.pipeline.sourced + item.pipeline.evaluated;
            if (actSum > 0) continue;
        }

        // Item belongs to the scoped client universe: evaluate universal tab counters
        allCount++;
        if (item.is_direct) {
            directCount++;
        } else {
            internalCount++;
        }

        if (item.jobs.active > 0) {
            hiringCount++;
        } else {
            inactiveCount++;
        }

        // 2. Tab filter determines which rows are rendered in the current tab view
        if (tab === 'internal' && item.is_direct) continue;
        if (tab === 'direct' && !item.is_direct) continue;
        if (tab === 'hiring' && item.jobs.active <= 0) continue;
        if (tab === 'inactive' && item.jobs.active > 0) continue;

        filtered.push(item);
    }

    const totalCount = filtered.length;

    // Sort
    filtered.sort(function(a, b) {
        if (sort === 'name_asc') {
            return a.name.localeCompare(b.name) || (b.id - a.id);
        } else if (sort === 'name_desc') {
            return b.name.localeCompare(a.name) || (b.id - a.id);
        } else if (sort === 'jobs_desc') {
            return (b.jobs.active - a.jobs.active) || (b.id - a.id);
        } else if (sort === 'oldest' || sort === '2') {
            return (a.created_timestamp - b.created_timestamp) || (a.id - b.id);
        } else {
            // recent / newest
            return (b.created_timestamp - a.created_timestamp) || (b.id - a.id);
        }
    });

    // Pagination slice
    const from = (page - 1) * perPage;
    const pageRows = filtered.slice(from, from + perPage);

    const elapsed = performance.now() - t0;

    self.postMessage({
        type: 'QUERY_RESULT',
        queryId: qId,
        total: totalCount,
        page: page,
        per_page: perPage,
        rows: pageRows.map(r => r.raw),
        counts: {
            all: allCount,
            internal: internalCount,
            direct: directCount,
            hiring: hiringCount,
            inactive: inactiveCount
        },
        elapsed_ms: elapsed.toFixed(2)
    });
}

function deltaUpsert(clientData, version) {
    if (!clientData || !clientData.id) return;
    if (version) self.dataVersion = version;

    const item = normalizeRow(clientData);
    self.datasetMap.set(item.id, item);

    // Rebuild dataset array
    self.dataset = Array.from(self.datasetMap.values());

    self.postMessage({
        type: 'DELTA_APPLIED',
        id: item.id,
        total: self.dataset.length,
        version: self.dataVersion
    });
}

function pipelineRevisionUpdate(clientId, revision, newPv, changed) {
    if (!clientId) return;
    const cid = Number(clientId);
    const item = self.datasetMap.get(cid);
    if (!item) return;

    if (revision && item.revision && Number(revision) <= Number(item.revision)) {
        // Discard stale or duplicate revision
        return;
    }
    if (revision) item.revision = Number(revision);

    if (Array.isArray(newPv) && newPv.length >= 9) {
        const sourced = Number(newPv[0] || 0);
        const screening = Number(newPv[1] || 0);
        const evaluated = Number(newPv[2] || 0);
        const negotiations = Number(newPv[3] || 0);
        const offers = Number(newPv[4] || 0);
        const joined = Number(newPv[5] || 0);
        const rejected = Number(newPv[6] || 0);
        const inReview = Number(newPv[7] || 0);
        const interviews = Number(newPv[8] || 0);

        const activePipeline = sourced + screening + evaluated + negotiations + offers + joined;
        const applications = activePipeline + rejected + inReview;

        item.pv = [sourced, screening, evaluated, negotiations, offers, joined, rejected, inReview, interviews];
        item.pipeline = {
            active_pipeline: activePipeline,
            applications: applications,
            sourced: sourced,
            screening: screening,
            evaluated: evaluated,
            negotiations: negotiations,
            offers: offers,
            joined: joined,
            rejected: rejected,
            in_review: inReview,
            interviews: interviews
        };
    } else if (changed && typeof changed === 'object') {
        const p = item.pipeline;
        const fromStatus = Number(changed.from_status || 0);
        const toStatus = Number(changed.new_status || changed.to_status || 0);
        const statusKeyMap = {
            1: 'sourced',
            2: 'screening',
            3: 'evaluated',
            4: 'negotiations',
            5: 'offers',
            6: 'joined',
            7: 'rejected',
            8: 'in_review'
        };
        if (fromStatus && statusKeyMap[fromStatus] && p[statusKeyMap[fromStatus]] > 0) {
            p[statusKeyMap[fromStatus]] = Math.max(0, p[statusKeyMap[fromStatus]] - 1);
        }
        if (toStatus && statusKeyMap[toStatus]) {
            p[statusKeyMap[toStatus]] = (p[statusKeyMap[toStatus]] || 0) + 1;
        }

        const activePipeline = (p.sourced || 0) + (p.screening || 0) + (p.evaluated || 0) + (p.negotiations || 0) + (p.offers || 0) + (p.joined || 0);
        const applications = activePipeline + (p.rejected || 0) + (p.in_review || 0);
        p.active_pipeline = activePipeline;
        p.applications = applications;

        item.pv = [
            p.sourced,
            p.screening,
            p.evaluated,
            p.negotiations,
            p.offers,
            p.joined,
            p.rejected,
            p.in_review || 0,
            p.interviews
        ];
    }

    self.postMessage({
        type: 'PIPELINE_REVISION_APPLIED',
        clientId: cid,
        revision: item.revision,
        pv: item.pv,
        pipeline: item.pipeline
    });
}

function exportCsv(msg) {
    const t0 = performance.now();
    const f = (msg && msg.filters) ? msg.filters : (msg || {});
    const scope = (msg && msg.scope) ? msg.scope : 'filtered';
    const manifest = (msg && msg.manifest) ? msg.manifest : {};

    if (!self.dataset || self.dataset.length === 0) {
        self.postMessage({
            type: 'EXPORT_ERROR',
            code: 'DATASET_EMPTY',
            message: 'No client records are available in local browser memory to export.'
        });
        return;
    }

    let records = [];

    if (scope === 'all') {
        records = self.dataset.slice();
    } else {
        const tab = (f.tab || 'all').toLowerCase();
        const search = (f.search || '').trim().toLowerCase();
        const countryId = f.country_id;
        const countryName = (f.country_name || '').trim().toLowerCase();
        const cityId = f.city_id;
        const cityName = (f.city_name || '').trim().toLowerCase();
        const unspecifiedLoc = Boolean(f.unspecified_location || countryId === 'unspecified');
        const industry = (f.industry || f.industry_id || '').toString().trim().toLowerCase();
        const activity = (f.activity || '').trim().toLowerCase();

        for (let i = 0; i < self.dataset.length; i++) {
            const item = self.dataset[i];

            // 1. Search filter
            if (search) {
                const matchesSearch = (item.name_lower && item.name_lower.includes(search)) ||
                                      (item.industry_lower && item.industry_lower.includes(search)) ||
                                      (item.country_name_lower && item.country_name_lower.includes(search)) ||
                                      (item.city_name_lower && item.city_name_lower.includes(search));
                if (!matchesSearch) continue;
            }

            // 2. Location filter
            if (unspecifiedLoc) {
                if (item.has_location) continue;
            } else if (countryId || countryName) {
                let matchCountry = false;
                if (countryId && item.country_ids && item.country_ids.some(cid => String(cid) === String(countryId))) {
                    matchCountry = true;
                } else if (countryId && item.country_id && String(item.country_id) === String(countryId)) {
                    matchCountry = true;
                } else if (countryName && item.country_name_lower && item.country_name_lower.includes(countryName)) {
                    matchCountry = true;
                } else if (countryId && isNaN(countryId) && item.country_name_lower && item.country_name_lower.includes(String(countryId).toLowerCase())) {
                    matchCountry = true;
                }
                if (!matchCountry) continue;

                if (cityId || cityName) {
                    let matchCity = false;
                    if (cityId && item.city_id && String(item.city_id) === String(cityId)) {
                        matchCity = true;
                    } else if (cityName && item.city_name_lower && item.city_name_lower.includes(cityName)) {
                        matchCity = true;
                    } else if (cityId && isNaN(cityId) && item.city_name_lower && item.city_name_lower.includes(String(cityId).toLowerCase())) {
                        matchCity = true;
                    }
                    if (!matchCity) continue;
                }
            }

            // 3. Industry filter
            if (industry) {
                const industryNum = Number(industry);
                const matchesId = !isNaN(industryNum) && industryNum > 0 && item.industry_id === industryNum;
                const matchesName = item.industry_lower && item.industry_lower.includes(industry);
                if (!matchesId && !matchesName) continue;
            }

            // 4. Activity filter
            if (activity === 'hiring') {
                if (item.jobs.active <= 0) continue;
            } else if (activity === 'pipeline') {
                const pipeSum = item.pipeline.sourced + item.pipeline.evaluated + item.pipeline.interviews;
                if (pipeSum <= 0) continue;
            } else if (activity === 'dormant') {
                const actSum = item.jobs.active + item.pipeline.sourced + item.pipeline.evaluated;
                if (actSum > 0) continue;
            }

            // 5. Tab filter
            if (tab === 'internal' && item.is_direct) continue;
            if (tab === 'direct' && !item.is_direct) continue;
            if (tab === 'hiring' && item.jobs.active <= 0) continue;
            if (tab === 'inactive' && item.jobs.active > 0) continue;

            records.push(item);
        }
    }

    // Apply Sorting (strictly preserving active UI sort)
    const sort = (f.sort || 'recent').toLowerCase();
    records.sort(function(a, b) {
        if (sort === 'name_asc') {
            return a.name.localeCompare(b.name) || (b.id - a.id);
        } else if (sort === 'name_desc') {
            return b.name.localeCompare(a.name) || (b.id - a.id);
        } else if (sort === 'jobs_desc') {
            return (b.jobs.active - a.jobs.active) || (b.id - a.id);
        } else if (sort === 'oldest' || sort === '2') {
            return (a.created_timestamp - b.created_timestamp) || (a.id - b.id);
        } else {
            return (b.created_timestamp - a.created_timestamp) || (b.id - a.id);
        }
    });

    // CWE-1236 Defense: Neutralize formula triggers (=, +, -, @, \t, \r)
    function sanitizeCsvCell(value) {
        if (value === null || value === undefined) return '""';
        let str = String(value).trim();
        if (/^[=+\-@\t\r]/.test(str)) {
            str = "'" + str;
        }
        return '"' + str.replace(/"/g, '""') + '"';
    }

    // Enterprise Audit Manifest Header
    const manifestLines = [
        '# ==============================================================================',
        '# JERO ENTERPRISE ATS CLIENTS EXPORT MANIFEST',
        `# Export ID: ${manifest.exportId || ('EXP-' + Date.now())}`,
        `# Exported At: ${new Date().toISOString()}`,
        `# Scope: ${scope === 'all' ? 'All Clients' : (f.tab || 'All').toUpperCase() + ' (Filtered)'}`,
        `# Total Records: ${records.length}`,
        `# Tenant ID: ${manifest.tenantId || ''}`,
        `# Generated By: ${manifest.userName || 'Authorized User'} (ID #${manifest.userId || ''})`,
        `# Dataset Version: ${self.dataVersion || '1.0'}`,
        '# Security Classification: Internal Business Confidential',
        '# =============================================================================='
    ];

    const headers = [
        'Client ID', 'Company Name', 'Industry', 'Company Size', 'City', 'Country',
        'Client Type', 'Status', 'Jobs Hiring', 'Jobs Hold', 'Jobs Closed',
        'Sourced', 'Screening', 'Evaluated', 'Negotiation', 'Offers',
        'Joined', 'Rejected', 'In Review', 'Interviewees', 'Total Applications',
        'Connected Date', 'Last Activity UTC', 'Last Activity (Display)'
    ];

    const rows = [
        manifestLines.join('\r\n'),
        headers.map(h => `"${h}"`).join(',')
    ];

    for (let i = 0; i < records.length; i++) {
        const item = records[i];
        rows.push([
            sanitizeCsvCell(item.id),
            sanitizeCsvCell(item.name),
            sanitizeCsvCell(item.industry),
            sanitizeCsvCell(item.company_size),
            sanitizeCsvCell(item.city_name),
            sanitizeCsvCell(item.country_name),
            sanitizeCsvCell(item.client_type || (item.is_direct ? 'Direct Client' : 'Internal Client')),
            sanitizeCsvCell(item.status),
            item.jobs.active,
            item.jobs.hold,
            item.jobs.closed,
            item.pipeline.sourced,
            item.pipeline.screening,
            item.pipeline.evaluated,
            item.pipeline.negotiations,
            item.pipeline.offers,
            item.pipeline.joined,
            item.pipeline.rejected,
            item.pipeline.in_review || 0,
            item.pipeline.interviews,
            item.pipeline.applications,
            sanitizeCsvCell(item.connected_date || item.date),
            sanitizeCsvCell(item.last_activity_utc || item.last_activity_formatted || '-'),
            sanitizeCsvCell(item.last_activity_display || item.last_activity)
        ].join(','));
    }

    // Prepend UTF-8 BOM (\uFEFF) for strict Microsoft Excel character fidelity
    const bom = '\uFEFF';
    const csvContent = bom + rows.join('\r\n');
    const elapsed = Math.round(performance.now() - t0);

    self.postMessage({
        type: 'CSV_READY',
        csvContent: csvContent,
        rowCount: records.length,
        scope: scope,
        exportId: manifest.exportId || '',
        durationMs: elapsed
    });
}
