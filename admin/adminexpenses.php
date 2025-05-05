<?php
    // session_start(); // Start the session (important for checking session variables)
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

    if(isset($_POST['add_expenses'])) {
        // Get the expenses data from the form
        $expensesname = htmlspecialchars($_POST['expensesname']);
        $infodata = htmlspecialchars($_POST['expensesinfo']);
        $expensesamount = htmlspecialchars($_POST['expensesamount']);
        $house = htmlspecialchars($_POST['house']);
        $expensesdate = htmlspecialchars($_POST['expensesdate']);

        // Validate that the expenses amount is a valid number (integer or float)
        if (filter_var($expensesamount, FILTER_VALIDATE_FLOAT) === false || !is_numeric($expensesamount)) {
            $_SESSION['error_message'] = "Should only be numerical characters";
            header("Location: adminexpenses.php");
            exit();
        } else {
            // Call the addExpenses method to add the new expenses
            $added = $admin->addExpenses($expensesname, $infodata, $expensesamount, $house, $expensesdate);
            if ($added) {
                // Expenses added successfully
                header("Location: adminexpenses.php?expenses_added=1");
                exit();
            } else {
                // Error occurred while adding expenses
                echo "Error occurred while adding Expenses.";
            }
        }
    }

    if(isset($_POST['edit_expenses'])) {
        $expensesname = htmlspecialchars($_POST['expensesname']);
        $expensesid = $_POST['expensesid'];
        $expensesinfo = htmlspecialchars($_POST['expensesinfo']);
        $expensesamount = htmlspecialchars($_POST['expensesamount']);

        if (filter_var($expensesamount, FILTER_VALIDATE_FLOAT) === false || !is_numeric($expensesamount)) {
            $_SESSION['error_message_update'] = "Should only be numerical characters";
            header("Location: adminexpenses.php");
            exit();
        } else {
            $updated = $admin->updateExpenses($expensesname, $expensesinfo, $expensesid, $expensesamount);
            if($updated) {
                header("Location: adminexpenses.php");
                exit();
            } else {
                echo "Error occurred while updating expenses.";
            }
        }    
    }

    // Check if the form is submitted for deleting an expense
    if(isset($_POST['delete_expenses'])) {
        // Get the expense ID to be deleted
        $expensesid = $_POST['expensesid'];
        // Call the deleteExpenses method to delete the expense
        $deleted = $admin->deleteExpenses($expensesid);
        if($deleted) {
            // Expense deleted successfully, you can display a success message here if needed
            header("Location: adminexpenses.php?expenses_deleted=1");
        } else {
            // Error occurred while deleting expense, display an error message or handle as needed
            echo "Error occurred while deleting expense.";
        }
    }

    // $sql = "SELECT * FROM expenses";
    $sql = "SELECT expenses.*, houses.house_name, houses.id AS housingid
            FROM expenses
            LEFT JOIN houses ON expenses.house_id = houses.id;";
    $result = $admin->conn->query($sql);

    // $sql_option = "SELECT expenses.*, houses.house_name, houses.id AS housingid
    //         FROM expenses
    //         LEFT JOIN houses ON expenses.house_id = houses.id;";
    $sql_option = "SELECT * FROM houses;";
    $result_option = $admin->conn->query($sql_option);

    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page
    $page = "adminexpenses";
?>

    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/header.php'; ?>
            <div class="col main content" style="padding-top: 12px; padding-bottom: 12px; max-height: 100vh;">
                <div class="card-body" style="margin-top: 0; height: 100%; max-height: 100%;overflow-y: auto;display: flex;flex-direction: column;">
                    <div class="row">
                        <div class="col-lg-12" id="tableheader">
                            <!-- <input type="text" id="searchBar" placeholder="Search..." class="form-control mb-3" />
                            <button class="btn btn-primary float-end table-buttons-update" id="new_expenses"><i class="fa fa-plus"></i> New Expenses</button> -->
                            <div class="row">
                                <div class="col-6">
                                    <input type="text" id="searchBar" placeholder="Search..." class="form-control mb-3 " style="max-width: 180px;" />
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-primary float-end table-buttons-update" id="new_expenses"><i class="fa fa-plus"></i> New Expenses</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive"  id="tablelimiter">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <!-- <th scope="col" onclick="handleSort('id')" style="cursor: pointer;"># <span id="sortIconId"></span></th>
                                    <th scope="col" onclick="handleSort('name')" style="cursor: pointer;">Name <span id="sortIconName"></span></th>
                                    <th scope="col" onclick="handleSort('info')" style="cursor: pointer;">Info <span id="sortIconInfo"></span></th>
                                    <th scope="col" onclick="handleSort('amount')" style="cursor: pointer;">Amount <span id="sortIconAmount"></span></th>
                                    <th scope="col" onclick="handleSort('date')" style="cursor: pointer;">Date <span id="sortIconDate"></span></th>
                                    <th scope="col">House <span id="sortIconHouse"></span></th>
                                    <th scope="col">Actions</th> -->

                                    <th scope="col" data-column="id" class="sortable-column" style="cursor: pointer;">#</th>
                                    <th scope="col" data-column="name" class="sortable-column" style="cursor: pointer;">
                                        Name
                                        <span id="nameSortArrow"></span>
                                    </th>
                                    <th scope="col" data-column="info" class="sortable-column" style="cursor: pointer;">
                                        Info
                                        <span id="infoSortArrow"></span>
                                    </th>
                                    <th scope="col">
                                        Amount
                                    </th>
                                    <th scope="col">
                                        Date
                                    </th>
                                    <th scope="col" data-column="house_name" class="sortable-column" style="cursor: pointer;">
                                        House
                                        <span id="house_nameSortArrow"></span>
                                    </th>
                                    <th scope="col">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="result">
                                <?php
                                if ($result->num_rows > 0) {
                                    // Output data of each row
                                    while($row = $result->fetch_assoc()) {
                                        $expenses[] = $row; // Collect data for the JavaScript array
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $row['id'] . "</th>";
                                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['info']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['house_name']) . "</td>";
                                        echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
                                        echo "<div class='row justify-content-center m-0'>";
                                        echo "<div class='col-xl-6 px-2'>";
                                        // Add a form with a delete button for each record
                                        echo "<form method='POST' action='adminexpenses.php' class='float-xl-end align-items-center'>";
                                        echo "<input type='hidden' name='expensesid' value='" . $row['id'] . "'>";
                                        echo "<button type='submit' name='delete_expenses' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>";
                                        echo "</form>";
                                        echo "</div>";
                                        echo "<div class='col-xl-6 px-2'>";
                                        // Add a form with a update button for each record
                                        echo "<input type='hidden' name='expensesid' value='" . $row['id'] . "'>";
                                        echo "<button type='button' class='btn btn-primary update-expenses-btn float-xl-start table-buttons-update' data-id='" . $row['id'] . "' data-expensesname='" . htmlspecialchars($row['name']) . "'data-expensesinfo='" . htmlspecialchars($row['info']) . "'data-expensesamount='" . htmlspecialchars($row['amount']) .  "' style='width: 80px;'>Update</button>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                    echo "<script>var arrayData = " . json_encode($expenses) . ";</script>"; // Pass the data to JavaScript
                                } else {
                                    echo "<tr><td colspan='4'>No Expenses found</td></tr>";
                                }
                                $admin->conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>   
                </div>
                <!-- New Expenses Modal -->
                <div class="modal fade" id="newExpensesModal" tabindex="-1" aria-labelledby="newExpensesModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header" style="background-color: #527853;">
                            <h5 class="modal-title text-white" id="newexpensesModalLabel">New Expenses</h5>
                            <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php
                                if (isset($_SESSION['error_message']) && !empty($_SESSION['error_message'])) {
                                    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                                    // Unset the error message after displaying it
                                    echo '<script>var newExpensesModal = new bootstrap.Modal(document.getElementById("newExpensesModal"), { keyboard: false });newExpensesModal.show();</script>';
                                    unset($_SESSION['error_message']);
                                }
                            ?>
                            <form id="newExpensesForm" method="POST" action="adminexpenses.php">
                            <div class="mb-3">
                                <label for="username" class="form-label">Expense Name</label>
                                <input type="text" class="form-control" id="username" name="expensesname" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Expense Info</label>
                                <input type="text" class="form-control" id="username" name="expensesinfo" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Expense Amount</label>
                                <input type="text" class="form-control" id="username" name="expensesamount" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">House</label>
                                <select class="form-select" id="role" name="house">
                                    <!-- Empty option as default -->
                                    <option value="" selected disabled hidden>Select an expense</option>
                                    <?php
                                        // Check if results exist
                                        if ($result_option->num_rows > 0) {
                                            // Output options for each expense amount
                                            while ($row_option = $result_option->fetch_assoc()) {
                                                // if($row_option['housingid'] != null || !empty($row_option['housingid']))
                                                // echo "<option value='" . $row_option['housingid'] . "'>" . $row_option['house_name'] . "</option>";
                                                echo "<option value='" . $row_option['id'] . "'>" . $row_option['house_name'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>No categories found</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Date of Expense</label>
                                <input type="date" class="form-control" id="date" name="expensesdate" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="add_expenses" class="btn btn-primary table-buttons-update">Add Expenses</button>
                            </div>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>
                <!-- Update Expenses Modal -->
                <div class="modal fade" id="updateExpensesModal" tabindex="-1" aria-labelledby="updateExpensesModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #527853;">
                                <h5 class="modal-title text-white" id="updateExpensesModalLabel">Update Expenses</h5>
                                <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <?php
                                    if (isset($_SESSION['error_message_update']) && !empty($_SESSION['error_message_update'])) {
                                        echo '<div class="alert alert-danger">' . $_SESSION['error_message_update'] . '</div>';
                                        // Unset the error message after displaying it
                                        echo '<script>var updateExpensesModal = new bootstrap.Modal(document.getElementById("updateExpensesModal"), { keyboard: false });updateExpensesModal.show();</script>';
                                        unset($_SESSION['error_message_update']);
                                    }
                                ?>
                                <form id="updateExpensesForm" method="POST" action="adminexpenses.php">
                                    <input type="hidden" id="updateExpensesId" name="expensesid">
                                    <div class="mb-3">
                                        <label for="updateExpensesname" class="form-label">Expenses Name</label>
                                        <input type="text" class="form-control" id="updateExpensesname" name="expensesname" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="updateExpensesinfo" class="form-label">Expenses Info</label>
                                        <input type="text" class="form-control" id="updateExpensesinfo" name="expensesinfo" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="updateExpensesamount" class="form-label">Expenses Amount</label>
                                        <input type="text" class="form-control" id="updateExpensesamount" name="expensesamount" required>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="edit_expenses" class="btn btn-primary table-buttons-update">Update Expenses</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.getElementById('new_expenses').addEventListener('click', function () {
                        var newExpensesModal = new bootstrap.Modal(document.getElementById('newExpensesModal'), {
                            keyboard: false
                        });
                        newExpensesModal.show();
                    });
                </script>
                <script>
                    // document.addEventListener('DOMContentLoaded', function () {
                    //     var updateButtons = document.querySelectorAll('.update-expenses-btn');
                    //     updateButtons.forEach(function (button) {
                    //         button.addEventListener('click', function () {
                    //             var userId = button.getAttribute('data-id');
                    //             var username = button.getAttribute('data-expensesname');
                    //             var userinfo = button.getAttribute('data-expensesinfo');
                    //             var amount = button.getAttribute('data-expensesamount');
                                
                    //             // Fill the modal with the user's current data
                    //             document.getElementById('updateExpensesId').value = userId;
                    //             document.getElementById('updateExpensesname').value = username;
                    //             document.getElementById('updateExpensesinfo').value = userinfo;
                    //             document.getElementById('updateExpensesamount').value = amount;
                                
                    //             var updateExpensesModal = new bootstrap.Modal(document.getElementById('updateExpensesModal'), {
                    //                 keyboard: false
                    //             });
                    //             updateExpensesModal.show();
                    //         });
                    //     });
                    // });

                    document.addEventListener('DOMContentLoaded', function () {
                        document.body.addEventListener('click', function (event) {
                            if (event.target.classList.contains('update-expenses-btn')) {
                                var button = event.target;

                                var userId = button.getAttribute('data-id');
                                var username = button.getAttribute('data-expensesname');
                                var userinfo = button.getAttribute('data-expensesinfo');
                                var amount = button.getAttribute('data-expensesamount');
                                
                                // Fill the modal with the expenses's current data
                                document.getElementById('updateExpensesId').value = userId;
                                document.getElementById('updateExpensesname').value = username;
                                document.getElementById('updateExpensesinfo').value = userinfo;
                                document.getElementById('updateExpensesamount').value = amount;

                                var updateUserModal = new bootstrap.Modal(document.getElementById('updateExpensesModal'), {
                                    keyboard: false
                                });
                                updateUserModal.show();
                            }
                        });
                    });
                </script>
                <!-- <script>
                    var sortColumnType = 'id';
                    var sortDirectionType = 'asc';

                    function renderTableType() {
                        var dataContainer = document.querySelector('tbody#result');
                        dataContainer.innerHTML = '';

                        arrayData.sort(function(a, b) {
                            var aValueType = a[sortColumnType];
                            var bValueType = b[sortColumnType];

                            if (sortColumnType === 'id' || sortColumnType === 'amount') {
                                aValueType = parseFloat(aValueType);
                                bValueType = parseFloat(bValueType);
                            } else if (sortColumnType === 'date') {
                                aValueType = new Date(aValueType);
                                bValueType = new Date(bValueType);
                            }

                            if (aValueType < bValueType) return sortDirectionType === 'asc' ? -1 : 1;
                            if (aValueType > bValueType) return sortDirectionType === 'asc' ? 1 : -1;
                            return 0;
                        });

                        arrayData.forEach(e => {
                            dataContainer.innerHTML += `
                                <tr>
                                    <th scope="row">${e.id}</th>
                                    <td>${e.name}</td>
                                    <td>${e.info}</td>
                                    <td>${e.amount}</td>
                                    <td>${e.date}</td>
                                    <td>${e.house_name ? e.house_name: 'N/A'}</td>
                                    <td class='justify-content-center text-center align-middle'>
                                        <div class='row justify-content-center m-0'>
                                            <div class='col-xl-6 px-2'>
                                                <form method='POST' action='adminexpenses.php' class='float-xl-end align-items-center'>
                                                    <input type='hidden' name='expensesid' value='${e.id}'>
                                                    <button type='submit' name='delete_expenses' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>
                                                </form>
                                            </div>
                                            <div class='col-xl-6 px-2'>
                                                <button type='button' class='btn btn-primary update-expenses-btn float-xl-start table-buttons-update' data-id='${e.id}' data-expensesname='${e.name}' data-expensesinfo='${e.info}' data-expensesamount='${e.amount}' style='width: 80px;'>Update</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });

                        updateSortIcons();
                    }

                    function handleSort(column) {
                        if (sortColumnType === column) {
                            sortDirectionType = sortDirectionType === 'asc' ? 'desc' : 'asc';
                        } else {
                            sortColumnType = column;
                            sortDirectionType = 'asc';
                        }

                        renderTableType();
                    }

                    function updateSortIcons() {
                        document.getElementById('sortIconId').textContent = '';
                        document.getElementById('sortIconName').textContent = '';
                        document.getElementById('sortIconInfo').textContent = '';
                        document.getElementById('sortIconAmount').textContent = '';
                        document.getElementById('sortIconDate').textContent = '';

                        let icon = sortDirectionType === 'asc' ? '↑' : '↓';
                        if (sortColumnType === 'id') {
                            document.getElementById('sortIconId').textContent = icon;
                        } else if (sortColumnType === 'name') {
                            document.getElementById('sortIconName').textContent = icon;
                        } else if (sortColumnType === 'info') {
                            document.getElementById('sortIconInfo').textContent = icon;
                        } else if (sortColumnType === 'amount') {
                            document.getElementById('sortIconAmount').textContent = icon;
                        } else if (sortColumnType === 'date') {
                            document.getElementById('sortIconDate').textContent = icon;
                        }
                    }

                    // Initial render
                    renderTableType();


                </script> -->
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
                    // $(document).ready(function() {
                    //     $('#searchBar').on('input', function() {
                    //         var searchQuery = $(this).val();

                    //         $.ajax({
                    //             url: 'search/search_expenses.php', // PHP script to perform search
                    //             type: 'POST',
                    //             data: { query: searchQuery },
                    //             success: function(response) {
                    //                 $('tbody').html(response); // Replace table body with new data
                    //             }
                    //         });
                    //     });
                    // });



                    $(document).ready(function() {
                        let currentSortColumn = 'id';
                        let currentSortOrder = 'ASC';

                        function fetchExpenses(page = 1, query = '', sortColumn = currentSortColumn, sortOrder = currentSortOrder) {
                            $.ajax({
                                url: 'search/search_expenses.php',
                                type: 'POST',
                                data: { 
                                    page: page, 
                                    query: query, 
                                    sort_column: sortColumn, 
                                    sort_order: sortOrder 
                                },
                                success: function(response) {
                                    $('tbody').html(response); // Update table body with data
                                }
                            });
                        }

                        // Initial fetch on page load
                        fetchExpenses();

                        // Search bar event
                        $('#searchBar').on('input', function() {
                            var searchQuery = $(this).val();
                            fetchExpenses(1, searchQuery);
                        });

                        // Pagination button event
                        $(document).on('click', '.pagination-btn', function() {
                            var page = $(this).data('page');
                            var searchQuery = $('#searchBar').val();
                            fetchExpenses(page, searchQuery);
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
                            fetchExpenses(1, searchQuery, currentSortColumn, currentSortOrder);
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
                             
                <!-- <p>Home</p> -->
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
