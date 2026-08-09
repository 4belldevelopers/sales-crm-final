@extends('layouts.metronic_front_app')
<title>jero.works - Login</title>

<style>
/* ── Tokens ────────────────────────────────────────────────── */
:root {
    --pri:    #2E66FF;          /* primary blue */
    --pri-h:  #1F4FE0;
    --pri-l:  #EEF2FF;
    --acc:    #FF7A1A;          /* orange accent */
    --acc-h:  #E86608;
    --acc-l:  #FFF1E5;
    --bg-pg:  #F4F5FA;          /* outer page background */
    --bg-pn:  #EFEBFB;          /* left panel lavender */
    --bg-card:#FFFFFF;
    --dk:     #0B1424;
    --tx:     #1F2937;
    --gr:     #5B6478;
    --gr-l:   #98A0AE;
    --bd:     #E2E5ED;
    --bd-l:   #EDEFF4;
    --ok:     #16A34A;
    --ok-l:   #E8F8EE;
    --p-purple:#EFEAFE;
    --p-green: #E3F4EA;
    --p-orange:#FFEDDC;
    --p-pink:  #FFE3EC;
    --p-blue:  #DEE9FE;
    --shadow-card: 0 1px 2px rgba(15,23,42,0.04), 0 8px 24px rgba(15,23,42,0.06);
    --shadow-card-hover: 0 1px 2px rgba(15,23,42,0.05), 0 16px 36px rgba(15,23,42,0.10);
    --shadow-btn: 0 1px 2px rgba(46,102,255,0.30), 0 6px 16px rgba(46,102,255,0.30);
    --shadow-btn-h: 0 1px 2px rgba(31,79,224,0.40), 0 10px 24px rgba(31,79,224,0.40);
}
html, body { margin: 0 !important; padding: 0 !important; height: 100%; overflow: hidden; }
body { background: var(--bg-pg); font-family: 'Poppins', sans-serif; }

/* ── Outer shell (locked to viewport) ────────────────────────── */
.jw-shell {
    height: 100vh;
    width: 100%;
    display: flex;
    align-items: stretch;
    background: var(--bg-pg);
    overflow: hidden;
}
.jw-page {
    display: flex;
    flex: 1;
    width: 100%;
    height: 100vh;
    overflow: hidden;
}
.jw-page * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }

/* ═══════════════ LEFT PANEL ═══════════════ */
.jw-left {
    flex: 1.15;
    background: var(--bg-pn);
    padding: 18px 32px 12px;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
    min-width: 0;
    min-height: 0;
    height: 100vh;
}

/* Brand mark */
.jw-brand { display: flex; align-items: center; gap: 9px; text-decoration: none; line-height: 1; }
.jw-brand-mark { height: 32px; width: auto; display: block; }
.jw-brand-name {
    font-size: 21px; font-weight: 800; color: var(--dk);
    letter-spacing: -0.6px; line-height: 1;
    font-family: 'Poppins', 'Nunito', 'Quicksand', system-ui, sans-serif;
}
.jw-brand-sub {
    display: block; font-size: 10.5px; color: var(--gr);
    font-weight: 500; margin-top: 4px; margin-left: 41px;
    letter-spacing: 0.2px;
}

/* Decorative dotted grid (top-right of left panel) */
.jw-dots {
    position: absolute;
    top: 28px;
    right: 48px;
    width: 110px;
    height: 64px;
    pointer-events: none;
    opacity: 0.7;
}
.jw-dots svg { width: 100%; height: 100%; }

/* Hero */
.jw-hero { margin: 10px 0 8px; max-width: 100%; }
.jw-hero h1 {
    font-size: 28px;
    font-weight: 800;
    color: var(--dk);
    line-height: 1.15;
    letter-spacing: -0.9px;
    margin: 0 0 6px;
}
.jw-hero h1 .acc { color: var(--acc); }
.jw-hero p {
    font-size: 12.5px; color: var(--gr); line-height: 1.5; margin: 0; font-weight: 400;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* 3 persona cards */
.jw-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 12px;
}
.jw-card {
    background: var(--bg-card);
    border-radius: 12px;
    padding: 11px 11px 0;
    border: 1px solid rgba(15,23,42,0.04);
    box-shadow: var(--shadow-card);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
    min-height: 0;
    transition: transform .2s, box-shadow .2s;
}
.jw-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-card-hover); }
.jw-card-badge {
    display: inline-block;
    font-size: 9px; font-weight: 700;
    padding: 3px 8px;
    border-radius: 999px;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    text-transform: uppercase;
    align-self: flex-start;
}
.jw-card-badge.b-comp  { background: var(--p-blue);   color: #1F4FE0; }
.jw-card-badge.b-agc   { background: var(--p-green);  color: #15803D; }
.jw-card-badge.b-tal   { background: var(--p-purple); color: #6D28D9; }
.jw-card-title {
    font-size: 13px; font-weight: 700; color: var(--dk);
    line-height: 1.25; margin: 0 0 4px; letter-spacing: -0.1px;
}
.jw-card-desc {
    font-size: 10.5px; color: var(--gr); line-height: 1.45;
    margin: 0 0 6px;
}
.jw-card-illu {
    margin-top: auto;
    height: 78px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    padding-bottom: 0;
}
.jw-card-illu svg { width: 100%; height: 100%; display: block; }

/* Stats bar */
.jw-stats {
    background: var(--bg-card);
    border-radius: 12px;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid rgba(15,23,42,0.04);
    box-shadow: var(--shadow-card);
    gap: 10px;
}
.jw-stat { display: flex; align-items: center; gap: 8px; min-width: 0; }
.jw-stat-ico {
    width: 28px; height: 28px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.jw-stat-ico svg { width: 15px; height: 15px; }
.jw-stat-ico.s-people { background: var(--p-blue); }
.jw-stat-ico.s-jobs   { background: var(--p-green); }
.jw-stat-ico.s-cand   { background: var(--p-pink); }
.jw-stat-ico.s-up     { background: var(--p-orange); }
.jw-stat-meta strong {
    display: block; font-size: 12.5px; font-weight: 800; color: var(--dk);
    line-height: 1.1; letter-spacing: -0.3px;
}
.jw-stat-meta span {
    display: block; font-size: 9.5px; color: var(--gr); line-height: 1.2;
    white-space: nowrap;
}

/* Footer (left panel bottom) */
.jw-left-foot {
    margin-top: 12px;
    display: flex; align-items: center; justify-content: space-between;
    font-size: 10.5px; color: var(--gr);
    gap: 10px; flex-wrap: wrap;
}
.jw-left-foot a { color: var(--gr); text-decoration: none; transition: color .15s; }
.jw-left-foot a:hover { color: var(--dk); }
.jw-left-foot .links { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
.jw-locale {
    display: inline-flex; align-items: center; gap: 5px;
    background: var(--bg-card);
    border: 1px solid var(--bd);
    padding: 3px 9px; border-radius: 6px;
    font-size: 10.5px; font-weight: 600; color: var(--dk); cursor: pointer;
}

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

/* Top bar (right) */
.jw-top {
    display: flex; align-items: center; justify-content: flex-end;
    gap: 10px; padding: 10px 36px 0;
    font-size: 13px; color: var(--gr);
}
.jw-top-cta {
    color: var(--pri); text-decoration: none; font-weight: 500;
}
.jw-top-btn {
    background: transparent;
    color: var(--pri);
    border: 1.5px solid var(--pri);
    border-radius: 8px;
    padding: 7px 14px;
    font-size: 13px; font-weight: 600;
    cursor: pointer; text-decoration: none;
    transition: background .2s, color .2s;
}
.jw-top-btn:hover { background: var(--pri); color: #fff; }

/* Right body (centered card) */
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
    max-width: 540px;
    background: var(--bg-card);
    border: 1px solid var(--bd-l);
    border-radius: 14px;
    padding: 18px 30px 12px;
    box-shadow: var(--shadow-card);
}
.jw-card-form h2 {
    font-size: 20px; font-weight: 800; color: var(--dk);
    margin: 0 0 2px; letter-spacing: -0.4px; text-align: center;
}
.jw-card-form .sub {
    font-size: 12px; color: var(--gr); text-align: center;
    margin: 0 0 12px;
}

/* Alerts (preserve existing behavior) */
.jw-alert {
    border-radius: 8px; padding: 10px 14px;
    font-size: 12.5px; margin-bottom: 16px; line-height: 1.5;
}
.jw-alert-err { background: #FEF2F2; color: #B91C1C; border: 1px solid #FEE2E2; }
.jw-alert-ok  { background: #F0FDF4; color: #15803D; border: 1px solid #BBF7D0; }
.jw-err { font-size: 11.5px; color: #DC2626; margin-top: 4px; display: block; }

/* Field */
.jw-field { margin-bottom: 9px; }
.jw-field > label {
    display: block; font-size: 12px; font-weight: 600;
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
    border: 1.5px solid var(--bd); border-radius: 9px;
    padding: 0 42px;
    font-size: 13px; font-family: 'Poppins', sans-serif;
    color: var(--dk); outline: none; background: #fff;
    transition: border-color .2s, box-shadow .2s;
}
.jw-inp::placeholder { color: var(--gr-l); }
.jw-inp:hover { border-color: #C9CFDB; }
.jw-inp:focus { border-color: var(--pri); box-shadow: 0 0 0 3px rgba(46,102,255,0.12); }
.jw-inp.is-ok { border-color: var(--ok); }
.jw-inp.is-ok:focus { box-shadow: 0 0 0 3px rgba(22,163,74,0.14); }
.jw-iw .ok-mark {
    position: absolute; right: 14px; color: var(--ok);
    display: flex; pointer-events: none;
}

/* Mid row */
.jw-mid {
    display: flex; align-items: center; justify-content: space-between;
    margin: 0 0 9px;
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

/* Captcha row */
.jw-cap-lbl {
    font-size: 12px; font-weight: 600; color: var(--dk);
    margin-bottom: 4px;
}
.jw-cap-row {
    display: flex; align-items: center; gap: 9px; margin-bottom: 12px;
}
.jw-cap-img {
    width: 90px; height: 40px; border: 1.5px solid var(--bd);
    border-radius: 9px; overflow: hidden;
    background: #F9FAFB;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.jw-cap-img img { height: 34px; width: auto; object-fit: contain; }
.jw-cap-btn {
    display: inline-flex; align-items: center; gap: 4px;
    background: transparent; color: var(--pri);
    font-size: 12px; font-weight: 600; cursor: pointer;
    border: none; padding: 0 4px; white-space: nowrap;
    flex-shrink: 0; transition: color .15s;
}
.jw-cap-btn:hover { color: var(--pri-h); }
.jw-cap-field { flex: 1; position: relative; }
.jw-cap-inp {
    width: 100%; height: 40px;
    border: 1.5px solid var(--bd); border-radius: 9px;
    padding: 0 14px; padding-right: 36px;
    font-size: 14px; letter-spacing: 4px; font-weight: 700;
    font-family: 'Poppins', sans-serif; outline: none; background: #fff;
    color: var(--dk); transition: border-color .2s, box-shadow .2s;
}
.jw-cap-inp:focus { border-color: var(--pri); box-shadow: 0 0 0 3px rgba(46,102,255,0.12); }
.jw-cap-inp.is-ok { border-color: var(--ok); }
.jw-cap-field .ok-mark { right: 10px; }

/* Sign-in button (BLUE with arrow) */
.jw-btn {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    width: 100%; height: 42px;
    background: var(--pri); color: #fff;
    border: none; border-radius: 9px;
    font-size: 14px; font-weight: 600;
    font-family: 'Poppins', sans-serif; cursor: pointer;
    transition: background .2s, box-shadow .2s, transform .05s;
    box-shadow: var(--shadow-btn);
    letter-spacing: 0.2px;
}
.jw-btn:hover { background: var(--pri-h); box-shadow: var(--shadow-btn-h); }
.jw-btn:active { transform: translateY(1px); }
.jw-btn .arrow { display: flex; transition: transform .2s; }
.jw-btn:hover .arrow { transform: translateX(3px); }

/* Forgot form extras */
.jw-fgt-acts { display: flex; gap: 12px; margin-top: 18px; }
.jw-fgt-sub {
    flex: 1; height: 46px; background: var(--pri); color: #fff;
    border: none; border-radius: 10px;
    font-size: 14px; font-weight: 600;
    font-family: 'Poppins', sans-serif; cursor: pointer;
    transition: background .2s, box-shadow .2s;
    box-shadow: var(--shadow-btn);
}
.jw-fgt-sub:hover { background: var(--pri-h); box-shadow: var(--shadow-btn-h); }
.jw-fgt-cancel {
    height: 46px; padding: 0 20px; background: #F3F4F6; color: #374151;
    border: none; border-radius: 10px;
    font-size: 14px; font-weight: 600;
    font-family: 'Poppins', sans-serif; text-decoration: none;
    display: inline-flex; align-items: center; cursor: pointer;
    transition: background .2s;
}
.jw-fgt-cancel:hover { background: #E5E7EB; color: #374151; }

/* Trust strip (below sign-in card) */
.jw-trust {
    width: 100%; max-width: 540px;
    margin-top: 10px;
    background: var(--bg-card);
    border: 1px solid var(--bd-l);
    border-radius: 12px;
    padding: 8px 12px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px 12px;
    box-shadow: var(--shadow-card);
}
.jw-trust-item { display: flex; align-items: center; gap: 8px; min-width: 0; }
.jw-trust-ico {
    width: 24px; height: 24px; border-radius: 6px;
    background: var(--p-green);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.jw-trust-ico.t-ssl  { background: var(--p-blue); }
.jw-trust-ico.t-up   { background: var(--p-orange); }
.jw-trust-ico.t-soc  { background: var(--p-purple); }
.jw-trust-ico.t-gdpr { background: var(--p-pink); }
.jw-trust-ico svg { width: 12px; height: 12px; }
.jw-trust-meta { min-width: 0; overflow: hidden; }
.jw-trust-meta strong {
    display: block; font-size: 11.5px; font-weight: 700;
    color: var(--dk); line-height: 1.15;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    letter-spacing: -0.1px;
}
.jw-trust-meta span {
    display: block; font-size: 10px; color: var(--gr); line-height: 1.2;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* ── Responsive ─────────────────────────────────────────────── */

/* 1440px – larger 15.6" at lower resolution */
@media (max-width: 1440px) {
    .jw-left  { padding: 26px 36px 18px; }
    .jw-hero h1 { font-size: 34px; }
    .jw-dots { right: 36px; top: 30px; width: 110px; height: 64px; }
}

/* 1366px – most 14" / 15.6" laptops */
@media (max-width: 1366px) {
    .jw-card-illu  { height: 58px; }
    .jw-card-form { max-width: 500px; }
    .jw-trust { max-width: 500px; }
    .jw-dots { display: none; }
    .jw-hero h1 { font-size: 26px; }
    .jw-left { padding: 14px 28px 10px; }
    .jw-hero { margin: 8px 0 6px; }
    .jw-hero p { font-size: 12px; }
    .jw-cards { gap: 8px; margin-bottom: 8px; }
    .jw-card { padding: 9px 9px 0; }
    .jw-card-illu svg { transform: scale(0.92); }
    .jw-stats { padding: 8px 12px; }
    .jw-stat-ico { width: 24px; height: 24px; }
    .jw-stat-ico svg { width: 13px; height: 13px; }
    .jw-stat-meta strong { font-size: 12px; }
    .jw-stat-meta span { font-size: 9px; }
    .jw-left-foot { margin-top: 8px; font-size: 10px; }
}

/* 1280px – smaller 14" laptops */
@media (max-width: 1280px) {
    .jw-card-illu   { height: 50px; }
    .jw-card-form { max-width: 460px; }
    .jw-trust { max-width: 460px; }
    .jw-hero h1 { font-size: 24px; }
}

/* Tall viewports – keep dots visible above 880h */
@media (min-height: 880px) {
    .jw-dots { display: block; }
}

/* 1024px and below – tablet */
@media (max-width: 1024px) {
    .jw-page { flex-direction: column; }
    .jw-left { padding: 24px 24px 20px; }
    .jw-hero h1 { font-size: 28px; }
    .jw-cards { grid-template-columns: 1fr 1fr; }
    .jw-card:last-child { grid-column: span 2; }
    .jw-body { padding: 22px 24px; }
    .jw-card-form { max-width: 100%; }
    .jw-trust { max-width: 100%; }
}

/* 700px and below – small tablet / large phone */
@media (max-width: 700px) {
    .jw-cards { grid-template-columns: 1fr; }
    .jw-card:last-child { grid-column: span 1; }
    .jw-stats { flex-wrap: wrap; justify-content: flex-start; }
    .jw-stat { flex: 1 1 45%; }
    .jw-trust { grid-template-columns: 1fr; }
}

/* 480px and below – phone */
@media (max-width: 480px) {
    .jw-hero h1 { font-size: 24px; }
    .jw-top-btn { padding: 6px 12px; font-size: 12px; }
    .jw-card-form { padding: 22px 18px 16px; border-radius: 14px; }
    .jw-trust { padding: 12px 14px; }
    .jw-trust { grid-template-columns: 1fr; }
    .jw-left-foot .links { gap: 12px; }
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

    {{-- Decorative dot grid --}}
    <div class="jw-dots" aria-hidden="true">
        <svg viewBox="0 0 140 80">
            <g fill="#FFB68A" opacity="0.55">
                @for ($r = 0; $r < 8; $r++)
                    @for ($c = 0; $c < 14; $c++)
                        <circle cx="{{ 5 + $c*10 }}" cy="{{ 5 + $r*10 }}" r="1.6"/>
                    @endfor
                @endfor
            </g>
        </svg>
    </div>

    {{-- Brand --}}
    <a href="https://jero.works" class="jw-brand">
        <svg class="jw-brand-mark" viewBox="0 0 38 44" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
            {{-- Yellow dot above umbrella --}}
            <circle cx="19" cy="2.6" r="1.9" fill="#F5C518"/>
            {{-- Umbrella canopy (top dome) --}}
            <path d="M19 3.6 C10 3.6 2.6 9.6 1 17.6 C0.5 19.6 2 21 4 21 H34 C36 21 37.5 19.6 37 17.6 C35.4 9.6 28 3.6 19 3.6 Z" fill="#111827"/>
            {{-- Scalloped bottom edge (4 lobes) --}}
            <path d="M1.4 18.6 C2 22 4.4 24.4 7.4 24.4 C10 24.4 12.2 22.6 13 20 C13.8 22.6 16 24.4 19 24.4 C22 24.4 24.2 22.6 25 20 C25.8 22.6 28 24.4 30.6 24.4 C33.6 24.4 36 22 36.6 18.6 C36.4 19.6 36 20.4 35.4 21 H2.6 C2 20.4 1.6 19.6 1.4 18.6 Z" fill="#111827"/>
            {{-- Umbrella pole --}}
            <rect x="18.2" y="24" width="1.6" height="11" fill="#111827"/>
            {{-- Hook / "j" curl at the bottom --}}
            <path d="M19.8 35 C19.8 37.4 18 39.4 15.6 39.8 C13.6 40.2 11.6 39.4 10.6 37.6 C10.2 37 10.4 36.2 11 35.8 C11.6 35.4 12.4 35.6 12.8 36.2 C13.4 37.2 14.4 37.6 15.4 37.4 C16.6 37.2 17.4 36.2 17.4 35 V35" stroke="#111827" stroke-width="1.6" fill="none" stroke-linecap="round"/>
        </svg>
        <span class="jw-brand-name">jero.works</span>
    </a>
    <span class="jw-brand-sub">AI Recruitment Platform</span>

    {{-- Hero --}}
    <div class="jw-hero">
        <h1>One Platform. Every <span class="acc">Hiring Need.</span></h1>
        <p>Connect companies, agencies and talent on a modern recruitment platform built for speed and trust.</p>
    </div>

    {{-- Persona cards --}}
    <div class="jw-cards">

        {{-- Card 1: Companies --}}
        <div class="jw-card">
            <span class="jw-card-badge b-comp">For Companies</span>
            <h3 class="jw-card-title">Find Trusted<br>Agencies</h3>
            <p class="jw-card-desc">Connect with verified recruitment agencies and hire faster.</p>
            <div class="jw-card-illu">
                <svg viewBox="0 0 160 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- City backdrop --}}
                    <rect x="4"  y="64" width="14" height="40" fill="#E2ECFF"/>
                    <rect x="20" y="56" width="12" height="48" fill="#D6E4FF"/>
                    <rect x="34" y="48" width="14" height="56" fill="#C8DAFF"/>
                    <rect x="50" y="60" width="10" height="44" fill="#D6E4FF"/>
                    <rect x="120" y="58" width="14" height="46" fill="#D6E4FF"/>
                    <rect x="136" y="50" width="12" height="54" fill="#C8DAFF"/>
                    {{-- Man body / suit --}}
                    {{-- Hair --}}
                    <path d="M48 30c0-8 6-14 14-14s14 6 14 14c-3-3-7-5-14-5s-11 2-14 5Z" fill="#3D2817"/>
                    {{-- Head --}}
                    <ellipse cx="62" cy="32" rx="10" ry="11" fill="#F5C9A3"/>
                    {{-- Glasses --}}
                    <circle cx="58" cy="32" r="3" fill="none" stroke="#222" stroke-width="1.2"/>
                    <circle cx="66" cy="32" r="3" fill="none" stroke="#222" stroke-width="1.2"/>
                    <line x1="61" y1="32" x2="63" y2="32" stroke="#222" stroke-width="1.2"/>
                    {{-- Smile --}}
                    <path d="M58 38c1.5 1.5 5.5 1.5 8 0" stroke="#222" stroke-width="1" stroke-linecap="round" fill="none"/>
                    {{-- Neck --}}
                    <rect x="60" y="42" width="4" height="4" fill="#F5C9A3"/>
                    {{-- Suit jacket --}}
                    <path d="M44 46l16-2 16 2 4 24-2 36H46l-2-36Z" fill="#2E66FF"/>
                    {{-- Shirt collar --}}
                    <path d="M58 46l4 6 4-6-2 12h-4Z" fill="#FFFFFF"/>
                    {{-- Tie --}}
                    <path d="M61 52l2 8-1 14h-2l-1-14Z" fill="#FF7A1A"/>
                    {{-- Arms forward to laptop --}}
                    <path d="M44 50l-8 16 8 4 8-6Z" fill="#2E66FF"/>
                    <path d="M80 50l8 16-8 4-8-6Z" fill="#2E66FF"/>
                    {{-- Hands --}}
                    <ellipse cx="42" cy="72" rx="4" ry="3" fill="#F5C9A3"/>
                    <ellipse cx="82" cy="72" rx="4" ry="3" fill="#F5C9A3"/>
                    {{-- Laptop --}}
                    <rect x="38" y="76" width="48" height="22" rx="2" fill="#1F2937"/>
                    <rect x="40" y="78" width="44" height="18" rx="1" fill="#E5E7EB"/>
                    <rect x="32" y="98" width="60" height="3" rx="1.5" fill="#374151"/>
                    {{-- Floating badge (people) --}}
                    <circle cx="108" cy="38" r="14" fill="#EEF2FF" stroke="#FFFFFF" stroke-width="2"/>
                    <g transform="translate(100 30)" fill="#2E66FF">
                        <circle cx="4" cy="3" r="2.6"/>
                        <circle cx="11" cy="3" r="2.6" opacity=".65"/>
                        <path d="M-1 13c0-3.5 2.5-6 7-6s7 2.5 7 6" stroke="#2E66FF" stroke-width="1.6" fill="none" stroke-linecap="round"/>
                    </g>
                </svg>
            </div>
        </div>

        {{-- Card 2: Agencies --}}
        <div class="jw-card">
            <span class="jw-card-badge b-agc">For Agencies</span>
            <h3 class="jw-card-title">Search Work<br>Opportunities</h3>
            <p class="jw-card-desc">Discover verified job opportunities from the leading companies.</p>
            <div class="jw-card-illu">
                <svg viewBox="0 0 160 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- Resume papers --}}
                    <rect x="8"  y="22" width="32" height="42" rx="3" fill="#FFFFFF" stroke="#E5E7EB" stroke-width="1"/>
                    <rect x="14" y="28" width="20" height="2.4" rx="1" fill="#16A34A"/>
                    <rect x="14" y="34" width="14" height="2" rx="1" fill="#9CA3AF"/>
                    <rect x="14" y="40" width="22" height="2" rx="1" fill="#9CA3AF"/>
                    <rect x="14" y="46" width="18" height="2" rx="1" fill="#9CA3AF"/>
                    <rect x="14" y="52" width="20" height="2" rx="1" fill="#9CA3AF"/>
                    <circle cx="34" cy="58" r="3" fill="#16A34A" opacity=".25"/>
                    {{-- Woman hair (long brown) --}}
                    <path d="M48 28c0-9 6-15 14-15s14 6 14 15c-2-2-6-3-14-3s-12 1-14 3Z" fill="#3D2817"/>
                    <path d="M44 30c-1 8 0 22 2 28l4-2c-2-6-3-18-2-26Z" fill="#3D2817"/>
                    <path d="M80 30c1 8 0 22-2 28l-4-2c2-6 3-18 2-26Z" fill="#3D2817"/>
                    {{-- Head --}}
                    <ellipse cx="62" cy="32" rx="10" ry="11" fill="#F5C9A3"/>
                    {{-- Eyes --}}
                    <circle cx="58" cy="32" r="1.2" fill="#222"/>
                    <circle cx="66" cy="32" r="1.2" fill="#222"/>
                    {{-- Smile --}}
                    <path d="M58 37c1.5 1.5 5.5 1.5 8 0" stroke="#222" stroke-width="1" stroke-linecap="round" fill="none"/>
                    {{-- Neck --}}
                    <rect x="60" y="42" width="4" height="4" fill="#F5C9A3"/>
                    {{-- Green jacket --}}
                    <path d="M46 46l16-2 16 2 4 24-2 36H48l-2-36Z" fill="#16A34A"/>
                    {{-- Inner shirt --}}
                    <path d="M58 46l4 6 4-6-1 8h-6Z" fill="#FFFFFF"/>
                    {{-- Arms --}}
                    <path d="M46 50l-8 18 8 4 8-8Z" fill="#16A34A"/>
                    <path d="M78 50l8 18-8 4-8-8Z" fill="#16A34A"/>
                    <ellipse cx="44" cy="74" rx="4" ry="3" fill="#F5C9A3"/>
                    <ellipse cx="80" cy="74" rx="4" ry="3" fill="#F5C9A3"/>
                    {{-- Laptop --}}
                    <rect x="38" y="78" width="48" height="22" rx="2" fill="#1F2937"/>
                    <rect x="40" y="80" width="44" height="18" rx="1" fill="#E5E7EB"/>
                    <rect x="32" y="100" width="60" height="3" rx="1.5" fill="#374151"/>
                    {{-- Floating briefcase --}}
                    <rect x="106" y="32" width="26" height="20" rx="3" fill="#E7F6EC" stroke="#FFFFFF" stroke-width="2"/>
                    <rect x="113" y="28" width="12" height="5" rx="1.5" fill="#16A34A"/>
                    <rect x="110" y="38" width="18" height="2.5" fill="#16A34A"/>
                    <circle cx="119" cy="46" r="1.5" fill="#16A34A"/>
                </svg>
            </div>
        </div>

        {{-- Card 3: Talent --}}
        <div class="jw-card">
            <span class="jw-card-badge b-tal">For Talent</span>
            <h3 class="jw-card-title">Hire Immediate<br>Joiners</h3>
            <p class="jw-card-desc">Explore bench talent ready to join at the earliest.</p>
            <div class="jw-card-illu">
                <svg viewBox="0 0 160 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- Phone in hand --}}
                    <rect x="48" y="62" width="18" height="32" rx="3" fill="#1F2937"/>
                    <rect x="50" y="64" width="14" height="26" rx="1.5" fill="#E2ECFF"/>
                    <circle cx="57" cy="73" r="3" fill="#6D28D9"/>
                    <rect x="52" y="80" width="10" height="2" fill="#9CA3AF"/>
                    <rect x="52" y="84" width="8" height="2" fill="#9CA3AF"/>
                    <rect x="52" y="88" width="10" height="2" fill="#9CA3AF"/>
                    {{-- Man hair --}}
                    <path d="M64 28c0-8 5-14 14-14s14 6 14 14c-3-3-7-5-14-5s-11 2-14 5Z" fill="#3D2817"/>
                    {{-- Head --}}
                    <ellipse cx="78" cy="32" rx="10" ry="11" fill="#F5C9A3"/>
                    {{-- Eyes --}}
                    <circle cx="74" cy="32" r="1.2" fill="#222"/>
                    <circle cx="82" cy="32" r="1.2" fill="#222"/>
                    {{-- Smile --}}
                    <path d="M74 37c1.5 1.5 5.5 1.5 8 0" stroke="#222" stroke-width="1" stroke-linecap="round" fill="none"/>
                    {{-- Neck --}}
                    <rect x="76" y="42" width="4" height="4" fill="#F5C9A3"/>
                    {{-- Purple hoodie --}}
                    <path d="M60 46l18-2 18 2 4 26-2 38H62l-2-38Z" fill="#6D28D9"/>
                    {{-- Hood collar --}}
                    <path d="M70 46c2-2 6-2 8 0l-1 6-6-1Z" fill="#FFFFFF" opacity=".25"/>
                    {{-- Arms (one forward holding phone) --}}
                    <path d="M62 52l-12 18 12 6 6-8Z" fill="#6D28D9"/>
                    {{-- Arm holding phone --}}
                    <path d="M76 56l16 16-4 8-12-12Z" fill="#6D28D9"/>
                    <ellipse cx="64" cy="76" rx="4" ry="3" fill="#F5C9A3"/>
                    <ellipse cx="92" cy="80" rx="4" ry="3" fill="#F5C9A3"/>
                    {{-- Floating lightning bolt --}}
                    <circle cx="118" cy="34" r="14" fill="#F1ECFE" stroke="#FFFFFF" stroke-width="2"/>
                    <path d="M120 26l-6 10h4l-3 8 7-12h-4l2-6Z" fill="#6D28D9"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Stats bar --}}
    <div class="jw-stats">
        <div class="jw-stat">
            <div class="jw-stat-ico s-people">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <circle cx="9" cy="8" r="3.2" fill="#2E66FF"/>
                    <circle cx="16" cy="8" r="3.2" fill="#2E66FF" opacity=".55"/>
                    <path d="M2 21c0-3.87 3.13-7 7-7h5c3.87 0 7 3.13 7 7" stroke="#2E66FF" stroke-width="2" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="jw-stat-meta">
                <strong>150+</strong>
                <span>Enterprise Clients</span>
            </div>
        </div>
        <div class="jw-stat">
            <div class="jw-stat-ico s-jobs">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="7" width="18" height="13" rx="2" fill="#16A34A" opacity=".25"/>
                    <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="#16A34A" stroke-width="2" fill="none"/>
                    <rect x="3" y="7" width="18" height="13" rx="2" stroke="#16A34A" stroke-width="2" fill="none"/>
                </svg>
            </div>
            <div class="jw-stat-meta">
                <strong>25,000+</strong>
                <span>Active Jobs</span>
            </div>
        </div>
        <div class="jw-stat">
            <div class="jw-stat-ico s-cand">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="8" r="3.5" fill="#EC4899"/>
                    <path d="M5 20c0-3.87 3.13-7 7-7s7 3.13 7 7" stroke="#EC4899" stroke-width="2" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="jw-stat-meta">
                <strong>250,000+</strong>
                <span>Candidate Profiles</span>
            </div>
        </div>
        <div class="jw-stat">
            <div class="jw-stat-ico s-up">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" fill="#FF7A1A" opacity=".25" stroke="#FF7A1A" stroke-width="1.7"/>
                    <path d="M8 12l3 3 5-5" stroke="#FF7A1A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
            </div>
            <div class="jw-stat-meta">
                <strong>99.9%</strong>
                <span>Platform Uptime</span>
            </div>
        </div>
    </div>

    {{-- Left footer --}}
    <div class="jw-left-foot">
        <span>© 2026 Jero Technologies Pvt. Ltd. All rights reserved.</span>
        <div class="links">
            <a href="/privacy-policy">Privacy Policy</a>
            <a href="/terms">Terms of Service</a>
            <a href="/support">Support</a>
            <span class="jw-locale" aria-label="Language">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="9" stroke="#6B7280" stroke-width="1.6"/>
                    <path d="M3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18" stroke="#6B7280" stroke-width="1.6"/>
                </svg>
                English
                <svg width="10" height="10" viewBox="0 0 12 12" fill="none">
                    <path d="M3 5l3 3 3-3" stroke="#6B7280" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </div>
    </div>

</div>{{-- /jw-left --}}

{{-- ═══════════════ RIGHT PANEL ═══════════════ --}}
<div class="jw-right">

    {{-- Top bar --}}
    <div class="jw-top">
        <span>New to jero.works?</span>
        <a href="{{ route('register') }}" class="jw-top-btn">Create Free Account</a>
    </div>

    {{-- Body / Card --}}
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
                <label>Email Address</label>
                <div class="jw-iw">
                    <span class="il">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="4" width="16" height="11" rx="2.2" stroke="#9CA3AF" stroke-width="1.5"/><path d="M1 7l8 5.5L17 7" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"/></svg>
                    </span>
                    <input class="jw-inp" type="text" name="email" id="jw-email" placeholder="Enter your email" autocomplete="off" value="{{ old('email') }}"/>
                    <span class="ok-mark" id="jw-email-ok" style="display:none;">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8.4" fill="#16A34A"/><path d="M5 9l3 3 5-5.5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <input type="hidden" name="url" value="{{ @$url }}"/>
                </div>
                @if($errors->has('email'))<span class="jw-err">{{ $errors->first('email') }}</span>@endif
            </div>

            {{-- Password --}}
            <div class="jw-field">
                <label>Password</label>
                <div class="jw-iw">
                    <span class="il">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="3" y="8" width="12" height="9" rx="2.2" stroke="#9CA3AF" stroke-width="1.5"/><path d="M6 8V6a3 3 0 0 1 6 0v2" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"/><circle cx="9" cy="12.5" r="1.3" fill="#9CA3AF"/></svg>
                    </span>
                    <input class="jw-inp" type="password" name="password" id="jw-pwd" placeholder="Enter your password" autocomplete="off"/>
                    <span class="ir" onclick="jwEye()" style="user-select:none">
                        <svg id="jw-eye" width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M1.5 9s2.8-5.5 7.5-5.5S16.5 9 16.5 9s-2.8 5.5-7.5 5.5S1.5 9 1.5 9Z" stroke="#9CA3AF" stroke-width="1.5"/><circle cx="9" cy="9" r="2.2" stroke="#9CA3AF" stroke-width="1.5"/></svg>
                    </span>
                </div>
                @if($errors->has('password'))<span class="jw-err"><strong>{{ $errors->first('password') }}</strong></span>@endif
            </div>

            {{-- Remember + Forgot --}}
            <div class="jw-mid">
                <div class="jw-rem">
                    <input type="checkbox" name="remember" id="jw-rem" {{ old('remember') ? 'checked' : '' }}>
                    <label for="jw-rem">Remember me</label>
                </div>
                <a href="javascript:void(0);" id="to-recover" class="jw-fgt">Forgot Password?</a>
            </div>

            {{-- Captcha --}}
            <div class="jw-cap-lbl">Enter the number in the box</div>
            <div class="jw-cap-row">
                <div class="jw-cap-img">
                    <img src="{{ route('captcha.generate') }}" id="captchaImg" alt="captcha">
                </div>
                <button type="button" onclick="refreshCaptcha()" class="jw-cap-btn">
                    <svg width="14" height="14" viewBox="0 0 15 15" fill="none">
                        <path d="M2 7.5A5.5 5.5 0 0 1 13 7.5" stroke="#2E66FF" stroke-width="1.6" stroke-linecap="round"/>
                        <path d="M11 5.5L13 7.5l2-2" stroke="#2E66FF" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M13 7.5A5.5 5.5 0 0 1 2 7.5" stroke="#2E66FF" stroke-width="1.6" stroke-linecap="round"/>
                        <path d="M4 9.5L2 7.5 0 9.5" stroke="#2E66FF" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Refresh
                </button>
                <div class="jw-cap-field">
                    <input type="text" name="captcha" class="jw-cap-inp" id="jw-cap" autocomplete="off" required>
                    <span class="ok-mark" id="jw-cap-ok" style="display:none;">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8.4" fill="#16A34A"/><path d="M5 9l3 3 5-5.5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </div>
            </div>
            @if($errors->has('captcha'))
                <span class="jw-err" style="margin-top:-16px;margin-bottom:14px;display:block">{{ $errors->first('captcha') }}</span>
            @endif

            {{-- Submit --}}
            <button type="submit" id="kt_sign_in_submit" class="jw-btn">
                <span class="indicator-label">Sign In</span>
                <span class="arrow">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M3 10h13M11 5l5 5-5 5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="indicator-progress" style="display:none">Please wait…&nbsp;<span class="spinner-border spinner-border-sm align-middle"></span></span>
            </button>

            {{-- Sign Up --}}
            <div style="text-align:center;margin-top:10px;">
                <span style="font-size:12px;color:var(--gr);">Don't have an account?</span>
                <a href="{{ route('register') }}" style="font-size:12px;font-weight:700;color:var(--pri);text-decoration:none;margin-left:5px;">Sign Up</a>
            </div>
        </form>

        {{-- FORGOT PASSWORD FORM --}}
        <form class="jw-card-form" novalidate id="recoverform" action="{{ route('password.emailsend') }}" method="post" style="<?php echo $resetForm; ?>">
            {{ csrf_field() }}
            <h2>Forgot Password? 🔑</h2>
            <p class="sub">Enter your email to reset your password.</p>

            @if(session('status'))
                <div class="jw-alert jw-alert-ok">{{ session('status') }}</div>
            @endif
            @if(session('accountnotverify'))
                <div class="jw-alert jw-alert-err">{{ session('accountnotverify') }}</div>
            @endif

            <div class="jw-field {{ $errors->has('email') ? 'has-error' : '' }}">
                <label>Email Address</label>
                <div class="jw-iw">
                    <span class="il">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="4" width="16" height="11" rx="2.2" stroke="#9CA3AF" stroke-width="1.5"/><path d="M1 7l8 5.5L17 7" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"/></svg>
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

        {{-- Trust strip --}}
        <div class="jw-trust" role="list">
            <div class="jw-trust-item" role="listitem">
                <div class="jw-trust-ico t-ssl">
                    <svg viewBox="0 0 24 24" fill="none">
                        <rect x="5" y="11" width="14" height="10" rx="2" stroke="#1F4FE0" stroke-width="2"/>
                        <path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="#1F4FE0" stroke-width="2"/>
                    </svg>
                </div>
                <div class="jw-trust-meta">
                    <strong>SSL Secured</strong>
                    <span>256-bit encryption</span>
                </div>
            </div>
            <div class="jw-trust-item" role="listitem">
                <div class="jw-trust-ico t-up">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="#E86608" stroke-width="2" fill="none"/>
                        <path d="M8 12l3 3 5-5" stroke="#E86608" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                </div>
                <div class="jw-trust-meta">
                    <strong>99.9% Uptime</strong>
                    <span>Reliable Platform</span>
                </div>
            </div>
            <div class="jw-trust-item" role="listitem">
                <div class="jw-trust-ico t-soc">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="#6D28D9" stroke-width="2" fill="none"/>
                        <path d="M9 12l2 2 4-4" stroke="#6D28D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                </div>
                <div class="jw-trust-meta">
                    <strong>SOC 2 Ready</strong>
                    <span>Enterprise Grade</span>
                </div>
            </div>
            <div class="jw-trust-item" role="listitem">
                <div class="jw-trust-ico t-gdpr">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="#DB2777" stroke-width="2" fill="none"/>
                        <path d="M8 12l3 3 5-5" stroke="#DB2777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                </div>
                <div class="jw-trust-meta">
                    <strong>GDPR Compliant</strong>
                    <span>Data Protection</span>
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
    document.getElementById('captchaImg').src = "{{ route('captcha.generate') }}?t=" + Date.now();
    var c = document.getElementById('jw-cap');
    if (c) { c.value = ''; c.classList.remove('is-ok'); }
    var ok = document.getElementById('jw-cap-ok');
    if (ok) ok.style.display = 'none';
}

function jwEye() {
    var i = document.getElementById('jw-pwd'), e = document.getElementById('jw-eye');
    if (i.type === 'password') {
        i.type = 'text';
        e.innerHTML = '<path d="M1.5 9s2.8-5.5 7.5-5.5S16.5 9 16.5 9s-2.8 5.5-7.5 5.5S1.5 9 1.5 9Z" stroke="#6B7280" stroke-width="1.5"/><circle cx="9" cy="9" r="2.2" stroke="#6B7280" stroke-width="1.5"/><line x1="2" y1="2" x2="17" y2="17" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>';
    } else {
        i.type = 'password';
        e.innerHTML = '<path d="M1.5 9s2.8-5.5 7.5-5.5S16.5 9 16.5 9s-2.8 5.5-7.5 5.5S1.5 9 1.5 9Z" stroke="#9CA3AF" stroke-width="1.5"/><circle cx="9" cy="9" r="2.2" stroke="#9CA3AF" stroke-width="1.5"/>';
    }
}

// Show green check when fields have content (mirrors screenshot)
document.addEventListener('DOMContentLoaded', function () {
    var email = document.getElementById('jw-email');
    var emailOk = document.getElementById('jw-email-ok');
    function checkEmail() {
        if (email && emailOk) {
            var v = email.value.trim();
            var ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
            emailOk.style.display = ok ? 'flex' : 'none';
            email.classList.toggle('is-ok', ok);
        }
    }
    if (email) {
        checkEmail();
        email.addEventListener('input', checkEmail);
    }

    var cap = document.getElementById('jw-cap');
    var capOk = document.getElementById('jw-cap-ok');
    function checkCap() {
        if (cap && capOk) {
            var ok = cap.value.trim().length >= 3;
            capOk.style.display = ok ? 'flex' : 'none';
            cap.classList.toggle('is-ok', ok);
        }
    }
    if (cap) {
        checkCap();
        cap.addEventListener('input', checkCap);
    }
});
</script>