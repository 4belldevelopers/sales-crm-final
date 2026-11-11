@extends('layouts.service_company_layout')
@push('head-script')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<?php
    use App\Helper\GeneralMethod;

    $country_code = GeneralMethod::countryCodeFun();
    $phone_code   = $team->calling_code ? $team->calling_code : $country_code;

    // Enterprise default avatar SVG
    $default_avatar   = asset('default-enterprise-avatar.svg');
    $background_image = $default_avatar;
    $has_photo        = false;

    if (!empty(@$team->image) && strlen(@$team->image) > 10) {
        $background_image = @$team->image;
        $has_photo        = true;
    }

    /* Build location string once (used by city tagify input) */
    $return = '';
    if (!empty(@$city_name) && !empty(@$state_name) && !empty(@$country_name)) {
        $return = ucwords(@$city_name) . ' | ' . ucwords(@$state_name) . ' | ' . ucwords(@$country_name);
    }

    $isOrgAdmin = (auth()->check() && (
        (method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('admin')) ||
        (int)(auth()->user()->root_user ?? 0) === 1
    ));
?>

<!--begin::Content Container-->
<div class="content d-flex flex-column flex-column-fluid pt-2" id="kt_content">
  <div id="kt_content_container" class="container-fluid px-2 god-profile-shell">

    <!--begin::Form-->
    <form id="kt_account_profile_details_form" name="kt_account_profile_details_form" method="post" class="form" novalidate="novalidate" enctype="multipart/form-data">
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <input type="hidden" name="teamUserId" id="teamUserId" value="{{ $team->id }}" />
      {{-- Background preserved field to prevent database column nullification --}}
      <input type="hidden" name="whatsapp_number" id="whatsapp_number" value="{{ @$team->whatsapp_number }}" />
      @csrf
      @method('PUT')

      <!-- =========================================================================
           TOP UNIFIED HEADER CARD (God-Level Navigation Bar with Action Buttons)
           ========================================================================= -->
      <div class="card god-top-nav-card" id="god_top_nav_card">
        <div class="god-header-grid">
          
          <!-- LEFT: Title & Recruiter Badge -->
          <div class="god-header-identity">
            <div class="god-header-icon-box">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
            </div>
            <div class="god-header-titles">
              <h1 class="god-header-title">
                Edit Profile
                <span class="god-header-badge"><i class="bi bi-shield-check"></i> Recruiter Profile</span>
              </h1>
              <p class="god-header-subtitle">Manage your personal information, corporate designation, and primary contact coordinates.</p>
            </div>
          </div>

          <!-- RIGHT: Action Buttons (Cancel & Save Changes) -->
          <div class="god-header-actions">
            <button type="button" class="btn god-top-cancel-btn" onclick="window.location = '<?php echo url('service_company/profile');?>';">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
              <span>Cancel</span>
            </button>
            <button type="button" class="btn god-top-submit-btn" id="updateProfile">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <span>Save Changes</span>
            </button>
          </div>

        </div>
      </div>
      <!--end::Header-->

      <!-- =========================================================================
           2-COLUMN GOD-LEVEL LAYOUT (Matched 1:1 to Add Internal Client Page 1)
           Left (7.2fr): Personal Information | Right (3.8fr): Profile Photo
           ========================================================================= -->
      <div class="god-level-profile-layout">

        <!-- ========================== LEFT COLUMN: PERSONAL INFORMATION ========================== -->
        <div class="client-main-column">
          <section class="card god-card god-profile-info-card">
            
            <!-- Card Header: Blue icon + Title + Subtitle -->
            <div class="god-card-header">
              <div class="god-card-header-icon blue-sky-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="8.5" cy="7" r="4"/>
                  <line x1="20" y1="8" x2="20" y2="14"/>
                  <line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
              </div>
              <div class="god-card-header-titles">
                <h3 class="god-card-title">Personal Information</h3>
                <p class="god-card-subtitle">Update your personal details, corporate designation, and primary contact coordinates.</p>
              </div>
            </div>

            <!-- 2-Column Fields Grid -->
            <div class="god-fields-grid">

              <!-- ROW 1, COL 1: Full Name (Locked if non-admin) -->
              <div class="client-field fv-plugins-icon-container god-field-wrap">
                <div class="god-field-header">
                  <label class="god-field-label" for="name">Full Name <span style="color: #ef4444;">*</span></label>
                  @if(!$isOrgAdmin)
                    <span style="font-size: 10px; font-weight: 600; background: #fffbeb; color: #92400e; border: 1px solid #fde68a; border-radius: 999px; padding: 1px 7px; margin-left: auto;">
                      <i class="bi bi-lock-fill"></i> Admin Assigned
                    </span>
                  @endif
                </div>
                <div class="god-input-box {{ !empty($team->name) ? 'has-value' : '' }} {{ !$isOrgAdmin ? 'is-readonly' : '' }}">
                  <span class="god-input-left-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                      <circle cx="12" cy="7" r="4"/>
                    </svg>
                  </span>
                  @if(!$isOrgAdmin)
                    <input type="text" class="god-native-input" name="name" id="name" value="{{ $team->name }}" readonly style="background: transparent; color: #64748b; cursor: not-allowed;">
                  @else
                    <input type="text" class="god-native-input" name="name" id="name" value="{{ $team->name }}" placeholder="Enter Full name">
                    <button type="button" class="god-input-clear-btn" id="name_clear_btn" title="Clear name" aria-label="Clear name" style="{{ !empty($team->name) ? 'display:inline-flex;' : 'display:none;' }}">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                  @endif
                </div>
                @if(!$isOrgAdmin)
                  <div style="font-size: 10.5px; color: #64748b; margin-top: 3px; display: flex; align-items: center; gap: 4px;">
                    <i class="bi bi-info-circle"></i> Assigned by organization administrator and locked for governance.
                  </div>
                @endif
                <div class="fv-plugins-message-container invalid-feedback" style="font-size: 11px; color: #ef4444; margin-top: 3px;"></div>
              </div>

              <!-- ROW 1, COL 2: Designation -->
              <div class="client-field fv-plugins-icon-container god-field-wrap">
                <div class="god-field-header">
                  <label class="god-field-label" for="designation">Designation <span style="color: #ef4444;">*</span></label>
                </div>
                <div class="god-input-box {{ !empty($team->designation) ? 'has-value' : '' }}">
                  <span class="god-input-left-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                      <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                  </span>
                  <input type="text" class="god-native-input" name="designation" id="designation" value="{{ $team->designation }}" placeholder="e.g. Lead Talent Specialist / Director">
                  <button type="button" class="god-input-clear-btn" id="designation_clear_btn" title="Clear designation" aria-label="Clear designation" style="{{ !empty($team->designation) ? 'display:inline-flex;' : 'display:none;' }}">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
                </div>
                <div class="fv-plugins-message-container invalid-feedback" style="font-size: 11px; color: #ef4444; margin-top: 3px;"></div>
              </div>

              <!-- ROW 2, COL 1: Work Email Address -->
              <div class="client-field fv-plugins-icon-container god-field-wrap">
                <div class="god-field-header">
                  <label class="god-field-label" for="email">Work Email Address <span style="color: #ef4444;">*</span></label>
                </div>
                <div class="god-input-box is-readonly has-value">
                  <span class="god-input-left-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                      <polyline points="22,6 12,13 2,6"/>
                    </svg>
                  </span>
                  <input type="email" class="god-native-input" name="email" id="email" value="{{ $team->email }}" readonly style="background: transparent; color: #64748b; cursor: not-allowed;">
                  @if(!empty($team->email))
                    <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 10.5px; font-weight: 600; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; border-radius: 999px; padding: 1px 7px; margin-left: auto; flex-shrink: 0;">
                      <i class="bi bi-patch-check-fill"></i> Verified
                    </span>
                  @endif
                </div>
                <div class="fv-plugins-message-container invalid-feedback" style="font-size: 11px; color: #ef4444; margin-top: 3px;"></div>
              </div>

              <!-- ROW 2, COL 2: Phone Number -->
              <div class="client-field fv-plugins-icon-container god-field-wrap">
                <div class="god-field-header">
                  <label class="god-field-label" for="mobile">Phone Number <span style="color: #ef4444;">*</span></label>
                </div>
                <div class="god-phone-wrap" style="width: 100%;">
                  <x-jero-phone-input
                      id="mobile"
                      name="mobile"
                      countryCodeName="phone_country_code"
                      countryCodeId="phone_country_code"
                      :value="$team->mobile"
                      :countryCode="@$phone_code"
                      label=""
                      :required="true"
                      placeholder="Enter Phone" />
                </div>
                <div class="fv-plugins-message-container invalid-feedback" style="font-size: 11px; color: #ef4444; margin-top: 3px;"></div>
              </div>

              <!-- ROW 3 (Full Width): Work Location (City) -->
              <div class="client-field fv-plugins-icon-container god-field-wrap god-field-full">
                <div class="god-field-header">
                  <label class="god-field-label" for="kt_tagify_work_location">Work Location (City) <span style="color: #ef4444;">*</span></label>
                </div>
                <div class="god-tagify-wrap">
                  <input class="god-native-input" id="kt_tagify_work_location" name="kt_tagify_work_location" value="{{ $return }}" placeholder="Search your city..."/>
                </div>
                <div style="font-size: 10.5px; color: #64748b; margin-top: 3px; display: flex; align-items: center; gap: 4px;">
                  <i class="bi bi-search"></i> Instant city auto-suggestion enabled across all verified state and national registries.
                </div>
                <div class="fv-plugins-message-container invalid-feedback" style="font-size: 11px; color: #ef4444; margin-top: 3px;"></div>
              </div>

              <!-- ROW 4 (Full Width): LinkedIn Profile URL -->
              <div class="client-field fv-plugins-icon-container god-field-wrap god-field-full">
                <div class="god-field-header">
                  <label class="god-field-label" for="linkedin">LinkedIn Profile URL <span style="font-size: 10.5px; color: #94a3b8; font-weight: 400;">(Optional)</span></label>
                </div>
                <div class="god-input-box {{ !empty($team->linkedin) ? 'has-value' : '' }}">
                  <span class="god-input-left-icon" style="color: #0a66c2;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                    </svg>
                  </span>
                  <input type="url" class="god-native-input" name="linkedin" id="linkedin" value="{{ @$team->linkedin }}" placeholder="https://linkedin.com/in/yourprofile">
                  <button type="button" class="god-input-clear-btn" id="linkedin_clear_btn" title="Clear LinkedIn" aria-label="Clear LinkedIn" style="{{ !empty($team->linkedin) ? 'display:inline-flex;' : 'display:none;' }}">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
                </div>
                <div class="fv-plugins-message-container invalid-feedback" style="font-size: 11px; color: #ef4444; margin-top: 3px;"></div>
              </div>

            </div>
          </section>
        </div>

        <!-- ========================== RIGHT COLUMN: PROFILE PHOTO ========================== -->
        <div class="client-side-column god-logo-column">
          <section class="card god-card god-company-logo-card">
            
            <!-- Card Header: Blue image icon + Title + Subtitle -->
            <div class="god-card-header">
              <div class="god-card-header-icon blue-sky-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                  <circle cx="8.5" cy="8.5" r="1.5"/>
                  <polyline points="21 15 16 10 5 21"/>
                </svg>
              </div>
              <div class="god-card-header-titles">
                <h3 class="god-card-title">Profile Photo</h3>
                <p class="god-card-subtitle">Set or update your profile photo.</p>
              </div>
            </div>

            <!-- Hidden Native File & Avatar Remove Inputs -->
            <input type="file" name="avatar" id="avatar" accept=".png, .jpg, .jpeg, .webp" style="display:none;" />
            <input type="hidden" name="avatar_remove" id="avatar_remove" value="0" />

            <!-- Large Photo Preview Container (Dashed border rounded box) -->
            <div class="god-logo-preview-box" id="god_photo_preview_box" title="Click to browse or drop an image here" style="cursor: pointer;">
              <div class="god-logo-surface {{ $has_photo ? 'has-image' : '' }}"
                   id="god_avatar_surface"
                   style="<?php echo $has_photo ? 'background-image: url(' . $background_image . ');' : ''; ?>">
                @if(!$has_photo)
                  <div class="god-logo-placeholder" id="avatar_placeholder">
                    <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                      <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span class="placeholder-title">Profile Photo</span>
                    <span class="placeholder-sub">Click Change Photo or drop file</span>
                  </div>
                @endif
              </div>
            </div>

            <!-- Status Banner: matched to Add Internal -->
            <div class="god-logo-status-banner {{ $has_photo ? 'is-detected' : 'is-empty' }}" id="god_photo_status_banner">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" id="god_photo_status_svg">
                @if($has_photo)
                  <polyline points="20 6 9 17 4 12"/>
                @else
                  <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                @endif
              </svg>
              <span id="god_photo_status_text">{{ $has_photo ? 'Profile photo uploaded' : 'No photo uploaded yet' }}</span>
            </div>

            <!-- Action Buttons: Change Photo & Remove Photo -->
            <div class="god-logo-actions">
              <button type="button" class="btn god-logo-btn god-change-logo-btn" id="god_change_photo_btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                  <circle cx="12" cy="13" r="4"/>
                </svg>
                <span>Change Photo</span>
              </button>

              <button type="button" class="btn god-logo-btn god-remove-logo-btn" id="god_remove_photo_btn" style="{{ $has_photo ? 'display:inline-flex;' : 'display:none;' }}">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="3 6 5 6 21 6"/>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
                <span>Remove Photo</span>
              </button>
            </div>

            <!-- Supported formats helper note -->
            <div class="god-logo-formats-note">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
              </svg>
              <span>Supported formats: PNG, JPG, WEBP (Max 2MB).<br>Recommended: 400×400px. Used in ATS pipeline & candidate communication.</span>
            </div>

          </section>
        </div>

      </div>
      <!--end::2-Column Layout-->

    </form>
    <!--end::Form-->

  </div>
</div>
<!--end::Content Container-->

<style>
/* ==========================================================================
   GOD-LEVEL DESIGN SYSTEM (Identical Parity with Add Internal Client Page 1)
   ========================================================================== */
.god-profile-shell {
  max-width: 1360px !important;
  margin: 0 auto !important;
  padding: 6px 12px 32px !important;
  font-family: 'Poppins', sans-serif !important;
}
.god-profile-shell * {
  box-sizing: border-box !important;
  font-family: 'Poppins', sans-serif !important;
}

/* ==========================================================================
   TOP NAV CARD (Add Internal Top Navigation Standard)
   ========================================================================== */
.god-top-nav-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 12px !important;
  padding: 10px 18px !important;
  margin-bottom: 16px !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
}
.god-header-grid {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 16px !important;
}
.god-header-identity {
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
}
.god-header-icon-box {
  width: 34px !important;
  height: 34px !important;
  border-radius: 8px !important;
  background: #eff6ff !important;
  color: #2563eb !important;
  border: 1px solid #bfdbfe !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
}
.god-header-title {
  font-size: 14.5px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  margin: 0 !important;
  line-height: 1.25 !important;
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}
.god-header-subtitle {
  font-size: 11px !important;
  color: #64748b !important;
  margin: 1px 0 0 !important;
  line-height: 1.3 !important;
}
.god-header-badge {
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  font-size: 10.5px !important;
  font-weight: 600 !important;
  background: #eff6ff !important;
  color: #2563eb !important;
  padding: 1px 8px !important;
  border-radius: 999px !important;
  border: 1px solid #bfdbfe !important;
}
.god-header-actions {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  margin-left: auto !important;
}
.god-top-cancel-btn {
  background: #ffffff !important;
  color: #475569 !important;
  border: 1.5px solid #cbd5e1 !important;
  font-size: 11.5px !important;
  font-weight: 600 !important;
  border-radius: 8px !important;
  padding: 0 14px !important;
  height: 34px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 5px !important;
  cursor: pointer !important;
  transition: all 0.15s ease !important;
}
.god-top-cancel-btn:hover {
  background: #f1f5f9 !important;
  color: #0f172a !important;
  border-color: #94a3b8 !important;
}
.god-top-submit-btn {
  background: linear-gradient(180deg, #ffd428 0%, #f5c400 100%) !important;
  color: #10143b !important;
  border: 1px solid #d4a017 !important;
  font-size: 11.5px !important;
  font-weight: 700 !important;
  border-radius: 8px !important;
  padding: 0 16px !important;
  height: 34px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 6px !important;
  box-shadow: 0 1px 3px rgba(245, 196, 0, 0.25) !important;
  cursor: pointer !important;
  transition: all 0.15s ease !important;
  font-family: 'Poppins', sans-serif !important;
}
.god-top-submit-btn:hover {
  background: #f5c400 !important;
  box-shadow: 0 2px 6px rgba(245, 196, 0, 0.35) !important;
  transform: translateY(-1px) !important;
}
.god-top-submit-btn:disabled {
  opacity: 0.65 !important;
  cursor: not-allowed !important;
  transform: none !important;
}

/* ==========================================================================
   2-COLUMN LAYOUT (7.2fr Left / 3.8fr Right - Exactly from Add Internal)
   ========================================================================== */
.god-level-profile-layout {
  display: grid !important;
  grid-template-columns: minmax(0, 7.2fr) minmax(0, 3.8fr) !important;
  gap: 16px !important;
  align-items: stretch !important;
  width: 100% !important;
}
.god-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 12px !important;
  padding: 18px 20px !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
  box-sizing: border-box !important;
  height: 100% !important;
  display: flex !important;
  flex-direction: column !important;
  justify-content: space-between !important;
  width: 100% !important;
}

/* Card Header */
.god-card-header {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  margin-bottom: 14px !important;
}
.god-card-header-icon {
  width: 32px !important;
  height: 32px !important;
  border-radius: 8px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
}
.god-card-header-icon.blue-sky-icon {
  background: #eff6ff !important;
  color: #2563eb !important;
}
.god-card-header-titles {
  min-width: 0 !important;
}
.god-card-title {
  font-size: 14px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  margin: 0 0 1px 0 !important;
  line-height: 1.25 !important;
}
.god-card-subtitle {
  font-size: 11px !important;
  color: #64748b !important;
  margin: 0 !important;
  line-height: 1.3 !important;
  font-weight: 400 !important;
}

/* Form Fields Grid */
.god-fields-grid {
  display: grid !important;
  grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
  gap: 12px 14px !important;
  flex: 1 1 auto !important;
  align-content: space-between !important;
}
.god-field-full {
  grid-column: 1 / -1 !important;
  width: 100% !important;
}
.god-field-wrap {
  display: flex !important;
  flex-direction: column !important;
  min-width: 0 !important;
  margin-bottom: 0 !important;
}
.god-field-header {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  margin-bottom: 4px !important;
}
.god-field-label {
  font-size: 11.5px !important;
  font-weight: 600 !important;
  color: #0f172a !important;
  margin: 0 !important;
  line-height: 1.25 !important;
}

/* Input Boxes */
.god-input-box {
  display: flex !important;
  align-items: center !important;
  background: #ffffff !important;
  border: 1.5px solid #cbd5e1 !important;
  border-radius: 8px !important;
  padding: 0 10px !important;
  height: 38px !important;
  min-height: 38px !important;
  position: relative !important;
  transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
  box-sizing: border-box !important;
  width: 100% !important;
  gap: 4px !important;
}
.god-input-box:focus-within {
  border-color: #ffd428 !important;
  box-shadow: 0 0 0 3px rgba(255, 212, 40, 0.22) !important;
}
.god-input-box.is-invalid,
.god-input-box.has-validation-error {
  border-color: #ef4444 !important;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.18) !important;
}
.god-input-box.is-readonly {
  background: #f8fafc !important;
  border-color: #e2e8f0 !important;
}

.god-input-left-icon {
  color: #94a3b8 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin-right: 6px !important;
  flex-shrink: 0 !important;
}
.god-native-input {
  border: none !important;
  outline: none !important;
  background: transparent !important;
  color: #0f172a !important;
  font-size: 12px !important;
  font-weight: 500 !important;
  flex: 1 1 auto !important;
  min-width: 0 !important;
  height: 100% !important;
  padding: 0 !important;
  margin: 0 !important;
  box-shadow: none !important;
}
.god-native-input::placeholder {
  color: #94a3b8 !important;
  font-weight: 400 !important;
}

/* Clear Buttons */
.god-input-clear-btn {
  background: transparent !important;
  border: none !important;
  color: #94a3b8 !important;
  cursor: pointer !important;
  padding: 0 !important;
  width: 18px !important;
  height: 18px !important;
  display: none !important;
  align-items: center !important;
  justify-content: center !important;
  border-radius: 50% !important;
  transition: all 0.15s ease !important;
  flex-shrink: 0 !important;
  margin-right: 2px !important;
}
.god-input-clear-btn.is-visible,
.god-input-box.has-value .god-input-clear-btn {
  display: inline-flex !important;
}
.god-input-clear-btn:hover {
  background: #fee2e2 !important;
  color: #ef4444 !important;
}

/* Phone input wrapper */
.god-phone-wrap .iti {
  width: 100% !important;
}
.god-phone-wrap .iti input {
  height: 38px !important;
  border-radius: 8px !important;
  border: 1.5px solid #cbd5e1 !important;
  font-size: 12px !important;
  font-weight: 500 !important;
}
.god-phone-wrap .iti input:focus {
  border-color: #ffd428 !important;
  box-shadow: 0 0 0 3px rgba(255, 212, 40, 0.22) !important;
}

/* Tagify wrapper */
.god-tagify-wrap .tagify {
  border: 1.5px solid #cbd5e1 !important;
  border-radius: 8px !important;
  min-height: 38px !important;
  font-size: 12px !important;
  font-weight: 500 !important;
  --tag-bg: #eff6ff !important;
  --tag-text-color: #2563eb !important;
  padding-left: 6px !important;
}
.god-tagify-wrap .tagify:focus-within {
  border-color: #ffd428 !important;
  box-shadow: 0 0 0 3px rgba(255, 212, 40, 0.22) !important;
}

/* ==========================================================================
   RIGHT COLUMN: PROFILE PHOTO (Company Logo Exact Parity)
   ========================================================================== */
.god-logo-column {
  display: flex !important;
  flex-direction: column !important;
}
.god-logo-preview-box {
  border: 1.5px dashed #cbd5e1 !important;
  border-radius: 10px !important;
  background: #ffffff !important;
  padding: 10px !important;
  min-height: 145px !important;
  max-height: 145px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  position: relative !important;
  box-sizing: border-box !important;
  transition: all 0.2s ease !important;
}
.god-logo-preview-box:hover,
.god-logo-preview-box.is-drag {
  border-color: #2563eb !important;
  background: #f8fafc !important;
}
.god-logo-surface {
  width: 110px !important;
  height: 110px !important;
  border-radius: 10px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  background-size: cover !important;
  background-repeat: no-repeat !important;
  background-position: center !important;
  background-color: #f8fafc !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
  position: relative !important;
  overflow: hidden !important;
  transition: all 0.2s ease !important;
}
.god-logo-surface.has-image {
  background-color: #ffffff !important;
}

.god-logo-placeholder {
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 2px !important;
  color: #94a3b8 !important;
  text-align: center !important;
  padding: 8px !important;
}
.god-logo-placeholder .placeholder-title {
  font-size: 11px !important;
  font-weight: 600 !important;
  color: #64748b !important;
  margin-top: 2px !important;
}
.god-logo-placeholder .placeholder-sub {
  font-size: 9.5px !important;
  color: #94a3b8 !important;
}

/* Status Banner */
.god-logo-status-banner {
  width: 100% !important;
  border-radius: 6px !important;
  padding: 6px 10px !important;
  font-size: 11px !important;
  font-weight: 600 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 6px !important;
  margin-top: 8px !important;
  box-sizing: border-box !important;
  text-align: center !important;
}
.god-logo-status-banner.is-detected {
  background: #dcfce7 !important;
  color: #15803d !important;
}
.god-logo-status-banner.is-empty {
  background: #f1f5f9 !important;
  color: #64748b !important;
}

/* Actions */
.god-logo-actions {
  display: flex !important;
  flex-direction: row !important;
  gap: 8px !important;
  margin-top: 8px !important;
  width: 100% !important;
}
.god-logo-btn {
  flex: 1 1 auto !important;
  height: 34px !important;
  border-radius: 8px !important;
  font-size: 11.5px !important;
  font-weight: 600 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 6px !important;
  cursor: pointer !important;
  transition: all 0.15s ease !important;
  text-decoration: none !important;
}
.god-change-logo-btn {
  background: #ffffff !important;
  border: 1.5px solid #cbd5e1 !important;
  color: #0f172a !important;
}
.god-change-logo-btn:hover {
  background: #f8fafc !important;
  border-color: #94a3b8 !important;
}
.god-remove-logo-btn {
  background: #fff1f2 !important;
  border: 1.5px solid #fecdd3 !important;
  color: #ef4444 !important;
}
.god-remove-logo-btn:hover {
  background: #fee2e2 !important;
  border-color: #fda4af !important;
}

/* Formats Note */
.god-logo-formats-note {
  font-size: 10px !important;
  color: #64748b !important;
  line-height: 1.35 !important;
  text-align: center !important;
  margin-top: 8px !important;
  display: flex !important;
  align-items: flex-start !important;
  justify-content: center !important;
  gap: 4px !important;
}
.god-logo-formats-note svg {
  flex-shrink: 0 !important;
  margin-top: 1px !important;
}

/* Responsive adjustments */
@media (max-width: 991px) {
  .god-level-profile-layout {
    grid-template-columns: 1fr !important;
  }
  .god-header-grid {
    flex-wrap: wrap !important;
  }
}
@media (max-width: 640px) {
  .god-fields-grid {
    grid-template-columns: 1fr !important;
  }
  .god-header-actions {
    width: 100% !important;
    justify-content: flex-end !important;
  }
}
</style>
@endsection

@push('footer-script')
<script src="https://unpkg.com/@yaireo/tagify"></script>
<script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>

<script>
$(document).ready(function () {
    if (window.JeroPhoneBox) {
        window.JeroPhoneBox.initAll();
    }
});

/* ==========================================================================
   DYNAMIC CLEAR BUTTONS (Instant value clearing & state toggling)
   ========================================================================== */
(function() {
    ['name', 'designation', 'linkedin'].forEach(function(fieldId) {
        var input = document.getElementById(fieldId);
        var btn   = document.getElementById(fieldId + '_clear_btn');
        if (!input || !btn) return;
        var box   = input.closest('.god-input-box');

        function updateClearState() {
            var val = (input.value || '').trim();
            if (val.length > 0) {
                btn.style.display = 'inline-flex';
                if (box) box.classList.add('has-value');
            } else {
                btn.style.display = 'none';
                if (box) box.classList.remove('has-value');
            }
        }

        ['input', 'keyup', 'change'].forEach(function(ev) {
            input.addEventListener(ev, updateClearState);
        });

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            input.value = '';
            updateClearState();
            input.focus();
            $(input).trigger('input').trigger('change');
        });

        updateClearState();
    });
})();

/* ==========================================================================
   GOD-LEVEL PROFILE PHOTO HANDLERS (Browse, Dropzone, 2MB Guard, Remove)
   ========================================================================== */
(function() {
    var fileInput    = document.getElementById('avatar');
    var previewBox   = document.getElementById('god_photo_preview_box');
    var avatarSurface= document.getElementById('god_avatar_surface');
    var placeholder  = document.getElementById('avatar_placeholder');
    var statusBanner = document.getElementById('god_photo_status_banner');
    var statusSvg    = document.getElementById('god_photo_status_svg');
    var statusText   = document.getElementById('god_photo_status_text');
    var changeBtn    = document.getElementById('god_change_photo_btn');
    var removeBtn    = document.getElementById('god_remove_photo_btn');
    var removeInput  = document.getElementById('avatar_remove');
    var defaultAvatarUrl = "{{ $default_avatar }}";

    if (!fileInput) return;

    if (changeBtn) {
        changeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            fileInput.click();
        });
    }

    if (previewBox) {
        previewBox.addEventListener('click', function(e) {
            if (!e.target.closest('button')) {
                fileInput.click();
            }
        });
    }

    function processPhoto(file) {
        if (!file || !file.type.match('image.*')) return;

        // 2MB Client RAM Check (2,097,152 bytes)
        if (file.size > 2097152) {
            Swal.fire({
                icon: "warning",
                title: "File Size Exceeded",
                text: "The profile photo size must not exceed 2 MB.",
                customClass: { confirmButton: "btn btn-primary" }
            });
            fileInput.value = '';
            return false;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            avatarSurface.style.backgroundImage = "url('" + e.target.result + "')";
            avatarSurface.classList.add('has-image');
            if (placeholder) placeholder.style.display = 'none';

            if (removeInput) removeInput.value = '0';

            if (statusBanner) {
                statusBanner.className = 'god-logo-status-banner is-detected';
                statusSvg.innerHTML = '<polyline points="20 6 9 17 4 12"/>';
                statusText.textContent = 'Profile photo uploaded';
            }
            if (removeBtn) removeBtn.style.display = 'inline-flex';
        };
        reader.readAsDataURL(file);
    }

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            processPhoto(this.files[0]);
        }
    });

    if (removeBtn) {
        removeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            fileInput.value = '';
            if (removeInput) removeInput.value = '1';

            avatarSurface.style.backgroundImage = "none";
            avatarSurface.classList.remove('has-image');
            if (placeholder) {
                placeholder.style.display = 'flex';
            } else {
                avatarSurface.innerHTML = `
                  <div class="god-logo-placeholder" id="avatar_placeholder">
                    <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                      <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span class="placeholder-title">Profile Photo</span>
                    <span class="placeholder-sub">Click Change Photo or drop file</span>
                  </div>`;
            }

            if (statusBanner) {
                statusBanner.className = 'god-logo-status-banner is-empty';
                statusSvg.innerHTML = '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>';
                statusText.textContent = 'Photo marked for removal';
            }
            removeBtn.style.display = 'none';
        });
    }

    // Drag and Drop
    if (previewBox) {
        ['dragenter', 'dragover'].forEach(function(ev) {
            previewBox.addEventListener(ev, function(e) {
                e.preventDefault();
                e.stopPropagation();
                previewBox.classList.add('is-drag');
            });
        });
        ['dragleave', 'drop'].forEach(function(ev) {
            previewBox.addEventListener(ev, function(e) {
                e.preventDefault();
                e.stopPropagation();
                previewBox.classList.remove('is-drag');
            });
        });
        previewBox.addEventListener('drop', function(e) {
            var files = e.dataTransfer && e.dataTransfer.files;
            if (files && files.length) {
                fileInput.files = files;
                processPhoto(files[0]);
            }
        });
    }
})();

/* ==========================================================================
   TAGIFY CITY LOCATION AUTOSUGGEST & PRELOAD
   ========================================================================== */
setTimeout(() => {
    const cities = @json($cities ?? []);
    let inputId = `#kt_tagify_work_location`;
    let inputElement = document.querySelector(inputId);

    if (inputElement) {
        let tagifyInstance = new Tagify(inputElement, {
            whitelist: cities,
            enforceWhitelist: true,
            maxTags: 1,
            dropdown: {
                maxItems: 20,
                classname: "",
                enabled: 0,
                closeOnSelect: true
            }
        });

        tagifyInstance.on('input', function(e) {
            tagifyInstance.dropdown.show();
        });

        tagifyInstance.on('dropdown:select', function(e) {
            tagifyInstance.addTags([e.detail.data.value]);
        });

        function debounce(func, delay) {
            let timer;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        const fetchSuggestions = debounce(function(query) {
            $.ajax({
                url: "{{ url('/') }}" + "/city-job-suggestions?q=" + encodeURIComponent(query),
                method: 'GET',
                success: function(data) {
                    if (Array.isArray(data)) {
                        tagifyInstance.settings.whitelist = data;
                        tagifyInstance.dropdown.show.call(tagifyInstance);
                    }
                },
                error: function(err) {
                    console.error("Error fetching cities:", err);
                }
            });
        }, 250);

        tagifyInstance.on('input', function(e) {
            var query = e.detail.value.trim();
            if (query.length < 2) return;
            fetchSuggestions(query);
        });

        setTimeout(() => {
            var city = "{{ @$return }}";
            if (city != '') {
                var cityName = city.split(" | ")[0];
                fetchSuggestions(cityName);
            }
        }, 100);

        setTimeout(() => {
            var defaultLocation = {!! json_encode(@$return) !!};
            if (defaultLocation) {
                tagifyInstance.addTags([defaultLocation]);
                tagifyInstance.dropdown.hide();
            }
        }, 500);
    }
}, 300);

/* ==========================================================================
   FORM VALIDATION & AJAX SUBMISSION (Header Submit Button)
   ========================================================================== */
$(document).on('click', '#updateProfile', function(e) {
    e.preventDefault();

    var errors = [];
    function reqCheck(id, label) {
        var el = document.getElementById(id);
        var $wrap = el ? $(el).closest('.god-field-wrap') : null;
        var $box  = el ? $(el).closest('.god-input-box') : null;
        var val = el ? (el.value || '').trim() : '';
        if (val === '[]') val = '';

        if (!val) {
            errors.push(label);
            if ($box) $box.addClass('is-invalid');
            if ($wrap) {
                var fb = $wrap.find('.invalid-feedback')[0];
                if (fb) {
                    fb.textContent = label + ' is required';
                    fb.style.display = 'block';
                }
            }
        } else {
            if ($box) $box.removeClass('is-invalid');
            if ($wrap) {
                var fb = $wrap.find('.invalid-feedback')[0];
                if (fb) {
                    fb.textContent = '';
                    fb.style.display = 'none';
                }
            }
        }
    }

    reqCheck('name',                    'Full name');
    reqCheck('designation',             'Designation');
    reqCheck('mobile',                  'Phone number');
    reqCheck('kt_tagify_work_location', 'Location (city)');

    // Optional LinkedIn URL regex validation
    var linkedinEl = document.getElementById('linkedin');
    if (linkedinEl && linkedinEl.value.trim() !== '') {
        var lval = linkedinEl.value.trim();
        var $lwrap = $(linkedinEl).closest('.god-field-wrap');
        var $lbox  = $(linkedinEl).closest('.god-input-box');
        if (!/^https?:\/\/.+\..+/i.test(lval)) {
            errors.push('LinkedIn URL');
            if ($lbox) $lbox.addClass('is-invalid');
            if ($lwrap) {
                var lfb = $lwrap.find('.invalid-feedback')[0];
                if (lfb) {
                    lfb.textContent = 'Please enter a valid URL (https://...)';
                    lfb.style.display = 'block';
                }
            }
        } else {
            if ($lbox) $lbox.removeClass('is-invalid');
            if ($lwrap) {
                var lfb = $lwrap.find('.invalid-feedback')[0];
                if (lfb) {
                    lfb.textContent = '';
                    lfb.style.display = 'none';
                }
            }
        }
    }

    if (errors.length > 0) {
        var $first = $('.is-invalid').first();
        if ($first.length) {
            $first[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return false;
    }

    // Check file size if a file is selected
    const fileInput = document.getElementById('avatar');
    if (fileInput && fileInput.files[0]) {
        if (fileInput.files[0].size > 2097152) {
            Swal.fire({
                icon: "warning",
                title: "File Size Exceeded",
                text: "The profile photo size must not exceed 2 MB."
            });
            return false;
        }
    }

    var $btn = $('#updateProfile');
    $btn.prop('disabled', true);
    $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...');

    var data = new FormData();
    var form_data = $('#kt_account_profile_details_form').serializeArray();
    $.each(form_data, function (key, input) {
        data.append(input.name, input.value);
    });

    if (fileInput && fileInput.files.length > 0) {
        data.append("logo", fileInput.files[0]);
    }

    $.ajax({
        url: "{{ route('service_company.profile.update', @$team->id) }}",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        method: "POST",
        processData: false,
        contentType: false,
        data: data,
        success: function (response) {
            $btn.prop('disabled', false);
            $btn.html('<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" class="me-1"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Save Changes</span>');

            if (response.status == 'success') {
                Swal.fire({
                    icon: "success",
                    title: "Profile Updated",
                    text: "Your profile details have been successfully updated!",
                    timer: 1800,
                    showConfirmButton: false
                }).then(function() {
                    if (response.team_page) {
                        window.location = '{{ route('service_company.team.index') }}';
                    } else {
                        window.location = '{{ route('service_company.profile.index') }}';
                    }
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Update Failed",
                    text: response.message || "Failed to update profile. Please try again."
                });
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false);
            $btn.html('<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" class="me-1"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Save Changes</span>');
            var msg = "An unexpected error occurred while saving profile.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: "error",
                title: "Error",
                text: msg
            });
        }
    });
});
</script>
@endpush
