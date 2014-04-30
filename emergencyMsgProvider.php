<?php
	 include 'DB_service.php';

     /***prepare the request parameter***/
     /*var $studentIDsArray = json_decode($POST["studentIDs"]);*/
      $notificationContent = json_decode($_POST['msg']);
	 echo $notificationContent;
	 $dbHelper = new DB_service;
	 $res = $dbHelper->getAllDeviceTokens();
     print_r($res);

?>