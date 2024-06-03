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

} else {
    echo 'User not found';
}
?>



<form class="row g-3 pt-3 needs-validation" novalidate enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">

            <input type="hidden" value="<?php echo $row['UserID']?>" name ="UserID">
            <div class="mb-3">
                <label for="IdNumber" class="form-label">ID</label>
                <input type="number" class="form-control" id="IdNumber" required name="IdNumber"
                    value="<?php echo $row['IdNumber'] ?>">
                <div class="invalid-feedback">Please provide User ID.</div>
            </div>
            <div class="mb-3">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstName" required name="firstName"
                    value="<?php echo $row['Fname'] ?>">
                <div class="invalid-feedback">Please provide a first name.</div>
            </div>
            <div class="mb-3">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastName" required name="lastName"
                    value="<?php echo $row['Lname'] ?>">
                <div class="invalid-feedback">Please provide a last name.</div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" required name="gender">
                            <option value="">Select Gender</option>
                            <option value="male" <?php echo ($row['Gender'] == 'Male') ? 'selected' : ''; ?>>Male
                            </option>
                            <option value="female" <?php echo ($row['Gender'] == 'Female') ? 'selected' : ''; ?>>
                                Female
                            </option>
                        </select>
                        <div class="invalid-feedback">Please select a gender.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="birthday" class="form-label">Birthday</label>
                        <input type="date" class="form-control" id="birthday" required name="birthday"
                            value="<?php echo $row['Birthday'] ?>">
                        <div class="invalid-feedback">Please input birthday.</div>
                    </div>
                </div>
            </div>



        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <div class="d-flex justify-content-center">
                    <div class="container">
                        <div id="dropArea" class="text-center" ondrop="handleDrop(event)"
                            ondragover="handleDragOver(event)">
                            <img src="DataAdd/uploads/<?php echo $row['ProfilePhoto'] ?>" alt="profile" width="200"
                                height="200">
                        </div>
                        <input type="file" id="fileInput" onchange="handleFileSelect(event)" name="profile"
                            class="form-control mt-3">

                    </div>
                </div>

            </div>

        </div>
    </div>
    <div class="mb-3">
        <label for="validationCustom01" class="form-label">Address</label>
        <div class="input-group">
            <?php
            // Assuming $row['Address'] contains the full address string
            $addressParts = explode(",", $row['Address']); // Split the address into parts
            $firstPart = explode(" ", $addressParts[0]);

            // trim() function removes any leading or trailing whitespace
            ?>
            <input type="text" class="form-control" id="houseNumber" placeholder="House Number"
                aria-label="House Number" name="houseNumber" required value="<?php echo trim($firstPart[0]); ?>">
            <input type="text" class="form-control" id="streetName" placeholder="Street Name" aria-label="Street Name"
                name="streetName" required value="<?php echo trim($firstPart[1]); ?>">
            <input type="text" class="form-control" id="barangay" placeholder="Barangay" aria-label="Barangay"
                name="barangay" required value="<?php echo trim($addressParts[1]); ?>">
            <div class="invalid-feedback">
                Please provide a complete address.
            </div>
        </div>
        <br>
        <div class="input-group">
            <input type="text" class="form-control" id="cityMunicipality" placeholder="City/Municipality"
                aria-label="City/Municipality" name="city" required value="<?php echo trim($addressParts[2]); ?>">
            <input type="text" class="form-control" id="province" placeholder="Province" aria-label="Province"
                name="province" required value="<?php echo trim($addressParts[3]); ?>">
            <div class="invalid-feedback">
                Please provide a complete address.
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Left side -->
        <div class="col-md-6">
            <!-- Contact Number -->

            <div class="mb-3">
                <label for="contactNumber" class="form-label">Contact Number</label>
                <input type="tel" class="form-control" id="contactNum" required pattern="[0-9]{11}"
                    placeholder="Enter a valid 11-digit contact number" name="contactNum"
                    value="<?php echo $row['ContactNumber'] ?>">
                <div class="invalid-feedback" id="contactNumError">Please provide a valid 11-digit contact number.
                </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" required name="email"
                    value="<?php echo $row['Email'] ?>">
                <div class="invalid-feedback">Please provide a valid email address.</div>
            </div>

            <!-- Username -->
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" required name="username"
                    value="<?php echo $row['Username'] ?>">
                <div class="invalid-feedback">Please provide a username.</div>
            </div>




        </div>
        <!-- Right side -->

        <div class="col-md-6">

            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <select class="form-select" aria-label="Default select example" name="BaypointeDepartmentID"
                    id="departmentSelect" required>
                    <option value="">Select Department</option>
                    <?php
                    require ('../config/db_con.php');

                    // Retrieve the selected department ID (for example, from a form submission or URL parameter)
                    $selectedDepartmentID = $row['BaypointeDepartmentID']; // Replace 'selected_department_id' with the actual parameter name you're using
                    
                    // Fetch department data from the database
                    $query = "SELECT * FROM baypointedepartments";
                    $result = mysqli_query($conn, $query);

                    // Check if there are any departments
                    if (mysqli_num_rows($result) > 0) {
                        // Output data of each row
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Output an option for each department
                            $selected = ($row['BaypointeDepartmentID'] == $selectedDepartmentID) ? 'selected' : ''; // Check if the department is selected
                            echo '<option value="' . $row['BaypointeDepartmentID'] . '" ' . $selected . '>' . $row['DepartmentName'] . '</option>';
                        }
                    } else {
                        echo '<option disabled>No departments found</option>';
                    }
                    ?>



                </select>
                <div class="invalid-feedback">Please select a department.</div>


            </div>

            <!-- Department -->
            <div class="mb-3">
                <label for="group" class="form-label">Group</label>
                <?php
                $userid = $_POST['userid'];

                // Query to fetch user data
                $table = mysqli_query($conn, "SELECT u.*, u.Password, usr.UserRoleName, bd.DepartmentName FROM users u   
                            INNER JOIN userroles usr ON u.UserRoleID = usr.UserRoleID 
                            INNER JOIN baypointedepartments bd ON u.BaypointeDepartmentID = bd.BaypointeDepartmentID
                            WHERE u.Active = 1 AND u.UserID = $userid");


                ?>
                <select class="form-select" aria-label="Default select example" name="group" required id="groupSelect">
                    <?php
                    // Fetch data and generate HTML
                    
                    // Assuming $table is your MySQL result object
                    $row = mysqli_fetch_assoc($table);

                    if ($row) {
                        echo '<option value="">Select Group</option>
                       <option value="Admin"' . ($row['is_Admin_Group'] == '1' ? ' selected' : '') . '>Admin</option>
                       <option value="Ancillary" ' . ($row['is_Ancillary_Group'] == '1' ? ' selected' : '') . '>Ancillary</option>
                       <option value="Nursing" ' . ($row['is_Nursing_Group'] == '1' ? ' selected' : '') . '>Nursing</option>
                       <option value="EXECOM" ' . ($row['is_EXECOM_Group'] == '1' ? ' selected' : '') . '>EXECOM</option>';
                    } else {
                        echo 'User not found';
                    }
                    ?>




                </select>
                <div class="invalid-feedback">Please select a group.</div>
            </div>

            <!-- User Role -->
            <div class="mb-3">
                <label for="userRole" class="form-label">User Role</label>
                <?php
                $userid = $_POST['userid'];

                // Query to fetch user data
                $table = mysqli_query($conn, "SELECT u.*, u.Password, usr.UserRoleName, bd.DepartmentName FROM users u   
                            INNER JOIN userroles usr ON u.UserRoleID = usr.UserRoleID 
                            INNER JOIN baypointedepartments bd ON u.BaypointeDepartmentID = bd.BaypointeDepartmentID
                            WHERE u.Active = 1 AND u.UserID = $userid");


                ?>
                <select class="form-select" aria-label="Default select example" name="role" required id="roleSelect">
                    <option value="">Select User Role</option>
                    <?php
                    // Fetch data and generate HTML
                    
                    // Assuming $table is your MySQL result object
                    $row = mysqli_fetch_assoc($table);

                    if ($row) {
                        echo '<option value="0"' . ($row['UserRoleID'] == '0' ? ' selected' : '') . '>Super Admin</option>';
                        echo '<option value="1"' . ($row['UserRoleID'] == '1' ? ' selected' : '') . '>Admin</option>';
                        echo '<option value="2"' . ($row['UserRoleID'] == '2' ? ' selected' : '') . '>User</option>';
                        echo '<option value="3"' . ($row['UserRoleID'] == '3' ? ' selected' : '') . '>Doctors</option>';
                    } else {
                        echo 'User not found';
                    }
                    ?>




                </select>

                <div class="invalid-feedback">Please select a user role.</div>
            </div>
        </div>
    </div>
    <!-- Submit Button -->
    <div class="col-12">
        <div class="d-flex gap-2 justify-content-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" id="updateBtn" class="btn btn-primary" name="updateBtn">Save</button>
        </div>
    </div>
</form>



<!-- Bootstrap JS and jQuery (for toggling) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $(document).ready(function () {
        // Toggle password visibility
        $("#togglePassword").click(function () {
            var passwordField = $("#passwordInput");
            var fieldType = passwordField.attr("type");
            if (fieldType === "password") {
                passwordField.attr("type", "text");
                $("#togglePassword i").removeClass("bi-eye").addClass("bi-eye-slash");
            } else {
                passwordField.attr("type", "password");
                $("#togglePassword i").removeClass("bi-eye-slash").addClass("bi-eye");
            }
        });

        // Toggle confirm password visibility
        $("#toggleConfirmPassword").click(function () {
            var confirmPasswordField = $("#confirmPassword");
            var fieldType = confirmPasswordField.attr("type");
            if (fieldType === "password") {
                confirmPasswordField.attr("type", "text");
                $("#toggleConfirmPassword i").removeClass("bi-eye").addClass("bi-eye-slash");
            } else {
                confirmPasswordField.attr("type", "password");
                $("#toggleConfirmPassword i").removeClass("bi-eye-slash").addClass("bi-eye");
            }
        });
    });
</script>


<script>
    $(document).ready(function () {
        $('#updateBtn').click(function (e) {
            var form = $('.needs-validation')[0];

            if (form.checkValidity()) {
                var formData = new FormData(form);
                var loading = Swal.fire({
                    title: 'Please wait',
                    html: 'Submitting your data...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: 'DataGet/DataUpdate/edit_user.php', // Modify the URL to point to your PHP script
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        loading.close();
                        // Handle the response from the server
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response,
                            onClose: () => {
                                // Redirect or reload the page after successful update
                                window.location.reload();
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        loading.close();
                        // Handle errors
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating user information. Please try again.'
                        });
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });
</script>




<!-- Udpate data -->
<!-- <script>
    $(document).ready(function () {
        $('#updateBtn').click(function (e) {
            e.preventDefault(); // Prevent the default form submission behavior
            var form = $('.needs-validation')[0];
            form.classList.add('was-validated'); // Add 'was-validated' class to show validation errors

            // Check if the form is valid
            if (form.checkValidity() === true) {
                var formData = new FormData(form); // Create FormData object from the form

                $.ajax({
                    type: 'POST',
                    url: 'DataGet/DataUpdate/edit_user.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        // Handle success response
                        console.log(response);
                    },
                    error: function (xhr, status, error) {
                        // Handle error
                        console.error(xhr.responseText);
                    }
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please fill in all required fields.'
                });
            }
        });
    });

    console.log('It\'s working'); // Moved outside of the document ready block
</script> -->