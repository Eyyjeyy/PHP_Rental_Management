<?php
include '../../db_connect.php';

$query = isset($_POST['query']) ? trim($_POST['query']) : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$sort_column = isset($_POST['sort_column']) ? $_POST['sort_column'] : 'contracts.id';
$sort_order = isset($_POST['sort_order']) ? $_POST['sort_order'] : 'ASC';
$records_per_page = 5;

$offset = ($page - 1) * $records_per_page;

// $sql = "SELECT * FROM physical_contracts";

// // Add the WHERE clause only if there's a search query
// $total_sql = "
//     SELECT COUNT(*) as total 
//     FROM physical_contracts
//     JOIN tenants ON physical_contracts.tenantid = tenants.id
//     WHERE 
//         CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) LIKE '%$query%' OR
//         physical_contracts.datestart LIKE '%$query%' OR
//         physical_contracts.expirationdate LIKE '%$query%'
// ";
// $total_result = $conn->query($total_sql);
// $total_rows = $total_result->fetch_assoc()['total'];
// $total_pages = ceil($total_rows / $records_per_page);

// $sql = "
//     SELECT 
//         physical_contracts.*, 
//         CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) AS full_name 
//     FROM physical_contracts
//     JOIN tenants ON physical_contracts.tenantid = tenants.id
//     WHERE 
//         CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) LIKE '%$query%' OR
//         physical_contracts.datestart LIKE '%$query%' OR
//         physical_contracts.expirationdate LIKE '%$query%'
//     ORDER BY $sort_column $sort_order
//     LIMIT $offset, $records_per_page
// ";

// $result = $conn->query($sql);

// if ($result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         echo "<tr>";
//         echo "<th scope='row'>" . $row['id'] . "</th>";
//         echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
//         echo "<td>" . htmlspecialchars($row['datestart']) . "</td>";
//         echo "<td>" . htmlspecialchars($row['expirationdate']) . "</td>";
//         echo "<td>";
//             if (!empty($row['fileurl'])) {
//                 $fileUrl = '../asset/physical_contracts/' . htmlspecialchars($row['fileurl']);
//                 echo "<a href='#' data-bs-toggle='modal' data-bs-target='#imagePreviewModal' onclick=\"showImageModal('$fileUrl')\">";
//                 echo "<img src='$fileUrl' alt='Tenant Picture' class='img-fluid' style='width: 150px; height: 150px; object-fit: cover;'>";
//                 echo "</a>";
//             } else {
//                 echo "<img src='../asset/physical_contracts/default.png' alt='Default Picture' class='img-fluid' style='width: 150px; height: 150px; object-fit: cover;'>";
//             }
//         echo "</td>";
//         echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
//         echo "<div class='row justify-content-center m-0'>";
//             echo "<div class='col-xl-6 px-2'>";
//                 // Add a form with a delete button for each record
//                 echo "<form method='POST' action='admin_contract_template.php' class='float-xl-end align-items-center' style='height:100%;'>";
//                     echo "<input type='hidden' name='physicalcontractid' value='" . $row['id'] . "'>";
//                     echo "<button type='submit' name='delete_physicalcontract' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>";
//                 echo "</form>";
//             echo "</div>";
//             echo "<div class='col-xl-6 px-2'>";
//                 if (!empty($row['fileurl'])) { // Ensure fileurl is not empty
//                     echo "<a href='". '../asset/physical_contracts/' . htmlspecialchars($row['fileurl']) . "' download class='btn btn-success table-buttons-download justify-content-center' style='width: 120px;'>Download</a>";
//                 } else {
//                     echo "<span>No file available</span>";
//                 }
//             echo "</div>";
//         echo "</div>";
//         echo "</td>";
//         echo "</tr>";
//     }
// } else {
//     echo "<tr><td colspan='6' class='text-center'>No physical contracts found</td></tr>";
// }

// echo "<tr><td colspan='6' class='text-center'>";
// for ($i = 1; $i <= $total_pages; $i++) {
//     echo "<button class='btn btn-secondary pagination-btn' data-page='$i'>$i</button> ";
// }
// echo "</td></tr>";

// $conn->close();
// Count total rows for pagination
$total_sql = "
    SELECT COUNT(DISTINCT physical_contracts.id) AS total
    FROM physical_contracts
    JOIN tenants ON physical_contracts.tenantid = tenants.id
    LEFT JOIN contract_images ON physical_contracts.id = contract_images.physical_contract_id
    WHERE 
        CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) LIKE '%$query%' OR
        physical_contracts.datestart LIKE '%$query%' OR
        physical_contracts.expirationdate LIKE '%$query%'
";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $records_per_page);

// Main query to fetch filtered and paginated data with GROUP_CONCAT
$sql = "
    SELECT 
        physical_contracts.*, 
        CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) AS full_name, 
        GROUP_CONCAT(contract_images.image_path SEPARATOR ',') AS image_paths
    FROM physical_contracts
    JOIN tenants ON physical_contracts.tenantid = tenants.id
    LEFT JOIN contract_images ON physical_contracts.id = contract_images.physical_contract_id
    WHERE 
        CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) LIKE '%$query%' OR
        physical_contracts.datestart LIKE '%$query%' OR
        physical_contracts.expirationdate LIKE '%$query%'
    GROUP BY physical_contracts.id
    ORDER BY $sort_column $sort_order
    LIMIT $offset, $records_per_page
";

$result = $conn->query($sql);

// Display data in rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<th scope='row'>" . $row['id'] . "</th>";
        echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['datestart']) . "</td>";
        echo "<td>" . htmlspecialchars($row['expirationdate']) . "</td>";
        echo "<td>";

        // Display multiple images if available
        $imagePaths = explode(',', $row['image_paths']);
        foreach ($imagePaths as $imagePath) {
            $fileUrl = '../asset/physical_contracts/' . htmlspecialchars($imagePath);
            if (!empty($imagePath)) {
                echo "<a href='#' data-bs-toggle='modal' data-bs-target='#imagePreviewModal' onclick=\"showImageModal('$fileUrl')\">";
                echo "<img src='$fileUrl' alt='Contract Image' class='img-fluid' style='width: 100px; height: 100px; object-fit: cover; margin-right: 5px;'>";
                echo "</a>";
            }
        }

        echo "</td>";
        echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
        echo "<div class='row justify-content-center m-0'>";
        echo "<div class='col-xl-6 px-2'>";
        echo "<form method='POST' action='admin_contract_template.php' class='float-xl-end align-items-center' style='height:100%;'>";
        echo "<input type='hidden' name='physicalcontractid' value='" . $row['id'] . "'>";
        echo "<button type='submit' name='delete_physicalcontract' class='btn btn-danger table-buttons-delete' style='width: 120px;'>Delete</button>";
        echo "</form>";
        echo "</div>";
        echo "<div class='col-xl-6 px-2'>";
        // if (!empty($row['fileurl'])) { // Ensure fileurl is not empty
        //     echo "<a href='". '../asset/physical_contracts/' . htmlspecialchars($row['fileurl']) . "' download class='btn btn-success table-buttons-download justify-content-center' style='width: 120px;'>Download</a>";
        // } else {
        //     echo "<span>No file available</span>";
        // }
        // Add download links for each image
        if (!empty($row['image_paths'])) {
            foreach ($imagePaths as $imagePath) {
                $downloadUrl = '../asset/physical_contracts/' . htmlspecialchars($imagePath);
                if (!empty($imagePath)) {
                    echo "<a href='$downloadUrl' download class='btn btn-success table-buttons-download justify-content-center table-buttons-update' style='width: 120px; margin-bottom: 5px;'>Download</a><br>";
                }
            }
        } else {
            echo "<span>No file available</span>";
        }
        echo "</div>";
        echo "</div>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>No physical contracts found</td></tr>";
}

// Pagination buttons
echo "<tr><td colspan='6' class='text-center'>";
for ($i = 1; $i <= $total_pages; $i++) {
    $active = ($i === $page) ? 'active' : '';
    echo "<button class='btn btn-secondary pagination-btn $active' data-page='$i'>$i</button> ";
}
echo "</td></tr>";

$conn->close();
?>
