@php
		use App\Helper\GeneralMethod;
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
</style>
<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <input type="hidden" name="hid_job_id" id="hid_job_id" value="" />
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
 
	
<div class="post d-flex flex-column-fluid" id="kt_post">
  <div id="kt_content_container" class="container-fluid px-3">
    <div class="card">
      <div class="card-body pt-3 px-5">

		 <div class="d-flex align-items-center position-relative my-1 me-3"> <span class="svg-icon svg-icon-1 position-absolute ms-6">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
                  </svg>
                  </span>
                  <input type="text" id="inputCopySearch" name="inputCopySearch" data-kt-user-table-filter="search" class="form-control form-control-solid ps-14" style="padding: .8rem 1rem;" placeholder="Search...">

                    <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                        <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end" data-toggle="modal" data-target="#apptrackerfilterBench">
                            <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black">
                                    </path>
                                </svg>
                            </span> Filter
                        </button>
                    </div>
        </div>


        
            <!--Start Popup-->
            <div class="modal fade show" id="apptrackerfilterBench" tabindex="-1" style="">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header flex-stack">
                            <h2>Filter (Search Bench Talents)</h2>
                            <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal" id="apptrackerfilterBench_close">
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)"></rect>
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"></rect>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="apptrackerfilterBench_body_content" id="">
                            <div class="modal-body" style="padding: 2em;">
                                <form id="createSchedule" class="ajax-form" method="post">
                                    <div class="section">
                                        <p class="section-label">1. Search Skills / Keywords</span></p> 
                                        <div class="search-input-wrap">
                                            <input class="search-input" name="kt_tagify_skills" value="{{ @$default_skill }}" placeholder="Enter Skills" id="kt_tagify_skills" />
                                        </div>
                                    </div>

                                    <div class="section">
                                        <p class="section-label">2. Select the City</span></p> 
                                        <div class="search-input-wrap">
                                            <input class="search-input" value="" id="kt_tagify_location_bench" placeholder="Search City" />
                                        </div>
                                    </div>

                                    <div class="section form-group row fv-row mb-10 fv-plugins-icon-container">
                                        <p class="section-label">3. Experience</span></p> 
                                        <div class="col-lg-6">
                                            <label class="form-label required">Minimum years:</label>
                                            <select class="form-select form-select-solid" name="min_exp_bench" id="min_exp_bench">
                                            <option value="" selected="">Select Experience</option>
                                            <option value="0">Freshers</option>
                                            @for($i=1; $i<=30; $i++)
                                            <option value="{{ $i }}">{{ $i }} &nbsp; Year{{ ($i>=1) ? 's' : '' }}</option>
                                            @endfor                    
                                            </select>
                                            <div class="fv-plugins-message-container"></div>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label required">Maximum years:</label>
                                            <select class="form-select form-select-solid" name="max_exp_bench" id="max_exp_bench">
                                            <option value="" selected="">Select Experience</option>
                                            <option value="0">Freshers</option>
                                            @for($i=1; $i<=30; $i++)
                                            <option value="{{ $i }}">{{ $i }} &nbsp; Year{{ ($i>=1) ? 's' : '' }}</option>
                                            @endfor                                
                                            </select>
                                            <div class="fv-plugins-message-container" id="experience_bench_error" style="color: red !important;text-align: center;"></div>
                                        </div>
                                    </div> 


                                    <div class="section form-group row fv-row mb-10 fv-plugins-icon-container">
                                        <p class="section-label">4. Bill Rates</span></p> 
                                        <div class="col-lg-12">
                                            <div class="row">
                                                    
                                                    <div class="col-lg-4">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Currency</label>
                                                                <select class="form-select form-select-solid" arial-label="Select Currency" name="min_salary_currency_bench" id="min_salary_currency_bench">
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
                                                                <label class="form-label required">Mode</label>
                                                                <select class="form-select form-select-solid" arial-label="Select Currency" name="sal_mode_bench" id="sal_mode_bench">
                                                                    <option value="">Select Mode</option>
                                                                    <option value="0" {{ $min_sal_mode==0 ? 'selected' : '' }}>Annual</option>
                                                                    <option value="1" {{ $min_sal_mode==1 ? 'selected' : '' }}>Monthly</option>
                                                                    <option value="2" {{ $min_sal_mode==2 ? 'selected' : '' }}>Hourly</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <label class="form-label required">Minimum Bill Rates:</label>
                                                                @php
                                                                    $min_salary = [
                                                                        0 => @$job->min_yearly,
                                                                        1 => @$job->min_monthly,
                                                                        2 => @$job->min_hourly
                                                                    ][@$job->min_sal_mode] ?? '';
                                                                @endphp

                                                                <input type="number" class="form-control form-control-lg form-control-solid" name="min_sal_bench" id="min_sal_bench" placeholder="Enter min salary" value="">
                                                                <div class="fv-plugins-message-container"></div>
                                                                <span class="form-text text-muted minimum_salary_word"></span>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                    <label class="form-label required">Maximum Bill Rates:</label>
                                                                    @php
                                                                        $max_salary = [
                                                                            0 => @$job->max_yearly,
                                                                            1 => @$job->max_monthly,
                                                                            2 => @$job->max_hourly
                                                                        ][@$job->max_sal_mode] ?? '';
                                                                    @endphp
                                                                    <input type="number" class="form-control form-control-lg form-control-solid" name="max_sal_bench" id="max_sal_bench" placeholder="Enter max salary" value="">
                                                                    <div class="fv-plugins-message-container"></div>
                                                                    <span class="form-text text-muted maximum_salary_word"></span>
                                                            </div>  
                                                            <span class="form-text text-muted" id="salary_bench_error" style="    color: red !important;text-align: center;"></span>                                                             
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="section d-flex flex-column mb-8">
                                        <p class="section-label">5. Outsource type</span></p> 
                                        <div class="row py-5 mb-3">
                                            <div class="col-lg-4">
                                                <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource_bench" value="0" id="app_int_outsource_bench0" checked>
                                                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_outsource_bench0">
                                                    <span class="d-block fw-bold text-start">
                                                        <span class="text-dark-800 fw-bolder d-block fs-5">All</span>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource_bench" value="1" id="app_int_outsource_bench2">
                                                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_outsource_bench2">
                                                    <span class="d-block fw-bold text-start">
                                                        <span class="text-dark-800 fw-bolder d-block fs-5">Fulltime Hiring</span>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input type="radio" class="btn-check app_int_outsource" name="app_int_outsource_bench" value="2" id="app_int_outsource_bench3">
                                                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_outsource_bench3">
                                                    <span class="d-block fw-bold text-start">
                                                        <span class="text-dark-800 fw-bolder d-block fs-5">Contract Hiring</span>
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
                                    <button type="button" id="changetextupdatebench" class="btn btn-primary">
                                        <span class="indicator-label changetextupdatebench">Submit</span>
                                    </button>
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


        
        <div class="bench-bt">
        <table class="table align-middle table-row-dashed fs-6">
          <thead>
            <tr class="fw-bolder text-muted">
              <th style="min-width:180px;">Talent</th>
              <th style="min-width:90px;">Experience</th>
              <th style="min-width:110px;">Availability</th>
              <th style="min-width:110px;">Location</th>
              <th style="min-width:130px;">Expected Salary</th>
              <th style="min-width:130px;">Bench Status</th>
              <th style="min-width:130px;">Relationship Manager</th>
              <th style="min-width:100px;">Action</th>
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

	window.fetchCandidatesbench = function fetchCandidatesbench(page = 1, search = '') {
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

        /* .request-send handler is registered once in market-jobs.blade.php to avoid duplicate bindings */

       $(document).on("click",".changetextupdatebench", function(e){
        e.preventDefault();
        $('.changetextupdatebench').text('Searching....!')
           fetchCandidatesbench();
        var $this = $('#apptrackerfilterBench');
        // $this.fadeOut(2000, function() {
        $this.modal('hide');
        // });
        setTimeout(() => {
            $('#apptrackerfilterBench_close').click();
            $('.changetextupdatebench').text('Submit')
            
        }, 1500);
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

