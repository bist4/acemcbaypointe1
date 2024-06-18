<?php
session_start();
require ('../../config/db_con.php');

$promoID = $_POST['promoID'];

// Query to fetch user data
$table = mysqli_query($conn, "SELECT * FROM promo_and_packages p
            WHERE p.Active = 1 AND p.Promo_and_PackagesID = $promoID");

// Fetch data and generate HTML
if ($row = mysqli_fetch_assoc($table)) {


    echo '<div class="row">'; // 1st row
    echo '<input type="hidden" value='.$row['Promo_and_PackagesID'].' id="promoID">';
    // Column for the image
    echo '<div class="col-md-6 d-flex justify-content-center">'; // 1st col for image
    echo '<div class="mb-3">';
    echo '<div class="d-flex justify-content-center">';
    echo '<img width="100px" height="100px" src="DataAdd/uploads/' . $row['Image_Promo'] . '" alt="Front Image">';
    echo '</div>'; // end of d-flex justify-content-center
    echo '</div>'; // end of mb-3
    echo '</div>'; // end of col-md-6

    // Column for title and description
    echo '<div class="col-md-6">'; // 2nd col for title and description
    echo '<div class="mb-3">';
    echo '<p class="form-label">Title: ' . $row['Title_Promo'] . '</p>';
    echo '</div>'; // end of mb-3

    echo '<div class="mb-3">';
    echo '<p class="form-label">Description: ' . $row['Description_Promo'] . '</p>';
    echo '</div>'; // end of mb-3

    // Buttons for approval and rejection
    echo '<div class="col-12">';

    echo '<div class="d-flex gap-2 justify-content-end">';
    if (isset($_SESSION['UserID'])) {
        // Prepare the SQL query with proper concatenation
        $sql = "SELECT Action_Approved, Action_Reject, ModuleID FROM `privileges` WHERE ModuleID = 21  AND UserID = '" . $_SESSION['UserID'] . "'";

        // Execute the SQL query
        $result = mysqli_query($conn, $sql);

        // Check if query was successful
        if ($result) {
            $row_privileges = mysqli_fetch_assoc($result);
            // Display the buttons based on privileges
            if ($row_privileges['Action_Approved'] == 1) {
                if($row['Status'] == 'APPROVED'){
                    echo '<div data-bs-toggle="tooltip">
                    <button type="button" 
                    data-promo-id="' . $row['Promo_and_PackagesID'] . '"
                    data-author="'.$row['Author_Promo'].'"
                    data-promo-title="'.$row['Title_Promo'].'"
                    class="btn btn-success decline-btn" 
                    id="declineBtn"
                    data-bs-toggle="modal" data-bs-target="#viewEvent">
                    Decline
                    </button>
                    </div>';
                }else{
                   
                    echo '<div data-bs-toggle="tooltip">
                    <button type="button" 
                    data-promo-id="' . $row['Promo_and_PackagesID'] . '"
                    data-author="'.$row['Author_Promo'].'"
                    data-promo-title="'.$row['Title_Promo'].'"
                    class="btn btn-success approve-btn" 
                    id="approveBtn"
                    data-bs-toggle="modal" data-bs-target="#viewEvent">
                    Approve
                    </button>
                    </div>';
                }
              
            }
            if ($row_privileges['Action_Reject'] == 1) {
                if($row['Status'] == 'APPROVED'){
                    echo '';
                }else{
                    echo '<div data-bs-toggle="tooltip">
                    <button type="button" 
                    data-promo-id="' . $row['Promo_and_PackagesID'] . '"
                    data-author="'.$row['Author_Promo'].'"
                    data-promo-title="'.$row['Title_Promo'].'"
                    id="rejectBtn"
                    class="btn btn-danger reject-btn" 
                    data-bs-toggle="modal" data-bs-target="#viewEvent">
                    Reject
                    </button>
                    </div>';
                }
               
            }
        } else {
            // Handle the case where the query fails
            echo "Error executing query: " . mysqli_error($conn);
        }
    } else {
        // Handle the case where UserID is not set in the session
        echo "UserID not found in session.";
    }
    echo '</div>'; // end of d-flex

    echo '</div>'; // end of col-12

    echo '</div>'; // end of col-md-6

    echo '</div>'; // end of 1st row






} else {
    echo 'Event not found';
}
?>

<!-- Approved -->
<script>
   $(document).ready(function () {
    $('#approveBtn').click(function (e) {
        // Get the promo ID from the button's data attribute
        var promoID = $(this).data('promo-id');
        var author = $(this).data('author');
        var newsTitle = $(this).data('promo-title');

        // Show confirmation dialog
        Swal.fire({
            title: 'Confirm',
            text: "Are you sure you want to approve this promo?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            // If user confirms, show authentication input prompt
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Message',
                    input: 'textarea',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Continue',
                    showLoaderOnConfirm: true,
                    preConfirm: (message) => {
                        // Send AJAX request to update promo status and insert message
                        return $.ajax({
                            type: 'POST',
                            url: 'DataUpdate/approvePromo.php',
                            data: {
                                'promoID': promoID,
                                'message': message,
                                'author': author,
                                'newsTitle':newsTitle
                            }, // Pass the promo ID and message to the server
                            dataType: 'json' // Specify dataType as json
                        }).then(response => {
                            // Handle successful response
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.success,
                                onClose: () => {
                                    window.location.reload();
                                }
                            });
                        }).catch(error => {
                            // Handle error response
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while updating promo information. Please try again.'
                            });
                            console.error(error.responseText);
                        });
                    }
                });
            }
        });
    });
});
</script>


<!-- Decline -->
<script>
   $(document).ready(function () {
    $('#declineBtn').click(function (e) {
        // Get the promo ID from the button's data attribute
        var promoID = $(this).data('promo-id');
        var author = $(this).data('author');
    
        var newsTitle = $(this).data('promo-title');
        // Show confirmation dialog
        Swal.fire({
            title: 'Confirm',
            text: "Are you sure you want to decline this promo?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            // If user confirms, show authentication input prompt
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Message',
                    input: 'textarea',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Continue',
                    showLoaderOnConfirm: true,
                    preConfirm: (message) => {
                        // Send AJAX request to update promo status and insert message
                        return $.ajax({
                            type: 'POST',
                            url: 'DataUpdate/declineNews.php',
                            data: {
                                'promoID': promoID,
                                'message': message,
                                'author': author,
                                'newsTitle' :newsTitle
                            }, // Pass the promo ID and message to the server
                            dataType: 'json' // Specify dataType as json
                        }).then(response => {
                            // Handle successful response
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.success,
                                onClose: () => {
                                    window.location.reload();
                                }
                            });
                        }).catch(error => {
                            // Handle error response
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while updating promo information. Please try again.'
                            });
                            console.error(error.responseText);
                        });
                    }
                });
            }
        });
    });
});
</script>

<!-- Reject -->
 
<script>
   $(document).ready(function () {
    $('#rejectBtn').click(function (e) {
        // Get the promo ID from the button's data attribute
        var promoID = $(this).data('promo-id');
        var author = $(this).data('author');
        var newsTitle = $(this).data('promo-title');
    

        // Show confirmation dialog
        Swal.fire({
            title: 'Confirm',
            text: "Are you sure you want to reject this promo?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            // If user confirms, show authentication input prompt
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Message',
                    input: 'textarea',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Continue',
                    showLoaderOnConfirm: true,
                    preConfirm: (message) => {
                        // Send AJAX request to update promo status and insert message
                        return $.ajax({
                            type: 'POST',
                            url: 'DataUpdate/rejectPromo.php',
                            data: {
                                'promoID': promoID,
                                'message': message,
                                'author': author,
                                'newsTitle' :newsTitle
                            }, // Pass the promo ID and message to the server
                            dataType: 'json' // Specify dataType as json
                        }).then(response => {
                            // Handle successful response
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.success,
                                onClose: () => {
                                    window.location.reload();
                                }
                            });
                        }).catch(error => {
                            // Handle error response
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while updating promo information. Please try again.'
                            });
                            console.error(error.responseText);
                        });
                    }
                });
            }
        });
    });
});
</script>
 