<?php
session_start();


// Check if the user is already logged in
if (!isset($_SESSION['user_email'])) {
   
}
?>








<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Login</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/logo2.png" rel="icon">
  <link rel="stylesheet" href="index.css">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
 <style>
  .error {
    color: red;
}

 </style>
</head>

<body>

  <main>
    <div class="wrapper animate__animated animate__zoomInDown">
        <div class="logo">
            <img src="assets/img/logo3.png" alt=""   >
        </div>
        <div class="text-center mt-4 name">
            Login
        </div>
        <?php if (isset($_GET['error'])) { ?>
          <p class="error text-center"><?php echo $_GET['error']; ?></p>
        <?php } ?>
        <form  class="p-3 mt-3" id="loginForm" action="change_password.php" method="Post">
            <div class="form-field d-flex align-items-center">
                <span class="bi bi-key-fill"></span>
                <input type="password" name="password" id="pwd" placeholder="New Password">
            </div>
            <div class="form-field d-flex align-items-center">
                <span class="bi bi-key-fill"></span>
                <input type="password" name="conformPassword" id="pwd" placeholder="Confirm Password">
            </div>
            <button type="submit" id="changeBtn" class="btn mt-3">Change Password</button>
        </form>
       
    </div>
  </main><!-- End #main -->

 

 

 

</body>

</html>
 



                


                