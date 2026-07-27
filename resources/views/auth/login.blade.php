@extends('layouts.metronic_front_app')
<title>Jero Works — AI Recruitment Platform | Sign In</title>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    :root {
        --c-blue: #2563eb;
        --c-blue-dark: #1d4ed8;
        --c-blue-light: #eff6ff;
        --c-blue-ring: rgba(37, 99, 235, .12);
        --c-orange: #f97316;
        --c-green: #22c55e;
        --c-indigo: #4f46e5;
        --c-purple: #7c3aed;
        --c-900: #0f172a;
        --c-800: #1e293b;
        --c-700: #334155;
        --c-600: #475569;
        --c-500: #64748b;
        --c-400: #94a3b8;
        --c-300: #cbd5e1;
        --c-200: #e2e8f0;
        --c-100: #f1f5f9;
        --c-50: #f8fafc;
        --c-white: #ffffff;
        --t: all .22s cubic-bezier(.4, 0, .2, 1);
        --font: 'Inter', 'Poppins', -apple-system, sans-serif;
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0
    }

    #kt_body,
    body {
        background: #ffffff !important;
        min-height: 100vh;
        font-family: var(--font)
    }

    .d-flex.flex-column.flex-root {
        min-height: 100vh
    }

    .d-flex.flex-column.flex-lg-row.flex-column-fluid {
        min-height: 100vh;
        align-items: stretch
    }

    /* ── Page shell: premium soft white background ── */
    .jwp {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        width: 100%;
        font-family: var(--font);
        position: relative;
        overflow: hidden;
        /* Pure white base — glows are layered on top via blobs */
        background: #ffffff;
    }

    /* ── BLOB 1: Large warm peach/orange glow — center-left ── */
    .jwp-blob-tl {
        position: absolute;
        top: 38%;
        left: 2%;
        width: 650px;
        height: 550px;
        border-radius: 50%;
        background: radial-gradient(circle,
                rgba(253, 186, 116, .22) 0%,
                rgba(251, 146, 60, .14) 30%,
                rgba(249, 115, 22, .07) 55%,
                transparent 72%);
        filter: blur(75px);
        pointer-events: none;
        z-index: 0;
        transform: translateY(-50%);
    }

    /* ── BLOB 2: Purple/violet glow — top-left corner ── */
    .jwp-blob-tr {
        position: absolute;
        top: -60px;
        left: -60px;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        background: radial-gradient(circle,
                rgba(167, 139, 250, .14) 0%,
                rgba(124, 58, 237, .07) 40%,
                transparent 68%);
        filter: blur(60px);
        pointer-events: none;
        z-index: 0;
    }

    /* ── BLOB 3: Soft blue glow — top-right (behind dot area) ── */
    .jwp-blob-br {
        position: absolute;
        top: -40px;
        right: -40px;
        width: 380px;
        height: 380px;
        border-radius: 50%;
        background: radial-gradient(circle,
                rgba(147, 197, 253, .12) 0%,
                rgba(59, 130, 246, .06) 45%,
                transparent 68%);
        filter: blur(55px);
        pointer-events: none;
        z-index: 0;
    }

    /* ── BLOB 4: Warm orange — bottom-left corner ── */
    .jwp-blob-bl {
        position: absolute;
        bottom: -80px;
        left: -80px;
        width: 440px;
        height: 440px;
        border-radius: 50%;
        background: radial-gradient(circle,
                rgba(253, 186, 116, .12) 0%,
                rgba(249, 115, 22, .06) 45%,
                transparent 68%);
        filter: blur(58px);
        pointer-events: none;
        z-index: 0;
    }

    /* ── BLOB 5: Purple — bottom-right accent ── */
    .jwp-blob-c {
        position: absolute;
        bottom: -60px;
        right: 15%;
        width: 340px;
        height: 340px;
        border-radius: 50%;
        background: radial-gradient(circle,
                rgba(196, 181, 253, .10) 0%,
                rgba(124, 58, 237, .05) 50%,
                transparent 68%);
        filter: blur(50px);
        pointer-events: none;
        z-index: 0;
    }

    /* ── Dot grid — top-right, pink/salmon dots ── */
    .jwp-dots {
        position: absolute;
        top: 0;
        right: 0;
        width: 420px;
        height: 400px;
        pointer-events: none;
        z-index: 1;
        background-image: radial-gradient(circle, rgba(251, 113, 133, .55) 1.5px, transparent 1.5px);
        background-size: 22px 22px;
        -webkit-mask-image: radial-gradient(ellipse 85% 85% at 100% 0%, black 25%, transparent 75%);
        mask-image: radial-gradient(ellipse 85% 85% at 100% 0%, black 25%, transparent 75%);
    }

    /* ── Second dot cluster — bottom-left, pink subtle ── */
    .jwp-dots-bl {
        position: absolute;
        bottom: 40px;
        left: 0;
        width: 200px;
        height: 180px;
        pointer-events: none;
        z-index: 1;
        background-image: radial-gradient(circle, rgba(251, 113, 133, .3) 1.5px, transparent 1.5px);
        background-size: 22px 22px;
        -webkit-mask-image: radial-gradient(ellipse 80% 80% at 0% 100%, black 15%, transparent 70%);
        mask-image: radial-gradient(ellipse 80% 80% at 0% 100%, black 15%, transparent 70%);
    }

    /* ── Curved swoosh arc — bottom-left warm peach stroke ── */
    .jwp-arc {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 62%;
        height: 80%;
        pointer-events: none;
        z-index: 1;
        overflow: visible;
    }
    .jwp-arc svg {
        width: 100%;
        height: 100%;
        overflow: visible;
    }

    .jwp-body {
        display: flex;
        flex: 1 1 auto;
        min-height: 0;
        position: relative;
        z-index: 2
    }

    .jwp-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 44px;
        height: 62px;
        flex-shrink: 0;
        position: relative;
        z-index: 20;
        /* background: rgba(255, 255, 255, .80); */
        /* backdrop-filter: blur(18px); */
        /* -webkit-backdrop-filter: blur(18px); */
        /* border-bottom: 1px solid rgba(203, 213, 225, .28) */
    }

    .jwp-logo {
        display: inline-flex;
        align-items: center;
        gap: 11px;
        text-decoration: none
    }

    .jwp-logo-icon {
        width: 42px;
        height: 42px;
        background: var(--c-900);
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .25)
    }

    .jwp-logo-icon svg {
        width: 25px;
        height: 25px
    }

    .jwp-logo-text {
        display: flex;
        flex-direction: column;
        line-height: 1
    }

    .jwp-logo-name {
        font-size: 19px;
        font-weight: 800;
        color: var(--c-900);
        letter-spacing: -.4px;
        font-family: var(--font)
    }

    .jwp-logo-sub {
        font-size: 10px;
        font-weight: 500;
        color: var(--c-500);
        margin-top: 2px;
        font-family: var(--font)
    }

    .jwp-reg {
        display: flex;
        align-items: center;
        gap: 12px
    }

    .jwp-reg-hint {
        font-size: 13px;
        font-weight: 500;
        color: var(--c-500);
        font-family: var(--font)
    }

    .jwp-reg-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 20px;
        border: 1.5px solid var(--c-blue);
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        color: var(--c-blue);
        background: transparent;
        text-decoration: none;
        font-family: var(--font);
        transition: var(--t);
        white-space: nowrap
    }

    .jwp-reg-btn:hover {
        background: var(--c-blue);
        color: var(--c-white);
        box-shadow: 0 4px 14px rgba(37, 99, 235, .3);
        transform: translateY(-1px)
    }

    .jwp-reg-btn svg {
        width: 13px;
        height: 13px;
        transition: transform .2s ease
    }

    .jwp-reg-btn:hover svg {
        transform: translateX(3px)
    }

    /* ── LEFT panel: fully transparent — page blobs show through ── */
    .jwp-left {
        flex: 1 1 0;
        min-width: 0;
        background: transparent;
        display: flex;
        flex-direction: column;
        padding: 0 52px;
        position: relative;
        overflow: hidden
    }

    .jwp-left::before {
        content: none
    }

    .jwp-left::after {
        content: none
    }

    .jwp-left-inner {
        position: relative;
        z-index: 2;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 16px 0 12px
    }

    /* Hero */
    .jwp-hero {
        margin-bottom: 16px;
        animation: aFadeUp .6s .05s both
    }

    .jwp-hero h1 {
        font-size: clamp(30px, 3.2vw, 46px);
        font-weight: 900;
        color: var(--c-900);
        line-height: 1.05;
        letter-spacing: -2px;
        margin-bottom: 10px;
        font-family: var(--font)
    }

    .jwp-hero h1 .hl-orange {
        color: var(--c-orange)
    }

    .jwp-hero p {
        font-size: 13px;
        color: var(--c-500);
        line-height: 1.65;
        max-width: 400px;
        font-family: var(--font)
    }

    /* Cards — no border, shadow only */
    .jwp-cards {
        display: flex;
        gap: 12px;
        margin-bottom: 14px;
        animation: aFadeUp .6s .14s both
    }

    .jwp-fc {
        flex: 1 1 0;
        min-width: 0;
        background: var(--c-white);
        border: none;
        border-radius: 16px;
        padding: 14px 13px 0;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .09), 0 1px 4px rgba(15, 23, 42, .05);
        overflow: hidden;
        cursor: default;
        transition: transform .28s cubic-bezier(.4, 0, .2, 1), box-shadow .28s ease;
        position: relative
    }

    .jwp-fc:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 48px rgba(15, 23, 42, .13), 0 4px 16px rgba(15, 23, 42, .07)
    }

    .jwp-fc-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .14)
    }

    .badge-b {
        background: linear-gradient(135deg, #3b82f6, #2563eb)
    }

    .badge-g {
        background: linear-gradient(135deg, #22c55e, #16a34a)
    }

    .badge-p {
        background: linear-gradient(135deg, #a855f7, #7c3aed)
    }

    .jwp-fc-badge svg {
        width: 16px;
        height: 16px;
        stroke: #fff;
        stroke-width: 2;
        fill: none
    }

    .jwp-fc-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 8px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        border-radius: 100px;
        padding: 2px 9px;
        width: fit-content;
        margin-bottom: 8px;
        font-family: var(--font)
    }

    .chip-b {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe
    }

    .chip-g {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0
    }

    .chip-p {
        background: #f5f3ff;
        color: #7c3aed;
        border: 1px solid #ddd6fe
    }

    .jwp-fc h3 {
        font-size: 13px;
        font-weight: 800;
        color: var(--c-900);
        line-height: 1.25;
        margin-bottom: 5px;
        font-family: var(--font)
    }

    .jwp-fc p {
        font-size: 11px;
        color: var(--c-500);
        line-height: 1.55;
        flex: 1;
        font-family: var(--font)
    }

    .jwp-fc-art {
        height: 118px;
        margin-top: 6px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        position: relative;
        overflow: hidden
    }

    .jwp-fc-art svg {
        width: 100%;
        height: 100%
    }

    /* Stats — no border, transparent background */
    .jwp-stats {
        display: flex;
        gap: 0;
        animation: aFadeUp .6s .22s both;
        background: transparent;
        border: none;
        border-radius: 0;
        overflow: visible;
        box-shadow: none
    }

    .jwp-stat {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        padding: 10px 0;
        border-right: none;
        transition: none
    }

    .jwp-stat:last-child {
        border-right: none
    }

    .jwp-stat:hover {
        background: transparent
    }

    .jwp-stat-ico {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0
    }

    .sic-b {
        background: #eff6ff;
        color: #2563eb
    }

    .sic-g {
        background: #f0fdf4;
        color: #16a34a
    }

    .sic-p {
        background: #f5f3ff;
        color: #7c3aed
    }

    .sic-o {
        background: #fff7ed;
        color: #ea580c
    }

    .jwp-stat-ico svg {
        width: 17px;
        height: 17px
    }

    .jwp-stat-num {
        font-size: 17px;
        font-weight: 800;
        color: var(--c-900);
        line-height: 1;
        font-family: var(--font);
        letter-spacing: -.5px
    }

    .jwp-stat-lbl {
        font-size: 10px;
        color: var(--c-400);
        font-weight: 500;
        margin-top: 2px;
        font-family: var(--font)
    }

    .jwp-right {
        width: 500px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px 28px;
        position: relative;
        /* white glass panel — blobs from page show through subtly */
        background: rgba(255, 255, 255, .72);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        /* border-left: 1px solid rgba(203, 213, 225, .22); */
    }

    .jwp-right::before {
        content: none
    }

    .jwp-right::after {
        content: none
    }

    .jwf {
        background: var(--c-white);
        border-radius: 22px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .06), 0 8px 40px rgba(15, 23, 42, .10), 0 1px 3px rgba(15, 23, 42, .04);
        padding: 36px 36px 28px;
        width: 100%;
        max-width: 460px;
        position: relative;
        animation: aFadeUp .55s .08s both
    }

    .jwf-head {
        text-align: center;
        margin-bottom: 24px
    }

    .jwf-head h2 {
        font-size: 26px;
        font-weight: 800;
        color: var(--c-900);
        margin-bottom: 6px;
        letter-spacing: -.5px;
        font-family: var(--font)
    }

    .jwf-head p {
        font-size: 13px;
        color: var(--c-500);
        line-height: 1.5;
        font-family: var(--font)
    }

    .jfg {
        margin-bottom: 16px
    }

    .jfg-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 600;
        color: var(--c-800);
        margin-bottom: 8px;
        font-family: var(--font)
    }

    .jfg-forgot {
        font-size: 13px;
        font-weight: 600;
        color: var(--c-blue);
        text-decoration: none;
        cursor: pointer;
        transition: color .2s
    }

    .jfg-forgot:hover {
        color: var(--c-blue-dark);
        text-decoration: underline
    }

    .jfg-wrap {
        position: relative;
        display: flex;
        align-items: center
    }

    .jfg-icon-l {
        position: absolute;
        left: 15px;
        color: var(--c-400);
        display: flex;
        align-items: center;
        pointer-events: none;
        transition: color .2s;
        z-index: 1
    }

    .jfg-icon-l svg {
        width: 17px;
        height: 17px
    }

    .jfg-icon-r {
        position: absolute;
        right: 13px;
        display: flex;
        align-items: center;
        z-index: 1
    }

    .jfg-input {
        width: 100%;
        height: 52px;
        padding: 0 48px;
        border: 1.5px solid var(--c-200);
        border-radius: 12px;
        font-size: 14px;
        font-family: var(--font);
        color: var(--c-900);
        background: #fafbfc;
        transition: var(--t);
        outline: none
    }

    .jfg-input::placeholder {
        color: #bdc8d8
    }

    .jfg-input:hover {
        border-color: var(--c-300)
    }

    .jfg-input:focus {
        border-color: var(--c-blue);
        background: var(--c-white);
        box-shadow: 0 0 0 4px var(--c-blue-ring)
    }

    .jfg-wrap:focus-within .jfg-icon-l {
        color: var(--c-blue)
    }

    .jfg-chk {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--c-green);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(34, 197, 94, .3);
        flex-shrink: 0
    }

    .jfg-chk svg {
        width: 12px;
        height: 12px;
        stroke: #fff;
        stroke-width: 3;
        fill: none
    }

    .jfg-eye {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        color: var(--c-400);
        display: flex;
        align-items: center;
        border-radius: 7px;
        transition: color .2s, background .2s
    }

    .jfg-eye:hover {
        color: var(--c-600);
        background: var(--c-100)
    }

    .jfg-eye svg {
        width: 18px;
        height: 18px
    }

    .jfg-rem {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 16px
    }

    .jfg-rem input[type="checkbox"] {
        width: 17px;
        height: 17px;
        accent-color: var(--c-blue);
        cursor: pointer;
        flex-shrink: 0
    }

    .jfg-rem label {
        font-size: 13px;
        color: var(--c-700);
        font-weight: 500;
        cursor: pointer;
        font-family: var(--font)
    }

    .jwf-cap-lbl {
        font-size: 13px;
        font-weight: 600;
        color: var(--c-800);
        margin-bottom: 8px;
        font-family: var(--font)
    }

    .jwf-cap-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px
    }

    .jwf-cap-img-wrap {
        height: 52px;
        min-width: 100px;
        max-width: 110px;
        border: 1.5px solid var(--c-200);
        border-radius: 12px;
        background: var(--c-white);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        box-shadow: inset 0 1px 3px rgba(15, 23, 42, .05)
    }

    .jwf-cap-img-wrap img {
        height: 100%;
        width: 100%;
        object-fit: contain;
        display: block
    }

    .jwf-cap-refresh {
        height: 52px;
        padding: 0 8px;
        border: none;
        background: none;
        font-size: 13px;
        font-weight: 600;
        color: var(--c-blue);
        font-family: var(--font);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        flex-shrink: 0;
        border-radius: 10px;
        transition: var(--t);
        white-space: nowrap
    }

    .jwf-cap-refresh:hover {
        background: var(--c-blue-light)
    }

    .jwf-cap-refresh svg {
        width: 15px;
        height: 15px;
        transition: transform .45s ease
    }

    .jwf-cap-refresh:hover svg {
        transform: rotate(200deg)
    }

    .jwf-cap-field {
        flex: 1;
        position: relative;
        display: flex;
        align-items: center
    }

    .jwf-cap-input {
        width: 100%;
        height: 52px;
        padding: 0 38px 0 12px;
        border: 1.5px solid var(--c-200);
        border-radius: 12px;
        font-size: 14px;
        font-family: var(--font);
        color: var(--c-900);
        background: #fafbfc;
        transition: var(--t);
        outline: none
    }

    .jwf-cap-input::placeholder {
        color: #bdc8d8
    }

    .jwf-cap-input:focus {
        border-color: var(--c-blue);
        background: var(--c-white);
        box-shadow: 0 0 0 4px var(--c-blue-ring)
    }

    .jwf-cap-chk {
        position: absolute;
        right: 12px
    }

    .jwf-btn {
        width: 100%;
        height: 56px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: var(--c-white);
        border: none;
        border-radius: 14px;
        font-size: 16px;
        font-weight: 700;
        font-family: var(--font);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 22px;
        margin: 16px 0 22px;
        box-shadow: 0 4px 20px rgba(37, 99, 235, .4), 0 1px 4px rgba(37, 99, 235, .2);
        transition: transform .22s ease, box-shadow .22s ease;
        position: relative;
        overflow: hidden;
        letter-spacing: .2px
    }

    .jwf-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, .15) 0%, transparent 60%);
        opacity: 0;
        transition: opacity .25s
    }

    .jwf-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(37, 99, 235, .48), 0 2px 10px rgba(37, 99, 235, .25)
    }

    .jwf-btn:hover::before {
        opacity: 1
    }

    .jwf-btn:active {
        transform: translateY(0)
    }

    .jwf-btn-label {
        flex: 1;
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: .2px
    }

    .jwf-btn-ico {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .18);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: transform .22s ease, background .22s
    }

    .jwf-btn:hover .jwf-btn-ico {
        background: rgba(255, 255, 255, .28);
        transform: translateX(4px)
    }

    .jwf-btn-ico svg {
        width: 18px;
        height: 18px;
        stroke: #fff;
        stroke-width: 2.5;
        fill: none
    }

    .jwf-security {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        gap: 8px;
        border-top: 1px solid var(--c-100);
        padding-top: 18px
    }

    .jwf-sec-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        padding: 10px 5px;
        background: var(--c-50);
        border: 1px solid #eef2f7;
        border-radius: 12px;
        transition: var(--t);
        cursor: default
    }

    .jwf-sec-item:hover {
        background: var(--c-white);
        border-color: #bfdbfe;
        box-shadow: 0 3px 12px rgba(37, 99, 235, .1);
        transform: translateY(-2px)
    }

    .jwf-sec-ico {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center
    }

    .sec-g {
        background: #f0fdf4;
        color: #16a34a
    }

    .sec-b {
        background: #eff6ff;
        color: #2563eb
    }

    .sec-i {
        background: #eef2ff;
        color: #4f46e5
    }

    .sec-v {
        background: #f5f3ff;
        color: #7c3aed
    }

    .jwf-sec-ico svg {
        width: 14px;
        height: 14px
    }

    .jwf-sec-name {
        font-size: 9.5px;
        font-weight: 700;
        color: var(--c-700);
        text-align: center;
        line-height: 1.3;
        font-family: var(--font)
    }

    .jwf-sec-sub {
        font-size: 8.5px;
        font-weight: 500;
        color: var(--c-400);
        text-align: center;
        line-height: 1.2;
        font-family: var(--font)
    }

    .jw-alert {
        padding: 11px 14px;
        border-radius: 11px;
        font-size: 13px;
        margin-bottom: 16px;
        line-height: 1.55;
        font-family: var(--font)
    }

    .jw-alert-err {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626
    }

    .jw-alert-ok {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #15803d
    }

    .jw-field-err {
        font-size: 11.5px;
        color: #dc2626;
        margin-top: 5px;
        display: block;
        font-family: var(--font)
    }

    .jwp-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 44px;
        background: rgba(255, 255, 255, .80);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-top: 1px solid rgba(203, 213, 225, .25);
        flex-shrink: 0;
        position: relative;
        z-index: 5
    }

    .jwp-footer-copy {
        font-size: 12px;
        color: var(--c-400);
        font-family: var(--font);
        font-weight: 400
    }

    .jwp-footer-links {
        display: flex;
        align-items: center;
        gap: 24px
    }

    .jwp-footer-links a {
        font-size: 12.5px;
        font-weight: 500;
        color: var(--c-500);
        text-decoration: none;
        font-family: var(--font);
        transition: color .2s
    }

    .jwp-footer-links a:hover {
        color: var(--c-blue)
    }

    .jwp-lang {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12.5px;
        font-weight: 500;
        color: var(--c-500);
        border: 1px solid var(--c-200);
        border-radius: 8px;
        padding: 5px 12px;
        cursor: pointer;
        font-family: var(--font);
        transition: var(--t)
    }

    .jwp-lang:hover {
        border-color: var(--c-300);
        color: var(--c-700)
    }

    .jwp-lang svg {
        width: 14px;
        height: 14px
    }

    @keyframes aFadeUp {
        from {
            opacity: 0;
            transform: translateY(18px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    .ani-1 {
        animation: aFadeUp .6s .06s both
    }

    .ani-2 {
        animation: aFadeUp .6s .12s both
    }

    .ani-3 {
        animation: aFadeUp .6s .18s both
    }

    .ani-4 {
        animation: aFadeUp .6s .24s both
    }

    @keyframes aSpin {
        to {
            transform: rotate(360deg)
        }
    }

    @media(max-width:1100px) {
        .jwp-left {
            padding: 0 36px
        }

        .jwp-right {
            width: 440px;
            padding: 28px 22px
        }

        .jwp-topbar {
            padding: 0 36px
        }

        .jwp-footer {
            padding: 14px 36px
        }
    }

    @media(max-width:880px) {
        .jwp-body {
            flex-direction: column
        }

        .jwp-right {
            width: 100%;
            padding: 28px 24px
        }

        .jwp-left {
            padding: 0 28px
        }

        .jwp-topbar {
            padding: 0 28px
        }

        .jwp-footer {
            padding: 14px 28px;
            flex-direction: column;
            gap: 10px;
            text-align: center
        }

        .jwp-footer-links {
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px
        }

        .jwp-reg-hint {
            display: none
        }
    }

    @media(max-width:640px) {
        .jwp-left {
            padding: 0 18px
        }

        .jwp-topbar {
            padding: 0 18px;
            height: 60px
        }

        .jwp-footer {
            padding: 12px 18px
        }

        .jwp-hero h1 {
            font-size: 32px;
            letter-spacing: -1.2px
        }

        .jwp-cards {
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px
        }

        .jwp-cards::-webkit-scrollbar {
            display: none
        }

        .jwp-fc {
            min-width: 200px;
            scroll-snap-align: start
        }

        .jwp-stats {
            flex-wrap: wrap
        }

        .jwp-stat {
            flex: 1 1 45%;
            border-bottom: 1px solid var(--c-200)
        }

        .jwp-stat:last-child,
        .jwp-stat:nth-last-child(-n+2):first-child {
            border-bottom: none
        }

        .jwf {
            padding: 28px 20px 24px;
            border-radius: 18px
        }

        .jwf-security {
            grid-template-columns: 1fr 1fr
        }
    }
</style>

@section('content')
    <?php
    if (session('status')) {
        $resetForm = '';
        $loginForm = 'display:none';
    } else {
        $resetForm = 'display:none';
        $loginForm = '';
    }
    ?>

    <div class="jwp">
        {{-- ── Premium background system ── --}}
        <div class="jwp-blob-tl" aria-hidden="true"></div>
        <div class="jwp-blob-tr" aria-hidden="true"></div>
        <div class="jwp-blob-br" aria-hidden="true"></div>
        <div class="jwp-blob-bl" aria-hidden="true"></div>
        <div class="jwp-blob-c" aria-hidden="true"></div>
        <div class="jwp-dots" aria-hidden="true"></div>
        <div class="jwp-dots-bl" aria-hidden="true"></div>
        {{-- Curved swoosh arc line — warm peach, bottom-left --}}
        <div class="jwp-arc" aria-hidden="true">
          <svg viewBox="0 0 800 500" fill="none" preserveAspectRatio="xMinYMax meet">
            <path d="M -60 480 Q 180 320 420 360 Q 620 395 820 280"
              stroke="url(#arcGrad1)" stroke-width="1.8" stroke-linecap="round" fill="none" opacity=".7"/>
            <path d="M -80 520 Q 160 350 400 390 Q 600 425 840 310"
              stroke="url(#arcGrad2)" stroke-width="1.2" stroke-linecap="round" fill="none" opacity=".4"/>
            <defs>
              <linearGradient id="arcGrad1" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%"   stop-color="#fdba74" stop-opacity=".0"/>
                <stop offset="30%"  stop-color="#fb923c" stop-opacity=".7"/>
                <stop offset="65%"  stop-color="#fca5a5" stop-opacity=".5"/>
                <stop offset="100%" stop-color="#f9a8d4" stop-opacity=".0"/>
              </linearGradient>
              <linearGradient id="arcGrad2" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%"   stop-color="#fdba74" stop-opacity=".0"/>
                <stop offset="35%"  stop-color="#fb923c" stop-opacity=".5"/>
                <stop offset="70%"  stop-color="#fca5a5" stop-opacity=".3"/>
                <stop offset="100%" stop-color="#f9a8d4" stop-opacity=".0"/>
              </linearGradient>
            </defs>
          </svg>
        </div>

        <header class="jwp-topbar" role="banner">
            <a href="https://jero.works" class="jwp-logo" aria-label="Jero Works home">
                <div class="jwp-logo-icon" aria-hidden="true">
                    <svg viewBox="0 0 28 28" fill="none">
                        <path d="M14 3C7.93 3 3 7.93 3 14h22C25 7.93 20.07 3 14 3z" fill="#fff" />
                        <path d="M3 14c0 1.7 1.35 3.1 3.1 3.1S9.2 15.7 9.2 14" stroke="#fff" stroke-width="1.7"
                            stroke-linecap="round" fill="none" />
                        <path d="M9.2 14c0 1.7 1.35 3.1 3.1 3.1S15.4 15.7 15.4 14" stroke="#fff" stroke-width="1.7"
                            stroke-linecap="round" fill="none" />
                        <line x1="14" y1="14" x2="14" y2="23" stroke="#fff" stroke-width="1.8"
                            stroke-linecap="round" />
                        <path d="M11 23c0 1.65 1.35 3 3 3s3-1.35 3-3" stroke="#fff" stroke-width="1.7"
                            stroke-linecap="round" fill="none" />
                    </svg>
                </div>
                <div class="jwp-logo-text">
                    <span class="jwp-logo-name">jero.works</span>
                    <span class="jwp-logo-sub">AI Recruitment Platform</span>
                </div>
            </a>
            <div class="jwp-reg">
                <span class="jwp-reg-hint">New to jero.works?</span>
                <a href="{{ route('register') }}" class="jwp-reg-btn" id="jw-create-btn">
                    Create Free Account
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </a>
            </div>
        </header>

        <div class="jwp-body">
            <main class="jwp-left" role="main">
                <div class="jwp-left-inner">

                    <div class="jwp-hero ani-1">
                        <h1>One Platform.<br>Every <span class="hl-orange">Hiring Need.</span></h1>
                        <p>Connect companies, agencies and talent on a<br>modern recruitment platform built for speed and
                            trust.</p>
                    </div>

                    <div class="jwp-cards ani-2" role="list">

                        {{-- Card 1: FOR COMPANIES — man with glasses, laptop, blue theme --}}
                        <div class="jwp-fc" role="listitem" tabindex="0">
                            <div class="jwp-fc-badge badge-b" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>
                            <span class="jwp-fc-chip chip-b">FOR COMPANIES</span>
                            <h3>Find Trusted<br>Agencies</h3>
                            <p>Connect with verified recruitment agencies and hire faster.</p>
                            <div class="jwp-fc-art" aria-hidden="true">
                                <svg viewBox="0 0 200 155" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <radialGradient id="bg1a" cx="50%" cy="60%" r="50%">
                                            <stop offset="0%" stop-color="#e0f2fe" />
                                            <stop offset="60%" stop-color="#bae6fd" />
                                            <stop offset="100%" stop-color="#7dd3fc" />
                                        </radialGradient>
                                        <radialGradient id="skin1" cx="40%" cy="35%" r="60%">
                                            <stop offset="0%" stop-color="#fde68a" />
                                            <stop offset="100%" stop-color="#f59e0b" />
                                        </radialGradient>
                                    </defs>
                                    <!-- Background circle -->
                                    <ellipse cx="100" cy="100" rx="80" ry="68"
                                        fill="url(#bg1a)" />
                                    <!-- Laptop base/hinge -->
                                    <rect x="38" y="118" width="124" height="9" rx="4.5" fill="#1e293b" />
                                    <rect x="45" y="127" width="110" height="4" rx="2" fill="#0f172a" />
                                    <!-- Laptop screen body -->
                                    <rect x="44" y="80" width="112" height="40" rx="5" fill="#0f172a" />
                                    <rect x="46" y="82" width="108" height="36" rx="4" fill="#1e293b" />
                                    <!-- Screen display -->
                                    <rect x="48" y="84" width="104" height="32" rx="3" fill="#0f172a" />
                                    <rect x="52" y="88" width="42" height="3.5" rx="1.7" fill="#38bdf8" />
                                    <rect x="52" y="94" width="28" height="2" rx="1" fill="#334155" />
                                    <rect x="52" y="98" width="34" height="2" rx="1" fill="#334155" />
                                    <rect x="52" y="102" width="22" height="2" rx="1" fill="#334155" />
                                    <rect x="100" y="87" width="44" height="25" rx="3" fill="#1d4ed8"
                                        opacity=".75" />
                                    <rect x="104" y="91" width="36" height="2" rx="1" fill="#93c5fd" />
                                    <rect x="104" y="95" width="28" height="2" rx="1" fill="#60a5fa"
                                        opacity=".6" />
                                    <rect x="104" y="99" width="32" height="2" rx="1" fill="#60a5fa"
                                        opacity=".4" />
                                    <!-- Left arm + hand -->
                                    <path d="M76 90 Q66 95 52 108" stroke="#1d4ed8" stroke-width="13"
                                        stroke-linecap="round" fill="none" />
                                    <ellipse cx="50" cy="110" rx="9" ry="8"
                                        fill="url(#skin1)" />
                                    <!-- Right arm + hand -->
                                    <path d="M124 90 Q134 95 148 108" stroke="#1d4ed8" stroke-width="13"
                                        stroke-linecap="round" fill="none" />
                                    <ellipse cx="150" cy="110" rx="9" ry="8"
                                        fill="url(#skin1)" />
                                    <!-- Body / torso (blue suit) -->
                                    <path d="M76 90 Q76 80 100 80 Q124 80 124 90 L128 120 H72 Z" fill="#2563eb" />
                                    <!-- Shirt + tie -->
                                    <rect x="93" y="80" width="14" height="22" rx="2" fill="#f8fafc" />
                                    <polygon points="100,80 97,88 100,95 103,88" fill="#dc2626" />
                                    <rect x="98" y="92" width="4" height="6" rx="2" fill="#b91c1c" />
                                    <!-- Neck -->
                                    <rect x="93" y="70" width="14" height="12" rx="5"
                                        fill="url(#skin1)" />
                                    <!-- Head -->
                                    <ellipse cx="100" cy="55" rx="22" ry="20"
                                        fill="url(#skin1)" />
                                    <!-- Hair (dark brown, swept) -->
                                    <path d="M78 50 Q78 32 100 30 Q122 32 122 50 Q118 36 100 36 Q82 36 78 50Z"
                                        fill="#292524" />
                                    <rect x="78" y="44" width="6" height="8" rx="3" fill="#292524" />
                                    <rect x="116" y="44" width="6" height="8" rx="3" fill="#292524" />
                                    <!-- Left ear -->
                                    <ellipse cx="78" cy="55" rx="4" ry="5"
                                        fill="#f59e0b" />
                                    <!-- Right ear -->
                                    <ellipse cx="122" cy="55" rx="4" ry="5"
                                        fill="#f59e0b" />
                                    <!-- Glasses frame -->
                                    <rect x="87" y="52" width="10" height="7" rx="3" fill="none"
                                        stroke="#1c1917" stroke-width="1.8" />
                                    <rect x="103" y="52" width="10" height="7" rx="3" fill="none"
                                        stroke="#1c1917" stroke-width="1.8" />
                                    <line x1="97" y1="55.5" x2="103" y2="55.5" stroke="#1c1917"
                                        stroke-width="1.5" />
                                    <line x1="78" y1="55.5" x2="87" y2="55.5" stroke="#1c1917"
                                        stroke-width="1.5" />
                                    <line x1="113" y1="55.5" x2="122" y2="55.5" stroke="#1c1917"
                                        stroke-width="1.5" />
                                    <!-- Eyes (behind glasses) -->
                                    <circle cx="92" cy="55.5" r="2.5" fill="#1c1917" />
                                    <circle cx="108" cy="55.5" r="2.5" fill="#1c1917" />
                                    <circle cx="93" cy="54.5" r=".8" fill="white" />
                                    <circle cx="109" cy="54.5" r=".8" fill="white" />
                                    <!-- Smile -->
                                    <path d="M94 64 Q100 69 106 64" stroke="#92400e" stroke-width="1.8"
                                        stroke-linecap="round" fill="none" />
                                    <!-- Nose -->
                                    <path d="M99 60 Q97 63 100 64 Q103 63 101 60" stroke="#d97706" stroke-width="1.2"
                                        stroke-linecap="round" fill="none" />
                                </svg>
                            </div>
                        </div>

                        {{-- Card 2: FOR AGENCIES — woman with long hair, laptop, green theme --}}
                        <div class="jwp-fc" role="listitem" tabindex="0">
                            <div class="jwp-fc-badge badge-g" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <rect x="2" y="7" width="20" height="14" rx="2" />
                                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                                </svg>
                            </div>
                            <span class="jwp-fc-chip chip-g">FOR AGENCIES</span>
                            <h3>Search Work<br>Opportunities</h3>
                            <p>Discover verified job opportunities from leading companies.</p>
                            <div class="jwp-fc-art" aria-hidden="true">
                                <svg viewBox="0 0 200 155" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <radialGradient id="bg2a" cx="50%" cy="60%" r="50%">
                                            <stop offset="0%" stop-color="#dcfce7" />
                                            <stop offset="60%" stop-color="#bbf7d0" />
                                            <stop offset="100%" stop-color="#86efac" />
                                        </radialGradient>
                                        <radialGradient id="skin2" cx="40%" cy="35%" r="60%">
                                            <stop offset="0%" stop-color="#fde68a" />
                                            <stop offset="100%" stop-color="#f59e0b" />
                                        </radialGradient>
                                    </defs>
                                    <ellipse cx="100" cy="100" rx="80" ry="68"
                                        fill="url(#bg2a)" />
                                    <!-- Laptop base -->
                                    <rect x="38" y="118" width="124" height="9" rx="4.5" fill="#1e293b" />
                                    <rect x="45" y="127" width="110" height="4" rx="2" fill="#0f172a" />
                                    <!-- Laptop screen -->
                                    <rect x="44" y="80" width="112" height="40" rx="5" fill="#0f172a" />
                                    <rect x="46" y="82" width="108" height="36" rx="4" fill="#1e293b" />
                                    <rect x="48" y="84" width="104" height="32" rx="3" fill="#0f172a" />
                                    <rect x="52" y="88" width="40" height="3.5" rx="1.7" fill="#4ade80" />
                                    <rect x="52" y="94" width="30" height="2" rx="1" fill="#334155" />
                                    <rect x="52" y="98" width="24" height="2" rx="1" fill="#334155" />
                                    <rect x="52" y="102" width="36" height="2" rx="1" fill="#334155" />
                                    <rect x="98" y="87" width="46" height="25" rx="3" fill="#15803d"
                                        opacity=".7" />
                                    <rect x="102" y="91" width="38" height="2" rx="1" fill="#86efac" />
                                    <rect x="102" y="95" width="30" height="2" rx="1" fill="#86efac"
                                        opacity=".6" />
                                    <rect x="102" y="99" width="26" height="2" rx="1" fill="#86efac"
                                        opacity=".4" />
                                    <!-- Arms -->
                                    <path d="M76 90 Q66 96 52 110" stroke="#15803d" stroke-width="13"
                                        stroke-linecap="round" fill="none" />
                                    <ellipse cx="50" cy="112" rx="9" ry="8"
                                        fill="url(#skin2)" />
                                    <path d="M124 90 Q134 96 148 110" stroke="#15803d" stroke-width="13"
                                        stroke-linecap="round" fill="none" />
                                    <ellipse cx="150" cy="112" rx="9" ry="8"
                                        fill="url(#skin2)" />
                                    <!-- Body green jacket -->
                                    <path d="M76 90 Q76 80 100 80 Q124 80 124 90 L128 120 H72 Z" fill="#16a34a" />
                                    <!-- Collar / blouse -->
                                    <path d="M93 80 L100 92 L107 80" stroke="#f0fdf4" stroke-width="2" fill="#f0fdf4"
                                        fill-opacity=".9" />
                                    <!-- Neck -->
                                    <rect x="93" y="70" width="14" height="12" rx="5"
                                        fill="url(#skin2)" />
                                    <!-- Head -->
                                    <ellipse cx="100" cy="54" rx="22" ry="21"
                                        fill="url(#skin2)" />
                                    <!-- Long dark hair -->
                                    <path d="M78 48 Q76 30 100 28 Q124 30 122 48 Q120 34 100 34 Q80 34 78 48Z"
                                        fill="#1c1917" />
                                    <path d="M78 48 Q72 65 72 90 L78 90 Q78 65 80 50Z" fill="#1c1917" />
                                    <path d="M122 48 Q128 65 128 90 L122 90 Q122 65 120 50Z" fill="#1c1917" />
                                    <rect x="78" y="42" width="5" height="8" rx="2.5" fill="#1c1917" />
                                    <rect x="117" y="42" width="5" height="8" rx="2.5" fill="#1c1917" />
                                    <!-- Ears -->
                                    <ellipse cx="78" cy="54" rx="4" ry="5"
                                        fill="#f59e0b" />
                                    <ellipse cx="122" cy="54" rx="4" ry="5"
                                        fill="#f59e0b" />
                                    <!-- Eyes -->
                                    <ellipse cx="92" cy="54" rx="3.5" ry="3"
                                        fill="#1c1917" />
                                    <ellipse cx="108" cy="54" rx="3.5" ry="3"
                                        fill="#1c1917" />
                                    <ellipse cx="90.5" cy="52.5" rx="1.2" ry="1"
                                        fill="white" />
                                    <ellipse cx="106.5" cy="52.5" rx="1.2" ry="1"
                                        fill="white" />
                                    <!-- Eyebrows -->
                                    <path d="M88 49.5 Q92 47.5 96 49.5" stroke="#1c1917" stroke-width="1.6"
                                        stroke-linecap="round" fill="none" />
                                    <path d="M104 49.5 Q108 47.5 112 49.5" stroke="#1c1917" stroke-width="1.6"
                                        stroke-linecap="round" fill="none" />
                                    <!-- Nose -->
                                    <path d="M99 59 Q97 62 100 63.5 Q103 62 101 59" stroke="#d97706" stroke-width="1.2"
                                        stroke-linecap="round" fill="none" />
                                    <!-- Smile -->
                                    <path d="M94 67 Q100 73 106 67" stroke="#92400e" stroke-width="1.8"
                                        stroke-linecap="round" fill="none" />
                                    <!-- Lips -->
                                    <path d="M95 69 Q100 72 105 69" fill="#f9a8d4" opacity=".6" />
                                </svg>
                            </div>
                        </div>

                        {{-- Card 3: FOR TALENT — person in purple hoodie with phone --}}
                        <div class="jwp-fc" role="listitem" tabindex="0">
                            <div class="jwp-fc-badge badge-p" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" fill="white"
                                        stroke="none" />
                                </svg>
                            </div>
                            <span class="jwp-fc-chip chip-p">FOR TALENT</span>
                            <h3>Hire Immediate<br>Joiners</h3>
                            <p>Explore bench talent ready to join at the earliest.</p>
                            <div class="jwp-fc-art" aria-hidden="true">
                                <svg viewBox="0 0 200 155" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <radialGradient id="bg3a" cx="50%" cy="60%" r="50%">
                                            <stop offset="0%" stop-color="#ede9fe" />
                                            <stop offset="60%" stop-color="#ddd6fe" />
                                            <stop offset="100%" stop-color="#c4b5fd" />
                                        </radialGradient>
                                        <radialGradient id="skin3" cx="40%" cy="35%" r="60%">
                                            <stop offset="0%" stop-color="#fde68a" />
                                            <stop offset="100%" stop-color="#f59e0b" />
                                        </radialGradient>
                                        <radialGradient id="phone3" cx="50%" cy="30%" r="70%">
                                            <stop offset="0%" stop-color="#a78bfa" />
                                            <stop offset="100%" stop-color="#7c3aed" />
                                        </radialGradient>
                                    </defs>
                                    <ellipse cx="100" cy="100" rx="80" ry="68"
                                        fill="url(#bg3a)" />
                                    <!-- Phone (held in right hand) -->
                                    <rect x="128" y="72" width="26" height="44" rx="5" fill="#1e1b4b" />
                                    <rect x="130" y="74" width="22" height="40" rx="4"
                                        fill="url(#phone3)" />
                                    <rect x="132" y="77" width="18" height="28" rx="2.5" fill="#4c1d95"
                                        opacity=".5" />
                                    <rect x="134" y="80" width="14" height="3" rx="1.5" fill="#e9d5ff"
                                        opacity=".9" />
                                    <rect x="134" y="85" width="10" height="2" rx="1" fill="#c4b5fd"
                                        opacity=".7" />
                                    <rect x="134" y="89" width="12" height="2" rx="1" fill="#c4b5fd"
                                        opacity=".6" />
                                    <rect x="134" y="93" width="8" height="2" rx="1" fill="#c4b5fd"
                                        opacity=".5" />
                                    <circle cx="141" cy="100" r="3" fill="#7c3aed" />
                                    <rect x="139" y="110" width="4" height="1.5" rx=".75" fill="#e9d5ff"
                                        opacity=".5" />
                                    <!-- Right arm holding phone -->
                                    <path d="M118 95 Q126 88 130 80" stroke="#7c3aed" stroke-width="14"
                                        stroke-linecap="round" fill="none" />
                                    <ellipse cx="132" cy="77" rx="8" ry="7"
                                        fill="url(#skin3)" />
                                    <!-- Body / hoodie -->
                                    <path d="M72 95 Q72 84 100 84 Q128 84 128 95 L132 130 H68 Z" fill="#7c3aed" />
                                    <!-- Hoodie details: pocket seam -->
                                    <rect x="84" y="105" width="32" height="18" rx="4" fill="#6d28d9" />
                                    <!-- Hoodie zipper -->
                                    <rect x="98" y="84" width="4" height="22" rx="2" fill="#6d28d9" />
                                    <!-- Hood outline -->
                                    <path d="M72 95 Q68 80 100 76 Q132 80 128 95" stroke="#6d28d9" stroke-width="2"
                                        fill="none" />
                                    <!-- Left arm (down/relaxed) -->
                                    <path d="M72 95 Q62 100 55 116" stroke="#7c3aed" stroke-width="14"
                                        stroke-linecap="round" fill="none" />
                                    <ellipse cx="54" cy="119" rx="9" ry="8"
                                        fill="url(#skin3)" />
                                    <!-- Legs -->
                                    <rect x="78" y="128" width="18" height="26" rx="9" fill="#4c1d95" />
                                    <rect x="104" y="128" width="18" height="26" rx="9" fill="#4c1d95" />
                                    <!-- Shoes -->
                                    <rect x="74" y="151" width="24" height="6" rx="3" fill="#1c1917" />
                                    <rect x="102" y="151" width="24" height="6" rx="3" fill="#1c1917" />
                                    <!-- Neck -->
                                    <rect x="93" y="72" width="14" height="14" rx="5"
                                        fill="url(#skin3)" />
                                    <!-- Head -->
                                    <ellipse cx="100" cy="57" rx="24" ry="22"
                                        fill="url(#skin3)" />
                                    <!-- Short dark hair -->
                                    <path d="M76 50 Q76 33 100 31 Q124 33 124 50 Q120 36 100 36 Q80 36 76 50Z"
                                        fill="#1c1917" />
                                    <rect x="76" y="44" width="6" height="9" rx="3" fill="#1c1917" />
                                    <rect x="118" y="44" width="6" height="9" rx="3" fill="#1c1917" />
                                    <path d="M78 50 Q76 56 76 58" stroke="#1c1917" stroke-width="5"
                                        stroke-linecap="round" fill="none" />
                                    <path d="M122 50 Q124 56 124 58" stroke="#1c1917" stroke-width="5"
                                        stroke-linecap="round" fill="none" />
                                    <!-- Ears -->
                                    <ellipse cx="76" cy="57" rx="4" ry="5"
                                        fill="#f59e0b" />
                                    <ellipse cx="124" cy="57" rx="4" ry="5"
                                        fill="#f59e0b" />
                                    <!-- Eyes -->
                                    <ellipse cx="92" cy="57" rx="3.5" ry="3.5"
                                        fill="#1c1917" />
                                    <ellipse cx="108" cy="57" rx="3.5" ry="3.5"
                                        fill="#1c1917" />
                                    <ellipse cx="90.5" cy="55.5" rx="1.2" ry="1.2"
                                        fill="white" />
                                    <ellipse cx="106.5" cy="55.5" rx="1.2" ry="1.2"
                                        fill="white" />
                                    <!-- Eyebrows -->
                                    <path d="M88 51 Q92 49 96 51" stroke="#1c1917" stroke-width="1.8"
                                        stroke-linecap="round" fill="none" />
                                    <path d="M104 51 Q108 49 112 51" stroke="#1c1917" stroke-width="1.8"
                                        stroke-linecap="round" fill="none" />
                                    <!-- Nose -->
                                    <path d="M99 62 Q97 65 100 66.5 Q103 65 101 62" stroke="#d97706" stroke-width="1.2"
                                        stroke-linecap="round" fill="none" />
                                    <!-- Slight smile -->
                                    <path d="M94 70 Q100 75 106 70" stroke="#92400e" stroke-width="1.8"
                                        stroke-linecap="round" fill="none" />
                                </svg>
                            </div>
                        </div>

                    </div>

                    <div class="jwp-stats ani-3" role="list">
                        <div class="jwp-stat" role="listitem">
                            <div class="jwp-stat-ico sic-b"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg></div>
                            <div>
                                <div class="jwp-stat-num" data-count="150" data-suffix="+">0</div>
                                <div class="jwp-stat-lbl">Enterprise Clients</div>
                            </div>
                        </div>
                        <div class="jwp-stat" role="listitem">
                            <div class="jwp-stat-ico sic-g"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="2" y="7" width="20" height="14" rx="2" />
                                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                                </svg></div>
                            <div>
                                <div class="jwp-stat-num" data-count="25" data-suffix=",000+">0</div>
                                <div class="jwp-stat-lbl">Active Jobs</div>
                            </div>
                        </div>
                        <div class="jwp-stat" role="listitem">
                            <div class="jwp-stat-ico sic-p"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg></div>
                            <div>
                                <div class="jwp-stat-num" data-count="250" data-suffix=",000+">0</div>
                                <div class="jwp-stat-lbl">Candidate Profiles</div>
                            </div>
                        </div>
                        <div class="jwp-stat" role="listitem">
                            <div class="jwp-stat-ico sic-o"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                </svg></div>
                            <div>
                                <div class="jwp-stat-num" data-count="99" data-suffix=".9%">0</div>
                                <div class="jwp-stat-lbl">Platform Uptime</div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>

            <aside class="jwp-right" role="complementary">

                <div class="jwf" id="jw-login-card" style="{{ $loginForm }}">
                    <div class="jwf-head">
                        <h2>Welcome Back!</h2>
                        <p>Sign in to continue to your jero.works account</p>
                    </div>
                    @if (session('accountnotverify'))
                        <div class="jw-alert jw-alert-err" role="alert">{{ session('accountnotverify') }}</div>
                    @endif
                    @if (@$class && @$messsage)
                        <div class="jw-alert jw-alert-{{ $class == 'success' ? 'ok' : 'err' }}" role="alert">
                            {!! @$messsage !!}</div>
                    @endif
                    <form novalidate id="kt_sign_in_form1" action="{{ route('login') }}" method="post">
                        @csrf
                        <input type="hidden" name="url" value="{{ @$url }}">
                        <div class="jfg">
                            <div class="jfg-label"><label for="jw-email">Email Address</label></div>
                            <div class="jfg-wrap">
                                <span class="jfg-icon-l"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                        <polyline points="22,6 12,13 2,6" />
                                    </svg></span>
                                <input class="jfg-input" type="text" name="email" id="jw-email"
                                    placeholder="you@company.com" autocomplete="email" value="{{ old('email') }}"
                                    aria-required="true">
                                <span class="jfg-icon-r" id="jw-email-chk" style="display:none">
                                    <div class="jfg-chk"><svg viewBox="0 0 24 24" fill="none">
                                            <polyline points="20 6 9 17 4 12" stroke="white" stroke-width="3"
                                                stroke-linecap="round" />
                                        </svg></div>
                                </span>
                            </div>
                            @if ($errors->has('email'))
                                <span class="jw-field-err" role="alert">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                        <div class="jfg">
                            <div class="jfg-label">
                                <label for="jw-pwd">Password</label>
                                <a href="javascript:void(0);" id="to-recover" class="jfg-forgot">Forgot Password?</a>
                            </div>
                            <div class="jfg-wrap">
                                <span class="jfg-icon-l"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg></span>
                                <input class="jfg-input" type="password" id="jw-pwd" name="password"
                                    placeholder="••••••••••••" autocomplete="current-password" aria-required="true">
                                <span class="jfg-icon-r">
                                    <button type="button" class="jfg-eye" id="jw-eye-btn" onclick="jwTogglePwd()"
                                        aria-label="Toggle password visibility" aria-pressed="false">
                                        <svg id="jw-eye-show" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <svg id="jw-eye-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            style="display:none">
                                            <path
                                                d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                                            <line x1="1" y1="1" x2="23" y2="23" />
                                        </svg>
                                    </button>
                                </span>
                            </div>
                            @if ($errors->has('password'))
                                <span class="jw-field-err" role="alert">{{ $errors->first('password') }}</span>
                            @endif
                        </div>
                        @if ($errors->has('device_message'))
                            <div class="jw-alert jw-alert-err" role="alert">{{ $errors->first('device_message') }}</div>
                        @endif
                        <div class="jfg-rem">
                            <input type="checkbox" id="jw-rem" name="remember" checked>
                            <label for="jw-rem">Remember me</label>
                        </div>
                        <div class="jwf-cap-lbl">Enter the number in the box</div>
                        <div class="jwf-cap-row">
                            <div class="jwf-cap-img-wrap"><img src="{{ route('captcha.generate') }}" id="captchaImg"
                                    alt="CAPTCHA code"></div>
                            <button type="button" onclick="jwRefreshCaptcha()" class="jwf-cap-refresh"
                                id="jw-refresh-captcha-btn">
                                <svg id="jw-refresh-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="23 4 23 10 17 10" />
                                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
                                </svg>
                                Refresh
                            </button>
                            <div class="jwf-cap-field">
                                <input type="text" name="captcha" id="jw-captcha-input" class="jwf-cap-input"
                                    placeholder="Enter code" autocomplete="off" required inputmode="numeric">
                                <span class="jwf-cap-chk" id="jw-cap-chk" style="display:none">
                                    <div class="jfg-chk"><svg viewBox="0 0 24 24" fill="none">
                                            <polyline points="20 6 9 17 4 12" stroke="white" stroke-width="3"
                                                stroke-linecap="round" />
                                        </svg></div>
                                </span>
                            </div>
                        </div>
                        @if ($errors->has('captcha'))
                            <span class="jw-field-err" style="margin-bottom:10px;display:block;"
                                role="alert">{{ $errors->first('captcha') }}</span>
                        @endif
                        <button type="submit" id="kt_sign_in_submit" class="jwf-btn">
                            <span class="jwf-btn-label">Sign In</span>
                            <span class="jwf-btn-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 12 19" />
                                </svg></span>
                        </button>
                    </form>
                    <div class="jwf-security" role="list">
                        {{-- SSL Secured: green lock --}}
                        <div class="jwf-sec-item" role="listitem">
                            <div class="jwf-sec-ico sec-g">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </div>
                            <div class="jwf-sec-name">SSL Secured</div>
                            <div class="jwf-sec-sub">256-bit encryption</div>
                        </div>
                        {{-- 99.9% Uptime: blue shield with check --}}
                        <div class="jwf-sec-item" role="listitem">
                            <div class="jwf-sec-ico sec-b">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    <polyline points="9 12 11 14 15 10" />
                                </svg>
                            </div>
                            <div class="jwf-sec-name">99.9% Uptime</div>
                            <div class="jwf-sec-sub">Reliable Platform</div>
                        </div>
                        {{-- SOC 2 Ready: green shield --}}
                        <div class="jwf-sec-item" role="listitem">
                            <div class="jwf-sec-ico sec-g">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                </svg>
                            </div>
                            <div class="jwf-sec-name">SOC 2 Ready</div>
                            <div class="jwf-sec-sub">Enterprise Grade</div>
                        </div>
                        {{-- GDPR: blue/indigo shield with info --}}
                        <div class="jwf-sec-item" role="listitem">
                            <div class="jwf-sec-ico sec-b">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    <line x1="12" y1="11" x2="12" y2="15" />
                                    <circle cx="12" cy="8" r=".7" fill="currentColor" stroke="none" />
                                </svg>
                            </div>
                            <div class="jwf-sec-name">GDPR Compliant</div>
                            <div class="jwf-sec-sub">Data Protection</div>
                        </div>
                    </div>
                </div>

                <div class="jwf" id="jw-recover-card" style="{{ $resetForm }}">
                    <div class="jwf-head">
                        <h2>Reset Password</h2>
                        <p>Enter your email to receive a password reset link.</p>
                    </div>
                    @if (session('status'))
                        <div class="jw-alert jw-alert-ok" role="alert">{{ session('status') }}</div>
                    @endif
                    @if (session('accountnotverify'))
                        <div class="jw-alert jw-alert-err" role="alert">{{ session('accountnotverify') }}</div>
                    @endif
                    <form novalidate id="recoverform" action="{{ route('password.emailsend') }}" method="post">
                        {{ csrf_field() }}
                        <div class="jfg">
                            <div class="jfg-label"><label for="jw-recover-email">Email Address</label></div>
                            <div class="jfg-wrap">
                                <span class="jfg-icon-l"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                        <polyline points="22,6 12,13 2,6" />
                                    </svg></span>
                                <input type="email" name="email" id="jw-recover-email" class="jfg-input"
                                    placeholder="you@company.com" value="{{ old('email') }}" required
                                    autocomplete="email">
                            </div>
                            @if ($errors->has('email'))
                                <span class="jw-field-err" role="alert">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                        <button type="submit" id="reset_password" class="jwf-btn">
                            <span class="jwf-btn-label">Send Reset Link</span>
                            <span class="jwf-btn-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 12 19" />
                                </svg></span>
                        </button>
                        <div style="text-align:center;margin-top:-8px;"><a href="{{ route('login') }}"
                                style="font-size:13px;color:#64748b;text-decoration:none;font-weight:500;">← Back to
                                Login</a></div>
                    </form>
                </div>

            </aside>
        </div>

        <footer class="jwp-footer" role="contentinfo">
            <div class="jwp-footer-copy">© 2026 Jero Technologies Pvt. Ltd. All rights reserved.</div>
            <div class="jwp-footer-links">
                <a href="{{ url('/payment-terms-and-conditions') }}" target="_blank" rel="noopener">Privacy Policy</a>
                <a href="https://jero.works/support-policy" target="_blank" rel="noopener">Terms of Service</a>
                <a href="https://jero.works/support-policy" target="_blank" rel="noopener">Support</a>
                <div class="jwp-lang" tabindex="0" role="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="2" y1="12" x2="22" y2="12" />
                        <path
                            d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                    </svg>
                    English
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px">
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                </div>
            </div>
        </footer>
    </div>

    <script>
        function jwTogglePwd() {
            var f = document.getElementById('jw-pwd'),
                b = document.getElementById('jw-eye-btn'),
                s = document.getElementById('jw-eye-show'),
                h = document.getElementById('jw-eye-hide'),
                p = f.type === 'password';
            f.type = p ? 'text' : 'password';
            s.style.display = p ? 'none' : '';
            h.style.display = p ? '' : 'none';
            b.setAttribute('aria-pressed', p ? 'true' : 'false')
        }

        function jwRefreshCaptcha() {
            var img = document.getElementById('captchaImg'),
                ico = document.getElementById('jw-refresh-ico');
            img.src = "{{ route('captcha.generate') }}?t=" + Date.now();
            ico.style.transition = 'transform .45s ease';
            ico.style.transform = 'rotate(200deg)';
            setTimeout(function() {
                ico.style.transform = 'rotate(0deg)';
                ico.style.transition = 'transform .3s ease'
            }, 500);
            var ci = document.getElementById('jw-captcha-input'),
                ck = document.getElementById('jw-cap-chk');
            if (ci) ci.value = '';
            if (ck) ck.style.display = 'none'
        }

        function jwCountUp(el) {
            var t = parseInt(el.dataset.count, 10),
                s = el.dataset.suffix || '',
                d = 1800,
                st = performance.now();

            function step(n) {
                var p = Math.min((n - st) / d, 1),
                    e = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.floor(e * t) + (p >= 1 ? s : '');
                if (p < 1) requestAnimationFrame(step)
            }
            requestAnimationFrame(step)
        }
        $(document).ready(function() {
            $('#to-recover').on('click', function() {
                $('#kt_sign_in_form1').closest('.jwf').fadeOut(200, function() {
                    $('#recoverform').closest('.jwf').fadeIn(260)
                })
            });
            var se = document.querySelector('.jwp-stats');
            if (se && typeof IntersectionObserver !== 'undefined') {
                var o = new IntersectionObserver(function(e) {
                    e.forEach(function(x) {
                        if (x.isIntersecting) {
                            x.target.querySelectorAll('[data-count]').forEach(jwCountUp);
                            o.unobserve(x.target)
                        }
                    })
                }, {
                    threshold: 0.3
                });
                o.observe(se)
            } else if (se) {
                se.querySelectorAll('[data-count]').forEach(jwCountUp)
            }
            var ei = document.getElementById('jw-email'),
                ec = document.getElementById('jw-email-chk');
            if (ei && ec) {
                function ce() {
                    ec.style.display = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(ei.value.trim()) ? '' : 'none'
                }
                ei.addEventListener('input', ce);
                ce()
            }
            var ci = document.getElementById('jw-captcha-input'),
                cc = document.getElementById('jw-cap-chk');
            if (ci && cc) {
                ci.addEventListener('input', function() {
                    cc.style.display = ci.value.trim().length >= 2 ? '' : 'none'
                })
            }
            $('#kt_sign_in_form1').on('submit', function() {
                var b = document.getElementById('kt_sign_in_submit');
                if (b) {
                    b.disabled = true;
                    b.innerHTML =
                        '<span class="jwf-btn-label" style="display:flex;align-items:center;justify-content:center;gap:8px"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="animation:aSpin .7s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>Signing In...</span>';
                    b.style.opacity = '.85'
                }
            });
        });
    </script>
@endsection
