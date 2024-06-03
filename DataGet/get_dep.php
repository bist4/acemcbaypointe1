<?php
require ('../config/db_con.php');
 
$departmentid = $_POST['departmentid'];

// Query to fetch user data
$table = mysqli_query($conn, "SELECT * FROM departments d
            WHERE d.Active = 1 AND d.departmentid = $departmentid");
 
// Fetch data and generate HTML
if ($row = mysqli_fetch_assoc($table)) {

    echo '<div class="row">'; // 1st row
    echo '<div class="col-md-6">'; // 1st col
        echo '<div class="mb-3">';
            echo '<p class="form-label">Title: ' . $row['Title'] . '</p>';
        echo '</div>'; // end of mb-3

        echo '<div class="mb-3">';
            echo '<p class="form-label">Description: ' . $row['Description'] . '</p>';
        echo '</div>'; // end of mb-3

        echo '<div class="mb-3">';
            echo '<p class="form-label">Services: ' . $row['Services'] . '</p>';
        echo '</div>'; // end of mb-3

        echo '<div class="mb-3">';
            echo '<p class="form-label">Email: ' . $row['Email'] . '</p>';
        echo '</div>'; // end of mb-3
    echo '</div>'; // end of 1st col-md-6

    echo '<div class="col-md-6 d-flex justify-content-center">';
        echo '<div class="row">'; // Nested row for images
            echo '<div class="col-md-6">';
            echo 'Front Image';
                echo '<div class="mb-3">';
                    echo '<div class="d-flex justify-content-center">';
                         // Title for Front Image
                        echo '<img width="100px" height="100px" src="DataAdd/uploads/' . $row['FrontImage'] . '">';
                    echo '</div>'; // end of d-flex justify-content-center
                echo '</div>'; // end of mb-3
            echo '</div>'; // end of col-md-6

            echo '<div class="col-md-6">';
            echo 'Back Image';
                echo '<div class="mb-3">';
                    echo '<div class="d-flex justify-content-center">';
                        // Title for Back Image
                        echo '<img width="100px" height="100px" src="DataAdd/uploads/' . $row['BackImage'] . '">';
                    echo '</div>'; // end of d-flex justify-content-center
                echo '</div>'; // end of mb-3
            echo '</div>'; // end of col-md-6
        echo '</div>'; // end of nested row
    echo '</div>'; // end of col-md-6
echo '</div>'; // end of 1st row












} else {
  echo 'User not found';
}
?>

