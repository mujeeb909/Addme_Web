<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CustomerController;
use Illuminate\Http\Request as HttpRequest;
// use Request;
// use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
// use Response;
use Session;
// use Hash;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Validation\Rules\Unique;
use Ladumor\OneSignal\OneSignal;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserGroups;
use App\Models\Profile;
use App\Models\ProfileType;
use App\Models\Menu;
use App\Models\CustomerProfile;
use App\Models\BusinessInfo;
use App\Models\BusinessRequest;
use App\Models\BusinessUser;
use App\Models\ContactCard;
use App\Models\UserNote;
use App\Models\TapsViews;
use App\Models\DeleteAccount;
use App\Models\Feedback;
use App\Models\UniqueCode;
use App\Models\Platform;
use App\Models\TemplateAssignee;
use App\Models\UserTemplate;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{

    public $data;
    public function __construct()
    {
        //$this->middleware('auth');
        $this->data = common_data();
    }

    public function index()
    {
        //dd(session()->all());
        //echo Auth::user()->email;exit;

        /*ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);*/

        $this->data['customers'] = User::query()->select(DB::raw('count(1) as total'), DB::raw('SUM(CASE WHEN is_pro =1 THEN 1 ELSE 0 END) as is_pro'), DB::raw('SUM(CASE WHEN is_pro =0 THEN 1 ELSE 0 END) as normal'), DB::raw('SUM(CASE WHEN gender =1 THEN 1 ELSE 0 END) as male'), DB::raw('SUM(CASE WHEN gender = 2 THEN 1 ELSE 0 END) as female'), DB::raw('SUM(CASE WHEN gender = 3 THEN 1 ELSE 0 END) as unknown'))->where('user_group_id', '=', '2')->get();

        $this->data['total_customers'] = $this->data['customers'][0]->total; //User::where('user_group_id', '=', '2')->get()->count();
        $this->data['total_pCustomers'] = $this->data['customers'][0]->normal; //User::where('user_group_id', '=', '2')->where('is_pro', '=', '0')->get()->count();
        $this->data['total_bCustomers'] = $this->data['customers'][0]->is_pro; //User::where('user_group_id', '=', '2')->where('is_pro', '=', '1')->get()->count();

        $total_customers = User::query()->select(DB::raw('DATE_FORMAT(created_at,"%d %b") as rec_date'), DB::raw('COUNT(1) as total'))->where('created_at', '>=', 'DATE(NOW()) - INTERVAL 7 DAY')->where('user_group_id', '=', '2')->groupBy(DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'))->offset(0)->limit(7)->get();

        $no_of_days = getLastNDays(7, 'd M');
        $total_customers_vals = $_no_of_days = '';

        if (!empty($total_customers)) {
            foreach ($total_customers as $rec) {
                $no_of_days[str_replace(' ', '', $rec->rec_date)]['total'] = $rec->total;
            }
        }

        if (!empty($no_of_days)) {
            foreach ($no_of_days as $rec) {
                if (isset($rec['day'])) {
                    $_no_of_days .= '"' . $rec['day'] . '"' . ',';
                    $total_customers_vals .= $rec['total'] . ',';
                }
            }
        }

        $this->data['no_of_days'] = trim($_no_of_days, ',');
        $this->data['last_7days_customers'] = trim($total_customers_vals, ',');

        $this->data['gender_wise'] = $this->data['customers']; //User::query()->select(DB::raw('SUM(CASE WHEN gender =1 THEN 1 ELSE 0 END) as male'),DB::raw('SUM(CASE WHEN gender = 2 THEN 1 ELSE 0 END) as female'),DB::raw('SUM(CASE WHEN gender = 3 THEN 1 ELSE 0 END) as unknown'))->where('user_group_id', '=', '2')->get();

        $this->data['unique_codes'] = UniqueCode::query()->select(DB::raw('count(1) as total'), DB::raw('SUM(CASE WHEN user_id != 0 THEN 1 ELSE 0 END) as activated'), DB::raw('SUM(CASE WHEN user_id = 0 AND status = 1 THEN 1 ELSE 0 END) as available'), DB::raw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active'), DB::raw('SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as inactive'), DB::raw('SUM(CASE WHEN brand != "" AND brand IS NOT NULL THEN 1 ELSE 0 END) as branded'))->first();
        // pre_print($this->data['unique_codes']);
        return view('admin.index', ['data' => $this->data]);
    }

    public function login(HttpRequest $request)
    {

        if (isset($_POST) && !empty($_POST)) {

            $validator = Validator::make($request->all(), array(
                'email' => 'required|string',
                'password' => 'required|string'
            ));

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

            $user = User::where('email', $request->email)->whereIn('user_group_id', ['1'])->first();
            if ($user != null) {
                $authSuccess = false;
                if (Hash::check($request->password, $user->password)) {
                    auth()->login($user, true);
                    $authSuccess = true;
                }

                if ($authSuccess) {

                    if ($user->status == 0) {
                        $data['errors'][0]['error'] = "Your account isn't active.";
                        $data['success'] = FALSE;

                        return response($data, 201);
                    } else {

                        //unlink(root_dir().'/jsons/'.Auth::user()->id.'-permitted-menu-slugs.json');
                        $request->session()->regenerate();
                        $data['message'] = 'Logged In Successfully.';
                        $data['success'] = TRUE;
                        return response($data, 201);
                    }
                }
            }

            $data['errors'][0]['error'] = 'Email or password doesn\'t seem correct.';
            $data['success'] = FALSE;

            return response($data, 201);
        } else {
            if (!empty(Auth::user())) {
                return redirect('/admin-dashboard');
            }
            return view('admin.auth');
        }
    }

    public function staffs($offset = 0)
    {

        $this->data['add_button']   = 'Add New';
        $this->data['page_title']    = 'Staffs';
        $this->data['add_btn_url']  = admin_url() . '/add_staff';
        $this->data['update_btn_url'] = admin_url() . '/update_staff';
        $this->data['column_title']    = array('Name', 'Email', 'Gender', 'User Group', 'Status', 'Created On');
        $this->data['column_values'] = array('name', 'email', 'user_gender', 'user_group', 'user_status', 'row_datetime');
        $this->data['delete_click'] = "deleteTableEntry('" . encrypt('users') . "','" . encrypt('id') . "','{({row-id})}');";
        $this->data['breadcrumbs']  = array('Dashboard' => admin_url(), 'Staffs' => 'javascript:;');
        $this->data['show_add']     = 1;
        $this->data['ExportBpAdmins']     = 0;
        $this->data['name'] = $this->data['email'] = $this->data['status'] = '';

        //eql();//DB::raw('initcap(role) as role'),
        $query = User::query()->select('users.id', 'name', 'email', 'status as user_status', 'gender as user_gender', 'users.created_at as row_datetime', 'user_groups.title as user_group')->where('role', '<>', 'admin');

        if (isset($_GET['name']) && trim($_GET['name']) != '') {
            $this->data['name'] = trim($_GET['name']);
            $query = $query->where('name', 'like', '%' . $this->data['name'] . '%');
        }

        if (isset($_GET['email']) && trim($_GET['email']) != '') {
            $this->data['email'] = trim($_GET['name']);
            $query = $query->where('email', 'like', $this->data['email']);
        }

        if (isset($_GET['status']) && trim($_GET['status']) != '') {
            $this->data['status'] = trim($_GET['status']);
            $query = $query->where('status', '=', $this->data['status']);
        }

        $query->leftJoin('user_groups', 'users.user_group_id', '=', 'user_groups.id');
        $query->orderBy('id', 'DESC');
        $this->data['tbl_records']     = $query->paginate(per_page());
        //$email = DB::table('users')->where('name', 'John')->pluck('name', 'email');
        //lq();
        //pre_print($data);

        $data = page_buttons('staffs');
        return view('admin.records_listing', ['data' => array_merge($this->data, $data)]);
    }

    public function add_staff(HttpRequest $request)
    {
        //echo decrypt($request->id);exit;
        if (isset($_POST) && !empty($_POST)) {
            $validations['name'] = 'required|string';
            if (!isset($request->tbl_id)) {

                $validations['email'] = 'required|string|email|unique:users';
                $validations['password'] = 'required|string|confirmed|min:6';
            } else {
                if ($request->password != ''  && $request->password_confirmation != '') {
                    $validations['password'] = 'required|string|confirmed|min:6';
                }
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

                $user = new User;
                $user->email = $request->email;
                $user->role = 'unknown'; //$request->role;
                $user->created_by = Auth::user()->id;
                $user->created_at = Carbon::now();
                $user->email_verified_at = Carbon::now();
            } else {
                $user = User::findorfail($request->tbl_id);
                $user->updated_by = Auth::user()->id;
                $user->updated_at = Carbon::now();
            }

            $user->name = $request->name;
            $user->password = ($request->password != '') ? bcrypt($request->password) : $user->password;
            $user->gender = $request->gender;
            $user->status = $request->status;
            $user->user_group_id = $request->user_group_id;
            $user->save();

            $data['message'] = 'Record Updated Successfully.';
            $data['success'] = TRUE;
            return response($data, 201);
        } else {
            $this->data['redirect']    = admin_url() . '/staffs';
            $this->data['post_url']    = admin_url() . '/add_staff';
            $this->data['page_title']  = 'Add Account';
            $this->data['form_fields'] = array('name' => 'Name_text', 'email' => 'Email_text', 'password' => 'Password_password', 'password_confirmation' => 'Confirm-Password_password', 'user_group_id' => 'User-Group_dd', 'gender' => 'Gender_radio', 'status' => 'Status_radio'); //, 'role' => 'Account-Role_radio');

            $this->data['status'] = array('Active_1', 'Inactive_0');
            $this->data['gender'] = array('Male_1', 'Female_2', 'Unknown_3');
            $this->data['role'] = array('Agent_agent', 'Finance_finance');
            $this->data['user_group_id'] = UserGroups::query()->where('id', '<>', 1)->get();
            $tbl_record = array();

            $tbl_id = isset($request->id) ? decrypt($request->id) : 0;
            if ($tbl_id != '0') {
                $this->data['page_title'] = 'Update Staff';
                $this->data['tbl_id']     = $this->data['user_id'] = $tbl_id;
                $this->data['tbl_record'] = $tbl_record = User::findorfail($this->data['tbl_id']);

                $fields = $this->data['form_fields'];
                $fields['email'] = 'Email_email';
                unset($fields['role']);
                if (!empty($this->data['tbl_record']) && $this->data['tbl_record']->role == 1) {
                    unset($fields['role'], $fields['password'], $fields['cpassword']);
                }

                $this->data['form_fields'] = $fields;
                $this->data['redirect']   = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->data['redirect'];
            }

            $this->data['breadcrumbs'] = array('Home' => admin_url(), 'Staffs' => admin_url() . '/staffs', 'Add Account' => '#');
            //pre_print($data);
            return view('admin.form_tpl', ['data' => $this->data, 'tbl_record' => $tbl_record]);
        }
    }

    public function customers($offset = 0)
    {
        $this->data['add_button']   = 'Add New';
        $this->data['page_title']    = 'Customers';
        $this->data['add_btn_url']  = admin_url() . '/add_customer';
        $this->data['update_btn_url'] = admin_url() . '/update_customer';
        $this->data['column_title']    = array('Name', 'Email', 'Username', 'Gender','Company Name' ,'Status', 'Is Pro?', 'Created On');
        $this->data['column_values'] = array('name', 'email', 'username', 'user_gender','company_name', 'user_status', 'is_pro_yes_no', 'row_datetime');
        $this->data['delete_click'] = "deleteTableEntry('" . encrypt('users') . "','" . encrypt('id') . "','{({row-id})}');";
        $this->data['breadcrumbs']  = array('Dashboard' => admin_url(), 'Customers' => 'javascript:;');
        $this->data['name'] = $this->data['email'] = $this->data['status'] = $this->data['username'] = $this->data['company_name'] = '';

        //eql();
        $query = User::query()->select('users.id', DB::raw('CONCAT(first_name," ",last_name) as name'), 'email', 'username', 'gender as user_gender','company_name', 'users.created_at as row_datetime', 'status as user_status', 'is_pro as is_pro_yes_no', 'business_users.user_role')->where('user_group_id', '=', '2');
        $query->leftJoin('business_users', 'users.id', '=', 'business_users.user_id');

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

        $query->orderBy('id', 'DESC');
        $this->data['tbl_records']     = $query->paginate(per_page());
        //pre_print($data);
        $this->data['distinctCompanyNames'] = DB::table('users')
        ->where('user_group_id', '=', '3')
        ->distinct('company_name')
        ->pluck('company_name');

        $data = page_buttons('customers');
        $this->data['ExportBpAdmins']     = 0;
        return view('admin.records_listing', ['data' => array_merge($this->data, $data)]);
    }

    public function add_customer(HttpRequest $request)
    {
        //echo decrypt($request->id);exit;
        if (isset($_POST) && !empty($_POST)) {
            $validations['first_name'] = 'required|string';
            $validations['last_name'] = 'required|string';

            if ($request->has('tbl_id')) {
                $validations['username'] = 'required|string|unique:users,username,' . $request->tbl_id;
                $validations['email'] = 'required|string|email|unique:users,email,' . $request->tbl_id;
                if ($request->password != ''  && $request->password_confirmation != '') {
                    $validations['password'] = 'required|string|confirmed|min:6';
                }
            } else {
                $validations['username'] = 'required|string|unique:users';
                $validations['email'] = 'required|string|email|unique:users';
                $validations['password'] = 'required|string|confirmed|min:6';
            }

            if ($request->is_pro == is_grace_user() || $request->is_pro == is_pro_user()) {
                $validations['subscription_date'] = 'required';
                $validations['subscription_expires_on'] = 'required';
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

                $user = new User;
                $user->created_by = Auth::user()->id;
                $user->created_at = Carbon::now();
                $user->email_verified_at = Carbon::now();
            } else {
                $user = User::findorfail($request->tbl_id);
                $user->updated_by = Auth::user()->id;
                $user->updated_at = Carbon::now();
            }

            $user->email = $request->email;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->username = $request->username;
            $user->bio = $request->bio;
            $user->dob = $request->dob;
            $user->first_login = $request->first_login;
            $user->designation = $request->designation;
            $user->company_name = $request->company_name;
            $user->password = ($request->password != '') ? bcrypt($request->password) : $user->password;
            $user->is_pro = in_array($request->is_pro, [3, 4]) ? is_business_user() : $request->is_pro;
            $user->is_public = $request->is_public;
            $user->gender = $request->gender;
            $user->status = $request->status;
            if ($request->is_pro == is_grace_user() || $request->is_pro == is_pro_user()) {
                $user->subscription_date = $request->subscription_date;
                $user->subscription_expires_on = $request->subscription_expires_on;
            }
            $user->save();

            $is_business_admin = 4;
            $is_business_user = 3;
            $is_business_moderator = 2;
            if (in_array($request->is_pro, [$is_business_admin, $is_business_moderator, $is_business_user])) {

                // $BusinessUserExists = BusinessUser::where('user_id', $user->id);
                // if ($BusinessUserExists->count() == 0) {
                //     $BusinessUser = new BusinessUser();
                //     $BusinessUser->parent_id = 0;
                //     $BusinessUser->user_role = 'admin'; //$request->user_role;
                //     $parts = explode('@', $user->email);
                //     // Remove and return the last part, which should be the domain
                //     $domain = array_pop($parts);
                //     $BusinessUser->domain = trim($domain);
                //     $BusinessUser->created_by = Auth::user()->id;
                //     $BusinessUser->account_limit = 1;
                //     $BusinessUser->created_at = Carbon::now();
                // } else {
                //     $BusinessUser = $BusinessUserExists->first();
                // }

                // $BusinessUser->user_id = $user->id;
                // $BusinessUser->save();

                // $user->user_group_id = 3;
                // $user->save();

                $BusinessUserExists = BusinessUser::where('user_id', $user->id);
                $parent_id = $request->business_company;
                if ($parent_id == '') {
                    $data['success'] = FALSE;
                    $data['errors'][0]['error'] = 'Please select a company.';
                    $data['success'] = FALSE;
                    return response($data, 201);
                }

                $BusinessAdmin = BusinessUser::where('user_id', $parent_id)->first();
                $business_domain = $BusinessAdmin->domain;
                $max_account_limit = $BusinessAdmin->account_limit;
                $parts = explode('@', $user->email);
                $domain = array_pop($parts);

                $CustomerController = new CustomerController;
                $account_limit = $CustomerController->account_limit($parent_id, []);
                if ($account_limit != 1) {
                    $data['success'] = FALSE;
                    $data['errors'][0]['error'] = ($account_limit == 2) ? 'Account limit is exceeding.' : 'Account limit has been reached.';
                    $data['success'] = FALSE;
                    return response($data, 201);
                }

                $allowed = explode(',', trim($business_domain));
                if (!in_array($domain, $allowed) && !in_array('*', $allowed)) {
                    $data['errors'][0]['error'] = 'Email addresses with only whitelisted domains can be added.';
                    $data['success'] = FALSE;
                    return response($data, 201);
                }

                $old_parent_id = 0;
                if ($request->is_pro == $is_business_admin) {
                    $old_parent_id = $parent_id;
                    $parent_id = $user->id;
                }

                if ($BusinessUserExists->count() == 0) {
                    $BusinessUser = new BusinessUser();
                    $BusinessUser->parent_id = $parent_id == $user->id ? 0 : $parent_id;
                    $BusinessUser->user_role = $request->is_pro == $is_business_user ? 'user' : 'admin';
                    $BusinessUser->domain = $request->is_pro == $is_business_user ? $domain : $business_domain;
                    $BusinessUser->created_by = Auth::user()->id;
                    $BusinessUser->account_limit = $request->is_pro == $is_business_user ? 1 : $max_account_limit;
                    $BusinessUser->created_at = Carbon::now();
                } else {
                    $BusinessUser = $BusinessUserExists->first();
                    $BusinessUser->parent_id = $parent_id == $user->id ? 0 : $parent_id;
                    $BusinessUser->user_role = $request->is_pro == $is_business_user ? 'user' : 'admin';
                    $BusinessUser->domain = $request->is_pro == $is_business_user ? $domain : $business_domain;
                    $BusinessUser->account_limit = $request->is_pro == $is_business_user ? 1 : $max_account_limit;
                    $BusinessUser->updated_by = Auth::user()->id;
                }

                $BusinessUser->user_id = $user->id;
                $BusinessUser->save();

                if ($old_parent_id != 0) {
                    $usersToUpdate = BusinessUser::where('parent_id', $old_parent_id);
                    if ($usersToUpdate->count() > 0) {
                        $usersToUpdate = $usersToUpdate->get();
                        foreach ($usersToUpdate as $updateUser) {
                            $updateUser->parent_id = $parent_id;
                            $updateUser->save();
                        }
                    }

                    $oldBusinessOwner = User::where('id', $old_parent_id)->first();
                    $oldBusinessOwner->user_group_id = 2;
                    $oldBusinessOwner->updated_by = Auth::user()->id;
                    $oldBusinessOwner->save();

                    $newBusinessOwner = User::where('id', $parent_id)->first();
                    $newBusinessOwner->user_group_id = 3;
                    $newBusinessOwner->company_name = ($newBusinessOwner->company_name == '' || $newBusinessOwner->company_name == NULL) ? $oldBusinessOwner->company_name : $newBusinessOwner->company_name;
                    $newBusinessOwner->updated_by = Auth::user()->id;
                    $newBusinessOwner->save();

                    $BusinessAdmin->parent_id = $parent_id;
                    $BusinessAdmin->user_role = 'user';
                    $BusinessAdmin->account_limit = 1;
                    $BusinessAdmin->updated_by = Auth::user()->id;
                    $BusinessAdmin->save();

                    $old = ['id' => $oldBusinessOwner->id, 'email' => $oldBusinessOwner->email];
                    $new = ['id' => $newBusinessOwner->id, 'email' => $newBusinessOwner->email];

                    $info['type'] = 'business-owner-changed';
                    $info['type_id'] = 0;
                    $info['details'] = json_encode(['old_owner' => $old, 'new_owner' => $new]);
                    $info['device_id'] = isset($request->device_id) ? $request->device_id : 0;
                    $info['ip_address'] = getUserIP();
                    $info['created_by'] = Auth::user()->id;
                    add_activity($info);
                }
            }
            // else if ($request->is_pro == 3) {

            //     $BusinessUserExists = BusinessUser::where('user_id', $user->id);
            //     if ($BusinessUserExists->count() == 0) {
            //         //
            //         $parent_id = $request->business_company;
            //         $BusinessAdmin = BusinessUser::where('user_id', $parent_id)->first();
            //         $business_domain = $BusinessAdmin->domain;
            //         $parts = explode('@', $user->email);
            //         $domain = array_pop($parts);

            //         $CustomerController = new CustomerController;
            //         $account_limit = $CustomerController->account_limit($parent_id, []);
            //         if ($account_limit != 1) {
            //             $data['success'] = FALSE;
            //             $data['errors'][0]['error'] = ($account_limit == 2) ? 'Account limit is exceeding.' : 'Account limit has been reached.';
            //             $data['success'] = FALSE;
            //             return response($data, 201);
            //         }

            //         $allowed = explode(',', trim($business_domain));
            //         if (!in_array($domain, $allowed)) {
            //             $data['errors'][0]['error'] = 'Email addresses with only whitelisted domains can be added.';
            //             $data['success'] = FALSE;
            //             return response($data, 201);
            //         }
            //         //
            //         $BusinessUser = new BusinessUser();
            //         $BusinessUser->parent_id = $parent_id;
            //         $BusinessUser->user_role = 'user';
            //         $BusinessUser->domain = trim($domain);
            //         $BusinessUser->created_by = Auth::user()->id;
            //         $BusinessUser->account_limit = 1;
            //         $BusinessUser->created_at = Carbon::now();
            //     } else {
            //         $BusinessUser = $BusinessUserExists->first();
            //     }

            //     $BusinessUser->user_id = $user->id;
            //     $BusinessUser->save();
            // }


            $data['message'] = 'Record Updated Successfully.';
            $data['success'] = TRUE;
            return response($data, 201);
        } else {
            $this->data['redirect']    = admin_url() . '/customers';
            $this->data['post_url']    = admin_url() . '/add_customer';
            $this->data['page_title']  = 'Add Customer';
            $this->data['form_fields'] = array('first_name' => 'First-Name_text', 'last_name' => 'Last-Name_text', 'designation' => 'Designation_text', 'company_name' => 'Company-Name_text', 'email' => 'Email_text', 'username' => 'Username_username', 'bio' => 'Bio_textarea', 'password' => 'Password_password', 'password_confirmation' => 'Confirm-Password_password', 'dob' => 'Date-of-Birth_date', 'gender' => 'Gender_radio', 'status' => 'Status_radio', 'is_pro' => 'Is-Pro?_radio', 'business_company' => 'Company_dd', 'subscription_date' => 'Subscription-Date_date', 'subscription_expires_on' => 'Subscription-Expires-On_date', 'is_public' => 'Is-Public?_radio', 'first_login' => 'First-Login_radio');

            $this->data['status'] = array('Active_1', 'Inactive_0');
            $this->data['is_public'] = array('Yes_1', 'No_0');
            $this->data['is_pro'] = array('Pro-User_1', 'Normal-User_0', 'Free-Subscription_-1', 'Business-Moderator_2', 'Business-User_3', 'Business-Admin_4');
            $this->data['first_login'] = array('Yes_1', 'No_0');
            // $this->data['forced_reset'] = array('Yes_1', 'No_0');
            $this->data['gender'] = array('Male_1', 'Female_2', 'Unknown_3');
            $this->data['business_company'] = User::select('id', 'company_name as title')->where('user_group_id', 3)->orderBy('company_name', 'ASC')->get();
            $tbl_record = array();

            $tbl_id = isset($request->id) ? decrypt($request->id) : 0;
            if ($tbl_id != '0') {
                $this->data['page_title'] = 'Update Customer';
                $this->data['tbl_id']     = $this->data['user_id'] = $tbl_id;
                $this->data['tbl_record'] = $tbl_record = User::findorfail($this->data['tbl_id']);
                $BusinessUser = BusinessUser::where('user_id', $tbl_id);
                if ($BusinessUser->count() > 0) {
                    $BusinessUser = $BusinessUser->first();
                    $this->data['tbl_record']->is_pro = $BusinessUser->user_role == 'user' ? 3 : 2;
                    $this->data['tbl_record']->business_company = $BusinessUser->parent_id;
                }

                $this->data['tbl_record']->subscription_date = date('Y-m-d', strtotime($tbl_record->subscription_date));
                $this->data['tbl_record']->subscription_expires_on = date('Y-m-d', strtotime($tbl_record->subscription_expires_on));

                // $fields = $this->data['form_fields'];
                // $fields['email'] = 'Email_email';
                // $this->data['form_fields'] = $fields;
                $this->data['redirect']   = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->data['redirect'];
            }

            $this->data['breadcrumbs'] = array('Home' => admin_url(), 'Customers' => admin_url() . '/customers', 'Update Customer' => '#');
            // pre_print($this->data['tbl_record']);
            return view('admin.form_tpl', ['data' => $this->data, 'tbl_record' => $tbl_record]);
        }
    }

    public function assign_customer_cp(HttpRequest $request)
    {
        //echo decrypt($request->id);exit;
        if (isset($_POST) && !empty($_POST)) {
            $validations['business_name'] = 'required|string';

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

            $CustomerController = new CustomerController;
            $account_limit = $CustomerController->account_limit($request->business_name, []);
            if ($account_limit != 1) {
                $data['success'] = FALSE;
                $data['errors'][0]['error'] = ($account_limit == 2) ? 'Account limit is exceeding.' : 'Account limit has been reached.';
                $data['success'] = FALSE;
                return response($data, 201);
            }

            $User = User::findorfail($request->tbl_id);
            $User->is_pro = is_business_user();
            $User->save();

            $BusinessUser = new BusinessUser();
            $BusinessUser->user_id = $User->id;
            $BusinessUser->parent_id = $request->business_name;
            $BusinessUser->account_limit = 0;
            $BusinessUser->domain = NULL;
            $BusinessUser->user_role = 'user'; //user,admin
            $BusinessUser->created_by = Auth::user()->id;
            $BusinessUser->created_at = Carbon::now();
            $BusinessUser->save();

            //email should be sent here

            $data['message'] = 'Record Updated Successfully.';
            $data['success'] = TRUE;
            return response($data, 201);
        } else {
            $this->data['redirect']    = admin_url() . '/customers';
            $this->data['post_url']    = admin_url() . '/assign_customer_cp';

            $this->data['form_fields'] = array('business_name' => 'Business-Name_dd');
            $this->data['business_name'] = User::select('id', 'company_name as title')->where('user_group_id', '3')->where('status', 1)->where('company_name', '!=', '')->where('company_name', '!=', NULL)->get();
            $tbl_record = array();

            $tbl_id = isset($request->id) ? decrypt($request->id) : 0;
            if ($tbl_id != '0') {
                $this->data['tbl_id']     = $this->data['user_id'] = $tbl_id;
                $this->data['tbl_record'] = $tbl_record = User::findorfail($this->data['tbl_id']);
                $this->data['page_title'] = 'Assign ' . $tbl_record->first_name . ' ' . $tbl_record->last_name . ' to Customer Portal';
                $this->data['redirect']   = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->data['redirect'];
            } else {
                return redirect('/customers');
            }

            $this->data['breadcrumbs'] = array('Home' => admin_url(), 'Customers' => admin_url() . '/customers', $this->data['page_title'] => '#');
            //pre_print($data);
            return view('admin.form_tpl', ['data' => $this->data, 'tbl_record' => $tbl_record]);
        }
    }

    public function import_customers_business(HttpRequest $request)
    {
        set_time_limit(0);
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        if (isset($_POST) && !empty($_POST)) {

            $path = $request->file('file')->getRealPath(); //root_dir().'test-codes.csv';//
            $records = array_map('str_getcsv', file($path));
            // pre_print($records);
            if (!count($records) > 0) {
                $data['errors'][0]['error'] = 'No data found!';
                $data['success'] = FALSE;
                return response($data, 201);
            }

            // Get field names from header column
            $fields = array_map('strtolower', $records[0]);

            // Remove the header column
            array_shift($records);
            $rows = [];
            foreach ($records as $record) {
                // pre_print($record);
                if (count($fields) < 2 || count($fields) != count($record)) {
                    // return 'csv_upload_invalid_data';
                    $data['errors'][0]['error'] = $record;
                    $data['success'] = FALSE;
                    return response($data, 201);
                }

                // Decode unwanted html entities
                $record =  array_map("html_entity_decode", $record);

                // Set the field name as key
                $record = array_combine($fields, $record);

                // Get the clean data
                $rows[] = $this->clear_encoding_str($record);
            }
            // pre_print($rows);
            $i = 0;
            foreach ($rows as $data) {
                // pre_print($data);
                $User = User::where('email', $data['registration-email']);
                if ($User->count() != 0) {
                    continue;
                }

                //create user account
                $User = new User;
                $User->name = $data['first-name'] . ' ' . $data['last-name'];
                $User->first_name = $data['first-name'];
                $User->last_name = $data['last-name'];
                $User->email = $data['registration-email'];
                $User->username = !isset($data['username']) ? unique_username($data['company-name'] . email_split($data['registration-email'])) : $data['username'];
                $User->company_name = $data['company-name'];
                $User->designation = $data['designation'];
                $User->bio = isset($data['bio']) ? $data['bio'] : NULL;
                $User->password = bcrypt('reflex@11');
                $User->status = 1;
                $User->fcm_token = 16261;
                $User->allow_data_usage = 0;
                $User->device_type = 0;
                $User->device_id = 0;
                $User->first_login = 1;
                $User->is_pro = is_business_user();
                $User->vcode = rand(111111, 999999);;
                $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
                $User->privacy_policy_date = Carbon::now();
                $User->license_date = Carbon::now();
                $User->created_at = Carbon::now();
                $User->created_by = Auth::user()->id;
                $User->updated_by = 16261;
                $User->save();

                $Home = new UserController;
                $Home->add_contact_card_profile($User);

                $BusinessUser = new BusinessUser();
                $BusinessUser->user_id = $User->id;
                $BusinessUser->parent_id = 16261;
                $BusinessUser->account_limit = 0;
                $BusinessUser->domain = NULL;
                $BusinessUser->user_role = 'user'; //$request->user_role;
                $BusinessUser->created_by = Auth::user()->id;
                $BusinessUser->created_at = Carbon::now();
                $BusinessUser->save();

                $profiles = ['facebook' => 'facebook', 'instagram' => 'instagram', 'whatsapp' => 'whatsapp', 'call' => 'call', 'text' => 'text', 'www' => 'www', 'address' => 'address', 'snapchat' => 'snapchat', 'twitter' => 'twitter', 'tiktok' => 'tiktok', 'linkedin' => 'linkedin', 'youtube' => 'youtube', 'spotify' => 'spotify', 'apple-music' => 'apple-music', 'soundcloud' => 'soundcloud', 'paypal' => 'paypal', 'klarna' => 'klarna', 'cash-app' => 'cash-app', 'twitch' => 'twitch', 'pinterest' => 'pinterest', 'telegram' => 'telegram', 'linktree' => 'linktree', 'xing' => 'xing', 'google' => 'google', 'discord' => 'discord', 'skype' => 'skype', 'speisekarte' => 'speisekarte', 'getrankekarte' => 'getränkekarte', 'reservieren' => 'reservieren', 'shop' => 'shop', 'calendar' => 'calendar'];
                foreach ($profiles as $key => $val) {
                    if (isset($data[$key]) && trim($data[$key]) != '') {
                        $Obj = new CustomerProfile;
                        $Obj->profile_link     = mb_convert_encoding($data[$key], 'UTF-8');
                        $Obj->profile_code     = mb_convert_encoding($val, 'UTF-8');
                        $Obj->is_business     = 0;
                        $Obj->user_id         = $User->id;
                        $Obj->created_by     = Auth::user()->id;
                        $Obj->created_at     = Carbon::now();
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
                $i++;
            }

            $response['message'] = $i . ': New Users Added Successfully.';
            $response['success'] = TRUE;
            return response($response, 201);
        }
    }

    public function import_customers(HttpRequest $request)
    {
        set_time_limit(0);
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        if (isset($_POST) && !empty($_POST)) {

            $path = $request->file('file')->getRealPath(); //root_dir().'test-codes.csv';//
            $records = array_map('str_getcsv', file($path));
            // pre_print($records);
            if (!count($records) > 0) {
                $data['errors'][0]['error'] = 'No data found!';
                $data['success'] = FALSE;
                return response($data, 201);
            }

            // Get field names from header column
            $fields = array_map('strtolower', $records[0]);

            // Remove the header column
            array_shift($records);
            $rows = [];
            foreach ($records as $record) {
                if (count($fields) < 2 || count($fields) != count($record)) {
                    // return 'csv_upload_invalid_data';
                    $data['errors'][0]['error'] = 'Invalid data!';
                    $data['success'] = FALSE;
                    return response($data, 201);
                }

                // Decode unwanted html entities
                $record =  array_map("html_entity_decode", $record);

                // Set the field name as key
                $record = array_combine($fields, $record);

                // Get the clean data
                $rows[] = $this->clear_encoding_str($record);
            }
            // pre_print($rows);
            $i = 0;
            foreach ($rows as $data) {
                // pre_print($data);
                $User = User::where('email', $data['registration-email']);
                if ($User->count() != 0) {
                    continue;
                }

                $User = User::where('username', $data['username']);
                if ($User->count() != 0) {
                    // continue;
                    $data['username'] = NULL;
                }

                //create user account
                $User = new User;
                $User->name = $data['first-name'] . ' ' . $data['last-name'];
                $User->first_name = $data['first-name'];
                $User->last_name = $data['last-name'];
                $User->email = $data['registration-email'];
                $User->username = $data['username'];
                $User->company_name = $data['company-name'];
                $User->designation = $data['designation'];
                $User->password = bcrypt($data['password']);
                $User->status = 1;
                $User->fcm_token = '';
                $User->allow_data_usage = 0;
                $User->device_type = 0;
                $User->device_id = 0;
                $User->first_login = 1;
                $User->vcode = rand(111111, 999999);;
                $User->vcode_expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
                $User->created_at = Carbon::now();
                $User->created_by = Auth::user()->id;
                $User->privacy_policy_date = Carbon::now();
                $User->license_date = Carbon::now();
                $User->save();

                $Home = new UserController;
                $Home->add_contact_card_profile($User);

                $profiles = ['facebook' => 'facebook', 'instagram' => 'instagram', 'whatsapp' => 'whatsapp', 'call' => 'call', 'text' => 'text', 'www' => 'www', 'address' => 'address', 'snapchat' => 'snapchat', 'twitter' => 'twitter', 'tiktok' => 'tiktok', 'linkedin' => 'linkedin', 'youtube' => 'youtube', 'spotify' => 'spotify', 'apple-music' => 'apple-music', 'soundcloud' => 'soundcloud', 'paypal' => 'paypal', 'klarna' => 'klarna', 'cash-app' => 'cash-app', 'twitch' => 'twitch', 'pinterest' => 'pinterest', 'telegram' => 'telegram', 'linktree' => 'linktree', 'xing' => 'xing', 'google' => 'google', 'discord' => 'discord', 'skype' => 'skype', 'speisekarte' => 'speisekarte', 'getrankekarte' => 'getr�nkekarte', 'reservieren' => 'reservieren', 'shop' => 'shop', 'calendar' => 'calendar'];
                foreach ($profiles as $key => $val) {
                    if (isset($data[$key]) && trim($data[$key]) != '') {
                        $Obj = new CustomerProfile;
                        $Obj->profile_link     = utf8_encode($data[$key]);
                        $Obj->profile_code     = utf8_encode($val);
                        $Obj->is_business     = 0;
                        $Obj->user_id         = $User->id;
                        $Obj->created_by     = Auth::user()->id;
                        $Obj->created_at     = Carbon::now();
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
                $i++;
            }

            $response['message'] = $i . ': New Users Added Successfully.';
            $response['success'] = TRUE;
            return response($response, 201);
        }
    }

    public function clients($offset = 0)
    {

        $this->data['add_button']   = 'Add New';
        $this->data['page_title']    = 'Clients';
        $this->data['add_btn_url']  = admin_url() . '/add_client';
        $this->data['update_btn_url'] = admin_url() . '/update_client';
        $this->data['column_title']    = array('Name', 'Email', 'Gender', 'Status', 'Created On');
        $this->data['column_values'] = array('name', 'email', 'user_gender', 'user_status', 'row_datetime');
        $this->data['delete_click'] = "deleteTableEntry('" . encrypt('users') . "','" . encrypt('id') . "','{({row-id})}');";
        $this->data['breadcrumbs']  = array('Dashboard' => admin_url(), 'Clients' => 'javascript:;');
        $this->data['name'] = $this->data['email'] = $this->data['status'] = $this->data['username'] = '';

        //eql();
        $query = User::query()->select('users.id', 'name', 'email', 'gender as user_gender', 'users.created_at as row_datetime', 'status as user_status')->where('user_group_id', '=', '3');

        if (isset($_GET['name']) && trim($_GET['name']) != '') {
            $this->data['name'] = trim($_GET['name']);
            $query = $query->where('name', 'like', '%' . $this->data['name'] . '%');
        }

        if (isset($_GET['email']) && trim($_GET['email']) != '') {
            $this->data['email'] = trim($_GET['email']);
            $query = $query->where('email', 'like', $this->data['email']);
        }

        if (isset($_GET['status']) && trim($_GET['status']) != '') {
            $this->data['status'] = trim($_GET['status']);
            $query = $query->where('status', '=', $this->data['status']);
        }

        $query->orderBy('id', 'DESC');
        $this->data['tbl_records']     = $query->paginate(per_page());
        //pre_print($data);

        $data = page_buttons('clients');
        $this->data['ExportBpAdmins']     = 0;
        return view('admin.records_listing', ['data' => array_merge($this->data, $data)]);
    }

    public function add_client(HttpRequest $request)
    {
        //echo decrypt($request->id);exit;
        if (isset($_POST) && !empty($_POST)) {
            $validations['name'] = 'required|string';
            if (!isset($request->tbl_id)) {
                $validations['email'] = 'required|string|email|unique:users';
                $validations['password'] = 'required|string|confirmed|min:6';
            } else {
                if ($request->password != ''  && $request->password_confirmation != '') {
                    $validations['password'] = 'required|string|confirmed|min:6';
                }
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

                $user = new User;
                $user->email = $request->email;
                $user->created_by = Auth::user()->id;
                $user->created_at = Carbon::now();
                $user->email_verified_at = Carbon::now();
            } else {
                $user = User::findorfail($request->tbl_id);
                $user->updated_by = Auth::user()->id;
                $user->updated_at = Carbon::now();
            }

            $user->name = $request->name;
            $user->password = ($request->password != '') ? bcrypt($request->password) : $user->password;
            $user->gender = $request->gender;
            $user->status = $request->status;
            $user->save();

            $data['message'] = 'Record Updated Successfully.';
            $data['success'] = TRUE;
            return response($data, 201);
        } else {
            $this->data['redirect']    = admin_url() . '/clients';
            $this->data['post_url']    = admin_url() . '/add_client';
            $this->data['page_title']  = 'Add Client';
            $this->data['form_fields'] = array('name' => 'Name_text', 'email' => 'Email_text', 'password' => 'Password_password', 'password_confirmation' => 'Confirm-Password_password', 'gender' => 'Gender_radio', 'status' => 'Status_radio');

            $this->data['status'] = array('Active_1', 'Inactive_0');
            $this->data['gender'] = array('Male_1', 'Female_2', 'Unknown_3');
            $tbl_record = array();

            $tbl_id = isset($request->id) ? decrypt($request->id) : 0;
            if ($tbl_id != '0') {
                $this->data['page_title'] = 'Update Client';
                $this->data['tbl_id']     = $this->data['user_id'] = $tbl_id;
                $this->data['tbl_record'] = $tbl_record = User::findorfail($this->data['tbl_id']);

                $fields = $this->data['form_fields'];
                $fields['email'] = 'Email_email';

                $this->data['form_fields'] = $fields;
                $this->data['redirect']   = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->data['redirect'];
            }

            $this->data['breadcrumbs'] = array('Home' => admin_url(), 'Clients' => admin_url() . '/clients', $this->data['page_title'] => '#');
            //pre_print($data);
            return view('admin.form_tpl', ['data' => $this->data, 'tbl_record' => $tbl_record]);
        }
    }

    public function chips($offset = 0)
    {
        $this->data['add_button']   = 'Add New';
        $this->data['page_title']    = 'Chips';
        $this->data['add_btn_url']  = admin_url() . '/add_chip';
        $this->data['update_btn_url'] = admin_url() . '/update_chip';
        if (isset($_GET['q']) && trim($_GET['q']) == 'mapped') {
            $this->data['column_title']    = array('Code', 'Mapped Profile', 'Username', 'Activation Date', 'Brand', 'Device', 'Status', 'Created On');
            $this->data['column_values'] = array('str_code_html', 'mapped_profile', 'username', 'activation_date', 'brand', 'device', 'user_status', 'row_datetime');
        } else {
            $this->data['column_title']    = array('Code', 'Mapped Profile', 'Activation Date', 'Brand', 'Device', 'Status', 'Created On');
            $this->data['column_values'] = array('str_code_html', 'mapped_profile', 'activation_date', 'brand', 'device', 'user_status', 'row_datetime');
        }
        $this->data['delete_click'] = "deleteTableEntry('" . encrypt('unique_codes') . "','" . encrypt('id') . "','{({row-id})}');";
        $this->data['breadcrumbs']  = array('Dashboard' => admin_url(), 'Chips' => 'javascript:;');
        $this->data['str_code'] = $this->data['email'] = $this->data['status'] = $this->data['username'] = '';

        //eql();
        $query = UniqueCode::query()->select('unique_codes.id', 'str_code as str_code_html', 'activation_date', 'brand', 'unique_codes.created_at as row_datetime', 'unique_codes.status as user_status', 'unique_codes.status', 'users.username as mapped_profile', 'unique_codes.device', 'users.username'); //->where('user_group_id', '=', '3');

        $query->leftJoin('users', 'users.id', '=', 'unique_codes.user_id');

        if (isset($_GET['str_code']) && trim($_GET['str_code']) != '') {
            $this->data['str_code'] = trim($_GET['str_code']);
            $query = $query->where('str_code', '' . $this->data['str_code'] . '');
        }

        if (isset($_GET['status']) && trim($_GET['status']) != '') {
            $this->data['status'] = trim($_GET['status']);
            $query = $query->where('unique_codes.status', '=', $this->data['status']);
        }

        if (isset($_GET['device']) && trim($_GET['device']) != '') {
            $this->data['device'] = trim($_GET['device']);
            $query = $query->where('unique_codes.device', '=', $this->data['device']);
        }

        if (isset($_GET['username']) && trim($_GET['username']) != '') {
            $this->data['username'] = trim($_GET['username']);
            $query = $query->where('users.username', '=', $this->data['username']);
        }

        if (isset($_GET['q']) && trim($_GET['q']) != '') {
            $q = trim($_GET['q']);
            if ($q == 'mapped') {
                $query = $query->where('user_id', '!=', 0);
            } elseif ($q == 'available') {
                $query = $query->where('user_id', '=', 0)->where('activated', '=', 0);
            } else if ($q == 'branded') {
                $query = $query->where('brand', '!=', NULL)->where('brand', '!=', '');
            } else if ($q == 'deactivated') {
                $query = $query->where('user_id', '=', 0)->where('activated', '=', 1);
            } else {
                $query = $query->where('user_id', '=', 0)->where('activated', '=', 0);
            }
        } else {
            $query = $query->where('user_id', '=', 0);
        }

        $query->orderBy('unique_codes.user_id', 'DESC');
        $this->data['tbl_records']     = $query->paginate(per_page());
        //pre_print($data);

        $data = page_buttons('chips');
        $this->data['ExportBpAdmins']     = 0;
        return view('admin.records_listing', ['data' => array_merge($this->data, $data)]);
    }

    public function add_chip(HttpRequest $request)
    {
        //echo decrypt($request->id);exit;
        if (isset($_POST) && !empty($_POST)) {

            if ($request->status == 1) {

                $validations['device'] = 'required|string';

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
            }

            $UniqueCode = UniqueCode::findorfail($request->tbl_id);

            $UniqueCode->brand = $request->brand;
            $UniqueCode->status = $request->status;
            $UniqueCode->device = $request->device;

            if ($request->deactivate == 1) {
                $UniqueCode->assigned_to = $UniqueCode->user_id;
                $UniqueCode->user_id = 0;
            }

            if ($request->unmapped == 1) {
                $UniqueCode->assigned_to = $UniqueCode->user_id;
                $UniqueCode->user_id = 0;
                $UniqueCode->activated = 0;
            }

            if ($request->has('reactivate_chip')) {
                if ($request->reactivate_chip == 1) {
                    $UniqueCode->user_id = 0;
                    $UniqueCode->activated = 0;
                    $UniqueCode->activation_date = NULL;
                }
            }

            $UniqueCode->updated_by = Auth::user()->id;
            $UniqueCode->updated_at = Carbon::now();
            $UniqueCode->save();

            $data['message'] = 'Record Updated Successfully.';
            $data['success'] = TRUE;
            return response($data, 201);
        } else {
            $this->data['redirect']    = admin_url() . '/chips';
            $this->data['post_url']    = admin_url() . '/add_chip';
            $this->data['page_title']  = 'Add Chip';
            $this->data['form_fields'] = array('str_code' => 'Code_text', 'brand' => 'Brand_text', 'device' => 'Device_dd', 'status' => 'Status_radio');

            $this->data['device'] = devices();
            $this->data['status'] = array('Active_1', 'Inactive_0');
            $tbl_record = array();

            $tbl_id = isset($request->id) ? decrypt($request->id) : 0;
            if ($tbl_id != '0') {
                $this->data['page_title'] = 'Update Chip';
                $this->data['tbl_id']     = $tbl_id;
                $this->data['tbl_record'] = $tbl_record = UniqueCode::findorfail($this->data['tbl_id']);

                $fields = $this->data['form_fields'];
                if ($this->data['tbl_record']->user_id != 0) {
                    // $this->data['tbl_record']->deactivate = 0;
                    // $fields['deactivate'] = 'Deactivate-Chip_radio';
                    // $this->data['deactivate'] = array('Yes_1', 'No_0');
                    $this->data['tbl_record']->unmapped = 0;
                    $fields['unmapped'] = 'Unmap-Chip?_radio';
                    $this->data['unmapped'] = array('No_0', 'Yes_1');
                }
                // $query = $query->where('user_id', '=', 0)->where('activated', '=', 1);
                if ($this->data['tbl_record']->user_id == 0 && $this->data['tbl_record']->activated == 1) {
                    $this->data['tbl_record']->reactivate_chip = 0;
                    $fields['reactivate_chip'] = 'ReActivate-Chip_radio';
                    $this->data['reactivate_chip'] = array('Yes_1', 'No_0');
                }

                $this->data['form_fields'] = $fields;
                $this->data['redirect']   = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->data['redirect'];
            }

            $this->data['breadcrumbs'] = array('Home' => admin_url(), 'Chips' => admin_url() . '/chips', $this->data['page_title'] => '#');
            //pre_print($data);
            return view('admin.form_tpl', ['data' => $this->data, 'tbl_record' => $tbl_record]);
        }
    }

    public function upload_chip_csv(HttpRequest $request)
    {
        //echo decrypt($request->id);exit;
        if (isset($_POST) && !empty($_POST)) {
            $validations['brand'] = 'required|string';

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

            $path = $request->file('str_code')->getRealPath();
            $records = array_map('str_getcsv', file($path));

            if (!count($records) > 0) {
                $data['errors'][0]['error'] = 'No data found!';
                $data['errors'][0]['field'] = 'str_code';

                $data['success'] = FALSE;
                return response($data, 201);
            }

            // Get field names from header column
            $fields = array_map('strtolower', $records[0]);

            // Remove the header column
            array_shift($records);
            $rows = [];
            foreach ($records as $record) {
                if (count($fields) != count($record)) {
                    // return 'csv_upload_invalid_data';
                    $data['errors'][0]['error'] = 'Invalid data!';
                    $data['errors'][0]['field'] = 'str_code';

                    $data['success'] = FALSE;
                    return response($data, 201);
                }

                // Decode unwanted html entities
                $record =  array_map("html_entity_decode", $record);

                // Set the field name as key
                $record = array_combine($fields, $record);

                // Get the clean data
                $rows[] = $this->clear_encoding_str($record);
            }

            foreach ($rows as $data) {
                // pre_print($data);
                $UniqueCode = UniqueCode::where('str_code', $data['codes']);
                if ($UniqueCode->count() != 0) {
                    $UniqueCode = $UniqueCode->first();
                    // $UniqueCode->updated_by = Auth::user()->id;
                    $UniqueCode->updated_at = Carbon::now();
                    $UniqueCode->brand = $request->brand;
                    $UniqueCode->save();
                }
            }

            $data['message'] = 'CSV Uploaded Successfully.';
            $data['success'] = TRUE;
            return response($data, 201);
        } else {
            $this->data['redirect']    = admin_url() . '/chips';
            $this->data['post_url']    = admin_url() . '/upload_chip_csv';
            $this->data['page_title']  = 'Upload Chips in Bulk';
            $this->data['form_fields'] = array('brand' => 'Brand_text', 'str_code' => 'Codes-(CSV)_file');

            $this->data['status'] = array('Active_1', 'Inactive_0');
            $tbl_record = array();
            $this->data['breadcrumbs'] = array('Home' => admin_url(), 'Chips' => admin_url() . '/chips', $this->data['page_title'] => '#');
            //pre_print($data);
            return view('admin.form_tpl', ['data' => $this->data, 'tbl_record' => $tbl_record]);
        }
    }

    public function export_chips(HttpRequest $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        if ($request->username != '') {
            $User = User::where('username', $request->username);
            if ($User->count() == 0) {
                $data['errors'][0]['error'] = 'Invalid Username / Brand.';
                $data['success'] = FALSE;
                return response($data, 201);
            }
        }
        $limit = isset($request->export_limit) && $request->export_limit != '' ? $request->export_limit : 100;
        $UniqueCodes = UniqueCode::select('str_code')->where('status', 0)->where('user_id', 0)->whereRaw('(brand = NULL OR brand = "")');
        $UniqueCodes = $UniqueCodes->limit($limit)->get();
        $columns = ['Codes', 'Brand', 'URL'];
        $array[] = $columns;
        foreach ($UniqueCodes as $rec) {
            $array[] = [$rec->str_code, $request->username, main_url() . '/' . $request->device . '/' . $rec->str_code];
            $UniqueCode = UniqueCode::where('str_code', $rec->str_code);
            if ($UniqueCode->count() != 0 && $request->device != '') {
                $UniqueCode = $UniqueCode->first();
                $UniqueCode->status = 1;
                $UniqueCode->device = $request->device;
                $UniqueCode->brand = $request->username;
                $UniqueCode->save();
            }
        }
        // open raw memory as file so no temp files needed, you might run out of memory though
        $f = fopen(root_dir() . 'downloads/codes.csv', 'w');
        // $f = fopen('php://output', 'w');
        // pre_print($array);
        foreach ($array as $line) {
            // generate csv lines from the inner arrays
            fputcsv($f, $line, ',');
        }
        // tell the browser it's going to be a csv file
        // header('Content-Type: application/csv; charset=UTF-8');
        // tell the browser we want to save it instead of displaying it
        // header('Content-Disposition: attachment; filename="codes.csv";');
        Session::flash('download.in.the.next.request', main_url() . '/downloads/codes.csv');
        $data['message'] = 'Export Successful.';
        $data['success'] = TRUE;
        return response($data, 201);
    }

    public function import_codes_csv(HttpRequest $request)
    {
        set_time_limit(0);
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        if (isset($_POST) && !empty($_POST)) {

            $path = $request->file('file')->getRealPath();
            $records = array_map('str_getcsv', file($path));
            // pre_print($records);
            if (!count($records) > 0) {
                $data['errors'][0]['error'] = 'No data found!';
                $data['success'] = FALSE;
                return response($data, 201);
            }

            // Get field names from header column
            $fields = array_map('strtolower', $records[0]);

            // Remove the header column
            array_shift($records);
            $rows = [];
            foreach ($records as $record) {
                if (count($fields) < 2 || count($fields) != count($record)) {
                    // return 'csv_upload_invalid_data';
                    $data['errors'][0]['error'] = 'Invalid data!';
                    $data['success'] = FALSE;
                    return response($data, 201);
                }

                // Decode unwanted html entities
                $record =  array_map("html_entity_decode", $record);

                // Set the field name as key
                $record = array_combine($fields, $record);

                // Get the clean data
                $rows[] = $this->clear_encoding_str($record);
            }

            foreach ($rows as $data) {
                // pre_print($data);
                $UniqueCode = UniqueCode::where('str_code', $data['codes']);
                if ($UniqueCode->count() != 0) {
                    $UniqueCode = $UniqueCode->first();
                    $UniqueCode->brand = $data['brand'];
                    $UniqueCode->updated_by = Auth::user()->id;
                    $UniqueCode->updated_at = Carbon::now();
                    $UniqueCode->save();
                }
            }

            $data['message'] = 'Uploaded Successfully.';
            $data['success'] = TRUE;
            return response($data, 201);
        }
    }

    public function profiles($offset = 0)
    {
        $this->data['add_button']   = 'Add New';
        $this->data['page_title']    = 'Profiles';
        $this->data['add_btn_url']  = admin_url() . '/add_profile';
        $this->data['update_btn_url'] = admin_url() . '/update_profile';
        $this->data['image_url']    = icon_url();
        $this->data['column_title']    = array('Icon', 'SVG Colored', 'Title', 'Title (de)', 'Profile Type', 'Status', 'Is Pro Feature?', 'Created On');
        $this->data['column_values'] = array('img', 'icon_svg', 'title', 'title_de', 'type', 'user_status', 'is_pro_yes_no', 'row_datetime');
        $this->data['delete_click'] = "deleteTableEntry('" . encrypt('profiles') . "','" . encrypt('id') . "','{({row-id})}');";
        $this->data['breadcrumbs']  = array('Dashboard' => admin_url(), 'Profiles' => 'javascript:;');

        $query = Profile::query()->select('profiles.id', 'profiles.title', 'profiles.icon as img', 'profiles.title_de', 'profile_code', 'profiles.created_at as row_datetime', 'profiles.status as user_status', 'is_pro as is_pro_yes_no', 'profile_types.title as type', 'icon_svg_colorized as icon_svg');
        $query->leftJoin('profile_types', 'profile_types.id', '=', 'profiles.profile_type_id');

        $query->orderBy('id', 'DESC');
        $this->data['tbl_records']     = $query->paginate(per_page());

        $data = page_buttons('profiles');
        $this->data['ExportBpAdmins']     = 0;
        //pre_print($this->data);
        return view('admin.records_listing', ['data' => array_merge($this->data, $data)]);
    }

    public function platforms($offset = 0)
    {
        $this->data['add_button']   = 'Add New';
        $this->data['page_title']    = 'Platforms';
        $this->data['add_btn_url']  = admin_url() . '/add_platform';
        $this->data['update_btn_url'] = admin_url() . '/update_platform';
        $this->data['image_url']    = icon_url();
        $this->data['column_title']    = array('Name', 'Title', 'Icon SVG', 'description', 'Created On');
        $this->data['column_values'] = array('name', 'title', 'icon', 'description', 'row_datetime');
        $this->data['delete_click'] = "deleteTableEntry('" . encrypt('platforms') . "','" . encrypt('id') . "','{({row-id})}');";
        $this->data['breadcrumbs']  = array('Dashboard' => admin_url(), 'platforms' => 'javascript:;');

        $query = Platform::query()->select('platforms.id', 'platforms.name', 'platforms.icon', 'platforms.title', 'platforms.description', 'platforms.created_at as row_datetime');


        $query->orderBy('id', 'ASC');
        $this->data['tbl_records']     = $query->paginate(per_page());

        $data = page_buttons('profiles');
        //pre_print($this->data);
        return view('admin.platforms_listing', ['data' => array_merge($this->data, $data)]);
    }

    public function add_platform(HttpRequest $request)
    {

        //echo decrypt($request->id);exit;
        if (isset($_POST) && !empty($_POST)) {
            $validations['name'] = 'required|string|unique:platforms,name,'.$request->tbl_id;
            $validations['icon'] = 'required|string';
            $validations['title'] = 'required|string';
            $validations['description'] = 'required|string';

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

                $platform = new Platform;
            } else {
                $platform = Platform::findorfail($request->tbl_id);
            }

            $platform->name = $request->name;
            $platform->icon = $request->icon;
            $platform->title = $request->title;
            $platform->description = $request->description;
            $platform->status = $request->status;
            $platform->save();
            $data['message'] = 'Record Updated Successfully.';
            $data['success'] = TRUE;
            return response($data, 201);
        } else {
            $this->data['name'][] = (object)array('id' => 'hubspot', 'title' => 'Hubspot');
            $this->data['name'][] = (object)array('id' => 'azure-ad', 'title' => 'Active Directory');
            //email//address//file
            $this->data['redirect']    = admin_url() . '/platforms';
            $this->data['post_url']    = admin_url() . '/add_platform';
            $this->data['page_title']  = 'Add Platforms';
            $this->data['form_fields'] = array(
                'name' => 'Name_dd', 'title' => 'Title_text', 'icon' => 'Icon_textarea',
                'description' => 'Description_textarea', 'status' => 'Status_radio'
            );
            $this->data['status'] = array('Active_1', 'Inactive_0');
            $tbl_record = array();
            $this->data['breadcrumbs'] = array('Home' => admin_url(), 'Platforms' => admin_url() . '/platforms', 'Add Platforms' => '#');
            // $this->data['profile_type_id'] = ProfileType::get();
            $tbl_id = isset($request->id) ? decrypt($request->id) : 0;
            if ($tbl_id != '0') {
                $this->data['page_title'] = 'Update Platforms';
                $this->data['tbl_id']     = $this->data['user_id'] = $tbl_id;
                $this->data['tbl_record'] = $tbl_record = Platform::findorfail($this->data['tbl_id']);

                $this->data['redirect']   = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->data['redirect'];
                $this->data['breadcrumbs'] = array('Home' => admin_url(), 'Platforms' => admin_url() . '/profiles', 'Update Profile' => '#');
            }
            //pre_print($this->data);
            return view('admin.form_platform', ['data' => $this->data, 'tbl_record' => $tbl_record]);
        }
    }

    public function add_profile(HttpRequest $request)
    {


        //echo decrypt($request->id);exit;
        if (isset($_POST) && !empty($_POST)) {
            $validations['title'] = 'required|string';
            $validations['profile_type_id'] = 'required|string';

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

                $profile = new Profile;
                $profile->created_by = Auth::user()->id;
                $profile->created_at = Carbon::now();
            } else {
                $profile = Profile::findorfail($request->tbl_id);
                $profile->updated_by = Auth::user()->id;
                $profile->updated_at = Carbon::now();
            }

            $upload_dir = icon_dir();
            $response = upload_file($request, 'icon', $upload_dir);
            if ($response['success'] == FALSE) {
                $data['success'] = $response['success'];
                $data['message'] = $response['message'];
                $data['data'] = [];
                return response()->json($data, 201);
            }

            $profile->profile_type_id = $request->profile_type_id;
            $profile->title = $request->title;
            $profile->title_de = $request->title_de;
            $profile->profile_code = $request->profile_code;
            $profile->icon_svg_default = $request->icon_svg_default;
            $profile->icon_svg_colorized = $request->icon_svg_colorized;
            $profile->type = $request->type;
            $profile->base_url = ($request->type != 'url') ? $request->base_url : NULL;
            $profile->status = $request->status;
            $profile->is_pro = $request->is_pro ?? 0;
            $profile->is_pet = $request->is_pet ?? 0;
            $profile->is_sos = $request->is_sos ?? 0;
            $profile->is_personal = $request->is_personal ?? 0;
            $profile->is_ch_business = $request->is_ch_business ?? 0;
            if ($response['filename'] != '') {
                $profile->icon = $response['filename'];
            }
            $profile->save();

            $data['message'] = 'Record Updated Successfully.';
            $data['success'] = TRUE;
            return response($data, 201);
        } else {
            $this->data['redirect']    = admin_url() . '/profiles';
            $this->data['post_url']    = admin_url() . '/add_profile';
            $this->data['page_title']  = 'Add Profile';
            $this->data['form_fields'] = array('profile_type_id' => 'Profile-Type_dd', 'title' => 'Title_text', 'title_de' => 'Title-(de)_text', 'profile_code' => 'Code_text', 'type' => 'Type_dd', 'base_url' => 'Base-URL_text', 'icon' => 'Icon_file', 'status' => 'Status_radio', 'icon_svg_default' => 'SVG-Icon-Default_textarea',
             'icon_svg_colorized' => 'SVG-Icon-Colorized_textarea', 'is_pro' => 'Is-Pro-Feature?_radio',
             'is_pet' => 'IS Pet_checkbox',
             'is_sos' => 'IS SOS_checkbox',
             'is_personal' => 'IS Personal_checkbox',
             'is_ch_business' => 'IS Business_checkbox'

            );

            $this->data['status'] = array('Active_1', 'Inactive_0');
            $this->data['is_pro'] = array('Yes_1', 'No_0');

            $this->data['type'][] = (object)array('id' => 'url', 'title' => 'URL');
            $this->data['type'][] = (object)array('id' => 'username', 'title' => 'Username');
            $this->data['type'][] = (object)array('id' => 'number', 'title' => 'Number');
            $this->data['type'][] = (object)array('id' => 'other', 'title' => 'Other'); //email//address//file

            $tbl_record = array();
            $this->data['breadcrumbs'] = array('Home' => admin_url(), 'Profiles' => admin_url() . '/profiles', 'Add Profile' => '#');
            $this->data['profile_type_id'] = ProfileType::get();

            $tbl_id = isset($request->id) ? decrypt($request->id) : 0;
            if ($tbl_id != '0') {
                $this->data['page_title'] = 'Update Profile';
                $this->data['tbl_id']     = $this->data['user_id'] = $tbl_id;
                $this->data['tbl_record'] = $tbl_record = Profile::findorfail($this->data['tbl_id']);

                $this->data['redirect']   = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->data['redirect'];
                $this->data['breadcrumbs'] = array('Home' => admin_url(), 'Profiles' => admin_url() . '/profiles', 'Update Profile' => '#');
            }
            //pre_print($this->data);
            return view('admin.form_tpl', ['data' => $this->data, 'tbl_record' => $tbl_record]);
        }
    }

    public function user_groups($offset = 0)
    {

        $this->data['add_button']    = 'Add New';
        $this->data['page_title']     = 'User Groups';
        $this->data['add_btn_url']   = admin_url() . '/add_user_group';
        $this->data['update_btn_url'] = admin_url() . '/update_user_group';
        $this->data['column_title']     = array('Title', 'Created On');
        $this->data['column_values'] = array('title', 'row_datetime');
        $this->data['delete_click']  = "deleteTableEntry('" . encrypt('user_groups') . "','" . encrypt('id') . "','{({row-id})}');";
        $this->data['breadcrumbs']   = array('Dashboard' => admin_url(), 'User Groups' => 'javascript:;');
        $this->data['custom']          = '<a class="btn btn-success" href="' . admin_url() . '/permissions/{({row-id})}"><i class="fa fa-eye"></i> Permissions</a>';

        $query = UserGroups::query()->select('id', 'title', 'created_at as row_datetime')->where('id', '<>', 1)->orderBy('id', 'DESC');
        $this->data['tbl_records']      = $query->paginate(per_page());
        //pre_print($data);

        $data = page_buttons('user_groups');
        $this->data['ExportBpAdmins']     = 0;
        return view('admin.records_listing', ['data' => array_merge($this->data, $data)]);
    }

    public function add_user_group(HttpRequest $request)
    {
        //echo decrypt($request->id);exit;
        if (isset($_POST) && !empty($_POST)) {
            $validations['title'] = 'required|string';
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

                $user_group = new UserGroups;
                $user_group->permissions = '-1';
                $user_group->created_by = Auth::user()->id;
                $user_group->created_at = Carbon::now();
            } else {
                $user_group = UserGroups::findorfail($request->tbl_id);
            }

            $user_group->title = $request->title;
            $user_group->updated_by = Auth::user()->id;
            $user_group->updated_at = Carbon::now();
            $user_group->save();

            $data['message'] = 'Record Updated Successfully.';
            $data['success'] = TRUE;
            return response($data, 201);
        } else {
            $this->data['redirect']    = admin_url() . '/user_groups';
            $this->data['post_url']    = admin_url() . '/add_user_group';
            $this->data['page_title']  = 'Add User Group';
            $this->data['form_fields'] = array('title' => 'Title_text');

            $tbl_record = array();

            $tbl_id = isset($request->id) ? decrypt($request->id) : 0;
            if ($tbl_id != '0') {
                $this->data['page_title'] = 'Update User Group';
                $this->data['tbl_id']     = $tbl_id;
                $this->data['tbl_record'] = $tbl_record = UserGroups::findorfail($this->data['tbl_id']);
                $this->data['redirect']   = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->data['redirect'];
            }

            $this->data['breadcrumbs'] = array('Home' => admin_url(), 'User Groups' => admin_url() . '/user_groups', 'Add User Group' => '#');
            //pre_print($data);
            return view('admin.form_tpl', ['data' => $this->data, 'tbl_record' => $tbl_record]);
        }
    }

    public function permissions(HttpRequest $request)
    {

        if (isset($_POST) && !empty($_POST)) {

            $user_group = UserGroups::findorfail($request->user_group_id);
            $user_group->permissions = !empty($request->chk) ? trim(implode(',', $request->chk)) : 0;
            $user_group->updated_by = Auth::user()->id;
            $user_group->updated_at = Carbon::now();
            $user_group->save();

            $data['message'] = 'Permissions Assigned Successfully.';
            $data['success'] = TRUE;
            return response($data, 201);
        } else {
            $this->data['page_title']    = 'Permissions';
            $this->data['breadcrumbs']  = array('Dashboard' => admin_url(), 'Permissions' => 'javascript:;');

            $this->data['tbl_record']     = UserGroups::findorfail(decrypt($request->id));
            $this->data['menus']         = Menu::query()->orderBy('sort', 'ASC')->get();
            $this->data['user_group_id'] = !empty($this->data['tbl_record']) ? $this->data['tbl_record']->id : '0';
            $this->data['menu_ids']        = !empty($this->data['tbl_record']) ? explode(',', $this->data['tbl_record']->permissions) : array();
            //pre_print($this->data);
            return view('admin.permissions', ['data' => $this->data]);
        }
    }

    public function profile(HttpRequest $request)
    {
        //echo decrypt($request->id);exit;
        if (isset($_POST) && !empty($_POST)) {
            $validations['name'] = 'required|string';
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

            $user = User::findorfail($request->tbl_id);
            $user->name = $request->name;
            $user->gender = $request->gender;
            $user->updated_by = Auth::user()->id;
            $user->updated_at = Carbon::now();

            if (isset($request->first_name)) {
                $user->first_name = $request->first_name;
            }

            if (isset($request->last_name)) {
                $user->last_name = $request->last_name;
            }

            if (isset($request->designation)) {
                $user->designation = $request->designation;
            }

            if (isset($request->company_name)) {
                $user->company_name = $request->company_name;
            }

            $user->save();

            $request->session()->flash('success_msg', 'Profile Updated Successfully!');
            $data['message'] = 'Profile Updated Successfully.';
            $data['success'] = TRUE;
            return response($data, 201);
        } else {
            $this->data['redirect']        = admin_url() . '/profile';
            $this->data['post_url']        = admin_url() . '/profile';
            $this->data['password_url']    = admin_url() . '/change_password ';
            $this->data['page_title']    = 'Update Profile';

            $this->data['tbl_id']         = Auth::user()->id;;
            $this->data['tbl_record']     = $tbl_record = User::findorfail($this->data['tbl_id']);
            $this->data['breadcrumbs']    = array('Home' => admin_url(), 'Profile' => '#');
            //pre_print($this->data);
            return view('admin.profile', ['data' => $this->data, 'tbl_record' => $tbl_record]);
        }
    }

    public function change_password(HttpRequest $request)
    {
        //echo decrypt($request->id);exit;
        $validations['current_password'] = 'required|string';
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
            return response($data, 201);
        }

        $user = User::findorfail($request->tbl_id);
        if (Hash::check($request->current_password, $user->password)) {
            $user->password = bcrypt($request->password);
            $user->updated_by = Auth::user()->id;
            $user->updated_at = Carbon::now();
            $user->save();
        } else {
            $data['errors'][0]['error'] = 'Current password doesn\'t seem correct.';
            $data['success'] = FALSE;
            return response($data, 201);
        }

        $data['message'] = 'Password Changed Successfully.';
        $request->session()->flash('success_msg', 'Password Changed Successfully!');
        $data['success'] = TRUE;
        return response($data, 201);
    }

    public function delete(HttpRequest $request)
    {
        $table = decrypt($request->idx);
        $colummn = decrypt($request->r);
        $id = decrypt($request->id);
        //echo $table;exit;

        $record = DB::table($table)->where($colummn, $id)->first();


        if ($table == 'users') {

            $User = User::findorfail($id);

            $obj = new DeleteAccount;
            $obj->user_id = $User->id;
            $obj->reason = "Deleted by Admin";
            $obj->details = "N/A";
            $obj->name = $User->first_name . ' ' . $User->last_name;
            $obj->created_by = Auth::user()->id;
            $obj->save();

            $User->delete();

            UniqueCode::where("user_id", $id)->update(["activated" => 0, "user_id" => 0, "updated_by" => Auth::user()->id]);
            BusinessInfo::where('user_id', $id)->delete();
            ContactCard::where('user_id', $id)->delete();
            CustomerProfile::where('user_id', $id)->delete();
            TapsViews::where('user_id', $id)->delete();
            UserNote::where('user_id', $id)->delete();
        } else {
            DB::table($table)->where($colummn, $id)->delete();
        }

        echo 1;
    }

    public function delete_business_customer(HttpRequest $request)
    {
        if (isset($_POST) && !empty($_POST)) {
            if ($request->confirm == 1) {
                $id = $request->tbl_id;
                $User = User::findorfail($id);

                $obj = new DeleteAccount;
                $obj->user_id = $User->id;
                $obj->reason = "Deleted by Admin";
                $obj->details = "N/A";
                $obj->name = $User->first_name . ' ' . $User->last_name;
                $obj->created_by = Auth::user()->id;
                $obj->save();

                $childUsers = BusinessUser::where('parent_id', $id)->get();
                foreach ($childUsers as $childUser) {
                    $childUser->delete();
                    $childUserId = $childUser->user_id;

                    $childUserAccount = User::findorfail($childUserId);

                    $obj = new DeleteAccount;
                    $obj->user_id = $childUserAccount->id;
                    $obj->reason = "Deleted by Admin along with BP Admin";
                    $obj->details = "N/A";
                    $obj->name = $User->first_name . ' ' . $User->last_name;
                    $obj->created_by = Auth::user()->id;
                    $obj->save();

                    $childUserAccount->delete();

                    UniqueCode::where("user_id", $childUserId)->update(["activated" => 0, "user_id" => 0, "updated_by" => Auth::user()->id]);
                    BusinessInfo::where('user_id', $childUserId)->delete();
                    ContactCard::where('user_id', $childUserId)->delete();
                    CustomerProfile::where('user_id', $childUserId)->delete();
                    TapsViews::where('user_id', $childUserId)->delete();
                    UserNote::where('user_id', $childUserId)->delete();
                    TemplateAssignee::where('user_id', $childUserId)->delete();
                }

                $User->delete();

                UniqueCode::where("user_id", $id)->update(["activated" => 0, "user_id" => 0, "updated_by" => Auth::user()->id]);
                BusinessUser::where('user_id', $id)->delete();
                BusinessUser::where('parent_id', $id)->delete();
                BusinessInfo::where('user_id', $id)->delete();
                ContactCard::where('user_id', $id)->delete();
                CustomerProfile::where('user_id', $id)->delete();
                UserTemplate::where('user_id', $id)->delete();
                TapsViews::where('user_id', $id)->delete();
                UserNote::where('user_id', $id)->delete();
                TemplateAssignee::where('user_id', $id)->delete();

                $data['message'] = 'Account data deleted successfully.';
                $request->session()->flash('success_msg', 'Account data deleted successfully.');
                $data['success'] = TRUE;
                return response($data, 201);
            } else {
                $request->session()->flash('warning_msg', 'Account deletion has been skipped.');
                $data['message'] = 'Account deletion has been skipped.';
                $data['success'] = TRUE;
                return response($data, 201);
            }
        } else {

            $this->data['redirect']    = admin_url() . '/business_customers';
            $this->data['post_url']    = admin_url() . '/delete_business_customer';

            $this->data['form_fields'] = array('status' => 'All-users-accounts-attached-to-this-business-admin-will-also-be-deleted.-Are-you-sure-to-delete-this?_radio');
            $this->data['status'] = array('Yes_1', 'No_0');
            $this->data['tbl_record'] = $tbl_record = User::findorfail(decrypt($request->id));
            $this->data['page_title'] = 'Delete Business Admin';
            $this->data['breadcrumbs'] = array('Home' => admin_url(), 'Business Customers' => admin_url() . '/business_customers', $this->data['page_title'] => '#');
            //pre_print($data);
            return view('admin.delete_business_admin', ['data' => $this->data, 'tbl_record' => $tbl_record]);
        }
    }

    public function download_csv(HttpRequest $request)
    {
        /* SET SESSION group_concat_max_len = 1000000;
			select u.id,name,email as registration_email,username,bio,dob,gender,device_id,platform, group_concat(concat(cp.profile_code,'::',cp.is_business,'::',cp.profile_link) SEPARATOR '##') as profile
			from users u
			left join customer_profiles cp on cp.user_id = u.id and cp.profile_code != 'contact-card'
			where u.user_group_id = 2
			group by u.id  */
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        // $id = 102;
        // $Users = User::select('id','name','email','username','dob','gender','device_id','platform')->where('user_group_id',2)->get();
        $Users = json_decode(file_get_contents('users.json'));
        //pre_print($Users);
        $Profiles = Profile::select('profile_code', 'title')->where('profile_code', '!=', 'contact-card')->get();
        $columns = ['Name', 'Registration Email', 'Username', 'Date of Birth', 'Gender', 'Device ID'];
        $gender = array(1 => 'Male', 2 => 'Female', 3 => 'Unknown', 4 => 'Custom');

        if (!empty($Profiles)) {
            foreach ($Profiles as $rec) {
                $columns[] = $rec->title;
                $columns[] = $rec->title . ' (Business)';
            }
        }

        $array[] = $columns;
        foreach ($Users as $User) {
            $CustomerProfile = explode('##', trim($User->profile));
            // if($User->id == 2075){
            // 	pre_print($CustomerProfile);
            // }else{
            // 	continue;
            // }
            // pre_print($CustomerProfile);
            // $CustomerProfile = CustomerProfile::select('profile_code','profile_link')->where('user_id',$User->id)->where('profile_code','!=', 'contact-card')->get();
            $User->gender = isset($gender[$User->gender]) ? $gender[$User->gender] : 'Unknown';

            if (!empty($Profiles)) {
                foreach ($Profiles as $rec) {
                    $idx = str_replace('-', '_', $rec->profile_code);
                    $User->$idx = '';
                    $idx = str_replace('-', '_', $rec->profile_code . '_business');
                    $User->$idx = '';
                }
            }

            if (!empty($CustomerProfile)) {
                foreach ($CustomerProfile as $rec) {
                    $str = explode('::', $rec);
                    //
                    $profile_code = isset($str[0]) ? $str[0] : '';
                    $is_business = isset($str[1]) ? $str[1] : '';
                    $profile_link = isset($str[2]) ? $str[2] : '';

                    if ($profile_code != '') {
                        $idx = str_replace('#', '', str_replace('-', '_', $profile_code));
                        if ($User->$idx == '') {
                            $User->$idx = ($is_business == 0 ? $profile_link . ";" : '');
                        } else {
                            $User->$idx .= ($is_business == 0 ? $profile_link . ";" : '');
                        }

                        $User->$idx = trim(trim(trim($User->$idx), ';'));

                        $idx = str_replace('#', '', str_replace('-', '_', $profile_code . '_business'));
                        if ($User->$idx == '') {
                            $User->$idx = ($is_business == 1 ? $profile_link . ";" : '');
                        } else {
                            $User->$idx .= ($is_business == 1 ? $profile_link . ";" : '');
                        }

                        $User->$idx = trim(trim(trim($User->$idx), ';'));
                    }
                }
            }
            // pre_print(json_decode(json_encode($columns)));
            // if($User->id == 2075){

            // }else{
            // 	continue;
            // }
            unset($User->profile, $User->id, $User->bio);
            $array[] = json_decode(json_encode($User), true); //['bilal','mbilal.pg','103339325261'];
        }
        // pre_print($array);
        // open raw memory as file so no temp files needed, you might run out of memory though
        $f = fopen(root_dir() . 'users.csv', 'w');
        // loop over the input array
        foreach ($array as $line) {
            // generate csv lines from the inner arrays
            fputcsv($f, $line, ',');
        }


        // tell the browser it's going to be a csv file
        //header('Content-Type: application/csv; charset=UTF-8');
        // tell the browser we want to save it instead of displaying it
        //header('Content-Disposition: attachment; filename="users.csv";');
    }

    public function feedbacks($offset = 0)
    {
        $this->data['add_button']   = 'Add New';
        $this->data['page_title']    = 'Feedbacks';
        $this->data['add_btn_url']  = admin_url() . '/add_feedback';
        $this->data['update_btn_url'] = admin_url() . '/update_feedback';
        $this->data['column_title']    = array('Name', 'Subject', 'Details', 'Date');
        $this->data['column_values'] = array('full_name', 'subject', 'details', 'row_datetime');
        $this->data['delete_click'] = "deleteTableEntry('" . encrypt('feedback') . "','" . encrypt('id') . "','{({row-id})}');";
        $this->data['breadcrumbs']  = array('Dashboard' => admin_url(), 'Feedbacks' => 'javascript:;');
        $this->data['from_date'] = $this->data['to_date'] = $this->data['full_name'] = '';
        $from_date = '2020-01-01';
        $from_time = '00:00:00';
        $to_date = date('Y-m-d');
        $to_time = '23:59:59'; //date('H:i:s');
        //eql();
        $query = Feedback::query()->select('id', 'full_name', 'subject', 'details', 'created_at as row_datetime');

        if (isset($_GET['full_name']) && trim($_GET['full_name']) != '') {
            $this->data['full_name'] = trim($_GET['full_name']);
            $query = $query->where('full_name', 'like', '%' . $this->data['full_name'] . '%');
        }

        if (isset($_GET['q']) && trim($_GET['q']) != '') {
            $q = trim($_GET['q']);
            if ($q == 'mapped') {
                $query = $query->where('user_id', '!=', 0);
            }
        } else {
            $query = $query->where('user_id', '!=', 0);
        }

        if (isset($_GET['from_date']) && trim($_GET['from_date']) != '') {
            $from_date = $_GET['from_date'];
            $this->data['from_date'] = $from_date;
        }

        if (isset($_GET['from_time']) && trim($_GET['from_time']) != '') {
            $from_time = $_GET['from_time'];
            $this->data['from_time'] = $from_time;
        }

        if (isset($_GET['to_date']) && trim($_GET['to_date']) != '') {
            $to_date = $_GET['to_date'];
            $this->data['to_date'] = $to_date;
        }

        if (isset($_GET['to_time']) && trim($_GET['to_time']) != '') {
            $to_time = $_GET['to_time'];
            $this->data['to_time'] = $to_time;
        }

        if (isset($_GET['per_page']) && trim($_GET['per_page']) != '') {
            $per_page = $_GET['per_page'];
            $this->data['per_page'] = $per_page;
        }

        $from = $from_date . ' ' . $from_time;
        $to = $to_date . ' ' . $to_time;

        $query->whereBetween('created_at', [$from, $to])->orderBy('id', 'DESC');
        $this->data['tbl_records'] = $query->paginate(per_page());
        //pre_print($data);

        $data = page_buttons('feedbacks');
        $this->data['ExportBpAdmins']     = 0;
        return view('admin.records_listing', ['data' => array_merge($this->data, $data)]);
    }

    public function notifications($offset = 0)
    {
        $this->data['add_button']   = 'Add New';
        $this->data['page_title']    = 'Notifications';
        $this->data['add_btn_url']  = admin_url() . '/add_notification';
        $this->data['update_btn_url'] = admin_url() . '/update_notification';
        $this->data['column_title']    = array('Name', 'Status', 'Created On');
        $this->data['column_values'] = array('name', 'user_status', 'row_datetime');
        $this->data['delete_click'] = "deleteTableEntry('" . encrypt('users') . "','" . encrypt('id') . "','{({row-id})}');";
        $this->data['breadcrumbs']  = array('Dashboard' => admin_url(), 'Notifications' => 'javascript:;');
        $this->data['name'] = $this->data['email'] = $this->data['status'] = $this->data['username'] = '';

        //eql();
        $query = User::query()->select('users.id', 'name', 'users.created_at as row_datetime', 'status as user_status')->where('user_group_id', '=', '3')->orderBy('id', 'DESC');
        $this->data['tbl_records']     = $query->paginate(per_page());
        //pre_print($data);

        $data = page_buttons('notifications');
        $this->data['ExportBpAdmins']     = 0;
        return view('admin.records_listing', ['data' => array_merge($this->data, $data)]);
    }

    public function add_notification(HttpRequest $request)
    {
        if (isset($_POST) && !empty($_POST)) {
            $validations['message'] = 'required|string';

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

            $fields['included_segments'] = ['Subscribed Users'];
            $message = $request->message;

            $notificationID = OneSignal::sendPush($fields, $message);

            $info['type'] = 'notification';
            $info['type_id'] = 0;
            $info['details'] = json_encode(['message' => $message, 'id' => $notificationID]);
            $info['device_id'] = isset($request->device_id) ? $request->device_id : 0;
            $info['ip_address'] = getUserIP();
            $info['created_by'] = Auth::user()->id;
            add_activity($info);
            // echo $notificationID["id"];

            $data['message'] = 'Notification Sent Successfully.';
            $data['success'] = TRUE;
            return response($data, 201);
        } else {
            $this->data['redirect']    = admin_url() . '/add_notification';
            $this->data['post_url']    = admin_url() . '/add_notification';
            $this->data['page_title']  = 'Send Notification';
            $this->data['form_fields'] = array('message' => 'Message_textarea');

            $tbl_record = array();
            $this->data['breadcrumbs'] = array('Home' => admin_url(), $this->data['page_title'] => '#');
            //pre_print($data);
            return view('admin.form_tpl', ['data' => $this->data, 'tbl_record' => $tbl_record]);
        }
    }

    public function business_requests($offset = 0)
    {
        $this->data['add_button']   = 'Add New';
        $this->data['page_title']    = 'Business Request';
        $this->data['add_btn_url']  = admin_url() . '/add_business_request';
        $this->data['update_btn_url'] = admin_url() . '/update_business_request';
        $this->data['column_title']    = array('Name', 'Email', 'Phone No.', 'Company', 'Message', 'Date');
        $this->data['column_values'] = array('full_name', 'email', 'phone_no', 'company', 'message', 'row_datetime');
        $this->data['delete_click'] = "deleteTableEntry('" . encrypt('business_requests') . "','" . encrypt('id') . "','{({row-id})}');";
        $this->data['breadcrumbs']  = array('Dashboard' => admin_url(), 'Business Requests' => 'javascript:;');
        $this->data['from_date'] = $this->data['to_date'] = $this->data['email'] = '';
        $from_date = '2020-01-01';
        $from_time = '00:00:00';
        $to_date = date('Y-m-d');
        $to_time = '23:59:59'; //date('H:i:s');
        //eql();
        $query = BusinessRequest::query()->select('id', DB::raw('CONCAT(last_name, " " ,first_name) AS full_name'), 'email', 'phone_no', 'message', 'company', 'created_at as row_datetime');

        if (isset($_GET['email']) && trim($_GET['email']) != '') {
            $this->data['email'] = trim($_GET['email']);
            $query = $query->where('email', 'like', '%' . $this->data['email'] . '%');
        }

        if (isset($_GET['from_date']) && trim($_GET['from_date']) != '') {
            $from_date = $_GET['from_date'];
            $this->data['from_date'] = $from_date;
        }

        if (isset($_GET['from_time']) && trim($_GET['from_time']) != '') {
            $from_time = $_GET['from_time'];
            $this->data['from_time'] = $from_time;
        }

        if (isset($_GET['to_date']) && trim($_GET['to_date']) != '') {
            $to_date = $_GET['to_date'];
            $this->data['to_date'] = $to_date;
        }

        if (isset($_GET['to_time']) && trim($_GET['to_time']) != '') {
            $to_time = $_GET['to_time'];
            $this->data['to_time'] = $to_time;
        }

        if (isset($_GET['per_page']) && trim($_GET['per_page']) != '') {
            $per_page = $_GET['per_page'];
            $this->data['per_page'] = $per_page;
        }

        $from = $from_date . ' ' . $from_time;
        $to = $to_date . ' ' . $to_time;

        $query->whereBetween('created_at', [$from, $to])->orderBy('id', 'DESC');
        $this->data['tbl_records'] = $query->paginate(per_page());
        //pre_print($data);

        $data = page_buttons('business_requests');
        $this->data['ExportBpAdmins']     = 0;
        return view('admin.records_listing', ['data' => array_merge($this->data, $data)]);
    }

    public function logout(HttpRequest $request)
    {
        auth()->logout();
        $request->session()->flush();

        return redirect('/admin-dashboard');
    }

    private function clear_encoding_str($value)
    {
        if (is_array($value)) {
            $clean = [];
            foreach ($value as $key => $val) {
                $key = str_replace(' ', '-', mb_convert_encoding(trim($key, '﻿'), 'UTF-8'));
                $clean[$key] = mb_convert_encoding(mb_convert_encoding($val, 'UTF-8'), 'UTF-8', 'UTF-8');
            }
            return $clean;
        }
        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }
    public function exportBpAdmins()
    {
        return Excel::download(new UsersExport, 'BpAdmins.xlsx');
    }
}
