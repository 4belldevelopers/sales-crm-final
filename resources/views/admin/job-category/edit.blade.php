@extends('layouts.app')

<style>
.content-header{display: none;}
#btnFiterSubmitSearch
{
    padding:0.5rem 0.20rem!important;
} 
</style>


@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title pt-2 pb-2"><i class="fa fa-edit"></i>&nbsp;@lang('app.edit') Category</h4>
                    <form class="ajax-form" method="POST" id="createForm">
                        @csrf
                        <input name="_method" type="hidden" value="PUT">
                        <div id="education_fields"></div>
                        <div class="row">
                            <div class="col-sm-6 nopadding">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" name="name" value="{{ $category->name }}" class="form-control" placeholder="@lang('menu.jobCategories') @lang('app.name')">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row pt-4">
                            <div class="col-12 text-center">
                                <button type="button" id="save-form" class="btn btn-light-success btn-sm">
                                    <i class="fa fa-save"></i>&nbsp;@lang('app.update')
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
        var objTo = document.getElementById('education_fields')
        var divtest = document.createElement("div");
        divtest.setAttribute("class", "form-group removeclass" + room);
        var rdiv = 'removeclass' + room;
        divtest.innerHTML = 
        '<div class="row"><div class="col-sm-9 nopadding"><div class="form-group"><div class="input-group"> <input type="text" name="name[]" class="form-control" placeholder="@lang('menu.jobCategories') @lang('app.name')"><div class="input-group-append"> <button class="btn btn-danger" type="button" onclick="remove_education_fields(' + room + ');"> <i class="fa fa-minus"></i> </button></div></div></div></div><div class="clear"></div></div>';

        objTo.appendChild(divtest)
    })

    function remove_education_fields(rid) {
        $('.removeclass' + rid).remove();
    }

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.job-categories.update', $category->id)}}',
            container: '#createForm',
            type: "POST",
            redirect: true,
            data: $('#createForm').serialize()
        })
    });
</script>
@endpush