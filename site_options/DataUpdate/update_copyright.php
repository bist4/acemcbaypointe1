<?php

require('../../config/db_con.php');
session_start();

// Check if the connection is successful
if ($conn->connect_error) {
    // Redirect to die_con.php if connection fails
    header("Location: ../config/die_con.php");
    exit(); // Ensure that script execution stops after redirection
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $copyrightID = $_POST['copyrightID'];
    // Sanitize input data
    $copyright = mysqli_real_escape_string($conn, $_POST['copyright']);

    
   
 
    $active = 1;

    $userAuthor = $_SESSION['Username'];

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

        // Fetch original department details before updating
        $sqlOriginalData = "SELECT CopyrightName FROM copyright WHERE CopyrightID = ?";
        $stmtOriginalData = $conn->prepare($sqlOriginalData);
        $stmtOriginalData->bind_param("i", $copyrightID);
        $stmtOriginalData->execute();
        $resultOriginalData = $stmtOriginalData->get_result();

        if ($resultOriginalData && $resultOriginalData->num_rows > 0) {
            $originalData = $resultOriginalData->fetch_assoc();
            $originalCopyrightname = $originalData['CopyrightName'];
          
    
            

            // Update department details
            $sqlUpdate = "UPDATE copyright SET  CopyrightName=?";
            $params = array($copyright);

    

            $sqlUpdate .= " WHERE CopyrightID = ?";
            $params[] = $copyrightID;

            $stmtUpdate = $conn->prepare($sqlUpdate);
            // Bind parameters dynamically
            $stmtUpdate->bind_param(str_repeat('s', count($params)), ...$params);

            if ($stmtUpdate->execute()) {
                // Department updated successfully

                // Construct activity log message
                $activity = 'Update ';
                $changes = array();

                if ($copyright != $originalCopyrightname) {
                    $changes[] = 'Footer ' . $originalCopyrightname . ' to ' . $copyright;
                }

                
                // Constructing activity message
                $activity .= implode(', ', $changes);

                // Log activity only if there were changes
                if (!empty($changes)) {
                    // Log the activity
                    // Set the timezone to Asia/Manila
                    date_default_timezone_set('Asia/Manila');
                    $formattedDateTime = date('Y-m-d H:i:s');

                    // Insert activity log into activity_history table
                    $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                    $stmtLog = $conn->prepare($sqlLog);
                    $action = 'UPDATE';
                    $stmtLog->bind_param("sssii", $action, $activity, $formattedDateTime, $loggedInUserID, $active);
                    $stmtLog->execute();
                }

                $response['success'] = "Social Media Updated successfully";
            } 
            else {
                // Error occurred while updating department
                $response['error'] = "Error updating social media: " . $stmtUpdate->error;
            }
        } else {
            // Handle the case where the original department data is not found
            $response['error'] = "Original social media data not found";
        }
    } else {
        // Handle user not found in database
        $response['error'] = "User not found in the database";
    }

    // Return JSON response
    echo json_encode($response);

    // Close statements
    $stmtUserCheck->close();
    $stmtUpdate->close();
    $stmtOriginalData->close();

    // Close connection
    $conn->close();
}
?>