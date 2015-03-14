<?php
session_start();
include_once('security.php');
include_once('header.php');

if (isset($_POST['email']) && isset($_POST['password']))
{
  $email = $_POST['email'];
  $password = $_POST['password'];
  $auth_params = array(':email'=> $email);
  $auth_query = $db->prepare('SELECT password
                             FROM users
							               WHERE email = :email');
  if ($auth_query->execute($auth_params))
  {
    $auth_data = $auth_query->fetch(PDO::FETCH_ASSOC);
    $password_hash = $auth_data['password'];
    
    if (validate_password($password, $password_hash))
    {
      $_SESSION['email'] = $email;
      session_write_close();
      header('location: manage.php');
      echo 'Authentication successful! Redirecting to the <a href="manage.php">management page</a>.';
    }
    else
    {
      header('location: index.php?origin=failedauth');
      echo 'Authentication failed. You should redirect to the <a href="index.php?origin=failedauth">log in page</a>.';
    }
  }
  else
  {
    $auth_error = $auth_query->ErrorInfo();
    die("Error authenticating: {$auth_error[2]}");
  }
}
else
{
  header('location: index.php');
  echo 'There is no way you should be here. Something has gone terribly, horribly wrong. At any rate, <a href="index.php">here\'s an index link!</a>';
}
include_once('footer.php');

?>