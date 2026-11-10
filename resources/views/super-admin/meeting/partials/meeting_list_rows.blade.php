{{--
    Partial: Google Meet Pool Table Rows
    Module: Super Admin Google Meeting Setting
    Strict CQRS: Rendered from Elasticsearch-hydrated LengthAwarePaginator
--}}
@forelse($google_meeting_setting as $key => $item)
    @php
        $initial = strtoupper(substr($item->account_name ?: 'G', 0, 1));
        $isActive = ((int) $item->status === 1);
    @endphp
    <tr id="row-{{ $item->id }}" data-id="{{ $item->id }}">
        <!-- Checkbox -->
        <td class="text-center">
            <div class="form-check form-check-sm form-check-custom form-check-solid justify-content-center">
                <input class="form-check-input row-checkbox" type="checkbox" value="{{ $item->id }}" data-id="{{ $item->id }}">
            </div>
        </td>

        <!-- Row Index -->
        <td class="text-center text-muted fw-bold fs-7">
            {{ $google_meeting_setting->firstItem() + $key }}
        </td>

        <!-- Host Account / Email -->
        <td>
            <div class="host-account-wrapper">
                <div class="host-avatar">
                    {{ $initial }}
                </div>
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6 text-hover-primary item-account-name">
                        {{ $item->account_name ?: 'System Host' }}
                    </span>
                    <span class="text-muted fs-8">Google Host ID #{{ $item->id }}</span>
                </div>
            </div>
        </td>

        <!-- Meeting Link URL -->
        <td>
            <div class="d-flex align-items-center gap-2">
                <div class="meet-link-pill" title="{{ $item->meeting_link }}">
                    <i class="fas fa-video text-success fs-7"></i>
                    <span class="meet-link-text item-meeting-link">
                        {{ $item->meeting_link }}
                    </span>
                </div>
                <button type="button"
                        class="btn btn-icon btn-light btn-sm btn-copy-clipboard"
                        data-clipboard-text="{{ $item->meeting_link }}"
                        title="Copy URL to clipboard">
                    <i class="far fa-copy text-muted fs-7"></i>
                </button>
                <a href="{{ $item->meeting_link }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn btn-icon btn-light-success btn-sm"
                   title="Test / Join Google Meet in new tab">
                    <i class="fas fa-external-link-alt fs-7"></i>
                </a>
            </div>
        </td>

        <!-- Status Toggle -->
        <td class="text-center">
            <div class="d-flex flex-column align-items-center gap-1">
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input status-toggle"
                           type="checkbox"
                           value="{{ $item->id }}"
                           id="status_switch_{{ $item->id }}"
                           {{ $isActive ? 'checked' : '' }}
                           data-id="{{ $item->id }}"
                           data-current="{{ $item->status }}">
                </div>
                <span class="status-badge-container">
                    @if($isActive)
                        <span class="badge-status-active">Active</span>
                    @else
                        <span class="badge-status-inactive">Paused</span>
                    @endif
                </span>
            </div>
        </td>

        <!-- Actions -->
        <td class="text-end pe-6">
            <div class="d-inline-flex gap-1">
                <button type="button"
                        class="btn btn-icon btn-light-primary btn-sm btn-edit-link"
                        data-id="{{ $item->id }}"
                        data-account="{{ $item->account_name }}"
                        data-link="{{ $item->meeting_link }}"
                        title="Edit account email or link URL">
                    <i class="fas fa-edit fs-7"></i>
                </button>
                <button type="button"
                        class="btn btn-icon btn-light-danger btn-sm btn-delete-single"
                        data-id="{{ $item->id }}"
                        title="Delete this link from pool">
                    <i class="fas fa-trash-alt fs-7"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-10">
            <div class="d-flex flex-column align-items-center justify-content-center">
                <div class="symbol symbol-60px mb-4">
                    <div class="symbol-label bg-light-warning text-warning">
                        <i class="fas fa-video-slash fs-2x"></i>
                    </div>
                </div>
                <h4 class="text-dark fw-bolder mb-1">No Google Meeting Links Found</h4>
                <p class="text-muted fs-7 mb-4">
                    @if(!empty($meeting_link) || ($statusFilter !== null && $statusFilter !== ''))
                        No meeting links match your search/filter criteria.
                    @else
                        Your Google Meet pool is currently empty. Add meeting links to enable candidate interview scheduling.
                    @endif
                </p>
                <button type="button" class="btn btn-sm btn-primary fw-bold" id="btnEmptyStateAdd">
                    <i class="fas fa-plus-circle me-1"></i> Add Meeting Links Now
                </button>
            </div>
        </td>
    </tr>
@endforelse
