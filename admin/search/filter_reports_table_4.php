<?php
include '../../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;

    if ($startDate && $endDate) {
        $query = "SELECT 
            DATE(t.date_start) AS date, 
            COUNT(DISTINCT t.id) AS number_of_tenants, 
            COUNT(DISTINCT t.house_id) AS number_of_apartments
        FROM tenants t
        WHERE t.date_start IS NOT NULL
        AND DATE(t.date_start) BETWEEN ? AND ?
        GROUP BY DATE(t.date_start)
        ORDER BY DATE(t.date_start)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();

        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<th scope='row' style='width: 100px;'>" . $row['number_of_tenants'] . "</th>";
            echo "<td style='width: 150px;'>" . htmlspecialchars($row['number_of_apartments']) . "</td>";
            echo "<td style='width: 100px;'>" . htmlspecialchars($row['date']) . "</td>";
            echo "</tr>";
        }
    } else {
        $query = "SELECT 
            DATE(t.date_start) AS date, 
            COUNT(DISTINCT t.id) AS number_of_tenants, 
            COUNT(DISTINCT t.house_id) AS number_of_apartments
        FROM tenants t
        WHERE t.date_start IS NOT NULL
        GROUP BY DATE(t.date_start)
        ORDER BY DATE(t.date_start)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();

        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<th scope='row' style='width: 100px;'>" . $row['number_of_tenants'] . "</th>";
            echo "<td style='width: 150px;'>" . htmlspecialchars($row['number_of_apartments']) . "</td>";
            echo "<td style='width: 100px;'>" . htmlspecialchars($row['date']) . "</td>";
            echo "</tr>";
        }
    }
}
?>