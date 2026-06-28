@extends('layouts.staffing_company_layout')
@push('head-script')
<style>
body
{
   overflow-x:hidden;
}
</style>
@endpush
@section('content')
<?php 
		use App\Helper\GeneralMethod;
?>
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-5 pb-0" id="kt_content">
  <!--begin::Toolbar-->
  @include('sections.demo-toolbar')
  <!--end::Toolbar-->
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">
      <!-- Chart Row -->
      <div class="row">
        <div class="col-lg-12">
          <!--begin::Card-->
          <div class="card card-custom gutter-b">
            <div class="card-header">
              <div class="card-title">
                <h3 class="card-label">Chart - Metronic-7</h3>
              </div>
            </div>
            <div class="card-body">
              <!--begin::Chart-->
              <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
              <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
              <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
              <script src="{{ asset('demo/js/apexcharts.js') }}<?php echo TM_VERSION; ?>"></script>
              <script src="{{ asset('demo/js/apexcharts.common.js') }}<?php echo TM_VERSION; ?>"></script>
              <script src="{{ asset('demo/js/apexcharts.amd.js') }}<?php echo TM_VERSION; ?>"></script>
              <link href="{{ asset('demo/js/apexcharts.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
              <script>
                        am4core.ready(function () {
                        
                            // Themes begin
                            am4core.useTheme(am4themes_animated);
                            // Themes end
                        
                            var chart = am4core.create('kt_amcharts_1', am4charts.XYChart)
                            chart.colors.step = 2;
                        
                            chart.legend = new am4charts.Legend()
                            chart.legend.position = 'top'
                            chart.legend.paddingBottom = 20
                            chart.legend.labels.template.maxWidth = 95
                        
                            var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
                            xAxis.dataFields.category = 'category'
                            xAxis.renderer.cellStartLocation = 0.1
                            xAxis.renderer.cellEndLocation = 0.9
                            xAxis.renderer.grid.template.location = 0;
                        
                            var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
                            yAxis.min = 0;
                        
                            function createSeries(value, name) {
                                var series = chart.series.push(new am4charts.ColumnSeries())
                                series.dataFields.valueY = value
                                series.dataFields.categoryX = 'category'
                                series.name = name
                        
                                series.events.on('hidden', arrangeColumns);
                                series.events.on('shown', arrangeColumns);
                        
                                var bullet = series.bullets.push(new am4charts.LabelBullet())
                                bullet.interactionsEnabled = false
                                bullet.dy = 30;
                                bullet.label.text = '{valueY}'
                                bullet.label.fill = am4core.color('#ffffff')
                        
                                return series;
                            }
                        
                            chart.data = [
                                {
                                    category: 'Place #1',
                                    first: 40,
                                    second: 55,
                                    third: 60
                                },
                                {
                                    category: 'Place #2',
                                    first: 30,
                                    second: 78,
                                    third: 69
                                },
                                {
                                    category: 'Place #3',
                                    first: 27,
                                    second: 40,
                                    third: 45
                                },
                                {
                                    category: 'Place #4',
                                    first: 50,
                                    second: 33,
                                    third: 22
                                }
                            ]
                        
                        
                            createSeries('first', 'The First');
                            createSeries('second', 'The Second');
                            createSeries('third', 'The Third');
                        
                            function arrangeColumns() {
                        
                                var series = chart.series.getIndex(0);
                        
                                var w = 1 - xAxis.renderer.cellStartLocation - (1 - xAxis.renderer.cellEndLocation);
                                if (series.dataItems.length > 1) {
                                    var x0 = xAxis.getX(series.dataItems.getIndex(0), 'categoryX');
                                    var x1 = xAxis.getX(series.dataItems.getIndex(1), 'categoryX');
                                    var delta = ((x1 - x0) / chart.series.length) * w;
                                    if (am4core.isNumber(delta)) {
                                        var middle = chart.series.length / 2;
                        
                                        var newIndex = 0;
                                        chart.series.each(function (series) {
                                            if (!series.isHidden && !series.isHiding) {
                                                series.dummyData = newIndex;
                                                newIndex++;
                                            }
                                            else {
                                                series.dummyData = chart.series.indexOf(series);
                                            }
                                        })
                                        var visibleCount = newIndex;
                                        var newMiddle = visibleCount / 2;
                        
                                        chart.series.each(function (series) {
                                            var trueIndex = chart.series.indexOf(series);
                                            var newIndex = series.dummyData;
                        
                                            var dx = (newIndex - trueIndex + middle - newMiddle) * delta
                        
                                            series.animate({ property: 'dx', to: dx }, series.interpolationDuration, series.interpolationEasing);
                                            series.bulletsContainer.animate({ property: 'dx', to: dx }, series.interpolationDuration, series.interpolationEasing);
                                        })
                                    }
                                }
                            }
                        
                        }); // end am4core.ready()
                        
                        $( document ).ready(function() {
                        
                        		var element = document.getElementById('kt_apexcharts_1');
                        
                        var height = parseInt(KTUtil.css(element, 'height'));
                        var labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
                        var borderColor = KTUtil.getCssVariableValue('--bs-gray-200');
                        var baseColor = KTUtil.getCssVariableValue('--bs-warning');
                        var secondaryColor = KTUtil.getCssVariableValue('--bs-gray-300');
                        
                        if (!element) {
                            return;
                        }
                        
                        var options = {
                            series: [{
                                name: 'Net Profit',
                                data: [44, 55, 57, 56, 61, 58]
                            }, {
                                name: 'Revenue',
                                data: [76, 85, 101, 98, 87, 105]
                            }],
                            chart: {
                                fontFamily: 'inherit',
                                type: 'bar',
                                height: height,
                                toolbar: {
                                    show: false
                                }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: true,
                                    columnWidth: ['30%'],
                                    endingShape: 'rounded'
                                },
                            },
                            legend: {
                                show: false
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                show: true,
                                width: 2,
                                colors: ['transparent']
                            },
                            xaxis: {
                                categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                                axisBorder: {
                                    show: false,
                                },
                                axisTicks: {
                                    show: false
                                },
                                labels: {
                                    style: {
                                        colors: labelColor,
                                        fontSize: '12px'
                                    }
                                }
                            },
                            yaxis: {
                                labels: {
                                    style: {
                                        colors: labelColor,
                                        fontSize: '12px'
                                    }
                                }
                            },
                            fill: {
                                opacity: 1
                            },
                            states: {
                                normal: {
                                    filter: {
                                        type: 'none',
                                        value: 0
                                    }
                                },
                                hover: {
                                    filter: {
                                        type: 'none',
                                        value: 0
                                    }
                                },
                                active: {
                                    allowMultipleDataPointsSelection: false,
                                    filter: {
                                        type: 'none',
                                        value: 0
                                    }
                                }
                            },
                            tooltip: {
                                style: {
                                    fontSize: '12px'
                                },
                                y: {
                                    formatter: function (val) {
                                        return '$' + val + ' thousands'
                                    }
                                }
                            },
                            colors: [baseColor, secondaryColor],
                            grid: {
                                borderColor: borderColor,
                                strokeDashArray: 4,
                                yaxis: {
                                    lines: {
                                        show: true
                                    }
                                }
                            }
                        };
                        
                        var chart = new ApexCharts(element, options);
                        chart.render();
                        
                        
                        
                        
                        });
                        
                         var options = {
                                  series: [{
                                  name: 'Net Profit',
                                  data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
                                }, {
                                  name: 'Revenue',
                                  data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
                                }, {
                                  name: 'Free Cash Flow',
                                  data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
                                }],
                                  chart: {
                                  type: 'bar',
                                  height: 350
                                },
                                plotOptions: {
                                  bar: {
                                    horizontal: false,
                                    columnWidth: '55%',
                                    endingShape: 'rounded'
                                  },
                                },
                                dataLabels: {
                                  enabled: false
                                },
                                stroke: {
                                  show: true,
                                  width: 2,
                                  colors: ['transparent']
                                },
                                xaxis: {
                                  categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
                                },
                                yaxis: {
                                  title: {
                                    text: '$ (thousands)'
                                  }
                                },
                                fill: {
                                  opacity: 1
                                },
                                tooltip: {
                                  y: {
                                    formatter: function (val) {
                                      return "$ " + val + " thousands"
                                    }
                                  }
                                }
                                };
                        
                                var chart = new ApexCharts(document.querySelector("#chart"), options);
                                chart.render();
                        				
                     </script>
              <div class="card card-bordered">
                <div class="carsd-body">
                  <!--<div id="chart" style="height: 350px;"></div>
                              <div id="kt_apexcharts_2" style="height: 350px;"></div>-->
                  <div id="kt_amcharts_1" style="height: 350px;">aaaa</div>
                </div>
              </div>
              <!--end::Chart-->
            </div>
          </div>
          <!--end::Card-->
        </div>
      </div>
      <!-- Chart Row End -->
      <!--begin::Interview Tracker & Requisitions-->
      <div class="row g-5 g-xxl-8">
        <!--begin::Interview Tracker-->
        <div class="col-xxl-6 col-lg-6 col-md-6 col-sm-12 col-sm-12 pt-4 pb-0">
          <!--begin::Tables Widget 5-->
          <div class="card card-xxl-stretch">
            <!--begin::Header-->
            <div class="card-header border-0 pt-5">
              <h3 class="card-title align-items-start flex-column"> <span class="card-label fw-bolder fs-3 mb-1">Interview Tracker</span> </h3>
              <div class="card-toolbar"> <a href="<?php echo url('staffing_company/interview-schedule/table-view'); ?>" class="btn btn-primary btn-sm">View All</a> </div>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body py-3">
              <div class="tab-content">
                <!--begin::Tap pane-->
                <div class="tab-pane fade show active" id="kt_table_widget_5_tab_1">
                  <!--begin::Table container-->
                  <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                      <!--begin::Table head-->
                      <thead>
                        <!--begin::Table row-->
                        <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                          <th class="min-w-180px font-13">Name</th>
                          <th class="min-w-100px font-13">Requisitions</th>
                          <th class="min-w-125px font-13">Interview Mode</th>
                          <th class="min-w-40px font-13">Date</th>
                        </tr>
                        <!--end::Table row-->
                      </thead>
                      <!--end::Table head-->
                      <!--begin::Table body-->
                      <tbody class="text-gray-600 fw-bold">
                        <?php 
                                       if(!empty($intSchedules)){
                                           foreach($intSchedules as $isdata){
                                       	  
                                       		$schedule_date		=	'';
                                       		$schedule_date		= 	date("d M Y", strtotime($isdata->schedule_date));
                                       		
                                       		$schedule_time		=	'';
                                       		$schedule_time		= 	date("H:i", strtotime($isdata->schedule_date));
                                       		
                                       		$bgTextDecore		=	$strVal	=	'';
                                       		$str				=	trim(@$isdata->full_name); 
                                       		$str	= $strVal	=	strtolower($str[0]);
                                       		$str				=	ord($str);
                                       		if($str >=97 && $str <=101){
                                       			$bgTextDecore	=	'fs-3 bg-light-danger text-danger';	
                                       		}else if($str >=102 && $str <=106){
                                       			$bgTextDecore	=	'fs-3 bg-light-warning text-warning';	
                                       		}else if($str >=107 && $str <=111){
                                       			$bgTextDecore	=	'fs-3 bg-light-primary text-primary';	
                                       		}else if($str >=112 && $str <=116){
                                       			$bgTextDecore	=	'fs-3 bg-light-success text-success';	
                                       		}else if($str >=117 && $str <=121){
                                       			$bgTextDecore	=	'fs-3 bg-dark text-secondary';	
                                       		}else {
                                       			$bgTextDecore	=	'fs-3 bg-light-secondary text-dark';	
                                       		}
                                       			
                                       	
                                       ?>
                        <tr>
                          <td class="d-flex align-items-center border-0"><div class="symbol symbol-circle symbol-50px overflow-hidden me-2 rounded-2"> <a href="javascript:void(0);">
                              <div class="symbol-label rounded-2 <?php echo @$bgTextDecore; ?>"><?php echo strtoupper(@$strVal);  ?></div>
                              </a> </div>
                            <!-- <div class="symbol symbol-circle symbol-50px overflow-hidden me-2">
                                             <a href="javascript:void(0);">
                                                 <div class="symbol-label">
                                                     <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-26.jpg" alt="Emma Smith" class="w-100" />
                                                 </div>
                                             </a>
                                             </div>-->
                            <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-bs-toggle="tooltip" title="<?php echo ucwords($isdata->full_name); ?>"> <?php echo substr(ucwords($isdata->full_name), 0, 9); ?> </a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail" data-bs-toggle="tooltip" title="<?php echo ucwords($isdata->current_designation); ?>"> <?php echo substr(ucwords($isdata->current_designation), 0, 9); ?> </a> </div></td>
                          <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-bs-toggle="tooltip" title="<?php echo ucwords($isdata->title); ?>"> <?php echo ucwords($isdata->title); ?> </a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary"><?php echo ucwords($isdata->clients_name); ?></a> </div></td>
                          <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"><?php echo ucwords($isdata->mode); ?></a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary"><?php echo ucwords($isdata->interview_mode_name); ?></a> </div></td>
                          <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"><?php echo @$schedule_date; ?></a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary"><?php echo @$schedule_time; ?></a> </div></td>
                        </tr>
                        <?php 
                                       }
                                       }
                                       ?>
                      </tbody>
                      <!--end::Table body-->
                    </table>
                  </div>
                  <!--end::Table-->
                </div>
              </div>
            </div>
            <!--end::Body-->
          </div>
          <!--end::Tables Widget 5-->
        </div>
        <!--end::Interview Tracker-->
        <?php 
               //echo '<pre>'; print_r($jobListing); exit; 
               ?>
        <!--begin::Requisitions-->
        <div class="col-xxl-6 col-lg-6 col-md-6 col-sm-12 col-sm-12 pt-4 pb-0">
          <!--begin::Tables Widget 5-->
          <div class="card card-xxl-stretch">
            <!--begin::Header-->
            <div class="card-header border-0 pt-5">
              <h3 class="card-title align-items-start flex-column"> <span class="card-label fw-bolder fs-3 mb-1">Requisitions</span> </h3>
              <div class="card-toolbar"> <a href="<?php echo url('staffing_company/jobs'); ?>" class="btn btn-primary btn-sm">View All</a> </div>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body py-3">
              <div class="tab-content">
                <!--begin::Tap pane-->
                <div class="tab-pane fade show active" id="kt_table_widget_5_tab_1">
                  <!--begin::Table container-->
                  <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                      <!--begin::Table head-->
                      <thead>
                        <!--begin::Table row-->
                        <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                          <th class="min-w-180px">Requisitions</th>
                          <th class="min-w-100px">Exp | Salary</th>
                          <th class="min-w-125px">Location</th>
                          <th class="min-w-40px">Status</th>
                        </tr>
                        <!--end::Table row-->
                      </thead>
                      <!--end::Table head-->
                      <!--begin::Table body-->
                      <tbody class="text-gray-600 fw-bold">
                        <!--begin::Table row-->
                        <?php 
                                       if(!empty($jobListing)){
                                       	foreach($jobListing as $jdata){
                                       		
                                       		$statusClass	=	'';
                                       		if($jdata->status	==	'active'){
                                       			$statusClass	=	'badge-light-success';
                                       		}else if($jdata->status	==	'inactive'){
                                       			$statusClass	=	'badge-light-danger';
                                       		}else if($jdata->status	==	'hold'){
                                       			$statusClass	=	'badge-light-warning';
                                       		}
                                       		
                                       		$exp 			= '';
                                       		$exp 			= @$jdata->min_exp;			
                                       		if(!empty($jdata->max_exp))
                                       		{
                                       			$exp 	= @$jdata->min_exp.'-'.@$jdata->max_exp;
                                       		}
                                       		
                                       		$bgTextDecore		=	$strVal	=	'';
                                       		$str				=	trim(@$jdata->title);
                                       		$str	= $strVal	=	strtolower($str[0]);
                                       		$str				=	ord($str);
                                       		if($str >=97 && $str <=101){
                                       			$bgTextDecore	=	'fs-3 bg-light-danger text-danger';	
                                       		}else if($str >=102 && $str <=106){
                                       			$bgTextDecore	=	'fs-3 bg-light-warning text-warning';	
                                       		}else if($str >=107 && $str <=111){
                                       			$bgTextDecore	=	'fs-3 bg-light-primary text-primary';	
                                       		}else if($str >=112 && $str <=116){
                                       			$bgTextDecore	=	'fs-3 bg-light-success text-success';	
                                       		}else if($str >=117 && $str <=121){
                                       			$bgTextDecore	=	'fs-3 bg-dark text-secondary';	
                                       		}else {
                                       			$bgTextDecore	=	'fs-3 bg-light-secondary text-dark';	
                                       		}                                       		
											
                                          $icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                             <a href="javascript:void(0);" data-row-id="'.$jdata->id.'" class="show-detail">
                                             <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                                             </a>
                                             </div>';

                                          if(!empty($jdata->logo_logical_path))
                                          {
                                             $icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5">
                                                <a href="javascript:void(0);">
                                                <div class="symbol-label border-radius-5">
                                                <img src="'.url('public/'.@$jdata->logo_logical_path).'" alt="Company Logo" class="border-radius-5 w-100">
                                                </div>
                                                </a>
                                                </div>';
                                          }	

										$min_sal_mode	=	$max_sal_mode	=	$min_salary_currency	= $max_salary_currency	=	"";
										$max_salary 			=	"";
										$min_salary 			=	"";
										if(@$jdata->min_sal_mode !=''){
										 	$min_sal_mode			=	strtoupper(substr($salaryMode[@$jdata->min_sal_mode], 0, 1)); 
										}
										if(@$jdata->max_sal_mode !=''){
											$max_sal_mode			=	strtoupper(substr($salaryMode[@$jdata->max_sal_mode], 0, 1));
										}
										if(@$jdata->min_salary_currency !=''){
											$min_salary_currency	=	$currencySymbole[@$jdata->min_salary_currency];
										}
										if(@$jdata->max_salary_currency !=''){
											$max_salary_currency	=	$currencySymbole[@$jdata->max_salary_currency];
										}
										if(@$jdata->max_salary !=''){
											$max_salary 			=	GeneralMethod::thousandsCurrencyFormat(@$jdata->max_salary); 
										}
										if(@$jdata->min_salary !=''){
											$min_salary 			=	GeneralMethod::thousandsCurrencyFormat(@$jdata->min_salary); 
					 				    } 
				  
										$client_loc_arr = DB::table('client_location_jobs')
										->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
										->join('countries', 'countries.id', '=', 'client_location.country_id')
										->select('client_location.address', 'client_location.city',  'client_location.postcode', 'countries.country_name')
										//->where('jobs.status', 'active')
										->where('client_location_jobs.job_id', @$jdata->id)
										->first();

										 $jobid 		= $jdata->id;
										 $client_loc 	= DB::table('client_location_jobs')
										 ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
										 ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
										 ->select(
										 'client_location.address', 'client_location.city',  'client_location.postcode', 'client_location.country_id',
										 'city_list.city as city_name'
										 )
										 ->where('client_location_jobs.job_id', $jobid)
										 ->first();
                                             
										 $country_id		=	$country_name = '';	
										 $country_id		=	@$client_loc->country_id;
										 if(@$country_id > 0){
											$country_name	= GeneralMethod::getCountryName($country_id);	
											foreach($country_name as $cval){
											   $country_name 	=  ucwords($cval['sortname']);	
											}
										 }
                                             
										 $city				= substr(@$client_loc->city, 0,6); 
										 if(@$client_loc->country_id == 101){
											$city			= substr(@$client_loc->city_name, 0, 6);		
										 }
					                                       				  	
                                       			
                                       ?>
                        <tr>
                          <td class="d-flex align-items-center border-0"><!--<div class="symbol symbol-circle symbol-50px overflow-hidden me-2">
                                             <a href="javascript:void(0);">
                                                 <div class="symbol-label">
                                                     <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/svg/brand-logos/plurk.svg" alt="Emma Smith" class="w-100" />
                                                 </div>
                                             </a>
                                             </div>-->
                            <?php 
											 			echo @$icon;
											 ?>
                            <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-bs-toggle="tooltip" title="<?php echo ucwords(@$jdata->title); ?>"> <?php echo substr(ucwords(@$jdata->title), 0, 9); ?> </a> <span class="fw-bold text-muted d-block fs-7"><?php echo ucwords($jdata->clients_name); ?></span> </div></td>
                          <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"><?php echo @$exp; ?> Yrs</a> <span class="fw-bold text-muted d-block fs-7"> <?php echo $min_salary_currency.$max_salary.'/'.$min_sal_mode; ?> - <?php echo $min_salary_currency.$min_salary.'/'.$max_sal_mode; ?></span> </div></td>
                          <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-bs-toggle="tooltip" title="<?php echo @$client_loc_arr->address ?>"> <?php echo substr(ucwords(@$client_loc_arr->address), 0, 9); ?> </a> <span class="fw-bold text-muted d-block fs-7" data-bs-toggle="tooltip" title="<?php echo @$client_loc_arr->city ?>, <?php echo @$client_loc_arr->country_name ?>"> <?php echo ucwords(substr(@$city, 0, 10)) ?>, <?php echo ucwords(substr(@$country_name, 0, 10)); ?> </span> </div></td>
                          <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-gray-800 text-hover-primary mb-1"> <span class="badge <?php echo @$statusClass; ?>"><?php echo strtoupper(@$jdata->status); ?></span> </a> <span>R-<?php echo @$jdata->id; ?></span> </div></td>
                        </tr>
                        <?php 
                                       }
                                       }
                                       ?>
                      </tbody>
                      <!--end::Table body-->
                    </table>
                  </div>
                  <!--end::Table-->
                </div>
              </div>
            </div>
            <!--end::Body-->
          </div>
          <!--end::Tables Widget 5-->
        </div>
        <!--end::Requisitions-->
      </div>
      <!--end::Interview Tracker & Requisitions-->
      <?php 
            //echo '<pre>'; print_r($newJoining); exit; 
            
            ?>
      <!--begin::Joinings & Offer Tracker-->
      <div class="row g-5 g-xxl-8">
        <!--begin::Joinings-->
        <div class="col-xxl-6 col-lg-6 col-md-6 col-sm-12 col-sm-12 pt-5 pb-0 mt-0">
          <!--begin::Tables Widget 5-->
          <div class="card card-xxl-stretch">
            <!--begin::Header-->
            <div class="card-header border-0 pt-5">
              <h3 class="card-title align-items-start flex-column"> <span class="card-label fw-bolder fs-3 mb-1">Joinings</span> </h3>
              <div class="card-toolbar"> <a href="<?php echo url('staffing_company/job-onboard'); ?>" class="btn btn-primary btn-sm">View All</a> </div>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body py-3">
              <div class="tab-content">
                <!--begin::Tap pane-->
                <div class="tab-pane fade show active" id="kt_table_widget_5_tab_1">
                  <!--begin::Table container-->
                  <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                      <!--begin::Table head-->
                      <thead>
                        <!--begin::Table row-->
                        <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                          <th class="min-w-180px">Name</th>
                          <th class="min-w-100px">Requisitions</th>
                          <th class="min-w-125px">Referred By</th>
                          <th class="min-w-40px">Date</th>
                        </tr>
                        <!--end::Table row-->
                      </thead>
                      <!--end::Table head-->
                      <!--begin::Table body-->
                      <tbody class="text-gray-600 fw-bold">
                        <?php 
                                       //echo '<pre>'; print_r($offer); exit; 
                                       
                                       if(!empty($newJoining)){
                                       	foreach($newJoining as $jdata){
                                       	
                                       		$full_name		=	'';
                                       		$full_name		=	substr(@$jdata->full_name, 0, 15);
                                       		
                                       		$clients_name	=	'';
                                          		$clients_name	=	substr(@$jdata->clients_name, 0, 15);
                                       		
                                       		$title			=	'';
                                          		$title			=	substr(@$jdata->title, 0, 15);
                                       		
                                       		$current_designation		=	'';
                                          		$current_designation		=	substr(@$jdata->current_designation, 0, 15);
                                       		
                                       		$submitted_by_name		=	'';
                                          		$submitted_by_name		=	substr(@$jdata->submitted_by_name, 0, 15);
                                       		
                                       		$company_name			=	'';
                                          		$company_name			=	substr(@$jdata->company_name, 0, 15);
                                       		
                                       		$bgTextDecore			=	$strVal	=	'';
                                       		$str					=	trim(@$jdata->full_name);
                                       		$str	= $strVal		=	strtolower($str[0]);
                                       		$str					=	ord($str);
                                       		if($str >=97 && $str <=101){
                                       			$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
                                       		}else if($str >=102 && $str <=106){
                                       			$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
                                       		}else if($str >=107 && $str <=111){
                                       			$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
                                       		}else if($str >=112 && $str <=116){
                                       			$bgTextDecore		=	'fs-3 bg-light-success text-success';	
                                       		}else if($str >=117 && $str <=121){
                                       			$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
                                       		}else {
                                       			$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
                                       		}
                                       		
											$schedule_date	=	'';
											$schedule_date	= 	date("d M Y", strtotime($jdata->created_at));
											
											$schedule_time	=	'';
											$schedule_time	= 	date("H:i", strtotime($jdata->created_at));
                                       	
                                       ?>
                        <!--begin::Table row-->
                        <tr>
                          <!--begin::Name=-->
                          <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                            <!--<div class="symbol symbol-circle symbol-50px overflow-hidden me-2">
                                             <a href="javascript:void(0);">
                                                 <div class="symbol-label">
                                                     <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-26.jpg" alt="img-1" class="w-100" />
                                                 </div>
                                             </a>
                                             </div>-->
                            <div class="symbol symbol-circle symbol-50px overflow-hidden me-2 rounded-2"> <a href="javascript:void(0);">
                              <div class="symbol-label rounded-2 <?php echo @$bgTextDecore; ?>"><?php echo strtoupper(@$strVal); ?></div>
                              </a> </div>
                            <!--end::Avatar-->
                            <!--begin::Name details-->
                            <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-bs-toggle="tooltip" title="<?php echo ucwords($full_name); ?>"> <?php echo substr(ucwords($full_name),0, 9); ?> </a> <span class="fw-bold text-muted d-block fs-7"><?php echo ucwords($current_designation); ?></span> </div>
                            <!--begin::Name details-->
                          </td>
                          <!--end::Name=-->
                          <!--begin::Requisitions=-->
                          <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-bs-toggle="tooltip" title="<?php echo ucwords($title); ?>"> <?php echo ucwords($title); ?> </a> <span class="fw-bold text-muted d-block fs-7"><?php echo ucwords($clients_name); ?></span> </div></td>
                          <!--end::Requisitions=-->
                          <!--begin::Interview Mode-->
                          <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-bs-toggle="tooltip" title="<?php echo ucwords($submitted_by_name); ?>"> <?php echo substr(ucwords($submitted_by_name), 0, 9); ?> </a> <span class="fw-bold text-muted d-block fs-7"><?php echo ucwords($company_name); ?></span> </div></td>
                          <!--begin::Interview Mode-->
                          <!--begin::Date-->
                          <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"><?php echo @$schedule_date; ?></a> <span class="fw-bold text-muted d-block fs-7"><?php echo @$schedule_time; ?></span> </div></td>
                          <!--begin::Date-->
                        </tr>
                        <!--end::Table row-->
                        <?php 
                                       }
                                       }	
                                       ?>
                      </tbody>
                      <!--end::Table body-->
                    </table>
                  </div>
                  <!--end::Table-->
                </div>
              </div>
            </div>
            <!--end::Body-->
          </div>
          <!--end::Tables Widget 5-->
        </div>
        <!--end::Joinings-->
        <!--begin::Offer Tracker-->
        <div class="col-xxl-6 col-lg-6 col-md-6 col-sm-12 col-sm-12 pt-5 pb-0 mt-0">
          <!--begin::Tables Widget 5-->
          <div class="card card-xxl-stretch">
            <!--begin::Header-->
            <div class="card-header border-0 pt-5">
              <h3 class="card-title align-items-start flex-column"> <span class="card-label fw-bolder fs-3 mb-1">Offer Tracker</span> </h3>
              <div class="card-toolbar"> <a href="<?php echo url('staffing_company/applicationoffer'); ?>" class="btn btn-primary btn-sm">View All</a> </div>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body py-3">
              <div class="tab-content">
                <!--begin::Tap pane-->
                <div class="tab-pane fade show active" id="kt_table_widget_5_tab_1">
                  <!--begin::Table container-->
                  <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                      <!--begin::Table head-->
                      <thead>
                        <!--begin::Table row-->
                        <tr class="text-start text-muted fw-bolder fs-7 gs-0">
                          <th class="min-w-180px">Name</th>
                          <th class="min-w-100px">Requisitions</th>
                          <th class="min-w-125px">Referred By</th>
                          <th class="min-w-40px">Stage</th>
                        </tr>
                        <!--end::Table row-->
                      </thead>
                      <!--end::Table head-->
                      <!--begin::Table body-->
                      <tbody class="text-gray-600 fw-bold">
                        <?php 
                                       //echo '<pre>'; print_r($offer); exit; 
                                       
                                       if(!empty($offer)){
                                       	foreach($offer as $odata){
                                       	
                                       		$full_name		=	'';
                                       		$full_name		=	substr(@$odata->full_name, 0, 15);
                                       		
                                       		$client_name	=	'';
                                          		$client_name	=	substr(@$odata->client_name, 0, 15);
                                       		
                                       		$title			=	'';
                                          		$title			=	substr(@$odata->title, 0, 15);
                                       		
                                       		$current_designation		=	'';
                                          		$current_designation		=	substr(@$odata->current_designation, 0, 15);
                                       		
                                       		$master_status_name		=	'';
                                          		$master_status_name		=	substr(@$odata->master_status_name, 0, 15);
                                       		
                                       		$submitted_by			=	'';
                                          		$submitted_by			=	substr(@$odata->submitted_by, 0, 15);
                                       		
                                       		$child_status_name		=	'';
                                          		$child_status_name		=	substr(@$odata->child_status_name, 0, 15);
                                       		
                                       		$bgTextDecore			=	$strVal	=	'';
                                       		$str					=	trim(@$odata->full_name);
                                       		$str	= $strVal		=	strtolower($str[0]);
                                       		$str					=	ord($str);
                                       		if($str >=97 && $str <=101){
                                       			$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
                                       		}else if($str >=102 && $str <=106){
                                       			$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
                                       		}else if($str >=107 && $str <=111){
                                       			$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
                                       		}else if($str >=112 && $str <=116){
                                       			$bgTextDecore		=	'fs-3 bg-light-success text-success';	
                                       		}else if($str >=117 && $str <=121){
                                       			$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
                                       		}else {
                                       			$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
                                       		}
                                       		
                                       		$company_name			=	'';
                                          	$company_name			=	substr(@$odata->company_name, 0, 15);
                                       ?>
                        <!--begin::Table row-->
                        <tr>
                          <!--begin::Name=-->
                          <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                            <!--<div class="symbol symbol-circle symbol-50px overflow-hidden me-2">
                                             <a href="javascript:void(0);">
                                                 <div class="symbol-label">
                                                     <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-26.jpg" alt="Emma Smith" class="w-100" />
                                                 </div>
                                             </a>
                                             </div>-->
                            <div class="symbol symbol-circle symbol-50px overflow-hidden me-2 rounded-2"> <a href="javascript:void(0);">
                              <div class="symbol-label rounded-2 <?php echo @$bgTextDecore; ?>"><?php echo strtoupper(@$strVal); ?></div>
                              </a> </div>
                            <!--end::Avatar-->
                            <!--begin::Name details-->
                            <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-bs-toggle="tooltip" title="<?php echo ucwords(@$full_name); ?>"> <?php echo substr(ucwords(@$full_name), 0, 9); ?> </a> <span class="fw-bold text-muted d-block fs-7"><?php echo ucwords(@$current_designation); ?></span> </div>
                            <!--begin::Name details-->
                          </td>
                          <!--end::Name=-->
                          <!--begin::Requisitions=-->
                          <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-bs-toggle="tooltip" title="<?php echo ucwords(@$title); ?>"> <?php echo substr(ucwords(@$title), 0, 9); ?> </a> <span class="fw-bold text-muted d-block fs-7"><?php echo ucwords(@$client_name); ?></span> </div></td>
                          <!--end::Requisitions=-->
                          <!--begin::Interview Mode-->
                          <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-bs-toggle="tooltip" title="<?php echo ucwords(@$submitted_by); ?>"> <?php echo substr(ucwords(@$submitted_by), 0, 9); ?> </a> <span class="fw-bold text-muted d-block fs-7"><?php echo ucwords(@$company_name); ?></span> </div></td>
                          <!--begin::Interview Mode-->
                          <!--begin::Date-->
                          <td><div class="d-flex flex-column"> <span class="badge offer-bg"><?php echo ucwords(@$master_status_name); ?></span> <span class="fw-bold text-muted d-block fs-7"><?php echo ucwords(@$child_status_name); ?></span> </div></td>
                          <!--begin::Date-->
                        </tr>
                        <!--end::Table row-->
                        <?php 
                                       }
                                       }											  
                                       								  
                                       ?>
                      </tbody>
                      <!--end::Table body-->
                    </table>
                  </div>
                  <!--end::Table-->
                </div>
              </div>
            </div>
            <!--end::Body-->
          </div>
          <!--end::Tables Widget 5-->
        </div>
        <!--end::Offer Tracker-->
      </div>
      <!--end::Joinings & Offer Tracker-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->
@endsection
@push('footer-script')
<script src="{{ asset('assets/plugins/jQueryUI/jquery-ui.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script>
   function current_date() {
   var myDiv = document.getElementById("current_date");
   
   var date 		= new Date();
   var dayList 	= ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
   var monthNames 	= ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
   var dayName 	= dayList[date.getDay()];
   var monthName 	= monthNames[date.getMonth()];
   var today 		= `${dayName}, ${date.getDate()} ${monthName}, ${date.getFullYear()}`;
   // var today = `${dayName}, ${date.getDate()}-${date.getMonth()}-${date.getFullYear()}`;
   
   var hour 	= date.getHours();
   var min 		= date.getMinutes();
   var sec 		= date.getSeconds();
   
   var time = hour + ":" + min + ":" + sec;
   myDiv.innerText =`${today}`;
   //myDiv.innerText =`${today} | ${time} | 2 Hours, 4 miniute ago`;
   //var a = document.createElement("p");
   //alert("call");
   }
   setInterval(current_date, 1000);
   
   function current_time() {
   var myDiv = document.getElementById("current_time");
   
       var now 		= new Date();
   	
   	//alert(now); 
   	//new Date(year, month, date, hour, minute, second, millisecond)
       var then 		= new Date("<?php echo @$user->last_login; ?>");
       var minsDiff 	= Math.floor(( now.getTime()-then.getTime() ) / 1000 / 60);
       var hoursDiff 	= Math.floor(minsDiff / 60);
       minsDiff 		= minsDiff % 60;
     var titme			= hoursDiff + ' Hours and ' + minsDiff + ' Miniute ago';
   	
   	myDiv.innerText =`${titme}`;
   }
   setInterval(current_time, 1000);
</script>
<script>
   var updated = true;
   
   function showNewTodoForm() {
       let url = "{{ route('staffing_company.todo-items.create') }}"
   
       $.ajaxModal('#application-md-modal', url);
   }
   
   function initSortable() {
       let updates = {'pending-tasks': false, 'completed-tasks': false};
       let completedFirstPosition = $('#completed-tasks').find('li.draggable').first().data('position');
       let pendingFirstPosition = $('#pending-tasks').find('li.draggable').first().data('position');
   
       $('#pending-tasks').sortable({
           connectWith: '#completed-tasks',
           cursor: 'move',
           handle: '.handle',
           stop: function (event, ui) {
               const id = ui.item.data('id');
               const oldPosition = ui.item.data('position');
   
               if (updates['pending-tasks']===true && updates['completed-tasks']===true)
               {
                   const inverseIndex =  completedFirstPosition > 0 ? completedFirstPosition - ui.item.index() + 1 : 1;
                   const newPosition = inverseIndex;
   
                   updateTodoItem(id, position={oldPosition, newPosition}, status='completed');
   
               }
               else if(updates['pending-tasks']===true && updates['completed-tasks']===false)
               {
                   const newPosition = pendingFirstPosition - ui.item.index();
   
                   updateTodoItem(id, position={oldPosition, newPosition});
               }
   
               //finally, clear out the updates object
               updates['pending-tasks']=false;
               updates['completed-tasks']=false;
           },
           update: function (event, ui) {
               updates[$(this).attr('id')] = true;
           }
       }).disableSelection();
   
       $('#completed-tasks').sortable({
           connectWith: '#pending-tasks',
           cursor: 'move',
           handle: '.handle',
           stop: function (event, ui) {
               const id = ui.item.data('id');
               const oldPosition = ui.item.data('position');
   
               if (updates['pending-tasks']===true && updates['completed-tasks']===true)
               {
                   const inverseIndex =  pendingFirstPosition > 0 ? pendingFirstPosition - ui.item.index() + 1 : 1;
                   const newPosition = inverseIndex;
   
                   updateTodoItem(id, position={oldPosition, newPosition}, status='pending');
               }
               else if(updates['pending-tasks']===false && updates['completed-tasks']===true)
               {
                   const newPosition = completedFirstPosition - ui.item.index();
   
                   updateTodoItem(id, position={oldPosition, newPosition});
               }
   
               //finally, clear out the updates object
               updates['pending-tasks']=false;
               updates['completed-tasks']=false;
           },
           update: function (event, ui) {
               updates[$(this).attr('id')] = true;
           }
       }).disableSelection();
   }
   
   function updateTodoItem(id, pos, status=null) {
       let data = {
           _token: '{{ csrf_token() }}',
           id: id,
           position: pos,
       };
   
       if (status) {
           data = {...data, status: status}
       }
   
       $.easyAjax({
           url: "{{ route('staffing_company.todo-items.updateTodoItem') }}",
           type: 'POST',
           data: data,
           container: '#todo-items-list',
           success: function (response) {
               $('#todo-items-list').html(response.view);
               initSortable();
           }
       });
   }
   
   function showUpdateTodoForm(id) {
       let url = "{{ route('staffing_company.todo-items.edit', ':id') }}"
       url = url.replace(':id', id);
   
       $.ajaxModal('#application-md-modal', url);
   }
   
   function deleteTodoItem(id) {
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
               let url = "{{ route('staffing_company.todo-items.destroy', ':id') }}";
               url = url.replace(':id', id);
   
               let data = {
                   _token: '{{ csrf_token() }}',
                   _method: 'DELETE'
               }
   
               $.easyAjax({
                   url,
                   data,
                   type: 'POST',
                   container: '#roleMemberTable',
                   success: function (response) {
                       if (response.status == 'success') {
                           $('#todo-items-list').html(response.view);
                           initSortable();
                       }
                   }
               })
           }
       });
   }
   
   @if ($user->roles->count() > 0)
       $('#todo-items-list').html(`{!! $todoItemsView !!}`);
   @endif
   
   initSortable();
   
   $('body').on('click', '#create-todo-item', function () {
       $.easyAjax({
           url: "{{route('staffing_company.todo-items.store')}}",
           container: '#createTodoItem',
           type: "POST",
           data: $('#createTodoItem').serialize(),
           success: function (response) {
               if(response.status == 'success'){
                   $('#todo-items-list').html(response.view);
                   initSortable();
   
                   $('#application-md-modal').modal('hide');
               }
           }
       })
   });
   
   $('body').on('click', '#update-todo-item', function () {
       const id = $(this).data('id');
       let url = "{{route('staffing_company.todo-items.update', ':id')}}"
       url = url.replace(':id', id);
   
       $.easyAjax({
           url: url,
           container: '#editTodoItem',
           type: "POST",
           data: $('#editTodoItem').serialize(),
           success: function (response) {
               if(response.status == 'success'){
                   $('#todo-items-list').html(response.view);
                   initSortable();
   
                   $('#application-md-modal').modal('hide');
               }
           }
       })
   });
   
   $('body').on('change', '#todo-items-list input[name="status"]', function () {
       const id = $(this).data('id');
       let status = 'pending';
   
       if ($(this).is(':checked')) {
           status = 'completed';
       }
   
       updateTodoItem(id, null, status);
   })
</script>
@endpush