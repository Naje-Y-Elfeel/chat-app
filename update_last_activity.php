<?php

require_once 'pdo.php';

session_start();

$statement = $pdo->prepare("UPDATE login_details SET last_activity = now()
                      WHERE login_details_id = :lid");
$statement->execute(array(':lid' => $_SESSION['login_details_id']));

 ?>
