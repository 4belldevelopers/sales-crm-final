         
         

          <div class="hero-section">
              <div class="hero-left">
                  <div class="stats-row">
                    <a href="{{$tabactive['allRequisition_url']}}">
                            <div class="stat-card {{$tabactive['allRequisition_active']}} customDivWidthJobs">
                                <div class="stat-icon blue" style="margin-right: 7px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                </div>
                                <div class="stat-info">
                                <div class="stat-num" id="{{$tabactive['allRequisition_id']}}">{{$tabactive['allRequisition']}}</div>
                                <div class="stat-label">Total Active Jobs</div>
                                </div>
                            </div>
                        </a>
                @if($tabactive['productAllowCompany'])
                    <a href="{{$tabactive['clientRequisition_url']}}">
                        <div class="stat-card {{$tabactive['clientRequisition_active']}} customDivWidthJobs">
                            <div class="stat-icon green" style="margin-right: 7px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                            </div>
                            <div class="stat-info">
                            <div class="stat-num" id="{{$tabactive['clientRequisition_id']}}">{{$tabactive['clientRequisition']}}</div>
                            <div class="stat-label">Client Jobs</div>
                            </div>
                        </div>
                    </a>

                    <a href="{{$tabactive['customRequisition_url']}}">
                        <div class="stat-card {{$tabactive['customRequisition_active']}} customDivWidthJobs">
                            <div class="stat-icon yellow" style="margin-right: 7px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                            </div>
                            <div class="stat-info">
                            <div class="stat-num" id="{{$tabactive['customRequisition_id']}}">{{$tabactive['customRequisition']}}</div>
                            <div class="stat-label">Internal Jobs</div>
                            </div>
                        </div>
                    </a>

                    <a href="{{$tabactive['jobRequest_url']}}">
                        <div class="stat-card {{$tabactive['jobRequest_active']}} customDivWidthJobs">
                            <div class="stat-icon purple" style="margin-right: 7px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                            </svg>
                            </div>
                            <div class="stat-info">
                            <div class="stat-num" id="{{$tabactive['jobRequest_id']}}">{{$tabactive['jobRequest']}}</div>
                            <div class="stat-label">Job Request Received</div>
                            </div>
                        </div>
                    </a>
                @endif
                  </div>
              </div>
          </div>
         
         
         {{-- <div class="d-flex flex-column {{$tabactive['allRequisition_active']}} customDivWidthJobs">
              <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{$tabactive['allRequisition_url']}}"
                      class="{{ ($tabactive['allRequisition_active']) ? 'text-primary' : 'text-gray-600'}} mb-1"> {{ __('messages.all_jobs') }} Openings</a> </h5>
              <span class="text-gray-600 existing_data" id="{{$tabactive['allRequisition_id']}}">{{$tabactive['allRequisition']}}</span>
          </div> --}}
          {{-- @if($tabactive['productAllowCompany'])
            <span class="h-20px border-gray-200 border-start mx-4"></span>
            <div class="d-flex flex-column {{$tabactive['customRequisition_active']}} customDivWidthJobs">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <a href="{{$tabactive['customRequisition_url']}}"
                            class="{{ ($tabactive['customRequisition_active']) ? 'text-primary' : 'text-gray-600'}} text-hover-primary mb-1"> {{ __('messages.internal_jobs') }} Openings
                        </a> </h5>
                    <span class="text-gray-600" id="{{$tabactive['customRequisition_id']}}">{{$tabactive['customRequisition']}}</span>
            </div>
            <span class="h-20px border-gray-200 border-start mx-4"></span>
            <div class="d-flex flex-column {{$tabactive['clientRequisition_active']}} customDivWidthJobs">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <a href="{{$tabactive['clientRequisition_url']}}"
                            class="{{ ($tabactive['clientRequisition_active']) ? 'text-primary' : 'text-gray-600'}} text-hover-primary mb-1"> {{ __('messages.client_jobs') }} Openings
                        </a> </h5>
                    <span class="text-gray-600" id="{{$tabactive['clientRequisition_id']}}">{{$tabactive['clientRequisition']}}</span>
            </div>
            <span class="h-20px border-gray-200 border-start mx-4"></span>
            <div class="d-flex flex-column {{$tabactive['jobRequest_active']}} customDivWidthJobs">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <a href="{{$tabactive['jobRequest_url']}}"
                            class="{{ ($tabactive['jobRequest_active']) ? 'text-primary' : 'text-gray-600'}} text-hover-primary mb-1"> {{ __('messages.job_requests') }}
                        </a> </h5>
                    <span class="text-gray-600" id="{{$tabactive['jobRequest_id']}}">{{$tabactive['jobRequest']}}</span>
            </div>
          @endif --}}