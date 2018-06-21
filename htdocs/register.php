<?php
require 'php\functions.php';
// require 'php\database_functions.php';
// require 'php\config.php';
require 'php\connect.php';
// session_start();
show_header();
echo '<body>';
if($_SERVER['REQUEST_METHOD'] != 'POST')
  {
      /*the form hasn't been posted yet, display it
        note that the action="" will cause the form to post to the same page it is on */
      echo '
      <div id="log">
        <form method="post" action="">
          <h4> You have to login with your password sent by email! </h4>
          <input type="text" name="user_name"  placeholder="email" class="email" required>
          <input type="password" name="user_pass"  placeholder="parola" class="pass" required>
          <button type="submit" ng-href="home.html">Login</button>
          </form>
      </div>';
  }
  else
  {



echo '</body>'
 ?>
