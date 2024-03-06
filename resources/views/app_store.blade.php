<!doctype html>

<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AddMee</title>
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset_url() . 'apple-icon-57x57.png' }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset_url() . 'apple-icon-60x60.png' }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset_url() . 'apple-icon-72x72.png' }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset_url() . 'apple-icon-76x76.png' }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset_url() . 'apple-icon-114x114.png' }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset_url() . 'apple-icon-120x120.png' }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset_url() . 'apple-icon-144x144.png' }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset_url() . 'apple-icon-152x152.png' }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset_url('apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset_url() . 'android-icon-192x192.png' }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset_url() . 'favicon-32x32.png' }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset_url() . 'favicon-96x96.png' }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset_url() . 'favicon-16x16.png' }}">

    <script type="text/javascript">
        function getMobileOS() {
            const ua = navigator.userAgent
            if (/android/i.test(ua)) {
                window.location.href = "https://play.google.com/store/apps/details?id=com.ls.nfc.addmee";
            } else if ((/iPad|iPhone|iPod/.test(ua)) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints >
                    1)) {
                window.location.href = "https://apps.apple.com/ae/app/addmee/id1566147650";

            } else {

                window.location.href = "https://play.google.com/store/apps/details?id=com.ls.nfc.addmee"
            }

        }

        window.onload = getMobileOS
    </script>
</head>

<body>
    <div class="mw6 center pa3 sans-serif">
        <h1 class="mb4"></h1>
    </div>
</body>

</html>
