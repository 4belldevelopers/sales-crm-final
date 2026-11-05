// Verification test for Client-First Derived-State Engine & Card Builder
const assert = require('assert');

// Test data simulating raw_data from ServiceCompanyTeamController::data_ajax
const sampleUsers = [
    {
        id: 1,
        name: 'Vibhanshu Jaiswal',
        email: 'vibhanshu@4bell.com',
        calling_code: '91',
        mobile: '7217860087',
        phone: '+91 7217860087',
        display_name: 'Administrator',
        role_name: 'Administrator',
        designation: 'Directors',
        status: 'active',
        root_user: 1,
        password: '$2y$10$hashedpassword',
        created_at: '2026-10-08 12:00:00',
        last_login: new Date(Date.now() - 3 * 3600 * 1000).toISOString(),
        pre_active_jobs: 30,
        pre_captain_jobs: 42,
        pre_trm_talents: 0,
        pre_rank: 1,
        pre_score: 0,
        pre_rank_month: 'Sep 2026',
        is_client_rep: true,
        is_agency_rep: false,
        pre_agency_shares: 0,
        channels: ['client', 'agency', 'bench'],
        priorities: []
    },
    {
        id: 2,
        name: 'Amit Kumar',
        email: 'amit@4bell.com',
        calling_code: '91',
        mobile: '9876543210',
        phone: '+91 9876543210',
        display_name: 'Recruiter',
        role_name: 'Recruiter',
        designation: 'Talent Acquisition',
        status: 'active',
        root_user: 0,
        password: '$2y$10$hashedpassword',
        created_at: '2026-10-15 10:00:00',
        last_login: new Date(Date.now() - 10 * 60 * 1000).toISOString(),
        pre_active_jobs: 5,
        pre_captain_jobs: 2,
        pre_trm_talents: 12,
        pre_rank: 2,
        pre_score: 45,
        pre_rank_month: 'Sep 2026',
        is_client_rep: false,
        is_agency_rep: false,
        pre_agency_shares: 0,
        channels: ['bench'],
        priorities: []
    },
    {
        id: 3,
        name: 'Sarah Connor',
        email: 'sarah@4bell.com',
        calling_code: '1',
        mobile: '5551234567',
        phone: '+1 5551234567',
        display_name: 'Lead Recruiter',
        role_name: 'Lead Recruiter',
        designation: 'Senior Lead',
        status: 'inactive',
        root_user: 0,
        password: '$2y$10$hashedpassword',
        created_at: '2026-09-01 10:00:00',
        last_login: null,
        pre_active_jobs: 0,
        pre_captain_jobs: 0,
        pre_trm_talents: 0,
        pre_rank: 'N/A',
        pre_score: 0,
        pre_rank_month: '',
        is_client_rep: false,
        is_agency_rep: false,
        pre_agency_shares: 0,
        channels: [],
        priorities: []
    },
    {
        id: 4,
        name: 'New Pending Member',
        email: 'pending@4bell.com',
        calling_code: '',
        mobile: '',
        phone: '',
        display_name: 'Recruiter',
        role_name: 'Recruiter',
        designation: 'Recruiter',
        status: 'invited',
        root_user: 0,
        password: '',
        created_at: '2026-10-30 10:00:00',
        last_login: null,
        pre_active_jobs: 0,
        pre_captain_jobs: 0,
        pre_trm_talents: 0,
        pre_rank: 'N/A',
        pre_score: 0,
        pre_rank_month: '',
        is_client_rep: false,
        is_agency_rep: false,
        pre_agency_shares: 0,
        channels: [],
        priorities: []
    }
];

// Mock window.teamState
const teamState = {
    records: sampleUsers,
    isFullDataset: true,
    statusTab: 'all',
    searchQuery: '',
    sortVal: '',
    pageSize: 10,
    currentPage: 1
};

function getDerivedRecords(state) {
    if (!state || !Array.isArray(state.records)) return [];
    var statusTab = state.statusTab || 'all';
    var query = (state.searchQuery || '').trim().toLowerCase();
    var sortVal = state.sortVal || '';

    // 1. SHALLOW CLONE TO GUARANTEE IMMUTABILITY
    var list = state.records.map(function(item) {
        return Object.assign({}, item);
    });

    // 2. STATUS TAB FILTERING
    if (statusTab && statusTab !== 'all') {
        list = list.filter(function(r) {
            var channels = Array.isArray(r.channels) ? r.channels : [];
            var status = (r.status || '').toLowerCase();
            var isInvited = !r.password || status === 'invited';

            if (statusTab === 'client') {
                return channels.indexOf('client') !== -1 || !!r.is_client_rep;
            } else if (statusTab === 'vendor' || statusTab === 'agency') {
                return channels.indexOf('agency') !== -1 || channels.indexOf('vendor') !== -1 || !!r.is_agency_rep || ((r.pre_agency_shares || 0) > 0);
            } else if (statusTab === 'recruitment' || statusTab === 'bench') {
                return channels.indexOf('bench') !== -1 || channels.indexOf('recruitment') !== -1 || (parseInt(r.root_user, 10) !== 1 && status === 'active');
            } else if (statusTab === 'inactive') {
                return status === 'inactive';
            } else if (statusTab === 'invited') {
                return isInvited;
            } else if (statusTab === 'active') {
                return status === 'active' && !isInvited;
            }
            return true;
        });
    }

    // 3. SEARCH QUERY FILTERING
    if (query) {
        list = list.filter(function(r) {
            var name = (r.name || '').toLowerCase();
            var email = (r.email || '').toLowerCase();
            var phone = (r.phone || r.mobile || '').toLowerCase();
            var designation = (r.designation || '').toLowerCase();
            var role = (r.display_name || r.role_name || '').toLowerCase();
            return name.indexOf(query) !== -1 ||
                   email.indexOf(query) !== -1 ||
                   phone.indexOf(query) !== -1 ||
                   designation.indexOf(query) !== -1 ||
                   role.indexOf(query) !== -1;
        });
    }

    // 4. MULTI-DIMENSIONAL SORTING
    list.sort(function(a, b) {
        switch (sortVal) {
            case '1': // Joined: Newest to Oldest
                var tA1 = a.created_at ? new Date(a.created_at).getTime() : 0;
                var tB1 = b.created_at ? new Date(b.created_at).getTime() : 0;
                if (tA1 !== tB1) return tB1 - tA1;
                return (b.id || 0) - (a.id || 0);

            case '2': // Joined: Oldest to Newest
                var tA2 = a.created_at ? new Date(a.created_at).getTime() : 0;
                var tB2 = b.created_at ? new Date(b.created_at).getTime() : 0;
                if (tA2 !== tB2) return tA2 - tB2;
                return (a.id || 0) - (b.id || 0);

            case 'name_asc': // Name: A to Z
                return (a.name || '').localeCompare(b.name || '');

            case 'name_desc': // Name: Z to A
                return (b.name || '').localeCompare(a.name || '');

            case 'designation_asc': // Designation: A to Z
                var desA = a.designation || a.role_name || '';
                var desB = b.designation || b.role_name || '';
                return desA.localeCompare(desB);

            case 'role_asc': // System Role: A to Z
                var rA = a.display_name || a.role_name || '';
                var rB = b.display_name || b.role_name || '';
                return rA.localeCompare(rB);

            case 'active_jobs_desc': // Workload: Active Jobs High to Low
                var ajDiff = (b.pre_active_jobs || 0) - (a.pre_active_jobs || 0);
                if (ajDiff !== 0) return ajDiff;
                return (b.id || 0) - (a.id || 0);

            case 'bench_talents_desc': // Workload: Bench Talent Rep High to Low
                var btDiff = (b.pre_trm_talents || 0) - (a.pre_trm_talents || 0);
                if (btDiff !== 0) return btDiff;
                return (b.id || 0) - (a.id || 0);

            case 'score_desc': // Performance Score High to Low
                var scDiff = (b.pre_score || b.score || 0) - (a.pre_score || a.score || 0);
                if (scDiff !== 0) return scDiff;
                return (b.id || 0) - (a.id || 0);

            case 'status_active': // Account Status: Active First
                var aAct = (a.status === 'active') ? 1 : 0;
                var bAct = (b.status === 'active') ? 1 : 0;
                if (aAct !== bAct) return bAct - aAct;
                return (b.id || 0) - (a.id || 0);

            case 'last_active':
            default: // Last Active (Most Recent First)
                var aTime = a.last_login ? new Date(a.last_login).getTime() : 0;
                var bTime = b.last_login ? new Date(b.last_login).getTime() : 0;
                if (aTime !== bTime) return bTime - aTime;
                return (b.id || 0) - (a.id || 0);
        }
    });

    return list;
}

// -------------------------------------------------------------
// RUN UNIT TESTS
// -------------------------------------------------------------
console.log('Testing Derived-State Engine...');

// Test 1: Immutability (canonical dataset must never be altered)
const originalFirstId = teamState.records[0].id;
teamState.sortVal = 'name_asc';
const sortedByName = getDerivedRecords(teamState);
assert.strictEqual(teamState.records[0].id, originalFirstId, 'Canonical records must NOT be mutated by sorting!');
assert.strictEqual(sortedByName[0].name, 'Amit Kumar', 'First member should be Amit Kumar alphabetically');
console.log('✅ Test 1 Passed: Canonical state immutability preserved');

// Test 2: Status Tab: Bench Talent Enquiries ('bench' / 'recruitment')
teamState.sortVal = '';
teamState.statusTab = 'bench';
const benchMembers = getDerivedRecords(teamState);
assert.strictEqual(benchMembers.length, 2, 'Should have 2 bench members (Vibhanshu via channel & Amit via role/channel)');
console.log('✅ Test 2 Passed: Bench Talent Enquiries filtering');

// Test 3: Status Tab: Client & Sales Enquiries ('client')
teamState.statusTab = 'client';
const clientMembers = getDerivedRecords(teamState);
assert.strictEqual(clientMembers.length, 1, 'Should have 1 client rep (Vibhanshu)');
assert.strictEqual(clientMembers[0].name, 'Vibhanshu Jaiswal');
console.log('✅ Test 3 Passed: Client & Sales Enquiries filtering');

// Test 4: Status Tab: Agency Queries ('agency' / 'vendor')
teamState.statusTab = 'agency';
const agencyMembers = getDerivedRecords(teamState);
assert.strictEqual(agencyMembers.length, 1, 'Should have 1 agency rep (Vibhanshu)');
console.log('✅ Test 4 Passed: Agency Queries filtering');

// Test 5: Status Tab: Inactive Members
teamState.statusTab = 'inactive';
const inactiveMembers = getDerivedRecords(teamState);
assert.strictEqual(inactiveMembers.length, 1, 'Should have 1 inactive member (Sarah Connor)');
assert.strictEqual(inactiveMembers[0].name, 'Sarah Connor');
console.log('✅ Test 5 Passed: Inactive Members filtering');

// Test 6: Status Tab: Pending Invites ('invited')
teamState.statusTab = 'invited';
const invitedMembers = getDerivedRecords(teamState);
assert.strictEqual(invitedMembers.length, 1, 'Should have 1 invited member');
assert.strictEqual(invitedMembers[0].name, 'New Pending Member');
console.log('✅ Test 6 Passed: Pending Invites filtering');

// Test 7: Full-text Search
teamState.statusTab = 'all';
teamState.searchQuery = '7217860087';
const phoneSearch = getDerivedRecords(teamState);
assert.strictEqual(phoneSearch.length, 1, 'Should find Vibhanshu by phone');
assert.strictEqual(phoneSearch[0].id, 1);

teamState.searchQuery = 'senior lead';
const desigSearch = getDerivedRecords(teamState);
assert.strictEqual(desigSearch.length, 1, 'Should find Sarah Connor by designation');
assert.strictEqual(desigSearch[0].id, 3);
console.log('✅ Test 7 Passed: Full-text multi-field search');

// Test 8: Multi-dimensional sorting
teamState.searchQuery = '';
teamState.sortVal = 'active_jobs_desc';
const jobSorted = getDerivedRecords(teamState);
assert.strictEqual(jobSorted[0].id, 1, 'Vibhanshu has 30 active jobs (highest)');
assert.strictEqual(jobSorted[1].id, 2, 'Amit has 5 active jobs (second)');

teamState.sortVal = 'bench_talents_desc';
const benchSorted = getDerivedRecords(teamState);
assert.strictEqual(benchSorted[0].id, 2, 'Amit has 12 bench talents (highest)');

teamState.sortVal = 'score_desc';
const scoreSorted = getDerivedRecords(teamState);
assert.strictEqual(scoreSorted[0].id, 2, 'Amit has 45 score pts (highest)');
console.log('✅ Test 8 Passed: Multi-dimensional sorting (active jobs, bench talents, score)');

console.log('====================================================');
console.log('ALL CLIENT-FIRST DERIVED-STATE TESTS PASSED 100%!');
console.log('====================================================');

// Card & Pagination HTML generator
function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function getAvatarClass(name) {
    var raw = (name || 'User').trim();
    var first = (raw[0] || 'u').toLowerCase();
    var code = first.charCodeAt(0);
    if (code >= 97 && code <= 101) return 'bg-light-danger text-danger';
    if (code >= 102 && code <= 106) return 'bg-light-warning text-warning';
    if (code >= 107 && code <= 111) return 'bg-light-primary text-primary';
    if (code >= 112 && code <= 116) return 'bg-light-success text-success';
    if (code >= 117 && code <= 121) return 'bg-dark text-white';
    return 'bg-light-secondary text-dark';
}

function getRolePill(roleName) {
    var lower = (roleName || '').toLowerCase();
    if (lower.indexOf('admin') !== -1) return 'role-admin';
    if (lower.indexOf('lead') !== -1) return 'role-lead';
    return 'role-default';
}

function formatRelativeTime(dateStr) {
    if (!dateStr) return { text: 'Never logged in', dotClass: 'dot-gray' };
    var d = new Date(dateStr);
    if (isNaN(d.getTime())) return { text: 'Never logged in', dotClass: 'dot-gray' };
    var now = new Date();
    var diffSec = Math.floor((now - d) / 1000);
    var mins = Math.floor(diffSec / 60);
    var dotClass = mins <= 60 ? 'dot-green' : (mins <= 1440 ? 'dot-amber' : 'dot-gray');
    if (diffSec < 60) return { text: 'Just now', dotClass: dotClass };
    if (diffSec < 3600) return { text: Math.floor(diffSec / 60) + ' minutes ago', dotClass: dotClass };
    if (diffSec < 86400) return { text: Math.floor(diffSec / 3600) + ' hours ago', dotClass: dotClass };
    if (diffSec < 2592000) return { text: Math.floor(diffSec / 86400) + ' days ago', dotClass: dotClass };
    return { text: Math.floor(diffSec / 2592000) + ' months ago', dotClass: dotClass };
}

function formatJoinedDate(dateStr) {
    if (!dateStr) return 'Recently';
    var d = new Date(dateStr);
    if (isNaN(d.getTime())) return 'Recently';
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var day = String(d.getDate()).padStart(2, '0');
    var month = months[d.getMonth()];
    var year = d.getFullYear();
    return day + ' ' + month + ', ' + year;
}

function buildTeamMemberCardHtml(row) {
    var rawName = (row.name || 'User').trim();
    var strVal = (rawName[0] || 'U').toLowerCase();
    var avatarClass = getAvatarClass(rawName);

    var roleName = row.display_name || row.role_name || 'Team Member';
    var rolePill = getRolePill(roleName);
    var userDesignation = (row.designation && row.designation.trim()) ? row.designation.trim() : '';
    var designation = userDesignation ? userDesignation : roleName;

    var activeJobs = parseInt(row.pre_active_jobs, 10) || 0;
    var captainJobs = parseInt(row.pre_captain_jobs, 10) || 0;
    var trmTalents = parseInt(row.pre_trm_talents, 10) || 0;
    var totalOps = activeJobs + captainJobs + trmTalents;

    var channels = Array.isArray(row.channels) ? row.channels : [];
    var publicCount = channels.length;

    var isInvited = !row.password || row.status === 'invited';
    var isActive = (row.status === 'active');

    var cardAccent = '#cbd5e1';
    if (isActive) {
        cardAccent = (row.is_client_rep || row.is_agency_rep) ? '#009ef7' : '#10b981';
    } else if (isInvited) {
        cardAccent = '#f59e0b';
    }

    var relTime = formatRelativeTime(row.last_login);
    var joinedDate = formatJoinedDate(row.created_at);

    var phone = row.phone || '';
    if (!phone && row.mobile) {
        phone = (row.calling_code ? '+' + row.calling_code.replace(/^\+/, '') + ' ' : '') + row.mobile;
    }

    var isRanked = !isInvited && typeof row.pre_rank === 'number' && row.pre_rank > 0 && isActive;
    var rankDisplay = isRanked ? '#' + row.pre_rank : 'N/A';
    var rankMonthDisplay = (isRanked && row.pre_rank_month) ? '(' + row.pre_rank_month + ')' : '';
    var scoreVal = isRanked ? (parseInt(row.pre_score || row.score, 10) || 0) : 0;
    var medal = (isRanked && row.pre_rank === 1) ? '🥇' : ((isRanked && row.pre_rank === 2) ? '🥈' : ((isRanked && row.pre_rank === 3) ? '🥉' : ''));

    var userDataObj = {
        id: row.id,
        name: rawName,
        email: row.email || '',
        phone: phone,
        image: row.image || '',
        initial: strVal.toUpperCase(),
        avatar_class: avatarClass,
        role: roleName,
        designation: designation,
        status: (row.status || 'active').toLowerCase(),
        last_active: relTime.text,
        channels: channels,
        priorities: row.priorities || []
    };
    var userDataAttr = escapeHtml(JSON.stringify(userDataObj));

    var html = '<div class="tm-primary-card js-team-card" data-user-id="' + row.id + '" data-is-client-rep="' + (row.is_client_rep ? '1' : '0') + '" data-is-agency-rep="' + (row.is_agency_rep ? '1' : '0') + '" style="border-left: 3.5px solid ' + cardAccent + ';">';
    html += '<div class="tm-subcards-grid">';

    // Subcard 1: Member Identity
    html += '<div class="tm-subcard tm-subcard-identity">';
    html += '<div class="tm-subcard-head">';
    html += '<div class="tm-subcard-title-wrap">';
    html += '<svg class="tm-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
    html += '<span class="tm-subcard-title">Member Identity</span>';
    html += '</div>';
    if (parseInt(row.root_user, 10) === 1) {
        html += '<span class="tm-subcard-badge badge-owner" title="Organization Owner"><i class="fa-solid fa-crown text-warning me-1"></i>Owner</span>';
    } else {
        html += '<span class="tm-subcard-badge badge-member">• Member</span>';
    }
    html += '</div>';
    html += '<div class="tm-identity-body">';
    html += '<div class="tm-avatar ' + avatarClass + ' show-detail-team" data-row-id="' + row.id + '" role="button" tabindex="0" title="View ' + escapeHtml(rawName) + ' Profile" style="cursor: pointer;">';
    if (row.image) {
        html += '<img src="' + escapeHtml(row.image) + '" alt="' + escapeHtml(rawName) + '">';
    } else {
        html += strVal.toUpperCase();
    }
    html += '</div>';
    html += '<div class="tm-identity-details">';
    html += '<span class="tm-member-name show-detail-team" data-row-id="' + row.id + '" role="button" tabindex="0" title="View ' + escapeHtml(rawName) + ' Profile">' + escapeHtml(rawName.substring(0, 24)) + '</span>';
    html += '<div class="tm-member-designation" title="' + (userDesignation ? 'Designation: ' + escapeHtml(userDesignation) : 'System Role: ' + escapeHtml(roleName)) + '">';
    html += '<i class="fa-solid fa-id-badge text-muted"></i>';
    html += '<span>' + escapeHtml(designation.substring(0, 26)) + '</span>';
    html += '</div>';
    html += '<div class="tm-member-email-wrap">';
    html += '<i class="fa-regular fa-envelope text-muted"></i>';
    html += '<a href="mailto:' + escapeHtml(row.email || '') + '" class="tm-member-email-link" title="' + escapeHtml(row.email || '') + '">' + escapeHtml((row.email || '').substring(0, 26)) + '</a>';
    html += '</div>';
    html += '</div></div></div>';

    // Subcard 2: System Role
    html += '<div class="tm-subcard tm-subcard-role">';
    html += '<div class="tm-subcard-head">';
    html += '<div class="tm-subcard-title-wrap">';
    html += '<svg class="tm-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>';
    html += '<span class="tm-subcard-title">System Role</span>';
    html += '</div></div>';
    html += '<div class="tm-role-body">';
    html += '<div class="tm-role-badge-row">';
    html += '<span class="tm-role-badge team-role-pill ' + rolePill + '" title="Assigned system role"><i class="fa-solid fa-shield-halved me-1"></i>' + escapeHtml(roleName) + '</span>';
    html += '</div>';
    html += '<div class="tm-role-action-row">';
    if (parseInt(row.root_user, 10) !== 1) {
        html += '<button type="button" class="btn-change-role update-user-role" data-row-id="' + row.id + '" title="Change role and permissions directly from here"><i class="fa-solid fa-user-gear me-1 text-primary"></i>Change Role</button>';
    } else {
        html += '<span class="tm-role-locked-note" title="Primary owner role is authoritative and cannot be changed"><i class="fa-solid fa-lock text-muted"></i>Owner Role</span>';
    }
    html += '</div></div></div>';

    // Subcard 3: Workload
    html += '<div class="tm-subcard tm-subcard-workload">';
    html += '<div class="tm-subcard-head">';
    html += '<div class="tm-subcard-title-wrap">';
    html += '<svg class="tm-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>';
    html += '<span class="tm-subcard-title">Workload</span>';
    html += '</div>';
    html += '<span class="tm-subcard-badge badge-count">' + totalOps + ' Ops</span>';
    html += '</div>';
    html += '<div class="tm-workload-body">';
    html += '<div class="tm-wl-row" title="Active Jobs Assigned"><i class="fa-solid fa-briefcase text-primary me-1"></i><span><strong>' + activeJobs + '</strong> Active Jobs</span></div>';
    html += '<div class="tm-wl-row" title="SPOC / Captain on Jobs"><i class="fa-solid fa-crown text-purple me-1"></i><span><strong>' + captainJobs + '</strong> Captain Jobs</span></div>';
    html += '<div class="tm-wl-row" title="Bench Talents Managed (Bench Talent Representative)"><i class="fa-solid fa-users text-success me-1"></i><span title="Bench Talents Managed (Bench Talent Representative)"><strong>' + trmTalents + '</strong> Bench Talent Rep</span></div>';
    html += '</div></div>';

    // Subcard 4: Account Status
    html += '<div class="tm-subcard tm-subcard-status">';
    html += '<div class="tm-subcard-head">';
    html += '<div class="tm-subcard-title-wrap">';
    html += '<svg class="tm-subcard-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>';
    html += '<span class="tm-subcard-title">Account Status</span>';
    html += '</div></div>';
    html += '<div class="tm-status-body">';
    if (row.password) {
        html += '<div class="tm-status-row tm-status-row-control">';
        html += '<label class="ios-switch" title="Toggle active status">';
        html += '<input type="checkbox" class="chkStatus" id="status_change_' + row.id + '" value="active" ' + (isActive ? 'checked' : '') + ' onclick="handleClick(this, ' + row.id + ');" ' + (parseInt(row.root_user, 10) === 1 ? 'disabled' : '') + '>';
        html += '<span class="ios-slider"></span>';
        html += '</label></div>';
        html += '<div class="tm-status-row tm-status-row-label">';
        html += '<span class="tm-status-text status_text ' + (isActive ? 'text-success' : 'text-muted') + '">' + (isActive ? 'Active' : 'Inactive') + '</span>';
        html += '</div>';
        html += '<div class="tm-status-row tm-status-row-sub">';
        if (parseInt(row.root_user, 10) === 1) {
            html += '<span class="tm-status-subnote">Owner (Always Active)</span>';
        } else {
            html += '<span class="tm-status-subnote">Click switch to toggle</span>';
        }
        html += '</div>';
    } else {
        html += '<div class="tm-status-row tm-status-row-control">';
        html += '<button type="button" class="btn-resend-invite-icon" onclick="reSendhandleClick(this, ' + row.id + ');" title="Resend invitation email">';
        html += '<i class="fa-solid fa-paper-plane text-warning" style="font-size: 11px;"></i>';
        html += '</button></div>';
        html += '<div class="tm-status-row tm-status-row-label"><span class="tm-status-text text-warning">Awaiting Signup</span></div>';
        html += '<div class="tm-status-row tm-status-row-sub">';
        html += '<a href="javascript:;" class="btn-resend-invite" onclick="reSendhandleClick(this, ' + row.id + ');" title="Resend invitation email"><i class="fa-solid fa-arrows-rotate me-1"></i>Resend Invite</a>';
        html += '</div>';
    }
    html += '</div></div>';

    // Subcard 5: Contact Details Visibility
    html += '<div class="tm-subcard tm-subcard-comm" data-user=\'' + userDataAttr + '\' onclick="openAssignCommDrawer(this)" role="button" tabindex="0" title="Click anywhere to manage contact visibility">';
    html += '<div class="tm-subcard-head">';
    html += '<div class="tm-subcard-title-wrap">';
    html += '<i class="fa-solid fa-address-book text-primary" style="font-size: 11px;"></i>';
    html += '<span class="tm-subcard-title">Contact Details Visibility</span>';
    html += '</div>';
    html += '<div class="d-flex align-items-center gap-1 flex-shrink-0">';
    if (publicCount === 0) {
        html += '<span class="tm-subcard-badge badge-role" title="Contact details kept private"><i class="fa-solid fa-eye-slash me-1"></i>Private</span>';
    }
    html += '<button type="button" class="btn-comm-change-action" data-user=\'' + userDataAttr + '\' onclick="event.stopPropagation(); openAssignCommDrawer(this);" title="Change contact visibility preferences">Change</button>';
    html += '</div></div>';
    html += '<div class="tm-comm-body">';
    if (publicCount > 0) {
        html += '<div class="tm-comm-rows-list">';
        if (channels.indexOf('client') !== -1) {
            html += '<div class="tm-comm-row"><span class="tm-comm-chip client" title="Sales &amp; Client Enquiries"><i class="fa-solid fa-phone me-1"></i>Sales &amp; Client Enquiries</span></div>';
        }
        if (channels.indexOf('agency') !== -1 || channels.indexOf('vendor') !== -1) {
            html += '<div class="tm-comm-row"><span class="tm-comm-chip vendor" title="Agency Queries"><i class="fa-solid fa-phone me-1"></i>Agency Queries</span></div>';
        }
        if (channels.indexOf('bench') !== -1 || channels.indexOf('recruitment') !== -1) {
            html += '<div class="tm-comm-row"><span class="tm-comm-chip recruitment" title="Bench Talent Enquiries"><i class="fa-solid fa-phone me-1"></i>Bench Talent Enquiries</span></div>';
        }
        html += '</div>';
    } else {
        html += '<div class="d-flex align-items-center justify-content-between w-100 gap-1 flex-wrap">';
        html += '<span class="text-muted" style="font-size: 10.5px; font-weight: 500;"><i class="fa-solid fa-lock text-muted me-1"></i> No Details Shared (Keep Private)</span>';
        html += '<button type="button" class="btn-assign-comm-empty" data-user=\'' + userDataAttr + '\' onclick="event.stopPropagation(); openAssignCommDrawer(this);" title="Share contact details for marketplace enquiries"><i class="fa-solid fa-plus me-1 text-primary"></i>Share Details</button>';
        html += '</div>';
    }
    html += '</div></div>';

    html += '</div>'; // End tm-subcards-grid

    // Subcard 6: Bottom Activity Strip
    html += '<div class="tm-card-bottom-strip">';
    html += '<div class="tm-bottom-left-meta">';
    html += '<span class="tm-meta-item" title="Registration Date">';
    html += '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
    html += '<span>Joined: <strong>' + joinedDate + '</strong></span>';
    html += '</span>';
    html += '<span class="tm-meta-dot">•</span>';
    html += '<span class="tm-meta-item" title="Last Login Freshness">';
    html += '<span class="dot-pulse ' + relTime.dotClass + '"></span>';
    html += '<span>Last active: <strong>' + escapeHtml(relTime.text) + '</strong></span>';
    html += '</span>';
    if (phone) {
        html += '<span class="tm-meta-dot">•</span>';
        html += '<span class="tm-meta-item" title="Direct Phone">';
        html += '<i class="fa-solid fa-phone" style="font-size: 10px;"></i>';
        html += '<span>' + escapeHtml(phone) + '</span>';
        html += '</span>';
    }
    html += '</div>';

    html += '<div class="tm-bottom-right-group">';
    html += '<div class="tm-perf-strip-box">';
    html += '<span class="tm-perf-rank" title="Performance Rank">';
    html += medal + ' <strong>' + rankDisplay + '</strong>';
    if (rankMonthDisplay) {
        html += '<span class="tm-perf-month">' + escapeHtml(rankMonthDisplay) + '</span>';
    }
    html += '</span>';
    if (isRanked) {
        html += '<span class="tm-meta-dot">•</span>';
        html += '<span class="tm-perf-score" title="Score Points">Score: <strong>' + scoreVal + ' pts</strong></span>';
    }
    html += '<button type="button" class="btn-view-score teamViewScore" data-user-id="' + row.id + '" title="Click to view detailed performance scoreboard"><i class="fa-solid fa-chart-line me-1"></i>View Score</button>';
    html += '</div>';

    html += '<button type="button" class="btn-view-profile show-detail-team" data-row-id="' + row.id + '" title="View Full Profile"><span>View Profile</span><i class="fa-solid fa-arrow-right" style="font-size: 9px;"></i></button>';
    html += '</div>';

    html += '</div>'; // End tm-card-bottom-strip
    html += '</div>'; // End tm-primary-card

    return html;
}

// Test card builder on all 4 mock users
sampleUsers.forEach((u, i) => {
    const cardHtml = buildTeamMemberCardHtml(u);
    assert.ok(cardHtml.includes('tm-primary-card'), 'Card HTML must include tm-primary-card');
    assert.ok(cardHtml.includes(u.name.substring(0, 10)), 'Card HTML must include user name');
    console.log(`✅ Test Card ${i + 1} (${u.name}): generated cleanly (${cardHtml.length} chars)`);
});
