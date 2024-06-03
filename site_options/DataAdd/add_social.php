<?php
require('../../config/db_con.php');
session_start();

$response = array(); // Initialize response array

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if file is uploaded
    
 
    $socialName = mysqli_real_escape_string($conn, $_POST['socialName']);
    $link = mysqli_real_escape_string($conn, $_POST['link']);
    $active = 1;

    // Use prepared statement to prevent SQL injection
    $sql = "INSERT INTO social_media (Media_Name, Link, Active, CreatedAt)
            VALUES (?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $socialName, $link, $active);

    if ($stmt->execute()) {

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
                $activity = 'Add new social media name: ' . $socialName;
                
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

        $response['success'] = "New social media created successfully";
    } else {
        $response['error'] = "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close prepared statement
    $stmt->close();

    // Return JSON response
    echo json_encode($response);
}
 
?>
