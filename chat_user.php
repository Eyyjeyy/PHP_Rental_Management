


<?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);
    
    include 'admin.php';
    $admin = new Admin();

    if(!$admin->isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    if($admin->isLoggedIn() && $admin->session_role == 'admin') {
        header("Location: admin/admindashboard.php");
        exit();
    }

    $user_id = $admin->session_id;
    $chat_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    $_SESSION['chat_user_id'] = $chat_user_id;


    $users = $admin->getAdminOnly();
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
                    ?>
                    <div class="message <?php echo $class; ?>">
                        <p><strong><?php echo $sender; ?>:</strong> <?php echo $message; ?></p>
                        <?php if ($image_path): ?>
                            <img src="<?php echo $image_path; ?>" alt="Image">
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
        $file_path = null;
    
        if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
            $file_tmp_path = $_FILES['file']['tmp_name'];
            $file_name = $_FILES['file']['name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'ogg');
    
            if (in_array($file_extension, $allowed_extensions)) {
                $upload_file_dir = './uploads/';
                $dest_path = $upload_file_dir . uniqid() . '.' . $file_extension;
    
                if (move_uploaded_file($file_tmp_path, $dest_path)) {
                    $file_path = $dest_path;
                } else {
                    echo "File could not be uploaded.";
                }
            } else {
                echo "Invalid file extension.";
            }
        }
    
        if (!empty($message) || $file_path) {
            $chat_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
            $admin->sendMessage($user_id, $chat_user_id, $message, $file_path);
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
    // Determine the base URL
    $base_url = dirname($_SERVER['SCRIPT_NAME']) . '/';
?>

<?php include 'regular/includes/header_user.php'; ?>


<div class="container-fluid" style="padding: 0;">
    <div class="row mx-auto w-65 justify-content-center" id="chatwhole">
    <div class="row mt-5 mx-0" id="coluserchat">
        <div class="col-12 col-md-4 pe-0" id="usercol">
            <div class="card h-100">
                <div class="card-header" style="background-color: #EE7214;">
                    <h5 class="text-center mb-0" style="font-size: 1.2rem; font-weight: bold;">Users</h5>
                </div>
                <div class="card-body bg-#F9E8D9" id="list" style="overflow-y: auto; height: 80%;">
                    <ul class="ps-0" style="list-style: none;">
                        <?php foreach ($users as $user): ?>
                            <li>
                                <a href="chat_user.php?user_id=<?php echo $user['id']; ?>" data-user-id="<?php echo $user['id']; ?>" class="text-decoration-none" style="color: #2C3E50;">
                                    <p class="fs-5 mb-2">
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
        <div class="col-12 col-md-8 ps-0" id="chatcol">            
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="text-center mb-0" style="font-size: 1.2rem; font-weight: bold;">Chat</h5>
                </div>
                <div class="card-body bg-#F9E8D9 h-100 chatcontent" style="overflow-y: auto; max-height: 60vh;">
                    <div class="messages" style="max-height: 100%;">
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
                                                <img src="<?php echo $image_path; ?>" class="img-fluid" alt="Image">
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
                </div>
                <!-- Send Message Form -->
                <div id="form-container" class="flex-grow-1">
                    <?php if ($chat_user_id): ?>
                        <form id="message-form" action="chat_user.php?user_id=<?php echo $chat_user_id; ?>" method="POST" enctype="multipart/form-data" style="height: 15%;">
                            <div class="row h-100 m-0" id="textbtnzone">
                                <div class="textzone-2">
                                    <div class="row m-0" id="textzone">
                                        <textarea class="w-100" name="message" id="message-input" placeholder="Type your message here..." required></textarea>
                                    </div>
                                </div>
                                <div class="col-3 p-0 pt-2" id="btnzone">
                              
                                <div class="btn-2">
                                    <label id="file-name-label">No file selected</label>
                                    <input type="file" id="file" name="file" style="display:none;">
                                    <button type="submit" style="border-style: none; border-radius: 4px;" id="file-btn" class="btn btn-primary">Upload File</button>

                                    <button class="btn btn-primary" id="send-btn" style="border-style: none; border-radius: 4px;" type="submit">Send</button>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function scrollToBottom() {
            $('.chatcontent').scrollTop($('.chatcontent')[0].scrollHeight);
        }

        var userId = <?php echo $user_id; ?>; // Assuming $user_id is set in PHP
        var chatUserId = <?php echo $chat_user_id; ?>;

        function initSSE(chatUserId) {
            if (typeof(EventSource) !== "undefined") {
                var source = new EventSource("fetch_chat.php?user_id=" + userId + "&chat_user_id=" + chatUserId);

                source.onmessage = function(event) {
                    var data = JSON.parse(event.data);
                    var messagesHtml = '';

                    data.forEach(function(row) {
                        var sender = row.sender_id == userId ? 'You' : row.sender_username;
                        var messageClass = row.sender_id == userId ? 'message-right' : 'message-left';

                        // Generate unique ID for modal based on message ID
                        var modalId = 'fileModal' + row.id;

                        var messageHtml = '<div class="message ' + messageClass + '">';
                        messageHtml += '<p><strong>' + sender + ':</strong> ' + row.message + '</p>';

                        if (row.image_path) {
                            var fileExtension = row.image_path.split('.').pop().toLowerCase();

                            if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                                // Add link to trigger modal for image
                                messageHtml += '<a href="#" data-bs-toggle="modal" data-bs-target="#' + modalId + '">';
                                messageHtml += '<img src="' + row.image_path + '" alt="Image" class="chat-image" style="max-width: 100px; max-height: 100px; cursor: pointer;">';
                                messageHtml += '</a>';

                                // Add modal HTML for image
                                messageHtml += '<div class="modal fade" id="' + modalId + '" tabindex="-1" aria-labelledby="' + modalId + 'Label" aria-hidden="true">';
                                messageHtml += '<div class="modal-dialog modal-dialog-centered">';
                                messageHtml += '<div class="modal-content">';
                                messageHtml += '<div class="modal-header">';
                                messageHtml += '<h5 class="modal-title" id="' + modalId + 'Label">Image Preview</h5>';
                                messageHtml += '<button type="button" class="btn-svg" data-bs-dismiss="modal" aria-label="Close">';
                                messageHtml += '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">';
                                messageHtml += '<path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>';
                                messageHtml += '</svg>';
                                messageHtml += '</button>';
                                messageHtml += '</div>';
                                messageHtml += '<div class="modal-body mx-auto">';
                                messageHtml += '<img src="' + row.image_path + '" alt="Image" class="img-fluid" style="max-width: 100%;">';
                                messageHtml += '</div>';
                                messageHtml += '</div>';
                                messageHtml += '</div>';
                                messageHtml += '</div>';
                            } else if (['mp4', 'webm', 'ogg'].includes(fileExtension)) {
                                // Inline video player
                                messageHtml += '<video controls class="mw-100">';
                                messageHtml += '<source src="' + row.image_path + '" type="video/' + fileExtension + '">';
                                messageHtml += 'Your browser does not support the video tag.';
                                messageHtml += '</video>';
                            }
                        }

                        messageHtml += '<span class="timestamp">' + row.timestamp + '</span>';
                        var seenStatus = (row.seen == 1 && row.sender_id == userId) ? '<span class="seen-status">Seen</span>' : '';
                        if (messageClass === 'message-right') {
                            messageHtml += seenStatus;
                        }
                        messageHtml += '</div>';

                        messagesHtml += messageHtml;
                    });

                    $('.messages').html(messagesHtml);
                };

                source.onerror = function(event) {
                    console.error('Error with SSE:', event);
                };

                return source;
            } else {
                console.error("Your browser does not support SSE.");
                return null;
            }
        }

        var currentSource = null;
        function switchChatUser(newChatUserId) {
            if (currentSource) {
                currentSource.close();
            }
            currentSource = initSSE(newChatUserId);

            // Scroll to the bottom when a new user is selected
            setTimeout(scrollToBottom, 100);  // Add a delay to ensure messages are loaded first

            showSendMessageForm(newChatUserId);
        }

        // Initial fetch of messages for the first user
        
        if (chatUserId) {
            switchChatUser(chatUserId);
        }

        // Handle user selection
        $('a[data-user-id]').on('click', function(e) {
            e.preventDefault();
            var newChatUserId = $(this).data('user-id');
            chatUserId = newChatUserId;
            history.pushState(null, '', '?user_id=' + newChatUserId);
            switchChatUser(newChatUserId);
            markMessagesSeen();
        });

        

        function showSendMessageForm(chatUserId) {
            if (chatUserId) {
                var formHtml = `
                    <form id="message-form" action="chat_user.php?user_id=${chatUserId}" method="POST" enctype="multipart/form-data" style="height: 15%;">
                        <div class="row h-100 m-0" id="textbtnzone">
                            <div class="textzone-2">
                                <div class="row m-0" id="textzone">
                                    <textarea class="w-100" name="message" id="message-input" placeholder="Type your message here..."></textarea>
                                </div>
                            </div>
                            <div class="col-3 p-0" id="btnzone">
                                <div class="btn-2">
                                    <label for="file" id="file-name-label" class="btn btn-primary w-100">No file selected</label>
                                    <input type="file" id="file" name="file" style="display:none;">
                                    <button type="submit" style="border-style: none; border-radius: 4px;" id="file-btn" class="btn btn-primary mt-2">Upload File</button>
                                    <button class="btn btn-primary mt-2" id="send-btn" style="border-style: none; border-radius: 4px;" type="submit">Send</button>
                                </div>
                            </div>
                        </div>
                    </form>
                `;
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
                $('#form-container').html(formHtml);

                // Re-bind form submission handler
                $('#message-form').on('submit', function(e) {
                    e.preventDefault();

                    var formData = new FormData(this);
                    $.ajax({
                        url: 'chat_user.php?user_id=' + chatUserId,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log('Form submitted successfully:', response);
                            $('#message-input').val('');
                            $('#file').val('');
                            $('#file-name-label').text('No file selected'); // Reset the file label
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('Error submitting form:', textStatus, errorThrown);
                        }
                    });
                });
            }
        }

        // Initial call to display the send message form
        showSendMessageForm(chatUserId);

        // Function to mark messages as seen
        function markMessagesSeen() {
            $.post('mark_seen.php', { user_id: userId, chat_user_id: chatUserId }, function(response) {
                console.log('Messages marked as seen:', response);
            });
        }

        // If the chat window is in focus, mark messages as seen
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


<!-- No Realtime Update from Receiving Other User Message -->
<!-- <script>
    $(document).ready(function(){
        // Fetch messages initially
        function fetchMessages() {
            var userId = <?php echo $chat_user_id; ?>; // Assuming $chat_user_id is set in PHP
            $.ajax({
                url: 'chat_user.php?fetch_messages=1&user_id=' + userId,
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
                url: 'chat_user.php?user_id=' + userId2, // Same PHP file
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




<?php include 'regular/includes/footer.php'; ?>