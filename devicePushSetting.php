<?php
    include'DB_service.php';
    $token  = $_POST[ 'deviceToken' ];
    $moodle = $_POST[ 'moodle' ];
    $library = $_POST[ 'library' ];
     $emergency = $_POST[ 'emergency' ];


    $dbHelper = new DB_service;
    $dbHelper-> devicePushSettingAdjuster($token , $moodle , $library , $emergency); 
?>
