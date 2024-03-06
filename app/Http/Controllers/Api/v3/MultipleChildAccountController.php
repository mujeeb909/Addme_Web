<?php

/** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mail;
use \Mailjet\Resources;
use App\Models\User;
use App\Models\BusinessInfo;
use App\Models\BusinessUser;
use App\Models\ContactCard;
use App\Models\CustomerProfile;
use App\Models\DeleteAccount;
use App\Models\TapsViews;
use App\Models\TempUsers;
use App\Models\UniqueCode;
use App\Models\UserNote;
use App\Models\UserSettings;
use App\Models\PlatformIntegration;
use App\Models\Platform;

class MultipleChildAccountController extends Controller
{
    /** multiple-account
     * check Users Childs Account Limits With New Email
     *
     * pass  parent_id
     * pass  new_account
     */
    public function childs_account_limits($parent_id, $new_account)
    {
        $total_childs_account = User::where('parent_id', $parent_id)->count();
        $limit = 5; // Assuming the account limit is 5
        $total = $total_childs_account + $new_account;
        if ($total > $limit) {
            return 0; // Company account limit reached
        } else {
            return 1; // Account creation allowed
        }
    }

    //------------------------------------------------------------------------------
    /**
     * Child account signup With New Email
     *
     * @param \Illuminate\Http\Request email
     * @param \Illuminate\Http\Request password
     */
    public function signupWithNewEmail(Request $request)
    {
        $token = $request->user();
        $validations['email'] = 'required|string|email|unique:users';
        $validations['password'] = 'required|string|confirmed|min:6';

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
        $parent_id = parent_id($token);
        if($token->user_group_id == 2){
            $new_account = 1;
            $account_limit = $this->childs_account_limits($parent_id, $new_account);
            $account_limits = 5;
            if ($account_limit != 1)
             {
                $data['success'] = FALSE;
                $data['message'] = ($account_limits) ? 'Company account limit reached. Please get in contact with the AddMee Customer Care.' : 'Company account limit reached. Please get in contact with the AddMee Customer Care.';
                $data['data'] = (object)[];
                return response($data, 400);
            }
        }

        $User = new TempUsers();
        $User->email = $request->email;
        $User->username = $request->has('username') && $request->username != '' ? $request->username : NULL;
        $User->password = bcrypt($request->password);
        $User->fcm_token = isset($request->fcm_token) ? $request->fcm_token : '';
        if (isset($request->gender) && $request->gender != '') {
            $User->gender = isset($request->gender) ? (int)$request->gender : 3;
        }

        if (isset($request->user_group_id)) {
            $User->user_group_id = $request->user_group_id;
        }

        $User->allow_data_usage = isset($request->allow_data_usage) ? $request->allow_data_usage : 0;
        $User->device_type = isset($request->device_type) ? $request->device_type : 0;
        $User->device_id = isset($request->device_id) ? $request->device_id : 0;
        $User->vcode = rand(111111, 999999);;
        $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
        $User->created_at = Carbon::now();
        $User->privacy_policy_date = Carbon::now();
        $User->license_date = Carbon::now();
        $User->parent_id = $parent_id;
        $User->child_account_type = 'withNewEmail';
        $User->save();

        // $html = 'Hi ' . $User->username . ',<br><br> Your OTP is: ' . $User->vcode . '';
        if (isset($_POST['language']) && trim($_POST['language']) == 'de') {
            $html = "Hallo,<br><br>
            willkommen bei " . config("app.name", "") . ".<br><br>
            Hiermit erhältst Du Dein Initial-Passwort, um Deine Registrierung in der " . config("app.name", "") . " App abzuschließen.
            <br><br>
            Dein Initial-Passwort lautet: " . $User->vcode . "<br><br>
            Bitte gib dieses Intial-Passwort in der " . config("app.name", "") . " App ein.<br><br>
            Hast Du Dich nicht über die " . config("app.name", "") . " App registriert? Dann brauchst Du nichts weiter zu tun, der Account wird automatisch wieder gelöscht.<br><br>
            Viel Erfolg und einen guten Start mit " . config("app.name", "") . ".<br><br>
            Dein " . config("app.name", "") . " Kundenservice";
            $subject = 'Willkommen bei ' . config("app.name", "");
        } else {

            $html = "Hello,<br><br>
            Welcome to " . config("app.name", "") . ".<br><br>
            This is your initial password to complete your registration in the " . config("app.name", "") . " app.<br><br>
            Your initial password is: " . $User->vcode . ".<br><br>
            Please enter this initial password in the " . config("app.name", "") . " app.<br><br>
            Have you not registered via the " . config("app.name", "") . " app? Then you don't need to do anything else, the account will be deleted automatically.
            <br><br>
            Good luck and a good start with " . config("app.name", "") . ".<br><br>
            Your " . config("app.name", "") . " Customer Service";
            $subject =  'Welcome to ' . config("app.name", "");
        }

        $emailInfo["email"] = $User->email;
        $emailInfo["otp"] = $User->vcode;
        $emailInfo["username"] = $User->username;
        $emailInfo["subject"] = $subject;
        if (strtolower(config("app.name", "")) != 'addmee') {

            Mail::send('mail.sent_otp', $emailInfo, function ($message) use ($emailInfo) {
                $message->to($emailInfo["email"])->subject($emailInfo["subject"]);
            });
        } else {
            $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);

            $body = [
                'Messages' => [
                    [
                        'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                        'To' => [['Email' => $User->email, 'Name' => $User->username]],
                        'Subject' => $subject,
                        'TextPart' => $subject,
                        'HTMLPart' => $html,
                        'CustomID' => config("app.name", "")
                    ]
                ]
            ];

            $response = $mj->post(Resources::$Email, ['body' => $body]);
        }

        unset($User->vcode, $User->vcode_expiry, $User->access_token, $User->password);
        $data['success'] = TRUE;
        $data['message'] = 'Registration successful.';
        $data['data'] = array('user' => $User);
        return response()->json($data, 201);
    }

    /**
     * switch Account
     * @param \Illuminate\Http\Request coa_group_id
     * @return \Illuminate\Http\Response
     */
    public function switchAccount(Request $request)
    {
        $validations['id'] = 'required|int';
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

        $err = 'Your account is inactive. Please get in contact with your company administrator.';
        if ($request->has('language')) {
            if ($request->language == 'de') {
                $err = 'Der Account is Deaktiviert. Bitte an den Unternehmens-Administrator wenden.';
            }
        }

        if (current_method() == 'customer-login') {

            $User = User::where('id', $request->id)->whereIn('user_group_id', [2, 3, 7, 8, 9, 10])->first();
            if ($User && $User->user_group_id == 2) {
                $BusinessUser = BusinessUser::where('user_id', $User->id)->where('user_role', 'admin');
                if ($BusinessUser->count() == 0) {
                    $data['success'] = FALSE;
                    $data['message'] = 'Account does not exist.';
                    $data['data'] = (object)[];
                    return response($data, 422);
                }
            }

            $err = 'Your company licenses are inactive. Please get in contact with the AddMee Customer Care (customercare@addmee.de).';
            if ($request->has('language')) {
                if ($request->language == 'de') {
                    $err = 'Your company licenses are inactive. Please get in contact with the AddMee Customer Care (customercare@addmee.de).';
                }
            }
        } else {
            $User = User::where('id', $request->id)->whereIn('user_group_id', [2, 3, 7, 8, 9, 10])->first();
        }

        if ($User) {

            $password = 'yIek7QPAmFSLQCOpdwHx';
            $User->id;
            if ($request->id == $User->id) {

                if ($User->status == 0) {
                    $data['success'] = FALSE;
                    $data['message'] = $err;
                    $data['data'] = (object)[];
                    return response($data, 401);
                }

                if (parent_status($User) == 0) {

                    $data['success'] = FALSE;
                    $data['message'] = $err;
                    $data['data'] = (object)[];
                    return response($data, 401);
                }

                $token = $request->user();
                $findUser = User::where('id',$token->id)->update([
                    'switch_account_status' => 0
                ]);

                $User->last_login = Carbon::now();
                $User->switch_account_status = 1;
                $User->fcm_token = $request->fcm_token;
                $User->device_type = isset($request->device_type) ? $request->device_type : $User->device_type;
                $User->device_id = isset($request->device_id) ? $request->device_id : $User->device_id;
                $User->save();

                $template = anyTemplateAssigned($User->id);
                // pre_print($template);

                $ChildAccounts = User::where('parent_id', $User->id)
                ->select('id', 'first_name', 'last_name', 'designation', 'company_name', 'company_friendly_name', 'company_address', 'company_logo', 'email', 'username', 'dob', 'gender', 'user_group_id', 'logo', 'bio', 'banner', 'is_pro', 'is_public', 'profile_view', 'status', 'provider', 'provider_id', 'device_id', 'device_type', 'platform', 'email_verified_at', 'last_login', 'subscription_date', 'subscription_expires_on', 'open_direct', 'fcm_token', 'allow_data_usage', 'privacy_policy_date', 'license_date', 'first_login', 'forced_reset', 'note_description', 'note_visible_from', 'note_visible_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'role', 'parent_id', 'switch_account_status')
                ->get();
                if ($ChildAccounts->count() > 0) {
                    $ChildAccounts = userChildsObj($ChildAccounts, $template);
                }else{
                    $ChildAccounts = null ;
                }

                $settings = userSettingsObj($User->id, $template);
                if (count($settings) > 0) {
                    $settings['capture_lead'] = (string)$settings['capture_lead'];
                }

                if (!empty($settings) && $settings['2fa_enabled'] == 1) {
                    $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
                    $User->vcode = rand(111111, 999999);
                    $User->save();

                    $html = 'Hi ' . $User->username . ',<br><br> Your One Time Password is: ' . $User->vcode . '<br><br>Regards<br><br>Support Team';
                    //sendgrid_api($html, env("MAIL_FROM_NAME", "").' Code is: '.$User->vcode, $User->email, $User->name);
                    $emailInfo["email"] = $User->email;
                    $emailInfo["subject"] = config("app.name", "") . ': Your One-Time Password Request';
                    $emailInfo["otp"] = $User->vcode;
                    $emailInfo["username"] = $User->username;

                    if (strtolower(config("app.name", "")) != 'addmee') {

                        // Mail::send('mail.sent_otp', $emailInfo, function ($message) use ($emailInfo) {
                        //     $message->to($emailInfo["email"])->subject($emailInfo["subject"]);
                        // });
                    } else {
                        $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
                        $body = [
                            'Messages' => [
                                [
                                    'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                                    'To' => [['Email' => $User->email, 'Name' => $User->first_name . ' ' . $User->last_name]],
                                    'Subject' => config("app.name", "") . ": Your One-Time Password Request",
                                    'TextPart' => config("app.name", "") . ": Your One-Time Password Request",
                                    'HTMLPart' => $html,
                                    'CustomID' => config("app.name", "")
                                ]
                            ]
                        ];

                        $response = $mj->post(Resources::$Email, ['body' => $body]);
                        // Mail::send('mail.sent_otp', $emailInfo, function ($message) use ($emailInfo) {
                        //     $message->to($emailInfo["email"])->subject($emailInfo["subject"]);
                        // });
                    }

                    $User = UserObj($User, 0, $template);
                    unset($settings['2fa_enabled'], $settings['created_by'], $settings['created_at'], $settings['updated_by'], $settings['updated_at'], $settings['user_old_data'], $settings['settings_old_data']);
                    $data['success'] = TRUE;
                    $data['message'] = 'OTP has been sent.';
                    $data['data'] = array('user' => $User, 'settings' => (object) $settings, 'child_accounts' => $ChildAccounts);
                    return response()->json($data, 201);
                } else {

                    $BusinessInfo = BusinessInfo::where('user_id', $User->id)->first();
                    $request->user()->currentAccessToken()->delete();
                    $access_token = $User->createToken('authToken')->plainTextToken;
                    $User = UserObj($User, 0, $template);

                    $info['type'] = 'user';
                    $info['type_id'] = $User->id;
                    $info['details'] = 'user-login';
                    $info['device_id'] = isset($request->device_id) ? $request->device_id : 0;
                    $info['ip_address'] = getUserIP();
                    $info['created_by'] = $User->id;
                    add_activity($info);

                    unset($settings['2fa_enabled'], $settings['created_by'], $settings['created_at'], $settings['updated_by'], $settings['updated_at'], $settings['user_old_data'], $settings['settings_old_data']);

                    $BusinessUser = BusinessUser::where('user_id', $User->id);
                    if ($BusinessUser->count() > 0) {
                        $User->role = $BusinessUser->first()->user_role;
                    } else {
                        if (isset($User->role) && $User->role == 'admiinn') {
                            $User->role = 'user';
                        }
                    }

                    $data['success'] = TRUE;
                    $data['message'] = 'Logged In Successfully.';
                    $data['data'] = array('user' => $User, 'business_info' => $BusinessInfo, 'access_token' => $access_token, 'settings' => (object) $settings , 'child_accounts' => $ChildAccounts);
                    return response()->json($data, 201);
                }
            } else {
                $data['success'] = FALSE;
                $data['message'] = 'Incorrect Id.';
                $data['data'] = (object)[];
                return response($data, 401);
            }
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Account does not exist.';
            $data['data'] = (object)[];
            return response($data, 422);
        }
    }

    /**
     * child Account Type Update
     *
     * @param \Illuminate\Http\Request user_group_id
     * @return \Illuminate\Http\Response
     */
    public function childAccountTypeUpdate(Request $request)
    {
        $validations['user_group_id'] = 'required|int';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Account Type is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $invalidUserGroupIds = [7, 8, 9, 10];
        if (!in_array($request->user_group_id, $invalidUserGroupIds)) {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid Account Type.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $User = User::findorfail($token->id);
        $User->user_group_id = $request->user_group_id;
        $User->save();

        $data['success'] = TRUE;
        $data['message'] = 'Account Type Added successfully.';
        $data['data'] = array('user' => $User);
        return response()->json($data, 201);
    }

    /**
     *  Account Type Update
     *
     * @param \Illuminate\Http\Request user_group_id
     * @return \Illuminate\Http\Response
     */
    public function AccountTypeUpdate(Request $request)
    {
        $validations['user_group_id'] = 'required|int';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Account Type is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $invalidUserGroupIds = [7, 8, 9, 10];
        if (!in_array($request->user_group_id, $invalidUserGroupIds)) {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid Account Type.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $User = User::findorfail($token->id);
        $User->user_group_id = $request->user_group_id;
        $User->save();

        $data['success'] = TRUE;
        $data['message'] = 'Account Type Added successfully.';
        $data['data'] = array('user' => $User);
        return response()->json($data, 201);
    }

    //------------------------------------------------------------------------------
    /**
     * Child account signup With existing Email
     *
     * @param \Illuminate\Http\Request username
     * @param \Illuminate\Http\Request password
     */
    public function signupWithExistingEmail(Request $request)
    {

        $token = $request->user();
        $validations['username'] = 'required|string|unique:users';
        $validations['password'] = 'required|string|confirmed|min:6';

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

        $parent_id = parent_id($token);
        $parentId = $token->parent_id;
        if (isset($parentId) && $parentId > 0) {
            $parent_id = $parentId;
        }

        if($token->user_group_id == 2 || $token->user_group_id == 7 || $token->user_group_id == 8 || $token->user_group_id == 9 || $token->user_group_id == 10){
            $new_account = 1;
            $account_limit = $this->childs_account_limits($parent_id, $new_account);
            $account_limits = 5;
            if ($account_limit != 1)
             {
                $data['success'] = FALSE;
                $data['message'] = ($account_limits) ? 'Company account limit reached. Please get in contact with the AddMee Customer Care.' : 'Company account limit reached. Please get in contact with the AddMee Customer Care.';
                $data['data'] = (object)[];
                return response($data, 400);
            }
        }

        $User = new TempUsers();

        $User->username = $request->username;
        $User->email = $request->has('email') && $request->email != '' ? $request->email : $request->username;
        $User->password = bcrypt($request->password);
        $User->fcm_token = isset($request->fcm_token) ? $request->fcm_token : '';
        if (isset($request->gender) && $request->gender != '') {
            $User->gender = isset($request->gender) ? (int)$request->gender : 3;
        }

        if (isset($request->user_group_id)) {
            $User->user_group_id = $request->user_group_id;
        }

        $User->allow_data_usage = isset($request->allow_data_usage) ? $request->allow_data_usage : 0;
        $User->device_type = isset($request->device_type) ? $request->device_type : 0;
        $User->device_id = isset($request->device_id) ? $request->device_id : 0;
        $User->vcode = rand(111111, 999999);;
        $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
        $User->created_at = Carbon::now();
        $User->privacy_policy_date = Carbon::now();
        $User->license_date = Carbon::now();
        $User->parent_id = $parent_id;
        $User->child_account_type = 'withExistingEmail';
        $User->save();

        // $html = 'Hi ' . $User->username . ',<br><br> Your OTP is: ' . $User->vcode . '';
        if (isset($_POST['language']) && trim($_POST['language']) == 'de') {
            $html = "Hallo,<br><br>
            willkommen bei " . config("app.name", "") . ".<br><br>
            Hiermit erhältst Du Dein Initial-Passwort, um Deine Registrierung in der " . config("app.name", "") . " App abzuschließen.
            <br><br>
            Dein Initial-Passwort lautet: " . $User->vcode . "<br><br>
            Bitte gib dieses Intial-Passwort in der " . config("app.name", "") . " App ein.<br><br>
            Hast Du Dich nicht über die " . config("app.name", "") . " App registriert? Dann brauchst Du nichts weiter zu tun, der Account wird automatisch wieder gelöscht.<br><br>
            Viel Erfolg und einen guten Start mit " . config("app.name", "") . ".<br><br>
            Dein " . config("app.name", "") . " Kundenservice";
            $subject = 'Willkommen bei ' . config("app.name", "");
        } else {

            $html = "Hello,<br><br>
            Welcome to " . config("app.name", "") . ".<br><br>
            This is your initial password to complete your registration in the " . config("app.name", "") . " app.<br><br>
            Your initial password is: " . $User->vcode . ".<br><br>
            Please enter this initial password in the " . config("app.name", "") . " app.<br><br>
            Have you not registered via the " . config("app.name", "") . " app? Then you don't need to do anything else, the account will be deleted automatically.
            <br><br>
            Good luck and a good start with " . config("app.name", "") . ".<br><br>
            Your " . config("app.name", "") . " Customer Service";
            $subject =  'Welcome to ' . config("app.name", "");
        }

        if($token->child_account_type = 'withExistingEmail'){
            $parentFind = User::where('id',$parent_id)->first();
            $email = $parentFind->email;
        }else{
            $email = $token->email;
        }

        $emailInfo["email"] = $email;
        $emailInfo["otp"] = $User->vcode;
        $emailInfo["username"] = $User->username;
        $emailInfo["subject"] = $subject;
        if (strtolower(config("app.name", "")) != 'addmee') {

            Mail::send('mail.sent_otp', $emailInfo, function ($message) use ($emailInfo) {
                $message->to($emailInfo["email"])->subject($emailInfo["subject"]);
            });
        } else {
            $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);

            $body = [
                'Messages' => [
                    [
                        'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                        'To' => [['Email' => $email, 'Name' => $User->username]],
                        'Subject' => $subject,
                        'TextPart' => $subject,
                        'HTMLPart' => $html,
                        'CustomID' => config("app.name", "")
                    ]
                ]
            ];

            $response = $mj->post(Resources::$Email, ['body' => $body]);
        }

        unset($User->vcode, $User->vcode_expiry, $User->access_token, $User->password);
        $data['success'] = TRUE;
        $data['message'] = 'Registration successful.';
        $data['data'] = array('user' => $User);
        return response()->json($data, 201);
    }

    public function createChildWithExistingEmail(Request $request)
    {

        $token = $request->user();
        $validations['username'] = 'required|string|unique:users';

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

        // for find Parent
        $parent_id = $token->id;

        $email = $token->email;

        $parent  = User::where('id',$token->parent_id)->first();
        if ($parent) {
            $parent_id = $parent->id;
            $email = $parent->email;
        }

        // for Business Potal Admin and Users
        $check_business_user = BusinessUser::where('user_id', $token->id)->where('parent_id', '!=', 0)->first();
        if($token->user_group_id == 3 || $check_business_user){
            $data['success'] = FALSE;
            $data['message'] = 'Business users have no permission to create child accounts.';
            $data['data'] = (object)[];
            return response($data, 400);

        }

        // for simple customer , Pet , Sos , Personal and Business
        if($token->user_group_id == 2 || $token->user_group_id == 7 || $token->user_group_id == 8 || $token->user_group_id == 9 || $token->user_group_id == 10){
            $new_account = 1;
            $account_limit = $this->childs_account_limits($parent_id, $new_account);
            $account_limits = 5;
            if ($account_limit != 1)
             {
                $data['success'] = FALSE;
                $data['message'] = ($account_limits) ? 'Company account limit reached. Please get in contact with the AddMee Customer Care.' : 'Company account limit reached. Please get in contact with the AddMee Customer Care.';
                $data['data'] = (object)[];
                return response($data, 400);
            }
        }

        $User = new TempUsers();
        $User->username = $request->username;
        $latestUserID = User::orderBy('id', 'DESC')->first()->id;
        $User->email = (string) $latestUserID;
       // $User->email = $request->has('email') && $request->email != '' ? $request->email : $request->username;
        $User->password = $token->password;
        $User->fcm_token = isset($request->fcm_token) ? $request->fcm_token : '';
        if (isset($request->gender) && $request->gender != '') {
            $User->gender = isset($request->gender) ? (int)$request->gender : 3;
        }

        $User->allow_data_usage = isset($request->allow_data_usage) ? $request->allow_data_usage : 0;
        $User->device_type = isset($request->device_type) ? $request->device_type : 0;
        $User->device_id = isset($request->device_id) ? $request->device_id : 0;
        $User->vcode = rand(111111, 999999);;
        $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
        $User->created_at = Carbon::now();
        $User->privacy_policy_date = Carbon::now();
        $User->license_date = Carbon::now();
        $User->parent_id = $parent_id;
        $User->child_account_type = 'withExistingEmail';
        $User->save();

        // $html = 'Hi ' . $User->username . ',<br><br> Your OTP is: ' . $User->vcode . '';
        if (isset($_POST['language']) && trim($_POST['language']) == 'de') {
            $html = "Hallo,<br><br>
            willkommen bei " . config("app.name", "") . ".<br><br>
            Hiermit erhältst Du Dein Initial-Passwort, um Deine Registrierung in der " . config("app.name", "") . " App abzuschließen.
            <br><br>
            Dein Initial-Passwort lautet: " . $User->vcode . "<br><br>
            Bitte gib dieses Intial-Passwort in der " . config("app.name", "") . " App ein.<br><br>
            Hast Du Dich nicht über die " . config("app.name", "") . " App registriert? Dann brauchst Du nichts weiter zu tun, der Account wird automatisch wieder gelöscht.<br><br>
            Viel Erfolg und einen guten Start mit " . config("app.name", "") . ".<br><br>
            Dein " . config("app.name", "") . " Kundenservice";
            $subject = 'Willkommen bei ' . config("app.name", "");
        } else {

            $html = "Hello,<br><br>
            Welcome to " . config("app.name", "") . ".<br><br>
            This is your initial password to complete your registration in the " . config("app.name", "") . " app.<br><br>
            Your initial password is: " . $User->vcode . ".<br><br>
            Please enter this initial password in the " . config("app.name", "") . " app.<br><br>
            Have you not registered via the " . config("app.name", "") . " app? Then you don't need to do anything else, the account will be deleted automatically.
            <br><br>
            Good luck and a good start with " . config("app.name", "") . ".<br><br>
            Your " . config("app.name", "") . " Customer Service";
            $subject =  'Welcome to ' . config("app.name", "");
        }

        $emailInfo["email"] = $email;
        $emailInfo["otp"] = $User->vcode;
        $emailInfo["username"] = $User->username;
        $emailInfo["subject"] = $subject;
        if (strtolower(config("app.name", "")) != 'addmee') {

            Mail::send('mail.sent_otp', $emailInfo, function ($message) use ($emailInfo) {
                $message->to($emailInfo["email"])->subject($emailInfo["subject"]);
            });
        } else {
            $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);

            $body = [
                'Messages' => [
                    [
                        'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                        'To' => [['Email' => $email, 'Name' => $User->username]],
                        'Subject' => $subject,
                        'TextPart' => $subject,
                        'HTMLPart' => $html,
                        'CustomID' => config("app.name", "")
                    ]
                ]
            ];

            $response = $mj->post(Resources::$Email, ['body' => $body]);
        }

        unset($User->vcode, $User->vcode_expiry, $User->access_token, $User->password);
        $data['success'] = TRUE;
        $data['message'] = 'Registration successful.';
        $data['data'] = array('user' => $User);
        return response()->json($data, 201);
    }

    public function makeChildWithExistingEmail(Request $request)
    {

        $token = $request->user();
        $validations['username'] = 'required|string|unique:users';

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

        // for find Parent
        $parent_id = $token->id;

        $parent  = User::where('id',$token->parent_id)->first();
        if ($parent) {
            $parent_id = $parent->id;
        }

        // for Business Potal Admin and Users
        $check_business_user = BusinessUser::where('user_id', $token->id)->where('parent_id', '!=', 0)->first();
        if($token->user_group_id == 3 || $check_business_user){
            $data['success'] = FALSE;
            $data['message'] = 'Business users have no permission to create child accounts.';
            $data['data'] = (object)[];
            return response($data, 400);

        }

        // for simple customer , Pet , Sos , Personal and Business
        if($token->user_group_id == 2 || $token->user_group_id == 7 || $token->user_group_id == 8 || $token->user_group_id == 9 || $token->user_group_id == 10){
            $new_account = 1;
            $account_limit = $this->childs_account_limits($parent_id, $new_account);
            $account_limits = 5;
            if ($account_limit != 1)
             {
                $data['success'] = FALSE;
                $data['message'] = ($account_limits) ? 'Company account limit reached. Please get in contact with the AddMee Customer Care.' : 'Company account limit reached. Please get in contact with the AddMee Customer Care.';
                $data['data'] = (object)[];
                return response($data, 400);
            }
        }

        $User = new User();
        $User->username = $request->username;
        $latestUserID = User::orderBy('id', 'DESC')->first()->id;
        $User->email = (string) $latestUserID;
       // $User->email = $request->has('email') && $request->email != '' ? $request->email : $request->username;
        $User->password = $token->password;
        $User->fcm_token = isset($request->fcm_token) ? $request->fcm_token : '';
        if (isset($request->gender) && $request->gender != '') {
            $User->gender = isset($request->gender) ? (int)$request->gender : 3;
        }

        $User->allow_data_usage = isset($request->allow_data_usage) ? $request->allow_data_usage : 0;
        $User->device_type = isset($request->device_type) ? $request->device_type : 0;
        $User->device_id = isset($request->device_id) ? $request->device_id : 0;
        $User->vcode = rand(111111, 999999);;
        $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
        $User->created_at = Carbon::now();
        $User->privacy_policy_date = Carbon::now();
        $User->license_date = Carbon::now();
        $User->first_login = 0;
        $User->parent_id = $parent_id;
        $User->child_account_type = 'withExistingEmail';
        $User->provider = $token->provider;
        $User->provider_id =$token->provider_id;
        $User->save();

        if ($User->allow_data_usage == 1) {
            $info['type'] = 'user';
            $info['type_id'] = $User->id;
            $info['details'] = 'allow-data-usage';
            $info['device_id'] = isset($request->device_id) ? $request->device_id : 0;
            $info['ip_address'] = getUserIP();
            $info['created_by'] = $User->id;
            add_activity($info);
        }

        $this->add_contact_card_profile($User);
        $access_token = $User->createToken('authToken')->plainTextToken;

        unset($User->vcode, $User->vcode_expiry, $User->access_token, $User->password);
        $data['success'] = TRUE;
        $data['message'] = 'Registration successful.';
        $data['data'] = array('user' => $User, 'access_token' => $access_token);
        return response()->json($data, 201);
    }

    public function add_contact_card_profile($User) //used inside controllers
    {
        $Profile = CustomerProfile::where('user_id', $User->id)->where('profile_code', 'contact-card')->where('is_business', 0);

        if ($Profile->count() == 0) {
            $Obj = new CustomerProfile;
            $Obj->profile_link = $User->id;
            $Obj->profile_code = 'contact-card';
            $Obj->is_business = 0;
            $Obj->user_id = $User->id;
            $Obj->created_by = $User->id;
            $Obj->created_at = Carbon::now();
            $Obj->save();
        }

        $Profile = CustomerProfile::where('user_id', $User->id)->where('profile_code', 'contact-card')->where('is_business', 1);
        if ($Profile->count() == 0) {
            $bObj = new CustomerProfile;
            $bObj->profile_link = $User->id;
            $bObj->profile_code = 'contact-card';
            $bObj->is_business = 1;
            $bObj->user_id = $User->id;
            $bObj->created_by = $User->id;
            $bObj->created_at = Carbon::now();
            $bObj->save();
        }
    }



}
