@php
    $eu          = $editUser ?? null;
    $initials    = $eu ? strtoupper(substr(trim($eu->name ?? 'U'), 0, 1)) : 'U';
    $isOnline    = ($eu->status ?? '') === 'active';
    $memberSince = $eu ? \Carbon\Carbon::parse($eu->created_at)->format('d M Y') : '';

    // Icon map for known role name patterns
    $roleIcons = [
        'admin'        => ['icon' => 'fa-crown',      'bg' => '#ede9fe', 'color' => '#7c3aed'],
        'recruiter'    => ['icon' => 'fa-users',      'bg' => '#dbeafe', 'color' => '#2563eb'],
        'interviewer'  => ['icon' => 'fa-comments',   'bg' => '#d1fae5', 'color' => '#059669'],
        'sales'        => ['icon' => 'fa-chart-bar',  'bg' => '#fef3c7', 'color' => '#d97706'],
        'sub'          => ['icon' => 'fa-user-shield','bg' => '#fce7f3', 'color' => '#db2777'],
        'default'      => ['icon' => 'fa-gear',       'bg' => '#ffedd5', 'color' => '#ea580c'],
    ];

    function getRoleIcon($roleName, $roleIcons) {
        $lower = strtolower($roleName);
        foreach ($roleIcons as $key => $cfg) {
            if (str_contains($lower, $key)) return $cfg;
        }
        return ['icon' => 'fa-shield-halved', 'bg' => '#f1f5f9', 'color' => '#64748b'];
    }

    // Split roles: first one full-width (if only 1 or odd), rest in pairs
    $rolesArr  = collect($roleslist ?? []);
    $selRoleId = $selrole->role_id ?? null;
@endphp

<input type="hidden" name="cuser_id" id="cuser_id" value="{{ $id ?? '' }}" />

{{-- ── User info card ── --}}
<div style="display:flex; align-items:center; justify-content:space-between; background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:18px 20px; margin-bottom:24px;">
    <div style="display:flex; align-items:center; gap:14px;">
        {{-- Avatar --}}
        <div style="position:relative; flex-shrink:0;">
            @if (!empty($eu->image))
                <img src="{{ $eu->image }}" alt="" style="width:58px; height:58px; border-radius:50%; object-fit:cover; border:2px solid #e2e8f0;">
            @else
                <div style="width:58px; height:58px; border-radius:50%; background:#6366f1; color:#fff; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:700; font-family:'Poppins',sans-serif;">{{ $initials }}</div>
            @endif
            <span style="position:absolute; bottom:2px; right:2px; width:12px; height:12px; border-radius:50%; background:{{ $isOnline ? '#22c55e' : '#94a3b8' }}; border:2px solid #fff;"></span>
        </div>
        {{-- Info --}}
        <div>
            <div style="font-size:16px; font-weight:700; color:#111827; font-family:'Poppins',sans-serif; margin-bottom:3px;">{{ ucwords(strtolower($eu->name ?? '')) }}</div>
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                <span style="font-size:13px; color:#6b7280; font-family:'Poppins',sans-serif;">{{ $eu->email ?? '' }}</span>
                <button type="button" onclick="navigator.clipboard.writeText('{{ $eu->email ?? '' }}')" style="background:none; border:none; padding:0; cursor:pointer; color:#9ca3af; font-size:12px;" title="Copy email">
                    <i class="fa-regular fa-copy"></i>
                </button>
            </div>
            <div style="font-size:12px; color:#94a3b8; font-family:'Poppins',sans-serif;">Member since {{ $memberSince }}</div>
        </div>
    </div>
    {{-- Current role badge --}}
    @if ($currentRoleName)
    <div style="text-align:right; flex-shrink:0; margin-left:16px;">
        <div style="font-size:11px; color:#9ca3af; font-family:'Poppins',sans-serif; margin-bottom:6px; font-weight:500; text-transform:uppercase; letter-spacing:.5px;">Current Role</div>
        <div style="display:inline-flex; align-items:center; gap:7px; background:#eff6ff; border:1.5px solid #bfdbfe; border-radius:8px; padding:6px 13px;">
            <i class="fa-solid fa-user-tie" style="color:#2563eb; font-size:13px;"></i>
            <span style="font-size:13px; font-weight:600; color:#1d4ed8; font-family:'Poppins',sans-serif;">{{ $currentRoleName }}</span>
        </div>
    </div>
    @endif
</div>

{{-- ── Select New Role ── --}}
<div style="margin-bottom:20px;">
    <div style="font-size:15px; font-weight:700; color:#111827; font-family:'Poppins',sans-serif; margin-bottom:4px;">Select New Role</div>
    <div style="font-size:13px; color:#6b7280; font-family:'Poppins',sans-serif; margin-bottom:16px;">Choose a role to update the user's permissions.</div>

    <div id="roleCardsGrid" style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        @foreach ($rolesArr as $i => $rlist)
            @php
                $isSelected = ($selRoleId == $rlist->id);
                $cfg        = getRoleIcon($rlist->display_name, $roleIcons);
                // Full width if only 1 item OR last item when count is odd
                $fullWidth  = ($rolesArr->count() == 1) || ($rolesArr->count() % 2 == 1 && $loop->last);
            @endphp
            <label for="role_{{ $rlist->id }}"
                class="role-card-label"
                data-role-id="{{ $rlist->id }}"
                style="
                    grid-column: {{ $fullWidth ? '1 / -1' : 'auto' }};
                    display:flex; align-items:flex-start; gap:13px;
                    border:1.5px solid {{ $isSelected ? '#6366f1' : '#e5e7eb' }};
                    border-radius:12px;
                    padding:14px 16px;
                    cursor:pointer;
                    background:{{ $isSelected ? '#f5f3ff' : '#fff' }};
                    transition:border-color .15s, background .15s;
                    position:relative;
                ">
                {{-- Icon --}}
                <div style="width:40px; height:40px; border-radius:10px; background:{{ $cfg['bg'] }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa-solid {{ $cfg['icon'] }}" style="font-size:16px; color:{{ $cfg['color'] }};"></i>
                </div>
                {{-- Text --}}
                <div style="flex:1; min-width:0;">
                    <div style="font-size:13px; font-weight:600; color:#111827; font-family:'Poppins',sans-serif; margin-bottom:3px;">{{ ucwords($rlist->display_name) }}</div>
                    <div style="font-size:12px; color:#6b7280; font-family:'Poppins',sans-serif; line-height:1.4;">{{ ucwords($rlist->description) }}</div>
                </div>
                {{-- Radio --}}
                <input type="radio" name="user_role" id="role_{{ $rlist->id }}"
                    value="{{ $rlist->id }}"
                    {{ $isSelected ? 'checked' : '' }}
                    style="width:18px; height:18px; accent-color:#6366f1; flex-shrink:0; margin-top:2px; cursor:pointer;">
            </label>
        @endforeach
    </div>
</div>

{{-- ── Warning banner ── --}}
<div style="display:flex; align-items:flex-start; gap:12px; background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:14px 16px; margin-top:8px;">
    <i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b; font-size:16px; flex-shrink:0; margin-top:1px;"></i>
    <div>
        <div style="font-size:13px; font-weight:600; color:#92400e; font-family:'Poppins',sans-serif; margin-bottom:2px;">Permission changes apply immediately.</div>
        <div style="font-size:12px; color:#b45309; font-family:'Poppins',sans-serif;">The user will be updated with the new role and access rights.</div>
    </div>
</div>

<script>
// Highlight selected role card
document.querySelectorAll('.role-card-label').forEach(function(card) {
    var radio = card.querySelector('input[type="radio"]');
    if (radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.role-card-label').forEach(function(c) {
                c.style.borderColor = '#e5e7eb';
                c.style.background  = '#fff';
            });
            card.style.borderColor = '#6366f1';
            card.style.background  = '#f5f3ff';
        });
    }
});
</script>
