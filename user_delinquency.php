<?php
    // session_start(); // Start the session (important for checking session variables)
    include 'admin.php';
    $admin = new Admin();
    // $admin->handleRedirect(); // Call handleRedirect to check login status and redirect

    // Removing this causes website to proceed to index.php despite isLoggedIn from admin.php returning false
    if(!$admin->isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    if($admin->isLoggedIn() && $admin->session_role == 'admin') {
        header("Location: admin/admindashboard.php");
        exit();
    }

    $sql = "
    SELECT 
        tenants.id AS tenant_id,
        tenants.*, 
        houses.*,
        GROUP_CONCAT(payments.amount ORDER BY payments.date_payment DESC) AS payment_amounts,
        GROUP_CONCAT(payments.date_payment ORDER BY payments.date_payment DESC) AS date_payment,
        MAX(payments.date_payment) AS last_payment_date  -- Get the last payment date
    FROM tenants 
    LEFT JOIN payments ON tenants.id = payments.tenants_id AND payments.approval = 'true' -- Include only approved payments
    LEFT JOIN houses ON tenants.house_id = houses.id
    WHERE tenants.users_id = ?
    GROUP BY tenants.id
    ";
    $stmt = $admin->conn->prepare($sql);
    $stmt->bind_param("i", $admin->session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $pageTitle = 'User Delinquency Page';
?>

<?php include 'regular/includes/header_user.php'; ?>

<div class="container-fluid" style="margin-top: 200px; margin-bottom: 130px;">
    
    <div class="row mt-5 mb-5">
        <div class="row mx-auto w-65 d-flex align-items-center m-0 p-0">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <p class="fs-5 mb-0 text-center" style="font-size: 1.2rem; font-weight: bold;">Delinquency</p>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="row">
                                <div class="col-sm-6"></div>
                                <div class="col-sm-6"></div>
                            </div>
                        </div>
                        <div class="table-container">
                            <table class="table table-striped table-bordered border border-5" id="tabletabletable">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Tenant</th>
                                        <th scope="col">House</th>
                                        <th scope="col">Months Missed</th>
                                        <th scope="col"># of Missed Payments</th>
                                        <th scope="col">Amount Paid</th>
                                        <th scope="col">Total Missing Payments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if ($result->num_rows > 0) {
                                            // // Fetch all rows as an associative array
                                            // $data = $result->fetch_all(MYSQLI_ASSOC);
                                            // // Display the array
                                            // echo "<pre>"; // For better readability in the browser
                                            // print_r($data);
                                            // echo "</pre>";
                                            
                                            while ($row = $result->fetch_assoc()) {
                                                $tenant_id = $row['id']; // Unknown ID, probably house id
                                                $date_preferred = $row['date_preferred'];
                                                $last_payment_date = $row['last_payment_date'];
                                                $monthly_rent = $row['price'];  // Use the price from the houses table as the monthly rent

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

                                                // Initialize variables
                                                $missing_months = 0;
                                                $missed_months_dates = []; // Array to store missed months
                                                $missing_payment_total = 0; // To track the total amount of missing payments
                                                $paid_total = 0; // To sum the amounts paid for missed months

                                                // Loop through all months from start_date to today
                                                for ($i = 0; $i <= $months_difference; $i++) {
                                                    $current_month = date('Y-m', strtotime("+$i months", $start_date_timestamp));
                                                    $payment_found = false;

                                                    // Check if the current month has any payment
                                                    foreach (explode(',', $row['date_payment']) as $key => $payment_date) {
                                                        if (substr($payment_date, 0, 7) == $current_month) {  // Check year-month format
                                                            $payment_found = true;

                                                            // Add the corresponding amount paid to the monthly tracker
                                                            $amounts = explode(',', $row['payment_amounts']);
                                                            $monthly_paid = isset($amounts[$key]) ? (float)$amounts[$key] : 0;
                                                            $paid_total += $monthly_paid;
                                                            break;
                                                        }
                                                    }

                                                    // If no payment is found for this month, increment missing months
                                                    if (!$payment_found) {
                                                        $missing_months++;
                                                        $missed_months_dates[] = $current_month; // Add to missed months list
                                                        
                                                        // Add the monthly rent to the total missing payment amount
                                                        $missing_payment_total += $monthly_rent; // Add the house price (monthly rent) to the total
                                                    }
                                                }

                                                // Deduct total paid from total missing payment amount
                                                // $missing_payment_total -= $paid_total;

                                                // Ensure missing_payment_total does not go below 0
                                                $missing_payment_total = max(0, $missing_payment_total);


                                                echo "<tr>";
                                                echo "<td>" . $row["tenant_id"] . "</td>"; 
                                                echo "<td>" . $row["fname"] . " " . $row["mname"] . " " . $row["lname"] . "</td>";
                                                echo "<td>" . $row["house_name"] . "</td>"; 
                                                echo "<td>" . htmlspecialchars(implode(', ', $missed_months_dates)) . "</td>";
                                                echo "<td>" . htmlspecialchars($missing_months) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['payment_amounts']) . "</td>";
                                                echo "<td>" . htmlspecialchars($missing_payment_total) . "</td>"; // Display the total missing payment
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='3'>No payments found</td></tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  function fetchUnreadMessages() {
    $.ajax({
      url: 'fetch_unread_count.php',
      method: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data && data.unread_messages !== undefined) {
          $('#unseenChatLabel').text(data.unread_messages);
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.error("Error fetching unread messages:", textStatus, errorThrown);
      }
    });
  }

  // Run once on page load
  fetchUnreadMessages();

  // Poll every 3 seconds
  setInterval(fetchUnreadMessages, 3000);
</script>

<script>
  function fetchDelinquencyMonthMissed() {
    $.ajax({
      url: 'fetch_user_delinquency_month.php',
      method: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data && data.missed_months !== undefined) {
          $('#delinquencyCount').text(data.missed_months);
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.error("Error fetching unread messages:", textStatus, errorThrown);
      }
    });
  }

  // Run once on page load
  fetchDelinquencyMonthMissed();

  // Poll every 3 seconds
  setInterval(fetchDelinquencyMonthMissed, 3000);
</script>

<?php include 'regular/includes/footer.php'; ?>