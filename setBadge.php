<?php
	 include 'DB_service.php';
     include_once "severAPI.php";
     /***prepare the request parameter***/
     //var $studentIDsArray = json_decode($POST["studentIDs"]);
      $token = $_GET['token_SET']; 
	$newBadgeVal = $_GET['newBadgeVal'];
 
	 $dbHelper = new DB_service;
	 $dbHelper->setPNSBadge($token , $newBadgeVal);
      
 ?>
