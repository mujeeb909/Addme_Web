@extends('layouts.front')

@section('content')
    <div class="col-md-6 col-xs-12 col-lg-6 shadow-profile bg-row">
        <div class="profile">
            <div id="profile-image" class="profile-image">
                <div class="user-img">
                    <!--<img alt="profile picture" src="{{ uploads_url() . 'img/customer.png' }}">-->
                    <h2 class="Register" style="width: 100%; text-align: center;">Willkommen bei
                        {{ config('app.name', 'AddMee') }}</h2>
                </div>
            </div>
        </div>
        <div class="logo-tab">
            <div class="powered-by d-none">
                <strong> <a target="_blank" href="#" class="shadow">Erstelle Dein AddMee Profil</a></strong>
            </div>
            <a href="https://www.addmee.de/">
                <img alt='{{ config('app.name', '') }}' class="addmee-logo" height="40"
                    src="{{ uploads_url() . 'img/addmee-logo.png' }}">
            </a>
            <div class="powered-by">
                <!-- <a class="patent" href="#">Patentiert <img alt="AddMee" height="13" src="{{ uploads_url() . 'img/checked.png' }}"></a> -->
            </div>
        </div>

    </div>
@endsection
