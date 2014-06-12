<?php
	 include 'DB_service.php';
     include_once "severAPI.php";
     /***prepare the request parameter***/
     //var $studentIDsArray = json_decode($POST["studentIDs"]);
      $notificationContent = $_POST['msg'];
	// echo $notificationContent;
	 $dbHelper = new DB_service;
	 $deviceToken = $dbHelper->getAllEmerDeviceTokens();
      //  print_r($deviceToken);
      

     /*start to push*/
   $alert = $notificationContent; 
   $sound = "default";
   $badge = 1;
   $moduleName = "emergencyinfo";
   $content = $notificationContent;
  
   $push = new Push($deviceToken);

   /* $bigBody = array(
        	),
   	'moduleName' => $moduleName,
   	'content' => $content
		),
	array(
	 'aps' => array(
            'alert' => $alert,
            'sound' => $sound,
            'badge' => (int)$badge
	array(
	 'aps' => array(
            'alert' => $alert,
            'sound' => $sound,
            'badge' => (int)$badge
        	),
   	'moduleName' => $moduleName,
   	'content' => $content
		)
	);
   print_r($bigBody);*/

    $body['aps'] = array(
            'alert' => $alert,
            'sound' => $sound,
            'badge' => (int)$badge
        );
   $body['moduleName'] = $moduleName;
   $body['content'] = $content;
 //  $push->pushData($body); 

 ?>
