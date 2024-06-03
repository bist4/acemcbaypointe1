<?php
session_start();

// Check if the user is not logged in (Username not in session)
if(!$_SESSION['Username']){
    header("Location: index.php");
}

?>