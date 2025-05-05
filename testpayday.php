<?php
include 'admin.php';
require __DIR__ . '/vendor/autoload.php';
use Twilio\Rest\Client;


$admin = new Admin();

$email = "test@gmail.com";
$message = '<p style="font-size: 18px; color: #004c00; font-family: Helvetica;">Dear Admin/Landlord <strong>Name Middlename Lastname</strong>,</p>';
$message .= '<p style="font-size: 16px; color: #414141;">';
$message .= ' John is already pass their due on their eviction notice.<br><br>';
$message .= 'Best regards,<br>Renttrack Pro<br></p>';
$admin->sendEmail($email, "Eviction Reminder", $message);

$query = "SELECT * FROM users where role='admin' LIMIT 1";
$result = $admin->conn->query($query);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $phonenumber = $row['phonenumber'];
    }
}

// if (true) {
    // Prepare the message
    $smsMessage = "Dear Admin/Landlord Name Middlename Lastname, John is already pass their due on their eviction notice. ";

    // Set up the cURL request to send SMS
    $ch = curl_init();
    $parameters = array(
    'apikey' => '', // Replace with your actual API key
    'number' => $phonenumber,  // Recipient's number
    'message' => $smsMessage,
    'sendername' => 'Thesis' // Replace with your registered sender name
    );

    // Set cURL options for the request
    curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the cURL request and get the response
    $output = curl_exec($ch);

    // Close the cURL session
    curl_close($ch);
// }
?>