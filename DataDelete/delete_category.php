<?php
require('../config/db_con.php');
session_start();

// Check if the request method is POST and if categoryId is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['categoryId'])) {
    $categoryId = $_POST['categoryId'];

    // Fetch category name before deletion for logging purposes
    $queryFetchCategoryName = "SELECT CategoryName FROM categories WHERE CategoryID = ?";
    $stmtFetchCategoryName = $conn->prepare($queryFetchCategoryName);
    $stmtFetchCategoryName->bind_param("i", $categoryId);
    $stmtFetchCategoryName->execute();
    $resultFetchCategoryName = $stmtFetchCategoryName->get_result();

    if ($resultFetchCategoryName && $resultFetchCategoryName->num_rows > 0) {
        $row = $resultFetchCategoryName->fetch_assoc();
        $categoryName = $row['CategoryName'];

        // Set the timezone to Asia/Manila
        date_default_timezone_set('Asia/Manila');
        $formattedDateTime = date('Y-m-d H:i:s');

        // Check if the user is logged in
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

                // Insert delete activity log into activity_history table
                $action = 'DELETE';
                $activity = 'Delete category: ' . $categoryName;
                $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                $stmtLog = $conn->prepare($sqlLog);
                $active = 1; // Assuming 1 represents active state
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
        
        // Proceed with the deletion after logging
        $query = "UPDATE categories SET Active = 0 WHERE CategoryID = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {    
            $stmt->bind_param("i", $categoryId);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Category not found']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
