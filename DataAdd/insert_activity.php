<?php

require ('../config/db_con.php');
session_start();

// Set the time zone to ASIA/MANILA
date_default_timezone_set('Asia/Manila');

// Decode JSON data received from the AJAX request
$data = json_decode(file_get_contents("php://input"));

if ($data && isset($data->reason)) {
    $reason = $data->reason;
    $userID = $_SESSION['UserID'];
    $status = 0; // Assuming default status is 0

    // Using prepared statement to prevent SQL injection
    $sql = "INSERT INTO maintenance (Activity, Status, StartDateTime, UserID) VALUES (?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $reason, $status, $userID);

    if ($stmt->execute()) {
        header("Location: ../maintenance.php");
    } else {
        // Insertion failed
        // You can echo an error message if needed
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
} else {
    // Data not received or reason not set
    echo "Error: Data not received or reason not set";
}


 
?>