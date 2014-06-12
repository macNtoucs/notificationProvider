<?php

   include_once "severAPI.php";

   $alert = $_POST[alert]; 
   $sound = $_POST[sound];
   $badge = $_POST[badge];
   $moduleName = $_POST[moduleName];
   $content = $_POST[content];
   echo $alert;
   $push = new Push('544c79118a76adf8ba9a8a00030d9360d2fe8e21c1071bfeacbd63c9912ef1da');

   $body['aps'] = array(
            'alert' => $alert.'  Server Time:'.date('Y-m-d H:i:s'),
            'sound' => $sound,
            'badge' => $badge
        );
   $body['moduleName'] = $moduleName;
   $body['content'] = $content.'  Server Time:'.date('Y-m-d H:i:s');
   $push->pushData($body);
?>