<?php
// Created by Alesandro Slepcevic - alesandro@plus.hr
// Modified by Jonathan Ihlein - jonathan@ihle.in
// Limitations: AFAIK, this format cannot make a distinction between TLS, SSL and STARTTLS (which shouldn't matter these days anyway). "SSL" is either on or off.
// AuthRequired is hardcoded as "on". Always require authentication to use a server which clients access. If you have a special case where authentication is not required
// then you probably don't need autodiscovery. Contact me if you have a weird edgecase where this is necessary.
// Potential issues: The autodiscovery spec has SSL default to "on". This script defaults to "off".
header(':', true, 405);
header("Content-type: text/xml");
//Check - does this line need to be echoed? Something to do with the linefeed?
echo "<?xml version='1.0' encoding='UTF-8'?> \n";
?>
<Autodiscover xmlns="http://schemas.microsoft.com/exchange/autodiscover/responseschema/2006">
  <Response xmlns="http://schemas.microsoft.com/exchange/autodiscover/outlook/responseschema/2006a">
    <Account>
      <AccountType>email</AccountType>
      <Action>settings</Action>
<?php
require_once('common.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
  $post_text = file_get_contents('php://input');
  $string = $post_text;
  $matches = array();
  $pattern = '/[A-Za-z0-9_-]+@[A-Za-z0-9_-]+.([A-Za-z0-9_-][A-Za-z0-9_]+)/';
  preg_match($pattern, $string, $matches);
  $email_domain = explode('@', $matches[0]);
}

if (isset($_GET['email']))
{
  $string = $_GET['email'];
  $matches = array();
  $pattern = '/[A-Za-z0-9_-]+@[A-Za-z0-9_-]+.([A-Za-z0-9_-][A-Za-z0-9_]+)/';
  preg_match($pattern, $string, $matches);
  $email_domain = explode('@', $matches[0]);
}

if (isset($email_domain))
{
  $email_address = $matches[0];
  $domain = $email_domain[1];
  $domain_params = array(':domain' => $domain);
  $domain_query = $db->prepare('SELECT d.domainid,
                                       p.protocol, p.sockettype, p.hostname, p.port, p.authentication
                                FROM domains d
                                LEFT JOIN protocols p ON d.domainid = p.domainid
                                WHERE d.domain = :domain');
  if($domain_query->execute($domain_params))
  {
    while ($protocol = $domain_query->fetch(PDO::FETCH_ASSOC))
    {
      $protocol_id = $protocol['protocol'];
      $socket_type_id = $protocol['sockettype'];
      $ssl = 'off';
      if ($socket_type_id)
      {
        $ssl = 'on';
      }
      $hostname = $protocol['hostname'];
      $port = $protocol['port'];
      $auth_id = $protocol['authentication'];
      $spa = 'off';
      if ($auth_id)
      {
        $spa = 'on';
      }
      //$sockets[$socket_type_id]
      echo "
      <Protocol>
        <Type>{$protocols[$protocol_id]}</Type>
        <Server>$hostname</Server>
        <Port>$port</Port>
        <LoginName>$email_address</LoginName>
        <DomainName>$domain</DomainName>
        <SPA>$spa</SPA>
        <SSL>$ssl</SSL>
        <AuthRequired>on</AuthRequired>
      </Protocol>\n";
    }
  }
  else
  {
    //Just assume defaults? Or fail?
  }
}
?>
    </Account>
  </Response>
</Autodiscover>
