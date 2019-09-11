<?php
require_once 'pdo.php';

session_start();

if(!isset($_SESSION['user_id'])){
  header('Location: login.php');
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

    <link rel="stylesheet" href="assets/jquery-ui.css">
    <link rel="stylesheet" href="assets/Bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
  </head>
  <body>

    <div class="container">
      <h2 class="mt-5 text-center">Welcome to Chat Application</h2>

      <div class="row mt-5">
        <div class="col-5">
          <h5>Users List</h5>
        </div>
        <div class="ml-auto col-6">
          <h5 class="text-right">Welcome <?php echo($_SESSION['user_name']);?> - <a href="logout.php">Logout</a> </h5>
        </div>
      </div>
      <div class="row">
        <div class="col-12" id="users"></div>
        <div id="user_model_details"></div>
      </div>
    </div>

    <script src="assets/jquery-3.3.1.js"></script>
    <script src="assets/Bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/jquery-ui.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){

        fetch_users();

        setInterval(function(){
          update_last_activity();
          fetch_users();
          update_chat_history_data();
        }, 5000);

        function fetch_users(){
          $.ajax({
            url: "fetch_users.php",
            method: "POST",
            success: function(data){
              $('#users').html(data);
            }
          });
        }

        function update_last_activity(){
          $.ajax({
            url: "update_last_activity.php",
            success: function(){

            }
          });
        }

        function make_chat_dialog_box(to_user_id, to_user_name){
          var modal_content ='<div id="user_dialog_'+to_user_id+'"\
            class="user_dialog" title="You have chat with '+to_user_name+'">';
          modal_content += '<div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom: 24px;\
              padding: 16px;" class="chat_history" data-touserid="'+to_user_id+'" id="chat_history_'+to_user_id+'">';
          modal_content += fetch_user_chat_history(to_user_id);
          modal_content += '</div>';
          modal_content += '<div class="form-group">';
          modal_content += '<textarea name="chat_message_'+to_user_id+'" id="chat_message_'+to_user_id+'"\
            class="form-control"></textarea>';
          modal_content += '</div><div class="form-group" align="right">';
          modal_content += '<button type="button" name="send_chat" id="'+to_user_id+'"\
            class="btn btn-info send_chat">Send</button></div></div>';
          $('#user_model_details').html(modal_content);
        }

        $(document).on('click', '.start_chat', function(){
          var to_user_id = $(this).data('touserid');
          var to_user_name = $(this).data('tousername');
          make_chat_dialog_box(to_user_id, to_user_name);

          $("#user_dialog_"+to_user_id).dialog({
            autoOpen: false,
            width: 400
          });

          $('#user_dialog_'+to_user_id).dialog('open');
        });

        $(document).on('click', '.send_chat', function(){
          var to_user_id = $(this).attr('id');
          var chat_message = $('#chat_message_'+to_user_id).val();
          $.ajax({
            url:"insert_chat.php",
            method: "POST",
            data: {to_user_id:to_user_id, chat_message:chat_message},
            success:function(data){
              $('#chat_message_'+to_user_id).val('');
              $('#chat_history_'+to_user_id).html(data);
            }
          });

        });

        function fetch_user_chat_history(to_user_id) {
          $.ajax({
            url:"fetch_user_chat_history.php",
            method:"POST",
            data: {to_user_id: to_user_id},
            success: function(data){
              $('#chat_history_'+to_user_id).html(data);
            }
          });
        }

        function update_chat_history_data() {
          $('.chat_history').each(function(){
            var to_user_id = $(this).data('touserid');
            fetch_user_chat_history(to_user_id);
          });
        }

      });
    </script>
  </body>
</html>
