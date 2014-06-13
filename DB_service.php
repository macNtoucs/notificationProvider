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
    	if (count($dataArray) == 5 ) {
		 array_push($tokenArray, $dataArray[ 'deviceToken' ]); 
     		 return $tokenArray;
       }
    	
      foreach( $dataArray as &$row){
    		$filter = array('deviceToken' => $row ['deviceToken']);
    		$canPush = $this->DB->Select('devicesetting',$filter);		    		
		if ($canPush[ 'emergency' ] ==1)  array_push($tokenArray, $row[ 'deviceToken' ] );    		
    	}
    	 return $tokenArray;
    	
    }
                  

    function register($token , $OS, $studentID=""){
           $tokenArray = $this->DB->Select('deviceandstudent' ,array ('deviceToken' => $token));
              // print_r($tokenArray);
            if($tokenArray==1) {  //register
    	          $newMember = array('deviceToken' => $token, 'deviceType' => $OS , 'studentID' => $studentID);
		    $this->DB->Insert($newMember,'deviceandstudent');
                $newdeviceSetting = array ('deviceToken' => $token);
		    $this->DB->Insert($newdeviceSetting,'devicesetting');
		    $this-> getBadge($token);
		     return;
		}
           else{  //have registered
		   //  echo $token;
       	   $newMemberdata = array('deviceType' => $OS , 'studentID' => $studentID);
		    $this->DB->Update('deviceandstudent',$newMemberdata,array ('deviceToken' => $token));
		    $this->DB->UpdateTimestamp('deviceandstudent',array ('deviceToken' => $token));
		    $this-> getBadge($token);
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

	function transSIDstoTokens($sidsArray){
	  $tokenArray =  array(); 
        foreach ($sidsArray as $sid){
			
			$devAndSidData = $this -> DB -> Select('deviceandstudent' , array('studentID' => $sid));
			//print_r(count($devAndSidData));
		     if (count($devAndSidData) ==6 ) {
				array_push($tokenArray ,$devAndSidData[ 'deviceToken' ]);
					echo $sid.' -->  '. $devAndSidData[ 'deviceToken' ] .'</br>';			
				}
                 else if (count($devAndSidData) >= 2 ) {
				foreach ($devAndSidData as $_sid){
					array_push($tokenArray , $_sid[ 'deviceToken' ]);
					echo $sid.' -->  '. $_sid[ 'deviceToken' ] .'</br>';					
				}
			}
                 
                 else echo $sid.' -->  No any devices </br>';
		}
        
        return $tokenArray;
      }
    
     function getBadge($token){ //after this function, three cols will set to be 0.  
		$par = array( "deviceToken" => $token );
		$res = $this -> DB -> Select('badge' , $par);
		//print_r($res);
		$badgeArray = array('emergencyinfo' => (int)$res['emergency'] , 'stellar' => (string)$res['moodle'] , 'libraries' => (int)$res['library']);
		//print_r($badgeArray);
            $json = json_encode($badgeArray);
		$json = str_replace("\\","",$json);
		$json = str_replace('}"',"}",$json);
		echo  str_replace('"{',"{",$json);
           // echo $json;
		$this->DB->Update('badge',
                              array ("emergency" => 0 , "moodle" => "" ,"library" => 0),
                              $par);
	}
       
       function setPNSBadge($token, $badgeCnt){
		$par = array( "deviceToken" => $token );
		$this->DB->Update('badge',
                              array ("pns" => $badgeCnt),
                              $par);
        }
	 function getPNSBadge($token){
		$par = array( "deviceToken" => $token );
		$member = $this->DB->Select('badge',$par);
		return $member['pns'];
        }

	function saveNotification ($msg, $type, $fromCourse){
		    $newHis = array('content' => $msg, 'type' => $OS , 'fromcourse' => $fromCourse);
		    $this->DB->Insert($newHis,'notificationhistory');
		
		}

	function increaseCourseBadge($courseID, $deviceTokenArray){
		foreach ($deviceTokenArray as $deviceToken){
			$member = $this -> DB -> Select( 'badge' , array("deviceToken" => $deviceToken));
			$courseBadgeArray = json_decode( $member['moodle'],true );
			$chk = array_key_exists($courseID, $courseBadgeArray);

			if ($chk == false){ //new course badge, should be insert a new one.
				$courseBadgeArray [ $courseID ]= 1;
				}
			else{ //there has been a course, increase badge for this course
				++$courseBadgeArray[ $courseID ] ;
				}
			$newCourseBadgeJson = json_encode($courseBadgeArray);

			$this->DB->Update('badge',
                              array ("moodle" => $newCourseBadgeJson , 'pns' => ++$member['pns']),
                              array("deviceToken" => $deviceToken));
		}
	  }
	function increaseEmerBadge(){
		    $dataArray = $this->DB->Select('badge');
		     print_r($dataArray);
		    foreach($dataArray as $data){
				$this->DB->Update('badge',
                              array ("emergency" => ++$data['emergency'], 'pns' => ++$data['pns']),
                              array("deviceToken" => $data['deviceToken']));
			}
		}
}

php?>
