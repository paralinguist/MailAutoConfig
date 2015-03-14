<?php
session_start();
$auth_link = '<a href="index.php">log in</a>';
if (isset($_SESSION['email']))
{
  $auth_link = '<a href="logout.php">log out</a>';
}

$page_title = 'Mail Auto Config';
if (isset($page_description))
{
  $page_title .= ": $page_description";
}

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
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $page_title ?></title>
    <meta name="description" content="Mail Autoconfiguration Management">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  </head>
  <body>
<?php
//TODO: Some kind of nav bar?
echo "    <div id='navigation'>$auth_link</div>\n";
?>