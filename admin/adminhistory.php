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

    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page

    $sql = "SELECT history.id AS history_id, history.*, users.*
            FROM history
            LEFT JOIN users ON history.admin_id = users.id;";
    $result = $admin->conn->query($sql);

    $page = "adminhistory";
?>


    <div class="container-fluid">
        <div class="row">
         
        <?php include 'includes/header.php'; ?>

            <div class="col main content">
                <div class="card-body">
                    <div class="table-responsive"  id="tablelimiter">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Admin</th>
                                    <th scope="col">Action</th>
                                    <th scope="col">Details</th>
                                    <th scope="col" style="max-width: 80px;">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    // Output data of each row
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<th scope='row'>" . $row['history_id'] . "</th>";
                                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['action']) . "</td>";
                                        echo "<td>" . $row['details'] . " </td>";
                                        echo "<td>" . $row['timestamp'] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No users found</td></tr>";
                                }
                                // $admin->conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>  
                </div>
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
                             
                <!-- <p>Home</p> -->
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
