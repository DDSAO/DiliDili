<?php
/* (1) SETTINGS + SUPPORT FUNCTIONS */
// VERBOSE - SHOWS SYSTEM MESSAGES
$verbose = false;

// CHANGE THE ADDRESS AND PORT TO YOUR OWN!
$address = '127.0.0.1';
$port = 12345;

// SUPPORT FUNCTION
// Source: https://srchea.com/build-a-real-time-application-using-html5-websockets
function unmask ($payload) {
  $length = ord($payload[1]) & 127;
  if ($length == 126) {
    $masks = substr($payload, 4, 4);
    $data = substr($payload, 8);
  } elseif ($length == 127) {
    $masks = substr($payload, 10, 4);
    $data = substr($payload, 14);
  } else {
    $masks = substr($payload, 2, 4);
    $data = substr($payload, 6);
  }
  $text = '';
  for ($i = 0; $i < strlen($data); ++$i) {
    $text .= $data[$i] ^ $masks[$i%4];
  }

  
  return $text;
}

/* (2) CREATE WEBSOCKET + LISTEN FOR CONNECTIONS */
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, $address, $port);
socket_listen($socket);
socket_set_nonblock($socket); // Allow multiple client connections

/* (3) ENDLESS LOOP */
$clients = [];
$buffer = "";

while (true) {

  /* (4) LISTEN FOR NEW CONNECTIONS */
  // WEBSOCKET HANDSHAKE HEADERS
  if (($client = socket_accept($socket)) !== false) {
    if ($verbose) { echo "New client connection\r\n"; }
    $request = socket_read($client, 5000);
    if ($verbose) { echo $request;  }
    preg_match('#Sec-WebSocket-Key: (.*)\r\n#', $request, $matches);
    $key = base64_encode(pack('H*', sha1($matches[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
    $headers = "HTTP/1.1 101 Switching Protocols\r\n";
    $headers .= "Upgrade: websocket\r\n";
    $headers .= "Connection: Upgrade\r\n";
    $headers .= "Sec-WebSocket-Version: 13\r\n";
    $headers .= "Sec-WebSocket-Accept: $key\r\n\r\n";
    socket_write($client, $headers, strlen($headers));
    
    $clients[] = $client;
    send(json_encode(array(
      'source'=>'system',
      'text'=>strval(count($clients)),
    )));
    if ($verbose) { echo "Total " . count($clients) . " client connection(s)\r\n"; }
  }
  

  if (count($clients)>0) {

    /* (5) LISTEN FOR DATA INPUT */
    $buffer = "";
    $contents = [];
    foreach ($GLOBALS['clients'] as $key => $c) {
        if (($chat = socket_recv($c,$buffer, 1000, MSG_DONTWAIT)) !== false) {
            //when user leaves, it will send a wired string with length 8. Dont know why
            //but it just happens everytime.
            if (strlen($buffer) === 8){
              unset($GLOBALS['clients'][$key]);
              send(json_encode(array(
                'source'=>'system',
                'text'=>strval(count($clients)),
              )));
              send(json_encode(array(
                'source'=>'admin',
                'text'=>'someone leaves',
              )));
            } else {
              send(unmask($buffer));
            }
            
            $buffer = "";
      } 
    }
  }
}

function send($content) {
    $handledContent = chr(129) . chr(strlen($content)) . $content;

    foreach ($GLOBALS['clients'] as $key=>$c) {
      if(socket_write($c, $handledContent,1000) === false) {
        unset($GLOBALS['clients'][$key]);
       
        send(json_encode(array(
          'source'=>'system',
          'text'=>strval(count($GLOBALS['clients'])),
        )));
        send(json_encode(array(
          'source'=>'admin',
          'text'=>'someone leaves',
        )));
      };
      
    }

}
?>
