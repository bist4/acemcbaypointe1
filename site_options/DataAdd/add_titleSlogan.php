<?php
require('../../config/db_con.php');
session_start();

$response = array(); // Initialize response array

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if file is uploaded
    if (isset($_FILES['fileLogo']) && !empty($_FILES['fileLogo']['name'])) {
        $fileLogo = handleUpload('fileLogo');
    } else {
        $response['error'] = 'Please select a fileLogo image to upload.';
        echo json_encode($response);
        exit(); // Stop script execution
    }

 
    $webTitle = mysqli_real_escape_string($conn, $_POST['webTitle']);
    $slogan = mysqli_real_escape_string($conn, $_POST['slogan']);
    $active = 1;

    // Use prepared statement to prevent SQL injection
    $sql = "INSERT INTO title_slogan (Website_Title, Slogan, Logo, Active, CreatedAt)
            VALUES (?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $webTitle, $slogan, $fileLogo, $active);

    if ($stmt->execute()) {

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

                $action = 'ADD';
                $activity = 'Add new tile and slogan: ' . $webTitle;
                
                // Set the timezone to Asia/Manila
                date_default_timezone_set('Asia/Manila');
                
                $formattedDateTime = date('Y-m-d H:i:s');

                // Insert activity log into activity_history table
                $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                $stmtLog = $conn->prepare($sqlLog);
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

        $response['success'] = "New Title created successfully";
    } else {
        $response['error'] = "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close prepared statement
    $stmt->close();

    // Return JSON response
    echo json_encode($response);
}

// Function to handle file upload
function handleUpload($inputName) {
    $targetDir = "uploads/";
    $fileName = basename($_FILES[$inputName]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Allow certain file formats
    $allowTypes = array('jpg', 'png', 'jpeg');
    if (in_array($fileType, $allowTypes)) {
        // Upload file to server
        if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $targetFilePath)) {
            return $fileName;
        } else {
            $response['error'] = "Sorry, there was an error uploading your file.";
            echo json_encode($response);
            exit(); // Stop script execution
        }
    } else {
        $response['error'] = 'Sorry, only JPG, JPEG, PNG files are allowed to upload.';
        echo json_encode($response);
        exit(); // Stop script execution
    }
}
?>
