<?php
require_once('pdo.php');

session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    return;
}

$salt = 'XyZzy12*_';

if ( isset( $_POST['email'] ) && isset( $_POST['password'] ) ){

  if( strlen($_POST['email']) < 1 || strlen($_POST['password']) < 1 )
  {
    $_SESSION['error'] = "Email and Password are required";
    header('Location: login.php');
    return ;
  }
  else {
    $check = hash('md5', $salt.$_POST['password']);
    $stmt = $pdo->prepare('SELECT user_id, user_name FROM users
    WHERE email = :em AND password = :pw');
    $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false){
      $_SESSION['user_name'] = $row['user_name'];
      $_SESSION['user_id'] = $row['user_id'];

      $newStatement = $pdo->prepare("INSERT INTO login_details (user_id) VALUES (:uid)");
      $newStatement->execute( array( ':uid' => $row['user_id'] ) );
      $_SESSION['login_details_id'] = $pdo->lastInsertId();

      header("Location: index.php");
      return ;
    }
    else {
      $_SESSION['error'] = "Incorrect email or password";
      header('Location: login.php');
      return ;
    }
  }
}
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title></title>

    <link rel="stylesheet" href="assets/Bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
  </head>
  <body>

    <div class="row justify-content-center">
      <div class="card">
        <div class="card-header">
          <h1>Chat Application Login</h1>
        </div>
        <div class="card-body">
          <h4 class="card-title">Please Log in</h4>
            <form method="POST">
              <?php
              if(isset($_SESSION['error'])){
                echo('<p style = "color : red;">'.htmlentities( $_SESSION['error'])."</p>\n");
              }
              unset($_SESSION['error']);

              if(isset($_SESSION['success'])){
                echo('<p style = "color : green;">'.htmlentities( $_SESSION['success'])."</p>\n");
              }
              unset($_SESSION['success']);
               ?>
              <div class="form-group">
                <label for="formGroupExampleInput">Email</label>
                <input type="email" class="form-control" id="formGroupExampleInput" name="email" placeholder="Example input" required>
              </div>
              <div class="form-group">
                <label for="formGroupExampleInput2">Password</label>
                <input type="password" class="form-control" id="formGroupExampleInput2" name="password" placeholder="Another input" password>
              </div>
              <button type="submit" class="btn btn-primary">Log In</button>
              <p>Dont have account? <a href="registeration.php">Click here to create one</a></p>
            </form>
        </div>
      </div>
    </div>

    <script src="assets/jquery-3.3.1.js"></script>
    <script src="assets/Bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
