<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

session_start(); // Start the session

if (isset($_POST["send"])) {
    $email = $_POST["email"];

    // Include the database connection file
    include('config/db_con.php');

    try {
        // Create a new PDO instance using the database connection details
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username,  $password);
    
        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if the email exists in the 'users' table
        $stmt = $pdo->prepare("SELECT * FROM users WHERE Email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Email exists in the database, proceed to send the email

            // Start a session with the user's email
            $_SESSION['user_email'] = $email;

            // Configure and send the email
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'saasisubicinc@gmail.com';
            $mail->Password = 'fxytjsahrwtyhdhb';

            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->setFrom('saasisubicinc@gmail.com');

            $mail->addAddress($email);
            $mail->isHTML(true);

            $mail->Subject = "ACE Baypointe: Change Password";
            $message = "Hi " . $email . "<br><br>"
                . "To start using 'ACE Baypointe', change your password at the following link:<br><br>"
                . "localhost/acemcbaypointe/new_password.php";

            $mail->Body = $message;

            // Send the email
            if ($mail->send()) {
                // Email sent successfully, redirect to the 'forgot_password.php' page
                header("location: https://mail.google.com/mail");
            } else {
                // Email sending failed; handle the error (you can customize this part)
                echo 'Email could not be sent.';
                
            }
        } else {
            // Email does not exist in the database, display an error message
            header("Location: ../search_email.php?error=EmailNotRegistered");
            exit();
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>

<?php

?>