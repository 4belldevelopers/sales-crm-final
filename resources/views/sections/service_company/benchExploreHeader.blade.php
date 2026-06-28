<div class="d-flex flex-column {{ $tabactive['bench_explore_pool_active'] }} customDivWidthdirect">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> 
                        <a href="{{ $tabactive['bench_explore_pool_url'] }}"
                            class="{{ ($tabactive['bench_explore_pool_active']) ? 'text-primary' : 'text-gray-600'}}  text-hover-primary mb-1">Pool Jobs</a> </h5>
                    <span class="text-gray-600 existing_data" id="{{ $tabactive['bench_explore_pool_id'] }}">{{ $tabactive['bench_explore_counter'] }}</span>
                </div>
                
                    <span class="h-20px border-gray-200 border-start mx-4"></span>
                    <div class="d-flex flex-column {{ $tabactive['recommend_req_active'] }} customDivWidthdirect">
                        <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                            <a href="{{ $tabactive['recommend_req_url'] }}"
                                class="{{ ($tabactive['recommend_req_active']) ? 'text-primary' : 'text-gray-600'}} text-hover-primary mb-1">Recommend Client Jobs
                            </a> </h5>
                        <span class="text-gray-600" id="{{ $tabactive['recommend_req_id'] }}">{{ $tabactive['recommend_req_counter'] }}</span>
                    </div>
                    <span class="h-20px border-gray-200 border-start mx-4"></span>
                    <div class="d-flex flex-column {{ $tabactive['recommend_req_internal_active'] }} customDivWidthdirect">
                        <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                            <a href="{{ $tabactive['recommend_req_internal_url'] }}"
                                class="{{ ($tabactive['recommend_req_internal_active']) ? 'text-primary' : 'text-gray-600'}} text-hover-primary mb-1">Recommend Internal Jobs
                            </a> </h5>
                        <span class="text-gray-600" id="{{ $tabactive['recommend_req_internal_id'] }}">{{ $tabactive['recommend_req_internal_counter'] }}</span>
                    </div>
               