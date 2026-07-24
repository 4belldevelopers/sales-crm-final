@extends('layouts.metronic_front_app')
<title>Jero Works — AI Recruitment Platform | Sign In</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
:root {
  --c-blue:#2563eb;--c-blue-dark:#1d4ed8;--c-blue-light:#eff6ff;--c-blue-ring:rgba(37,99,235,.12);
  --c-orange:#f97316;--c-green:#22c55e;--c-indigo:#4f46e5;--c-purple:#7c3aed;
  --c-900:#0f172a;--c-800:#1e293b;--c-700:#334155;--c-600:#475569;--c-500:#64748b;
  --c-400:#94a3b8;--c-300:#cbd5e1;--c-200:#e2e8f0;--c-100:#f1f5f9;--c-50:#f8fafc;--c-white:#ffffff;
  --t:all .22s cubic-bezier(.4,0,.2,1);
  --font:'Inter','Poppins',-apple-system,sans-serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
#kt_body,body{background:#f0f4ff!important;min-height:100vh;font-family:var(--font)}
.d-flex.flex-column.flex-root{min-height:100vh}
.d-flex.flex-column.flex-lg-row.flex-column-fluid{min-height:100vh;align-items:stretch}
.jwp{display:flex;flex-direction:column;min-height:100vh;width:100%;font-family:var(--font);background:var(--c-white)}
.jwp-body{display:flex;flex:1 1 auto;min-height:0}
.jwp-topbar{display:flex;align-items:center;justify-content:space-between;padding:0 44px;height:72px;flex-shrink:0;position:relative;z-index:10}
.jwp-logo{display:inline-flex;align-items:center;gap:11px;text-decoration:none}
.jwp-logo-icon{width:42px;height:42px;background:var(--c-900);border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 14px rgba(15,23,42,.25)}
.jwp-logo-icon svg{width:25px;height:25px}
.jwp-logo-text{display:flex;flex-direction:column;line-height:1}
.jwp-logo-name{font-size:19px;font-weight:800;color:var(--c-900);letter-spacing:-.4px;font-family:var(--font)}
.jwp-logo-sub{font-size:10px;font-weight:500;color:var(--c-500);margin-top:2px;font-family:var(--font)}
.jwp-reg{display:flex;align-items:center;gap:12px}
.jwp-reg-hint{font-size:13px;font-weight:500;color:var(--c-500);font-family:var(--font)}
.jwp-reg-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border:1.5px solid var(--c-blue);border-radius:9px;font-size:13px;font-weight:600;color:var(--c-blue);background:transparent;text-decoration:none;font-family:var(--font);transition:var(--t);white-space:nowrap}
.jwp-reg-btn:hover{background:var(--c-blue);color:var(--c-white);box-shadow:0 4px 14px rgba(37,99,235,.3);transform:translateY(-1px)}
.jwp-reg-btn svg{width:13px;height:13px;transition:transform .2s ease}
.jwp-reg-btn:hover svg{transform:translateX(3px)}
.jwp-left{flex:1 1 0;min-width:0;background:var(--c-white);display:flex;flex-direction:column;padding:0 52px;position:relative;overflow:hidden}
.jwp-left::before{content:'';position:absolute;top:0;right:0;width:260px;height:240px;background-image:radial-gradient(circle,#c8d5e3 1.5px,transparent 1.5px);background-size:22px 22px;opacity:.45;pointer-events:none;z-index:0}
.jwp-left::after{content:'';position:absolute;bottom:-100px;left:-100px;width:360px;height:360px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,.04) 0%,transparent 65%);pointer-events:none;z-index:0}
.jwp-left-inner{position:relative;z-index:1;flex:1;display:flex;flex-direction:column;justify-content:center;padding:40px 0 36px}
.jwp-hero{margin-bottom:28px;animation:aFadeUp .6s .05s both}
.jwp-hero h1{font-size:clamp(36px,4vw,54px);font-weight:900;color:var(--c-900);line-height:1.04;letter-spacing:-2.2px;margin-bottom:16px;font-family:var(--font)}
.jwp-hero h1 .hl-orange{color:var(--c-orange)}
.jwp-hero p{font-size:14px;color:var(--c-500);line-height:1.7;max-width:420px;font-family:var(--font)}
.jwp-cards{display:flex;gap:16px;margin-bottom:28px;animation:aFadeUp .6s .14s both}
.jwp-fc{flex:1 1 0;min-width:0;background:var(--c-white);border:1px solid var(--c-200);border-radius:18px;padding:18px 16px 0;display:flex;flex-direction:column;box-shadow:0 2px 12px rgba(15,23,42,.07),0 1px 4px rgba(15,23,42,.04);overflow:hidden;cursor:default;transition:transform .28s cubic-bezier(.4,0,.2,1),box-shadow .28s ease}
.jwp-fc:hover{transform:translateY(-6px);box-shadow:0 20px 60px rgba(15,23,42,.13),0 6px 20px rgba(15,23,42,.07)}
.jwp-fc-chip{display:inline-flex;align-items:center;gap:4px;font-size:8.5px;font-weight:800;letter-spacing:1px;text-transform:uppercase;border-radius:100px;padding:3px 10px;width:fit-content;margin-bottom:12px;font-family:var(--font)}
.chip-b{background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe}
.chip-g{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0}
.chip-p{background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe}
.jwp-fc h3{font-size:14.5px;font-weight:800;color:var(--c-900);line-height:1.25;margin-bottom:8px;font-family:var(--font)}
.jwp-fc p{font-size:12px;color:var(--c-500);line-height:1.6;flex:1;font-family:var(--font)}
.jwp-fc-art{height:130px;margin-top:10px;display:flex;align-items:flex-end;justify-content:center;position:relative;overflow:hidden}
.jwp-fc-art svg{width:100%;height:100%}
.jwp-stats{display:flex;gap:32px;animation:aFadeUp .6s .22s both}
.jwp-stat{display:flex;align-items:center;gap:10px}
.jwp-stat-ico{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.sic-b{background:#eff6ff;color:#2563eb}.sic-g{background:#f0fdf4;color:#16a34a}
.sic-p{background:#f5f3ff;color:#7c3aed}.sic-o{background:#fff7ed;color:#ea580c}
.jwp-stat-ico svg{width:18px;height:18px}
.jwp-stat-num{font-size:18px;font-weight:800;color:var(--c-900);line-height:1;font-family:var(--font);letter-spacing:-.5px}
.jwp-stat-lbl{font-size:10px;color:var(--c-400);font-weight:500;margin-top:2px;font-family:var(--font)}
.jwp-right{width:500px;flex-shrink:0;display:flex;align-items:center;justify-content:center;padding:32px 28px;position:relative}
.jwp-right::before{content:'';position:absolute;top:-80px;right:-80px;width:340px;height:340px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,.06) 0%,transparent 65%);pointer-events:none}
.jwp-right::after{content:'';position:absolute;bottom:-60px;left:-60px;width:280px;height:280px;border-radius:50%;background:radial-gradient(circle,rgba(249,115,22,.05) 0%,transparent 65%);pointer-events:none}
.jwf{background:var(--c-white);border-radius:24px;box-shadow:0 2px 8px rgba(15,23,42,.06),0 8px 40px rgba(15,23,42,.10),0 1px 3px rgba(15,23,42,.04);padding:40px 36px 32px;width:100%;max-width:460px;position:relative;animation:aFadeUp .55s .08s both}
.jwf-head{text-align:center;margin-bottom:28px}
.jwf-head h2{font-size:24px;font-weight:800;color:var(--c-900);margin-bottom:7px;letter-spacing:-.4px;font-family:var(--font)}
.jwf-head p{font-size:13px;color:var(--c-500);line-height:1.5;font-family:var(--font)}
.jfg{margin-bottom:18px}
.jfg-label{display:flex;align-items:center;justify-content:space-between;font-size:13px;font-weight:600;color:var(--c-800);margin-bottom:8px;font-family:var(--font)}
.jfg-forgot{font-size:13px;font-weight:600;color:var(--c-blue);text-decoration:none;cursor:pointer;transition:color .2s}
.jfg-forgot:hover{color:var(--c-blue-dark);text-decoration:underline}
.jfg-wrap{position:relative;display:flex;align-items:center}
.jfg-icon-l{position:absolute;left:15px;color:var(--c-400);display:flex;align-items:center;pointer-events:none;transition:color .2s;z-index:1}
.jfg-icon-l svg{width:17px;height:17px}
.jfg-icon-r{position:absolute;right:13px;display:flex;align-items:center;z-index:1}
.jfg-input{width:100%;height:52px;padding:0 48px;border:1.5px solid var(--c-200);border-radius:12px;font-size:14px;font-family:var(--font);color:var(--c-900);background:#fafbfc;transition:var(--t);outline:none}
.jfg-input::placeholder{color:#bdc8d8}
.jfg-input:hover{border-color:var(--c-300)}
.jfg-input:focus{border-color:var(--c-blue);background:var(--c-white);box-shadow:0 0 0 4px var(--c-blue-ring)}
.jfg-wrap:focus-within .jfg-icon-l{color:var(--c-blue)}
.jfg-chk{width:24px;height:24px;border-radius:50%;background:var(--c-green);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(34,197,94,.3);flex-shrink:0}
.jfg-chk svg{width:12px;height:12px;stroke:#fff;stroke-width:3;fill:none}
.jfg-eye{background:none;border:none;cursor:pointer;padding:5px;color:var(--c-400);display:flex;align-items:center;border-radius:7px;transition:color .2s,background .2s}
.jfg-eye:hover{color:var(--c-600);background:var(--c-100)}
.jfg-eye svg{width:18px;height:18px}
.jfg-rem{display:flex;align-items:center;gap:9px;margin-bottom:18px}
.jfg-rem input[type="checkbox"]{width:17px;height:17px;accent-color:var(--c-blue);cursor:pointer;flex-shrink:0}
.jfg-rem label{font-size:13px;color:var(--c-700);font-weight:500;cursor:pointer;font-family:var(--font)}
.jwf-cap-lbl{font-size:13px;font-weight:600;color:var(--c-800);margin-bottom:10px;font-family:var(--font)}
.jwf-cap-row{display:flex;align-items:center;gap:10px;margin-bottom:16px}
.jwf-cap-img-wrap{height:50px;min-width:110px;border:1.5px solid var(--c-200);border-radius:10px;background:var(--c-white);flex-shrink:0;display:flex;align-items:center;justify-content:center;overflow:hidden}
.jwf-cap-img-wrap img{height:100%;width:100%;object-fit:contain;display:block}
.jwf-cap-refresh{height:50px;padding:0 13px;border:none;background:none;font-size:13px;font-weight:600;color:var(--c-blue);font-family:var(--font);cursor:pointer;display:flex;align-items:center;gap:5px;flex-shrink:0;border-radius:10px;transition:var(--t);white-space:nowrap}
.jwf-cap-refresh:hover{background:var(--c-blue-light)}
.jwf-cap-refresh svg{width:14px;height:14px;transition:transform .45s ease}
.jwf-cap-refresh:hover svg{transform:rotate(200deg)}
.jwf-cap-field{flex:1;position:relative;display:flex;align-items:center}
.jwf-cap-input{width:100%;height:50px;padding:0 44px 0 14px;border:1.5px solid var(--c-200);border-radius:10px;font-size:14px;font-family:var(--font);color:var(--c-900);background:#fafbfc;transition:var(--t);outline:none}
.jwf-cap-input::placeholder{color:#bdc8d8}
.jwf-cap-input:focus{border-color:var(--c-blue);background:var(--c-white);box-shadow:0 0 0 3px var(--c-blue-ring)}
.jwf-cap-chk{position:absolute;right:11px}
.jwf-btn{width:100%;height:56px;background:linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);color:var(--c-white);border:none;border-radius:12px;font-size:16px;font-weight:700;font-family:var(--font);cursor:pointer;display:flex;align-items:center;justify-content:center;gap:10px;margin:18px 0 24px;box-shadow:0 4px 20px rgba(37,99,235,.4),0 1px 4px rgba(37,99,235,.2);transition:transform .22s ease,box-shadow .22s ease;position:relative;overflow:hidden;letter-spacing:.2px}
.jwf-btn::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.15) 0%,transparent 60%);opacity:0;transition:opacity .25s}
.jwf-btn:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(37,99,235,.48),0 2px 10px rgba(37,99,235,.25)}
.jwf-btn:hover::before{opacity:1}
.jwf-btn:active{transform:translateY(0)}
.jwf-btn svg{width:20px;height:20px;transition:transform .22s ease}
.jwf-btn:hover svg{transform:translateX(5px)}
.jwf-security{display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:8px;border-top:1px solid var(--c-100);padding-top:20px}
.jwf-sec-item{display:flex;flex-direction:column;align-items:center;gap:5px;padding:10px 5px;background:var(--c-50);border:1px solid #eef2f7;border-radius:12px;transition:var(--t);cursor:default}
.jwf-sec-item:hover{background:var(--c-white);border-color:#bfdbfe;box-shadow:0 3px 12px rgba(37,99,235,.1);transform:translateY(-2px)}
.jwf-sec-ico{width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center}
.sec-g{background:#f0fdf4;color:#16a34a}.sec-b{background:#eff6ff;color:#2563eb}
.sec-i{background:#eef2ff;color:#4f46e5}.sec-v{background:#f5f3ff;color:#7c3aed}
.jwf-sec-ico svg{width:14px;height:14px}
.jwf-sec-name{font-size:9.5px;font-weight:700;color:var(--c-700);text-align:center;line-height:1.3;font-family:var(--font)}
.jwf-sec-sub{font-size:8.5px;font-weight:500;color:var(--c-400);text-align:center;line-height:1.2;font-family:var(--font)}
.jw-alert{padding:11px 14px;border-radius:11px;font-size:13px;margin-bottom:16px;line-height:1.55;font-family:var(--font)}
.jw-alert-err{background:#fef2f2;border:1px solid #fecaca;color:#dc2626}
.jw-alert-ok{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d}
.jw-field-err{font-size:11.5px;color:#dc2626;margin-top:5px;display:block;font-family:var(--font)}
.jwp-footer{display:flex;align-items:center;justify-content:space-between;padding:16px 44px;background:var(--c-white);border-top:1px solid rgba(203,213,225,.4);flex-shrink:0;z-index:5}
.jwp-footer-copy{font-size:12px;color:var(--c-400);font-family:var(--font);font-weight:400}
.jwp-footer-links{display:flex;align-items:center;gap:24px}
.jwp-footer-links a{font-size:12.5px;font-weight:500;color:var(--c-500);text-decoration:none;font-family:var(--font);transition:color .2s}
.jwp-footer-links a:hover{color:var(--c-blue)}
.jwp-lang{display:inline-flex;align-items:center;gap:5px;font-size:12.5px;font-weight:500;color:var(--c-500);border:1px solid var(--c-200);border-radius:8px;padding:5px 12px;cursor:pointer;font-family:var(--font);transition:var(--t)}
.jwp-lang:hover{border-color:var(--c-300);color:var(--c-700)}
.jwp-lang svg{width:14px;height:14px}
@keyframes aFadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
.ani-1{animation:aFadeUp .6s .06s both}.ani-2{animation:aFadeUp .6s .12s both}
.ani-3{animation:aFadeUp .6s .18s both}.ani-4{animation:aFadeUp .6s .24s both}
@keyframes aSpin{to{transform:rotate(360deg)}}
@media(max-width:1100px){.jwp-left{padding:0 36px}.jwp-right{width:440px;padding:28px 22px}.jwp-topbar{padding:0 36px}.jwp-footer{padding:14px 36px}.jwp-stats{gap:20px}}
@media(max-width:880px){.jwp-body{flex-direction:column}.jwp-right{width:100%;padding:28px 24px}.jwp-left{padding:0 28px}.jwp-topbar{padding:0 28px}.jwp-footer{padding:14px 28px;flex-direction:column;gap:10px;text-align:center}.jwp-footer-links{flex-wrap:wrap;justify-content:center;gap:16px}.jwp-reg-hint{display:none}}
@media(max-width:640px){.jwp-left{padding:0 18px}.jwp-topbar{padding:0 18px;height:60px}.jwp-footer{padding:12px 18px}.jwp-hero h1{font-size:32px;letter-spacing:-1.2px}.jwp-cards{overflow-x:auto;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;padding-bottom:6px}.jwp-cards::-webkit-scrollbar{display:none}.jwp-fc{min-width:200px;scroll-snap-align:start}.jwp-stats{flex-wrap:wrap;gap:16px}.jwf{padding:28px 20px 24px;border-radius:18px}.jwf-security{grid-template-columns:1fr 1fr}}
</style>

@section('content')
<?php
if(session('status')){ $resetForm=''; $loginForm='display:none'; }
else { $resetForm='display:none'; $loginForm=''; }
?>

<div class="jwp">
  <header class="jwp-topbar" role="banner">
    <a href="https://jero.works" class="jwp-logo" aria-label="Jero Works home">
      <div class="jwp-logo-icon" aria-hidden="true">
        <svg viewBox="0 0 28 28" fill="none">
          <path d="M14 3C7.93 3 3 7.93 3 14h22C25 7.93 20.07 3 14 3z" fill="#fff"/>
          <path d="M3 14c0 1.7 1.35 3.1 3.1 3.1S9.2 15.7 9.2 14" stroke="#fff" stroke-width="1.7" stroke-linecap="round" fill="none"/>
          <path d="M9.2 14c0 1.7 1.35 3.1 3.1 3.1S15.4 15.7 15.4 14" stroke="#fff" stroke-width="1.7" stroke-linecap="round" fill="none"/>
          <line x1="14" y1="14" x2="14" y2="23" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
          <path d="M11 23c0 1.65 1.35 3 3 3s3-1.35 3-3" stroke="#fff" stroke-width="1.7" stroke-linecap="round" fill="none"/>
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
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </a>
    </div>
  </header>

  <div class="jwp-body">
    <main class="jwp-left" role="main">
      <div class="jwp-left-inner">

        <div class="jwp-hero ani-1">
          <h1>One Platform.<br>Every <span class="hl-orange">Hiring Need.</span></h1>
          <p>Connect companies, agencies and talent on a<br>modern recruitment platform built for speed and trust.</p>
        </div>

        <div class="jwp-cards ani-2" role="list">

          <div class="jwp-fc" role="listitem" tabindex="0">
            <span class="jwp-fc-chip chip-b">FOR COMPANIES</span>
            <h3>Find Trusted<br>Agencies</h3>
            <p>Connect with verified recruitment agencies and hire faster.</p>
            <div class="jwp-fc-art" aria-hidden="true">
              <svg viewBox="0 0 170 140" fill="none">
                <circle cx="85" cy="80" r="62" fill="url(#c1g)"/>
                <defs><radialGradient id="c1g" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="#dbeafe"/><stop offset="100%" stop-color="#bfdbfe"/></radialGradient></defs>
                <circle cx="148" cy="28" r="18" fill="#fff" opacity=".95"/>
                <circle cx="141" cy="24" r="4" fill="#dbeafe" stroke="#3b82f6" stroke-width="1.4"/>
                <circle cx="155" cy="24" r="4" fill="#dbeafe" stroke="#3b82f6" stroke-width="1.4"/>
                <path d="M133 38v-2a7 7 0 0 1 7-7h4a7 7 0 0 1 7 7v2" stroke="#2563eb" stroke-width="1.6" stroke-linecap="round" fill="none"/>
                <rect x="32" y="100" width="106" height="5" rx="2.5" fill="#1e293b"/>
                <rect x="36" y="78" width="98" height="24" rx="3" fill="#1e293b"/>
                <rect x="38" y="80" width="94" height="20" rx="2" fill="#0f172a"/>
                <rect x="40" y="82" width="90" height="16" rx="1.5" fill="#1e293b"/>
                <rect x="44" y="85" width="30" height="2.5" rx="1.2" fill="#60a5fa"/>
                <rect x="44" y="90" width="20" height="1.5" rx=".75" fill="#475569"/>
                <rect x="44" y="94" width="25" height="1.5" rx=".75" fill="#475569"/>
                <rect x="80" y="84" width="22" height="10" rx="2" fill="#2563eb" opacity=".7"/>
                <circle cx="85" cy="49" r="14" fill="#fbbf24"/>
                <path d="M71 45c0-7.7 6.3-14 14-14s14 6.3 14 14" fill="#1c1917"/>
                <rect x="71" y="45" width="5" height="7" rx="2.5" fill="#1c1917"/>
                <rect x="93" y="45" width="5" height="7" rx="2.5" fill="#1c1917"/>
                <rect x="76" y="50" width="6" height="4.5" rx="2.2" fill="none" stroke="#1c1917" stroke-width="1.2"/>
                <rect x="85" y="50" width="6" height="4.5" rx="2.2" fill="none" stroke="#1c1917" stroke-width="1.2"/>
                <line x1="82" y1="52" x2="85" y2="52" stroke="#1c1917" stroke-width="1"/>
                <line x1="71" y1="51" x2="76" y2="51" stroke="#1c1917" stroke-width="1"/>
                <line x1="91" y1="51" x2="96" y2="51" stroke="#1c1917" stroke-width="1"/>
                <rect x="68" y="62" width="34" height="18" rx="9" fill="#1e40af"/>
                <rect x="79" y="62" width="12" height="12" rx="2" fill="#f8fafc"/>
                <polygon points="85,62 82.5,72 85,76 87.5,72" fill="#ef4444"/>
                <rect x="51" y="64" width="18" height="8" rx="4" fill="#1e40af"/>
                <circle cx="54" cy="72" r="5.5" fill="#fbbf24"/>
                <rect x="101" y="64" width="18" height="8" rx="4" fill="#1e40af"/>
                <circle cx="116" cy="72" r="5.5" fill="#fbbf24"/>
                <rect x="73" y="78" width="10" height="24" rx="5" fill="#1e3a8a"/>
                <rect x="87" y="78" width="10" height="24" rx="5" fill="#1e3a8a"/>
                <rect x="71" y="99" width="14" height="5" rx="2.5" fill="#0f172a"/>
                <rect x="85" y="99" width="14" height="5" rx="2.5" fill="#0f172a"/>
              </svg>
            </div>
          </div>

          <div class="jwp-fc" role="listitem" tabindex="0">
            <span class="jwp-fc-chip chip-g">FOR AGENCIES</span>
            <h3>Search Work<br>Opportunities</h3>
            <p>Discover verified job opportunities from leading companies.</p>
            <div class="jwp-fc-art" aria-hidden="true">
              <svg viewBox="0 0 170 140" fill="none">
                <circle cx="85" cy="80" r="62" fill="url(#c2g)"/>
                <defs><radialGradient id="c2g" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="#dcfce7"/><stop offset="100%" stop-color="#bbf7d0"/></radialGradient></defs>
                <circle cx="148" cy="28" r="18" fill="#fff" opacity=".95"/>
                <rect x="138" y="24" width="20" height="14" rx="3" fill="#dcfce7" stroke="#16a34a" stroke-width="1.6"/>
                <path d="M143 24v-2.5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2V24" stroke="#16a34a" stroke-width="1.6" stroke-linecap="round" fill="none"/>
                <line x1="138" y1="31" x2="158" y2="31" stroke="#16a34a" stroke-width=".9" opacity=".5"/>
                <rect x="147" y="29" width="4" height="4" rx="1" fill="#16a34a"/>
                <rect x="32" y="100" width="106" height="5" rx="2.5" fill="#1e293b"/>
                <rect x="36" y="78" width="98" height="24" rx="3" fill="#1e293b"/>
                <rect x="38" y="80" width="94" height="20" rx="2" fill="#0f172a"/>
                <rect x="40" y="82" width="90" height="16" rx="1.5" fill="#1e293b"/>
                <rect x="44" y="85" width="30" height="2.5" rx="1.2" fill="#4ade80"/>
                <rect x="44" y="90" width="20" height="1.5" rx=".75" fill="#475569"/>
                <rect x="44" y="94" width="25" height="1.5" rx=".75" fill="#475569"/>
                <rect x="80" y="84" width="22" height="10" rx="2" fill="#16a34a" opacity=".7"/>
                <circle cx="85" cy="47" r="14" fill="#fbbf24"/>
                <path d="M71 43c0-7.7 6.3-14 14-14s14 6.3 14 14" fill="#1c1917"/>
                <rect x="71" y="43" width="5" height="22" rx="2.5" fill="#1c1917"/>
                <rect x="93" y="43" width="5" height="22" rx="2.5" fill="#1c1917"/>
                <ellipse cx="85" cy="39" rx="14" ry="7" fill="#1c1917"/>
                <rect x="68" y="60" width="34" height="20" rx="9" fill="#15803d"/>
                <rect x="79" y="60" width="12" height="12" rx="2" fill="#dcfce7"/>
                <rect x="51" y="62" width="18" height="8" rx="4" fill="#15803d"/>
                <circle cx="54" cy="70" r="5.5" fill="#fbbf24"/>
                <rect x="101" y="62" width="18" height="8" rx="4" fill="#15803d"/>
                <circle cx="116" cy="70" r="5.5" fill="#fbbf24"/>
                <rect x="73" y="78" width="10" height="24" rx="5" fill="#14532d"/>
                <rect x="87" y="78" width="10" height="24" rx="5" fill="#14532d"/>
                <rect x="71" y="99" width="14" height="5" rx="2.5" fill="#0f172a"/>
                <rect x="85" y="99" width="14" height="5" rx="2.5" fill="#0f172a"/>
              </svg>
            </div>
          </div>

          <div class="jwp-fc" role="listitem" tabindex="0">
            <span class="jwp-fc-chip chip-p">FOR TALENT</span>
            <h3>Hire Immediate<br>Joiners</h3>
            <p>Explore bench talent ready to join at the earliest.</p>
            <div class="jwp-fc-art" aria-hidden="true">
              <svg viewBox="0 0 170 140" fill="none">
                <circle cx="85" cy="80" r="62" fill="url(#c3g)"/>
                <defs><radialGradient id="c3g" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="#ede9fe"/><stop offset="100%" stop-color="#ddd6fe"/></radialGradient></defs>
                <circle cx="148" cy="28" r="18" fill="#fff" opacity=".95"/>
                <polygon points="151,14 140,32 149,32 146,44 158,26 149,26" fill="#7c3aed"/>
                <rect x="108" y="62" width="16" height="28" rx="4" fill="#1e293b"/>
                <rect x="110" y="64" width="12" height="22" rx="2" fill="#2d1b69"/>
                <rect x="111" y="66" width="10" height="15" rx="1.5" fill="#7c3aed" opacity=".45"/>
                <rect x="112" y="68" width="6" height="2" rx="1" fill="#c4b5fd" opacity=".9"/>
                <rect x="112" y="72" width="4" height="1.5" rx=".75" fill="#94a3b8" opacity=".6"/>
                <rect x="62" y="60" width="50" height="40" rx="10" fill="#7c3aed"/>
                <ellipse cx="87" cy="61" rx="16" ry="5" fill="#6d28d9"/>
                <line x1="80" y1="64" x2="77" y2="78" stroke="#5b21b6" stroke-width="1.4" stroke-linecap="round"/>
                <line x1="94" y1="64" x2="97" y2="78" stroke="#5b21b6" stroke-width="1.4" stroke-linecap="round"/>
                <rect x="72" y="78" width="30" height="18" rx="5" fill="#5b21b6"/>
                <polygon points="90,64 86,74 90,74 88,82 95,71 91,71" fill="#fbbf24" opacity=".95"/>
                <rect x="46" y="62" width="18" height="8" rx="4" fill="#7c3aed"/>
                <circle cx="49" cy="70" r="5.5" fill="#fbbf24"/>
                <rect x="110" y="62" width="14" height="8" rx="4" fill="#7c3aed"/>
                <circle cx="87" cy="45" r="14" fill="#fbbf24"/>
                <path d="M73 41c0-7.7 6.3-14 14-14s14 6.3 14 14" fill="#1c1917"/>
                <rect x="73" y="41" width="5" height="6" rx="2.5" fill="#1c1917"/>
                <rect x="95" y="41" width="5" height="6" rx="2.5" fill="#1c1917"/>
                <ellipse cx="87" cy="38" rx="16" ry="5" fill="#1c1917"/>
                <rect x="95" y="32" width="14" height="4" rx="2" fill="#1c1917"/>
                <rect x="73" y="98" width="11" height="22" rx="5.5" fill="#4c1d95"/>
                <rect x="90" y="98" width="11" height="22" rx="5.5" fill="#4c1d95"/>
                <rect x="71" y="117" width="15" height="5" rx="2.5" fill="#1c1917"/>
                <rect x="88" y="117" width="15" height="5" rx="2.5" fill="#1c1917"/>
              </svg>
            </div>
          </div>

        </div>

        <div class="jwp-stats ani-3" role="list">
          <div class="jwp-stat" role="listitem"><div class="jwp-stat-ico sic-b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><div><div class="jwp-stat-num" data-count="150" data-suffix="+">0</div><div class="jwp-stat-lbl">Enterprise Clients</div></div></div>
          <div class="jwp-stat" role="listitem"><div class="jwp-stat-ico sic-g"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg></div><div><div class="jwp-stat-num" data-count="25" data-suffix=",000+">0</div><div class="jwp-stat-lbl">Active Jobs</div></div></div>
          <div class="jwp-stat" role="listitem"><div class="jwp-stat-ico sic-p"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div><div><div class="jwp-stat-num" data-count="250" data-suffix=",000+">0</div><div class="jwp-stat-lbl">Candidate Profiles</div></div></div>
          <div class="jwp-stat" role="listitem"><div class="jwp-stat-ico sic-o"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><div><div class="jwp-stat-num" data-count="99" data-suffix=".9%">0</div><div class="jwp-stat-lbl">Platform Uptime</div></div></div>
        </div>

      </div>
    </main>

    <aside class="jwp-right" role="complementary">

      <div class="jwf" id="jw-login-card" style="{{ $loginForm }}">
        <div class="jwf-head">
          <h2>Welcome Back!</h2>
          <p>Sign in to continue to your jero.works account</p>
        </div>
        @if(session('accountnotverify'))<div class="jw-alert jw-alert-err" role="alert">{{ session('accountnotverify') }}</div>@endif
        @if(@$class && @$messsage)<div class="jw-alert jw-alert-{{ $class=='success' ? 'ok' : 'err' }}" role="alert">{!! @$messsage !!}</div>@endif
        <form novalidate id="kt_sign_in_form1" action="{{ route('login') }}" method="post">
          @csrf
          <input type="hidden" name="url" value="{{ @$url }}">
          <div class="jfg">
            <div class="jfg-label"><label for="jw-email">Email Address</label></div>
            <div class="jfg-wrap">
              <span class="jfg-icon-l"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
              <input class="jfg-input" type="text" name="email" id="jw-email" placeholder="you@company.com" autocomplete="email" value="{{ old('email') }}" aria-required="true">
              <span class="jfg-icon-r" id="jw-email-chk" style="display:none"><div class="jfg-chk"><svg viewBox="0 0 24 24" fill="none"><polyline points="20 6 9 17 4 12" stroke="white" stroke-width="3" stroke-linecap="round"/></svg></div></span>
            </div>
            @if($errors->has('email'))<span class="jw-field-err" role="alert">{{ $errors->first('email') }}</span>@endif
          </div>
          <div class="jfg">
            <div class="jfg-label">
              <label for="jw-pwd">Password</label>
              <a href="javascript:void(0);" id="to-recover" class="jfg-forgot">Forgot Password?</a>
            </div>
            <div class="jfg-wrap">
              <span class="jfg-icon-l"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
              <input class="jfg-input" type="password" id="jw-pwd" name="password" placeholder="••••••••••••" autocomplete="current-password" aria-required="true">
              <span class="jfg-icon-r">
                <button type="button" class="jfg-eye" id="jw-eye-btn" onclick="jwTogglePwd()" aria-label="Toggle password visibility" aria-pressed="false">
                  <svg id="jw-eye-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  <svg id="jw-eye-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
              </span>
            </div>
            @if($errors->has('password'))<span class="jw-field-err" role="alert">{{ $errors->first('password') }}</span>@endif
          </div>
          @if($errors->has('device_message'))<div class="jw-alert jw-alert-err" role="alert">{{ $errors->first('device_message') }}</div>@endif
          <div class="jfg-rem">
            <input type="checkbox" id="jw-rem" name="remember" checked>
            <label for="jw-rem">Remember me</label>
          </div>
          <div class="jwf-cap-lbl">Enter the number in the box</div>
          <div class="jwf-cap-row">
            <div class="jwf-cap-img-wrap"><img src="{{ route('captcha.generate') }}" id="captchaImg" alt="CAPTCHA code"></div>
            <button type="button" onclick="jwRefreshCaptcha()" class="jwf-cap-refresh" id="jw-refresh-captcha-btn">
              <svg id="jw-refresh-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
              Refresh
            </button>
            <div class="jwf-cap-field">
              <input type="text" name="captcha" id="jw-captcha-input" class="jwf-cap-input" placeholder="Enter code" autocomplete="off" required inputmode="numeric">
              <span class="jwf-cap-chk" id="jw-cap-chk" style="display:none"><div class="jfg-chk"><svg viewBox="0 0 24 24" fill="none"><polyline points="20 6 9 17 4 12" stroke="white" stroke-width="3" stroke-linecap="round"/></svg></div></span>
            </div>
          </div>
          @if($errors->has('captcha'))<span class="jw-field-err" style="margin-bottom:10px;display:block;" role="alert">{{ $errors->first('captcha') }}</span>@endif
          <button type="submit" id="kt_sign_in_submit" class="jwf-btn">Sign In <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></button>
        </form>
        <div class="jwf-security" role="list">
          <div class="jwf-sec-item" role="listitem"><div class="jwf-sec-ico sec-g"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div><div class="jwf-sec-name">SSL Secured</div><div class="jwf-sec-sub">256-bit encryption</div></div>
          <div class="jwf-sec-item" role="listitem"><div class="jwf-sec-ico sec-b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div><div class="jwf-sec-name">99.9% Uptime</div><div class="jwf-sec-sub">Reliable Platform</div></div>
          <div class="jwf-sec-item" role="listitem"><div class="jwf-sec-ico sec-i"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg></div><div class="jwf-sec-name">SOC 2 Ready</div><div class="jwf-sec-sub">Enterprise Grade</div></div>
          <div class="jwf-sec-item" role="listitem"><div class="jwf-sec-ico sec-v"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="11" x2="12" y2="15"/><circle cx="12" cy="8" r=".8" fill="currentColor"/></svg></div><div class="jwf-sec-name">GDPR Compliant</div><div class="jwf-sec-sub">Data Protection</div></div>
        </div>
      </div>

      <div class="jwf" id="jw-recover-card" style="{{ $resetForm }}">
        <div class="jwf-head"><h2>Reset Password</h2><p>Enter your email to receive a password reset link.</p></div>
        @if(session('status'))<div class="jw-alert jw-alert-ok" role="alert">{{ session('status') }}</div>@endif
        @if(session('accountnotverify'))<div class="jw-alert jw-alert-err" role="alert">{{ session('accountnotverify') }}</div>@endif
        <form novalidate id="recoverform" action="{{ route('password.emailsend') }}" method="post">
          {{ csrf_field() }}
          <div class="jfg">
            <div class="jfg-label"><label for="jw-recover-email">Email Address</label></div>
            <div class="jfg-wrap">
              <span class="jfg-icon-l"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
              <input type="email" name="email" id="jw-recover-email" class="jfg-input" placeholder="you@company.com" value="{{ old('email') }}" required autocomplete="email">
            </div>
            @if($errors->has('email'))<span class="jw-field-err" role="alert">{{ $errors->first('email') }}</span>@endif
          </div>
          <button type="submit" id="reset_password" class="jwf-btn">Send Reset Link <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></button>
          <div style="text-align:center;margin-top:-8px;"><a href="{{ route('login') }}" style="font-size:13px;color:#64748b;text-decoration:none;font-weight:500;">← Back to Login</a></div>
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
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        English
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:11px;height:11px"><polyline points="6 9 12 15 18 9"/></svg>
      </div>
    </div>
  </footer>
</div>

<script>
function jwTogglePwd(){var f=document.getElementById('jw-pwd'),b=document.getElementById('jw-eye-btn'),s=document.getElementById('jw-eye-show'),h=document.getElementById('jw-eye-hide'),p=f.type==='password';f.type=p?'text':'password';s.style.display=p?'none':'';h.style.display=p?'':'none';b.setAttribute('aria-pressed',p?'true':'false')}
function jwRefreshCaptcha(){var img=document.getElementById('captchaImg'),ico=document.getElementById('jw-refresh-ico');img.src="{{ route('captcha.generate') }}?t="+Date.now();ico.style.transition='transform .45s ease';ico.style.transform='rotate(200deg)';setTimeout(function(){ico.style.transform='rotate(0deg)';ico.style.transition='transform .3s ease'},500);var ci=document.getElementById('jw-captcha-input'),ck=document.getElementById('jw-cap-chk');if(ci)ci.value='';if(ck)ck.style.display='none'}
function jwCountUp(el){var t=parseInt(el.dataset.count,10),s=el.dataset.suffix||'',d=1800,st=performance.now();function step(n){var p=Math.min((n-st)/d,1),e=1-Math.pow(1-p,3);el.textContent=Math.floor(e*t)+(p>=1?s:'');if(p<1)requestAnimationFrame(step)}requestAnimationFrame(step)}
$(document).ready(function(){
  $('#to-recover').on('click',function(){$('#kt_sign_in_form1').closest('.jwf').fadeOut(200,function(){$('#recoverform').closest('.jwf').fadeIn(260)})});
  var se=document.querySelector('.jwp-stats');
  if(se&&typeof IntersectionObserver!=='undefined'){var o=new IntersectionObserver(function(e){e.forEach(function(x){if(x.isIntersecting){x.target.querySelectorAll('[data-count]').forEach(jwCountUp);o.unobserve(x.target)}})},{threshold:0.3});o.observe(se)}
  else if(se){se.querySelectorAll('[data-count]').forEach(jwCountUp)}
  var ei=document.getElementById('jw-email'),ec=document.getElementById('jw-email-chk');
  if(ei&&ec){function ce(){ec.style.display=/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(ei.value.trim())?'':'none'}ei.addEventListener('input',ce);ce()}
  var ci=document.getElementById('jw-captcha-input'),cc=document.getElementById('jw-cap-chk');
  if(ci&&cc){ci.addEventListener('input',function(){cc.style.display=ci.value.trim().length>=2?'':'none'})}
  $('#kt_sign_in_form1').on('submit',function(){var b=document.getElementById('kt_sign_in_submit');if(b){b.disabled=true;b.innerHTML='<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="animation:aSpin .7s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>&nbsp;Signing In...';b.style.opacity='.85'}});
});
</script>
@endsection
