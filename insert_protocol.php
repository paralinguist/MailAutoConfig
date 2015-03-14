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
if (isset($_SESSION['email']) && isset($_POST['domainid']))
{
  $protocol_params = array(':domainid' => $_POST['domainid'],
                           ':protocol' => $_POST['protocol'],
                           ':sockettype' => $_POST['sockettype'],
                           ':hostname' => $_POST['hostname'],
                           ':port' => $_POST['port'],
                           ':authentication' => $_POST['authentication']);
  $insert_sql = $db->prepare("INSERT INTO protocols (domainid, protocol, sockettype, hostname, port, authentication)
                              VALUES (:domainid, :protocol, :sockettype, :hostname, :port, :authentication)");
  $insert_sql->execute($protocol_params);
}
?>