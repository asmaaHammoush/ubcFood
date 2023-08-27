<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


//    public function sendNotification(Request $request)
//    {
//        $data = [
//            "to" => $request->token,
//            "notification" => [
//                "title" => "Push Notification Title",
//                "body" => "Push Notification Body",
//                "icon" => "myicon",
//                "click_action" => "https://example.com"
//            ],
//            "data" => [
//                "custom_key" => "custom_value"
//            ]
//        ];
//
//        $serverKey = "AAAA494fPuU:APA91bHH9eT2OlXatVooxTA0DZKFmArTuqmhmsNmpyRV8hiJceGBJQNkVp3xYp-BvtKTn6sv1XY3tbzutXNhn9HYrEhmz8BfUaSkGe2V_s9ztxYa3cTVsxpHU1WH5bC2Z6x3enNFYMdE";
//        $payload = [
//            "iss" => "firebase-adminsdk",
//            "sub" => $serverKey,
//            "aud" => "https://fcm.googleapis.com/",
//            "iat" => time(),
//            "exp" => time() + 60*60 // One hour
//        ];
//
//        $jwt = JWTAuth::encode($payload, $serverKey, 'HS256');
//
//        $curl = curl_init();
//
//        curl_setopt_array($curl, [
//            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_CUSTOMREQUEST => "POST",
//            CURLOPT_POSTFIELDS => json_encode($data),
//            CURLOPT_HTTPHEADER => [
//                "Authorization: Bearer $jwt,",
//                "Content-Type: application/json"
//            ]
//        ]);
//
//        $response = curl_exec($curl);
//        $err = curl_error($curl);
//
//        curl_close($curl);
//
//        if ($err) {
//            return response()->json(["success" => false, "message" => $err]);
//        } else {
//            return response()->json(["success" => true, "message" => $response]);
//        }
//    }

    public function sendWebNotification(Request $request) {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = 'AAAAsAMpnI8:APA91bHNNjRb6T5yDjtU6JZM5rIZaxZtzZEORhMVKZ-Hmm4PI2c_zFkoRN7AnLw5-5yikyEZFW-AfDE7rrABFJs2CNFvsH0RvHUGVRM18fKjOYStQNI5rr22Ewp_bZVBKczlLDlerfVs';
        $data = '{
            "to" : "' .$request->device_key . '",
            "notification" : {
                "title" :  "' .$request->title . '",
                "body" :  "' .$request->body . '",
                },
        }';
//       $encodedData = json_encode($data);
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
            ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Execute post
        $result = curl_exec($ch);
         dd($result);
    }


//    public function sendWebNotification(Request $request)
//    {
//        $token = $request->device_key;
//        $from = "AAAAsAMpnI8:APA91bHNNjRb6T5yDjtU6JZM5rIZaxZtzZEORhMVKZ-Hmm4PI2c_zFkoRN7AnLw5-5yikyEZFW-
//        AfDE7rrABFJs2CNFvsH0RvHUGVRM18fKjOYStQNI5rr22Ewp_bZVBKczlLDlerfVs";
//        $msg = array
//        (
//            'body' => "Testing Testing",
//            'title' => "Hi, From Raj",
//            'receiver' => 'erw',
//            'icon' => "https://image.flaticon.com/icons/png/512/270/270014.png",/*Default Icon*/
//            'sound' => 'mySound'/*Default sound*/
//        );
//
//        $fields = array
//        (
//            'to' => $token,
//            'notification' => $msg
//        );
//
//        $headers = array
//        (
//            'Authorization: key=' . $from,
//            'Content-Type: application/json'
//        );
//        //#Send Reponse To FireBase Server
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
//        $result = curl_exec($ch);
//        dd($result);
//        curl_close($ch);
//        // if ($result === FALSE) { die('Curl failed: ' . curl_error($ch)); }
//        // Close connection curl_close($ch);
//        // FCM response dd($result); } }
//    }
}
