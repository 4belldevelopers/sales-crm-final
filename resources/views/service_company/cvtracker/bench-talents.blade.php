@php
    use App\Helper\GeneralMethod;

    /**
     * Intent: Fully Dynamic Data Extraction for Bench Recommended Talents Drawer Hero Section (Zero static fallbacks).
     * Tenancy Note: $job is tenant-scoped from ServiceCompanyJobsController@btlisting.
     */
    $bHeroTitle = @$job->title ?? '';
    $bHeroInitial = $bHeroTitle ? strtoupper(substr($bHeroTitle, 0, 1)) : '';
    $bHeroEmpType = isset($job->employment_type) ? (($job->employment_type == 1) ? 'Contract' : 'Full-time') : '';
    $bHeroLoc = !empty($default_loc) ? $default_loc : '';

    $bHeroExpStr = '';
    if (isset($job->min_exp) || isset($job->max_exp)) {
        $minExpVal = $job->min_exp ?? 0;
        $maxExpVal = $job->max_exp ?? 0;
        $bHeroExpStr = "{$minExpVal}–{$maxExpVal} Years";
    }

    // Salary formatting — strictly dynamic based on $job attributes
    $bMinSal = [
        0 => @$job->min_yearly,
        1 => @$job->min_monthly,
        2 => @$job->min_hourly
    ][@$job->min_sal_mode] ?? @$job->min_salary;

    $bMaxSal = [
        0 => @$job->max_yearly,
        1 => @$job->max_monthly,
        2 => @$job->max_hourly
    ][@$job->max_sal_mode] ?? @$job->max_salary;

    $bHeroSalStr = '';
    if (($bMinSal && $bMinSal > 0) || ($bMaxSal && $bMaxSal > 0)) {
        if ($bMinSal >= 100000 || $bMaxSal >= 100000) {
            $minLpa = $bMinSal > 0 ? number_format($bMinSal / 100000, 2) : '0.00';
            $maxLpa = $bMaxSal > 0 ? number_format($bMaxSal / 100000, 2) : '0.00';
            $bHeroSalStr = "₹{$minLpa} – ₹{$maxLpa} LPA";
        } elseif ($bMinSal > 0 && $bMaxSal > 0) {
            $bHeroSalStr = "₹" . number_format($bMinSal) . " – ₹" . number_format($bMaxSal);
        } elseif ($bMinSal > 0) {
            $bHeroSalStr = "₹" . number_format($bMinSal);
        } elseif ($bMaxSal > 0) {
            $bHeroSalStr = "₹" . number_format($bMaxSal);
        }
    }

    $bHeroNoticeStr = @$job->notice_period ?? 'Immediate – 30 Days';
    $bHeroJobId = @$job->id;
@endphp
<style>
.text-justify{text-align: justify!important;}
.symbol1 > img,
.symbol1 .symbol-label1{width: 32px!important; height: 32px!important;}
.table > :not(caption) > * > *{padding: 0rem 0.4rem;}

/* ── Bench Talents drawer — matches Inhouse Talents design language ── */
.bench-bt, .bench-bt * { font-family: 'Poppins', sans-serif !important; font-size: 13px; }
.bench-bt .table thead tr th {
    font-size: 11px !important; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.4px; color: #a1a5b7; padding: 8px 6px !important;
    border-bottom: 2px solid #f1f3f8; white-space: nowrap;
}
.bench-bt .table tbody tr { border-bottom: 1px solid #f1f3f8; }
.bench-bt .table tbody tr:hover { background: #fafbff; }
.bench-bt .table tbody td { padding: 8px 6px !important; vertical-align: top; }
.bench-bt .table .fs-6 { font-size: 13px !important; }
.bench-bt .table .fs-7 { font-size: 11px !important; }

/* Skill chips in the footer strip */
.bench-bt .skill { font-size: 10px !important; font-weight: 300 !important; }

/* ══════════════════════════════════════════════════════════════
   Bench Recommended Talents Hero Section Styles (btr-hero-*)
   ══════════════════════════════════════════════════════════════ */
.btr-hero-container {
    font-family: 'Poppins', sans-serif !important;
    margin-bottom: 18px;
}

/* Top Job Details Bar */
.btr-hero-card {
    background: #ffffff;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 14px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.btr-hero-left {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}

.btr-hero-avatar {
    width: 46px;
    height: 46px;
    border-radius: 10px;
    background: #FEF3C7;
    color: #D97706;
    font-weight: 700;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.btr-hero-title-block {
    min-width: 0;
}

.btr-hero-title {
    font-size: 16px;
    font-weight: 700;
    color: #1E293B;
    line-height: 1.3;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.btr-hero-sub {
    font-size: 13px;
    color: #64748B;
    font-weight: 400;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.btr-hero-sub-dot {
    display: inline-block;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: #94A3B8;
}

/* Stat items & vertical dividers */
.btr-hero-right {
    display: flex;
    align-items: center;
    gap: 0;
    flex-shrink: 0;
}

.btr-hero-divider {
    width: 1px;
    height: 36px;
    background: #E2E8F0;
    margin: 0 18px;
}

.btr-hero-stat-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btr-hero-stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #F1F5F9;
    color: #64748B;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.btr-hero-stat-icon svg {
    width: 18px;
    height: 18px;
}

.btr-hero-stat-label {
    font-size: 11px;
    color: #64748B;
    font-weight: 500;
    line-height: 1.2;
    margin-bottom: 2px;
}

.btr-hero-stat-value {
    font-size: 13px;
    color: #1E293B;
    font-weight: 600;
    line-height: 1.2;
    white-space: nowrap;
}

.btr-hero-btn-details {
    background: #FFFFFF;
    border: 1px solid #CBD5E1;
    color: #2563EB;
    font-size: 13px;
    font-weight: 600;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.15s ease;
    font-family: 'Poppins', sans-serif !important;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-left: 10px;
}

.btr-hero-btn-details:hover {
    background: #EFF6FF;
    border-color: #93C5FD;
    color: #1D4ED8;
}

/* Match Summary Cards Grid */
.btr-summary-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
}

.btr-summary-card {
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    min-width: 0;
}

.btr-summary-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.btr-card-blue {
    background: #F8F9FF;
    border: 1px solid #E0E7FF;
}
.btr-card-green {
    background: #F0FDF4;
    border: 1px solid #DCFCE7;
}
.btr-card-cyan {
    background: #EFF6FF;
    border: 1px solid #DBEAFE;
}
.btr-card-orange {
    background: #FFF7ED;
    border: 1px solid #FFEDD5;
}
.btr-card-purple {
    background: #FAF5FF;
    border: 1px solid #F3E8FF;
}

.btr-card-num {
    font-size: 24px;
    font-weight: 700;
    line-height: 1.1;
    margin-bottom: 2px;
}

.btr-num-blue { color: #3B82F6; }
.btr-num-green { color: #16A34A; }
.btr-num-cyan { color: #2563EB; }
.btr-num-orange { color: #EA580C; }
.btr-num-purple { color: #9333EA; }

.btr-card-label {
    font-size: 13px;
    font-weight: 600;
    color: #1E293B;
    margin-bottom: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.btr-card-sub {
    font-size: 11px;
    color: #64748B;
    white-space: nowrap;
}

.btr-card-icon-badge {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-left: 8px;
}

.btr-card-icon-badge svg {
    width: 20px;
    height: 20px;
}

.btr-badge-blue { background: #EEF2FF; color: #3B82F6; }
.btr-badge-green { background: #DCFCE7; color: #16A34A; }
.btr-badge-cyan { background: #DBEAFE; color: #2563EB; }
.btr-badge-orange { background: #FFEDD5; color: #EA580C; }
.btr-badge-purple { background: #F3E8FF; color: #9333EA; }

/* Responsive break points */
@media (max-width: 1200px) {
    .btr-summary-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 900px) {
    .btr-hero-card {
        flex-wrap: wrap;
    }
    .btr-hero-right {
        width: 100%;
        justify-content: space-between;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #F1F5F9;
    }
    .btr-summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 600px) {
    .btr-summary-grid {
        grid-template-columns: 1fr;
    }
    .btr-hero-divider {
        display: none;
    }
    .btr-hero-right {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
}
</style>
<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <input type="hidden" name="hid_job_id" id="hid_job_id" value="" />
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
 
	
<div class="post d-flex flex-column-fluid" id="kt_post">
  <div id="kt_content_container" class="container-fluid px-3">
    <div class="card">
      <div class="card-body pt-3 px-5">

        {{-- ── Bench Recommended Talents Hero & Match Summary Cards ── --}}
        <div class="btr-hero-container">
          <!-- Header Title Block with Shifted Close Button -->
          <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
              <div class="d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 10px; background: #EEF2FF; color: #4F46E5; flex-shrink: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 2C12 2 13.5 7.5 17 9C13.5 10.5 12 16 12 16C12 16 10.5 10.5 7 9C10.5 7.5 12 2 12 2Z" fill="#4F46E5"/>
                  <path d="M19 1C19 1 19.6 3.4 21 4C19.6 4.6 19 7 19 7C19 7 18.4 4.6 17 4C18.4 3.4 19 1 19 1Z" fill="#6366F1"/>
                  <path d="M5 16C5 16 5.5 18 7 18.5C5.5 19 5 21 5 21C5 21 4.5 19 3 18.5C4.5 18 5 16 5 16Z" fill="#818CF8"/>
                </svg>
              </div>
              <div>
                <h3 style="font-size: 18px; font-weight: 700; color: #1E293B; margin: 0; line-height: 1.25; font-family: 'Poppins', sans-serif;">AI Recommended Bench Candidates</h3>
                <span style="font-size: 13px; color: #64748B; font-weight: 400; font-family: 'Poppins', sans-serif;">Top bench talents matching your job requirements</span>
              </div>
            </div>
            <!-- Shifted Close Button -->
            <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n2" onclick="$('#kt_activitiesBT_close').trigger('click');" style="cursor: pointer; background: transparent; border: none; font-size: 20px; color: #64748B; padding: 4px 8px;" title="Close">
              <span class="svg-icon svg-icon-1">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
                    <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
                  </g>
                </svg>
              </span>
            </button>
          </div>

          <!-- Top Job Details Bar -->
          <div class="btr-hero-card">
            <div class="btr-hero-left">
              <div class="btr-hero-avatar">{{ $bHeroInitial }}</div>
              <div class="btr-hero-title-block">
                <div class="btr-hero-title" title="{{ $bHeroTitle }}">{{ $bHeroTitle }}</div>
                <div class="btr-hero-sub">
                  <span>{{ $bHeroEmpType }}</span>
                  <span class="btr-hero-sub-dot"></span>
                  <span>{{ $bHeroLoc }}</span>
                </div>
              </div>
            </div>

            <div class="btr-hero-right">
              <div class="btr-hero-divider"></div>
              
              <!-- Experience -->
              <div class="btr-hero-stat-item">
                <div class="btr-hero-stat-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                  </svg>
                </div>
                <div>
                  <div class="btr-hero-stat-label">Experience</div>
                  <div class="btr-hero-stat-value">{{ $bHeroExpStr }}</div>
                </div>
              </div>

              <div class="btr-hero-divider"></div>

              <!-- CTC / Salary -->
              <div class="btr-hero-stat-item">
                <div class="btr-hero-stat-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 2l-2 2m-2-2l2 2m2 4l-4 4M3 11l8-8 10 10-8 8L3 11z"></path>
                    <circle cx="7.5" cy="7.5" r="1.5"></circle>
                  </svg>
                </div>
                <div>
                  <div class="btr-hero-stat-label">CTC</div>
                  <div class="btr-hero-stat-value">{{ $bHeroSalStr }}</div>
                </div>
              </div>

              <div class="btr-hero-divider"></div>

              <!-- Notice Period -->
              <div class="btr-hero-stat-item">
                <div class="btr-hero-stat-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                    <path d="m9 16 2 2 4-4"></path>
                  </svg>
                </div>
                <div>
                  <div class="btr-hero-stat-label">Notice Period</div>
                  <div class="btr-hero-stat-value">{{ $bHeroNoticeStr }}</div>
                </div>
              </div>

              <div class="btr-hero-divider"></div>

              <!-- View Job Details Action -->
              <button type="button" class="btn btr-hero-btn-details client-show-detail" data-row-id="{{ $bHeroJobId }}">
                View Job Details
              </button>
            </div>
          </div>

          <!-- 5 Match Summary Cards Row (Zero static initial numbers) -->
          <div class="btr-summary-grid">
            <!-- 1. Total Matches -->
            <div class="btr-summary-card btr-card-blue">
              <div>
                <div class="btr-card-num btr-num-blue" id="bHeroTotalMatches">—</div>
                <div class="btr-card-label">Total Matches</div>
                <div class="btr-card-sub" id="bHeroAgenciesSub">Across agencies</div>
              </div>
              <div class="btr-card-icon-badge btr-badge-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </div>
            </div>

            <!-- 2. Excellent Match -->
            <div class="btr-summary-card btr-card-green">
              <div>
                <div class="btr-card-num btr-num-green" id="bHeroExcellentMatches">—</div>
                <div class="btr-card-label">Excellent Match</div>
                <div class="btr-card-sub">90% and above</div>
              </div>
              <div class="btr-card-icon-badge btr-badge-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="8" r="6"></circle>
                  <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path>
                </svg>
              </div>
            </div>

            <!-- 3. Good Match -->
            <div class="btr-summary-card btr-card-cyan">
              <div>
                <div class="btr-card-num btr-num-cyan" id="bHeroGoodMatches">—</div>
                <div class="btr-card-label">Good Match</div>
                <div class="btr-card-sub">75% – 89%</div>
              </div>
              <div class="btr-card-icon-badge btr-badge-cyan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                  <path d="m9 12 2 2 4-4"></path>
                </svg>
              </div>
            </div>

            <!-- 4. Available Today -->
            <div class="btr-summary-card btr-card-orange">
              <div>
                <div class="btr-card-num btr-num-orange" id="bHeroAvailableToday">—</div>
                <div class="btr-card-label">Available Today</div>
                <div class="btr-card-sub">Immediate Joiners</div>
              </div>
              <div class="btr-card-icon-badge btr-badge-orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
              </div>
            </div>

            <!-- 5. Premium Agencies -->
            <div class="btr-summary-card btr-card-purple">
              <div>
                <div class="btr-card-num btr-num-purple" id="bHeroPremiumAgencies">—</div>
                <div class="btr-card-label">Premium Agencies</div>
                <div class="btr-card-sub">Gold & Verified</div>
              </div>
              <div class="btr-card-icon-badge btr-badge-purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="m2 4 3 12h14l3-12-6 7-4-5-4 5-6-7z"></path>
                  <path d="M5 20h14"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

<style>
.bench-filter-drawer-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 10000 !important;
    display: none;
}
.bench-filter-drawer-overlay.active {
    display: block !important;
}
.bench-filter-drawer-backdrop {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background: rgba(15, 23, 42, 0.45) !important;
    backdrop-filter: blur(2px) !important;
    opacity: 0 !important;
    transition: opacity 0.25s ease-in-out !important;
}
.bench-filter-drawer-overlay.active .bench-filter-drawer-backdrop {
    opacity: 1 !important;
}
.bench-filter-drawer-container {
    position: absolute !important;
    top: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 70vw !important;
    max-width: 70% !important;
    height: 100vh !important;
    background: #ffffff !important;
    box-shadow: -10px 0 35px rgba(0, 0, 0, 0.25) !important;
    display: flex !important;
    flex-direction: column !important;
    transform: translateX(100%) !important;
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
    z-index: 10001 !important;
    font-family: 'Poppins', sans-serif !important;
}
.bench-filter-drawer-overlay.active .bench-filter-drawer-container {
    transform: translateX(0) !important;
}
@media (max-width: 991px) {
    .bench-filter-drawer-container {
        width: 88vw;
        max-width: 88%;
    }
}
.bench-filter-drawer-header {
    padding: 16px 24px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    flex-shrink: 0;
}
.bench-filter-drawer-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #EEF2FF;
    color: #4F46E5;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.bench-filter-drawer-body {
    padding: 20px 24px;
    overflow-y: auto;
    flex: 1;
}
.bench-filter-drawer-footer {
    padding: 16px 24px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fafbfc;
    flex-shrink: 0;
}
.bench-filter-section {
    padding-bottom: 20px;
    margin-bottom: 20px;
    border-bottom: 1px solid #f1f5f9;
}
.bench-filter-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.bench-filter-label {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    color: #475569;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}
</style>

        <!-- Full-Width Search Input Bar & Right-Shifted Filter Button -->
        <div class="d-flex align-items-center justify-content-between gap-3 mb-4 mt-2 px-1 w-100">
            <!-- Search Input Field (Full Width) -->
            <div class="d-flex align-items-center position-relative flex-grow-1">
                <span class="svg-icon svg-icon-1 position-absolute ms-4 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                    </svg>
                </span>
                <input type="text" id="inputCopySearch" name="inputCopySearch" data-kt-user-table-filter="search" class="form-control form-control-solid ps-12 fs-6 w-100" style="padding: 0.75rem 1.25rem; border-radius: 8px;" placeholder="Search talents by name, skills, title, location..." />
            </div>

            <!-- Filter Button (Shifted Right) -->
            <div class="d-flex align-items-center flex-shrink-0">
                <button type="button" class="btn btn-light-primary fw-bolder px-5 py-3 d-inline-flex align-items-center gap-2" style="border-radius: 8px;" id="open_apptrackerfilterBench_btn">
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                        </svg>
                    </span>
                    <span>Filter</span>
                </button>
            </div>
        </div>

        <!--Start Side Drawer Filter (70% Width - Market Jobs Filter Design)-->
        <div id="apptrackerfilterBench" class="bench-filter-drawer-overlay" tabindex="-1">
            <div class="bench-filter-drawer-backdrop" id="apptrackerfilterBench_backdrop"></div>
            <div class="bench-filter-drawer-container">
                <!-- Header -->
                <div class="bench-filter-drawer-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bench-filter-drawer-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="fw-bolder m-0 text-gray-900 fs-4" style="font-family: 'Poppins', sans-serif;">Filter Bench Talents</h3>
                            <p class="text-muted m-0 fs-7" style="color: #64748B;">Filter AI recommended candidate pool by skills, city, experience, bill rates & type</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger" id="apptrackerfilterBench_close" title="Close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18M6 6l12 12"/>
                            </svg>
                        </span>
                    </button>
                </div>

                <!-- Body -->
                <div class="bench-filter-drawer-body">
                    <form id="createSchedule" class="ajax-form" method="post">
                        <!-- 1. Search Skills / Keywords -->
                        <div class="bench-filter-section">
                            <label class="bench-filter-label">1. Search Skills / Keywords</label> 
                            <div class="search-input-wrap">
                                <input class="search-input form-control form-control-solid" name="kt_tagify_skills" value="{{ @$default_skill }}" placeholder="Enter Skills to search Bench Talents..." id="kt_tagify_skills" />
                            </div>
                        </div>

                        <!-- 2. Select the City -->
                        <div class="bench-filter-section">
                            <label class="bench-filter-label">2. Select the City</label> 
                            <div class="search-input-wrap">
                                <input class="search-input form-control form-control-solid" value="" id="kt_tagify_location_bench" placeholder="Search City..." />
                            </div>
                        </div>

                        <!-- 3. Experience -->
                        <div class="bench-filter-section">
                            <label class="bench-filter-label">3. Experience</label> 
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label class="form-label required fs-7 text-muted fw-bold">Minimum years:</label>
                                    <select class="form-select form-select-solid" name="min_exp_bench" id="min_exp_bench">
                                        <option value="" selected="">Select Experience</option>
                                        <option value="0">Freshers</option>
                                        @for($i=1; $i<=30; $i++)
                                        <option value="{{ $i }}">{{ $i }} &nbsp; Year{{ ($i>=1) ? 's' : '' }}</option>
                                        @endfor                    
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label required fs-7 text-muted fw-bold">Maximum years:</label>
                                    <select class="form-select form-select-solid" name="max_exp_bench" id="max_exp_bench">
                                        <option value="" selected="">Select Experience</option>
                                        <option value="0">Freshers</option>
                                        @for($i=1; $i<=30; $i++)
                                        <option value="{{ $i }}">{{ $i }} &nbsp; Year{{ ($i>=1) ? 's' : '' }}</option>
                                        @endfor                                
                                    </select>
                                    <div id="experience_bench_error" style="color: red !important; text-align: center;" class="mt-1 fs-7"></div>
                                </div>
                            </div> 
                        </div>

                        <!-- 4. Bill Rates -->
                        <div class="bench-filter-section">
                            <label class="bench-filter-label">4. Bill Rates</label> 
                            <div class="row g-3 mb-3">
                                <div class="col-lg-6">
                                    <label class="form-label required fs-7 text-muted fw-bold">Currency</label>
                                    <select class="form-select form-select-solid" name="min_salary_currency_bench" id="min_salary_currency_bench">
                                        <option value="">Select currency</option>
                                        <option value="0" {{ $min_salary_currency==0 ? 'selected' : '' }}>INR</option>
                                        <option value="1" {{ $min_salary_currency==1 ? 'selected' : '' }}>EUR</option>
                                        <option value="2" {{ $min_salary_currency==2 ? 'selected' : '' }}>JPY</option>
                                        <option value="3" {{ $min_salary_currency==3 ? 'selected' : '' }}>GBP</option>
                                        <option value="4" {{ $min_salary_currency==4 ? 'selected' : '' }}>USD</option>
                                        <option value="5" {{ $min_salary_currency==5 ? 'selected' : '' }}>AED</option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label required fs-7 text-muted fw-bold">Mode</label>
                                    <select class="form-select form-select-solid" name="sal_mode_bench" id="sal_mode_bench">
                                        <option value="">Select Mode</option>
                                        <option value="0" {{ $min_sal_mode==0 ? 'selected' : '' }}>Annual</option>
                                        <option value="1" {{ $min_sal_mode==1 ? 'selected' : '' }}>Monthly</option>
                                        <option value="2" {{ $min_sal_mode==2 ? 'selected' : '' }}>Hourly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label class="form-label required fs-7 text-muted fw-bold">Minimum Bill Rates:</label>
                                    <input type="number" class="form-control form-control-solid" name="min_sal_bench" id="min_sal_bench" placeholder="Enter min salary" value="">
                                    <span class="form-text text-muted minimum_salary_word"></span>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label required fs-7 text-muted fw-bold">Maximum Bill Rates:</label>
                                    <input type="number" class="form-control form-control-solid" name="max_sal_bench" id="max_sal_bench" placeholder="Enter max salary" value="">
                                    <span class="form-text text-muted maximum_salary_word"></span>
                                </div>  
                                <span class="form-text text-muted" id="salary_bench_error" style="color: red !important; text-align: center;"></span>                                                             
                            </div>
                        </div>

                        <!-- 5. Outsource Type -->
                        <div class="bench-filter-section">
                            <label class="bench-filter-label">5. Outsource type</label> 
                            <div class="row g-3 py-2">
                                <div class="col-lg-4">
                                    <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource_bench" value="0" id="app_int_outsource_bench0" checked>
                                    <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 w-100 d-flex align-items-center justify-content-center" for="app_int_outsource_bench0">
                                        <span class="d-block fw-bold text-start">
                                            <span class="text-dark-800 fw-bolder d-block fs-6">All</span>
                                        </span>
                                    </label>
                                </div>
                                <div class="col-lg-4">
                                    <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource_bench" value="1" id="app_int_outsource_bench2">
                                    <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 w-100 d-flex align-items-center justify-content-center" for="app_int_outsource_bench2">
                                        <span class="d-block fw-bold text-start">
                                            <span class="text-dark-800 fw-bolder d-block fs-6">Fulltime Hiring</span>
                                        </span>
                                    </label>
                                </div>
                                <div class="col-lg-4">
                                    <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource_bench" value="2" id="app_int_outsource_bench3">
                                    <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 w-100 d-flex align-items-center justify-content-center" for="app_int_outsource_bench3">
                                        <span class="d-block fw-bold text-start">
                                            <span class="text-dark-800 fw-bolder d-block fs-6">Contract Hiring</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="bench-filter-drawer-footer">
                    <div class="d-flex align-items-center gap-2 text-muted fs-7">
                        <span>Refine your candidate pool</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-light btn-sm px-4" id="apptrackerfilterBench_cancel">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M18 6 6 18M6 6l12 12"/></svg>
                            Close
                        </button>
                        <button type="button" id="changetextupdatebench" class="btn btn-primary btn-sm px-5" style="background-color: #4F46E5; border-color: #4F46E5;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3Z"/></svg>
                            <span class="indicator-label changetextupdatebench">Apply Filters</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!--End Side Drawer Filter-->


        
        <div class="bench-bt">
        <table class="table align-middle table-row-dashed fs-6">
          <thead>
            <tr class="fw-bolder text-muted text-uppercase fs-8">
              <th class="w-25px pe-2">
                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                  <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#copyJobTable .form-check-input" value="1" />
                </div>
              </th>
              <th class="min-w-200px">TALENT</th>
              <th class="min-w-100px">EXPERIENCE</th>
              <th class="min-w-110px">AVAILABILITY</th>
              <th class="min-w-120px">LOCATION</th>
              <th class="min-w-130px">EXPECTED SALARY</th>
              <th class="min-w-120px">BENCH STATUS</th>
              <th class="min-w-150px">RELATIONSHIP MANAGER</th>
              <th class="text-end min-w-130px">ACTION</th>
            </tr>
          </thead>
          <tbody class="text-gray-600 fw-bold" id="copyJobTable"></tbody>
        </table>
        </div>
		 <div id="paginationcopy"></div>
      </div>
    </div>
  </div>
</div>
	
  </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>	

<script>
     setTimeout(() => {
         fetchCandidatesbench();
     }, 1000);
    
	 $(document).on('click', '#paginationcopy .page-link', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchCandidatesbench(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#paginationcopy .page-link').removeClass('disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#paginationcopy .page-link').removeClass('disabled');
                });
    });

	function fetchCandidatesbench(page = 1, search = '') {
            var clientid = '1';
            if($('#kt_tagify_2').val()!=''){
                // $('.loadingMessage').css({ display: 'block' });
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }
            $.ajax({
                url: `{!! route('service_company.bench.copybenchtable') !!}?page=${page}`,
                type: 'GET',
                data: {
                  search : $('#inputCopySearch').val(),
                  cus_search : $('#kt_tagify_skills').val(),
                  kt_tagify_location : $('#kt_tagify_location_bench').val(),
                  min_exp : $('#min_exp_bench').val(),
                  max_exp : $('#max_exp_bench').val(),
                  min_sal : $('#min_sal_bench').val(),
                  max_sal : $('#max_sal_bench').val(),
                  min_salary_currency : $('#min_salary_currency_bench').val(),
                  app_int_outsource : $('[name="app_int_outsource_bench"]:checked').val(),
                  sal_mode : $('#sal_mode_bench').val(),
				  currentJobId : '{{ @$currentJobId }}',
                  findTalentAdd : '{{ @$findTalentAdd }}'
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    if (response && response.html !== undefined) {
                        $('#copyJobTable').html(response.html);
                        $('#paginationcopy').html(response.pagination);
                        if (response.stats) {
                            $('#bHeroTotalMatches').text(response.stats.total_matches);
                            if (response.stats.agencies_count) {
                                $('#bHeroAgenciesSub').text('Across ' + response.stats.agencies_count + ' Agencies');
                            }
                            $('#bHeroExcellentMatches').text(response.stats.excellent_matches);
                            $('#bHeroGoodMatches').text(response.stats.good_matches);
                            $('#bHeroAvailableToday').text(response.stats.available_today);
                            $('#bHeroPremiumAgencies').text(response.stats.premium_agencies);
                        }
                    }
                    $('#apptrackerfilterBench_close').click();
                },
                error: function(xhr) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    $('#copyJobTable').html('<tr><td colspan="10" class="text-center text-muted py-4">Failed to load data. Please refresh and try again.</td></tr>');
                }
            });
    }

</script>
<script>	
   $(document).ready(function() {	

        


        $('#inputCopySearch').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                fetchCandidatesbench();
            }

        });

        $(document).on("click",".request-send", function(){
           
            var ele = $(this);
            var jobId  = $(this).attr('data-jobId');
            var conId  = $(this).attr('data-conId');
            var elm = $(this);
            
            swal({
                title: "Are you sure?",
                text: " Do you want to Submit Candidate request on this Job?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, I am sure!',
                cancelButtonText: "No, cancel it!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm){
                if (isConfirm){
                    var url   		=	"{{ route('service_company.jobs.recommandedrequestcondidate',':id') }}";
                    url     		=	url.replace(':id', jobId)+"?conId="+conId;
                    var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
                    $.ajax({
                                type  		: 'GET',
                                url   		: url,
                                success 	: function (response) {
                                    if(response.status=='success'){
                                    swal("Recommend!", "Candidates are successfully Recommend!", "success");
                                    var jobId  = ele.attr('data-jobId');
                                    var conId  = ele.attr('data-conId');
                                    var matlaccess = 1;
                                    var url   		=	"{{ route('service_company.jobs.mailrecommandedrequestcondidate',':id') }}";
                                    url     		=	url.replace(':id', jobId)+"?conId="+conId+"?matlaccess="+matlaccess;                  
                                    // tableLoad('load');
                                    fetchCandidatesbench()
                                    }else{
                                    fetchCandidatesbench();
                                    swal("Recommend Failed", response.message, "warning").then(function(){
                                        fetchCandidatesbench();
                                    });
                                    }
                                }
                        });

                } else {

                    swal("Cancelled", "", "error");

                }
            });
        });

        $(document).on("click", "#open_apptrackerfilterBench_btn", function(e){
            e.preventDefault();
            e.stopPropagation();
            if ($('#apptrackerfilterBench').length && $('#apptrackerfilterBench').parent()[0] !== document.body) {
                $('body').append($('#apptrackerfilterBench'));
            }
            $('#apptrackerfilterBench').addClass('active');
        });

        $(document).on("click", "#apptrackerfilterBench_close, #apptrackerfilterBench_cancel, #apptrackerfilterBench_backdrop", function(e){
            e.preventDefault();
            e.stopPropagation();
            $('#apptrackerfilterBench').removeClass('active');
        });

        $(document).on("click",".changetextupdatebench", function(e){
            e.preventDefault();
            e.stopPropagation();
            $('.changetextupdatebench').text('Searching....!');
            fetchCandidatesbench();
            $('#apptrackerfilterBench').removeClass('active');
            setTimeout(() => {
                $('.changetextupdatebench').text('Submit');
            }, 1000);
        });



        var inputElm = document.querySelector('#kt_tagify_skills');
        var tagifyUsers = new Tagify(inputElm, {
        maxTags: 20,
        dropdown: {
            maxItems: 20, // <- mixumum allowed rendered suggestions
            classname: "", // <- custom classname for this dropdown, so it could be targeted
            enabled: 0, // <- show suggestions on focus
            closeOnSelect: true // <- do not hide the suggestions dropdown once an item has been selected
        },
        templates: {
            tag: function(tagData) {
                tagData = tagData || {};
                var value = tagData.value || '';
                return `
                    <tag title="${value}" contenteditable='false' spellcheck="false"
                        class="tagify__tag ${tagData.class ? tagData.class : ""}"
                        ${this.getAttributes(tagData)}>
                        <div style="padding:2px 6px; display:flex; align-items:center; gap:6px;">
                            <span class="tagify__tag-text">${value}</span>
                            <span style="width:16px;height:16px;border-radius:50%;background:#A5B4FC; border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:11px;color:#3730A3;line-height:1;flex-shrink:0;">
                                <x title="" class="tagify__tag__removeBtn"role="button" aria-label="remove tag"></x>
                            </span>
                        </div>
                    </tag>
                `;
            }
        },

        transformTag: function(tagData) {
            tagData.checked = tagData.checked || false;
            return tagData;
        }
    });


    var inputloc = document.querySelector("#kt_tagify_location_bench");
    var cities = @json(@$cities);
    // Initialize Tagify script on the above inputs
    var tagifylocation = new Tagify(inputloc, {
        whitelist: cities,
        // enforceWhitelist: true, // Prevents users from adding custom values
        maxTags: 1,
        dropdown: {
            maxItems: 200, // <- mixumum allowed rendered suggestions
            classname: "", // <- custom classname for this dropdown, so it could be targeted
            enabled: 0, // <- show suggestions on focus
            closeOnSelect: true, // <- do not hide the suggestions dropdown once an item has been selected
            highlightFirst: true // First suggestion active
        },
        templates: {
            tag: function(tagData) {
                tagData = tagData || {};
                var value = tagData.value || '';
                return `
                    <tag title="${value}" contenteditable='false' spellcheck="false"
                        class="tagify__tag ${tagData.class ? tagData.class : ""}"
                        ${this.getAttributes(tagData)}>
                        <div style="padding:2px 6px; display:flex; align-items:center; gap:6px;">
                            <span class="tagify__tag-text">${value}</span>
                            <span style="width:16px;height:16px;border-radius:50%;background:#A5B4FC; border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:11px;color:#3730A3;line-height:1;flex-shrink:0;">
                                <x title="" class="tagify__tag__removeBtn"role="button" aria-label="remove tag"></x>
                            </span>
                        </div>
                    </tag>
                `;
            }
        },

        transformTag: function(tagData) {
            tagData.checked = tagData.checked || false;
            return tagData;
        }
    });
    // Prevent creating tags on blur or enter key
    tagifylocation.on('input', function(e) {
        tagifylocation.dropdown.show(); // show the dropdown immediately
    });
    // Create tags only on select
    tagifylocation.on('dropdown:select', function(e) {
        tagifylocation.addTags([e.detail.data.value]);
    });
    
    tagifylocation.on('add', function(e) {
        // alert(cities);
        // Remove the tag if it wasn't selected from the dropdown
        // if (!cities.includes(e.detail.data.value)) {
        //     setTimeout(() => {
        //         tagifylocation.removeTag(e.detail.data.value);
        //     }, 500);

        // }
    });

    function debounce(func, delay) {
            let timer;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }
        // Debounced function to fetch suggestions
        const fetchSuggestions_bench = debounce(function(query) {
            $.ajax({
                url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query,
                method: 'GET',
                success: function(data) {
                    console.warn("Fetched Cities: ", data);

                    if (Array.isArray(data)) {
                        tagifylocation.settings.whitelist = data; // Update whitelist
                        // tagifylocation.dropdown.show(query); // Show suggestions
                        tagifylocation.dropdown.show.call(tagifylocation);
                    } else {
                        console.error("Invalid API response format: Expected array.");
                    }
                },
                error: function(err) {
                    console.error("Error fetching cities:", err);
                }
            });
        }, 200); // 300ms delay

         // Listen for user input to trigger API call
    tagifylocation.on('input', function(e) {
        var query = e.detail.value.trim();
        if (query.length < 2) return;
        fetchSuggestions_bench(query);
    });

    setTimeout(() => {
      var city = ""; //{{ @$default_loc }}
      if(city !=''){
        var cityName = city.split(" | ")[0]; // Get the first part (Ballia)
        fetchSuggestions_bench(cityName);
      }
    }, 100);
    setTimeout(() => {
        var defaultLocation = ''; // {!! json_encode(@$default_loc) !!} Ensure proper Blade syntax for JavaScript
        if (defaultLocation) {
            tagifylocation.addTags([defaultLocation]); // Add the default location as a tag
            tagifylocation.dropdown.hide();
        }
    }, 2000); // 3 seconds delay



    document.addEventListener('click', function (e) {
            // remove button clicked
            if (e.target.classList.contains('tagify__tag__removeBtn')) {
                // get tag element
                const tagElm = e.target.closest('.tagify__tag');
                // remove tag from tagify
                tagifylocation.removeTags(tagElm);
                tagifyUsers.removeTags(tagElm);
            }
        });


   });

</script>

