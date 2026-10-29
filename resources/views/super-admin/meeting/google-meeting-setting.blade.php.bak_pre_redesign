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
			<h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Google Meeting Links</h1>

                 <a href="javascript:void(0);"> 
                    <form class="search" style="margin: 6px;">
                            <input type="search" class="search__input" name="meeting_link" value="{{ @$meeting_link }}" placeholder="Search"  style="width: 550px !important;">
                            <button class="link_search__btn btn btn-sm btn-primary btn-active-light-primary" style="position: absolute;border-radius: 0rem 0.475rem 0.475rem 0rem;">Search</button>
                            <i class="ion-ios-search link_search__icon"></i>
                    </form>
                </a>

        </div>
        <div class="d-flex align-items-center py-1">
                <a href="{{ route('superadmin.company.addMeetingList',['google_meeting_lists']) }}" class="btn btn-sm btn-light btn-active-primary">
                    <span class="svg-icon svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                            <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                        </svg>
                    </span>    
                    Add Meeting Link
                </a>
		         <a href="javascript:void(0);" type="button" data-type="disapprove" class="btn btn-sm btn-flex btn-light-danger reject_mail disapprove fw-bolder me-3 d-none" data-toggle="modal" data-target="#disapprove"> 
                   
                     Delete 
                </a>
		</div>
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
                            <th class="w-25px d-flex">#
                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input datatable_chk chk_list" type="checkbox" data-kt-check="true" data-kt-check-target="#myTable .form-check-input" value="1">
                                  </div>
                            </th>
                            <th class="min-w-200px">Account Name</th>
                            <th class="min-w-200px">Meeting Links</th>
                            <th class="min-w-100px">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($google_meeting_setting as $key=>$item)
                           <tr>
                                <td class="d-flex">
                                    {{$key+1}}
                                    
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input chk_list_item " name="chk_list_item[]" type="checkbox" data-jobid="{{ @$item->id }}" value="{{ @$item->id }}">
                                      </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-dark fw-bolder fs-6">{{ $item->account_name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-dark fw-bolder fs-6">{{ $item->meeting_link }}</span>
                                        </div>
                                    </div>
                                </td>

                                 <td>
                                    <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input id="working_status_{{  $item->id }}" class="form-check-input chkStatus" data-status="{{ ($item->status) ? 0 : 1 }}" type="checkbox" value="{{  $item->id }}" name="working_status" {{ ( $item->status) ? 'checked' : '' }} onclick="WorkingStatus(this, $item->id);"></div>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
                 <x-custom-pagination :paginator="$google_meeting_setting" />    
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
    <script>

            $(document).on('click','.chk_list',function(){
                setTimeout(() => {
				if($(".chk_list").prop('checked') == true){
					$('.chk_list_item').attr('checked','checked');
                    $('.approve').removeClass("d-none");
                    $('.disapprove').removeClass("d-none");
				}else{
					$('.chk_list_item').removeAttr('checked');	
                    $('.approve').addClass("d-none");
                    $('.disapprove').addClass("d-none");	
				}
                }, 500);   
		    });


            $(document).on('click','.chk_list_item',function(){
                var i		=	0;	
                var arr 		=	[];
                $('.chk_list_item:checked').each(function () {
                    arr.push($(this).val());
                });	
                    setTimeout(() => {
                    if(arr.length > 0){
                            $('.disapprove').removeClass("d-none");	
                        }else{
                            $('.disapprove').addClass("d-none");
                        }
                    }, 500);   
            });

            $(document).on("click", ".disapprove", function () {

                var table = 'google_meeting_lists';
                var arr = [];

                $('.chk_list_item:checked').each(function () {
                    arr.push($(this).val());
                });

                // ✅ Check if nothing selected
                if (arr.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Selection',
                        text: 'Please select at least one record.'
                    });
                    return;
                }

                // ✅ Confirmation Popup
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {

                    if (result.isConfirmed) {

                        var url = "{{ route('superadmin.company.meetingListDelete') }}";
                        var token = "{{ csrf_token() }}";

                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: { '_token': token, 'arr': arr, 'table': table },

                            success: function (response) {

                                if (response.status) {

                                    Swal.fire({
                                        icon: response.type,
                                        title: 'Added!',
                                        text: response.message
                                    }).then(() => {
                                        location.reload();
                                    });

                                } else {

                                    Swal.fire({
                                        icon: response.type,
                                        title: 'Error',
                                        text: response.message
                                    });

                                }
                            },

                            error: function (xhr) {
                                let msg = "Something went wrong!";

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Server Error',
                                    text: msg
                                });
                            }
                        });
                    }
                });
            });

      $(document).on("click",".chkStatus",function(){
                 var id = $(this).val();
                 var chkStatus = $(this).attr('data-status');
                 var url 				=	"{{ route('superadmin.company.googleMeetStatusUpdate') }}";
                 var token 				=	"{{ csrf_token() }}";
                 $.ajax({
                    type		: 'POST',
                    url			: url,
                    beforeSend	: function(){ },
                    data		: {'_token': token, 'chkStatus':chkStatus, 'id': id },
                    success		: function (response){																																						
                                        if (response.status == "success"){  															  
                                            alert("Status Changed");
                                            location.reload();
                                        } 
                    }
                });
           })
    </script>
@endpush