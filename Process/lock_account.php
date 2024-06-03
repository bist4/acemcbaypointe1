<?php
require('../config/db_con.php');
session_start();

// Check if the request method is POST and if userID is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userID'])) {
    $userID = $_POST['userID'];

    // Update the lock_account field for the specified user
    $query = "UPDATE users SET is_Lock = 1 WHERE UserID = ?";

    // Prepare the SQL statement
    $stmt = $conn->prepare($query);

    if ($stmt) {    
        // Bind parameters
        $stmt->bind_param("i", $userID);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);

            // Log activity if the user is logged in
            if (isset($_SESSION['Username'])) {
                $loggedInUsername = $_SESSION['Username'];

                // Check if the logged-in user exists in the database
                $sqlUserCheck = "SELECT * FROM users WHERE Username=?";
                $stmtUserCheck = $conn->prepare($sqlUserCheck);
                $stmtUserCheck->bind_param("s", $loggedInUsername);
                $stmtUserCheck->execute();
                $resultUserCheck = $stmtUserCheck->get_result();

                if ($resultUserCheck && $resultUserCheck->num_rows > 0) {
                    $row = $resultUserCheck->fetch_assoc();
                    $loggedInUserID = $row['UserID'];

                    $action = 'LOCK';
                    $activity = 'Lock user account';
                    $currentDateTime = date('Y-m-d H:i:s');
                    $active = 1;

                    // Insert activity log into activity_history table
                    $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                    $stmtLog = $conn->prepare($sqlLog);
                    $stmtLog->bind_param("ssssi", $action, $activity, $currentDateTime, $loggedInUserID, $active);
                    $resultLog = $stmtLog->execute();

                    if (!$resultLog) {
                        // Handle log insertion failure
                        // You might want to log this failure or handle it accordingly
                    }

                } else {
                    // Handle user not found in database
                }

            }

        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        
        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
