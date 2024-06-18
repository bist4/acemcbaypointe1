<?php
session_start();
require('../../config/db_con.php');

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if newsID is set and is a valid integer
    if(isset($_POST['newsID']) && is_numeric($_POST['newsID'])) {
        $newsID = $_POST['newsID'];
        $status = "REJECT";
        $newsTitle = $_POST['newsTitle'];

        $user = $_SESSION['Username'];

        $loggedInUsername = $_SESSION['Username'];
        $sqlUserCheck = "SELECT Fname, Lname FROM users WHERE Username=?";
        $stmtUserCheck = $conn->prepare($sqlUserCheck);
        $stmtUserCheck->bind_param("s", $loggedInUsername);
        $stmtUserCheck->execute();
        $resultUserCheck = $stmtUserCheck->get_result();
        $row = $resultUserCheck->fetch_assoc();
        $user = $row['Fname'] . ' ' . $row['Lname'];


        $decisionStatus = 'Reject by ' . $user;

        $message = $_POST['message'];
        $authorWithParentheses = $_POST['author'];
        $author = preg_replace('/\s*\(.*?\)\s*/', '', $authorWithParentheses);
        
        // Prepare and bind parameters for the SQL query
        $sql = "UPDATE news SET Status = ?, Decision_Status = ? WHERE NewsID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $status, $decisionStatus, $newsID);

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
                    $activity = 'Reject news title '. $newsTitle;
                    date_default_timezone_set('Asia/Manila');
                    $formattedDateTime = date('Y-m-d H:i:s');
                    $active = 1; // Assuming 'Active' field is boolean
                    
                    $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                    $stmtLog = $conn->prepare($sqlLog);
                    $stmtLog->bind_param("sssii", $action, $activity, $formattedDateTime, $loggedInUserID, $active);
                    $stmtLog->execute();

                    $act = 'Reject your news post';
                    $sqlMessages = "INSERT INTO messages (Messages, Activity, MFrom, MTo, Date, NewsID) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmtMessages = $conn->prepare($sqlMessages);
                    $stmtMessages->bind_param("ssissi", $message, $act, $loggedInUserID, $author, $formattedDateTime, $newsID);
                    $stmtMessages->execute();

                } else {
                    $response['error'] = "User not found in the database!";
                }
            }
            // If the query is successful, return success message
            $response['success'] = "Reject Successfully!";
        } else {
            // If the query fails, return error message
            $response['error'] = "Failed to update news status!";
        }
    } else {
        $response['error'] = "Invalid news ID!";
    }
} else {
    $response['error'] = "Invalid request method!";
}

// Return response in JSON format
echo json_encode($response);
?>
