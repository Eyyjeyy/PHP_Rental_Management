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

    if (isset($_POST['add_archiverecord'])) {
        $add_userid = htmlspecialchars($_POST['add_archive_userid']);
        $add_archivevacancydate = htmlspecialchars($_POST['add_archive_vacancydate']);

        $added = $admin->addArchive($add_userid, $add_archivevacancydate);
        if($added) {
            header("Location: adminarchive.php?archive_added=1");
            exit();
        } else {
            if(empty($_SESSION['error_message'])) {
                $_SESSION['error_message'] = "Addition Failed due to an error";
            }
            header("Location: adminarchive.php?error=add");
            exit();
        }
    }

    if (isset($_POST['delete_archive'])) {
        $delete_archiveid = htmlspecialchars($_POST['archive_id']);

        echo $delete_archiveid;

        $deleted = $admin->deleteArchive($delete_archiveid);
        if($deleted) {
            header("Location: adminarchive.php?archive_deleted=1");
            exit();
        } else {
            if(empty($_SESSION['error_message'])) {
                $_SESSION['error_message'] = "Delete Failed due to an error";
            }
            header("Location: adminarchive.php?error=delete");
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

    $sql = "SELECT * FROM archives";
    $result = $admin->conn->query($sql);

    $userdropdownsql = "SELECT * FROM users WHERE role='user'";
    $userdropdownresult = $admin->conn->query($userdropdownsql);

    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page
    $page = "adminarchive";
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
                                    <button class="btn btn-primary float-end table-buttons-update" id="add_archive"><i class="fa fa-plus"></i>Add to Archives</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" id="tablelimiter" style="max-height: 100%;">
                        <table class="table table-striped table-bordered" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th scope="col" class="sortable-column" data-column="id">#</th>
                                    <th scope="col" class="sortable-column" data-column="username">Username</th>
                                    <th scope="col" class="sortable-column" data-column="firstname">First Name</th>
                                    <th scope="col" class="sortable-column" data-column="middlename">Middle Name</th>
                                    <th scope="col" class="sortable-column" data-column="lastname">Last Name</th>
                                    <th scope="col" class="sortable-column" data-column="password">Apartment</th>
                                    <th scope="col" class="sortable-column" data-column="email">Date of Vacate</th>
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
                                            echo "<td>" . htmlspecialchars($row['archive_users_username']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['archive_users_firstname']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['archive_users_middlename']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['archive_users_lastname']) . "</td>";
                                            // echo "<td>" . htmlspecialchars($row['archive_houses_house_name']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['archive_houses_house_name']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['vacancydate']) . "</td>";
                                            echo "<td class='justify-content-center text-center align-middle'>";
                                                echo "<div class='row justify-content-center m-0'>";
                                                    echo "<div class='col-xl-6 mt-1 px-2'>";
                                                        echo "<input type='hidden' name='user_id' value='" . $row['id'] . "'>";
                                                        echo "<button name='delete_user' id='view_archive' class='btn btn-primary float-xl-end table-buttons-update view_archive' data-id='" . $row['users_id'] . "' style='width: 120px;'>View</button>";                                                        
                                                    echo "</div>";
                                                    
                                                    echo "<div class='col-xl-6 mt-1 px-2'>";
                                                        echo "<form method='POST' action='adminarchive.php' class='float-xl-start align-items-center mb-0'>";
                                                            echo "<input type='hidden' name='archive_id' value='" . $row['id'] . "'>";
                                                            echo "<button type='submit' name='delete_archive' class='btn btn-danger table-buttons-delete' style='width: 120px;'>Delete</button>";
                                                        echo "</form>";                                                
                                                    echo "</div>";
                                                    echo "<div class='col-xl-6 mt-1 px-2'>";
                                                        echo "<button id='downloadPdf' type='button' class='btn btn-primary table-buttons-update download_archive' data-id='" . $row['users_id'] . "' style='width: 120px;'>Download</button>";                                                        
                                                    echo "</div>";
                                                    
                                                    // echo "<div class='col-xl-4 px-2'>";
                                                    //     // Add a form with a update button for each record
                                                    //     echo "<input type='hidden' name='user_id' value='" . $row['id'] . "'>";
                                                    //     echo "<button type='button' class='btn btn-primary update-user-btn table-buttons-update' data-id='" . $row['id'] . "' data-username='" . htmlspecialchars($row['archive_users_username']) . "' data-firstname= '" . htmlspecialchars($row['archive_users_firstname']) . "' data-middlename= '" . htmlspecialchars($row['archive_users_middlename']) . "' data-lastname= '" . htmlspecialchars($row['archive_users_lastname']) . "' data-password='" . htmlspecialchars($row['archive_users_password']) . "' data-email='" . htmlspecialchars($row['archive_users_email']) . "'data-number='" . htmlspecialchars($row['archive_users_phonenumber']) . "' style='width: 100px;'>Download</button>";
                                                    // echo "</div>";
                                                    // echo "<div class='col-xl-4 px-2'>";
                                                    //     echo "<input type='hidden' name='user_id' value='" . $row['id'] . "'>";
                                                    //     echo "<button type='button' class='btn btn-primary update-user-btn float-xl-start table-buttons-delete' data-id='" . $row['id'] . "' data-username='" . htmlspecialchars($row['archive_users_username']) . "' data-firstname= '" . htmlspecialchars($row['archive_users_firstname']) . "' data-middlename= '" . htmlspecialchars($row['archive_users_middlename']) . "' data-lastname= '" . htmlspecialchars($row['archive_users_lastname']) . "' data-password='" . htmlspecialchars($row['archive_users_password']) . "' data-email='" . htmlspecialchars($row['archive_users_email']) . "'data-number='" . htmlspecialchars($row['archive_users_phonenumber']) . "' style='width: 80px;'>Update</button>";
                                                    // echo "</div>";
                                                echo "</div>";
                                            echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No archived records found</td></tr>";
                                }
                                // $admin->conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- New Archive Modal -->
                <div class="modal fade" id="newArchiveModal" tabindex="-1" aria-labelledby="newArchiveModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header" style="background-color: #527853;">
                            <h5 class="modal-title text-white" id="newArchiveModalLabel">Add Archive</h5>
                            <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="newArchiveForm" method="POST" action="adminarchive.php">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="add_archive_userid" class="form-label">User</label>
                                            <select class="form-select" id="add_archive_userid" name="add_archive_userid" required>
                                                <?php
                                                if ($userdropdownresult->num_rows > 0) {
                                                    // Output data of each row
                                                    while($userdropdownrow = $userdropdownresult->fetch_assoc()) {
                                                        echo "<option value='" . $userdropdownrow['id'] . "'>" . $userdropdownrow['firstname'] . " " . $userdropdownrow['lastname'] . "</option>";
                                                    }
                                                } else {
                                                    echo "<option>No Users Found</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="add_archive_tenantname" class="form-label">Tenant</label>
                                            <input type="text" class="form-control" id="add_archive_tenantname" name="add_archive_tenantname" readonly required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="add_archive_vacancydate" class="form-label">Date of Vacancy</label>
                                            <input type="date" class="form-control" id="add_archive_vacancydate" name="add_archive_vacancydate" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="add_archiverecord" class="btn btn-primary table-buttons-update d-block mx-auto">Add Archive</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- View Archive Modal -->
                <div class="modal fade" id="viewArchiveModal" tabindex="-1" aria-labelledby="viewArchiveModalLabel" aria-hidden="true">
                    <div class="modal-dialog" style="max-width: 70%;">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #527853;">
                                <h5 class="modal-title text-white" id="viewArchiveModalLabel">View Data</h5>
                                <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="viewArchiveForm" method="POST" action="adminarchive.php">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Users</label>
                                                <!-- <h1>REMINDER: !ADD archived column to deposit and payments tables, refer to fetch_viewPaymentsdata_archive.php!</h1> -->
                                                <div class="table-responsive" id="tablelimiter" style="max-height: 100%;">
                                                    <table class="table table-striped table-bordered" style="margin-bottom: 0;">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" class="sortable-column" data-column="id">#</th>
                                                                <th scope="col" class="sortable-column" data-column="username">Username</th>
                                                                <th scope="col" class="sortable-column" data-column="firstname">First Name</th>
                                                                <th scope="col" class="sortable-column" data-column="middlename">Middle Name</th>
                                                                <th scope="col" class="sortable-column" data-column="lastname">Last Name</th>
                                                                <th scope="col" class="sortable-column" data-column="password">Phonenumber</th>
                                                                <th scope="col" class="sortable-column" data-column="email">Email</th>
                                                                <th scope="col" class="sortable-column" data-column="email">Password</th>
                                                                <!-- <th scope="col">Actions</th> -->
                                                            </tr>
                                                        </thead>
                                                        <tbody id="viewUserTable">
                                                            <?php
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="mt-5 mb-3">
                                                <label class="form-label fw-bold">Tenant</label>
                                                <div class="table-responsive" id="tablelimiter" style="max-height: 100%;">
                                                    <table class="table table-striped table-bordered" style="margin-bottom: 0;">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" class="sortable-column" data-column="id">#</th>
                                                                <th scope="col" class="sortable-column" data-column="username">Apartment</th>
                                                                <th scope="col" class="sortable-column" data-column="username">Date Registered</th>
                                                                <th scope="col" class="sortable-column" data-column="firstname">Notification Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="viewTenantTable">
                                                            <?php
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="mt-5 mb-3">
                                                <label class="form-label fw-bold">Payments</label>
                                                <div class="table-responsive" id="tablelimiter" style="max-height: 100%;">
                                                    <table class="table table-striped table-bordered" style="margin-bottom: 0;">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" class="sortable-column" data-column="id">#</th>
                                                                <th scope="col" class="sortable-column" data-column="username">Payment Type</th>
                                                                <th scope="col" class="sortable-column" data-column="username">Amount</th>
                                                                <th scope="col" class="sortable-column" data-column="firstname">Payment Date</th>
                                                                <th scope="col" class="sortable-column" data-column="firstname">Status</th>
                                                                <th scope="col" class="sortable-column" data-column="firstname">Reason</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="viewPaymentsTable">
                                                            <?php
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="mt-5 mb-3 position-relative">
                                                <label class="form-label fw-bold">Contracts</label>
                                                <div class="table-responsive" id="tablelimiter" style="overflow-x: hidden; max-width: 100%; max-height: 100%;">
                                                    <table class="table table-striped table-bordered" style="margin-bottom: 0;">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" class="sortable-column" data-column="id">#</th>
                                                                <th scope="col" class="sortable-column" data-column="username">Contract Start</th>
                                                                <th scope="col" class="sortable-column" data-column="firstname">Contract Expiry</th>
                                                                <th scope="col" class="sortable-column" data-column="firstname">Contract File</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="viewContractsTable">
                                                            <?php
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-12">
                                            <button type="submit" name="add_archiverecord" class="btn btn-primary table-buttons-update d-block mx-auto">Add Archive</button>
                                        </div> -->
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.getElementById('add_archive').addEventListener('click', function () {
                        var newArchiveModal = new bootstrap.Modal(document.getElementById('newArchiveModal'), {
                            keyboard: false
                        });
                        newArchiveModal.show();
                    });                
                </script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.4.0/purify.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <script>
                    $(document).ready(function () {
                        
                        function fetchTenantData() {
                            var userId = $('#add_archive_userid').val();
                            if (userId) {
                                $.ajax({
                                    url: '../fetch_tenants_archive.php', 
                                    method: 'POST',
                                    data: { user_id: userId },
                                    success: function (response) {
                                        // Update the input field with the tenant's name
                                        $('#add_archive_tenantname').val(response);
                                    },
                                    error: function () {
                                        alert('Failed to fetch tenant data.');
                                    }
                                });
                            } else {
                                $('#add_archive_tenantname').val('');
                            }
                        }

                        $('#add_archive_userid').on('change', fetchTenantData);

                        // Trigger the fetch logic on page load
                        fetchTenantData();
                    });
                </script>
                <script>
                    $(document).ready(function () {
                        $('.view_archive').on('click', function (event) {
                            var dataId = $(this).data('id');
                            console.log(dataId);

                            var viewArchiveModal = new bootstrap.Modal($('#viewArchiveModal')[0], {
                                keyboard: false
                            });
                            viewArchiveModal.show();
                            
                            ///////////////////
                            ///////////////////
                            // Users Table //
                            if (dataId) {
                                $.ajax({
                                    url: './fetcharchive/fetch_viewusersdata_archive.php', 
                                    method: 'POST',
                                    data: { viewData_user_id: dataId },
                                    success: function (response) {
                                        // Update the input field with the tenant's name
                                        $('#viewUserTable').html(response);
                                    },
                                    error: function () {
                                        alert('Failed to fetch tenant data.');
                                    }
                                });
                            } else {
                                $('#viewUserTable').html('');
                            }
                            
                            ///////////////////
                            ///////////////////
                            // Tenants Table //
                            if (dataId) {
                                $.ajax({
                                    url: './fetcharchive/fetch_viewTenantsdata_archive.php', 
                                    method: 'POST',
                                    data: { viewData_user_id: dataId },
                                    success: function (response) {
                                        // Update the input field with the tenant's name
                                        $('#viewTenantTable').html(response);
                                    },
                                    error: function () {
                                        alert('Failed to fetch tenant data.');
                                    }
                                });
                            } else {
                                $('#viewTenantTable').html('');
                            }

                            ///////////////////
                            ///////////////////
                            // Payments Table //
                            if (dataId) {
                                $.ajax({
                                    url: './fetcharchive/fetch_viewPaymentsdata_archive.php', 
                                    method: 'POST',
                                    data: { viewData_payment_id: dataId },
                                    success: function (response) {
                                        // Update the input field with the tenant's name
                                        $('#viewPaymentsTable').html(response);
                                    },
                                    error: function () {
                                        alert('Failed to fetch tenant data.');
                                    }
                                });
                            } else {
                                $('#viewPaymentsTable').html('');
                            }

                            ///////////////////
                            ///////////////////
                            // Contracts Table //
                            if (dataId) {
                                $.ajax({
                                    url: './fetcharchive/fetch_viewContractsdata_archive.php', 
                                    method: 'POST',
                                    data: { viewData_contract_id: dataId },
                                    success: function (response) {
                                        // Update the input field with the tenant's name
                                        $('#viewContractsTable').html(response);
                                    },
                                    error: function () {
                                        alert('Failed to fetch tenant data.');
                                    }
                                });
                            } else {
                                $('#viewContractsTable').html('');
                            }
                        });

                    });
                </script>
                
                <!-- <script>
                    document.querySelectorAll('.download_archive').forEach(button => {
                        button.addEventListener('click', function () {
                            // Show the modal first
                            var viewArchiveModal = new bootstrap.Modal(document.getElementById('viewArchiveModal'), {
                                keyboard: false
                            });
                            viewArchiveModal.show();

                            const dataId = $(this).data('id');
                            console.log(dataId);

                            // Fetch and update content based on the dataId
                            if (dataId) {
                                // Fetch Users Table
                                $.ajax({
                                    url: './fetcharchive/fetch_viewusersdata_archive.php',
                                    method: 'POST',
                                    data: { viewData_user_id: dataId },
                                    success: function (response) {
                                        $('#viewUserTable').html(response);
                                    },
                                    error: function () {
                                        alert('Failed to fetch tenant data.');
                                    }
                                });

                                // Fetch Tenants Table
                                $.ajax({
                                    url: './fetcharchive/fetch_viewTenantsdata_archive.php',
                                    method: 'POST',
                                    data: { viewData_user_id: dataId },
                                    success: function (response) {
                                        $('#viewTenantTable').html(response);
                                    },
                                    error: function () {
                                        alert('Failed to fetch tenant data.');
                                    }
                                });

                                // Fetch Payments Table
                                $.ajax({
                                    url: './fetcharchive/fetch_viewPaymentsdata_archive.php',
                                    method: 'POST',
                                    data: { viewData_payment_id: dataId },
                                    success: function (response) {
                                        $('#viewPaymentsTable').html(response);
                                    },
                                    error: function () {
                                        alert('Failed to fetch tenant data.');
                                    }
                                });

                                // Fetch Contracts Table
                                $.ajax({
                                    url: './fetcharchive/fetch_viewContractsdata_archive.php',
                                    method: 'POST',
                                    data: { viewData_contract_id: dataId },
                                    success: function (response) {
                                        $('#viewContractsTable').html(response);
                                    },
                                    error: function () {
                                        alert('Failed to fetch tenant data.');
                                    }
                                });
                            } else {
                                $('#viewUserTable, #viewTenantTable, #viewPaymentsTable, #viewContractsTable').html('');
                            }

                            // Capture the updated content of the modal for PDF download
                            const modalContent = document.querySelector('#viewArchiveModal .modal-content');

                            // Delay before capturing
                            setTimeout(() => {
                                // Use html2canvas to capture the content of the updated modal
                                html2canvas(modalContent, { scale: 2 }).then((canvas) => {
                                    const imgData = canvas.toDataURL('image/png'); // Get image data

                                    // Initialize jsPDF
                                    const { jsPDF } = window.jspdf;
                                    const pdf = new jsPDF({
                                        orientation: 'portrait',
                                        unit: 'px',
                                        format: 'a4',
                                    });

                                    // Add image to PDF
                                    const pageWidth = pdf.internal.pageSize.getWidth();
                                    const pageHeight = pdf.internal.pageSize.getHeight();
                                    const imgWidth = canvas.width;
                                    const imgHeight = canvas.height;

                                    const ratio = Math.min(pageWidth / imgWidth, pageHeight / imgHeight);
                                    const pdfWidth = imgWidth * ratio;
                                    const pdfHeight = imgHeight * ratio;

                                    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                                    pdf.save('archive-data.pdf'); // Download PDF
                                });
                            }, 500); // 500ms delay
                        });
                    });
                </script> -->

                <script>
                    document.querySelectorAll('.download_archive').forEach(button => {
                        button.addEventListener('click', function () {
                            const dataId = $(this).data('id');
                            console.log(dataId);

                            if (dataId) {
                                // Fetch Users Table
                                $.ajax({
                                    url: './fetcharchive/fetch_viewusersdata_archive.php',
                                    method: 'POST',
                                    data: { viewData_user_id: dataId },
                                    success: function (response) {
                                        $('#viewUserTable').html(response);
                                    },
                                    error: function () {
                                        alert('Failed to fetch tenant data.');
                                    }
                                });

                                // Fetch Tenants Table
                                $.ajax({
                                    url: './fetcharchive/fetch_viewTenantsdata_archive.php',
                                    method: 'POST',
                                    data: { viewData_user_id: dataId },
                                    success: function (response) {
                                        $('#viewTenantTable').html(response);
                                    },
                                    error: function () {
                                        alert('Failed to fetch tenant data.');
                                    }
                                });

                                // Fetch Payments Table
                                $.ajax({
                                    url: './fetcharchive/fetch_viewPaymentsdata_archive.php',
                                    method: 'POST',
                                    data: { viewData_payment_id: dataId },
                                    success: function (response) {
                                        $('#viewPaymentsTable').html(response);
                                    },
                                    error: function () {
                                        alert('Failed to fetch tenant data.');
                                    }
                                });

                                // Fetch Contracts Table
                                $.ajax({
                                    url: './fetcharchive/fetch_viewContractsdata_archive.php',
                                    method: 'POST',
                                    data: { viewData_contract_id: dataId },
                                    success: function (response) {
                                        $('#viewContractsTable').html(response);
                                    },
                                    error: function () {
                                        alert('Failed to fetch tenant data.');
                                    }
                                });
                            } else {
                                $('#viewUserTable, #viewTenantTable, #viewPaymentsTable, #viewContractsTable').html('');
                            }

                            // Delay before capturing
                            setTimeout(() => {
                                // Construct HTML content for PDF
                                let contentHTML = `
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="sortable-column" data-column="id">#</th>
                                                <th scope="col" class="sortable-column" data-column="username">Username</th>
                                                <th scope="col" class="sortable-column" data-column="firstname">First Name</th>
                                                <th scope="col" class="sortable-column" data-column="middlename">Middle Name</th>
                                                <th scope="col" class="sortable-column" data-column="lastname">Last Name</th>
                                                <th scope="col" class="sortable-column" data-column="password">Phonenumber</th>
                                                <th scope="col" class="sortable-column" data-column="email">Email</th>
                                                <th scope="col" class="sortable-column" data-column="email">Password</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${$('#viewUserTable').html()}
                                        </tbody>
                                    </table>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="sortable-column" data-column="id">#</th>
                                                <th scope="col" class="sortable-column" data-column="username">Apartment</th>
                                                <th scope="col" class="sortable-column" data-column="username">Date Registered</th>
                                                <th scope="col" class="sortable-column" data-column="firstname">Notification Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${$('#viewTenantTable').html()}
                                        </tbody>
                                    </table>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="sortable-column" data-column="id">#</th>
                                                <th scope="col" class="sortable-column" data-column="username">Payment Type</th>
                                                <th scope="col" class="sortable-column" data-column="username">Amount</th>
                                                <th scope="col" class="sortable-column" data-column="firstname">Payment Date</th>
                                                <th scope="col" class="sortable-column" data-column="firstname">Status</th>
                                                <th scope="col" class="sortable-column" data-column="firstname">Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${$('#viewPaymentsTable').html()}
                                        </tbody>
                                    </table>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="sortable-column" data-column="id">#</th>
                                                <th scope="col" class="sortable-column" data-column="username">Contract Start</th>
                                                <th scope="col" class="sortable-column" data-column="firstname">Contract Expiry</th>
                                                <th scope="col" class="sortable-column" data-column="firstname">Contract File</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${$('#viewContractsTable').html()}
                                        </tbody>
                                    </table>
                                `;

                                // Use jsPDF to generate PDF
                                const { jsPDF } = window.jspdf;
                                const pdf = new jsPDF({
                                    orientation: 'portrait',
                                    unit: 'px',
                                    format: 'a4',
                                });

                                pdf.html(contentHTML, {
                                    callback: function (pdf) {
                                        pdf.save('archive-data.pdf'); // Download PDF
                                    },
                                    x: 30,
                                    y: 10,
                                    width: 380, // max width of content in PDF
                                    windowWidth: 800
                                });
                            }, 500); // 500ms delay
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

    <?php include 'includes/footer.php'; ?>