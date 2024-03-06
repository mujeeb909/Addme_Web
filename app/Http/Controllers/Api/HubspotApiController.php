<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PlatformIntegration;
use App\Models\Platform;
use GuzzleHttp\Client;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use DateTime;
use DateTimeZone;


class HubspotApiController extends Controller
{
     /**
     * Intigrations of hubspot Api.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function getAccessTokens(Request $request)
    {


        // Validate the request data
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'redirect_url' => 'required|string',
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 'false', 'message' => $validator->errors()->first()], 422);
        }
        // Extract JSON data
        $jsonData = $request->json()->all();
        $client_id = $jsonData['client_id'];
        $client_secret = $jsonData['client_secret'];
        $redirect_url = $jsonData['redirect_url'];
        $code = $jsonData['code'];


        $platform = Platform::where('id',1)->first();
        $platform = $platform ?? new Platform();
        $platform->client_id = $client_id;
        $platform->client_secret = $client_secret;
        $platform->redirect_uri = $redirect_url;
        $platform->save();
        // Call HubSpot API to exchange the code for access tokens
        $httpClient = new Client();

        try {
               $url = main_url();
                if($url == 'https://devaddmee.addmee-portal.de'){
                    $appUrl = 'https://dev.addmee-portal.de';
                }elseif($url == 'https://addmee.app'){
                    $appUrl = 'https://addmee-portal.de';
                }else{
                    $appUrl = 'http://localhost:30001';
                }

            $response = $httpClient->post('https://api.hubapi.com/oauth/v1/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'redirect_uri' => $appUrl . $redirect_url,
                    'code' => $code,
                ],
            ]);

            $hubspotData = json_decode($response->getBody(), true);
            $token = $request->user();
            $user = PlatformIntegration::where('platform_id',1)->where('user_id', $token->id)->first();
            // Create a new instance regardless of whether the user exists or not
            $user = $user ?? new PlatformIntegration();
            $user->user_id = $token->id;
            $user->platform_id = 1;
            $user->code = $code;
            $user->refresh_token = $hubspotData['refresh_token'];
            $user->access_token = $hubspotData['access_token'];
            $user->expires_in = $hubspotData['expires_in'];
            // $user->expires_in = $hubspotData['expires_in'];
            $currentDateTime = new DateTime();
            $minutes = (int)$hubspotData['expires_in'] / 60;
            $currentDateTime->add(new \DateInterval('PT'.$minutes.'M'));
            // Now $currentDateTime contains the current datetime + 30 minutes
            $user->expires_in = $currentDateTime->format('Y-m-d H:i:s');
            $currentTime = now();
            $expires_in = $user->expires_in;
            $differenceInMinutes = $currentTime->diffInMinutes($expires_in);
            // Check if the token needs to be refreshed
            if ($differenceInMinutes > 30) {
                $user->save();
            }else{
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'HubSpot connected successfully',
                'data' => [
                    'id' => $user->id,
                    'platform_id' => $user->platform_id,
                    'access_token' => $user->access_token,


                ],
            ]);
        } catch (\Exception $e) {
            // Handle specific error messages
            $statusCode = $e->getResponse()->getStatusCode();
            $errorMessage = json_decode($e->getResponse()->getBody(), true)['message'];
            return response()->json(['success' => 'false', 'message' => $errorMessage], $statusCode);
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function getHubspotContacts(Request $request)
    {

        $check_user = PlatformIntegration::where('platform_id',1)->where('user_id', $request->user_id)
        ->first();
        if($check_user){
        $accessToken = $check_user->access_token;
        $refreshToken = $check_user->refresh_token;
        $currentTime = now();
        $expires_in = $check_user->expires_in;
        $differenceInMinutes = $currentTime->diffInMinutes($expires_in);
        // Check if the token needs to be refreshed
        if ($currentTime > $expires_in) {
            $accessToken = generateAccessTokenWithRefreshToken($refreshToken,$check_user);
        }
       }else{
        $accessToken = $request->accesstoken;
       }

        $client = new Client();
        $endpoint = 'https://api.hubapi.com/crm/v3/objects/contacts';

        try {
            $response = $client->get($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $contacts = json_decode($response->getBody(), true);

            if (isset($contacts['results'])) {
                // Process the retrieved contacts
                return $contacts['results'];
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'No Contacts Found',
                ]);
            }
        } catch (\Exception $e) {
            // Handle specific error messages
            $statusCode = $e->getResponse()->getStatusCode();
            $errorMessage = json_decode($e->getResponse()->getBody(), true)['message'];

            if ($statusCode === 401) {
                // Token expired error
                return response()->json(['success' => false, 'message' => 'Token has expired'], 401);
            } else {
                // Handle other errors
                return response()->json(['success' => false, 'message' => $errorMessage], $statusCode);
            }
        }
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

public function create(Request $request)
{
   //
}



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
