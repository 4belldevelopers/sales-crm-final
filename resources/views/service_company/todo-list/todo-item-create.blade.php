<div class="modal-header">
  <h4 class="modal-title">@lang('modules.module.todos.createTodoItem')</h4>
  <button type="button" class="close mr-0" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
  <form id="createTodoItem" class="ajax-form" method="POST" autocomplete="off">
    @csrf
    <div class="form-body">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            <label>@lang('modules.module.todos.form.title')</label>
            <input type="text" class="form-control form-control-lg" id="title" name="title" placeholder="Enter Your Task">
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer justify-content-center mt-3">
      <div class="form-actions">
        <button type="button" id="create-todo-item" class="btn btn-light-primary btn-sm p-1"> <i class="fa fa-paper-plane"></i>&nbsp;@lang('app.apply') </button>
        <button type="button" class="btn btn-light-danger btn-sm p-1" data-dismiss="modal"> <i class="fa fa-circle fa-sm"></i>&nbsp;Close </button>
      </div>
    </div>
  </form>
</div>
