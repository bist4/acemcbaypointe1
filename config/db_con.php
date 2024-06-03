<?php

$servername= "localhost";
$username= "root";
$password = "";

$dbname = "acemcb";


$conn = mysqli_connect($servername, $username, $password, $dbname);


// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

if ($conn->connect_error) {
    // Redirect to die_con.php if connection fails
    header("Location: die_con.php");
    exit(); // Ensure that script execution stops after redirection
}

?>