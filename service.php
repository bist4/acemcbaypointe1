<?php
require ('config/db_con.php');
include ('security.php');

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
$q = $conn -> query($sql);
$row = $q->fetch_assoc();
$checkStatus = $row['Status']; 

if($_SESSION['UserRoleName'] != '0'){
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

    <title>Account Management</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/logo2.png" rel="icon">
    <link href="assets/img/logo2.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
   

    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">


    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
 


    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">




    <!-- Dtatables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.6/css/dataTables.bootstrap5.min.css">

    <link rel='stylesheet' href='bootsrap3.css'>
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
                <img src="assets/img/logo2.png" alt="">
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

                        <img src="DataAdd/uploads/<?php
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
                            <a class="dropdown-item d-flex align-items-center" href="logout.php">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>

                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link collapsed" href="home.php">
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
                        echo '<a class="nav-link collapsed" href="accounts.php">';
                        echo '<i class="bi bi-person"></i>';
                        echo '<span>Account Management</span>';
                        echo '</a>';
                        echo '</li>';
                    }

                    if ($row['ModuleName'] == 'Inbox') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link collapsed" href="inbox.php">';
                        echo '<i class="bi bi-inbox"></i>';
                        echo '<span>Inbox</span>';
                        echo '</a>';
                        echo '</li>';
                    }

                    if ($row['ModuleName'] == 'Maintenance') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link collapsed" href="maintenance.php">';
                        echo '<i class="bi bi-gear"></i>';
                        echo '<span>Maintenance</span>';
                        echo '</a>';
                        echo '</li>';
                    }


                    // Check for Site Options module and construct sub-navigation items
                    if ($row['ModuleName'] == 'Title & Slogan' || $row['ModuleName'] == 'Company Profile' || $row['ModuleName'] == 'Social Media' || $row['ModuleName'] == 'Copyright' || $row['ModuleName'] == 'Contact Information') {
                        $siteOptionsSubMenu .= '<li>';
                        $siteOptionsSubMenu .= '<a href="site_options/' . strtolower(str_replace(' ', '_', $row['ModuleName'])) . '.php">';
                        $siteOptionsSubMenu .= '<i class="bi bi-circle"></i><span>' . $row['ModuleName'] . '</span>';
                        $siteOptionsSubMenu .= '</a>';
                        $siteOptionsSubMenu .= '</li>';
                    }

                    // Check for Page Options module and construct sub-navigation items
                    // if ($row['ModuleName'] == 'Add New Page') {
                    //     $pageOptionsSubMenu .= '<li>';
                    //     $pageOptionsSubMenu .= '<a href="pages/' . strtolower(str_replace(' ', '_', $row['ModuleName'])) . '.php">';
                    //     $pageOptionsSubMenu .= '<i class="bi bi-circle"></i><span>' . $row['ModuleName'] . '</span>';
                    //     $pageOptionsSubMenu .= '</a>';
                    //     $pageOptionsSubMenu .= '</li>';
                    // }

                    if ($row['ModuleName'] == 'Event List' || $row['ModuleName'] == 'Event Request') {
                        $eventOptionsSubMenu .= '<li>';
                        $eventOptionsSubMenu .= '<a href="events/' . strtolower(str_replace(' ', '_', $row['ModuleName'])) . '.php">';
                        $eventOptionsSubMenu .= '<i class="bi bi-circle"></i><span>' . $row['ModuleName'] . '</span>';
                        $eventOptionsSubMenu .= '</a>';
                        $eventOptionsSubMenu .= '</li>';
                    }

                    if ($row['ModuleName'] == 'Event Super Admin Logs' || $row['ModuleName'] == 'Event Admin Logs' || $row['ModuleName'] == 'Event User Logs' || $row['ModuleName'] == 'Event Doctors Logs') {
                        $logOptionsSubMenu .= '<li>';
                        $logOptionsSubMenu .= '<a href="logs/' . strtolower(str_replace(' ', '_', $row['ModuleName'])) . '.php">';
                        $logOptionsSubMenu .= '<i class="bi bi-circle"></i><span>' . $row['ModuleName'] . '</span>';
                        $logOptionsSubMenu .= '</a>';
                        $logOptionsSubMenu .= '</li>';
                    }



                    if ($row['ModuleName'] == 'Category Options') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link  collapsed" href="category.php">';
                        echo '<i class="bi bi-justify-left"></i>';
                        echo '<span>Category Options</span>';
                        echo '</a>';
                        echo '</li>';
                    }

                    if ($row['ModuleName'] == 'Department Options') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link collapsed" href="department.php">';
                        echo '<i class="bi bi-building"></i>';
                        echo '<span>Department Options</span>';
                        echo '</a>';
                        echo '</li>';
                    }

                    if ($row['ModuleName'] == 'Service Options') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link " href="service.php">';
                        echo '<i class="bi bi-life-preserver"></i>';
                        echo '<span>Service Options</span>';
                        echo '</a>';
                        echo '</li>';
                    }


                    if ($row['ModuleName'] == 'Activity History') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link collapsed" href="activity_history.php">';
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
                    echo '<i class="bi bi-calendar4"></i><span>Event Options</span><i class="bi bi-chevron-down ms-auto"></i>';
                    echo '</a>';
                    echo '<ul id="event-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">';
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
            <h1>Service Options</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                    <li class="breadcrumb-item">Service Options</li>

                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <?php
                            // Check if UserID is set in the session
                            if (isset($_SESSION['UserID'])) {
                                // Prepare the SQL query with proper concatenation
                                $sql = "SELECT Action_Add, ModuleID FROM `privileges` WHERE ModuleID = 12 AND UserID = '" . $_SESSION['UserID'] . "'";

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
                           
                            <!-- Table with stripped rows -->
                            <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">

                                <thead>
                                    <tr>
                                        <!-- <th>No.</th> -->
                                        <th>Service Title</th>
                                        <th>Description</th>
                                        <th>Doctors</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    require ('config/db_con.php');
                                    $table = mysqli_query($conn, "SELECT  s.*, ds.* FROM services s
                                    INNER JOIN doctors ds ON s.Doctors = ds.DoctorID
                                    WHERE s.Active = 1 ORDER BY s.ServiceID DESC");

                                    // $serialNo = 1;
                                    while ($row = mysqli_fetch_assoc($table)) {
                                        ?>
                                        <tr>
                                            <!-- <td><?php echo $serialNo++; ?></td> -->
                                            <td><?php echo $row['Title']; ?></td>
                                            <td><?php echo $row['Description']; ?></td>
                                            <!-- <td> <img src="DataAdd/uploads/service/icons/<?php echo $row["Icon"]; ?>" width = 200 title="<?php echo $row['Icon']; ?>"> </td> -->

                                            <td>Dr. <?php echo $row['Name']; ?></td>


                                            <td>
                                                <div class="d-inline-flex gap-3">
                                                    <!-- <div data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        data-bs-title="Edit">
                                                        <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                                            data-bs-target="#editUser">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    </div> -->
                                                    <?php
                                                    if (isset($_SESSION['UserID'])) {
                                                        // Prepare the SQL query with proper concatenation
                                                        $sql = "SELECT Action_View, ModuleID FROM `privileges` WHERE ModuleID = 12  AND UserID = '" . $_SESSION['UserID'] . "'";

                                                        // Execute the SQL query
                                                        $result = mysqli_query($conn, $sql);

                                                        // Check if query was successful
                                                        if ($result) {
                                                            $row_privileges = mysqli_fetch_assoc($result);
                                                            // Display the button if Action_Add is 1
                                                            if ($row_privileges['Action_View'] == 1) {
                                                                echo '<button type="button" class="btn btn-primary view-btn"  
                                                                data-bs-toggle="modal" data-bs-target="#viewServ" 
                                                                    data-service-id="' . $row['ServiceID'] . '">
                                                                    <i class="bi bi-eye"></i>  
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
                                                        $sql = "SELECT Action_Update, ModuleID FROM `privileges` WHERE ModuleID = 12  AND UserID = '" . $_SESSION['UserID'] . "'";

                                                        // Execute the SQL query
                                                        $result = mysqli_query($conn, $sql);

                                                        // Check if query was successful
                                                        if ($result) {
                                                            $row_privileges = mysqli_fetch_assoc($result);
                                                            // Display the button if Action_Add is 1
                                                            if ($row_privileges['Action_Update'] == 1) {
                                                                echo '<button type="button" class="btn btn-info edit-btn"  
                                                                data-bs-toggle="modal" data-bs-target="#editUser" 
                                                                    data-service-id="' . $row['ServiceID'] . '">
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
                                                        // Prepare the SQL query with proper concatenation
                                                        $sql = "SELECT Action_Delete, ModuleID FROM `privileges` WHERE ModuleID = 12  AND UserID = '" . $_SESSION['UserID'] . "'";

                                                        // Execute the SQL query
                                                        $result = mysqli_query($conn, $sql);

                                                        // Check if query was successful
                                                        if ($result) {
                                                            $row_privileges = mysqli_fetch_assoc($result);
                                                            // Display the button if Action_Add is 1
                                                            if ($row_privileges['Action_Delete'] == 1) {
                                                                echo '<div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Delete">
                                                                        <button type="button" 
                                                                        data-id="' . $row['ServiceID'] . '"
                                                                        data-name="' . $row['Title'] . '"
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
                                        <!-- <th>No.</th> -->
                                        <th>Service Title</th>
                                        <th>Description</th>
                                        <th>Doctors</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                            <!-- End Table with stripped rows -->

                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main><!-- End #main -->



    <!-- Modal For User -->
    <div class="modal fade" id="addUser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="exampleModalLgLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="exampleModalLgLabel">
                        Add Service
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="serviceForm" novalidate enctype="multipart/form-data">
                        <div class="mb-3">
                            <div class="row">
                                <div class="col">
                                    <label for="titleInput" class="form-label">Service Title</label>
                                    <input type="text" class="form-control" id="servTitle" aria-label="Service Title"
                                        name="servTitle" required>
                                    <span id="serviceError" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="invalid-feedback">
                                Only letters are allowed, special characters and numbers are not allowed.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ImageInput" class="form-label">Image</label>
                            <input class="form-control" type="file" id="ImageInput" name="Image" required>
                            <span id="ImageError" class="text-danger"></span>

                        </div>

                        <div class="mb-3">
                            <label for="iconInput" class="form-label">Icon</label>
                            <input class="form-control" type="file" id="iconInput" name="icon" required>
                            <span id="iconError" class="text-danger"></span>
                        </div>

                        <div class="mb-3">
                            <label for="descInput" class="form-label">Description</label>
                            <textarea class="form-control" rows="3" name="description" id="descInput"></textarea>
                            <span id="descError" class="text-danger"></span>
                        </div>

                        <div class="mb-3">
                            <label for="servicesInput" class="form-label">Services</label>
                            <textarea class="form-control" rows="3" name="services" id="servicesInput"></textarea>
                            <span id="servicesError" class="text-danger"></span>
                        </div>

                        <div class="mb-3">
                            <label for="doctorInput" class="form-label">Doctor</label>
                            <select class="form-select" aria-label="Default select example" name="DoctorID"
                                id="doctorSelect" required>
                                <option value="">Select Doctors</option>
                                <?php
                                require ('config/db_con.php');

                                // Fetch doctor data from the database
                                $query = "SELECT * FROM doctors WHERE Active = 1";
                                $result = mysqli_query($conn, $query);

                                // Check if there are any doctors
                                if (mysqli_num_rows($result) > 0) {
                                    // Output data of each row
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        // Output an option for each doctor
                                        echo '<option value="' . $row['DoctorID'] . '">Dr. ' . $row['Name'] . '</option>';
                                    }
                                } else {
                                    echo '<option disabled>No doctors found</option>';
                                }
                                ?>
                            </select>
                            <span id="doctorError" class="text-danger"></span>
                            <!-- Error message element for doctor selection -->
                        </div>


                        <div class="mb-3">
                            <label for="contactNum" class="form-label">Contact Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-phone-fill"></i></span>
                                <input type="text" id="contactNum" class="form-control" name="contactNum"
                                    placeholder="09 XXXX XXXX">
                                <span id="contactNumError" class="text-danger"></span>
                            </div>
                            <textarea class="form-control mt-2" rows="3" name="contactInfo" id="contactInput"
                                placeholder="Enter Information details"></textarea>
                            <span id="contactInfoError" class="text-danger"></span>
                        </div>



                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-end">
                                <a id="closeBtn" class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
                                <button class="btn btn-primary" id="addBtn" type="submit">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View modal -->
    <div class="modal fade" id="viewServ" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="exampleModalLgLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="exampleModalLgLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>


                </div>
                <div class="modal-body" id="servBody">


                </div>
            </div>
        </div>
    </div>

    <!-- Edit modal -->
    <div class="modal fade" id="editUser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="addCategoryModalLabel">
                        Edit Service
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">



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

    <!-- Vendor JS Files -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-contactnumber-form/validate.js"></script>
    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>


    <!-- 
    <script>
        $(document).ready(function () {
            $('#addBtn').click(function (e) {
                e.preventDefault();
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
                        url: 'DataAdd/add_service.php',
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
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Please fill in all required fields.'
                    });
                }
                form.classList.add('was-validated'); // Add 'was-validated' class to show validation errors
            });
        });
    </script> -->
    <!-- Add Data -->
    <script>
        document.getElementById('serviceForm').addEventListener('submit', function (event) {
            event.preventDefault();

            // Title Validation
            const serviceTitle = document.getElementById('servTitle').value.trim();
            const serviceTitleInput = document.getElementById('servTitle');
            const errorElement = document.getElementById('serviceError');

            // Image Upload Validation
            const ImageInput = document.getElementById('ImageInput');
            const iconInput = document.getElementById('iconInput');
            const ImageError = document.getElementById('ImageError');
            const iconError = document.getElementById('iconError');

            const Image1 = ImageInput.files[0];
            const iconImage = iconInput.files[0];
            const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i; // Add more if needed
            const allowedIconExtensions = /(\.svg|\.psd|\.png|\.eps)$/i;

            // Description and Services Input Validation
            const descInput = document.getElementById('descInput');
            const servicesInput = document.getElementById('servicesInput');
            const descError = document.getElementById('descError');
            const servicesError = document.getElementById('servicesError');

            // Doctor Selection Validation
            const doctorSelect = document.getElementById('doctorSelect');
            const doctorError = document.getElementById('doctorError');
            const selectedDoctor = doctorSelect.value;

            // Contact Information Input Validation
            const contactNum = document.getElementById('contactNum');
            const contactNumError = document.getElementById('contactNumError');
            const contactnumber = contactNum.value.trim();
            const contactnumberpattern = /^\d{10}$/;

            // Contact Information Input Validation
            const contactInput = document.getElementById('contactInput');
            const contactInfoError = document.getElementById('contactInfoError');
            const contactInfo = contactInput.value.trim();


            if (!serviceTitle) {
                errorElement.textContent = "Service Title is required";
                serviceTitleInput.style.borderColor = "red";
                return;
            } else {
                errorElement.textContent = ""; // Clear error message if service title is provided
                serviceTitleInput.style.borderColor = ""; // Reset border color
                if (!Image1) {
                    ImageError.textContent = "Image is required";
                    return;
                } else if (!allowedExtensions.test(Image1.name)) {
                    ImageError.textContent = "Invalid file type. Please upload an image with .jpg, .jpeg, or .png extension.";
                    return;
                } else {
                    ImageError.textContent = ""; // Clear error message if valid image is selected

                    if (!iconImage) {
                        iconError.textContent = "Icon is required";
                        return;
                    } else if (!allowedIconExtensions.test(iconImage.name)) {
                        iconError.textContent = "Invalid file type. Please upload an image with .svg, .psd, .png, or .eps extension.";
                        return;
                    } else {
                        iconError.textContent = ""; // Clear error message if valid icon is selected

                        if (!descInput.value.trim()) {
                            descError.textContent = "Description is required";
                            return;
                        } else {
                            descError.textContent = ""; // Clear error message if description is provided
                            if (!servicesInput.value.trim()) {
                                servicesError.textContent = "Services are required";
                                return;
                            } else {
                                servicesError.textContent = ""; // Clear error message if services are provided
                                if (!selectedDoctor) {
                                    doctorError.textContent = "Please select a doctor";
                                    return;
                                } else {
                                    doctorError.textContent = ""; // Clear error message if doctor is selected
                                    if (!contactnumber) {
                                        contactNumError.textContent = "Contact number is required";
                                        return;
                                    } else if (!contactnumberpattern.test(contactnumber)) {
                                        contactNumError.textContent = "Invalid contact number format";
                                        return;
                                    } else {
                                        contactNumError.textContent = ""; // Clear error message if valid contact number is entered
                                        if (!contactInfo) {
                                            contactInfoError.textContent = "Contact information is required";
                                            return;
                                        } else {
                                            contactInfoError.textContent = ""; // Clear error message if contact information is provided

                                            var formData = new FormData();

                                            formData.append('serviceTitle', serviceTitle);
                                            formData.append('Image1', Image1);
                                            formData.append('iconImage', iconImage);
                                            formData.append('description', descInput.value.trim());
                                            formData.append('services', servicesInput.value.trim());
                                            formData.append('doctorID', selectedDoctor);
                                            formData.append('contactNum', contactNum.value.trim());
                                            formData.append('contactInfo', contactInput.value.trim());


                                            $.ajax({
                                                type: 'POST',
                                                url: 'DataAdd/add_service.php',
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
                                }
                            }
                        }

                    }
                }
            }
        });
    </script>

    <!-- delete data -->
    <script>
        $(document).on('click', '.delete-danger', function () {
            var serviceName = $(this).data('name');
            var confirmMsg = "Are you sure you want to delete service name " + serviceName + "?";
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
                        url: "DataDelete/delete_service.php",
                        method: "POST",
                        data: { serviceid: id },
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


    <script>
        $(document).ready(function () {
            //Only needed for the filename of export files.
            //Normally set in the title tag of your page.
            document.title = "Service Options";
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
                if (columnIndex !== 6) { // Skip the "Action" column (index 6)
                    table.column(columnIndex)
                        .search(this.value)
                        .draw();
                }
            });
        });
    </script>

    <!-- View dep -->
    <script>
        $(document).ready(function () {
            $('.btn-primary').click(function () {
                var serviceid = $(this).data('service-id');

                // Make an AJAX request to fetch data from the server
                $.ajax({
                    url: 'DataGet/get_serv.php', // PHP script to fetch data from the server
                    method: 'POST',
                    data: { serviceid: serviceid },
                    success: function (response) {
                        // Insert the HTML into the modal body
                        $('#servBody').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>

    <!-- Edit data -->
    <script>
        $(document).ready(function () {
            $('.btn-info').click(function () {
                var serviceid = $(this).data('service-id');

                // Make an AJAX request to fetch data from the server
                $.ajax({
                    url: 'DataGet/edit_service.php', // PHP script to fetch data from the server
                    method: 'POST',
                    data: { serviceid: serviceid },
                    success: function (response) {
                        // Insert the HTML into the modal body
                        $('#modalBody').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>

</html>