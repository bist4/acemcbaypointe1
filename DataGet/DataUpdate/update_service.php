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

    $ServiceID = $_POST['ServiceID'];
    // Sanitize input data
    $serviceTitle = mysqli_real_escape_string($conn, $_POST['serviceTitle']);

    // Check if front image is uploaded
    if (isset($_FILES['Image1']) && $_FILES['Image1']['error'] === UPLOAD_ERR_OK) {
        $frontImageName = $_FILES['Image1']['name'];
        $frontImageTmp = $_FILES['Image1']['tmp_name'];
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
    if (isset($_FILES['iconImage']) && $_FILES['iconImage']['error'] === UPLOAD_ERR_OK) {
        $IconImagename = $_FILES['iconImage']['name'];
        $IconImageTmp = $_FILES['iconImage']['tmp_name'];
        // Move the uploaded image to a desired location
        if (move_uploaded_file($IconImageTmp, '../../DataAdd/uploads/' . $IconImagename)) {
            // Image moved successfully
        } else {
            // Handle move operation failure
            // You might want to log this failure or handle it accordingly
        }
    } else {
        // Back image not uploaded, retain the existing value
        $IconImagename = ''; // Set default value or handle accordingly
    }

    // Retrieve other form data
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $services = mysqli_real_escape_string($conn, $_POST['services']);
    $doctorID = mysqli_real_escape_string($conn, $_POST['doctorID']);
    $contactNum1 = mysqli_real_escape_string($conn, $_POST['contactNum1']);
    $contactInfo = mysqli_real_escape_string($conn, $_POST['contactInfo']);

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
        $sqlOriginalData = "SELECT s.Title, s.Description, s.Services, d.Name, s.ImageService, s.Icon, s.ContactNumber, s.Contact_Details
        FROM services s
        INNER JOIN doctors d ON s.Doctors = d.DoctorID 
        WHERE s.ServiceID = ?";
        $stmtOriginalData = $conn->prepare($sqlOriginalData);
        $stmtOriginalData->bind_param("i", $ServiceID);
        $stmtOriginalData->execute();
        $resultOriginalData = $stmtOriginalData->get_result();

        if ($resultOriginalData && $resultOriginalData->num_rows > 0) {
            $originalData = $resultOriginalData->fetch_assoc();
            $originalTitle = $originalData['Title'];
            $originalDescription = $originalData['Description'];
            $originalServices = $originalData['Services'];
            $originalImage = $originalData['ImageService'];
            $originalIcon = $originalData['Icon'];
            $originalContactNum = $originalData['ContactNumber'];
            $originalContactInfo = $originalData['Contact_Details'];

            // Update department details
            $sqlUpdate = "UPDATE services SET Title =?, Description=?, Services=?, Doctors=?, ContactNumber=?, Contact_Details=?";
            $params = array($serviceTitle, $description, $services, $doctorID, $contactNum1, $contactInfo);

            // Conditionally update image fields if new images are uploaded
            if ($frontImageName !== '') {
                $sqlUpdate .= ", ImageService=?";
                $params[] = $frontImageName;
            } else {
                $frontImageName = $originalImage; // Retain the original value
            }

            if ($IconImagename !== '') {
                $sqlUpdate .= ", Icon=?";
                $params[] = $IconImagename;
            } else {
                $IconImagename = $originalIcon; // Retain the original value
            }

            $sqlUpdate .= " WHERE ServiceID = ?";
            $params[] = $ServiceID;

            $stmtUpdate = $conn->prepare($sqlUpdate);
            // Bind parameters dynamically
            $stmtUpdate->bind_param(str_repeat('s', count($params)), ...$params);

            if ($stmtUpdate->execute()) {
                // Department updated successfully

                // Construct activity log message
                $activity = 'Update ';
                $changes = array();

                if ($serviceTitle != $originalTitle) {
                    $changes[] = 'Title ' . $originalTitle . ' to ' . $serviceTitle;
                }

                if ($description != $originalDescription) {
                    $changes[] = 'Description ' . $originalDescription . ' to ' . $description;
                }

                if ($services != $originalServices) {
                    $changes[] = 'Services ' . $originalServices . ' to ' . $services;
                }

               

                if($frontImageName != $originalImage) {
                    $changes[] = 'Image '.'<img width="40" height="40" src="DataAdd/uploads/' . $originalImage . '" alt="Original Front Image"> to <img width="40" height="40" src="DataAdd/uploads/' . $frontImageName . '" alt="New Front Image">';


                }

                if($IconImagename != $originalIcon)   {
                    $changes[] = 'Icon '.'<img width="40" height="40" src="DataAdd/uploads/' . $originalIcon . '" alt="Original Back Image"> to <img width="40" height="40" src="DataAdd/uploads/' . $IconImagename . '" alt="New Back Image">';

                }
                

                if($contactNum1 != $originalContactNum)   {
                    $changes[] = 'Contact Number ' . $originalContactNum . ' to ' . $contactNum1;

                }

                
                if($contactInfo != $originalContactInfo)   {
                    $changes[] = 'Contact Information ' . $originalContactInfo . ' to ' . $contactInfo;

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

                $response['success'] = "Service updated successfully";
            } else {
                // Error occurred while updating department
                $response['error'] = "Error updating service: " . $stmtUpdate->error;
            }
        } else {
            // Handle the case where the original service data is not found
            $response['error'] = "Original service data not found";
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
