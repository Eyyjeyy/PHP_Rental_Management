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
    
    // Check if the form is submitted for adding a new user
    if(isset($_POST['add_user'])) {
        // Get the user data from the form
        $username = trim(htmlspecialchars($_POST['username']));
        $firstname = trim(htmlspecialchars($_POST['firstname']));
        $middlename = trim(htmlspecialchars($_POST['middlename']));
        $lastname = trim(htmlspecialchars($_POST['lastname']));
        $password = htmlspecialchars($_POST['password']);
        $role = htmlspecialchars($_POST['role']);

        if(!preg_match("/^[a-zA-Z ]*$/", $firstname)) {
            $_SESSION['error_message'] = "Firstname should only have letters and spaces.";
            header("Location: adminusers.php?error=add");
            exit();
        }

        // Call the addUser method to add the new user
        $added = $admin->addUser($username, $firstname, $middlename, $lastname, $password, $role);
        if($added) {
            // User added successfully, you can display a success message here if needed
            // echo "User added successfully.";
            header("Location: adminusers.php?user_added=1");
            exit();
        } else {
            // Error occurred while adding user, display an error message or handle as needed
            // echo "Error occurred while adding user.";

            $_SESSION['error_message'] = "Addition Failed due to an error";
            header("Location: adminusers.php?error=add");
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

    if(isset($_POST['edit_user'])) {
        $username = htmlspecialchars($_POST['username']);
        $firstname = htmlspecialchars($_POST['firstname']);
        $middlename = htmlspecialchars($_POST['middlename']);
        $lastname = htmlspecialchars($_POST['lastname']);
        $password = htmlspecialchars($_POST['password']);
        $role = htmlspecialchars($_POST['role']);
        $user_id = $_POST['user_id'];
        $updateNumber = $_POST['updateNumber'];
        $updateEmail = $_POST['updateEmail'];

        // Validate updateNumber for 9-11 digits
        if (!preg_match('/^\d{9,11}$/', $updateNumber)) {
            $_SESSION['error_message'] = "9 and 11 digits only";
            header("Location: adminusers.php?error=add");
            exit();
        }

        $updated = $admin->updateUser($user_id, $username, $firstname, $middlename, $lastname, $password, $role, $updateNumber, $updateEmail);
        if($updated) {
            header("Location: adminusers.php");
            exit();
        } else {
            echo "Error occurred while updating user.";
        }
    }

    // Check if the form is submitted for deleting a user
    if(isset($_POST['delete_user'])) {
        // Get the user ID to be deleted
        $user_id = $_POST['user_id'];
        // Call the deleteUser method to delete the user
        $deleted = $admin->deleteUser($user_id);
        if($deleted) {
            // User deleted successfully, you can display a success message here if needed
            header("Location: adminusers.php?user_deleted=1");
        } else {
            // Error occurred while deleting user, display an error message or handle as needed
            echo "Error occurred while deleting user.";
        }
    }
    
    // Get sort column and direction from query parameters
    $sortColumn = isset($_GET['column']) ? $_GET['column'] : 'id';
    $sortDirection = isset($_GET['direction']) && $_GET['direction'] === 'desc' ? 'DESC' : 'ASC';

    // Ensure the sort column is one of the allowed columns to prevent SQL injection
    $allowedColumns = ['id', 'username', 'firstname', 'middlename', 'lastname', 'password', 'role'];
    if (!in_array($sortColumn, $allowedColumns)) {
        $sortColumn = 'id';
    }

    // Determine the next sort direction
    $nextSortDirection = $sortDirection === 'ASC' ? 'desc' : 'asc';

    // Determine the arrow symbol based on the current sort direction
    $arrow = $sortDirection === 'ASC' ? '↑' : '↓';

    // Retrieve users data from the database
    $sql = "SELECT * FROM users ORDER BY $sortColumn $sortDirection";
    $result = $admin->conn->query($sql);

    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page
    $page = "adminuser";
?>

    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/header.php'; ?>
            <div class="col main content" style="padding-top: 12px; padding-bottom: 12px; max-height: 100vh;">
                <div class="card-body"  id="userbody" style="margin-top: 0; height: 100%; max-height: 100%;overflow-y: auto;display: flex;flex-direction: column;">
                    <div class="row">
                        <div class="col-lg-12" id="tableheader">
                            <div class="row">
                                <div class="col-6">
                                    <input type="text" id="searchBar" placeholder="Search..." class="form-control mb-3 " style="max-width: 180px;" />
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-primary float-end table-buttons-update" id="new_user"><i class="fa fa-plus"></i> New User</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" id="tablelimiter" style="max-height: 100%;">
                        <table class="table table-striped table-bordered" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th scope="col" class="sortable-column" data-column="id">ID</th>
                                    <th scope="col" class="sortable-column" data-column="username">Username</th>
                                    <th scope="col" class="sortable-column" data-column="firstname">First Name</th>
                                    <th scope="col" class="sortable-column" data-column="middlename">Middle Name</th>
                                    <th scope="col" class="sortable-column" data-column="lastname">Last Name</th>
                                    <th scope="col" class="sortable-column" data-column="phonenumber">Phone Number</th>
                                    <th scope="col" class="sortable-column" data-column="email">Email</th>
                                    <th scope="col" class="sortable-column" data-column="password">Password</th>
                                    <th scope="col" class="sortable-column" data-column="role">Role</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    // Output data of each row
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $row['id'] . "</th>";
                                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['middlename']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['phonenumber'] ? $row['phonenumber'] : 'N/A') . "</td>";
                                        echo "<td>" . htmlspecialchars($row['email'] ? $row['email'] : 'N/A') . "</td>";
                                        echo "<td>" . htmlspecialchars($row['password'] ? str_repeat('*', strlen($row['password'])) : 'N/A') . "</td>";
                                        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                                        echo "<td class='justify-content-center text-center align-middle'>";
                                        echo "<div class='row justify-content-center m-0'>";
                                        echo "<div class='col-xl-6 px-2'>";
                                        // Add a form with a delete button for each record
                                        echo "<form method='POST' action='adminusers.php' class='float-xl-end align-items-center'>";
                                        echo "<input type='hidden' name='user_id' value='" . $row['id'] . "'>";
                                        echo "<button type='submit' name='delete_user' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>";
                                        echo "</form>";
                                        echo "</div>";
                                        echo "<div class='col-xl-6 px-2'>";
                                        // Add a form with a update button for each record
                                        echo "<input type='hidden' name='user_id' value='" . $row['id'] . "'>";
                                        echo "<button type='button' class='btn btn-primary update-user-btn float-xl-start table-buttons-update' data-id='" . $row['id'] . "' data-username='" . htmlspecialchars($row['username']) . "' data-firstname= '" . htmlspecialchars($row['firstname']) . "' data-middlename= '" . htmlspecialchars($row['middlename']) . "' data-lastname= '" . htmlspecialchars($row['lastname']) . "' data-password='" . htmlspecialchars($row['password']) . "' data-role='" . htmlspecialchars($row['role']) . "'data-email='" . htmlspecialchars($row['email']) . "'data-number='" . htmlspecialchars($row['phonenumber']) . "' style='width: 80px;'>Update</button>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No users found</td></tr>";
                                }
                                $admin->conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- New User Modal -->
                <div class="modal fade" id="newUserModal" tabindex="-1" aria-labelledby="newUserModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header" style="background-color: #527853;">
                            <h5 class="modal-title text-white" id="newUserModalLabel">New User</h5>
                            <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="newUserForm" method="POST" action="adminusers.php">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="firstname" class="form-label">Firstname</label>
                                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="middlename" class="form-label">Middlename</label>
                                            <input type="text" class="form-control" id="middlename" name="middlename" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="lastname" class="form-label">Lastname</label>
                                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role</label>
                                            <select class="form-select" id="role" name="role" required>
                                            <option value="admin">Admin</option>
                                            <option value="user">User</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="add_user" class="btn btn-primary table-buttons-update">Add User</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>
                <!-- Update User Modal -->
                <div class="modal fade" id="updateUserModal" tabindex="-1" aria-labelledby="updateUserModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #527853;">
                                <h5 class="modal-title text-white" id="updateUserModalLabel">Update User</h5>
                                <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="updateUserForm" method="POST" action="adminusers.php">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="hidden" id="updateUserId" name="user_id">
                                            <div class="mb-3">
                                                <label for="updateUsername" class="form-label">Username</label>
                                                <input type="text" class="form-control" id="updateUsername" name="username" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="updateFirstname" class="form-label">Firstname</label>
                                                <input type="text" class="form-control" id="updateFirstname" name="firstname" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="updateMiddlename" class="form-label">Middlename</label>
                                                <input type="text" class="form-control" id="updateMiddlename" name="middlename" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="updateLastname" class="form-label">Lastname</label>
                                                <input type="text" class="form-control" id="updateLastname" name="lastname" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="updatePassword" class="form-label">Password</label>
                                                <!-- <input type="password" class="form-control" id="updatePassword" name="password" required> -->
                                                <!-- <button type="button" id="togglePassword">Reveal</button> -->
                                                <div style="position: relative;">
                                                    <input type="password" class="form-control" id="updatePassword" name="password" required>
                                                    <button type="button" id="togglePassword" class="p-0" style="position: absolute; right: 10; top: 11.1; border: none; background: white; cursor: pointer;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"></path>
                                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="updateRole" class="form-label">Role</label>
                                                <select class="form-select" id="updateRole" name="role" required>
                                                    <option value="admin">Admin</option>
                                                    <option value="user">User</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="updateEmail" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="updateEmail" name="updateEmail" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="updateNumber" class="form-label">Phone number</label>
                                                <input type="text" class="form-control" id="updateNumber" name="updateNumber" required>
                                            </div>
                                        </div>
                                        <script>
                                            document.getElementById('updateNumber').addEventListener('input', function (e) {
                                                // Remove non-numeric characters
                                                this.value = this.value.replace(/[^0-9]/g, '');
                                            });
                                        </script>
                                        <div class="col-12">
                                            <button type="submit" name="edit_user" class="btn btn-primary table-buttons-update">Update User</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.getElementById('new_user').addEventListener('click', function () {
                        var newUserModal = new bootstrap.Modal(document.getElementById('newUserModal'), {
                            keyboard: false
                        });
                        newUserModal.show();
                    });
                </script>
                <script>
                    // document.addEventListener('DOMContentLoaded', function () {
                    //     var updateButtons = document.querySelectorAll('.update-user-btn');
                    //     updateButtons.forEach(function (button) {
                    //         button.addEventListener('click', function () {
                    //             var userId = button.getAttribute('data-id');
                    //             var username = button.getAttribute('data-username');
                    //             var firstname = button.getAttribute('data-firstname');
                    //             var middlename = button.getAttribute('data-middlename');
                    //             var lastname = button.getAttribute('data-lastname');
                    //             var password = button.getAttribute('data-password');
                    //             var role = button.getAttribute('data-role');
                                
                    //             // Fill the modal with the user's current data
                    //             document.getElementById('updateUserId').value = userId;
                    //             document.getElementById('updateUsername').value = username;
                    //             document.getElementById('updateFirstname').value = firstname;
                    //             document.getElementById('updateMiddlename').value = middlename;
                    //             document.getElementById('updateLastname').value = lastname;
                    //             document.getElementById('updatePassword').value = password;
                    //             document.getElementById('updateRole').value = role;
                                
                    //             var updateUserModal = new bootstrap.Modal(document.getElementById('updateUserModal'), {
                    //                 keyboard: false
                    //             });
                    //             updateUserModal.show();
                    //         });
                    //     });
                    // });

                    document.addEventListener('DOMContentLoaded', function () {
                        document.body.addEventListener('click', function (event) {
                            if (event.target.classList.contains('update-user-btn')) {
                                var button = event.target;

                                var userId = button.getAttribute('data-id');
                                var username = button.getAttribute('data-username');
                                var firstname = button.getAttribute('data-firstname');
                                var middlename = button.getAttribute('data-middlename');
                                var lastname = button.getAttribute('data-lastname');
                                var password = button.getAttribute('data-password');
                                var role = button.getAttribute('data-role');
                                var email = button.getAttribute('data-email');
                                var number = button.getAttribute('data-number');

                                // Fill the modal with the user's current data
                                document.getElementById('updateUserId').value = userId;
                                document.getElementById('updateUsername').value = username;
                                document.getElementById('updateFirstname').value = firstname;
                                document.getElementById('updateMiddlename').value = middlename;
                                document.getElementById('updateLastname').value = lastname;
                                document.getElementById('updatePassword').value = password;
                                document.getElementById('updateRole').value = role;
                                document.getElementById('updateEmail').value = email;
                                document.getElementById('updateNumber').value = number;

                                var updateUserModal = new bootstrap.Modal(document.getElementById('updateUserModal'), {
                                    keyboard: false
                                });
                                updateUserModal.show();
                            }
                        });
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
                                url: 'search/search_users.php',
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
                    function togglePassword(inputId, show) {
                        const input = document.getElementById(inputId);
                        input.type = show ? 'text' : 'password';
                        console.log(inputId);
                    }
                </script>
                <script>
                    const passwordInput = document.getElementById('updatePassword');
                    const toggleButton = document.getElementById('togglePassword');

                    toggleButton.addEventListener('mousedown', () => {
                        passwordInput.type = 'text'; // Reveal the password
                    });

                    toggleButton.addEventListener('mouseup', () => {
                        passwordInput.type = 'password'; // Hide the password
                    });

                    toggleButton.addEventListener('mouseleave', () => {
                        passwordInput.type = 'password'; // Hide the password if the button is left
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
