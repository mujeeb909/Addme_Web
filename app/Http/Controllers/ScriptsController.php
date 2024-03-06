<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use Mailjet\Resources;

class ScriptsController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function list_free_users_with_no_devices(Request $request)
    {
        $users = User::query()->select('users.id', 'users.first_name', 'users.last_name', 'users.username')->where('is_pro', 0)->whereRaw('users.id NOT IN (SELECT DISTINCT user_id FROM unique_codes WHERE user_id != 0)')->get();
        if (!empty($users)) {
            foreach ($users as $user) {
                $html = 'Hi ' . $user->first_name . ' ' . $user->last_name . ',<br><br>
                Your profile on ' . config("app.name", "") . ' has been open direct.<br><br>
                Your ' . config("app.name", "") . '';
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
            }
        }
    }
}
