<?php
require_once 'pdo.php';

session_start();

$statement = $pdo->prepare("SELECT * FROM users WHERE user_id != :cuid");
$statement->execute(array(':cuid' => $_SESSION['user_id']));
$result = $statement->fetchAll();

$output = '
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>User Name</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>';

foreach ($result as $row) {

  $status = '';
  $current_timestamp = strtotime(date('Y-m-d H:i:s').'-10 second');
  $current_timestamp = date('Y-m-d H:i:s', $current_timestamp);
  $user_last_activity = fetch_user_last_activity($row['user_id'], $pdo);

  if ($user_last_activity > $current_timestamp) {
    $status = "<p class='badge badge-success'>Online</p>";
  } else {
    $status = "<p class='badge badge-danger'>Offline</p>";
  }

  $output .= '
    <tr>
      <td>'.$row['user_name'].' '.count_unseen_messages($row['user_id'], $_SESSION['user_id'], $pdo).'</td>
      <td>'.$status.'</td>
      <td><button type="button" class="btn btn-info btn-sm start_chat"
        data-touserid="'.$row['user_id'].'" data-tousername="'.$row['user_name'].'">
          Start Chat
        </button>
      </td>
    </tr>';
}

$output .='</tbody></table>';

echo($output);

?>
