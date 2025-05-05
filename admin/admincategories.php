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

    // Check if the form is submitted for adding a new category
    if(isset($_POST['add_category'])) {
        // Get the category data from the form
        $categoryname = htmlspecialchars($_POST['categoryname']);
        // Call the addCategory method to add the new category
        $added = $admin->addCategory($categoryname);
        if($added) {
            // Category added successfully, you can display a success message here if needed
            // echo "Category added successfully.";
            header("Location: admincategories.php?category_added=1");
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
            header("Location: admincategories.php");
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
            header("Location: admincategories.php?category_deleted=1");
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
    
    $sql = "SELECT * FROM categories ORDER BY $sortColumn $sortDirection";
    $result = $admin->conn->query($sql);

    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page
    $page = "admincategories";
?>

   
    <div class="container-fluid">
        <div class="row">
        <?php include 'includes/header.php'; ?>
            <div class="col main content">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12" id="tableheader">
                            <button class="btn btn-primary float-end table-buttons-update" id="new_category"><i class="fa fa-plus"></i> New Category</button>
                        </div>
                    </div>
                    <div class="table-responsive"  id="tablelimiter">
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
                                if ($result->num_rows > 0) {
                                    // Output data of each row
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $row['id'] . "</th>";
                                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                        echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
                                        echo "<div class='row justify-content-center m-0'>";
                                        echo "<div class='col-xl-6 px-2'>";
                                        // Add a form with a delete button for each record
                                        echo "<form method='POST' action='admincategories.php' class='float-xl-end align-items-center'>";
                                        echo "<input type='hidden' name='categoryid' value='" . $row['id'] . "'>";
                                        echo "<button type='submit' name='delete_category' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>";
                                        echo "</form>";
                                        echo "</div>";
                                        echo "<div class='col-xl-6 px-2'>";
                                        // Add a form with a update button for each record
                                        echo "<input type='hidden' name='categoryid' value='" . $row['id'] . "'>";
                                        echo "<button type='button' class='btn btn-primary update-category-btn float-xl-start table-buttons-update' data-id='" . $row['id'] . "' data-categoryname='" . htmlspecialchars($row['name']) . "' style='width: 80px;'>Update</button>";
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
                            <form id="newCategoryForm" method="POST" action="admincategories.php">
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
                                <form id="updateCategoryForm" method="POST" action="admincategories.php">
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
                    setFavicon('../asset/Renttrack pro no word.png'); // Change to your favicon path
                    });
                </script>
                <p>Home</p>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
