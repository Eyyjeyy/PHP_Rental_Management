<?php

use FontLib\Table\Type\head;

include '../admin.php';
    $admin = new Admin();
    // $admin->handleRedirect(); // Call handleRedirect to check login status and redirect

    // Removing this causes website to proceed to index.php despite isLoggedIn from admin.php returning false
    if(!$admin->isLoggedIn()) {
        header("Location: ../login.php");
        exit();
    }
    if($admin->isLoggedIn() && $admin->session_role == 'user') {
        header("Location: ../index.php");
        exit();
    }

    if (isset($_POST['send_reminder'])) {
        $reminder_tenant_id = trim(htmlspecialchars($_POST['tenantsid']));
        $reminder_missing_months = trim(htmlspecialchars($_POST['missing_months']));
        $reminder_missed_months_dates = htmlspecialchars($_POST['missed_months_dates']);

        $send_reminder = $admin->delinquencySendReminder($reminder_tenant_id, $reminder_missing_months, $reminder_missed_months_dates);
        if($send_reminder) {
            header("Location: admindelinquency.php?reminder_sent=1");
            exit();
        } else {
            if(empty($_SESSION['error_message'])) {
                $_SESSION['error_message'] = "Reminder Failed due to an error";
            }
            header("Location: admindelinquency.php?error=reminder");
            exit();
        }
    }

    if (isset($_POST['submit_eviction'])) {
        $evictiontenantid = trim(htmlspecialchars($_POST['evictiontenantid']));
        $missedpaymenttotal = trim(htmlspecialchars($_POST['missedpaymenttotal']));
        $misseddates = trim(htmlspecialchars($_POST['misseddates']));
        $evictiondate = trim(htmlspecialchars($_POST['evictiondate']));
        $evictionpaydays = trim(htmlspecialchars($_POST['evictionpaydays']));
        $adminaddress = trim(htmlspecialchars($_POST['adminaddress']));
        $signatureData = $_POST['signature'];
        
        // Validate that evictionpaydays is a valid positive whole number (no letters, decimals, or negatives)
        if (!preg_match("/^\d+$/", $evictionpaydays)) {
            $_SESSION['error_message'] = "Number of days for tenant to pay must be a valid positive whole number.";
            header("Location: admindelinquency.php?error=invalid_evictionpaydays");
            exit();
        }

        // Allow up to 70 characters
        if (!preg_match('/^.{1,70}$/', $adminaddress)) {
            // Address is valid, process it
            $_SESSION['error_message'] = "Address can only have up to 70 characters";
            header("Location: admindelinquency.php?error=invalid_adminaddress");
            exit();
        }

        $sendEviction = $admin->sendEviction($evictiontenantid, $missedpaymenttotal, $misseddates, $evictiondate, $evictionpaydays, $adminaddress, $signatureData);
        if($sendEviction) {
            header("Location: admindelinquency.php?eviction_sent=1");
            exit();
        } else {
            if(empty($_SESSION['error_message'])) {
                $_SESSION['error_message'] = "Addition Failed due to an error";
            }
            header("Location: admindelinquency.php?error=eviction");
            exit();
        }
    }

    if (isset($_POST['eviction_settings_save'])) {
        $update_landlordaddress_setting = (htmlspecialchars($_POST['landlordaddress_setting']));
        $update_paydays_setting = trim(htmlspecialchars($_POST['paydays_setting']));

        $saveEvictionSettings = $admin->saveEvictionSettings($admin->session_id, $update_landlordaddress_setting, $update_paydays_setting);
        if($saveEvictionSettings) {
            header("Location: admindelinquency.php?eviction_setting=1");
            exit();
        } else {
            if(empty($_SESSION['error_message'])) {
                $_SESSION['error_message'] = "Saving Setting Failed due to an error";
            }
            header("Location: admindelinquency.php?error=eviction_setting");
            exit();
        }
    }

    // Check if there's an error message stored in the session
    if (isset($_SESSION['error_message'])) {
        // Display the error message as an alert
        echo '<div class="alert alert-danger mb-0" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        // Unset the session variable to clear the error message
        unset($_SESSION['error_message']);
    }

    // $sql = "
    // SELECT 
    //     tenants.*, 
    //     houses.*,
    //     GROUP_CONCAT(payments.amount ORDER BY payments.date_payment DESC) AS payment_amounts,
    //     GROUP_CONCAT(payments.date_payment ORDER BY payments.date_payment DESC) AS date_payment
    // FROM tenants 
    // LEFT JOIN payments ON tenants.id = payments.tenants_id
    // LEFT JOIN houses ON tenants.house_id = houses.id
    // GROUP BY tenants.id
    // ";
    // $result = $admin->conn->query($sql);

    // $sql = "
    // SELECT 
    //     tenants.id AS tenant_id,
    //     tenants.*, 
    //     houses.*,
    //     GROUP_CONCAT(payments.amount ORDER BY payments.date_payment DESC) AS payment_amounts,
    //     GROUP_CONCAT(payments.date_payment ORDER BY payments.date_payment DESC) AS date_payment,
    //     MAX(payments.date_payment) AS last_payment_date  -- Get the last payment date
    // FROM tenants 
    // LEFT JOIN payments ON tenants.id = payments.tenants_id
    // LEFT JOIN houses ON tenants.house_id = houses.id
    // GROUP BY tenants.id
    // ";

    // $sql = "
    // SELECT 
    //     tenants.id AS tenant_id,
    //     tenants.*, 
    //     houses.*,
    //     eviction_popup.users_id AS eviction_users_id,
    //     eviction_popup.file_path,
    //     GROUP_CONCAT(payments.amount ORDER BY payments.date_payment DESC) AS payment_amounts,
    //     GROUP_CONCAT(payments.date_payment ORDER BY payments.date_payment DESC) AS date_payment,
    //     MAX(payments.date_payment) AS last_payment_date  -- Get the last payment date
    // FROM tenants 
    // LEFT JOIN payments ON tenants.id = payments.tenants_id AND payments.approval = 'true' -- Include only approved payments
    // LEFT JOIN houses ON tenants.house_id = houses.id
    // LEFT JOIN eviction_popup ON tenants.users_id = eviction_popup.users_id
    // GROUP BY tenants.id
    // ";
    $sql = "
    SELECT 
        tenants.id AS tenant_id,
        tenants.*, 
        houses.*,
        eviction_popup.users_id AS eviction_users_id,
        eviction_popup.file_path,
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
    GROUP BY tenants.id
    ";
    $result = $admin->conn->query($sql);



    $sqlevictionsettings = "
    SELECT * FROM eviction_setting
    ";
    $sqlevictionsettings_results = $admin->conn->query($sqlevictionsettings);

    $sqlevictionsettings_2 = "
    SELECT * FROM eviction_setting
    ";
    $sqlevictionsettings_results_2 = $admin->conn->query($sqlevictionsettings_2);


    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page
    $page = "admindelinquency";
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/header.php'; ?>
        <style>
            .wrapper {min-height:200px;border: 1px solid #000;}
            .signature-pad {position: absolute;left: 0;top: 0;width: 100%;height: 100%;}

            .popup-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1000;
            }
            .popup-content {
                background: white;
                padding: 0px;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }
            .popup-buttons {
                margin-top: 20px;
            }
            .popup-buttons button {
                margin: 0 10px;
            }
        </style>
        <div class="col main content" style="padding-top: 12px; padding-bottom: 12px;">
            <div class="card-body" style="margin-top: 0; height:100%; max-height: 100%; overflow-y: auto;">
                <div class="row">
                    <div class="col-lg-12" id="tableheader">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <input type="text" id="searchBar" placeholder="Search..." class="form-control d-inline mb-3 " style="max-width: 180px;" />
                                <a class="d-inline-block position-relative" href="" style="height: 38px; top: -1; padding-left: 10px; color: #F28543;" data-bs-toggle="modal" data-bs-target="#settingsModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-gear-fill ms-0" viewBox="0 0 16 16">
                                        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                                    </svg>
                                </a>
                            </div>
                            <!-- <div class="col-6">
                                <button class="btn btn-primary float-end table-buttons-update" id="new_contract"><i class="fa fa-plus"></i> New Contract</button>
                            </div> -->
                        </div>                            
                    </div>
                </div>
                <div class="table-responsive"  id="tablelimiter">
                    <table class="table table-striped table-bordered" id="contractTable">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" data-column="tenantname" class="sortable-column" data-column="tenantname" style="cursor: pointer;">
                                    Tenant
                                    <span class="sort-arrow" data-column="tenantname"></span>
                                </th>
                                <th scope="col" data-column="tenantapproval" class="sortable-column">
                                    House
                                    <span id="tenantapprovalSortArrow"></span>
                                </th>
                                <th scope="col" data-column="datestart" class="sortable-column">
                                    Months Missed
                                    <span id="datestartSortArrow"></span>
                                </th>
                                <th scope="col" data-column="expirationdate" class="sortable-column">
                                    # of Missed Payments
                                    <span id="expirationdateSortArrow"></span>
                                </th>
                                <th scope="col" data-column="expirationdate" class="sortable-column">
                                    Amount Paid
                                    <span id="expirationdateSortArrow"></span>
                                </th>
                                <th scope="col">Total Missing Payments</th>
                                <th scope="col">Eviction Preview</th>
                                <th scope="col">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody id="contractTableBody">
                            <?php
                            $hasData = false;
                            if ($result->num_rows > 0) {
                                // Output data of each row
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

                                    // Deduct total paid from total missing payment amount
                                    // $missing_payment_total -= $paid_total;

                                    // Ensure missing_payment_total does not go below 0
                                    $missing_payment_total = max(0, $missing_payment_total);

                                    // Only display the tenant in delinquency if his/her missing months of payments are 2 or more months
                                    if ($missing_months >= 2 ) {
                                        $hasData = true; // Mark that at least one row is displayed
                                        echo "<tr>";
                                            echo "<th scope='row'>" . $row['tenant_id'] . "</th>";
                                            echo "<td>" . htmlspecialchars($row['fname']) . " " . htmlspecialchars($row['mname']) . " " . htmlspecialchars($row['lname']) . "</td>";
                                            echo "<td class='text-center'>" . ($row['house_name']) . "</td>";
                                            echo "<td>" . htmlspecialchars(implode(', ', $missed_months_dates)) . "</td>";
                                            echo "<td>" . htmlspecialchars($missing_months) . "</td>";
                                            echo "<td><p style='max-width: 150px; word-wrap: break-word;'>" . htmlspecialchars($row['payment_amounts']) . "</p></td>";
                                            echo "<td>" . htmlspecialchars($missing_payment_total) . "</td>"; // Display the total missing payment
                                            echo "<td>";
                                                echo "<div class='row justify-content-center m-0'>";
                                                    echo "<div class='col-xxl-12 px-2'>";
                                                        echo "<img src='../asset/pdf-file.webp' id='pdfpreview' data-contid='" . "../asset/eviction_tenant/" . $row["file_path"] . "' alt='View PDF' class='view-contract-icon img-fluid' data-toggle='modal' data-target='#previewModal' style='cursor:pointer; width: 100px; height: 100px; object-fit: contain;'>";
                                                    echo "</div>";
                                                echo "</div>";
                                            echo "</td>";
                                            echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
                                                echo "<div class='row justify-content-center m-0'>";
                                                    echo "<div class='col-12 px-2'>";
                                                        // Add a form with a delete button for each record
                                                        // echo "<form method='POST' action='adminpayments.php' class='float-xxl-end align-items-center'>";
                                                        echo "<form method='POST' action='admindelinquency.php' class='align-items-center'>";
                                                            echo "<input type='hidden' name='tenantsid' value='" . $row['tenant_id'] . "'>";
                                                            echo "<input type='hidden' name='missing_months' value='" . $missing_months . "'>";
                                                            echo "<input type='hidden' name='missed_months_dates' value='" . htmlspecialchars(implode(', ', $missed_months_dates)) . "'>";
                                                            echo "<button type='submit' name='send_reminder' class='btn btn-primary d-flex table-buttons-update justify-content-center' style='width: 160px;'>
                                                                Send Reminder
                                                            </button>";
                                                        echo "</form>";
                                                    echo "</div>";
                                                    echo "<div class='col-12 px-2'>";
                                                        echo "<button class='btn btn-primary table-buttons-update' data-bs-toggle='modal' data-bs-target='#sendEvictionModal' id='send_eviction' data-id='" . $row['tenant_id'] . "' data-missedpaymenttotal='" . $missing_payment_total . "' data-misseddates='" . htmlspecialchars(implode(', ', $missed_months_dates)) . "' style='width: 160px;'><i class='fa fa-plus'></i>Send Eviction</button>";
                                                    echo "</div>";
                                                echo "</div>";
                                            echo "</td>";
                                        echo "</tr>";
                                    }

                                    // echo "<tr>";
                                    //     echo "<th scope='row'>" . $row['id'] . "</th>";
                                    //     echo "<td>" . htmlspecialchars($row['fname']) . " " . htmlspecialchars($row['mname']) . " " . htmlspecialchars($row['lname']) . "</td>";
                                    //     echo "<td class='text-center'>" . ($row['house_name']) . "</td>";
                                    //     echo "<td>" . htmlspecialchars($missing_months) . "</td>";
                                    //     echo "<td>" . htmlspecialchars($row['expirationdate']) . "</td>";
                                    //     echo "<td>" . htmlspecialchars($row['expirationdate']) . "</td>";
                                    //     echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
                                    //         echo "<div class='row justify-content-center m-0'>";
                                    //             echo "<div class='col-xl-6 px-2'>";
                                    //                 // Add a form with a delete button for each record
                                    //                 echo "<form method='POST' action='admin_contract_template.php' class='float-xl-end align-items-center' style='height:100%;'>";
                                    //                     echo "<input type='hidden' name='contractid' value='" . $row['id'] . "'>";
                                    //                     echo "<button type='submit' name='delete_contract' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>";
                                    //                 echo "</form>";
                                    //             echo "</div>";
                                    //             echo "<div class='col-xl-6 px-2'>";
                                    //                 if (!empty($row['fileurl'])) { // Ensure fileurl is not empty
                                    //                     echo "<a href='". '..' . htmlspecialchars($row['fileurl']) . "' download class='btn btn-success table-buttons-download justify-content-center table-buttons-update' style='width: 120px;'>Download</a>";
                                    //                 } else {
                                    //                     echo "<span>No file available</span>";
                                    //                 }
                                    //             echo "</div>";
                                    //         echo "</div>";
                                    //     echo "</td>";
                                    // echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No contracts found</td></tr>";
                            }
                            // If no rows matched the condition
                            if (!$hasData) {
                                echo "<tr><td colspan='8' class='text-center'>No tenants with 2 or more missed months.</td></tr>";
                            }
                            $admin->conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Send Eviction Modal -->
            <div class="modal fade" id="sendEvictionModal" tabindex="-1" aria-labelledby="sendEvictionModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #527853;">
                            <h5 class="modal-title text-white" id="sendEvictionModalLabel">Send Eviction</h5>
                            <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="sendEvictionForm" method="POST" action="admindelinquency.php">

                                <input type="hidden" id="evictiontenantid" name="evictiontenantid">
                                <input type="hidden" id="missedpaymenttotal" name="missedpaymenttotal">
                                <input type="hidden" id="misseddates" name="misseddates">
                                <input type="hidden" id="signature" name="signature">
                                
                                <div class="mb-3">
                                    <label for="evictiondate" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="evictiondate" name="evictiondate" required>
                                </div>
                                <div class="mb-3">
                                    <label for="evictionpaydays" class="form-label">Number of Days for Tenant to Pay</label>
                                    <input type="text" class="form-control" id="evictionpaydays" name="evictionpaydays" required>
                                </div>
                                <div class="mb-3">
                                    <label for="adminaddress" class="form-label">Admin Address</label>
                                    <input type="text" class="form-control" id="adminaddress" name="adminaddress" required>
                                </div>
                                <div class="mb-3 position-relative">
                                    <label for="signature-pad" class="form-label position-absolute">Admin Signature</label>
                                </div>
                                <div class="mt-3 mb-3 position-relative d-inline-block" style="min-height: 150px; flex: 1;">
                                    <div class="wrapper" style="min-height: 200px; border: 1px solid #000;">
                                        <canvas id="signature-pad" class="signature-pad" style="position: absolute; left: 0; top: 0; width: 100%; height: 100%;"></canvas>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="row justify-content-center">
                                        <div class="col-auto">
                                            <button id="clear1" class="text-white" type="button">Clear</button>
                                        </div>
                                    </div>
                                    <!-- <button id="save">Save Signature</button> -->
                                </div>
                                <div class="col-4 align-self-center mb-3">
                                    <button type="submit" id="submit_eviction" name="submit_eviction" class="btn btn-primary table-buttons-update d-block mx-auto w-100 d-none">Send Eviction</button>
                                    <button type="button" id="confirmSendEviction" class="btn btn-primary table-buttons-update eviction d-block mx-auto" 
                                    style="background-color: #527853;border-color: #527853;color: white;padding: 7.5px 10px;border-radius: 4px;">Send Eviction</button>
                                </div>
                            </form>
                            <!-- Confirmation Popup Modal -->
                            <div id="confirmationPopup" class="popup-overlay" style="display: none;">
                                <div class="popup-content">
                                    <h5 class="text-white" style="background-color: #527853; padding: 16px;">Confirm Action</h5>
                                    <p style="padding: 20px;">Are the details provided correct?</p>
                                    <div class="popup-buttons" style="margin-top: 0; margin-bottom: 16px;">
                                        <button id="confirmYes" class="btn table-buttons-update" style="background-color: #527853; color: white;">Yes</button>
                                        <button id="confirmNo" class="btn table-buttons-delete" style="background-color: #EE7214; color: white;">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal HTML -->
            <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="evictionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" style="max-width: 900px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="evictionModalLabel">Eviction PDF</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Embed the iframe with the PDF -->
                            <iframe src="../<?php echo $pdfUrl; ?>" id="evictionIframe" width="100%" height="700px"></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Eviction settings Modal -->
            <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header" style="background-color: #527853;">
                        <h5 class="modal-title text-white" id="settingsModalLabel">Eviction Setting</h5>
                        <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="evictionSettingForm" method="POST" action="admindelinquency.php">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="landlordaddress_setting" class="form-label">Default LandLord Address</label>
                                        <?php
                                            if ($sqlevictionsettings_results->num_rows > 0) {
                                                // Output data of each row
                                                while($sqlevictionsettings_row = $sqlevictionsettings_results->fetch_assoc()) {
                                                    $selected_landlord_address = $sqlevictionsettings_row['landlord_address'];
                                                    echo "<input type='text' class='form-control' id='landlordaddress_setting' name='landlordaddress_setting' value='" . (empty($selected_landlord_address) ? "Bagatua Compound" : $selected_landlord_address ) ."' required>";
                                                }
                                            } else {
                                                echo "<input type='text' class='form-control' id='landlordaddress_setting' name='landlordaddress_setting' value='Bagatua Compound' required>";
                                            }
                                        ?>
                                        <!-- <input type="text" class="form-control" id="landlordaddress_setting" name="landlordaddress_setting" required> -->
                                    </div>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="paydays_setting" class="form-label">Days to Pay</label>
                                        <?php
                                            if ($sqlevictionsettings_results_2->num_rows > 0) {
                                                // Output data of each row
                                                while($sqlevictionsettings_row_2 = $sqlevictionsettings_results_2->fetch_assoc()) {
                                                    $selected_days_to_pay = $sqlevictionsettings_row_2['days_to_pay'];
                                                    echo "<input type='number' class='form-control' id='paydays_setting' name='paydays_setting' value='" . ($selected_days_to_pay) . "' required>";
                                                }
                                            } else {
                                                echo "<input type='number' class='form-control' id='paydays_setting' name='paydays_setting' value='15' required>";
                                            }
                                        ?>
                                        <!-- <input type="number" class="form-control" id="paydays_setting" name="paydays_setting" required> -->
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="eviction_settings_save" class="btn btn-primary table-buttons-update">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
            </div>

            <script>
                document.querySelectorAll('#send_eviction').forEach(button => {
                    button.addEventListener('click', function () {
                        // Get the tenant ID from the data-id attribute of the clicked button
                        var tenantId = this.getAttribute('data-id');
                        var missedpaymenttotal = this.getAttribute('data-missedpaymenttotal');
                        var misseddates = this.getAttribute('data-misseddates');

                        // Set the tenant ID into the hidden input field in the modal
                        document.getElementById('evictiontenantid').value = tenantId;
                        document.getElementById('missedpaymenttotal').value = missedpaymenttotal;
                        document.getElementById('misseddates').value = misseddates;
                    });
                });
            </script>

            <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
            <script>
                // Initialize both signature pads
                const canvas1 = document.getElementById("signature-pad");
                const signaturePad1 = new SignaturePad(canvas1);

                function resizeCanvas(canvas, signaturePad) {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                    signaturePad.clear(); // Clear the canvas after resizing to prevent scaling artifacts
                }

                // Resize both canvases when the modal is shown and on window resize
                const sendEvictionModal = document.getElementById("sendEvictionModal");
                sendEvictionModal.addEventListener("shown.bs.modal", () => {
                    resizeCanvas(canvas1, signaturePad1);
                });

                window.addEventListener("resize", () => {
                    resizeCanvas(canvas1, signaturePad1);
                });

                // Function to center the signature in the canvas
                function centerSignature(canvas, signaturePad) {
                    const context = canvas.getContext("2d");
                    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    let minX = canvas.width, minY = canvas.height, maxX = 0, maxY = 0;

                    // Loop over every pixel to find the bounding box
                    for (let y = 0; y < canvas.height; y++) {
                        for (let x = 0; x < canvas.width; x++) {
                            const index = (y * canvas.width + x) * 4;
                            const alpha = imageData.data[index + 3];
                            if (alpha > 0) {
                                if (x < minX) minX = x;
                                if (y < minY) minY = y;
                                if (x > maxX) maxX = x;
                                if (y > maxY) maxY = y;
                            }
                        }
                    }

                    const width = maxX - minX;
                    const height = maxY - minY;

                    const centeredCanvas = document.createElement("canvas");
                    centeredCanvas.width = canvas.width;
                    centeredCanvas.height = canvas.height;
                    const centeredContext = centeredCanvas.getContext("2d");

                    centeredContext.drawImage(
                        canvas,
                        minX, minY, width, height,
                        (canvas.width - width) / 2, (canvas.height - height) / 2, width, height
                    );

                    return centeredCanvas.toDataURL("image/png");
                }

                // Clear both signature pads
                document.getElementById("clear1").addEventListener("click", () => {
                    signaturePad1.clear();
                });

                // Handle form submission
                document.getElementById("sendEvictionForm").addEventListener("submit", (event) => {
                    if (!signaturePad1.isEmpty()) {
                        document.getElementById("signature").value = centerSignature(canvas1, signaturePad1);
                    } else {
                        event.preventDefault();
                        alert("Please provide both signatures.");
                    }
                });
            </script>

            <script>
                document.getElementById('confirmSendEviction').addEventListener('click', function () {
                    // Show the custom confirmation popup
                    document.getElementById('confirmationPopup').style.display = 'flex';
                });

                document.getElementById('confirmYes').addEventListener('click', function () {
                    // User confirms action, submit the form programmatically
                    document.getElementById('submit_eviction').click();
                });

                document.getElementById('confirmNo').addEventListener('click', function () {
                    // User cancels action, hide the popup
                    document.getElementById('confirmationPopup').style.display = 'none';
                });
            </script>

            <script>
                document.body.addEventListener('click', function (event) {
                    if (event.target && event.target.id === 'pdfpreview') {
                        const testcontract_id = event.target.getAttribute("data-contid");

                        // Get the iframe element
                        var iframe = document.getElementById('evictionIframe');

                        // Update the iframe's src with the testcontract_id
                        iframe.src = `${testcontract_id}`; // Adjust the URL as needed

                        var contractModal = new bootstrap.Modal(document.getElementById('previewModal'), {
                            keyboard: false
                        });
                        contractModal.show();
                    }
                });
            </script>

            <!-- Include jQuery library -->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script>
                function fetchUnreadMessages() {
                    $.ajax({
                    url: '../fetch_unread_count.php',
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
                // Function to create and set the favicon
                function setFavicon(iconURL) {
                // Create a new link element
                const favicon = document.createElement('link');
                favicon.rel = 'icon';
                favicon.type = 'image/x-icon';
                favicon.href = iconURL;

                // Remove any existing favicons
                const existingIcons = document.querySelectorAll('link[rel="icon"]');
                existingIcons.forEach(icon => icon.remove());

                // Append the new favicon to the head
                document.head.appendChild(favicon);
                }

                // Example usage: set the favicon on page load
                document.addEventListener('DOMContentLoaded', () => {
                setFavicon('../asset/Renttrack pro logo.png'); // Change to your favicon path
                });
            </script>
        </div>
    </div>
</div>