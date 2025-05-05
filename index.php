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

    // $sql = "SELECT * FROM eviction_popup WHERE eviction_popup.users_id = $admin->session_id AND eviction_popup.seen = '' ";
    // $result = $admin->conn->query($sql);

    // if ($result->num_rows > 0) {
    //     // Update the 'seen' column to 'true'
    //     $seenPopupSql = "UPDATE eviction_popup SET seen = 'true' WHERE users_id = $admin->session_id AND seen = ''";
    //     if ($admin->conn->query($seenPopupSql) === TRUE) {
    //         // Display the modal popup
    //         echo "
    //         <div id='modal' style='
    //             display: block; 
    //             position: fixed; 
    //             left: 0; 
    //             width: 100%; 
    //             height: 100%; 
    //             background-color: rgba(0,0,0,0.5); 
    //             z-index: 1000;'>
    //             <div style='
    //                 background: white; 
    //                 padding: 20px; 
    //                 margin: 100px auto; 
    //                 width: 300px; 
    //                 text-align: center; 
    //                 border-radius: 5px;'>
    //                 <p>Record found for user ID: $admin->session_id</p>
    //                 <button id='closeBtn' style='display: none;'>Close</button>
    //             </div>
    //         </div>
    //         <script>
    //             // Show the close button after 10 seconds
    //             setTimeout(function() {
    //                 document.getElementById('closeBtn').style.display = 'block';
    //             }, 10000);

    //             // Close the modal when the close button is clicked
    //             document.getElementById('closeBtn').onclick = function() {
    //                 document.getElementById('modal').style.display = 'none';
    //             };
    //         </script>
    //         ";
    //     } else {
    //         // error_log('Error updating seen status: ' . $admin->conn->error);
    //     }
    // }

    $pageTitle = 'Home Page';
?>

<?php include 'regular/includes/header_user.php'; ?>




<div class="container-fluid p-0 mb-0">
    <div class="row mx-auto w-65 d-flex align-items-center p-0" style="" id="textindex">
        <div class="row mt-5 m-0 p-0">
            <div class="col-12 col-lg-6 d-flex flex-column justify-content-center p-0" style="margin-right: 0px;">
            <p class="fs-1 fw-bolder inter-google text-center mb-lg-3 m-0 p-0" style="color: #3A583C;">Welcome</p>
                <p class="fs-5 inter-google mx-sm-4 text-center d-lg-none m-0 p-0">
                    RentTrack Pro is a rental monitoring system, designed to cater to the needs of the landlord/s
                    for a system for managing house rentals efficiently, leveraging the power of web technologies to streamline
                    the processing and storage of tenant, payments and house information.
                </p>
                <p class="fs-10 inter-google mx-sm-4 text-center w-80 d-none d-lg-block align-self-center m-0 p-0">
                    RentTrack Pro is a rental monitoring system, designed to cater to the needs of the landlord/s
                    for a system for managing house rentals efficiently, leveraging the power of web technologies to streamline
                    the processing and storage of tenant, payments and house information.
                </p>
            </div>
            <div class="col-12 col-lg-6 m-0 p-0" id="carousellpics">
                <div class="row m-0 h-100 justify-content-center p-0">
                    <!-- <div id="carouselExampleControls_1" class="carousel slide align-self-center m-0 p-0" data-bs-ride="carousel">
                        <div class="carousel-inner m-0 p-0">
                            <div class="carousel-item active m-0 p-0">
                                <img src="asset/Chalet-04633e05326048b3a8765fc6a646ca74.jpg" style="object-fit: cover; width: 100%; height: 300px;" class="mx-auto d-block" alt="...">
                            </div>
                            <div class="carousel-item m-0 p-0">
                                <img src="asset/blackbasin-warm-5987.jpg" style="object-fit: cover; width: 100%; height: 300px;" class="mx-auto d-block" alt="...">
                            </div>
                            <div class="carousel-item m-0 p-0">
                                <img src="asset/photo-1484931627545-f6d9b3aaa6eb.jfif" style="object-fit: cover; width: 100%; height: 300px;" class="mx-auto d-block" alt="...">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls_1" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls_1" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div> -->
                    <div id="carouselExampleControls" class="carousel slide" data-bs-pause="true" style="max-width: 500px;">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="asset/house_1_test.jpg" class="d-block w-100" style="object-fit: cover;" height="300" alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="asset/house_1_test.jpg" class="d-block w-100" style="object-fit: cover;" height="300" alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="asset/house_1_test.jpg" class="d-block w-100" style="object-fit: cover;" height="300" alt="...">
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
                </div>
            </div>

        </div>
    </div>
    <div class="row mx-auto w-65 d-flex align-items-center p-0" style="margin-bottom: 100px;">

        <div class="row mt-5 m-0 p-0">
         
            <div class="col-12 col-lg-6 m-0 p-0" id="carousellpics">
                <div class="row m-0 h-100 justify-content-center p-0">
                    <!-- <div id="carouselExampleControls" class="carousel slide align-self-center m-0 p-0" data-bs-ride="">
                        <div class="carousel-inner m-0 p-0">
                            <div class="carousel-item active m-0 p-0">
                                <img src="asset/Chalet-04633e05326048b3a8765fc6a646ca74.jpg" style="object-fit: cover; width: 100%; height: 300px;" class="mx-auto d-block" alt="...">
                            </div>
                            <div class="carousel-item m-0 p-0">
                                <img src="asset/blackbasin-warm-5987.jpg" style="object-fit: cover; width: 100%; height: 300px;" class="mx-auto d-block" alt="...">
                            </div>
                            <div class="carousel-item m-0 p-0">
                                <img src="asset/photo-1484931627545-f6d9b3aaa6eb.jfif" style="object-fit: cover; width: 100%; height: 300px;" class="mx-auto d-block" alt="...">
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
                    </div> -->
                    <div id="carouselExampleControlsII" class="carousel slide" data-bs-pause="true" style="max-width: 500px;">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="asset/house_1_test.jpg" class="d-block w-100" style="object-fit: cover;" height="300" alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="asset/house_1_test.jpg" class="d-block w-100" style="object-fit: cover;" height="300" alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="asset/house_1_test.jpg" class="d-block w-100" style="object-fit: cover;" height="300" alt="...">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControlsII" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControlsII" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 d-flex flex-column justify-content-center m-0 p-0" id="textindex-1">
                <p class="fs-1 fw-bolder inter-google text-center mb-lg-3 m-0 p-0" style="color: #F28543;">Goals</p>
                <p class="fs-5 inter-google mx-sm-4 text-center d-lg-none m-0 p-0">
                  RentTrack Pro's goal is to streamline rental management for landlords by optimizing tenant data, simplifying payment processes, 
                    and accurately tracking property information. Through advanced web technologies, it aims to enhance efficiency and ease of use 
                    in property management.
                </p>
                <p class="fs-10 inter-google mx-sm-4 text-center w-80 d-none d-lg-block align-self-center m-0 p-0">
                    RentTrack Pro's goal is to streamline rental management for landlords by optimizing tenant data, simplifying payment processes, 
                    and accurately tracking property information. Through advanced web technologies, it aims to enhance efficiency and ease of use 
                    in property management.
                </p>
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

<?php
    $sql = "SELECT eviction_popup.*, users.firstname, users.middlename, users.lastname
    FROM eviction_popup 
    INNER JOIN users ON users.id = $admin->session_id
    WHERE eviction_popup.users_id = $admin->session_id AND eviction_popup.seen = '' ";
    $result = $admin->conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Display the modal
        echo "
        <div class='modal fade' id='myModal' tabindex='-1' aria-labelledby='modalLabel' aria-hidden='true'>
            <div class='modal-dialog' style='margin: 0; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 700px;'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='modalLabel'>Eviction Notice</h5>
                    </div>
                    <div class='modal-body'>
                        Dear {$row['firstname']} {$row['lastname']}, check your email for eviction notice or download it with the button below.
                    </div>
                    <div class='row mb-3 justify-content-center' style='padding: .75rem;'>
                        <div class='row d-flex mb-3 justify-content-center'>
                            <iframe src='" . "asset/eviction_tenant/" . $row['file_path'] . "' class='w-100' style='height: 600px;'>
                            </iframe>
                        </div>
                        <a href='" . "./asset/eviction_tenant/" . ($row['file_path']) . "' class='d-block btn btn-secondary btn-download table-buttons-update' download='" . ($row['file_path']). "' style='text-align: center; max-width: 150px; max-height: 38px;'>Download</a>
                    </div>
                    <div class='modal-footer'>
                        <button id='closeBtn' type='button' class='btn btn-secondary' data-bs-dismiss='modal' style='display: none;'>Close</button>
                    </div>
                </div>
            </div>
        </div>
    
        <script>
            // Initialize the Bootstrap modal
            var myModal = new bootstrap.Modal(document.getElementById('myModal'), {
                backdrop: 'static', // Prevent closing by clicking outside
                keyboard: false    // Disable closing with the Esc key
            });
            myModal.show();
    
            // Show the close button after 10 seconds and update the database
            setTimeout(function() {
                document.getElementById('closeBtn').style.display = 'block';
    
                // Make an AJAX call to update the database
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'eviction_seen_status.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('user_id=$admin->session_id');
                
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        console.log('Seen status updated successfully.');
                    } else {
                        console.error('Error updating seen status.');
                    }
                };
            }, 10000);
    
            // Close the modal when the button is clicked
            document.getElementById('closeBtn').onclick = function() {
                myModal.hide();
            };
        </script>
        ";
    }
?>

<?php include 'regular/includes/footer.php'; ?>