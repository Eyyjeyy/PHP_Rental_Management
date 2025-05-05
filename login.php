<?php 
    // Include or require the file containing the Admin class definition
    include 'admin.php';
    $admin = new Admin();
    // $admin->handleRedirect();

    // Now you can instantiate the Admin class
    // if($admin->isLoggedIn()) {
    //     header("Location: index.php");
    //     exit();
    // }
    // if($admin->isLoggedIn() && $admin->session_role == 'admin') {
    //     header("Location: admin/admindashboard.php");
    //     exit();
    // }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        if ($admin->login($username, $password)) {
            if ($admin->session_role == 'admin') {
                header("Location: admin/admindashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $_SESSION['message'] = "Invalid credentials. Please try again.";
            header("Location: login.php"); // Redirect to clear the POST data
            exit();
        }
    }

    if (isset($_POST['recovery_user']) && !empty($_POST['email'])) {
        $email = $_POST['email'];
        
        // Assuming $admin->sendOTP($email) sends the OTP
        $sentOtp = $admin->sendOTP($email);
    
        // Respond back with a success or failure message
        if ($sentOtp) {
            echo "OTP has been sent to your email.";
        } else {
            echo "Failed to send OTP. Please try again.";
        }
    
        // Stop further execution for AJAX request
        exit();
    }

    if (isset($_POST['reset_password']) && isset($_POST['otp']) && isset($_POST['newPassword']) && isset($_POST['confirmPassword'])) {
        $otp = $_POST['otp'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        // Check if new passwords match
        if ($newPassword !== $confirmPassword) {
            echo "Passwords do not match!";
            exit();
        }

        // Call your method to reset the password
        $passwordReset = $admin->resetPassword($otp, $newPassword); // Assuming you have this method

        if ($passwordReset) {
            echo "Password has been reset successfully.";
        } else {
            echo "Failed to reset password. Please try again.";
        }

        // Stop further execution for AJAX request
        exit();
    }
    

    if ($admin->isLoggedIn()) {
        if ($admin->session_role == 'admin') {
            header("Location: admin/admindashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        header("Location: register.php");
        exit();
    }

    // Set the title for this page
    $pageTitle = "Login Page"; // Change this according to the current page
?>

    <?php include 'regular/includes/header.php'; ?>
    <div class="container-fluid">
        <div class="row" style="min-height: 100vh;">
            <div class="col-7 p-0">
             
                <div class="div-col1">
                    <!-- <img src="asset/Renttrack pro.png" style="object-fit: cover; width: 10%;" class="position-absolute p-0 img-fluid align-items-center" alt="..."> -->

                    <div id="carouselExampleControls" class="carousel slide align-self-center" data-bs-ride="">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="asset/house_1_test.jpg" style="object-fit: cover; height: 500px; width: 90%;" class="mx-auto d-block" alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="asset/house_1_test.jpg" style="object-fit: cover; height: 500px; width: 90%;" class="mx-auto d-block" alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="asset/house_1_test.jpg" style="object-fit: cover; height: 500px; width: 90%;" class="mx-auto d-block" alt="...">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>

                    <!-- <img src="asset/blackbasin-warm-5987.jpg" style="object-fit: cover; width: 95%; height: 75%;" class="p-0 img-fluid align-self-center" alt="..."> -->
                </div>
            </div>
            <div class="div-col2">
                <div class="d-flex justify-content-center position-absolute" style="margin-bottom: 70vh;">
                    <img src="asset/Renttrack pro logo.png" class="img-fluid" alt="...">
                </div>
                <div class="login-panel panel panel-primary flex-column justify-content-center align-self-center mx-auto">
                    <div class="d-flex justify-content-center">
                        <p class="header">
                            Log In
                        </p>
                    </div>
                    <div class="d-flex panel-body justify-content-center align-self-center">
                        <form method="POST" action="login.php" class="d-flex flex-column align-items-center">
                            <div class="form-group userform mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" style="color: #9b9b9b75;" width="20" height="20" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                                </svg>
                                <input class="form-control user" placeholder="Username" type="text" name="username">
                            </div>
                            <div class="form-group userform mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" style="color: #9b9b9b75;" width="20" height="20" fill="currentColor" class="bi bi-key-fill" viewBox="0 0 16 16">
                                    <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2M2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                                </svg>
                                <input class="form-control password" placeholder="Password" type="password" name="password">
                            </div>
                            <div class="row w-100">
                                <div class="col-sm-12 col-md-6 p-0 text-center">
                                    <button type="submit" name="login" class="btn btn-mb btn-block text-white" style="min-width: 83px;">
                                        <span class="glyphicon glyphicon-log-in">
                                        </span>Login
                                    </button>
                                </div>
                                <div class="col-sm-12 col-md-6 text-center p-0" style="min-width: 83px;">
                                    <button type="submit" name="register" class="btn btn-mb btn-block text-white" style="min-width: 83px;">
                                        <span class="glyphicon glyphicon-log-in">
                                        </span>Register
                                    </button>
                                </div>
                            </div>
                            <div class="row w-100">
                                <div class="col-12 p-0 text-center">
                                    <a href="" class="text-decoration-none" id="recovery">Forgot Password?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Recovery Modal -->
                <div class="modal fade" id="recoveryModal" tabindex="-1" aria-labelledby="recoveryModalLabel" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog" style="margin: 0; position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #527853;">
                                <h5 class="modal-title text-white" id="recoveryModalLabel">Password Recovery</h5>
                                <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="newUserForm" method="POST" action="login.php">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Please enter your email to receive an OTP</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" name="recovery_user" class="btn btn-primary table-buttons-update">Send OTP</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password Reset Modal -->
                <div class="modal fade" id="passwordResetModal" tabindex="-1" aria-labelledby="passwordResetModalLabel" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog" style="margin: 0; position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #527853;">
                                <h5 class="modal-title text-white" id="passwordResetModalLabel">Reset Password</h5>
                                <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="passwordResetForm" method="POST" action="login.php">
                                    <div class="mb-3">
                                        <label for="otp" class="form-label">Enter OTP</label>
                                        <input type="text" class="form-control" id="otp" name="otp" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="newPassword" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" name="reset_password" class="btn btn-success">Reset Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Script Code prevents form resubmission when refreshing -->
                <!-- login page after submitting wrong credentials -->
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        if (window.history.replaceState) {
                            window.history.replaceState(null, null, window.location.href);
                        }
                    });
                </script>

                <?php
                    if(isset($_SESSION['message'])){
                        ?>
                            <div class="alert alert-info text-center">
                                <?php echo $_SESSION['message']; ?>
                            </div>
                        <?php

                        unset($_SESSION['message']);
                    }
                ?>

                <script>
                    // Show recovery modal on click
                    document.getElementById('recovery').addEventListener('click', function (event) {
                        event.preventDefault(); // Prevents the anchor link from reloading the page
                        var recoveryModal = new bootstrap.Modal(document.getElementById('recoveryModal'), {
                            keyboard: false
                        });
                        recoveryModal.show();
                    });

                    // AJAX form submission for OTP
                    document.getElementById('newUserForm').addEventListener('submit', function (event) {
                        event.preventDefault();

                        const email = document.getElementById('email').value;

                        fetch('login.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                email: email,
                                recovery_user: true
                            })
                        })
                        .then(response => response.text())
                        .then(data => {
                            // alert(data); // Display the OTP response message for debugging

                            if (data.includes("OTP has been sent to your email.")) {
                                const existingAlert = document.querySelector('#recoveryModal .modal-body .alert');
                                if (existingAlert) {
                                    existingAlert.remove();
                                }
                                // Create a new alert div
                                const alertDiv = document.createElement('div');
                                alertDiv.className = 'alert alert-success';
                                alertDiv.textContent = "OTP has been sent to your email and SMS.";
                                
                                // Append the alert to the modal body
                                const modalBody = document.querySelector('#recoveryModal .modal-body');
                                modalBody.insertBefore(alertDiv, modalBody.firstChild); // Insert at the top

                                // Hide the recovery modal after a brief moment
                                setTimeout(() => {
                                    var recoveryModal = bootstrap.Modal.getInstance(document.getElementById('recoveryModal'));
                                    if (recoveryModal) {
                                        recoveryModal.hide();
                                    }

                                    // Show the password reset modal
                                    console.log("Opening Password Reset Modal");
                                    var passwordResetModalElement = document.getElementById('passwordResetModal');
                                    var passwordResetModal = new bootstrap.Modal(passwordResetModalElement, {
                                        keyboard: false
                                    });
                                    passwordResetModal.show();
                                }, 2000); // Adjust the timeout duration as needed
                            } else {
                                const existingAlert = document.querySelector('#recoveryModal .modal-body .alert');
                                if (existingAlert) {
                                    existingAlert.remove();
                                }
                                // Optionally handle the failure case with an alert in the modal
                                const alertDiv = document.createElement('div');
                                alertDiv.className = 'alert alert-danger';
                                alertDiv.textContent = "Failed to send OTP. Please try again.";
                                
                                // Append the alert to the modal body
                                const modalBody = document.querySelector('#recoveryModal .modal-body');
                                modalBody.insertBefore(alertDiv, modalBody.firstChild); // Insert at the top
                            }
                        })
                        .catch(error => console.error('Error:', error));
                    });

                    // AJAX form submission for password reset
                    document.getElementById('passwordResetForm').addEventListener('submit', function (event) {
                        event.preventDefault();

                        const otp = document.getElementById('otp').value;
                        const newPassword = document.getElementById('newPassword').value;
                        const confirmPassword = document.getElementById('confirmPassword').value;

                        // Basic validation
                        if (newPassword !== confirmPassword) {
                            alert("Passwords do not match!");
                            return;
                        }

                        fetch('login.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                otp: otp,
                                newPassword: newPassword,
                                confirmPassword: confirmPassword,
                                reset_password: true // Indicating that this is a password reset request
                            })
                        })
                        .then(response => response.text())
                        .then(data => {
                            alert(data); // Display the password reset response message for debugging

                            // You can handle further actions based on the response
                            if (data.includes("Password has been reset successfully")) {
                                // Close the password reset modal
                                var passwordResetModal = bootstrap.Modal.getInstance(document.getElementById('passwordResetModal'));
                                if (passwordResetModal) {
                                    passwordResetModal.hide();
                                }
                                // Optionally, you can redirect or show a success message
                                // window.location.href = 'login.php'; // Redirect to login page after successful reset
                            }
                        })
                        .catch(error => console.error('Error:', error));
                    });
                </script>
            </div>
        </div>
    </div>