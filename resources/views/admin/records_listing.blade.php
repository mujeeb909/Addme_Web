@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                @if ($message = Session::get('success_msg'))
                    <div class="alert alert-success alert-block mb-1 p-2">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @elseif (Session::get('warning_msg'))
                    <div class="alert alert-warning alert-block mb-1 p-2">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ Session::get('warning_msg') }}</strong>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header"><i class="fa fa-list"></i> {{ $data['page_title'] }}
                        @if ($data['show_add'] == 1)
                            <span class="pull-right"><a href="{{ $data['add_btn_url'] }}" class="btn btn-md btn-success"><i
                                        class="fa fa-plus"></i> {{ $data['add_button'] }}</a></span>

                        @endif
                        @if ($data['ExportBpAdmins'] == 1)
                        <span class="pull-right"><a href="{{ route('exportBpAdmins') }}" class="btn btn-md btn-info"><i
                            class="fa fa-plus"></i> Export CSV</a></span>
                            @endif
                    </div>
                    <form action="{{ admin_url() . '/' . $data['page_method'] }}" method="get" name="t-search"
                        id="t-search">
                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-responsive-sm table-striped">
                                @if ($data['page_method'] == 'staffs')
                                    <thead>
                                        <tr>
                                            <th colspan="1">
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ $data['name'] }}" placeholder="Name...">
                                            </th>
                                            <th colspan="1">
                                                <input type="text" class="form-control" name="email"
                                                    value="{{ $data['email'] }}" placeholder="Email...">
                                            </th>
                                            <th colspan="1">
                                                <input type="text" class="form-control" name="username"
                                                    value="{{ $data['username'] }}" placeholder="Username...">
                                            </th>
                                            <th colspan="2">
                                                <select class="form-control" name="status" id="status">
                                                    <option value="">Status</option>
                                                    <option value="1" {{ $data['status'] == '1' ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option value="0" {{ $data['status'] == '0' ? 'selected' : '' }}>
                                                        InActive</option>
                                                </select>
                                            </th>
                                            <th colspan="3" align="right">
                                                <button type="submit" name="submit-btn" class="btn btn-xs btn-success"><i
                                                        class="fa fa-search"></i>&nbsp;Search</button>
                                                <a class="btn btn-xs btn-danger" href="{{ $data['page_method'] }}"><i
                                                        class="fa fa-close"></i>&nbsp;Reset</a>
                                            </th>
                                        </tr>
                                    </thead>
                                @elseif($data['page_method'] == 'customers' || $data['page_method'] == 'business_customers')
                                    <thead>
                                        <tr>
                                            <th colspan="1">
                                                @if ($data['page_method'] == 'customers')
                                                    <input type="text" class="form-control" name="name"
                                                        value="{{ $data['name'] }}" placeholder="Name...">
                                                @else
                                                    <input type="text" class="form-control" name="name"
                                                        value="{{ $data['name'] }}" placeholder="Name...">
                                                @endif
                                            </th>
                                            <th colspan="1">
                                                <input type="text" class="form-control" name="email"
                                                    value="{{ $data['email'] }}" placeholder="Email...">
                                            </th>
                                            <th colspan="1">
                                                <input type="text" class="form-control" name="username"
                                                    value="{{ $data['username'] }}" placeholder="Username...">
                                            </th>
                                            <th colspan="2">
                                                <select class="form-control" name="status" id="status">
                                                    <option value="">Status</option>
                                                    <option value="1" {{ $data['status'] == '1' ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option value="0" {{ $data['status'] == '0' ? 'selected' : '' }}>
                                                        InActive</option>
                                                </select>
                                            </th>
                                            <th colspan="2">
                                                <select class="form-control companyName select2 " id="companyName" name="company_name">

                                                    <option value="" {{ empty($data['company_name']) ? 'selected' : '' }}>Select Company</option>
                                                    @foreach($data['distinctCompanyNames'] as $company)
                                                        <option value="{{ $company }}" {{ $company == $data['company_name'] ? 'selected' : '' }}>{{ $company }}</option>
                                                    @endforeach
                                                </select>
                                            </th>
                                            <th colspan="4" align="right">
                                                <button type="submit" name="submit-btn" class="btn btn-xs btn-success"><i
                                                        class="fa fa-search"></i>&nbsp;Search</button>
                                                <a class="btn btn-xs btn-danger" href="{{ $data['page_method'] }}"><i
                                                        class="fa fa-close"></i>&nbsp;Reset</a>
                                                        @if ($data['page_method'] == 'customers')
                                                        <a class="btn btn-xs btn-info" href="javascript:;" data-toggle="modal"
                                                            data-target="#import-customers-modal"><i
                                                                class="fa fa-cloud-upload"></i>&nbsp;Import CSV</a>
                                                    @endif
                                            </th>

                                        </tr>
                                    </thead>
                                @elseif($data['page_method'] == 'clients')
                                    <thead>
                                        <tr>
                                            <th colspan="1">
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ $data['name'] }}" placeholder="Name...">
                                            </th>
                                            <th colspan="1">
                                                <input type="text" class="form-control" name="email"
                                                    value="{{ $data['email'] }}" placeholder="Email...">
                                            </th>
                                            <th colspan="2">
                                                <select class="form-control" name="status" id="status">
                                                    <option value="">Status</option>
                                                    <option value="1" {{ $data['status'] == '1' ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option value="0" {{ $data['status'] == '0' ? 'selected' : '' }}>
                                                        InActive</option>
                                                </select>
                                            </th>
                                            <th colspan="3" align="right">
                                                <button type="submit" name="submit-btn"
                                                    class="btn btn-xs btn-success"><i
                                                        class="fa fa-search"></i>&nbsp;Search</button>
                                                <a class="btn btn-xs btn-danger" href="{{ $data['page_method'] }}"><i
                                                        class="fa fa-close"></i>&nbsp;Reset</a>
                                            </th>
                                        </tr>
                                    </thead>
                                @elseif($data['page_method'] == 'chips')
                                    <thead>
                                        <tr>
                                            <th colspan="1">
                                                <input type="text" class="form-control" name="str_code"
                                                    value="{{ $data['str_code'] }}" placeholder="Code...">
                                                <input type="hidden" name="q"
                                                    value="{{ isset($_GET['q']) && trim($_GET['q']) != '' ? $_GET['q'] : 'available' }}">
                                            </th>
                                            @if (isset($_GET['q']) && trim($_GET['q']) == 'mapped')
                                                <th colspan="1">
                                                    <input type="text" class="form-control" name="username"
                                                        value="{{ $data['username'] }}" placeholder="Username...">
                                                </th>
                                            @endif
                                            <th colspan="1">
                                                <select class="form-control" name="status" id="status">
                                                    <option value="">Status</option>
                                                    <option value="1" {{ $data['status'] == '1' ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option value="0" {{ $data['status'] == '0' ? 'selected' : '' }}>
                                                        InActive</option>
                                                </select>
                                            </th>
                                            <th colspan="1">
                                                <select class="form-control" name="device" id="device">
                                                    <option value="">Device</option>
                                                    @foreach (devices() as $device)
                                                        <option value="{{ $device['id'] }}"
                                                            {{ isset($_GET['device']) && trim($_GET['device']) == $device['id'] ? 'selected' : '' }}>
                                                            {{ $device['title'] }}</option>
                                                    @endforeach
                                                </select>
                                            </th>
                                            <th colspan="2" align="right">
                                                <button type="submit" name="submit-btn"
                                                    class="btn btn-xs btn-success"><i
                                                        class="fa fa-search"></i>&nbsp;Search</button>
                                                <!-- <a class="btn btn-xs btn-danger" href="{{ $data['page_method'] }}"><i class="fa fa-close"></i>&nbsp;Reset</a> -->
                                            </th>
                                            <th colspan="1">

                                            </th>
                                            <th colspan="1">
                                                <a class="btn btn-xs btn-info" href="javascript:;" data-toggle="modal"
                                                    data-target="#export-codes-modal"><i
                                                        class="fa fa-cloud-download"></i>&nbsp;Export CSV</a>
                                            </th>
                                            <th colspan="1">
                                                <a class="btn btn-xs btn-info" href="javascript:;" data-toggle="modal"
                                                    data-target="#attach-brand-modal"><i
                                                        class="fa fa-cloud-upload"></i>&nbsp;Import CSV</a>
                                            </th>
                                        </tr>
                                    </thead>
                                @elseif($data['page_method'] == 'feedbacks')
                                    <thead>
                                        <tr>
                                            <th colspan="1">
                                                <input type="text" class="form-control" name="full_name"
                                                    value="{{ $data['full_name'] }}" placeholder="Full Name...">
                                            </th>
                                            <th colspan="1">
                                                <input type="date" class="form-control datepicker" name="from_date"
                                                    value="{{ $data['from_date'] }}" placeholder="From Date...">
                                            </th>
                                            <th colspan="1">
                                                <input type="date" class="form-control datepicker" name="to_date"
                                                    value="{{ $data['to_date'] }}" placeholder="To Date...">
                                            </th>
                                            <th colspan="1">
                                                <button type="submit" name="submit-btn"
                                                    class="btn btn-xs btn-success"><i
                                                        class="fa fa-search"></i>&nbsp;Search</button>
                                                <a class="btn btn-xs btn-danger" href="{{ $data['page_method'] }}"><i
                                                        class="fa fa-close"></i>&nbsp;Reset</a>
                                            </th>
                                        </tr>
                                    </thead>
                                @elseif($data['page_method'] == 'business_requests')
                                    <thead>
                                        <tr>
                                            <th colspan="1">
                                                <input type="text" class="form-control" name="email"
                                                    value="{{ $data['email'] }}" placeholder="Email...">
                                            </th>
                                            <th colspan="1">
                                                <input type="date" class="form-control datepicker" name="from_date"
                                                    value="{{ $data['from_date'] }}" placeholder="From Date...">
                                            </th>
                                            <th colspan="1">
                                                <input type="date" class="form-control datepicker" name="to_date"
                                                    value="{{ $data['to_date'] }}" placeholder="To Date...">
                                            </th>
                                            <th colspan="1">
                                                <button type="submit" name="submit-btn"
                                                    class="btn btn-xs btn-success"><i
                                                        class="fa fa-search"></i>&nbsp;Search</button>
                                                <a class="btn btn-xs btn-danger" href="{{ $data['page_method'] }}"><i
                                                        class="fa fa-close"></i>&nbsp;Reset</a>
                                            </th>
                                        </tr>
                                    </thead>
                                @endif
                                <thead>
                                    <tr>
                                        @php
                                            $j = 0;
                                        @endphp

                                        @foreach ($data['column_title'] as $col_title)
                                            <th class="text-{{ $j == 0 ? 'left' : 'center' }}">{{ $col_title }}</th>
                                            @php
                                                $j++;
                                            @endphp
                                        @endforeach

                                        @if ($data['show_action'] == 1)
                                            <th class="text-center" width="15%">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($data['tbl_records']) > 0)
                                        @foreach ($data['tbl_records'] as $row)
                                            <tr>
                                                @php
                                                    $i = 0;
                                                @endphp
                                                @foreach ($data['column_values'] as $col_value)
                                                    <td class="text-{{ $i == 0 ? 'left' : 'center' }}"
                                                        style="max-width:400px;">{!! columnValue($col_value, $row, $data['image_url']) !!}</td>
                                                    @php
                                                        $i++;
                                                    @endphp
                                                @endforeach

                                                @if ($data['show_action'] == 1)
                                                    <td class="text-center">
                                                        @if ($data['show_update'] == 1 || $data['show_delete'] == 1 || $data['show_child'] == 1)
                                                            <div class="btn-group">
                                                                @if ($data['show_child'] == 1 &&  $data['page_title'] == "Customers")
                                                                <a class="btn btn-md btn-warning mb-1"
                                                                    href="{{ $data['child_btn_url'] . '/' . $row->id }}">
                                                                    <i class="fa fa-eye"></i> Child</a>
                                                                @endif
                                                                &nbsp;&nbsp;
                                                                @if ($data['show_update'] == 1)
                                                                    <a class="btn btn-md btn-primary mb-1"
                                                                        href="{{ $data['update_btn_url'] . '/' . encrypt($row->id) }}">
                                                                        <i class="fa fa-pencil"></i> Update</a>
                                                                @endif
                                                                &nbsp;&nbsp;
                                                                @if ($data['show_delete'] == 1)
                                                                    <a class="btn btn-md btn-danger mb-1"
                                                                        href="javascript:;"
                                                                        onClick="{{ str_replace('{({row-id})}', encrypt($row->id), $data['delete_click']) }}"><i
                                                                            class="fa fa-times"></i> Delete</a>
                                                                @endif


                                                            </div>
                                                        @endif
                                                        @if ($data['custom'] != '')
                                                            {!! str_replace('{({row-id})}', encrypt($row->id), $data['custom']) !!}
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                            @php
                                                $i++;
                                            @endphp
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ $j + 1 }}" align="center">No record found!!!</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                            {!! $data['tbl_records']->appends(Request::all())->links() !!}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="attach-brand-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Import CSV</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="" class="form-horizontal" method="post" id="import-form"
                        enctype="multipart/form-data">
                        <div class="card-body">
                            @csrf
                            <fieldset class="mt-3">
                                <div class="form-group mt-1 col-xs-12 col-sm-12 pull-left">
                                    <label class="col-sm-12 col-xs-12 no-pad" for="file">CSV</label>
                                    <input type="file" name="file">
                                </div>
                            </fieldset>
                        </div>
                        <div class="card-body">
                            <a href="{{ asset_url() . '/import-codes.csv' }} " class="">Download Sample CSV</a>
                            <button type="button" id="submit" class="btn btn-md btn-primary pull-right"
                                onClick="submitForm_to('{{ admin_url() }}/import_codes_csv','{{ admin_url() }}/chips?q=branded','import-form')"><i
                                    class="fa fa-dot-circle-o"></i> Submit</button>
                            <div class="text-left">
                                <div id="msgs-import-form" class="pt-2"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div id="export-codes-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Export Codes & Mark as Active</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="" class="form-horizontal" method="post" id="export-form"
                        enctype="multipart/form-data">
                        <div class="card-body">
                            @csrf
                            <fieldset class="mt-3">
                                <div class="form-group mt-1 col-xs-12 col-sm-12 pull-left">
                                    <label class="col-sm-12 col-xs-12 no-pad" for="file">Device:</label>
                                    <select class="form-control" name="device" id="device">
                                        @foreach (devices() as $device)
                                            <option value="{{ $device['id'] }}"
                                                {{ isset($_GET['device']) && trim($_GET['device']) == $device['id'] ? 'selected' : '' }}>
                                                {{ $device['title'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mt-1 col-xs-12 col-sm-12 pull-left">
                                    <label class="col-sm-12 col-xs-12 no-pad" for="file">Brand/Username:</label>
                                    <input type="text" class="form-control" name="username" id="username"
                                        value="" placeholder="Brand/Username...">
                                </div>
                                <div class="form-group mt-1 col-xs-12 col-sm-12 pull-left">
                                    <label class="col-sm-12 col-xs-12 no-pad" for="file">No. of NFC Chips:</label>
                                    <input type="number" class="form-control" min="0" name="export_limit"
                                        id="export_limit" value="" placeholder="No. of NFC Chips...">
                                </div>
                            </fieldset>
                        </div>
                        <div class="card-body">
                            <button type="button" class="btn btn-md btn-primary pull-right"
                                onClick="submitForm_to('{{ admin_url() }}/export_chips','{{ admin_url() }}/chips?q=available','export-form')"><i
                                    class="fa fa-dot-circle-o"></i> Export & Mark as Active</button>
                            <p><b>Note:</b> These NFC chips will be marked as active as well.</p>
                            <div class="text-left">
                                <div id="msgs-export-form" class="pt-2"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div id="import-customers-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Import CSV with Brand</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="" class="form-horizontal" method="post" id="import-customers-form"
                        enctype="multipart/form-data">
                        <div class="card-body">
                            @csrf
                            <fieldset class="mt-3">
                                <div class="form-group mt-1 col-xs-12 col-sm-12 pull-left">
                                    <label class="col-sm-12 col-xs-12 no-pad" for="file">CSV</label>
                                    <input type="file" name="file">
                                </div>
                            </fieldset>
                        </div>
                        <div class="card-body">
                            <a href="{{ asset_url() . '/import-customers.csv' }} " class="">Download Sample CSV</a>
                            <button type="button" id="submit" class="btn btn-md btn-primary pull-right"
                                onClick="submitForm_to('{{ admin_url() }}/import_customers','{{ admin_url() }}/customers','import-customers-form')"><i
                                    class="fa fa-dot-circle-o"></i> Submit</button>
                            <div class="text-left">
                                <div id="msgs-import-customers-form" class="pt-2"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
@endsection

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Select2 CSS and JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 on the companyName dropdown
        $('#companyName').select2({
            placeholder: 'Select Company',
            allowClear: true,
            width: '100%',
        });
    });
</script>

