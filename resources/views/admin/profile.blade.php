@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="col-sm-12 col-xl-12 no-pad">
    	@if ($message = Session::get('success_msg'))
        <div class="alert alert-success alert-block mb-1">
            <button type="button" class="close" data-dismiss="alert">×</button>    
            <strong>{{ $message }}</strong>
        </div>
        @endif
        <div class="card">
            <div class="card-header"> <i class="fa fa-align-justify"></i> {{ $data['page_title'] }} <small></small> </div>
            <form class="form-horizontal" action="" method="post" name="user-new" id="user-new">
                <div class="card-body">
                    <fieldset class="mt-3">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="first_name">Name</label>
                                <input type="text" id="name" name="name" value="{{ !empty($tbl_record) ? $tbl_record->name : '' }}" placeholder="Name..." class="form-control mb-1" onBlur="validate('txt', 'text', 'name', 'Name');">
                                <span style="display:none;" class="name_info"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="email">Email</label>
                                <input type="text" id="email" disabled name="email" value="{{ !empty($tbl_record) ? $tbl_record->email : '' }}" class="form-control mb-1" placeholder="Email..." onBlur="validate('txt', 'email', 'email', 'Email');">
                                <span style="display:none;" class="email_info"></span>
                            </div>
                        </div>                                
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label class="col-sm-12 col-xs-12 no-pad" for="gender">Gender</label>
                                <div class="form-check form-check-inline">
                                    <label class="col-form-label mr-2" for="male">Male</label>
                                    <input class="form-check-input" type="radio" name="gender" value="1" id="male" {{ (!empty($tbl_record) && $tbl_record->gender == 1) ? 'checked' : '' }}>
                                    <label class="col-form-label mr-2" for="female">Female</label>
                                    <input class="form-check-input" type="radio" name="gender" value="2" id="female" {{ (!empty($tbl_record) && $tbl_record->gender == 2) ? 'checked' : '' }}>
                                    <label class="col-form-label mr-2" for="unknown">Unknown</label>
                                    <input class="form-check-input" type="radio" name="gender" value="3" id="unknown" {{ (!empty($tbl_record) && $tbl_record->gender == 3) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>								
                    </fieldset>
                </div>
                <div class="card-footer">
                    <button type="button" id="submit" class="btn btn-primary" onClick="submitForm_to('{{ $data['post_url'] }}', '{{ $data['redirect'] }}','user-new')"><i class="fa fa-dot-circle-o"></i> Submit</button>
                    @csrf
                    @if(!empty($tbl_record))
                    <input id="tbl_id" name="tbl_id" type="hidden" value="{{ $tbl_record->id }}">
                    @endif
                    <div class="text-left">
                        <div id="msgs-user-new" class="pt-2"></div> 
                    </div>
                </div>                        
            </form>
        </div>
        
        <div class="card">
            <div class="card-header"> <i class="fa fa-align-justify"></i> Change Password <small></small> </div>
            <form class="form-horizontal" action="" method="post" name="user-new-1" id="user-new-1">
                <div class="card-body">
                    <fieldset class="mt-3">
                        <div class="row">                           
                            <div class="form-group mt-1 col-xs-12 col-sm-6 pull-left">
                                <label for="opassword">Current Password:</label>
                                <input type="password" name="current_password" id="current_password" placeholder="Current Password..." autocomplete="off" value="" class="form-control mb-1" onblur="validate('txt', 'password', 'current_password', 'Current Password');" readonly onFocus="$(this).removeAttr('readonly');">
                                <span style="display:none;" class="current_password_info"></span>
                            </div>
                        </div>
                        <div class="row">                           
                            <div class="form-group mt-1 col-xs-12 col-sm-6 pull-left">
                                <label for="password">New Password:</label>
                                <input type="password" name="password" id="password" placeholder="New Password..." autocomplete="off" value="" class="form-control mb-1" onblur="validate('txt', 'password', 'password', 'New Password');" readonly onFocus="$(this).removeAttr('readonly');">
                                <span style="display:none;" class="password_info"></span>
                            </div>                                      
                            <div class="form-group mt-1 col-xs-12 col-sm-6 pull-left">
                                <label for="password_confirmation">Confirm Password:</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password..." autocomplete="off" value="" class="form-control mb-1" readonly onFocus="$(this).removeAttr('readonly');">
                                <span style="display:none;" class="password_confirmation_info"></span>
                            </div>
                        </div>								
                    </fieldset>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-primary" onClick="submitForm_to('{{ $data['password_url'] }}','{{ $data['redirect'] }}','user-new-1')"><i class="fa fa-dot-circle-o"></i> Submit</button>
                    @csrf
                    @if(!empty($tbl_record))
                    <input id="tbl_id" name="tbl_id" type="hidden" value="{{ $tbl_record->id }}">
                    @endif
                    <div class="text-left">
                        <div id="msgs-user-new-1" class="pt-2"></div> 
                    </div>
                </div>                        
            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function(e) {
    $('input[type="email"]').attr('disabled',true);
});
</script>
@endsection