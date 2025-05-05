<?php
    // session_start(); // Start the session (important for checking session variables)
    include 'admin.php';
    $admin = new Admin();
    // $admin->handleRedirect(); // Call handleRedirect to check login status and redirect

    // Removing this causes website to proceed to index.php despite isLoggedIn from admin.php returning false
    if(!$admin->isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    if($admin->isLoggedIn() && $admin->session_role == 'admin') {
        header("Location: admin/admindashboard.php");
        exit();
    }
    
    if(isset($_POST['finish_contract'])) {
        // $lesseewitness = trim(htmlspecialchars($_POST['lesseewitness']));

        $lesseewitnessfname = trim(htmlspecialchars($_POST['lesseewitnessfname']));
        $lesseewitnessmname = trim(htmlspecialchars($_POST['lesseewitnessmname']));
        $lesseewitnesslname = trim(htmlspecialchars($_POST['lesseewitnesslname']));
        $previousaddressinput = trim(htmlspecialchars($_POST['previousaddress-input']));

        $id_ctc_input = trim(htmlspecialchars($_POST['id/ctc']));
        $idtype = trim(htmlspecialchars($_POST['idtype']));
        $date_issued = trim(htmlspecialchars($_POST['dateissued']));
        $expiration_of_id = trim(htmlspecialchars($_POST['expirationofid']));

        $signatureData = $_POST['signature'];
        $signatureData2 = $_POST['signature2'];

        $loadsql = "SELECT contracts.* FROM contracts INNER JOIN tenants ON contracts.tenants_id = tenants.id
        INNER JOIN users ON tenants.users_id = users.id
        WHERE users.id = ?";
        $loadstmt = $admin->conn->prepare($loadsql);
        $loadstmt->bind_param("i", $admin->session_id);
        $loadstmt->execute();
        $loadresult = $loadstmt->get_result();
        
        if (!preg_match('/^[A-Za-z]{3,}$/', $lesseewitnessfname) || !preg_match('/^[A-Za-z]{3,}$/', $lesseewitnessmname) || !preg_match('/^[A-Za-z]{3,}$/', $lesseewitnesslname)) {
            $_SESSION['error_message'] = "Letters only and at least 3 letters long";
            header("Location: contract_user.php?error");
            exit();
        }
        // $completed = $admin->completeContract($lesseewitness, $previousaddressinput, $signatureData, $signatureData2);
        $completed = $admin->completeContract($lesseewitnessfname, $lesseewitnessmname, $lesseewitnesslname, $id_ctc_input, $idtype, $date_issued, $expiration_of_id, $previousaddressinput, $signatureData, $signatureData2);
        if ($completed) {
            header("Location: contract_user.php?contract_completed=1");
            exit();
        } else {
            if(empty($_SESSION['error_message'])) {
                $_SESSION['error_message'] = "Completion Failed due to an error";
            }
            header("Location: contract_user.php?error=complete");
            exit();
        }
    }

    if(isset($_POST['decline_contract'])) {
        // Get the contract ID to be approved
        $contractsid = $_POST['contractsid'];

        $declined = $admin->declineContract($contractsid);
        if($declined) {
            header("Location: contract_user.php?contract=declined");
            exit();
        } else {
            if(empty($_SESSION['error_message'])) {
                $_SESSION['error_message'] = "Decline Failed due to an error";
            }
            header("Location: contract_user.php?error=declined");
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

    $sql = "SELECT contracts.* 
    FROM contracts
    INNER JOIN tenants ON contracts.tenants_id = tenants.id
    INNER JOIN users ON tenants.users_id = users.id
    WHERE users.id = ?";
    $stmt = $admin->conn->prepare($sql);
    $stmt->bind_param("i", $admin->session_id);
    $stmt->execute();
    $result = $stmt->get_result();



    $sql_physical = "SELECT physical_contracts.*, tenants.fname as fname, tenants.mname as mname, tenants.lname as lname, 'true' as physical_status, contract_images.image_path as image_path
    FROM physical_contracts
    INNER JOIN tenants ON physical_contracts.tenantid = tenants.id
    INNER JOIN users ON tenants.users_id = users.id
    INNER JOIN contracts ON contracts.tenants_id = physical_contracts.tenantid
    INNER JOIN contract_images ON contract_images.physical_contract_id = physical_contracts.id
    WHERE users.id = ?";
    $stmt_physical = $admin->conn->prepare($sql_physical);
    $stmt_physical->bind_param("i", $admin->session_id);
    $stmt_physical->execute();
    $result_physical = $stmt_physical->get_result();

    $pageTitle = 'Contract Page';
?>

<?php include 'regular/includes/header_user.php'; ?>
<style>
    .wrapper {min-height:200px;border: 1px solid #000;}
    .signature-pad {position: absolute;left: 0;top: 0;width: 100%;height: 100%;}

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

<div class="container-fluid" style="margin-top: 200px; margin-bottom: 130px;">
    <div class="row">
        <div class="row mx-auto w-65 d-flex align-items-center m-0 p-0">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <p class="fs-5 mb-0 text-center" style="font-size: 1.2rem; font-weight: bold;">Contracts</p>
                    </div>
                    <div class="card-body" style="background-color: #F9F3EE;">
                        <div class="row mb-3">

                        </div>
                        <div class="table-container">
                            <table class="table table-striped table-bordered border border-5" style="background-color: #F9E8D9;">
                                <thead>
                                    <tr>
                                    <th scope="col" class="text-center">Tenant <br>&nbsp;</th>
                                        <th scope="col" class="text-center">Status <br>&nbsp;</th>
                                        <th scope="col" class="text-center">Contract <br>Start</th>
                                        <th scope="col" class="text-center">Contract <br>Expiry</th>
                                        <th scope="col" class="text-center">Contract <br>Preview</th>
                                        <th scope="col" class="text-center">Action <br>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        // Check if there are any rows in the result set
                                        if ($result->num_rows > 0) {
                                            // Output data of each row
                                            while ($row = $result->fetch_assoc()) {
                                                $tenantname = $row['tenantname'] ?? 'Tenant not Found';
                                                $tenantapproval = $row['tenantapproval'] ?? 'PENDING';
                                                $datestart = $row['datestart'] ?? 'N/A';
                                                $expirationdate = $row['expirationdate'] ?? 'N/A';
                                                // Extract the file extension and replace it with '.pdf'
                                                $fileUrl = $row["fileurl"];
                                                $pathInfo = pathinfo($fileUrl); // Get path information
                                                $pdfUrl = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.pdf';
                                                echo "<tr>";
                                                echo "<td class='text-center'>" . htmlspecialchars($tenantname) . "</td>";
                                                echo "<td class='text-center'>" . ($row["tenantapproval"] === "true" ? "APPROVED" : ($row["tenantapproval"] === "false" ? "UNAPPROVED" : "PENDING")) . "</td>"; // actual column name from your database
                                                echo "<td class='text-center'>" . htmlspecialchars($datestart) . "</td>";
                                                echo "<td class='text-center'>" . htmlspecialchars($expirationdate) . "</td>";
                                                echo "<td class='text-center'>";
                                                    echo "<a href='#' data-bs-toggle='modal' data-bs-target='#contractPreviewModal' onclick=\"showContractFileModal('" . "." . "$pdfUrl')\">";
                                                        echo "<img src='./asset/pdf-file.webp' alt='Contract Image' class='img-fluid' style='width: 100px; object-fit: cover;'>";
                                                    echo "</a>";
                                                echo "</td>";
                                                echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
                                                    echo "<div class='row justify-content-center m-0'>";
                                                        echo "<div class='col-xxl-4 px-2 pe-xxl-0'>";
                                                            // echo "<input type='hidden' name='contractsid' value='" . $row['id'] . "'>";
                                                            if ($row["tenantapproval"] !== "true" && $row["tenantapproval"] !== "false") {
                                                                echo "
                                                                <button type='button' class='btn btn-primary float-xxl-end' id='complete_contract' style='background-color: #527853;border-color: #527853;color: white;padding: 7.5px 10px;border-radius: 4px;'><i class='fa fa-plus'></i>Complete</button>";
                                                            }
                                                        echo "</div>";
                                                        if ($row["tenantapproval"] !== "true" && $row["tenantapproval"] !== "false") {
                                                            echo "<div class='col-xxl-3 px-2'>";
                                                                echo "<form method='POST' action='contract_user.php' class='align-items-center'>";
                                                                    echo "<input type='hidden' name='contractsid' value='" . $row['id'] . "'>";
                                                                    if ($row["tenantapproval"] !== "true" && $row["tenantapproval"] !== "false") {
                                                                        echo "
                                                                        <button type='submit' class='btn btn-danger mt-1 mb-1 m-xxl-0' name='decline_contract' style='background-color: #EE7214;border-color: #EE7214;color: white;padding: 7.5px 10px;border-radius: 4px;'><i class='fa fa-plus'></i>Decline</button>";
                                                                    }
                                                                echo "</form>";
                                                            echo "</div>";
                                                        }
                                                        echo "<div class='" . ($row["tenantapproval"] === "true" ? "col-12 " : "") . ($row["tenantapproval"] !== "true" ? "col-xxl-4 ps-xxl-0 " : "") . ($row["tenantapproval"] === "false" ? "col-xxl-12 " : "") . "px-2'>";
                                                            if (!empty($row['fileurl'])) { // Ensure fileurl is not empty
                                                                echo "<a href='" . '.' . htmlspecialchars($row['fileurl']) . "' download class='btn btn-success table-buttons-download " . ($row["tenantapproval"] === "true" || $row["tenantapproval"] === "false" ? "" : "float-xxl-start ") . "justify-content-center' style='width: 120px;height:41px;'>Download</a>";
                                                            } else {
                                                                echo "<span>No file available</span>";
                                                            }
                                                        echo "</div>";
                                                    echo "</div>";
                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            if ($result_physical->num_rows == 0) {
                                                echo "<tr><td colspan='6' class='text-center'>No contracts</td></tr>";
                                            }
                                            // echo "<tr><td colspan='6' class='text-center'>No contracts</td></tr>";
                                        }
                                    ?>
                                </tbody>
                                
                                <tbody>
                                    <?php
                                        // Check if there are any rows in the result set
                                        if ($result_physical->num_rows > 0) {
                                            // Output data of each row
                                            while ($row_physical = $result_physical->fetch_assoc()) {
                                                echo "<tr><td colspan='5' class='text-center pt-3 pb-3'><strong>Physical contracts</strong></td></tr>";
                                                echo "<tr>";
                                                echo "<td class='text-center'>" . ($row_physical["fname"]) . " " . ($row_physical["mname"]) . " " . ($row_physical["lname"]) . "</td>";
                                                echo "<td class='text-center'>" . ($row_physical["physical_status"] === 'true' ? 'COMPLETE' : 'N/A') . "</td>";
                                                echo "<td class='text-center'>" . ($row_physical["datestart"]) . "</td>";
                                                echo "<td class='text-center'>" . ($row_physical["expirationdate"]) . "</td>";
                                                echo "<td class='text-center'>" . "N/A" . "</td>";
                                                echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
                                                    echo "<div class='row justify-content-center m-0'>";
                                                        echo "<a href='" . './asset/physical_contracts/' . htmlspecialchars($row_physical['image_path']) . "' download class='btn btn-success table-buttons-download justify-content-center' style='width: 120px;height:41px;'>Download</a>";
                                                    echo "</div'>";
                                                echo "</td'>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            if (!$result_physical->num_rows > 0 && !$result->num_rows > 0) {
                                                // echo "<tr><td colspan='6' class='text-center'>No Physical contracts</td></tr>";
                                            }
                                            // echo "<tr><td colspan='6' class='text-center'>No contracts</td></tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Complete Contract Modal -->
<div class="modal fade" id="completeContractModal" tabindex="-1" aria-labelledby="newCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #527853;">
                <h5 class="modal-title text-white" id="newcategoryModalLabel">Complete Contract</h5>
                <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                        </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="completeContractForm" method="POST" action="contract_user.php">
                    <div class="mb-3">
                        <!-- <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required> -->
                        <input type="hidden" id="signature" name="signature">
                        <input type="hidden" id="signature2" name="signature2">
                    </div>
                    <div class="mb-3">
                        <label for="lessee">Lessee</label>
                        <input type="text" class="form-control" id="lessee" value="<?php echo $tenantname ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="previousaddress-input" class="form-label">Previous Address</label>
                        <textarea name="previousaddress-input" id="previousaddress-input" class="d-block w-100" required></textarea>
                    </div>
                    <!-- <div class="mb-3">
                        <label for="lesseewitness" class="form-label">Lessee Witness</label>
                        <input type="text" class="form-control" id="lesseewitness" name="lesseewitness" required>
                    </div> -->
                    <div class="mb-3 position-relative" style="min-height: 150px; flex: 1;">
                        <label for="signature-pad" class="form-label">Lessee Signature</label>
                        <div class="wrapper">
                            <canvas id="signature-pad" class="signature-pad"></canvas>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <button id="clear1" class="btn receipt text-white" type="button" style="color: #F9E8D9;padding: 10px;border: none;border-radius: 4px;cursor: pointer;">Clear</button>
                            </div>
                        </div>
                        <!-- <button id="clear1" style="background-color: #527853;color: #F9E8D9;padding: 10px;border: none;border-radius: 4px;cursor: pointer;">Clear</button> -->
                        <!-- <button id="save">Save Signature</button> -->
                    </div>
                    <!-- <div class="mb-3">
                        <label for="lesseewitness" class="form-label">Lessee Witness</label>
                        <input type="text" class="form-control" id="lesseewitness" name="lesseewitness" required>
                    </div> -->
                    <div class="mb-3">
                        <label for="lesseewitnessfname" class="form-label">Lessee Witness Firstname</label>
                        <input type="text" class="form-control" id="lesseewitnessfname" name="lesseewitnessfname" required>
                    </div>
                    <div class="mb-3">
                        <label for="lesseewitnessmname" class="form-label">Lessee Witness Middlename</label>
                        <input type="text" class="form-control" id="lesseewitnessmname" name="lesseewitnessmname" required>
                    </div>
                    <div class="mb-3">
                        <label for="lesseewitnesslname" class="form-label">Lessee Witness Lastname</label>
                        <input type="text" class="form-control" id="lesseewitnesslname" name="lesseewitnesslname" required>
                    </div>
                    <div class="mb-3 position-relative" style="min-height: 150px; flex: 1;">
                        <label for="signature-pad-2" class="form-label">Lessee Witness's Signature</label>
                        <div class="wrapper">
                            <canvas id="signature-pad-2" class="signature-pad"></canvas>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <button id="clear2" class="btn receipt text-white" type="button" style="color: #F9E8D9;padding: 10px;border: none;border-radius: 4px;cursor: pointer;">Clear</button>
                            </div>
                        </div>
                        <!-- <button id="clear2" style="background-color: #527853;color: #F9E8D9;padding: 10px;border: none;border-radius: 4px;cursor: pointer;">Clear</button> -->
                        <!-- <button id="save">Save Signature</button> -->
                    </div>
                    <!-- <div class="mb-3">
                        <button id="clear" style="background-color: #527853;color: #F9E8D9;padding: 10px;border: none;border-radius: 4px;cursor: pointer;">Clear</button>
                        <button id="save">Save Signature</button>
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
                    <div class="row justify-content-center">
                        <div class="col-auto">
                        <button type="submit" id="finish_contract" name="finish_contract" class="btn btn complete table-buttons-update d-none">Complete Contract</button>
                        <button type="button" id="confirmCompleteContract" class="btn btn-primary table-buttons-update addcontract" 
                        style="background-color: #527853;border-color: #527853;color: white;padding: 7.5px 10px;border-radius: 4px;">Complete Contract</button>
                        </div>
                    </div>
                    <!-- <button type="submit" name="finish_contract" class="btn btn complete table-buttons-update">Complete Contract</button> -->
                </form>
                <!-- Confirmation Popup Modal -->
                <div id="confirmationPopup" class="popup-overlay" style="display: none;">
                    <div class="popup-content">
                        <h5 class="text-white" style="background-color: #527853; padding: 16px;">Confirm Action</h5>
                        <p style="padding: 20px;">Are you sure you want to add this contract?</p>
                        <div class="popup-buttons" style="margin-top: 0; margin-bottom: 16px;">
                            <button id="confirmYes" class="btn table-buttons-update" style="background-color: #527853; color: white;">Yes</button>
                            <button id="confirmNo" class="btn table-buttons-delete" style="background-color: #EE7214; color: white;">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contract Preview Modal -->
<div class="modal fade" id="contractPreviewModal" tabindex="-1" aria-labelledby="contractPreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="width: 1200px; max-width: 100%;">
        <div class="modal-content" style="height: 1500px; max-height: 90vh; overflow-y: hidden;">
            <div class="modal-header">
                <h5 class="modal-title" id="contractPreviewLabel">Contract Preview</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                <button type="button" class="btn-svg" data-bs-dismiss="modal" aria-label="Close"> 
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body text-center" style="max-height: 100%;">
                <img id="modalImage" src="" alt="Preview" class="w-100 img-fluid" style="height: 100%; object-fit: cover;">
                <!-- PDF Preview -->
                <iframe id="modalPDF" src="" width="100%" height="500px" style="display: none; height: 100%;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('complete_contract').addEventListener('click', function () {
        var completeContractModal = new bootstrap.Modal(document.getElementById('completeContractModal'), {
            keyboard: false
        });
        completeContractModal.show();
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
    document.body.addEventListener('click', function (event) {
        if (event.target && event.target.id === 'id-ctc') {
            const idCtcInput = event.target;

            // Check if the listener is already added
            if (!idCtcInput.dataset.listenerAdded) {
                idCtcInput.addEventListener('input', function () {
                    // Replace any non-numeric characters with an empty string
                    this.value = this.value.replace(/[^0-9]/g, '');
                });

                // Mark the listener as added
                idCtcInput.dataset.listenerAdded = true;
            }
        }
    });
</script>
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
    const completeContractModal = document.getElementById("completeContractModal");
    completeContractModal.addEventListener("shown.bs.modal", () => {
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
    document.getElementById("completeContractForm").addEventListener("submit", (event) => {
        if (!signaturePad1.isEmpty() && !signaturePad2.isEmpty()) {
            document.getElementById("signature").value = centerSignature(canvas1, signaturePad1);
            document.getElementById("signature2").value = centerSignature(canvas2, signaturePad2);
        } else {
            event.preventDefault();
            alert("Please provide both signatures.");
        }
    });
</script>

<script>
    document.getElementById('confirmCompleteContract').addEventListener('click', function () {
        // Show the custom confirmation popup
        document.getElementById('confirmationPopup').style.display = 'flex';
    });

    document.getElementById('confirmYes').addEventListener('click', function () {
        // User confirms action, submit the form programmatically
        document.getElementById('finish_contract').click();
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
      url: 'fetch_unread_count.php',
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
  function fetchDelinquencyMonthMissed() {
    $.ajax({
      url: 'fetch_user_delinquency_month.php',
      method: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data && data.missed_months !== undefined) {
          $('#delinquencyCount').text(data.missed_months);
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.error("Error fetching unread messages:", textStatus, errorThrown);
      }
    });
  }

  // Run once on page load
  fetchDelinquencyMonthMissed();

  // Poll every 3 seconds
  setInterval(fetchDelinquencyMonthMissed, 3000);
</script>

<script>
    // function showImageModal(imageUrl) {
    //     document.getElementById('modalImage').src = imageUrl;
    // }
    function showContractFileModal(contractUrl) {
        var fileExtension = contractUrl.split('.').pop().toLowerCase();
        
        if (fileExtension === 'pdf') {
            // If it's a PDF, show the PDF preview
            document.getElementById('modalImage').style.display = 'none';
            document.getElementById('modalPDF').style.display = 'block';
            document.getElementById('modalPDF').src = contractUrl;
        } else {
            // If it's an image, show the image preview
            document.getElementById('modalImage').style.display = 'block';
            document.getElementById('modalPDF').style.display = 'none';
            document.getElementById('modalImage').src = contractUrl;
        }
    }
</script>

<?php include 'regular/includes/footer.php'; ?>