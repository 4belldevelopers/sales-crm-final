@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;

    $avatarColors = ['#8b5cf6', '#3b82f6', '#f59e0b', '#10b981', '#ec4899', '#06b6d4', '#6366f1'];
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $name = trim(@$row->name ?: 'Unknown');
            $initials = '';
            $parts = preg_split('/\s+/', $name);
            if (count($parts) >= 2) {
                $initials = mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1);
            } else {
                $initials = mb_substr($name, 0, 2);
            }
            $initials = strtoupper($initials);

            $colorIndex = abs(crc32($name)) % count($avatarColors);
            $bgColor = $avatarColors[$colorIndex];

            $imagePath = '';
            if (!empty($row->image) && strlen($row->image) > 5) {
                $imagePath = $row->image;
            }

            $status_disabled = 'disabled';
            $status = strtolower(@$row->status ?: 'active');
            if ($status === 'active') {
                $status_disabled = '';
            }

            $designation = @$row->designation ?: @$row->display_name ?: 'Contact Person';
            $department = @$row->department ?: @$row->display_name ?: 'HR';

            $timeAgo = 'Recently';
            if (!empty($row->created_at)) {
                try {
                    $ts = is_numeric($row->created_at) ? (int)$row->created_at : strtotime($row->created_at);
                    if ($ts && $ts > 0) {
                        $timeAgo = Carbon::createFromTimestamp($ts)->diffForHumans();
                    }
                } catch (\Exception $e) {
                    $timeAgo = 'Recently';
                }
            }

            $mobile = trim(@$row->mobile ?: '');
            $email = trim(@$row->email ?: '');
            $cleanMobile = preg_replace('/[^0-9]/', '', $mobile);
            $waNumber = '';
            if (!empty($cleanMobile)) {
                $waNumber = (strlen($cleanMobile) == 10) ? '91' . $cleanMobile : $cleanMobile;
            }
        @endphp
        <tr>
            <!-- 1. Contact (Avatar + Name + Designation + Primary Badge) -->
            <td>
                <div class="contact-cell">
                    <div class="contact-avatar-table" style="background-color: {{ $bgColor }};" aria-hidden="true">
                        @if (!empty($imagePath))
                            <img src="{{ $imagePath }}" alt="{{ $name }}" style="width: 100%; height: 100%; object-fit: cover;" />
                        @else
                            {{ $initials }}
                        @endif
                    </div>
                    <div class="contact-info-table">
                        <div class="contact-name-table show-detail-team" data-row-id="{{ $row->id }}" title="{{ $name }}" tabindex="0" role="button" aria-label="View details for {{ $name }}">
                            {{ ucwords(mb_substr($name, 0, 30)) }}
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
                            <span class="contact-role-table">{{ ucwords($designation) }}</span>
                            @if ($row->root_user == 1)
                                <span class="contact-primary-badge">
                                    <i class="fas fa-check" aria-hidden="true"></i> Primary
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </td>

            <!-- 3. Department -->
            <td>
                <span class="dept-badge">{{ ucwords($department) }}</span>
            </td>

            <!-- 4. Email & Phone -->
            <td>
                <div class="contact-email-phone">
                    @if (!empty($email))
                        <div>
                            <a href="mailto:{{ $email }}" class="email-cell" aria-label="Email {{ $email }}">{{ $email }}</a>
                        </div>
                    @else
                        <div>
                            <span class="text-muted fs-7">—</span>
                        </div>
                    @endif
                    <div class="phone-cell">
                        <div class="phone-flag" aria-hidden="true">
                            <svg viewBox="0 0 36 24">
                                <rect width="36" height="8" fill="#FF9932" />
                                <rect y="8" width="36" height="8" fill="#ffffff" />
                                <rect y="16" width="36" height="8" fill="#138808" />
                                <circle cx="18" cy="12" r="3" fill="#000080" opacity="0.3" />
                            </svg>
                        </div>
                        <div class="phone-number d-inline-flex align-items-center gap-1">
                            <span class="phone-prefix">+91</span>
                            @if (!empty($mobile))
                                <span class="phone-digits">{{ $mobile }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </td>

            <!-- 5. Last Contacted -->
            <td>
                <span class="text-muted fs-7">{{ $timeAgo }}</span>
            </td>

            <!-- 6. Status -->
            <td>
                <span class="status-badge-table {{ $status === 'active' ? 'active' : 'inactive' }}">
                    <i class="fas fa-circle" aria-hidden="true"></i> {{ $status === 'active' ? 'Active' : 'Inactive' }}
                </span>
            </td>

            <!-- 6. Actions -->
            <td>
                <div class="action-btns">
                    @if (!empty($cleanMobile))
                        <a href="tel:+91{{ $cleanMobile }}" class="action-btn-table" data-bs-toggle="tooltip" data-toggle="tooltip" title="Call" aria-label="Call {{ $name }}">
                            <i class="fas fa-phone" aria-hidden="true"></i>
                        </a>
                    @else
                        <span class="action-btn-table opacity-50 cursor-not-allowed" data-bs-toggle="tooltip" data-toggle="tooltip" title="Call" aria-label="No phone available">
                            <i class="fas fa-phone text-muted" aria-hidden="true"></i>
                        </span>
                    @endif

                    @if (!empty($email))
                        <a href="mailto:{{ $email }}" class="action-btn-table" data-bs-toggle="tooltip" data-toggle="tooltip" title="Email" aria-label="Email {{ $name }}">
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                        </a>
                    @else
                        <span class="action-btn-table opacity-50 cursor-not-allowed" data-bs-toggle="tooltip" data-toggle="tooltip" title="Email" aria-label="No email available">
                            <i class="fas fa-envelope text-muted" aria-hidden="true"></i>
                        </span>
                    @endif

                    @if (!empty($waNumber))
                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer" class="action-btn-table whatsapp" data-bs-toggle="tooltip" data-toggle="tooltip" title="WhatsApp" aria-label="WhatsApp {{ $name }}">
                            <i class="fab fa-whatsapp" aria-hidden="true"></i>
                        </a>
                    @else
                        <span class="action-btn-table opacity-50 cursor-not-allowed" data-bs-toggle="tooltip" data-toggle="tooltip" title="WhatsApp" aria-label="WhatsApp not available">
                            <i class="fab fa-whatsapp text-muted" aria-hidden="true"></i>
                        </span>
                    @endif

                    <div class="dropdown d-inline-block">
                        <button type="button" class="action-btn-table more" data-bs-toggle="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-display="static" title="More" aria-label="More actions for {{ $name }}">
                            <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-end shadow-sm border-0 py-2" style="min-width: 140px; z-index: 1050;">
                            <a class="dropdown-item py-2 px-3 fs-7 text-dark" href="{{ route('superadmin.team.edit', [$row->id]) }}">
                                <i class="fa fa-edit text-primary me-2 fs-7" aria-hidden="true"></i> Edit Contact
                            </a>
                            <a class="dropdown-item py-2 px-3 fs-7 text-dark show-detail-team" href="javascript:void(0);" data-row-id="{{ $row->id }}">
                                <i class="fa fa-eye text-info me-2 fs-7" aria-hidden="true"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="6" style="text-align: center; color: #a1a5b7; padding: 36px 16px; font-weight: 500;">
            <i class="fas fa-address-book fs-3 text-muted mb-2 d-block"></i>
            No contacts found for this company
        </td>
    </tr>
@endif
