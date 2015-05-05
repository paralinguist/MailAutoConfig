<?php
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
//Protocol IDs used in DB
DEFINE('POP3', 0);
DEFINE('IMAP', 1);
DEFINE('SMTP', 2);
$protocols = array(POP3 => 'POP3',
                   IMAP => 'IMAP',
                   SMTP => 'SMTP');

//Socket IDs used in DB
DEFINE('PLAIN', 0);
DEFINE('SSL', 1);
DEFINE('TLS', 2);
DEFINE('STARTTLS', 3);
$sockets = array(PLAIN => 'Plain', 
                 SSL => 'SSL', 
                 TLS => 'TLS', 
                 STARTTLS => 'StartTLS');

//Authentication methods, as defined by Mozilla. 
//Other methods are either discouraged or deprecated: https://developer.mozilla.org/en-US/docs/Mozilla/Thunderbird/Autoconfiguration/FileFormat/HowTo
DEFINE('PASSWORD_CLEARTEXT', 0);
DEFINE('PASSWORD_ENCRYPTED', 1);
$auth_methods = array(PASSWORD_CLEARTEXT => 'Cleartext (unencrypted)',
                      PASSWORD_ENCRYPTED => 'Encrypted');

//Various constant constraints
DEFINE('EMAIL_MAX_LENGTH', 254);
DEFINE('SALTED_HASH_LENGTH', 78);
DEFINE('DATETIME_LENGTH', 26);
DEFINE('IP_MAX_LENGTH', 45);
DEFINE('FQDN_MAX_LENGTH', 253);
?>
