<?php
include '../../db_connect.php'; // Include your database connection

// Get the search query and trim any leading/trailing whitespace
$query = isset($_POST['query']) ? trim($_POST['query']) : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$sort_column = isset($_POST['sort_column']) ? $_POST['sort_column'] : 'expenses.id'; // Default sorting by expenses ID
$sort_order = isset($_POST['sort_order']) ? $_POST['sort_order'] : 'ASC'; // Default sorting in ascending order
$records_per_page = 10; // Adjust as needed

// Calculate the offset for pagination
$offset = ($page - 1) * $records_per_page;

// // Build the SQL query
// $sql = "SELECT expenses.*, houses.house_name, houses.id AS housingid
//             FROM expenses
//             LEFT JOIN houses ON expenses.house_id = houses.id";

// // Add the WHERE clause only if there’s a search query
// if (!empty($query)) {
//     $sql .= " WHERE 
//         expenses.name LIKE '%$query%' OR
//         expenses.info LIKE '%$query%' OR
//         houses.house_name LIKE '%$query%'
//     ";
// }


// $result = $conn->query($sql);

// Get the total number of matching records for pagination
$total_sql = "
    SELECT COUNT(*) as total FROM expenses
    LEFT JOIN houses ON expenses.house_id = houses.id
    WHERE
        expenses.name LIKE '%$query%' OR
        expenses.info LIKE '%$query%' OR
        houses.house_name LIKE '%$query%'
";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $records_per_page);

// Fetch paginated, sorted records
$sql = "
    SELECT expenses.*, houses.house_name, houses.id AS housingid
    FROM expenses
    LEFT JOIN houses ON expenses.house_id = houses.id
    WHERE 
        expenses.name LIKE '%$query%' OR
        expenses.info LIKE '%$query%' OR
        houses.house_name LIKE '%$query%'
    ORDER BY $sort_column $sort_order
    LIMIT $offset, $records_per_page
";

$result_tenant_table = $conn->query($sql);


// Generate table rows based on search results
if ($result_tenant_table->num_rows > 0) {
    while ($row = $result_tenant_table->fetch_assoc()) {
        $expenses[] = $row; // Collect data for the JavaScript array
        echo "<tr>";
        echo "<th scope='row'>" . $row['id'] . "</th>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['info']) . "</td>";
        echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['house_name']) . "</td>";
        echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
        echo "<div class='row justify-content-center m-0'>";
        echo "<div class='col-xl-6 px-2'>";
        // Add a form with a delete button for each record
        echo "<form method='POST' action='adminexpenses.php' class='float-xl-end align-items-center'>";
        echo "<input type='hidden' name='expensesid' value='" . $row['id'] . "'>";
        echo "<button type='submit' name='delete_expenses' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>";
        echo "</form>";
        echo "</div>";
        echo "<div class='col-xl-6 px-2'>";
        // Add a form with a update button for each record
        echo "<input type='hidden' name='expensesid' value='" . $row['id'] . "'>";
        echo "<button type='button' class='btn btn-primary update-expenses-btn float-xl-start table-buttons-update' data-id='" . $row['id'] . "' data-expensesname='" . htmlspecialchars($row['name']) . "'data-expensesinfo='" . htmlspecialchars($row['info']) . "'data-expensesamount='" . htmlspecialchars($row['amount']) .  "' style='width: 80px;'>Update</button>";
        echo "</div>";
        echo "</div>";
        echo "</td>";
        echo "</tr>";
    }
    // echo "<script>var arrayData = " . json_encode($expenses) . ";</script>"; // Pass the data to JavaScript
} else {
    echo "<tr><td colspan='10'>No Payments found</td></tr>";
}

// Output pagination buttons
echo "<tr><td colspan='10' class='text-center'>";
for ($i = 1; $i <= $total_pages; $i++) {
    echo "<button class='btn btn-secondary pagination-btn' data-page='$i'>$i</button> ";
}
echo "</td></tr>";

$conn->close();
?>
