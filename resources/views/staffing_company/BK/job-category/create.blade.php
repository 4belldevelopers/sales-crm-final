@extends('layouts.app')

<style>
.content-header
{
    display: none;
}
#btnFiterSubmitSearch
{
    padding:0.5rem 0.20rem!important;
}
.input-group > .custom-select:not(:last-child), .input-group > .form-control:not(:last-child)
{
    margin: 0em!important;
}
</style>
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title pt-2 mb-2"><i class="fa fa-plus-square"></i>&nbsp;Add Category</h4>

                    <form class="ajax-form" method="POST" id="createForm" onsubmit="return false;">
                        @csrf

                    <div id="education_fields">
                        <div class="row">
                            <div class="col-sm-6 nopadding">
                                <div class="form-group mb-1">
                                    <div class="input-group">
                                        <input type="text" name="name[]" class="form-control" placeholder="@lang('menu.jobCategories') @lang('app.name')">
                                        <div class="input-group-append">
                                            <button class="btn btn-light-success btn-sm" type="button" id="add-more">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row pt-4">
                        <div class="col-12 text-center">
                            <button type="button" id="save-form" class="btn btn-light-success btn-sm">
                                <i class="fa fa-save"></i>&nbsp;@lang('app.submit')
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
    <script>
        var room = 1;

        $('#add-more').click(function(){
            room++;

            var divtest = `
                <div class="row removeclass${room}">
                    <div class="col-sm-6 nopadding">
                        <div class="form-group">
                            <div class="input-group mb-1">
                                <input type="text" name="name[]" class="form-control" placeholder="@lang('menu.jobCategories') @lang('app.name')">
                                <div class="input-group-append">
                                    <button class="btn btn-light-danger btn-sm" type="button" onclick="remove_education_fields(${room});">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>`;

            $('#education_fields').append(divtest);
            $(`.removeclass${room} input`).focus();
        })

        function remove_education_fields(rid) {
            $('.removeclass' + rid).remove();
        }

        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('admin.job-categories.store')}}',
                container: '#createForm',
                type: "POST",
                redirect: true,
                data: $('#createForm').serialize()
            })
        });
    </script>
@endpush