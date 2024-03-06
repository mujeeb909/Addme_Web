@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="col-sm-12 col-xl-12">
            @if ($message = Session::get('success_msg'))
                <div class="alert alert-success alert-block mb-1">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif
            <div class="card">
                <div class="card-header"> <i class="fa fa-align-justify"></i> {{ $data['page_title'] }}</div>
                <form class="form-horizontal" method="post" enctype="multipart/form-data"
                    name="delete_business_customer_form" id="delete_business_customer_form">
                    <div class="card-body">
                        <fieldset class="mt-3">
                            @csrf
                            <div class="form-group mt-1 col-xs-12 col-sm-12 pull-left">
                                <!-- checkbox and radio -->
                                <h5 class="col-sm-12 col-xs-12 no-pad mb-4" for="confirm">All users accounts attached to
                                    this business admin will also be deleted. Are you sure to delete this?</h5>
                                <div class="form-check form-check-inline display-ib">
                                    <p class="pull-left display-ib">
                                        <input class="form-check-input" type="radio" name="confirm" value="1"
                                            id="yesconfirm" checked="">
                                        <label class="col-form-label mr-4" for="yesconfirm">Yes</label>
                                    </p>
                                    <p class="pull-left display-ib">
                                        <input class="form-check-input" type="radio" name="confirm" value="0"
                                            id="noconfirm">
                                        <label class="col-form-label mr-4" for="noconfirm">No</label>
                                    </p>
                                </div>
                                <!-- checkbox and radio ends -->
                            </div>

                        </fieldset>
                    </div>
                    <div class="card-footer">
                        <button type="button" id="submit" class="btn btn-md btn-primary"
                            onClick="submitForm_to('{{ $data['post_url'] }}','{{ $data['redirect'] }}','{{ $data['page_method'] . '_form' }}')"><i
                                class="fa fa-dot-circle-o"></i> Submit</button>
                        @if (!empty($tbl_record))
                            <input type="hidden" name="tbl_id" id="tbl_id" value="{{ $tbl_record->id }}">
                        @endif
                        <div class="text-left">
                            <div id="msgs-delete_business_customer_form" class="pt-2"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
