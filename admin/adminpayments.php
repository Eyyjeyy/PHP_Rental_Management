<?php

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

    if(isset($_POST['approve_payment'])) {
        // Get the payment ID to be approved
        $paymentsid = $_POST['paymentsid'];

        $approved = $admin->approvePayment($paymentsid);
        if($approved) {

            header("Location: adminpayments.php?payment=approved");
        } else {

            echo "Error occurred while approving a payment.";
        }
    }

    if(isset($_POST['decline_payment'])) {
        // Get the payment ID to be approved
        $paymentsid = $_POST['paymentsid'];

        $declined = $admin->declinePayment($paymentsid);
        if($declined) {
            header("Location: adminpayments.php?payment=declined");
        } else {
            echo "Error occurred while approving a payment.";
        }
    }

    if(isset($_POST['approve_deposit'])) {
        $depositid = $_POST['paymentsid'];
        $approveDeposit = $admin->approveDeposit($depositid);

        if($approveDeposit) {
            header("Location: adminpayments.php?depsoit=approved");
            exit();
        } else {
            if(empty($_SESSION['error_message'])) {
                $_SESSION['error_message'] = "Error occurred while approving a deposit.";
            }
            header("Location: adminpayments.php?error=approved");
            exit();
        }
    }

    if(isset($_POST['decline_deposit'])) {
        $depositid = $_POST['paymentsid'];
        $declineDeposit = $admin->declineDeposit($depositid);

        if($declineDeposit) {
            header("Location: adminpayments.php?depsoit=declined");
            exit();
        } else {
            if(empty($_SESSION['error_message'])) {
                $_SESSION['error_message'] = "Error occurred while declining a deposit.";
            }
            header("Location: adminpayments.php?error=declined");
            exit();
        }
    }

    if(isset($_POST['update_deposits_table'])) {
        $houseid = $_POST['houseid'];
        $payment_id = $_POST['payment_id'];
        $updatestatus = $_POST['updatestatus'];
        $reason = $_POST['reason'];

        $update = $admin->updateDeposit($houseid, $payment_id, $updatestatus, $reason);
        if($update) {
            header("Location: adminpayments.php?deposit=update");
            exit();
        } else {
            if(empty($_SESSION['error_message'])) {
                $_SESSION['error_message'] = "Error occurred while updating a deposit.";
            }
            header("Location: adminpayments.php?error=update");
            exit();
        }
    }

    if (isset($_SESSION['error_message'])) {
        // Display the error message as an alert
        echo '<div class="alert alert-danger mb-0" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        // Unset the session variable to clear the error message
        unset($_SESSION['error_message']);
    }

    // Get sort column and direction from query parameters
    $sortColumn = isset($_GET['column']) ? $_GET['column'] : 'date_payment';
    $sortDirection = isset($_GET['direction']) && $_GET['direction'] === 'desc' ? 'DESC' : 'ASC';

    // Ensure the sort column is one of the allowed columns to prevent SQL injection
    $allowedColumns = ['id', 'name', 'amount', 'date_payment'];
    if (!in_array($sortColumn, $allowedColumns)) {
        $sortColumn = 'date_payment';
    }

    // Determine the next sort direction
    $nextSortDirection = $sortDirection === 'ASC' ? 'desc' : 'asc';

    // Determine the arrow symbol based on the current sort direction
    $arrow = $sortDirection === 'ASC' ? '↑' : '↓';

    $sql = "SELECT * FROM payments ORDER BY $sortColumn $sortDirection";
    $result = $admin->conn->query($sql);

    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page
    $page = "adminpayments";
?>


    <div class="container-fluid">
        <div class="row">
        <?php include 'includes/header.php'; ?>
            <div class="col main content" style="padding-top: 12px; padding-bottom: 12px; max-height: 100vh;">
                <div class="card-body" style="margin-top: 0; height: 100%;">
                    <div class="row">
                        <div class="col-lg-12">
                            <!-- <input type="text" id="searchBar" placeholder="Search..." class="form-control mb-3" /> -->
                            <!-- <button class="btn btn-primary float-end" id="new_category"><i class="fa fa-plus"></i> New Category</button> -->
                            <div class="row">
                                <div class="col-6">
                                    <input type="text" id="searchBar" placeholder="Search..." class="form-control mb-3 " style="max-width: 180px;" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive"  id="tablelimiter" style="max-height: 100%;">
                        <table class="table table-striped table-bordered">
                        <thead>
                            <!-- <tr>
                                <th scope="col">
                                    <a href="javascript:void(0);" onclick="sortTable(0, 'id')" class="text-decoration-none d-inline-block" style="color: #212529;">
                                        # <span id="idSortArrow">↑</span>
                                    </a>
                                </th>
                                <th scope="col">
                                    <a href="javascript:void(0);" onclick="sortTable(1, 'name')" class="text-decoration-none d-inline-block" style="color: #212529;">
                                        Tenant <span id="nameSortArrow"></span>
                                    </a>
                                </th>
                                <th scope="col">
                                    <a href="javascript:void(0);" onclick="sortTable(2, 'amount')" class="text-decoration-none d-inline-block" style="color: #212529;">
                                        Amount <span id="amountSortArrow"></span>
                                    </a>
                                </th>
                                <th scope="col">Receipt</th>
                                <th scope="col">
                                    <a href="javascript:void(0);" onclick="sortTable(4, 'date_payment')" class="text-decoration-none d-inline-block" style="color: #212529;">
                                        Date <span id="datePaymentSortArrow"></span>
                                    </a>
                                </th>
                                <th scope="col">Actions</th>
                            </tr> -->
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" data-column="name" class="sortable-column">
                                    Tenant
                                    <span id="fnameSortArrow"></span>
                                </th>
                                <th scope="col" data-column="payment_type" class="sortable-column">
                                    Payment Type
                                    <span id="payment_typeSortArrow"></span>
                                </th>
                                <th scope="col" data-column="amount" class="sortable-column">
                                    Amount
                                    <span id="mnameSortArrow"></span>
                                </th>
                                <th scope="col">Receipt</th>
                                <th scope="col" data-column="date_payment" class="sortable-column">
                                    Date
                                    <span id="lnameSortArrow"></span>
                                </th>
                                <th scope="col">Status</th>
                                <th scope="col">Reason</th>
                                <!-- <th scope="col" data-column="users_username" class="sortable-column">
                                    Username
                                    <span id="usernameSortArrow"></span>
                                </th> -->
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                            <tbody id="paymentTableBody">
                                <?php
                                if ($result->num_rows > 0) {
                                    // Output data of each row
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $row['id'] . "</th>";
                                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                        echo "<td>" . "as" . "</td>";
                                        echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
                                        // echo "<td><img src='" . $row["filepath"] . "' alt='Receipt' class='img-fluid' style='max-width: 100px; height: auto;'></td>";



                                        

                                        // echo "
                                        // <td>
                                        //     <a href='#' data-bs-toggle='modal' data-bs-target='#imageModal" . $row["id"] . "'>
                                        //         <img src='" . $row["filepath"] . "' alt='Receipt' class='img-fluid' style='max-width: 100px; height: auto;'>
                                        //     </a>
                                        // </td>
                                        // <div class='modal fade' id='imageModal" . $row["id"] . "' tabindex='-1' aria-labelledby='imageModalLabel" . $row["id"] . "' aria-hidden='true'>
                                        //     <div class='modal-dialog modal-dialog-centered'>
                                        //         <div class='modal-content'>
                                        //             <div class='modal-header' style='background-color: #527853;'>
                                        //                 <h5 class='modal-title text-white' id='imageModalLabel" . $row["id"] . "'>Receipt Preview</h5>
                                        //                 <button type='button' class='btn-svg p-0' data-bs-dismiss='modal' aria-label='Close' style='width: 24px; height: 24px;'>
                                        //                     <svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' class='bi bi-x-lg w-100' viewBox='0 0 16 16'>
                                        //                         <path d='M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z'/>
                                        //                     </svg>
                                        //                 </button>
                                        //             </div>
                                        //             <div class='modal-body'>
                                        //                 <img src='" . $row["filepath"] . "' alt='Receipt' class='img-fluid'>
                                        //             </div>
                                        //         </div>
                                        //     </div>
                                        // </div>";



                                        echo "<td>" . htmlspecialchars($row['date_payment']) . "</td>";
                                        echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
                                        echo "<div class='row justify-content-center m-0'>";
                                        echo "<div class='col-xxl-6 px-2'>";
                                        // Add a form with a delete button for each record
                                        echo "<form method='POST' action='adminpayments.php' class='float-xxl-end align-items-center'>";
                                        echo "<input type='hidden' name='paymentsid' value='" . $row['id'] . "'>";
                                        $approval = $row['approval'] === 'true' ? 'disabled' : '';
                                        echo "<button type='submit' name='approve_payment' class='btn btn-primary d-flex table-buttons-update' style='width: 120px;' $approval>
                                        <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='currentColor' class='bi bi-check align-self-center' viewBox='0 0 16 16'>
                                            <path d='M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z'/>
                                        </svg>
                                        Approve
                                        </button>";
                                        echo "</form>";
                                        echo "</div>";
                                        echo "<div class='col-xxl-6 d-flex justify-content-center justify-content-xxl-start px-2'>";
                                        // Add a form with a update button for each record
                                        echo "<form method='POST' action='adminpayments.php' class='align-items-center'>";
                                        echo "<input type='hidden' name='paymentsid' value='" . $row['id'] . "'>";
                                        $decline = $row['approval'] === 'false' ? 'disabled' : '';
                                        echo "<button type='submit' name='decline_payment' class='btn btn-danger update-category-btn float-xxl-start d-flex table-buttons-delete' data-id='" . $row['id'] . "' data-paymentname='" . htmlspecialchars($row['name']) . "' style='width: 120px;' $decline>
                                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-ban-fill align-self-center me-2' viewBox='0 0 16 16'>
                                            <path d='M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M2.71 12.584q.328.378.706.707l9.875-9.875a7 7 0 0 0-.707-.707l-9.875 9.875Z'/>
                                        </svg>
                                        Decline
                                        </button>";
                                        echo "</form>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No categories found</td></tr>";
                                }
                                $admin->conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Update Deposit Modal -->
                <div class="modal fade" id="newCategoryModal" tabindex="-1" aria-labelledby="newCategoryModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header" style="background-color: #527853;">
                            <h5 class="modal-title text-white" id="newcategoryModalLabel">Update Deposit</h5>
                            <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="newCategoryForm" method="POST" action="adminpayments.php">
                            <div class="mb-3">
                                <label for="houseid" class="form-label">House ID</label>
                                <input type="text" class="form-control" id="houseid" name="houseid" required readonly>
                            </div>
                            <div class="mb-3">
                                <label for="users_name" class="form-label">Name</label>
                                <select class="form-control" id="users_name" name="payment_id" required readonly>
                                    <!-- Options will be inserted here dynamically -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="updatestatus">Status</label>
                                <select class="form-control" id="updatestatus" name="updatestatus" required>
                                    <?php 
                                        $option1 = "Approved";
                                        $option2 = "Unapproved";
                                        $option3 = "1 Month Consumed";
                                        $option4 = "2 Months Consumed";
                                    ?>
                                    <!-- <option value="<?php echo $option1; ?>" id="updatestatusOption"><?php echo $option1; ?></option>
                                    <option value="<?php echo $option2; ?>" id="updatestatusOption"><?php echo $option2; ?></option> -->
                                    <option value="<?php echo $option3; ?>" id="updatestatusOption"><?php echo $option3; ?></option>
                                    <option value="<?php echo $option4; ?>" id="updatestatusOption"><?php echo $option4; ?></option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="reason">Reason</label>
                                <select class="form-control" id="reason" name="reason" required>
                                    <option value="Emergency" id="reasonOption">Emergency</option>
                                    <option value="Bills" id="reasonOption">Bills</option>
                                    <option value="Rent" id="reasonOption">Rent</option>
                                    <option value="Withdrawn" id="reasonOption">Withdrawn</option>
                                </select>
                                <!-- <input type="text" class="form-control" id="reason" name="reason" required> -->
                            </div>
                            <button type="submit" name="update_deposits_table" class="btn btn-primary table-buttons-update">Update</button>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>
                <script>
                    // document.getElementById('new_category').addEventListener('click', function () {
                    //     var newCategoryModal = new bootstrap.Modal(document.getElementById('newCategoryModal'), {
                    //         keyboard: false
                    //     });
                    //     newCategoryModal.show();
                    // });
                </script>
                <script>
                    document.addEventListener('click', function(e) {
                        if (e.target && e.target.id === 'update_deposit') {
                            // Your modal opening logic here
                            // Directly trigger the modal here

                            // Retrieve the record's ID from the data-id attribute
                            var recordId = e.target.getAttribute('data-id');
                            var newCategoryModal = new bootstrap.Modal(document.getElementById('newCategoryModal'), {
                                keyboard: false
                            });
                            newCategoryModal.show();

                            console.log("Button clicked");
                            // Log the record ID (you can use this to make further actions or populate the modal)
                            console.log("Button clicked for record ID: " + recordId);
                            
                            // // Find the select element and set its value dynamically
                            // var select = document.getElementById('categorySelect');
                            // var option = document.createElement('option'); // Create a new option element
                            // option.value = recordId; // Set the value to the payment ID
                            // option.text = recordId; // Set the display text to the payment ID
                            // select.innerHTML = ''; // Clear any existing options
                            // select.appendChild(option); // Append the new option
                            
                            // Use fetch to send the recordId to the server
                            fetch('fetchdeposit/deposit.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: 'recordId=' + encodeURIComponent(recordId)  // Send recordId to PHP
                            })
                            .then(response => response.json())  // Parse the JSON response from PHP
                            .then(data => {
                                // Handle the data received from the server
                                console.log(data);  // This is the server's response

                                // Example: Populate the select element with the record ID
                                var houseidField = document.getElementById('houseid');
                                var select = document.getElementById('users_name');
                                var option = document.createElement('option');
                                option.id = 'optiontest';
                                // option.value = data.id; // Set the value to the payment ID
                                option.text = data.id; // Set the display text to the payment ID
                                select.innerHTML = '';  // Clear any existing options
                                select.appendChild(option);  // Append the new option

                                // Optionally, update other parts of the modal with fetched data
                                // Populate modal field's values/text using the column name from sql query. Refer to deposit.php
                                houseidField.value = data.houses_id;
                                document.getElementById('optiontest').value = data.id;
                                document.getElementById('optiontest').text = data.firstname + ' ' + data.middlename + ' ' + data.lastname;
                            })
                            .catch(error => {
                                console.error('Error:', error);  // Handle any errors that occur
                            });
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
                    $(document).ready(function() {
                        let currentSortColumn = 'id';
                        let currentSortOrder = 'ASC';

                        function fetchUsers(page = 1, query = '', sortColumn = currentSortColumn, sortOrder = currentSortOrder) {
                            $.ajax({
                                url: 'search/search_payments.php',
                                type: 'POST',
                                data: { 
                                    page: page, 
                                    query: query, 
                                    sort_column: sortColumn, 
                                    sort_order: sortOrder 
                                },
                                success: function(response) {
                                    $('tbody#paymentTableBody').html(response); // Update table body with data
                                }
                            });
                        }

                        // Initial fetch on page load
                        fetchUsers();

                        // Search bar event
                        $('#searchBar').on('input', function() {
                            var searchQuery = $(this).val();
                            fetchUsers(1, searchQuery);
                        });

                        // Pagination button event
                        $(document).on('click', '.pagination-btn', function() {
                            var page = $(this).data('page');
                            var searchQuery = $('#searchBar').val();
                            fetchUsers(page, searchQuery);
                        });

                        // Column header sorting event
                        $('.sortable-column').on('click', function() {
                            let column = $(this).data('column');
                            currentSortOrder = (currentSortColumn === column && currentSortOrder === 'ASC') ? 'DESC' : 'ASC';
                            currentSortColumn = column;

                            // Toggle the arrow indicator directly in the column header
                            $('.sortable-column').each(function() {
                                // Check if the column header contains an arrow (↑ or ↓) and remove it
                                let text = $(this).text().trim();
                                if (text.endsWith('↑') || text.endsWith('↓')) {
                                    $(this).text(text.slice(0, -2));  // Remove the last two characters (arrow)
                                }
                            });

                            // Add the appropriate arrow to the clicked column header
                            let arrow = currentSortOrder === 'ASC' ? ' ↑' : ' ↓';
                            $(this).append(arrow);  // Append the arrow directly to the text

                            let searchQuery = $('#searchBar').val();
                            fetchUsers(1, searchQuery, currentSortColumn, currentSortOrder);
                        });
                    });
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