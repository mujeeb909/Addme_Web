@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col">
			<div class="card">
				<div class="card-body" style="padding:1.5rem">
					<div class="bg-gradient-info p-3"><i class="fa fa-2x fa-users"></i></div>
					<div class="text-value">{{ $data['total_customers'] }}</div>
					<div><small class="text-muted text-uppercase font-weight-bold">Total Customers</small></div>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card">
				<div class="card-body" style="padding:1.5rem">
					<div class="bg-gradient-danger p-3"><i class="fa fa-2x fa-users"></i></div>
					<div class="text-value">{{ $data['total_bCustomers'] }} </div>
					<div><small class="text-muted text-uppercase font-weight-bold">Total Business Profiles</small></div>

				</div>
			</div>
		</div>
		<div class="col">
			<div class="card">
				<div class="card-body" style="padding:1.5rem">
					<div class="bg-gradient-success p-3"><i class="fa fa-2x fa-users"></i></div>
					<div class="text-value">{{ $data['total_pCustomers'] }}</div>
					<div><small class="text-muted text-uppercase font-weight-bold"> Total Personal Profiles</small></div>

				</div>
			</div>
		</div>
		<div class="col">
			<div class="card">
				<div class="card-body" style="padding:1.5rem">
					<div class="bg-gradient-warning p-3"><i class="fa fa-2x fa-money"></i></div>
					<div class="text-value">4.8</div>
					<div><small class="text-muted text-uppercase font-weight-bold">Total Earnings</small></div>

				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col">
			<div class="card">
				<div class="card-body" style="padding:1.5rem">
					<div class="bg-gradient-info p-3"><i class="fa fa-2x fa-microchip"></i></div>
					<div class="text-value">{{ $data['unique_codes']->activated }}</div>
					<div><small class="text-muted text-uppercase font-weight-bold">Chips Mapped with Profiles</small></div>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card">
				<div class="card-body" style="padding:1.5rem">
					<div class="bg-gradient-warning p-3"><i class="fa fa-2x fa-microchip"></i></div>
					<div class="text-value">{{ $data['unique_codes']->available }} </div>
					<div><small class="text-muted text-uppercase font-weight-bold">Available Active Chips</small></div>

				</div>
			</div>
		</div>
		<div class="col">
			<div class="card">
				<div class="card-body" style="padding:1.5rem">
					<div class="bg-gradient-danger p-3"><i class="fa fa-2x fa-microchip"></i></div>
					<div class="text-value">{{$data['unique_codes']->inactive}}</div>
					<div><small class="text-muted text-uppercase font-weight-bold">Inactive Chips</small></div>

				</div>
			</div>
		</div>
		<div class="col">
			<div class="card">
				<div class="card-body" style="padding:1.5rem">
					<div class="bg-gradient-success p-3"><i class="fa fa-2x fa-microchip"></i></div>
					<div class="text-value">{{ $data['unique_codes']->branded }}</div>
					<div><small class="text-muted text-uppercase font-weight-bold"> Tagged with Brand Name</small></div>

				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6 col-lg-6">
			<div class="card">
				<div class="card-body" style="padding:1.25rem">
					<div class="text-value"></div>
					<div><small class="text-muted text-uppercase font-weight-bold">Peronal Profiles: {{ CalcPercentage($data['total_pCustomers'], $data['total_customers']) }}%</small></div>
					<div class="progress progress-xs my-2">
						<div class="progress-bar bg-danger" role="progressbar" style="width:{{ CalcPercentage($data['total_pCustomers'], $data['total_customers']) }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-lg-6">
			<div class="card">
				<div class="card-body" style="padding:1.25rem">
					<div class="text-value"></div>
					<div><small class="text-muted text-uppercase font-weight-bold">Business Profiles: {{ CalcPercentage($data['total_bCustomers'], $data['total_customers']) }}%</small></div>
					<div class="progress progress-xs my-2">
						<div class="progress-bar bg-success" role="progressbar" style="width:{{ CalcPercentage($data['total_bCustomers'], $data['total_customers']) }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-lg-6">
			<div class="card">
				<div id="new_customers" style="width: 100%; height: 400px; margin: 0 auto"></div>
			</div>
		</div>
		<div class="col-xs-12 col-lg-6">
			<div class="card">
				<div id="earnings" style="width: 100%; height: 400px; margin: 0 auto"></div>
			</div>
		</div>
		<div class="col-xs-12 col-lg-6">
			<div class="card">
				<div id="gender" style="width: 100%; height: 400px; margin: 0 auto"></div>
			</div>
		</div>
		<div class="col-xs-12 col-lg-6">
			<div class="card">
				<div id="platform" style="width: 100%; height: 400px; margin: 0 auto"></div>
			</div>
		</div>
	</div>
</div>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script type="text/javascript">
	Highcharts.chart('new_customers', {
		title: {
			text: 'New Customers'
		},

		yAxis: {
			allowDecimals: false,
			title: {
				text: 'No. of Customers'
			}
		},
		credits: {
			enabled: false
		},
		xAxis: [{
			categories: [<?php echo $data['no_of_days'] ?>],
			crosshair: true
		}],
		series: [{
			name: 'Days',
			color: '#4dbd74',
			dashStyle: 'ShortDash',
			data: [{
				{
					$data['last_7days_customers']
				}
			}]
		}],

		responsive: {
			rules: [{
				condition: {
					maxWidth: 500
				},
				chartOptions: {
					legend: {
						layout: 'horizontal',
						align: 'center',
						verticalAlign: 'bottom'
					}
				}
			}]
		}

	});

	//earnings
	Highcharts.chart('earnings', {
		title: {
			text: 'Earnings - Last 7 Days'
		},

		yAxis: {
			allowDecimals: false,
			title: {
				text: 'Total Earnings'
			}
		},
		credits: {
			enabled: false
		},
		xAxis: [{
			categories: ["25 Feb", "26 Feb", "27 Feb", "28 Feb", "01 Mar", "02 Mar", "03 Mar"],
			crosshair: true
		}],
		series: [{
			name: 'Days',
			color: '#4dbd74',
			dashStyle: 'ShortDash',
			data: [0, 0, 9, 0, 0, 0, 0]
		}],

		responsive: {
			rules: [{
				condition: {
					maxWidth: 500
				},
				chartOptions: {
					legend: {
						layout: 'horizontal',
						align: 'center',
						verticalAlign: 'bottom'
					}
				}
			}]
		}

	});

	// Create the chart
	Highcharts.chart('platform', {
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false,
			type: 'pie'
		},
		title: {
			text: 'Platform Based SignUps'
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b>: {point.percentage:.1f} %'
				}
			}
		},
		credits: {
			enabled: false
		},
		series: [{
			name: '',
			colorByPoint: true,
			data: [{
					name: 'IOS 10',
					y: 2,
					sliced: true,
					//selected: true
				},
				{
					name: 'Android',
					y: 1,
					//sliced: true,
					//selected: true
				},
			]
		}]
	});

	// Create the chart
	Highcharts.chart('gender', {
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false,
			type: 'pie'
		},
		title: {
			text: 'Gender Based Inbound Calls'
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b>: {point.percentage:.1f} %'
				}
			}
		},
		credits: {
			enabled: false
		},
		series: [{
			name: '',
			colorByPoint: true,
			data: [{
				name: 'Male',
				y: {
					{
						$data['gender_wise'][0] - > male != '' ? $data['gender_wise'][0] - > male : 0
					}
				},
				sliced: true,
				selected: true
			}, {
				name: 'Female',
				y: {
					{
						$data['gender_wise'][0] - > female != '' ? $data['gender_wise'][0] - > female : 0
					}
				}
			}, {
				name: 'Unknown',
				y: {
					{
						$data['gender_wise'][0] - > unknown != '' ? $data['gender_wise'][0] - > unknown : 0
					}
				}
			}]
		}]
	});

	function createChart() {

	}
	// Create the chart		
	document.addEventListener('DOMContentLoaded', function() {
		//createChart();
	}, false);
</script>
<style type="text/css">
	.bg-gradient-info {
		background: linear-gradient(45deg, #39f 0%, #2982cc 100%) !important;
	}

	.bg-gradient-danger {
		background: linear-gradient(45deg, #e55353 0%, #d93737 100%) !important;
		border-color: #d93737 !important;
	}

	.bg-gradient-warning {
		background: linear-gradient(45deg, #f9b115 0%, #f6960b 100%) !important;
		border-color: #f6960b !important;
	}

	.bg-gradient-success {
		background: linear-gradient(45deg, #2eb85c 0%, #1b9e3e 100%) !important;
		border-color: #1b9e3e !important;
	}

	.bg-gradient-info,
	.bg-gradient-danger,
	.bg-gradient-warning,
	.bg-gradient-success {
		border-color: #2982cc !important;
		color: #fff;
		float: left;
		margin-right: 1rem !important;
	}
</style>
@endsection