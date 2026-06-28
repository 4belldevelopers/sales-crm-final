<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"> Dashboard <span class="h-20px border-gray-200 border-start ms-3 mx-2 d-none"></span> <small class="text-muted fs-7 fw-bold my-1 ms-1 d-none">#XRS-45670</small> </h1>
    </div>
    <div class="d-flex align-items-center py-1">
      <div id="myDiv" class="btn btn-sm btn-light fs-6 text-primary fw-bolder">Loading...</div>
    </div>
  </div>
</div>
<script>
  function showDateTime()
  {
		var myDiv 			= document.getElementById("myDiv");
	
		var date 			= new Date();
		var dayList 		= ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
		var monthNames 		= ["Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sep", "Oct", "Nov", "Dec"];
		var dayName 		= dayList[date.getDay()];
		var monthName 		= monthNames[date.getMonth()];
		var today		 	= `${dayName}, ${monthName} ${date.getDate()}, ${date.getFullYear()}`;
	
		var hour 			= date.getHours();
		var min 			= date.getMinutes();
		var sec 			= date.getSeconds();
		
		var time 			= hour + ":" + min + ":" + sec;
		myDiv.innerHTML 	= `<span class="fw-bold text-dark text-hover-dark">Today is :</span>  ${today}`;
  }
		setInterval(showDateTime, 1000);
</script>
