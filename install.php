<?php
session_start();
date_default_timezone_set('UTC');
if (!file_exists('mailconfig.db') && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['repassword']))
{
  $db = new PDO('sqlite:mailconfig.db');
  $email = $_POST['email'];
  $password = $_POST['password'];
  $repassword = $_POST['repassword'];
  if ($password === $repassword)
  {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $create_users = $db->prepare('CREATE TABLE users(email varchar('.EMAIL_MAX_LENGTH.'), password varchar('.SALTED_HASH_LENGTH.'), lastaccessed varchar('.DATETIME_LENGTH.'), lastip varchar('.IP_MAX_LENGTH.'))');
    $users_success = $create_users->execute();
    $create_domains = $db->prepare('CREATE TABLE domains(domainid INTEGER PRIMARY KEY, domain VARCHAR('.FQDN_MAX_LENGTH.'))');
    $domains_success = $create_domains->execute();
    $create_protocols = $db->prepare('CREATE TABLE protocols(domainid INTEGER, protocol INTEGER, sockettype INTEGER, hostname VARCHAR('.FQDN_MAX_LENGTH.'), port INTEGER, authentication INTEGER,
                                                                            PRIMARY KEY(domainid, protocol, sockettype))');
    $protocols_success = $create_protocols->execute();
    if($users_success && $domains_success && $protocols_success)
    {
      $iso8601_datetime = date('c');
      $ip_address = $_SERVER['REMOTE_ADDR'];
      $create_user_params = array(':email' => $email, ':password' => $password_hash, ':datetime' => $iso8601_datetime, ':lastip' => $ip_address);
      $create_user_sql = $db->prepare('INSERT INTO users(email, password, lastaccessed, lastip) VALUES(:email, :password, :datetime, :lastip)');
      if (!$create_user_sql->execute($create_user_params))
      {
        $create_user_error = $create_user_sql->ErrorInfo();
        unlink('mailconfig.db');
        die("Error creating user: {$create_user_error[2]}");
      }
      else
      {
        $_SESSION['email'] = $email;
        session_write_close();
        header('location: manage.php');
        echo 'Installation successful! Redirecting to the <a href="manage.php">management page</a>.';
        exit();
      }
    }
    else
    {
      //TODO: delete database
      unlink('mailconfig.db');
      die('Failed to create the mailconfig DB!');
    }
  }
}

?>