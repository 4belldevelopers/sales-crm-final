@if($status ==	true)
<div class="alert alert-success" role="alert" style="height:100%">
  <h4 class="alert-heading">Email Sent Successfully!.</h4>
  <p>Aww yeah, you successfully read this important alert message. This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.</p>
  <hr>
  <p class="mb-0">Whenever you need to, be sure to use margin utilities to keep things nice and tidy.</p>
</div>
@else
<div class="alert alert-warning alert-dismissible fade show" role="alert">
  <strong>Something Wrong!</strong> Please try again.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
