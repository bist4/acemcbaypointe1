<?php

require('../../../config/db_con.php');
session_start();

// Check if the connection is successful
if ($conn->connect_error) {
    // Redirect to die_con.php if connection fails
    header("Location: ../config/die_con.php");
    exit(); // Ensure that script execution stops after redirection
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $Title_SloganID = $_POST['Title_SloganID'];
    // Sanitize input data
    $webTitle1 = mysqli_real_escape_string($conn, $_POST['webTitle1']);

    // Check if front image is uploaded
    if (isset($_FILES['fileLogo']) && $_FILES['fileLogo']['error'] === UPLOAD_ERR_OK) {
        $fileLogoName = $_FILES['fileLogo']['name'];
        $fileLogoTmp = $_FILES['fileLogo']['tmp_name'];
        // Move the uploaded image to a desired location
        if (move_uploaded_file($fileLogoTmp, '../../../DataAdd/uploads/' . $fileLogoName)) {
            // Image moved successfully
        } else {
            // Handle move operation failure
            // You might want to log this failure or handle it accordingly
        }
    } else {
        // Front image not uploaded, retain the existing value
        $fileLogoName = ''; // Set default value or handle accordingly
    }

    // Check if back image is uploaded
    if (isset($_FILES['backImage']) && $_FILES['backImage']['error'] === UPLOAD_ERR_OK) {
        $backImageName = $_FILES['backImage']['name'];
        $backImageTmp = $_FILES['backImage']['tmp_name'];
        // Move the uploaded image to a desired location
        if (move_uploaded_file($backImageTmp, '../../DataAdd/uploads/' . $backImageName)) {
            // Image moved successfully
        } else {
            // Handle move operation failure
            // You might want to log this failure or handle it accordingly
        }
    } else {
        // Back image not uploaded, retain the existing value
        $backImageName = ''; // Set default value or handle accordingly
    }

    // Retrieve other form data
    $slogan1 = mysqli_real_escape_string($conn, $_POST['slogan1']);
 
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
        $sqlOriginalData = "SELECT Website_Title, Slogan, Logo FROM title_slogan WHERE Title_SloganID = ?";
        $stmtOriginalData = $conn->prepare($sqlOriginalData);
        $stmtOriginalData->bind_param("i", $Title_SloganID);
        $stmtOriginalData->execute();
        $resultOriginalData = $stmtOriginalData->get_result();

        if ($resultOriginalData && $resultOriginalData->num_rows > 0) {
            $originalData = $resultOriginalData->fetch_assoc();
            $originalWebTitle = $originalData['Website_Title'];
            $originalSlogan = $originalData['Slogan'];
            $originalLogo = $originalData['Logo'];
            

            // Update department details
            $sqlUpdate = "UPDATE title_slogan SET Website_Title =?, Slogan=?";
            $params = array($webTitle1, $slogan1);

            // Conditionally update image fields if new images are uploaded
            if ($fileLogoName !== '') {
                $sqlUpdate .= ", Logo=?";
                $params[] = $fileLogoName;
            } else {
                $fileLogoName = $originalLogo; // Retain the original value
            }

 

            $sqlUpdate .= " WHERE Title_SloganID = ?";
            $params[] = $Title_SloganID;

            $stmtUpdate = $conn->prepare($sqlUpdate);
            // Bind parameters dynamically
            $stmtUpdate->bind_param(str_repeat('s', count($params)), ...$params);

            if ($stmtUpdate->execute()) {
                // Department updated successfully

                // Construct activity log message
                $activity = 'Update ';
                $changes = array();

                if ($webTitle1 != $originalWebTitle) {
                    $changes[] = 'Website Title ' . $originalWebTitle . ' to ' . $webTitle1;
                }

                if ($slogan1 != $originalSlogan) {
                    $changes[] = 'Slogan ' . $originalSlogan . ' to ' . $slogan1;
                }

               

                if($fileLogoName != $originalLogo) {
                    $changes[] = 'Logo '.'<img width="40" height="40" src="DataAdd/uploads/' . $originalLogo . '" alt="Original Front Image"> to <img width="40" height="40" src="DataAdd/uploads/' . $fileLogoName . '" alt="New Front Image">';


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

                $response['success'] = "Title and Slogan Updated successfully";
            } else {
                // Error occurred while updating department
                $response['error'] = "Error updating department: " . $stmtUpdate->error;
            }
        } else {
            // Handle the case where the original department data is not found
            $response['error'] = "Original department data not found";
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
