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
        $firstname = htmlspecialchars($_POST['firstname']);
        $middlename = htmlspecialchars($_POST['middlename']);
        $lastname = htmlspecialchars($_POST['lastname']);
        $contactno = htmlspecialchars($_POST['contactno']);
        $houseid = htmlspecialchars($_POST['house_name']);
        $registerdate = htmlspecialchars($_POST['registerdate']);
        // Call the addTenant method to add the new tenant
        $added = $admin->addTenant($firstname, $middlename, $lastname, $contactno, $houseid, $registerdate);
        if($added) {
            // Tenant added successfully, you can display a success message here if needed
            // echo "Tenant added successfully.";
            header("Location: admintenants.php?tenant_added=1");
            exit();
        } else {
            // Error occurred while adding tenant, display an error message or handle as needed
            echo "Error occurred while adding tenant.";
        }
    }
    
    // $sql = "SELECT * FROM tenants";

    $sql = "SELECT tenants.*, categories.name AS house_name
    FROM tenants
    INNER JOIN houses ON tenants.house_id = houses.id
    INNER JOIN categories ON houses.category_id = categories.id;
    ";
    
    $result = $admin->conn->query($sql);
    // $sql_option = "SELECT houses.id, houses.house_number, categories.name AS category_name FROM houses 
    // INNER JOIN categories ON categories.id = houses.category_id 
    // ORDER BY houses.id ASC";
    // $sql_option = "SELECT houses.id, houses.house_number, houses.category_id AS houses_id FROM houses ORDER BY houses.id ASC";
    $sql_option = "SELECT houses.id, houses.house_number, houses.category_id AS house_catid, categories.name AS category_name FROM houses
    INNER JOIN categories ON categories.id = houses.category_id
    ORDER BY houses.id ASC";
    $result_option = $admin->conn->query($sql_option);
?>


    <div class="container-fluid">
        <div class="row">
        <?php include 'includes/header.php'; ?>
            <div class="col main content">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <button class="btn btn-primary float-end" id="new_tenant"><i class="fa fa-plus"></i> New Tenant</button>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Firstname</th>
                                <th scope="col">Middlename</th>
                                <th scope="col">Lastname</th>
                                <th scope="col">Contact</th>
                                <th scope="col">House</th>
                                <th scope="col">Date</th>
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
                                    echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['house_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['date_start']) . "</td>";
                                    echo "<td class='d-flex'>";
                                    // Add a form with a delete button for each record
                                    echo "<form method='POST' action='admintenants.php'>";
                                    echo "<input type='hidden' name='tenantid' value='" . $row['id'] . "'>";
                                    echo "<button type='submit' name='delete_tenant' class='btn btn-danger' style='width: 80px;'>Delete</button>";
                                    echo "</form>";
                                    // Add a form with a update button for each record
                                    echo "<input type='hidden' name='tenantid' value='" . $row['id'] . "'>";
                                    echo "<button type='button' class='btn btn-primary update-tenant-btn' data-id='" . $row['id'] . "' data-tenantname='" . htmlspecialchars($row['fname']) . "' style='width: 80px;'>Update</button>";
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
                <!-- New Tenants Modal -->
                <div class="modal fade" id="newTenantModal" tabindex="-1" aria-labelledby="newTenantModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="newtenantModalLabel">New Tenant</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="newTenantForm" class="d-flex flex-row" method="POST" action="admintenants.php">
                                <div class="row">
                                    <div class="col-md-4">
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
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Contact #</label>
                                            <input type="text" class="form-control" id="contactno" name="contactno" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">House</label>
                                            <select class="form-select" id="role" name="house_name" required>
                                                <!-- <option value="3">3</option> -->
                                                <?php
                                                    // Fetch houses from the database
                                                    

                                                    // Check if houses exist
                                                    if ($result_option->num_rows > 0) {
                                                        // Output options for each category
                                                        while ($row_option = $result_option->fetch_assoc()) {
                                                            // echo "<option value='" . $row_option['id'] . "'>" . $row_option['category_name'] . "</option>";
                                                            echo "<option value='" . $row_option['id'] . "'>" . $row_option['house_number'] . " " . $row_option['category_name'] . "</option>";
                                                        }
                                                    } else {
                                                        echo "<option value=''>No houses found</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Registration Date</label>
                                            <input type="date" class="form-control" id="registerdate" name="registerdate" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="add_tenant" class="btn btn-primary">Add Tenant</button>
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
                <p>Home</p>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
