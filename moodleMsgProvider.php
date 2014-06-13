<?php
	 include_once 'DB_service.php';
     include_once "severAPI.php";
     /***prepare the request parameter***/
     //var $studentIDsArray = json_decode($POST["studentIDs"]);
      $notificationContent =  json_decode ($_POST['notification'],true ); 
	//print_r($notificationContent['toStudentIDs']);
	
	 $dbHelper = new DB_service;
	
       

	 $alert = $notificationContent['sendMsg']; 
   	 $sound = "default";
  	 $badge = 1;
    	 $moduleName = "stellar";
  	 $content = $notificationContent[ 'courseID' ];
   

         echo "push a [" . $content . "]  msg: ".$alert . "</br></br>Devices list:</br>";
         echo "------------------------------------------------------------------------------------------------------------------------------------</br>"; 
       $deviceTokenArray = $dbHelper->transSIDstoTokens($notificationContent['toStudentIDs']);
	    echo "------------------------------------------------------------------------------------------------------------------------------------</br></br>";
	
         /*set badge according to this courseID and these tokens*/
	$dbHelper -> increaseCourseBadge($notificationContent[ 'courseID' ], $deviceTokenArray);
       
	 // print_r($deviceTokenArray);
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
   	 $push = new Push($deviceTokenArray);

   	$push->pushData($bigBody); 

 ?>
