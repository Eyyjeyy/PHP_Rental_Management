<?php
include 'db_connect.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    $sql = "UPDATE eviction_popup SET seen = 'true' WHERE users_id = $user_id AND seen = ''";
    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>