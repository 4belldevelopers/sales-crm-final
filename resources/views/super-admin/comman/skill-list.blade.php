@extends('layouts.superadmin_layout')

<style>
    body
    {
        overflow-x: hidden;
    }
</style>

@section('create-button')
<a href="{{ route('superadmin.currency-settings.create') }}" class="btn btn-dark btn-sm m-l-15">
    <i class="fa fa-plus-circle"></i> @lang('app.createNew')
</a>
@endsection
@section('content')

<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
	<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
		<div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
			<h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Skills</h1>
        </div>
         <a href="{{ route('superadmin.company.skillUpdate', [0,'new']) }}" type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3" id="AddCandidate"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                    <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                    <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                  </g>
                </svg>
                </span>Add Skills</a>
	</div>
</div>
<!--end::toolbar-->



<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="myTable">
                    <thead>
                        <tr>
                            <th class="min-w-200px">Skills</th>
                            <th class="min-w-200px">Update</th>
                            <th class="min-w-100px">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dataskill as $key=> $row)
                           <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-dark fw-bolder fs-6">{{ $row->skill_name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                     <div id="working_status_{{  $row->skill_name }}" data-id="{{ ($row->id) }}" data-name="{{ ($row->skill_name) }}" class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                       <a class="text-primary" href="{{ route('superadmin.company.skillUpdate', [$row->id,$row->skill_name]) }}"><i class="fa fa-edit text-primary fs-4" aria-hidden="true"> Edit/View</i> </a>
                                    </div>
                                </td>

                                 <td>
                                    <div id="working_status_{{  $row->skill_name }}" data-id="{{ ($row->id) }}" data-name="{{ ($row->skill_name) }}" class="form-check form-switch form-switch-sm form-check-custom form-check-solid chkStatus">
                                       <a class="text-danger" href="javascript:;"><i class="fa fa-trash text-danger fs-4" aria-hidden="true"> Delete</i>  </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
                <x-custom-pagination :paginator="$dataskill" /> 
                {{-- {{ $dataskill->links() }} --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
    <script>
       $(document).on("click",".chkStatus",function(){
                 var id = $(this).data('id');
                 var name = $(this).data('name');
                 var url 				=	"{{ route('superadmin.company.skillDelete') }}";
                 var token 				=	"{{ csrf_token() }}";
                 $.ajax({
                    type		: 'POST',
                    url			: url,
                    beforeSend	: function(){ },
                    data		: {'_token': token, 'name':name, 'id': id },
                    success		: function (response){																																						
                                        if (response.status == "success"){  															  
                                            alert("Skill Deleted");
                                            location.reload();
                                        } 
                    }
                });
        })
    </script>
@endpush