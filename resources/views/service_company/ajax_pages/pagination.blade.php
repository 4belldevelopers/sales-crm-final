<style>
/* ── Pagination bar ───────────────────────────── */
.pagi-bar{
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:10px;
    padding:12px 20px;
    border-top:1px solid #F0F2F5;
    background:#fff;
    font-family:'Poppins',sans-serif;
    width:100%;
}
.pagi-info{
    font-size:13px; font-weight:500; color:#64748B;
}
.pagi-right{
    display:flex; align-items:center; gap:6px;
}
/* Prev / Next arrow buttons */
.pagi-arrow{
    width:32px; height:32px;
    border:1.5px solid #E5E9F0; border-radius:8px;
    background:#fff; color:#64748B;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; font-size:14px; transition:all .15s;
}
.pagi-arrow:hover:not(.pagi-disabled){ background:#2563EB; color:#fff; border-color:#2563EB; }
.pagi-arrow.pagi-disabled{ opacity:.35; pointer-events:none; cursor:not-allowed; }
/* Page number buttons */
.pagi-page{
    min-width:32px; height:32px; padding:0 8px;
    border:1.5px solid #E5E9F0; border-radius:8px;
    background:#fff; color:#374151;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; font-size:13px; font-weight:600;
    transition:all .15s; font-family:'Poppins',sans-serif;
}
.pagi-page:hover:not(.pagi-active-disabled){ background:#F5F8FF; }
.pagi-page.pagi-active{
    background:#2563EB; color:#fff;
    border-color:#2563EB; pointer-events:none;
}
.pagi-page.pagi-active-disabled{ pointer-events:none; }
.pagi-dots{
    font-size:13px; color:#94A3B8; padding:0 4px;
    display:flex; align-items:center;
}
/* Fixed page-size label (10 / page) — not interactive, no selector */
.pagi-perpage{
    height:32px; padding:0 10px;
    display:inline-flex; align-items:center;
    border:1.5px solid #E5E9F0; border-radius:8px;
    font-size:13px; font-weight:500; color:#374151;
    background:#fff;
    font-family:'Poppins',sans-serif; margin-left:8px;
}
</style>

@if($pagination['total'] > 0)
@php
    $perPage    = $pagination['per_page'] ?? 10;
    $total      = $pagination['total'];
    $totalPages = $pagination['last_page'];
    $curPage    = $pagination['current_page'];
    $listType   = $pagination['listType'] ?? 'records';
    $from       = ($curPage - 1) * $perPage + 1;
    $to         = min($curPage * $perPage, $total);
    $label      = (isset($pagination['listType']) && $pagination['listType'] === 'allcandidate')
                  ? 'companies' : 'records';
@endphp

<div class="pagi-bar">
    {{-- Left: info text --}}
    <div class="pagi-info">
        Showing {{ $from }} to {{ $to }} of {{ $total }} {{ $label }}
    </div>

    {{-- Right: prev + pages + next + per-page --}}
    <div class="pagi-right">
        {{-- Prev --}}
        <button class="pagi-arrow {{ $curPage <= 1 ? 'pagi-disabled' : '' }}"
                data-page="{{ $curPage - 1 }}" data-listType="{{ $listType }}">
            <i class="ti ti-chevron-left"></i>
        </button>

        {{-- Page numbers --}}
        @for ($i = 1; $i <= $totalPages; $i++)
            @if ($i == 1 || $i == $totalPages || ($i >= $curPage - 2 && $i <= $curPage + 2))
                <button class="pagi-page {{ $i == $curPage ? 'pagi-active pagi-active-disabled' : '' }}"
                        data-page="{{ $i }}" data-listType="{{ $listType }}">{{ $i }}</button>
            @elseif ($i == $curPage - 3 || $i == $curPage + 3)
                <span class="pagi-dots">…</span>
            @endif
        @endfor

        {{-- Next --}}
        <button class="pagi-arrow {{ $curPage >= $totalPages ? 'pagi-disabled' : '' }}"
                data-page="{{ $curPage + 1 }}" data-listType="{{ $listType }}">
            <i class="ti ti-chevron-right"></i>
        </button>

    </div>
</div>
@endif
