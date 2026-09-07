@extends('layouts.superadmin_layout')

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<!--<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">-->
<style>
        body
        {overflow-x: hidden;}
        .company-logo {
            max-height: 50px;
			max-width:120px;
        }

        .company-logo-div {
            border-radius: 2px;
            padding: 2px 2px 2px 2px;
            margin-bottom: 2px;
        }
        .symbol.symbol-circle, .symbol.symbol-circle .symbol-label, .symbol.symbol-circle > img
        {
            border-radius: 10%;
        }

@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesWallet
  {
    width: 1025px;
  }
}

@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesWallet
  {
    width: 1200px;
  }
}

</style>


@endpush

@section('content')
<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">About the company <span class="h-20px border-gray-200 border-start ms-3 mx-2 d-none"></span> <small class="text-muted fs-7 fw-bold my-1 ms-1 d-none">#XRS-45670</small> </h1>
    </div>
  </div>
</div>
<!--end::toolbar-->
<?php 
	
	 $logo_path				=	url('img/client-default.png');
     if(!empty(@$account->logo) && strlen(@$account->logo)>10){
     			$logo_path		=  url('public/user-uploads/company-logo/'.@$account->logo); 	
     }
	
?>
<input type="hidden" name="company_id" id="company_id" value="<?php echo @$company->id; ?>" />
<div class="post d-flex flex-column-fluid mt-5" id="kt_post">
  <div id="kt_content_container" class="container-fluid px-3">
    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-2 company-logo-div bg-dark" >
                <p><img src="{{ $logo_path }}" class="img-fluid company-logo" /></p>
              </div>
              <div class="col-md-12 mb-3"> <small class="text-muted fw-light">@lang('modules.company.registeredOn') {{ $company->created_at->format('d M, Y') }}</small> </div>
            </div>
            <div class="row">
              <div class="col-md-6 b-r">
                <label class="text-muted fs-5">@lang('modules.accountSettings.companyName')</label>
                @if($company->isFeatured())
                <label>@lang('app.featured')</label>
                @endif
                <p class="text-dark fw-bold fs-5">{{ $company->company_name }} </p>
              </div>
              <div class="col-md-6 pl-3">
                <label class="text-muted fs-5">@lang('modules.accountSettings.companyEmail')</label>
                <p class="text-dark fw-bold fs-5">{{ $company->company_email }}</p>
              </div>
            </div>
            <hr style="border: none;border-top: 2px dotted #d4d9dd;color: #fff;">
            <div class="row">
              <div class="col-md-6 b-r">
                <label class="text-muted fs-5">@lang('modules.accountSettings.companyPhone')</label>
                <p class="text-dark fw-bold fs-5">{{ $company->company_phone }}</p>
              </div>
              <div class="col-md-6 pl-3">
                <label class="text-muted fs-5">@lang('modules.accountSettings.companyWebsite')</label>
                <p class="text-dark fw-bold fs-5">{{ $company->website }}</p>
              </div>
            </div>
            <hr style="border: none;border-top: 2px dotted #d4d9dd;color: #fff;">
            <div class="row">
              <div class="col-md-6 b-r">
                <label class="text-muted fs-5">@lang('modules.accountSettings.companyAddress')</label>
                <p class="text-dark fw-bold fs-5">
				<?php 				  				
							echo ucwords($company_city_name);	
							echo ' , ';
							echo ucwords($company_country_name);								
				?>
                </p>
              </div>
              <div class="col-md-6 pl-3">
                <label class="text-muted fs-5">@lang('app.status')</label>
                <p> @if($company->status == 'active')
                  <label class="badge bg-light-success text-success">@lang('app.active')</label>
                  @else
                  <label class="badge bg-light-danger text-danger">@lang('app.inactive')</label>
                  @endif </p>
              </div>
            </div>
            <div class="row">
              <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="myContactPerson">
                <thead class="">
                  <tr class="fw-bolder text-muted">
                    <th class="min-w-200px">Name</th>
                    <th class="min-w-150px">Position</th>
                    <th class="min-w-150px">Email</th>
                    <th class="min-w-100px">Phone</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body">
           <!-- <h4 class="card-title">@lang('modules.company.currentPackage')</h4>-->
            <table class="table table-condensed d-none">
              <!--<tr>
                <div class="">
                  <td class="text-muted fs-5">@lang('app.package') :</td>
                </div>
                <td class="text-dark fs-5 fw-bold">{{ $company->package->name }}</td>
              </tr>-->
             <!-- <tr>
                <td class="text-muted fs-5">@lang('app.package') @lang('app.type') :</td>
                <td class="text-dark fs-5 fw-bold">{{ (!is_null($company->package_type)) ? __('app.'.$company->package_type) : "--" }}</td>
              </tr>-->
              <!--<tr>
                <td class="text-muted fs-5">@lang('app.price') :</td>
                @if($company->package_type == 'monthly')
                <td class="text-dark fs-5 fw-bold">{{ $global->currency->currency_symbol.$company->package->monthly_price }}</td>
                @else
                <td class="text-dark fs-5 fw-bold">{{ $global->currency->currency_symbol.$company->package->annual_price }}</td>
                @endif </tr>-->
              <tr>
                <td  class="text-muted fs-5">@lang('modules.subscription.expiresOn') :</td>
                <td
                                @if(!is_null($company->licence_expire_on) && $company->licence_expire_on->isPast() )
                  class="text-danger fs-5 fw-bold""
                  @else
                  class="text-info fs-5 fw-bold"
                  @endif
                  >
                  @if(!is_null($company->licence_expire_on))
                  {!! $company->licence_expire_on->format('d M, Y').' <br>
                  <small><i>'.$company->licence_expire_on->diffForHumans().'</i></small>' !!}
                  @endif
                  
                  @if(!is_null($companyPackage))
                  @php
                  $date = \Carbon\Carbon::parse($companyPackage->end_date);
                  $now = \Carbon\Carbon::today();
                  
                  echo $diff = $date->diffInDays($now);    
                  @endphp
                  @lang('modules.dashboard.daysLeft')
                  @endif </td>
              </tr>
            </table>
            <div class="row" style="display:contents;">
              
			  <div class="col px-0" > <a href="<?php echo url('super-admin/custompackages/create/'.@$account->id); ?>" type="button" class="btn btn-sm btn-primary btn-active-light-dark w-100" id="AddCandidate"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                    <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                    <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                  </g>
                </svg>
                </span>New Package </a> </div>
				
              <div class="col px-0" style="margin-top:10px;"> <a href="javascript:;" data-company-id="{{ $company->id }}" id="change-package" class="btn btn-sm btn-primary btn-active-light-dark w-100"> <i class="fa fa-box"></i>Edit @lang('app.package') </a> </div>
              <div class="col px-0 mt-4"> <a href="javascript:;" data-company-id="{{ $company->id }}" id="login-as-company" class="btn btn-sm btn-primary btn-active-light-dark w-100"> <i class="fa fa-sign-in-alt fa-lg"></i> @lang('app.loginAsCompany') </a> </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-5">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
		  		<table>
					<tr>
						<td>
								<h4 class="card-title" style="text-align:left">Current Package</h4>
						</td>
						<td>
								<a href="javascript:void(0);" id="editCompanyWallet"> 
									&nbsp;<i class="bi bi-pencil-fill" style="color:#5DB8F9"></i>Edit
								</a>
						</td>
					</tr>
				</table>
            
			 
            <div class="table-responsive">
              <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="currentPackage">
                <thead class="">
                  <tr class="fw-bolder text-muted">
                    <th class="min-w-200px">Product</th>
                    <th class="min-w-150px">Quantity Allocated</th>
                    <th class="min-w-150px">Consumed</th>
                    <th class="min-w-150px">Remaining</th>
                    <th class="min-w-150px">Start Date</th>
                    <th class="min-w-150px">End Date</th>
                    <th class="min-w-150px">Status</th>
                  </tr>
                </thead>
                <tbody id="currentPackageBody">
                  
				  <tr>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							CV Access / Received
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->cvaccess_quantity ? number_format(@$package_active->cvaccess_quantity): 0; ?> 
                      </a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->cvaccess_used ? number_format(@$package_active->cvaccess_used): 0; ?> 
                      	</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->cvaccess_balance ? number_format(@$package_active->cvaccess_balance): 0; ?> 
                      	</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->cvaccess_startdate ? date("d M, Y", strtotime(@$package_active->cvaccess_startdate)): ''; ?> 
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->cvaccess_enddate ? date("d M, Y", strtotime(@$package_active->cvaccess_enddate)): ''; ?> 
						</a>
					</td>
                    <td class="">
<?php  
echo @$package_active->cvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
?> 	
					</td>
                  </tr>
                  
				  <tr>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6" >
							Users
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                    		<?php  echo @$package_active->users_quantity ? number_format(@$package_active->users_quantity): 0; ?>
                      	</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                    		<?php  echo @$package_active->users_used ? number_format(@$package_active->users_used): 0; ?>
                      	</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->users_balance ? number_format(@$package_active->users_balance): 0; ?>
                        </a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->users_startdate ? date("d M, Y", strtotime(@$package_active->users_startdate)): ''; ?> 
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->users_enddate ? date("d M, Y", strtotime(@$package_active->users_enddate)): ''; ?> 
						</a>
					</td>
                    <td class="">
<?php  
echo @$package_active->users_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
?> 
					</td>
                  </tr>
				  
                  <tr>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							New Bench Client Request
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->newbenchclientrequest_quantity ? number_format(@$package_active->newbenchclientrequest_quantity): 0; ?>
                      	</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->newbenchclientrequest_used ? number_format(@$package_active->newbenchclientrequest_used): 0; ?>
                      	</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->newbenchclientrequest_balance ? number_format(@$package_active->newbenchclientrequest_balance): 0; ?>
                      </a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->newbenchclientrequest_startdate ? date("d M, Y", strtotime(@$package_active->newbenchclientrequest_startdate)): ''; ?> 
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->newbenchclientrequest_enddate ? date("d M, Y", strtotime(@$package_active->newbenchclientrequest_enddate)): ''; ?> 
						</a>
					</td>
                    <td class="">
<?php  
echo @$package_active->newbenchclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
?> 
					</td>
                  </tr>
				  
                  <tr>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							Emails
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->emails_quantity ? number_format(@$package_active->emails_quantity): 0; ?>
                      	</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->emails_used ? number_format(@$package_active->emails_used): 0; ?>
                      	</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->emails_balance ? number_format(@$package_active->emails_balance): 0; ?>
                      	</a>
					 </td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->emails_startdate ? date("d M, Y", strtotime(@$package_active->emails_startdate)): ''; ?>
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->emails_enddate ? date("d M, Y", strtotime(@$package_active->emails_enddate)): ''; ?>
						</a>
					</td>
                    <td class="">
<?php  
echo @$package_active->emails_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
?> 
					</td>
                  </tr>
				  
                  <tr>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							Old CV Access
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->oldcvaccess_quantity ? number_format(@$package_active->oldcvaccess_quantity): 0; ?>
                      </a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->oldcvaccess_used ? number_format(@$package_active->oldcvaccess_used): 0; ?>
                      </a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->oldcvaccess_balance ? number_format(@$package_active->oldcvaccess_balance): 0; ?>
                      </a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->oldcvaccess_startdate ? date("d M, Y", strtotime(@$package_active->oldcvaccess_startdate)): ''; ?>
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->oldcvaccess_enddate ? date("d M, Y", strtotime(@$package_active->oldcvaccess_enddate)): ''; ?>
						</a>
					</td>
                    <td class="">
<?php  
echo @$package_active->oldcvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
?>
					</td>
                  </tr>
				  
				  
<?php 
	if($account->company_type !=3){
?>				  
				  
				  <tr>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							Contract Recruitment Lead
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->newclientrequest_quantity ? number_format(@$package_active->newclientrequest_quantity): 0; ?>
                      	</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->newclientrequest_used ? number_format(@$package_active->newclientrequest_used): 0; ?>
                      	</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->newclientrequest_balance ? number_format(@$package_active->newclientrequest_balance): 0; ?>
                      	</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->newclientrequest_startdate ? date("d M, Y", strtotime(@$package_active->newclientrequest_startdate)): ''; ?> 
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->newclientrequest_enddate ? date("d M, Y", strtotime(@$package_active->newclientrequest_enddate)): ''; ?> 
						</a>
					</td>
                    <td class="">
					<?php  
					echo @$package_active->newclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
					?> 
					</td>
                  </tr>
				  
				  <tr>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							Fulltime Recruitment Leads
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->newjobrequest_quantity ? number_format(@$package_active->newjobrequest_quantity): 0; ?>
                      	</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->newjobrequest_used ? number_format(@$package_active->newjobrequest_used): 0; ?>
                      </a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->newjobrequest_balance ? number_format(@$package_active->newjobrequest_balance): 0; ?>
                      </a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->newjobrequest_startdate ? date("d M, Y", strtotime(@$package_active->newjobrequest_startdate)): ''; ?>
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->newjobrequest_enddate ? date("d M, Y", strtotime(@$package_active->newjobrequest_enddate)): ''; ?>
						</a>
					</td>
                    <td class="">
<?php  
echo @$package_active->newjobrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
?> 
					</td>
                  </tr>
				  
				  <tr>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							Boost Profile Keywords 
						</a>
					</td>
                   <!-- <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->boostkeywords_text ? @$package_active->boostkeywords_text: ''; ?>
                      	</a>
					</td>-->
					
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->boostkeywords_quantity ? number_format(@$package_active->boostkeywords_quantity): 0; ?>
                      </a>
					</td>
                   <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->boostkeywords_used ? number_format(@$package_active->boostkeywords_used): 0; ?>
                      </a>
					</td>
					<td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->boostkeywords_balance ? number_format(@$package_active->boostkeywords_balance): 0; ?>
                      </a>
					</td>
					
					
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->newjobrequest_startdate ? date("d M, Y", strtotime(@$package_active->boostkeywords_startdate)): ''; ?>
						</a>
					</td>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							<?php  echo @$package_active->newjobrequest_enddate ? date("d M, Y", strtotime(@$package_active->boostkeywords_enddate)): ''; ?>
						</a>
					</td>
						<td class="">
						<?php  
						echo @$package_active->boostkeywords_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
						?> 
						</td>
                  </tr>
				  
				  <tr>
                    <td>
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
							&nbsp;
						</a>
					</td>
                   <td colspan="7">
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                      		<?php  echo @$package_active->boostkeywords_text ? @$package_active->boostkeywords_text: ''; ?>
                      	</a>
					</td>
					
                    
					
					
                   
                  </tr>
				  

<?php 
	}
?>				   
				  
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
	
	
<?php 

	//echo '<pre>'; print_r($package_active);  exit;  
?>	
    <div class="row mt-5">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center py-1" >
              <h1 class="card-title">History</h1>
              <div class="card-toolbar" style="float:right; flex:auto">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base"> <a href="<?php echo url('super-admin/custompackages/create/'.@$account->id); ?>" type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3" id="AddCandidate"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"></polygon>
                      <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                      <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                    </g>
                  </svg>
                  </span>New Package </a> </div>
              </div>
            </div>
          </div>
        </div>
		
		<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="myPackageHistory">
                <thead class="d-none">
                  <tr class="fw-bolder text-muted">
                    <th class="min-w-200px">Name</th>
                  </tr>
                </thead>
              </table>
			  
			  
        
		
		
      </div>
    </div>
    <div class="table-responsive mt-5 d-none">
      <table id="myTable" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
        <thead>
          <tr>
            <th>No.</th>
            <th>@lang('app.package')</th>
            <th>@lang('app.type')</th>
            <th>@lang('app.startDate')</th>
            <th>@lang('app.endDate')</th>
            <th>@lang('app.status')</th>
          </tr>
        </thead>
        <tbody>
        
        @foreach ($company->packages as $key=>$item)
        <tr>
          <td>{{ $key+1 }}</td>
          <td>{{ ucfirst($item->package->name) }}</td>
          <td>{{ $item->package_type }}</td>
          <td>{{ $item->start_date->format('d M, Y') }}</td>
          <td>{{ (!is_null($item->end_date)) ? $item->end_date->format('d M, Y') : "" }}</td>
          <td> @if ($item->status == 'active')
            <label class="badge bg-success">@lang('app.active')</label>
            @else
            <label class="badge bg-danger">@lang('app.inactive')</label>
            @endif </td>
        </tr>
        @endforeach
        </tbody>
        
      </table>
    </div>
  </div>
</div>



<div id="kt_activitiesWallet_toggle"></div>
<div id="kt_activitiesWallet" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesWallet_toggle" data-kt-drawer-close="#kt_activitiesWallet_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Edit Company Wallet</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesWallet_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesWallet_body"></div>
  </div>
</div>


@endsection
@push('footer-script')
<!--<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>-->
<script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script>

 $('body').on('click', '#editCompanyWallet', function ()
		{
			
			
			$('#kt_activitiesWallet_toggle').trigger('click');		
			var id    		=	$('#company_id').val();
			var url   		=	"{{ route('superadmin.custompackages.editWallet',':id') }}";
			url     		=	url.replace(':id', id);
			var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
			$.ajax({
							type  		: 'GET',
							url   		: url,
							 beforeSend	: function() {
										 $('#kt_activitiesWallet_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
							},
							success 	: function (response) 
							{
									setTimeout(
									()=>
									{
											if (response.status == "success")
											{
												$('#kt_activitiesWallet_body').html(response.view);
											}
									}, 500);
									
							}
					});
		}); 
		
</script>

<script>

	  var company_id		=	$('#company_id').val();
	 
	  var table = $('#myContactPerson').dataTable({
            
				responsive     		: true, 
      			processing      	: true,
				ordering			: false, //disable ordering       			
      			destroy       		: true,   
      			stateSave     		: true,      
      			"bLengthChange"   	: true,   
      			"lengthMenu"        : [[10, 25, 50, 100], [10, 25, 50, 100]],
				sDom				: 'lrtip',
				
				
				//responsive		: true,
            	// processing		: true,
				
				searching			: false,
            	serverSide			: true,
            	ajax				: '{!! route('superadmin.company.myContactPerson') !!}?company_id='+company_id,
            	language			: {
                						//"url": "<?php echo __("app.datatable") ?>" 
            						},
            	"fnDrawCallback"	: function( oSettings ) {
                												$("body").tooltip({
                    																selector: '[data-toggle="tooltip"]'
                													});
            						  },
            	columns: [
               							{ data: 'Name', name: 'Name'},
										{ data: 'Position', name: 'Position' },
										{ data: 'Email', name: 'Email' },
										{ data: 'Phone', name: 'Phone' }
            		     ]
        });
      new $.fn.dataTable.FixedHeader( table );

		$('body').on('click', '.sa-params', function(){
            var id = $(this).data('row-id');
            swal({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('superadmin.company.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });
		
</script>

<script>


	 var company_id			=	$('#company_id').val();
	 var myPackageHistory 	= $('#myPackageHistory').dataTable({
				responsive     		: true, 
      			processing      	: true,
				ordering			: false, //disable ordering       			
      			"bLengthChange"   	: true,   
      			"lengthMenu"        : [[10, 25, 50, 100], [10, 25, 50, 100]],
				sDom				: 'lrtip',
				//responsive		: true,
            	// processing		: true,				
				searching			: false,
            	serverSide			: true,
            	ajax				: '{!! route('superadmin.custompackages.myPackageHistory') !!}?company_id='+company_id,
            	language			: {
                						//"url": "<?php echo __("app.datatable") ?>" 
            						},
            	"fnDrawCallback"	: function( oSettings ) {
                												$("body").tooltip({
                    																selector: '[data-toggle="tooltip"]'
                													});
            						  },
            	columns: [
               							{ data: 'Name', name: 'Name'}
            		     ]
        });
      new $.fn.dataTable.FixedHeader( myPackageHistory );

</script>


<script>

	$('body').on('click', '.comment_submit', function(){
	
			var id 			=	$(this).attr('rel');
			var comment 	=	$('#comment_'+id).val();
			
			
			
			var url       	=	"{{ route('superadmin.custompackages.submitcomment',':id') }}";
			url         	=	url.replace(':id', id);
			var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
			var token       =	"{{ csrf_token() }}";
			$.ajax({
								type		: 'POST',
								url			: url,
								data		: {'_token': token, '_method': 'POST', 'id' : id, 'comment': comment},
								cache		: false,
								beforeSend	: function() {
															//$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
								},
								success 	: function (response) {
												if (response.status == "success") {
													  Swal.fire("Success", 'Invoice comment submit successfully !', "success")
													  myPackageHistory._fnDraw();
												}else{
													Swal.fire("Warning", 'Opps something wrong, please try again !', "info");	
													myPackageHistory._fnDraw();
												}
									}
					});
			
	
	});
</script>


<script>


   /* var table = $('#myTable').dataTable({
            
											responsive		: true,
            // processing: true,
            								serverSide		: true,
            								ajax			: '{!! route('superadmin.company.data') !!}',
            								language		: languageOptions(),
											"fnDrawCallback": function( oSettings ) {
												$("body").tooltip({
													selector: '[data-toggle="tooltip"]'
												});
											},
            columns: [
                { data: 'DT_Row_Index'},
                { data: 'logo', name: 'logo' },
                { data: 'company_name', name: 'company_name' },
                    @if(module_enabled('Subdomain'))
                { data: 'sub_domain', name: 'sub_domain' },
                    @else
                { data: 'company_email', name: 'company_email' },
                    @endif
                { data: 'package_id', name: 'package_id' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', width: '20%' }
            ]
        });


        new $.fn.dataTable.FixedHeader( table );
*/
       /* $('body').on('click', '.sa-params', function(){
            var id = $(this).data('row-id');
            swal({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('superadmin.company.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });*/

   /* @if(module_enabled('Subdomain'))
    $('body').on('click', '.domain-params', function(){
        var company_id = $(this).data('company-id');
        var company_url = $(this).data('company-url');
        swal({
            title: "Are you sure?",
            text: `You want to notify company admins about company Login URL <br> Company URL: <a href="${company_url}"><b>${company_url}</b></a>`,
            html:true,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, Notify it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var token = "{{ csrf_token() }}";

                $.ajax({
                    type: 'POST',
                    url: "{{route('notify.domain')}}",
                    data: {'_token': token, 'company_id': company_id},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            table._fnDraw();
                        }
                    }
                });
            }
        });
    });
    @endif*/




   /* var table = $('#myTable').dataTable({
            responsive: true,
            // processing: true,
            serverSide: true,
            ajax: '{!! route('superadmin.company.data') !!}',
            language: languageOptions(),
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            columns: [
                { data: 'DT_Row_Index'},
                { data: 'logo', name: 'logo' },
                { data: 'company_name', name: 'company_name' },
                    @if(module_enabled('Subdomain'))
                { data: 'sub_domain', name: 'sub_domain' },
                    @else
                { data: 'company_email', name: 'company_email' },
                    @endif
                { data: 'package_id', name: 'package_id' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', width: '20%' }
            ]
        });


        new $.fn.dataTable.FixedHeader( table );*/

        /*$('body').on('click', '.sa-params', function(){
            var id = $(this).data('row-id');
            swal({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('superadmin.company.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });*/

   /* @if(module_enabled('Subdomain'))
    $('body').on('click', '.domain-params', function(){
        var company_id = $(this).data('company-id');
        var company_url = $(this).data('company-url');
        swal({
            title: "Are you sure?",
            text: `You want to notify company admins about company Login URL <br> Company URL: <a href="${company_url}"><b>${company_url}</b></a>`,
            html:true,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, Notify it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var token = "{{ csrf_token() }}";

                $.ajax({
                    type: 'POST',
                    url: "{{route('notify.domain')}}",
                    data: {'_token': token, 'company_id': company_id},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            table._fnDraw();
                        }
                    }
                });
            }
        });
    });
    @endif
*/
</script>
@endpush 