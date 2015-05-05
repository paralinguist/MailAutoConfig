<?php

function get_table_bottom($domain_id)
{
  return "    
          </tbody>
        </table>
        <button id='add-prot-domain-$domain_id' class='add-protocol' role='button'><img src='img/add.png' alt='' class='button-icon'> Add a protocol</button>
        <button id='save-prot-domain-$domain_id' class='save-protocol save-button' role='button'><img src='img/save.png' alt='' class='button-icon'> Save this protocol</button>
        <button id='cancel-prot-domain-$domain_id' class='cancel-protocol cancel-button' role='button'><img src='img/cancel.png' alt='' class='button-icon'> Cancel</button>
      </div>";
}

require_once('header.php');
if (!isset($_SESSION['email']))
{
  header('location: index.php');
  exit();
}
else
{
  echo "    <h2>Welcome, {$_SESSION['email']}!<h2>\n";
  echo '    <div id="domains-container">';
  $domain_query = $db->prepare('SELECT d.domainid, d.domain,
                                       p.protocol, p.sockettype, p.hostname, p.port, p.authentication
                                FROM domains d
                                LEFT JOIN protocols p ON d.domainid = p.domainid');

  if($domain_query->execute())
  {
    $last_domain = '';
    $domain_id = 0;
    $count = 0;
    while ($protocol = $domain_query->fetch(PDO::FETCH_ASSOC))
    {
      $last_domain_id = $domain_id;
      $domain_id = $protocol['domainid'];
      $domain = $protocol['domain'];
      $protocol_id = $protocol['protocol'];
      $socket_type_id = $protocol['sockettype'];
      $hostname = $protocol['hostname'];
      $port = $protocol['port'];
      $auth_id = $protocol['authentication'];
      if ($last_domain != $domain)
      {
        if ($count)
        {
          echo get_table_bottom($last_domain_id);
        }
        echo "      
      <div id='domain-{$domain_id}-container' class='domain-container'>
        <h2 id='header-$domain_id'>
          $domain 
          <button id='edit-domain-$domain_id' class='edit-domain' role='button'><img src='img/edit.png' alt='' class='button-icon'></button>
          <button id='save-domain-$domain_id' class='save-domain save-button' role='button'><img src='img/save.png' alt='' class='button-icon'></button>
          <button id='delete-domain-$domain_id' class='delete-domain' role='button'><img src='img/delete.png' alt='' class='button-icon'></button>
        </h2>
        <table id='domain-$domain_id'>
          <thead>
            <tr>
              <th>Protocol</th>
              <th>Socket</th>
              <th>Hostname</th>
              <th>Port</th>
              <th>Authentication</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>";
        $last_domain = $domain;
        $count++;
      }
      if (!(is_null($protocol_id)))
      {
        echo "      
            <tr id='domain-$domain_id-$protocol_id-$socket_type_id'>
              <td>{$protocols[$protocol_id]}</td>
              <td>{$sockets[$socket_type_id]}</td>
              <td>$hostname</td>
              <td>$port</td>
              <td>{$auth_methods[$auth_id]}</td>
              <td>
                <button id='edit-domain-$domain_id-protocol-$protocol_id-socket-$socket_type_id' class='edit-protocol' role='button'><img src='img/edit.png' alt='' class='button-icon'></button>
                <button id='save-domain-$domain_id-protocol-$protocol_id-socket-$socket_type_id' class='save-protocol save-button' role='button'><img src='img/save.png' alt='' class='button-icon'></button>
                <button id='delete-domain-$domain_id-protocol-$protocol_id-socket-$socket_type_id' class='delete-protocol' role='button'><img src='img/delete.png' alt='' class='button-icon'></button>
              </td>
            </tr>";
      }
    }
    if ($count)
    {
      echo get_table_bottom($domain_id);
    }
    echo "    
    </div>
    <button id='create-domain'><img src='img/add.png' alt='' class='button-icon'> Add a domain</button>
    <button id='save-domain' class='save-button'><img src='img/save.png' alt='' class='button-icon'> Save this domain</button>";
  }
  else
  {
    $domain_error = $domain_query->ErrorInfo();
    echo("Failed to get domains: {$domain_error[2]}");
  }
}
?>


    <script>
<?php
echo "
      var protocolSelectText = \"<select name='protocol' id='protocol' class='select ui-widget-content ui-corner-all'>\" + 
                               \"  <option value='".POP3."'>{$protocols[POP3]}</option>\" +
                               \"  <option value='".IMAP."'>{$protocols[IMAP]}</option>\" +
                               \"  <option value='".SMTP."'>{$protocols[SMTP]}</option>\" +
                               \"</select>\";\n\n";

echo "
      var socketSelectText = \"<select name='socket' id='socket' class='select ui-widget-content ui-corner-all'>\" +
                             \"  <option value='".PLAIN."'>{$sockets[PLAIN]}</option>\" +
                             \"  <option value='".SSL."'>{$sockets[SSL]}</option>\" +
                             \"  <option value='".TLS."'>{$sockets[TLS]}</option>\" +
                             \"  <option value='".STARTTLS."'>{$sockets[STARTTLS]}</option>\" +
                             \"</select>\";\n\n";

echo "
      var authSelectText = \"<select name='authentication' id='authentication' class='select ui-widget-content ui-corner-all'>\" +
                           \"  <option value='".PASSWORD_CLEARTEXT."'>{$auth_methods[PASSWORD_CLEARTEXT]}</option>\" +
                           \"  <option value='".PASSWORD_ENCRYPTED."'>{$auth_methods[PASSWORD_ENCRYPTED]}</option>\" +
                           \"</select>\";\n\n";

echo "
      function getTableBottom(domainID)
      {
        return \"".preg_replace( "/\r|\n/", "\" + \n \"", get_table_bottom("'\"+domainID+\"'"))."\";
      }\n\n";

?>

      $(document).on('click', '.add-protocol', function()
      {
        $( '.add-protocol' ).hide();
        //TODO: shunt repeated code into function
        var domainID = this.id.split('-')[3];
        var domainTableID = 'domain-' + domainID;
        var domainTable = $( '#' + domainTableID );
        var tableBody = $( '#' + domainTableID + ' tbody' );
        $( '#save-prot-' + domainTableID ).show();
        $( '#cancel-prot-' + domainTableID ).show();
        tableBody.append(
          '<tr id="' + domainTableID + '-NewProtocol">' +
          '  <td>' + protocolSelectText + '</td>' +
          '  <td>' + socketSelectText + '</td>' +
          '  <td><input type="text" name="hostname" id="hostname" value="mail.hostname.tld" class="text ui-widget-content ui-corner-all"></td>' +
          '  <td><input type="text" name="port" id="port" value="995" class="text ui-widget-content ui-corner-all"></td>' +
          '  <td>' + authSelectText + '</td>' +
          '  <td></td>' +
          '</tr>');
      });
      
      $(document).on('click', '.cancel-protocol', function()
      {
        //TODO: shunt repeated code into function
        var domainID = this.id.split('-')[3];
        var domainTableID = 'domain-' + domainID;
        var row = $( '#' + domainTableID + '-NewProtocol');
        $( '#' + this.id ).hide();
        $( '#save-prot-' + domainTableID ).hide();
        $( '.add-protocol' ).show();
        row.remove();
      });

      $(document).on( 'click', '.save-protocol', function()
      {
        //TODO: shunt repeated code into function
        var domainID = this.id.split('-')[3];
        var domainTableID = 'domain-' + domainID;
        var row = $( '#' + domainTableID + '-NewProtocol');
        $( '#' + this.id ).hide();
        $( '#cancel-prot-' + domainTableID ).hide();
        $( '.add-protocol' ).show();
        var protocolText = $( '#protocol option:selected' ).text();
        var protocol = $( '#protocol' ).val();
        var socketText = $( '#socket option:selected' ).text();
        var socket = $( '#socket' ).val();
        var hostname = $( '#hostname' ).val();
        var port = $( '#port' ).val();
        var authText = $( '#authentication option:selected' ).text();
        var auth = $( '#authentication' ).val();
        var protocolPost =
              {
                domainid: domainID,
                protocol: protocol,
                sockettype: socket,
                hostname: hostname,
                port: port,
                authentication: auth
              }
        $.post( 'insert_protocol.php', protocolPost, function(protocolResult)
        {
        });

        //TODO: check for success

        row.empty()
        row.append(
          '  <td>' + protocolText + '</td>' +
          '  <td>' + socketText + '</td>'+
          '  <td>' + hostname + '</td>' +
          '  <td>' + port + '</td>' +
          '  <td>' + authText + '</td>' +
          '  <td></td>' +
          '</tr>');
        row.attr( 'id', domainTableID + '-' + protocol + '-' + socket );
        
      });

      $( '#create-domain' ).click(function()
      {
        $( '#create-domain' ).hide();
        $( '#save-domain' ).show();
        $( '#domains-container' ).append(
           '<div id="new-domain-container" class="domain-container">' +
           '  <h2 id="new-header"><input name="new-domain" id="new-domain" type="text" value="Enter new Domain Name"></h2>' +
           '</div>');
      });

      $( '#save-domain' ).click(function()
      {
        var domain = $( '#new-domain' ).val();
        var domainID = 0;
        var domainPost =
        {
          domain: domain
        }

        $.post( 'insert_domain.php', domainPost, function(returnid)
        {
          domainID = parseInt(returnid);
          if(typeof domainID === 'number')
          {
            if(domainID % 1 === 0)
            {
              $( '#save-domain' ).hide();
              $( '#create-domain' ).show();
              $( '#new-header' ).html(domain);
              $( '#new-domain-container' ).append(
                '  <table id="domain-' + domainID + '" class="ui-widget ui-widget-content">' +
                '    <thead>' +
                '      <tr class="ui-widget-header">' +
                '        <th>Protocol</th>' +
                '        <th>Socket</th>' +
                '        <th>Hostname</th>' +
                '        <th>Port</th>' +
                '        <th>Authentication</th>' +
                '        <th>Action</th>' +
                '      </tr>' +
                '    </thead>' +
                '    <tbody>' + getTableBottom(domainID) +
                '    </tbody>' +
                '  </table>' +
                '  <button id="add-prot-domain-' + domainID + '" class="add-protocol" role="button">Add a protocol</button>' + 
                '  <button id="save-prot-domain-' + domainID + '" class="save-protocol save-button" role="button">Save this protocol</button>' + 
                '</div>');
              $( '#new-header' ).attr( 'id', 'header-' + domainID );
              $( '#new-domain-container' ).attr( 'id', 'domain-' + domainID + '-container' );
            }
          }
        });
        var newHeader = $( '#new-domain-container' );
        if (!newHeader.length === 0)
        {
          newHeader.remove();
          //TODO: warn the user that creation failed
        }
      });
	  
    $(document).on( 'click', '.delete-protocol', function()
    {
      var splitID = this.id.split('-');
      var domainID = splitID[2];
      var protocolID = splitID[4]
      var socketTypeID = splitID[6];
      var tableRowID = 'domain-' + domainID + '-' + protocolID + '-' + socketTypeID;
      var protocolPost =
      {
        domainID: domainID,
        protocolID: protocolID,
        socketTypeID: socketTypeID
      }
      $.post( 'delete_protocol.php', protocolPost, function(protocolResult)
      {
        $('#'+tableRowID).remove();
      });
	  });

    </script> 
<?php
include_once('footer.php');
?>
