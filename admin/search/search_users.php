<?php
include '../../db_connect.php';

// Get search, pagination, and sorting parameters
$query = isset($_POST['query']) ? $_POST['query'] : '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$sort_column = isset($_POST['sort_column']) ? $_POST['sort_column'] : 'id';
$sort_order = isset($_POST['sort_order']) ? $_POST['sort_order'] : 'ASC';
$records_per_page = 10; // Adjust as needed

// Calculate the offset for pagination
$offset = ($page - 1) * $records_per_page;

// Get the total number of matching records for pagination
$total_sql = "
    SELECT COUNT(*) as total FROM users 
    WHERE 
        username LIKE '%$query%' OR 
        firstname LIKE '%$query%' OR 
        middlename LIKE '%$query%' OR 
        lastname LIKE '%$query%' OR 
        phonenumber LIKE '%$query%' OR 
        email LIKE '%$query%' OR 
        role LIKE '%$query%'
";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $records_per_page);

// Fetch paginated, sorted records
$sql = "
    SELECT * FROM users
    WHERE 
        username LIKE '%$query%' OR 
        firstname LIKE '%$query%' OR 
        middlename LIKE '%$query%' OR 
        lastname LIKE '%$query%' OR 
        phonenumber LIKE '%$query%' OR 
        email LIKE '%$query%' OR 
        role LIKE '%$query%'
    ORDER BY $sort_column $sort_order
    LIMIT $offset, $records_per_page
";
$result = $conn->query($sql);

// Generate table rows based on search results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $password = $row['password'] ?? '';
        $maskedPassword = $password ? str_repeat('*', strlen($password)) : 'N/A';
        echo "<tr>";
        echo "<th scope='row'>" . $row['id'] . "</th>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['middlename']) . "</td>";
        echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['phonenumber'] ? $row['phonenumber'] : 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($row['email'] ? $row['email'] : 'N/A') . "</td>";
        echo "
        <td> 
            <div style='position: relative; display: inline-block;'>
                <input type='password' value='" . htmlspecialchars($password) . "' style='margin-right: 1em; border: none; background: transparent; width: " . strlen($maskedPassword) * 10 . "px;' readonly id='password-{$row['id']}'>
                <button type='button' class='p-0' style='position: absolute; right: 0; top: 4.1; border: none; background: none; cursor: pointer;' 
                        onmousedown=\"togglePassword('password-{$row['id']}', true)\"
                        onmouseup=\"togglePassword('password-{$row['id']}', false)\"
                        onmouseleave=\"togglePassword('password-{$row['id']}', false)\">
                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='black' class='bi bi-eye-fill' viewBox='0 0 16 16'>
                        <path d='M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0'/>
                        <path d='M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7'/>
                    </svg>
                </button>
            </div>
        </td>
        ";
        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
        echo "<td class='justify-content-center text-center align-middle'>";
        echo "<div class='row justify-content-center m-0'>";
        echo "<div class='col-xl-6 px-2'>";
        echo "<form method='POST' action='adminusers.php' class='float-xl-end align-items-center'>";
        echo "<input type='hidden' name='user_id' value='" . $row['id'] . "'>";
        echo "<button type='submit' name='delete_user' class='btn btn-danger table-buttons-delete' style='width: 80px;'>Delete</button>";
        echo "</form>";
        echo "</div>";
        echo "<div class='col-xl-6 px-2'>";
        echo "<input type='hidden' name='user_id' value='" . $row['id'] . "'>";
        echo "<button type='button' class='btn btn-primary update-user-btn float-xl-start table-buttons-update' data-id='" . $row['id'] . "' data-username='" . htmlspecialchars($row['username']) . "' data-firstname= '" . htmlspecialchars($row['firstname']) . "' data-middlename= '" . htmlspecialchars($row['middlename']) . "' data-lastname= '" . htmlspecialchars($row['lastname']) . "' data-password='" . htmlspecialchars($row['password']) . "' data-role='" . htmlspecialchars($row['role']) . "' data-email='" . htmlspecialchars($row['email']) . "' data-number='" . htmlspecialchars($row['phonenumber']) . "' style='width: 80px;'>Update</button>";
        echo "</div>";
        echo "</div>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='10'>No users found</td></tr>";
}

// Output pagination buttons
echo "<tr><td colspan='10' class='text-center'>";
for ($i = 1; $i <= $total_pages; $i++) {
    echo "<button class='btn btn-secondary pagination-btn' data-page='$i'>$i</button> ";
}
echo "</td></tr>";

$conn->close();
?>
