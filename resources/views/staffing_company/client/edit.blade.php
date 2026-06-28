@extends('layouts.staffing_company_layout')
<?php 
		//echo '<pre>'; print_r($client); exit; 
?>
@section('content')
<style>

.form-group label, label {
    margin-top: .5em!important;
    margin-bottom: 0.5em;
}
 .content-header
 {
 display: none;
 }

.message{ color:#006600; margin:10px; text-align:center; font-weight:bold}
.error{ color:#FF0000}
.input-group-append {z-index: 0;height: fit-content;}
.select2-container{width:282px!important;margin-right:0em!important;}
/*.select2-selection select2-selection--single*/
.select2-selection{border-radius:0px!important}
.form-group label,label {font-weight: 600;}

/*Upload Filles*/
.fileinput-button {
  position: relative;
  overflow: hidden;
}

.fileinput-button input {
  position: absolute;
  top: 0;
  right: 0;
  margin: 0;
  opacity: 0;
  -ms-filter: "alpha(opacity=0)";
  font-size: 200px;
  direction: ltr;
  cursor: pointer;
}

ul.thumb-Images li {
  display: inline-block;
  vertical-align: top;
}

.img-wrap {
  position: relative;
  display: inline-block;
  font-size: 0;
}

.img-wrap .close {
position: absolute;
    top: 0px;
    left: 2px;
    z-index: 100;
    background: black;
    color: #fff;
    opacity: 1;
    padding: 0px 2px;
    font-size: 15px;
}

.img-wrap:hover .close {
  opacity: 1;
  background-color: #ff0000;
}

.FileNameCaptionStyle {
  font-size: 12px;
}
/*Upload Files*/

</style>
<div class="row">
<div class="col-12">
<div class="card">
<div class="card-body">
  <div class="row custom-m-top">
    <div class="col-md-6">
      <h4 class="card-title pt-3 text-left"><i class="fa fa-edit"></i>&nbsp;@lang('app.edit') Client</h4>
    </div>
    <!--<div class="col-md-6">
            <ol class="breadcrumb float-sm-right mt-xs-2 small bg-white border-bottom border-top border-secondary">
              <li class="breadcrumb-item"><a href="{{ $user->
    is_superadmin?route('superadmin.dashboard.index'): route('staffing_company.dashboard')}}"><i class="fa fa-home"></i></a>
    </li>
    <li class="breadcrumb-item active">{{ $pageTitle }}</li>
    </ol>
  </div>
  --> </div>
<!-- <h4 class="card-title">@lang('app.edit')</h4> -->
<form class="ajax-form" method="POST" id="createForm" enctype="multipart/form-data">
  @csrf
  <input name="_method" type="hidden" value="PUT">
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="name">Client</label>
        <input type="text" class="form-control" name="name" id="name" value="{{ $client->name }}" placeholder="Client name">
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label for="website">Website</label>
        <input type="text" class="form-control" name="website" id="website" value="{{ $client->website }}" placeholder="Website Url">
      </div>
    </div>
    <div class="col-md-12">
      <div class="form-group">
        <label for="detail">Detail</label>
        <textarea class="form-control" id="detail" name="detail" rows="4" placeholder="Enter text ...">{{ $client->detail }}</textarea>
      </div>
    </div>
    <div class="col-md-12">
      <div class="form-group">
        <label for="feedback">Any Other Detail</label>
        <textarea class="form-control" id="feedback" name="feedback" rows="4" placeholder="Enter text ...">{{ $client->feedback }}</textarea>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label for="commission">Commission(%)</label>
        <input type="number" class="form-control" name="commission" id="commission" value="{{ $client->commission }}" placeholder="Commission(%)" onkeypress="return isNumberKey(event)">
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label for="payout">Payout(Days)</label>
        <input type="number" class="form-control" name="payout" id="payout" placeholder="payout Days" value="{{ $client->payout }}"  maxlength="3" onkeypress="return isNumberKey(event)">
      </div>
    </div>
  </div>
  <!-- Old Attach Files -->
  <!-- <div class="col-md-12">
            <div class="form-group">
              <label for="file">Agreement / NDA</label>
              <input type="file" class="form-control" id="input-file-now" name="image[]" accept=".png,.jpg,.jpeg" class="dropify1"  data-default-file="{{ asset('avatar.png')  }}" multiple  />
              <br />
              <?php 
			
          			if(count($client_file) > 0 ){
          			
          	   ?>
                        <div>
                          <div style="clear:both; margin:10px;" >
                            <div style="float:left; width:300px; font-weight:bold">filename</div>
                            <div style="float:left; width:200px; font-weight:bold ">Action</div>
                          </div>
                          <?php 
          									$cnt = 1; 
          									foreach($client_file as $clientArr)
          									{
          							?>
                <div id="<?php echo @$clientArr->
  id;  ?>" style="clear:both; margin:10px;">
  <div style="float:left; width:300px;"><?php echo @$clientArr->file_name;  ?></div>
  <div style="float:left; width:150px; "> <a href="{{url('staffing_company/clients/download', $clientArr->id )}}" class="btn btn-primary btn-circle" data-toggle="tooltip" data-original-title="Download"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp; | &nbsp; <a href="javascript:void();" class="btn btn-danger px-2 py-2 btn-circle sa-params" data-toggle="tooltip" data-id="<?php echo @$clientArr->id;  ?>" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a> </div>
  </div>
  <?php  $cnt = $cnt+1; } ?>
  </div>
  <?php  } ?>
  </div>
  </div>
  -->
  <!-- Old Attach Files -->
  <div class="col-md-12 pt-2">
    <div class="form-group">
      <label for="file">Agreement / NDA - New</label>
      <span class="btn btn-light-primary btn-sm fileinput-button ml-2"> <span style="font-size: small;font-weight: 700;font-family: inherit;">Attach Files&nbsp;<i class="fa fa-link"></i></span>
      <input type="file" name="image[]" id="files" >
      <br />
      </span>
      <output id="Filelist"></output>
      <br />
      <?php 
      
      if(count($client_file) > 0 ){
      
     ?>
      <div>
        <div style="clear:both; margin:10px;" >
          <div style="float:left; width:300px; font-weight:bold">filename</div>
          <div style="float:left; width:200px; font-weight:bold ">Action</div>
        </div>
        <?php 
                  $cnt = 1; 
                  foreach($client_file as $clientArr)
                  {
              ?>
        <div id="<?php echo @$clientArr->id;  ?>" style="clear:both; margin:10px;">
          <div style="float:left; width:300px;"><?php echo @$clientArr->file_name;  ?></div>
          <div style="float:left; width:150px; "> <a href="{{url('staffing_company/clients/download', $clientArr->id )}}" class="btn btn-primary btn-circle" data-toggle="tooltip" data-original-title="Download"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp; | &nbsp; <a href="javascript:void();" class="btn btn-danger px-2 py-2 btn-circle sa-params" data-toggle="tooltip" data-id="<?php echo @$clientArr->id;  ?>" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a> </div>
        </div>
        <?php  $cnt = $cnt+1; } ?>
      </div>
      <?php  } ?>
    </div>
  </div>
  <label for="commission">Contact Person Info </label>
  <?php 
						
						if(count($client_contact_info) > 0 )
						{
							$cnt = 1; 
							foreach($client_contact_info as $contArr)
							{
								echo '<div class="row">
			<div class="col-sm-12 nopadding">
				<div class="form-group">
					<div class="input-group">
            <div style="width: 25%;">
  						<span style="font-weight:600">Name</span> :&nbsp;&nbsp;
              <span style="font-weight:400">'.@$contArr->contact_person_name.'</span>&nbsp;&nbsp;
            </div>
            <!-- &nbsp;&nbsp;|&nbsp;&nbsp; -->
            <div style="width: 25%;">
  						<span style="font-weight:600">Email</span> :&nbsp;&nbsp;
              <span style="font-weight:400">'.@$contArr->contact_person_email.'</span>&nbsp;&nbsp;
            </div>  
            <!-- &nbsp;&nbsp;|&nbsp;&nbsp; -->
            <div style="width: 25%;">
  						<span style="font-weight:600">Contact Number</span> :&nbsp;&nbsp;
              <span style="font-weight:400">'.@$contArr->contact_person_number.'</span>&nbsp;&nbsp;
            </div>
            <!-- &nbsp;&nbsp;|&nbsp;&nbsp; -->
            <div>
  						<span style="font-weight:600">Designation</span> :&nbsp;&nbsp;
              <span style="font-weight:400">'.@$contArr->contact_person_designation.'</span>
            </div>						
					</div>
				</div>
			</div>
		</div>';
							}
						}
		?>
  <div class="row" >
    <div class="col-sm-12 nopadding">
      <div class="form-group">
        <div class="input-group">
          <input type="text" name="cp_name[]" class="form-control" placeholder="Contact Name">
          &nbsp;
          <input type="text" name="cp_email[]" class="form-control" placeholder="Contact Email">
          &nbsp;
          <input type="text" name="cp_number[]" class="form-control" placeholder="Contact Number">
          &nbsp;
          <input type="text" name="cp_designation[]" class="form-control" placeholder="Designation">
          <div class="input-group-append">
            <button class="btn btn-light-success btn-sm px-2 py-2" type="button" id="add-more"><i class="fa fa-plus"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="education_fields"></div>
  <label for="commission">Office Address </label>
  <?php 
						
						if(count($client_location) > 0 )
						{
							$cnt = 1; 
							foreach($client_location as $locArr)
							{
								echo '<div class="row">
			<div class="col-sm-12 nopadding">
				<div class="form-group">
					<div class="input-group">
            <div style="width: 25%;">
						<span style="font-weight:bold">Country</span> :&nbsp;&nbsp;
            '.@$locArr->country_name.'&nbsp;&nbsp;&nbsp;
          </div>
          <div style="width: 25%;">
						<span style="font-weight:bold">City</span>:&nbsp;&nbsp; 
            '.@$locArr->city.'&nbsp;&nbsp;&nbsp;
          </div>
          <div style="width: 25%;">
						<span style="font-weight:bold">Office Address</span> :&nbsp;&nbsp;
            '.@$locArr->address.'&nbsp;&nbsp;&nbsp;
          </div>
          <div style="width: 25%;">
						<span style="font-weight:bold">Postcode</span> :&nbsp;&nbsp; 
            '.@$locArr->postcode.'
					</div>	
					</div>
				</div>
			</div>
		</div>';
							}
						}
		?>
  <div class="row">
    <div class="col-sm-12 nopadding">
      <div class="form-group">
        <div class="input-group">
          <select name="country_id[]" id="country_id[]" class="form-control select2 custom-select">
            <?php 
									$option_list ='';
									foreach($countries as $country)
									{	
										echo $option 		=  '<option value="'.$country->id.'">'.ucfirst($country->country_name).'</option>';
										$option_list 	.= $option;
							 		} 
							 ?>
          </select>
          <input type="text" name="city[]" class="form-control" placeholder="City" style="margin-left: .4em;">
          &nbsp;
          <input type="text" name="address[]" class="form-control" placeholder="Office address">
          &nbsp;
          <input type="text" name="postcode[]" class="form-control" placeholder="Postcode">
          <div class="input-group-append">
            <button class="btn btn-light-success btn-sm px-2 py-2" type="button" id="add-more-location"><i
										class="fa fa-plus"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="location_fields"></div>
  <div class="row mt-3">
    <div class="col-12 text-center">
      <button type="button" id="save-form" class="btn btn-light-primary btn-sm"><i class="fa fa-spinner"></i> Update</button>
    </div>
  </div>
</form>
<textarea name="hid_option" id="hid_option" cols="5" rows="5" style="display:none"><?php echo  @$option_list; ?></textarea>
</div>
</div>
</div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<!-- Attach Files -->
<script>
  //I added event handler for the file upload control to access the files properties.
document.addEventListener("DOMContentLoaded", init, false);

//To save an array of attachments
var AttachmentArray = [];

//counter for attachment array
var arrCounter = 0;

//to make sure the error message for number of files will be shown only one time.
var filesCounterAlertStatus = false;

//un ordered list to keep attachments thumbnails
var ul = document.createElement("ul");
ul.className = "thumb-Images";
ul.id = "imgList";

function init() {
  //add javascript handlers for the file upload event
  document
    .querySelector("#files")
    .addEventListener("change", handleFileSelect, false);
}

//the handler for file upload event
function handleFileSelect(e) {
  //to make sure the user select file/files
  if (!e.target.files) return;

  //To obtaine a File reference
  var files = e.target.files;

  // Loop through the FileList and then to render image files as thumbnails.
  for (var i = 0, f; (f = files[i]); i++) {
    //instantiate a FileReader object to read its contents into memory
    var fileReader = new FileReader();

    // Closure to capture the file information and apply validation.
    fileReader.onload = (function(readerEvt) {
      return function(e) {
        //Apply the validation rules for attachments upload
        ApplyFileValidationRules(readerEvt);

        //Render attachments thumbnails.
        RenderThumbnail(e, readerEvt);

        //Fill the array of attachment
        FillAttachmentArray(e, readerEvt);
      };
    })(f);

    // Read in the image file as a data URL.
    // readAsDataURL: The result property will contain the file/blob's data encoded as a data URL.
    // More info about Data URI scheme https://en.wikipedia.org/wiki/Data_URI_scheme
    fileReader.readAsDataURL(f);
  }
  document
    .getElementById("files")
    .addEventListener("change", handleFileSelect, false);
}

//To remove attachment once user click on x button
jQuery(function($) {
  $("div").on("click", ".img-wrap .close", function() {
    var id = $(this)
      .closest(".img-wrap")
      .find("img")
      .data("id");

    //to remove the deleted item from array
    var elementPos = AttachmentArray.map(function(x) {
      return x.FileName;
    }).indexOf(id);
    if (elementPos !== -1) {
      AttachmentArray.splice(elementPos, 1);
    }

    //to remove image tag
    $(this)
      .parent()
      .find("img")
      .not()
      .remove();

    //to remove div tag that contain the image
    $(this)
      .parent()
      .find("div")
      .not()
      .remove();

    //to remove div tag that contain caption name
    $(this)
      .parent()
      .parent()
      .find("div")
      .not()
      .remove();

    //to remove li tag
    var lis = document.querySelectorAll("#imgList li");
    for (var i = 0; (li = lis[i]); i++) {
      if (li.innerHTML == "") {
        li.parentNode.removeChild(li);
      }
    }
  });
});

//Apply the validation rules for attachments upload
function ApplyFileValidationRules(readerEvt) {
  //To check file type according to upload conditions
  if (CheckFileType(readerEvt.type) == false) {
    alert(
      "The file (" +
        readerEvt.name +
        ") does not match the upload conditions, You can only upload in PDF or Word format!"
    );
    e.preventDefault();
    return;
  }

  //To check file Size according to upload conditions
  if (CheckFileSize(readerEvt.size) == false) {
    alert(
      "The file (" +
        readerEvt.name +
        ") does not match the upload conditions, The maximum file size for uploads should not exceed 300 KB"
    );
    e.preventDefault();
    return;
  }

  //To check files count according to upload conditions
  if (CheckFilesCount(AttachmentArray) == false) {
    if (!filesCounterAlertStatus) {
      filesCounterAlertStatus = true;
      alert(
        "You have added more than 10 files. According to upload conditions you can upload 10 files maximum"
      );
    }
    e.preventDefault();
    return;
  }
}

//To check file type according to upload conditions
function CheckFileType(fileType) 
{
  if (fileType == "application/msword") 
  {
    return true;
  } 
  else if (fileType == "application/vnd.ms-excel") 
  {
    return true;
  } 
  else if (fileType == "application/vnd.ms-powerpoint") 
  {
    return true;
  } 
  else if (fileType == "text/plain") 
  {
    return true;
  } 
  else if (fileType == "application/pdf") 
  {
    return true;
  } 
  else if (fileType == "image/*") 
  {
    return true;
  }
  else if (fileType == "application/vnd.openxmlformats-officedocument.wordprocessingml.document")  
  {
    return true;
  }  
  else 
  {
    return false;
  }
  return true;
}

//To check file Size according to upload conditions
function CheckFileSize(fileSize) {
  if (fileSize < 90000000000) {
    return true;
  } else {
    return false;
  }
  return true;
}

//To check files count according to upload conditions
function CheckFilesCount(AttachmentArray) {
  //Since AttachmentArray.length return the next available index in the array,
  //I have used the loop to get the real length
  var len = 0;
  for (var i = 0; i < AttachmentArray.length; i++) {
    if (AttachmentArray[i] !== undefined) {
      len++;
    }
  }
  //To check the length does not exceed 10 files maximum
  if (len > 9) {
    return false;
  } else {
    return true;
  }
}

//Render attachments thumbnails.
function RenderThumbnail(e, readerEvt) {
  var li = document.createElement("li");
  ul.appendChild(li);
  li.innerHTML = [
    '<div class="img-wrap"> <span class="close">&times;</span>' +
      '<img class="thumb" src="',
    e.target.result,
    '" title="',
    escape(readerEvt.name),
    '" data-id="',
    readerEvt.name,
    '"/>' + "</div>"
  ].join("");

  var div = document.createElement("div");
  div.className = "FileNameCaptionStyle";
  li.appendChild(div);
  div.innerHTML = [readerEvt.name].join("");
  document.getElementById("Filelist").insertBefore(ul, null);
}

//Fill the array of attachment
function FillAttachmentArray(e, readerEvt) {
  AttachmentArray[arrCounter] = {
    AttachmentType: 1,
    ObjectType: 1,
    FileName: readerEvt.name,
    FileDescription: "Attachment",
    NoteText: "",
    MimeType: readerEvt.type,
    Content: e.target.result.split("base64,")[1],
    FileSizeInBytes: readerEvt.size
  };
  arrCounter = arrCounter + 1;
}
</script>
<!-- Attach Files -->
<script>
        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });
 </script>
<script>
		$( document ).ready(function() {
		  
		  
		    $('body').on('click', '.sa-params', function(){
            var id  = $(this).attr("data-id");
            swal({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('staffing_company.clients.deleterecord',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'GET',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE', 'id': id},
                        success: function (response) {
								$( "#"+id ).remove();
                            if (response.status == "success") {
                                $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });
		
			
		  
				 
		});




       // For select 2
        $(".select2").select2();
        var room = 1;
	
		$('#add-more-location').click(function () {

			var option 			=	$('#hid_option').val();
			//alert(option); 
            room++;
            var objTo 			= document.getElementById('location_fields')
            var divtest 		= document.createElement("div");
            divtest.setAttribute("class", "form-group removeclass" + room);
            var rdiv 			= 'removeclass' + room;
            divtest.innerHTML 	= '<div class="row"><div class="col-sm-12 nopadding"><div class="form-group"><div class="input-group"><select name="country_id[]" id="country_id" class="form-control select2 custom-select">'+option+'</select><input type="text" name="city[]" class="form-control" placeholder="City">&nbsp;<input type="text" name="address[]" class="form-control" placeholder="Office address">&nbsp;<input type="text" name="postcode[]" class="form-control" placeholder="postcode"><div class="input-group-append"><button class="btn btn-danger px-2 py-2" type="button" onClick="remove_location_fields(' + room + ');"> <i class="fa fa-minus"></i> </button></div></div></div></div><div class="clear"></div></row>';

            objTo.appendChild(divtest)
        })
		
		 function remove_location_fields(rid) {
            $('.removeclass' + rid).remove();
        }
		
		
        $('#add-more').click(function () {

            room++;
            var objTo 			= document.getElementById('education_fields')
            var divtest 		= document.createElement("div");
            divtest.setAttribute("class", "form-group removeclass" + room);
            var rdiv 			= 'removeclass' + room;
            divtest.innerHTML 	= '<div class="row"><div class="col-sm-12 nopadding"><div class="form-group"><div class="input-group"><input type="text" name="cp_name[]" class="form-control" placeholder="Contact Name">&nbsp;<input type="text" name="cp_email[]" class="form-control" placeholder="Contact Email">&nbsp;<input type="text" name="cp_number[]" class="form-control" placeholder="Contact Number">&nbsp;<input type="text" name="cp_designation[]" class="form-control" placeholder="Designation"><div class="input-group-append"><button class="btn btn-danger px-2 py-2" type="button" onClick="remove_education_fields(' + room + ');"> <i class="fa fa-minus"></i> </button></div></div></div></div><div class="clear"></div></row>';

            objTo.appendChild(divtest)
        })

        function remove_education_fields(rid) {
            $('.removeclass' + rid).remove();
        }
		
		 
		
        $('#save-form').click(function () {
            $.easyAjax({
                url			: '{{route('staffing_company.clients.update', $client->id)}}',
                container	: '#createForm',
                type		: "POST",
                redirect	: true,
				file		: true,
                data		: $('#createForm').serialize()
            })
        });
    </script>
@endpush