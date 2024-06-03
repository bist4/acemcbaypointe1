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

    $DepartmentID = $_POST['DepartmentID'];
    // Sanitize input data
    $departmentTitle = mysqli_real_escape_string($conn, $_POST['departmentTitle']);

    // Check if front image is uploaded
    if (isset($_FILES['frontImage']) && $_FILES['frontImage']['error'] === UPLOAD_ERR_OK) {
        $frontImageName = $_FILES['frontImage']['name'];
        $frontImageTmp = $_FILES['frontImage']['tmp_name'];
        // Move the uploaded image to a desired location
        if (move_uploaded_file($frontImageTmp, '../../DataAdd/uploads/' . $frontImageName)) {
            // Image moved successfully
        } else {
            // Handle move operation failure
            // You might want to log this failure or handle it accordingly
        }
    } else {
        // Front image not uploaded, retain the existing value
        $frontImageName = ''; // Set default value or handle accordingly
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
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $services = mysqli_real_escape_string($conn, $_POST['services']);
    $doctorID = mysqli_real_escape_string($conn, $_POST['doctorID']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
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
        $sqlOriginalData = "SELECT Title, Description, Services, Email, FrontImage, BackImage FROM departments WHERE DepartmentID = ?";
        $stmtOriginalData = $conn->prepare($sqlOriginalData);
        $stmtOriginalData->bind_param("i", $DepartmentID);
        $stmtOriginalData->execute();
        $resultOriginalData = $stmtOriginalData->get_result();

        if ($resultOriginalData && $resultOriginalData->num_rows > 0) {
            $originalData = $resultOriginalData->fetch_assoc();
            $originalTitle = $originalData['Title'];
            $originalDescription = $originalData['Description'];
            $originalServices = $originalData['Services'];
            $originalEmail = $originalData['Email'];
            $originalFrontImage = $originalData['FrontImage'];
            $originalBackImage = $originalData['BackImage'];

            // Update department details
            $sqlUpdate = "UPDATE departments SET Title =?, Description=?, Services=?, Doctors=?, Email=?";
            $params = array($departmentTitle, $description, $services, $doctorID, $email);

            // Conditionally update image fields if new images are uploaded
            if ($frontImageName !== '') {
                $sqlUpdate .= ", FrontImage=?";
                $params[] = $frontImageName;
            } else {
                $frontImageName = $originalFrontImage; // Retain the original value
            }

            if ($backImageName !== '') {
                $sqlUpdate .= ", BackImage=?";
                $params[] = $backImageName;
            } else {
                $backImageName = $originalBackImage; // Retain the original value
            }

            $sqlUpdate .= " WHERE DepartmentID = ?";
            $params[] = $DepartmentID;

            $stmtUpdate = $conn->prepare($sqlUpdate);
            // Bind parameters dynamically
            $stmtUpdate->bind_param(str_repeat('s', count($params)), ...$params);

            if ($stmtUpdate->execute()) {
                // Department updated successfully

                // Construct activity log message
                $activity = 'Update ';
                $changes = array();

                if ($departmentTitle != $originalTitle) {
                    $changes[] = 'Title ' . $originalTitle . ' to ' . $departmentTitle;
                }

                if ($description != $originalDescription) {
                    $changes[] = 'Description ' . $originalDescription . ' to ' . $description;
                }

                if ($services != $originalServices) {
                    $changes[] = 'Services ' . $originalServices . ' to ' . $services;
                }

                if ($email != $originalEmail) {
                    $changes[] = 'Email ' . $originalEmail . ' to ' . $email;
                }

                if($frontImageName != $originalFrontImage) {
                    $changes[] = 'Front image '.'<img width="40" height="40" src="DataAdd/uploads/' . $originalFrontImage . '" alt="Original Front Image"> to <img width="40" height="40" src="DataAdd/uploads/' . $frontImageName . '" alt="New Front Image">';


                }

                if($backImageName != $originalBackImage)   {
                    $changes[] = 'Back Image '.'<img width="40" height="40" src="DataAdd/uploads/' . $originalBackImage . '" alt="Original Back Image"> to <img width="40" height="40" src="DataAdd/uploads/' . $backImageName . '" alt="New Back Image">';

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

                $response['success'] = "Department updated successfully";
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
