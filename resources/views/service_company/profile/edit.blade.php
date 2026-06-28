@extends('layouts.service_company_layout')
@push('head-script')
@endpush
@section('content')
<?php

	 use App\Helper\GeneralMethod;
   $country_code =  GeneralMethod::countryCodeFun();
   $phone_code = $team->calling_code ? $team->calling_code : $country_code;

	 $background_image		=	url('public/user-uploads/team_profile_img/avatar.png');
	 $has_photo = false;
	 if(!empty(@$team->image) && strlen(@$team->image)>10){
   			$background_image		=  @$team->image;
   			$has_photo = true;
   	}

   /* Build location string once (used by header, preview and the city input) */
   $return = '';
   if(!empty(@$city_name) && !empty(@$state_name) && !empty(@$country_name)){
      $return = ucwords(@$city_name) . ' | ' . ucwords(@$state_name) . ' | ' . ucwords(@$country_name);
   }

   /* Profile completion + "what's missing" computed in PHP — no extra queries / server load */
   $checks = [
      'photo'       => $has_photo,
      'name'        => !empty($team->name),
      'designation' => !empty($team->designation),
      'email'       => !empty($team->email),
      'phone'       => !empty($team->mobile),
      'location'    => !empty(trim(@$return)),
      'linkedin'    => !empty(@$team->linkedin),
   ];
   $filled       = count(array_filter($checks));
   $total        = count($checks);
   $completion   = $total ? (int) round(($filled / $total) * 100) : 0;

   $location_text = !empty(trim(@$return)) ? trim(@$return) : 'Location not set';
?>
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-2" id="kt_content">
  <div id="kt_content_container" class="container-fluid px-2 jp-wrap">

    <!--begin::Header-->
    <div class="jp-page-head">
      <div class="jp-page-head__left">
        <h1 class="jp-title">Edit Profile</h1>
        <p class="jp-subtitle">Manage your personal and company information</p>
      </div>
      <div class="jp-page-head__right">
        <div class="jp-completion">
          <div class="jp-completion__top">
            <span class="jp-completion__label">Profile Completion</span>
            <a href="javascript:void(0);" class="jp-completion__link">View Suggestions <i class="bi bi-chevron-right"></i></a>
          </div>
          <div class="jp-completion__row">
            <span class="jp-completion__pct">{{ $completion }}%</span>
            <div class="jp-progress"><span style="width: {{ $completion }}%"></span></div>
          </div>
        </div>
      </div>
    </div>
    <!--end::Header-->

    <!--begin::Form-->
    <form id="kt_account_profile_details_form" name="kt_account_profile_details_form" method="post" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" enctype="multipart/form-data">
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <input type="hidden" name="teamUserId" id="teamUserId" value="{{$team->id}}" />
      @csrf
      @method('PUT')

      <div class="jp-grid">

        <!--begin::Main column-->
        <div class="jp-main">

          <!--begin::Profile Photo-->
          <section class="jp-card">
            <div class="jp-photo">
              <div class="jp-photo__info">
                <h3 class="jp-card__title">Profile Photo</h3>
                <p class="jp-card__hint">This will be displayed on your profile and company page.</p>
              </div>
              <div class="jp-avatar">
                <span class="jp-avatar__img" id="jp_avatar_preview" style="background-image:url('{{ $background_image }}')"></span>
                <label class="jp-avatar__edit" for="avatar" title="Change photo"><i class="bi bi-camera-fill"></i></label>
              </div>
              <label class="jp-dropzone" id="jp_dropzone">
                <i class="bi bi-cloud-arrow-up jp-dropzone__icon"></i>
                <span class="jp-dropzone__text">Drag &amp; drop your photo here <span class="jp-link">or click to browse</span></span>
                <span class="jp-dropzone__sub">PNG, JPG or JPEG (Max. 5MB)</span>
                <input type="file" name="avatar" id="avatar" accept=".png, .jpg, .jpeg" hidden>
                <input type="hidden" name="avatar_remove">
              </label>
            </div>
          </section>
          <!--end::Profile Photo-->

          <!--begin::Personal Information-->
          <section class="jp-card">
            <div class="jp-card__head">
              <span class="jp-card__ico"><i class="bi bi-person"></i></span>
              <h3 class="jp-card__title">Personal Information</h3>
            </div>

            <div class="jp-form-grid">
              <div class="jp-field">
                <label class="jp-label">Full Name <span class="jp-req">*</span></label>
                <input type="text" name="name" id="name" class="jp-input" placeholder="Enter Full name" value="{{$team->name}}">
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>

              <div class="jp-field">
                <label class="jp-label">Designation <span class="jp-req">*</span></label>
                <input type="text" name="designation" id="designation" class="jp-input" placeholder="Enter Designation" value="{{$team->designation}}">
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>

              <div class="jp-field">
                <label class="jp-label">Email Address <span class="jp-req">*</span></label>
                <div class="jp-input-wrap">
                  <input type="email" name="email" id="email" class="jp-input jp-input--readonly" placeholder="Enter Email" value="{{$team->email}}" readonly>
                  @if(!empty($team->email))
                  <span class="jp-badge jp-badge--verified"><i class="bi bi-check-circle-fill"></i> Verified</span>
                  @endif
                </div>
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>

              <div class="jp-field">
                <label class="jp-label">Phone Number <span class="jp-req">*</span></label>
                <div class="jp-input-wrap jp-phone">
                  <input type="number" name="mobile" id="mobile" maxlength="10" class="jp-input" placeholder="Enter Phone" value="{{$team->mobile}}">
                  <input type="hidden" value="{{ @$phone_code }}" id="phone_country_code" name="phone_country_code">
                  <span class="jp-wa"><i class="bi bi-whatsapp"></i></span>
                </div>
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
          </section>
          <!--end::Personal Information-->

          <!--begin::Location-->
          <section class="jp-card">
            <div class="jp-card__head">
              <span class="jp-card__ico"><i class="bi bi-geo-alt"></i></span>
              <h3 class="jp-card__title">Location</h3>
            </div>
            <div class="jp-field">
              <label class="jp-label">City <span class="jp-req">*</span></label>
              <input class="jp-input" id="kt_tagify_work_location" name="kt_tagify_work_location" value="{{ $return }}" placeholder="Search your city..."/>
              <div class="fv-plugins-message-container invalid-feedback"></div>
            </div>
          </section>
          <!--end::Location-->

          <!--begin::Social Links-->
          <section class="jp-card">
            <div class="jp-card__head">
              <span class="jp-card__ico"><i class="bi bi-link-45deg"></i></span>
              <h3 class="jp-card__title">Social Links <span class="jp-card__optional">(Optional)</span></h3>
            </div>

            <div class="jp-form-grid">
              <div class="jp-field">
                <label class="jp-label">LinkedIn Profile</label>
                <input type="url" name="linkedin" id="linkedin" class="jp-input" placeholder="https://linkedin.com/in/yourprofile" value="{{ @$team->linkedin }}">
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>

              <div class="jp-field">
                <label class="jp-label">WhatsApp Number</label>
                <div class="jp-input-wrap jp-phone">
                  <input type="number" name="whatsapp_number" id="whatsapp_number" class="jp-input" placeholder="Enter WhatsApp number" value="{{ @$team->whatsapp_number }}">
                  <span class="jp-wa"><i class="bi bi-whatsapp"></i></span>
                </div>
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
          </section>
          <!--end::Social Links-->

        </div>
        <!--end::Main column-->

        <!--begin::Side column-->
        <aside class="jp-side">

          <!--begin::Preview-->
          <section class="jp-card jp-preview">
            <div class="jp-card__head jp-card__head--sm">
              <span class="jp-card__ico"><i class="bi bi-eye"></i></span>
              <h3 class="jp-card__title">Profile Preview</h3>
            </div>
            <div class="jp-preview__body">
              <span class="jp-preview__avatar" style="background-image:url('{{ $background_image }}')"></span>
              <div class="jp-preview__name">
                {{ $team->name ?: 'Your Name' }}
                @if(!empty($team->email))<i class="bi bi-patch-check-fill jp-preview__verify"></i>@endif
              </div>
              <div class="jp-preview__role">{{ $team->designation ?: 'Your Designation' }}</div>
              <div class="jp-preview__loc"><i class="bi bi-geo-alt"></i> {{ $location_text }}</div>
              <span class="jp-tag jp-tag--green"><i class="bi bi-check-circle-fill"></i> Verified Employer</span>
            </div>
          </section>
          <!--end::Preview-->

          <!--begin::Missing-->
          <section class="jp-card jp-missing">
            <div class="jp-missing__head">
              <span class="jp-missing__ico"><i class="bi bi-pie-chart"></i></span>
              <div>
                <h3 class="jp-card__title">What's Missing?</h3>
                <p class="jp-card__hint">Complete these to boost your profile</p>
              </div>
            </div>
            <ul class="jp-missing__list">
              @unless($checks['photo'])
              <li><span class="jp-missing__item"><i class="bi bi-person"></i> Profile Photo</span><a href="javascript:void(0);" class="jp-btn-ghost" onclick="document.getElementById('avatar').click();">Add now</a></li>
              @endunless
              @unless($checks['linkedin'])
              <li><span class="jp-missing__item"><i class="bi bi-linkedin"></i> LinkedIn Profile</span><a href="javascript:void(0);" class="jp-btn-ghost" onclick="document.getElementById('linkedin').focus();">Add now</a></li>
              @endunless
              <li><span class="jp-missing__item"><i class="bi bi-whatsapp"></i> WhatsApp Automation</span><a href="javascript:void(0);" class="jp-btn-ghost" onclick="document.getElementById('whatsapp_number').focus();">Add now</a></li>
            </ul>
          </section>
          <!--end::Missing-->

          <!--begin::Tips-->
          <section class="jp-card jp-tips">
            <div class="jp-tips__head">
              <span class="jp-tips__ico"><i class="bi bi-lightbulb"></i></span>
              <h3 class="jp-card__title">Tips</h3>
            </div>
            <ul class="jp-tips__list">
              <li><i class="bi bi-check2"></i> A complete profile gets 3x more visibility</li>
              <li><i class="bi bi-check2"></i> Add your profile photo for trust</li>
              <li><i class="bi bi-check2"></i> Add LinkedIn to build credibility</li>
              <li><i class="bi bi-check2"></i> Enable WhatsApp automation to engage faster</li>
            </ul>
          </section>
          <!--end::Tips-->

        </aside>
        <!--end::Side column-->

      </div>

      <!--begin::Sticky footer-->
      <div class="jp-footbar">
        <div class="jp-footbar__status">
          <span class="jp-footbar__title">You have unsaved changes</span>
          <span class="jp-footbar__sub">Last saved a few seconds ago</span>
        </div>
        <div class="jp-footbar__actions">
          <button type="button" class="jp-btn jp-btn--light" onclick="window.location = '<?php echo url('service_company/profile');?>';">Cancel</button>
          <button type="button" class="jp-btn jp-btn--outline" id="saveDraft" name="saveDraft">Save Draft</button>
          <button type="submit" class="jp-btn jp-btn--primary" id="updateProfile" name="updateProfile">Save Changes</button>
        </div>
      </div>
      <!--end::Sticky footer-->

      <input type="hidden">
    </form>
    <!--end::Form-->

  </div>
</div>
<!--end::Content-->

<style>
.jp-wrap{font-family:'Poppins',sans-serif;color:#1f2a37;max-width:1320px;margin:0 auto;padding-bottom:84px;}
.jp-wrap *{font-family:'Poppins',sans-serif;}

/* Header */
.jp-page-head{display:flex;justify-content:space-between;align-items:flex-start;gap:24px;flex-wrap:wrap;margin-bottom:16px;}
.jp-title{font-size:14px;font-weight:700;color:#1f2a37;margin:0 0 2px;}
.jp-subtitle{font-size:11.5px;color:#8a94a6;margin:0;}
.jp-completion{min-width:280px;}
.jp-completion__top{display:flex;justify-content:space-between;align-items:center;gap:24px;margin-bottom:6px;}
.jp-completion__label{font-size:11.5px;color:#8a94a6;}
.jp-completion__link{font-size:11.5px;font-weight:600;color:#2b63f6;text-decoration:none;white-space:nowrap;}
.jp-completion__link i{font-size:9px;}
.jp-completion__row{display:flex;align-items:center;gap:10px;}
.jp-completion__pct{font-size:14px;font-weight:700;color:#1f2a37;}
.jp-progress{flex:1;height:6px;border-radius:6px;background:#e9edf5;overflow:hidden;}
.jp-progress>span{display:block;height:100%;border-radius:6px;background:linear-gradient(90deg,#2b63f6,#5b8def);}

/* Layout grid */
.jp-grid{display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:18px;align-items:start;}
.jp-main{display:flex;flex-direction:column;gap:16px;min-width:0;}
.jp-side{display:flex;flex-direction:column;gap:16px;position:sticky;top:14px;}

/* Card */
.jp-card{background:#fff;border:1px solid #eef0f5;border-radius:12px;padding:18px 20px;box-shadow:0 1px 2px rgba(20,30,60,.03);}
.jp-card__head{display:flex;align-items:center;gap:10px;margin-bottom:16px;}
.jp-card__head--sm{margin-bottom:14px;}
.jp-card__ico{width:30px;height:30px;border-radius:8px;background:#eef3ff;color:#2b63f6;display:flex;align-items:center;justify-content:center;font-size:14px;flex:none;}
.jp-card__title{font-size:13px;font-weight:600;color:#1f2a37;margin:0;}
.jp-card__optional{font-size:11px;font-weight:400;color:#9aa3b2;}
.jp-card__hint{font-size:11px;color:#9aa3b2;margin:2px 0 0;}

/* Photo */
.jp-photo{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:18px;}
.jp-photo__info{min-width:0;}
.jp-avatar{position:relative;width:88px;height:88px;justify-self:center;}
.jp-avatar__img{display:block;width:88px;height:88px;border-radius:50%;background-size:cover;background-position:center;border:3px solid #eef0f5;}
.jp-avatar__edit{position:absolute;right:-2px;bottom:2px;width:26px;height:26px;border-radius:50%;background:#2b63f6;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer;border:2px solid #fff;}
.jp-dropzone{border:1.5px dashed #d7deea;border-radius:10px;padding:14px;text-align:center;display:flex;flex-direction:column;align-items:center;gap:3px;cursor:pointer;transition:.15s;background:#fafbfe;}
.jp-dropzone.is-drag,.jp-dropzone:hover{border-color:#2b63f6;background:#f4f8ff;}
.jp-dropzone__icon{font-size:20px;color:#9fb0d0;}
.jp-dropzone__text{font-size:11.5px;color:#6b7585;}
.jp-dropzone__sub{font-size:10px;color:#aab2c2;}
.jp-link{color:#2b63f6;font-weight:600;}

/* Form */
.jp-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px 18px;}
.jp-field{display:flex;flex-direction:column;min-width:0;}
.jp-label{font-size:11.5px;font-weight:500;color:#5a6473;margin-bottom:6px;}
.jp-req{color:#f1416c;}
.jp-input{width:100%;height:40px;border:1px solid #e6e9f0;border-radius:8px;padding:0 12px;font-size:13px;color:#1f2a37;background:#fff;outline:none;transition:.15s;}
.jp-input::placeholder{color:#aab2c2;}
.jp-input:focus{border-color:#2b63f6;box-shadow:0 0 0 3px rgba(43,99,246,.10);}
.jp-input--readonly{background:#f6f8fc;color:#6b7585;}
/* Validation states */
.jp-field .invalid-feedback{display:none;font-size:10.5px;color:#f1416c;margin-top:5px;}
.jp-field.has-error .invalid-feedback{display:block;}
.jp-field.has-error .jp-input{border-color:#f1416c;}
.jp-field.has-error .jp-input:focus{box-shadow:0 0 0 3px rgba(241,65,108,.10);}
.jp-field.has-error .tagify{border-color:#f1416c !important;}
.jp-field.is-valid .jp-input{border-color:#1fa463;}
.jp-input-wrap{position:relative;}
.jp-input-wrap .jp-input{padding-right:96px;}
.jp-badge{position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:10.5px;font-weight:600;display:inline-flex;align-items:center;gap:4px;}
.jp-badge--verified{color:#1fa463;}
.jp-phone .jp-input{padding-right:42px;}
.jp-wa{position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#1fa463;font-size:16px;}

/* intl-tel-input fit */
.jp-phone .iti{width:100%;}
.jp-phone .iti__flag-container + input,.jp-phone .iti input{height:40px;}

/* Preview */
.jp-preview__body{display:flex;flex-direction:column;align-items:center;text-align:center;background:#f7f9fd;border-radius:10px;padding:18px 14px;}
.jp-preview__avatar{width:64px;height:64px;border-radius:50%;background-size:cover;background-position:center;border:3px solid #fff;box-shadow:0 2px 6px rgba(20,30,60,.10);margin-bottom:10px;}
.jp-preview__name{font-size:13px;font-weight:700;color:#1f2a37;display:flex;align-items:center;gap:5px;}
.jp-preview__verify{color:#2b63f6;font-size:12px;}
.jp-preview__role{font-size:11.5px;color:#6b7585;margin-top:2px;}
.jp-preview__loc{font-size:11px;color:#8a94a6;margin:6px 0 10px;}
.jp-tag{font-size:10.5px;font-weight:600;display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;}
.jp-tag--green{background:#e7f7ee;color:#1fa463;}

/* Missing */
.jp-missing__head{display:flex;gap:10px;align-items:flex-start;margin-bottom:12px;}
.jp-missing__ico{width:30px;height:30px;border-radius:8px;background:#fff4e6;color:#f6a609;display:flex;align-items:center;justify-content:center;font-size:14px;flex:none;}
.jp-missing__list{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:10px;}
.jp-missing__list li{display:flex;align-items:center;justify-content:space-between;gap:10px;}
.jp-missing__item{font-size:11.5px;color:#5a6473;display:flex;align-items:center;gap:8px;}
.jp-missing__item i{color:#8a94a6;font-size:13px;}
.jp-btn-ghost{font-size:10.5px;font-weight:600;color:#2b63f6;text-decoration:none;border:1px solid #d7e0f7;border-radius:6px;padding:4px 10px;background:#fff;}
.jp-btn-ghost:hover{background:#f4f8ff;}

/* Tips */
.jp-tips__head{display:flex;align-items:center;gap:10px;margin-bottom:12px;}
.jp-tips__ico{width:30px;height:30px;border-radius:8px;background:#f3eeff;color:#8b5cf6;display:flex;align-items:center;justify-content:center;font-size:14px;flex:none;}
.jp-tips__list{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:9px;}
.jp-tips__list li{font-size:11px;color:#6b7585;display:flex;align-items:flex-start;gap:8px;line-height:1.4;}
.jp-tips__list i{color:#1fa463;font-size:12px;margin-top:1px;}

/* Footer bar */
.jp-footbar{position:sticky;bottom:0;left:0;right:0;margin-top:18px;background:#fff;border:1px solid #eef0f5;border-radius:12px;padding:12px 18px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;box-shadow:0 -2px 10px rgba(20,30,60,.05);z-index:20;}
.jp-footbar__status{display:flex;flex-direction:column;}
.jp-footbar__title{font-size:12px;font-weight:600;color:#1f2a37;}
.jp-footbar__sub{font-size:10.5px;color:#9aa3b2;}
.jp-footbar__actions{display:flex;gap:10px;}
.jp-btn{height:38px;padding:0 18px;border-radius:8px;font-size:12.5px;font-weight:600;cursor:pointer;border:1px solid transparent;display:inline-flex;align-items:center;justify-content:center;transition:.15s;}
.jp-btn--light{background:#f1f3f8;color:#5a6473;}
.jp-btn--light:hover{background:#e7eaf2;}
.jp-btn--outline{background:#fff;border-color:#d7e0f7;color:#2b63f6;}
.jp-btn--outline:hover{background:#f4f8ff;}
.jp-btn--primary{background:#2b63f6;color:#fff;}
.jp-btn--primary:hover{background:#1f53e0;}
.jp-btn--primary:disabled{opacity:.6;cursor:not-allowed;}

/* Tagify fit */
.jp-wrap .tagify{border:1px solid #e6e9f0;border-radius:8px;min-height:40px;--tag-bg:#eef3ff;--tag-text-color:#2b63f6;font-size:13px;}
.jp-wrap .tagify:focus-within{border-color:#2b63f6;box-shadow:0 0 0 3px rgba(43,99,246,.10);}

/* Responsive — fits 15.6" laptops and scales down */
@media (max-width:1100px){
  .jp-grid{grid-template-columns:1fr;}
  .jp-side{position:static;}
}
@media (max-width:680px){
  .jp-form-grid{grid-template-columns:1fr;}
  .jp-photo{grid-template-columns:1fr;text-align:center;}
  .jp-avatar{justify-self:center;}
  .jp-page-head{flex-direction:column;}
  .jp-completion{width:100%;}
  .jp-footbar{flex-direction:column;align-items:stretch;}
  .jp-footbar__actions{justify-content:flex-end;}
}
</style>

<script>
/* Lightweight avatar preview + drag&drop (no server load) */
(function(){
  var input = document.getElementById('avatar');
  var dz    = document.getElementById('jp_dropzone');
  var prev  = document.getElementById('jp_avatar_preview');
  var sidePrev = document.querySelector('.jp-preview__avatar');
  if(!input) return;

  function render(file){
    if(!file || !file.type.match('image.*')) return;
    var reader = new FileReader();
    reader.onload = function(e){
      var url = "url('" + e.target.result + "')";
      if(prev) prev.style.backgroundImage = url;
      if(sidePrev) sidePrev.style.backgroundImage = url;
    };
    reader.readAsDataURL(file);
  }
  input.addEventListener('change', function(){ if(this.files && this.files[0]) render(this.files[0]); });

  ['dragenter','dragover'].forEach(function(ev){
    dz.addEventListener(ev, function(e){ e.preventDefault(); e.stopPropagation(); dz.classList.add('is-drag'); });
  });
  ['dragleave','drop'].forEach(function(ev){
    dz.addEventListener(ev, function(e){ e.preventDefault(); e.stopPropagation(); dz.classList.remove('is-drag'); });
  });
  dz.addEventListener('drop', function(e){
    var files = e.dataTransfer && e.dataTransfer.files;
    if(files && files.length){ input.files = files; render(files[0]); }
  });
})();
</script>

<script>
/* Client-side validation (real-time + on submit) — no server load */
(function(){
  // Field rules. Each validator returns an error string, or '' when valid.
  var rules = {
    name: function(v){ return v.trim() === '' ? 'Full name is required' : ''; },
    designation: function(v){ return v.trim() === '' ? 'Designation is required' : ''; },
    mobile: function(v){
      v = v.trim();
      if(v === '') return 'Phone number is required';
      if(!/^[0-9]+$/.test(v)) return 'Phone number can only contain digits';
      if(v.length !== 10) return 'Phone number must be 10 digits long';
      return '';
    },
    kt_tagify_work_location: function(v){ return v.trim() === '' ? 'Please select your city' : ''; },
    linkedin: function(v){ // optional
      v = v.trim();
      if(v === '') return '';
      return /^https?:\/\/.+\..+/i.test(v) ? '' : 'Enter a valid URL (https://...)';
    },
    whatsapp_number: function(v){ // optional
      v = v.trim();
      if(v === '') return '';
      if(!/^[0-9]+$/.test(v)) return 'WhatsApp number can only contain digits';
      if(v.length !== 10) return 'WhatsApp number must be 10 digits long';
      return '';
    }
  };

  function field(el){ return el ? el.closest('.jp-field') : null; }

  function showError(el, msg){
    var f = field(el); if(!f) return;
    var box = f.querySelector('.invalid-feedback');
    if(msg){
      f.classList.add('has-error'); f.classList.remove('is-valid');
      if(box) box.textContent = msg;
    } else {
      f.classList.remove('has-error');
      if(el.value && el.value.trim() !== '') f.classList.add('is-valid');
      if(box) box.textContent = '';
    }
  }

  function validateOne(id){
    var el = document.getElementById(id);
    if(!el || !rules[id]) return true;
    var msg = rules[id](el.value || '');
    showError(el, msg);
    return msg === '';
  }

  // Real-time validation on input / blur
  Object.keys(rules).forEach(function(id){
    var el = document.getElementById(id);
    if(!el) return;
    var ev = (id === 'mobile') ? 'input change' : 'input blur';
    ev.split(' ').forEach(function(e){
      el.addEventListener(e, function(){ validateOne(id); });
    });
  });
  // City uses Tagify — it syncs the original input and fires 'change' on it
  $(document).on('change', '#kt_tagify_work_location', function(){ validateOne('kt_tagify_work_location'); });

  // Public: validate everything, focus the first invalid field. Returns true when valid.
  window.jpValidateAll = function(){
    var firstInvalid = null;
    Object.keys(rules).forEach(function(id){
      if(!validateOne(id) && !firstInvalid) firstInvalid = document.getElementById(id);
    });
    if(firstInvalid){
      var f = field(firstInvalid);
      if(f && f.scrollIntoView) f.scrollIntoView({behavior:'smooth', block:'center'});
      try { firstInvalid.focus(); } catch(e){}
      return false;
    }
    return true;
  };
})();
</script>
@endsection

@push('footer-script')
<script src="https://unpkg.com/@yaireo/tagify"></script>
<script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />


<script>

  $(document).ready(function () {
    // Initialize intl-tel-input for Primary and Secondary phone fields
    const primaryPhoneInput = window.intlTelInput(document.querySelector("#mobile"), {
      separateDialCode: true,
      initialCountry: "{{ $phone_code }}",
      preferredCountries: ["in", "us", "gb", "ca", "au"],
    });


      // Function to update hidden country code inputs
    function updateCountryCode(inputField, hiddenField) {
      const selectedCountryData = inputField.getSelectedCountryData();
      //$(hiddenField).val(selectedCountryData.dialCode); // Set the country code in the hidden field
      const countryCode = selectedCountryData.iso2; // e.g., 'us', 'in', etc.
      $(hiddenField).val(countryCode);

    }
    // Update country code on input or country flag selection
    $("#mobile").on("input change", function () {
      updateCountryCode(primaryPhoneInput, "#phone_country_code");
    });


    // Update country code dynamically when the user selects a different country flag
    $(".iti__flag-container").on("click", function (event) {
      const targetInput = $(event.target).closest(".iti").find("input");

      if (targetInput.is("#mobile")) {
        updateCountryCode(primaryPhoneInput, "#phone_country_code");
      }

    });

    setTimeout(function () {
      $(".iti__flag-container").click();
    },500);

  });


</script>

<!--begin::Script-->
<script>
    /******/ (() => { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};
/*!*********************************************************************************************!*\
  !*** FORM VALIDATION***!
  \*********************************************************************************************/


  setTimeout(() => {


  const cities = @json($cities);

  let inputId = `#kt_tagify_work_location`;
  let inputElement = document.querySelector(inputId);

  if (inputElement) {
      let tagifyInstance = new Tagify(inputElement, {
          whitelist: cities,
          enforceWhitelist: true, // Prevents users from adding custom values
          maxTags: 1,
          dropdown: {
              maxItems: 20,           // maximum allowed rendered suggestions
              classname: "", // custom classname for this dropdown
              enabled: 0,             // show suggestions on focus
              closeOnSelect: true    // do not hide the suggestions dropdown once an item has been selected
          }
      });

      // Prevent creating tags on blur or enter key
      tagifyInstance.on('input', function(e) {
          tagifyInstance.dropdown.show(); // show the dropdown immediately
      });

      // Create tags only on select
      tagifyInstance.on('dropdown:select', function(e) {
          tagifyInstance.addTags([e.detail.data.value]);
      });

      tagifyInstance.on('add', function(e) {
        // Remove the tag if it wasn't selected from the dropdown
        // if (!cities.includes(e.detail.data.value)) {
        //   tagifyInstance.removeTag(e.detail.data.value);
        // }
      });


      function debounce(func, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    }

         // Debounced function to fetch suggestions
    const fetchSuggestions = debounce(function(query) {
        $.ajax({
            url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query,
            method: 'GET',
            success: function(data) {
                console.warn("Fetched Cities: ", data);

                if (Array.isArray(data)) {
                    tagifyInstance.settings.whitelist = data; // Update whitelist
                    // tagifyInstance.dropdown.show(query); // Show suggestions
                    tagifyInstance.dropdown.show.call(tagifyInstance);
                } else {
                    console.error("Invalid API response format: Expected array.");
                }
            },
            error: function(err) {
                console.error("Error fetching cities:", err);
            }
        });
    }, 200); // 300ms delay

    // Listen for user input to trigger API call
    tagifyInstance.on('input', function(e) {
        var query = e.detail.value.trim();
        if (query.length < 2) return;
        fetchSuggestions(query);
    });

    setTimeout(() => {
      var city = "{{@$return}}";
      if(city !=''){
        var cityName = city.split(" | ")[0]; // Get the first part (Ballia)
        fetchSuggestions(cityName);
      }
    }, 100);
    setTimeout(() => {
        var defaultLocation = {!! json_encode(@$return) !!}; // Ensure proper Blade syntax for JavaScript
        if (defaultLocation) {
          tagifyInstance.addTags([defaultLocation]); // Add the default location as a tag
          tagifyInstance.dropdown.hide();
        }
    }, 2000); // 3 seconds delay


  }

  }, 1500);




// Class definition
var KTAccountSettingsProfileDetails = function () {
    // Private variables
    var form;
    var submitButton;
    var validation;

    // Private functions
    var initValidation = function () {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validation = FormValidation.formValidation(
            form,
            {
                fields: {
                    fullname: {
                        validators: {
                            notEmpty: {
                                message: 'Full name is required'
                            }
                        }
                    },
                    designation: {
                        validators: {
                            notEmpty: {
                                message: 'Designation is required'
                            }
                        }
                    },
                    email: {
                        validators: {
                            notEmpty: {
                                message: 'Email is required'
                            }
                        }
                    },
                    mobile: {
                        validators: {
                            notEmpty: {
                                message: 'Phone is required'
                            },
                            stringLength: {
                                min: 10,
                                max: 10,
                                message: 'Phone number must be 10 digits long'
                            },
                            regexp: {
                                regexp: /^[0-9]+$/,
                                message: 'Phone number can only contain digits'
                            }
                        }
                    },
                    country: {
                        validators: {
                            notEmpty: {
                                message: 'Please select a country'
                            }
                        }
                    },
                    state: {
                        validators: {
                            notEmpty: {
                                message: 'Please select a state'
                            }
                        }
                    }


                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    //defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );

        // Select2 validation integration
        $(form.querySelector('[name="country"]')).on('change', function() {
            // Revalidate the color field when an option is chosen
            validation.revalidateField('country');
        });

        $(form.querySelector('[name="state"]')).on('change', function() {
            // Revalidate the color field when an option is chosen
            validation.revalidateField('state');
        });

        $(form.querySelector('[name="city"]')).on('change', function() {
            // Revalidate the color field when an option is chosen
            validation.revalidateField('city');
        });
    }

    var handleForm = function () {
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            validation.validate().then(function (status) {
                if (status == 'Valid') {

                    swal.fire({
                        text: "Thank you! You've updated your basic info",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-light-primary"
                        }
                    });

                } else {
                    swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-light-primary"
                        }
                    });
                }
            });
        });
    }

    // Public methods
    return {
        init: function () {
            form = document.getElementById('kt_account_profile_details_form');
            submitButton = form.querySelector('#kt_account_profile_details_submit');

            initValidation();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTAccountSettingsProfileDetails.init();
});

/******/ })()
;
//# FORM VALIDATION
</script>
<!--end::Script-->
<script>

$( document ).ready(function() {

		 $('.country').change(function(){

					var cid				=	$(this).val() ;
					if(cid == 101){
						$('#city_id').show();
						$('#city').hide();
					}else{
						$('#city').show();
						$('#city_id').hide();
					}

   					var url 			=	"{{ route('service_company.job-applications.statelist',':cid') }}";
   					url 				=	url.replace(':cid', cid);
   					$.ajax({
   									type	: 'GET',
   									url		: url,
   									success	: function (response) {
   																		$('#state').html(response.data);

   																   }
   					});
   		});

		$(document).on('change', '.change_state', function() {

				var country				=	$('#country').val();
   				var state_id			=	this.value;
				if(country == 101){
					$('#city_id').show();
					$('#city').hide();
				}else{
					$('#city').show();
					$('#city_id').hide();
				}
				var url 			=	"{{ route('service_company.clients.citylist',':state_id') }}";
				url 				=	url.replace(':state_id', state_id);
				$.ajax({
								type	: 'GET',
								url		: url,
								success	: function (response) {
																	$('#city_id').html(response.data);
															   }
				});
   		});



    	$(document).on('change', '.country123', function() {

   					cid						=	this.value;
   					var url 			=	"{{ route('service_company.job-applications.statelist',':cid') }}";
   					url 				=	url.replace(':cid', cid);
   					$.ajax({
   									type	: 'GET',
   									url		: url,
   									success	: function (response) {
   																		$('#state').html(response.data);
   																   }
   					      });

   	});

});

</script>
<script>




$(document).on('click', '#updateProfile', function() {

 	/*if(! $(#kt_account_profile_details_form).valid())
	{
		alert("false");
		return false;
	}else{
		alert("true");
		return false;
	} */

   	var data 			=	new FormData();
   	var form_data 		=	$('#kt_account_profile_details_form').serializeArray();
   	$.each(form_data, function (key, input) {
       		data.append(input.name, input.value);
   	});

   	var file_data 		= $('input[name="avatar"]')[0].files;
   	for (var i = 0; i < file_data.length; i++) {
       		data.append("logo", file_data[i]);
   	}
    // Client-side validation: shows inline errors and focuses the first invalid field
    if (typeof window.jpValidateAll === 'function' && !window.jpValidateAll()) {
      return false; // Stop form submission
    }

    const fileInput = document.getElementById('avatar'); // Replace with your file input ID
    const file = fileInput.files[0];
    // Check if a file is selected
    if (file) {
          if (file.size > 2097152) {
            Swal.fire("Failed", "The file size must not exceed 2 MB", "warning");
            e.preventDefault(); // Stop form submission
            return false;
        }
    }

    // Check file size (2 MB = 2097152 bytes)



   	/*var kt_docs_tinymce_hidden	=	tinymce.get("kt_docs_tinymce_hidden").getContent();
    	data.append("description", kt_docs_tinymce_hidden);
   	data.append('key', 'value');*/
    $('#updateProfile').prop('disabled',true);
    $('#updateProfile').text('Waiting...');

   	$.ajax({
       			//url					: '{{route('service_company.clients.store')}}',
   				url					: '{{route('service_company.profile.update', @$team->id)}}',
   				headers				: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
       			method				: "POST",
       			processData			: false,
       			contentType			: false,
      		 	data				: data,
   				redirect			: true,
       			success				: function (data) {
              $('#updateProfile').prop('disabled',false);
              $('#updateProfile').text('Save Changes');
   										//alert(data.status);
   										if(data.status == 'success'){
												Swal.fire("Success", "Profile update successfully!", "success");
                        if(data.team_page){
                           window.location = '{{ route('service_company.team.index') }}' ;
                        }else{
                          window.location = '{{ route('service_company.profile.index') }}' ;
                        }

   												//{"status":"success","message":"menu.clients created successfully.","action":"redirect","url":"https:\/\/rm.vibhanshujaiswal.com\/service_company\/clients"}
   										}

       			}
       			//error: function (e) {
           				//error
       			//}
   			});

   });





</script>
@endpush
