<?php
// Include the necessary database connection and configuration
include 'db_connect.php';

// Fetch the data from the database
$query = "SELECT houses.*, categories.name AS category_name FROM houses INNER JOIN categories ON categories.id = houses.category_id ORDER BY houses.id ASC";
$result = $conn->query($query);

// Initialize an empty array to store the data
$data = array();

// Loop through the result set and populate the data array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $house = [
            "id" => $row['id'],
            "house_number" => $row['house_number'],
            "price" => $row['price'],
            "category_name" => $row['category_name'],
            "delete_btn" => "<button type='button' class='btn btn-danger delete-house' data-id='" . $row['id'] . "' style='width: 80px;'>Delete</button>",  
            "update_btn" => "<a href='update_house.php?id=" . $row['id'] . "' class='btn btn-primary update-house'>Update</a>", // Update button with ID and class
          ];
          $data[] = $house;
    }
}

// Close the database connection
$conn->close();

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>

