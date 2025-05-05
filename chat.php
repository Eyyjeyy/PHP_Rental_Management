<?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);
    
    include 'admin.php';
    $admin = new Admin();

    if(!$admin->isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    if($admin->isLoggedIn() && $admin->session_role == 'user') {
        header("Location: index.php");
        exit();
    }

    $user_id = $admin->session_id;
    $chat_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    $_SESSION['chat_user_id'] = $chat_user_id;


    $users = $admin->getAllUsers();
    // echo $chat_user_id . "<br>";
    // echo $_SESSION['chat_user_id'] . "<br>";
    // echo $admin->session_id . "<br>";
    // echo $user_id . "<br>";

    // Check if the request is an AJAX request to fetch messages
    if (isset($_GET['fetch_messages']) && $_GET['fetch_messages'] == 1) {
        $user_id = $admin->session_id;
        // echo "AJ";
        if ($chat_user_id) {
            $sql = "SELECT m.*, 
                        u1.username AS sender_username, 
                        u2.username AS receiver_username
                    FROM messages m
                    LEFT JOIN users u1 ON m.sender_id = u1.id
                    LEFT JOIN users u2 ON m.receiver_id = u2.id
                    WHERE (m.sender_id = '$user_id' AND m.receiver_id = '$chat_user_id') 
                    OR (m.sender_id = '$chat_user_id' AND m.receiver_id = '$user_id')
                    ORDER BY m.timestamp";
            $result = $admin->conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $message = htmlspecialchars($row['message']);
                    $timestamp = $row['timestamp'];
                    $sender_id = $row['sender_id'];
                    $receiver_id = $row['receiver_id'];
                    $sender_username = htmlspecialchars($row['sender_username']);
                    $receiver_username = htmlspecialchars($row['receiver_username']);
                    $sender = ($sender_id == $user_id) ? 'You' : $sender_username;
                    $class = ($sender_id == $user_id) ? 'message-right' : 'message-left';
                    $image_path = $row['image_path'];
                    $message_id = $row['id']; // Unique ID for each message
                    ?>
                    <div class="message <?php echo $class; ?>">
                        <p><strong><?php echo $sender; ?>:</strong> <?php echo $message; ?></p>
                        <?php if ($image_path): ?>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal<?php echo $message_id; ?>">
                                <img src="<?php echo $image_path; ?>" alt="Image">
                            </a>
                            <div class="modal fade" id="imageModal<?php echo $message_id; ?>" tabindex="-1" aria-labelledby="imageModalLabel<?php echo $message_id; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel<?php echo $message_id; ?>">Image Preview</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body mx-auto">
                                            <img src="<?php echo $image_path; ?>" alt="Image" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <span class="timestamp"><?php echo $timestamp; ?></span>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No messages yet.</p>";
            }
        } else {
            echo "<p>Select a user to start chatting.</p>";
        }
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $message = $_POST['message'] ?? '';
        $media_path = null;
    
        if (isset($_FILES['media']) && $_FILES['media']['error'] == UPLOAD_ERR_OK) {
            $file_tmp_path = $_FILES['media']['tmp_name'];
            $file_name = $_FILES['media']['name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'ogg');
    
            if (in_array($file_extension, $allowed_extensions)) {
                $upload_file_dir = './uploads/';
                $dest_path = $upload_file_dir . uniqid() . '.' . $file_extension;
    
                if (move_uploaded_file($file_tmp_path, $dest_path)) {
                    $media_path = $dest_path;
                } else {
                    echo "File could not be uploaded.";
                }
            } else {
                echo "Invalid file extension.";
            }
        }
    
        if (!empty($message) || $media_path) {
            $chat_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
            $admin->sendMessage($user_id, $chat_user_id, $message, $media_path);
        }
    }

    // if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    //     $message = $_POST['message'];
    //     if (!empty($message)) {
    //         $admin->sendMessage($user_id, $chat_user_id, $message);
    //     }
    // }

    // $sql = "SELECT * FROM messages 
    //         WHERE (sender_id = $user_id AND receiver_id = $chat_user_id) 
    //         OR (sender_id = $chat_user_id AND receiver_id = $user_id) 
    //         ORDER BY timestamp";
    // $sql = "SELECT m.*, u1.username AS sender_name, u2.username AS receiver_name
    //     FROM messages m
    //     INNER JOIN users u1 ON m.sender_id = u1.id
    //     INNER JOIN users u2 ON m.receiver_id = u2.id
    //     WHERE (m.sender_id = (SELECT id FROM users WHERE users_id = $user_id) AND m.receiver_id = (SELECT id FROM users WHERE users_id = $chat_user_id))
    //     OR (m.sender_id = (SELECT id FROM users WHERE users_id = $chat_user_id) AND m.receiver_id = (SELECT id FROM users WHERE users_id = $user_id))
    //     ORDER BY m.timestamp";

    $sql = "SELECT m.*, 
               u1.username AS sender_username, 
               u2.username AS receiver_username
        FROM messages m
        LEFT JOIN users u1 ON m.sender_id = u1.id
        LEFT JOIN users u2 ON m.receiver_id = u2.id
        WHERE m.receiver_id = '$user_id' OR m.sender_id = '$user_id'
        ORDER BY m.timestamp";
    $result = $admin->conn->query($sql);

    $sql_debug = "SELECT m.*, 
               u1.username AS sender_username, 
               u2.username AS receiver_username
        FROM messages m
        LEFT JOIN users u1 ON m.sender_id = u1.id
        LEFT JOIN users u2 ON m.receiver_id = u2.id
        WHERE m.receiver_id = '$user_id' OR m.sender_id = '$user_id'
        ORDER BY m.timestamp";
    $result_debug = $admin->conn->query($sql);

    // if ($result_debug) {
    //     if ($result_debug->num_rows > 0) {
    //         echo "Debug Results:<br>";
    //         while ($row_debug = $result_debug->fetch_assoc()) {
    //             echo "Logged in User's ID: " . $admin->session_id . "<br>";
    //             echo "Message ID: " . $row_debug['id'] . "<br>";
    //             echo "Sender ID: " . $row_debug['sender_id'] . "<br>";
    //             echo "Receiver ID: " . $row_debug['receiver_id'] . "<br>";
    //             echo "Sender Username: " . $row_debug['sender_username'] . "<br>";
    //             echo "Receiver Username: " . $row_debug['receiver_username'] . "<br>";
    //             echo "Message: " . $row_debug['message'] . "<br>";
    //             echo "Timestamp: " . $row_debug['timestamp'] . "<br>";
    //             echo "<hr>";
    //         }
    //     } else {
    //         echo "<p>No debug messages found for user ID: $user_id</p>";
    //     }
    // } else {
    //     echo "Debug Query Error: " . $admin->conn->error;
    // }

    // Debug output: print SQL query
    // echo "<p>SQL Query: $sql</p>";

    if ($result === false) {
        // Query execution failed
        echo "Query failed: " . $admin->conn->error;
        exit;
    }

    $pageTitle = "Chat Page"; // Change this according to the current page
    $page = "adminchat";
    // Determine the base URL
    $base_url = dirname($_SERVER['SCRIPT_NAME']) . '/';
?>



    <div class="container-fluid">
        <div class="row">
           
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
   
    <div class="col sidebar text-white">
    
                <nav class="navbar navbar-expand-lg navbar-light sidebar">
               

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav d-flex flex-column">
                        <a class="navbar-brand mt-4 mb-3 py-0 justify-content-center" href="admin/admindashboard.php">
                <img src="asset/Renttrack pro logo.png" id="userpiclogo" class="img-fluid" alt="..." style="height: 100px;">
            </a>          
                        <div class="hover-container">
                            <a class="nav-link" href="admin/admindashboard.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                <path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/>
                            </svg>
                                <p>Dashboard</p>
                            </a>
                       </div>
                       <div class="hover-container">
                            <a class="nav-link" href="admin/adminusers.php">

                            
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M12 1a1 1 0 0 1 1 1v10.755S12 11 8 11s-5 1.755-5 1.755V2a1 1 0 0 1 1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                                    <path d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                                </svg>
                                <p>Users</p>
                            </a>
                             </div>
                             <div class="hover-container">
                            <a class="nav-link" aria-current="page" href="admin/adminhouses.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M3 0a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h3v-3.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V16h3a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1zm1 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5M4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5"/>
                                </svg>
                                <p>Apartments</p>
                            </a>
    </div>
    <div class="hover-container">
                            <!-- <a class="nav-link" href="admin/admincategories.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0"/>
                                </svg>
                                <p>Categories</p>
                            </a> -->
    </div>
    <div class="hover-container">
                            <a class="nav-link" href="admin/admintenants.php">
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
                            <a class="nav-link" href="admin/adminpayments.php">
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
                            <a class="nav-link" href="admin/admindelinquency.php">
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
                            <a class="nav-link" href="admin/adminpapers.php">
                                <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M9.5 0a.5.5 0 0 1 .5.5.5.5 0 0 0 .5.5.5.5 0 0 1 .5.5V2a.5.5 0 0 1-.5.5h-5A.5.5 0 0 1 5 2v-.5a.5.5 0 0 1 .5-.5.5.5 0 0 0 .5-.5.5.5 0 0 1 .5-.5z"/>
                                    <path d="M3.5 1h.585A1.5 1.5 0 0 0 4 1.5V2a1.5 1.5 0 0 0 1.5 1.5h5A1.5 1.5 0 0 0 12 2v-.5q-.001-.264-.085-.5h.585A1.5 1.5 0 0 1 14 2.5v12a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-12A1.5 1.5 0 0 1 3.5 1"/>
                                </svg>
                                <p>Papers</p>
                            </a>
                        </div>
                        <div class="hover-container">
                            <a class="nav-link" href="admin/admin_contract_template.php">
                                <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-suitcase-lg-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M7 0a2 2 0 0 0-2 2H1.5A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14H2a.5.5 0 0 0 1 0h10a.5.5 0 0 0 1 0h.5a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2H11a2 2 0 0 0-2-2zM6 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1zM3 13V3h1v10zm9 0V3h1v10z"/>
                                </svg>
                                <p>Contracts</p>
                            </a>
                        </div>
                        <div class="hover-container">
                            <a class="nav-link" href="admin/adminexpenses.php">
                                <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v2h6a.5.5 0 0 1 .5.5c0 .253.08.644.306.958.207.288.557.542 1.194.542s.987-.254 1.194-.542C9.42 6.644 9.5 6.253 9.5 6a.5.5 0 0 1 .5-.5h6v-2A1.5 1.5 0 0 0 14.5 2z"/>
                                    <path d="M16 6.5h-5.551a2.7 2.7 0 0 1-.443 1.042C9.613 8.088 8.963 8.5 8 8.5s-1.613-.412-2.006-.958A2.7 2.7 0 0 1 5.551 6.5H0v6A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5z"/>
                                </svg>
                                <p>Expenses</p>
                            </a>
                        </div>
                        <div class="hover-container">
                            <a class="nav-link" href="admin/adminreports.php">
                                <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v2h6a.5.5 0 0 1 .5.5c0 .253.08.644.306.958.207.288.557.542 1.194.542s.987-.254 1.194-.542C9.42 6.644 9.5 6.253 9.5 6a.5.5 0 0 1 .5-.5h6v-2A1.5 1.5 0 0 0 14.5 2z"/>
                                    <path d="M16 6.5h-5.551a2.7 2.7 0 0 1-.443 1.042C9.613 8.088 8.963 8.5 8 8.5s-1.613-.412-2.006-.958A2.7 2.7 0 0 1 5.551 6.5H0v6A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5z"/>
                                </svg>
                                <p>Reports</p>
                            </a>
                        </div>
    <div class="hover-container">
                            <a class="nav-link <?= $page == 'adminchat' ? 'active' : '' ?>" href="chat.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chat-left-text-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                    <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793zm3.5 1a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1zm0 2.5a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1zm0 2.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1z"/>
                                </svg>
                                <p>Chat</p>
                                <?php
                                    foreach ($users as $user) {
                                        if ($user['id'] == $admin->session_id) {
                                            $userFound = $user;
                                            break; // Stop the loop once the user is found
                                        }
                                    }
                                    if ($userFound) {
                                        echo "<p class='notifs fw-bold position-absolute' style='color: #F28543; right: 80px;' id='unseenChatLabel'>" . $userFound['unread_count'] . "</p>";
                                    } else {
                                        echo "<p class='notifs fw-bold position-absolute' style='color: #F28543; right: 80px;'>0</p>"; // Fallback if user not found
                                    }
                                ?>
                            </a>
    </div>
                            <div class="hover-container">
                                <a class="nav-link <?= $page == 'adminarchive' ? 'active' : '' ?>" href="admin/adminarchive.php">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-file-zip-fill my-svg-icon" fill="currentColor" width="24" height="24" viewBox="0 0 16 16">
                                        <path d="M8.5 9.438V8.5h-1v.938a1 1 0 0 1-.03.243l-.4 1.598.93.62.93-.62-.4-1.598a1 1 0 0 1-.03-.243"></path>
                                        <path d="M4 0h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2m2.5 8.5v.938l-.4 1.599a1 1 0 0 0 .416 1.074l.93.62a1 1 0 0 0 1.109 0l.93-.62a1 1 0 0 0 .415-1.074l-.4-1.599V8.5a1 1 0 0 0-1-1h-1a1 1 0 0 0-1 1m1-5.5h-1v1h1v1h-1v1h1v1H9V6H8V5h1V4H8V3h1V2H8V1H6.5v1h1z"></path>
                                    </svg>
                                    <p>Archive</p>
                                </a>
                            </div>
    <div class="hover-container">
                            <a class="nav-link" href="admin/adminhistory.php">
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
                            <a class="nav-link" href="logout.php">
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
                <img src="asset/Renttrack pro logo.png" id="userpiclogo" class="img-fluid" alt="..." style="height: 100px;">
            </a>
        </div>

        <div class="col d-flex justify-content-end" id="navnav">
        <button class="navbar-toggler" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="navbar-toggler-icon"></span>
        </button>


        <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="min-height: 50px; max-height: 90vh; overflow-y: auto;">
        <a class="dropdown-item" href="admin/admindashboard.php">Dashboard</a>
        <a class="dropdown-item" href="admin/adminusers.php">Users</a>
        <a class="dropdown-item" href="admin/adminhouses.php">Apartments</a>
        <!-- <a class="dropdown-item" href="admin/admincategories.php">Categories</a> -->
        <a class="dropdown-item" href="admin/admintenants.php">Tenants</a>
        <a class="dropdown-item" href="admin/adminpayments.php">Payments</a>
        <a class="dropdown-item" href="admin/admindelinquency.php">Delinquency</a>
        <a class="dropdown-item" href="admin/adminpapers.php">Papers</a>
        <a class="dropdown-item" href="admin/admin_contract_template.php">Contracts</a>
        <a class="dropdown-item" href="admin/adminexpenses.php">Expenses</a>
        <a class="dropdown-item" href="admin/adminreports.php">Reports</a>
        <a class="dropdown-item" href="chat.php">Chat</a>
        <a class="dropdown-item" href="admin/adminhistory.php">History</a>
        <a class="dropdown-item" href="logout.php">Logout</a>
        </div>
    </div>
    </div>
    </nav>
    </div>

            <!-- <div class="col main content">
                <div class="card-body">
                    <?php
                        echo "AJ";
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $message = $row['message'];
                                $timestamp = $row['timestamp'];
                                $sender_id = $row['sender_id'];
                                $receiver_id = $row['receiver_id'];
                                $sender_username = htmlspecialchars($row['sender_username']);
                                $receiver_username = htmlspecialchars($row['receiver_username']);
                                // Determine the message sender
                                $sender = ($row['sender_id'] == $user_id) ? 'You' : $sender_username;
                                // Determine the CSS class for message alignment
                                $message_class = ($sender_id == $user_id) ? 'message-right' : 'message-left';
                                ?>
                                <div class="<?php echo $message_class; ?>">
                                    <p><strong><?php echo $sender; ?>:</strong> <?php echo $message; ?></p>
                                    <span class="timestamp"><?php echo $timestamp; ?></span>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<p>No messages yet.</p>";
                        }
                    ?>
                </div>
                <form method="post" action="">
                    <textarea name="message" required></textarea>
                    <button type="submit">Send</button>
                </form>
            </div> -->
            <div class="col main content" style="margin: 0; padding: 0; justify-content: center;">
                <div class="row mx-auto" style="height: 100%; width: 100%; justify-content: center;">

                    <div class="col-12 col-md-5 pe-md-0" id="usercol">
                        <div class="card h-100">
                            <div class="card-header" style="background-color: #EE7214;">
                                <h5 class="text-center mb-0 text-white" style="font-size: 1.2rem; font-weight: bold;">Users</h5>
                            </div>
                            <div class="card-body mt-0" style="background-color: #F9F3EE; overflow-y: auto; height: 80%; margin-bottom: 0;">
                                <ul class="ps-0 h-100" style="list-style: none;">
                                    <?php foreach ($users as $user): ?>
                                        <li>
                                            <a href="chat.php?user_id=<?php echo $user['id']; ?>" class="text-decoration-none" onclick="scrollToBottom()" style="color: #2C3E50;">
                                                <p class="fs-5 mb-2 pt-0 w-auto" style="font-weight: 400; padding-bottom: 10px; text-align: left;
                                                padding: 5px;
                                                padding-left: 10px;
                                                padding-bottom: 10px;
                                                border-radius: 5px;
                                                font-weight: 400;
                                                font-size: 30px;">
                                                    <span id="unseen-count-<?php echo $user['id']; ?>" class="badge bg-success" style="margin-left: 10px; visibility: hidden;">0</span>
                                                    <?php echo htmlspecialchars($user['username']); ?>
                                                </p>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-7 ps-md-0" id="chatcol">    
                        <div class="card h-100">
                            <div class="card-header" style="background-color: #527853;">
                                <h5 class="text-center mb-0 text-white" style="font-size: 1.2rem; font-weight: bold;">Chat</h5>
                            </div>
                            <div class="card-body mt-0" id="messagecard" style="background-color: #F9F3EE; overflow-y: auto; margin-bottom: 0;">
                                <div class="messages p-0" style="background-color: transparent; border: none; max-height: 100%; overflow-y: visible">
                                    <?php
                                        if ($chat_user_id) {

                                        } else {
                                            echo "<p>Select a user to start chatting.</p>";
                                        }
                                    ?>
                                </div>
                                
                            </div>
                            <div id="form-container" style="background-color: #FDF4EF;">
                                <?php if ($chat_user_id): ?>
                                    <form id="message-form" action="chat_user.php?user_id=<?php echo $chat_user_id; ?>" method="POST" enctype="multipart/form-data">
                                        <div class="row h-100 m-0" id="textbtnzone">
                                            <div class="col" style="margin-top: 7px;">
                                                <div class="textzone-2">
                                                    <div class="row m-0" id="textzone">
                                                        <textarea class="w-100" name="message" id="message-input" placeholder="Type your message here..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3 p-0" style="margin-top: 7px; min-width: 200px; margin: 5px;" id="btnzone">
                                        
                                                <div class="btn-2 mw-100">
                                                    <label id="file-name-label" class="btn btn-primary w-100 mx-auto">No file selected</label>
                                                    <input type="file" id="file" name="media" accept="image/*,video/*" style="display:none;">
                                                    <div class="d-flex justify-content-center mt-2">
                                                        <label for="file" style="border-style: none; border-radius: 4px; width: 100px;" id="file-btn" class="btn btn-primary ms-0">Upload</label>
                                                        <button class="btn btn-primary me-0" id="send-btn" style="border-style: none; border-radius: 4px; width: 100px;" type="submit">Send</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col chatflex d-none">
                <!-- User List -->
                <div class="user-list">
                    <div class="list">
                        <h2>Users</h2>
                        <ul>
                            <?php foreach ($users as $user): ?>
                                <li><a href="chat.php?user_id=<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Chat Section -->
                <div class="chat-section">
                    <div class="messages">
                        <?php
                        if ($chat_user_id) {
                            // Fetch messages between the logged-in user and the selected user
                            $sql = "SELECT m.*, 
                                        u1.username AS sender_username, 
                                        u2.username AS receiver_username
                                    FROM messages m
                                    LEFT JOIN users u1 ON m.sender_id = u1.id
                                    LEFT JOIN users u2 ON m.receiver_id = u2.id
                                    WHERE (m.sender_id = '$user_id' AND m.receiver_id = '$chat_user_id') 
                                    OR (m.sender_id = '$chat_user_id' AND m.receiver_id = '$user_id')
                                    ORDER BY m.timestamp";
                            $result = $admin->conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $message = htmlspecialchars($row['message']);
                                    $timestamp = $row['timestamp'];
                                    $sender_id = $row['sender_id'];
                                    $receiver_id = $row['receiver_id'];
                                    $sender_username = htmlspecialchars($row['sender_username']);
                                    $receiver_username = htmlspecialchars($row['receiver_username']);
                                    $sender = ($sender_id == $user_id) ? 'You' : $sender_username;
                                    $class = ($sender_id == $user_id) ? 'message-right' : 'message-left';
                                    $image_path = $row['image_path'];
                                    ?>
                                    <div class="message <?php echo $class; ?>">
                                        <p><strong><?php echo $sender; ?>:</strong> <?php echo $message; ?></p>
                                        <?php if ($image_path): ?>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal<?php echo $message_id; ?>">
                                                <img src="<?php echo $image_path; ?>" alt="Image">
                                            </a>
                                            <div class="modal fade" id="imageModal<?php echo $message_id; ?>" tabindex="-1" aria-labelledby="imageModalLabel<?php echo $message_id; ?>" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="imageModalLabel<?php echo $message_id; ?>">Image Preview</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body mx-auto">
                                                            <img src="<?php echo $image_path; ?>" alt="Image" class="img-fluid">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <span class="timestamp"><?php echo $timestamp; ?></span>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo "<p>No messages yet.</p>";
                            }
                        } else {
                            echo "<p>Select a user to start chatting.</p>";
                        }
                        ?>
                    </div>
                    
                    <!-- Send Message Form -->
                    <?php if ($chat_user_id): ?>
                        <form id="message-form" action="chat.php?user_id=<?php echo $chat_user_id; ?>" method="POST" enctype="multipart/form-data">
                            <textarea name="message" id="message-input" placeholder="Type your message here..." required></textarea>
                            <input type="file" name="media" id="media-input" accept="image/*,video/*">
                            <button type="submit">Send</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Scroll to the bottom when the page loads
            function scrollToBottom() {
                $('#messagecard').scrollTop($('#messagecard')[0].scrollHeight);
            }

            // Initially scroll to bottom when page loads
            scrollToBottom();

            // Set up Server-Sent Events (SSE) to listen for new messages
            var userId = <?php echo $user_id; ?>;
            var chatUserId = <?php echo $chat_user_id; ?>;
            var eventSource = new EventSource('fetch_chat.php?user_id=' + userId + '&chat_user_id=' + chatUserId);

            eventSource.onmessage = function(event) {
                var messages = JSON.parse(event.data);
                var html = '';
                messages.forEach(function(message) {
                    var sender = (message.sender_id == userId) ? 'You' : message.sender_username;
                    var classMessage = (message.sender_id == userId) ? 'message-right' : 'message-left';
                    var mediaHTML = '';
                    if (message.image_path) {
                        var fileExtension = message.image_path.split('.').pop().toLowerCase();
                        if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                            mediaHTML = `
                            <a href="#" data-bs-toggle="modal" data-bs-target="#mediaModal${message.id}">
                                <img src="${message.image_path}" alt="Image">
                            </a>
                            <div class="modal fade" id="mediaModal${message.id}" tabindex="-1" aria-labelledby="mediaModalLabel${message.id}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: #527853;">
                                            <h5 class="modal-title text-white" id="mediaModalLabel${message.id}">Image Preview</h5>
                                            <button type="button" class="btn-svg p-0" data-bs-dismiss="modal" aria-label="Close" style="width: 24px; height: 24px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg w-100" viewBox="0 0 16 16">
                                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="modal-body mx-auto">
                                            <img src="${message.image_path}" alt="Image" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        } else if (['mp4', 'webm', 'ogg'].includes(fileExtension)) {
                            mediaHTML = `
                            <video controls class="mw-100">
                                <source src="${message.image_path}" type="video/${fileExtension}">
                                Your browser does not support the video tag.
                            </video>`;
                        }
                    }

                    var seenStatus = (message.seen == 1 && message.sender_id == userId) ? '<span class="seen-status">Seen</span>' : '';
                    if (classMessage === 'message-right') {
                        html += `<div class="message ${classMessage}">
                                    <p><strong>${sender}:</strong> ${message.message}</p>
                                    ${mediaHTML}
                                    <span class="timestamp">${message.timestamp}</span>
                                    ${seenStatus}
                                </div>`;
                    } else {
                        html += `<div class="message ${classMessage}">
                                    <p><strong>${sender}:</strong> ${message.message}</p>
                                    ${mediaHTML}
                                    <span class="timestamp">${message.timestamp}</span>
                                </div>`;
                    }
                });
                $('.messages').html(html);
                
                $()
                scrollToBottom();
            };

            // JavaScript to update the file name label when a file is selected
            $(document).ready(function() {
                $('#file').on('change', function() {
                    var fileName = $(this).val().split('\\').pop(); // Get the selected file name
                    if (fileName) {
                        $('#file-name-label').text(fileName);
                    } else {
                        $('#file-name-label').text('No file selected');
                    }
                });
            });

            // Handle form submission
            $('#message-form').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                $.ajax({
                    url: 'chat.php?user_id=' + chatUserId,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Form submitted successfully:', response);
                        $('#message-input').val('');
                        $('#file').val('');
                        $('#file-name-label').text('No file selected');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error submitting form:', textStatus, errorThrown);
                    }
                });
            });

            // Function to mark messages as seen
            // function markMessagesAsSeen() {
            //     $.post('mark_seen.php', { 
            //         user_id: userId, 
            //         chat_user_id: chatUserId 
            //     });
            // }
            function markMessagesSeen() {
                $.post('mark_seen.php', { user_id: userId, chat_user_id: chatUserId }, function(response) {
                    console.log('Messages marked as seen:', response);
                });
            }

            // If the chat is active (window is focused), mark messages as seen
            if (document.hasFocus()) {
                markMessagesSeen();
            }
            
            // Optional: Mark messages as seen when the window gains focus
            $(window).on('focus', function() {
                markMessagesSeen();
            });
        });
    </script>
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
        function fetchUnseenCounts() {
            $.ajax({
                url: 'fetch_unseen_count_specific.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Use the data object to update each user's unseen count
                    <?php foreach ($users as $user): ?>
                        // Directly use a new variable name inside this block
                        (function(userId) {
                            const unseenCount = data[userId] || 0; // Get unseen count or default to 0
                            const unseenCountLabel = $('#unseen-count-' + userId);

                            // Update the label and show/hide based on count
                            unseenCountLabel.text(unseenCount);
                            if (unseenCount > 0) {
                                unseenCountLabel.css('visibility', 'visible'); // Make the badge visible
                            } else {
                                unseenCountLabel.css('visibility', 'hidden'); // Make the badge invisible but occupy space
                            }
                        })(<?php echo $user['id']; ?>); // Immediately invoked function with user ID
                    <?php endforeach; ?>
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error fetching unseen counts:", textStatus, errorThrown);
                }
            });
        }

        // Poll every 3 seconds
        setInterval(fetchUnseenCounts, 3000);

        // Run once on page load
        fetchUnseenCounts();
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
        setFavicon('asset/Renttrack pro logo.png'); // Change to your favicon path
        });
    </script>




    <!-- <script>
        // Realtime retrieval of total number of messages received by logged in user but not yet seen by him

        function updateUnreadCount() {
            $.ajax({
                url: 'fetch_unread_count.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Check if the response contains the unread count
                    if (data && data.unread_count !== undefined) {
                        $('#unseenChatLabel').text(data.unread_count);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching unread count:", error);
                }
            });
        }

        // Poll every 5 seconds (5000 milliseconds)
        setInterval(updateUnreadCount, 5000);

        // Initial call to set the unread count on page load
        $(document).ready(function() {
            updateUnreadCount();
        });
    </script> -->











    <!-- <script>
        $(document).ready(function(){
            // Fetch messages initially
            function fetchMessages() {
                var userId = <?php echo $chat_user_id; ?>; // Assuming $chat_user_id is set in PHP
                $.ajax({
                    url: 'chat.php?fetch_messages=1&user_id=' + userId,
                    method: 'GET',
                    success: function(data) {
                        $('.messages').html(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error fetching messages:', textStatus, errorThrown);
                    }
                });
            }

            fetchMessages();

            // Handle form submission
            $('#message-form').on('submit', function(e){
                e.preventDefault();

                var formData = new FormData(this);
                var userId2 = <?php echo $chat_user_id; ?>; // Assuming $chat_user_id is set in PHP
                $.ajax({
                    url: 'chat.php?user_id=' + userId2, // Same PHP file
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Form submitted successfully:', response);
                        $('#message-input').val('');
                        $('#image-input').val('');
                        fetchMessages();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error submitting form:', textStatus, errorThrown);
                    }
                });
            });
        });
    </script> -->










    <!-- <script>
        document.getElementById('message-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const messages = document.querySelector('.messages');

            fetch('chat.php?user_id=<?php echo $chat_user_id; ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Clear input fields
                document.getElementById('message-input').value = '';
                document.getElementById('image-input').value = '';

                // Add message to the chat
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('message', 'message-right');
                messageDiv.innerHTML = `
                    <p><strong>You:</strong> ${data.message}</p>
                    ${data.image ? `<img src="${data.image}" alt="Image">` : ''}
                    <span class="timestamp">${new Date(data.timestamp).toLocaleString()}</span>
                `;
                messages.appendChild(messageDiv);

                // Scroll to the bottom of the messages
                messages.scrollTop = messages.scrollHeight;
            })
            .catch(error => console.error('Error:', error));
        });
    </script> -->
    
<?php include 'admin/includes/footer.php'; ?>