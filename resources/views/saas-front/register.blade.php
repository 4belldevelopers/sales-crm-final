@extends('layouts.metronic_front_app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('demo/dist/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

<style>
:root {
  --j-primary: #1d61f2;
  --j-primary-h: #154ec4;
  --j-ink: #0f172a;
  --j-gray: #64748b;
  --j-border: #e2e8f0;
  --j-bg: #f8fafc;
  --j-white: #ffffff;
  --j-danger: #ef4444;
  --j-success: #10b981;
}

body, html {
  font-family: 'Poppins', sans-serif !important;
  background: var(--j-bg) !important;
  margin: 0;
  padding: 0;
  min-height: 100vh;
}

.jero-signup-wrap {
  display: flex;
  width: 100%;
  max-width: 1100px;
  min-height: 100vh;
  margin: 0 auto;
  align-items: center;
  justify-content: center;
  padding: 20px;
  box-sizing: border-box;
}

.jero-signup-card {
  display: flex;
  width: 100%;
  max-height: 90vh;
  background: var(--j-white);
  border: 1px solid var(--j-border);
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(15,23,42,0.06);
  overflow: hidden;
}

/* Left Showcase Pane */
.jero-value-pane {
  width: 38%;
  background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
  color: #fff;
  padding: 36px 30px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  box-sizing: border-box;
}

.jero-value-head h2 {
  font-size: 20px;
  font-weight: 700;
  color: #fff;
  margin: 16px 0 6px;
  line-height: 1.3;
}

.jero-value-head p {
  font-size: 12.5px;
  color: #94a3b8;
  line-height: 1.45;
  margin: 0;
}

.jero-features-list {
  list-style: none;
  padding: 0;
  margin: 22px 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.jero-feature-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 12.5px;
  color: #e2e8f0;
}

.jero-feature-check {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: rgba(29,97,242,0.25);
  color: #60a5fa;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  flex-shrink: 0;
}

.jero-trust-badge {
  font-size: 11px;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 6px;
  border-top: 1px solid rgba(255,255,255,0.08);
  padding-top: 16px;
}

/* Right Form Pane */
.jero-form-pane {
  width: 62%;
  padding: 32px 36px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.jero-form-header h1 {
  font-size: 21px;
  font-weight: 700;
  color: var(--j-ink);
  margin: 0 0 4px;
}

.jero-form-header p {
  font-size: 12px;
  color: var(--j-gray);
  margin: 0 0 16px;
}

.jero-field {
  margin-bottom: 12px;
  position: relative;
}

.jero-label {
  display: block;
  font-size: 11.5px;
  font-weight: 600;
  color: var(--j-ink);
  margin-bottom: 4px;
}

.jero-input-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.jero-input {
  width: 100%;
  height: 38px;
  border: 1px solid var(--j-border);
  border-radius: 7px;
  padding: 0 12px;
  font-size: 12.5px;
  font-family: inherit;
  color: var(--j-ink);
  outline: none;
  transition: all 0.15s ease;
  box-sizing: border-box;
  background: #fff;
}

.jero-input:focus {
  border-color: var(--j-primary);
  box-shadow: 0 0 0 3px rgba(29,97,242,0.12);
}

.jero-input.is-invalid {
  border-color: var(--j-danger);
  background: #fef2f2;
}

.jero-input.is-valid {
  border-color: var(--j-success);
}

.jero-field .jero-phone-box-wrapper .jero-phone-input-group {
  border-color: var(--j-border) !important;
  border-radius: 7px !important;
  height: 38px !important;
  background-color: #fff !important;
  transition: all 0.15s ease !important;
}

.jero-field .jero-phone-box-wrapper .jero-phone-input-group:focus-within {
  border-color: var(--j-primary) !important;
  box-shadow: 0 0 0 3px rgba(29,97,242,0.12) !important;
}

.jero-field .jero-phone-box-wrapper .jero-phone-input-group.is-invalid {
  border-color: var(--j-danger) !important;
  background-color: #fef2f2 !important;
}

.jero-btn-submit {
  width: 100%;
  height: 42px;
  background: var(--j-primary);
  color: #fff;
  border: none;
  border-radius: 7px;
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 14px;
}

.jero-btn-submit:hover {
  background: var(--j-primary-h);
}

.jero-btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.jero-legal-text {
  font-size: 11px;
  color: var(--j-gray);
  text-align: center;
  margin-top: 12px;
  line-height: 1.4;
}

.jero-legal-text a {
  color: var(--j-primary);
  text-decoration: none;
  font-weight: 500;
}

.jero-field-error {
  color: var(--j-danger);
  font-size: 11px;
  margin-top: 3px;
  display: none;
}

/* Domain collision notice card */
.jero-collision-box {
  display: none;
  background: #fffbeb;
  border: 1px solid #fde68a;
  border-radius: 8px;
  padding: 14px;
  margin-bottom: 14px;
}

.jero-collision-title {
  font-size: 12.5px;
  font-weight: 600;
  color: #b45309;
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 4px;
}

.jero-collision-desc {
  font-size: 11.5px;
  color: #92400e;
  line-height: 1.4;
  margin: 0 0 10px;
}

.jero-collision-cta {
  display: inline-block;
  background: #d97706;
  color: #fff;
  font-size: 11.5px;
  font-weight: 600;
  padding: 6px 14px;
  border-radius: 6px;
  text-decoration: none;
}

.jero-collision-cta:hover {
  background: #b45309;
  color: #fff;
}

/* Success screen */
.jero-success-pane {
  display: none;
  text-align: center;
  padding: 30px 10px;
}

.jero-success-icon {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #ecfdf5;
  color: #059669;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 26px;
  margin-bottom: 14px;
}

.jero-success-pane h2 {
  font-size: 20px;
  font-weight: 700;
  color: var(--j-ink);
  margin-bottom: 6px;
}

.jero-success-pane p {
  font-size: 13px;
  color: var(--j-gray);
  line-height: 1.5;
  max-width: 440px;
  margin: 0 auto 20px;
}

@media (max-width: 900px) {
  .jero-value-pane { display: none; }
  .jero-form-pane { width: 100%; padding: 24px 20px; }
  .jero-signup-wrap { padding: 12px; }
}
</style>

<div class="jero-signup-wrap">
  <div class="jero-signup-card">
    
    <!-- Left Showcase Pane -->
    <div class="jero-value-pane">
      <div class="jero-value-head">
        <a href="/" style="display:flex;align-items:center;gap:8px;text-decoration:none;">
          <svg width="28" height="28" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="8" fill="#1d61f2"/><path d="M10 22L16 10L22 22" stroke="white" stroke-width="2.5" stroke-linecap="round"/></svg>
          <span style="font-size:20px;font-weight:700;color:#fff;letter-spacing:-0.5px;">JERO</span>
        </a>
        <h2>The GOD-Level ATS &amp; Talent Marketplace</h2>
        <p>Start hiring top talent in minutes. Fast, clean, and enterprise-grade.</p>
        
        <ul class="jero-features-list">
          <li class="jero-feature-item">
            <span class="jero-feature-check">✓</span>
            <span>Sub-second candidate search &amp; filters</span>
          </li>
          <li class="jero-feature-item">
            <span class="jero-feature-check">✓</span>
            <span>Dual-sided B2B talent exchange</span>
          </li>
          <li class="jero-feature-item">
            <span class="jero-feature-check">✓</span>
            <span>Zero server read strain architecture</span>
          </li>
          <li class="jero-feature-item">
            <span class="jero-feature-check">✓</span>
            <span>Free webmail &amp; corporate domain support</span>
          </li>
        </ul>
      </div>

      <div class="jero-trust-badge">
        <span>🔒 SOC 2 Type II</span> • <span>ISO 27001</span> • <span>GDPR Compliant</span>
      </div>
    </div>

    <!-- Right Form Pane -->
    <div class="jero-form-pane" id="signup_container">
      
      <!-- Domain Collision Alert Card -->
      <div class="jero-collision-box" id="collision_card">
        <div class="jero-collision-title">
          <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
          <span id="collision_title">This company domain is already registered on Jero.</span>
        </div>
        <p class="jero-collision-desc" id="collision_desc">
          An administrator or team member from this domain is already registered. If you want to join the same company, please ask your company administrator to invite you to Jero.
        </p>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
          <a href="{{ route('login') }}" class="jero-collision-cta">Go to Jero</a>
          <span style="font-size:11px;color:#92400e;">Think this is an error? <a href="mailto:support@jeroworks.com" style="color:#b45309;font-weight:600;text-decoration:underline;">Contact Support</a></span>
        </div>
      </div>

      <div class="jero-form-header" id="form_header">
        <h1>Create your Workspace</h1>
        <p>Enter your basic details to get started. No credit card required.</p>
      </div>

      <!-- Single-Page 5-Field Form -->
      <form id="jero_signup_form" novalidate autocomplete="on">
        @csrf
        <input type="text" name="website_url" style="display:none;" tabindex="-1" autocomplete="off" aria-hidden="true">

        <!-- 1. Full Name -->
        <div class="jero-field">
          <label class="jero-label">Full Name <span style="color:var(--j-danger);">*</span></label>
          <input type="text" name="name" id="name" class="jero-input" placeholder="e.g. Sarah Connor" required autocomplete="name">
          <div class="jero-field-error" id="err_name"></div>
        </div>

        <!-- 2. Work Email -->
        <div class="jero-field">
          <label class="jero-label">Work Email <span style="color:var(--j-danger);">*</span></label>
          <input type="email" name="email" id="signup_email" class="jero-input" placeholder="sarah@company.com or sarah@gmail.com" required autocomplete="email">
          <div class="jero-field-error" id="err_email"></div>
        </div>

        <!-- 3. Phone Number with Global Mobile Number Architecture (V7.0) -->
        <div class="jero-field">
          <label class="jero-label">Phone Number <span style="color:var(--j-danger);">*</span></label>
          <x-jero-phone-input
            id="contact_number"
            name="contact_number"
            countryCodeName="contact_code"
            countryCodeId="contact_code"
            label=""
            :required="true"
            placeholder="Enter phone number"
            countryCode="+1"
            defaultIso="US"
          />
          <div class="jero-field-error" id="err_contact_number"></div>
        </div>

        <!-- 4. Company Type -->
        <div class="jero-field">
          <label class="jero-label">Company Type <span style="color:var(--j-danger);">*</span></label>
          <select name="company_type" id="company_type" class="jero-input" required>
            <option value="" disabled selected>Select your company business model...</option>
            <option value="3">Product or Manufacturing Company (Direct Employer)</option>
            <option value="1">Recruitment or Staffing Agency</option>
            <option value="2">Consulting or Services Company</option>
            <option value="4">Freelancer / Individual Recruiter</option>
          </select>
          <div class="jero-field-error" id="err_company_type"></div>
        </div>

        <!-- 5. Password with Strength Meter -->
        <div class="jero-field">
          <label class="jero-label">Password <span style="color:var(--j-danger);">*</span></label>
          <div class="jero-input-wrap">
            <input type="password" name="password" id="signup_password" class="jero-input" placeholder="Minimum 8 characters" required autocomplete="new-password">
            <span id="pwd_toggle" style="position:absolute;right:10px;cursor:pointer;color:var(--j-gray);font-size:14px;user-select:none;">👁</span>
          </div>
          <div id="pwd_strength_bar" style="display:flex;gap:4px;margin-top:5px;height:3px;">
            <div style="flex:1;background:#e2e8f0;border-radius:2px;transition:background 0.2s;" id="str_1"></div>
            <div style="flex:1;background:#e2e8f0;border-radius:2px;transition:background 0.2s;" id="str_2"></div>
            <div style="flex:1;background:#e2e8f0;border-radius:2px;transition:background 0.2s;" id="str_3"></div>
            <div style="flex:1;background:#e2e8f0;border-radius:2px;transition:background 0.2s;" id="str_4"></div>
          </div>
          <div class="jero-field-error" id="err_password"></div>
        </div>

        <!-- Submit Button -->
        <button type="submit" id="btn_submit" class="jero-btn-submit">
          <span id="btn_text">Create Workspace &amp; Verify Email →</span>
          <span id="btn_spinner" style="display:none;" class="spinner-border spinner-border-sm"></span>
        </button>

        <div class="jero-legal-text">
          By registering, you agree to our <a href="{{ route('termsAndConditions') }}" target="_blank">Terms</a> &amp; <a href="/privacy-policy" target="_blank">Privacy Policy</a>.
        </div>

        <div style="text-align:center;margin-top:10px;font-size:12px;color:var(--j-gray);">
          Already have an account? <a href="{{ route('login') }}" style="color:var(--j-primary);font-weight:600;text-decoration:none;">Sign In</a>
        </div>
      </form>

      <!-- Post-Submit Verification Waiting State -->
      <div class="jero-success-pane" id="success_pane">
        <div class="jero-success-icon">✓</div>
        <h2>Verify your email address</h2>
        <p>
          We've sent an activation link to <strong id="sent_email_text" style="color:var(--j-ink);">your email</strong>.<br>
          Please check your inbox and click the verification link to start your Jero workspace.
        </p>
        <div style="display:flex;gap:10px;justify-content:center;align-items:center;">
          <a href="{{ route('login') }}" class="jero-btn-submit" style="width:auto;padding:0 22px;text-decoration:none;">Go to Sign In</a>
        </div>
        <p style="font-size:11px;color:var(--j-gray);margin-top:18px;">
          Didn't receive the email? Check your spam folder or wait a moment.
        </p>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('jero_signup_form');
  const btnSubmit = document.getElementById('btn_submit');
  const btnText = document.getElementById('btn_text');
  const btnSpinner = document.getElementById('btn_spinner');
  const emailInput = document.getElementById('signup_email');
  const pwdInput = document.getElementById('signup_password');
  const pwdToggle = document.getElementById('pwd_toggle');
  const collisionCard = document.getElementById('collision_card');
  const collisionTitle = document.getElementById('collision_title');
  const collisionDesc = document.getElementById('collision_desc');
  const successPane = document.getElementById('success_pane');
  const formHeader = document.getElementById('form_header');

  // Password visibility toggle
  pwdToggle.addEventListener('click', function () {
    if (pwdInput.type === 'password') {
      pwdInput.type = 'text';
      pwdToggle.textContent = '🔒';
    } else {
      pwdInput.type = 'password';
      pwdToggle.textContent = '👁';
    }
  });

  // Client-Side Password Strength Estimation (User RAM)
  pwdInput.addEventListener('input', function () {
    const val = pwdInput.value;
    const s1 = document.getElementById('str_1');
    const s2 = document.getElementById('str_2');
    const s3 = document.getElementById('str_3');
    const s4 = document.getElementById('str_4');
    
    // Reset
    [s1, s2, s3, s4].forEach(el => el.style.background = '#e2e8f0');
    if (!val) return;

    let score = 0;
    if (val.length >= 8) score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
    if (/[0-9]/.test(val) || /[^A-Za-z0-9]/.test(val)) score++;

    if (score >= 1) s1.style.background = '#ef4444'; // Red
    if (score >= 2) s2.style.background = '#f59e0b'; // Amber
    if (score >= 3) s3.style.background = '#3b82f6'; // Blue
    if (score >= 4) s4.style.background = '#10b981'; // Green
  });

  // Client-side domain detection & live check on blur
  emailInput.addEventListener('blur', function () {
    const email = emailInput.value.trim().toLowerCase();
    if (!email || !email.includes('@')) return;

    const domain = email.split('@')[1];
    const freeWebmails = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com', 'aol.com', 'zoho.com', 'mail.com', 'protonmail.com'];

    // Free webmails are 100% permitted - no check needed
    if (freeWebmails.includes(domain)) {
      collisionCard.style.display = 'none';
      return;
    }

    // Corporate domain probe (asynchronous cached check)
    fetch("{{ route('domain-company-register-chk') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ email: email })
    })
    .then(r => r.json())
    .then(data => {
      if (data.status === 'domain_collision') {
        collisionTitle.textContent = data.title || 'This company domain is already registered on Jero.';
        collisionDesc.textContent = data.message || 'An administrator or team member from this domain is already registered.';
        collisionCard.style.display = 'block';
      } else {
        collisionCard.style.display = 'none';
      }
    })
    .catch(() => {});
  });

  // Clear errors on typing
  ['name', 'email', 'contact_number', 'company_type', 'password'].forEach(f => {
    const el = document.querySelector(`[name="${f}"]`);
    if (el) {
      el.addEventListener('input', () => {
        el.classList.remove('is-invalid');
        const group = el.closest('.jero-phone-input-group');
        if (group) group.classList.remove('is-invalid');
        const errEl = document.getElementById(`err_${f}`);
        if (errEl) errEl.style.display = 'none';
      });
    }
  });

  // Multi-Tab BroadcastChannel: auto-transition when email verified in another tab
  if (typeof BroadcastChannel !== 'undefined') {
    const authChannel = new BroadcastChannel('jero_auth_channel');
    authChannel.onmessage = (event) => {
      if (event.data?.type === 'VERIFICATION_SUCCESS') {
        window.location.href = event.data.redirect_url || '/service_company/account/onboarding';
      }
    };
  }

  // Form Submit Handler
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // Client-side quick check
    let hasError = false;
    const requiredFields = [
      { name: 'name', label: 'Please enter your full name.' },
      { name: 'email', label: 'Please enter a valid work email.' },
      { name: 'contact_number', label: 'Please enter your phone number.' },
      { name: 'company_type', label: 'Please select your company type.' },
      { name: 'password', label: 'Please enter a password (min 8 characters).' }
    ];

    requiredFields.forEach(item => {
      const el = document.querySelector(`[name="${item.name}"]`);
      const errEl = document.getElementById(`err_${item.name}`);
      if (!el || !el.value || !el.value.trim()) {
        if (el) {
          el.classList.add('is-invalid');
          const group = el.closest('.jero-phone-input-group');
          if (group) group.classList.add('is-invalid');
        }
        if (errEl) {
          errEl.textContent = item.label;
          errEl.style.display = 'block';
        }
        hasError = true;
      }
    });

    if (hasError) return;

    // Loading State
    btnSubmit.disabled = true;
    btnText.textContent = 'Creating Workspace...';
    btnSpinner.style.display = 'inline-block';

    const formData = new FormData(form);

    fetch("{{ route('company-register') }}", {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: formData
    })
    .then(async response => {
      const data = await response.json();
      btnSubmit.disabled = false;
      btnSpinner.style.display = 'none';
      btnText.textContent = 'Create Workspace & Verify Email →';

      if (response.status === 422) {
        if (data.status === 'domain_collision') {
          collisionTitle.textContent = data.title;
          collisionDesc.textContent = data.message;
          collisionCard.style.display = 'block';
          collisionCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          return;
        }

        // Field validation errors
        if (data.validation) {
          Object.keys(data.validation).forEach(field => {
            const el = document.querySelector(`[name="${field}"]`);
            const errEl = document.getElementById(`err_${field}`);
            if (el) el.classList.add('is-invalid');
            if (errEl) {
              errEl.textContent = data.validation[field][0];
              errEl.style.display = 'block';
            }
          });
        }
        return;
      }

      if (data.status === 'success') {
        form.style.display = 'none';
        formHeader.style.display = 'none';
        collisionCard.style.display = 'none';
        document.getElementById('sent_email_text').textContent = data.email || emailInput.value;
        successPane.style.display = 'block';
      } else {
        alert(data.message || 'Registration failed. Please try again.');
      }
    })
    .catch(err => {
      btnSubmit.disabled = false;
      btnSpinner.style.display = 'none';
      btnText.textContent = 'Create Workspace & Verify Email →';
      alert('A network error occurred. Please try again.');
    });
  });
});
</script>
@endsection
