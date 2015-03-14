<?php
$install = false;
$page_description = 'Authentication - Please log in';
include_once('header.php');
if (!file_exists('mailconfig.db'))
{
  $install = true;
}
else
{
  $user_query = $db->prepare('SELECT lastaccessed FROM users');
  $user_query->execute();
  $user_data = $user_query->fetch(PDO::FETCH_ASSOC);
  if (!($user_data))
  {
    $install = true;
  }
}

if ($install)
{
  $page_description = 'Installation - Create your account';
}

if (isset($_SESSION['email']))
{
  header('location: manage.php');
  exit();
}
?>
    <form method="post" action="<?php echo $install ? 'install.php' : 'login.php' ?>">
      <p>
        <label for="email" class="login-label">Email:</label>
        <input id="email" name="email" type="text" class="login-input"><br>
        <label for="password" class="login-label">Password:</label>
        <input id="password" name="password" type="password" class="login-input"><br>
<?php 
  if ($install)
  {
    echo '        <label for="repassword" class="login-label">Re-enter password:</label>
                  <input id="repassword" name="repassword" type="password" class="login-input"><br>';
  }
?>
        <input id="submit" name="submit" type="submit" class="login-button" value="<?php echo $install ? 'create admin' : 'log in' ?>">
      </p>
    </form>

<?php
include_once('footer.php');
?>
