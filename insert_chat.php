<?php

require_once 'pdo.php';

session_start();

$query = "INSERT INTO chat_message (to_user_id, from_user_id, chat_message, status)
  VALUES (:to_uid, :from_uid, :ch_msg, :status)";

$statement = $pdo->prepare($query);
$statement->execute(array(
  ':to_uid'   => $_POST['to_user_id'],
  ':from_uid' => $_SESSION['user_id'],
  ':ch_msg'   => $_POST['chat_message'],
  ':status'   => 1
));

if($statement){
  echo fetch_user_chat_history($_SESSION['user_id'], $_POST['to_user_id'], $pdo);
}
 ?>
