<?php
// Include your database connection file
include("db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);

    // Query the tenants table for the user's name
    $query = "SELECT fname, lname FROM tenants WHERE users_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($firstname, $lastname);
    if ($stmt->fetch()) {
        echo htmlspecialchars($firstname . " " . $lastname);
    } else {
        echo "No Tenant Found";
    }
    $stmt->close();
}
$conn->close();
?>