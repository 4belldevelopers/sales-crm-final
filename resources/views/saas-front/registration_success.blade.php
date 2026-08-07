<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Your Email Address - Jero</title>
  
  <!-- Google Font: Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- CSS Dependencies -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
  
  <!-- jQuery & SweetAlert2 -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* 
     * JERO SaaS - Registration Success Email Verification Page
     * Intent: Single Viewport Height (100vh No Scroll) Design for Desktop.
     * Tenancy / Logic: Pure Blade presentation layer. Preserves all existing routes ($user->id, $user->email, resend AJAX, Swal).
     */
    :root {
      --primary: #1d61f2;
      --primary-hover: #154ec2;
      --primary-light: #eff6ff;
      --primary-border: #dbeafe;
      --text-dark: #0f172a;
      --text-muted: #64748b;
      --bg-page: #f8fafc;
      --card-bg: #ffffff;
      --card-border: #e2e8f0;
      --radius-lg: 16px;
      --radius-md: 12px;
      --radius-sm: 8px;
      --shadow-sm: 0 2px 6px rgba(15, 23, 42, 0.03);
      --shadow-md: 0 6px 18px rgba(15, 23, 42, 0.05);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html, body {
      height: 100vh;
      max-height: 100vh;
      overflow: hidden;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--bg-page);
      color: var(--text-dark);
      line-height: 1.45;
      display: flex;
      flex-direction: column;
      -webkit-font-smoothing: antialiased;
    }

    /* Layout Container Locked to 100vh */
    .page-container {
      max-width: 1260px;
      width: 100%;
      height: 100vh;
      max-height: 100vh;
      margin: 0 auto;
      padding: 16px 28px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden;
    }

    /* Header */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-bottom: 8px;
      flex-shrink: 0;
    }

    .brand-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
    }

    .brand-logo-icon {
      width: 28px;
      height: 28px;
    }

    .brand-title {
      font-size: 20px;
      font-weight: 800;
      color: var(--text-dark);
      line-height: 1;
      letter-spacing: -0.5px;
    }

    .brand-subtitle {
      font-size: 11px;
      font-weight: 500;
      color: var(--text-muted);
      margin-top: 2px;
      display: block;
    }

    .header-help {
      display: flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      color: var(--text-muted);
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
      transition: color 0.2s;
    }

    .header-help:hover {
      color: var(--primary);
    }

    .help-icon-circle {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: var(--primary);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      font-weight: 700;
    }

    /* Hero Grid Layout */
    .hero-layout {
      display: grid;
      grid-template-columns: 44% 56%;
      gap: 32px;
      align-items: center;
      flex: 1;
      min-height: 0;
      padding: 4px 0;
    }

    /* Left Column Illustration */
    .illustration-col {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100%;
      max-height: 100%;
      overflow: hidden;
    }

    .illustration-wrapper {
      width: 100%;
      max-width: 440px;
      max-height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .illustration-svg {
      width: 100%;
      max-height: 350px;
      height: auto;
      display: block;
      filter: drop-shadow(0 12px 24px rgba(29, 97, 242, 0.07));
    }

    /* Right Column Details */
    .content-col {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      justify-content: center;
      height: 100%;
      min-height: 0;
    }

    /* Top Decorative Icon Header */
    .top-icon-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 14px;
      width: 100%;
      margin-bottom: 10px;
    }

    .top-decor-line {
      height: 1px;
      background: #e2e8f0;
      flex: 1;
      max-width: 80px;
    }

    .top-blue-badge {
      width: 46px;
      height: 46px;
      border-radius: 50%;
      background: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 5px;
    }

    .top-blue-badge-inner {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      background: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      box-shadow: 0 3px 10px rgba(29, 97, 242, 0.25);
    }

    .page-title {
      font-size: 24px;
      font-weight: 800;
      color: var(--text-dark);
      letter-spacing: -0.5px;
      margin-bottom: 4px;
    }

    .email-sent-text {
      font-size: 12.5px;
      color: var(--text-muted);
      margin-bottom: 6px;
    }

    .email-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--primary-light);
      border: 1px solid var(--primary-border);
      color: var(--primary);
      font-weight: 600;
      font-size: 13.5px;
      padding: 5px 16px;
      border-radius: 50px;
      margin-bottom: 10px;
    }

    .email-badge svg {
      flex-shrink: 0;
    }

    .page-description {
      font-size: 12.5px;
      color: var(--text-muted);
      max-width: 460px;
      margin-bottom: 14px;
      line-height: 1.4;
    }

    /* What Happens Next Card */
    .steps-card {
      width: 100%;
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: var(--radius-lg);
      padding: 14px 18px;
      margin-bottom: 10px;
      box-shadow: var(--shadow-sm);
      text-align: left;
    }

    .steps-card-title {
      font-size: 13.5px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 12px;
    }

    .steps-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      position: relative;
    }

    /* Connecting dotted line */
    .steps-grid::before {
      content: '';
      position: absolute;
      top: 19px;
      left: 12%;
      right: 12%;
      border-top: 2px dashed #cbd5e1;
      z-index: 1;
    }

    .step-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      position: relative;
      z-index: 2;
    }

    .step-icon-wrapper {
      position: relative;
      width: 38px;
      height: 38px;
      margin-bottom: 6px;
    }

    .step-icon-circle {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: var(--primary-light);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--primary-border);
    }

    .step-number {
      position: absolute;
      top: -2px;
      right: -2px;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      background: var(--primary);
      color: #ffffff;
      font-size: 9px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid #ffffff;
    }

    .step-title {
      font-size: 11.5px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 2px;
    }

    .step-desc {
      font-size: 10px;
      color: var(--text-muted);
      line-height: 1.25;
    }

    /* Workspace Status Card */
    .status-card {
      width: 100%;
      background: #f0f5ff;
      border: 1px solid #dbeafe;
      border-radius: var(--radius-lg);
      padding: 10px 16px;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      text-align: left;
    }

    .status-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .status-clock-icon {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: #fef3c7;
      color: #d97706;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .status-title {
      font-size: 12.5px;
      font-weight: 700;
      color: var(--text-dark);
    }

    .status-sub {
      font-size: 11px;
      color: var(--text-muted);
    }

    .status-right {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      text-align: right;
      gap: 2px;
    }

    .status-info-text {
      font-size: 10.5px;
      color: var(--text-muted);
      max-width: 270px;
      line-height: 1.25;
    }

    .status-live-indicator {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      color: #16a34a;
      font-size: 11px;
      font-weight: 600;
    }

    .pulse-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: #22c55e;
      box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
      animation: pulse-ring 1.8s infinite;
    }

    @keyframes pulse-ring {
      0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
      }
      70% {
        transform: scale(1);
        box-shadow: 0 0 0 5px rgba(34, 197, 94, 0);
      }
      100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
      }
    }

    /* Action Buttons Row */
    .actions-grid {
      width: 100%;
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      margin-bottom: 0;
    }

    .action-card {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: var(--radius-md);
      padding: 10px 10px;
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      color: var(--text-dark);
      transition: all 0.2s ease;
      cursor: pointer;
      box-shadow: var(--shadow-sm);
    }

    .action-card:hover {
      border-color: var(--primary);
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }

    .action-icon {
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .action-text {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      text-align: left;
      overflow: hidden;
    }

    .action-title {
      font-size: 11.5px;
      font-weight: 700;
      color: var(--text-dark);
      line-height: 1.2;
    }

    .action-subtitle {
      font-size: 9.5px;
      color: var(--text-muted);
      margin-top: 1px;
    }

    .resend-action .action-title {
      color: var(--primary);
    }

    /* Bottom Features Grid */
    .features-card {
      width: 100%;
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: var(--radius-lg);
      padding: 12px 18px;
      margin-top: 8px;
      box-shadow: var(--shadow-sm);
      flex-shrink: 0;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
    }

    .feature-item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      text-align: left;
    }

    .feature-icon-circle {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: var(--primary-light);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .feature-content {
      display: flex;
      flex-direction: column;
    }

    .feature-title {
      font-size: 11.5px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 1px;
    }

    .feature-desc {
      font-size: 10px;
      color: var(--text-muted);
      line-height: 1.25;
    }

    /* Footer */
    .footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 8px;
      padding-bottom: 2px;
      border-top: 1px solid var(--card-border);
      margin-top: 8px;
      flex-shrink: 0;
    }

    .copyright {
      font-size: 11.5px;
      color: var(--text-muted);
    }

    .footer-links {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .footer-link {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 11.5px;
      color: var(--text-muted);
      text-decoration: none;
      transition: color 0.2s;
    }

    .footer-link:hover {
      color: var(--primary);
    }

    /* Hidden element for original resend section hook */
    .countdownDisplaySection {
      display: none;
    }

    /* Responsive Breakdown for Tablet & Mobile */
    @media (max-width: 1024px) {
      html, body {
        height: auto;
        max-height: none;
        overflow-y: auto;
      }
      .page-container {
        height: auto;
        max-height: none;
        overflow: visible;
        padding: 20px;
      }
      .hero-layout {
        grid-template-columns: 1fr;
        gap: 24px;
        padding: 12px 0;
      }
      .illustration-col {
        max-width: 340px;
        margin: 0 auto;
      }
      .actions-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      .features-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 640px) {
      .page-container {
        padding: 14px;
      }
      .steps-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
      }
      .steps-grid::before {
        display: none;
      }
      .status-card {
        flex-direction: column;
        align-items: flex-start;
      }
      .status-right {
        align-items: flex-start;
        text-align: left;
      }
      .actions-grid {
        grid-template-columns: 1fr;
      }
      .features-grid {
        grid-template-columns: 1fr;
      }
      .footer {
        flex-direction: column;
        gap: 8px;
        text-align: center;
      }
    }
  </style>
</head>
<body>

  <div class="page-container">

    <!-- ═══════════ HEADER ═══════════ -->
    <header class="header">
      <a href="https://jero.works/" class="brand-logo">
        <svg class="brand-logo-icon" viewBox="0 0 40 44" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M20 2C9.5 2 1 10.5 1 21H39C39 10.5 30.5 2 20 2Z" fill="#111827"/>
          <path d="M1 21C1 21 7.5 17.5 20 21" stroke="rgba(255,255,255,0.35)" stroke-width="1.2"/>
          <path d="M39 21C39 21 32.5 17.5 20 21" stroke="rgba(255,255,255,0.35)" stroke-width="1.2"/>
          <line x1="20" y1="21" x2="20" y2="37" stroke="#111827" stroke-width="2.6" stroke-linecap="round"/>
          <path d="M20 37C20 40.31 17.31 43 14 43" stroke="#111827" stroke-width="2.6" stroke-linecap="round" fill="none"/>
        </svg>
        <div>
          <span class="brand-title">Jero</span>
          <span class="brand-subtitle">AI Recruitment Platform</span>
        </div>
      </a>

      <a href="mailto:support@jero.works" class="header-help">
        <span class="help-icon-circle">?</span>
        <span>Need help?</span>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </a>
    </header>

    <!-- ═══════════ MAIN HERO LAYOUT ═══════════ -->
    <div class="hero-layout">
      
      <!-- LEFT COLUMN: 3D GLASS ILLUSTRATION -->
      <div class="illustration-col">
        <div class="illustration-wrapper">
          <svg class="illustration-svg" viewBox="0 0 500 420" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="bgGlow" x1="0" y1="0" x2="500" y2="420" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#eff6ff" stop-opacity="0.9"/>
                <stop offset="100%" stop-color="#dbeafe" stop-opacity="0.3"/>
              </linearGradient>
              <linearGradient id="envGrad" x1="50" y1="180" x2="250" y2="360" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#3b82f6"/>
                <stop offset="100%" stop-color="#1d4ed8"/>
              </linearGradient>
              <linearGradient id="cardGrad" x1="60" y1="70" x2="380" y2="280" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#ffffff"/>
                <stop offset="100%" stop-color="#f8fafc"/>
              </linearGradient>
              <filter id="shadowSoft" x="10" y="20" width="480" height="390" filterUnits="userSpaceOnUse">
                <feDropShadow dx="0" dy="14" stdDeviation="18" flood-color="#1e3a8a" flood-opacity="0.08"/>
              </filter>
            </defs>

            <!-- Background Glow Shape -->
            <ellipse cx="230" cy="220" rx="200" ry="160" fill="url(#bgGlow)" />

            <!-- Sparkle Stars -->
            <path d="M 400 90 L 403 98 L 411 101 L 403 104 L 400 112 L 397 104 L 389 101 L 397 98 Z" fill="#93c5fd" />
            <path d="M 70 140 L 72 145 L 77 147 L 72 149 L 70 154 L 68 149 L 63 147 L 68 145 Z" fill="#60a5fa" />
            <path d="M 330 330 L 332 334 L 336 336 L 332 338 L 330 342 L 328 338 L 324 336 L 328 334 Z" fill="#93c5fd" />

            <!-- Main Dashboard Window Mockup -->
            <g filter="url(#shadowSoft)">
              <rect x="70" y="60" width="310" height="210" rx="14" fill="url(#cardGrad)" stroke="#e2e8f0" stroke-width="1.5" />
              <!-- Window dots -->
              <circle cx="88" cy="76" r="4" fill="#ef4444" />
              <circle cx="100" cy="76" r="4" fill="#f59e0b" />
              <circle cx="112" cy="76" r="4" fill="#10b981" />
              <line x1="70" y1="90" x2="380" y2="90" stroke="#f1f5f9" stroke-width="1.5" />

              <!-- Left Sidebar -->
              <rect x="70" y="90" width="45" height="180" fill="#f8fafc" rx="0" />
              <!-- Umbrella logo mini -->
              <path d="M 92 102 C 86 102 82 106 82 110 L 102 110 C 102 106 98 102 92 102 Z" fill="#1e293b" />
              <line x1="92" y1="110" x2="92" y2="118" stroke="#1e293b" stroke-width="1.5" stroke-linecap="round" />

              <!-- Sidebar items -->
              <rect x="80" y="130" width="25" height="4" rx="2" fill="#cbd5e1" />
              <rect x="80" y="145" width="25" height="4" rx="2" fill="#cbd5e1" />
              <rect x="80" y="160" width="25" height="4" rx="2" fill="#cbd5e1" />
              <rect x="80" y="175" width="25" height="4" rx="2" fill="#cbd5e1" />

              <!-- Stats Cards -->
              <rect x="130" y="105" width="65" height="45" rx="8" fill="#eff6ff" stroke="#dbeafe" stroke-width="1" />
              <circle cx="145" cy="120" r="7" fill="#3b82f6" opacity="0.2" />
              <circle cx="145" cy="118" r="3" fill="#2563eb" />
              <path d="M 140 126 C 140 123 142 122 145 122 C 148 122 150 123 150 126" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" fill="none" />
              <text x="162" y="132" font-family="Poppins" font-size="14" font-weight="700" fill="#1e293b">24</text>

              <rect x="205" y="105" width="65" height="45" rx="8" fill="#eff6ff" stroke="#dbeafe" stroke-width="1" />
              <circle cx="220" cy="120" r="7" fill="#3b82f6" opacity="0.2" />
              <circle cx="220" cy="118" r="3" fill="#2563eb" />
              <path d="M 215 126 C 215 123 217 122 220 122 C 223 122 225 123 225 126" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" fill="none" />
              <text x="237" y="132" font-family="Poppins" font-size="14" font-weight="700" fill="#1e293b">18</text>

              <rect x="280" y="105" width="65" height="45" rx="8" fill="#eff6ff" stroke="#dbeafe" stroke-width="1" />
              <circle cx="295" cy="120" r="7" fill="#3b82f6" opacity="0.2" />
              <circle cx="295" cy="118" r="3" fill="#2563eb" />
              <path d="M 290 126 C 290 123 292 122 295 122 C 298 122 300 123 300 126" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" fill="none" />
              <text x="312" y="132" font-family="Poppins" font-size="14" font-weight="700" fill="#1e293b">12</text>

              <!-- Chart Area -->
              <rect x="130" y="165" width="215" height="85" rx="8" fill="#ffffff" stroke="#f1f5f9" stroke-width="1" />
              <rect x="145" y="210" width="12" height="30" rx="3" fill="#93c5fd" />
              <rect x="165" y="195" width="12" height="45" rx="3" fill="#3b82f6" />
              <rect x="185" y="220" width="12" height="20" rx="3" fill="#93c5fd" />
              <rect x="205" y="185" width="12" height="55" rx="3" fill="#2563eb" />
              <rect x="225" y="200" width="12" height="40" rx="3" fill="#60a5fa" />
              <path d="M 255 225 Q 275 190 295 210 T 330 180" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" />
            </g>

            <!-- Floating Paper Airplane -->
            <g transform="translate(310, 75) rotate(-10)">
              <path d="M 0 25 L 45 0 L 20 40 L 15 27 Z" fill="#60a5fa" />
              <path d="M 45 0 L 15 27 L 0 25 Z" fill="#93c5fd" />
              <path d="M 15 27 L 20 40 L 26 30 Z" fill="#2563eb" />
              <path d="M -15 32 Q -5 32 0 25" stroke="#93c5fd" stroke-width="1.5" stroke-dasharray="3 3" fill="none" />
            </g>

            <!-- Floating 3D Open Blue Envelope -->
            <g filter="url(#shadowSoft)">
              <rect x="90" y="160" width="190" height="130" rx="12" fill="#ffffff" stroke="#dbeafe" stroke-width="1.5" />
              <rect x="115" y="180" width="140" height="8" rx="4" fill="#e2e8f0" />
              <rect x="115" y="196" width="100" height="6" rx="3" fill="#f1f5f9" />
              <circle cx="185" cy="230" r="22" fill="#2563eb" />
              <path d="M 176 230 L 182 236 L 194 224" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" fill="none" />
              <path d="M 80 205 L 80 320 C 80 328 86 334 94 334 L 276 334 C 284 334 290 328 290 320 L 290 205 L 185 275 Z" fill="url(#envGrad)" />
              <path d="M 80 205 L 185 275 L 290 205 L 290 320 C 290 328 284 334 276 334 L 94 334 C 86 334 80 328 80 320 Z" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" />
            </g>

            <!-- Floating Shield Check Badge -->
            <g transform="translate(325, 260)" filter="url(#shadowSoft)">
              <rect x="0" y="0" width="55" height="62" rx="14" fill="#ffffff" stroke="#e2e8f0" stroke-width="1" />
              <path d="M 27.5 12 C 27.5 12 38 15 42 17 C 42 32 36 42 27.5 48 C 19 42 13 32 13 17 C 17 15 27.5 12 27.5 12 Z" fill="#eff6ff" stroke="#22c55e" stroke-width="2" />
              <circle cx="27.5" cy="29" r="10" fill="#22c55e" />
              <path d="M 23.5 29 L 26 31.5 L 31.5 26" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
            </g>

          </svg>
        </div>
      </div>

      <!-- RIGHT COLUMN: VERIFICATION DETAILS & ACTIONS -->
      <div class="content-col">
        
        <!-- Top Icon Header -->
        <div class="top-icon-wrapper">
          <div class="top-decor-line"></div>
          <div class="top-blue-badge">
            <div class="top-blue-badge-inner">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="4" width="20" height="16" rx="3"></rect>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
              </svg>
            </div>
          </div>
          <div class="top-decor-line"></div>
        </div>

        <!-- Title -->
        <h1 class="page-title">Verify Your Email Address</h1>

        <!-- Email Display Sentence & Badge -->
        <p class="email-sent-text">We've sent a verification link to</p>
        <div class="email-badge">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="4" width="20" height="16" rx="2.5"></rect>
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
          </svg>
          <span><?php echo isset($user) ? e($user->email) : e($email ?? 'your email'); ?></span>
        </div>

        <!-- Description -->
        <p class="page-description">
          Please check your inbox and click the verification link to activate your workspace.
        </p>

        <!-- ═══════════ WHAT HAPPENS NEXT CARD ═══════════ -->
        <div class="steps-card">
          <div class="steps-card-title">What happens next?</div>
          <div class="steps-grid">
            
            <!-- Step 1 -->
            <div class="step-item">
              <div class="step-icon-wrapper">
                <div class="step-icon-circle">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                  </svg>
                </div>
                <span class="step-number">1</span>
              </div>
              <div class="step-title">Check your inbox</div>
              <div class="step-desc">Look for an email from Jero.</div>
            </div>

            <!-- Step 2 -->
            <div class="step-item">
              <div class="step-icon-wrapper">
                <div class="step-icon-circle">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 3 7.07 16.97 2.51-7.39 7.39-2.51L3 3z"></path>
                    <path d="m13 13 6 6"></path>
                  </svg>
                </div>
                <span class="step-number">2</span>
              </div>
              <div class="step-title">Click the link</div>
              <div class="step-desc">Tap the verification link in the email.</div>
            </div>

            <!-- Step 3 -->
            <div class="step-item">
              <div class="step-icon-wrapper">
                <div class="step-icon-circle">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <path d="m9 12 2 2 4-4"></path>
                  </svg>
                </div>
                <span class="step-number">3</span>
              </div>
              <div class="step-title">Email verified</div>
              <div class="step-desc">Your account and workspace will be activated.</div>
            </div>

            <!-- Step 4 -->
            <div class="step-item">
              <div class="step-icon-wrapper">
                <div class="step-icon-circle">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path>
                    <path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-3.05 11a22.35 22.35 0 0 1-3.95 2z"></path>
                    <path d="M9 12H4.5s.55-3.03 2-4.5c1.47-1.47 4.5-2 4.5-2"></path>
                    <path d="M12 15v4.5s3.03-.55 4.5-2c1.47-1.47 2-4.5 2-4.5"></path>
                  </svg>
                </div>
                <span class="step-number">4</span>
              </div>
              <div class="step-title">Start using Jero</div>
              <div class="step-desc">You'll be redirected to your dashboard.</div>
            </div>

          </div>
        </div>

        <!-- ═══════════ WORKSPACE STATUS CARD ═══════════ -->
        <div class="status-card">
          <div class="status-left">
            <div class="status-clock-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div>
              <div class="status-title">Waiting for Email Verification</div>
              <div class="status-sub">Your workspace will be activated after verification.</div>
            </div>
          </div>

          <div class="status-right">
            <div class="status-info-text">
              We automatically check for verification every few seconds. Once verified, we'll redirect you to your dashboard.
            </div>
            <div class="status-live-indicator">
              <span class="pulse-dot"></span>
              <span>Auto-checking...</span>
            </div>
          </div>
        </div>

        <!-- ═══════════ ACTION BUTTONS GRID ═══════════ -->
        <div class="actions-grid">
          
          <!-- Open Gmail -->
          <a href="https://mail.google.com" target="_blank" class="action-card">
            <div class="action-icon">
              <svg width="22" height="22" viewBox="0 0 24 24">
                <path d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6z" fill="#ea4335" opacity="0.15"/>
                <path d="M20 18h-2V9.25L12 14 4 9.25V18H2V6c0-.72.38-1.35.94-1.7.56-.35 1.27-.32 1.8.08L12 10.15l7.26-5.77c.53-.4 1.24-.43 1.8-.08.56.35.94.98.94 1.7v12z" fill="#ea4335"/>
                <path d="M4 6l8 5.75L20 6" stroke="#4285f4" stroke-width="2" fill="none"/>
              </svg>
            </div>
            <div class="action-text">
              <span class="action-title">Open Gmail</span>
              <span class="action-subtitle">Go to your inbox</span>
            </div>
          </a>

          <!-- Open Outlook -->
          <a href="https://outlook.live.com" target="_blank" class="action-card">
            <div class="action-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                <rect x="2" y="4" width="20" height="16" rx="3" fill="#0078d4" opacity="0.15"/>
                <path d="M14 6h6c1.1 0 2 .9 2 2v8c0 1.1-.9 2-2 2h-6V6z" fill="#0078d4"/>
                <path d="M2 8c0-1.1.9-2 2-2h10v12H4c-1.1 0-2-.9-2-2V8z" fill="#106ebe"/>
                <circle cx="9" cy="12" r="3" fill="#ffffff"/>
                <text x="7.5" y="14" font-family="Poppins" font-size="7" font-weight="700" fill="#0078d4">O</text>
              </svg>
            </div>
            <div class="action-text">
              <span class="action-title">Open Outlook</span>
              <span class="action-subtitle">Go to your inbox</span>
            </div>
          </a>

          <!-- Resend Email (Preserving JS ID/rel hooks) -->
          <a href="javascript:void(0);" class="action-card resend-action Resend" id="startCountdown" rel="<?php echo isset($user) ? e($user->id) : e($user_id ?? ''); ?>">
            <div class="action-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1d61f2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21.5 2v6h-6"></path>
                <path d="M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path>
              </svg>
              <span id="loader">&nbsp;</span>
            </div>
            <div class="action-text">
              <span class="action-title">Resend Email</span>
              <span class="action-subtitle">
                Available in <span id="countdownDisplay">02:00</span>
              </span>
            </div>
          </a>

          <!-- Wrong Email? -->
          <a href="{{ route('register') }}" class="action-card">
            <div class="action-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1d61f2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
            </div>
            <div class="action-text">
              <span class="action-title">Wrong Email?</span>
              <span class="action-subtitle">Change email address</span>
            </div>
          </a>

        </div>

        <!-- Section hidden countdown section preserved for original script -->
        <div class="countdownDisplay countdownDisplaySection" style="display: none;">
          Wait for <span id="countdownDisplaySectionTimer"></span> to resend mail
        </div>

      </div>

    </div>

    <!-- ═══════════ BOTTOM FEATURES GRID ═══════════ -->
    <div class="features-card">
      <div class="features-grid">
        
        <!-- Feature 1 -->
        <div class="feature-item">
          <div class="feature-icon-circle">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
          </div>
          <div class="feature-content">
            <div class="feature-title">Secure & Private</div>
            <div class="feature-desc">Your data is encrypted and always protected.</div>
          </div>
        </div>

        <!-- Feature 2 -->
        <div class="feature-item">
          <div class="feature-icon-circle">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
          </div>
          <div class="feature-content">
            <div class="feature-title">Account Protection</div>
            <div class="feature-desc">Email verification helps keep your account secure.</div>
          </div>
        </div>

        <!-- Feature 3 -->
        <div class="feature-item">
          <div class="feature-icon-circle">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
              <line x1="9" y1="6" x2="15" y2="6"></line>
              <line x1="9" y1="10" x2="15" y2="10"></line>
              <line x1="9" y1="14" x2="15" y2="14"></line>
              <line x1="9" y1="18" x2="15" y2="18"></line>
            </svg>
          </div>
          <div class="feature-content">
            <div class="feature-title">Workspace Activation</div>
            <div class="feature-desc">Verify your email to activate your Jero workspace.</div>
          </div>
        </div>

        <!-- Feature 4 -->
        <div class="feature-item">
          <div class="feature-icon-circle">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
          </div>
          <div class="feature-content">
            <div class="feature-title">Need Help?</div>
            <div class="feature-desc">Our support team is here to help you.</div>
          </div>
        </div>

      </div>
    </div>

    <!-- ═══════════ FOOTER ═══════════ -->
    <footer class="footer">
      <div class="copyright">
        © {{ date('Y') }} Jero. All rights reserved.
      </div>
      <div class="footer-links">
        <a href="javascript:void(0);" class="footer-link">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
          </svg>
          <span>Live Chat</span>
        </a>
        <a href="https://jero.works/" class="footer-link" target="_blank">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
          </svg>
          <span>Help Center</span>
        </a>
        <a href="mailto:support@jero.works" class="footer-link">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
            <polyline points="22,6 12,13 2,6"></polyline>
          </svg>
          <span>support@jero.works</span>
        </a>
      </div>
    </footer>

  </div>

  <!-- ═══════════ PRESERVED JS LOGIC ═══════════ -->
  <script>
    (function() {
      // Ensure resend functionality works when view is rendered standalone or dynamically via AJAX
      function initResendScript() {
        var countdownDisplaySection = document.querySelector('.countdownDisplaySection');
        if (countdownDisplaySection) {
          countdownDisplaySection.style.display = 'none';
        }
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initResendScript);
      } else {
        initResendScript();
      }

      // Delegate click handler so dynamically loaded view in AJAX containers binds correctly
      $(document).off('click', '#startCountdown').on('click', '#startCountdown', function(e) {
        e.preventDefault();
        var resendBtn = $(this);
        var id = resendBtn.attr('rel') || '<?php echo isset($user->id) ? $user->id : (isset($user) ? $user->id : ($user_id ?? '')); ?>';
        
        if (!id) {
          console.error('Resend error: User ID missing');
          return;
        }

        var url = "{{ url('/resend') }}/" + id;
        let timeLeft = 120; // 2 minutes in seconds
        var countdownDisplay = $('#countdownDisplay');
        var loader = $('#loader');

        resendBtn.css({ 'pointer-events': 'none', 'opacity': '0.5' });

        var baseurl = "{{ asset('assets/images/AjaxLoader.gif') }}"; 
        loader.html('<img src="' + baseurl + '" height="16px" width="16px" style="vertical-align:middle; margin-left:4px;">');

        $.ajax({
          type: 'GET',
          url: url,
          success: function(response) {
            loader.html('');
            if (response.status === 'success' || response.status === 'true' || response.success) {	
              var countdownInterval = setInterval(function() {
                var minutes = Math.floor(timeLeft / 60);
                var seconds = timeLeft % 60;
                var formattedTime = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;

                countdownDisplay.text(formattedTime);

                timeLeft--;

                if (timeLeft < 0) {
                  clearInterval(countdownInterval);
                  countdownDisplay.text('02:00');
                  resendBtn.css({ 'pointer-events': 'auto', 'opacity': '1' });
                }
              }, 1000);

              if (typeof Swal !== 'undefined') {
                Swal.fire("Success!", "Account activation link has been sent successfully.", "success");
              } else {
                alert("Account activation link has been sent successfully.");
              }
            } else {
              resendBtn.css({ 'pointer-events': 'auto', 'opacity': '1' });
              if (typeof Swal !== 'undefined') {
                Swal.fire("Error!", response.message || "Failed to resend activation link.", "error");
              } else {
                alert("Failed to resend activation link.");
              }
            }
          },
          error: function() {
            loader.html('');
            resendBtn.css({ 'pointer-events': 'auto', 'opacity': '1' });
            if (typeof Swal !== 'undefined') {
              Swal.fire("Error!", "An error occurred while resending the email.", "error");
            } else {
              alert("An error occurred while resending the email.");
            }
          }
        });
      });
    })();
  </script>
</body>
</html>