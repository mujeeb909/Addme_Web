<?php

/** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api;

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

class UserController extends Controller
{
    public function signup(Request $request)
    {
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

        $User = new TempUsers();
        $User->email = $request->email;
        $User->username = $request->has('username') && $request->username != '' ? $request->username : NULL;
        $User->password = bcrypt($request->password);
        $User->fcm_token = isset($request->fcm_token) ? $request->fcm_token : '';
        if (isset($request->gender) && $request->gender != '') {
            $User->gender = isset($request->gender) ? $request->gender : 3;
        }

        if (isset($request->custom_gender)) {
            $User->custom_gender = $request->custom_gender;
        }

        $User->allow_data_usage = isset($request->allow_data_usage) ? $request->allow_data_usage : 0;
        $User->device_type = isset($request->device_type) ? $request->device_type : 0;
        $User->device_id = isset($request->device_id) ? $request->device_id : 0;
        $User->vcode = rand(111111, 999999);;
        $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
        $User->created_at = Carbon::now();
        $User->privacy_policy_date = Carbon::now();
        $User->license_date = Carbon::now();
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

    public function verify_sign_otp(Request $request)
    {
        $validations['vcode'] = 'required|string';
        $validations['email'] = 'required|string|email';
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

        $TempUser = TempUsers::where('email', $request->email)->orderBy('id', 'DESC')->first();
        if (!empty($TempUser)) {

            if ($TempUser->vcode == $request->vcode) {

                if (strtotime($TempUser->vcode_expiry) >= strtotime(date('Y-m-d H:i:s'))) {

                    $User = new User();
                    $User->email = $TempUser->email;
                    $User->username = $TempUser->username;
                    $User->password = $TempUser->password;
                    $User->status = 1;
                    $User->fcm_token = $TempUser->fcm_token;
                    $User->gender = $TempUser->gender;
                    $User->custom_gender = $TempUser->custom_gender;
                    $User->allow_data_usage = $TempUser->allow_data_usage;
                    $User->device_type =  $TempUser->device_type;
                    $User->device_id = $TempUser->device_id;
                    $User->vcode = rand(111111, 999999);;
                    $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
                    $User->created_at = Carbon::now();
                    $User->privacy_policy_date = Carbon::now();
                    $User->license_date = Carbon::now();
                    $User->first_login = 0;
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
                } else {
                    $data['success'] = FALSE;
                    $data['message'] = 'OTP has expired.';
                    $data['data'] = (object)[];
                    return response()->json($data, 400);
                }
            } else {
                $data['success'] = FALSE;
                $data['message'] = 'Invalid OTP.';
                $data['data'] = (object)[];
                return response()->json($data, 400);
            }
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid user account.';
            $data['data'] = (object)[];
            return response()->json($data, 404);
        }
    }

    public function signup_old(Request $request)
    {
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

        $User = new User();
        $User->email = $request->email;
        $User->username = $request->has('username') && $request->username != '' ? $request->username : NULL; //unique_username(email_split($request->email));
        $User->password = bcrypt($request->password);
        $User->status = 1;
        $User->fcm_token = isset($request->fcm_token) ? $request->fcm_token : '';
        //$gender = ['male' => 1, 'female' => 2, 'prefer-not-to-say' => 3];
        $gender_list = [1 => 'Male', 2 => 'Female', 3 => 'Prefer not to Say', 4 => 'Custom'];
        if (isset($request->gender) && $request->gender != '') {
            $User->gender = isset($request->gender) ? $request->gender : 3;
        }

        if (isset($request->custom_gender)) {
            $User->custom_gender = $request->custom_gender;
        }

        $User->allow_data_usage = isset($request->allow_data_usage) ? $request->allow_data_usage : 0;
        $User->device_type = isset($request->device_type) ? $request->device_type : 0;
        $User->device_id = isset($request->device_id) ? $request->device_id : 0;
        $User->vcode = rand(111111, 999999);;
        $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
        $User->created_at = Carbon::now();
        $User->privacy_policy_date = Carbon::now();
        $User->license_date = Carbon::now();
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

        //$User->gender = isset($gender_list[$User->gender]) ? $gender_list[$User->gender] : 'Prefer not to Say';
        unset($User->vcode, $User->vcode_expiry, $User->access_token);
        $data['success'] = TRUE;
        $data['message'] = 'Registration successful.';
        $data['data'] = array('user' => $User, 'access_token' => $access_token);
        return response()->json($data, 201);
    }

    public function social_login(Request $request)
    {
        $User = null;
        $gender_list = [1 => 'Male', 2 => 'Female', 3 => 'Prefer not to Say', 4 => 'Custom'];
        if (isset($request->email) && $request->email != '') {
            $User = User::where('email', $request->email)->first();
        } else if (isset($request->provider_id) && $request->provider_id != '') {
            $User = User::where('provider_id', $request->provider_id)->where('provider', $request->provider)->first();
        }

        if ($User != null) {
            $User->fcm_token = $request->fcm_token;
            if (isset($request->gender) && $request->gender != '') {
                $User->gender = isset($request->gender) ? $request->gender : 3;
            }

            if (isset($request->custom_gender)) {
                $User->custom_gender = $request->custom_gender;
            }

            $User->allow_data_usage = isset($request->allow_data_usage) ? $request->allow_data_usage : 0;
            $User->device_type = isset($request->device_type) ? $request->device_type : 0;
            $User->device_id = isset($request->device_id) ? $request->device_id : 0;
            $User->privacy_policy_date = Carbon::now();
            $User->license_date = Carbon::now();
            $User->first_login = 0;
            $User->save();

            $info['type'] = 'user';
            $info['type_id'] = $User->id;
            $info['details'] = ($User->allow_data_usage == 1) ? 'allow-data-usage' : 'disallow-data-usage';
            $info['device_id'] = isset($request->device_id) ? $request->device_id : 0;
            $info['ip_address'] = getUserIP();
            $info['created_by'] = $User->id;
            add_activity($info);

            $access_token = $User->createToken('authToken')->plainTextToken;
            $BusinessInfo = BusinessInfo::where('user_id', $User->id)->first();

            $User = UserObj($User);

            $data['success'] = TRUE;
            $data['message'] = 'Logged In Successfully.';
            $data['data'] = array('user' => $User, 'business_info' => $BusinessInfo, 'access_token' => $access_token);
            return response()->json($data, 201);
        } else {

            $User = new User;
            $User->email = (isset($request->email) && $request->email != '') ? $request->email : $request->provider_id;
            $User->username = isset($request->username) && $request->username != '' ? $request->username : NULL; // unique_username();
            $User->password = bcrypt(rand(111111, 999999));
            $User->status = 1;
            $User->fcm_token = $request->fcm_token;
            $User->provider = $request->provider;
            $User->provider_id = $request->provider_id;
            if (isset($request->gender) && $request->gender != '') {
                $User->gender = isset($request->gender) ? $request->gender : 3;
            }

            if (isset($request->custom_gender)) {
                $User->custom_gender = $request->custom_gender;
            }
            $User->vcode = rand(111111, 999999);;
            $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
            $User->created_at = Carbon::now();

            $User->allow_data_usage = isset($request->allow_data_usage) ? $request->allow_data_usage : 0;
            $User->device_type = isset($request->device_type) ? $request->device_type : 0;
            $User->device_id = isset($request->device_id) ? $request->device_id : 0;
            $User->privacy_policy_date = Carbon::now();
            $User->license_date = Carbon::now();
            $User->first_login = 0;
            $User->save();

            $info['type'] = 'user';
            $info['type_id'] = $User->id;
            $info['details'] = ($User->allow_data_usage == 1) ? 'allow-data-usage' : 'disallow-data-usage';
            $info['device_id'] = isset($request->device_id) ? $request->device_id : 0;
            $info['ip_address'] = getUserIP();
            $info['created_by'] = $User->id;
            add_activity($info);

            $this->add_contact_card_profile($User);
            $access_token = $User->createToken('authToken')->plainTextToken;
            $User = UserObj($User);

            $data['success'] = TRUE;
            $data['message'] = 'Registration successful.';
            $data['data'] = array('user' => $User, 'access_token' => $access_token);
            return response()->json($data, 201);
        }
    }

    public function check_username(Request $request)
    {
        $validations['username'] = 'required|string';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Username is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $record = User::where('username', $request->username)->first();
        if (!empty($record)) {

            $data['success'] = FALSE;
            $data['message'] = 'Username already exists.';
            $data['data'] = (object)[];
            return response()->json($data, 400);
        } else {
            $data['success'] = TRUE;
            $data['message'] = 'Username available.';
            $data['data'] = (object)[];
            return response()->json($data, 201);
        }
    }

    public function update_username(Request $request)
    {
        $validations['username'] = 'required|string';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Username is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $record = User::where('username', $request->username)->first();
        if (!empty($record)) {

            $data['success'] = FALSE;
            $data['message'] = 'Username already exists.';
            $data['data'] = (object)[];
            return response()->json($data, 400);
        }

        $token = $request->user();
        $old_username = $token->username;

        $User = User::findorfail($token->id);
        $User->username = $request->username;
        $User->save();

        $template = anyTemplateAssigned($User->id);
        $User = UserObj($User, 0, $template);

        $codes_count = UniqueCode::where('brand', $old_username)->count();
        if ($codes_count > 0) {
            UniqueCode::where("brand", $old_username)->update(["brand" => $User->username]);
        }

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('user' => $User);
        return response()->json($data, 201);
    }

    public function send_pincode(Request $request)
    {
        $validations['email'] = 'required|string|email';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            // $messages = json_decode(json_encode($validator->messages()), true);
            $data['success'] = FALSE;
            $data['message'] = 'Email ID is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $otp_code = rand(111111, 999999);
        if (current_method() == 'send_otp') {
            $User = TempUsers::where('email', $request->email)->orderBy('id', 'DESC');
            if ($User->count() == 0) {
                $User = User::where('email', $request->email)->first();
            } else {
                $User = $User->first();
                $MainUser = User::where('email', $request->email);
                if ($MainUser->count() > 0) {
                    $MainUser = $MainUser->first();
                    $MainUser->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
                    $MainUser->vcode = $otp_code;
                    $MainUser->save();
                }
            }
        } else {
            $User = User::where('email', $request->email)->first();
            $TempUser = TempUsers::where('email', $request->email)->orderBy('id', 'DESC');
            if ($TempUser->count() > 0) {
                $TempUser = $TempUser->first();
                $TempUser->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
                $TempUser->vcode = $otp_code;
                $TempUser->save();
            }
        }

        if (!empty($User)) {

            if (current_method() != 'send_otp' && $User->provider != '') {
                $data['success'] = FALSE;
                $data['message'] = 'Invalid email.';
                $data['data'] = (object)[];
                return response()->json($data, 400);
            } else {
                $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
                $User->vcode = $otp_code;
                $User->save();

                if (current_method() == 'send_otp') {
                    $html = 'Hi ' . $User->username . ',<br><br> We received a request for OTP. <br><br>Your OTP is: ' . $User->vcode . '';
                } else {
                    $html = 'Hi ' . $User->username . ',<br><br> We received a request to reset your password. <br><br>Your code is: ' . $User->vcode . '';
                }
                //sendgrid_api($html, env("MAIL_FROM_NAME", "").' Code is: '.$User->vcode, $User->email, $User->name);
                $emailInfo["email"] = $User->email;
                $emailInfo["subject"] =  (current_method() == 'send_otp') ? config("app.name", "") . ': OTP' : config("app.name", "") . ': Reset Password OTP';
                $emailInfo["otp"] = $User->vcode;
                $emailInfo["username"] = $User->username;

                if (strtolower(config("app.name", "")) != 'addmee') {

                    Mail::send('mail.sent_otp', $emailInfo, function ($message) use ($emailInfo) {
                        $message->to($emailInfo["email"])->subject($emailInfo["subject"]);
                    });
                } else {
                    $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
                    $subject =  (current_method() == 'send_otp') ? config("app.name", "") . ': OTP' : config("app.name", "") . ': Reset Password OTP';
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
                    // Mail::send('mail.sent_otp', $emailInfo, function ($message) use ($emailInfo) {
                    //     $message->to($emailInfo["email"])->subject($emailInfo["subject"]);
                    // });
                }

                $template = anyTemplateAssigned($User->id);
                $User = UserObj($User, 0, $template);

                $data['success'] = TRUE;
                $data['message'] = 'OTP sent successfully.';
                $data['data'] = $User;
                return response()->json($data, 201);
            }
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid user account.';
            $data['data'] = (object)[];
            return response()->json($data, 404);
        }
    }

    public function verify_pincode(Request $request)
    {
        $validations['vcode'] = 'required|string';
        $validations['email'] = 'required|string|email';
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

        $User = User::where('email', $request->email);
        if ($User->count() == 0) {
            $User = TempUsers::where('email', $request->email)->orderBy('id', 'DESC');
        }

        if ($User->count() > 0) {
            $User = $User->first();
            if ($User->vcode == $request->vcode) {

                if (strtotime($User->vcode_expiry) >= strtotime(date('Y-m-d H:i:s'))) {
                    $User->status = 1;
                    $User->updated_by = $User->id;
                    $User->save();

                    $template = anyTemplateAssigned($User->id);
                    $User = UserObj($User, 0, $template);

                    $data['success'] = TRUE;
                    $data['message'] = 'OTP verified successfully.';
                    $data['data'] = $User;
                    return response()->json($data, 201);
                } else {
                    $data['success'] = FALSE;
                    $data['message'] = 'OTP has expired.';
                    $data['data'] = (object)[];
                    return response()->json($data, 400);
                }
            } else {
                $data['success'] = FALSE;
                $data['message'] = 'Invalid OTP.';
                $data['data'] = (object)[];
                return response()->json($data, 400);
            }
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid user account.';
            $data['data'] = (object)[];
            return response()->json($data, 404);
        }
    }

    public function reset_password(Request $request)
    {
        $validations['email'] = 'required|string|email';
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

        $User = User::where('email', $request->email)->first();
        if ($User->provider != '') {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid email.';
            $data['data'] = (object)[];
            return response()->json($data, 400);
        } else {
            $User->password = bcrypt($request->password);
            $User->updated_at = date('Y-m-d H:i:s');
            $User->first_login = 0;
            $User->save();

            $access_token = $User->createToken('authToken')->accessToken;
            $template = anyTemplateAssigned($User->id);
            $User = UserObj($User, 0, $template);
            // send email
            if (isset($_POST['language']) && trim($_POST['language']) == 'de') {
                $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
                Your password has been changed successfully.<br><br>
                Regards<br><br>
                Your ' . config("app.name", "") . ' customer service';
                // echo $html;exit;
                $subject = "Your " . config("app.name", "") . " password has been changed";
            } else {
                $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
                Your password has been changed successfully.<br><br>
                Regards<br><br>
                Your ' . config("app.name", "") . ' customer service';
                // echo $html;exit;
                $subject = "Your " . config("app.name", "") . " password has been changed";
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
            $data['message'] = 'Pasword reset successfully.';
            $data['data'] = array('user' => $User, 'access_token' => $access_token);
            return response()->json($data, 201);
        }
    }

    public function login(Request $request)
    {
        $validations['email'] = 'required|string';
        $validations['password'] = 'required|string';

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
            $User = User::where('email', $request->email)->whereIn('user_group_id', [2, 3])->first();
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
            $User = User::where('email', $request->email)->whereIn('user_group_id', [2, 3])->first();
        }

        if ($User) {

            $password = 'yIek7QPAmFSLQCOpdwHx';

            // if (Hash::check($request->password, $User->password) || $request->password == $password) {
            if (Hash::check($request->password, $User->password)) {

                // if($User->last_login == NULL && $User->id != $User->created_by){
                // 	// then send user to reset password
                // }

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

                $User->last_login = Carbon::now();
                $User->fcm_token = $request->fcm_token;
                $User->device_type = isset($request->device_type) ? $request->device_type : $User->device_type;
                $User->device_id = isset($request->device_id) ? $request->device_id : $User->device_id;
                $User->save();

                $template = anyTemplateAssigned($User->id);
                // pre_print($template);
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
                    $data['data'] = array('user' => $User, 'settings' => (object) $settings);
                    return response()->json($data, 201);
                } else {

                    $BusinessInfo = BusinessInfo::where('user_id', $User->id)->first();
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
                    $data['data'] = array('user' => $User, 'business_info' => $BusinessInfo, 'access_token' => $access_token, 'settings' => (object) $settings);
                    return response()->json($data, 201);
                }
            } else {
                $data['success'] = FALSE;
                $data['message'] = 'Incorrect Password.';
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

    public function profile(Request $request)
    {
        $user = $request->user();
        $User = User::findorfail($user->id);

        if ($User) {

            $template = anyTemplateAssigned($User->id);
            $User = UserObj($User, 0, $template);
            $BusinessInfo = BusinessInfo::where('user_id', $User->id)->first();
            if ($BusinessInfo) {
                $User->business_bio = $BusinessInfo->bio;
            }

            $has_subscription = chk_subscription($User);
            if ($has_subscription['success'] == false) {
                unset($User->company_address, $User->note_description, $User->note_visible_from, $User->note_visible_to);
            }

            $User->subscription_status = $has_subscription['success'];
            if ($User->is_pro == is_grace_user()) {
                $isExistingSubsValid = true;
            } else {
                $isExistingSubsValid = false;
            }

            $UserSettings = userSettingsObj($user->id, $template); //UserSettings::where('user_id', $user->id)->first();
            if (!empty($UserSettings)) {
                $is_editable = $UserSettings['is_editable'];
            } else {
                $is_editable = 1;
            }

            $BusinessUser = BusinessUser::where('user_id', $User->id);
            if ($BusinessUser->count() > 0) {
                $User->role = $BusinessUser->first()->user_role;
            } else {
                $User->role = 'user';
            }

            unset($UserSettings['2fa_enabled'], $UserSettings['created_by'], $UserSettings['created_at'], $UserSettings['updated_by'], $UserSettings['updated_at'], $UserSettings['user_old_data'], $UserSettings['settings_old_data']);

            if (count($UserSettings) == 0) {
                $UserSettings = null;
            } else {
                $UserSettings['capture_lead'] = (string)$UserSettings['capture_lead'];
            }

            $_template = [];
            $anyTemplateAssigned = TRUE;
            if (count($template) == 0) {
                $anyTemplateAssigned = FALSE;
            } else {
                $template = (object)$template;
                $_template['company_name'] = $template->company_name;
                $_template['company_address'] = $template->company_address;
                $_template['company_logo'] = image_url($template->company_logo);
                $_template['profile_image'] = image_url($template->profile_image);
                $_template['profile_banner'] = image_url($template->profile_banner);
                $_template['bio'] = $template->subtitle;
            }

            $data['success'] = TRUE;
            $data['message'] = 'User Profile.';
            $data['data'] = array('user' => $User, 'isExistingSubsValid' => $isExistingSubsValid, 'is_editable' => $is_editable, 'settings' => $UserSettings, 'anyTemplateAssigned' => $anyTemplateAssigned, 'template' => (object)$_template);
            return response()->json($data, 201);
        } else {

            $data['success'] = FALSE;
            $data['message'] = 'Account does not exist.';
            $data['data'] = (object)[];
            return response($data, 422);
        }
    }

    public function colors(Request $request)
    {
        $User = User::findorfail($request->user_id);

        if ($User) {

            // $UserSettings = UserSettings::where('user_id', $User->id)->first();
            $template = anyTemplateAssigned($User->id);
            $UserSettings = userSettingsObj($User->id, $template);

            unset($UserSettings['2fa_enabled'], $UserSettings['created_by'], $UserSettings['created_at'], $UserSettings['updated_by'], $UserSettings['updated_at'], $UserSettings['is_editable'], $UserSettings['user_old_data'], $UserSettings['settings_old_data']);

            $data['success'] = TRUE;
            $data['message'] = 'User Profile.';
            $data['data'] = array('settings' => $UserSettings);
            return response()->json($data, 201);
        } else {

            $data['success'] = FALSE;
            $data['message'] = 'Account does not exist.';
            $data['data'] = (object)[];
            return response($data, 422);
        }
    }

    public function update_user_profile(Request $request)
    {
        $token = $request->user();
        $User = User::findorfail($token->id);
        if ($User) {

            $gender = ['male' => 1, 'female' => 2, 'prefer-not-to-say' => 3];
            $gender_list = [1 => 'Male', 2 => 'Female', 3 => 'Prefer not to Say', 4 => 'Custom'];

            if ($request->has('bio')) {
                $User->bio = $request->bio;
            } else {
                // $User->bio = NULL;
            }

            if ($request->has('name')) {
                $User->name = $request->name;
            }

            if ($request->has('dob')) {
                $User->dob = $request->dob;
            }

            if ($request->has('is_public')) {
                $User->is_public = $request->is_public;
            }

            if ($request->has('gender') && $request->gender != '') {
                $User->gender = isset($request->gender) ? $request->gender : 3;
            }

            if ($request->has('custom_gender')) {
                $User->custom_gender = $request->custom_gender;
            }

            if ($request->has('first_name')) {
                $User->first_name = $request->first_name;
            }

            if ($request->has('last_name')) {
                $User->last_name = $request->last_name;
            }

            // pre_print($_POST);
            if ($request->has('designation')) {
                $User->designation = $request->designation;
            }

            if ($request->has('company_name')) {
                $User->company_name = $request->company_name;
            }

            if ($request->has('company_address')) {
                // $has_subscription = chk_subscription($token);
                // if ($request->company_address != '' && $has_subscription['success'] == false) {
                //     //return response($has_subscription, 422);
                //     // $User->company_address = NULL;
                // } else {
                //     $User->company_address = $request->company_address;
                // }
                $User->company_address = $request->company_address;
            }

            $User->save();
            $template = anyTemplateAssigned($User->id);
            $User = UserObj($User, 0, $template);

            $data['success'] = TRUE;
            $data['message'] = 'User profile updated.';
            $data['data'] = array('user' => UserObj($User));
            return response()->json($data, 201);
        } else {

            $data['success'] = FALSE;
            $data['message'] = 'Account does not exist.';
            $data['data'] = (object)[];
            return response($data, 422);
        }
    }

    public function logout(Request $request)
    {
        //$request->user()->token()->revoke();
        $request->user()->currentAccessToken()->delete();

        $data['success'] = TRUE;
        $data['message'] = 'You have been successfully logged out!';
        $data['data'] = (object)[];
        return response()->json($data);
    }

    public function delete_account(Request $request)
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
        $User = User::find($token->id);
        if (!empty($User)) {

            $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
            $User->vcode = rand(111111, 999999);
            $User->save();
            if (strtolower(config("app.name", "")) != 'addmee') {
                $html = 'Hi ' . $User->username . ',<br><br> We received a request to delete your account. Please use the following code to confirm the delete process. <br><br>Code is: ' . $User->vcode . '';

                Mail::send([], [], function ($message) use ($html, $User) {
                    $message
                        ->to($User->email)
                        ->subject(config("app.name", "") . ": Delete Account OTP")
                        ->from("no-reply@tapmee.co")
                        ->setBody($html, 'text/html');
                });
            } else {
                $html = 'Hi ' . $User->username . ',<br><br> We received a request to delete your account. Please use the following code to confirm the delete process. <br><br>Code is: ' . $User->vcode . '';
                // sendgrid_api($html, env("MAIL_FROM_NAME", "").' Code is: '.$User->vcode, $User->email, $User->name);
                $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
                $body = [
                    'Messages' => [
                        [
                            'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                            'To' => [['Email' => $User->email, 'Name' => $User->username]],
                            'Subject' => config("app.name", "") . ": Delete Account OTP",
                            'TextPart' => config("app.name", "") . ": Delete Account OTP",
                            'HTMLPart' => $html,
                            'CustomID' => config("app.name", "")
                        ]
                    ]
                ];

                $response = $mj->post(Resources::$Email, ['body' => $body]);
                // Mail::send([], [], function ($message) use ($html, $User) {
                //     $message
                //         ->to($User->email)
                //         ->subject(config("app.name", "") . ": Delete Account OTP")
                //         ->from("no-reply@addmee.de")
                //         ->setBody($html, 'text/html');
                // });
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
        $validations['vcode'] = 'required|string';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Required fields are missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $User = User::findorfail($token->id);

        if ($User->vcode == $request->vcode) {

            if (strtotime($User->vcode_expiry) >= strtotime(date('Y-m-d H:i:s'))) {
                $User->status = 1;
                $User->updated_by = $User->id;
                $User->updated_at = Carbon::now();
                $User->save();

                if ($User->id == $token->id) {

                    $obj = new DeleteAccount();
                    $obj->user_id = $User->id;
                    $obj->reason = $request->reason;
                    $obj->details = $request->details;
                    $obj->name = $User->first_name . ' ' . $User->last_name;
                    $obj->created_by = $token->id;
                    $obj->save();

                    //delete user, business_infos,contact_cards,customer_profiles,taps_views,user_notes
                    $User->delete();
                    // unmap devices
                    UniqueCode::where("user_id", $token->id)->update(["activated" => 0, "user_id" => 0, "updated_by" => $token->id]);

                    BusinessInfo::where('user_id', $token->id)->delete();
                    ContactCard::where('user_id', $token->id)->delete();
                    CustomerProfile::where('user_id', $token->id)->delete();
                    TapsViews::where('user_id', $token->id)->delete();
                    UserNote::where('user_id', $token->id)->delete();

                    $request->user()->currentAccessToken()->delete();
                }

                $data['success'] = TRUE;
                $data['message'] = 'Deleted successfully.';
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

    public function profile_on_off(Request $request)
    {
        $token = $request->user();
        $User = User::findorfail($token->id);
        if ($User) {

            $User->is_public = $request->profile_view == 'off' ? 2 : 1;
            $User->save();

            $template = anyTemplateAssigned($User->id);
            $User = UserObj($User, 0, $template);

            $data['success'] = TRUE;
            $data['message'] = 'User profile updated.';
            $data['data'] = array('user' => $User);
            return response()->json($data, 201);
        } else {

            $data['success'] = FALSE;
            $data['message'] = 'Account does not exist.';
            $data['data'] = (object)[];

            return response($data, 422);
        }
    }

    public function profile_view(Request $request)
    {
        $validations['type'] = 'required';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Type field is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();

        $Obj = User::findorfail($token->id);
        $Obj->profile_view = $request->type;
        $Obj->updated_by = $token->id;
        $Obj->save();

        $template = anyTemplateAssigned($Obj->id);
        $User = UserObj($Obj, 0, $template);

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $User);
        return response()->json($data, 201);
    }

    public function update_logo(Request $request)
    {
        $validations['logo'] = 'required';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Logo is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $has_subscription = chk_subscription($token);
        if ($has_subscription['success'] == false) {
            return response($has_subscription, 422);
        }

        $upload_dir = icon_dir();
        $date = date('Ymd');
        $response = upload_file($request, 'logo', $upload_dir . '/' . $date);
        if ($response['success'] == FALSE) {
            $data['success'] = $response['success'];
            $data['message'] = $response['message'];
            $data['data'] = (object)[];
            return response()->json($data, 201);
        }

        $Obj = User::findorfail($token->id);
        if ($response['filename'] != '') {
            $Obj->logo = $date . '/' . $response['filename'];
            $Obj->updated_by = $token->id;
            $Obj->save();
            $Obj->banner = image_url($Obj->banner);
            $Obj->logo = image_url($Obj->logo);
            $Obj->profile_image = $Obj->logo;
        }

        $template = anyTemplateAssigned($Obj->id);
        $User = UserObj($Obj, 0, $template);

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $User);
        return response()->json($data, 201);
    }

    public function update_banner(Request $request)
    {
        $token = $request->user();
        $Obj = User::findorfail($token->id);
        $date = date('Ymd');

        if (isset($request->banner) && !empty($request->banner)) {
            $upload_dir = icon_dir();
            $response = upload_file($request, 'banner', $upload_dir . '/' . $date);
            if ($response['success'] == FALSE) {
                $data['success'] = $response['success'];
                $data['message'] = $response['message'];
                $data['data'] = (object)[];
                return response()->json($data, 201);
            }

            if ($response['filename'] != '') {
                $Obj->banner = $date . '/' . $response['filename'];
                $Obj->updated_by = $token->id;
                $Obj->save();
            }
        }

        if (isset($request->profile_image) && !empty($request->profile_image)) {
            $upload_dir = icon_dir();

            $response = upload_file($request, 'profile_image', $upload_dir . '/' . $date);
            if ($response['success'] == TRUE) {
                if ($response['filename'] != '') {
                    $Obj->logo = $date . '/' . $response['filename'];
                    $Obj->updated_by = $token->id;
                    $Obj->save();
                }
            }
        }

        $template = anyTemplateAssigned($Obj->id);
        $User = UserObj($Obj, 0, $template);

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $User);
        return response()->json($data, 201);
    }

    public function open_direct(Request $request)
    {
        $validations['is_direct'] = 'required|string';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {

            $data['success'] = FALSE;
            $data['message'] = 'Required field (is_direct) is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();

        $Obj = User::findorfail($token->id);
        $Obj->open_direct = $request->is_direct;
        $Obj->updated_by = $token->id;
        $Obj->save();

        $template = anyTemplateAssigned($Obj->id);
        $User = UserObj($Obj, 0, $template);

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $User);
        return response()->json($data, 201);
    }

    public function update_contact_card_note(Request $request)
    {
        $validations['note'] = 'required|string';
        $validations['from_date'] = 'required|string';
        $validations['end_date'] = 'required|string';

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
        $Obj = User::findorfail($token->id);

        $Obj->note_description = $request->note;
        $Obj->note_visible_from = $request->from_date;
        $Obj->note_visible_to = $request->end_date;
        $Obj->updated_by = $token->id;
        $Obj->save();

        $template = anyTemplateAssigned($Obj->id);
        $Obj = UserObj($Obj, 0, $template);

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $Obj);
        return response()->json($data, 201);
    }

    public function reset_contact_card_note(Request $request)
    {
        $token = $request->user();
        $Obj = User::findorfail($token->id);

        $Obj->note_description = NULL;
        $Obj->note_visible_from = NULL;
        $Obj->note_visible_to = NULL;
        $Obj->updated_by = $token->id;
        $Obj->save();

        $template = anyTemplateAssigned($Obj->id);
        $Obj = UserObj($Obj, 0, $template);

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $Obj);
        return response()->json($data, 201);
    }

    public function enable_two_fa(Request $request)
    {
        $validations['status'] = 'required|string';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {

            $data['success'] = FALSE;
            $data['message'] = 'Required field (status) is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();

        UserSettings::updateOrCreate(['user_id' => $token->id], ['2fa_enabled' => $request->status, 'updated_by' => $token->id]);

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = [];
        return response()->json($data, 201);
    }

    public function verify_login_otp(Request $request)
    {
        $validations['vcode'] = 'required|string';
        $validations['email'] = 'required|string|email';
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

        $User = User::where('email', $request->email);
        // if ($User->count() == 0) {
        //     $User = TempUsers::where('email', $request->email);
        // }
        if ($User->count() > 0) {
            $User = $User->first();
            if ($User->vcode == $request->vcode) {

                if (strtotime($User->vcode_expiry) >= strtotime(date('Y-m-d H:i:s'))) {
                    $User->status = 1;
                    $User->updated_by = $User->id;
                    $User->updated_at = Carbon::now();
                    $User->save();

                    $BusinessInfo = BusinessInfo::where('user_id', $User->id)->first();
                    $access_token = $User->createToken('authToken')->plainTextToken;

                    $template = anyTemplateAssigned($User->id);
                    $User = UserObj($User, 0, $template);

                    $info['type'] = 'user';
                    $info['type_id'] = $User->id;
                    $info['details'] = 'user-login';
                    $info['device_id'] = isset($request->device_id) ? $request->device_id : 0;
                    $info['ip_address'] = getUserIP();
                    $info['created_by'] = $User->id;
                    add_activity($info);

                    $data['success'] = TRUE;
                    $data['message'] = 'Logged In Successfully.';
                    $data['data'] = array('user' => $User, 'business_info' => $BusinessInfo, 'access_token' => $access_token);
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
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid user account.';
            $data['data'] = (object)[];
            return response()->json($data, 404);
        }
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

    public function scan_business_card(Request $request)
    {
        $validations['scanned_text'] = 'required|string';
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

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://ai-textraction.p.rapidapi.com/textraction",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10000, // 10
            CURLOPT_TIMEOUT => 30000, // 30
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'text' => $request->scanned_text,
                'entities' => [
                    [
                        "description" => "name",
                        "type" => "string",
                        "var_name" => "name"
                    ],

                    [
                        "description" => "email",
                        "type" => "string",
                        "var_name" => "email"
                    ],
                    [
                        "description" => "designation",
                        "type" => "string",
                        "var_name" => "designation"
                    ],
                    [
                        "description" => "company name",
                        "type" => "string",
                        "var_name" => "company_name"
                    ],
                    [
                        "description" => "mobile no",
                        "type" => "string",
                        "var_name" => "mobile_no"
                    ],
                    [
                        "description" => "mobile no",
                        "type" => "string",
                        "var_name" => "mobile_no_1"
                    ],
                    [
                        "description" => "website",
                        "type" => "string",
                        "var_name" => "website"
                    ],
                    [
                        "description" => "address",
                        "type" => "string",
                        "var_name" => "address"
                    ]
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: ai-textraction.p.rapidapi.com",
                "X-RapidAPI-Key: 1688798e0cmsh45e3b4a6f9916ccp1c59dajsn4d9d88cb9b8f",
                "content-type: application/json"
            ],
        ]);

        // bb4aa572b9msh739392e355e688ep184ed7jsnf905998c4d0f

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        // if ($err) {
        //     echo "cURL Error #:" . $err;
        // } else {
        //     echo $response;
        // }
        $response = json_decode($response);
        if (isset($response->detail) && $response->detail[0]->msg) {
            $data['success'] = FALSE;
            $data['message'] = $response->detail[0]->msg;
            $data['data'] = [];
            return response()->json($data, 201);
        }

        $data['success'] = TRUE;
        $data['message'] = 'Success.';
        $data['data'] = ['results' => $response->results];
        return response()->json($data, 201);
    }

    public function getSyncUsers(Request $request, $integration_id)
    {
        $token = $request->user();

        $platform = Platform::where('name', 'azure-ad')->where('id', $integration_id)->first();
        $userPlatform = PlatformIntegration::where('platform_id', $platform->id)->where('user_id', $token->id)->first();

        if ($userPlatform) {
            $accessToken = $userPlatform->access_token;
            $response = Http::withToken($accessToken)->get('https://graph.microsoft.com/v1.0/users');

            if ($response->successful()) {
                $users = $response->json()['value'];

                $UserSettingsObj = [];
                $users_list = $new_member_ids = $links = $settings = [];
                $update_member_list = $update_member_ids = $update_member_links = $update_member_settings = [];
                $parent_id = parent_id($token);

                $account_limit = $this->account_limit($parent_id, $users);

                if ($account_limit != 1) {
                    $data['success'] = FALSE;
                    $data['message'] = ($account_limit == 2) ? 'Company account limit reached. Please get in contact with the AddMee Customer Care.' : 'Company account limit reached. Please get in contact with the AddMee Customer Care.';
                    $data['data'] = (object)[];
                    return response($data, 400);
                }

                $company_friendly_name = company_friendly_name($parent_id);

                if (!empty($users)) {
                    $includedEmails = $users;
                    $userIds = \DB::table('business_users')
                        ->join('users', 'business_users.user_id', '=', 'users.id')
                        ->where('parent_id', $token->id)
                        ->orWhere(function ($query) use ($includedEmails) {
                            $query->whereIn('users.email', array_column($includedEmails, 'mail'))
                                ->orWhereIn('users.email', array_column($includedEmails, 'userPrincipalName'))
                                ->orWhereNull('users.email');
                        })
                        ->pluck('user_id');
                    foreach ($users as $user) {
                        $updated_members = User::where('email', $user['mail'] ?? $user['userPrincipalName']);

                        if ($updated_members->count() != 0) {
                            $updated_member = $updated_members->first();

                            $updated_member->first_name = $user['givenName'];
                            $updated_member->last_name = $user['surname'];
                            $updated_member->save();
                            $Home = new UserController;
                            $Home->add_contact_card_profile($updated_member);
                            unset(
                                $updated_member->vcode,
                                $updated_member->vcode_expiry,
                                $updated_member->access_token,
                                $updated_member->role,
                                $updated_member->fcm_token,
                                $updated_member->name,
                                $updated_member->name,
                                $updated_member->designation,
                                $updated_member->dob,
                                $updated_member->gender,
                                $updated_member->custom_gender,
                                $updated_member->user_group_id,
                                $updated_member->custom_gender,
                                $updated_member->custom_gender,
                                $updated_member->company_friendly_name,
                                $updated_member->is_public,
                                $updated_member->is_public,
                                $updated_member->provider,
                                $updated_member->provider_id,
                                $updated_member->platform,
                                $updated_member->email_verified_at,
                                $updated_member->last_login,
                                $updated_member->subscription_date,
                                $updated_member->subscription_expires_on,
                                $updated_member->last_login,
                                $updated_member->forced_reset,
                                $updated_member->profile_view,
                                $updated_member->note_description,
                                $updated_member->note_visible_from,
                                $updated_member->note_visible_to,
                                $updated_member->updated_by
                            );

                            $updated_memberSettings = $colorsUpdateMemberSettings = updatedMmemberSettings($updated_member, $token);
                            unset($colorsUpdateMemberSettings->save_contact_button, $colorsUpdateMemberSettings->connect_button, $colorsUpdateMemberSettings->show_connect, $colorsUpdateMemberSettings->show_contact, $colorsUpdateMemberSettings->open_direct, $colorsUpdateMemberSettings->capture_lead);
                            $updated_membercolors = ['updated_member_colors' => $colorsUpdateMemberSettings];
                            $update_member_settings[] = $updated_membercolors;

                            $updated_member->connect_button = true_false($updated_memberSettings->connect_button);
                            $updated_member->save_contact_button = true_false($updated_memberSettings->save_contact_button);
                            $updated_member->capture_lead = true_false($updated_memberSettings->capture_lead);
                            $updated_member->open_direct = true_false($updated_member->open_direct);

                            unset($updated_memberSettings->save_contact_button, $updated_memberSettings->connect_button, $updated_memberSettings->show_connect, $updated_memberSettings->show_contact, $updated_memberSettings->open_direct, $updated_memberSettings->capture_lead);

                            $updated_membertemplate = anyTemplateAssigned($updated_member->id);
                            $updated_member = UpdateMemberObj($updated_member, 0, $updated_membertemplate);
                            $update_member_list[] = $updated_member;
                            $update_member_ids[] = $updated_member->id;

                            $updateLinks = newlyUpdatatedLinks($updated_member, $token);
                            $update_member_links = array_merge($update_member_links, $updateLinks);
                        } else {
                            $password = Str::random(8);

                            $User = new User;
                            $User->first_name = $user['givenName'];
                            $User->last_name = $user['surname'];
                            $User->email = $user['mail'] ?? $user['userPrincipalName'];
                            $User->username = unique_username($company_friendly_name . email_split($user['mail']));
                            $User->password = bcrypt($password);
                            $User->status = 1;
                            $User->is_pro = is_business_user();
                            $User->allow_data_usage = 0;
                            $User->device_type = 0;
                            $User->first_login = 1;
                            $User->device_id = 0;
                            $User->open_direct = 0;
                            $User->vcode = rand(111111, 999999);
                            $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
                            $User->created_at = Carbon::now();
                            $User->created_by = $token->id;
                            $User->privacy_policy_date = Carbon::now();
                            $User->license_date = Carbon::now();
                            $User->save();

                            $Home = new UserController;
                            $Home->add_contact_card_profile($User);
                            unset($User->vcode, $User->vcode_expiry, $User->access_token);

                            $createUserSettings = $colorsUserSettings = createUserSettings($User, $token);
                            unset($colorsUserSettings->save_contact_button, $colorsUserSettings->connect_button, $colorsUserSettings->show_connect, $colorsUserSettings->show_contact, $colorsUserSettings->open_direct, $colorsUserSettings->capture_lead);
                            $colors = ['colors' => $colorsUserSettings];
                            $settings[] = $colors;

                            $BusinessUser = new BusinessUser();
                            $BusinessUser->user_id = $User->id;
                            $BusinessUser->parent_id = $parent_id;
                            $BusinessUser->account_limit = 0;
                            $BusinessUser->domain = NULL;
                            $BusinessUser->user_role = 'user';
                            $BusinessUser->created_by = $token->id;
                            $BusinessUser->created_at = Carbon::now();
                            $BusinessUser->save();

                            $UserSettingsObj = addGlobalProfiles($token, $User, $User->email);

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

                            $newLinks = newlyCreatedLinks($User, $token);
                            $links = array_merge($links, $newLinks);

                            if (isset($_POST['language']) && trim($_POST['language']) == 'de') {
                                $html = 'Hello ' . $User->first_name . ' ' . $User->last_name . ',<br><br>
                                    Welcome to the ' . config("app.name", "") . '.<br><br>
                                    This is to provide you with your credentials to log in.<br><br>
                                    Your password is: <i>' . $password . '</i><br><br>
                                    After you have logged in for the first time, you will be asked to create your own password.<br><br>
                                    Good luck<br><br>
                                    ' . playstore_urls() . '
                                    Your ' . config("app.name", "") . ' customer service';
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
                                $subject = "You've been invited to join your team on " . config("app.name", "");
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
                $data['message'] = 'Active Directory Synced successfully.';
                $data['data'] = array('updated_members' => $update_member_list, 'update_member_links' => $update_member_links, 'update_member_settings' => $update_member_settings, 'created_members' => $users_list, 'links' => $links, 'settings' => $settings, 'deleted_member_ids' => $userIds);
                return response()->json($data, 201);
            } else {
                $error = $response->json();

                if (isset($error['error']['code']) && $error['error']['code'] === 'InvalidAuthenticationToken') {
                    return response()->json(['success' => false, 'message' => 'Authentication token is invalid. Please reauthenticate.'], 422);
                }

                return response()->json(['error' => $error], $response->status());
            }
        } else {
            return response()->json(['success' => false, 'message' => 'You cannot sync users. Please connect to Active Directory first!'], 422);
        }
    }
}
