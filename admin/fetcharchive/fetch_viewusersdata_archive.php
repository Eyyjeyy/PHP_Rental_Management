<?php
// Include your database connection file
include("../../db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['viewData_user_id'])) {
    $viewData_user_id = intval($_POST['viewData_user_id']);

    // Query the tenants table for the user's name
    $query = "SELECT * 
    FROM archives
    WHERE users_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $viewData_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "
                <tr>
                    <td>{$row['id']}</td>
                    <td>{$row['archive_users_username']}</td>
                    <td>{$row['archive_users_firstname']}</td>
                    <td>{$row['archive_users_middlename']}</td>
                    <td>{$row['archive_users_lastname']}</td>
                    <td>{$row['archive_users_phonenumber']}</td>
                    <td>{$row['archive_users_email']}</td>
                    <td>{$row['archive_users_password']}</td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No Tenant Found</td></tr>";
    }
    $stmt->close();
}
$conn->close();
?>