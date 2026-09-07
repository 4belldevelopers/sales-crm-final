/**
 * ConsultancyDrawerEngine — Zero-Server-Load Client Metrics
 *
 * INTENT: Provides presentation-layer calculations in the browser (date trend windows,
 * delta percentages, candidate status grouping, Intl currency formatting, and DOM hydration).
 * CONSTRAINT: No business-logic changes. Same status IDs [1..8] and formula rules as legacy PHP.
 */
const ConsultancyDrawerEngine = {

    // 1. Date trend windows (7-day, 30-day)
    computeTrendWindows(rawEvents) {
        if (!Array.isArray(rawEvents)) {
            return { count7d: 0, count30d: 0, total: 0 };
        }
        const now = Date.now();
        const DAY_MS = 86400000;
        const sevenDaysAgo  = now - (7 * DAY_MS);
        const thirtyDaysAgo = now - (30 * DAY_MS);
        let count7d = 0, count30d = 0, total = rawEvents.length;

        for (let i = 0; i < total; i++) {
            const time = new Date(rawEvents[i].created_at).getTime();
            if (time >= sevenDaysAgo)  count7d++;
            if (time >= thirtyDaysAgo) count30d++;
        }
        return { count7d, count30d, total };
    },

    // 2. Growth delta %
    calculateGrowthDelta(currentCount, previousCount) {
        if (!previousCount || previousCount === 0) {
            return currentCount > 0
                ? { pct: 100, trend: 'up' }
                : { pct: 0,   trend: 'neutral' };
        }
        const delta = ((currentCount - previousCount) / previousCount) * 100;
        return {
            pct:   Math.abs(Math.round(delta * 10) / 10),
            trend: delta >= 0 ? 'up' : 'down'
        };
    },

    // 3. Status grouping [1..8] -> status labels and colors
    groupCandidatesByStatus(bucketData) {
        if (!Array.isArray(bucketData)) return [];

        const STATUS_LOOKUP = {
            1: { name: 'Sourced',     color: '#3699FF' },
            2: { name: 'Screening',   color: '#FFA800' },
            3: { name: 'Evaluation',  color: '#8950FC' },
            4: { name: 'Interview',   color: '#1BC5BD' },
            5: { name: 'Offer',       color: '#F64E60' },
            6: { name: 'Joined',      color: '#0BB783' },
            7: { name: 'Rejected',    color: '#F64E60' },
            8: { name: 'Withdrawn',   color: '#B5B5C3' }
        };
        return bucketData.map(item => ({
            statusId:   item.key,
            count:      item.doc_count,
            label:      STATUS_LOOKUP[item.key]?.name     || 'Other',
            badgeColor: STATUS_LOOKUP[item.key]?.color    || '#E4E6EF'
        }));
    },

    // 4. Intl currency formatting
    formatCurrency(amount, currencyCode = 'INR') {
        const numericAmount = Number(amount) || 0;
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: currencyCode,
            maximumFractionDigits: 0
        }).format(numericAmount);
    },

    // 5. Drawer DOM hydration
    renderDrawer(data) {
        if (!data) return;

        const trends = this.computeTrendWindows(data.sourcing_events || []);
        const growth = this.calculateGrowthDelta(trends.count7d, data.previous_7d_count || 0);

        const elPartnerName = document.getElementById('drawer_partner_name');
        if (elPartnerName && data.partner?.name) elPartnerName.textContent = data.partner.name;

        const elActiveJobs = document.getElementById('drawer_active_jobs');
        if (elActiveJobs && data.active_jobs_count !== undefined) elActiveJobs.textContent = data.active_jobs_count;

        const elSourcing7d = document.getElementById('drawer_sourcing_7d');
        if (elSourcing7d) elSourcing7d.textContent = trends.count7d;

        const elGrowthBadge = document.getElementById('drawer_growth_badge');
        if (elGrowthBadge) {
            elGrowthBadge.textContent = `${growth.trend === 'up' ? '+' : '-'}${growth.pct}%`;
        }
    }
};

if (typeof window !== 'undefined') {
    window.ConsultancyDrawerEngine = ConsultancyDrawerEngine;
}
