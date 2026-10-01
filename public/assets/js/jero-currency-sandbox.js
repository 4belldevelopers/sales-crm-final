/**
 * ============================================================================
 * JERO ATS & TALENT MARKETPLACE - GLOBAL CURRENCY CLIENT SANDBOX (v4.1)
 * ============================================================================
 * Architectural Directives:
 *  - Rule 7: Zero business logic mutations & zero module regressions
 *  - Rule 14: Metronic UI standards, max 14px Poppins, 1366x768 responsive
 *  - Rule 17: Complete page/blade isolation, zero prototype pollution
 *  - Zero-Crash: Bounded LRU Cache (<50KB), universal circuit breakers, RAF batching
 * ============================================================================
 */
(function (window, document) {
    'use strict';

    if (window.JeroCurrency) {
        return; // Idempotent guard: never initialize twice
    }

    // 1. BOUNDED LRU CACHE (Strictly capped at 200 entries, <50 KB User RAM ceiling)
    class BoundedLRU {
        constructor(maxEntries = 200) {
            this.maxEntries = maxEntries;
            this.map = new Map();
        }
        get(key) {
            if (!this.map.has(key)) return null;
            const val = this.map.get(key);
            this.map.delete(key);
            this.map.set(key, val);
            return val;
        }
        set(key, val) {
            if (this.map.has(key)) {
                this.map.delete(key);
            } else if (this.map.size >= this.maxEntries) {
                const oldestKey = this.map.keys().next().value;
                this.map.delete(oldestKey);
            }
            this.map.set(key, val);
        }
        clear() {
            this.map.clear();
        }
    }

    // 2. PRIVATE MODULE STATE (Completely sealed in IIFE closure)
    const _lruCache = new BoundedLRU(200);
    const _domElementState = new WeakMap();
    const _abortController = new AbortController();
    let _activeRates = window.__JERO_FX_RATES__ || { USD: 1.0, INR: 96.06, EUR: 0.88, GBP: 0.76, AED: 3.67 };
    let _snapshotVersion = window.__JERO_FX_VERSION__ || '202609-001';
    let _activeCurrency = window.__JERO_USER_CURRENCY__ || (localStorage.getItem('jero_user_currency') || 'USD');
    let _isUpdating = false;

    // Currency Formatting Configuration
    const CURRENCY_SYMBOLS = {
        USD: '$', INR: '₹', EUR: '€', GBP: '£', AED: 'AED ', CAD: 'C$',
        AUD: 'A$', SGD: 'S$', JPY: '¥', CHF: 'CHF ', CNY: '¥', SAR: 'SAR ',
        QAR: 'QAR ', KWD: 'KD ', BHD: 'BD ', OMR: 'RO ', EGP: 'E£ ',
        ILS: '₪', SEK: 'kr ', NOK: 'kr ', DKK: 'kr ', PLN: 'zł ',
        CZK: 'Kč ', HUF: 'Ft ', RON: 'lei ', BGN: 'лв ', TRY: '₺ ',
        ZAR: 'R ', BRL: 'R$ ', MXN: 'Mex$ ', CLP: 'CLP$ ', COP: 'COL$ ',
        ARS: 'AR$ ', NGN: '₦ ', KES: 'KSh ', PKR: '₨ ', BDT: '৳ ',
        LKR: 'Rs ', NPR: 'रू '
    };

    /**
     * Defensive Multi-Currency Math Converter
     * Direct USD cross-rate formula: (Amount / RateFromUSD) * RateToUSD
     */
    function safeConvert(amount, fromCurr, toCurr) {
        try {
            if (amount === null || amount === undefined || isNaN(amount)) return null;
            const num = parseFloat(amount);
            if (!isFinite(num)) return null;

            const from = (fromCurr || 'USD').toUpperCase();
            const to = (toCurr || _activeCurrency).toUpperCase();
            if (from === to) return num;

            const cacheKey = `${from}_${to}_${num}_${_snapshotVersion}`;
            const cached = _lruCache.get(cacheKey);
            if (cached !== null) return cached;

            const rateFrom = _activeRates[from] || 1.0;
            const rateTo = _activeRates[to] || 1.0;
            if (rateFrom <= 0 || rateTo <= 0) return num;

            const result = (num / rateFrom) * rateTo;
            _lruCache.set(cacheKey, result);
            return result;
        } catch (e) {
            console.warn('[JeroCurrency:SafeConvert] Circuit breaker fallback engaged:', e);
            return typeof amount === 'number' ? amount : null;
        }
    }

    /**
     * Defensive Formatter (Poppins 14px Metronic standard)
     */
    function safeFormat(amount, currencyCode, options = {}) {
        try {
            if (amount === null || amount === undefined || isNaN(amount)) return 'N/A';
            const curr = (currencyCode || _activeCurrency).toUpperCase();
            const symbol = CURRENCY_SYMBOLS[curr] || (curr + ' ');
            const zeroDecimalCurrencies = ['JPY', 'KRW', 'VND', 'CLP'];
            const decimals = options.decimals !== undefined ? options.decimals : (zeroDecimalCurrencies.includes(curr) ? 0 : 2);

            const num = parseFloat(amount);
            const formattedNum = num.toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });

            return `${symbol}${formattedNum}`;
        } catch (e) {
            console.warn('[JeroCurrency:SafeFormat] Circuit breaker fallback engaged:', e);
            return `${currencyCode || 'USD'} ${amount}`;
        }
    }

    /**
     * Format a compact range string (e.g., "$ 50K - 80K / Annum")
     */
    function safeFormatRange(minAmount, maxAmount, fromCurr, toCurr, modeSuffix = '') {
        try {
            const targetCurr = (toCurr || _activeCurrency).toUpperCase();
            const convMin = safeConvert(minAmount, fromCurr, targetCurr);
            const convMax = safeConvert(maxAmount, fromCurr, targetCurr);

            const sym = CURRENCY_SYMBOLS[targetCurr] || (targetCurr + ' ');

            const formatCompact = (val) => {
                if (!val || isNaN(val)) return '0';
                if (val >= 1000000) return (val / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
                if (val >= 1000) return (val / 1000).toFixed(0) + 'K';
                return val.toFixed(0);
            };

            let rangeText = '';
            if (convMin > 0 && convMax > 0) {
                rangeText = `${sym}${formatCompact(convMin)} - ${sym}${formatCompact(convMax)}`;
            } else if (convMin > 0) {
                rangeText = `${sym}${formatCompact(convMin)}`;
            } else if (convMax > 0) {
                rangeText = `${sym}${formatCompact(convMax)}`;
            } else {
                return '';
            }

            return modeSuffix ? `${rangeText} ${modeSuffix}` : rangeText;
        } catch (e) {
            console.warn('[JeroCurrency:SafeFormatRange] Fallback engaged:', e);
            return '';
        }
    }

    /**
     * Non-Blocking 60 FPS DOM Micro-Batcher via requestAnimationFrame
     * Yields execution every 15 elements or 6ms to maintain 60 FPS frame rate
     */
    function safeBatchUpdateDOM(containerNode, targetCurrency) {
        if (_isUpdating) return;
        _isUpdating = true;

        try {
            const root = containerNode || document;
            const elements = root.querySelectorAll('[data-jero-currency-amount]');
            if (!elements || elements.length === 0) {
                _isUpdating = false;
                return;
            }

            const targetCurr = (targetCurrency || _activeCurrency).toUpperCase();
            let index = 0;

            function processFrame() {
                const startTime = performance.now();
                while (index < elements.length && (performance.now() - startTime) < 6) {
                    const el = elements[index++];
                    if (!el.isConnected) continue; // Skip detached DOM nodes

                    const rawAmount = el.getAttribute('data-jero-currency-amount');
                    const origCurr = (el.getAttribute('data-jero-currency-original') || 'USD').toUpperCase();
                    const maxAmount = el.getAttribute('data-jero-currency-max');
                    const mode = el.getAttribute('data-jero-currency-mode') || '';

                    const effectiveCurr = targetCurr === 'ORIGINAL' ? origCurr : targetCurr;

                    let formattedText = '';
                    if (maxAmount && parseFloat(maxAmount) > 0) {
                        formattedText = safeFormatRange(rawAmount, maxAmount, origCurr, effectiveCurr, mode);
                    } else {
                        const converted = safeConvert(rawAmount, origCurr, effectiveCurr);
                        formattedText = safeFormat(converted, effectiveCurr);
                        if (mode) formattedText += ' ' + mode;
                    }

                    // Mutate leaf text only if changed (prevents layout thrashing)
                    if (formattedText && el.textContent.trim() !== formattedText.trim()) {
                        el.textContent = formattedText;
                    }
                }

                if (index < elements.length) {
                    requestAnimationFrame(processFrame);
                } else {
                    _isUpdating = false;
                }
            }

            requestAnimationFrame(processFrame);
        } catch (e) {
            _isUpdating = false;
            console.error('[JeroCurrency:BatchUpdate] Execution exception caught:', e);
        }
    }

    /**
     * Teardown & Lifecycle Cleanup
     */
    function safeDestroy() {
        try {
            _abortController.abort();
            _lruCache.clear();
            _isUpdating = false;
        } catch (e) {
            // Silently complete cleanup
        }
    }

    // 3. EXPOSE SEALED & FROZEN PUBLIC API (Rule 17 Sandbox)
    window.JeroCurrency = Object.freeze({
        convert: safeConvert,
        format: safeFormat,
        formatRange: safeFormatRange,
        convertAndFormat: function (amt, from, to, opt) {
            const conv = safeConvert(amt, from, to);
            return safeFormat(conv, to, opt);
        },
        getCurrentCurrency: function () {
            return _activeCurrency;
        },
        setCurrency: function (curr, containerNode) {
            if (!curr || typeof curr !== 'string') return;
            _activeCurrency = curr.toUpperCase();
            try {
                localStorage.setItem('jero_user_currency', _activeCurrency);
            } catch (e) {}
            safeBatchUpdateDOM(containerNode, _activeCurrency);
        },
        setRates: function (rates, version) {
            if (rates && typeof rates === 'object') {
                _activeRates = rates;
                if (version) _snapshotVersion = version;
                _lruCache.clear();
            }
        },
        batchUpdateDOM: safeBatchUpdateDOM,
        destroy: safeDestroy
    });

})(window, document);
