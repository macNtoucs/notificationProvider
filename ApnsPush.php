<?php

   include_once "severAPI.php";

   $alert = $_POST[alert]; 
   $sound = $_POST[sound];
   $badge = $_POST[badge];
   $moduleName = $_POST[moduleName];
   $content = $_POST[content];
   $deviceToken = $_POST[deviceToken];
   echo $alert;
   $push = new Push($deviceToken);

   $body['aps'] = array(
            'alert' => $alert.'  Server Time:'.date('Y-m-d H:i:s'),
            'sound' => $sound,
            'badge' => (int)$badge
        );
   $body['moduleName'] = $moduleName;
   $body['content'] = $content.'  Server Time:'.date('Y-m-d H:i:s');
   $push->pushData($body);
?>