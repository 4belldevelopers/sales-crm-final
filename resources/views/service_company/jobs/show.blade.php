<?php
use App\Helper\GeneralMethod;
use Carbon\Carbon;

 $currencyArr 				=	GeneralMethod::currency();
 $salaryMode 			=	GeneralMethod::salaryMode();
 $qualification  = GeneralMethod::qualification(@$job->qualification);
                                    
 $feedback_status = GeneralMethod::feedback_status();
 $available_status = GeneralMethod::available_status();
 
$employment_type = '';
$employment_type = $job->employment_type;
//if(!empty($employment_type)){
$employment_type = $employmentType[$job->employment_type];
//}
$bgTextDecore = $strVal = '';
$str = trim(@$job->client_name);
$str = $strVal = strtolower($str[0]);
$str = ord($str);
if ($str >= 97 && $str <= 101) {
    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
} elseif ($str >= 102 && $str <= 106) {
    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
} elseif ($str >= 107 && $str <= 111) {
    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
} elseif ($str >= 112 && $str <= 116) {
    $bgTextDecore = 'fs-3 bg-light-success text-success';
} elseif ($str >= 117 && $str <= 121) {
    $bgTextDecore = 'fs-3 bg-dark text-secondary';
} else {
    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
}

$background_image = '';
if (!empty(@$job->logo_logical_path) && strlen(@$job->logo_logical_path) > 10) {
    $background_image = url('public/' . @$job->logo_logical_path);
}

$currency = GeneralMethod::currencySymbole();
$max_salary = $min_salary = $max_sal_mode = $min_sal_mode = '';
$max_salary = GeneralMethod::thousandsCurrencyFormat($job->max_salary);
$min_salary = GeneralMethod::thousandsCurrencyFormat($job->min_salary);

  $min_sal_mode = strtoupper(substr($salaryMode[$job->min_sal_mode], 0, 1));
  $max_sal_mode = strtoupper(substr($salaryMode[$job->max_sal_mode], 0, 1));
$work_type = 'Work From Office';
 if($job->work_type=='no'){
     $work_type = "Work From Office";
 }elseif($job->work_type=='yes'){
     $work_type = "Remote";
 }elseif($job->work_type=='other'){
     $work_type = "Hybrid";
 }


$logos = GeneralMethod::companyDetails($job->id);

$logo_path = '';

$logo_path = url('public/user-uploads/company-logo/' . @$logos->logo);
if (!empty($logos->logo_logical_path)) {
    $logo_path = url('public/' . @$logos->logo_logical_path);
}
$logo_path = GeneralMethod::companyDetails($job->id);
$companyOrClientDetails = GeneralMethod::companyOrClientDetails($job->id);
// dd($companyOrClientDetails);
if(@$tabshow){
    $company_or_client = GeneralMethod::companyDetailsByClient($job->id);
    
    if($job->self_company==1){
      $description = ucfirst(@$job->company_description);
    }else{
        if(($job->comId ?? $job->company_id ?? null) == auth()->user()->company_id){
            $description = ucfirst(@$job->client_description);
        }else{
            $description = ucfirst(@$job->company_description);
        }
      
    }
}else{
    $company_or_client = GeneralMethod::companyDetailsByClient($job->id);
    $logo_path =  @$job->company_logo;
    // $company_or_client = @$company_name;
    $description = ucfirst(@$job->company_description);
}
// dd($company_or_client);

if(@$jobfindhide==false){
      $logo_path = '';
}


$end_date	=	@$job->end_date;
$date 		=	strtotime($end_date);
$end_date   =	date('d-M-Y', $date);
			
?>
  @php
      
        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(5);
        $endOfMonthcomman =  Carbon::now()->endOfMonth();
        
				$endDate = Carbon::today()->endOfDay();
				$startDate = Carbon::today()->subDays(6)->startOfDay();

  @endphp

<style>
.question-list .list-group-item {
    border: 0;
    border-bottom: 1px solid #f0f0f0;
}

.question-item:hover {
    background-color: #fafafa;
}
.question-item {
    width: 1044px;
}

.toggle-question:hover {
    background-color: #eef5ff;
    border-color: #cfe2ff;
}

.remove-question:hover {
    background-color: #fff0f0;
    border-color: #f5c2c7;
}

.question-input input {
    border-radius: 6px;
}

#kt_activitiesJob_close svg {
        color: red;
}

</style>

<style>
        :root {
            --primary: #1a73e8;
            --primary-dark: #1557b0;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #1a73e8;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
        }

        .jd-wrap * { box-sizing: border-box; }

        

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }



        .top-actions {
            display: flex; gap: 10px;
            padding: 16px 24px;
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-color);
            flex-wrap: wrap;
        }

        .action-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 16px;
            border: 1px solid var(--border-color);
            background: var(--bg-white);
            border-radius: var(--radius-sm);
            font-family: 'Poppins', sans-serif;
            font-size: 12px; font-weight: 500;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .action-btn:hover {
            background: var(--bg-light);
            border-color: var(--text-secondary);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .action-btn i { font-size: 13px; color: var(--text-secondary); }
        .action-btn.primary { background: var(--primary); color: #fff; border-color: var(--primary); }
        .action-btn.primary:hover { background: var(--primary-dark); }
        .action-btn.primary i { color: #fff; }

        .content-area { padding: 24px; max-width: 1400px; }

        .job-header-card {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .job-header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .job-header-left {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            flex: 1;
            min-width: 0;
        }

        .company-logo {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: #dc2626;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 15px; font-weight: 700;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .company-logo img {
            width: 72px; height: 72px;
            object-fit: cover;
            display: block;
        }

        .company-logo::after {
            content: '';
            position: absolute;
            bottom: 2px; right: 2px;
            width: 14px; height: 14px;
            background: var(--success);
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .job-title-section {
            flex: 1;
            min-width: 0;
        }

        .job-title-section h1 {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .status-badge {
            display: inline-flex;
            align-items: center; gap: 6px;
            padding: 4px 12px;
            background: #dcfce7;
            color: var(--success);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #bbf7d0;
        }

        .status-badge:hover { background: #bbf7d0; transform: scale(1.02); }
        .status-badge i { font-size: 10px; }

        .job-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            color: var(--text-secondary);
            font-size: 12px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .job-meta span { display: flex; align-items: center; gap: 6px; }
        .job-meta i { font-size: 13px; color: var(--text-muted); }

        .job-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .job-tag {
            padding: 4px 12px;
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-primary);
            transition: all 0.2s;
            cursor: pointer;
        }

        .job-tag:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        .job-tag.more { background: var(--primary); color: #fff; border-color: var(--primary); }

        .hero-personnel-inline {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
        }

        .personnel-card-inline {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: var(--bg-light);
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            transition: all 0.2s;
            cursor: pointer;
        }

        .personnel-card-inline:hover {
            box-shadow: var(--shadow-sm);
            border-color: var(--primary);
        }

        .personnel-avatar-inline {
            width: 36px; height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: var(--shadow-sm);
        }

        .personnel-info-inline {
            display: flex;
            flex-direction: column;
        }

        .personnel-role-inline {
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .personnel-name-inline {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .personnel-dept-inline {
            font-size: 10px;
            color: var(--text-secondary);
        }

        .personnel-actions-inline {
            display: flex;
            gap: 6px;
            margin-left: 6px;
        }

        .personnel-icon-btn-inline {
            width: 28px; height: 28px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-secondary);
            position: relative;
            font-size: 12px;
        }

        .personnel-icon-btn-inline:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            transform: scale(1.1);
        }

        .job-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 180px;
        }

        .job-action-btn {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-family: 'Poppins', sans-serif;
            font-size: 12px; font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid var(--border-color);
            background: var(--bg-white);
            color: var(--text-primary);
            white-space: nowrap;
        }

        .job-action-btn:hover {
            background: var(--bg-light);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .job-action-btn.edit { background: var(--primary); color: #fff; border-color: var(--primary); }
        .job-action-btn.edit:hover { background: var(--primary-dark); }
        .job-action-btn i { font-size: 13px; }

        .pipeline-section {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .pipeline-card {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: 16px;
            border: 1px solid var(--border-color);
            display: flex; align-items: center; gap: 12px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .pipeline-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
            border-color: var(--primary);
        }

        .pipeline-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .pipeline-icon.applied { background: #dbeafe; color: var(--primary); }
        .pipeline-icon.shortlisted { background: #fef3c7; color: var(--warning); }
        .pipeline-icon.interviewed { background: #ede9fe; color: #8b5cf6; }
        .pipeline-icon.selected { background: #d1fae5; color: var(--success); }
        .pipeline-icon.offer { background: #ffe4e6; color: #f43f5e; }
        .pipeline-icon.joined { background: #e0f2fe; color: #0ea5e9; }

        .pipeline-info { flex: 1; }
        .pipeline-label { font-size: 11px; color: var(--text-secondary); font-weight: 500; margin-bottom: 2px; }
        .pipeline-value {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex; align-items: center; gap: 6px;
        }

        .pipeline-change { font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 2px; }
        .pipeline-change.up { color: var(--success); }
        .pipeline-change.down { color: var(--danger); }

        .tabs-section {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .tabs-nav {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            overflow-x: auto;
            scrollbar-width: none;
        }

        .tabs-nav::-webkit-scrollbar { display: none; }

        .tab-btn {
            padding: 14px 20px;
            font-family: 'Poppins', sans-serif;
            font-size: 12px; font-weight: 500;
            color: var(--text-secondary);
            background: none; border: none;
            cursor: pointer;
            white-space: nowrap;
            position: relative;
            transition: all 0.2s;
        }

        .tab-btn:hover { color: var(--primary); background: rgba(26, 115, 232, 0.04); }
        .tab-btn.active { color: var(--primary); font-weight: 600; }
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: 0; left: 12px; right: 12px;
            height: 2px;
            background: var(--primary);
            border-radius: 2px 2px 0 0;
        }

        .jd-wrap .tabs-content { padding: 24px; }
        .jd-wrap .tab-panel { display: none; animation: fadeIn 0.3s ease; }
        .jd-wrap .tab-panel.active { display: block !important; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .jd-wrap .info-grid {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        .jd-wrap .info-card {
            display: block !important;
            background: var(--bg-white);
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            padding: 20px;
            transition: all 0.2s;
            position: relative;
            min-width: 0;
        }

        .jd-wrap .info-card:hover { box-shadow: var(--shadow-sm); }

        .jd-wrap .info-card-header {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .jd-wrap .info-card-title { font-size: 13px; font-weight: 600; color: var(--text-primary); }

        .jd-wrap .info-card-actions {
            display: flex !important;
            align-items: center;
            gap: 8px;
        }

        .info-card-edit {
            font-size: 11px; color: var(--primary); font-weight: 500;
            cursor: pointer; transition: all 0.2s;
            background: none; border: none;
            font-family: 'Poppins', sans-serif;
        }

        .info-card-edit:hover { color: var(--primary-dark); transform: scale(1.05); }

        .copy-btn {
            width: 26px; height: 26px;
            border-radius: 5px;
            border: 1px solid var(--border-color);
            background: var(--bg-white);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-muted);
            font-size: 11px;
        }

        .copy-btn:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            transform: scale(1.1);
        }

        .copy-btn.copied {
            background: var(--success);
            color: #fff;
            border-color: var(--success);
        }

        .jd-wrap .info-row {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between;
            align-items: flex-start;
            width: 100% !important;
            padding: 8px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .jd-wrap .info-row:last-child { border-bottom: none; }
        .jd-wrap .info-label { font-size: 12px; color: var(--text-secondary); font-weight: 400; flex-shrink: 0; }
        .jd-wrap .info-value { font-size: 12px; color: var(--text-primary); font-weight: 500; text-align: right; max-width: 60%; }
        .jd-wrap .info-value a { color: var(--primary); text-decoration: none; transition: all 0.2s; }
        .jd-wrap .info-value a:hover { text-decoration: underline; }

        .jd-wrap .desc-grid {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .jd-wrap .desc-card {
            display: block !important;
            background: var(--bg-white);
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            padding: 20px;
            position: relative;
            min-width: 0;
        }

        .desc-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .desc-card h3 {
            font-size: 13px; font-weight: 600;
            margin: 0;
        }

        .desc-text { font-size: 12px; color: var(--text-secondary); line-height: 1.8; margin-bottom: 12px; }
        .desc-list { list-style: none; margin-bottom: 12px; }
        .desc-list li {
            display: flex; align-items: flex-start; gap: 8px;
            font-size: 12px; color: var(--text-secondary);
            margin-bottom: 8px; line-height: 1.6;
        }
        .desc-list li i { color: var(--primary); font-size: 14px; margin-top: 2px; flex-shrink: 0; }

        .view-more {
            font-size: 12px; color: var(--primary); font-weight: 500;
            cursor: pointer; background: none; border: none;
            font-family: 'Poppins', sans-serif; padding: 0;
            transition: all 0.2s;
        }
        .view-more:hover { color: var(--primary-dark); transform: translateX(4px); }

        .questions-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .questions-card {
            background: var(--bg-white);
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            padding: 20px;
            position: relative;
        }

        .questions-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .questions-card h3 {
            font-size: 13px; font-weight: 600;
            margin: 0;
        }

        .question-list { list-style: none; counter-reset: question; }
        .question-list li {
            counter-increment: question;
            font-size: 12px; color: var(--text-secondary);
            margin-bottom: 12px; padding-left: 24px;
            position: relative; line-height: 1.6;
        }
        .question-list li::before {
            content: counter(question) ".";
            position: absolute; left: 0;
            color: var(--text-primary); font-weight: 600;
        }

        .view-all-btn {
            margin-top: 12px;
            padding: 8px 16px;
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-family: 'Poppins', sans-serif;
            font-size: 12px; font-weight: 500;
            color: var(--text-primary);
            cursor: pointer; transition: all 0.2s;
        }
        .view-all-btn:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

        .company-details-vertical {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .company-detail-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 12px;
            padding: 8px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .company-detail-row:last-child { border-bottom: none; }

        .company-detail-row i {
            width: 18px; color: var(--text-muted);
            margin-top: 2px; flex-shrink: 0;
            font-size: 13px;
        }

        .company-detail-label {
            color: var(--text-secondary);
            min-width: 120px;
            font-weight: 400;
        }

        .company-detail-value {
            color: var(--text-primary);
            font-weight: 500;
            flex: 1;
            word-break: break-word;
        }

        .company-detail-value a {
            color: var(--primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .company-detail-value a:hover { text-decoration: underline; }

        .popup-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center; justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }
        .popup-overlay.active { display: flex; animation: fadeIn 0.2s ease; }

        .popup-modal {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: 24px;
            width: 90%;
            max-width: 420px;
            box-shadow: var(--shadow-lg);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .popup-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px;
        }
        .popup-header h3 { font-size: 15px; font-weight: 600; }

        .popup-close {
            width: 32px; height: 32px;
            border-radius: 50%; border: none;
            background: var(--bg-light);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .popup-close:hover { background: var(--danger); color: #fff; }

        .popup-body { margin-bottom: 20px; }
        .popup-body p { font-size: 13px; color: var(--text-secondary); margin-bottom: 12px; }

        .popup-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            transition: all 0.2s;
        }
        .popup-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
        }

        .popup-actions { display: flex; gap: 10px; justify-content: flex-end; }

        .popup-btn {
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-family: 'Poppins', sans-serif;
            font-size: 12px; font-weight: 500;
            cursor: pointer; transition: all 0.2s;
            border: 1px solid var(--border-color);
            background: var(--bg-white);
        }
        .popup-btn:hover { background: var(--bg-light); }
        .popup-btn.primary { background: var(--primary); color: #fff; border-color: var(--primary); }
        .popup-btn.primary:hover { background: var(--primary-dark); }

        .phone-input-wrapper { display: flex; gap: 8px; }
        .country-select {
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            background: var(--bg-white);
            cursor: pointer;
            min-width: 80px;
        }

        .tooltip { position: relative;opacity: 1 !important; }
        .tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: calc(100% + 8px); left: 50%;
            transform: translateX(-50%) scale(0.9);
            padding: 6px 10px;
            background: var(--text-primary);
            color: #fff; font-size: 11px;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0; pointer-events: none;
            transition: all 0.2s;
            z-index: 10;
        }
        .tooltip:hover::after { opacity: 1; transform: translateX(-50%) scale(1); }

        .toast-container {
            position: fixed;
            top: 24px; right: 24px;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .toast {
            background: var(--bg-white);
            border-radius: var(--radius-md);
            padding: 14px 20px;
            box-shadow: var(--shadow-lg);
            display: flex; align-items: center; gap: 10px;
            font-size: 13px; font-weight: 500;
            border-left: 3px solid var(--success);
            animation: slideInRight 0.3s ease;
            min-width: 280px;
        }
        .toast.error { border-left-color: var(--danger); }
        .toast.info { border-left-color: var(--primary); }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(100%); }
            to { opacity: 1; transform: translateX(0); }
        }

        .mobile-toggle {
            display: none;
            position: fixed;
            top: 16px; left: 16px;
            z-index: 101;
            width: 40px; height: 40px;
            background: var(--sidebar-bg);
            color: #fff; border: none;
            border-radius: 8px;
            cursor: pointer;
            align-items: center; justify-content: center;
        }

        .pdf-loading-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.85);
            display: none;
            align-items: center; justify-content: center;
            z-index: 3000;
            flex-direction: column;
            gap: 20px;
        }
        .pdf-loading-overlay.active { display: flex; }
        .pdf-spinner {
            width: 48px; height: 48px;
            border: 3px solid rgba(255,255,255,0.2);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .pdf-loading-text {
            color: #fff;
            font-size: 14px;
            font-weight: 500;
        }

        @media (max-width: 1280px) {
            .pipeline-section { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 900px) {
            .jd-wrap .info-grid { grid-template-columns: repeat(2, 1fr); }
            .jd-wrap .desc-grid { grid-template-columns: 1fr; }
            .jd-wrap .questions-section { grid-template-columns: 1fr; }
        }

        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .mobile-toggle { display: flex; }
            .job-header-top { flex-direction: column; gap: 16px; }
            .job-actions { width: 100%; flex-direction: row; flex-wrap: wrap; }
            .pipeline-section { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .jd-wrap .pipeline-section { grid-template-columns: 1fr; }
            .jd-wrap .info-grid { grid-template-columns: 1fr; }
            .jd-wrap .content-area { padding: 16px; }
            .jd-wrap .top-actions { padding: 12px 16px; }
            .jd-wrap .hero-personnel-inline { flex-direction: column; align-items: flex-start; }
        }

        .fade-in-up {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
    
        .stat-card { transition: all 0.15s ease; background: #fff; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.5rem 0.25rem; text-align: center; }
        .stat-card:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .stat-card .icon-box { width: 1.375rem; height: 1.375rem; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.25rem; font-size: 0.65rem; }
        .stat-card .label { font-size: 0.65rem; color: #64748b; margin-bottom: 0.15rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding: 0 0.25rem; }
        .stat-card .value { font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-bottom: 0.15rem; }
        .stat-card .link { font-size: 0.65rem; color: #3B82F6; text-decoration: none; font-weight: 500; cursor: pointer; background: none; border: none; }
        .stat-card .link:hover { text-decoration: underline; }
        .agency-row { transition: all 0.1s ease; }
        .agency-row:hover { background: #F8FAFC; }
        .agency-row td { padding: 0.75rem 0.875rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .agency-card { transition: all 0.15s ease; background: #fff; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.75rem; }
        .agency-card:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .btn-primary { transition: all 0.15s; background: #3B82F6; color: white; border: none; border-radius: 0.375rem; padding: 0.375rem 0.875rem; font-size: 0.85rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 0.375rem; }
        .btn-primary:hover { background: #2563EB; transform: translateY(-1px); box-shadow: 0 2px 6px rgba(59,130,246,0.25); }
        .btn-secondary { transition: all 0.15s; background: #fff; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 0.375rem 0.875rem; font-size: 0.85rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 0.375rem; }
        .btn-secondary:hover { background: #f8fafc; border-color: #cbd5e1; }
        .view-btn { transition: all 0.15s; cursor: pointer; width: 1.75rem; height: 1.75rem; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; background: #fff; color: #64748b; font-size: 0.75rem; }
        .view-btn:first-child { border-radius: 0.375rem 0 0 0.375rem; border-right: none; }
        .view-btn:last-child { border-radius: 0 0.375rem 0.375rem 0; }
        .view-btn.active { border-color: #3B82F6; color: #3B82F6; background: #EFF6FF; }
        .view-btn:hover:not(.active) { background: #f8fafc; }
        .page-btn { transition: all 0.15s; cursor: pointer; min-width: 1.75rem; height: 1.75rem; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; border-radius: 0.375rem; background: #fff; color: #475569; font-size: 0.85rem; font-weight: 500; padding: 0 0.5rem; }
        .page-btn.active { background: #3B82F6; color: white; border-color: #3B82F6; }
        .page-btn:hover:not(.active) { background: #f1f5f9; }
        .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
        .email-icon, .phone-icon { transition: all 0.15s; cursor: pointer; width: 1.375rem; height: 1.375rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.25rem; color: #64748b; font-size: 0.75rem; border: none; background: transparent; }
        .email-icon:hover { background: #EFF6FF; color: #3B82F6; }
        .phone-icon:hover { background: #F0FDF4; color: #22C55E; }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; margin-right: 0.375rem; }
        .status-dot.active { background: #22C55E; }
        .status-dot.inactive { background: #94A3B8; }
        .status-badge { display: inline-flex; align-items: center; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
        .status-badge.active { background: #f0fdf4; color: #166534; }
        .status-badge.inactive { background: #f8fafc; color: #64748b; }
        
        @keyframes modalSlide { from { opacity: 0; transform: translateY(-8px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .search-input { width: 100%; padding: 0.5rem 0.75rem 0.5rem 2rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.85rem; color: #1e293b; background: #fff; transition: all 0.15s; }
        .search-input:focus { outline: none; border-color: #3B82F6; box-shadow: 0 0 0 2px rgba(59,130,246,0.1); }
        .search-input::placeholder { color: #94a3b8; }
        .filter-select { padding: 0.5rem 1.75rem 0.5rem 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.85rem; color: #475569; background: #fff; cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.5rem center; }
        .filter-select:focus { outline: none; border-color: #3B82F6; }
        .agency-logo-cus { width: 2.7rem; height: 2.7rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.65rem; color: white; flex-shrink: 0; }
        .contact-avatar { display: none; }
        .performance-metric { text-align: center; min-width: 2.25rem; }
        .performance-metric .number { font-weight: 700; font-size: 0.9rem; color: #0f172a; line-height: 1.2; }
        .performance-metric .label { font-size: 0.65rem; color: #64748b; text-transform: capitalize; margin-top: 0.125rem; }
        .view-terms-btn { padding: 0.2rem 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; color: #3B82F6; background: white; transition: all 0.15s; cursor: pointer; }
        .view-terms-btn:hover { background: #EFF6FF; border-color: #3B82F6; }
        .table-header-cust { font-size: 0.7rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em; padding: 0.375rem 0.375rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left; white-space: nowrap; }
        .table-header-cust.center { text-align: center; }
        .table-header-cust.right { text-align: right; }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 0.5rem; }
        .grid-view { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 0.5rem; }
        .grid-view .agency-card { display: flex; flex-direction: column; gap: 0.75rem; }
        .grid-view .agency-card .card-header { display: flex; align-items: center; gap: 0.625rem; }
        .grid-view .agency-card .card-body { display: flex; flex-direction: column; gap: 0.5rem; }
        .grid-view .agency-card .card-footer { display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 0.75rem; border-top: 1px solid #f1f5f9; }

        /* Agencies Tab Responsive Fixes for 15.6" Laptop */
        .stats-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 0.75rem; margin-bottom: 0.75rem; }

        @media (max-width: 1400px) {
            .stats-grid { grid-template-columns: repeat(4, minmax(0, 1fr)) !important; }
        }
        @media (max-width: 1100px) {
            .stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
        }
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
            .job-meta .sep { display: none; }
            .grid-view { grid-template-columns: 1fr; }
        }

        /* Compact table for smaller screens */
        .agency-row td { padding: 0.5rem 0.375rem; font-size: 0.8rem; }
        .table-header-cust { padding: 0.5rem 0.5rem; font-size: 0.7rem; }
        .performance-metric { min-width: 2rem; }
        .performance-metric .number { font-size: 0.85rem; }
        .performance-metric .label { font-size: 0.65rem; }
        .agency-logo-cus { width: 2.7rem; height: 2.7rem; font-size: 0.65rem; }
        .contact-avatar { width: 1.5rem; height: 1.5rem; }

        /* Filters wrap properly */
        .card > div[style*="flex-wrap:wrap"] { gap: 0.5rem !important; }
        .card > div[style*="flex-wrap:wrap"] > div { flex-wrap: wrap; }

        /* Table horizontal scroll container */
        #tableView { min-width: 100%; }
        #tableView table { min-width: 900px; }

            .bg-custom-bar {
        background: #f8fafc !important;
    }
    .page-close {
    position: absolute;
    top: 17px;
    right: 19px;
    width: 32px;
    height: 32px;
    border-radius: var(--radius-md);
    border: 1px solid #e2e8f0 !important;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

    /* ── 15.6-inch laptop fix (1366–1440 px range) ─────────────────── */
    @media (max-width: 1440px) {
        .top-actions { padding: 10px 14px; gap: 6px; }
        .action-btn { padding: 6px 10px; font-size: 11px; gap: 5px; }
        .action-btn i { font-size: 11px; }
        .content-area { padding: 16px; }
        .job-header-card { padding: 16px; }
        .job-header-top { gap: 12px; }
        .job-actions { min-width: 155px; }
        .job-action-btn { padding: 7px 12px; font-size: 11px; }
        .pipeline-section { gap: 10px; }
        .pipeline-card { padding: 12px; gap: 8px; }
        .pipeline-icon { width: 34px; height: 34px; font-size: 13px; }
        .pipeline-value { font-size: 14px; }
    }

    @media (max-width: 1366px) {
        .jd-wrap .top-actions { overflow-x: auto; flex-wrap: nowrap; scrollbar-width: none; }
        .jd-wrap .top-actions::-webkit-scrollbar { display: none; }
        .jd-wrap .action-btn { white-space: nowrap; flex-shrink: 0; padding: 5px 9px; font-size: 10.5px; }
        .jd-wrap .job-title-section h1 { font-size: 13.5px; }
        .jd-wrap .info-grid { grid-template-columns: repeat(3, 1fr); gap: 14px; }
        .jd-wrap .desc-grid { grid-template-columns: 1fr 1fr; gap: 14px; }
        .jd-wrap .questions-section { grid-template-columns: 1fr 1fr; gap: 14px; }
        .jd-wrap .hero-personnel-inline { gap: 10px; }
        .jd-wrap .personnel-card-inline { padding: 8px 10px; gap: 8px; }
    }
    </style>

 @php
    $targetJobId = $job->id;
    $company_id = auth()->user()->company_id;
    $relationget =  GeneralMethod::getrRcommendationRelationData($company_id,$targetJobId);
    $companyidcount= ($job->comId ?? $job->company_id ?? null) ?? $job->company_id ?? null;
    $from_company_id = $relationget->from_company_id ?? null;
    $to_company_id = $relationget->to_company_id ?? null;
    $applicationCounter = GeneralMethod::statusCountAll($job->id);
    $interview_counter = GeneralMethod::statusCountInterview($companyidcount,$job->id);
    $souCount = GeneralMethod::statusCount($companyidcount, $job->id,1);
    $evaCount = GeneralMethod::statusCount($companyidcount, $job->id,3);
    $offerCount = GeneralMethod::statusCount($companyidcount, $job->id,5);
    $joindCount = GeneralMethod::statusCount($companyidcount, $job->id,6);
    $recommendation_id = $to_company_id;
    if ($recommendation_id > 0 && ($job->comId ?? $job->company_id ?? null) != auth()->user()->company_id) {
        $applicationCounter = GeneralMethod::statusCountThirdpartyAll($to_company_id,$job->id);
        $interview_counter = GeneralMethod::statusCountThirdpartyInterview(auth()->user()->company_id, $job->id);
        $souCount = GeneralMethod::statusCountThirdparty($to_company_id, $job->id,1);
        $evaCount = GeneralMethod::statusCountThirdparty($to_company_id,$job->id,3);
        $offerCount = GeneralMethod::statusCountThirdparty($to_company_id, $job->id,5);
        $joindCount = GeneralMethod::statusCountThirdparty($to_company_id, $job->id,6);
    }
    $applicationCounter = number_format($applicationCounter);
    $hiringManager = $hiringManagerdesignation = $hiringManagerCompany = $contact_person_number = $contact_person_email = '--------';
    $hrManager = $hrManagerdesignation = $hrcontact_person_number = $hrcontact_person_email = '';
    // client_contact_info_jobs is an array when loaded via Elasticsearch (drawer); absent on DB::table() result (market-job-detail full page)
    if (is_array(@$job->client_contact_info_jobs)) {
        $job_assign = $job->job_assign;
        $jobAssign = collect($job_assign);

        if(count($job->client_contact_info_jobs)>0){
            $hiringManager = @$job->client_contact_info_jobs[0]['contact_person_name'];
            $hiringManagerdesignation = @$job->client_contact_info_jobs[0]['contact_person_designation'];
            $contact_person_number = @$job->client_contact_info_jobs[0]['contact_person_number'];
            $contact_person_email = @$job->client_contact_info_jobs[0]['contact_person_email'];
            $companyId = @$job->client_contact_info_jobs[0]['company_id'];
            $hiringManagerCompany = DB::table('companies')->where('id',$companyId)->first()->company_name;
        }

        if ($recommendation_id > 0 && @$to_company_id == auth()->user()->company_id) {
            $jobAssignListCaptain = $jobAssign->where('spok_user_id', '>', 0)->where('company_id', ($job->comId ?? $job->company_id ?? null))
            ->filter()
            ->map(fn ($c) => ($c))
            ->values()
            ->all();
            // dd($jobAssignListCaptain);
            $clientjobAssignListCaptain = '';
            if(count($jobAssignListCaptain)>0 && ($job->comId ?? $job->company_id ?? null) != auth()->user()->company_id){
                $hiringManager = ucfirst($jobAssignListCaptain[0]['user_name']);
                $companyId = $jobAssignListCaptain[0]['company_id'];
                $spok_user_id = $jobAssignListCaptain[0]['spok_user_id'];
                $hiringManagerCompany = DB::table('companies')->where('id',$companyId)->first()->company_name;
                $users = DB::table('users')->select('users_profile.designation','users.email','users.mobile')
                ->leftJoin('users_profile','users_profile.user_id','=','users.id')
                ->where('users.id',$spok_user_id)->first();
                $hiringManagerdesignation = @$users->designation ?? '';
                $contact_person_number = @$users->mobile ?? '';
                $contact_person_email = @$users->email ?? '';
            }

        }
        if ($recommendation_id > 0 && @$to_company_id == auth()->user()->company_id) {
            $jobId = $job->id;

            $partnersData = $jobAssign->where('company_id', $to_company_id)->where('job_id', $jobId)
            ->filter(function ($rowJob) {
                    return $rowJob['user_id'] == $rowJob['spok_user_id'];

            })->sortBy('user_name')->take(1)->values();
                foreach ($partnersData as $pval) {
                    $pval = (object) $pval;
                    $users = DB::table('users')->select('users_profile.designation','users.name','users.email','users.mobile')
                    ->leftJoin('users_profile','users_profile.user_id','=','users.id')
                    ->where('users.id',$pval->spok_user_id)->first();
                    $hrManager = ucfirst(@$users->name);
                    $hrManagerdesignation = @$users->designation ?? '';
                    $hrcontact_person_number = @$users->mobile ?? '';
                    $hrcontact_person_email = @$users->email ?? '';
                }
        } else {

            $jobId = $job->id;

            $partnersCount = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)
            ->unique('user_id')->count();

            $partnersData = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)
            ->filter(function ($rowJob) {
                    return $rowJob['user_id'] == $rowJob['spok_user_id'];
            })->sortBy('user_name')->take(1)->values();
            $showCount = 0;
            foreach ($partnersData as $pval) {
                $pval = (object) $pval;
                $users = DB::table('users')->select('users_profile.designation','users.name','users.email','users.mobile')
                ->leftJoin('users_profile','users_profile.user_id','=','users.id')
                ->where('users.id',$pval->spok_user_id)->first();
                $hrManager = ucfirst(@$users->name);
                $hrManagerdesignation = @$users->designation ?? '';
                $hrcontact_person_number = @$users->mobile ?? '';
                $hrcontact_person_email = @$users->email ?? '';
            }
        }
    }




@endphp



    @if(empty($hideMarketSections))
    <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon page-close btn-active-light-danger me-n5" id="kt_activitiesJob_close"> 
            <span class="svg-icon svg-icon-1">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
                    <rect x="0" y="7" width="16" height="2" rx="1" />
                    <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
                </g>
                </svg>
            </span> 
        </button>
    </div>
    @endif {{-- hideMarketSections: kt_activitiesJob_close --}}

    <div class="app-container jd-wrap">

        <main class="main-content" id="mainContent">

            {{-- @if($benchshowtab)
               @if(@$jobfindhide!=false) --}}
                    <?php
                       $public_bench_path = url('/service_company/apptracker/public_bench/' . GeneralMethod::value_encrypt(@$job->id));
                        if(($job->comId ?? $job->company_id ?? null)==auth()->user()->company_id){
                            $url  = url("service_company/apptracker/".GeneralMethod::value_encrypt(@$job->id)."/all");
                            $urladd =url('service_company/job-applications/create/' . GeneralMethod::value_encrypt(@$job->id) . '/0');
                        }else{
                            $url  = url("service_company/apptracker/clientapp/".GeneralMethod::value_encrypt(@$job->id)."/all");
                            $urladd =url('service_company/job-applications/clientcreate/' . GeneralMethod::value_encrypt(@$job->id) . '/0');
                        }
                    ?>

                    @if(empty($hideMarketSections))
                    <div class="top-actions">
                        @if($benchshowtab)
                            @if($jobfindhide)
                                <a href="{{ $url }}" class="">
                                    <button class="action-btn">
                                        <i class="fas fa-eye"></i>View Candidates
                                    </button>
                                </a>
                                <a href="{{ $urladd }}" class="job-drawer-add-candidate" data-job-status="{{ @$job->status }}">
                                    <button class="action-btn">
                                        <i class="fas fa-user-plus"></i>Add Candidate
                                    </button>
                                </a>
                                <script>
                                    // Hold/Closed jobs: block the direct navigation and explain why,
                                    // same popup as the apptracker page's Add Candidate button. Bound
                                    // directly (not delegated) since this fragment is re-injected fresh
                                    // each time the job drawer opens.
                                    $('.job-drawer-add-candidate').off('click').on('click', function(e) {
                                        var jobStatus = $(this).data('job-status');
                                        if (jobStatus === 'hold' || jobStatus === 'inactive') {
                                            e.preventDefault();
                                            var statusLabel = jobStatus === 'hold' ? 'Hold' : 'Closed';
                                            Swal.fire(
                                                'Position ' + statusLabel,
                                                'The position has been ' + statusLabel + '. You cannot upload new profiles for this position — kindly connect with the Hiring Manager regarding the same.',
                                                'warning'
                                            );
                                        }
                                    });
                                </script>
                            @endif
                            @if(@$tabshow)
                                @if($viewmore)
                                    @if($agencyView)
                                    
                                        <a href="{{ $public_bench_path }}" class="">
                                            <button class="action-btn">
                                                <i class="fas fa-eye"></i>Agency Bench Talents
                                            </button>
                                        </a>

                                        <button class="action-btn spartner-listing" data-row-id="{{ @$job->id }}">
                                            <i class="fas fa-building"></i>Add Agency
                                        </button>
                                    @endif
                                 @endif
                            @endif
                            @if(empty($hideMarketSections))
                            <button class="action-btn hm-listing" data-row-id="{{ @$job->id }}">
                                <i class="fas fa-users"></i>Add Team
                            </button>
                            @endif
                            @if(@$tabshow)
                                @if($viewmore)
                                    @if($agencyView)
                                    @php
                                        // Same Direct Client check used on the jobs list — a job recommended to
                                        // this agency isn't this agency's to change the status of.
                                        $isDirectClientJob = $recommendation_id > 0 && @$to_company_id == auth()->user()->company_id;
                                    @endphp
                                    <button class="action-btn status_stage" data-row-id="{{ @$job->id }}" data-direct-client="{{ $isDirectClientJob ? '1' : '0' }}">
                                        <i class="fas fa-sync-alt"></i>Change Status
                                        <i class="fas fa-chevron-down" style="margin-left:4px;font-size:10px;"></i>
                                    </button>
                                    @endif
                                 @endif
                            @endif
                        @endif

                    </div>
                    @endif {{-- hideMarketSections: top-actions --}}
                {{-- @endif          
            @endif --}}

            <div class="content-area">
                <div class="job-header-card fade-in-up">
                    <div class="job-header-top">
                        <div class="job-header-left">
                            <?php
                            if (!empty($logo_path)) {
                                echo '<div class="company-logo ' . @$bgTextDecore . '"><img src="' . str_replace('index.php', '', @$logo_path) . '" alt="logo" /></div>';
                            } else {
                                echo '<div class="company-logo ' . @$bgTextDecore . '">' . ucwords(@$strVal) . '</div>';
                            }
                            ?>
                            <div class="job-title-section">
                                <h1>
                                    {{ucwords(@$job->title)}}

                                    <?php
                                        if (@$job->status == 'active') {
                                    echo '<span onclick="toggleStatus()" class="btn btn-sm btn-light-success btn-active-primary fw-bolder me-3 fs-6"><i class="fas fa-circle" style="font-size:8px;"></i> Hiring <i class="fas fa-chevron-down" style="font-size:9px;"></i></span>';
                                } elseif (@$job->status == 'hold') {
                                    echo '<span onclick="toggleStatus()" class="btn btn-sm btn-light-warning btn-active-warning fw-bolder me-3 fs-6"><i class="fas fa-circle" style="font-size:8px;"></i> Hold <i class="fas fa-chevron-down" style="font-size:9px;"></i></span>';
                                } else {
                                    echo '<span onclick="toggleStatus()" class="btn btn-sm btn-light-danger btn-active-danger fw-bolder me-3 fs-6"><i class="fas fa-circle" style="font-size:8px;"></i> Closed <i class="fas fa-chevron-down" style="font-size:9px;"></i></span>';
                                }

                                ?>
                                </h1>
                                <div class="job-meta">
                                    <span><i class="fas fa-building"></i> <?php  if(@$jobfindhide!=false){ echo ucwords(@$company_or_client);}else{ echo '-----';} ?></span>
                                    <span style="width:4px;height:4px;background:var(--text-muted);border-radius:50%;"></span>
                                    <span><i class="fas fa-laptop-code"></i> {{ @$job->cat_name }}</span>
                                </div>
                                
                                <div class="job-tags">
                                        <?php
                                            if (!empty($skillArr)) {
                                                foreach ($skillArr as $sval) {
                                                    echo '<span class="job-tag">' . ucfirst($sval->skill_name) . '</span>';
                                                }
                                            }
                                        ?>
                                    {{-- <span class="job-tag more">+2</span> --}}
                                </div>
                                @if(empty($hideMarketSections))
                                <div class="hero-personnel-inline">
                                    <div class="personnel-card-inline" onclick="showPersonnelDetails('hiring-manager')">
                                        <span class="personnel-avatar-inline" style="background: #cdcaca; padding: 7px 0px 0px 11px;"><i class="far fa-user"></i>
                                        </span>
                                        <div class="personnel-info-inline">
                                            <span class="personnel-role-inline">Hiring Manager</span>
                                            <span class="personnel-name-inline">{{ $hiringManager }}</span>
                                            <span class="personnel-dept-inline">{{ $hiringManagerdesignation }}</span>
                                        </div>
                                        <div class="personnel-actions-inline">
                                            <a href="mailto:{{ $contact_person_email }}">
                                                <button class="personnel-icon-btn-inline tooltip" data-tooltip="Send Email">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                            </a>
                                            <a href="tel:{{ $contact_person_number }}">
                                                <button class="personnel-icon-btn-inline tooltip" data-tooltip="Call Phone">
                                                    <i class="fas fa-phone"></i>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="personnel-card-inline" onclick="showPersonnelDetails('recruiter')">
                                        <span class="personnel-avatar-inline" style="background: #cdcaca; padding: 7px 0px 0px 11px;"><i class="far fa-user"></i>
                                        </span>
                                        <div class="personnel-info-inline">
                                            <span class="personnel-role-inline">Assigned Recruiter</span>
                                            <span class="personnel-name-inline">{{ $hrManager }}</span>
                                            <span class="personnel-dept-inline">{{ $hrManagerdesignation }}</span>
                                        </div>
                                        <div class="personnel-actions-inline">
                                            <a href="mailto:{{ $hrcontact_person_email }}">
                                                <button class="personnel-icon-btn-inline tooltip" data-tooltip="Send Email">
                                                    <i class="far fa-envelope"></i>
                                                </button>
                                            </a>
                                            <a href="tel:{{ $hrcontact_person_number }}">
                                                <button class="personnel-icon-btn-inline tooltip" data-tooltip="Call Phone">
                                                    <i class="fas fa-phone"></i>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif {{-- hideMarketSections: hero-personnel-inline --}}
                            </div>
                        </div>
                        <div class="job-actions">
                             @if (in_array('edit_jobs', $userPermissions))
                                @if($benchshowtab)
                                    @if ($viewmore && ($job->comId ?? $job->company_id ?? null)==auth()->user()->company_id)
                                    <a href="<?php echo route('service_company.jobs.edit', GeneralMethod::value_encrypt($job->id)); ?>" class="">
                                        <button class="job-action-btn edit" style="width: 100%;">
                                            <i class="fas fa-pencil-alt"></i>Edit Job
                                        </button>
                                    </a>
                                    @endif
                                @endif
                            @endif
                            @if($agencyView)
                            <button class="job-action-btn" onclick="showSharePopup()">
                                <i class="fas fa-share-alt"></i>Share Job
                            </button>
                            <a href="{{ url('job-details/' .(@$job->id) . '/'.auth()->user()->company_id) }}" class="" target="_blank">
                                <button class="job-action-btn" style="width: 100%;">
                                    <i class="fas fa-external-link-alt"></i>View On Career Page
                                </button>
                            </a>
                            @endif
                            @if(empty($hideMarketSections))
                            <button class="job-action-btn" onclick="downloadJobPDF()">
                                <i class="fas fa-download"></i>Download Job
                            </button>
                            @endif
                            {{-- <button class="job-action-btn" onclick="showAllActions()">
                                <i class="fas fa-ellipsis-h"></i>All Actions
                            </button> --}}
                        </div>
                    </div>
                </div>

                @if(empty($hideMarketSections))
                <div class="pipeline-section fade-in-up delay-1">
                    <div class="pipeline-card">
                        <div class="pipeline-icon applied"><i class="far fa-thumbs-up"></i></div>
                        <div class="pipeline-info">
                            <div class="pipeline-label">Applied</div>
                            <div class="pipeline-value">
                                {{ $applicationCounter }}
                                {{-- <span class="pipeline-change up"><i class="fas fa-arrow-up" style="font-size:9px;"></i> 12%</span> --}}
                            </div>
                        </div>
                    </div>
                    <div class="pipeline-card">
                        <div class="pipeline-icon selected"><i class="far fa-check-circle"></i></div>
                        <div class="pipeline-info">
                            <div class="pipeline-label">Sourcing</div>
                            <div class="pipeline-value">
                               {{$souCount}}
                                {{-- <span class="pipeline-change up"><i class="fas fa-arrow-up" style="font-size:9px;"></i> 2%</span> --}}
                            </div>
                        </div>
                    </div>
                    <div class="pipeline-card">
                        <div class="pipeline-icon shortlisted"><i class="far fa-user"></i></div>
                        <div class="pipeline-info">
                            <div class="pipeline-label">Evaluation</div>
                            <div class="pipeline-value">
                                {{$evaCount }}
                                {{-- <span class="pipeline-change up"><i class="fas fa-arrow-up" style="font-size:9px;"></i> 8%</span> --}}
                            </div>
                        </div>
                    </div>
                    <div class="pipeline-card">
                        <div class="pipeline-icon offer"><i class="fas fa-gift"></i></div>
                        <div class="pipeline-info">
                            <div class="pipeline-label">Offer Released</div>
                            <div class="pipeline-value">
                                {{ $offerCount }}
                                {{-- <span class="pipeline-change up"><i class="fas fa-arrow-up" style="font-size:9px;"></i> 1%</span> --}}
                            </div>
                        </div>
                    </div>
                    <div class="pipeline-card">
                        <div class="pipeline-icon joined"><i class="far fa-user-circle"></i></div>
                        <div class="pipeline-info">
                            <div class="pipeline-label">Joined</div>
                            <div class="pipeline-value">
                               {{$joindCount}}
                                {{-- <span class="pipeline-change up"><i class="fas fa-arrow-up" style="font-size:9px;"></i> 1%</span> --}}
                            </div>
                        </div>
                    </div>
                    <div class="pipeline-card">
                        <div class="pipeline-icon interviewed"><i class="far fa-file-alt"></i></div>
                        <div class="pipeline-info">
                            <div class="pipeline-label">Interviewed</div>
                            <div class="pipeline-value">
                                {{ $interview_counter }}
                                {{-- <span class="pipeline-change up"><i class="fas fa-arrow-up" style="font-size:9px;"></i> 5%</span> --}}
                            </div>
                        </div>
                    </div>
                </div>
                @endif {{-- hideMarketSections: pipeline-section --}}

                <div class="tabs-section fade-in-up delay-2">
                    <div class="tabs-nav">
                        <button class="tab-btn active" onclick="switchTab(this, 'overview')">Job Overview</button>
                        @if(@$jobfindhide!=false)
                            @if($benchshowtab) 
                                @if(@$tabshow) 
                                    <button class="tab-btn" onclick="switchTab(this, 'interviews')">Interviews</button>
                                @endif 
                                @if(@$tabshow)
                                    <button class="tab-btn" onclick="switchTab(this, 'team')">Team</button>
                                @endif
                                @if(($job->comId ?? $job->company_id ?? null)==auth()->user()->company_id)         
                                    @if(@$tabshow)   
                                        @if ($viewmore)
                                           <button class="tab-btn" onclick="switchTab(this, 'agencies')">Agencies</button>
                                        @endif
                                    @endif
                                @endif
                                @if(($job->comId ?? $job->company_id ?? null)==auth()->user()->company_id)
                                        <button class="tab-btn" onclick="switchTab(this, 'agency_t_c')">Agency T&C</button>
                                @endif
                                        <button class="tab-btn" onclick="switchTab(this, 'dashboard')">Job Dashboard</button>
                            @endif 
                            @if($benchshowtab)
                                        <button class="tab-btn" onclick="switchTab(this, 'questions')">Interview Questions</button>
                            @else
                                @if($benchshowtabComman)
                                    @if(!$benchshowtabclose)
                                        <button class="tab-btn" onclick="switchTab(this, 'contactPersons')">Contacts</button>
                                        <button class="tab-btn" onclick="switchTab(this, 'timeline')">Timeline</button>
                                    @endif
                                @endif
                            @endif
                        @endif
                    </div>
                    {{-- @dd(@$job); --}}

                    <div class="tabs-content">
                        <div id="overview" class="tab-panel active">
                            <div class="info-grid">
                                <div class="info-card" id="card-basic">
                                    <div class="info-card-header">
                                        <span class="info-card-title">Basic Details</span>
                                        <div class="info-card-actions">
                                            <button type="button" class="copy-btn tooltip" data-tooltip="Copy all" onclick="copyCardText('card-basic')"><i class="far fa-copy"></i></button>
                                            {{-- <button class="info-card-edit" onclick="showEditPopup('Basic Details')">Edit</button> --}}
                                        </div>
                                    </div>
                                    <div class="info-row"><span class="info-label">Job ID</span><span class="info-value">{{ @$job->id }}</span></div>
                                    <div class="info-row"><span class="info-label">Due Date</span><span class="info-value">{{ date('d M, Y', strtotime(@$job->end_date)) }} <i class="far fa-calendar" style="margin-left:4px;cursor:pointer;" onclick="showDatePicker()"></i></span></div>
                                    <div class="info-row"><span class="info-label">Job Department</span><span class="info-value">{{ @$job->cat_name }}</span></div>
                                    <div class="info-row"><span class="info-label">Qualifications</span><span class="info-value">{{ @$qualification->name }}</span></div>
                                    <div class="info-row"><span class="info-label">Position Type</span><span class="info-value">{{ ucwords($employment_type) }}</span></div>
                                </div>

                                <div class="info-card" id="card-exp">
                                    <div class="info-card-header">
                                        <span class="info-card-title">Experience & Openings</span>
                                        <div class="info-card-actions">
                                            <button type="button" class="copy-btn tooltip" data-tooltip="Copy all" onclick="copyCardText('card-exp')"><i class="far fa-copy"></i></button>
                                            {{-- <button class="info-card-edit" onclick="showEditPopup('Experience & Openings')">Edit</button> --}}
                                        </div>
                                    </div>
                                    <div class="info-row"><span class="info-label">Min Experience</span><span class="info-value">
                                    {{ @$job->min_exp }} Year</span></div>
                                    <div class="info-row"><span class="info-label">Max Experience</span><span class="info-value">{{@$job->max_exp}} Years</span></div>
                                    <div class="info-row"><span class="info-label">Gender</span><span class="info-value">{{@$job->required_columns ?? 'Rather Not Say'}}</span></div>
                                    <div class="info-row"><span class="info-label">No. of Openings</span><span class="info-value">
                                    {{ @$job->total_positions }}</span></div>
                                    <div class="info-row"><span class="info-label">Job Location</span><span class="info-value">
                                    {{ ucwords(@$jobCitySelected) }}</span></div>
                                    <div class="info-row"><span class="info-label">Work Mode</span><span class="info-value">{{@$work_type}}</span></div>
                                    <div class="info-row"><span class="info-label">Work Shift</span><span class="info-value">{{ @$job->work_shifts }}</span></div>
                                </div>

                                <div class="info-card" id="card-comp">
                                    <div class="info-card-header">
                                        <span class="info-card-title">Compensation & Shift</span>
                                        <div class="info-card-actions">
                                            <button type="button" class="copy-btn tooltip" data-tooltip="Copy all" onclick="copyCardText('card-comp')"><i class="far fa-copy"></i></button>
                                            {{-- <button class="info-card-edit" onclick="showEditPopup('Compensation & Shift')">Edit</button> --}}
                                        </div>
                                    </div>
                                    <div class="info-row"><span class="info-label">Min Salary</span><span class="info-value">
                                    {!! $currency[@$job->min_salary_currency] !!} {{ @$min_salary }} /{{ @$max_sal_mode }}</span></div>
                                    <div class="info-row"><span class="info-label">Max Salary</span><span class="info-value">{!! $currency[@$job->min_salary_currency]!!} {{@$max_salary}} /{{@$max_sal_mode}}</span></div>
                                    <div class="info-row"><span class="info-label">Pay Type</span><span class="info-value">
                                    {{ $salaryMode[$job->max_sal_mode] }}</span></div>
                                    <div class="info-row"><span class="info-label">Currency</span><span class="info-value">{{ $currencyArr[@$job->min_salary_currency ?? 0] }}</span></div>
                                    <div class="info-row"><span class="info-label">Hiring Manager</span><span class="info-value">{{ $hiringManager }}</span></div>
                                    <div class="info-row"><span class="info-label">Hiring HR</span><span class="info-value">{{ $hrManager }}</span></div>
                                </div>
                            </div>

                            <div class="desc-grid">
                                <div class="desc-card" id="card-desc">
                                    <div class="desc-card-header">
                                        <h3>Job Description</h3>
                                        <div class="info-card-actions">
                                            <button type="button" class="copy-btn tooltip" data-tooltip="Copy all" onclick="copyCardText('card-desc')"><i class="far fa-copy"></i></button>
                                            {{-- <button class="info-card-edit" onclick="showEditPopup('Job Description')">Edit</button> --}}
                                        </div>
                                    </div>
                                    <?php echo @$job->job_description; ?>
                                    {{-- <button class="view-more" onclick="showFullDescription()">View More <i class="fas fa-chevron-down" style="font-size:10px;margin-left:4px;"></i></button> --}}
                                </div>

                                @if(empty($hideMarketSections))
                                <div class="desc-card" id="card-about">
                                    <div class="desc-card-header">
                                        <h3>About Company</h3>
                                        <div class="info-card-actions">
                                            <button type="button" class="copy-btn tooltip" data-tooltip="Copy all" onclick="copyCardText('card-about')"><i class="far fa-copy"></i></button>
                                            {{-- <button class="info-card-edit" onclick="showEditPopup('About Company')">Edit</button> --}}
                                        </div>
                                    </div>
                                    @if(@$job->self_company==1)
                                        <?php echo $description; ?>
                                        @else
                                        <?php echo $description; ?>
                                    @endif
                                    {{-- <button class="view-more" onclick="showFullCompany()">View More <i class="fas fa-chevron-down" style="font-size:10px;margin-left:4px;"></i></button> --}}
                                </div>
                                @endif {{-- hideMarketSections: about-company --}}
                            </div>

                            @if(empty($hideMarketSections))
                            <div class="questions-section">
                                <div class="questions-card" id="card-questions">
                                    <div class="questions-card-header">
                                        <h3>Job Questions</h3>
                                        <div class="info-card-actions">
                                            <button type="button" class="copy-btn tooltip" data-tooltip="Copy all" onclick="copyCardText('card-questions')"><i class="far fa-copy"></i></button>
                                            {{-- <button class="info-card-edit" onclick="showEditPopup('Job Questions')">Edit</button> --}}
                                        </div>
                                    </div>
                                    @php
                                         $job_additional_detail = DB::table('job_additional_detail')->select('*')->where('job_id', @$job->id)->get();
                                    @endphp
                                    <ol class="question-list">
                                        @if(@$job_additional_detail)
                                           @foreach($job_additional_detail as $qRow)
                                           <li>{{ $qRow->detail_value }}</li>
                                           @endforeach
                                        @endif
                                    </ol>
                                    {{-- <button class="view-all-btn" onclick="showAllQuestions()">View All Questions</button> --}}
                                </div>

                                <div class="questions-card" id="card-company">
                                    <div class="questions-card-header">
                                        <h3>Company Details</h3>
                                        <div class="info-card-actions">
                                            <button type="button" class="copy-btn tooltip" data-tooltip="Copy all" onclick="copyCardText('card-company')"><i class="far fa-copy"></i></button>
                                            {{-- <button class="info-card-edit" onclick="showEditPopup('Company Details')">Edit</button> --}}
                                        </div>
                                    </div>
                                    <div class="company-details-vertical">
                                        <div class="company-detail-row">
                                            <i class="fas fa-globe"></i>
                                            <span class="company-detail-label">Website</span>
                                            <span class="company-detail-value">
                                                <a href="{{ @$companyOrClientDetails->company_website }}" target="_blank">
                                                    {{ @$companyOrClientDetails->company_website }} <i class="fas fa-external-link-alt" style="font-size:10px;"></i>
                                                </a>
                                            </span>
                                        </div>
                                        <div class="company-detail-row">
                                            <i class="fas fa-industry"></i>
                                            <span class="company-detail-label">Industries</span>
                                            <span class="company-detail-value">{{@$companyOrClientDetails->ind_name}}</span>
                                        </div>
                                        <div class="company-detail-row">
                                            <i class="fas fa-users"></i>
                                            <span class="company-detail-label">No. of Employees</span>
                                            <span class="company-detail-value">{{@$companyOrClientDetails->company_size}}</span>
                                        </div>
                                        <div class="company-detail-row">
                                            <i class="far fa-calendar-alt"></i>
                                            <span class="company-detail-label">Founded Year</span>
                                            <span class="company-detail-value">{{@$companyOrClientDetails->founded}}</span>
                                        </div>
                                        <div class="company-detail-row">
                                            <i class="far fa-building"></i>
                                            <span class="company-detail-label">Company Type</span>
                                            <span class="company-detail-value">{{@$companyOrClientDetails->acount_type}}</span>
                                        </div>
                                        {{-- <div class="company-detail-row">
                                            <i class="fas fa-star"></i>
                                            <span class="company-detail-label">Specialties</span>
                                            <span class="company-detail-value">Capsules, Films & Foils, Engineering, Inspection, Track & Trace</span>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                            @endif {{-- hideMarketSections: job-questions + company-details --}}
                        </div>
                        <div id="interviews" class="tab-panel">
                            <style>
                                .iv-wrap { background:var(--bg-white); border:1px solid var(--border-color); border-radius:var(--radius-lg); overflow:hidden; }
                                .iv-scroll { overflow-x:auto; }
                                .iv-table { width:100%; min-width:1040px; border-collapse:collapse; font-family:'Poppins',sans-serif; }
                                .iv-table thead th { text-align:left; font-size:10.5px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; color:var(--text-secondary); background:var(--bg-light); padding:12px 14px; border-bottom:1px solid var(--border-color); white-space:nowrap; }
                                .iv-table tbody td { padding:14px; border-bottom:1px solid var(--border-color); vertical-align:top; }
                                .iv-table tbody tr:last-child td { border-bottom:none; }
                                .iv-table tbody tr:hover { background:#fafcff; }
                                .iv-cand { display:flex; align-items:flex-start; gap:10px; }
                                .iv-avatar { width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:13px; font-weight:700; flex-shrink:0; cursor:pointer; }
                                .iv-name { font-size:13px; font-weight:600; color:var(--text-primary); cursor:pointer; }
                                .iv-name:hover { color:var(--primary); }
                                .iv-sub { font-size:11px; color:var(--text-secondary); margin-top:1px; }
                                .iv-followup { margin-top:7px; display:inline-flex; align-items:center; gap:5px; padding:4px 9px; font-size:11px; font-weight:500; color:var(--text-secondary); background:var(--bg-white); border:1px solid var(--border-color); border-radius:var(--radius-sm); cursor:pointer; transition:all .15s; }
                                .iv-followup:hover { border-color:var(--primary); color:var(--primary); }
                                .iv-strong { font-size:13px; font-weight:600; color:var(--text-primary); }
                                .iv-muted { font-size:11px; color:var(--text-secondary); margin-top:2px; }
                                .iv-line { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--text-primary); margin-bottom:4px; }
                                .iv-line i { font-size:11px; color:var(--text-muted); width:13px; text-align:center; }
                                .iv-link { font-size:12px; color:var(--primary); text-decoration:none; font-weight:500; }
                                .iv-link:hover { text-decoration:underline; }
                                .iv-badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
                                .iv-pill-pending { color:#d97706; font-size:12px; font-weight:600; }
                                .iv-pill-done { color:var(--success); font-size:12px; font-weight:600; }
                                .iv-pill-pending i, .iv-pill-done i { font-size:10px; margin-right:3px; }
                                .iv-fb-add { display:inline-flex; align-items:center; gap:6px; padding:7px 12px; font-size:12px; font-weight:600; color:#fff; background:var(--primary); border:none; border-radius:var(--radius-sm); cursor:pointer; transition:all .15s; white-space:nowrap; }
                                .iv-fb-add:hover { background:var(--primary-dark); }
                                .iv-fb-view { display:inline-flex; align-items:center; gap:6px; padding:7px 12px; font-size:12px; font-weight:600; color:var(--primary); background:var(--bg-white); border:1px solid var(--border-color); border-radius:var(--radius-sm); cursor:pointer; transition:all .15s; white-space:nowrap; }
                                .iv-fb-view:hover { border-color:var(--primary); background:#eff6ff; }
                                .iv-empty { padding:40px 20px; text-align:center; color:var(--text-secondary); font-size:13px; }
                                .iv-tag-internal { display:inline-flex; align-items:center; gap:5px; font-size:11px; color:var(--text-secondary); margin-top:4px; }
                                .iv-tag-internal .dot { width:5px; height:5px; border-radius:50%; background:var(--primary); display:inline-block; }
                            </style>
                            @php
                                $ivPalette  = ['#22c55e','#8b5cf6','#3b82f6','#f59e0b','#ef4444','#0ea5e9','#ec4899','#14b8a6'];
                                $ivCompanyId = auth()->user()->company_id;
                                $ivReqType   = (@$job->self_company == 1) ? 'Internal' : 'Client';
                            @endphp
                            <div class="iv-wrap">
                                <div class="iv-scroll">
                                    <table class="iv-table">
                                        <thead>
                                            <tr>
                                                <th>Candidate</th>
                                                <th>Job Requisitions</th>
                                                <th>Interview Date &amp; Time</th>
                                                <th>Interviewers</th>
                                                <th>Source</th>
                                                <th>Stage</th>
                                                <th>Feedback</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse (@$interviewShedule ?? [] as $row)
                                                @php
                                                    $ivFirst    = strtolower(substr(trim(@$row->full_name) ?: '#', 0, 1));
                                                    $ivColor    = $ivPalette[ord($ivFirst) % count($ivPalette)];
                                                    $ivProfile  = ($row->thirdparty_company_id == $ivCompanyId) ? 'client-show-application-detail' : 'show-application-detail';
                                                    $ivFeedback = trim(@$feedback_status[@$row->feedback]['name'] ?? '');
                                                    $ivAvail    = ($row->available_status > 0) ? (@$available_status[@$row->available_status]['name'] ?? '') : '';
                                                    $ivHasFb    = ($ivFeedback !== '');
                                                @endphp
                                                <tr>
                                                    {{-- Candidate --}}
                                                    <td>
                                                        <div class="iv-cand">
                                                            <div class="iv-avatar {{ $ivProfile }}" data-row-id="{{ $row->wiseID }}" style="background:{{ $ivColor }};">
                                                                {{ strtoupper($ivFirst) }}
                                                            </div>
                                                            <div>
                                                                <div class="iv-name {{ $ivProfile }}" data-row-id="{{ $row->wiseID }}" title="{{ ucwords(@$row->full_name) }}">
                                                                    {{ ucwords(mb_substr(@$row->full_name, 0, 18)) }}
                                                                </div>
                                                                <div class="iv-sub">{{ ucwords(mb_substr(@$row->current_designation, 0, 22)) }}</div>
                                                                <button type="button" class="iv-followup" data-row-id="{{ $row->id }}">
                                                                    <i class="far fa-comment-dots"></i> Follow Up <i class="fas fa-chevron-down" style="font-size:9px;"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    {{-- Job Requisitions --}}
                                                    <td>
                                                        <div class="iv-strong" title="{{ @$job->title }}">{{ ucwords(mb_substr(@$job->title, 0, 18)) }}</div>
                                                        <div class="iv-muted">{{ mb_substr(@$company_or_client, 0, 16) }}</div>
                                                        <span class="iv-tag-internal"><span class="dot"></span>{{ $ivReqType }} &nbsp;&bull;&nbsp; {{ $job->id }}</span>
                                                    </td>
                                                    {{-- Interview Date & Time --}}
                                                    <td>
                                                        <div class="iv-line"><i class="far fa-calendar"></i>{{ date('d M, Y', strtotime($row->schedule_date)) }}</div>
                                                        <div class="iv-line"><i class="far fa-clock"></i>{{ date('h:i A', strtotime($row->schedule_date)) }} @if(!empty(@$row->timezone_abbreviation))({{ $row->timezone_abbreviation }})@endif</div>
                                                        <div class="iv-line"><i class="fas fa-video"></i>{{ ucwords(mb_substr(@$row->mode_lavel_name ?: @$row->mode_name, 0, 14)) }}</div>
                                                        @if(!empty(@$row->url_link) && filter_var($row->url_link, FILTER_VALIDATE_URL))
                                                            <a href="{{ $row->url_link }}" target="_blank" class="iv-link"><i class="fas fa-link" style="font-size:10px;"></i> Interview Link</a>
                                                        @endif
                                                    </td>
                                                    {{-- Interviewers --}}
                                                    <td>
                                                        @php
                                                            $ivPanel = '';
                                                            if (!empty($row->interviewers)) {
                                                                $ivArr = array_filter(array_map('trim', preg_split('/\s*,\s*/', trim($row->interviewers))));
                                                                $ivPanel = implode(', ', $ivArr);
                                                            }
                                                        @endphp
                                                        @if($ivPanel !== '')
                                                            <div class="iv-strong" title="{{ $ivPanel }}">{{ ucwords(mb_substr($ivPanel, 0, 16)) }}</div>
                                                            <div class="iv-muted">{{ mb_substr(@$row->uc_company_name, 0, 14) }}</div>
                                                        @else
                                                            <div class="iv-muted">N/A</div>
                                                        @endif
                                                        @if(!empty(@$row->mailstatus))
                                                            <span class="iv-link" style="font-size:11px;"><i class="far fa-paper-plane" style="font-size:10px;"></i> Mail Send</span>
                                                        @endif
                                                    </td>
                                                    {{-- Source --}}
                                                    <td>
                                                        <div class="iv-strong">{{ ucwords(mb_substr(@$row->posted_by, 0, 14)) }}</div>
                                                        <div class="iv-muted">{{ mb_substr(@$row->uc_company_name, 0, 14) }}</div>
                                                    </td>
                                                    {{-- Stage --}}
                                                    <td>
                                                        <span class="iv-badge {{ @$row->css_style }}">{{ mb_substr(@$row->master_status_name, 0, 12) }}</span>
                                                        @if(!empty(@$row->child_status_name))
                                                            <div class="iv-muted">{{ mb_substr(@$row->child_status_name, 0, 12) }}</div>
                                                        @endif
                                                    </td>
                                                    {{-- Feedback --}}
                                                    <td>
                                                        @if($ivHasFb)
                                                            <div class="iv-pill-done" style="margin-bottom:7px;"><i class="fas fa-check-circle"></i>{{ ucwords(mb_substr($ivAvail ?: 'Feedback', 0, 16)) }}</div>
                                                            <button type="button" class="iv-fb-view show-feedback" data-row-id="{{ $row->id }}">
                                                                <i class="far fa-eye"></i> View Feedback
                                                            </button>
                                                        @else
                                                            <div class="iv-pill-pending"><i class="far fa-clock"></i>Pending</div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7"><div class="iv-empty">No interviews scheduled.</div></td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="team" class="tab-panel">
                            <!-- Team Header -->
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;gap:0.75rem;flex-wrap:wrap;">
                                <div style="min-width:0;">
                                    <h2 class="fs-3" style="font-weight:600;color:#0f172a;">Teams</h2>
                                    <p class="fs-5" style="color:#64748b;margin-top:0.125rem;">Manage and track team working on this job</p>
                                </div>
                                <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
                                        <button data-row-id="{{ @$job->id }}" class="btn-primary hm-listing fs-5" style="padding:0.3rem 0.625rem;">
                                            <i class="fa-solid fa-plus fs-5" style=""></i> Add Team
                                        </button>
                                </div>
                            </div>

                            <!-- Team Container -->
                            <div id="teamsContainer" class="card" style="overflow:hidden;">
                                <!-- Table View -->
                                <div id="" style="overflow-x: auto; display: block;">
                                    <table style="width:100%;border-collapse:collapse;">
                                        <thead>
                                            <tr>
                                                <th class="table-header-cust fs-6" style="width:16%;min-width:130px;">Contact Person</th>
                                                <th class="table-header-cust center fs-6" style="width:32%;min-width:280px;">
                                                    <span style="display:flex;align-items:center;justify-content:center;gap:0.375rem;">Performance
                                                        <i class="fa-regular fa-circle-question info-icon" title="Performance metrics for the last 90 days"></i>
                                                    </span>
                                                </th>                                                
                                                <th class="table-header-cust fs-6" style="width:10%;min-width:90px;">Job shared</th>
                                                <th class="table-header-cust center fs-6" style="width:9%;min-width:80px;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="teamTableBody">
                                            
                                            @if ($recruiterlist)
                                                @foreach ($recruiterlist as $rval)
                                                    <tr class="agency-row">
                                                        @php
                                                                $bgTextDecore = $strVal = '';
                                                                $str = trim(@$rval->name);
                                                                $str = $strVal = strtolower($str[0]);
                                                                $str = ord($str);
                                                                if ($str >= 97 && $str <= 101) {
                                                                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                                                                } elseif ($str >= 102 && $str <= 106) {
                                                                    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                                                                } elseif ($str >= 107 && $str <= 111) {
                                                                    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                                                                } elseif ($str >= 112 && $str <= 116) {
                                                                    $bgTextDecore = 'fs-3 bg-light-success text-success';
                                                                } elseif ($str >= 117 && $str <= 121) {
                                                                    $bgTextDecore = 'fs-3 bg-dark text-secondary';
                                                                } else {
                                                                    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                                                                }
                                                        @endphp
                                                        <td>
                                                            <div style="display:flex;align-items:center;gap:0.375rem;">
                                                                <div>
                                                                    <div class="fs-6" style="font-size:0.8rem;font-weight:500;color:#0f172a;line-height:1.2;">{{ substr(ucwords(@$rval->name), 0, 20) }}</div>
                                                                    <div class="fs-7" style="font-size:0.7rem;color:#64748b;line-height:1.2;">{{ $rval->designation }}</div>
                                                                    <div class="fs-7" style="display:flex;align-items:center;gap:0.125rem;margin-top:0.125rem;">
                                                                        <button class="email-icon" data-email="{{ $rval->email }}" data-name="{{ @$rval->name }}" title="Send email">
                                                                            <i class="fa-regular fa-envelope fs-7" style=""></i>
                                                                        </button>
                                                                        <button class="phone-icon" data-phone="{{ $rval->mobile }}" data-country="IN" data-name="{{ @$rval->name }}" title="Call contact">
                                                                            <i class="fa-solid fa-phone fs-7" style=""></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div style="display:flex;align-items:center;justify-content:center;gap:0.5rem;">
                                                                <div class="performance-metric">
                                                                    <div class="number fs-6">{{ @$rval->sourced }}</div>
                                                                    <div class="label fs-7">Sourced</div>
                                                                </div>
                                                                <div class="performance-metric">
                                                                    <div class="number fs-6">{{ @$rval->evaluated }}</div>
                                                                    <div class="label fs-7">Evaluated</div>
                                                                </div>
                                                                <div class="performance-metric">
                                                                    <div class="number fs-6">{{ @$rval->Interview }}</div>
                                                                    <div class="label fs-7">Interviews</div>
                                                                </div>
                                                                <div class="performance-metric">
                                                                    <div class="number fs-6">{{ @$rval->offer }}</div>
                                                                    <div class="label fs-7">Offer</div>
                                                                </div>
                                                                <div class="performance-metric">
                                                                    <div class="number fs-6">{{ @$rval->Joined }}</div>
                                                                    <div class="label fs-7">Joined</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $created_at = $rval->created_at;
                                                                $newDate = date('d M, Y', strtotime($created_at));
                                                                $daysAgo = GeneralMethod::timeAgo($rval->created_at);
                                                            @endphp
                                                            <div class="fs-6" style="font-weight:500;color:#0f172a;">{{ ucwords($daysAgo) }}</div>
                                                            <div class="fs-7" style="color:#64748b;margin-top:0.125rem;">{{ $newDate }}</div>
                                                        </td>
                                                        
                                                        <td style="text-align:center;">
                                                            <span class="status-badge active fs-6">
                                                                <span class="status-dot active"></span>
                                                                Active
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                        <div id="agencies" class="tab-panel">
                            <!-- Agencies Header -->
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;gap:0.75rem;flex-wrap:wrap;">
                                <div style="min-width:0;">
                                    <h2 class="fs-3" style="font-weight:600;color:#0f172a;">Agencies</h2>
                                    <p class="fs-5" style="color:#64748b;margin-top:0.125rem;">Manage and track agencies working on this job</p>
                                </div>
                                <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
                                    @if(@$tabshow)
                                        @if ($viewmore)
                                        <button data-row-id="{{ @$job->id }}" class="btn-primary spartner-listing fs-5" style="padding:0.3rem 0.625rem;">
                                            <i class="fa-solid fa-plus fs-5" style=""></i> Add Agency
                                        </button>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Agencies Container -->
                            <div id="agenciesContainer" class="card" style="overflow:hidden;">
                                <!-- Table View -->
                                <div id="tableView" style="overflow-x: auto; display: block;">
                                    <table style="width:100%;border-collapse:collapse;">
                                        <thead>
                                            <tr>
                                                <th class="table-header-cust fs-6" style="width:18%;min-width:140px;">Agency</th>
                                                <th class="table-header-cust fs-6" style="width:16%;min-width:130px;">Contact</th>
                                                <th class="table-header-cust center fs-6" style="width:32%;min-width:280px;">
                                                    <span style="display:flex;align-items:center;justify-content:center;gap:0.375rem;">Performance
                                                        <i class="fa-regular fa-circle-question info-icon" title="Performance metrics for the last 90 days"></i>
                                                    </span>
                                                </th>
                                                <th class="table-header-cust fs-6" style="width:10%;min-width:90px;">Job shared</th>
                                                <th class="table-header-cust center fs-6" style="width:9%;min-width:80px;">Status</th>
                                                <th class="table-header-cust right fs-6" style="width:9%;min-width:80px;">Terms</th>
                                            </tr>
                                        </thead>
                                        <tbody id="agenciesTableBody">
                                            
                                            @if ($recommendedDataList)
                                                @foreach ($recommendedDataList as $reqVal)
                                                    <tr class="agency-row">
                                                        <td>
                                                            <div style="display:flex;align-items:center;gap:0.625rem;">
                                                                <div class="agency-logo-cus" style="background:#0369a1;color:#fff;font-size:1.1rem;">
                                                                    @php
                                                                    $logo_path = '';
                                                                    if (!empty(@$reqVal->logo) && strlen(@$reqVal->logo) > 10) {
                                                                        $logo_path = @$reqVal->logo;
                                                                    }
                                                                    
                                                                    @endphp
                                                                    @if($logo_path)
                                                                       <img src="{{str_replace('index.php', '', $logo_path) }}" alt="Company Logo" class="border-radius-5 w-100">
                                                                    @else
                                                                        {{ substr(ucwords(@$reqVal->company_name), 0, 1) }}
                                                                    @endif
                                                                   
                                                                </div>
                                                                <div> 
                                                                    <div class="fs-6" style="font-weight:600;color:#0f172a;">{{ substr(ucwords(@$reqVal->company_name), 0, 15) }}</div>
                                                                    <div class="fs-7" style="color:#64748b;margin-top:0.125rem;">{{ substr(ucwords(@$reqVal->ind_name), 0, 15) }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $contHtml = '';
                                                                $contactQuery = GeneralMethod::team_persion($reqVal->jobid, $reqVal->id);
                                                            @endphp
                                                            @if (!empty($contactQuery))
                                                                @foreach ($contactQuery as $cont)
                                                                        <div style="display:flex;align-items:center;gap:0.375rem;">
                                                                            <div>
                                                                                <div class="fs-6" style="font-size:0.8rem;font-weight:500;color:#0f172a;line-height:1.2;">{{ $cont->name }}</div>
                                                                                <div class="fs-7" style="font-size:0.7rem;color:#64748b;line-height:1.2;">{{ $cont->designation }}</div>
                                                                                <div class="fs-7" style="display:flex;align-items:center;gap:0.125rem;margin-top:0.125rem;">
                                                                                    <button class="email-icon" data-email="{{ $cont->email }}" data-name="Vikram Singh" title="Send email">
                                                                                        <i class="fa-regular fa-envelope fs-7" style=""></i>
                                                                                    </button>
                                                                                    <button class="phone-icon" data-phone="{{ $cont->mobile }}" data-country="IN" data-name="Vikram Singh" title="Call contact">
                                                                                        <i class="fa-solid fa-phone fs-7" style=""></i>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div style="display:flex;align-items:center;justify-content:center;gap:0.5rem;">
                                                                <div class="performance-metric">
                                                                    <div class="number fs-6">{{ @$reqVal->sourced }}</div>
                                                                    <div class="label fs-7">Sourced</div>
                                                                </div>
                                                                <div class="performance-metric">
                                                                    <div class="number fs-6">{{ @$reqVal->evaluated }}</div>
                                                                    <div class="label fs-7">Evaluated</div>
                                                                </div>
                                                                <div class="performance-metric">
                                                                    <div class="number fs-6">{{ @$reqVal->Interview }}</div>
                                                                    <div class="label fs-7">Interviews</div>
                                                                </div>
                                                                <div class="performance-metric">
                                                                    <div class="number fs-6">{{ @$reqVal->offer }}</div>
                                                                    <div class="label fs-7">Offer</div>
                                                                </div>
                                                                <div class="performance-metric">
                                                                    <div class="number fs-6">{{ @$reqVal->Joined }}</div>
                                                                    <div class="label fs-7">Joined</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $created_at = $reqVal->created_at;
                                                                $newDate = date('d M, Y', strtotime($created_at));
                                                                $daysAgo = GeneralMethod::timeAgo($reqVal->created_at);
                                                            @endphp
                                                            <div class="fs-6" style="font-weight:500;color:#0f172a;">{{ ucwords($daysAgo) }}</div>
                                                            <div class="fs-7" style="color:#64748b;margin-top:0.125rem;">{{ $newDate }}</div>
                                                        </td>
                                                        <td style="text-align:center;">
                                                            <span class="status-badge {{ @$reqVal->final_status==1 ? 'active' : 'danger' }} fs-6">
                                                                <span class="status-dot {{ @$reqVal->final_status==1 ? 'active' : 'danger' }}"></span>
                                                                {{ @$reqVal->final_status == 1 ? 'Active' : 'Closed' }}
                                                            </span>
                                                        </td>
                                                        <td style="text-align:right;">
                                                            <button data-row-id="{{ $reqVal->relation_id }}" data-row-relation_id="{{$reqVal->relation_id }}" class="view-terms-btn fs-6 client-terms-accept" data-name="Global Tech Staffing">View Terms</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                        </div>
                        <div id="dashboard" class="tab-panel">
                            <div class="info-card" id="card-dashboard">
                                <div class="info-card-header">
                                    <span class="info-card-title">Dashboard Analytics</span>
                                    <div class="info-card-actions">
                                        <button type="button" class="copy-btn tooltip" data-tooltip="Copy all" onclick="copyCardText('card-dashboard')"><i class="far fa-copy"></i></button>
                                    </div>
                                </div>
                                <!--begin::Col-->
                                <div class="row">
                                    <!--Start Total Candidate Added -->
                                    <div class="col-lg-6 col-xl-cus-6 col-xl-6 col-xxl-6 mb-6 mb-xl-0">
                                        <!--begin::Chart widget 3-->
                                        <div class="card card-flush overflow-hidden h-md-100">
                                            <!--begin::Header-->
                                            <div class="card-header py-5">
                                                <!--begin::Title-->
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$total_candidates}}</span>
                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Candidates added In</span>
                                                </h3>
                                                <!--end::Title-->
                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Card body-->
                                            <div class="card-body d-flex justify-content-between flex-column pb-1 px-0" style="position: relative">
                                                <!--begin::Chart-->
                                                <div id="kt_charts_widget_3" class="min-h-auto">
                                                    <canvas id="myChart_<?php echo @$job->id; ?>"></canvas>
                                                </div><br>
                                                <!--end::Chart-->
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                        <!--end::Chart widget 3-->
                                    </div>
                                    <!--End Total Candidate Added -->
                                    <!--Start Candidate last 7 days -->
                                    <div class="col-lg-6 col-xl-cus-6 col-xl-6 col-xxl-6 mb-6 mb-xl-0">
                                        <!--begin::Chart widget 3-->
                                        <div class="card card-flush overflow-hidden h-md-100">
                                            <!--begin::Header-->
                                            <div class="card-header py-5">
                                                <!--begin::Title-->
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotal}}</span>
                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Candidates added In 7 days</span>
                                                </h3>
                                                <!--end::Title-->
                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Card body-->
                                            <div class="card-body d-flex justify-content-between flex-column pb-1 px-0" style="position: relative">
                                                <!--begin::Chart-->
                                                <div id="kt_charts_widget_3" class="min-h-auto">
                                                    <canvas id="candidates_left_chart_<?php echo @$job->id; ?>"></canvas>
                                                </div><br>
                                                <!--end::Chart-->
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                        <!--end::Chart widget 3-->
                                    </div>
                                    <!--End Candidate last 7 days -->
                                </div>
                                <div class="row gx-5 gx-xl-10 mb-xl-10">
                                    <div class="col-md-6 col-xl-cus-5 col-lg-6 col-xl-5 col-xxl-6 mb-2">
                                        <div class="card card-flush h-md-50 mb-xl-10">
                                            <!--begin::Header-->
                                            <div class="card-header pt-5">
                                                <!--begin::Title-->
                                                <div class="card-title d-flex flex-column">
                                                    <!--begin::Info-->
                                                    <div class="d-flex align-items-center">
                                                        <!--begin::Amount-->
                                                        <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotaloffer}}
                                                        </span>
                                                        <!--end::Amount-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Subtitle-->
                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Offers In {{ date("M Y ", strtotime($startOfMonthcomman)) }} To {{ date("M Y ", strtotime($endOfMonthcomman)) }}</span>
                                                    <!--end::Subtitle-->
                                                </div>
                                                <!--end::Title-->
                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Card body-->
                                            <div class="card-body d-flex align-items-end pt-0">
                                                <!--begin::Progress-->
                                                <div class="d-flex align-items-center flex-column mt-3 w-100">
                                                    <div id="kt_card_widget_4_chart_<?php echo @$job->id; ?>" style="min-width: 100%; min-height: 200px">
                                                        <canvas id="candidates_offer_chart_<?php echo @$job->id; ?>" height="170"></canvas>
                                                    </div>
                                                </div>
                                                <!--end::Progress-->
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-cus-5 col-lg-6 col-xl-5 col-xxl-6 mb-2">
                                        <div class="card card-flush h-md-50 mb-xl-10">
                                            <!--begin::Header-->
                                            <div class="card-header pt-5">
                                                <!--begin::Title-->
                                                <div class="card-title d-flex flex-column">
                                                    <!--begin::Amount-->
                                                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotaljoin}}</span>
                                                    <!--end::Amount-->
                                                    <!--begin::Subtitle-->
                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Joined In {{ date("M Y ", strtotime($startOfMonthcomman)) }} To {{ date("M Y ", strtotime($endOfMonthcomman)) }}</span>
                                                    <!--end::Subtitle-->
                                                </div>
                                                <!--end::Title-->
                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Card body-->
                                            <div class="card-body d-flex flex-column justify-content-end pe-0">
                                                <!--begin::Title-->
                                                <!--end::Title-->
                                                 <!--begin::Users group-->
                                                 <div class="symbol-group symbol-hover flex-nowrap">
                                                    <div id="kt_card_widget_4_chart_<?php echo @$job->id; ?>" style="min-width: 100%; min-height: 200px">
                                                        <canvas id="candidates_join_chart_<?php echo @$job->id; ?>" height="170">
                                                        </canvas>
                                                    </div>
                                                </div>
                                                <!--end::Users group-->
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                    </div>
                                </div>
                                <!--Start Interview Section-->
                                <div class="row gy-5 g-xl-10">
                                    <!--begin::Col-->
                                    <div class="col-xl-4 mb-xl-10">
                                        <!--begin::List widget 17-->
                                        <div class="card card-flush h-xl-100">
                                            <!--begin::Header-->
                                            <div class="card-header pt-7">
                                                <!--begin::Title-->
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="card-label fw-bold text-gray-800">Upcoming Interviews</span>
                                                </h3>
                                                <!--end::Title-->
                                                <!--end::Toolbar-->
                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Body-->
                                            <div class="card-body pt-0">
                                                <!--begin::Content-->
                                                <div class="d-flex flex-stack my-5">
                                                    <span class="text-gray-500 fs-7 fw-bold"></span>
                                                    <span class="text-gray-500 fw-bold fs-7"></span>
                                                </div>
                                                <!--end::Content-->
                                                @if (count($upcomingData) > 0)
                                                    @foreach ($upcomingData as $rowArr)
                                                            @php
                                                                $row = (object) $rowArr;
                                                                $schedule_date = Carbon::parse($row->schedule_date)->format('M d');
                                                                $schedule_date_explode = explode(" ",$schedule_date);
                                                            @endphp
                                                        <!--begin::Item-->
                                                        <div class="d-flex flex-stack">
                                                            <!--begin::Wrapper-->
                                                            <div class="d-flex align-items-center me-3">
                                                                <!--begin::Icon-->
                                                                <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-40px h-40px mr-2 text-center" style="display: inline-grid">
                                                                    <span style="font-size: 12px; font-weight: 600;">{{$schedule_date_explode[0]}}</span><span style="color: #373bed; font-size: 17px; font-weight: 900;">{{$schedule_date_explode[1]}}</span>
                                                                </a>
                                                                <!--end::Icon-->
                                                                <!--begin::Section-->
                                                                <div class="flex-grow-1">
                                                                    <a href="javascript:;" class="text-gray-800 text-hover-primary fs-5 fw-bold lh-0">{{ucwords(mb_substr(@$row->full_name, 0, 20))}}</a>
                                                                    <span class="text-gray-500 fw-semibold d-block fs-7">{{ucwords(mb_substr(@$row->current_designation, 0, 21))}}</span>
                                                                </div>
                                                                <!--end::Section-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Value-->
                                                            <span class="text-gray-800 fw-bold fs-6">
                                                            {{$row->schedule_to_time. ($row->timezone_abbreviation ? ' (' . $row->timezone_abbreviation . ')' : '')}} <br>
                                                            <span class="text-gray-500 fw-semibold d-block fs-7">
                                                            {{ucwords(mb_substr($row->mode_name, 0, 13))}} 
                                                            <span>
                                                            </span>
                                                            <!--end::Value-->
                                                        </div>
                                                        <!--end::Item-->
                                                        <!--begin::Separator-->
                                                        <div class="separator separator-dashed my-4"></div>
                                                        <!--end::Separator-->
                                                    @endforeach
                                                @else
                                                    <table border="1" width="100%" cellpadding="10">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="4" align="center">No records found</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                @endif
                                            </div>
                                            <!--end::Body-->
                                        </div>
                                        <!--end::List widget 17-->
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col-->
                                    <div class="col-xl-8 mb-5 mb-xl-10">
                                        <!--begin::Table widget 6-->
                                        <div class="card card-flush h-xl-100">
                                            <!--begin::Header-->
                                            <div class="card-header pt-7" style="position: relative">
                                                <!--begin::Title-->
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="card-label fw-bold text-gray-800">Candidates Interviews</span>
                                                </h3>
                                                <!--end::Title-->
                                                <span class="" style="position: absolute; right: 14px; top: 26px;">
                                                    <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="sortingfilter" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdatagetmonth(this.value)">
                                                        <option value="1">Last 6 Months</option>
                                                        <option value="0">Last 7 Days</option>
                                                    </select>
                                                </span>
                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Body-->
                                            <div class="card-body">
                                                <!--begin::Tab Content-->
                                                <div class="tab-content">
                                                    <!--begin::Tap pane-->
                                                    <div class="tab-pane fade active show" id="kt_stats_widget_6_tab_1_<?php echo @$job->id; ?>" role="tabpanel">
                                                        <!--begin::Table container-->
                                                        <div id="kt_card_widget_6_chart_<?php echo @$job->id; ?>" class="w-100" style="height: 200px">
                                                            <div class="inetview_recent_chart_<?php echo @$job->id; ?> d-none" id="kt_card_widget_4_chart_<?php echo @$job->id; ?>" 
                                                            style="min-width: 100%; min-height: 200px">
                                                            <canvas id="inetview_recent_chart_<?php echo @$job->id; ?>" height="300"></canvas>
                                                        </div>
                                                        <div class="inetview_recent_chart_month_<?php echo @$job->id; ?>" id="kt_card_widget_4_chart" style="min-width: 100%; min-height: 200px">
                                                            <canvas id="inetview_recent_chart_month_<?php echo @$job->id; ?>" height="230"></canvas>
                                                        </div>
                                                    </div>
                                                    <!--end::Table-->
                                                </div>
                                                <!--end::Tap pane-->
                                            </div>
                                            <!--end::Tab Content-->
                                        </div>
                                        <!--end: Card Body-->
                                    </div>
                                    <!--end::Table widget 6-->
                                </div>
                                <!--end::Col-->
                                </div>
                                <!--End Interview Section-->
                            </div>
                        </div>
                        <div id="questions" class="tab-panel">
                            <div class="questions-card" id="card-all-questions" style="width:100%;">
                                <div class="questions-card-header">
                                    <h3>All Interview Questions</h3>
                                    <div class="info-card-actions">
                                        @if(@$captainAccessCheck)
                                            <button class="btn btn-primary align-self-center" id="addQuestionBtn_{{ $job->id }}"> 
                                            <i class="far fa-copy"></i>+ Add Question
                                            </button>
                                        @else
                                            <button class="btn btn-primary align-self-center" id="addQuestionBtn_{{ $job->id }}"> 
                                            <i class="far fa-copy"></i>+ Add Question
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body" id="questionList_{{@$job->id}}">
                                     {!! @$interview_questions->questions !!}
                                </div>
                            </div>
                        </div>
                        <div id="agency_t_c" class="tab-panel">
                            <div class="info-card" id="card-activity">
                                <div class="info-card-header">
                                    <span class="info-card-title">Terms & Condition</span>
                                    <div class="info-card-actions">
                                        <button type="button" class="copy-btn tooltip" data-tooltip="Copy all" onclick="copyCardText('card-activity')"><i class="far fa-copy"></i></button>
                                    </div>
                                </div>
                                <div style="padding:16px 0;">
                                   {!!@$terms!!}
                                </div>
                            </div>
                        </div>
                        <div id="contactPersons" class="tab-panel">
                            <style>
                                .ct-wrap { background:var(--bg-white); border:1px solid var(--border-color); border-radius:var(--radius-lg); overflow:hidden; }
                                .ct-scroll { overflow-x:auto; }
                                .ct-table { width:100%; min-width:680px; border-collapse:collapse; font-family:'Poppins',sans-serif; }
                                .ct-table thead th { text-align:left; font-size:10.5px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; color:var(--text-secondary); background:var(--bg-light); padding:12px 14px; border-bottom:1px solid var(--border-color); white-space:nowrap; }
                                .ct-table tbody td { padding:14px; border-bottom:1px solid var(--border-color); vertical-align:middle; font-size:12px; color:var(--text-primary); }
                                .ct-table tbody tr:last-child td { border-bottom:none; }
                                .ct-table tbody tr:hover { background:#fafcff; }
                                .ct-person { display:flex; align-items:center; gap:10px; }
                                .ct-avatar { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:13px; font-weight:700; flex-shrink:0; }
                                .ct-name { font-size:13px; font-weight:600; color:var(--text-primary); }
                                .ct-link { color:var(--primary); text-decoration:none; }
                                .ct-link:hover { text-decoration:underline; }
                                .ct-muted { color:var(--text-secondary); }
                                .ct-empty { padding:40px 20px; text-align:center; color:var(--text-secondary); font-size:13px; }
                            </style>
                            @php $ctPalette = ['#22c55e','#8b5cf6','#3b82f6','#f59e0b','#ef4444','#0ea5e9','#ec4899','#14b8a6']; @endphp
                            <div class="ct-wrap">
                                <div class="ct-scroll">
                                    <table class="ct-table">
                                        <thead>
                                            <tr>
                                                <th style="width:40px;">#</th>
                                                <th>Name</th>
                                                <th>Designation</th>
                                                <th>Contact</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse (@$bench_assign_row ?? [] as $ctVal)
                                                @php
                                                    $ctVal   = (object) $ctVal;
                                                    $ctFirst = strtolower(substr(trim(@$ctVal->name) ?: '#', 0, 1));
                                                    $ctColor = $ctPalette[ord($ctFirst) % count($ctPalette)];
                                                @endphp
                                                <tr>
                                                    <td class="ct-muted">{{ $loop->iteration }}</td>
                                                    <td>
                                                        <div class="ct-person">
                                                            <div class="ct-avatar" style="background:{{ $ctColor }};">{{ strtoupper($ctFirst) }}</div>
                                                            <span class="ct-name">{{ ucwords(@$ctVal->name) }}</span>
                                                        </div>
                                                    </td>
                                                    <td>{{ ucwords(@$ctVal->designation) ?: '--' }}</td>
                                                    <td>
                                                        @if(!empty(@$ctVal->email))
                                                            <a href="mailto:{{ $ctVal->email }}" class="ct-link">{{ $ctVal->email }}</a><br>
                                                        @endif
                                                        @if(!empty(@$ctVal->mobile))
                                                            <span class="ct-muted">{{ ucwords(@$ctVal->mobile) }}</span>
                                                        @endif
                                                        @if(empty(@$ctVal->email) && empty(@$ctVal->mobile)) -- @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="4"><div class="ct-empty">No contacts available.</div></td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="timeline" class="tab-panel">
                            <style>
                                .tl-wrap { background:var(--bg-white); border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:22px 24px; }
                                .tl-list { position:relative; margin:0; padding:0 0 0 6px; list-style:none; }
                                .tl-item { position:relative; padding:0 0 22px 28px; border-left:2px solid var(--border-color); }
                                .tl-item:last-child { border-left-color:transparent; padding-bottom:0; }
                                .tl-dot { position:absolute; left:-9px; top:0; width:16px; height:16px; border-radius:50%; background:var(--bg-white); border:2px solid var(--primary); display:flex; align-items:center; justify-content:center; }
                                .tl-dot i { font-size:7px; color:var(--primary); }
                                .tl-card { background:var(--bg-light); border:1px solid var(--border-color); border-radius:var(--radius-md); padding:12px 14px; }
                                .tl-top { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; }
                                .tl-title { font-size:13px; font-weight:600; color:var(--text-primary); }
                                .tl-sub { font-size:11px; color:var(--text-secondary); margin-top:1px; }
                                .tl-time { font-size:11px; color:var(--text-secondary); white-space:nowrap; text-align:right; }
                                .tl-time b { display:block; color:var(--text-primary); font-size:12px; font-weight:600; }
                                .tl-flow { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-top:10px; }
                                .tl-flow .arrow { color:var(--text-muted); font-size:11px; }
                                .tl-badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
                                .tl-meta { font-size:11.5px; color:var(--text-secondary); margin-top:8px; display:flex; flex-wrap:wrap; gap:14px; }
                                .tl-meta span b { color:var(--text-primary); font-weight:600; }
                                .tl-comment { margin-top:10px; font-size:12px; color:var(--text-secondary); background:var(--bg-white); border:1px dashed var(--border-color); border-radius:var(--radius-sm); padding:8px 12px; }
                                .tl-comment b { color:var(--text-primary); }
                                .tl-empty { padding:40px 20px; text-align:center; color:var(--text-secondary); font-size:13px; }
                            </style>
                            <div class="tl-wrap">
                                @if(!empty($updateTimeLine))
                                    <ul class="tl-list">
                                        @foreach ($updateTimeLine as $tdata)
                                            @php
                                                $tdata     = (object) $tdata;
                                                $tlDate    = date('d-M-Y', strtotime(@$tdata->created_at));
                                                $tlTime    = date('h:i A', strtotime(@$tdata->created_at));
                                                $tlBy      = ucwords(@$tdata->name);
                                                $tlCompany = ucwords(@$tdata->cur_companyname);
                                                $prevBg = $curBg = '';
                                                if (!empty(@$tdata->prev_process_name)) {
                                                    $prevBg = GeneralMethod::candidateStatusColorCode(@$tdata->prev_process_name)['tiles_bg'] ?? '';
                                                }
                                                if (!empty(@$tdata->master_process_name)) {
                                                    $curBg = GeneralMethod::candidateStatusColorCode(@$tdata->master_process_name)['tiles_bg'] ?? '';
                                                }
                                            @endphp
                                            <li class="tl-item">
                                                <span class="tl-dot"><i class="fas fa-circle"></i></span>
                                                <div class="tl-card">
                                                    <div class="tl-top">
                                                        <div>
                                                            <div class="tl-title">{{ ucwords(@$tdata->title) ?: 'Activity' }}</div>
                                                            <div class="tl-sub">{{ ucwords(GeneralMethod::companyDetailsByClient(@$tdata->job_id)) }}</div>
                                                        </div>
                                                        <div class="tl-time"><b>{{ $tlDate }}</b>{{ $tlTime }} (IST)</div>
                                                    </div>

                                                    @if(in_array(@$tdata->event_id, [1,2]))
                                                        @php
                                                            $prevName = (@$tdata->event_id == 1 && !empty(@$tdata->note)) ? @$tdata->note : @$tdata->prev_process_name;
                                                            $prevBg   = (@$tdata->event_id == 1) ? 'sourcing-bg' : $prevBg;
                                                        @endphp
                                                        <div class="tl-flow">
                                                            @if(!empty($prevName))<span class="tl-badge {{ $prevBg }}">{{ ucwords($prevName) }}</span><i class="fas fa-arrow-right arrow"></i>@endif
                                                            <span class="tl-badge {{ $curBg }}">{{ ucwords(@$tdata->master_process_name) }}</span>
                                                        </div>
                                                        @if(!empty(@$tdata->note) && @$tdata->event_id == 2)
                                                            <div class="tl-comment"><b>Comment:</b> {{ @$tdata->note }}</div>
                                                        @endif
                                                    @elseif(@$tdata->event_id == 3)
                                                        @php
                                                            $imode = '';
                                                            if (@$tdata->interview_mode > 0) {
                                                                $imodeArr = GeneralMethod::interview_mode();
                                                                $imode = @$imodeArr[@$tdata->interview_mode];
                                                            }
                                                            $iDate = !empty(@$tdata->interview_schedule_date) ? date('d-M-Y', strtotime($tdata->interview_schedule_date)) : '';
                                                            $iTime = !empty(@$tdata->interview_to_time) ? date('h:i A', strtotime($tdata->interview_to_time)) : '';
                                                        @endphp
                                                        <div class="tl-meta">
                                                            <span><b>Interview</b> {{ $imode }}</span>
                                                            @if($iDate)<span><b>When:</b> {{ $iDate }} {{ $iTime }}</span>@endif
                                                            @if(!empty(@$tdata->interviewers))<span><b>Panel:</b> {{ ucwords($tdata->interviewers) }}</span>@endif
                                                        </div>
                                                    @elseif(@$tdata->event_id == 11)
                                                        <div class="tl-flow">
                                                            <span class="tl-badge {{ (@$tdata->note == 'Interested') ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }}">{{ @$tdata->note }}</span>
                                                            <span class="tl-sub">Mail</span>
                                                        </div>
                                                        @if(!empty(@$tdata->comment))
                                                            <div class="tl-comment"><b>Subject:</b> {{ @$tdata->comment }}</div>
                                                        @endif
                                                    @else
                                                        @if(!empty(@$tdata->note))
                                                            <div class="tl-comment">{{ @$tdata->note }}</div>
                                                        @endif
                                                    @endif

                                                    <div class="tl-meta">
                                                        <span><b>By:</b> {{ $tlBy }}@if($tlCompany) — {{ $tlCompany }}@endif</span>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="tl-empty">No timeline activity yet.</div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>





        <div class="pdf-loading-overlay" id="pdfLoadingOverlay">
        <div class="pdf-spinner"></div>
        <div class="pdf-loading-text">Generating PDF... Please wait</div>
    </div>


     <!--begin::Add Interview Question Modal-->
    <div class="modal modal fade" tabindex="-1" aria-hidden="true" id="add_interview_question_modal_{{@$job->id}}">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header" id="add_team_member_header_{{@$job->id}}" style="padding: 5px !important;">
                    <h2 class="fw-bolder">Add Interview Question</h2>
                    <div class="btn btn-icon btn-sm" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>


                </div>
                <div class="modal-body scroll-y mx-5">
                        <div class="row client-cperson-row">
                            <div class="row client-cperson-container" style="position: relative;">
                                <span class="form-text text-muted form-label fw-bolder countContact"
                                    style="position: absolute; top: 0;"></span>
                                <div class="col-xl-12">
                                    <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container"
                                        id="remove">
                                        <div class="col-lg-12">
                                            <textarea id="kt_docs_tinymce_hidden_{{@$job->id}}" name="kt_docs_tinymce_hidden" class="job-editor tox-target">{!! @$interview_questions->questions !!}</textarea>
                                        </div>

                                        <input type="hidden" name="question-value" id="question-value_{{@$job->id}}"
                                            class="form-control question-value form-control-lg form-control-solid rounded me-5"
                                            value="{{ @$interview_questions->id }}" placeholder="Enter interview question">
                                        <span class="form-text text-muted">&nbsp;</span>
                                        <div class="fv-plugins-message-container"></div>
                                    </div>
                                </div>
                               
                                 <div class="text-center col-xl-12">
                                    <button class="btn btn-primary "
                                        id="saveQuestions_{{ $job->id }}"> <span class="indicator-label">Submit</span> <span
                                            class="indicator-progress">Please wait... <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span> </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
    <!--end::Add Interview Question popup-->


    <div class="toast-container" id="toastContainer"></div>

    <div class="popup-overlay" id="emailPopup">
        <div class="popup-modal">
            <div class="popup-header">
                <h3><i class="far fa-envelope" style="margin-right:8px;color:var(--primary);"></i>Send Email</h3>
                <button class="popup-close" onclick="closePopup('emailPopup')"><i class="fas fa-times"></i></button>
            </div>
            <div class="popup-body">
                <p>To: <strong id="emailRecipient">Aditi Sharma</strong></p>
                <p style="font-size:12px;color:var(--text-muted);" id="emailAddress">aditi.sharma@acg-world.com</p>
                <textarea class="popup-input" rows="4" placeholder="Type your message here..." style="margin-top:12px;resize:vertical;"></textarea>
            </div>
            <div class="popup-actions">
                <button class="popup-btn" onclick="closePopup('emailPopup')">Cancel</button>
                <button class="popup-btn primary" onclick="sendEmail()">Send Email</button>
            </div>
        </div>
    </div>

    <div class="popup-overlay" id="phonePopup">
        <div class="popup-modal">
            <div class="popup-header">
                <h3><i class="fas fa-phone" style="margin-right:8px;color:var(--success);"></i>Call Phone</h3>
                <button class="popup-close" onclick="closePopup('phonePopup')"><i class="fas fa-times"></i></button>
            </div>
            <div class="popup-body">
                <p>Call: <strong id="phoneRecipient">Aditi Sharma</strong></p>
                <div class="phone-input-wrapper" style="margin-top:12px;">
                    <select class="country-select" id="countryCode" onchange="updatePhoneFormat()">
                        <option value="+91">🇮🇳 +91</option>
                        <option value="+1">🇺🇸 +1</option>
                        <option value="+44">🇬🇧 +44</option>
                        <option value="+61">🇦🇺 +61</option>
                        <option value="+81">🇯🇵 +81</option>
                        <option value="+49">🇩🇪 +49</option>
                        <option value="+33">🇫🇷 +33</option>
                        <option value="+86">🇨🇳 +86</option>
                        <option value="+65">🇸🇬 +65</option>
                        <option value="+971">🇦🇪 +971</option>
                    </select>
                    <input type="tel" class="popup-input" id="phoneNumber" placeholder="98765 43210" style="flex:1;">
                </div>
            </div>
            <div class="popup-actions">
                <button class="popup-btn" onclick="closePopup('phonePopup')">Cancel</button>
                <button class="popup-btn primary" onclick="makeCall()"><i class="fas fa-phone" style="margin-right:6px;"></i>Call Now</button>
            </div>
        </div>
    </div>

    <div class="popup-overlay" id="editPopup">
        <div class="popup-modal">
            <div class="popup-header">
                <h3><i class="fas fa-pencil-alt" style="margin-right:8px;color:var(--primary);"></i>Edit <span id="editTitle">Basic Details</span></h3>
                <button class="popup-close" onclick="closePopup('editPopup')"><i class="fas fa-times"></i></button>
            </div>
            <div class="popup-body">
                <p style="margin-bottom:12px;">Make changes to the section below:</p>
                <div id="editFields">
                    <input type="text" class="popup-input" placeholder="Field value" style="margin-bottom:8px;">
                </div>
            </div>
            <div class="popup-actions">
                <button class="popup-btn" onclick="closePopup('editPopup')">Cancel</button>
                <button class="popup-btn primary" onclick="saveEdit()">Save Changes</button>
            </div>
        </div>
    </div>

    <div class="popup-overlay" id="candidatePopup">
        <div class="popup-modal" style="max-width:520px;">
            <div class="popup-header">
                <h3><i class="fas fa-user-plus" style="margin-right:8px;color:var(--primary);"></i>Add Candidate</h3>
                <button class="popup-close" onclick="closePopup('candidatePopup')"><i class="fas fa-times"></i></button>
            </div>
            <div class="popup-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                    <input type="text" class="popup-input" placeholder="First Name">
                    <input type="text" class="popup-input" placeholder="Last Name">
                </div>
                <input type="email" class="popup-input" placeholder="Email Address" style="margin-bottom:12px;">
                <div class="phone-input-wrapper" style="margin-bottom:12px;">
                    <select class="country-select">
                        <option value="+91">🇮🇳 +91</option>
                        <option value="+1">🇺🇸 +1</option>
                        <option value="+44">🇬🇧 +44</option>
                    </select>
                    <input type="tel" class="popup-input" placeholder="Phone Number" style="flex:1;">
                </div>
                <input type="text" class="popup-input" placeholder="Current Company">
            </div>
            <div class="popup-actions">
                <button class="popup-btn" onclick="closePopup('candidatePopup')">Cancel</button>
                <button class="popup-btn primary" onclick="addCandidate()">Add Candidate</button>
            </div>
        </div>
    </div>

    <div class="popup-overlay" id="sharePopup">
        <div class="popup-modal">
            <div class="popup-header">
                <h3><i class="fas fa-share-alt" style="margin-right:8px;color:var(--primary);"></i>Share Job</h3>
                <button class="popup-close" onclick="closePopup('sharePopup')"><i class="fas fa-times"></i></button>
            </div>
            <div class="popup-body">
                <p style="margin-bottom:12px;">Share this job via:</p>
                <div style="display:flex;gap:10px;justify-content:center;margin-bottom:16px;">
                    <button class="personnel-icon-btn-inline" style="width:44px;height:44px;font-size:15px;" onclick="showToast('Shared via LinkedIn', 'info')">
                        <i class="fab fa-linkedin" style="color:#0077b5;"></i>
                    </button>
                    <button class="personnel-icon-btn-inline" style="width:44px;height:44px;font-size:15px;" onclick="showToast('Shared via Twitter', 'info')">
                        <i class="fab fa-twitter" style="color:#1da1f2;"></i>
                    </button>
                    <button class="personnel-icon-btn-inline" style="width:44px;height:44px;font-size:15px;" onclick="showToast('Shared via WhatsApp', 'info')">
                        <i class="fab fa-whatsapp" style="color:#25d366;"></i>
                    </button>
                    <button class="personnel-icon-btn-inline" style="width:44px;height:44px;font-size:15px;" onclick="showToast('Shared via Email', 'info')">
                        <i class="far fa-envelope" style="color:var(--danger);"></i>
                    </button>
                </div>
                <div style="background:var(--bg-light);padding:12px;border-radius:8px;border:1px solid var(--border-color);">
                    <p style="font-size:11px;color:var(--text-muted);margin-bottom:4px;">Job Link</p>
                    <div style="display:flex;gap:8px;">
                        <input type="text" class="popup-input" value="{{ url('job-details/' .(@$job->id) . '/'.auth()->user()->company_id) }}" readonly style="flex:1;font-size:12px;">
                        <button class="popup-btn primary" data-url="{{ url('job-details/' .(@$job->id) . '/'.auth()->user()->company_id) }}" id="shareJobBtn" style="white-space:nowrap;"><i class="far fa-copy"></i> Copy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>





<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>


<script>
        function switchTab(btn, tabId) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }

        function toggleSubmenu(el) {
            const submenu = el.nextElementSibling;
            const chevron = el.querySelector('.chevron');
            if (submenu && submenu.classList.contains('submenu')) {
                submenu.classList.toggle('open');
                chevron.classList.toggle('rotate');
            }
        }

        function showPopup(id) {
            document.getElementById(id).classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closePopup(id) {
            document.getElementById(id).classList.remove('active');
            document.body.style.overflow = '';
        }

        function showEmailPopup(name, email) {
            document.getElementById('emailRecipient').textContent = name;
            document.getElementById('emailAddress').textContent = email;
            showPopup('emailPopup');
        }
        function sendEmail() {
            showToast('Email sent successfully!', 'success');
            closePopup('emailPopup');
        }

        function showPhonePopup(name, defaultCode) {
            document.getElementById('phoneRecipient').textContent = name;
            document.getElementById('countryCode').value = defaultCode;
            showPopup('phonePopup');
        }
        function updatePhoneFormat() {
            const code = document.getElementById('countryCode').value;
            const placeholder = code === '+91' ? '98765 43210' :
                               code === '+1' ? '(555) 123-4567' :
                               code === '+44' ? '07700 900123' : 'Phone Number';
            document.getElementById('phoneNumber').placeholder = placeholder;
        }
        function makeCall() {
            const code = document.getElementById('countryCode').value;
            const number = document.getElementById('phoneNumber').value;
            if (number) {
                showToast('Calling ' + code + ' ' + number + '...', 'info');
                closePopup('phonePopup');
            } else {
                showToast('Please enter a phone number', 'error');
            }
        }

        function showEditPopup(section) {
            document.getElementById('editTitle').textContent = section;
            showPopup('editPopup');
        }
        function saveEdit() {
            showToast('Changes saved successfully!', 'success');
            closePopup('editPopup');
        }

        function showAddCandidatePopup() {
            showPopup('candidatePopup');
        }
        function addCandidate() {
            showToast('Candidate added successfully!', 'success');
            closePopup('candidatePopup');
        }

        function showSharePopup() {
            showPopup('sharePopup');
        }
        function copyLink() {
            var urlshare = "{{ url('job-details/' .(@$job->id) . '/'.auth()->user()->company_id) }}";
            navigator.clipboard.writeText(urlshare).then(() => {
                showToast('Link copied to clipboard!', 'success');
            });
        }

        // Robust copy function that works on every card
        function copyCardText(cardId) {
            const card = document.getElementById(cardId);
            if (!card) {
                showToast('Card not found', 'error');
                return;
            }

            // Clone the card so we can strip out buttons/icons without affecting the page
            const clone = card.cloneNode(true);
            clone.querySelectorAll('button, .info-card-actions, .info-card-edit, .copy-btn, .view-more, .view-all-btn, i.fas, i.far, i.fab').forEach(el => el.remove());

            // Gather text exactly as the user sees it
            let text = clone.innerText || clone.textContent || '';

            // Clean up extra blank lines and spaces
            text = text
                .replace(/\r\n/g, '\n')
                .replace(/[ \t]+/g, ' ')
                .replace(/\n\s*\n\s*\n/g, '\n\n')
                .trim();

            if (!text) {
                showToast('No content to copy', 'error');
                return;
            }

            // Copy to clipboard with secure-context check + execCommand fallback
            const doCopy = (txt) => {
                if (navigator.clipboard && window.isSecureContext) {
                    return navigator.clipboard.writeText(txt);
                }

                const ta = document.createElement('textarea');
                ta.value = txt;
                ta.setAttribute('readonly', '');
                ta.style.cssText = 'position:fixed;left:-9999px;top:0;width:1px;height:1px;opacity:0;z-index:-1;';
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                ta.setSelectionRange(0, txt.length);

                let ok = false;
                try {
                    ok = document.execCommand('copy');
                } catch (e) {
                    ok = false;
                }
                document.body.removeChild(ta);
                return ok ? Promise.resolve() : Promise.reject(new Error('Copy failed'));
            };

            const btn = card.querySelector('.copy-btn');

            doCopy(text).then(() => {
                if (btn) {
                    btn.classList.add('copied');
                    btn.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        btn.classList.remove('copied');
                        btn.innerHTML = '<i class="far fa-copy"></i>';
                    }, 1500);
                }
                showToast('Copied to clipboard!', 'success');
            }).catch(() => {
                showToast('Copy failed. Please copy manually.', 'error');
            });
        }

        function showToast(message, type) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast ' + type;
            const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
            const color = type === 'success' ? 'var(--success)' : type === 'error' ? 'var(--danger)' : 'var(--primary)';
            toast.innerHTML = '<i class="fas fa-' + icon + '" style="color:' + color + ';"></i><span>' + message + '</span>';
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function toggleStatus() {
            showToast('Status options: Hiring, On Hold, Closed, Cancelled', 'info');
        }

        function showPipelineDetails(stage) {
            showToast(stage + ': Clicked for detailed view', 'info');
        }

        function showPersonnelDetails(role) {
            showToast('Viewing ' + role + ' profile details', 'info');
        }

        function showFullDescription() {
            showToast('Loading full job description...', 'info');
        }
        function showFullCompany() {
            showToast('Loading full company profile...', 'info');
        }
        function showAllQuestions() {
            switchTab(document.querySelector('[onclick*="questions"]'), 'questions');
        }
        function showAllActions() {
            showToast('All actions: Duplicate, Archive, Delete, Print', 'info');
        }
        function showDatePicker() {
            showToast('Date picker opened', 'info');
        }
        function showSupportPopup() {
            showToast('Support ticket system opening...', 'info');
        }
        function showEditJobPopup() {
            showEditPopup('Job Details');
        }

        document.querySelectorAll('.popup-overlay').forEach(overlay => {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.popup-overlay.active').forEach(p => p.classList.remove('active'));
                document.body.style.overflow = '';
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            showToast('Job dashboard loaded successfully', 'success');
        });

        /* ── Download Job PDF (html2pdf.js) ───────────────────────── */
        function downloadJobPDF() {
            const overlay = document.getElementById('pdfLoadingOverlay');
            overlay.classList.add('active');

            const loadScript = (src) => new Promise((resolve, reject) => {
                if (document.querySelector(`script[src="${src}"]`)) { resolve(); return; }
                const s = document.createElement('script');
                s.src = src; s.onload = resolve; s.onerror = reject;
                document.head.appendChild(s);
            });

            loadScript('https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js')
            .then(() => {
                try { _buildPDF(); }
                catch(e) {
                    console.error(e);
                    overlay.classList.remove('active');
                    showToast('PDF generation failed. Please try again.', 'error');
                }
            }).catch(() => {
                overlay.classList.remove('active');
                showToast('Failed to load PDF library.', 'error');
            });

            function _buildPDF() {

                /* ── helpers ──────────────────────────────────────── */
                // Decode any HTML entities in a string (& amp; → &, &lt; → <, etc.)
                function decodeEntities(str) {
                    const d = document.createElement('textarea');
                    d.innerHTML = str || '';
                    return d.value;
                }

                // Build a clean two-column table row — always decode entities on value
                function row(label, value, isHtml) {
                    const displayVal = isHtml ? (value || '—') : (decodeEntities(String(value || '')) || '—');
                    return `<tr>
                        <td style="padding:8px 12px 8px 0;color:#64748b;font-size:10.5px;font-weight:400;
                                   border-bottom:1px solid #f1f5f9;width:38%;vertical-align:top;
                                   white-space:nowrap;">${label}</td>
                        <td style="padding:8px 0 8px 12px;color:#1e293b;font-size:10.5px;font-weight:600;
                                   border-bottom:1px solid #f1f5f9;vertical-align:top;
                                   word-break:break-word;">${displayVal}</td>
                    </tr>`;
                }

                function section(num, title, body) {
                    return `
                    <div style="margin-bottom:24px;page-break-inside:avoid;">
                        <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:5px;
                                    padding-bottom:6px;">${num}. ${title}</div>
                        <div style="height:2px;background:linear-gradient(90deg,#1a73e8,#60a5fa);
                                    margin-bottom:14px;border-radius:2px;"></div>
                        ${body}
                    </div>`;
                }

                function subSection(title, rows) {
                    return `
                    <div style="margin-bottom:18px;page-break-inside:avoid;">
                        <div style="font-size:11px;font-weight:700;color:#1a73e8;
                                    margin-bottom:8px;padding:5px 8px;
                                    background:#eff6ff;border-radius:4px;
                                    border-left:3px solid #1a73e8;">${title}</div>
                        <table style="width:100%;border-collapse:collapse;">${rows}</table>
                    </div>`;
                }

                /* ── PHP-side clean values (no HTML icons, no raw entities) ── */
                @php
                    // Clean currency: use text symbol, not FontAwesome HTML icon
                    $currText    = html_entity_decode($currencyArr[$job->min_salary_currency ?? 0] ?? 'INR');
                    $salaryRange = $currText . ' ' . $min_salary . ' – ' . $currText . ' ' . $max_salary . ' / ' . ucfirst(strtolower($salaryMode[$job->max_sal_mode] ?? ''));
                    $salaryMin   = $currText . ' ' . $min_salary . ' / ' . ucfirst(strtolower($salaryMode[$job->min_sal_mode] ?? ''));
                    $salaryMax   = $currText . ' ' . $max_salary . ' / ' . ucfirst(strtolower($salaryMode[$job->max_sal_mode] ?? ''));
                    $payType     = html_entity_decode($salaryMode[$job->max_sal_mode] ?? '');
                    $indName     = html_entity_decode($companyOrClientDetails->ind_name ?? '');
                    $companyWeb  = html_entity_decode($companyOrClientDetails->company_website ?? '');
                    $compSize    = html_entity_decode($companyOrClientDetails->company_size ?? '');
                    $compFounded = html_entity_decode($companyOrClientDetails->founded ?? '');
                    $compType    = html_entity_decode($companyOrClientDetails->acount_type ?? '');
                    $statusLabel = $job->status == 'active' ? 'Hiring' : ($job->status == 'hold' ? 'Hold' : 'Closed');
                    $statusColor = $job->status == 'active' ? '#10b981' : ($job->status == 'hold' ? '#f59e0b' : '#ef4444');
                    $bgHex       = $job->status == 'active' ? '#dc2626' : ($job->status == 'hold' ? '#f59e0b' : '#64748b');
                @endphp

                const statusColor  = '{{ $statusColor }}';
                const statusLabel  = '{{ $statusLabel }}';
                const logoLetter   = '{{ strtoupper(substr(trim($strVal), 0, 1)) }}';
                const logoBg       = '{{ $bgHex }}';

                /* ── logo: grab from the rendered page element ──────── */
                // The .company-logo img is already loaded on the page — reuse it directly.
                const companyLogoImg = document.querySelector('.company-logo img');
                const pdfLogoUrl     = companyLogoImg ? companyLogoImg.src : '';

                const logoHtml = pdfLogoUrl
                    ? `<img src="${pdfLogoUrl}" crossorigin="anonymous"
                              style="width:64px;height:64px;border-radius:50%;object-fit:cover;
                                     border:3px solid #e2e8f0;
                                     box-shadow:0 4px 14px rgba(0,0,0,0.12);
                                     margin-bottom:12px;display:block;margin-left:auto;margin-right:auto;" />`
                    : `<div style="width:64px;height:64px;border-radius:50%;background:${logoBg};
                                   display:flex;align-items:center;justify-content:center;
                                   font-size:26px;font-weight:700;color:#fff;
                                   margin:0 auto 12px;
                                   box-shadow:0 4px 14px rgba(0,0,0,0.15);">${logoLetter}</div>`;

                /* ── collect live DOM data ───────────────────────── */
                const descEl = document.getElementById('card-desc');
                let descHtml = '<p style="color:#94a3b8;font-size:10.5px;">No description provided.</p>';
                if (descEl) {
                    const cl = descEl.cloneNode(true);
                    cl.querySelectorAll('.desc-card-header,.info-card-actions,button,i.fas,i.far').forEach(e => e.remove());
                    const txt = (cl.textContent || '').replace(/\s+/g, ' ').trim();
                    if (txt) descHtml = `<p style="font-size:10.5px;color:#475569;line-height:1.75;margin:0;">${txt}</p>`;
                }

                const aboutEl = document.getElementById('card-about');
                let aboutHtml = '<p style="color:#94a3b8;font-size:10.5px;">No company description.</p>';
                if (aboutEl) {
                    const cl = aboutEl.cloneNode(true);
                    cl.querySelectorAll('.desc-card-header,.info-card-actions,button,i.fas,i.far').forEach(e => e.remove());
                    const txt = (cl.textContent || '').replace(/\s+/g, ' ').trim();
                    if (txt) aboutHtml = `<p style="font-size:10.5px;color:#475569;line-height:1.75;margin:0;">${txt}</p>`;
                }

                const qEl    = document.getElementById('card-questions');
                const qItems = qEl ? Array.from(qEl.querySelectorAll('ol > li')) : [];
                const questionsHtml = qItems.length
                    ? '<ol style="padding-left:16px;margin:0;">' +
                      qItems.map(li =>
                        `<li style="font-size:10.5px;color:#475569;margin-bottom:7px;line-height:1.6;">${(li.textContent||'').trim()}</li>`
                      ).join('') + '</ol>'
                    : '<p style="font-size:10.5px;color:#94a3b8;">No questions added.</p>';

                /* ── skills tags ─────────────────────────────────── */
                const skillsData = [];
                @if (!empty($skillArr))
                    @foreach ($skillArr as $sval)
                        skillsData.push('{{ addslashes(ucfirst($sval->skill_name)) }}');
                    @endforeach
                @endif
                const skillsHtml = skillsData.length
                    ? skillsData.map(s =>
                        `<span style="display:inline-block;padding:4px 11px;border:1px solid #bfdbfe;
                                      border-radius:20px;font-size:10px;font-weight:500;color:#1d4ed8;
                                      background:#eff6ff;margin:3px 5px 3px 0;">${s}</span>`
                      ).join('')
                    : '<span style="font-size:10.5px;color:#94a3b8;">No skills listed.</span>';

                /* ── build full HTML template ────────────────────── */
                const html = `
                <div style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;color:#1e293b;
                            background:#fff;padding:44px 52px;max-width:760px;margin:0 auto;
                            font-size:10.5px;line-height:1.5;">

                    <!-- ═══ HEADER ═══ -->
                    <div style="text-align:center;margin-bottom:24px;">
                        ${logoHtml}
                        <div style="font-size:20px;font-weight:700;color:#0f172a;letter-spacing:-0.3px;">
                            {{ addslashes(ucwords($job->title)) }}
                        </div>
                        <div style="font-size:11px;color:#64748b;margin-top:5px;">
                            Job ID: {{ $job->id }}&nbsp;&nbsp;|&nbsp;&nbsp;{{ addslashes(ucwords($company_or_client ?? "")) }}
                        </div>
                    </div>
                    <div style="height:1px;background:#e2e8f0;margin-bottom:28px;"></div>

                    <!-- ═══ 1. COMPANY INFORMATION ═══ -->
                    ${section(1, 'Company Information', `
                        <table style="width:100%;border-collapse:collapse;">
                            ${row('Company Name', '{{ addslashes(ucwords($company_or_client ?? "")) }}')}
                            ${row('Department',   '{{ addslashes($job->cat_name ?? "") }}')}
                            ${row('Location',     '{{ addslashes(ucwords($jobCitySelected ?? "")) }}')}
                            ${row('Experience',   '{{ $job->min_exp }} – {{ $job->max_exp }} Years')}
                            ${row('Salary',       '{{ addslashes($salaryRange) }}')}
                            ${row('Shift',        '{{ addslashes($job->work_shifts ?? "") }}')}
                            ${row('Status',
                                `<span style="display:inline-block;padding:2px 10px;border-radius:20px;
                                              font-size:10px;font-weight:700;color:${statusColor};
                                              background:${statusColor}18;border:1px solid ${statusColor}40;">
                                    ${statusLabel}
                                 </span>`, true)}
                        </table>
                    `)}

                    <!-- ═══ 2. SKILLS REQUIRED ═══ -->
                    ${section(2, 'Skills Required',
                        `<div style="padding:6px 0;">${skillsHtml}</div>`
                    )}

                    <!-- ═══ 3. JOB OVERVIEW ═══ -->
                    ${section(3, 'Job Overview',
                        subSection('Basic Details', `
                            ${row('Job ID',         '{{ $job->id }}')}
                            ${row('Due Date',       '{{ date("d-M-Y", strtotime($job->end_date)) }}')}
                            ${row('Department',     '{{ addslashes($job->cat_name ?? "") }}')}
                            ${row('Qualifications', '{{ addslashes($qualification->name ?? "") }}')}
                            ${row('Position Type',  '{{ addslashes(ucwords($employment_type ?? "")) }}')}
                        `) +
                        subSection('Experience &amp; Openings', `
                            ${row('Min Experience',  '{{ $job->min_exp }} Year')}
                            ${row('Max Experience',  '{{ $job->max_exp }} Years')}
                            ${row('Gender',          '{{ addslashes($job->required_columns ?? "Rather Not Say") }}')}
                            ${row('No. of Openings', '{{ $job->total_positions }}')}
                            ${row('Job Location',    '{{ addslashes(ucwords($jobCitySelected ?? "")) }}')}
                            ${row('Work Mode',       '{{ $work_type }}')}
                            ${row('Work Shift',      '{{ addslashes($job->work_shifts ?? "") }}')}
                        `) +
                        subSection('Compensation &amp; Shift', `
                            ${row('Min Salary',     '{{ addslashes($salaryMin) }}')}
                            ${row('Max Salary',     '{{ addslashes($salaryMax) }}')}
                            ${row('Pay Type',       '{{ addslashes($payType) }}')}
                            ${row('Currency',       '{{ addslashes($currText) }}')}
                            ${row('Hiring Manager', '{{ addslashes($hiringManager ?? "") }}')}
                            ${row('Hiring HR',      '{{ addslashes($hrManager ?? "") }}')}
                        `)
                    )}

                    <!-- ═══ 4. JOB DESCRIPTION ═══ -->
                    ${section(4, 'Job Description',
                        `<div style="padding:4px 0;">${descHtml}</div>`
                    )}

                    <!-- ═══ 5. JOB QUESTIONS ═══ -->
                    ${section(5, 'Job Questions', questionsHtml)}

                    <!-- ═══ 6. ABOUT COMPANY ═══ -->
                    ${section(6, 'About Company',
                        `<div style="padding:4px 0;">${aboutHtml}</div>`
                    )}

                    <!-- ═══ 7. COMPANY DETAILS ═══ -->
                    ${section(7, 'Company Details', `
                        <table style="width:100%;border-collapse:collapse;">
                            ${row('Website',          '{{ addslashes($companyWeb) }}')}
                            ${row('Industries',       '{{ addslashes($indName) }}')}
                            ${row('No. of Employees', '{{ addslashes($compSize) }}')}
                            ${row('Founded Year',     '{{ addslashes($compFounded) }}')}
                            ${row('Company Type',     '{{ addslashes($compType) }}')}
                        </table>
                    `)}

                    <!-- ═══ FOOTER ═══ -->
                    <div style="margin-top:32px;padding-top:14px;border-top:1px solid #e2e8f0;
                                text-align:center;font-size:9px;color:#94a3b8;letter-spacing:0.3px;">
                        Generated by jero.works &nbsp;&#183;&nbsp; {{ addslashes(ucwords($job->title)) }} &nbsp;&#183;&nbsp; Job ID: {{ $job->id }}
                    </div>

                </div>`;

                /* ── render with html2pdf.js (from HTML string) ─────── */
                html2pdf()
                    .set({
                        margin:      [10, 10, 10, 10],
                        filename:    'Job_{{ $job->id }}_{{ Str::slug($job->title) }}.pdf',
                        image:       { type: 'jpeg', quality: 0.98 },
                        html2canvas: {
                            scale:           2,
                            useCORS:         true,
                            allowTaint:      true,
                            logging:         false,
                            backgroundColor: '#ffffff'
                        },
                        jsPDF:     { unit: 'mm', format: 'a4', orientation: 'portrait' },
                        pagebreak: { mode: ['css', 'legacy'] }
                    })
                    .from(html)
                    .save()
                    .then(() => {
                        overlay.classList.remove('active');
                        showToast('PDF downloaded successfully!', 'success');
                    })
                    .catch(err => {
                        console.error(err);
                        overlay.classList.remove('active');
                        showToast('PDF generation failed. Please try again.', 'error');
                    });
            }
        }
    </script>



<script>
    
var jobIdQuestion = "<?php echo @$job->id; ?>";
var questionList = document.getElementById(`questionList_${jobIdQuestion}`);
var addBtn = document.getElementById(`addQuestionBtn_${jobIdQuestion}`);
var saveBtn = document.getElementById(`saveQuestions_${jobIdQuestion}`);


if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
    CKEDITOR.tools.enableHtml5Elements(document);
}

// CKEDITOR.config.height = 'auto';
CKEDITOR.config.width = 'auto';

document.querySelectorAll('.job-editor').forEach(function (editorBox) {

    CKEDITOR.replace(editorBox.id, {
        height: 400,
        toolbarGroups: [
            { name: 'clipboard', groups: ['clipboard', 'undo'] },
            { name: 'editing', groups: ['find', 'selection'] },
            { name: 'links' },
            { name: 'insert' },
            { name: 'tools' },
            { name: 'basicstyles', groups: ['basicstyles'] },
            { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align'] },
            { name: 'styles' },
            { name: 'colors' },
            { name: 'customFields', groups: ['customFields'] }
        ]
    });

});



document.getElementById(`saveQuestions_${jobIdQuestion}`).addEventListener('click', function () {

    let questions = [];
  
    
    document.querySelectorAll('.question-value').forEach(input => {
        if (input.value.trim() !== '') {
            questions.push(input.value.trim());
        }
    });
    var data = CKEDITOR.instances['kt_docs_tinymce_hidden_' + jobIdQuestion].getData();
    var questionsId = $('#question-value_'+jobIdQuestion).val();
    // if (questions.length === 0) {
    //     alert('Please add at least one question');
    //     return;
    // }

    showButtonLoader(saveBtn.id);

    fetch("{{ route('questions.questionsStore') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            job_id: jobIdQuestion,
            questions: data,            
            questionsId: questionsId,
            deleteChk: 'No'
        })
    })
    .then(res => res.json())
    .then(response => {
        $(`#add_interview_question_modal_${jobIdQuestion}`).modal('hide');
        $(`#questionList_${jobIdQuestion}`).html(response.html);
        //renumberQuestions(questionList);
         hideButtonLoader(saveBtn.id);
         Swal.fire({
            icon: 'success',
            title: 'Success',
            text: response.message,
            timer: 2000,
            showConfirmButton: false
        });
    })
    .catch(error => {
        console.error(error);
        hideButtonLoader(saveBtn.id);
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: 'Something went wrong. Please try again.',
        });
    });

});

setTimeout(() => {
        // alert("k");
        $('.iti__flag-container').each(function(index) {
            var cnt = $(this).attr('data-row');
            const flagContainer = document.getElementById(`phoneDisplayReal_${cnt}`);
            var calling_code = $(this).attr('data-code');
            
            const flagDiv = document.createElement("div");
            flagDiv.className = `iti__flag iti__${calling_code}`; // Ensure calling_code is in lowercase

            const dialCode = countryDialCodes[calling_code] || ""; // Fetch the dial code dynamically
            const dialCodeSpan = document.createElement("span");
            dialCodeSpan.textContent = dialCode;

            // Append the flag and dial code
            flagContainer.appendChild(flagDiv);
            flagContainer.appendChild(dialCodeSpan);


        });
}, 2000);

// Add new question
addBtn.addEventListener('click', () => {
    var captainAccessCheck = Number('{{@$captainAccessCheck}}');
    if (captainAccessCheck !== 1) {
       Swal.fire({
            icon: 'info',
            title: 'Access Restricted',
            text: '{{@$alertmessage}}',
        });
        return;
    }
   $(`#add_interview_question_modal_${jobIdQuestion}`).modal('show');
});

function showButtonLoader(btnId) {
    var btn = document.getElementById(btnId);
    btn.setAttribute('data-kt-indicator', 'on');
    btn.disabled = true;
}

function hideButtonLoader(btnId) {
    var btn = document.getElementById(btnId);
    btn.removeAttribute('data-kt-indicator');
    btn.disabled = false;
}


// addBtn.addEventListener('click', () => {
//     var li = document.createElement('li');
//     li.className = 'list-group-item question-item';

//     li.innerHTML = `<div class="d-flex align-items-start justify-content-between gap-3"> <!-- Question Text --> <div class="flex-grow-1"> <div class="fw-semibold text-dark question-text"><span class="question-number" style="font-weight: 900;"></span> New Question </div> </div> <!-- Actions --> <div class="d-flex gap-2"> <button class="btn btn-sm btn-light border toggle-question" title="Edit Question"> ✏️ </button> <button class="btn btn-sm btn-light border text-danger remove-question" title="Delete Question"> 🗑️ </button> </div> </div> <!-- Edit Input --> <div class="question-input mt-3"> <input type="text" class="form-control form-control-sm question-value" value="" placeholder="Enter interview question"> </div>`;

//     questionList.appendChild(li);
//     renumberQuestions(questionList);
// });



// Event delegation
questionList.addEventListener('click', function (e) {

    // Edit toggle
    if (e.target.classList.contains('toggle-question')) {
        var item = e.target.closest('.question-item');
        var inputBox = item.querySelector('.question-input');

        inputBox.classList.toggle('d-none');
        e.target.textContent = inputBox.classList.contains('d-none') ? 'Edit' : 'Close';
    }

    // Delete question
    if (e.target.classList.contains('remove-question')) {

        var captainAccessCheck = Number('{{@$captainAccessCheck}}');
        if(captainAccessCheck !== 1) {
            Swal.fire({
                    icon: 'info',
                    title: 'Access Restricted',
                    text: '{{@$alertmessage}}',
                });
            return;
        }


        Swal.fire({
            title: 'Are you sure?',
            text: 'This question will be permanently deleted',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) {
                
                e.target.closest('.question-item').remove();
                var btn = e.target;
                var id = btn.dataset.id; // ✅ correct way

                // Remove from UI
              fetch("{{ route('questions.questionsStore') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            job_id: jobIdQuestion,          
                            questionsId: id,
                            questions: id,
                            deleteChk: 'Yes'
                        })
                    })
                    .then(res => res.json())
                    .then(response => {
                        $(`#add_interview_question_modal_${jobIdQuestion}`).modal('hide');
                        renumberQuestions(questionList);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Something went wrong. Please try again.',
                        });
                    });
            }
        });

        renumberQuestions(questionList);
    }
});

// Sync input value to text
questionList.addEventListener('input', function (e) {
    if (e.target.classList.contains('question-value')) {
        var item = e.target.closest('.question-item');
        item.querySelector('.question-text').textContent = e.target.value || 'New Question';
        renumberQuestions(questionList);
    }
});

function renumberQuestions(questionList) {
    var items = questionList.querySelectorAll('.question-item');

    items.forEach((item, index) => {
        var textDiv = item.querySelector('.question-text');
        let numberSpan = textDiv.querySelector('.question-number');

        if (!numberSpan) {
            numberSpan = document.createElement('span');
            numberSpan.className = 'question-number';
            textDiv.prepend(numberSpan);
        }

        numberSpan.textContent = `Q.${index + 1} `;
    });
}

setTimeout(() => {
    renumberQuestions(questionList);
}, 500);

</script>



        <script>

             var myChartId = "<?php echo @$job->id; ?>";

             var showDataLabels = {
                    id: 'showDataLabels',
                    afterDatasetsDraw(chart) {
                        const { ctx } = chart;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const value = dataset.data[index];
                                ctx.save();
                                ctx.fillStyle = 'black';
                                ctx.font = 'bold 12px sans-serif';
                                ctx.textAlign = 'center';
                                ctx.fillText(value, bar.x, bar.y - 1.5);
                                ctx.restore();
                            });
                        });
                    }
            };


            var showDataLabelsvertical = {
                id: 'showDataLabelsvertical',
                afterDatasetsDraw(chart) {
                    var { ctx } = chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        var meta = chart.getDatasetMeta(i);
                        meta.data.forEach((bar, index) => {
                            var value = dataset.data[index];
                            ctx.save();
                            ctx.fillStyle = 'black';
                            ctx.font = 'bold 12px sans-serif';
                            ctx.textAlign = 'left';  // align left for horizontal bars
                            ctx.textBaseline = 'middle';

                            // place text at the end of bar (right side)
                            ctx.fillText(value, bar.x + 1, bar.y);
                            ctx.restore();
                        });
                    });
                }
            };

            function loadCountryChart() {
                var ctx = document.getElementById(`myChart_${myChartId}`).getContext('2d');
                var countryChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['In Review','Sourcing', 'Screening', 'Evaluation', 'Negotiation', 'Offer','Joined','Rejected'],
                    datasets: [{
                        label: 'Users',
                        data: ["{{$inreview_count}}", "{{$sourcing_count}}", "{{$screening_count}}", "{{$evaluation_count}}", "{{$negotiation_count}}", "{{$offer_count}}", "{{$joined_count}}","{{$Rejected_count}}"],
                        backgroundColor: [
                            '#bdbd26', // inreview_count
                            '#000000', // sourcing_count
                            '#c000ff', // screening_count
                            '#2896ff', // evaluation_count
                            '#ff4098', // negotiation_count
                            '#ffab00', // offer_count
                            '#06c732',
                            '#ff0202' // joined_count // joined_count
                        ],
                        borderRadius: 10,
                        barThickness: 30
                    }]
                },
                options: {
                    indexAxis: 'y', // makes it horizontal
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.parsed.x.toLocaleString()
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    if (value <= 10) return value;
                                    else if (value % 100 === 0) return value.toLocaleString();
                                    else return '';
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                },
                plugins: [showDataLabelsvertical] // ✅ custom labels plugin
               });
            } 

            setTimeout(() => {
                  loadCountryChart();
            }, 300); 


        var candidates_left_chart = document.getElementById(`candidates_left_chart_${myChartId}`).getContext('2d');

        var allJobGraphBar = new Chart(candidates_left_chart, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Total Candidates',
                    data: {!! json_encode($candidateValues) !!},
                    backgroundColor: [
                        '#2196f3', // Blue 
                        '#2196f3', // Blue 
                        '#2196f3', // Blue 
                        '#2196f3', // Blue 
                        '#2196f3', // Blue 
                        '#2196f3', // Blue 
                        '#2196f3'  // Blue 
                    ],
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0 // show integer values only
                        }
                    }
                }
            },
            plugins: [showDataLabels] // ✅ custom labels plugin
        });


            
            var candidates_offer_chart = document.getElementById(`candidates_offer_chart_${myChartId}`).getContext('2d');

            var allofferGraphBar = new Chart(candidates_offer_chart, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labelsCommoffer) !!},
                    datasets: [{
                        label: 'Total Candidates',
                        data: {!! json_encode($candidateValuesCommoffer) !!},
                        backgroundColor: [
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00' 
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });

            
            var candidates_join_chart = document.getElementById(`candidates_join_chart_${myChartId}`).getContext('2d');

            var alljoinGraphBar = new Chart(candidates_join_chart, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labelsCommjoin) !!},
                    datasets: [{
                        label: 'Total Candidates',
                        data: {!! json_encode($candidateValuesCommjoin) !!},
                        backgroundColor: [
                            '#06c732',  
                            '#06c732',  
                            '#06c732',  
                            '#06c732',  
                            '#06c732',  
                            '#06c732'  
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });

            var inetview_recent_chart_month = document.getElementById(`inetview_recent_chart_month_${myChartId}`).getContext('2d');

            var netview_recentGraphBarMonth = new Chart(inetview_recent_chart_month, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($interviewlabelsMonth) !!},
                    datasets: [{
                        label: 'Total Interviews',
                        data: {!! json_encode($interviewValuesMonth) !!},
                        backgroundColor: [
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3'  // Blue 
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });

            var inetview_recent_chart = document.getElementById(`inetview_recent_chart_${myChartId}`).getContext('2d');

            var netview_recentGraphBar = new Chart(inetview_recent_chart, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($interviewlabels) !!},
                    datasets: [{
                        label: 'Total Interviews',
                        data: {!! json_encode($interviewValues) !!},
                        backgroundColor: [
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3'  // Blue 
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });


            function filtersdatagetmonth(valSelected){
                if(valSelected==1){
                      $(`.inetview_recent_chart_month_${myChartId}`).removeClass('d-none');
                      $(`.inetview_recent_chart_${myChartId}`).addClass('d-none');
                }else{
                      $(`.inetview_recent_chart_month_${myChartId}`).addClass('d-none');
                      $(`.inetview_recent_chart_${myChartId}`).removeClass('d-none');
                }

            }






            
    </script>
    

@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script>
        loadTable();
        var table;

        function loadTable() {

            var hid_job_id = $('#hid_job_id').val();
            table = $('#interviewData').dataTable({

                responsive: true,
                processing: true,
                serverSide: true,
                destroy: true,
                ordering: false, //disable ordering														
                "bLengthChange": false, //thought this line could hide the LengthMenu														
                sDom: 'lrtip',
                oLanguage: {
                    sLengthMenu: "_MENU_",
                },
                ajax: '{!! route('service_company.interview-schedule.interviewData') !!}?hid_job_id=' + hid_job_id,

                "fnDrawCallback": function(settings, start, end, max, total, pre) {

                    var total_record = this.fnSettings().fnRecordsTotal();
                    $('#total_record').html(total_record);
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },

                "columnDefs": [{
                        "targets": 0
                    },
                    {
                        "targets": 1,
                        "class": "align-items-center"
                    },
                    {
                        "targets": 2
                    },
                    {
                        "targets": 3
                    },
                    {
                        "targets": 4
                    },
                    {
                        "targets": 5
                    },
                    {
                        "targets": 6
                    },
                    {
                        "targets": 7
                    },
                    {
                        "targets": 8
                    },
                    {
                        "targets": 9
                    }
                ],
                columns: [{
                        data: 'chkbox',
                        name: 'chkbox',
                        width: '1%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Name',
                        name: 'Name',
                        width: '15%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Requisitions',
                        name: 'Requisitions',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'InterviewMode',
                        name: 'InterviewMode',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Interviewers',
                        name: 'Interviewers',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Source',
                        name: 'Source',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'InterviewDate',
                        name: 'InterviewDate',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Feedback',
                        name: 'Feedback',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Rating',
                        name: 'Rating',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Stage',
                        name: 'Stage',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    }
                ],

            });
            new $.fn.dataTable.FixedHeader(table);

        }
    </script>
@endpush
