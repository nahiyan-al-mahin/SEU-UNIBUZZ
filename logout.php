<!-- This php file logs out users -->

<?php
session_start();
session_destroy();
header("Location: login.php");
exit();
?>