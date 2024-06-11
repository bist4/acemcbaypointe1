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
            echo '<a class="nav-link" href="accounts.php">';
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

          // // Check for Page Options module and construct sub-navigation items
          // if ($row['ModuleName'] == 'Add New Page') {
          //     $pageOptionsSubMenu .= '<li>';
          //     $pageOptionsSubMenu .= '<a href="pages/' . strtolower(str_replace(' ', '_', $row['ModuleName'])) . '.php">';
          //     $pageOptionsSubMenu .= '<i class="bi bi-circle"></i><span>' . $row['ModuleName'] . '</span>';
          //     $pageOptionsSubMenu .= '</a>';
          //     $pageOptionsSubMenu .= '</li>';
          // }
      
          if ($row['ModuleName'] == 'Event List' || $row['ModuleName'] == 'News List' || $row['ModuleName'] == 'Promo and Packages') {
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
          echo '<i class="bi bi-calendar4"></i><span>What\'s New</span><i class="bi bi-chevron-down ms-auto"></i>';
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
      <h1>Account Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="home.php">Home</a></li>
          <li class="breadcrumb-item">Account Management</li>

        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">

              <div>


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





                <div class="bootstrap-3-table">

                  <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                      <tr>
                        <th>ID Number</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      require ('config/db_con.php');
                      $table = mysqli_query($conn, "SELECT 
                        u.UserID, 
                        u.IdNumber,
                        u.Fname, 
                        u.Lname, 
                        u.Gender,
                        u.Age,
                        u.Birthday,
                        u.Address,
                        u.ContactNumber,
                        u.is_Admin_Group,
                        u.is_Ancillary_Group,
                        u.is_Nursing_Group,
                        u.is_Outsource_Group,
                        u.Username, 
                        u.Password,
                        u.Email, 
                        u.UserRoleID,
                        u.ProfilePhoto,
                        bd.DepartmentName, 
                        u.BaypointeDepartmentID,
                        usr.UserRoleName,
                        p.PrivilegeID,
                        u.is_Lock,
                        COUNT(p.PrivilegeID) AS NumOfPrivileges
                    FROM 
                        users u
                  
                    INNER JOIN 
                        userroles usr ON u.UserRoleID = usr.UserRoleID
                    INNER JOIN 
                        baypointedepartments bd ON u.BaypointeDepartmentID = bd.BaypointeDepartmentID
                    LEFT JOIN 
                        privileges p ON u.UserID = p.UserID
                    WHERE 
                        u.Active = 1 AND usr.UserRoleID NOT IN (0)
                    GROUP BY 
                        u.UserID, u.Fname, u.Lname,u.Gender, u.IdNumber,
                        u.Age,
                        u.Birthday,
                        u.Address,
                        u.ContactNumber,
                        u.is_Admin_Group,
                        u.is_Ancillary_Group,
                        u.is_Nursing_Group,
                        u.ProfilePhoto,
                        u.UserRoleID,
                        u.is_Lock,
                        u.BaypointeDepartmentID,
                        u.is_Outsource_Group, u.Username, u.Password,u.Email, bd.DepartmentName, usr.UserRoleName;");

                      $serialNo = 1;
                      while ($row = mysqli_fetch_assoc($table)) {
                        $lockAccountValue = $row['is_Lock'];
                        ?>
                        <tr>
                          <!-- <td><?php echo $serialNo++; ?></td> -->
                          <td><?php echo $row['IdNumber']; ?></td>
                          <td><?php echo $row['Fname'] . ' ' . $row['Lname']; ?></td>
                          <td><?php echo $row['Username']; ?></td>
                          <td><?php echo $row['Email']; ?></td>
                          <td><?php echo $row['DepartmentName']; ?></td>
                          <td><?php echo $row['UserRoleName']; ?></td>
                          <td>
                            <div class="d-inline-flex gap-3">
                              <div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="View">

                                <?php
                                if (isset($_SESSION['UserID'])) {
                                  // Prepare the SQL query with proper concatenation
                                  $sql = "SELECT Action_View FROM `privileges` WHERE UserID = '" . $_SESSION['UserID'] . "'";

                                  // Execute the SQL query
                                  $result = mysqli_query($conn, $sql);

                                  // Check if query was successful
                                  if ($result) {
                                    $row_privileges = mysqli_fetch_assoc($result);
                                    // Display the button if Action_Add is 1
                                    if ($row_privileges['Action_View'] == 1) {
                                      echo '<button type="button" class="btn btn-primary view-btn"  
                                          data-bs-toggle="modal" data-bs-target="#viewUser" 
                                          data-user-id="' . $row['UserID'] . '">
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
                              </div>

                              <div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Edit">
                                <?php
                                if (isset($_SESSION['UserID'])) {
                                  // Prepare the SQL query with proper concatenation
                                  $sql = "SELECT Action_Update FROM `privileges` WHERE UserID = '" . $_SESSION['UserID'] . "'";

                                  // Execute the SQL query
                                  $result = mysqli_query($conn, $sql);

                                  // Check if query was successful
                                  if ($result) {
                                    $row_privileges = mysqli_fetch_assoc($result);
                                    // Display the button if Action_Add is 1
                                    if ($row_privileges['Action_Update'] == 1) {
                                      echo '<button type="button" class="btn btn-info edit-btn"  
                                          data-bs-toggle="modal" data-bs-target="#editUser" 
                                            data-user-id="' . $row['UserID'] . '">
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
                              </div>

                              <div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Unlock Account">
                                <?php
                                if (isset($_SESSION['UserID'])) {
                                  // Prepare the SQL query with proper concatenation
                                  $sql = "SELECT Action_Lock, Action_Unlock FROM `privileges` WHERE UserID = '" . $_SESSION['UserID'] . "'";

                                  // Execute the SQL query
                                  $result = mysqli_query($conn, $sql);

                                  // Check if query was successful
                                  if ($result) {
                                    $row_privileges = mysqli_fetch_assoc($result);
                                    // Display the button if Action_Add is 1
                              


                                    // if ($row_privileges['Action_Lock'] == 1 && $row_privileges['Action_Unlock'] == 1) {
                                    //   echo '<button type="button" class="btn ' . ($lockAccountValue == 1 ? 'btn-danger' : 'btn-success') . '"  
                                    //     data-bs-toggle="modal" data-bs-target="#lockAccount" onclick="confirmLock(' . $lockAccountValue . ')" data-user-id="' . $row['UserID'] . '">
                                    //     <i class="bi ' . ($lockAccountValue == 1 ? 'bi-lock' : 'bi-unlock') . '"></i>  
                                    //   </button>';
                                    // }
                                    if ($row_privileges['Action_Lock'] == 1 && $row_privileges['Action_Unlock'] == 1) {
                                      echo '<button type="button" class="btn ' . ($lockAccountValue == 1 ? 'btn-danger' : 'btn-success') . '"  
                                          data-bs-toggle="modal" data-bs-target="#lockAccount" onclick="' . ($lockAccountValue == 1 ? 'confirmUnlock(' : 'confirmLock(') . $lockAccountValue . ')" data-user-id="' . $row['UserID'] . '">
                                          <i class="bi ' . ($lockAccountValue == 1 ? 'bi-lock' : 'bi-unlock') . '"></i>  
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

                              <div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Assign Modules">
                                <?php
                                if (isset($_SESSION['UserID'])) {
                                  // Prepare the SQL query with proper concatenation
                                  $sql = "SELECT AssignModule_View FROM `privileges` WHERE UserID = '" . $_SESSION['UserID'] . "'";

                                  // Execute the SQL query
                                  $result = mysqli_query($conn, $sql);

                                  // Check if query was successful
                                  if ($result) {
                                    $row_privileges = mysqli_fetch_assoc($result);
                                    // Display the button if Action_Add is 1
                                    if ($row_privileges['AssignModule_View'] == 1) {
                                      echo '<button  type="button" class="btn btn-secondary" id="Assignmodule" 
                                            data-bs-toggle="modal" data-bs-target="#priveleges"
                                              data-user-id="' . $row['UserID'] . '">
                                              <i class="bi bi-clipboard"></i>  
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



                            </div>
                          </td>
                        </tr>
                        <?php
                      }
                      ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th>ID Number</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Action</th>
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




  <!-- Privileges modal -->
  <div class="modal fade" id="priveleges" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="exampleModalLgLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title h4" id="exampleModalLgLabel">Modules</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="modalBody2">
          <!-- Placeholder for the data -->
          <div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal For User -->
  <div class="modal fade" id="addUser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="exampleModalLgLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title h4" id="exampleModalLgLabel">Add User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form class="row g-3 pt-3 needs-validation" novalidate enctype="multipart/form-data">
            <div class="row">
              <div class="col-md-6">


                <div class="mb-3">
                  <label for="IdNumber" class="form-label">ID</label>
                  <input type="number" class="form-control" id="IdNumber" required name="IdNumber">
                  <div class="invalid-feedback">Please provide User ID.</div>
                </div>
                <div class="mb-3">
                  <label for="firstName" class="form-label">First Name</label>
                  <input type="text" class="form-control" id="firstName" required name="firstName">
                  <div class="invalid-feedback">Please provide a first name.</div>
                </div>
                <div class="mb-3">
                  <label for="lastName" class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="lastName" required name="lastName">
                  <div class="invalid-feedback">Please provide a last name.</div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="gender" class="form-label">Gender</label>
                      <select class="form-select" id="gender" required name="gender">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                      </select>
                      <div class="invalid-feedback">Please select a gender.</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="birthday" class="form-label">Birthday</label>
                      <input type="date" class="form-control" id="birthday" required name="birthday">
                      <div class="invalid-feedback">Please input birthday.</div>
                    </div>
                  </div>
                </div>



              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <div class="d-flex justify-content-center">
                    <div class="container">
                      <div id="dropArea" class="text-center" ondrop="handleDrop(event)"
                        ondragover="handleDragOver(event)">
                        <i class="bi bi-download icons"></i>

                      </div>
                      <input type="file" id="fileInput" onchange="handleFileSelect(event)" name="profile"
                        class="form-control mt-3">

                    </div>
                  </div>

                </div>

              </div>
            </div>
            <div class="mb-3">
              <label for="validationCustom01" class="form-label">Address</label>
              <div class="input-group">
                <input type="text" class="form-control" id="houseNumber" placeholder="House Number"
                  aria-label="House Number" name="houseNumber" required>
                <input type="text" class="form-control" id="streetName" placeholder="Street Name"
                  aria-label="Street Name" name="streetName" required>
                <input type="text" class="form-control" id="barangay" placeholder="Barangay" aria-label="Barangay"
                  name="barangay" required>
                <div class="invalid-feedback">
                  Please provide a complete address.
                </div>
              </div>
              <br>
              <div class="input-group">
                <input type="text" class="form-control" id="cityMunicipality" placeholder="City/Municipality"
                  aria-label="City/Municipality" name="city" required>
                <input type="text" class="form-control" id="province" placeholder="Province" aria-label="Province"
                  name="province" required>
                <div class="invalid-feedback">
                  Please provide a complete address.
                </div>
              </div>
            </div>
            <div class="row">
              <!-- Left side -->
              <div class="col-md-6">
                <!-- Contact Number -->

                <div class="mb-3">
                  <label for="contactNumber" class="form-label">Contact Number</label>
                  <input type="tel" class="form-control" id="contactNum" required pattern="[0-9]{11}"
                    placeholder="Enter a valid 11-digit contact number" name="contactNum">
                  <div class="invalid-feedback" id="contactNumError">Please provide a valid 11-digit contact number.
                  </div>
                </div>


                <!-- Username -->
                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input type="text" class="form-control" id="username" required name="username">
                  <div class="invalid-feedback">Please provide a username.</div>
                </div>
                <!-- Password -->
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="passwordInput" required name="password" />
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                      <i class="bi bi-eye"></i>
                    </button>
                    <div class="invalid-feedback">
                      Please provide a valid password (at least 8 characters, containing at least one uppercase letter,
                      one lowercase letter, and one digit).
                    </div>
                  </div>
                </div>
                <!-- Confirm Password -->
                <div class="mb-3">
                  <label for="confirmPassword" class="form-label">Confirm Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="confirmPassword" required name="password" />
                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                      <i class="bi bi-eye"></i>
                    </button>
                    <div class="invalid-feedback">Please confirm your password.</div>
                    <div id="passwordMismatchError" class="invalid-feedback"></div>
                  </div>
                </div>
              </div>
              <!-- Right side -->
              <div class="col-md-6">
                <!-- Email -->
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" required name="email">
                  <div class="invalid-feedback">Please provide a valid email address.</div>
                </div>
                <div class="mb-3">
                  <label for="department" class="form-label">Department</label>
                  <select class="form-select" aria-label="Default select example" name="BaypointeDepartmentID"
                    id="departmentSelect" required>
                    <option value="">Select Department</option>
                    <?php
                    require ('config/db_con.php');

                    // Fetch department data from the database
                    $query = "SELECT * FROM baypointedepartments";
                    $result = mysqli_query($conn, $query);

                    // Check if there are any departments
                    if (mysqli_num_rows($result) > 0) {
                      // Output data of each row
                      while ($row = mysqli_fetch_assoc($result)) {
                        // Output an option for each department
                        echo '<option value="' . $row['BaypointeDepartmentID'] . '">' . $row['DepartmentName'] . '</option>';
                      }
                    } else {
                      echo '<option disabled>No departments found</option>';
                    }
                    ?>
                  </select>
                  <div class="invalid-feedback">Please select a department.</div>


                </div>

                <!-- Department -->
                <div class="mb-3">
                  <label for="group" class="form-label">Group</label>
                  <select class="form-select" disabled aria-label="Default select example" name="group" id="groupSelect"
                    required>
                    <option value="">Select Group</option>
                    <option value="Admin">Admin</option>
                    <option value="Ancillary">Ancillary</option>
                    <option value="Nursing">Nursing</option>
                    <option value="EXECOM">EXECOM</option>


                  </select>
                  <div class="invalid-feedback">Please select a group.</div>
                </div>

                <!-- User Role -->
                <div class="mb-3">
                  <label for="userRole" class="form-label">User Role</label>
                  <select class="form-select" disabled aria-label="Default select example" name="role" required
                    id="roleSelect">
                    <option value="">Select User Role</option>
                    <option value="0">Super Admin</option>
                    <option value="1">Admin</option>
                    <option value="2">User</option>
                    <option value="3">Doctors</option>
                  </select>
                  <div class="invalid-feedback">Please select a user role.</div>
                </div>
              </div>
            </div>
            <!-- Submit Button -->
            <div class="col-12">
              <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="addBtn" class="btn btn-primary" name="addBtn">Add</button>
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


  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!--For validdation of password  -->
  <script>
    document.getElementById('passwordInput').addEventListener('input', function () {
      var password = this.value;
      var hasUpperCase = /[A-Z]/.test(password);
      var hasLowerCase = /[a-z]/.test(password);
      var hasDigit = /\d/.test(password);
      var isValidLength = password.length >= 8;

      var isValid = hasUpperCase && hasLowerCase && hasDigit && isValidLength;

      if (!isValid) {
        this.setCustomValidity("Please provide a valid password (at least 8 characters, containing at least one uppercase letter, one lowercase letter, and one digit).");
        this.parentNode.querySelector('.invalid-feedback').style.display = 'block';
      } else {
        this.setCustomValidity('');
        this.parentNode.querySelector('.invalid-feedback').style.display = 'none';
      }
    });


  </script>

  <!--Conact Number  -->
  <script>
    // Get the input element
    var inputElement = document.getElementById("contactNum");

    // Add event listener for input event
    inputElement.addEventListener("input", function (event) {
      // Get the value entered by the user
      var inputValue = event.target.value;

      // Remove non-numeric characters from the input value
      var numericValue = inputValue.replace(/\D/g, "");

      // Update the input field value with numeric-only value
      event.target.value = numericValue;
    });
  </script>



  <!-- Add data -->
  <script>
    $(document).ready(function () {
      $('#addBtn').click(function (e) {
        // e.preventDefault();
        var form = $('.needs-validation')[0];
        // console.log(form);
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
          console.log(formData);
          $.ajax({
            type: 'POST',
            url: 'DataAdd/add_user.php',
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
                  // showConfirmButton: false,
                  // timer: 5000,
                }).then(function () {
                  window.location.href = 'accounts.php';
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

  <!--Show password and hide  -->
  <script>
    // Toggle Password Visibility for Password Field
    const togglePasswordButton = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('passwordInput');

    togglePasswordButton.addEventListener('click', function () {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      this.querySelector('i').classList.toggle('bi-eye');
      this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    // Toggle Password Visibility for Confirm Password Field
    const toggleConfirmPasswordButton = document.getElementById('toggleConfirmPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');

    toggleConfirmPasswordButton.addEventListener('click', function () {
      const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      confirmPasswordInput.setAttribute('type', type);
      this.querySelector('i').classList.toggle('bi-eye');
      this.querySelector('i').classList.toggle('bi-eye-slash');
    });
  </script>


  <!-- Drag photo -->
  <script>
    function handleFileSelect(event) {
      const file = event.target.files[0];
      handleFile(file);
    }

    function handleDrop(event) {
      event.preventDefault();
      const file = event.dataTransfer.files[0];
      handleFile(file);

      const fileInput = document.getElementById('fileInput');
      fileInput.files = event.dataTransfer.files;
    }

    function handleDragOver(event) {
      event.preventDefault();
    }

    function handleFile(file) {
      const dropArea = document.getElementById('dropArea');
      const fileReader = new FileReader();

      fileReader.onload = () => {
        let fileURL = fileReader.result;
        let imgTag = document.createElement('img');
        imgTag.src = fileURL;
        imgTag.alt = 'profile';
        imgTag.name = 'profile';
        imgTag.width = 200;
        imgTag.height = 200;
        dropArea.innerHTML = '';
        dropArea.appendChild(imgTag);

        // Log the file to the console
        console.log("Selected File:", file);
        console.log("Image tag:", imgTag);
      };

      // Read the file as Data URL (base64 encoded)
      fileReader.readAsDataURL(file);
    }
  </script>

  <!-- View User -->
  <script>
    $(document).ready(function () {
      $('.btn-primary').click(function () {
        var userid = $(this).data('user-id');

        // Make an AJAX request to fetch data from the server
        $.ajax({
          url: 'DataGet/get_User.php', // PHP script to fetch data from the server
          method: 'POST',
          data: { userid: userid },
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


  <!-- Edit User -->
  <script>
    $(document).ready(function () {
      $('.btn-info').click(function () {
        var userid = $(this).data('user-id');

        // Make an AJAX request to fetch data from the server
        $.ajax({
          url: 'DataGet/edit_User.php', // PHP script to fetch data from the server
          method: 'POST',
          data: { userid: userid },
          success: function (response) {
            // Insert the HTML into the modal body
            $('#modalBody1').html(response);
          },
          error: function (xhr, status, error) {
            console.error(xhr.responseText);
          }
        });
      });
    });
  </script>

  <!-- Script to check authentication status and show privileges -->
  <script>
    $(document).ready(function () {
      $('.btn-secondary').click(function () {
        var userid = $(this).data('user-id');

        // Make an AJAX request to fetch data from the server
        $.ajax({
          url: 'DataGet/userPrivileges.php', // PHP script to fetch data from the server
          method: 'POST',
          data: { userid: userid },
          success: function (response) {
            // Insert the HTML into the modal body
            $('#modalBody2').html(response);
          },
          error: function (xhr, status, error) {
            console.error(xhr.responseText);
          }
        });
      });
    });
  </script>




  <!-- Password matching -->
  <script>
    document.getElementById("confirmPassword").addEventListener("input", function () {
      var password = document.getElementById("passwordInput").value;
      var confirmPassword = document.getElementById("confirmPassword").value;
      var error = document.getElementById("passwordMismatchError");

      if (password !== confirmPassword) {
        error.textContent = "Passwords do not match.";
        document.getElementById("confirmPassword").setCustomValidity("Passwords do not match.");
      } else {
        error.textContent = "";
        document.getElementById("confirmPassword").setCustomValidity("");
      }
    });
  </script>

  <script>
    const contactNumInput = document.getElementById('contactNum');
    const contactNumError = document.getElementById('contactNumError');

    contactNumInput.addEventListener('input', function () {
      const isValid = this.validity.valid;
      if (!isValid) {
        contactNumError.style.display = 'block';
      } else {
        contactNumError.style.display = 'none';
      }
    });
  </script>


  <script>
    const departmentSelect = document.getElementById('departmentSelect');
    const groupSelect = document.getElementById('groupSelect');
    const roleSelect = document.getElementById('roleSelect');

    const adminOption = groupSelect.querySelector('option[value="Admin"]');
    const ancillaryOption = groupSelect.querySelector('option[value="Ancillary"]');
    const nursingOption = groupSelect.querySelector('option[value="Nursing"]');
    const novalue = groupSelect.querySelector('option[value=""]');

    const superAdmin = roleSelect.querySelector('option[value="0"]');
    const adminOptions = roleSelect.querySelector('option[value="1"]');
    const userOptions = roleSelect.querySelector('option[value="2"]');
    const doctorsOptions = roleSelect.querySelector('option[value="3"]');




    departmentSelect.addEventListener('change', function () {
      const selectedDepartment = departmentSelect.value;
      console.log('Selected department:', selectedDepartment); // Add this line for debugging

      if (selectedDepartment === '6' || selectedDepartment === '7' || selectedDepartment === '9' || selectedDepartment === '10' || selectedDepartment === '13' || selectedDepartment === '14' || selectedDepartment === '19') {
        groupSelect.value = 'Admin';
        groupSelect.removeAttribute('disabled');

        if (ancillaryOption) {
          ancillaryOption.disabled = true;
        }

        if (nursingOption) {
          nursingOption.disabled = true;
        }
        if (novalue) {
          novalue.disabled = true;
        }

        if (selectedDepartment == '13') {
          roleSelect.value = '0';
          roleSelect.removeAttribute('disabled');

          if (adminOptions) {
            adminOptions.disabled = true;
          }
          if (userOptions) {
            userOptions.disabled = true;
          }
          if (doctorsOptions) {
            doctorsOptions.disabled = true;
          }





        } else if (selectedDepartment == '9' || selectedDepartment == '19') {
          roleSelect.value = '1';
          roleSelect.removeAttribute('disabled');

          if (superAdmin) {
            superAdmin.disabled = true;
          }
          if (userOptions) {
            userOptions.disabled = true;
          }
          if (doctorsOptions) {
            doctorsOptions.disabled = true;
          }

        }
        else if (selectedDepartment == '6' || selectedDepartment == '7' || selectedDepartment == '10' || selectedDepartment == '14') {
          roleSelect.value = '2';
          roleSelect.removeAttribute('disabled');

          if (adminOptions) {
            adminOptions.disabled = true;
          }
          if (superAdmin) {
            superAdmin.disabled = true;
          }
          if (doctorsOptions) {
            doctorsOptions.disabled = true;
          }
        }
      }

      else if (selectedDepartment == '25' || selectedDepartment == '27' || selectedDepartment == '28' || selectedDepartment == '29' || selectedDepartment == '30' || selectedDepartment == '31' || selectedDepartment == '32' || selectedDepartment == '33' || selectedDepartment == '34') {
        groupSelect.value = 'Ancillary';
        groupSelect.removeAttribute('disabled');
        roleSelect.value = '2';
        roleSelect.removeAttribute('disabled');

        if (adminOptions) {
          adminOptions.disabled = true;
        }
        if (superAdmin) {
          superAdmin.disabled = true;
        }
        if (doctorsOptions) {
          doctorsOptions.disabled = true;
        }
        if (adminOption) {
          adminOption.disabled = true;
        }
        if (nursingOption) {
          nursingOption.disabled = true;
        }
        if (novalue) {
          novalue.disabled = true;
        }

      }

      else if (selectedDepartment == '35' || selectedDepartment == '36' || selectedDepartment == '37' || selectedDepartment == '39' || selectedDepartment == '43' || selectedDepartment == '45') {
        groupSelect.value = 'Nursing';
        groupSelect.removeAttribute('disabled');
        if (adminOption) {
          adminOption.disabled = true;
        }
        if (ancillaryOption) {
          ancillaryOption.disabled = true;
        }
        if (novalue) {
          novalue.disabled = true;
        }

        if (selectedDepartment == '35' || selectedDepartment == '36' || selectedDepartment == '37' || selectedDepartment == '39' || selectedDepartment == '45') {
          roleSelect.value = '2';
          roleSelect.removeAttribute('disabled');
          if (adminOptions) {
            adminOptions.disabled = true;
          }
          if (superAdmin) {
            superAdmin.disabled = true;
          }
          if (doctorsOptions) {
            doctorsOptions.disabled = true;
          }
        }
        else if (selectedDepartment == '43') {
          roleSelect.value = '1';
          roleSelect.removeAttribute('disabled');

          if (userOptions) {
            userOptions.disabled = true;
          }
          if (superAdmin) {
            superAdmin.disabled = true;
          }
          if (doctorsOptions) {
            doctorsOptions.disabled = true;
          }
        }

      }
      else if (selectedDepartment == '48') {
        groupSelect.value = 'EXECOM';
        groupSelect.removeAttribute('disabled');
        roleSelect.value = '3';
        roleSelect.removeAttribute('disabled');
        if (adminOptions) {
          adminOptions.disabled = true;
        }
        if (superAdmin) {
          superAdmin.disabled = true;
        }
        if (userOptions) {
          userOptions.disabled = true;
        }
        if (novalue) {
          novalue.disabled = true;
        }
      }






      else {
        groupSelect.value = '';
        roleSelect.value = '';
      }
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


  <!-- DataTable -->
  <script>
    $(document).ready(function () {
      //Only needed for the filename of export files.
      //Normally set in the title tag of your page.
      document.title = "Account Management";
      // Create search inputs in footer
      $("#example tfoot th").each(function (index) {
        var title = $(this).text();
        if (index !== 6) { // Skip the "Action" column (index 6)
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


  <!-- Lock Account -->
  <script>
    function confirmLock() {
      // Check if the button is in btn-success state
      if (document.querySelector('.btn-success')) {
        let userID = $(event.target).data('user-id');
        console.log(userID);
        // Show SweetAlert confirmation message
        Swal.fire({
          title: 'Are you sure?',
          text: 'You want to lock this account?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
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
                  // You need to replace "getSessionUsername()" with the actual function or variable
                  // that retrieves the session username.
                  const sessionUsername = "<?php echo isset($_SESSION['Username']) ? $_SESSION['Username'] : ''; ?>";
                  if (username !== sessionUsername) {
                    throw new Error("Username doesn't match the session username");
                  }
                  return { username: sessionUsername };
                } catch (error) {
                  Swal.showValidationMessage(`
                    ${error.message}
                  `);
                }
              },
              allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
              if (result.isConfirmed) {
                $.ajax({
                  type: 'POST',
                  url: 'Process/lock_account.php',
                  data: { userID: userID },
                  success: function (response) {
                    Swal.fire({
                      icon: 'success',
                      title: 'Account Locked',
                      text: 'The account has been successfully locked!'
                    }).then(function () {
                      location.reload(); // Reload the page after locking
                    });
                  }
                });
              }
            });
          }
        });
      }
    }



  </script>

  <script>
    function confirmUnlock() {
      // Check if the button is in btn-success state
      if (document.querySelector('.btn-success')) {
        let userID = $(event.target).data('user-id');
        console.log(userID);
        // Show SweetAlert confirmation message
        Swal.fire({
          title: 'Are you sure?',
          text: 'You want to unlock this account?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
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
                  // You need to replace "getSessionUsername()" with the actual function or variable
                  // that retrieves the session username.
                  const sessionUsername = "<?php echo isset($_SESSION['Username']) ? $_SESSION['Username'] : ''; ?>";
                  if (username !== sessionUsername) {
                    throw new Error("Username doesn't match the session username");
                  }
                  return { username: sessionUsername };
                } catch (error) {
                  Swal.showValidationMessage(`
              ${error.message}
            `);
                }
              },
              allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
              if (result.isConfirmed) {
                $.ajax({
                  type: 'POST',
                  url: 'Process/unlock_account.php',
                  data: { userID: userID },
                  success: function (response) {
                    Swal.fire({
                      icon: 'success',
                      title: 'Account Unlocked',
                      text: 'The account has been successfully unlocked!'
                    }).then(function () {
                      location.reload(); // Reload the page after locking
                    });
                  }
                });
              }
            });
          }
        });
      }
    }



  </script>


</body>

</html>