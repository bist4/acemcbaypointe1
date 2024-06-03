<?php
require ('../config/db_con.php');
session_start();

// Check if the connection is successful
if ($conn->connect_error) {
    // Redirect to die_con.php if connection fails
    header("Location: ../config/die_con.php");
    exit(); // Ensure that script execution stops after redirection
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize input data
    $departmentTitle = mysqli_real_escape_string($conn, $_POST['departmentTitle']);

    // Check if front image is uploaded
    if (isset($_FILES['frontImage']) && $_FILES['frontImage']['error'] === UPLOAD_ERR_OK) {
        $frontImageName = $_FILES['frontImage']['name'];
        $frontImageTmp = $_FILES['frontImage']['tmp_name'];
        // Move the uploaded image to a desired location
        if (move_uploaded_file($frontImageTmp, 'uploads/' . $frontImageName)) {
            // Image moved successfully
        } else {
            // Handle move operation failure
            // You might want to log this failure or handle it accordingly
        }
    } else {
        // Front image not uploaded, set default or handle accordingly
        $frontImageName = ''; // Set default value or handle accordingly
    }

    // Check if back image is uploaded
    if (isset($_FILES['backImage']) && $_FILES['backImage']['error'] === UPLOAD_ERR_OK) {
        $backImageName = $_FILES['backImage']['name'];
        $backImageTmp = $_FILES['backImage']['tmp_name'];
        // Move the uploaded image to a desired location
        if (move_uploaded_file($backImageTmp, 'uploads/' . $backImageName)) {
            // Image moved successfully
        } else {
            // Handle move operation failure
            // You might want to log this failure or handle it accordingly
        }
    } else {
        // Back image not uploaded, set default or handle accordingly
        $backImageName = ''; // Set default value or handle accordingly
    }

    // Retrieve other form data
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $services = mysqli_real_escape_string($conn, $_POST['services']);
    $doctorID = mysqli_real_escape_string($conn, $_POST['doctorID']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $active = 1;

    $userAuthor = $_SESSION['Username'];
    // Check if the department title already exists
    $sqlCheck = "SELECT * FROM departments WHERE Title = ? AND Doctors = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("ss", $departmentTitle, $doctorID);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // Department with the same title and doctor already exists
        $response['error'] = "A department with the same title and doctor already exists";
    } else {

        $sql = "SELECT * FROM users WHERE Username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $userAuthor);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User found, fetch user details
            $user = $result->fetch_assoc();
            $fname = $user['Fname'];
            $sql = "INSERT INTO departments (Title, Description, Services, Doctors, Email,  FrontImage, BackImage, UserID, Active, CreatedAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssissssi", $departmentTitle, $description, $services, $doctorID, $email, $frontImageName, $backImageName, $fname, $active); // "si" indicates string and integer parameters
            if ($stmt->execute()) {
                // Log activity if the user is logged in
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

                        $sqlDoctor = "SELECT Name FROM doctors WHERE DoctorID = ?";
                        $stmtDoctor = $conn->prepare($sqlDoctor);
                        $stmtDoctor->bind_param("i", $doctorID);
                        $stmtDoctor->execute();
                        $resultDoctor = $stmtDoctor->get_result();

                        // Initialize doctor's name variable
                        $doctorName = "";
                        if ($resultDoctor->num_rows > 0) {
                            $doctorData = $resultDoctor->fetch_assoc();
                            $doctorName = $doctorData['Name'];
                        } else {
                            // Handle case where doctor is not found
                            // You might want to log this or handle it differently based on your requirements
                            $doctorName = "Unknown Doctor";
                        }

                        $action = 'ADD';
                        $activity = 'Add new department: ' . $departmentTitle . '<br>' .'Doctor: '. $doctorName;

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

                $response['success'] = "New department created successfully";

            } else {
                $response['error'] = "Error: " . $stmt->error;
            }

        } else {
            // User not found in the database, handle this scenario accordingly
        }

        // Close statement
        $stmt->close();
    }

    // Return JSON response
    echo json_encode($response);

    // Close connection
    $conn->close();
}
?>