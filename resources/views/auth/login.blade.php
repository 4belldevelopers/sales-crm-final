@extends('layouts.metronic_front_app')
<title>Sign In — jero.works</title>

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
body { background: var(--bg-pg); font-family: 'Poppins', system-ui, -apple-system, sans-serif; color: var(--tx); }

/* ── Shell ─────────────────────────────────────────────────── */
.jw-shell {
    height: 100vh;
    width: 100%;
    display: flex;
    overflow: hidden;
    background: var(--bg-pg);
}
.jw-page {
    display: flex;
    flex: 1;
    width: 100%;
    height: 100vh;
    overflow: hidden;
}

/* ═══════════════ LEFT PANEL ═══════════════ */
.jw-left {
    flex: 1.1;
    min-width: 0;
    background: var(--bg-pn);
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
    padding: 22px 36px 18px;
    min-height: 0;
    height: 100vh;
}

/* Subtle decorative dotted wash */
.jw-left::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(36,99,255,0.06) 1px, transparent 1px);
    background-size: 22px 22px;
    opacity: 0.5;
    pointer-events: none;
}

/* Brand mark */
.jw-brand {
    display: inline-flex; align-items: center; gap: 9px;
    text-decoration: none; line-height: 1;
    position: relative;
}
.jw-brand-mark { height: 32px; width: auto; display: block; }
.jw-brand-name {
    font-size: 21px; font-weight: 800; color: var(--dk);
    letter-spacing: -0.6px;
    font-family: 'Poppins', sans-serif;
}
.jw-brand-sub {
    display: block; font-size: 10.5px; color: var(--gr);
    font-weight: 500; margin-top: 4px; margin-left: 41px;
    letter-spacing: 0.15px;
}

/* Hero */
.jw-hero { margin: 18px 0 14px; max-width: 100%; position: relative; }
.jw-hero h1 {
    font-size: 34px;
    font-weight: 800;
    color: var(--dk);
    line-height: 1.15;
    letter-spacing: -1.2px;
    margin: 0 0 10px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.jw-hero h1 .acc { color: var(--acc); }
.jw-hero p {
    font-size: 13px; color: var(--gr); line-height: 1.55;
    margin: 0; font-weight: 400;
    max-width: 100%;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ── 3 Image Cards (art-directed assets, no CSS rebuild) ──── */
.jw-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-bottom: 16px;
    position: relative;
    align-items: stretch;
    height: 300px;          /* fixed row height — equalizes all 3 cards */
}
.jw-card-link {
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: inherit;
    border-radius: 0;
    background: transparent;
    padding: 0;
    height: 100%;
    transition: transform .2s ease;
}
.jw-card-link:hover,
.jw-card-link:focus-visible {
    transform: translateY(-3px);
    outline: none;
}
.jw-card-img {
    display: block;
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    margin: 0 auto;
    object-fit: contain;
    object-position: center center;
    /* Never distort, crop, or stretch the art. */
    user-select: none;
    -webkit-user-drag: none;
}

/* ── Stats bar with subtle dividers ────────────────────────── */
.jw-stats {
    background: var(--bg-card);
    border-radius: var(--r-lg);
    padding: 12px 18px;
    display: flex;
    align-items: stretch;
    border: 1px solid rgba(15,23,42,0.04);
    box-shadow: var(--shadow-sm);
    position: relative;
}
.jw-stat {
    flex: 1;
    display: flex; align-items: center; gap: 10px;
    padding: 0 6px;
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
    width: 30px; height: 30px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.jw-stat-ico svg { width: 16px; height: 16px; }
.jw-stat-ico.s-people { background: var(--p-blue);   color: var(--c-blue); }
.jw-stat-ico.s-jobs   { background: var(--p-green);  color: var(--c-green); }
.jw-stat-ico.s-cand   { background: var(--p-pink);   color: #DB2777; }
.jw-stat-ico.s-up     { background: var(--p-orange); color: var(--c-orange); }
.jw-stat-meta strong {
    display: block; font-size: 14px; font-weight: 800; color: var(--dk);
    line-height: 1.1; letter-spacing: -0.3px;
}
.jw-stat-meta span {
    display: block; font-size: 10px; color: var(--gr); line-height: 1.2;
    margin-top: 1px;
}

/* ── Left footer ──────────────────────────────────────────── */
.jw-left-foot {
    margin-top: auto;
    padding-top: 12px;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 8px;
    font-size: 10.5px; color: var(--gr);
    position: relative;
}
.jw-left-foot .links { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
.jw-left-foot a { color: var(--gr); text-decoration: none; transition: color .15s; }
.jw-left-foot a:hover { color: var(--dk); }
.jw-locale {
    display: inline-flex; align-items: center; gap: 5px;
    color: var(--tx); cursor: pointer; font-weight: 500;
    background: transparent; border: none;
    padding: 0;
}
.jw-locale:hover { color: var(--dk); }

/* ═══════════════ RIGHT PANEL ═══════════════ */
.jw-right {
    flex: 1;
    background: var(--bg-pg);
    display: flex;
    flex-direction: column;
    min-width: 0;
    position: relative;
    height: 100vh;
    overflow: hidden;
}

/* Top bar */
.jw-top {
    display: flex; align-items: center; justify-content: flex-end;
    gap: 10px; padding: 12px 36px 0;
    font-size: 13px; color: var(--gr);
    flex-shrink: 0;
}
.jw-top-cta {
    color: var(--pri); text-decoration: none; font-weight: 500;
}
.jw-top-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: transparent;
    color: var(--pri);
    border: 1.5px solid var(--pri);
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 13px; font-weight: 600;
    cursor: pointer; text-decoration: none;
    transition: background .2s, color .2s;
}
.jw-top-btn:hover { background: var(--pri); color: #fff; }

/* Right body */
.jw-body {
    flex: 1;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 4px 36px 10px;
    overflow-y: hidden;
    min-height: 0;
}

/* Card */
.jw-card-form {
    width: 100%;
    max-width: 520px;
    background: var(--bg-card);
    border: 1px solid var(--bd-l);
    border-radius: var(--r-xl);
    padding: 24px 32px 18px;
    box-shadow: var(--shadow-md);
}
.jw-card-form h2 {
    font-size: 22px; font-weight: 800; color: var(--dk);
    margin: 0 0 4px; letter-spacing: -0.5px; text-align: center;
}
.jw-card-form .sub {
    font-size: 12.5px; color: var(--gr); text-align: center;
    margin: 0 0 18px;
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
.jw-field { margin-bottom: 10px; }
.jw-field > label {
    display: block; font-size: 12px; font-weight: 600;
    color: var(--dk); margin-bottom: 5px;
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
    width: 100%; height: 44px;
    border: 1.5px solid var(--bd); border-radius: var(--r-md);
    padding: 0 42px;
    font-size: 13.5px; font-family: 'Poppins', sans-serif;
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
    margin: 2px 0 12px;
}
.jw-rem { display: flex; align-items: center; gap: 8px; }
.jw-rem input[type=checkbox] {
    width: 16px; height: 16px; accent-color: var(--pri);
    cursor: pointer; flex-shrink: 0; border-radius: 4px;
}
.jw-rem label { font-size: 12.5px; color: var(--tx); cursor: pointer; user-select: none; }
.jw-fgt {
    font-size: 12.5px; font-weight: 600; color: var(--pri);
    text-decoration: none; cursor: pointer;
}
.jw-fgt:hover { text-decoration: underline; color: var(--pri-h); }

/* Security verification block */
.jw-sec {
    margin-bottom: 12px;
}
.jw-sec-head {
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 4px;
}
.jw-sec-icon {
    width: 16px; height: 16px; color: var(--pri);
    display: flex; align-items: center; justify-content: center;
}
.jw-sec-icon svg { width: 16px; height: 16px; }
.jw-sec-title {
    font-size: 12.5px; font-weight: 700; color: var(--dk);
}
.jw-sec-sub {
    font-size: 11px; color: var(--gr);
    margin: 0 0 8px 24px;
}
.jw-cap-row {
    display: flex; align-items: center; gap: 8px;
}
.jw-cap-img {
    flex: 0 0 auto;
    height: 42px;
    border: 1.5px solid var(--bd);
    border-radius: var(--r-md);
    overflow: hidden;
    background: #F9FAFB;
    display: flex; align-items: center; justify-content: center;
    padding: 0 8px;
    min-width: 110px;
}
.jw-cap-img img { height: 32px; width: auto; object-fit: contain; }
.jw-cap-btn {
    width: 28px; height: 28px;
    display: inline-flex; align-items: center; justify-content: center;
    background: transparent; color: var(--pri);
    border: 1px solid var(--bd);
    border-radius: 8px;
    cursor: pointer;
    flex-shrink: 0;
    transition: background .15s, border-color .15s, color .15s;
}
.jw-cap-btn:hover { background: var(--pri-l); border-color: var(--pri); color: var(--pri-h); }
.jw-cap-btn svg { width: 13px; height: 13px; }
.jw-cap-field { flex: 1; position: relative; }
.jw-cap-inp {
    width: 100%; height: 42px;
    border: 1.5px solid var(--bd); border-radius: var(--r-md);
    padding: 0 14px;
    font-size: 13.5px; font-weight: 500;
    font-family: 'Poppins', sans-serif; outline: none; background: #fff;
    color: var(--dk); transition: border-color .2s, box-shadow .2s;
}
.jw-cap-inp::placeholder { color: var(--gr-l); font-weight: 400; }
.jw-cap-inp:focus { border-color: var(--pri); box-shadow: 0 0 0 4px rgba(36,99,255,0.12); }
.jw-cap-inp.is-ok { border-color: var(--ok); }

/* Sign-in button */
.jw-btn {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    width: 100%; height: 46px;
    background: var(--pri); color: #fff;
    border: none; border-radius: var(--r-md);
    font-size: 14px; font-weight: 600;
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

/* Sign Up link removed — "Create Free Account" CTA is already in the top bar */

/* Trust strip (3 items, compact) */
.jw-trust {
    width: 100%; max-width: 520px;
    margin-top: 10px;
    background: var(--bg-card);
    border: 1px solid var(--bd-l);
    border-radius: var(--r-lg);
    padding: 8px 12px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
    box-shadow: var(--shadow-sm);
    flex-shrink: 0;
}
.jw-trust-item { display: flex; align-items: center; gap: 7px; min-width: 0; }
.jw-trust-ico {
    width: 22px; height: 22px;
    border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.jw-trust-ico svg { width: 12px; height: 12px; }
.jw-trust-ico.t-secure  { background: var(--p-blue);   color: var(--c-blue); }
.jw-trust-ico.t-private { background: var(--p-green);  color: var(--c-green); }
.jw-trust-ico.t-uptime  { background: var(--p-orange); color: var(--c-orange); }
.jw-trust-meta { min-width: 0; overflow: hidden; }
.jw-trust-meta strong {
    display: block; font-size: 11px; font-weight: 700;
    color: var(--dk); line-height: 1.2;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    letter-spacing: -0.1px;
}
.jw-trust-meta span {
    display: block; font-size: 9.5px; color: var(--gr); line-height: 1.2;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

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
.jw-fgt-cancel:hover { background: #E5E7EB; color: #374151; }

/* ── Responsive ─────────────────────────────────────────────── */

/* 1440 */
@media (max-width: 1440px) {
    .jw-hero h1 { font-size: 30px; }
    .jw-left { padding: 18px 32px 14px; }
}

/* 1366 — most 15.6" laptops */
@media (max-width: 1366px) {
    .jw-hero h1 { font-size: 26px; letter-spacing: -0.8px; }
    .jw-hero { margin: 12px 0 10px; }
    .jw-hero p { font-size: 12.5px; }
    .jw-left { padding: 14px 28px 12px; }
    .jw-cards { gap: 8px; margin-bottom: 12px; height: 260px; }
    .jw-stats { padding: 10px 14px; }
    .jw-stat { gap: 8px; }
    .jw-stat-ico { width: 26px; height: 26px; }
    .jw-stat-ico svg { width: 14px; height: 14px; }
    .jw-stat-meta strong { font-size: 13px; }
    .jw-stat-meta span { font-size: 9.5px; }
    .jw-card-form { padding: 22px 28px 16px; }
    .jw-card-form h2 { font-size: 22px; }
    .jw-inp { height: 44px; font-size: 13.5px; }
    .jw-cap-img, .jw-cap-inp { height: 44px; }
    .jw-btn { height: 46px; font-size: 14px; }
    .jw-trust { padding: 9px 12px; }
}

/* 1280 */
@media (max-width: 1280px) {
    .jw-hero h1 { font-size: 24px; }
}

/* Tablet */
@media (max-width: 1024px) {
    .jw-page { flex-direction: column; height: auto; }
    .jw-left, .jw-right { height: auto; }
    .jw-left { padding: 24px; }
    .jw-cards {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        height: 280px;
    }
    .jw-card-link:last-child { grid-column: span 2; max-width: 50%; justify-self: center; }
    .jw-stats { flex-wrap: wrap; }
    .jw-stat { flex: 1 1 45%; }
    .jw-stat + .jw-stat::before { display: none; }
    .jw-body { padding: 22px 24px; overflow-y: auto; }
    .jw-card-form, .jw-trust { max-width: 100%; }
    .jw-trust { grid-template-columns: 1fr; }
}

/* Mobile — horizontal-swipe carousel so all 3 cards stay accessible */
@media (max-width: 700px) {
    .jw-cards {
        grid-template-columns: none;
        display: flex;
        gap: 12px;
        height: 240px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        padding: 4px 2px 12px;
        margin: 0 -18px 12px;
        padding-left: 18px;
        padding-right: 18px;
    }
    .jw-card-link {
        flex: 0 0 70%;
        scroll-snap-align: start;
    }
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

    {{-- Brand --}}
    <a href="https://jero.works" class="jw-brand">
        <svg class="jw-brand-mark" viewBox="0 0 38 44" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
            <circle cx="19" cy="2.6" r="1.9" fill="#F5C518"/>
            <path d="M19 3.6 C10 3.6 2.6 9.6 1 17.6 C0.5 19.6 2 21 4 21 H34 C36 21 37.5 19.6 37 17.6 C35.4 9.6 28 3.6 19 3.6 Z" fill="#071A3A"/>
            <path d="M1.4 18.6 C2 22 4.4 24.4 7.4 24.4 C10 24.4 12.2 22.6 13 20 C13.8 22.6 16 24.4 19 24.4 C22 24.4 24.2 22.6 25 20 C25.8 22.6 28 24.4 30.6 24.4 C33.6 24.4 36 22 36.6 18.6 C36.4 19.6 36 20.4 35.4 21 H2.6 C2 20.4 1.6 19.6 1.4 18.6 Z" fill="#071A3A"/>
            <rect x="18.2" y="24" width="1.6" height="11" fill="#071A3A"/>
            <path d="M19.8 35 C19.8 37.4 18 39.4 15.6 39.8 C13.6 40.2 11.6 39.4 10.6 37.6 C10.2 37 10.4 36.2 11 35.8 C11.6 35.4 12.4 35.6 12.8 36.2 C13.4 37.2 14.4 37.6 15.4 37.4 C16.6 37.2 17.4 36.2 17.4 35" stroke="#071A3A" stroke-width="1.6" fill="none" stroke-linecap="round"/>
        </svg>
        <span class="jw-brand-name">jero.works</span>
    </a>
    <span class="jw-brand-sub">AI Recruitment Platform</span>

    {{-- Hero --}}
    <div class="jw-hero">
        <h1>One Platform. Every <span class="acc">Hiring Need.</span></h1>
        <p>Connect companies, agencies and talent on one intelligent recruitment platform built for speed and trust.</p>
    </div>

    {{-- 3 Audience Cards (art-directed image assets) --}}
    <div class="jw-cards">

        {{-- Card 1: Find Trusted Agencies (For Companies) --}}
        <a href="{{ route('register') }}" class="jw-card-link" aria-label="Find Trusted Agencies for Companies">
            <img class="jw-card-img"
                 src="{{ asset('img/cards/find-trusted-agencies.webp') }}"
                 alt="Find Trusted Agencies for Companies"
                 width="1024" height="1536"
                 decoding="async">
        </a>

        {{-- Card 2: Find Hiring Opportunities (For Agencies) --}}
        <a href="{{ route('register') }}" class="jw-card-link" aria-label="Find Hiring Opportunities for Recruitment Agencies">
            <img class="jw-card-img"
                 src="{{ asset('img/cards/find-hiring-opportunities.webp') }}"
                 alt="Find Hiring Opportunities for Recruitment Agencies"
                 width="1254" height="1254"
                 decoding="async">
        </a>

        {{-- Card 3: Find Bench Talents (For Bench Talent) --}}
        <a href="{{ route('register') }}" class="jw-card-link" aria-label="Find Bench Talents and Immediate Joiners">
            <img class="jw-card-img"
                 src="{{ asset('img/cards/find-bench-talents.webp') }}"
                 alt="Find Bench Talents and Immediate Joiners"
                 width="1254" height="1254"
                 decoding="async">
        </a>
    </div>

    {{-- Stats --}}
    <div class="jw-stats">
        <div class="jw-stat">
            <div class="jw-stat-ico s-people" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3.2" fill="currentColor"/><circle cx="16" cy="8" r="3.2" fill="currentColor" opacity=".55"/><path d="M2 21c0-3.87 3.13-7 7-7h5c3.87 0 7 3.13 7 7" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/></svg>
            </div>
            <div class="jw-stat-meta">
                <strong>150+</strong>
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
                <svg viewBox="0 0 24 24" fill="none"><path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="currentColor" stroke-width="1.8" fill="none"/><path d="M8 12l3 3 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
            </div>
            <div class="jw-stat-meta">
                <strong>99.9%</strong>
                <span>Platform Uptime</span>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="jw-left-foot">
        <span>© 2026 Jero Technologies Pvt. Ltd. All rights reserved.</span>
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
    </div>

</div>{{-- /jw-left --}}

{{-- ═══════════════ RIGHT PANEL ═══════════════ --}}
<div class="jw-right">

    {{-- Top bar --}}
    <div class="jw-top">
        <span>New to Jero?</span>
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
                    <input class="jw-inp" type="text" name="email" id="jw-email" placeholder="Enter your email" autocomplete="off" value="{{ old('email') }}" aria-describedby="jw-email-help"/>
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
                    <input class="jw-inp" type="password" name="password" id="jw-pwd" placeholder="Enter your password" autocomplete="off" aria-describedby="jw-pwd-help"/>
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
                </div>
                <p class="jw-sec-sub">Enter the code shown below</p>
                <div class="jw-cap-row">
                    <div class="jw-cap-img">
                        <img src="{{ route('captcha.generate') }}" id="captchaImg" alt="Security code">
                    </div>
                    <button type="button" onclick="refreshCaptcha()" class="jw-cap-btn" aria-label="Refresh security code">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M21 12a9 9 0 1 1-3.5-7.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M21 4v5h-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
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

            {{-- Sign Up link removed — "Create Free Account" CTA is already in the top bar --}}
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
                           required autocomplete="off">
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

        {{-- Trust strip (3 items, no false cert claims) --}}
        <div class="jw-trust" role="list">
            <div class="jw-trust-item" role="listitem">
                <div class="jw-trust-ico t-secure" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="currentColor" stroke-width="2"/></svg>
                </div>
                <div class="jw-trust-meta">
                    <strong>Secure Login</strong>
                    <span>256-bit encryption</span>
                </div>
            </div>
            <div class="jw-trust-item" role="listitem">
                <div class="jw-trust-ico t-private" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="currentColor" stroke-width="2" fill="none"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
                </div>
                <div class="jw-trust-meta">
                    <strong>Privacy Protected</strong>
                    <span>Your data is safe</span>
                </div>
            </div>
            <div class="jw-trust-item" role="listitem">
                <div class="jw-trust-ico t-uptime" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="currentColor" stroke-width="2" fill="none"/><path d="M8 12l3 3 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
                </div>
                <div class="jw-trust-meta">
                    <strong>99.9% Uptime</strong>
                    <span>Reliable platform</span>
                </div>
            </div>
        </div>

    </div>{{-- /jw-body --}}

</div>{{-- /jw-right --}}

</div>{{-- /jw-page --}}
</div>{{-- /jw-shell --}}
@endsection

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
