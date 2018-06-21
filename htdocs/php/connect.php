<?php
//connect.php
$dbServer = 'localhost';
$dbUsername   = 'root';
$dbPassword   = '';
$dbName   = 'admintw';

$con = new mysqli($dbServer, $dbUsername, $dbPassword, $dbName);
if ($con->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
 ?>
