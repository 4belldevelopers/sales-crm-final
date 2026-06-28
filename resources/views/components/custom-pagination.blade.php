@props(['paginator'])

@if ($paginator->hasPages())
<div id="paginationcopanylist">

    <style>
    .custom-pagination {
        display: flex;
        gap: 5px;
        justify-content: flex-end;
        margin-top: 15px;
    }
    .custom-pagination .page-link {
        padding: 0.375rem 0.75rem;
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 0.475rem;
        height: 2.5rem;
        min-width: 2.5rem;
        font-weight: 500;
        font-size: 1.075rem;
        text-decoration: none;
        border: none;
        transition: background 0.2s;
    }
    .custom-pagination .page-link:hover {
        background: #f0f0f0;
    }
    .custom-pagination .page-link.active {
        background-color: #007bff;
        color: white;
    }
    .pagination-info {
        text-align: left;
        margin-bottom: 10px;
        font-weight: bold;
    }
    .pagination-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        width: 100%;
    }
    .previous_disabled, .next_disabled, .active_disabled {
        pointer-events: none;
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>




{{-- Pagination Info --}}
<div class="pagination-info">
    Showing {{ $paginator->firstItem() ?? 0 }}
    to {{ $paginator->lastItem() ?? 0 }}
    of {{ $paginator->total() }} records.
</div>

<div class="pagination-wrapper">
    <div class="custom-pagination">

        {{-- Previous Link --}}
        @if ($paginator->onFirstPage())
            <a href="#" class="page-link previous_disabled">Previous</a>
        @else
            <a href="{{ $paginator->appends(request()->query())->previousPageUrl() }}" class="page-link">Previous</a>
        @endif

        {{-- Page Numbers --}}
        @php
            $start = max(1, $paginator->currentPage() - 2);
            $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
        @endphp

        @if ($start > 1)
            <a href="{{ $paginator->appends(request()->query())->url(1) }}" class="page-link">1</a>
            @if ($start > 2)
                <span class="page-link dots">...</span>
            @endif
        @endif

        @for ($i = $start; $i <= $end; $i++)
            <a href="{{ $paginator->appends(request()->query())->url($i) }}"
               class="page-link {{ $paginator->currentPage() == $i ? 'active active_disabled' : '' }}">
               {{ $i }}
            </a>
        @endfor

        @if ($end < $paginator->lastPage())
            @if ($end < $paginator->lastPage() - 1)
                <span class="page-link dots">...</span>
            @endif
            <a href="{{ $paginator->appends(request()->query())->url($paginator->lastPage()) }}" class="page-link">
                {{ $paginator->lastPage() }}
            </a>
        @endif

        {{-- Next Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->appends(request()->query())->nextPageUrl() }}" class="page-link">Next</a>
        @else
            <a href="#" class="page-link next_disabled">Next</a>
        @endif
    </div>
</div>
</div>
@endif
