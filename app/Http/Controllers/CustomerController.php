<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request as HttpRequest;
// use Request;
// use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\BusinessUser;
use App\Models\CustomerProfile;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Str;
use Mail;
use Validator;
use App\Rules\DomainNameRule;
use \Mailjet\Resources;
use Hashids\Hashids;
use Laravel\Passport\Token;

class CustomerController extends Controller
{

    public $data;
    public function __construct()
    {
        //$this->middleware('auth');
        $this->data = common_data();
    }

    public function business_customers($offset = 0)
    {
        $this->data['add_button']   = 'Add New';
        $this->data['page_title']    = 'Business Customers';
        $this->data['add_btn_url']  = admin_url() . '/add_business_customer';
        $this->data['update_btn_url'] = admin_url() . '/update_business_customer';
        $this->data['column_title']    = array('Company', 'Business Admin', 'Email', 'Username',  'Gender', 'Status', 'Created On');
        $this->data['column_values'] = array('company_name', 'name', 'email', 'username', 'user_gender', 'user_status', 'row_datetime');
        $this->data['delete_click'] = "deleteTableEntry('" . encrypt('users') . "','" . encrypt('id') . "','{({row-id})}');";
        $this->data['breadcrumbs']  = array('Dashboard' => admin_url(), 'Business Customers' => 'javascript:;');
        $this->data['name'] = $this->data['email'] = $this->data['status'] = $this->data['username']= $this->data['company_name']  = '';


        eql();
        $query = BusinessUser::query()->select('users.id', DB::raw('CONCAT(first_name, " ", last_name) as name'), 'email', 'gender as user_gender', 'users.created_at as row_datetime', 'status as user_status', 'username', 'company_name')->leftJoin('users', 'users.id', '=', 'business_users.user_id')->where('users.user_group_id', '=', '3');

        if (isset($_GET['name']) && trim($_GET['name']) != '') {
            $this->data['name'] = trim($_GET['name']);
            // $query = $query->where('name', 'like', '%' . $this->data['name'] . '%');
            $query = $query->whereRaw(DB::raw('CONCAT(first_name, " ", last_name)') . ' like "%' . $this->data['name'] . '%"');
        }

        if (isset($_GET['email']) && trim($_GET['email']) != '') {
            $this->data['email'] = trim($_GET['email']);
            $query = $query->where('email', 'like', '%' . $this->data['email'] . '%');

        }

        if (isset($_GET['username']) && trim($_GET['username']) != '') {
            $this->data['username'] = trim($_GET['username']);
            $query = $query->where('username', 'like', $this->data['username']);
        }
        if (isset($_GET['company_name']) && trim($_GET['company_name']) != '') {
            $this->data['company_name'] = trim($_GET['company_name']);
            $query = $query->where('company_name', 'like', '%' . $this->data['company_name'] . '%');
        }

        if (isset($_GET['status']) && trim($_GET['status']) != '') {
            $this->data['status'] = trim($_GET['status']);
            $query = $query->where('status', '=', $this->data['status']);
        }

        $query->orderBy('company_name', 'ASC');
        $this->data['tbl_records']     = $query->paginate(per_page());
        //pre_print($data);
        // lq();

        $this->data['distinctCompanyNames'] = DB::table('users')
        ->where('user_group_id', '=', '3')
        ->distinct('company_name')
        ->pluck('company_name');

        $data = page_buttons('business_customers');
        $this->data['ExportBpAdmins']     = 1;
        return view('admin.records_listing', ['data' => array_merge($this->data, $data)]);
    }

    public function add_business_customer(HttpRequest $request)
    {
        if (isset($_POST) && !empty($_POST)) {
            $validations['first_name'] = 'required|string';
            $validations['last_name'] = 'required|string';
            $validations['account_limit'] = 'required';
            $validations['company_friendly_name'] = 'required';
            $validations['domain'] = ['required', new DomainNameRule];
            if ($request->has('tbl_id')) {
                $validations['username'] = 'required|string|unique:users,username,' . $request->tbl_id;
                $validations['email'] = 'required|string|email|unique:users,email,' . $request->tbl_id;
            } else {
                $validations['email'] = 'required|string|email|unique:users';
                // $validations['username'] = 'required|string|unique:users';
                // $validations['password'] = 'required|string|confirmed|min:6';
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
                return response($data, 201);
            }

            if (!isset($request->tbl_id)) {

                $user = new User();
                $user->user_group_id = 3;
                $user->first_login = 1;
                $user->created_by = Auth::user()->id;
                $user->created_at = Carbon::now();
                $user->email_verified_at = Carbon::now();
            } else {
                $user = User::findorfail($request->tbl_id);
                $user->updated_by = Auth::user()->id;
                $user->updated_at = Carbon::now();
            }

            $password = Str::random(8);
            $user->email = $request->email;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->company_name = $request->company_name;
            $user->company_friendly_name = $request->company_friendly_name;
            if ($request->has('tbl_id')) {
                $user->username = $request->username;
            } else {
                if ($request->has('username')) {
                    $user->username = $request->username;
                } else {
                    $user->username = unique_username($user->company_friendly_name . email_split($user->email));
                }
            }
            $user->is_pro = is_business_user();
            $user->gender = $request->gender;
            $user->status = $request->status;
            $user->save();

            if (isset($request->tbl_id) && $request->status == 0) {
                $members = BusinessUser::whereRaw('parent_id = ' . $user->id . ' OR user_id = ' . $user->id)->get();
                if (!empty($members)) {
                    foreach ($members as $member) {
                        // $tokens = Token::where('user_id', $member->user_id)->where('revoked', false)->get();
                        // foreach ($tokens as $token) {
                        //     $token->revoke();
                        //     // Optionally, you can also delete the token to remove it from the database
                        //     $token->delete();
                        // }
                        $userObj = User::where('id', $member->user_id);
                        if ($userObj->count() > 0) {
                            $userObj->first()->tokens()->delete();
                        }
                    }
                }
            }

            if (!isset($request->tbl_id)) {

                $user->password = bcrypt($password);
                $user->save();

                $Home = new UserController;
                $Home->add_contact_card_profile($user);
                addGlobalProfiles(Auth::user(), $user, $request->email);
                createUserSettings($user, Auth::user());

                $BusinessUser = new BusinessUser();
                $BusinessUser->user_id = $user->id;
                $BusinessUser->account_limit = $request->account_limit;
                $BusinessUser->domain = $request->domain;
                $BusinessUser->user_role = 'admin'; //$request->user_role;
                $BusinessUser->created_by = Auth::user()->id;
                $BusinessUser->created_at = Carbon::now();
                $BusinessUser->save();

                if ($request->account_limit > 0) {
                    //UPDATE
                    DB::table('unique_codes')
                        ->where('status', 0)->where('user_id', 0)
                        ->limit($request->account_limit)
                        ->orderBy('id', 'ASC')
                        ->update(array('brand' => $user->username, 'status' => 1));  // update the record in the DB.
                }

                $html = 'Hello ' . $user->first_name . ' ' . $user->last_name . ',<br><br>
                Welcome to the AddMee Business Portal.<br><br>
                This is to provide you with your credentials to log in.<br><br>
                Your initial password is: <i>' . $password . '</i><br><br>
                After you have logged in for the first time, you will be asked to create your own password.<br><br>
                The link to the AddMee Business Portal is: ' . business_portal_url() . '<br><br>
                After successful setup, you can configure the AddMee company profile and create additional employees according to your booked quota.<br><br>
                Under "Settings -> FAQ" you will receive support in the form of descriptive texts and explanatory videos. If you have any questions, please contact our customer service at customercare@addmee.de.<br><br>
                Good luck and a good start with AddMee Business.<br><br>
                Your ' . config("app.name", "") . ' customer service';
                // echo $html;exit;

                $subject = "Welcome to the " . config("app.name", "") . " Business Portal";

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

                // Mail::send('mail.sent_otp', $emailInfo, function ($message) use ($emailInfo) {
                //     $message->to($emailInfo["email"])->subject($emailInfo["subject"]);
                // });
            } else {

                $BusinessUser = BusinessUser::where('user_id', $user->id)->first();
                $BusinessUser->account_limit = $request->account_limit;
                $BusinessUser->domain = $request->domain;
                $BusinessUser->updated_by = Auth::user()->id;
                // pre_print($BusinessUser);
                $BusinessUser->save();
            }

            $data['message'] = 'Record Updated Successfully.';
            $data['success'] = TRUE;
            return response($data, 201);
        } else {
            $this->data['redirect']    = admin_url() . '/business_customers';
            $this->data['post_url']    = admin_url() . '/add_business_customer';
            $this->data['page_title']  = 'Add Business Customer';
            $this->data['form_fields'] = array('first_name' => 'First-Name_text', 'last_name' => 'Last-Name_text', 'email' => 'Email_text', 'username' => 'Username_text', 'company_name' => 'Company-Name_text', 'company_friendly_name' => 'Company-Friendly-Name_text', 'gender' => 'Gender_radio', 'account_limit' => 'Account-Limit_number', 'domain' => 'Domain_text', 'status' => 'Status_radio');
            //'password' => 'Password_password', 'password_confirmation' => 'Confirm-Password_password', 'user_role' => 'User-Role_radio',

            $this->data['status'] = array('Active_1', 'Inactive_0');
            $this->data['gender'] = array('Male_1', 'Female_2', 'Unknown_3');
            $this->data['user_role'] = array('Administrator_admin', 'User_user');
            $tbl_record = array();

            $tbl_id = isset($request->id) ? decrypt($request->id) : 0;
            if ($tbl_id != '0') {
                $this->data['page_title'] = 'Update bBusiness Customer';
                $this->data['tbl_id']     = $this->data['user_id'] = $tbl_id;
                $this->data['tbl_record'] = $tbl_record = User::findorfail($this->data['tbl_id']);

                $BusinessUser = BusinessUser::where('user_id', $tbl_id)->first();
                $this->data['tbl_record']->domain = $BusinessUser->domain;
                $this->data['tbl_record']->account_limit = $BusinessUser->account_limit;

                // $fields = $this->data['form_fields'];
                // $fields['email'] = 'Email_email';
                // $this->data['form_fields'] = $fields;
                $this->data['redirect']   = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->data['redirect'];
            } else {
                unset($this->data['form_fields']['username']);
            }

            $this->data['breadcrumbs'] = array('Home' => admin_url(), 'Business Customers' => admin_url() . '/business_customers', $this->data['page_title'] => '#');
            //pre_print($data);
            return view('admin.form_tpl', ['data' => $this->data, 'tbl_record' => $tbl_record]);
        }
    }

    public function members_activation_details(HttpRequest $request)
    {
        // https://github.com/vinkla/hashids
        // composer require hashids/hashids
        $hashids = new Hashids('addmee');
        // $encode_id = $hashids->encode($request->id, 10);
        $decode_id = $hashids->decode($request->code);
        $user_id = isset($decode_id[0]) ? $decode_id[0] : 0;

        $user = User::find($user_id);

        $data['success'] = TRUE;
        $data['message'] = 'Profile Details.';
        $data['data'] = array('member' => $user);
        return response()->json($data, 201);
    }
}
