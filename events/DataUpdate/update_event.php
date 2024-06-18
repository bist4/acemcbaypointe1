<?php
require ('../../config/db_con.php');
session_start();

$response = array(); // Initialize response array

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if file is uploaded
    if (isset($_FILES['image1']) && !empty($_FILES['image1']['name'])) {
        $image1 = handleUpload('image1');
    } else {
        $response['error'] = 'Please select an image to upload.';
        echo json_encode($response);
        exit(); // Stop script execution
    }

    $eventID = mysqli_real_escape_string($conn, $_POST['eventID']);
    $eventTitle = mysqli_real_escape_string($conn, $_POST['eventTitle1']);
    $eventDesc = mysqli_real_escape_string($conn, $_POST['eventDesc1']);
    $active = 1;
    $userAuthor = $_SESSION['Username'];
    $userRoleID = $_SESSION['UserRoleName'];
    $departmentName = $_SESSION['DepartmentName'];

    $sql = "SELECT * FROM users WHERE Username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userAuthor);
    $stmt->execute();
    $result = $stmt->get_result();

    $status = "";
    $user = $_SESSION['Username'];

    $loggedInUsername = $_SESSION['Username'];
    $sqlUserCheck = "SELECT Fname, Lname FROM users WHERE Username=?";
    $stmtUserCheck = $conn->prepare($sqlUserCheck);
    $stmtUserCheck->bind_param("s", $loggedInUsername);
    $stmtUserCheck->execute();
    $resultUserCheck = $stmtUserCheck->get_result();
    $row = $resultUserCheck->fetch_assoc();
    $user = $row['Fname'] . ' ' . $row['Lname'];

    $decisionStatus = "";

    $canEdit = 0;
    if($userRoleID == 0){
        $status = "APPROVED";
        $decisionStatus = "Approved by " . $user;
    } else {
        $status = "PENDING";
    }

    $sql1 = "SELECT DepartmentName FROM baypointedepartments WHERE BaypointeDepartmentID = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("i", $departmentName);
    $stmt1->execute();
    $resultDep = $stmt1->get_result();

    if ($result->num_rows > 0 && $resultDep->num_rows > 0) {
        $user = $result->fetch_assoc();
        $department = $resultDep->fetch_assoc();
        $fname = $user['Fname'];
        $depName = $department['DepartmentName'];
        $aut = $fname . "\n(" . $depName . ")";

        date_default_timezone_set('Asia/Manila');
    
        $DateTime = date('Y-m-d H:i:s');
        // Use prepared statement to prevent SQL injection
        $sql = "UPDATE events SET EventTitle=?, Description=?, Image1=?, Author=?, Status=?, Decision_Status=?, Active=?, Date=?, can_edit =? WHERE EventID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssisii", $eventTitle, $eventDesc, $image1, $aut, $status, $decisionStatus, $active, $DateTime, $canEdit, $eventID);
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
    
                    $action = 'UPDATE';
                    $activity = 'Updated event: ' . $eventTitle;
    
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
    
            $response['success'] = "Event updated successfully";
        } else {
            $response['error'] = "Error: " . $sql . "<br>" . $conn->error;
        }
    
        // Close prepared statement
        $stmt->close();
    }
    // Return JSON response
    echo json_encode($response);
}

// Function to handle file upload
function handleUpload($inputName)
{
    $targetDir = "../DataAdd/uploads/";
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
