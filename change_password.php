<?php
session_start();
require('config/db_con.php');

// Get the user's email from the session
$userEmail = $_SESSION['user_email'];

if (isset($_POST['password']) && isset($_POST['confirmPassword'])) {
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($newPassword === $confirmPassword) {
        // Passwords match, proceed with the update

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Prepare and execute the SQL update statement
        $updateQuery = "UPDATE users SET Password = ? WHERE Email = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ss", $hashedPassword, $userEmail);

        if ($stmt->execute()) {
            // Password updated successfully
            session_destroy();
            header('Location: index.php'); // Redirect to a success page
            exit;
        } else {
            // Password update failed
            header('Location: error.php'); // Redirect to an error page
            exit;
        }

        $stmt->close();
    } else {
        // Passwords do not match
        header('Location: password_mismatch.php'); // Redirect to a page indicating password mismatch
        exit;
    }
} else {
    // Passwords not provided
    header('Location: error.php'); // Redirect to an error page
    exit;
}
?>