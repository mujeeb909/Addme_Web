@extends('layouts.front')

@section('content')
    @if ($blurOff == 'blurOn')
        <style type="text/css">
            .edit:hover,
            .edit:visited {
                color: #2E2E2E;
                text-decoration: underline;
            }
        </style>
    @else
        <style type="text/css">
            .opac-3 {
                opacity: 0.3
            }
        </style>
    @endif

    <div class="col-md-12 col-xs-12 col-lg-6 shadow-profile bg-row" id="content-section">
        <div class="profile {{ $blurOff }}">
            @php
                $has_subscription = chk_subscription($profile);
                if ($has_subscription['success'] == false) {
                    /* $profile->logo = ''; */
                }
            @endphp

            <div id="profile-image" class="profile-image">
                <!--<img alt="Pro" class="shadow" style="position: absolute; width: 32px; height: 32px; top: 15px; left: 15px" src="Probutton.png">-->
                <div class="user-img" data-style="background-image:url(<?php echo $profile->banner != '' ? trim($profile->banner) : uploads_url() . 'img/customer.png'; ?>)">
                    <img alt="profile picture" src="<?php echo $profile->banner != '' ? trim($profile->banner) : uploads_url() . 'img/customer.png'; ?>">
                </div>
                <input type="file" name="file" style="display: none" id="photo">
                <div class="logo">
                    <div class="logo-img-div"
                        style="background-image:url({{ $profile->logo != '' ? trim($profile->logo) : uploads_url() . 'img/dp_profile.png' }})">
                    </div>
                    @if ($profile->company_logo != '')
                        <div class="company-logo"
                            style="border: 2px solid {{ $settings['photo_border_color'] }}; background-image:url({{ $profile->company_logo }});">
                        </div>
                    @endif
                </div>
                <div class="logo d-none">
                    <img src="{{ $profile->logo != '' ? trim($profile->logo) : uploads_url() . 'img/dp_profile.png' }}">
                    @if ($profile->company_logo != '')
                        <img class="company-logo"
                            src="{{ $profile->company_logo != '' ? trim($profile->company_logo) : uploads_url() . 'img/company_logo.png' }}"
                            style="border: 2px solid {{ $settings['photo_border_color'] }};">
                    @endif
                </div>
            </div>

            <div class="col-12 pull-left mt-4">
                <div class="user-details-block">
                    <div class="flex-row username">
                        <div class="flex-row">
                            <div class="col-12 col-8-- pull-left no-padding">
                                <h3 class="profile-name">
                                    {{ $profile->first_name == '' && $profile->last_name == '' ? $profile->name : $profile->first_name . ' ' . $profile->last_name }}
                                </h3>
                                @if ($profile->designation != '')
                                    <h3 class="user-designation">
                                        {{ $profile->designation }}
                                        @if ($profile->company_name != '')
                                            <span
                                                class="company-name-2">{{ ' ' . $language_text['at'] . ' ' . $profile->company_name }}</span>
                                        @endif
                                    </h3>
                                @endif
                                @if ($profile->designation == '' && $profile->company_name != '')
                                    <h3 class="company-name"><img class="d-none" alt="profile picture" width="16"
                                            src="{{ uploads_url() . 'img/company-icon.png' }}">
                                        {{ $profile->company_name }}
                                    </h3>
                                @endif
                                @if ($profile->company_address != '')
                                    <h3 class="company-name"><img class="d-none" alt="profile picture" width="16"
                                            src="{{ uploads_url() . 'img/address.png' }}"> {{ $profile->company_address }}
                                    </h3>
                                @endif
                            </div>
                            <div class="col-2 pull-left no-padding d-none">
                                @if ($ContactCard > 0)
                                    <a
                                        href="{{ $blurOff == 'blurOn' ? main_url() . '/contact-card/' . encrypt($profile->id) : '#' }}">
                                        <img alt="profile picture" src="{{ uploads_url() . 'img/contact-card.png' }}"
                                            style="width: 50px; max-width: 90%; float:right; top: -10px; position: relative;"></a>
                                @endif
                            </div>
                            <div class="col-2 pull-left no-padding d-none">
                                <a id="edit" data-toggle="modal" data-target="#myModal" href="javascript:;">
                                    <img alt="profile picture" src="{{ uploads_url() . 'img/connect-icon.png' }}"
                                        style="width: 50px; max-width: 90%; top: -10px; position: relative;"></a>
                            </div>
                            <div class="col-12 pull-left no-padding">
                                {{-- <a id="name-web-view-a"><img class="d-none" alt="profile picture" width="16"
                                        src="{{ uploads_url() . 'img/link-icon.png' }}">
                                    {{ main_url_wo_http() . $profile->username }}</a> --}}
                                @if ($profile->bio != '')
                                    <div class="bio-text">
                                        <p class="bio-title-1"><span class="bio-title">Bio</span></p>
                                        <p id="bio_change" class="bio">{!! nl2br($profile->bio) !!}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <p id="username" class="hidden">
                    {{ main_url_wo_http() . $profile->username }}
                </p>
            </div>
            <div class="d-flex-- flex-row-- bd-highlight-- justify-content-center-- opac-3-- d-none">
                <!--<a id="add-to" class="btn btn-primary add-btn edit m-r" href="javascript:;"><b>Kontakt hinzufÃ¼gen</b></a> -->
                <a id="edit" data-toggle="modal" data-target="#myModal" class="btn btn-primary add-btn edit m-l"
                    href="javascript:;"><b>Connecten</b></a>
            </div>

            <div class="profile-section">
                <div class="user-details-block" style="background-color: transparent">
                    <div class="d-flex flex-row bd-highlight justify-content-center opac-3" id="button-div">
                        @if (isset($settings['show_connect']) && $settings['show_connect'] != 0)
                            <div class="{{ $settings['full_width_btn'] == 1 ? 'col-11' : 'col-6' }} lg-btns d-none">
                                <a data-show-connect="{{ $settings['show_connect'] }}" href="javascript:;"
                                    data-toggle="modal" data-target="#myModal"
                                    class="btn btn-primary red-button connect-btn"><img width="20"
                                        class="pull-left d-none" src="{{ uploads_url() . 'img/ic_connect.png' }}"><span
                                        style="display: inline-block;text-align: center; margin: 0 auto; white-space: pre-line;">{{ $language_text['connect'] }}</span></a>
                            </div>
                        @endif
                        @if (isset($settings['show_contact']) && $settings['show_contact'] != 0)
                            {{-- @if ($ContactCard > 0) --}}
                                <div class="{{ $settings['full_width_btn'] == 1 ? 'col-11' : 'col-6' }} lg-btns d-none">
                                    <a data-show-contact="{{ $settings['show_contact'] }}"
                                        href="{{ $blurOff == 'blurOn' ? main_url() . '/contact-card/' . encrypt($profile->id) . '?language=' . $language : '#' }}"
                                        class="btn btn-primary red-button save-contact" id="downloadButton"><img width="20"
                                            class="pull-left d-none"
                                            src="{{ uploads_url() . 'img/ic_contact_card.png' }}"><span
                                            style="display: inline-block;text-align: center; margin: 0 auto; white-space: pre-line;">{!! $language_text['save_contact'] !!}</span></a>
                                </div>
                            {{-- @endif --}}
                        @endif
                    </div>
                </div>
                @if ($profile->is_public == 2)
                    {{-- is_public --}}
                @elseif ($profile->is_public == 0 && $profile->profile_view == 'personal')
                    {{-- is_public --}}
                @elseif (!empty($BusinessInfo) && $BusinessInfo->is_public == 2 && $profile->profile_view == 'business')
                @else
                    @if (count($brand_profiles) > 0)
                        <div class="my-profiles brand-profiles">
                            <div class="flex-row wrp">
                                <h2 class="header-h2">
                                    @if (!empty($brand) && 1 != 1)
                                        <img src="{{ $brand->logo != '' ? image_url($brand->logo) : uploads_url() . 'img/company-logo.png' }}"
                                            width="32">
                                    @endif
                                    {{ $brand_name }}
                                </h2>
                                <div class="owl-carousel owl-theme horizontal-scroll-wrapper--">
                                    @foreach ($brand_profiles as $t => $row)
                                        <div class="item grid-square-normal-1" data-link-id="31">
                                            <a id="{{ $row->title_de }}" rel="" target="_blank"
                                                href="{{ $row->profile_link }}" class="social-titles">
                                                @if (filter_var($row->icon, FILTER_VALIDATE_URL) !== false)
                                                    <img class="shadow-- width-100" data-src="{{ $row->icon }}"
                                                        alt="{{ $row->title_de }}" src="{{ $row->icon }}">
                                                @else
                                                    <span class="svg-icon width-100 {{ $row->iconType }} {{ $settings['color_link_icons'] }}">{!! $row->icon !!}</span>
                                                @endif
                                                {{ $row->title_de }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="my-profiles">
                        <div class="flex-row wrp">
                            @if (count($profiles) > 0)
                                @foreach ($profiles as $row)
                                    <div class="grid-square-normal {{ $row->is_focused == 1 ? 'focused-profile' : '' }}"
                                        data-link-id="31">
                                        @if ($row->profile_link == 'popup-link')
                                            <a id="{{ $row->title_de }}" href="javascript:;"
                                                class="social-titles popup-link">
                                                @if (filter_var($row->icon, FILTER_VALIDATE_URL) !== false)
                                                    <img class="shadow width-100" data-id="{{ $row->id }}"
                                                        alt="{!! $row->title_de !!}" src="{{ $row->icon }}">
                                                @else
                                                    <span class="svg-icon width-100">{!! $row->icon !!}</span>
                                                @endif

                                                <div class="profile-title">
                                                    <h6>{{ $row->title_de }}</h6>
                                                </div>
                                            </a>
                                        @else
                                            <a rel="" target="_blank" href="{{ $row->profile_link }}"
                                                data-id="{{ $row->id }}" class="social-titles">
                                                @if (filter_var($row->icon, FILTER_VALIDATE_URL) !== false)
                                                    <img class="shadow width-100" alt="{!! $row->title_de !!}"
                                                        src="{{ $row->icon }}">
                                                @else
                                                    <span class="svg-icon width-100 {{ $row->iconType }} {{ $settings['color_link_icons'] }} ">{!! $row->icon !!}</span>
                                                @endif
                                                <div class="profile-title">
                                                    <h6>{{ $row->title_de }}</h6>
                                                    @if ($row->is_focused == 101)
                                                        <span>{{ $row->profile_value }}</span>
                                                    @endif
                                                </div>
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @if ($profile->is_public == 2)
            <div style="text-align: center; margin-top: 50px">
                <img alt="profile picture" width="40" src="{{ uploads_url() . 'img/padlock.png' }}">
                <p class="proxima-nova">{{ $language_text['private'] }}</p>
            </div>
        @elseif ($profile->is_public == 0 && $profile->profile_view == 'personal')
            <div style="text-align: center; margin-top: 50px">
                <img alt="profile picture" width="40" src="{{ uploads_url() . 'img/padlock.png' }}">
                <p class="proxima-nova">{{ $language_text['private'] }}</p>
            </div>
        @elseif (!empty($BusinessInfo) && $BusinessInfo->is_public == 2 && $profile->profile_view == 'business')
            <div style="text-align: center; margin-top: 50px">
                <img alt="profile picture" width="40" src="{{ uploads_url() . 'img/padlock.png' }}">
                <p class="proxima-nova">{{ $language_text['private'] }}</p>
            </div>
        @endif
        <div class="logo-tab">
            <div class="powered-by d-none">
                <strong> <a target="_blank" href="https://addmee.de/pages/app" class="shadow">Eigenes Profil
                        anlegen</a></strong>
            </div>
            <a href="https://www.addmee.de/">
                <img alt='{{ config('app.name', '') }}' class="addmee-logo" height="40"
                    src="{{ uploads_url() . 'img/addmee-logo.png' }}">
            </a>
            <div class="powered-by">
                <!-- <a class="patent" href="#">Patentiert <img alt='{{ config('app.name', '') }}' height="13" src="{{ uploads_url() . 'img/checked.png' }}"></a> -->
            </div>
        </div>
        @if ($blurOff == 'blurOn')
            <div class="modal fade" id="myModal" role="dialog" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-md" style="top: 12vh; width: 85%; margin: 0 auto;">
                    <div class="modal-content" style="text-align: center; border-radius: 15px">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">Ã</button>
                            <h4 class="modal-title form-body"
                                style="text-align: center; margin: 0 auto; font-weight: 700; width: 80%">
                                @if ($language == 'de')
                                    Mit
                                    {{ $profile->first_name == '' && $profile->last_name == '' ? $profile->name : $profile->first_name . ' ' . $profile->last_name }}
                                    vernetzen
                                @else
                                    Connect with
                                    {{ $profile->first_name == '' && $profile->last_name == '' ? $profile->name : $profile->first_name . ' ' . $profile->last_name }}
                                @endif
                            </h4>
                            <h4 class="modal-title form-img-body d-none"
                                style="text-align: center; margin: 0 auto; font-weight: 700; width: 80%">
                                {{ $language_text['success_msg'] }}
                            </h4>
                        </div>
                        {{-- <p class="mt-2">Teile deine Informationen mit
                            {{ $profile->first_name == '' && $profile->last_name == '' ? $profile->name : $profile->first_name . ' ' . $profile->last_name }}
                        </p> --}}
                        <div class="modal-body form-body">
                            <input type="text" autocomplete="first_name" id="first_name" value=""
                                class="form-control mb-3" placeholder="{{ $language_text['first_name'] }}*">
                            <p class="mb-3 text-left" id="first_name_info"></p>
                            <input type="text" autocomplete="last_name" id="last_name" value=""
                                class="form-control mb-3" placeholder="{{ $language_text['last_name'] }}*">
                            <p class="mb-3 text-left" id="last_name_info"></p>
                            <input type="email" autocomplete="email" id="email" value=""
                                class="form-control mb-3 " placeholder="{{ $language_text['your_email'] }}*">
                            <p class="mb-3 text-left" id="email_info"></p>
                            <input type="tel" autocomplete="tel" id="number"  pattern="[0-9]*" value=""
                                class="form-control mb-3" placeholder="{{ $language_text['your_phone_number'] }}*" onkeydown="return (event.key >= '0' && event.key <= '9') || event.key === '+' || event.key === 'Backspace' || event.key === 'Delete' || event.key === 'Tab' || event.key === 'Escape' || event.key === 'Enter'">
                            <input type="hidden" id="user_id" value="{{ $profile->id }}">
                            <p class="mb-3 text-left" id="number_info"></p>

                            <input type="text" autocomplete="company" id="company" value=""
                                class="form-control mb-3" placeholder="{{ $language_text['company'] }}">
                            <p class="mb-3 text-left" id="company"></p>
                            <textarea id="note" value="" class="form-control mb-3" placeholder="{{ $language_text['your_note'] }}"
                                rows="2"></textarea>
                            <p class="mb-3 text-left" id="note_info"></p>
                            <div data-style="display: none">
                                <input type="checkbox" class="checkboxcontact" id="checkboxcontact"
                                    style="transform: scale(1.3); margin-right: 5px;" name="bIsStatPrivate">
                                <span class="pp-tg">
                                    {!! $language_text['privacypolicy'] !!}</span>
                            </div>
                        </div>
                        <div class="modal-footer form-body" style="border: 0px">
                            <button type="button" disabled onClick="addPerson()"
                                class="btn btn-default my-btn my-connect-btn">{{ $language_text['your_connect'] }}</button>
                        </div>
                        <div class="modal-body d-none form-img-body">
                            <img src="{{ asset_url() . 'admin/img/ic_gif_sucess.gif' }}" width="100" alt="">
                        </div>
                        <div class="modal-body">
                            <p class="mb-3 text-center" id="msg_info"></p>
                            <p class="mb-3 text-center bold form-img-body d-none">{{ $language_text['email_was_sent'] }}
                            </p>
                            <p class="mb-3 text-center" id="hubspotContactMessage"></p>
                            <button type="button" class="btn btn-default mb-2 pull-right form-img-body d-none"
                                data-dismiss="modal"
                                style="color: #212529;font-weight:bold;background-color: transparent;">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <script type="text/javascript">
            var relative_path = '';
            var site_url = '';
            var xhr;
            var capture_lead = "{{ isset($settings['capture_lead']) ? $settings['capture_lead'] : 0 }}";

            $(document).ready(function() {
                $('#checkboxcontact').change(function() {
                    if (this.checked) {
                        $('.my-btn').prop('disabled', false);
                    } else {
                        $('.my-btn').prop('disabled', true);
                    }
                });

                if (capture_lead == 1) {
                    $('#button-div .connect-btn').click();
                }
            });

            function addPerson() {
                $('.my-connect-btn').prop('disabled', true);
                var name = $.trim($('#name').val());
                var first_name = $.trim($('#first_name').val());
                var last_name = $.trim($('#last_name').val());
                var email = $.trim($('#email').val());
                var number = $.trim($('#number').val());
                var company = $.trim($('#company').val());
                var note = $.trim($('#note').val());
                var user_id = $.trim($('#user_id').val());
                $('#hubspotContactMessage').empty();
                if (first_name == '') {
                    $('#checkboxcontact').prop('checked', false);
                    $('#first_name_info').html(
                            '<span class="alert alert-danger">{{ $language_text['first_name_alert'] }}</span>')
                        .fadeIn(150).delay(5000).fadeOut(150);
                    return false;
                }

                // if (!/^[a-zA-ZÄäÖöÜüß\s]+$/.test(first_name)) {
                //     $('#checkboxcontact').prop('checked', false);
                //     $('#first_name_info').html('<span class="alert alert-danger">{{ $language_text['first_name_validation_alert'] }}</span>')
                //     .fadeIn(150).delay(5000).fadeOut(150);
                //     return false;
                // }

                if (last_name == '') {
                    $('#checkboxcontact').prop('checked', false);
                    $('#last_name_info').html(
                            '<span class="alert alert-danger">{{ $language_text['last_name_alert'] }}</span>')
                        .fadeIn(150).delay(5000).fadeOut(150);
                    return false;
                }

                 // Check Last name for blank input
                //  if (!/^[a-zA-ZÄäÖöÜüß\s]+$/.test(last_name)) {
                //     $('#checkboxcontact').prop('checked', false);
                //     $('#last_name_info').html('<span class="alert alert-danger">{{ $language_text['last_name_validation_alert'] }}</span>')
                //     .fadeIn(150).delay(5000).fadeOut(150);
                //     return false;
                // }


                if (email == '') {
                    $('#checkboxcontact').prop('checked', false);
                    $('#email_info').html('<span class="alert alert-danger">{{ $language_text['email_alert'] }}</span>')
                        .fadeIn(150)
                        .delay(5000).fadeOut(150);
                    return false;
                }

                 // Check for Email valid Format
                 if (!/^\S+@\S+\.\S+$/.test(email)) {
                    $('#checkboxcontact').prop('checked', false);
                    $('#email_info').html('<span class="alert alert-danger">{{ $language_text['email_validation_alert'] }}</span>')
                    .fadeIn(150).delay(5000).fadeOut(150);
                    return false;
                }

                if (number == '') {
                    $('#checkboxcontact').prop('checked', false);
                    $('#number_info').html('<span class="alert alert-danger">{{ $language_text['phone_alert'] }}</span>')
                        .fadeIn(150)
                        .delay(5000).fadeOut(150);
                    return false;
                }

                if (!/^[+-]?\d+$/.test(number)) {
                    $('#checkboxcontact').prop('checked', false);
                    $('#number_info').html('<span class="alert alert-danger">{{ $language_text['phone_validation_alert'] }}</span>')
                        .fadeIn(150)
                        .delay(5000).fadeOut(150);
                    return false;
                }


                // if (note == '') {
                //     $('#note_info').html('<span class="alert alert-danger">{{ $language_text['note_alert'] }}</span>').fadeIn(
                //             150)
                //         .delay(5000).fadeOut(150);
                //     return false;
                // }

                number = number.replace('+', '%2B')
                var data = 'first_name=' + first_name + '&last_name=' + last_name + '&email=' + email + '&phone_no=' + number +
                    '&company=' + company + '&note=' + note +
                    '&user_id=' +
                    user_id + '&_token={{ csrf_token() }}';
                $('#msg_info').html('<span class="alert alert-success">Processing...</span>').fadeIn(150);
                $.ajax({
                    type: "POST",
                    url: '{{ main_url() }}/api/user_note',
                    dataType: "json",
                    data: data,
                    success: function(response) {
                        console.log(response.success);
                        if (response.success) {
                            if (response.hasOwnProperty('hubspotContactMessage')) {
                                $('#hubspotContactMessage').html(
                                    '<span class="alert alert-success">' + response.hubspotContactMessage +
                                    '</span>'
                                ).fadeIn(150);
                            } else {
                                $('#hubspotContactMessage').hide();
                            }
                            //$('button.close').click();

                            $('#msg_info').html('').fadeOut(150);
                            $('#first_name').val('');
                            $('#last_name').val('');
                            $('#email').val('');
                            $('#number').val('');
                            $('#company').val('');
                            $('#note').val('');
                            $('.form-body').addClass('d-none')
                            $('.form-img-body').removeClass('d-none')
                          }
                        //location.reload();
                        //$('#tr_'+de_utoa(id)+'').remove();
                        else {
                            $('#msg_info').html('<span class="alert alert-danger">Error.</span>').fadeIn(
                                150).delay(5000).fadeOut(150);
                        }
                    },
                    error: function() {
                        alert("Please add valid email address.");
                    //    $('#checkboxcontact').prop('checked', false);
                    //         $('#email_info').html('<span class="alert alert-danger">{{ $language_text['email_alert'] }}</span>')
                    //     .fadeIn(150).delay(5000).fadeOut(150);
                    //     $('#msg_info').html('');
                    //     return false;
                    }
                });
            }

            function hideModal() {
                console.log('close modal')
                // $('#myModal').modal('hide')
                $('.form-body').removeClass('d-none')
                $('.form-img-body').addClass('d-none')
            }

            function reset_offset(val) {
                offset = 0;
                $('#load-more').fadeOut(150);
            }

            function utoa(str) {
                return window.btoa(window.btoa(window.btoa(window.btoa(window.btoa(window.btoa(window.btoa(unescape(
                    encodeURIComponent(str)))))))));
            }

            $(document).ready(function(e) {
                var w = $('#content-section').width()
                // $('.user-img').css('height', '198px');
                $('.user-img').css('height', 'auto');
                getLocation();
            });

            function error() {}

            function getLocation() {
                return;
                if (navigator.geolocation) {
                    var options = {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    };
                    navigator.geolocation.getCurrentPosition(showPosition, error, options);
                } else {
                    x.innerHTML = "Geolocation is not supported by this browser.";
                }
            }

            function showPosition(position) {
                return;
                //console.log(position.coords.latitude, position.coords.longitude);
                var data = 'user_id={{ $profile->id }}&latitude=' + position.coords.latitude + '&longitude=' + position.coords
                    .longitude;
                $.ajax({
                    type: "GET",
                    url: '{{ main_url() }}/api/tap_view/',
                    dataType: 'json',
                    data: data,
                    success: function(response) {

                        console.log(position);
                    }
                });
            }

            $(document).ready(function() {
                $('#button-div .lg-btns').removeClass('d-none', {
                    duration: 500
                })

                $('.popup-link').on('click', function() {
                    $('.popup-section').css({
                        display: 'block',
                        width: 400 + 'px'
                    });
                });

                $('.close-btn').on('click', function() {
                    $('#success').fadeOut(100)
                    $('.icon-img').fadeOut(200)
                });

                $('#myModal').on('hidden.bs.modal', function() {
                    // Modal is closed
                    $('#hubspotContactMessage').empty();
                    $('#checkboxcontact').prop('checked', false);
                    setTimeout(function() {
                        hideModal()
                    }, 500);
                });
            })

            let language = 'en'
            let isQueryParam = '<?php echo $isQueryParam; ?>'
            console.log('isQueryParam: ', isQueryParam)
            if (window.navigator.language) {
                language = window.navigator.language.substr(0, 2)
                if (language != 'en' && language != 'de') {
                    language = 'en'
                }
            }

            // language = 'de'
            // if (language != 'en' && language != '<?php echo $language; ?>') {
            if (isQueryParam == false && language != '<?php echo $language; ?>') {
                $.ajax({
                    type: "POST",
                    url: '{{ main_url() }}/api/update_browser_language',
                    dataType: "json",
                    data: 'language=' + language,
                    success: function(response) {

                        let reloaded = localStorage.getItem('reloaded');
                        let _language = localStorage.getItem('language');
                        let _date = localStorage.getItem('date');
                        let cur_date = "{{ date('h') }}";
                        if (cur_date != _date) {
                            localStorage.setItem('reloaded', null);
                            localStorage.setItem('language', null);
                        }

                        console.log('reload val', reloaded, cur_date, _date, _language)
                        if (reloaded == undefined || reloaded == null || _language != language) {
                            localStorage.setItem('reloaded', true);
                            localStorage.setItem('language', language);
                            localStorage.setItem('date', "{{ date('h') }}");
                            location.reload();
                        }
                        //$('#tr_'+de_utoa(id)+'').remove();
                    },
                    error: function() {
                        // alert("Sorry, The requested property could not be found.");
                    }
                });
            }
            console.log('language', language, '<?php echo $language; ?>')
            if (language == 'de') {
                $(document).ready(function() {
                    $('.red-button').css('display', 'flex');
                    // var height = $('#button-div .save-contact').height();
                    // console.log(height)
                    // height = height + parseInt(16)
                    // $('#button-div .connect-btn span, #button-div .save-contact span').css('white-space', 'pre-line');
                    // $('#button-div .connect-btn').css('height', height + 'px');
                    // $('#button-div .connect-btn span, #button-div .save-contact span').css('margin-left', '10px');
                });
            }

            document.getElementById("downloadButton").addEventListener("click", function(event) {

            var confirmed = confirm("{{ $language_text['confirmation_msg'] }}");

            if (!confirmed) {
            event.preventDefault();
            }
            });
</script>

        {{-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC4sckpx0c1d9JOug9jJg_URJI_wgVSFVw"></script> --}}

        {{-- popup --}}
        <style>
            .pp-tg,
            .pp-tg a {
                color: #000;
                font-size: 15px;
            }

            .pp-tg a {
                text-decoration: underline;
            }

            /* .user-img {
                                                                    background-size: cover
                                                                } */

            .popup-section {
                position: fixed;
                z-index: 999;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                min-height: 300px;
                width: 400px;
                background-color: #fff;
                margin: 40px auto;
                box-shadow: 10px 10px 28px 1px rgba(0, 0, 0, 0.75);
                border-radius: 15px;
                display: none;
                max-width: 90%;
                padding: 20px;
            }

            .text-center {
                text-align: center
            }

            .popup-section h1 {
                font-style: normal;
                font-weight: 600;
                font-size: 20px;
                line-height: 29px;
                text-align: center;
                color: #080808;
            }

            .popup-section .btn {
                max-width: 80%;
                font-style: normal;
                font-weight: 700;
                font-size: 15px;
                line-height: 18px;
                display: flex;
                justify-content: center;
                text-align: center;
                border-radius: 17px !important;
                padding: 10px 50px !important;
                margin: 5px auto;
            }

            .popup-section .success-btn {
                color: #185698;
                border: 2px solid #185698 !important;
            }

            .popup-section .success-btn:hover {
                color: #fff;
                background-color: #185698 !important;
            }

            textarea {
                font-family: Arial
            }

            .owl-carousel .owl-item img {
                border-radius: 10px !important;
            }
        </style>
        <section class="popup-section" id="success">
            <div class="icon">
                <h1 class="alert-heading">{{ $language_text['qrcode_title'] }}</h1>
            </div>
            <div class="popup-content text-center">
                <img src="{{ uploads_url() . 'qrcodes/' . $profile->id . '.svg' }}"
                    style="max-width: 70%;margin: 15px 0;" alt="">
                <br><small><b>{{ $language_text['iphone_qrcode_note'] }}</b><br>
                    {{ $language_text['iphone_qrcode_note_text'] }}</small>
                <br><small><b>{{ $language_text['android_qrcode_note'] }}</b><br>
                    {{ $language_text['android_qrcode_note_text'] }}</small>
            </div>
            <div class="popup-btns mb-4">
                <a class="btn border-theme success-btn close-btn" href="javascript:;">{{ $language_text['Close'] }}</a>
            </div>
        </section>
        <script>
            $(document).ready(function() {
                $('.owl-carousel').owlCarousel({
                    loop: false,
                    margin: 10,
                    dots: false,
                    responsiveClass: true,
                    responsive: {
                        0: {
                            items: 4,
                            nav: true,
                            loop: false
                        },
                        600: {
                            items: 4,
                            nav: true,
                            loop: false
                        },
                        1000: {
                            items: 4,
                            nav: true,
                            loop: false
                        }
                    }
                });
            })
        </script>
    @endsection
