@extends('layouts.product_company_layout')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">

<style>
.content-header{display: none;}
iframe{width: 100%!important;}
.modal-header{padding: 0px!important;margin: 0 auto;border-bottom: none;}

/* *** The Checkboxes *** */
/*label.btn.toggle-checkbox > i.fa:before { content:"\f096"; }
    label.btn.toggle-checkbox > i.fa:before { content:"\f046"; }*/

label.btn.active { box-shadow: none; }
/*label.btn.default.active 
{    
    background-color: #222222;
    border-color: #111111;
    color: #ffffff;
    box-shadow: none;
}*/
label.btn.default
{    
    background-color: #2196f3;
    border-color: #2196f3;
    color: #ffffff;
    box-shadow: none;
}
label.btn.toggle-checkbox > i.fa:before > [type="checkbox"]:not(:checked){
    content:"\f096!important";
}
label.btn.toggle-checkbox > i.fa:before > [type="checkbox"]:checked {
    content:"\f046!important";
}

/* *** The Checkboxes End *** */

</style>

@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title pt-2 pb-2"><i class="fa fa-file"></i>&nbsp;Application Settings</h4>
                    <form id="editSettings" class="ajax-form">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">
                                        <i class="fa fa-tags"></i>&nbsp;@lang('modules.applicationSetting.legalTermText')
                                    </label>
                                    <textarea class="form-control" id="legal_term" name="legal_term" rows="15" placeholder="Enter text ...">{!! $setting->legal_term !!}</textarea>
                                </div>
                            </div>
                        </div>    

                        <hr>

                        <div class="col-md-12 pl-0">
                            <h4 class="card-title mb-2">
                                <i class="fa fa-envelope"></i>&nbsp; @lang('modules.applicationSetting.mailSettings')                            
                            </h4>
                            <label class="">Send mail if candidate move to </label>
                        </div>

                        <div id="mail-setting" class="row">
                            <br>
                            @forelse($settings->mail_setting as $key => $mailSetting)
                                <div class="form-group" data-toggle="buttons">
                                    <label class="btn btn-default btn-sm toggle-checkbox default ml-1 mr-1">
                                        <i class="fa fa-fw"></i>
                                        <input id="default" autocomplete="off" type="checkbox"  aria-checked="false" aria-disabled="false"
                                        @if($mailSetting['status']) checked
                                                @endif value="{{$key}}" name="checkBoardColumn[]"
                                                class="columnCheck icheckbox_flat-green"
                                        > 
                                        {{ ucwords($mailSetting['name']) }}
                                    </label>
                                </div>
                            @empty
                            @endforelse
                        </div>

                        <div class="row">
                            <div class="col-12 text-center py-4">
                                <button type="button" id="save-form" class="btn btn-light-success btn-sm">
                                    <i class="fa fa-save"></i>&nbsp;@lang('app.save')
                                </button>
                                <button type="reset" class="btn btn-light-warning btn-sm">
                                    <i class="fa fa-refresh"></i>&nbsp;@lang('app.reset')
                                </button>
                            </div>    
                        </div>    
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/html5-editor/wysihtml5-0.3.0.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>

<script>
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
    })

    var jobDescription = $('#legal_term').wysihtml5({
        "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
        "emphasis": true, //Italics, bold, etc. Default true
        "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
        "html": true, //Button which allows you to edit the generated HTML. Default false
        "link": true, //Button to insert a link. Default true
        "image": true, //Button to insert an image. Default true,
        "color": true, //Button to change color of font
        stylesheets: ["{{ asset('assets/node_modules/html5-editor/wysiwyg-color.css') }}"], // (path_to_project/lib/css/wysiwyg-color.css)
    });

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('product_company.application-setting.update', $global->id)}}',
            container: '#editSettings',
            type: "POST",
            redirect: true,
            file: true
        })
    });
</script>

  
@endpush