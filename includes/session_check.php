<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /hotel/index.php");
    exit();
}
?>
