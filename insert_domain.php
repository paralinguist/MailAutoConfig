<?php
session_start();
try
{
  global $db;
  if (file_exists('mailconfig.db'))
  {
    $db = new PDO('sqlite:mailconfig.db');
  }
}
catch(PDOException $ex)
{
  die("Failed to connect to mailconfig DB: " . $ex->getMessage());
}
if (isset($_SESSION['email']) && isset($_POST['domain']))
{
  $domain_params = array(':domain' => $_POST['domain']);
  $insert_sql = $db->prepare("INSERT INTO domains (domain)
                             VALUES (:domain)");
  $insert_sql->execute($domain_params);
  echo $db->lastInsertId();
}
?>