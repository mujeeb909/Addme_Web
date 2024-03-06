<?php

/** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\HomeController;

use App\Http\Controllers\Controller;
use App\Models\BusinessInfo;
use App\Models\BusinessUser;
use App\Models\ContactCard;
use App\Models\CustomerProfile;
use App\Models\CustomerProfileTemplate;
use App\Models\DeleteAccount;
use App\Models\Profile;
use App\Models\TapsViews;
use App\Models\TemplateAssignee;
use App\Models\UniqueCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mail;
use \Mailjet\Resources;

use App\Models\User;
use App\Models\UserNote;
use App\Models\UserSettings;
use DateTime;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Hashids\Hashids;

use function PHPUnit\Framework\isEmpty;

class CustomerController extends Controller
{
    public function create_accounts(Request $request)
    {
        $validations['data'] = 'required|string';

        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $messages = json_decode(json_encode($validator->messages()), true);
            $i = 0;
            foreach ($messages as $key => $val) {
                $data['errors'][$i]['error'] = $val[0];
                $data['errors'][$i]['field'] = $key;
                $i++;
            }

            $data['success'] = FALSE;
            $data['message'] = 'Required data is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $users_list = $new_member_ids = [];
        $parent_id = parent_id($token);

        $validators = $this->validateUserJsonData($request->data, $parent_id);
        if (!empty($validators)) {
            return $validators;
        }

        $records = json_decode($request->data);
        $company_friendly_name = company_friendly_name($parent_id);
        // pre_print($records);

        $account_limit = $this->account_limit($parent_id, $records);
        if ($account_limit != 1) {
            $data['success'] = FALSE;
            $data['message'] = ($account_limit == 2) ? 'Company account limit reached. Please get in contact with the AddMee Customer Care.' : 'Company account limit reached. Please get in contact with the AddMee Customer Care.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        if (!empty($records)) {
            foreach ($records as $rec) {

                $password = Str::random(8);

                $User = new User;
                $User->first_name = isset($rec->first_name) ? $rec->first_name : NULL;
                $User->last_name = isset($rec->last_name) ? $rec->last_name : NULL;
                $User->email = $rec->email;
                $User->username = unique_username($company_friendly_name . email_split($User->email));
                $User->password = bcrypt($password);
                $User->status = 1;
                $User->is_pro = is_business_user();
                $User->allow_data_usage = 0;
                $User->device_type = 0;
                $User->device_id = 0;
                $User->vcode = rand(111111, 999999);;
                $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
                $User->created_at = Carbon::now();
                $User->created_by = $token->id;
                $User->privacy_policy_date = Carbon::now();
                $User->license_date = Carbon::now();
                $User->save();

                $Home = new UserController;
                $Home->add_contact_card_profile($User);
                unset($User->vcode, $User->vcode_expiry, $User->access_token);

                $BusinessUser = new BusinessUser();
                $BusinessUser->user_id = $User->id;
                $BusinessUser->parent_id = $parent_id;
                $BusinessUser->account_limit = 0;
                $BusinessUser->domain = NULL;
                $BusinessUser->user_role = 'user'; //$request->user_role;
                $BusinessUser->created_by = $token->id;
                $BusinessUser->created_at = Carbon::now();
                $BusinessUser->save();

                // create profile
                addGlobalProfiles($token, $User, $rec->email);
                // end

                // start
                $template = anyTemplateAssigned($User->id);
                $User = UserObj($User, 0, $template);
                $new_member_ids[] = $User->id;
                $users_list[] = $User;
                // end

                // send email
                if (isset($_POST['language']) && trim($_POST['language']) == 'de') {
                    $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
                    Welcome to the ' . config("app.name", "") . '.<br><br>
                    This is to provide you with your credentials to log in.<br><br>
                    Your password is: <i>' . $password . '</i><br><br>
                    After you have logged in for the first time, you will be asked to create your own password.<br><br>
                    Good luck<br><br>
                    ' . playstore_urls() . '
                    Your ' . config("app.name", "") . ' customer service';
                    // echo $html;exit;
                    $subject = "You've been invited to join your team on " . config("app.name", "") . "";
                } else {
                    $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
                    Welcome to the ' . config("app.name", "") . '.<br><br>
                    This is to provide you with your credentials to log in.<br><br>
                    Your password is: <i>' . $password . '</i><br><br>
                    After you have logged in for the first time, you will be asked to create your own password.<br><br>
                    Good luck<br><br>
                    ' . playstore_urls() . '
                    Your ' . config("app.name", "") . ' customer service';
                    // echo $html;exit;
                    $subject = "You've been invited to join your team on " . config("app.name", "") . "";
                }

                $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
                $body = [
                    'Messages' => [
                        [
                            'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                            'To' => [['Email' => $User->email, 'Name' => $User->first_name . ' ' . $User->last_name]],
                            'Subject' => $subject,
                            'TextPart' => $subject,
                            'HTMLPart' => $html,
                            'CustomID' => config("app.name", "")
                        ]
                    ]
                ];

                $mj->post(Resources::$Email, ['body' => $body]);
                // send email ends
            }
        }

        $data['success'] = TRUE;
        $data['message'] = 'Accounts created successfully.';
        $data['data'] = array('users' => $users_list);
        return response()->json($data, 201);
    }

    public function create_member_accounts(Request $request)
    {
        $UserSettingsObj = [];
        $validations['emails'] = 'required|string';
        $validations['send_invites'] = 'required|string';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $messages = json_decode(json_encode($validator->messages()), true);
            $i = 0;
            foreach ($messages as $key => $val) {
                $data['errors'][$i]['error'] = $val[0];
                $data['errors'][$i]['field'] = $key;
                $i++;
            }

            $data['success'] = FALSE;
            $data['message'] = 'Required data is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $users_list = $new_member_ids = $links = $settings = [];
        $parent_id = parent_id($token);

        $validators = $this->validateUserData($request->emails, $parent_id);
        if (!empty($validators)) {
            return $validators;
        }

        $records = explode(',', $request->emails);
        // pre_print($records);

        $account_limit = $this->account_limit($parent_id, $records);
        if ($account_limit != 1) {
            $data['success'] = FALSE;
            $data['message'] = ($account_limit == 2) ? 'Company account limit reached. Please get in contact with the AddMee Customer Care.' : 'Company account limit reached. Please get in contact with the AddMee Customer Care.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $company_friendly_name = company_friendly_name($parent_id);

        if (!empty($records)) {
            foreach ($records as $email) {

                $password = Str::random(8);

                $User = new User;
                $User->first_name = NULL;
                $User->last_name = NULL;
                $User->email = $email;
                $User->username = unique_username($company_friendly_name . email_split($email));
                $User->password = bcrypt($password);
                $User->status = 1;
                $User->is_pro = is_business_user();
                $User->allow_data_usage = 0;
                $User->device_type = 0;
                $User->first_login = 1;
                $User->device_id = 0;
                $User->open_direct = 0;
                $User->vcode = rand(111111, 999999);;
                $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
                $User->created_at = Carbon::now();
                $User->created_by = $token->id;
                $User->privacy_policy_date = Carbon::now();
                $User->license_date = Carbon::now();
                $User->save();

                $Home = new UserController;
                $Home->add_contact_card_profile($User);
                unset($User->vcode, $User->vcode_expiry, $User->access_token);

                // settings
                $createUserSettings = $colorsUserSettings = createUserSettings($User, $token);

                unset($colorsUserSettings->save_contact_button, $colorsUserSettings->connect_button, $colorsUserSettings->show_connect, $colorsUserSettings->show_contact, $colorsUserSettings->open_direct, $colorsUserSettings->capture_lead);

                $colors = ['colors' => $colorsUserSettings];
                $settings[] = $colors;
                // settings ends

                $BusinessUser = new BusinessUser();
                $BusinessUser->user_id = $User->id;
                $BusinessUser->parent_id = $parent_id;
                $BusinessUser->account_limit = 0;
                $BusinessUser->domain = NULL;
                $BusinessUser->user_role = 'user'; //$request->user_role;
                $BusinessUser->created_by = $token->id;
                $BusinessUser->created_at = Carbon::now();
                $BusinessUser->save();

                // create profile
                $UserSettingsObj = addGlobalProfiles($token, $User, $User->email);
                // end

                if (!empty($UserSettingsObj)) {
                    $User->connect_button = true_false($UserSettingsObj->connect_button);
                    $User->save_contact_button = true_false($UserSettingsObj->save_contact_button);
                    $User->capture_lead = true_false($UserSettingsObj->capture_lead);
                    $User->open_direct = true_false($User->open_direct);
                } else {
                    $User->connect_button = true_false($createUserSettings->connect_button);
                    $User->save_contact_button = true_false($createUserSettings->save_contact_button);
                    $User->capture_lead = true_false($createUserSettings->capture_lead);
                    $User->open_direct = true_false($User->open_direct);

                    unset($createUserSettings->save_contact_button, $createUserSettings->connect_button, $createUserSettings->show_connect, $createUserSettings->show_contact, $createUserSettings->open_direct, $createUserSettings->capture_lead);
                }

                $template = anyTemplateAssigned($User->id);
                $User = UserObj($User, 0, $template);
                $users_list[] = $User;
                $new_member_ids[] = $User->id;

                // list links
                $newLinks = newlyCreatedLinks($User, $token);
                $links = array_merge($links, $newLinks);
                // end links list

                if ($request->send_invites == 1) {
                    // send email
                    if (isset($_POST['language']) && trim($_POST['language']) == 'de') {
                        $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
                        Welcome to the ' . config("app.name", "") . '.<br><br>
                        This is to provide you with your credentials to log in.<br><br>
                        Your password is: <i>' . $password . '</i><br><br>
                        After you have logged in for the first time, you will be asked to create your own password.<br><br>
                        Good luck<br><br>
                        ' . playstore_urls() . '
                        Your ' . config("app.name", "") . ' customer service';
                        // echo $html;exit;
                        $subject = "You've been invited to join your team on " . config("app.name", "") . "";
                    } else {
                        $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
                        Welcome to the ' . config("app.name", "") . '.<br><br>
                        This is to provide you with your credentials to log in.<br><br>
                        Your password is: <i>' . $password . '</i><br><br>
                        After you have logged in for the first time, you will be asked to create your own password.<br><br>
                        Good luck<br><br>
                        ' . playstore_urls() . '
                        Your ' . config("app.name", "") . ' customer service';
                        // echo $html;exit;
                        $subject = "You've been invited to join your team on " . config("app.name", "") . "";
                    }

                    $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
                    $body = [
                        'Messages' => [
                            [
                                'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                                'To' => [['Email' => $User->email, 'Name' => $User->first_name . ' ' . $User->last_name]],
                                'Subject' => $subject,
                                'TextPart' => $subject,
                                'HTMLPart' => $html,
                                'CustomID' => config("app.name", "")
                            ]
                        ]
                    ];

                    $mj->post(Resources::$Email, ['body' => $body]);
                    // send email ends
                }
            }
        }

        if (!empty($UserSettingsObj)) {
            unset($settings);
            $UserSettingsObj->section_color = notEmpty($UserSettingsObj->section_color) ? $UserSettingsObj->section_color : $colorsUserSettings->section_color;
            $UserSettingsObj->bg_color = notEmpty($UserSettingsObj->bg_color) ? $UserSettingsObj->bg_color : $colorsUserSettings->bg_color;
            $UserSettingsObj->btn_color = notEmpty($UserSettingsObj->btn_color) ? $UserSettingsObj->btn_color : $colorsUserSettings->btn_color;
            $UserSettingsObj->text_color = notEmpty($UserSettingsObj->text_color) ? $UserSettingsObj->text_color : $colorsUserSettings->text_color;
            $UserSettingsObj->photo_border_color = notEmpty($UserSettingsObj->photo_border_color) ? $UserSettingsObj->photo_border_color : $colorsUserSettings->photo_border_color;

            unset($UserSettingsObj->save_contact_button, $UserSettingsObj->connect_button, $UserSettingsObj->show_connect, $UserSettingsObj->show_contact, $UserSettingsObj->open_direct, $UserSettingsObj->capture_lead);
            $colors = ['colors' => $UserSettingsObj];
            $settings[] = $colors;
        }

        $data['success'] = TRUE;
        if (count($new_member_ids) > 1) {
            $data['message'] = 'Members successfully created.';
        } else {
            $data['message'] = 'Member successfully created.';
        }
        $data['data'] = array('users' => $users_list, 'links' => $links, 'settings' => $settings);
        return response()->json($data, 201);
    }

    public function create_member_accounts_with_emails(Request $request)
    {


        $UserSettingsObj = [];

        // $validations['new_members.*.first_name'] = 'required|string';
        // $validations['new_members.*.last_name'] = 'required|string';
        $validations['new_members.*.email'] = 'required|email';
        $validations['send_invites'] = 'required';
        // $validations['template_id'] = 'required|integer';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $messages = json_decode(json_encode($validator->messages()), true);
            $i = 0;
            foreach ($messages as $key => $val) {
                $data['errors'][$i]['error'] = $val[0];
                $data['errors'][$i]['field'] = $key;
                $i++;
            }

            $data['success'] = false;
            $data['message'] = 'Required data is missing or invalid.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $users_list = $new_member_ids = $links = $settings = [];
        $parent_id = parent_id($token);

        $validators = $this->validateMemberData($request->new_members, $parent_id);
        if (!empty($validators)) {
            return $validators;
        }

        $records = $request->new_members;
        //$records = explode(',', $request->new_members);
        // pre_print($records);

        $account_limit = $this->account_limit($parent_id, $records);
        if ($account_limit != 1) {
            $data['success'] = FALSE;
            $data['message'] = ($account_limit == 2) ? 'Company account limit reached. Please get in contact with the AddMee Customer Care.' : 'Company account limit reached. Please get in contact with the AddMee Customer Care.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $company_friendly_name = company_friendly_name($parent_id);

        if (!empty($records)) {
            foreach ($records as $idx => $member) {

                $password = Str::random(8);

                $User = new User;
                $User->first_name = $member['first_name'] ?? NULL;
                $User->last_name = $member['last_name'] ?? NULL;
                $User->email = $member['email'];
                $User->username = unique_username($company_friendly_name . email_split($member['email']));
                $User->password = bcrypt($password);
                $User->status = 1;
                $User->is_pro = is_business_user();
                $User->allow_data_usage = 0;
                $User->device_type = 0;
                $User->first_login = 1;
                $User->device_id = 0;
                $User->open_direct = 0;
                $User->vcode = rand(111111, 999999);;
                $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
                $User->created_at = Carbon::now();
                $User->created_by = $token->id;
                $User->privacy_policy_date = Carbon::now();
                $User->license_date = Carbon::now();
                $User->save();

                $Home = new UserController;
                $Home->add_contact_card_profile($User);
                unset(
                    $User->vcode,
                    $User->vcode_expiry,
                    $User->access_token,
                    $User->status,
                    $User->allow_data_usage,
                    $User->device_type,
                    $User->first_login,
                    $User->device_id,
                    $User->open_direct,
                    $User->created_at,
                    $User->created_by,
                    $User->privacy_policy_date,
                    $User->license_date,
                    $User->updated_at

                );

                // settings
                $createUserSettings = $colorsUserSettings = createUserSettings($User, $token);
                $user_settings_id = $createUserSettings->id;
                unset($colorsUserSettings->save_contact_button, $colorsUserSettings->connect_button, $colorsUserSettings->show_connect, $colorsUserSettings->show_contact, $colorsUserSettings->open_direct, $colorsUserSettings->capture_lead);

                $colors = ['colors' => $colorsUserSettings];
                $settings[$idx] = $colors;
                // settings ends

                $BusinessUser = new BusinessUser();
                $BusinessUser->user_id = $User->id;
                $BusinessUser->parent_id = $parent_id;
                $BusinessUser->account_limit = 0;
                $BusinessUser->domain = NULL;
                $BusinessUser->user_role = 'user'; //$request->user_role;
                $BusinessUser->created_by = $token->id;
                $BusinessUser->created_at = Carbon::now();
                $BusinessUser->save();

                // create profile
                $template_id = 0;
                if ($request->has('template_id') && $request->template_id != '') {
                    $template_id = $request->template_id;
                }

                $UserSettingsObj = addGlobalProfilesBp($token, $User, $User->email, $template_id);
                // end

                if (!empty($UserSettingsObj)) {
                    $User->connect_button = true_false($UserSettingsObj->connect_button);
                    $User->save_contact_button = true_false($UserSettingsObj->save_contact_button);
                    $User->capture_lead = true_false($UserSettingsObj->capture_lead);
                    $User->open_direct = true_false($User->open_direct);
                } else {
                    $User->connect_button = true_false($createUserSettings->connect_button);
                    $User->save_contact_button = true_false($createUserSettings->save_contact_button);
                    $User->capture_lead = true_false($createUserSettings->capture_lead);
                    $User->open_direct = true_false($User->open_direct);

                    unset(
                        $createUserSettings->save_contact_button,
                        $createUserSettings->connect_button,
                        $createUserSettings->show_connect,
                        $createUserSettings->show_contact,
                        $createUserSettings->open_direct,
                        $createUserSettings->capture_lead,
                        $User->connect_button,
                        $User->save_contact_button,
                        $User->capture_lead,
                        $User->open_direct
                    );
                }

                $template = anyTemplateAssigned($User->id);
                $User = UserObjTemplateBp($User, 0, $template);
                $users_list[] = $User;

                $new_member_ids[] = $User->id;

                // list links
                $newLinks = newlyCreatedLinksBp($User, $token);
                $links = array_merge($links, $newLinks);
                // end links list

                if ($request->send_invites == 1) {
                    // send email
                    if (isset($_POST['language']) && trim($_POST['language']) == 'de') {
                        $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
                        Welcome to the ' . config("app.name", "") . '.<br><br>
                        This is to provide you with your credentials to log in.<br><br>
                        Your password is: <i>' . $password . '</i><br><br>
                        After you have logged in for the first time, you will be asked to create your own password.<br><br>
                        Good luck<br><br>
                        ' . playstore_urls() . '
                        Your ' . config("app.name", "") . ' customer service';
                        // echo $html;exit;
                        $subject = "You've been invited to join your team on " . config("app.name", "") . "";
                    } else {
                        $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
                        Welcome to the ' . config("app.name", "") . '.<br><br>
                        This is to provide you with your credentials to log in.<br><br>
                        Your password is: <i>' . $password . '</i><br><br>
                        After you have logged in for the first time, you will be asked to create your own password.<br><br>
                        Good luck<br><br>
                        ' . playstore_urls() . '
                        Your ' . config("app.name", "") . ' customer service';
                        // echo $html;exit;
                        $subject = "You've been invited to join your team on " . config("app.name", "") . "";
                    }

                    $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
                    $body = [
                        'Messages' => [
                            [
                                'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                                'To' => [['Email' => $User->email, 'Name' => $User->first_name . ' ' . $User->last_name]],
                                'Subject' => $subject,
                                'TextPart' => $subject,
                                'HTMLPart' => $html,
                                'CustomID' => config("app.name", "")
                            ]
                        ]
                    ];

                    $mj->post(Resources::$Email, ['body' => $body]);
                    // send email ends
                }
            }
        }

        if (!empty($UserSettingsObj)) {
            unset($settings[$idx]);
            $UserSettingsObj->section_color = notEmpty($UserSettingsObj->section_color) ? $UserSettingsObj->section_color : $colorsUserSettings->section_color;
            $UserSettingsObj->bg_color = notEmpty($UserSettingsObj->bg_color) ? $UserSettingsObj->bg_color : $colorsUserSettings->bg_color;
            $UserSettingsObj->btn_color = notEmpty($UserSettingsObj->btn_color) ? $UserSettingsObj->btn_color : $colorsUserSettings->btn_color;
            $UserSettingsObj->text_color = notEmpty($UserSettingsObj->text_color) ? $UserSettingsObj->text_color : $colorsUserSettings->text_color;
            $UserSettingsObj->photo_border_color = notEmpty($UserSettingsObj->photo_border_color) ? $UserSettingsObj->photo_border_color : $colorsUserSettings->photo_border_color;
            $UserSettingsObj->id = $user_settings_id;

            unset($UserSettingsObj->save_contact_button, $UserSettingsObj->connect_button, $UserSettingsObj->show_connect, $UserSettingsObj->show_contact, $UserSettingsObj->open_direct, $UserSettingsObj->capture_lead);
            // $colors = ['colors' => $UserSettingsObj];
            $settings[] = $colors;
        }

        $new_settings = [];
        foreach ($settings as $setting) {
            $new_settings[] = $setting;
        }

        if ($request->has('template_id') && $request->template_id != '') {
            $assignees_ids = getAssigneeIDs($request->template_id);
            $assignees_ids = arrayValuesToInt($assignees_ids);
            $template = [
                'id' => $request->template_id,
                'assignees_ids' => $assignees_ids, // Assuming $User->id is the ID you want to include
            ];
        } else {
            $template = null;
        }

        $data['success'] = TRUE;
        if (count($new_member_ids) > 1) {
            $data['message'] = 'Members successfully created.';
        } else {
            $data['message'] = 'Member successfully created.';
        }
        $data['data'] = array('members' => $users_list, 'links' => $links, 'settings' => $new_settings, 'template' => $template);
        return response()->json($data, 201);
    }

    public function create_account(Request $request)
    {
        $validations['email'] = 'required|string|email|unique:users';
        $validator = Validator::make($request->all(), $validations);
        $message = 'Required data is missing.';
        if ($validator->fails()) {
            $messages = json_decode(json_encode($validator->messages()), true);
            $i = 0;
            foreach ($messages as $key => $val) {
                $data['errors'][$i]['error'] = $message = 'Already a Business User Account'; //$val[0];
                $data['errors'][$i]['field'] = $key;
                $i++;
            }

            $data['success'] = FALSE;
            $data['message'] = $message;
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $Obj = BusinessUser::where('user_id', $token->id)->first();
        $domain = explode('//', $Obj->domain);
        $domain = count($domain) > 1 ? $domain[1] : $domain[0];
        $domain = trim(trim(str_replace('www.', '', $domain)), '/');
        $domain = explode(',', $Obj->domain);
        $allowed = $domain;
        // pre_print($allowed);
        $parts = explode('@', $request->email);
        // Remove and return the last part, which should be the domain
        $domain = array_pop($parts);
        if (!in_array($domain, $allowed) && !in_array('*', $allowed)) {
            $data['success'] = FALSE;
            $data['message'] = 'Email addresses with only whitelisted domains can be added.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $parent_id = parent_id($token);

        $account_limit = $this->account_limit($parent_id, []);
        if ($account_limit != 1) {
            $data['success'] = FALSE;
            $data['message'] = ($account_limit == 2) ? 'Company account limit reached. Please get in contact with the AddMee Customer Care.' : 'Company account limit reached. Please get in contact with the AddMee Customer Care.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $password = Str::random(8);
        $company_friendly_name = company_friendly_name($parent_id);

        $User = new User;
        $User->first_name = $request->first_name;
        $User->last_name = $request->last_name;
        $User->email = $request->email;
        $User->username = unique_username($company_friendly_name . email_split($User->email));
        $User->password = bcrypt($password);
        $User->status = 1;
        $User->is_pro = is_business_user();
        $User->allow_data_usage = 0;
        $User->first_login = 1;
        $User->device_type = 0;
        $User->device_id = 0;
        $User->vcode = rand(111111, 999999);;
        $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
        $User->created_at = Carbon::now();
        $User->created_by = $token->id;
        $User->privacy_policy_date = Carbon::now();
        $User->license_date = Carbon::now();
        $User->save();

        $Home = new UserController;
        $Home->add_contact_card_profile($User);
        unset($User->vcode, $User->vcode_expiry, $User->access_token);

        $BusinessUser = new BusinessUser();
        $BusinessUser->user_id = $User->id;
        $BusinessUser->parent_id = $parent_id;
        $BusinessUser->account_limit = 0;
        $BusinessUser->domain = NULL;
        $BusinessUser->user_role = isset($request->user_role) ? $request->user_role : 'user'; //user,admin
        $BusinessUser->created_by = $token->id;
        $BusinessUser->created_at = Carbon::now();
        $BusinessUser->save();

        // create profile
        addGlobalProfiles($token, $User, $User->email);
        // end
        $template = anyTemplateAssigned($User->id);
        $User = UserObj($User, 0, $template);

        // send email
        if (isset($_POST['language']) && trim($_POST['language']) == 'de') {
            $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
            Welcome to the ' . config("app.name", "") . '.<br><br>
            This is to provide you with your credentials to log in.<br><br>
            Your password is: <i>' . $password . '</i><br><br>
            After you have logged in for the first time, you will be asked to create your own password.<br><br>
            Good luck<br><br>
            ' . playstore_urls() . '
            Your ' . config("app.name", "") . ' customer service';
            // echo $html;exit;
            $subject = "You've been invited to join your team on " . config("app.name", "") . "";
        } else {
            $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
            Welcome to the ' . config("app.name", "") . '.<br><br>
            This is to provide you with your credentials to log in.<br><br>
            Your password is: <i>' . $password . '</i><br><br>
            After you have logged in for the first time, you will be asked to create your own password.<br><br>
            Good luck<br><br>
            ' . playstore_urls() . '
            Your ' . config("app.name", "") . ' customer service';
            // echo $html;exit;
            $subject = "You've been invited to join your team on " . config("app.name", "") . "";
        }

        $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                    'To' => [['Email' => $User->email, 'Name' => $User->first_name . ' ' . $User->last_name]],
                    'Subject' => $subject,
                    'TextPart' => $subject,
                    'HTMLPart' => $html,
                    'CustomID' => config("app.name", "")
                ]
            ]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        // send email ends

        $data['success'] = TRUE;
        $data['message'] = 'Account created successfully.';
        $data['data'] = ['user' => $User];
        return response()->json($data, 201);
    }

    public function members_list(Request $request)
    {
        $token = $request->user();
        $parent_id = parent_id($token);

        $members = BusinessUser::select('users.*', 'admin.logo as business_admin_logo', 'business_users.user_role as role')
            ->join('users', 'users.id', '=', 'business_users.user_id')
            ->leftJoin('users as admin', 'admin.id', '=', 'business_users.parent_id')
            ->whereRaw('(parent_id = ' . $parent_id . ' OR user_id = ' . $parent_id . ')')->orderBy('id', 'DESC')->get();

        if (!empty($members)) {
            foreach ($members as $member) {
                $member->company_logo = notEmpty($member->company_logo) ? image_url($member->company_logo) : image_url($member->business_admin_logo);
                $member->profile_banner = image_url($member->banner);
                $member->profile_image = image_url($member->logo);
                $member->views_count = TapsViews::where('user_id', $member->id)->count();
                $member->leads_count = 0;
                $member->devices_count = UniqueCode::where('user_id', $member->id)->where('activated', 1)->count();

                $template = anyTemplateAssigned($member->id);
                $member = UserObj($member, 0, $template);

                unset($member->password, $member->remember_token, $member->created_by, $member->updated_by, $member->email_verified_at, $member->name, $member->business_admin_logo);
            }
        }

        $data['success'] = TRUE;
        $data['message'] = 'Members';
        $data['data'] = array('members' => $members);
        return response()->json($data, 201);
    }

    public function member_profiles(Request $request)
    {
        $token = $request->user();
        $type = 0; //isset($request->type) && $request->type != 'all' ? ($request->type == 'business' ? 1 : 0) : 2;

        $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.title as cp_title', 'cp.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.file_image', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'cp.is_direct as open_direct', 'p.type', 'cp.status as visible');
        $query->leftJoin('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
        $query = $query->where('user_id', $request->user_id)->where('p.status', 1);
        if ($type != 2) {
            $query = $query->where('is_business', $type);
        }
        $query->orderBy('cp.sequence', 'ASC');
        $query->orderBy('cp.id', 'DESC');
        $profiles = $query->get();

        $Home = new HomeController;
        $profiles = $Home->profile_meta($profiles, $token);

        $data['success'] = TRUE;
        $data['message'] = 'Profiles';
        $data['data'] = array('profiles' => $profiles);
        return response()->json($data, 201);
    }

    public function upload_accounts_csv(Request $request)
    {
        if (empty($_FILES)) {
            $data['success'] = FALSE;
            $data['message'] = 'No data found!';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        set_time_limit(0);
        $UserSettingsObj = [];
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        $path = $request->file('file')->getRealPath(); //root_dir().'test-codes.csv';//
        $records = array_map('utf8_encode', file($path));
        // $records = array_map('str_getcsv', $records);//removed this line to use semi colon
        $records = array_map(function ($v) {
            return str_getcsv($v, ";");
        }, $records);
        // pre_print($records);
        if (!count($records) > 0) {
            $data['success'] = FALSE;
            $data['message'] = 'No data found!';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        // Get field names from header column
        $fields = array_map('strtolower', $records[0]);
        // pre_print($fields);
        // Remove the header column
        array_shift($records);
        $rows = [];
        foreach ($records as $record) {
            if (count($fields) < 2 || count($fields) != count($record)) {
                // return 'csv_upload_invalid_data';
                $data['message'] = 'Invalid data!';
                $data['success'] = FALSE;
                return response($data, 400);
            }

            // Decode unwanted html entities
            $record =  array_map("html_entity_decode", $record);

            // Set the field name as key
            $record = array_combine($fields, $record);
            // pre_print($record);

            // Get the clean data
            $rows[] = $this->clear_encoding_str($record);
        }
        // pre_print($rows);
        $token = $request->user();
        $i = 0;
        $users_list = $new_member_ids = $links = [];
        $parent_id = parent_id($token);

        $validators = $this->validateUserJsonData(json_encode($rows), $parent_id);
        if (!empty($validators)) {
            return $validators;
        }

        // pre_print($rows);
        $account_limit = $this->account_limit($parent_id, $rows);
        if ($account_limit != 1) {
            $data['success'] = FALSE;
            $data['message'] = ($account_limit == 2) ? 'Company account limit reached. Please get in contact with the AddMee Customer Care.' : 'Company account limit reached. Please get in contact with the AddMee Customer Care.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        foreach ($rows as $row) {
            // pre_print($row);
            if (!isset($row['first-name'])) {
                // return 'csv_upload_invalid_data';
                $data['message'] = 'First name column is missing';
                $data['success'] = FALSE;
                return response($data, 400);
            }

            if (!isset($row['last-name'])) {
                // return 'csv_upload_invalid_data';
                $data['message'] = 'Last name column is missing';
                $data['success'] = FALSE;
                return response($data, 400);
            }

            if (!isset($row['email'])) {
                // return 'csv_upload_invalid_data';
                $data['message'] = 'Email column is missing';
                $data['success'] = FALSE;
                return response($data, 400);
            }

            if (!isset($row['bio'])) {
                // return 'csv_upload_invalid_data';
                $data['message'] = 'Bio column is missing';
                $data['success'] = FALSE;
                // return response($data, 400);
            }

            if (!isset($row['company'])) {
                // return 'csv_upload_invalid_data';
                $data['message'] = 'Company column is missing';
                $data['success'] = FALSE;
                return response($data, 400);
            }

            if (!isset($row['job-title'])) {
                // return 'csv_upload_invalid_data';
                $data['message'] = 'Job title column is missing';
                $data['success'] = FALSE;
                return response($data, 400);
            }
            // pre_print($row);
            $User = User::where('email', $row['email']);
            if ($User->count() != 0) {
                continue;
            }

            $company_name = company_friendly_name($parent_id);
            $row['username'] = unique_username($company_name . email_split($row['email']));
            $password = Str::random(8);
            //create user account
            $User = new User;
            $User->name = $row['first-name'] . ' ' . $row['last-name'];
            $User->first_name = $row['first-name'];
            $User->last_name = $row['last-name'];
            $User->email = $row['email'];
            $User->username = $row['username'];
            $User->bio = isset($row['bio']) ? $row['bio'] : NULL;
            $User->company_name = $row['company'];
            $User->designation = $row['job-title'];
            $User->password = bcrypt($password);
            $User->status = 1;
            $User->is_pro = is_business_user();
            $User->fcm_token = '';
            $User->allow_data_usage = 0;
            $User->device_type = 0;
            $User->device_id = 0;
            $User->first_login = 1;
            $User->vcode = rand(111111, 999999);;
            $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
            $User->created_by = $token->id;
            $User->privacy_policy_date = Carbon::now();
            $User->license_date = Carbon::now();
            $User->save();

            // settings
            $createUserSettings = $colorsUserSettings = createUserSettings($User, $token);

            unset($colorsUserSettings->save_contact_button, $colorsUserSettings->connect_button, $colorsUserSettings->show_connect, $colorsUserSettings->show_contact, $colorsUserSettings->open_direct, $colorsUserSettings->capture_lead);

            $colors = ['colors' => $colorsUserSettings];
            $settings[] = $colors;
            // settings ends

            $BusinessUser = new BusinessUser();
            $BusinessUser->user_id = $User->id;
            $BusinessUser->parent_id = $parent_id;
            $BusinessUser->account_limit = 0;
            $BusinessUser->domain = NULL;
            $BusinessUser->user_role = 'user'; //$request->user_role;
            $BusinessUser->created_by = $token->id;
            $BusinessUser->created_at = Carbon::now();
            $BusinessUser->save();

            // create profile
            $template_id = 0;
            if ($request->has('template_id') && $request->template_id != '') {
                $template_id = $request->template_id;
            }
            $UserSettingsObj = addGlobalProfiles($token, $User, $User->email, $template_id);

            if (!empty($UserSettingsObj)) {
                $User->connect_button = true_false($UserSettingsObj->connect_button);
                $User->save_contact_button = true_false($UserSettingsObj->save_contact_button);
                $User->capture_lead = true_false($UserSettingsObj->capture_lead);
                $User->open_direct = true_false($User->open_direct);
            } else {
                $User->connect_button = true_false($createUserSettings->connect_button);
                $User->save_contact_button = true_false($createUserSettings->save_contact_button);
                $User->capture_lead = true_false($createUserSettings->capture_lead);
                $User->open_direct = true_false($User->open_direct);

                unset($createUserSettings->save_contact_button, $createUserSettings->connect_button, $createUserSettings->show_connect, $createUserSettings->show_contact, $createUserSettings->open_direct, $createUserSettings->capture_lead);
            }
            // end

            $Home = new UserController;
            $Home->add_contact_card_profile($User);

            $template = anyTemplateAssigned($User->id);
            $User = UserObj($User, 0, $template);
            $users_list[] = $User;
            $new_member_ids[] = $User->id;
            // Phone Number, Website	Location	Linkedin	Instagram
            $profiles = ['instagram' => 'instagram', 'phone-number' => 'call', 'phone-number' => 'text', 'website' => 'www', 'location' => 'address', 'linkedin' => 'linkedin', 'phone-number-title-office' => 'business_call', 'phone-number-office' => 'business_call', 'phone-number-title-mobile' => 'call', 'phone-number-mobile' => 'call'];
            foreach ($profiles as $key => $val) {
                if (isset($row[$key]) && trim($row[$key]) != '') {
                    $Obj = new CustomerProfile();
                    $Obj->profile_link = mb_convert_encoding($row[$key], 'UTF-8');
                    $Obj->profile_code = mb_convert_encoding($val, 'UTF-8');
                    $Obj->is_business = 0;
                    $Obj->user_id = $User->id;
                    $Obj->created_by = $token->id;
                    $Obj->save();

                    $ContactCard = ContactCard::where('user_id', $User->id)->where('is_business', 0);
                    if ($ContactCard->count() > 0) {
                        $ContactCard = $ContactCard->first();
                        $ContactCard->customer_profile_ids = trim($ContactCard->customer_profile_ids . ',' . $Obj->id, ',');
                        $ContactCard->is_business = 0;
                        $ContactCard->updated_by = $User->id;
                        $ContactCard->save();
                    } else {
                        $ContactCard = new ContactCard;
                        $ContactCard->customer_profile_ids = $Obj->id;
                        $ContactCard->is_business = 0;
                        $ContactCard->user_id = $User->id;
                        $ContactCard->created_by = $User->id;
                        $ContactCard->save();
                    }
                }
            }
            // end

            // list links
            $newLinks = newlyCreatedLinks($User, $token);
            $links = array_merge($links, $newLinks);
            // end links list

            // send email
            $send_invites = false;
            if ($request->has('send_invites') && $request->send_invites != '') {
                $send_invites = $request->send_invites;
            }

            if ($send_invites == true) {
                if (isset($_POST['language']) && trim($_POST['language']) == 'de') {
                    $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
                Welcome to the ' . config("app.name", "") . '.<br><br>
                This is to provide you with your credentials to log in.<br><br>
                Your password is: <i>' . $password . '</i><br><br>
                After you have logged in for the first time, you will be asked to create your own password.<br><br>
                Good luck<br><br>
                ' . playstore_urls() . '
                Your ' . config("app.name", "") . ' customer service';
                    // echo $html;exit;
                    $subject = "You've been invited to join your team on " . config("app.name", "") . "";
                } else {
                    $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
                Welcome to the ' . config("app.name", "") . '.<br><br>
                This is to provide you with your credentials to log in.<br><br>
                Your password is: <i>' . $password . '</i><br><br>
                After you have logged in for the first time, you will be asked to create your own password.<br><br>
                Good luck<br><br>
                ' . playstore_urls() . '
                Your ' . config("app.name", "") . ' customer service';
                    // echo $html;exit;
                    $subject = "You've been invited to join your team on " . config("app.name", "") . "";
                }

                $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
                $body = [
                    'Messages' => [
                        [
                            'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                            'To' => [['Email' => $User->email, 'Name' => $User->first_name . ' ' . $User->last_name]],
                            'Subject' => $subject,
                            'TextPart' => $subject,
                            'HTMLPart' => $html,
                            'CustomID' => config("app.name", "")
                        ]
                    ]
                ];

                // $response =
                $mj->post(Resources::$Email, ['body' => $body]);
                // send email ends
            }
            $i++;
        }

        if (!empty($UserSettingsObj)) {
            unset($settings);
            $UserSettingsObj->section_color = notEmpty($UserSettingsObj->section_color) ? $UserSettingsObj->section_color : $colorsUserSettings->section_color;
            $UserSettingsObj->bg_color = notEmpty($UserSettingsObj->bg_color) ? $UserSettingsObj->bg_color : $colorsUserSettings->bg_color;
            $UserSettingsObj->btn_color = notEmpty($UserSettingsObj->btn_color) ? $UserSettingsObj->btn_color : $colorsUserSettings->btn_color;
            $UserSettingsObj->text_color = notEmpty($UserSettingsObj->text_color) ? $UserSettingsObj->text_color : $colorsUserSettings->text_color;
            $UserSettingsObj->photo_border_color = notEmpty($UserSettingsObj->photo_border_color) ? $UserSettingsObj->photo_border_color : $colorsUserSettings->photo_border_color;
            $colors = ['colors' => $UserSettingsObj];
            $settings[] = $colors;
        }

        if (count($new_member_ids) > 1) {
            $data['message'] =  $i . ': Members successfully created.';
        } else {
            $data['message'] =  $i . ': Member successfully created.';
        }
        $response['success'] = TRUE;
        $response['data'] = array('users' => $users_list, 'links' => $links, 'settings' => $settings);
        return response($response, 201);
    }

    public function upload_members_csv(Request $request)
    {
        if (empty($_FILES)) {
            $data['success'] = FALSE;
            $data['message'] = 'No data found!';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $validations['send_invites'] = 'required';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $messages = json_decode(json_encode($validator->messages()), true);
            $i = 0;
            foreach ($messages as $key => $val) {
                $data['errors'][$i]['error'] = $val[0];
                $data['errors'][$i]['field'] = $key;
                $i++;
            }

            $data['success'] = false;
            $data['message'] = 'Required data is missing or invalid.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        set_time_limit(0);
        $UserSettingsObj = [];
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        $path = $request->file('file')->getRealPath(); //root_dir().'test-codes.csv';//
        // $records = array_map('utf8_encode', file($path));
        // // $records = array_map('str_getcsv', $records);//removed this line to use semi colon
        // $records = array_map(function ($v) {
        //     return str_getcsv($v, ";");
        // }, $records);
        $lines = file($path);
        // Detect encoding of the first line
        $encoding = detect_encoding($lines[0]);

        // Convert each line to UTF-8
        $records = array_map(function ($line) use ($encoding) {
            return mb_convert_encoding($line, 'UTF-8', $encoding);
        }, $lines);

        $records = array_map(function ($v) {
            return str_getcsv($v, ";");
        }, $records);

        // pre_print($records);
        if (!count($records) > 0) {
            $data['success'] = FALSE;
            $data['message'] = 'No data found!';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        // Get field names from header column
        $fields = array_map('strtolower', $records[0]);
        // pre_print($fields);
        // Remove the header column
        array_shift($records);
        $rows = [];
        foreach ($records as $record) {
            if (count($fields) < 2 || count($fields) != count($record)) {
                // return 'csv_upload_invalid_data';
                $data['message'] = 'Invalid data!';
                $data['success'] = FALSE;
                return response($data, 400);
            }

            // Decode unwanted html entities
            $record =  array_map("html_entity_decode", $record);

            // Set the field name as key
            $record = array_combine($fields, $record);
            // pre_print($record);

            // Get the clean data
            $rows[] = $this->clear_encoding_str($record);
        }
        // pre_print($rows);
        $token = $request->user();
        $i = 0;
        $users_list = $new_member_ids = $links = [];
        $parent_id = parent_id($token);

        $settings = [];
        $mapped_fields = json_decode($request->mapped_fields, true);
        $hasEmail = false;
        foreach ($mapped_fields as $idx => $mapped_field) {
            if ($idx == 'email') {
                $hasEmail = true;
            }
        }

        if ($hasEmail == false) {
            $data['message'] = 'Email is not mapped with CSV.';
            $data['success'] = FALSE;
            return response($data, 400);
        }

        $validators = $this->validateUserJsonData(json_encode($rows), $parent_id);
        if (!empty($validators)) {
            return $validators;
        }

        // pre_print($rows);
        $account_limit = $this->account_limit($parent_id, $rows);
        if ($account_limit != 1) {
            $data['success'] = FALSE;
            $data['message'] = ($account_limit == 2) ? 'Company account limit reached. Please get in contact with the AddMee Customer Care.' : 'Company account limit reached. Please get in contact with the AddMee Customer Care.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $isValidEmail = true;
        foreach ($rows as $idx => $row) {
            $email = map_field($mapped_fields, $row, 'email', 'email');
            $validator = Validator::make(['email' => $email], [
                'email' => 'email',
            ]);

            if ($validator->passes()) {
                //is valid
            } else {
                $isValidEmail = false;
                break;
            }
        }

        if ($isValidEmail == false) {
            $data['message'] = 'The mapped column in CSV do not have valid email addresses.';
            $data['success'] = FALSE;
            return response($data, 400);
        }

        // pre_print($mapped_fields);
        foreach ($rows as $idx => $row) {

            // pre_print($row);
            if (!isset($row['first-name'])) {
                // return 'csv_upload_invalid_data';
                $data['message'] = 'First name column is missing';
                $data['success'] = FALSE;
                // return response($data, 400);
            }

            if (!isset($row['last-name'])) {
                // return 'csv_upload_invalid_data';
                $data['message'] = 'Last name column is missing';
                $data['success'] = FALSE;
                // return response($data, 400);
            }

            if (!isset($row['email'])) {
                // return 'csv_upload_invalid_data';
                $data['message'] = 'Email column is missing';
                $data['success'] = FALSE;
                return response($data, 400);
            }

            if (!isset($row['bio'])) {
                // return 'csv_upload_invalid_data';
                $data['message'] = 'Bio column is missing';
                $data['success'] = FALSE;
                // return response($data, 400);
            }

            if (!isset($row['company'])) {
                // return 'csv_upload_invalid_data';
                $data['message'] = 'Company column is missing';
                $data['success'] = FALSE;
                // return response($data, 400);
            }

            if (!isset($row['job-title'])) {
                // return 'csv_upload_invalid_data';
                $data['message'] = 'Job title column is missing';
                $data['success'] = FALSE;
                // return response($data, 400);
            }

            if (!isset($mapped_fields['first_name']) && !isset($mapped_fields['last_name'])) {
                if (isset($mapped_fields['full_name'])) {
                    $full_name_idx = str_replace(' ', '-', strtolower($mapped_fields['full_name']));
                    // echo $row[$full_name_idx];
                    if (isset($row[$full_name_idx]) && trim($row[$full_name_idx]) != '') {
                        $name = explode(' ', $row[$full_name_idx]);
                        $row['first-name'] = $name[0];
                        $row['last-name'] = trim(str_replace($name[0], '', $row[$full_name_idx]));
                        $mapped_fields['first_name'] = 'First Name';
                        $mapped_fields['last_name'] = 'Last Name';
                    }
                }
            }
            // pre_print($row);
            $street_address = $zip_post_code = $city = $state_province = $country = '';
            if (isset($mapped_fields['street_address']) && isset($row[str_replace(' ', '-', $mapped_fields['street_address'])]) && trim($row[str_replace(' ', '-', $mapped_fields['street_address'])]) != '') {
                $street_address = $row[str_replace(' ', '-', $mapped_fields['street_address'])];
            }

            if (isset($mapped_fields['zip_post_code']) && isset($row[str_replace(' ', '-', $mapped_fields['zip_post_code'])]) && trim($row[str_replace(' ', '-', $mapped_fields['zip_post_code'])]) != '') {
                $zip_post_code = $row[str_replace(' ', '-', $mapped_fields['zip_post_code'])];
            }
            if (isset($mapped_fields['city']) && isset($row[str_replace(' ', '-', $mapped_fields['city'])]) && trim($row[str_replace(' ', '-', $mapped_fields['city'])]) != '') {
                $city = $row[str_replace(' ', '-', $mapped_fields['city'])];
            }
            if (isset($mapped_fields['state_province']) && isset($row[str_replace(' ', '-', $mapped_fields['state_province'])]) && trim($row[str_replace(' ', '-', $mapped_fields['state_province'])]) != '') {
                $state_province = $row[str_replace(' ', '-', $mapped_fields['state_province'])];
            }

            if (isset($mapped_fields['country']) && isset($row[str_replace(' ', '-', $mapped_fields['country'])]) && trim($row[str_replace(' ', '-', $mapped_fields['country'])]) != '') {
                $country = $row[str_replace(' ', '-', $mapped_fields['country'])];
            }

            $hasAddress = false;
            if (!isset($mapped_fields['company_address'])) {
                $location_parts = array_filter([$street_address, $zip_post_code, $city, $state_province, $country]);

                if (!empty($location_parts)) {
                    $row['location'] = implode(', ', $location_parts);
                    $row['location'] = trim($row['location']);
                    $mapped_fields['company_address'] = 'Location';
                    if (trim($row['location']) != '') {
                        $hasAddress = true;
                    }
                }
            } else {
                $hasAddress = true;
            }

            // pre_print($mapped_fields);
            $User = User::where('email', $row['email']);
            if ($User->count() != 0) {
                continue;
            }

            $company_name = company_friendly_name($parent_id);
            $row['username'] = unique_username($company_name . email_split($row['email']));
            $password = Str::random(8);
            //create user account
            $User = new User;
            //  $User->name = $row['first-name'] . ' ' . $row['last-name'];
            $User->first_name = map_field($mapped_fields, $row, 'first_name', 'first-name');
            $User->last_name = map_field($mapped_fields, $row, 'last_name', 'last-name');
            $User->email = map_field($mapped_fields, $row, 'email', 'email');
            $User->username = $row['username'];
            $User->bio = isset($row['bio']) ? map_field($mapped_fields, $row, 'bio', 'bio') : NULL;
            $User->company_name = map_field($mapped_fields, $row, 'company_name', 'company');
            $User->company_address = map_field($mapped_fields, $row, 'company_address', 'location');
            $User->designation = map_field($mapped_fields, $row, 'designation', 'job-title');
            $User->password = bcrypt($password);
            $User->status = 1;
            $User->is_pro = is_business_user();
            $User->fcm_token = '';
            $User->allow_data_usage = 0;
            $User->device_type = 0;
            $User->device_id = 0;
            $User->first_login = 1;
            $User->vcode = rand(111111, 999999);;
            $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
            $User->created_by = $token->id;
            $User->privacy_policy_date = Carbon::now();
            $User->license_date = Carbon::now();
            // pre_print($User);
            $User->save();

            // settings
            $createUserSettings = $colorsUserSettings = createUserSettings($User, $token);
            $user_settings_id = $createUserSettings->id;

            unset($colorsUserSettings->save_contact_button, $colorsUserSettings->connect_button, $colorsUserSettings->show_connect, $colorsUserSettings->show_contact, $colorsUserSettings->open_direct, $colorsUserSettings->capture_lead);

            $colors = ['colors' => $colorsUserSettings];
            $settings[$idx] = $colors;
            // settings ends

            $BusinessUser = new BusinessUser();
            $BusinessUser->user_id = $User->id;
            $BusinessUser->parent_id = $parent_id;
            $BusinessUser->account_limit = 0;
            $BusinessUser->domain = NULL;
            $BusinessUser->user_role = 'user'; //$request->user_role;
            $BusinessUser->created_by = $token->id;
            $BusinessUser->created_at = Carbon::now();
            $BusinessUser->save();

            // create profile
            $template_id = 0;
            if ($request->has('template_id') && $request->template_id != '') {
                $template_id = $request->template_id;
            }

            $UserSettingsObj = addGlobalProfilesBp($token, $User, $User->email, $template_id);

            if (!empty($UserSettingsObj)) {
                $User->connect_button = true_false($UserSettingsObj->connect_button);
                $User->save_contact_button = true_false($UserSettingsObj->save_contact_button);
                $User->capture_lead = true_false($UserSettingsObj->capture_lead);
                $User->open_direct = true_false($User->open_direct);
            } else {
                $User->connect_button = true_false($createUserSettings->connect_button);
                $User->save_contact_button = true_false($createUserSettings->save_contact_button);
                $User->capture_lead = true_false($createUserSettings->capture_lead);
                $User->open_direct = true_false($User->open_direct);


                unset(
                    $createUserSettings->save_contact_button,
                    $createUserSettings->connect_button,
                    $createUserSettings->show_connect,
                    $createUserSettings->show_contact,
                    $createUserSettings->open_direct,
                    $createUserSettings->capture_lead,
                    $User->connect_button,
                    $User->save_contact_button,
                    $User->capture_lead,
                    $User->open_direct
                );
            }
            // end

            $Home = new UserController;
            $Home->add_contact_card_profile($User);
            unset(
                $User->vcode,
                $User->vcode_expiry,
                $User->access_token,
                $User->status,
                $User->allow_data_usage,
                $User->device_type,
                $User->first_login,
                $User->device_id,
                $User->open_direct,
                $User->created_at,
                $User->created_by,
                $User->privacy_policy_date,
                $User->license_date,
                $User->updated_at,
                $User->fcm_token

            );

            $template = anyTemplateAssigned($User->id);
            $User = UserObjTemplateBp($User, 0, $template);
            // $User = UserObj($User, 0, $template);
            $users_list[] = $User;
            $new_member_ids[] = $User->id;
            // Phone Number, Website	Location	Linkedin	Instagram
            $profiles = ['instagram' => 'instagram', 'phone-number' => 'call', 'phone-number' => 'text', 'website' => 'www', 'location' => 'address', 'linkedin' => 'linkedin', 'phone-number-title-office' => 'business_call', 'phone-number-office' => 'business_call', 'phone-number-title-mobile' => 'call', 'phone-number-mobile' => 'call'];
            foreach ($profiles as $key => $val) {
                if (isset($row[$key]) && trim($row[$key]) != '') {
                    if ($key == 'location' && $hasAddress == false) {
                        continue;
                    }
                    $Obj = new CustomerProfile();
                    $Obj->profile_link = mb_convert_encoding($row[$key], 'UTF-8');
                    $Obj->profile_code = mb_convert_encoding($val, 'UTF-8');
                    $Obj->is_business = 0;
                    $Obj->user_id = $User->id;
                    $Obj->created_by = $token->id;
                    $Obj->save();

                    $ContactCard = ContactCard::where('user_id', $User->id)->where('is_business', 0);
                    if ($ContactCard->count() > 0) {
                        $ContactCard = $ContactCard->first();
                        $ContactCard->customer_profile_ids = trim($ContactCard->customer_profile_ids . ',' . $Obj->id, ',');
                        $ContactCard->is_business = 0;
                        $ContactCard->updated_by = $User->id;
                        $ContactCard->save();
                    } else {
                        $ContactCard = new ContactCard;
                        $ContactCard->customer_profile_ids = $Obj->id;
                        $ContactCard->is_business = 0;
                        $ContactCard->user_id = $User->id;
                        $ContactCard->created_by = $User->id;
                        $ContactCard->save();
                    }
                }
            }
            // end

            resetProfilesSequence($User->id);

            // list links
            $newLinks = newlyCreatedLinksBp($User, $token);
            $links = array_merge($links, $newLinks);
            // end links list
            if ($request->send_invites == true) {
                // send email
                if (isset($_POST['language']) && trim($_POST['language']) == 'de') {
                    $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
                Welcome to the ' . config("app.name", "") . '.<br><br>
                This is to provide you with your credentials to log in.<br><br>
                Your password is: <i>' . $password . '</i><br><br>
                After you have logged in for the first time, you will be asked to create your own password.<br><br>
                Good luck<br><br>
                ' . playstore_urls() . '
                Your ' . config("app.name", "") . ' customer service';
                    // echo $html;exit;
                    $subject = "You've been invited to join your team on " . config("app.name", "") . "";
                } else {
                    $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
                Welcome to the ' . config("app.name", "") . '.<br><br>
                This is to provide you with your credentials to log in.<br><br>
                Your password is: <i>' . $password . '</i><br><br>
                After you have logged in for the first time, you will be asked to create your own password.<br><br>
                Good luck<br><br>
                ' . playstore_urls() . '
                Your ' . config("app.name", "") . ' customer service';
                    // echo $html;exit;
                    $subject = "You've been invited to join your team on " . config("app.name", "") . "";
                }

                $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
                $body = [
                    'Messages' => [
                        [
                            'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                            'To' => [['Email' => $User->email, 'Name' => $User->first_name . ' ' . $User->last_name]],
                            'Subject' => $subject,
                            'TextPart' => $subject,
                            'HTMLPart' => $html,
                            'CustomID' => config("app.name", "")
                        ]
                    ]
                ];

                // $response =
                $mj->post(Resources::$Email, ['body' => $body]);
                // send email ends
                $i++;
            }
        }

        if (!empty($UserSettingsObj)) {
            unset($settings[$idx]);
            $UserSettingsObj->section_color = notEmpty($UserSettingsObj->section_color) ? $UserSettingsObj->section_color : $colorsUserSettings->section_color;
            $UserSettingsObj->bg_color = notEmpty($UserSettingsObj->bg_color) ? $UserSettingsObj->bg_color : $colorsUserSettings->bg_color;
            $UserSettingsObj->btn_color = notEmpty($UserSettingsObj->btn_color) ? $UserSettingsObj->btn_color : $colorsUserSettings->btn_color;
            $UserSettingsObj->text_color = notEmpty($UserSettingsObj->text_color) ? $UserSettingsObj->text_color : $colorsUserSettings->text_color;
            $UserSettingsObj->photo_border_color = notEmpty($UserSettingsObj->photo_border_color) ? $UserSettingsObj->photo_border_color : $colorsUserSettings->photo_border_color;
            $UserSettingsObj->color_link_icons = notEmpty($UserSettingsObj->color_link_icons) ? $UserSettingsObj->color_link_icons : $colorsUserSettings->color_link_icons;
            $UserSettingsObj->color_link_icons = true_false($UserSettingsObj->color_link_icons);
            $UserSettingsObj->id = $user_settings_id;

            unset($UserSettingsObj->show_contact, $UserSettingsObj->show_connect, $UserSettingsObj->connect_button, $UserSettingsObj->save_contact_button, $UserSettingsObj->capture_lead, $UserSettingsObj->open_direct);

            // $colors = ['colors' => $UserSettingsObj];
            $settings[$idx] = $colors;
        }
        $new_settings = [];
        foreach ($settings as $setting) {
            $new_settings[] = $setting;
        }

        if ($request->has('template_id') && $request->template_id != '') {
            $assignees_ids = getAssigneeIDs($request->template_id);
            $assignees_ids = arrayValuesToInt($assignees_ids);
            $template = [
                'id' => (int) $request->template_id,
                'assignees_ids' => $assignees_ids, // Assuming $User->id is the ID you want to include
            ];
        } else {
            $template = null;
        }


        if (count($new_member_ids) > 1) {
            $data['message'] =  $i . ': Members successfully created.';
        } else {
            $data['message'] =  $i . ': Member successfully created.';
        }
        $data['success'] = TRUE;
        $data['data'] = array('members' => $users_list, 'links' => $links, 'settings' => $new_settings, 'template' => $template);
        return response()->json($data, 201);
    }


    public function set_language(Request $request)
    {
        $validations['language'] = 'required|string';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $messages = json_decode(json_encode($validator->messages()), true);
            $i = 0;
            foreach ($messages as $key => $val) {
                $data['errors'][$i]['error'] = $val[0];
                $data['errors'][$i]['field'] = $key;
                $i++;
            }

            $data['success'] = FALSE;
            $data['message'] = 'Required param is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        UserSettings::updateOrCreate(['user_id' => $token->id], ['language' => $request->language, 'updated_by' => $token->id]);

        $response['message'] = 'Updated Successfully.';
        $response['success'] = TRUE;
        $response['data'] = [];
        return response($response, 201);
    }

    public function is_globally_editable(Request $request)
    {
        $validations['status'] = 'required|string';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $messages = json_decode(json_encode($validator->messages()), true);
            $i = 0;
            foreach ($messages as $key => $val) {
                $data['errors'][$i]['error'] = $val[0];
                $data['errors'][$i]['field'] = $key;
                $i++;
            }

            $data['success'] = FALSE;
            $data['message'] = 'Required param is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        UserSettings::updateOrCreate(['user_id' => $token->id], ['is_editable' => $request->status, 'updated_by' => $token->id]);

        $response['message'] = 'Updated Successfully.';
        $response['success'] = TRUE;
        $response['data'] = [];
        return response($response, 201);
    }

    public function set_deactivation_date(Request $request)
    {
        $validations['deactivates_on'] = 'required|string';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Date is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $parent_id = parent_id($token);

        $User = User::findorfail($parent_id);

        $codes_count = UniqueCode::where('brand', $User->username)->count();
        if ($codes_count > 0) {
            UniqueCode::where("brand", $User->username)->where("status", 1)->where("activated", 1)->where("user_id", '!=', 0)
                ->update(["expires_on" => $request->deactivates_on]);
        }

        // deactivate chips
        // UniqueCode::where("brand", $User->username)->where("status", 1)->where("activated", 1)->where("user_id", '!=', 0)
        // ->update(["user_id" => 0]);

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = [];
        return response()->json($data, 201);
    }

    public function delete_account(Request $request)
    {
        $validations['reason'] = 'required|string';
        $validations['user_id'] = 'required|string';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {

            $data['success'] = FALSE;
            $data['message'] = 'Required fields are missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $User = User::find($request->user_id);
        if (!empty($User)) {

            $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
            $User->vcode = rand(111111, 999999);
            $User->save();
            if (strtolower(config("app.name", "")) != 'addmee') {
                // email
            } else {
                // email
            }

            $data['success'] = TRUE;
            $data['message'] = 'Verification code sent successfully.';
            $data['data'] = (object)[]; //$User;
            return response()->json($data, 201);
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid user account.';
            $data['data'] = (object)[];
            return response()->json($data, 404);
        }
    }

    public function confirm_delete_account(Request $request)
    {
        $validations['reason'] = 'required|string';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Required field (reason) is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $BusinessUser = BusinessUser::where('user_id', $request->user_id)->where('parent_id', $token->id)->count();
        if ($BusinessUser == 0) {
            $data['success'] = FALSE;
            $data['message'] = 'You are not authorized to delete this account.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $User = User::findorfail($request->user_id);

        if (1) { //$User->vcode == $request->vcode) {

            if (1) { //strtotime($User->vcode_expiry) >= strtotime(date('Y-m-d H:i:s'))) {

                if (1) { //$User->id == $token->id) {

                    $user_id = $User->id;
                    $obj = new DeleteAccount();
                    $obj->user_id = $user_id;
                    $obj->reason = $request->reason;
                    $obj->details = $request->details;
                    $obj->name = $User->first_name . ' ' . $User->last_name;
                    $obj->created_by = $User->id;
                    $obj->save();

                    //delete user, business_infos,contact_cards,customer_profiles,taps_views,user_notes
                    $User->delete();

                    UniqueCode::where("user_id", $user_id)->update(["activated" => 0, "user_id" => 0, "updated_by" => $token->id]);

                    BusinessInfo::where('user_id', $user_id)->delete();
                    ContactCard::where('user_id', $user_id)->delete();
                    CustomerProfile::where('user_id', $user_id)->delete();
                    TapsViews::where('user_id', $user_id)->delete();
                    UserNote::where('user_id', $user_id)->delete();
                    UserSettings::where('user_id', $user_id)->delete();
                    BusinessUser::where('user_id', $user_id)->delete();
                    TemplateAssignee::where('user_id', $user_id)->delete();

                    if ($User->currentAccessToken()) {
                        $User->currentAccessToken()->delete();
                    }
                }

                $data['success'] = TRUE;
                $data['message'] = 'The requested member has been deleted.';
                $data['data'] = (object)[];
                return response()->json($data, 201);
            } else {
                $data['success'] = FALSE;
                $data['message'] = 'Verification code has expired.';
                $data['data'] = (object)[];
                return response()->json($data, 400);
            }
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid verification code.';
            $data['data'] = (object)[];
            return response()->json($data, 400);
        }
    }

    public function confirm_delete_accounts(Request $request)
    {
        $validations['reason'] = 'required|string';
        $validations['ids'] = 'required|string';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Required fields are missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $user_ids = explode(',', $request->ids);
        if (!empty($user_ids)) {
            $deletedCount = 0;
            foreach ($user_ids as $user_id) {
                $BusinessUser = BusinessUser::where('user_id', $user_id)->where('parent_id', $token->id)->count();
                if ($BusinessUser == 0) {
                    $data['success'] = FALSE;
                    $data['message'] = 'The member with admin rights can not be deleted.';
                    $data['data'] = (object)[];
                    return response($data, 400);
                    continue;
                }

                $User = User::findorfail($user_id);

                if (1) { //$User->vcode == $request->vcode) {

                    if (1) { //strtotime($User->vcode_expiry) >= strtotime(date('Y-m-d H:i:s'))) {

                        if (1) { //$User->id == $token->id) {

                            $user_id = $User->id;
                            $obj = new DeleteAccount();
                            $obj->user_id = $user_id;
                            $obj->reason = $request->reason;
                            $obj->details = $request->details;
                            $obj->name = $User->first_name . ' ' . $User->last_name;
                            $obj->created_by = $User->id;
                            $obj->save();

                            //delete user, business_infos,contact_cards,customer_profiles,taps_views,user_notes
                            $User->delete();
                            BusinessInfo::where('user_id', $user_id)->delete();
                            ContactCard::where('user_id', $user_id)->delete();
                            CustomerProfile::where('user_id', $user_id)->delete();
                            TapsViews::where('user_id', $user_id)->delete();
                            UserNote::where('user_id', $user_id)->delete();
                            UserSettings::where('user_id', $user_id)->delete();
                            BusinessUser::where('user_id', $user_id)->delete();
                            UniqueCode::where("user_id", $user_id)->update(["activated" => 0, "user_id" => 0, "updated_by" => $token->id]);
                            TemplateAssignee::where('user_id', $user_id)->delete();

                            if ($User->currentAccessToken()) {
                                $User->currentAccessToken()->delete();
                            }
                            $deletedCount++;
                        }

                        // $data['success'] = TRUE;
                        // $data['message'] = 'The requested member has been deleted.';
                        // $data['data'] = (object)[];
                        // return response()->json($data, 201);
                    } else {
                        // $data['success'] = FALSE;
                        // $data['message'] = 'Verification code has expired.';
                        // $data['data'] = (object)[];
                        // return response()->json($data, 400);
                    }
                } else {
                    // $data['success'] = FALSE;
                    // $data['message'] = 'Invalid verification code.';
                    // $data['data'] = (object)[];
                    // return response()->json($data, 400);
                }
            }

            $data['success'] = TRUE;
            $data['message'] = $deletedCount . ($deletedCount > 1 ? ' members have' : ' member has') . ' been deleted.';
            $data['data'] = (object)[];
            return response()->json($data, 201);
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid data.';
            $data['data'] = (object)[];
            return response()->json($data, 400);
        }
    }

    public function bulk_update_members(Request $request)
    {
        $validations['op_type'] = 'required|string';
        $validations['user_ids'] = 'required|string'; //comma separated

        if (isset($_POST['op_type']) && in_array($request->op_type, ['profile_photo', 'profile_banner', 'company_logo'])) {
            $validations['image'] = 'required';
        }

        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Required fields are missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $parent_id = parent_id($token);

        $image = NULL;
        $user_ids = explode(',', $request->user_ids);
        // $BusinessUser = BusinessUser::whereIn('user_id', $user_ids)->whereRaw('parent_id = ' . $parent_id . ' OR user_id = ' . $parent_id);

        $BusinessUser = BusinessUser::whereIn('user_id', $user_ids); //->where('parent_id', $token->id);
        if ($BusinessUser->count() == 0) {
            $data['success'] = FALSE;
            $data['message'] = 'No record found.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        if (isset($_POST['op_type']) && in_array($request->op_type, ['profile_photo', 'profile_banner', 'company_logo'])) {
            $upload_dir = icon_dir();
            $date = date('Ymd');
            $response = upload_file($request, 'image', $upload_dir . '/' . $date);
            if ($response['success'] == FALSE) {
                $data['success'] = $response['success'];
                $data['message'] = $response['message'];
                $data['data'] = [];
                // $data['data'] = (object)[];
                return response()->json($data, 201);
            }

            if ($response['filename'] != '') {
                $image = $date . '/' . $response['filename'];
            }
        }

        $Users = $BusinessUser->select('users.*')->join('users', 'users.id', '=', 'business_users.user_id')->get();
        $list = [];
        $message = 'Updated successfully.';
        foreach ($Users as $user) {
            if ($request->op_type == 'reset_password_invite') {
                $password = Str::random(8);

                if (isset($_POST['language']) && trim($_POST['language']) == 'de') {
                    $html = 'Hallo ' . $user->first_name . ' ' . $user->last_name . ',<br><br>
                    Ihr Passwort wurde vom Administrator des Unternehmens zurückgesetzt.<br><br>
                    Hiermit erhalten Sie Ihr Einmal-Passwort.<br><br>
                    Ihr Einmal-Passwort lautet: <i>' . $password . '</i><br><br>
                    Nachdem Sie sich mit Ihrem Einmal-Passwort eingeloggt haben, werden Sie aufgefordert ein eigenes Passwort anzulegen.<br><br>
                    Bitte klicken Sie auf folgenden AddMee Business Portal Link, um Ihr Einmal-Passwort einzugeben:<br><br>
                    ' . business_portal_url() . '<br><br>
                    Weiterhin viel Erfolg mit AddMee Business.<br><br>
                    Ihr AddMee Kundenservice';
                    // echo $html;exit;
                    $subject = "" . config("app.name", "") . " Business Portal: Ihr Passwort wurde zurückgesetzt";
                } else {
                    $html = 'Hello ' . $user->first_name . ' ' . $user->last_name . ',<br><br>
                    Your password has been reset by the company administrator.<br><br>
                    Herewith you will receive your one-time password.<br><br>
                    Your one-time password is: <i>' . $password . '</i><br><br>
                    After logging in with your one-time password, you will be prompted to create your own password.<br><br>
                    Please click on the following AddMee Business Portal link to enter your one-time password:<br><br>
                    ' . business_portal_url() . '<br><br>
                    Have good networking with AddMee Business.<br><br>
                    Your ' . config("app.name", "") . ' customer service';
                    // echo $html;exit;
                    $subject = "" . config("app.name", "") . " Business Portal: Your password has been reset";
                }

                $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
                $body = [
                    'Messages' => [
                        [
                            'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                            'To' => [['Email' => $user->email, 'Name' => $user->first_name . ' ' . $user->last_name]],
                            'Subject' => $subject,
                            'TextPart' => $subject,
                            'HTMLPart' => $html,
                            'CustomID' => config("app.name", "")
                        ]
                    ]
                ];

                $response = $mj->post(Resources::$Email, ['body' => $body]);
                $UserObj = User::findorfail($user->id);
                $UserObj->password = bcrypt($password);
                $UserObj->first_login = 1;
                $UserObj->save();
                // $message =
                // Mail::send('mail.sent_otp', $emailInfo, function ($message) use ($emailInfo) {
                //     $message->to($emailInfo["email"])->subject($emailInfo["subject"]);
                // });
            } else if ($request->op_type == 'resend_invite') {
                $password = Str::random(8);
                if (isset($_POST['language']) && trim($_POST['language']) == 'de') {
                    $html = 'Hello ' . $user->first_name . ' ' . $user->last_name . ',<br><br>
                    Welcome to the ' . config("app.name", "") . '.<br><br>
                    This is to provide you with your credentials to log in.<br><br>
                    Your password is: <i>' . $password . '</i><br><br>
                    After you have logged in for the first time, you will be asked to create your own password.<br><br>
                    Good luck<br><br>
                    ' . playstore_urls() . '
                    Your ' . config("app.name", "") . ' customer service';
                    // echo $html;exit;
                    $subject = "You've been invited to join your team on " . config("app.name", "") . "";
                } else {
                    $html = 'Hello ' . $user->first_name . ' ' . $user->last_name . ',<br><br>
                    Welcome to the ' . config("app.name", "") . '.<br><br>
                    This is to provide you with your credentials to log in.<br><br>
                    Your password is: <i>' . $password . '</i><br><br>
                    After you have logged in for the first time, you will be asked to create your own password.<br><br>
                    Good luck<br><br>
                    ' . playstore_urls() . '
                    Your ' . config("app.name", "") . ' customer service';
                    // echo $html;exit;
                    $subject = "You've been invited to join your team on " . config("app.name", "") . "";
                }

                $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
                $body = [
                    'Messages' => [
                        [
                            'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                            'To' => [['Email' => $user->email, 'Name' => $user->first_name . ' ' . $user->last_name]],
                            'Subject' => $subject,
                            'TextPart' => $subject,
                            'HTMLPart' => $html,
                            'CustomID' => config("app.name", "")
                        ]
                    ]
                ];

                $response = $mj->post(Resources::$Email, ['body' => $body]);
                $UserObj = User::findorfail($user->id);
                $UserObj->password = bcrypt($password);
                $UserObj->first_login = 1;
                $UserObj->save();
                $message = 'Invite successfully resent';
                // Mail::send('mail.sent_otp', $emailInfo, function ($message) use ($emailInfo) {
                //     $message->to($emailInfo["email"])->subject($emailInfo["subject"]);
                // });

                // $user = array_intersect($good, $post);
            } else if ($request->op_type == 'lock_unlock') {

                $status = $request->status == 1 ? 'unlock' : 'lock';
                $html = 'Hello ' . $user->first_name . ' ' . $user->last_name . ',<br><br>
                Your account on ' . config("app.name", "") . ' has been ' . $status . '<br><br>
                Your ' . config("app.name", "") . ' customer service';
                // echo $html;exit;
                $subject = "Account status on " . config("app.name", "") . " Portal";
                $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
                $body = [
                    'Messages' => [
                        [
                            'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                            'To' => [['Email' => $user->email, 'Name' => $user->first_name . ' ' . $user->last_name]],
                            'Subject' => $subject,
                            'TextPart' => $subject,
                            'HTMLPart' => $html,
                            'CustomID' => config("app.name", "")
                        ]
                    ]
                ];

                $response = $mj->post(Resources::$Email, ['body' => $body]);

                $UserObj = User::findorfail($user->id);
                $UserObj->is_public = $request->status == 0 ? 2 : 1;
                $UserObj->save();

                // Mail::send('mail.sent_otp', $emailInfo, function ($message) use ($emailInfo) {
                //     $message->to($emailInfo["email"])->subject($emailInfo["subject"]);
                // });
            } else if ($request->op_type == 'editable') {

                $is_editable = $request->status;
                UserSettings::updateOrCreate(['user_id' => $user->id], ['is_editable' => $is_editable, 'updated_by' => $token->id]);
                $UserObj = User::findorfail($user->id);
                $UserObj = (array) json_decode(json_encode($UserObj));
                $UserObj['is_editable'] = (int)$is_editable;
                $UserObj['id'] = $user->id;
                $UserObj = (object) $UserObj;
            } else if ($request->op_type == 'direct_turn_on_off') {

                $status = $request->status == 1 ? 'on' : 'off';
                $html = 'Hello ' . $user->first_name . ' ' . $user->last_name . ',<br><br>
                Your profile on ' . config("app.name", "") . ' has been open direct ' . $status . '<br><br>
                Your ' . config("app.name", "") . ' customer service';
                // echo $html;exit;
                $subject = "Account status on " . config("app.name", "") . " Portal";
                $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
                $body = [
                    'Messages' => [
                        [
                            'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                            'To' => [['Email' => $user->email, 'Name' => $user->first_name . ' ' . $user->last_name]],
                            'Subject' => $subject,
                            'TextPart' => $subject,
                            'HTMLPart' => $html,
                            'CustomID' => config("app.name", "")
                        ]
                    ]
                ];

                $response = $mj->post(Resources::$Email, ['body' => $body]);

                $UserObj = User::findorfail($user->id);
                $UserObj->open_direct = $request->status;
                $UserObj->save();
                // Mail::send('mail.sent_otp', $emailInfo, function ($message) use ($emailInfo) {
                //     $message->to($emailInfo["email"])->subject($emailInfo["subject"]);
                // });
            } else if ($request->op_type == 'profile_photo') {

                $UserObj = User::findorfail($user->id);
                $UserObj->logo = $image;
                $UserObj->save();

                // $UserObj->profile_banner = image_url($UserObj->banner);
                // $UserObj->company_logo = image_url($UserObj->company_logo);
                // $UserObj->profile_image = image_url($UserObj->logo);
            } else if ($request->op_type == 'profile_banner') {

                $UserObj = User::findorfail($user->id);
                $UserObj->banner = $image;
                $UserObj->save();

                // $UserObj->profile_banner = image_url($UserObj->banner);
                // $UserObj->company_logo = image_url($UserObj->company_logo);
                // $UserObj->profile_image = image_url($UserObj->logo);
            } else if ($request->op_type == 'company_logo') {

                $UserObj = User::findorfail($user->id);
                $UserObj->company_logo = $image;
                $UserObj->save();

                // $UserObj->profile_banner = image_url($UserObj->banner);
                // $UserObj->company_logo = image_url($UserObj->company_logo);
                // $UserObj->profile_image = image_url($UserObj->logo);
            }

            if (!empty($UserObj)) {
                $template = anyTemplateAssigned($UserObj->id);
                unset(
                    $UserObj->created_at,
                    $UserObj->created_by,
                    $UserObj->created_at,
                    $UserObj->updated_by,
                    $UserObj->updated_at,
                    $UserObj->role,
                    $UserObj->forced_reset,
                    $UserObj->email_verified_at,
                    $UserObj->last_login,
                    $UserObj->subscription_date,
                    $UserObj->subscription_expires_on,
                    $UserObj->fcm_token,
                    $UserObj->allow_data_usage,
                    $UserObj->privacy_policy_date,
                    $UserObj->license_date,
                    $UserObj->first_login,
                    $UserObj->profile_view,
                    $UserObj->status,
                    $UserObj->provider,
                    $UserObj->provider_id,
                    $UserObj->device_id,
                    $UserObj->device_type,
                    $UserObj->platform,
                    $UserObj->user_group_id
                );

                $UserObj = UserObj($UserObj, 0, $template);
            }

            $list[] = !empty($UserObj) ? $UserObj : [];
        }

        $data['success'] = TRUE;
        $data['message'] = $message;
        $data['data'] = $list;
        return response()->json($data, 201);
    }

    public function add_member_profile(Request $request)
    {
        if ($request->profile_code != 'file') {
            $validations['profile_link'] = 'required|string';
        }

        $validations['profile_code'] = 'required|string';
        $validations['business_profile'] = 'required|string';

        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $messages = json_decode(json_encode($validator->messages()), true);
            $i = 0;
            foreach ($messages as $key => $val) {
                $data['errors'][$i]['error'] = $val[0];
                $data['errors'][$i]['field'] = $key;
                $i++;
            }

            $data['success'] = FALSE;
            $data['message'] = 'Required fields are missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();

        $date = date('Ymd');
        $Profile = Profile::where('profile_code', $request->profile_code);
        if ($Profile->count() > 0) {

            $Obj = new CustomerProfile;
            $Obj->profile_link = $request->profile_link;
            $Obj->profile_code = $request->profile_code;
            $Obj->is_business = ($request->business_profile == 'yes') ? 1 : 0;
            $Obj->user_id = $request->member_id;
            if ($request->has('visible')) {
                $Obj->status = $request->visible;
            }

            $maxProfileSequence = CustomerProfile::select(\DB::raw('ifnull(MAX(sequence),-1) as sequence'))->where('user_id', $request->member_id)->where('profile_code', '!=', 'contact-card')->first();
            if ($request->has('sequence')) {
                $CustomerProfile = CustomerProfile::where('user_id', $request->member_id)->where('sequence', $request->sequence);
                if ($CustomerProfile->count() > 0) {
                    $CustomerProfileObj = $CustomerProfile->first();
                    $CustomerProfileObj->sequence = $maxProfileSequence->sequence + 1;
                    $CustomerProfileObj->save();
                }

                $Obj->sequence = (int) $request->sequence;
            } else {
                // $CustomerProfileCount = CustomerProfile::where('user_id', $user->id)->count();
                $Obj->sequence = $maxProfileSequence->sequence + 1; //$CustomerProfileCount;
                $Obj->sequence = ($Obj->sequence < 1) ? 0 : $Obj->sequence;
            }

            if ($request->has('is_focused')) {
                $Obj->is_focused = $request->is_focused;
            }

            if ($request->has('is_highlighted')) {
                $Obj->is_focused = $request->is_highlighted;
            }

            $Obj->created_by = $token->id;
            $Obj->created_at = Carbon::now();

            if ($request->title != NULL || $request->title != '') {
                $Obj->title = $request->title;
            }

            if (!in_array($request->profile_code, is_free_profile_btn())) {
                if (isset($_FILES['icon']) && $_FILES['icon']['name'] != '') {
                    $upload_dir = icon_dir();
                    $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
                    if ($response['success'] == TRUE) {
                        if ($response['filename'] != '') {
                            $Obj->icon = $date . '/' . $response['filename'];
                        }
                    }
                }
            }
            // end

            $upload_dir = file_dir();
            $response = upload_file($request, 'file_image', $upload_dir . '/' . $date);
            $Obj->file_image = '';
            if ($response['success'] == TRUE) {
                if ($response['filename'] != '') {
                    $Obj->file_image = $date . '/' .  $response['filename'];
                }
            }

            if ($request->profile_code != 'file') {
                $Obj->save();
            } else {
                if ($Obj->file_image == '') {
                    $data['success'] = FALSE;
                    $data['message'] = 'File uploading failed.';
                    $data['data'] = (object)[];
                    return response($data, 400);
                }

                $Obj->save();
            }

            $is_business = $request->business_profile == 'yes' ? 1 : 0;
            $ContactCard = ContactCard::where('user_id', $request->member_id)->where('is_business', $is_business);
            if ($request->profile_code != 'wifi') {
                if ($ContactCard->count() > 0) {
                    $ContactCard = $ContactCard->first();
                    $ContactCard->customer_profile_ids = trim($ContactCard->customer_profile_ids . ',' . $Obj->id, ',');
                    $ContactCard->is_business = $is_business;
                    $ContactCard->updated_by = $token->id;
                    $ContactCard->save();
                } else {
                    $ContactCard = new ContactCard;
                    $ContactCard->customer_profile_ids = $Obj->id;
                    $ContactCard->is_business = $is_business;
                    $ContactCard->user_id = $request->member_id;
                    $ContactCard->created_by = $token->id;
                    $ContactCard->save();
                }
            }

            resetProfilesSequence($request->member_id);

            $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.title as cp_title', 'cp.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible', 'cp.is_focused as is_highlighted'); //'cp.is_direct as open_direct',
            $query->leftJoin('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
            $query = $query->where('cp.id', $Obj->id);
            $profiles = $query->get();

            $Home = new HomeController;
            $profiles = $Home->profile_meta($profiles, $token);
            // pre_print($profiles);
            if (!empty($profiles)) {
                $Obj = (object)$profiles[0];
            } else {
                $Obj = CustomerProfile::findorfail($Obj->id);

                if ($Obj->icon != '') {
                    $Obj->icon = icon_url() . $Obj->icon;
                }

                if ($Obj->file_image != '') {
                    $Obj->file_image = file_url() . $Obj->file_image;
                }

                $Obj->visible = ($Obj->status == 1) ? true : false;
                $Obj->is_highlighted = ($Obj->is_highlighted == 1) ? true : false;
                $Obj->profile_link = ($Obj->profile_code != 'file') ? $Profile->first()->base_url . $Obj->profile_link : $Obj->file_image;

                unset($Obj->file_image, $Obj->status, $Obj->created_by, $Obj->created_at, $Obj->updated_by, $Obj->updated_at, $Obj->is_direct);
            }


            $data['success'] = TRUE;
            $data['message'] = 'Profile listed successfully!';
            $data['data'] = array('contact_card' => $ContactCard, 'profile' => $Obj);
            return response()->json($data, 201);
        } else {

            $data['success'] = FALSE;
            $data['message'] = 'Invalid profile code.';
            $data['data'] = (object)[];
            return response($data, 422);
        }
    }

    public function add_multiple_member_profile(Request $request)
    {
        if ($request->profile_code != 'file') {
            $validations['profile_link_value'] = 'required|string';
        }

        $validations['profile_code'] = 'required|string';
        $validations['member_ids'] = 'required|string';

        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $messages = json_decode(json_encode($validator->messages()), true);
            $i = 0;
            foreach ($messages as $key => $val) {
                $data['errors'][$i]['error'] = $val[0];
                $data['errors'][$i]['field'] = $key;
                $i++;
            }

            $data['success'] = FALSE;
            $data['message'] = 'Required fields are missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();

        $date = date('Ymd');
        $Profile = Profile::where('profile_code', $request->profile_code);
        if ($Profile->count() > 0) {

            $user_ids = explode(',', $request->member_ids);

            $BusinessUser = BusinessUser::whereIn('user_id', $user_ids); //->where('parent_id', $token->id);
            // if ($BusinessUser->count() == 0) {
            //     $data['success'] = FALSE;
            //     $data['message'] = 'No record found.';
            //     $data['data'] = (object)[];
            //     return response($data, 400);
            // }

            $icon = $file_image = $icon_svg = '';
            if (!in_array($request->profile_code, is_free_profile_btn())) {
                if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
                    if (isset($_FILES['icon']) && $_FILES['icon']['name'] != '') {
                        $upload_dir = icon_dir();
                        $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
                        if ($response['success'] == TRUE) {
                            if ($response['filename'] != '') {
                                $icon = $date . '/' . $response['filename'];
                            }
                        }
                    }
                }

                if ($request->has('icon_svg') && $request->icon_svg != '') {
                    $icon_svg = $request->icon_svg;
                    $icon = '';
                }
            }
            // end
            if (isset($_FILES['file_image']) && $_FILES['file_image']['name'] != '') {
                $upload_dir = file_dir();
                $response = upload_file($request, 'file_image', $upload_dir . '/' . $date);

                if ($response['success'] == TRUE) {
                    if ($response['filename'] != '') {
                        $file_image = $date . '/' .  $response['filename'];
                    }
                }
            }

            $Users = $BusinessUser->select('users.*')->join('users', 'users.id', '=', 'business_users.user_id')->get();
            $list = [];
            $message = 'Updated successfully.';
            foreach ($Users as $user) {

                $Obj = new CustomerProfile;
                $Obj->profile_link = $request->profile_link_value;
                $Obj->profile_code = $request->profile_code;
                $Obj->is_business = 0;
                $Obj->user_id = $user->id;
                if ($request->has('visible')) {
                    $Obj->status = $request->visible;
                }

                $maxProfileSequence = CustomerProfile::select(\DB::raw('ifnull(MAX(sequence),-1) as sequence'))->where('user_id', $user->id)->where('profile_code', '!=', 'contact-card')->first();

                if ($request->has('sequence')) {
                    $CustomerProfile = CustomerProfile::where('user_id', $user->id)->where('sequence', $request->sequence);
                    if ($CustomerProfile->count() > 0) {
                        $CustomerProfileObj = $CustomerProfile->first();
                        $CustomerProfileObj->sequence = $maxProfileSequence->sequence + 1;
                        $CustomerProfileObj->save();
                    }
                    $Obj->sequence = (int)$request->sequence;
                } else {
                    // $CustomerProfileCount = CustomerProfile::where('user_id', $user->id)->count();
                    $Obj->sequence = $maxProfileSequence->sequence + 1; //$CustomerProfileCount;
                    $Obj->sequence = ($Obj->sequence < 1) ? 0 : $Obj->sequence;
                }

                if ($request->has('is_focused')) {
                    $Obj->is_focused = $request->is_focused;
                }

                if ($request->has('is_highlighted')) {
                    $Obj->is_focused = $request->is_highlighted;
                }

                $Obj->created_by = $token->id;
                $Obj->created_at = Carbon::now();

                if ($request->title != NULL || $request->title != '') {
                    $Obj->title = $request->title;
                }

                // new method svg
                // if ($icon != '' && $icon != NULL) {
                //     $Obj->icon = $icon;
                // } else {
                //     if (!empty($request->icon_svg) && !empty($Profile->first()->icon_svg_default)) {
                //         $Obj->icon_svg_default = $request->icon_svg;
                //     } else {
                //         $Obj->icon = $Profile->first()->icon;
                //     }
                // }
                // end
                $Obj->icon_svg_default = $icon_svg;
                if ($icon != '') {
                    $Obj->icon = $icon;
                } else if ($icon_svg != '') {
                    $Obj->icon = '';
                }

                if ($file_image != '') {
                    $Obj->file_image = '';
                }

                if ($request->has('icon')) {
                    if ($request->icon == '') {
                        $Obj->icon = $request->icon;
                    }
                }

                if ($request->profile_code != 'file') {
                    $Obj->save();
                } else {
                    if ($Obj->file_image == '') {
                        $data['success'] = FALSE;
                        $data['message'] = 'File uploading failed.';
                        $data['data'] = (object)[];
                        return response($data, 400);
                    }

                    $Obj->save();
                }

                $is_business = 0;
                $ContactCard = ContactCard::where('user_id', $user->id)->where('is_business', $is_business);
                if ($request->profile_code != 'wifi') {
                    if ($ContactCard->count() > 0) {
                        $ContactCard = $ContactCard->first();
                        $ContactCard->customer_profile_ids = trim($ContactCard->customer_profile_ids . ',' . $Obj->id, ',');
                        $ContactCard->is_business = $is_business;
                        $ContactCard->updated_by = $token->id;
                        $ContactCard->save();
                    } else {
                        $ContactCard = new ContactCard;
                        $ContactCard->customer_profile_ids = $Obj->id;
                        $ContactCard->is_business = $is_business;
                        $ContactCard->user_id = $user->id;
                        $ContactCard->created_by = $token->id;
                        $ContactCard->save();
                    }
                }

                resetProfilesSequence($user->id);

                $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.title as cp_title', 'cp.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible', 'cp.is_focused as is_highlighted', \DB::raw('COALESCE(cp.icon_svg_default, p.icon_svg_default) as icon_svg'), 'p.id as link_type_id', 'p.icon_svg_default as profile_icon_svg_default'); //'cp.is_direct as open_direct',
                $query->leftJoin('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
                $query = $query->where('cp.id', $Obj->id);
                $profiles = $query->get();

                $Home = new HomeController;
                $profiles = $Home->profile_meta($profiles, $token);
                // pre_print($profiles);
                if (!empty($profiles)) {
                    $Obj = (object)$profiles[0];

                    unset($Obj->file_image, $Obj->status, $Obj->created_by, $Obj->created_at, $Obj->updated_by, $Obj->updated_at, $Obj->is_direct, $Obj->added_to_contact_card);
                } else {
                    $Obj = CustomerProfile::findorfail($Obj->id);

                    if ($Obj->icon != '') {
                        $Obj->icon = icon_url() . $Obj->icon;
                    }

                    if ($Obj->file_image != '') {
                        $Obj->file_image = file_url() . $Obj->file_image;
                    }

                    $Obj->visible = ($Obj->status == 1) ? true : false;
                    $Obj->is_highlighted = ($Obj->is_highlighted == 1) ? true : false;
                    $Obj->profile_link = ($Obj->profile_code != 'file') ? $Profile->first()->base_url . $Obj->profile_link : $Obj->file_image;

                    if ($Obj->icon != '') {
                        $Obj->icon_svg = icon_svg_default($request, $Obj->profile_code);
                    }

                    unset($Obj->file_image, $Obj->status, $Obj->created_by, $Obj->created_at, $Obj->updated_by, $Obj->updated_at, $Obj->is_direct, $Obj->added_to_contact_card);
                }

                $list[] = $Obj;
            }

            $data['success'] = TRUE;
            $data['message'] = 'Profile listed successfully.';
            $data['data'] = array('links' => $list);
            return response()->json($data, 201);
        } else {

            $data['success'] = FALSE;
            $data['message'] = 'Invalid profile code.';
            $data['data'] = (object)[];
            return response($data, 422);
        }
    }

    public function update_member_profile(Request $request)
    {
        $Obj = CustomerProfile::findorfail($request->member_profile_id);
        $current_sequence = $Obj->sequence;

        if ($Obj->profile_code != 'file') {
            $validations['profile_link'] = 'required|string';

            $validator = Validator::make($request->all(), $validations);

            if ($validator->fails()) {
                $messages = json_decode(json_encode($validator->messages()), true);
                $i = 0;
                foreach ($messages as $key => $val) {
                    $data['errors'][$i]['error'] = $val[0];
                    $data['errors'][$i]['field'] = $key;
                    $i++;
                }

                $data['success'] = FALSE;
                $data['message'] = 'Required fields are missing.';
                $data['data'] = (object)[];
                return response($data, 400);
            }
        }

        $token = $request->user();

        if ($request->title != NULL || $request->title != '') {
            $Obj->title = $request->title;
        }

        if (!in_array($Obj->profile_code, is_free_profile_btn())) {
            if (isset($_FILES['icon']) && $_FILES['icon']['name'] != '') {
                $upload_dir = icon_dir();
                $date = date('Ymd');
                $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
                if ($response['success'] == TRUE) {
                    if ($response['filename'] != '') {
                        $Obj->icon = $date . '/' . $response['filename'];
                    }
                }
            }
        }

        $upload_dir = file_dir();
        $response = upload_file($request, 'file_image', $upload_dir);
        if ($response['success'] == TRUE) {
            if ($response['filename'] != '') {
                $Obj->file_image = $date . '/' . $response['filename'];
            }
        }

        if ($request->has('visible')) {
            $Obj->status = $request->visible;
        }

        if ($request->has('sequence')) {
            if ($current_sequence != $request->sequence) {

                $CustomerProfile = CustomerProfile::where('user_id', $request->member_id)->where('sequence', $request->sequence);
                if ($CustomerProfile->count() > 0) {
                    $maxProfileSequence = CustomerProfile::select(\DB::raw('ifnull(MAX(sequence),-1) as sequence'))->where('profile_code', '!=', 'contact-card')->where('user_id', $request->member_id)->first();
                    $maxProfileSequence->sequence = $maxProfileSequence->sequence;
                    $CustomerProfileObj = $CustomerProfile->first();
                    $CustomerProfileObj->sequence = $maxProfileSequence->sequence + 1;
                    $CustomerProfileObj->save();
                }

                $Obj->sequence = (int)$request->sequence;
            }
        }

        if ($request->has('is_focused')) {
            $Obj->is_focused = $request->is_focused;
        }

        if ($request->has('is_highlighted')) {
            $Obj->is_focused = $request->is_highlighted;
        }

        $Obj->profile_link = $request->profile_link;
        $Obj->is_business = $request->is_business == 'yes' ? 1 : 0;
        $Obj->updated_by = $token->id;
        $Obj->save();

        resetProfilesSequence($Obj->user_id);

        $Profile = Profile::where('profile_code', $Obj->profile_code);

        $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.title as cp_title', 'cp.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible', 'cp.is_focused as is_highlighted'); //'cp.is_direct as open_direct',
        $query->leftJoin('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
        $query = $query->where('cp.id', $Obj->id);
        $profiles = $query->get();

        $Home = new HomeController;
        $profiles = $Home->profile_meta($profiles, $token);
        // pre_print($profiles);
        if (!empty($profiles)) {
            $Obj = (object)$profiles[0];
        } else {

            if ($Obj->icon != '') {
                $Obj->icon = icon_url() . $Obj->icon;
            }

            if ($Obj->file_image != '') {
                $Obj->file_image = file_url() . $Obj->file_image;
            }

            $Obj->visible = ($Obj->status == 1) ? true : false;
            $Obj->is_highlighted = ($Obj->is_highlighted == 1) ? true : false;
            $Obj->profile_link = ($Obj->profile_code != 'file') ? $Profile->first()->base_url . $Obj->profile_link : $Obj->file_image;

            unset($Obj->file_image, $Obj->status, $Obj->created_by, $Obj->created_at, $Obj->updated_by, $Obj->updated_at, $Obj->is_direct, $Obj->is_focused);
        }

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $Obj);
        return response()->json($data, 201);
    }

    public function show_hide_member_profile(Request $request)
    {
        $validations['is_visible'] = 'required';

        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Required sequence field is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();

        $Obj = CustomerProfile::findorfail($request->member_profile_id);

        $Obj->status = $request->is_visible;
        $Obj->updated_by = $token->id;
        $Obj->save();

        $Obj->visible = $request->is_visible == 1 ? true : false;

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $Obj);
        return response()->json($data, 201);
    }

    public function member_profiles_sequence(Request $request)
    {
        $validations['sequence'] = 'required|string';

        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Required sequence field is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $sequence = json_decode($request->sequence);
        //pre_print($sequence);
        $ids = [];
        if (!empty($sequence)) {
            foreach ($sequence as $id => $val) {
                $Obj = CustomerProfile::find($id);
                if ($Obj) {
                    $CustomerProfile = CustomerProfile::where('user_id', $request->user_id)->where('sequence', $val)->where('id', '!=', $Obj->id);
                    if ($CustomerProfile->count() > 0) {
                        // $CustomerProfileCount = CustomerProfile::where('user_id', $request->user_id)->count();
                        // $CustomerProfileObj = $CustomerProfile->first();
                        // $CustomerProfileObj->sequence = $CustomerProfileCount;
                        // $CustomerProfileObj->updated_by = $token->id;
                        // $CustomerProfileObj->save();
                        // \DB::table('customer_profiles')->where('user_id', $request->user_id)->update(['name' => 'John']);
                        // \DB::statement('UPDATE `customer_profiles` SET `sequence` = `sequence` + 1 WHERE user_id = ' . $request->user_id . ' AND id != ' . $Obj->id);
                    }

                    $Obj->sequence = $val;
                    $Obj->updated_by = $token->id;
                    $Obj->save();

                    $ids[] = $id;
                }
            }
        }

        resetProfilesSequence($request->user_id);

        $profiles = CustomerProfile::select('id', 'sequence')->where('user_id', $request->user_id)->whereIn('id', $ids)->orderBy('sequence', 'ASC')->get();

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = ['id' => $request->user_id, 'links' => $profiles];
        return response()->json($data, 201);
    }

    public function update_user_settings(Request $request)
    {
        $validations['user_id'] = 'required';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $messages = json_decode(json_encode($validator->messages()), true);
            $i = 0;
            foreach ($messages as $key => $val) {
                $data['errors'][$i]['error'] = $val[0];
                $data['errors'][$i]['field'] = $key;
                $i++;
            }

            $data['success'] = FALSE;
            $data['message'] = 'Required fields are missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        UserSettings::updateOrCreate(['user_id' => $request->user_id], ['updated_by' => $request->user_id]);

        $parent_id = parent_id($token);
        $BusinessUser = BusinessUser::where('parent_id', $parent_id)->where('user_id', $request->user_id);
        $Obj = UserSettings::where('user_id', $request->user_id)->first();

        if ($Obj->user_id == $token->id || $BusinessUser->count() > 0 || $parent_id == $request->user_id) {

            if ($request->has('btn_color')) {
                $Obj->btn_color = $request->btn_color;
            }

            if ($request->has('bg_color')) {
                $Obj->bg_color = $request->bg_color;
            }

            if ($request->has('text_color')) {
                $Obj->text_color = $request->text_color;
            }

            if ($request->has('photo_border_color')) {
                $Obj->photo_border_color = $request->photo_border_color;
            }

            if ($request->has('section_color')) {
                $Obj->section_color = $request->section_color;
            }

            if ($request->has('color_link_icons')) {
                $Obj->color_link_icons = $request->color_link_icons;
            }

            // change bg image
            if ($request->has('bg_image')) {
                $upload_dir = icon_dir();
                $date = date('Ymd');
                $response = upload_file($request, 'bg_image', $upload_dir . '/' . $date);
                if ($response['success'] == FALSE) {
                    $data['success'] = $response['success'];
                    $data['message'] = $response['message'];
                    $data['data'] = (object)[];
                    return response()->json($data, 201);
                }

                if ($response['filename'] != '') {
                    $bg_image = $date . '/' . $response['filename'];
                    $Obj->bg_image = $bg_image;
                }
            }

            $Obj->updated_by = $token->id;
            $Obj->save();

            if ($Obj->bg_image != '') {
                $Obj->bg_image = icon_url() . $Obj->bg_image;
            }

            $Obj = (array)json_decode(json_encode($Obj));
            $Obj['color_link_icons'] = true_false($Obj['color_link_icons']);
            unset(
                $Obj["language"],
                $Obj["2fa_enabled"],
                $Obj["is_editable"],
                $Obj["show_contact"],
                $Obj["show_connect"],
                $Obj["control_buttons_locked"],
                $Obj["profile_opens_locked"],
                $Obj["colors_custom_locked"],
                $Obj["user_old_data"],
                $Obj["settings_old_data"],
                $Obj["created_by"],
                $Obj["updated_by"],
                $Obj["created_at"],
                $Obj['capture_lead'],
                $Obj["updated_at"]
            );

            $data['success'] = TRUE;
            $data['message'] = 'Updated successfully.';
            $data['data'] = array('profile' => (object) $Obj);
            return response()->json($data, 201);
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Profile does not exist.';
            $data['data'] = (object)[];
            return response($data, 422);
        }
    }

    public function update_member_data(Request $request)
    {

        $token = $request->user();
        UserSettings::updateOrCreate(['user_id' => $request->user_id], ['updated_by' => $request->user_id]);

        $parent_id = parent_id($token);
        $BusinessUser = BusinessUser::where('parent_id', $parent_id)->where('user_id', $request->user_id);
        $Obj = UserSettings::where('user_id', $request->user_id)->first();

        if ($Obj->user_id == $token->id || $BusinessUser->count() > 0 || $parent_id == $request->user_id) {

            $response_data = [];
            $response_data['id'] = $Obj->user_id;

            $userInfo = User::select('id', 'first_name', 'last_name', 'username')->find($request->user_id);

            if ($request->has('capture_lead')) {
                $Obj->capture_lead = $request->capture_lead;
                if ($request->capture_lead == 1) {
                    $userInfo->open_direct = 0;
                    $userInfo->save();
                }

                $response_data['capture_lead'] = $Obj->capture_lead == 1 ? true : false;
            }

            $Obj->updated_by = $token->id;
            $Obj->save();
            // pre_print($request->first_name);
            if ($request->has('first_name')) {
                $userInfo->first_name = $request->first_name;
                $userInfo->updated_by = $token->id;
                $userInfo->save();

                $response_data['first_name'] = $userInfo->first_name;
            }

            if ($request->has('last_name')) {
                $userInfo->last_name = $request->last_name;
                $userInfo->updated_by = $token->id;
                $userInfo->save();

                $response_data['last_name'] = $userInfo->last_name;
            }

            if ($request->has('bio')) {
                $userInfo->bio = $request->bio;
                $userInfo->updated_by = $token->id;
                $userInfo->save();

                $response_data['bio'] = $userInfo->bio;
            }

            if ($request->has('designation')) {
                $userInfo->designation = $request->designation;
                $userInfo->updated_by = $token->id;
                $userInfo->save();

                $response_data['designation'] = $userInfo->designation;
            }

            if ($request->has('dob')) {
                $userInfo->dob = $request->dob;
                $userInfo->updated_by = $token->id;
                $userInfo->save();

                $response_data['dob'] = $userInfo->dob;
            }

            if ($request->has('gender')) {
                $userInfo->gender = $request->gender;
                $userInfo->updated_by = $token->id;
                $userInfo->save();

                $response_data['gender'] = $userInfo->gender;
            }

            if ($request->has('company_address')) {
                $userInfo->company_address = $request->company_address;
                $userInfo->updated_by = $token->id;
                $userInfo->save();
                $response_data['company_address'] = $userInfo->company_address;
            }

            if ($request->has('company_name')) {
                $userInfo->company_name = $request->company_name;
                $userInfo->updated_by = $token->id;
                $userInfo->save();
                $response_data['company_name'] = $userInfo->company_name;
            }

            if ($request->has('direct_open')) {
                $userInfo->open_direct = $request->direct_open;
                $userInfo->updated_by = $token->id;
                $userInfo->save();
                $response_data['open_direct'] = $userInfo->open_direct;

                if ($request->direct_open == 1) {
                    $Obj->capture_lead = 0;
                    $Obj->updated_by = $token->id;
                    $Obj->save();
                }

                $userInfo->direct_open = $request->direct_open == 1 ? true : false;
                unset($userInfo->open_direct);
            }

            if ($request->has('open_direct')) {
                $userInfo->open_direct = $request->open_direct;
                $userInfo->updated_by = $token->id;
                $userInfo->save();
                $response_data['open_direct'] = $userInfo->open_direct;

                if ($request->open_direct == 1) {
                    $Obj->capture_lead = 0;
                    $Obj->updated_by = $token->id;
                    $Obj->save();
                }

                $userInfo->open_direct = $request->open_direct == 1 ? true : false;
                // unset($userInfo->open_direct);
            }
            // images
            if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {

                $upload_dir = icon_dir();
                $date = date('Ymd');
                $response = upload_file($request, 'profile_image', $upload_dir . '/' . $date);
                if ($response['success'] == FALSE) {
                    $data['success'] = $response['success'];
                    $data['message'] = $response['message'];
                    $data['data'] = (object)[];
                    return response()->json($data, 201);
                }

                if ($response['filename'] != '') {
                    $image = $date . '/' . $response['filename'];
                    $userInfo->logo = $image;
                    $userInfo->updated_by = $token->id;
                    $userInfo->save();
                    $response_data['profile_image'] = image_url($userInfo->logo);
                }
            } else {
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['name'] == '') {
                    $userInfo->logo = NULL;
                    $userInfo->updated_by = $token->id;
                    $userInfo->save();
                    $response_data['profile_image'] = NULL;
                }
            }

            if ($request->has('profile_image')) {
                if ($request->profile_image == '') {
                    $userInfo->logo = NULL;
                    $userInfo->updated_by = $token->id;
                    $userInfo->save();
                    $response_data['profile_image'] = NULL;
                }
            }

            if ($request->hasFile('profile_banner') && $request->file('profile_banner')->isValid()) {

                $upload_dir = icon_dir();
                $date = date('Ymd');
                $response = upload_file($request, 'profile_banner', $upload_dir . '/' . $date);
                if ($response['success'] == FALSE) {
                    $data['success'] = $response['success'];
                    $data['message'] = $response['message'];
                    $data['data'] = (object)[];
                    return response()->json($data, 201);
                }

                if ($response['filename'] != '') {
                    $image = $date . '/' . $response['filename'];
                    $userInfo->banner = $image;
                    $userInfo->updated_by = $token->id;
                    $userInfo->save();
                    $response_data['profile_banner'] = image_url($userInfo->banner);
                }
            } else {
                if (isset($_FILES['profile_banner']) && $_FILES['profile_banner']['name'] == '') {
                    $userInfo->banner = NULL;
                    $userInfo->updated_by = $token->id;
                    $userInfo->save();
                    $response_data['profile_banner'] = NULL;
                }
            }

            if ($request->has('profile_banner')) {
                if ($request->profile_banner == '') {
                    $userInfo->banner = NULL;
                    $userInfo->updated_by = $token->id;
                    $userInfo->save();
                    $response_data['profile_banner'] = NULL;
                }
            }

            if ($request->hasFile('company_logo') && $request->file('company_logo')->isValid()) {

                $upload_dir = icon_dir();
                $date = date('Ymd');
                $response = upload_file($request, 'company_logo', $upload_dir . '/' . $date);
                if ($response['success'] == FALSE) {
                    $data['success'] = $response['success'];
                    $data['message'] = $response['message'];
                    $data['data'] = (object)[];
                    return response()->json($data, 201);
                }

                if ($response['filename'] != '') {
                    $image = $date . '/' . $response['filename'];
                    $userInfo->company_logo = $image;
                    $userInfo->updated_by = $token->id;
                    $userInfo->save();

                    $response_data['company_logo'] = image_url($userInfo->company_logo);
                }
            } else {
                if (isset($_FILES['company_logo']) && $_FILES['company_logo']['name'] == '') {
                    $userInfo->company_logo = NULL;
                    $userInfo->updated_by = $token->id;
                    $userInfo->save();
                    $response_data['company_logo'] = NULL;
                }
            }

            if ($request->has('company_logo')) {
                if ($request->company_logo == '') {
                    $userInfo->company_logo = NULL;
                    $userInfo->updated_by = $token->id;
                    $userInfo->save();
                    $response_data['company_logo'] = NULL;
                }
            }
            // images end

            // operations end here
            $userInfo->capture_lead = $Obj->capture_lead == 1 ? true : false;
            $userInfo->profile_banner = image_url($userInfo->banner);
            $userInfo->company_logo = image_url($userInfo->company_logo);
            $userInfo->profile_image = image_url($userInfo->logo);
            unset($userInfo->logo, $userInfo->banner);

            $data['success'] = TRUE;
            $data['message'] = 'Updated successfully.';
            $data['data'] = array('profile' => (object) $response_data);
            return response()->json($data, 201);
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Profile does not exist.';
            $data['data'] = (object)[];
            return response($data, 422);
        }
    }

    public function open_direct(Request $request)
    {
        $validations['is_direct'] = 'required|string';
        $validations['user_id'] = 'required';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {

            $data['success'] = FALSE;
            $data['message'] = 'Required field (is_direct) is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();

        $Obj = User::findorfail($request->user_id);
        $Obj->open_direct = $request->is_direct;
        $Obj->updated_by = $token->id;
        $Obj->save();
        $Obj->banner = image_url($Obj->banner);
        $Obj->logo = image_url($Obj->logo);
        $Obj->profile_image = $Obj->logo;

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $Obj);
        return response()->json($data, 201);
    }

    public function nfc_activation_url_qrcode(Request $request)
    {
        // https://github.com/vinkla/hashids
        $hashids = new Hashids('addmee');
        $encode_id = $hashids->encode($request->id, 10);
        $decode_id = $hashids->decode($encode_id);
        $decode_id = $decode_id[0];

        // $qr_file = date('YmdHis') . '.svg';
        // $dir = root_dir() . 'public/uploads/qrcodes/' . date('Ymd') . '/';
        // if (!file_exists($dir)) {
        //     mkdir($dir, 0777, true);
        //     $fp = fopen($dir . 'index.html', 'wb');
        //     fwrite($fp, '');
        //     fclose($fp);
        // }

        // $fp = fopen($dir . $qr_file, 'w');
        // fwrite($fp, QrCode::size(300)->format('svg')->generate('https://addmee.app/devices/activate/' . $encode_id));
        // fclose($fp);

        // $qr_code = file_get_contents(uploads_url() . 'qrcodes/' . date('Ymd') . '/' . $qr_file);
        // echo main_url() . '/devices/activate/' . $encode_id;exit;
        $svgContent = QrCode::size(300)->format('svg')->generate(main_url() . '/devices/activate/' . $encode_id);
        header('Content-Type: image/svg+xml');
        return response($svgContent, 200)->header('Content-Type', 'image/svg+xml')->header('Access-Control-Allow-Origin', '*');
        // $data['success'] = TRUE;
        // $data['message'] = 'Updated successfully.';
        // $data['data'] = ['qr_code' => trim($qr_code)]; //array('qr_code' => uploads_url() . 'qrcodes/' . date('Ymd') . '/' . $qr_file);
        // return response()->json($data, 201);
    }

    private function validateUserJsonData($json, $parent_id)
    {
        $users = json_decode($json);
        if (empty($users)) {
            $data['success'] = FALSE;
            $data['message'] = 'Required data is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        // $validations['email'] = 'required|string|email|unique:users';

        $Obj = BusinessUser::where('user_id', $parent_id)->first();
        $allowed = explode(',', trim($Obj->domain));
        $message = 'Required data is missing.';
        $email_list = [];

        foreach ($users as $user) {
            if (in_array($user->email, $email_list)) {
                $data['success'] = FALSE;
                $data['message'] = 'Email (' . $user->email . ') is being duplicated';
                $data['data'] = (object)[];
                return response($data, 400);
            } else {
                $email_list[] = $user->email;
            }

            // $validator = Validator::make((array)$user, $validations);

            $validator = Validator::make((array)$user, [
                'email' => [
                    'required',
                    'string',
                    'email',
                    function ($attribute, $value, $fail) {
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL) || !strpos($value, '@') || !strpos($value, '.')) {
                            $fail('The email address must be in a valid format.');
                        }
                    },
                    'unique:users',
                ],
            ]);

            if ($validator->fails()) {
                $messages = json_decode(json_encode($validator->messages()), true);
                $i = 0;
                foreach ($messages as $key => $val) {
                    if ($key == 'email') {
                        $data['errors'][$i]['error'] = $message = 'The email address must be in a valid format.';
                    } else {
                        $data['errors'][$i]['error'] = $message = 'Already a Business User Account';
                    }
                    $data['errors'][$i]['field'] = $key;
                    $i++;
                }


                $data['success'] = FALSE;
                $data['message'] = $message;
                $data['data'] = (object)$user;
                return response($data, 400);
            }

            $parts = explode('@', $user->email);
            // Remove and return the last part, which should be the domain
            $domain = array_pop($parts);
            if (!in_array($domain, $allowed) && !in_array('*', $allowed)) {
                $data['success'] = FALSE;
                $data['message'] = 'Email addresses with only whitelisted domains can be added.';
                $data['data'] = (object)$user;
                return response($data, 400);
            }
        }

        return [];
    }

    private function validateUserData($emails, $parent_id)
    {
        $users = explode(',', $emails);
        if (empty($users)) {
            $data['success'] = FALSE;
            $data['message'] = 'Emails are missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $validations['email'] = 'required|string|email|unique:users';

        $Obj = BusinessUser::where('user_id', $parent_id)->first();
        $allowed = explode(',', trim($Obj->domain));

        $email_list = [];

        foreach ($users as $email) {

            if (in_array($email, $email_list)) {
                $data['success'] = FALSE;
                $data['message'] = 'Email (' . $email . ') is being duplicated';
                $data['data'] = (object)[];
                return response($data, 400);
            } else {
                $email_list[] = $email;
            }

            $user['email'] = $email;
            $validator = Validator::make((array)$user, $validations);
            $message = 'Required data is missing.';

            if ($validator->fails()) {
                $messages = json_decode(json_encode($validator->messages()), true);
                $i = 0;
                foreach ($messages as $key => $val) {
                    // $message = $data['errors'][$i]['error'] = str_replace('The email', 'The email (' . $email . ')', $val[0]);
                    $message = $data['errors'][$i]['error'] = 'Already a Business User Account (' . $email . ')';
                    $data['errors'][$i]['field'] = $key;
                    $i++;
                }

                $data['success'] = FALSE;
                $data['message'] = $message;
                $data['data'] = (object)$user;
                return response($data, 400);
            }

            $parts = explode('@', $email);
            // Remove and return the last part, which should be the domain
            $domain = array_pop($parts);
            if (!in_array($domain, $allowed) && !in_array('*', $allowed)) {
                $data['success'] = FALSE;
                $data['message'] = 'Email addresses with only whitelisted domains can be added.';
                $data['data'] = $email;
                return response($data, 400);
            }
        }

        return [];
    }

    private function validateMemberData($users, $parent_id)
    {
        if (empty($users)) {
            return response([
                'success' => false,
                'message' => 'Emails are missing.',
                'data' => (object)[],
            ], 400);
        }

        $businessUser = BusinessUser::where('user_id', $parent_id)->first();
        $allowedDomains = explode(',', trim($businessUser->domain));

        $emailList = [];

        foreach ($users as $user) {
            $email = $user['email'];

            if (in_array($email, $emailList)) {
                return response([
                    'success' => false,
                    'message' => 'Email (' . $email . ') is being duplicated',
                    'data' => (object)[],
                ], 400);
            } else {
                $emailList[] = $email;
            }

            $validator = Validator::make($user, [
                'email' => 'required|string|email|unique:users',
            ]);

            if ($validator->fails()) {
                $firstErrorField = $validator->errors()->first();
                return response([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'data' => (object)$user,
                ], 400);
            }

            [$userEmail, $userDomain] = explode('@', $email, 2);

            if (!in_array($userDomain, $allowedDomains) && !in_array('*', $allowedDomains)) {
                return response([
                    'success' => false,
                    'message' => 'Email addresses with only whitelisted domains can be added.',
                    'data' => $email,
                ], 400);
            }
        }

        return [];
    }



    public function account_limit($parent_id, $new_users_list) //used in admin controller
    {
        $total_members = BusinessUser::where('parent_id', $parent_id)->count();
        $limit = BusinessUser::where('user_id', $parent_id)->first();
        $total = $total_members + count($new_users_list);
        if ($limit) {
            if ($total_members >= $limit->account_limit) {
                return 0;
            } elseif ($total > $limit->account_limit) {
                return 2;
            }
        } else {
            return 0;
        }
        return 1;
    }

    public function list_member_profiles(Request $request)
    {
        $token = $request->user();
        $type = 0; //isset($request->type) && $request->type != 'all' ? ($request->type == 'business' ? 1 : 0) : 2;
        $has_subscription = chk_subscription($token);
        $parent_id = parent_id($token);

        $members = BusinessUser::select('user_id', 'user_role as role')->whereRaw('parent_id = ' . $parent_id . ' OR user_id = ' . $parent_id)->get();
        $members_list = [];
        if (!empty($members)) {
            foreach ($members as $member) {

                $userInfo = User::select('id', 'username', 'first_name', 'last_name', 'designation', 'company_name', 'email', 'banner', 'banner as profile_banner', 'logo', 'logo as profile_image', 'company_logo', 'company_address', 'is_public', 'open_direct', 'bio')->where('id', $member->user_id)->first();

                if ($userInfo == null) {
                    // pre_print($member->user_id);
                    continue;
                }

                $capture_lead = false;
                $is_editable = 1;
                $connect_button = true;
                $save_contact_button = true;

                $template = anyTemplateAssigned($userInfo->id);
                $UserSettings = userSettingsObj($member->user_id, $template);
                // UserSettings::where('user_id', $member->user_id);
                if (count($UserSettings) > 0) {
                    // $UserSettingsObj = $UserSettings->first();
                    $UserSettingsObj = (object) $UserSettings;
                    $capture_lead = true_false($UserSettingsObj->capture_lead);
                    $is_editable = true_false($UserSettingsObj->is_editable);
                    $connect_button = true_false($UserSettingsObj->show_connect);
                    $save_contact_button = true_false($UserSettingsObj->show_contact);
                }

                $userInfo = UserObj($userInfo, 0, $template);

                $userInfo = json_decode(json_encode($userInfo), true);
                $userInfo['role'] = $member->role;
                $userInfo['locked'] = true_false($member->is_public);
                $userInfo['direct_open'] = true_false($userInfo['open_direct']);
                $userInfo['capture_lead'] = $capture_lead;
                $userInfo['devices_count'] = UniqueCode::where('user_id', $member->user_id)->where('activated', 1)->count();
                $userInfo['leads_count'] = 0;
                $userInfo['is_editable'] = $is_editable;
                $userInfo['connect_button'] = $connect_button;
                $userInfo['save_contact_button'] = $save_contact_button;
                $userInfo['views_count'] = TapsViews::where('user_id', $member->user_id)->count();
                $userInfo = (object)$userInfo;

                $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.title as cp_title', 'cp.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.file_image', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible', 'cp.is_focused as is_highlighted', 'cp.icon_svg_default as icon_svg', 'p.icon_svg_default as profile_icon_svg_default'); //'cp.is_direct as open_direct',

                if ($has_subscription['success'] == false) {
                    $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'p.title as cp_title', 'p.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.file_image', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible', 'cp.icon_svg_default as icon_svg', 'p.icon_svg_default as profile_icon_svg_default'); //'cp.is_direct as open_direct',
                    $query = $query->where('p.is_pro', 0);
                }

                $query->leftJoin('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
                $query = $query->where('user_id', $member->user_id)->where('p.status', 1);
                if ($type != 2) {
                    $query = $query->where('is_business', $type);
                }

                $query->orderBy('cp.sequence', 'ASC');
                $query->orderBy('cp.id', 'ASC');
                $profiles = $query->get();
                $Home = new HomeController;
                $profiles = $Home->profile_meta($profiles, $token);
                $contact_cards = [];
                $user_profiles = [];

                if (!empty($profiles)) {
                    // pre_print($profiles);
                    foreach ($profiles as $idx => $profile) {
                        if ($profile->profile_code == 'contact-card') {
                            $contact_cards[] = $profile;
                            // unset($profiles[$idx]);
                        } else {
                            $profile->template_id = NULL;
                            $profile->template_link_id = NULL;

                            $ObjCP = TemplateAssignee::where('customer_profile_id', $profile->id);
                            if ($ObjCP->count() > 0) {
                                $profile->template_id = $ObjCP->first()->user_template_id;
                                $profile->template_link_id = $ObjCP->first()->customer_profile_template_id;
                                // pre_print($profile);
                            }

                            $user_profiles[] = $profile;
                        }
                    }
                }

                $userInfo->links = $user_profiles;
                $userInfo->contact_cards = $contact_cards;

                if (count($UserSettings) > 0) {
                    // $UserSettings = $UserSettings->first();
                    $UserSettings = (object) $UserSettings;
                    $colors['colors'] = ['id' => $UserSettings->id, 'section_color' => $UserSettings->section_color,  'bg_color' => $UserSettings->bg_color,  'btn_color' => $UserSettings->btn_color,  'photo_border_color' => $UserSettings->photo_border_color,  'text_color' => $UserSettings->text_color,  'color_link_icons' => true_false($UserSettings->color_link_icons)];
                } else {
                    $colors['colors'] = [];
                }

                $userInfo->settings = $colors;
                $members_list[] = $userInfo;
            }
        }

        $data['success'] = TRUE;
        $data['message'] = count($members) . ' members found';
        $data['data'] = array('members' => $members_list);
        return response()->json($data, 201);
    }

    public function list_members_profiles_only(Request $request)
    {
        $token = $request->user();
        $type = 0; //isset($request->type) && $request->type != 'all' ? ($request->type == 'business' ? 1 : 0) : 2;
        $has_subscription = chk_subscription($token);
        $parent_id = parent_id($token);

        $members = BusinessUser::select('user_id', 'user_role as role')->whereRaw('parent_id = ' . $parent_id . ' OR user_id = ' . $parent_id)->get();
        $members_list = [];
        $contact_cards = [];
        $user_profiles = [];
        if (!empty($members)) {
            foreach ($members as $member) {

                $userInfo = User::select('id', 'username', 'first_name', 'last_name', 'designation', 'company_name', 'email', 'banner as profile_banner', 'logo as profile_image', 'company_logo', 'company_address', 'is_public', 'open_direct', 'bio')->where('id', $member->user_id)->first();

                if ($userInfo == null) {
                    // pre_print($member->user_id);
                    continue;
                }

                $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.title as cp_title', 'cp.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.file_image', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible', 'cp.is_focused as is_highlighted', 'global_id', 'p.icon_svg_default', 'p.icon_svg_default as profile_icon_svg_default', 'cp.icon_svg_default as cp_icon_svg_default', 'p.id as link_type_id'); //'cp.is_direct as open_direct',

                if ($has_subscription['success'] == false) {
                    $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'p.title as cp_title', 'p.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.file_image', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible', 'global_id', 'p.icon_svg_default', 'cp.icon_svg_default as cp_icon_svg_default', 'p.icon_svg_default as profile_icon_svg_default', 'p.id as link_type_id'); //'cp.is_direct as open_direct',
                    $query = $query->where('p.is_pro', 0);
                }

                $query->leftJoin('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
                $query = $query->where('user_id', $member->user_id)->where('p.status', 1)->where('cp.profile_code', '!=', 'contact-card');
                if ($type != 2) {
                    $query = $query->where('is_business', $type);
                }

                $query->orderBy('cp.sequence', 'ASC');
                $query->orderBy('cp.id', 'ASC');
                $profiles = $query->get();
                $Home = new HomeController;
                $profiles = $Home->profile_meta($profiles, $token);

                if (!empty($profiles)) {
                    // pre_print($profiles);
                    foreach ($profiles as $idx => $profile) {
                        if ($profile->profile_code == 'contact-card') {
                            $contact_cards[] = $profile;
                            // unset($profiles[$idx]);
                        } else {
                            $profile->value = $profile->profile_link_value;
                            $profile->href = $profile->profile_link;
                            $profile->sequence = $idx;
                            $profile->icon_url = $profile->icon;
                            $profile->global_id = $profile->global_id == 0 ? NULL : $profile->global_id;
                            $profile->template_id = NULL;
                            $profile->template_link_id = NULL;
                            $profile->is_unique = 0;
                            $profile->icon_svg = $profile->cp_icon_svg_default != '' ? $profile->cp_icon_svg_default : $profile->icon_svg_default;

                            $ObjCP = TemplateAssignee::where('customer_profile_id', $profile->id);
                            if ($ObjCP->count() > 0) {
                                $ObjectCP = $ObjCP->first();

                                $profile->template_id = $ObjectCP->user_template_id;
                                $profile->template_link_id = $ObjectCP->customer_profile_template_id;

                                $CustomerProfileTemplate = CustomerProfileTemplate::where('id', $ObjectCP->customer_profile_template_id);

                                $profile->is_unique = $CustomerProfileTemplate->count() > 0 ? (int) $CustomerProfileTemplate->first()->is_unique : 0;
                                // pre_print($profile);
                            }
                            unset($profile->added_to_contact_card, $profile->icon, $profile->base_url, $profile->profile_link_value, $profile->title_de, $profile->is_business, $profile->profile_link, $profile->icon_svg_default, $profile->cp_icon_svg_default);
                            $user_profiles[] = $profile;
                        }
                    }
                }
            }
        }

        $data['success'] = TRUE;
        $data['message'] = 'Found ' . count($user_profiles) . ' links for ' . count($members) . ' members.';
        $data['data'] = array('links' => $user_profiles);
        return response()->json($data, 201);
    }

    public function members_hashtags(Request $request)
    {
        $token = $request->user();
        $parent_id = parent_id($token);
        $hashtags = $this->getHastTags($parent_id);

        $data['success'] = TRUE;
        $data['message'] = 'HashTags were found for ' . count($hashtags) . ' members.';
        $data['data'] = array('hashtags' => $hashtags);
        return response()->json($data, 201);
    }

    public function add_members_hashtags(Request $request)
    {
        $validations['userIds'] = 'required';
        $validations['fromDate'] = 'required|string';
        $validations['toDate'] = 'required|string';
        $validations['value'] = 'required|string';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $messages = json_decode(json_encode($validator->messages()), true);
            $i = 0;
            foreach ($messages as $key => $val) {
                $data['errors'][$i]['error'] = $val[0];
                $data['errors'][$i]['field'] = $key;
                $i++;
            }

            $data['success'] = FALSE;
            $data['message'] = 'Required data is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        // pre_print($request->userIds);
        $token = $request->user();
        $parent_id = parent_id($token);
        $members = BusinessUser::select('user_id', 'user_role as role')->whereRaw('(parent_id = ' . $parent_id . ' OR user_id = ' . $parent_id . ')')->whereIn('user_id', $request->userIds)->get();
        // pre_print($members);
        if (!empty($members)) {
            foreach ($members as $member) {

                $userInfo = User::where('id', $member->user_id)->first();
                $userInfo->note_description = $request->value;
                $userInfo->note_visible_from = $request->fromDate;
                $userInfo->note_visible_to = $request->toDate;
                $userInfo->save();
            }
        }

        $hashtags = $this->getHastTags($parent_id);

        $data['success'] = TRUE;
        $data['message'] = 'HashTag is successfully added for ' . count($request->userIds) . ' members.';
        $data['data'] = array('hashtags' => $hashtags);
        return response()->json($data, 201);
    }

    public function update_members_hashtags(Request $request)
    {
        $token = $request->user();
        // $parent_id = parent_id($token);
        // $members = BusinessUser::select('user_id')->whereRaw('(parent_id = ' . $parent_id . ' OR user_id = ' . $parent_id . ')')->where('user_id', $request->id)->get();
        // // pre_print($members);
        // $hashtag = [];
        // if (!empty($members)) {
        //     foreach ($members as $member) {

        //     }
        // }
        $userInfo = User::where('id', $request->id)->first();
        if ($request->has('value')) {
            $userInfo->note_description = $request->value;
        }

        if ($request->has('fromDate')) {
            $userInfo->note_visible_from = $request->fromDate;
        }

        if ($request->has('toDate')) {
            $userInfo->note_visible_to = $request->toDate;
        }
        //
        $userInfo->save();

        $dateTime = new DateTime($userInfo->note_visible_from);
        $userInfo->note_visible_from = $dateTime->format("Y-m-d\TH:i:s.u\Z");

        $dateTime = new DateTime($userInfo->note_visible_to);
        $userInfo->note_visible_to = $dateTime->format("Y-m-d\TH:i:s.u\Z");

        $hashtag = ['id' => $userInfo->id, 'userId' => $userInfo->id, 'value' => $userInfo->note_description, 'fromDate' => $userInfo->note_visible_from, 'toDate' => $userInfo->note_visible_to];

        $data['success'] = TRUE;
        $data['message'] = 'HashTag is successfully updated.';
        $data['data'] = $hashtag;
        return response()->json($data, 201);
    }

    public function delete_members_hashtags(Request $request)
    {
        $validations['userIds'] = 'required';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $messages = json_decode(json_encode($validator->messages()), true);
            $i = 0;
            foreach ($messages as $key => $val) {
                $data['errors'][$i]['error'] = $val[0];
                $data['errors'][$i]['field'] = $key;
                $i++;
            }

            $data['success'] = FALSE;
            $data['message'] = 'Required data is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        // pre_print($request->userIds);
        $token = $request->user();
        $parent_id = parent_id($token);
        $members = BusinessUser::select('user_id', 'user_role as role')->whereRaw('(parent_id = ' . $parent_id . ' OR user_id = ' . $parent_id . ')')->whereIn('user_id', $request->userIds)->get();
        // pre_print($members);
        $hashtags = [];
        if (!empty($members)) {
            foreach ($members as $member) {
                $userInfo = User::where('id', $member->user_id)->first();
                $userInfo->note_description = NULL;
                $userInfo->note_visible_from = NULL;
                $userInfo->note_visible_to = NULL;
                $userInfo->save();

                $hashtags[] = $userInfo->id;
            }
        }

        $data['success'] = TRUE;
        $data['message'] = 'HashTag is successfully removed for ' . count($hashtags) . ' members.';
        $data['data'] = array('hashtags' => $hashtags);
        return response()->json($data, 201);
    }

    private function getHastTags($parent_id)
    {
        $members = BusinessUser::select('user_id', 'user_role as role')->whereRaw('parent_id = ' . $parent_id . ' OR user_id = ' . $parent_id)->get();
        $hashtags = [];
        if (!empty($members)) {
            foreach ($members as $member) {

                $userInfo = User::select('id', 'id as userId', 'note_description as value', 'note_visible_from as fromDate', 'note_visible_to as toDate')->where('id', $member->user_id)->where('note_description', '!=', NULL)->first();

                if ($userInfo == null) {
                    // pre_print($member->user_id);
                    continue;
                }

                $dateTime = new DateTime($userInfo->fromDate);
                $userInfo->fromDate = $dateTime->format("Y-m-d\TH:i:s.u\Z");

                $dateTime = new DateTime($userInfo->toDate);
                $userInfo->toDate = $dateTime->format("Y-m-d\TH:i:s.u\Z");

                $hashtags[] = $userInfo;
            }
        }

        return $hashtags;
    }

    public function update_members_data(Request $request)
    {
        $validations['ids'] = 'required|string'; //comma separated

        if (isset($_POST['op_type']) && in_array($request->op_type, ['profile_photo', 'profile_banner', 'company_logo'])) {
            $validations['image'] = 'required';
        }

        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Required fields are missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $parent_id = parent_id($token);

        $user_ids = explode(',', $request->ids);
        // $BusinessUser = BusinessUser::whereIn('user_id', $user_ids)->whereRaw('parent_id = ' . $parent_id . ' OR user_id = ' . $parent_id);

        $BusinessUser = BusinessUser::whereIn('user_id', $user_ids); //->where('parent_id', $token->id);
        if ($BusinessUser->count() == 0) {
            $data['success'] = FALSE;
            $data['message'] = 'No record found.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $Users = $BusinessUser->select('users.*')->join('users', 'users.id', '=', 'business_users.user_id')->get();
        $list = [];
        $message = 'Updated successfully.';
        foreach ($Users as $user) {

            $user_info = [];
            $user_info['id'] = $user->id;

            $Obj = UserSettings::where('user_id', $user->id);
            if ($Obj->count() == 0) {
                UserSettings::updateOrCreate(['user_id' => $user->id], ['updated_by' => $user->id]);
                $Obj = UserSettings::where('user_id', $user->id)->first();
            } else {
                $Obj = $Obj->first();
            }

            $UserObj = User::findorfail($user->id);

            if ($request->has('capture_lead')) {
                $Obj->capture_lead = $request->capture_lead;
                $Obj->save();
                if ($request->capture_lead == 1) {
                    $UserObj->open_direct = 0;
                    $UserObj->save();
                }

                $user_info['capture_lead'] = (int) $Obj->capture_lead;
                $user_info['open_direct'] = (int) $UserObj->open_direct;
            }

            if ($request->has('open_direct')) {
                $UserObj->open_direct = $request->open_direct;
                $UserObj->save();

                if ($request->open_direct == 1) {
                    $Obj->capture_lead = 0;
                    $Obj->save();
                }

                $user_info['capture_lead'] = (int) $Obj->capture_lead;
                $user_info['open_direct'] = (int) $UserObj->open_direct;
            }

            if ($request->has('first_name')) {
                $UserObj->first_name = $request->first_name;
                $UserObj->updated_by = $token->id;
                $UserObj->save();
                $user_info['open_direct'] = $UserObj->first_name;
            }

            if ($request->has('last_name')) {
                $UserObj->last_name = $request->last_name;
                $UserObj->updated_by = $token->id;
                $UserObj->save();
                $user_info['open_direct'] = $UserObj->last_name;
            }

            if ($request->has('bio')) {
                $UserObj->bio = $request->bio;
                $UserObj->updated_by = $token->id;
                $UserObj->save();
                $user_info['open_direct'] = $UserObj->bio;
            }

            if ($request->has('designation')) {
                $UserObj->designation = $request->designation;
                $UserObj->updated_by = $token->id;
                $UserObj->save();
                $user_info['open_direct'] = $UserObj->designation;
            }

            if ($request->has('dob')) {
                $UserObj->dob = $request->dob;
                $UserObj->updated_by = $token->id;
                $UserObj->save();
                $user_info['open_direct'] = $UserObj->dob;
            }

            if ($request->has('gender')) {
                $UserObj->gender = $request->gender;
                $UserObj->updated_by = $token->id;
                $UserObj->save();
                $user_info['open_direct'] = $UserObj->gender;
            }

            if ($request->has('company_address')) {
                $UserObj->company_address = $request->company_address;
                $UserObj->updated_by = $token->id;
                $UserObj->save();
                $user_info['open_direct'] = $UserObj->company_address;
            }

            if ($request->has('company_name')) {
                $UserObj->company_name = $request->company_name;
                $UserObj->updated_by = $token->id;
                $UserObj->save();
                $user_info['open_direct'] = $UserObj->company_name;
            }

            if ($request->has('connect_button')) {
                $Obj->show_connect = $request->connect_button;
                $Obj->save();

                $user_info['connect_button'] = (int) $Obj->show_connect;
                $message = "The Connect button will be visible for all these members.";
            }

            if ($request->has('save_contact_button')) {
                $Obj->show_contact = $request->save_contact_button;
                $Obj->save();

                $user_info['save_contact_button'] = (int) $Obj->show_contact;
                $message = "The Save Contact button will be visible for all these members.";
            }

            $list[] = $user_info;
        }

        $data['success'] = TRUE;
        $data['message'] = $message;
        $data['data'] = $list;
        return response()->json($data, 201);
    }

    public function add_same_profile_all_members(Request $request)
    {


        $validations['code'] = 'required';
        $validations['title'] = 'required';
        $validations['is_highlighted'] = 'required';
        $validations['is_global'] = 'required';
        if ($request->code != 'file') {
            $validations['value'] = 'required|string';
        }



        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $messages = json_decode(json_encode($validator->messages()), true);
            $i = 0;
            foreach ($messages as $key => $val) {
                $data['errors'][$i]['error'] = $val[0];
                $data['errors'][$i]['field'] = $key;
                $i++;
            }

            $data['success'] = FALSE;
            $data['message'] = 'Required fields are missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();

        $date = date('Ymd');
        $Profile = Profile::where('profile_code', $request->code);
        if ($Profile->count() > 0) {

            $global_id = 0;
            // eql();
            // if ($request->is_global == false || $request->is_global == 'false' || $request->is_global == '0' ||
            if ($request->is_global == 0) {
                $BusinessUser = BusinessUser::where('user_id', $request->user_id);
                $message = "The button is added to " . $token->first_name . ' ' . $token->last_name . "'s card.";
            } else {
                // $BusinessUser = BusinessUser::whereRaw('(parent_id = ' . $token->id . ' OR user_id = ' . $token->id . ')');
                $BusinessUser = BusinessUser::whereRaw('(business_users.parent_id = ' . $token->id . ' OR business_users.user_id = ' . $token->id . ')');

                $global_id = $token->id . random_int(100, 999);
                $message = "The button is added to all members.";
            }

            if ($BusinessUser->count() == 0) {
                $data['success'] = FALSE;
                $data['message'] = 'No record found.';
                $data['data'] = (object)[];
                return response($data, 400);
            }

            $icon = $file_image = $icon_svg = '';
            if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
                if (isset($_FILES['icon']) && $_FILES['icon']['name'] != '') {
                    $upload_dir = icon_dir();
                    $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
                    if ($response['success'] == TRUE) {
                        if ($response['filename'] != '') {
                            $icon = $date . '/' . $response['filename'];
                        }
                    }
                }
            }

            if ($request->has('icon_svg') && $request->icon_svg != '') {
                $icon_svg = $request->icon_svg;
                $icon = '';
            }

            if ($request->has('icon')) {
                if ($request->icon == '') {
                    $icon = $request->icon;
                }
            }
            // end
            if (isset($_FILES['file_image']) && $_FILES['file_image']['name'] != '') {
                $upload_dir = file_dir();
                $response = upload_file($request, 'file_image', $upload_dir . '/' . $date);

                if ($response['success'] == TRUE) {
                    if ($response['filename'] != '') {
                        $file_image = $date . '/' .  $response['filename'];
                    }
                }
            }

            $Users = $BusinessUser->select('users.*')->join('users', 'users.id', '=', 'business_users.user_id')->get();
            // lq();
            // pre_print(json_decode(json_encode($Users)));
            $list = [];
            foreach ($Users as $user) {

                if ($request->is_global == 0) {
                    $message = "The button is added to " . $user->first_name . ' ' . $user->last_name . "'s card.";
                }

                $Obj = new CustomerProfile;
                $Obj->profile_link = $request->value;
                $Obj->profile_code = $request->code;
                $Obj->is_business = 0;
                $Obj->global_id = $global_id;
                $Obj->user_id = $user->id;
                $Obj->status = 1;
                if ($request->has('visible')) {
                    $Obj->status = $request->visible;
                }

                $maxProfileSequence = CustomerProfile::select(\DB::raw('ifnull(MAX(sequence),-1) as sequence'))->where('profile_code', '!=', 'contact-card')->where('user_id', $user->id)->first();

                if ($request->has('sequence')) {
                    $CustomerProfile = CustomerProfile::where('user_id', $user->id)->where('sequence', $request->sequence);
                    if ($CustomerProfile->count() > 0) {
                        $CustomerProfileObj = $CustomerProfile->first();
                        $CustomerProfileObj->sequence = (int) $maxProfileSequence->sequence + 1;
                        $CustomerProfileObj->save();
                    }
                    $Obj->sequence = (int)$request->sequence;
                } else {
                    // $CustomerProfileCount = CustomerProfile::where('user_id', $user->id)->count();
                    $Obj->sequence = (int) $maxProfileSequence->sequence + 1; //$CustomerProfileCount;
                    $Obj->sequence = ($Obj->sequence < 1) ? 0 : $Obj->sequence;
                }

                if ($request->has('is_focused')) {
                    $Obj->is_focused = $request->is_focused;
                }

                if ($request->has('is_highlighted')) {
                    $Obj->is_focused = $request->is_highlighted;
                }

                $Obj->created_by = $token->id;
                $Obj->created_at = Carbon::now();

                if ($request->title != NULL || $request->title != '') {
                    $Obj->title = $request->title;
                }

                // new logic for icon_svg
                // if ($icon != '' && $icon != NULL) {
                //     $Obj->icon = $icon;
                // } else {
                //     if (!empty($request->icon_svg) && !empty($Profile->first()->icon_svg_default)) {
                //         $Obj->icon_svg_default = $request->icon_svg;
                //     } else {
                //         $Obj->icon = $Profile->first()->icon;
                //     }
                // }
                // end new logic for icon

                $Obj->icon_svg_default = $icon_svg;
                if ($icon != '') {
                    $Obj->icon = $icon;
                } else if ($icon_svg != '') {
                    $Obj->icon = '';
                } else {
                    $icon_svg_default = !empty($Profile->first()->icon_svg_default) ? $Profile->first()->icon_svg_default : $icon_svg;
                    $Obj->icon_svg_default = $icon_svg_default;
                }

                if ($file_image != '') {
                    $Obj->file_image = '';
                }

                if ($request->code != 'file') {
                    $Obj->save();
                } else {
                    if ($Obj->file_image == '') {
                        $data['success'] = FALSE;
                        $data['message'] = 'File uploading failed.';
                        $data['data'] = (object)[];
                        return response($data, 400);
                    }

                    $Obj->save();
                }

                $is_business = 0;
                $ContactCard = ContactCard::where('user_id', $user->id)->where('is_business', $is_business);
                if ($request->profile_code != 'wifi') {
                    if ($ContactCard->count() > 0) {
                        $ContactCard = $ContactCard->first();
                        $ContactCard->customer_profile_ids = trim($ContactCard->customer_profile_ids . ',' . $Obj->id, ',');
                        $ContactCard->is_business = $is_business;
                        $ContactCard->updated_by = $token->id;
                        $ContactCard->save();
                    } else {
                        $ContactCard = new ContactCard;
                        $ContactCard->customer_profile_ids = $Obj->id;
                        $ContactCard->is_business = $is_business;
                        $ContactCard->user_id = $user->id;
                        $ContactCard->created_by = $token->id;
                        $ContactCard->save();
                    }
                }

                resetProfilesSequence($Obj->user_id);

                $query = \DB::table('customer_profiles AS cp')->select(
                    'cp.id',
                    'cp.profile_link',
                    'cp.profile_code',
                    'cp.title as cp_title',
                    'cp.icon as cp_icon',
                    'cp.is_business',
                    'p.title',
                    'p.title_de',
                    'p.base_url',
                    'p.icon',
                    'cp.user_id',
                    'cp.sequence',
                    'p.is_pro',
                    'p.type',
                    'cp.status as visible',
                    'cp.is_focused as is_highlighted',
                    'cp.global_id',
                    'cp.status',
                    'p.icon_svg_default as profile_icon_svg_default',
                    \DB::raw('COALESCE(cp.icon_svg_default, p.icon_svg_default) as icon_svg'),
                    'p.id as link_type_id'
                ); //'cp.is_direct as open_direct',
                $query->leftJoin('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
                $query = $query->where('cp.id', $Obj->id);
                $profiles = $query->get();

                $Home = new HomeController;
                $profiles = $Home->profile_meta($profiles, $token);
                // pre_print($Obj);
                if (!empty($profiles)) {
                    $Obj = (object)$profiles[0];

                    if ($Obj->icon != '') {
                        $Obj->icon_url = $Obj->icon;
                    }

                    if (isset($Obj->file_image) && $Obj->file_image != '') {
                        $Obj->file_image = $Obj->file_image;
                    }
                } else {
                    $Obj = CustomerProfile::findorfail($Obj->id);

                    if ($Obj->icon != '') {
                        $Obj->icon_url = icon_url() . $Obj->icon;
                    }

                    if (isset($Obj->file_image) && $Obj->file_image != '') {
                        $Obj->file_image = file_url() . $Obj->file_image;
                    }
                }
                $Obj->visible = (isset($Obj->status) && $Obj->status == 1) ? true : (isset($Obj->visible) ? $Obj->visible : false);
                $Obj->is_highlighted = ($Obj->is_highlighted == 1) ? true : false;
                $Obj->profile_link = ($Obj->profile_code != 'file') ? $Profile->first()->base_url . $Obj->profile_link : $Obj->file_image;
                $Obj->href = $Obj->profile_link;
                $Obj->value = $Obj->profile_link_value;
                if ($Obj->icon_svg == '') {
                    $Obj->icon_svg = icon_svg_default($request, $Obj->profile_code);
                }

                if ($global_id == 0) {
                    $Obj->global_id = NULL;
                }

                unset($Obj->file_image, $Obj->status, $Obj->created_by, $Obj->created_at, $Obj->updated_by, $Obj->updated_at, $Obj->is_direct, $Obj->added_to_contact_card, $Obj->is_business, $Obj->title_de, $Obj->base_url, $Obj->profile_link, $Obj->profile_link_value, $Obj->icon);

                $list[] = $Obj;
            }

            $data['success'] = TRUE;
            $data['message'] = $message;
            $data['data'] = array('links' => $list);
            return response()->json($data, 201);
        } else {

            $data['success'] = FALSE;
            $data['message'] = 'Invalid profile code.';
            $data['data'] = (object)[];
            return response($data, 422);
        }
    }

    public function update_same_profile_all_members(Request $request)
    {
        $token = $request->user();
        $date = date('Ymd');
        if ($request->has('code') && $request->code != '') {
            $Profile = Profile::where('profile_code', $request->code);
            if ($Profile->count() == 0) {
                $data['success'] = FALSE;
                $data['message'] = 'Invalid profile code.';
                $data['data'] = (object)[];
                return response($data, 422);
            }
        }

        $ObjCustomerProfile = CustomerProfile::where('id', $request->customer_profile_id);
        if ($ObjCustomerProfile->count() == 0) {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid data.';
            $data['data'] = (object)[];
            return response($data, 422);
        }

        $ObjCustomerProfile = $ObjCustomerProfile->first();
        $global_id = $ObjCustomerProfile->global_id;

        if ($global_id == '' || $global_id == NULL || $global_id == 'NULL' || $global_id == null || $global_id == 'null' || $global_id == '0' || $global_id == 0) {
            $CustomerProfileUpdateObjects = CustomerProfile::where('id', $request->customer_profile_id)->get();
            $message = "The button is updated to " . $token->first_name . ' ' . $token->last_name . "'s card.";
        } else {

            $CustomerProfileUpdateObjects = [];
            if ($global_id != 0 && $ObjCustomerProfile->global_id == $global_id) {
                $CustomerProfileUpdateObjects = CustomerProfile::where('global_id', $global_id)->get();
            }

            $message = "The button is updated to all members.";
        }

        $icon = $file_image = '';
        if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
            if (isset($_FILES['icon']) && $_FILES['icon']['name'] != '') {
                $upload_dir = icon_dir();
                $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
                if ($response['success'] == TRUE) {
                    if ($response['filename'] != '') {
                        $icon = $date . '/' . $response['filename'];
                    }
                }
            }
        }

        // end
        if (isset($_FILES['file_image']) && $_FILES['file_image']['name'] != '') {
            $upload_dir = file_dir();
            $response = upload_file($request, 'file_image', $upload_dir . '/' . $date);

            if ($response['success'] == TRUE) {
                if ($response['filename'] != '') {
                    $file_image = $date . '/' .  $response['filename'];
                }
            }
        }

        // pre_print(json_decode(json_encode($CustomerProfileUpdateObjects)));
        $list = [];
        foreach ($CustomerProfileUpdateObjects as $Obj) {

            if ($request->has('code') && $request->code != '') {
                $Obj->profile_code = $request->code;
            }

            // new svg method

            // if ($icon != '' && $icon != NULL) {
            //     $Obj->icon = $icon;
            // } else {
            //     if (!empty($request->icon_svg) && !empty($Obj->first()->icon_svg_default)) {
            //         $Obj->icon_svg_default = $request->icon_svg;
            //     } else {
            //         $Obj->icon = $Obj->first()->icon;
            //     }
            // }

            // end new svg method

            if ($icon != '') {
                $Obj->icon = $icon;
            }

            if ($request->has('icon_svg') && $request->icon_svg != '') {
                $Obj->icon_svg_default = $request->icon_svg;
                $Obj->icon = $icon = '';
            }

            if ($icon != '') {
                $Obj->icon_svg_default = '';
            }

            if ($request->has('value') && $request->value != '') {
                $Obj->profile_link = $request->value;
            }

            if ($request->has('visible')) {
                $Obj->status = $request->visible;
            }

            if ($request->has('is_focused')) {
                $Obj->is_focused = $request->is_focused;
            }

            if ($request->has('is_highlighted')) {
                $Obj->is_focused = $request->is_highlighted;
            }

            $Obj->updated_by = $token->id;

            if ($request->title != NULL || $request->title != '') {
                $Obj->title = $request->title;
            }

            if ($file_image != '') {
                $Obj->file_image = '';
            }

            if ($request->code != 'file') {
                $Obj->save();
            } else {
                if ($Obj->file_image == '') {
                    $data['success'] = FALSE;
                    $data['message'] = 'File uploading failed.';
                    $data['data'] = (object)[];
                    return response($data, 400);
                }

                $Obj->save();
            }

            resetProfilesSequence($Obj->user_id);

            $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.title as cp_title', 'cp.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.user_id', \DB::raw('COALESCE(cp.icon_svg_default, p.icon_svg_default) as icon_svg'), 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible', 'cp.is_focused as is_highlighted', 'cp.global_id', 'cp.status'); //'cp.is_direct as open_direct',
            $query->leftJoin('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
            $query = $query->where('cp.id', $Obj->id);
            $profiles = $query->get();

            $Home = new HomeController;
            $profiles = $Home->profile_meta($profiles, $token);
            // pre_print($Obj);
            if (!empty($profiles)) {
                $Obj = (object)$profiles[0];

                if ($Obj->icon != '') {
                    $Obj->icon_url = $Obj->icon;
                }

                if (isset($Obj->file_image) && $Obj->file_image != '') {
                    $Obj->file_image = $Obj->file_image;
                }
            } else {
                $Obj = CustomerProfile::findorfail($Obj->id);

                if ($Obj->icon != '') {
                    $Obj->icon_url = icon_url() . $Obj->icon;
                }


                if (isset($Obj->file_image) && $Obj->file_image != '') {
                    $Obj->file_image = file_url() . $Obj->file_image;
                }
            }

            // $Profile = Profile::where('profile_code', $Obj->profile_code);

            $Obj->visible = (isset($Obj->status) && $Obj->status == 1) ? true : (isset($Obj->visible) ? $Obj->visible : false);
            $Obj->is_highlighted = ($Obj->is_highlighted == 1) ? true : false;
            // $Obj->profile_link = ($Obj->profile_code != 'file') ? $Profile->first()->base_url . $Obj->profile_link : $Obj->file_image;
            $Obj->href = $Obj->profile_link;
            $Obj->value = $Obj->profile_link_value;

            if ($global_id == 0) {
                $Obj->global_id = NULL;
            }

            if ($Obj->icon_url != '') {
                $Obj->icon_svg = icon_svg_default($request, $Obj->profile_code);
            }

            unset($Obj->file_image, $Obj->status, $Obj->created_by, $Obj->created_at, $Obj->updated_by, $Obj->updated_at, $Obj->is_direct, $Obj->added_to_contact_card, $Obj->is_business, $Obj->title_de, $Obj->base_url, $Obj->profile_link, $Obj->profile_link_value, $Obj->icon, $Obj->icon_svg_default);

            $list[] = $Obj;
        }



        $data['success'] = TRUE;
        $data['message'] = $message;
        $data['data'] = array('links' => $list);
        return response()->json($data, 201);
    }

    public function delete_same_profile_all_members(Request $request)
    {
        $token = $request->user();
        $global_id = $request->global_id;
        if ($global_id == '' || $global_id == NULL || $global_id == 'NULL' || $global_id == null || $global_id == 'null' || $global_id == '0' || $global_id == 0) {
            $CustomerProfileUpdateObjects = CustomerProfile::where('id', $request->customer_profile_id)->get();
            $message = "The button is deleted from " . $token->first_name . ' ' . $token->last_name . "'s card.";
        } else {

            $ObjCustomerProfile = CustomerProfile::where('id', $request->customer_profile_id);
            if ($ObjCustomerProfile->count() == 0) {
                $data['success'] = FALSE;
                $data['message'] = 'Invalid data.';
                $data['data'] = (object)[];
                return response($data, 422);
            }

            $ObjCustomerProfile = $ObjCustomerProfile->first();
            $CustomerProfileUpdateObjects = [];

            if ($global_id != 0 && $ObjCustomerProfile->global_id == $global_id) {
                $CustomerProfileUpdateObjects = CustomerProfile::where('global_id', $global_id)->get();
            }

            $message = "The button is deleted for all members.";
        }

        $list = [];
        foreach ($CustomerProfileUpdateObjects as $Obj) {
            $list[] = $Obj->id;
            $Obj->delete();
        }

        $data['success'] = TRUE;
        $data['message'] = $message;
        $data['data'] = array('linkIds' => $list);
        return response()->json($data, 201);
    }

    private function clear_encoding_str($value)
    {
        // str_replace("'", "/'", $value);
        if (is_array($value)) {
            $clean = [];
            foreach ($value as $key => $val) {
                $key = str_replace(' ', '-', mb_convert_encoding(trim(filter_var($key, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH)), 'UTF-8'));
                $key = str_replace(':', '', str_replace('(', '', str_replace(')', '', str_replace('--', '-', $key))));
                $clean[$key] = mb_convert_encoding(mb_convert_encoding($val, 'UTF-8'), 'UTF-8', 'UTF-8');
            }
            return $clean;
        }
        return mb_convert_encoding(trim($value), 'UTF-8', 'UTF-8');
    }
}
