<?php
  $sql_insert_question = "INSERT INTO QUESTIONS(user_id, question_title, question_content, question_date) VALUES('{$user_id}', '{$question_title}' , '{$question_content}' , sysdate())";
  $sql_insert_invitation = "INSERT INTO INVITATIONS(user_id, invited_user_id, question_id, invitation_date) VALUES('{$user_id}', '{$invited_user_id}', '{$question_id}', sysdate())";
 ?>
