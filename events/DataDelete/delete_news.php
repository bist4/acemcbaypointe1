<?php
session_start();
require ('../../config/db_con.php');
require '../../phpmailer/src/Exception.php';
require '../../phpmailer/src/PHPMailer.php';
require '../../phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if newsID is set and is a valid integer
    if (isset($_POST['newsID']) && is_numeric($_POST['newsID'])) {
        $newsID = $_POST['newsID'];
        $message = $_POST['message'];
        $active = 0;

        // Prepare and bind parameters for the SQL query
        $sql = "UPDATE news SET Active = ? WHERE NewsID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $active, $newsID);

        if ($stmt->execute()) {
            // Log activity if the user is logged in
            if (isset($_SESSION['Username'])) {
                $loggedInUsername = $_SESSION['Username'];
                $sqlUserCheck = "SELECT UserID FROM users WHERE Username=?";
                $stmtUserCheck = $conn->prepare($sqlUserCheck);
                $stmtUserCheck->bind_param("s", $loggedInUsername);
                $stmtUserCheck->execute();
                $resultUserCheck = $stmtUserCheck->get_result();

                if ($resultUserCheck && $resultUserCheck->num_rows > 0) {
                    $row = $resultUserCheck->fetch_assoc();
                    $loggedInUserID = $row['UserID'];

                    // Insert activity log into activity_history table
                    $action = 'DELETE';
                    $activity = 'Delete News';
                    date_default_timezone_set('Asia/Manila');
                    $formattedDateTime = date('Y-m-d H:i:s');
                    $active = 1; // Assuming 'Active' field is boolean

                    $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                    $stmtLog = $conn->prepare($sqlLog);
                    $stmtLog->bind_param("sssii", $action, $activity, $formattedDateTime, $loggedInUserID, $active);
                    $stmtLog->execute();
                } else {
                    $response['error'] = "User not found in the database!";
                }
            }

            // Fetch all user emails
            $sqlFetchEmails = "SELECT Email FROM users";
            $resultEmails = $conn->query($sqlFetchEmails);

            if ($resultEmails && $resultEmails->num_rows > 0) {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'saasisubicinc@gmail.com';
                $mail->Password = 'fxytjsahrwtyhdhb';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;
                $mail->setFrom('saasisubicinc@gmail.com');
                $mail->isHTML(true);
                $mail->Subject = "Alert: News Deletion Notice"; // Set your email subject

                while ($row = $resultEmails->fetch_assoc()) {
                    $mail->addAddress($row['Email']);
                    $mail->Body = '<!DOCTYPE html>
                <html lang="en">
                
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Event Deletion Notice</title>
                    <!-- Bootstrap CSS -->
                    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
                
                </head>
                
                <body>
                    <div class="">
                        <div class="aHl"></div>
                        <div id=":s1" tabindex="-1"></div>
                        <div id=":sb" class="ii gt"
                            jslog="20277; u014N:xr6bB; 1:WyIjdGhyZWFkLWY6MTgwMTQzOTQxNjM4MDAyODI0NyJd; 4:WyIjbXNnLWY6MTgwMTQzOTQxNjM4MDAyODI0NyJd">
                            <div id=":sc" class="a3s aiL msg7318187275459045608"><u></u>
                                <div style="margin:0;padding:0" bgcolor="#FFFFFF">
                                    <table width="100%" height="100%" style="min-width:348px" border="0" cellspacing="0" cellpadding="0"
                                        lang="en">
                                        <tbody>
                                            <tr height="32" style="height:32px">
                                                <td></td>
                                            </tr>
                                            <tr align="center">
                                                <td>
                                                    <div>
                                                        <div></div>
                                                    </div>
                                                    <table border="0" cellspacing="0" cellpadding="0"
                                                        style="padding-bottom:20px;max-width:516px;min-width:220px">
                                                        <tbody>
                                                            <tr>
                                                                <td width="8" style="width:8px"></td>
                                                                <td>
                                                                    <div style="border-style:solid;border-width:thin;border-color:#dadce0;border-radius:8px;padding:40px 20px"
                                                                        align="center" class="m_7318187275459045608mdv2rw">
                                                                        <img src="assets/img/logo1.png" width="74" height="24"
                                                                            aria-hidden="true" style="margin-bottom:16px" alt="logo"
                                                                            class="CToWUd" data-bit="iit">
                                                                        <div
                                                                            style="font-family:\'Google Sans\',Roboto,RobotoDraft,Helvetica,Arial,sans-serif;border-bottom:thin solid #dadce0;color:rgba(0,0,0,0.87);line-height:32px;padding-bottom:24px;text-align:center;word-break:break-word">
                                                                            <div style="font-size:24px">News Deleted
                                                                            </div>
                                                                            <table align="center" style="margin-top:8px">
                                                                                <tbody>
                                                                                    <tr style="line-height:normal">
                                                                                        <td align="right" style="padding-right:8px"> 
                                                                                        </td>
                                                                                        <td><a
                                                                                                style="font-family:\'Google Sans\',Roboto,RobotoDraft,Helvetica,Arial,sans-serif;color:rgba(0,0,0,0.87);font-size:14px;line-height:20px">' . $row['Email'] . '</a>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        <div
                                                                            style="font-family:Roboto-Regular,Helvetica,Arial,sans-serif;font-size:14px;color:rgba(0,0,0,0.87);line-height:20px;padding-top:20px;text-align:left">
                                                                            <h3>Dear users,</h3>
                                                                            Please be informed that the news has been deleted.
                                                                            We apologize for any inconvenience caused. If you have any
                                                                            questions, please contact our support team.
                                                                        </div>
                                                                        <div
                                                                        style="font-family:Roboto-Regular,Helvetica,Arial,sans-serif;font-size:14px;color:rgba(0,0,0,0.87);line-height:20px;padding-top:20px;text-align:left">
                                                                        <h3>Alternative Explanations</h3>
                                                                         '.$_POST['message'].'
                                                                    </div>
                                                                    </div>
                                                                    <div style="text-align:left">
                                                                        <div
                                                                            style="font-family:Roboto-Regular,Helvetica,Arial,sans-serif;color:rgba(0,0,0,0.54);font-size:11px;line-height:18px;padding-top:12px;text-align:center">
                                                                            <div> You received this alert to inform you about the
                                                                                deletion of news.
                                                                                Please note that these changes are important. If you
                                                                                have any questions, please contact our support team.
                                                                            </div>
                                                                            <div style="direction:ltr"></div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td width="8" style="width:8px"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr height="32" style="height:32px">
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="yj6qo"></div>
                            <div class="yj6qo"></div>
                            <div class="yj6qo"></div>
                            <div class="yj6qo"></div>
                        </div>
                        <div class="WhmR8e" data-hash="0"></div>
                    </div>
                    <!-- Bootstrap Bundle with Popper -->
                    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
                </body>
                
                </html>'; // Set your HTML email content

                    // Send email
                    if ($mail->send()) {
                        $response['success'][] = "Email sent to: " . $row['Email'];
                    } else {
                        $response['error'][] = "Failed to send email to: " . $row['Email'] . ". Error: " . $mail->ErrorInfo;
                    }
                    // Clear recipients for next iteration
                    $mail->clearAddresses();
                }
            } else {
                $response['error'] = "No users found in the database!";
            }

            // If the query is successful, return success message
            $response['success'] = "Delete Successfully!";
        } else {
            // If the query fails, return error message
            $response['error'] = "Failed to update news active!";
        }
    } else {
        $response['error'] = "Invalid news ID!";
    }
} else {
    $response['error'] = "Invalid request method!";
}

// Return response in JSON format
echo json_encode($response);
?>