<?php
     header("Content-Type: text/html; charset=utf-8");
	include 'sql.php';
	class DB_Service{
		 function DB_Service(){
	            $this->DB = new MySQL('ntou_notificationprovider', 'root','macntoucs', 'localhost');
		}

    function getAllDeviceTokens(){
	$dataArray = array();
    	$dataArray = $this->DB->Select('deviceandstudent');
    
    	$tokenArray =  array();
    	foreach( $dataArray as &$row){
           array_push($tokenArray, $row[ 'deviceToken' ] );
    	}
    	 return $tokenArray;
    }
   function getAllEmerDeviceTokens(){
    	$dataArray = $this->DB->Select('deviceandstudent');

    	$tokenArray =  array();
    	
    	foreach( $dataArray as &$row){
    		$filter = array('deviceToken' => $row ['deviceToken']);
    		$canPush = $this->DB->Select('devicesetting',$filter);
    		if ($canPush[ 'emergency' ] ==1)  array_push($tokenArray, $row[ 'deviceToken' ] );
    		else continue;
    	}
    	 return $tokenArray;
    	
    }
                  

    function register($token , $OS, $studentID=""){
           $tokenArray = $this->DB->Select('deviceandstudent' ,array ('deviceToken' => $token));
              // print_r($tokenArray);
            if($tokenArray==1) {
    	          $newMember = array('deviceToken' => $token, 'deviceType' => $OS , 'studentID' => $studentID);
		    $this->DB->Insert($newMember,'deviceandstudent');
                $newdeviceSetting = array ('deviceToken' => $token);
		    $this->DB->Insert($newdeviceSetting,'devicesetting');
		     return;
		}
           else{
       	    $newMemberdata = array('deviceType' => $OS , 'studentID' => $studentID);
		    $this->DB->Update('deviceandstudent',$newMemberdata,array ('deviceToken' => $token));
		    $this->DB->UpdateTimestamp('deviceandstudent',array ('deviceToken' => $token));
		     return;
                }
            
    }

    function devicePushSettingAdjuster ($token , $moodle , $library , $emergency){
	$adjustTarget = array ('deviceToken' => $token);
        $newSetting = array ('moodle' => $moodle, 'library' => $library , 'emergency' => $emergency);
        $this -> DB -> Update('devicesetting' , $newSetting , $adjustTarget);
         
     }


     function userValidator ($acc , $pwd){
	$par = array("account" => $acc , 'password' => $pwd);
      $res =  $this -> DB -> Select('provider',$par);
        if ($res ==1 ) return 'false';
          else return 'true';
      }
}

php?>
