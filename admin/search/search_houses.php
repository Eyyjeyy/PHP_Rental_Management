<?php
include '../../db_connect.php'; // Include your database connection

// Get search, pagination, and sorting parameters
$query = isset($_POST['query']) ? trim($_POST['query']) : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$sort_column = isset($_POST['sort_column']) ? $_POST['sort_column'] : 'houses.id'; // Default sorting by house ID
$sort_order = isset($_POST['sort_order']) ? $_POST['sort_order'] : 'ASC'; // Default sorting in ascending order
$records_per_page = 5; // Adjust as needed

// Calculate the offset for pagination
$offset = ($page - 1) * $records_per_page;

// Get the total number of matching records for pagination
$total_sql = "
    SELECT COUNT(*) as total FROM houses
    INNER JOIN categories ON categories.id = houses.category_id
    LEFT JOIN houseaccounts ON houses.id = houseaccounts.houses_id
    WHERE
        houses.house_name LIKE '%$query%' OR
        categories.name LIKE '%$query%' OR
        houseaccounts.elec_accnum LIKE '%$query%' OR
        houseaccounts.elec_accname LIKE '%$query%' OR
        houseaccounts.water_accname LIKE '%$query%' OR
        houseaccounts.water_accnum LIKE '%$query%'
";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $records_per_page);

// Fetch paginated, sorted records
// $sql = "
//     SELECT houses.*, categories.name AS category_name, houseaccounts.elec_accnum, houseaccounts.elec_accname, 
//            houseaccounts.water_accname, houseaccounts.water_accnum
//     FROM houses
//     INNER JOIN categories ON categories.id = houses.category_id
//     LEFT JOIN houseaccounts ON houses.id = houseaccounts.houses_id
//     WHERE 
//         houses.house_name LIKE '%$query%' OR
//         categories.name LIKE '%$query%' OR
//         houseaccounts.elec_accnum LIKE '%$query%' OR
//         houseaccounts.elec_accname LIKE '%$query%' OR
//         houseaccounts.water_accname LIKE '%$query%' OR
//         houseaccounts.water_accnum LIKE '%$query%'
//     ORDER BY $sort_column $sort_order
//     LIMIT $offset, $records_per_page
// ";
// $result = $conn->query($sql);

// Use the commented code above instead and uncomment lines 104-108 to bring back pagination feature
$sql = "
    SELECT houses.*, categories.name AS category_name, houseaccounts.elec_accnum, houseaccounts.elec_accname, 
           houseaccounts.water_accname, houseaccounts.water_accnum
    FROM houses
    INNER JOIN categories ON categories.id = houses.category_id
    LEFT JOIN houseaccounts ON houses.id = houseaccounts.houses_id
    WHERE 
        houses.house_name LIKE '%$query%' OR
        categories.name LIKE '%$query%' OR
        houseaccounts.elec_accnum LIKE '%$query%' OR
        houseaccounts.elec_accname LIKE '%$query%' OR
        houseaccounts.water_accname LIKE '%$query%' OR
        houseaccounts.water_accnum LIKE '%$query%'
    ORDER BY $sort_column $sort_order
";
$result = $conn->query($sql);

// Generate table rows based on search results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<th scope='row'>" . $row['id'] . "</th>";
        echo "<td>" . htmlspecialchars($row['house_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['elec_accnum']) . "</td>";
        echo "<td>" . htmlspecialchars($row['elec_accname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['water_accnum']) . "</td>";
        echo "<td>" . htmlspecialchars($row['water_accname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['address']) . "</td>";
        echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
        echo "<div class='row justify-content-center m-0'>";
        echo "<div class='col-xxl-6 px-2'>";
        // Add a form with a delete button for each record
        echo "<form method='POST' action='adminhouses.php' class='float-xxl-end align-items-center'>";
        echo "<input type='hidden' name='house_id' value='" . $row['id'] . "'>";
        echo "<button type='submit' name='delete_house' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>";
        echo "</form>";
        echo "</div>";
        echo "<div class='col-xxl-6 px-2'>";
        // Add a form with a update button for each record
        echo "<input type='hidden' name='house_id' value='" . $row['id'] . "'>";
        echo "<button type='button' class='btn btn-primary update-house-btn float-xxl-start table-buttons-update' data-id='" . $row['id'] . "' data-housenumber='" . htmlspecialchars($row['house_name']) . "' data-price='" . htmlspecialchars($row['price']) . "' data-categoryid='" . htmlspecialchars($row['category_id']) . "' data-meralconum='" . htmlspecialchars($row['elec_accnum']) . "' data-meralconame='" . htmlspecialchars($row['elec_accname']) . "' data-mayniladnum='" . htmlspecialchars($row['water_accnum']) . "' data-mayniladname='" . htmlspecialchars($row['water_accname']) . "' data-address='" . htmlspecialchars($row['address']) . "' style='width: 80px;'>Update</button>";
        echo "</div>";
        echo "</div>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='10'>No Apartments found</td></tr>";
}

// Output pagination buttons
// echo "<tr><td colspan='10' class='text-center'>";
// for ($i = 1; $i <= $total_pages; $i++) {
//     echo "<button class='btn btn-secondary pagination-btn' data-page='$i'>$i</button> ";
// }
// echo "</td></tr>";

$conn->close();
?>
