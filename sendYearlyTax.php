<?php
include 'admin.php';
require __DIR__ . '/vendor/autoload.php';
use Twilio\Rest\Client;


$admin = new Admin();

$query = "SELECT * FROM users WHERE role='admin' LIMIT 1";
$result = $admin->conn->query($query);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $email = $row['email'];
        $name = $row['firstname'] . " " . $row['middlename'] . " " . $row['lastname'];
        $phoneNumber = $row['phonenumber'];
    }
}

// Get the current date
$current_date = date('Y-m-d');

$trigger_date = date('Y') . '-12-01'; // Generates "YYYY-12-01" for the current year

if ($current_date === $trigger_date) {
    // Code to execute on December 1
    echo "its january 21 " . $email;
    $message = '<p style="font-size: 18px; color: #004c00; font-family: Helvetica;">Dear Admin/Landlord <strong>' . $name . '</strong>,</p>';
    $message .= '<p style="font-size: 16px; color: #414141;">';
    $message .= 'This is a reminder to pay your tax this year.<br><br>';
    $message .= 'Best regards,<br>Renttrack Pro<br></p>';
    $admin->sendEmail($email, "Yearly Tax", $message);

    if ($phoneNumber) {
      // Prepare the message
      $smsMessage = "Dear Admin/Landlord " . $name . " This is a reminder to pay your tax this year. ";

      // Set up the cURL request to send SMS
      $ch = curl_init();
      $parameters = array(
        'apikey' => '', // Replace with your actual API key
        'number' => $phoneNumber,  // Recipient's number
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
    }
}
?>