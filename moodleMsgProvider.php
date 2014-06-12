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
	$dbHelper -> increaseCourse($courseID, $deviceTokenArray);
       
	//  print_r($deviceToken);
   	 $push = new Push($deviceTokenArray);
  	 $body['aps'] = array(
            'alert' => $alert,
            'sound' => $sound,
            'badge' => (int)$badge //this badge is pns in deviceAndStudent table.
        );
 	 $body['moduleName'] = $moduleName;
  	 $body['content'] = $content;
   	//$push->pushData($body); 
      //echo json_encode(array('B5704R2A' => 3, 'B5703N54' => 1));

 ?>
