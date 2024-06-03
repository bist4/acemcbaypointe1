<!-- <
require('../../config/db_con.php');
session_start();

// Check if the connection is successful
if ($conn->connect_error) {
    // Redirect to die_con.php if connection fails
    header("Location: ../../config/die_con.php");
    exit(); // Ensure that script execution stops after redirection
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $CategoryID = mysqli_real_escape_string($conn, $_POST['categoryID']); 
    $CategoryName = mysqli_real_escape_string($conn, $_POST['categoryName']); 

    // Check if CategoryID is provided
    if (empty($CategoryID)) {
        $response['error'] = "CategoryID is required for updating.";
        echo json_encode($response);
        exit(); // Stop further execution
    }

    // Check if the category exists before updating
    $sqlCategoryCheck = "SELECT * FROM categories WHERE CategoryID = ?";
    $stmtCategoryCheck = $conn->prepare($sqlCategoryCheck);
    $stmtCategoryCheck->bind_param("i", $CategoryID);
    $stmtCategoryCheck->execute();
    $resultCategoryCheck = $stmtCategoryCheck->get_result();

    if ($resultCategoryCheck && $resultCategoryCheck->num_rows > 0) {
        // Category exists, proceed with the update
        $sql = "UPDATE categories SET CategoryName = ? WHERE CategoryID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $CategoryName, $CategoryID); // "si" indicates string and integer parameters

        if ($stmt->execute()) {
            // Log activity if the user is logged in
            if (isset($_SESSION['Username'])) {
                $loggedInUsername = $_SESSION['Username'];

                // Check if the logged-in user exists in the database
                $sqlUserCheck = "SELECT UserID FROM users WHERE Username=?";
                $stmtUserCheck = $conn->prepare($sqlUserCheck);
                $stmtUserCheck->bind_param("s", $loggedInUsername);
                $stmtUserCheck->execute();
                $resultUserCheck = $stmtUserCheck->get_result();

                if ($resultUserCheck && $resultUserCheck->num_rows > 0) {
                    $row = $resultUserCheck->fetch_assoc();
                    $loggedInUserID = $row['UserID'];

                    $action = 'UPDATE';
                    $activity = 'Update category: ' . $CategoryName;
                    
                    // Set the timezone to Asia/Manila
                    date_default_timezone_set('Asia/Manila');
                    
                    $formattedDateTime = date('Y-m-d H:i:s');

                    // Insert activity log into activity_history table
                    $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                    $stmtLog = $conn->prepare($sqlLog);
                    $active = 1; // Assuming 'Active' field is boolean
                    $stmtLog->bind_param("sssii", $action, $activity, $formattedDateTime, $loggedInUserID, $active);
                    $stmtLog->execute();
                } else {
                    // Handle user not found in database
                }
            }

            $response['success'] = "Category updated successfully";
        } else {
            $response['error'] = "Error updating category: " . $stmt->error;
        }
    } else {
        // Category does not exist
        $response['error'] = "Category with ID $CategoryID does not exist.";
    }

    // Return JSON response
    echo json_encode($response);

    // Close statements
    $stmt->close();
    $stmtCategoryCheck->close();

    // Close connection
    $conn->close();
}
?> -->

<?php

// Initialize response array
$response = array();

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if CategoryID and CategoryName are set in the POST request
    if (isset($_POST['CategoryID']) && isset($_POST['CategoryName'])) {
        // Sanitize inputs to prevent SQL injection
        $categoryID = $_POST['CategoryID'];
        $categoryName = $_POST['CategoryName'];

        require('../../config/db_con.php');
        session_start();

        // Check if the category exists before updating
        $sqlCategoryCheck = "SELECT * FROM categories WHERE CategoryID = ?";
        $stmtCategoryCheck = $conn->prepare($sqlCategoryCheck);
        $stmtCategoryCheck->bind_param("i", $categoryID);
        $stmtCategoryCheck->execute();
        $resultCategoryCheck = $stmtCategoryCheck->get_result();

        if ($resultCategoryCheck && $resultCategoryCheck->num_rows > 0) {
            // Fetch original category details
            $originalCategory = $resultCategoryCheck->fetch_assoc();
            $originalCategoryName = $originalCategory['CategoryName'];

            // Prepare and execute SQL statement to update category name
            $sql = "UPDATE categories SET CategoryName = ? WHERE CategoryID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $categoryName, $categoryID);

            if ($stmt->execute()) {
                // Fetch updated category details
                $updatedCategory = array(
                    'CategoryID' => $categoryID,
                    'CategoryName' => $categoryName
                );

                // Log activity if the user is logged in
                if (isset($_SESSION['Username'])) {
                    $loggedInUsername = $_SESSION['Username'];

                    // Check if the logged-in user exists in the database
                    $sqlUserCheck = "SELECT UserID FROM users WHERE Username=?";
                    $stmtUserCheck = $conn->prepare($sqlUserCheck);
                    $stmtUserCheck->bind_param("s", $loggedInUsername);
                    $stmtUserCheck->execute();
                    $resultUserCheck = $stmtUserCheck->get_result();

                    if ($resultUserCheck && $resultUserCheck->num_rows > 0) {
                        $row = $resultUserCheck->fetch_assoc();
                        $loggedInUserID = $row['UserID'];

                        $action = 'UPDATE';
                        $activity = 'Update category: ' . $originalCategoryName . ' to ' . $categoryName;

                        // Set the timezone to Asia/Manila
                        date_default_timezone_set('Asia/Manila');

                        $formattedDateTime = date('Y-m-d H:i:s');

                        // Insert activity log into activity_history table
                        $sqlLog = "INSERT INTO activity_history (Action, Activity, DateTime, UserID, Active) VALUES (?, ?, ?, ?, ?)";
                        $stmtLog = $conn->prepare($sqlLog);
                        $active = 1; // Assuming 'Active' field is boolean
                        $stmtLog->bind_param("sssii", $action, $activity, $formattedDateTime, $loggedInUserID, $active);
                        $stmtLog->execute();
                    } else {
                        // Handle user not found in database
                        $response['error'] = "User not found in the database!";
                    }
                }
                // If the query is successful, return success message along with original and updated data
                $response['success'] = "Category updated successfully!";
                $response['original_category'] = $originalCategory;
                $response['updated_category'] = $updatedCategory;
            } else {
                // If the query fails, return error message
                $response['error'] = "Failed to update category!";
            }

            // Close statement
            $stmt->close();
        } else {
            // If CategoryID or CategoryName is not set, return error message
            $response['error'] = "Category ID or name is missing!";
        }

        // Close statement
        $stmtCategoryCheck->close();

        // Close database connection
        $conn->close();
    } else {
        // If request method is not POST, return error message
        $response['error'] = "Invalid request method!";
    }
}

// Send response back to the client
echo json_encode($response);
?>

