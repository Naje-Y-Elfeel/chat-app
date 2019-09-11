<?php

define ('DB_HOST', 'localhost');
define ('DB_NAME', 'chat-app');
define ('DB_PASSWORD', 'root');
define ('DB_USER', 'root');
define ('DB_PORT', '3306');

$pdo = new PDO('mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);

// SEE THE ERROR FOLDER FOR DETAILS
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function fetch_user_last_activity($user_id, $connection){
  $statement = $connection->prepare("SELECT * FROM login_details WHERE user_id = :uid
                              ORDER BY last_activity DESC LIMIT 1");
  $statement->execute(array(':uid' => $user_id));
  $result = $statement->fetch(PDO::FETCH_ASSOC);

  return $result['last_activity'];
}

function fetch_user_chat_history($from_user_id, $to_user_id, $connection)
{
  $statement = $connection->prepare("SELECT * FROM chat_message WHERE (from_user_id = :from_uid
    AND to_user_id = :to_uid) OR (from_user_id = :to_uid AND to_user_id = :from_uid)
      ORDER BY timestamp");
  $statement->execute(array(
    ':from_uid'  => $from_user_id,
    ':to_uid'    => $to_user_id
  ));
  $result = $statement->fetchAll();
  $output = '<ul class="list-unstyled">';
  foreach ($result as $row) {
    $user_name = '';
    if ($row['from_user_id'] == $from_user_id) {
      $user_name = '<b class="text-success">You</b>';
    }else{
      $user_name = '<b class="text-danger">'.get_user_name($row["from_user_id"], $connection).'</b>';
    }
    $output .= '
      <li style="border-bottom:1px dotted #ccc">
        <p>'.$user_name.' - '.$row["chat_message"].'
          <div align="right">
              - <small><em>'.$row["timestamp"].'</em></small>
          </div>
        </p>
      </li>
    ';
  }
  $output .= '</ul>';
  $statement = $connection->prepare("UPDATE chat_message SET status = '0'
    WHERE from_user_id = :from_uid AND to_user_id = :to_uid AND status = '1'");
  $statement->execute(array(
    ':from_uid' => $to_user_id,
    ':to_uid'   => $from_user_id
  ));
  return $output;
}

function get_user_name($user_id, $connection)
{
  $statement = $connection->prepare("SELECT user_name FROM users WHERE user_id = :uid");
  $statement->execute(array(
    ':uid' => $user_id
  ));
  $result = $statement->fetchAll();
  foreach ($result as $row) {
    return $row['user_name'];
  }
}

function count_unseen_messages($from_user_id, $to_user_id, $connection)
{
  $statement = $connection->prepare('SELECT * FROM chat_message WHERE from_user_id = :from_uid
  AND to_user_id = :to_uid AND status = 1');
  $statement->execute(array(
    ':from_uid' => $from_user_id,
    ':to_uid'   => $to_user_id
  ));
  $count = $statement->rowCount();
  $output = '';
  if($count > 0){
    $output = '<span class="badge badge-success">'.$count.'</span>';
  }
  return $output;
}
