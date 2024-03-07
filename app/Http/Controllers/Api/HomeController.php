<?php

/** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hash;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\Profile;
use App\Models\ProfileType;
use App\Models\CustomerProfile;
use App\Models\BusinessInfo;
use App\Models\BusinessRequest;
use App\Models\BusinessUser;
use App\Models\ContactCard;
use App\Models\UserNote;
use App\Models\TapsViews;
use App\Models\DeleteAccount;
use App\Models\Feedback;
use App\Models\Subscriptions;
use App\Models\UniqueCode;
use App\Models\UserSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PlatformIntegration;
use App\Models\Platform;
use Exception;
use DateTime;
use DateTimeZone;
use Mail;
use \Mailjet\Resources;
use PKPass\PKPass;


class HomeController extends Controller
{
    public function profile_types(Request $request)
    {
        $token = $request->user();
        $is_business = $token->profile_view == 'business' ? 1 : 0;
        $has_subscription = chk_subscription($token);

        if ($has_subscription['success'] == false) {
            $sql = 'is_custom = 0';
        } else {
            $sql = '(is_custom = 0 OR p.created_by = ' . $token->id . ')';
        }

        $query = \DB::table('profiles AS p')->select('p.id', 'p.title', 'title_de', 'profile_code', 'is_pro', 'icon', 'p.profile_type_id', 'pt.title as profile_type', 'p.type as hint', 'p.base_url')->leftJoin('profile_types AS pt', 'pt.id', '=', 'p.profile_type_id')->where('p.status', 1)->whereRaw($sql);
        $profiles = $query->get();
        foreach ($profiles as $profile) {
            $profile->icon = icon_url() . $profile->icon;
        }

        $data['success'] = TRUE;
        $data['message'] = 'Profile Types';
        $data['data'] = array('profiles' => $profiles);
        return response()->json($data, 201);
    }

    public function member_profile_types(Request $request)
    {
        $token = $request->user();
        // $is_business = $token->profile_view == 'business' ? 1 : 0;
        $has_subscription = chk_subscription($token);

        if ($has_subscription['success'] == false) {
            $sql = 'is_custom = 0';
        } else {
            $sql = '(is_custom = 0 OR p.created_by = ' . $token->id . ')';
        }

        $query = \DB::table('profiles AS p')->select('p.id', 'p.title', 'title_de', 'profile_code', 'is_pro', 'icon', 'p.profile_type_id', 'pt.title as profile_type', 'p.type as hint', 'p.base_url', 'pt.id as category_id', 'icon_svg_default', 'icon_svg_colorized')->leftJoin('profile_types AS pt', 'pt.id', '=', 'p.profile_type_id')->where('p.status', 1)->where('pt.status', 1)->whereRaw($sql);
        $profiles = $query->get();
        $link_types = [];
        foreach ($profiles as $profile) {
            // $profile->icon = icon_url() . $profile->icon;
            $link_type['id'] = $profile->id;
            $link_type['code'] = $profile->profile_code;
            $link_type['icon_url'] = icon_url() . $profile->icon;
            $link_type['icon_svg_default'] = $profile->icon_svg_default;
            $link_type['icon_svg_colorized'] = $profile->icon_svg_colorized;
            $link_type['title'] = $profile->title;
            $link_type['value_type'] = $profile->profile_type;
            $link_type['base_url'] = $profile->base_url;
            $link_type['category_id'] = $profile->category_id;
            $link_type['is_pro'] = $profile->is_pro;

            $link_types[] = $link_type;
        }

        $link_categories = ProfileType::select('id', 'title')->where('status', 1)->get();

        $data['success'] = TRUE;
        $data['message'] = 'Found ' . count($link_types) . ' Link-Types under ' . count($link_categories) . ' Link-Categories.';
        $data['data'] = array('link_types' => $link_types, 'link_categories' => $link_categories);
        return response()->json($data, 201);
    }

    //list profiles
    public function list_profiles(Request $request)
    {
        $token = $request->user();
        $is_business = $token->profile_view == 'business' ? 1 : 0;
        $has_subscription = chk_subscription($token);

        if ($has_subscription['success'] == false) {
            $sql = 'is_custom = 0';
        } else {
            $sql = '(is_custom = 0 OR p.created_by = ' . $token->id . ')';
        }

        $query = \DB::table('profiles AS p')->select(
            'p.id',
            'p.title',
            'title_de',
            'profile_code',
            'is_pro',
            'icon',
            'p.profile_type_id',
            'pt.title as profile_type',
            'p.type as hint',
            'p.base_url',
            'p.is_pet',
            'p.is_pet',
            'p.is_personal',
            'p.is_ch_business'
        )
            ->leftJoin('profile_types AS pt', 'pt.id', '=', 'p.profile_type_id')->where('p.status', 1)->whereRaw($sql);
        $profiles = $query->get();
        $userGroupId = $token->user_group_id;

        if ($userGroupId == 7) {
            $profiles = $query->where('p.is_pet', 1)->get();
        } elseif ($userGroupId == 8) {
            $profiles = $query->where('p.is_sos', 1)->get();
        } elseif ($userGroupId == 9) {
            $profiles = $query->where('p.is_personal', 1)->get();
        } elseif ($userGroupId == 10) {
            $profiles = $query->where('p.is_ch_business', 1)->get();
        } else {
            $profiles = $query->get();
        }
        if (count($profiles) != 0) {
            foreach ($profiles as $profile) {
                if ($profile->profile_code == 'contact-card') {
                    $ContactCard = ContactCard::where('user_id', $token->id)->where('is_business', $is_business);
                    $profile->is_added = $ContactCard->count();
                    $profile->icon = icon_url() . $profile->icon;

                    $ContactCard = $ContactCard->first();
                    $customer_profile_ids = isset($ContactCard->customer_profile_ids) ? $ContactCard->customer_profile_ids : 0;
                    $CustomerProfile = CustomerProfile::whereIn('id', explode(',', $customer_profile_ids));
                    if ($CustomerProfile->count() == 0) {
                        $profile->is_added = 0;
                    }
                } else {
                    $Obj = CustomerProfile::where('profile_code', $profile->profile_code)->where('user_id', $token->id)->where('is_business', $is_business);
                    // update profile object icon and total times added
                    $profile->is_added = $Obj->count();
                    $profile->icon_bk = $profile->icon;
                    $profile->icon = icon_url() . $profile->icon;
                    // end
                    if ($token->is_pro == is_normal_user()) {
                        $profile_links = $Obj->where('is_default', 1)->orderBy('id', 'ASC')->get();
                    } else if ($token->is_pro == is_pro_user()) {
                        $profile_links = $Obj->orderBy('id', 'ASC')->get();
                    } else {
                        $profile_links = $Obj->get();
                    }

                    $my_recs = [];
                    if (!empty($profile_links)) {
                        foreach ($profile_links as $i => $rec) {

                            if ($has_subscription['success'] == false) {
                                $rec->icon = $profile->icon_bk;
                                $rec->title = $profile->title;
                            }

                            $rec = ProfileButton(0, $rec, null, 'mobile');
                            $my_recs[] = $rec;
                        }
                    }

                    $profile->profile_links = $my_recs;
                }
            }
        }

        $data['success'] = TRUE;
        $data['message'] = 'Profiles';
        $data['data'] = array('profiles' => $profiles);
        return response()->json($data, 201);
    }

    public function add_custom_profile(Request $request)
    {
        $validations['title'] = 'required|string';
        $validations['type'] = 'required|string';

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
        $has_subscription = chk_subscription($token);

        if ($has_subscription['success'] == false) {
            $data['success'] = FALSE;
            $data['message'] = 'No active subscription available.';
            $data['data'] = [];
            return response()->json($data, 201);
        }

        $profile_count = Profile::where('created_by', $token->id)->where('is_custom', 1)->where('title', $request->title);
        if ($profile_count->count() != 0) {
            $data['success'] = FALSE;
            $data['message'] = 'Profile with same title already exists.';
            $data['data'] = [];
            return response()->json($data, 201);
        }

        if ($token->is_pro != 2) {
            $profile_count = Profile::where('created_by', $token->id)->where('is_custom', 1)->count();
            if ($profile_count >= custom_profile_limit()) {
                $data['success'] = FALSE;
                $data['message'] = 'Limit exceeded.';
                $data['data'] = [];
                return response()->json($data, 201);
            }
        }

        $upload_dir = icon_dir();
        $date = date('Ymd');
        $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
        if ($response['success'] == FALSE) {
            $data['success'] = $response['success'];
            $data['message'] = $response['message'];
            $data['data'] = [];
            return response()->json($data, 201);
        }

        $filename = '';
        if ($response['success'] == TRUE && $response['filename'] != '') {
            $filename = $date . '/' . $response['filename'];
        }

        if ($filename == '') {
            $data['success'] = FALSE;
            $data['message'] = 'Please upload an icon.';
            $data['data'] = [];
            return response()->json($data, 201);
        }

        $profile = new Profile;
        $profile->profile_type_id = 5; //$request->profile_type_id;
        $profile->title = $request->title;
        $profile->title_de = $request->title;
        $profile->profile_code = unique_profile_code($request->title);
        $profile->type = $request->type;
        $profile->base_url = $request->base_url; //($request->type != 'url') ? $request->base_url : NULL;
        $profile->status = 1;
        $profile->is_custom = 1;
        $profile->is_pro = 0;
        $profile->created_by = $token->id;
        $profile->created_at = Carbon::now();
        $profile->icon = $filename;
        $profile->save();

        $data['success'] = TRUE;
        $data['message'] = 'Profile listed successfully.';
        $data['data'] = array('profile' => $profile);
        return response()->json($data, 201);
    }

    public function update_custom_profile(Request $request)
    {
        $validations['title'] = 'required|string';
        $validations['type'] = 'required|string';
        $validations['profile_id'] = 'required|string';

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
        $has_subscription = chk_subscription($token);

        if ($has_subscription['success'] == false) {
            $data['success'] = FALSE;
            $data['message'] = 'No active subscription available.';
            $data['data'] = [];
            return response()->json($data, 201);
        }

        $profile_count = Profile::where('created_by', $token->id)->where('is_custom', 1)->where('title', $request->title)->where('id', '!=', $request->profile_id);

        if ($profile_count->count() != 0) {
            $data['success'] = FALSE;
            $data['message'] = 'Profile with same title already exists.';
            $data['data'] = [];
            return response()->json($data, 201);
        }

        $upload_dir = icon_dir();
        $date = date('Ymd');
        $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
        if ($response['success'] == FALSE) {
            $data['success'] = $response['success'];
            $data['message'] = $response['message'];
            $data['data'] = [];
            return response()->json($data, 201);
        }

        $filename = '';
        if ($response['success'] == TRUE && $response['filename'] != '') {
            $filename = $date . '/' . $response['filename'];
        }

        $profile = Profile::findorfail($request->profile_id);
        $profile->title = $request->title;
        $profile->title_de = $request->title;
        $profile->type = $request->type;
        $profile->base_url = ($request->type != 'url') ? $request->base_url : NULL;
        $profile->status = 1;
        $profile->updated_by = $token->id;
        if ($filename == '') {
            $profile->icon = $filename;
        }
        $profile->save();

        $data['success'] = TRUE;
        $data['message'] = 'Profile updated successfully.';
        $data['data'] = array('profile' => $profile);
        return response()->json($data, 201);
    }

    public function delete_custom_profile(Request $request)
    {
        $validations['profile_id'] = 'required|string';

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
            $data['message'] = 'Profile ID is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $profile = Profile::where('id', $request->profile_id)->where('created_by', $token->id);

        if ($profile->count() == 0) {
            $data['success'] = FALSE;
            $data['message'] = 'Profile ID is invalid.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        CustomerProfile::where('profile_code', $profile->profile_code)->delete();
        $profile->delete();

        $info['type'] = 'delete-custom-profile';
        $info['type_id'] = $request->profile_id;
        $info['details'] = json_encode($profile);
        $info['device_id'] = isset($request->device_id) ? $request->device_id : 0;
        $info['ip_address'] = getUserIP();
        $info['created_by'] = $token->id;
        add_activity($info);

        $data['success'] = TRUE;
        $data['message'] = 'Profile deleted successfully.';
        $data['data'] = array('profile' => $profile);
        return response()->json($data, 201);
    }

    public function list_all_profiles(Request $request)
    {
        $token = $request->user();
        $is_business = $token->profile_view == 'business' ? 1 : 0;
        $has_subscription = chk_subscription($token);
        if ($has_subscription['success'] == false) {
            $sql = 'is_custom = 0';
        } else {
            $sql = '(is_custom = 0 OR p.created_by = ' . $token->id . ')';
        }

        $ProfileTypes = ProfileType::where('status', 1)->get();
        //pre_print($ProfileTypes);
        foreach ($ProfileTypes as $pt) {

            $query = \DB::table('profiles AS p')->select('p.id', 'p.title', 'title_de', 'profile_code', 'is_pro', 'icon', 'p.profile_type_id', 'p.type as hint', 'p.base_url', 'is_pet', 'is_sos', 'is_personal', 'is_ch_business')->where('profile_type_id', $pt->id)->where('p.status', 1)->whereRaw($sql);
            //'pt.title as profile_type','p.type as hint'

            $profiles = $query->get();

            if (count($profiles) != 0) {
                foreach ($profiles as $profile) {
                    if ($profile->profile_code == 'contact-card') {
                        $ContactCard = ContactCard::where('user_id', $token->id)->where('is_business', $is_business);
                        $profile->is_added = $ContactCard->count();
                        $profile->icon = icon_url() . $profile->icon;

                        $ContactCard = $ContactCard->first();
                        $customer_profile_ids = isset($ContactCard->customer_profile_ids) ? $ContactCard->customer_profile_ids : 0;
                        $CustomerProfile = CustomerProfile::whereIn('id', explode(',', $customer_profile_ids));
                        if ($CustomerProfile->count() == 0) {
                            $profile->is_added = 0;
                        }
                    } else {
                        $Obj = CustomerProfile::where('profile_code', $profile->profile_code)->where('user_id', $token->id)->where('is_business', $is_business);
                        $profile->is_added = $Obj->count();
                        $profile->icon_bk = $profile->icon;
                        $profile->icon = icon_url() . $profile->icon;
                        $profile_links = $Obj->get();

                        $my_recs = [];
                        if (!empty($profile_links)) {
                            foreach ($profile_links as $i => $rec) {

                                if ($has_subscription['success'] == false) {
                                    $rec->icon = $profile->icon_bk;
                                    $rec->title = $profile->title;
                                }

                                $rec = ProfileButton(0, $rec, null, 'mobile');
                                $my_recs[] = $rec;
                            }
                        }

                        $profile->profile_links = $my_recs;
                    }
                }
            }

            $pt->profiles = $profiles;
        }

        $data['success'] = TRUE;
        $data['message'] = 'Profiles';
        $data['data'] = array('profile_types' => $ProfileTypes);
        return response()->json($data, 201);
    }

    public function list_all_profiles_new(Request $request)
    {
        $token = $request->user();
        $is_business = $token->profile_view == 'business' ? 1 : 0;
        $has_subscription = chk_subscription($token);
        if ($has_subscription['success'] == false) {
            $sql = 'is_custom = 0';
        } else {
            $sql = '(is_custom = 0 OR p.created_by = ' . $token->id . ')';
        }

        $ProfileTypes = ProfileType::where('status', 1)->get();
        //pre_print($ProfileTypes);
        foreach ($ProfileTypes as $pt) {

            $query = \DB::table('profiles AS p')->select('p.id', 'p.title', 'title_de', 'profile_code', 'is_pro', 'icon', 'p.profile_type_id', 'p.type as hint', 'p.base_url', 'is_pet', 'is_sos', 'is_personal', 'is_ch_business','p.icon_svg_default as profile_icon_svg_default')->where('profile_type_id', $pt->id)->where('p.status', 1)->whereRaw($sql);
            //'pt.title as profile_type','p.type as hint'

            if ($token->user_group_id == 7 ) {
                $profiles = $query->where('is_pet', 1 );
            } elseif ($token->user_group_id == 8 ) {
                $profiles = $query->where('is_sos', 1);
            } elseif ($token->user_group_id == 9 ) {
                $profiles = $query->where('is_personal', 1);
            } elseif ($token->user_group_id == 10) {
                $profiles = $query->where('is_ch_business', 1);
            } else {
                $profiles = $query;
            }

            $profiles = $query->get();

            // $profiles = $profiles->get();

            if (count($profiles) != 0) {
                foreach ($profiles as $profile) {
                    if ($profile->profile_code == 'contact-card') {
                        $ContactCard = ContactCard::where('user_id', $token->id)->where('is_business', $is_business);
                        $profile->is_added = $ContactCard->count();
                        $profile->icon = icon_url() . $profile->icon;

                        $ContactCard = $ContactCard->first();
                        $customer_profile_ids = isset($ContactCard->customer_profile_ids) ? $ContactCard->customer_profile_ids : 0;
                        $CustomerProfile = CustomerProfile::whereIn('id', explode(',', $customer_profile_ids));
                        if ($CustomerProfile->count() == 0) {
                            $profile->is_added = 0;
                        }
                    } else {
                        $Obj = CustomerProfile::where('profile_code', $profile->profile_code)->where('user_id', $token->id)->where('is_business', $is_business);
                        $profile->is_added = $Obj->count();
                        $profile->icon_bk = $profile->icon;
                        $profile->icon = icon_url() . $profile->icon;
                        $profile_links = $Obj->get();

                        $my_recs = [];
                        if (!empty($profile_links)) {
                            foreach ($profile_links as $i => $rec) {

                                if ($has_subscription['success'] == false) {
                                    $rec->icon = $profile->icon_bk;
                                    $rec->title = $profile->title;
                                }

                                $rec = ProfileButton(0, $rec, null, 'mobile');
                                $my_recs[] = $rec;
                            }
                        }

                        $profile->profile_links = $my_recs;
                    }
                }
            }

            $pt->profiles = $profiles;
        }

        $data['success'] = TRUE;
        $data['message'] = 'Profiles';
        $data['data'] = array('profile_types' => $ProfileTypes);
        return response()->json($data, 201);
    }

    public function add_profile(Request $request)
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
        $has_subscription = chk_subscription($token);
        // pre_print($has_subscription);
        if (!in_array($request->profile_code, is_free_profile_btn())) {
            if ($request->business_profile == 'yes' && $has_subscription['success'] == false) {
                return response($has_subscription, 422);
            }
        }

        $date = date('Ymd');
        $Profile = Profile::where('profile_code', $request->profile_code);
        if ($Profile->count() > 0) {

            $is_default = 1;
            if (!in_array($request->profile_code, is_free_profile_btn())) {
                if ($Profile->first()->is_pro == 1 && $has_subscription['success'] == false) {
                    return response($has_subscription, 422);
                }


                $profile_limit = checkProfileLimit($token, $request);
                if ($profile_limit['success'] == false) {
                    return response($profile_limit, 400);
                }

                $is_default = $profile_limit['is_default'];

                if (isset($has_subscription['limit_exceeded']) && $has_subscription['limit_exceeded'] == true && $is_default == 0) {
                    $data['success'] = FALSE;
                    $data['message'] = 'Profiles limit has been exceeded!';
                    $data['data'] = (object)[];
                    return $data;
                }
            } else if (in_array($request->profile_code, is_free_profile_btn())) {

                if (CustomerProfile::where('profile_code', $request->profile_code)->where('user_id', $token->id)->count() >= 10) {
                    $data['success'] = FALSE;
                    $data['message'] = 'Profiles limit already reached.';
                    $data['data'] = (object)[];
                    return $data;
                }
            }

            $Obj = new CustomerProfile;
            $Obj->profile_link = $request->profile_link;
            $Obj->profile_code = $request->profile_code;
            $Obj->is_default = $is_default;
            $Obj->is_business = ($request->business_profile == 'yes') ? 1 : 0;
            $Obj->user_id = $token->id;
            $Obj->created_by = $token->id;
            $Obj->created_at = Carbon::now();

            if ($has_subscription['success'] && !in_array($request->profile_code, is_free_profile_btn())) {

                // check if limit to change icon and title has been reached before update
                if (($request->title != NULL || $request->title != '' || (isset($_FILES['icon']) && $_FILES['icon']['name'] != '')) && isset($has_subscription['limit_exceeded']) && $has_subscription['limit_exceeded'] == true) {
                    // do some action
                    $data['success'] = FALSE;
                    $data['message'] = $has_subscription['limit_message'];
                    $data['data'] = (object)[];
                    return response($data, 400);
                } else {
                    $Obj->title = $request->title;
                    $upload_dir = icon_dir();
                    $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
                    if ($response['success'] == TRUE) {
                        if ($response['filename'] != '') {
                            $Obj->icon = $date . '/' . $response['filename'];
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
            }

            if (in_array($request->profile_code, is_free_profile_btn())) {
                $Obj->title = $request->title;
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

            if ($Obj->icon != '') {
                $Obj->icon = icon_url() . $Obj->icon;
            }

            if ($Obj->file_image != '') {
                $Obj->file_image = file_url() . $Obj->file_image;
            }

            $Obj->profile_link = ($Obj->profile_code != 'file') ? $Profile->first()->base_url . $Obj->profile_link : $Obj->file_image;
            unset($Obj->file_image);

            $is_business = $request->business_profile == 'yes' ? 1 : 0;
            $ContactCard = ContactCard::where('user_id', $token->id)->where('is_business', $is_business);
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
                    $ContactCard->user_id = $token->id;
                    $ContactCard->created_by = $token->id;
                    $ContactCard->save();
                }
            }

            $data['success'] = TRUE;
            $data['message'] = 'Profile listed successfully.';
            $data['data'] = array('contact_card' => $ContactCard, 'profile' => $Obj);
            return response()->json($data, 201);
        } else {

            $data['success'] = FALSE;
            $data['message'] = 'Invalid profile code.';
            $data['data'] = (object)[];
            return response($data, 422);
        }
    }

    public function add_profile_svg(Request $request)
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
        $has_subscription = chk_subscription($token);
        // pre_print($has_subscription);
        if (!in_array($request->profile_code, is_free_profile_btn())) {
            if ($request->business_profile == 'yes' && $has_subscription['success'] == false) {
                return response($has_subscription, 422);
            }
        }

        $date = date('Ymd');
        $Profile = Profile::where('profile_code', $request->profile_code);
        if ($Profile->count() > 0) {

            $is_default = 1;
            if (!in_array($request->profile_code, is_free_profile_btn())) {
                if ($Profile->first()->is_pro == 1 && $has_subscription['success'] == false) {
                    return response($has_subscription, 422);
                }


                $profile_limit = checkProfileLimit($token, $request);
                if ($profile_limit['success'] == false) {
                    return response($profile_limit, 400);
                }

                $is_default = $profile_limit['is_default'];

                if (isset($has_subscription['limit_exceeded']) && $has_subscription['limit_exceeded'] == true && $is_default == 0) {
                    $data['success'] = FALSE;
                    $data['message'] = 'Profiles limit has been exceeded!';
                    $data['data'] = (object)[];
                    return $data;
                }
            } else if (in_array($request->profile_code, is_free_profile_btn())) {

                if (CustomerProfile::where('profile_code', $request->profile_code)->where('user_id', $token->id)->count() >= 10) {
                    $data['success'] = FALSE;
                    $data['message'] = 'Profiles limit already reached.';
                    $data['data'] = (object)[];
                    return $data;
                }
            }

            $Obj = new CustomerProfile;
            $Obj->profile_link = $request->profile_link;
            $Obj->profile_code = $request->profile_code;
            $Obj->is_default = $is_default;
            $Obj->is_business = ($request->business_profile == 'yes') ? 1 : 0;
            $Obj->user_id = $token->id;
            $Obj->created_by = $token->id;
            $Obj->created_at = Carbon::now();

            if ($has_subscription['success'] && !in_array($request->profile_code, is_free_profile_btn())) {

                // check if limit to change icon and title has been reached before update
                if (($request->title != NULL || $request->title != '' || (isset($_FILES['icon']) && $_FILES['icon']['name'] != '')) && isset($has_subscription['limit_exceeded']) && $has_subscription['limit_exceeded'] == true) {
                    // do some action
                    $data['success'] = FALSE;
                    $data['message'] = $has_subscription['limit_message'];
                    $data['data'] = (object)[];
                    return response($data, 400);
                } else {
                    $Obj->title = $request->title;
                    $upload_dir = icon_dir();
                    if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
                        $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
                        if ($response['success'] == TRUE) {
                            if ($response['filename'] != '') {
                                $Obj->icon = $date . '/' . $response['filename'];
                            }
                        }
                    }

                    if ($request->has('icon')) {
                        if ($request->icon == '') {
                            $Obj->icon = $request->icon;
                        }
                    }

                    if ($request->has('icon_svg') && $request->icon_svg != '') {
                        $Obj->icon_svg_default = $Profile->first()->icon_svg_default;
                        $Obj->icon = '';
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
            }

            if (in_array($request->profile_code, is_free_profile_btn())) {
                $Obj->title = $request->title;
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

            if ($Obj->icon != '') {
                $Obj->icon = icon_url() . $Obj->icon;
            }

            if ($Obj->file_image != '') {
                $Obj->file_image = file_url() . $Obj->file_image;
            }

            $Obj->profile_link = ($Obj->profile_code != 'file') ? $Profile->first()->base_url . $Obj->profile_link : $Obj->file_image;
            unset($Obj->file_image);

            $is_business = $request->business_profile == 'yes' ? 1 : 0;
            $ContactCard = ContactCard::where('user_id', $token->id)->where('is_business', $is_business);
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
                    $ContactCard->user_id = $token->id;
                    $ContactCard->created_by = $token->id;
                    $ContactCard->save();
                }
            }

            $data['success'] = TRUE;
            $data['message'] = 'Profile listed successfully.';
            $data['data'] = array('contact_card' => $ContactCard, 'profile' => $Obj);
            return response()->json($data, 201);
        } else {

            $data['success'] = FALSE;
            $data['message'] = 'Invalid profile code.';
            $data['data'] = (object)[];
            return response($data, 422);
        }
    }

    public function update_profile(Request $request)
    {
        $validations['my_profile_id'] = 'required|string';
        //if($request->profile_code != 'file'){
        //$validations['profile_link'] = 'required|string';
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

        $Obj = CustomerProfile::findorfail($request->my_profile_id);

        $has_subscription = chk_subscription($token, $request->my_profile_id);
        if ($has_subscription['success'] && !in_array($Obj->profile_code, is_free_profile_btn())) {
            // check if limit to change icon and title has been reached before update
            if (($request->title != NULL || $request->title != '' || (isset($_FILES['icon']) && $_FILES['icon']['name'] != '')) && isset($has_subscription['limit_exceeded']) && $has_subscription['limit_exceeded'] == true) {
                // do some action
                $data['success'] = FALSE;
                $data['message'] = $has_subscription['limit_message'];
                $data['data'] = (object)[];
                return response($data, 400);
            } else {
                if ($request->has('title')) {
                    $Obj->title = $request->title;
                }

                // if ($request->hasFile('icon')) {
                $upload_dir = icon_dir();
                $date = date('Ymd');
                $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
                if ($response['success'] == TRUE) {
                    if ($response['filename'] != '') {
                        $Obj->icon = $date . '/' . $response['filename'];
                    }
                }
                // }
            }

            $upload_dir = file_dir();
            $response = upload_file($request, 'file_image', $upload_dir);
            if ($response['success'] == TRUE) {
                if ($response['filename'] != '') {
                    $Obj->file_image = $date . '/' . $response['filename'];
                }
            }
        }

        if (in_array($request->profile_code, is_free_profile_btn())) {
            $Obj->title = $request->title;
        }

        $Obj->profile_link = $request->profile_link;
        $Obj->is_business = $request->is_business == 'yes' ? 1 : 0;
        $Obj->updated_by = $token->id;
        $Obj->save();

        if ($Obj->icon != '') {
            $Obj->icon = icon_url() . $Obj->icon;
        }

        if ($Obj->file_image != '') {
            $Obj->file_image = file_url() . $Obj->file_image;
        }

        $Profile = Profile::where('profile_code', $Obj->profile_code);
        $Obj->profile_link = ($Obj->profile_code != 'file') ? $Profile->first()->base_url . $Obj->profile_link : $Obj->file_image;
        unset($Obj->file_image);

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $Obj);
        return response()->json($data, 201);
    }

    public function update_profile_svg(Request $request)
    {
        $validations['my_profile_id'] = 'required|string';
        //if($request->profile_code != 'file'){
        //$validations['profile_link'] = 'required|string';
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

        $Obj = CustomerProfile::findorfail($request->my_profile_id);

        $has_subscription = chk_subscription($token, $request->my_profile_id);
        if ($has_subscription['success'] && !in_array($Obj->profile_code, is_free_profile_btn())) {
            // check if limit to change icon and title has been reached before update
            if (($request->title != NULL || $request->title != '' || (isset($_FILES['icon']) && $_FILES['icon']['name'] != '')) && isset($has_subscription['limit_exceeded']) && $has_subscription['limit_exceeded'] == true) {
                // do some action
                $data['success'] = FALSE;
                $data['message'] = $has_subscription['limit_message'];
                $data['data'] = (object)[];
                return response($data, 400);
            } else {
                if ($request->has('title')) {
                    $Obj->title = $request->title;
                }

                // if ($request->hasFile('icon')) {
                $upload_dir = icon_dir();
                $date = date('Ymd');
                $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
                if ($response['success'] == TRUE) {
                    if ($response['filename'] != '') {
                        $Obj->icon = $date . '/' . $response['filename'];
                    }
                }
                // }
            }

            $upload_dir = file_dir();
            $response = upload_file($request, 'file_image', $upload_dir);
            if ($response['success'] == TRUE) {
                if ($response['filename'] != '') {
                    $Obj->file_image = $date . '/' . $response['filename'];
                }
            }
        }

        if (in_array($request->profile_code, is_free_profile_btn())) {
            $Obj->title = $request->title;
        }

        $Obj->profile_link = $request->profile_link;
        $Obj->is_business = $request->is_business == 'yes' ? 1 : 0;
        $Obj->updated_by = $token->id;
        $Obj->save();

        if ($Obj->icon != '') {
            $Obj->icon = icon_url() . $Obj->icon;
        }

        if ($Obj->file_image != '') {
            $Obj->file_image = file_url() . $Obj->file_image;
        }

        $Profile = Profile::where('profile_code', $Obj->profile_code);
        $Obj->profile_link = ($Obj->profile_code != 'file') ? $Profile->first()->base_url . $Obj->profile_link : $Obj->file_image;
        unset($Obj->file_image);

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $Obj);
        return response()->json($data, 201);
    }

    public function delete_profile(Request $request)
    {
        $validations['my_profile_id'] = 'required|string';

        $validator = Validator::make($request->all(), $validations);
        $data = $request->json()->all();

        if ($validator->fails()) {
            if (isset($data['my_profile_id'])) {
                $my_profile_id = $data['my_profile_id'];
            } else {
                $data['success'] = FALSE;
                $data['message'] = 'Required field (my_profile_id) is missing.';
                $data['data'] = (object)[];
                return response($data, 400);
            }
        } else {
            $my_profile_id = $request->my_profile_id;
        }

        $token = $request->user();
        $Obj = CustomerProfile::find($my_profile_id);
        $is_default = $Obj->is_default;
        //if ($Obj && $Obj->user_id == $token->id) {
        $Obj->delete();
        //}

        if ($is_default == 1 && ($token->is_pro == is_pro_user() || $token->is_pro == is_grace_user())) {
            // set a profile as default
        }

        $data['success'] = TRUE;
        $data['message'] = 'Deleted successfully.';
        $data['data'] = (object)[];
        return response()->json($data, 201);
    }

    public function my_profiles(Request $request)
    {
        $token = $request->user();
        $has_subscription = chk_subscription($token);

        $type = isset($request->type) && $request->type != 'all' ? ($request->type == 'business' ? 1 : 0) : 2;
        $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.title as cp_title', 'cp.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.file_image', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible'); //'cp.is_direct as open_direct',
        //if($token->is_pro == 0){
        if ($has_subscription['success'] == false) {
            $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.title as cp_title', 'p.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.file_image', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible');
            $query = $query->where('p.is_pro', 0); //'cp.is_direct as open_direct',
        }

        $query->leftJoin('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
        $query = $query->where('user_id', $token->id)->where('p.status', 1);
        if ($type != 2) {
            $query = $query->where('is_business', $type);
        }

        if ($token->is_pro == is_normal_user()) {
            // $query->groupBy('cp.profile_code');
            $query->where('cp.is_default', 1);
        }

        $query->orderBy('cp.sequence', 'ASC');
        $query->orderBy('cp.id', 'ASC');
        $profiles = $query->get();
        // pre_print($profiles);
        $profiles = $this->profile_meta_version1($profiles, $token);

        $count = -1;
        // if (isset($request->type) && $request->type == 'all') {
        $count = UniqueCode::where('user_id', $token->id)->where('activated', 1)->count();
        // }

        $data['success'] = TRUE;
        $data['message'] = 'Profiles';
        $data['data'] = array('profiles' => $profiles, 'activated_devices' => $count);
        return response()->json($data, 201);
    }


    public function my_profiles_svg(Request $request)
    {
        $token = $request->user();
        $has_subscription = chk_subscription($token);

        $type = isset($request->type) && $request->type != 'all' ? ($request->type == 'business' ? 1 : 0) : 2;
        $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.title as cp_title', 'cp.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.file_image', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible', 'cp.icon_svg_default as custom_icon_svg', 'p.icon_svg_default as profile_icon_svg_default'); //'cp.is_direct as open_direct',
        //if($token->is_pro == 0){
        if ($has_subscription['success'] == false) {
            $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.title as cp_title', 'p.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.file_image', 'cp.user_id', 'cp.sequence', 'p.is_pro', 'p.type', 'cp.status as visible', 'cp.icon_svg_default as custom_icon_svg', 'p.icon_svg_default as profile_icon_svg_default');
            $query = $query->where('p.is_pro', 0); //'cp.is_direct as open_direct',
        }

        $query->leftJoin('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
        $query = $query->where('user_id', $token->id)->where('p.status', 1);
        if ($type != 2) {
            $query = $query->where('is_business', $type);
        }

        if ($token->is_pro == is_normal_user()) {
            // $query->groupBy('cp.profile_code');
            $query->where('cp.is_default', 1);
        }

        $query->orderBy('cp.sequence', 'ASC');
        $query->orderBy('cp.id', 'ASC');
        $profiles = $query->get();
        // pre_print($profiles);
        $profiles = $this->profile_meta($profiles, $token);

        $count = -1;
        // if (isset($request->type) && $request->type == 'all') {
        $count = UniqueCode::where('user_id', $token->id)->where('activated', 1)->count();
        // }

        $data['success'] = TRUE;
        $data['message'] = 'Profiles';
        $data['data'] = array('profiles' => $profiles, 'activated_devices' => $count);
        return response()->json($data, 201);
    }

    public function update_title(Request $request)
    {
        $validations['my_profile_id'] = 'required|string';
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
            $data['message'] = 'Required fields are missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $Obj = CustomerProfile::where('id', $request->my_profile_id)->where('user_id', $token->id);
        if ($Obj->count() == 0) {

            $data['success'] = FALSE;
            $data['message'] = 'Profile does not exist.';
            $data['data'] = (object)[];
            return response($data, 422);
        }

        $Obj = $Obj->first();

        if (!in_array($Obj->profile_code, is_free_profile_btn())) {
            $has_subscription = chk_subscription($token, $request->my_profile_id);
            if ($has_subscription['success'] == false) {
                return response($has_subscription, 422);
            }

            if (isset($has_subscription['limit_exceeded']) && $has_subscription['limit_exceeded'] == true) {
                $data['success'] = FALSE;
                $data['message'] = $has_subscription['limit_message'];
                $data['data'] = (object)[];
                return response($data, 400);
            }
        }

        $Obj->title = $request->title;
        $Obj->updated_by = $token->id;
        $Obj->save();

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $Obj);
        return response()->json($data, 201);
    }

    public function update_icon(Request $request)
    {
        $validations['my_profile_id'] = 'required|string';
        $validations['icon'] = 'required';

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

        $Obj = CustomerProfile::where('id', $request->my_profile_id)->where('user_id', $token->id);
        if ($Obj->count() == 0) {

            $data['success'] = FALSE;
            $data['message'] = 'Profile does not exist.';
            $data['data'] = (object)[];
            return response($data, 422);
        }

        $Obj = $Obj->first();

        if (in_array($Obj->profile_code, is_free_profile_btn())) {
            $data['success'] = FALSE;
            $data['message'] = 'Profile icon cannot be changed.';
            $data['data'] = (object)[];
            return response($data, 422);
        }

        $has_subscription = chk_subscription($token, $request->my_profile_id);
        if ($has_subscription['success'] == false) {
            return response($has_subscription, 422);
        }

        if (isset($has_subscription['limit_exceeded']) && $has_subscription['limit_exceeded'] == true) {
            $data['success'] = FALSE;
            $data['message'] = $has_subscription['limit_message'];
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $upload_dir = icon_dir();
        $date = date('Ymd');
        $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
        if ($response['success'] == FALSE) {
            $data['success'] = $response['success'];
            $data['message'] = $response['message'];
            $data['data'] = (object)[];
            return response()->json($data, 201);
        }

        if ($response['filename'] != '') {
            $Obj->icon = $date . '/' . $response['filename'];
            $Obj->updated_by = $token->id;
            $Obj->save();
            $Obj->icon = icon_url() . $Obj->icon;
        }

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $Obj);
        return response()->json($data, 201);
    }

    public function update_icon_svg(Request $request)
    {
        $validations['my_profile_id'] = 'required|string';
        // $validations['icon'] = 'required';

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

        $Obj = CustomerProfile::where('id', $request->my_profile_id)->where('user_id', $token->id);
        if ($Obj->count() == 0) {

            $data['success'] = FALSE;
            $data['message'] = 'Profile does not exist.';
            $data['data'] = (object)[];
            return response($data, 422);
        }

        $Obj = $Obj->first();

        if (in_array($Obj->profile_code, is_free_profile_btn())) {
            $data['success'] = FALSE;
            $data['message'] = 'Profile icon cannot be changed.';
            $data['data'] = (object)[];
            return response($data, 422);
        }

        $has_subscription = chk_subscription($token, $request->my_profile_id);
        if ($has_subscription['success'] == false) {
            return response($has_subscription, 422);
        }

        if (isset($has_subscription['limit_exceeded']) && $has_subscription['limit_exceeded'] == true) {
            $data['success'] = FALSE;
            $data['message'] = $has_subscription['limit_message'];
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $upload_dir = icon_dir();
        $date = date('Ymd');

        if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
            $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
            if ($response['success'] == FALSE) {
                $data['success'] = $response['success'];
                $data['message'] = $response['message'];
                $data['data'] = (object)[];
                return response()->json($data, 201);
            }

            if ($response['filename'] != '') {
                $Obj->icon = $date . '/' . $response['filename'];
                $Obj->updated_by = $token->id;
                $Obj->save();
                $Obj->icon = icon_url() . $Obj->icon;
            }
        }

        if ($request->has('icon')) {
            if ($request->icon == '') {
                $Obj->icon = $request->icon;
                $Obj->save();
            }
        }

        if ($request->has('icon_svg') && $request->icon_svg != '') {
            $Obj->icon_svg_default = $request->icon_svg;
            $Obj->icon = '';
            $Obj->save();
        }

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $Obj);
        return response()->json($data, 201);
    }

    public function profile_focused(Request $request)
    {
        $validations['my_profile_id'] = 'required|string';
        $validations['is_focused'] = 'required';

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

        // $has_subscription = chk_subscription($token, $request->my_profile_id);
        // if ($has_subscription['success'] == false) {
        //     return response($has_subscription, 422);
        // }

        $Obj = CustomerProfile::where('id', $request->my_profile_id)->where('user_id', $token->id);
        if ($Obj->count() == 0) {

            $data['success'] = FALSE;
            $data['message'] = 'Profile does not exist.';
            $data['data'] = (object)[];
            return response($data, 422);
        }

        $Obj = $Obj->first();
        $Obj->is_focused = $request->is_focused;
        $Obj->updated_by = $token->id;
        $Obj->save();
        $Obj->icon = icon_url() . $Obj->icon;

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = array('profile' => $Obj);
        return response()->json($data, 201);
    }

    public function update_business_info(Request $request)
    {
        $token = $request->user();

        $has_subscription = chk_subscription($token);
        if ($has_subscription['success'] == false) {
            return response($has_subscription, 422);
        }

        $BusinessInfo = BusinessInfo::where('user_id', $token->id);
        if ($BusinessInfo->count() > 0) {
            $BusinessInfo = $BusinessInfo->first();
            $BusinessInfo->bio = $request->bio;
            $BusinessInfo->user_id = $token->id;
            $BusinessInfo->updated_by = $token->id;
            $BusinessInfo->is_public = $request->is_public;
            $BusinessInfo->user_id = $token->id;
            $BusinessInfo->save();

            $data['success'] = TRUE;
            $data['message'] = 'Business info updated.';
            $data['data'] = array('business_info' => $BusinessInfo);
            return response()->json($data, 201);
        } else {

            $BusinessInfo = new BusinessInfo;
            $BusinessInfo->bio = $request->bio;
            $BusinessInfo->user_id = $token->id;
            $BusinessInfo->created_by = $token->id;
            $BusinessInfo->is_public = $request->is_public;
            $BusinessInfo->save();

            $data['success'] = TRUE;
            $data['message'] = 'Business info added.';
            $data['data'] = array('business_info' => $BusinessInfo);
            return response()->json($data, 201);
        }
    }

    public function contact_card(Request $request)
    {
        $token = $request->user();
        $validations['profile_ids'] = 'required';
        $validations['is_business'] = 'required';
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

        // $has_subscription = chk_subscription($token);
        // if ($has_subscription['success'] == false) {
        //     //return response($has_subscription, 422);
        // }

        $is_business = $request->is_business == 'yes' ? 1 : 0;
        $ContactCard = ContactCard::where('user_id', $token->id)->where('is_business', $is_business)->first();
        if ($ContactCard) {

            $ContactCard->customer_profile_ids = $request->profile_ids;
            $ContactCard->is_business = $is_business;
            $ContactCard->updated_by = $token->id;
            $ContactCard->save();

            $data['success'] = TRUE;
            $data['message'] = 'Contact card info updated.';
            $data['data'] = array('contact_card' => $ContactCard);
            return response()->json($data, 201);
        } else {

            $ContactCard = new ContactCard;
            $ContactCard->customer_profile_ids = $request->profile_ids;
            $ContactCard->is_business = $is_business;
            $ContactCard->user_id = $token->id;
            $ContactCard->created_by = $token->id;
            $ContactCard->save();

            $data['success'] = TRUE;
            $data['message'] = 'Contact card info added.';
            $data['data'] = array('contact_card' => $ContactCard);
            return response()->json($data, 201);
        }
    }

    public function get_contact_card(Request $request)
    {
        $token = $request->user();
        $validations['is_business'] = 'required';
        $validator = Validator::make($request->all(), $validations);

        if ($validator->fails()) {
            $data['success'] = FALSE;
            $data['message'] = 'Is_Business (is_business) field is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $is_business = $request->is_business == 'yes' ? 1 : 0;
        $ContactCards = ContactCard::where('user_id', $token->id)->where('is_business', $is_business);
        $profiles = [];
        if ($ContactCards->count() != 0) {

            $ContactCards = $ContactCards->first();
            $type = $is_business;
            $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_link as profile_value', 'cp.profile_code', 'cp.title as cp_title', 'cp.icon as cp_icon', 'cp.is_business', 'p.title', 'p.title_de', 'p.base_url', 'p.icon', 'cp.file_image', 'cp.user_id', 'cp.sequence', 'p.type');
            $query->leftJoin('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
            $query = $query->where('user_id', $token->id);
            $query = $query->where('is_business', $type)->where('p.status', 1);
            $has_subscription = chk_subscription($token);
            if ($has_subscription['success'] == false) {
                $query = $query->where('p.is_pro', 0);
            }

            $customer_profile_ids = isset($ContactCards->customer_profile_ids) ? $ContactCards->customer_profile_ids : 0;
            $query = $query->whereIn('cp.id', explode(',', $customer_profile_ids));
            $query->orderBy('cp.sequence', 'ASC');
            $query->orderBy('cp.id', 'DESC');
            $profiles = $query->get();
            $profiles = $this->profile_meta($profiles, $token);
        }

        $data['success'] = TRUE;
        $data['message'] = 'Contact card info.';
        $data['data'] = array('contact_card' => $profiles);
        return response()->json($data, 201);
    }

    public function public_profile(Request $request)
    {


        if ($request->username == '') {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid request.';
            $data['data'] = (object)[];
            return response($data, 422);
        }

        $no_record_found = false;
        $brand_name = $username = '';
        $Obj = User::where('username', $request->username);
        if ($Obj->count() == 0) {
            // die('Error: Invalid username');
            $str_code = $request->username;
            $code = UniqueCode::where('str_code', $str_code);
            if ($code->count() == 0) {
                $data['success'] = FALSE;
                $data['message'] = 'This is not a valid ' . config("app.name", "") . ' product.';
                $data['data'] = (object)[];
                return response($data, 404);
                // $no_record_found = true;
            } else {
                $code = $code->first();

                if (date('Y-m-d', strtotime($code->expires_on)) == date('Y-m-d')) {
                    // UniqueCode::where("str_code", $str_code)->update(["activated" => 0, "user_id" => 0, "updated_by" => -1]);
                    $code->activated = 0;
                    $code->user_id = 0;
                    $code->updated_by = -1;
                    $code->save();
                }

                if ($code->status == 0) {
                    $data['success'] = FALSE;
                    $data['message'] = 'This is not a valid ' . config("app.name", "") . ' product.';
                    $data['data'] = (object)[];
                    return response($data, 404);
                    $no_record_found = true;
                } else if ($code->user_id == 0) {
                    // $data['success'] = FALSE;
                    // $data['message'] = 'Invalid request.';
                    // $data['data'] = (object)[];
                    // return response($data, 422);
                    $no_record_found = true;
                } else {
                    $Obj = User::where('id', $code->user_id);
                    if ($Obj->count() == 0) {
                        // $data['success'] = FALSE;
                        // $data['message'] = 'Account doesn`t exist.';
                        // $data['data'] = (object)[];
                        // return response($data, 422);
                        $no_record_found = true;
                    } else {
                        $Obj = $Obj->first();
                        if ($Obj->status == 0) {
                            $no_record_found = true;
                            $retValue = check_user_type_status($Obj);
                            $user_type = $retValue['user_type'];
                            if ($user_type != 'user') {
                                $username = 'addmeebusiness';
                            } else {
                                $username = 'addmee';
                            }
                        } else {
                            $retValue = check_user_type_status($Obj);
                            $user_status = $retValue['user_status'];
                            if ($user_status == 0) {
                                $user_type = $retValue['user_type'];
                                $no_record_found = true;
                                if ($user_type != 'user') {
                                    $username = 'addmeebusiness';
                                } else {
                                    $username = 'addmee';
                                }
                            }
                            // else {
                            // if ($brand_name == '') {
                            // $user_type = $retValue['user_type'];
                            // if ($user_type != 'user') {
                            // $brand_name = 'addmeebusiness';
                            // } else {
                            // $brand_name = 'addmee';
                            // }
                            // }
                            // }
                            $brand_name = $code->brand;
                        }
                    }
                }
            }
        } else {
            $Obj = $Obj->first();
            if ($Obj->status == 0) {
                $no_record_found = true;
                $retValue = check_user_type_status($Obj);
                $user_type = $retValue['user_type'];
                if ($user_type != 'user') {
                    $username = 'addmeebusiness';
                } else {
                    $username = 'addmee';
                }
            } else {
                // $BusinessUser = BusinessUser::where('user_id', $Obj->id);
                // if ($BusinessUser->count() > 0) {
                //     $BusinessUser = $BusinessUser->first();
                //     $parent_id = $BusinessUser->parent_id;
                //     if ($parent_id != 0) {
                //         $parentUser = User::where('id', $parent_id);
                //         if ($parentUser->count() > 0) {
                //             $parentUser = $parentUser->first();
                //             if ($parentUser->status == 0) {
                //                 $username = 'addmeebusiness';
                //                 $no_record_found = true;
                //             }
                //         }
                //     }
                // }
                $retValue = check_user_type_status($Obj);
                $user_status = $retValue['user_status'];
                if ($user_status == 0) {
                    $user_type = $retValue['user_type'];
                    $no_record_found = true;
                    if ($user_type != 'user') {
                        $username = 'addmeebusiness';
                    } else {
                        $username = 'addmee';
                    }
                }
            }
        }

        if ($no_record_found) {
            if (strtolower(config("app.name", "")) == 'addmee') {
                if ((isset($code) && $code && $code->device != '' && in_array($code->device, ['c', 'cc']) || $username != '')) {
                    $username = 'addmeebusiness';
                } else {
                    $username = 'addmee';
                }
            } else {
                $username = 'tapmee';
            }

            $Obj = User::where('username', $username);
            if ($Obj->count() == 0) {
                $data['success'] = FALSE;
                $data['message'] = 'Invalid request.';
                $data['data'] = (object)[];
                return response($data, 422);
            } else {
                $Obj = $Obj->first();
            }
        }

        $BusinessInfo = BusinessInfo::where('user_id', $Obj->id)->first();

        if (!empty($Obj) && $Obj->is_public == 2) {
            $data['success'] = TRUE;
            $data['message'] = 'Dieses Profil ist privat';

            unset($Obj->vcode, $Obj->access_token);
            $Obj->banner = image_url($Obj->banner);
            $Obj->logo = image_url($Obj->logo);
            $Obj->profile_image = $Obj->logo;

            $data['data'] = array('profiles' => [], 'user' => $Obj, 'user_profile_link' => main_url() . '/' . $request->username);
            return response($data, 200);
        } else if (!empty($Obj) && $Obj->is_public == 0 && $Obj->profile_view == 'personal') {
            $data['success'] = TRUE;
            $data['message'] = 'Dieses Profil ist privat';

            unset($Obj->vcode, $Obj->access_token);
            $Obj->banner = image_url($Obj->banner);
            $Obj->logo = image_url($Obj->logo);
            $Obj->profile_image = $Obj->logo;

            $data['data'] = array('profiles' => [], 'user' => $Obj, 'user_profile_link' => main_url() . '/' . $request->username);
            return response($data, 200);
        } else if (!empty($BusinessInfo) && $BusinessInfo->is_public == 0 && $Obj->profile_view == 'business') {
            $data['success'] = TRUE;
            $data['message'] = 'Dieses Profil ist privat';

            unset($Obj->vcode, $Obj->access_token);
            $Obj->banner = image_url($Obj->banner);
            $Obj->logo = image_url($Obj->logo);
            $Obj->profile_image = $Obj->logo;

            $data['data'] = array('profiles' => [], 'user' => $Obj, 'user_profile_link' => main_url() . '/' . $request->username);
            return response($data, 200);
        } else {

            $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.icon as cp_icon', 'cp.title as cp_title', 'p.title', 'p.title_de', 'p.icon', 'p.base_url', 'u.username', 'u.name', 'u.subscription_expires_on', 'u.profile_view', 'cp.is_business', 'cp.file_image', 'cp.sequence', 'cp.user_id', 'p.is_pro', 'p.type', 'cp.status as visible', 'cp.is_focused as is_highlighted');
            $query->Join('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
            $query->Join('users AS u', 'u.id', '=', 'cp.user_id');
            $query = $query->where('username', $Obj->username)->where('p.status', 1)->where('cp.status', 1);
            //if($token->is_pro == 0){
            if (!empty($Obj)) {
                $has_subscription = chk_subscription($Obj);
                if ($has_subscription['success'] == false) {
                    $query = $query->where('p.is_pro', 0);
                    $query = $query->groupBy('cp.profile_code');
                    unset($Obj->company_address);
                }
            }

            $query->orderBy('cp.sequence', 'ASC');
            $query->orderBy('cp.id', 'ASC');
            $profiles = $query->get();
            //pre_print($profiles);
            $my_recs = [];
            if (count($profiles) > 0) {
                foreach ($profiles as $i => $profile) {

                    if ($profile->profile_view == 'business' && $profile->is_business == 0) {
                        unset($profiles[$i]);
                        continue;
                    }

                    if ($profile->profile_view == 'personal' && $profile->is_business == 1) {
                        unset($profiles[$i]);
                        continue;
                    }

                    if ($profile->profile_code == 'whatsapp') {
                        $profile->profile_link = trim($profile->profile_link, '+');
                    }

                    $profile->icon = icon_url() . $profile->icon;
                    $contact_link = main_url() . '/contact-card/' . encrypt($profile->user_id);
                    
                    $profile->profile_link_value = $profile->profile_link;
                    $profile->profile_link = ($profile->profile_code != 'contact-card') ? $profile->profile_link : $contact_link;
                    
                    if ($profile->profile_code == 'www' || $profile->type == 'url') {
                        $is_valid_url = false;
                        if (substr($profile->profile_link, 0, 8) == "https://" || substr($profile->profile_link, 0, 7) == "http://") {
                            $is_valid_url = true;
                        }

                        if ($is_valid_url == false) {
                            $profile->profile_link = 'http://' . $profile->profile_link;
                        }
                    }

                    $profile->profile_link = ($profile->profile_code != 'file') ? $profile->base_url . $profile->profile_link : file_url() . $profile->file_image;
                    // if ($profile->subscription_expires_on != NULL && strtotime($profile->subscription_expires_on) >= strtotime(date('Y-m-d H:i:s'))) {
                    if ($has_subscription['success'] == true) {
                        if ($profile->cp_title != '' && $profile->cp_title !=  NULL) {
                            $profile->title = $profile->title_de = $profile->cp_title;
                        }

                        $profile->icon = ($profile->cp_icon != '' && $profile->cp_icon != NULL) ? icon_url() . $profile->cp_icon : $profile->icon;
                    }
                    unset($profile->cp_icon, $profile->cp_title, $profile->subscription_expires_on, $profile->file_image);

                    $ContactCardTotal = 1;
                    if ($profile->profile_code == 'contact-card') {
                        $is_business = $profile->profile_view == 'business' ? 1 : 0;
                        
                        $ContactCard = ContactCard::where('user_id', $profile->user_id)->where('is_business', $is_business);
                        
                        if ($ContactCard->count() == 0) {
                            $ContactCardTotal = 0;
                            
                            
                        } else {
                            
                            $ContactCard = $ContactCard->first();
                            
                            $customer_profile_ids = isset($ContactCard->customer_profile_ids) ? $ContactCard->customer_profile_ids : 0;
                            $CustomerProfile = CustomerProfile::whereIn('id', explode(',', $customer_profile_ids));
                            
                            if ($CustomerProfile->count() == 0) {
                                $ContactCardTotal = 0;
                            }
                        }
                    }

                    if (isset($profile->is_highlighted)) {
                        $profile->is_highlighted = ($profile->is_highlighted == 1) ? true : false;
                    }

                    if ($ContactCardTotal != 0) {
                        $my_recs[] = $profile;
                    }
                }
            }

            $brand_profiles = [];
            $profileDetails = [];
            if ($brand_name != '') {
                $brand_profiles = $this->brand_profiles($brand_name);
                // pre_print($brand_profiles);
                if (!empty($brand_profiles)) {
                    if (isset($brand_profiles[0])) {
                        $__brand_profile = (array) $brand_profiles[0];
                        if (isset($__brand_profile['first_name']) && isset($__brand_profile['last_name']) && isset($__brand_profile['designation'])) {
                            $profileDetails['name'] = $__brand_profile['first_name'] . ' ' . $__brand_profile['last_name'];
                            $profileDetails['first_name'] = $__brand_profile['first_name'];
                            $profileDetails['last_name'] = $__brand_profile['last_name'];
                            $profileDetails['company_name'] = isset($__brand_profile['company_name']) ? $__brand_profile['company_name'] : '';
                            $profileDetails['designation'] = $__brand_profile['designation'];
                        }
                    }
                }
            }

            if (empty($profileDetails) || (is_array($profileDetails) && count($profileDetails) == 0)) {
                $profileDetails = (object) $profileDetails;
            }

            $Obj->banner = image_url($Obj->banner);
            $Obj->logo = image_url($Obj->logo);
            $Obj->profile_image = $Obj->logo;
            $Obj->company_logo = image_url($Obj->company_logo);

            if (!empty($request->lat) && !empty($request->lng) && trim($request->lat) != '' && trim($request->lng) != '') {
                $token = $request->user();
                $_Obj = new TapsViews;
                $_Obj->user_id = $Obj->id;
                $_Obj->is_tap_view = 't';
                $_Obj->type = 'main-profile';
                $_Obj->type_id = $Obj->id;
                $_Obj->lat = $request->lat;
                $_Obj->lng = $request->lng;
                $_Obj->created_by = !empty($token) ? $token->id : 0;
                $_Obj->save();
            }

            $template = anyTemplateAssigned($Obj->id);
            $UserSettings = userSettingsObj($Obj->id, $template); //UserSettings::where('user_id', $Obj->id)->first();
            $Obj = UserObj($Obj, 0, $template);
            unset($UserSettings['2fa_enabled'], $UserSettings['created_by'], $UserSettings['created_at'], $UserSettings['updated_by'], $UserSettings['updated_at'], $UserSettings['is_editable'], $UserSettings['user_old_data'], $UserSettings['settings_old_data']);

            if (count($UserSettings) == 0) {
                $UserSettings = null;
            } else {
                $UserSettings['capture_lead'] = (string)$UserSettings['capture_lead'];
                $UserSettings['designation_color'] = $UserSettings['address_company_color'] = $UserSettings['text_color'];
                if ($UserSettings['text_color'] == 'rgba(17, 24, 3, 1)') {
                    $UserSettings['designation_color'] = 'rgba(97,97,97,1)';
                    $UserSettings['address_company_color'] = 'rgba(158, 158, 158, 1)';
                }
            }

            $data['success'] = TRUE;
            $data['message'] = 'Profiles';
            $data['data'] = array('profiles' => $my_recs, 'user_profile_link' => main_url() . '/' . $Obj->username, 'user' => $Obj, 'brand_profiles' => $brand_profiles, 'brandDetail' => $profileDetails, 'settings' => $UserSettings);
            
            return response()->json($data, 201);
        }
    }

    public function public_profile_svg(Request $request)
    {


        if ($request->username == '') {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid request.';
            $data['data'] = (object)[];
            return response($data, 422);
        }

        $no_record_found = false;
        $brand_name = $username = '';
        $Obj = User::where('username', $request->username);
        if ($Obj->count() == 0) {
            // die('Error: Invalid username');
            $str_code = $request->username;
            $code = UniqueCode::where('str_code', $str_code);
            if ($code->count() == 0) {
                $data['success'] = FALSE;
                $data['message'] = 'This is not a valid ' . config("app.name", "") . ' product.';
                $data['data'] = (object)[];
                return response($data, 404);
                // $no_record_found = true;
            } else {
                $code = $code->first();

                if (date('Y-m-d', strtotime($code->expires_on)) == date('Y-m-d')) {
                    // UniqueCode::where("str_code", $str_code)->update(["activated" => 0, "user_id" => 0, "updated_by" => -1]);
                    $code->activated = 0;
                    $code->user_id = 0;
                    $code->updated_by = -1;
                    $code->save();
                }

                if ($code->status == 0) {
                    $data['success'] = FALSE;
                    $data['message'] = 'This is not a valid ' . config("app.name", "") . ' product.';
                    $data['data'] = (object)[];
                    return response($data, 404);
                    $no_record_found = true;
                } else if ($code->user_id == 0) {
                    // $data['success'] = FALSE;
                    // $data['message'] = 'Invalid request.';
                    // $data['data'] = (object)[];
                    // return response($data, 422);
                    $no_record_found = true;
                } else {
                    $Obj = User::where('id', $code->user_id);
                    if ($Obj->count() == 0) {
                        // $data['success'] = FALSE;
                        // $data['message'] = 'Account doesn`t exist.';
                        // $data['data'] = (object)[];
                        // return response($data, 422);
                        $no_record_found = true;
                    } else {
                        $Obj = $Obj->first();
                        if ($Obj->status == 0) {
                            $no_record_found = true;
                            $retValue = check_user_type_status($Obj);
                            $user_type = $retValue['user_type'];
                            if ($user_type != 'user') {
                                $username = 'addmeebusiness';
                            } else {
                                $username = 'addmee';
                            }
                        } else {
                            $retValue = check_user_type_status($Obj);
                            $user_status = $retValue['user_status'];
                            if ($user_status == 0) {
                                $user_type = $retValue['user_type'];
                                $no_record_found = true;
                                if ($user_type != 'user') {
                                    $username = 'addmeebusiness';
                                } else {
                                    $username = 'addmee';
                                }
                            } else {
                            }
                            $brand_name = $code->brand;
                        }
                    }
                }
            }
        } else {
            $Obj = $Obj->first();
            if ($Obj->status == 0) {
                $no_record_found = true;
                $retValue = check_user_type_status($Obj);
                $user_type = $retValue['user_type'];
                if ($user_type != 'user') {
                    $username = 'addmeebusiness';
                } else {
                    $username = 'addmee';
                }
            } else {
                // $BusinessUser = BusinessUser::where('user_id', $Obj->id);
                // if ($BusinessUser->count() > 0) {
                //     $BusinessUser = $BusinessUser->first();
                //     $parent_id = $BusinessUser->parent_id;
                //     if ($parent_id != 0) {
                //         $parentUser = User::where('id', $parent_id);
                //         if ($parentUser->count() > 0) {
                //             $parentUser = $parentUser->first();
                //             if ($parentUser->status == 0) {
                //                 $username = 'addmeebusiness';
                //                 $no_record_found = true;
                //             }
                //         }
                //     }
                // }
                $retValue = check_user_type_status($Obj);
                $user_status = $retValue['user_status'];
                if ($user_status == 0) {
                    $user_type = $retValue['user_type'];
                    $no_record_found = true;
                    if ($user_type != 'user') {
                        $username = 'addmeebusiness';
                    } else {
                        $username = 'addmee';
                    }
                }
            }
        }

        if ($no_record_found) {
            if (strtolower(config("app.name", "")) == 'addmee') {
                if ((isset($code) && $code && $code->device != '' && in_array($code->device, ['c', 'cc']) || $username != '')) {
                    $username = 'addmeebusiness';
                } else {
                    $username = 'addmee';
                }
            } else {
                $username = 'tapmee';
            }

            $Obj = User::where('username', $username);
            if ($Obj->count() == 0) {
                $data['success'] = FALSE;
                $data['message'] = 'Invalid request.';
                $data['data'] = (object)[];
                return response($data, 422);
            } else {
                $Obj = $Obj->first();
            }
        }

        $BusinessInfo = BusinessInfo::where('user_id', $Obj->id)->first();

        if (!empty($Obj) && $Obj->is_public == 2) {
            $data['success'] = TRUE;
            $data['message'] = 'Dieses Profil ist privat';

            unset($Obj->vcode, $Obj->access_token);
            $Obj->banner = image_url($Obj->banner);
            $Obj->logo = image_url($Obj->logo);
            $Obj->profile_image = $Obj->logo;

            $data['data'] = array('profiles' => [], 'user' => $Obj, 'user_profile_link' => main_url() . '/' . $request->username);
            return response($data, 200);
        } else if (!empty($Obj) && $Obj->is_public == 0 && $Obj->profile_view == 'personal') {
            $data['success'] = TRUE;
            $data['message'] = 'Dieses Profil ist privat';

            unset($Obj->vcode, $Obj->access_token);
            $Obj->banner = image_url($Obj->banner);
            $Obj->logo = image_url($Obj->logo);
            $Obj->profile_image = $Obj->logo;

            $data['data'] = array('profiles' => [], 'user' => $Obj, 'user_profile_link' => main_url() . '/' . $request->username);
            return response($data, 200);
        } else if (!empty($BusinessInfo) && $BusinessInfo->is_public == 0 && $Obj->profile_view == 'business') {
            $data['success'] = TRUE;
            $data['message'] = 'Dieses Profil ist privat';

            unset($Obj->vcode, $Obj->access_token);
            $Obj->banner = image_url($Obj->banner);
            $Obj->logo = image_url($Obj->logo);
            $Obj->profile_image = $Obj->logo;

            $data['data'] = array('profiles' => [], 'user' => $Obj, 'user_profile_link' => main_url() . '/' . $request->username);
            return response($data, 200);
        } else {

            $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.icon as cp_icon', 'cp.title as cp_title', 'p.title', 'p.title_de', 'p.icon', 'p.base_url', 'u.username', 'u.name', 'u.subscription_expires_on', 'u.profile_view', 'cp.is_business', 'cp.file_image', 'cp.sequence', 'cp.user_id', 'p.is_pro', 'p.type', 'cp.status as visible', 'cp.is_focused as is_highlighted', 'p.icon_svg_colorized as profile_icon_svg_colorized' , 'p.icon_svg_default as profile_icon_svg_default','cp.icon as custom_icon_url', 'cp.icon_svg_default as custom_icon_svg');
            $query->Join('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
            $query->Join('users AS u', 'u.id', '=', 'cp.user_id');
            $query = $query->where('username', $Obj->username)->where('p.status', 1)->where('cp.status', 1);
            //if($token->is_pro == 0){
            if (!empty($Obj)) {
                $has_subscription = chk_subscription($Obj);
                if ($has_subscription['success'] == false) {
                    $query = $query->where('p.is_pro', 0);
                    $query = $query->groupBy('cp.profile_code');
                    unset($Obj->company_address);
                }
            }

            $query->orderBy('cp.sequence', 'ASC');
            $query->orderBy('cp.id', 'ASC');
            $profiles = $query->get();
            // pre_print($profiles);
            $my_recs = [];
            if (count($profiles) > 0) {
                foreach ($profiles as $i => $profile) {

                    if ($profile->profile_view == 'business' && $profile->is_business == 0) {
                        unset($profiles[$i]);
                        continue;
                    }

                    if ($profile->profile_view == 'personal' && $profile->is_business == 1) {
                        unset($profiles[$i]);
                        continue;
                    }

                    if ($profile->profile_code == 'whatsapp') {
                        $profile->profile_link = trim($profile->profile_link, '+');
                    }

                    $profile->icon = icon_url() . $profile->icon;
                    $contact_link = main_url() . '/contact-card/' . encrypt($profile->user_id);

                    $profile->profile_link_value = $profile->profile_link;
                    $profile->profile_link = ($profile->profile_code != 'contact-card') ? $profile->profile_link : $contact_link;

                    if ($profile->profile_code == 'www' || $profile->type == 'url') {
                        $is_valid_url = false;
                        if (substr($profile->profile_link, 0, 8) == "https://" || substr($profile->profile_link, 0, 7) == "http://") {
                            $is_valid_url = true;
                        }

                        if ($is_valid_url == false) {
                            $profile->profile_link = 'http://' . $profile->profile_link;
                        }
                    }

                    $profile->profile_link = ($profile->profile_code != 'file') ? $profile->base_url . $profile->profile_link : file_url() . $profile->file_image;
                    // if ($profile->subscription_expires_on != NULL && strtotime($profile->subscription_expires_on) >= strtotime(date('Y-m-d H:i:s'))) {
                    if ($has_subscription['success'] == true) {
                        if ($profile->cp_title != '' && $profile->cp_title !=  NULL) {
                            $profile->title = $profile->title_de = $profile->cp_title;
                        }

                        $profile->icon = ($profile->cp_icon != '' && $profile->cp_icon != NULL) ? icon_url() . $profile->cp_icon : $profile->icon;
                        $profile->custom_icon_url = ($profile->custom_icon_url != '' && $profile->custom_icon_url != NULL) ? icon_url() . $profile->custom_icon_url : "";
                    } else {
                        $profile->custom_icon_svg = null;
                    }

                    unset($profile->cp_icon, $profile->cp_title, $profile->subscription_expires_on, $profile->file_image);

                    $ContactCardTotal = 1;
                    if ($profile->profile_code == 'contact-card') {
                        $is_business = $profile->profile_view == 'business' ? 1 : 0;
                        $ContactCard = ContactCard::where('user_id', $profile->user_id)->where('is_business', $is_business);
                        if ($ContactCard->count() == 0) {
                            $ContactCardTotal = 0;
                        } else {
                            $ContactCard = $ContactCard->first();
                            $customer_profile_ids = isset($ContactCard->customer_profile_ids) ? $ContactCard->customer_profile_ids : 0;
                            $CustomerProfile = CustomerProfile::whereIn('id', explode(',', $customer_profile_ids));
                            if ($CustomerProfile->count() == 0) {
                                $ContactCardTotal = 0;
                            }
                        }
                    }

                    if (isset($profile->is_highlighted)) {
                        $profile->is_highlighted = ($profile->is_highlighted == 1) ? true : false;
                    }

                    if ($ContactCardTotal != 0) {
                        $my_recs[] = $profile;
                    }
                }
            }

            $brand_profiles = [];
            $profileDetails = [];
            if ($brand_name != '') {
                $brand_profiles = $this->brand_profiles($brand_name);
                // pre_print($brand_profiles);
                if (!empty($brand_profiles)) {
                    if (isset($brand_profiles[0])) {
                        $__brand_profile = (array) $brand_profiles[0];
                        if (isset($__brand_profile['first_name']) && isset($__brand_profile['last_name']) && isset($__brand_profile['designation'])) {
                            $profileDetails['name'] = $__brand_profile['first_name'] . ' ' . $__brand_profile['last_name'];
                            $profileDetails['first_name'] = $__brand_profile['first_name'];
                            $profileDetails['last_name'] = $__brand_profile['last_name'];
                            $profileDetails['company_name'] = isset($__brand_profile['company_name']) ? $__brand_profile['company_name'] : '';
                            $profileDetails['designation'] = $__brand_profile['designation'];
                        }
                    }
                }
            }

            if (empty($profileDetails) || (is_array($profileDetails) && count($profileDetails) == 0)) {
                $profileDetails = (object) $profileDetails;
            }

            $Obj->banner = image_url($Obj->banner);
            $Obj->logo = image_url($Obj->logo);
            $Obj->profile_image = $Obj->logo;
            $Obj->company_logo = image_url($Obj->company_logo);

            if (!empty($request->lat) && !empty($request->lng) && trim($request->lat) != '' && trim($request->lng) != '') {
                $token = $request->user();
                $_Obj = new TapsViews;
                $_Obj->user_id = $Obj->id;
                $_Obj->is_tap_view = 't';
                $_Obj->type = 'main-profile';
                $_Obj->type_id = $Obj->id;
                $_Obj->lat = $request->lat;
                $_Obj->lng = $request->lng;
                $_Obj->created_by = !empty($token) ? $token->id : 0;
                $_Obj->save();
            }

            $template = anyTemplateAssigned($Obj->id);
            $UserSettings = userSettingsObj_Old($Obj->id, $template); //UserSettings::where('user_id', $Obj->id)->first();
            $Obj = UserObj($Obj);
            unset($UserSettings['2fa_enabled'], $UserSettings['created_by'], $UserSettings['created_at'], $UserSettings['updated_by'], $UserSettings['updated_at'], $UserSettings['is_editable'], $UserSettings['user_old_data'], $UserSettings['settings_old_data']);

            if (count($UserSettings) == 0) {
                $UserSettings = null;
            } else {
                $UserSettings['capture_lead'] = (string)$UserSettings['capture_lead'];
                $UserSettings['designation_color'] = $UserSettings['address_company_color'] = $UserSettings['text_color'];
                if ($UserSettings['text_color'] == 'rgba(17, 24, 3, 1)') {
                    $UserSettings['designation_color'] = 'rgba(97,97,97,1)';
                    $UserSettings['address_company_color'] = 'rgba(158, 158, 158, 1)';
                }
            }

            $Obj = UserObj($Obj, 0, $template);

            $data['success'] = TRUE;
            $data['message'] = 'Profiles';
            $data['data'] = array('profiles' => $my_recs, 'user_profile_link' => main_url() . '/' . $Obj->username, 'user' => $Obj, 'brand_profiles' => $brand_profiles, 'brandDetail' => $profileDetails, 'settings' => $UserSettings);
            return response()->json($data, 201);
        }
    }

    public function user_notes(Request $request)
    {
        $token = $request->user();
        $UserNotes = UserNote::where('user_id', $token->id)->orderBy('id', 'DESC')->get();
        if (!empty($UserNotes)) {
            foreach ($UserNotes as $i => $UserNote) {
                $UserNotes[$i]->photo = $UserNote->photo != NULL ? image_url($UserNote->photo) : '';
            }
        }
        $data['success'] = TRUE;
        $data['message'] = 'User Notes';
        $data['data'] = array('user_notes' => $UserNotes);
        return response()->json($data, 201);
    }



    // Function to get contact by email from hubspot
    private function getContactByEmail($email, $accessToken)
    {
        $client = new Client();
        $endpoint = 'https://api.hubapi.com/crm/v3/objects/contacts/search';
        $data = [
            'filterGroups' => [
                [
                    'filters' => [
                        [
                            'value' => $email,
                            'propertyName' => 'email',
                            'operator' => 'EQ',
                        ],
                    ],
                ],
            ],
        ];

        $response = $client->post($endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);

        $result = json_decode($response->getBody(), true);
        if (!empty($result['results'])) {
            $data['success'] = FALSE;
            $data['message'] = $result['results'][0];
            return response($data, 400);
        }
        return null;
    }

    // Function to get contact by Phone from hubspot
    private function getContactByPhone($Phone, $accessToken)
    {
        $client = new Client();
        $endpoint = 'https://api.hubapi.com/crm/v3/objects/contacts/search';
        $data = [
            'filterGroups' => [
                [
                    'filters' => [
                        [
                            'value' => $Phone,
                            'propertyName' => 'phone',
                            'operator' => 'EQ',
                        ],
                    ],
                ],
            ],
        ];

        $response = $client->post($endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);

        $result = json_decode($response->getBody(), true);
        if (!empty($result['results'])) {
            $data['success'] = FALSE;
            $data['message'] = $result['results'][0];
            return response($data, 400);
        }
        return null;
    }



    public function add_user_note(Request $request)
    {


        $validations['first_name'] = 'required';
        $validations['last_name'] = 'required';
        $validations['email'] = 'required|string|email';
        // $validations['phone_no'] = 'required';
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
        // start hubspot api

        $findBusinessUser = BusinessUser::where('user_id', $request->user_id)->first();

        if (!empty($findBusinessUser) && $findBusinessUser->parent_id != 0) {
            $user_id = $findBusinessUser->parent_id;
        } elseif (!empty($findBusinessUser) && $findBusinessUser->parent_id == 0) {
            $user_id = $findBusinessUser->user_id;
        } else {
            $user_id = $request->user_id;
        }

        $check_user = PlatformIntegration::where('platform_id', 1)->where('user_id', $user_id)->first();
        $findUser = User::where('id', $request->user_id)->first();

        if ($findUser) {
            $created_email = $findUser->email ?? ''; // Use the null coalescing operator
            $findname = ($findUser->first_name == '' && $findUser->last_name == '') ? $findUser->name : $findUser->first_name . ' ' . $findUser->last_name;
        } else {
            // Handle the case where the user is not found
            $created_email = '';
            $findname = '';
        }

        if ($check_user) {
            $accessToken = $check_user->access_token;
            $refreshToken = $check_user->refresh_token;
            $currentTime = now();
            $expires_in = $check_user->expires_in;
            $differenceInMinutes = $currentTime->diffInMinutes($expires_in);

            // Check if the token needs to be refreshed
            if ($currentTime > $expires_in) {
                $accessToken = generateAccessTokenWithRefreshToken($refreshToken, $check_user);
            }

            $client = new Client();

            $endpoint = 'https://api.hubapi.com/crm/v3/objects/contacts/search';
            $dataFilter = [
                'filterGroups' => [
                    [
                        'filters' => [
                            [
                                'value' => $request->email,
                                'propertyName' => 'email',
                                'operator' => 'EQ',
                            ],
                        ],
                    ],
                ],
            ];

            $response = $client->post($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $dataFilter,
            ]);

            $result = json_decode($response->getBody(), true);

            if (!empty($result['results'])) {
                // Contact already exists, update the contact with new data
                $contactId = $result['results'][0]['id'];
                if ($contactId) {
                    $updateEndpoint = 'https://api.hubapi.com/crm/v3/objects/contacts/' . $contactId;

                    // Fetch existing contact data
                    $existingContactData = $client->get($updateEndpoint, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type' => 'application/json',
                        ],

                        'query' => [
                            'properties' => 'firstname,lastname,phone,company,', // Add 'phone' to explicitly request it
                        ],
                    ]);



                    $createTimelineEventEndpoint = "https://api.hubapi.com/engagements/v1/engagements";

                    // Data for creating a new note
                    $newNoteData = [
                        'engagement' => [
                            'active' => true,
                            'timestamp' => round(microtime(true) * 1000), // Use the current timestamp
                            'type' => 'NOTE',
                        ],
                        'associations' => [
                            'contactIds' => [$contactId], // The contact ID you want to associate the note with
                        ],
                        'metadata' => [
                            'body' => $request->note . PHP_EOL . '[AddMee System Notice: The owner of this contact is ' . $findname . ' with Email Address: ' . $created_email . ']',
                        ],
                    ];

                    $createNoteResponse = $client->post($createTimelineEventEndpoint, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type' => 'application/json',
                        ],
                        'json' => $newNoteData,
                    ]);





                    $existingContactData = json_decode($existingContactData->getBody(), true);

                    // Modify the updateData array based on existing values
                    $updateData = [
                        'properties' => [
                            'firstname' => (empty($existingContactData['properties']['firstname']) && !empty($request->first_name)) ? $request->first_name : $existingContactData['properties']['firstname'],
                            'lastname' => (empty($existingContactData['properties']['lastname']) && !empty($request->last_name)) ? $request->last_name : $existingContactData['properties']['lastname'],
                            'phone' => (empty($existingContactData['properties']['phone']) && !empty($request->phone_no)) ? $request->phone_no : $existingContactData['properties']['phone'],
                            'company' => (empty($existingContactData['properties']['company']) && !empty($request->company)) ? $request->company : $existingContactData['properties']['company'],

                        ],
                    ];

                    // Perform the update only if there are changes
                    if (!empty(array_filter($updateData['properties'], 'strlen'))) {
                        $response = $client->patch($updateEndpoint, [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $accessToken,
                                'Content-Type' => 'application/json',
                            ],
                            'json' => $updateData,
                        ]);

                        $statusCode = $response->getStatusCode();
                        $result = json_decode($response->getBody(), true);

                        if (!empty($result['results'])) {
                            $data['success'] = FALSE;
                            $data['message'] = $result['results'][0];
                            return response($data, 400);
                        }

                        $data['success'] = TRUE;
                        $data['hubspotContactMessage'] = 'Contact updated in HubSpot successfully';
                    }
                }
            } else {


                $getOwnersEndpoint = 'https://api.hubapi.com/owners/v2/owners';

                // Make a request to get owners
                $getOwnersEndpointresponse = $client->get($getOwnersEndpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                    ],
                ]);

                // Decode the response
                $ownersData = json_decode($getOwnersEndpointresponse->getBody(), true);

                $matchedOwnerId = '';
                if (!empty($ownersData)) {
                    // Iterate through the array
                    foreach ($ownersData as $owner) {
                        $ownerId = $owner['ownerId'];
                        $email = $owner['email'];
                        if ($email === $created_email) {
                            $matchedOwnerId = $ownerId;
                            break;
                        }
                    }
                }
                $endpoint = 'https://api.hubapi.com/crm/v3/objects/contacts';
                // Data for creating/updating contacts
                $dataCreate = [
                    'properties' => [
                        'email' => $request->email,
                        'firstname' => $request->first_name ?? '',
                        'lastname' => $request->last_name,
                        'phone' => $request->phone_no,
                        'hubspot_owner_id' => $matchedOwnerId,
                        'company' => $request->company ?? '',
                    ],
                ];
                $response = $client->post($endpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $dataCreate,
                ]);

                $statusCode = $response->getStatusCode();
                $result = json_decode($response->getBody(), true);

                if ($statusCode != 201) {
                    // Handle the error response if the creation fails
                    $data['success'] = FALSE;
                    $data['message'] = $result;
                    return response($data, 400);
                }


                $contactId = $result['id']; // Assuming the contact ID is present in the response

                $createTimelineEventEndpoint = "https://api.hubapi.com/engagements/v1/engagements";

                // Data for creating a new note
                $newNoteData = [
                    'engagement' => [
                        'active' => true,
                        'timestamp' => round(microtime(true) * 1000), // Use the current timestamp
                        'type' => 'NOTE',
                    ],
                    'associations' => [
                        'contactIds' => [$contactId], // The contact ID you want to associate the note with
                    ],
                    'metadata' => [
                        'body' => $request->note . PHP_EOL . '[AddMee System Notice: The owner of this contact is ' . $findname . ' with Email Address: ' . $created_email . ']',
                    ],
                ];

                $createNoteResponse = $client->post($createTimelineEventEndpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $newNoteData,
                ]);





                // Handle success response or perform additional actions if needed
                $data['success'] = TRUE;
                $data['hubspotContactMessage'] = 'Contact pushed into HubSpot successfully';
                // return response($data, 200);
            }
        }
        //end hubspot api
        $Obj = new UserNote;
        $Obj->first_name = $request->first_name;
        $Obj->last_name = $request->last_name;
        $Obj->name = $request->first_name . ' ' . $request->last_name;
        $Obj->email = $request->email;
        $Obj->phone_no = $request->phone_no;
        $Obj->note = $request->note;
        $Obj->website = $request->has('website') ? $request->website : NULL;
        $Obj->job_tittle = $request->has('job_tittle') ? $request->job_tittle : NULL;
        $Obj->company = $request->has('company') ? $request->company : NULL;
        $Obj->address = $request->has('address') ? $request->address : NULL;
        $Obj->note = $request->note;
        $Obj->user_id = $request->user_id;
        $Obj->created_by = $request->user_id;

        $upload_dir = icon_dir();
        $date = date('Ymd');
        $response = upload_file($request, 'photo', $upload_dir . '/' . $date);
        if ($response['success'] == TRUE && $response['filename'] != '') {
            $Obj->photo = $date . '/' . $response['filename'];
        }

        $Obj->save();

        //send_notification($token, $message);
        $User = User::find($request->user_id);
        $image = $User->banner != '' ? image_url($User->banner) : uploads_url() . 'img/customer.png';
        $name = ($User->first_name == '' && $User->last_name == '') ? $User->name : $User->first_name . ' ' . $User->last_name;

        if (strtolower(config("app.name", "")) != 'addmee') {
            $html = '<p style="font-family: sans-serif; text-align:center;"><img alt="' . config("app.name", "") . '" height="40" src="' . $image . '"><br>' . $name . '<br><br><br>Hi ' . $Obj->name . ',<br><br> This is ' . $name . '`s digital business card: <br><br><a style="padding: 10px 20px; background-color: #E30045; color: #fff; font-size: 16px; text-decoration: none; border-radius: 15px; font-weight: 600; font-family: sans-serif;" href="' . main_url() . '/' . $User->username . '">Click to Open Card</a><br><br><a href="__SITEURL__"><img alt="' . config("app.name", "") . '" height="40" src="' . uploads_url() . 'img/tapmee-logo.png"></a></p>';
            // echo $html;exit;

            $receiver_html = '<p style="font-family: sans-serif; text-align:center;">Hi ' . $name . ', you have a new connection on ' . ucwords(config("app.name", "")) . '! <br><br><img alt="' . config("app.name", "") . '" height="40" src="' . uploads_url() . 'img/customer.png"><br><br>' . $Obj->name . '<br><br>' . $Obj->email . '<br><br>' . $Obj->phone_no;

            if ($request->has('company')) {
                $receiver_html .= '<br><br>' . $Obj->company;
            }
            if ($request->has('job_tittle')) {
                $receiver_html .= '<br><br>' . $Obj->job_tittle;
            }
            if ($request->has('website')) {
                $receiver_html .= '<br><br><a href="' . $Obj->website . '">' . $Obj->website . '</a>';
            }

            $receiver_html .= '<br><br>' . $Obj->note . '<br><br>' . dmy($Obj->created_at) . '<br><br><a href="__SITEURL__"><img alt="' . config("app.name", "") . '" height="40" src="' . uploads_url() . 'img/tapmee-logo.png"></a></p>';
            // <br><br>Reply to this email to start a conversation with '.$Obj->name.'

            //user who tries to connect
            $html = str_replace('__SITEURL__', 'https://tapmee.co/', $html);
            Mail::send([], [], function ($message) use ($html, $name, $Obj) {
                $message
                    ->to($Obj->email)
                    ->subject(config("app.name", "") . ": You connected with " . $name . "")
                    ->from("no-reply@tapmee.co", config("app.name", "") . " Connect")
                    ->setBody($html, 'text/html');
            });

            //user with whom sender is connected
            $receiver_html = str_replace('__SITEURL__', 'https://tapmee.co/', $receiver_html);
            Mail::send([], [], function ($message) use ($receiver_html, $name, $User) {
                $message
                    ->to($User->email)
                    ->subject(config("app.name", "") . ": Connect")
                    ->from("no-reply@tapmee.co", "" . config("app.name", "") . " Connect")
                    ->setBody($receiver_html, 'text/html');
            });
        } else {
            $html = '<p style="font-family: sans-serif; text-align:center;"><img alt="' . config("app.name", "") . '" height="40" src="' . $image . '"><br>' . $name . '<br><br><br>Hi ' . $request->name . ',<br><br> This is ' . $name . '`s digital business card: <br><br><a style="padding: 10px 20px; background-color: #E30045; color: #fff; font-size: 16px; text-decoration: none; border-radius: 15px; font-weight: 600; font-family: sans-serif;" href="' . main_url() . '/' . $User->username . '">Click to Open Card</a><br><br><a href="__SITEURL__"><img alt="' . config("app.name", "") . '" height="40" src="' . uploads_url() . 'img/addmee-logo.png"></a></p>';
            // echo $html;exit;

            $receiver_html = '<p style="font-family: sans-serif; text-align:center;">Hi ' . $name . ', you have a new ' . ucwords(config("app.name", "")) . ' connection! <br><br><img alt="' . config("app.name", "") . '" height="40" src="' . uploads_url() . 'img/customer.png"><br><br>' . $Obj->name . '<br><br>' . $Obj->email . '<br><br>' . $Obj->phone_no;

            if ($request->has('company')) {
                $receiver_html .= '<br><br>' . $Obj->company;
            }
            if ($request->has('job_tittle')) {
                $receiver_html .= '<br><br>' . $Obj->job_tittle;
            }
            if ($request->has('website')) {
                $receiver_html .= '<br><br><a href="' . $Obj->website . '">' . $Obj->website . '</a>';
            }

            $receiver_html .= '<br><br>' . $Obj->note . '<br><br>' . dmy($Obj->created_at) . '<br><br><a href="__SITEURL__"><img alt="' . config("app.name", "") . '" height="40" src="' . uploads_url() . 'img/addmee-logo.png"></a></p>';

            $html = str_replace('__SITEURL__', 'https://addmee.de/', $html);
            //user who tries to connect
            $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
            $body = [
                'Messages' => [
                    [
                        'From' => ['Email' => "no-reply@addmee.de", 'Name' => "" . config("app.name", "") . " Connect"],
                        'To' => [['Email' => $Obj->email, 'Name' => $Obj->name]],
                        'Subject' => "You connected with " . $name . " ",
                        'TextPart' => "You connected with " . $name . "",
                        'HTMLPart' => $html,
                        'CustomID' => config("app.name", "")
                    ]
                ]
            ];

            $response = $mj->post(Resources::$Email, ['body' => $body]);

            //user with whom sender is connected
            $receiver_html = str_replace('__SITEURL__', 'https://addmee.de/', $receiver_html);
            $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
            $body = [
                'Messages' => [
                    [
                        'From' => ['Email' => "no-reply@addmee.de", 'Name' => "" . config("app.name", "") . " Connect"],
                        'To' => [['Email' => $User->email, 'Name' => $name]],
                        'Subject' => $request->first_name . ' ' . $request->last_name . " connected with you",
                        'TextPart' => $request->first_name . ' ' . $request->last_name . " connected with you",
                        'HTMLPart' => $receiver_html,
                        'CustomID' => config("app.name", "")
                    ]
                ]
            ];

            $response = $mj->post(Resources::$Email, ['body' => $body]);
        }

        $data['success'] = TRUE;
        $data['message'] = 'Added successfully.';
        $data['data'] = array('note' => $Obj);
        return response()->json($data, 201);
    }

    public function save_sequence(Request $request)
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
        if (!empty($sequence)) {
            foreach ($sequence as $id => $val) {
                $Obj = CustomerProfile::find($id);
                if ($Obj) {
                    $Obj->sequence = $val;
                    $Obj->updated_by = $token->id;
                    $Obj->save();
                }
            }
        }

        $data['success'] = TRUE;
        $data['message'] = 'Updated successfully.';
        $data['data'] = (object)[];
        return response()->json($data, 201);
    }

    public function add_feedback(Request $request)
    {
        $validations['first_name'] = 'required';
        $validations['last_name'] = 'required';
        $validations['subject'] = 'required';
        $validations['details'] = 'required';
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
        $Obj = new Feedback;
        $Obj->full_name = $request->full_name;
        $Obj->first_name = $request->first_name;
        $Obj->last_name = $request->last_name;
        $Obj->email = $request->email;
        $Obj->details = $request->details;
        $Obj->phone_no = $request->phone_no;
        $Obj->subject = $request->subject;
        $Obj->user_id = $token->id;
        $Obj->created_by = $token->id;
        $Obj->save();

        if (strtolower(config("app.name", "")) != 'addmee') {
            // code
        } else {
            $html = '<p style="font-family:sans-serif;padding-left: 30px;">First Name: ' . $Obj->first_name . '<br><br>Last Name: ' . $Obj->last_name . '<br><br>Email: ' . $Obj->email . '<br><br>Phone No: ' . $Obj->phone_no . '<br><br>Subject: ' . $Obj->subject . '<br><br>Details: ' . $Obj->details . '<br><br></p>';
            // echo $html;exit;

            $datetime = new DateTime('now', new DateTimeZone('Europe/Berlin'));
            $date = $datetime->format('Y-m-d');
            $time = $datetime->format('h:i A');
            $subject = 'In-App feedback from ' . $date . ' at ' . $time . ' by ' . $Obj->first_name . ' ' . $Obj->last_name;

            $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
            $body = [
                'Messages' => [
                    [
                        'From' => ['Email' => "no-reply@addmee.de", 'Name' => $Obj->first_name . ' ' . $Obj->first_name],
                        'To' => [['Email' => 'info@addmee.de', 'Name' => 'AddMee']],
                        'Subject' => $subject,
                        'TextPart' => $subject,
                        'HTMLPart' => $html,
                        'CustomID' => config("app.name", "")
                    ]
                ]
            ];

            $response = $mj->post(Resources::$Email, ['body' => $body]);
        }

        $data['success'] = TRUE;
        $data['message'] = 'Added successfully.';
        $data['data'] = array('feedback' => $Obj);
        return response()->json($data, 201);
    }

    public function update_subscription(Request $request)
    {
        $validations['subscription_status'] = 'required';
        $validations['expires_on'] = 'required';
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
        $User = User::findorfail($token->id);
        if ($User) {

            $expires_on = $request->expires_on;
            $time_offset = explode('+', $expires_on);
            $time_offset = count($time_offset) > 1 ? $time_offset[1] : '+00';

            $subscription_expires_on = new DateTime($expires_on);
            $subscription_expires_on->setTimeZone(new DateTimeZone("UTC"));
            $expires_on = $subscription_expires_on->format("Y-m-d H:i:s");

            if (strtolower($request->subscription_status) == 'active') {
                $User->subscription_date = date('Y-m-d H:i:s');
                $User->subscription_expires_on =  $expires_on; //$request->expires_on;
                $User->is_pro = is_pro_user();
                $User->save();

                $subscription = Subscriptions::where('user_id', $User->id);
                if ($subscription->count() > 0) {
                    $subscription = $subscription->first();
                } else {
                    $subscription = new Subscriptions;
                }

                $subscription->user_id = $User->id;
                $subscription->package_name = strtolower($request->product_id);
                $subscription->status = $request->subscription_status;
                $subscription->payment_token = $request->payment_token;
                $subscription->subscription_date = date('Y-m-d H:i:s');
                $subscription->expires_on = $request->expires_on;
                $subscription->time_offset = $time_offset;
                $subscription->device = $request->device;
                $subscription->created_by = $User->id;
                $subscription->save();
            } else {
                // those users who have given 1 year free subscription
                if ($User->is_pro != is_grace_user()) {

                    $User->subscription_expires_on = $expires_on; //$request->expires_on;
                    $User->is_pro = is_normal_user();
                    $User->save();

                    $subscription = Subscriptions::where('user_id', $User->id);
                    if ($subscription->count() > 0) {
                        $subscription = $subscription->orderby('id', 'DESC')->first();

                        $subscription->status = $request->subscription_status;
                        $subscription->expires_on = $request->expires_on; //$request->expires_on;
                        $subscription->time_offset = $time_offset;
                        $subscription->device = $request->device;
                        $subscription->updated_by = $User->id;
                        $subscription->save();
                    }
                }
            }

            $User->banner = image_url($User->banner);
            $User->logo = image_url($User->logo);
            $User->profile_image = ($User->logo);

            $info['type'] = 'subscription';
            $info['type_id'] = $User->id;
            $info['details'] = json_encode(['user_id' => $User->id, 'product_id' => $request->product_id, 'subscription_status' => $request->subscription_status, 'payment_token' => $request->payment_token, 'expires_on' => $request->expires_on]);
            $info['device_id'] = isset($request->device_id) ? $request->device_id : 0;
            $info['ip_address'] = getUserIP();
            $info['created_by'] = $User->id;
            add_activity($info);

            $data['success'] = TRUE;
            $data['message'] = 'User subscription updated.';
            $data['data'] = array('user' => $User);
            return response()->json($data, 201);
        } else {

            $data['success'] = FALSE;
            $data['message'] = 'Account does not exist.';
            $data['data'] = (object)[];

            return response($data, 422);
        }
    }

    public function get_subscription_status(Request $request)
    {
        $token = $request->user();
        $has_subscription = chk_subscription($token);

        $subscription = Subscriptions::where('user_id', $token->id);
        if ($subscription->count() > 0) {
            $subscription = $subscription->first();
        } else {
            $subscription = (object) [];
        }

        $isGracePeriod = $token->is_pro == is_grace_user() ? true : false;
        $data['success'] = TRUE;
        $data['message'] = 'Subscription Status.';
        $data['data'] = array('subscription_status' => $has_subscription['success'], 'subscription' => $subscription, 'isGracePeriod' => $isGracePeriod);
        return response()->json($data, 201);
    }

    public function device_list(Request $request)
    {
        $token = $request->user();
        $qry = UniqueCode::where('user_id', $token->id)->where('activated', 1);
        $devices = $qry->get();
        $devices_total = $qry->count();

        $data['success'] = TRUE;
        $data['message'] = 'Devices List.';
        $data['data'] = array('devices' => $devices, 'devices_count' => $devices_total);
        return response()->json($data, 201);
    }

    public function update_device_name(Request $request)
    {
        $validations['device_name'] = 'required';
        $validations['device_id'] = 'required';
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
        $device = UniqueCode::where('id', $request->device_id)->where('activated', 1);
        if ($device->count() > 0) {
            $device = $device->first();
            $device_name = $device->device_name;
            if ($device->user_id == $token->id) {
                $device->device_name = $request->device_name;
                $device->save();

                $info['type'] = 'device_name';
                $info['type_id'] = $device->id;
                $info['details'] = json_encode(['old_device_name' => $device_name]);
                $info['device_id'] = isset($request->device_id) ? $request->device_id : 0;
                $info['ip_address'] = getUserIP();
                $info['created_by'] = $token->id;
                add_activity($info);

                $data['success'] = TRUE;
                $data['message'] = 'Device name updated.';
                $data['data'] = array('device' => $device);
                return response()->json($data, 201);
            } else {
                $data['success'] = FALSE;
                $data['message'] = 'Device does not belong to current user.';
                $data['data'] = (object)[];

                return response($data, 422);
            }
        } else {

            $data['success'] = FALSE;
            $data['message'] = 'Device does not exist.';
            $data['data'] = (object)[];

            return response($data, 422);
        }
    }

    public function unmap_device(Request $request)
    {
        $validations['device'] = 'required';
        $validations['password'] = 'required';
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
        $device = UniqueCode::where('id', $request->device)->where('activated', 1);
        if ($device->count() > 0) {
            $device = $device->first();

            $User = User::where('id', $token->id)->first();
            if (Hash::check($request->password, $User->password)) {
                if ($device->user_id == $token->id) {

                    $device->user_id = 0;
                    $device->activated = 0;
                    $device->updated_by = $token->id;
                    $device->save();

                    $info['type'] = 'unmap_device';
                    $info['type_id'] = $device->id;
                    $info['details'] = json_encode(['user_id' => $token->id]);
                    $info['device_id'] = isset($request->device_id) ? $request->device_id : 0;
                    $info['ip_address'] = getUserIP();
                    $info['created_by'] = $token->id;
                    add_activity($info);

                    $data['success'] = TRUE;
                    $data['message'] = 'Device unmapped successfully.';
                    $data['data'] = array('device' => $device);
                    return response()->json($data, 201);
                } else {
                    $data['success'] = FALSE;
                    $data['message'] = 'This product does not belong to your account and therefore cannot be unpaired.';
                    $data['data'] = (object)[];

                    return response($data, 422);
                }
            } else {
                $data['success'] = FALSE;
                $data['message'] = 'Incorrect Password.';
                $data['data'] = (object)[];
                return response($data, 400);
            }
        } else {

            $data['success'] = FALSE;
            $data['message'] = 'Device does not exist.';
            $data['data'] = (object)[];

            return response($data, 422);
        }
    }

    public function profile_meta_version1($profiles, $token) // is used in customer controller
    {
        $has_subscription = chk_subscription($token);
        $my_recs = [];
        if (count($profiles) > 0) {

            $ContactCard = ContactCard::where('user_id', $token->id)->where('is_business', 0)->first();
            $customer_profile_ids = isset($ContactCard->customer_profile_ids) ? $ContactCard->customer_profile_ids : 0;
            $BusinessContactCard = ContactCard::where('user_id', $token->id)->where('is_business', 1)->first();
            $personal_profile_ids = isset($ContactCard->customer_profile_ids) ? explode(',', $customer_profile_ids) : array();
            $business_profile_ids = isset($BusinessContactCard->customer_profile_ids) ? explode(',', $BusinessContactCard->customer_profile_ids) : array();

            foreach ($profiles as $i => $profile) {

                if ($token->open_direct == 1 && $i == 0) {
                    $profile->open_direct = 1;
                }

                $profile->icon = icon_url() . $profile->icon;
                $contact_link = main_url() . '/contact-card/' . encrypt($profile->user_id);

                $profile->profile_link_value = $profile->profile_link;
                $profile->profile_link = ($profile->profile_code != 'contact-card') ? $profile->profile_link : $contact_link;

                if ($profile->profile_code == 'www' || $profile->type == 'url') {
                    $is_valid_url = false;
                    if (substr($profile->profile_link, 0, 8) == "https://" || substr($profile->profile_link, 0, 7) == "http://") {
                        $is_valid_url = true;
                    }

                    if ($is_valid_url == false) {
                        $profile->profile_link = 'http://' . $profile->profile_link;
                    }
                }

                $profile->profile_link = ($profile->profile_code != 'file') ? $profile->base_url . $profile->profile_link : file_url() . $profile->file_image;

                if ($has_subscription['success'] == true) {
                    if ($profile->cp_title != '' && $profile->cp_title !=  NULL) {
                        $profile->title = $profile->title_de = $profile->cp_title;
                    }

                    $profile->icon = ($profile->cp_icon != '' && $profile->cp_icon != NULL) ? icon_url() . $profile->cp_icon : $profile->icon;
                } else if (in_array($profile->profile_code, is_free_profile_btn())) {
                    if ($profile->cp_title != '' && $profile->cp_title !=  NULL) {
                        $profile->title = $profile->title_de = $profile->cp_title;
                    }
                }

                unset($profile->cp_icon, $profile->cp_title, $profile->file_image);

                $profile->added_to_contact_card = 'no';
                if ($profile->is_business == 0) {
                    $profile->added_to_contact_card = in_array($profile->id, $personal_profile_ids) ? 'yes' : 'no';
                } else if ($profile->is_business == 1) {
                    $profile->added_to_contact_card = in_array($profile->id, $business_profile_ids) ? 'yes' : 'no';
                }

                $ContactCardTotal = 1;
                if ($profile->profile_code == 'contact-card') {
                    $is_business = $token->profile_view == 'business' ? 1 : 0;
                    $ContactCard = ContactCard::where('user_id', $profile->user_id)->where('is_business', $is_business);

                    $ContactCardTotal = $ContactCard->count();

                    if ($ContactCardTotal != 0) {
                        $ContactCard = $ContactCard->first();
                        $customer_profile_ids = isset($ContactCard->customer_profile_ids) ? $ContactCard->customer_profile_ids : 0;
                        $CustomerProfile = CustomerProfile::whereIn('id', explode(',', $customer_profile_ids));
                        if ($CustomerProfile->count() == 0) {
                            $ContactCardTotal = 0;
                        }
                    }
                }

                if (isset($profile->visible)) {
                    $profile->visible = ($profile->visible == 1) ? true : false;
                }

                if (isset($profile->is_highlighted)) {
                    $profile->is_highlighted = ($profile->is_highlighted == 1) ? true : false;
                }

                // template
                $query = \DB::table('template_assignees AS ta')->select('cpt.user_template_id', 'cpt.is_unique');
                $query->leftJoin('customer_profile_templates AS cpt', 'cpt.id', '=', 'ta.customer_profile_template_id');
                $query->where('ta.customer_profile_id', $profile->id);
                if ($query->count() > 0) {
                    $rec = $query->first();
                    $profile->template_id = (int) $rec->user_template_id;
                    $profile->is_unique = (int) $rec->is_unique;
                } else {
                    $profile->template_id = 0;
                    $profile->is_unique = 0;
                }

                unset($profile->file_image, $profile->status, $profile->created_by, $profile->created_at, $profile->updated_by, $profile->updated_at, $profile->is_direct);

                if ($ContactCardTotal != 0) {
                    $my_recs[] = $profile;
                }
            }
        }

        return $my_recs;
    }

    public function profile_meta($profiles, $token) // is used in customer controller
    {
        $has_subscription = chk_subscription($token);
        $my_recs = [];
        if (count($profiles) > 0) {

            $ContactCard = ContactCard::where('user_id', $token->id)->where('is_business', 0)->first();
            $customer_profile_ids = isset($ContactCard->customer_profile_ids) ? $ContactCard->customer_profile_ids : 0;
            $BusinessContactCard = ContactCard::where('user_id', $token->id)->where('is_business', 1)->first();
            $personal_profile_ids = isset($ContactCard->customer_profile_ids) ? explode(',', $customer_profile_ids) : array();
            $business_profile_ids = isset($BusinessContactCard->customer_profile_ids) ? explode(',', $BusinessContactCard->customer_profile_ids) : array();

            foreach ($profiles as $i => $profile) {

                if ($token->open_direct == 1 && $i == 0) {
                    $profile->open_direct = 1;
                }

                $profile->icon = ''; // icon_url() . $profile->icon;
                $contact_link = main_url() . '/contact-card/' . encrypt($profile->user_id);
                // if (isset($profile->profile_icon_svg_default) && $profile->profile_icon_svg_default != '') {
                if (isset($profile->icon_svg) && $profile->icon_svg == '') {
                    if (isset($profile->profile_icon_svg_default) && $profile->profile_icon_svg_default != '') {
                        $profile->icon_svg = $profile->profile_icon_svg_default;
                    }
                } else {
                    if (isset($profile->profile_icon_svg_default) && $profile->profile_icon_svg_default != '') {
                        $profile->icon_svg = $profile->profile_icon_svg_default;
                    }
                }
                if (isset($profile->icon_svg) && $profile->icon_svg != '') {
                    // $profile->icon_svg = $profile->profile_icon_svg_default;
                    $profile->icon = '';
                }

                $profile->profile_link_value = $profile->profile_link;
                $profile->profile_link = ($profile->profile_code != 'contact-card') ? $profile->profile_link : $contact_link;

                if ($profile->profile_code == 'www' || $profile->type == 'url') {
                    $is_valid_url = false;
                    if (substr($profile->profile_link, 0, 8) == "https://" || substr($profile->profile_link, 0, 7) == "http://") {
                        $is_valid_url = true;
                    }

                    if ($is_valid_url == false) {
                        $profile->profile_link = 'http://' . $profile->profile_link;
                    }
                }

                $profile->profile_link = ($profile->profile_code != 'file') ? $profile->base_url . $profile->profile_link : file_url() . $profile->file_image;

                if ($has_subscription['success'] == true) {
                    if ($profile->cp_title != '' && $profile->cp_title !=  NULL) {
                        $profile->title = $profile->title_de = $profile->cp_title;
                    }

                    $profile->icon = ($profile->cp_icon != '' && $profile->cp_icon != NULL) ? icon_url() . $profile->cp_icon : $profile->icon;
                    if (isset($profile->icon_svg) && $profile->icon_svg != '') {
                        //$profile->icon = '';
                    }
                } else if (in_array($profile->profile_code, is_free_profile_btn())) {
                    if ($profile->cp_title != '' && $profile->cp_title !=  NULL) {
                        $profile->title = $profile->title_de = $profile->cp_title;
                    }

                    $profile->custom_icon_svg = null;
                }

                unset($profile->cp_icon, $profile->cp_title, $profile->file_image, $profile->link_type_id,);

                $profile->added_to_contact_card = 'no';
                if ($profile->is_business == 0) {
                    $profile->added_to_contact_card = in_array($profile->id, $personal_profile_ids) ? 'yes' : 'no';
                } else if ($profile->is_business == 1) {
                    $profile->added_to_contact_card = in_array($profile->id, $business_profile_ids) ? 'yes' : 'no';
                }

                $ContactCardTotal = 1;
                if ($profile->profile_code == 'contact-card') {
                    $is_business = $token->profile_view == 'business' ? 1 : 0;
                    $ContactCard = ContactCard::where('user_id', $profile->user_id)->where('is_business', $is_business);

                    $ContactCardTotal = $ContactCard->count();

                    if ($ContactCardTotal != 0) {
                        $ContactCard = $ContactCard->first();
                        $customer_profile_ids = isset($ContactCard->customer_profile_ids) ? $ContactCard->customer_profile_ids : 0;
                        $CustomerProfile = CustomerProfile::whereIn('id', explode(',', $customer_profile_ids));
                        if ($CustomerProfile->count() == 0) {
                            $ContactCardTotal = 0;
                        }
                    }
                }

                if (isset($profile->visible)) {
                    $profile->visible = ($profile->visible == 1) ? true : false;
                }

                if (isset($profile->is_highlighted)) {
                    $profile->is_highlighted = ($profile->is_highlighted == 1) ? true : false;
                }

                // template
                $query = \DB::table('template_assignees AS ta')->select('cpt.user_template_id', 'cpt.is_unique');
                $query->leftJoin('customer_profile_templates AS cpt', 'cpt.id', '=', 'ta.customer_profile_template_id');
                $query->where('ta.customer_profile_id', $profile->id);
                if ($query->count() > 0) {
                    $rec = $query->first();
                    $profile->template_id = (int) $rec->user_template_id;
                    $profile->is_unique = (int) $rec->is_unique;
                } else {
                    $profile->template_id = 0;
                    $profile->is_unique = 0;
                }

                $profile->icon_url = $profile->icon;
                // if ($profile->icon_url != '' && isset($profile->profile_icon_svg_default)) {

                    if ((!property_exists($profile, 'icon_svg') || $profile->icon_svg == '') && isset($profile->profile_icon_svg_default)) {
                        $profile->icon_svg = $profile->profile_icon_svg_default;
                    }
                // if ($profile->icon_svg == '' && isset($profile->profile_icon_svg_default)) {
                //     $profile->icon_svg = $profile->profile_icon_svg_default;
                // }

                if (isset($profile->profile_icon_svg_default)) {
                    unset($profile->profile_icon_svg_default);
                }
                unset(
                    $profile->file_image,
                    $profile->status,
                    $profile->created_by,
                    $profile->created_at,
                    $profile->updated_by,
                    $profile->is_pro
                );

                if ($ContactCardTotal != 0) {
                    $my_recs[] = $profile;
                }
            }
        }

        return $my_recs;
    }

    public function tap_view(Request $request)
    {
        if (!empty($request->latitude) && !empty($request->longitude) && trim($request->latitude) != '' && trim($request->longitude) != '') {
            $token = $request->user();
            $_Obj = new TapsViews;
            $_Obj->user_id = $request->user_id;
            $_Obj->is_tap_view = 'v';
            $_Obj->type = 'main-profile';
            $_Obj->type_id = $request->user_id;
            $_Obj->lat = $request->latitude;
            $_Obj->lng = $request->longitude;
            $_Obj->created_by = 0;
            $_Obj->save();
        }

        $data['success'] = TRUE;
        $data['message'] = 'Profiles';
        return response()->json($data, 201);
    }

    public function analytics(Request $request)
    {
        $user_id = $request->user_id;

        $taps = TapsViews::where('type_id', $user_id)->where('type', 'main-profile')->where('is_tap_view', 't');
        $taps = $taps->count();

        $views = TapsViews::where('type_id', $user_id)->where('type', 'main-profile')->where('is_tap_view', 'v');
        $views = $views->count();

        $lastest_taps = TapsViews::where('type_id', $user_id)->where('type', 'main-profile')->where('is_tap_view', 't')->limit(20)->get();

        $days = getLastNDays(7, 'd M Y');

        $this_week_taps = TapsViews::select(\DB::raw("COUNT(1) AS total"), \DB::raw("DATE_FORMAT(created_at,'%d %b %Y') as rec_date"))->where('type_id', $user_id)->where('type', 'main-profile')->where('is_tap_view', 't')->whereBetween("created_at", [date('Y-m-d 00:00:01', strtotime('-6 days')), date('Y-m-d 11:59:59')])->groupBy(\DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"))->get();

        if (!empty($this_week_taps)) {
            foreach ($this_week_taps as $rec) {
                $days[str_replace(' ', '', $rec->rec_date)]['total'] = $rec->total;
            }
        }

        $this_week_taps = [];
        foreach ($days as $d) {
            $d['day'] = date('l, d M Y', strtotime($d['day']));
            $this_week_taps[] = $d;
        }

        //pre_print($this_week_taps);
        $data['success'] = TRUE;
        $data['message'] = 'Analytics';
        $data['data'] = array('taps' => $taps, 'views' => $views, 'lastest_taps' => $lastest_taps, 'this_week_taps' => $this_week_taps);
        return response()->json($data, 201);
    }

    public function add_business_request(Request $request)
    {
        $validations['first_name'] = 'required';
        $validations['last_name'] = 'required';
        $validations['email'] = 'required|string|email';
        $validations['phone_no'] = 'required';
        $validations['company'] = 'required';
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

        $Obj = new BusinessRequest;
        $Obj->first_name = $request->first_name;
        $Obj->last_name = $request->last_name;
        $Obj->email = $request->email;
        $Obj->phone_no = $request->phone_no;
        $Obj->message = $request->message;
        $Obj->company = $request->company;
        $Obj->created_by = $request->user_id;
        $Obj->save();

        if (strtolower(config("app.name", "")) != 'addmee') {
            // code
        } else {
            $html = '<p style="font-family:sans-serif;padding-left: 30px;">Vorname: ' . $Obj->first_name . '<br><br>Nachname: ' . $Obj->last_name . '<br><br>Email: ' . $Obj->email . '<br><br>Telefon-Nr: ' . $Obj->phone_no . '<br><br>Unternehmen: ' . $Obj->company . '<br><br> Nachricht: ' . $Obj->message . '<br><br></p>';
            // echo $html;exit;

            $datetime = new DateTime('now', new DateTimeZone('Europe/Berlin'));
            $date = $datetime->format('Y-m-d');
            $time = $datetime->format('h:i A');
            $subject = 'Business-Anfrage vom ' . $date . ' um ' . $time . ' von ' . $Obj->first_name . ' ' . $Obj->last_name . '';
            // Business request from [Date] at [Time] by [First Name] [Last Name]

            $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
            $body = [
                'Messages' => [
                    [
                        'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                        'To' => [['Email' => 'business@addmee.de', 'Name' => 'AddMee']],
                        'Subject' => $subject,
                        'TextPart' => $subject,
                        'HTMLPart' => $html,
                        'CustomID' => config("app.name", "")
                    ]
                ]
            ];

            $response = $mj->post(Resources::$Email, ['body' => $body]);
        }

        $data['success'] = TRUE;
        $data['message'] = 'Request sent successfully.';
        $data['data'] = array('business_request' => $Obj);
        return response()->json($data, 201);
    }

    public function test_email()
    {
        $html = 'Hi ,<br><br> We received a request to delete your account. Please use the following code to confirm.';
        $User = [];
        Mail::send([], [], function ($message) use ($html, $User) {
            $message
                ->to('mbilal.pg@gmail.com')
                ->subject(config("app.name", "") . ": Delete Account OTP")
                ->from("no-reply@tapmee.co")
                ->setBody($html, 'text/html');
        });
    }

    public function test_save_form(Request $request)
    {
        // $validations['merchant_name'] = 'required|string';
        // $validations['merchant_location'] = 'required|string';
        // $validations['customer_name'] = 'required|string';
        // $validations['customer_phone'] = 'required|string';
        // $validations['customer_address'] = 'required|string';

        // $validator = Validator::make($request->all(), $validations);

        // if ($validator->fails()) {
        //     $messages = json_decode(json_encode($validator->messages()), true);
        //     $i = 0;
        //     foreach ($messages as $key => $val) {
        //         $data['errors'][$i]['error'] = $val[0];
        //         $data['errors'][$i]['field'] = $key;
        //         $i++;
        //     }

        //     $data['success'] = FALSE;
        //     $data['message'] = 'Required fields are missing.';
        //     $data['data'] = (object)[];
        //     return response($data, 400);
        // }


        // $profile = new Profile;
        // $profile->merchant_name = $request->merchant_name;
        // $profile->merchant_location = $request->merchant_location;
        // $profile->type_of_equipment = $request->type_of_equipment;
        // $profile->customer_name = $request->customer_name;
        // $profile->customer_phone = $request->customer_phone;
        // $profile->customer_address = $request->customer_address;
        // $profile->save();

        $mj = new \Mailjet\Client('4133c35d76d4c148cad9d63efa8ed0cc', 'c0e0cc12628811dfea4a53ccd3f95f7f', true, ['version' => 'v3.1']);
        $subject =  'Reset Password OTP';
        $html = 'We received a request to reset your password.';
        $body = [
            'Messages' => [
                [
                    'From' => ['Email' => "no-reply@addmee.de", 'Name' => config("app.name", "")],
                    'To' => [['Email' => 'mbilal.pg@gmail.com', 'Name' => 'Bilal Khan']],
                    'Subject' => $subject,
                    'TextPart' => $subject,
                    'HTMLPart' => $html,
                    'CustomID' => config("app.name", "")
                ]
            ]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $data['success'] = TRUE;
        $data['message'] = 'Form submitted successfully.';
        $data['data'] = array('profile' => $response);
        return response()->json($data, 201);
    }

    public function update_browser_language(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['language'] = $request->language;
        // session(['language' => $request->language]);

        echo 1;
    }

    public function mapCode(Request $request)
    {
        $request->validate([
            'json_data' => 'required|file|max:1024', // Example: Max file size of 1MB
        ]);

        $upload_dir = icon_dir();
        $date = date('Ymd');
        $response = upload_file($request, 'json_data', $upload_dir . '/' . $date);
        if ($response['success'] == FALSE) {
            $data['success'] = $response['success'];
            $data['message'] = $response['message'];
            $data['data'] = [];
            return response()->json($data, 201);
        }

        $filename = '';
        if ($response['success'] == TRUE && $response['filename'] != '') {
            $filename = $date . '/' . $response['filename'];
        } else {
            $data['success'] = false;
            $data['message'] = 'Error Occured.';
            $data['data'] = [];
            return response()->json($data, 201);
        }

        // $path = $request->file('file')->store('public');
        // $fileContents = file_get_contents(storage_path('app/' . $path));
        $fileContents = file_get_contents($upload_dir . $filename);
        // pre_print(json_decode($fileContents));
        $codes = json_decode($fileContents);
        $mapped_codes = $unmapped_codes = [];
        foreach ($codes as $rec) {
            $code = UniqueCode::where('str_code', $rec->TableURL);
            if ($code->count() == 0) {
                $unmapped_codes[] = $rec->TableURL;
                continue;
            }

            $code = $code->first();
            if ($code->status == 0) {
                $unmapped_codes[] = $rec->TableURL;
                continue;
            } else if ($code->user_id > 0) {
                $unmapped_codes[] = $rec->TableURL;
                continue;
            } else if ($code->activated == 1 && $code->user_id == 0) {
                $unmapped_codes[] = $rec->TableURL;
                continue;
            } else {

                $User = User::where('username', $rec->Link);
                if ($User->count() > 0) {
                    $User = $User->first();
                    $user_id = $User->id;
                    $code->status = 1;
                    $code->activated = 1;
                    $code->user_id = $user_id;
                    $code->device = 'N/A';
                    $code->activation_date = date('Y-m-d H:i:s');
                    $code->save();

                    $mapped_codes[] = $rec->TableURL;
                } else {
                    $unmapped_codes[] = $rec->TableURL;
                }
            }
        }

        $data['success'] = TRUE;
        $data['message'] = 'Code';
        $data['data'] = ['mapped_codes' => count($mapped_codes), 'unmapped_codes' => $unmapped_codes];
        return response()->json($data, 201);
    }

    public function apple_wallet_pass(Request $request)
    {
        $user_id = $request->user_id;
        $language = 'en';
        if (isset($_GET['language']) && trim($_GET['language']) != '') {
            $language = trim($_GET['language']);
        }

        $User = User::where('id', $user_id);
        if ($User->count() == 0) {
            $data['success'] = false;
            $data['message'] = 'Invalid User ID.';
            $data['data'] = [];
            return response()->json($data, 404);
        }

        $User = $User->first();
        $pass = new PKPass(root_dir() . 'apple-certificates/Certificates.p12', 'Test');
        $profile_photo = $User->logo != '' && file_exists(icon_dir() . $User->logo) ? image_url($User->logo) : '';
        // Pass content
        $data = [
            "description" => "AddMee",
            "formatVersion" => 1,
            "organizationName" => "AddMee",
            "passTypeIdentifier" => "pass.Addmeewallet.com", // Change this!
            "teamIdentifier" => "3L3ZFNBVLZ", // Change this!
            "serialNumber" => "42954451241",
            "generic" => [
                "headerFields" => [
                    [
                        "key" => "header",
                        "label" => "",
                        "value" => ""
                    ]
                ],
                "primaryFields" => [
                    [
                        "key" => "origin",
                        "label" => "Name",
                        "value" => $User->first_name . " " . $User->last_name,
                        "textColor" => "rgb(0, 0, 0)",
                        "labelColor" => "rgb(90, 90, 90)",
                        "valueColor" => "rgb(0, 0, 0)",
                        "font" => array(
                            "fontSize" => 10  // Set the desired font size here
                        ),
                        "textAlignment" => "PKTextAlignmentNatural", // Adjusted textAlignment
                    ],
                ],
                "secondaryFields" => [
                    [
                        "key" => "date",
                        "label" => "Designation",
                        "value" => $User->designation,
                        "textColor" => "rgb(0, 0, 0)",
                        "labelColor" => "rgb(90, 90, 90)",
                        "valueColor" => "rgb(0, 0, 0)",
                        "textAlignment" => "PKTextAlignmentLeft",
                    ],
                ],
                "auxiliaryFields" => [
                    [
                        "key" => "date1",
                        "label" => "Company",
                        "value" => $User->company_name,
                        "textColor" => "rgb(0, 0, 0)",
                        "labelColor" => "rgb(90, 90, 90)",
                        "valueColor" => "rgb(0, 0, 0)",
                        "textAlignment" => "PKTextAlignmentLeft"
                    ],
                    [
                        "key" => "date2",
                        "value" => "",
                        "textAlignment" => "PKTextAlignmentLeft"
                    ]
                ],
                "backFields" => [
                    [
                        "key" => "notificationMessage",
                        "label" => "Latest Updates",
                        "value" => "\nLog in on the AddMee App to edit your AddMee Card and profile.\n\nWelcome to AddMee!"
                    ]
                ],
                "transitType" => "PKTransitTypeGeneric"
            ],
            "barcode" => [
                "format" => "PKBarcodeFormatQR",
                "message" => main_url() . '/' . $User->username,
                "messageEncoding" => "iso-8859-1"
            ],
            "backgroundColor" => "rgb(255, 255, 255)",
            "foregroundColor" => "rgb(0, 0, 0)",
            "labelColor" => "rgb(90, 90, 90)",
            "valueColor" => "rgb(0, 0, 0)",
            // "font" => [
            //     "family" => "Helvetica",
            //     "name" => "Helvetica-Bold",
            //     "size" => 15
            // ],
            // 'logoText' => 'AddMee',
            // 'relevantDate' => date('Y-m-d\TH:i:sP'),
            "thumbnail" => $profile_photo,
            "ThumbNail" => $profile_photo
        ];

        $pass->setData($data);

        // Add files to the pass package
        $pass->addFile('img/icon.png');
        $pass->addFile('img/icon@2x.png');
        $pass->addFile('img/logo.png');

        // Create and output the pass
        // $pass->create(true);
        $passPackage = $pass->create();
        return response($passPackage)
            ->header('Content-Type', 'application/vnd.apple.pkpass')
            ->header('Content-Disposition', 'attachment; filename="pass.pkpass"');
    }

    public function send_test_notifications_curl()
    {
        // https://documentation.onesignal.com/reference/create-notification
        $curl = curl_init();
        $APP_ID = "e3650ec2-db36-4dd9-99be-e0eee461ea6b";
        $REST_API_KEY = "MjQzMjQxMmItYjZiMy00YmEyLTk3NWYtYzZhODRiNjcwYjk3";

        $body = ["app_id" => $APP_ID, "included_segments" => ["Subscribed Users"], "contents" => ["en" => "English or Any Language Message", "es" => "Spanish Message"], "name" => "INTERNAL_CAMPAIGN_NAME"];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://onesignal.com/api/v1/notifications",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic " . $REST_API_KEY,
                "accept: application/json",
                "content-type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }

    private function brand_profiles($username)
    {
        $query = \DB::table('customer_profiles AS cp')->select('cp.id', 'cp.profile_link', 'cp.profile_code', 'cp.icon as cp_icon', 'cp.title as cp_title', 'p.title', 'p.title_de', 'p.icon', 'p.base_url', 'u.username', 'u.name', 'u.subscription_expires_on', 'u.profile_view', 'cp.is_business', 'cp.file_image', 'cp.sequence', 'cp.user_id', 'p.is_pro', 'p.type', 'u.first_name', 'u.last_name', 'u.company_name', 'u.designation');
        $query->Join('profiles AS p', 'p.profile_code', '=', 'cp.profile_code');
        $query->Join('users AS u', 'u.id', '=', 'cp.user_id');
        $query = $query->where('username', $username)->where('p.status', 1);

        $Obj = User::where('username', $username);
        if ($Obj->count() == 0) {
            return [];
        }
        $Obj = $Obj->first();
        $has_subscription = chk_subscription($Obj);
        // if ($has_subscription['success'] == false) {
        // $query = $query->where('p.is_pro', 0);
        // }

        $query->orderBy('cp.sequence', 'ASC');
        $query->orderBy('cp.id', 'ASC');
        $profiles = $query->get();

        $my_recs = [];
        if (count($profiles) > 0) {
            foreach ($profiles as $i => $profile) {

                // if($profile->profile_view == 'business' && $profile->is_business == 0){
                // 	unset($profiles[$i]);
                // 	continue;
                // }

                // if($profile->profile_view == 'personal' && $profile->is_business == 1){
                // 	unset($profiles[$i]);
                // 	continue;
                // }

                if ($profile->profile_code == 'whatsapp') {
                    $profile->profile_link = trim($profile->profile_link, '+');
                }

                $profile->icon = icon_url() . $profile->icon;
                $contact_link = main_url() . "/contact-card/" . encrypt($profile->user_id);

                $profile->profile_link_value = $profile->profile_link;
                $profile->profile_link = ($profile->profile_code != 'contact-card') ? $profile->profile_link : $contact_link;

                if ($profile->profile_code == 'www' || $profile->type == 'url') {
                    $is_valid_url = false;
                    if (substr($profile->profile_link, 0, 8) == "https://" || substr($profile->profile_link, 0, 7) == "http://") {
                        $is_valid_url = true;
                    }

                    if ($is_valid_url == false) {
                        $profile->profile_link = 'http://' . $profile->profile_link;
                    }
                }

                $profile->profile_link = ($profile->profile_code != 'file') ? $profile->base_url . $profile->profile_link : file_url() . $profile->file_image;

                if ($has_subscription['success'] == true) {
                    if ($profile->cp_title != '' && $profile->cp_title !=  NULL) {
                        $profile->title = $profile->title_de = $profile->cp_title;
                    }

                    $profile->icon = ($profile->cp_icon != '' && $profile->cp_icon != NULL) ? icon_url() . $profile->cp_icon : $profile->icon;
                }

                unset($profile->cp_icon, $profile->cp_title, $profile->subscription_expires_on, $profile->file_image);

                $ContactCardTotal = 1;
                if ($profile->profile_code == 'contact-card') {
                    // $is_business = $profile->profile_view == 'business' ? 1 : 0;
                    // $ContactCard = ContactCard::where('user_id', $profile->user_id)->where('is_business', $is_business);
                    // if ($ContactCard->count() == 0) {
                    //     $ContactCardTotal = 0;
                    // } else {
                    //     $ContactCard = $ContactCard->first();
                    //     $customer_profile_ids = isset($ContactCard->customer_profile_ids) ? $ContactCard->customer_profile_ids : 0;
                    //     $CustomerProfile = CustomerProfile::whereIn('id', explode(',', $customer_profile_ids));
                    //     if ($CustomerProfile->count() == 0) {
                    //         $ContactCardTotal = 0;
                    //     }
                    // }
                    $ContactCardTotal = 0;
                    unset($profiles[$i]);
                }

                if ($ContactCardTotal != 0) {
                    $my_recs[] = $profile;
                }
            }
        }

        return $my_recs;
    }


    public function createCustomProperty()
    {
        // Replace 'YOUR_API_KEY' with your actual HubSpot API key
        $apiKey = 'COOml-LRMRIOAAEAQAAAwRAAAAAAAAUYyIaaFSCVzMUdKPWflgEyFE3bMk_TZmEzrv2m4ASYP12rzUzkOjAAAABBAAAAAAAAAAAAAAAAAIAAAAAAAAAAAAAgAAgAMADgCQAAAAAAAAAAAACQEAJCFPHq1Lk2ttYMyQ1g_aAr9ckpizgJSgNuYTFSAFoA';
        // $apiKey = 'CNK4q4rRMRIHAAEAQAAAARjIhpoVIJXMxR0o9Z-WATIUtSMcE5VKsuYWVIH76zhxB-lqR1M6MAAAAEEAAAAAAAAAAAAAAAAAgAAAAAAAAAAAACAAAAAAAOABAAAAAAAAAAAAAAAQAkIUC9e-JP_chFAdxhDFAKDZVtv2mmNKA25hMVIAWgA';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey,
        ])->post('https://api.hubapi.com/crm/v3/properties/contact', [
            'name' => 'CustomProperty',
            'label' => 'Custom Property',
            'description' => 'A custom property for contacts',
            'type' => 'string', // Adjust the type as needed (e.g., string, date, number)
            'fieldType' => 'text', // Adjust the field type as needed (e.g., text, single-line-text, multi-line-text)
            'formField' => false, // Set to true if you want the property to appear in forms
            'displayOrder' => 6, // Adjust the display order as needed

        ]);

        // Handle the response
        if ($response->successful()) {
            // Property created successfully
            $propertyData = $response->json();
            // Additional handling if needed
            return response()->json($propertyData);
        } else {
            // Error handling
            $errorMessage = $response->json('message');
            // Handle the error accordingly
            return response()->json(['error' => $errorMessage], $response->status());
        }
    }
}
