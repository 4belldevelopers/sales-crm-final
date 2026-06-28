<div class="modal-header">
  <h4 class="modal-title">@lang('modules.module.todos.editTodoItem')</h4>
  <button type="button" class="close mr-0" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
  <form id="editTodoItem" class="ajax-form" method="POST" autocomplete="off">
    @csrf
    @method('PUT')
    <div class="form-body">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            <label>@lang('modules.module.todos.form.title')</label>
            <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ $todoItem->title }}">
          </div>
          <div class="form-group">
            <label for="status">@lang('modules.module.todos.form.status')</label>
            <select id="status" class="form-control" name="status">
              <option 
                                @if ($todoItem->status == 'pending')
              selected @endif value="pending">
              @lang('modules.module.todos.pending') </option>
              <option
                                @if ($todoItem->status == 'completed')
              selected @endif value="completed">
              @lang('modules.module.todos.completed') </option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer justify-content-center mt-3">
      <div class="form-actions">
        <button type="button" id="update-todo-item" data-id="{{ $todoItem->id }}" class="btn btn-light-primary btn-sm p-1"> <i class="fa fa-paper-plane"></i>&nbsp;@lang('app.update') </button>
        <button type="button" class="btn btn-light-danger btn-sm p-1" data-dismiss="modal"> <i class="fa fa-circle fa-sm"></i>&nbsp;Close </button>
      </div>
    </div>
  </form>
</div>
