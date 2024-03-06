<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ucwords(config('app.name', 'AddMee')) }}</title>

    <title>{{ config('app.name', 'AddMee') }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset_url() . 'favicon-32x32.png' }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset_url() . 'favicon-96x96.png' }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset_url() . 'favicon-16x16.png' }}">

    <!-- Fonts -->
    <!--<link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">-->

    <!-- Styles -->
    <link href="{{ assets_url('css/flag-icon.min.css') }}" rel="stylesheet">
    <link href="{{ assets_url('css/simple-line-icons.css') }}" rel="stylesheet">
    <!-- Main styles for this application-->
    <link href="{{ assets_url('css/style.css') }}{{ assets_version() }}" rel="stylesheet">
    <link href="{{ assets_url('css/my-style.css') }}{{ assets_version() }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ assets_url('js/bootstrap.min.js') }}" defer></script>
    <script src="{{ assets_url('js/jquery.min.js') }}"></script>
</head>

<body class="app flex-row align-items-center">
    <main id="app" class="width-100">
        @yield('content')
    </main>

    <script src="{{ assets_url('js/jquery.form.js') }}" defer></script>
    <script src="{{ assets_url('js/functions.js') }}{{ assets_version() }}"></script>
    <script type="text/javascript">
        $(window).on('keydown', function(event) {
            //console.log(event.keyCode);
            if (event.keyCode == 13) {
                $('#submit').trigger('click');
                event.preventDefault();
                return false;
            }
        });
    </script>
</body>

</html>
