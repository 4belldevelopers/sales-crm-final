<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $headCS = $careerSettings ?? null;
        $headFont = !empty($headCS->font_family) ? $headCS->font_family : 'Poppins';
        $seoMetaTitle = !empty($headCS->seo_meta_title) ? $headCS->seo_meta_title : null;
        $seoMetaDesc  = !empty($headCS->seo_meta_desc) ? $headCS->seo_meta_desc : null;
        $seoKeywords  = !empty($headCS->seo_keywords) ? $headCS->seo_keywords : null;
        $seoRobots    = !empty($headCS->seo_robots) ? $headCS->seo_robots : 'index,follow';
        $seoCanonical = !empty($headCS->seo_canonical) ? $headCS->seo_canonical : null;
        $advGa4Id     = !empty($headCS->adv_ga4_id) ? $headCS->adv_ga4_id : null;
        $advFbPixel   = !empty($headCS->adv_fb_pixel) ? $headCS->adv_fb_pixel : null;
    @endphp
    <title>{{ $seoMetaTitle ?: ((@$account->company_name ?? 'Company') . ' – Careers & Job Openings') }}</title>
    @if($seoMetaDesc)<meta name="description" content="{{ $seoMetaDesc }}">@endif
    @if($seoKeywords)<meta name="keywords" content="{{ $seoKeywords }}">@endif
    <meta name="robots" content="{{ $seoRobots }}">
    @if($seoCanonical)<link rel="canonical" href="{{ $seoCanonical }}">@endif
    <link rel="shortcut icon" type="image/x-icon" href="{{ !empty($headCS->td_favicon_path) ? asset($headCS->td_favicon_path) : asset('img/favicon.svg') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" />
    @if(!empty($headFont) && $headFont !== 'Poppins')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family={{ urlencode($headFont) }}:wght@300;400;500;600;700;800;900&display=swap" />
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="{{ asset('demo/plugins/global/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet">
    <link href="{{ asset('demo/css/style.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet">
    @if($advGa4Id)
    <!-- Google Analytics 4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $advGa4Id }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $advGa4Id }}');</script>
    @endif
    @if($advFbPixel)
    <!-- Facebook Pixel -->
    <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{{ $advFbPixel }}');fbq('track','PageView');</script>
    @endif
</head>

@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode      = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $job_status      = GeneralMethod::job_status();

    /* ── Template Design settings ── */
    $cS = $careerSettings ?? null;
    $cpP        = !empty($cS->td_color_primary) ? $cS->td_color_primary : (!empty($cS->primary_color) ? $cS->primary_color : '#2563EB');
    $cpS        = !empty($cS->td_color_secondary) ? $cS->td_color_secondary : (!empty($cS->secondary_color) ? $cS->secondary_color : '#1F2937');
    $cpA        = !empty($cS->td_color_accent) ? $cS->td_color_accent : '#10B981';
    $cpBg       = !empty($cS->td_color_bg) ? $cS->td_color_bg : '#FFFFFF';
    $cpFont     = !empty($cS->font_family) ? $cS->font_family : 'Poppins';
    $cpTheme    = $cS->td_theme_mode      ?? 'light';
    $cpTemplate = $cS->td_template        ?? 'modern';
    $cpLayout   = $cS->td_layout          ?? 'wide';
    $cpJobsDisplay = !empty($cS->js_display_style) ? $cS->js_display_style : ($cS->jobs_display ?? 'card');
    $cpLogoPath = $cS->td_logo_path       ?? null;
    $cpFavPath  = $cS->td_favicon_path    ?? null;
    $cpCustomCss= $cS->td_custom_css      ?? '';
    $cpOptAnim  = $cS->td_opt_animation   ?? 1;
    $cpStatus   = isset($cS->career_page_status) ? (int)$cS->career_page_status : 1;
    $advMaintenance = !empty($cS->adv_maintenance);
    $advMaintMsg    = $cS->adv_maintenance_msg ?? "We're making some updates to serve you better. Please check back shortly!";

    /* ── Hero section settings ── */
    $heroBadge     = !empty($cS->hero_badge) ? $cS->hero_badge : ('Top Employer ' . date('Y'));
    $heroHeading   = !empty($cS->hero_heading) ? $cS->hero_heading : null;
    $heroDesc      = !empty($cS->hero_description) ? $cS->hero_description : 'Join our team of innovators and problem solvers who are building products used by thousands of recruiters worldwide.';
    $heroCta1Text  = $cS->hero_cta1_text ?? null;
    $heroCta1Link  = $cS->hero_cta1_link ?? null;
    $heroCta1Newtab= !empty($cS->hero_cta1_newtab);
    $heroCta2Text  = $cS->hero_cta2_text ?? null;
    $heroCta2Link  = $cS->hero_cta2_link ?? null;
    $heroCta2Newtab= !empty($cS->hero_cta2_newtab);
    $heroLayout    = $cS->hero_layout ?? 'left-right';
    $heroBgStyle   = $cS->hero_bg_style ?? 'gradient';
    $heroBgGradient= $cS->hero_bg_gradient ?? null;

    /* ── Company Info settings ── */
    $companyBrand    = !empty($cS->ci_company_name) ? $cS->ci_company_name : (@$account->company_name ?? '4Bell Technology');
    $companyInitials = strtoupper(substr(trim($companyBrand), 0, 2));
    $companyTagline  = $cS->ci_tagline ?? 'Building the future of recruitment with technology and trust.';
    $companyAbout    = $cS->ci_about ?? null;
    $companyMission  = $cS->ci_mission ?? null;
    $companyVision   = $cS->ci_vision ?? null;
    $companyIndustry = $cS->ci_industry ?? 'Information Technology & Services';

    /* ── Benefits settings ── */
    $benefitsEnabled = !isset($cS->benefits_enabled) || !empty($cS->benefits_enabled);
    $benefitsTitle   = $cS->benefits_title ?? 'Why Work With Us';
    $benefitsDesc    = $cS->benefits_description ?? 'We believe in building a culture where people can do their best work and grow together. Here are some of the benefits our team enjoys.';
    $benefitsStyle   = $cS->benefits_display_style ?? 'grid';
    $benefitsCount   = $cS->benefits_count ?? 8;

    /* ── Job settings ── */
    $jsShowPostedDate = !isset($cS->js_show_posted_date) || !empty($cS->js_show_posted_date);
    $jsShowExperience = !isset($cS->js_show_experience) || !empty($cS->js_show_experience);
    $jsShowSalary     = !isset($cS->js_show_salary) || !empty($cS->js_show_salary);
    $jsShowTags       = !isset($cS->js_show_tags) || !empty($cS->js_show_tags);

    /* ── Social links ── */
    $socLinkedin  = $cS->seo_linkedin ?? $cS->ci_linkedin ?? null;
    $socTwitter   = $cS->seo_twitter ?? $cS->ci_twitter ?? null;
    $socFacebook  = $cS->seo_facebook ?? null;
    $socInstagram = $cS->seo_instagram ?? null;
    $socYoutube   = $cS->seo_youtube ?? null;
    $socWebsite   = $cS->ci_website ?? null;

    $statDepts     = $cS->stat_departments ?? 15;
    $statCountries = $cS->stat_countries   ?? 5;
    $statEmployees = $cS->stat_employees   ?? '220+';
    $statRetention = $cS->stat_retention   ?? '95%';

    /* avatar gradient presets – index cycles per card */
    $avGradients = [
        'linear-gradient(135deg,#1E3A8A,#3B82F6)',
        'linear-gradient(135deg,#065F46,#10B981)',
        'linear-gradient(135deg,#4C1D95,#7C3AED)',
        'linear-gradient(135deg,#7C2D12,#F97316)',
        'linear-gradient(135deg,#7F1D1D,#EF4444)',
        'linear-gradient(135deg,#134E4A,#14B8A6)',
        'linear-gradient(135deg,#0F172A,#374151)',
    ];
@endphp

<style>
/* ════════════════════════════════════════
   CAREER JOB PAGE  ·  pixel-matched & dynamic
════════════════════════════════════════ */
:root{
    --p:  {{ $cpP }};
    --s:  {{ $cpS }};
    --a:  {{ $cpA }};
    --bg: {{ $cpBg }};
    --t:  #0F172A;
    --m:  #64748B;
    --b:  #E2E8F0;
    --l:  #F8FAFC;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:auto;}
body{font-family:'{{ $cpFont }}','Poppins',sans-serif;font-size:13px;color:var(--t);background:var(--bg);overflow-x:hidden;}
@if($cpLayout === 'boxed')
.container{width:100%;max-width:1140px;margin:0 auto;padding:0 24px;}
@elseif($cpLayout === 'fullwidth')
.container{width:100%;max-width:100%;margin:0;padding:0 40px;}
@else
.container{width:100%;max-width:1280px;margin:0 auto;padding:0 24px;}
@endif

@if($cpTheme === 'dark')
:root{--t:#F1F5F9;--m:#94A3B8;--b:#334155;--l:#1E293B;--bg:#0F172A;}
body{background:#0F172A;}
.job-card,.filters,.hero,.trusted,.stats-bar,.benefits-item{background:#1E293B !important;border-color:#334155 !important;}
.jobs-area{background:#0F172A !important;}
@endif
a{text-decoration:none;}

/* ── NAVBAR ── */
.nav{
    position:sticky;top:0;z-index:999;
    display:flex;align-items:center;justify-content:space-between;
    padding:0 40px;height:62px;
    background:#fff;border-bottom:1px solid var(--b);
    box-shadow:0 1px 6px rgba(0,0,0,.06);
}
.nav-logo{display:flex;align-items:center;gap:9px;}
.nav-logo-mark{
    width:36px;height:36px;border-radius:10px;flex-shrink:0;
    background:linear-gradient(135deg,var(--s),var(--p));
    display:flex;align-items:center;justify-content:center;
    font-size:13px;font-weight:900;color:#fff;
}
.nav-logo-name{font-size:14px;font-weight:800;color:var(--s);}
.nav-links{display:flex;align-items:center;}
.nav-link{
    padding:22px 14px;font-size:12px;font-weight:500;color:var(--m);
    border-bottom:2px solid transparent;transition:color .15s;
}
.nav-link:hover{color:var(--p);}
.nav-link.active{color:var(--p);border-bottom-color:var(--p);font-weight:700;}
.nav-actions{display:flex;align-items:center;gap:10px;}
.btn-out{
    padding:7px 18px;border:1.5px solid var(--b);border-radius:8px;
    font-size:12px;font-weight:600;color:var(--s);background:#fff;
    font-family:'{{ $cpFont }}','Poppins',sans-serif;transition:all .15s;
}
.btn-out:hover{border-color:var(--p);color:var(--p);}
.btn-pri{
    padding:8px 20px;background:var(--p);border-radius:8px;
    font-size:12px;font-weight:700;color:#fff;
    font-family:'{{ $cpFont }}','Poppins',sans-serif;transition:all .15s;
}
.btn-pri:hover{filter:brightness(0.9);color:#fff;}

/* ── HERO LAYOUT VARIATIONS ── */
.hero{ background:#fff; padding:56px 0 48px; position:relative; }
.hero .container{
    display:grid;grid-template-columns:56% 44%;
    gap:40px 40px;align-items:start;
}
.hero-search-wrap{
    grid-column:1/-1;
    margin-top:-16px;
}
.hero.hero-layout-center .container{
    display:flex;flex-direction:column;align-items:center;text-align:center;
    max-width:860px;margin:0 auto;
}
.hero.hero-layout-center .hero-sub{margin-left:auto;margin-right:auto;}
.hero.hero-layout-center .hero-tags{justify-content:center;}
.hero.hero-layout-center .hero-imgs{display:none;}
.hero.hero-layout-center .hero-search-wrap{width:100%;margin-top:16px;}
.hero.hero-layout-fullwidth{padding:72px 0 60px;}
.hero.hero-layout-fullwidth .container{grid-template-columns:60% 40%;gap:30px;align-items:center;}
.hero.hero-layout-video .hero-imgs::after{
    content:"\f144";font-family:"Font Awesome 6 Free";font-weight:900;
    position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
    font-size:48px;color:#fff;background:rgba(37,99,235,.85);
    width:72px;height:72px;border-radius:50%;display:flex;align-items:center;justify-content:center;
    box-shadow:0 8px 24px rgba(0,0,0,.2);cursor:pointer;
}
.hero-eyebrow{
    display:inline-flex;align-items:center;gap:6px;
    font-size:11px;font-weight:700;color:var(--p);
    background:#EFF6FF;border:1px solid #BFDBFE;
    padding:4px 12px;border-radius:20px;margin-bottom:16px;
}
.hero-eyebrow i{font-size:11px;}
.hero-h1{font-size:38px;font-weight:900;color:var(--s);line-height:1.15;margin-bottom:14px;}
.hero-h1 span{color:var(--p);}
.hero-sub{font-size:13px;color:var(--m);line-height:1.7;margin-bottom:22px;max-width:500px;}
.hero-tags{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:26px;}
.hero-tag{
    display:flex;align-items:center;gap:6px;
    padding:5px 12px;background:var(--l);border:1px solid var(--b);
    border-radius:20px;font-size:11px;font-weight:600;color:var(--s);
}
.hero-tag i{font-size:11px;color:var(--p);}
.hero-search{
    display:flex;align-items:stretch;
    height:54px;
    background:#fff;border:2px solid var(--b);border-radius:10px;overflow:hidden;
    box-shadow:0 6px 24px rgba(37,99,235,.1);
}
.hero-search-icon{padding:0 16px;font-size:15px;color:var(--m);display:flex;align-items:center;flex-shrink:0;}
.hero-search input{
    flex:1;height:100%;border:none;outline:none;
    font-size:13px;font-family:'Poppins',sans-serif;color:var(--t);background:transparent;
    padding:0;
}
.hero-search input::placeholder{color:#94A3B8;}
.hero-search-btn{
    height:100%;padding:0 28px;background:var(--p);border:none;border-radius:0;
    font-size:13px;font-weight:700;color:#fff;white-space:nowrap;flex-shrink:0;
    font-family:'Poppins',sans-serif;display:flex;align-items:center;gap:7px;cursor:pointer;
}
.hero-search-btn:hover{background:#1D4ED8;}
/* right images */
.hero-imgs{display:grid;grid-template-columns:1fr 1fr;gap:14px;position:relative;}
.hero-img{
    border-radius:16px;overflow:hidden;height:220px;
    background:#E2E8F0;
}
.hero-img:first-child{margin-top:0;}
.hero-img img{width:100%;height:100%;object-fit:cover;}
.hero-float{
    position:absolute;bottom:-12px;left:50%;transform:translateX(-50%);
    background:#fff;border:1.5px solid var(--b);border-radius:12px;
    padding:9px 16px;display:flex;align-items:center;gap:10px;
    box-shadow:0 6px 20px rgba(0,0,0,.09);white-space:nowrap;
}
.hero-float-avs{display:flex;}
.hero-float-avs span{
    width:26px;height:26px;border-radius:50%;
    background:var(--p);border:2px solid #fff;margin-left:-7px;
    display:flex;align-items:center;justify-content:center;
    font-size:9px;font-weight:800;color:#fff;
}
.hero-float-avs span:first-child{margin-left:0;}
.hero-float-txt{font-size:12px;font-weight:700;color:var(--t);}

/* ── TRUSTED ── */
.trusted{
    padding:22px 0 20px;
    border-top:1px solid var(--b);border-bottom:1px solid var(--b);
    background:#fff;
}
.trusted-lbl{font-size:11px;font-weight:700;color:var(--t);margin-bottom:16px;}
.trusted-row{display:flex;align-items:center;gap:0;flex-wrap:nowrap;width:100%;}
.trusted-item{
    padding:6px 0;
    font-size:14px;font-weight:800;white-space:nowrap;
    display:flex;align-items:center;gap:4px;
    flex:1;justify-content:center;
}
.trusted-item + .trusted-item{ border-left:1px solid var(--b); }
.trusted-more{
    flex:1;display:flex;align-items:center;justify-content:center;
    padding:6px 0;border-left:1px solid var(--b);
    font-size:13px;font-weight:500;color:var(--m);white-space:nowrap;
}

/* ── STATS ── */
.stats{background:var(--l);border-bottom:1px solid var(--b);}
.stats .container{display:flex;}
.stat{
    flex:1;display:flex;align-items:center;gap:13px;
    padding:18px 26px;border-right:1px solid var(--b);
}
.stat:last-child{border-right:none;}
.stat-icon{
    width:38px;height:38px;border-radius:10px;flex-shrink:0;
    background:rgba(37,99,235,.1);display:flex;align-items:center;justify-content:center;
}
.stat-icon i{font-size:16px;color:var(--p);}
.stat-val{font-size:19px;font-weight:800;color:var(--t);line-height:1.1;}
.stat-lbl{font-size:10px;color:var(--m);margin-top:1px;}

/* ── MAIN GRID ── */
.main-grid{ background:var(--l); }
.main-grid .container{display:grid;grid-template-columns:218px 1fr;}

/* ── FILTERS ── */
.filters{
    border-right:1px solid var(--b);padding:20px 16px 30px;
    background:#fff;position:sticky;top:0;align-self:start;max-height:100vh;
    overflow-y:auto;
}
.filters::-webkit-scrollbar{width:0;}
.filter-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;}
.filter-hdr-title{font-size:13px;font-weight:700;color:var(--t);}
.filter-clear{font-size:11px;font-weight:600;color:var(--p);cursor:pointer;}
.filter-sec{margin-bottom:0;border-bottom:1px solid var(--b);}
.filter-sec-hdr{
    display:flex;align-items:center;justify-content:space-between;
    font-size:12px;font-weight:700;color:var(--t);
    padding:10px 0;cursor:pointer;
}
.filter-sec-hdr i{font-size:10px;color:var(--m);transition:transform .2s;}
.filter-sec-body{padding-bottom:12px;}
.f-check{display:flex;align-items:center;gap:8px;margin-bottom:6px;cursor:pointer;}
.f-check input[type=checkbox]{width:14px;height:14px;accent-color:var(--p);cursor:pointer;flex-shrink:0;}
.f-check label{font-size:11px;color:#374151;cursor:pointer;line-height:1.3;}
.f-search{
    display:flex;align-items:center;gap:6px;
    border:1.5px solid var(--b);border-radius:7px;padding:5px 9px;margin-bottom:8px;
}
.f-search i{font-size:10px;color:var(--m);}
.f-search input{flex:1;border:none;outline:none;font-size:11px;font-family:'Poppins',sans-serif;color:var(--t);background:transparent;}
.f-tags{display:flex;flex-wrap:wrap;gap:5px;}
.f-tag{
    padding:3px 10px;background:var(--l);border:1px solid var(--b);
    border-radius:20px;font-size:10px;font-weight:600;color:#374151;cursor:pointer;
}
.f-tag:hover,.f-tag.on{background:#EFF6FF;border-color:var(--p);color:var(--p);}

/* ── JOBS AREA ── */
.jobs-area{padding:20px 0 20px 28px;background:var(--l);}
.jobs-topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;}
.jobs-count{font-size:15px;font-weight:800;color:var(--t);}
.jobs-sort{display:flex;align-items:center;gap:8px;}
.jobs-sort-lbl{font-size:12px;color:var(--m);}
.jobs-sort select{
    border:1.5px solid var(--b);border-radius:7px;padding:5px 10px 5px 8px;
    font-size:12px;font-weight:600;color:var(--t);
    font-family:'Poppins',sans-serif;background:#fff;cursor:pointer;outline:none;
}

/* ── JOB CARD ── */
.job-card{
    background:#fff;border:1.5px solid var(--b);border-radius:12px;
    padding:18px 24px 14px;margin-bottom:12px;
    transition:box-shadow .15s,border-color .15s;
}
.job-card:hover{box-shadow:0 4px 20px rgba(37,99,235,.08);border-color:#93C5FD;}

/* main row: drag · avatar · info · meta-cols · right */
.jc-main{display:flex;align-items:flex-start;gap:12px;}

/* 2×3 dot drag handle */
.jc-drag{
    display:grid;grid-template-columns:1fr 1fr;gap:3px;
    padding-top:6px;cursor:grab;opacity:.25;flex-shrink:0;
}
.jc-drag span{display:block;width:4px;height:4px;background:#94A3B8;border-radius:50%;}

/* circular avatar */
.jc-av{
    width:54px;height:54px;border-radius:50%;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;
    font-size:20px;font-weight:900;color:#fff;overflow:hidden;
    border:1.5px solid rgba(0,0,0,.06);
}
.jc-av img{width:100%;height:100%;object-fit:cover;border-radius:50%;}

/* title / category / company */
.jc-info{flex:0 0 180px;min-width:0;}
.jc-title{font-size:13px;font-weight:700;color:var(--t);margin-bottom:3px;line-height:1.35;}
.jc-cat{font-size:10px;color:var(--m);margin-bottom:2px;}
.jc-co{font-size:11px;color:var(--m);font-weight:500;}

/* meta 2-column grid in the centre */
.jc-meta-cols{
    flex:1;display:grid;grid-template-columns:1fr 1fr;
    row-gap:6px;column-gap:10px;align-content:start;
}
.jc-mi{display:flex;align-items:center;gap:5px;font-size:11px;color:#374151;}
.jc-mi i{font-size:11px;color:var(--m);flex-shrink:0;}
.jc-mi.sal{font-weight:700;color:var(--t);}
.jc-mi.jid{color:var(--p);font-weight:600;grid-column:1/-1;}

/* right column: [badge · bookmark] then time below */
.jc-right{display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0;min-width:120px;}
.jc-right-top{display:flex;align-items:center;gap:8px;}
.jc-badge{
    padding:3px 12px;border-radius:20px;font-size:11px;font-weight:700;
    white-space:nowrap;border:1.5px solid transparent;
}
.badge-hiring{background:#F0FDF4;color:#16A34A;border-color:#86EFAC;}
.badge-new{background:#EFF6FF;color:#1D4ED8;border-color:#93C5FD;}
.badge-closed{background:#FEF2F2;color:#DC2626;border-color:#FCA5A5;}
.badge-hold{background:#FFFBEB;color:#D97706;border-color:#FCD34D;}
.jc-time{font-size:11px;color:var(--m);text-align:right;}
.jc-bm{
    cursor:pointer;color:#94A3B8;transition:color .15s;font-size:15px;
    width:28px;height:28px;border:1.5px solid var(--b);border-radius:7px;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.jc-bm:hover{color:var(--p);border-color:var(--p);}

/* bottom row: skills label + chips | actions */
.jc-bottom{display:flex;align-items:center;justify-content:space-between;margin-top:12px;flex-wrap:wrap;gap:8px;}
.jc-skills-row{display:flex;align-items:center;flex-wrap:wrap;gap:5px;flex:1;}
.jc-skills-lbl{font-size:11px;font-weight:600;color:#374151;margin-right:4px;white-space:nowrap;}
.jc-skill{
    padding:3px 10px;background:#F1F5F9;border:none;
    border-radius:20px;font-size:10px;font-weight:600;color:#475569;
}

/* action buttons */
.jc-actions{display:flex;align-items:center;gap:8px;flex-shrink:0;}
.btn-view{
    padding:7px 18px;border:1.5px solid var(--b);border-radius:8px;
    font-size:12px;font-weight:600;color:var(--s);background:#fff;
    cursor:pointer;font-family:'Poppins',sans-serif;transition:all .15s;text-decoration:none;display:inline-flex;align-items:center;
}
.btn-view:hover{border-color:var(--p);color:var(--p);}
.btn-apply{
    padding:7px 22px;background:var(--p);border:none;border-radius:8px;
    font-size:12px;font-weight:700;color:#fff;
    cursor:pointer;font-family:'Poppins',sans-serif;transition:background .15s;text-decoration:none;display:inline-flex;align-items:center;
}
.btn-apply:hover{background:#1D4ED8;color:#fff;}

/* empty state */
.jobs-empty{text-align:center;padding:60px 20px;}
.jobs-empty i{font-size:40px;color:var(--b);display:block;margin-bottom:10px;}
.jobs-empty-t{font-size:14px;font-weight:700;color:var(--t);}
.jobs-empty-s{font-size:12px;color:var(--m);margin-top:4px;}

/* ── PAGINATION ── */
.pag{display:flex;align-items:center;justify-content:flex-end;gap:4px;margin-top:18px;}
.pag-btn{
    min-width:34px;height:34px;border:1.5px solid var(--b);border-radius:8px;
    display:flex;align-items:center;justify-content:center;
    font-size:12px;font-weight:600;color:var(--t);
    background:#fff;padding:0 8px;transition:all .15s;
}
.pag-btn:hover{border-color:var(--p);color:var(--p);}
.pag-btn.on{background:var(--p);border-color:var(--p);color:#fff;}
.pag-btn.off{opacity:.4;pointer-events:none;cursor:not-allowed;}
.pag-dots{min-width:34px;height:34px;display:flex;align-items:center;justify-content:center;font-size:12px;color:var(--m);}

/* ── BENEFITS ── */
.benefits{border-top:1px solid var(--b);background:#fff;}
.benefits .container{display:grid;grid-template-columns:repeat(4,1fr);}
.benefit{display:flex;align-items:center;gap:14px;padding:22px 24px;border-right:1px solid var(--b);}
.benefit:last-child{border-right:none;}
.benefit-icon{width:40px;height:40px;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;}
.benefit-icon i{font-size:18px;}
.benefit-title{font-size:12px;font-weight:700;color:var(--t);}
.benefit-desc{font-size:10px;color:var(--m);margin-top:2px;}

/* ── FOOTER ── */
.footer{
    display:flex;align-items:center;justify-content:space-between;
    padding:16px 40px;border-top:1px solid var(--b);background:#fff;
}
.footer-left{display:flex;flex-direction:column;gap:4px;}
.footer-logo{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:800;color:var(--s);}
.footer-logo-mark{width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,var(--s),var(--p));display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:900;color:#fff;}
.footer-links{display:flex;align-items:center;gap:20px;}
.footer-link{font-size:11px;color:var(--m);}
.footer-link:hover{color:var(--p);}
.footer-copy{font-size:10px;color:var(--m);}
.footer-socials{display:flex;align-items:center;gap:7px;}
.footer-social{
    width:32px;height:32px;border:1.5px solid var(--b);border-radius:8px;
    display:flex;align-items:center;justify-content:center;
    font-size:13px;color:var(--m);transition:all .15s;text-decoration:none;
}
.footer-social:hover{border-color:var(--p);color:var(--p);}

@media(max-width:1100px){
    .hero .container{grid-template-columns:1fr;}.hero-imgs{display:none;}
    .main-grid .container{grid-template-columns:1fr;}.filters{display:none;}
    .benefits .container{grid-template-columns:1fr 1fr;}
}
</style>
@if(!empty($cpCustomCss))
<style id="career-custom-css">{{ $cpCustomCss }}</style>
@endif

<body class="career-tpl-{{ $cpTemplate }} career-layout-{{ $cpLayout }}{{ $cpOptAnim ? ' career-anim' : '' }}">

@if($cpStatus === 0 || $advMaintenance)
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--bg);font-family:'{{ $cpFont }}',sans-serif;padding:40px;text-align:center;">
    <div style="max-width:480px;background:#fff;border:1.5px solid var(--b);border-radius:16px;padding:40px;box-shadow:0 10px 30px rgba(0,0,0,.08);">
        <div style="width:64px;height:64px;background:rgba(37,99,235,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <i class="fa-solid fa-building-lock" style="font-size:28px;color:var(--p);"></i>
        </div>
        <h2 style="font-size:20px;font-weight:800;color:var(--s);margin-bottom:8px;">{{ $companyBrand }} Careers</h2>
        <p style="font-size:13px;color:var(--m);line-height:1.6;margin-bottom:20px;">
            @if($cpStatus === 0)
                Our career portal is currently offline for scheduled maintenance. Please check back soon for open opportunities!
            @else
                {{ $advMaintMsg }}
            @endif
        </p>
        <div style="font-size:11px;color:#94A3B8;">&copy; {{ date('Y') }} {{ $companyBrand }}. All rights reserved.</div>
    </div>
</div>
@else

{{-- ════ HERO ════ --}}
<section class="hero hero-layout-{{ $heroLayout }}" @if($heroBgStyle === 'gradient' && !empty($heroBgGradient)) style="background:{{ $heroBgGradient }};" @endif>
<div class="container">
    <div>
        <div class="hero-eyebrow"><i class="fa-solid fa-star"></i> {{ $heroBadge }}</div>
        @if(!empty($heroHeading))
            <h1 class="hero-h1">{!! $heroHeading !!}</h1>
        @else
            <h1 class="hero-h1">Build the Future with<br><span>{{ $companyBrand }}</span></h1>
        @endif
        <p class="hero-sub">{{ $heroDesc }}</p>
        
        @if(!empty($heroCta1Text) || !empty($heroCta2Text))
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:22px;flex-wrap:wrap;">
            @if(!empty($heroCta1Text))
                <a href="{{ $heroCta1Link ?: '#jobs' }}" class="btn-pri" {{ $heroCta1Newtab ? 'target="_blank"' : '' }}>{{ $heroCta1Text }}</a>
            @endif
            @if(!empty($heroCta2Text))
                <a href="{{ $heroCta2Link ?: '#about' }}" class="btn-out" {{ $heroCta2Newtab ? 'target="_blank"' : '' }}>{{ $heroCta2Text }}</a>
            @endif
        </div>
        @endif

        <div class="hero-tags">
            <div class="hero-tag"><i class="fa-solid fa-user-group"></i> Great People</div>
            <div class="hero-tag"><i class="fa-regular fa-clock"></i> Flexible Work</div>
            <div class="hero-tag"><i class="fa-solid fa-chart-line"></i> Learning Growth</div>
            <div class="hero-tag"><i class="fa-solid fa-rocket"></i> Career Acceleration</div>
        </div>
    </div>
    <div class="hero-imgs">
        <div class="hero-img"><img src="{{ !empty($cS->td_hero_img1_path) ? asset($cS->td_hero_img1_path) : 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=400&h=220&fit=crop&auto=format' }}" alt="Team collaboration"></div>
        <div class="hero-img"><img src="{{ !empty($cS->td_hero_img2_path) ? asset($cS->td_hero_img2_path) : 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=400&h=220&fit=crop&auto=format' }}" alt="Modern office"></div>
    </div>
    <div class="hero-search-wrap">
        <form method="GET" action="" class="hero-search">
            <i class="fa-solid fa-magnifying-glass" style="padding:0 16px;font-size:15px;color:var(--m);display:flex;align-items:center;flex-shrink:0;"></i>
            <input type="text" name="custom_search" value="{{ request('custom_search') }}" placeholder="Search by job title, keyword or skills..." style="flex:1;height:100%;border:none;outline:none;font-size:13px;font-family:'Poppins',sans-serif;color:var(--t);background:transparent;" />
            <button type="submit" style="height:100%;padding:0 28px;background:var(--p);border:none;font-size:13px;font-weight:700;color:#fff;white-space:nowrap;font-family:'Poppins',sans-serif;display:flex;align-items:center;gap:7px;cursor:pointer;flex-shrink:0;">
                <i class="fa-solid fa-magnifying-glass"></i> Search Jobs
            </button>
        </form>
    </div>
</div>
</section>

{{-- ════ TRUSTED ════ --}}
<section class="trusted">
<div class="container">
    <div class="trusted-lbl">Trusted by leading companies</div>
    <div class="trusted-row">
        {{-- Amazon --}}
        <div class="trusted-item" style="font-family:Georgia,serif;">
            <span style="color:#232F3E;font-size:16px;font-weight:900;letter-spacing:-.5px;">amazon</span><span style="color:#FF9900;font-size:13px;font-weight:700;">.co</span>
        </div>
        {{-- Flipkart --}}
        <div class="trusted-item" style="color:#2874F0;font-size:15px;">
            Flipkart&nbsp;<i class="fa-solid fa-star" style="color:#F59E0B;font-size:10px;"></i>
        </div>
        {{-- Alibaba --}}
        <div class="trusted-item" style="color:#FF6A00;font-size:15px;font-style:italic;">Alibaba</div>
        {{-- NYKAA --}}
        <div class="trusted-item" style="color:#000;font-size:14px;letter-spacing:2px;font-family:'Poppins',sans-serif;">NYKAA</div>
        {{-- Jio --}}
        <div class="trusted-item">
            <span style="background:#0B2547;color:#fff;font-size:12px;font-weight:900;
                         width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;">Jio</span>
        </div>
        {{-- Paytm --}}
        <div class="trusted-item" style="font-size:15px;">
            <span style="color:#002970;font-weight:900;">Pay</span><span style="color:#00B9F1;font-weight:900;">tm</span>
        </div>
        {{-- BYJU'S --}}
        <div class="trusted-item" style="color:#6D28D9;font-size:14px;">
            <span style="background:#6D28D9;color:#fff;font-size:9px;padding:2px 4px;border-radius:3px;margin-right:4px;font-weight:900;">B</span>BYJU'S
        </div>
        {{-- 150+ --}}
        <div class="trusted-more">150+ other clients</div>
    </div>
</div>
</section>

{{-- ════ STATS ════ --}}
<section class="stats">
<div class="container">
    <div class="stat"><div class="stat-icon"><i class="fa-solid fa-briefcase"></i></div><div><div class="stat-val">{{ $total }}</div><div class="stat-lbl">Open Positions</div></div></div>
    <div class="stat"><div class="stat-icon"><i class="fa-solid fa-layer-group"></i></div><div><div class="stat-val">{{ $statDepts }}</div><div class="stat-lbl">Departments</div></div></div>
    <div class="stat"><div class="stat-icon"><i class="fa-solid fa-globe"></i></div><div><div class="stat-val">{{ $statCountries }}</div><div class="stat-lbl">Countries</div></div></div>
    <div class="stat"><div class="stat-icon"><i class="fa-solid fa-user-group"></i></div><div><div class="stat-val">{{ $statEmployees }}</div><div class="stat-lbl">Employees</div></div></div>
    <div class="stat"><div class="stat-icon"><i class="fa-solid fa-heart"></i></div><div><div class="stat-val">{{ $statRetention }}</div><div class="stat-lbl">Employee Retention</div></div></div>
</div>
</section>

{{-- ════ MAIN ════ --}}
<div class="main-grid">
<div class="container">

    {{-- ── FILTER SIDEBAR ── --}}
    <aside class="filters">
        <form method="GET" action="" id="filterForm">
            {{-- preserve search & sort --}}
            <input type="hidden" name="custom_search"     value="{{ request('custom_search') }}">
            <input type="hidden" name="custom_hiringtype" value="{{ request('custom_hiringtype','All') }}">

            <div class="filter-hdr">
                <span class="filter-hdr-title">Filters</span>
                <a href="{{ url()->current() }}" class="filter-clear">Clear All</a>
            </div>

            {{-- Experience --}}
            <div class="filter-sec">
                <div class="filter-sec-hdr" onclick="togSec(this)">
                    Experience <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="filter-sec-body">
                    @foreach(['Fresher','1 – 3 Years','3 – 5 Years','5 – 8 Years','8+ Years'] as $ex)
                    <label class="f-check">
                        <input type="checkbox" name="filter_exp[]" value="{{ $ex }}" onchange="submitFilters()"
                            {{ in_array($ex, (array)request('filter_exp',[])) ? 'checked' : '' }}>
                        <span>{{ $ex }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Department --}}
            <div class="filter-sec">
                <div class="filter-sec-hdr" onclick="togSec(this)">
                    Department <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="filter-sec-body">
                    @foreach(['All Departments','Engineering','Product','Design','Marketing','Sales','HR','Finance','Support'] as $dp)
                    <label class="f-check">
                        <input type="checkbox" name="filter_dept[]" value="{{ $dp }}" onchange="submitFilters()"
                            {{ in_array($dp, (array)request('filter_dept',[])) ? 'checked' : '' }}>
                        <span>{{ $dp }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Employment Type --}}
            <div class="filter-sec">
                <div class="filter-sec-hdr" onclick="togSec(this)">
                    Employment Type <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="filter-sec-body">
                    @foreach(['Full-time','Part-time','Contract','Internship'] as $et)
                    <label class="f-check">
                        <input type="checkbox" name="filter_emp_type[]" value="{{ $et }}" onchange="submitFilters()"
                            {{ in_array($et, (array)request('filter_emp_type',[])) ? 'checked' : '' }}>
                        <span>{{ $et }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Location --}}
            <div class="filter-sec">
                <div class="filter-sec-hdr" onclick="togSec(this)">
                    Location <i class="fa-solid fa-chevron-up"></i>
                </div>
                <div class="filter-sec-body">
                    <div class="f-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Search location" oninput="filterLocSearch(this)">
                    </div>
                    @foreach(['Noida','Bangalore','Hyderabad','Pune','Remote'] as $lc)
                    <label class="f-check loc-row">
                        <input type="checkbox" name="filter_location[]" value="{{ $lc }}" onchange="submitFilters()"
                            {{ in_array($lc, (array)request('filter_location',[])) ? 'checked' : '' }}>
                        <span>{{ $lc }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Skills --}}
            <div class="filter-sec" style="border-bottom:none;">
                <div class="filter-sec-hdr" onclick="togSec(this)">
                    Skills <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="filter-sec-body">
                    <div class="f-search" style="margin-bottom:8px;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Search skills" oninput="filterSkillSearch(this)">
                    </div>
                    <div class="f-tags" id="skillTagsWrap">
                        @php $allSkills = ['JavaScript','React','Node.js','Python','AWS','UI/UX','Java','PHP','Angular','Vue']; $visibleSkills = array_slice($allSkills,0,6); $hiddenSkills = array_slice($allSkills,6); @endphp
                        @foreach($visibleSkills as $sk)
                        <button type="button" name="filter_skills[]" value="{{ $sk }}"
                            class="f-tag skill-tag {{ in_array($sk,(array)request('filter_skills',[])) ? 'on' : '' }}"
                            onclick="toggleSkillTag(this)">{{ $sk }}</button>
                        @endforeach
                        @foreach($hiddenSkills as $sk)
                        <button type="button" name="filter_skills[]" value="{{ $sk }}"
                            class="f-tag skill-tag skill-tag-extra {{ in_array($sk,(array)request('filter_skills',[])) ? 'on' : '' }}"
                            onclick="toggleSkillTag(this)" style="display:none;">{{ $sk }}</button>
                        @endforeach
                        <button type="button" class="f-tag" id="skillMoreBtn" onclick="showMoreSkills(this)" style="color:var(--p);border-color:var(--p);background:#EFF6FF;">+ More</button>
                    </div>
                </div>
            </div>
        </form>
    </aside>

    {{-- ── JOBS LIST ── --}}
    <div class="jobs-area">
        <div class="jobs-topbar">
            <div class="jobs-count">{{ $total }} Open Positions</div>
            <div class="jobs-sort">
                <span class="jobs-sort-lbl">Sort by:</span>
                <form method="GET" action="" id="sortForm">
                    <select name="custom_hiringtype" onchange="var ff=document.getElementById('filterForm'); if(ff){ var ht=ff.querySelector('input[name=custom_hiringtype]'); if(ht){ ht.value=this.value; } ff.submit(); } else { this.form.submit(); }">
                        <option value="All" {{ request('custom_hiringtype','All')==='All'?'selected':'' }}>Newest First</option>
                        <option value="2"   {{ request('custom_hiringtype')==='2'?'selected':'' }}>Oldest First</option>
                        <option value="3"   {{ request('custom_hiringtype')==='3'?'selected':'' }}>Salary: Low to High</option>
                        <option value="4"   {{ request('custom_hiringtype')==='4'?'selected':'' }}>Salary: High to Low</option>
                        <option value="5"   {{ request('custom_hiringtype')==='5'?'selected':'' }}>A to Z (Company)</option>
                        <option value="7"   {{ request('custom_hiringtype')==='7'?'selected':'' }}>Full-Time Only</option>
                        <option value="8"   {{ request('custom_hiringtype')==='8'?'selected':'' }}>Contract Only</option>
                    </select>
                </form>
            </div>
        </div>

        @if(count($data) > 0)
            @foreach($data as $idx => $rowArr)
            @php
                $row = (object)$rowArr;
                $avGrad = $avGradients[$idx % count($avGradients)];

                /* first letter of title for avatar */
                $titleStr    = trim(@$row->title ?? 'J');
                $firstLetter = strtoupper($titleStr[0] ?? 'J');

                /* recommendation logic */
                $match = collect($row->recommendation_relation)->first(function($item) use ($company_id){
                    return ($item['from_company_id'] ?? null) == $company_id
                        || ($item['to_company_id']   ?? null) == $company_id;
                });
                $recId        = ($match['to_company_id'] ?? null) == $company_id ? $match['to_company_id'] : null;
                $row->final_status = ($match['to_company_id'] ?? null) == $company_id ? $match['final_status'] : null;
                $isRec        = ($recId > 0 && @$row->to_company_id == @$company_id);
                $displayCo    = $isRec
                    ? ucwords(mb_substr(@$row->company_name, 0, 50))
                    : ucwords(mb_substr(@$account->company_name, 0, 50));

                /* skills — eager loaded */
                $skills = array_slice($jobSkillsMap[$row->id] ?? [], 0, 8);

                /* salary */
                $minSalMode = strtoupper(mb_substr($salaryMode[$row->min_sal_mode], 0, 1));
                $maxSalMode = strtoupper(mb_substr($salaryMode[$row->max_sal_mode], 0, 1));
                $minCur     = strip_tags($currencySymbole[@$row->min_salary_currency] ?? '₹');
                $maxCur     = strip_tags($currencySymbole[@$row->max_salary_currency] ?? '₹');
                $minSal     = GeneralMethod::thousandsCurrencyFormat($row->min_salary);
                $maxSal     = GeneralMethod::thousandsCurrencyFormat($row->max_salary);

                /* location — eager loaded */
                $locLabel = 'Remote';
                if ($row->work_type !== 'yes') {
                    $locs = $jobLocationsMap[$row->id] ?? [];
                    if (count($locs) > 1) {
                        $locLabel = 'Multiple';
                    } elseif (count($locs) === 1) {
                        $locLabel = $locs[0];
                    } else {
                        $locLabel = 'N/A';
                    }
                }
                $workMode   = ($row->work_type === 'yes') ? 'Remote' : 'Hybrid';
                $empType    = ($row->employment_type == 1) ? 'Contract' : 'Full-time';

                /* status badge */
                $statusText = ucwords($row->status);
                if ($statusText === 'Inactive') $statusText = 'Closed';
                if (($row->final_status == 2 || $row->final_status == 3) && @$row->to_company_id == @$company_id)
                    $statusText = 'Removed';
                $daysDiff   = (int)Carbon::parse($row->created_at)->diffInDays(now());
                $badgeLabel = ($statusText === 'Active')
                    ? ($daysDiff <= 3 ? 'New' : 'Hiring')
                    : $statusText;
                $badgeClass = match($badgeLabel){
                    'Hiring'            => 'badge-hiring',
                    'New'               => 'badge-new',
                    'Closed','Removed'  => 'badge-closed',
                    default             => 'badge-hold',
                };

                $timeAgo    = Carbon::parse($row->created_at)->diffForHumans(['parts'=>1]);

                /* company logo — eager loaded */
                $logoPath   = $jobLogosMap[$row->id] ?? '';

                /* hiring manager */
                $hmName = collect($row->job_assign)->first(function($item) use ($company_id){
                    return ($item['company_id'] ?? null) == $company_id
                        && (int)($item['spok_user_id'] ?? 0) === (int)($item['user_id'] ?? -1);
                })['user_name'] ?? null;
            @endphp

            <div class="job-card">
                {{-- MAIN ROW: drag · avatar · info · meta-cols · right --}}
                <div class="jc-main">
                    <div class="jc-drag">
                        <span></span><span></span>
                        <span></span><span></span>
                        <span></span><span></span>
                    </div>

                    {{-- Square avatar --}}
                    <div class="jc-av" style="background:{{ $avGrad }};">
                        @if(!empty($logoPath))
                            <img src="{{ str_replace('index.php','',$logoPath) }}" alt="{{ $displayCo }}">
                        @else
                            {{ $firstLetter }}
                        @endif
                    </div>

                    {{-- Title / category / company --}}
                    <div class="jc-info">
                        <div class="jc-title">{{ ucwords(mb_substr(@$row->title, 0, 50)) }}</div>
                        @if(@$row->job_category_name)
                        <div class="jc-cat">{{ $row->job_category_name }}</div>
                        @endif
                        <div class="jc-co">{{ $displayCo }}</div>
                    </div>

                    {{-- 2-col meta grid --}}
                    <div class="jc-meta-cols">
                        @if(@$row->min_exp || @$row->max_exp)
                        <div class="jc-mi"><i class="fa-regular fa-clock"></i> {{ @$row->min_exp }} – {{ @$row->max_exp }} Years</div>
                        @else
                        <div></div>
                        @endif
                        <div class="jc-mi"><i class="fa-solid fa-location-dot"></i> {{ $locLabel }}</div>

                        @if(@$row->min_salary || @$row->max_salary)
                        <div class="jc-mi sal"><i class="fa-solid fa-indian-rupee-sign"></i> {{ $minCur }}{{ $minSal }}{{ $minSalMode }} – {{ $maxCur }}{{ $maxSal }}{{ $maxSalMode }}</div>
                        @else
                        <div></div>
                        @endif
                        <div class="jc-mi"><i class="fa-solid fa-house"></i> {{ $workMode }}</div>

                        <div class="jc-mi jid"><i class="fa-solid fa-tag"></i> Job ID: {{ $row->id }}</div>
                    </div>

                    {{-- Badge + bookmark on same row, time below --}}
                    <div class="jc-right">
                        <div class="jc-right-top">
                            <span class="jc-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                            <div class="jc-bm"><i class="fa-regular fa-bookmark"></i></div>
                        </div>
                        <span class="jc-time">{{ $timeAgo }}</span>
                    </div>
                </div>

                {{-- BOTTOM ROW: Skills label + chips | action buttons --}}
                <div class="jc-bottom">
                    <div class="jc-skills-row">
                        @if(!empty($skills))
                        <span class="jc-skills-lbl">Skills:</span>
                        @foreach($skills as $sk)
                        <span class="jc-skill">{{ ucfirst(is_object($sk) ? $sk->skill_name : $sk) }}</span>
                        @endforeach
                        @endif
                    </div>
                    <div class="jc-actions">
                        <a href="{{ url('job-details/'.@$row->id.'/'.$company_id) }}" class="btn-view">View Job</a>
                        <a href="{{ url('job-apply/'.@$row->id.'/'.$company_id) }}" class="btn-apply">Apply</a>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="jobs-empty">
                <i class="fa-solid fa-briefcase"></i>
                <div class="jobs-empty-t">No open positions right now</div>
                <div class="jobs-empty-s">Check back soon — we're always growing!</div>
            </div>
        @endif

        {{-- ── PAGINATION ── --}}
        @php
            $tPages    = $last_page;
            $cPage     = $current_page;
            $side      = 2;
            $pStart    = max(1, $cPage - $side);
            $pEnd      = min($tPages, $cPage + $side);
            $queryVars = request()->except('page');
            $qs        = !empty($queryVars) ? '&' . http_build_query($queryVars) : '';
        @endphp
        @if($tPages > 1)
        <div class="pag">
            <a href="?page={{ max(1,$cPage-1) }}{{ $qs }}" class="pag-btn {{ $cPage<=1?'off':'' }}">
                <i class="fa-solid fa-chevron-left" style="font-size:9px;"></i>&nbsp;Previous
            </a>
            @if($pStart > 2)<a href="?page=1{{ $qs }}" class="pag-btn">1</a><span class="pag-dots">…</span>@endif
            @for($p=$pStart;$p<=$pEnd;$p++)
            <a href="?page={{ $p }}{{ $qs }}" class="pag-btn {{ $cPage==$p?'on':'' }}">{{ $p }}</a>
            @endfor
            @if($pEnd < $tPages-1)<span class="pag-dots">…</span><a href="?page={{ $tPages }}{{ $qs }}" class="pag-btn">{{ $tPages }}</a>@endif
            <a href="?page={{ min($tPages,$cPage+1) }}{{ $qs }}" class="pag-btn {{ $cPage>=$tPages?'off':'' }}">
                Next&nbsp;<i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
            </a>
        </div>
        @endif
    </div>{{-- /jobs-area --}}

</div>{{-- /container --}}
</div>{{-- /main-grid --}}

@if($benefitsEnabled)
{{-- ════ BENEFITS ════ --}}
<section class="benefits" id="benefits">
<div class="container">
    @php $bens=[
        ['fa-house','#EFF6FF','#2563EB','Flexible Work Culture','Work from anywhere, anytime'],
        ['fa-heart-pulse','#FFF1F2','#E11D48','Health & Wellness','Health insurance & wellness programs'],
        ['fa-chart-line','#F0FDF4','#16A34A','Learning & Growth','Continuous learning & upskilling'],
        ['fa-rocket','#FFF7ED','#F97316','Career Acceleration','Fast-track your growth with us'],
    ]; 
    $bens = array_slice($bens, 0, $benefitsCount);
    @endphp
    @foreach($bens as [$ico,$bg,$col,$title,$desc])
    <div class="benefit">
        <div class="benefit-icon" style="background:{{ $bg }};"><i class="fa-solid {{ $ico }}" style="color:{{ $col }};"></i></div>
        <div><div class="benefit-title">{{ $title }}</div><div class="benefit-desc">{{ $desc }}</div></div>
    </div>
    @endforeach
</div>
</section>
@endif

@endif

<script src="{{ asset('demo/plugins/global/plugins.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}{{ TM_VERSION }}"></script>
<script>
/* filter section toggle */
function togSec(hdr){
    var body = hdr.nextElementSibling, ic = hdr.querySelector('i');
    if(!body) return;
    var hidden = body.style.display === 'none';
    body.style.display = hidden ? '' : 'none';
    ic.className = hidden ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down';
}
/* submit filter form on any checkbox/tag change */
function submitFilters(){
    var f = document.getElementById('filterForm');
    if(f) f.submit();
}
/* skill tag toggle — adds hidden input then submits */
function toggleSkillTag(btn){
    btn.classList.toggle('on');
    var form = document.getElementById('filterForm');
    /* remove existing hidden inputs for this skill */
    form.querySelectorAll('input[type=hidden][name="filter_skills[]"]').forEach(function(el){
        if(el.value === btn.value) el.remove();
    });
    if(btn.classList.contains('on')){
        var h = document.createElement('input');
        h.type = 'hidden'; h.name = 'filter_skills[]'; h.value = btn.value;
        form.appendChild(h);
    }
    submitFilters();
}

/* location search */
function filterLocSearch(inp){
    var q = inp.value.toLowerCase();
    document.querySelectorAll('.loc-row').forEach(function(r){
        r.style.display = r.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}

/* bookmark toggle */
document.querySelectorAll('.jc-bm').forEach(function(bm){
    bm.addEventListener('click', function(){
        var ic = this.querySelector('i');
        ic.classList.toggle('fa-regular');
        ic.classList.toggle('fa-solid');
        this.classList.toggle('on');
    });
});

/* skill search filter */
function filterSkillSearch(inp){
    var q = inp.value.toLowerCase();
    document.querySelectorAll('.skill-tag').forEach(function(btn){
        btn.style.display = q && !btn.value.toLowerCase().includes(q) ? 'none' : '';
    });
    var moreBtn = document.getElementById('skillMoreBtn');
    if(moreBtn) moreBtn.style.display = q ? 'none' : '';
}
/* show all hidden skill tags */
function showMoreSkills(btn){
    document.querySelectorAll('.skill-tag-extra').forEach(function(el){ el.style.display = ''; });
    btn.style.display = 'none';
}

/* iframe height reporting */
function sendHeight(){
    window.parent.postMessage({frameHeight: document.body.scrollHeight}, '*');
    setTimeout(function(){
        window.parent.postMessage({frameHeight: document.body.scrollHeight}, '*');
    }, 150);
}
window.onload = sendHeight;
window.onresize = sendHeight;
</script>

</body>
</html>
