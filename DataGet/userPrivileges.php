<?php
require('../config/db_con.php');
include('../security.php');
// Start the session

// Check if the user is logged in
if (isset($_SESSION['Username'])) {
    $loggedInName = $_SESSION['Username'];

    // Prepare the SQL statement with placeholders
    $query = "SELECT u.*, p.*, usr.*, m.ModuleName, m.ModuleID FROM privileges p
    INNER JOIN users u ON p.UserID = u.UserID
    INNER JOIN userroles usr ON u.UserRoleID = usr.UserRoleID
    INNER JOIN modules m ON p.ModuleID = m.ModuleID
    WHERE u.Username = ? AND p.Hide_Module = 1 AND p.Action_Add IN (1, 0) AND p.Action_Update IN (1, 0) AND p.Action_Delete IN (1, 0) AND p.Action_View IN (1, 0) AND p.Action_Lock IN (1, 0)  AND p.AssignModule_View IN (1, 0) AND p.AssignModule_Update IN (1, 0)";

    // Prepare the statement
    $stmt = $conn->prepare($query);
    
    // Bind the parameter
    $stmt->bind_param("s", $loggedInName);

    // Execute the query
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();

    // Fetch the row
    if ($result) {
        $row = $result->fetch_assoc();
    } else {
        // Handle the case where the query fails
        echo "Error in fetching RoleID and UserID: " . $conn->error;
    }

    // Check if Action_Add is 1 (allowed) or 0 (not allowed)
    $actionAdd = $row['Action_Add'];
    $actionUpdate = $row['Action_Update'];
    $actionDelete = $row['Action_Delete'];
    $actionView = $row['Action_View'];
    $actionLock = $row['Action_Lock'];
    $actionUnlock = $row['Action_Unlock'];
    $actionModuleView = $row['AssignModule_View'];
    $actionModuleUpdate = $row['AssignModule_Update'];

 
    
}
if ($row['is_Lock'] == 1) {
    // User account is locked
    header("Location: lock_account.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form class="needs-validation" novalidate>
 


<table class="table datatable">
<thead>
    <tr>
        <th scope="col">Module Name</th>
     
        <th scope="col">Add</th>
        <th scope="col">Edit</th>
        <th scope="col">Delete</th>
        <th scope="col">View</th>
        <th scope="col">Reply</th>
        <th scope="col">Lock</th>
        <th scope="col">Unlock</th>
        <th scope="col">Hide</th>
        <th scope="col">Show</th>
        <th scope="col">Reject</th>
        <th scope="col">Decline</th>
        <th scope="col">Pending</th>
        <th scope="col">Review</th>
        <th scope="col">Request</th>
        <th scope="col">Show Module</th>
    </tr>
</thead>
<tbody>

<?php
 

// Assuming $_POST['userid'] contains the user ID
$userid = $_POST['userid'];

// Select all modules
$userPriv = mysqli_query($conn, "SELECT m.*, p.* FROM modules m LEFT JOIN privileges p ON m.ModuleID = p.ModuleID AND p.UserID = $userid");

while ($row = mysqli_fetch_assoc($userPriv)){
    ?>
    <tr>
        <td><?php echo $row['ModuleName']?></td>
        
        <?php
        // List of permissions
        $permissions = ['Action_Add', 'Action_Update', 'Action_Delete', 'Action_View', 'Action_Reply', 'Action_Lock', 'Action_Unlock', 'Action_Hide', 'Action_Show', 'Action_Reject', 'Action_Decline', 'Action_Pending', 'Action_Review', 'Action_Request', 'Hide_Module'];

        // Loop through each permission
        foreach ($permissions as $permission) {
            echo '<td class="text-center"><input class="form-check-input permission-checkbox" type="checkbox" value="1" id="' . $permission . '_Checkbox"';
            // Check if the permission is set to 1 in the database
            if ($row[$permission] == 1) {
                echo ' checked';
            }
            echo '></td>';
        }
        ?>
    </tr>
<?php
}
?>
</tbody>
</table>
<div class="col-12">
    <div class="d-flex gap-2 justify-content-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <?php
            if($actionModuleUpdate == 1) {
                echo '<button type="button" id="updateBtn" class="btn btn-primary">Update</button>';
            }
            
        ?>
    </div>
</div>
</form>
</body>
</html>