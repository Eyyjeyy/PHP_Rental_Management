<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- <link rel="stylesheet" href= "../asset/admin.css"> -->
    <?php if ($pageTitle == 'Chat Page'): ?>
        <link rel="stylesheet" href="asset/admin.css">
    <?php else: ?>
        <link rel="stylesheet" href="../asset/admin.css">
    <?php endif; ?>
    
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->



    <title><?php echo isset($pageTitle) ? $pageTitle : 'Default Title'; ?></title>
    
  </head>
  <body>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
   
    <!-- <div class="col sidebar text-white">
    
                <nav class="navbar navbar-expand-lg navbar-light sidebar">
               

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav d-flex flex-column">
                        <a class="navbar-brand mt-4 mb-3 py-0 justify-content-center" href="admindashboard.php">
                <img src="../asset/Renttrack pro no word_2.png" id="userpiclogo" class="img-fluid" alt="..." style="height: 50px;">
            </a>          
                        <div class="hover-container">
                            <a class="nav-link <?= $page == 'admindashboard' ? 'active' : '' ?>" href="admindashboard.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/>
                            </svg>
                                <p>Dashboard</p>
                            </a>
                       </div>
                       <div class="hover-container">
                            <a class="nav-link <?= $page == 'adminuser' ? 'active' : '' ?>" href="adminusers.php">

                            
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M12 1a1 1 0 0 1 1 1v10.755S12 11 8 11s-5 1.755-5 1.755V2a1 1 0 0 1 1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                                    <path d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                                </svg>
                                <p>Users</p>
                            </a>
                             </div>
                             <div class="hover-container">
                            <a class="nav-link <?= $page == 'adminhouses' ? 'active' : '' ?>" aria-current="page" href="adminhouses.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M3 0a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h3v-3.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V16h3a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1zm1 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5M4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5"/>
                                </svg>
                                <p>Apartments</p>
                            </a>
    </div>
    <div class="hover-container">
                            <a class="nav-link <?= $page == 'admincategories' ? 'active' : '' ?>" href="admincategories.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0"/>
                                </svg>
                                <p>Categories</p>
                            </a>
    </div>
    <div class="hover-container">
                            <a class="nav-link <?= $page == 'admintenants' ? 'active' : '' ?>" href="admintenants.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M8 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M6 6.75v8.5a.75.75 0 0 0 1.5 0V10.5a.5.5 0 0 1 1 0v4.75a.75.75 0 0 0 1.5 0v-8.5a.25.25 0 1 1 .5 0v2.5a.75.75 0 0 0 1.5 0V6.5a3 3 0 0 0-3-3H7a3 3 0 0 0-3 3v2.75a.75.75 0 0 0 1.5 0v-2.5a.25.25 0 0 1 .5 0"/>
                                </svg>
                                <p>Tenants</p>
                                <?php
                                    $users_notTenants = $admin->countUsersNotInTenants();
                                    echo "<p class='notifs fw-bold position-absolute' style='color: #F28543; right: 80px;'>" . $users_notTenants . "</p>";
                                ?>
                            </a>
    </div>
    <div class="hover-container">
                            <a class="nav-link <?= $page == 'adminpayments' ? 'active' : '' ?>" href="adminpayments.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M8.277.084a.5.5 0 0 0-.554 0l-7.5 5A.5.5 0 0 0 .5 6h1.875v7H1.5a.5.5 0 0 0 0 1h13a.5.5 0 1 0 0-1h-.875V6H15.5a.5.5 0 0 0 .277-.916zM12.375 6v7h-1.25V6zm-2.5 0v7h-1.25V6zm-2.5 0v7h-1.25V6zm-2.5 0v7h-1.25V6zM8 4a1 1 0 1 1 0-2 1 1 0 0 1 0 2M.5 15a.5.5 0 0 0 0 1h15a.5.5 0 1 0 0-1z"/>
                                </svg>
                                <p>Payments</p>
                                <?php
                                    $unapproved_payments = $admin->countPendingApprovals();
                                    echo "<p class='notifs fw-bold position-absolute' style='color: #F28543;  right: 80px;'>" . $unapproved_payments . "</p>";
                                ?>
                            </a>
    </div>
    <div class="hover-container">
                            <a class="nav-link <?= $page == 'adminpapers' ? 'active' : '' ?>" href="adminpapers.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M9.5 0a.5.5 0 0 1 .5.5.5.5 0 0 0 .5.5.5.5 0 0 1 .5.5V2a.5.5 0 0 1-.5.5h-5A.5.5 0 0 1 5 2v-.5a.5.5 0 0 1 .5-.5.5.5 0 0 0 .5-.5.5.5 0 0 1 .5-.5z"/>
                                    <path d="M3.5 1h.585A1.5 1.5 0 0 0 4 1.5V2a1.5 1.5 0 0 0 1.5 1.5h5A1.5 1.5 0 0 0 12 2v-.5q-.001-.264-.085-.5h.585A1.5 1.5 0 0 1 14 2.5v12a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-12A1.5 1.5 0 0 1 3.5 1"/>
                                </svg>
                                <p>Papers</p>
                            </a>
    </div>
    <div class="hover-container">
                            <a class="nav-link <?= $page == 'adminexpenses' ? 'active' : '' ?>" href="adminexpenses.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v2h6a.5.5 0 0 1 .5.5c0 .253.08.644.306.958.207.288.557.542 1.194.542s.987-.254 1.194-.542C9.42 6.644 9.5 6.253 9.5 6a.5.5 0 0 1 .5-.5h6v-2A1.5 1.5 0 0 0 14.5 2z"/>
                                    <path d="M16 6.5h-5.551a2.7 2.7 0 0 1-.443 1.042C9.613 8.088 8.963 8.5 8 8.5s-1.613-.412-2.006-.958A2.7 2.7 0 0 1 5.551 6.5H0v6A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5z"/>
                                </svg>
                                <p>Expenses</p>
                            </a>
    </div>
    <div class="hover-container">
                            <a class="nav-link <?= $page == 'adminchat' ? 'active' : '' ?>" href="../chat.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793zm3.5 1a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1zm0 2.5a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1zm0 2.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1z"/>
                                </svg>
                                <p>Chat</p>
                            </a>
    </div>
    <div class="hover-container">
                            <a class="nav-link <?= $page == 'adminhistory' ? 'active' : '' ?>" href="adminhistory.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                                    <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                                    <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                                </svg>
                                <p>History</p>
                            </a>
    </div>

                        </ul>
                        <ul class="navbar-nav d-flex flex-column">
                              <div class="hover-container">
                            <a class="nav-link" href="../logout.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M7.5 1v7h1V1z"/>
                                    <path d="M3 8.812a5 5 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812"/>
                                </svg>
                                <p>Logout</p>
                            </a>
                            </div>
                        </ul>
                        
                    </div>
                </nav>
            </div>

            <div class="navcontainer p-0">
            <nav class="navbar navbar-expand-lg navbar-light flex-column py-0" id="navbar" style="background-color: #527853;">
                <div class="container-fluid mb-3 mt-3" id="navbarbar">
                <div class="row mx-auto w-65 d-flex align-items-center">


                <div class="col d-flex align-items-center">
            <a class="navbar-brand py-0" href="admindashboard.php">
                <img src="../asset/Renttrack pro no word_2.png" id="userpiclogo" class="img-fluid" alt="..." style="height: 50px;">
            </a>
        </div>

        <div class="col d-flex justify-content-end" id="navnav">
        <button class="navbar-toggler" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="navbar-toggler-icon"></span>
        </button>


        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="admindashboard.php">Dashboard</a>
        <a class="dropdown-item" href="adminusers.php">Users</a>
        <a class="dropdown-item" href="adminhouses.php">Apartments</a>
        <a class="dropdown-item" href="admincategories.php">Categories</a>
        <a class="dropdown-item" href="admintenants.php">Tenants</a>
        <a class="dropdown-item" href="adminpayments.php">Payments</a>
        <a class="dropdown-item" href="adminpapers.php">Papers</a>
        <a class="dropdown-item" href="adminexpenses.php">Expenses</a>
        <a class="dropdown-item" href="../chat.php">Chat</a>
        <a class="dropdown-item" href="adminhistory.php">History</a>
        <a class="dropdown-item" href="../logout.php">Logout</a>
        </div>
    </div>
    </div>
    </nav>
    </div> -->

    <div class="col sidebar text-white">
        <nav class="navbar navbar-expand-lg navbar-light sidebar">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav d-flex flex-column">
                    <a class="navbar-brand mt-4 mb-3 py-0 justify-content-center" href="admindashboard.php">
                        <img src="../asset/Renttrack pro logo.png" id="userpiclogo" class="img-fluid" alt="..." style="height: 100px;">
                    </a>
                    <div class="hover-container">
                        <a class="nav-link <?= $page == 'admindashboard' ? 'active' : '' ?>" href="admindashboard.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"></path>
                            </svg>
                            <p>Dashboard</p>
                        </a>
                    </div>
                    <div class="hover-container">
                        <a class="nav-link <?= $page == 'adminuser' ? 'active' : '' ?>" href="adminusers.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M12 1a1 1 0 0 1 1 1v10.755S12 11 8 11s-5 1.755-5 1.755V2a1 1 0 0 1 1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"></path>
                                <path d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6"></path>
                            </svg>
                            <p>Users</p>
                        </a>
                    </div>
                    <div class="hover-container">
                        <a class="nav-link <?= $page == 'adminhouses' ? 'active' : '' ?>" aria-current="page" href="adminhouses.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M3 0a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h3v-3.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V16h3a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1zm1 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5M4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5"></path>
                            </svg>
                            <p>Apartments</p>
                        </a>
                    </div>
                    <!-- <div class="hover-container">
                        <a class="nav-link <?= $page == 'admincategories' ? 'active' : '' ?>" href="admincategories.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0"></path>
                            </svg>
                            <p>Categories</p>
                        </a>
                    </div> -->
                    <div class="hover-container">
                        <a class="nav-link <?= $page == 'admintenants' ? 'active' : '' ?>" href="admintenants.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M8 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M6 6.75v8.5a.75.75 0 0 0 1.5 0V10.5a.5.5 0 0 1 1 0v4.75a.75.75 0 0 0 1.5 0v-8.5a.25.25 0 1 1 .5 0v2.5a.75.75 0 0 0 1.5 0V6.5a3 3 0 0 0-3-3H7a3 3 0 0 0-3 3v2.75a.75.75 0 0 0 1.5 0v-2.5a.25.25 0 0 1 .5 0"></path>
                            </svg>
                            <p>Tenants</p>
                            <?php
                                $users_notTenants = $admin->countUsersNotInTenants();
                                echo "<p class='notifs fw-bold position-absolute' style='color: #F28543; right: 80px;'>" . $users_notTenants . "</p>";
                            ?>
                        </a>
                    </div>
                    <div class="hover-container">
                        <a class="nav-link <?= $page == 'adminpayments' ? 'active' : '' ?>" href="adminpayments.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M8.277.084a.5.5 0 0 0-.554 0l-7.5 5A.5.5 0 0 0 .5 6h1.875v7H1.5a.5.5 0 0 0 0 1h13a.5.5 0 1 0 0-1h-.875V6H15.5a.5.5 0 0 0 .277-.916zM12.375 6v7h-1.25V6zm-2.5 0v7h-1.25V6zm-2.5 0v7h-1.25V6zm-2.5 0v7h-1.25V6zM8 4a1 1 0 1 1 0-2 1 1 0 0 1 0 2M.5 15a.5.5 0 0 0 0 1h15a.5.5 0 1 0 0-1z"></path>
                            </svg>
                            <p>Payments</p>
                            <?php
                                $unapproved_payments = $admin->countPendingApprovals();
                                echo "<p class='notifs fw-bold position-absolute' style='color: #F28543;  right: 80px;'>" . $unapproved_payments . "</p>";
                            ?>                       
                        </a>
                    </div>
                    <div class="hover-container">
                        <a class="nav-link <?= $page == 'admindelinquency' ? 'active' : '' ?>" href="admindelinquency.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-person-fill-slash my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M13.879 10.414a2.501 2.501 0 0 0-3.465 3.465zm.707.707-3.465 3.465a2.501 2.501 0 0 0 3.465-3.465m-4.56-1.096a3.5 3.5 0 1 1 4.949 4.95 3.5 3.5 0 0 1-4.95-4.95ZM11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
                            </svg>
                            <p>Delinquency</p>
                            <?php
                            $delinquents = $admin->countDelinquents();
                            echo "<p class='notifs fw-bold position-absolute' style='color: #F28543;  right: 80px;'>" . $delinquents . "</p>";
                            ?>
                        </a>
                    </div>
                    <div class="hover-container">
                        <a class="nav-link <?= $page == 'adminpapers' ? 'active' : '' ?>" href="adminpapers.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M9.5 0a.5.5 0 0 1 .5.5.5.5 0 0 0 .5.5.5.5 0 0 1 .5.5V2a.5.5 0 0 1-.5.5h-5A.5.5 0 0 1 5 2v-.5a.5.5 0 0 1 .5-.5.5.5 0 0 0 .5-.5.5.5 0 0 1 .5-.5z"></path>
                                <path d="M3.5 1h.585A1.5 1.5 0 0 0 4 1.5V2a1.5 1.5 0 0 0 1.5 1.5h5A1.5 1.5 0 0 0 12 2v-.5q-.001-.264-.085-.5h.585A1.5 1.5 0 0 1 14 2.5v12a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-12A1.5 1.5 0 0 1 3.5 1"></path>
                            </svg>
                            <p>Papers</p>
                        </a>
                    </div>
                    <div class="hover-container">
                        <a class="nav-link <?= $page == 'admincontracts' ? 'active' : '' ?>" href="admin_contract_template.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-suitcase-lg-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M7 0a2 2 0 0 0-2 2H1.5A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14H2a.5.5 0 0 0 1 0h10a.5.5 0 0 0 1 0h.5a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2H11a2 2 0 0 0-2-2zM6 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1zM3 13V3h1v10zm9 0V3h1v10z"/>
                            </svg>
                            <p>Contracts</p>
                        </a>
                    </div>
                    <div class="hover-container">
                        <a class="nav-link <?= $page == 'adminexpenses' ? 'active' : '' ?>" href="adminexpenses.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v2h6a.5.5 0 0 1 .5.5c0 .253.08.644.306.958.207.288.557.542 1.194.542s.987-.254 1.194-.542C9.42 6.644 9.5 6.253 9.5 6a.5.5 0 0 1 .5-.5h6v-2A1.5 1.5 0 0 0 14.5 2z"></path>
                                <path d="M16 6.5h-5.551a2.7 2.7 0 0 1-.443 1.042C9.613 8.088 8.963 8.5 8 8.5s-1.613-.412-2.006-.958A2.7 2.7 0 0 1 5.551 6.5H0v6A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5z"></path>
                            </svg>
                            <p>Expenses</p>
                        </a>
                    </div>
                    <div class="hover-container">
                        <a class="nav-link <?= $page == 'adminreports' ? 'active' : '' ?>" href="adminreports.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v2h6a.5.5 0 0 1 .5.5c0 .253.08.644.306.958.207.288.557.542 1.194.542s.987-.254 1.194-.542C9.42 6.644 9.5 6.253 9.5 6a.5.5 0 0 1 .5-.5h6v-2A1.5 1.5 0 0 0 14.5 2z"></path>
                                <path d="M16 6.5h-5.551a2.7 2.7 0 0 1-.443 1.042C9.613 8.088 8.963 8.5 8 8.5s-1.613-.412-2.006-.958A2.7 2.7 0 0 1 5.551 6.5H0v6A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5z"></path>
                            </svg>
                            <p>Reports</p>
                        </a>
                    </div>
                    <div class="hover-container">
                        <a class="nav-link <?= $page == 'adminchat' ? 'active' : '' ?>" href="../chat.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793zm3.5 1a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1zm0 2.5a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1zm0 2.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1z"></path>
                            </svg>
                            <p>Chat</p>
                            <p class="notifs fw-bold position-absolute" style="color: #F28543; right: 80px;" id="unseenChatLabel"></p>
                        </a>
                    </div>
                    <div class="hover-container">
                        <a class="nav-link <?= $page == 'adminarchive' ? 'active' : '' ?>" href="adminarchive.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-file-zip-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M8.5 9.438V8.5h-1v.938a1 1 0 0 1-.03.243l-.4 1.598.93.62.93-.62-.4-1.598a1 1 0 0 1-.03-.243"/>
                                <path d="M4 0h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2m2.5 8.5v.938l-.4 1.599a1 1 0 0 0 .416 1.074l.93.62a1 1 0 0 0 1.109 0l.93-.62a1 1 0 0 0 .415-1.074l-.4-1.599V8.5a1 1 0 0 0-1-1h-1a1 1 0 0 0-1 1m1-5.5h-1v1h1v1h-1v1h1v1H9V6H8V5h1V4H8V3h1V2H8V1H6.5v1h1z"/>
                            </svg>
                            <p>Archive</p>
                        </a>
                    </div>
                    <div class="hover-container">
                        <a class="nav-link <?= $page == 'adminhistory' ? 'active' : '' ?>" href="adminhistory.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"></path>
                                <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"></path>
                                <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"></path>
                            </svg>
                            <p>History</p>
                        </a>
                    </div>
                </ul>
                <ul class="navbar-nav d-flex flex-column">
                    <div class="hover-container">
                        <a class="nav-link" href="../logout.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M7.5 1v7h1V1z"></path>
                                <path d="M3 8.812a5 5 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812"></path>
                            </svg>
                            <p>Logout</p>
                        </a>
                    </div>
                </ul>
            </div>
        </nav>
    </div>
    <div class="navcontainer p-0">
        <nav class="navbar navbar-expand-lg navbar-light flex-column py-0" id="navbar" style="background-color: #527853;">
            <div class="container-fluid mb-3 mt-3" id="navbarbar">
                <div class="row mx-auto w-65 d-flex align-items-center">
                    <div class="col d-flex align-items-center">
                        <a class="navbar-brand py-0" href="admindashboard.php">
                            <img src="../asset/Renttrack pro logo.png" id="userpiclogo" class="img-fluid" alt="..." style="height: 100px;">
                        </a>
                    </div>
                    <div class="col d-flex justify-content-end" id="navnav">
                        <button class="navbar-toggler" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="min-height: 50px; max-height: 90vh; overflow-y: auto;">
                            <a class="dropdown-item" href="admindashboard.php">Dashboard</a>
                            <a class="dropdown-item" href="adminusers.php">Users</a>
                            <a class="dropdown-item" href="adminhouses.php">Apartments</a>
                            <!-- <a class="dropdown-item" href="admincategories.php">Categories</a> -->
                            <a class="dropdown-item" href="admintenants.php">Tenants</a>
                            <a class="dropdown-item" href="adminpayments.php">Payments</a>
                            <a class="dropdown-item" href="admindelinquency.php">Delinquency</a>
                            <a class="dropdown-item" href="adminpapers.php">Papers</a>
                            <a class="dropdown-item" href="admin_contract_template.php">Contracts</a>
                            <a class="dropdown-item" href="adminexpenses.php">Expenses</a>
                            <a class="dropdown-item" href="../chat.php">Chat</a>
                            <a class="dropdown-item" href="adminhistory.php">History</a>
                            <a class="dropdown-item" href="../logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>