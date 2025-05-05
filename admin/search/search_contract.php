<?php
include '../../admin.php';
include '../../db_connect.php'; // Include your database connection

$admin1 = new Admin();

// Get the search query and trim any leading/trailing whitespace
$query = isset($_POST['query']) ? trim($_POST['query']) : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$sort_column = isset($_POST['sort_column']) ? $_POST['sort_column'] : 'contracts.id'; // Default sorting by contract ID
$sort_order = isset($_POST['sort_order']) ? $_POST['sort_order'] : 'ASC'; // Default sorting in ascending order
$records_per_page = 5; // Adjust as needed

// Calculate the offset for pagination
$offset = ($page - 1) * $records_per_page;

// Build the SQL query
$sql = "SELECT * FROM contracts";

// Add the WHERE clause only if there’s a search query
// if (!empty($query)) {
//     $sql .= " WHERE 
//         tenantname LIKE '%$query%' OR
//         tenantapproval LIKE '%$query%' OR
//         datestart LIKE '%$query%' OR
//         expirationdate LIKE '%$query%'
//     ";
// }

// Get the total number of matching records for pagination
$total_sql = "
    SELECT COUNT(*) as total FROM contracts
    WHERE
        tenantname LIKE '%$query%' OR
        tenantapproval LIKE '%$query%' OR
        datestart LIKE '%$query%' OR
        expirationdate LIKE '%$query%'
";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $records_per_page);

// Fetch paginated, sorted records
$sql = "
    SELECT * FROM contracts
    WHERE 
        tenantname LIKE '%$query%' OR
        tenantapproval LIKE '%$query%' OR
        datestart LIKE '%$query%' OR
        expirationdate LIKE '%$query%'
    ORDER BY $sort_column $sort_order
    LIMIT $offset, $records_per_page
";

$result_tenant_table = $conn->query($sql);
// $contracts = []; // Array to hold contract data

// Generate table rows based on search results
if ($result_tenant_table->num_rows > 0) {
    while ($row = $result_tenant_table->fetch_assoc()) {
        $contracts[] = $row; // Collect data for the JavaScript array
        echo "<tr>";
        echo "<th scope='row'>" . $row['id'] . "</th>";
        echo "<td>" . htmlspecialchars($row['tenantname']) . "</td>";
        echo "<td class='text-center'>" . ($row["tenantapproval"] === "true" ? "APPROVED" : ($row["tenantapproval"] === "false" ? "REJECTED" : "PENDING")) . "</td>";
        echo "<td>" . htmlspecialchars($row['datestart']) . "</td>";
        echo "<td>" . htmlspecialchars($row['expirationdate']) . "</td>";

        // $pdfUrl = $admin1->displayContractPDF($row['fileurl']); // Assuming you have an instance of the class
        $pdfUrl = ($row['fileurl']);
        
        // Output the iframe with the generated PDF
        // echo "<td>";
        // echo "<div class='col-12 px-2'>";
        // echo "<iframe src='../" . $pdfUrl . "' width='100%' height='500px'></iframe>";
        // echo "</div>";
        // echo "</td>";

        echo 
        "
        <td>
            <div class='col-12 px-2'>
                <!-- Add an icon or image as the clickable element -->
                <img src='../asset/pdf-file.webp' test='". $row['fileurl'] ."' id='testcontract' data-contid='.." . $pdfUrl . "' alt='View Contract' class='view-contract-icon img-fluid' data-toggle='modal' data-target='#contractModal' style='cursor:pointer; width: 100px; height: 100px; object-fit: cover; margin-right: 5px;'>
            </div>
        </td>
        ";


        echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>"; // Actions (Delete, Download)
        echo "<div class='row justify-content-center m-0'>"; // Div container for buttons
        echo "<div class='col-xl-6 px-2'>"; // Delete button form
        echo "<form method='POST' action='admin_contract_template.php' class='float-xl-end align-items-center' style='height:100%;'>";
        echo "<input type='hidden' name='contractid' value='" . $row['id'] . "'>";
        echo "<button type='submit' name='delete_contract' class='btn btn-danger table-buttons-delete' style='width: 120px;'>Delete</button>";
        echo "</form>";
        echo "</div>";
        echo "<div class='col-xl-6 px-2'>"; // Download button
        if (!empty($row['fileurl'])) {
            echo "<a href='". '..' . htmlspecialchars($row['fileurl']) . "' download class='btn btn-success table-buttons-download justify-content-center table-buttons-update mx-auto mx-xl-0' style='width: 120px;'>Download</a>";
        } else {
            echo "<span>No file available</span>";
        }
        echo "</div>";        
        // echo "<div class='col-xl-6 px-2'>";
        //         // echo "<button type='submit' name='print_data'>Print Data</button>";
        //         echo "<button id='printBtn' name='print_data' data-print-id='" . $row['id'] . "'>Print Data</button>";
        // echo "</div>";
        echo "</div>";
        echo "</td>";
        echo "</tr>";
    }
    // Pass the contract data to JavaScript
    // echo "<script>var contractsData = " . json_encode($contracts) . ";</script>";
} else {
    echo "<tr><td colspan='7' class='text-center'>No contracts found</td></tr>";
}

// Output pagination buttons
// echo "<tr><td colspan='6' class='text-center'>";
// for ($i = 1; $i <= $total_pages; $i++) {
//     echo "<button class='btn btn-secondary pagination-btn' data-page='$i'>$i</button> ";
// }
// echo "</td></tr>";

$conn->close();
?>
