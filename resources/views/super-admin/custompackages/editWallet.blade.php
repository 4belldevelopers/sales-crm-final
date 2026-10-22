<?php
use App\Helper\GeneralMethod;
?>

<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">

<style>
    /* Scoped Super Admin Wallet Drawer (Consultancy Reference Parity) */
    .sa-wallet-drawer {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #181c32;
        background: #f5f8fa;
        overflow: visible !important;
        height: auto !important;
        max-height: none !important;
    }

    .sa-wallet-drawer#kt_activities_body,
    .sa-wallet-drawer.card-body {
        overflow: visible !important;
        height: auto !important;
        max-height: none !important;
    }

    .sa-wallet-drawer #kt_activities_scroll {
        padding: 12px 16px 80px 16px !important;
        background: #f5f8fa !important;
        overflow: visible !important;
        height: auto !important;
        max-height: none !important;
    }

    .sa-wallet-drawer .profile-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #eff2f5;
        padding: 16px 20px;
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .sa-wallet-drawer .logo-circle {
        position: relative;
        width: 56px;
        height: 56px;
        border-radius: 12px;
        border: 1.5px solid #e4e6ef;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sa-wallet-drawer .profile-name {
        font-size: 18px;
        font-weight: 700;
        color: #181c32;
        line-height: 1.25;
        margin: 0;
    }

    .sa-wallet-drawer .profile-meta {
        display: flex;
        align-items: center;
        gap: 6px 14px;
        flex-wrap: wrap;
        font-size: 12px;
        color: #7e8299;
        margin-top: 4px;
    }

    .sa-wallet-drawer .meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .sa-wallet-drawer .meta-item i {
        font-size: 11px;
        color: #a1a5b7;
    }

    .sa-wallet-drawer .meta-divider {
        color: #d1d5db;
        font-size: 10px;
    }

    .sa-wallet-drawer .wallet-table-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #eff2f5;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }

    .sa-wallet-drawer table#currentPackage th {
        background: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 10px 12px;
        border-bottom: 1.5px solid #e2e8f0;
    }

    .sa-wallet-drawer table#currentPackage td {
        padding: 8px 12px;
        vertical-align: middle;
        font-size: 12.5px;
        border-bottom: 1px solid #f1f5f9;
    }

    .sa-wallet-drawer .ele_border {
        border: 1px solid #dce3ee !important;
        background: #f8fafc !important;
        border-radius: 6px !important;
        height: 36px !important;
        min-width: 90px;
        padding: 4px 8px;
        font-size: 12px;
        transition: border-color 0.15s ease;
    }

    .sa-wallet-drawer .ele_border:focus {
        border-color: #2563eb !important;
        background: #ffffff !important;
        outline: none;
    }

    .sa-wallet-drawer .ele_border_merge {
        border: 1px solid #dce3ee !important;
        background: #f8fafc !important;
        border-radius: 6px !important;
        height: 36px !important;
        width: 100% !important;
        max-width: 420px;
        padding: 4px 10px;
        font-size: 12px;
        transition: border-color 0.15s ease;
    }

    .sa-wallet-drawer .ele_border_merge:focus {
        border-color: #2563eb !important;
        background: #ffffff !important;
        outline: none;
    }

    .sa-wallet-drawer .input-xs,
    .sa-wallet-drawer select.input-xs {
        height: 36px !important;
        border: 1px solid #dce3ee !important;
        background: #f8fafc !important;
        border-radius: 6px !important;
        font-size: 12px;
        padding: 4px 8px;
    }

    .sa-wallet-drawer .input-xs:focus,
    .sa-wallet-drawer select.input-xs:focus {
        border-color: #2563eb !important;
        background: #ffffff !important;
        outline: none;
    }

    /* Pinned Sticky Footer */
    .sa-wallet-drawer .sa-drawer-footer {
        position: sticky;
        bottom: 0;
        left: 0;
        right: 0;
        background: #ffffff;
        border-top: 1px solid #edf2f7;
        padding: 12px 24px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        z-index: 20;
        box-shadow: 0 -4px 14px rgba(0, 0, 0, 0.05);
    }
</style>
<?php

//echo '<pre>'; print_r(@$packageDetail); exit;

$bgTextDecore = $strVal = '';
$str = trim(@$packageDetail->company_name);
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

$company_type = '';
$company_type_arr = GeneralMethod::accountCompanyType();
if (@$packageDetail->company_type == 1) {
    $company_type = $company_type_arr[@$packageDetail->company_type];
} elseif (@$packageDetail->company_type == 2) {
    $company_type = $company_type_arr[@$packageDetail->company_type];
} else {
    $company_type = $company_type_arr[3];
}

$cvaccess_startdate = $cvaccess_enddate = $cvaccess_status = $cvaccess_quantity = $cvaccess_price = $cvaccess_totalprice = '';
$cvaccess_startdate = @$packageDetail->cvaccess_startdate ? date('d M, Y', strtotime(@$packageDetail->cvaccess_startdate)) : '&nbsp;';
$cvaccess_enddate = @$packageDetail->cvaccess_enddate ? date('d M, Y', strtotime(@$packageDetail->cvaccess_enddate)) : '&nbsp;';
$cvaccess_status = @$packageDetail->cvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>' : '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';
$cvaccess_quantity = @$packageDetail->cvaccess_quantity ? number_format(@$packageDetail->cvaccess_quantity) : '&nbsp;';
$cvaccess_price = @$packageDetail->cvaccess_price ? number_format(@$packageDetail->cvaccess_price, 2) : '&nbsp;';
$cvaccess_totalprice = @$packageDetail->cvaccess_totalprice > 0 ? number_format(@$packageDetail->cvaccess_totalprice, 2) : '&nbsp;';

$users_startdate = $users_enddate = $users_status = $users_quantity = $users_price = $users_totalprice = '';
$users_startdate = @$packageDetail->users_startdate ? date('d M, Y', strtotime(@$packageDetail->users_startdate)) : '&nbsp;';
$users_enddate = @$packageDetail->users_enddate ? date('d M, Y', strtotime(@$packageDetail->users_enddate)) : '&nbsp;';
$users_status = @$packageDetail->users_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>' : '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';
$users_quantity = @$packageDetail->users_quantity ? number_format(@$packageDetail->users_quantity) : '&nbsp;';
$users_price = @$packageDetail->users_price ? number_format(@$packageDetail->users_price, 2) : '&nbsp;';
$users_totalprice = @$packageDetail->users_totalprice > 0 ? number_format(@$packageDetail->users_totalprice, 2) : '&nbsp;';

$newclientrequest_startdate = $newclientrequest_enddate = $newclientrequest_status = $newclientrequest_quantity = $newclientrequest_price = $newclientrequest_totalprice = '';
$newclientrequest_startdate = @$packageDetail->newclientrequest_startdate ? date('d M, Y', strtotime(@$packageDetail->newclientrequest_startdate)) : '&nbsp;';
$newclientrequest_enddate = @$packageDetail->newclientrequest_enddate ? date('d M, Y', strtotime(@$packageDetail->newclientrequest_enddate)) : '&nbsp;';
$newclientrequest_status = @$packageDetail->newclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>' : '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';
$newclientrequest_quantity = @$packageDetail->newclientrequest_quantity ? number_format(@$packageDetail->newclientrequest_quantity) : '&nbsp;';
$newclientrequest_price = @$packageDetail->newclientrequest_price ? number_format(@$packageDetail->newclientrequest_price, 2) : '&nbsp;';
$newclientrequest_totalprice = @$packageDetail->newclientrequest_totalprice > 0 ? number_format(@$packageDetail->newclientrequest_totalprice, 2) : '&nbsp;';

$newjobrequest_startdate = $newjobrequest_enddate = $newjobrequest_status = $newjobrequest_quantity = $newjobrequest_price = $newjobrequest_totalprice = '';
$newjobrequest_startdate = @$packageDetail->newjobrequest_startdate ? date('d M, Y', strtotime(@$packageDetail->newjobrequest_startdate)) : '&nbsp;';
$newjobrequest_enddate = @$packageDetail->newjobrequest_enddate ? date('d M, Y', strtotime(@$packageDetail->newjobrequest_enddate)) : '&nbsp;';
$newjobrequest_status = @$packageDetail->newjobrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>' : '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';
$newjobrequest_quantity = @$packageDetail->newjobrequest_quantity ? number_format(@$packageDetail->newjobrequest_quantity) : '&nbsp;';
$newjobrequest_price = @$packageDetail->newjobrequest_price ? number_format(@$packageDetail->newjobrequest_price, 2) : '&nbsp;';
$newjobrequest_totalprice = @$packageDetail->newjobrequest_totalprice > 0 ? number_format(@$packageDetail->newjobrequest_totalprice, 2) : '&nbsp;';

$newbenchclientrequest_startdate = $newbenchclientrequest_enddate = $newbenchclientrequest_status = $newbenchclientrequest_quantity = $newbenchclientrequest_price = $newbenchclientrequest_totalprice = '';
$newbenchclientrequest_startdate = @$packageDetail->newbenchclientrequest_startdate ? date('d M, Y', strtotime(@$packageDetail->newbenchclientrequest_startdate)) : '&nbsp;';
$newbenchclientrequest_enddate = @$packageDetail->newbenchclientrequest_enddate ? date('d M, Y', strtotime(@$packageDetail->newbenchclientrequest_enddate)) : '&nbsp;';
$newbenchclientrequest_status = @$packageDetail->newbenchclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>' : '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';
$newbenchclientrequest_quantity = @$packageDetail->newbenchclientrequest_quantity ? number_format(@$packageDetail->newbenchclientrequest_quantity) : '&nbsp;';
$newbenchclientrequest_price = @$packageDetail->newbenchclientrequest_price ? number_format(@$packageDetail->newbenchclientrequest_price, 2) : '&nbsp;';
$newbenchclientrequest_totalprice = @$packageDetail->newbenchclientrequest_totalprice > 0 ? number_format(@$packageDetail->newbenchclientrequest_totalprice, 2) : '&nbsp;';

$emails_startdate = $emails_enddate = $emails_status = $emails_quantity = $emails_price = $emails_totalprice = '';
$emails_startdate = @$packageDetail->emails_startdate ? date('d M, Y', strtotime(@$packageDetail->emails_startdate)) : '&nbsp;';
$emails_enddate = @$packageDetail->emails_enddate ? date('d M, Y', strtotime(@$packageDetail->emails_enddate)) : '&nbsp;';
$emails_status = @$packageDetail->emails_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>' : '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';
$emails_quantity = @$packageDetail->emails_quantity ? number_format(@$packageDetail->emails_quantity) : '&nbsp;';
$emails_price = @$packageDetail->emails_price ? number_format(@$packageDetail->emails_price, 2) : '&nbsp;';
$emails_totalprice = @$packageDetail->emails_totalprice > 0 ? number_format(@$packageDetail->emails_totalprice, 2) : '&nbsp;';

$oldcvaccess_startdate = $oldcvaccess_enddate = $oldcvaccess_status = $oldcvaccess_quantity = $oldcvaccess_price = $oldcvaccess_totalprice = '';
$oldcvaccess_startdate = @$packageDetail->oldcvaccess_startdate ? date('d M, Y', strtotime(@$packageDetail->oldcvaccess_startdate)) : '&nbsp;';
$oldcvaccess_enddate = @$packageDetail->oldcvaccess_enddate ? date('d M, Y', strtotime(@$packageDetail->oldcvaccess_enddate)) : '&nbsp;';
$oldcvaccess_status = @$packageDetail->oldcvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>' : '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';
$oldcvaccess_quantity = @$packageDetail->oldcvaccess_quantity ? number_format(@$packageDetail->oldcvaccess_quantity) : '&nbsp;';
$oldcvaccess_price = @$packageDetail->oldcvaccess_price ? number_format(@$packageDetail->oldcvaccess_price, 2) : '&nbsp;';
$oldcvaccess_totalprice = @$packageDetail->oldcvaccess_totalprice > 0 ? number_format(@$packageDetail->oldcvaccess_totalprice, 2) : '&nbsp;';

?>


<div class="card-body position-relative px-0 py-0 sa-wallet-drawer" id="kt_activities_body">
    <div id="kt_activities_scroll" class="position-relative scroll-y pt-0" data-kt-scroll="true"
        data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
        data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
        
        <!--begin::Company Profile Card-->
        <div class="profile-card">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <div class="logo-circle">
                    <div class="d-flex align-items-center justify-content-center w-100 h-100 fw-bolder fs-4 <?php echo @$bgTextDecore; ?> rounded-2">
                        <?php echo strtoupper($strVal); ?>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                        <h2 class="profile-name"><?php echo @$packageDetail->company_name; ?></h2>
                        <div>
                            <?php if ($packageDetail->status == 'active') { ?>
                                <span class="badge badge-light-success fw-bold py-1 px-3">Active</span>
                            <?php } else { ?>
                                <span class="badge badge-light-danger fw-bold py-1 px-3">Inactive</span>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="profile-meta">
                        <?php if (!empty(@$packageDetail->company_email)) { ?>
                            <div class="meta-item">
                                <i class="fa-solid fa-envelope"></i>
                                <span><?php echo @$packageDetail->company_email; ?></span>
                            </div>
                            <span class="meta-divider">•</span>
                        <?php } ?>
                        <?php if (!empty(@$packageDetail->company_phone)) { ?>
                            <div class="meta-item">
                                <i class="fa-solid fa-phone"></i>
                                <span><?php echo @$packageDetail->company_phone; ?></span>
                            </div>
                            <span class="meta-divider">•</span>
                        <?php } ?>
                        <?php if (!empty(@$company_type)) { ?>
                            <div class="meta-item">
                                <i class="fa-solid fa-briefcase"></i>
                                <span><?php echo @$company_type; ?></span>
                            </div>
                            <span class="meta-divider">•</span>
                        <?php } ?>
                        <div class="meta-item">
                            <i class="fa-solid fa-users"></i>
                            <span><?php echo @$packageDetail->company_size ? @$packageDetail->company_size : '0'; ?> Size</span>
                        </div>
                        <?php if (!empty(@$packageDetail->website)) { ?>
                            <span class="meta-divider">•</span>
                            <div class="meta-item">
                                <i class="fa-solid fa-globe"></i>
                                <a href="<?php echo (strpos(@$packageDetail->website, 'http') === 0) ? @$packageDetail->website : 'https://'.@$packageDetail->website; ?>" target="_blank" class="text-primary"><?php echo @$packageDetail->website; ?></a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Company Profile Card-->

        <div class="card card-custom mx-0 border-0 bg-transparent shadow-none">
            <div class="card-header border-0 px-0 min-h-40px mb-2">
                <h4 class="fw-bolder m-0 text-gray-800 fs-5 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-wallet text-warning fs-6"></i>
                    <span>Package & Wallet Features Allocation</span>
                </h4>
            </div>
            <div class="card-body py-0">
                <div class="tab-content pt-5">
                    <div class="tab-pane active show" id="job_description" role="tabpanel">
                        <!--<div class="card-header cursor-pointer px-0">
     <div class="card-title m-0">
      <h3 class="fw-bolder m-0">Detail</h3>
     </div>
    </div>-->

                        <div class="card-body px-0 py-7">

                            <div class="row">

                                <div class="card" style="margin-top:10px;">
                                    <div class="card-body">

                                        <div class="table-responsive">



                                            <form class="ajax-form" method="POST" id="createForm"
                                                name="createForm">
                                                @csrf

                                                <table
                                                    class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4"
                                                    id="currentPackage">
                                                    <thead class="">

                                                        <tr class="fw-bolder text-muted">
                                                            <th class="min-w-200px">Product</th>
                                                            <th class="min-w-140px">Quantity Allocated</th>
                                                            <th class="min-w-140px">Consumed</th>
                                                            <th class="min-w-140px">Remaining</th>
                                                            {{-- <th class="min-w-140px">Start Date</th>
                                                            <th class="min-w-140px">End Date</th> --}}
                                                            <th class="min-w-140px">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="">

													@if(@$package_active_all)

														@foreach($package_active_all as $package_active)
														@php
														  $field_name = @$package_active->field_name;
														  $field_name_quantity  = $package_active->field_name."_quantity";
														  $field_name_used  = $package_active->field_name."_used";
														  $field_name_balance  = $package_active->field_name."_balance";
														 
				
														@endphp


                                                        <tr>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
																	{{ @$package_active->title }} 
                                                                </a>
                                                            </td>
                                                            <td>															
                                                                <input type="number" id="{{ @$package_active->field_name }}_quantity"
                                                                    name="{{ @$package_active->field_name }}_quantity"
                                                                    value="<?php echo @$package_active->$field_name_quantity ? (@$package_active->$field_name_quantity) : 0; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="number" id="{{ @$package_active->field_name }}_used"
                                                                    name="{{ @$package_active->field_name }}_used" value="<?php echo @$package_active->$field_name_used ? (@$package_active->$field_name_used) : 0; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="number" id="{{ @$package_active->field_name }}_balance"
                                                                    name="{{ @$package_active->field_name }}_balance"
                                                                    value="<?php echo @$package_active->$field_name_balance ? (@$package_active->$field_name_balance) : 0; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                           
                                                            
                                                            <td class="">
                                                                <select name="{{ @$package_active->field_name }}_status" id="{{ @$package_active->field_name }}_status"
                                                                    class="form-control form-control-sm form-control-solid ele_border">
                                                                    <option value="1" <?php echo @$packageDetail->cvaccess_status == 1 ? 'selected="selected"' : ''; ?>>Active
                                                                    </option>
                                                                    <option value="0" <?php echo @$packageDetail->cvaccess_status != 1 ? 'selected="selected"' : ''; ?>>
                                                                        InActive</option>
                                                                </select>
                                                            </td>
                                                        </tr>

														@endforeach
													@endif

                                                        {{-- <tr>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    Users
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <input type="number" id="users_quantity"
                                                                    name="users_quantity" value="<?php echo @$packageDetail->users_quantity; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="number" id="users_used"
                                                                    name="users_used" value="<?php echo @$packageDetail->users_used; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="number" id="users_balance"
                                                                    name="users_balance" value="<?php echo @$packageDetail->users_balance; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="date" id="users_startdate"
                                                                    name="users_startdate"
                                                                    value="<?php echo @$packageDetail->users_startdate; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    onfocus="this.showPicker()" />
                                                            </td>
                                                            <td>
                                                                <input type="date" id="users_enddate"
                                                                    name="users_enddate" value="<?php echo @$packageDetail->users_enddate; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    onfocus="this.showPicker()" />
                                                            </td>
                                                            <td>
                                                                <select name="users_status" id="users_status"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    style="border:1px solid #666666; width:100px;">
                                                                    <option value="1" <?php echo @$packageDetail->users_status == 1 ? 'selected="selected"' : ''; ?>>Active
                                                                    </option>
                                                                    <option value="0" <?php echo @$packageDetail->users_status != 1 ? 'selected="selected"' : ''; ?>>
                                                                        InActive</option>
                                                                </select>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    New Bench Client Request
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <input type="number"
                                                                    name="newbenchclientrequest_quantity"
                                                                    id="newbenchclientrequest_quantity"
                                                                    value="<?php echo @$packageDetail->newbenchclientrequest_quantity; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="number"
                                                                    name="newbenchclientrequest_used"
                                                                    id="newbenchclientrequest_used"
                                                                    value="<?php echo @$packageDetail->newbenchclientrequest_used; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="number"
                                                                    name="newbenchclientrequest_balance"
                                                                    id="newbenchclientrequest_balance"
                                                                    value="<?php echo @$packageDetail->newbenchclientrequest_balance; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="date"
                                                                    name="newbenchclientrequest_startdate"
                                                                    id="newbenchclientrequest_startdate"
                                                                    value="<?php echo @$packageDetail->newbenchclientrequest_startdate; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    onfocus="this.showPicker()" />
                                                            </td>
                                                            <td>
                                                                <input type="date"
                                                                    name="newbenchclientrequest_enddate"
                                                                    id="newbenchclientrequest_enddate"
                                                                    value="<?php echo @$packageDetail->newbenchclientrequest_enddate; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    onfocus="this.showPicker()" />
                                                            </td>
                                                            <td class="">
                                                                <select name="newbenchclientrequest_status"
                                                                    id="newbenchclientrequest_status"
                                                                    class="form-control form-control-sm form-control-solid ele_border">
                                                                    <option value="1" <?php echo @$packageDetail->newbenchclientrequest_status == 1 ? 'selected="selected"' : ''; ?>>Active
                                                                    </option>
                                                                    <option value="0" <?php echo @$packageDetail->newbenchclientrequest_status != 1 ? 'selected="selected"' : ''; ?>>
                                                                        InActive</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    Emails
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <input type="number" id="emails_quantity"
                                                                    name="emails_quantity"
                                                                    value="<?php echo @$packageDetail->emails_quantity; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="number" id="emails_used"
                                                                    name="emails_used" value="<?php echo @$packageDetail->emails_used; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="number" id="emails_balance"
                                                                    name="emails_balance" value="<?php echo @$packageDetail->emails_balance; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="date" id="emails_startdate"
                                                                    name="emails_startdate"
                                                                    value="<?php echo @$packageDetail->emails_startdate; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    onfocus="this.showPicker()" />
                                                            </td>
                                                            <td>
                                                                <input type="date" id="emails_enddate"
                                                                    name="emails_enddate" value="<?php echo @$packageDetail->emails_enddate; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    onfocus="this.showPicker()" />
                                                            </td>
                                                            <td class="">
                                                                <select id="emails_status" name="emails_status"
                                                                    class="form-control form-control-sm form-control-solid ele_border">
                                                                    <option value="1" <?php echo @$packageDetail->emails_status == 1 ? 'selected="selected"' : ''; ?>>Active
                                                                    </option>
                                                                    <option value="0" <?php echo @$packageDetail->emails_status != 1 ? 'selected="selected"' : ''; ?>>
                                                                        InActive</option>
                                                                </select>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    Old CV Access
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <input type="number" id="oldcvaccess_quantity"
                                                                    name="oldcvaccess_quantity"
                                                                    value="<?php echo @$packageDetail->oldcvaccess_quantity; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="number" id="oldcvaccess_used"
                                                                    name="oldcvaccess_used"
                                                                    value="<?php echo @$packageDetail->oldcvaccess_used; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="number" id="oldcvaccess_balance"
                                                                    name="oldcvaccess_balance"
                                                                    value="<?php echo @$packageDetail->oldcvaccess_balance; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="date" id="oldcvaccess_startdate"
                                                                    name="oldcvaccess_startdate"
                                                                    value="<?php echo @$packageDetail->oldcvaccess_startdate; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    onfocus="this.showPicker()" />
                                                            </td>
                                                            <td>
                                                                <input type="date" id="oldcvaccess_enddate"
                                                                    name="oldcvaccess_enddate"
                                                                    value="<?php echo @$packageDetail->oldcvaccess_enddate; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    onfocus="this.showPicker()" />
                                                            </td>
                                                            <td class="">
                                                                <select name="oldcvaccess_status"
                                                                    id="oldcvaccess_status"
                                                                    class="form-control form-control-sm form-control-solid ele_border">
                                                                    <option value="1" <?php echo @$packageDetail->oldcvaccess_status == 1 ? 'selected="selected"' : ''; ?>>Active
                                                                    </option>
                                                                    <option value="0" <?php echo @$packageDetail->oldcvaccess_status != 1 ? 'selected="selected"' : ''; ?>>
                                                                        InActive</option>
                                                                </select>
                                                            </td>
                                                        </tr> --}}



                                                        <?php 
															//if($packageDetail->company_type != 3){
														?>

                                                        {{-- <tr>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    Contract Recruitment Lead
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <input type="number" id="newclientrequest_quantity"
                                                                    name="newclientrequest_quantity"
                                                                    value="<?php echo @$packageDetail->newclientrequest_quantity; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="number" id="newclientrequest_used"
                                                                    name="newclientrequest_used"
                                                                    value="<?php echo @$packageDetail->newclientrequest_used; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="number" id="newclientrequest_balance"
                                                                    name="newclientrequest_balance"
                                                                    value="<?php echo @$packageDetail->newclientrequest_balance; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="date" id="newclientrequest_startdate"
                                                                    name="newclientrequest_startdate"
                                                                    value="<?php echo @$packageDetail->newclientrequest_startdate; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    onfocus="this.showPicker()" />
                                                            </td>
                                                            <td>
                                                                <input type="date" id="newclientrequest_enddate"
                                                                    name="newclientrequest_enddate"
                                                                    value="<?php echo @$packageDetail->newclientrequest_enddate; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    onfocus="this.showPicker()" />
                                                            </td>
                                                            <td>
                                                                <select name="newclientrequest_status"
                                                                    id="newclientrequest_status"
                                                                    class="form-control form-control-sm form-control-solid ele_border">
                                                                    <option value="1" <?php echo @$packageDetail->newclientrequest_status == 1 ? 'selected="selected"' : ''; ?>>Active
                                                                    </option>
                                                                    <option value="0" <?php echo @$packageDetail->newclientrequest_status != 1 ? 'selected="selected"' : ''; ?>>
                                                                        InActive</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    Fulltime Recruitment Leads
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <input type="number" id="newjobrequest_quantity"
                                                                    name="newjobrequest_quantity"
                                                                    value="<?php echo @$packageDetail->newjobrequest_quantity; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="number" id="newjobrequest_used"
                                                                    name="newjobrequest_used"
                                                                    value="<?php echo @$packageDetail->newjobrequest_used; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="number" id="newjobrequest_balance"
                                                                    name="newjobrequest_balance"
                                                                    value="<?php echo @$packageDetail->newjobrequest_balance; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>
                                                            <td>
                                                                <input type="date" id="newjobrequest_startdate"
                                                                    name="newjobrequest_startdate"
                                                                    value="<?php echo @$packageDetail->newjobrequest_startdate; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    onfocus="this.showPicker()" />
                                                            </td>
                                                            <td>
                                                                <input type="date" id="newjobrequest_enddate"
                                                                    name="newjobrequest_enddate"
                                                                    value="<?php echo @$packageDetail->newjobrequest_enddate; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    onfocus="this.showPicker()" />
                                                            </td>
                                                            <td class="">
                                                                <select name="newjobrequest_status"
                                                                    id="newjobrequest_status"
                                                                    class="form-control form-control-sm form-control-solid ele_border">
                                                                    <option value="1" <?php echo @$packageDetail->newjobrequest_status == 1 ? 'selected="selected"' : ''; ?>>Active
                                                                    </option>
                                                                    <option value="0" <?php echo @$packageDetail->newjobrequest_status != 1 ? 'selected="selected"' : ''; ?>>
                                                                        InActive</option>
                                                                </select>
                                                            </td>
                                                        </tr>

                                                        <tr>

                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">Boost
                                                                    Profile Keywords</a>
                                                            </td>

                                                            <td>
                                                                <input type="number" id="boostkeywords_quantity"
                                                                    name="boostkeywords_quantity"
                                                                    value="<?php echo @$packageDetail->boostkeywords_quantity; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>

                                                            <td>
                                                                <input type="number" id="boostkeywords_used"
                                                                    name="boostkeywords_used"
                                                                    value="<?php echo @$packageDetail->boostkeywords_used; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>

                                                            <td>
                                                                <input type="number" id="boostkeywords_balance"
                                                                    name="boostkeywords_balance"
                                                                    value="<?php echo @$packageDetail->boostkeywords_balance; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border" />
                                                            </td>

                                                            <td>
                                                                <input type="date" id="boostkeywords_startdate"
                                                                    name="boostkeywords_startdate"
                                                                    value="<?php echo @$packageDetail->boostkeywords_startdate; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    onfocus="this.showPicker()" />
                                                            </td>
                                                            <td>
                                                                <input type="date" id="boostkeywords_enddate"
                                                                    name="boostkeywords_enddate"
                                                                    value="<?php echo @$packageDetail->boostkeywords_enddate; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border"
                                                                    onfocus="this.showPicker()" />
                                                            </td>
                                                            <td class="">
                                                                <select name="boostkeywords_status"
                                                                    id="boostkeywords_status"
                                                                    class="form-control form-control-sm form-control-solid ele_border">
                                                                    <option value="1" <?php echo @$packageDetail->boostkeywords_status == 1 ? 'selected="selected"' : ''; ?>>Active
                                                                    </option>
                                                                    <option value="0" <?php echo @$packageDetail->boostkeywords_status != 1 ? 'selected="selected"' : ''; ?>>
                                                                        InActive</option>
                                                                </select>
                                                            </td>
                                                        </tr> --}}


                                                        {{-- <tr>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    Keywords
                                                                </a>
                                                            </td>
                                                            <td colspan="6">
                                                                <input type="text" id="boostkeywords_text"
                                                                    name="boostkeywords_text"
                                                                    value="<?php echo @$packageDetail->boostkeywords_text; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border_merge" />
                                                            </td>

                                                        </tr> --}}

                                                        <?php 
	//}
?>
                                                            <tr>
                                                            <td>
                                                                <a href="javascript:void(0);"
                                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                                                    Keywords
                                                                </a>
                                                            </td>
                                                            <td colspan="6">
                                                                <input type="text" id="boostkeywords_text"
                                                                    name="boostkeywords_text"
                                                                    value="<?php echo @$package_active_details->boostkeywords_text; ?>"
                                                                    class="form-control form-control-sm form-control-solid ele_border_merge" />
                                                            </td>

                                                        </tr>

                                                    </tbody>
                                                </table>

                                                <!-- Sticky Drawer Footer (Preserves #update-form and serialize) -->
                                                <div class="sa-drawer-footer">
                                                    <a href="javascript:void(0);" class="btn btn-light-secondary me-3 px-5 py-2 fw-semibold" data-kt-drawer-dismiss="true">
                                                        Cancel
                                                    </a>
                                                    <button type="button" name="update" id="update-form" value="update" class="btn btn-primary px-6 py-2 fw-semibold d-inline-flex align-items-center gap-2">
                                                        <i class="fa-solid fa-floppy-disk fs-6"></i>
                                                        <span>Update Wallet</span>
                                                    </button>
                                                </div>

                                            </form>



                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
//echo '<pre>'; print_r(@$packageDetail); exit;
?>

<script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>"
    type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>

<script>
    var input = document.querySelector('input[name=boostkeywords_text]');
    new Tagify(input)
</script>


<script>
    /* $('#update-form').click(function () {
      
   			
			//alert('Call'); return false; 
							
   		  var formData 					=	new FormData($('#createForm')[0]);					
          $.ajax({
              		  url				: '{{ route('superadmin.custompackages.update', @$company_id) }}'+'?_token=' + '{{ csrf_token() }}',	
   					  type				: "POST",
   					  data 				: formData,
   					  processData		: false,  				// tell jQuery not to process the data
   					  contentType		: false,  				// tell jQuery not to set contentType
   					  //container		: '#createForm',
   					 
   					  //redirect			: true,
   					  //file				: true,
   					  //data			: $('#createForm').serialize(),
   					  success			: function (response) {
   					  							//window.location = response.url;
   					  						},
              			error			: function (response) {
                  										handleFails(response);
              										}
          		})
      });*/
</script>


<script>
    $('#update-form').click(function() {

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })
        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Ignore it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {

                var formData = new FormData($('#createForm')[0]);
                $.ajax({
                    url: '{{ route('superadmin.custompackages.update', @$company_id) }}' +
                        '?_token=' + '{{ csrf_token() }}',
                    type: "POST",
                    data: formData,
                    processData: false, // tell jQuery not to process the data
                    contentType: false, // tell jQuery not to set contentType
                    //container		: '#createForm',

                    //redirect			: true,
                    //file				: true,
                    //data			: $('#createForm').serialize(),
                    success: function(response) {
                        if (response.status == 'success') {
                            // $('#currentPackageBody').html(response.datatable);

                            swalWithBootstrapButtons.fire(
                                'Success!',
                                'Selected request has been Updated.',
                                'success'
                            )
							$("#kt_activitiesWallet_close").click();

                        }
                        //window.location = response.url;
                    },
                    error: function(response) {
                        handleFails(response);
                    }
                })

                /*var url   			= "{{ route('service_company.clients.request_update') }}";
                $.ajax({
                			type	: 'GET',
                			url		: url,
                			data	: {'invitation_id':id, 'event_id': event_id, 'from_company': from_company},
                			success	: function (response) {
                															var clientAll		=	response.clientDataCnt.clientAll;
                															var clientDirect	=	response.clientDataCnt.clientDirect;
                															var clientCustom	=	response.clientDataCnt.clientCustom;
                															var clientRequest	=	response.clientDataCnt.clientRequest;
                															
                															$('#span_clientAll').html(clientAll);
                															$('#span_clientDirect').html(clientDirect);
                															$('#span_clientCustom').html(clientCustom);
                															$('#span_clientRequest').html(clientRequest);
                															
                												if(response.status == 'success') {
                			
                															table._fnDraw(); 
                															swalWithBootstrapButtons.fire(
                																							'Ignore!',
                																							'Selected request has been Ignore.',
                																							'success'
                																						)
                												}
                			}
                });*/








            } else if (
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Cancelled',
                    'Selected request is safe :)',
                    'error'
                )
            }
        })

    });
</script>
