<?php
require ('../config/db_con.php');

// Assuming $_POST['userid'] contains the user ID sent from the client-side
$userid = $_POST['userid'];

// Query to fetch user data
$table = mysqli_query($conn, "SELECT u.*, u.Password, usr.UserRoleName, bd.DepartmentName FROM users u   
            INNER JOIN userroles usr ON u.UserRoleID = usr.UserRoleID 
            INNER JOIN baypointedepartments bd ON u.BaypointeDepartmentID = bd.BaypointeDepartmentID
            WHERE u.Active = 1 AND u.UserID = $userid");
 
// Fetch data and generate HTML
if ($row = mysqli_fetch_assoc($table)) {

    echo '<div class="row">' ; //1st row
    echo    '<div class="col-md-6">'; //1st col
    echo        '<div class="mb-3">';
    echo            '<p  class="form-label">Full Name: ' . $row['Fname'] .  ' ' .$row['Lname'].'</p>';
    echo        '</div>'; // end of mb-3

    echo        '<div class="row">' ;
    echo            '<div class="col-md-6">';
    echo                '<div class="mb-3">';
    echo                    '<p  class="form-label">Gender: ' . $row['Gender'] . '</p>';
    echo                '</div>'; // end of mb-3

    echo            '</div>'; //end of col-md-6
    echo            '<div class="col-md-6">';
    echo                '<div class="mb-3">';
    echo                    '<p  class="form-label">Birthday: ' . $row['Birthday'] . '</p>';
    echo                '</div>'; // end of mb-3
    echo            '</div>'; //end of col-md-6
    
    echo        '</div>'; //end of row

    echo        '<div class="mb-3">';
    echo            '<p  class="form-label">Address: ' . $row['Address'] . '</p>';
    echo        '</div>'; // end of mb-3

    echo        '<div class="mb-3">';
    echo            '<p  class="form-label">Contact Number: ' . $row['ContactNumber'] . '</p>';
    echo        '</div>'; // end of mb-3

    
    echo        '<div class="mb-3">';
    echo            '<p  class="form-label">Email: ' . $row['Email'] . '</p>';
    echo        '</div>'; // end of mb-3

    echo        '<div class="mb-3">';
    echo            '<p  class="form-label">Username: ' . $row['Username'] . '</p>';
    echo        '</div>'; // end of mb-3



    echo    '</div>'; //end of 1st col-md-6

    
    echo    '<div class="col-md-6">'; //2nd col
    echo        '<div class="mb-3">';
    echo           '<div class="d-flex justify-content-center">';
    echo                '<img width="200px" height="200px" src="DataAdd/uploads/' . $row['ProfilePhoto'] . '">';
    echo            '</div>'; // end of mb-3
    echo        '</div>'; // end of mb-3
    echo        '<div class="d-flex justify-content-center">ID Number: ' .$row['IdNumber'].'</div>';
    

    echo    '</div>'; //end of 2nd col-md-6
    
    echo '</div>'; //end of 1st row

    echo '<div class="row">' ; //1st row

    echo    '<div class="col-md-6">'; //1st col
    echo        '<div class="mb-3">';
    echo            '<p class="form-label">Group: ';
                        if ($row['is_Admin_Group'] == 1) {
                            echo 'Admin';
                        } elseif ($row['is_Ancillary_Group'] == 1) {
                            echo 'Ancillary';
                        } elseif ($row['is_Nursing_Group'] == 1) {
                            echo 'Nursing';
                        } elseif ($row['is_Outsource_Group'] == 1) {
                            echo 'Outsource';
                        }
    echo            '</p>';
    echo        '</div>'; // end of mb-3

    echo        '<div class="mb-3">';
    echo            '<p class="form-label">Role: '.$row['UserRoleName'].'</p>';
                    
    echo        '</div>'; // end of mb-3
    
    echo    '</div>'; //end of 1st col-md-6

    echo    '<div class="col-md-6">'; //1st col
    echo        '<div class="mb-3">';
    echo            '<p  class="form-label">Department: ' . $row['DepartmentName'] . '</p>';
    echo        '</div>'; // end of mb-3


    
    echo    '</div>'; //end of 1st col-md-6


    echo '</div>'; //end of 1st row













} else {
  echo 'User not found';
}
?>

