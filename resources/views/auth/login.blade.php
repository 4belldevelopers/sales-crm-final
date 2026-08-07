@extends('layouts.metronic_front_app')
<title>Jero - Login</title>

<style>
/* ── V2 Login ── */
:root {
    --p:  #F5A623;
    --ph: #E8960D;
    --pl: #FFF3DC;
    --lbg: #FFF9EC;
    --rbg: #FFFFFF;
    --dk: #111827;
    --gr: #6b7280;
    --bd: #E5E7EB;
}
html, body { margin: 0 !important; padding: 0 !important; }

/* ── Shell centers the page on ultra-wide screens ── */
.v2-shell {
    display: flex;
    width: 100%;
    min-height: 100vh;
    overflow: hidden;
    align-items: stretch;
    background: #fff;
}
.v2-page {
    display: flex;
    width: 100%;
    min-height: 100vh;
    overflow: hidden;
}
.v2-page * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }

/* ═══════════ LEFT PANEL ═══════════ */
.v2-left {
    width: 635px;
    flex-shrink: 0;
    background: var(--lbg);
    display: flex;
    flex-direction: column;
    padding: 36px 40px 0;
    position: relative;
    overflow: hidden;
    height: 100vh;
}

/* Logo */
.v2-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; margin-bottom: 32px; }
.v2-logo-text { font-size: 26px; font-weight: 800; color: var(--dk); letter-spacing: -0.5px; line-height: 1; }

/* Hero */
.v2-hero { margin-bottom: 24px; }
.v2-hero h1 { font-size: 32px; font-weight: 800; line-height: 1.18; color: var(--dk); margin: 0 0 10px; letter-spacing: -0.6px; }
.v2-hero h1 .acc { color: var(--p); }
.v2-hero p { font-size: 13.5px; color: var(--gr); line-height: 1.6; margin: 0; }

/* Features */
.v2-feats { display: flex; flex-direction: column; gap: 14px; margin-bottom: 0; }
.v2-feat { display: flex; align-items: flex-start; gap: 16px; }
.v2-feat-ico {
    width: 46px; height: 46px; border-radius: 12px;
    background: var(--pl);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.v2-feat-body strong { display: block; font-size: 14px; font-weight: 700; color: var(--dk); margin-bottom: 3px; line-height: 1.3; }
.v2-feat-body span { font-size: 12px; color: var(--gr); }

/* (testimonial removed) */

/* Testimonial card */
.v2-testi {
    position: relative;
    z-index: 2;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,0.9);
    border-radius: 14px;
    padding: 14px 18px;
    margin-top: 18px;
    margin-bottom: 110px;
    box-shadow: 0 6px 20px rgba(180,130,30,0.10);
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 100%;
}
.v2-testi-q {
    display: flex; align-items: flex-start; gap: 8px;
    font-size: 12.5px; color: var(--dk); line-height: 1.55; font-weight: 500;
}
.v2-testi-q .qmark {
    color: var(--p); font-size: 22px; line-height: 0.9; font-weight: 800; flex-shrink: 0;
    font-family: Georgia, serif;
}
.v2-testi-foot {
    display: flex; align-items: center; gap: 10px;
}
.v2-testi-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: linear-gradient(135deg, #F5C840 0%, #E8960D 100%);
    color: #fff; font-size: 12px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; letter-spacing: 0.3px;
    box-shadow: 0 2px 6px rgba(232,150,13,0.30);
}
.v2-testi-meta strong {
    display: block; font-size: 13px; font-weight: 700; color: var(--dk); line-height: 1.2;
}
.v2-testi-meta span {
    display: block; font-size: 11.5px; color: var(--gr); line-height: 1.3;
}

/* City skyline */
.v2-city { position: absolute; bottom: 0; left: 0; right: 0; line-height: 0; z-index: 1; }
.v2-cloud {
    position: absolute; bottom: 0; left: 0; right: 0; line-height: 0;
    z-index: 0; pointer-events: none;
}

/* ═══════════ RIGHT PANEL ═══════════ */
.v2-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: var(--rbg);
    min-width: 0;
}

/* Help bar */
.v2-help {
    display: flex; align-items: center; justify-content: flex-end;
    gap: 10px; padding: 20px 48px; flex-shrink: 0;
}
.v2-help-ico { color: var(--gr); display: flex; align-items: center; }
.v2-help-need { font-size: 13px; color: var(--dk); }
.v2-help-sep { color: #d1d5db; font-size: 15px; }
.v2-help a { font-size: 13px; text-decoration: none; }
.v2-help a.lc { color: var(--p); font-weight: 700; }
.v2-help a.hc { color: var(--gr); }
.v2-help a:hover { opacity: .8; }

/* Right body */
.v2-body {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 12px 48px 20px;
    overflow-y: auto;
}

/* Card */
.v2-card {
    width: 100%; max-width: 640px;
    background: #fff;
    border: 1px solid #E8E8E8;
    border-radius: 16px;
    padding: 32px 40px;
    box-shadow: 0 2px 24px rgba(0,0,0,.06);
    margin-bottom: 14px;
}
.v2-card-title { font-size: 24px; font-weight: 800; color: var(--dk); margin: 0 0 4px; letter-spacing: -0.3px; }
.v2-card-sub { font-size: 13px; color: var(--gr); margin: 0 0 22px; }

/* Alerts */
.v2-alert { border-radius: 8px; padding: 10px 14px; font-size: 12.5px; margin-bottom: 16px; line-height: 1.5; }
.v2-alert-err { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
.v2-alert-ok  { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.v2-err { font-size: 11.5px; color: #dc2626; margin-top: 4px; display: block; }

/* Field */
.v2-field { margin-bottom: 16px; }
.v2-field > label { display: block; font-size: 13px; font-weight: 600; color: var(--dk); margin-bottom: 6px; }
.v2-iw { position: relative; display: flex; align-items: center; }
.v2-iw .il { position: absolute; left: 15px; color: #9ca3af; display: flex; pointer-events: none; }
.v2-iw .ir { position: absolute; right: 15px; color: #9ca3af; display: flex; cursor: pointer; }
.v2-inp {
    width: 100%; height: 48px;
    border: 1.5px solid var(--bd); border-radius: 10px;
    padding: 0 48px;
    font-size: 14px; font-family: 'Poppins', sans-serif; color: var(--dk);
    outline: none; background: #fff;
    transition: border-color .2s, box-shadow .2s;
}
.v2-inp::placeholder { color: #9ca3af; }
.v2-inp:focus { border-color: var(--p); box-shadow: 0 0 0 3px rgba(245,166,35,.12); }

/* Mid row */
.v2-mid { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
.v2-rem { display: flex; align-items: center; gap: 9px; }
.v2-rem input[type=checkbox] { width: 19px; height: 19px; accent-color: var(--p); cursor: pointer; flex-shrink: 0; border-radius: 4px; }
.v2-rem label { font-size: 13.5px; color: var(--dk); cursor: pointer; margin: 0; user-select: none; }
.v2-fgt { font-size: 13.5px; font-weight: 600; color: var(--p); text-decoration: none; cursor: pointer; }
.v2-fgt:hover { text-decoration: underline; color: var(--ph); }

/* Captcha */
.v2-cap-lbl { font-size: 13px; font-weight: 600; color: var(--dk); margin-bottom: 6px; }
.v2-cap-row { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
.v2-cap-img {
    width: 110px; height: 48px; border: 1.5px solid var(--bd); border-radius: 10px;
    overflow: hidden; background: #f9fafb;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.v2-cap-img img { height: 44px; width: auto; object-fit: contain; }
.v2-cap-btn {
    display: inline-flex; align-items: center; gap: 6px;
    height: 48px; padding: 0 14px;
    border: 1.5px solid var(--bd); border-radius: 10px;
    background: #fff; color: var(--p);
    font-size: 13px; font-weight: 600; font-family: 'Poppins', sans-serif;
    cursor: pointer; white-space: nowrap; flex-shrink: 0;
    transition: background .2s;
}
.v2-cap-btn:hover { background: var(--pl); }
.v2-cap-field { flex: 1; }
.v2-cap-inp {
    width: 100%; height: 48px;
    border: 1.5px solid var(--bd); border-radius: 10px;
    padding: 0 16px; font-size: 16px; letter-spacing: 5px; font-weight: 700;
    font-family: 'Poppins', sans-serif; outline: none; background: #fff; color: var(--dk);
    transition: border-color .2s;
}
.v2-cap-inp:focus { border-color: var(--p); box-shadow: 0 0 0 3px rgba(245,166,35,.12); }

/* Login button */
.v2-btn {
    display: flex; align-items: center; justify-content: center; gap: 11px;
    width: 100%; height: 52px;
    background: var(--p); color: #fff;
    border: none; border-radius: 12px;
    font-size: 16px; font-weight: 700; font-family: 'Poppins', sans-serif;
    cursor: pointer; letter-spacing: .2px;
    transition: background .2s, box-shadow .2s;
    box-shadow: 0 4px 16px rgba(245,166,35,.38);
}
.v2-btn:hover { background: var(--ph); box-shadow: 0 6px 22px rgba(245,166,35,.48); }

/* Forgot form extras */
.v2-fgt-acts { display: flex; gap: 12px; margin-top: 18px; }
.v2-fgt-sub {
    flex: 1; height: 54px; background: var(--p); color: #fff;
    border: none; border-radius: 10px;
    font-size: 15px; font-weight: 700; font-family: 'Poppins', sans-serif;
    cursor: pointer; transition: background .2s;
}
.v2-fgt-sub:hover { background: var(--ph); }
.v2-fgt-cancel {
    height: 54px; padding: 0 22px; background: #f3f4f6; color: #374151;
    border: none; border-radius: 10px; font-size: 14px; font-weight: 600;
    font-family: 'Poppins', sans-serif; text-decoration: none;
    display: inline-flex; align-items: center; cursor: pointer; transition: background .2s;
}
.v2-fgt-cancel:hover { background: #e5e7eb; color: #374151; }

/* Security strip */
.v2-sec {
    width: 100%; max-width: 640px;
    background: var(--pl);
    border: 1px solid rgba(245,166,35,.2);
    border-radius: 14px;
    padding: 16px 22px;
    display: flex; align-items: center; gap: 16px;
}
.v2-sec-ico {
    width: 42px; height: 42px; border-radius: 10px;
    background: rgba(245,166,35,.18);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.v2-sec-text strong { display: block; font-size: 13.5px; font-weight: 700; color: var(--dk); margin-bottom: 3px; }
.v2-sec-text span { font-size: 12px; color: var(--gr); }

/* Footer */
.v2-foot {
    display: flex; align-items: center; justify-content: center;
    gap: 18px; padding: 16px 48px;
    border-top: 1px solid #ebebeb; flex-shrink: 0;
}
.v2-foot-copy { font-size: 12.5px; color: #9ca3af; }
.v2-foot-sep { color: #d1d5db; }
.v2-foot a { font-size: 12.5px; color: var(--gr); text-decoration: none; }
.v2-foot a:hover { color: var(--p); }

/* ── Responsive ────────────────────────────────────────── */

/* 1440px – larger 15.6" at lower resolution */
@media (max-width: 1440px) {
    .v2-left        { width: 500px; padding: 30px 36px 0; }
    .v2-hero h1     { font-size: 28px; }
    .v2-feats       { gap: 12px; }
    .v2-body        { padding: 10px 42px 16px; }
    .v2-help        { padding: 18px 42px; }
    .v2-foot        { padding: 14px 42px; }
}

/* 1366px – most 14" / 15.6" laptops (1366×768 is the most common) */
@media (max-width: 1366px) {
    .v2-left        { width: 400px; padding: 22px 26px 0; }
    .v2-logo        { margin-bottom: 18px; }
    .v2-logo-text   { font-size: 22px; }
    .v2-hero        { margin-bottom: 14px; }
    .v2-hero h1     { font-size: 22px; letter-spacing: -.4px; line-height: 1.15; }
    .v2-hero p      { font-size: 12.5px; line-height: 1.5; }
    .v2-feats       { gap: 9px; }
    .v2-feat        { gap: 12px; }
    .v2-feat-ico    { width: 38px; height: 38px; border-radius: 9px; }
    .v2-feat-ico svg{ width: 19px; height: 19px; }
    .v2-feat-body strong { font-size: 13px; }
    .v2-feat-body span   { font-size: 11px; }
    .v2-testi       { margin-bottom: 110px; padding: 12px 16px; }
    .v2-testi-q     { font-size: 12px; }
    .v2-body        { padding: 6px 30px 12px; }
    .v2-help        { padding: 12px 30px; }
    .v2-foot        { padding: 8px 30px; }
    .v2-card        { padding: 22px 28px; max-width: 540px; }
    .v2-card-title  { font-size: 20px; }
    .v2-card-sub    { margin-bottom: 14px; }
    .v2-city svg    { max-height: 180px; }
}

/* 1280px – smaller 14" laptops */
@media (max-width: 1280px) {
    .v2-left        { width: 340px; padding: 18px 20px 0; }
    .v2-logo        { margin-bottom: 14px; }
    .v2-hero        { margin-bottom: 12px; }
    .v2-hero h1     { font-size: 20px; }
    .v2-feat-ico    { width: 34px; height: 34px; }
    .v2-feat        { gap: 11px; }
    .v2-testi       { display: none; }
    .v2-body        { padding: 6px 22px 10px; }
    .v2-help        { padding: 10px 22px; }
    .v2-foot        { padding: 8px 22px; }
    .v2-card        { padding: 20px 24px; max-width: 500px; }
    .v2-card-title  { font-size: 19px; }
    .v2-city svg    { max-height: 150px; }
}

/* Tall viewport – 880px+ allows full testimonial */
@media (min-height: 880px) {
    .v2-testi       { display: block; }
}

/* Short viewport – 879px or shorter hides testimonial */
@media (max-height: 879px) {
    .v2-testi       { display: none; }
}

/* Short viewport – 768px tall (1366×768 laptops, max-height wins over max-width) */
@media (max-height: 768px) {
    .v2-logo        { margin-bottom: 14px; }
    .v2-hero        { margin-bottom: 10px; }
    .v2-feats       { gap: 7px; }
    .v2-help        { padding-top: 8px; padding-bottom: 8px; }
    .v2-foot        { padding-top: 6px; padding-bottom: 6px; }
    .v2-card        { padding-top: 16px; padding-bottom: 16px; margin-bottom: 8px; }
    .v2-card-title  { font-size: 19px; }
    .v2-card-sub    { margin-bottom: 10px; }
    .v2-field       { margin-bottom: 9px; }
    .v2-field > label { margin-bottom: 4px; }
    .v2-inp         { height: 40px; font-size: 13.5px; }
    .v2-mid         { margin-bottom: 9px; }
    .v2-cap-lbl     { margin-bottom: 4px; }
    .v2-cap-row     { margin-bottom: 9px; }
    .v2-cap-img     { height: 40px; width: 96px; }
    .v2-cap-img img { height: 36px; }
    .v2-cap-btn     { height: 40px; padding: 0 12px; font-size: 12.5px; }
    .v2-cap-inp     { height: 40px; padding: 0 12px; }
    .v2-btn         { height: 42px; font-size: 13.5px; }
    .v2-sec         { padding: 10px 14px; gap: 12px; }
    .v2-sec-ico     { width: 34px; height: 34px; }
    .v2-sec-text strong { font-size: 12.5px; }
    .v2-sec-text span   { font-size: 11px; }
    .v2-city svg    { max-height: 120px; }
}

/* Very short – 700px tall (small laptops or zoomed browsers) */
@media (max-height: 700px) {
    .v2-city        { display: none; }
    .v2-card        { padding: 14px 18px; margin-bottom: 6px; }
    .v2-card-sub    { margin-bottom: 8px; }
    .v2-field       { margin-bottom: 7px; }
    .v2-cap-row     { margin-bottom: 7px; }
    .v2-inp, .v2-cap-btn, .v2-cap-img, .v2-cap-inp { height: 38px; }
    .v2-btn         { height: 40px; font-size: 13.5px; }
    .v2-sec         { padding: 10px 14px; }
}

/* Very short – 600px tall */
@media (max-height: 600px) {
    .v2-sec         { display: none; }
    .v2-card        { padding: 14px 18px; }
    .v2-btn         { height: 38px; }
}

/* 960px and below – tablets: stack left over right */
@media (max-width: 960px) {
    .v2-left        { width: 100%; height: auto; padding: 32px 28px 220px; }
    .v2-page        { flex-direction: column; overflow-y: auto; height: auto; min-height: 100vh; }
    .v2-right       { height: auto; }
    .v2-body        { padding: 24px 20px; justify-content: flex-start; }
    .v2-card        { padding: 24px 20px; }
    .v2-help        { padding: 16px 20px; }
    .v2-foot        { padding: 14px 20px; }
    .v2-sec         { max-width: 100%; }
}

/* 480px and below – phones */
@media (max-width: 480px) {
    .v2-left        { padding: 24px 16px 180px; }
    .v2-hero h1     { font-size: 22px; }
    .v2-body        { padding: 16px 12px; }
    .v2-card        { padding: 20px 16px; border-radius: 12px; }
    .v2-help        { padding: 12px 16px; }
    .v2-foot        { gap: 10px; flex-wrap: wrap; padding: 12px 16px; }
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

<div class="v2-shell">
<div class="v2-page">

{{-- ═══════════ LEFT PANEL ═══════════ --}}
<div class="v2-left">

    {{-- Logo --}}
    <a href="https://jero.works" class="v2-logo">
        <svg width="40" height="44" viewBox="0 0 40 44" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 2C9.5 2 1 10.5 1 21H39C39 10.5 30.5 2 20 2Z" fill="#111827"/>
            <path d="M1 21C1 21 7.5 17.5 20 21" stroke="rgba(255,255,255,0.35)" stroke-width="1.2"/>
            <path d="M39 21C39 21 32.5 17.5 20 21" stroke="rgba(255,255,255,0.35)" stroke-width="1.2"/>
            <line x1="20" y1="21" x2="20" y2="37" stroke="#111827" stroke-width="2.6" stroke-linecap="round"/>
            <path d="M20 37C20 40.31 17.31 43 14 43" stroke="#111827" stroke-width="2.6" stroke-linecap="round" fill="none"/>
        </svg>
        <span class="v2-logo-text">Jero</span>
    </a>

    {{-- Hero --}}
    <div class="v2-hero">
        <h1>AI-Powered Hiring.<br>Faster. Smarter. <span class="acc">Free.</span></h1>
        <p>The intelligent ATS &amp; hiring marketplace<br>trusted by 1000+ companies and recruiters.</p>
    </div>

    {{-- Features --}}
    <div class="v2-feats">
        <div class="v2-feat">
            <div class="v2-feat-ico">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><polygon points="13,2 7,14 11.5,14 9,23 17,10 12.5,10" fill="#F5A623"/></svg>
            </div>
            <div class="v2-feat-body">
                <strong>AI-Powered Automation</strong>
                <span>Automate hiring workflows end-to-end</span>
            </div>
        </div>
        <div class="v2-feat">
            <div class="v2-feat-ico">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <circle cx="9" cy="8" r="3.5" fill="#F5A623"/>
                    <circle cx="16" cy="8" r="3.5" fill="#F5A623" opacity=".55"/>
                    <path d="M2 21c0-3.87 3.13-7 7-7h5c3.87 0 7 3.13 7 7" stroke="#F5A623" stroke-width="2" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="v2-feat-body">
                <strong>Marketplace Access</strong>
                <span>Connect with top agencies &amp; talent</span>
            </div>
        </div>
        <div class="v2-feat">
            <div class="v2-feat-ico">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="14" width="4.5" height="8" rx="1.5" fill="#F5A623"/>
                    <rect x="9.5" y="10" width="4.5" height="12" rx="1.5" fill="#F5A623" opacity=".72"/>
                    <rect x="16" y="5" width="4.5" height="17" rx="1.5" fill="#F5A623" opacity=".5"/>
                </svg>
            </div>
            <div class="v2-feat-body">
                <strong>Smart Analytics</strong>
                <span>Insights that help you hire the best</span>
            </div>
        </div>
        <div class="v2-feat">
            <div class="v2-feat-ico">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2L3 6v5.5C3 17.25 6.8 22.6 12 24c5.2-1.4 9-6.75 9-12.5V6L12 2Z" fill="rgba(245,166,35,.28)" stroke="#F5A623" stroke-width="1.7"/>
                    <path d="M8 12l3 3 5-5" stroke="#F5A623" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="v2-feat-body">
                <strong>Secure &amp; Compliant</strong>
                <span>Enterprise-grade security for your data</span>
            </div>
        </div>
    </div>

    {{-- Testimonial card --}}
    <div class="v2-testi">
        <div class="v2-testi-q">
            <span class="qmark">&ldquo;</span>
            <span>Jero has transformed the way we hire. It&rsquo;s fast, simple and incredibly powerful.</span>
        </div>
        <div class="v2-testi-foot">
            <div class="v2-testi-avatar">RS</div>
            <div class="v2-testi-meta">
                <strong>Rahul Sharma</strong>
                <span>HR Head, TechNova Solutions</span>
            </div>
        </div>
    </div>

    {{-- Decorative clouds --}}
    <div class="v2-cloud" aria-hidden="true">
        <svg viewBox="0 0 500 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMax meet" style="width:100%;height:80px;">
            <g fill="#FFE9A8" opacity="0.7">
                <ellipse cx="60"  cy="50" rx="32" ry="9"/>
                <ellipse cx="85"  cy="48" rx="22" ry="7"/>
                <ellipse cx="180" cy="40" rx="28" ry="8"/>
                <ellipse cx="320" cy="55" rx="36" ry="10"/>
                <ellipse cx="345" cy="52" rx="20" ry="7"/>
                <ellipse cx="440" cy="45" rx="26" ry="8"/>
            </g>
        </svg>
    </div>

    {{-- City skyline --}}
    <div class="v2-city">
        <svg viewBox="0 0 500 230" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMax meet" style="width:100%">
            <defs>
                <linearGradient id="skyg" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#FFF9EC" stop-opacity="0"/>
                    <stop offset="100%" stop-color="#FFD060" stop-opacity="0.5"/>
                </linearGradient>
            </defs>
            <!-- Atmosphere -->
            <rect x="0" y="0" width="500" height="230" fill="url(#skyg)"/>

            <!-- Back layer (lightest) -->
            <rect x="0"   y="155" width="18"  height="75"  fill="#F5E4A8"/>
            <rect x="20"  y="135" width="14"  height="95"  fill="#F5E4A8"/>
            <rect x="36"  y="150" width="20"  height="80"  fill="#F5E0A0"/>
            <rect x="58"  y="125" width="16"  height="105" fill="#F5DC98"/>
            <rect x="76"  y="140" width="22"  height="90"  fill="#F5E0A0"/>
            <rect x="100" y="110" width="18"  height="120" fill="#F5DC98"/>
            <rect x="102" y="102" width="14"  height="10"  fill="#F5DC98"/>
            <rect x="120" y="130" width="24"  height="100" fill="#F5E4A8"/>
            <rect x="146" y="115" width="16"  height="115" fill="#F5DC98"/>
            <rect x="164" y="135" width="20"  height="95"  fill="#F5E0A0"/>
            <rect x="186" y="105" width="18"  height="125" fill="#F5DC98"/>
            <rect x="188" y="95"  width="14"  height="12"  fill="#F5DC98"/>
            <rect x="206" y="120" width="22"  height="110" fill="#F5E4A8"/>
            <rect x="230" y="108" width="16"  height="122" fill="#F5DC98"/>
            <rect x="232" y="98"  width="12"  height="12"  fill="#F5DC98"/>
            <rect x="248" y="125" width="24"  height="105" fill="#F5E0A0"/>
            <rect x="274" y="112" width="18"  height="118" fill="#F5DC98"/>
            <rect x="294" y="130" width="20"  height="100" fill="#F5E4A8"/>
            <rect x="316" y="100" width="16"  height="130" fill="#F5DC98"/>
            <rect x="318" y="90"  width="12"  height="12"  fill="#F5DC98"/>
            <rect x="334" y="120" width="24"  height="110" fill="#F5E0A0"/>
            <rect x="360" y="108" width="18"  height="122" fill="#F5DC98"/>
            <rect x="380" y="135" width="16"  height="95"  fill="#F5E4A8"/>
            <rect x="398" y="115" width="22"  height="115" fill="#F5DC98"/>
            <rect x="422" y="125" width="20"  height="105" fill="#F5E0A0"/>
            <rect x="444" y="140" width="14"  height="90"  fill="#F5E4A8"/>
            <rect x="460" y="130" width="18"  height="100" fill="#F5DC98"/>
            <rect x="480" y="145" width="20"  height="85"  fill="#F5E0A0"/>

            <!-- Mid layer -->
            <rect x="0"   y="165" width="25"  height="65"  fill="#F5C840"/>
            <rect x="27"  y="148" width="22"  height="82"  fill="#F0BC30"/>
            <rect x="51"  y="155" width="28"  height="75"  fill="#F5C840"/>
            <rect x="81"  y="143" width="20"  height="87"  fill="#F0BC30"/>
            <rect x="103" y="152" width="26"  height="78"  fill="#F5C840"/>
            <rect x="131" y="140" width="22"  height="90"  fill="#F0BC30"/>
            <rect x="155" y="150" width="30"  height="80"  fill="#F5C840"/>
            <rect x="187" y="138" width="22"  height="92"  fill="#F0BC30"/>
            <rect x="211" y="148" width="28"  height="82"  fill="#F5C840"/>
            <rect x="241" y="142" width="22"  height="88"  fill="#F0BC30"/>
            <rect x="265" y="152" width="26"  height="78"  fill="#F5C840"/>
            <rect x="293" y="140" width="24"  height="90"  fill="#F0BC30"/>
            <rect x="319" y="150" width="28"  height="80"  fill="#F5C840"/>
            <rect x="349" y="138" width="22"  height="92"  fill="#F0BC30"/>
            <rect x="373" y="148" width="26"  height="82"  fill="#F5C840"/>
            <rect x="401" y="142" width="22"  height="88"  fill="#F0BC30"/>
            <rect x="425" y="150" width="28"  height="80"  fill="#F5C840"/>
            <rect x="455" y="145" width="22"  height="85"  fill="#F0BC30"/>
            <rect x="479" y="155" width="21"  height="75"  fill="#F5C840"/>

            <!-- Front layer (darkest/tallest) — key skyscrapers -->
            <!-- Far left cluster -->
            <rect x="0"   y="175" width="32"  height="55"  fill="#F5B020"/>
            <rect x="34"  y="160" width="28"  height="70"  fill="#EAA010"/>
            <rect x="64"  y="150" width="20"  height="80"  fill="#F5B020"/>
            <!-- Left-center cluster -->
            <rect x="86"  y="168" width="26"  height="62"  fill="#EAA010"/>
            <rect x="114" y="155" width="30"  height="75"  fill="#F5B020"/>
            <rect x="146" y="162" width="22"  height="68"  fill="#EAA010"/>
            <!-- Tall center tower -->
            <rect x="170" y="105" width="26"  height="125" fill="#F5A623"/>
            <rect x="172" y="92"  width="22"  height="15"  fill="#F5A623"/>
            <rect x="175" y="84"  width="16"  height="10"  fill="#F5A623"/>
            <rect x="179" y="78"  width="8"   height="8"   fill="#F5A623"/>
            <!-- Windows on tall tower -->
            <rect x="174" y="110" width="5" height="6" rx="1" fill="rgba(255,255,255,0.25)"/>
            <rect x="183" y="110" width="5" height="6" rx="1" fill="rgba(255,255,255,0.25)"/>
            <rect x="174" y="122" width="5" height="6" rx="1" fill="rgba(255,255,255,0.25)"/>
            <rect x="183" y="122" width="5" height="6" rx="1" fill="rgba(255,255,255,0.25)"/>
            <rect x="174" y="134" width="5" height="6" rx="1" fill="rgba(255,255,255,0.25)"/>
            <rect x="183" y="134" width="5" height="6" rx="1" fill="rgba(255,255,255,0.25)"/>
            <!-- Right-center buildings -->
            <rect x="198" y="158" width="28"  height="72"  fill="#F5B020"/>
            <rect x="228" y="145" width="24"  height="85"  fill="#EAA010"/>
            <!-- Second tall tower -->
            <rect x="254" y="112" width="24"  height="118" fill="#F5A623"/>
            <rect x="256" y="100" width="20"  height="14"  fill="#F5A623"/>
            <rect x="259" y="93"  width="14"  height="9"   fill="#F5A623"/>
            <!-- Windows -->
            <rect x="258" y="118" width="4" height="5" rx="1" fill="rgba(255,255,255,0.25)"/>
            <rect x="266" y="118" width="4" height="5" rx="1" fill="rgba(255,255,255,0.25)"/>
            <rect x="258" y="130" width="4" height="5" rx="1" fill="rgba(255,255,255,0.25)"/>
            <rect x="266" y="130" width="4" height="5" rx="1" fill="rgba(255,255,255,0.25)"/>
            <rect x="258" y="142" width="4" height="5" rx="1" fill="rgba(255,255,255,0.25)"/>
            <rect x="266" y="142" width="4" height="5" rx="1" fill="rgba(255,255,255,0.25)"/>
            <!-- Right cluster -->
            <rect x="280" y="160" width="26"  height="70"  fill="#F5B020"/>
            <rect x="308" y="148" width="28"  height="82"  fill="#EAA010"/>
            <rect x="338" y="155" width="22"  height="75"  fill="#F5B020"/>
            <rect x="362" y="142" width="30"  height="88"  fill="#EAA010"/>
            <rect x="394" y="152" width="24"  height="78"  fill="#F5B020"/>
            <rect x="420" y="160" width="28"  height="70"  fill="#EAA010"/>
            <rect x="450" y="168" width="22"  height="62"  fill="#F5B020"/>
            <rect x="474" y="158" width="26"  height="72"  fill="#EAA010"/>

            <!-- Ground -->
            <rect x="0" y="218" width="500" height="12" fill="#F0A800"/>
        </svg>
    </div>
</div>{{-- /v2-left --}}

{{-- ═══════════ RIGHT PANEL ═══════════ --}}
<div class="v2-right">

    {{-- Help bar --}}
    <div class="v2-help">
        <span class="v2-help-ico">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="9" stroke="#6b7280" stroke-width="1.6"/><path d="M10 14.5v-1.2" stroke="#6b7280" stroke-width="1.6" stroke-linecap="round"/><path d="M10 11.5c0-1.8 2.2-1.8 2.2-3.3a2.2 2.2 0 0 0-4.4 0" stroke="#6b7280" stroke-width="1.6" stroke-linecap="round"/></svg>
        </span>
        <span class="v2-help-need">Need help?</span>
        <span class="v2-help-sep">|</span>
        <a href="javascript:void(0);" class="lc">Live Chat</a>
        <span class="v2-help-sep">|</span>
        <a href="https://jero.works/support-policy" target="_blank" class="hc">Help Center</a>
    </div>

    {{-- Body --}}
    <div class="v2-body">

        {{-- LOGIN FORM --}}
        <form class="v2-card" novalidate id="kt_sign_in_form1" action="{{ route('login') }}" method="post" style="<?php echo $loginForm; ?>">
            @csrf
            <h2 class="v2-card-title">Welcome Back! 👋</h2>
            <p class="v2-card-sub">Login to continue to your Jero workspace</p>

            @if(@$class && @$messsage)
                <div class="v2-alert v2-alert-err"><strong>{{ @$class }}</strong> {!! @$messsage !!}</div>
            @endif
            @if(session('accountnotverify'))
                <div class="v2-alert v2-alert-err">{{ session('accountnotverify') }}</div>
            @endif
            @if($errors->has('device_message'))
                <div class="v2-alert v2-alert-err">{{ $errors->first('device_message') }}</div>
            @endif

            {{-- Email --}}
            <div class="v2-field">
                <label>Email Address</label>
                <div class="v2-iw">
                    <span class="il">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="4" width="16" height="11" rx="2.2" stroke="#9ca3af" stroke-width="1.5"/><path d="M1 7l8 5.5L17 7" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round"/></svg>
                    </span>
                    <input class="v2-inp" type="text" name="email" placeholder="Enter your email" autocomplete="off" value="{{ old('email') }}"/>
                    <input type="hidden" name="url" value="{{ @$url }}"/>
                </div>
                @if($errors->has('email'))<span class="v2-err">{{ $errors->first('email') }}</span>@endif
            </div>

            {{-- Password --}}
            <div class="v2-field">
                <label>Password</label>
                <div class="v2-iw">
                    <span class="il">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="3" y="8" width="12" height="9" rx="2.2" stroke="#9ca3af" stroke-width="1.5"/><path d="M6 8V6a3 3 0 0 1 6 0v2" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round"/><circle cx="9" cy="12.5" r="1.3" fill="#9ca3af"/></svg>
                    </span>
                    <input class="v2-inp" type="password" name="password" id="v2pwd" placeholder="Enter your password" autocomplete="off"/>
                    <span class="ir" onclick="v2Eye()" style="user-select:none">
                        <svg id="v2eye" width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M1.5 9s2.8-5.5 7.5-5.5S16.5 9 16.5 9s-2.8 5.5-7.5 5.5S1.5 9 1.5 9Z" stroke="#9ca3af" stroke-width="1.5"/><circle cx="9" cy="9" r="2.2" stroke="#9ca3af" stroke-width="1.5"/></svg>
                    </span>
                </div>
                @if($errors->has('password'))<span class="v2-err"><strong>{{ $errors->first('password') }}</strong></span>@endif
            </div>

            {{-- Remember + Forgot --}}
            <div class="v2-mid">
                <div class="v2-rem">
                    <input type="checkbox" name="remember" id="v2rem" {{ old('remember') ? 'checked' : '' }}>
                    <label for="v2rem">Remember me</label>
                </div>
                <a href="javascript:void(0);" id="to-recover" class="v2-fgt">Forgot Password?</a>
            </div>

            {{-- Captcha --}}
            <div class="v2-cap-lbl">Enter the number in the box</div>
            <div class="v2-cap-row">
                <div class="v2-cap-img">
                    <img src="{{ route('captcha.generate') }}" id="captchaImg" alt="captcha">
                </div>
                <button type="button" onclick="refreshCaptcha()" class="v2-cap-btn">
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2 7.5A5.5 5.5 0 0 1 13 7.5" stroke="#F5A623" stroke-width="1.6" stroke-linecap="round"/><path d="M11 5.5L13 7.5l2-2" stroke="#F5A623" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M13 7.5A5.5 5.5 0 0 1 2 7.5" stroke="#F5A623" stroke-width="1.6" stroke-linecap="round"/><path d="M4 9.5L2 7.5 0 9.5" stroke="#F5A623" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Refresh
                </button>
                <div class="v2-cap-field">
                    <input type="text" name="captcha" class="v2-cap-inp" autocomplete="off" required>
                </div>
            </div>
            @if($errors->has('captcha'))
                <span class="v2-err" style="margin-top:-16px;margin-bottom:14px;display:block">{{ $errors->first('captcha') }}</span>
            @endif

            {{-- Submit --}}
            <button type="submit" id="kt_sign_in_submit" class="v2-btn">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><rect x="3" y="9" width="14" height="10" rx="2.5" stroke="white" stroke-width="1.7"/><path d="M7 9V7a3 3 0 0 1 6 0v2" stroke="white" stroke-width="1.7" stroke-linecap="round"/><circle cx="10" cy="14" r="1.5" fill="white"/></svg>
                <span class="indicator-label">Login to Jero</span>
                <span class="indicator-progress" style="display:none">Please wait…&nbsp;<span class="spinner-border spinner-border-sm align-middle"></span></span>
            </button>

            {{-- Sign Up --}}
            <div style="text-align:center;margin-top:16px;">
                <span style="font-size:13px;color:var(--gr);">Don't have an account?</span>
                <a href="{{ route('register') }}" style="font-size:13px;font-weight:700;color:var(--p);text-decoration:none;margin-left:5px;">Sign Up</a>
            </div>
        </form>

        {{-- FORGOT PASSWORD FORM --}}
        <form class="v2-card" novalidate id="recoverform" action="{{ route('password.emailsend') }}" method="post" style="<?php echo $resetForm; ?>">
            {{ csrf_field() }}
            <h2 class="v2-card-title">Forgot Password? 🔑</h2>
            <p class="v2-card-sub">Enter your email to reset your password.</p>

            @if(session('status'))
                <div class="v2-alert v2-alert-ok">{{ session('status') }}</div>
            @endif
            @if(session('accountnotverify'))
                <div class="v2-alert v2-alert-err">{{ session('accountnotverify') }}</div>
            @endif

            <div class="v2-field {{ $errors->has('email') ? 'has-error' : '' }}">
                <label>Email Address</label>
                <div class="v2-iw">
                    <span class="il">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="4" width="16" height="11" rx="2.2" stroke="#9ca3af" stroke-width="1.5"/><path d="M1 7l8 5.5L17 7" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round"/></svg>
                    </span>
                    <input type="email" name="email" id="email"
                           class="v2-inp {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           placeholder="Enter your email" value="{{ old('email') }}"
                           required autocomplete="off">
                </div>
                @if($errors->has('email'))<span class="v2-err"><strong>{{ $errors->first('email') }}</strong></span>@endif
            </div>

            <div class="v2-fgt-acts">
                <button type="submit" id="reset_password" class="v2-fgt-sub">
                    <span class="indicator-label">Reset Password</span>
                    <span class="indicator-progress" style="display:none">Please wait…&nbsp;<span class="spinner-border spinner-border-sm align-middle"></span></span>
                </button>
                <a href="{{ route('login') }}" class="v2-fgt-cancel">Cancel</a>
            </div>
        </form>

        {{-- Security strip --}}
        <div class="v2-sec">
            <div class="v2-sec-ico">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 6v5.5C3 17.25 6.8 22.6 12 24c5.2-1.4 9-6.75 9-12.5V6L12 2Z" fill="rgba(245,166,35,.28)" stroke="#F5A623" stroke-width="1.7"/><path d="M8 12l3 3 5-5" stroke="#F5A623" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div class="v2-sec-text">
                <strong>Secure &bull; Trusted &bull; Encrypted</strong>
                <span>Your information is protected and safe with us.</span>
            </div>
        </div>

    </div>{{-- /v2-body --}}

    {{-- Footer --}}
    <div class="v2-foot">
        <span class="v2-foot-copy">© 2025 Jero. All rights reserved.</span>
        <span class="v2-foot-sep">|</span>
        <a href="/privacy-policy">Privacy Policy</a>
        <span class="v2-foot-sep">|</span>
        <a href="/terms">Terms of Service</a>
    </div>

</div>{{-- /v2-right --}}

</div>{{-- /v2-page --}}
</div>{{-- /v2-shell --}}
@endsection

<script>
function refreshCaptcha() {
    document.getElementById('captchaImg').src = "{{ route('captcha.generate') }}?t=" + Date.now();
}
function v2Eye() {
    var i = document.getElementById('v2pwd'), e = document.getElementById('v2eye');
    if (i.type === 'password') {
        i.type = 'text';
        e.innerHTML = '<path d="M1.5 9s2.8-5.5 7.5-5.5S16.5 9 16.5 9s-2.8 5.5-7.5 5.5S1.5 9 1.5 9Z" stroke="#6b7280" stroke-width="1.5"/><circle cx="9" cy="9" r="2.2" stroke="#6b7280" stroke-width="1.5"/><line x1="2" y1="2" x2="17" y2="17" stroke="#6b7280" stroke-width="1.5" stroke-linecap="round"/>';
    } else {
        i.type = 'password';
        e.innerHTML = '<path d="M1.5 9s2.8-5.5 7.5-5.5S16.5 9 16.5 9s-2.8 5.5-7.5 5.5S1.5 9 1.5 9Z" stroke="#9ca3af" stroke-width="1.5"/><circle cx="9" cy="9" r="2.2" stroke="#9ca3af" stroke-width="1.5"/>';
    }
}
</script>
