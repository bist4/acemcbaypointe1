<?php
session_start();


// Check if the user is already logged in
if (isset($_SESSION['Username'])) {
  $role = $row['UserRoleID'];
  
  if ($role == 0) {
    header("Location: home.php");
    exit();
    
  }else if ($role == 1) {
    header("Location: home.php");
    exit();
    
  }else if ($role == 2) {
    header("Location: home.php");
    exit();
    
  }else if ($role == 3) {
    header("Location: home.php");
    exit();
    
  }else {
    header("Location: index.php");
  }
  exit();
}
?>







<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Forgot Password</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/logo2.png" rel="icon">
  <link rel="stylesheet" href="index.css">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

  <style>
    .error {
      color: red;
    }

    /* Style for loading spinner */
    .spinner-border {
      display: none; /* Initially hide the spinner */
    }
  </style>
</head>

<body>

  <main>
    <div class="wrapper">
      <div class="logo">
        <img src="assets/img/logo3.png" alt="">
      </div>
      <div class="text-center mt-4 name">
        Forgot Password
      </div>
      <?php if (isset($_GET['error'])) { ?>
        <p class="error text-center"><?php echo $_GET['error']; ?></p>
      <?php } ?>
      <form class="p-3 mt-3" id="loginForm" action="forgot.php" method="Post">
        <div class="form-field d-flex align-items-center">
          <i class="bi bi-person-fill"></i>
          <input type="email" name="email" id="email" placeholder="Email">
        </div>

        <!-- Loading spinner -->
        <div class="text-center mt-3">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <button type="submit" id="searchBtn" class="btn mt-3">Search Email</button>
      </form>

    </div>
  </main>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script>
    // Show loading spinner on form submit
    document.getElementById('loginForm').addEventListener('submit', function() {
      document.querySelector('.spinner-border').style.display = 'inline-block';
    });
  </script>

</body>

</html>
