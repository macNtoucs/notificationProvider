<?php
	 include 'DB_service.php';
     include_once "severAPI.php";
     /***prepare the request parameter***/
     //var $studentIDsArray = json_decode($POST["studentIDs"]);
      $notificationContent = $_POST['msg'];
	// echo $notificationContent;
	 $dbHelper = new DB_service;
	 $deviceToken = $dbHelper->getAllDeviceTokens();
     print_r($deviceToken);
      

     /*start to push*/
   $alert = $notificationContent; 
   $sound = "default";
   $badge = 1;
   $moduleName = "emergencyinfo";
   $content = $notificationContent;
  
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