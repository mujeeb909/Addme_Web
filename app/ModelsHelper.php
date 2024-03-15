<?php
// models
use App\Models\CustomerProfile;
use App\Models\CustomerProfileTemplate;
use App\Models\Profile;
use App\Models\TemplateAssignee;
use App\Models\User;
use App\Models\UserSettings;
use App\Models\UserTemplate;

function anyTemplateAssigned($UserID)
{
    $anyTemplateAssigned = TemplateAssignee::where('user_id', $UserID)->where('customer_profile_id', 0);
    if ($anyTemplateAssigned->count() > 0) {
        $templateAssigned = $anyTemplateAssigned->first();
        $template = UserTemplate::where('id', $templateAssigned->user_template_id)->first();
        return json_decode(json_encode($template), true);
    }

    return [];
}
function anyTemplateAssignedProfile($UserID)
{
    $anyTemplateAssigned = TemplateAssignee::where('user_id', $UserID)->where('customer_profile_id', 0);
    if ($anyTemplateAssigned->count() > 0) {
        $templateAssigned = $anyTemplateAssigned->first();
        $template = UserTemplate::where('id', $templateAssigned->user_template_id)->where('colors_custom_locked', 1)->first();
        return json_decode(json_encode($template), true);
    }

    return [];
}

// function TemplateAssigned($UserID)
// {
//     $anyTemplateAssigned = TemplateAssignee::where('user_id', $UserID)->where('customer_profile_id', 0);
//     if ($anyTemplateAssigned->count() > 0) {
//         $templateAssigned = $anyTemplateAssigned->first();
//         $template = UserTemplate::where('id', $templateAssigned->user_template_id)->first();
//         return json_decode(json_encode($template), true);
//     }

//     return [];
// }

// function TemplateAssigned($UserID, $template_id, $parent_id)
// {
//     $findtemplate = UserTemplate::where('id', $template_id)->where('user_id', $parent_id)->first();

//     if (!empty($findtemplate)) {

//         $anyTemplateAssigned = TemplateAssignee::where('user_id', $UserID);

//         if ($anyTemplateAssigned->count() > 0) {
//             $templateAssigned = $anyTemplateAssigned->get();

//             foreach ($templateAssigned as $templateAssigne) {
//                 $templateUpdate = TemplateAssignee::where('user_id', $UserID)->where('id',$templateAssigne->id)->first();

//                 if ($templateUpdate) {
//                     $templateUpdate->user_template_id = $findtemplate->id;
//                     $templateUpdate->save();
//                 }
//             }
//         }

//         return json_decode(json_encode($findtemplate), true);
//     }

//     return [];
// }


function notEmpty($value)
{
    if ($value != '' & $value != NULL) {
        return true;
    }
    return false;
}

function UserObjTemplateBp($User, $UserID = 0, $template = [])
{

    // $User->banner = $User->banner;
    // $User->logo = $User->logo;
    $User->profile_image = isset($User->logo) ? trim($User->logo) : null;
    $User->profile_banner = isset($User->banner) ? trim($User->banner) : null;
    $User->company_logo = isset($User->company_logo) ? trim($User->company_logo) : null;
    $User->company_address = isset($User->company_address) ? trim($User->company_address) : null;
    if ($User->profile_image != null && filter_var($User->profile_image, FILTER_VALIDATE_URL) === false) {
        $User->profile_image = image_url($User->profile_image);
    }

    if ($User->logo != null && filter_var($User->logo, FILTER_VALIDATE_URL) === false) {
        $User->logo = image_url($User->logo);
    }

    if ($User->banner != null && filter_var($User->banner, FILTER_VALIDATE_URL) === false) {
        $User->banner = image_url($User->banner);
    }

    if ($User->profile_banner != null && filter_var($User->profile_banner, FILTER_VALIDATE_URL) === false) {
        $User->profile_banner = image_url($User->profile_banner);
    }

    if ($User->company_logo != null && filter_var($User->company_logo, FILTER_VALIDATE_URL) === false) {
        $User->company_logo = image_url($User->company_logo);
    }

    unset(
        $User->vcode,
        $User->banner,
        $User->logo,
        $User->vcode_expiry,
        $User->access_token,
        $User->custom_gender,
        $User->profile_image,
        $User->profile_banner,
        $User->company_logo,
        $User->connect_button,
        $User->save_contact_button,
        $User->capture_lead,
        $User->open_direct,
    );
    return $User;
}

function UserObjTemplate($User, $UserID = 0, $template = [])
{
    if (!empty($template)) {
        $template = (object)$template;
        $open_direct = isset($User->open_direct) ? $User->open_direct : 0;
        $User->company_name = notEmpty($template->company_name) ? $template->company_name : $User->company_name;
        $User->company_address = notEmpty($template->company_address) ? $template->company_address : $User->company_address;
        $User->company_logo = notEmpty($template->company_logo) ? $template->company_logo : $User->company_logo;
        $User->logo = notEmpty($template->profile_image) ? $template->profile_image : $User->logo;
        $User->banner = notEmpty($template->profile_banner) ? $template->profile_banner : $User->banner;
        $User->bio = notEmpty($template->subtitle) ? $template->subtitle : $User->bio;
        $User->open_direct = $template->open_direct != '' && true_false($template->profile_opens_locked) == true ? $template->open_direct : $open_direct;
    }

    // $User->banner = $User->banner;
    // $User->logo = $User->logo;
    $User->profile_image = isset($User->logo) ? trim($User->logo) : null;
    $User->profile_banner = isset($User->banner) ? trim($User->banner) : null;
    $User->company_logo = isset($User->company_logo) ? trim($User->company_logo) : null;
    $User->company_address = isset($User->company_address) ? trim($User->company_address) : null;
    if ($User->profile_image != null && filter_var($User->profile_image, FILTER_VALIDATE_URL) === false) {
        $User->profile_image = image_url($User->profile_image);
    }

    if ($User->logo != null && filter_var($User->logo, FILTER_VALIDATE_URL) === false) {
        $User->logo = image_url($User->logo);
    }

    if ($User->banner != null && filter_var($User->banner, FILTER_VALIDATE_URL) === false) {
        $User->banner = image_url($User->banner);
    }

    if ($User->profile_banner != null && filter_var($User->profile_banner, FILTER_VALIDATE_URL) === false) {
        $User->profile_banner = image_url($User->profile_banner);
    }

    if ($User->company_logo != null && filter_var($User->company_logo, FILTER_VALIDATE_URL) === false) {
        $User->company_logo = image_url($User->company_logo);
    }

    unset(
        $User->vcode,
        $User->banner,
        $User->logo,
        $User->vcode_expiry,
        $User->access_token,
        $User->custom_gender,
        // $User->profile_image,
        // $User->profile_banner,
        // $User->company_logo,
        $User->connect_button,
        $User->save_contact_button,
        $User->capture_lead,
        $User->open_direct
    );
    return $User;
}


function UserObj($User, $UserID = 0, $template = [])
{
    if (!empty($template)) {
        $template = (object)$template;
        $open_direct = isset($User->open_direct) ? $User->open_direct : 0;
        $User->company_name = notEmpty($template->company_name) ? $template->company_name : $User->company_name;
        $User->company_address = notEmpty($template->company_address) ? $template->company_address : $User->company_address;
        $User->company_logo = notEmpty($template->company_logo) ? $template->company_logo : $User->company_logo;
        $User->logo = notEmpty($template->profile_image) ? $template->profile_image : $User->logo;
        $User->banner = notEmpty($template->profile_banner) ? $template->profile_banner : $User->banner;
        $User->bio = notEmpty($template->subtitle) ? $template->subtitle : $User->bio;
        $User->open_direct = $template->open_direct != '' && true_false($template->profile_opens_locked) == true ? $template->open_direct : $open_direct;
    }

    // $User->banner = $User->banner;
    // $User->logo = $User->logo;
    $User->profile_image = isset($User->logo) ? trim($User->logo) : null;
    $User->profile_banner = isset($User->banner) ? trim($User->banner) : null;
    $User->company_logo = isset($User->company_logo) ? trim($User->company_logo) : null;
    if ($User->profile_image != null && filter_var($User->profile_image, FILTER_VALIDATE_URL) === false) {
        $User->profile_image = image_url($User->profile_image);
    }

    if ($User->logo != null && filter_var($User->logo, FILTER_VALIDATE_URL) === false) {
        $User->logo = image_url($User->logo);
    }

    if ($User->banner != null && filter_var($User->banner, FILTER_VALIDATE_URL) === false) {
        $User->banner = image_url($User->banner);
    }

    if ($User->profile_banner != null && filter_var($User->profile_banner, FILTER_VALIDATE_URL) === false) {
        $User->profile_banner = image_url($User->profile_banner);
    }

    if ($User->company_logo != null && filter_var($User->company_logo, FILTER_VALIDATE_URL) === false) {
        $User->company_logo = image_url($User->company_logo);
    }

    unset($User->vcode, $User->vcode_expiry, $User->access_token, $User->custom_gender);
    return $User;
}

function userChildsObj($users, $template = [])
{
    foreach ($users as $user) {
        $user = UserObj($user, 0, $template);
    }

    return $users;
}

function UpdateMemberObj($updated_member, $UserID = 0, $updated_membertemplate = [])
{
    if (!empty($updated_membertemplate)) {
        $updated_membertemplate = (object)$updated_membertemplate;
        $open_direct = isset($updated_member->open_direct) ? $updated_member->open_direct : 0;
        $updated_member->company_name = notEmpty($updated_membertemplate->company_name) ? $updated_membertemplate->company_name : $updated_member->company_name;
        $updated_member->company_address = notEmpty($updated_membertemplate->company_address) ? $updated_membertemplate->company_address : $updated_member->company_address;
        $updated_member->company_logo = notEmpty($updated_membertemplate->company_logo) ? $updated_membertemplate->company_logo : $updated_member->company_logo;
        $updated_member->logo = notEmpty($updated_membertemplate->profile_image) ? $updated_membertemplate->profile_image : $updated_member->logo;
        $updated_member->banner = notEmpty($updated_membertemplate->profile_banner) ? $updated_membertemplate->profile_banner : $updated_member->banner;
        $updated_member->bio = notEmpty($updated_membertemplate->subtitle) ? $updated_membertemplate->subtitle : $updated_member->bio;
        $updated_member->open_direct = $updated_membertemplate->open_direct != '' && true_false($updated_membertemplate->profile_opens_locked) == true ? $updated_membertemplate->open_direct : $open_direct;
    }

    // $updated_member->banner = $updated_member->banner;
    // $updated_member->logo = $updated_member->logo;
    $updated_member->profile_image = isset($updated_member->logo) ? trim($updated_member->logo) : null;
    $updated_member->profile_banner = isset($updated_member->banner) ? trim($updated_member->banner) : null;
    $updated_member->company_logo = isset($updated_member->company_logo) ? trim($updated_member->company_logo) : null;
    if ($updated_member->profile_image != null && filter_var($updated_member->profile_image, FILTER_VALIDATE_URL) === false) {
        $updated_member->profile_image = image_url($updated_member->profile_image);
    }

    if ($updated_member->logo != null && filter_var($updated_member->logo, FILTER_VALIDATE_URL) === false) {
        $updated_member->logo = image_url($updated_member->logo);
    }

    if ($updated_member->banner != null && filter_var($updated_member->banner, FILTER_VALIDATE_URL) === false) {
        $updated_member->banner = image_url($updated_member->banner);
    }

    if ($updated_member->profile_banner != null && filter_var($updated_member->profile_banner, FILTER_VALIDATE_URL) === false) {
        $updated_member->profile_banner = image_url($updated_member->profile_banner);
    }

    if ($updated_member->company_logo != null && filter_var($updated_member->company_logo, FILTER_VALIDATE_URL) === false) {
        $updated_member->company_logo = image_url($updated_member->company_logo);
    }

    unset($updated_member->vcode, $updated_member->vcode_expiry, $updated_member->access_token, $updated_member->custom_gender);
    return $updated_member;
}

function UserDetailsObj($User, $UserID = 0)
{
    $User->banner = image_url($User->banner);
    $User->logo = image_url($User->logo);
    $User->profile_image = ($User->logo);

    unset($User->vcode, $User->vcode_expiry, $User->access_token, $User->custom_gender);
    return $User;
}

function userSettingsObj_Old($UserID = 0, $template = [])
{
    $settings = UserSettings::where('user_id', $UserID);
    $settingsExists = $settings->count();
    if ($settingsExists > 0) {
        $settings = $settings->first();

        if (!empty($template)) {
            $template = (object)$template;
            $settings->is_editable = $settings->is_editable; //notEmpty($template->is_editable) ? $template->is_editable : $settings->is_editable;
            $settings->bg_image = notEmpty($template->background_image) ? $template->background_image : $settings->bg_image;
            // colors
            $settings->bg_color = notEmpty($template->background_color) && true_false($template->colors_custom_locked) ? $template->background_color : $settings->bg_color;
            $settings->btn_color = notEmpty($template->button_color) && true_false($template->colors_custom_locked) ? $template->button_color : $settings->btn_color;
            $settings->text_color = notEmpty($template->text_color) && true_false($template->colors_custom_locked) ? $template->text_color : $settings->text_color;
            $settings->photo_border_color = notEmpty($template->border_color) && true_false($template->colors_custom_locked) ? $template->border_color : $settings->photo_border_color;
            $settings->section_color = notEmpty($template->section_color) && true_false($template->colors_custom_locked) ? $template->section_color : $settings->section_color;
            $settings->color_link_icons = notEmpty($template->color_link_icons) && true_false($template->colors_custom_locked) ? $template->color_link_icons : $settings->color_link_icons;
            // btns
            $settings->capture_lead = $template->capture_lead != '' && true_false($template->profile_opens_locked) ? $template->capture_lead : $settings->capture_lead;
            $settings->show_contact = $template->show_contact != '' && $template->control_buttons_locked == 1 ? $template->show_contact : $settings->show_contact;
            $settings->show_connect = $template->show_connect != '' && $template->control_buttons_locked == 1 ? $template->show_connect : $settings->show_connect;
            // locks
            $settings->control_buttons_locked = notEmpty($template->control_buttons_locked) ? $template->control_buttons_locked : $settings->control_buttons_locked;
            $settings->profile_opens_locked = notEmpty($template->profile_opens_locked) ? $template->profile_opens_locked : $settings->profile_opens_locked;
            $settings->colors_custom_locked = notEmpty($template->colors_custom_locked) ? $template->colors_custom_locked : $settings->colors_custom_locked;
        }

        $settings = json_decode(json_encode($settings), true);
        return $settings;
    } else {
        if (!empty($template)) {
            $template = (object)$template;
            $settings['is_editable'] = 1; //notEmpty($template->is_editable) ? $template->is_editable : 1;
            $settings['bg_color'] = notEmpty($template->background_color) ? $template->background_color : 'rgba(255, 255, 255, 1)';
            $settings['btn_color'] = notEmpty($template->button_color) ? $template->button_color : 'rgba(0, 0, 0, 1)';
            $settings['text_color'] = notEmpty($template->text_color) ? $template->text_color : 'rgba(17, 24, 3, 1)';
            $settings['photo_border_color'] = notEmpty($template->border_color) ? $template->border_color : 'rgba(255, 255, 255, 1)';
            $settings['section_color'] = notEmpty($template->section_color) ? $template->section_color : 'rgba(255, 255, 255, 0)';
            $settings['color_link_icons'] = notEmpty($template->color_link_icons) ? $template->color_link_icons : 0;
            $settings['bg_image'] = notEmpty($template->background_image) ? $template->background_image : NULL;
            $settings['capture_lead'] = notEmpty($template->capture_lead) ? $template->capture_lead : 0;
            $settings['show_contact'] = notEmpty($template->show_contact) ? $template->show_contact : 1;
            $settings['show_connect'] = notEmpty($template->show_connect) ? $template->show_connect : 1;
            $settings['control_buttons_locked'] = notEmpty($template->control_buttons_locked) ? $template->control_buttons_locked : 0;
            $settings['profile_opens_locked'] = notEmpty($template->profile_opens_locked) ? $template->profile_opens_locked : 0;
            $settings['colors_custom_locked'] = notEmpty($template->colors_custom_locked) ? $template->colors_custom_locked : 0;

            $settings = json_decode(json_encode($settings), true);
            return $settings;
        }
    }

    return [];
}

function icon_svg_default($request, $profile_code = '')
{
    $code = '';
    if ($request->has('code')) {
        $code = $request->code;
    } else if ($request->has('profile_code')) {
        $code = $request->profile_code;
    } else {
        $code = $profile_code;
    }

    if ($code != '') {
        $Profile = Profile::where('profile_code', $code);
        if ($Profile->count() > 0) {
            $Profile = $Profile->first();
            return $Profile->icon_svg_default;
        } else {
            return '';
        }
    } else {
        return '';
    }

    return '';
}

function userSettingsObj($UserID = 0, $template = [])
{
    $settings = UserSettings::where('user_id', $UserID);
    $settingsExists = $settings->count();
    if ($settingsExists > 0) {
        $settings = $settings->first();

        $settings = json_decode(json_encode($settings), true);
        return $settings;
    }
    return [];
}

function ProfileButton($CustomerProfileId, $CustomerProfile = null, $ProfileType = null, $platform = 'bp')
{
    if ($platform == 'bp') {
        if ($CustomerProfileId != 0) {
            $CustomerProfile = CustomerProfile::where('id', $CustomerProfileId);
            if ($CustomerProfile->count()) {
                $Obj = $CustomerProfile->first();
            } else {
                return [];
            }
        } else if ($CustomerProfile != null) {
            $Obj = $CustomerProfile;
        }

        $Profile = $ProfileType->first();

        $Obj->value = $Obj->profile_link;
        $Obj->href = $Obj->profile_link != '' ? (($Obj->profile_code != 'file') ? $Profile->base_url . $Obj->profile_link : $Obj->file_image) : '';
        $Obj->icon_url =  $Obj->icon != '' ? icon_url() . $Obj->icon : '';
        $Obj->icon_svg =  $Obj->icon_svg != '' ? $Obj->icon_svg : $Profile->icon_svg_default;
        $Obj->template_id = (int) $Obj->user_template_id;
        $Obj->visible = true_false($Obj->status);
        $Obj->is_highlighted = true_false($Obj->is_focused);
        $Obj->is_unique = (int) $Obj->is_unique;
        $Obj->type = $Profile->type;

        unset($Obj->icon, $Obj->status, $Obj->is_focused, $Obj->file_image, $Obj->user_id, $Obj->is_business, $Obj->is_direct,  $Obj->is_default, $Obj->global_id, $Obj->created_by, $Obj->created_at, $Obj->updated_by, $Obj->updated_at, $Obj->profile_link, $Obj->user_template_id);

        return $Obj;
    } else {

        if ($CustomerProfileId != 0) {
            $CustomerProfile = CustomerProfile::where('id', $CustomerProfileId);
            if ($CustomerProfile->count()) {
                $Obj = $CustomerProfile->first();
            } else {
                return [];
            }
        } else if ($CustomerProfile != null) {
            $Obj = $CustomerProfile;
        }

        if ($Obj->profile_code == 'contact-card') {
            $Obj->profile_link = main_url() . '/contact-card/' . encrypt($Obj->user_id);
        } else if ($Obj->profile_code == 'file') {
            $Obj->profile_link = file_url() . $Obj->file_image;
        }

        $Obj->icon = ($Obj->icon != '' && $Obj->icon != NULL) ? icon_url() . $Obj->icon : $Obj->icon;
        $Obj->file_image = ($Obj->file_image != '' && $Obj->file_image != NULL) ? file_url() . $Obj->file_image : $Obj->file_image;

        $query = \DB::table('template_assignees AS ta')->select('cpt.user_template_id', 'cpt.is_unique');
        $query->leftJoin('customer_profile_templates AS cpt', 'cpt.id', '=', 'ta.customer_profile_template_id');
        $query->where('ta.customer_profile_id', $Obj->id);
        if ($query->count() > 0) {
            $rec = $query->first();
            $Obj->template_id = (int) $rec->user_template_id;
            $Obj->is_unique = (int) $rec->is_unique;
        } else {
            $Obj->template_id = 0;
            $Obj->is_unique = 0;
        }

        return $Obj;
    }
}

function TemplateProfileButton($TemplateProfileId, $TemplateProfile = null)
{
    if ($TemplateProfileId != 0) {
        $TemplateProfile = CustomerProfileTemplate::where('id', $TemplateProfileId)->first();
    }

    $Profile = Profile::where('profile_code', $TemplateProfile->profile_code)->first();

    $TemplateProfile->value = $TemplateProfile->profile_link == NULL ? '' : $TemplateProfile->profile_link;
    if ($TemplateProfile->profile_link == NULL || $TemplateProfile->profile_link == '') {
        $TemplateProfile->href = '';
    } else {
        $TemplateProfile->href = ($TemplateProfile->profile_code != 'file') ? $Profile->base_url . $TemplateProfile->profile_link : $TemplateProfile->file_image;
    }

    $TemplateProfile->icon_url = $TemplateProfile->icon != '' ? icon_url() . $TemplateProfile->icon : '';

    if ($TemplateProfile->icon_svg == '') {
        $TemplateProfile->icon_svg = $Profile->icon_svg_default;
    }

    $TemplateProfile->visible = true_false($TemplateProfile->status);
    $TemplateProfile->is_highlighted = true_false($TemplateProfile->is_focused);

    unset($TemplateProfile->icon, $TemplateProfile->status, $TemplateProfile->is_focused, $TemplateProfile->file_image, $TemplateProfile->user_id, $TemplateProfile->is_business, $TemplateProfile->is_direct, $TemplateProfile->sequence, $TemplateProfile->is_default, $TemplateProfile->global_id, $TemplateProfile->created_by, $TemplateProfile->created_at, $TemplateProfile->updated_by, $TemplateProfile->updated_at, $TemplateProfile->profile_link);
    return $TemplateProfile;
}

// returns formatted single template object
function templateObj($templateObj)
{
    $templateObj->company_logo = image_url($templateObj->company_logo);
    $templateObj->profile_image = image_url($templateObj->profile_image);
    $templateObj->profile_banner = image_url($templateObj->profile_banner);
    $templateObj->save_contact_button = true_false($templateObj->show_contact);
    $templateObj->connect_button = true_false($templateObj->show_connect);
    unset($templateObj->show_connect, $templateObj->show_contact);

    $profiles = CustomerProfileTemplate::where('user_template_id', $templateObj->id)->get();
    if (!empty($profiles)) {
        foreach ($profiles as $i => $Obj) {
            $ProfileTemplateObj = CustomerProfileTemplateObj($Obj);
            unset($profiles[$i]);
            $profiles[$i] = $ProfileTemplateObj;
        }
    }

    $templateObj->links = $profiles;
    $templateObj->assignees_ids = getAssigneeIDs($templateObj->id);
    $templateObj->settings = templateSettingsObject($templateObj);

    unset($templateObj->section_color, $templateObj->profile_color, $templateObj->border_color, $templateObj->background_color, $templateObj->button_color, $templateObj->text_color, $templateObj->photo_border_color, $templateObj->color_link_icons, $templateObj->background_image, $templateObj->created_by, $templateObj->created_at, $templateObj->updated_by, $templateObj->updated_at);

    return $templateObj;
}

// returns formatted colors list
function templateSettingsObject($template)
{
    $settings = [];
    $settings['section_color'] = $template->section_color;
    $settings['profile_color'] = $template->profile_color;
    $settings['border_color'] = $template->border_color;
    $settings['background_color'] = $template->background_color;
    $settings['button_color'] = $template->button_color;
    $settings['text_color'] = $template->text_color;
    $settings['photo_border_color'] = $template->photo_border_color;
    // $settings['background_image'] = image_url($template->background_image);
    $settings['color_link_icons'] = true_false($template->color_link_icons);
    $_settings['colors'] = $settings;

    return $_settings;
}

// returns formatted single template profile object
function CustomerProfileTemplateObj($ProfileTemplateObj)
{
    $Profile = Profile::where('profile_code', $ProfileTemplateObj->profile_code)->first();
    if ($Profile) {
        $ProfileTemplateObj->icon_url = $ProfileTemplateObj->icon != '' ? icon_url() . $ProfileTemplateObj->icon : '';
        $ProfileTemplateObj->value = $ProfileTemplateObj->profile_link;
        $ProfileTemplateObj->profile_link = ($ProfileTemplateObj->profile_code != 'file') ? $Profile->base_url . $ProfileTemplateObj->profile_link : $ProfileTemplateObj->file_image;
        $ProfileTemplateObj->href = $ProfileTemplateObj->profile_link;
        $ProfileTemplateObj->is_highlighted = true_false($ProfileTemplateObj->is_focused == null ? 0 : $ProfileTemplateObj->is_focused);
        $ProfileTemplateObj->visible = true_false($ProfileTemplateObj->status);
        $ProfileTemplateObj->type = $Profile->type;
        $ProfileTemplateObj->template_id = $ProfileTemplateObj->user_template_id;
        $ProfileTemplateObj->icon_svg = $ProfileTemplateObj->icon_svg != '' ? $ProfileTemplateObj->icon_svg : $Profile->icon_svg_default;

        // if ($ProfileTemplateObj->icon_svg != '') {
        //     $ProfileTemplateObj->icon_url = '';
        // }
        unset($ProfileTemplateObj->icon, $ProfileTemplateObj->profile_link, $ProfileTemplateObj->user_template_id, $ProfileTemplateObj->file_image, $ProfileTemplateObj->is_business, $ProfileTemplateObj->is_focused, $ProfileTemplateObj->global_id, $ProfileTemplateObj->created_by, $ProfileTemplateObj->created_at, $ProfileTemplateObj->updated_by, $ProfileTemplateObj->updated_at, $ProfileTemplateObj->is_default, $ProfileTemplateObj->is_direct, $ProfileTemplateObj->user_id, $ProfileTemplateObj->status);
    }

    return $ProfileTemplateObj;
}

// model TemplateProfile exists
function templateExists($TemplateId, $ParentId)
{
    $UserTemplate = UserTemplate::where('id', $TemplateId)->where('user_id', $ParentId);
    if ($UserTemplate->count() == 0) {
        $data['success'] = FALSE;
        $data['message'] = 'Invalid Template.';
        $data['data'] = (object)[];
    } else {
        $data['success'] = TRUE;
        $data['template'] =  $UserTemplate->first();
    }
    return $data;
}

//create or update values
function createOrUpdateTemplateProfile($record, $UserTemplateID, $token, $action = 'add')
{
    if ($action == 'update') {
        $Obj = CustomerProfileTemplate::where('id', $record->id);
        if ($Obj->count() > 0) {
            $Obj = $Obj->first();
            $Obj->profile_link = (isset($record->profile_link)) ? $record->profile_link : $Obj->profile_link;
            $Obj->title = (isset($record->title)) ? $record->title : $Obj->title;;
            $Obj->updated_by = $token->id;
        } else {
            return [];
        }
    } else {
        $Obj = new CustomerProfileTemplate();
        $Obj->profile_link = (isset($record->profile_link)) ? $record->profile_link : NULL;
        $Obj->profile_code = $record->profile_code;
        $Obj->title = (isset($record->title)) ? $record->title : NULL;
        $Obj->sequence = maxTemplateProfileSequence($UserTemplateID);
        $Obj->created_by = $token->id;
    }

    $Obj->user_id = 0;
    $Obj->is_business = 0;
    $Obj->status = 1;
    $Obj->is_default = 0;
    $Obj->user_template_id = $UserTemplateID;
    $Obj->save();

    return $Obj;
}

// create or update settings value
function createOrUpdateSettings($key, $value, $user_id, $token)
{
    if (in_array($key, ['open_direct'])) {
        $userObj = User::where('id', $user_id);
        if ($userObj->count() > 0) {
            $userObj = $userObj->first();
            $userObj->$key = $value;
            $userObj->updated_by = $token->id;
            $userObj->save();
            return $userObj;
        } else {
            return [];
        }
    } else {
        $userObj = UserSettings::where('user_id', $user_id);
        if ($userObj->count() > 0) {
            $userObj = $userObj->first();
            $userObj->$key = $value;
            $userObj->updated_by = $token->id;
            $userObj->save();
        } else {
            if ($user_id != 0 && $user_id != '' && $user_id != null) {
                $userObj = new UserSettings();
                $userObj->$key = $value;
                $userObj->user_id = $user_id;
                if ($key != 'color_link_icons') {
                    $userObj->color_link_icons = 0;
                }
                $userObj->created_by = $token->id;
                $userObj->save();
            } else {
                return [];
            }
        }
        return $userObj;
    }
}

// returns max value
function maxSequence($UserID)
{
    $maxProfileSequence = CustomerProfile::select(\DB::raw('ifnull(MAX(sequence),-1) as sequence'))->where('user_id', $UserID)->where('profile_code', '!=', 'contact-card')->first();

    return (int) $maxProfileSequence->sequence + 1;
}

// returns max value
function maxTemplateProfileSequence($UserTemplateID)
{
    $maxProfileSequence = CustomerProfileTemplate::select(\DB::raw('ifnull(MAX(sequence),-1) as sequence'))->where('user_template_id', $UserTemplateID)->first();

    return (int) $maxProfileSequence->sequence + 1;
}

// unset values from model objects

function unsetTemplateObjValue($UserTemplate)
{
    unset($UserTemplate->section_color, $UserTemplate->profile_color, $UserTemplate->border_color, $UserTemplate->background_color, $UserTemplate->button_color, $UserTemplate->text_color, $UserTemplate->photo_border_color, $UserTemplate->color_link_icons, $UserTemplate->background_image, $UserTemplate->show_contact, $UserTemplate->show_connect, $UserTemplate->created_by, $UserTemplate->created_at, $UserTemplate->updated_by, $UserTemplate->updated_at, $UserTemplate->control_buttons_locked, $UserTemplate->profile_opens_locked, $UserTemplate->colors_custom_locked, $UserTemplate->is_editable, $UserTemplate->capture_lead, $UserTemplate->is_default, $UserTemplate->open_direct, $UserTemplate->user_id);

    return $UserTemplate;
}
