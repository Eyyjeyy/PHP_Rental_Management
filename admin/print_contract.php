<?php
// print_contract.php

include '../admin.php';
$admin = new Admin();
use PhpOffice\PhpWord\IOFactory;
include("../db_connect.php");

// Retrieve the ID passed via the query string
$printid = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Example SQL query to fetch data
$query ="
            SELECT * FROM contracts
            INNER JOIN tenants ON contracts.tenants_id = tenants.id
            INNER JOIN users ON tenants.users_id = users.id
            WHERE contracts.id = $printid
        "; 
$result = $admin->conn->query($query);

// Start HTML content
echo "<html>
<head>
    <title>Printable Contracts</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Contracts</h1>
    <table>
        <thead>
            <tr>
                <th>Tenant</th>
                <th>Contract Start</th>
                <th>Contract Expiry</th>
                <th>$printid</th>
            </tr>
        </thead>
        <tbody>";

// Populate table rows with data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $filePath = '..' . $row['fileurl']; // URL or path to your .docx file
        $encodedPath = ($filePath);

        // Define the destination directory
        $directory = '../asset/user_contracts/';

        // Ensure the directory exists
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);  // Create the directory if it doesn't exist
        }

        // Define the path for the temporary file
        $tempFilePath = $directory . 'docx_' . uniqid() . '.docx';

        // Copy the original file to the new location
        if (copy($filePath, $tempFilePath)) {
            // Load the temporary .docx file using PHPWord
            $phpWord = IOFactory::load($tempFilePath);
            
            // Start output buffering to capture HTML output
            ob_start();
            
            // Convert the .docx to HTML format
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
            $htmlWriter->save('php://output');
            
            // Get the captured output (HTML content)
            $htmlContent = ob_get_clean();
            

        }

        echo "<tr>
                 <td>
                    <div style='width:100%; height:700px; overflow-y:auto;'>
                    $htmlContent
                    </div>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='3'>No data available</td></tr>";
}

echo "</tbody>
    </table>
</body>
</html>";
?>
