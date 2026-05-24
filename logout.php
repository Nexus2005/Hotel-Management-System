<?php
session_start();
session_unset();
session_destroy();
header("Location: /hotel/index.php");
exit();
?>
