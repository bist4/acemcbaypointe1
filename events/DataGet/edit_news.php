<?php
require ('../../config/db_con.php');

// Assuming $_POST['newsID'] contains the user ID sent from the client-side
$newsID = $_POST['newsID'];

// Query to fetch user data
$table = mysqli_query($conn, "SELECT n.* FROM news n    
            WHERE n.Active = 1 AND n.NewsID = $newsID");

// Fetch data and generate HTML
if ($row = mysqli_fetch_assoc($table)) {

} else {
    echo 'Event not found';
}
?>

 
<form id="eventForm" novalidate enctype="multipart/form-data">
    <input type="hidden" value="<?php echo $row['NewsID'] ?>" name="newsID" id="newsID">
    <div class="mb-3">
        <label for="eventTitle1" class="form-label">News Title</label>
        <input type="text" class="form-control" id="eventTitle1" value="<?php echo $row['Title_News'] ?>">
        <span id="titleError1" class="text-danger"></span> <!-- Error message element -->
    </div>
    <div class="mb-3">
        <label for="eventDesc1" class="form-label">News Description</label>
        <textarea name="eventDesc1" id="eventDesc1" class="form-control"><?php echo $row['Description_News'] ?></textarea>
        <span id="descError1" class="text-danger"></span> <!-- Error message element -->
    </div>
    
    <div class="mb-3">
        <div class="row g-0 align-items-center"> <!-- Add align-items-center to vertically align content -->
            <div class="col-md-4">
                <img id="image1Preview" src="DataAdd/uploads/<?php echo $row['Image_News'] ?>"
                    class="img-fluid rounded-start" alt="..." style="width: 180px; height: auto;">
                <!-- Set a fixed width for the image -->
            </div>
            <div class="col-md-8">
                <label for="image1" class="form-label">News Image</label>
                <input class="form-control" type="file" id="image1Input" name="image1" required
                    onchange="previewImage('image1Input', 'image1Preview')">
                <span id="image1Error1" class="text-danger"></span>
                <!-- Error message element for front image -->
                <?php if (isset($row['FrontImage'])): ?>
                    <input type="hidden" id="image1ImageName" value="<?php echo $row['Image_News']; ?>">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <button type="button" class="btn btn-primary" id="updateBtn">Submit</button>
</form>

<script>
        // After the document is loaded, set the value of the input fields to the filenames from the database
        document.addEventListener("DOMContentLoaded", function () {
            const image1ImageName = document.getElementById('image1ImageName');
           

            if (image1ImageName) {
                document.getElementById('image1Input').value = image1ImageName.value;
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




 <!-- update  -->
 <script>
    $(document).ready(function () {
    $('#updateBtn').click(function (e) {
        e.preventDefault();

        const eventTitle1 = $('#eventTitle1').val().trim();
        const eventDesc1 = $('#eventDesc1').val().trim();
        const image1 = $('#image1Input')[0].files[0];

        const newsID = $('#newsID').val().trim();
        const eventTitleIn1 = $('#eventTitle1');
        const eventDescIn1 = $('#eventDesc1');
        const image1In1 = $('#image1Input');

        const titleError1 = $('#titleError1');
        const descError1 = $('#descError1');
        const image1Error1 = $('#image1Error1');

        let isValid = true;

        // Clear previous error messages
        titleError1.text('');
        descError1.text('');
        image1Error1.text('');
        const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i; // Add more if needed
        var regex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?0-9]/;

        // Validate event title
        if (!eventTitle1) {
            titleError1.text('News title is required');
            eventTitleIn1.css('borderColor', 'red');
            isValid = false;
        } else if (regex.test(eventTitle1)) {
            titleError1.text('Special characters and numbers are not allowed.');
            eventTitleIn1.css('borderColor', 'red');
            isValid = false;
        } else {
            titleError1.text('');
            eventTitleIn1.css('borderColor', '');
            isValid = true;
        }

        // Validate event description
        if (!eventDesc1) {
            descError1.text('News description is required');
            eventDescIn1.css('borderColor', 'red');
            isValid = false;
        } else {
            descError1.text('');
            eventDescIn1.css('borderColor', '');
            isValid = true;
        }

        // Validate image1
        if (!allowedExtensions.exec(image1.name)) {
            image1Error1.text('Invalid file type. Please upload an image with .jpg, .jpeg, or .png extension.');
            image1In1.css('borderColor', "red");
            isValid = false;
        } else {
            image1Error1.text('');
            image1In1.css('borderColor', "");
            isValid = true;
        }

        // If the form is not valid, exit the function
        if (!isValid) {
            return;
        }

        // Prepare form data
        const formData = new FormData();
        formData.append('newsID', newsID);
        formData.append('eventTitle1', eventTitle1);
        formData.append('eventDesc1', eventDesc1);
        formData.append('image1', image1);

        // Send form data to the server using AJAX
        $.ajax({
            url: 'DataUpdate/update_news.php', // Replace with your server-side script URL
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
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

        
    });
});

 </script>