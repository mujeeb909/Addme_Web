<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerProfile;
use App\Models\CustomerProfileTemplate;
use App\Models\Profile;
use App\Models\TemplateAssignee;
use App\Models\User;
use App\Models\UserSettings;
use App\Models\UserTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TemplateController extends Controller
{
    public function list_template(Request $request)
    {
        $token = $request->user();
        $parent_id = parent_id($token);
        $UserTemplates = UserTemplate::where('user_id', $parent_id);

        if ($UserTemplates->count() > 0) {
            $UserTemplates = $UserTemplates->get();
            foreach ($UserTemplates as $idx => $template) {
                $templateObj = templateObj($template);
                unset($UserTemplates[$idx]);
                $UserTemplates[$idx] = $templateObj;
            }

            $data['success'] = TRUE;
            $data['message'] = count($UserTemplates) . ' templates found.';
            $data['data'] = ['templates' => $UserTemplates];
            return response($data, 201);
        } else {
            $data['success'] = TRUE;
            $data['message'] = 'No template added yet.';
            $data['data'] = ['templates' => []];
            return response($data, 200);
        }
    }

    public function create_template(Request $request)
    {
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
            $data['message'] = 'Required data is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $parent_id = parent_id($token);
        $company_logo = $profile_image = $profile_banner = $background_image = '';

        $upload_dir = icon_dir();
        $date = date('Ymd');
        $images = ['company_logo', 'profile_image', 'profile_banner', 'background_image'];
        foreach ($images as $image) {
            if (isset($_FILES[$image]) && $_FILES[$image]['name'] != '') {

                $response = upload_file($request, $image, $upload_dir . '/' . $date);
                if ($response['success'] == FALSE) {
                    $data['success'] = $response['success'];
                    $data['message'] = $response['message'];
                    $data['data'] = (object)[];
                    return response()->json($data, 200);
                }

                if ($response['filename'] != '') {
                    $$image = $date . '/' . $response['filename'];
                }
            }
        }

        $UserTemplate = new UserTemplate();
        $UserTemplate->name = $request->name;
        $UserTemplate->user_id = $parent_id;
        $UserTemplate->company_name = $request->company_name;
        $UserTemplate->company_address = $request->company_address;
        $UserTemplate->company_logo = $company_logo;
        $UserTemplate->profile_image = $profile_image;
        $UserTemplate->profile_banner = $profile_banner;
        $UserTemplate->subtitle = $request->subtitle;
        $UserTemplate->section_color = $request->section_color;
        $UserTemplate->profile_color = $request->profile_color;
        $UserTemplate->border_color = $request->border_color;
        $UserTemplate->background_color = $request->background_color;
        $UserTemplate->button_color = $request->button_color;
        $UserTemplate->text_color = $request->text_color;
        $UserTemplate->photo_border_color = $request->photo_border_color;
        $UserTemplate->background_image = $background_image;
        $UserTemplate->color_link_icons = $request->color_link_icons;
        // $UserTemplate->show_contact = $request->show_contact;
        // $UserTemplate->show_connect = $request->show_connect;
        $UserTemplate->is_editable = ($request->has('is_editable')) ? $request->is_editable : 1;
        $UserTemplate->capture_lead = ($request->has('capture_lead')) ? $request->capture_lead : 0;
        $UserTemplate->open_direct = ($request->has('open_direct')) ? $request->open_direct : 0;
        $UserTemplate->created_by = $token->id;
        if ($request->has('save_contact_button')) {
            $UserTemplate->show_contact = $request->save_contact_button;
        } else {
            $UserTemplate->show_contact = 1;
        }

        if ($request->has('connect_button')) {
            $UserTemplate->show_connect = $request->connect_button;
        } else {
            $UserTemplate->show_connect = 1;
        }
        // pre_print(json_decode(json_encode($UserTemplate)));
        if ($request->has('is_default')) {
            $UserTemplate->is_default = $request->is_default;
            if ($request->is_default == 1) {
                $defaultTemplate = UserTemplate::where('user_id', $parent_id)->where('is_default', 1);
                if ($defaultTemplate->count() != 0) {
                    $defaultTemplate = $defaultTemplate->first();
                    $defaultTemplate->is_default = 0;
                    $defaultTemplate->save();
                } else {
                    $UserTemplate->is_default = 1;
                }
            }
        } else {
            $defaultTemplate = UserTemplate::where('user_id', $parent_id)->where('is_default', 1);
            if ($defaultTemplate->count() == 0) {
                $UserTemplate->is_default = 1;
            }
        }

        $UserTemplate->save();
        $UserTemplate->save_contact_button = true_false($UserTemplate->show_contact);
        $UserTemplate->connect_button = true_false($UserTemplate->show_connect);
        $UserTemplate->capture_lead = true_false($UserTemplate->capture_lead);
        $UserTemplate->open_direct = true_false($UserTemplate->open_direct);
        $UserTemplate->is_editable = true_false($UserTemplate->is_editable);
        $UserTemplate->is_default = true_false($UserTemplate->is_default);

        // create profiles
        $links = [];
        if ($request->has('links')) {
            $records = json_decode($request->links);
            if (!empty($records)) {
                foreach ($records as $rec) {
                    if ($rec->profile_code != '') {
                        $Profile = Profile::where('profile_code', $rec->profile_code);
                        if ($Profile->count() > 0) {
                            $links[] = createOrUpdateTemplateProfile($rec, $UserTemplate->id, $token, 'add');
                        }
                    }
                }
            }
        }

        $UserTemplate->settings = templateSettingsObject($UserTemplate);
        $UserTemplate = unsetTemplateObjValue($UserTemplate);

        $data['success'] = TRUE;
        $data['message'] = 'Template created successfully.';
        $data['data'] = array('template' => $UserTemplate, 'links' => $links, 'assignees_ids' => []);
        return response()->json($data, 201);
    }

    public function update_template(Request $request)
    {
        $token = $request->user();
        $parent_id = parent_id($token);

        $isDataExists = templateExists($request->template_id, $parent_id);
        if ($isDataExists['success'] == false) {
            return response($isDataExists, 400);
        } else {
            $UserTemplate = $isDataExists['template'];
        }

        $upload_dir = icon_dir();
        $date = date('Ymd');
        $images = ['company_logo', 'profile_image', 'profile_banner', 'background_image'];
        foreach ($images as $image) {
            if ($request->hasFile($image) && $request->file($image)->isValid()) {

                $response = upload_file($request, $image, $upload_dir . '/' . $date);
                if ($response['success'] == FALSE) {
                    $data['success'] = $response['success'];
                    $data['message'] = $response['message'];
                    $data['data'] = (object)[];
                    return response()->json($data, 200);
                }

                if ($response['filename'] != '') {
                    $UserTemplate->$image = $date . '/' . $response['filename'];
                }
            } else {
                if (isset($_FILES[$image]) && $_FILES[$image]['name'] == '') {
                    $UserTemplate->$image = NULL;
                }

                if ($request->has($image) && $request->$image == '') {
                    $UserTemplate->$image = NULL;
                }
            }
        }

        if ($request->has('name')) {
            $UserTemplate->name = $request->name;
        }
        if ($request->has('company_name')) {
            $UserTemplate->company_name = $request->company_name;
        }
        if ($request->has('company_address')) {
            $UserTemplate->company_address = $request->company_address;
        }

        if ($request->has('subtitle')) {
            $UserTemplate->subtitle = $request->subtitle;
        }
        if ($request->has('section_color')) {
            $UserTemplate->section_color = $request->section_color;
        }
        if ($request->has('profile_color')) {
            $UserTemplate->profile_color = $request->profile_color;
            $UserTemplate->background_color = $request->profile_color;
        }
        if ($request->has('border_color')) {
            $UserTemplate->border_color = $request->border_color;
            $UserTemplate->photo_border_color = $request->border_color;
        }
        if ($request->has('background_color')) {
            $UserTemplate->background_color = $request->background_color;
        }
        if ($request->has('button_color')) {
            $UserTemplate->button_color = $request->button_color;
        }
        if ($request->has('text_color')) {
            $UserTemplate->text_color = $request->text_color;
        }
        if ($request->has('photo_border_color')) {
            $UserTemplate->photo_border_color = $request->photo_border_color;
            $UserTemplate->photo_border_color = $request->border_color;
        }

        if ($request->has('color_link_icons')) {
            $UserTemplate->color_link_icons = $request->color_link_icons;
        }
        if ($request->has('show_contact')) {
            $UserTemplate->show_contact = $request->show_contact;
        }
        if ($request->has('show_connect')) {
            $UserTemplate->show_connect = $request->show_connect;
        }
        if ($request->has('is_editable')) {
            $UserTemplate->is_editable = $request->is_editable;
        }
        if ($request->has('capture_lead')) {
            $UserTemplate->capture_lead = $request->capture_lead;
        }
        if ($request->has('is_default')) {
            $UserTemplate->is_default = $request->is_default;
        }
        if ($request->has('open_direct')) {
            $UserTemplate->open_direct = $request->open_direct;
        }
        $UserTemplate->updated_by = $token->id;
        // pre_print(json_decode(json_encode($UserTemplate)));
        $UserTemplate->save();

        // $this->update_template_account_data($UserTemplate, $token);

        // create profiles
        $links = [];
        if ($request->has('links')) {
            $records = json_decode($request->links);
            // pre_print($records);
            if (!empty($records)) {
                foreach ($records as $rec) {
                    if (isset($rec->id) && $rec->id != '' && $rec->id != '0' && $rec->id != 0) {
                        $Obj = createOrUpdateTemplateProfile($rec, $UserTemplate->id, $token, 'update');

                        if (count($Obj) > 0) {
                            $links[] = $Obj;
                            $this->update_template_profile_data($Obj, $UserTemplate->id, $token, 'update');
                        }
                    } elseif ((!isset($rec->id) || $rec->id == '') && $rec->profile_code != '') {

                        $Obj = createOrUpdateTemplateProfile($rec, $UserTemplate->id, $token, 'add');
                        $links[] = $Obj;
                        $this->update_template_profile_data($Obj, $UserTemplate->id, $token, 'add');
                    }
                }
            }
        }

        $UserTemplate->settings = templateSettingsObject($UserTemplate);
        $UserTemplate->company_logo = image_url($UserTemplate->company_logo);
        $UserTemplate->profile_image = image_url($UserTemplate->profile_image);
        $UserTemplate->profile_banner = image_url($UserTemplate->profile_banner);

        $UserTemplate = unsetTemplateObjValue($UserTemplate);

        $data['success'] = TRUE;
        $data['message'] = 'Template updated successfully.';
        $data['data'] = array('template' => $UserTemplate, 'links' => $links, 'assignees_ids' => arrayValuesToInt(getAssigneeIDs($request->template_id)));
        return response()->json($data, 201);
    }

    public function create_template_profile(Request $request)
    {

        $validations['code'] = 'required|string';
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
        $parent_id = parent_id($token);

        $isDataExists = templateExists($request->template_id, $parent_id);
        if ($isDataExists['success'] == false) {
            return response($isDataExists, 400);
        }

        $Profile = Profile::where('profile_code', $request->code);
        $hasIcon = false;
        if ($Profile->count() > 0) {
            $Obj = new CustomerProfileTemplate();
            $Obj->user_template_id = $request->template_id;
            $Obj->profile_link = $request->has('value') ? ($request->value != '' ? $request->value : '') : '';
            $Obj->profile_code = $request->code;
            $Obj->status = $request->has('status') ? $request->status : 1;
            $Obj->title = $request->has('title') ? $request->title : '';
            $Obj->sequence = maxTemplateProfileSequence($request->template_id);
            $Obj->is_focused = $request->has('is_highlighted') ? $request->is_highlighted : 0;
            $Obj->is_unique = $request->has('is_unique') ? $request->is_unique : 0;
            $Obj->created_by = $token->id;

            if ($request->hasFile('icon') && $request->file('icon')->isValid()) {

                $upload_dir = icon_dir();
                $date = date('Ymd');
                $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
                if ($response['success'] == FALSE) {
                    $response['data'] = (object)[];
                    return response()->json($response, 200);
                }

                if ($response['filename'] != '') {
                    $Obj->icon =  $date . '/' . $response['filename'];
                    $hasIcon = true;
                }
            }

            // new svg method

            // if ($Obj->icon != '' && $Obj->icon != NULL) {
            //     $Obj->icon = $Obj->icon;
            // } else {
            //     if (!empty($request->icon_svg) && !empty($Profile->first()->icon_svg_default)) {
            //         $Obj->icon_svg = $request->icon_svg;
            //     } else {
            //         $Obj->icon = $Profile->first()->icon;
            //     }
            // }

            // end new svg method

            if ($request->has('icon_svg') && $request->icon_svg != '') {
                $Obj->icon_svg = $request->icon_svg;
                $Obj->icon = '';
                $hasIcon = false;
            }

            $Obj->save();

            // assign new link to user
            $profiles = TemplateAssignee::where('user_template_id', $request->template_id)->where('customer_profile_id', 0)->get();
            $member_profile_btns = [];
            if (!empty($profiles)) {
                foreach ($profiles as $profile) {

                    $ProfileObj = new CustomerProfile();
                    $ProfileObj->profile_link = $Obj->profile_link;
                    $ProfileObj->profile_code = $Obj->profile_code;
                    $ProfileObj->is_focused = $Obj->is_focused;
                    $ProfileObj->icon = $Obj->icon;
                    $ProfileObj->title = $Obj->title;
                    $ProfileObj->user_id = $profile->user_id;
                    $ProfileObj->status = $Obj->status == null ? 1 : $Obj->status;
                    $ProfileObj->sequence = maxSequence($profile->user_id);
                    $ProfileObj->is_default = 0;
                    $ProfileObj->created_by = $token->id;
                    if ($Obj->icon_svg != '') {
                        $ProfileObj->icon_svg_default = $Obj->icon_svg;
                    }
                    $ProfileObj->save();

                    $member_profile_btns[] = $ProfileObj->id;

                    $TemplateAssigneeObj = new TemplateAssignee();
                    $TemplateAssigneeObj->user_id = $profile->user_id;
                    $TemplateAssigneeObj->is_assigned = 1;
                    $TemplateAssigneeObj->user_template_id = $request->template_id;
                    $TemplateAssigneeObj->customer_profile_id = $ProfileObj->id;
                    $TemplateAssigneeObj->customer_profile_template_id = $Obj->id;
                    $TemplateAssigneeObj->created_by = $token->id;
                    $TemplateAssigneeObj->save();
                }
            }
            // end here

            $Obj = ProfileButton(0, $Obj, $Profile);
            if ($Obj->icon != '') {
                $Obj->icon_svg = icon_svg_default($request, $Obj->profile_code);
            }
            $assignees_ids = getAssigneeIDs($request->template_id);
            $member_links = template_links($assignees_ids, $Obj->id, $request);

            if ($hasIcon == false) {
                $Obj->icon_url = '';
            }


            $data['success'] = TRUE;
            $data['message'] = 'The template button has been updated successfully.';
            $data['data'] = array('template_link' => $Obj, 'member_links' => $member_links);
            return response()->json($data, 201);
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid profile code.';
            $data['data'] = (object)[];
            return response($data, 400);
        }
    }

    public function delete_template_profile(Request $request)
    {
        $token = $request->user();
        $parent_id = parent_id($token);
        $isDataExists = templateExists($request->template_id, $parent_id);
        if ($isDataExists['success'] == false) {
            return response($isDataExists, 400);
        }

        $member_links = [];
        $CustomerProfileTemplate = CustomerProfileTemplate::where('id', $request->template_profile_id)->where('user_template_id', $request->template_id);
        if ($CustomerProfileTemplate->count() > 0) {

            $forDeletion = TemplateAssignee::where('customer_profile_template_id', $request->template_profile_id);
            if ($forDeletion->count() > 0) {
                $forDeletion = $forDeletion->get();
                foreach ($forDeletion as $row) {
                    if ($row->customer_profile_id > 0) {
                        $member_links[] = $row->customer_profile_id;
                        CustomerProfile::where('id', $row->customer_profile_id)->delete();
                    }
                }
            }

            TemplateAssignee::where('customer_profile_template_id', $request->template_profile_id)->delete();
            $CustomerProfileTemplate->delete();

            $data['success'] = TRUE;
            $data['message'] = 'The button is removed from the template.';
            $data['data'] = array('template_link_id' => (int) $request->template_profile_id, 'member_link_ids' => $member_links);
            return response()->json($data, 201);
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'The template profile id not found.';
            $data['data'] = array('template_link_id' => (int) $request->template_profile_id, 'member_link_ids' => $member_links);
            return response()->json($data, 404);
        }
    }

    public function update_template_profile(Request $request)
    {
        $token = $request->user();
        $parent_id = parent_id($token);
        $isDataExists = templateExists($request->template_id, $parent_id);
        if ($isDataExists['success'] == false) {
            return response($isDataExists, 400);
        }

        $member_links = [];
        // create profiles
        $Obj = CustomerProfileTemplate::where('id', $request->template_profile_id);
        if ($Obj->count() > 0) {
            $Obj = $Obj->first();

            if ($request->has('title')) {
                $Obj->title = $request->title;
            }

            if ($request->has('value')) {
                $Obj->profile_link = $request->value == '' ? '' : $request->value;
            }

            if ($request->has('is_highlighted')) {
                $Obj->is_focused = $request->is_highlighted;
            }

            if ($request->has('visible')) {
                $Obj->status = $request->visible;
            }

            if ($request->hasFile('icon') && $request->file('icon')->isValid()) {

                $upload_dir = icon_dir();
                $date = date('Ymd');
                $response = upload_file($request, 'icon', $upload_dir . '/' . $date);
                if ($response['success'] == FALSE) {
                    $response['data'] = (object)[];
                    return response()->json($response, 200);
                }

                if ($response['filename'] != '') {
                    $Obj->icon = date('Ymd') . '/' . $response['filename'];
                    $Obj->icon_svg = NULL;
                }
            } else {
                if (isset($_FILES['icon']) && $_FILES['icon']['name'] == '') {
                    $Obj->icon = NULL;
                }
            }

            if ($request->has('icon_svg') && $request->icon_svg != '') {
                $Obj->icon_svg = $request->icon_svg;
                $Obj->icon = NULL;
            }

            if ($request->has('icon')) {
                if ($request->icon == '') {
                    $Obj->icon = NULL;
                }
            }

            $Obj->updated_by = $token->id;
            $Obj->save();

            $this->update_template_profile_data($Obj, $request->template_id, $token, 'update');
            $Obj = TemplateProfileButton(0, $Obj);
            $Obj->icon_svg = $Obj->icon_svg == null ? '' : $Obj->icon_svg;

            $assignees_ids = getAssigneeIDs($request->template_id);
            $member_links = template_links($assignees_ids, $Obj->id);
            unset($Obj->user_template_id, $Obj->is_unique, $Obj->profile_code);
            $_member_links = [];
            if (!empty($member_links)) {
                foreach ($member_links as $link) {
                    unset($link->profile_code, $link->user_id, $link->sequence, $link->global_id, $link->type, $link->is_unique, $link->template_id);
                    $link->icon_svg = $link->icon_svg == null ? '' : $link->icon_svg;
                    $_member_links[] = $link;
                }
            }
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid Profile.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $data['success'] = TRUE;
        $data['message'] = 'The template button has been updated successfully.';
        $data['data'] = array('template_link' => $Obj, 'member_links' => $_member_links);
        return response()->json($data, 201);
    }

    public function reorder_template_profiles(Request $request)
    {
        $validations['sequence'] = 'required|string';
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
        $parent_id = parent_id($token);

        $profile_ids = explode(',', $request->sequence);
        // pre_print($sequence);
        $isDataExists = templateExists($request->template_id, $parent_id);
        if ($isDataExists['success'] == false) {
            return response($isDataExists, 400);
        }

        $list = [];
        $profiles = CustomerProfileTemplate::where('user_template_id', $request->template_id)->whereIn('id', $profile_ids);
        if ($profiles->count() > 0) {
            $profiles = $profiles->get();
            foreach ($profiles as $Obj) {
                $key = array_search($Obj->id, $profile_ids);
                $Obj->sequence = $key;
                $Obj->updated_by = $token->id;
                $Obj->save();
                $list[] = ['id' => $Obj->id, 'sequence' => $key];
            }
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid Profile.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $data['success'] = TRUE;
        $data['message'] = 'The template buttons sequence has been updated successfully.';
        $data['data'] = ['templateLinks' => $list];
        return response()->json($data, 201);
    }

    public function assign_template(Request $request)
    {
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
            $data['message'] = 'Required data is missing.';
            $data['data'] = (object)[];
            return response($data, 400);
        }

        $token = $request->user();
        $parent_id = parent_id($token);

        $members = $request->member_ids;
        $member_ids = explode(',', $members);

        $this->assign_template_to_member($member_ids, $parent_id, $request->template_id, $token);

        $assignees_ids = TemplateAssignee::select(\DB::raw('GROUP_CONCAT(user_id) as user_id'))->where('user_template_id', $request->template_id)->where('customer_profile_id', 0)->first();
        $assignees_ids = $assignees_ids->user_id != null ? explode(',', $assignees_ids->user_id) : [];

        $data['success'] = TRUE;
        $data['message'] = 'Template assigned successfully.';
        $data['data'] = array('assignees_ids' => arrayValuesToInt($assignees_ids));
        return response()->json($data, 201);
    }

    public function assign_template_json(Request $request)
    {
        $token = $request->user();
        $parent_id = parent_id($token);
        $member_ids = [];
        if ($request->has('assignees_ids')) {
            $member_ids = $request->assignees_ids;
            // pre_print($member_ids);
        } else {
            $validations['assignees_ids'] = 'required';
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
        }

        $assignees_ids = getAssigneeIDs($request->template_id);
        $unassigned_member_ids = array_diff($assignees_ids, $member_ids);
        $new_member_ids = array_diff($member_ids, $assignees_ids);
        // pre_print($new_member_ids);

        if (!empty($unassigned_member_ids)) {
            foreach ($unassigned_member_ids as $user_id) {
                $this->unassign_template($request->template_id, $user_id);
            }
        }

        $this->assign_template_to_member($member_ids, $parent_id, $request->template_id, $token);
        $member_links = template_links($new_member_ids);
        $assignees_ids = getAssigneeIDs($request->template_id);

        if (!empty($assignees_ids)) {
            foreach ($assignees_ids as $user_id) {
                resetProfilesSequence($user_id);
            }
        }

        $members = [];
        $users = User::whereIn('id', $assignees_ids);
        if ($users->count() > 0) {
            $UserTemplate = UserTemplate::where('id', $request->template_id)->first();
            $users = $users->get();
            foreach ($users as $user) {
                $member = [];
                $member['id'] =  $user->id;
                $member['open_direct'] = notEmpty($UserTemplate->open_direct) ? (int) $UserTemplate->open_direct : (int)$user->open_direct;

                $user_settings = UserSettings::where('user_id', $user->id);
                if ($user_settings->count() > 0) {
                    $user_settings = $user_settings->first();

                    $member['save_contact_button'] = notEmpty($UserTemplate->show_contact) ? (int) $UserTemplate->show_contact : (int) $user_settings->show_contact;
                    $member['connect_button'] = notEmpty($UserTemplate->show_connect) ? (int) $UserTemplate->show_connect : (int)$user_settings->show_connect;
                    $member['capture_lead'] = notEmpty($UserTemplate->capture_lead)  ? (int) $UserTemplate->capture_lead : (int) $user_settings->capture_lead;

                    $colors = [];
                    $colors['id'] = $user_settings->id;
                    $colors['section_color'] = notEmpty($UserTemplate->section_color) ? $UserTemplate->section_color : $user_settings->section_color;
                    $colors['bg_color'] = notEmpty($UserTemplate->background_color) ? $UserTemplate->background_color : $user_settings->bg_color;
                    $colors['btn_color'] = notEmpty($UserTemplate->button_color) ? $UserTemplate->button_color : $user_settings->btn_color;
                    $colors['photo_border_color'] = notEmpty($UserTemplate->photo_border_color) ? $UserTemplate->photo_border_color : $user_settings->photo_border_color;
                    $colors['text_color'] = notEmpty($UserTemplate->text_color) ? $UserTemplate->text_color : $user_settings->text_color;
                    $colors['color_link_icons'] = notEmpty($UserTemplate->color_link_icons) ? true_false($UserTemplate->color_link_icons) : true_false($user_settings->color_link_icons);
                } else {
                    $colors = [];
                    $colors['id'] = $user->id;
                    $colors['section_color'] = $UserTemplate->section_color;
                    $colors['bg_color'] = $UserTemplate->background_color;
                    $colors['btn_color'] = $UserTemplate->button_color;
                    $colors['photo_border_color'] = $UserTemplate->photo_border_color;
                    $colors['text_color'] = $UserTemplate->text_color;
                    $colors['color_link_icons'] = true_false($UserTemplate->color_link_icons);

                    $member['save_contact_button'] = (int) $UserTemplate->show_contact;
                    $member['connect_button'] = (int) $UserTemplate->show_connect;
                    $member['capture_lead'] = (int) $UserTemplate->capture_lead;
                }

                $member['settings']['colors'] = $colors;
                $members[] = $member;
            }
        }

        $data['success'] = TRUE;
        if (count($assignees_ids) > 1) {
            $data['message'] = count($assignees_ids) . ' members assigned to the template.';
        } else {
            $data['message'] = count($assignees_ids) . ' member assigned to the template.';
        }
        $data['data'] = ['template' => ['id' => (int) $request->template_id, 'assignees_ids' => arrayValuesToInt($assignees_ids)], 'member_links' => $member_links]; //, 'members' => $members
        return response()->json($data, 201);
    }

    public function delete_template(Request $request)
    {
        $token = $request->user();
        $parent_id = parent_id($token);
        $member_links_ids = [];

        $UserTemplate = UserTemplate::where('id', $request->template_id)->where('user_id', $parent_id);
        if ($UserTemplate->count() > 0) {

            $UserTemplate = $UserTemplate->first();

            $forDeletion = TemplateAssignee::where('user_template_id', $request->template_id);
            if ($forDeletion->count() > 0) {
                $forDeletion = $forDeletion->get();
                foreach ($forDeletion as $row) {
                    if ($row->customer_profile_id > 0) {
                        CustomerProfile::where('id', $row->customer_profile_id)->delete();
                        $member_links_ids[] = (int)$row->customer_profile_id;
                    }
                }
            }

            CustomerProfileTemplate::where('user_template_id', $UserTemplate->id)->delete();
            TemplateAssignee::where('user_template_id', $UserTemplate->id)->delete();
            $UserTemplate->delete();

            $data['success'] = TRUE;
            $data['message'] = 'The template is removed successfully.';
            $data['data'] = array('template_id' => (int)$request->template_id, 'member_links_ids' => $member_links_ids);
            return response()->json($data, 201);
        } else {
            $data['success'] = FALSE;
            $data['message'] = 'Invalid template.';
            $data['data'] = array('template_id' => (int)$request->template_id);
            return response()->json($data, 404);
        }
    }

    public function make_template_default(Request $request)
    {
        $token = $request->user();
        $parent_id = parent_id($token);

        $defaultTemplate = UserTemplate::where('user_id', $parent_id)->where('is_default', 1);
        if ($defaultTemplate->count() != 0) {
            $defaultTemplate = $defaultTemplate->first();
            $defaultTemplate->is_default = 0;
            $defaultTemplate->save();
        }

        $Template = UserTemplate::where('user_id', $parent_id)->where('id', $request->template_id);
        if ($Template->count() == 0) {
            $data['success'] = FALSE;
            $data['message'] = 'The template id not found.';
            $data['data'] = array('id' => $request->template_id);
            return response()->json($data, 404);
        }

        $Template = $Template->first();
        $Template->is_default = 1;
        $Template->save();

        // $assignees_ids = TemplateAssignee::select(\DB::raw('GROUP_CONCAT(user_id) as user_id'))->where('user_template_id', $request->template_id)->where('customer_profile_id', 0)->first();
        // $assignees_ids = $assignees_ids->user_id != null ? explode(',', $assignees_ids->user_id) : [];

        $data['success'] = TRUE;
        $data['message'] = 'Default template changed successfully.';
        // $data['data'] = array('assignees_ids' => arrayValuesToInt($assignees_ids));
        $data['data'] = array('default_template_id' => (int)$request->template_id);
        return response()->json($data, 201);
    }

    public function show_hide_buttons(Request $request)
    {
        $message = '';
        $token = $request->user();
        $parent_id = parent_id($token);
        $template = [];
        $isDataExists = templateExists($request->template_id, $parent_id);
        if ($isDataExists['success'] == false) {
            return response($isDataExists, 400);
        } else {
            $UserTemplate = $isDataExists['template'];
        }

        if ($request->has('save_contact_button')) {
            $is_visible = $request->save_contact_button == 0 ? 'hidden' : 'visible';
            $message = "The Save Contact button will be " . $is_visible . " for this template.";
            $UserTemplate->show_contact = $request->save_contact_button;
            $template = ['id' => (int)$request->template_id, 'save_contact_button' => true_false($request->save_contact_button)];
        }

        if ($request->has('connect_button')) {
            $is_visible = $request->connect_button == 0 ? 'hidden' : 'visible';
            $message = "The Connect button will be " . $is_visible . " for this template.";
            $UserTemplate->show_connect = $request->connect_button;
            $template = ['id' => (int)$request->template_id, 'connect_button' => (int)$request->connect_button];
        }

        if ($request->has('profile_opens_locked')) {
            $message = "The Lead Capture and One Share buttons are " . ($request->profile_opens_locked == 0 ? 'unlocked' : 'locked') . " for this template.";

            $UserTemplate->profile_opens_locked = $request->profile_opens_locked;
            $template = ['id' => (int)$request->template_id, 'profile_opens_locked' => (int)$request->profile_opens_locked];
        }

        if ($request->has('control_buttons_locked')) {
            $message = "The Connect and Save Contact buttons are " . ($request->control_buttons_locked == 0 ? 'unlocked' : 'locked') . " for this template.";
            $UserTemplate->control_buttons_locked = $request->control_buttons_locked;
            $template = ['id' => (int)$request->template_id, 'control_buttons_locked' => (int)$request->control_buttons_locked];
        }

        if ($request->has('colors_custom_locked')) {
            $message = "The color customization is " . ($request->colors_custom_locked == 0 ? 'unlocked' : 'locked') . " for this template.";
            $UserTemplate->colors_custom_locked = $request->colors_custom_locked;
            $template = ['id' => (int)$request->template_id, 'colors_custom_locked' => (int)$request->colors_custom_locked];
        }

        if ($request->has('open_direct') && $request->has('capture_lead')) {
            $message = "Updated successfully.";
            $UserTemplate->open_direct = $request->open_direct;
            $UserTemplate->capture_lead = $request->capture_lead;
            $template = ['id' => (int)$request->template_id, 'open_direct' => true_false($request->open_direct), 'capture_lead' => true_false($request->capture_lead)];
        } else {

            if ($request->has('open_direct')) {
                $message = "Updated successfully.";
                $UserTemplate->open_direct = $request->open_direct;
                $template = ['id' => (int)$request->template_id, 'open_direct' => true_false($request->open_direct), 'capture_lead' => true_false($UserTemplate->capture_lead)];
            }

            if ($request->has('capture_lead')) {
                $message = "Updated successfully.";
                $UserTemplate->capture_lead = $request->capture_lead;
                $template = ['id' => (int)$request->template_id, 'open_direct' => true_false($UserTemplate->open_direct), 'capture_lead' => true_false($request->capture_lead)];
            }
        }
        $UserTemplate->updated_by = $token->id;
        $UserTemplate->save();

        $control_buttons_locked = true_false($UserTemplate->control_buttons_locked);
        $profile_opens_locked = true_false($UserTemplate->profile_opens_locked);

        $TemplateAssignees = TemplateAssignee::where('user_template_id', $request->template_id)->where('customer_profile_id', 0);
        $total = $TemplateAssignees->count();
        $users = [];
        if ($total > 0) {
            $TemplateAssignees = $TemplateAssignees->get();

            if ($request->has('connect_button') && $control_buttons_locked == true) {
                foreach ($TemplateAssignees as $rec) {
                    // createOrUpdateSettings('show_connect', $request->connect_button, $rec->user_id, $token);
                    $users[] = ['id' => $rec->user_id, 'connect_button' => true_false($request->connect_button)];
                }
            }

            if ($request->has('save_contact_button') && $control_buttons_locked == true) {
                foreach ($TemplateAssignees as $rec) {
                    // createOrUpdateSettings('show_contact', $request->save_contact_button, $rec->user_id, $token);
                    $users[] = ['id' => $rec->user_id, 'save_contact_button' => true_false($request->save_contact_button)];
                }
            }

            if ($request->has('control_buttons_locked')) {
                foreach ($TemplateAssignees as $rec) {
                    // createOrUpdateSettings('control_buttons_locked', $request->control_buttons_locked, $rec->user_id, $token);
                    if ($request->control_buttons_locked == 1) {
                        // $users[] = ['id' => $rec->user_id, 'control_buttons_locked' => (int)$request->control_buttons_locked];
                        $__UserTemplate = UserTemplate::where('id', $request->template_id)->first();
                        // createOrUpdateSettings('show_connect', $__UserTemplate->show_connect, $rec->user_id, $token);
                        // createOrUpdateSettings('show_contact', $__UserTemplate->show_contact, $rec->user_id, $token);
                        $users[] = ['id' => $rec->user_id, 'save_contact_button' => true_false($__UserTemplate->show_contact), 'connect_button' => true_false($__UserTemplate->show_connect)];
                    }
                }
            }

            if ($request->has('profile_opens_locked')) {
                foreach ($TemplateAssignees as $rec) {
                    // createOrUpdateSettings('profile_opens_locked', $request->profile_opens_locked, $rec->user_id, $token);
                    if ($request->profile_opens_locked == 1) {
                        // $users[] = ['id' => $rec->user_id, 'profile_opens_locked' => (int)$request->profile_opens_locked];
                        $__UserTemplate = UserTemplate::where('id', $request->template_id)->first();
                        // createOrUpdateSettings('capture_lead', $__UserTemplate->capture_lead, $rec->user_id, $token);
                        // createOrUpdateSettings('open_direct', $__UserTemplate->open_direct, $rec->user_id, $token);
                        $users[] = ['id' => $rec->user_id, 'capture_lead' => true_false($__UserTemplate->capture_lead), 'open_direct' => true_false($__UserTemplate->open_direct)];
                    }
                }
            }

            if ($request->has('colors_custom_locked')) {
                foreach ($TemplateAssignees as $rec) {
                    // createOrUpdateSettings('colors_custom_locked', $request->colors_custom_locked, $rec->user_id, $token);
                    $settings = [];
                    if ($request->colors_custom_locked == 1) {

                        $__UserTemplate = UserTemplate::where('id', $request->template_id)->first();
                        // updateUserSettings($rec->user_id, $token, $__UserTemplate);
                        $colors = [];

                        $user_settings = UserSettings::where('user_id', $rec->user_id);
                        if ($user_settings->count() > 0) {
                            $user_settings = $user_settings->first();

                            $colors['id'] = $user_settings->id;
                            $colors['section_color'] = notEmpty($__UserTemplate->section_color) ? $__UserTemplate->section_color : $user_settings->section_color;

                            $colors['bg_color'] = isset($__UserTemplate->profile_color) ? $__UserTemplate->profile_color : $__UserTemplate->background_color;

                            $colors['bg_color'] = notEmpty($colors['bg_color']) ?  $colors['bg_color'] : $user_settings->bg_color;

                            $colors['btn_color'] = notEmpty($__UserTemplate->button_color) ? $__UserTemplate->button_color : $user_settings->btn_color;

                            $colors['photo_border_color'] = isset($__UserTemplate->border_color) ? $__UserTemplate->border_color : $__UserTemplate->photo_border_color;

                            $colors['photo_border_color'] = notEmpty($colors['photo_border_color']) ? $colors['photo_border_color'] : $user_settings->photo_border_color;

                            $colors['text_color'] = notEmpty($__UserTemplate->text_color) ? $__UserTemplate->text_color : $user_settings->text_color;

                            $colors['color_link_icons'] = notEmpty($__UserTemplate->color_link_icons) ? true_false($__UserTemplate->color_link_icons) : true_false($user_settings->color_link_icons);

                            $settings['colors'] = $colors;
                        } else {

                            $colors['id'] = $rec->user_id;
                            $colors['section_color'] = $__UserTemplate->section_color;
                            $colors['bg_color'] = isset($__UserTemplate->profile_color) ? $__UserTemplate->profile_color : $__UserTemplate->background_color;
                            $colors['btn_color'] = $__UserTemplate->button_color;
                            $colors['photo_border_color'] = isset($__UserTemplate->border_color) ? $__UserTemplate->border_color : $__UserTemplate->photo_border_color;
                            $colors['text_color'] = $__UserTemplate->text_color;
                            $colors['color_link_icons'] = true_false($__UserTemplate->color_link_icons);

                            $settings['colors'] = $colors;
                        }
                    }
                    $users[] = ['id' => $rec->user_id, 'colors_custom_locked' => (int)$request->colors_custom_locked, 'settings' => $settings];
                }
            }

            if ($request->has('open_direct') && $request->has('capture_lead') && $profile_opens_locked == true) {
                foreach ($TemplateAssignees as $rec) {
                    // createOrUpdateSettings('capture_lead', $request->capture_lead, $rec->user_id, $token);
                    // createOrUpdateSettings('open_direct', $request->open_direct, $rec->user_id, $token);
                    $users[] = ['id' => $rec->user_id, 'capture_lead' => true_false($request->capture_lead), 'open_direct' => true_false($request->open_direct)];
                }
            } else {
                if ($request->has('open_direct') && $profile_opens_locked == true) {
                    foreach ($TemplateAssignees as $rec) {
                        // createOrUpdateSettings('open_direct', $request->open_direct, $rec->user_id, $token);
                        $users[] = ['id' => $rec->user_id, 'capture_lead' => true_false($UserTemplate->capture_lead), 'open_direct' => true_false($request->open_direct)];
                    }
                }

                if ($request->has('capture_lead') && $profile_opens_locked == true) {
                    foreach ($TemplateAssignees as $rec) {
                        // createOrUpdateSettings('capture_lead', $request->capture_lead, $rec->user_id, $token);
                        $users[] = ['id' => $rec->user_id, 'capture_lead' => true_false($request->capture_lead), 'open_direct' => true_false($UserTemplate->open_direct)];
                    }
                }
            }
        }

        $data['success'] = TRUE;
        $data['message'] =  $message;
        $data['data'] = array('template' => $template); //, 'members' => $users
        return response()->json($data, 200);
    }

    // call in other controllers
    public function assign_template_to_member($member_ids, $parent_id, $template_id, $token, $setAllData = true)
    {
        $open_direct = 0;
        $UserSettingsObjUpdate = $UserTemplate = [];
        // pre_print($member_ids);
        if (!empty($member_ids) && count($member_ids) > 0) {
            foreach ($member_ids as $user_id) {
                if ($user_id <= 0) {
                    continue;
                }

                $User = User::where('id', $user_id);
                if ($User->count() == 0) {
                    continue;
                }

                $anyTemplateAssigned = TemplateAssignee::where('user_id', $user_id)->where('customer_profile_id', 0)->count();
                $alreadyAssigned = TemplateAssignee::where('user_id', $user_id)->where('user_template_id', $template_id)->where('customer_profile_id', 0);
                if ($alreadyAssigned->count() == 1) {
                    $TemplateAssignee = $alreadyAssigned->first();
                    if ($TemplateAssignee->is_assigned != 1) {
                        $TemplateAssignee->is_assigned = 1;
                        $TemplateAssignee->updated_by = $token->id;
                        $TemplateAssignee->save();
                    }
                } else {
                    $TemplateAssignee = new TemplateAssignee();
                    $TemplateAssignee->user_id = $user_id;
                    $TemplateAssignee->is_assigned = 1;
                    $TemplateAssignee->user_template_id = $template_id;
                    $TemplateAssignee->customer_profile_id = 0;
                    $TemplateAssignee->customer_profile_template_id = 0;
                    $TemplateAssignee->created_by = $token->id;
                    $TemplateAssignee->save();
                }

                $UserTemplate = UserTemplate::where('id', $template_id)->where('user_id', $parent_id);
                if ($UserTemplate->count() > 0) {
                    $UserTemplate = $UserTemplate->first();

                    $User = $User->first();
                    // $oldUserData = ['company_name' => $User->company_name, 'company_address' => $User->company_address, 'company_logo' => $User->company_logo, 'logo' => $User->logo, 'banner' => $User->banner, 'bio' => $User->bio, 'open_direct' => $User->open_direct];

                    /*if ($setAllData == true) {
                        $User->company_name = $UserTemplate->company_name;
                        $User->company_address = $UserTemplate->company_address;
                        if ($UserTemplate->company_logo != '' && $UserTemplate->company_logo != null) {
                            $User->company_logo = $UserTemplate->company_logo;
                        }

                        if ($UserTemplate->profile_image != '' && $UserTemplate->profile_image != null) {
                            $User->logo = $UserTemplate->profile_image;
                        }

                        if ($UserTemplate->profile_banner != '' && $UserTemplate->profile_banner != null) {
                            $User->banner = $UserTemplate->profile_banner;
                        }
                        $User->bio =  $UserTemplate->subtitle;
                    }

                    if ($UserTemplate->profile_opens_locked == 1) {
                        $User->open_direct =  $UserTemplate->open_direct;
                    }

                    $User->updated_by = $token->id;
                    $User->save();*/
                    $open_direct = $UserTemplate->open_direct;

                    /*$UserSettingsObj = UserSettings::where('user_id', $user_id);
                    if ($UserSettingsObj->count() > 0) {
                        $UserSettingsObj = $UserSettingsObj->first();
                        $OldUserSettings = [
                            'section_color' => $UserSettingsObj->section_color,
                            'is_editable' => $UserSettingsObj->is_editable,
                            'bg_color' => $UserSettingsObj->bg_color,
                            'btn_color' => $UserSettingsObj->btn_color,
                            'text_color' => $UserSettingsObj->text_color,
                            'photo_border_color' => $UserSettingsObj->photo_border_color,
                            'bg_image' => $UserSettingsObj->bg_image,
                            'capture_lead' => $UserSettingsObj->capture_lead,
                            'color_link_icons' => $UserSettingsObj->color_link_icons,
                            'show_contact' => $UserSettingsObj->show_contact,
                            'show_connect' => $UserSettingsObj->show_connect
                        ];
                    } else {
                        $OldUserSettings = [];
                    }

                    $UserSettingsObjUpdate = updateUserSettings($user_id, $token, $UserTemplate, $setAllData);
                    if ($anyTemplateAssigned == 0) {
                        $UserSettingsObjUpdate->user_old_data = json_encode($oldUserData);
                        $UserSettingsObjUpdate->settings_old_data = json_encode($OldUserSettings);
                        $UserSettingsObjUpdate->save();
                    }*/
                } else {
                    $UserTemplate = $UserTemplate->first();
                }

                $profiles = CustomerProfileTemplate::where('user_template_id', $template_id)->get();
                if (!empty($profiles)) {
                    foreach ($profiles as $profile) {
                        $checkTemplateAssignee = TemplateAssignee::where('customer_profile_template_id', $profile->id)->where('user_id', $user_id);
                        if ($checkTemplateAssignee->count() == 0) {

                            $ProfileObj = new CustomerProfile();
                            $ProfileObj->profile_link = $profile->profile_link;
                            $ProfileObj->profile_code = $profile->profile_code;
                            $ProfileObj->icon = $profile->icon;
                            // new code
                            $ProfileObj->icon_svg_default = $profile->icon_svg ?? '';
                            // end
                            $ProfileObj->title = $profile->title;
                            $ProfileObj->user_id = $user_id;
                            $ProfileObj->is_business = $profile->is_business;
                            $ProfileObj->status = $profile->status;
                            $ProfileObj->sequence = maxSequence($user_id);
                            $ProfileObj->is_focused = $profile->is_focused == null ? 0 : $profile->is_focused;
                            $ProfileObj->is_default = 0;
                            $ProfileObj->created_by = $token->id;
                            $ProfileObj->created_at = Carbon::now();
                            $ProfileObj->save();

                            $Obj = new TemplateAssignee();
                            $Obj->user_id = $user_id;
                            $Obj->is_assigned = 1;
                            $Obj->user_template_id = $template_id;
                            $Obj->customer_profile_id = $ProfileObj->id;
                            $Obj->customer_profile_template_id = $profile->id;
                            $Obj->created_by = $token->id;
                            $Obj->save();
                        }
                    }
                }

                // delete any other template if assigned
                $forDeletion = TemplateAssignee::where('user_id', $user_id)->where('user_template_id', '!=', $template_id);
                if ($forDeletion->count() > 0) {
                    $forDeletion = $forDeletion->get();
                    foreach ($forDeletion as $row) {
                        if ($row->customer_profile_id > 0) {
                            CustomerProfile::where('id', $row->customer_profile_id)->delete();
                        }
                        $row->delete();
                        // $this->overwrite_old_data($user_id);
                    }
                }
            }

            $UserSettingsObjUpdate = userSettingsFromTemplate($user_id, $token, $UserTemplate, $setAllData);
            if (!empty($UserSettingsObjUpdate)) {
                $UserSettings = [];
                $UserSettings['id'] = $UserSettingsObjUpdate->id;
                $UserSettings['bg_color'] = $UserSettingsObjUpdate->bg_color;
                $UserSettings['btn_color'] = $UserSettingsObjUpdate->btn_color;
                $UserSettings['photo_border_color'] = $UserSettingsObjUpdate->photo_border_color;
                $UserSettings['section_color'] = $UserSettingsObjUpdate->section_color;
                $UserSettings['text_color'] = $UserSettingsObjUpdate->text_color;
                $UserSettings['show_contact'] = (int) $UserSettingsObjUpdate->show_contact;
                $UserSettings['show_connect'] = (int) $UserSettingsObjUpdate->show_connect;
                $UserSettings['color_link_icons'] = (int) $UserSettingsObjUpdate->color_link_icons;
                $UserSettings['connect_button'] = (int) $UserSettingsObjUpdate->show_connect;
                $UserSettings['save_contact_button'] = (int) $UserSettingsObjUpdate->show_contact;
                $UserSettings['capture_lead'] = (int) $UserSettingsObjUpdate->capture_lead;
                $UserSettings['open_direct'] = (int) $open_direct;
                $UserSettings['user_id'] = $UserSettingsObjUpdate->user_id;

                return (object) $UserSettings;
            }
        }

        return [];
    }

    private function unassign_template($template_id, $user_id, $parent_id = 0)
    {
        $profiles = TemplateAssignee::where('user_id', $user_id)->where('user_template_id', $template_id);
        if ($profiles->count() > 0) {
            $profiles = $profiles->get();
            foreach ($profiles as $profile) {
                if ($profile->id > 0) {
                    CustomerProfile::where('id', $profile->customer_profile_id)->delete();
                }

                $profile->delete();
                // $this->overwrite_old_data($user_id);
            }
        }

        resetProfilesSequence($user_id);
    }

    private function overwrite_old_data($user_id)
    {
        $user_settings = UserSettings::where('user_id', $user_id);
        $User = User::where('id', $user_id);
        if ($User->count() > 0 && $user_settings->count() > 0) {
            $user_settings = $user_settings->first();

            if ($user_settings->user_old_data != null) {
                $user_old_data = json_decode($user_settings->user_old_data);

                if (!empty($user_old_data)) {
                    $User = $User->first();
                    $User->company_name = $user_old_data->company_name;
                    $User->company_address = $user_old_data->company_address;
                    $User->company_logo = $user_old_data->company_logo;
                    $User->logo = $user_old_data->logo;
                    $User->banner = $user_old_data->banner;
                    $User->bio =  $user_old_data->bio;
                    $User->open_direct =  isset($user_old_data->open_direct) ? $user_old_data->open_direct : 0;
                    $User->save();
                }
            }

            if ($user_settings->settings_old_data != null) {
                $settings_old_data = json_decode($user_settings->settings_old_data);
                if (!empty($settings_old_data)) {
                    $update_data = [
                        'section_color' => $settings_old_data->section_color,
                        'is_editable' => $settings_old_data->is_editable,
                        'background_color' => $settings_old_data->bg_color,
                        'button_color' => $settings_old_data->btn_color,
                        'text_color' => $settings_old_data->text_color,
                        'photo_border_color' => $settings_old_data->photo_border_color,
                        'background_image' => $settings_old_data->bg_image,
                        'capture_lead' => $settings_old_data->capture_lead,
                        'color_link_icons' => $settings_old_data->color_link_icons,
                        'show_contact' => $settings_old_data->show_contact,
                        'show_connect' => $settings_old_data->show_connect
                    ];

                    $update_data['colors_custom_locked'] = 0;
                    $update_data['control_buttons_locked'] = 0;
                    $update_data['profile_opens_locked'] = 0;
                    $update_data['user_old_data'] = null;
                    $update_data['settings_old_data'] = null;
                    // pre_print($update_data);

                    updateUserSettings($user_id, [], (object) $update_data);
                }
            }
        }
    }

    public function update_template_profile_data($profile, $template_id, $token, $action)
    {
        // pre_print($profile);
        if ($action == 'update') {
            $profiles = TemplateAssignee::where('user_template_id', $template_id)->where('customer_profile_template_id', $profile->id)->get();
            if (!empty($profiles)) {
                foreach ($profiles as $rec) {
                    $ProfileObj = CustomerProfile::where('id', $rec->customer_profile_id)->first();
                    if ($profile->is_unique != 1) {
                        $ProfileObj->profile_link = $profile->profile_link;
                    }
                    // $ProfileObj->profile_code = $profile->profile_code;
                    if (!in_array($rec->profile_code, is_free_profile_btn())) {
                        $ProfileObj->icon = $profile->icon;
                        $ProfileObj->icon_svg_default = $profile->icon_svg;
                    }
                    $ProfileObj->title = $profile->title;
                    $ProfileObj->status = $profile->status;
                    $ProfileObj->is_focused = $profile->is_focused;
                    $ProfileObj->updated_by = $token->id;
                    $ProfileObj->save();
                }
            }
        } else {

            $members = TemplateAssignee::where('user_template_id', $template_id)->where('customer_profile_id', 0)->get();
            if (!empty($members)) {
                foreach ($members as $member) {

                    $ProfileObj = new CustomerProfile();
                    $ProfileObj->profile_link = $profile->vprofile_linklue;
                    $ProfileObj->profile_code = $profile->profile_code;
                    $ProfileObj->title = $profile->title;
                    $ProfileObj->user_id = $member->id;
                    $ProfileObj->sequence = maxSequence($member->id);
                    $ProfileObj->is_default = 0;
                    $ProfileObj->created_by = $token->id;
                    $ProfileObj->save();

                    $Obj = new TemplateAssignee();
                    $Obj->user_id = $member->id;
                    $Obj->is_assigned = 1;
                    $Obj->user_template_id = $template_id;
                    $Obj->customer_profile_id = $ProfileObj->id;
                    $Obj->customer_profile_template_id = $profile->id;
                    $Obj->created_by = $token->id;
                    $Obj->save();
                }
            }
        }

        return true;
    }

    public function update_template_account_data($UserTemplate, $token)
    {
        return true;

        $accounts = TemplateAssignee::where('user_template_id', $UserTemplate->id)->where('customer_profile_id', 0)->get();
        if (!empty($accounts)) {
            foreach ($accounts as $account) {
                $User = User::where('id', $account->user_id);
                // pre_print(json_decode(json_encode($UserTemplate)));
                if ($User->count() > 0) {
                    $User = $User->first();
                    $User->company_name = $UserTemplate->company_name;
                    $User->company_address = $UserTemplate->company_address;
                    $User->company_logo = $UserTemplate->company_logo;
                    $User->logo = $UserTemplate->profile_image;
                    $User->banner = $UserTemplate->profile_banner;
                    $User->bio =  $UserTemplate->subtitle;
                    $User->updated_by = $token->id;
                    $User->save();

                    updateUserSettings($account->id, $token, $UserTemplate);
                }
            }
        }

        return true;
    }

    public function delete_template_profile_data($profile_template_id, $template_id, $token)
    {
        $members = TemplateAssignee::where('user_template_id', $template_id)->where('customer_profile_template_id', '=', $profile_template_id)->where('customer_profile_id', '!=', 0)->get();
        if (!empty($members)) {
            foreach ($members as $member) {

                CustomerProfile::where('id', $member->customer_profile_id)->where('user_id', $member->user_id)->delete();
                CustomerProfileTemplate::where('id', $profile_template_id)->delete();
                $member->delete();
            }
        }

        return true;
    }
}
