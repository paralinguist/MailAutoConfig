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

require_once('common.php');

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
