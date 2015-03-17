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
  $protocol_params = array(':domainid' => $_POST['domainID'],
                           ':protocol' => $_POST['protocolID'],
                           ':sockettype' => $_POST['socketTypeID']);
  $delete_sql = $db->prepare('DELETE FROM protocols WHERE domainid = :domainid AND protocol = :protocol AND sockettype = :sockettype;');
  $delete_sql->execute($protocol_params);
}
?>
