<?php
include_once('header.php');
$_SESSION = array();
session_destroy();
header('Location: index.php');
?>
    <h1>Successfully logged out...</h1>
    You should redirect to the <a href="index.php?origin=loggedout">log in page</a>.
<?php
include_once('footer.php');
?>