<?php
require 'php\functions.php';
// require 'php\config.php';
require 'php\mysqlconnect.php';

afisare_header();
echo '<body>';
$_SESSION['signed_in'] = false;
if($_SESSION['signed_in'] != true){
  login();
}else{
  mypage($_SESSION['user_id']);
}
echo '</body>'
 ?>
