<?php
session_start();
session_unset();
session_destroy();
header("Location: index.php"); // Redirect to homepage or login page
exit();
?>
