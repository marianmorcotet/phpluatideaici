<?php
session_start();
$_SESSION[] = 0;
session_destroy();
header("Location: ..//index.php");
 ?>
