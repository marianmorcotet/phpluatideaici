<?php
//functions.php
// require 'php\database_functions.php';

function show_header(){
  echo '<header>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" type="text/css" href="css\stylesForForms.css">
  <link rel="stylesheet" type="text/css" href="css\stylesForBody.css">
  <link rel="stylesheet" type="text/css" href="css\stylesForNav.css">
  <link rel="stylesheet" type="text/css" href="css\stylesForQuestions.css">
  <link rel="stylesheet" type="text/css" href="css\styleForAfisare.css">
  <title> Gossip uaic </title>

  </header>';
};
function afisare_nav(){
  echo '<nav>
  <ul>
    <li>
      <a href="home.php">Home</a>
    </li>
    <li>
      <a href="questions.php">My gossips</a>
    </li>
    <li>
      <a href="invitations.php?invitation_id=0">Invitations</a>
    </li>
    <li>
      <a href="profile.php">'. clear_user_name($_SESSION['user_name']).' Profile</a>
    </li>
    <li>
      <a href="logout.php">Log Out</a>
    </li>
  </ul>
</nav>';
};


function login(){
  require "php\connect.php";
  if($_SERVER['REQUEST_METHOD'] != 'POST')
    {
        /*the form hasn't been posted yet, display it
          note that the action="" will cause the form to post to the same page it is on */
        echo '
        <div id="log">
          <form method="post" action="">
            <h4><a href="register.php"><button class = "good" type="button">Login mai jos sau apasa aici pentru inregistrare</button></a></h4>
            <input type="text" name="user_name"  placeholder="email" class="email" required>
            <input type="password" name="user_pass"  placeholder="parola" class="pass" required>
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


// function check_user_id_and_password


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

function question_content($question_id){
  require "connect.php";
  $stmt = $con->prepare("SELECT question_content FROM QUESTIONS WHERE question_id = ?");
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

function get_invitation_id_from_question_id($user_id,$question_id){
  require "connect.php";
  $stmt = $con->prepare("SELECT invitation_id FROM INVITATIONS WHERE invited_user_id = ? AND question_id = ?");
  $stmt->bind_param("ii", $user_id, $question_id);
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


function get_invitation_sender_id_from_question_id($user_id,$question_id){
  require "connect.php";
  $stmt = $con->prepare("SELECT user_id FROM INVITATIONS WHERE invited_user_id = ? AND question_id = ?");
  $stmt->bind_param("ii", $user_id, $question_id);
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



function check_question_invitations_for_delete($question_id){
  require "connect.php";
  $stmt = $con->prepare("SELECT * FROM INVITATIONS WHERE question_id = ?");
  $stmt->bind_param("i", $question_id);
  if($stmt->execute()){
    if(!$stmt->affected_rows){
      $stmt->close();
      mysqli_close($con);
      return 1;
    }
  }
  $stmt->close();
  mysqli_close($con);
  return 0;
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




function extract_user_names_from($string){
  $invited_users_names = explode(",", $string);
  return $invited_users_names;
};


// function change_value_of_signed_in($user_id, $value){
//   require "connect.php";
//   $stmt = $con->prepare("UPDATE USERS SET user_signed_in = ? WHERE user_id = ?");
//   $stmt->bind_param("ii",$value, $user_id);
//   $stmt->execute();
//   $stmt->close();
//   mysqli_close($con);
// };



function clear_user_name($user_name){
  $clear_user_name = explode("@",$user_name);
  return $clear_user_name[0];
};




function show_no_invites(){
  echo '
  <div class="afisare">
    <h4>One by one view.</h4>
    <p>Nu mai ai nici o invitatie!</p>
    <a href="questions.php?question_id=0&user_id='.$_SESSION['user_id'].'"><button class = "good" type="button">Catre intrebari</button></a>
    <a href="home.php"><button class = "good" type="button">Adauga o intrebare</button></a>
    </div>';
};





function show_invitation_by_id($invitation_id,$sender_id){
  $sender_name = get_user_name_from_id($sender_id);
  echo '
  <div class="afisare">
    <h4>One by one view.</h4>
    <p>Intrebare primita de la: '.$sender_name.'</p>
    <a href="accept.php?accepted=1&invitation_id='.$invitation_id.'."><button class = "good" type="button">Accepta</button></a>
    <a href="accept.php?accepted=0&invitation_id='.$invitation_id.'."><button class = "bad" type="button">Respinge</button></a>
    </div>';
};




function
($user_id){
  echo '
  <!-- LISTA INTREBARI -->
      <div id="coolList">
          <ul class="questions-list">';
  require "connect.php";
  $stmt = $con->prepare("SELECT question_id FROM INVITATIONS WHERE invited_user_id = ? AND invitation_opened IS NOT NULL ORDER BY question_id");
  $stmt->bind_param("i",$user_id);
  $stmt->bind_result($question_id);
  if($stmt->execute()){
    while($stmt->fetch()){
      $question_title = question_title($question_id);
      $question_content = question_content($question_id);
      $invitation_id = get_invitation_id_from_question_id($_SESSION['user_id'],$question_id);
      $sender_id = get_invitation_sender_id_from_question_id($_SESSION['user_id'],$question_id);
      $sender_name = get_user_name_from_id($sender_id);
      if(isset($question_title, $question_content, $invitation_id)){
        // echo '
        // <div class="afisare">
        //   <h4>Titlu: '.$question_title.'</h4>
        //   <p>Continut: '.$question_content.'</p>
        //   <a class="special" href="statistics_for_question.php?question_id='.$question_id.'">Genereaza statistici</a>
        //   <a class="bad" href="delete_question.php?invitation_id='.$invitation_id.'">Sterge intrebarea</a>
        //   <a class="good" href="question.php?question_id='.$question_id.'">Raspunde</a>
        //   </div>';
        echo '
        <li>
          <h4 class="title-q">Titlu: '.$question_title.'<br> de la: '.clear_user_name($sender_name).'</h4>

          <p class="contains">'.$question_content.'</p>
          <ul class="options-q">
            <li class="options"><a class="gen-stat" href="statistics_for_question.php?question_id='.$question_id.'">Genereaza statistici</a></li>
            <li class="options"><a class="delete-q" href="delete_question.php?invitation_id='.$invitation_id.'&question_id='.$question_id.'">Sterge intrebare</a></li>
            <li class="options"><a class="see-q" href="question.php?question_id='.$question_id.'">Raspunde</a></li>
          </ul>
        </li>
        <br>
        ';
      }else{
        print "question_title is $question_title";
        print "user_id is $user_id";
      }

    }if(!isset($question_id)){
      echo '
      <li>
        <h4 class="title-q">Nu mai ai intrebari</h4>
        <p class="contains">Daca nu te place nimeni????</p>
      </li>
      <br>';
    }
  }
  $stmt->close();
  echo '
    </ul>
  </div> <!-- coolList -->';
  mysqli_close($con);
};




function console_log( $data ){
  echo '<script>';
  echo 'console.log('. json_encode( $data ) .')';
  echo '</script>';
}




function send_email_to($user_id){
  $to = get_user_name_from_id($user_id);
  $subject = "LeAPPsa invitation!";
  $txt = "http://localhost//invitations.php?invitation_id=".$invitation_id;
  $headers = "From: ".$_SESSION['user_name']."\r\n";
  mail($to,$subject,$txt,$headers);
};

 ?>
