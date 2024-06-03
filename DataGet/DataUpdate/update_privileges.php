<?php

session_start();

// Assuming $_POST['updatedData'] contains the updated privileges data sent from the client-side
$userid = $_POST['userid'];
$updatedData = $_POST['updatedData'];

// Perform database connection
require('../../config/db_con.php');

// Update privileges in the database
foreach ($updatedData as $permission => $value) {
    mysqli_query($conn, "UPDATE privileges SET $permission = $value WHERE UserID = $userid");
}

// Close database connection
mysqli_close($conn);

// Send response
echo "success"; // Send a success message back to the AJAX request

?>
