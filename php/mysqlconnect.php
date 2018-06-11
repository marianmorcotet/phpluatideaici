<?php
//connect.php
// extension php_mysql.dll;
// include 'php_mysql';
$dbServer = 'localhost';
$dbUsername   = 'root';
$dbPassword   = '';
$dbName   = 'admintw';

$con = mysqli_connect($dbServer, $dbUsername,  $dbPassword, $dbName);
if(!$con){
  exit('Error: could not establish database connection');
}

?>
