<?php
require('../config/db_con.php');
session_start();

$response = array(); // Initialize response array

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if file is uploaded
    if ((isset($_FILES['Image']) && !empty($_FILES['Image']['name']))) {
        $Image = handleUpload('Image');
    } else {
        $response['error'] = 'Please select front and back images to upload.';
        echo json_encode($response);
        exit(); // Stop script execution
    }

    // Collect form data
    $name = $_POST['name']; // corrected variable name
    $schedule = $_POST['schedule'];
    $department = $_POST['department'];

     
  

    $active = 1;

    // Insert data into departments table
    $sql = "INSERT INTO doctors (Name, Department, Image, Schedule, Active, CreatedAt)
            VALUES ('$name','$department','$Image', '$schedule', $active, NOW())";

    if ($conn->query($sql) === TRUE) {
        $response['success'] = "New Doctors created successfully";
    } else {
        $response['error'] = "Error: " . $sql . "<br>" . $conn->error;
    }

    // Return JSON response
    echo json_encode($response);
}

// Function to handle file upload
function handleUpload($inputName) {
    $targetDir = "uploads/doctors";
    $fileName = basename($_FILES[$inputName]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Allow certain file formats
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
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
        $response['error'] = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.';
        echo json_encode($response);
        exit(); // Stop script execution
    }
}
?>
