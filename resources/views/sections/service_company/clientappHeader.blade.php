          <div class="d-flex flex-column {{ $tabactive['pool_active'] }} customDivWidthdirect">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> 
                        <a href="{{ $tabactive['pool_url'] }}"
                            class="{{ ($tabactive['pool_active']) ? 'text-primary' : 'text-gray-600'}}  text-hover-primary mb-1">Pool</a> </h5>
                    <span class="text-gray-600 existing_data" id="{{ $tabactive['pool_id'] }}">{{ $tabactive['pool_counter'] }}</span>
            </div>
          <span class="h-20px border-gray-200 border-start mx-4"></span>        
           <div class="d-flex flex-column {{ $tabactive['similar_active'] }} customDivWidthdirect">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <a href="{{ $tabactive['similar_url'] }}"
                            class="{{ ($tabactive['similar_active']) ? 'text-primary' : 'text-gray-600'}} text-hover-primary mb-1">{{ __('messages.existing_data') }}
                        </a> </h5>
                    <span class="text-gray-600" id="{{ $tabactive['similar_id'] }}">{{ $tabactive['existing_counter'] }}</span>
            </div>
          <span class="h-20px border-gray-200 border-start mx-4"></span> 
           <div class="d-flex flex-column {{ $tabactive['inhouseBench_active'] }} customDivWidthdirect">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <a href="{{ $tabactive['inhouseBench_url'] }}"
                            class="{{ ($tabactive['inhouseBench_active']) ? 'text-primary' : 'text-gray-600'}} text-hover-primary mb-1">{{ __('messages.internal_bench_data') }} 
                        </a>
                    </h5>
                    <span class="text-gray-600" id="{{ $tabactive['inhouseBench_id'] }}">{{ $tabactive['internal_bench_counter'] }}</span>
            </div>
        <span class="h-20px border-gray-200 border-start mx-4"></span>
        <div class="d-flex flex-column {{ $tabactive['career_active'] }} customDivWidthdirect">
            <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a
                    href="{{ $tabactive['career_url'] }}"
                    class="{{ ($tabactive['career_active']) ? 'text-primary' : 'text-gray-600'}} text-hover-primary mb-1">{{ __('messages.career_apply') }}</a> </h5>
            <span class="text-gray-600" id="{{ $tabactive['career_id'] }}">{{ $tabactive['career_counter'] }}</span>
        </div>