@extends('layouts.metronic_front_app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
  --ob-primary: #1d61f2;
  --ob-primary-h: #154ec4;
  --ob-ink: #0f172a;
  --ob-gray: #64748b;
  --ob-border: #e2e8f0;
  --ob-bg: #f8fafc;
  --ob-white: #ffffff;
  --ob-danger: #ef4444;
  --ob-success: #10b981;
}

.jero-onboard-wrap {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 120px);
  padding: 30px 20px;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
}

.jero-onboard-card {
  width: 100%;
  max-width: 620px;
  background: var(--ob-white);
  border: 1px solid var(--ob-border);
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(15,23,42,0.06);
  padding: 36px 40px;
  box-sizing: border-box;
}

.jero-onboard-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #ecfdf5;
  color: #059669;
  font-size: 11.5px;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 20px;
  margin-bottom: 12px;
}

.jero-onboard-head h1 {
  font-size: 22px;
  font-weight: 700;
  color: var(--ob-ink);
  margin: 0 0 6px;
}

.jero-onboard-head p {
  font-size: 13px;
  color: var(--ob-gray);
  margin: 0 0 24px;
  line-height: 1.5;
}

.jero-field {
  margin-bottom: 18px;
}

.jero-label {
  display: block;
  font-size: 12.5px;
  font-weight: 600;
  color: var(--ob-ink);
  margin-bottom: 6px;
}

.jero-input {
  width: 100%;
  height: 42px;
  border: 1px solid var(--ob-border);
  border-radius: 8px;
  padding: 0 14px;
  font-size: 13px;
  font-family: inherit;
  color: var(--ob-ink);
  outline: none;
  transition: all 0.15s ease;
  box-sizing: border-box;
  background: #fff;
}

.jero-input:focus {
  border-color: var(--ob-primary);
  box-shadow: 0 0 0 3px rgba(29,97,242,0.12);
}

.jero-input.is-invalid {
  border-color: var(--ob-danger);
  background: #fef2f2;
}

/* Logo Upload Dropzone */
.jero-logo-dropzone {
  border: 2px dashed var(--ob-border);
  border-radius: 10px;
  padding: 20px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  background: #fafafa;
  position: relative;
}

.jero-logo-dropzone:hover {
  border-color: var(--ob-primary);
  background: #f0f6ff;
}

.jero-logo-preview {
  width: 64px;
  height: 64px;
  border-radius: 8px;
  object-fit: contain;
  display: none;
  margin: 0 auto 10px;
  background: #fff;
  border: 1px solid var(--ob-border);
  padding: 4px;
}

.jero-logo-text {
  font-size: 12px;
  color: var(--ob-gray);
  margin: 0;
}

.jero-logo-limits {
  font-size: 11px;
  color: #94a3b8;
  margin-top: 4px;
}

.jero-btn-complete {
  width: 100%;
  height: 46px;
  background: var(--ob-primary);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 26px;
}

.jero-btn-complete:hover {
  background: var(--ob-primary-h);
}

.jero-btn-complete:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.jero-field-error {
  color: var(--ob-danger);
  font-size: 11.5px;
  margin-top: 4px;
  display: none;
}
</style>

<div class="jero-onboard-wrap">
  <div class="jero-onboard-card">
    
    <div class="jero-onboard-badge">
      <span>✓ Email Verified</span>
    </div>

    <div class="jero-onboard-head">
      <h1>Welcome to Jero Workspace!</h1>
      <p>Your account is active. Finalize your company name and brand to enter your ATS dashboard.</p>
    </div>

    <form id="jero_onboarding_form" enctype="multipart/form-data" novalidate>
      @csrf

      <!-- 1. Company / Organization Name -->
      <div class="jero-field">
        <label class="jero-label">Company / Organization Name <span style="color:var(--ob-danger);">*</span></label>
        <input 
          type="text" 
          name="company_name" 
          id="company_name" 
          class="jero-input" 
          placeholder="e.g. Acme Technologies Inc." 
          value="{{ ($company && $company->company_name !== 'Pending Onboarding') ? $company->company_name : '' }}" 
          required 
          autofocus
        >
        <div class="jero-field-error" id="err_company_name"></div>
      </div>

      <!-- 2. Company Logo (Strict <= 2MB limit) -->
      <div class="jero-field">
        <label class="jero-label">Company Logo (Optional — Max 2MB)</label>
        <div class="jero-logo-dropzone" id="logo_dropzone">
          <img id="logo_preview" class="jero-logo-preview" alt="Logo Preview">
          <p class="jero-logo-text" id="logo_label">
            <strong>Click to upload</strong> or drag and drop your logo
          </p>
          <div class="jero-logo-limits">
            Supported: PNG, JPG, WebP, SVG • <strong>Strict Limit: 2MB</strong>
          </div>
          <input type="file" name="logo" id="logo_input" accept="image/png,image/jpeg,image/webp,image/svg+xml" style="display:none;">
        </div>
        <div class="jero-field-error" id="err_logo"></div>
      </div>

      <!-- 3. Official Website URL -->
      <div class="jero-field">
        <label class="jero-label">Official Website URL (Optional)</label>
        <input 
          type="url" 
          name="website" 
          id="website" 
          class="jero-input" 
          placeholder="https://acmetech.com" 
          value="{{ $company->website ?? '' }}"
        >
        <div class="jero-field-error" id="err_website"></div>
      </div>

      <!-- Submit CTA -->
      <button type="submit" id="btn_onboard_submit" class="jero-btn-complete">
        <span id="btn_onboard_text">Complete Setup &amp; Enter Dashboard →</span>
        <span id="btn_onboard_spinner" style="display:none;" class="spinner-border spinner-border-sm"></span>
      </button>

    </form>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('jero_onboarding_form');
  const dropzone = document.getElementById('logo_dropzone');
  const logoInput = document.getElementById('logo_input');
  const logoPreview = document.getElementById('logo_preview');
  const logoLabel = document.getElementById('logo_label');
  const errLogo = document.getElementById('err_logo');
  const btnSubmit = document.getElementById('btn_onboard_submit');
  const btnText = document.getElementById('btn_onboard_text');
  const btnSpinner = document.getElementById('btn_onboard_spinner');

  // Broadcast to other tabs that verification completed
  if (typeof BroadcastChannel !== 'undefined') {
    try {
      const authChannel = new BroadcastChannel('jero_auth_channel');
      authChannel.postMessage({
        type: 'VERIFICATION_SUCCESS',
        redirect_url: window.location.href
      });
    } catch (e) {}
  }

  // Click dropzone to open file dialog
  dropzone.addEventListener('click', () => logoInput.click());

  // Drag and drop events
  ['dragenter', 'dragover'].forEach(name => {
    dropzone.addEventListener(name, (e) => {
      e.preventDefault();
      dropzone.style.borderColor = 'var(--ob-primary)';
      dropzone.style.background = '#f0f6ff';
    });
  });

  ['dragleave', 'drop'].forEach(name => {
    dropzone.addEventListener(name, (e) => {
      e.preventDefault();
      dropzone.style.borderColor = 'var(--ob-border)';
      dropzone.style.background = '#fafafa';
    });
  });

  dropzone.addEventListener('drop', (e) => {
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      logoInput.files = e.dataTransfer.files;
      handleLogoSelect(e.dataTransfer.files[0]);
    }
  });

  logoInput.addEventListener('change', () => {
    if (logoInput.files && logoInput.files.length > 0) {
      handleLogoSelect(logoInput.files[0]);
    }
  });

  // Client-Side Validation & Preview (Strict <= 2MB Enforcement in User RAM)
  function handleLogoSelect(file) {
    errLogo.style.display = 'none';

    // 1. Strict 2MB Limit Guard (2,097,152 bytes)
    const MAX_SIZE = 2 * 1024 * 1024;
    if (file.size > MAX_SIZE) {
      errLogo.textContent = 'Logo file size exceeds 2MB limit (' + (file.size / (1024 * 1024)).toFixed(2) + 'MB). Please choose a file under 2MB.';
      errLogo.style.display = 'block';
      logoInput.value = '';
      logoPreview.style.display = 'none';
      logoLabel.innerHTML = '<strong>Click to upload</strong> or drag and drop your logo';
      return;
    }

    // 2. MIME type check
    const validMimes = ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'];
    if (!validMimes.includes(file.type)) {
      errLogo.textContent = 'Invalid file type. Please upload a PNG, JPG, WebP, or SVG.';
      errLogo.style.display = 'block';
      logoInput.value = '';
      return;
    }

    // 3. User RAM Preview
    const reader = new FileReader();
    reader.onload = function (e) {
      logoPreview.src = e.target.result;
      logoPreview.style.display = 'block';
      logoLabel.innerHTML = '<strong>' + file.name + '</strong> (' + (file.size / 1024).toFixed(1) + ' KB)';
    };
    reader.readAsDataURL(file);
  }

  // Clear company_name error on input
  const nameInput = document.getElementById('company_name');
  nameInput.addEventListener('input', () => {
    nameInput.classList.remove('is-invalid');
    const err = document.getElementById('err_company_name');
    if (err) err.style.display = 'none';
  });

  // Form Submit Handler
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const companyName = nameInput.value.trim();
    if (!companyName) {
      nameInput.classList.add('is-invalid');
      const err = document.getElementById('err_company_name');
      err.textContent = 'Please enter your company or organization name.';
      err.style.display = 'block';
      return;
    }

    // Loading State
    btnSubmit.disabled = true;
    btnText.textContent = 'Finalizing Workspace...';
    btnSpinner.style.display = 'inline-block';

    const formData = new FormData(form);

    fetch("{{ route('service_company.account.onboarding.save') }}", {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: formData
    })
    .then(async response => {
      const data = await response.json();
      if (response.ok && data.status === 'success') {
        window.location.href = data.redirect_url || "{{ url('service_company/dashboard') }}";
      } else {
        btnSubmit.disabled = false;
        btnSpinner.style.display = 'none';
        btnText.textContent = 'Complete Setup & Enter Dashboard →';
        if (data.errors) {
          if (data.errors.company_name) {
            nameInput.classList.add('is-invalid');
            document.getElementById('err_company_name').textContent = data.errors.company_name[0];
            document.getElementById('err_company_name').style.display = 'block';
          }
          if (data.errors.logo) {
            errLogo.textContent = data.errors.logo[0];
            errLogo.style.display = 'block';
          }
        } else {
          alert(data.message || 'An error occurred while saving profile. Please try again.');
        }
      }
    })
    .catch(err => {
      btnSubmit.disabled = false;
      btnSpinner.style.display = 'none';
      btnText.textContent = 'Complete Setup & Enter Dashboard →';
      alert('Network error. Please try again.');
    });
  });
});
</script>
@endsection
