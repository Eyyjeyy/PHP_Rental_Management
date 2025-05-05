<?php
    include '../admin.php';
    $admin = new Admin();

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
        $categorynamepapers = htmlspecialchars($_POST['categoryname']);
        // Call the addCategory method to add the new category
        $added = $admin->addCategoryPapers($categorynamepapers);
        if($added) {
            // Category added successfully, you can display a success message here if needed
            // echo "Category added successfully.";
            echo 'success';
            // header("Location: adminpapers.php?category_added=1");
            exit();
        } else {
            // Error occurred while adding category, display an error message or handle as needed
            echo 'error';
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['categoryname'])) {
        // Get the category name from the form
        $categorynamepapers = htmlspecialchars($_POST['categoryname']);

        // Call the addCategoryPapers method to add the new category
        $added = $admin->addCategoryPapers($categorynamepapers);

        if ($added) {
            // Category added successfully, return success response
            echo 'success2';
        } else {
            // Error occurred while adding category, return error response
            echo 'error';
        }

        exit(); // Stop further execution after handling AJAX request
    }
    // Check if the form is submitted for deleting a category
    if(isset($_POST['delete_category'])) {
        // Get the category ID to be deleted
        $categoryid = $_POST['categoryid'];
        // Call the deleteCategory method to delete the category
        $deleted = $admin->deleteCategoryPapers($categoryid);
        if($deleted) {
            // Category deleted successfully, you can display a success message here if needed
            // header("Location: admincategories.php?category_deleted=1");
            echo 'success';
        } else {
            // Error occurred while deleting category, display an error message or handle as needed
            // echo "Error occurred while deleting category.";
            echo 'error';
        }
    }

    // Handle form submission for adding papers
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_id'])) {
        $categoryId = $_POST['category_id'];
        $paperName = htmlspecialchars($_POST['paper_name']);

        // File upload handling
        // $uploadDir = '../uploads/'; // Specify your upload directory
        // $fileName = basename($_FILES['paper_file']['name']);
        // $targetPath = $uploadDir . $fileName;

        $uploadDir = '../uploads/'; // Specify your upload directory
        $originalFileName = basename($_FILES['paper_file']['name']);
        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        $fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
        $uniqueFileName = $fileNameWithoutExtension . '_' . uniqid() . '.' . $fileExtension;
        $targetPath = $uploadDir . $uniqueFileName;


        if (move_uploaded_file($_FILES['paper_file']['tmp_name'], $targetPath)) {
            // File uploaded successfully, insert into database
            $added = $admin->addPaper($categoryId, $paperName, $uniqueFileName, $targetPath);
            if ($added) {
                // echo 'success';
                header("Location: adminpapers.php");
            } else {
                // echo 'error';
            }
        } else {
            echo 'error';
        }
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['delete_file']) && $_POST['delete_file'] == 'true') {
            $fileId = intval($_POST['fileid']);
            if ($admin->deletePaper($fileId)) {
                echo 'success';
            } else {
                echo 'error';
            }
            exit;
        }
        // ... handle other POST requests
    }

    // Pagination settings
    $limit = 5; // Number of records per page
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    // Fetch total number of records
    $sql_count = "SELECT COUNT(*) AS count FROM paper_files";
    $result_count = $admin->conn->query($sql_count);
    $total_records = $result_count->fetch_assoc()['count'];
    $total_pages = ceil($total_records / $limit);

    // Fetch records for current page
    $sql_paper = "SELECT * FROM paper_files LIMIT $start, $limit";
    $result_paper = $admin->conn->query($sql_paper);



    $sql = "SELECT * FROM paper_categories";
    $result = $admin->conn->query($sql);

    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page
    $page = "adminpapers";
?>

 
    <div class="container-fluid">
        <div class="row">
        <?php include 'includes/header.php'; ?>
            <div class="col main content">
                <div class="card-body" style="margin-top: 12px;">
                    <!-- <div class="row">
                        <form id="newPapersForm" action="adminpapers.php" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="paperCategory" class="form-label">Select Category</label>
                                <select class="form-select" id="paperCategory" name="category_id" required>
                                    <?php
                                    // Fetch categories from database
                                    $sql = "SELECT * FROM paper_categories";
                                    $result = $admin->conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="paperName" class="form-label">Paper Name</label>
                                <input type="text" class="form-control" id="paperName" name="paper_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="paperFile" class="form-label">Upload File</label>
                                <input type="file" class="form-control" id="paperFile" name="paper_file" required>
                            </div>
                            <div class="col-lg-12">
                                <button type="submit" name="add_papers" class="btn btn-primary float-end" style="padding: 6px 20px;">
                                    <i class="fa fa-plus">
                                    </i>
                                    Add Papers
                                </button>
                            </div>
                        </form>
                    </div> -->
                    <div class="row">
                        <div class="col-lg-12" id="tableheader">
                            <!-- <button class="btn btn-primary float-end table-buttons-update" id="new_papers"><i class="fa fa-plus"></i> New Papers</button> -->
                            <div class="row">
                                <div class="col-6">
                                    <input type="text" id="searchBar" placeholder="Search..." class="form-control mb-3 " style="max-width: 180px;" />
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-primary float-end table-buttons-update" id="new_papers"><i class="fa fa-plus"></i> New Papers</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive"  id="tablelimiter">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" class="sortable" data-column="file_id" data-direction="asc">#</th>
                                    <th scope="col" class="sortable" data-column="file_name" data-direction="asc">Paper Name</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="papertable">
                                <?php
                                $sql_paper = "SELECT * FROM paper_files";
                                $result_paper = $admin->conn->query($sql_paper);
                                if ($result_paper->num_rows > 0) {
                                    while ($row_paper = $result_paper->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $row_paper['id'] . "</th>";
                                        echo "<td>" . htmlspecialchars($row_paper['file_name']) . "</td>";
                                        echo "<td>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <!-- <div id="pagination-controls">
                            <button id="prev-page" onclick="prevPage()">Previous</button>
                            <span id="page-info"></span>
                            <button id="next-page" onclick="nextPage()">Next</button>
                        </div> -->
                        <nav aria-label="Page navigation">
                            <!-- <ul class="pagination justify-content-center" id="pagination-controls"></ul> -->
                            <ul class="pagination justify-content-center" id="use commented the <uk> code commented above this line to utilize previous pagination before the search bar"></ul>
                        </nav>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <button class="btn btn-primary float-end table-buttons-update" id="new_category"><i class="fa fa-plus"></i> New Category</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <a href="#" onclick="handleSort('id')" class="text-decoration-none" style="color: #212529;">
                                            # <span id="sortIconId"></span>
                                        </a>
                                    </th>
                                    <th scope="col">
                                        <a href="#" onclick="handleSort('name')" class="text-decoration-none" style="color: #212529;">
                                            Paper Type <span id="sortIconName"></span>
                                        </a>
                                    </th>
                                    <th scope="col">
                                        <a href="#" onclick="handleSort('created_at')" class="text-decoration-none" style="color: #212529;">
                                            Created At <span id="sortIconCreatedAt"></span>
                                        </a>
                                    </th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="result">
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $row['id'] . "</th>";
                                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                        echo "<td>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                }
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
                            <form id="newCategoryForm" method="POST" action="adminpapers.php">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="username" name="categoryname" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="add_category" class="btn btn-primary table-buttons-update">Add Category</button>
                                </div>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>
                <!-- New Papers Modal -->
                <div class="modal fade" id="newPapersModal" tabindex="-1" aria-labelledby="newCategoryPapersLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #527853;">
                                <h5 class="modal-title text-white" id="newpapersModalLabel">New Papers</h5>
                                <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="newPaperFilesForm" method="POST" action="adminpapers.php">
                                    <!-- <div class="mb-3">
                                        <label for="username" class="form-label">Paper Name</label>
                                        <input type="text" class="form-control" id="username" name="categoryname" required>
                                    </div> -->
                                    <div class="mb-3">
                                        <label for="paperCategory" class="form-label">Select Category</label>
                                        <select class="form-select" id="paperCategory" name="category_id" required>
                                            <?php
                                            // Fetch categories from database
                                            $sql = "SELECT * FROM paper_categories";
                                            $result = $admin->conn->query($sql);
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="paperName" class="form-label">Paper Name</label>
                                        <input type="text" class="form-control" id="paperName" name="paper_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="paperFile" class="form-label">Upload File</label>
                                        <input type="file" class="form-control" id="paperFile" name="paper_file" required>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="add_category" class="btn btn-primary table-buttons-update">Add Papers</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // $(document).ready(function(){
        //     $('#newCategoryForm').on('submit', function(e){
        //         e.preventDefault();

        //         var formData = new FormData(this);
        //         $.ajax({
        //             url: 'adminpapers.php',
        //             type: 'GET',
        //             data: formData,
        //             processData: false,
        //             contentType: false,
        //             success: function(response) {
        //                 console.log('Form submitted successfully:', response);
        //                 // $('#message-input').val('');
        //                 // $('#image-input').val('');
        //                 // fetchMessages();
        //             },
        //             error: function(jqXHR, textStatus, errorThrown) {
        //                 console.error('Error submitting form:', textStatus, errorThrown);
        //             }
        //         });
        //     });
        // });
        document.getElementById('newCategoryForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            var form = event.target;
            var formData = new FormData(form);

            fetch('adminpapers.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log('Response from server:', data);

                var messageDiv = document.getElementById('result');
                if (data.includes('success')) {
                    messageDiv.innerHTML = '<p class="text-success">Category added successfully.</p>';
                    
                    // Update the dropdown with the new category
                    var paperCategorySelect = document.getElementById('paperCategory');
                    var newOption = document.createElement('option');
                    newOption.value = data.newCategoryId; // Assuming the server returns the new category ID
                    newOption.text = formData.get('categoryname');
                    paperCategorySelect.appendChild(newOption);

                    // Optionally, clear the form fields after successful submission
                    form.reset();
                } else {
                    messageDiv.innerHTML = '<p class="text-danger">Failed to add category.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
        });
    </script>
    <script>
        // document.getElementById('new_category').addEventListener('click', function () {
        //     var newCategoryModal = new bootstrap.Modal(document.getElementById('newCategoryModal'), {
        //         keyboard: false
        //     });
        //     newCategoryModal.show();
        // });

        document.getElementById('new_category').addEventListener('click', function () {
            var newCategoryModal = new bootstrap.Modal(document.getElementById('newCategoryModal'), {
                keyboard: false
            });
            newCategoryModal.show();
        });

        // document.getElementById('newPapersForm').addEventListener('submit', function(event) {
        //     event.preventDefault();
            
        //     var form = event.target;
        //     var formData = new FormData(form);

        //     fetch('adminpapers.php', {
        //         method: 'POST',
        //         body: formData
        //     })
        //     .then(response => response.text())
        //     .then(data => {
        //         console.log('Response from server:', data);
        //         if (data.includes('success')) {
        //             // Handle successful paper addition
        //             // window.location.reload(); // Reload the page to see the new paper
        //             form.reset();
        //         } else {
        //             // Handle error in paper addition
        //             console.error('Failed to add paper');
        //         }
        //     })
        //     .catch(error => {
        //         console.error('Error:', error);
        //     });
        // });
    </script>
    <script>
        document.getElementById('new_papers').addEventListener('click', function () {
            var newCategoryModal = new bootstrap.Modal(document.getElementById('newPapersModal'), {
                keyboard: false
            });
            newCategoryModal.show();
        });

        document.getElementById('newPaperFilesForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            var form = event.target;
            var formData = new FormData(form);

            fetch('adminpapers.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log('Response from server:', data);
                if (data.includes('success')) {
                    // Handle successful paper addition
                    // window.location.reload(); // Reload the page to see the new paper
                    window.location.reload(); // Reload the page to see the new paper
                    form.reset();
                } else {
                    // Handle error in paper addition
                    console.error('Failed to add paper');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
    <script>
        var sortColumnType = 'id';
        var sortDirectionType = 'asc';
        var arrayData = [];

        var source = new EventSource("../fetch_papers.php");
        source.onmessage = function(event) {
            arrayData = JSON.parse(event.data);
            renderTableType();
            updateDropdown();
        };

        function renderTableType() {
            var dataContainer = document.querySelector('tbody#result');
            dataContainer.innerHTML = '';

            arrayData.sort(function(a, b) {
                var aValueType = a[sortColumnType];
                var bValueType = b[sortColumnType];

                if (sortColumnType === 'id') {
                    aValueType = parseInt(aValueType, 10);
                    bValueType = parseInt(bValueType, 10);
                    return sortDirectionType === 'asc' ? aValueType - bValueType : bValueType - aValueType;
                } else {
                    aValueType = aValueType.toString().toLowerCase();
                    bValueType = bValueType.toString().toLowerCase();
                    if (aValueType < bValueType) {
                        return sortDirectionType === 'asc' ? -1 : 1;
                    }
                    if (aValueType > bValueType) {
                        return sortDirectionType === 'asc' ? 1 : -1;
                    }
                    return 0;
                }
            });

            arrayData.forEach(e => {
                dataContainer.innerHTML += `
                    <tr>
                        <td>${e.id}</td>
                        <td>${e.name}</td>
                        <td>${e.created_at}</td>
                        <td style="text-align: center;">
                            <button class="btn btn-danger btn-delete table-buttons-delete" name="delete_category" data-id="${e.id}" style="width: 100px;">Delete</button>
                        </td>
                    </tr>
                `;
            });

            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    var id = this.getAttribute('data-id');
                    deleteCategory(id);
                });
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
            document.getElementById('sortIconCreatedAt').textContent = '';

            let icon = sortDirectionType === 'asc' ? '↑' : '↓';
            if (sortColumnType === 'id') {
                document.getElementById('sortIconId').textContent = icon;
            } else if (sortColumnType === 'name') {
                document.getElementById('sortIconName').textContent = icon;
            } else if (sortColumnType === 'created_at') {
                document.getElementById('sortIconCreatedAt').textContent = icon;
            }
        }

        function updateDropdown() {
            var paperCategorySelect = document.getElementById('paperCategory');
            paperCategorySelect.innerHTML = '';

            arrayData.forEach(function(category) {
                var option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                paperCategorySelect.appendChild(option);
            });
        }

        function deleteCategory(id) {
            var formData = new FormData();
            formData.append('delete_category', 'true');
            formData.append('id', id);

            fetch('adminpapers.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes('success')) {
                    arrayData = arrayData.filter(item => item.id != id);
                    renderTableType();
                }
            });
        }
    </script>


    <!-- <script>
        var sortColumn = 'id'; // Default sort column
        var sortDirection = 'asc'; // Default sort direction

        var source = new EventSource("../fetch_papers.php");
        source.onmessage = function(event) {
            // document.getElementById("result").innerHTML += event.data + "<br>";
            var arrayData = JSON.parse(event.data);
            console.log(arrayData);
            var dataContainer = document.querySelector('tbody#result')
            dataContainer.innerHTML = ''
            arrayData.forEach(e => {
                dataContainer.innerHTML +=`
                <tr>
                    <td>${e.id}</td>
                    <td>${e.name}</td>
                    <td>${e.created_at}</td>
                    <td>
                        <button class="btn btn-danger btn-delete table-buttons-delete" name="delete_category" data-id="${e.id}">Delete</button>
                    </td>
                </tr>
                `;
            });

            var paperCategorySelect = document.getElementById('paperCategory');
            paperCategorySelect.innerHTML = ''; // Clear existing options

            // Update the category dropdown
            arrayData.forEach(function(category) {
                var option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                paperCategorySelect.appendChild(option);
            });
        }
    </script> -->

    <!-- <script>
        var allDataFiles = [];
        var currentPage = 1;
        var itemsPerPage = 5;

        var source_files = new EventSource("../fetch_papers_files.php");
        source_files.onmessage = function(event_files) {
            // allDataFiles = JSON.parse(event_files.data);
            // currentPage = 1; // Reset to the first page whenever new data is fetched
            // renderTable();
            // updatePaginationControls();
            var newDataFiles = JSON.parse(event_files.data);
            
            if (JSON.stringify(allDataFiles) !== JSON.stringify(newDataFiles)) {
                allDataFiles = newDataFiles;
                console.log("Updated allDataFiles:", allDataFiles);
                renderTable();
                updatePaginationControls();
            }
        };

        

        var sortColumn = 'file_id'; // Default sort column
        var sortDirection = 'asc'; // Default sort direction

        function renderTable() {
            var dataContainer_files = document.querySelector('tbody#papertable');
            dataContainer_files.innerHTML = '';

            var startIndex = (currentPage - 1) * itemsPerPage;
            var endIndex = startIndex + itemsPerPage;
            var paginatedData = allDataFiles.slice(startIndex, endIndex);

            // Render the table headers with sort arrows
            var tableHeaders = `
                <tr>
                    <th scope="col"><a href="#" onclick="sortTable('file_id')" class="text-decoration-none" style="color: #212529;"># ${sortColumn === 'file_id' ? (sortDirection === 'asc' ? '↑' : '↓') : ''}</a></th>
                    <th scope="col"><a href="#" onclick="sortTable('file_name')" class="text-decoration-none" style="color: #212529;">File Name ${sortColumn === 'file_name' ? (sortDirection === 'asc' ? '↑' : '↓') : ''}</a></th>
                    <th scope="col"><a href="#" onclick="sortTable('category_name')" class="text-decoration-none" style="color: #212529;">Category ${sortColumn === 'category_name' ? (sortDirection === 'asc' ? '↑' : '↓') : ''}</a></th>
                    <th scope="col"><a href="#" onclick="sortTable('uploaded_at')" class="text-decoration-none" style="color: #212529;">Uploaded At ${sortColumn === 'uploaded_at' ? (sortDirection === 'asc' ? '↑' : '↓') : ''}</a></th>
                    <th scope="col">Actions</th>
                </tr>
            `;
            document.querySelector('thead').innerHTML = tableHeaders;

            paginatedData.forEach(e_files => {
                dataContainer_files.innerHTML += `
                    <tr>
                        <td>${e_files.file_id}</td>
                        <td>${e_files.file_name}</td>
                        <td>${e_files.category_name}</td>
                        <td>${e_files.uploaded_at}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <div class="row m-0">
                                    <div class="col d-flex justify-content-center mb-3 px-2">
                                        <button class="btn btn-danger btn-delete table-buttons-delete" name="delete_file" data-id="${e_files.file_id}" style="width: 100px;">Delete</button>
                                    </div>
                                    <div class="col d-flex justify-content-center px-2">
                                        <a href="${e_files.file_url}" class="btn btn-primary btn-download table-buttons-update" download="${e_files.file_name}" style="width: 100px; text-align: center; max-height: 38px;">Download</a>
                                    </div>
                                </div>
                                
                                
                            </div>
                            
                        </td>
                    </tr>
                `;
            });
            // Add event listeners for delete buttons
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    var id = this.getAttribute('data-id');
                    deleteFile(id);
                });
            });
        }

        function sortData(column, direction) {
            allDataFiles.sort(function(a, b) {
                let aValue = column === 'file_id' ? parseInt(a[column], 10) : a[column];
                let bValue = column === 'file_id' ? parseInt(b[column], 10) : b[column];

                if (aValue < bValue) {
                    return direction === 'asc' ? -1 : 1;
                } else if (aValue > bValue) {
                    return direction === 'asc' ? 1 : -1;
                } else {
                    return 0;
                }
            });
        }

        function sortTable(column) {
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'asc';
            }

            // Sort allDataFiles based on the selected column and direction
            sortData(sortColumn, sortDirection);

            renderTable(); // Re-render the table with the sorted data
            updatePaginationControls(); // Update pagination
        }

        function deleteFile(id) {
            var formData = new FormData();
            formData.append('delete_file', 'true');
            formData.append('fileid', id);

            fetch('adminpapers.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log('Response from server:', data);

                var message_filesDiv = document.getElementById('papertable');
                if (data.includes('success')) {
                    message_filesDiv.innerHTML += '<p class="text-success">File deleted successfully.</p>';
                    // Optionally, remove the deleted file row from the table
                    document.querySelector('button[data-id="' + id + '"]').closest('tr').remove();
                    // Refresh the data to reflect changes
                    allDataFiles = allDataFiles.filter(file => file.file_id != id);
                    renderTable();
                    updatePaginationControls();
                } else {
                    message_filesDiv.innerHTML += '<p class="text-danger">Failed to delete file.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
        }

        function updatePaginationControls() {
            // var totalPages = Math.ceil(allDataFiles.length / itemsPerPage);
            // document.getElementById('page-info').textContent = `Page ${currentPage} of ${totalPages}`;

            // document.getElementById('prev-page').disabled = currentPage === 1;
            // document.getElementById('next-page').disabled = currentPage === totalPages;
            var totalPages = Math.ceil(allDataFiles.length / itemsPerPage);
            var paginationControls = document.getElementById('pagination-controls');
            paginationControls.innerHTML = '';

            for (var i = 1; i <= totalPages; i++) {
                var li = document.createElement('li');
                li.classList.add('page-item');
                if (i === currentPage) {
                    li.classList.add('active');
                }
                
                var button = document.createElement('button');
                button.textContent = i;
                button.classList.add('page-link');
                button.addEventListener('click', function() {
                    currentPage = parseInt(this.textContent);
                    renderTable();
                    updatePaginationControls();
                });

                li.appendChild(button);
                paginationControls.appendChild(li);
            }

            // Inline Style to the direct descendant of the active class
            var activeElement = document.querySelector('.page-item.active');
            if (activeElement) {
                var directDescendant = activeElement.querySelector('.page-link');
                if (directDescendant) {
                    directDescendant.style.backgroundColor = '#6c757d';
                }
            }
        }

        function prevPage() {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
                updatePaginationControls();
            }
        }

        function nextPage() {
            var totalPages = Math.ceil(allDataFiles.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
                updatePaginationControls();
            }
        }
    </script> -->
    
    <!-- Without Pagination for papertable -->
    <!-- <script>
        var soruce_files = new EventSource("../fetch_papers_files.php");
        soruce_files.onmessage = function(event_files) {
            // document.getElementById("result").innerHTML += event.data + "<br>";
            var arrayData_files = JSON.parse(event_files.data);
            console.log(arrayData_files);
            var dataContainer_files = document.querySelector('tbody#papertable')
            dataContainer_files.innerHTML = ''
            arrayData_files.forEach(e_files => {
                dataContainer_files.innerHTML +=`
                <tr>
                    <td>${e_files.id}</td>
                    <td>${e_files.file_name}</td>
                    <td>${e_files.uploaded_at}</td>
                    <td>
                        <button class="btn btn-danger btn-delete" name="delete_category" data-id="${e_files.id}">Delete</button>
                    </td>
                </tr>
                `;
            });
        }
    </script> -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('tbody#result').addEventListener('click', function(event) {
                if (event.target.classList.contains('btn-delete')) {
                    var categoryId = event.target.getAttribute('data-id');
                    deleteCategory(categoryId);
                }
            });
        });

        function deleteCategory(categoryId) {
            var formData = new FormData();
            formData.append('delete_category', true);
            formData.append('categoryid', categoryId);

            fetch('adminpapers.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log('Response from server:', data);

                var messageDiv = document.getElementById('result');
                if (data.includes('success')) {
                    messageDiv.innerHTML += '<p class="text-success">Category deleted successfully.</p>';
                    // Optionally, remove the deleted category row from the table
                    document.querySelector('button[data-id="' + categoryId + '"]').closest('tr').remove();
                } else {
                    messageDiv.innerHTML += '<p class="text-danger">Failed to delete category.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('tbody#papertable').addEventListener('click', function(event) {
                console.log('click');
                if (event.target.classList.contains('btn-delete')) {
                    var paperId = event.target.getAttribute('data-id');
                    deletePaper(paperId);
                }
            });
        });

        function deletePaper(paperId) {
            var formData = new FormData();
            formData.append('delete_file', true);
            formData.append('fileid', paperId);

            fetch('adminpapers.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log('Response from server:', data);

                var messageDiv = document.getElementById('result');
                if (data.includes('success')) {
                    messageDiv.innerHTML += '<p class="text-success">Category deleted successfully.</p>';
                    // Optionally, remove the deleted category row from the table
                    document.querySelector('button[data-id="' + paperId + '"]').closest('tr').remove();
                } else {
                    messageDiv.innerHTML += '<p class="text-danger">Failed to delete category.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
        }
    </script>
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
                    url: 'search/search_papers.php',
                    type: 'POST',
                    data: { 
                        page: page, 
                        query: query, 
                        sort_column: sortColumn, 
                        sort_order: sortOrder 
                    },
                    success: function(response) {
                        $('tbody#papertable').html(response); // Update table body with data
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

    <?php include 'includes/footer.php'; ?>