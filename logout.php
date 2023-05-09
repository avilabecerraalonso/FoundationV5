<?php
// Start the session
session_start();

session_unset();
session_destroy();
echo '<script>localStorage.removeItem("session_id");</script>';
setcookie('session_id', '', time() - 3600, "/");

// Redirect the user to the login page
header('Location: ./');
exit;
?>
