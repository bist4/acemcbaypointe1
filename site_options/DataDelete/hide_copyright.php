<?php
require ('../../config/db_con.php');
session_start();

if ($conn->connect_error) {
    // Redirect to die_con.php if connection fails
    header("Location: ../../config/die_con.php");
    exit(); // Ensure that script execution stops after redirection
}

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the required parameters are set
    if (isset($_POST['copyrightID']) && isset($_POST['isActive'])) {
        // Sanitize and retrieve the parameters
        $copyrightID = $_POST['copyrightID'];
        $isActive = $_POST['isActive'];

        // Toggle the Active state
        $newState = $isActive == 1 ? 0 : 1;
        $userAuthor = $_SESSION['Username'];
        // Update data in the copyright table
        $sql = "UPDATE copyright SET Active = ? WHERE CopyrightID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $newState, $copyrightID);
        $stmt->execute();

        // Check if the update was successful
        if ($stmt->affected_rows > 0) {
            // Send success response with appropriate message
            if ($newState == 1) {
                // Check if the logged-in user exists in the database
                $sqlUserCheck = "SELECT UserID FROM users WHERE Username=?";
                $stmtUserCheck = $conn->prepare($sqlUserCheck);
                $stmtUserCheck->bind_param("s", $userAuthor);
                $stmtUserCheck->execute();
                $resultUserCheck = $stmtUserCheck->get_result();

                if ($resultUserCheck && $resultUserCheck->num_rows > 0) {
                    // User found, fetch user details
                    $user = $resultUserCheck->fetch_assoc();
                    $loggedInUserID = $user['UserID'];
                    $activity = "Show Footer";
                    if (!empty($activity)) {
                        // Log the activity
                        // Set the timezone to Asia/Manila
                        date_default_timezone_set('Asia/Manila');
                        $formattedDateTime = date('Y-m-d H:i:s');

                        // Insert activity log into activity_history table
                        $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                        $stmtLog = $conn->prepare($sqlLog);
                        $action = 'SHOW';
                        $active = 1;
                        $stmtLog->bind_param("sssii", $action, $activity, $formattedDateTime, $loggedInUserID, $active);
                        $stmtLog->execute();
                    }
                }
                echo json_encode(["success" => "Show successful"]);
            } else {
                $sqlUserCheck = "SELECT UserID FROM users WHERE Username=?";
                $stmtUserCheck = $conn->prepare($sqlUserCheck);
                $stmtUserCheck->bind_param("s", $userAuthor);
                $stmtUserCheck->execute();
                $resultUserCheck = $stmtUserCheck->get_result();

                if ($resultUserCheck && $resultUserCheck->num_rows > 0) {
                    // User found, fetch user details
                    $user = $resultUserCheck->fetch_assoc();
                    $loggedInUserID = $user['UserID'];
                    $activity = "Hide Footer ";
                    if (!empty($activity)) {
                        // Log the activity
                        // Set the timezone to Asia/Manila
                        date_default_timezone_set('Asia/Manila');
                        $formattedDateTime = date('Y-m-d H:i:s');

                        // Insert activity log into activity_history table
                        $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                        $stmtLog = $conn->prepare($sqlLog);
                        $action = 'HIDE';
                        $active = 1;
                        $stmtLog->bind_param("sssii", $action, $activity, $formattedDateTime, $loggedInUserID, $active);
                        $stmtLog->execute();
                    }
                }
                echo json_encode(["success" => "Hide successful"]);
            }
            exit;
        } else {
            // Send error response
            echo json_encode(["error" => "Failed to toggle"]);
            exit;
        }
    } else {
        // Send error response if required parameters are not set
        echo json_encode(["error" => "Required parameters are missing"]);
        exit;
    }
} else {
    // Send error response if not a POST request
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}
?>