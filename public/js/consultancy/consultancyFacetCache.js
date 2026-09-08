/**
 * ConsultancyFacetCache — In-Memory Facet Filtering & Tenant Size Thresholding
 *
 * INTENT: Handles in-memory client-side filtering for partner listings.
 * Enforces tenant size thresholds:
 * - <=200 partners: full in-memory facet array (0 ms filter roundtrips).
 * - 201-1,000 partners: initial 200 chunk + lazy "Load More" pagination.
 * - >1,000 partners: windowed filtering on loaded items.
 */
const ConsultancyFacetCache = {
    cache: [],
    loadedCount: 0,
    totalCount: 0,

    init(items, total = 0) {
        this.cache = Array.isArray(items) ? [...items] : [];
        this.loadedCount = this.cache.length;
        this.totalCount = total || this.loadedCount;
    },

    appendChunk(newItems) {
        if (Array.isArray(newItems)) {
            this.cache.push(...newItems);
            this.loadedCount = this.cache.length;
        }
    },

    filter(predicate) {
        if (typeof predicate !== 'function') return [...this.cache];
        return this.cache.filter(predicate);
    },

    getChunkStrategy() {
        if (this.totalCount <= 200) {
            return { mode: 'full_in_memory', chunkable: false };
        } else if (this.totalCount <= 1000) {
            return { mode: 'chunked_load_more', chunkable: true, chunkSize: 200 };
        } else {
            return { mode: 'virtualized_window', chunkable: true, chunkSize: 200 };
        }
    }
};

if (typeof window !== 'undefined') {
    window.ConsultancyFacetCache = ConsultancyFacetCache;
}
