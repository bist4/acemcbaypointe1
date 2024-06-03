<?php
session_start();
include('config/db_con.php');

if (isset($_SESSION['UserID'])) {
    

    if($row['UserRoleID'] == 0){
        // Fetch user details from the database
        $user_id = $_SESSION['UserID'];
        $select_sql = "SELECT Fname FROM users WHERE UserID = ?";
        $stmt = mysqli_prepare($conn, $select_sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $fname);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Update the login status to '0' (offline) in the database using prepared statement
        $update_sql = "UPDATE users SET is_Login = 0 WHERE UserID = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Log the login activity including the user's first name
        $logActivity = "INSERT INTO superadmin_logs (DateTime, Activity, UserID, Active, Action) 
            VALUES (NOW(), CONCAT('Logout as Super Admin- ', ?), ?, 1, 'LOGOUT')";
        $stmt = mysqli_prepare($conn, $logActivity);
        mysqli_stmt_bind_param($stmt, "si", $fname, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    if($row['UserRoleID'] == 1){
        // Fetch user details from the database
        $user_id = $_SESSION['UserID'];
        $select_sql = "SELECT Fname FROM users WHERE UserID = ?";
        $stmt = mysqli_prepare($conn, $select_sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $fname);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Update the login status to '0' (offline) in the database using prepared statement
        $update_sql = "UPDATE users SET is_Login = 0 WHERE UserID = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Log the login activity including the user's first name
        $logActivity = "INSERT INTO admin_logs (DateTime, Activity, UserID, Active) 
            VALUES (NOW(), CONCAT('Logout as Admin- ', ?), ?, 1)";
        $stmt = mysqli_prepare($conn, $logActivity);
        mysqli_stmt_bind_param($stmt, "si", $fname, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Destroy the session and redirect to login page
session_destroy();
header("Location: index.php");
exit();
?>
