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




  // Close the statement
  $stmt->close();

  // Close the database connection
  $conn->close();
}

if (isset($_SESSION['is_Lock']) && $_SESSION['is_Lock'] == 1) {
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

  <title>Profile</title>
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


  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Mar 17 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
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
        <a class="nav-link " href="home.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <?php

      // Check if there are rows in the result set
      if ($result->num_rows > 0) {
        $siteOptionsSubMenu = '';
        $pageOptionsSubMenu = '';
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
            echo '<a class="nav-link collapsed" href="doctor.php">';
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


      } else {
        // Handle the case where there are no rows in the result set
        echo "No modules found.";
      }

      ?>

    </ul>
  </aside>


  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Profile</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">Profile</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row">
        <div class="col-xl-4">

          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">


              <img src="DataAdd/uploads/<?php
              echo $_SESSION['ProfilePic'];
              ?> " alt="Profile" class="rounded-circle">
              <h2><?php echo $_SESSION['Fname'] ?> <?php echo $_SESSION['Lname'] ?></h6>
                </h6>
              </h2>
              <h3>
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
                </h6>
              </h3>
              <div class="social-links mt-2">
                <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
              </div>
            </div>
          </div>

        </div>

        <div class="col-xl-8">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab"
                    data-bs-target="#profile-overview">Overview</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">Settings</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change
                    Password</button>
                </li>

              </ul>
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">

                  <h5 class="card-title">Profile Details</h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label ">Full Name</div>
                    <div class="col-lg-9 col-md-8"><?php echo $_SESSION['Fname'] ?> <?php echo $_SESSION['Lname'] ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Gender</div>
                    <div class="col-lg-9 col-md-8"><?php echo $_SESSION['Gender'] ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Age</div>
                    <div class="col-lg-9 col-md-8"><?php echo $_SESSION['Age'] ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Birthday</div>
                    <div class="col-lg-9 col-md-8">
                      <?php echo date('F j, Y', strtotime($_SESSION['Birthday'])); ?>
                    </div>
                  </div>


                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Group</div>
                    <div class="col-lg-9 col-md-8">
                      <?php echo $_SESSION['is_Admin_Group'] == 1 ? 'Admin' : ''; ?>
                      <?php echo $_SESSION['is_Ancillary_Group'] == 1 ? 'Ancillary' : ''; ?>
                      <?php echo $_SESSION['is_Nursing_Group'] == 1 ? 'Nursing' : ''; ?>
                      <?php echo $_SESSION['is_Outsource_Group'] == 1 ? 'Outsource' : ''; ?>
                    </div>

                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Department</div>
                    <div class="col-lg-9 col-md-8">
                      <?php

                      if ($_SESSION['DepartmentName'] == 6) {
                        echo 'Admitting/Comm. & Info.';
                      } elseif ($_SESSION['DepartmentName'] == 7) {
                        echo 'Dietary';
                      } elseif ($_SESSION['DepartmentName'] == 9) {
                        echo 'HRDM';
                      } elseif ($_SESSION['DepartmentName'] == 10) {
                        echo 'IPCU';
                      } elseif ($_SESSION['DepartmentName'] == 13) {
                        echo 'MIS';
                      } elseif ($_SESSION['DepartmentName'] == 14) {
                        echo 'OHS';
                      } elseif ($_SESSION['DepartmentName'] == 19) {
                        echo 'Sales & Marketing';
                      } elseif ($_SESSION['DepartmentName'] == 25) {
                        echo 'Blood Bank';
                      } elseif ($_SESSION['DepartmentName'] == 27) {
                        echo 'Heart Station';
                      } elseif ($_SESSION['DepartmentName'] == 28) {
                        echo 'Laboratory';
                      } elseif ($_SESSION['DepartmentName'] == 29) {
                        echo 'OB-GYNE';
                      } elseif ($_SESSION['DepartmentName'] == 30) {
                        echo 'Pharmacy';
                      } elseif ($_SESSION['DepartmentName'] == 31) {
                        echo 'Pulmonary';
                      } elseif ($_SESSION['DepartmentName'] == 32) {
                        echo 'Radiology';
                      } elseif ($_SESSION['DepartmentName'] == 33) {
                        echo 'Diabetes';
                      } elseif ($_SESSION['DepartmentName'] == 34) {
                        echo 'Rehabilitation & Medicine';
                      } elseif ($_SESSION['DepartmentName'] == 35) {
                        echo 'CTU';
                      } elseif ($_SESSION['DepartmentName'] == 36) {
                        echo 'Dialysis';
                      } elseif ($_SESSION['DepartmentName'] == 37) {
                        echo 'ER';
                      } elseif ($_SESSION['DepartmentName'] == 39) {
                        echo 'NICU';
                      } elseif ($_SESSION['DepartmentName'] == 43) {
                        echo 'OPD';
                      } elseif ($_SESSION['DepartmentName'] == 45) {
                        echo 'EMG';
                      } else {
                        // Handle case when department code doesn't match any known department
                        echo 'Unknown Department';
                      }

                      ?>
                    </div>
                  </div>



                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Address</div>
                    <div class="col-lg-9 col-md-8"><?php echo $_SESSION['Address'] ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Phone</div>
                    <div class="col-lg-9 col-md-8"><?php echo $_SESSION['ContactNumber'] ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email</div>
                    <div class="col-lg-9 col-md-8"><?php echo $_SESSION['Email'] ?></div>
                  </div>

                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <form>
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                      <div class="col-md-8 col-lg-9">
                        <img src="DataAdd/uploads/<?php
                        echo $_SESSION['ProfilePic'];
                        ?> " alt="Profile">
                        <div class="pt-2">
                          <a href="#" class="btn btn-primary btn-sm" title="Upload new profile image"><i
                              class="bi bi-upload"></i></a>
                          <a href="#" class="btn btn-danger btn-sm" title="Remove my profile image"><i
                              class="bi bi-trash"></i></a>
                        </div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="fullName" type="text" class="form-control" id="fullName"
                          value="<?php echo $_SESSION['Fname'] ?> <?php echo $_SESSION['Lname'] ?>">
                      </div>
                    </div>



                    <div class="row mb-3">
                      <label for="company" class="col-md-4 col-lg-3 col-form-label">Group</label>
                      <div class="col-md-8 col-lg-9">
                        <select class="form-select" aria-label="Default select example" name="group" id="groupSelect"
                          onchange="updateDepartments()" required>

                          <option value="Admin" <?php echo $_SESSION['is_Admin_Group'] == 1 ? 'selected' : ''; ?>>Admin
                          </option>
                          <option value="Ancillary" <?php echo $_SESSION['is_Ancillary_Group'] == 1 ? 'selected' : ''; ?>>
                            Ancillary</option>
                          <option value="Nursing" <?php echo $_SESSION['is_Nursing_Group'] == 1 ? 'selected' : ''; ?>>
                            Nursing</option>
                          <option value="Outsource" <?php echo $_SESSION['is_Outsource_Group'] == 1 ? 'selected' : ''; ?>>
                            Outsource</option>
                        </select>

                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Job" class="col-md-4 col-lg-3 col-form-label">Department</label>
                      <div class="col-md-8 col-lg-9">

                        <select class="form-select" aria-label="Default select example" name="BaypointeDepartmentID"
                          id="departmentSelect" required>
                          <?php
                          // Assuming $conn is your database connection
                          require ('config/db_con.php');
                          $query = "SELECT * FROM baypointedepartments";
                          $result = mysqli_query($conn, $query);

                          // Check if the query was successful
                          if ($result) {
                            // Loop through the result set
                            while ($department = mysqli_fetch_assoc($result)) {
                              // Check if the current department ID matches the one from the database row
                              $selected = $department['BaypointeDepartmentID'] == $_SESSION['BaypointeDepartmentID'] ? 'selected' : '';
                              // Output the option with the department name and set 'selected' attribute if matched
                              echo '<option value="' . $department['BaypointeDepartmentID'] . '" ' . $selected . '>' . $department['DepartmentName'] . '</option>';
                            }
                          } else {
                            // Handle error if query fails
                            echo '<option value="">Error retrieving departments</option>';
                          }
                          ?>
                        </select>


                      </div>
                    </div>



                    <div class="row mb-3">
                      <label for="Address" class="col-md-4 col-lg-3 col-form-label">Address</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="address" type="text" class="form-control" id="Address"
                          value="<?php echo $_SESSION['Address'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Contact Number</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="phone" type="text" class="form-control" id="Phone"
                          value="<?php echo $_SESSION['ContactNumber'] ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="email" type="email" class="form-control" id="Email"
                          value="<?php echo $_SESSION['Email'] ?>">
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                  </form><!-- End Profile Edit Form -->

                </div>

                <div class="tab-pane fade pt-3" id="profile-settings">

                  <!-- Settings Form -->
                  <form>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Email Notifications</label>
                      <div class="col-md-8 col-lg-9">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="changesMade" checked>
                          <label class="form-check-label" for="changesMade">
                            Changes made to your account
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="newProducts" checked>
                          <label class="form-check-label" for="newProducts">
                            Information on new products and services
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="proOffers">
                          <label class="form-check-label" for="proOffers">
                            Marketing and promo offers
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="securityNotify" checked disabled>
                          <label class="form-check-label" for="securityNotify">
                            Security alerts
                          </label>
                        </div>
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                  </form><!-- End settings Form -->

                </div>

                <div class="tab-pane fade pt-3" id="profile-change-password">
                  <!-- Change Password Form -->
                  <form>

                    <div class="row mb-3">
                      <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="password" type="password" class="form-control" id="currentPassword">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="newpassword" type="password" class="form-control" id="newPassword">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="renewpassword" type="password" class="form-control" id="renewPassword">
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                  </form><!-- End Change Password Form -->

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

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

</body>

</html>