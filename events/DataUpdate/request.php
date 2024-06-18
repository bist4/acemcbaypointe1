<?php
session_start();
require ('../../config/db_con.php');

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if eventID is set and is a valid integer
    if (isset($_POST['eventID']) && is_numeric($_POST['eventID'])) {
        $eventID = $_POST['eventID'];
        $status = "UNDER REVIEW";
        $user = $_SESSION['Username'];


        $loggedInUsername = $_SESSION['Username'];
        $sqlUserCheck = "SELECT Fname, Lname FROM users WHERE Username=?";
        $stmtUserCheck = $conn->prepare($sqlUserCheck);
        $stmtUserCheck->bind_param("s", $loggedInUsername);
        $stmtUserCheck->execute();
        $resultUserCheck = $stmtUserCheck->get_result();
        $row = $resultUserCheck->fetch_assoc();
        $user = $row['Fname'] . ' ' . $row['Lname'];







        $decisionStatus = 'Requested by ' . $user;
        $message = $_POST['message'];
        $authorWithParentheses = $_POST['author'];
        $author = preg_replace('/\s*\(.*?\)\s*/', '', $authorWithParentheses);

        // Prepare and bind parameters for the SQL query
        $sql = "UPDATE events SET Status = ?, Decision_Status = ? WHERE EventID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $status, $decisionStatus, $eventID);

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
                    $activity = 'APPROVED EVENT';
                    date_default_timezone_set('Asia/Manila');
                    $formattedDateTime = date('Y-m-d H:i:s');
                    $active = 1; // Assuming 'Active' field is boolean

                    $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                    $stmtLog = $conn->prepare($sqlLog);
                    $stmtLog->bind_param("sssii", $action, $activity, $formattedDateTime, $loggedInUserID, $active);
                    $stmtLog->execute();

                    $eventTit = "SELECT EventTitle FROM events WHERE EventID=?";
                    $stmtEventTtl = $conn->prepare($eventTit);
                    $stmtEventTtl->bind_param("i", $eventID);
                    $stmtEventTtl->execute();
                    $resulEventTtl = $stmtEventTtl->get_result();

                    if ($resulEventTtl && $resulEventTtl->num_rows > 0) {
                        $row = $resulEventTtl->fetch_assoc();
                        $eventTitle = $row['EventTitle'];

                        $act = 'Request to edit the event ' . $eventTitle;

                        // Prepare and execute SQL statement for inserting into messages table
                        $sqlMessages = "INSERT INTO messages (Messages, Activity, MFrom, MTo, Date, EventID) VALUES (?, ?, ?, ?, ?, ?)";
                        $stmtMessages = $conn->prepare($sqlMessages);
                        $stmtMessages->bind_param("ssissi", $message, $act, $loggedInUserID, $author, $formattedDateTime, $eventID);

                        if ($stmtMessages->execute()) {
                            // If the query is successful, set success response
                            $response['success'] = "Your request was Successfully sent";
                        } else {
                            // If the query fails, set error response
                            $response['error'] = "Failed to insert message into messages table!";
                        }
                    } else {
                        // If no event found or EventTitle not available, set error response
                        $response['error'] = "Event not found or EventTitle not available!";
                    }

                } else {
                    $response['error'] = "User not found in the database!";
                }
            }
            // If the query is successful, return success message
            $response['success'] = "Your request was Successfully sent";
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