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
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <title>Promo and Packages</title>
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

        .icon-stack {
            position: relative;
            display: inline-block;
            width: 1em;
            height: 1em;
        }

        .icon-stack .bi-lock {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
            /* Ensure the lock icon is above the pencil icon */
        }

        .icon-stack .bi-pencil {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 0;
            /* Ensure the pencil icon is below the lock icon */
            filter: blur(1px);
            /* Adjust the blur amount as needed */
            opacity: 0.5;
        }

        .custom-card {
            transition: transform 0.2s ease-in-out;
        }

        .custom-card:hover {
            transform: scale(1.05);
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
                    if ($row['ModuleName'] == 'Title & Slogan' || $row['ModuleName'] == 'Company Profile' || $row['ModuleName'] == 'Social Media' || $row['ModuleName'] == 'Copyright' || $row['ModuleName'] == 'Contact Information') {
                        $siteOptionsSubMenu .= '<li>';
                        $siteOptionsSubMenu .= '<a href="../site_options/' . strtolower(str_replace(' ', '_', $row['ModuleName'])) . '.php">';
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
            
                    if (
                        $row['ModuleName'] == 'Event List' ||
                        $row['ModuleName'] == 'News List' ||
                        $row['ModuleName'] == 'Promo and Packages'

                    ) {
                        $activeClass = $row['ModuleName'] == 'Promo and Packages' ? ' active' : '';

                        $eventOptionsSubMenu .= '<li>';
                        $eventOptionsSubMenu .= '<a href="' . strtolower(str_replace(' ', '_', $row['ModuleName'])) . '.php" class="' . $activeClass . '">';
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
                    echo '<ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">';
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
                    echo '<ul id="event-nav" class="nav-content collapse show " data-bs-parent="#sidebar-nav">';
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
                <a class="nav-link collapsed" href="trash.php">
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
            <h1>Promo and Packages</h1>
            <nav class="d-flex justify-content-between">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                    <li class="breadcrumb-item">What's New</li>
                    <li class="breadcrumb-item active">Promo and Packages</li>
                </ol>

            </nav>
        </div><!-- End Page Title -->

        <section class="section">

            <?php


            $name = $_SESSION['Fname'];
            if ($_SESSION['UserRoleName'] == 0) {
                $rejects = $conn->query("SELECT COUNT(*) FROM news WHERE Status = 'REJECT'")->fetch_row()[0];
                $decline = $conn->query("SELECT COUNT(*) FROM news WHERE Status = 'DECLINE'")->fetch_row()[0];
                $request = $conn->query("SELECT COUNT(*) FROM news WHERE Status = 'UNDER REVIEW'")->fetch_row()[0];
            } else {
                $rejects = $conn->query("SELECT COUNT(*) FROM news WHERE Status = 'REJECT' AND Author_News REGEXP '^$name\\s*\\()'")->fetch_row()[0];
                $decline = $conn->query("SELECT COUNT(*) FROM news WHERE Status = 'DECLINE' AND Author_News REGEXP '^$name\\s*\\()'")->fetch_row()[0];
                // $request = $conn->query("SELECT COUNT(*) FROM news WHERE Status = 'REQUEST' AND Author REGEXP '^$name\\s*\\()'" )->fetch_row()[0];
            }


            ?>


            <div class="row">
                <div class="col">
                    <div class="card text-center custom-card">

                        <div class="card-header bg-primary text-white">
                            REJECT
                        </div>
                        <div class="card-body">
                            <h5 class="card-title" data-bs-toggle="modal" data-bs-target="#rejects"
                                style="cursor: pointer;">
                                <?php echo $rejects ?>
                            </h5>
                        </div>

                    </div>
                </div>

                <div class="col">
                    <div class="card text-center custom-card">

                        <div class="card-header bg-danger text-white">
                            DECLINE
                        </div>
                        <div class="card-body">
                            <h5 class="card-title" data-bs-toggle="modal" data-bs-target="#decline"
                                style="cursor: pointer;">
                                <?php echo $decline ?>
                            </h5>
                        </div>
                    </div>
                </div>

                <?php if ($_SESSION['UserRoleName'] == 0) {
                    echo '<div class="col">
                   <div class="card text-center custom-card">

                        <div class="card-header bg-success text-white">
                            REQUEST
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"  data-bs-toggle="modal" data-bs-target="#request"  style="cursor: pointer;">' . $request . '</h5>
                        </div>
                    </div>
                </div>';
                } else {
                    echo '';
                }
                ?>
            </div>

            <div class="row">
                <div class="col-lg">

                    <div class="card">
                        <div class="card-body table-responsive">
                            <h5 class="card-title">Pending Promo and Packages</h5>

                            <?php
                            // Assuming you have established a database connection
                            
                            // Check if UserID is set in the session
                            if (isset($_SESSION['UserID'])) {
                                // Prepare the SQL query with proper concatenation
                                $sql = "SELECT Action_Add, ModuleID FROM `privileges` WHERE ModuleID = 21 AND UserID = '" . $_SESSION['UserID'] . "'";

                                // Execute the SQL query
                                $result = mysqli_query($conn, $sql);

                                // Check if query was successful
                                if ($result) {
                                    $row = mysqli_fetch_assoc($result);
                                    // Display the button if Action_Add is 1
                                    if ($row['Action_Add'] == 1) {
                                        echo '<div class="d-flex justify-content-end pt-4">';
                                        echo ' <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUser">
                          <i class="bi bi-plus"></i>  
                        </button>';
                                        echo '</div>';
                                    } else {
                                        echo '<div class="d-flex justify-content-end pt-4">';
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

                            <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">

                                <thead>
                                    <tr>
                                        <!-- <th>Serial No.</th> -->
                                        <th>Title</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th>Author</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    require ('../config/db_con.php');
                                    $name = $_SESSION['Fname'];
                                    if ($_SESSION['UserRoleName'] == 0) {
                                        $table = mysqli_query($conn, "SELECT * FROM promo_and_packages WHERE Status = 'PENDING' AND Active = 1 ORDER BY Promo_and_PackagesID DESC");

                                    } else {
                                        $table = mysqli_query($conn, "SELECT * FROM promo_and_packages WHERE Status = 'PENDING' AND Author_Promo REGEXP '^$name\\s*\\()' AND Active = 1 ORDER BY Promo_and_PackagesID DESC");

                                    }


                                    // $serialNo = 1;
                                    while ($row = mysqli_fetch_assoc($table)) {
                                        ?>
                                        <tr>
                                            <!-- <td><?php echo $serialNo++; ?></td> -->

                                            <td><?php echo $row['Title_Promo']; ?></td>
                                            <td><img src="DataAdd/uploads/<?php echo $row['Image_Promo']; ?>"
                                                    alt="event image" width="100px" height="100px"></td>
                                            <td>
                                                <?php
                                                // echo $row['Status']; 
                                                if ($row['Status'] == 'APPROVED') {
                                                    echo '<span class="badge bg-info">APPROVED</span>';
                                                } else if ($row['Status'] == 'PENDING') {
                                                    echo '<span class="badge bg-secondary">PENDING</span>';
                                                } else if ($row['Status'] == 'REVIEW') {
                                                    echo '<span class="badge bg-info">REVIEW</span>';
                                                } else if ($row['Status'] == 'DECLINE') {
                                                    echo '<span class="badge bg-warning">DECLINE</span>';
                                                } else if ($row['Status'] == 'REJECT') {
                                                    echo '<span class="badge bg-danger">REJECT</span>';
                                                }
                                                ?>



                                            </td>
                                            <td><?php echo $row['Author_Promo']; ?></td>

                                            <td class="text-center">
                                                <div class="d-inline-flex gap-3">

                                                    <?php
                                                    if (isset($_SESSION['UserID'])) {
                                                        // Prepare the SQL query with proper concatenation
                                                        $sql = "SELECT Action_View, ModuleID FROM `privileges` WHERE ModuleID = 21  AND UserID = '" . $_SESSION['UserID'] . "'";

                                                        // Execute the SQL query
                                                        $result = mysqli_query($conn, $sql);

                                                        // Check if query was successful
                                                        if ($result) {
                                                            $row_privileges = mysqli_fetch_assoc($result);
                                                            // Display the button if Action_Add is 1
                                                            if ($row_privileges['Action_View'] == 1) {
                                                                echo '<div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="View">
                                                                <button type="button" 
                                                                data-promo-id="' . $row['Promo_and_PackagesID'] . '"
                                                                class="btn btn-primary view-btn" 
                                                                data-bs-toggle="modal" data-bs-target="#viewEvent">
                                                                    <i class="bi bi-eye"></i>
                                                                </button>
                                                            </div>';
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
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <!-- <th>Serial No.</th> -->
                                        <th>Title</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th>Author</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>





                        </div>
                    </div>

                </div>


            </div>
        </section>

        <section class="section">
            <div class="row">
                <div class="col-lg">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h5 class="card-title">Approved News</h5>
                            <table id="example1" class="table table-striped table-bordered" cellspacing="0"
                                width="100%">

                                <thead>
                                    <tr>
                                        <!-- <th>Serial No.</th> -->
                                        <th>News Title</th>
                                        <th>News Image</th>
                                        <th>Status</th>
                                        <th>Author</th>
                                        <th>Decision Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    require ('../config/db_con.php');
                                    $name = $_SESSION['Fname'];
                                    if ($_SESSION['UserRoleName'] == 0) {
                                        $table = mysqli_query($conn, "SELECT * FROM news WHERE Status = 'APPROVED' AND Active = 1 ORDER BY NewsID DESC ");
                                    } else {
                                        $table = mysqli_query($conn, "SELECT * FROM news WHERE Status = 'APPROVED' OR Status = 'UNDER REVIEW' OR Status = 'REQUEST APPROVED' AND Author_News REGEXP '^$name\\s*\\()' AND Active = 1 ORDER BY NewsID DESC ");
                                    }


                                    // $serialNo = 1;
                                    while ($row = mysqli_fetch_assoc($table)) {
                                        ?>
                                        <tr>
                                            <!-- <td><?php echo $serialNo++; ?></td> -->

                                            <td><?php echo $row['Title_News']; ?></td>
                                            <td><img src="DataAdd/uploads/<?php echo $row['Image_News']; ?>"
                                                    alt="news image" width="100px" height="100px"></td>
                                            <td>
                                                <?php
                                                // echo $row['Status']; 
                                                if ($row['Status'] == 'APPROVED') {
                                                    echo '<span class="badge bg-info">APPROVED</span>';
                                                } else if ($row['Status'] == 'PENDING') {
                                                    echo '<span class="badge bg-secondary">PENDING</span>';
                                                } else if ($row['Status'] == 'UNDER REVIEW') {
                                                    echo '<span class="badge bg-danger">UNDER REVIEW</span>';
                                                } else if ($row['Status'] == 'DECLINE') {
                                                    echo '<span class="badge bg-warning">DECLINE</span>';
                                                } else if ($row['Status'] == 'REJECT') {
                                                    echo '<span class="badge bg-danger">REJECT</span>';
                                                } else if ($row['Status'] == 'REQUEST APPROVED') {
                                                    echo '<span class="badge bg-info">REQUEST APPROVED</span>';
                                                }
                                                ?>



                                            </td>
                                            <td><?php echo $row['Author_News']; ?></td>
                                            <td><?php echo $row['Decision_Status'] ?></td>
                                            <td class="text-center">
                                                <div class="d-inline-flex gap-3">

                                                    <?php
                                                    if (isset($_SESSION['UserID'])) {
                                                        // Prepare the SQL query with proper concatenation
                                                        $sql = "SELECT Action_View, ModuleID FROM `privileges` WHERE ModuleID = 21  AND UserID = '" . $_SESSION['UserID'] . "'";

                                                        // Execute the SQL query
                                                        $result = mysqli_query($conn, $sql);

                                                        // Check if query was successful
                                                        if ($result) {
                                                            $row_privileges = mysqli_fetch_assoc($result);
                                                            // Display the button if Action_Add is 1
                                                            if ($row_privileges['Action_View'] == 1) {
                                                                echo '<div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="View">
                                                                <button type="button" 
                                                                data-news-id="' . $row['NewsID'] . '"
                                                                class="btn btn-primary view-btn" 
                                                                data-bs-toggle="modal" data-bs-target="#viewEvent">
                                                                    <i class="bi bi-eye"></i>
                                                                </button>
                                                            </div>';
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
                                                        $sql = "SELECT Action_Update, ModuleID FROM `privileges` WHERE ModuleID = 21  AND UserID = '" . $_SESSION['UserID'] . "'";

                                                        // Execute the SQL query
                                                        $result = mysqli_query($conn, $sql);

                                                        // Check if query was successful
                                                        if ($result) {
                                                            $row_privileges = mysqli_fetch_assoc($result);
                                                            // Display the button if Action_Add is 1
                                                            if ($row_privileges['Action_Update'] == 1) {


                                                                if ($_SESSION['UserRoleName'] == 0) {

                                                                    if ($row['can_edit'] == 0 || $row['can_edit'] == 1) {
                                                                        echo '<button type="button" class="btn btn-info edit-event"  
                                                                        id="editAdminBtn"
                                                                    data-bs-toggle="modal" data-bs-target="#editEvent" 
                                                                        data-news-id="' . $row['NewsID'] . '"
                                                                        data-decision="' . $row['Decision_Status'] . '"
                                                                        data-news-title="' . $row['Title_News'] . '"
                                                                        data-author="' . $row['Author_News'] . '">
                                                                        
                                                                       
                                                                        <i class="bi bi-pencil"></i>
                                                                    </button>';
                                                                    }

                                                                } else if ($_SESSION['UserRoleName'] != 0) {
                                                                    if ($row['can_edit'] == 0) {
                                                                        echo '<button type="button" class="btn btn-info edit-btn"  
                                                                        id="editBtn"
                                                                    data-bs-toggle="modal" data-bs-target="#editUser" 
                                                                        data-news-id="' . $row['NewsID'] . '"
                                                                        data-decision="' . $row['Decision_Status'] . '"
                                                                        data-author="' . $row['Author_News'] . '">
                                                                        <span class="icon-stack">
                                                                        <i class="bi bi-lock" style="position: absolute;"></i>
                                                                        <i class="bi bi-pencil" style="filter: blur(1px); opacity: 0.5;"></i>
                                                                    </span>
                                                                    </button>';
                                                                    } else {
                                                                        echo '<button type="button" class="btn btn-info edit-event"  
                                                                        id="editAdminBtn"
                                                                    data-bs-toggle="modal" data-bs-target="#editEvent" 
                                                                        data-news-id="' . $row['NewsID'] . '"
                                                                        data-decision="' . $row['Decision_Status'] . '"
                                                                        data-news-title="' . $row['Title_News'] . '"
                                                                        data-author="' . $row['Author_News'] . '">
                                                                        
                                                                       
                                                                        <i class="bi bi-pencil"></i>
                                                                    </button>';
                                                                    }
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
                                                    ?>

                                                    <?php
                                                    if (isset($_SESSION['UserID'])) {
                                                        // Prepare the SQL query with proper concatenation
                                                        $sql = "SELECT Action_Delete, ModuleID FROM `privileges` WHERE ModuleID = 21  AND UserID = '" . $_SESSION['UserID'] . "'";

                                                        // Execute the SQL query
                                                        $result = mysqli_query($conn, $sql);

                                                        // Check if query was successful
                                                        if ($result) {
                                                            $row_privileges = mysqli_fetch_assoc($result);
                                                            // Display the button if Action_Add is 1
                                                            if ($row_privileges['Action_Delete'] == 1) {
                                                                echo '<div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Delete">
                                                                <button type="button" 
                                                                data-news-id="' . $row['NewsID'] . '"
                                                                id="deleteBtn"
                                                                class="btn btn-danger delete-danger" 
                                                                data-bs-toggle="modal" data-bs-target="#lockAccount">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>';
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
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <!-- <th>Serial No.</th> -->
                                        <th>News Title</th>
                                        <th>Front Image</th>
                                        <th>Status</th>
                                        <th>Author</th>
                                        <th>Decision Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->



    <!-- Modal -->
    <div class="modal fade" id="addUser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="addCategoryModalLabel">
                        Add Promo and Packages
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="promoPackForm">
                        <div class="mb-3">
                            <label for="eventTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="eventTitle" placeholder="Enter title">
                            <span id="titleError" class="text-danger"></span> <!-- Error message element -->
                        </div>
                        <div class="mb-3">
                            <label for="eventDesc" class="form-label">Description</label>
                            <textarea name="eventDesc" id="eventDesc" class="form-control"></textarea>
                            <span id="descError" class="text-danger"></span> <!-- Error message element -->
                        </div>
                        <div class="mb-3">
                            <label for="deadlinePromo" class="form-label">Deadline Promo</label>
                            <input type="date" class="form-control" id="deadlinePromo" placeholder="Enter title">
                            <span id="deadlineError" class="text-danger"></span> <!-- Error message element -->
                        </div>

                        <script>
                            // Get today's date
                            const today = new Date();
                            // Format today's date as YYYY-MM-DD
                            const todayFormatted = today.toISOString().split('T')[0];
                            // Set the min attribute to today's date
                            document.getElementById('deadlinePromo').setAttribute('min', todayFormatted);
                        </script>


                        <div class="mb-3">
                            <label for="image1" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image1">
                            <span id="image1Error" class="text-danger"></span> <!-- Error message element -->
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>



                </div>
            </div>
        </div>
    </div>

    <!-- View modal -->
    <div class="modal fade" id="viewEvent" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="exampleModalLgLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="exampleModalLgLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>


                </div>
                <div class="modal-body" id="newsBody">


                </div>
            </div>
        </div>
    </div>

    <!-- Edit modal -->
    <div class="modal fade" id="editEvent" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="addCategoryModalLabel">
                        Edit News
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="newsEditForm">



                </div>
            </div>
        </div>
    </div>
    <!-- Modal Rejects-->
    <div class="modal fade" id="rejects" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="addCategoryModalLabel">
                        Rejected News
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body table-responsive" id="rejectBody">
                    <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">

                        <thead>
                            <tr>
                                <!-- <th>Serial No.</th> -->
                                <th>News Title</th>
                                <th>News Image</th>
                                <th>Message</th>
                                <th>Author</th>
                                <th>Date and Time</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require ('../config/db_con.php');

                            $name = $_SESSION['Fname'];
                            if ($_SESSION['UserRoleName'] == 0) {
                                $table = mysqli_query($conn, "SELECT m.*, n.* FROM messages m INNER JOIN news n ON m.NewsID=n.NewsID WHERE n.Status = 'REJECT' AND m.Activity = 'Reject your news post' AND n.Active = 1 ORDER BY n.NewsID DESC");
                            } else {
                                $table = mysqli_query($conn, "SELECT m.*, n.* FROM messages m INNER JOIN news n ON m.NewsID=n.NewsID WHERE n.Status = 'REJECT' AND n.Author_News REGEXP '^$name\\s*\\()' AND m.Activity = 'Reject your news post' AND n.Active = 1 ORDER BY n.NewsID DESC ");
                            }
                            // $serialNo = 1;
                            while ($row = mysqli_fetch_assoc($table)) {
                                ?>
                                <tr>
                                    <!-- <td><?php echo $serialNo++; ?></td> -->


                                    <td><?php echo $row['Title_News']; ?></td>
                                    <td><img src="DataAdd/uploads/<?php echo $row['Image_News']; ?>" alt="event image"
                                            width="100px" height="100px"></td>
                                    <td>
                                        <?php
                                        $message = htmlspecialchars($row['Messages']);
                                        $maxLength = 50; // Maximum length of the message to display
                                        if (strlen($message) > $maxLength) {
                                            // Truncate the message and add line breaks
                                            $message = wordwrap($message, $maxLength, "<br>\n", true);

                                            // Check if the message length exceeds 150 characters to add "Read more" functionality
                                            $maxLengthForReadMore = 150;
                                            if (strlen($message) > $maxLengthForReadMore) {
                                                $messageToShow = substr($message, 0, $maxLengthForReadMore);
                                                $messageHidden = substr($message, $maxLengthForReadMore);
                                                echo $messageToShow;
                                                ?>
                                                <span id="more" style="display:none;"><?php echo $messageHidden; ?></span>
                                                <button class="btn btn-outline-primary" onclick="toggleReadMore()" id="myBtn">Read
                                                    more</button>
                                            <?php } else {
                                                echo $message;
                                            }
                                        } else {
                                            echo $message;
                                        }
                                        ?>
                                        <script>
                                            function toggleReadMore() {
                                                var moreText = document.getElementById("more");
                                                var btnText = document.getElementById("myBtn");

                                                if (moreText.style.display === "none" || moreText.style.display === "") {
                                                    moreText.style.display = "inline";
                                                    btnText.innerHTML = "Read less";
                                                } else {
                                                    moreText.style.display = "none";
                                                    btnText.innerHTML = "Read more";
                                                }
                                            }
                                        </script>

                                    </td>
                                    <td><?php echo $row['Author_News']; ?></td>
                                    <td><?php echo date('F j, Y   h:i A', strtotime($row['Date'])); ?></td>



                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <!-- <th>Serial No.</th> -->
                                <th>News Title</th>
                                <th>News Image</th>
                                <th>Message</th>
                                <th>Author</th>
                                <th>Date and Time</th>


                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Decline-->
    <div class="modal fade" id="decline" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="addCategoryModalLabel">
                        Declined News
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body table-responsive">

                    <table id="example3" class="table table-striped table-bordered" cellspacing="0" width="100%">

                        <thead>
                            <tr>
                                <!-- <th>Serial No.</th> -->
                                <th>News Title</th>
                                <th>News Image</th>
                                <th>Message</th>
                                <th>Author</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require ('../config/db_con.php');

                            $name = $_SESSION['Fname'];
                            if ($_SESSION['UserRoleName'] == 0) {
                                $table = mysqli_query($conn, "SELECT m.*, n.* FROM messages m INNER JOIN news n ON m.NewsID=n.NewsID WHERE n.Status = 'DECLINE' AND m.Activity = 'Decline your news post' AND n.Active = 1 ORDER BY n.NewsID DESC");
                            } else {
                                $table = mysqli_query($conn, "SELECT m.*, n.* FROM messages m INNER JOIN news n ON m.NewsID=n.NewsID WHERE n.Status = 'DECLINE' AND n.Author_News REGEXP '^$name\\s*\\()' AND m.Activity = 'Decline your news post' AND n.Active = 1 ORDER BY n.NewsID DESC ");
                            }






                            // $serialNo = 1;
                            while ($row = mysqli_fetch_assoc($table)) {
                                ?>
                                <tr>
                                    <!-- <td><?php echo $serialNo++; ?></td> -->


                                    <td><?php echo $row['Title_News']; ?></td>
                                    <td><img src="DataAdd/uploads/<?php echo $row['Image_News']; ?>" alt="event image"
                                            width="100px" height="100px"></td>
                                    <td>
                                        <?php
                                        $message = htmlspecialchars($row['Messages']);
                                        $maxLength = 50; // Maximum length of the message to display
                                        if (strlen($message) > $maxLength) {
                                            // Truncate the message and add line breaks
                                            $message = wordwrap($message, $maxLength, "<br>\n", true);

                                            // Check if the message length exceeds 150 characters to add "Read more" functionality
                                            $maxLengthForReadMore = 150;
                                            if (strlen($message) > $maxLengthForReadMore) {
                                                $messageToShow = substr($message, 0, $maxLengthForReadMore);
                                                $messageHidden = substr($message, $maxLengthForReadMore);
                                                echo $messageToShow;
                                                ?>
                                                <span id="more" style="display:none;"><?php echo $messageHidden; ?></span>
                                                <button class="btn btn-outline-primary" onclick="toggleReadMore()" id="myBtn">Read
                                                    more</button>
                                            <?php } else {
                                                echo $message;
                                            }
                                        } else {
                                            echo $message;
                                        }
                                        ?>
                                        <script>
                                            function toggleReadMore() {
                                                var moreText = document.getElementById("more");
                                                var btnText = document.getElementById("myBtn");

                                                if (moreText.style.display === "none" || moreText.style.display === "") {
                                                    moreText.style.display = "inline";
                                                    btnText.innerHTML = "Read less";
                                                } else {
                                                    moreText.style.display = "none";
                                                    btnText.innerHTML = "Read more";
                                                }
                                            }
                                        </script>

                                    </td>



                                    <td><?php echo $row['Author_News']; ?></td>


                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <!-- <th>Serial No.</th> -->
                                <th>News Title</th>
                                <th>News Image</th>
                                <th>Message</th>
                                <th>Author</th>

                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Request-->
    <div class="modal fade" id="request" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="addCategoryModalLabel" aria-hidden="true">

        <div class="modal-dialog  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="addCategoryModalLabel">
                        News Requested for Edit
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body table-responsive">
                    <table id="example4" class="table table-striped table-bordered" cellspacing="0" width="100%">

                        <thead>
                            <tr>
                                <!-- <th>Serial No.</th> -->
                                <th>News Title</th>
                                <th>News Image</th>
                                <th>Request</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require ('../config/db_con.php');

                            $table = mysqli_query($conn, "SELECT * FROM news WHERE Status = 'UNDER REVIEW' AND Active = 1 ORDER BY NewsID DESC");




                            // $serialNo = 1;
                            while ($row = mysqli_fetch_assoc($table)) {
                                ?>
                                <tr>
                                    <!-- <td><?php echo $serialNo++; ?></td> -->


                                    <td><?php echo $row['Title_News']; ?></td>
                                    <td><img src="DataAdd/uploads/<?php echo $row['Image_News']; ?>" alt="event image"
                                            width="100px" height="100px"></td>

                                    <td><?php echo $row['Decision_Status']; ?></td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-3">
                                            <?php
                                            if (isset($_SESSION['UserID'])) {
                                                // Prepare the SQL query with proper concatenation
                                                $sql = "SELECT Action_Approved, ModuleID FROM `privileges` WHERE ModuleID = 21  AND UserID = '" . $_SESSION['UserID'] . "'";

                                                // Execute the SQL query
                                                $result = mysqli_query($conn, $sql);

                                                // Check if query was successful
                                                if ($result) {
                                                    $row_privileges = mysqli_fetch_assoc($result);
                                                    // Display the button if Action_Add is 1
                                                    if ($row_privileges['Action_Approved'] == 1) {

                                                        echo '<button type="button" class="btn btn-info edit-btn"  
                                                                    id="approvedRequest"
                                                                data-bs-toggle="modal" data-bs-target="#editUser" 
                                                                    data-news-id="' . $row['NewsID'] . '"
                                                                    data-decision="' . $row['Decision_Status'] . '"
                                                                    data-news-title="' . $row['Title_News'] . '"

                                                                    data-author="' . $row['Author_News'] . '">
                                                                    <i class="">Approved Request</i>
                                                                 
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
                                                $sql = "SELECT Action_Reject, ModuleID FROM `privileges` WHERE ModuleID = 21  AND UserID = '" . $_SESSION['UserID'] . "'";

                                                // Execute the SQL query
                                                $result = mysqli_query($conn, $sql);

                                                // Check if query was successful
                                                if ($result) {
                                                    $row_privileges = mysqli_fetch_assoc($result);
                                                    // Display the button if Action_Add is 1
                                                    if ($row_privileges['Action_Reject'] == 1) {

                                                        echo '<button type="button" class="btn btn-danger edit-btn"  
                                                                    id="rejectRequest"
                                                                data-bs-toggle="modal" data-bs-target="#editUser" 
                                                                    data-news-id="' . $row['NewsID'] . '"
                                                                    data-decision="' . $row['Decision_Status'] . '"
                                                                    data-news-title="' . $row['Title_News'] . '"
                                                                    data-author="' . $row['Author_News'] . '">
                                                                    <i class="">Reject Request</i>
                                                                 
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
                                    </td>


                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <!-- <th>Serial No.</th> -->
                                <th>News Title</th>
                                <th>News Image</th>

                                <th>Request</th>
                                <th>Action</th>

                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>


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

    <!-- Table Example  -->
    <script>
        $(document).ready(function () {
            //Only needed for the filename of export files.
            //Normally set in the title tag of your page.

            // Create search inputs in footer
            $("#example tfoot th").each(function (index) {
                var title = $(this).text();
                if (index !== 4) { // Skip the "Action" column (index 6)
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
                    {
                        extend: 'print',
                        customize: function (win) {
                            $(win.document.body)
                                .css('font-size', '10pt')
                                .prepend('<img src="http://datatables.net/media/images/logo-fade.png" style="position:absolute; top:0; left:0;" />');

                            $(win.document.body)
                                .find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }
                    }
                ],
                initComplete: function (settings, json) {
                    var footer = $("#example tfoot tr");
                    $("#example thead").append(footer);
                }
            });

            // Apply the search
            $("#example thead").on("keyup", "input", function () {
                var columnIndex = $(this).parent().index();
                if (columnIndex !== 6) { // Skip the "Action" column (index 6)
                    table.column(columnIndex)
                        .search(this.value)
                        .draw();
                }
            });

            $('#example tbody').on('click', '.view-btn', function () {
                // Your edit button logic here
                var newsID = $(this).data('news-id');
                // Example action, replace with your logic
                // console.log('Edit button clicked for category ID:', newsID);
                $.ajax({
                    url: 'DataGet/get_news.php', // PHP script to fetch data from the server
                    method: 'POST',
                    data: { newsID: newsID },
                    success: function (response) {
                        // Insert the HTML into the modal body
                        $('#newsBody').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            $('#example tbody').on('click', '.edit-btn', function () {
                // Your edit button logic here
                var newsID = $(this).data('news-id');
                // Example action, replace with your logic
                // console.log('Edit button clicked for category ID:', newsID);
                $.ajax({
                    url: 'DataGet/edit_event.php',
                    method: 'POST',
                    data: { categoryid: newsID },
                    success: function (response) {
                        $('#modalBody').html(response); // Insert the fetched data into the modal body
                        $('#editUser').modal('show'); // Show the modal
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>

    <!--Table Example 1  -->
    <script>
        $(document).ready(function () {
            //Only needed for the filename of export files.
            //Normally set in the title tag of your page.

            // Create search inputs in footer
            $("#example1 tfoot th").each(function (index) {
                var title = $(this).text();
                if (index !== 5) { // Skip the "Action" column (index 6)
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                } else {
                    $(this).empty(); // Remove label for "Action" column
                }
            });
            // DataTable initialisation
            var table = $("#example1").DataTable({
                ordering: false,
                dom: '<"dt-buttons"Bf><"clear">lirtp',
                paging: true,
                autoWidth: true,
                responsive: true,
                buttons: [
                    "colvis",
                    "copyHtml5",
                    "csvHtml5",
                    "excelHtml5",
                    "pdfHtml5",
                    {
                        extend: 'print',
                        customize: function (win) {
                            $(win.document.body)
                                .css('font-size', '10pt')
                                .prepend('<img src="http://datatables.net/media/images/logo-fade.png" style="position:absolute; top:0; left:0;" />');

                            $(win.document.body)
                                .find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }
                    }
                ],
                initComplete: function (settings, json) {
                    var footer = $("#example1 tfoot tr");
                    $("#example1 thead").append(footer);
                }
            });

            // Apply the search
            $("#example1 thead").on("keyup", "input", function () {
                var columnIndex = $(this).parent().index();
                if (columnIndex !== 6) { // Skip the "Action" column (index 6)
                    table.column(columnIndex)
                        .search(this.value)
                        .draw();
                }
            });

            $('#example1 tbody').on('click', '.view-btn', function () {
                // Your edit button logic here
                var newsID = $(this).data('news-id');
                // Example action, replace with your logic
                // console.log('Edit button clicked for category ID:', newsID);
                $.ajax({
                    url: 'DataGet/get_news.php', // PHP script to fetch data from the server
                    method: 'POST',
                    data: { newsID: newsID },
                    success: function (response) {
                        // Insert the HTML into the modal body
                        $('#newsBody').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            $('#example1 tbody').on('click', '.edit-btn', function () {
                // Your edit button logic here
                var newsID = $(this).data('news-id');
                // Example action, replace with your logic
                // console.log('Edit button clicked for category ID:', newsID);

            });
        });
    </script>

    <!--Table Example 2  -->
    <script>
        $(document).ready(function () {
            //Only needed for the filename of export files.
            //Normally set in the title tag of your page.

            // Create search inputs in footer
            $("#example2 tfoot th").each(function (index) {
                var title = $(this).text();
                if (index !== 4) { // Skip the "Action" column (index 6)
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                } else if (index = 5) { // Skip the "Action" column (index 6)
                    $(this).html('<input type="date" placeholder="Search ' + title + '"class="date-search"/>');
                }
                else {
                    $(this).empty(); // Remove label for "Action" column
                }
            });
            // DataTable initialisation
            var table = $("#example2").DataTable({
                ordering: false,
                dom: '<"dt-buttons"Bf><"clear">lirtp',
                paging: true,
                autoWidth: true,
                responsive: true,
                buttons: [
                    "colvis",
                    "copyHtml5",
                    "csvHtml5",
                    "excelHtml5",
                    "pdfHtml5",
                    {
                        extend: 'print',
                        customize: function (win) {
                            $(win.document.body)
                                .css('font-size', '10pt')
                                .prepend('<img src="http://datatables.net/media/images/logo-fade.png" style="position:absolute; top:0; left:0;" />');

                            $(win.document.body)
                                .find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }
                    }
                ],
                initComplete: function (settings, json) {
                    var footer = $("#example2 tfoot tr");
                    $("#example2 thead").append(footer);
                }
            });

            // Apply the search
            $("#example2 thead").on("keyup", "input", function () {
                var columnIndex = $(this).parent().index();
                if (columnIndex !== 6) { // Skip the "Action" column (index 6)
                    table.column(columnIndex)
                        .search(this.value)
                        .draw();
                }
            });

            $('#example2 tbody').on('click', '.view-btn', function () {
                // Your edit button logic here
                var newsID = $(this).data('news-id');
                // Example action, replace with your logic
                // console.log('Edit button clicked for category ID:', newsID);
                $.ajax({
                    url: 'DataGet/get_news.php', // PHP script to fetch data from the server
                    method: 'POST',
                    data: { newsID: newsID },
                    success: function (response) {
                        // Insert the HTML into the modal body
                        $('#newsBody').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            $('#example2 tbody').on('click', '.edit-btn', function () {
                // Your edit button logic here
                var newsID = $(this).data('news-id');
                // Example action, replace with your logic
                // console.log('Edit button clicked for category ID:', newsID);

            });
        });
    </script>

    <!--Table Example 3  -->
    <script>
        $(document).ready(function () {
            //Only needed for the filename of export files.
            //Normally set in the title tag of your page.

            // Create search inputs in footer
            $("#example3 tfoot th").each(function (index) {
                var title = $(this).text();
                if (index !== 4) { // Skip the "Action" column (index 6)
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                } else {
                    $(this).empty(); // Remove label for "Action" column
                }
            });
            // DataTable initialisation
            var table = $("#example3").DataTable({
                ordering: false,
                dom: '<"dt-buttons"Bf><"clear">lirtp',
                paging: true,
                autoWidth: true,
                responsive: true,
                buttons: [
                    "colvis",
                    "copyHtml5",
                    "csvHtml5",
                    "excelHtml5",
                    "pdfHtml5",
                    {
                        extend: 'print',
                        customize: function (win) {
                            $(win.document.body)
                                .css('font-size', '10pt')
                                .prepend('<img src="http://datatables.net/media/images/logo-fade.png" style="position:absolute; top:0; left:0;" />');

                            $(win.document.body)
                                .find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }
                    }
                ],
                initComplete: function (settings, json) {
                    var footer = $("#example3 tfoot tr");
                    $("#example3 thead").append(footer);
                }
            });


            // Apply the search
            $("#example3 thead").on("keyup", "input", function () {
                var columnIndex = $(this).parent().index();
                if (columnIndex !== 6) { // Skip the "Action" column (index 6)
                    table.column(columnIndex)
                        .search(this.value)
                        .draw();
                }
            });

            $('#example3 tbody').on('click', '.view-btn', function () {
                // Your edit button logic here
                var newsID = $(this).data('news-id');
                // Example action, replace with your logic
                // console.log('Edit button clicked for category ID:', newsID);
                $.ajax({
                    url: 'DataGet/get_news.php', // PHP script to fetch data from the server
                    method: 'POST',
                    data: { newsID: newsID },
                    success: function (response) {
                        // Insert the HTML into the modal body
                        $('#newsBody').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            $('#example3 tbody').on('click', '.edit-btn', function () {
                // Your edit button logic here
                var newsID = $(this).data('news-id');
                // Example action, replace with your logic
                // console.log('Edit button clicked for category ID:', newsID);

            });
        });
    </script>

    <!--Table Example 4  -->
    <script>
        $(document).ready(function () {
            //Only needed for the filename of export files.
            //Normally set in the title tag of your page.

            // Create search inputs in footer
            $("#example4 tfoot th").each(function (index) {
                var title = $(this).text();
                if (index !== 4) { // Skip the "Action" column (index 6)
                    $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                } else {
                    $(this).empty(); // Remove label for "Action" column
                }
            });
            // DataTable initialisation
            var table = $("#example4").DataTable({
                ordering: false,
                dom: '<"dt-buttons"Bf><"clear">lirtp',
                paging: true,
                autoWidth: true,
                responsive: true,
                buttons: [
                    "colvis",
                    "copyHtml5",
                    "csvHtml5",
                    "excelHtml5",
                    "pdfHtml5",
                    {
                        extend: 'print',
                        customize: function (win) {
                            $(win.document.body)
                                .css('font-size', '10pt')
                                .prepend('<img src="http://datatables.net/media/images/logo-fade.png" style="position:absolute; top:0; left:0;" />');

                            $(win.document.body)
                                .find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }
                    }
                ],
                initComplete: function (settings, json) {
                    var footer = $("#example4 tfoot tr");
                    $("#example4 thead").append(footer);
                }
            });

            // Apply the search
            $("#example4 thead").on("keyup", "input", function () {
                var columnIndex = $(this).parent().index();
                if (columnIndex !== 6) { // Skip the "Action" column (index 6)
                    table.column(columnIndex)
                        .search(this.value)
                        .draw();
                }
            });

            $('#example4 tbody').on('click', '.view-btn', function () {
                // Your edit button logic here
                var newsID = $(this).data('news-id');
                // Example action, replace with your logic
                // console.log('Edit button clicked for category ID:', newsID);
                $.ajax({
                    url: 'DataGet/get_news.php', // PHP script to fetch data from the server
                    method: 'POST',
                    data: { newsID: newsID },
                    success: function (response) {
                        // Insert the HTML into the modal body
                        $('#newsBody').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            $('#example4 tbody').on('click', '.edit-btn', function () {
                // Your edit button logic here
                var newsID = $(this).data('news-id');
                // Example action, replace with your logic
                // console.log('Edit button clicked for category ID:', newsID);

            });
        });
    </script>

    <!-- ADD DATA -->
    <script>
        document.getElementById('promoPackForm').addEventListener('submit', function (event) {
            event.preventDefault();

            const eventTitle = document.getElementById('eventTitle').value.trim();
            const eventDesc = document.getElementById('eventDesc').value.trim();
            const image1 = document.getElementById('image1').files[0];
            const deadlinePromo = document.getElementById('deadlinePromo').value.trim();




            const eventTitleIn = document.getElementById('eventTitle');
            const eventDescIn = document.getElementById('eventDesc');
            const image1In = document.getElementById('image1');
            const deadlinePromoIn = document.getElementById('deadlinePromo');

            const titleError = document.getElementById('titleError');
            const descError = document.getElementById('descError');
            const image1Error = document.getElementById('image1Error');
            const deadlineError = document.getElementById('deadlineError');

            let isValid = true;

            // Clear previous error messages
            titleError.textContent = '';
            descError.textContent = '';
            image1Error.textContent = '';
            const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i; // Add more if needed
            var regex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?0-9]/;

            // Validate news title
            if (!eventTitle) {
                titleError.textContent = 'Title is required';
                eventTitleIn.style.borderColor = 'red';
                isValid = false;
            } else if (regex.test(eventTitle)) {
                titleError.textContent = 'Special characters and numbers are not allowed.';
                eventTitleIn.style.borderColor = 'red';
                isValid = false;
            }
            else {
                titleError.textContent = '';
                eventTitleIn.style.borderColor = '';
                isValid = true;
            }

            // Validate event description
            if (!eventDesc) {
                descError.textContent = 'Description is required';
                eventDescIn.style.borderColor = 'red';
                isValid = false;
            } else {
                descError.textContent = '';
                eventDescIn.style.borderColor = '';
                isValid = true;
            }



            // Validate image1
            if (!image1) {
                image1Error.textContent = 'Image is required';
                image1In.style.borderColor = "red";
                isValid = false;
            } else if (!allowedExtensions.exec(image1.name)) {
                image1Error.textContent = 'Invalid file type. Please upload an image with .jpg, .jpeg, or .png extension.';
                image1In.style.borderColor = "red";
                isValid = false;
            }
            else {
                image1Error.textContent = '';
                image1In.style.borderColor = "";
                isValid = true;

                // Validate news title
                if (!deadlinePromo) {
                    deadlineError.textContent = 'Deadline is required';
                    deadlinePromoIn.style.borderColor = 'red';
                    isValid = false;
                }
                else {
                    deadlineError.textContent = '';
                    deadlinePromoIn.style.borderColor = '';
                    isValid = true;
                }
            }


            // Validate news title
            if (!deadlinePromo) {
                deadlineError.textContent = 'Deadline is required';
                deadlinePromoIn.style.borderColor = 'red';
                isValid = false;
            }
            else {
                deadlineError.textContent = '';
                deadlinePromoIn.style.borderColor = '';
                isValid = true;
            }

            // If the form is not valid, exit the function
            if (!isValid) {
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('eventTitle', eventTitle);
            formData.append('eventDesc', eventDesc);
            formData.append('image1', image1);
            formData.append('deadlinePromo', deadlinePromo);


            // Send form data to the server using AJAX
            $.ajax({
                url: 'DataAdd/add_promo.php', // Replace with your server-side script URL
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
                        $('#addEventModal').modal('hide');
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

            // Close the modal
            $('#addEventModal').modal('hide');
        });
    </script>
    <!-- View dep -->
    <script>
        $(document).ready(function () {
            $('.btn-primary').click(function () {
                var eventid = $(this).data('news-id');

                // Make an AJAX request to fetch data from the server
                $.ajax({
                    url: 'DataGet/get_event.php', // PHP script to fetch data from the server
                    method: 'POST',
                    data: { eventid: eventid },
                    success: function (response) {
                        // Insert the HTML into the modal body
                        $('#newsBody').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>

    <!-- Delete -->
    <script>
        $(document).ready(function () {
            // Use event delegation to handle clicks on any delete button
            $(document).on('click', '#deleteBtn', function (e) {
                // Get the event ID from the button's data attribute
                var newsID = $(this).data('news-id');

                // Show confirmation dialog
                Swal.fire({
                    title: 'Confirm',
                    text: "Are you sure you want to delete this news?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            // Prompt for authentication
                            const { value: username } = await Swal.fire({
                                title: "Authentication",
                                input: "text",
                                inputAttributes: {
                                    autocapitalize: "off"
                                },
                                showCancelButton: true,
                                confirmButtonText: "Continue",
                                showLoaderOnConfirm: true,
                                preConfirm: async (username) => {
                                    try {
                                        // Retrieve session username using PHP
                                        const sessionUsername = "<?php echo isset($_SESSION['Username']) ? $_SESSION['Username'] : ''; ?>";
                                        if (username !== sessionUsername) {
                                            throw new Error("Username doesn't match the session username");
                                        }
                                        return { username: sessionUsername };
                                    } catch (error) {
                                        Swal.showValidationMessage(error.message);
                                    }
                                },
                                allowOutsideClick: () => !Swal.isLoading()
                            });

                            if (username) {
                                // Prompt for deletion message
                                const { value: message } = await Swal.fire({
                                    title: 'Message',
                                    input: 'textarea',
                                    inputAttributes: {
                                        autocapitalize: 'off'
                                    },
                                    showCancelButton: true,
                                    confirmButtonText: 'Continue',
                                    showLoaderOnConfirm: true,
                                    preConfirm: (message) => {
                                        return $.ajax({
                                            type: 'POST',
                                            url: 'DataDelete/delete_news.php',
                                            data: { 'newsID': newsID, 'message': message },
                                            dataType: 'json'
                                        });
                                    }
                                });

                                // Handle deletion response
                                if (message && message.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: message.success,
                                        onClose: () => {
                                            window.location.reload();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'An error occurred while deleting the event. Please try again.'
                                    });
                                }
                            }
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'An error occurred. Please try again.'
                            });
                        }
                    }
                });
            });
        });
    </script>

    <!-- Edit Request-->
    <script>
        $(document).ready(function () {
            $(document).on('click', '#editBtn', function (e) {
                var newsID = $(this).data('news-id');
                var author = $(this).data('author');
                var decision = $(this).data('decision');

                Swal.fire({
                    title: 'Confirm',
                    text: "Are you sure you want to request an edit?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
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
                                // Send AJAX request to update event status and insert message
                                return $.ajax({
                                    type: 'POST',
                                    url: 'DataUpdate/request.php',
                                    data: {
                                        'newsID': newsID,
                                        'message': message,
                                        'author': author,
                                        'decision': decision
                                    }, // Pass the event ID and message to the server
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
                                        text: 'An error occurred while updating event information. Please try again.'
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

    <!-- Edit Admin-->
    <script>
        $(document).ready(function () {
            $('.edit-event').click(function () {
                var newsID = $(this).data('news-id');

                // Make an AJAX request to fetch data from the server
                $.ajax({
                    url: 'DataGet/edit_news.php', // PHP script to fetch data from the server
                    method: 'POST',
                    data: { newsID: newsID },
                    success: function (response) {
                        // Insert the HTML into the modal body
                        $('#newsEditForm').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>

    <!-- Approved request -->
    <script>
        $(document).ready(function () {
            $(document).on('click', '#approvedRequest', function (e) {
                e.preventDefault();
                var newsID = $(this).data('news-id');
                var author = $(this).data('author');
                var decision = $(this).data('decision');
                var eventTitle = $(this).data('news-title');


                Swal.fire({
                    title: 'Confirm',
                    text: "Are you sure you want to approve the request?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: 'DataUpdate/approveReqNews.php',
                            data: {
                                'newsID': newsID,
                                'author': author,
                                'decision': decision,
                                'eventTitle': eventTitle
                            },
                            dataType: 'json',
                            success: function (response) {
                                // Handle success scenario
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.success,
                                    icon: 'success'
                                }).then(() => {
                                    // Optionally reload or update the page
                                    location.reload();
                                });
                            },
                            error: function (xhr, status, error) {
                                // Handle error scenario
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'There was an error processing your request.',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });

    </script>

    <!-- Reject request -->
    <script>
        $(document).ready(function () {
            $(document).on('click', '#rejectRequest', function (e) {
                e.preventDefault();
                var newsID = $(this).data('news-id');
                var author = $(this).data('author');
                var newsTitle = $(this).data('news-title');


                Swal.fire({
                    title: 'Confirm',
                    text: "Are you sure you want to reject the request?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: 'DataUpdate/rejectReqNews.php',
                            data: {
                                'newsID': newsID,
                                'author': author,
                                'newsTitle': newsTitle
                            },
                            dataType: 'json',
                            success: function (response) {
                                // Handle success scenario
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.success,
                                    icon: 'success'
                                }).then(() => {
                                    // Optionally reload or update the page
                                    location.reload();
                                });
                            },
                            error: function (xhr, status, error) {
                                // Handle error scenario
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'There was an error processing your request.',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });

    </script>




</body>

</html>