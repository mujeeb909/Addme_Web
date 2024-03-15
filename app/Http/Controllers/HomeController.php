<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as HttpRequest;
use App\Models\ContactCard;
use App\Models\User;
use App\Models\UserNote;
use App\Models\BusinessInfo;
use App\Models\BusinessUser;
use App\Models\CustomerProfile;
use App\Models\UserTemplate;
use App\Models\TemplateAssignee;
use App\Models\UniqueCode;
use App\Models\UserSettings;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Cache;

class HomeController extends Controller
{
    public $page_title = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->page_title = ucwords(config('app.name', 'AddMee'));
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        return view('home', ['page_title' => $this->page_title, 'profile' => []]);
    }

    public function app_store()
    {
        return view('app_store', ['page_title' => $this->page_title, 'profile' => []]);
    }

    public function profile(HttpRequest $request)
    {
        // $language = (strtolower($Country) == 'germany') ? 'de' : 'en';
        // session(['language' => 'en']);
        // Session::set('language', 'en');
        // $language = Session::get('language');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $isQueryParam = false;
        if (isset($_GET['language']) && trim($_GET['language']) != '') {
            $language = trim($_GET['language']);
            $isQueryParam = true;
        } else {
            $language = $_SESSION['language'] = 'de'; //session('language');
            // $language =isset($_SESSION['language']) ? $_SESSION['language'] : 'de'; //session('language');
        }
        // pre_print($language);

        // $viewData = Cache::remember('profile-' . $request->username, 1, function () use ($request, $language) {

        $no_record_found = false;
        $brand_name = '';
        $user = $Obj = User::where('username', $request->username);
        if ($user->count() == 0) {
            // die('Error: Invalid username');
            $str_code = $request->username;
            $code = $UniqueCode = UniqueCode::where('str_code', $str_code);
            if ($code->count() == 0) {
                // die('Error: Invalid request.');
                $no_record_found = true;
            }

            if ($no_record_found == false) {

                $code = $code->first();

                if (date('Y-m-d', strtotime($code->expires_on)) == date('Y-m-d')) {
                    $code->activated = 0;
                    $code->user_id = 0;
                    $code->updated_by = -1;
                    $code->save();
                }

                if ($code->user_id == 0) {
                    // die('Error: Invalid request.');
                    $no_record_found = true;
                }

                if ($code->status == 0) {
                    // die('Error: Invalid request.');
                    $no_record_found = true;
                }

                $Obj = User::where('id', $code->user_id);
                if ($Obj->count() == 0) {
                    // die('Error: Invalid request.');
                    $no_record_found = true;
                } else {
                    $brand_name = $code->brand;
                }
            }
        }

        $user = $Obj = $Obj->first();
        $user_group_id = 0;
        if (isset($user->status) && $user->status == 0) {
            $no_record_found = true;
            $user_group_id = $user->user_group_id;
        }

        if ($no_record_found) {
            if (strtolower(config("app.name", "")) == 'addmee') {
                if ((isset($UniqueCode) && $UniqueCode->count() != 0 && $code->device != '' && in_array($code->device, ['c', 'cc'])) || $user_group_id == 3) {
                    $username = 'addmeebusiness';
                    //
                } else {
                    $username = 'addmee';
                }
            } else {
                $username = 'tapmee';
            }

            $user = $Obj = User::where('username', $username);

            if ($user->count() == 0) {
                die('Invalid Request.');
            } else {
                return redirect()->away(main_url() . '/' . $username);
                // return ['hasRedirect' => true, 'redirectURL' => main_url() . '/' . $username];
            }
        }

        // user change here
     //   $template = anyTemplateAssigned($user->id);
        $template = anyTemplateAssignedProfile($user->id);
        $user = UserObj($user, 0, $template);

        $is_business = $user->profile_view == 'business' ? 1 : 0;
        $ContactCard = ContactCard::where('user_id', $user->id)->where('is_business', $is_business)->where('customer_profile_ids', '!=', '0')->count();
        $BusinessInfo = BusinessInfo::where('user_id', $user->id)->first();

        $BusinessUser = BusinessUser::where('user_id', $user->id);
        if ($BusinessUser->count() > 0) {
            $BusinessUser = $BusinessUser->first();
            $parent_id = $BusinessUser->parent_id;
            if ($parent_id != 0) {
                $parentUser = User::where('id', $parent_id);
                if ($parentUser->count() > 0) {
                    $parentUser = $parentUser->first();
                    if ($parentUser->status == 0) {
                        return redirect()->away(main_url() . '/addmeebusiness');
                        // return ['hasRedirect' => true, 'redirectURL' => main_url() . '/addmeebusiness'];
                    }
                }
            }
        }

        $language = ($language == '') ? 'en' : $language;
        $language = !in_array($language, ['de', 'en']) ? 'en' : $language;
        $profiles = $this->meta($request, $Obj, '', $language);
        $brand_profiles = $brand_name != '' ? $this->meta($request, $Obj, $brand_name, $language) : [];
        $brand = [];
        if ($brand_name != '') {
            $brand = User::where('username', $brand_name)->first();
            if (!empty($brand)) {
                $brand_name = isset($brand->company_name) ? $brand->company_name : '';
                $brand_name = trim($brand_name) == '' ? $brand->first_name . ' ' . $brand->last_name : $brand_name;
                $brand_name = trim($brand_name) == '' ? $brand->name : $brand_name;
                $brand_name = trim($brand_name) == '' ? $brand->username : $brand_name;
            }
        }
        //  pre_print($profiles);



        $blur = 'blurOn';
        if (!empty($Obj) && $Obj->is_public == 2) {
            $blur = 'blurOff';
        } else if (!empty($Obj) && $Obj->is_public == 0 && $Obj->profile_view == 'personal') {
            $blur = 'blurOff';
        } else if (!empty($BusinessInfo) && $BusinessInfo->is_public == 0 && $Obj->profile_view == 'business') {
            $blur = 'blurOff';
        }

        if ($blur != 'blurOff') {
            // pre_print($profiles);
            // if (!empty($profiles) && isset($profiles[0]->open_direct) && $profiles[0]->open_direct == 1) {
            if (!empty($profiles) && $user->open_direct == 1) {
                return redirect()->away($profiles[0]->profile_link);
                // return ['hasRedirect' => true, 'redirectURL' => $profiles[0]->profile_link];
            }
        }

        // pre_print($user);
        $has_subscription = chk_subscription($Obj);
        if ($has_subscription['success'] == false) {
            unset($user->company_address);
        }

        $full_name = $user->first_name == '' && $user->last_name == '' ? $user->name : $user->first_name . ' ' . $user->last_name;
        $this->page_title = $full_name . ' | ' . ucwords(config('app.name', 'AddMee'));
        // $UserSettings = UserSettings::where('user_id', $user->id)->first();
        $UserSettings = userSettingsObj_Old($user->id, $template);
       // pre_print($UserSettings);
        if (count($UserSettings) > 0) {
            $UserSettings = (object) $UserSettings;
            $settings = [
                'bg_color' => notEmpty($UserSettings->bg_color) ? $UserSettings->bg_color : 'rgba(255, 255, 255, 1)',
                'bg_image' => notEmpty($UserSettings->bg_image) ? icon_url() . $UserSettings->bg_image : NULL,
                'btn_color' => notEmpty($UserSettings->btn_color) ? $UserSettings->btn_color : 'rgba(0, 0, 0, 1)',
                'text_color' => notEmpty($UserSettings->text_color) ? $UserSettings->text_color : 'rgba(17, 24, 3, 1)',
                'color_link_icons' => notEmpty($UserSettings->color_link_icons) ? $UserSettings->color_link_icons : 0,
                'photo_border_color' => notEmpty($UserSettings->photo_border_color) ? $UserSettings->photo_border_color : 'rgba(255, 255, 255, 1)',
                'section_color' => notEmpty($UserSettings->section_color) ? $UserSettings->section_color : 'rgba(255, 255, 255, 0)',
                'show_contact' => $UserSettings->show_contact,
                'show_connect' => $UserSettings->show_connect,
                'capture_lead' => $UserSettings->capture_lead,
            ];

            // if ($settings['bg_color'] != '#fff' && $settings['bg_color'] != '#ffffff') {
            // list($r, $g, $b) = sscanf($settings['bg_color'], "#%02x%02x%02x");
            // $settings['bg_color'] = "rgba($r, $g, $b, 1)";
            // $settings['innser_section_bg_color'] = "rgba($r, $g, $b, 1)";
            // }

            if (strtolower($settings['section_color']) != '#fff' && strtolower($settings['section_color']) != '#ffffff') {
                list($r, $g, $b) = sscanf($settings['section_color'], "#%02x%02x%02x");
                // $settings['focused_profile_color'] = "rgba(255,255,255,0.2)";
            }

            if ($settings['text_color'] == '') {
                unset($settings['text_color']);
            }
        } else {
            $settings = ['bg_color' => '#f8f8f8', 'btn_color' => '#000000', 'photo_border_color' => '#ffffff', 'section_color' => '#ffffff', 'bg_image' => '', 'show_contact' => 1, 'show_connect' => 1, 'capture_lead' => 0];
        }

        $settings['focused_profile'] = isset($settings['section_color']) ? $settings['section_color'] : '#FFFFFF';
        $settings['focused_profile_bg'] = 'rgba(255, 255, 255, 0.2)';
        if (strtolower($settings['focused_profile']) == '#ffffff' || strtolower($settings['focused_profile']) == '#fff') {
            $settings['focused_profile_bg'] = 'rgb(248, 250, 252)';
        }

        if (isset($settings['text_color']) && ($settings['text_color'] == '#000000' || $settings['text_color'] == 'rgba(17, 24, 3, 1)')) {
            unset($settings['text_color']);
        }

        $settings['full_width_btn'] = 0;
        if ((isset($settings['show_contact']) && $settings['show_contact'] == 0) || (isset($settings['show_contact']) && $settings['show_connect'] == 0)) {
            $settings['full_width_btn'] = 1;
        }
        // pre_print($settings);

        $viewData = ['profiles' => $profiles, 'brand_profiles' => $brand_profiles, 'BusinessInfo' => $BusinessInfo, 'profile' => $user, 'blurOff' => $blur, 'brand_name' => $brand_name, 'brand' => $brand, 'ContactCard' => $ContactCard, 'page_title' => $this->page_title, 'language' => $language, 'settings' => $settings, 'hasRedirect' => false];
        // return $viewData; //view('profiles', $viewData);
        // });
        // pre_print($viewData);
        // if (isset($viewData['hasRedirect']) && $viewData['hasRedirect'] == true) {
        //     return redirect()->away($viewData['redirectURL']);;
        // }

        $iPod = stripos($_SERVER['HTTP_USER_AGENT'], "iPod");
        $iPhone = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
        $iPad = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");
        $webOS   = stripos($_SERVER['HTTP_USER_AGENT'], "webOS");
        $Android = stripos($_SERVER['HTTP_USER_AGENT'], "Android");

        $isIOS = false;
        if ($iPod || $iPhone) {
            //browser reported as an iPhone/iPod touch -- do something here
            $isIOS = true;
        } else if ($iPad) {
            //browser reported as an iPad -- do something here
            $isIOS = true;
        }

        $viewData['language_text'] = $this->lang($language);
        $viewData['isIOS'] = $isIOS;
        $viewData['Android'] = $Android;
        $viewData['isQueryParam'] = $isQueryParam;
        //pre_print($viewData);
        return view('profiles', $viewData);
    }

    private function lang($language)
    {
        if ($language == 'de') {
            $list['save_contact'] = 'Kontakt speichern';
            $list['connect'] = 'Vernetzen';
            $list['private'] = 'Dieses Profil ist privat';
            $list['your_name'] = 'Vollständiger Name';
            $list['first_name'] = 'Vorname';
            $list['last_name'] = 'Nachname';
            $list['your_email'] = 'E-Mail';
            $list['your_phone_number'] = 'Telefonnummer';
            $list['your_note'] = 'Eine Notiz hinterlassen';
            $list['your_connect'] = 'Vernetzen';
            $list['name_alert'] = 'Bitte Name eingeben.';
            $list['first_name_alert'] = 'Bitte geben Sie Ihren Vornamen ein.';
            $list['last_name_alert'] = 'Bitte geben Sie ihren Nachnamen ein.';
            $list['email_alert'] = 'Bitte E-Mail eingeben.';
            $list['phone_alert'] = 'Bitte Telefonnummer eingeben.';
            $list['note_alert'] = 'Bitte Notiz eingeben.';
            $list['success_msg'] = 'Erfolgreich vernetzt!';
            $list['at'] = 'bei';
            $list['privacypolicy'] = 'Ich habe die <a href="https://www.addmee.de/pages/privacy-policy-addmee-app" target="_blank">Datenschutzbestimmungen</a> zur Kenntnis genommen und bin mit ihnen einverstanden.';
            $list['qrcode_title'] = 'Zum WLAN-Verbinden QR-Code scannen';
            $list['iphone_qrcode_note'] = 'Hinweis iPhone Nutzer';
            $list['iphone_qrcode_note_text'] = 'Zum WLAN-Verbinden QR-Code für 2 Sek. gedrückt halten und im Popup-Menü auswählen.';
            $list['android_qrcode_note'] = 'Hinweis andere Nutzer';
            $list['android_qrcode_note_text'] = 'QR-Code kann zum WLAN-Verbinden mit anderen Personen geteilt werden.';
            $list['Close'] = 'Schließen';
            $list['email_was_sent'] = "Zur Bestätigung wurde eine Email versendet";
            $list['company'] = "Unternehmen";
            $list['contact_pushed'] = "Kontakt wurde erfolgreich in Hubspot übertragen";
            $list['first_name_validation_alert'] = 'Der Vorname darf keine Ziffern oder Sonderzeichen enthalten.';
            $list['last_name_validation_alert'] = 'Der Nachname darf keine Ziffern oder Sonderzeichen enthalten.';
            $list['email_validation_alert'] = 'Bitte trage eine gültige Email-Adresse ein';
            $list['phone_validation_alert'] = 'Bitte trage einen gültige Telefonnummer ein';
            $list['confirmation_msg'] = "Nachdem mit ‘OK‘ bestätigt wurde, wird eine VCF Datei heruntergeladen. Diese bitte öffnen, um die Kontaktkarte im Telefonbuch abzuspeichern.";
        } else {
            $list['save_contact'] = 'Save Contact';
            // $list['save_contact'] = 'Kontakt<br>speichern';
            $list['connect'] = 'Connect';
            $list['private'] = 'This profile is private';
            $list['your_name'] = 'Full name';
            $list['first_name'] = 'First Name';
            $list['last_name'] = 'Last Name';
            $list['your_email'] = 'Email';
            $list['your_phone_number'] = 'Phone Number';
            $list['your_note'] = 'Leave a note';
            $list['your_connect'] = 'Connect';
            $list['name_alert'] = 'Please enter your name.';
            $list['first_name_alert'] = 'Please enter your first name.';
            $list['last_name_alert'] = 'Please enter your last name.';
            $list['email_alert'] = 'Please enter your email.';
            $list['phone_alert'] = 'Please enter your phone number.';
            $list['note_alert'] = 'Please enter a note.';
            $list['success_msg'] = 'Successfully connected!';
            $list['at'] = 'at';
            $list['privacypolicy'] = 'I have read and agree with the <a href="https://www.addmee.de/pages/privacy-policy-addmee-app" target="_blank">privacy policy</a>.';
            $list['qrcode_title'] = 'Scan QR code to connect to WiFi';
            $list['iphone_qrcode_note'] = 'Note iPhone users';
            $list['iphone_qrcode_note_text'] = 'To connect to WiFi, press and hold QR code for 2 sec. and select from pop-up menu.';
            $list['android_qrcode_note'] = 'Note Android users';
            $list['android_qrcode_note_text'] = 'QR code can be shared with other people to connect with WiFi.';
            $list['Close'] = 'Close';
            $list['email_was_sent'] = "An email was sent to confirm";
            $list['company'] = "Company";
            $list['contact_pushed'] = "Contact pushed into hubspot successfully";
            $list['phone_validation_alert'] = 'Please add valid phone number';
            $list['email_validation_alert'] = 'Please add valid email address';
            $list['last_name_validation_alert'] = 'Last name should not contain numbers or special characters.';
            $list['first_name_validation_alert'] = 'First name should not contain numbers or special characters.';
            $list['confirmation_msg'] = "After clicking on ‘OK‘, a VCF file will be downloaded. Please open this file to store the contact card in your phone book.
            OK Cancel";

        }

        return $list;
    }

    public function privacy()
    {
        return view('privacy', ['page_title' => $this->page_title, 'profile' => []]);
    }

    public function terms()
    {
        return view('terms', ['page_title' => $this->page_title, 'profile' => []]);
    }

    public function contact_card(HttpRequest $request)
    {
        $iPod    = stripos($_SERVER['HTTP_USER_AGENT'], "iPod");
        $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
        $iPad    = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");
        $Android = stripos($_SERVER['HTTP_USER_AGENT'], "Android");
        $webOS   = stripos($_SERVER['HTTP_USER_AGENT'], "webOS");

        $language = 'en';
        if (isset($_GET['language']) && trim($_GET['language']) != '') {
            $language = trim($_GET['language']);
        }
        $language = !in_array($language, ['de', 'en']) ? 'en' : $language;

        $isIOS = false;
        //do something with this information
        if ($iPod || $iPhone) {
            //browser reported as an iPhone/iPod touch -- do something here
            $isIOS = true;
        } else if ($iPad) {
            //browser reported as an iPad -- do something here
            $isIOS = true;
        } else if ($Android) {
            //browser reported as an Android device -- do something here
        } else if ($webOS) {
            //browser reported as a webOS device -- do something here
        }

        $user = $Obj = User::where('id', decrypt($request->id))->first();
        $is_business = $user->profile_view == 'business' ? 1 : 0;
        $ContactCard = ContactCard::where('user_id', $user->id)->where('is_business', $is_business);

        if ($ContactCard->count() > 0) {
            $ContactCard = $ContactCard->first();
            $ContactCard = explode(',', $ContactCard->customer_profile_ids);
        } else {
            $ContactCard = [];
        }

        $profiles = $this->meta($request, $Obj, '', $language);
        //pre_print($profiles);

        $name = explode(' ', $user->name);
        $first_name = $user->first_name; //$name[0];
        $last_name = $user->last_name; //trim(str_replace($first_name, '', $user->name));

        if ($first_name == '' && $last_name == '') {
            $first_name = $name[0];
            $last_name = trim(str_replace($first_name, '', $user->name));
        }

        // $first_name = str_replace('ä', 'ae', $first_name);
        // $first_name = str_replace('ö', 'oe', $first_name);
        // $first_name = str_replace('ü', 'ue', $first_name);
        // $last_name = str_replace('ä', 'ae', $last_name);
        // $last_name = str_replace('ö', 'oe', $last_name);
        // $last_name = str_replace('ü', 'ue', $last_name);

        $file_name = trim($first_name . ' ' . $last_name);
        $file_name = str_replace('ä', 'ae', $file_name);
        $file_name = str_replace('ö', 'oe', $file_name);
        $file_name = str_replace('ü', 'ue', $file_name);
        $file_name = preg_replace("/[^A-Za-z0-9.]/", '', $file_name);

        $vCard  = "BEGIN:VCARD\n";
        $vCard .= "VERSION:3.0\n";
        if ($isIOS == true) {
            $vCard .= "N:" . $last_name . ";" . $first_name . "\n";
        }
        $vCard .= "FN:" . trim($first_name . ' ' . $last_name) . "\n"; //full name
        $vCard .= "NAME:" . trim($first_name . ' ' . $last_name) . "\n";
        if ($user->designation != '') {
            $vCard .= "TITLE:" . $user->designation . "\n";
        }
        if ($user->company_name != '') {
            $vCard .= "ORG:" . $user->company_name . "\n";
        }
        //$vCard .= "FIRSTNAME:" . $first_name . "\n";
        //$vCard .= "LASTNAME:" . $last_name . "\n";
        $vCard .= "URL;TYPE=My " . config("app.name", "") . " Profile:" . main_url() . "/" . $user->username . "\n";
        if (!empty($profiles)) {
            foreach ($profiles as $profile) {
                if ($profile->profile_code == 'wifi') {
                    continue;
                }

                if (in_array($profile->id, $ContactCard)) {
                    if ($profile->profile_code == 'call' || $profile->profile_code == 'business_call' || $profile->profile_code == 'whatsapp') {
                        $profile->profile_code = ($profile->profile_code == 'business_call') ? 'business' : ($profile->profile_code == 'call' ? 'Mobile' : $profile->profile_code);
                        $vCard .= "TEL;TYPE=" . ucwords($profile->profile_code) . ":" . $profile->profile_value . "\n";
                    } else if ($profile->profile_code == 'text') {
                        $vCard .= "TEL;TYPE=" . ucwords($profile->profile_code) . ":" . $profile->profile_value . "\n";
                    } else if ($profile->profile_code == 'email') {
                        $vCard .= "EMAIL;INTERNET;TYPE=" . ucwords($profile->profile_code) . ":" . $profile->profile_value . "\n";
                    } else if ($profile->profile_code == 'www') {
                        $vCard .= "URL;TYPE=Website:" . $profile->profile_value . "\n";
                    } else if ($profile->profile_code == 'address') {
                        $vCard .= "ADR;TYPE=" . ucwords($profile->profile_code) . ":" . $profile->profile_value . "\n";
                    } else if ($profile->profile_code != 'contact-card') {
                        $vCard .= "URL;TYPE=" . ucwords($profile->title) . ":" . $profile->profile_link . "\n";
                    }
                }
            }
        }

        /*   if ($user->banner != '') {
            if (file_exists(icon_dir() . $user->banner)) {
                $getPhoto = file_get_contents(icon_dir() . $user->banner);
                $b64vcard = base64_encode($getPhoto);
                $b64mline = chunk_split($b64vcard, 74, "\n");
                $b64final = preg_replace('/(.+)/', ' $1', $b64mline);
                $photo  = $b64final;
                $vCard .= "PHOTO;ENCODING=b;TYPE=JPEG:";
                $vCard .= $photo . "\n";
            }
        } */

        if ($user->logo != '') {
            if (file_exists(icon_dir() . $user->logo)) {
                $getPhoto = file_get_contents(icon_dir() . $user->logo);
                $b64vcard = base64_encode($getPhoto);
                $b64mline = chunk_split($b64vcard, 74, "\n");
                $b64final = preg_replace('/(.+)/', ' $1', $b64mline);
                $photo  = $b64final;
                $vCard .= "PHOTO;ENCODING=b;TYPE=JPEG:";
                $vCard .= $photo . "\n";
            }
        }

        //$vCard .= "ORG;CHARSET=utf-8:\n"; //Dieses Profil ist privat.
        $has_subscription = chk_subscription($Obj);
        // pre_print(strtotime($user->note_visible_to) .'>='. strtotime(date('Y-m-d')));
        $currentDate =  Carbon::create(date('Y-m-d'));
        $note_visible_from = Carbon::create($user->note_visible_from);
        $note_visible_to = Carbon::create($user->note_visible_to);

        if (
            $has_subscription['success'] == true &&
            $user->note_description != NULL && $user->note_visible_from != NULL && $user->note_visible_to != NULL
            && $currentDate->lessThanOrEqualTo($note_visible_to)
            && $currentDate->greaterThanOrEqualTo($note_visible_from)
        ) {
            $vCard .= "NOTE;CHARSET=utf-8:Added by " . config("app.name", "") . ", " . date('M d, Y') . ' ' . $user->note_description . "\n";
        } else {
            $vCard .= "NOTE;CHARSET=utf-8:Added by " . config("app.name", "") . ", " . date('M d, Y') . "\n";
        }
        $vCard .= "END:VCARD";

        // header('Content-Type: text/vcard');
        $file_name = $file_name . '.vcf';
        // header('Content-Description: Download vCard');
        // header('Content-Type: text/vcard');
        // header('Content-Disposition: attachment; filename="' . $file_name . '"');
        // // header('Content-Length: ' . filesize($filePath));
        // header('Content-Transfer-Encoding: binary');
        // header('Expires: 0');
        // header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        // header('Pragma: public');
        // ob_clean();
        // flush();
        // $vCard = str_replace('ä', 'ae', $vCard);
        // $vCard = str_replace('ö', 'oe', $vCard);
        // $vCard = str_replace('ü', 'ue', $vCard);
        $filePath =  root_dir() . 'vcf/' . $file_name;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $fileHandle = fopen($filePath, 'c+');
        fwrite($fileHandle, $vCard);
        fclose($fileHandle);

        return Response::download($filePath, $file_name, [
            'Content-Type' => 'text/vcard',
        ]);
        // exit;
        // echo $vCard;
    }

    public function contact_lead(HttpRequest $request)
    {
        $iPod    = stripos($_SERVER['HTTP_USER_AGENT'], "iPod");
        $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
        $iPad    = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");
        $Android = stripos($_SERVER['HTTP_USER_AGENT'], "Android");
        $webOS   = stripos($_SERVER['HTTP_USER_AGENT'], "webOS");

        $language = 'en';
        if (isset($_GET['language']) && trim($_GET['language']) != '') {
            $language = trim($_GET['language']);
        }
        $language = !in_array($language, ['de', 'en']) ? 'en' : $language;

        $isIOS = false;
        //do something with this information
        if ($iPod || $iPhone) {
            //browser reported as an iPhone/iPod touch -- do something here
            $isIOS = true;
        } else if ($iPad) {
            //browser reported as an iPad -- do something here
            $isIOS = true;
        } else if ($Android) {
            //browser reported as an Android device -- do something here
        } else if ($webOS) {
            //browser reported as a webOS device -- do something here
        }



        $user = $Obj = UserNote::where('id', $request->id)->first();



        $name = explode(' ', $user->name);
        $first_name = $user->first_name; //$name[0];
        $last_name = $user->last_name; //trim(str_replace($first_name, '', $user->name));

        if ($first_name == '' && $last_name == '') {
            $first_name = $name[0];
            $last_name = trim(str_replace($first_name, '', $user->name));
        }

        // $first_name = str_replace('ä', 'ae', $first_name);
        // $first_name = str_replace('ö', 'oe', $first_name);
        // $first_name = str_replace('ü', 'ue', $first_name);
        // $last_name = str_replace('ä', 'ae', $last_name);
        // $last_name = str_replace('ö', 'oe', $last_name);
        // $last_name = str_replace('ü', 'ue', $last_name);

        $file_name = trim($first_name . ' ' . $last_name);
        $file_name = str_replace('ä', 'ae', $file_name);
        $file_name = str_replace('ö', 'oe', $file_name);
        $file_name = str_replace('ü', 'ue', $file_name);
        $file_name = preg_replace("/[^A-Za-z0-9.]/", '', $file_name);

        $vCard  = "BEGIN:VCARD\n";
        $vCard .= "VERSION:3.0\n";
        if ($isIOS == true) {
            $vCard .= "N:" . $last_name . ";" . $first_name . "\n";
        }
        $vCard .= "FN:" . trim($first_name . ' ' . $last_name) . "\n"; //full name
        $vCard .= "NAME:" . trim($first_name . ' ' . $last_name) . "\n";

        if ($user->job_tittle != '') {
            $vCard .= "TITLE:" . $user->job_tittle . "\n";
        }
        if ($user->company != '') {
            $vCard .= "ORG:" . $user->company . "\n";
        }


        if ($user->photo != '') {
            if (file_exists(icon_dir() . $user->photo)) {
                $getPhoto = file_get_contents(icon_dir() . $user->photo);
                $b64vcard = base64_encode($getPhoto);
                $b64mline = chunk_split($b64vcard, 74, "\n");
                $b64final = preg_replace('/(.+)/', ' $1', $b64mline);
                $photo  = $b64final;
                $vCard .= "PHOTO;ENCODING=b;TYPE=JPEG:";
                $vCard .= $photo . "\n";
            }
        }


       // $vCard .= "TEL;INTERNET;TYPE=" . ucwords($user->phone_no) . ":" . $user->phone_no . "\n";
       if($user->website){
        $vCard .= "URL;TYPE=Website:" . $user->website . "\n";
       }
       if($user->address){
        $vCard .= "ADR;TYPE=" . ucwords('Address') . ":" . $user->address . "\n";
       }
       if($user->phone_no){
        $vCard .= "TEL;TYPE=Mobile:" . $user->phone_no . "\n";
       }
       if($user->mobile_no_1){
       // $vCard .= "TEL;TYPE=Mobile2:" . $user->mobile_no_1 . "\n";
        $vCard .= "TEL;TYPE=" . ucwords('Mobile2') . ":" . $user->mobile_no_1 . "\n";

       }
       if($user->email){
        $vCard .= "EMAIL;INTERNET;TYPE=" . ucwords('Email') . ":" . $user->email . "\n";
       }
       if($user->note){
        $vCard .= "NOTE;CHARSET=utf-8:Added by " . config("app.name", "") . ", " . date('M d, Y') . ' ' . $user->note . "\n";
       }

        $vCard .= "END:VCARD";


        // pre_print(strtotime($user->note_visible_to) .'>='. strtotime(date('Y-m-d')));
        // $currentDate =  Carbon::create(date('Y-m-d'));
        // $note_visible_from = Carbon::create($user->note_visible_from);
        // $note_visible_to = Carbon::create($user->note_visible_to);


        // header('Content-Type: text/vcard');
        $file_name = $file_name . '.vcf';
        // header('Content-Description: Download vCard');
        // header('Content-Type: text/vcard');
        // header('Content-Disposition: attachment; filename="' . $file_name . '"');
        // // header('Content-Length: ' . filesize($filePath));
        // header('Content-Transfer-Encoding: binary');
        // header('Expires: 0');
        // header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        // header('Pragma: public');
        // ob_clean();
        // flush();
        // $vCard = str_replace('ä', 'ae', $vCard);
        // $vCard = str_replace('ö', 'oe', $vCard);
        // $vCard = str_replace('ü', 'ue', $vCard);
          $filePath =  root_dir() . 'vcf-lead/' . $file_name;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $fileHandle = fopen($filePath, 'c+');
        fwrite($fileHandle, $vCard);
        fclose($fileHandle);

        return Response::download($filePath, $file_name, [
            'Content-Type' => 'text/vcard',
        ]);
        // exit;
        // echo $vCard;
    }

    function meta($request, $Obj, $brand_name = '', $language = 'en')
    {

        $has_subscription = chk_subscription($Obj);
        //pre_print($has_subscription);
        $query = \DB::table('customer_profiles AS cp')
            ->select(
                'cp.id',
                'cp.profile_link',
                'cp.profile_code',
                'cp.icon as cp_icon',
                'cp.title as cp_title',
                'p.title',
                'p.title_de',
                'p.icon as picon',
                'p.base_url',
                'u.username',
                'u.name',
                'u.id as user_id',
                'u.subscription_expires_on',
                'u.profile_view',
                'cp.is_business',
                'cp.file_image',
                'cp.user_id',
                'p.type',
                'u.is_pro',
                'u.open_direct',
                'cp.is_focused',
                'cp.icon_svg_default as custom_icon_svg',
                'p.icon_svg_colorized as profile_icon_svg_colorized',
                'p.icon_svg_default as profile_icon_svg_default'
            )
            ->join('profiles AS p', 'p.profile_code', '=', 'cp.profile_code')
            ->join('users AS u', 'u.id', '=', 'cp.user_id')
            ->where('p.status', 1)
            ->where('cp.status', 1);

        if ($brand_name != '') {
            $query = $query->where('u.username', $brand_name)->whereRaw('cp.profile_code != "contact-card"');
        } else {
            $query = $query->where('u.id', $Obj->id);
            if ($has_subscription['success'] == false) {
                $query = $query->where('p.is_pro', 0);
                $query = $query->where('cp.is_default', 1);
                // $query = $query->groupBy('cp.profile_code');
            }
        }

        $query->orderBy('cp.sequence', 'ASC');
        $query->orderBy('cp.id', 'ASC');
        if ($brand_name != '') {
            //$query->limit(4);
        }
        $profiles = $query->get();


        if (count($profiles) > 0) {


            $iconType = "svg_default";
            foreach ($profiles as $i => $profile) {

                if ($Obj->open_direct == 1 && $i == 0) {
                    $profile->open_direct = 1;
                }

                // if ($profile->profile_code == 'whatsapp') {
                //     $profile->profile_link = trim($profile->profile_link, '+');
                // }



                $profile->icon = icon_url() . $profile->picon;

                //  pre_print($profile->icon);
                $contact_link = main_url() . '/contact-card/' . encrypt($profile->user_id);
                if ($profile->profile_icon_svg_default != '') {
                    $profile->icon = $profile->profile_icon_svg_default;
                }

                // if ($profile->profile_icon_svg_colorized != '') {
                //     $profile->icon = $profile->profile_icon_svg_colorized;
                // }

                // $settings->color_link_icons = notEmpty($template->color_link_icons) && true_false($template->colors_custom_locked)
                //  ? $template->color_link_icons : $settings->color_link_icons;


                $setting_color_link_icons = UserSettings::where('user_id', $Obj->id)->first();
                if($setting_color_link_icons){
                    $setting_color_link_icon = $setting_color_link_icons->color_link_icons;
                }else{
                    $setting_color_link_icon = 0;
                }

               // $template = anyTemplateAssigned($Obj->id);
               $template = anyTemplateAssignedProfile($Obj->id);
                $template = (object)$template;
                if (!empty($template) && isset($template->color_link_icons)) {
                    $setting_color_link_icon = notEmpty($template->color_link_icons) && true_false($template->colors_custom_locked) ? $template->color_link_icons : 0;
                }

              //  pre_print($setting_color_link_icon);


                // if ($setting_color_link_icons) {
                //     $setting_color_link_icon = $setting_color_link_icons->color_link_icons;

                    // Check if $setting_color_link_icon is not empty and equal to 1
                    if (!empty($setting_color_link_icon) && $setting_color_link_icon == 1) {
                        $profile->icon = $profile->profile_icon_svg_colorized;
                        $iconType = "svg_colorized";
                    }
                // }

                $profile->profile_value = $profile->profile_link;
                $profile->profile_link = ($profile->profile_code != 'contact-card') ? $profile->profile_link : $contact_link;
                if ($profile->profile_code == 'whatsapp') {
                    $profile->profile_link = $profile->base_url . trim($profile->profile_link, '+');
                } else {
                    $profile->profile_link = ($profile->profile_code != 'file') ? $profile->base_url . $profile->profile_link : file_url() . $profile->file_image;
                }

                if ($profile->profile_code == 'www' || $profile->type == 'url') {
                    $is_valid_url = false;
                    if (substr($profile->profile_link, 0, 8) == "https://" || substr($profile->profile_link, 0, 7) == "http://") {
                        $is_valid_url = true;
                    }

                    if ($is_valid_url == false) {
                        $profile->profile_link = 'http://' . $profile->profile_link;
                    }
                } else if ($profile->profile_code == 'wifi') {
                    $qr_file = $profile->user_id . '.svg';
                    $dir = root_dir() . 'uploads/qrcodes/';
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }

                    if ($profile->profile_link != '' && $profile->profile_link != null) {
                        $fp = fopen($dir . $qr_file, 'w');
                        fwrite($fp, QrCode::size(300)->format('svg')->generate($profile->profile_link));
                        fclose($fp);
                    }

                    // $profile->icon = uploads_url() . 'qrcodes/' . $qr_file;
                    // $profile->icon = icon_url() . 'qr-code--v2.png';
                    $profile->profile_link = 'popup-link';
                }

                // if ($profile->subscription_expires_on != NULL && strtotime($profile->subscription_expires_on) >= strtotime(date('Y-m-d H:i:s'))) {

                if ($language == 'de') {
                    $profile->title = $profile->title_de;
                } else {
                    $profile->title_de = $profile->title;
                }

                if (!in_array($profile->profile_code, is_free_profile_btn())) {



                    if ($has_subscription['success'] == true) {

                        if ($profile->cp_title != '' && $profile->cp_title !=  NULL) {
                            $profile->title = $profile->title_de = $profile->cp_title;
                        }
                        // old
                        // $profile->icon = ($profile->cp_icon != '' && $profile->cp_icon != NULL) ? icon_url() . $profile->cp_icon : $profile->icon;

                        // if ($profile->custom_icon_svg != '') {
                        //     $profile->icon = $profile->custom_icon_svg;
                        // }
                        if (!empty($setting_color_link_icon) && $setting_color_link_icon == 1) {

                            if ($profile->cp_icon != '' && $profile->cp_icon != NULL) {
                                $profile->icon = icon_url() . $profile->cp_icon ?? $profile->profile_icon_svg_default;
                            } else {
                                $profile->icon = $profile->profile_icon_svg_colorized;
                                $iconType = "svg_colorized";
                            }
                            if (empty($profile->cp_icon) && empty($profile->custom_icon_svg)) {
                                $profile->icon = icon_url() . $profile->icon ?? $profile->profile_icon_svg_default;
                            }
                        } else {
                            if ($profile->cp_icon != '' && $profile->cp_icon != NULL) {
                                $profile->icon = icon_url() . $profile->cp_icon ?? $profile->profile_icon_svg_default;
                            } else {
                                $profile->icon = $profile->custom_icon_svg;
                            }
                            if (empty($profile->cp_icon) && empty($profile->custom_icon_svg)) {
                                $profile->icon = $profile->icon ?? $profile->profile_icon_svg_colorized;
                            }
                        }

                        if (!empty($setting_color_link_icon) && $setting_color_link_icon == 1) {
                            if (empty($profile->cp_icon) && empty($profile->custom_icon_svg)) {
                                $profile->icon = $profile->profile_icon_svg_colorized;
                            }
                            $iconType = "svg_colorized";

                        }else{
                            if (empty($profile->cp_icon) && empty($profile->custom_icon_svg)) {
                                $profile->icon = $profile->profile_icon_svg_default;
                            }
                        }


                    }
                } else {
                    if ($profile->cp_title != '' && $profile->cp_title !=  NULL) {
                        $profile->title = $profile->title_de = $profile->cp_title;
                    }



                }






                if ($profile->profile_view == 'business' && $profile->is_business == 0) {
                    unset($profiles[$i]);
                } else if ($profile->profile_view == 'personal' && $profile->is_business == 1) {
                    unset($profiles[$i]);
                }

               if ($profile->profile_code == 'contact-card') {
                    // $is_business = $profile->profile_view == 'business' ? 1 : 0;
                    // $ContactCard = ContactCard::where('user_id', $profile->user_id)->where('is_business', $is_business);
                    // if($ContactCard->count() == 0){
                    // 	unset($profiles[$i]);
                    // }else{
                    // 	$ContactCard = $ContactCard->first();
                    // 	$customer_profile_ids = $ContactCard->customer_profile_ids;
                    // 	$CustomerProfile = CustomerProfile::whereIn('id',explode(',',$customer_profile_ids));
                    // 	if($CustomerProfile->count() == 0){
                    // 		unset($profiles[$i]);
                    // 	}
                    // }
                    unset($profiles[$i]);
                }
            }
        }
        // pre_print($profiles);
        $final_profiles_data = [];
        if (count($profiles) > 0) {
            foreach ($profiles as $i => $profile) {

                $profile->iconType = $iconType;
                $final_profiles_data[] = $profile;
            }
        }
        return $final_profiles_data;
    }

    public function generate_unique_codes()
    {

        for ($i = 0; $i < 200; $i++) {
            $code = new UniqueCode;
            $code->str_code = strtoupper(uniqid(rand(1, 9)));
            $code->save();
        }
        // echo strtoupper(uniqid(rand(1,9)));
    }

    public function tests()
    {
        echo '<p style="text-align:center;">Hi Muhammad Bilal, you have a new connection on ' . ucwords(config("app.name", "")) . '! <br><br>Reply to this email to start a conversation with Hamza <br><br><img alt="' . config("app.name", "") . '" height="40" src="' . uploads_url() . 'img/user.png"><br><br>Hamza<br><br>hamza@email<br><br>+923336333<br><br>' . dmytime('2022-02-25') . '<br><br><a href="https://addmee.de/"><img alt="' . config("app.name", "") . '" height="40" src="' . uploads_url() . 'img/addmee-logo.png"></a><a  href="https://addmee.de/pages/app">Create My ' . ucwords(config("app.name", "")) . ' Card</a></p>';
    }



    public function test_contact_card(HttpRequest $request)
    {
        mb_internal_encoding("UTF-8");
        $iPod    = stripos($_SERVER['HTTP_USER_AGENT'], "iPod");
        $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
        $iPad    = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");
        $Android = stripos($_SERVER['HTTP_USER_AGENT'], "Android");
        $webOS   = stripos($_SERVER['HTTP_USER_AGENT'], "webOS");

        $isIOS = false;
        //do something with this information
        if ($iPod || $iPhone) {
            //browser reported as an iPhone/iPod touch -- do something here
            $isIOS = true;
        } else if ($iPad) {
            //browser reported as an iPad -- do something here
            $isIOS = true;
        } else if ($Android) {
            //browser reported as an Android device -- do something here
        } else if ($webOS) {
            //browser reported as a webOS device -- do something here
        }

        $user = $Obj = User::where('username', $request->username)->first();
        $is_business = $user->profile_view == 'business' ? 1 : 0;
        $ContactCard = ContactCard::where('user_id', $user->id)->where('is_business', $is_business);

        if ($ContactCard->count() > 0) {
            $ContactCard = $ContactCard->first();
            $ContactCard = explode(',', $ContactCard->customer_profile_ids);
        } else {
            $ContactCard = [];
        }

        $profiles = $this->meta($request, $Obj);
        //pre_print($profiles);

        $name = explode(' ', $user->name);
        $first_name = $user->first_name; //$name[0];
        $last_name = $user->last_name; //trim(str_replace($first_name, '', $user->name));

        if ($first_name == '' && $last_name == '') {
            $first_name = $name[0];
            $last_name = trim(str_replace($first_name, '', $user->name));
        }

        // $first_name = str_replace('ä', 'ae', $first_name);
        // $first_name = str_replace('ö', 'oe', $first_name);
        // $first_name = str_replace('ü', 'ue', $first_name);
        // $last_name = str_replace('ä', 'ae', $last_name);
        // $last_name = str_replace('ö', 'oe', $last_name);
        // $last_name = str_replace('ü', 'ue', $last_name);

        $file_name = trim($first_name . ' ' . $last_name);
        $file_name = str_replace('ä', 'ae', $file_name);
        $file_name = str_replace('ö', 'oe', $file_name);
        $file_name = str_replace('ü', 'ue', $file_name);
        $file_name = preg_replace("/[^A-Za-z0-9.]/", '', $file_name);

        $vCard  = "BEGIN:VCARD\n";
        $vCard .= "VERSION:3.0\n";
        if ($isIOS == true) {
            $vCard .= "N;CHARSET=utf-8:" . $last_name . ";" . $first_name . "\n";
        }
        $vCard .= "FN;CHARSET=utf-8:" . trim($first_name . ' ' . $last_name) . "\n"; //full name
        // $vCard .= "NAME:" . trim($first_name . ' ' . $last_name) . "\n";
        if ($user->designation != '') {
            $vCard .= "TITLE;CHARSET=utf-8:" . $user->designation . "\n";
        }
        if ($user->company_name != '') {
            $vCard .= "ORG;CHARSET=utf-8:" . $user->company_name . "\n";
        }

        $vCard .= "URL;TYPE=My " . config("app.name", "") . " Profile:" . main_url() . "/" . $user->username . "\n";
        if (!empty($profiles)) {
            foreach ($profiles as $profile) {
                if ($profile->profile_code == 'wifi') {
                    continue;
                }

                if (in_array($profile->id, $ContactCard)) {
                    if ($profile->profile_code == 'call' || $profile->profile_code == 'business_call' || $profile->profile_code == 'whatsapp') {
                        $profile->profile_code = ($profile->profile_code == 'business_call') ? 'business' : ($profile->profile_code == 'call' ? 'Mobile' : $profile->profile_code);
                        $vCard .= "TEL;TYPE=" . ucwords($profile->profile_code) . ":" . $profile->profile_value . "\n";
                    } else if ($profile->profile_code == 'text') {
                        $vCard .= "TEL;TYPE=" . ucwords($profile->profile_code) . ":" . $profile->profile_value . "\n";
                    } else if ($profile->profile_code == 'email') {
                        $vCard .= "EMAIL;INTERNET;TYPE=" . ucwords($profile->profile_code) . ":" . $profile->profile_value . "\n";
                    } else if ($profile->profile_code == 'www') {
                        $vCard .= "URL;TYPE=Website:" . $profile->profile_value . "\n";
                    } else if ($profile->profile_code == 'address') {
                        $vCard .= "ADR;TYPE=" . ucwords($profile->profile_code) . ":" . $profile->profile_value . "\n";
                    } else if ($profile->profile_code != 'contact-card') {
                        $vCard .= "URL;TYPE=" . ucwords($profile->title) . ":" . $profile->profile_link . "\n";
                    }
                }
            }
        }

        if ($user->logo != '') {
            if (file_exists(icon_dir() . $user->logo)) {
                $getPhoto = file_get_contents(icon_dir() . $user->logo);
                $b64vcard = base64_encode($getPhoto);
                $b64mline = chunk_split($b64vcard, 74, "\n");
                $b64final = preg_replace('/(.+)/', ' $1', $b64mline);
                $photo  = $b64final;
                $vCard .= "PHOTO;ENCODING=b;TYPE=JPEG:";
                $vCard .= $photo . "\n";
            }
        }

        $has_subscription = chk_subscription($Obj);
        $currentDate =  Carbon::create(date('Y-m-d'));
        $note_visible_from = Carbon::create($user->note_visible_from);
        $note_visible_to = Carbon::create($user->note_visible_to);

        if (
            $has_subscription['success'] == true &&
            $user->note_description != NULL && $user->note_visible_from != NULL && $user->note_visible_to != NULL
            && $currentDate->lessThanOrEqualTo($note_visible_to)
            && $currentDate->greaterThanOrEqualTo($note_visible_from)
        ) {
            $vCard .= "NOTE;CHARSET=utf-8:Added by " . config("app.name", "") . ", " . date('M d, Y') . ' ' . $user->note_description . "\n";
        } else {
            $vCard .= "NOTE;CHARSET=utf-8:Added by " . config("app.name", "") . ", " . date('M d, Y') . "\n";
        }
        $vCard .= "END:VCARD";

        $file_name = $file_name . '.vcf';
        $filePath = vcf_dir() . $file_name;
        // file_put_contents(vcf_dir() . $file_name, $vCard);
        $fp = fopen($filePath, 'w');
        fwrite($fp, trim($vCard));
        fclose($fp);
        // header("Content-Type: text/vcard; charset=utf-8");
        // header('Content-Disposition: attachment; filename="' . $file_name . '.vcf"');

        // $vCard = str_replace('ä', 'ae', $vCard);
        // $vCard = str_replace('ö', 'oe', $vCard);
        // $vCard = str_replace('ü', 'ue', $vCard);

        header('Content-Description: Download vCard');
        header('Content-Type: text/vcard');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        ob_clean();
        flush();

        echo $vCard;
        // readfile($filePath);
    }

    public function test()
    {
        $vcard = "BEGIN:VCARD\n";
        $vcard .= "VERSION:3.0\n";
        $vcard .= "FN:Susanne Schlüters\n";
        $vcard .= "NAME:Susanne Schlüters\n";
        $vcard .= "ORG:BVMW-Münsterland\n";
        $vcard .= "URL;TYPE=My AddMee Profile:https://devaddmee.addmee-portal.de/SusanneSchluetersBVMW\n";
        $vcard .= "URL;TYPE=Website:https://www.bvmw.de/muensterland/\n";
        $vcard .= "ADR;TYPE=Address:Kastanienstraße 61, 48485 Neuenkirchen\n";
        $vcard .= "EMAIL;INTERNET;TYPE=Email:susanne.schlueters@bvmw.de\n";
        $vcard .= "TEL;TYPE=Mobile:+491712647153\n";
        $vcard .= "URL;TYPE=LinkedIn:https://www.linkedin.com/company/bvmw-münsterland/about\n";
        $vcard .= "URL;TYPE=Instagram:https://instagram.com/bvmw.muensterland\n";
        $vcard .= "URL;TYPE=Facebook:https://www.facebook.com/bvmw.muensterland/\n";
        $vcard .= "NOTE;CHARSET=utf-8:Added by AddMee, Jun 13, 2023\n";
        $vcard .= "END:VCARD";

        // Set the appropriate headers for file download
        header("Content-Type: text/vcard");
        header("Content-Disposition: attachment; filename=contact.vcf");

        // Send the vCard content as a file download
        echo $vcard;
        exit;
        // SELECT * FROM `users` WHERE `id` IN (SELECT user_id from business_users WHERE parent_id IN (16261,15874));
        $users = User::select('users.*')->whereIn('parent_id', [16261, 15874])->Join('business_users AS bu', 'bu.user_id', '=', 'users.id')->get();
        pre_print($users);
        foreach ($users as $user) {
            $CustomerProfile = CustomerProfile::where('profile_code', 'email')->where('user_id', $user->id)->count();
            if ($CustomerProfile == 0) {
                $Obj = new CustomerProfile;
                $Obj->profile_link = $user->email;
                $Obj->profile_code = 'email';
                $Obj->is_business = 0;
                $Obj->user_id = $user->id;
                $Obj->created_by = 1;
                $Obj->save();

                $is_business = 0;
                $ContactCard = ContactCard::where('user_id', $user->id)->where('is_business', $is_business);

                if ($ContactCard->count() > 0) {
                    $ContactCard = $ContactCard->first();
                    $ContactCard->customer_profile_ids = trim($ContactCard->customer_profile_ids . ',' . $Obj->id, ',');
                    $ContactCard->is_business = $is_business;
                    $ContactCard->updated_by = $user->id;
                    $ContactCard->save();
                } else {
                    $ContactCard = new ContactCard;
                    $ContactCard->customer_profile_ids = $Obj->id;
                    $ContactCard->is_business = $is_business;
                    $ContactCard->user_id = $user->id;
                    $ContactCard->created_by = $user->id;
                    $ContactCard->save();
                }
            }
        }
    }

    //apis
    public function activateCode(HttpRequest $request)
    {
        if (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] != 'application/json') {
            return redirect()->away(main_url() . '/' . $request->code);
        }

        $code = UniqueCode::where('str_code', $request->code);
        if ($code->count() == 0) {

            $data['success'] = FALSE;
            $data['message'] = 'This is not a valid ' . config("app.name", "") . ' product.';
            $data['data'] = [];
            return response()->json($data, 404);
        }

        $token = $request->user();
        $code = $code->first();
        if ($code->status == 0) {

            $data['success'] = FALSE;
            $data['message'] = 'This is not a valid ' . config("app.name", "") . ' product.';
            $data['data'] = [];
            return response()->json($data, 400);
        } else if ($code->user_id > 0) {

            // if (config("app.name", "") != 'addmee') {
            // } else {
            // 	$data['message'] = 'Dieses AddMee Produkt ist bereits aktiviert.';
            // }
            if ($token->id == $code->user_id) {
                $data['message'] = 'This ' . config("app.name", "") . ' product is already activated.';
            } else {
                $data['message'] = 'This ' . config("app.name", "") . ' product has been already activated with another profile.';
            }
            $data['success'] = FALSE;
            $data['data'] = [];
            return response()->json($data, 400);
        } else if ($code->activated == 1 && $code->user_id == 0 && $token->id != $code->assigned_to) {
            $data['message'] = 'This ' . config("app.name", "") . ' product is deactivated.';
            $data['success'] = FALSE;
            $data['data'] = [];
            return response()->json($data, 400);
        } else {
            $token = $request->user();

            if (isset($_GET['user_id'])) {
                $user_id = trim($_GET['user_id']);
                if ($user_id == 0) {
                    $data['message'] = 'Invalid ' . config("app.name", "") . ' user.';
                    $data['success'] = FALSE;
                    $data['data'] = [];
                    return response()->json($data, 400);
                }
            } else {
                $user_id = $token->id;
            }
            // $code->status = 1;
            $code->activated = 1;
            $code->user_id = $user_id;
            $code->device = $request->device;
            $code->activation_date = date('Y-m-d H:i:s');
            $code->save();

            $data['success'] = TRUE;
            $data['message'] = 'Code';
            $data['data'] = ['code' => $code];
            return response()->json($data, 201);
        }
    }
}
