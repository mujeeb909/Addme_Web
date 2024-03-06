@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="col-sm-12 col-xl-12">
            <div class="card">
                <div class="card-header"> <i class="fa fa-align-justify"></i> {{ $data['page_title'] }}</div>
                <form class="form-horizontal" method="post" enctype="multipart/form-data"
                    name="{{ $data['page_method'] . '_form' }}" id="{{ $data['page_method'] . '_form' }}">
                    <div class="card-body">
                        <fieldset class="mt-3">
                            @csrf
                            @php
                                $i = 0;
                            @endphp

                            @foreach ($data['form_fields'] as $name => $title)
                                @php
                                    $title = explode('_', $title);
                                    $type = trim($title[1]);
                                    $title = trim(str_replace('-', ' ', $title[0]));
                                @endphp
                                <?php //pre_print($type);exit;
                                ?>
                                <div class="form-group mt-1 col-xs-12 col-sm-6 pull-left">
                                    <!-- checkbox and radio -->
                                    @if ($type == 'radio')
                                    <label class="col-sm-12 col-xs-12 no-pad" for="{{ $name }}">{{ $title }}</label>
                                    <div class="form-check form-check-inline display-ib">
                                        @foreach ($data[$name] as $idx => $val)
                                            @php
                                                $val = explode('_', $val);
                                                $radio_name = trim(str_replace('-', ' ', $val[0]));
                                                $val = trim($val[1]);
                                            @endphp
                                            <p class="pull-left display-ib">
                                                <input class="form-check-input" type="radio"
                                                    name="{{ $name }}"
                                                    value="{{ $val }}"
                                                    id="{{ strtolower($radio_name) }}{{ $name }}"
                                                    {{ !empty($tbl_record) && $tbl_record->$name == $val ? 'checked' : (empty($tbl_record) && $idx == 0 ? 'checked' : '') }}>
                                                <label class="col-form-label mr-4"
                                                    for="{{ strtolower($radio_name) }}{{ $name }}">{{ $radio_name }}</label>
                                            </p>
                                        @endforeach
                                    </div>
                                    <!-- radio buttons end -->
                                    @elseif ($type == 'checkbox' && in_array($name, ['is_pet', 'is_sos', 'is_personal', 'is_ch_business']))
                                    <!-- Checkboxes -->

                                        <label class="col-sm-12 col-xs-12 no-pad" for="{{ $name }}">{{ $title }}</label>
                                        <div class="form-check form-check-inline display-ib">
                                            <p class="pull-left display-ib">
                                                <input class="form-check-input" type="checkbox"
                                                       name="{{ $name }}"
                                                       value="1"
                                                       id="{{ $name }}"
                                                       {{ !empty($tbl_record) && $tbl_record->$name ? 'checked' : '' }}>
                                                <label class="col-form-label ml-2" for="{{ $name }}">{{ $title }}</label>
                                            </p>
                                        </div>

                                    @elseif($type == 'textarea')
                                        <label class="col-sm-12 col-xs-12 no-pad"
                                            for="{{ $name }}">{{ $title }}</label>
                                        <textarea name="{{ $name }}" id="{{ $name }}" placeholder="{{ $title }}..." autocomplete="off"
                                            class="form-control mb-1"
                                            onBlur="validate('txt', '{{ $type }}', '{{ $name }}', '{{ $title }}');">{{ !empty($tbl_record) ? $tbl_record->$name : '' }}</textarea>
                                        <span style="display:none;" class="{{ $name . '_info' }}"></span>
                                        <!-- textarea ends -->
                                    @else
                                        <label for="{{ $name }}">{{ $title }}:</label>
                                        <!-- select bix -->
                                        @if ($type == 'dd')
                                            @php $dd_val = !empty($tbl_record) ? $tbl_record->$name : ''; @endphp
                                            {!! select_tpl($data[$name], $name, $title, 'y', $dd_val) !!}
                                        @elseif($type == 'ddm')
                                            $dd_val = !empty($tbl_record) ? $tbl_record->$name : '';
                                            {{ multi_select_tpl($data[$name], $name, $title, 'y', $dd_val) }}
                                        @else
                                            <!-- text, password, file fields -->
                                            <input type="{{ $type }}" name="{{ $name }}"
                                                id="{{ $name }}" placeholder="{{ $title }}..."
                                                autocomplete="off"
                                                value="{{ !empty($tbl_record) && !in_array($name, ['cpassword', 'password']) ? $tbl_record->$name : '' }}"
                                                class="form-control mb-1"
                                                onBlur="validate('txt', '{{ $type }}', '{{ $name }}', '{{ $title }}');">
                                        @endif
                                        <span style="display:none;" class="{{ $name . '_info' }}"></span>
                                    @endif
                                </div>
                                @php
                                    $i++;
                                @endphp

                                @if ($i % 2 == 0 && $i != count($data['form_fields']))
                        </fieldset>
                        <fieldset>
                            @endif
                            @endforeach
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
                            <div id="msgs-{{ $data['page_method'] . '_form' }}" class="pt-2"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(e) {
            $('input[type="email"]').attr('disabled', true);
            // $('input[type="username"]').attr('disabled',true);

            $('#type').change(function(e) {
                if ($(this).val() == 'url') {
                    $('#base_url').parent('.form-group').fadeOut(150);
                } else {
                    $('#base_url').parent('.form-group').fadeIn(150);
                }
            });

            if ($('#business_company')) {
                let is_pro = $("input[name='is_pro']:checked").val();
                console.log(is_pro)
                if (is_pro != 3 && is_pro != 2 && is_pro != 4) {
                    $('#business_company').parent('.form-group').addClass('d-none')
                }

                $('input[name="is_pro"]').change(function(e) {
                    let is_pro = $("input[name='is_pro']:checked").val();
                    console.log(is_pro)
                    if (is_pro != 3 && is_pro != 2 && is_pro != 4) {
                        $('#business_company').parent('.form-group').addClass('d-none')
                    } else {
                        $('#business_company').parent('.form-group').removeClass('d-none')
                    }

                    if (is_pro == 1 || is_pro == -1) {
                        $('#subscription_expires_on').parent('.form-group').removeClass('d-none')
                        $('#subscription_date').parent('.form-group').removeClass('d-none')
                    } else {
                        $('#subscription_expires_on').parent('.form-group').addClass('d-none')
                        $('#subscription_date').parent('.form-group').addClass('d-none')
                    }
                });
            }
        });
    </script>
@endsection
