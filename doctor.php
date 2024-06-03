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

    // Fetch the row
    if ($result) {
        $row = $result->fetch_assoc();
    } else {
        // Handle the case where the query fails
        echo "Error in fetching RoleID and UserID: " . $conn->error;
    }

    // Check if Action_Add is 1 (allowed) or 0 (not allowed)
    $actionAdd = $row['Action_Add'];
    $actionUpdate = $row['Action_Update'];
    $actionDelete = $row['Action_Delete'];
    $actionView = $row['Action_View'];
    $actionLock = $row['Action_Lock'];
    $actionUnlock = $row['Action_Unlock'];
    $actionModuleView = $row['AssignModule_View'];
    $actionModuleUpdate = $row['AssignModule_Update'];

    // Close the statement
    $stmt->close();

    // Close the database connection
    $conn->close();
}
if ($row['is_Lock'] == 1) {
    // User account is locked
    header("Location: lock_account.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Doctors Options</title>
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
            <a href="home.php" class="logo d-flex align-items-center">
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
                        <img src="DataAdd/uploads/<?php echo $row['ProfilePhoto']; ?>" alt="Profile"
                            class="rounded-circle">
                        <span class="d-none d-md-block dropdown-toggle ps-2">
                            <?php
                            // Assuming $row['Fname'] contains the first name and $row['Lname'] contains the last name
                            
                            // Get the first letter of Fname
                            $firstLetter = strtoupper(substr($row['Fname'], 0, 1));

                            echo $firstLetter;
                            ?>.
                            <?php echo $row['Lname'] ?>
                        </span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6> <?php
                            // Assuming $row['Fname'] contains the first name and $row['Lname'] contains the last name
                            
                            // Get the first letter of Fname
                            $firstLetter = strtoupper(substr($row['Fname'], 0, 1));

                            echo $firstLetter;
                            ?>.
                                <?php echo $row['Lname'] ?>
                            </h6>
                            <span><?php echo $row['UserRoleName'] ?></span>
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
            </li><!-- End Dashboard Nav -->

            <?php
            if ($row['ModuleName'] == 'Account Management') {
                echo '<li class="nav-item">';
                echo '<a class="nav-link collapsed" href="accounts.php">';
                echo '<i class="bi bi-person"></i>';
                echo '<span>Account Management</span>';
                echo '</a>';
                echo '</li>';
            }

            ?>



            <?php
            // Check if there are any modules to display
            if ($result->num_rows > 0) {
                // Define variables to hold sub-navigation items for Site Options
                $siteOptionsSubMenu = '';
                $pageOptionsSubMenu = '';
                $eventOptionsSubMenu = '';
                $logOptionsSubMenu = '';



                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    // Check for specific module names and generate navigation items accordingly
            


                    if ($row['ModuleName'] == 'Inbox') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link collapsed" href="inbox.php">';
                        echo '<i class="bi bi-inbox"></i>';
                        echo '<span>Inbox</span>';
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
                    if ($row['ModuleName'] == 'Add New Page') {
                        $pageOptionsSubMenu .= '<li>';
                        $pageOptionsSubMenu .= '<a href="pages/' . strtolower(str_replace(' ', '_', $row['ModuleName'])) . '.php">';
                        $pageOptionsSubMenu .= '<i class="bi bi-circle"></i><span>' . $row['ModuleName'] . '</span>';
                        $pageOptionsSubMenu .= '</a>';
                        $pageOptionsSubMenu .= '</li>';
                    }

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


                    if ($row['ModuleName'] == 'Doctors Options') {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link " href="doctor.php">';
                        echo '<i class="bi bi-person-bounding-box"></i>';
                        echo '<span>Doctors Options</span>';
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

                if ($pageOptionsSubMenu !== '') {
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link collapsed" data-bs-target="#page-nav" data-bs-toggle="collapse" href="#">';
                    echo '<i class="bi bi-stickies"></i><span>Page</span><i class="bi bi-chevron-down ms-auto"></i>';
                    echo '</a>';
                    echo '<ul id="page-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">';
                    echo $pageOptionsSubMenu; // Output the constructed sub-navigation items
                    echo '</ul>';
                    echo '</li>';
                }

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






                // Add other conditions for different modules similarly
                // ...
            } else {
                echo "No modules available";
            }
            ?>
        </ul>

    </aside><!-- End Sidebar-->



    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Doctors Options</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                    <li class="breadcrumb-item">Doctors Options</li>

                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-end mb-3 pt-4">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#addUser">
                                    <i class="bi bi-plus"></i>  
                                </button>
                            </div>

                            <!-- Table with stripped rows -->
                            <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Name</th>
                                        <th>Department</th>

                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    require ('config/db_con.php');
                                    $table = mysqli_query($conn, "SELECT  * FROM doctors WHERE Active = 1");

                                    $serialNo = 1;
                                    while ($row = mysqli_fetch_assoc($table)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $serialNo++; ?></td>
                                            <td><?php echo $row['Name']; ?></td>
                                            <td><?php echo $row['Department']; ?></td>
                                            <!-- <td> <img src="DataAdd/uploads/doctors<?php echo $row["Image"]; ?>" width = 200 title="<?php echo $row['Image']; ?>"> </td> -->


                                            <td>
                                                <div class="d-inline-flex gap-3">
                                                    <div data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        data-bs-title="View">
                                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                            data-bs-target="#viewUser">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    </div>

                                                    <div data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        data-bs-title="Edit">
                                                        <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                                            data-bs-target="#editUser">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    </div>

                                                    <div data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        data-bs-title="Delete">
                                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                            data-bs-target="#lockAccount">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>

                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No.</th>
                                        <th>Name</th>
                                        <th>Department</th>

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
                        Add Doctor
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3 needs-validation" novalidate enctype="multipart/form-data" action="POST">
                        <div class="mb-3">
                            <div class="row">
                                <div class="col">
                                    <label for="nameInput" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="name" aria-label="First Name"
                                        name="name" required>
                                </div>
                            </div>
                            <div class="invalid-feedback">
                                Only letters are allowed, special characters and numbers are not allowed.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nameInput" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="name" aria-label="Middle Name" name="name">
                        </div>

                        <div class="mb-3">
                            <label for="nameInput" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="name" aria-label="Last Name" name="name"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="depInput" class="form-label">Department</label>
                            <select class="form-select" aria-label="Default select example" name="department" required>
                                <option value="">Select Department</option>
                                <option value="Anesthesiology">Anesthesiology</option>
                                <option value="Critical Care">Critical Care</option>
                                <option value="Dentist">Dentist</option>
                                <option value="ENT">ENT</option>
                                <option value="General Surgery">General Surgery</option>
                                <option value="Geriatrician">Geriatrician</option>
                                <option value="Gynecology/ Oncology">Gynecology/ Oncology</option>
                                <option value="Internal Medicine">Internal Medicine</option>
                                <option value="Cardiology">Cardiology</option>
                                <option value="Dermatology">Dermatology</option>
                                <option value="Diabetology">Diabetology</option>
                                <option value="Endocrinology">Endocrinology</option>
                                <option value="Gastroenterology">Gastroenterology</option>
                                <option value="Geriatrics">Geriatrics</option>
                                <option value="Hematology">Hematology</option>
                                <option value="INF. & COMM. Dses.">INF. & COMM. Dses.</option>
                                <option value="Neurology & Psychiatry">Neurology & Psychiatry</option>
                                <option value="Oncology">Oncology</option>
                                <option value="Pulmonology">Pulmonology</option>
                                <option value="Rheumatology">Rheumatology</option>
                                <option value="Family Medicine">Family Medicine</option>
                                <option value="Nephrology">Nephrology</option>
                                <option value="Neurology">Neurology</option>
                                <option value="Neuropsychology">Neuropsychology</option>
                                <option value="Psychiatry">Psychiatry</option>
                                <option value="Urology">Urology</option>
                                <option value="Medico Legal">Medico Legal</option>
                                <option value="Neurosurgery">Neurosurgery</option>
                                <option value="Obstetrics and Gynecology">Obstetrics and Gynecology</option>
                                <option value="Obstetrics and Gynecology- Infectious Diseases">Obstetrics and
                                    Gynecology- Infectious Diseases</option>
                                <option value="Occupational Family & Medicine">Occupational Family & Medicine</option>
                                <option value="Ophthalmology">Ophthalmology</option>
                                <option value="Orthopedic Surgery">Orthopedic Surgery</option>
                                <option value="Pathology">Pathology</option>
                                <option value="Pediatrics">Pediatrics</option>
                                <option value="Plastic Surgery">Plastic Surgery</option>
                                <option value="Rehabilitation Medicine">Rehabilitation Medicine</option>
                                <option value="Sonology">Sonology</option>
                                <option value="Surgical Oncology">Surgical Oncology</option>
                                <option value="Thoracic and Cardiovascular Surgery">Thoracic and Cardiovascular Surgery
                                </option>
                                <option value="Physical Medicine and Rehabilitation">Physical Medicine and
                                    Rehabilitation</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ImageInput" class="form-label">Upload Image</label>
                            <input class="form-control" type="file" id="ImageInput" name="Image" required>
                        </div>

                        <div class="mb-3">
                            <label for="schedInput" class="form-label">Schedule</label>
                            <textarea class="form-control" rows="3" name="schedule"></textarea>
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


    <script>
        $(document).ready(function () {
            //Only needed for the filename of export files.
            //Normally set in the title tag of your page.
            document.title = "Doctors Options";
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
                        url: 'DataAdd/add_doctor.php',
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
                                    window.location.href = 'doctor.php';
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
    </script>

</body>

</html>