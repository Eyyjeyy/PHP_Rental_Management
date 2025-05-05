<?php
include '../../db_connect.php'; // Include your database connection

// Get search, pagination, and sorting parameters
$query = isset($_POST['query']) ? trim($_POST['query']) : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$sort_column = isset($_POST['sort_column']) ? $_POST['sort_column'] : 'houses.id'; // Default sorting by house ID
$sort_order = isset($_POST['sort_order']) ? $_POST['sort_order'] : 'ASC'; // Default sorting in ascending order
$records_per_page = 7; // Adjust as needed

// Calculate the offset for pagination
$offset = ($page - 1) * $records_per_page;

// Get the total number of matching records for pagination
// $total_sql = "
//     SELECT COUNT(*) as total FROM payments
//     WHERE
//         payments.name LIKE '%$query%' OR
//         payments.amount LIKE '%$query%' OR
//         payments.date_payment LIKE '%$query%'
// ";
$total_sql = "
    SELECT COUNT(*) as total 
    FROM (
        SELECT id FROM payments
        WHERE name LIKE '%$query%' OR amount LIKE '%$query%' OR date_payment LIKE '%$query%'
        UNION ALL
        SELECT id FROM deposit
        WHERE tenantid LIKE '%$query%' OR depositamount LIKE '%$query%' OR depositdate LIKE '%$query%'
    ) AS combined
";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $records_per_page);

// Fetch paginated, sorted records
// $sql = "
//     SELECT * FROM payments
//     WHERE 
//         payments.name LIKE '%$query%' OR
//         payments.amount LIKE '%$query%' OR
//         payments.date_payment LIKE '%$query%'
//     ORDER BY $sort_column $sort_order
//     LIMIT $offset, $records_per_page
// ";
$sql = "
    SELECT payments.id as id, name, amount, date_payment, approval, filepath, 'payment' AS payment_type, ' ' as reason FROM payments
    WHERE 
        name LIKE '%$query%' OR
        amount LIKE '%$query%' OR
        date_payment LIKE '%$query%'
    UNION ALL
    SELECT deposit.id as id, CONCAT(users.firstname, ' ', users.middlename, ' ', users.lastname) AS name, depositamount AS amount, depositdate AS date_payment, approval, deposit_filepath as filepath, 'deposit' AS payment_type, reason FROM deposit
    INNER JOIN tenants ON deposit.tenantid = tenants.id
    INNER JOIN users ON tenants.users_id = users.id
    WHERE 
        tenantid LIKE '%$query%' OR
        depositamount LIKE '%$query%' OR
        depositdate LIKE '%$query%'
    ORDER BY $sort_column $sort_order
    LIMIT $offset, $records_per_page
";
$result = $conn->query($sql);



// Generate table rows based on search results
// if ($result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         echo "<tr>";
//         echo "<th scope='row'>" . $row['id'] . "</th>";
//         echo "<td>" . htmlspecialchars($row['name']) . "</td>";
//         echo "<td>" . "as" . "</td>";
//         echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
//         // echo "<td><img src='" . $row["filepath"] . "' alt='Receipt' class='img-fluid' style='max-width: 100px; height: auto;'></td>";

//         echo "
//         <td>
//             <a href='#' data-bs-toggle='modal' data-bs-target='#imageModal" . $row["id"] . "'>
//                 <img src='" . $row["filepath"] . "' alt='Receipt' class='img-fluid' style='max-width: 100px; height: auto;'>
//             </a>
//         </td>
//         <div class='modal fade' id='imageModal" . $row["id"] . "' tabindex='-1' aria-labelledby='imageModalLabel" . $row["id"] . "' aria-hidden='true'>
//             <div class='modal-dialog modal-dialog-centered'>
//                 <div class='modal-content'>
//                     <div class='modal-header' style='background-color: #527853;'>
//                         <h5 class='modal-title text-white' id='imageModalLabel" . $row["id"] . "'>Receipt Preview</h5>
//                         <button type='button' class='btn-svg p-0' data-bs-dismiss='modal' aria-label='Close' style='width: 24px; height: 24px;'>
//                             <svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' class='bi bi-x-lg w-100' viewBox='0 0 16 16'>
//                                 <path d='M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z'/>
//                             </svg>
//                         </button>
//                     </div>
//                     <div class='modal-body'>
//                         <img src='" . $row["filepath"] . "' alt='Receipt' class='img-fluid'>
//                     </div>
//                 </div>
//             </div>
//         </div>";

//         echo "<td>" . htmlspecialchars($row['date_payment']) . "</td>";
//         echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
//         echo "<div class='row justify-content-center m-0'>";
//         echo "<div class='col-xxl-6 px-2'>";
//         // Add a form with a delete button for each record
//         echo "<form method='POST' action='adminpayments.php' class='float-xxl-end align-items-center'>";
//         echo "<input type='hidden' name='paymentsid' value='" . $row['id'] . "'>";
//         $approval = $row['approval'] === 'true' ? 'disabled' : '';
//         echo "<button type='submit' name='approve_payment' class='btn btn-primary d-flex table-buttons-update' style='width: 120px;' $approval>
//         <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='currentColor' class='bi bi-check align-self-center' viewBox='0 0 16 16'>
//             <path d='M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z'/>
//         </svg>
//         Approve
//         </button>";
//         echo "</form>";
//         echo "</div>";
//         echo "<div class='col-xxl-6 d-flex justify-content-center justify-content-xxl-start px-2'>";
//         // Add a form with a update button for each record
//         echo "<form method='POST' action='adminpayments.php' class='align-items-center'>";
//         echo "<input type='hidden' name='paymentsid' value='" . $row['id'] . "'>";
//         $decline = $row['approval'] === 'false' ? 'disabled' : '';
//         echo "<button type='submit' name='decline_payment' class='btn btn-danger update-category-btn float-xxl-start d-flex table-buttons-delete' data-id='" . $row['id'] . "' data-paymentname='" . htmlspecialchars($row['name']) . "' style='width: 120px;' $decline>
//         <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-ban-fill align-self-center me-2' viewBox='0 0 16 16'>
//             <path d='M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M2.71 12.584q.328.378.706.707l9.875-9.875a7 7 0 0 0-.707-.707l-9.875 9.875Z'/>
//         </svg>
//         Decline
//         </button>";
//         echo "</form>";
//         echo "</div>";
//         echo "</div>";
//         echo "</td>";
//         echo "</tr>";
//     }
// } else {
//     echo "<tr><td colspan='10'>No Payments found</td></tr>";
// }

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<th scope='row'>" . $row['id'] . "</th>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        // Add a column to show payment type (either 'payment' or 'deposit')
        echo "<td>" . ($row['payment_type'] == 'payment' ? 'Rent' : 'Deposit') . "</td>";
        echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
        // echo "<td><img src='" . $row["filepath"] . "' alt='Receipt' class='img-fluid' style='max-width: 100px; height: auto;'></td>";



        // echo "
        // <td>
        //     <a href='#' data-bs-toggle='modal' data-bs-target='#imageModal" . $row["id"] . "'>
        //         <img src='" . $row["filepath"] . "' alt='Receipt' class='img-fluid' style='max-width: 100px; height: auto; max-height: 100px;'>
        //     </a>
        // </td>
        // <div class='modal fade' id='imageModal" . $row["id"] . "' tabindex='-1' aria-labelledby='imageModalLabel" . $row["id"] . "' aria-hidden='true'>
        //     <div class='modal-dialog modal-dialog-centered'>
        //         <div class='modal-content'>
        //             <div class='modal-header' style='background-color: #527853;'>
        //                 <h5 class='modal-title text-white' id='imageModalLabel" . $row["id"] . "'>Receipt Preview</h5>
        //                 <button type='button' class='btn-svg p-0' data-bs-dismiss='modal' aria-label='Close' style='width: 24px; height: 24px;'>
        //                     <svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' class='bi bi-x-lg w-100' viewBox='0 0 16 16'>
        //                         <path d='M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z'/>
        //                     </svg>
        //                 </button>
        //             </div>
        //             <div class='modal-body'>
        //                 <img src='" . $row["filepath"] . "' alt='Receipt' class='img-fluid'>
        //             </div>
        //         </div>
        //     </div>
        // </div>";

        

        echo "<td>";

        if ($row["payment_type"] == "deposit") {
            // If the payment type is "deposit", create a modal specifically for deposits
            echo "<a href='#' data-bs-toggle='modal' data-bs-target='#depositImageModal" . $row["id"] . "'>
                    <img src='" . $row["filepath"] . "' alt='Deposit Receipt' class='img-fluid' style='max-width: 100px; height: auto; max-height: 100px;'>
                </a>";
            echo "<div class='modal fade' id='depositImageModal" . $row["id"] . "' tabindex='-1' aria-labelledby='depositImageModalLabel" . $row["id"] . "' aria-hidden='true'>
                    <div class='modal-dialog modal-dialog-centered'>
                        <div class='modal-content'>
                            <div class='modal-header' style='background-color: #527853;'>
                                <h5 class='modal-title text-white' id='depositImageModalLabel" . $row["id"] . "'>Deposit Receipt Preview</h5>
                                <button type='button' class='btn-svg p-0' data-bs-dismiss='modal' aria-label='Close' style='width: 24px; height: 24px;'>
                                    <svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' class='bi bi-x-lg w-100' viewBox='0 0 16 16'>
                                        <path d='M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z'/>
                                    </svg>
                                </button>
                            </div>
                            <div class='modal-body'>
                                <img src='" . $row["filepath"] . "' alt='Deposit Receipt' class='img-fluid'>
                            </div>
                        </div>
                    </div>
                </div>";
        } else {
            // If the payment type is not "deposit", keep the original modal for rent
            echo "<a href='#' data-bs-toggle='modal' data-bs-target='#imageModal" . $row["id"] . "'>
                    <img src='" . $row["filepath"] . "' alt='Receipt' class='img-fluid' style='max-width: 100px; height: auto; max-height: 100px;'>
                </a>";
            echo "<div class='modal fade' id='imageModal" . $row["id"] . "' tabindex='-1' aria-labelledby='imageModalLabel" . $row["id"] . "' aria-hidden='true'>
                    <div class='modal-dialog modal-dialog-centered'>
                        <div class='modal-content'>
                            <div class='modal-header' style='background-color: #527853;'>
                                <h5 class='modal-title text-white' id='imageModalLabel" . $row["id"] . "'>Receipt Preview</h5>
                                <button type='button' class='btn-svg p-0' data-bs-dismiss='modal' aria-label='Close' style='width: 24px; height: 24px;'>
                                    <svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' class='bi bi-x-lg w-100' viewBox='0 0 16 16'>
                                        <path d='M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z'/>
                                    </svg>
                                </button>
                            </div>
                            <div class='modal-body'>
                                <img src='" . $row["filepath"] . "' alt='Receipt' class='img-fluid'>
                            </div>
                        </div>
                    </div>
                </div>";
        }

        echo "</td>";

        echo "<td>" . htmlspecialchars($row['date_payment']) . "</td>";
        echo "<td>" . htmlspecialchars($row['approval'] === 'true' ? 'Approved' : ($row['approval'] === '' ? 'Pending' : ($row['approval'] === 'false' ? 'Unapproved' : $row['approval']))) . "</td>";
        echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
        echo "<td class='justify-content-center text-center align-middle' style='height: 100%;'>";
        echo "<div class='row justify-content-center m-0'>";
        // echo "<div class='col-xxl-4 px-2'>";
        echo "<div class='col-xxl-4 px-2 " . ($row['payment_type'] === 'deposit' && ($row['approval'] === 'true' || $row['approval'] === 'Unapproved' || $row['approval'] === '1 Month Consumed' || $row['approval'] === '2 Months Consumed') ? 'd-none' : '') ."'>";
        // Add a form with a delete button for each record
        echo "<form method='POST' action='adminpayments.php' class='float-xxl-end align-items-center mb-0'>";
        echo "<input type='hidden' name='paymentsid' value='" . $row['id'] . "'>";
        $approval = $row['approval'] === 'true' ? 'disabled' : '';
        // echo "<button type='submit' name='approve_payment' class='btn btn-primary d-flex table-buttons-update' style='width: 120px;' $approval>
        //     <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='currentColor' class='bi bi-check align-self-center' viewBox='0 0 16 16'>
        //         <path d='M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z'/>
        //     </svg>
        //     Approve
        // </button>";
        echo "<button type='submit' name='" . ($row['payment_type'] === 'deposit' ? 'approve_deposit' : 'approve_payment') . "' class='btn btn-primary d-flex table-buttons-update mt-1" . ($row['payment_type'] === 'deposit' && ($row['approval'] === 'true' || $row['approval'] === 'Unapproved' || $row['approval'] === '1 Month Consumed' || $row['approval'] === '2 Months Consumed') ? ' invisible' : ' visible') . "' style='width: 120px;' $approval>
            <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='currentColor' class='bi bi-check align-self-center' viewBox='0 0 16 16'>
                <path d='M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z'/>
            </svg>
            Approve
        </button>";
        echo "</form>";
        echo "</div>";            
        // echo "<div class='col-xxl-auto d-flex justify-content-center px-2'>";
        //     echo "<button class='btn btn-primary float-end table-buttons-update' id='new_category'><i class='fa fa-plus'></i>Update</button>";
        // echo "</div>";

        // echo "<div class='col-xxl-4 d-flex justify-content-center justify-content-xxl-start px-2'>";
        // echo "<div class='col-xxl-4 d-flex justify-content-center px-2'>";
        // echo "<div class='col-xxl-auto d-flex justify-content-center px-2'>";
        // echo "<div class='col-xxl-auto d-flex justify-content-center px-2" . ($row['payment_type'] === 'deposit' && ($row['approval'] === 'true' || $row['approval'] === 'Unapproved' || $row['approval'] === '1 Month Consumed' || $row['approval'] === '2 Months Consumed') ? ' invisible' : ' visible') . "'>";
        echo "<div class='col-xxl-auto d-flex justify-content-center px-2" . ($row['payment_type'] === 'deposit' && ($row['approval'] === 'true' || $row['approval'] === 'Unapproved' || $row['approval'] === '1 Month Consumed' || $row['approval'] === '2 Months Consumed') ? ' d-none' : ' visible') . "'>";
            // Add a form with a update button for each record
            echo "<form method='POST' action='adminpayments.php' class='align-items-center mb-0'>";
                echo "<input type='hidden' name='paymentsid' value='" . $row['id'] . "'>";
                $decline = $row['approval'] === 'false' ? 'disabled' : '';
                echo "<button type='submit' name='" . ($row['payment_type'] === 'deposit' ? 'decline_deposit' : 'decline_payment') . "' class='btn btn-danger update-category-btn float-xxl-start d-flex table-buttons-delete mt-1 mb-1' data-id='" . $row['id'] . "' data-paymentname='" . htmlspecialchars($row['name']) . "' style='width: 120px;' $decline>
                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-ban-fill align-self-center me-2' viewBox='0 0 16 16'>
                            <path d='M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M2.71 12.584q.328.378.706.707l9.875-9.875a7 7 0 0 0-.707-.707l-9.875 9.875Z'/>
                        </svg>
                        Decline
                </button>";
            echo "</form>";
        echo "</div>";
        // echo "<div class='col-xxl-auto d-flex" . ($row['payment_type'] === 'deposit' && $row['approval'] === 'true' ? ' visible' : ' invisible') . " justify-content-center px-2'>";
        // echo "<div class='col-xxl-auto d-flex" . ($row['payment_type'] === 'deposit' && $row['approval'] !== '' ? ' visible' : ' invisible') . " justify-content-center px-2'>";
        echo "<div class='col-xxl-auto d-flex" . ($row['payment_type'] === 'deposit' && $row['approval'] !== '' && $row['approval'] !== 'Unapproved' ? ' visible' : ' d-none') . " justify-content-center px-2'>";
        echo "<button class='btn btn-primary float-end table-buttons-update' id='update_deposit' data-id='" . $row['id'] . "' style='width: 120px;'><i class='fa fa-plus'></i>Update</button>";
        echo "</div>";
        
        echo "</div>";
        echo "</td>";
        echo "</tr>";
    }
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
