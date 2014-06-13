<?php
	 include 'DB_service.php';
     include_once "severAPI.php";
     /***prepare the request parameter***/
     //var $studentIDsArray = json_decode($POST["studentIDs"]);
      $notificationContent = $_POST['msg'];
	// echo $notificationContent;
	 $dbHelper = new DB_service;
	 $deviceTokenArray = $dbHelper->getAllEmerDeviceTokens();
      //  print_r($deviceToken);
      

     /*start to push*/
   $alert = $notificationContent; 
   $sound = "default";
   $badge = 1;
   $moduleName = "emergencyinfo";
   $content = $notificationContent;
  
   $push = new Push($deviceTokenArray);

  $bigBody = array();
	foreach($deviceTokenArray as $deviceToken){
		$badge = $dbHelper -> getPNSBadge($deviceToken);
		$newBody = array(
				'aps' => array(
					    'alert' => $alert,
					    'sound' => $sound,
					    'badge' => $badge
					),
			   	'moduleName' => $moduleName,
			   	'content' => $content					
			);
		 array_push( $bigBody,$newBody);
	}  
     $dbHelper -> increaseEmerBadge();
     $push->pushData($bigBody); 

 ?>
