<?php
 

function viewBtn()
{
    if (isset($_SESSION['Username'])) {
        $loggedInName = $_SESSION['Username'];

        // Prepare the SQL statement with placeholders
        $query = "SELECT u.*, p.*, usr.*, m.ModuleName, m.ModuleID FROM privileges p
          INNER JOIN users u ON p.UserID = u.UserID
          INNER JOIN userroles usr ON u.UserRoleID = usr.UserRoleID
          INNER JOIN modules m ON p.ModuleID = m.ModuleID
          WHERE u.Username = ? AND p.Hide_Module = 1 AND p.Action_View IN (1, 0)";

        // Prepare the statement
        global $conn;
        $stmt = $conn->prepare($query);

        // Bind the parameter
        $stmt->bind_param("s", $loggedInName);

        // Execute the query
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Check if any row is fetched
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }

        // Close the statement
        $stmt->close();
    }
}
?>

 
