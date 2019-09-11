<?php
require_once('pdo.php');

session_start();

$salt = 'XyZzy12*_';

if( isset($_POST['user_name']) && isset($_POST['email']) && isset($_POST['password']) ){

    if( strlen($_POST['user_name']) == 0 || strlen($_POST['email']) == 0 ||
      strlen($_POST['password']) == 0 ){

        $_SESSION['error'] = "All feilds are required";
        header("Location: registeration.php");
        return;
      }

    if( strpos($_POST['email'], '@') === false ){

      $_SESSION['error'] = "Email Address must contain @";
      header("Location: registeration.php");
      return;
      }

    $password = hash('md5', $salt.$_POST['password']);

    $stmt = $pdo->prepare('INSERT INTO users ( user_name, email, password) VALUES ( :un, :em, :psw)');

    $stmt->execute(array(
      ':un' => $_POST['user_name'],
      ':em' => $_POST['email'],
      ':psw' => $password)
    );

    $_SESSION['success'] = "User Added Successfully, Now you can log in";
    header("Location: login.php");
    return;
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
          <h1>Create Account</h1>
        </div>
        <div class="card-body">
          <h4 class="card-title">Please fill in the following details</h4>
            <form method="POST">
              <?php
              if(isset($_SESSION['error'])){
                echo('<p style = "color : red;">'.htmlentities( $_SESSION['error'])."</p>\n");
              }
              unset($_SESSION['error']);
               ?>
               <div class="form-group">
                 <label for="formGroupExampleInput">User Name</label>
                 <input type="text" class="form-control" id="formGroupExampleInput" name="user_name" placeholder="Please write your name" required>
               </div>

              <div class="form-group">
                <label for="formGroupExampleInput1">Email</label>
                <input type="email" class="form-control" id="formGroupExampleInput1" name="email" placeholder="Email goes here" required>
              </div>
              <div class="form-group">
                <label for="formGroupExampleInput2">Password</label>
                <input type="password" class="form-control" id="formGroupExampleInput2" name="password" placeholder="Password" password>
              </div>
              <button type="submit" class="btn btn-primary">Create Account</button>
              <p>Already have account?<a href="login.php"> Login</a></p>
            </form>
        </div>
      </div>
    </div>

    <script src="assets/jquery-3.3.1.js"></script>
    <script src="assets/Bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
