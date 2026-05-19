<?php
// admin/index.php
if (session_status() === PHP_SESSION_NONE) session_start();
header("Location: dashboard.php");
exit();
?>