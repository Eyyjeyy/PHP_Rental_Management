<?php
include '../../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;

    $monthly = $_POST['month'] ?? null;
    $quarterly = $_POST['quarterly'] ?? null;
    $yearly = $_POST['yearly'] ?? null;

    if ($monthly) {
        $query = "SELECT 
            tenants.id AS tenant_id,
            tenants.*, 
            houses.*,
            eviction_popup.users_id AS eviction_users_id,
            eviction_popup.file_path,
            SUM(payments.amount) AS total_amount_payment,
            GROUP_CONCAT(payments.amount ORDER BY payments.date_payment DESC) AS payment_amounts,
            GROUP_CONCAT(payments.date_payment ORDER BY payments.date_payment DESC) AS date_payment,
            MAX(payments.date_payment) AS last_payment_date  -- Get the last payment date
        FROM tenants 
        LEFT JOIN payments ON tenants.id = payments.tenants_id 
            AND payments.approval = 'true' 
            AND MONTH(payments.date_payment) = MONTH(CURRENT_DATE) -- Filter payments for the current month
            AND YEAR(payments.date_payment) = YEAR(CURRENT_DATE)  -- Ensure it's the current year
        LEFT JOIN houses ON tenants.house_id = houses.id
        LEFT JOIN (
            SELECT 
                users_id, 
                file_path 
            FROM eviction_popup 
            WHERE id = (
                SELECT MAX(id) 
                FROM eviction_popup AS ep_sub 
                WHERE ep_sub.users_id = eviction_popup.users_id
            )
        ) AS eviction_popup ON tenants.users_id = eviction_popup.users_id
        GROUP BY tenants.id";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $tenant_id = $row['id'];
            $date_preferred = $row['date_preferred'];
            $last_payment_date = $row['last_payment_date'];
            $monthly_rent = $row['price'];

            // Use today's date for comparison
            $current_date = date('Y-m-d');
            $current_month = date('Y-m');
            
            // If no payment has been made, set the last payment date to today
            if (!$last_payment_date) {
                $last_payment_date = $current_date;
            }

            // Convert dates to timestamps
            $date_preferred_timestamp = strtotime($date_preferred);
            $current_date_timestamp = strtotime($current_date);
            $start_date_timestamp = strtotime("+1 month", $date_preferred_timestamp);

            // Initialize variables
            $missing_months = 0;
            $missed_months_dates = [];
            $missing_payment_total = 0;
            $paid_total = 0;

            // Focus only on the current month
            $payment_found = false;
            foreach (explode(',', $row['date_payment']) as $key => $payment_date) {
                if (substr($payment_date, 0, 7) == $current_month) {
                    $payment_found = true;

                    // Add the corresponding amount paid for the month
                    $amounts = explode(',', $row['payment_amounts']);
                    $monthly_paid = isset($amounts[$key]) ? (float)$amounts[$key] : 0;
                    $paid_total += $monthly_paid;
                    break;
                }
            }

            // If no payment is found for this month, calculate the missing amount
            if (!$payment_found) {
                $missing_months = 1;
                $missed_months_dates[] = $current_month;
                $missing_payment_total = $monthly_rent;
            }

            // Output the tenant information
            echo "<tr>";
            echo "<th scope='row' style='width: 100px;'>" . htmlspecialchars($row['fname']) . "</th>";
            echo "<td style='width: 150px;'>" . htmlspecialchars($row['house_name']) . "</td>";
            echo "<td style='width: 100px;'>" . $missing_months . "</td>";
            echo "<td style='width: 100px;'>" . number_format($missing_payment_total) . "</td>";
            echo "<td style='width: 100px;'>" . number_format($row['total_amount_payment']) . "</td>";
            echo "<td style='width: 100px;'>" . htmlspecialchars(implode(', ', $missed_months_dates)) . "</td>";
            echo "</tr>";
        }

    } else if ($quarterly) {
        $query = "SELECT 
            tenants.id AS tenant_id,
            tenants.*, 
            houses.*,
            eviction_popup.users_id AS eviction_users_id,
            eviction_popup.file_path,
            SUM(payments.amount) AS total_amount_payment,
            GROUP_CONCAT(payments.amount ORDER BY payments.date_payment DESC) AS payment_amounts,
            GROUP_CONCAT(payments.date_payment ORDER BY payments.date_payment DESC) AS date_payment,
            MAX(payments.date_payment) AS last_payment_date
        FROM tenants 
        LEFT JOIN payments ON tenants.id = payments.tenants_id 
            AND payments.approval = 'true' 
            AND (
                    (
                        YEAR(payments.date_payment) = YEAR(CURRENT_DATE) 
                        AND MONTH(payments.date_payment) IN (MONTH(CURRENT_DATE), MONTH(CURRENT_DATE) - 1, MONTH(CURRENT_DATE) - 2)
                    )
                    OR (
                        YEAR(payments.date_payment) = YEAR(CURRENT_DATE) - 1
                        AND MONTH(CURRENT_DATE) <= 2
                        AND MONTH(payments.date_payment) IN (12, 11)
                    )
                )
        LEFT JOIN houses ON tenants.house_id = houses.id
        LEFT JOIN (
            SELECT 
                users_id, 
                file_path 
            FROM eviction_popup 
            WHERE id = (
                SELECT MAX(id) 
                FROM eviction_popup AS ep_sub 
                WHERE ep_sub.users_id = eviction_popup.users_id
            )
        ) AS eviction_popup ON tenants.users_id = eviction_popup.users_id
        GROUP BY tenants.id";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $tenant_id = $row['id'];
            $date_preferred = $row['date_preferred'];
            $last_payment_date = $row['last_payment_date'];
            $monthly_rent = $row['price'];

            // Use today's date for comparison
            $current_date = date('Y-m-d');
            $current_month = date('Y-m');
            
            // If no payment has been made, set the last payment date to today
            if (!$last_payment_date) {
                $last_payment_date = $current_date;
            }

            // Convert dates to timestamps
            $date_preferred_timestamp = strtotime($date_preferred);
            $current_date_timestamp = strtotime($current_date);
            $start_date_timestamp = strtotime("+1 month", $date_preferred_timestamp);

            // Initialize variables
            $missing_months = 0;
            $missed_months_dates = [];
            $missing_payment_total = 0;
            $paid_total = 0;

            // Loop through the payment dates and check for payments in the last 3 months
            $payment_found = false;
            $months_to_check = [
                $current_month, 
                date('Y-m', strtotime("last month")), 
                date('Y-m', strtotime("last month -1 month"))
            ];
            
            foreach (explode(',', $row['date_payment']) as $key => $payment_date) {
                if (in_array(substr($payment_date, 0, 7), $months_to_check)) {
                    $payment_found = true;

                    // Add the corresponding amount paid for the month
                    $amounts = explode(',', $row['payment_amounts']);
                    $monthly_paid = isset($amounts[$key]) ? (float)$amounts[$key] : 0;
                    $paid_total += $monthly_paid;
                }
            }

            // If no payment is found for these months, calculate the missing amounts
            if (!$payment_found) {
                $missing_months = count($months_to_check);
                $missed_months_dates = $months_to_check;
                $missing_payment_total = $monthly_rent * $missing_months;
            }

            // Output the tenant information
            echo "<tr>";
            echo "<th scope='row' style='width: 100px;'>" . htmlspecialchars($row['fname']) . "</th>";
            echo "<td style='width: 150px;'>" . htmlspecialchars($row['house_name']) . "</td>";
            echo "<td style='width: 100px;'>" . $missing_months . "</td>";
            echo "<td style='width: 100px;'>" . number_format($missing_payment_total) . "</td>";
            echo "<td style='width: 100px;'>" . number_format($row['total_amount_payment']) . "</td>";
            echo "<td style='width: 100px;'>" . htmlspecialchars(implode(', ', $missed_months_dates)) . "</td>";
            echo "</tr>";
        }


    } else if ($yearly) {
        $query = "SELECT 
            tenants.id AS tenant_id,
            tenants.*, 
            houses.*,
            eviction_popup.users_id AS eviction_users_id,
            eviction_popup.file_path,
            SUM(payments.amount) AS total_amount_payment,
            GROUP_CONCAT(payments.amount ORDER BY payments.date_payment DESC) AS payment_amounts,
            GROUP_CONCAT(payments.date_payment ORDER BY payments.date_payment DESC) AS date_payment,
            MAX(payments.date_payment) AS last_payment_date
        FROM tenants 
        LEFT JOIN payments ON tenants.id = payments.tenants_id 
            AND payments.approval = 'true' 
            AND YEAR(payments.date_payment) = YEAR(CURRENT_DATE)  -- Filter for current year only
        LEFT JOIN houses ON tenants.house_id = houses.id
        LEFT JOIN (
            SELECT 
                users_id, 
                file_path 
            FROM eviction_popup 
            WHERE id = (
                SELECT MAX(id) 
                FROM eviction_popup AS ep_sub 
                WHERE ep_sub.users_id = eviction_popup.users_id
            )
        ) AS eviction_popup ON tenants.users_id = eviction_popup.users_id
        GROUP BY tenants.id";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $tenant_id = $row['id'];
            $date_preferred = $row['date_preferred'];
            $last_payment_date = $row['last_payment_date'];
            $monthly_rent = $row['price'];

            // Use today's date for comparison
            $current_date = date('Y-m-d');
            $current_year = date('Y'); // Current year
            $current_month = (int) date('m'); // Current month (as an integer)
            
            // If no payment has been made, set the last payment date to today
            if (!$last_payment_date) {
                $last_payment_date = $current_date;
            }

            // Convert dates to timestamps
            $date_preferred_timestamp = strtotime($date_preferred);
            $current_date_timestamp = strtotime($current_date);
            $start_date_timestamp = strtotime("+1 month", $date_preferred_timestamp);

            // Initialize variables
            $missing_months = 0;
            $missed_months_dates = [];
            $missing_payment_total = 0;
            $paid_total = 0;

            // Loop through the payment dates and check for payments in the current year
            $payment_found = false;
            
            // Get all months for the current year (from January to current month)
            $months_to_check = range(1, $current_month);  // Current month and all prior months
            $missed_months_dates = $months_to_check;

            // Check all payments in the current year
            foreach (explode(',', $row['date_payment']) as $key => $payment_date) {
                if (substr($payment_date, 0, 4) == $current_year) {  // Only consider payments in the current year
                    $payment_found = true;

                    // Add the corresponding amount paid for the month
                    $amounts = explode(',', $row['payment_amounts']);
                    $monthly_paid = isset($amounts[$key]) ? (float)$amounts[$key] : 0;
                    $paid_total += $monthly_paid;

                    // Remove the months that have already been paid for
                    $payment_month = (int) substr($payment_date, 5, 2);
                    if (($key = array_search($payment_month, $missed_months_dates)) !== false) {
                        unset($missed_months_dates[$key]);
                    }
                }
            }

            // If no payment is found for this year, calculate the missing amounts
            if (!$payment_found) {
                $missing_months = count($missed_months_dates);
                $missing_payment_total = $monthly_rent * $missing_months;
            } else {
                // Add missing payment total based on missed months
                $missing_months = count($missed_months_dates);
                $missing_payment_total = $monthly_rent * $missing_months;
            }

            // Convert missed month numbers to YYYY-MM format
            $missed_months_yyyy_mm = [];
            foreach ($missed_months_dates as $month_num) {
                $missed_months_yyyy_mm[] = $current_year . '-' . str_pad($month_num, 2, '0', STR_PAD_LEFT); // Format as YYYY-MM
            }

            // Output the tenant information
            echo "<tr>";
            echo "<th scope='row' style='width: 100px;'>" . htmlspecialchars($row['fname']) . "</th>";
            echo "<td style='width: 150px;'>" . htmlspecialchars($row['house_name']) . "</td>";
            echo "<td style='width: 100px;'>" . $missing_months . "</td>";
            echo "<td style='width: 100px;'>" . number_format($missing_payment_total) . "</td>";
            echo "<td style='width: 100px;'>" . number_format($row['total_amount_payment']) . "</td>";
            echo "<td style='width: 100px;'>" . htmlspecialchars(implode(', ', $missed_months_yyyy_mm)) . "</td>"; // Display missed months in YYYY-MM format
            echo "</tr>";
        }


    } else if ($startDate && $endDate) {
        $query = "SELECT 
            tenants.id AS tenant_id,
            tenants.*, 
            houses.*,
            eviction_popup.users_id AS eviction_users_id,
            eviction_popup.file_path,
            SUM(payments.amount) AS total_amount_payment,
            GROUP_CONCAT(payments.amount ORDER BY payments.date_payment DESC) AS payment_amounts,
            GROUP_CONCAT(payments.date_payment ORDER BY payments.date_payment DESC) AS date_payment,
            MAX(payments.date_payment) AS last_payment_date  -- Get the last payment date
        FROM tenants 
        LEFT JOIN payments ON tenants.id = payments.tenants_id AND payments.approval = 'true' -- Include only approved payments
        LEFT JOIN houses ON tenants.house_id = houses.id
        LEFT JOIN (
            SELECT 
                users_id, 
                file_path 
            FROM eviction_popup 
            WHERE id = (
                SELECT MAX(id) 
                FROM eviction_popup AS ep_sub 
                WHERE ep_sub.users_id = eviction_popup.users_id
            )
        ) AS eviction_popup ON tenants.users_id = eviction_popup.users_id
        GROUP BY tenants.id";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $tenant_id = $row['id'];
            $date_preferred = $row['date_preferred'];
            $last_payment_date = $row['last_payment_date'];
            $monthly_rent = $row['price'];

            if (!$last_payment_date) {
                $last_payment_date = date('Y-m-d');
            }

            $date_preferred_timestamp = strtotime($date_preferred);
            $current_date_timestamp = strtotime(date('Y-m-d'));
            $start_date_timestamp = strtotime("+1 month", $date_preferred_timestamp);

            $months_difference = (date('Y', $current_date_timestamp) - date('Y', $start_date_timestamp)) * 12 
                                + date('m', $current_date_timestamp) - date('m', $start_date_timestamp);

            $missing_months = 0;
            $missed_months_dates = [];
            $missing_payment_total = 0;
            $paid_total = 0;

            for ($i = 0; $i <= $months_difference; $i++) {
                $current_month = date('Y-m', strtotime("+$i months", $start_date_timestamp));
                $payment_found = false;

                foreach (explode(',', $row['date_payment']) as $key => $payment_date) {
                    if (substr($payment_date, 0, 7) == $current_month) {
                        $payment_found = true;
                        $amounts = explode(',', $row['payment_amounts']);
                        $monthly_paid = isset($amounts[$key]) ? (float)$amounts[$key] : 0;
                        $paid_total += $monthly_paid;
                        break;
                    }
                }

                if (!$payment_found) {
                    $missing_months++;
                    $missed_months_dates[] = $current_month;
                    $missing_payment_total += $monthly_rent;
                }
            }

            $missing_payment_total = max(0, $missing_payment_total);

            // Check if any missed month falls between $startDate and $endDate
            $missed_in_range = false;
            foreach ($missed_months_dates as $missed_date) {
                $missed_timestamp = strtotime($missed_date . '-01'); // Convert YYYY-MM to a date
                if ($missed_timestamp >= strtotime($startDate) && $missed_timestamp <= strtotime($endDate)) {
                    $missed_in_range = true;
                    break;
                }
            }

            // Display tenant only if they have missed months in the date range
            if ($missed_in_range) {
                echo "<tr>";
                echo "<th scope='row' style='width: 100px;'>" . $row['fname'] . "</th>";
                echo "<td style='width: 150px;'>" . htmlspecialchars($row['house_name']) . "</td>";
                echo "<td style='width: 100px;'>" . $missing_months . "</td>";
                echo "<td style='width: 100px;'>" . htmlspecialchars($missing_payment_total) . "</td>";
                echo "<td style='width: 100px;'>" . htmlspecialchars($row['total_amount_payment']) . "</td>";
                echo "<td style='width: 100px;'>" . htmlspecialchars(implode(', ', $missed_months_dates)) . "</td>";
                echo "</tr>";
            }
        }

    } else {
        $query = "SELECT 
            tenants.id AS tenant_id,
            tenants.*, 
            houses.*,
            eviction_popup.users_id AS eviction_users_id,
            eviction_popup.file_path,
            SUM(payments.amount) AS total_amount_payment,
            GROUP_CONCAT(payments.amount ORDER BY payments.date_payment DESC) AS payment_amounts,
            GROUP_CONCAT(payments.date_payment ORDER BY payments.date_payment DESC) AS date_payment,
            MAX(payments.date_payment) AS last_payment_date  -- Get the last payment date
        FROM tenants 
        LEFT JOIN payments ON tenants.id = payments.tenants_id AND payments.approval = 'true' -- Include only approved payments
        LEFT JOIN houses ON tenants.house_id = houses.id
        LEFT JOIN (
            SELECT 
                users_id, 
                file_path 
            FROM eviction_popup 
            WHERE id = (
                SELECT MAX(id) 
                FROM eviction_popup AS ep_sub 
                WHERE ep_sub.users_id = eviction_popup.users_id
            )
        ) AS eviction_popup ON tenants.users_id = eviction_popup.users_id
        GROUP BY tenants.id";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();

        while($row = $result->fetch_assoc()) {
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

            // Ensure missing_payment_total does not go below 0
            $missing_payment_total = max(0, $missing_payment_total);

            echo "<tr>";
            echo "<th scope='row' style='width: 100px;'>" . $row['fname'] . "</th>";
            echo "<td style='width: 150px;'>" . htmlspecialchars($row['house_name']) . "</td>";
            echo "<td style='width: 100px;'>" . $missing_months . "</td>";
            echo "<td style='width: 100px;'>" . htmlspecialchars($missing_payment_total) . "</td>"; // Display the total missing payment
            echo "<td style='width: 100px;'>" . htmlspecialchars($row['total_amount_payment']) . "</td>";
            // echo "<td style='width: 100px;'>" . htmlspecialchars($row['date_start']) . "</td>";
            echo "<td style='width: 100px;'>" . htmlspecialchars(implode(', ', $missed_months_dates)) . "</td>";
            echo "</td>";
            echo "</tr>";
        }
    }
}
?>