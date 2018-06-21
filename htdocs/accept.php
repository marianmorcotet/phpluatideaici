<?php
require 'php\functions.php';
// require 'php\database_functions.php';
// require 'php\config.php';
// require 'php\mysqlconnect.php';
session_start();
show_header();
require "php/connect.php";
$invitation_id = $_GET['invitation_id'];
$accepted = $_GET['accepted'];

if($accepted){
  //if user accepts we change invitation_accepted
  $stmt = $con->prepare("UPDATE INVITATIONS SET invitation_opened = 1 WHERE invitation_id = ? AND invited_user_id = ?");
  $stmt->bind_param("ii", $invitation_id, $_SESSION['user_id']);
  $stmt->execute();
  if($stmt->affected_rows){
    afisare_nav();
    echo '
    <div class="afisare">
        <h3>Ai acceptat invitatia!</h3>
    <a href="invitations.php?invitation_id=0"><button class = "good" type="button">Catre invitatii</button></a>
    <a href="questions.php"><button class = "good" type="button">Catre intrebari</button></a>
    </div>';
    $stmt->close();
  }else{
    echo "EROAREEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE LA UPDATEEEEEEEEEEEEEEEEEEEEEEEEEEee";
  }


}else{
  //else we just delete this info
  $stmt = $con->prepare("DELETE FROM INVITATIONS WHERE invitation_id = ? AND invited_user_id = ?");
  $stmt->bind_param("i", $invitation_id, $_SESSION['user_id']);
  $stmt->execute();
  if($stmt->affected_rows){
    echo '
    <div class="intrebare">
        <h3>Ai refuzat invitatia!</h3>
    <a href="invitations.php"><button type="button">Catre invitatii</button></a>
    <a href="questions.php"><button type="button">Catre intrebari</button></a>
    </div>';
    $stmt->close();
  }else{
    echo "EROAREEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE LA DELETEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE";
  }
}

mysqli_close($con);

?>
