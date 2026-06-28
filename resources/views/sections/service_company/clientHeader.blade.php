         <div class="d-flex flex-column {{$tabactive['allClient_active']}} customDivWidthJobs">
              <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{$tabactive['allClient_url']}}"
                      class="{{ ($tabactive['allClient_active']) ? 'text-primary' : 'text-gray-600'}} mb-1"> {{ __('messages.all_clients') }}</a> </h5>
              <span class="text-gray-600 existing_data" id="{{$tabactive['span_clientAll']}}">{{$tabactive['clientAll']}}</span>
          </div>
         
            <span class="h-20px border-gray-200 border-start mx-4"></span>
            <div class="d-flex flex-column {{$tabactive['clientDirect_active']}} customDivWidthJobs">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <a href="{{$tabactive['clientDirect_url']}}"
                            class="{{ ($tabactive['clientDirect_active']) ? 'text-primary' : 'text-gray-600'}} text-hover-primary mb-1"> {{ __('messages.direct_clients') }}
                        </a> </h5>
                    <span class="text-gray-600" id="{{$tabactive['span_clientDirect']}}">{{$tabactive['clientDirect']}}</span>
            </div>
            <span class="h-20px border-gray-200 border-start mx-4"></span>
            <div class="d-flex flex-column {{$tabactive['customClient_active']}} customDivWidthJobs">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <a href="{{$tabactive['customClient_url']}}"
                            class="{{ ($tabactive['customClient_active']) ? 'text-primary' : 'text-gray-600'}} text-hover-primary mb-1"> {{ __('messages.internal_clients') }}
                        </a> </h5>
                    <span class="text-gray-600" id="{{$tabactive['span_clientcustom']}}">{{$tabactive['clientCustom']}}</span>
            </div>
            <span class="h-20px border-gray-200 border-start mx-4"></span>
            <div class="d-flex flex-column {{$tabactive['clientRequest_active']}} customDivWidthJobs">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <a href="{{$tabactive['clientRequest_url']}}"
                            class="{{ ($tabactive['clientRequest_active']) ? 'text-primary' : 'text-gray-600'}} text-hover-primary mb-1"> {{ __('messages.client_requests') }}
                        </a> </h5>
                    <span class="text-gray-600" id="{{$tabactive['span_clientRequest']}}">{{$tabactive['clientRequest']}}</span>
            </div>
          