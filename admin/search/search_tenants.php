<?php
include '../../db_connect.php'; // Include your database connection

// Get search, pagination, and sorting parameters
$query = isset($_POST['query']) ? trim($_POST['query']) : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$sort_column = isset($_POST['sort_column']) ? $_POST['sort_column'] : 'houses.id'; // Default sorting by house ID
$sort_order = isset($_POST['sort_order']) ? $_POST['sort_order'] : 'ASC'; // Default sorting in ascending order
$records_per_page = 10; // Adjust as needed

// Calculate the offset for pagination
$offset = ($page - 1) * $records_per_page;

// Get the total number of matching records for pagination
$total_sql = "
    SELECT COUNT(*) as total FROM tenants
    LEFT JOIN houses ON tenants.house_id = houses.id
    WHERE
        tenants.fname LIKE '%$query%' OR
        tenants.mname LIKE '%$query%' OR
        tenants.lname LIKE '%$query%' OR
        tenants.users_username LIKE '%$query%' OR
        tenants.date_start LIKE '%$query%' OR
        tenants.date_preferred LIKE '%$query%'
";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $records_per_page);

// Fetch paginated, sorted records
$sql = "
    SELECT tenants.*, houses.house_name AS house_name
            FROM tenants
            LEFT JOIN houses ON tenants.house_id = houses.id
    WHERE 
        tenants.fname LIKE '%$query%' OR
        tenants.mname LIKE '%$query%' OR
        tenants.lname LIKE '%$query%' OR
        tenants.users_username LIKE '%$query%' OR
        tenants.date_start LIKE '%$query%' OR
        tenants.date_preferred LIKE '%$query%'
    ORDER BY $sort_column $sort_order
    LIMIT $offset, $records_per_page
";


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

// Output pagination buttons
echo "<tr><td colspan='10' class='text-center'>";
for ($i = 1; $i <= $total_pages; $i++) {
    echo "<button class='btn btn-secondary pagination-btn' data-page='$i'>$i</button> ";
}
echo "</td></tr>";

$conn->close();
?>
