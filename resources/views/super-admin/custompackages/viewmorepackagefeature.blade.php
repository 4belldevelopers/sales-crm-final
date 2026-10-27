{{--
    Modern Enterprise Feature & Coin Deduction Matrix
    Rendered via AJAX on package selection
--}}
@if (isset($coin_events) && count($coin_events) > 0)
    <div class="card border border-gray-200 shadow-none mb-6">
        <div class="card-header border-0 pt-4 pb-2 px-5 bg-light-primary rounded-top">
            <div class="card-title m-0">
                <span class="svg-icon svg-icon-2 text-primary me-2">
                    <i class="fa-solid fa-list-check text-primary fs-5"></i>
                </span>
                <h5 class="fw-bold text-gray-800 m-0" style="font-size: 14px;">Coin Deduction Rules & Event Matrix</h5>
            </div>
            <div class="card-toolbar">
                <span class="badge badge-primary px-3 py-1" style="font-size: 11px;">
                    <i class="fa-solid fa-coins me-1 text-white"></i> {{ count($coin_events) }} Features Configured
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-row-bordered table-row-gray-200 align-middle gs-4 gy-3 my-0">
                    <thead class="bg-light">
                        <tr class="fw-semibold text-gray-600 text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                            <th class="ps-5 min-w-180px">Feature / Activity</th>
                            <th class="min-w-120px text-center">Deduction Unit</th>
                            <th class="min-w-130px text-center">Rate (Coins)</th>
                            <th class="pe-5 min-w-140px text-end">Min Balance Required</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700" style="font-size: 12.5px;">
                        @foreach ($coin_events as $index => $val)
                            <tr class="align-middle">
                                <td class="ps-5">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px symbol-circle me-3 bg-light-info">
                                            <span class="symbol-label">
                                                <i class="fa-solid fa-bolt text-info fs-7"></i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-900 fw-semibold">{{ $val->title }}</span>
                                            <span class="text-muted fs-8">ID: #{{ $val->id }} &bull; {{ ucfirst($val->event_type ?? $event_type ?? 'operational') }}</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="id[]" value="{{ $val->id }}">
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-secondary text-gray-700 fw-semibold px-2 py-1 text-capitalize" style="font-size: 11px;">
                                        {{ $val->deduction_type ?: 'Per Action' }}
                                    </span>
                                    <input type="hidden" name="deduction_type[]" id="{{ $val->id }}_deduction_type" value="{{ $val->deduction_type }}">
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center justify-content-center">
                                        <div class="input-group input-group-sm w-110px">
                                            <span class="input-group-text bg-light border-gray-300 py-1 px-2 text-warning">
                                                <i class="fa-solid fa-coins fs-8"></i>
                                            </span>
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0"
                                                   class="form-control form-control-solid form-control-sm text-end fw-semibold deduction-rate-input"
                                                   name="default_rate[]" 
                                                   id="{{ $val->id }}_default_rate"
                                                   value="{{ $val->default_rate }}"
                                                   style="font-size: 12px; height: 32px;"
                                                   data-event-id="{{ $val->id }}">
                                        </div>
                                    </div>
                                </td>
                                <td class="pe-5 text-end">
                                    <span class="badge badge-light-primary fw-bold px-2 py-1" style="font-size: 11px;">
                                        <i class="fa-solid fa-shield-halved me-1 text-primary"></i> {{ number_format($val->minimum_required, 0) }} Coins
                                    </span>
                                    <input type="hidden" name="minimum_required[]" id="{{ $val->id }}_minimum_required" value="{{ $val->minimum_required }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Hidden fields passed from packageFeature response to ensure seamless form serialization --}}
    <input type="hidden" name="event_type" id="event_type" value="{{ $event_type ?? 'operational' }}">
@else
    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-6">
        <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
            <i class="fa-solid fa-circle-exclamation text-warning fs-3"></i>
        </span>
        <div class="d-flex flex-stack flex-grow-1">
            <div class="fw-semibold">
                <h5 class="text-gray-900 fw-bold fs-6 mb-1">No Coin Events Configured</h5>
                <div class="fs-7 text-gray-600">This package does not have any active coin deduction rules defined for this event type.</div>
            </div>
        </div>
    </div>
@endif
