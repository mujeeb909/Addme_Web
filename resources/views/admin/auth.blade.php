@extends('layouts.login')

@section('content')
    <div class="container">
        <div class="row justify-content-center margin-ve">
            <div class="col-md-12 text-center">
                <img src="{{ uploads_url() . 'img/addmee-logo.png' }}" style="max-height:80px;" class="margin-tb-15">
            </div>
            <div class="col-md-6 col-md-offset-3">
                <div class="card-group">
                    <div class="card p-4">
                        <form action="" name="login-form" id="login-form" method="post">
                            <div class="card-body">
                                <h1>Login Here</h1>
                                <p class="text-muted">Sign In to your account</p>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="icon-user"></i></span>
                                    </div>
                                    <input type="text" id="email" name="email"
                                        onBlur="validate('txt', 'email', 'email', 'Email ID');" class="form-control"
                                        placeholder="Email ID">
                                </div>
                                <div class="input-group mb-2">
                                    <span style="display:none;" class="username_info"></span>
                                </div>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="icon-lock"></i></span>
                                    </div>
                                    <input type="password" id="password" autocomplete="off" name="password"
                                        onBlur="validate('txt', 'text', 'password', 'Password');" class="form-control"
                                        placeholder="Password">
                                </div>
                                <div class="input-group">
                                    <span style="display:none;" class="password_info"></span>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-success active mt-3" id="submit"
                                            onClick="submitForm_to('{{ url('admin/login') }}', '{{ url('admin-dashboard') }}', 'login-form')">Submit</button>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div id="msgs-login-form" style=" padding-top:7px; text-align:left;"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
