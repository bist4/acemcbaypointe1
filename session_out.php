<?php
$sessionTimeout = 10;

// Check if the session is active
if (isset($_SESSION['Active_Time']) && (time() - $_SESSION['Active_Time'] > $sessionTimeout)) {
    // If the session has expired, destroy the session and log the user out
    session_unset();
    session_destroy();
    header("Location: index.php?error=Session expired. Please log in again.");
    exit();
} else {
    // If the session is still active, update the last active time
    $_SESSION['Active_Time'] = time();
}
?>
<script type="text/javascript">
    var sessionTimeout = 10; // Session timeout in seconds
    var sessionTimeoutMilliseconds = sessionTimeout * 1000;

    function resetSessionTimeout() {
        clearTimeout(sessionTimeoutID);
        sessionTimeoutID = setTimeout(logout, sessionTimeoutMilliseconds);
    }

    function logout() {
        window.location.href = 'index.php'; // Create a logout script to destroy the PHP session
    }

    var sessionTimeoutID = setTimeout(logout, sessionTimeoutMilliseconds);

    document.onmousemove = resetSessionTimeout;
    document.onkeypress = resetSessionTimeout;
</script>