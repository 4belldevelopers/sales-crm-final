<style>
#completed-tasks .list-group-item:last-child
{
    border-bottom: none!important;
}
</style>


<div class="card-header p-2">
    <h3 class="card-title d-flex justify-content-between align-items-center">
      <div class="card-label text-dark">
        <i class="fa fa-list-ul"></i>&nbsp;@lang('modules.module.todos.todoList')
      </div>
      <div>
          <a href="javascript:showNewTodoForm();" class="btn btn-sm btn-light-warning mr-2">
            <i class="fa fa-plus-square fa-sm"></i>&nbsp;<span>Add Task</span>
          </a>
          <a class="btn btn-sm btn-light-primary pull-right" href="{{ route('admin.todo-items.index') }}">
            @lang('modules.module.todos.viewAll')
            &nbsp;<i class="fa fa-angle-right"></i></a>
          </a>          
      </div>
    </h3>
</div>

<div id="upper-box" class="todo-box">
    <div class="todo-title d-none">
        <h5>@lang('modules.module.todos.pendingTasks')</h5>
        <a href="javascript:showNewTodoForm();" class="btn btn-sm btn-add">
            <i class="fa fa-plus"></i>
        </a>
    </div>
    <ul class="list-group px-0 py-0" id="pending-tasks">
        @forelse ($pendingTodos as $todo)
            <li data-id="{{ $todo->id }}" data-position="{{ $todo->position }}" class="draggable list-group-item mx-0 mt-0 mb-0 bg-transparent padding-7">
                {{--<div class="handle">--}}
                    {{--<i class="fa fa-bars"></i>--}}
                {{--</div>--}}
                <div class="d-flex">
                    <span class="mb-0">
                        <input data-id="{{ $todo->id }}" type="checkbox" name="status" id="status-{{ $todo->id }}">
                        <label for="status-{{ $todo->id }}">{{ $todo->title }}</label>
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <p class="mb-0">
						<?php 
								$date 			= $todo->created_at->format('Y-m-d');
								$date 			= date("d-M-Y H:m:s", strtotime($date)); 
								echo @$date;
						?>
					</p>
                    <div class="text-right">
                        <a href="javascript:showUpdateTodoForm('{{ $todo->id }}');" class="btn btn-edit">
                            <i class="fa fa-pencil text-primary"></i></a>
                        <a href="javascript:deleteTodoItem('{{ $todo->id }}');" class="btn btn-delete">
                            <i class="fa fa-trash-o text-danger"></i></a>
                    </div>
                </div>
            </li>
        @empty
            <li class="list-group-item mx-0 mb-0 pt-1 bg-light-warning text-warning" id="no-pending-task">
                <h6 class="card-title">
                    <i class="fa fa-check-square"></i>&nbsp;@lang('modules.module.todos.noPendingTasks')
                </h6>
            </li>
        @endforelse
    </ul>
</div>
<div id="lower-box" class="todo-box" style="border-bottom: 1px solid #d4d4d4;">
    <div class="todo-title d-none">
        <h5>@lang('modules.module.todos.completedTasks')</h5>
    </div>
    <ul class="list-group px-0 py-0" id="completed-tasks">
        @forelse ($completedTodos as $todo)
            <!-- mx-0 mt-0 mb-1 bg-transparent border-bottom-dashed -->
            <li data-id="{{ $todo->id }}" data-position="{{ $todo->position }}" class="draggable list-group-item mx-0 mt-0 mb-1 bg-transparent padding-7">
                {{--<div class="handle">--}}
                    {{--<i class="fa fa-bars"></i>--}}
                {{--</div>--}}
                <div class="d-flex">
                    <span class="mb-0">
                        <input data-id="{{ $todo->id }}" checked type="checkbox" name="status" id="status-{{ $todo->id }}">
                        <label for="status-{{ $todo->id }}">{{ $todo->title }}</label>
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <p class="mb-0">
							<?php 
										$date 			= $todo->created_at->format('Y-m-d');
										$date 			= date("d-M-Y H:m:s", strtotime($date)); 
										echo @$date;
							?>
					</p>
                    <div class="text-right">
                        <a href="javascript:showUpdateTodoForm('{{ $todo->id }}');" class="btn btn-edit">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a href="javascript:deleteTodoItem('{{ $todo->id }}');" class="btn btn-delete">
                            <i class="fa fa-trash-o text-danger"></i>
                        </a>
                    </div>
                </div>
            </li>
        @empty
            <li class="list-group-item mx-0 mt-0 mb-1 px-0 py-0 bg-light-danger text-danger" id="no-completed-task">
                <h6 class="card-title">
                    <i class="fa fa-times-circle"></i>&nbsp;No tasks are marked as completed?
                    <!-- @lang('modules.module.todos.noCompletedTasks') -->
                </h6>
            </li>
        @endforelse
    </ul>
</div>

