<?php
//
require 'php\functions.php';
function login(){
  require "php\connect.php";
  if($_SERVER['REQUEST_METHOD'] != 'POST')
    {
        /*the form hasn't been posted yet, display it
          note that the action="" will cause the form to post to the same page it is on */
        echo '
        <div id="log">
          <form method="post" action="">
            <h4> You have to login with your password sent by email! </h4>
            <input type="text" name="user_name"  placeholder="email" class="email" required>
            <input type="password" name="user_pass"  placeholder="password" class="pass" required>
            <button type="submit" ng-href="home.html">Login</button>
            </form>
        </div>';
    }
    else
    {
          //the form has been posted without errors, so save it
          //notice the use of mysql_real_escape_string, keep everything safe!
          //also notice the sha1 function which hashes the password
          $sql = "SELECT
                      user_id,
                      user_name
                  FROM
                      USERS
                  WHERE
                      user_name = '" . mysqli_real_escape_string($con, $_POST['user_name']) . "'
                  AND
                      user_pass = '" . $_POST['user_pass'] . "'";

          $result = mysqli_query($con, $sql);
          if(!$result)
          {
              //something went wrong, display the error
              echo 'Something went wrong while signing in. Please try again later.'."<br>";
          }
          else
          {
              //the query was successfully executed, there are 2 possibilities
              //1. the query returned data, the user can be signed in
              //2. the query returned an empty result set, the credentials were wrong
              if(mysqli_num_rows($result) == 0)
              {
                  header("Location: ..//index.php");
              }
              else
              {
                  //set the $_SESSION['signed_in'] variable to TRUE
                  $_SESSION['signed_in'] = true;
                  // $value = 1;
                  // change_value_of_signed_in($user_id, $value);
                  //we also put the user_id and user_name values in the $_SESSION, so we can use it at various pages
                  while($row = mysqli_fetch_assoc($result))
                  {
                      $_SESSION['user_id']    = $row['user_id'];
                      $_SESSION['user_name']  = $row['user_name'];
                      // $_SESSION['user_name']  = clear_user_name($_SESSION['user_name']);
                  }

                  header("Location: ..//home.php");
              }
          }
      }
      mysqli_close($con);
};


function get_last_insert_id(){
  require "connect.php";
  $sql = "SELECT max(question_id) FROM QUESTIONS;";
  $last_id = mysqli_query($con, $sql);
  $last_id = $last_id->fetch_row();
  mysqli_close($con);
  return $last_id[0];
};



function get_user_id_from_name($name){
  require "connect.php";
  $sql = "SELECT user_id FROM USERS WHERE user_name = '$name' ";
  $result = mysqli_query($con, $sql);
  $result = $result->fetch_row();
  mysqli_close($con);
  return $result[0];
};





function get_user_name_from_id($user_id){
  require "connect.php";
  $stmt = $con->prepare("SELECT user_name FROM USERS where user_id = ?");
  $stmt->bind_param("i",$user_id);
  if($stmt->execute()){
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();
    mysqli_close($con);
    return $result;
  }else{
    return 0;
  }
};



function insereaza_intrebare($user_id,$question_title,$question_content){
  //CHECK IF DATA IS COMPLETE
  if(isset($user_id,$question_title,$question_content)){
    require "connect.php";
    $stmt = $con->prepare("INSERT INTO QUESTIONS(question_id, user_id, question_title, question_content, question_date)
    VALUES(?, ?, ?, ?, ?)");
    $question_date = date("Y/m/d");
    $question_id = 0;
    $stmt->bind_param("iisss",$question_id,$user_id,$question_title,$question_content,$question_date);
    $stmt->execute();
    if ($stmt->affected_rows == 0) {
      echo "Execute failed at question: (" . $con->errno . ") " . mysqli_error($con)."<br>";
      $stmt->close();
      mysqli_close($con);
      return 0;
    };
    $stmt->close();
    mysqli_close($con);
    return 1;
  }else{
    return 0;
  }

};





function insereaza_invitatie($user_id,$invited_user_id,$question_id){
  //CHECK IF DATA IS COMPLETE
  if(isset($user_id,$invited_user_id,$question_id)){
    require "connect.php";
    $invitation_date = date("Y/m/d");
    $invitation_id = 0;
    $stmtt = $con->prepare("INSERT INTO INVITATIONS(invitation_id, user_id, invited_user_id, question_id, invitation_date)
    VALUES(?, ?, ?, ?, ?)");
    $stmtt->bind_param("iiiis",$invitation_id,$user_id,$invited_user_id,$question_id,$invitation_date);
    if (!$stmtt->execute()) {
      echo "Execute failed at inv: (" . $con->errno . ") " . mysqli_error($con)."<br>";
      $stmtt->close();
      mysqli_close($con);
      return 0;
    }
    $stmtt->close();
    mysqli_close($con);
    send_email_to($user_id);
    return 1;
  }else{
    return 0;
    }
};




function question_rollback($id){
  require "connect.php";
  $stmt = $con->prepare("DELETE FROM QUESTIONS WHERE question_id = ?");
  $stmt->bind_param("i",$id);
  $stmt->execute();
  $stmt->close();
  $stmt = $con->prepare("SELECT question_id FROM QUESTIONS WHERE question_id = ?");
  $stmt->bind_param("i",$id);
  $stmt->execute();
  if($stmt->num_rows){
    mysqli_close($con);
    return 0;
  }
  mysqli_close($con);
  return 1;
};












function question_title($question_id){
  require "connect.php";
  $stmt = $con->prepare("SELECT question_title FROM QUESTIONS WHERE question_id = ?");
  $stmt->bind_param("i", $question_id);
  if($stmt->execute()){
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();
    mysqli_close($con);
    return $result;
  } else {
    return 0;
  }

};




function get_invitation_sender_id($invitation_id){
  require "connect.php";
  $stmt = $con->prepare("SELECT user_id FROM INVITATIONS WHERE invitation_id = ?");
  $stmt->bind_param("i", $invitation_id);
  if($stmt->execute()){
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();
    mysqli_close($con);
    return $result;
  } else {
    return 0;
  }
};





function check_invitation($invitation_id,$sender_id){
  require "connect.php";
  $stmt = $con->prepare("SELECT * FROM INVITATIONS WHERE invitation_id = ? AND user_id = ? AND invited_user_id = ? AND invitation_opened IS NULL");
  $stmt->bind_param("iii", $invitation_id, $sender_id, $_SESSION['user_id']);
  $stmt->execute();
  if($stmt->num_rows){
    mysqli_close($con);
    return 1;
  }
  mysqli_close($con);
  return 0;
};






function get_an_invitation($user_id){
  require "connect.php";
  $stmt = $con->prepare("SELECT min(invitation_id) FROM INVITATIONS WHERE invited_user_id = ? AND invitation_opened IS NULL");
  $stmt->bind_param("i", $user_id);
  if($stmt->execute()){
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();
    mysqli_close($con);
    return $result;
  }

  mysqli_close($con);
  return 0;
};







 ?>
