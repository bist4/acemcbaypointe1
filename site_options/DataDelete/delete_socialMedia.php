<?php
require('../../config/db_con.php');
session_start();

if ($conn->connect_error) {
    // Redirect to die_con.php if connection fails
    header("Location: ../../config/die_con.php");
    exit(); // Ensure that script execution stops after redirection
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'  && isset($_POST['SocialMediaID'])) {
    // Collect form data
    $SocialMediaID = $_POST['SocialMediaID'];

     // Fetch category name before deletion for logging purposes
    $queryFetchName = "SELECT Media_Name FROM social_media WHERE Social_MediaID = ?";
    $stmtFetchSocialMedia = $conn->prepare($queryFetchName);
    $stmtFetchSocialMedia->bind_param("i", $SocialMediaID);
    $stmtFetchSocialMedia->execute();
    $resultFetchSocial = $stmtFetchSocialMedia->get_result();

    if ($resultFetchSocial && $resultFetchSocial->num_rows > 0) {
        $row = $resultFetchSocial->fetch_assoc();
        $MediaName = $row['Media_Name'];

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
                $activity = 'Delete Social Media: ' . $MediaName;
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
    }

    // Update data in social_media table
    $sql = "UPDATE social_media SET Active = 0 WHERE Social_MediaID = ?";
    
    // Prepare statement
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("i", $SocialMediaID);

        // Execute statement
        if ($stmt->execute()) {

            
            $response['success'] = "Social Media deleted successfully";

             //logs activity

        } else {
            $response['error'] = "Error executing query: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    } else {
        $response['error'] = "Error preparing statement: " . $conn->error;
    }

    // Return JSON response
    echo json_encode($response);

    // Close connection
    $conn->close();
}
?>
