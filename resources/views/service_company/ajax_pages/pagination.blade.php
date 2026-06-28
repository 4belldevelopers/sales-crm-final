<style>
    .custom-pagination {
        display: flex;
        gap: 5px;
        justify-content: flex-end; /* Align pagination to the left */
        margin-top: 0px;
    }

    .custom-pagination .page-link {
        padding: 0.375rem 0.75rem;
        /* border: 1px solid #ddd; */
        cursor: pointer;
        /* background-color: #f8f9fa; */
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 0.475rem;
        height: 2.5rem;
        min-width: 2.5rem;
        font-weight: 500;
        font-size: 1.075rem;
        /* border-radius: 5px; */
    }

    .custom-pagination .page-link.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination-info {
        text-align: left; /* Align info to the left */
        margin-bottom: 0px;
        font-weight: bold;
    }

    .pagination-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-end; /* Align entire pagination block to the left */
        width: 80%;    
        gap: 14px;
    }
    .previous_disabled{
        pointer-events: none; /* Disable clicking */
        opacity: 0.5; /* Make it visually look disabled */
        cursor: not-allowed; /* Change the cursor to indicate it's disabled */
    }
    .next_disabled{
        pointer-events: none; /* Disable clicking */
        opacity: 0.5; /* Make it visually look disabled */
        cursor: not-allowed; /* Change the cursor to indicate it's disabled */
    }
    .active_disabled{
        pointer-events: none; /* Disable clicking */
        opacity: 0.5; /* Make it visually look disabled */
        cursor: not-allowed; /* Change the cursor to indicate it's disabled */
    }
    .pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 11px 14px;
        border-top: 1px solid #e4e6ef;
        border-bottom: 1px solid #e4e6ef;
        background: #ffffff;
    }
</style>
@if($pagination['total'] >0)
<div class="pagination-bar">
    <div class="pagination-info">
        Showing {{ ($pagination['current_page'] - 1) * 10 + 1 }} 
        to {{ min($pagination['current_page'] * 10, $pagination['total']) }} 
        of {{ $pagination['total'] }} records.
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
