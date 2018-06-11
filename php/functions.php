<?php
//functions.php
function afisare_header(){
  echo '<header>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" type="text/css" href="css\stylesForForms.css">
  <link rel="stylesheet" type="text/css" href="css\stylesForBody.css">
  <link rel="stylesheet" type="text/css" href="css\stylesForNav.css">
  <link rel="stylesheet" type="text/css" href="css\stylesForQuestions.css">
  <title> Gossip uaic </title>

  </header>';
};
function afisare_nav(){
  echo '<nav>
  <ul>
    <li>
      <a href="home.html">Home</a>
    </li>
    <li>
      <a href="mypage.html">My gossips</a>
    </li>
    <li>
      <a href="topic.html">Top</a>
    </li>
    <li>
      <a href="invi.html">Invitations</a>
    </li>
  </ul>
</nav>';
};
function login(){
  require 'mysqlconnect.php';
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
              echo 'Something went wrong while signing in. Please try again later.';
              // echo mysqli_error($con); //debugging purposes, uncomment when needed
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

                  //we also put the user_id and user_name values in the $_SESSION, so we can use it at various pages
                  while($row = mysqli_fetch_assoc($result))
                  {
                      $_SESSION['user_id']    = $row['user_id'];
                      $_SESSION['user_name']  = $row['user_name'];
                  }

                  mypage($_SESSION['user_id']);;
              }
          }
      }
};
function afisare_adauga_intrebare(){
  echo '
<form method="post">
  <h4>Adauga o intrebare: </h4>
  <input type="text" placeholder="Titlu" name="question_title" required>
  <input type="text" placeholder="Continut" name="question_content" required>
  <input type="text" placeholder="mail@info.uaic.ro, mail..." name="question_invitations" required>
  <button type="submit">Submit</button>
</form>';
};
function mypage(string $logged_user_id){
  afisare_nav();
  afisare_adauga_intrebare();

};
// afisare_intrebari($user_id){
//   $sql = "SELECT count(*) FROM QUESTIONS where user_id = '" . $_SESSION['user_id'] . "'";
//   // $sql = "SELECT
//   //             count(*)
//   //         FROM
//   //             QUESTIONS
//   //         WHERE
//   //             user_id = "$user_id;
//   $result = $conn->query($sql);
//   echo $result;
//   // $ID_prima_intrebare = "SELECT id_question FROM questions where id_user=id_player and ROWNUM=1 order by id_question asc;";
//   // for ($i = 1; $i <= $Nr_intrebari; $i++) {
//   //         $Titlu_intrebare = "SELECT title FROM questions where id_user=id_player and ROWNUM=1 order by id_question asc;";
//   //         $Intrebare = "SELECT content FROM questions where id_user=id_player and ROWNUM=1 order by id_question asc;";
//   //         $ID_prima_intrebare = "SELECT id_question FROM questions where id_user=id_player and id_question > $ID_prima_intrebare and ROWNUM=1 order by id_question asc;";
//   //
//   //         echo'<div id="coolList">
//   //         <ul class="questions-list">
//   //             <li>
//   //               <h4 class="title-q">';
//   //         echo $Titlu_intrebare;
//   //         echo '</h4>
//   //
//   //               <p class="contains">';
//   //         echo $Intrebare;
//   //         echo'</p>
//   //               <ul class="options-q">
//   //                 <li class="options"><a class="gen-stat" href="statistici.html">Genereaza statistici</a></li>
//   //                 <li class="options"><a class="delete-q" href="mypage1.html">Sterge intrebarea</a></li>
//   //                 <li class="options"><a class="see-q" href="raspunsuri.html">Vezi raspunsuri</a></li>
//   //               </ul>
//   //             </li>
//   //          </div> <!-- coolList -->';
//   //        };
// };
 ?>
