<?php
    include'DB_service.php';
    $token  = $_POST[ 'deviceToken' ];
    $OS = $_POST[ 'OS' ];
    $studentID = $_POST['studentID'];
    //  $notiStatusNumber = $_POST['notiStatusNumber'];
    $dbHelper = new DB_service;
    $dbHelper-> register($token , $OS , $studentID);
	//$dbHelper-> register($token , $OS , $studentID, $notiStatusNumber); 
?>
