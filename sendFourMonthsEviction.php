<?php
include 'admin.php';
require __DIR__ . '/vendor/autoload.php';

use setasign\Fpdi\Fpdi;
use Twilio\Rest\Client;


$admin = new Admin();

// Get tenants eligible for email notification
$query = "
    SELECT 
        tenants.id AS tenant_id,
        tenants.*, 
        users.email, users.phonenumber, users.firstname, users.middlename, users.lastname, 
        houses.*,
        GROUP_CONCAT(payments.amount ORDER BY payments.date_payment DESC) AS payment_amounts,
        GROUP_CONCAT(payments.date_payment ORDER BY payments.date_payment DESC) AS date_payment,
        MAX(payments.date_payment) AS last_payment_date  -- Get the last payment date
    FROM tenants 
    LEFt JOIN users ON tenants.users_id = users.id
    LEFT JOIN payments ON tenants.id = payments.tenants_id AND payments.approval = 'true' -- Include only approved payments
    LEFT JOIN houses ON tenants.house_id = houses.id
    GROUP BY tenants.id
";
$result = $admin->conn->query($query);

$hasData = false;
echo "<table>";
echo "<tbody>";
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        // $tenant_id = $row['id']; // Tenant ID
        // $date_preferred = $row['date_preferred'];
        // $last_payment_date = $row['last_payment_date'];
        // $monthly_rent = $row['price'];  // Monthly rent from the houses table

        // // Fetch notificationsendmonths from tenants table
        // $notificationsendmonths = (int)$row['notification_sent_months'];

        // // If no payment has been made, set the last payment date to the current date
        // if (!$last_payment_date) {
        //     $last_payment_date = date('Y-m-d');
        // }

        // // Convert dates to timestamps for easier date manipulation
        // $date_preferred_timestamp = strtotime($date_preferred);
        // $current_date_timestamp = strtotime(date('Y-m-d'));  // Today's date

        // // Offset date_preferred by 1 month
        // $start_date_timestamp = strtotime("+1 month", $date_preferred_timestamp);

        // // Calculate the number of months between start_date and today
        // $months_difference = (date('Y', $current_date_timestamp) - date('Y', $start_date_timestamp)) * 12 
        //                     + date('m', $current_date_timestamp) - date('m', $start_date_timestamp);

        // $missing_months = 0;
        // $missed_months_dates = []; // Array to store missed months
        // $missing_payment_total = 0; // Total amount of missing payments
        // $total_paid = array_sum(explode(',', $row['payment_amounts'])); // Total payments made
        // $remaining_payment_balance = $total_paid; // Track remaining payment balance

        // Loop through all months from start_date to today
        // for ($i = 0; $i <= $months_difference; $i++) {
        //     $current_month = date('Y-m', strtotime("+$i months", $start_date_timestamp));
        //     $payment_found = false;

        //     // Check if the current month has any payment
        //     foreach (explode(',', $row['date_payment']) as $key => $payment_date) {
        //         if (substr($payment_date, 0, 7) == $current_month) { // Check year-month format
        //             $payment_found = true;

        //             // Add the corresponding amount paid to the monthly tracker
        //             $amounts = explode(',', $row['payment_amounts']);
        //             $monthly_paid = isset($amounts[$key]) ? (float)$amounts[$key] : 0;

        //             echo "<br>" . $current_month . " " . $remaining_payment_balance . " * " . $monthly_paid . ", <br><br>";

        //             // Deduct from remaining payment balance
        //             $remaining_payment_balance -= $monthly_paid;
        //             $remaining_payment_balance = max(0, $remaining_payment_balance); // Ensure it doesn't go below 0
        //             break;
        //         }
        //     }

        //     // If no payment is found for this month OR the payment balance can't cover this month
        //     if (!$payment_found || $remaining_payment_balance < $monthly_rent) {
        //         $missing_months++;
        //         $missed_months_dates[] = $current_month; // Add to missed months list
        //         echo $remaining_payment_balance . " - " . $monthly_rent . ", ";
        //         // Deduct monthly rent from the remaining payment balance (if any)
        //         $remaining_payment_balance -= $monthly_rent;
        //         $remaining_payment_balance = max(0, $remaining_payment_balance); // Ensure it doesn't go below 0
                
        //         // Add the monthly rent to the total missing payment amount
        //         $missing_payment_total += $monthly_rent; // Add the house price (monthly rent) to the total
        //     }
        // }



        $tenant_id = $row['id']; // Tenant ID
        $date_preferred = $row['date_preferred'];
        $last_payment_date = $row['last_payment_date'];
        $monthly_rent = $row['price'];  // Monthly rent from the houses table

        // If no payment has been made, set the last payment date to the current date
        if (!$last_payment_date) {
            $last_payment_date = date('Y-m-d');
        }

        // Convert dates to timestamps for easier date manipulation
        $date_preferred_timestamp = strtotime($date_preferred);
        $current_date_timestamp = strtotime(date('Y-m-d'));  // Today's date

        // Offset date_preferred by 1 month
        $start_date_timestamp = strtotime("+1 month", $date_preferred_timestamp);

        // Calculate the number of months between start_date and today
        $months_difference = (date('Y', $current_date_timestamp) - date('Y', $start_date_timestamp)) * 12 
                            + date('m', $current_date_timestamp) - date('m', $start_date_timestamp);

        $missing_months = 0;
        $missed_months_dates = []; // Array to store missed months
        $missing_payment_total = 0; // Total amount of missing payments

        // Split payment dates and amounts into arrays
        $payment_dates = explode(',', $row['date_payment']);
        $payment_amounts = array_map('floatval', explode(',', $row['payment_amounts'])); // Convert to float

        for ($i = 0; $i <= $months_difference; $i++) {
            $current_month = date('Y-m', strtotime("+$i months", $start_date_timestamp));
            $monthly_paid = 0; // Track payments for the current month

            // Check payments for the current month
            foreach ($payment_dates as $key => $payment_date) {
                if (substr($payment_date, 0, 7) == $current_month) { // Check year-month format
                    $monthly_paid += isset($payment_amounts[$key]) ? $payment_amounts[$key] : 0;
                }
            }
            echo $monthly_paid . ", ";

            // If payments for the current month are less than the monthly rent, count as missed
            if ($monthly_paid < $monthly_rent) {
                $missing_months++;
                $missed_months_dates[] = $current_month; // Add to missed months list

                // Calculate the shortfall for this month
                $shortfall = $monthly_rent - $monthly_paid;
                $missing_payment_total += $shortfall; // Add shortfall to total missing payment
            }
        }

        // Ensure missing_payment_total does not go below 0
        // $missing_payment_total = max(0, $missing_payment_total);

        $remindersendmonths = $row['reminder_sent_months'];
        $tenant_probably_real_id = $row['tenant_id'];

        echo "<table>";
        echo "<tbody style='border: 1px solid black;'>";
        echo "<tr style='border: 1px solid black;'>";
        echo "<td>" . htmlspecialchars($row['fname']) . " " . htmlspecialchars($row['mname']) . " " . htmlspecialchars($row['lname']) . "</td>";
        echo "<td class='text-center'>" . ($row['house_name']) . "</td>";
        echo "<td>" . htmlspecialchars(implode(', ', $missed_months_dates)) . "</td>";
        echo "<td>Months=" . htmlspecialchars($missing_months) . "</td>";
        echo "<td>" . htmlspecialchars($row['payment_amounts']) . "</td>";
        echo "<td>" . htmlspecialchars($missing_payment_total) . "</td>"; // Display the total missing payment
        echo "</tr>";
        echo "</tbody>";
        echo "</table>";

        // Send Reminders automatically when tenant has 2 missing months
        if ($missing_months == $remindersendmonths + 2) {
            $message = '<p style="font-size: 18px; color: #004c00; font-family: Helvetica;">' . htmlspecialchars($row['fname']) . " " . htmlspecialchars($row['mname']) . " " . htmlspecialchars($row['lname']) . ', <strong></strong>,</p>';
            $message .= '<p style="font-size: 16px; color: #414141;">';
            $message .= 'Be reminded of your ' . $missing_months . ' missed payments.<br><br> For the months of ' . htmlspecialchars(implode(', ', $missed_months_dates));
            $message .= '<br>Best regards,<br>Renttrack Pro<br></p>';
            $admin->sendEmail($row['email'], "Reminder", $message);

            $phoneNumber = $row['phonenumber'];
            if ($phoneNumber) {
                // Prepare the message
                $smsMessage = "Be reminded of your " . $missing_months . " missed payments. For the months of " . htmlspecialchars(implode(', ', $missed_months_dates));

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
            
            $reminder_query = "UPDATE tenants set reminder_sent_months = 2 WHERE id=$tenant_probably_real_id";
            $admin->conn->query($reminder_query);
        }
        // return;

        // Check if we should send the reminder based on missing months
        if ($missing_months >= $notificationsendmonths + 4) {
            // sendEmail($row['email'], $missed_months_dates, $missing_payment_total);
            // Function to send email
            $message = "This is a reminder that your payment has been overdue for " . $missing_months . ". Please settle it as soon as possible.";
            $admin->sendEmail($row['email'], "Eviction Notice", $message);

            // Eviction Settings Values
            $eviction_setting = "SELECT * FROM eviction_setting";
            $setting_result = $admin->conn->query($eviction_setting);
            if ($setting_result->num_rows > 0) {
                while($setting_row = $setting_result->fetch_assoc()) {
                    $landlordaddress = $setting_row['landlord_address'];
                    $evictionpaydays = $setting_row['days_to_pay'];
                }
            }

            // Admin (Landlord)
            $landlord_admin = "SELECT * FROM users WHERE role='admin' LIMIT 1";
            $landlord_admin_result = $admin->conn->query($landlord_admin);
            if ($landlord_admin_result->num_rows > 0) {
                while($landlord_admin_row = $landlord_admin_result->fetch_assoc()) {
                    $adminfirstname = $landlord_admin_row['firstname'];
                    $adminmiddlename = $landlord_admin_row['middlename'];
                    $adminlastname = $landlord_admin_row['lastname'];
                    $adminphonenumber = $landlord_admin_row['phonenumber'];
                }
            }

            $pdf = new Fpdi();
            // Load the PDF template
            $templateFile = __DIR__ . "/asset/eviction_template.pdf";
            $pageCount = $pdf->setSourceFile($templateFile);
            // Import the first page of the template
            $template = $pdf->importPage(1);
            // Add a page to the new PDF
            $pdf->AddPage();
            // Use the imported template
            $pdf->useTemplate($template);
            // Set the font for adding text
            $pdf->SetFont('Helvetica', '', 12);

            $defaultTimezone = date_default_timezone_get();
            date_default_timezone_set('Asia/Manila');
            $pdf->SetXY(80, 60); 
            $pdf->Cell(150, 7, date('Y-m-d'), 0, 'L');
            date_default_timezone_set($defaultTimezone);

            $pdf->SetXY(80, 68); 
            $pdf->Cell(50, 7, $tenantname = $row['fname'] . " " . $row['mname'] . " " . $row['lname'], 0, 'L');

            $pdf->SetXY(80, 77); 
            $pdf->Cell(50, 7, $row['address'], 0, 'L');

            $pdf->SetXY(108, 101); 
            $pdf->Cell(50, 7, $missing_payment_total, 0, 'L');

            $pdf->SetXY(104, 115); 
            $pdf->Cell(50, 7, $row['price'], 0, 'L');

            $pdf->SetXY(87, 122.5); 
            $pdf->Cell(50, 7, implode(', ', $missed_months_dates), 0, 'L');

            $pdf->SetXY(99, 131); 
            $pdf->Cell(50, 7, $missing_payment_total, 0, 'L');

            $pdf->SetXY(32, 152); 
            $pdf->Cell(50, 7, $evictionpaydays, 0, 'L',"C");

            $pdf->SetXY(74, 184); 
            $pdf->Cell(50, 7, $adminfirstname . " " . $adminmiddlename . " " . $adminlastname, 0, 'L');
            
            $pdf->SetXY(44, 192); 
            $pdf->Cell(50, 7, (empty($landlordaddress) ? "Bagatua Compound" : $landlordaddress ), 0, 'L');

            $pdf->SetXY(48, 200); 
            $pdf->Cell(50, 7, $adminphonenumber, 0, 'L');

            $pdf->SetXY(83, 232); 
            $pdf->Cell(50, 7, $adminfirstname . " " . $adminmiddlename . " " . $adminlastname, 0, 'L',"C");

            $signatureImageforPdf = __DIR__ . "/asset/eviction_tenant/landlord_signature.png";
            $pdf->Image($signatureImageforPdf, 83, 222, 50);
            $newFileName = __DIR__ . DIRECTORY_SEPARATOR . "asset" . DIRECTORY_SEPARATOR . "eviction_tenant" . DIRECTORY_SEPARATOR . 
               "eviction_" . $tenant_id . "_" . time() . "_" . uniqid() . ".pdf";
            $pdf->Output('F', $newFileName);

            $updateQuery = "
                UPDATE tenants
                SET notification_sent_months = $notificationsendmonths + 4
                WHERE id = $tenant_id
            ";
            $admin->conn->query($updateQuery);


            $fileName = basename($newFileName);
            $insertSql = "INSERT INTO eviction_popup (users_id, file_path) 
                  VALUES (?, ?)";
            $insertStmt = $admin->conn->prepare($insertSql);
            $insertStmt->bind_param("is", $row['users_id'], $fileName);
            $insertStmt->execute();
        }

        // Display the tenant if missing months are more than or equal to 1
        if ($missing_months >= $row['notification_sent_months'] + 4) {
            $hasData = true; // Mark that at least one row is displayed
            echo "<tr>";
                echo "<th scope='row'>" . $row['tenant_id'] . "</th>";
                echo "<td>" . htmlspecialchars($row['fname']) . " " . htmlspecialchars($row['mname']) . " " . htmlspecialchars($row['lname']) . "</td>";
                echo "<td class='text-center'>" . ($row['house_name']) . "</td>";
                echo "<td>" . htmlspecialchars(implode(', ', $missed_months_dates)) . "</td>";
                echo "<td>" . htmlspecialchars($missing_months) . "</td>";
                echo "<td>" . htmlspecialchars($row['payment_amounts']) . "</td>";
                echo "<td>" . htmlspecialchars($missing_payment_total) . "</td>"; // Display the total missing payment
                echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
                    echo "<div class='row justify-content-center m-0'>";
                        echo "<div class='col-xxl-6 px-2'>";
                            echo "<form method='POST' action='admindelinquency.php' class='align-items-center'>";
                                echo "<input type='hidden' name='tenantsid' value='" . $row['tenant_id'] . "'>";
                                echo "<input type='hidden' name='missing_months' value='" . $missing_months . "'>";
                                echo "<input type='hidden' name='missed_months_dates' value='" . htmlspecialchars(implode(', ', $missed_months_dates)) . "'>";
                                echo "<button type='submit' name='send_reminder' class='btn btn-primary d-flex table-buttons-update justify-content-center' style='width: 160px;'>
                                    Send Reminder
                                </button>";
                            echo "</form>";
                        echo "</div>";
                        echo "<div class='col-xxl-6 px-2'>";
                            echo "<button class='btn btn-primary table-buttons-update' data-bs-toggle='modal' data-bs-target='#sendEvictionModal' id='send_eviction' data-id='" . $row['tenant_id'] . "' data-missedpaymenttotal='" . $missing_payment_total . "' data-misseddates='" . htmlspecialchars(implode(', ', $missed_months_dates)) . "' style='width: 160px;'><i class='fa fa-plus'></i>Send Eviction</button>";
                        echo "</div>";
                    echo "</div>";
                echo "</td>";
            echo "</tr>";
        }
    }
} else {
    echo "<tr><td colspan='8' class='text-center'>No contracts found</td></tr>";
}
// If no rows matched the condition
if (!$hasData) {
    echo "<tr><td colspan='8' class='text-center'>No tenants with missing months.</td></tr>";
}

$admin->conn->close();
echo "</tbody>";
echo "</table>";



?>
