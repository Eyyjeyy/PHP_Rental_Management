<?php
include '../../db_connect.php'; // Include your database connection

// Get the search query and trim any leading/trailing whitespace
$query = isset($_POST['query']) ? trim($_POST['query']) : '';

// Build the SQL query
$sql = "
    SELECT tenants.*, houses.house_name AS house_name
            FROM tenants
            LEFT JOIN houses ON tenants.house_id = houses.id
";

// Add the WHERE clause only if there’s a search query
if (!empty($query)) {
    $sql .= " WHERE 
        tenants.fname LIKE '%$query%' OR
        tenants.mname LIKE '%$query%' OR
        tenants.lname LIKE '%$query%' OR
        tenants.users_username LIKE '%$query%' OR
        tenants.date_start LIKE '%$query%' OR
        tenants.date_preferred LIKE '%$query%'
    ";
}

// Append the ORDER BY clause
// $sql .= " ORDER BY $sortColumn $sortDirection;";

$result = $conn->query($sql);


// Generate table rows based on search results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<th scope='row'>" . $row['id'] . "</th>";
        echo "<td>" . htmlspecialchars($row['fname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['mname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['lname']) . "</td>";
        // echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
        echo "<td>" . htmlspecialchars($row['users_username']) . "</td>";
        echo "<td>Category: " . htmlspecialchars($row['house_category']) . "<br>House Name: " . htmlspecialchars($row['house_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['date_start']) . "</td>";
        echo "<td>" . htmlspecialchars($row['date_preferred']) . "</td>";
        echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
        echo "<div class='row justify-content-center m-0'>";
        echo "<div class='col-xl-6 px-2'>";
        // Add a form with a delete button for each record
        echo "<form method='POST' action='admintenants.php' class='float-xl-end align-items-center' style='height:100%;'>";
        echo "<input type='hidden' name='tenantid' value='" . $row['id'] . "'>";
        echo "<button type='submit' name='delete_tenant' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>";
        echo "</form>";
        echo "</div>";
        echo "<div class='col-xl-6 px-2'>";
        // Add a form with a update button for each record
        echo "<input type='hidden' name='tenantid' value='" . $row['id'] . "'>";
        echo "<button type='button' class='btn btn-primary update-tenant-btn float-xl-start table-buttons-update' data-id='" . $row['id'] . "' data-tenantname='" . htmlspecialchars($row['fname']) . "' data-middlename= '" . htmlspecialchars($row['mname']) . "' data-lastname= '" . htmlspecialchars($row['lname']) . "' data-contactno= '" . htmlspecialchars($row['contact']) . "' data-registerdate= '" . htmlspecialchars($row['date_start']) . "' style='width: 80px;'>Update</button>";
        echo "</div>";
        echo "</div>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='10'>No Apartments found</td></tr>";
}

$conn->close();
?>
