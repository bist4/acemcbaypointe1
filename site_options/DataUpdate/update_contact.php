<?php
require ('../../config/db_con.php');
session_start();

if ($conn->connect_error) {
    // Redirect to die_con.php if connection fails
    header("Location: ../../config/die_con.php");
    exit(); // Ensure that script execution stops after redirection
}

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the required parameters are set
    if (isset($_POST['Contact_InformationID']) && isset($_POST['addressVal']) && isset($_POST['SubicOfficeVal']) && isset($_POST['emailVal'])) {
        // Sanitize and retrieve the parameters
        $Contact_InformationID = $_POST['Contact_InformationID'];
        $Address = $_POST['addressVal'];
        $Phone = $_POST['SubicOfficeVal'];
        $Email = $_POST['emailVal'];
        $smartVal = $_POST['smartVal'];
        $globeVal = $_POST['globeVal'];
        $sunVal = $_POST['sunVal'];

        // Concatenate the values with <br> tags
        $mobile = 'Smart:' . $smartVal . "\n" . 'Globe:' . $globeVal . "\n" . 'Sun:' . $sunVal;


        $userAuthor = $_SESSION['Username'];

        // Check if the logged-in user exists in the database
        $sqlUserCheck = "SELECT UserID FROM users WHERE Username=?";
        $stmtUserCheck = $conn->prepare($sqlUserCheck);
        $stmtUserCheck->bind_param("s", $userAuthor);
        $stmtUserCheck->execute();
        $resultUserCheck = $stmtUserCheck->get_result();

        if ($resultUserCheck && $resultUserCheck->num_rows > 0) {
            $user = $resultUserCheck->fetch_assoc();
            $loggedInUserID = $user['UserID'];

            // Fetch original contact details before updating
            $sqlOriginalData = "SELECT Address, Phone, Email, Mobile FROM contact_information WHERE Contact_InformationID = ?";
            $stmtOriginalData = $conn->prepare($sqlOriginalData);
            $stmtOriginalData->bind_param("i", $Contact_InformationID);
            $stmtOriginalData->execute();
            $resultOriginalData = $stmtOriginalData->get_result();

            if ($resultOriginalData && $resultOriginalData->num_rows > 0) {
                $originalData = $resultOriginalData->fetch_assoc();
                $originalAddress = $originalData['Address'];
                $originalSubicOffice = $originalData['Phone'];
                $originalEmail = $originalData['Email'];
                $originalmobile = $originalData['Mobile'];

                // Update data in the contact_information table
                $sql = "UPDATE contact_information SET Address = ?, Phone = ?, Email = ?, Mobile=? WHERE Contact_InformationID = ?";
                $stmt = $conn->prepare($sql);

                if ($stmt === false) {
                    // Handle error
                    echo json_encode(["error" => "Prepare statement failed: " . $conn->error]);
                } else {
                    // Bind parameters
                    $stmt->bind_param("ssssi", $Address, $Phone, $Email, $mobile, $Contact_InformationID);

                    // Execute the statement
                    $stmt->execute();

                    // Check if the update was successful
                    if ($stmt->affected_rows > 0) {
                        $activity = 'Updated ';
                        $changes = array();

                        if ($Address != $originalAddress) {
                            $changes[] = 'Address from ' . $originalAddress . ' to ' . $Address;
                        }
                        if ($Phone != $originalSubicOffice) {
                            $changes[] = 'Phone from ' . $originalSubicOffice . ' to ' . $Phone;
                        }
                        if ($Email != $originalEmail) {
                            $changes[] = 'Email from ' . $originalEmail . ' to ' . $Email;
                        }
                        if ($mobile != $originalmobile) {
                            $changes[] = 'Mobile from ' . $originalmobile . ' to ' . $mobile;
                        }

                        // Constructing activity message
                        $activity .= implode(', ', $changes);

                        // Log activity only if there were changes
                        if (!empty($changes)) {
                            // Log the activity
                            // Set the timezone to Asia/Manila
                            date_default_timezone_set('Asia/Manila');
                            $formattedDateTime = date('Y-m-d H:i:s');
                            $active = 1;

                            // Insert activity log into activity_history table
                            $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                            $stmtLog = $conn->prepare($sqlLog);
                            $action = 'UPDATE';
                            $stmtLog->bind_param("sssii", $action, $activity, $formattedDateTime, $loggedInUserID, $active);
                            $stmtLog->execute();
                        }

                        // Send success response with appropriate message
                        echo json_encode(["success" => "Contact Information updated successfully"]);
                    } else {
                        // Send error response
                        echo json_encode(["error" => "No changes were made or updated"]);
                    }
                }
            } else {
                echo json_encode(["error" => "Original data not found"]);
            }
        } else {
            echo json_encode(["error" => "User not found"]);
        }
    } else {
        echo json_encode(["error" => "Required parameters missing"]);
    }
} else {
    // Send error response if not a POST request
    echo json_encode(["error" => "Invalid request method"]);
}

// Close the database connection
$conn->close();
?>