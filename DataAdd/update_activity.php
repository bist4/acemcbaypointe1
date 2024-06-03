<!-- <

require('../config/db_con.php');
session_start();

//Get the last start date from table maintenance
$sql = "SELECT StartDateTime FROM maintenance ORDER BY MaintenanceID DESC LIMIT 1";
$q = $conn->query($sql);
$row = $q->fetch_assoc();

$startDatetime = $row['StartDateTime'];
$now = new DateTime('now', new DateTimeZone('Asia/Manila'));

// Calculate the difference
$interval = $startDatetime->diff($now);
// Get the difference in days
$days = $interval->format('%a');
// Get the difference in hours, minutes, and seconds
$time = $interval->format('%H:%I:%S');

$endDateTime = "$days days and $time hours";
$status = 1;

$sql = "UPDATE maintenance SET EndDate =?, TimeTrace=?, Status=? ORDER BY MaintenanceID DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $now, $endDateTime, $status);

if ($stmt->execute()) {
    header("Location: ../maintenance.php");
} else {
    // Insertion failed
    // You can echo an error message if needed
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$stmt->close();

?> -->



<!-- <
require('../config/db_con.php');
session_start();

$now = new DateTime('now', new DateTimeZone('Asia/Manila'));
$nowFormatted = $now->format('Y-m-d H:i:s'); // Format datetime as string

$sql = "UPDATE maintenance 
        SET EndDate = '$nowFormatted', Status = 1 
        ORDER BY MaintenanceID DESC 
        LIMIT 1";

if ($conn->query($sql) === TRUE) {
    echo "Maintenance record updated successfully.";
} else {
    echo "Error updating maintenance record: " . $conn->error;
}

// Optionally, you can close the session
session_write_close();
?> -->



<?php
require ('../config/db_con.php');
session_start();

$now = new DateTime('now', new DateTimeZone('Asia/Manila'));
$nowFormatted = $now->format('Y-m-d H:i:s'); // Format datetime as string

$sql = "UPDATE maintenance 
        SET EndDate = '$nowFormatted', Status = 1 
        WHERE MaintenanceID = (SELECT MaintenanceID FROM maintenance ORDER BY MaintenanceID DESC LIMIT 1)";

if ($conn->query($sql) === TRUE) {
    echo "Maintenance record updated successfully.";

    // Calculate time difference
    $startDateTime = new DateTime($_SESSION['StartDateTime']); // Assuming you stored StartDateTime in session
    $now = new DateTime(); // Current time
    $interval = $startDateTime->diff($now);

    // Calculate total minutes
    $totalMinutes = $interval->days * 24 * 60; // Days to minutes
    $totalMinutes += $interval->h * 60; // Hours to minutes
    $totalMinutes += $interval->i; // Add remaining minutes

    // Convert total minutes to days, hours, and minutes
    $days = floor($totalMinutes / (24 * 60));
    $totalMinutes %= (24 * 60);
    $hours = floor($totalMinutes / 60);
    $minutes = $totalMinutes % 60;

    $total = "$days day(s), $hours hour(s), $minutes minute(s)";

    // Update TimeTrace column
    $updateTimeTraceSql = "UPDATE maintenance SET TimeTrace = '$total' WHERE MaintenanceID = (SELECT MaintenanceID FROM maintenance ORDER BY MaintenanceID DESC LIMIT 1)";
    if ($conn->query($updateTimeTraceSql) === TRUE) {
        echo " TimeTrace updated successfully.";
    } else {
        echo "Error updating TimeTrace: " . $conn->error;
    }
} else {
    echo "Error updating maintenance record: " . $conn->error;
}

// Optionally, you can close the session
session_write_close();
?>