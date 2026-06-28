@extends('layouts.service_company_layout')
@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>

<style>
@media only screen and (min-width: 900px) and (max-width: 2560px){
  .table td, .table th, .table tr
  {width: max-content!important;}
  .table > :not(caption) > * > *
  { padding: 0rem; }
  td:nth-child(n+2),
  th:nth-child(n+2)
  { width: revert!important;}
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesJob {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesJob {width: 1200px;}
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesClientjob_toggle {width: 1025px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesClientjob_toggle {width: 1200px;}
}
.table-custom-class tbody tr {
    height: 82px;
    position: relative;
}

.selectednotice {
            background-color: #007bff !important; /* Example color */
            color: white !important;
        }
.tagify__tag {
            display: flex;
            align-items: center;
        }
        .tagify__tag .star {
            margin-left: 5px;
            cursor: pointer;
            font-size: 21px;
            position: relative;
            top: -5px;
        }
        .tagify__tag .star.checked {
            color: #ef1632;
        }

</style>
<link href="{{ asset('demo/css/styleNew.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')

<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
   
      @php 
              

            $bench_explore_url = url('service_company/bench-candidates/bench_explore/') .'/'. @$bench_detail->id . '/all';
            $recommend_req_url = url('service_company/bench-candidates/recommend_req/') .'/'. @$bench_detail->id;
            $recommend_internal_req = url('service_company/bench-candidates/recommend_internal_req/') .'/'. @$bench_detail->id;
            $tabactive = [
            'bench_explore_pool_active'=>'',
            'bench_explore_pool_url'=> $bench_explore_url,
            'bench_explore_pool_id'=>'bench_explore_pool_id',
            'bench_explore_counter' =>$recomCount['bench_explore_counter'],
            'recommend_req_active'=>'tabactive',
            'recommend_req_url'=>$recommend_req_url,
            'recommend_req_id'=>'recommend_req_id',
            'recommend_req_counter' =>$recomCount['recommend_req_counter'],
            'recommend_req_internal_active'=>'',
            'recommend_req_internal_url'=>$recommend_internal_req,
            'recommend_req_internal_id'=>'recommend_req_internal_id',
            'recommend_req_internal_counter' =>$recomCount['recommend_internal_req_counter'],
            ];
              
      @endphp 

      @include('sections.service_company.bench-toolbar',['job' => @$job,'bench_detail'=>@$bench_detail])

    <form action="" id="filter-form" style="">
      @csrf
			<input type="hidden" name="jobid" id="jobid" value="{{ @$job->id }}" />
			<input type="hidden" name="mstatus_id" id="mstatus_id" value="{{ @$mstatus_id }}" />			 
      <div class="d-flex align-items-center py-1">        
        <div class="d-flex align-items-center position-relative my-1 me-3">
          <input type="hidden" id="inputSearch" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
        </div>        
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
              <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 d-none" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                <span class="svg-icon svg-icon-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
                  </svg>
                </span>
				Filter
              </button>
              <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 d-none" data-bs-toggle="modal" data-bs-target="#kt_modal_export_users">
              <span class="svg-icon svg-icon-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black" />
                  <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black" />
                  <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4" />
                </svg>
              </span>
              Export</button>
            </div>
          </div>
      </div>
	  </form>	  
  </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">

      <div id="" class="container-fluid d-flex second_header">
          @include('sections.service_company.benchExploreHeader',['tabactive'=>$tabactive])
      </div>


      <div class="card">
      <div class="card-body pt-3 px-5"> 
      
        <div class="d-flex align-items-center flex-wrap">
          <div class="col-lg-4 d-flex align-items-center px-2" style="width:65%;position: relative;">
                <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch" value="{{ @$default_skill }}" placeholder="Search..." id="kt_tagify_2" />
                <div data-toggle="modal" data-target="#apptrackerfilter" style="position: absolute; z-index: 99; width: 100%; height: 100%; top: 0;"> </div>
          </div>
          <div class="col-lg-2 d-flex align-items-center px-2 d-none" style="width: 15%;">
            <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="status"  data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-3="filter_status" style="border: 1px solid #ffffff;" onchange="filtersdata()">
              <option value="active" selected="selected">Active</option>  
            </select>
          </div>
          <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
            <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="hiringtype" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdata()">
              <option value="">Sort</option>
              <option value="1" selected="selected">Newest to Oldest (Date)</option>
              <option value="2">Oldest to Newest (Date)</option>
              <option value="3">Low to High (Salary)</option>
              <option value="4">High to Low (Salary)</option>
            </select>
          </div>
          <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
            <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end" data-toggle="modal" data-target="#apptrackerfilter">
              <span class="svg-icon svg-icon-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                  </svg>
              </span>
              Filter
            </button>
          </div>
        </div>


             <!--Start Popup-->
             <div class="modal fade show" id="apptrackerfilter" tabindex="-1" style="">
              <div class="modal-dialog modal-dialog-centered modal-lg">
                 <div class="modal-content">
                    <div class="modal-header flex-stack">
                       <h2>Filter</h2>
                       <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal" id="apptrackerfilter_close">
                          <span class="svg-icon svg-icon-1">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" ></rect>
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" ></rect>
                             </svg>
                          </span>
                       </div>
                    </div>
                    <div class="apptrackerfilter_body_content" id="">
                       <div class="modal-body" style="padding: 2em;">
                          <form id="createSchedule" class="ajax-form" method="post">
                              <div class="d-flex flex-column mb-8">
                                    <label class="form-label required">Skills/Keywords</label>
                                    <input class="form-control form-control-solid d-flex align-items-center" name="kt_tagify_users" value="{{ @$default_skill }}" placeholder="" id="kt_tagify_users" />
                                    
                                    
                                <link href="{{ asset('demo/css/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
                                <script src="{{ asset('demo/js/plugins.bundle.js') }}{{ TM_VERSION }}"></script>
                                    <script>
                                        const inputElm = document.querySelector('#kt_tagify_users');
    
                                            const tagify = new Tagify(inputElm, {
                                                templates: {
                                                    tag: function(tagData) {
                                                        tagData = tagData || {};
                                                        const value = tagData.value || '';
                                                        const isChecked = tagData.checked ? 'checked' : '';
                                                        return `
                                                            <tag title="${value}" contenteditable='false' spellcheck="false" class="tagify__tag ${tagData.class ? tagData.class : ""}" ${this.getAttributes(tagData)}>
                                                                <x title='' class="tagify__tag__removeBtn" role="button" aria-label="remove tag"></x>
                                                                <div>
                                                                    <span class="star ${isChecked}">🟌</span>
                                                                    <span class="tagify__tag-text">${value}</span>
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
    
                                          // Add event listener for star click
                                          document.addEventListener('click', function(e) {
                                              if (e.target.classList.contains('star')) {
                                                  const tagElm = e.target.closest('.tagify__tag');
                                                  const value = tagElm.querySelector('.tagify__tag-text').textContent;
                                                  
                                                  e.target.classList.toggle('checked');
                                                  
                                                  // Update the tag data
                                                  const tagData = tagify.value.find(tag => tag.value === value);
                                                  if (tagData) {
                                                      tagData.checked = e.target.classList.contains('checked');
                                                      tagify.updateValueByDOMTags();
                                                  }
                                              }
                                          });
    
    
    
                                          // Initialize Tagify for kt_tagify_2
                                          const inputJobSearch = document.querySelector('#kt_tagify_2');
                                          // const tagify2 = new Tagify(inputJobSearch);
                                          const tagify2 = new Tagify(inputJobSearch, {
                                              maxTags: 6  // Limit the number of tags to 5
                                          });
    
                                          // Function to update inputJobSearch based on tagify's value
                                          function updateInputJobSearch() {
                                              const tags = tagify.value.map(tag => tag.value).join(',');
                                              inputJobSearch.value = tags;
                                              autoclickstar();
                                          }
    
                                          function updateTagifyUsers() {
                                              const newTags = inputJobSearch.value.split(',').map(tag => ({ value: tag.trim() }));
                                              tagify.removeAllTags();
                                              tagify.addTags(newTags);
                                          }
    
    
                                          // Listen for tag removal
                                          tagify.on('add', updateInputJobSearch);
                                          tagify.on('remove', updateInputJobSearch);
                                          inputJobSearch.addEventListener('change', updateTagifyUsers);
    
                                          // Initial sync
                                          updateInputJobSearch();
    
                                          // Sync tagify_users with inputJobSearch
                                          inputJobSearch.addEventListener('change', function() {
                                              const newTags = inputJobSearch.value.split(',').map(tag => ({ value: tag.trim() }));
                                              tagify.loadOriginalValues(newTags);
                                          });

                                          function autoclickstar(){
                                        setTimeout(() => {
                                          var starElement = document.querySelector('.star');
                                          starElement.click();
                                          setTimeout(() => {
                                            starElement.click();
                                          }, 200);
                                        }, 500);
                                      }

                                                function debounceskill(func, delay) {
                                                    let timeoutskill;
                                                    return function(...args) {
                                                        clearTimeout(timeoutskill);
                                                        timeoutskill = setTimeout(() => func.apply(this, args), delay);
                                                    };
                                                }

                                                 const fetchSuggestionsSkills = debounceskill(function(query) {
                                                    $.ajax({
                                                        url: "{{ url('/') }}" + "/skill-suggestions?q=" + query,
                                                        method: 'GET',
                                                        success: function(data) {
                                                            console.warn("Fetched Skill: ", data);

                                                            if (Array.isArray(data)) {
                                                                tagify.settings.whitelist = data; // Update whitelist
                                                                // tagify.dropdown.show(query); // Show suggestions
                                                                tagify.dropdown.show.call(tagify);
                                                            } else {
                                                                console.error("Invalid API response format: Expected array.");
                                                            }
                                                        },
                                                        error: function(err) {
                                                            console.error("Error fetching Skill:", err);
                                                        }
                                                    });
                                                }, 200); 

                                                // Listen for user input to trigger API call
                                                tagify.on('input', function(e) {
                                                    var query = e.detail.value.trim();
                                                    if (query.length < 1) return;
                                                    fetchSuggestionsSkills(query);
                                                });
    
    
    
                                        // new Tagify(inputElm);   
                                            
                                    </script>
                                    <!--<textarea class="form-control form-control-solid" rows="4" name="" placeholder="Enter your Skills/Keywords..."></textarea>-->
                                </div>
    
                                <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                  <div class="d-flex flex-column mb-3 fv-row">
                                        <label class="form-label">Current location of Candidate</label>
                                        <input class="form-control form-control-solid" value="{{ $default_loc }}" id="kt_tagify_location" placeholder="Enter your Location..."/>
                                      <!--<input class="form-control form-control-solid" value="" name="tags" placeholder="Enter your Location...">-->
                                      <script>
                                          // The DOM elements you wish to replace with Tagify
                                          var inputloc = document.querySelector("#kt_tagify_location");
                                          var cities = @json($cities);
                                          
                                          // Initialize Tagify script on the above inputs
                                          var tagifylocation =  new Tagify(inputloc, {
                                            whitelist: cities,
                                              maxTags: 10,
                                              dropdown: {
                                                  maxItems: 20,           // <- mixumum allowed rendered suggestions
                                                  classname: "", // tagify__inline__suggestions <- custom classname for this dropdown, so it could be targeted
                                                  enabled: 0,             // <- show suggestions on focus
                                                  closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
                                              }
                                          });

                                          inputloc.addEventListener('keydown', function(e) {
                                              if (e.key === "Enter") {
                                                  e.preventDefault(); // Prevents the default behavior of adding a tag
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
                                      </script>
                                  </div>
                                  <div class="d-flex d-none">
                                      <div class="fw-semibold me-5 d-flex">
                                          <input class="form-check-input h-20px w-20px mr-2" type="checkbox" name="communication[]" value="email" checked="checked">
                                          <div class="fs-7 text-muted">Include Candidate who prefer to relocate</div>
                                      </div>
                                  </div>
                              </div>
                                      
                                <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                    <div class="col-lg-6">
                                      <label class="form-label required">Minimum Experience in years:</label>
                                      <select class="form-select form-select-solid" name="min_exp" id="min_exp">
                                        <option value="" selected="">Select Experience</option>
                                        <option value="0" {{ (0==$min_exp) ? 'selected' : '' }}>Freshers</option>
                                        @for($i=1; $i<=30; $i++)
                                        <option value="{{ $i }}" {{ ($i==$min_exp) ? 'selected' : '' }}>{{ $i }} &nbsp; Year{{ ($i>=1) ? 's' : '' }}</option>
                                        @endfor                    
                                      </select>
                                      <div class="fv-plugins-message-container"></div>
                                    </div>
                                    <div class="col-lg-6">
                                      <label class="form-label required">Maximum Experience in years:</label>
                                      <select class="form-select form-select-solid" name="max_exp" id="max_exp">
                                        <option value="" selected="">Select Experience</option>
                                        <option value="0" {{ (0==$max_exp) ? 'selected' : '' }}>Freshers</option>
                                        @for($i=1; $i<=30; $i++)
                                        <option value="{{ $i }}" {{ ($i==$max_exp) ? 'selected' : '' }}>{{ $i }} &nbsp; Year{{ ($i>=1) ? 's' : '' }}</option>
                                        @endfor                                
                                       </select>
                                      <div class="fv-plugins-message-container" id="experience_error" style="color: red !important;text-align: center;"></div>
                                    </div>
                                </div>    
                                    
                                <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                    <div class="col-lg-12">
                                        <div class="row">
                                                    
                                                    <div class="col-lg-4">
                                                        <div class="row">
                                                          <div class="col-lg-6">
                                                              <label class="form-label required">Currency</label>
                                                              <select class="form-select form-select-solid" arial-label="Select Currency" name="min_salary_currency" id="min_salary_currency">
                                                                <option value="">Select currency</option>
                                                                <option value="0" {{ (0==$min_salary_currency) ? 'selected' : '' }}>INR</option>
                                                                <option value="1" {{ (1==$min_salary_currency) ? 'selected' : '' }}>EUR</option>
                                                                <option value="2" {{ (2==$min_salary_currency) ? 'selected' : '' }}>JPY</option>
                                                                <option value="3" {{ (3==$min_salary_currency) ? 'selected' : '' }}>GBP</option>
                                                                <option value="4" {{ (4==$min_salary_currency) ? 'selected' : '' }}>USD</option>
                                                                <option value="5" {{ (5==$min_salary_currency) ? 'selected' : '' }}>AED</option>
                                                              </select>
                                                          </div>
                                                          <div class="col-lg-6">
                                                              <label class="form-label required">Mode</label>
                                                              <select class="form-select form-select-solid" arial-label="Select Currency" name="sal_mode" id="sal_mode">
                                                                <option value="">Select Mode</option>
                                                                <option value="0" {{ (0==$min_sal_mode) ? 'selected' : '' }}>Annual</option>
                                                                <option value="1" {{ (1==$min_sal_mode) ? 'selected' : '' }}>Monthly</option>
                                                                <option value="2" {{ (2==$min_sal_mode) ? 'selected' : '' }}>Hourly</option>
                                                              </select>
                                                          </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <div class="row"> 
                                                          
                                                            <div class="col-lg-6">
                                                              <label class="form-label required">Minimum Salary:</label>
                                                              <input type="number" class="form-control form-control-lg form-control-solid" name="min_sal" id="min_sal" placeholder="Enter min salary" value="{{ $min_salary }}">
                                                              <div class="fv-plugins-message-container"></div>
                                                              <span class="form-text text-muted minimum_salary_word"></span>
                                                            </div>
                                                            <div class="col-lg-6">
                                                              <label class="form-label required">Maximum Salary:</label>
                                                              <input type="number" class="form-control form-control-lg form-control-solid" name="max_sal" id="max_sal" placeholder="Enter max salary" value="{{ $max_salary }}">
                                                            <div class="fv-plugins-message-container"></div>
                                                            <span class="form-text text-muted maximum_salary_word"></span>
                                                            </div>

                                                            <span class="form-text text-muted" id="salary_error" style="    color: red !important;text-align: center;"></span>                                                             
                                                        </div>
                                                    </div>
    
                                        </div>
                                    </div>
                                </div>
                                
    
                                <div class="d-flex flex-column mb-8">
                                  <label class="form-label required">Hiring type</label>
                                  <div class="row py-5 mb-3">
                                     <div class="col-lg-2">
                                        <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="" id="app_int_outsource1" checked="&quot;checked&quot;">
                                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_outsource1"> 
                                        <span class="d-block fw-bold text-start"> 
                                        <span class="text-dark-800 fw-bolder d-block fs-5">All</span> 
                                        </span> 
                                        </label>
                                     </div>
                                     <div class="col-lg-5">
                                        <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="0" id="app_int_outsource2">
                                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_outsource2"> 
                                        <span class="d-block fw-bold text-start"> 
                                        <span class="text-dark-800 fw-bolder d-block fs-5">Fulltime </span> 
                                        </span> 
                                        </label>
                                     </div>
                                     <div class="col-lg-5">
                                        <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource" value="1" id="app_int_outsource3">
                                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_outsource3"> 
                                        <span class="d-block fw-bold text-start"> 
                                        <span class="text-dark-800 fw-bolder d-block fs-5">Contract</span> 
                                        </span> 
                                        </label>
                                     </div>
                                  </div>
                              </div>
    
    
                                <div class="d-flex flex-column mb-8">
                                  <label class="form-label required">Activity</label>
                                  <div class="row py-5 mb-3">
                                     <div class="col-lg-2">
                                        <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="1" id="app_int_activity1" checked="&quot;checked&quot;">
                                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_activity1"> 
                                        <span class="d-block fw-bold text-start"> 
                                        <span class="text-dark-800 fw-bolder d-block fs-5">All</span> 
                                        </span> 
                                        </label>
                                     </div>
                                     <div class="col-lg-2">
                                        <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="2" id="app_int_activity2">
                                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_activity2"> 
                                        <span class="d-block fw-bold text-start"> 
                                        <span class="text-dark-800 fw-bolder d-block fs-5">30 Days</span> 
                                        </span> 
                                        </label>
                                     </div>
                                     <div class="col-lg-2">
                                        <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="3" id="app_int_activity3">
                                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_activity3"> 
                                        <span class="d-block fw-bold text-start"> 
                                        <span class="text-dark-800 fw-bolder d-block fs-5">60 Days</span> 
                                        </span> 
                                        </label>
                                     </div>
                                     <div class="col-lg-2">
                                        <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="4" id="app_int_activity4">
                                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_activity4"> 
                                        <span class="d-block fw-bold text-start"> 
                                        <span class="text-dark-800 fw-bolder d-block fs-5">90 Days</span> 
                                        </span> 
                                        </label>
                                     </div>
                                     <div class="col-lg-2">
                                        <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="5" id="app_int_activity5">
                                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_activity5"> 
                                        <span class="d-block fw-bold text-start"> 
                                        <span class="text-dark-800 fw-bolder d-block fs-5">180 Days</span> 
                                        </span> 
                                        </label>
                                     </div>
                                     <div class="col-lg-2">
                                        <input type="radio" class="btn-check app_int_activity" name="app_int_activity" value="6" id="app_int_activity6">
                                        <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_activity6"> 
                                        <span class="d-block fw-bold text-start"> 
                                        <span class="text-dark-800 fw-bolder d-block fs-5">1 Year</span> 
                                        </span> 
                                        </label>
                                     </div>
                                  </div>
                              </div>
    
    
                          
    
    
    
    
                          </form>
                       </div>
                       <div class="modal-footer flex-center">
                          <div class="div1">
                             <button type="button" id="" class="btn btn-secondary me-3" data-dismiss="modal">Close</button>
                             <button type="button" id="changetextupdate" class="btn btn-primary changetextupdate"> 
                             <span class="indicator-label">Submit</span></button>
                          </div>
                       </div>
                       <script src="https://servicetesting.4bell.com/assets/node_modules/moment/moment.js?ver=1553620716" type="text/javascript"></script>
                       <script src="https://servicetesting.4bell.com/assets/node_modules/multiselect/js/jquery.multi-select.js?ver=1553620716"></script>
                       <script src="https://servicetesting.4bell.com/assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js?ver=1553620716" type="text/javascript"></script>
                    </div>
                 </div>
              </div>
            </div>	
            <!--End Popup-->


    
    
          @if(in_array("view_jobs", $userPermissions))
          <div class="loadingMessage loadingMessagebenchCandidateRecommendedReqAjax" style="display: none; position: absolute;">Processing...</div>
            <main class="main-content">
            
               <!-- Agency Cards List -->
              <div class="agencies-list" id="benchCandidateRecommendedReqAjax">
              </div>
              
               <div id="paginationbenchCandidateRecommendedReq"></div>
            </main>
          <table id="myTable" class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class d-none">
            <thead>
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                  </div></th>
                <th class="">Jobs</th>
                <th class="">Exp.</th>
                <th class="">Salary</th>
                <th class="">Location</th>
                <th class="">Status</th>
                <th class="">Date</th>
                <th class="">Action</th>
              </tr>
            </thead>
            <tbody id=""> </tbody>
          </table>
          <div id=""></div>
          @else
          <table id="no-access" class="table align-middle table-row-dashed fs-6 gy-5" >
            <thead>
              <tr class="fw-bolder text-muted">
                <th class=""><div align="center">ACCESS DENIED CONTACT ADMINISTRATOR</div></th>
              </tr>
            </thead>
          </table>
          @endif
      </div>
    </div>
  </div>
</div>
<!--end::Content-->

<div class="modal fade bs-modal-sm in" id="scheduleDetailModalStatus" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" id="modal-data-application">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeadingStatus"></span> </div>
      <div class="modal-body"> Loading... </div>
      <div class="modal-footer">
        <button type="button" class="btn default" data-dismiss="modal">Close</button>
        <button type="button" class="btn blue">Save changes</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModalSizeLg" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document"> ... </div>
</div>

<div class="modal fade right" id="sidebar-right" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg">
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Right Sidebar</h4>
      </div>
      <div class="modal-body">
        <ul class="nav nav-pills nav-stacked text-center lead">
          <li role="presentation" class="active"> <a href="#home">Home</a> </li>
          <li role="presentation" class="dropdown"> 
		  	<a href="#" class="dropdown-toggle" id="myTabDrop1" data-toggle="dropdown" aria-controls="myTabDrop1-contents" aria-expanded="false">
				Services <span class="fa fa-fw fa-chevron-down"></span> 
			</a>
            <ul class="dropdown-menu btn-block">
              <li> <a href="#design" role="tab">Design</a> </li>
              <li> <a href="#develop" role="tab">Develop</a> </li>
              <li> <a href="#support" role="tab">Support</a> </li>
            </ul>
          </li>
          <li role="presentation"> <a href="#about">About</a> </li>
        </ul>
        <hr>
        <div class="form-group hide">
          <div class="input-group">
            <input class="form-control" placeholder="Search" name="customSearch" id="customSearch" class="customSearch">
            <span class="input-group-btn">
            <button class="btn btn-default" type="button"> <i class="fa fa-fw fa-search"></i> </button>
            </span> </div>
        </div>
        <div class="form-group has-feedback">
          <input type="text" class="form-control" id="search-right" name="search-right" class="search-right" placeholder="Search">
          <span class="glyphicon glyphicon-search form-control-feedback" aria-hidden="true"></span> </div>
        <hr>
        <div class="btn-group-vertical center-block"> <a class="btn btn-instagram btn-lg" href="https://www.instagram.com/jorenerene/" target="_blank"> <i class="fa fa-fw fa-instagram"></i> </a> <a class="btn btn-twitter btn-lg" href="https://www.twitter.com/jorenerene/" target="_blank"> <i class="fa fa-fw fa-twitter"></i> </a> <a class="btn btn-github btn-lg" href="https://www.github.com/jorenerene/" target="_blank"> <i class="fa fa-fw fa-github"></i> </a> </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bs-modal-lg in" id="JobDetailModalStatus" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal-data-application" style="margin:.5rem auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="JobDetailModalStatusData"></span> </div>
    </div>
  </div>
</div>

<div class="modal fade bs-modal-md in" id="scheduleDetailModal_vis" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" id="modal-data-application" style="margin:.5rem auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading_vis"></span> </div>
    </div>
  </div>
</div>

<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Requisition</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
      </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesJob_close">
        <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span>
        </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
  </div>
</div>
 
@endsection
@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>	
<!--<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>-->

 <script>
        var input2 = document.querySelector("#kt_tagify_2");
        new Tagify(input2);
 </script>
        

<script>
   $(document).ready(function() {	

	   $('#inputSearch').keyup(function() {
			// table._fnDraw();
      fetchBenchRecommendedReqCandidateList();
	   });

     $(document).on("click",".checkrequestsend", function(){ 
                swal("Recommendation Alert", 'Please move the candidate to Bench status before sending the request', "warning");

      });

     $(document).on("click",".request-send", function(){
      var ele = $(this);
      var jobId  = $(this).attr('data-jobId');
      var conId  = $(this).attr('data-conId');
      // var url   		=	"{{ route('service_company.jobs.job_request',':id') }}";
      //       url     		=	url.replace(':id', jobId);
      // alert(url)
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
        // jobs/job_request
            var url   		=	"{{ route('service_company.jobs.recommandedrequestcondidate',':id') }}";
            url     		=	url.replace(':id', jobId)+"?conId="+conId;
            var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		      $.ajax({
   						type  		: 'GET',
   						url   		: url,
   						//  beforeSend	: function() {
              //   elm.parent().html('<img src="'+baseurl+'" height="35px" width="100%">');
              //   $('.confirm').html('<img src="'+baseurl+'" height="35px" width="100%">');

              //          	},
   						success 	: function (response) {
                console.log(response);
                if(response.status=='success'){
                  swal("Recommend!", "Candidates are successfully Recommend!", "success");
                  var jobId  = ele.attr('data-jobId');
                  var conId  = ele.attr('data-conId');
                  var matlaccess = 1;
                  var url   		=	"{{ route('service_company.jobs.mailrecommandedrequestcondidate',':id') }}";
                  url     		=	url.replace(':id', jobId)+"?conId="+conId+"?matlaccess="+matlaccess;                  
                  // tableLoad('load');
                  fetchBenchRecommendedReqCandidateList()
                  $.ajax({
                      type  		: 'GET',
                      url   		: url,
                      success 	: function (response) {
                    
                      }
                      
                  });





                }else{
                  // tableLoad('load');
                  fetchBenchRecommendedReqCandidateList();
                  swal("Recommend Failed", response.message, "warning").then(function(){
                    // tableLoad('load');	
                    fetchBenchRecommendedReqCandidateList();
                  });
                }
                
   						}
              

   	  			});

           

          } else {

            swal("Cancelled", "", "error");

          }
      });
    });

   });
</script>
<script>
  var table;

        setTimeout(() => {
          // tableLoad('load');	
        }, 500);

// setTimeout(() => {
	function tableLoad(type) {
    table = $('#myTable').dataTable({
   
   			responsive  	: true,
   			processing  	: true,
   			serverSide  	: true,
   			destroy   		: true,
   			ordering		: false, //disable ordering	
   			"bLengthChange" : false, //thought this line could hide the LengthMenu   			
   			sDom			: 'lrtip',   			
   			oLanguage 		: { sLengthMenu: "_MENU_", },
			ajax			: {'url' : '{!! route('service_company.recommend.all_data') !!}', 
      "data": function ( d ){
        d.search = $('#inputSearch').val(),
        d.condidate_id = '{{$segment}}',
        d.custom_search = $('#kt_tagify_2').val(),
        d.custom_status = 'active',
        d.custom_hiringtype = $('#hiringtype').val(),
        d.cus_search = $('#kt_tagify_users').val(),
        d.min_exp = $('#min_exp').val(),
        d.max_exp = $('#max_exp').val(),
        d.kt_tagify_location = $('#kt_tagify_location').val(),
        d.min_sal = $('#min_sal').val(),
        d.max_sal = $('#max_sal').val(),
        d.min_salary_currency =  $('#min_salary_currency').val(),
        d.sal_mode = $('#sal_mode').val(),
        d.app_int_activity = $('[name="app_int_activity"]:checked').val(),
        d.app_int_outsource = $('[name="app_int_outsource"]:checked').val()
      }},
   		  "drawCallback"	: function( settings ) {
									$('[data-toggle="tooltip1"]').tooltip();
									$('[data-toggle="tooltip2"]').tooltip();
									$('[data-toggle="tooltip3"]').tooltip();
									$('[data-toggle="tooltip4"]').tooltip();		
							},
   		  "columnDefs"		:
   						  		[ 
   									{ "width": "50px", "targets": 0, "width": "4%", "class":""},
   									{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
   									{  "width": "50px"  , "targets": 2},		
   									{  "width": "100px", "targets": 3 },		
   									{  "width": "200px", "targets": 4 }, 
                    {  "width": "100px" ,"targets": 5 },
   									{  "width": "150px" ,"targets": 6 },
   									{  "width": "200px" , "targets": 7 } 	
   							  ], 
   				columns:
   				[
   				  { data: 'chkbox', name: 'chkbox',  orderable: false, searchable: false},  
   					{ data: 'Requisition', name: 'Requisition' , orderable: false, searchable: false},
   					{ data: 'Exp', name: 'Exp' , orderable: false, searchable: false},
   					{ data: 'Salary', name: 'Salary' , orderable: false, searchable: false},
   					{ data: 'Location', name: 'Location' , orderable: false, searchable: false},
   					
   					{ data: 'Status', name: 'Status' , orderable: false, searchable: false},
   					{ data: 'Date', name: 'Date' , orderable: false, searchable: false},
   					{ data: 'action', name: 'action' , orderable: false, searchable: false} 	   
   				],				
				//timeout: 500000,
           		async: false
   				
   				}); 

        // }, 500);
 
   	       new $.fn.dataTable.FixedHeader( table );
        }

   	$(document).on('change','#kt_tagify_2,#status,#hiringtype',function(){
   	   table._fnDraw();
   	});

     $(document).on('click','.changetextupdate',function(){
      $('.changetextupdate').text('Searching....!');
   	  //  table._fnDraw();
       fetchBenchRecommendedReqCandidateList();
        var $this = $('#apptrackerfilter');
                // $this.fadeOut(2000, function() {
                  $this.modal('hide');
                // });
                setTimeout(() => {
                   $('#apptrackerfilter_close').click();
                   $('.changetextupdate').text('Submit')
              }, 2000);
   	});
   
     $(document).on('click', '.show-detail', function ()
   	{
   		$('#kt_activitiesJob_toggle').trigger('click');		
   		var id    		=	$(this).data('row-id');
   		var url   		=	"{{ route('service_company.jobs.viewmore',':id') }}";
   		url     		=	url.replace(':id', id);
   		var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success 	: function (response) {
									$('#kt_activitiesJob_body').html(response.view);	
   						}
   	  			});
   	}); 
   	
		
     $(document).on('click', '.status_stage', function () { 
		$('#status_body_content').html('');
		var id        	=	$(this).data('row-id');	
		var url       	=	"{{ route('service_company.jobs.editJobStatus',':id') }}";
		url           	=	url.replace(':id', id);
		$.ajax({
					type  	: 'GET',
					url   	: url,
					success : function (response) {
						if (response.status == "success") {
							$('#status_body_content').html(response.view);      
						}
					}
		}); 			
});
	
$(document).on('click', '.client-show-detail', function ()
{
	$('#kt_activitiesClientjob_toggle').trigger('click');	
	var id    		=	$(this).data('row-id');
	var url   		=	"{{ route('service_company.jobs.clientviewmore',':id') }}";
	url     		=	url.replace(':id', id);
	var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
	$.ajax({
					type  		: 'GET',
					url   		: url,
					 beforeSend	: function() {
								 $('#kt_activitiesclientjob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');			
					},
					success 	: function (response) {
							$('#kt_activitiesclientjob_body').html(response.view);		
					}
			});
}); 
	
	
$(document).on('click', '.spartner-listing', function ()
{
	$('#kt_activitiesStaffing_toggle').trigger('click');		
	var id    		= $(this).data('row-id');
	var url   		= "{{ route('service_company.jobs.spartnerlisting',':id') }}";
	url     		= url.replace(':id', id);
	 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
	$.ajax({
					type  : 'GET',
					url   : url,
					 beforeSend: function() {
								 $('#kt_activities_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
					},
					success : function (response) {
							$('#kt_activities_body').html(response.view);		
					}
			});
}); 
	
   
   function updateJobStatus(id, statusName)
   {
     var id        	= id;
     var statusName  = statusName;
     var url       	= "{{ route('service_company.jobs.updatestatus',':id') }}";
     url         	= url.replace(':id', id);
     var token       = "{{ csrf_token() }}";
     $.easyAjax({
					  type		: 'POST',
					  url		: url,
					  data		: {'_token': token, '_method': 'POST', 'id':id, 'statusName':statusName},
					  success	: function (response){
						  table._fnDraw();
					 }
				 });
   }
   
    
</script>
<script>

$(document).ready(function(){

  $(document).on('click', '.hmlisting', function(){
			
														$('#kt_activitiesRecruiter_toggle').trigger('click');  
														var id    		= $(this).data('row-id');
														var token 		=	"{{ csrf_token() }}";
										
											
														var url   		=	"{{ route('service_company.jobs.hmlisting',':id') }}"; 
														url     		=	url.replace(':id', id);  		
														var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";    		
														$.ajax({
																	type  		: 'GET',
																	url   		: url,
																	data		: {'_token': token, 'id' : id},
																	 beforeSend	: function() {
																				 $('#kt_activitiesRecruiter_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
																	},
																	success 	: function (response) {
																				$('#kt_activitiesRecruiter_body').html(response.view);
																	}
															}); 
												
		
		});
		 

});
</script>
<script>

$(document).on('click', '.client-hm-listing', function ()
   	{	
   		$('#kt_activitiesCHM_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('service_company.jobs.clienthmlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  		: 'GET',
   						url   		: url,
   						 beforeSend	: function() {
                                     $('#kt_activitiesCHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) {
								$('#kt_activitiesCHM_body').html(response.view);  								
   						}
   	  			});
   	});
	
     $(document).on('click', '.team-listing', function ()
   	{
   	
   		$('#kt_activitiesTEAM_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('service_company.jobs.currentteamlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  : 'GET',
   						url   : url,
   						 beforeSend: function() {
                                     $('#kt_activitiesTEAM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) {
								$('#kt_activitiesTEAM_body').html(response.view);		
   						}
   	  			});
   	});
	
</script>
<script>

$(document).on('click', '.hm-listing', function ()
   	{
			
   		$('#kt_activitiesHM_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('service_company.jobs.hmlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  : 'GET',
   						url   : url,
   						 beforeSend: function() {
                                     $('#kt_activitiesHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) {		
								$('#kt_activitiesHM_body').html(response.view);		
   						}
   	  			});
   	});


     $(document).ready(function() {
            fetchBenchRecommendedReqCandidateList();
            $(document).on('click', '#paginationbenchCandidateRecommendedReq .page-link', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchBenchRecommendedReqCandidateList(page, $('#search').val()).then(() => {
                  // Re-enable the links after successful data fetch
                  $('#paginationbenchCandidateRecommendedReq .page-link').removeClass('disabled');
                }).catch(() => {
                  // Re-enable the links even if there's an error
                  $('#paginationbenchCandidateRecommendedReq .page-link').removeClass('disabled');
                });
                
            });
        });   

		function fetchBenchRecommendedReqCandidateList(page = 1, search = '') {
        
				$('.loadingMessagebenchCandidateRecommendedReqAjax').css({ display: 'block' });
				$.ajax({
					url: `{!! route('service_company.recommend.all_data_ajax') !!}?page=${page}`,
					type: 'GET',
					data: {
						search:$('#inputSearch').val(),
            condidate_id: "{{$segment}}",
            custom_search:$('#kt_tagify_2').val(),
            custom_status:'active',
            custom_hiringtype:$('#hiringtype').val(),
            cus_search:$('#kt_tagify_users').val(),
            min_exp:$('#min_exp').val(),
            max_exp:$('#max_exp').val(),
            kt_tagify_location:$('#kt_tagify_location').val(),
            min_sal:$('#min_sal').val(),
            max_sal:$('#max_sal').val(),
            min_salary_currency: $('#min_salary_currency').val(),
            sal_mode:$('#sal_mode').val(),
            app_int_activity:$('[name="app_int_activity"]:checked').val(),
            app_int_outsource:$('[name="app_int_outsource"]:checked').val()
					},
					success: function(response) {
						$('.loadingMessagebenchCandidateRecommendedReqAjax').css({ display: 'none' });
						$('#benchCandidateRecommendedReqAjax').html(response.html);
            $('#recommend_req_id').html(response.total);
						$('#paginationbenchCandidateRecommendedReq').html(response.pagination);
					}
				});
		}



</script>
@endpush 