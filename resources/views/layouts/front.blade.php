<!doctype html>
<html lang="en">

<head>
    <!-- Google tag (gtag.js) -->
    {{-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-0CXLG4WYLN"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-0CXLG4WYLN');
    </script> --}}

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($page_title) ? $page_title . ' - ' : '' }} AddMee</title>
    <meta name="description" content="{{ isset($page_title) ? $page_title . ' - ' : '' }} AddMee" />
    @if (!empty($profile))
        <meta property="og:image"
            content="{{ $profile->logo != '' ? image_url($profile->logo) : uploads_url() . 'img/addmee-logo.png' }}">
    @else
        <meta property="og:image" content="{{ uploads_url() . 'img/addmee-logo.png' }}">
    @endif
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1024">
    <meta property="og:image:height" content="1024">

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
    <link rel="manifest" href="{{ asset_url() . 'manifest.json' }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset_url() . 'ms-icon-144x144.png' }}">
    <meta name="theme-color" content="#ffffff">

    <!-- Styles -->
    <link href="{{ asset_url() . 'admin/css/flag-icon.min.css' }}" rel="stylesheet">
    <link href="{{ asset_url() . 'admin/css/simple-line-icons.css' }}" rel="stylesheet">
    <!-- Main styles for this application-->
    <link href="{{ asset_url() . 'admin/css/addmee-new.css?v1.2.1' }}" rel="stylesheet">
    {{-- <link href="{{ asset_url() . 'admin/css/addmee.css' }}" rel="stylesheet"> --}}
    <link rel="stylesheet"
        href="https://owlcarousel2.github.io/OwlCarousel2/assets/owlcarousel/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="https://owlcarousel2.github.io/OwlCarousel2/assets/owlcarousel/assets/owl.theme.default.min.css">
    {{-- <script src="https://owlcarousel2.github.io/OwlCarousel2/assets/vendors/jquery.min.js"></script> --}}
    <style type="text/css">
        .logo img,
        .logo-img-div {
            /* background-color: {{ isset($settings['photo_border_color']) ? $settings['photo_border_color'] : '#fff' }}; */
            border: 3px solid {{ isset($settings['photo_border_color']) ? $settings['photo_border_color'] : '#fff' }};
        }

        .logo-img-div {
            object-fit: scale-down;
            display: block;
            margin: 0 auto;
            width: 110px;
            height: 110px;
            margin-top: 58px;
            border-radius: 100%;
            box-shadow: 6px 10px 25px 0 rgba(0, 0, 0, 0.19), 0 6px 20px 0 rgba(0, 0, 0, 0.19) !important;
            background-color: #EBE2E4;
            background-size: 100%;
            background-repeat: no-repeat;
        }

        div.company-logo {
            background-repeat: no-repeat;
            border-radius: 50%;
            background-size: 100%;
            background-position: center;
        }

        .bio-text {
            border: 1px solid #EAECF0;
            /* box-shadow: 1px 1px 1px 0 rgba(0, 0, 0, 0.19), 0 1px 2px 0 rgba(0, 0, 0, 0.19); */
            /* border: 1px solid {{ isset($settings['bio_text_border_color']) ? $settings['bio_text_border_color'] : '#e4e4df' }}; */
            background-color: {{ isset($settings['section_color']) && $settings['section_color'] != '#f8f8f8' ? $settings['section_color'] : '#f5f5f4' }};
        }

        .bio {
            color: {{ isset($settings['text_color']) ? $settings['text_color'] : 'rgba(17, 24, 3, 1)' }};
        }

        .bio-title {
            color: {{ isset($settings['text_color']) ? $settings['text_color'] : 'rgba(17, 24, 3, 1)' }};
            background-color: {{ isset($settings['bg_color']) && $settings['bg_color'] != '#f8f8f8' ? str_replace('0.3', '0.5', $settings['bg_color']) : '#f5f5f4' }};
        }

        .bg-row-- {
            background-image: url('{{ uploads_url() . 'img/bg.png' }}');
            background-size: 100%;
            margin-top: 44px;
        }

        .bg-row {
            background-color: {{ isset($settings['bg_color']) ? $settings['bg_color'] : '#f8f8f8' }};
            /* margin-top: 44px; */
        }

        .profile-name {
            color: {{ isset($settings['text_color']) ? $settings['text_color'] : 'rgba(17, 24, 3, 1)' }};
            /* margin-bottom: 15px; */
            font-size: 20px;
            font-weight: 600;
            text-align: left;
            padding-left: 15px;
        }

        #name-web-view-a {
            text-align: left;
            font-size: 16px;
            float: left;
            padding-left: 15px;
            color: {{ isset($settings['text_color']) ? $settings['text_color'] : '#9E9E9E' }};
        }

        .user-designation {
            text-align: left;
            font-size: 18px;
            padding-left: 15px;
            color: {{ isset($settings['text_color']) ? $settings['text_color'] : '#616161' }};
            font-weight: 600;
        }

        .company-name {
            text-align: left;
            font-size: 16px;
            padding-left: 15px;
            color: {{ isset($settings['text_color']) ? $settings['text_color'] : '#9E9E9E' }};
        }

        .company-name-2 {
            text-align: left;
            font-size: 16px;
            font-weight: 400;
            color: {{ isset($settings['text_color']) ? $settings['text_color'] : '#9E9E9E' }};
        }

        .social-titles {
            float: left;
            font-size: 12px;
            max-width: 100%;
            min-width: 100%;
            color: {{ isset($settings['text_color']) ? $settings['text_color'] : 'rgba(17, 24, 3, 1)' }};
        }

        .social-titles:hover {
            color: {{ isset($settings['text_color']) ? $settings['text_color'] : 'rgba(17, 24, 3, 1)' }};
            /* text-decoration: underline; */
        }

        .focused-profile {
            padding: 0 10px;
            margin: 0;
            height: 140px;
            width: calc(100% - 20px);
            background-color: {{ isset($settings['focused_profile']) ? $settings['focused_profile'] : '' }};
        }

        .focused-profile img {
            padding: 0;
            max-width: 110px;
            margin: 0;
        }

        .focused-profile::before {
            content: '';
            width: calc(100% - 20px);
            display: block;
            height: 140px;
            position: absolute;
            border-radius: 15px;
            background-color: {{ isset($settings['focused_profile_bg']) ? $settings['focused_profile_bg'] : '' }};
        }

        .focused-profile .social-titles {
            position: absolute;
            z-index: 999;
            border-radius: 15px;
            width: calc(100% - 20px);
        }

        .focused-profile::after {
            content: '';
            width: calc(100% - 20px);
            display: block;
            height: 140px;
            border-radius: 15px;
            position: absolute;
            background-color: {{ isset($settings['focused_profile_bg']) ? $settings['focused_profile_bg'] : '' }};
        }

        .focused-profile a {
            padding: 15px calc(3% + 10px) 10px calc(3% + 10px);
        }

        .focused-profile h6 {
            font-weight: 600;
            font-size: 17px !important;
            margin: 0;
            line-height: 42px;
            color: {{ isset($settings['text_color']) ? $settings['text_color'] : '#2E2E2E' }};
        }

        .profile-section {
            float: left;
            margin: 0 15px;
            width: calc(100% - 30px);
            border-radius: 10px;
            background-color: {{ isset($settings['section_color']) ? $settings['section_color'] : '#fff' }};
        }

        .red-button {
            border: 1px solid {{ isset($settings['btn_color']) ? $settings['btn_color'] : '#000' }};
            background-color: {{ isset($settings['btn_color']) ? $settings['btn_color'] : '#000' }};
            box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, 0.2), 0px 2px 2px 0px rgba(0, 0, 0, 0.14), 0px 1px 5px 0px rgba(0, 0, 0, 0.12);
        }

        .red-button:hover {
            background-color: {{ isset($settings['btn_color']) ? $settings['btn_color'] : '#000' }};
            border: 1px solid {{ isset($settings['btn_color']) ? $settings['btn_color'] : '#000' }};
        }

        .profile-title h6 {
            font-size: 13px;
        }

        .shadow {
            margin-bottom: 6.9px;
        }

        .brand-profiles {
            border-radius: 5px;
            width: 100%;
            padding: 2px 3px;
            margin: 0;
        }

        .header-h2 {
            font-size: 20px;
            margin: 15px 0 5px 0;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-align: left;
            padding-left: 15px;
        }

        /* horizental scroll */
        .horizontal-scroll-wrapper {
            overflow: auto;
            white-space: nowrap;
        }

        .grid-square-normal-1 {
            display: inline-block;
            width: 75%;
            margin: 15px 1.5% 0 1.5%;
            white-space: normal;
        }

        /* scrollbar css */
        ::-webkit-scrollbar {
            width: 5px;
            /* Width of the scrollbar */
            height: 5px;
            /* Height of the scrollbar for the horizontal scrollbar */
        }

        /* Target the scrollbar thumb */
        ::-webkit-scrollbar-thumb {
            background-color: #ccc;
            /* Color of the scrollbar thumb */
            border-radius: 6px;
            /* Border radius of the scrollbar thumb */
        }

        /* Change appearance on hover */
        ::-webkit-scrollbar-thumb:hover {
            background-color: #555;
            /* Color on hover */
        }

        /* Change appearance when actively scrolling */
        ::-webkit-scrollbar-thumb:active {
            background-color: #333;
            /* Color when actively scrolling */
        }

        @media (max-width:426px) {
            .focused-profile img {
                max-width: 90px;
            }

            .focused-profile::before,
            .focused-profile::after,
            .focused-profile {
                /* height: 120px; */
            }

            .grid-square-normal-1 {
                width: 95%;
            }

            /* .shadow {
                margin-bottom: 5px;
                border-radius: 10px !important;
             } */
        }

        /* carousal style */
        .owl-prev,
        .owl-next {
            width: 22px;
            height: 97px;
            position: absolute;
            margin: 5px 2px !important;
            top: 44%;
            transform: translateY(-50%);
            display: block !important;
            /* border: 2px solid rgb(255 255 255 / 70%) !important; */
            /* border-radius: 50% !important; */
            background-color: rgb(0 0 0 / 60%) !important;
        }

        .owl-prev {
            left: -3%;
        }

        .owl-next {
            right: -3%;
        }

        .owl-prev i,
        .owl-next i {
            transform: scale(2, 5);
            color: #ccc;
        }

        .owl-prev span,
        .owl-next span {
            color: #fff !important;
            font-size: 26px;
        }

        .owl-carousel {
            width: 95%;
            margin: 0 auto;
        }

        /* .owl-prev.disabled,
        .owl-next.disabled {
            display: none !important;
        } */
        .owl-prev,
        .owl-next {
            opacity: 0 !important;
        }

        .owl-carousel:hover .owl-prev {
            opacity: 1 !important;
        }

        .owl-carousel:hover .owl-next {
            opacity: 1 !important;
        }

        .my-profiles {
            width: 100%
        }

        .svg-icon {
            display: inline-block;
            max-width: 135px;
            float: left;
        }

        .focused-profile .svg-icon {
            max-width: 110px;
        }

        .svg-icon svg {
            width: 100%;
            height: 100%;
            max-height: 135px;
            fill: {{ isset($settings['color_link_icons']) && $settings['color_link_icons'] == 1 ? $settings['btn_color'] : '' }};
        }

        .svg_colorized svg rect{
            fill: {{ isset($settings['color_link_icons']) && $settings['color_link_icons'] == 1 ? $settings['btn_color'] : '' }};
        }

        .svg-icon svg rect{
            fill: {{ isset($settings['color_link_icons']) && $settings['color_link_icons'] == 1 ? $settings['btn_color'] : '' }};
        }

        .svg_colorized svg{
            background-color: {{ isset($settings['color_link_icons']) && $settings['color_link_icons'] == 1 ? $settings['btn_color'] : '' }};
            border-radius: 19px;
        }

        .svg_colorized > svg [fill="black"] {
            /* fill: $settings['btn_color']; */
            fill: {{ isset($settings['color_link_icons']) && $settings['color_link_icons'] == 1 ? $settings['btn_color'] : '' }};
        }

        .user-img img {
            height: auto !important;
            border-radius: 40px;
            min-width: 100%;
        }

        .profile-title {
            float: left;
            width: 100%;
        }

        @media(max-width: 767px) {
            img.shadow {
                border-radius: 10px !important;
            }
        }
    </style>

    <!-- Scripts -->

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
</head>
@if (isset($settings['bg_image']) && $settings['bg_image'] != '')

    <body
        style="background-image: url('{{ $settings['bg_image'] }}'); background-repeat: no-repeat; background-size: cover;">
    @else

        <body>
@endif
<div class="container">
    <div class="row">
        <a href="https://addmee.de/" class="top-banner-app"><img src="{{ uploads_url() . 'img/cart.png' }}"
                width="28">&nbsp;Tippe hier, um jetzt deinen AddMee zu erhalten</a>
        <div class="col-md-3 col-xs-12 col-lg-3 hidden-xs">&nbsp;</div>
        @yield('content')
    </div>
</div>
{{-- <script type="module">
    // Import the functions you need from the SDKs you need
    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/9.14.0/firebase-app.js";
    import {
        getAnalytics
    } from "https://www.gstatic.com/firebasejs/9.14.0/firebase-analytics.js";
    // TODO: Add SDKs for Firebase products that you want to use
    // https://firebase.google.com/docs/web/setup#available-libraries

    // Your web app's Firebase configuration
    // For Firebase JS SDK v7.20.0 and later, measurementId is optional
    const firebaseConfig = {
        apiKey: "AIzaSyCziXI5o4vw3-6E-c1Yf4EnyURbVZddIFM",
        authDomain: "addmee-f98d1.firebaseapp.com",
        projectId: "addmee-f98d1",
        storageBucket: "addmee-f98d1.appspot.com",
        messagingSenderId: "589918697888",
        appId: "1:589918697888:web:3db36f07d56f12fda10cc5",
        measurementId: "G-NY3L2DM1VR"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const analytics = getAnalytics(app);
</script> --}}
<script src="https://owlcarousel2.github.io/OwlCarousel2/assets/owlcarousel/owl.carousel.js"></script>
</body>

</html>
