<?php
// Include your database connection file
include("../../db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['viewData_contract_id'])) {
    $viewData_contract_id = intval($_POST['viewData_contract_id']);

    // Query the tenants table for the user's name
    $query = "SELECT *
    FROM archives
    WHERE archives.users_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $viewData_contract_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if (!empty($row['archive_contracts_datestart']) && !empty($row['archive_contracts_expirationdate']))
            echo "
                <tr>
                    <td>{$row['id']}</td>
                    <td>{$row['archive_contracts_datestart']}</td>
                    <td>{$row['archive_contracts_expirationdate']}</td>
                    <td>
                        <div class='row justify-content-center align-items-center'>

                            <a href='" . ".." . $row['archive_contracts_fileurl'] . "' download class='btn btn-success justify-content-center table-buttons-download table-buttons-update' style='width: 120px;'>Download</a><br>
                        </div>
                    </td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No Contracts Found</td></tr>";
    }
    $stmt->close();
}
$conn->close();
?>