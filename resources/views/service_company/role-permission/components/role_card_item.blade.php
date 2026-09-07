<?php
    $roleId      = $rlist->id;
    $uCount      = $allRoleStats[$roleId]['userCount'] ?? 0;
    $roleUsers   = $allRoleStats[$roleId]['users'] ?? collect();
    $grpCount    = $allRoleStats[$roleId]['grpCount'] ?? 0;
    $palette     = $cardPalette[$loop->index % count($cardPalette)];
    $access      = $allRoleStats[$roleId]['access'] ?? (function_exists('rpAccessLevel') ? rpAccessLevel($grpCount, $rlist->name ?? '') : ['label' => 'Low Access', 'class' => 'badge-low']);
    $isCustom    = (bool) $rlist->manual_add;
    $roleType    = $isCustom ? 'custom' : 'system';
    $grpAll      = $rlist->grouped_permissions ?? collect();
    $grpKeys     = $grpAll instanceof \Illuminate\Support\Collection ? $grpAll->keys()->all() : array_keys((array) $grpAll);
    $grpShown    = array_slice($grpKeys, 0, 3);
    $grpExtra    = count($grpKeys) > 3 ? count($grpKeys) - 3 : 0;
    $detailJson  = $allRoleStats[$roleId]['detailJson'] ?? '{}';
?>
<div class="rp-role-card"
     data-role-type="{{ $roleType }}"
     data-role-name="{{ strtolower(ucwords($rlist->display_name)) }}"
     data-user-count="{{ $uCount }}"
     data-grp-count="{{ $grpCount }}"
     data-access-level="{{ $access['class'] }}"
     data-role-id="{{ $roleId }}"
     data-role-detail='{{ $detailJson }}'
     onclick="openRoleFormFromCard(this)">

    <div class="rp-card-top">
        <div class="rp-card-icon-wrap">
            <div class="rp-card-icon" style="background:{{ $palette['bg'] }};">
                {!! rpCardIcon($palette['icon'], $palette['stroke']) !!}
            </div>
            <div>
                <div class="rp-card-role-name">{{ ucwords($rlist->display_name) }}</div>
                <div class="rp-card-role-type {{ $isCustom ? 'custom' : '' }}">
                    {{ $isCustom ? 'Custom Role' : 'System Role' }}
                </div>
            </div>
        </div>
        <span class="access-badge {{ $access['class'] }}">{{ $access['label'] }}</span>
    </div>

    <div class="rp-card-counts">
        <div>
            <div class="rp-count-val">{{ $grpCount }}</div>
            <div class="rp-count-lbl">Permission Groups</div>
        </div>
        <div>
            <div class="rp-count-val">{{ $uCount }}</div>
            <div class="rp-count-lbl">{{ $uCount == 1 ? 'User' : 'Users' }}</div>
        </div>
    </div>

    @if(count($grpShown) > 0)
    <div class="rp-perm-label">Permission Groups</div>
    <div class="rp-perm-tags">
        @foreach($grpShown as $gk)
        <span class="rp-perm-tag">{{ \App\Helper\GeneralMethod::roleGroupLabel($gk) }}</span>
        @endforeach
        @if($grpExtra > 0)
        <span class="rp-perm-tag">+{{ $grpExtra }} more</span>
        @endif
    </div>
    @endif

    <div class="rp-users-row">
        <span class="rp-users-label">Users</span>
        @if($uCount > 0)
        <div class="rp-avatars-stack">
            @for($ai = 0; $ai < min($uCount, 4); $ai++)
            <div class="rp-mini-av" style="background:{{ $avColors[$ai % count($avColors)] }};">
                {{ chr(65 + $ai) }}
            </div>
            @endfor
        </div>
        @if($uCount > 4)
        <span class="rp-more-users">+{{ $uCount - 4 }} more</span>
        @endif
        @else
        <span style="font-size:12px;color:#b0bbd8;">No users assigned</span>
        @endif
    </div>

    <div class="rp-card-actions" onclick="event.stopPropagation()">
        <div class="rp-action-group">
            <button type="button" class="rp-edit-btn" onclick="event.stopPropagation(); openRoleFormFromCard(this.closest('.rp-role-card'));">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Edit
            </button>
            @if($rlist->manual_add)
            <button class="rp-del-btn role_delete" data-id="{{ $roleId }}" onclick="event.stopPropagation()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    <path d="M10 11v6"/><path d="M14 11v6"/>
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                </svg>
                Delete
            </button>
            @endif
        </div>
    </div>
</div>
