<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require ('../message1.php');

require '../../phpmailer/src/Exception.php';
require '../../phpmailer/src/PHPMailer.php';
require '../../phpmailer/src/SMTP.php';


// Include the database connection file
require ('../../config/db_con.php');
session_start();
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $userID = $_POST["UserID"];
    $IdNumber = $_POST['IdNumber'];
    $fname = $_POST['firstName'];
    $lname = $_POST['lastName'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $address = $_POST['houseNumber'] . ' ' . $_POST['streetName'] . ', ' . $_POST['barangay'] . ', ' . $_POST['city'] . ', ' . $_POST['province'];
    $contact = $_POST['contactNum'];
    $email = $_POST['email'];
    $username = $_POST['username'];

    // Determine user groups
    $isAdmin = isset($_POST['group']) && $_POST['group'] === 'Admin' ? 1 : 0;
    $isAncillary = isset($_POST['group']) && $_POST['group'] === 'Ancillary' ? 1 : 0;
    $isNursing = isset($_POST['group']) && $_POST['group'] === 'Nursing' ? 1 : 0;
    $isOutsource = isset($_POST['group']) && $_POST['group'] === 'Outsource' ? 1 : 0;
    $isExecom = isset($_POST['group']) && $_POST['group'] === 'EXECOM' ? 1 : 0;

    $department = $_POST['BaypointeDepartmentID'];
    $role = $_POST['role'];

    // Calculate user's age
    $birthDate = new DateTime($birthday);
    $currentDate = new DateTime();
    $age = $currentDate->diff($birthDate)->y;

    // Set default values
    $active = 1;
    $is_login = 0;
    $is_Lock = 0;


    // Query to fetch existing email
    $getEmailQuery = "SELECT Email FROM users WHERE UserID=?";
    $getEmailStmt = $conn->prepare($getEmailQuery);
    $getEmailStmt->bind_param("i", $userID);
    $getEmailStmt->execute();
    $getEmailStmt->bind_result($existingEmail);
    $getEmailStmt->fetch();
    $getEmailStmt->close();

 

    
    // Update query
    $query = "UPDATE users SET IdNumber=?, Fname=?, Lname=?, Gender=?, Birthday=?, Age=?, Address=?, ContactNumber=?, 
    Email=?, Username=?, is_Admin_Group=?, 
    is_Ancillary_Group=?, is_Nursing_Group=?, is_Outsource_Group=?, is_EXECOM_Group=?, BaypointeDepartmentID=?, UserRoleID=?, Active = ?, is_Login = ?, is_Lock =? WHERE UserID=?";

    // Prepare statement
    $stmt = $conn->prepare($query);

    // Bind parameters
    $stmt->bind_param(
        "issssissssiiiiiiiiiii",
        $IdNumber,
        $fname,
        $lname,
        $gender,
        $birthday,
        $age,
        $address,
        $contact,
        $email,
        $username,
        $isAdmin,
        $isAncillary,
        $isNursing,
        $isOutsource,
        $isExecom,
        $department,
        $role,
        $active,
        $is_login,
        $is_Lock,
        $userID
    );



    // Execute statement
    if ($stmt->execute()) {

        if ($email !== $existingEmail) {
            // Email changed, send notification
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'saasisubicinc@gmail.com';
            $mail->Password = 'fxytjsahrwtyhdhb';

            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->setFrom('saasisubicinc@gmail.com');

            try {
                $mail->addAddress($_POST["email"]);
                $mail->isHTML(true);
                $mail->Subject = "User updated";
                $message = "
                <!DOCTYPE html>
                <html lang=\"en\">
                
                <head>
                    <meta charset=\"UTF-8\">
                    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                    <title>Document</title>
                
                    <style>
                        body{
                            display: flex;
                            justify-content: center;
                        }
                        .main {
                            border: none;
                            border-radius: 5px;
                            box-shadow: 0px 0 30px rgba(1, 41, 112, 0.1);
                            width: 30rem;
                        }
                
                        .main1 {
                            padding: 50px;
                        }
                
                        .main1 h1,
                        .main h2,
                        .main1 h3,
                        .main1 p {
                
                            font-family: \"Nunito\", sans-serif;
                            color: #899bbd;
                            font-weight: 600;
                        }
                
                        .main1 h3,
                        .main1 p {
                            font-size: 13px;
                        }
                
                        .info {
                            line-height: 5px;
                        }
                
                        .content .par p {
                            line-height: 20px;
                        }
                
                        .content .title h1 {
                            font-size: 24px;
                            margin-bottom: 0;
                            font-weight: 600;
                            color: #012970;
                        }
                
                        .main1 button {
                            margin-top: 30px;
                            color: #fff;
                            background-color: #0d6efd;
                            border-color: #0d6efd;
                            border-radius: 0.375rem;
                            box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
                            cursor: pointer;
                            /* cursor: wait; */
                            padding: 0.375rem 0.75rem;
                            line-height: 1.5;
                            font-weight: 400;
                        }
                
                
                
                
                        .main1 button:hover {
                            color: #fff;
                            background-color: #0b5ed7;
                            border-color: #0a58ca;
                        }
                    </style>
                </head>
                
                <body>
                    <div class=\"main\">
                        <div class=\"main1\" align=\"center\">
                            <div class=\"img\">
                                <img src=\"../assets/img/logo1.png\" width=\"100\" height=\"50\" alt=\"\">
                            </div>
                
                            <div class=\"content\">
                                <div class=\"title\" align=\"center\">
                                    <h1>Hi " . $_POST['firstName'] . " " . $_POST['lastName'] . "</h1>
                                </div>
                                <div class=\"par\">
                                    <p>Your email was changed and heres your account information.</p>
                                    <ul>
                                        <li>Name : " . $_POST['firstName'] . "  " . $_POST['lastName'] . "</li>
                                        <li>Username : " . $_POST['username'] . "</li>
                                       
        
                                    </ul>
                                </div>
                            </div>
                
                            <div style=\"width:100%;height:5px;display:block\" align=\"center\">
                                <div style=\"width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0\">
                                </div>
                            </div>
                
                            <div class=\"info\">
                                
                            </div>
                
                            <div class=\"butt\">
                                <button> <a href='http://localhost/acemcbaypointe/'>Login to your account</a></button>
                            </div>
                        </div>
                    </div>
                </body>
                
                </html>
                ";


                $mail->Body = $message;

                // Check if email sending is successful
                if ($mail->send()) {

                } else {
                    $response['error'] = "Email sending failed: " . $mail->ErrorInfo;
                }
            } catch (Exception $e) {
                $response['error'] = "Error sending email: " . $e->getMessage();
                echo json_encode($response);
                exit(); // Stop script execution
            }
        } else {
            echo "User information updated successfully.";
        }

        // Log activity
        if (isset($_SESSION['Username'])) {
            $loggedInUsername = $_SESSION['Username'];

            $sqlUserCheck = "SELECT * FROM users WHERE Username=?";
            $stmtUserCheck = $conn->prepare($sqlUserCheck);
            $stmtUserCheck->bind_param("s", $loggedInUsername);
            $stmtUserCheck->execute();
            $resultUserCheck = $stmtUserCheck->get_result();

            if ($resultUserCheck && $resultUserCheck->num_rows > 0) {
                $row = $resultUserCheck->fetch_assoc();
                $UserID = $row['UserID'];

                $action = 'UPDATE';
                $activity = 'Update user account';
                $currentDateTime = date('Y-m-d H:i:s');
                $active = 1;

                $sqlLog = "INSERT INTO activity_history (Action,Activity,DateTime,UserID,Active) VALUES (?, ?, ?, ?, ?)";
                $stmtLog = $conn->prepare($sqlLog);
                $stmtLog->bind_param("sssii", $action, $activity, $currentDateTime, $UserID, $active);
                $resultLog = $stmtLog->execute();

            }

        }
    } else {
        echo "Error updating user information: " . $stmt->error;
        echo "<script>console.log('" . $stmt->error . "');</script>";

    }

    // Close statement
    $stmt->close();
}
?>