<?php
require('../config/db_con.php');
session_start();

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ((isset($_FILES['profile']) && !empty($_FILES['profile']['name']))) {
        $profile = handleUpload('profile');
    } else {
        $response['error'] = 'Please select a profile photo to upload.';
        echo json_encode($response);
        exit(); // Stop script execution
    }

    $success = true;
 


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

    $department = $_POST['BaypointeDepartmentID'];
    $role = $_POST['role'];

    // Calculate age from birthday
    $birthDate = new DateTime($birthday);
    $currentDate = new DateTime();
    $age = $currentDate->diff($birthDate)->y;

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_ARGON2I);
    $active = 1;
    $is_login = 0;
    $is_Lock = 0;

    //check if username is alerady taken 
    $sqlUsernameCheck = "SELECT Username FROM users WHERE Username = ?";
    $stmtCheckUsername = $conn->prepare($sqlUsernameCheck);
    $stmtCheckUsername->bind_param("s", $username);
    $stmtCheckUsername->execute();
    $resultUsername = $stmtCheckUsername->get_result();
 

    if($resultUsername ->num_rows > 0){
        $success = false;
        $response['error'] = "Username is not available";
    
    }
    else {

    $sql = "INSERT INTO users (IdNumber, Fname, Lname, ProfilePhoto, Gender, Birthday, Age, Address, ContactNumber, Email, Username, Password, is_Admin_Group, is_Ancillary_Group, is_Nursing_Group, is_Outsource_Group, BaypointeDepartmentID, UserRoleID, is_Login, Active, CreatedAt, is_Lock) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("sssssssssssssssssssii", $IdNumber, $fname, $lname, $profile, $gender, $birthday, $age, $address, $contact, $email, $username, $hashedPassword, $isAdmin, $isAncillary, $isNursing, $isOutsource, $department, $role, $is_login, $active, $is_Lock);

    // Execute the statement
    if ($stmt->execute()) {
        $response['success'] = "New user created successfully";

         // Fetching the first name and last name of the newly added user
         $newUserFname = $fname;
         $newUserLname = $lname;
 
 
         $userID = $_SESSION['UserID'];
 
         // Retrieve the first name of the user
         $select_sql = "SELECT Fname FROM users WHERE UserID = ?";
         $stmt = mysqli_prepare($conn, $select_sql);
         mysqli_stmt_bind_param($stmt, "i", $userID);
         mysqli_stmt_execute($stmt);
         mysqli_stmt_bind_result($stmt, $fname);
         mysqli_stmt_fetch($stmt);
         mysqli_stmt_close($stmt);
         
         $logActivity = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) 
                 VALUES ('ADD', CONCAT('Super Admin - ', ?, ' added new account user ', ?, ' ', ?), NOW(), ?, 1 )";
         $stmt = mysqli_prepare($conn, $logActivity);
         mysqli_stmt_bind_param($stmt, "sssi", $newUserFname, $newUserFname, $newUserLname, $userID);
         mysqli_stmt_execute($stmt);
         mysqli_stmt_close($stmt);

    } else {
        $response['error'] = "Error: " . $sql . "<br>" . $conn->error;
    }



    //For Super Admin Priveleges
    if($role == 0){
        $userId = $conn->insert_id;

        for ($moduleId = 1; $moduleId <= 19; $moduleId++) {
            $stmtPrivileges = $conn->prepare("INSERT INTO privileges (UserID, ModuleID,AssignModule_View, AssignModule_Update, Action_Add, Action_Update, Action_Delete, Action_View, Action_Reply, Action_Lock, Action_Unlock, Action_Hide, Action_Show, Action_Reject, Action_Decline, Action_Pending, Action_Review, Hide_Module) 
            VALUES (?, ?, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1)");

            $stmtPrivileges->bind_param("ii", $userId, $moduleId);
            $stmtPrivileges->execute();
            $stmtPrivileges->close();
        }
    
    }
    //For Admin Priveleges
    else if($role == 1){
        //For Sales and Marketing 
        if ($department == 19) {
            $userId = $conn->insert_id;
            
            for ($moduleId = 3; $moduleId <= 15; $moduleId++) {
                $stmtPrivileges = $conn->prepare("INSERT INTO privileges (UserID, ModuleID, Action_Add, Action_Update, Action_Delete, Action_View, Action_Reply, Action_Lock, Action_Unlock, Action_Hide, Action_Show, Action_Reject, Action_Decline, Action_Pending, Action_Review, Hide_Module) 
                VALUES (?, ?, ?, ?, ?, ?, 0,0,0,0,0,0,0,0,0,1)");
        
                // Assign values to variables
                $actionAdd = 1;
                $actionUpdate = 1;
                $actionDelete = 1;
                $actionView = 1;
        
                if ($moduleId == 3 || $moduleId == 4 || $moduleId == 7) {
                    // For modules 3, 4, and 7: Action_Update only
                    $stmtPrivileges->bind_param("iiii", $userId, $moduleId, $actionAdd, $actionUpdate );
                } elseif ($moduleId == 5) {
                    // For module 5: Add, Update, Delete
                    $stmtPrivileges->bind_param("iiiii", $userId, $moduleId, $actionAdd, $actionUpdate, $actionDelete);
                } elseif ($moduleId == 11 || $moduleId == 12 || $moduleId == 14 || $moduleId == 15) {
                    // For modules 11, 12, 14: Add, Update, Delete, View
                    $actionView = 1; // Update actionView variable
                    $stmtPrivileges->bind_param("iiiiii", $userId, $moduleId, $actionAdd, $actionUpdate, $actionDelete, $actionView);
                } else {
                    // If the module ID is not listed, skip this iteration
                    continue;
                }
                
                $stmtPrivileges->execute();
                $stmtPrivileges->close();
            }
        }

        //For HRDM 
        if ($department == 9) {
            $userId = $conn->insert_id;
            
            for ($moduleId = 2; $moduleId <= 15; $moduleId++) {
                $stmtPrivileges = $conn->prepare("INSERT INTO privileges (UserID, ModuleID, Action_Add, Action_Update, Action_Delete, Action_View, Action_Reply, Action_Lock, Action_Unlock, Action_Hide, Action_Show, Action_Reject, Action_Decline, Action_Pending, Action_Review, Hide_Module) 
                VALUES (?, ?, ?, ?, ?, ?, ?,0,0,0,0,0,0,0,0,1)");
        
                // Assign values to variables
                $actionAdd = 1;
                $actionUpdate = 1;
                $actionDelete = 1;
                $actionView = 1;
                $actionReply = 1;
        
                if ($moduleId == 2) {
                    // For module 5: Add, Update, Delete
                    $stmtPrivileges->bind_param("iiiii", $userId, $moduleId, $actionView, $actionReply, $actionDelete);
                }
                elseif ($moduleId == 3 || $moduleId == 4 || $moduleId == 7) {
                    // For modules 3, 4, and 7: Action_Update only
                    $stmtPrivileges->bind_param("iiiii", $userId, $moduleId, $actionAdd, $actionUpdate, $actionView);
                } elseif ($moduleId == 5) {
                    // For module 5: Add, Update, Delete
                    $stmtPrivileges->bind_param("iiiiii", $userId, $moduleId, $actionAdd, $actionUpdate, $actionDelete, $actionView);
                } elseif ($moduleId == 12 || $moduleId == 14 || $moduleId == 15) {
                    // For modules 11, 12, 14: Add, Update, Delete, View
                    $actionView = 1; // Update actionView variable
                    $stmtPrivileges->bind_param("iiiiii", $userId, $moduleId, $actionAdd, $actionUpdate, $actionDelete, $actionView);
                } else {
                    // If the module ID is not listed, skip this iteration
                    continue;
                }
                
                $stmtPrivileges->execute();
                $stmtPrivileges->close();
            }
        }
        
        //For OPD 
        if ($department == 43) {
            $userId = $conn->insert_id;
            
            for ($moduleId = 2; $moduleId <= 15; $moduleId++) {
                $stmtPrivileges = $conn->prepare("INSERT INTO privileges (UserID, ModuleID, Action_Add, Action_Update, Action_Delete, Action_View, Action_Reply, Action_Lock, Action_Unlock, Action_Hide, Action_Show, Action_Reject, Action_Decline, Action_Pending, Action_Review, Hide_Module) 
                VALUES (?, ?, ?, ?, ?, ?, ?,0,0,0,0,0,0,0,0,1)");
        
                // Assign values to variables
                $actionAdd = 1;
                $actionUpdate = 1;
                $actionDelete = 1;
                $actionView = 1;
                $actionReply = 1;
        
                if ($moduleId == 2) {
                    // For module 5: Add, Update, Delete
                    $stmtPrivileges->bind_param("iiiii", $userId, $moduleId, $actionView, $actionReply, $actionDelete);
                }
                elseif ($moduleId == 3 || $moduleId == 4 || $moduleId == 7) {
                    // For modules 3, 4, and 7: Action_Update only
                    $stmtPrivileges->bind_param("iiiii", $userId, $moduleId, $actionAdd, $actionUpdate, $actionView);
                } elseif ($moduleId == 5) {
                    // For module 5: Add, Update, Delete
                    $stmtPrivileges->bind_param("iiiiii", $userId, $moduleId, $actionAdd, $actionUpdate, $actionDelete, $actionView);
                } elseif ($moduleId == 12 || $moduleId == 14 || $moduleId == 15) {
                    // For modules 11, 12, 14: Add, Update, Delete, View
                    $actionView = 1; // Update actionView variable
                    $stmtPrivileges->bind_param("iiiiii", $userId, $moduleId, $actionAdd, $actionUpdate, $actionDelete, $actionView);
                } else {
                    // If the module ID is not listed, skip this iteration
                    continue;
                }
                
                $stmtPrivileges->execute();
                $stmtPrivileges->close();
            }
        }
        
    }
    else if ($role == 2){
        $userId = $conn->insert_id;
        $moduleId = 14; 

        $stmtPrivileges = $conn->prepare("INSERT INTO privileges (UserID, ModuleID, Action_Add, Action_Update, Action_Delete, Action_View, Action_Reply, Action_Lock, Action_Unlock, Action_Hide, Action_Show, Action_Reject, Action_Decline, Action_Pending, Action_Review, Action_Request, Hide_Module) 
            VALUES (?, ?, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1)");

        $stmtPrivileges->bind_param("ii", $userId, $moduleId);
        $stmtPrivileges->execute();
        $stmtPrivileges->close();
         
    }
    else if ($role == 3){
        $userId = $conn->insert_id;
        $moduleId = 15; 

        $stmtPrivileges = $conn->prepare("INSERT INTO privileges (UserID, ModuleID, Action_Add, Action_Update, Action_Delete, Action_View, Action_Reply, Action_Lock, Action_Unlock, Action_Hide, Action_Show, Action_Reject, Action_Decline, Action_Pending, Action_Review, Action_Request, Hide_Module) 
            VALUES (?, ?, 0, 0, 0, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 1)");

        $stmtPrivileges->bind_param("ii", $userId, $moduleId);
        $stmtPrivileges->execute();
        $stmtPrivileges->close();
         
    }

    echo json_encode($response);
}
}

function handleUpload($inputName) {
    $targetDir = "uploads/";
    $fileName = basename($_FILES[$inputName]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Allow certain file formats
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    if (in_array($fileType, $allowTypes)) {
        // Upload file to server
        if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $targetFilePath)) {
            return $fileName;
        } else {
            $response['error'] = "Sorry, there was an error uploading your file.";
            echo json_encode($response);
            exit(); // Stop script execution
        }
    } else {
        $response['error'] = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.';
        echo json_encode($response);
        exit(); // Stop script execution
    }
}
?>
