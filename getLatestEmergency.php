<?php
    include'DB_service.php';
    $dbHelper = new DB_service;
    $dbHelper-> getLatestEmergency();
?>
