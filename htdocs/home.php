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
  if($_SERVER['REQUEST_METHOD'] != 'POST')
    {
        // echo $_SESSION['intrebare_adaugata'];
        /*the form hasn't been posted yet, display it
          note that the action="" will cause the form to post to the same page it is on */
        if($_SESSION['intrebare_adaugata'] == 1){
        //SUCCES
          echo '
          <form method="post" action="">
            <h4>Succes, mai adaugi o intrebare? </h4>
            <input type="text" maxlength="100" placeholder="Titlu" name="question_title" required>
            <input type="text" maxlength="500" placeholder="Continut" name="question_content" required>
            <input type="text" maxlength="1000" placeholder="mail@info.uaic.ro, mail..." name="question_invitations" required>
            <button type="submit">Submit</button>
          </form>';
        }else if($_SESSION['intrebare_adaugata'] == 0){
          //FIRST TIME
          echo '
          <form method="post" action="">
            <h4>Adauga o intrebare: </h4>
            <input type="text" maxlength="100" placeholder="Titlu" name="question_title" required>
            <input type="text" maxlength="500" placeholder="Continut" name="question_content" required>
            <input type="text" maxlength="1000" placeholder="mail@info.uaic.ro, mail..." name="question_invitations" required>
            <button type="submit">Submit</button>
          </form>';
          }else{
            //FAIL
            echo '
            <form method="post" action="">
              <h4>Eroare, incerci din nou? </h4>
              <h4>Daca unul din cei invitati a fost scris gresit, doar acel user nu a fost invitat<h4>
              <input type="text" maxlength="100" placeholder="Titlu" name="question_title" required>
              <input type="text" maxlength="500" placeholder="Continut" name="question_content" required>
              <input type="text" maxlength="1000" placeholder="mail@info.uaic.ro, mail..." name="question_invitations" required>
              <button type="submit">Submit</button>
            </form>';
          }
    }
    else{
      $user_id = $_SESSION['user_id'];
      $question_title = $_POST['question_title'];
      $question_content = $_POST['question_content'];
      $question_invitations = $_POST['question_invitations'];
      if(insereaza_intrebare($user_id,$question_title,$question_content)){
        $question_id = get_last_insert_id();
        $user_name_array = extract_user_names_from($question_invitations);
        for ($iter=0 ; $iter<count($user_name_array) ;$iter++){
          $value = $user_name_array[$iter];
          $invited_user_id = get_user_id_from_name($value);
          if(insereaza_invitatie($user_id,$invited_user_id,$question_id)){
            // Print "insert invitatie nu a mers";
            $_SESSION['intrebare_adaugata'] = 1;
            header("Location: ..//home.php");
            }else{
              //ROLLBACK THE QUESTION INSERT
              if(!question_rollback($question_id)){
                exit("DE CEEEEEEEEEEEEEe");
              }
              $_SESSION['intrebare_adaugata'] = 2;
              header("Location: ..//home.php");
            }
        }
      }else{
        //ROLLBACK THE QUESTION INSERT
        if(!question_rollback($question_id)){
          exit("DE CEEEEEEEEEEEEEe");
        }
        $_SESSION['intrebare_adaugata'] = 2;
        header("Location: ..//home.php");
      }
    }
}


echo '</body>';
 ?>
