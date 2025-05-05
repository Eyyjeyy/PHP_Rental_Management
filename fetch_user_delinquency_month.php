<?php
header('Content-Type: application/json');

include 'admin.php';
$admin = new Admin();
include("db_connect.php");

$currentidd = $_SESSION['user_id']; // Assuming this is the current user ID

// SQL query to get unread message count
$query = "SELECT 
            tenants.id AS tenant_id,
            tenants.date_preferred,
            houses.price AS monthly_rent,
            GROUP_CONCAT(payments.date_payment ORDER BY payments.date_payment DESC) AS payment_dates,
            MAX(payments.date_payment) AS last_payment_date
        FROM tenants
        LEFT JOIN payments ON tenants.id = payments.tenants_id AND payments.approval = 'true'
        LEFT JOIN houses ON tenants.house_id = houses.id
        WHERE tenants.users_id = ?
        GROUP BY tenants.id";

    // Prepare and execute the query
    $stmt = $admin->conn->prepare($query);
    $stmt->bind_param("i", $admin->session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $missingMonths = 0;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $date_preferred = $row['date_preferred'];
          $last_payment_date = $row['last_payment_date'];
          $monthly_rent = $row['monthly_rent'];
          $payment_dates = $row['payment_dates'];
  
          // If no payment has been made, set the last payment date to the current date
          if (!$last_payment_date) {
            $last_payment_date = date('Y-m-d');
          }
  
          // Convert dates to timestamps for easier date manipulation
          $preferredTimestamp = strtotime($date_preferred);
          $currentTimestamp = strtotime(date('Y-m-d')); // Today's date
  
          // Offset date_preferred by 1 month
          $startDateTimestamp = strtotime("+1 month", $preferredTimestamp);
  
          // Calculate the number of months between start_date and today
          $monthsDifference = (date('Y', $currentTimestamp) - date('Y', $startDateTimestamp)) * 12 
                            + date('m', $currentTimestamp) - date('m', $startDateTimestamp);
  
          // Loop through all months from start_date to today
          for ($i = 0; $i <= $monthsDifference; $i++) {
            $currentMonth = date('Y-m', strtotime("+$i months", $startDateTimestamp));
            $paymentFound = false;
  
            // Check if the current month has any payment
            foreach (explode(',', $payment_dates) as $paymentDate) {
              if (substr($paymentDate, 0, 7) == $currentMonth) { // Check year-month format
                $paymentFound = true;
                break;
              }
            }
  
            // If no payment is found for this month, increment missing months
            if (!$paymentFound) {
              $missingMonths++;
            }
          }
        }
    }

$currentData = ['missed_months' => $missingMonths]; // Prepare the response


// Output JSON data
echo json_encode($currentData);
