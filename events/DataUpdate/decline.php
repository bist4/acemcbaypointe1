<?php
session_start();
require('../../config/db_con.php');

$response = array();
// SELECT m.MTo, u.Fname FROM `messages` m INNER JOIN users u ON m.MTo = u.Fname;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if eventID is set and is a valid integer
    if(isset($_POST['eventID']) && is_numeric($_POST['eventID'])) {
        $eventID = $_POST['eventID'];
        $status = "DECLINE";
        $message = $_POST['message'];
        $authorWithParentheses = $_POST['author'];
        $author = preg_replace('/\s*\(.*?\)\s*/', '', $authorWithParentheses);
        
        // Prepare and bind parameters for the SQL query
        $sql = "UPDATE events SET Status = ? WHERE EventID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $eventID);

        if ($stmt->execute()) {
            // Log activity if the user is logged in
            if (isset($_SESSION['Username'])) {
                $loggedInUsername = $_SESSION['Username'];
                $sqlUserCheck = "SELECT UserID FROM users WHERE Username=?";
                $stmtUserCheck = $conn->prepare($sqlUserCheck);
                $stmtUserCheck->bind_param("s", $loggedInUsername);
                $stmtUserCheck->execute();
                $resultUserCheck = $stmtUserCheck->get_result();

                if ($resultUserCheck && $resultUserCheck->num_rows > 0) {
                    $row = $resultUserCheck->fetch_assoc();
                    $loggedInUserID = $row['UserID'];

                    // Insert activity log into activity_history table
                    $action = 'UPDATE';
                    $activity = 'DECLINE EVENT';
                    $formattedDateTime = date('Y-m-d H:i:s');
                    $active = 1; // Assuming 'Active' field is boolean
                    
                    $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                    $stmtLog = $conn->prepare($sqlLog);
                    $stmtLog->bind_param("sssii", $action, $activity, $formattedDateTime, $loggedInUserID, $active);
                    $stmtLog->execute();

                    $act = 'Decline your post event';
                    $sqlMessages = "INSERT INTO messages (Messages, Activity, MFrom, MTo, Date) VALUES (?, ?, ?, ?, ?)";
                    $stmtMessages = $conn->prepare($sqlMessages);
                    $stmtMessages->bind_param("ssiss", $message, $act, $loggedInUserID, $author, $formattedDateTime);
                    $stmtMessages->execute();

                } else {

                    $response['error'] = "User not found in the database!";
                }
            }
            // If the query is successful, return success message
            $response['success'] = "Decline Successfully!";
        } else {
            // If the query fails, return error message
            $response['error'] = "Failed to update event status!";
        }
    } else {
        $response['error'] = "Invalid event ID!";
    }
} else {
    $response['error'] = "Invalid request method!";
}

// Return response in JSON format
echo json_encode($response);
?>
