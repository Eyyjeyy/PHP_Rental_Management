<?php
include 'admin.php';
$admin = new Admin();
include("db_connect.php"); // Include your database connection file

if (isset($_POST['tenant_id'])) {
    $tenant_id = intval($_POST['tenant_id']); // Sanitize the input
    $query = "SELECT * FROM tenants LEFT JOIN houses ON houses.id = tenants.house_id WHERE tenants.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tenant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo $row['address'] . ',' . $row['price'];
    } else {
        echo "Address not found.";
    }
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
