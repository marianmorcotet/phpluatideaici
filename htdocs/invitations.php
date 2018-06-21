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
  $invitation_id = 0;
  $invitation_id = $_GET['invitation_id'];
  if($invitation_id){
    //url from email so this means this invitation has priority
    $sender_id = get_invitation_sender_id($invitation_id);//get sender id
    if($sender_id){
      if(check_invitation($invitation_id,$sender_id)){
        //if it passed the check show it
        show_invitation($invitation_id,$sender_id);
      }
    }
  }else{
    //paged was accesed from navigator so show all invitations 1 by 1;
    $invitation_id = get_an_invitation($_SESSION['user_id']);
    if($invitation_id == 0){
      //if id is still 0 it means that there are no invites so inform the user
      show_no_invites();
    }else{
      $sender_id = get_invitation_sender_id($invitation_id);
      if($invitation_id){
        //there are invitations to show
        show_invitation_by_id($invitation_id,$sender_id);
      }
    }


  }
}


echo '</body>';
 ?>
