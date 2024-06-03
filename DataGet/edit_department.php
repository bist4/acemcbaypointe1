<?php
require ('../config/db_con.php');

// Assuming $_POST['departmentid'] contains the user ID sent from the client-side
$departmentid = $_POST['departmentid'];

// Query to fetch user data
$table = mysqli_query($conn, "SELECT d.*, ds.* FROM departments d  
    INNER JOIN doctors ds ON d.Doctors = ds.DoctorID
    WHERE d.Active = 1 AND d.DepartmentID = $departmentid");

// Fetch data and generate HTML
if ($row = mysqli_fetch_assoc($table)) {

} else {
    echo 'Department not found';
}
?>


<form id="departmentForm" novalidate enctype="multipart/form-data">
    <input type="hidden" value="<?php echo $row['DepartmentID'] ?>" name="DepartmentID" id="DepartmentID">
    <div class="mb-3">
        <div class="row">
            <div class="col">
                <label for="titleInput" class="form-label">Department Title</label>
                <input type="text" class="form-control" id="depTitle1" aria-label="Department Title" name="deptTitle"
                    required value="<?php echo $row['Title'] ?>">
                <span id="departmentError1" class="text-danger"></span>
                <!-- Error message element -->
            </div>
        </div>

    </div>

    <div class="mb-3">
        <div class="row g-0 align-items-center"> <!-- Add align-items-center to vertically align content -->
            <div class="col-md-4">
                <img id="frontImagePreview" src="DataAdd/uploads/<?php echo $row['FrontImage'] ?>"
                    class="img-fluid rounded-start" alt="..." style="width: 180px; height: auto;">
                <!-- Set a fixed width for the image -->
            </div>
            <div class="col-md-8">
                <label for="frontImageInput" class="form-label">Front Image</label>
                <input class="form-control" type="file" id="frontImageInput1" name="frontImage" required
                    onchange="previewImage('frontImageInput1', 'frontImagePreview')">
                <span id="frontImageError1" class="text-danger"></span>
                <!-- Error message element for front image -->
                <?php if (isset($row['FrontImage'])): ?>
                    <input type="hidden" id="frontImageName" value="<?php echo $row['FrontImage']; ?>">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <div class="row g-0 align-items-center"> <!-- Add align-items-center to vertically align content -->
            <div class="col-md-4">
                <img id="backImagePreview" src="DataAdd/uploads/<?php echo $row['BackImage'] ?>"
                    class="img-fluid rounded-start" alt="..." style="width: 180px; height: auto;">
                <!-- Set a fixed width for the image -->
            </div>
            <div class="col-md-8">
                <label for="backImageInput" class="form-label">Back Image</label>
                <input class="form-control" type="file" id="backImageInput1" name="backImage" required
                    onchange="previewImage('backImageInput1', 'backImagePreview')">
                <span id="backImageError1" class="text-danger"></span>
                <!-- Error message element for front image -->
                <?php if (isset($row['BackImage'])): ?>
                    <input type="hidden" id="backImageName" value="<?php echo $row['BackImage']; ?>">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // After the document is loaded, set the value of the input fields to the filenames from the database
        document.addEventListener("DOMContentLoaded", function () {
            const frontImageName = document.getElementById('frontImageName');
            const backImageName = document.getElementById('backImageName');

            if (frontImageName) {
                document.getElementById('frontImageInput1').value = frontImageName.value;
            }

            if (backImageName) {
                document.getElementById('backImageInput1').value = backImageName.value;
            }
        });

        function previewImage(inputId, imageId) {
            const input = document.getElementById(inputId);
            const image = document.getElementById(imageId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    image.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }
    </script>


    <div class="mb-3">
        <label for="descInput" class="form-label">Description</label>
        <textarea class="form-control" id="descInput1" rows="3"
            name="description"><?php echo $row['Description'] ?></textarea>
        <span id="descriptionError1" class="text-danger"></span>
        <!-- Error message element for description -->
    </div>

    <div class="mb-3">
        <label for="servicesInput" class="form-label">Services</label>
        <textarea class="form-control" id="servicesInput1" rows="3"
            name="services"><?php echo $row['Services'] ?></textarea>
        <span id="servicesError1" class="text-danger"></span>
        <!-- Error message element for services -->
    </div>


    <div class="mb-3">
        <label for="doctorInput" class="form-label">Doctor</label>
        <select class="form-select" aria-label="Default select example" name="DoctorID" id="doctorSelect1" required>
            <option value="">Select Doctors</option>
            <?php
            require ('../config/db_con.php');

            $selectedDoctor = $row['Doctors']; // Assuming $row['Doctors'] contains the selected doctor's ID
            
            // Fetch doctor data from the database
            $query = "SELECT * FROM doctors WHERE Active = 1";
            $result = mysqli_query($conn, $query);

            // Check if there are any doctors
            if (mysqli_num_rows($result) > 0) {
                // Output data of each row
                while ($row = mysqli_fetch_assoc($result)) {
                    // Output an option for each doctor
                    $selected = ($row['DoctorID'] == $selectedDoctor) ? 'selected' : ''; // Assuming the correct column name is DoctorID
                    echo '<option value="' . $row['DoctorID'] . '" ' . $selected . '>Dr. ' . $row['Name'] . '</option>';
                }
            } else {
                echo '<option disabled>No doctors found</option>';
            }
            ?>
        </select>
        <span id="doctorError1" class="text-danger"></span>
        <!-- Error message element for doctor selection -->
    </div>


    <?php
    require ('../config/db_con.php');

    // Assuming $_POST['departmentid'] contains the user ID sent from the client-side
    $departmentid = $_POST['departmentid'];

    // Query to fetch user data
    $table = mysqli_query($conn, "SELECT d.*, ds.* FROM departments d  
        INNER JOIN doctors ds ON d.Doctors = ds.DoctorID
        WHERE d.Active = 1 AND d.DepartmentID = $departmentid");

    // Fetch data and generate HTML
    if ($row = mysqli_fetch_assoc($table)) {

    } else {
        echo 'Department not found';
    }
    ?>
    <div class="mb-3">
        <label for="emailInput" class="form-label">Email</label>
        <input type="email" class="form-control" id="emailInput1" name="email" required
            value="<?php echo $row['Email'] ?>">
        <span id="emailError1" class="text-danger"></span> <!-- Error message element for email -->
    </div>


    <div class="col-12">
        <div class="d-flex gap-2 justify-content-end">
            <a id="closeBtn" class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
            <button type="button" class="btn btn-primary" id="updateBtn">Submit</button>
        </div>
    </div>
</form>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<!--Edit data -->
<script>
    $(document).ready(function () {
        $('#updateBtn').click(function (e) {
            e.preventDefault();

            // Title Validation
            const departmentTitle = document.getElementById('depTitle1').value.trim();
            const departmentInput = document.getElementById('depTitle1');
            const errorElement = document.getElementById('departmentError1');

            const DepartmentID = document.getElementById('DepartmentID').value.trim();
             


            // Description and Services Input Validation
            const descInput = document.getElementById('descInput1');
            const servicesInput = document.getElementById('servicesInput1');
            const descError = document.getElementById('descriptionError1');
            const servicesError = document.getElementById('servicesError1');



            // Description Validation
            const description = descInput.value.trim();
            // Services Validation
            const services = servicesInput.value.trim();

            // Doctor Selection Validation
            const doctorSelect = document.getElementById('doctorSelect1');
            const doctorError = document.getElementById('doctorError1');
            const selectedDoctor = doctorSelect.value;

            // Email Input Validation
            const emailInput = document.getElementById('emailInput1');
            const emailError = document.getElementById('emailError1');

            const email = emailInput.value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

             // If front image is uploaded, perform validation
        const frontImageInput = document.getElementById('frontImageInput1');
        const frontImageError = document.getElementById('frontImageError1');
        const frontImage = frontImageInput.files[0];
        if (frontImage) {
            const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i; // Add more if needed
            if (!allowedExtensions.exec(frontImage.name)) {
                frontImageError.textContent = "Invalid file type. Please upload an image with .jpg, .jpeg, or .png extension.";
                return;
            } else {
                frontImageError.textContent = ""; // Clear error message if valid image is selected
            }
        }

        // If back image is uploaded, perform validation
        const backImageInput = document.getElementById('backImageInput1');
        const backImageError = document.getElementById('backImageError1');
        const backImage = backImageInput.files[0];
        if (backImage) {
            const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i; // Add more if needed
            if (!allowedExtensions.exec(backImage.name)) {
                backImageError.textContent = "Invalid file type. Please upload an image with .jpg, .jpeg, or .png extension.";
                return;
            } else {
                backImageError.textContent = ""; // Clear error message if valid image is selected
            }
        }

            if (!departmentTitle) {
                errorElement.textContent = "Department title is required";
                departmentInput.style.borderColor = "red";
                return;
            }
            else {
                errorElement.textContent = ""; // Clear error message if category name is provided
                departmentInput.style.borderColor = ""; // Reset border color

            }
            var regex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?0-9]/;
            if (regex.test(departmentTitle)) {
                // Display error message
                document.getElementById('departmentError1').textContent = "Special characters and numbers are not allowed.";
            } else {
                if (!description) {
                    descError.textContent = "Description is required";
                    return;
                } else {
                    descError.textContent = ""; // Clear error message if description is provided
                    // Services Validation
                    if (!services) {
                        servicesError.textContent = "Services are required";
                        return;
                    } else {
                        servicesError.textContent = ""; // Clear error message if services are provided
                        if (!selectedDoctor) {
                            doctorError.textContent = "Please select a doctor";
                            return;
                        } else {
                            doctorError.textContent = ""; // Clear error message if doctor is selected
                            if (!email) {
                                emailError.textContent = "Email is required";
                                return;
                            } else if (!emailPattern.test(email)) {
                                emailError.textContent = "Invalid email format";
                                return;
                            } else {
                                emailError.textContent = ""; // Clear error message if valid email is entered

                                // Create FormData object
                                var formData = new FormData();

                                // Append form data to FormData object
                                formData.append('departmentTitle', departmentTitle);
                                formData.append('description', description);
                                formData.append('services', services);
                                formData.append('doctorID', selectedDoctor);
                                formData.append('email', email);
                                formData.append('frontImage', frontImage);
                                formData.append('backImage', backImage);
                                formData.append('DepartmentID', DepartmentID);

                                //Now if there's no error proceed to add in the database
                                $.ajax({
                                    url: 'DataGet/DataUpdate/update_dep.php',
                                    method: 'POST',
                                    data: formData,
                                    processData: false,  // Prevent jQuery from processing data
                                    contentType: false,  // Prevent jQuery from setting contentType
                                    dataType: 'json',
                                    success: function (response) {
                                        if (response.success) {
                                            Swal.fire({
                                                title: "Success",
                                                text: response.success,
                                                icon: "success",
                                                showConfirmButton: false,
                                                allowOutsideClick: false
                                            });
                                            $('#addDepartmentModal').modal('hide');
                                            // Reload the page after a short delay
                                            setTimeout(function () {
                                                location.reload();
                                            }, 1500);

                                        } else {
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Warning',
                                                text: response.error,
                                            });
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        // Handle AJAX error, if any
                                        console.error(xhr.responseText);
                                    }
                                });
                            }

                        }

                    }
                         
                     
                }




            }
        });
    });

</script>