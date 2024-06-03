<?php
require ('config/db_con.php');
include ('security.php');

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


?>
<?php
$check = '';

if ($checkStatus == 0) {
    $check = 'checked';

    $confirm = 'Are you sure the maintenace is complete';
} else {
    $confirm = "Turning on maintenance mmode disbaled all account types except the super admin account";
}

?>




<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Maintenance</title>
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

    <link rel="stylesheet" href="assets/css/animation.css" />

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



        .switch {
            position: relative;
            display: inline-block;
            vertical-align: top;
            width: 56px;
            height: 20px;
            /* padding: 3px; */
            padding: 12px 3px 12px 3px;
            background-color: white;
            border-radius: 18px;
            box-shadow: inset 0 -1px white, inset 0 1px 1px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            background-image: -webkit-linear-gradient(top, #eeeeee, white 25px);
            background-image: -moz-linear-gradient(top, #eeeeee, white 25px);
            background-image: -o-linear-gradient(top, #eeeeee, white 25px);
            background-image: linear-gradient(to bottom, #eeeeee, white 25px);
        }

        .switch-input {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
        }

        .switch-label {
            position: relative;
            display: block;
            height: inherit;
            font-size: 10px;
            text-transform: uppercase;
            background: #eceeef;
            bottom: 10px;
            border-radius: inherit;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.12), inset 0 0 2px rgba(0, 0, 0, 0.15);
            -webkit-transition: 0.15s ease-out;
            -moz-transition: 0.15s ease-out;
            -o-transition: 0.15s ease-out;
            transition: 0.15s ease-out;
            -webkit-transition-property: opacity background;
            -moz-transition-property: opacity background;
            -o-transition-property: opacity background;
            transition-property: opacity background;
        }

        .switch-label:before,
        .switch-label:after {
            position: absolute;
            top: 50%;
            margin-top: -.5em;
            line-height: 1;
            -webkit-transition: inherit;
            -moz-transition: inherit;
            -o-transition: inherit;
            transition: inherit;
        }

        .switch-label:before {
            content: attr(data-off);
            right: 11px;
            color: #aaa;
            text-shadow: 0 1px rgba(255, 255, 255, 0.5);
        }

        .switch-label:after {
            content: attr(data-on);
            left: 11px;
            color: white;
            text-shadow: 0 1px rgba(0, 0, 0, 0.2);
            opacity: 0;
        }

        .switch-input:checked~.switch-label {
            background: #47a8d8;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), inset 0 0 3px rgba(0, 0, 0, 0.2);
        }

        .switch-input:checked~.switch-label:before {
            opacity: 0;
        }

        .switch-input:checked~.switch-label:after {
            opacity: 1;
        }

        .switch-handle {
            position: absolute;
            top: 3px;
            left: 4px;
            width: 18px;
            height: 18px;
            background: white;
            border-radius: 10px;
            box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
            background-image: -webkit-linear-gradient(top, white 40%, #f0f0f0);
            background-image: -moz-linear-gradient(top, white 40%, #f0f0f0);
            background-image: -o-linear-gradient(top, white 40%, #f0f0f0);
            background-image: linear-gradient(to bottom, white 40%, #f0f0f0);
            -webkit-transition: left 0.15s ease-out;
            -moz-transition: left 0.15s ease-out;
            -o-transition: left 0.15s ease-out;
            transition: left 0.15s ease-out;
        }

        .switch-handle:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            margin: -6px 0 0 -6px;
            width: 12px;
            height: 12px;
            background: #f9f9f9;
            border-radius: 6px;
            box-shadow: inset 0 1px rgba(0, 0, 0, 0.02);
            background-image: -webkit-linear-gradient(top, #eeeeee, white);
            background-image: -moz-linear-gradient(top, #eeeeee, white);
            background-image: -o-linear-gradient(top, #eeeeee, white);
            background-image: linear-gradient(to bottom, #eeeeee, white);
        }

        .switch-input:checked~.switch-handle {
            left: 33px;
            box-shadow: -1px 1px 5px rgba(0, 0, 0, 0.2);
        }

        .switch-green>.switch-input:checked~.switch-label {
            background: #4fb845;
        }
    </style>
</head>

<body>
    <!-- <
include('session_out.php');
?> -->

    <!-- partial:index.partial.html -->
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
                        echo '<a class="nav-link" href="maintenance.php">';
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
                        echo '<a class="nav-link collapsed" href="category.php">';
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
                        echo '<a class="nav-link collapsed" href="service.php">';
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
            <h1>Maintenance</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                    <li class="breadcrumb-item">Maintenance</li>

                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <div>


                                <div class="d-flex justify-content-end pt-4">
                                    <label class="switch">


                                        <input type="checkbox" id="togle" class="switch-input"
                                            onclick="confirmOn(event)" <?php echo $check; ?>>

                                        <span class="switch-label" data-on="On" data-off="Off"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>


                                <?php
                                // Assuming you have established a database connection
                                
                                // Check if UserID is set in the session
                                if (isset($_SESSION['UserID'])) {
                                    // Prepare the SQL query with proper concatenation
                                    $sql = "SELECT Action_Add FROM `privileges` WHERE UserID = '" . $_SESSION['UserID'] . "'";

                                    // Execute the SQL query
                                    $result = mysqli_query($conn, $sql);

                                    // Check if query was successful
                                    if ($result) {
                                        $row = mysqli_fetch_assoc($result);
                                        // Display the button if Action_Add is 1
                                        if ($row['Action_Add'] == 1) {



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





                                <div class="bootstrap-3-table">

                                    <table id="example" class="table table-striped table-bordered" cellspacing="0"
                                        width="100%">
                                        <thead>
                                            <tr>

                                                <th>User</th>
                                                <th>Status</th>
                                                <th>Activity</th>
                                                <th>Start Date and Time</th>
                                                <th>End Date and Time</th>
                                                <th>Total Hours/Days</th>



                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            require ('config/db_con.php');
                                            $table = mysqli_query($conn, "SELECT m.*, u.Fname, u.Lname FROM maintenance m INNER JOIN users u ON m.UserID = u.UserID ORDER BY MaintenanceID DESC");


                                            while ($row = mysqli_fetch_assoc($table)) {
                                                $status = $row['Status']
                                                    ?>
                                                <tr>


                                                    <td><?php echo $row['Fname'] . ' ' . $row['Lname']; ?></td>
                                                    <td>
                                                        <?php

                                                        if ($status == 0) {
                                                            echo 'Under maintenance';
                                                        } else {
                                                            echo 'Complete';
                                                        }


                                                        ?>
                                                        <?php
                                                        $start = new DateTime($row['StartDateTime']);
                                                        $end = $row['EndDate'] ? new DateTime($row['EndDate']) : null;

                                                        $intervalFormatted = '';
                                                        if ($end !== null) {
                                                            $interval = $start->diff($end);
                                                            $days = $interval->format('%a');
                                                            $hours = $interval->format('%h');
                                                            $minutes = $interval->format('%i');
                                                            $seconds = $interval->format('%s');

                                                            // Format days
                                                            $daysString = $days > 0 ? $days . ($days == 1 ? ' day ' : ' days ') : '';

                                                            // Format hours
                                                            $hoursString = $hours > 0 ? $hours . ($hours == 1 ? ' hour ' : ' hours ') : '';

                                                            // Format minutes
                                                            $minutesString = $minutes > 0 ? $minutes . ($minutes == 1 ? ' minute ' : ' minutes ') : '';

                                                            // Format seconds
                                                            $secondsString = $seconds > 0 ? $seconds . ($seconds == 1 ? ' second' : ' seconds') : '';

                                                            // Concatenate all parts
                                                            $intervalFormatted = $daysString . $hoursString . $minutesString . $secondsString;
                                                        }
                                                        ?>



                                                    </td>
                                                    <td><?php echo $row['Activity']; ?></td>
                                                    <td><?php echo (new DateTime($row['StartDateTime']))->format('F j, Y   h:i:s A'); ?>
                                                    </td>
                                                    <?php
                                                    if ($row['Status'] != 0) {
                                                        echo '<td>' . date('F j, Y   h:i:s A', strtotime($row['EndDate'])) . '</td>';
                                                    } else {
                                                        echo '<td></td>'; // Display blank if Status is 0
                                                    }
                                                    ?>



                                                    <td>
                                                        <?php echo $intervalFormatted; ?>
                                                    </td>



                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>User</th>
                                                <th>Status</th>
                                                <th>Activity</th>
                                                <th>Start Date and Time</th>
                                                <th>End Date and Time</th>
                                                <th>Total Hours/Days</th>

                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </div>

                            <!-- End Table with stripped rows -->

                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main><!-- End #main -->


    <!-- View modal -->
    <div class="modal fade" id="viewUser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="exampleModalLgLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="exampleModalLgLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>


                </div>
                <div class="modal-body" id="modalBody">


                </div>
            </div>
        </div>
    </div>


    <!-- editmodal -->
    <div class="modal fade" id="editUser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="exampleModalLgLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="exampleModalLgLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>


                </div>
                <div class="modal-body" id="modalBody1">


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


    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>



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



    <!-- DataTable -->
    <script>
        $(document).ready(function () {
            //Only needed for the filename of export files.
            //Normally set in the title tag of your page.
            document.title = "Maintenance";
            // Create search inputs in footer
            $("#example tfoot th").each(function (index) {
                var title = $(this).text();
                if (index === 3) { // Check if it's the column where you want to have date search
                    $(this).html('<input type="date" placeholder="Search ' + title + '" class="date-search"/>');
                } else if (index === 4) { // Check if it's the column where you want to have date search
                    $(this).html('<input type="date" placeholder="Search ' + title + '" class="date-search"/>');
                }
                else if (index !== 6) { // Skip the "Action" column (index 6)
                    $(this).html('<input type="text" placeholder="Search ' + title + '" class="text-search"/>');
                } else {
                    $(this).empty(); // Remove label for "Action" column
                }
            });

            $('.date-search').on('change', function () {
                // Get the value of the date input
                var dateValue = $(this).val();
                // Do something with the date value, like filtering your data table
                // For example:
                table.column(3).search(dateValue).draw();
            });


            // DataTable initialisation
            var table = $("#example").DataTable({

                dom: '<"dt-buttons"Bf><"clear">lirtp',
                paging: true,
                autoWidth: true,
                buttons: [
                    {
                        extend: 'colvis',
                        text: '<i class="bi bi-columns"></i>', // Bootstrap icon for column visibility
                        titleAttr: 'Column visibility'
                    },
                    {
                        extend: 'copyHtml5',
                        text: '<i class="bi bi-clipboard"></i>', // Bootstrap icon for copying to clipboard
                        titleAttr: 'Copy to clipboard'
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i>', // Bootstrap icon for exporting to CSV
                        titleAttr: 'Export to CSV'
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-earmark-excel"></i>', // Bootstrap icon for exporting to Excel
                        titleAttr: 'Export to Excel'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="bi bi-file-earmark-pdf"></i>', // Bootstrap icon for exporting to PDF
                        titleAttr: 'Export to PDF'
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i>', // Bootstrap icon for printing
                        titleAttr: 'Print'
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
        });
    </script>


    <!-- <script>
    function confirmOn(event) {
        var checkBox = event.target;
        if (checkBox.checked) {
            var confirmMsg = "Are you sure you want to perform this action?";
            if (!confirm(confirmMsg)) {
                checkBox.checked = false; // Uncheck the checkbox if user cancels
            }
        }
    }
</script> -->
    <!-- <script>
        function confirmOn(event) {
            var checkBox = event.target;
            if (checkBox.checked) {
                checkBox.checked = false;
                var confirmMsg = "Are you sure you want to perform this action?";
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
                    Swal.fire({
                        title: "Type the reason",
                        input: "text",
                        
                        inputAttributes: {
                            autocapitalize: "off"
                        },
                        showCancelButton: true,
                        confirmButtonText: "Submit",
                        showLoaderOnConfirm: true,
                        preConfirm: async (reason) => {
                            try {
                                if (!reason) {
                                    throw new Error("Please input remarks");
                                }
                                return { reason }; // return the reason as an object
                            } catch (error) {
                                Swal.showValidationMessage(error.message);
                            }
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                        const { reason } = result.value; 
                        if (reason) {
                            checkBox.checked = true;
                            $.ajax({
                                type: 'POST',
                                url: 'DataAdd/insert_activity.php',
                                data: JSON.stringify({ reason: reason }), // Send data as JSON
                                contentType: 'application/json', // Specify content type as JSON
                                success: function (response) {
                                    // Reload the page upon successful submission
                                    location.reload();
                                },
                                error: function () {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'An error occurred while submitting data.',
                                    });
                                },
                            });
                        } else {
                            Swal.fire("Error", "No reason provided", "error");
                        }
                       
                    }

                    });

                    if (!result.isConfirmed) {
                        checkBox.checked = false; // Uncheck the checkbox if user cancels
                    }
                });
            }
        }
    </script> -->

    <!-- <script>
        function confirmOn(event) {
            var checkBox = event.target;

            if (checkBox.checked) {
                checkBox.checked = false;
                var confirmMsg = "Are you sure you want to on the on the maintenance?";
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
                        Swal.fire({
                            title: "Type the reason",
                            input: "text",
                            inputAttributes: {
                                autocapitalize: "off"
                            },
                            showCancelButton: true,
                            confirmButtonText: "Submit",
                            showLoaderOnConfirm: true,
                            preConfirm: async (reason) => {
                                try {
                                    if (!reason) {
                                        throw new Error("Please input remarks");
                                    }
                                    return { reason }; // return the reason as an object
                                } catch (error) {
                                    Swal.showValidationMessage(error.message);
                                }
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const { reason } = result.value;
                                if (reason) {
                                    checkBox.checked = true;
                                    $.ajax({
                                        type: 'POST',
                                        url: 'DataAdd/insert_activity.php',
                                        data: JSON.stringify({ reason: reason }), // Send data as JSON
                                        contentType: 'application/json', // Specify content type as JSON
                                        success: function (response) {
                                            // Reload the page upon successful submission
                                            location.reload();
                                        },
                                        error: function () {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: 'An error occurred while submitting data.',
                                            });
                                        },
                                    });
                                } else {
                                    Swal.fire("Error", "No reason provided", "error");
                                }
                            } else {
                                checkBox.checked = false; // Uncheck the checkbox if user cancels
                            }
                        });
                    }
                    else {
                        checkBox.checked = false; // Uncheck the checkbox if user cancels
                    }
                });
            }
            if (checkBox) {
                checkBox.checked = true;
                var confirmed = Swal.fire({
                    title: 'Confirmation',
                    text: 'Are you sure the maintenance is complete?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // event.target.checked = false;
                        $.ajax({
                            url: "DataAdd/update_activity.php",
                            success: function (response) {
                                console.log("sucess");
                                location.reload();
                            }

                        });
                    }
                    else {
                        event.preventDefault();
                        checkBox.checked = true;

                    }

                })
                // var confirmed = Swal.fire("Confirmation", "Are you sure you the maintenance is complete?", "warning");
                // if (confirmed) {
                //     // If the user confirms, uncheck the checkbox
                //     event.target.checked = false;
                // } else {
                //     // If the user cancels, keep the checkbox checked
                //     event.preventDefault();
                // }


            }
        }
    </script> -->

    <script>
        function confirmOn(event) {
            var checkBox = event.target;

            if (checkBox.checked) {
                checkBox.checked = false;
                var confirmMsg = "Are you sure you want to turn on maintenance?";
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
                        Swal.fire({
                            title: "Type the reason",
                            input: "text",
                            inputAttributes: {
                                autocapitalize: "off"
                            },
                            showCancelButton: true,
                            confirmButtonText: "Submit",
                            showLoaderOnConfirm: true,
                            preConfirm: async (reason) => {
                                try {
                                    if (!reason) {
                                        throw new Error("Please input remarks");
                                    }
                                    return { reason }; // return the reason as an object
                                } catch (error) {
                                    Swal.showValidationMessage(error.message);
                                }
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const { reason } = result.value;
                                if (reason) {
                                    checkBox.checked = true;
                                    $.ajax({
                                        type: 'POST',
                                        url: 'DataAdd/insert_activity.php',
                                        data: JSON.stringify({ reason: reason }), // Send data as JSON
                                        contentType: 'application/json', // Specify content type as JSON
                                        success: function (response) {
                                            // Reload the page upon successful submission
                                            location.reload();
                                        },
                                        error: function () {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: 'An error occurred while submitting data.',
                                            });
                                        },
                                    });
                                } else {
                                    Swal.fire("Error", "No reason provided", "error");
                                }
                            } else {
                                checkBox.checked = false; // Uncheck the checkbox if user cancels
                            }
                        });
                    }
                    else {
                        checkBox.checked = false; // Uncheck the checkbox if user cancels
                    }
                });
            } else {
                checkBox.checked = true;
                var confirmMsg = "Are you sure the maintenance is complete?";
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
                        $.ajax({
                            url: "DataAdd/update_activity.php",
                            success: function (response) {
                                console.log("success");
                                location.reload();
                            },
                            error: function () {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while updating data.',
                                });
                            }
                        });
                    } else {
                        checkBox.checked = false;
                    }
                });
            }
        }
    </script>



    <!-- <script>
        function confirmOff(event) {
            var checkBox = event.target;
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'Are you sure the maintenace is complete?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If the user confirms, proceed with the action
                    $.ajax({
                        url: "DataAdd/update_activity.php",
                        success: function (response) {
                             console.log("sucess");
                             location.reload();
                        }

                    });
                }

                else {
                    // If the user cancels, revert the checkbox back to its previous state
                    checkBox.checked = checkBox.checked;
                }
            });
        }
    </script>
 -->

</body>

</html>