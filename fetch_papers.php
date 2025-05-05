<?php
header('Cache-Control: no-store');
header('Content-Type: text/event-stream');

include("db_connect.php");

$previousData = '';
while (true) {
    $query = "SELECT * FROM paper_categories";
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