<?php
require 'php\functions.php';
// require 'php\database_functions.php';
// require 'php\config.php';
// require 'php\mysqlconnect.php';
session_start();
show_header();
// require "php/connect.php";
$invitation_id = $_GET['invitation_id'];
$question_id = $_GET['question_id'];
require "php\connect.php";
$stmt = $con->prepare("DELETE FROM INVITATIONS WHERE invitation_id = ? AND invited_user_id = ? AND question_id = ? AND invitation_opened IS NOT NULL");
$stmt->bind_param("iii", $invitation_id, $_SESSION['user_id'], $question_id);
$stmt->execute();
if($stmt->affected_rows){
  $stmt->close();
  if(check_question_invitations_for_delete($question_id)){
    //everyone who was invited to that question has deleted it so delete the question
    if(question_rollback($question_id)){
      //question was deleted
      mysqli_close($con);
      header("Location: ..//questions.php");
    }
  }
}else{
  echo "EROAREEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE LA DELETEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE";
  $stmt->close();
}

mysqli_close($con);
header("Location: ..//questions.php");

?>
