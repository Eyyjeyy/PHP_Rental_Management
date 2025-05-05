<?php
include '../../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;

    if ($startDate && $endDate) {
        // $query = "SELECT h.id, h.house_name, 
        //    IFNULL(MAX(t.date_end), CURDATE()) AS vacant_until
        // FROM houses h
        // LEFT JOIN tenants t ON h.id = t.house_id
        // GROUP BY h.id
        // HAVING (MAX(t.date_end) IS NULL OR MAX(t.date_end) BETWEEN ? AND ?)";

        // $stmt = $conn->prepare($query);
        // $stmt->bind_param("ss", $startDate, $endDate);
        // $stmt->execute();
        // $result = $stmt->get_result();

        // while($row = $result->fetch_assoc()) {
        //     echo "<tr>";
        //     echo "<th scope='row' style='max-width: 100px;'>" . $row['house_name'] . "</th>";
        //     echo "<td style='width: 150px;'>" . htmlspecialchars($row['vacant_until']) . "</td>";
        //     echo "<td style='width: 100px;'>" . htmlspecialchars($row['date_end']) . "</td>";
        //     echo "</tr>";
        // }



        $query = "WITH tenant_periods AS (
            SELECT 
                h.id AS house_id,
                h.house_name,
                h.date_registered,
                t.date_start,
                t.date_end
            FROM houses h
            LEFT JOIN tenants t ON h.id = t.house_id
        ),
        vacant_periods AS (
            SELECT 
                house_id,
                house_name,
                date_registered AS vacant_from,
                MIN(date_start) AS vacant_until
            FROM tenant_periods
            WHERE date_start IS NOT NULL
            GROUP BY house_id, house_name, date_registered

            UNION ALL

            SELECT 
                house_id,
                house_name,
                t.date_end AS vacant_from,
                LEAD(t.date_start) OVER (PARTITION BY house_id ORDER BY t.date_start) AS vacant_until
            FROM tenant_periods t
            WHERE t.date_end < CURDATE()

            UNION ALL

            SELECT 
                house_id,
                house_name,
                MAX(t.date_end) AS vacant_from,
                CURDATE() AS vacant_until
            FROM tenant_periods t
            WHERE NOT EXISTS (
                SELECT 1 FROM tenants t2 
                WHERE t2.house_id = t.house_id AND t2.date_end IS NULL
            )
            GROUP BY house_id, house_name

            UNION ALL

            SELECT
                h.id AS house_id,
                h.house_name,
                h.date_registered AS vacant_from,
                CURDATE() AS vacant_until
            FROM houses h
            LEFT JOIN tenants t ON h.id = t.house_id
            WHERE t.house_id IS NULL
        )
        SELECT 
            house_id,
            house_name,
            vacant_from,
            vacant_until
        FROM vacant_periods
        WHERE vacant_from < vacant_until
        AND (vacant_from BETWEEN ? AND ? OR vacant_until BETWEEN ? AND ?)
        ORDER BY house_id, vacant_from";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $startDate, $endDate, $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();

        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            // echo "<th scope='row' style='max-width: 100px;'>" . $row['house_name'] . "</th>";
            // echo "<td style='width: 150px;'>" . htmlspecialchars($row['vacant_until']) . "</td>";
            // echo "<td style='width: 100px;'>" . htmlspecialchars($row['date_end']) . "</td>";

            echo "<th scope='row' style='width: 100px;'>" . $row['house_name'] . "</th>";
            echo "<td style='width: 150px;'>" . $row['vacant_from'] . " - " . ($row['vacant_until'] ?? 'Currently Rented') . "</td>";
            // echo "<td style='width: 100px;'>" . htmlspecialchars($row['vacant_until']) . "</td>";
            echo "</tr>";
        }
    } else {

    }
}
?>