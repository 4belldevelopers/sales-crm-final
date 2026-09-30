/**
 * JERO Enterprise Mobile Number Box Engine (V6.0)
 * 100% Client-First User RAM Execution | Zero Server Strain | Anti-Clipping Portal
 * WCAG 2.1 AA Compliant | Metronic 15.6" Screen Fit | Zero Feature Conflict
 */
(function (window, document) {
  'use strict';

  // Singleton Portal Dropdown
  const Portal = {
    el: null,
    searchEl: null,
    listEl: null,
    activeInstance: null,
    selectedIndex: -1,

    init() {
      if (this.el) return;

      this.el = document.createElement('div');
      this.el.className = 'jero-phone-portal-menu card shadow-lg border';
      this.el.id = 'jero_phone_portal_root';
      this.el.setAttribute('role', 'region');
      this.el.setAttribute('aria-label', 'Country Selector');
      this.el.style.cssText = [
        'position: fixed',
        'z-index: 99999',
        'display: none',
        'width: 340px',
        'max-height: 360px',
        'overflow: hidden',
        'font-family: Poppins, Helvetica, sans-serif',
        'border-radius: 0.625rem',
        'box-shadow: 0 10px 30px 0 rgba(0,0,0,0.18)',
        'background: #ffffff',
        'border: 1px solid #dce3ee'
      ].join(';');

      this.el.innerHTML = `
        <div class="p-2 border-bottom bg-light d-flex align-items-center" style="border-top-left-radius: 0.625rem; border-top-right-radius: 0.625rem;">
          <i class="fa fa-search text-gray-500 ms-2 me-2 fs-7" aria-hidden="true"></i>
          <input type="text" class="form-control form-control-sm form-control-solid fs-7 border-0 bg-white" placeholder="Search country or code (e.g. India, +91)..." id="jero_phone_search" autocomplete="off" spellcheck="false" style="outline: none; box-shadow: none;">
        </div>
        <div class="jero-country-list py-1" id="jero_country_list" style="max-height: 300px; overflow-y: auto; scrollbar-width: thin;" role="listbox"></div>
      `;

      document.body.appendChild(this.el);
      this.searchEl = document.getElementById('jero_phone_search');
      this.listEl = document.getElementById('jero_country_list');

      this.attachGlobalEvents();
    },

    toggle(instance) {
      this.init();
      if (this.activeInstance === instance && this.el.style.display === 'block') {
        this.close();
        return;
      }
      this.activeInstance = instance;
      this.selectedIndex = -1;
      this.searchEl.value = '';
      this.renderCountries();
      this.position();
      this.el.style.display = 'block';
      this.activeInstance.btn.setAttribute('aria-expanded', 'true');
      setTimeout(() => {
        if (this.searchEl) this.searchEl.focus();
      }, 30);
    },

    position() {
      if (!this.activeInstance || !this.el) return;
      const rect = this.activeInstance.btn.getBoundingClientRect();
      const dropdownHeight = 350;
      const spaceBelow = window.innerHeight - rect.bottom;

      let top;
      if (spaceBelow < dropdownHeight && rect.top > dropdownHeight) {
        // Collision-Aware Flip upwards on 15.6" screens or bottom-drawer positioning
        top = rect.top - dropdownHeight - 4;
      } else {
        top = rect.bottom + 4;
      }
      let left = Math.max(8, rect.left);
      if (left + 340 > window.innerWidth) {
        left = Math.max(8, window.innerWidth - 348);
      }

      this.el.style.position = 'fixed';
      this.el.style.top = `${top}px`;
      this.el.style.left = `${left}px`;
      this.el.style.zIndex = '99999';
    },

    close() {
      if (this.el) {
        this.el.style.display = 'none';
      }
      if (this.activeInstance) {
        this.activeInstance.btn.setAttribute('aria-expanded', 'false');
        this.activeInstance = null;
      }
    },

    renderCountries(query = '') {
      if (!this.listEl || !this.activeInstance) return;

      const q = query.trim().toLowerCase();
      const qDigits = q.replace(/[^\d]/g, '');
      const countries = this.activeInstance.countries;

      const filtered = countries.filter(c => {
        if (!q) return true;
        const nameMatch = c.name.toLowerCase().includes(q);
        const isoMatch = c.iso.toLowerCase() === q || c.iso.toLowerCase().startsWith(q);
        const dialMatch = c.dial.includes(q) || (qDigits && c.dial.replace(/[^\d]/g, '').includes(qDigits));
        return nameMatch || isoMatch || dialMatch;
      });

      if (filtered.length === 0) {
        this.listEl.innerHTML = `<div class="p-4 text-center text-muted fs-8">No country matching "${query}"</div>`;
        return;
      }

      let html = '';
      filtered.forEach((c, index) => {
        const isSelected = this.activeInstance.currentCountry && this.activeInstance.currentCountry.iso === c.iso;
        html += `
          <div class="d-flex align-items-center justify-content-between px-3 py-2 cursor-pointer jero-country-item ${isSelected ? 'bg-light-primary text-primary fw-bold' : 'text-gray-800'}" 
               data-dial="${c.dial}" 
               data-iso="${c.iso}" 
               data-index="${index}"
               role="option"
               aria-selected="${isSelected ? 'true' : 'false'}"
               style="font-size: 13px; border-bottom: 1px solid #f4f4f7; transition: background-color 0.15s ease;">
            <div class="d-flex align-items-center text-truncate me-2">
              <span class="me-2 fs-5" style="line-height: 1;">${c.flag}</span>
              <span class="text-truncate">${c.name}</span>
            </div>
            <div class="d-flex align-items-center flex-shrink-0">
              <span class="text-muted fs-8 me-2">(${c.length} digits)</span>
              <span class="badge bg-light text-dark fs-8 fw-bold px-2 py-1">${c.dial}</span>
            </div>
          </div>
        `;
      });
      this.listEl.innerHTML = html;

      // Click binding
      this.listEl.querySelectorAll('.jero-country-item').forEach(item => {
        item.addEventListener('click', () => {
          const dial = item.getAttribute('data-dial');
          this.activeInstance.selectCountryByDial(dial);
          this.close();
        });
      });
    },

    attachGlobalEvents() {
      // Outside click to close
      document.addEventListener('click', (e) => {
        if (this.el && this.el.style.display === 'block') {
          if (!this.el.contains(e.target) && !e.target.closest('.jero-country-picker-btn')) {
            this.close();
          }
        }
      });

      // Search typing
      this.searchEl.addEventListener('input', (e) => {
        this.renderCountries(e.target.value);
      });

      // Keyboard navigation
      this.searchEl.addEventListener('keydown', (e) => {
        const items = this.listEl.querySelectorAll('.jero-country-item');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
          e.preventDefault();
          this.selectedIndex = (this.selectedIndex + 1) % items.length;
          this.highlightItem(items);
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          this.selectedIndex = (this.selectedIndex - 1 + items.length) % items.length;
          this.highlightItem(items);
        } else if (e.key === 'Enter') {
          e.preventDefault();
          if (this.selectedIndex >= 0 && items[this.selectedIndex]) {
            items[this.selectedIndex].click();
          } else if (items.length > 0) {
            items[0].click();
          }
        } else if (e.key === 'Escape') {
          e.preventDefault();
          this.close();
        }
      });

      window.addEventListener('resize', () => {
        if (this.el && this.el.style.display === 'block') this.position();
      });

      window.addEventListener('scroll', () => {
        if (this.el && this.el.style.display === 'block') this.position();
      }, true);
    },

    highlightItem(items) {
      items.forEach((item, idx) => {
        if (idx === this.selectedIndex) {
          item.classList.add('bg-light-primary');
          item.scrollIntoView({ block: 'nearest' });
        } else {
          item.classList.remove('bg-light-primary');
        }
      });
    }
  };

  // Main Component Class
  class JeroPhoneBox {
    constructor(wrapper) {
      this.wrapper = typeof wrapper === 'string' ? document.getElementById(wrapper) : wrapper;
      if (!this.wrapper || this.wrapper._jeroPhoneBoxInstance) return;

      const fieldId = this.wrapper.getAttribute('data-field-id') || 'phone';
      this.input = document.getElementById(fieldId) || this.wrapper.querySelector('input[type="tel"]') || this.wrapper.querySelector('input[name="' + fieldId + '"]');
      this.hiddenCode = this.wrapper.querySelector('.jero-phone-country-code-input') ||
                        document.getElementById(`${fieldId}_country_code`) ||
                        this.wrapper.querySelector('input[type="hidden"]');
      this.btn = this.wrapper.querySelector('.jero-country-picker-btn') || document.getElementById(`${fieldId}_btn`);
      this.flagSpan = this.wrapper.querySelector('.jero-selected-flag') || document.getElementById(`${fieldId}_flag`);
      this.dialSpan = this.wrapper.querySelector('.jero-selected-dial') || document.getElementById(`${fieldId}_dial`);
      this.counter = this.wrapper.querySelector('.jero-digit-counter') || document.getElementById(`${fieldId}_counter`);
      this.hint = this.wrapper.querySelector('.jero-phone-feedback') || document.getElementById(`${fieldId}_hint`);

      if (!this.input || !this.hiddenCode || !this.btn) {
        console.warn('[JeroPhoneBox] Incomplete elements for wrapper:', this.wrapper);
        return;
      }

      this.countries = window.__JERO_COUNTRY_PHONE_DATA__ || [];
      this.currentCountry = null;

      this.wrapper._jeroPhoneBoxInstance = this;
      this.init();
    }

    init() {
      const defaultIso = (this.wrapper.getAttribute('data-default-iso') || 'IN').toUpperCase();
      let initialDial = (this.hiddenCode.value || '').trim();

      // Check if value is ISO2 code (e.g. "in", "us", "+in")
      const stripped = initialDial.replace('+', '').toUpperCase();
      if (stripped.length === 2 && /^[A-Z]{2}$/.test(stripped)) {
        const countryByIso = this.countries.find(c => c.iso === stripped);
        if (countryByIso) {
          this.selectCountry(countryByIso);
          this.bindEvents();
          this.updateCounter();
          return;
        }
      }

      if (initialDial && !initialDial.startsWith('+')) {
        initialDial = '+' + initialDial;
        this.hiddenCode.value = initialDial;
      }

      if (initialDial) {
        this.selectCountryByDial(initialDial);
      } else {
        const hqCountry = this.countries.find(c => c.iso === defaultIso) || this.countries.find(c => c.iso === 'IN');
        if (hqCountry) this.selectCountry(hqCountry);
      }

      this.bindEvents();
      this.updateCounter();
    }

    selectCountry(country) {
      if (!country) return;
      this.currentCountry = country;
      this.hiddenCode.value = country.dial;
      if (this.flagSpan) this.flagSpan.textContent = country.flag;
      if (this.dialSpan) this.dialSpan.textContent = country.dial;

      // Update digit limit
      this.input.setAttribute('maxlength', country.length);

      // Trigger change on hidden input for form validation
      this.hiddenCode.dispatchEvent(new Event('change', { bubbles: true }));

      // Clean existing digits if they exceed new country limit
      const currentDigits = this.input.value.replace(/[^\d]/g, '');
      if (currentDigits.length > country.length) {
        this.input.value = currentDigits.substring(0, country.length);
      }
      this.updateCounter();
    }

    selectCountryByDial(dialCode) {
      if (!dialCode) return;
      const cleanDial = dialCode.startsWith('+') ? dialCode : '+' + dialCode;
      const defaultIso = (this.wrapper.getAttribute('data-default-iso') || 'IN').toUpperCase();
      const country = this.countries.find(c => c.dial === cleanDial && c.iso === defaultIso) ||
                      this.countries.find(c => c.dial === cleanDial) ||
                      this.countries.find(c => c.iso === 'IN');
      if (country) this.selectCountry(country);
    }

    bindEvents() {
      // Keystroke input
      this.input.addEventListener('input', (e) => {
        const raw = e.target.value;
        const clean = raw.replace(/[^\d]/g, '');
        if (raw !== clean) {
          e.target.value = clean;
        }
        this.updateCounter();
      });

      // Smart paste handling
      this.input.addEventListener('paste', (e) => {
        e.preventDefault();
        const text = (e.clipboardData || window.clipboardData).getData('text') || '';
        this.handlePaste(text);
      });

      // Toggle portal dropdown
      this.btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        Portal.toggle(this);
      });
    }

    handlePaste(rawText) {
      if (!rawText) return;
      const trimmed = rawText.trim();

      // Check if string contains an international dial code (e.g. +1, +91, +971)
      for (const country of this.countries) {
        if (trimmed.startsWith(country.dial)) {
          this.selectCountry(country);
          const digits = trimmed.substring(country.dial.length).replace(/[^\d]/g, '');
          this.input.value = digits.substring(0, country.length);
          this.updateCounter();
          this.input.dispatchEvent(new Event('input', { bubbles: true }));
          return;
        }
      }

      // Check for leading 0
      let cleanDigits = trimmed.replace(/[^\d]/g, '');
      if (cleanDigits.startsWith('0') && this.currentCountry && cleanDigits.length > this.currentCountry.length) {
        cleanDigits = cleanDigits.substring(1);
      }

      const limit = this.currentCountry ? this.currentCountry.length : 15;
      this.input.value = cleanDigits.substring(0, limit);
      this.updateCounter();
      this.input.dispatchEvent(new Event('input', { bubbles: true }));
    }

    updateCounter() {
      if (!this.counter) return;

      const currentLength = (this.input.value || '').length;
      const targetLength = this.currentCountry ? this.currentCountry.length : 10;

      this.counter.textContent = `${currentLength}/${targetLength}`;

      if (currentLength === 0) {
        this.counter.className = 'position-absolute end-0 me-3 badge bg-light text-muted fs-8 fw-semibold jero-digit-counter';
      } else if (currentLength === targetLength) {
        this.counter.className = 'position-absolute end-0 me-3 badge bg-light-success text-success fs-8 fw-bold jero-digit-counter';
        this.counter.innerHTML = `${currentLength}/${targetLength} <i class="fa-solid fa-check fs-9 text-success ms-1"></i>`;
      } else {
        this.counter.className = 'position-absolute end-0 me-3 badge bg-light-warning text-warning fs-8 fw-semibold jero-digit-counter';
      }
    }
  }

  // Global API
  window.JeroPhoneBox = {
    init(wrapperOrId) {
      const el = typeof wrapperOrId === 'string' ? document.getElementById(wrapperOrId) : wrapperOrId;
      if (el) new JeroPhoneBox(el);
    },
    initAll() {
      document.querySelectorAll('.jero-phone-box-wrapper').forEach(wrapper => {
        new JeroPhoneBox(wrapper);
      });
    },
    getInstance(fieldId) {
      const wrapper = document.getElementById(`${fieldId}_wrapper`);
      return wrapper ? wrapper._jeroPhoneBoxInstance : null;
    },
    toggleDropdown(fieldId) {
      const wrapper = document.getElementById(`${fieldId}_wrapper`) || document.querySelector(`[data-field-id="${fieldId}"]`);
      if (wrapper) {
        if (!wrapper._jeroPhoneBoxInstance) {
          new JeroPhoneBox(wrapper);
        }
        if (wrapper._jeroPhoneBoxInstance) {
          Portal.toggle(wrapper._jeroPhoneBoxInstance);
        }
      }
    }
  };

  // Delegated Click for Dropdown Toggle (Universal fallback)
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.jero-country-picker-btn');
    if (btn) {
      e.preventDefault();
      e.stopPropagation();
      const wrapper = btn.closest('.jero-phone-box-wrapper');
      if (wrapper) {
        if (!wrapper._jeroPhoneBoxInstance) {
          new JeroPhoneBox(wrapper);
        }
        if (wrapper._jeroPhoneBoxInstance) {
          Portal.toggle(wrapper._jeroPhoneBoxInstance);
        }
      }
    }
  });

  // Auto-init on DOMContentLoaded
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => window.JeroPhoneBox.initAll());
  } else {
    window.JeroPhoneBox.initAll();
  }

})(window, document);
