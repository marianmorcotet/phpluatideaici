<?php
require 'php\functions.php';
// require 'php\database_functions.php';
// require 'php\config.php';
// require 'php\mysqlconnect.php';
session_start();
show_header();
echo '<body>';
// require "php/connect.php";
// $_SESSION['signed_in'] = false;
// echo $_SESSION['signed_in']."afisare 1";
if($_SESSION['signed_in'] != true){
  login();
}else{
  afisare_nav();
  if($_SERVER['REQUEST_METHOD'] != 'POST'){
    echo '
      <form method="post" action="">
        <h4>Schimba parola!</h4>
        <input type="password" name="actual_pass"  placeholder="Parola actuala" class="pass" required>
        <input type="password" name="new_pass"  placeholder="Parola noua" class="pass" required>
        <input type="password" name="new_pass_conf"  placeholder="Confirma noua parola" class="pass" required>
        <button type="submit" ng-href="home.html">Trimite</button>
        </form>';
  }else{
    $actual_pass = $_POST['actual_pass'];
    $new_pass = $_POST['new_pass'];
    $new_pass_conf - $_POST['new_pass_conf'];
    if()
  }

echo '</body>';
?>
