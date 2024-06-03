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

    <title>Social Media</title>
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
                        $activeClass = $row['ModuleName'] == 'Social Media' ? ' active' : '';

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
                    echo '<i class="bi bi-calendar4"></i><span>Event Options</span><i class="bi bi-chevron-down ms-auto"></i>';
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
            <h1>Social Media</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                    <li class="breadcrumb-item">Site Options</li>
                    <li class="breadcrumb-item active">Social Media</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>
                            <div class="d-flex justify-content-end">


                                <?php
                                // Assuming you have established a database connection
                                
                                // Check if UserID is set in the session
                                if (isset($_SESSION['UserID'])) {
                                    // Prepare the SQL query with proper concatenation
                                    $sql = "SELECT Action_Add, ModuleID FROM `privileges` WHERE ModuleID = 5 AND UserID = '" . $_SESSION['UserID'] . "'";

                                    // Execute the SQL query
                                    $result = mysqli_query($conn, $sql);

                                    // Check if query was successful
                                    if ($result) {
                                        $row = mysqli_fetch_assoc($result);
                                        // Display the button if Action_Add is 1
                                        if ($row['Action_Add'] == 1) {
                                            echo '<div class="d-flex justify-content-end" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Add">';
                                            echo ' <button type="button" class="btn btn-success" data-bs-toggle="modal"  
                                                data-bs-target="#addMedia">
                                                <i class="bi bi-plus"></i>
                                            </button>';
                                            echo '</div>';
                                        } else {
                                            echo '<div class="d-flex justify-content-end">';
                                            echo '';
                                            echo '</div>';
                                        }
                                    } else {
                                        // Handle the case where the query fails
                                        echo "Error executing query: " . mysqli_error($conn);
                                    }
                                } else {
                                    // Handle the case where UserID is not set in the session
                                    echo "UserID not found in session.";
                                }
                                ?>
                            </div>

                            <?php
                            require ('../config/db_con.php');

                            $table = mysqli_query($conn, "SELECT * FROM social_media WHERE Active = 1");
                            ?>
                            <!-- General Form Elements -->
                            <form id="socialMediaForm" novalidate>
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label card-title">Media Name</label>
                                    <label class="col-sm-10 col-form-label card-title">Link</label>

                                </div>

                                <?php if (mysqli_num_rows($table) > 0) { ?>
                                    <?php while ($row = mysqli_fetch_assoc($table)) { ?>
                                        <div class="row mb-3">
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control medianame" name="medianame[]" required
                                                    readonly id="medianame" value="<?php echo $row['Media_Name'] ?>">
                                                <span id="medianameError" class="text-danger"></span>
                                            </div>

                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <input type="text" class="form-control linkname " name="links[]" required
                                                        readonly id="linkname" value="<?php echo $row['Link'] ?>">
                                                    <input type="hidden" name="SocialMediaIDs[]" id="Social_MediaID"
                                                        value="<?php echo $row['Social_MediaID'] ?>">

                                                    <?php

                                                    if (isset($_SESSION['UserID'])) {
                                                        $sql = "SELECT Action_Update, ModuleID FROM `privileges` WHERE ModuleID = 5 AND UserID = '" . $_SESSION['UserID'] . "'";

                                                        // Execute the SQL query
                                                        $result = mysqli_query($conn, $sql);
                                                        if ($result) {
                                                            $row_privileges = mysqli_fetch_assoc($result);
                                                            // Display the button if Action_Add is 1
                                                            if ($row_privileges['Action_Update'] == 1) {
                                                                echo '<button class="btn btn-outline-primary edit-btn" type="button"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-original-title="Edit"
                                                                    data-socialmedia-id="' . $row['Social_MediaID'] . '"
                                                                    data-socialmedia-name="' . $row['Media_Name'] . '">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>';
                                                            }
                                                        } else {
                                                            // Handle the case where the query fails
                                                            echo "Error executing query: " . mysqli_error($conn);
                                                        }
                                                    } else {
                                                        // Handle the case where UserID is not set in the session
                                                        echo "UserID not found in session.";
                                                    }

                                                    ?>
                                                    <?php

                                                    if (isset($_SESSION['UserID'])) {
                                                        $sql = "SELECT Action_Update, ModuleID FROM `privileges` WHERE ModuleID = 5 AND UserID = '" . $_SESSION['UserID'] . "'";

                                                        // Execute the SQL query
                                                        $result = mysqli_query($conn, $sql);
                                                        if ($result) {
                                                            $row_privileges = mysqli_fetch_assoc($result);
                                                            // Display the button if Action_Add is 1
                                                            if ($row_privileges['Action_Update'] == 1) {
                                                                echo '<button class="btn btn-outline-primary update-btn" type="button"  style="display: none"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    data-bs-original-title="Update"
                                                                    data-socialmedia-id="' . $row['Social_MediaID'] . '"
                                                                    data-socialmedia-name="' . $row['Media_Name'] . '"
                                                                    data-sociallink="' . $row['Link'] . '">
                                                                    <i class="bi bi-arrow-repeat"></i>
                                                                </button>';
                                                            }
                                                        } else {
                                                            // Handle the case where the query fails
                                                            echo "Error executing query: " . mysqli_error($conn);
                                                        }
                                                    } else {
                                                        // Handle the case where UserID is not set in the session
                                                        echo "UserID not found in session.";
                                                    }

                                                    ?>

                                                    <?php
                                                    if (isset($_SESSION['UserID'])) {
                                                        // Prepare the SQL query with proper concatenation
                                                        $sql = "SELECT Action_Delete, ModuleID FROM `privileges` WHERE ModuleID = 5  AND UserID = '" . $_SESSION['UserID'] . "'";

                                                        // Execute the SQL query
                                                        $result = mysqli_query($conn, $sql);

                                                        // Check if query was successful
                                                        if ($result) {
                                                            $row_privileges = mysqli_fetch_assoc($result);
                                                            // Display the button if Action_Add is 1
                                                            if ($row_privileges['Action_Delete'] == 1) {
                                                                echo '  <button id="deleteBtn" class="btn btn-outline-danger delete-btn"
                                                                type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-original-title="Delete"
                                                                data-socialmedia-id="' . $row['Social_MediaID'] . '"
                                                                data-socialmedia-name="' . $row['Media_Name'] . '">
                                                                <i class="bi bi-trash"></i>
                                                            </button>';
                                                            }

                                                        } else {
                                                            // Handle the case where the query fails
                                                            echo "Error executing query: " . mysqli_error($conn);
                                                        }
                                                    } else {
                                                        // Handle the case where UserID is not set in the session
                                                        echo "UserID not found in session.";
                                                    }
                                                    ?>




                                                    <?php

                                                    if (isset($_SESSION['UserID'])) {
                                                        $sql = "SELECT Action_Update, ModuleID FROM `privileges` WHERE ModuleID = 5 AND UserID = '" . $_SESSION['UserID'] . "'";

                                                        // Execute the SQL query
                                                        $result = mysqli_query($conn, $sql);
                                                        if ($result) {
                                                            $row_privileges = mysqli_fetch_assoc($result);
                                                            // Display the button if Action_Add is 1
                                                            if ($row_privileges['Action_Update'] == 1) {
                                                                echo ' <button class="btn btn-outline-secondary cancel-btn" type="button"
                                                                style="display: none" data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-original-title="Cancel">
                                                               
                                                                <i class="bi bi-x">Cancel</i>
                                                            </button>';
                                                            }
                                                        } else {
                                                            // Handle the case where the query fails
                                                            echo "Error executing query: " . mysqli_error($conn);
                                                        }
                                                    } else {
                                                        // Handle the case where UserID is not set in the session
                                                        echo "UserID not found in session.";
                                                    }

                                                    ?>

                                                </div>
                                                <span id="linkError1" class="text-danger"></span>

                                            </div>


                                        </div>

                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="row mb-3">
                                        <div class="col-sm-2">
                                            <p class="form-label">No data available.</p>
                                        </div>
                                        <div class="col-sm-10">
                                            <p class="form-label">No data available.</p>
                                        </div>

                                    </div>
                                <?php } ?>


                            </form>


                        </div>
                    </div>

                </div>


            </div>
        </section>

    </main><!-- End #main -->

    <div class="modal fade" id="addMedia" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="exampleModalLgLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="exampleModalLgLabel">
                        Add Social Media
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- General Form Elements -->
                    <form id="titleSloganForm" novalidate enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="socialName" class="form-label">Social Media Name</label>
                            <input id="socialName" type="text" class="form-control" name="socialName" required>
                            <span id="socialError" class="text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <label for="link" class="form-label">Link</label>
                            <input id="link" type="text" class="form-control" name="link" required>
                            <span id="linkError" class="text-danger"></span>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <a id="closeBtn" class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
                            <button class="btn btn-primary" id="addBtn" type="submit">Add</button>
                        </div>
                    </form>



                </div>
            </div>
        </div>
    </div>




    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span> Allied Care Experts (ACE) Medical Center - Baypointe 2024</span></strong>
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


    <!-- add data -->
    <script>
        document.getElementById('titleSloganForm').addEventListener('submit', function (event) {
            event.preventDefault();

            // Title Validation
            const socialName = document.getElementById('socialName').value.trim();
            const socialInput = document.getElementById('socialName');
            const socialError = document.getElementById('socialError');

            // Slogan Validation
            const link = document.getElementById('link').value.trim();
            const linkInput = document.getElementById('link');
            const linkError = document.getElementById('linkError');



            // Reset previous error messages and border colors
            socialError.textContent = "";
            socialInput.style.borderColor = "";
            linkError.textContent = "";
            linkInput.style.borderColor = "";


            // Validate title
            if (!socialName) {
                socialError.textContent = "Social media name is required";
                socialInput.style.borderColor = "red";
                return;
            }

            const regex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?0-9]/;

            if (regex.test(socialName)) {
                socialError.textContent = "Special characters and numbers are not allowed.";
                socialInput.style.borderColor = "red";
                return;
            }

            // Validate link
            if (!linkInput.value) {
                linkError.textContent = "Link is required";
                linkInput.style.borderColor = "red";
                return;
            } else {
                const httpsUrlPattern = /^https:\/\/[^\s/$.?#].[^\s]*$/i;
                if (!httpsUrlPattern.test(linkInput.value)) {
                    linkError.textContent = "Link must be a valid HTTPS URL";
                    linkInput.style.borderColor = "red";
                    return;
                } else {
                    linkError.textContent = ""; // Clear any previous error message
                    linkInput.style.borderColor = ""; // Reset border color

                    var formData = new FormData();

                    formData.append('socialName', socialName);
                    formData.append('link', link);

                    $.ajax({
                        type: 'POST',
                        url: 'DataAdd/add_social.php',
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
                                    window.location.href = 'social_media.php';
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
                            loading.close(); // Close loading animation
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while submitting data.',
                            });
                        },
                        complete: function () {
                            loading.close(); // Close loading animation regardless of success or failure
                        }
                    });

                }
            }

        });
    </script>




    <!-- delete data -->
    <script>
        $(document).on('click', '.delete-danger', function () {
            var socialName = $(this).data('webtitle');
            var confirmMsg = "Are you sure you want to delete " + socialName + "?";
            Swal.fire({
                title: 'Confirmation',
                text: confirmMsg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {

                    var id = $(this).data("id");
                    var table = $('#example').DataTable();
                    var row = $(this).closest('tr');
                    table.row(row).remove().draw(false);

                    $.ajax({
                        url: "DataDelete/delete_titleslogan.php",
                        method: "POST",
                        data: { titleSlogan: id },
                        success: function (response) {
                            Swal.fire({
                                title: "Success",
                                icon: "success",
                                text: "Deleted Successfully"
                            });
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while updating data.',
                            });
                        }
                    });
                }
            });


        });
    </script>



    <!-- Edit and cancel show/none -->
    <script>
        $(document).ready(function () {
            $('.edit-btn').click(function (e) {
                e.preventDefault();
                var parent = $(this).closest('.row');

                parent.find('.medianame, .linkname').prop('readonly', false);
                parent.find('.delete-btn').hide();
                parent.find('.cancel-btn').show();
                parent.find('.edit-btn').hide();
                parent.find('.update-btn').show();


            });

            $('.cancel-btn').click(function (e) {
                e.preventDefault();
                var parent = $(this).closest('.row');

                parent.find('.medianame, .linkname').prop('readonly', true);
                parent.find('.delete-btn').show();
                parent.find('.cancel-btn').hide();
                parent.find('.edit-btn').show();
                parent.find('.update-btn').hide();



            });
        });



    </script>

    <!-- Delete Data-->
    <script>
        $(document).ready(function () {
            $('.btn-outline-danger').click(function (e) {
                e.preventDefault();

                var SocialMediaID = $(this).data('socialmedia-id');
                var mediaName = $(this).data('socialmedia-name');

                // Show confirmation message using SweetAlert
                Swal.fire({
                    title: 'Confirmation',
                    text: 'Are you sure you want to delete ' + mediaName + '?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes',
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        var loading = Swal.fire({
                            title: 'Please wait',
                            html: 'Deleting your data...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        var dataToSend = {
                            SocialMediaID: SocialMediaID
                        };

                        $.ajax({
                            type: 'POST',
                            url: 'DataDelete/delete_socialMedia.php',
                            data: dataToSend,
                            success: function (response) {
                                response = JSON.parse(response);
                                loading.close();
                                toastr.clear(); // Clear all toastr messages
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: response.success,
                                        showConfirmButton: false,
                                        timer: 1000,
                                    }).then(function () {
                                        window.location.href = 'social_media.php';
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
                                loading.close(); // Close loading animation
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while submitting data.',
                                });
                            },
                            complete: function () {
                                loading.close(); // Close loading animation regardless of success or failure
                            }
                        });
                    }
                });
            });
        });
    </script>


    <!-- dATA table -->
    <script>
        $(document).ready(function () {
            //Only needed for the filename of export files.
            //Normally set in the title tag of your page.
            document.title = "Account Management";
            // Create search inputs in footer
            $("#example tfoot th").each(function (index) {
                var title = $(this).text();
                if (index !== 3) { // Skip the "Action" column (index 6)
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                } else {
                    $(this).empty(); // Remove label for "Action" column
                }
            });
            // DataTable initialisation
            var table = $("#example").DataTable({
                ordering: false,
                dom: '<"dt-buttons"Bf><"clear">lirtp',
                paging: true,
                autoWidth: true,
                buttons: [
                    "colvis",
                    "copyHtml5",
                    "csvHtml5",
                    "excelHtml5",
                    "pdfHtml5",
                    "print"
                ],
                initComplete: function (settings, json) {
                    var footer = $("#example tfoot tr");
                    $("#example thead").append(footer);
                }
            });

            // Apply the search
            $("#example thead").on("keyup", "input", function () {
                var columnIndex = $(this).parent().index();
                if (columnIndex !== 3) { // Skip the "Action" column (index 6)
                    table.column(columnIndex)
                        .search(this.value)
                        .draw();
                }
            });
        });
    </script>

    <!-- edit data -->
    <script>
        $(document).ready(function () {
            $('.update-btn').click(function (e) {
                e.preventDefault();

                const $socialID = $(this).data('socialmedia-id');
                const $row = $(this).closest('.row');

                
                const mediaNameVal = $row.find('.medianame').val().trim();
                const linkVal = $row.find('.linkname').val().trim();

                const mediaNameInput = $row.find('.medianame');
                const linkInput = $row.find('.linkname');

                const mediaError = $row.find('#medianameError');
                const linkError1 = $row.find('#linkError1');

                // Clear previous error messages and styles
                mediaError.text('');
                mediaNameInput.css('border-color', '');
                linkError1.text('');
                linkInput.css('border-color', '');

                // Validation logic for media name
                if (!mediaNameVal) {
                    mediaError.text('Social media is required');
                    mediaNameInput.css('border-color', 'red');
                    return;
                }

                const regex1 = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?0-9]/;
                if (regex1.test(mediaNameVal)) {
                    mediaError.text('Special characters and numbers are not allowed.');
                    mediaNameInput.css('border-color', 'red');
                    return;
                }

                // Validation logic for link
                if (!linkVal) {
                    linkError1.text('Link is required');
                    linkInput.css('border-color', 'red');
                    return;
                } else {
                    const httpsUrlPattern1 = /^https:\/\/[^\s/$.?#].[^\s]*$/i;
                    if (!httpsUrlPattern1.test(linkVal)) {
                        linkError1.text('Link must be a valid HTTPS URL');
                        linkInput.css('border-color', 'red');
                        return;
                    }
                }


                var formData = new FormData();
                formData.append('mediaNameVal', mediaNameVal);
                formData.append('linkVal', linkVal);
                formData.append('socialID', $socialID);

                $.ajax({
                    type: 'POST',
                    url: 'DataGet/DataUpdate/update_socialMedia.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        response = JSON.parse(response);
                        if(response.success){
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.success,
                                showConfirmButton: false,
                                timer: 1000,
                            }).then(function () {
                                window.location.href = 'social_media.php';
                            });
                        }
                        else if (response.error) {
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
                // alert('Success! Data updated for social media ID: ' + $socialID);
            });
        });

    </script>



</body>

</html>