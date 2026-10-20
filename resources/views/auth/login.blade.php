@extends('layouts.metronic_front_app')

<?php
$isSignup = (isset($initialMode) && $initialMode === 'signup') 
    || request()->routeIs('register') 
    || request('mode') === 'signup' 
    || session('signup_mode');

if (session('status')) {
    $activeState = 'recover';
} elseif ($isSignup) {
    $activeState = 'signup';
} else {
    $activeState = 'login';
}

$loginStyle   = ($activeState === 'login') ? '' : 'display:none;';
$signupStyle  = ($activeState === 'signup') ? '' : 'display:none;';
$recoverStyle = ($activeState === 'recover') ? '' : 'display:none;';
?>

<title id="jw-page-title">{{ $activeState === 'signup' ? 'Create your Jero.works account 🚀' : 'Sign In - jero.works' }}</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* Presentation-only login & signup unified shell: master layout, visual language, and responsiveness strictly preserved. */
:root {
    --jw-navy: #0b1f44;
    --jw-blue: #2563eb;
    --jw-violet: #6d3ff2;
    --jw-purple: #7c3aed;
    --jw-green: #16b364;
    --jw-orange: #ff6b1a;
    --jw-muted: #667085;
    --jw-soft: #f6f8ff;
    --jw-border: #e4e7ec;
    --jw-white: #ffffff;
    --jw-danger: #dc2626;
}
* { box-sizing: border-box; }
html, body { margin: 0 !important; min-height: 100%; }
body, button, input, select, textarea, span, p, h1, h2, h3, h4, h5, h6, a, div, label {
    font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, sans-serif !important;
}
body { color: var(--jw-navy); background: var(--jw-soft); }
.jw-auth-shell {
    min-height: 100vh;
    width: 100%;
    overflow-x: hidden;
    background:
        radial-gradient(circle at 6% 7%, rgba(37, 99, 235, .08), transparent 30%),
        radial-gradient(circle at 82% 18%, rgba(109, 63, 242, .09), transparent 29%),
        radial-gradient(circle at 62% 84%, rgba(255, 107, 26, .06), transparent 28%),
        linear-gradient(135deg, #ffffff 0%, #f8faff 48%, #fbf9ff 100%);
}
.jw-auth-inner {
    width: min(1500px, calc(100% - 64px));
    min-height: 100vh;
    margin: 0 auto;
    display: grid;
    grid-template-rows: auto minmax(0, 1fr) auto;
    gap: 18px;
}
.jw-auth-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 24px;
    padding-top: 28px;
}
.jw-logo {
    display: inline-flex;
    flex-direction: column;
    align-items: flex-start;
    text-decoration: none;
    color: var(--jw-navy);
}
.jw-logo img { display: block; height: 46px; width: auto; max-width: 245px; }
.jw-logo span {
    margin-left: 54px;
    margin-top: 1px;
    font-size: 13px;
    font-weight: 500;
    color: #69728a;
}
.jw-account-cta {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 14px;
    min-height: 44px;
    font-size: 13px;
    color: #404967;
    white-space: nowrap;
}
.jw-create-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    min-height: 42px;
    padding: 0 17px;
    color: var(--jw-violet);
    background: rgba(255, 255, 255, .86);
    border: 1.5px solid #7565ff;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: background .18s ease, border-color .18s ease, transform .18s ease;
}
.jw-create-btn:hover,
.jw-create-btn:focus-visible {
    background: #fff;
    border-color: var(--jw-blue);
    color: var(--jw-blue);
    outline: none;
    transform: translateY(-1px);
}
.jw-auth-main {
    display: grid;
    grid-template-columns: minmax(0, 1.08fr) minmax(430px, .92fr);
    align-items: center;
    gap: clamp(30px, 4.2vw, 62px);
    min-height: 0;
}
.jw-hero { display: flex; flex-direction: column; min-width: 0; }
.jw-hero-copy { max-width: 700px; margin-left: 6px; }
.jw-hero-copy h1 {
    margin: 0;
    color: var(--jw-navy);
    font-size: clamp(34px, 2.95vw, 44px) !important;
    font-weight: 800;
    line-height: 1.14;
    letter-spacing: 0;
}
.jw-gradient-text {
    display: inline-block;
    color: var(--jw-orange);
    background: linear-gradient(90deg, var(--jw-orange) 0%, #e04e9b 49%, var(--jw-violet) 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}
.jw-hero-copy p {
    margin: 12px 0 0;
    color: #5f6b86;
    font-size: 14px;
    line-height: 1.55;
    font-weight: 500;
    white-space: nowrap;
}
.jw-ecosystem {
    position: relative;
    width: min(720px, 100%);
    aspect-ratio: 1.16 / .82;
    margin: 14px auto 12px;
}
.jw-eco-lines {
    position: absolute;
    inset: 3% 5% 0;
    width: 90%;
    height: 95%;
    overflow: visible;
    z-index: 1;
}
.jw-eco-orbit { fill: none; stroke: #cfdbf7; stroke-width: 1.25; stroke-dasharray: 5 8; }
.jw-eco-path { fill: none; stroke-width: 2.2; stroke-linecap: round; stroke-linejoin: round; opacity: .78; }
.jw-eco-node { position: absolute; z-index: 2; width: 190px; text-align: center; }
.jw-eco-node.top { left: 50%; top: 0; transform: translateX(-50%); }
.jw-eco-node.left { left: 0; bottom: 5%; }
.jw-eco-node.right { right: 0; bottom: 5%; }
.jw-node-icon {
    width: 86px;
    height: 86px;
    margin: 0 auto 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--node-color);
    background: rgba(255, 255, 255, .94);
    border: 1px solid rgba(230, 235, 247, .95);
    border-radius: 50%;
    box-shadow: 0 18px 38px rgba(23, 40, 84, .08), 0 2px 5px rgba(23, 40, 84, .05);
}
.jw-node-icon::before {
    content: '';
    position: absolute;
    width: 98px;
    height: 98px;
    border-radius: 50%;
    background: var(--node-glow);
    filter: blur(20px);
    z-index: -1;
}
.jw-node-icon svg { width: 42px; height: 42px; }
.jw-node-copy {
    width: 100%;
    padding: 6px 10px 7px;
    background: rgba(255, 255, 255, .82);
    border-radius: 12px;
}
.jw-eco-node strong {
    display: block;
    color: var(--node-color);
    font-size: 13px;
    font-weight: 700;
    line-height: 1.25;
}
.jw-eco-node span {
    display: block;
    margin-top: 5px;
    color: #17233f;
    font-size: 11.5px;
    font-weight: 500;
    line-height: 1.55;
}
.jw-hub {
    position: absolute;
    z-index: 3;
    left: 50%;
    top: 55%;
    width: 178px;
    height: 178px;
    transform: translate(-50%, -50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: rgba(255, 255, 255, .96);
    border: 1px solid rgba(231, 235, 247, .96);
    border-radius: 50%;
    box-shadow: 0 22px 48px rgba(23, 40, 84, .1), 0 2px 5px rgba(23, 40, 84, .04);
}
.jw-hub::before {
    content: '';
    position: absolute;
    inset: -26px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255, 107, 26, .12), transparent 66%);
    z-index: -1;
}
.jw-hub .brand-mark { font-size: 31px; line-height: 1; color: #75540a; }
.jw-hub strong {
    display: block;
    margin-top: 5px;
    color: #111827;
    font-size: 27px;
    font-weight: 800;
}
.jw-hub span {
    margin-top: 7px;
    color: #23304f;
    font-size: 12px;
    line-height: 1.35;
    font-weight: 500;
}
.jw-hub i {
    display: block;
    width: 34px;
    height: 2px;
    margin-top: 12px;
    background: var(--jw-orange);
    border-radius: 999px;
}
.jw-metrics {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    align-items: center;
    gap: 0;
    width: min(680px, 100%);
    margin: 0 auto;
    padding: 18px 16px;
    background: rgba(255, 255, 255, .93);
    border: 1px solid rgba(230, 235, 247, .95);
    border-radius: 14px;
    box-shadow: 0 16px 32px rgba(23, 40, 84, .06);
}
.jw-metric {
    position: relative;
    display: flex;
    align-items: center;
    gap: 11px;
    min-width: 0;
    padding: 0 12px;
}
.jw-metric + .jw-metric::before {
    content: '';
    position: absolute;
    left: 0;
    top: 13%;
    bottom: 13%;
    width: 1px;
    background: #edf0f7;
}
.jw-metric-icon {
    width: 44px;
    height: 44px;
    flex: 0 0 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--metric-color);
    background: var(--metric-bg);
    border-radius: 50%;
}
.jw-metric-icon svg { width: 24px; height: 24px; }
.jw-metric strong { display: block; color: var(--jw-navy); font-size: 15px; font-weight: 800; line-height: 1.2; }
.jw-metric span { display: block; margin-top: 2px; color: #536078; font-size: 10.5px; font-weight: 500; line-height: 1.25; }
.jw-privacy-note {
    display: flex;
    align-items: center;
    gap: 8px;
    width: min(680px, 100%);
    margin: 24px auto 0;
    color: #46516d;
    font-size: 12px;
    font-weight: 500;
}
.jw-privacy-note svg { color: var(--jw-blue); width: 17px; height: 17px; }
.jw-login-zone { display: flex; justify-content: center; min-width: 0; }
.jw-card-form {
    width: min(570px, 100%);
    margin: 0 auto;
    padding: clamp(28px, 3vw, 42px) clamp(30px, 3.5vw, 44px);
    background: rgba(255, 255, 255, .96);
    border: 1px solid rgba(228, 231, 236, .92);
    border-radius: 24px;
    box-shadow: 0 25px 60px rgba(23, 40, 84, .10), 0 2px 6px rgba(23, 40, 84, .04);
}
.jw-fade-enter {
    animation: jwCardFade .2s ease-out;
}
@keyframes jwCardFade {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
.jw-card-form h2 {
    margin: 0;
    color: var(--jw-navy);
    font-size: 31px !important;
    font-weight: 800 !important;
    line-height: 1.2;
    text-align: center;
}
.jw-card-form .sub {
    margin: 8px 0 26px;
    color: #66708e;
    font-size: 14px;
    line-height: 1.5;
    font-weight: 500;
    text-align: center;
}
.jw-alert {
    margin: 0 0 18px;
    padding: 11px 13px;
    color: #991b1b;
    background: #fef2f2;
    border: 1px solid #fee2e2;
    border-radius: 10px;
    font-size: 12px;
    line-height: 1.5;
}
.jw-alert-ok,
.jw-alert-success { color: #166534; background: #f0fdf4; border-color: #bbf7d0; }
.jw-alert-err,
.jw-alert-error,
.jw-alert-danger { color: #991b1b; background: #fef2f2; border-color: #fee2e2; }

/* Corporate Domain Collision Banner */
.jw-collision-box {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin: 0 0 18px;
    padding: 14px 16px;
    background: #fff7ed;
    border: 1.5px solid #fed7aa;
    border-radius: 12px;
    animation: jwCardFade .2s ease-out;
}
.jw-collision-icon {
    flex: 0 0 24px;
    margin-top: 2px;
}
.jw-collision-content {
    flex: 1;
    min-width: 0;
}
.jw-collision-content strong {
    display: block;
    color: #9a3412;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.35;
}
.jw-collision-content p {
    margin: 4px 0 10px;
    color: #7c2d12;
    font-size: 12px;
    line-height: 1.5;
}
.jw-collision-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.jw-btn-mini-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    background: #ea580c;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background .15s ease;
}
.jw-btn-mini-primary:hover { background: #c2410c; }
.jw-btn-mini-link {
    font-size: 12px;
    color: #9a3412;
    text-decoration: underline;
    font-weight: 600;
}
.jw-btn-mini-link:hover { color: #7c2d12; }

/* Domain badge next to field label */
.jw-domain-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 999px;
}
.jw-domain-tag.corporate {
    color: #15803d;
    background: #dcfce7;
    border: 1px solid #bbf7d0;
}
.jw-domain-tag.webmail {
    color: #475467;
    background: #f2f4f7;
    border: 1px solid #eaecf0;
}
.jw-domain-tag.exists {
    color: #b91c1c;
    background: #fee2e2;
    border: 1px solid #fecaca;
}

.jw-field { margin-bottom: 18px; }
.jw-field-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 10px;
}
.jw-field label,
.jw-field-row label {
    display: block;
    margin: 0 0 10px;
    color: var(--jw-navy);
    font-size: 12px;
    font-weight: 700;
}
.jw-field-row label { margin: 0; }
.jw-fgt { color: var(--jw-violet); font-size: 12px; font-weight: 600; text-decoration: none; }
.jw-fgt:hover,
.jw-fgt:focus-visible { color: var(--jw-blue); text-decoration: underline; outline: none; }
.jw-iw { position: relative; display: flex; align-items: center; }
.jw-iw .il,
.jw-iw .ir,
.jw-iw .ok-mark {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    color: #8b95af;
}
.jw-iw .il { left: 16px; pointer-events: none; }
.jw-iw .ir { right: 16px; cursor: pointer; }
.jw-iw .ok-mark { right: 16px; pointer-events: none; }
.jw-inp {
    width: 100%;
    height: 54px;
    padding: 0 48px;
    color: var(--jw-navy);
    background: #fff;
    border: 1.5px solid #d7ddea;
    border-radius: 10px;
    outline: none;
    font-size: 14px;
    font-weight: 500;
    transition: border-color .16s ease, box-shadow .16s ease;
}
.jw-inp::placeholder { color: #8993ad; font-weight: 500; }
.jw-inp:hover { border-color: #c7cfdf; }
.jw-inp:focus { border-color: var(--jw-blue); box-shadow: 0 0 0 4px rgba(37, 99, 235, .11); }
.jw-inp.is-ok { border-color: var(--jw-green); }
.jw-inp.is-invalid { border-color: var(--jw-danger); }
select.jw-inp,
.jw-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    cursor: pointer;
    background-color: #fff;
    padding-right: 44px;
}
select.jw-inp:invalid,
select.jw-inp option[value=""] {
    color: #8993ad;
}
select.jw-inp option {
    color: var(--jw-navy);
    font-size: 13.5px;
    padding: 10px 12px;
    background: #fff;
}
.jw-err {
    display: block;
    margin-top: 7px;
    color: var(--jw-danger);
    font-size: 11.5px;
    font-weight: 500;
    line-height: 1.45;
}

/* Scoped styling to ensure x-jero-phone-input harmonizes with master shell */
.jw-card-form .jero-phone-field-group {
    margin-bottom: 0 !important;
}
.jw-card-form .jero-phone-label {
    display: block !important;
    margin: 0 0 10px !important;
    color: var(--jw-navy) !important;
    font-size: 12px !important;
    font-weight: 700 !important;
}
.jw-card-form .jero-phone-input-wrap {
    height: 54px !important;
    min-height: 54px !important;
    max-height: 54px !important;
    border: 1.5px solid #d7ddea !important;
    border-radius: 10px !important;
    transition: border-color .16s ease, box-shadow .16s ease !important;
}
.jw-card-form .jero-phone-input-wrap:focus-within {
    border-color: var(--jw-blue) !important;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, .11) !important;
}
.jw-card-form .jero-phone-native-input {
    font-size: 14px !important;
    font-weight: 500 !important;
    color: var(--jw-navy) !important;
}

/* Password strength bar */
.jw-pwd-strength-bar {
    display: flex;
    gap: 5px;
    height: 4px;
    margin-top: 8px;
}
.jw-pwd-seg {
    flex: 1;
    height: 100%;
    background: #e2e8f0;
    border-radius: 2px;
    transition: background .2s ease;
}
.jw-pwd-seg.weak { background: #ef4444; }
.jw-pwd-seg.fair { background: #f59e0b; }
.jw-pwd-seg.good { background: #3b82f6; }
.jw-pwd-seg.strong { background: #10b981; }
.jw-pwd-hint {
    margin-top: 6px;
    font-size: 11.5px;
    color: #64748b;
    font-weight: 500;
}

.jw-sec {
    margin: 0 0 20px;
    padding: 13px;
    background: #fbfcff;
    border: 1px solid #eef1f8;
    border-radius: 12px;
}
.jw-sec-head { display: flex; align-items: flex-start; gap: 9px; margin-bottom: 12px; }
.jw-sec-icon {
    width: 22px;
    height: 22px;
    flex: 0 0 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--jw-violet);
    background: #f2efff;
    border-radius: 50%;
}
.jw-sec-icon svg { width: 15px; height: 15px; }
.jw-sec-title { display: block; color: var(--jw-navy); font-size: 12.5px; font-weight: 700; line-height: 1.2; }
.jw-sec-bracket { display: block; margin-top: 2px; color: #69738c; font-size: 11.5px; font-weight: 500; }
.jw-cap-row {
    display: grid;
    grid-template-columns: minmax(142px, 36%) 42px minmax(0, 1fr);
    align-items: center;
    gap: 8px;
}
.jw-cap-box {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 54px;
    padding: 0 8px;
    background: #fff;
    border: 1.5px solid #d7ddea;
    border-radius: 10px;
    overflow: hidden;
}
.jw-cap-code-img { max-width: 100%; width: 160px; height: 50px; object-fit: contain; display: block; user-select: none; }
.jw-cap-refresh-btn {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--jw-blue);
    background: #fff;
    border: 1.5px solid #d7ddea;
    border-radius: 10px;
    cursor: pointer;
    transition: border-color .16s ease, background .16s ease, transform .16s ease;
}
.jw-cap-refresh-btn:hover,
.jw-cap-refresh-btn:focus-visible {
    background: #f5f7ff;
    border-color: var(--jw-blue);
    outline: none;
    transform: translateY(-1px);
}
.jw-cap-refresh-btn svg { width: 18px; height: 18px; }
.jw-cap-inp {
    width: 100%;
    height: 54px;
    padding: 0 14px;
    color: var(--jw-navy);
    background: #fff;
    border: 1.5px solid #d7ddea;
    border-radius: 10px;
    outline: none;
    font-size: 13px;
    font-weight: 500;
    transition: border-color .16s ease, box-shadow .16s ease;
}
.jw-cap-inp::placeholder { color: #8993ad; }
.jw-cap-inp:focus { border-color: var(--jw-blue); box-shadow: 0 0 0 4px rgba(37, 99, 235, .11); }
.jw-cap-inp.is-ok { border-color: var(--jw-green); }
.jw-btn {
    width: 100%;
    height: 60px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    color: #fff;
    background: linear-gradient(90deg, var(--jw-blue) 0%, var(--jw-violet) 100%);
    border: 0;
    border-radius: 10px;
    box-shadow: 0 12px 25px rgba(88, 74, 234, .28);
    cursor: pointer;
    font-size: 20px;
    font-weight: 700;
    transition: transform .16s ease, box-shadow .16s ease, filter .16s ease;
}
.jw-btn:hover,
.jw-btn:focus-visible {
    outline: none;
    filter: saturate(1.06);
    transform: translateY(-1px);
    box-shadow: 0 16px 30px rgba(88, 74, 234, .34);
}
.jw-btn[disabled] { opacity: .74; cursor: not-allowed; transform: none; }
.jw-btn .arrow { display: inline-flex; align-items: center; transition: transform .16s ease; }
.jw-btn:hover .arrow { transform: translateX(3px); }

/* Switch link beneath forms */
.jw-switch-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    margin-top: 18px;
    font-size: 13px;
    color: #4b5563;
    font-weight: 500;
}
.jw-link-switch {
    color: var(--jw-violet);
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    transition: color .15s ease;
}
.jw-link-switch:hover,
.jw-link-switch:focus-visible {
    color: var(--jw-blue);
    text-decoration: underline;
}

.jw-card-divider {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 22px 0 18px;
}
.jw-card-divider::before,
.jw-card-divider::after { content: ''; height: 1px; flex: 1; background: #eef1f8; }
.jw-card-divider span {
    width: 32px;
    height: 32px;
    margin: 0 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #7180b8;
}
.jw-card-divider svg { width: 19px; height: 19px; }
.jw-card-trust {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 16px;
    background: linear-gradient(90deg, #f3f1ff 0%, #f7f8ff 100%);
    border: 1px solid #edf0fb;
    border-radius: 12px;
}
.jw-trust-icon {
    width: 54px;
    height: 54px;
    flex: 0 0 54px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--jw-violet);
    background: #ebe5ff;
    border-radius: 50%;
}
.jw-trust-icon svg { width: 28px; height: 28px; }
.jw-card-trust strong { display: block; color: var(--jw-navy); font-size: 15px; font-weight: 800; line-height: 1.35; }
.jw-card-trust span { display: block; margin-top: 4px; color: #3d4967; font-size: 12.5px; font-weight: 500; line-height: 1.55; }
.jw-fgt-acts { display: flex; gap: 12px; margin-top: 18px; }
.jw-fgt-sub,
.jw-fgt-cancel {
    min-height: 50px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
}
.jw-fgt-sub { flex: 1; color: #fff; background: linear-gradient(90deg, var(--jw-blue) 0%, var(--jw-violet) 100%); border: 0; cursor: pointer; }
.jw-fgt-cancel { padding: 0 20px; color: #334155; background: #f4f6fb; border: 1px solid #e2e8f0; cursor: pointer; }
.jw-footer {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
    align-items: center;
    gap: 20px;
    padding: 24px 0 20px;
    border-top: 1px solid #e7ebf3;
    color: #5d6780;
    font-size: 11.5px;
    font-weight: 500;
}
.jw-footer-copy { justify-self: start; }
.jw-footer-secure {
    justify-self: center;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #35405c;
}
.jw-footer-secure svg { width: 15px; height: 15px; color: var(--jw-green); }
.jw-footer-links {
    justify-self: end;
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 26px;
}
.jw-footer-links a {
    color: var(--jw-navy);
    text-decoration: none;
    font-size: 11.5px;
    font-weight: 500;
}
.jw-footer-links a:hover,
.jw-footer-links a:focus-visible { color: var(--jw-blue); outline: none; }
.jw-locale {
    min-height: 36px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 0 13px;
    color: #48536f;
    background: #fff;
    border: 1px solid #d7ddea;
    border-radius: 8px;
    font-size: 11.5px;
    font-weight: 500;
    cursor: pointer;
}

/* Metronic 15.6" Screen Adaptations (1366x768 & 1920x1080) */
@media (max-width: 1366px) {
    .jw-auth-inner { width: min(1234px, calc(100% - 42px)); gap: 8px; }
    .jw-auth-top { padding-top: 18px; }
    .jw-auth-main { grid-template-columns: minmax(0, 1.06fr) minmax(420px, .94fr); gap: 34px; }
    .jw-logo img { height: 40px; }
    .jw-logo span { margin-left: 48px; font-size: 11.5px; }
    .jw-hero-copy h1 { font-size: 34px !important; line-height: 1.12; }
    .jw-hero-copy p { font-size: 12.5px; line-height: 1.45; margin-top: 8px; }
    .jw-ecosystem { width: min(625px, 100%); margin-top: 6px; margin-bottom: 8px; }
    .jw-node-icon { width: 78px; height: 78px; margin-bottom: 5px; }
    .jw-node-icon svg { width: 37px; height: 37px; }
    .jw-node-icon::before { width: 88px; height: 88px; }
    .jw-eco-node { width: 172px; }
    .jw-eco-node.left { left: -1%; bottom: 3%; }
    .jw-eco-node.right { right: -1%; bottom: 3%; }
    .jw-node-copy { padding: 5px 11px 6px; }
    .jw-eco-node strong { font-size: 12px; }
    .jw-eco-node span { font-size: 10.5px; line-height: 1.42; }
    .jw-hub { width: 158px; height: 158px; }
    .jw-hub strong { font-size: 23px; }
    .jw-hub span { font-size: 10.5px; }
    .jw-metrics { padding: 12px 12px; }
    .jw-metric { gap: 9px; padding: 0 9px; }
    .jw-metric-icon { width: 36px; height: 36px; flex-basis: 36px; }
    .jw-metric-icon svg { width: 20px; height: 20px; }
    .jw-metric strong { font-size: 13.5px; }
    .jw-card-form { width: min(520px, 100%); padding: 22px 34px; }
    .jw-card-form h2 { font-size: 25px !important; }
    .jw-card-form .sub { margin-bottom: 18px; font-size: 12.5px; }
    .jw-field { margin-bottom: 12px; }
    .jw-inp { height: 46px; font-size: 13px; }
    .jw-card-form .jero-phone-input-wrap { height: 46px !important; min-height: 46px !important; max-height: 46px !important; }
    .jw-card-form .jero-phone-native-input { font-size: 13px !important; }
    .jw-cap-row { grid-template-columns: minmax(136px, 36%) 40px minmax(0, 1fr); }
    .jw-sec { margin-bottom: 14px; padding: 12px; }
    .jw-cap-box,
    .jw-cap-inp { height: 42px; }
    .jw-cap-code-img { height: 38px; }
    .jw-cap-refresh-btn { width: 38px; height: 38px; }
    .jw-btn { height: 48px; font-size: 17px; }
    .jw-card-divider { margin: 14px 0 12px; }
    .jw-card-trust { padding: 13px 15px; }
    .jw-trust-icon { width: 44px; height: 44px; flex-basis: 44px; }
    .jw-card-trust strong { font-size: 13px; }
    .jw-card-trust span { font-size: 11.5px; line-height: 1.45; }
    .jw-footer { padding: 12px 0 10px; }
    .jw-privacy-note { margin-top: 10px; }
}
@media (max-width: 1120px) {
    .jw-auth-main { grid-template-columns: minmax(0, .88fr) minmax(420px, 1fr); gap: 24px; }
    .jw-hero-copy p { white-space: normal; }
    .jw-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); row-gap: 14px; }
    .jw-metric:nth-child(3)::before { display: none; }
}
@media (max-width: 920px) {
    .jw-auth-inner { width: min(720px, calc(100% - 32px)); }
    .jw-auth-main { grid-template-columns: 1fr; align-items: start; }
    .jw-login-zone { order: 1; }
    .jw-hero { order: 2; }
    .jw-auth-top { align-items: center; }
    .jw-account-cta span { display: none; }
    .jw-card-form { max-width: 100%; }
    .jw-hero-copy { margin-top: 8px; }
    .jw-footer { grid-template-columns: 1fr; justify-items: center; text-align: center; }
    .jw-footer-copy,
    .jw-footer-secure,
    .jw-footer-links { justify-self: center; }
}
@media (max-width: 560px) {
    .jw-auth-inner { width: min(100% - 24px, 430px); gap: 14px; }
    .jw-auth-top { padding-top: 16px; }
    .jw-logo img { height: 34px; }
    .jw-logo span { margin-left: 41px; font-size: 10.5px; }
    .jw-create-btn { min-height: 36px; padding: 0 10px; font-size: 11px; }
    .jw-card-form { padding: 24px 18px; border-radius: 18px; }
    .jw-card-form h2 { font-size: 23px !important; }
    .jw-card-form .sub { margin-bottom: 22px; font-size: 12px; }
    .jw-inp { height: 48px; font-size: 12.5px; }
    .jw-card-form .jero-phone-input-wrap { height: 48px !important; min-height: 48px !important; max-height: 48px !important; }
    .jw-cap-row { grid-template-columns: 1fr 42px; }
    .jw-cap-field { grid-column: 1 / -1; }
    .jw-btn { height: 50px; font-size: 16px; }
    .jw-card-trust { align-items: flex-start; padding: 14px; }
    .jw-trust-icon { width: 42px; height: 42px; flex-basis: 42px; }
    .jw-hero-copy h1 { font-size: 30px !important; }
    .jw-hero-copy p { font-size: 12px; white-space: normal; }
    .jw-ecosystem { aspect-ratio: .92 / .94; margin-top: 16px; }
    .jw-eco-node { width: 124px; }
    .jw-eco-node.top { top: 0; }
    .jw-eco-node.left { left: 0; bottom: 1%; }
    .jw-eco-node.right { right: 0; bottom: 1%; }
    .jw-node-icon { width: 58px; height: 58px; margin-bottom: 5px; }
    .jw-node-icon svg { width: 28px; height: 28px; }
    .jw-eco-node span { display: none; }
    .jw-hub { width: 116px; height: 116px; }
    .jw-hub strong { font-size: 17px; }
    .jw-hub span { font-size: 9px; }
    .jw-metrics { grid-template-columns: 1fr; }
    .jw-metric + .jw-metric::before { display: none; }
    .jw-privacy-note { font-size: 11px; align-items: flex-start; }
    .jw-footer-links { flex-wrap: wrap; gap: 16px; }
}
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        scroll-behavior: auto !important;
        transition-duration: .01ms !important;
    }
}
</style>

@section('content')
<div class="jw-auth-shell">
    <div class="jw-auth-inner">
        <!-- Master Shell Top Header with Dynamic In-Place Switcher -->
        <header class="jw-auth-top">
            <a href="https://jero.works/" class="jw-logo" aria-label="Jero.works">
                <img src="{{ asset('img/jero-logo-black-yellow.svg') }}" alt="Jero.works">
                <span>AI Recruitment Platform</span>
            </a>
            <div class="jw-account-cta">
                <span id="jw-top-cta-text">{{ $activeState === 'signup' ? 'Already have an account?' : 'New to Jero.works?' }}</span>
                <button type="button" class="jw-create-btn" id="jw-top-switch-btn" onclick="jwSwitchAuth('{{ $activeState === 'signup' ? 'login' : 'signup' }}')">
                    <span id="jw-top-btn-label">{{ $activeState === 'signup' ? 'Sign In' : 'Create Free Account' }}</span>
                    <svg id="jw-top-btn-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </header>

        <main class="jw-auth-main">
            <!-- LEFT SIDE: Immutable Master Ecosystem Design -->
            <section class="jw-hero" aria-label="Jero.works hiring ecosystem">
                <div class="jw-hero-copy">
                    <h1>One Platform.<br>One Ecosystem.<br><span class="jw-gradient-text">Limitless Opportunities.</span></h1>
                    <p>Jero.works connects Companies, Agencies and Bench Talents to build the future of hiring.</p>
                </div>

                <div class="jw-ecosystem">
                    <svg class="jw-eco-lines" viewBox="0 0 640 460" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
                        <defs>
                            <linearGradient id="jwLineBluePurple" x1="230" y1="90" x2="120" y2="350" gradientUnits="userSpaceOnUse"><stop stop-color="#2563eb"/><stop offset="1" stop-color="#7c3aed"/></linearGradient>
                            <linearGradient id="jwLineBlueGreen" x1="410" y1="90" x2="520" y2="350" gradientUnits="userSpaceOnUse"><stop stop-color="#2563eb"/><stop offset="1" stop-color="#16b364"/></linearGradient>
                            <linearGradient id="jwLineBenchAgency" x1="120" y1="350" x2="520" y2="350" gradientUnits="userSpaceOnUse"><stop stop-color="#7c3aed"/><stop offset=".52" stop-color="#2563eb"/><stop offset="1" stop-color="#16b364"/></linearGradient>
                            <marker id="jwArrowBluePurple" markerWidth="8" markerHeight="8" refX="4" refY="4" orient="auto" markerUnits="strokeWidth"><path d="M0 0L8 4L0 8Z" fill="#7c3aed"/></marker>
                            <marker id="jwArrowBlueGreen" markerWidth="8" markerHeight="8" refX="4" refY="4" orient="auto" markerUnits="strokeWidth"><path d="M0 0L8 4L0 8Z" fill="#16b364"/></marker>
                            <marker id="jwArrowMulti" markerWidth="8" markerHeight="8" refX="4" refY="4" orient="auto" markerUnits="strokeWidth"><path d="M0 0L8 4L0 8Z" fill="#16b364"/></marker>
                        </defs>
                        <ellipse class="jw-eco-orbit" cx="320" cy="254" rx="235" ry="195"/>
                        <path class="jw-eco-path" d="M248 128C190 186 146 260 122 329" stroke="url(#jwLineBluePurple)" marker-end="url(#jwArrowBluePurple)"/>
                        <path class="jw-eco-path" d="M392 128C450 187 494 260 518 329" stroke="url(#jwLineBlueGreen)" marker-end="url(#jwArrowBlueGreen)"/>
                        <path class="jw-eco-path" d="M146 352C252 416 390 416 494 352" stroke="url(#jwLineBenchAgency)" marker-end="url(#jwArrowMulti)"/>
                        <circle cx="248" cy="128" r="3.5" fill="#2563eb"/><circle cx="392" cy="128" r="3.5" fill="#2563eb"/><circle cx="146" cy="352" r="3.5" fill="#7c3aed"/>
                    </svg>
                    <div class="jw-eco-node top" style="--node-color:#2563eb;--node-glow:rgba(37,99,235,.16);">
                        <div class="jw-node-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 21V5a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v16" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 9h1a2 2 0 0 1 2 2v10M3 21h18M8 7h2M12 7h2M8 11h2M12 11h2M8 15h2M12 15h2" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                        <div class="jw-node-copy"><strong>For Companies</strong><span>Find the right talent,<br>faster and smarter.</span></div>
                    </div>
                    <div class="jw-eco-node left" style="--node-color:#7c3aed;--node-glow:rgba(124,58,237,.16);">
                        <div class="jw-node-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 20c0-3.3 2.7-6 6-6h1" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/><circle cx="10" cy="8" r="4" stroke="currentColor" stroke-width="1.9"/><path d="M17.8 13.1l1.05 2.12 2.34.34-1.7 1.66.4 2.33-2.09-1.1-2.09 1.1.4-2.33-1.7-1.66 2.35-.34 1.04-2.12Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg></div>
                        <div class="jw-node-copy"><strong>For Bench Talents</strong><span>Discover opportunities<br>that match your skills<br>and grow your career.</span></div>
                    </div>
                    <div class="jw-eco-node right" style="--node-color:#16b364;--node-glow:rgba(22,179,100,.16);">
                        <div class="jw-node-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M3.5 20c0-3.05 2.45-5.5 5.5-5.5h1.2" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/><circle cx="9" cy="8.2" r="3.7" stroke="currentColor" stroke-width="1.9"/><path d="M17.6 12.8l.96 1.95 2.15.31-1.55 1.51.36 2.13-1.92-1.01-1.92 1.01.37-2.13-1.55-1.51 2.14-.31.96-1.95Z" stroke="currentColor" stroke-width="1.55" stroke-linejoin="round"/><path d="M15 7h4M17 5v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div>
                        <div class="jw-node-copy"><strong>For Agencies</strong><span>Build stronger networks<br>and create more<br>placements.</span></div>
                    </div>
                    <div class="jw-hub"><div class="brand-mark" aria-hidden="true">☂</div><strong>Jero.works</strong><span>One Connected<br>Hiring Ecosystem</span><i aria-hidden="true"></i></div>
                </div>

                <div class="jw-metrics" aria-label="Platform highlights">
                    <div class="jw-metric" style="--metric-color:#7c3aed;--metric-bg:#f1e8ff;"><div class="jw-metric-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 20c0-3.3 2.7-6 6-6h1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="10" cy="8" r="3.5" stroke="currentColor" stroke-width="2"/><path d="M18 13l.9 1.8 2 .3-1.45 1.4.35 2-1.8-.95-1.8.95.35-2-1.45-1.4 2-.3L18 13Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></div><div><strong>25K+</strong><span>Active Jobs</span></div></div>
                    <div class="jw-metric" style="--metric-color:#16b364;--metric-bg:#e4f8ed;"><div class="jw-metric-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3.4" stroke="currentColor" stroke-width="2"/><path d="M3.5 20c0-3 2.5-5.5 5.5-5.5h1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M17 13l.9 1.8 2 .3-1.45 1.4.35 2-1.8-.95-1.8.95.35-2-1.45-1.4 2-.3L17 13Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg></div><div><strong>250K+</strong><span>Verified Talents</span></div></div>
                    <div class="jw-metric" style="--metric-color:#ff6b1a;--metric-bg:#fff0e7;"><div class="jw-metric-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 21V5a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v16M17 9h1a2 2 0 0 1 2 2v10M3 21h18M8 8h2M12 8h2M8 12h2M12 12h2M8 16h2M12 16h2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></div><div><strong>20K+</strong><span>Agencies</span></div></div>
                    <div class="jw-metric" style="--metric-color:#2563eb;--metric-bg:#e8f0ff;"><div class="jw-metric-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="currentColor" stroke-width="2"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div><div><strong>15K+</strong><span>Companies</span></div></div>
                </div>
                <div class="jw-privacy-note"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="currentColor" stroke-width="2"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Built with privacy by design and enterprise-grade security.</span></div>
            </section>

            <!-- RIGHT SIDE: Dual-State Authentication Shell -->
            <section class="jw-login-zone" aria-label="Authentication Card">
                
                <!-- STATE 1: LOGIN -->
                <form class="jw-card-form jw-fade-enter" novalidate id="kt_sign_in_form1" action="{{ route('login') }}" method="post" style="{{ $loginStyle }}">
                    @csrf
                    <h2>Welcome Back! 👋</h2>
                    <p class="sub">Sign in to continue to your Jero.works account</p>
                    
                    @if(@$class && @$messsage)<div class="jw-alert {{ in_array(strtolower(trim(@$class)), ['success', 'ok']) ? 'jw-alert-ok jw-alert-success' : '' }}"><strong>{{ rtrim(ucfirst(@$class), '!') }}!</strong> {!! @$messsage !!}</div>@endif
                    @if(session('success'))<div class="jw-alert jw-alert-ok jw-alert-success"><strong>Success!</strong> {{ session('success') }}</div>@endif
                    @if(session('error'))<div class="jw-alert"><strong>Error!</strong> {{ session('error') }}</div>@endif
                    @if(!session('error') && session('accountnotverify'))<div class="jw-alert">{{ session('accountnotverify') }}</div>@endif
                    @if($errors->has('device_message'))<div class="jw-alert">{{ $errors->first('device_message') }}</div>@endif

                    <div class="jw-field">
                        <label for="jw-email">Email Address</label>
                        <div class="jw-iw">
                            <span class="il" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 20 20" fill="none"><rect x="2.2" y="4.5" width="15.6" height="11" rx="2.2" stroke="currentColor" stroke-width="1.7"/><path d="M3 7.2l7 5 7-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            <input class="jw-inp" type="text" name="email" id="jw-email" placeholder="Enter your email" autocomplete="username" value="{{ old('email') }}" aria-describedby="jw-email-help"/>
                            <span class="ok-mark" id="jw-email-ok" style="display:none;" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8.4" fill="#16B364"/><path d="M5 9l3 3 5-5.5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            <input type="hidden" name="url" value="{{ @$url }}"/>
                        </div>
                        @if($errors->has('email'))<span class="jw-err" id="jw-email-help">{{ $errors->first('email') }}</span>@endif
                    </div>

                    <div class="jw-field">
                        <div class="jw-field-row"><label for="jw-pwd">Password</label><a href="javascript:void(0);" id="to-recover" class="jw-fgt">Forgot password?</a></div>
                        <div class="jw-iw">
                            <span class="il" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 20 20" fill="none"><rect x="3.3" y="8.7" width="13.4" height="8.2" rx="2.1" stroke="currentColor" stroke-width="1.7"/><path d="M6.6 8.7V6.4a3.4 3.4 0 0 1 6.8 0v2.3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="10" cy="12.9" r="1.25" fill="currentColor"/></svg></span>
                            <input class="jw-inp" type="password" name="password" id="jw-pwd" placeholder="Enter your password" autocomplete="current-password" aria-describedby="jw-pwd-help"/>
                            <span class="ir" onclick="jwEye()" role="button" tabindex="0" aria-label="Toggle password visibility" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();jwEye();}"><svg id="jw-eye" width="19" height="19" viewBox="0 0 20 20" fill="none"><path d="M2 10s3-5.6 8-5.6S18 10 18 10s-3 5.6-8 5.6S2 10 2 10Z" stroke="currentColor" stroke-width="1.7"/><circle cx="10" cy="10" r="2.4" stroke="currentColor" stroke-width="1.7"/></svg></span>
                        </div>
                        @if($errors->has('password'))<span class="jw-err" id="jw-pwd-help"><strong>{{ $errors->first('password') }}</strong></span>@endif
                    </div>

                    <div class="jw-sec">
                        <div class="jw-sec-head">
                            <span class="jw-sec-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="currentColor" stroke-width="1.8"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            <span><span class="jw-sec-title">Security verification</span><span class="jw-sec-bracket">Please verify that you are human</span></span>
                        </div>
                        <div class="jw-cap-row">
                            <div class="jw-cap-box"><img src="{{ route('captcha.generate') }}" id="captchaImg" alt="Security code" class="jw-cap-code-img"></div>
                            <button type="button" onclick="refreshCaptcha()" class="jw-cap-refresh-btn" aria-label="Refresh captcha" title="Refresh captcha"><svg viewBox="0 0 24 24" fill="none"><path d="M21 12a9 9 0 1 1-3.5-7.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M21 4v5h-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                            <div class="jw-cap-field"><input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="4" name="captcha" class="jw-cap-inp" id="jw-cap" placeholder="Enter the code shown" autocomplete="off" required aria-describedby="jw-cap-help"></div>
                        </div>
                        @if($errors->has('captcha'))<span class="jw-err" id="jw-cap-help">{{ $errors->first('captcha') }}</span>@endif
                    </div>

                    <button type="submit" id="kt_sign_in_submit" class="jw-btn">
                        <span class="indicator-label">Sign In</span>
                        <span class="arrow" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 20 20" fill="none"><path d="M3 10h13M11 5l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        <span class="indicator-progress" style="display:none" aria-live="polite">Signing in...&nbsp;<span class="spinner-border spinner-border-sm align-middle"></span></span>
                    </button>

                    <div class="jw-switch-row">
                        <span>Don't have an account?</span>
                        <a href="javascript:void(0);" onclick="jwSwitchAuth('signup')" class="jw-link-switch">Create Free Account →</a>
                    </div>

                    <div class="jw-card-divider" aria-hidden="true"><span><svg viewBox="0 0 24 24" fill="none"><path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="currentColor" stroke-width="1.8"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span></div>
                    <div class="jw-card-trust"><div class="jw-trust-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="currentColor" stroke-width="2"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div><div><strong>Your security comes first</strong><span>Your credentials are encrypted and<br>never shared with employers or agencies.</span></div></div>
                </form>

                <!-- STATE 2: SIGNUP (Account Creation) -->
                <form class="jw-card-form jw-fade-enter" novalidate id="kt_sign_up_form" action="{{ route('company-register') }}" method="post" style="{{ $signupStyle }}">
                    @csrf
                    <!-- Honeypot anti-bot security: humans never touch hidden fields -->
                    <input type="text" name="website_url" style="display:none;" tabindex="-1" autocomplete="off">

                    <h2>Create your Jero.works account 🚀</h2>
                    <p class="sub">Get started in 30 seconds. No credit card required.</p>

                    <!-- Dynamic Error Alert Box -->
                    <div class="jw-alert jw-alert-danger" id="jw_signup_alert" style="display:none;" role="alert"></div>

                    <!-- Corporate Domain Collision Card -->
                    <div id="jw_domain_collision_card" class="jw-collision-box" style="display:none;" role="alert">
                        <div class="jw-collision-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" width="22" height="22">
                                <path d="M12 9v4M12 17h.01M5.07 19h13.86a2 2 0 0 0 1.74-2.98L13.74 4.5a2 2 0 0 0-3.48 0L3.33 16.02A2 2 0 0 0 5.07 19Z" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="jw-collision-content">
                            <strong id="jw_collision_title">This company already has a Jero.works workspace</strong>
                            <p id="jw_collision_msg">An administrator or team member from your company domain is already registered. If you want to join this workspace, please ask your company administrator to invite you.</p>
                            <div class="jw-collision-actions">
                                <button type="button" class="jw-btn-mini-primary" onclick="jwSwitchToLoginWithEmail()">Sign In Instead →</button>
                                <a href="mailto:support@jeroworks.com?subject=Request%20Access%20to%20Company%20Workspace" class="jw-btn-mini-link" id="jw_collision_request_btn">Request Access</a>
                            </div>
                        </div>
                    </div>

                    <!-- Field 1: Full Name -->
                    <div class="jw-field">
                        <label for="jw-signup-name">Full Name</label>
                        <div class="jw-iw">
                            <span class="il" aria-hidden="true">
                                <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </span>
                            <input class="jw-inp" type="text" name="name" id="jw-signup-name" placeholder="e.g. Sarah Jenkins" autocomplete="name" required/>
                        </div>
                        <span class="jw-err" id="jw-signup-name-err" style="display:none;"></span>
                    </div>

                    <!-- Field 2: Work Email with Live Corporate Domain Validation -->
                    <div class="jw-field">
                        <div class="jw-field-row">
                            <label for="jw-signup-email">Work Email</label>
                            <span class="jw-domain-tag" id="jw-domain-badge" style="display:none;"></span>
                        </div>
                        <div class="jw-iw">
                            <span class="il" aria-hidden="true">
                                <svg width="19" height="19" viewBox="0 0 20 20" fill="none">
                                    <rect x="2.2" y="4.5" width="15.6" height="11" rx="2.2" stroke="currentColor" stroke-width="1.7"/>
                                    <path d="M3 7.2l7 5 7-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <input class="jw-inp" type="email" name="email" id="jw-signup-email" placeholder="name@company.com" autocomplete="email" required/>
                            <span class="ok-mark" id="jw-signup-email-ok" style="display:none;" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                    <circle cx="9" cy="9" r="8.4" fill="#16B364"/>
                                    <path d="M5 9l3 3 5-5.5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="ir" id="jw-signup-email-spinner" style="display:none;" aria-hidden="true">
                                <span class="spinner-border spinner-border-sm text-primary"></span>
                            </span>
                        </div>
                        <span class="jw-err" id="jw-signup-email-err" style="display:none;"></span>
                    </div>

                    <!-- Field 3: Global Phone Number with Dynamic Calling Code Selector -->
                    <div class="jw-field">
                        <x-jero-phone-input
                            id="signup_phone"
                            name="contact_number"
                            countryCodeName="contact_code"
                            countryCodeId="signup_contact_code"
                            label="Phone Number"
                            placeholder="Enter phone number"
                            :required="true"
                        />
                        <span class="jw-err" id="jw-signup-phone-err" style="display:none;"></span>
                    </div>

                    <!-- Field 4: Company Type -->
                    <div class="jw-field">
                        <label for="jw-signup-company-type">Company Type</label>
                        <div class="jw-iw">
                            <span class="il" aria-hidden="true">
                                <svg width="19" height="19" viewBox="0 0 24 24" fill="none">
                                    <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <select class="jw-inp jw-select" name="company_type" id="jw-signup-company-type" required>
                                <option value="" disabled selected>Select company business model...</option>
                                <option value="2">Consulting or Services Company</option>
                                <option value="1">Recruitment or Staffing Agency</option>
                                <option value="3">Product or Manufacturing Company (Direct Employer)</option>
                                <option value="4">Freelancer / Individual Recruiter</option>
                            </select>
                            <span class="ir" style="pointer-events:none;" aria-hidden="true">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                    <path d="M6 9l6 6 6-6" stroke="#8b95af" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                        <span class="jw-err" id="jw-signup-company-type-err" style="display:none;"></span>
                    </div>

                    <!-- Field 5: Password with ZXCVBN Strength Meter -->
                    <div class="jw-field">
                        <label for="jw-signup-pwd">Password</label>
                        <div class="jw-iw">
                            <span class="il" aria-hidden="true">
                                <svg width="19" height="19" viewBox="0 0 20 20" fill="none">
                                    <rect x="3.3" y="8.7" width="13.4" height="8.2" rx="2.1" stroke="currentColor" stroke-width="1.7"/>
                                    <path d="M6.6 8.7V6.4a3.4 3.4 0 0 1 6.8 0v2.3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                    <circle cx="10" cy="12.9" r="1.25" fill="currentColor"/>
                                </svg>
                            </span>
                            <input class="jw-inp" type="password" name="password" id="jw-signup-pwd" placeholder="At least 8 characters" autocomplete="new-password" required/>
                            <span class="ir" onclick="jwEyeSignup()" role="button" tabindex="0" aria-label="Toggle password visibility">
                                <svg id="jw-signup-eye" width="19" height="19" viewBox="0 0 20 20" fill="none">
                                    <path d="M2 10s3-5.6 8-5.6S18 10 18 10s-3 5.6-8 5.6S2 10 2 10Z" stroke="currentColor" stroke-width="1.7"/>
                                    <circle cx="10" cy="10" r="2.4" stroke="currentColor" stroke-width="1.7"/>
                                </svg>
                            </span>
                        </div>
                        <div class="jw-pwd-strength-bar" id="jw_pwd_strength_bar">
                            <div class="jw-pwd-seg" id="jw_seg_1"></div>
                            <div class="jw-pwd-seg" id="jw_seg_2"></div>
                            <div class="jw-pwd-seg" id="jw_seg_3"></div>
                            <div class="jw-pwd-seg" id="jw_seg_4"></div>
                        </div>
                        <div class="jw-pwd-hint" id="jw_pwd_hint">Use 8 or more characters with letters & numbers</div>
                        <span class="jw-err" id="jw-signup-pwd-err" style="display:none;"></span>
                    </div>

                    <!-- Continue Action Button -->
                    <button type="submit" id="kt_sign_up_submit" class="jw-btn">
                        <span class="indicator-label">Continue</span>
                        <span class="arrow" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 20 20" fill="none">
                                <path d="M3 10h13M11 5l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="indicator-progress" style="display:none" aria-live="polite">
                            Creating Account...&nbsp;<span class="spinner-border spinner-border-sm align-middle"></span>
                        </span>
                    </button>

                    <!-- In-Place Switch to Login -->
                    <div class="jw-switch-row">
                        <span>Already have an account?</span>
                        <a href="javascript:void(0);" onclick="jwSwitchAuth('login')" class="jw-link-switch">Sign In →</a>
                    </div>

                    <div class="jw-card-divider" aria-hidden="true"><span><svg viewBox="0 0 24 24" fill="none"><path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="currentColor" stroke-width="1.8"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span></div>
                    <div class="jw-card-trust"><div class="jw-trust-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3l8 4v5c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4Z" stroke="currentColor" stroke-width="2"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div><div><strong>Enterprise Grade Architecture</strong><span>One administrator per custom company domain.<br>100% data isolation & SOC2 compliant.</span></div></div>
                </form>

                <!-- STATE 3: POST-SIGNUP VERIFICATION CONFIRMATION -->
                <div class="jw-card-form jw-fade-enter" id="jw_signup_success_card" style="display:none;">
                    <div style="text-align:center; padding: 12px 0 16px;">
                        <div style="width: 72px; height: 72px; margin: 0 auto 16px; border-radius: 50%; background: #e8f8f0; display: flex; align-items: center; justify-content: center; color: #16b364;">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <h2 style="font-size: 26px; font-weight: 800; color: #0b1f44; margin-bottom: 8px;">Check your inbox! ✉️</h2>
                        <p class="sub" style="margin-bottom: 24px; color: #475467;">We sent a verification link to <strong id="jw_success_email" style="color:#0b1f44;"></strong>. Click the link in your email to verify your address and launch your workspace.</p>
                        
                        <button type="button" onclick="jwSwitchAuth('login')" class="jw-btn" style="text-decoration:none;">
                            <span>Sign In to Your Account</span>
                            <span class="arrow"><svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M3 10h13M11 5l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        </button>
                        
                        <div style="margin-top: 18px; font-size: 12px; color: #64748b;">
                            Didn't receive the email? Check your spam folder or contact support.
                        </div>
                    </div>
                </div>

                <!-- STATE 4: FORGOT PASSWORD -->
                <form class="jw-card-form jw-fade-enter" novalidate id="recoverform" action="{{ route('password.emailsend') }}" method="post" style="{{ $recoverStyle }}">
                    {{ csrf_field() }}
                    <h2>Forgot Password?</h2>
                    <p class="sub">Enter your email to reset your password.</p>
                    @if(session('status'))<div class="jw-alert jw-alert-ok">{{ session('status') }}</div>@endif
                    @if(session('accountnotverify'))<div class="jw-alert">{{ session('accountnotverify') }}</div>@endif
                    <div class="jw-field {{ $errors->has('email') ? 'has-error' : '' }}">
                        <label for="recover-email">Email Address</label>
                        <div class="jw-iw">
                            <span class="il" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 20 20" fill="none"><rect x="2.2" y="4.5" width="15.6" height="11" rx="2.2" stroke="currentColor" stroke-width="1.7"/><path d="M3 7.2l7 5 7-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            <input type="email" name="email" id="recover-email" class="jw-inp {{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="Enter your email" value="{{ old('email') }}" required autocomplete="email">
                        </div>
                        @if($errors->has('email'))<span class="jw-err"><strong>{{ $errors->first('email') }}</strong></span>@endif
                    </div>
                    <div class="jw-fgt-acts">
                        <button type="submit" id="reset_password" class="jw-fgt-sub"><span class="indicator-label">Reset Password</span><span class="indicator-progress" style="display:none">Please wait...&nbsp;<span class="spinner-border spinner-border-sm align-middle"></span></span></button>
                        <button type="button" onclick="jwSwitchAuth('login')" class="jw-fgt-cancel">Cancel</button>
                    </div>
                </form>

            </section>
        </main>

        <footer class="jw-footer">
            <span class="jw-footer-copy">© 2026 Jero Technologies Pvt. Ltd. All rights reserved.</span>
            <span class="jw-footer-secure"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="6" y="10" width="12" height="10" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8.5 10V7.5a3.5 3.5 0 0 1 7 0V10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Your security is our priority</span>
            <span class="jw-footer-links">
                <a href="/privacy-policy">Privacy Policy</a>
                <a href="/terms">Terms of Service</a>
                <a href="/support">Support</a>
                <button type="button" class="jw-locale" aria-label="Language selector"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18" stroke="currentColor" stroke-width="1.6"/></svg>English<svg width="10" height="10" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M3 5l3 3 3-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
            </span>
        </footer>
    </div>
</div>
@endsection

@push('scripts')
<script>
// State management & In-Place switching between Login, Signup, Recover
function jwSwitchAuth(mode) {
    var loginForm   = document.getElementById('kt_sign_in_form1');
    var signupForm  = document.getElementById('kt_sign_up_form');
    var recoverForm = document.getElementById('recoverform');
    var successCard = document.getElementById('jw_signup_success_card');

    var topCtaText  = document.getElementById('jw-top-cta-text');
    var topBtnLabel = document.getElementById('jw-top-btn-label');
    var topSwitchBtn= document.getElementById('jw-top-switch-btn');
    var pageTitle   = document.getElementById('jw-page-title');

    // Hide all
    if (loginForm)   loginForm.style.display = 'none';
    if (signupForm)  signupForm.style.display = 'none';
    if (recoverForm) recoverForm.style.display = 'none';
    if (successCard) successCard.style.display = 'none';

    if (mode === 'signup') {
        if (signupForm) {
            signupForm.style.display = 'block';
            signupForm.classList.remove('jw-fade-enter');
            void signupForm.offsetWidth; // trigger reflow
            signupForm.classList.add('jw-fade-enter');
        }
        if (topCtaText)  topCtaText.textContent = 'Already have an account?';
        if (topBtnLabel) topBtnLabel.textContent = 'Sign In';
        if (topSwitchBtn) topSwitchBtn.setAttribute('onclick', "jwSwitchAuth('login')");
        if (pageTitle)   pageTitle.textContent = 'Create your Jero.works account 🚀';
        try { history.pushState({ authMode: 'signup' }, '', '{{ route("register") }}'); } catch(e) {}
    } else if (mode === 'recover') {
        if (recoverForm) {
            recoverForm.style.display = 'block';
            recoverForm.classList.remove('jw-fade-enter');
            void recoverForm.offsetWidth;
            recoverForm.classList.add('jw-fade-enter');
        }
        if (topCtaText)  topCtaText.textContent = 'Remember password?';
        if (topBtnLabel) topBtnLabel.textContent = 'Sign In';
        if (topSwitchBtn) topSwitchBtn.setAttribute('onclick', "jwSwitchAuth('login')");
        if (pageTitle)   pageTitle.textContent = 'Forgot Password - jero.works';
    } else {
        // default: login
        if (loginForm) {
            loginForm.style.display = 'block';
            loginForm.classList.remove('jw-fade-enter');
            void loginForm.offsetWidth;
            loginForm.classList.add('jw-fade-enter');
        }
        if (topCtaText)  topCtaText.textContent = 'New to Jero.works?';
        if (topBtnLabel) topBtnLabel.textContent = 'Create Free Account';
        if (topSwitchBtn) topSwitchBtn.setAttribute('onclick', "jwSwitchAuth('signup')");
        if (pageTitle)   pageTitle.textContent = 'Sign In - jero.works';
        try { history.pushState({ authMode: 'login' }, '', '{{ route("login") }}'); } catch(e) {}
    }
}

// Browser navigation sync
window.addEventListener('popstate', function (e) {
    if (e.state && e.state.authMode) {
        jwSwitchAuth(e.state.authMode);
    } else if (window.location.pathname.indexOf('register') !== -1) {
        jwSwitchAuth('signup');
    } else {
        jwSwitchAuth('login');
    }
});

// Switch from domain collision card to login with prefilled email
function jwSwitchToLoginWithEmail() {
    var signupEmail = document.getElementById('jw-signup-email');
    var loginEmail  = document.getElementById('jw-email');
    if (signupEmail && loginEmail && signupEmail.value) {
        loginEmail.value = signupEmail.value.trim();
        var emailOk = document.getElementById('jw-email-ok');
        if (emailOk) emailOk.style.display = 'flex';
        loginEmail.classList.add('is-ok');
    }
    jwSwitchAuth('login');
    var pwd = document.getElementById('jw-pwd');
    if (pwd) pwd.focus();
}

function refreshCaptcha() {
    var img = document.getElementById('captchaImg');
    if (img) img.src = "{{ route('captcha.generate') }}?t=" + Date.now();
    var c = document.getElementById('jw-cap');
    if (c) { c.value = ''; c.classList.remove('is-ok'); c.focus(); }
}

function jwEye() {
    var i = document.getElementById('jw-pwd'), e = document.getElementById('jw-eye');
    if (!i || !e) return;
    if (i.type === 'password') {
        i.type = 'text';
        e.innerHTML = '<path d="M2 10s3-5.6 8-5.6S18 10 18 10s-3 5.6-8 5.6S2 10 2 10Z" stroke="currentColor" stroke-width="1.7"/><circle cx="10" cy="10" r="2.4" stroke="currentColor" stroke-width="1.7"/><line x1="2.5" y1="2.5" x2="17.5" y2="17.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>';
        e.setAttribute('aria-label', 'Hide password');
    } else {
        i.type = 'password';
        e.innerHTML = '<path d="M2 10s3-5.6 8-5.6S18 10 18 10s-3 5.6-8 5.6S2 10 2 10Z" stroke="currentColor" stroke-width="1.7"/><circle cx="10" cy="10" r="2.4" stroke="currentColor" stroke-width="1.7"/>';
        e.setAttribute('aria-label', 'Show password');
    }
}

function jwEyeSignup() {
    var i = document.getElementById('jw-signup-pwd'), e = document.getElementById('jw-signup-eye');
    if (!i || !e) return;
    if (i.type === 'password') {
        i.type = 'text';
        e.innerHTML = '<path d="M2 10s3-5.6 8-5.6S18 10 18 10s-3 5.6-8 5.6S2 10 2 10Z" stroke="currentColor" stroke-width="1.7"/><circle cx="10" cy="10" r="2.4" stroke="currentColor" stroke-width="1.7"/><line x1="2.5" y1="2.5" x2="17.5" y2="17.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>';
        e.setAttribute('aria-label', 'Hide password');
    } else {
        i.type = 'password';
        e.innerHTML = '<path d="M2 10s3-5.6 8-5.6S18 10 18 10s-3 5.6-8 5.6S2 10 2 10Z" stroke="currentColor" stroke-width="1.7"/><circle cx="10" cy="10" r="2.4" stroke="currentColor" stroke-width="1.7"/>';
        e.setAttribute('aria-label', 'Show password');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Forgot password toggle on login
    var toRecover = document.getElementById('to-recover');
    if (toRecover) {
        toRecover.addEventListener('click', function(e) {
            e.preventDefault();
            jwSwitchAuth('recover');
        });
    }

    // Login email format check
    var loginEmail = document.getElementById('jw-email');
    var loginEmailOk = document.getElementById('jw-email-ok');
    var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    function checkLoginEmail() {
        if (!loginEmail || !loginEmailOk) return;
        var ok = emailRe.test(loginEmail.value.trim());
        loginEmailOk.style.display = ok ? 'flex' : 'none';
        loginEmail.classList.toggle('is-ok', ok);
    }
    if (loginEmail) { checkLoginEmail(); loginEmail.addEventListener('input', checkLoginEmail); }

    // Captcha validation check
    var cap = document.getElementById('jw-cap');
    function checkCap() {
        if (!cap) return;
        var ok = cap.value.trim().length >= 4;
        cap.classList.toggle('is-ok', ok);
    }
    if (cap) { checkCap(); cap.addEventListener('input', checkCap); }

    // Login form submission handler
    var loginBtn = document.getElementById('kt_sign_in_submit');
    var loginForm = document.getElementById('kt_sign_in_form1');
    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function () {
            var label = loginBtn.querySelector('.indicator-label');
            var prog  = loginBtn.querySelector('.indicator-progress');
            var arrow = loginBtn.querySelector('.arrow');
            if (label) label.style.display = 'none';
            if (arrow) arrow.style.display = 'none';
            if (prog)  prog.style.display = 'inline-flex';
            loginBtn.setAttribute('disabled', 'disabled');
        });
    }

    // -------------------------------------------------------------
    // SIGNUP WORKFLOW: Corporate Domain Check, Password Meter, Submission
    // -------------------------------------------------------------
    var signupEmail   = document.getElementById('jw-signup-email');
    var signupEmailOk = document.getElementById('jw-signup-email-ok');
    var signupSpinner = document.getElementById('jw-signup-email-spinner');
    var domainBadge   = document.getElementById('jw-domain-badge');
    var collisionCard = document.getElementById('jw-domain-collision_card');
    var collisionTitle= document.getElementById('jw_collision_title');
    var collisionMsg  = document.getElementById('jw_collision_msg');
    var collisionReq  = document.getElementById('jw_collision_request_btn');

    var domainCheckTimer = null;
    var lastCheckedDomain = '';

    function probeCorporateDomain() {
        if (!signupEmail) return;
        var val = signupEmail.value.trim().toLowerCase();
        if (!emailRe.test(val)) {
            if (domainBadge) domainBadge.style.display = 'none';
            if (signupEmailOk) signupEmailOk.style.display = 'none';
            if (collisionCard) collisionCard.style.display = 'none';
            signupEmail.classList.remove('is-ok', 'is-invalid');
            return;
        }

        var domainPart = val.split('@')[1] || '';
        if (domainPart === lastCheckedDomain) return;

        if (signupSpinner) signupSpinner.style.display = 'flex';
        if (signupEmailOk) signupEmailOk.style.display = 'none';

        var formData = new FormData();
        formData.append('email', val);
        formData.append('_token', '{{ csrf_token() }}');

        fetch("{{ route('domain-company-register-chk') }}", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (signupSpinner) signupSpinner.style.display = 'none';
            lastCheckedDomain = domainPart;

            if (data.status === 'domain_collision' || data.allowed === false) {
                // Collision detected: corporate domain already taken
                if (collisionCard) collisionCard.style.display = 'flex';
                if (collisionTitle && data.title) collisionTitle.textContent = data.title;
                if (collisionMsg && data.message) collisionMsg.textContent = data.message;
                if (collisionReq) {
                    collisionReq.href = "mailto:" + (data.support_email || 'support@jeroworks.com') + "?subject=Request%20Access%20to%20" + encodeURIComponent(domainPart) + "%20Workspace";
                }
                if (domainBadge) {
                    domainBadge.textContent = 'Workspace Exists';
                    domainBadge.className = 'jw-domain-tag exists';
                    domainBadge.style.display = 'inline-flex';
                }
                signupEmail.classList.remove('is-ok');
                signupEmail.classList.add('is-invalid');
            } else {
                // Domain available or free webmail
                if (collisionCard) collisionCard.style.display = 'none';
                if (signupEmailOk) signupEmailOk.style.display = 'flex';
                signupEmail.classList.remove('is-invalid');
                signupEmail.classList.add('is-ok');

                if (domainBadge) {
                    if (data.is_free_webmail) {
                        domainBadge.textContent = 'Personal Webmail';
                        domainBadge.className = 'jw-domain-tag webmail';
                    } else {
                        domainBadge.textContent = 'Available Workspace ✓';
                        domainBadge.className = 'jw-domain-tag corporate';
                    }
                    domainBadge.style.display = 'inline-flex';
                }
            }
        })
        .catch(function() {
            if (signupSpinner) signupSpinner.style.display = 'none';
        });
    }

    if (signupEmail) {
        signupEmail.addEventListener('input', function() {
            clearTimeout(domainCheckTimer);
            domainCheckTimer = setTimeout(probeCorporateDomain, 450);
        });
        signupEmail.addEventListener('blur', probeCorporateDomain);
    }

    // Password strength evaluator (User RAM execution)
    var signupPwd = document.getElementById('jw-signup-pwd');
    var seg1 = document.getElementById('jw_seg_1');
    var seg2 = document.getElementById('jw_seg_2');
    var seg3 = document.getElementById('jw_seg_3');
    var seg4 = document.getElementById('jw_seg_4');
    var pwdHint = document.getElementById('jw_pwd_hint');

    function evalPasswordStrength() {
        if (!signupPwd) return;
        var p = signupPwd.value;
        var score = 0;
        if (p.length >= 8) score++;
        if (p.length >= 12) score++;
        if (/[A-Z]/.test(p) && /[a-z]/.test(p)) score++;
        if (/[0-9]/.test(p) || /[^A-Za-z0-9]/.test(p)) score++;

        [seg1, seg2, seg3, seg4].forEach(function(s) { if (s) s.className = 'jw-pwd-seg'; });

        if (p.length === 0) {
            if (pwdHint) pwdHint.textContent = 'Use 8 or more characters with letters & numbers';
            return;
        }

        if (score === 1) {
            if (seg1) seg1.classList.add('weak');
            if (pwdHint) pwdHint.textContent = 'Too weak (minimum 8 characters)';
        } else if (score === 2) {
            if (seg1) seg1.classList.add('fair');
            if (seg2) seg2.classList.add('fair');
            if (pwdHint) pwdHint.textContent = 'Fair password — add uppercase or numbers';
        } else if (score === 3) {
            if (seg1) seg1.classList.add('good');
            if (seg2) seg2.classList.add('good');
            if (seg3) seg3.classList.add('good');
            if (pwdHint) pwdHint.textContent = 'Good password';
        } else if (score >= 4) {
            if (seg1) seg1.classList.add('strong');
            if (seg2) seg2.classList.add('strong');
            if (seg3) seg3.classList.add('strong');
            if (seg4) seg4.classList.add('strong');
            if (pwdHint) pwdHint.textContent = 'Strong enterprise-grade password';
        }
    }
    if (signupPwd) signupPwd.addEventListener('input', evalPasswordStrength);

    var typeSelect = document.getElementById('jw-signup-company-type');
    if (typeSelect) {
        typeSelect.addEventListener('change', function() {
            typeSelect.classList.remove('is-invalid');
            var alertEl = document.getElementById('jw_signup_alert');
            if (alertEl) alertEl.style.display = 'none';
        });
    }

    // Signup form AJAX submit (2-Step Flow: Step 1 Account Creation)
    var signupForm = document.getElementById('kt_sign_up_form');
    var signupBtn  = document.getElementById('kt_sign_up_submit');
    var signupAlert= document.getElementById('jw_signup_alert');

    function resetSignupBtn() {
        if (!signupBtn) return;
        signupBtn.removeAttribute('disabled');
        var label = signupBtn.querySelector('.indicator-label');
        var prog  = signupBtn.querySelector('.indicator-progress');
        var arrow = signupBtn.querySelector('.arrow');
        if (label) label.style.display = 'inline';
        if (arrow) arrow.style.display = 'inline-flex';
        if (prog)  prog.style.display = 'none';
    }

    if (signupForm && signupBtn) {
        signupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (signupAlert) { signupAlert.style.display = 'none'; signupAlert.textContent = ''; }

            var nameInput  = document.getElementById('jw-signup-name');
            var emailInput = document.getElementById('jw-signup-email');
            var typeInput  = document.getElementById('jw-signup-company-type');
            var pwdInput   = document.getElementById('jw-signup-pwd');

            if (!nameInput || !nameInput.value.trim()) {
                if (signupAlert) { signupAlert.textContent = 'Please enter your full name.'; signupAlert.style.display = 'block'; }
                if (nameInput) nameInput.focus();
                return;
            }
            if (!emailInput || !emailRe.test(emailInput.value.trim())) {
                if (signupAlert) { signupAlert.textContent = 'Please enter a valid work email address.'; signupAlert.style.display = 'block'; }
                if (emailInput) emailInput.focus();
                return;
            }
            if (!typeInput || !typeInput.value) {
                if (signupAlert) { signupAlert.textContent = 'Please select your company type.'; signupAlert.style.display = 'block'; }
                if (typeInput) { typeInput.focus(); typeInput.classList.add('is-invalid'); }
                return;
            }
            if (!pwdInput || pwdInput.value.length < 8) {
                if (signupAlert) { signupAlert.textContent = 'Password must be at least 8 characters long.'; signupAlert.style.display = 'block'; }
                if (pwdInput) pwdInput.focus();
                return;
            }

            // Spinner state
            var label = signupBtn.querySelector('.indicator-label');
            var prog  = signupBtn.querySelector('.indicator-progress');
            var arrow = signupBtn.querySelector('.arrow');
            if (label) label.style.display = 'none';
            if (arrow) arrow.style.display = 'none';
            if (prog)  prog.style.display = 'inline-flex';
            signupBtn.setAttribute('disabled', 'disabled');

            var formData = new FormData(signupForm);

            fetch("{{ route('company-register') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async function(res) {
                var data = await res.json().catch(function() { return {}; });
                if (res.ok && data.status === 'success') {
                    // Account creation success! Show verification confirmation
                    signupForm.style.display = 'none';
                    var successCard  = document.getElementById('jw_signup_success_card');
                    var successEmail = document.getElementById('jw_success_email');
                    if (successEmail) successEmail.textContent = data.email || emailInput.value.trim();
                    if (successCard) {
                        successCard.style.display = 'block';
                        successCard.classList.remove('jw-fade-enter');
                        void successCard.offsetWidth;
                        successCard.classList.add('jw-fade-enter');
                    }
                    var topCtaText  = document.getElementById('jw-top-cta-text');
                    var topBtnLabel = document.getElementById('jw-top-btn-label');
                    var topSwitchBtn= document.getElementById('jw-top-switch-btn');
                    if (topCtaText)   topCtaText.textContent = 'Already verified?';
                    if (topBtnLabel)  topBtnLabel.textContent = 'Sign In';
                    if (topSwitchBtn) topSwitchBtn.setAttribute('onclick', "jwSwitchAuth('login')");
                } else if (res.status === 422 && data.status === 'domain_collision') {
                    if (collisionCard) {
                        collisionCard.style.display = 'flex';
                        if (collisionTitle && data.title) collisionTitle.textContent = data.title;
                        if (collisionMsg && data.message) collisionMsg.textContent = data.message;
                    }
                    resetSignupBtn();
                } else if (res.status === 422 && data.validation) {
                    var keys = Object.keys(data.validation);
                    var firstMsg = data.validation[keys[0]][0];
                    if (signupAlert) {
                        signupAlert.textContent = firstMsg;
                        signupAlert.style.display = 'block';
                    }
                    resetSignupBtn();
                } else {
                    if (signupAlert) {
                        signupAlert.textContent = data.message || 'Unable to complete registration. Please try again.';
                        signupAlert.style.display = 'block';
                    }
                    resetSignupBtn();
                }
            })
            .catch(function() {
                if (signupAlert) {
                    signupAlert.textContent = 'A network error occurred. Please check your connection and retry.';
                    signupAlert.style.display = 'block';
                }
                resetSignupBtn();
            });
        });
    }
});
</script>
@endpush
