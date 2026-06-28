 <div class="filter-bar">
              <div class="search-input-wrap">
                  <input type="text" class="search-input inputJobSearch" name="inputJobSearch"
                      value="" placeholder="Search....." id="kt_tagify_2"/>
              </div>
              
              <div class="col-lg-2 d-flex align-items-center px-2" style="width: 10%;">
                <select class="form-select form-select-lg" data-hide-search="true" id="status" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-3="filter_status" onchange="filtersdata()">
                  <option value="all">All Status</option>
                  <option value="active" selected="selected">Hiring</option>   
                  <option value="hold">Hold</option>     
                  <option value="inactive">Closed</option>    
                  <option value="removed">Removed</option>
                </select>
              </div>
              
              <div class="col-lg-2 d-flex align-items-center px-2" style="width: 10%;">
                <select class="form-select form-select-lg" data-hide-search="true" id="employment_type" data-dropdown-css-class="w-150px" data-placeholder="Select an option" onchange="filtersdata()">
                  <option value="all" selected="selected">All Types</option>
                  <option value="0">Full-time</option>   
                  <option value="1">Contract</option>     
                  <option value="2">Freelance</option>    
                  <option value="3">Part Time</option>
                </select>
              </div>
              
              <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                <select class="form-select form-select-lg" data-hide-search="true" id="hiringtype" data-dropdown-css-class="w-150px" data-placeholder="Select an option" onchange="filtersdata()">
                  <option value="All">Sort</option>
                  <option value="1" selected="selected">Newest to Oldest (Date)</option>
                  <option value="2">Oldest to Newest (Date)</option>
                  <option value="3">Low to High (Salary)</option>
                  <option value="4">High to Low (Salary)</option>
                  <option value="5">A to Z (Company Wise)</option>
                  <option value="6">Z to A (Company Wise)</option>
                </select>
              </div>
              
              {{-- <div class="more-filters-btn" data-toggle="modal" data-target="#apptrackerfilter">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/>
                  </svg>
                  More Filters
              </div> --}}
              @if(in_array("add_jobs", $searchbar['userPermissions']))
                  <button class="" data-toggle="modal" data-target="#add_staffing_partners">
                     <a  href="javascript:;" type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3  py-4" id="AddjobSection" style="width: 100%">
                      <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
                        <path d="M12 5v14M5 12h14" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                      </svg>
                      Add Job
                    </a>
                  </button>
              @endif

          </div>