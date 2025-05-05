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

    // Check if the form is submitted for adding a new tenant
    if(isset($_POST['add_tenant'])) {
        // Get the tenant data from the form
        list($houseid, $housename) = explode('|', $_POST['house_name']);
        list($users_id, $users_username) = explode('|', $_POST['user_name']);
        $preferreddate = ($_POST['preferreddate']);
        // $firstname = htmlspecialchars($_POST['firstname']);
        // $middlename = htmlspecialchars($_POST['middlename']);
        // $lastname = htmlspecialchars($_POST['lastname']);
        // $contactno = htmlspecialchars($_POST['contactno']);
        // $users_username = htmlspecialchars($_POST['user_name']);
        // $housename = htmlspecialchars($_POST['house_name']);
        // $houseid = htmlspecialchars($_POST['house_id']);
        $registerdate = htmlspecialchars($_POST['registerdate']);
        // Validate contact number length

        // Call the addTenant method to add the new tenant
        $added = $admin->addTenant($users_id, $users_username, $houseid, $housename, $registerdate, $preferreddate);
        if ($added["success"]) {
            // Tenant added successfully, you can display a success message here if needed
            header("Location: admintenants.php?tenant_added=1");
            exit();
        } else {
            // Error occurred while adding tenant, store the error message in a session variable
            $_SESSION['error_message'] = $added["message"];
            header("Location: admintenants.php?error=add");
            exit();
        }
    }
    // Check if there's an error message stored in the session
    if(isset($_SESSION['error_message'])) {
        // Display the error message as an alert
        echo '<div class="alert alert-danger mb-0" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        // Unset the session variable to clear the error message
        unset($_SESSION['error_message']);
    }



    // Check if the form is submitted for editing a tenant
    if (isset($_POST['edit_tenant'])) {
        list($houseid, $housecategory) = explode('|', $_POST['house_data']);
        $tenant_id = htmlspecialchars($_POST['tenant_id']);
        $firstname = htmlspecialchars($_POST['firstname']);
        $middlename = htmlspecialchars($_POST['middlename']);
        $lastname = htmlspecialchars($_POST['lastname']);
        // $contactno = htmlspecialchars($_POST['contactno']);
        $contactno = "";
        // $houseid = htmlspecialchars($_POST['category_id']);
        // $housecategory = htmlspecialchars($_POST['category_id']);
        $registerdate = htmlspecialchars($_POST['registerdate']);

        // Validate contact number length
        // if (preg_match('/^\d{10,11}$/', $contactno)) {
        //     // Call the updateTenant method to update the tenant
        //     $updated = $admin->updateTenant($tenant_id, $firstname, $middlename, $lastname, $contactno, $houseid, $housecategory, $registerdate);
        //     if ($updated) {
        //         // Tenant updated successfully, redirect with success message
        //         header("Location: admintenants.php?tenant_updated=1");
        //         exit();
        //     } else {
        //         // Error occurred while updating tenant, display an error message or handle as needed
        //         echo "Error occurred while updating tenant.";
        //     }
        // } else {
        //     // Invalid contact number length, display an error message or handle as needed
        //     $_SESSION['error_message'] = "Contact number must be 10-11 digits long.";
        //     header("Location: admintenants.php?error=update");
        //     exit();
        // }
        
        // Call the updateTenant method to update the tenant
        $updated = $admin->updateTenant($tenant_id, $firstname, $middlename, $lastname, $contactno, $houseid, $housecategory, $registerdate);
        if ($updated) {
            // Tenant updated successfully, redirect with success message
            header("Location: admintenants.php?tenant_updated=1");
            exit();
        } else {
            // Error occurred while updating tenant, display an error message or handle as needed
            echo "Error occurred while updating tenant.";
        }
    }

    // Check if the form is submitted for deleting a tenant
    if(isset($_POST['delete_tenant'])) {
        // Get the tenant ID to be deleted
        $tenantid = $_POST['tenantid'];
        // Call the deleteTenant method to delete the tenant
        $deleted = $admin->deleteTenant($tenantid);
        if($deleted) {
            // Tenant deleted successfully, you can display a success message here if needed
            // echo "Tenant deleted successfully.";
            header("Location: admintenants.php?tenant_deleted=1");
        } else {
            // Error occurred while deleting tenant, display an error message or handle as needed
            echo "Error occurred while deleting tenant.";
        }
    }
    
    // $sql = "SELECT * FROM tenants";
    // $sql = "SELECT tenants.*
    // FROM tenants
    // INNER JOIN houses 
    // INNER JOIN categories ON houses.category_id = categories.id
    // ";



    // Get sort column and direction from query parameters
    // $sortColumn = isset($_GET['column']) ? $_GET['column'] : 'id';
    // $sortDirection = isset($_GET['direction']) && $_GET['direction'] === 'desc' ? 'DESC' : 'ASC';

    // // Ensure the sort column is one of the allowed columns to prevent SQL injection
    // $allowedColumns = ['id', 'fname', 'mname', 'lname', 'users_username', 'house_category', 'date_start', 'date_preferred'];
    // if (!in_array($sortColumn, $allowedColumns)) {
    //     $sortColumn = 'id';
    // }

    // // Determine the next sort direction
    // $nextSortDirection = $sortDirection === 'ASC' ? 'desc' : 'asc';

    // // Determine the arrow symbol based on the current sort direction
    // $arrow = $sortDirection === 'ASC' ? '↑' : '↓';

    $sql = "SELECT tenants.*, houses.house_name AS house_name
            FROM tenants
            LEFT JOIN houses ON tenants.house_id = houses.id;";

    $result = $admin->conn->query($sql);
    // $sql_option = "SELECT houses.id, houses.house_number, categories.name AS category_name FROM houses 
    // INNER JOIN categories ON categories.id = houses.category_id 
    // ORDER BY houses.id ASC";
    // $sql_option = "SELECT houses.id, houses.house_number, houses.category_id AS houses_id FROM houses ORDER BY houses.id ASC";
    
    // $sql_option = "SELECT houses.id, houses.house_number, houses.category_id AS house_catid, categories.name AS category_name FROM houses
    // INNER JOIN categories ON categories.id = houses.category_id
    // ORDER BY houses.id ASC";
    // $sql_option = "SELECT houses.*, categories.name AS house_name FROM houses INNER JOIN categories ON categories.id = houses.category_id";
    // $result_option = $admin->conn->query($sql_option);

    // $sql_option = "SELECT houses.*, categories.name AS category_name FROM houses 
    // INNER JOIN categories ON categories.id = houses.category_id";
    $sql_option = "SELECT houses.*, categories.name AS category_name 
    FROM houses
    INNER JOIN categories ON categories.id = houses.category_id
    LEFT JOIN tenants ON tenants.house_id = houses.id
    WHERE tenants.house_id IS NULL";
    $result_option = $admin->conn->query($sql_option);

    // $user_option = "SELECT * FROM users WHERE role = 'user'";
    $user_option = "SELECT *, users.id AS id FROM users 
    LEFT JOIN tenants ON tenants.users_id = users.id
    WHERE role = 'user' AND tenants.users_id IS NULL";
    $user_result = $admin->conn->query($user_option);


    $sql_option2 = "SELECT houses.*, categories.name AS category_name FROM houses INNER JOIN categories ON categories.id = houses.category_id";
    $result_option2 = $admin->conn->query($sql_option);

    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page
    $page = "admintenants";
?>

  
    <div class="container-fluid">
        <div class="row">
        <?php include 'includes/header.php'; ?>
            <div class="col main content" style="padding-top: 12px; padding-bottom: 12px; max-height: 100vh;">
                <div class="card-body" style="margin-top: 0px; height: 100%; max-height: 100%; display: flex;flex-direction: column;">
                    <div class="row">
                        <div class="col-lg-12" id="tableheader"> 
                            <!-- <input type="text" id="searchBar" placeholder="Search..." class="form-control mb-3" />
                            <button class="btn btn-primary float-end table-buttons-update" id="new_tenant"><i class="fa fa-plus"></i> New Tenant</button> -->
                            <div class="row">
                                <div class="col-6">
                                    <input type="text" id="searchBar" placeholder="Search..." class="form-control mb-3 " style="max-width: 180px;" />
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-primary float-end table-buttons-update" id="new_tenant"><i class="fa fa-plus"></i> New Tenant</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive"  id="tablelimiter" style="max-height: unset;">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col" data-column="fname" class="sortable-column">
                                        Firstname
                                        <span id="fnameSortArrow"></span>
                                    </th>
                                    <th scope="col" data-column="mname" class="sortable-column">
                                        Middlename
                                        <span id="mnameSortArrow"></span>
                                    </th>
                                    <th scope="col" data-column="lname" class="sortable-column">
                                        Lastname
                                        <span id="lnameSortArrow"></span>
                                    </th>
                                    <th scope="col" data-column="users_username" class="sortable-column">
                                        Username
                                        <span id="usernameSortArrow"></span>
                                    </th>
                                    <th scope="col" data-column="house_category" class="sortable-column">
                                        Apartment
                                        <span id="categorySortArrow"></span>
                                    </th>
                                    <th scope="col" data-column="date_start" class="sortable-column">
                                        Date Registered
                                        <span id="dateStartSortArrow"></span>
                                    </th>
                                    <th scope="col" data-column="date_preferred" class="sortable-column">
                                        Notification Date
                                        <span id="notificationDateSortArrow"></span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    // Output data of each row
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $row['id'] . "</th>";
                                        echo "<td>" . htmlspecialchars($row['fname']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['mname']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['lname']) . "</td>";
                                        // echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['users_username']) . "</td>";
                                        echo "<td>Category: " . htmlspecialchars($row['house_category']) . "<br>House Name: " . htmlspecialchars($row['house_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['date_start']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['date_preferred']) . "</td>";
                                        echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
                                        echo "<div class='row justify-content-center m-0'>";
                                        echo "<div class='col-xl-6 px-2'>";
                                        // Add a form with a delete button for each record
                                        echo "<form method='POST' action='admintenants.php' class='float-xl-end align-items-center' style='height:100%;'>";
                                        echo "<input type='hidden' name='tenantid' value='" . $row['id'] . "'>";
                                        echo "<button type='submit' name='delete_tenant' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>";
                                        echo "</form>";
                                        echo "</div>";
                                        echo "<div class='col-xl-6 px-2'>";
                                        // Add a form with a update button for each record
                                        echo "<input type='hidden' name='tenantid' value='" . $row['id'] . "'>";
                                        echo "<button type='button' class='btn btn-primary update-tenant-btn float-xl-start table-buttons-update' data-id='" . $row['id'] . "' data-tenantname='" . htmlspecialchars($row['fname']) . "' data-middlename= '" . htmlspecialchars($row['mname']) . "' data-lastname= '" . htmlspecialchars($row['lname']) . "' data-contactno= '" . htmlspecialchars($row['contact']) . "' data-registerdate= '" . htmlspecialchars($row['date_start']) . "' style='width: 80px;'>Update</button>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>No tenants found</td></tr>";
                                }
                                $admin->conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- New Tenants Modal -->
                <div class="modal fade" id="newTenantModal" tabindex="-1" aria-labelledby="newTenantModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header" style="background-color: #527853;">
                            <h5 class="modal-title text-white" id="newtenantModalLabel">New Tenant</h5>
                            <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="newTenantForm" class="d-flex flex-row" method="POST" action="admintenants.php">
                                <div class="row">
                                    <!-- <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Middle Name</label>
                                            <input type="text" class="form-control" id="middlename" name="middlename" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                                        </div>
                                    </div> -->

                                    <!-- <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Contact #</label>
                                            <input type="text" class="form-control" id="contactno" name="contactno" required>
                                        </div>
                                    </div> -->

                                    <!-- <div class="col-md-4"> -->
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Apartment Name, Price, Category</label>
                                            <!-- <select class="form-select" id="house" name="house_name" required onchange="updateHouseId()"> -->
                                            <select class="form-select" id="house" name="house_name" required>
                                                <!-- <option value="3">3</option> -->
                                                <?php
                                                    // Fetch houses from the database
                                                    

                                                    // Check if houses exist
                                                    if ($result_option->num_rows > 0) {
                                                        // Output options for each category
                                                        while ($row_option = $result_option->fetch_assoc()) {
                                                            // echo "<option value='" . $row_option['id'] . "'>" . $row_option['category_name'] . "</option>";

                                                            $house_info = $row_option['id'] . "|" . $row_option['category_name'];
                                                            echo "<option value='" . htmlspecialchars($house_info) . "'>" . htmlspecialchars($row_option['house_name']) . " " . htmlspecialchars($row_option['price']) . " " . htmlspecialchars($row_option['category_name']) . "</option>";
                                                            
                                                            // echo "<option value='" . htmlspecialchars($row_option['house_name']) . "' data-house-id='" . $row_option['id'] . "'>" . htmlspecialchars($row_option['house_number']) . " " . htmlspecialchars($row_option['category_name']) . "</option>";
                                                        }
                                                    } else {
                                                        echo "<option value=''>No houses found</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Users</label>
                                            <!-- <select class="form-select" id="house" name="house_name" required onchange="updateHouseId()"> -->
                                            <select class="form-select" id="user" name="user_name" required>
                                                <!-- <option value="3">3</option> -->
                                                <?php
                                                    // Fetch houses from the database
                                                    

                                                    // Check if houses exist
                                                    if ($user_result->num_rows > 0) {
                                                        // Output options for each category
                                                        while ($row_users = $user_result->fetch_assoc()) {
                                                            // echo "<option value='" . $row_option['id'] . "'>" . $row_option['category_name'] . "</option>";

                                                            $user_info = $row_users['id'] . "|" . $row_users['username'];
                                                            echo "<option value='" . htmlspecialchars($user_info) . "'>" . "" . htmlspecialchars($row_users['firstname']) . " " . htmlspecialchars($row_users['lastname']) . "</option>";
                                                            
                                                            // echo "<option value='" . htmlspecialchars($row_option['house_name']) . "' data-house-id='" . $row_option['id'] . "'>" . htmlspecialchars($row_option['house_number']) . " " . htmlspecialchars($row_option['category_name']) . "</option>";
                                                        }
                                                    } else {
                                                        echo "<option value=''>No Users found</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- <input type="hidden" id="house_id" name="house_id" value=""> -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <?php
                                                date_default_timezone_set('Asia/Manila');

                                                // Get the current date in the Philippines time zone
                                                $currentDate = date('Y-m-d');
                                            ?>
                                            <label for="username" class="form-label">Registration Date</label>
                                            <input type="date" class="form-control" id="registerdate" name="registerdate" value="<?php echo $currentDate; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="preferreddate" class="form-label">Date of Payment (Rent is applied to Balance 1 month from this)</label>
                                            <input type="date" class="form-control" id="preferreddate" name="preferreddate">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="add_tenant" class="btn btn-primary table-buttons-update">Add Tenant</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <script>
                            function updateHouseId() {
                                var select = document.getElementById('house');
                                var selectedOption = select.options[select.selectedIndex];
                                var houseId = selectedOption.getAttribute('data-house-id');
                                document.getElementById('house_id').value = houseId;
                            }
                        </script>
                        </div>
                    </div>
                </div>
                <!-- Update House Modal -->
                <div class="modal fade" id="updateTenantModal" tabindex="-1" aria-labelledby="updateTenantModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #527853;">
                                <h5 class="modal-title text-white" id="updateTenantModalLabel">Update Tenant</h5>
                                <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="updateTenantForm" method="POST" action="admintenants.php">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="hidden" id="updateTenantId" name="tenant_id">
                                            <div class="mb-3">
                                                <label for="updateTenantNumber" class="form-label">First Name</label>
                                                <input type="text" class="form-control" id="updateTenantNumber" name="firstname" required>
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
                                        <div class="col-md-4 d-none">
                                            <div class="mb-3">
                                                <label for="updateContactno" class="form-label">Contact</label>
                                                <input type="text" class="form-control" id="updateContactno" name="contactno">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="edit_registerdate" class="form-label">Register Date</label>
                                                <input type="date" class="form-control" id="edit_registerdate" name="registerdate" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="updateCategoryId" class="form-label">Apartment</label>
                                                <select class="form-select" id="updateCategoryId" name="house_data" required>
                                                    <?php
                                                        // Fetch categories from the database
                                                        

                                                        // Check if categories exist
                                                        if ($result_option2->num_rows > 0) {
                                                            // Output options for each category
                                                            while ($row_option2 = $result_option2->fetch_assoc()) {
                                                                // echo "<option value='" . $row_option['category_id'] . "'></option>";
                                                                $house_info2 = $row_option2['id']. "|" . $row_option2['category_name'];
                                                                // echo "<option value='" . htmlspecialchars($house_info2) . "'>" . $row_option2['id'] . " " . $row_option2['category_name'] . "</option>";
                                                                echo "<option value='" . htmlspecialchars($house_info2) . "'>" . $row_option2['house_name'] . " " . $row_option2['price'] . " " . $row_option2['category_name'] . "</option>";
                                                            }
                                                        } else {
                                                            echo "<option value=''>No categories found</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" name="edit_tenant" class="btn btn-primary table-buttons-update">Update User</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.getElementById('new_tenant').addEventListener('click', function () {
                        var newTenantModal = new bootstrap.Modal(document.getElementById('newTenantModal'), {
                            keyboard: false
                        });
                        newTenantModal.show();
                    });
                </script>
                <!-- <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var updateButtons = document.querySelectorAll('.update-tenant-btn');
                        updateButtons.forEach(function (button) {
                            button.addEventListener('click', function () {
                                var tenantId = button.getAttribute('data-id');
                                var firstname = button.getAttribute('data-tenantname');
                                var middlename = button.getAttribute('data-middlename');
                                var lastname = button.getAttribute('data-lastname');
                                var contactno = button.getAttribute('data-contactno');
                                var registerDate = button.getAttribute('data-registerdate');
                                
                                // Fill the modal with the user's current data
                                document.getElementById('updateTenantId').value = tenantId;
                                document.getElementById('updateTenantNumber').value = firstname;
                                document.getElementById('updateMiddlename').value = middlename;
                                document.getElementById('updateLastname').value = lastname;
                                document.getElementById('updateContactno').value = contactno;
                                document.getElementById('edit_registerdate').value = registerDate;
                                var updateTenantModal = new bootstrap.Modal(document.getElementById('updateTenantModal'), {
                                    keyboard: false
                                });
                                updateTenantModal.show();
                            });
                        });
                    });
                </script> -->
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        // Use event delegation by attaching the event listener to a parent element
                        document.body.addEventListener('click', function (event) {
                            // Check if the clicked element matches the '.update-tenant-btn' class
                            if (event.target.classList.contains('update-tenant-btn')) {
                                var button = event.target; // The button that was clicked

                                // Retrieve the necessary data attributes
                                var tenantId = button.getAttribute('data-id');
                                var firstname = button.getAttribute('data-tenantname');
                                var middlename = button.getAttribute('data-middlename');
                                var lastname = button.getAttribute('data-lastname');
                                var contactno = button.getAttribute('data-contactno');
                                var registerDate = button.getAttribute('data-registerdate');

                                // Fill the modal with the user's current data
                                document.getElementById('updateTenantId').value = tenantId;
                                document.getElementById('updateTenantNumber').value = firstname;
                                document.getElementById('updateMiddlename').value = middlename;
                                document.getElementById('updateLastname').value = lastname;
                                document.getElementById('updateContactno').value = contactno;
                                document.getElementById('edit_registerdate').value = registerDate;

                                // Show the modal
                                var updateTenantModal = new bootstrap.Modal(document.getElementById('updateTenantModal'), {
                                    keyboard: false
                                });
                                updateTenantModal.show();
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
                                url: 'search/search_tenants.php',
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
