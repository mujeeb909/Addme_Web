<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Auth::routes();
Route::get('/test', function () {
    die('a');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [HomeController::class, 'index']);
Route::get('/g-unique-codes', [HomeController::class, 'generate_unique_codes']);

Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard')->middleware(['auth']);
Route::get('/admin-dashboard', [AdminController::class, 'index'])->name('admin.dash')->middleware(['auth']);
Route::get('/admin-panel', [AdminController::class, 'index'])->name('admin.panel')->middleware(['auth']);
Route::post('/admin-panel', [AdminController::class, 'index'])->name('admin.panels')->middleware(['auth']);
Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::get('/admin-login', [AdminController::class, 'login']);
Route::post('/admin/login', [AdminController::class, 'login']);
Route::get('/admin/logout', [AdminController::class, 'logout']);

Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {

    Route::get('/profile', [AdminController::class, 'profile'])->name('view.profile');
    Route::post('/profile', [AdminController::class, 'profile'])->name('update.profile');
    Route::post('/change_password', [AdminController::class, 'change_password'])->name('change.password');

    Route::get('/staffs', [AdminController::class, 'staffs'])->name('admin.staffs');
    Route::get('/add_staff/{id?}', [AdminController::class, 'add_staff'])->name('add.staff');
    Route::get('/update_staff/{id?}', [AdminController::class, 'add_staff'])->name('update.staff');
    Route::post('/add_staff', [AdminController::class, 'add_staff']);

    Route::get('/user_groups', [AdminController::class, 'user_groups'])->name('admin.user_groups');
    Route::get('/add_user_group/{id?}', [AdminController::class, 'add_user_group'])->name('add.user_group');
    Route::get('/update_user_group/{id?}', [AdminController::class, 'add_user_group'])->name('update.user_group');
    Route::post('/add_user_group', [AdminController::class, 'add_user_group']);

    Route::get('/permissions/{id}', [AdminController::class, 'permissions'])->name('admin.permissions');
    Route::post('/permissions/{id}', [AdminController::class, 'permissions']);

    Route::get('/customers', [AdminController::class, 'customers'])->name('admin.customers');
    Route::get('/childs/{id?}', [AdminController::class, 'childs'])->name('admin.childs');
    Route::get('/add_customer/{id?}', [AdminController::class, 'add_customer'])->name('add.customer');
    Route::get('/update_customer/{id?}', [AdminController::class, 'add_customer'])->name('update.customer');
    Route::post('/add_customer', [AdminController::class, 'add_customer']);
    Route::post('/import_customers', [AdminController::class, 'import_customers']);
    Route::get('/import_customers', [AdminController::class, 'import_customers']);
    Route::get('/assign_customer_cp/{id}', [AdminController::class, 'assign_customer_cp'])->name('assign_customer_to_cp');
    Route::post('/assign_customer_cp', [AdminController::class, 'assign_customer_cp']);

    Route::get('/clients', [AdminController::class, 'clients'])->name('admin.clients');
    Route::get('/add_client/{id?}', [AdminController::class, 'add_client'])->name('add.client');
    Route::get('/update_client/{id?}', [AdminController::class, 'add_client'])->name('update.client');
    Route::post('/add_client', [AdminController::class, 'add_client']);

    Route::get('/chips', [AdminController::class, 'chips'])->name('admin.chips');
    Route::get('/add_chip/{id?}', [AdminController::class, 'add_chip'])->name('add.chip');
    Route::get('/update_chip/{id?}', [AdminController::class, 'add_chip'])->name('update.chip');
    Route::post('/add_chip', [AdminController::class, 'add_chip']);
    Route::get('/upload_chip_csv/{id?}', [AdminController::class, 'upload_chip_csv'])->name('upload.chip_csv');
    Route::post('/upload_chip_csv', [AdminController::class, 'upload_chip_csv']);
    Route::post('/export_chips', [AdminController::class, 'export_chips'])->name('admin.export_chips');
    Route::post('/import_codes_csv', [AdminController::class, 'import_codes_csv'])->name('admin.import_codes_csv');
    Route::get('/reactivate_chip', [AdminController::class, 'reactivate_chip'])->name('reactivate.chip');
    Route::post('/reactivate_chip', [AdminController::class, 'reactivate_chip']);

    Route::get('/profiles', [AdminController::class, 'profiles'])->name('admin.profiles');
    Route::get('/add_profile/{id?}', [AdminController::class, 'add_profile'])->name('add.profile');
    Route::get('/update_profile/{id?}', [AdminController::class, 'add_profile'])->name('update.profiles');
    Route::post('/add_profile', [AdminController::class, 'add_profile']);
    Route::get('/delete', [AdminController::class, 'delete'])->name('admin.delete');

    Route::get('/feedbacks', [AdminController::class, 'feedbacks'])->name('admin.feedbacks');
    Route::get('/business_requests', [AdminController::class, 'business_requests'])->name('admin.business_requests');

    Route::get('/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');
    Route::get('/add_notification', [AdminController::class, 'add_notification'])->name('add.notification');
    Route::post('/add_notification', [AdminController::class, 'add_notification']);

    Route::get('/business_customers', [CustomerController::class, 'business_customers'])->name('admin.business_customers');
    Route::get('/add_business_customer', [CustomerController::class, 'add_business_customer'])->name('add.business_customer');
    Route::get('/update_business_customer/{id?}', [CustomerController::class, 'add_business_customer'])->name('update.business_customer');
    Route::post('/add_business_customer', [CustomerController::class, 'add_business_customer']);
    Route::get('/delete_business_customer/{id?}', [AdminController::class, 'delete_business_customer'])->name('admin.user.delete');
    Route::post('/delete_business_customer', [AdminController::class, 'delete_business_customer']);

    Route::get('/platforms', [AdminController::class, 'platforms'])->name('admin.platforms');
    Route::get('/add_platform/{id?}', [AdminController::class, 'add_platform'])->name('add.platform');
    Route::post('/add_platform', [AdminController::class, 'add_platform']);
    Route::get('/update_platform/{id?}', [AdminController::class, 'add_platform'])->name('update.platform');
    Route::get('exportBpAdmins', [AdminController::class, 'exportBpAdmins'])->name('exportBpAdmins');
});

Route::get('/download_csv', [AdminController::class, 'download_csv'])->name('admin.download_csv');

Route::get('/terms-conditions', [HomeController::class, 'terms'])->name('terms');
Route::get('/privacy-policy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/contact-card/{id}', [HomeController::class, 'contact_card']);
Route::get('/contact-lead/{id}', [HomeController::class, 'contact_lead']);

// Route::get('/addmee', [HomeController::class, 'profile'])->name('viewprofile');
// if (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] == 'application/json') {
Route::get('{device}/{code}', [HomeController::class, 'activateCode'])->name('activateCode')->middleware('auth:api');
// Route::get('{code}', [HomeController::class, 'activateCode'])->name('activateCode')->middleware('auth:api');
// Route::get('{code}/{device?}', [HomeController::class, 'activateCode'])->name('activateCode')->middleware('auth:api');
// } else {
Route::get('/{username}', [HomeController::class, 'profile'])->name('profile');
// Route::middleware('cache.response:60')->get('/username', 'HomeController@profile')->name('profile');

Route::get('{device}/{username}', [HomeController::class, 'profile'])->name('profile.view');
Route::get('devices/activate/{code}', [CustomerController::class, 'members_activation_details'])->middleware('auth:api');
// }

Route::get('/page/app/stores', [HomeController::class, 'app_store'])->name('app_store');
Route::get('/pages/pages/test-test', [HomeController::class, 'test'])->name('test');
Route::get('/test/contact_card/{username}', [HomeController::class, 'test_contact_card'])->name('test_contact_card');

//api routes
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
