<?php
// Start the session
session_start();

include("config/db_con.php");

// Assuming you're getting username and password from a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User authenticated successfully
        $_SESSION['username'] = $username; // Set session username
        echo json_encode(array("success" => "User authenticated successfully"));
    } else {
        // If user not found or authentication failed
        echo json_encode(array("error" => "Invalid username or password"));
    }
}

// Closing the connection
$conn->close();
?>

<!-- // Swal.fire({
            //   title: "Authentication",
            //   html: `
            //       <input type="text" id="username" name="username" class="swal2-input" value="<?php echo $_SESSION['Username']; ?>" readonly>
            //       <input type="password" id="password" password="password" class="swal2-input" placeholder="Password">
            //   `,
            //   inputAttributes: {
            //     autocapitalize: "off"
            //   },
            //   showCancelButton: true,
            //   confirmButtonText: "Continue",
            //   showLoaderOnConfirm: true,
            //   preConfirm: async () => {
            //     const username = document.getElementById('username').value;
            //     const password = document.getElementById('password').value;
            //     try {
            //       // Perform authentication with username and password
            //       // Example:
            //       const response = await fetch('authenticate.php', {
            //         method: 'POST',
            //         headers: {
            //           'Content-Type': 'application/json'
            //         },
            //         body: JSON.stringify({ username, password })
            //       });
            //       if (!response.ok) {
            //         const errorMessage = await response.text();
            //         throw new Error(errorMessage);
            //       }
            //       return true; // Authentication successful
            //     } catch (error) {
            //       Swal.showValidationMessage(`Authentication failed: ${error}`);
            //       return false;
            //     }
            //   },
            //   allowOutsideClick: () => !Swal.isLoading()
            // }).then((result) => {
            //   if (result.isConfirmed) {

            //     Swal.fire("Saved!", "", "success");
            //   }
            // }); -->