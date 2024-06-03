<?php
require ('../config/db_con.php');

// Assuming $_POST['categoryid'] contains the user ID sent from the client-side
$categoryid = $_POST['categoryid'];

// Query to fetch user data
$table = mysqli_query($conn, "SELECT c.* FROM categories c    
            WHERE c.Active = 1 AND c.CategoryID = $categoryid");

// Fetch data and generate HTML
if ($row = mysqli_fetch_assoc($table)) {

} else {
    echo 'Category not found';
}
?>


<form id="categoryForm" class="needs-validation">
    <input type="hidden" value="<?php echo $row['CategoryID'] ?>" name="CategoryID" id="categoryID">
    <div class="mb-3">
        <label for="categoryName1" class="form-label">Category Name</label>
        <input type="text" class="form-control" id="categoryName1" value="<?php echo $row['CategoryName'] ?>">
        <span id="categoryError1" class="text-danger"></span> <!-- Error message element -->
    </div>
    <button type="button" class="btn btn-primary" id="updateBtn">Submit</button>
</form>




<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- <script>
    $(document).ready(function () {
        $('#updateBtn').click(function (e) {
            e.preventDefault(); // prevent the form from submitting
            

           
            var categoryName = $('#categoryName1').val().trim(); // Use consistent ID 'categoryName'
            const categoryInput = document.getElementById('categoryName1'); // Correct ID
            const errorElement = document.getElementById('categoryError1'); // Correct ID

            // Regular expression to check if the categoryName contains any special characters or numbers
            var specialChars = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
            var numbers = /\d/;

            if (!categoryName.trim()) {
                errorElement.innerText = "Category name is required";
                categoryInput.style.borderColor = "red";
            } else if (specialChars.test(categoryName) || numbers.test(categoryName)) {
                errorElement.innerText = "Special characters and numbers are not allowed";
                categoryInput.style.borderColor = "red";
            } else {
                errorElement.innerText = ""; // Clear error message if category name is provided
                categoryInput.style.borderColor = ""; // Reset border color

                // Perform AJAX request to send form data and retrieve CategoryID
                $.ajax({
                    type: "POST",
                    url: "DataGet/DataAdd2/update_category.php", // Replace with your PHP script URL
                    data: ,
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
        });
    });
</script> -->


<script>
    $(document).ready(function () {
        $('#updateBtn').click(function (e) {
            e.preventDefault(); // prevent the form from submitting
            var categoryName = $('#categoryName1').val().trim();
            var categoryID = $('#categoryID').val(); // Retrieve CategoryID
            const categoryInput = document.getElementById('categoryName1');
            const errorElement = document.getElementById('categoryError1');

            var specialChars = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
            var numbers = /\d/;

            if (!categoryName.trim()) {
                errorElement.innerText = "Category name is required";
                categoryInput.style.borderColor = "red";
            } else if (specialChars.test(categoryName) || numbers.test(categoryName)) {
                errorElement.innerText = "Special characters and numbers are not allowed";
                categoryInput.style.borderColor = "red";
            } else {
                errorElement.innerText = "";
                categoryInput.style.borderColor = "";

                // Prepare data to be sent via AJAX
                var formData = new FormData();
                formData.append('CategoryID', categoryID);
                formData.append('CategoryName', categoryName);

                $.ajax({
                    type: "POST",
                    url: "DataGet/DataUpdate/update_category.php",
                    data: formData, // Send form data
                    processData: false,
                    contentType: false,
              
                    success: function (response) {
                        Swal.fire({
                                title: "Success",
                                text: "Udpated success fully",
                                icon: "success",
                                showConfirmButton: false,
                                allowOutsideClick: false
                            });

                            setTimeout(function () {
                                location.reload();
                            }, 1500);
                       
                    },
                    error: function (xhr, status, error) {
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


 