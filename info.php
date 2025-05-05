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

    // // Prepare the SQL query to select the house_id from the tenants table using the user_id
    // $sql = "SELECT house_id FROM tenants WHERE users_id = ?";
    // $stmt = $admin->conn->prepare($sql);
    // $stmt->bind_param("i", $admin->session_id);
    // $stmt->execute();
    // $tenantResult = $stmt->get_result();
    
    // if ($tenantResult->num_rows === 0) {
    //   // No matching tenant found, return null or handle as needed
    //   return null;
    // }

    // // Retrieve the house_id
    // $tenantRow = $tenantResult->fetch_assoc();
    // $houseId = $tenantRow['house_id'];
    
    // $stmt->close();

    // // Prepare the SQL query to retrieve matching records in houseaccounts table using house_id
    // $sql = "SELECT * FROM houseaccounts WHERE houses_id = ?";
    // $stmt = $admin->conn->prepare($sql);
    // $stmt->bind_param("i", $houseId);
    // $stmt->execute();
    // $houseAccountResult = $stmt->get_result();
    
    // if ($houseAccountResult->num_rows === 0) {
    //   // No matching house account found, return null or handle as needed
    //   return null;
    // }

    // // Fetch the results as an associative array
    // $houseAccounts = $houseAccountResult->fetch_assoc();
    
    // $stmt->close();

    // Fetch the house_id using users_id from tenants table
    $sql = "SELECT house_id FROM tenants WHERE users_id = ?";
    $stmt = $admin->conn->prepare($sql);
    $stmt->bind_param("i", $admin->session_id);
    $stmt->execute();
    $tenantResult = $stmt->get_result();

    if ($tenantResult->num_rows === 0) {
        // No tenant found; Set $houseAccounts to null
        $houseAccounts = null;
    } else {
        // Fetch house_id and retrieve matching records in houseaccounts
        $tenantRow = $tenantResult->fetch_assoc();
        $houseId = $tenantRow['house_id'];
        $stmt->close();

        $sql = "SELECT * FROM houseaccounts WHERE houses_id = ?";
        $stmt = $admin->conn->prepare($sql);
        $stmt->bind_param("i", $houseId);
        $stmt->execute();
        $houseAccountResult = $stmt->get_result();
        $houseAccounts = ($houseAccountResult->num_rows > 0) ? $houseAccountResult->fetch_assoc() : null;
        $stmt->close();
    }

    $pageTitle = 'Info Page';
?>

<?php include 'regular/includes/header_user.php'; ?>

<div class="container-fluid" style="margin-top: 200px; margin-bottom: 130px;">
    
    <div class="row mt-5 mb-5">
        <div class="row mx-auto w-65 d-flex align-items-center m-0 p-0">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <p class="fs-5 mb-0 text-center" style="font-size: 1.2rem; font-weight: bold;">Info</p>
                    </div>
                    <div class="card-body" style="background-color: #F9E8D9;">
                        <form method="POST" action="info.php">
                            <?php
                                if (isset($_SESSION['error_message']) && !empty($_SESSION['error_message'])) {
                                    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                                    // Unset the error message after displaying it
                                    unset($_SESSION['error_message']);
                                }
                                if (isset($_SESSION['success_message']) && !empty($_SESSION['success_message'])) {
                                    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                                    // Unset the success message after displaying it
                                    unset($_SESSION['success_message']);
                                }
                            ?>
                            <input type="hidden" name="action" value="update_info">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">
                                        <p class="fs-5 fw-bold mb-0">Address</p>
                                    </label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1" name="address" value="<?php echo htmlspecialchars($houseAccounts['elec_accname'] ?? 'N/A'); ?>">
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">
                                        <p class="fs-5 fw-bold mb-0">Meralco Account Owner Name</p>
                                    </label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1" name="first_name" value="<?php echo htmlspecialchars($houseAccounts['elec_accname'] ?? 'N/A'); ?>">
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">
                                        <p class="fs-5 fw-bold mb-0">Meralco Account Owner Number</p>
                                    </label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1" name="middle_name" value="<?php echo htmlspecialchars($houseAccounts['elec_accnum'] ?? 'N/A'); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3 mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">
                                        <p class="fs-5 fw-bold mb-0">Maynilad Account Owner Name</p>
                                    </label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1" name="contact_number" value="<?php echo htmlspecialchars($houseAccounts['water_accname'] ?? 'N/A'); ?>">
                                </div>
                                <div class="col-12 col-md-6 mb-3 mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">
                                        <p class="fs-5 fw-bold mb-0">Maynilad Account Owner Number</p>
                                    </label>
                                    <input type="email" class="form-control" id="exampleFormControlInput1" name="email" value="<?php echo htmlspecialchars($houseAccounts['water_accnum'] ?? 'N/A'); ?>">
                                </div>
                                <!-- <div class="col-12 col-md-6 mb-3 mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">
                                        <p class="fs-5 fw-bold mb-0">Gcash</p>
                                    </label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1" name="gcash" value="<?php echo htmlspecialchars($houseAccounts['gcash'] ?? 'N/A'); ?>">
                                </div> -->
                                <!-- <div class="col-12 col-md-6 mb-3 mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">
                                        <p class="fs-5 fw-bold mb-0">Bank</p>
                                    </label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1" name="bank" value="<?php echo htmlspecialchars($houseAccounts['bank'] ?? 'N/A'); ?>">
                                </div> -->
                            </div>
                            <!-- <div class="row justify-content-center justify-content-md-end">
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary mb-3 mt-3 px-4" style="background-color: #527853; border-color: #527853;">Update</button>
                                </div>
                            </div> -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

<?php include 'regular/includes/footer.php'; ?>