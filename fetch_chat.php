<?php
header('Cache-Control: no-store');
header('Content-Type: text/event-stream');

include("db_connect.php");

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$chat_user_id = isset($_GET['chat_user_id']) ? intval($_GET['chat_user_id']) : 0;

if (!$user_id || !$chat_user_id) {
    exit();
}

$previousData = '';
while (true) {
    $query = "SELECT m.*, 
                u1.username AS sender_username, 
                u2.username AS receiver_username
                FROM messages m
                LEFT JOIN users u1 ON m.sender_id = u1.id
                LEFT JOIN users u2 ON m.receiver_id = u2.id
                WHERE (m.sender_id = '$user_id' AND m.receiver_id = '$chat_user_id') 
                OR (m.sender_id = '$chat_user_id' AND m.receiver_id = '$user_id')
                ORDER BY m.timestamp";
    $result = $conn->query($query);

    $currentData = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $currentData[] = $row;
        }
    }
    
    $currentDataJson = json_encode($currentData);

    if ($currentDataJson !== $previousData) {
        // Data will show on change
        echo "data: " . $currentDataJson . "\n\n";
        $previousData = $currentDataJson;
    }

    // Ensure that the buffer is flushed immediately
    ob_end_flush();
    flush();

    // Sleep for a while before checking for changes again
    sleep(3);
}
?>