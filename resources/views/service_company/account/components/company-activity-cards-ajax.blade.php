@forelse($items as $act)
  <div class="coa-timeline-item" data-category="{{ $act['category'] ?? 'pipeline' }}" data-id="{{ $act['id'] }}">
    {{-- Left Column: Actor Avatar Node & Vertical Track --}}
    <div class="coa-timeline-node">
      <div class="coa-actor-avatar" title="{{ $act['actor_name'] ?? 'Team Member' }}">
        @if(!empty($act['actor_image']))
          <img src="{{ $act['actor_image'] }}" alt="{{ $act['actor_name'] }}" />
        @else
          <span class="coa-actor-initials">{{ $act['actor_initials'] ?? 'TM' }}</span>
        @endif
      </div>
      <div class="coa-timeline-track"></div>
    </div>

    {{-- Right Column: Modern Timeline Sub-Card --}}
    <div class="coa-card">
      <div class="coa-card-head">
        <div class="coa-card-head-left">
          <span class="coa-badge" style="background: {{ $act['badge_bg'] ?? '#eff6ff' }}; color: {{ $act['badge_color'] ?? '#3b82f6' }};">
            <i class="{{ $act['icon'] ?? 'fas fa-bolt' }} me-1" style="font-size: 10px;"></i>
            {{ $act['badge_text'] ?? 'Activity' }}
          </span>
          <span class="coa-action-title">{{ $act['action'] }}</span>
        </div>
        <div class="coa-card-time" title="{{ $act['formatted_date'] }}">
          <span>{{ $act['time_ago'] }}</span>
          <span class="coa-bullet">·</span>
          <span class="coa-date-full">{{ $act['formatted_date'] }}</span>
        </div>
      </div>

      <div class="coa-card-body">
        <h4 class="coa-entity-title">{{ $act['title'] }}</h4>
        @if(!empty($act['description']))
          <p class="coa-description">{{ $act['description'] }}</p>
        @endif
      </div>

      <div class="coa-card-foot">
        <div class="coa-actor-info">
          <span class="coa-actor-label">Performed by</span>
          <span class="coa-actor-name">{{ $act['actor_name'] }}</span>
        </div>
        @if(!empty($act['link']))
          <a href="{{ $act['link'] }}" class="coa-link-btn" target="_blank">
            <span>{{ $act['link_label'] ?? 'View Details' }}</span>
            <i class="fas fa-arrow-up-right-from-square ms-1" style="font-size: 10px;"></i>
          </a>
        @endif
      </div>
    </div>
  </div>
@empty
  <div class="coa-empty-state">
    <div class="coa-empty-icon">
      <i class="fas fa-clock-rotate-left"></i>
    </div>
    <div class="coa-empty-title">No Company Activities Found</div>
    <div class="coa-empty-sub">
      Activities performed by your team members across job requisitions, candidate pipeline, interviews, offers, and team invitations will appear here dynamically in real time.
    </div>
  </div>
@endforelse
