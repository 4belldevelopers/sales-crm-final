<div class="modal-header">
  <h4 class="modal-title">@lang('modules.roles.addRole')</h4>
  <button type="button" class="close mr-0" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
  <form id="createProjectCategory" class="ajax-form" method="POST">
    @csrf
    @method('PUT')
    <div class="form-body">
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label>@lang('modules.permission.roleName')</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $role->name }}" placeholder="Edit Role Name">
          </div>
        </div>
      </div>
    </div>
    <div class="form-actions">
    <div class="col-12 text-center pt-2">
      <button type="button" id="save-category" class="btn btn-light-success btn-sm"> <i class="fa fa-save"></i>&nbsp;@lang('app.save') </button>
    </div>
  </form>
</div>
<script>


    $('#save-category').click(function () {
        $.easyAjax({
            url: '{{route("product_company.role-permission.update", $role->id)}}',
            container: '#createProjectCategory',
            type: "POST",
            data: $('#createProjectCategory').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
    });

</script>
