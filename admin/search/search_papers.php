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
    SELECT COUNT(*) as total FROM paper_files
    WHERE
        paper_files.file_name LIKE '%$query%' OR
        paper_files.category_name LIKE '%$query%' OR
        paper_files.uploaded_at LIKE '%$query%'
";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $records_per_page);

// Fetch paginated, sorted records
// $sql = "
//     SELECT * FROM paper_files
//     WHERE 
//         paper_files.file_name LIKE '%$query%' OR
//         paper_files.category_name LIKE '%$query%' OR
//         paper_files.uploaded_at LIKE '%$query%'
//     ORDER BY $sort_column $sort_order
//     LIMIT $offset, $records_per_page
// ";
// $result = $conn->query($sql);

// Use the commented code above instead and uncomment lines 81-85 to bring back pagination feature
$sql = "
    SELECT * FROM paper_files
    WHERE 
        paper_files.file_name LIKE '%$query%' OR
        paper_files.category_name LIKE '%$query%' OR
        paper_files.uploaded_at LIKE '%$query%'
    ORDER BY $sort_column $sort_order
";
$result = $conn->query($sql);



// Generate table rows based on search results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']). "</td>";
        echo "<td>" . htmlspecialchars($row['file_name']). "</td>";
        echo "<td>" . htmlspecialchars($row['category_name']). "</td>";
        echo "<td>" . htmlspecialchars($row['uploaded_at']). "</td>";
        echo "<td>";
        echo "<div class='d-flex justify-content-center'>
                <div class='row m-0'>
                    <div class='col d-flex justify-content-center mb-3 px-2'>
                        <button id='deletefile' class='btn btn-danger btn-delete table-buttons-delete' name='delete_file' data-id='". htmlspecialchars($row['id']). "' style='width: 100px;'>Delete</button>
                    </div>
                    <div class='col d-flex justify-content-center px-2'>
                        <a href='". htmlspecialchars($row['file_url']). "' class='btn btn-primary btn-download table-buttons-update' download='". htmlspecialchars($row['file_name']). "' style='width: 100px; text-align: center; max-height: 38px;'>Download</a>
                    </div>
                </div>
                
                
            </div>";
            
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='10'>No Payments found</td></tr>";
}

// Output pagination buttons
// echo "<tr><td colspan='10' class='text-center'>";
// for ($i = 1; $i <= $total_pages; $i++) {
//     echo "<button class='btn btn-secondary pagination-btn' data-page='$i'>$i</button> ";
// }
// echo "</td></tr>";

$conn->close();
?>
