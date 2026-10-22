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
            exportCsv(msg.filters);
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

    const jobs = r.jobs || {};
    const pipeline = r.pipeline || {};

    let createdTimestamp = 0;
    if (r.connected_on || r.date) {
        createdTimestamp = new Date(r.connected_on || r.date).getTime() || 0;
    }

    return {
        id: r.id,
        client_id: r.client_id || r.id,
        name: r.name || '',
        name_lower: (r.name || '').toLowerCase(),
        is_direct: Boolean(r.is_direct),
        client_type: r.client_type || (r.is_direct ? 'Direct Client' : 'Internal Client'),
        industry: r.industry || '',
        industry_lower: (r.industry || '').toLowerCase(),
        has_location: hasLocation,
        country_name: country,
        country_name_lower: country.toLowerCase(),
        country_id: r.country_id || null,
        city_name: city,
        city_name_lower: city.toLowerCase(),
        city_id: r.city_id || null,
        company_size: r.company_size || '',
        jobs: {
            active: Number(jobs.active || jobs.hiring || 0),
            hold: Number(jobs.hold || 0),
            closed: Number(jobs.closed || 0),
            total: Number(jobs.total || (Number(jobs.active || jobs.hiring || 0) + Number(jobs.hold || 0) + Number(jobs.closed || 0)))
        },
        pipeline: {
            sourced: Number(pipeline.sourced || 0),
            evaluated: Number(pipeline.evaluated || 0),
            interviews: Number(pipeline.interviews || 0),
            offers: Number(pipeline.offers || 0),
            joined: Number(pipeline.joined || 0)
        },
        date: r.connected_on || r.date || '-',
        date_ago: r.days_ago || r.date_ago || '',
        created_timestamp: createdTimestamp,
        last_activity: r.days_ago || r.last_activity || 'No Activity',
        last_activity_ago: r.days_ago || r.last_activity_ago || '',
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

    for (let i = 0; i < self.dataset.length; i++) {
        const item = self.dataset[i];

        // 1. Tab filter
        if (tab === 'internal' && item.is_direct) continue;
        if (tab === 'direct' && !item.is_direct) continue;
        if (tab === 'hiring' && item.jobs.active <= 0) continue;

        // 2. Search filter (multi-field substring)
        if (search) {
            const matchesSearch = item.name_lower.includes(search) ||
                                  item.industry_lower.includes(search) ||
                                  item.country_name_lower.includes(search) ||
                                  item.city_name_lower.includes(search);
            if (!matchesSearch) continue;
        }

        // 3. Location filter
        if (unspecifiedLoc) {
            if (item.has_location) continue;
        } else if (countryId || countryName) {
            let matchCountry = false;
            if (countryId && item.country_id && String(item.country_id) === String(countryId)) {
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

        // 4. Industry filter
        if (industry) {
            if (!item.industry_lower.includes(industry)) continue;
        }

        // 5. Activity filter
        if (activity === 'hiring') {
            if (item.jobs.active <= 0) continue;
        } else if (activity === 'pipeline') {
            const pipeSum = item.pipeline.sourced + item.pipeline.evaluated + item.pipeline.interviews;
            if (pipeSum <= 0) continue;
        } else if (activity === 'dormant') {
            const actSum = item.jobs.active + item.pipeline.sourced + item.pipeline.evaluated;
            if (actSum > 0) continue;
        }

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

    let allCount = self.dataset.length;
    let internalCount = 0;
    let directCount = 0;
    let hiringCount = 0;
    for (let i = 0; i < self.dataset.length; i++) {
        const dItem = self.dataset[i];
        if (dItem.is_direct) directCount++;
        else internalCount++;
        if (dItem.jobs.active > 0) hiringCount++;
    }

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
            hiring: hiringCount
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

function exportCsv(filters) {
    // Generate CSV on worker thread without main-thread blocking
    const rows = [];
    rows.push(['Company Name', 'Industry', 'Location', 'Jobs Hiring', 'Jobs Hold', 'Jobs Closed', 'Sourced', 'Evaluated', 'Interviews', 'Offers', 'Joined', 'Client Type', 'Date', 'Last Activity']);

    for (let i = 0; i < self.dataset.length; i++) {
        const item = self.dataset[i];
        rows.push([
            item.name,
            item.industry,
            item.city_name ? (item.city_name + (item.country_name ? ', ' + item.country_name : '')) : item.country_name,
            item.jobs.active,
            item.jobs.hold,
            item.jobs.closed,
            item.pipeline.sourced,
            item.pipeline.evaluated,
            item.pipeline.interviews,
            item.pipeline.offers,
            item.pipeline.joined,
            item.client_type,
            item.date,
            item.last_activity
        ]);
    }

    const csvContent = rows.map(e => e.map(cell => '"' + ('' + (cell || '')).replace(/"/g, '""') + '"').join(",")).join("\n");

    self.postMessage({
        type: 'CSV_READY',
        csvContent: csvContent,
        rowCount: self.dataset.length
    });
}
