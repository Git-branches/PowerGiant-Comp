<?php
session_start();
session_destroy();
header("Location: ../admin-powergiant/login.php");
exit;
?>
