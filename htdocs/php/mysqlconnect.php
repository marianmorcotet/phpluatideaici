<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "admintw";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // prepare sql and bind parameters
    $stmt = $conn->prepare("INSERT INTO QUESTIONS (user_id, question_title, question_content)
    VALUES (:user_id, :question_title, :question_content)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':question_title', $question_title);
    $stmt->bindParam(':question_content', $question_content);
    $stmt->execute();

    echo "New records created successfully";
    }
catch(PDOException $e)
    {
    echo "Error: " . $e->getMessage();
    }
$conn = null;
?>
