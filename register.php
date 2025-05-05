<?php 

    include 'admin.php';
    $admin = new Admin();

    if ($admin->isLoggedIn()) {
        if ($admin->session_role == 'admin') {
            header("Location: admin/admindashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        $username = $_POST['username'];
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'];
        $lastname = $_POST['lastname'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $phonenumber = $_POST['phonenumber'];
        // $role = $_POST['role'];

        $result = $admin->registerUser($username, $firstname, $middlename, $lastname, $password, $email, $phonenumber);
        if($result) {
            $_SESSION['message'] = "Registration Success";
            header("Location: register.php");
            exit();
        } else {
            //If session variable 'message' is not declared in registerUser function 
            if(!isset($_SESSION['message'])) {
                $_SESSION['message'] = "Registration Failed";
                header("Location: register.php");
                exit();
            }
        }
    }

    // Set the title for this page
    $pageTitle = "Register Page"; // Change this according to the current page
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
            <div class="div-col2" style="position: relative">
                <div class="d-flex justify-content-center position-absolute" style="margin-bottom: 70vh;">
                    <img src="asset/Renttrack pro logo.png" class="img-fluid register" alt="...">
                </div>
                <div class="login-panel panel panel-primary flex-column justify-content-center align-self-center mx-auto" style="margin-top: 16vh;">
                    <div class="d-flex justify-content-center">
                        <p class="header">
                            Register
                        </p>
                    </div>
                    <div class="d-flex panel-body justify-content-center align-self-center">
                        <form method="POST" action="register.php" class="d-flex flex-column align-items-center">
                            <div class="form-group userform mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" style="color: #9b9b9b75;" width="20" height="20" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                                </svg>
                                <input class="form-control user" placeholder="Username" type="text" name="username">
                            </div>
                            <div class="form-group userform mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" style="color: #9b9b9b75;" width="20" height="20" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                                </svg>
                                <input class="form-control user" placeholder="Firstname" type="text" name="firstname">
                            </div>
                            <div class="form-group userform mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" style="color: #9b9b9b75;" width="20" height="20" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                                </svg>
                                <input class="form-control user" placeholder="Middlename" type="text" name="middlename">
                            </div>
                            <div class="form-group userform mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" style="color: #9b9b9b75;" width="20" height="20" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                                </svg>
                                <input class="form-control user" placeholder="Lastname" type="text" name="lastname">
                            </div>
                            <div class="form-group userform mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" style="color: #9b9b9b75;" width="20" height="20" fill="currentColor" class="bi bi-envelope-fill" viewBox="0 0 16 16">
                                    <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414zM0 4.697v7.104l5.803-3.558zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586zm3.436-.586L16 11.801V4.697z"/>
                                </svg>
                                <input class="form-control user" placeholder="Email" type="text" name="email">
                            </div>
                            <div class="form-group userform mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" style="color: #9b9b9b75;" width="20" height="20" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                                </svg>
                                <input class="form-control user" placeholder="Phone number" type="number" name="phonenumber">
                            </div>
                            <div class="form-group userform mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" style="color: #9b9b9b75;" width="20" height="20" fill="currentColor" class="bi bi-key-fill" viewBox="0 0 16 16">
                                    <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2M2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                                </svg>
                                <input class="form-control password" placeholder="Password" type="password" name="password">
                            </div>
                            <div class="row w-100 justify-content-center">
                                <!-- <div class="col-sm-12 col-md-6 p-0 text-center">
                                    <button type="submit" name="login" class="btn btn-mb btn-primary btn-block" style="min-width: 83px;">
                                        <span class="glyphicon glyphicon-log-in">
                                        </span>Login
                                    </button>
                                    <a href="login.php" name="login" class="btn btn-mb btn-primary btn-block" style="min-width: 83px;">
                                        <span class="glyphicon glyphicon-log-in">
                                        </span>Login
                                    </a>
                                    
                                </div> -->
                                <div class="col-sm-12 col-md-6 text-center p-0" style="min-width: 83px;">
                                    <button type="submit" name="register" class="btn btn-mb btn-primary btn-block" style="min-width: 83px;">
                                        <span class="glyphicon glyphicon-log-in">
                                        </span>Register
                                    </button>
                                </div>
                            </div>
                            <div class="row w-100">
                                <div class="col-12 p-0 text-center">
                                    <a href="login.php" class="text-decoration-none" id="recovery">Already Have an Account?</a>
                                </div>
                            </div>
                        </form>
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
                            <div class="alert alert-info text-center position-absolute" style="bottom: 20px;">
                                <?php echo $_SESSION['message']; ?>
                            </div>
                        <?php

                        unset($_SESSION['message']);
                    }
                ?>
            </div>
        </div>
    </div>