<?php
	 include 'DB_service.php';
     include_once "severAPI.php";
     /***prepare the request parameter***/
     //var $studentIDsArray = json_decode($POST["studentIDs"]);
      $token = $_POST['token_GET'];
	
	 $dbHelper = new DB_service;
	 $dbHelper->getBadge($token);

 ?>
