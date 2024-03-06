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


class PlatformIntegrationController extends Controller
{
     /**
     * Get Platforms.
     *
     * @return \Illuminate\Http\Response
     */

 public function getIntegrationsPlatforms()
 {
    $platforms = Platform::get();

    if ($platforms->isEmpty()) {
        return response()->json([
            'success' => true,
            'message' => 'No platforms available to integrate.',
            'data' => [],
        ]);
    }

    $formattedPlatforms = $platforms->map(function ($platform) {
        return [
            'id' => $platform->id,
            'name' => $platform->name,
            'icon_svg' => $platform->icon,
            'title' => $platform->title,
            'description' => $platform->description,
        ];
    });

    return response()->json([
        'success' => true,
        'message' => count($platforms) . ' platform(s) available to integrate.',
        'data' => $formattedPlatforms,
    ]);
 }


    /**
     * Show Platforms Integrations.
     * * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */

public function getIntegrations(Request $request)
{
    $token = $request->user();
    $platformsIntegrations = PlatformIntegration::where('user_id',$token->id)->get();


   if ($platformsIntegrations->isEmpty()) {
       return response()->json([
           'success' => true,
           'message' => 'No integration found.',
           'data' => [],
       ]);
   }

   $platformsIntegrations = $platformsIntegrations->map(function ($platformsIntegrations) {
       return [
           'id' => $platformsIntegrations->id,
           'platform_id' => $platformsIntegrations->platform_id,
           //'code' => $platformsIntegrations->code,
           'access_token' => $platformsIntegrations->access_token,
       ];
   });

   return response()->json([
       'success' => true,
       'message' => count($platformsIntegrations) . ' integration found.',
       'data' => $platformsIntegrations,
   ]);
}

public function deleteIntegration($id)
{
    $integration = PlatformIntegration::find($id);
    if (!$integration) {
        return response()->json((object)[
            'success' => true,
            'message' => 'Integration not found',
            'data' => (object)['id' => (int)$id],
        ], 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    $integration->delete();

    return response()->json((object)[
        'success' => true,
        'message' => 'Integration disconnected successfully',
        'data' => (object)['id' => (int)$id],
    ], 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}




    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request client_id
     * @param \Illuminate\Http\Request client_secret
     * @param \Illuminate\Http\Request redirect_url
     * @param \Illuminate\Http\Request access_token
     * @return \Illuminate\Http\Response refresh_token
     * @return \Illuminate\Http\Response expires_in
     */

     public function getAzureAdAccessTokens(Request $request)
     {


         // Validate the request data
         $validator = Validator::make($request->all(), [
             'client_secret' => 'required|string',
             'client_id' => 'required|string',
             'redirect_url' => 'required|string',
             'access_token' => 'required|string',
            //  'refresh_token' => 'required|string',
             'expires_on' => 'required|string',
         ]);

         if ($validator->fails()) {
             return response()->json(['success' => 'false', 'message' => $validator->errors()->first()], 422);
         }

         // Extract JSON data
         $jsonData = $request->json()->all();
         $client_secret = $jsonData['client_secret'];
         $client_id = $jsonData['client_id'];
         $redirect_url = $jsonData['redirect_url'];
         $access_token = $jsonData['access_token'];
        //  $refresh_token = $jsonData['refresh_token'];
          $expires_on = $jsonData['expires_on'];

         try {

            $platform = Platform::where('name','azure-ad')->first();
            $platform = $platform ?? new Platform();
            $platform->client_secret = $client_secret;
            $platform->client_id = $client_id;
            $platform->redirect_uri = $redirect_url;
            $platform->save();

            $token = $request->user();
            $user = PlatformIntegration::where('platform_id',$platform->id)->where('user_id', $token->id)->first();
            // Create a new instance regardless of whether the user exists or not
            $user = $user ?? new PlatformIntegration();
            $user->user_id = $token->id;
            $user->platform_id = $platform->id;
            $user->access_token = $access_token;
            $carbonDate = Carbon::parse($expires_on);
            $formattedDate = $carbonDate->format('Y-m-d H:i:s');
            $user->expires_in = $formattedDate;
            $user->save();
            // $user->refresh_token = $refresh_token;
            // $user->expires_in = $expires_in;
            // $currentDateTime = new DateTime();
            // $minutes = (int)$expires_in / 60;
            // $currentDateTime->add(new \DateInterval('PT'.$minutes.'M'));
            // // Now $currentDateTime contains the current datetime + 30 minutes
            // $user->expires_in = $currentDateTime->format('Y-m-d H:i:s');
            // $currentTime = now();
            // $expires_in = $user->expires_in;
            // $differenceInMinutes = $currentTime->diffInMinutes($expires_in);
            // // Check if the token needs to be refreshed
            // if ($differenceInMinutes > 30) {
            //     $user->save();
            // }else{
            //     $user->save();
            // }

            return response()->json([
                'success' => true,
                'message' => 'Azure AD connected successfully',
                'data' => [
                    'id' => $user->id,
                    'platform_id' => $user->platform_id,
                    'access_token' => $user->access_token,
                ],
            ]);
         } catch (\Exception $e) {

            return response()->json(['success' => 'false', 'message' => $e->getMessage()], 500);
         }
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
