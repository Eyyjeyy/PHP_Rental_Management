<?php
include '../../db_connect.php'; // Include your database connection
// Assuming you're connected to the database already

// Check if the 'recordId' is sent via POST
if (isset($_POST['recordId'])) {
    $recordId = $_POST['recordId'];

    // Sanitize the input to avoid SQL injection
    // $stmt = $conn->prepare("SELECT * FROM deposit WHERE id = ?");
    $stmt = $conn->prepare("
        SELECT 
            deposit.*, 
            users.firstname, 
            users.middlename, 
            users.lastname 
        FROM deposit
        JOIN tenants ON deposit.tenantid = tenants.id
        JOIN users ON tenants.users_id = users.id
        WHERE deposit.id = ?
    ");
    $stmt->bind_param("i", $recordId);  // 'i' means the parameter is an integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the record and return it as JSON
        $row = $result->fetch_assoc();
        echo json_encode($row);  // Return the fetched data as JSON
    } else {
        echo json_encode(['error' => 'No record found']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'No recordId provided']);
}
?>
