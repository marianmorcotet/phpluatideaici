<?php
require 'php\functions.php';
// require 'php\database_functions.php';
// require 'php\config.php';
require 'php\connect.php';
session_start();
show_header();
echo '<body>';
// error_reporting(0);
if($_SESSION['signed_in'] != true){
  login();
}else{
  $_SESSION['intrebare_adaugata'] = 0;
  $_SESSION['signed_in'] = false;
  if($_SESSION['signed_in'] != true){
    login();
  }
}
echo '</body>'
 ?>
