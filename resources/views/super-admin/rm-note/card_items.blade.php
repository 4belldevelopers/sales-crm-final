@php
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

@forelse($templates as $tpl)
    @php
        $initial = mb_substr($tpl->title ?? 'T', 0, 1);
        $palette = $getPalette($initial);
    @endphp
    <div class="brmnote-card" id="brmnote-card-{{ $tpl->id }}" data-id="{{ $tpl->id }}" data-status="{{ $tpl->status ? 'active' : 'inactive' }}" data-search="{{ strtolower($tpl->id . ' #' . $tpl->id . ' ' . $tpl->title . ' ' . strip_tags($tpl->content)) }}">
        <div class="brmnote-card-grid">

            <!-- Sub-Card 1: Identity & Avatar -->
            <div class="brmnote-subcard-identity">
                <div class="brmnote-avatar-box" style="background-color: {{ $palette['bg'] }}; color: {{ $palette['color'] }}; border: 1px solid {{ $palette['border'] }};">
                    {{ strtoupper($initial) }}
                </div>
                <div class="brmnote-info-wrap">
                    <div class="brmnote-card-title" onclick="openPreviewModal({{ $tpl->id }})" title="Click to preview note">{{ $tpl->title }}</div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="brmnote-id-pill" title="Template ID">#{{ $tpl->id }}</span>
                        <span class="brmnote-scope-pill"><i class="fas fa-globe fs-9 me-1"></i> Bench RM Master</span>
                    </div>
                </div>
            </div>

            <!-- Sub-Card 2: Content Preview Snippet -->
            <div class="brmnote-subcard-content">
                <div class="brmnote-preview-snippet" id="preview-text-{{ $tpl->id }}">
                    {{ $tpl->preview_text ?: strip_tags($tpl->content) }}
                </div>
                <a href="javascript:void(0)" class="brmnote-view-full-link" onclick="openPreviewModal({{ $tpl->id }})">
                    <i class="fas fa-eye fs-9"></i> View Full Note
                </a>
            </div>

            <!-- Sub-Card 3: Telemetry & Temporal Metrics -->
            <div class="brmnote-subcard-telemetry">
                <div class="brmnote-metric-pill">
                    <i class="fas fa-file-alt text-muted fs-9"></i>
                    <span>{{ $tpl->word_count }} Words • {{ $tpl->char_count }} Chars</span>
                </div>
                <div class="brmnote-date-text">
                    <i class="fas fa-calendar-alt text-muted fs-9"></i>
                    <span>{{ $tpl->created_formatted }}</span>
                </div>
                <div class="brmnote-date-text text-muted fs-9">
                    <i class="fas fa-clock fs-9"></i>
                    <span>Updated {{ $tpl->updated_human }}</span>
                </div>
            </div>

            <!-- Sub-Card 4: Live Status Switch -->
            <div class="brmnote-subcard-status">
                <div class="form-check form-switch form-check-custom form-check-solid mb-1">
                    <input class="form-check-input brmnote-status-toggle" type="checkbox" id="switch-{{ $tpl->id }}" data-id="{{ $tpl->id }}" {{ $tpl->status ? 'checked' : '' }} onchange="toggleTemplateStatus({{ $tpl->id }}, this)">
                </div>
                <span class="brmnote-status-badge {{ $tpl->status ? 'active' : 'inactive' }}" id="status-badge-{{ $tpl->id }}">
                    {{ $tpl->status ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <!-- Sub-Card 5: Action Toolbar -->
            <div class="brmnote-subcard-actions">
                <button type="button" class="btn btn-sm btn-icon btn-light-primary brmnote-action-btn" onclick="copyTemplateContent({{ $tpl->id }})" title="Copy template content" data-bs-toggle="tooltip">
                    <i class="fas fa-copy"></i>
                </button>
                <button type="button" class="btn btn-sm btn-icon btn-light-info brmnote-action-btn" onclick="duplicateTemplate({{ $tpl->id }})" title="Duplicate template" data-bs-toggle="tooltip">
                    <i class="fas fa-clone"></i>
                </button>
                <button type="button" class="btn btn-sm btn-icon btn-light-warning brmnote-action-btn" onclick="openEditModal({{ $tpl->id }})" title="Edit template" data-bs-toggle="tooltip">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-icon btn-light-danger brmnote-action-btn" onclick="deleteTemplate({{ $tpl->id }})" title="Delete template" data-bs-toggle="tooltip">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

        </div>
    </div>
@empty
    <div class="brmnote-empty-card" id="brmnoteEmptyCard">
        <div class="brmnote-empty-icon">
            <i class="fas fa-user-tag"></i>
        </div>
        <h5 class="fw-bold text-gray-800 mb-1">No Relationship Manager Templates Found</h5>
        <p class="text-muted fs-7 mb-3">Get started by creating your first global Bench Candidate evaluation rubric.</p>
        <button type="button" class="btn btn-sm btn-primary" onclick="openAddModal()">
            <i class="fas fa-plus me-1"></i> Add First Template
        </button>
    </div>
@endforelse
