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

    // // Check if the form is submitted for adding a new user
    // if(isset($_POST['add_house'])) {
    //     // Get the user data from the form
    //     $housenumber = trim(htmlspecialchars($_POST['housenumber']));
    //     $price = trim(htmlspecialchars($_POST['price']));
    //     $category = htmlspecialchars($_POST['category']);

    //     // Validate the house number to ensure it contains only numerical characters
    //     if (!ctype_digit($housenumber)) {
    //         // Set an error message in the session and redirect back to the form
    //         $_SESSION['error_message'] = "House number should only contain numerical characters.";
    //         header("Location: adminhouses.php?error=add");
    //         exit();
    //     }

    //     // Validate the price to ensure it contains only numerical characters and a single optional period
    //     if (!preg_match('/^\d+(\.\d{1,2})?$/', $price)) {
    //         // Set an error message in the session and redirect back to the form
    //         $_SESSION['error_message'] = "Price should only contain numerical characters and a single optional period.";
    //         header("Location: adminhouses.php?error=add");
    //         exit();
    //     }

    //     // Call the addUser method to add the new user
    //     $added = $admin->addHouse($housenumber, $price, $category);
    //     if($added) {
    //         // User added successfully, you can display a success message here if needed
    //         // echo "User added successfully.";
    //         header("Location: adminhouses.php?house_added=1");
    //         exit();
    //     } else {
    //         // Error occurred while adding user, display an error message or handle as needed
    //         echo "Error occurred while adding user.";
    //     }
    // }

    if (isset($_POST['housenumber']) && isset($_POST['price']) && isset($_POST['category']) && isset($_POST['e_accountnum'])
        && isset($_POST['e_accountname'])) {
        // Get the user data from the form
        $housenumber = trim(htmlspecialchars($_POST['housenumber']));
        $price = trim(htmlspecialchars($_POST['price']));
        $category = htmlspecialchars($_POST['category']);
        $e_accountnum = trim(htmlspecialchars($_POST['e_accountnum']));
        $e_accountname = trim(htmlspecialchars($_POST['e_accountname']));
        $w_accountnum = trim(htmlspecialchars($_POST['w_accountnum']));
        $w_accountname = trim(htmlspecialchars($_POST['w_accountname']));
        $houseaddress = trim(htmlspecialchars($_POST['houseaddress']));
        // $gcash = trim(htmlspecialchars($_POST['gcash']));
        // $bank = trim(htmlspecialchars($_POST['bank']));
    
        // Validate the house number to ensure it contains only numerical characters
        if (ctype_digit($housenumber)) {
            $_SESSION['error_message'] = "Should only be alphabetical characters";
            header("Location: adminhouses.php");
            exit();
        }

        if (!ctype_digit($e_accountnum)) {
            $_SESSION['error_message'] = "Should only be numerical characters";
            header("Location: adminhouses.php");
            exit();
        }
    
        // Validate the price to ensure it contains only numerical characters and a single optional period
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $price)) {
            header("Location: adminhouses.php");
            exit();
        }

        // if (!ctype_digit($gcash) || !ctype_digit($bank)) {
        //     $_SESSION['error_message'] = "Should only be numerical characters";
        //     header("Location: adminhouses.php");
        //     exit();
        // }
    
        // Create an instance of your Admin class
        // $admin1 = new Admin($conn);
        // Call the addHouse method to add the new house
        $added = $admin->addHouse($housenumber, $price, $category, $e_accountname, $e_accountnum, $w_accountname, $w_accountnum, $houseaddress);
        if ($added) {
            $_SESSION['success_message'] = "Success";
            header("Location: adminhouses.php");
            exit();
        } else {
            echo "Error occurred while updating user.";
        }
    }
    
    // echo json_encode($response);

    // Check if there's an error message stored in the session
    if (isset($_SESSION['error_message'])) {
        // Display the error message as an alert
        // echo '<div class="alert alert-danger mb-0" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        // // Unset the session variable to clear the error message
        // unset($_SESSION['error_message']);
    }

    if(isset($_POST['edit_house'])) {
        $housenumber = htmlspecialchars($_POST['house_number']);
        $price = htmlspecialchars($_POST['price']);
        $category_id = htmlspecialchars($_POST['category_id']);
        $house_id = $_POST['house_id'];
        $apartmentaddress = $_POST['apartmentaddress'];
        $meralco_accnum = $_POST['meralco_accnum'];
        $meralco_accname = $_POST['meralco_accname'];
        $maynilad_accnum = $_POST['maynilad_accnum'];
        $maynilad_accname = $_POST['maynilad_accname'];
        $updated = $admin->updateHouse($house_id, $housenumber, $price, $category_id, $meralco_accnum, $meralco_accname, $maynilad_accnum, $maynilad_accname, $apartmentaddress);
        if($updated) {
            header("Location: adminhouses.php");
            exit();
        } else {
            echo "Error occurred while updating user.";
        }
    }

    // Check if the form is submitted for deleting a house
    if(isset($_POST['delete_house'])) {
        // Get the house ID to be deleted
        $house_id = $_POST['house_id'];
        // Call the deleteHouse method to delete the house
        $deleted = $admin->deleteHouse($house_id);
        if($deleted) {
            // House deleted successfully, you can display a success message here if needed
            // echo "User deleted successfully.";
            header("Location: adminhouses.php?house_deleted=1");
        } else {
            // Error occurred while deleting user, display an error message or handle as needed
            echo "Error occurred while deleting user.";
        }
    }
    
    // $sql = "SELECT * FROM houses";
    // $sql = "SELECT houses.*, categories.name AS category_name FROM houses INNER JOIN categories ON categories.id = houses.category_id ORDER BY houses.id ASC";
    $sql = "SELECT houses.*, categories.name AS category_name, houseaccounts.elec_accnum, houseaccounts.elec_accname, houseaccounts.water_accname, houseaccounts.water_accnum
    FROM houses
    INNER JOIN categories ON categories.id = houses.category_id
    LEFT JOIN houseaccounts ON houses.id = houseaccounts.houses_id
    ORDER BY houses.id ASC";
    $result = $admin->conn->query($sql);
    $sql_option = "SELECT * FROM categories";
    $result_option = $admin->conn->query($sql_option);
    $sql2 = "SELECT * FROM categories";
    $sql2_option = $admin->conn->query($sql2);




    // Check if the form is submitted for adding a new category
    if(isset($_POST['add_category'])) {
        // Get the category data from the form
        $categoryname = htmlspecialchars($_POST['categoryname']);
        // Call the addCategory method to add the new category
        $added = $admin->addCategory($categoryname);
        if($added) {
            // Category added successfully, you can display a success message here if needed
            // echo "Category added successfully.";
            header("Location: adminhouses.php?category_added=1");
            exit();
        } else {
            // Error occurred while adding category, display an error message or handle as needed
            echo "Error occurred while adding Category.";
        }
    }

    if(isset($_POST['edit_category'])) {
        $categoryname = htmlspecialchars($_POST['categoryname']);
        $categoryid = $_POST['categoryid'];
        $updated = $admin->updateCategory($categoryid, $categoryname);
        if($updated) {
            header("Location: adminhouses.php");
            exit();
        } else {
            echo "Error occurred while updating user.";
        }
    }

    // Check if the form is submitted for deleting a category
    if(isset($_POST['delete_category'])) {
        // Get the category ID to be deleted
        $categoryid = $_POST['categoryid'];
        // Call the deleteCategory method to delete the category
        $deleted = $admin->deleteCategory($categoryid);
        if($deleted) {
            // Category deleted successfully, you can display a success message here if needed
            header("Location: adminhouses.php?category_deleted=1");
        } else {
            // Error occurred while deleting category, display an error message or handle as needed
            echo "Error occurred while deleting category.";
        }
    }

    // Get sort column and direction from query parameters
    $sortColumn = isset($_GET['column']) ? $_GET['column'] : 'id';
    $sortDirection = isset($_GET['direction']) && $_GET['direction'] === 'desc' ? 'DESC' : 'ASC';

    // Ensure the sort column is one of the allowed columns to prevent SQL injection
    $allowedColumns = ['id', 'name'];
    if (!in_array($sortColumn, $allowedColumns)) {
        $sortColumn = 'id';
    }

    // Determine the next sort direction
    $nextSortDirection = $sortDirection === 'ASC' ? 'desc' : 'asc';

    // Determine the arrow symbol based on the current sort direction
    $arrow = $sortDirection === 'ASC' ? '↑' : '↓';
    
    $sqlcategory = "SELECT * FROM categories ORDER BY $sortColumn $sortDirection";
    $resultcategory = $admin->conn->query($sqlcategory);

    // $sql3 = "SELECT ha.* FROM houseaccounts ha INNER JOIN houses h ON ha.houses_id = h.id";
    // $result3 = $admin->conn->query($sql3);

    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page
    $page = "adminhouses";
?>

   
    <div class="container-fluid">
        <div class="row">
            
            
        <?php include 'includes/header.php'; ?>



            <div class="col main content" style="padding-top: 12px; padding-bottom: 12px; max-height: 100vh;">
                <div class="card-body" style="margin-top: 12px; height:100%; max-height: 100%; overflow-y: auto; display: flex; flex-direction: column;">
                    <div class="row">
                        <div class="col-lg-12" id="tableheader">
                            <button class="btn btn-primary float-end table-buttons-update" id="new_category"><i class="fa fa-plus"></i> New Category</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <a href="?column=id&direction=<?php echo $nextSortDirection; ?>" class="text-decoration-none d-inline-block" style="color: #212529;">
                                            #
                                            <?php echo $sortColumn === 'id' ? $arrow : ''; ?>
                                        </a>
                                    </th>
                                    <th scope="col">
                                        <a href="?column=name&direction=<?php echo $nextSortDirection; ?>" class="text-decoration-none d-inline-block" style="color: #212529;">
                                            Apartment Type
                                            <?php echo $sortColumn === 'name' ? $arrow : ''; ?>
                                        </a>
                                    </th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($resultcategory->num_rows > 0) {
                                    // Output data of each row
                                    while($rowcategory = $resultcategory->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $rowcategory['id'] . "</th>";
                                        echo "<td>" . htmlspecialchars($rowcategory['name']) . "</td>";
                                        echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
                                        echo "<div class='row justify-content-center m-0'>";
                                        echo "<div class='col-xl-6 px-2'>";
                                        // Add a form with a delete button for each record
                                        echo "<form method='POST' action='adminhouses.php' class='float-xl-end align-items-center'>";
                                        echo "<input type='hidden' name='categoryid' value='" . $rowcategory['id'] . "'>";
                                        echo "<button type='submit' name='delete_category' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>";
                                        echo "</form>";
                                        echo "</div>";
                                        echo "<div class='col-xl-6 px-2'>";
                                        // Add a form with a update button for each record
                                        echo "<input type='hidden' name='categoryid' value='" . $rowcategory['id'] . "'>";
                                        echo "<button type='button' class='btn btn-primary update-category-btn float-xl-start table-buttons-update' data-id='" . $rowcategory['id'] . "' data-categoryname='" . htmlspecialchars($rowcategory['name']) . "' style='width: 80px;'>Update</button>";
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

                    <div class="row">
                        <div class="col-lg-12" id="tableheader">
                            <!-- <input type="text" id="searchBar" placeholder="Search..." class="form-control mb-3" />
                            <button class="btn btn-primary float-end table-buttons-update" id="new_house"><i class="fa fa-plus"></i> New Apartment</button> -->
                            <div class="row">
                                <div class="col-6">
                                    <input type="text" id="searchBar" placeholder="Search..." class="form-control mt-3 mb-3 " style="max-width: 180px;" />
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-primary float-end table-buttons-update" id="new_house"><i class="fa fa-plus"></i> New Apartment</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive"  id="tablelimiter">
                        <table class="table table-striped table-bordered" id="secondTable">
                            <thead>
                                <tr>
                                    <!-- <th scope="col" data-column="id" onclick="sortTable('id')">#</th>
                                    <th scope="col" data-column="house_name" onclick="sortTable('house_name')">Apartment Name</th>
                                    <th scope="col" data-column="price" onclick="sortTable('price')">Rent</th>
                                    <th scope="col" data-column="category_name" onclick="sortTable('category_name')">Apartment Type</th>
                                    <th scope="col" data-column="elec_accnum" style="max-width: 80px;" onclick="sortTable('elec_accnum')">Meralco #</th>
                                    <th scope="col" data-column="elec_accname" onclick="sortTable('elec_accname')">Meralco Account Name</th>
                                    <th scope="col" data-column="water_accnum" onclick="sortTable('water_accnum')">Maynilad #</th>
                                    <th scope="col" data-column="water_accname" onclick="sortTable('water_accname')">Maynilad Account Name</th>
                                    <th scope="col">Actions</th> -->
                                    <th scope="col" class="sortable-column" data-column="id" onclick="sortTable('id')"># <span class="sort-arrow" data-column="id"></span></th>
                                    <th scope="col" class="sortable-column" data-column="house_name" onclick="sortTable('house_name')" style="cursor: pointer;">Apartment Name <span class="sort-arrow" data-column="house_name"></span></th>
                                    <th scope="col" class="sortable-column" data-column="price" onclick="sortTable('price')" style="cursor: pointer;">Rent Amount <span class="sort-arrow" data-column="price"></span></th>
                                    <th scope="col" class="sortable-column" data-column="category_name" onclick="sortTable('category_name')" style="cursor: pointer;">Apartment Type <span class="sort-arrow" data-column="category_name"></span></th>
                                    <th scope="col" class="sortable-column" data-column="elec_accnum" style="max-width: 80px; cursor: pointer;" onclick="sortTable('elec_accnum')">Meralco # <span class="sort-arrow" data-column="elec_accnum"></span></th>
                                    <th scope="col" class="sortable-column" data-column="elec_accname" onclick="sortTable('elec_accname')" style="cursor: pointer;">Meralco Account Name <span class="sort-arrow" data-column="elec_accname"></span></th>
                                    <th scope="col" class="sortable-column" data-column="water_accnum" onclick="sortTable('water_accnum')" style="cursor: pointer;">Maynilad # <span class="sort-arrow" data-column="water_accnum"></span></th>
                                    <th scope="col" class="sortable-column" data-column="water_accname" onclick="sortTable('water_accname')" style="cursor: pointer;">Maynilad Account Name <span class="sort-arrow" data-column="water_accname"></span></th>
                                    <th scope="col" class="sortable-column" data-column="address" onclick="sortTable('address')" style="cursor: pointer;">Address <span class="sort-arrow" data-column="address"></span></th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="house_data">
                                <?php
                                if ($result->num_rows > 0) {
                                    // Output data of each row
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $row['id'] . "</th>";
                                        echo "<td>" . htmlspecialchars($row['house_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                                        // echo "<td>" . htmlspecialchars($row['category_id']) . " <br> " . $row['category_name'] . " </td>";
                                        echo "<td>" . $row['category_name'] . " </td>";
                                        echo "<td>" . $row['elec_accnum'] . "</td>";
                                        echo "<td>" . $row['elec_accname'] . "</td>";
                                        echo "<td>" . $row['water_accnum'] . "</td>";
                                        echo "<td>" . $row['water_accname'] . "</td>";
                                        echo "<td>" . $row['address'] . "</td>";
                                        echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
                                        echo "<div class='row justify-content-center m-0'>";
                                        echo "<div class='col-xxl-6 px-2'>";
                                        // Add a form with a delete button for each record
                                        echo "<form method='POST' action='adminhouses.php' class='float-xxl-end align-items-center'>";
                                        echo "<input type='hidden' name='house_id' value='" . $row['id'] . "'>";
                                        echo "<button type='submit' name='delete_house' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>";
                                        echo "</form>";
                                        echo "</div>";
                                        echo "<div class='col-xxl-6 px-2'>";
                                        // Add a form with a update button for each record
                                        echo "<input type='hidden' name='house_id' value='" . $row['id'] . "'>";
                                        echo "<button type='button' class='btn btn-primary update-house-btn float-xxl-start table-buttons-update' data-id='" . $row['id'] . "' data-housenumber='" . htmlspecialchars($row['house_name']) . "' data-price='" . htmlspecialchars($row['price']) . "' data-categoryid='" . htmlspecialchars($row['category_id']) . "' data-meralconum='" . htmlspecialchars($row['elec_accnum']) . "' data-meralconame='" . htmlspecialchars($row['elec_accname']) . "' data-mayniladnum='" . htmlspecialchars($row['water_accnum']) . "' data-mayniladname='" . htmlspecialchars($row['water_accname']) . "' data-address='" . htmlspecialchars($row['address']) . "' style='width: 80px;'>Update</button>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No users found</td></tr>";
                                }
                                // $admin->conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- <div class="row" style="min-height: 54px;">
                    </div> -->
                </div>
                <!-- New House Modal -->
                <div class="modal fade" id="newHouseModal_house" tabindex="-1" aria-labelledby="newHouseModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header" style="background-color: #527853;">
                            <h5 class="modal-title text-white" id="newhouseModalLabel">New Apartment</h5>
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
                                    echo '<script>var newHouseModal = new bootstrap.Modal(document.getElementById("newHouseModal_house"), { keyboard: false });newHouseModal.show();</script>';
                                    unset($_SESSION['error_message']);
                                } else if (isset($_SESSION['success_message']) && !empty($_SESSION['success_message'])) {
                                    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                                    echo '<script>var newHouseModal = new bootstrap.Modal(document.getElementById("newHouseModal_house"), { keyboard: false });newHouseModal.show();</script>';
                                    unset($_SESSION['success_message']);
                                }
                            ?>
                            <form id="newHouseForm" method="POST">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Apartment Name</label>
                                            <input type="text" class="form-control" id="username" name="housenumber" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price</label>
                                            <input type="text" class="form-control" id="price" name="price" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Category Name</label>
                                            <select class="form-select" id="role" name="category" required>
                                                <!-- <option value="3">3</option> -->
                                                <?php
                                                    // Fetch categories from the database
                                                    

                                                    // Check if categories exist
                                                    if ($result_option->num_rows > 0) {
                                                        // Output options for each category
                                                        while ($row_option = $result_option->fetch_assoc()) {
                                                            // echo "<option value='" . $row_option['category_id'] . "'></option>";
                                                            echo "<option value='" . $row_option['id'] . "'>" . $row_option['name'] . "</option>";
                                                        }
                                                    } else {
                                                        echo "<option value=''>No categories found</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="e_accountnum" class="form-label">Meralco Account #</label>
                                            <input type="number" class="form-control" id="e_accountnum" name="e_accountnum" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="e_accountname" class="form-label">Meralco Account Name</label>
                                            <input type="text" class="form-control" id="e_accountname" name="e_accountname" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="w_accountnum" class="form-label">Maynilad Account #</label>
                                            <input type="number" class="form-control" id="w_accountnum" name="w_accountnum" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="w_accountname" class="form-label">Maynilad Account Name</label>
                                            <input type="text" class="form-control" id="w_accountname" name="w_accountname" required>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="gcash" class="form-label">Gcash</label>
                                            <input type="text" class="form-control" id="gcash" name="gcash" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="bank" class="form-label">Bank</label>
                                            <input type="text" class="form-control" id="bank" name="bank" required>
                                        </div>
                                    </div> -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="houseaddress" class="form-label">Apartment Address</label>
                                            <input type="text" class="form-control" id="houseaddress" name="houseaddress" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <button type="submit" name="add_house" class="btn btn-primary table-buttons-update">Add Apartment</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>
                <!-- Update Apartment Modal -->
                <div class="modal fade" id="updateHouseModal" tabindex="-1" aria-labelledby="updateHouseModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #527853;">
                                <h5 class="modal-title text-white" id="updateHouseModalLabel">Update Apartment</h5>
                                <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="updateHouseForm" method="POST" action="adminhouses.php">
                                    <div class="row">
                                        <input type="hidden" id="updateHouseId" name="house_id">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="updateHouseNumber" class="form-label">Apartment Name</label>
                                                <input type="text" class="form-control" id="updateHouseNumber" name="house_number" required>
                                            </div>
                                        </div>                                        
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="updatePrice" class="form-label">Price</label>
                                                <input type="text" class="form-control" id="updatePrice" name="price" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="updateCategoryId" class="form-label">Category Name</label>
                                                <select class="form-select" id="updateCategoryId" name="category_id" required>
                                                    <?php
                                                        // Fetch categories from the database
                                                        

                                                        // Check if categories exist
                                                        if ($sql2_option->num_rows > 0) {
                                                            // Output options for each category
                                                            while ($row_option2 = $sql2_option->fetch_assoc()) {
                                                                // echo "<option value='" . $row_option['category_id'] . "'></option>";
                                                                echo "<option value='" . $row_option2['id'] . "'>" . $row_option2['name'] . "</option>";
                                                            }
                                                        } else {
                                                            echo "<option value=''>No categories found</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="updateApartmentAddress" class="form-label">Apartment Address</label>
                                                <input type="text" class="form-control" id="updateApartmentAddress" name="apartmentaddress" required>
                                            </div>
                                        </div>                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="update_meralco_accnum" class="form-label">Meralco Account #</label>
                                                <input type="number" class="form-control" id="update_meralco_accnum" name="meralco_accnum">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="update_meralco_accname" class="form-label">Meralco Account Name</label>
                                                <input type="text" class="form-control" id="update_meralco_accname" name="meralco_accname">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="update_maynilad_accnum" class="form-label">Maynilad Account #</label>
                                                <input type="number" class="form-control" id="update_maynilad_accnum" name="maynilad_accnum">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="update_maynilad_accname" class="form-label">Maynilad Account Name</label>
                                                <input type="text" class="form-control" id="update_maynilad_accname" name="maynilad_accname">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" name="edit_house" class="btn btn-primary table-buttons-update">Update Apartment</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New Category Modal -->
                <div class="modal fade" id="newCategoryModal" tabindex="-1" aria-labelledby="newCategoryModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header" style="background-color: #527853;">
                            <h5 class="modal-title text-white" id="newcategoryModalLabel">New Category</h5>
                            <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="newCategoryForm" method="POST" action="adminhouses.php">
                            <div class="mb-3">
                                <label for="username" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="username" name="categoryname" required>
                            </div>
                            <button type="submit" name="add_category" class="btn btn-primary table-buttons-update">Add Category</button>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>
                <!-- Update Category Modal -->
                <div class="modal fade" id="updateCategoryModal" tabindex="-1" aria-labelledby="updateCategoryModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #527853;">
                                <h5 class="modal-title text-white" id="updateCategoryModalLabel">Update Category</h5>
                                <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="updateCategoryForm" method="POST" action="adminhouses.php">
                                    <input type="hidden" id="updateCategoryId" name="categoryid">
                                    <div class="mb-3">
                                        <label for="updateCategoryname" class="form-label">Category Name</label>
                                        <input type="text" class="form-control" id="updateCategoryname" name="categoryname" required>
                                    </div>
                                    <button type="submit" name="edit_category" class="btn btn-primary w-50 align-self-center table-buttons-update">Update Category</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <!-- <script>
                    $(document).ready(function() {
                        $('#newHouseForm').on('submit', function(e) {
                            e.preventDefault(); // Prevent the form from submitting the traditional way

                            $.ajax({
                                url: 'adminhouses.php', // The PHP script that processes the form
                                type: 'POST',
                                data: $(this).serialize(), // Serialize the form data
                                dataType: 'json',
                                success: function(response) {
                                    console.log(response); // Log the response to the console

                                    // Handle the response from the server
                                    if (response.success) {
                                        alert('House added successfully!');
                                        // Optionally, close the modal and refresh the house list
                                        $('#newHouseModal_house').modal('hide');
                                        // Refresh the house list or update the UI as needed
                                    } else {
                                        // Display the error message
                                        alert(response.message);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.log(response);
                                    // Handle any errors that occurred during the AJAX request
                                    alert('An error occurred: ' + error);
                                }
                            });
                        });
                    });
                </script> -->
                <script>
                    document.getElementById('new_house').addEventListener('click', function () {
                        var newHouseModal_house = new bootstrap.Modal(document.getElementById('newHouseModal_house'), {
                            keyboard: false
                        });
                        newHouseModal_house.show();
                    });
                </script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        // Use event delegation by attaching the event listener to a parent element
                        document.body.addEventListener('click', function (event) {
                            // Check if the clicked element matches the '.update-house-btn' class
                            if (event.target.classList.contains('update-house-btn')) {
                                var button = event.target; // The button that was clicked

                                // Retrieve the necessary data attributes
                                var userId = button.getAttribute('data-id');
                                var username = button.getAttribute('data-housenumber');
                                var password = button.getAttribute('data-price');
                                var role = button.getAttribute('data-categoryid');
                                var meralcoNum = button.getAttribute('data-meralconum');
                                var meralcoNam = button.getAttribute('data-meralconame');
                                var mayniladNum = button.getAttribute('data-mayniladnum');
                                var mayniladNam = button.getAttribute('data-mayniladname');
                                var houseaddress = button.getAttribute('data-address');

                                // Fill the modal with the user's current data
                                document.getElementById('updateHouseId').value = userId;
                                document.getElementById('updateHouseNumber').value = username;
                                document.getElementById('updatePrice').value = password;
                                document.getElementById('updateCategoryId').value = role;
                                document.getElementById('update_meralco_accnum').value = meralcoNum;
                                document.getElementById('update_meralco_accname').value = meralcoNam;
                                document.getElementById('update_maynilad_accnum').value = mayniladNum;
                                document.getElementById('update_maynilad_accname').value = mayniladNam;
                                document.getElementById('updateApartmentAddress').value = houseaddress;
                                
                                // Show the modal
                                var updateHouseModal = new bootstrap.Modal(document.getElementById('updateHouseModal'), {
                                    keyboard: false
                                });
                                updateHouseModal.show();
                            }
                        });
                    });
                </script>

                <script>
                    document.getElementById('new_category').addEventListener('click', function () {
                        var newCategoryModal = new bootstrap.Modal(document.getElementById('newCategoryModal'), {
                            keyboard: false
                        });
                        newCategoryModal.show();
                    });
                </script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var updateButtons = document.querySelectorAll('.update-category-btn');
                        updateButtons.forEach(function (button) {
                            button.addEventListener('click', function () {
                                var userId = button.getAttribute('data-id');
                                var username = button.getAttribute('data-categoryname');
                                
                                // Fill the modal with the user's current data
                                document.getElementById('updateCategoryId').value = userId;
                                document.getElementById('updateCategoryname').value = username;
                                
                                var updateCategoryModal = new bootstrap.Modal(document.getElementById('updateCategoryModal'), {
                                    keyboard: false
                                });
                                updateCategoryModal.show();
                            });
                        });
                    });
                </script>

                <script>
                    // let sortDirection = true; // True means ascending, false means descending

                    // function sortTable(column) {
                    //     const table = document.querySelector('table#secondTable'); // Get the table
                    //     const tbody = table.querySelector('tbody'); // Get tbody (data rows)
                    //     const rows = Array.from(tbody.querySelectorAll('tr')); // Convert rows NodeList to array

                    //     // Find the column index using the data attribute
                    //     const columnIndex = Array.from(document.querySelectorAll('table#secondTable thead th')).findIndex(th => th.getAttribute('data-column') === column);
                        
                    //     if (columnIndex === -1) {
                    //         console.error("Column not found");
                    //         return;
                    //     }
                    //     console.log(`Sorting by column: ${column}, index: ${columnIndex}`);

                    //     // Sort rows based on the clicked column's cell value
                    //     rows.sort((rowA, rowB) => {
                    //         const cellA = rowA.children[columnIndex] ? rowA.children[columnIndex].innerText.trim() : '';
                    //         const cellB = rowB.children[columnIndex] ? rowB.children[columnIndex].innerText.trim() : '';
                    //         console.log(cellA);

                    //         // Check if the cell content is numeric
                    //         const isNumeric = !isNaN(cellA) && !isNaN(cellB);

                    //         if (isNumeric) {
                    //             // Numeric sort (if cells are numbers)
                    //             return sortDirection ? cellA - cellB : cellB - cellA;
                    //         } else {
                    //             // Textual sort (if cells are strings)
                    //             return sortDirection ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
                    //         }
                    //     });
                    //     // Debugging: log the sorted rows
                    //     console.log("Sorted rows:", rows);

                    //     // Append the sorted rows back into the table body
                    //     rows.forEach(row => tbody.appendChild(row));

                    //     // Toggle the sort direction for next time
                    //     sortDirection = !sortDirection;
                        
                    // }
                    let currentSortColumn = ''; // Track the currently sorted column
                    let sortDirection = true; // True means ascending, false means descending

                    function sortTable(column) {
                        // const table = document.querySelector('table#secondTable');
                        // const tbody = table.querySelector('tbody');
                        // const rows = Array.from(tbody.querySelectorAll('tr'));
                        // const columnIndex = Array.from(document.querySelectorAll('table#secondTable thead th')).findIndex(th => th.getAttribute('data-column') === column);

                        // if (columnIndex === -1) {
                        //     console.error("Column not found");
                        //     return;
                        // }

                        // // Sort rows based on the clicked column's cell value
                        // rows.sort((rowA, rowB) => {
                        //     const cellA = rowA.children[columnIndex] ? rowA.children[columnIndex].innerText.trim() : '';
                        //     const cellB = rowB.children[columnIndex] ? rowB.children[columnIndex].innerText.trim() : '';
                            
                        //     const isNumeric = !isNaN(cellA) && !isNaN(cellB);
                        //     if (isNumeric) {
                        //         return sortDirection ? cellA - cellB : cellB - cellA;
                        //     } else {
                        //         return sortDirection ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
                        //     }
                        // });

                        // // Append the sorted rows back into the table body
                        // rows.forEach(row => tbody.appendChild(row));

                        // // Update the sort arrow
                        // document.querySelectorAll('.sort-arrow').forEach(arrow => arrow.innerHTML = ''); // Clear previous arrows
                        // const arrow = document.querySelector(`.sort-arrow[data-column="${column}"]`);
                        // arrow.innerHTML = sortDirection ? '↑' : '↓'; // Set arrow based on sort direction

                        // // Toggle the sort direction for next time
                        // sortDirection = currentSortColumn === column ? !sortDirection : true; // Reset to ascending on new column
                        // currentSortColumn = column;
                    }

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
                <!-- <script>
                    $(document).ready(function() {
                        $('#searchBar').on('input', function() {
                            var searchQuery = $(this).val();

                            $.ajax({
                                url: 'search/search_houses.php', // PHP script to perform search
                                type: 'POST',
                                data: { query: searchQuery },
                                success: function(response) {
                                    $('tbody#house_data').html(response); // Replace table body with new data
                                }
                            });
                        });
                    });
                </script> -->
                <script>
                    $(document).ready(function() {
                        let currentSortColumn = 'id';
                        let currentSortOrder = 'ASC';

                        function fetchUsers(page = 1, query = '', sortColumn = currentSortColumn, sortOrder = currentSortOrder) {
                            $.ajax({
                                url: 'search/search_houses.php',
                                type: 'POST',
                                data: { 
                                    page: page, 
                                    query: query, 
                                    sort_column: sortColumn, 
                                    sort_order: sortOrder 
                                },
                                success: function(response) {
                                    $('tbody#house_data').html(response); // Update table body with data
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
