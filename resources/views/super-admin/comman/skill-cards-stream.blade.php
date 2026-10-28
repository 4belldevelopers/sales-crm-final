@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    // Deterministic palette mapping for avatar initials
    $getPalette = function($char) {
        $c = strtoupper($char);
        if (in_array($c, ['A', 'I', 'Q', 'Y'])) return ['bg' => '#eff6ff', 'color' => '#2563eb', 'border' => '#dbeafe'];
        if (in_array($c, ['B', 'J', 'R', 'Z'])) return ['bg' => '#ecfdf5', 'color' => '#059669', 'border' => '#a7f3d0'];
        if (in_array($c, ['C', 'K', 'S']))      return ['bg' => '#fdf4ff', 'color' => '#c026d3', 'border' => '#f5d0fe'];
        if (in_array($c, ['D', 'L', 'T']))      return ['bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fde68a'];
        if (in_array($c, ['E', 'M', 'U']))      return ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0'];
        if (in_array($c, ['F', 'N', 'V']))      return ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecaca'];
        if (in_array($c, ['G', 'O', 'W']))      return ['bg' => '#f5f3ff', 'color' => '#7c3aed', 'border' => '#ddd6fe'];
        if (in_array($c, ['H', 'P', 'X']))      return ['bg' => '#e0f2fe', 'color' => '#0284c7', 'border' => '#bae6fd'];
        return ['bg' => '#f8fafc', 'color' => '#475569', 'border' => '#e2e8f0'];
    };
@endphp

<div id="skillsStreamContainer" class="cd-skills-stream">
    {{-- Stream Control Bar: Column Headers & Select All --}}
    @if(!$dataskill->isEmpty())
        <div class="cd-stream-header-strip mb-2">
            <div class="cd-skill-grid">
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-custom form-check-solid me-3">
                        <input type="checkbox" class="form-check-input" id="chkSelectAllSkills" title="Select / Deselect all on this page">
                    </div>
                    <span class="cd-strip-label">SKILL & IDENTIFIER</span>
                </div>
                <div>
                    <span class="cd-strip-label">INDEX & TELEMETRY</span>
                </div>
                <div>
                    <span class="cd-strip-label">TAXONOMY STATUS & SCOPE</span>
                </div>
                <div class="text-end">
                    <span class="cd-strip-label">ACTIONS</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Primary Cards Stream --}}
    @forelse ($dataskill as $row)
        @php
            $initial = mb_substr($row->skill_name ?? 'S', 0, 1);
            $palette = $getPalette($initial);
            $skillName = e($row->skill_name ?? '');

            // Highlight search query if active
            if (!empty($q)) {
                $quotedQ = preg_quote($q, '/');
                $highlightedName = preg_replace('/(' . $quotedQ . ')/i', '<span class="cd-search-highlight">$1</span>', $skillName);
            } else {
                $highlightedName = $skillName;
            }

            $createdDisplay = !empty($row->created_at)
                ? Carbon::parse($row->created_at)->format('d M Y, h:i A')
                : 'Taxonomy Baseline';
        @endphp

        <div class="cd-skill-card" id="skill-card-{{ $row->id }}" data-id="{{ $row->id }}" data-name="{{ $row->skill_name }}">
            <div class="cd-skill-grid">

                {{-- Sub-Card 1: Skill Identity & Monogram Avatar --}}
                <div class="cd-subcard cd-subcard-identity">
                    <div class="form-check form-check-custom form-check-solid me-3">
                        <input type="checkbox" class="form-check-input cd-skill-checkbox" value="{{ $row->id }}" data-name="{{ $row->skill_name }}" title="Select skill for batch operations">
                    </div>
                    <div class="cd-skill-avatar" style="background-color: {{ $palette['bg'] }}; color: {{ $palette['color'] }}; border: 1px solid {{ $palette['border'] }};">
                        <span>{{ strtoupper($initial) }}</span>
                    </div>
                    <div class="cd-skill-name-wrap">
                        <div class="cd-skill-title">{!! $highlightedName !!}</div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="cd-skill-id-badge" title="Elasticsearch Document ID: {{ $row->id }}">
                                #{{ Str::limit($row->id, 18) }}
                            </span>
                            <span class="badge badge-light-dark fs-9 fw-semibold py-0 px-2">Taxonomy Keyword</span>
                        </div>
                    </div>
                </div>

                {{-- Sub-Card 2: Index & Temporal Telemetry --}}
                <div class="cd-subcard cd-subcard-telemetry">
                    <div class="cd-telemetry-date">
                        <i class="fa fa-calendar-alt text-muted me-1"></i>
                        <span>{{ $createdDisplay }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1 mt-1">
                        <span class="badge badge-light-primary fw-bold fs-9">
                            <i class="fa-solid fa-database text-primary fs-9 me-1"></i>local_skills
                        </span>
                        <span class="badge badge-light-success fw-bold fs-9" title="Zero MySQL reads on queries">
                            <i class="fa-solid fa-bolt text-success fs-9 me-1"></i>Sub-5ms
                        </span>
                    </div>
                </div>

                {{-- Sub-Card 3: Taxonomy Status & Clipboard Copy --}}
                <div class="cd-subcard cd-subcard-status">
                    <span class="badge badge-light-success fw-bolder fs-8 py-1 px-2">
                        <i class="fa fa-check-circle text-success fs-9 me-1"></i>Active Master
                    </span>
                    <span class="badge badge-light-info fw-semibold fs-9 ms-1" title="Used in candidate skills, job requisitions, and autocomplete">
                        ATS Global
                    </span>
                    <button type="button" class="btn btn-sm btn-icon btn-light-primary btn-copy-skill ms-2" data-skill="{{ $row->skill_name }}" title="Copy normalized skill name">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>

                {{-- Sub-Card 4: Administrative Actions --}}
                <div class="cd-subcard cd-subcard-actions">
                    <button type="button" class="btn btn-sm btn-light-primary fw-bold px-3 py-1 btn-edit-skill me-2" data-id="{{ $row->id }}" data-name="{{ $row->skill_name }}" title="Edit Skill Name">
                        <i class="fa fa-edit me-1"></i> Edit
                    </button>
                    <button type="button" class="btn btn-sm btn-light-danger fw-bold px-3 py-1 btn-delete-skill" data-id="{{ $row->id }}" data-name="{{ $row->skill_name }}" title="Delete Skill">
                        <i class="fa fa-trash me-1"></i> Delete
                    </button>
                </div>

            </div>
        </div>
    @empty
        {{-- Empty State Card --}}
        <div class="cd-empty-card">
            <div class="cd-empty-icon">
                <i class="fa-solid fa-tags"></i>
            </div>
            <h5 class="fw-bolder text-gray-800 mb-1">No Master Skills Found</h5>
            <p class="text-muted fs-7 mb-4">No skills matched your search query or active filter selection.</p>
            <button type="button" class="btn btn-sm btn-light-primary fw-bold js-btn-reset-filters">
                <i class="fa fa-rotate me-1"></i> Reset All Filters
            </button>
        </div>
    @endforelse

    {{-- Modern Pagination Card --}}
    @if ($dataskill->hasPages())
        <div class="cd-pagination-card mt-3">
            <x-custom-pagination :paginator="$dataskill" />
        </div>
    @endif
</div>
