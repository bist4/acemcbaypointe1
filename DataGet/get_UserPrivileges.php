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

    // Close the statement
    $stmt->close();
    
    // Close the database connection
    $conn->close();
}
if ($row['is_Lock'] == 1) {
    // User account is locked
    header("Location: lock_account.php");
    exit();
}

?>

<?php 
// Assuming $_POST['privilegesid'] contains the privileges ID sent from the client-side
$userid = $_POST['userid'];

// Perform database connection
require('../config/db_con.php');

// Query to fetch user data


$query = "SELECT p.*, m.* FROM privileges p INNER JOIN modules m ON p.ModuleID = m.ModuleID WHERE UserID = ?";
$stmt = $conn->prepare($query);

// Bind the parameter
$stmt->bind_param("i", $userid);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

echo'<form class="needs-validation" novalidate>';
echo    '<div class="d-flex justify-content-end">';
echo        '<div class="form-check form-switch">';
echo            '<input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">';
echo            '<label class="form-check-label" for="flexSwitchCheckDefault">All Privileges</label>';
echo        '</div>';
echo    '</div>';

echo    '<div class="table-responsive">';
echo       '<table class="table  table-hover">';
echo           '<thead>';
echo               '<tr>';
echo                   '<th scope="col">Module Permission</th>';
echo                   '<th scope="col">Add</th>';
echo                   '<th scope="col">Edit</th>';
echo                   '<th scope="col">Delete</th>';
echo                   '<th scope="col">View</th>';
echo                   '<th scope="col">Reply</th>';
echo                   '<th scope="col">Lock</th>';
echo                   '<th scope="col">Unlock</th>';
echo                   '<th scope="col">Hide</th>';
echo                   '<th scope="col">Show</th>';
echo                   '<th scope="col">Reject</th>';
echo                   '<th scope="col">Decline</th>';
echo                   '<th scope="col">Pending</th>';
echo                   '<th scope="col">Review</th>';
echo                   '<th scope="col">Hide Module</th>';
echo               '</tr>';
echo           '</thead>';
echo           '<tbody>';


// Fetch data and generate HTML
while ($row = $result->fetch_assoc()) {
    echo '<tr class="text-center">';
    // Output data as desired in the table rows
    echo '<td>' . $row['ModuleName'] . '</td>';
    
    // Check if each permission is set to 1 and set the checkbox accordingly
    $permissions = ['Action_Add', 'Action_Update', 'Action_Delete', 'Action_View', 'Action_Reply', 'Action_Lock', 'Action_Unlock', 'Action_Hide', 'Action_Show', 'Action_Reject', 'Action_Decline', 'Action_Pending', 'Action_Review', 'Hide_Module'];
    foreach ($permissions as $permission) {
        echo '<td><input class="form-check-input permission-checkbox" type="checkbox" value="1" id="' . $permission . '_Checkbox"';
        // Check if the permission is set to 1 in the database
        if ($row[$permission] == 1) {
            echo ' checked';
        }
        echo '></td>';
    }
    
    echo '</tr>';
} 


echo            '</tbody>';
echo        '</table>';
echo    '</div>';

echo     '<div class="col-12">';
echo         '<div class="d-flex gap-2 justify-content-end">';
echo             '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
if ($actionModuleUpdate == 1) {
echo             '<button type="button" id="updateBtn" class="btn btn-primary">Update</button>';
}
echo         '</div>';
echo     '</div>';
echo '</form>';

echo '<script>';
echo '$(document).ready(function() {';
echo '    var prevPermissionsState = [];'; // Store previous states
echo '    $(".permission-checkbox").each(function() {';
echo '        prevPermissionsState.push($(this).prop("checked"));'; // Store each checkbox state
echo '    });';
echo '    $("#flexSwitchCheckDefault").change(function() {';
echo '        var isChecked = $(this).prop("checked");';
echo '        $(".permission-checkbox").prop("checked", isChecked);';
echo '        if (!isChecked) {';
echo '            $(".permission-checkbox").each(function(index) {';
echo '                $(this).prop("checked", prevPermissionsState[index]);'; // Restore previous states
echo '            });';
echo '        }';
echo '    });';
echo '});';
echo '</script>';

 
 



?>


<head>
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
/>
</head>
 
 
<!-- Authentication modal
<div class="modal fade animate_animated animate__heartBeat" id="auth" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLgLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="exampleModalLgLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody3">
                <div class="pt-4 pb-2 text-center">
                    <i class="bi bi-lock text-danger " style="font-size: 2em"></i>
                    <h5 class="card-title pb-0 fs-4">Authentication</h5>
                    <p class="small">Please enter your PIN code to continue.</p>
                </div>

                <?php if (isset($_GET['error'])) { ?>
                    <p class="error"><?php echo $_GET['error']; ?></p>
                <?php } ?>
                <form class="row g-3 needs-validation authen" novalidate method="post">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="input-group pin-code-input justify-content-center" id="otp-input">
                            <input type="text" maxlength="1" class="strong-2" required placeholder="_" autocomplete="no" pattern="\d*">
                            <input type="text" maxlength="1" class="strong-2" required placeholder="_" autocomplete="no" pattern="\d*">
                            <input type="text" maxlength="1" class="strong-2" required placeholder="_" autocomplete="no" pattern="\d*">
                            <input type="text" maxlength="1" class="strong-2" required placeholder="_" autocomplete="no" pattern="\d*">
                            <input type="text" maxlength="1" class="strong-2" required placeholder="_" autocomplete="no" pattern="\d*">
                            <input type="text" maxlength="1" class="strong-2" required placeholder="_" autocomplete="no" pattern="\d*">
                        </div>
                    </div>
                    <div class="col-12  d-flex justify-content-center">
                        <button class="btn btn-success w-50" type="submit">Verify</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div> -->

<!-- email modal -->
<div class="modal fade animate_animated animate__heartBeat" id="auth" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLgLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="exampleModalLgLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody3">
                <div class="pt-4 pb-2 text-center">
                    <i class="bi bi-lock text-danger " style="font-size: 2em"></i>
                    <h5 class="card-title pb-0 fs-4">Authentication</h5>
                    <p class="small">Please enter your password to continue update.</p>
                </div>

                <?php if (isset($_GET['error'])) { ?>
                    <p class="error"><?php echo $_GET['error']; ?></p>
                <?php } ?>
                <form class="row g-3 needs-validation authen" novalidate action="#" method="POST">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="input-group justify-content-center">
                            <input type="password" name="password" class="form-control">
                            
                        </div>
                    </div>
                    <div class="col-12  d-flex justify-content-center">
                        <!-- <button class="btn btn-success w-50" type="submit">Send OTP</button> -->
                        <input type="submit" value="Continue"  class="btn btn-success w-50" name="register">


                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


<!-- <script>
    $(document).ready(function (){
        $('#updateBtn').click( function() {
             
            
        }); 
    });
</script>
  -->
