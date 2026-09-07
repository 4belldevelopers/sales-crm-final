/**
 * Consultancy Pagination Helper
 *
 * INTENT: Handles client-side pagination computations (page windows, last page calculation, item ranges).
 */
const ConsultancyPagination = {
    calculatePagination(totalItems, currentPage = 1, pageSize = 10) {
        const total = Math.max(0, Number(totalItems) || 0);
        const size = Math.max(1, Number(pageSize) || 10);
        const lastPage = Math.max(1, Math.ceil(total / size));
        const page = Math.min(Math.max(1, Number(currentPage) || 1), lastPage);

        const from = total === 0 ? 0 : (page - 1) * size + 1;
        const to = Math.min(page * size, total);

        return {
            total,
            perPage: size,
            currentPage: page,
            lastPage,
            from,
            to,
            hasMore: page < lastPage
        };
    }
};

if (typeof window !== 'undefined') {
    window.ConsultancyPagination = ConsultancyPagination;
}
