{{-- Team list footer: result summary + numbered pagination + rows-per-page.
     Mirrors the shared ajax pagination but uses "members" wording and the
     rows-per-page control from the redesigned team page. --}}
@if($pagination['total'] > 0)
    @php
        $perPage      = $pagination['per_page'] ?? 10;
        $currentPage  = $pagination['current_page'];
        $totalPages   = $pagination['last_page'];
        $total        = $pagination['total'];
        $listType     = $pagination['listType'];
        $from         = ($currentPage - 1) * $perPage + 1;
        $to           = min($currentPage * $perPage, $total);
    @endphp
    <div class="team-pagination-bar">
        <div class="team-pagination-info">
            Showing {{ $from }} to {{ $to }} of {{ $total }} members
        </div>

        <div class="team-pagination-pages">
            <button class="team-page-link team-page-arrow {{ $currentPage > 1 ? '' : 'is-disabled' }}"
                data-page="{{ $currentPage - 1 }}" data-listType="{{ $listType }}" aria-label="Previous">
                <i class="ki-outline ki-left"></i>
            </button>

            @for ($i = 1; $i <= $totalPages; $i++)
                @if ($i == 1 || $i == $totalPages || ($i >= $currentPage - 1 && $i <= $currentPage + 1))
                    <button class="team-page-link {{ $i == $currentPage ? 'is-active' : '' }}"
                        data-page="{{ $i }}" data-listType="{{ $listType }}">{{ $i }}</button>
                @elseif ($i == $currentPage - 2 || $i == $currentPage + 2)
                    <span class="team-page-dots">…</span>
                @endif
            @endfor

            <button class="team-page-link team-page-arrow {{ $currentPage < $totalPages ? '' : 'is-disabled' }}"
                data-page="{{ $currentPage + 1 }}" data-listType="{{ $listType }}" aria-label="Next">
                <i class="ki-outline ki-right"></i>
            </button>
        </div>

        <div class="team-pagination-rows">
            <span>Rows per page</span>
            <select id="teamRowsPerPage" onchange="changeTeamPageSize(this.value)">
                @foreach ([10, 25, 50, 100] as $opt)
                    <option value="{{ $opt }}" {{ $perPage == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>
    </div>
@endif
