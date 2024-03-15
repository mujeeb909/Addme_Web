<?php

use App\Models\UserGroups;
use App\Models\Menu;
use App\Models\Activities;
use App\Models\CustomerProfile;
use App\Models\Profile;
use App\Models\BusinessUser;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Models\ContactCard;
use App\Models\UserTemplate;
use App\Models\UserSettings;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\HomeController;
use App\Models\CustomerProfileTemplate;
use App\Models\TemplateAssignee;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Models\PlatformIntegration;
use App\Models\Platform;



function http_accept()
{
    // return Request::acceptsJson();
    if (getUserIP() == '39.33.131.245') {
        // pre_print($_SERVER['HTTP_ACCEPT']);
    }

    if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && strtolower(trim($_SERVER['HTTP_ACCEPT'])) == 'application/json') {
        return true;
    }

    return false;
}


function sendOutlookEmail($userEmail, $subject, $body)
{
    $outlookConfig = [
        'driver' => 'smtp',
        'host' => 'smtp-mail.outlook.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => 'no-reply@addmee.de',
        'password' => 'gpjgjlhfgmhmgjsz',
    ];

    // Use the configuration to send emails
    Mail::mailer('smtp')->send([], [], function ($message) use ($userEmail, $subject, $body, $outlookConfig) {
        $message->from($outlookConfig['username']);
        $message->to($userEmail);
        $message->subject($subject);
        $message->setBody($body, 'text/html');
    });
}


function generateAccessTokenWithRefreshToken($refreshToken, $check_user)
{
    $platform = Platform::where('id', 1)->first();
    $tokenUrl = 'https://api.hubapi.com/oauth/v1/token';
    $httpClient = new Client();
    try {
        $response = $httpClient->post($tokenUrl, [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'client_id' => $platform->client_id,
                'client_secret' => $platform->client_secret,
                'refresh_token' => $refreshToken,
            ],
        ]);
        $tokenData = json_decode($response->getBody(), true);
        if ($tokenData) {
            $check_user->refresh_token = $tokenData['refresh_token'];
            $check_user->access_token = $tokenData['access_token'];
            $currentDateTime = new DateTime();
            $minutes = (int)$tokenData['expires_in'] / 60;
            $currentDateTime->add(new \DateInterval('PT' . $minutes . 'M'));
            // Now $currentDateTime contains the current datetime + 30 minutes
            $check_user->expires_in = $currentDateTime->format('Y-m-d H:i:s');
            $check_user->save();
        }
        // Access token
        return  $accessToken = $tokenData['access_token'];
    } catch (\Exception $e) {
        return null;
    }
}

function add_reference_no()
{
    return rand(100, 999);
}

function assets_version()
{
    return '?v=1.0.0';
}

function main_url()
{
    return URL::to('/');
}

function business_portal_url()
{
    return 'https://addmee-portal.de/';
}

function business_portal_url2()
{
    return 'https://dev.addmee-portal.de';
}

function main_url_wo_http()
{
    return str_replace('http://', '', str_replace('https://', '', main_url())) . '/';
}

// function assets_url($path)
// {
//     // pre_print(request()->getSchemeAndHttpHost());
//     if (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] != 'localhost' || $_SERVER['SERVER_NAME'] != '127.0.0.1')) {
//         return main_url() . '/public/admin/' . $path;
//     } else {
//         return main_url() . '/' . $path;
//     }
// }

function assets_url($path)
{
    if (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] != 'localhost' && $_SERVER['SERVER_NAME'] != '127.0.0.1')) {
        return main_url() . '/public/admin/' . $path;
    } else {
        return main_url() . '/' . $path;
    }
}


function admin_url()
{
    return URL::to('/admin/');
}

function asset_version()
{
    return '';
}

function root_dir()
{
    $root_dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    $root_dir = $_SERVER['DOCUMENT_ROOT'] . $root_dir;
    return $root_dir;
}

function uploads_dir()
{
    return root_dir() . 'uploads/';
}

function uploads_url()
{
    return URL::to('/uploads/') . '/';
}

function asset_url()
{
    return URL::to('/') . '/';
}

function icon_dir()
{
    return root_dir() . 'uploads/icons/';
}

function icon_url($user_id = '')
{
    return URL::to('/uploads/icons/') . '/';
}

function vcf_dir()
{
    return root_dir() . 'vcf/';
}

function vcf_url($user_id = '')
{
    return URL::to('/vcf/') . '/';
}

function file_dir()
{
    return root_dir() . 'uploads/files/';
}

function file_url($user_id = '')
{
    return URL::to('/uploads/files/') . '/';
}

function image_url($image = '')
{
    if ($image == '' || $image == null) {
        return '';
    }
    return icon_url() . $image;
}

function isJsonRequest()
{
    if (Request::wantsJson()) {
        return true;
    }

    return false;
}

function current_func()
{
    $method = Route::getCurrentRoute()->getActionName();
    $method = explode('@', $method);
    $count = count($method);
    $method = $method[$count - 1];
    return $method;
}

function current_method()
{
    $currentPath = Route::getFacadeRoot()->current()->uri();
    $method = Route::getCurrentRoute()->getActionName();
    $name = Route::getCurrentRoute()->getName();
    $prefix = Route::getCurrentRoute()->getPrefix();
    //dd(Request::wantsJson());
    $method = explode('@', $method);
    return \Request::segment(2); //$method = $method[count($method) - 1];
}

function common_data()
{

    $data['image_url']    = admin_url();
    $data['breadcrumbs'] = array('Home' => admin_url(), 'Dashboard' => '#');
    $data['show_action'] = 1;
    $data['show_delete'] = $data['show_add'] = 0;
    $data['show_update'] = 1;
    $data['show_child'] = 1;
    $data['custom']        = '';
    $data['page_method'] = current_method();
    return $data;
}

function currency()
{
    return '$ ';
}

function per_page()
{
    return 20;
}

function next_date($day)
{
    $next_day = strtotime("next " . strtolower($day));
    $next_day = date('W', $next_day) == date('W') ? $next_day + 7 * 86400 : $next_day;
    return date('M d, Y', ($next_day));
}

function posted_fields($_post)
{
    $info    = array();
    foreach ($_post as $key => $post) {
        $info[$key] = clean_posted_value($_POST[$key]);
    }

    return $info;
}

function clean_posted_value($value)
{
    return trim($value);
}

function eql()
{
    \DB::enableQueryLog();
}

function lq()
{
    $query = \DB::getQueryLog();
    dd(end($query));
}

function pre_print($data)
{
    echo '<pre>';
    print_r(json_decode(json_encode($data)));
    exit;
}

function encode($value)
{
    return base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode($value)))))));;
}

function decode($value)
{
    return base64_decode(base64_decode(base64_decode(base64_decode(base64_decode(base64_decode(base64_decode($value)))))));
}

function sendgrid_api($html, $subject, $sendTo, $to_name, $attachments = array())
{
    $url = 'https://api.sendgrid.com/';
    $json_string['to'][0] = $sendTo;
    $json_string['category'][0] = 'API';
    $params = array(
        'x-smtpapi' => json_encode($json_string),
        'to' => env("MAIL_FROM_ADDRESS", ""),
        'subject' => $subject,
        'html' => $html,
        'from' => env("MAIL_FROM_ADDRESS", ""),
        'fromname' => env("MAIL_FROM_NAME", "") . ' Support'
    );

    if (!empty($attachments)) {
        foreach ($attachments as $attachment) {
            $params['files[' . str_replace(' ', '-', $attachment['filename']) . ']'] = '@' . $attachment['file_url'];
            //$this->email->attach($attachment->file_url, 'attachment', $attachment0>filename);
            //https://sendgrid.com/docs/for-developers/sending-email/v2-php-code-example/
        }
    }

    $request = $url . 'api/mail.send.json';
    $ch = curl_init($request);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . env("MAIL_PASSWORD") . ''));
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    // Tell PHP not to use SSLv3 (instead opting for TLS)
    //curl_setopt ( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2 );

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $json_response = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($json_response);
    //pre_print($response);
    return TRUE; //!empty($response->msg) ? true : false;
}

function send_email($content, $subject, $to_email, $to_name, $from_name = 'SITE_TITLE', $from_email = 'SMTP_USER')
{
    error_reporting(0);
    return true;
}

function profile_img_url($staff_id, $value)
{
    if ($value == '') {
        return admin_url() . 'img/avatar.png';
    } else if (file_exists($staff_id . '/' . $value)) {
        return admin_url() . $staff_id . '/' . $value;
    } else {
        return admin_url() . 'img/avatar.png';
    }
}

function monthyear($date)
{
    return date('M, Y', strtotime($date));
}

function dmy($date)
{
    return date('d, M Y', strtotime($date));
}

function dmytime($date)
{
    return date('d, M Y, h:i A', strtotime($date));
}

function dmyhm($date)
{
    return dmytime($date);
}

function hoursmin($date)
{
    return date('h:i A', strtotime($date));
}

function format_datetime()
{
    $date = new DateTime('now', new DateTimeZone('Asia/Tel_Aviv'));
    return $date->format('Y-m-d H:i:s');
}

function format_time()
{
    $date = new DateTime('now', new DateTimeZone('Asia/Tel_Aviv'));
    return $date->format('H:i:s');
}

function select_tpl($records, $field_name, $field_title, $required = 'y', $selected = '')
{


    $blur = $required == 'y' ? ' onBlur="validate(\'dd\', \'dd\', \'' . $field_name . '\', \'' . $field_title . '\');"' : '';
    $html = '';
    $html = '<select id="' . $field_name . '" name="' . $field_name . '" class="form-control mb-1"' . $blur . '>';
    $html .= '<option value="">Select ' . $field_title . '</option>';
    foreach ($records as $record) {
        $record = (object) $record;
        $selected_opt = '';
        if ($selected != '' && $selected == $record->id) {
            $selected_opt = ' selected';
        }
        if ($record->title != '') {
            $html .= '<option value="' . $record->id . '"' . $selected_opt . '>' . $record->title . '</option>';
        }
    }
    $html .= '</select>';
    return $html;
}

function multi_select_tpl($records, $field_name, $field_title, $required = 'y', $selected = '')
{
    $blur     = $required == 'y' ? ' onBlur="validate(\'dd\', \'dd\', \'' . $field_name . '\', \'' . $field_title . '\');"' : '';
    $html     = '';
    $html     = '<select id="' . $field_name . '" name="' . $field_name . '[]" multiple class="form-control multiple mb-1"' . $blur . '>';
    $html     .= '<option value="">Select ' . $field_title . '</option>';
    $selected = explode(',', $selected);
    foreach ($records as $record) {
        $selected_opt = '';
        if (!empty($selected) && in_array($record->id, $selected)) {
            $selected_opt = ' selected';
        }
        $html .= '<option value="' . $record->id . '"' . $selected_opt . '>' . $record->title . '</option>';
    }
    $html .= '</select>';
    return $html;
}

function row_status($val = 1)
{
    if ($val == 1) {
        return '<badge class="badge badge-success">Active</badge>';
    } else if ($val == 2) {
        return '<badge class="badge badge-danger">Unverified</badge>';
    }

    return '<badge class="badge badge-danger">Inactive</badge>';
}

function user_status($val = 1)
{
    if ($val == 1) {
        return '<badge class="badge badge-success">Active</badge>';
    } else if ($val == 0) {
        return '<badge class="badge badge-danger">Inactive</badge>';
    }

    return '<badge class="badge badge-danger">Unverified</badge>';
}

function accountType($val = 1)
{
    if ($val == 2) {
        return '<badge class="badge badge-success">Parent</badge>';
    } else if ($val == 7) {
        return '<badge class="badge badge-success">Pet</badge>';
    }
    else if ($val ==8) {
        return '<badge class="badge badge-success">Sos</badge>';
    }
    else if ($val == 9) {
        return '<badge class="badge badge-success">Personal</badge>';
    }
    else if ($val == 10) {
        return '<badge class="badge badge-success">Business</badge>';
    }

    return '<badge class="badge badge-success">Unverified</badge>';
}

function doc_status($val = 1)
{
    if ($val == 1) {
        return '<badge class="badge badge-success">Approved</badge>';
    } else if ($val == 2) {
        return '<badge class="badge badge-danger">Rejected</badge>';
    }

    return '<badge class="badge badge-danger">Pending</badge>';
}

function user_gender($val = 3)
{
    if ($val == 1) {
        return 'Male';
    } else if ($val == 2) {
        return 'Female';
    }

    return 'Unknown';
}

function yes_no($value = 1)
{
    if (trim($value) == 1) {
        return '<badge class="badge badge-success">Yes</badge>';
    }

    return '<badge class="badge badge-danger">No</badge>';
}

function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR']    = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }

    return $ip;
}

function getLastNDays($days, $format = 'd/m')
{
    $m         = date("m");
    $de        = date("d");
    $y         = date("Y");
    $dateArray = array();
    for ($i = 0; $i <= $days - 1; $i++) {
        $dateArray[str_replace(' ', '', date($format, mktime(0, 0, 0, $m, ($de - $i), $y)))]['day']   = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
        $dateArray[str_replace(' ', '', date($format, mktime(0, 0, 0, $m, ($de - $i), $y)))]['total'] = 0;
    }

    return array_reverse($dateArray);
}

function CalcPercentage($total, $obtained) //returns %age
{
    $percent = ($total != 0) ? round(($obtained / $total) * 100, 2) : 0;
    return $percent;
}

function valueByPercent($total, $percent) //returns value against %age
{
    $val = (($percent * $total) / 100); //ceil
    return $val;
}

function calcValueByPercent($total, $percent) //returns value against %age without ceiling
{
    $val = ($percent * $total) / 100;
    return $val;
}

function columnValue($col_value, $row, $image_url = '')
{
    if ($col_value == 'row_datetime') {
        return dmyhm($row->$col_value);
    } else if ($col_value == 'is_pro_yes_no') {

        if (isset($row->user_role) && $row->$col_value == 2) {
            if (trim($row->user_role) == 'admin') {
                return '<badge class="badge badge-success">Business Admin</badge>';
            } else {
                return '<badge class="badge badge-success">Business User</badge>';
            }
        }

        if ($row->$col_value == 1) {
            return '<badge class="badge badge-success">Pro User</badge>';
        } else if ($row->$col_value == 2) {
            return '<badge class="badge badge-success">Business User</badge>';
        } else if ($row->$col_value == 0) {
            return '<badge class="badge badge-success">Normal User</badge>';
        } else if ($row->$col_value == -1) {
            return '<badge class="badge badge-success">Free Subscription</badge>';
        }
    } else if ($col_value == 'row_status') {
        return row_status($row->$col_value);
    } else if ($col_value == 'view_file') {
        if ($row->$col_value != '') {
            return '<a class="badge badge-primary" target="_blank" href="' . url() . '/' . $row->id . '/' . $row->$col_value . '">View File</a>';
        }

        return '';
    } else if ($col_value == 'mapped_profile') {
        if ($row->$col_value != '') {
            return '<a class="badge badge-primary" target="_blank" href="' . main_url() . '/' . $row->$col_value . '">View Profile</a>';
        }

        return '';
    } else if ($col_value == 'str_code_html') {
        if ($row->$col_value != '') {
            $str_url =  ($row->device != '') ? $row->device . '/' . $row->$col_value : $row->$col_value;
            if ($row->device != '' && $row->status == 1) {
                return '<a class="copy-str-code" onmouseout="showTooltip()" href="javascript:;" data-href="' . main_url() . '/' . $str_url . '">' . $row->$col_value . ' <i class="fa fa-copy"></i><span class="tooltiptext" id="myTooltip"> Copy to clipboard</span></a>';
            } else {
                return $row->$col_value;
            }
        }

        return $row->$col_value;
    } else if ($col_value == 'user_status') {
        return user_status($row->$col_value);
    } else if ($col_value == 'user_group_id') {
        return accountType($row->$col_value);
    }else if ($col_value == 'doc_status') {
        return doc_status($row->$col_value);
    } else if (strpos($col_value, 'date') > -1 && $row->$col_value != '') {
        return dmy($row->$col_value);
    } else if (strpos($col_value, 'yes_no')) {
        return yes_no($row->$col_value);
    } else if (strpos($col_value, '_gender')) {
        return user_gender($row->$col_value);
    } else if ($col_value == 'day') {
        return '<span class="badge badge-success">' . $row->$col_value . '</span>';
    } else if (strpos($col_value, 'order_status') > -1) {
        return ($row->$col_value);
    } else if (strpos($col_value, 'ad_type') > -1) {
        return ($row->$col_value);
    } else if ($col_value == 'img') {

        return '<img height="50" src="' . $image_url . $row->$col_value . '">';
    }

    return $row->$col_value;
}

function add_activity($info)
{
    $Activity = new Activities;
    $Activity->type = $info['type'];
    $Activity->type_id = $info['type_id'];
    $Activity->details = $info['details'];
    $Activity->device_id = $info['device_id'];
    $Activity->ip_address = $info['ip_address'];
    $Activity->created_at = date('Y-m-d H:i:s');
    $Activity->created_by = $info['created_by'];
    $Activity->save();
}

function permitted_menu_slugs()
{
    if (isset(Auth::user()->user_group_id)) {

        $json_file = root_dir() . "/jsons" . "/" . Auth::user()->id . '-permitted-menu-slugs.json';
        if (file_exists($json_file)) {
            $content = file_get_contents($json_file);
            $menu_slugs = ($content != '') ? json_decode($content) : array();
            return $menu_slugs;
        } else {
            $UserGroup = UserGroups::findorfail(Auth::user()->user_group_id);
            if ($UserGroup->permissions == '0') {
                $menus = Menu::query()->where('status', 1)->orderBy('sort', 'ASC')->get();
            } else {
                $permissions = explode(',', $UserGroup->permissions);
                $permissions = !empty($permissions) ? $permissions : array(0);
                $menus = Menu::query()->where('status', 1)->whereIn('id', $permissions)->orderBy('sort', 'ASC')->get();
            }

            $menu_slugs = array();
            if (count($menus) != 0) {
                foreach ($menus as $menu) {
                    $menu_slugs[] = $menu->menu_url;
                }
            }
            $fp = fopen($json_file, 'w');
            fwrite($fp, json_encode($menu_slugs));
            fclose($fp);
            return $menu_slugs;
        }
    }

    return array();
}

function permitted_menus($menu_id = 0)
{ //used only in Helpers.php:469

    if (isset(Auth::user()->user_group_id)) {
        $UserGroup = UserGroups::findorfail(Auth::user()->user_group_id);
        if ($UserGroup->permissions == '0') {
            $menus = Menu::query()->where('parent_id', 0)->where('status', 1)->orderBy('sort', 'ASC')->get();
        } else {
            $permissions = explode(',', $UserGroup->permissions);
            $permissions = !empty($permissions) ? $permissions : array(0);
            $menus = Menu::query()->where('parent_id', 0)->where('status', 1)->whereIn('id', $permissions)->orderBy('sort', 'ASC')->get();
        }

        return $menus;
    }

    return '';
}

function menus_html()
{

    $permitted_menus = permitted_menus();
    $html  = '';
    $url   = admin_url() . '/';
    $menu  = Menu::query()->where('menu_url', current_method())->get();
    $parent_id = count($menu) > 0 ? $menu[0]->parent_id : -1;
    $current_method = current_method();
    $permitted_menu_slugs = (array)permitted_menu_slugs();
    // pre_print($permitted_menus);
    foreach ($permitted_menus as $menu) {
        $open = '';
        if ($current_method == 'chips') {
            $open = 'open';
        }

        $class = (in_array($current_method, $permitted_menu_slugs) && $parent_id == $menu->id) ? 'active' : '';
        $html .= '<li class="nav-item ' . ($menu->has_sub_menus == 1 ? 'nav-dropdown ' . $open : '') . '"><a class="nav-link ' . ($menu->has_sub_menus == 1 ? 'nav-dropdown-toggle' : '') . ' ' . $class . '" href="' . ($menu->menu_url != '#' && $menu->menu_url != '' ? $url . $menu->menu_url : 'javascript:;') . '"><i class="nav-icon ' . $menu->css_class . '"></i> ' . $menu->title . '</a>';

        if ($menu->has_sub_menus == 1) {
            $html .= '<ul class="nav-dropdown-items">';

            $submenus = Menu::query()->where('parent_id', $menu->id)->where('is_sub_menu', 1)->where('status', 1)->orderBy('sort', 'ASC')->get();
            foreach ($submenus as $submenu) {
                $html .= '<li class="nav-item"> <a class="nav-link" href="' . admin_url() . '/' . $submenu->menu_url . '"><i class=""></i><i class="nav-icon"></i>' . $submenu->title . '</a></li>';
            }

            $html .= '</ul>';
        }

        $html .= '</li>';
    }

    return $html;
}

function page_buttons($page = '')
{
    $slugs = permitted_menu_slugs();
    $data = array();
    if ($page == 'staffs') {
        if (in_array('add_staff', $slugs)) {
            $data['show_add'] = 1;
        }

        if (!in_array('update_staff', $slugs)) {
            $data['show_update'] = 0;
            $data['show_action'] = 0;
        }
    }
    if ($page == 'business_customers') {
        if (in_array('add_business_customer', $slugs)) {
            $data['show_add'] = 1;
        }

        if (!in_array('update_business_customer', $slugs)) {
            $data['show_update'] = 0;
        }
        // $data['show_delete'] = 1;
        $data['custom'] = '<a class="btn btn-danger mb-1" href="' . admin_url() . '/delete_business_customer/{({row-id})}' . '"><i class="fa fa-times"></i> Delete</a>';

        $data['show_action'] = 1;
    } else if ($page == 'customers') {

        if (!in_array('update_customer', $slugs)) {
            $data['show_update'] = 0;
            $data['show_action'] = 0;
        }
        $data['show_action'] = 1;
        $data['show_delete'] = 1;
    } else if ($page == 'feedbacks') {

        if (!in_array('update_feedback', $slugs)) {
            $data['show_update'] = 0;
            $data['show_action'] = 0;
        }
        $data['show_action'] = 0;
        $data['show_delete'] = 0;
    } else if ($page == 'profiles') {

        if (in_array('add_profile', $slugs)) {
            $data['show_add'] = 1;
        }

        if (!in_array('update_profile', $slugs)) {
            $data['show_update'] = 0;
            $data['show_action'] = 0;
        }
    } elseif ($page == 'user_groups') {

        if (in_array('add_user_group', $slugs)) {
            $data['show_add'] = 1;
        }

        if (!in_array('update_user_group', $slugs)) {
            $data['show_update'] = 0;
        }

        if (!in_array('permissions', $slugs)) {
            $data['custom'] = '';
            $data['show_action'] = ($data['show_update'] == 0) ? 0 : 1;
        }
    } elseif ($page == 'drivers') {

        $data['show_add'] = 0;
        $data['show_update'] = 0;

        $data['custom']  = '<div class="dropdown"><a class="btn btn-md btn-primary mt-1 dropdown-toggle" href="javascript:;" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</a><div class="dropdown-menu" aria-labelledby="dropdownMenuLink" x-placement="bottom-start">';

        if (in_array('update_driver', $slugs)) {
            $data['custom'] .= '<a class="dropdown-item" href="' . admin_url() . '/update_driver/{({row-id})}' . '"><i class="fa fa-circle-o"></i> Update</a>';
        }
        if (in_array('driver_ratings', $slugs)) {
            $data['custom'] .= '<a class="dropdown-item" href="' . admin_url() . '/driver_ratings/{({row-id})}' . '"><i class="fa fa-circle-o"></i> Ratings</a>';
        }
        if (in_array('driver_vehicles', $slugs)) {
            $data['custom'] .= '<a class="dropdown-item" href="' . admin_url() . '/driver_vehicles/{({row-id})}' . '"><i class="fa fa-circle-o"></i> Vehicles</a>';
        }
        if (in_array('driver_wallets', $slugs)) {
            $data['custom'] .= '<a class="dropdown-item" href="' . admin_url() . '/driver_wallets/{({row-id})}' . '"><i class="fa fa-circle-o"></i> Wallet</a>';
        }
        if (in_array('driver_attachments', $slugs)) {
            $data['custom'] .= '<a class="dropdown-item" href="' . admin_url() . '/driver_attachments/{({row-id})}' . '"><i class="fa fa-circle-o"></i> Documents</a>';
        }

        $data['custom'] .= '</div></div>';
    }

    return $data;
}

function upload_file($request, $index, $destination)
{
    if (!file_exists($destination)) {
        mkdir($destination, 0777, true);
        $fp = fopen($destination . 'index.html', 'wb');
        fwrite($fp, '');
        fclose($fp);
    }

    $file = $request->file($index);
    if (empty($file)) {
        $data['success'] = TRUE;
        $data['filename'] = '';
        $data['message'] = 'Uploaded.';
        return $data;
    }
    $name = date('Ymdhis') . '__' . $file->getClientOriginalName();
    $ext = $file->getClientOriginalExtension();

    $valid_exts = array('png', 'jpg', 'jpeg', 'doc', 'docx', 'pdf', 'xls', 'xlsx', 'svg', 'bmp', 'mp4', 'avi', 'json', 'jfif', 'pjpeg', 'pjp', 'webp');
    if (!in_array($ext, $valid_exts)) {
        $data['success'] = FALSE;
        $data['message'] = 'Invalid Extension.';
        return $data;
    }
    // 2097152
    $size = $file->getSize();
    if ($size > 5242880) {
        $data['success'] = FALSE;
        $data['message'] = 'File size is greater than 5mb.';
        return $data;
    }
    //echo 'File Real Path: '.$file->getRealPath();
    //echo 'File Mime Type: '.$file->getMimeType();

    $filename = preg_replace('/[^A-Za-z0-9_\-]/', '', str_replace($ext, '', $name)) . '.' . $ext;
    $file->move($destination, $filename);

    $data['success'] = TRUE;
    $data['filename'] = $filename;
    $data['message'] = 'Uploaded.';
    return $data;
}

function chk_subscription($token, $customer_profile_id = 0)
{
    // if ($token->subscription_expires_on == NULL || strtotime($token->subscription_expires_on) <= strtotime(date('Y-m-d H:i:s'))) {
    if ($token->is_pro == 0) {
        $data['success'] = FALSE;
        $data['message'] = 'No active subscription available.';
        $data['data'] = [];
        return $data;
    }

    if ($token->subscription_expires_on != NULL && strtotime($token->subscription_expires_on) <= strtotime(date('Y-m-d H:i:s')) && $token->is_pro == is_grace_user()) {

        $User = User::where('id', $token->id)->whereIn('is_pro', [is_pro_user(), is_grace_user()]);
        if ($User->count() > 0) {
            $User = $User->first();
            $User->is_pro = 0;
            $User->save();
        }

        $data['success'] = FALSE;
        $data['message'] = 'No active subscription available.';
        $data['data'] = [];
        return $data;
    }

    $maxLimit = ($token->is_pro == is_pro_user() || $token->is_pro == is_grace_user()) ? max_profile_limit() : ($token->is_pro == is_business_user() ? 100000 : 1);

    $total = CustomerProfile::whereRaw('(title != "" OR icon != "" OR title != NULL OR icon != NULL)')->where('user_id', $token->id)->where('is_default', 1)->where('id', '!=', $customer_profile_id)->whereNotIn('profile_code', is_free_profile_btn())->count();

    $non_default_profiles = CustomerProfile::where('user_id', $token->id)->where('is_default', 0)->where('id', '!=', $customer_profile_id)->where('profile_code', '!=', 'contact-card')->count();

    $total = $non_default_profiles + $total;
    $data['limit_exceeded'] = ($total < $maxLimit) ? false : true;
    $data['limit_message'] = 'Customized profiles limit has been exceeded.';
    $data['success'] = TRUE;
    return $data;
}

function send_notification($token, $message)
{
    $API_ACCESS_KEY    = env('FCM_API_ACCESS_KEY');
    $tokens = array($token);

    // prep the bundle
    $msg = array(
        'body'         => $message,
        'title'        => config("app.name", ""),
        'subtitle'    => '',
        'tickerText' => '',
        'vibrate'    => 1,
        'sound'        => 1,
        'largeIcon'    => 'large_icon',
        'smallIcon'    => 'small_icon'
    );

    $fields     = array('registration_ids' => $tokens, 'notification' => $msg);
    $headers     = array('Authorization: key=' . $API_ACCESS_KEY, 'Content-Type: application/json');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result);
}

function devices()
{
    $data[] = ['id' => 's', 'title' => 'Sticker'];
    $data[] = ['id' => 'sc', 'title' => 'Sticker Custom'];
    $data[] = ['id' => 'c', 'title' => 'Card'];
    $data[] = ['id' => 'cc', 'title' => 'Card Custom'];
    $data[] = ['id' => 't', 'title' => 'Table'];
    $data[] = ['id' => 'tc', 'title' => 'Table Custom'];
    $data[] = ['id' => 'k', 'title' => 'Keychain'];
    $data[] = ['id' => 'kc', 'title' => 'Keychain Custom'];
    $data[] = ['id' => 'p', 'title' => 'Pet'];
    $data[] = ['id' => 'pc', 'title' => 'Pet Custom'];
    $data[] = ['id' => 'mc', 'title' => 'Metal Card'];
    $data[] = ['id' => 'bc', 'title' => 'Bamboo Card'];
    $data[] = ['id' => 'wb', 'title' => 'Wristband'];
    $data[] = ['id' => 'sb', 'title' => 'SOS Band'];
    $data[] = ['id' => 'ss', 'title' => 'SOS Sticker'];
    $data[] = ['id' => 'sk', 'title' => 'SOS Keychain'];
    $data[] = ['id' => 'ssc', 'title' => 'SOS Card'];

    return $data;
}

function unique_username($name = '')
{
    if ($name != '') {
        $name = str_replace('ä', 'ae', $name);
        $name = str_replace('ö', 'oe', $name);
        $name = str_replace('ü', 'ue', $name);
        $name = preg_replace("/[^A-Za-z0-9.]/", '', $name);

        $username = strtolower(str_replace(' ', '', $name)); // . '-' . rand(1, 999));
    } else {
        $username = strtolower(config("app.name", "") . '-' . uniqid(rand(1, 9)));
    }

    $user = User::where('username', $username)->count();
    if ($user == 0) {
        return $username;
    }

    return unique_username();
}

function email_split($email)
{
    $email_split = explode('@', $email);
    if (empty($email_split)) {
        $username = '';
    } else {
        $username = $email_split[0];
    }

    return $username;
}

function company_friendly_name($parent_id)
{
    $parent = User::where('id', $parent_id);
    if ($parent->count() == 0) {
        $company_friendly_name = '';
    } else {
        $parent = $parent->select('company_friendly_name')->first();
        $company_friendly_name = $parent->company_friendly_name;
    }

    return $company_friendly_name;
}

function parent_id_of_child_account($parent_id)
{
    $parent_id = $parent_id;

    $BusinessUserObj = BusinessUser::where('user_id', $parent_id)->first();
    if (!empty($BusinessUserObj)) {
        $parent_id = ($BusinessUserObj->parent_id != 0) ? $BusinessUserObj->parent_id : $parent_id;
    }

    return $parent_id;
}

function parent_id($token)
{
    $parent_id = $token->id;

    $BusinessUserObj = BusinessUser::where('user_id', $token->id)->first();
    if (!empty($BusinessUserObj)) {
        $parent_id = ($BusinessUserObj->parent_id != 0) ? $BusinessUserObj->parent_id : $token->id;
    }

    return $parent_id;
}

function detect_encoding($string)
{
  $list = ['UTF-8', 'ISO-8859-1', 'Windows-1252']; // Add more encodings as needed

  foreach ($list as $encoding) {
    if ($string === mb_convert_encoding(mb_convert_encoding($string, 'UTF-32', $encoding), 'UTF-8', $encoding)) {
      return $encoding;
    }
  }

  return null; // Encoding not detected
}

function map_field($mapped_fields, $row, $map_key, $row_key)
{
    return isset($mapped_fields[$map_key]) ? (isset($row[str_replace(' ', '-', strtolower($mapped_fields[$map_key]))]) ? $row[str_replace(' ', '-', strtolower($mapped_fields[$map_key]))] : '') : ''; //$row[$row_key];
}

function unique_profile_code($title)
{
    $profile_code = strtolower(str_replace(' ', '', mb_convert_encoding($title, 'UTF-8')) . '-' . rand(1, 999));
    $profile = Profile::where('profile_code', $profile_code)->count();
    if ($profile == 0) {
        return $profile_code;
    }

    return unique_profile_code(config("app.name", "") . '-' . mb_convert_encoding($title, 'UTF-8'));
}

function is_business_user()
{
    return 2;
}

function is_pro_user()
{
    return 1;
}

function is_grace_user()
{
    return -1;
}

function is_normal_user()
{
    return 0;
}

function custom_profile_limit()
{
    return 5;
}

function max_profile_limit()
{
    return 5;
}


function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE)
{
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = getUserIP(); //$_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }

    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), '', strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}

function playstore_urls()
{
    return '<a href="https://play.google.com/store/apps/details?id=com.ls.nfc.addmee"><img src="' . uploads_url() . 'img/google-play-badge.png" width="100"></a>
    <a href="https://apps.apple.com/gb/app/addmee/id1566147650"><img src="' . uploads_url() . 'img/app-store.png" width="100"></a>
    <br><br>';
}

function translate()
{
}

function checkProfileLimit($token, $request)
{
    $is_default = 0;
    if ($token->is_pro == is_pro_user() || $token->is_pro == is_grace_user()) {
        $default_profiles = CustomerProfile::where('profile_code', $request->profile_code)->where('user_id', $token->id)->where('is_default', 1)->count();
        if ($default_profiles >= 1) {
            $non_default_profiles = CustomerProfile::where('user_id', $token->id)->where('is_default', 0)->count();
            if (($non_default_profiles + 1) >= max_profile_limit()) {

                $data['success'] = FALSE;
                $data['message'] = 'Profiles limit has been exceeded.';
                $data['data'] = (object)[];
                return $data;
            } else {
                $is_default = 0;
            }
        } else {
            $is_default = 1;
        }
    } else if ($token->is_pro == is_normal_user() && !in_array($request->profile_code, is_free_profile_btn())) {
        $default_profiles = CustomerProfile::where('profile_code', $request->profile_code)->where('user_id', $token->id)->where('is_default', 1)->count();
        if ($default_profiles >= 1) {
            $data['success'] = FALSE;
            $data['message'] = 'Profile already added.'; //tested / working fine
            $data['data'] = (object)[];
            return $data;
        } else {
            $is_default = 1;
        }
    } else {
        $default_profiles = CustomerProfile::where('profile_code', $request->profile_code)->where('user_id', $token->id)->where('is_default', 1)->count();
        if ($default_profiles >= 1) {
            if (in_array($request->profile_code, is_free_profile_btn())) {
                if ($default_profiles >= 10) {
                    $data['success'] = FALSE;
                    $data['message'] = 'Profiles limit already reached.'; //tested / working fine
                    $data['data'] = (object)[];
                    return $data;
                } else {
                    $is_default = 1;
                }
            } else {
                $is_default = 0;
            }
        } else {
            $is_default = 1;
        }
    }

    $data['success'] = TRUE;
    $data['is_default'] = $is_default;
    return $data;
}

function is_free_profile_btn()
{
    return ['Notfallnummer'];
}

function addGlobalProfilesWithRequestTemplateId($token, $User, $email = '', $request_template_id)
{
    $token_id = $token == null ? Auth::user() : $token->id;
    $parent_id = parent_id($token);

    if ($email != '') {

        $Obj = new CustomerProfile;
        $Obj->profile_link = $email;
        $Obj->profile_code = 'email';
        $Obj->sequence = maxSequence($User->id);
        $Obj->is_business = 0;
        $Obj->user_id = $User->id;
        $Obj->created_by = $token_id;
        $Obj->created_at = Carbon::now();
        $Obj->save();

        $is_business = 0;
        $ContactCard = ContactCard::where('user_id', $User->id)->where('is_business', $is_business);
        if ($ContactCard->count() > 0) {
            $ContactCard = $ContactCard->first();
            $ContactCard->customer_profile_ids = trim($ContactCard->customer_profile_ids . ',' . $Obj->id, ',');
            $ContactCard->is_business = $is_business;
            $ContactCard->updated_by = $token_id;
            $ContactCard->save();
        } else {
            $ContactCard = new ContactCard;
            $ContactCard->customer_profile_ids = $Obj->id;
            $ContactCard->is_business = $is_business;
            $ContactCard->user_id = $User->id;
            $ContactCard->created_by = $token_id;
            $ContactCard->save();
        }
    }

    $profiles = CustomerProfile::where('global_id', '!=', 0)->where('user_id', $parent_id)->get();
    foreach ($profiles as $profile) {
        $global_id = $profile->global_id;
        if ($global_id != 0 && $global_id != '0' && $global_id != null && $global_id != 'null') {

            $Obj = new CustomerProfile;
            $Obj->title = $profile->title;
            $Obj->icon = $profile->icon;

            // new code
            $Obj->icon_svg_default = $profile->icon_svg_default ?? '';
            // end
            $Obj->file_image = $profile->file_image;
            $Obj->profile_link = $profile->profile_link;
            $Obj->profile_code = $profile->profile_code;
            $Obj->is_business = $profile->is_business;
            $Obj->global_id = $profile->global_id;
            $Obj->user_id = $User->id;
            $Obj->status = $profile->status;
            $Obj->sequence = maxSequence($User->id);
            $Obj->is_focused = $profile->is_focused;
            $Obj->created_by = $token_id;
            $Obj->created_at = Carbon::now();
            $Obj->save();

            $is_business = 0;
            $ContactCard = ContactCard::where('user_id', $User->id)->where('is_business', $is_business);
            if ($profile->profile_code != 'wifi') {
                if ($ContactCard->count() > 0) {
                    $ContactCard = $ContactCard->first();
                    $ContactCard->customer_profile_ids = trim($ContactCard->customer_profile_ids . ',' . $Obj->id, ',');
                    $ContactCard->is_business = $is_business;
                    $ContactCard->updated_by = $token_id;
                    $ContactCard->save();
                } else {
                    $ContactCard = new ContactCard;
                    $ContactCard->customer_profile_ids = $Obj->id;
                    $ContactCard->is_business = $is_business;
                    $ContactCard->user_id = $User->id;
                    $ContactCard->created_by = $token_id;
                    $ContactCard->save();
                }
            }
        }
    }

    $UserSettingsObjUpdate = [];

    if (!empty($request_template_id) && $request_template_id != null && $request_template_id != '') {

        $nonDefaultTemplate = UserTemplate::where('user_id', $parent_id)->where('id', $request_template_id);
        if ($nonDefaultTemplate->count() >= 1) {
            $nonDefaultTemplate = $nonDefaultTemplate->first();
            $member_ids[] = $User->id;
            $TemplateController = new TemplateController;
            $UserSettingsObjUpdate = $TemplateController->assign_template_to_member($member_ids, $parent_id, $nonDefaultTemplate->id, $token);
        }
    } else {
        $defaultTemplate = UserTemplate::where('user_id', $parent_id)->where('is_default', 1);
        // pre_print($defaultTemplate->count());
        if ($defaultTemplate->count() >= 1) {
            $defaultTemplate = $defaultTemplate->first();
            $member_ids[] = $User->id;
            // pre_print($member_ids);

            $TemplateController = new TemplateController;
            $UserSettingsObjUpdate = $TemplateController->assign_template_to_member($member_ids, $parent_id, $defaultTemplate->id, $token);
        }
    }

    return $UserSettingsObjUpdate;
}

function addGlobalProfiles($token, $User, $email = '', $template_id = 0)
{
    $token_id = $token == null ? Auth::user() : $token->id;
    $parent_id = parent_id($token);

    if ($email != '') {

        $Obj = new CustomerProfile;
        $Obj->profile_link = $email;
        $Obj->profile_code = 'email';
        $Obj->sequence = maxSequence($User->id);
        $Obj->is_business = 0;
        $Obj->user_id = $User->id;
        $Obj->created_by = $token_id;
        $Obj->created_at = Carbon::now();
        $Obj->save();

        $is_business = 0;
        $ContactCard = ContactCard::where('user_id', $User->id)->where('is_business', $is_business);
        if ($ContactCard->count() > 0) {
            $ContactCard = $ContactCard->first();
            $ContactCard->customer_profile_ids = trim($ContactCard->customer_profile_ids . ',' . $Obj->id, ',');
            $ContactCard->is_business = $is_business;
            $ContactCard->updated_by = $token_id;
            $ContactCard->save();
        } else {
            $ContactCard = new ContactCard;
            $ContactCard->customer_profile_ids = $Obj->id;
            $ContactCard->is_business = $is_business;
            $ContactCard->user_id = $User->id;
            $ContactCard->created_by = $token_id;
            $ContactCard->save();
        }
    }

    $profiles = CustomerProfile::where('global_id', '!=', 0)->where('user_id', $parent_id)->get();
    foreach ($profiles as $profile) {
        $global_id = $profile->global_id;
        if ($global_id != 0 && $global_id != '0' && $global_id != null && $global_id != 'null') {

            $Obj = new CustomerProfile;
            $Obj->title = $profile->title;
            $Obj->icon = $profile->icon;
            // new
            $Obj->icon_svg_default = $profile->icon_svg_default ?? '';
            // end
            $Obj->file_image = $profile->file_image;
            $Obj->profile_link = $profile->profile_link;
            $Obj->profile_code = $profile->profile_code;
            $Obj->is_business = $profile->is_business;
            $Obj->global_id = $profile->global_id;
            $Obj->user_id = $User->id;
            $Obj->status = $profile->status;
            $Obj->sequence = maxSequence($User->id);
            $Obj->is_focused = $profile->is_focused;
            $Obj->created_by = $token_id;
            $Obj->created_at = Carbon::now();
            $Obj->save();

            $is_business = 0;
            $ContactCard = ContactCard::where('user_id', $User->id)->where('is_business', $is_business);
            if ($profile->profile_code != 'wifi') {
                if ($ContactCard->count() > 0) {
                    $ContactCard = $ContactCard->first();
                    $ContactCard->customer_profile_ids = trim($ContactCard->customer_profile_ids . ',' . $Obj->id, ',');
                    $ContactCard->is_business = $is_business;
                    $ContactCard->updated_by = $token_id;
                    $ContactCard->save();
                } else {
                    $ContactCard = new ContactCard;
                    $ContactCard->customer_profile_ids = $Obj->id;
                    $ContactCard->is_business = $is_business;
                    $ContactCard->user_id = $User->id;
                    $ContactCard->created_by = $token_id;
                    $ContactCard->save();
                }
            }
        }
    }

    $UserSettingsObjUpdate = [];

    $defaultTemplate = null;

    if ($template_id != 0) {
        $defaultTemplate = UserTemplate::where('id', $template_id)->first();
    }

    if (!$defaultTemplate) {
        // $defaultTemplate = UserTemplate::where('user_id', $parent_id)->where('is_default', 1)->first();
    }

    if ($defaultTemplate && $defaultTemplate->count() >= 1) {
        $member_ids[] = $User->id;
        // pre_print($member_ids);

        $TemplateController = new TemplateController;
        $UserSettingsObjUpdate = $TemplateController->assign_template_to_member($member_ids, $parent_id, $defaultTemplate->id, $token);
    }

    // if ($defaultTemplate->count() == 0) {
    //     $nonDefaultTemplate = UserTemplate::where('user_id', $parent_id);
    //     if ($nonDefaultTemplate->count() >= 1) {
    //         $nonDefaultTemplate = $nonDefaultTemplate->first();
    //         $member_ids[] = $User->id;
    //         // pre_print($member_ids);

    //         $TemplateController = new TemplateController;
    //         $UserSettingsObjUpdate = $TemplateController->assign_template_to_member($member_ids, $parent_id, $nonDefaultTemplate->id, $token);
    //     } else {
    //     }
    // }

    return $UserSettingsObjUpdate;
}

function addGlobalProfilesBp($token, $User, $email = '', $template_id = 0)
{
    $token_id = $token == null ? Auth::user() : $token->id;
    $parent_id = parent_id($token);

    $profiles = CustomerProfile::where('global_id', '!=', 0)->where('user_id', $parent_id)->get();
    foreach ($profiles as $profile) {
        $global_id = $profile->global_id;
        if ($global_id != 0 && $global_id != '0' && $global_id != null && $global_id != 'null') {

            $Obj = new CustomerProfile;
            $Obj->title = $profile->title;
            $Obj->icon = $profile->icon;
            // new
            $Obj->icon_svg_default = $profile->icon_svg_default ?? '';
            // end
            $Obj->file_image = $profile->file_image;
            $Obj->profile_link = $profile->profile_link;
            $Obj->profile_code = $profile->profile_code;
            $Obj->is_business = $profile->is_business;
            $Obj->global_id = $profile->global_id;
            $Obj->user_id = $User->id;
            $Obj->status = $profile->status;
            $Obj->sequence = maxSequence($User->id);
            $Obj->is_focused = $profile->is_focused;
            $Obj->created_by = $token_id;
            $Obj->created_at = Carbon::now();
            $Obj->save();

            $is_business = 0;
            $ContactCard = ContactCard::where('user_id', $User->id)->where('is_business', $is_business);
            if ($profile->profile_code != 'wifi') {
                if ($ContactCard->count() > 0) {
                    $ContactCard = $ContactCard->first();
                    $ContactCard->customer_profile_ids = trim($ContactCard->customer_profile_ids . ',' . $Obj->id, ',');
                    $ContactCard->is_business = $is_business;
                    $ContactCard->updated_by = $token_id;
                    $ContactCard->save();
                } else {
                    $ContactCard = new ContactCard;
                    $ContactCard->customer_profile_ids = $Obj->id;
                    $ContactCard->is_business = $is_business;
                    $ContactCard->user_id = $User->id;
                    $ContactCard->created_by = $token_id;
                    $ContactCard->save();
                }
            }
        }
    }
    $UserSettingsObjUpdate = [];

    $defaultTemplate = null;

    if ($template_id != 0) {
        $defaultTemplate = UserTemplate::where('id', $template_id)->first();
    }

    if (!$defaultTemplate) {
        // $defaultTemplate = UserTemplate::where('user_id', $parent_id)->where('is_default', 1)->first();
    }

    if ($defaultTemplate && $defaultTemplate->count() >= 1) {
        $member_ids[] = $User->id;
        // pre_print($member_ids);

        $TemplateController = new TemplateController;
        $UserSettingsObjUpdate = $TemplateController->assign_template_to_member($member_ids, $parent_id, $defaultTemplate->id, $token);
    }

    // if ($defaultTemplate->count() == 0) {
    //     $nonDefaultTemplate = UserTemplate::where('user_id', $parent_id);
    //     if ($nonDefaultTemplate->count() >= 1) {
    //         $nonDefaultTemplate = $nonDefaultTemplate->first();
    //         $member_ids[] = $User->id;
    //         // pre_print($member_ids);

    //         $TemplateController = new TemplateController;
    //         $UserSettingsObjUpdate = $TemplateController->assign_template_to_member($member_ids, $parent_id, $nonDefaultTemplate->id, $token);
    //     } else {
    //     }
    // }

    return $UserSettingsObjUpdate;
}

function resetProfilesSequence($user_id)
{
    $profiles = CustomerProfile::where('user_id', $user_id)->where('profile_code', '!=', 'contact-card');
    if ($profiles->count() > 0) {
        $profiles = $profiles->orderBy('sequence', 'ASC')->get();
        foreach ($profiles as $idx => $profile) {
            $profile->sequence = $idx;
            $profile->save();
        }
    }
}

function arrayValuesToInt($array)
{
    if (count($array) == 0) {
        return [];
    }

    $newArray = [];
    foreach ($array as $arr) {
        $newArray[] = (int) $arr;
    }

    return $newArray;
}

function createUserSettings($User, $token)
{
    $total = UserSettings::where('user_id', $User->id);
    if ($total->count() > 0) {
        $UserSettings = $total->first();
    } else {
        $UserSettings = new UserSettings();
    }

    $UserSettings->bg_color  = 'rgba(255, 255, 255, 1)';
    $UserSettings->btn_color = 'rgba(0, 0, 0, 1)';
    $UserSettings->photo_border_color = 'rgba(255, 255, 255, 1)';
    $UserSettings->section_color = 'rgba(255, 255, 255, 0)';
    $UserSettings->text_color = 'rgba(17, 24, 3, 1)';
    $UserSettings->show_contact = 1;
    $UserSettings->show_connect = 1;
    $UserSettings->capture_lead = 0;
    $UserSettings->color_link_icons = 0;
    $UserSettings->user_id = $User->id;
    $UserSettings->created_by = $token->id;
    $UserSettings->save();

    $UserSettings['connect_button'] = (int) $UserSettings->show_connect;
    $UserSettings['save_contact_button'] = (int) $UserSettings->show_contact;
    $UserSettings['open_direct'] = (int) $User->open_direct;

    unset($UserSettings->created_by, $UserSettings->updated_at, $UserSettings->created_at);

    return $UserSettings;
}

function updatedMmemberSettings($updated_member, $token)
{
    $total = UserSettings::where('user_id', $updated_member->id);
    if ($total->count() > 0) {
        $UpdatedMemberSettings = $total->first();
    } else {
        $UpdatedMemberSettings = new UserSettings();
    }

    $UpdatedMemberSettings->bg_color  = $UpdatedMemberSettings->bg_color;
    $UpdatedMemberSettings->btn_color = $UpdatedMemberSettings->btn_color;
    $UpdatedMemberSettings->photo_border_color = $UpdatedMemberSettings->photo_border_color;
    $UpdatedMemberSettings->section_color = $UpdatedMemberSettings->section_color;
    $UpdatedMemberSettings->text_color = $UpdatedMemberSettings->text_color;
    $UpdatedMemberSettings->show_contact = $UpdatedMemberSettings->show_contact;
    $UpdatedMemberSettings->show_connect = $UpdatedMemberSettings->show_connect;
    $UpdatedMemberSettings->capture_lead = $UpdatedMemberSettings->capture_lead;
    $UpdatedMemberSettings->color_link_icons = $UpdatedMemberSettings->color_link_icons;
    $UpdatedMemberSettings->user_id = $updated_member->id;
    $UpdatedMemberSettings->created_by = $token->id;
    $UpdatedMemberSettings->save();

    $UpdatedMemberSettings['connect_button'] = (int) $UpdatedMemberSettings->show_connect;
    $UpdatedMemberSettings['save_contact_button'] = (int) $UpdatedMemberSettings->show_contact;
    $UpdatedMemberSettings['open_direct'] = (int) $updated_member->open_direct;
    unset(
        $UpdatedMemberSettings->created_by,
        $UpdatedMemberSettings->updated_at,
        $UpdatedMemberSettings->created_at,
        $UpdatedMemberSettings->language,
        $UpdatedMemberSettings->is_editable,
        $UpdatedMemberSettings->bg_image,
        $UpdatedMemberSettings->control_buttons_locked,
        $UpdatedMemberSettings->profile_opens_locked,
        $UpdatedMemberSettings->colors_custom_locked,
        $UpdatedMemberSettings->user_old_data,
        $UpdatedMemberSettings->settings_old_data,
        $UpdatedMemberSettings->updated_by,
        $UpdatedMemberSettings->id
    );

    return $UpdatedMemberSettings;
}

function updateUserSettings($user_id, $token, $UserTemplate, $setAllData = true)
{
    $UserSettings = UserSettings::where('user_id', $user_id);
    if ($UserSettings->count() > 0) {
        $UserSettings = $UserSettings->first();
    } else {
        $UserSettings = new UserSettings();
    }

    if ($setAllData == true) {
        $UserSettings->is_editable =  $UserTemplate->is_editable == NULL ? 1 : $UserTemplate->is_editable;
        $UserSettings->bg_image = $UserTemplate->background_image;
    }

    if ($UserTemplate->colors_custom_locked == 1) {
        $UserSettings->section_color = $UserTemplate->section_color;
        $UserSettings->bg_color = isset($UserTemplate->profile_color) ? $UserTemplate->profile_color : $UserTemplate->background_color;
        $UserSettings->btn_color = $UserTemplate->button_color;
        $UserSettings->text_color = $UserTemplate->text_color;
        $UserSettings->photo_border_color = isset($UserTemplate->border_color) ? $UserTemplate->border_color : $UserTemplate->photo_border_color;
    }
    $UserSettings->color_link_icons = $UserTemplate->color_link_icons == null ? 0 : $UserTemplate->color_link_icons;

    if ($UserTemplate->profile_opens_locked == 1) {
        $UserSettings->capture_lead = $UserTemplate->capture_lead;
    }

    if ($UserTemplate->control_buttons_locked == 1) {
        $UserSettings->show_contact = $UserTemplate->show_contact;
        $UserSettings->show_connect = $UserTemplate->show_connect;
    }
    $UserSettings->updated_by = !empty($token) ? $token->id : 0;

    if (isset($UserTemplate->colors_custom_locked)) {
        $UserSettings->colors_custom_locked = $UserTemplate->colors_custom_locked;
    }

    if (isset($UserTemplate->control_buttons_locked)) {
        $UserSettings->control_buttons_locked = $UserTemplate->control_buttons_locked;
    }

    if (isset($UserTemplate->profile_opens_locked)) {
        $UserSettings->profile_opens_locked = $UserTemplate->profile_opens_locked;
    }

    if (isset($UserTemplate->user_old_data)) {
        $UserSettings->user_old_data = $UserTemplate->user_old_data;
    }

    if (isset($UserTemplate->settings_old_data)) {
        $UserSettings->settings_old_data = $UserTemplate->settings_old_data;
    }

    $UserSettings->save();
    // if (isset($UserTemplate->colors_custom_locked)) {
    //     pre_print($UserTemplate);
    // }
    return $UserSettings;
}

function userSettingsFromTemplate($user_id, $token, $UserTemplate, $setAllData = true)
{
    $UserSettings = (object)[];
    $UserSettings->id = NULL;
    $UserSettings->bg_image = NULL;
    $UserSettings->bg_color = NULL;
    $UserSettings->btn_color = NULL;
    $UserSettings->text_color = NULL;
    $UserSettings->capture_lead = 0;
    $UserSettings->show_contact = 1;
    $UserSettings->show_connect = 1;
    $UserSettings->is_editable = 1;
    $UserSettings->section_color = NULL;
    $UserSettings->photo_border_color = NULL;
    $UserSettings->colors_custom_locked = 0;
    $UserSettings->control_buttons_locked = 0;
    $UserSettings->profile_opens_locked = 0;

    if ($setAllData == true && $UserTemplate != null) {
        $UserSettings->is_editable =  $UserTemplate->is_editable == NULL ? 1 : $UserTemplate->is_editable;
        $UserSettings->bg_image = $UserTemplate->background_image;
    }

    if ($UserTemplate->colors_custom_locked == 1) {
        $UserSettings->section_color = $UserTemplate->section_color;
        $UserSettings->bg_color = isset($UserTemplate->profile_color) ? $UserTemplate->profile_color : $UserTemplate->background_color;
        $UserSettings->btn_color = $UserTemplate->button_color;
        $UserSettings->text_color = $UserTemplate->text_color;
        $UserSettings->photo_border_color = isset($UserTemplate->border_color) ? $UserTemplate->border_color : $UserTemplate->photo_border_color;
    }

    $UserSettings->color_link_icons = $UserTemplate->color_link_icons == null ? 0 : $UserTemplate->color_link_icons;
    // $UserSettings->color_link_icons = $UserTemplate->color_link_icons == null ? 0 : 0;

    if ($UserTemplate->profile_opens_locked == 1) {
        $UserSettings->capture_lead = $UserTemplate->capture_lead;
    }

    if ($UserTemplate->control_buttons_locked == 1) {
        $UserSettings->show_contact = $UserTemplate->show_contact;
        $UserSettings->show_connect = $UserTemplate->show_connect;
    }

    if (isset($UserTemplate->colors_custom_locked)) {
        $UserSettings->colors_custom_locked = $UserTemplate->colors_custom_locked;
    }

    if (isset($UserTemplate->control_buttons_locked)) {
        $UserSettings->control_buttons_locked = $UserTemplate->control_buttons_locked;
    }

    if (isset($UserTemplate->profile_opens_locked)) {
        $UserSettings->profile_opens_locked = $UserTemplate->profile_opens_locked;
    }
    $UserSettings->user_id = $user_id;
    //pre_print($UserTemplate);
    return $UserSettings;
}

function newlyCreatedLinks($User, $token)
{
    $links = [];
    $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.title as cp_title', 'cp.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible', 'cp.is_focused as is_highlighted', 'cp.global_id', \DB::raw('COALESCE(cp.icon_svg_default, p.icon_svg_default) as icon_svg'), 'p.id as link_type_id'); //'cp.is_direct as open_direct',
    $query->leftJoin('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
    $query = $query->where('cp.user_id', $User->id);
    $profiles = $query->get();

    $Home = new HomeController;
    $profiles = $Home->profile_meta($profiles, $token);
    // pre_print($profiles);
    if (!empty($profiles)) {
        foreach ($profiles as $i => $profile) {
            if ($profile->profile_code == "contact-card") {
                unset($profiles[$i]);
                continue;
            }

            $ObjCP = TemplateAssignee::where('customer_profile_id', $profile->id);
            if ($ObjCP->count() > 0) {
                $ObjectCP = $ObjCP->first();
                $CustomerProfileTemplate = CustomerProfileTemplate::where('id', $ObjectCP->customer_profile_template_id);

                $profiles[$i]->template_id = $ObjectCP->user_template_id;
                $profiles[$i]->template_link_id = $ObjectCP->customer_profile_template_id;
                $profiles[$i]->is_unique = $CustomerProfileTemplate->count() > 0 ? (int) $CustomerProfileTemplate->first()->is_unique : 0;
            } else {
                $profiles[$i]->template_id = NULL;
                $profiles[$i]->template_link_id = NULL;
                $profiles[$i]->is_unique = 0;
            }

            $profiles[$i]->icon_url = $profiles[$i]->icon;
            $profiles[$i]->value = $profiles[$i]->profile_link_value;
            $profiles[$i]->href = $profiles[$i]->profile_link;
            $profiles[$i]->global_id = $profiles[$i]->global_id == '0' || $profiles[$i]->global_id == 0 ? NULL : (int)$profiles[$i]->global_id;

            unset($profiles[$i]->file_image, $profiles[$i]->status, $profiles[$i]->created_by, $profiles[$i]->created_at, $profiles[$i]->updated_by, $profiles[$i]->updated_at, $profiles[$i]->is_direct, $profiles[$i]->added_to_contact_card, $profiles[$i]->icon, $profiles[$i]->profile_link_value, $profiles[$i]->profile_link, $profiles[$i]->is_business, $profiles[$i]->title_de, $profiles[$i]->base_url, $profiles[$i]->title_de, $profiles[$i]->template_link_id);

            $links[] = $profiles[$i];
        }
    }

    return $links;
}
function newlyCreatedLinksBp($User, $token)
{
    $links = [];
    $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.title as cp_title', 'cp.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible', 'cp.is_focused as is_highlighted', 'cp.global_id', \DB::raw('COALESCE(cp.icon_svg_default, p.icon_svg_default) as icon_svg'), 'p.id as link_type_id', 'p.icon_svg_default as profile_icon_svg_default'); //'cp.is_direct as open_direct',
    $query->leftJoin('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
    $query = $query->where('cp.user_id', $User->id);
    $profiles = $query->get();

    $Home = new HomeController;
    $profiles = $Home->profile_meta($profiles, $token);
    // pre_print($profiles);
    if (!empty($profiles)) {
        foreach ($profiles as $i => $profile) {
            if ($profile->profile_code == "contact-card") {
                unset($profiles[$i]);
                continue;
            }

            $ObjCP = TemplateAssignee::where('customer_profile_id', $profile->id);
            if ($ObjCP->count() > 0) {
                $ObjectCP = $ObjCP->first();
                $CustomerProfileTemplate = CustomerProfileTemplate::where('id', $ObjectCP->customer_profile_template_id);

                $profiles[$i]->template_id = $ObjectCP->user_template_id;
                $profiles[$i]->template_link_id = $ObjectCP->customer_profile_template_id;
                $profiles[$i]->is_unique = $CustomerProfileTemplate->count() > 0 ? (int) $CustomerProfileTemplate->first()->is_unique : 0;
            } else {
                $profiles[$i]->template_id = NULL;
                $profiles[$i]->template_link_id = NULL;
                $profiles[$i]->is_unique = 0;
            }

            $profiles[$i]->icon_url = $profiles[$i]->icon;
            $profiles[$i]->value = $profiles[$i]->profile_link_value;
            $profiles[$i]->href = $profiles[$i]->profile_link;
            $profiles[$i]->global_id = $profiles[$i]->global_id == '0' || $profiles[$i]->global_id == 0 ? NULL : (int)$profiles[$i]->global_id;

            unset($profiles[$i]->file_image, $profiles[$i]->status, $profiles[$i]->created_by, $profiles[$i]->created_at, $profiles[$i]->updated_by, $profiles[$i]->updated_at, $profiles[$i]->is_direct, $profiles[$i]->added_to_contact_card, $profiles[$i]->icon, $profiles[$i]->profile_link_value, $profiles[$i]->profile_link, $profiles[$i]->is_business, $profiles[$i]->title_de, $profiles[$i]->base_url, $profiles[$i]->title_de, $profiles[$i]->template_link_id);

            $links[] = $profiles[$i];
        }
    }

    return $links;
}

function newlyUpdatatedLinks($updated_member, $token)
{
    $updateLinks = [];
    $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.title as cp_title', 'cp.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible', 'cp.is_focused as is_highlighted', 'cp.global_id', \DB::raw('COALESCE(cp.icon_svg_default, p.icon_svg_default) as icon_svg'), 'p.id as link_type_id'); //'cp.is_direct as open_direct',
    $query->leftJoin('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
    $query = $query->where('cp.user_id', $updated_member->id);
    $profiles = $query->get();

    $Home = new HomeController;
    $profiles = $Home->profile_meta($profiles, $token);
    // pre_print($profiles);
    if (!empty($profiles)) {
        foreach ($profiles as $i => $profile) {
            if ($profile->profile_code == "contact-card") {
                unset($profiles[$i]);
                continue;
            }

            $ObjCP = TemplateAssignee::where('customer_profile_id', $profile->id);
            if ($ObjCP->count() > 0) {
                $ObjectCP = $ObjCP->first();
                $CustomerProfileTemplate = CustomerProfileTemplate::where('id', $ObjectCP->customer_profile_template_id);

                $profiles[$i]->template_id = $ObjectCP->user_template_id;
                $profiles[$i]->template_link_id = $ObjectCP->customer_profile_template_id;
                $profiles[$i]->is_unique = $CustomerProfileTemplate->count() > 0 ? (int) $CustomerProfileTemplate->first()->is_unique : 0;
            } else {
                $profiles[$i]->template_id = NULL;
                $profiles[$i]->template_link_id = NULL;
                $profiles[$i]->is_unique = 0;
            }

            $profiles[$i]->icon_url = $profiles[$i]->icon;
            $profiles[$i]->value = $profiles[$i]->profile_link_value;
            $profiles[$i]->href = $profiles[$i]->profile_link;
            $profiles[$i]->global_id = $profiles[$i]->global_id == '0' || $profiles[$i]->global_id == 0 ? NULL : (int)$profiles[$i]->global_id;

            unset($profiles[$i]->file_image, $profiles[$i]->status, $profiles[$i]->created_by, $profiles[$i]->created_at, $profiles[$i]->updated_by, $profiles[$i]->updated_at, $profiles[$i]->is_direct, $profiles[$i]->added_to_contact_card, $profiles[$i]->icon, $profiles[$i]->profile_link_value, $profiles[$i]->profile_link, $profiles[$i]->is_business, $profiles[$i]->title_de, $profiles[$i]->base_url, $profiles[$i]->title_de);

            $updateLinks[] = $profiles[$i];
        }
    }

    return $updateLinks;
}

function true_false($value)
{
    return $value == 0 || $value == '' || '0' ? false : true;
}

function template_links_ids($member_ids)
{
    $member_links = [];
    if (!empty($member_ids)) {
        $links = TemplateAssignee::whereIn('user_id', $member_ids)->where('customer_profile_id', '!=', 0)->get();
        // pre_print(json_decode(json_encode($link)));
        if (!empty($links)) {
            foreach ($links as $link) {
                $profile = CustomerProfile::where('id', $link->customer_profile_id)->first();
                $member_links[] = $profile->id;
            }
        }
    }

    return $member_links;
}

function template_links($member_ids, $customer_profile_template_id = 0, $request = null)
{
    $member_links = [];
    if (!empty($member_ids)) {
        if ($customer_profile_template_id != 0) {
            $links = TemplateAssignee::whereIn('user_id', $member_ids)->where('customer_profile_template_id', $customer_profile_template_id)->get();
        } else {
            $links = TemplateAssignee::whereIn('user_id', $member_ids)->where('customer_profile_id', '!=', 0)->get();
        }
        // pre_print(json_decode(json_encode($link)));
        if (!empty($links)) {
            foreach ($links as $link) {
                $profile = CustomerProfile::where('id', $link->customer_profile_id)->first();
                $ProfileType = Profile::where('profile_code', $profile->profile_code)->first();
                $CustomerProfileTemplate = CustomerProfileTemplate::where('id', $link->customer_profile_template_id);
                // Copied from Template Link
                $profile->icon_url = $profile->icon != '' ? icon_url() . $profile->icon : '';
                $profile->icon_svg = $profile->icon_svg_default != '' ? $profile->icon_svg_default : $ProfileType->icon_svg_default;

                if ($CustomerProfileTemplate->count() > 0) {
                    $recc = $CustomerProfileTemplate->first();
                    if ($recc->icon_svg != '') {
                        $profile->icon_svg = $recc->icon_svg;
                    }
                }

                $profile->value = $profile->profile_link;
                $profile->profile_link = ($profile->profile_code != 'file') ? $ProfileType->base_url . $profile->profile_link : $profile->file_image;
                $profile->href = $profile->profile_link;
                $profile->is_highlighted = true_false($profile->is_focused == null ? 0 : $profile->is_focused);
                $profile->visible = true_false($profile->status);
                $profile->type = $ProfileType->type;
                // This should be the sequence in Members Links (i.e. Last)
                $profile->sequence = (int)$profile->sequence;
                // $profile->template_link_id = (int)$link->customer_profile_template_id;
                $profile->template_id = (int)$link->user_template_id;
                $profile->global_id = null;
                // $profile->is_pro = $ProfileType->is_pro;
                $profile->is_unique = $CustomerProfileTemplate->count() > 0 ? (int) $CustomerProfileTemplate->first()->is_unique : 0;

                if ($request != null) {
                    $profile->icon_svg = icon_svg_default($request, $profile->profile_code);
                }

                unset($profile->icon, $profile->profile_link, $profile->user_template_id, $profile->file_image, $profile->is_business, $profile->is_focused, $profile->created_by, $profile->created_at, $profile->updated_by, $profile->updated_at, $profile->is_default, $profile->is_direct, $profile->status, $profile->icon_svg_default);

                $member_links[] = $profile;
                //
            }
        }
    }

    return $member_links;
}

function parent_status($token)
{
    $parent_id = parent_id($token);
    $User = User::where('id', $parent_id);
    return $User->count() > 0 ? $User->first()->status : 1;
}

function getAssigneeIDs($template_id)
{
    $assignees_ids = TemplateAssignee::select(\DB::raw('GROUP_CONCAT(user_id) as user_id'))->where('user_template_id', $template_id)->where('customer_profile_id', 0)->first();
    $assignees_ids = $assignees_ids->user_id != null ? explode(',', $assignees_ids->user_id) : [];

    return $assignees_ids;
}

function check_user_type_status($User)
{
    $user_type = 'user';
    $user_status = -1;
    $BusinessUser = BusinessUser::where('user_id', $User->id);
    if ($BusinessUser->count() > 0) {
        $BusinessUser = $BusinessUser->first();
        $parent_id = $BusinessUser->parent_id;
        if ($parent_id != 0) {
            $parentUser = User::where('id', $parent_id);
            if ($parentUser->count() > 0) {
                $parentUser = $parentUser->first();
                $user_status = $parentUser->status;
            }
            $user_type = 'business_user';
        } else if ($parent_id == 0) {
            $user_type = 'business_admin';
        }
    }

    return ['user_type' => $user_type, 'user_status' => $user_status];
}
