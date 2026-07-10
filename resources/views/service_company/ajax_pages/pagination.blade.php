<style>
    .custom-pagination {
        display: flex;
        gap: 5px;
        justify-content: flex-end;
        margin-top: 0px;
    }

    .custom-pagination .page-link {
        padding: 0.375rem 0.75rem;
        cursor: pointer;
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 0.475rem;
        height: 2.5rem;
        min-width: 2.5rem;
        font-weight: 500;
        font-size: 1.075rem;
    }

    .custom-pagination .page-link.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination-info {
        text-align: left;
        margin-bottom: 0px;
        font-weight: 500;
        color: #5e6278;
        font-size: 13px;
    }

    .pagination-wrapper {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .previous_disabled{
        pointer-events: none;
        opacity: 0.5;
        cursor: not-allowed;
    }
    .next_disabled{
        pointer-events: none;
        opacity: 0.5;
        cursor: not-allowed;
    }
    .active_disabled{
        pointer-events: none;
        opacity: 0.5;
        cursor: not-allowed;
    }
    .pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        padding: 11px 14px;
        border-top: 1px solid #e4e6ef;
        background: #ffffff;
    }
    /* rows-per-page selector: cosmetic only where no backend size-change is wired elsewhere */
    .rows-per-page-select {
        border: 1px solid #e4e6ef;
        border-radius: 0.475rem;
        padding: 0.35rem 0.75rem;
        font-size: 13px;
        font-weight: 500;
        color: #5e6278;
        background-color: #f9f9f9;
    }
</style>
@if($pagination['total'] >0)
@php
    // per_page falls back to 10 for the ~70 other controller actions that render this
    // shared partial without passing it, so this change is backward compatible.
    $perPage = $pagination['per_page'] ?? 10;
@endphp
<div class="pagination-bar">
    <div class="d-flex align-items-center gap-4">
        <div class="pagination-info">
            Showing {{ ($pagination['current_page'] - 1) * $perPage + 1 }}
            to {{ min($pagination['current_page'] * $perPage, $pagination['total']) }}
            of {{ $pagination['total'] }} records.
        </div>
        @if(isset($pagination['listType']) && $pagination['listType'] === 'allcandidate')
            <div class="d-flex align-items-center gap-2">
                <span class="pagination-info mb-0">Rows per page</span>
                <select class="rows-per-page-select" id="rowsPerPageSelect">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
        @endif
    </div>
    <div class="pagination-wrapper">

        <div class="custom-pagination">
            @php
                $totalPages = $pagination['last_page'];
                $currentPage = $pagination['current_page'];
                $maxLinks = 5; // Number of links to display before truncating
                $listType = $pagination['listType'];
            @endphp

            {{-- Previous Button --}}
            {{-- @if ($currentPage > 1) --}}
                <button class="page-link {{ ($currentPage > 1) ? '' : 'previous_disabled' }}" data-page="{{ $currentPage - 1 }}" data-listType="{{ $listType }}">Previous</button>
            {{-- @endif --}}

            {{-- Page Links --}}
            @for ($i = 1; $i <= $totalPages; $i++)
                {{-- Always show first and last pages --}}
                @if ($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2))

                    @if($i<=1000)
                        <button class="page-link {{ $i == $currentPage ? 'active active_disabled' : '' }} " data-page="{{ $i }}" data-listType="{{ $listType }}">{{ $i }}</button>
                        @else

                    @endif

                @elseif ($i == $currentPage - 3 || $i == $currentPage + 3)
                    {{-- Show dots when skipping pages --}}
                    <span class="page-link dots">...</span>
                @endif
            @endfor

            {{-- Next Button --}}
            {{-- @if ($currentPage < $totalPages) --}}
                <button class="page-link {{ ($currentPage < $totalPages) ? '' : 'next_disabled' }}" data-page="{{ $currentPage + 1 }}" data-listType="{{ $listType }}">Next</button>
            {{-- @endif --}}
        </div>
    </div>
</div>

@endif
