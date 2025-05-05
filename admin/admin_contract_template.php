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

    if (isset($_POST['add_contract'])) {
        $userId = trim(htmlspecialchars($_POST['adminuserid']));
        $tenantId = trim(htmlspecialchars($_POST['tenantid']));

        // $lessorwitness = trim(htmlspecialchars($_POST['lessorwitness']));
        $lessorwitnessfname = trim(htmlspecialchars($_POST['lessorwitnessfname']));
        $lessorwitnessmname = trim(htmlspecialchars($_POST['lessorwitnessmname']));
        $lessorwitnesslname = trim(htmlspecialchars($_POST['lessorwitnesslname']));

        $id_ctc_input = trim(htmlspecialchars($_POST['id/ctc']));
        $idtype = trim(htmlspecialchars($_POST['idtype']));
        $date_issued = trim(htmlspecialchars($_POST['dateissued']));
        $expiration_of_id = trim(htmlspecialchars($_POST['expirationofid']));

        // $tenantaddressinput = trim(htmlspecialchars($_POST['tenantaddress-input']));
        $apartmentaddressinput = trim(htmlspecialchars($_POST['apartmentaddress-input']));
        $datestart = trim(htmlspecialchars($_POST['datestart']));
        $expirationdate = trim(htmlspecialchars($_POST['expirationdate']));
        $signatureData = $_POST['signature'];
        $signatureData2 = $_POST['signature2'];
        $deposit = trim(htmlspecialchars($_POST['deposit']));
        $rentprice = trim(htmlspecialchars($_POST['rentprice']));

        if (!preg_match('/^[A-Za-z]{3,}$/', $lessorwitnessfname) || !preg_match('/^[A-Za-z]{3,}$/', $lessorwitnessmname) || !preg_match('/^[A-Za-z]{3,}$/', $lessorwitnesslname)) {
            $_SESSION['error_message'] = "Letters only and at least 3 letters long";
            header("Location: admin_contract_template.php?error");
            exit();
        }

        // Validate that deposit is a number (no letters or special characters)
        if (!preg_match("/^\d+$/", $deposit)) {
            $_SESSION['error_message'] = "Deposit must be a valid number.";
            header("Location: admin_contract_template.php?error=invalid_deposit");
            exit();
        }
        if (!preg_match("/^\d+$/", $rentprice)) {
            $_SESSION['error_message'] = "Rent Price must be a valid number.";
            header("Location: admin_contract_template.php?error=invalid_rentprice");
            exit();
        }

        // Convert the date to a DateTime object for reliable parsing
        $date = DateTime::createFromFormat('Y-m-d', $datestart);
        
        if ($date) {
            // Extract the day
            $day = $date->format('j');
        
            // Function to get the ordinal suffix (st, nd, rd, th)
            function getDayWithSuffix($day) {
                if (!in_array(($day % 100), [11, 12, 13])) {
                    switch ($day % 10) {
                        case 1: return $day . 'st';
                        case 2: return $day . 'nd';
                        case 3: return $day . 'rd';
                    }
                }
                return $day . 'th';
            }
        
            // Get the formatted day with suffix
            $formattedDay = getDayWithSuffix($day);
        }

        // Query the users table to retrieve first, middle, and last names
        $query = "SELECT firstname, middlename, lastname FROM users WHERE id = ? AND role = 'admin'";
        $stmt = $admin->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($firstname, $middlename, $lastname);
        $stmt->fetch();
        $stmt->close();

        // Concatenate names to create the full adminusername
        $adminusername = trim("$firstname $middlename $lastname");

        // Query the tenants table to retrieve first, middle, and last names
        $tenantquery = "SELECT fname, mname, lname FROM tenants WHERE id = ?";
        $tenantstmt = $admin->conn->prepare($tenantquery);
        $tenantstmt->bind_param("i", $tenantId);
        $tenantstmt->execute();
        $tenantstmt->bind_result($tenantfirstname, $tenantmiddlename, $tenantlastname);
        $tenantstmt->fetch();
        $tenantstmt->close();

        // Concatenate names to create the full tenantusername
        $tenantusername = trim("$tenantfirstname $tenantmiddlename $tenantlastname");

        // Validate username to allow only letters, numbers, underscores, and apostrophes
        if (!preg_match("/^[a-zA-Z0-9_' ]+$/", $adminusername) || !preg_match("/^[a-zA-Z0-9_' ]+$/", $tenantusername)) {
            // Invalid tenantname
            $_SESSION['error_message'] = "Input can only contain letters, numbers, underscores, and spaces.";
            header("Location: admin_contract_template.php?error=invalid_username");
            exit();
        }

        // Allow up to 30 characters
        if (!preg_match('/^.{1,70}$/', trim($apartmentaddressinput))) {
            // Address is valid, process it
            $_SESSION['error_message'] = "Address can only have up to 70 characters";
            header("Location: admin_contract_template.php?error=invalid_address");
            exit();
        }

        $added = $admin->addContract($adminusername, $lessorwitnessfname, $lessorwitnessmname, $lessorwitnesslname, $tenantusername, $signatureData, $signatureData2, $datestart, $expirationdate, $formattedDay, $deposit, $tenantId,  
        $apartmentaddressinput, $rentprice, $id_ctc_input, $idtype, $date_issued, $expiration_of_id);
        if($added) {
            // Contract added successfully, you can display a success message here if needed
            // echo "Contract added successfully.";
            header("Location: admin_contract_template.php?contract_added=1");
            exit();
        } else {
            // Error occurred while adding contract, display an error message or handle as needed
            // echo "Error occurred while adding contract.";
            if(empty($_SESSION['error_message'])) {
                $_SESSION['error_message'] = "Addition Failed due to an error";
            }
            header("Location: admin_contract_template.php?error=add");
            exit();
        }
    }

    if (isset($_POST['delete_contract'])) {
        // Get the contract ID owning the contract to be deleted
        $contractid = $_POST['contractid'];
        // Call the deleteContract method to delete the contract
        $deleted = $admin->deleteContract($contractid);
        if($deleted) {
            header("Location: admin_contract_template.php?contract_deleted=1");
            exit();
        } else {
            if(empty($_SESSION['error_message'])) {
                $_SESSION['error_message'] = "Deletion Failed due to an error";
            }
            header("Location: admin_contract_template.php?error=delete");
            exit();
        }
    }

    // if (isset($_POST['upload_contract_file'])) {
    //     $uploadtenantid = $_POST['uploadtenantid'];
    //     $uploaddatestart = $_POST['uploaddatestart'];
    //     $uploadexpirationdate = $_POST['uploadexpirationdate'];
        
    //     // Check if a file was uploaded
    //     if (isset($_FILES['contractFile']) && $_FILES['contractFile']['error'] == 0) {
    //         $fileData = [
    //             'tmp_name' => $_FILES['contractFile']['tmp_name'],
    //             'name' => $_FILES['contractFile']['name'],
    //             'size' => $_FILES['contractFile']['size'],
    //             'type' => $_FILES['contractFile']['type'],
    //             'error' => $_FILES['contractFile']['error']
    //         ];

    //         // Pass the file data and other parameters to the function
    //         $uploaded = $admin->uploadContract($uploadtenantid, $uploaddatestart, $uploadexpirationdate, $fileData);

    //         if ($uploaded) {
    //             header("Location: admin_contract_template.php?contract_upload=1");
    //             exit();
    //         } else {
    //             if(empty($_SESSION['error_message'])) {
    //                 $_SESSION['error_message'] = "Upload Failed due to an error.";
    //             }
    //         }
    //     } else {
    //         if(empty($_SESSION['error_message'])) {
    //             $_SESSION['error_message'] = "No file uploaded or file upload error.";
    //         }
    //     }
    //     // header("Location: admin_contract_template.php?error=upload");
    //     // exit();
    // }

    if (isset($_POST['upload_contract_file'])) {
        $uploadtenantid = $_POST['uploadtenantid'];
        $uploaddatestart = $_POST['uploaddatestart'];
        $uploadexpirationdate = $_POST['uploadexpirationdate'];
        
        // Check if contractImages[] was uploaded
        $contractImages = [];
        if (!empty($_FILES['contractImages']['name'][0])) {
            foreach ($_FILES['contractImages']['name'] as $key => $imageName) {
                $contractImages[] = [
                    'tmp_name' => $_FILES['contractImages']['tmp_name'][$key],
                    'name' => $_FILES['contractImages']['name'][$key],
                    'size' => $_FILES['contractImages']['size'][$key],
                    'type' => $_FILES['contractImages']['type'][$key],
                    'error' => $_FILES['contractImages']['error'][$key]
                ];
            }
        }
    
        // Call the function and pass the file and images
        $uploaded = $admin->uploadContract(
            $uploadtenantid, 
            $uploaddatestart, 
            $uploadexpirationdate, 
            $contractImages // Pass the images array
        );
    
        if ($uploaded) {
            header("Location: admin_contract_template.php?contract_upload=1");
            exit();
        } else {
            $_SESSION['error_message'] = $_SESSION['error_message'] ?? "Upload failed due to an error.";
        }
    }
    

    // Physical Contracts
    if (isset($_POST['delete_physicalcontract'])) {
        // Get the contract ID owning the contract to be deleted
        $physicalcontractid = $_POST['physicalcontractid'];
        
        $deleted = $admin->deletePhysicalContract($physicalcontractid);
        if($deleted) {
            header("Location: admin_contract_template.php?physicalcontract_deleted=1");
            exit();
        } else {
            if(empty($_SESSION['error_message'])) {
                $_SESSION['error_message'] = "Deletion Failed due to an error";
            }
            header("Location: admin_contract_template.php?error=delete");
            exit();
        }
    }

    if (isset($_POST['print_data'])) {
        $query = "SELECT * FROM tenants"; // Replace with your SQL query
        $result = $admin->conn->query($query);

        // Start HTML for the printable page
        echo "<html>";
        echo "<head>";
        echo "<title>Printable Data</title>";
        echo "<style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    border: 1px solid black;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                }
            </style>";
        echo "</head>";
        echo "<body>";

        // Display the data in a table format
        echo "<h1>Data for Printing</h1>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Price</th></tr>";

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['price']}</td></tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No data found</td></tr>";
        }

        echo "</table>";

        // Add a print button to automatically trigger printing
        echo "<script>window.print();</script>";
        echo "</body>";
        echo "</html>";
    }

    // Check if there's an error message stored in the session
    if (isset($_SESSION['error_message'])) {
        // Display the error message as an alert
        echo '<div class="alert alert-danger mb-0" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        // Unset the session variable to clear the error message
        unset($_SESSION['error_message']);
    }

    $sql = "SELECT * FROM users WHERE role = 'admin'";
    $result = $admin->conn->query($sql);

    $sql_upload = "SELECT * FROM users WHERE role = 'admin'";
    $result_upload = $admin->conn->query($sql_upload);

    $sql_tenant = "SELECT * FROM tenants";
    $result_tenant = $admin->conn->query($sql_tenant);

    $sql_tenant_upload = "SELECT * FROM tenants";
    $result_tenant_upload = $admin->conn->query($sql_tenant_upload);

    // $sql_physical = "SELECT physical_contracts.*, CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) AS full_name
    // FROM physical_contracts
    // JOIN tenants ON physical_contracts.tenantid = tenants.id";
    $sql_physical = "
    SELECT 
        physical_contracts.*, 
        CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) AS full_name, 
        GROUP_CONCAT(contract_images.image_path SEPARATOR ',') AS image_paths
    FROM physical_contracts
    JOIN tenants ON physical_contracts.tenantid = tenants.id
    LEFT JOIN contract_images ON physical_contracts.id = contract_images.physical_contract_id
    GROUP BY physical_contracts.id";
    $result_physical = $admin->conn->query($sql_physical);

    $sql_tenant_table = "SELECT * FROM contracts";
    $result_tenant_table = $admin->conn->query($sql_tenant_table);

    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page
    $page = "admincontracts";
?>
    <!-- Styles -->
    <style>
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

    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/header.php'; ?>
            <style>
                /* .wrapper {
                    position: relative;
                    width: 400px;
                    height: 200px;
                    -moz-user-select: none;
                    -webkit-user-select: none;
                    -ms-user-select: none;
                    user-select: none;
                    border: solid 1px #ddd;
                    margin: 10px auto;
                }
                .signature-pad {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width:400px;
                    height:200px;
                } */
                .wrapper {min-height:200px;border: 1px solid #000;}
                .signature-pad {position: absolute;left: 0;top: 0;width: 100%;height: 100%}
            </style>
            <div class="col main content" style="padding-top: 12px; padding-bottom: 12px; max-height: 100vh;">
                <div class="card-body" style="margin-top: 0; height:100%; max-height: 100%; overflow-y: auto; display: flex; flex-direction: column;">
                    <div class="row">
                        <div class="col-lg-12" id="tableheader">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h3 class="fw-bold">Digital Contracts</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <input type="text" id="searchBar" placeholder="Search..." class="form-control mb-3 " style="max-width: 180px;" />
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-primary float-end table-buttons-update new-contract-btn" id="new_contract"><i class="fa fa-plus"></i> New Contract</button>
                                </div>
                            </div>                            
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-12">
                            <h3 class="fw-bold">Digital Contracts</h3>
                        </div>
                    </div> -->
                    <div class="table-responsive"  id="tablelimiter">
                        <table class="table table-striped table-bordered" id="contractTable">
                            <thead>
                                <tr>
                                    <!-- <th scope="col" class="sortable-column" data-column="house_name" onclick="sortTable('house_name')" style="cursor: pointer;">Apartment Name <span class="sort-arrow" data-column="house_name"></span></th> -->
                                    <th scope="col">#</th>
                                    <th scope="col" data-column="tenantname" class="sortable-column" data-column="tenantname" style="cursor: pointer;">
                                        Tenant
                                        <span class="sort-arrow" data-column="tenantname"></span>
                                    </th>
                                    <th scope="col" data-column="tenantapproval" class="sortable-column">
                                        Status
                                        <span id="tenantapprovalSortArrow"></span>
                                    </th>
                                    <th scope="col" data-column="datestart" class="sortable-column">
                                        Contract Start
                                        <span id="datestartSortArrow"></span>
                                    </th>
                                    <th scope="col" data-column="expirationdate" class="sortable-column">
                                        Contract Expiry
                                        <span id="expirationdateSortArrow"></span>
                                    </th>
                                    <th scope="col">
                                        Preview
                                    </th>
                                    <th scope="col">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="contractTableBody">
                                <?php
                                if ($result_tenant_table->num_rows > 0) {
                                    // Output data of each row
                                    while($row_tenant_table = $result_tenant_table->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $row_tenant_table['id'] . "</th>";
                                        echo "<td>" . htmlspecialchars($row_tenant_table['tenantname']) . "</td>";
                                        echo "<td class='text-center'>" . ($row_tenant_table["tenantapproval"] === "true" ? "APPROVED" : ($row_tenant_table["tenantapproval"] === "false" ? "REJECTED" : "PENDING")) . "</td>";
                                        echo "<td>" . htmlspecialchars($row_tenant_table['datestart']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row_tenant_table['expirationdate']) . "</td>";
                                        echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
                                        echo "<div class='row justify-content-center m-0'>";
                                            echo "<div class='col-xl-6 px-2'>";
                                                // Add a form with a delete button for each record
                                                echo "<form method='POST' action='admin_contract_template.php' class='float-xl-end align-items-center' style='height:100%;'>";
                                                    echo "<input type='hidden' name='contractid' value='" . $row_tenant_table['id'] . "'>";
                                                    echo "<button type='submit' name='delete_contract' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>";
                                                echo "</form>";
                                            echo "</div>";
                                            echo "<div class='col-xl-6 px-2'>";
                                                if (!empty($row_tenant_table['fileurl'])) { // Ensure fileurl is not empty
                                                    echo "<a href='". '..' . htmlspecialchars($row_tenant_table['fileurl']) . "' download class='btn btn-success table-buttons-download justify-content-center table-buttons-update' style='width: 120px;'>Download</a>";
                                                } else {
                                                    echo "<span>No file available</span>";
                                                }
                                            echo "</div>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No contracts found</td></tr>";
                                }
                                $admin->conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-lg-12" id="tableheader">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h3 class="fw-bold">Physical Contracts</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <input type="text" id="physical_contract_searchBar" placeholder="Search..." class="form-control mb-3 " style="max-width: 180px;" />
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-primary float-end table-buttons-update" id="upload_contract"><i class="fa fa-plus"></i>Upload Contract</button>
                                </div>
                            </div>                            
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-12">
                            <h3 class="fw-bold">Physical Contracts</h3>
                        </div>
                    </div> -->
                    <!-- <div class="table-responsive"  id="tablelimiter" style="max-height: 420px;"> -->
                    <div class="table-responsive"  id="tablelimiter">
                        <table class="table table-striped table-bordered" id="physicalcontractsTable">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col" data-column="full_name" class="sortable-column" data-column="full_name" style="cursor: pointer;">
                                        Tenant
                                        <span class="sort-arrow" data-column="full_name"></span>
                                    </th>
                                    <th scope="col" data-column="datestart" class="sortable-column">
                                        Contract Start
                                        <span id="datestartSortArrow"></span>
                                    </th>
                                    <th scope="col" data-column="expirationdate" class="sortable-column">
                                        Contract Expiry
                                        <span id="expirationdateSortArrow"></span>
                                    </th>
                                    <th scope="col" data-column="fileurl" class="sortable-column">
                                        Contract Preview
                                        <!-- <span id="expirationdateSortArrow"></span> -->
                                    </th>
                                    <th scope="col">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="physicalcontractsTableBody">
                                <?php
                                // if ($result_physical->num_rows > 0) {
                                //     // Output data of each row
                                //     while($row_physical = $result_physical->fetch_assoc()) {
                                //         echo "<tr>";
                                //         echo "<th scope='row'>" . $row_physical['id'] . "</th>";
                                //         echo "<td>" . htmlspecialchars($row_physical['full_name']) . "</td>";
                                //         echo "<td>" . htmlspecialchars($row_physical['datestart']) . "</td>";
                                //         echo "<td>" . htmlspecialchars($row_physical['expirationdate']) . "</td>";
                                //         echo "<td>";
                                //             if (!empty($row_physical['fileurl'])) {
                                //                 $fileUrl = '../asset/physical_contracts/' . htmlspecialchars($row_physical['fileurl']);
                                //                 echo "<a href='#' data-bs-toggle='modal' data-bs-target='#imagePreviewModal' onclick=\"showImageModal('$fileUrl')\">";
                                //                 echo "<img src='$fileUrl' alt='Tenant Picture' class='img-fluid' style='width: 150px; height: 150px; object-fit: cover;'>";
                                //                 echo "</a>";
                                //             } else {
                                //                 echo "<img src='../asset/physical_contracts/default.png' alt='Default Picture' class='img-fluid' style='width: 150px; height: 150px; object-fit: cover;'>";
                                //             }
                                //         echo "</td>";
                                //         echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
                                //         echo "<div class='row justify-content-center m-0'>";
                                //             echo "<div class='col-xl-6 px-2'>";
                                //                 // Add a form with a delete button for each record
                                //                 echo "<form method='POST' action='admin_contract_template.php' class='float-xl-end align-items-center' style='height:100%;'>";
                                //                     echo "<input type='hidden' name='physicalcontractid' value='" . $row_physical['id'] . "'>";
                                //                     echo "<button type='submit' name='delete_physicalcontract' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>";
                                //                 echo "</form>";
                                //             echo "</div>";
                                //             echo "<div class='col-xl-6 px-2'>";
                                //                 if (!empty($row_physical['fileurl'])) { // Ensure fileurl is not empty
                                //                     echo "<a href='". '../asset/physical_contracts/' . htmlspecialchars($row_physical['fileurl']) . "' download class='btn btn-success table-buttons-download justify-content-center' style='width: 120px;'>Download</a>";
                                //                 } else {
                                //                     echo "<span>No file available</span>";
                                //                 }
                                //             echo "</div>";
                                //         echo "</div>";
                                //         echo "</td>";
                                //         echo "</tr>";
                                //     }
                                // } else {
                                //     echo "<tr><td colspan='6' class='text-center'>No contracts found</td></tr>";
                                // }
                                // $admin->conn->close();
                                if ($result_physical->num_rows > 0) {
                                    // Output data of each row
                                    while ($row_physical = $result_physical->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $row_physical['id'] . "</th>";
                                        echo "<td>" . htmlspecialchars($row_physical['full_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row_physical['datestart']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row_physical['expirationdate']) . "</td>";
                                        
                                        // Display images
                                        echo "<td>";
                                        // if (!empty($row_physical['image_paths'])) {
                                        //     $imagePaths = explode(',', $row_physical['image_paths']); // Split the concatenated string into an array
                                        //     foreach ($imagePaths as $imagePath) {
                                        //         $fileUrl = '../asset/physical_contracts/' . htmlspecialchars($imagePath);
                                        //         echo "<a href='#' data-bs-toggle='modal' data-bs-target='#imagePreviewModal' onclick=\"showImageModal('$fileUrl')\">";
                                        //         // echo "<img src='$fileUrl' alt='Contract Image' class='img-fluid' style='width: 80px; height: 80px; object-fit: cover; margin: 5px;'>";
                                        //         echo "<img src='$fileUrl' alt='Contract Image' class='img-fluid' style='width: 100px; height: 100px; object-fit: cover; margin-right: 5px;'>";
                                        //         echo "</a>";
                                        //     }
                                        // } else {
                                        //     echo "<img src='../asset/physical_contracts/default.png' alt='Default Picture' class='img-fluid' style='width: 80px; height: 80px; object-fit: cover;'>";
                                        // }
                                        $filePaths = explode(',', $row_physical['image_paths']);
                                        foreach ($filePaths as $filePath) {
                                            $fileUrl = '../asset/physical_contracts/' . htmlspecialchars($filePath);
                                            if (!empty($filePath)) {
                                                // Check file extension to determine if it's a PDF
                                                $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                                                if ($fileExtension === 'pdf') {
                                                    // Display PDF file icon
                                                    echo "<a href='#' data-bs-toggle='modal' data-bs-target='#imagePreviewModal' onclick=\"showFileModal('$fileUrl')\">";
                                                    echo "<img src='../asset/pdf-file.webp' alt='PDF File' class='img-fluid' style='width: 100px; height: 100px; object-fit: contain; margin-right: 5px;'>";
                                                    echo "</a>";
                                                } else {
                                                    // Display image file
                                                    echo "<a href='#' data-bs-toggle='modal' data-bs-target='#imagePreviewModal' onclick=\"showFileModal('$fileUrl')\">";
                                                    echo "<img src='$fileUrl' alt='Contract Image' class='img-fluid' style='width: 100px; height: 100px; object-fit: cover; margin-right: 5px;'>";
                                                    echo "</a>";
                                                }
                                            }
                                        }
                                        echo "</td>";
                                        
                                        // Actions
                                        echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
                                        echo "<div class='row justify-content-center m-0'>";
                                        echo "<div class='col-xl-6 px-2'>";
                                        // Add a form with a delete button for each record
                                        echo "<form method='POST' action='admin_contract_template.php' class='float-xl-end align-items-center' style='height:100%;'>";
                                        echo "<input type='hidden' name='physicalcontractid' value='" . $row_physical['id'] . "'>";
                                        echo "<button type='submit' name='delete_physicalcontract' class='btn btn-danger table-buttons-delete' style='width: 120px;'>Delete</button>";
                                        echo "</form>";
                                        echo "</div>";
                                        echo "<div class='col-xl-6 px-2'>";
                                        // if (!empty($row_physical['fileurl'])) { // Ensure fileurl is not empty
                                        //     echo "<a href='" . '../asset/physical_contracts/' . htmlspecialchars($row_physical['fileurl']) . "' download class='btn btn-success table-buttons-download justify-content-center' style='width: 120px;'>Download</a>";
                                        // } else {
                                        //     echo "<span>No file available</span>";
                                        // }
                                        // Add download links for each image

                                        // if (!empty($row_physical['image_paths'])) {
                                        //     foreach ($imagePaths as $imagePath) {
                                        //         $downloadUrl = '../asset/physical_contracts/' . htmlspecialchars($imagePath);
                                        //         if (!empty($imagePath)) {
                                        //             echo "<a href='$downloadUrl' download class='btn btn-success table-buttons-download justify-content-center' style='width: 120px; margin-bottom: 5px;'>Download</a><br>";
                                        //         }
                                        //     }
                                        // } else {
                                        //     echo "<span>No file available</span>";
                                        // }
                                        if (!empty($row_physical['image_paths'])) {
                                            foreach ($filePaths as $filePath) {
                                                $downloadUrl = '../asset/physical_contracts/' . htmlspecialchars($filePath);
                                                if (!empty($filePath)) {
                                                    echo "<a href='$downloadUrl' download class='btn btn-success table-buttons-download justify-content-center table-buttons-update mx-auto mx-xl-0' style='width: 120px; margin-bottom: 5px;'>Download</a><br>";
                                                }
                                            }
                                        } else {
                                            echo "<span>No file available</span>";
                                        }

                                        echo "</div>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No contracts found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- New Contract Modal -->
                <div class="modal fade" id="newContractModal" tabindex="-1" aria-labelledby="newCategoryModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #527853;">
                                <h5 class="modal-title text-white" id="newcategoryModalLabel">New Contract</h5>
                                <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="newContractForm" method="POST" action="admin_contract_template.php">
                                    <div class="mb-3">
                                        <!-- <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" required> -->
                                        <input type="hidden" id="signature" name="signature">
                                        <input type="hidden" id="signature2" name="signature2">
                                    </div>
                                    <div class="mb-3">
                                        <label for="adminuserid" class="form-label">Lessor</label>
                                        <select class="form-select" id="adminuserid" name="adminuserid" required>
                                            <?php
                                                if ($result->num_rows > 0) {
                                                    // Output options for each category
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['firstname']) . " " . htmlspecialchars($row['middlename']) . " " . htmlspecialchars($row['lastname']) . "</option>";
                                                    }
                                                } else {
                                                    echo "<option value=''>No admin found</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="tenantid" class="form-label">Lessee</label>
                                        <select class="form-select" id="tenantid" name="tenantid" required>
                                            <?php
                                                if ($result_tenant->num_rows > 0) {
                                                    echo "<option value=''>Select a Lessee</option>";
                                                    // Output options for each category
                                                    while ($row_tenant = $result_tenant->fetch_assoc()) {
                                                        echo "<option value='" . htmlspecialchars($row_tenant['id']) . "'>" . htmlspecialchars($row_tenant['fname']) . " " . htmlspecialchars($row_tenant['mname']) . " " . htmlspecialchars($row_tenant['lname']) . "</option>";
                                                    }
                                                } else {
                                                    echo "<option value=''>No tenant found</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <!-- <div class="mb-3">
                                        <label for="tenantaddress-input" class="form-label">Lessee Previous Address</label>
                                        <textarea name="tenantaddress-input" id="tenantaddress-input" class="d-block w-100" required></textarea>
                                    </div> -->
                                    <div class="mb-3">
                                        <label for="apartmentaddress-input" class="form-label">Apartment Address</label>
                                        <!-- <textarea name="apartmentaddress-input" id="apartmentaddress-input" class="d-block w-100" readonly required></textarea> -->
                                        <select class="form-select" name="apartmentaddress-input" id="apartmentaddress-input" >
                                            <option value="" id="apartmentaddress-input-optionvalue"></option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="datestart" class="form-label">Date Start</label>
                                        <input type="date" class="form-control" id="datestart" name="datestart" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="expirationdate" class="form-label">Expiration Date</label>
                                        <input type="date" class="form-control" id="expirationdate" name="expirationdate" value="" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="deposit" class="form-label">Deposit</label>
                                        <input type="text" class="form-control" id="deposit" name="deposit" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="rentprice" class="form-label">Rent Amount</label>
                                        <input type="text" class="form-control" id="rentprice" name="rentprice" readonly required>
                                    </div>
                                    <!-- <div class="mb-3 position-relative d-inline-block" style="max-width: 200px; min-height: 150px; flex: 1;"> -->
                                    <div class="mb-3 position-relative d-inline-block" style="min-height: 150px; flex: 1;">
                                        <!-- <canvas id="signature-pad" width="400" height="200" class="position-absolute" style="border: 1px solid #000; width: 100%; height: 100%;"></canvas> -->
                                        <label for="signature-pad" class="form-label">Lessor Signature</label>
                                        <div class="wrapper">
                                            <canvas id="signature-pad" class="signature-pad"></canvas>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="row justify-content-center">
                                            <div class="col-auto">
                                                <button id="clear1" class="text-white" type="button">Clear</button>
                                            </div>
                                        </div>
                                        <!-- <button id="save">Save Signature</button> -->
                                    </div>
                                    <!-- <div class="mb-3">
                                        <label for="lessorwitness" class="form-label">Lessor Witness</label>
                                        <input type="text" class="form-control" id="lessorwitness" name="lessorwitness" required>
                                    </div> -->

                                    <div class="mb-3">
                                        <label for="lessorwitnessfname" class="form-label">Lessor Witness Firstname</label>
                                        <input type="text" class="form-control" id="lessorwitnessfname" name="lessorwitnessfname" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="lessorwitnessmname" class="form-label">Lessor Witness Middlename</label>
                                        <input type="text" class="form-control" id="lessorwitnessmname" name="lessorwitnessmname" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="lessorwitnesslname" class="form-label">Lessor Witness Lastname</label>
                                        <input type="text" class="form-control" id="lessorwitnesslname" name="lessorwitnesslname" required>
                                    </div>
                                    
                                    <div class="mb-3 position-relative d-inline-block" style="min-height: 150px; flex: 1;">
                                        <label for="signature-pad-2" class="form-label">Lessor Witness's Signature</label>
                                        <div class="wrapper">
                                            <canvas id="signature-pad-2" class="signature-pad"></canvas>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="row justify-content-center">
                                            <div class="col-auto">
                                                <button id="clear2" class="text-white" type="button">Clear</button>
                                            </div>
                                        </div>
                                        <!-- <button id="clear2">Clear</button> -->
                                        <!-- <button id="save">Save Signature</button> -->
                                    </div>
                                    <!-- <div class="mb-3">
                                        <button id="clear">Clear</button>
                                    </div> -->
                                    
                                    <div class="mb-3">
                                        <label for="id-ctc" class="form-label">ID/CTC No. (Number)</label>
                                        <input type="text" class="form-control" id="id-ctc" name="id/ctc" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="idtype" class="form-label">ID Type</label>
                                        <input type="text" class="form-control" id="idtype" name="idtype" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="dateissued" class="form-label">Date Issued of ID</label>
                                        <input type="date" class="form-control" id="dateissued" name="dateissued" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="expirationofid" class="form-label">Expiration of ID</label>
                                        <input type="date" class="form-control" id="expirationofid" name="expirationofid" required>
                                    </div>

                                    <button type="submit" id="add_contract" name="add_contract" class="btn btn-primary table-buttons-update addcontract d-none">Add Contract</button>
                                    <button type="button" id="confirmAddContract" class="btn btn-primary table-buttons-update addcontract">Add Contract</button>
                                </form>
                                <!-- Confirmation Popup Modal -->
                                <div id="confirmationPopup" class="popup-overlay" style="display: none;">
                                    <div class="popup-content">
                                        <h5 class="text-white" style="background-color: #527853; padding: 16px;">Confirm Action</h5>
                                        <p style="padding: 20px;">Are you sure you want to add this contract?</p>
                                        <div class="popup-buttons" style="margin-top: 0; margin-bottom: 16px;">
                                            <button id="confirmYes" class="btn table-buttons-update text-white">Yes</button>
                                            <button id="confirmNo" class="btn table-buttons-delete text-white">No</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Upload Contract Modal -->
                <div class="modal fade" id="uploadContractModal" tabindex="-1" aria-labelledby="uploadContractModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #527853;">
                                <h5 class="modal-title text-white" id="uploadContractModalLabel">Upload Contract</h5>
                                <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="uploadContractForm" method="POST" action="admin_contract_template.php" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <!-- <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" required> -->
                                        <input type="hidden" id="signature" name="signature">
                                        <input type="hidden" id="signature2" name="signature2">
                                    </div>
                                    <div class="mb-3">
                                        <label for="uploadadminuserid" class="form-label">Lessor</label>
                                        <select class="form-select" id="uploadadminuserid" readonly>
                                            <?php
                                                if ($result_upload->num_rows > 0) {
                                                    // Output options for each category
                                                    while ($row_upload = $result_upload->fetch_assoc()) {
                                                        echo "<option value='" . htmlspecialchars($row_upload['id']) . "'>" . htmlspecialchars($row_upload['firstname']) . " " . htmlspecialchars($row_upload['middlename']) . " " . htmlspecialchars($row_upload['lastname']) . "</option>";
                                                    }
                                                } else {
                                                    echo "<option value=''>No admin found</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="uploadtenantid" class="form-label">Lessee</label>
                                        <select class="form-select" id="uploadtenantid" name="uploadtenantid" required>
                                            <?php
                                                if ($result_tenant->num_rows > 0) {
                                                    // Output options for each category
                                                    while ($row_tenant_upload = $result_tenant_upload->fetch_assoc()) {
                                                        echo "<option value='" . htmlspecialchars($row_tenant_upload['id']) . "'>" . htmlspecialchars($row_tenant_upload['fname']) . " " . htmlspecialchars($row_tenant_upload['mname']) . " " . htmlspecialchars($row_tenant_upload['lname']) . "</option>";
                                                    }
                                                } else {
                                                    echo "<option value=''>No tenant found</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="uploaddatestart" class="form-label">Date Start</label>
                                        <input type="date" class="form-control" id="uploaddatestart" name="uploaddatestart" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="uploadexpirationdate" class="form-label">Expiration Date</label>
                                        <input type="date" class="form-control" id="uploadexpirationdate" name="uploadexpirationdate" value="" required>
                                    </div>
                                    <!-- <div class="input-group mb-3">
                                        <input type="file" class="form-control" id="inputGroupFile04" name="contractFile" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                                    </div> -->
                                    <div class="mb-3">
                                        <label for="contractImages" class="form-label">Upload Contract Images</label>
                                        <input type="file" class="form-control" id="contractImages" name="contractImages[]" multiple>
                                    </div>
                                    <button type="submit" name="upload_contract_file" class="btn btn-primary table-buttons-update uploadcontract">Upload Contract</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Image Preview Modal -->
                <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" style="width: 1200px; max-width: 100%;">
                        <div class="modal-content" style="height: 1500px; max-height: 90vh; overflow-y: hidden;">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imagePreviewLabel">Image Preview</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center" style="max-height: 100%;">
                                <img id="modalImage" src="" alt="Preview" class="w-100 img-fluid" style="height: 100%; object-fit: cover;">
                                <!-- PDF Preview -->
                                <iframe id="modalPDF" src="" width="100%" height="500px" style="display: none; height: 100%;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <iframe id="printFrame" style="display: none;"></iframe>

                <!-- Modal HTML -->
                <div class="modal fade" id="contractModal" tabindex="-1" aria-labelledby="contractModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" style="width: 1200px; max-width: 100%;">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #527853;">
                                <h5 class="modal-title text-white" id="contractModalLabel">Contract PDF</h5>
                                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                                <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Embed the iframe with the PDF -->
                                <iframe src="../<?php echo $pdfUrl; ?>" id="contractIframe" width="100%" height="500px"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const tenantDropdown = document.getElementById('tenantid');
                        const apartmentAddressInput = document.getElementById('apartmentaddress-input-optionvalue');
                        const rentprice = document.getElementById('rentprice');

                        tenantDropdown.addEventListener('change', function () {
                            const tenantId = this.value;
                            console.log("tenant id: ", tenantId);

                            if (tenantId) {
                                fetch('../fetch_contracts_tenant_house_address.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: `tenant_id=${encodeURIComponent(tenantId)}`
                                })
                                .then(response => response.text())
                                .then(data => {
                                    const [address, price] = data.split(',');
                                    if (address && price) {
                                        apartmentAddressInput.innerHTML = `${address}`;
                                        apartmentAddressInput.value = `${address}`;
                                        rentprice.value = `${price}`;
                                    } else {
                                        apartmentAddressInput.innerHTML = '';
                                        apartmentAddressInput.value = '';
                                        rentprice.value = '';
                                        // alert('Address not found.');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error fetching address:', error);
                                    alert('An error occurred while fetching the address.');
                                });
                            } else {
                                apartmentAddressInput.innerHTML = '';
                            }
                        });
                    });
                </script>

                <script>
                    document.getElementById('new_contract').addEventListener('click', function () {
                        var newContractModal = new bootstrap.Modal(document.getElementById('newContractModal'), {
                            keyboard: false
                        });
                        newContractModal.show();
                    });
                    document.getElementById('upload_contract').addEventListener('click', function () {
                        var uploadContractModal = new bootstrap.Modal(document.getElementById('uploadContractModal'), {
                            keyboard: false
                        });
                        uploadContractModal.show();
                    });

                </script>

                <script>
                    document.body.addEventListener('click', function (event) {
                        if (event.target && event.target.id === 'testcontract') {
                            const testcontract_id = event.target.getAttribute("data-contid");

                            // Get the iframe element
                            var iframe = document.getElementById('contractIframe');

                            // Update the iframe's src with the testcontract_id
                            iframe.src = `${testcontract_id}`; // Adjust the URL as needed

                            var contractModal = new bootstrap.Modal(document.getElementById('contractModal'), {
                                keyboard: false
                            });
                            contractModal.show();
                        }
                    });
                </script>

                <!-- Include jQuery library -->
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <script>
                    // $(document).ready(function() {
                    //     $('#searchBar').on('input', function() {
                    //         var searchQuery = $(this).val(); // Get the value of the search input

                    //         $.ajax({
                    //             url: 'search/search_contract.php', // Path to your search PHP script
                    //             type: 'POST',
                    //             data: { query: searchQuery }, // Send the search query via POST
                    //             success: function(response) {
                    //                 $('tbody#contractTableBody').html(response); // Replace the table body with the new data
                    //             },
                    //             error: function() {
                    //                 alert("Error in search!"); // Show an error message in case of failure
                    //             }
                    //         });
                    //     });
                    // });

                    // $(document).ready(function() {
                    //     let currentSortColumn = 'id';
                    //     let currentSortOrder = 'ASC';

                    //     function fetchUsers(page = 1, query = '', sortColumn = currentSortColumn, sortOrder = currentSortOrder) {
                    //         $.ajax({
                    //             url: 'search/search_contract.php',
                    //             type: 'POST',
                    //             data: { 
                    //                 page: page, 
                    //                 query: query, 
                    //                 sort_column: sortColumn, 
                    //                 sort_order: sortOrder 
                    //             },
                    //             success: function(response) {
                    //                 $('tbody#contractTableBody').html(response); // Update table body with data
                    //             }
                    //         });
                    //     }

                    //     // Initial fetch on page load
                    //     fetchUsers();

                    //     // Search bar event
                    //     $('#searchBar').on('input', function() {
                    //         var searchQuery = $(this).val();
                    //         fetchUsers(1, searchQuery);
                    //     });

                    //     // Pagination button event
                    //     $(document).on('click', '.pagination-btn', function() {
                    //         var page = $(this).data('page');
                    //         var searchQuery = $('#searchBar').val();
                    //         fetchUsers(page, searchQuery);
                    //     });

                    //     // Column header sorting event
                    //     $('.sortable-column').on('click', function() {
                    //         let column = $(this).data('column');
                    //         currentSortOrder = (currentSortColumn === column && currentSortOrder === 'ASC') ? 'DESC' : 'ASC';
                    //         currentSortColumn = column;

                    //         // Toggle the arrow indicator directly in the column header
                    //         $('.sortable-column').each(function() {
                    //             // Check if the column header contains an arrow (↑ or ↓) and remove it
                    //             let text = $(this).text().trim();
                    //             if (text.endsWith('↑') || text.endsWith('↓')) {
                    //                 $(this).text(text.slice(0, -2));  // Remove the last two characters (arrow)
                    //             }
                    //         });

                    //         // Add the appropriate arrow to the clicked column header
                    //         let arrow = currentSortOrder === 'ASC' ? ' ↑' : ' ↓';
                    //         $(this).append(arrow);  // Append the arrow directly to the text

                    //         let searchQuery = $('#searchBar').val();
                    //         fetchUsers(1, searchQuery, currentSortColumn, currentSortOrder);
                    //     });
                    // });



                    $(document).ready(function () {
                        // Common function to fetch data for any table
                        function fetchTableData(tableId, url, page = 1, query = '', sortColumn = 'id', sortOrder = 'ASC') {
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: {
                                    page: page,
                                    query: query,
                                    sort_column: sortColumn,
                                    sort_order: sortOrder,
                                },
                                success: function (response) {
                                    $(`#${tableId} tbody`).html(response); // Update the corresponding table body
                                },
                            });
                        }

                        // Initial fetch on page load !COMMENTOUT IF SOME FUNCTIONS ARE NOT WORKING!
                        fetchTableData('contractTable', 'search/search_contract.php', 1);  // Initial fetch for contract table

                        // Table 1: Contracts Table
                        let table1SortColumn = 'id';
                        let table1SortOrder = 'ASC';
                        $('#searchBar').on('input', function () {
                            const query = $(this).val();
                            fetchTableData('contractTable', 'search/search_contract.php', 1, query, table1SortColumn, table1SortOrder);
                        });
                        $(document).on('click', '#contractTable .pagination-btn', function () {
                            const page = $(this).data('page');
                            const query = $('#searchBar').val();
                            fetchTableData('contractTable', 'search/search_contract.php', page, query, table1SortColumn, table1SortOrder);
                        });
                        $('#contractTable .sortable-column').on('click', function () {
                            const column = $(this).data('column');
                            table1SortOrder = (table1SortColumn === column && table1SortOrder === 'ASC') ? 'DESC' : 'ASC';
                            table1SortColumn = column;
                            const query = $('#searchBar').val();
                            fetchTableData('contractTable', 'search/search_contract.php', 1, query, table1SortColumn, table1SortOrder);
                        });

                        // Table 2: Physical Contracts Table
                        let table2SortColumn = 'id';
                        let table2SortOrder = 'ASC';
                        $('#physical_contract_searchBar').on('input', function () {
                            const query = $(this).val();
                            fetchTableData('physicalcontractsTable', 'search/search_physical_contract.php', 1, query, table2SortColumn, table2SortOrder);
                        });
                        $(document).on('click', '#physicalcontractsTable .pagination-btn', function () {
                            const page = $(this).data('page');
                            const query = $('#physical_contract_searchBar').val();
                            fetchTableData('physicalcontractsTable', 'search/search_physical_contract.php', page, query, table2SortColumn, table2SortOrder);
                        });
                        $('#physicalcontractsTable .sortable-column').on('click', function () {
                            const column = $(this).data('column');
                            table2SortOrder = (table2SortColumn === column && table2SortOrder === 'ASC') ? 'DESC' : 'ASC';
                            table2SortColumn = column;
                            const query = $('#physical_contract_searchBar').val();
                            fetchTableData('physicalcontractsTable', 'search/search_physical_contract.php', 1, query, table2SortColumn, table2SortOrder);
                        });
                    });
                </script>

                <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
                <script>
                    // Initialize both signature pads
                    const canvas1 = document.getElementById("signature-pad");
                    const signaturePad1 = new SignaturePad(canvas1);
                    
                    const canvas2 = document.getElementById("signature-pad-2");
                    const signaturePad2 = new SignaturePad(canvas2);

                    function resizeCanvas(canvas, signaturePad) {
                        const ratio = Math.max(window.devicePixelRatio || 1, 1);
                        canvas.width = canvas.offsetWidth * ratio;
                        canvas.height = canvas.offsetHeight * ratio;
                        canvas.getContext("2d").scale(ratio, ratio);
                        signaturePad.clear(); // Clear the canvas after resizing to prevent scaling artifacts
                    }

                    // Resize both canvases when the modal is shown and on window resize
                    const newContractModal = document.getElementById("newContractModal");
                    newContractModal.addEventListener("shown.bs.modal", () => {
                        resizeCanvas(canvas1, signaturePad1);
                        resizeCanvas(canvas2, signaturePad2);
                    });

                    window.addEventListener("resize", () => {
                        resizeCanvas(canvas1, signaturePad1);
                        resizeCanvas(canvas2, signaturePad2);
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
                    document.getElementById("clear2").addEventListener("click", () => {
                        signaturePad2.clear();
                    });

                    // Handle form submission
                    document.getElementById("newContractForm").addEventListener("submit", (event) => {
                        if (!signaturePad1.isEmpty() && !signaturePad2.isEmpty()) {
                            document.getElementById("signature").value = centerSignature(canvas1, signaturePad1);
                            document.getElementById("signature2").value = centerSignature(canvas2, signaturePad2);
                        } else {
                            event.preventDefault();
                            alert("Please provide both signatures.");
                        }
                    });
                </script>

                <!-- <script>
                    document.getElementById('confirmAddContract').addEventListener('click', function () {
                        // Show the custom confirmation popup
                        document.getElementById('confirmationPopup').style.display = 'flex';
                    });

                    document.getElementById('confirmYes').addEventListener('click', function () {
                        // User confirms action, submit the form programmatically
                        document.getElementById('newContractForm').submit();
                    });

                    document.getElementById('confirmNo').addEventListener('click', function () {
                        // User cancels action, hide the popup
                        document.getElementById('confirmationPopup').style.display = 'none';
                    });
                </script> -->


                <!-- <script>
                    // Select the form
                    const form = document.getElementById("newContractForm");

                    // Add a submit event listener to the form
                    form.addEventListener("submit", function (event) {
                        // Show a confirmation dialog
                        const confirmation = confirm("Are you sure you want to add this contract?");
                        
                        // If the user clicks 'Cancel', prevent form submission
                        if (!confirmation) {
                            event.preventDefault();
                        }
                    });
                </script> -->

                <script>
                    document.getElementById('confirmAddContract').addEventListener('click', function () {
                        // Show the custom confirmation popup
                        document.getElementById('confirmationPopup').style.display = 'flex';
                    });

                    document.getElementById('confirmYes').addEventListener('click', function () {
                        // User confirms action, submit the form programmatically
                        document.getElementById('add_contract').click();
                    });

                    document.getElementById('confirmNo').addEventListener('click', function () {
                        // User cancels action, hide the popup
                        document.getElementById('confirmationPopup').style.display = 'none';
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
                <script>
                    // function showImageModal(imageUrl) {
                    //     document.getElementById('modalImage').src = imageUrl;
                    // }
                    function showFileModal(fileUrl) {
                        var fileExtension = fileUrl.split('.').pop().toLowerCase();
                        
                        if (fileExtension === 'pdf') {
                            // If it's a PDF, show the PDF preview
                            document.getElementById('modalImage').style.display = 'none';
                            document.getElementById('modalPDF').style.display = 'block';
                            document.getElementById('modalPDF').src = fileUrl;
                        } else {
                            // If it's an image, show the image preview
                            document.getElementById('modalImage').style.display = 'block';
                            document.getElementById('modalPDF').style.display = 'none';
                            document.getElementById('modalImage').src = fileUrl;
                        }
                    }
                </script>

                <script>
                    document.body.addEventListener("click", function (event) {
                        // Check if the clicked element has the ID 'printBtn'
                        if (event.target && event.target.id === "printBtn") {
                            console.log("click");
                            // Get the data-id of the clicked button
                            const dataId = event.target.getAttribute("data-print-id");
                            // Get the hidden iframe
                            const iframe = document.getElementById("printFrame");

                            if (iframe) {
                                // Set the source of the iframe to load the printable content
                                iframe.src = "print_contract.php?id=" + encodeURIComponent(dataId);

                                // Trigger printing when the iframe loads
                                iframe.onload = function () {
                                    iframe.contentWindow.print();
                                };
                            } else {
                                console.error("Iframe with ID 'printFrame' not found.");
                            }
                        }
                    });

                </script>
            </div>
        </div>
    </div>