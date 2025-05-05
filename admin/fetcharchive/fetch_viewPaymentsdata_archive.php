<?php
// Include your database connection file
include("../../db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['viewData_payment_id'])) {
    $viewData_payment_id = intval($_POST['viewData_payment_id']);

    // Query the tenants table for the user's name
    $query = "SELECT archives.*, payments.*, payments.id as paymentid, 'Rent' AS payment_type, '' as reason
    FROM payments
    LEFT JOIN archives ON archives.tenants_id = payments.tenants_id
    WHERE archives.users_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $viewData_payment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // echo "<pre>";
            // var_dump($row);
            // echo "</pre>";
            echo "
                <tr>
                    <td>" . $row['paymentid'] . "</td>
                    <td>" . $row['payment_type'] . "</td>
                    <td>{$row['amount']}</td>
                    <td>{$row['date_payment']}</td>
                    <td>" . ($row['approval'] == 'true' ? 'Approved' : ($row['approval'] == 'false' ? 'DECLINED' : 'PENDING' ) ) . "</td>
                    <td>" . $row['reason'] . "</td>
                    <td>
                        <img src='" . $row['filepath'] . "' alt='Deposit Receipt' class='img-fluid d-block mx-auto' style='max-width: 200px; height: auto; max-height: 200px;'>
                    </td>
                </tr>";
        }
    } else {
        // echo "<tr><td colspan='3'>No Payments Found</td></tr>";
    }
    $stmt->close();


    $deposit_query = "SELECT archives.*, deposit.*, 'Deposit' AS payment_type
    FROM deposit
    LEFT JOIN archives ON archives.tenants_id = deposit.tenantid
    WHERE archives.users_id = ?";
    $deposit_stmt = $conn->prepare($deposit_query);
    $deposit_stmt->bind_param("i", $viewData_payment_id);
    $deposit_stmt->execute();
    $deposit_result = $deposit_stmt->get_result();

    if ($deposit_result->num_rows > 0) {
        while ($deposit_row = $deposit_result->fetch_assoc()) {
            echo "
                <tr>
                    <td>" . $deposit_row['id'] . "</td>
                    <td>" . $deposit_row['payment_type'] . "</td>
                    <td>{$deposit_row['depositamount']}</td>
                    <td>{$deposit_row['depositdate']}</td>
                    <td>" . ($deposit_row['approval'] == 'true' ? 'Approved' : ($deposit_row['approval'] == 'Unapproved' ? 'DECLINED' : 'PENDING' ) ) . "</td>
                    <td>" . $deposit_row['reason'] . "</td>
                    <td>
                        <img src='" . $deposit_row['deposit_filepath'] . "' alt='Deposit Receipt' class='img-fluid d-block mx-auto' style='max-width: 200px; height: auto; max-height: 200px;'>
                    </td>
                </tr>";
        }
    } else {
        // If first query also has no records
        if (!$result->num_rows > 0) {
            echo "<tr><td colspan='6'>No Deposits Found</td></tr>";
        }
    }
    $deposit_stmt->close();
}
$conn->close();
?>