@if($pagination['total'] > 0)
@php
    $perPage    = $pagination['per_page'] ?? 10;
    $total      = $pagination['total'];
    $totalPages = $pagination['last_page'];
    $curPage    = $pagination['current_page'];
    $listType   = $pagination['listType'] ?? 'clients';
    $from       = ($curPage - 1) * $perPage + 1;
    $to         = min($curPage * $perPage, $total);
@endphp

<div class="cl-pagi-bar">
    {{-- Left: Showing info text --}}
    <div class="cl-pagi-info">
        Showing {{ $from }} to {{ $to }} of {{ $total }} clients
    </div>

    {{-- Right: Rows per page + arrows + page numbers --}}
    <div class="cl-pagi-controls">
        <div class="cl-pagi-perpage-wrap">
            <span class="cl-pagi-perpage-lbl">Rows per page:</span>
            <select class="cl-pagi-perpage-select pagi-perpage">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>

        {{-- Prev Button --}}
        <button class="cl-pagi-btn pagi-arrow {{ $curPage <= 1 ? 'pagi-disabled' : '' }}"
                data-page="{{ $curPage - 1 }}" data-listType="{{ $listType }}" aria-label="Previous page">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"/>
            </svg>
        </button>

        {{-- Page Numbers --}}
        @for ($i = 1; $i <= $totalPages; $i++)
            @if ($i == 1 || $i == $totalPages || ($i >= $curPage - 2 && $i <= $curPage + 2))
                <button class="cl-pagi-page pagi-page {{ $i == $curPage ? 'pagi-active pagi-active-disabled' : '' }}"
                        data-page="{{ $i }}" data-listType="{{ $listType }}">{{ $i }}</button>
            @elseif ($i == $curPage - 3 || $i == $curPage + 3)
                <span class="cl-pagi-dots">…</span>
            @endif
        @endfor

        {{-- Next Button --}}
        <button class="cl-pagi-btn pagi-arrow {{ $curPage >= $totalPages ? 'pagi-disabled' : '' }}"
                data-page="{{ $curPage + 1 }}" data-listType="{{ $listType }}" aria-label="Next page">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m9 18 6-6-6-6"/>
            </svg>
        </button>
    </div>
</div>
@endif
