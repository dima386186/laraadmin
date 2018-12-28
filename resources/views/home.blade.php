<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ LAConfigs::getByKey('site_description') }}">

    <meta property="og:title" content="{{ LAConfigs::getByKey('sitename') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="{{ LAConfigs::getByKey('site_description') }}" />

    <meta property="og:url" content="http://laraadmin.com/" />
    <meta property="og:sitename" content="laraAdmin" />
    <meta property="og:image" content="http://demo.adminlte.acacha.org/img/LaraAdmin-600x600.jpg" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@laraadmin" />
    <meta name="twitter:creator" content="@laraadmin" />

    <title>{{ LAConfigs::getByKey('sitename') }}</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('/la-assets/css/bootstrap.css') }}" rel="stylesheet">

    <link href="{{ asset('la-assets/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Custom styles for this template -->
    <link href="{{ asset('/la-assets/css/main.css') }}" rel="stylesheet">

    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,300,700' rel='stylesheet' type='text/css'>

    <script src="{{ asset('/la-assets/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
    <script src="{{ asset('/la-assets/js/smoothscroll.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">


</head>

<body data-spy="scroll" data-offset="0" data-target="#navigation">

<!-- Fixed navbar -->
<div id="navigation" class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><b>{{ LAConfigs::getByKey('sitename') }}</b></a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                @if (Auth::guest())
                    <li><a href="{{ url('/login') }}">Login</a></li>
                    <li><a href="{{ url('/register') }}">Register</a></li>
                @else
                    <li><a href="{{ url(config('laraadmin.adminRoute')) }}">{{ Auth::user()->name }}</a></li>
                @endif
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            @foreach( $currencies as $currency )
                <span class="currenciesName @if( $currency->name == $firstName ) activeCurrency @endif" data-id="{{ $currency->id }}">{{ $currency->name }}</span>
            @endforeach
                <button type="button" class="btn btn-xs pull-right" data-toggle="modal" data-target="#myModal"><i class="fa fa-eye" aria-hidden="true"></i></button>
        </div>
        <div class="panel-body">
            <canvas id="myChart" width="100" height="50"></canvas>
        </div>
    </div>
</div>

@include( 'accessory/small-modal' )

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="{{ asset('/la-assets/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.js"></script>
<script>
    var chart;

    $(document).ready(function () {

        buildChart( '{{ $labels }}', '{{ $rates }}' );

        $('.currenciesName').on('click', function () {
            var el = $(this);
            var id = el.data('id');

            $.get('{{ url("ajaxDefaultData") }}', {'id': id}, function (data) {
                $('.currenciesName').removeClass('activeCurrency');
                el.addClass('activeCurrency');

                destroyChart();
                buildChart( data.labels, data.rates );
            });
        });

        $('.currencyPeriod').on('click', function () {
            var el = $(this);
            var period = el.data('period');
            var id = $('.activeCurrency').data('id');

            $.get('{{ url('ajaxDataInPeriod') }}', { 'id': id, 'period': period }, function (data) {
                $('#myModal').hide();

                destroyChart();
                buildChart( data.labels, data.rates );
            });
        });
    });

    function buildChart( labels, rates ) {

        var ctx = $("#myChart");

        var labels = labels.split( ',' );
        var rates = rates.split( ',' );

        var data = {
            labels: labels,
            datasets: [
                {
                    label: 'chart',
                    data: rates
                }
            ]
        };

        chart = new Chart(ctx, {
            type: 'line',
            data: data
        });
    }

    function destroyChart() {
        chart.destroy();
    }

</script>
</body>
</html>

