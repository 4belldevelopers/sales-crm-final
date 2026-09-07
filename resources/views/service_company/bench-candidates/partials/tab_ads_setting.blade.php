<div class="content-split">
    <div class="content-main">
        <section class="cardshow">
            <div class="card-header-sidebar">
                <div class="card-icon" style="background: var(--color-purple-50); color: var(--color-purple-500);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
                <h3 class="card-title">Boostkeywords</h3>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-8"></label>
                <div class="switchery-demo form-check form-switch form-switch-sm form-check-custom form-check-solid">
                    <input type="checkbox" class="js-switch form-check-input checkStatusSales" name="checkStatusSales" value="{{ $benchApplication->id }}" @if (@$benchApplication->ads_switch == 'Yes') checked @endif data-color="#99d683" data-size="small"/>
                </div>
                <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 checkStatusSales_text">{{ @$benchApplication->ads_switch == 'Yes' ? 'Ads Active' : 'Ads Block' }}</a>
            </div>
            <br>
            <label class="form-label required fw-bolder">Description</label>
            <p class="summary-text summary-text-width"></p>
        </section>
    </div>
</div>
