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
  if($admin->isLoggedIn() && $admin->session_role == 'admin') {
      header("Location: ../admin/admindashboard.php");
      exit();
  }

  // // Retrieve the users_id from the session
  // $users_id = $_SESSION['user_id'];

  // // Fetch the houses_id from the tenants table using the users_id
  // $sql_houses_id = "SELECT house_id FROM tenants WHERE users_id = '$users_id'";
  // $result_houses_id = $admin->conn->query($sql_houses_id);

  // // Check if a house was found
  // if ($result_houses_id && $result_houses_id->num_rows > 0) {
  //   $row_houses_id = $result_houses_id->fetch_assoc();
  //   $houses_id = $row_houses_id['house_id'];
  // } else {
  //   // Handle the case where no house was found
  //   $houses_id = null;
  // }

  // Retrieve the users_id from the session
  $users_id = $_SESSION['user_id'];

  // Fetch the houses_id from the houseaccounts table based on the house_id from tenants table
  $sql_houses_id = "SELECT houseaccounts.houses_id 
  FROM tenants 
  INNER JOIN houseaccounts ON tenants.house_id = houseaccounts.houses_id 
  WHERE tenants.users_id = '$users_id'";
  $result_houses_id = $admin->conn->query($sql_houses_id);

  // Check if a house was found
  if ($result_houses_id && $result_houses_id->num_rows > 0) {
    $row_houses_id = $result_houses_id->fetch_assoc();
    $houses_id = $row_houses_id['houses_id'];
  } else {
    // Handle the case where no house was found
    $houses_id = null;
  }


  // // Check if the form is submitted for adding a new payment
  // if(isset($_POST['add_payment'])) {
  //   // Get the payment data from the form
  //   $name = trim(htmlspecialchars($_POST['name']));
  //   $amount = trim(htmlspecialchars($_POST['amount']));
  //   $paymentDate = htmlspecialchars($_POST['payment_date']);

  //   // File upload handling
  //   $file = $_FILES['payment_file']; // Get the uploaded file
  //   $fileName = $file['name']; // Get the file name
  //   $fileTmpName = $file['tmp_name']; // Get the temporary file path
  //   $fileError = $file['error']; // Get any errors
  //   $fileDestination = '../uploads/' . $fileName; // Set the destination folder
      

  //   // Check for upload errors
  //   if ($fileError === UPLOAD_ERR_OK) {
  //     $uploadDir = '../uploads/';
  //     // Ensure the upload directory exists
  //     if (!is_dir($uploadDir)) {
  //         mkdir($uploadDir, 0777, true);
  //     }

  //     // $fileDestination = $uploadDir . basename($fileName); // Set the destination folder

  //     // Generate a unique identifier (using uniqid) to append to the file name
  //     $uniqueIdentifier = uniqid();

  //     // Extract file extension
  //     $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
      
  //     // Generate a new unique file name with the original file name and extension
  //     $newFileName = basename($fileName, '.' . $fileExtension) . '_' . $uniqueIdentifier . '.' . $fileExtension;


  //     $fileDestination = $uploadDir . $newFileName; // Set the destination folder

  //     // Move the uploaded file to the destination folder
  //     if (move_uploaded_file($fileTmpName, $fileDestination)) {
  //         // Call the addPayment method to add the new payment with file path
  //         $added = $admin->addPayment($name, $amount, $houses_id, $paymentDate, $fileDestination);
  //         if ($added) {
  //           // Payment added successfully, you can display a success message here if needed
  //           header("Location: payments.php?payment_added=1");
  //           exit();
  //         } else {
  //           // Error occurred while adding payment, display an error message or handle as needed
  //           $_SESSION['error_message'] = "User not on Tenant List";
  //           header("Location: payments.php?payment_added=1");
  //           echo "Error occurred while adding payment.";
  //           exit();
  //         }
  //     } else {
  //         // Handle file move errors
  //         echo "File upload failed. Unable to move the file to the target directory.";
  //     }
  //   } else {
  //     // Handle file upload errors
  //     echo "File upload failed with error code: " . $fileError;
  //   }
  // }

  if (isset($_POST['add_payment'])) {
    // Get the payment data from the form
    $name = trim(htmlspecialchars($_POST['name']));
    $amount = trim(htmlspecialchars($_POST['amount']));
    $paymentDate = htmlspecialchars($_POST['payment_date']);
    $paymentType = $_POST['paymentType'];  // Get payment type (Deposit or Payment)

    // File upload handling
    $file = $_FILES['payment_file']; // Get the uploaded file
    $fileName = $file['name']; // Get the file name
    $fileTmpName = $file['tmp_name']; // Get the temporary file path
    $fileError = $file['error']; // Get any errors
    $uploadDir = ''; // Initialize the upload directory variable
    $fileDestination = ''; // Initialize file destination

    // Define different file handling logic based on payment type
    if ($paymentType === "Payment") {
      // For Payment, use the uploads folder
      $uploadDir = '../uploads/';
    } else {
      // For Deposit, use the deposits folder
      $uploadDir = '../deposits/';
    }

    // Set the destination folder and file name
    $fileDestination = $uploadDir . basename($fileName);

    // Check for file upload errors
    if ($fileError === UPLOAD_ERR_OK) {
      // Ensure the upload directory exists
      if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0777, true);  // Create the directory if it doesn't exist
      }

      // Generate a unique identifier to append to the file name
      $uniqueIdentifier = uniqid();
      $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
      $newFileName = basename($fileName, '.' . $fileExtension) . '_' . $uniqueIdentifier . '.' . $fileExtension;
      $fileDestination = $uploadDir . $newFileName;  // Update destination with the new unique file name

      // Move the uploaded file to the destination folder
      if (move_uploaded_file($fileTmpName, $fileDestination)) {
        // Check paymentType and call the corresponding function
        if ($paymentType === "Payment") {
          // Call the method to add payment with file path
          $added = $admin->addPayment($name, $amount, $houses_id, $paymentDate, $fileDestination);
        } else {
          // Call a different function for Deposit
          $added = $admin->addDeposit($name, $amount, $houses_id, $paymentDate, $fileDestination);
        }

        if ($added) {
          // Payment added successfully, redirect to payment page
          header("Location: payments.php?payment_added=1");
          exit();
        } else {
          // Error occurred while adding payment or deposit
          $_SESSION['error_message'] = "Error occurred while adding payment/deposit.";
          header("Location: payments.php?payment_added=1");
          exit();
        }
      } else {
        // Handle file move errors
        $_SESSION['error_message'] = "File upload failed. Unable to move the file to the target directory.";
        header("Location: payments.php?payment_added=1");
        exit();
      }
    } else {
      // Handle file upload errors
      $_SESSION['error_message'] = "File upload failed with error code: " . $fileError;
      header("Location: payments.php?payment_added=1");
      exit();
    }
  }


  // $sql = "
  // SELECT payments.* 
  // FROM payments
  // INNER JOIN tenants ON payments.tenants_id = tenants.id
  // WHERE tenants.users_id = '$admin->session_id'
  // ";
  // $result = $admin->conn->query($sql);

  $sql = "
  SELECT 
      payments.id AS document_id, 
      'payments' AS document_type, 
      payments.name, 
      payments.amount,
      payments.tenants_id, 
      payments.houses_id, 
      payments.filepath,
      payments.date_payment AS transaction_date,
      payments.approval 
  FROM payments
  INNER JOIN tenants ON payments.tenants_id = tenants.id
  WHERE tenants.users_id = '$admin->session_id'

  UNION ALL

  SELECT 
      deposit.id AS document_id, 
      'deposit' AS document_type, 
      CONCAT(users.firstname, ' ', users.middlename, ' ', users.lastname) AS name, 
      deposit.depositamount AS amount, 
      deposit.tenantid AS tenants_id, 
      deposit.houses_id, 
      deposit.deposit_filepath AS filepath, 
      deposit.depositdate AS transaction_date, 
      deposit.approval 
  FROM deposit
  INNER JOIN tenants ON deposit.tenantid = tenants.id
  INNER JOIN users ON tenants.users_id = users.id
  WHERE tenants.users_id = '$admin->session_id'
  ";
  $result = $admin->conn->query($sql);



  $sql_name = "SELECT * FROM users WHERE id = '$admin->session_id'";
  $result_name = $admin->conn->query($sql_name);

  $sql_gcashbank = "SELECT houseaccounts.* FROM houseaccounts INNER JOIN tenants ON houseaccounts.houses_id = tenants.house_id WHERE tenants.users_id = '$admin->session_id'";
  $result_gcashbank = $admin->conn->query($sql_gcashbank);

  if ($result_name && $result_name->num_rows > 0) {
    // Fetch the data from the result set
    $row_name = $result_name->fetch_assoc();
    $Firstname = $row_name['firstname'];
    $Middlename = $row_name['middlename'];
    $Lastname = $row_name['lastname'];
  } else {
    // Handle the case where no user with the specified ID is found
    echo "No user found with the specified ID.";
  }

  // Initialize default values
  $monthlyBalance = 0.00;
  $monthlyRentDue = 0.00;
  $totalPayments = 0.00;

  // Get the tenant information
  $sql_tenant = "SELECT t.date_start, t.date_preferred, t.date_end, t.house_id, t.id AS tenant_id, h.price 
  FROM tenants t
  INNER JOIN houses h ON t.house_id = h.id 
  WHERE t.users_id = '$users_id'";
  $result_tenant = $admin->conn->query($sql_tenant);

  if ($result_tenant->num_rows > 0) {
    $row_tenant = $result_tenant->fetch_assoc();
    $date_start = $row_tenant['date_start'];
    $date_preferred = $row_tenant['date_preferred'];
    $date_end = $row_tenant['date_end'];
    $house_id = $row_tenant['house_id'];
    $tenant_id = $row_tenant['tenant_id'];
    $price = $row_tenant['price'];

    // Determine the base date for calculation
    $baseDate = $date_preferred ? new DateTime($date_preferred) : new DateTime($date_start);

    // Calculate the interval to the end date if it exists, otherwise to the current date
    if ($date_end) {
    $endDate = new DateTime($date_end);
    $interval = $baseDate->diff($endDate);
    } else {
    $currentDate = new DateTime();
    $interval = $baseDate->diff($currentDate);
    }

    // Calculate the number of months passed
    $monthsPassed = $interval->y * 12 + $interval->m;

    // Calculate the total rent due
    $totalRentDue = $monthsPassed * $price;

    // Fetch payment amounts for the specific tenant, considering only payments on or after the base date
    $baseDateString = $baseDate->format('Y-m-d');
    $sql_payments = "SELECT SUM(amount) AS total_payments
          FROM payments
          WHERE tenants_id = $tenant_id
            AND houses_id = $house_id
            AND date_payment >= '$baseDateString'
            AND approval = 'true'";
    $result_payments = $admin->conn->query($sql_payments);
    $row_payments = $result_payments->fetch_assoc();
    $totalPayments = $row_payments['total_payments'] ?? 0.00;  // Default to 0 if no payments are found

    // Count the number of tenants sharing the same house
    $sql_tenants_count = "SELECT COUNT(*) AS tenants_count
              FROM tenants
              WHERE house_id = $house_id";
    $result_tenants_count = $admin->conn->query($sql_tenants_count);
    $row_tenants_count = $result_tenants_count->fetch_assoc();
    $tenants_count = $row_tenants_count['tenants_count'];

    // Calculate the rent due per tenant
    $rentDuePerTenant = $totalRentDue / $tenants_count;

    // Calculate the current balance for the current tenant
    $balance = $rentDuePerTenant - $totalPayments;

    // Display the balance
    // echo "Total Balance: $" . number_format($balance, 2);
    // echo "<br>Total Rent Due: $" . number_format($rentDuePerTenant, 2);
    // echo "<br>Total Payments: $" . number_format($totalPayments, 2);

    // Monthly Balance Calculation
    $currentMonthStart = (new DateTime('first day of this month'))->format('Y-m-d');
    $currentMonthEnd = (new DateTime('last day of this month'))->format('Y-m-d');

    // Calculate the monthly rent due for the tenant
    $monthlyRentDue = $price;

    // Fetch approved payment amounts for the current month
    $sql_monthly_payments = "SELECT SUM(amount) AS total_monthly_payments
      FROM payments
      WHERE tenants_id = $tenant_id
        AND houses_id = $house_id
        AND date_payment >= '$currentMonthStart'
        AND date_payment <= '$currentMonthEnd'
        AND approval = 'true'";
    $result_monthly_payments = $admin->conn->query($sql_monthly_payments);
    $row_monthly_payments = $result_monthly_payments->fetch_assoc();
    $totalMonthlyPayments = $row_monthly_payments['total_monthly_payments'] ?? 0.00;  // Default to 0 if no payments are found

    // Calculate the balance for the current month
    $monthlyBalance = $monthlyRentDue - $totalMonthlyPayments;

    // Ensure the balance does not go below 0
    $monthlyBalance = max(0, $monthlyBalance);

    // Display the monthly balance
    // echo "<br>Monthly Balance: $" . number_format($monthlyBalance, 2);
    // echo "<br>Monthly Rent Due: $" . number_format($monthlyRentDue, 2);
    // echo "<br>Total Monthly Payments: $" . number_format($totalMonthlyPayments, 2);



    // Calculate the number of months elapsed
    $startDate = $date_preferred ? new DateTime($date_preferred) : new DateTime($date_start);
    $currentDateCheck = new DateTime();
    $dateDifference = $startDate->diff($currentDateCheck);
    $elapsedMonths = $dateDifference->y * 12 + $dateDifference->m;

    $monthlyRate = $price;

    // Calculate the total rent amount due for all elapsed months
    $totalAmountDue = $elapsedMonths * $monthlyRate;

    // Fetch total approved payments made by the tenant
    $queryTotalPayments = "SELECT SUM(amount) AS approved_payments
        FROM payments
        WHERE tenants_id = $tenant_id
          AND houses_id = $house_id
          AND approval = 'true'";
    $resultTotalPayments = $admin->conn->query($queryTotalPayments);
    $paymentData = $resultTotalPayments->fetch_assoc();
    $totalApprovedPayments = $paymentData['approved_payments'] ?? 0.00; // Default to 0 if no payments exist

    // Calculate the total outstanding balance
    $outstandingBalance = $totalAmountDue - $totalApprovedPayments;

    // Ensure the balance does not drop below zero
    $outstandingBalance = max(0, $outstandingBalance);


  } else {
    echo "No tenant information found for the current user.";
  }





  $pageTitle = 'Payments Page'; 
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <?php if ($pageTitle == 'Home Page' || $pageTitle == 'Chat Page'): ?>
        <link rel="stylesheet" href="asset/user.css">
        <link rel="icon" type="image/x-icon" href="asset/Renttrack pro logo.png">
    <?php else: ?>
        <link rel="stylesheet" href="../asset/user.css">
        <link rel="icon" type="image/x-icon" href="../asset/Renttrack pro logo.png">
    <?php endif; ?>
    <!-- <link rel="stylesheet" href= "../asset/user.css"> -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

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

    <nav class="navbar navbar-expand-lg navbar-light flex-column py-0" id="navbar" style="background-color: #3A583C;">
    <!-- <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="../asset/Renttrack pro no word.png" class="img-fluid" alt="..." width="120" height="96">
        </a>
    </div> -->

    <div class="container-fluid mb-3 mt-3" id="navbarbar">
    <div class="row mx-auto w-65 d-flex align-items-center">
    
    




        <!-- Left-aligned image -->
        <div class="col d-flex align-items-center">
            <a class="navbar-brand py-0" href="../index.php">
                <img src="../asset/Renttrack pro logo.png" id="userpiclogo" class="img-fluid" alt="..." style="height: 100px;">
            </a>
        </div>
        
        <!-- Right-aligned navigation links and icons -->
        <div class="col d-flex justify-content-end" id="navnav">
        <button class="navbar-toggler" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="navbar-toggler-icon"></span>
        </button>


        <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="max-height: 600px;">
        <a class="dropdown-item" href="payments.php">Payment</a>
        <a class="dropdown-item" href="../info.php">Info</a>
        <a class="dropdown-item" href="../profile_user.php">Profile</a>
        <a class="dropdown-item" href="../contract_user.php">Contract</a>
        <a class="dropdown-item" href="../user_delinquency.php">Delinquency</a>
        <a class="dropdown-item" href="../chat_user.php">Chat</a>
        <a class="dropdown-item" href="../logout.php">Log Out</a>
    </div>


            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mb-2 mb-lg-0 d-flex align-items-center">
                    <li class="nav-item mx-1">
                        <a class="nav-link h-100 d-flex align-items-center" id="icontext" href="payments.php">
                            <p class="mb-0" id="linknav">Payment</p>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link h-100 d-flex align-items-center" id="icontext" href="../info.php">
                            <p class="mb-0" id="linknav">Info</p>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link h-100 d-flex align-items-center" id="icontext" href="../profile_user.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                            </svg>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link h-100 d-flex align-items-center position-relative" id="icontext" href="../contract_user.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-suitcase-lg-fill" viewBox="0 0 16 16">
                                <path d="M7 0a2 2 0 0 0-2 2H1.5A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14H2a.5.5 0 0 0 1 0h10a.5.5 0 0 0 1 0h.5a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2H11a2 2 0 0 0-2-2zM6 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1zM3 13V3h1v10zm9 0V3h1v10z"/>
                            </svg>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link h-100 d-flex align-items-center position-relative" id="icontext" href="../user_delinquency.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-person-fill-slash" viewBox="0 0 16 16">
                                <path d="M13.879 10.414a2.501 2.501 0 0 0-3.465 3.465zm.707.707-3.465 3.465a2.501 2.501 0 0 0 3.465-3.465m-4.56-1.096a3.5 3.5 0 1 1 4.949 4.95 3.5 3.5 0 0 1-4.95-4.95ZM11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"></path>
                            </svg>
                            <p class="notifs fw-bold position-absolute" style="color: #F28543; left: 42px;" id="delinquencyCount"></p>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link h-100 d-flex align-items-center position-relative" id="icontext" href="../chat_user.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-chat-dots-fill" viewBox="0 0 16 16">
                                <path d="M16 8c0 3.866-3.582 7-8 7a9 9 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7M5 8a1 1 0 1 0-2 0 1 1 0 0 0 2 0m4 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0m3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                            </svg>
                            <p class="notifs fw-bold position-absolute" style="color: #F28543; left: 42px;" id="unseenChatLabel"></p>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link h-100 d-flex align-items-center" id="icontext"href="../logout.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-chat-dots-fill" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z"/>
                                <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z"/>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

</nav>


<div class="row mx-auto w-65 d-flex align-items-center col main content" id="paymentwhole">
  <div class="mx-auto">
    <div class="card-header text-center" style="font-size: 1.2rem; font-weight: bold;">Payment</div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="row" id="createrec">
                  <div class="col-sm-6" id="monthly">
                    <?php 
                    // echo "<p class='fw-bolder'>Monthly Balance: &#8369;" . number_format($monthlyBalance, 2) . "</p>";
                    echo "<p class='fw-bolder'>Total Balance: &#8369;" . number_format($outstandingBalance ?? 0, 2) . "</p>";
                    echo "<p class='fw-bolder'>Monthly Rent Due: &#8369;" . number_format($monthlyRentDue, 2) . "</p>";
                    echo "<p class='fw-bolder'>Total Payments: &#8369;" . number_format($totalPayments, 2) . "</p>";
                    // if ($result_gcashbank->num_rows > 0) {
                    //   while ($row_gcashbank = $result_gcashbank->fetch_assoc()) {
                    //     echo "<p class='fw-bolder'>Gcash: " . $row_gcashbank['gcash'] . "</p>";
                    //     echo "<p class='fw-bolder'>Bank: " . $row_gcashbank['bank'] . "</p>";
                    //   }
                    // }
                    ?>
                  </div>
                  <div class="col-sm-6 d-flex flex-column d-sm-none" id="createrecbtn">
                    <p class="fw-bolder">Bank: Banko de Oro</p>
                    <p class="fw-bolder ms-xs-0">Bank Acc Name: Firstname M. Lastname</p>
                    <p class="fw-bolder">Bank Number: 2014923842854</p>
                    <!-- <p class="fw-bolder align-self-sm-end">&nbsp;</p>
                    <p class="fw-bolder align-self-sm-end">&nbsp;</p> -->
                    <?php
                    if ($result_gcashbank->num_rows > 0) {
                      while ($row_gcashbank = $result_gcashbank->fetch_assoc()) {
                        echo "<p class='fw-bolder'>Gcash: " . $row_gcashbank['gcash'] . "</p>";
                        // echo "<p class='fw-bolder'>Bank: " . $row_gcashbank['bank'] . "</p>";
                      }
                    }
                    ?>
                    <button type="button" class="btn receipt" id="new_payment" data-bs-toggle="modal" data-bs-target="#paymentModal" style="max-width: 150px;">
                        <i class="fa fa-plus"></i> Create Receipt
                    </button>
                  </div>
                  <div class="col-sm-6 d-flex flex-column d-none d-sm-flex" id="createrecbtn">
                    <p class='fw-bolder' style='margin-left: calc(10vw - 12vw);'>Bank: Banko de Oro</p>
                    <p class="fw-bolder ms-xs-0" style="margin-left: calc(10vw - 12vw);">Bank Acc Name: Firstname M. Lastname</p>
                    <p class="fw-bolder" style="margin-left: calc(10vw - 12vw);">Bank Number: 2014923842854</p>
                    <!-- <p class="fw-bolder align-self-sm-end">&nbsp;</p>
                    <p class="fw-bolder align-self-sm-end">&nbsp;</p> -->
                    <p class='fw-bolder' style='margin-left: calc(10vw - 12vw);'>Gcash: 09955835160</p>
                    <?php
                    if ($result_gcashbank->num_rows > 0) {
                      while ($row_gcashbank = $result_gcashbank->fetch_assoc()) {
                        echo "<p class='fw-bolder' style='margin-left: calc(10vw - 12vw);'>Gcash: " . $row_gcashbank['gcash'] . "</p>";
                        echo "<p class='fw-bolder' style='margin-left: calc(10vw - 12vw);'>Bank: " . $row_gcashbank['bank'] . "</p>";
                      }
                    }
                    ?>
                    <button type="button" class="btn receipt align-self-sm-end" id="new_payment" data-bs-toggle="modal" data-bs-target="#paymentModal" style="max-width: 150px; margin-left: calc(10vw - 12vw);">
                        <i class="fa fa-plus"></i> Create Receipt
                    </button>
                  </div>
                </div>
            </div>
            <div class="table-container">

                <table class="table table-striped table-bordered border border-5" id="tabletabletable">
                    <thead class="">
                        <tr>
                            <!-- <th scope="col">Name</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Image</th>
                            <th scope="col">Approved</th>
                            <th scope="col">Payment Date</th> -->

                            <th scope="col">Name</th>
                            <th scope="col">Payment Type</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Image</th>
                            <th scope="col">Status</th>
                            <th scope="col">Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Check if there are any rows in the result set
                        // if ($result->num_rows > 0) {
                        //     // Output data of each row
                        //     while ($row = $result->fetch_assoc()) {
                        //         echo "<tr>";
                        //         echo "<td>" . $row["name"] . "</td>"; // actual column name from your database
                        //         echo "<td>" . $row["amount"] . "</td>"; // actual column name from your database
                        //         echo "<td><img src='" . $row["filepath"] . "' alt='Receipt' class='img-fluid' style='max-width: 150px; height: 150px;'></td>";
                        //         // echo "<td>" . ($row["approval"] == "true" ? "APPROVED" : "UNAPPROVED") . "</td>";
                        //         echo "<td>" . ($row["approval"] === "true" ? "APPROVED" : ($row["approval"] === "false" ? "UNAPPROVED" : "PENDING")) . "</td>";
                        //         echo "<td>" . $row["date_payment"] . "</td>"; // actual column name from your database
                        //         echo "</tr>";
                        //     }
                        // } else {
                        //     echo "<tr><td colspan='3'>No payments found</td></tr>";
                        // }

                        if ($result->num_rows > 0) {
                          // Output data of each row
                          while ($row = $result->fetch_assoc()) {
                              echo "<tr>";
                              echo "<td>" . $row["name"] . "</td>"; 
                              echo "<td>" . ($row["document_type"] === 'payments' ? 'Rent' : 'Deposit' ) . "</td>";
                              echo "<td>" . $row["amount"] . "</td>"; 
                              echo "<td><img src='" . $row["filepath"] . "' alt='Receipt' class='img-fluid' style='max-width: 150px; height: 150px;'></td>";
                              // echo "<td>" . ($row["approval"] == "true" ? "APPROVED" : "UNAPPROVED") . "</td>";
                              echo "<td>" . ($row["approval"] === "true" ? "APPROVED" : ($row["approval"] === "false" || $row["approval"] === "Unapproved" ? "UNAPPROVED" : ($row["approval"] === "2 Months Consumed" || $row["approval"] === "1 Month Consumed" ? $row["approval"] : "PENDING"))) . "</td>";
                              echo "<td>" . $row["transaction_date"] . "</td>"; // actual column name from your database
                              echo "</tr>";
                          }
                        } else {
                            echo "<tr><td colspan='3'>No payments found</td></tr>";
                        }
                    ?>
                    </tbody>
                </table>
            
        </div>
    </div>
  </div>
</div>



<!-- Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentModalLabel" style>Create Receipt</h5>
         <button type="button" class="btn-svg"  data-bs-dismiss="modal" aria-label="Close"> 
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
        </svg>
    </button>
      </div>
      <form id="paymentForm" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <?php
            if ($houses_id == null) {
              $_SESSION['error_message'] = "User/Tenant has no House, Contact Admin";
              echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
              unset($_SESSION['error_message']);
            }
            if (isset($_SESSION['error_message']) && !empty($_SESSION['error_message'])) {
              echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
              // Unset the error message after displaying it
              echo '<script>var paymentModal = new bootstrap.Modal(document.getElementById("paymentModal"), { keyboard: false });paymentModal.show();</script>';
              unset($_SESSION['error_message']);
            } else if (isset($_SESSION['success_message']) && !empty($_SESSION['success_message'])) {
              echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
              echo '<script>var paymentModal = new bootstrap.Modal(document.getElementById("paymentModal"), { keyboard: false });paymentModal.show();</script>';
              unset($_SESSION['success_message']);
            }
          ?>
          <div class="mb-3">
            <!-- <input type='hidden' name='housesid' value="<?php echo htmlspecialchars($houses_id); ?>">
            <label for="paymentName" class="form-label">Name</label>
            <input type="text" class="form-control" id="paymentName" name="name"> -->
            <label for="housesId" class="form-label">House ID</label>
            <input type="text" class="form-control" id="housesId" name="housesid" value="<?php echo htmlspecialchars($houses_id); ?>" readonly>
          </div>
          <div class="mb-3">
            <label for="paymentName" class="form-label">Name</label>
            <input type="text" class="form-control" id="paymentName" name="name" value="<?php echo $Firstname . " " . $Middlename . " " . $Lastname ?>" readonly>
          </div>
          <div class="mb-3">
            <label for="paymentType" class="form-label">Payment Type</label>
            <select class="form-select" id="paymentType" name="paymentType" required>
              <option value="Deposit" selected>Deposit</option>
              <option value="Payment" selected>Rent</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="paymentAmount" class="form-label">Amount</label>
            <input type="text" class="form-control" id="paymentAmount" name="amount">
          </div>
          <div class="mb-3">
            <label for="paymentDate" class="form-label">Payment Date</label>
            <input type="date" class="form-control" id="paymentDate" name="payment_date">
          </div>
          <div class="mb-3">
            <label for="paymentFile" class="form-label">Upload File</label>
            <input type="file" class="form-control" id="paymentFile" name="payment_file">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="submitBtn" name="add_payment">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
          </div>
<!-- Include jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const paymentForm = document.getElementById("paymentForm");

    paymentForm.addEventListener("submit", function(event) {
      // No need to prevent default form submission
      // No need for AJAX submission

      // Form will be submitted traditionally
    });
  });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var housesIdInput = document.getElementById('housesId');
    var submitBtn = document.getElementById('submitBtn');

    // Function to check the value of housesId and enable/disable the submit button
    function checkHousesId() {
        if (!housesIdInput.value.trim()) {
            submitBtn.disabled = true;
        } else {
            submitBtn.disabled = false;
        }
    }

    // Initial check when the page loads
    checkHousesId();

    // Add an event listener to check the value whenever it changes
    housesIdInput.addEventListener('input', checkHousesId);
});
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
  function fetchDelinquencyMonthMissed() {
    $.ajax({
      url: '../fetch_user_delinquency_month.php',
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

<!-- <script>
  // JavaScript code to handle form submission
  document.addEventListener("DOMContentLoaded", function() {
    // Get the payment form
    const paymentForm = document.getElementById("paymentForm");

    // Add event listener for form submission
    paymentForm.addEventListener("submit", function(event) {
      event.preventDefault(); // Prevent default form submission

      // Perform form validation if needed

      // Submit the form via AJAX
      const formData = new FormData(paymentForm); // Create FormData object
      fetch("payments.php", { // Send form data to payments.php
        method: "POST",
        body: formData
      })
      .then(response => {
        if (response.ok) {
          // Handle successful form submission
          // For example, close the modal and reload the page
          $('#paymentModal').modal('hide');
          location.reload();
        } else {
          // Handle errors if any
          console.error("Error:", response.statusText);
        }
      })
      .catch(error => {
        console.error("Error:", error);
      });
    });
  });
</script> -->


<?php include '../regular/includes/footer.php'; ?>