<?php
include 'admin.php';
require __DIR__ . '/vendor/autoload.php';
use Twilio\Rest\Client;


$admin = new Admin();
$notifications = $admin->sendMonthlyPaymentNotifications();

// $account_sid = "";
// $auth_token = "";

// // A Twilio number you own with SMS capabilities
// $twilio_number = "+18133363443";

// $client = new Client($account_sid, $auth_token);
// $message = $client->messages->create(
//     // Where to send a text message (your cell phone?)
//     '+639955835160',
//     array(
//         'from' => $twilio_number,
//         'body' => 'SABAY SABAY TAYONG BIBITAWW'
//     )
// );

// if($message) {
//     echo 'Sent to Phone number';
// } else {
//     echo 'Text to Number Failed';
// }



// Initialize a cURL session
$ch = curl_init();

foreach ($notifications as $notification) {
    $balance = $notification['balance'];
    $message = "Dear {$notification['fname']} {$notification['lname']}, your monthly rent is ₱" . number_format($balance, 2) . ". Pay as soon as possible.";

    // Set the parameters for the API request
    $parameters = array(
        'apikey' => '', // Replace with your actual API key
        'number' => $notification['phonenumber'],  // Replace with the recipient's number
        'message' => $message,
        'sendername' => 'Thesis' // Replace with your registered sender name
    );

    // Set cURL options for the request
    curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the cURL request and get the response
    $output = curl_exec($ch);

    // Output the response from Semaphore
    echo $output . "\n";
}

// Close the cURL session
curl_close($ch);



// // Set the parameters for the API request
// $parameters = array(
//     'apikey' => '', // Replace with your actual API key
//     'number' => '09955835160',  // Replace with the recipient's number
//     'message' => 'I just sent my first message with Semaphore',
//     'sendername' => 'Thesis' // Replace with your registered sender name
// );

// // Set cURL options for the request
// curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
// curl_setopt($ch, CURLOPT_POST, 1);
// curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// // Execute the cURL request and get the response
// $output = curl_exec($ch);

// // Close the cURL session
// curl_close($ch);

// // Output the response from Semaphore
// echo $output;

// $admin->sendMonthlyPaymentNotifications();
