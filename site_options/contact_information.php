<?php
require ('../config/db_con.php');
include ('../security.php');

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


    $stmt->close();


}


if (isset($_SESSION['is_Lock']) && $_SESSION['is_Lock'] == 1) {
    // User account is locked
    header("Location: lock_account.php");
    exit();
}


?>

<?php
$sql = "SELECT * FROM maintenance ORDER BY MaintenanceID DESC LIMIT 1";
$q = $conn->query($sql);
$row = $q->fetch_assoc();
$checkStatus = $row['Status'];

if ($_SESSION['UserRoleName'] != '0') {
    if ($checkStatus == 0) {
        header('location: undermaintenance.php');
        session_destroy();
    }

}
?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Contact Information</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="../assets/img/logo2.png" rel="icon">
    <link href="../assets/img/logo2.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/animation.css" />

    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>




    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <!-- Template Main CSS File -->
    <link href="../assets/css/style.css" rel="stylesheet">




    <!-- Dtatables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.6/css/dataTables.bootstrap5.min.css">

    <link rel='stylesheet' href='../bootsrap3.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
    <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>

    <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Mar 17 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500&display=swap');

        .container {
            width: 300px;
            /* Adjusted width */

            padding: 10px;
            background: #fff;
            border-radius: 20px;
            box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
        }

        #dropArea {
            display: flex;

            align-items: center;
            justify-content: center;
            width: 280px;
            height: 200px;
            border-radius: 20px;
            border: 2px dashed #ccc;
            text-align: center;
            line-height: 200px;

        }

        .icons {
            color: blue;
            /* Change icon color to blue */
            font-size: 3rem;
            /* Increase icon size */
        }

        .pin-code-input input[type="text"] {
            text-align: center;
        }

        #otp-input {
            display: flex;
            column-gap: 8px;
        }

        #otp-input input {
            text-align: center;
            padding: 10px 8px 10px 8px;
            border: 1px solid #adadad;
            border-radius: 4px;
            outline: none;
            height: 64px;
            width: 50px;
        }

        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="index.php" class="logo d-flex align-items-center">
                <img src="../assets/img/logo2.png" alt="">
                <span class="d-none d-lg-block"> </span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div><!-- End Logo -->



        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <li class="nav-item d-block d-lg-none">
                    <a class="nav-link nav-icon search-bar-toggle " href="#">
                        <i class="bi bi-search"></i>
                    </a>
                </li><!-- End Search Icon-->
                <li class="nav-item dropdown pe-3">

                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">

                        <img src="../DataAdd/uploads/<?php
                        echo $_SESSION['ProfilePic'];
                        ?> " alt="Profile" class="rounded-circle">
                        <span class="d-none d-md-block dropdown-toggle ps-2">
                            <?php

                            $firstLetter = strtoupper(substr($_SESSION['Fname'], 0, 1));

                            echo $firstLetter . '. ' . $_SESSION['Lname'];
                            ?>

                        </span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6>
                                <?php

                                $firstLetter = strtoupper(substr($_SESSION['Fname'], 0, 1));

                                echo $firstLetter . '. ' . $_SESSION['Lname'];
                                ?>

                            </h6>
                            <span>
                                <?php


                                if ($_SESSION['UserRoleName'] == 0) {
                                    echo 'Super Admin';
                                } else if ($_SESSION['UserRoleName'] == 1) {
                                    echo 'Admin';
                                } else if ($_SESSION['UserRoleName'] == 2) {
                                    echo 'User';
                                } else if ($_SESSION['UserRoleName'] == 3) {
                                    echo 'Doctors';
                                } else {
                                    echo 'No user role';
                                }

                                ?>
                            </span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="users-profile.php">
                                <i class="bi bi-person"></i>
                                <span>My Profile</span>
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="../logout.php">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>

                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->

    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link collapsed" href="../home.php">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <?php

            // Check if there are rows in the result set
            if ($result->num_rows > 0) {
                $siteOptionsSubMenu = '';
                // $pageOptionsSubMenu = '';
                $eventOptionsSubMenu = '';
                $logOptionsSubMenu = '';


                // Iterate through the result set
                while ($row = $result->fetch_assoc()) {

                    if ($row['ModuleName'] == 'Account Management') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link collapsed" href="../accounts.php">';
                        echo '<i class="bi bi-person"></i>';
                        echo '<span>Account Management</span>';
                        echo '</a>';
                        echo '</li>';
                    }

                    if ($row['ModuleName'] == 'Inbox') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link collapsed" href="../inbox.php">';
                        echo '<i class="bi bi-inbox"></i>';
                        echo '<span>Inbox</span>';
                        echo '</a>';
                        echo '</li>';
                    }

                    if ($row['ModuleName'] == 'Maintenance') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link collapsed" href="../maintenance.php">';
                        echo '<i class="bi bi-gear"></i>';
                        echo '<span>Maintenance</span>';
                        echo '</a>';
                        echo '</li>';
                    }


                    // Check for Site Options module and construct sub-navigation items
                    if (
                        $row['ModuleName'] == 'Title & Slogan' ||
                        $row['ModuleName'] == 'Company Profile' ||
                        $row['ModuleName'] == 'Social Media' ||
                        $row['ModuleName'] == 'Copyright' ||
                        $row['ModuleName'] == 'Contact Information'
                    ) {
                        // Set the active class conditionally
                        $activeClass = $row['ModuleName'] == 'Contact Information' ? ' active' : '';

                        $siteOptionsSubMenu .= '<li>';
                        $siteOptionsSubMenu .= '<a href="' . strtolower(str_replace(' ', '_', $row['ModuleName'])) . '.php" class="' . $activeClass . '">';
                        $siteOptionsSubMenu .= '<i class="bi bi-circle"></i><span>' . $row['ModuleName'] . '</span>';
                        $siteOptionsSubMenu .= '</a>';
                        $siteOptionsSubMenu .= '</li>';
                    }

                    // // Check for Page Options module and construct sub-navigation items
                    // if ($row['ModuleName'] == 'Add New Page') {
                    //     $pageOptionsSubMenu .= '<li>';
                    //     $pageOptionsSubMenu .= '<a href="pages/' . strtolower(str_replace(' ', '_', $row['ModuleName'])) . '.php">';
                    //     $pageOptionsSubMenu .= '<i class="bi bi-circle"></i><span>' . $row['ModuleName'] . '</span>';
                    //     $pageOptionsSubMenu .= '</a>';
                    //     $pageOptionsSubMenu .= '</li>';
                    // }
            
                    if ($row['ModuleName'] == 'Event List' || $row['ModuleName'] == 'Event Request') {
                        $eventOptionsSubMenu .= '<li>';
                        $eventOptionsSubMenu .= '<a href="../events/' . strtolower(str_replace(' ', '_', $row['ModuleName'])) . '.php">';
                        $eventOptionsSubMenu .= '<i class="bi bi-circle"></i><span>' . $row['ModuleName'] . '</span>';
                        $eventOptionsSubMenu .= '</a>';
                        $eventOptionsSubMenu .= '</li>';
                    }

                    if ($row['ModuleName'] == 'Event Super Admin Logs' || $row['ModuleName'] == 'Event Admin Logs' || $row['ModuleName'] == 'Event User Logs' || $row['ModuleName'] == 'Event Doctors Logs') {
                        $logOptionsSubMenu .= '<li>';
                        $logOptionsSubMenu .= '<a href="../logs/' . strtolower(str_replace(' ', '_', $row['ModuleName'])) . '.php">';
                        $logOptionsSubMenu .= '<i class="bi bi-circle"></i><span>' . $row['ModuleName'] . '</span>';
                        $logOptionsSubMenu .= '</a>';
                        $logOptionsSubMenu .= '</li>';
                    }



                    if ($row['ModuleName'] == 'Category Options') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link collapsed" href="../category.php">';
                        echo '<i class="bi bi-justify-left"></i>';
                        echo '<span>Category Options</span>';
                        echo '</a>';
                        echo '</li>';
                    }

                    if ($row['ModuleName'] == 'Department Options') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link collapsed" href="../department.php">';
                        echo '<i class="bi bi-building"></i>';
                        echo '<span>Department Options</span>';
                        echo '</a>';
                        echo '</li>';
                    }

                    if ($row['ModuleName'] == 'Service Options') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link collapsed" href="../service.php">';
                        echo '<i class="bi bi-life-preserver"></i>';
                        echo '<span>Service Options</span>';
                        echo '</a>';
                        echo '</li>';
                    }


                    if ($row['ModuleName'] == 'Activity History') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link collapsed" href="../activity_history.php">';
                        echo '<i class="bi bi-clock-history"></i>';
                        echo '<span>Activity History</span>';
                        echo '</a>';
                        echo '</li>';
                    }




                }

                // Output Site Options navigation section if there are relevant sub-navigation items
                if ($siteOptionsSubMenu !== '') {
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">';
                    echo '<i class="bi bi-gear"></i><span>Site Options</span><i class="bi bi-chevron-down ms-auto"></i>';
                    echo '</a>';
                    echo '<ul id="components-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">';
                    echo $siteOptionsSubMenu; // Output the constructed sub-navigation items
                    echo '</ul>';
                    echo '</li>';
                }

                // if ($pageOptionsSubMenu !== '') {
                //     echo '<li class="nav-item">';
                //     echo '<a class="nav-link collapsed" data-bs-target="#page-nav" data-bs-toggle="collapse" href="#">';
                //     echo '<i class="bi bi-stickies"></i><span>Page</span><i class="bi bi-chevron-down ms-auto"></i>';
                //     echo '</a>';
                //     echo '<ul id="page-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">';
                //     echo $pageOptionsSubMenu; // Output the constructed sub-navigation items
                //     echo '</ul>';
                //     echo '</li>';
                // }
            
                if ($eventOptionsSubMenu !== '') {
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link collapsed" data-bs-target="#event-nav" data-bs-toggle="collapse" href="#">';
                    echo '<i class="bi bi-calendar4"></i><span>What\'s New</span><i class="bi bi-chevron-down ms-auto"></i>';
                    echo '</a>';
                    echo '<ul id="event-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">';
                    echo $eventOptionsSubMenu; // Output the constructed sub-navigation items
                    echo '</ul>';
                    echo '</li>';
                }


                if ($logOptionsSubMenu !== '') {
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link collapsed" data-bs-target="#log-nav" data-bs-toggle="collapse" href="#">';
                    echo '<i class="bi bi-clipboard"></i><span>Logs</span><i class="bi bi-chevron-down ms-auto"></i>';
                    echo '</a>';
                    echo '<ul id="log-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">';
                    echo $logOptionsSubMenu; // Output the constructed sub-navigation items
                    echo '</ul>';
                    echo '</li>';
                }
            } else {
                // Handle the case where there are no rows in the result set
                echo "No modules found.";
            }

            ?>

            <?php
            // It will be based on Action_Delete = 1 so that all users that has action delete will be have trash module
            if ($_SESSION['UserRoleName'] == '0') {
                echo '  <li class="nav-item">
                <a class="nav-link collapsed" href="../trash.php">
                <i class="bi bi-trash"></i>
                <span>Trash</span>
                </a>
            </li>';
            }

            ?>

        </ul>
    </aside>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Contact Information</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                    <li class="breadcrumb-item">Site Options</li>
                    <li class="breadcrumb-item active">Contact Information</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>

                            <?php
                    require('../config/db_con.php');

                    $table = mysqli_query($conn,"SELECT * FROM contact_information");
                ?>
                <?php if(mysqli_num_rows($table) > 0) { ?>
                    <form id="contactInfo" class="need-validation" novalidate>
                        <?php while($row = mysqli_fetch_assoc($table)) { ?>
                            <div class="row mb-3 pt-4">
                                <label for="inputText" class="col-sm-2 col-form-label">Address</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="address" name="address" required value="<?php echo $row['Address']?>">
                                    <span id="addError" class="text-danger"></span>
                                </div>
                               
                                
                            </div>
                            
                            <div class="row mb-3 pt-4">
                                <label for="inputText" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="email" name="email" required value="<?php echo $row['Email']?>">
                                    <input type="hidden" class="form-control" id="ContactInformationID" name="Contact_InformationID" required value="<?php echo $row['Contact_InformationID']?>">
                                    <span id="emailError" class="text-danger"></span>
                                     
                                </div>
                            </div>

                            <div class="row mb-3 pt-4">
                                <label for="inputText" class="col-sm-2 col-form-label">Phone</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="phone" name="phone" required value="<?php echo $row['Phone']?>">
                                    <span id="phoneError" class="text-danger"></span>
                                     
                                </div>
                            </div>

                            <div class="row mb-3 pt-4">
                                <label for="inputText" class="col-sm-2 col-form-label">Mobile</label>
                                <div class="col-sm-10">
                                    <?php
 
                                        // Split the data into lines
                                        $mobile_data = explode("\n", $row['Mobile']);

                                        // Initialize variables to store phone numbers
                                        $smart_mobile = '';
                                        $globe_mobile = '';
                                        $sun_mobile = '';

                                        // Loop through each line to extract phone numbers
                                        foreach ($mobile_data as $line) {
                                            // Check if the line contains Smart phone number
                                            if (strpos($line, 'Smart:') !== false) {
                                                $smart_mobile = trim(str_replace('Smart:', '', $line));
                                            }
                                            // Check if the line contains Globe phone number
                                            elseif (strpos($line, 'Globe:') !== false) {
                                                $globe_mobile = trim(str_replace('Globe:', '', $line));
                                            }
                                            // Check if the line contains Sun phone number
                                            elseif (strpos($line, 'Sun:') !== false) {
                                                $sun_mobile = trim(str_replace('Sun:', '', $line));
                                            }
                                        }
                                    ?>

                                    <!-- Display input fields with the extracted phone numbers -->
                                    <label for="smart_mobile">Smart</label>
                                    <input type="text" class="form-control" id="smart_mobile" name="smart_mobile" value="<?php echo $smart_mobile; ?>" placeholder="Smart" oninput="formatPhoneNumber(event)">
                                    <span id="smartError" class="text-danger"></span><br>
                                    <label for="globe_mobile">Globe</label>
                                    <input type="text" class="form-control" id="globe_mobile" name="globe_mobile" value="<?php echo $globe_mobile; ?>" placeholder="Globe" oninput="formatPhoneNumber(event)">
                                    <span id="globeError" class="text-danger"></span><br>
                                    <label for="sun_mobile">Sun</label>
                                    <input type="text" class="form-control" id="sun_mobile" name="sun_mobile" value="<?php echo $sun_mobile; ?>" placeholder="Sun" oninput="formatPhoneNumber(event)">
                                    <span id="sunError" class="text-danger"></span>

                                 
                                    
                                    <script>
                                        function formatPhoneNumber(event) {
                                            const input = event.target;
                                            let value = input.value.replace(/\D/g, '');
                                            
                                            if (value.length > 4) {
                                                value = value.slice(0, 4) + '-' + value.slice(4);
                                            }
                                            if (value.length > 8) {
                                                value = value.slice(0, 8) + '-' + value.slice(8, 12);
                                            }
                                            input.value = value;
                                        }
                                    </script>
                                    
                                     
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row d-flex justify-content-end">
                            <div class="col-sm-10 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" id="updateBtn">Update</button>
                            </div>
                        </div>
                    </form>
                <?php } ?>

                            


                             

                        </div>
                    </div>

                </div>


            </div>
        </section>

    </main><!-- End #main -->


    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong>
                <span>
                    <?php
                    require ('../config/db_con.php');

                    $table = mysqli_query($conn, "SELECT * FROM copyright WHERE Active = 1");
                    if (mysqli_num_rows($table) > 0) {
                        while ($row = mysqli_fetch_assoc($table)) {
                            echo "<strong>" . $row['CopyrightName'] . "</strong>";
                        }
                    }
                    ?>
                </span>

        </div>
        <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
            Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>
    <!-- Template Main JS File -->
    <script src="../assets/js/main.js"></script>

    <!-- partial -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js'></script>
    <script src='https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.colVis.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js'></script>
    <script src='https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js'></script>
    <script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.bootstrap.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js'></script>
    <script src='https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js'></script>
    <script src='https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js'></script>




      <!--Update  -->
      <script>
        $(document).ready(function () {
            $('#updateBtn').click(function (e) {
                e.preventDefault();

                var Contact_InformationID =document.getElementById('ContactInformationID').value.trim();
                var addressVal =document.getElementById('address').value.trim();
                var addressInput = document.getElementById('address')
                var addError = document.getElementById('addError');

                var SubicOfficeVal =document.getElementById('phone').value.trim();
                var SubicOfficeInput = document.getElementById('phone');
                var subError =document.getElementById('phoneError');

                var emailVal = document.getElementById('email').value.trim();
                var emailInput = document.getElementById('email');
                var emailError =document.getElementById('emailError');

                const smartVal =document.getElementById('smart_mobile').value.trim();
                const smartInput =document.getElementById('smart_mobile');
                var smartError =document.getElementById('smartError');

                const globeVal =document.getElementById('globe_mobile').value.trim();
                const globeInput =document.getElementById('globe_mobile');
                var globeError =document.getElementById('globeError');

                const sunVal =document.getElementById('sun_mobile').value.trim();
                const sunInput =document.getElementById('sun_mobile');
                var sunError =document.getElementById('sunError');



                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if(!addressVal){
                    addressInput.style.borderColor = "red";
                    addError.textContent = "Please input address";
                    return;
                }else{
                    addressInput.style.borderColor = "";
                    addError.textContent = "";
                }

                if(!SubicOfficeVal){
                    SubicOfficeInput.style.borderColor = "red";
                    subError.textContent = "Please input this fields";
                    return;
                }else {
                    SubicOfficeInput.style.borderColor = "";
                    subError.textContent = "";
                }

                if(!emailVal){
                    emailInput.style.borderColor = 'red';
                    emailError.textContent = "Plesase input email";
                    return;
                } else if (!emailPattern.test(emailVal)) {
                    emailError.textContent = "Invalid email format";
                    emailInput.borderColor = "red"
                    return;
                }else {
                    emailError.textContent = "";
                    emailInput.borderColor = ""
                
                 
                
                if(!smartVal){
                    smartInput.style.borderColor = "red";
                    smartError.textContent ="Please input this fields";
                    return;
                }
                else{
                    smartInput.style.borderColor = "";
                    smartError.textContent= "";
                }

                if(!globeVal){
                    globeInput.style.borderColor = "red";
                    globeError.textContent ="Please input this fields";
                    return;
                }else{
                    globeInput.style.borderColor = "";
                    globeError.textContent ="";
                }

                if(!sunVal){
                    sunInput.style.borderColor = "red";
                    sunError.textContent ="Please input this fields";
                    return;
                }else{
                    sunInput.style.borderColor = "";
                    sunError.textContent ="";
                }

                Swal.fire({
                    title: 'Confirmation',
                    text: 'Are you sure you want to update?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes',
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        var formData = new FormData();

                        formData.append('addressVal', addressVal);
                        formData.append('SubicOfficeVal', SubicOfficeVal);
                        formData.append('emailVal', emailVal);
                        formData.append('smartVal', smartVal);
                        formData.append('globeVal', globeVal);
                        formData.append('sunVal', sunVal);

                        formData.append('Contact_InformationID', Contact_InformationID);

                         
                        $.ajax({
                            type: 'POST',
                            url: 'DataUpdate/update_contact.php',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                response = JSON.parse(response);
                               
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: response.success,
                                        showConfirmButton: false,
                                        timer: 1000,
                                    }).then(function() {
                                        window.location.href = 'contact_information.php';
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
                                // Close loading animation
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while updating data.',
                                });
                            },
                            complete: function() {
                                // Close loading animation regardless of success or failure
                            }
                        });
                    } else {
                        window.location.href = 'contact_information.php';
                        time = 1000;
                    }
                });
                }
  
              
            });

        // Delete button functionality
            $('.delete-btn').click(function () {
                $(this).closest('.row').remove();
            });
        });

        

     </script>

 

</body>

</html>