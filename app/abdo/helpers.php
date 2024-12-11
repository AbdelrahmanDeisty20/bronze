<?php

function resposeJison($status, $msg, $data = null)
{
    $response = [
        "status" => $status,
        "msg" => $msg,
        "data" => $data,
    ];
    return response()->json($response);
}
function financialaccounts($total,$commission,$paid,$amount)
{
    $response = [
        "total" => $total,
        "commission" => $commission,
        "paid" => $paid,
        "amount" => $amount,
    ];
}
function Str_random($length = 10)
{
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
/**
 * @param $to
 * @param $message
 * @return mixed
 */
function smsMisr($to, $message)
{
    $url = 'https://smsmisr.com/api/webapi/?';
    $push_payload = array(
        'username' => "*****",
        'password' => "*****",
        'language' => "2",
        'sender' => "your_sender",
        'mobile' => '2' . $to,
        'message' => $message,
    );
    $reset = curl_init();
    curl_setopt($reset, CURLOPT_URL, $url . http_build_query($push_payload));
    curl_setopt($reset, CURLOPT_PORT, 1);
    curl_setopt($reset, CURLOPT_POSTFIELDS, $push_payload);
    curl_setopt($reset, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt(
        $reset,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/x-www-form-urlencoded'
        )
    );
    curl_setopt($reset, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($reset);
    curl_close($reset);
    return $response;
}
function notifyByFirebase($title,$body,$tokens,$data = [])        // paramete 5 =>>>> $type
{
// https://gist.github.com/rolinger/d6500d65128db95f004041c2b636753a
// API access key from Google FCM App Console
    // env('FCM_API_ACCESS_KEY'));

//    $singleID = 'eEvFbrtfRMA:APA91bFoT2XFPeM5bLQdsa8-HpVbOIllzgITD8gL9wohZBg9U.............mNYTUewd8pjBtoywd';
//    $registrationIDs = array(
//        'eEvFbrtfRMA:APA91bFoT2XFPeM5bLQdsa8-HpVbOIllzgITD8gL9wohZBg9U.............mNYTUewd8pjBtoywd',
//        'eEvFbrtfRMA:APA91bFoT2XFPeM5bLQdsa8-HpVbOIllzgITD8gL9wohZBg9U.............mNYTUewd8pjBtoywd',
//        'eEvFbrtfRMA:APA91bFoT2XFPeM5bLQdsa8-HpVbOIllzgITD8gL9wohZBg9U.............mNYTUewd8pjBtoywd'
//    );
    $registrationIDs = $tokens;

// prep the bundle
// to see all the options for FCM to/notification payload:
// https://firebase.google.com/docs/cloud-messaging/http-server-ref#notification-payload-support

// 'vibrate' available in GCM, but not in FCM
    $fcmMsg = array(
        'body' => $body,
        'title' => $title,
        'sound' => "default",
        'color' => "#203E78"
    );
// I haven't figured 'color' out yet.
// On one phone 'color' was the background color behind the actual app icon.  (ie Samsung Galaxy S5)
// On another phone, it was the color of the app icon. (ie: LG K20 Plush)

// 'to' => $singleID ;      // expecting a single ID
// 'registration_ids' => $registrationIDs ;     // expects an array of ids
// 'priority' => 'high' ; // options are normal and high, if not set, defaults to high.
    $fcmFields = array(
        'registration_ids' => $registrationIDs,
        'priority' => 'high',
        'notification' => $fcmMsg,
        'data' => $data
    );
    //dd(env('FIREBASE_API_ACCESS_KEY'));
    $headers = array(
         'Authorization: key='.env('FIREBASE_API_ACCESS_KEY'),
         'Content-Type: application/json'
     );

    // if($type == 'client')
    // {
    //     $headers = array(
    //         'Authorization: key='.env('API_ACCESS_KEY_client'),
    //         'Content-Type: application/json'
    //     );
    // }
    // if($type == 'driver')
    // {
    //     $headers = array(
    //         'Authorization: key='.env('API_ACCESS_KEY_driver'),
    //         'Content-Type: application/json'
    //     );
    // }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

