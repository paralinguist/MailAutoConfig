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
  die('Failed to connect to mailconfig DB: ' . $ex->getMessage());
}
if (isset($_SESSION['email']) && isset($_POST['domainID']))
{
  $domain_params = array(':domainid' => $_POST['domainID']);
  $delete_sql = $db->prepare('DELETE FROM domains WHERE domainid = :domainid;');
  $delete_sql->execute($domain_params);
}
?>
