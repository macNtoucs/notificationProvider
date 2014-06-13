<?php
 require_once 'DB_service.php';
class Push {
    public $deviceToken;//需要在构造时候设置

    //本地证书和密码
    public $localcert ='apns-dev.pem';
    public $passphrase = '';

    /*
* 功能：构造函数，设置deviceToken
*/
    function Push($deviceToken)
    {
        $this->deviceToken = $deviceToken;
    }
    /*
功能：生成发送内容并且转化为json格式
*/

    private function createPayload($message,$type,$sound)
    {
        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'sound' => $sound,
            'type' =>$type
        );
        
        // Encode the payload as JSON
        $payload = json_encode($body);

        return $payload;
    }

    // Put your private key's passphrase here:
   public function pushData($bigBody)
    {

          /* for ($i = 0; $i < count($this ->deviceToken); $i++){
	      $body['aps'] = array(
            'alert' => $bigBody[$i]['aps']['alert'],
            'sound' => $bigBody[$i]['aps']['sound'],
            'badge' => (int)$bigBody[$i]['aps']['badge'] //this badge is pns in deviceAndStudent table.
      			  );
 		 $body['moduleName'] = $bigBody[$i]['moduleName'];
  		 $body['content'] = $bigBody[$i]['content'];
		print_r($body);
		}  */
     $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert',$this->localcert);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);
        
        // Open a connection to the APNS server
        //$fp = stream_socket_client(“ssl://gateway.push.apple.com:2195“, $err, $errstr, 60, //STREAM_CLIENT_CONNECT, $ctx);
      
        $fp = stream_socket_client(
        'ssl://gateway.sandbox.push.apple.com:2195', $err,
        $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
	
           
        if (!$fp)
        exit("Failed to connect: $err $errstr" . PHP_EOL);

        echo 'Connected to APNS' . PHP_EOL;

        // 消息
       // $payload =json_encode($body);
        for ($i = 0; $i < count($this ->deviceToken); $i++){
	    $body['aps'] = array(
            'alert' => $bigBody[$i]['aps']['alert'],
            'sound' => $bigBody[$i]['aps']['sound'],
            'badge' => (int)$bigBody[$i]['aps']['badge'] //this badge is pns in deviceAndStudent table.
      			  );
 		 $body['moduleName'] = $bigBody[$i]['moduleName'];
  		 $body['content'] = $bigBody[$i]['content'];
		//print_r($body);
               
             $payload =json_encode($body);
            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $this ->deviceToken[$i]) . pack('n', strlen($payload)) . $payload;
            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));
        }

        if (!$result)
        {
            echo 'Message not delivered' . PHP_EOL;
        }
        else
        {
            echo 'Message successfully delivered' . PHP_EOL;
        }
        echo $result;
        // Close the connection to the server
        fclose($fp);
    }
   }
?>
