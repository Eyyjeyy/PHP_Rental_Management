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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : 'N/A';
        $middleName = isset($_POST['middle_name']) ? trim($_POST['middle_name']) : 'N/A';
        $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : 'N/A';
        $contactNumber = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : 'N/A';
        $email = isset($_POST['email']) ? trim($_POST['email']) : 'example@example.com';
        $password = isset($_POST['password']) ? trim($_POST['password']) : 'password';
        $confirmPassword = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : 'password';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if(empty($_POST['email'])) {
                header("Location: profile_user.php");
                exit();
            }

            $_SESSION['error_message'] = "Invalid email format";
            header("Location: profile_user.php");
            exit();
        }
        if ($password !== $confirmPassword) {
            $_SESSION['error_message'] = "Password does not match!";
            header("Location: profile_user.php");
            exit();
        }
        if (strlen($password) < 6 && !empty($password)) {
            $_SESSION['error_message'] = "Password must be at least 6 characters long!";
            header("Location: profile_user.php");
            exit();
        }

        $updateuser = $admin->updateUserProfile($admin->session_id, $firstName, $middleName, $lastName, $contactNumber, $email, $password);
        if ($updateuser) {
            if(!isset($_SESSION['success_message'])){
                $_SESSION['success_message'] = "Profile Update Successful!";
            }
            header("Location: profile_user.php?profile_updated=1");
            exit();
        } else {
            $_SESSION['error_message'] = "Error Occurred";
            header("Location: profile_user.php");
            exit();
        }
    }

    $pageTitle = 'Profile Page';
    $userProfile = $admin->getUserProfile($admin->session_id);
?>

<?php include 'regular/includes/header_user.php'; ?>

<div class="container-fluid" style="margin-top: 200px; margin-bottom: 130px;">
    
    <div class="row">
        <div class="row mx-auto w-65 d-flex align-items-center m-0 p-0">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <p class="fs-5 mb-0 text-center" style="font-size: 1.2rem; font-weight: bold;">Profile</p>
                    </div>
                    <div class="card-body" style="background-color: #F9E8D9;">
                        <form method="POST" action="profile_user.php">
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
                            <input type="hidden" name="action" value="update_profile">
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">
                                        <p class="fs-5 fw-bold mb-0">First Name</p>
                                    </label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1" name="first_name" value="<?php echo htmlspecialchars($userProfile['firstname']); ?>">
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">
                                        <p class="fs-5 fw-bold mb-0">Middle Name</p>
                                    </label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1" name="middle_name" value="<?php echo htmlspecialchars($userProfile['middlename']); ?>">
                                </div>
                                <div class="col-12" id="lastname">
                                    <label for="exampleFormControlInput1" class="form-label">
                                        <p class="fs-5 fw-bold mb-0">Last Name</p>
                                    </label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1" name="last_name" value="<?php echo htmlspecialchars($userProfile['lastname']); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3 mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">
                                        <p class="fs-5 fw-bold mb-0">Contact Number</p>
                                    </label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1" name="contact_number" value="<?php echo htmlspecialchars($userProfile['phonenumber']); ?>">
                                </div>
                                <div class="col-12 col-md-6 mb-3 mt-3">
                                    <label for="exampleFormControlInput1" class="form-label">
                                        <p class="fs-5 fw-bold mb-0">Email</p>
                                    </label>
                                    <input type="email" class="form-control" id="exampleFormControlInput1" name="email" value="<?php echo htmlspecialchars($userProfile['email']); ?>">
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">
                                        <p class="fs-5 fw-bold mb-0">Password</p>
                                    </label>
                                    <div style="position: relative;">
                                        <input type="password" class="form-control" id="exampleFormControlInput1password" name="password" value="<?php echo htmlspecialchars($userProfile['password']); ?>">
                                        <button type="button" id="togglePassword" class="p-0" 
                                                style="position: absolute; right: 10px; top: 6.1px; border: none; background: white; cursor: pointer;"
                                                onmousedown="togglePasswordVisibility(true)" 
                                                onmouseup="togglePasswordVisibility(false)" 
                                                onmouseleave="togglePasswordVisibility(false)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"></path>
                                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <!-- <input type="password" class="form-control" id="exampleFormControlInput1" name="password" value="<?php echo htmlspecialchars($userProfile['password']); ?>"> -->
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">
                                        <p class="fs-5 fw-bold mb-0">Confirm Password</p>
                                    </label>
                                    <input type="password" class="form-control" id="exampleFormControlInput1" name="confirm_password">
                                </div>
                            </div>
                            <div class="row justify-content-center justify-content-md-end">
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary mb-3 mt-3 px-4" id="submitbtn">Update</button>
                                </div>
                            </div>
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

<script>
    function togglePasswordVisibility(isVisible) {
        const passwordInput = document.getElementById('exampleFormControlInput1password');
        passwordInput.type = isVisible ? 'text' : 'password';
    }
</script>

<?php include 'regular/includes/footer.php'; ?>