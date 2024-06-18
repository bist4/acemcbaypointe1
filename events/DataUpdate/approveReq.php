<?php
session_start();
require ('../../config/db_con.php');

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if eventID is set and is a valid integer
    if (isset($_POST['eventID']) && is_numeric($_POST['eventID'])) {
        $eventID = $_POST['eventID'];
        $eventTitle = $_POST['eventTitle'];

        
        $user = $_SESSION['Username'];

        $status = 'REQUEST APPROVED';
        $loggedInUsername = $_SESSION['Username'];
        $sqlUserCheck = "SELECT Fname, Lname FROM users WHERE Username=?";
        $stmtUserCheck = $conn->prepare($sqlUserCheck);
        $stmtUserCheck->bind_param("s", $loggedInUsername);
        $stmtUserCheck->execute();
        $resultUserCheck = $stmtUserCheck->get_result();
        $row = $resultUserCheck->fetch_assoc();
        $user = $row['Fname'] . ' ' . $row['Lname'];

        $canEdit = 1;
        $decisionStatus = 'Approved the request by ' . $user;
        $message = 'Your request was approved';
        $authorWithParentheses = $_POST['author'];
        $author = preg_replace('/\s*\(.*?\)\s*/', '', $authorWithParentheses);

        // Prepare and bind parameters for the SQL query
        $sql = "UPDATE events SET Status = ?, Decision_Status = ?, can_edit = ? WHERE EventID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $status, $decisionStatus, $canEdit, $eventID);

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
                    $activity = 'Approve Event title '. $eventTitle;
                    date_default_timezone_set('Asia/Manila');
                    $formattedDateTime = date('Y-m-d H:i:s');
                    $active = 1; // Assuming 'Active' field is boolean

                    $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                    $stmtLog = $conn->prepare($sqlLog);
                    $stmtLog->bind_param("sssii", $action, $activity, $formattedDateTime, $loggedInUserID, $active);
                    $stmtLog->execute();

                    

                    $act = 'Your request to edit the event titled '.$eventTitle.' has been approved.';
                    $sqlMessages = "INSERT INTO messages (Messages, Activity, MFrom, MTo, Date, EventID) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmtMessages = $conn->prepare($sqlMessages);
                    $stmtMessages->bind_param("ssissi", $message, $act, $loggedInUserID, $author, $formattedDateTime, $eventID);
                    $stmtMessages->execute();

                    

                } else {
                    $response['error'] = "User not found in the database!";
                }
            }
            // If the query is successful, return success message
            $response['success'] = "Approved Successfully!";
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