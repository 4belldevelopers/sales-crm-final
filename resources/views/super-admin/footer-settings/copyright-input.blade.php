<div class="form-group" data-language-id="{{ !empty($footerSettings) ? $footerSettings->language_settings_id : $languageId }}">
    <h5 class="box-title fs-6 fw-bold form-label mb-2">@lang('modules.footerSettings.copyrightText')</h5>
    <input type="text" name="footer_copyright_text" class="form-control form-control-solid" value="{{ !empty($footerSettings) ? $footerSettings->footer_copyright_text : '' }}" />
</div>