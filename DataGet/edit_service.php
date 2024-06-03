<?php
require ('../config/db_con.php');

// Assuming $_POST['serviceid'] contains the user ID sent from the client-side
$serviceid = $_POST['serviceid'];

// Query to fetch user data
$table = mysqli_query($conn, "SELECT s.*, ds.* FROM services s  
    INNER JOIN doctors ds ON s.Doctors = ds.DoctorID
    WHERE s.Active = 1 AND s.ServiceID = $serviceid");

// Fetch data and generate HTML
if ($row = mysqli_fetch_assoc($table)) {

} else {
    echo 'Service not found';
}
?>


<form id="serviceForm" novalidate enctype="multipart/form-data">
<input type="hidden" value="<?php echo $row['ServiceID'] ?>" name="ServiceID" id="ServiceID">
    <div class="mb-3">
        <div class="row">
            <div class="col">
                <label for="titleInput" class="form-label">Service Title</label>
                <input type="text" class="form-control" id="servTitle1" aria-label="Service Title" name="servTitle1"
                    value="<?php echo $row['Title'] ?>" required>
                <span id="serviceError1" class="text-danger"></span>
            </div>
        </div>

    </div>

    <div class="mb-3">
        <div class="row g-0 align-items-center"> <!-- Add align-items-center to vertically align content -->
            <div class="col-md-4">
                <img id="imagePreview" src="DataAdd/uploads/<?php echo $row['ImageService'] ?>"
                    class="img-fluid rounded-start" alt="..." style="width: 180px; height: auto;">
                <!-- Set a fixed width for the image -->
            </div>
            <div class="col-md-8">
                <label for="ImageInput1" class="form-label">Image</label>
                <input class="form-control" type="file" id="ImageInput1" name="Image" required
                    onchange="previewImage('ImageInput1', 'imagePreview')">
                <span id="ImageError1" class="text-danger"></span>
                <!-- Error message element for image -->
                <?php if (isset($row['Image'])): ?>
                    <input type="hidden" id="imageName" value="<?php echo $row['ImageService']; ?>">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <div class="row g-0 align-items-center"> <!-- Add align-items-center to vertically align content -->
            <div class="col-md-4">
                <img id="iconPreview" src="DataAdd/uploads/<?php echo $row['Icon'] ?>" class="img-fluid rounded-start"
                    alt="..." style="width: 180px; height: auto;">
                <!-- Set a fixed width for the image -->
            </div>
            <div class="col-md-8">
                <label for="iconInput1" class="form-label">Icon</label>
                <input class="form-control" type="file" id="iconInput1" name="icon" required
                    onchange="previewImage('iconInput1', 'iconPreview')">
                <span id="iconError1" class="text-danger"></span>
                <!-- Error message element for icon -->
                <?php if (isset($row['icon'])): ?>
                    <input type="hidden" id="iconName" value="<?php echo $row['Icon']; ?>">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // After the document is loaded, set the value of the input fields to the filenames from the database
        document.addEventListener("DOMContentLoaded", function () {
            const imageName = document.getElementById('imageName');
            const iconName = document.getElementById('iconName');

            if (imageName) {
                document.getElementById('ImageInput1').value = imageName.value;
            }

            if (iconName) {
                document.getElementById('iconInput1').value = iconName.value;
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
        <label for="descInput2" class="form-label">Description</label>
        <textarea class="form-control" rows="3" name="description"
            id="descInput2"><?php echo $row['Description'] ?></textarea>
        <span id="descError2" class="text-danger"></span>
    </div>

    <div class="mb-3">
        <label for="servicesInput1" class="form-label">Services</label>
        <textarea class="form-control" rows="3" name="services"
            id="servicesInput1"><?php echo $row['Services'] ?></textarea>
        <span id="servicesError1" class="text-danger"></span>
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
    $serviceid = $_POST['serviceid'];

    // Query to fetch user data
    $table = mysqli_query($conn, "SELECT s.*, ds.* FROM services s  
        INNER JOIN doctors ds ON s.Doctors = ds.DoctorID
        WHERE s.Active = 1 AND s.ServiceID = $serviceid");

    // Fetch data and generate HTML
    if ($row = mysqli_fetch_assoc($table)) {

    } else {
        echo 'Department not found';
    }
    ?>

    <div class="mb-3">
        <label for="contactNum1" class="form-label">Contact Number</label>
        <div class="input-group">
            <span class="input-group-text"><i class="ri-phone-fill"></i></span>
            <input type="text" id="contactNum1" class="form-control" name="contactNum1" placeholder="09 XXXX XXXX"
                value="<?php echo $row['ContactNumber'] ?>">
            <span id="contactNumError1" class="text-danger"></span>
        </div>
        <textarea class="form-control mt-2" rows="3" name="contactInfo" id="contactInput1"
            placeholder="Enter Information details"><?php echo $row['Contact_Details'] ?></textarea>
        <span id="contactInfoError1" class="text-danger"></span>
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

            const ServiceID = document.getElementById('ServiceID').value.trim();

            // Title Validation
            const serviceTitle = document.getElementById('servTitle1').value.trim();
            const serviceTitleInput = document.getElementById('servTitle1');
            const errorElement = document.getElementById('serviceError1');

            // Image Upload Validation
            const ImageInput1 = document.getElementById('ImageInput1');
            const iconInput1 = document.getElementById('iconInput1');
            const ImageError1 = document.getElementById('ImageError1');
            const iconError1 = document.getElementById('iconError1');

            const Image1 = ImageInput1.files[0];
            const iconImage = iconInput1.files[0];

            if (Image1) {
                const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i; // Add more if needed
                if (!allowedExtensions.exec(Image1.name)) {
                    ImageError1.textContent = "Invalid file type. Please upload an image with .jpg, .jpeg, or .png extension.";
                    return;
                } else {
                    ImageError1.textContent = ""; // Clear error message if valid image is selected
                }
            }

            if (iconImage) {
                const allowedIconExtensions = /(\.svg|\.psd|\.png|\.eps)$/i;
                if (!allowedIconExtensions.exec(iconImage.name)) {
                    iconError1.textContent = "Invalid file type. Please upload an image with .svg, .psd, .png, or .eps extension.";
                    return;
                } else {
                    iconError1.textContent = ""; // Clear error message if valid image is selected
                }
            }

            // Description and Services Input Validation
            const descInput2 = document.getElementById('descInput2');
            const servicesInput1 = document.getElementById('servicesInput1');
            const descError2 = document.getElementById('descError2');
            const servicesError1 = document.getElementById('servicesError1');

            // Doctor Selection Validation
            const doctorSelect1 = document.getElementById('doctorSelect1');
            const doctorError1 = document.getElementById('doctorError1');
            const selectedDoctor = doctorSelect1.value;

            // Contact Information Input Validation
            const contactNum1 = document.getElementById('contactNum1');
            const contactNumError1 = document.getElementById('contactNumError1');
            const contactnumber = contactNum1.value.trim();
            const contactnumberpattern = /^\d{10}$/;

            const contactInput1 = document.getElementById('contactInput1');
            const contactInfoError1 = document.getElementById('contactInfoError1');
            const contactInfo = contactInput1.value.trim();

            if (!serviceTitle) {
                errorElement.textContent = "Service Title is required";
                serviceTitleInput.style.borderColor = "red";
                return;
            } else {
                errorElement.textContent = ""; // Clear error message if service title is provided
                serviceTitleInput.style.borderColor = ""; // Reset border color
            }

            if (!descInput2.value.trim()) {
                descError2.textContent = "Description is required";
                return;
            } else {
                descError2.textContent = ""; // Clear error message if description is provided
            }

            if (!servicesInput1.value.trim()) {
                servicesError1.textContent = "Services are required";
                return;
            } else {
                servicesError1.textContent = ""; // Clear error message if services are provided
            }

            if (!selectedDoctor) {
                doctorError1.textContent = "Please select a doctor";
                return;
            } else {
                doctorError1.textContent = ""; // Clear error message if doctor is selected
            }

            if (!contactnumber) {
                contactNumError1.textContent = "Contact number is required";
                return;
            } else if (!contactnumberpattern.test(contactnumber)) {
                contactNumError1.textContent = "Invalid contact number format";
                return;
            } else {
                contactNumError1.textContent = ""; // Clear error message if valid contact number is entered
            }

            if (!contactInfo) {
                contactInfoError1.textContent = "Contact information is required";
                return;
            } else {
                contactInfoError1.textContent = ""; // Clear error message if contact information is provided
            }

            var formData = new FormData();
            formData.append('serviceTitle', serviceTitle);
            formData.append('Image1', Image1);
            formData.append('iconImage', iconImage);
            formData.append('description', descInput2.value.trim());
            formData.append('services', servicesInput1.value.trim());
            formData.append('doctorID', selectedDoctor);
            formData.append('contactNum1', contactNum1.value.trim());
            formData.append('contactInfo', contactInfo);
            formData.append('ServiceID', ServiceID);

            $.ajax({
                type: 'POST',
                url: 'DataGet/DataUpdate/update_service.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    response = JSON.parse(response);
                    toastr.clear(); // Clear all toastr messages
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.success,
                            showConfirmButton: false,
                        }).then(function () {
                            window.location.href = 'service.php';
                        });
                    } else if (response.error) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: response.error,
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while submitting data.',
                    });
                }
            });
        });
    });
</script>
