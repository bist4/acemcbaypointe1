<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// require ('../message1.php');

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

require ('../config/db_con.php');
session_start();

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addBtn'])) {

    if (!isset($_FILES['profile']) || empty($_FILES['profile']['name'])) {
        $response['error'] = 'Please select a profile photo to upload.';
        echo json_encode($response);
        exit(); // Stop script execution
    }

    $profile = handleUpload('profile', $response);
    if ($profile === false) {
        exit(); // Stop script execution if upload fails
    }

    $IdNumber = $_POST['IdNumber'];
    $fname = $_POST['firstName'];
    $lname = $_POST['lastName'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $address = $_POST['houseNumber'] . ' ' . $_POST['streetName'] . ', ' . $_POST['barangay'] . ', ' . $_POST['city'] . ', ' . $_POST['province'];
    $contact = $_POST['contactNum'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $isAdmin = isset($_POST['group']) && $_POST['group'] === 'Admin' ? 1 : 0;
    $isAncillary = isset($_POST['group']) && $_POST['group'] === 'Ancillary' ? 1 : 0;
    $isNursing = isset($_POST['group']) && $_POST['group'] === 'Nursing' ? 1 : 0;
    $isOutsource = isset($_POST['group']) && $_POST['group'] === 'Outsource' ? 1 : 0;
    $isExecom = isset($_POST['group']) && $_POST['group'] === 'EXECOM' ? 1 : 0;


    $department = $_POST['BaypointeDepartmentID'];
    $role = $_POST['role'];

    $birthDate = new DateTime($birthday);
    $currentDate = new DateTime();
    $age = $currentDate->diff($birthDate)->y;

    $hashedPassword = password_hash($password, PASSWORD_ARGON2I);
    $active = 1;
    $is_login = 0;
    $is_Lock = 0;


    //Check existing email
    $sqlEmailCheck = "SELECT Email FROM users WHERE Email = ?";
    $stmtCheckEmail = $conn->prepare($sqlEmailCheck);
    $stmtCheckEmail->bind_param("s", $email);
    $stmtCheckEmail->execute();
    $resultEmail = $stmtCheckEmail->get_result();

    if ($resultEmail->num_rows > 0) {
        $response['error'] = "Email is not available";
        echo json_encode($response);
        exit(); // Stop script execution
    } else {


        // Check if email field is provided and not empty
        if (!isset($_POST["email"]) || empty($_POST["email"])) {
            $response['error'] = "Email field is empty or not provided";
            echo json_encode($response);
            exit(); // Stop script execution
        }



        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'saasisubicinc@gmail.com';
        $mail->Password = 'fxytjsahrwtyhdhb';

        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom('saasisubicinc@gmail.com');

        // Add try-catch block around sending email
        try {
            $mail->addAddress($_POST["email"]);
            $mail->isHTML(true);
            $mail->Subject = "New user account";
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
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                                    labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
                                    laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in
                                    voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat
                                    non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                            </div>
                        </div>
            
                        <div style=\"width:100%;height:5px;display:block\" align=\"center\">
                            <div style=\"width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0\">
                            </div>
                        </div>
            
                        <div class=\"info\">
                            <div class=\"user\">
                                <h3>Username</h3>
                                <h2>" . $_POST['username'] . "</h2>
                            </div>
                            <div class=\"pass\">
                                <h3>Password</h3>
                                <h2>" . $_POST['password'] . "</h2>
                            </div>
                        </div>
            
                        <div class=\"butt\">
                            <button> <a href='https://localhost/acemcbaypointe'>Login to your account</a></button>
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







        $sqlUsernameCheck = "SELECT Username FROM users WHERE Username = ?";
        $stmtCheckUsername = $conn->prepare($sqlUsernameCheck);
        $stmtCheckUsername->bind_param("s", $username);
        $stmtCheckUsername->execute();
        $resultUsername = $stmtCheckUsername->get_result();

        if ($resultUsername->num_rows > 0) {
            $response['error'] = "Username is not available";
            echo json_encode($response);
            exit(); // Stop script execution
        } else {
            $sqlInsertUser = "INSERT INTO users (IdNumber, Fname, Lname, ProfilePhoto, Gender, Birthday, Age, Address, ContactNumber, Email, Username, Password, is_Admin_Group, is_Ancillary_Group, is_Nursing_Group, is_Outsource_Group, is_EXECOM_Group, BaypointeDepartmentID, UserRoleID, is_Login, Active, CreatedAt, is_Lock) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
            $stmtInsertUser = $conn->prepare($sqlInsertUser);
            $stmtInsertUser->bind_param("ssssssssssssssssssssii", $IdNumber, $fname, $lname, $profile, $gender, $birthday, $age, $address, $contact, $email, $username, $hashedPassword, $isAdmin, $isAncillary, $isNursing, $isOutsource, $isExecom, $department, $role, $is_login, $active, $is_Lock);

            if (!$stmtInsertUser->execute()) {
                $response['error'] = "Error: " . $sqlInsertUser . "<br>" . $conn->error;
                echo json_encode($response);
                exit(); // Stop script execution
            }

            $userID = $conn->insert_id;

            if ($role == 0) {
                $sqlInsertPrivileges = "INSERT INTO privileges (UserID, ModuleID, AssignModule_View, AssignModule_Update, Action_Add, Action_Update, Action_Delete, Action_View, Action_Reply, Action_Lock, Action_Unlock, Action_Hide, Action_Show, Action_Reject, Action_Decline, Action_Pending, Action_Review, Action_Request, Hide_Module) 
                VALUES (?, ?, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1)";
                $stmtInsertPrivileges = $conn->prepare($sqlInsertPrivileges);

                if (!$stmtInsertPrivileges) {
                    $response['error'] = "Error preparing privileges statement: " . $conn->error;
                    echo json_encode($response);
                    exit(); // Stop script execution
                }

                for ($moduleId = 1; $moduleId <= 19; $moduleId++) {
                    if (!$stmtInsertPrivileges->bind_param("ii", $userID, $moduleId) || !$stmtInsertPrivileges->execute()) {
                        $response['error'] = "Error inserting privileges: " . $conn->error;
                        $stmtInsertUser->close();
                        echo json_encode($response);
                        exit(); // Stop script execution
                    }
                }

                $stmtInsertUser->close();
                $stmtInsertPrivileges->close();

                $response['success'] = "New user created successfully";
                echo json_encode($response);
            }
            // For Admin Privileges
            else if ($role == 1) {
                // Insert privileges based on department
                switch ($department) {
                    case 19: // Sales and Marketing
                        insertSalesAndMarketingPrivileges($userID, $conn);
                        $stmtInsertUser->close();
                        break;
                    case 9: // HRDM
                        insertHRDMPrivileges($userID, $conn);
                        $stmtInsertUser->close();
                        break;
                    case 43: // OPD
                        insertOPDPrivileges($userID, $conn);
                        $stmtInsertUser->close();
                        break;
                    default:
                        // Department not recognized
                        $response['error'] = "Department not recognized";
                        echo json_encode($response);
                        exit(); // Stop script execution
                }
            } else if ($role == 2) {
                $moduleId = 14;

                $sqlUserPriv = "INSERT INTO privileges (UserID, ModuleID, AssignModule_View, AssignModule_Update, Action_Add, Action_Update, Action_Delete, Action_View, Action_Reply, Action_Lock, Action_Unlock, Action_Hide, Action_Show, Action_Reject, Action_Decline, Action_Pending, Action_Review, Action_Request, Hide_Module) 
                VALUES (?, ?, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1)";

                $stmtUserInsertPrivileges = $conn->prepare($sqlUserPriv);

                if (!$stmtUserInsertPrivileges) {
                    $response['error'] = "Error preparing privileges statement: " . $conn->error;
                    echo json_encode($response);
                    exit(); // Stop script execution
                }

                if (!$stmtUserInsertPrivileges->bind_param("ii", $userID, $moduleId) || !$stmtUserInsertPrivileges->execute()) {
                    $response['error'] = "Error inserting privileges: " . $conn->error;
                    $stmtInsertUser->close();
                    echo json_encode($response);
                    exit(); // Stop script execution
                }

                $stmtInsertUser->close();
                $stmtUserInsertPrivileges->close(); // Corrected closing the prepared statement

                $response['success'] = "New user created successfully";
                echo json_encode($response);
            } else if ($role == 3) {
                $moduleId = 15;

                $sqlUserPriv = "INSERT INTO privileges (UserID, ModuleID, AssignModule_View, AssignModule_Update, Action_Add, Action_Update, Action_Delete, Action_View, Action_Reply, Action_Lock, Action_Unlock, Action_Hide, Action_Show, Action_Reject, Action_Decline, Action_Pending, Action_Review, Action_Request, Hide_Module) 
                VALUES (?, ?, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 0, 1)";

                $stmtUserInsertPrivileges = $conn->prepare($sqlUserPriv);

                if (!$stmtUserInsertPrivileges) {
                    $response['error'] = "Error preparing privileges statement: " . $conn->error;
                    echo json_encode($response);
                    exit(); // Stop script execution
                }

                if (!$stmtUserInsertPrivileges->bind_param("ii", $userID, $moduleId) || !$stmtUserInsertPrivileges->execute()) {
                    $response['error'] = "Error inserting privileges: " . $conn->error;
                    $stmtInsertUser->close();
                    echo json_encode($response);
                    exit(); // Stop script execution
                }

                $stmtInsertUser->close();
                $stmtUserInsertPrivileges->close(); // Corrected closing the prepared statement

                $response['success'] = "New user created successfully";
                echo json_encode($response);
            }
        }
    }


















}

function handleUpload($inputName, &$response)
{
    $targetDir = "uploads/";
    $fileName = basename($_FILES[$inputName]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    if (in_array($fileType, $allowTypes)) {
        if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $targetFilePath)) {
            return $fileName;
        } else {
            $response['error'] = "Sorry, there was an error uploading your file.";
            return false;
        }
    } else {
        $response['error'] = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.';
        return false;
    }
}




function insertSalesAndMarketingPrivileges($userID, $conn)
{
    for ($moduleId = 3; $moduleId <= 15; $moduleId++) {
        $sqlSalesPriv = "INSERT INTO privileges (UserID, ModuleID, 
        AssignModule_View, AssignModule_Update, 
        Action_Add, Action_Update, Action_Delete, Action_View,
        Action_Reply, Action_Lock, Action_Unlock, Action_Hide, 
        Action_Show, Action_Reject, Action_Decline, Action_Pending, 
        Action_Review, Action_Request, 
        Hide_Module) 
        VALUES (?, ?, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1)";

        $stmtSalesPrivileges = $conn->prepare($sqlSalesPriv);

        if (!$stmtSalesPrivileges) {
            $response['error'] = "Error preparing privileges statement: " . $conn->error;
            echo json_encode($response);
            exit(); // Stop script execution
        }

        if ($moduleId == 3 || $moduleId == 4 || $moduleId == 7) {
            // For modules 3, 4, and 7: Action_Update only
            if (!$stmtSalesPrivileges->bind_param("ii", $userID, $moduleId) || !$stmtSalesPrivileges->execute()) {
                $response['error'] = "Error inserting privileges: " . $conn->error;
                echo json_encode($response);
                exit(); // Stop script execution
            }
        } elseif ($moduleId == 5) {
            // For module 5: Add, Update, Delete
            if (!$stmtSalesPrivileges->bind_param("ii", $userID, $moduleId) || !$stmtSalesPrivileges->execute()) {
                $response['error'] = "Error inserting privileges: " . $conn->error;
                echo json_encode($response);
                exit(); // Stop script execution
            }
        } elseif ($moduleId == 11 || $moduleId == 12 || $moduleId == 14) {
            // For modules 11, 12, 14: Add, Update, Delete, View
            if (!$stmtSalesPrivileges->bind_param("ii", $userID, $moduleId) || !$stmtSalesPrivileges->execute()) {
                $response['error'] = "Error inserting privileges: " . $conn->error;
                echo json_encode($response);
                exit(); // Stop script execution
            }
        } else {
            // If the module ID is not listed, skip this iteration
            continue;
        }

    }
    $stmtSalesPrivileges->close();
    $response['success'] = "New user created successfully";
    echo json_encode($response);
}

function insertHRDMPrivileges($userID, $conn)
{
    for ($moduleId = 2; $moduleId <= 15; $moduleId++) {
        $sqlHRDMPriv = "INSERT INTO privileges (UserID, ModuleID, 
        AssignModule_View, AssignModule_Update, 
        Action_Add, Action_Update, Action_Delete, Action_View,
        Action_Reply, Action_Lock, Action_Unlock, Action_Hide, 
        Action_Show, Action_Reject, Action_Decline, Action_Pending, 
        Action_Review, Action_Request, 
        Hide_Module) 
        VALUES (?, ?, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1)";

        $stmtHRDMPrivileges = $conn->prepare($sqlHRDMPriv);

        if (!$stmtHRDMPrivileges) {
            $response['error'] = "Error preparing privileges statement: " . $conn->error;
            echo json_encode($response);
            exit(); // Stop script execution
        }

        if ($moduleId == 2) {
            // For module 5: Add, Update, Delete
            if (!$stmtHRDMPrivileges->bind_param("ii", $userID, $moduleId) || !$stmtHRDMPrivileges->execute()) {
                $response['error'] = "Error inserting privileges: " . $conn->error;
                echo json_encode($response);
                exit(); // Stop script execution
            }
        } elseif ($moduleId == 3 || $moduleId == 4 || $moduleId == 7) {
            // For modules 3, 4, and 7: Action_Update only
            if (!$stmtHRDMPrivileges->bind_param("ii", $userID, $moduleId) || !$stmtHRDMPrivileges->execute()) {
                $response['error'] = "Error inserting privileges: " . $conn->error;
                echo json_encode($response);
                exit(); // Stop script execution
            }
        } elseif ($moduleId == 5) {
            // For module 5: Add, Update, Delete
            if (!$stmtHRDMPrivileges->bind_param("ii", $userID, $moduleId) || !$stmtHRDMPrivileges->execute()) {
                $response['error'] = "Error inserting privileges: " . $conn->error;
                echo json_encode($response);
                exit(); // Stop script execution
            }
        } elseif ($moduleId == 12 || $moduleId == 14 || $moduleId == 15) {
            // For modules 11, 12, 14: Add, Update, Delete, View
            if (!$stmtHRDMPrivileges->bind_param("ii", $userID, $moduleId) || !$stmtHRDMPrivileges->execute()) {
                $response['error'] = "Error inserting privileges: " . $conn->error;
                echo json_encode($response);
                exit(); // Stop script execution
            }
        } else {
            // If the module ID is not listed, skip this iteration
            continue;
        }

    }

    $stmtHRDMPrivileges->close();
    $response['success'] = "New user created successfully";
    echo json_encode($response);
}

function insertOPDPrivileges($userID, $conn)
{
    for ($moduleId = 2; $moduleId <= 15; $moduleId++) {
        $sqlOPDPriv = "INSERT INTO privileges (UserID, ModuleID, 
        AssignModule_View, AssignModule_Update, 
        Action_Add, Action_Update, Action_Delete, Action_View,
        Action_Reply, Action_Lock, Action_Unlock, Action_Hide, 
        Action_Show, Action_Reject, Action_Decline, Action_Pending, 
        Action_Review, Action_Request, 
        Hide_Module) 
        VALUES (?, ?, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1)";

        $stmtOPDPrivileges = $conn->prepare($sqlOPDPriv);

        if (!$stmtOPDPrivileges) {
            $response['error'] = "Error preparing privileges statement: " . $conn->error;
            echo json_encode($response);
            exit(); // Stop script execution
        }

        if ($moduleId == 2) {
            // For module 5: Add, Update, Delete
            if (!$stmtOPDPrivileges->bind_param("ii", $userID, $moduleId) || !$stmtOPDPrivileges->execute()) {
                $response['error'] = "Error inserting privileges: " . $conn->error;
                echo json_encode($response);
                exit(); // Stop script execution
            }
        } elseif ($moduleId == 3 || $moduleId == 4 || $moduleId == 7) {
            // For modules 3, 4, and 7: Action_Update only
            if (!$stmtOPDPrivileges->bind_param("ii", $userID, $moduleId) || !$stmtOPDPrivileges->execute()) {
                $response['error'] = "Error inserting privileges: " . $conn->error;
                echo json_encode($response);
                exit(); // Stop script execution
            }
        } elseif ($moduleId == 5) {
            // For module 5: Add, Update, Delete
            if (!$stmtOPDPrivileges->bind_param("ii", $userID, $moduleId) || !$stmtOPDPrivileges->execute()) {
                $response['error'] = "Error inserting privileges: " . $conn->error;
                echo json_encode($response);
                exit(); // Stop script execution
            }
        } elseif ($moduleId == 12 || $moduleId == 14 || $moduleId == 15) {
            // For modules 11, 12, 14: Add, Update, Delete, View
            if (!$stmtOPDPrivileges->bind_param("ii", $userID, $moduleId) || !$stmtOPDPrivileges->execute()) {
                $response['error'] = "Error inserting privileges: " . $conn->error;
                echo json_encode($response);
                exit(); // Stop script execution
            }
        } else {
            // If the module ID is not listed, skip this iteration
            continue;
        }

    }
    $stmtOPDPrivileges->close();
    $response['success'] = "New user created successfully";
    echo json_encode($response);
}
?>