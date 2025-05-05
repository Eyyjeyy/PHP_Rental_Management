<?php
include '../../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;

    $monthly = $_POST['month'] ?? null;
    $quarterly = $_POST['quarterly'] ?? null;
    $yearly = $_POST['yearly'] ?? null;

    $search = isset($_POST['search']) ? trim($_POST['search']) : '';

    if($monthly) {
        // $query = "SELECT 
        //     tenants.house_id,
        //     houses.house_name,
        //     GROUP_CONCAT(DISTINCT tenants.id) AS tenant_ids,
        //     GROUP_CONCAT(DISTINCT CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) SEPARATOR ', ') AS tenant_names,
        //     COUNT(DISTINCT tenants.id) AS total_tenants,
        //     COUNT(DISTINCT payments.id) AS total_number_payments,
        //     SUM(payments.amount) AS total_amount_payment,
        //     (
        //         SELECT COUNT(*) 
        //         FROM expenses 
        //         WHERE expenses.house_id = tenants.house_id AND DATE_FORMAT(expenses.date, '%Y-%m') = DATE_FORMAT(payments.date_payment, '%Y-%m')
        //     ) AS total_number_expenses,
        //     (
        //         SELECT SUM(expenses.amount) 
        //         FROM expenses 
        //         WHERE expenses.house_id = tenants.house_id AND DATE_FORMAT(expenses.date, '%Y-%m') = DATE_FORMAT(payments.date_payment, '%Y-%m')
        //     ) AS total_amount_expenses,
        //     DATE_FORMAT(payments.date_payment, '%Y-%m') AS payment_date
        // FROM tenants
        // LEFT JOIN houses ON tenants.house_id = houses.id
        // LEFT JOIN payments ON tenants.house_id = payments.houses_id AND payments.approval = 'true'
        // GROUP BY tenants.house_id, payment_date
        // ORDER BY tenants.house_id, payment_date";

        $query = "SELECT 
            tenants.house_id,
            houses.house_name,
            GROUP_CONCAT(DISTINCT tenants.id) AS tenant_ids,
            GROUP_CONCAT(DISTINCT CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) SEPARATOR ', ') AS tenant_names,
            COUNT(DISTINCT tenants.id) AS total_tenants,
            COUNT(DISTINCT payments.id) AS total_number_payments,
            SUM(payments.amount) AS total_amount_payment,
            (
                SELECT COUNT(*) 
                FROM expenses 
                WHERE expenses.house_id = tenants.house_id 
                  AND DATE_FORMAT(expenses.date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
            ) AS total_number_expenses,
            (
                SELECT SUM(expenses.amount) 
                FROM expenses 
                WHERE expenses.house_id = tenants.house_id 
                  AND DATE_FORMAT(expenses.date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
            ) AS total_amount_expenses,
            DATE_FORMAT(payments.date_payment, '%Y-%m') AS payment_date
        FROM tenants
        LEFT JOIN houses ON tenants.house_id = houses.id
        LEFT JOIN payments ON tenants.house_id = payments.houses_id 
            AND payments.approval = 'true' 
            AND DATE_FORMAT(payments.date_payment, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m') -- Filter by the current month
        WHERE DATE_FORMAT(CURDATE(), '%Y-%m') = DATE_FORMAT(payments.date_payment, '%Y-%m') -- Ensure results are for the current month
        ";

        if (!empty($search)) {
            $query .= " AND (
                houses.house_name LIKE ? OR
                CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) LIKE ?
            )";
        }

        $query .= " GROUP BY tenants.house_id
        ORDER BY tenants.house_id";
        
        $stmt = $conn->prepare($query);
        // Bind parameters
        if (!empty($search)) {
            $searchParam = "%$search%";
            $stmt->bind_param("ss", $searchParam, $searchParam);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $reportsTotalPaymentsAmount = 0;
        $reportsTotalExpensesAmount = 0;
        $reportsTotalNetAmount = 0;

        while($row = $result->fetch_assoc()) {
            $IncomePerTenantTable_totalPayment = $row['total_amount_payment'];
            $IncomePerTenantTable_totalExpense = $row['total_amount_expenses'];
            $IncomePerTenantTable_netAmount = $IncomePerTenantTable_totalPayment - $IncomePerTenantTable_totalExpense;
            echo "<tr>";
            echo "<th scope='row' style='max-width: 100px;'>" . $row['tenant_names'] . "</th>";
            echo "<td style='width: 150px;'>" . htmlspecialchars($row['house_name']) . "</td>";
            echo "<td style='width: 100px;'>" . htmlspecialchars($row['total_number_payments']) . "</td>";
            echo "<td>" . htmlspecialchars($row['total_amount_payment']) . "</td>";
            echo "<td>" . htmlspecialchars($row['total_number_expenses']) . "</td>";
            echo "<td>" . $IncomePerTenantTable_totalExpense . "</td>";
            echo "<td>" . htmlspecialchars($IncomePerTenantTable_netAmount) . "</td>";
            // echo "<td>" . htmlspecialchars($row['payment_date']) . "</td>";
            echo "<td>" . (empty($row['payment_date']) ? $row['expense_date'] : htmlspecialchars($row['payment_date']) ) . "</td>";
            echo "</td>";
            echo "</tr>";
            $reportsTotalPaymentsAmount += $IncomePerTenantTable_totalPayment;
            $reportsTotalExpensesAmount += $IncomePerTenantTable_totalExpense;
            $reportsTotalNetAmount += $IncomePerTenantTable_netAmount;
        }
        echo "
        <tr style='border: transparent;'>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'>Total Payments: {$reportsTotalPaymentsAmount}</td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'>Total Expenses: {$reportsTotalExpensesAmount}</td>
            <td style='box-shadow: none;'>Total Income: {$reportsTotalNetAmount}</td>
            <td style='box-shadow: none;'></td>
        </tr>
        ";

    } else if ($quarterly) {
        
        // $query = "SELECT 
        //     tenants.house_id,
        //     houses.house_name,
        //     GROUP_CONCAT(DISTINCT tenants.id) AS tenant_ids,
        //     GROUP_CONCAT(DISTINCT CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) SEPARATOR ', ') AS tenant_names,
        //     COUNT(DISTINCT tenants.id) AS total_tenants,
        //     COUNT(DISTINCT payments.id) AS total_number_payments,
        //     SUM(payments.amount) AS total_amount_payment,
        //     (
        //         SELECT COUNT(*) 
        //         FROM expenses 
        //         WHERE expenses.house_id = tenants.house_id 
        //         AND YEAR(expenses.date) = YEAR(payments.date_payment) 
        //         AND QUARTER(expenses.date) = QUARTER(payments.date_payment)
        //     ) AS total_number_expenses,
        //     (
        //         SELECT SUM(expenses.amount) 
        //         FROM expenses 
        //         WHERE expenses.house_id = tenants.house_id 
        //         AND YEAR(expenses.date) = YEAR(payments.date_payment) 
        //         AND QUARTER(expenses.date) = QUARTER(payments.date_payment)
        //     ) AS total_amount_expenses,
        //     CONCAT(YEAR(payments.date_payment), '-Q', QUARTER(payments.date_payment)) AS payment_date
        // FROM tenants
        // LEFT JOIN houses ON tenants.house_id = houses.id
        // LEFT JOIN payments ON tenants.house_id = payments.houses_id AND payments.approval = 'true'
        // GROUP BY tenants.house_id, YEAR(date_payment), QUARTER(date_payment) 
        // ORDER BY tenants.house_id, YEAR(date_payment), QUARTER(date_payment)";

        $query = "SELECT 
            tenants.house_id,
            houses.house_name,
            GROUP_CONCAT(DISTINCT tenants.id) AS tenant_ids,
            GROUP_CONCAT(DISTINCT CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) SEPARATOR ', ') AS tenant_names,
            COUNT(DISTINCT tenants.id) AS total_tenants,
            COUNT(DISTINCT payments.id) AS total_number_payments,
            SUM(payments.amount) AS total_amount_payment,
            (
                SELECT COUNT(*) 
                FROM expenses 
                WHERE expenses.house_id = tenants.house_id 
                  AND DATE_FORMAT(expenses.date, '%Y-%m') = DATE_FORMAT(payments.date_payment, '%Y-%m')
            ) AS total_number_expenses,
            (
                SELECT SUM(expenses.amount) 
                FROM expenses 
                WHERE expenses.house_id = tenants.house_id 
                  AND DATE_FORMAT(expenses.date, '%Y-%m') = DATE_FORMAT(payments.date_payment, '%Y-%m')
            ) AS total_amount_expenses,
            DATE_FORMAT(payments.date_payment, '%Y-%m') AS payment_date
        FROM tenants
        LEFT JOIN houses ON tenants.house_id = houses.id
        LEFT JOIN payments ON tenants.house_id = payments.houses_id 
            AND payments.approval = 'true' 
        WHERE payments.date_payment BETWEEN DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 2 MONTH), '%Y-%m-01') 
          AND LAST_DAY(CURDATE())";

        if (!empty($search)) {
            $query .= " AND (
                CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) LIKE ? OR 
                houses.house_name LIKE ?
            )";
        }

        $query .= " GROUP BY tenants.house_id, payment_date ORDER BY tenants.house_id, payment_date";
        $stmt = $conn->prepare($query);
        if (!empty($search)) {
            $searchParam = '%' . $search . '%';
            $stmt->bind_param('ss', $searchParam, $searchParam);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $reportsTotalPaymentsAmount = 0;
        $reportsTotalExpensesAmount = 0;
        $reportsTotalNetAmount = 0;

        while($row = $result->fetch_assoc()) {
            $IncomePerTenantTable_totalPayment = $row['total_amount_payment'];
            $IncomePerTenantTable_totalExpense = $row['total_amount_expenses'];
            $IncomePerTenantTable_netAmount = $IncomePerTenantTable_totalPayment - $IncomePerTenantTable_totalExpense;
            echo "<tr>";
            echo "<th scope='row' style='max-width: 100px;'>" . $row['tenant_names'] . "</th>";
            echo "<td style='width: 150px;'>" . htmlspecialchars($row['house_name']) . "</td>";
            echo "<td style='width: 100px;'>" . htmlspecialchars($row['total_number_payments']) . "</td>";
            echo "<td>" . htmlspecialchars($row['total_amount_payment']) . "</td>";
            echo "<td>" . htmlspecialchars($row['total_number_expenses']) . "</td>";
            echo "<td>" . $IncomePerTenantTable_totalExpense . "</td>";
            echo "<td>" . htmlspecialchars($IncomePerTenantTable_netAmount) . "</td>";
            echo "<td>" . (empty($row['payment_date']) ? $row['expense_date'] : htmlspecialchars($row['payment_date']) ) . "</td>";
            echo "</td>";
            echo "</tr>";
            $reportsTotalPaymentsAmount += $IncomePerTenantTable_totalPayment;
            $reportsTotalExpensesAmount += $IncomePerTenantTable_totalExpense;
            $reportsTotalNetAmount += $IncomePerTenantTable_netAmount;
        }
        echo "
        <tr style='border: transparent;'>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'>Total Payments: {$reportsTotalPaymentsAmount}</td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'>Total Expenses: {$reportsTotalExpensesAmount}</td>
            <td style='box-shadow: none;'>Total Income: {$reportsTotalNetAmount}</td>
            <td style='box-shadow: none;'></td>
        </tr>
        ";

    } else if ($yearly) {
        // $query = "SELECT 
        //     tenants.house_id,
        //     houses.house_name,
        //     GROUP_CONCAT(DISTINCT tenants.id) AS tenant_ids,
        //     GROUP_CONCAT(DISTINCT CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) SEPARATOR ', ') AS tenant_names,
        //     COUNT(DISTINCT tenants.id) AS total_tenants,
        //     COUNT(DISTINCT payments.id) AS total_number_payments,
        //     SUM(payments.amount) AS total_amount_payment,
        //     (
        //         SELECT COUNT(*) 
        //         FROM expenses 
        //         WHERE expenses.house_id = tenants.house_id 
        //         AND YEAR(expenses.date) = YEAR(payments.date_payment)
        //     ) AS total_number_expenses,
        //     (
        //         SELECT SUM(expenses.amount) 
        //         FROM expenses 
        //         WHERE expenses.house_id = tenants.house_id 
        //         AND YEAR(expenses.date) = YEAR(payments.date_payment)
        //     ) AS total_amount_expenses,
        //     YEAR(payments.date_payment) AS payment_date
        // FROM tenants
        // LEFT JOIN houses ON tenants.house_id = houses.id
        // LEFT JOIN payments ON tenants.house_id = payments.houses_id AND payments.approval = 'true'
        // GROUP BY tenants.house_id, YEAR(payments.date_payment)
        // ORDER BY tenants.house_id, YEAR(payments.date_payment)";

        $query = "SELECT 
            tenants.house_id,
            houses.house_name,
            GROUP_CONCAT(DISTINCT tenants.id) AS tenant_ids,
            GROUP_CONCAT(DISTINCT CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) SEPARATOR ', ') AS tenant_names,
            COUNT(DISTINCT tenants.id) AS total_tenants,
            COUNT(DISTINCT payments.id) AS total_number_payments,
            SUM(payments.amount) AS total_amount_payment,
            (
                SELECT COUNT(*) 
                FROM expenses 
                WHERE expenses.house_id = tenants.house_id 
                  AND YEAR(expenses.date) = YEAR(CURDATE())
            ) AS total_number_expenses,
            (
                SELECT SUM(expenses.amount) 
                FROM expenses 
                WHERE expenses.house_id = tenants.house_id 
                  AND YEAR(expenses.date) = YEAR(CURDATE())
            ) AS total_amount_expenses,
            YEAR(payments.date_payment) AS payment_date
        FROM tenants
        LEFT JOIN houses ON tenants.house_id = houses.id
        LEFT JOIN payments ON tenants.house_id = payments.houses_id 
            AND payments.approval = 'true' 
            AND YEAR(payments.date_payment) = YEAR(CURDATE())
        WHERE YEAR(CURDATE()) = YEAR(payments.date_payment)";

        if (!empty($search)) {
            $query .= " AND (
                CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) LIKE ? OR 
                houses.house_name LIKE ?
            )";
        }
        $query .= " GROUP BY tenants.house_id, YEAR(payments.date_payment) ORDER BY tenants.house_id, YEAR(payments.date_payment)";
        $stmt = $conn->prepare($query);
        if (!empty($search)) {
            $searchParam = '%' . $search . '%';
            $stmt->bind_param('ss', $searchParam, $searchParam);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $reportsTotalPaymentsAmount = 0;
        $reportsTotalExpensesAmount = 0;
        $reportsTotalNetAmount = 0;

        while($row = $result->fetch_assoc()) {
            $IncomePerTenantTable_totalPayment = $row['total_amount_payment'];
            $IncomePerTenantTable_totalExpense = $row['total_amount_expenses'];
            $IncomePerTenantTable_netAmount = $IncomePerTenantTable_totalPayment - $IncomePerTenantTable_totalExpense;
            echo "<tr>";
            echo "<th scope='row' style='max-width: 100px;'>" . $row['tenant_names'] . "</th>";
            echo "<td style='width: 150px;'>" . htmlspecialchars($row['house_name']) . "</td>";
            echo "<td style='width: 100px;'>" . htmlspecialchars($row['total_number_payments']) . "</td>";
            echo "<td>" . htmlspecialchars($row['total_amount_payment']) . "</td>";
            echo "<td>" . htmlspecialchars($row['total_number_expenses']) . "</td>";
            echo "<td>" . $IncomePerTenantTable_totalExpense . "</td>";
            echo "<td>" . htmlspecialchars($IncomePerTenantTable_netAmount) . "</td>";
            echo "<td>" . (empty($row['payment_date']) ? $row['expense_date'] : htmlspecialchars($row['payment_date']) ) . "</td>";
            echo "</td>";
            echo "</tr>";
            $reportsTotalPaymentsAmount += $IncomePerTenantTable_totalPayment;
            $reportsTotalExpensesAmount += $IncomePerTenantTable_totalExpense;
            $reportsTotalNetAmount += $IncomePerTenantTable_netAmount;
        }
        echo "
        <tr style='border: transparent;'>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'>Total Payments: {$reportsTotalPaymentsAmount}</td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'>Total Expenses: {$reportsTotalExpensesAmount}</td>
            <td style='box-shadow: none;'>Total Income: {$reportsTotalNetAmount}</td>
            <td style='box-shadow: none;'></td>
        </tr>
        ";

    } else if ($startDate && $endDate) {
        // $query = "SELECT 
        //     tenants.house_id,
        //     houses.house_name,
        //     GROUP_CONCAT(DISTINCT tenants.id) AS tenant_ids,
        //     GROUP_CONCAT(DISTINCT CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) SEPARATOR ', ') AS tenant_names,
        //     COUNT(DISTINCT tenants.id) AS total_tenants,
        //     COUNT(DISTINCT payments.id) AS total_number_payments,
        //     SUM(payments.amount) AS total_amount_payment,
        //     (
        //         SELECT COUNT(*) 
        //         FROM expenses 
        //         WHERE expenses.house_id = tenants.house_id 
        //         AND expenses.date = payments.date_payment
        //     ) AS total_number_expenses,
        //     (
        //         SELECT SUM(expenses.amount) 
        //         FROM expenses 
        //         WHERE expenses.house_id = tenants.house_id 
        //         AND expenses.date = payments.date_payment
        //     ) AS total_amount_expenses,
        //     payments.date_payment AS payment_date
        // FROM tenants
        // LEFT JOIN houses ON tenants.house_id = houses.id
        // LEFT JOIN payments ON tenants.house_id = payments.houses_id
        // WHERE payments.date_payment BETWEEN ? AND ?
        // GROUP BY tenants.house_id, payments.date_payment
        // ORDER BY tenants.house_id, payments.date_payment";

        $query = "SELECT 
            tenants.house_id,
            houses.house_name,
            GROUP_CONCAT(DISTINCT tenants.id) AS tenant_ids,
            GROUP_CONCAT(DISTINCT CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) SEPARATOR ', ') AS tenant_names,
            COUNT(DISTINCT tenants.id) AS total_tenants,
            COUNT(DISTINCT payments.id) AS total_number_payments,
            SUM(payments.amount) AS total_amount_payment,
            (
                SELECT COUNT(*) 
                FROM expenses 
                WHERE expenses.house_id = tenants.house_id AND expenses.date = payments.date_payment
            ) AS total_number_expenses,
            (
                SELECT SUM(expenses.amount) 
                FROM expenses 
                WHERE expenses.house_id = tenants.house_id AND expenses.date = payments.date_payment
            ) AS total_amount_expenses,
            payments.date_payment AS payment_date,
            (
                SELECT MAX(expenses.date) FROM expenses
                WHERE expenses.house_id = tenants.house_id AND expenses.date = payments.date_payment
            ) AS expense_date
        FROM tenants
        LEFT JOIN houses ON tenants.house_id = houses.id
        LEFT JOIN payments ON tenants.house_id = payments.houses_id AND payments.approval = 'true'
        WHERE payments.date_payment BETWEEN ? AND ?
        GROUP BY tenants.house_id, payments.date_payment
        ORDER BY tenants.house_id, payments.date_payment";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();

        $reportsTotalPaymentsAmount = 0;
        $reportsTotalExpensesAmount = 0;
        $reportsTotalNetAmount = 0;

        while($row = $result->fetch_assoc()) {
            $IncomePerTenantTable_totalPayment = $row['total_amount_payment'];
            $IncomePerTenantTable_totalExpense = $row['total_amount_expenses'];
            $IncomePerTenantTable_netAmount = $IncomePerTenantTable_totalPayment - $IncomePerTenantTable_totalExpense;
            // echo "<tr>";
            // echo "<th scope='row' style='max-width: 100px;'>" . $row['tenant_names'] . "</th>";
            // echo "<td style='width: 150px;'>" . htmlspecialchars($row['house_name']) . "</td>";
            // echo "<td style='width: 100px;'>" . htmlspecialchars($row['total_number_payments']) . "</td>";
            // echo "<td>" . htmlspecialchars($row['total_amount_payment']) . "</td>";
            // echo "<td>" . htmlspecialchars($row['total_number_expenses']) . "</td>";
            // echo "<td>" . htmlspecialchars($IncomePerTenantTable_netAmount) . "</td>";
            // echo "<td>" . htmlspecialchars($row['payment_date']) . "</td>";
            // echo "</td>";
            // echo "</tr>";

            echo "<tr>";
            echo "<th scope='row' style='max-width: 100px;'>" . $row['tenant_names'] . "</th>";
            echo "<td style='width: 150px;'>" . htmlspecialchars($row['house_name']) . "</td>";
            echo "<td style='width: 100px;'>" . htmlspecialchars($row['total_number_payments']) . "</td>";
            echo "<td>" . htmlspecialchars($row['total_amount_payment']) . "</td>";
            echo "<td>" . htmlspecialchars($row['total_number_expenses']) . "</td>";
            echo "<td>" . $IncomePerTenantTable_totalExpense . "</td>";
            echo "<td>" . htmlspecialchars($IncomePerTenantTable_netAmount) . "</td>";
            // echo "<td>" . htmlspecialchars($row['payment_date']) . "</td>";
            echo "<td>" . (empty($row['payment_date']) ? $row['expense_date'] : htmlspecialchars($row['payment_date']) ) . "</td>";
            echo "</td>";
            echo "</tr>";
            $reportsTotalPaymentsAmount += $IncomePerTenantTable_totalPayment;
            $reportsTotalExpensesAmount += $IncomePerTenantTable_totalExpense;
            $reportsTotalNetAmount += $IncomePerTenantTable_netAmount;
        }
        echo "
        <tr style='border: transparent;'>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'>Total Payments: {$reportsTotalPaymentsAmount}</td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'>Total Expenses: {$reportsTotalExpensesAmount}</td>
            <td style='box-shadow: none;'>Total Income: {$reportsTotalNetAmount}</td>
            <td style='box-shadow: none;'></td>
        </tr>
        ";
    } else {
        $searchTerm = "%$search%"; // Prepare the search term with wildcards for partial matching
        // $query = "SELECT 
        //     tenants.house_id,
        //     houses.house_name,
        //     GROUP_CONCAT(DISTINCT tenants.id) AS tenant_ids,
        //     GROUP_CONCAT(DISTINCT CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) SEPARATOR ', ') AS tenant_names,
        //     COUNT(DISTINCT tenants.id) AS total_tenants,
        //     COUNT(DISTINCT payments.id) AS total_number_payments,
        //     SUM(payments.amount) AS total_amount_payment,
        //     (
        //         SELECT COUNT(*) 
        //         FROM expenses 
        //         WHERE expenses.house_id = tenants.house_id AND expenses.date = payments.date_payment
        //     ) AS total_number_expenses,
        //     (
        //         SELECT SUM(expenses.amount) 
        //         FROM expenses 
        //         WHERE expenses.house_id = tenants.house_id AND expenses.date = payments.date_payment
        //     ) AS total_amount_expenses,
        //     payments.date_payment AS payment_date
        // FROM tenants
        // LEFT JOIN houses ON tenants.house_id = houses.id
        // LEFT JOIN payments ON tenants.house_id = payments.houses_id AND payments.approval = 'true'
        // GROUP BY tenants.house_id, payments.date_payment
        // ORDER BY tenants.house_id, payments.date_payment";

        $query = "SELECT 
            tenants.house_id,
            houses.house_name,
            GROUP_CONCAT(DISTINCT tenants.id) AS tenant_ids,
            GROUP_CONCAT(DISTINCT CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) SEPARATOR ', ') AS tenant_names,
            COUNT(DISTINCT tenants.id) AS total_tenants,
            COUNT(DISTINCT payments.id) AS total_number_payments,
            SUM(payments.amount) AS total_amount_payment,
            (
                SELECT COUNT(*) 
                FROM expenses 
                WHERE expenses.house_id = tenants.house_id AND expenses.date = payments.date_payment
            ) AS total_number_expenses,
            (
                SELECT SUM(expenses.amount) 
                FROM expenses 
                WHERE expenses.house_id = tenants.house_id AND expenses.date = payments.date_payment
            ) AS total_amount_expenses,
            payments.date_payment AS payment_date,
            (
                SELECT MAX(expenses.date) FROM expenses
                WHERE expenses.house_id = tenants.house_id AND expenses.date = payments.date_payment
            ) AS expense_date
        FROM tenants
        LEFT JOIN houses ON tenants.house_id = houses.id
        LEFT JOIN payments ON tenants.house_id = payments.houses_id AND payments.approval = 'true'
        WHERE 
            houses.house_name LIKE ? OR 
            CONCAT(tenants.fname, ' ', tenants.mname, ' ', tenants.lname) LIKE ?
        GROUP BY tenants.house_id, payments.date_payment
        ORDER BY tenants.house_id, payments.date_payment";
        
        $stmt = $conn->prepare($query);
        // $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->bind_param("ss", $searchTerm, $searchTerm); // Bind search term for house name and tenant names
        $stmt->execute();
        $result = $stmt->get_result();

        $reportsTotalPaymentsAmount = 0;
        $reportsTotalExpensesAmount = 0;
        $reportsTotalNetAmount = 0;

        while($row = $result->fetch_assoc()) {
            $IncomePerTenantTable_totalPayment = $row['total_amount_payment'];
            $IncomePerTenantTable_totalExpense = $row['total_amount_expenses'];
            $IncomePerTenantTable_netAmount = $IncomePerTenantTable_totalPayment - $IncomePerTenantTable_totalExpense;
            echo "<tr>";
            echo "<th scope='row' style='max-width: 100px;'>" . $row['tenant_names'] . "</th>";
            echo "<td style='width: 150px;'>" . htmlspecialchars($row['house_name']) . "</td>";
            echo "<td style='width: 100px;'>" . htmlspecialchars($row['total_number_payments']) . "</td>";
            echo "<td>" . htmlspecialchars($row['total_amount_payment']) . "</td>";
            echo "<td>" . htmlspecialchars($row['total_number_expenses']) . "</td>";
            echo "<td>" . $IncomePerTenantTable_totalExpense . "</td>";
            echo "<td>" . htmlspecialchars($IncomePerTenantTable_netAmount) . "</td>";
            // echo "<td>" . htmlspecialchars($row['payment_date']) . "</td>";
            echo "<td>" . (empty($row['payment_date']) ? $row['expense_date'] : htmlspecialchars($row['payment_date']) ) . "</td>";
            echo "</td>";
            echo "</tr>";
            $reportsTotalPaymentsAmount += $IncomePerTenantTable_totalPayment;
            $reportsTotalExpensesAmount += $IncomePerTenantTable_totalExpense;
            $reportsTotalNetAmount += $IncomePerTenantTable_netAmount;
        }
        echo "
        <tr style='border: transparent;'>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'>Total Payments: {$reportsTotalPaymentsAmount}</td>
            <td style='box-shadow: none;'></td>
            <td style='box-shadow: none;'>Total Expenses: {$reportsTotalExpensesAmount}</td>
            <td style='box-shadow: none;'>Total Income: {$reportsTotalNetAmount}</td>
            <td style='box-shadow: none;'></td>
        </tr>
        ";
    }
}
?>