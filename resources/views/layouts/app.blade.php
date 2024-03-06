<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ucwords(config('app.name', 'AddMee')) }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset_url() . 'favicon-32x32.png' }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset_url() . 'favicon-96x96.png' }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset_url() . 'favicon-16x16.png' }}">

    @if (Session::has('download.in.the.next.request'))
        <meta http-equiv="refresh" content="2;url={{ Session::get('download.in.the.next.request') }}">
    @endif

    <!-- Fonts -->
    <!--<link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">-->

    <!-- Styles -->
    <link href="{{ assets_url('css/coreui-icons.min.css') }}" rel="stylesheet">
    <link href="{{ assets_url('css/flag-icon.min.css') }}" rel="stylesheet">
    <link href="{{ assets_url('css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ assets_url('css/simple-line-icons.css') }}" rel="stylesheet">
    <!-- Main styles for this application-->
    <link href="{{ assets_url('css/bootstrap-datepicker.css') }}" rel="stylesheet">
    <link href="{{ assets_url('css/style.css') }}{{ assets_version() }}" rel="stylesheet">
    <link href="{{ assets_url('css/my-style.css') }}{{ assets_version() }}" rel="stylesheet">
    <link href="{{ assets_url('css/richtext.min.css') }}" rel="stylesheet">
    <style type="text/css">
        .form-control:disabled,
        .form-control[readonly] {
            background-color: #fff;
            opacity: 1;
        }

        datepicker td,
        .datepicker th {
            width: 30px;
            height: 30px;
        }

        .white {
            color: #fff;
        }

        label {
            font-weight: 500;
        }

        .col-form-label {
            font-weight: 400;
        }

        .form-check-input {
            margin-right: 0.6rem !important;
        }

        .table .table {
            background-color: #fff;
            width: 98%;
            margin: 0 auto;
        }

        .table .table th {
            border: 0;
        }

        .border-td {
            border: 1px solid #c8ced3;
            border-top: 0;
        }

        .my-modal .col-sm-2,
        .my-modal .col-sm-4,
        .my-modal .col-sm-6 {
            padding: 0.75rem 5px;
        }

        .my-modal .col-sm-12 {
            border-bottom: 1px solid #e4e7ea;
        }

        .select2-container--bootstrap .select2-selection--multiple .select2-search--inline .select2-search__field {
            line-height: 1.9 !important;
        }

        .display-ib {
            display: inline-block;
        }

        .alert-success {
            color: #fff;
            background-color: #2eb85c;
        }

        .alert-danger {
            color: #fff;
            background-color: #e55353;
        }


        .copy-str-code,
        .copy-str-code:hover {
            color: #23282c;
            text-decoration: none;
        }

        .copy-str-code i:hover {
            color: #20a8d8;
            transition: transform 0.2s ease-in;
            transform: scale(1.2);
        }

        .copy-str-code {
            position: relative;
            display: inline-block;
        }

        .copy-str-code .tooltiptext {
            visibility: hidden;
            width: 140px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 150%;
            left: 50%;
            margin-left: -75px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .copy-str-code .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
        }

        .copy-str-code:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        td svg {
            width: 50px;
            height: 50px;
        }
    </style>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>

    <script src="{{ assets_url('js/bootstrap.min.js') }}" defer></script>
    <script src="{{ assets_url('js/jquery.min.js') }}"></script>
</head>

<body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show pace-done"
    data-class="app flex-row align-items-center">

    @include('inc.admin_nav')
    <div class="app-body" id="app">
        @include('inc.admin_sidenav')
        <main class="width-100 main">
            @include('inc.admin_breadcrum')
            @yield('content')
        </main>
    </div>
    @include('inc.admin_footer')

    <script src="{{ assets_url('js/pace.min.js') }}" defer></script>
    <script src="{{ assets_url('js/perfect-scrollbar.min.js') }}" defer></script>
    <script src="{{ assets_url('js/coreui.min.js') }}" defer></script>
    <script src="{{ assets_url('js/bootstrap-datepicker.js') }}" defer></script>
    <script src="{{ assets_url('js/jquery.richtext.js') }}" defer></script>
    <script src="{{ assets_url('js/jquery.form.js') }}" defer></script>
    <script src="{{ assets_url('js/functions.js?v=1.0.0') }}{{ assets_version() }}"></script>
    <link href="{{ assets_url('css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ assets_url('js/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.datepicker,input[type="date"]').datepicker({
                format: "yyyy-mm-dd",
                calendarWeeks: true,
                autoclose: true,
                todayHighlight: true
            });

            $('.datepicker,input[type="date"]').attr('type', 'text');

            $('.nav-link.nav-dropdown-toggle ').click(function(e) {
                //$('.open').removeClass('open');
            });
        });

        var relative_path = '';
        var site_url = '{{ url('/') }}/';
        var admin_url = site_url + 'admin/';
        var xhr;

        function deleteTableEntry(tablename, row, id) {
            var confirmDelete = confirm('Are you sure to delete ?');
            if (confirmDelete == true) {
                $.ajax({
                    type: "GET",
                    url: admin_url + 'delete?idx=' + tablename + '&r=' + row + '&id=' + id,
                    dataType: "html",
                    success: function(data) {

                        location.reload();
                        //$('#tr_'+de_utoa(id)+'').remove();
                    },
                    error: function() {
                        alert("Sorry, The requested property could not be found.");
                    }
                });
            }
        }

        function reset_offset(val) {
            offset = 0;
            $('#load-more').fadeOut(150);
        }

        function utoa(str) {
            return window.btoa(window.btoa(window.btoa(window.btoa(window.btoa(window.btoa(window.btoa(unescape(
                encodeURIComponent(str)))))))));
        }

        $('select.multiple').select2({
            theme: 'bootstrap',
            allowClear: false
        });

        if (document.getElementById('export-csv')) {
            $('#export-csv').click(function() {
                var export_limit = $('#export-limit').val();
                export_limit = (export_limit != '') ? export_limit : 100;
                window.location = '{{ admin_url() }}/export_chips?export_limit=' + export_limit;
            });
        }

        if (document.querySelector('.copy-str-code')) {
            $('.copy-str-code').click(function() {
                var href = $(this).data('href');
                console.log(href)

                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(href).select();
                document.execCommand("copy");
                $temp.remove();

                var tooltip = document.getElementById("myTooltip");
                tooltip.innerHTML = "Copied";
            });
        }

        function showTooltip() {
            var tooltip = document.getElementById("myTooltip");
            tooltip.innerHTML = "Copy";
        }
    </script>
</body>

</html>
