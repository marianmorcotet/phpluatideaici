<?php
require 'php\functions.php';
// require 'php\database_functions.php';
// require 'php\config.php';
// require 'php\mysqlconnect.php';
session_start();
show_header();
echo '<body>';
if($_SESSION['signed_in'] != true){
  login();
}else{
  afisare_nav();
  show_questions($_SESSION['user_id']);

}
echo "</body>";
 ?>
