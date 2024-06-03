<?php
require ('../../config/db_con.php');

// Assuming $_POST['titleSlogan'] contains the user ID sent from the client-side
$titleSlogan = $_POST['titleSlogan'];

// Query to fetch user data
$table = mysqli_query($conn, "SELECT * FROM title_slogan WHERE Title_SloganID = $titleSlogan");

// Fetch data and generate HTML
if ($row = mysqli_fetch_assoc($table)) {

} else {
    echo 'Title and Slogan not found';
}
?>


<form id="titleSloganForm" novalidate enctype="multipart/form-data">
<input type="hidden" value="<?php echo $row['Title_SloganID'] ?>" name="Title_SloganID" id="Title_SloganID">
    <div class="row mb-3">
        <label for="webTitle1" class="col-sm-2 col-form-label">Website Title</label>
        <div class="col-sm-10">
            <input id="webTitle1" type="text" class="form-control" name="websiteTitle" required
                value="<?php echo $row['Website_Title'] ?>">
            <span id="webTitleError1" class="text-danger"></span>
        </div>
    </div>
    <div class="row mb-3">
        <label for="slogan1" class="col-sm-2 col-form-label">Slogan</label>
        <div class="col-sm-10">
            <textarea id="slogan1" class="form-control" required
                name="slogan1"><?php echo $row['Slogan'] ?></textarea><!-- End TinyMCE Editor -->
            <span id="sloganError1" class="text-danger"></span>
        </div>
    </div>

    <div class="mb-3">
        <div class="row g-0 align-items-center"> <!-- Add align-items-center to vertically align content -->
            <div class="col-md-4">
                <img id="logoImagePreview" src="DataAdd/uploads/<?php echo $row['Logo'] ?>"
                    class="img-fluid rounded-start" alt="..." style="width: 180px; height: auto;">
                <!-- Set a fixed width for the image -->
            </div>
            <div class="col-md-8">
                <label for="LogoInput" class="form-label">Front Image</label>
                <input class="form-control" type="file" id="LogoInput1" name="Logo" required
                    onchange="previewImage('LogoInput1', 'logoImagePreview')">
                <span id="fileError1" class="text-danger"></span>
                <!-- Error message element for front image -->
                <?php if (isset($row['Logo'])): ?>
                    <input type="hidden" id="LogoName" value="<?php echo $row['Logo']; ?>">
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="d-flex gap-2 justify-content-end">
            <a id="closeBtn" class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
            <button type="button" class="btn btn-primary" id="updateBtn">Submit</button>
        </div>
    </div>
</form><!-- End General Form Elements -->

<script>
    // After the document is loaded, set the value of the input fields to the filenames from the database
    document.addEventListener("DOMContentLoaded", function () {
        const LogoName = document.getElementById('LogoName');


        if (LogoName) {
            document.getElementById('LogoInput1').value = LogoName.value;
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<!-- Edit Data -->
<script>
    $(document).ready(function () {
        $('#updateBtn').click(function (e) {
            e.preventDefault();

            const Title_SloganID = document.getElementById('Title_SloganID').value.trim();
            // Title Validation
            const webTitle1 = $('#webTitle1').val().trim();
            const webTitleInput1 = $('#webTitle1');
            const errorWebTitle1 = $('#webTitleError1');

            // Slogan Validation
            const slogan1 = $('#slogan1').val().trim();
            const sloganInput = $('#slogan1');
            const sloganError1 = $('#sloganError1');

            // File Validation
            const fileInput1 = $('#LogoInput1');
            const fileError1 = $('#fileError1');
            const fileLogo = fileInput1[0].files[0];

            // Reset previous error messages and border colors
            errorWebTitle1.text('');
            webTitleInput1.css('border-color', '');
            sloganError1.text('');
            sloganInput.css('border-color', '');
            fileError1.text('');
            fileInput1.css('border-color', '');

            // Validate title
            if (!webTitle1) {
                errorWebTitle1.text('Web title is required');
                webTitleInput1.css('border-color', 'red');
                return;
            }

            const regex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?0-9]/;

            if (regex.test(webTitle1)) {
                errorWebTitle1.text('Special characters and numbers are not allowed.');
                webTitleInput1.css('border-color', 'red');
                return;
            }

            // Validate slogan
            if (!slogan1) {
                sloganError1.text('Slogan is required');
                sloganInput.css('border-color', 'red');
                return;
            }

            // Validate file
            const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
            if (fileLogo && !allowedExtensions.test(fileLogo.name)) {
                fileError1.text('Invalid file type. Only .jpg, .jpeg, .png are allowed.');
                fileInput1.css('border-color', 'red');
                return;
            } else {
                var formData = new FormData();
                formData.append('webTitle1', webTitle1);
                formData.append('slogan1', slogan1);
                formData.append('fileLogo', fileLogo);
                formData.append('Title_SloganID', Title_SloganID);

                $.ajax({
                    type: 'POST',
                    url: 'DataGet/DataUpdate/update_titleSlogan.php',
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
                                timer: 1000,
                            }).then(function () {
                                window.location.href = 'title_&_slogan.php';
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
                 
            }
        });
    });
</script>