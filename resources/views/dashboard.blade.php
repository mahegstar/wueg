@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="section-header">
    <h1>{{ __('Dashboard') }}</h1>
</div>
<div class="section-body">
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-th-large"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ __('Total Categories') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $count_category }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                    <i class="fas fa-th"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ __('Total Subcategories') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $count_subcategory }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-question-circle"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ __('Total Questions') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $count_question }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ __('Total Users') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $count_user }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('User Registration Statistics') }}</h4>
                    <div class="card-header-action">
                        <select class="form-control" id="year-selector">
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="182"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Live Contests') }}</h4>
                </div>
                <div class="card-body">
                    <div class="summary">
                        <div class="summary-item">
                            <h6>{{ __('Active Contests') }} <span class="text-muted">({{ $count_live_contest }})</span></h6>
                            @if($count_live_contest > 0)
                                <p>{{ __('There are currently active contests running. Check the contest section for more details.') }}</p>
                            @else
                                <p>{{ __('No active contests at the moment.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Chart
    var monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    var monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($month_data->pluck('month_name')) !!},
            datasets: [{
                label: '{{ __("User Registrations") }}',
                data: {!! json_encode($month_data->pluck('user_count')) !!},
                backgroundColor: 'rgba(63, 82, 227, 0.8)',
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: false,
            },
            scales: {
                yAxes: [{
                    gridLines: {
                        drawBorder: false,
                        color: '#f2f2f2',
                    },
                    ticks: {
                        beginAtZero: true,
                        stepSize: 10
                    }
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            },
        }
    });

    // Year selector change event
    $('#year-selector').on('change', function() {
        var year = $(this).val();
        $.ajax({
            url: '/dashboard-year/' + year,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                monthlyChart.data.labels = data.mName;
                monthlyChart.data.datasets[0].data = data.mD;
                monthlyChart.options.scales.yAxes[0].ticks.stepSize = data.stepSizeMonth;
                monthlyChart.update();
            }
        });
    });
</script>
@endsection