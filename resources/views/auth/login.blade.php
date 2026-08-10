@extends('layouts.metronic_front_app')
<title>Sign In — jero.works</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ── Tokens ────────────────────────────────────────────────── */
:root {
    --pri:       #2463FF;   /* primary blue */
    --pri-h:     #1B4FE0;
    --pri-d:     #0F3BCC;
    --pri-l:     #EEF2FF;
    --acc:       #FF6A00;   /* Jero orange */
    --acc-d:     #E55A00;
    --bg-pn:     #F4F1FF;   /* soft lavender */
    --bg-pg:     #F6F7FB;
    --bg-card:   #FFFFFF;
    --dk:        #071A3A;   /* jero navy */
    --tx:        #1F2937;
    --gr:        #6B7280;
    --gr-l:      #98A0AE;
    --bd:        #E2E5ED;
    --bd-l:      #EDEFF4;
    --ok:        #16A34A;
    --c-blue:    #2463FF;
    --c-green:   #16A34A;
    --c-purple:  #6D28D9;
    --c-orange:  #FF6A00;
    --p-blue:    #DEE9FE;
    --p-green:   #DCF5E4;
    --p-purple:  #EFE6FE;
    --p-orange:  #FFE6D2;
    --p-pink:    #FFE3EC;
    --shadow-sm: 0 1px 2px rgba(15,23,42,0.04);
    --shadow-md: 0 4px 16px rgba(15,23,42,0.06);
    --shadow-lg: 0 12px 32px rgba(15,23,42,0.08);
    --shadow-btn: 0 1px 2px rgba(36,99,255,0.30), 0 6px 16px rgba(36,99,255,0.30);
    --shadow-btn-h: 0 2px 4px rgba(36,99,255,0.40), 0 12px 28px rgba(36,99,255,0.40);
    --r-sm: 8px;
    --r-md: 10px;
    --r-lg: 14px;
    --r-xl: 20px;
}

* { box-sizing: border-box; }
html, body { margin: 0 !important; padding: 0 !important; height: 100%; overflow: hidden; }
body, button, input, select, textarea, span, p, h1, h2, h3, h4, h5, h6, a, div, label {
    font-family: 'Poppins', system-ui, -apple-system, sans-serif !important;
}
body { background: var(--bg-pg); color: var(--tx); }

/* ── Unified Single-Panel Canvas ── */
.jw-shell {
    height: 100vh;
    width: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
    background:
        radial-gradient(circle at 10% 15%, rgba(36, 99, 255, 0.08) 0%, transparent 45%),
        radial-gradient(circle at 90% 85%, rgba(255, 106, 0, 0.08) 0%, transparent 45%),
        radial-gradient(circle at 50% 50%, rgba(139, 92, 246, 0.04) 0%, transparent 50%),
        #F8FAFC;
}
.jw-shell::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(15, 23, 42, 0.05) 1.2px, transparent 1.2px);
    background-size: 24px 24px;
    pointer-events: none;
    z-index: 0;
}
.jw-page {
    display: flex;
    flex: 1;
    width: 100%;
    min-height: 0;
    overflow: hidden;
    position: relative;
    z-index: 1;
}

/* ═══════════════ PANELS (Transparent Overlay) ═══════════════ */
.jw-left {
    flex: 1;
    min-width: 0;
    background: transparent;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
    padding: 14px 32px 10px;
    min-height: 0;
    height: 100%;
}
.jw-left::before { display: none; }

/* Brand mark */
.jw-brand {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    line-height: 1;
    position: relative;
    flex-shrink: 0;
    height: 36px;
}
.jw-brand-logo {
    height: 36px;
    width: auto;
    max-width: 220px;
    display: block;
}
.jw-brand-sub {
    display: block;
    font-size: 10.5px;
    color: var(--gr);
    font-weight: 500;
    margin-top: 3px;
    margin-left: 2px;
    letter-spacing: 0.15px;
}

/* Hero */
.jw-hero { margin: 22px 0 12px; max-width: 100%; position: relative; flex-shrink: 0; }
.jw-hero h1 {
    font-size: 36px !important;
    font-weight: 800;
    color: var(--dk);
    line-height: 1.15;
    letter-spacing: -1.1px;
    margin: 0 0 8px;
}
.jw-hero h1 .acc { color: var(--acc); }
.jw-hero p {
    font-size: 12.5px; color: var(--gr); line-height: 1.5;
    margin: 0; font-weight: 400;
    max-width: 100%;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ── Combined 3-in-1 Audience Illustration ── */
.jw-illustration-link {
    display: block;
    width: 100%;
    flex: 1;
    min-height: 0;
    margin: 4px 0 8px;
    position: relative;
    border-radius: var(--r-lg);
    overflow: hidden;
    transition: transform .2s ease;
    line-height: 0;
    background: transparent;
    max-height: 390px;
}
.jw-illustration-link:hover,
.jw-illustration-link:focus-visible {
    transform: translateY(-3px);
    outline: none;
}
.jw-illustration-img {
    display: block;
    width: 100%;
    height: 100%;
    margin: 0;
    object-fit: contain;
    object-position: center center;
    user-select: none;
    -webkit-user-drag: none;
    pointer-events: none;
}

/* ── Stats bar with subtle dividers ────────────────────────── */
.jw-stats {
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(10px);
    border-radius: var(--r-lg);
    padding: 8px 12px;
    display: flex;
    align-items: center;
    border: 1px solid rgba(226, 232, 240, 0.9);
    box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
    position: relative;
    flex-shrink: 0;
    z-index: 2;
}
.jw-stat {
    flex: 1;
    display: flex; align-items: center; gap: 8px;
    padding: 0 4px;
    position: relative;
    min-width: 0;
}
.jw-stat + .jw-stat::before {
    content: '';
    position: absolute;
    left: 0; top: 18%; bottom: 18%;
    width: 1px;
    background: var(--bd);
}
.jw-stat-ico {
    width: 26px; height: 26px;
    border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.jw-stat-ico svg { width: 14px; height: 14px; }
.jw-stat-ico.s-people { background: var(--p-blue);   color: var(--c-blue); }
.jw-stat-ico.s-jobs   { background: var(--p-green);  color: var(--c-green); }
.jw-stat-ico.s-cand   { background: var(--p-pink);   color: #DB2777; }
.jw-stat-ico.s-up     { background: var(--p-orange); color: var(--c-orange); }
.jw-stat-meta span {
    display: block; font-size: 10px; color: var(--gr); line-height: 1.2;
    margin-top: 1px;
}

/* ── Full-Width Transparent Footer Bar (100% Length across bottom) ── */
.jw-full-foot {
    width: 100%;
    flex-shrink: 0;
    padding: 10px 36px 14px;
    background: transparent;
    border-top: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    position: relative;
    z-index: 10;
}
.jw-full-foot .copy {
    font-size: 11px;
    color: #64748B;
    font-weight: 400;
}
.jw-full-foot .links {
    display: flex;
    align-items: center;
    gap: 24px;
}
.jw-full-foot .links a {
    font-size: 11.5px;
    color: #475569;
    text-decoration: none;
    font-weight: 500;
    transition: color .15s;
}
.jw-full-foot .links a:hover {
    color: var(--pri);
}
.jw-locale {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #FFFFFF;
    border: 1.5px solid #CBD5E1;
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 11.5px;
    font-weight: 500;
    color: #334155;
    cursor: pointer;
    transition: background .15s, border-color .15s;
}
.jw-locale:hover {
    background: #F8FAFC;
    border-color: #94A3B8;
}

/* ═══════════════ RIGHT PANEL ═══════════════ */
.jw-right {
    flex: 1;
    background: transparent;
    display: flex;
    flex-direction: column;
    min-width: 0;
    position: relative;
    height: 100vh;
    overflow: hidden;
}

/* Top bar */
.jw-top {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    padding: 14px 0 0;
    width: 100%;
    max-width: 520px;
    margin: 0 auto;
    height: 50px;
    font-size: 13px;
    color: var(--gr);
    flex-shrink: 0;
}
.jw-top-cta {
    color: var(--pri); text-decoration: none; font-weight: 500;
}
.jw-top-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: transparent;
    color: var(--pri);
    border: 1.5px solid var(--pri);
    border-radius: 8px;
    height: 36px;
    padding: 0 14px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: background .2s, color .2s;
}
.jw-top-btn:hover { background: var(--pri); color: #fff; }

/* Right body */
.jw-body {
    flex: 1;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 2px 28px 8px;
    overflow-y: auto;
    scrollbar-width: thin;
    min-height: 0;
}

/* Card */
.jw-card-form {
    width: 100%;
    max-width: 520px;
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(226, 232, 240, 0.9);
    border-radius: var(--r-xl);
    padding: 18px 32px 16px;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.07), 0 1px 3px rgba(15, 23, 42, 0.03);
}
.jw-card-form h2 {
    font-size: 20px; font-weight: 800; color: var(--dk);
    margin: 0 0 3px; letter-spacing: -0.5px; text-align: center;
}
.jw-card-form .sub {
    font-size: 12px; color: var(--gr); text-align: center;
    margin: 0 0 12px;
}


/* Alerts */
.jw-alert {
    border-radius: var(--r-sm); padding: 10px 14px;
    font-size: 12.5px; margin-bottom: 14px; line-height: 1.5;
}
.jw-alert-err { background: #FEF2F2; color: #B91C1C; border: 1px solid #FEE2E2; }
.jw-alert-ok  { background: #F0FDF4; color: #15803D; border: 1px solid #BBF7D0; }
.jw-err { font-size: 11.5px; color: #DC2626; margin-top: 4px; display: block; }

/* Field */
.jw-field { margin-bottom: 8px; }
.jw-field > label {
    display: block; font-size: 11.5px; font-weight: 600;
    color: var(--dk); margin-bottom: 4px;
}
.jw-iw { position: relative; display: flex; align-items: center; }
.jw-iw .il {
    position: absolute; left: 14px; color: var(--gr-l);
    display: flex; pointer-events: none;
}
.jw-iw .ir {
    position: absolute; right: 14px; color: var(--gr-l);
    display: flex; cursor: pointer;
}
.jw-inp {
    width: 100%; height: 40px;
    border: 1.5px solid var(--bd); border-radius: var(--r-md);
    padding: 0 42px;
    font-size: 13px; font-family: 'Poppins', sans-serif;
    color: var(--dk); outline: none; background: #fff;
    transition: border-color .2s, box-shadow .2s;
}
.jw-inp::placeholder { color: var(--gr-l); }
.jw-inp:hover { border-color: #C9CFDB; }
.jw-inp:focus { border-color: var(--pri); box-shadow: 0 0 0 4px rgba(36,99,255,0.12); }
.jw-inp.is-ok { border-color: var(--ok); }
.jw-inp.is-ok:focus { box-shadow: 0 0 0 4px rgba(22,163,74,0.14); }
.jw-iw .ok-mark {
    position: absolute; right: 14px; color: var(--ok);
    display: flex; pointer-events: none;
}

/* Mid row */
.jw-mid {
    display: flex; align-items: center; justify-content: space-between;
    margin: 2px 0 8px;
}
.jw-rem { display: flex; align-items: center; gap: 8px; }
.jw-rem input[type=checkbox] {
    width: 16px; height: 16px; accent-color: var(--pri);
    cursor: pointer; flex-shrink: 0; border-radius: 4px;
}
.jw-rem label { font-size: 12px; color: var(--tx); cursor: pointer; user-select: none; }
.jw-fgt {
    font-size: 12px; font-weight: 600; color: var(--pri);
    text-decoration: none; cursor: pointer;
}
.jw-fgt:hover { text-decoration: underline; color: var(--pri-h); }

/* Security verification block */
.jw-sec {
    margin-bottom: 10px;
}
.jw-sec-head {
    display: flex; align-items: center; gap: 6px;
    margin-bottom: 6px;
}
.jw-sec-icon {
    width: 14px; height: 14px; color: var(--pri);
    display: flex; align-items: center; justify-content: center;
}
.jw-sec-icon svg { width: 14px; height: 14px; }
.jw-sec-title {
    font-size: 12px; font-weight: 700; color: var(--dk);
}
.jw-sec-bracket {
    font-size: 11px; color: var(--gr); font-weight: 400;
}
.jw-cap-row {
    display: flex; align-items: center; gap: 8px;
}
.jw-cap-box {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    height: 40px;
    padding: 0 6px 0 10px;
    background: #FFFFFF;
    border: 1.5px solid var(--bd);
    border-radius: var(--r-md);
    box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.03);
    flex: 0 0 auto;
}
.jw-cap-code-img {
    height: 30px;
    width: auto;
    display: block;
    user-select: none;
    border-radius: 4px;
}
.jw-cap-refresh-btn {
    width: 28px;
    height: 28px;
    background: transparent;
    border: none;
    color: var(--pri);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: background .15s, color .15s;
    padding: 0;
}
.jw-cap-refresh-btn:hover {
    background: var(--pri-l);
    color: var(--pri-h);
}
.jw-cap-refresh-btn svg {
    width: 14px;
    height: 14px;
}
.jw-cap-field { flex: 1; position: relative; }
.jw-cap-inp {
    width: 100%; height: 40px;
    border: 1.5px solid var(--bd); border-radius: var(--r-md);
    padding: 0 12px;
    font-size: 13px; font-weight: 500;
    font-family: 'Poppins', sans-serif; outline: none; background: #fff;
    color: var(--dk); transition: border-color .2s, box-shadow .2s;
}
.jw-cap-inp::placeholder { color: var(--gr-l); font-weight: 400; }
.jw-cap-inp:focus { border-color: var(--pri); box-shadow: 0 0 0 4px rgba(36,99,255,0.12); }
.jw-cap-inp.is-ok { border-color: var(--ok); }

/* Sign-in button */
.jw-btn {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    width: 100%; height: 42px;
    background: var(--pri); color: #fff;
    border: none; border-radius: var(--r-md);
    font-size: 13.5px; font-weight: 600;
    font-family: 'Poppins', sans-serif; cursor: pointer;
    transition: background .2s, box-shadow .2s, transform .05s;
    box-shadow: var(--shadow-btn);
    letter-spacing: 0.2px;
}
.jw-btn:hover { background: var(--pri-h); box-shadow: var(--shadow-btn-h); }
.jw-btn:active { transform: translateY(1px); }
.jw-btn .arrow { display: flex; transition: transform .2s; }
.jw-btn:hover .arrow { transform: translateX(4px); }
.jw-btn[disabled] { opacity: 0.7; cursor: not-allowed; transform: none; }

/* Forgot form */
.jw-fgt-acts { display: flex; gap: 12px; margin-top: 18px; }
.jw-fgt-sub {
    flex: 1; height: 50px; background: var(--pri); color: #fff;
    border: none; border-radius: var(--r-md);
    font-size: 14px; font-weight: 600;
    font-family: 'Poppins', sans-serif; cursor: pointer;
    transition: background .2s, box-shadow .2s;
    box-shadow: var(--shadow-btn);
}
.jw-fgt-sub:hover { background: var(--pri-h); box-shadow: var(--shadow-btn-h); }
.jw-fgt-cancel {
    height: 50px; padding: 0 20px; background: #F3F4F6; color: #374151;
    border: none; border-radius: var(--r-md);
    font-size: 14px; font-weight: 600;
    font-family: 'Poppins', sans-serif; text-decoration: none;
    display: inline-flex; align-items: center; cursor: pointer;
    transition: background .2s;
}
/* Inside Card Trust Strip (Matching attached screenshot) */
.jw-card-trust {
    margin-top: 14px;
    background: #F8FAFC;
    border: 1px solid #F1F5F9;
    border-radius: var(--r-md);
    padding: 9px 12px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    align-items: center;
}
.jw-ct-item {
    display: flex;
    align-items: center;
    gap: 7px;
    min-width: 0;
}
.jw-ct-ico {
    width: 22px;
    height: 22px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.jw-ct-ico svg { width: 14px; height: 14px; }
.jw-ct-ico.t-green { color: #16A34A; }
.jw-ct-ico.t-blue { color: #2563EB; }
.jw-ct-ico.t-blue2 { color: #0284C7; }

.jw-ct-meta { min-width: 0; }
.jw-ct-meta strong {
    display: block;
    font-size: 10.5px;
    font-weight: 700;
    color: #0F172A;
    line-height: 1.15;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.jw-ct-meta span {
    display: block;
    font-size: 9px;
    color: #64748B;
    line-height: 1.15;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 1px;
}

/* ── Responsive ─────────────────────────────────────────────── */

/* ── Responsive ────────────────────────────────────────── */

/* 1440px */
@media (max-width: 1440px) {
    .jw-hero h1 { font-size: 36px !important; }
    .jw-left { padding: 16px 30px 12px; }
}

/* 1366 — most 15.6" laptops */
@media (max-width: 1366px) {
    .jw-hero h1 { font-size: 36px !important; letter-spacing: -0.6px; }
    .jw-hero { margin: 16px 0 8px; }
    .jw-hero p { font-size: 12px; -webkit-line-clamp: 2; }
    .jw-left { flex: 1; padding: 14px 22px 10px; }
    .jw-right { flex: 1; }
    .jw-illustration-link { aspect-ratio: 1152 / 768; max-height: 420px; margin-bottom: 10px; }
    .jw-card-form { max-width: 480px; padding: 22px 30px 18px; }
    .jw-card-form h2 { font-size: 20px; }
    .jw-inp { height: 40px; font-size: 13px; }
    .jw-cap-img, .jw-cap-inp { height: 38px; }
    .jw-btn { height: 42px; font-size: 13.5px; }
}

/* 1280px */
@media (max-width: 1280px) {
    .jw-hero h1 { font-size: 36px !important; }
}

/* Tablet */
@media (max-width: 1024px) {
    .jw-page { flex-direction: column; height: auto; }
    .jw-left, .jw-right { height: auto; }
    .jw-left { padding: 22px 22px 18px; }
    .jw-illustration-link { aspect-ratio: 1152 / 768; max-height: none; }
    .jw-body { padding: 22px 24px; overflow-y: auto; }
    .jw-card-form { max-width: 100%; }
}

/* Mobile */
@media (max-width: 700px) {
    .jw-illustration-link { margin-bottom: 12px; }
    .jw-hero h1 { font-size: 30px; }
    .jw-left { padding: 20px 18px; }
    .jw-body { padding: 18px; }
    .jw-card-form { padding: 20px 18px; border-radius: 16px; }
    .jw-card-form h2 { font-size: 22px; }
}

@media (max-width: 480px) {
    .jw-hero h1 { font-size: 26px; }
    .jw-top-btn { padding: 6px 12px; font-size: 12px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
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

<div class="jw-shell">
<div class="jw-page">

{{-- ═══════════════ LEFT PANEL ═══════════════ --}}
<div class="jw-left">

    {{-- Official SVG Brand Logo --}}
    <a href="https://jero.works" class="jw-brand" aria-label="jero.works">
        <img src="{{ asset('img/jero-logo-black-yellow.svg') }}" class="jw-brand-logo" alt="jero.works">
    </a>

    {{-- Hero --}}
    <div class="jw-hero">
        <h1>One Platform.<br>Every <span class="acc">Hiring Need.</span></h1>
        <p>Connect companies, agencies and talent on one intelligent recruitment platform built for speed and trust.</p>
    </div>

    {{-- Combined 3-in-1 Audience Illustration (All 3-in-1) --}}
    <a href="{{ route('register') }}" class="jw-illustration-link" aria-label="Find Trusted Agencies, Search Work Opportunities, and Hire Immediate Joiners">
        <picture>
            <source srcset="{{ asset('img/cards/all3-audiences.webp') }}" type="image/webp">
            <img class="jw-illustration-img"
                 src="{{ asset('img/cards/all-3in-one.svg') }}"
                 alt="Find Trusted Agencies, Search Work Opportunities, and Hire Immediate Joiners"
                 width="1152" height="768"
                 decoding="async">
        </picture>
    </a>

    {{-- Stats --}}
    <div class="jw-stats">
        <div class="jw-stat">
            <div class="jw-stat-ico s-people" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3.2" fill="currentColor"/><circle cx="16" cy="8" r="3.2" fill="currentColor" opacity=".55"/><path d="M2 21c0-3.87 3.13-7 7-7h5c3.87 0 7 3.13 7 7" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/></svg>
            </div>
            <div class="jw-stat-meta">
                <strong>15K+</strong>
                <span>Enterprise Clients</span>
            </div>
        </div>
        <div class="jw-stat">
            <div class="jw-stat-ico s-jobs" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="7" width="18" height="13" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2"/></svg>
            </div>
            <div class="jw-stat-meta">
                <strong>25K+</strong>
                <span>Active Jobs</span>
            </div>
        </div>
        <div class="jw-stat">
            <div class="jw-stat-ico s-cand" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5" fill="currentColor"/><path d="M5 20c0-3.87 3.13-7 7-7s7 3.13 7 7" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/></svg>
            </div>
            <div class="jw-stat-meta">
                <strong>250K+</strong>
                <span>Talent Profiles</span>
            </div>
        </div>
        <div class="jw-stat">
            <div class="jw-stat-ico s-up" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><path d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16M9 9h2M13 9h2M9 13h2M13 13h2M9 17h2M13 17h2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div class="jw-stat-meta">
                <strong>20K+</strong>
                <span>Agencies</span>
            </div>
        </div>
    </div>

</div>{{-- /jw-left --}}

{{-- ═══════════════ RIGHT PANEL ═══════════════ --}}
<div class="jw-right">

    {{-- Top bar --}}
    <div class="jw-top">
        <span>New to Jero.works?</span>
        <a href="{{ route('register') }}" class="jw-top-btn">
            Create Free Account
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </div>

    {{-- Body --}}
    <div class="jw-body">

        {{-- LOGIN FORM --}}
        <form class="jw-card-form" novalidate id="kt_sign_in_form1" action="{{ route('login') }}" method="post" style="<?php echo $loginForm; ?>">
            @csrf

            <h2>Welcome Back!</h2>
            <p class="sub">Sign in to continue to your jero.works account</p>

            @if(@$class && @$messsage)
                <div class="jw-alert jw-alert-err"><strong>{{ @$class }}</strong> {!! @$messsage !!}</div>
            @endif
            @if(session('accountnotverify'))
                <div class="jw-alert jw-alert-err">{{ session('accountnotverify') }}</div>
            @endif
            @if($errors->has('device_message'))
                <div class="jw-alert jw-alert-err">{{ $errors->first('device_message') }}</div>
            @endif

            {{-- Email --}}
            <div class="jw-field">
                <label for="jw-email">Email Address</label>
                <div class="jw-iw">
                    <span class="il" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="4" width="16" height="11" rx="2.2" stroke="currentColor" stroke-width="1.5"/><path d="M1 7l8 5.5L17 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    </span>
                    <input class="jw-inp" type="text" name="email" id="jw-email" placeholder="Enter your email" autocomplete="username" value="{{ old('email') }}" aria-describedby="jw-email-help"/>
                    <span class="ok-mark" id="jw-email-ok" style="display:none;" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8.4" fill="#16A34A"/><path d="M5 9l3 3 5-5.5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <input type="hidden" name="url" value="{{ @$url }}"/>
                </div>
                @if($errors->has('email'))<span class="jw-err" id="jw-email-help">{{ $errors->first('email') }}</span>@endif
            </div>

            {{-- Password --}}
            <div class="jw-field">
                <label for="jw-pwd">Password</label>
                <div class="jw-iw">
                    <span class="il" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="3" y="8" width="12" height="9" rx="2.2" stroke="currentColor" stroke-width="1.5"/><path d="M6 8V6a3 3 0 0 1 6 0v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="9" cy="12.5" r="1.3" fill="currentColor"/></svg>
                    </span>
                    <input class="jw-inp" type="password" name="password" id="jw-pwd" placeholder="Enter your password" autocomplete="current-password" aria-describedby="jw-pwd-help"/>
                    <span class="ir" onclick="jwEye()" role="button" tabindex="0" aria-label="Toggle password visibility" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();jwEye();}">
                        <svg id="jw-eye" width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M1.5 9s2.8-5.5 7.5-5.5S16.5 9 16.5 9s-2.8 5.5-7.5 5.5S1.5 9 1.5 9Z" stroke="currentColor" stroke-width="1.5"/><circle cx="9" cy="9" r="2.2" stroke="currentColor" stroke-width="1.5"/></svg>
                    </span>
                </div>
                @if($errors->has('password'))<span class="jw-err" id="jw-pwd-help"><strong>{{ $errors->first('password') }}</strong></span>@endif
            </div>

            {{-- Remember + Forgot --}}
            <div class="jw-mid">
                <div class="jw-rem">
                    <input type="checkbox" name="remember" id="jw-rem" {{ old('remember') ? 'checked' : '' }}>
                    <label for="jw-rem">Remember me</label>
                </div>
                <a href="javascript:void(0);" id="to-recover" class="jw-fgt">Forgot password?</a>
            </div>

            {{-- Security verification (CAPTCHA) --}}
            <div class="jw-sec">
                <div class="jw-sec-head">
                    <span class="jw-sec-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="currentColor" stroke-width="1.8" fill="none"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
                    </span>
                    <span class="jw-sec-title">Security verification</span>
                    <span class="jw-sec-bracket">{Enter the code shown below}</span>
                </div>

                <div class="jw-cap-row">
                    <div class="jw-cap-box">
                        <img src="{{ route('captcha.generate') }}" id="captchaImg" alt="Security code" class="jw-cap-code-img">
                        <button type="button" onclick="refreshCaptcha()" class="jw-cap-refresh-btn" aria-label="Refresh security code" title="Refresh security code">
                            <svg viewBox="0 0 24 24" fill="none"><path d="M21 12a9 9 0 1 1-3.5-7.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M21 4v5h-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                    <div class="jw-cap-field">
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="4" name="captcha" class="jw-cap-inp" id="jw-cap" placeholder="Enter code" autocomplete="off" required aria-describedby="jw-cap-help">
                    </div>
                </div>
                @if($errors->has('captcha'))
                    <span class="jw-err" id="jw-cap-help" style="margin-top:6px;display:block">{{ $errors->first('captcha') }}</span>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit" id="kt_sign_in_submit" class="jw-btn">
                <span class="indicator-label">Sign In</span>
                <span class="arrow" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M3 10h13M11 5l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="indicator-progress" style="display:none" aria-live="polite">Signing in…&nbsp;<span class="spinner-border spinner-border-sm align-middle"></span></span>
            </button>
            {{-- In-Card Trust Footer (Matching attached mockup) --}}
            <div class="jw-card-trust">
                <div class="jw-ct-item">
                    <div class="jw-ct-ico t-green">
                        <svg viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="currentColor" stroke-width="2"/></svg>
                    </div>
                    <div class="jw-ct-meta">
                        <strong>Secure Login</strong>
                        <span>256-bit encryption</span>
                    </div>
                </div>
                <div class="jw-ct-item">
                    <div class="jw-ct-ico t-blue">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="currentColor" stroke-width="2" fill="none"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
                    </div>
                    <div class="jw-ct-meta">
                        <strong>Privacy Protected</strong>
                        <span>Your data is safe</span>
                    </div>
                </div>
                <div class="jw-ct-item">
                    <div class="jw-ct-ico t-blue2">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="currentColor" stroke-width="2" fill="none"/><path d="M8 12l3 3 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
                    </div>
                    <div class="jw-ct-meta">
                        <strong>99.9% Uptime</strong>
                        <span>Reliable platform</span>
                    </div>
                </div>
            </div>
        </form>

        {{-- FORGOT PASSWORD FORM --}}
        <form class="jw-card-form" novalidate id="recoverform" action="{{ route('password.emailsend') }}" method="post" style="<?php echo $resetForm; ?>">
            {{ csrf_field() }}
            <h2>Forgot Password?</h2>
            <p class="sub">Enter your email to reset your password.</p>

            @if(session('status'))
                <div class="jw-alert jw-alert-ok">{{ session('status') }}</div>
            @endif
            @if(session('accountnotverify'))
                <div class="jw-alert jw-alert-err">{{ session('accountnotverify') }}</div>
            @endif

            <div class="jw-field {{ $errors->has('email') ? 'has-error' : '' }}">
                <label for="email">Email Address</label>
                <div class="jw-iw">
                    <span class="il" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="4" width="16" height="11" rx="2.2" stroke="currentColor" stroke-width="1.5"/><path d="M1 7l8 5.5L17 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    </span>
                    <input type="email" name="email" id="email"
                           class="jw-inp {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           placeholder="Enter your email" value="{{ old('email') }}"
                           required autocomplete="email">
                </div>
                @if($errors->has('email'))<span class="jw-err"><strong>{{ $errors->first('email') }}</strong></span>@endif
            </div>

            <div class="jw-fgt-acts">
                <button type="submit" id="reset_password" class="jw-fgt-sub">
                    <span class="indicator-label">Reset Password</span>
                    <span class="indicator-progress" style="display:none">Please wait…&nbsp;<span class="spinner-border spinner-border-sm align-middle"></span></span>
                </button>
                <a href="{{ route('login') }}" class="jw-fgt-cancel">Cancel</a>
            </div>
        </form>

    </div>{{-- /jw-body --}}

</div>{{-- /jw-right --}}

</div>{{-- /jw-page --}}

    {{-- Full-Length Complete Footer Bar --}}
    <footer class="jw-full-foot">
        <span class="copy">© 2026 Jero Technologies Pvt. Ltd. All rights reserved.</span>
        <div class="links">
            <a href="/privacy-policy">Privacy Policy</a>
            <a href="/terms">Terms of Service</a>
            <a href="/support">Support</a>
            <button type="button" class="jw-locale" aria-label="Language selector">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/>
                    <path d="M3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18" stroke="currentColor" stroke-width="1.6"/>
                </svg>
                English
                <svg width="10" height="10" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                    <path d="M3 5l3 3 3-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </footer>

</div>{{-- /jw-shell --}}
@endsection

@push('scripts')
<script>
function refreshCaptcha() {
    var img = document.getElementById('captchaImg');
    img.src = "{{ route('captcha.generate') }}?t=" + Date.now();
    var c = document.getElementById('jw-cap');
    if (c) { c.value = ''; c.classList.remove('is-ok'); c.focus(); }
}

function jwEye() {
    var i = document.getElementById('jw-pwd'), e = document.getElementById('jw-eye');
    if (i.type === 'password') {
        i.type = 'text';
        e.innerHTML = '<path d="M1.5 9s2.8-5.5 7.5-5.5S16.5 9 16.5 9s-2.8 5.5-7.5 5.5S1.5 9 1.5 9Z" stroke="currentColor" stroke-width="1.5"/><circle cx="9" cy="9" r="2.2" stroke="currentColor" stroke-width="1.5"/><line x1="2" y1="2" x2="17" y2="17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>';
        e.setAttribute('aria-label', 'Hide password');
    } else {
        i.type = 'password';
        e.innerHTML = '<path d="M1.5 9s2.8-5.5 7.5-5.5S16.5 9 16.5 9s-2.8 5.5-7.5 5.5S1.5 9 1.5 9Z" stroke="currentColor" stroke-width="1.5"/><circle cx="9" cy="9" r="2.2" stroke="currentColor" stroke-width="1.5"/>';
        e.setAttribute('aria-label', 'Show password');
    }
}

// Email validation — only show genuine OK state (RFC-ish + valid TLD)
document.addEventListener('DOMContentLoaded', function () {
    var email = document.getElementById('jw-email');
    var emailOk = document.getElementById('jw-email-ok');
    var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    function checkEmail() {
        if (!email || !emailOk) return;
        var ok = emailRe.test(email.value.trim());
        emailOk.style.display = ok ? 'flex' : 'none';
        email.classList.toggle('is-ok', ok);
    }
    if (email) { checkEmail(); email.addEventListener('input', checkEmail); }

    var cap = document.getElementById('jw-cap');
    function checkCap() {
        if (!cap) return;
        var ok = cap.value.trim().length >= 4;
        cap.classList.toggle('is-ok', ok);
    }
    if (cap) { checkCap(); cap.addEventListener('input', checkCap); }

    // Prevent double submit + show progress
    var btn = document.getElementById('kt_sign_in_submit');
    var form = document.getElementById('kt_sign_in_form1');
    if (form && btn) {
        form.addEventListener('submit', function () {
            var label = btn.querySelector('.indicator-label');
            var prog = btn.querySelector('.indicator-progress');
            var arrow = btn.querySelector('.arrow');
            if (label) label.style.display = 'none';
            if (arrow) arrow.style.display = 'none';
            if (prog) prog.style.display = 'inline-flex';
            btn.setAttribute('disabled', 'disabled');
        });
    }
});
</script>
@endpush
