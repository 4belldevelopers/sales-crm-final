{{-- Super Admin Dashboard Compact Toolbar Header --}}
<div class="toolbar py-2" id="kt_toolbar">
	<!--begin::Container-->
	<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
		<!--begin::Page title-->
		<div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 my-1">
			<!--begin::Title-->
			<h1 class="d-flex align-items-center text-gray-900 fw-bold fs-3 my-0">Dashboard</h1>
			<!--end::Title-->
		</div>
		<!--end::Page title-->
		<!--begin::Actions-->
		<div class="d-flex align-items-center my-1">
			<!--begin::Today Day-->
			<div id="myDiv" class="btn btn-sm btn-light-primary fs-7 fw-bold d-flex align-items-center py-2 px-4 rounded-2 border border-primary-subtle">
				<i class="ki-outline ki-calendar fs-5 text-primary me-2"></i>
				<span class="text-gray-700 me-1">Today is :</span>
				<span class="text-primary fw-bolder" id="todayDateText">{{ \Carbon\Carbon::now()->format('D, M d, Y') }}</span>
			</div>
			<!--end::Today Day-->
		</div>
		<!--end::Actions-->
	</div>
	<!--end::Container-->
</div>

<!--Js for Today day (run once on page load without continuous timer drain)-->
<script>
	(function() {
		try {
			var date = new Date();
			var dayList = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
			var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sep", "Oct", "Nov", "Dec"];
			var dayName = dayList[date.getDay()];
			var monthName = monthNames[date.getMonth()];
			var formatted = `${dayName}, ${monthName} ${date.getDate()}, ${date.getFullYear()}`;
			var el = document.getElementById("todayDateText");
			if (el) {
				el.textContent = formatted;
			}
		} catch (e) {}
	})();
</script>
