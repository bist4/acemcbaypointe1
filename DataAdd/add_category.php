<?php
require('../config/db_con.php');
session_start();

// Check if the connection is successful
if ($conn->connect_error) {
    // Redirect to die_con.php if connection fails
    header("Location: ../config/die_con.php");
    exit(); // Ensure that script execution stops after redirection
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $CategoryName = mysqli_real_escape_string($conn, $_POST['categoryName']); // Sanitize input data
    
    $active = 1;

    // Check if the category name already exists
    $sqlCheck = "SELECT * FROM categories WHERE CategoryName = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $CategoryName);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // Category name already exists
        $response['error'] = "Category name already exists";
    } else {
        // Insert data into categories table
        $sql = "INSERT INTO categories (CategoryName, Active, CreatedAt) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $CategoryName, $active); // "si" indicates string and integer parameters
        if ($stmt->execute()) {
            // Log activity if the user is logged in
            if (isset($_SESSION['Username'])) {
                $loggedInUsername = $_SESSION['Username'];

                // Check if the logged-in user exists in the database
                $sqlUserCheck = "SELECT UserID FROM users WHERE Username=?";
                $stmtUserCheck = $conn->prepare($sqlUserCheck);
                $stmtUserCheck->bind_param("s", $loggedInUsername);
                $stmtUserCheck->execute();
                $resultUserCheck = $stmtUserCheck->get_result();

                if ($resultUserCheck && $resultUserCheck->num_rows > 0) {
                    $row = $resultUserCheck->fetch_assoc();
                    $loggedInUserID = $row['UserID'];

                    $action = 'ADD';
                    $activity = 'Add new category: ' . $CategoryName;
                    
                    // Set the timezone to Asia/Manila
                    date_default_timezone_set('Asia/Manila');
                    
                    $formattedDateTime = date('Y-m-d H:i:s');

                    // Insert activity log into activity_history table
                    $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                    $stmtLog = $conn->prepare($sqlLog);
                    $stmtLog->bind_param("ssssi", $action, $activity, $formattedDateTime, $loggedInUserID, $active);
                    $resultLog = $stmtLog->execute();

                    if (!$resultLog) {
                        // Handle log insertion failure
                        // You might want to log this failure or handle it accordingly
                    }

                } else {
                    // Handle user not found in database
                }
            }

            $response['success'] = "New Category created successfully";
            
        } else {
            $response['error'] = "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }

    // Return JSON response
    echo json_encode($response);

    // Close connection
    $conn->close();
}
?>
