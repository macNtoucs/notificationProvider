<?php
    header("Content-Type: text/html; charset=utf-8");
	include 'DB_service.php';	
    var $dbHelper = new DB_service();


    var $studentIDsArray = json_decode($POST["studentIDs"]);
    var $courseID = json_decode($POST["courseID"]);
    var $alert = json_decode($POST["alert"]));
    var $badge = json_decode($POST["badge"]));
    var $content = json_decode($POST["content"]));
    var $sound = json_decode($POST["default"]));


