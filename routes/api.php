<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\HubspotApiController;
use App\Http\Controllers\Api\PlatformIntegrationController;
use App\Http\Controllers\Api\v3\UserController as V3UserController;
//use App\Http\Controllers\Api\v3\HomeController as V3HomeController;
use App\Http\Controllers\Api\v3\MultipleChildAccountController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::get('/test', function () {
    phpinfo();
});



// $file = root_dir() . 'jsons/requests/'.date('YmdHis').'__request.txt';
//file_put_contents($file, json_encode($_REQUEST).'=========='.json_encode($_FILES));

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});





Route::group(['middleware' => ['auth:api']], function () {

    Route::get('profile_types', [HomeController::class, 'profile_types']);
    Route::get('list_profiles', [HomeController::class, 'list_profiles']);
    Route::post('add_custom_profile', [HomeController::class, 'add_custom_profile']);
    Route::post('update_custom_profile', [HomeController::class, 'update_custom_profile']);
    Route::post('delete_custom_profile', [HomeController::class, 'delete_custom_profile']);

    Route::get('list_all_profiles', [HomeController::class, 'list_all_profiles']); //list members social profiles against all profiles
    Route::post('add_profile_svg', [HomeController::class, 'add_profile_svg']); //add member's social profile with icon svg
    Route::post('add_profile', [HomeController::class, 'add_profile']); //add member's social profile icon
    Route::post('update_profile', [HomeController::class, 'update_profile']); //update member's social profile
    Route::get('my_profiles_svg', [HomeController::class, 'my_profiles_svg']); //list member's social profiles with icon svg
    Route::get('my_profiles', [HomeController::class, 'my_profiles']); //list member's social profiles
    Route::post('update_title', [HomeController::class, 'update_title']); //update member's social profile title
    Route::post('update_icon_svg', [HomeController::class, 'update_icon_svg']); //update member's social profile with icon svg
    Route::post('update_icon', [HomeController::class, 'update_icon']); //update member's social profile icon
    Route::post('delete-profile', [HomeController::class, 'delete_profile']);
    Route::post('profile-focused', [HomeController::class, 'profile_focused']);
    Route::post('save_sequence', [HomeController::class, 'save_sequence']);

    Route::post('business_info', [HomeController::class, 'update_business_info']);
    Route::post('contact_card', [HomeController::class, 'contact_card']);
    Route::get('contact_card', [HomeController::class, 'get_contact_card']);
    Route::get('user_notes', [HomeController::class, 'user_notes']);

    Route::post('update-subscription', [HomeController::class, 'update_subscription']);
    Route::get('subscription-status', [HomeController::class, 'get_subscription_status']);

    Route::get('device-list', [HomeController::class, 'device_list']);
    Route::post('update-device-name', [HomeController::class, 'update_device_name']);
    Route::post('unmap-device', [HomeController::class, 'unmap_device']);
    Route::post('feedback', [HomeController::class, 'add_feedback']);

    Route::post('delete-account', [UserController::class, 'delete_account']);
    Route::post('confirm-delete-account', [UserController::class, 'confirm_delete_account']);

    Route::post('scan-business-card', [UserController::class, 'scan_business_card']);

    Route::post('enable_two_fa', [UserController::class, 'enable_two_fa']);
    Route::post('update_username', [UserController::class, 'update_username']);
    Route::get('user_profile', [UserController::class, 'profile']);
    Route::post('profile', [UserController::class, 'update_user_profile']);
    Route::get('logout', [UserController::class, 'logout']);
    Route::post('profile_on_off', [UserController::class, 'profile_on_off']);
    Route::post('open_direct', [UserController::class, 'open_direct']);
    Route::post('update_logo', [UserController::class, 'update_logo']);
    Route::post('update_banner', [UserController::class, 'update_banner']);
    Route::post('profile_view', [UserController::class, 'profile_view']);
    Route::post('set-contact-card-note', [UserController::class, 'update_contact_card_note']);
    Route::post('reset-contact-card-note', [UserController::class, 'reset_contact_card_note']);

    // hashtags
    Route::get('members/hashtags', [CustomerController::class, 'members_hashtags']);
    Route::post('members/hashtags', [CustomerController::class, 'add_members_hashtags']);
    Route::put('members/hashtags/{id}', [CustomerController::class, 'update_members_hashtags']);
    Route::delete('members/hashtags', [CustomerController::class, 'delete_members_hashtags']);
    // end

    // members
    Route::post('create-accounts', [CustomerController::class, 'create_accounts']);
    Route::post('create-member-accounts', [CustomerController::class, 'create_member_accounts']);
    // Add New Members by Emails
    Route::post('members/emails', [CustomerController::class, 'create_member_accounts_with_emails']);
    Route::post('create-account', [CustomerController::class, 'create_account']);
    Route::post('upload-accounts-csv', [CustomerController::class, 'upload_accounts_csv']);
    // Add new Member By CSv
    Route::post('members/csv', [CustomerController::class, 'upload_members_csv']);
    Route::get('members-list', [CustomerController::class, 'members_list']);
    Route::get('members-profiles', [CustomerController::class, 'member_profiles']);
    Route::post('set-language', [CustomerController::class, 'set_language']);
    Route::post('is-globally-editable', [CustomerController::class, 'is_globally_editable']);
    Route::post('set-deactivation-date', [CustomerController::class, 'set_deactivation_date']);
    Route::delete('members/{user_id}', [CustomerController::class, 'confirm_delete_account']);
    Route::delete('members', [CustomerController::class, 'confirm_delete_accounts']);
    Route::post('bulk-update-member', [CustomerController::class, 'bulk_update_members']);

    Route::post('member/{member_id}/profile', [CustomerController::class, 'add_member_profile']);
    Route::post('members/links', [CustomerController::class, 'add_multiple_member_profile']);
    Route::post('member/{member_id}/profile/{member_profile_id}', [CustomerController::class, 'update_member_profile']);
    Route::post('member/show-hide/profile/{member_profile_id}', [CustomerController::class, 'show_hide_member_profile']);
    Route::post('update-colors', [CustomerController::class, 'update_user_settings']);
    Route::post('update-bg-image', [CustomerController::class, 'update_user_settings']);
    Route::post('members/{user_id}', [CustomerController::class, 'update_member_data']);
    Route::put('members/{user_id}/links', [CustomerController::class, 'member_profiles_sequence']);
    Route::post('enable-open-direct', [CustomerController::class, 'open_direct']);
    Route::get('members', [CustomerController::class, 'list_member_profiles']);
    Route::put('members', [CustomerController::class, 'update_members_data']);
    Route::post('members/{user_id}/links', [CustomerController::class, 'add_same_profile_all_members']);
    Route::post('members/{user_id}/links/{customer_profile_id}', [CustomerController::class, 'update_same_profile_all_members']);
    Route::delete('members/{user_id}/links/{customer_profile_id}/{global_id?}', [CustomerController::class, 'delete_same_profile_all_members']);
    Route::get('members/links', [CustomerController::class, 'list_members_profiles_only']);
    Route::get('members/links/types', [HomeController::class, 'member_profile_types']);





    //Hubspot intigration
    Route::get('/integrations/platforms', [PlatformIntegrationController::class, 'getIntegrationsPlatforms']);
    Route::get('/integrations', [PlatformIntegrationController::class, 'getIntegrations']);
    Route::post('/integrations/hubspot', [HubspotApiController::class, 'getAccessTokens']);
    Route::get('getHubspotContacts', [HubspotApiController::class, 'getHubspotContacts']);
    Route::delete('integrations/{id}', [PlatformIntegrationController::class, 'deleteIntegration']);

    // Integration Azure AD
    Route::post('/integrations/azure-ad', [PlatformIntegrationController::class, 'getAzureAdAccessTokens']);
    Route::get('/integrations/{integration_id}/sync', [UserController::class, 'getSyncUsers']);

    // BP template APIs
    Route::get('templates', [TemplateController::class, 'list_template']);
    Route::post('templates', [TemplateController::class, 'create_template']);
    Route::post('templates/{template_id}', [TemplateController::class, 'update_template']);
    Route::put('templates/{template_id}', [TemplateController::class, 'show_hide_buttons']);
    // Route::post('templates/{template_id}/links/reorder', [TemplateController::class, 'reorder_template_profiles']);
    Route::put('templates/{template_id}/links/reorder', [TemplateController::class, 'reorder_template_profiles']);
    Route::post('templates/{template_id}/links', [TemplateController::class, 'create_template_profile']);
    Route::post('templates/{template_id}/links/{template_profile_id}', [TemplateController::class, 'update_template_profile']);
    Route::delete('templates/{template_id}/links/{template_profile_id}', [TemplateController::class, 'delete_template_profile']);
    Route::post('template/{template_id}/assign', [TemplateController::class, 'assign_template']);
    Route::post('templates/{template_id}/set-as-default', [TemplateController::class, 'make_template_default']);
    Route::delete('templates/{template_id}', [TemplateController::class, 'delete_template']);
    Route::put('templates/{template_id}/assign', [TemplateController::class, 'assign_template_json']);
});

Route::get('members/{id}/qrcode', [CustomerController::class, 'nfc_activation_url_qrcode']);

Route::post('user_note', [HomeController::class, 'add_user_note']);
//

Route::get('add-custom-property-tocontact', [HomeController::class, 'createCustomProperty']);

//
Route::get('tap_view', [HomeController::class, 'tap_view']);
Route::get('analytics', [HomeController::class, 'analytics']);
Route::get('public_profile', [HomeController::class, 'public_profile']); // with icon
Route::get('public_profile_svg', [HomeController::class, 'public_profile_svg']);//  with icon svg
Route::post('add-business-request', [HomeController::class, 'add_business_request']);
Route::get('test-email', [HomeController::class, 'test_email']);
Route::post('update_browser_language', [HomeController::class, 'update_browser_language']);

Route::post('signup', [UserController::class, 'signup']);
Route::post('social_login', [UserController::class, 'social_login']);
Route::post('check_username', [UserController::class, 'check_username']);
Route::post('login', [UserController::class, 'login']);
Route::post('customer-login', [UserController::class, 'login']);
Route::post('verify_pincode', [UserController::class, 'verify_pincode']);
Route::post('verify_login_otp', [UserController::class, 'verify_login_otp']);
Route::post('send_pincode', [UserController::class, 'send_pincode']);
Route::post('send_otp', [UserController::class, 'send_pincode']);
Route::post('reset_password', [UserController::class, 'reset_password']);
Route::get('colors/{user_id}', [UserController::class, 'colors']);
Route::post('verify-signup-otp', [UserController::class, 'verify_sign_otp']);
Route::post('test_save_form', [HomeController::class, 'test_save_form']);
Route::get('apple/wallet_pass/{user_id}', [HomeController::class, 'apple_wallet_pass']);
Route::post('map-codes', [HomeController::class, 'mapCode']);

// addmee multiples Account

Route::prefix('v3')->group(function () {
    Route::group(['middleware' => ['auth:api']], function () {

        /*
        |--------------------------------------------------------------------------
        | Mobile App Routes
        |--------------------------------------------------------------------------
        */
        Route::post('signup-with-new-email', [MultipleChildAccountController::class, 'signupWithNewEmail']); // old
        Route::post('signup-with-existing-email', [MultipleChildAccountController::class, 'signupWithExistingEmail']); // old
        Route::post('child-account-type', [MultipleChildAccountController::class, 'childAccountTypeUpdate']); // old
        Route::get('list_all_profiles_new', [HomeController::class, 'list_all_profiles_new']);
        Route::get('user_profile', [V3UserController::class, 'profile']);
        Route::post('create-child-with-existing-email', [MultipleChildAccountController::class, 'createChildWithExistingEmail']); //with otp
        Route::post('createChildWithExistingEmail', [MultipleChildAccountController::class, 'makeChildWithExistingEmail']);
        Route::post('account-type-update', [MultipleChildAccountController::class, 'AccountTypeUpdate']);
        Route::post('switch_account', [MultipleChildAccountController::class, 'switchAccount']);
        Route::post('delete-account', [V3UserController::class, 'delete_account']);
        Route::post('confirm-delete-account', [V3UserController::class, 'confirm_delete_account']);
    });

    /*
    |--------------------------------------------------------------------------
    | Mobile App Routes
    |--------------------------------------------------------------------------
    */

    Route::post('send_pincode_with_username_email', [V3UserController::class, 'send_pincode_with_username_email']); // old
    Route::post('reset_password_with_username_email', [V3UserController::class, 'reset_password_with_username_email']); // old
    Route::post('login-with-username-email', [V3UserController::class, 'loginWithUsernameEmail']); // old
    Route::post('verify_pincode_with_username_email', [V3UserController::class, 'verify_pincode_with_username_email']); // old

    Route::post('signup', [V3UserController::class, 'signup']);
    Route::post('login', [V3UserController::class, 'login']);
    Route::post('verify-signup-otp', [V3UserController::class, 'verify_sign_otp']);
    Route::post('reset_password', [V3UserController::class, 'reset_password']);
    Route::post('send_pincode', [V3UserController::class, 'send_pincode']);
    Route::post('send_otp_with_username', [V3UserController::class, 'send_otp_with_username']);
    Route::post('send_otp', [V3UserController::class, 'send_otp']);
    Route::post('verify_pincode', [V3UserController::class, 'verify_pincode']);
    Route::get('sendMail', [V3UserController::class, 'sendMail']);
    Route::post('social_login', [V3UserController::class, 'social_login']);
});


//asa
Route::fallback(function () {
    return response()->json([
        'data' => [],
        'success' => false,
        'status' => 404,
        'message' => 'Invalid Route'
    ]);
});
