@if($pagination['total'] > 0)
@php
    $perPage    = $pagination['per_page'] ?? 10;
    $total      = $pagination['total'];
    $totalPages = (int)$pagination['last_page'];
    $curPage    = (int)$pagination['current_page'];
    $listType   = $pagination['listType'] ?? 'records';
    $from       = ($curPage - 1) * $perPage + 1;
    $to         = min($curPage * $perPage, $total);
@endphp
<style>
.sa-pagination-bar {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap;
    gap: 12px;
    padding: 14px 20px;
    border-top: 1px solid #eff2f5;
    background: #ffffff;
    font-family: 'Poppins', sans-serif;
    width: 100%;
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
    box-sizing: border-box;
}
.sa-pagination-info {
    font-size: 13px;
    font-weight: 500;
    color: #7e8299;
    margin: 0;
}
.sa-pagination-controls {
    display: flex !important;
    align-items: center !important;
    gap: 6px;
    margin: 0;
    padding: 0;
}
.sa-pagination-controls .page-link {
    min-width: 32px;
    height: 32px;
    padding: 0 8px;
    border: 1.5px solid #e4e6ef !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    color: #5e6278 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.15s ease;
    text-decoration: none;
    line-height: 1;
    box-shadow: none !important;
    outline: none !important;
    margin: 0;
}
.sa-pagination-controls .page-link:hover:not(.active):not(.disabled):not(.previous_disabled):not(.next_disabled):not(.pagi-disabled) {
    background: #f1faff !important;
    border-color: #009ef7 !important;
    color: #009ef7 !important;
}
.sa-pagination-controls .page-link.active,
.sa-pagination-controls .page-link.pagi-active {
    background: #009ef7 !important;
    border-color: #009ef7 !important;
    color: #ffffff !important;
    cursor: default;
    pointer-events: none;
}
.sa-pagination-controls .page-link.disabled,
.sa-pagination-controls .page-link.previous_disabled,
.sa-pagination-controls .page-link.next_disabled,
.sa-pagination-controls .page-link.pagi-disabled {
    opacity: 0.35 !important;
    cursor: not-allowed !important;
    pointer-events: none;
    background: #ffffff !important;
    border-color: #eff2f5 !important;
    color: #b5b5c3 !important;
}
.sa-pagination-controls .page-link.dots {
    border: none !important;
    background: transparent !important;
    cursor: default;
    pointer-events: none;
    color: #b5b5c3 !important;
    padding: 0 4px;
    min-width: auto;
}
</style>

<div class="sa-pagination-bar">
    <div class="sa-pagination-info">
        Showing {{ $from }} to {{ $to }} of {{ $total }} records.
    </div>
    <div class="sa-pagination-controls">
        {{-- Previous Button --}}
        <button type="button" class="page-link pagi-arrow {{ ($curPage > 1) ? '' : 'disabled previous_disabled pagi-disabled' }}" data-page="{{ $curPage - 1 }}" data-listType="{{ $listType }}" aria-label="Previous Page">
            <i class="fas fa-chevron-left" style="font-size: 11px;" aria-hidden="true"></i>
        </button>

        {{-- Page Numbers --}}
        @for ($i = 1; $i <= $totalPages; $i++)
            @if ($i == 1 || $i == $totalPages || ($i >= $curPage - 2 && $i <= $curPage + 2))
                <button type="button" class="page-link pagi-page {{ $i == $curPage ? 'active pagi-active disabled active_disabled pagi-active-disabled' : '' }}" data-page="{{ $i }}" data-listType="{{ $listType }}" aria-label="Page {{ $i }}">
                    {{ $i }}
                </button>
            @elseif ($i == $curPage - 3 || $i == $curPage + 3)
                <span class="page-link dots" aria-hidden="true">…</span>
            @endif
        @endfor

        {{-- Next Button --}}
        <button type="button" class="page-link pagi-arrow {{ ($curPage < $totalPages) ? '' : 'disabled next_disabled pagi-disabled' }}" data-page="{{ $curPage + 1 }}" data-listType="{{ $listType }}" aria-label="Next Page">
            <i class="fas fa-chevron-right" style="font-size: 11px;" aria-hidden="true"></i>
        </button>
    </div>
</div>
@endif
