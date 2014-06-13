<?php
	 include 'DB_service.php';
     include_once "severAPI.php";
     /***prepare the request parameter***/
     //var $studentIDsArray = json_decode($POST["studentIDs"]);
      $token = $_POST['token_SET'];
	$newBadgeVal = $_POST['newBadgeVal'];
 
	 $dbHelper = new DB_service;
	 $dbHelper->setPNSBadge($token , $newBadgeVal);
      
 ?>
