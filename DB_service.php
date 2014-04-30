<?php
    header("Content-Type: text/html; charset=utf-8");
	include 'sql.php';
	class DB_Service{
		 function DB_Service(){
	            $this->DB = new MySQL('NTOU_NotificationProvider', 'root','', 'localhost');
		}

    function getAllDeviceTokens(){
    	$dataArray = $this->DB->Select('deviceAndStudent');
        return $dataArray;

    }







	function getRightPoint ($fbID){
		$parameterArray = array ('fb_ID' => $fbID);
		$dataArray = array();
		$dataArray = $this->DB->Select('user',$parameterArray);
		return $dataArray['right_point'];
	}
	function deleteFromCurrentCourse($postID){
		     $parameterArray = array ('postID' => $postID);
			 $this->DB->Delete('current_posts',$parameterArray);
	}

	function setCourseState ($postID,$nextState)	{
		     $parameterArray = array ('PostID' => $postID);
			 $dataArray = array();
			 $dataArray = $this->DB->Select('current_posts',$parameterArray);
			  $newState = array ('state' => $nextState);
			 //print_r($newState);
		     $this -> DB -> Update('current_posts',$newState,$parameterArray);
	}

	function utf8_str_split($str, $split_len = 1)
	{
			if (!preg_match('/^[0-9]+$/', $split_len) || $split_len < 1)
				return FALSE;

			$len = mb_strlen($str, 'UTF-8');
			if ($len <= $split_len)
				return array($str);

			preg_match_all('/.{'.$split_len.'}|[^\x00]{1,'.$split_len.'}$/us', $str, $ar);

			return $ar[0];
	}	

	function getHistory($fbID){
			$parameterArray = array ('fb_ID' => $fbID);
			$dataArray = array();
			$dataArray = $this->DB->Select('current_posts',$parameterArray);
			//echo json_encode($dataArray,JSON_UNESCAPED_UNICODE);
			return json_encode($dataArray,JSON_UNESCAPED_UNICODE);
	}

        function getAllCourse(){
		     $dataArray = array();
			 $dataArray = $this->DB->Select('course_info');
			 //print_r ($dataArray);
			// echo json_encode($dataArray,JSON_UNESCAPED_UNICODE);
			return json_encode($dataArray,JSON_UNESCAPED_UNICODE);
		}
      
	    function getCourseRate($courseNum){
		     $parameterArray = array ('courseNum' => $courseNum);
			 $dataArray = array();
			 $dataArray = $this->DB->Select('course_info',$parameterArray);
			 return  $dataArray["rating"];
		}

		function getCourseTime($courseNum){
			 $parameterArray = array ('courseNum' => $courseNum);
			 $dataArray = array();
			 $dataArray = $this->DB->Select('course_info',$parameterArray);
			 return  $dataArray["course_time"];
			}
		function getCourseTeacher($courseNum){
			 $parameterArray = array ('courseNum' => $courseNum);
			 $dataArray = array();
			 $dataArray = $this->DB->Select('course_info',$parameterArray);
			 //print_r ($dataArray);
			 return  $dataArray["teacher"];
			}

		private function fixCurrentResultArray(&$dataArray){
				$sendCourseName =  $this -> getCourseName($dataArray["send_course_ID"]);
				$recieveCourseName =  $this -> getCourseName($dataArray["recieve_course_ID"]);
				$dataArray["sendCourseName"] = $sendCourseName;
				$dataArray["sendCourseRate"] = $this -> getCourseRate($dataArray["send_course_ID"]);
				$dataArray["sendCourseTeacher"] = $this -> getCourseTeacher($dataArray["send_course_ID"]);
				$dataArray["sendCourseTime"] = $this -> getCourseTime($dataArray["send_course_ID"]);
				$dataArray["sendCourseNum"] = $dataArray["send_course_ID"];
				$dataArray["send_course_ID"] = $this -> getCourseIDUseCN ($dataArray["sendCourseNum"]);
				if ($recieveCourseName){
					$dataArray["recieveCourseName"] = $recieveCourseName;
					$dataArray["recieveCourseRate"] = $this -> getCourseRate($dataArray["recieve_course_ID"]);
					$dataArray["recieveCourseTeacher"] = $this -> getCourseTeacher($dataArray["recieve_course_ID"]);
					$dataArray["recieveCourseTime"] = $this -> getCourseTime($dataArray["recieve_course_ID"]);
					$dataArray["recieveCourseNum"] = $dataArray["recieve_course_ID"];
					$dataArray["recieve_course_ID"] = $this -> getCourseIDUseCN ($dataArray["recieveCourseNum"]);
					}	
		}

		function getCurrentCourses($page, $count,$type){
		   $dataArray = array();
		   $from = $count*($page-1);
		   $to = $page*$count;
		   $conditionArray= array();
		   if ($type == 'exchange') $conditionArray = array ('recieve_course_ID' => '<>none');
		   else if ($type == 'transaction') $conditionArray =  array ('recieve_course_ID' => 'none');
		  $dataArray = $this->DB->Select('current_posts',$conditionArray);
		  $dataArray = array_slice($dataArray,$from,$count);

		  foreach ($dataArray as &$rowArray){
			if( !is_array($rowArray)) {
			  		$this -> fixCurrentResultArray($dataArray);
				break;
			}
			$this -> fixCurrentResultArray($rowArray);
		 }

	   	 // echo json_encode($dataArray,JSON_UNESCAPED_UNICODE);
		  return json_encode($dataArray,JSON_UNESCAPED_UNICODE);
		}
		private function postInTransactionArea($fbID, $want_send_courseID){
			$newCourse = array('fb_ID' => $fbID, 'send_course_ID' => $want_send_courseID , 'state' => 'ready');
		    $this->DB->Insert($newCourse,'current_posts');
		}

		private function bestMatchCheck($want_send_courseID, $want_recieve_courseID){
		    //A換B 需要符合B換A
			$conditionArray = array('send_course_ID' => $want_recieve_courseID, 'recieve_course_ID' =>$want_send_courseID );
			$matchCourses = $this -> DB -> Select('current_posts',$conditionArray);
			//print_r($matchCourses);
			if ($matchCourses==1) return 'non-match' ;//找不到
			else if(is_array(reset($matchCourses))){
				$matchCourses[0]['send_course_ID'] = $this -> getCourseIDUseCN($want_recieve_courseID);
				$matchCourses[0]['recieve_course_ID'] = $this -> getCourseIDUseCN($want_send_courseID);
				return $matchCourses[0];
			}
			else {
				$matchCourses['send_course_ID'] = $this -> getCourseIDUseCN($want_recieve_courseID);
				$matchCourses['recieve_course_ID'] = $this -> getCourseIDUseCN($want_send_courseID);
				return $matchCourses;
			}
		}

		private function postInExchangeArea($fbID, $want_send_courseID, $want_recieve_courseID){
		   $result = $this -> bestMatchCheck($want_send_courseID, $want_recieve_courseID);
		   if ($result == 'non-match'){
				$newCourse = array('fb_ID' => $fbID, 'send_course_ID' => $want_send_courseID , 'recieve_course_ID' =>$want_recieve_courseID, 'state' => 'ready');
				$this->DB->Insert($newCourse,'current_posts');
			}
			 return $result;
		}

	    function postACourse($fbID, $want_send_courseID, $want_recieve_courseID='none'){
		   if ($want_recieve_courseID == 'none') {
				$this -> postInTransactionArea($fbID, $want_send_courseID); 
				return;
				}
		   else $result = $this -> postInExchangeArea($fbID, $want_send_courseID, $want_recieve_courseID);

		   if ($result != 'non-match')  return json_encode($result,JSON_UNESCAPED_UNICODE);
		   else return 'non-match';
		 }

		function Login($userName, $fbID){
			 $parameterArray = array ('fb_ID' => $fbID);
			 $dataArray = array();
			 $dataArray = $this->DB->Select('user',$parameterArray);
		if ($dataArray==1) { 
		  //新增使用者
		    $newUser= array('user_name' => $userName, 'fb_ID' => $fbID);
			$this->DB->Insert($newUser, 'user');
			}
		}

		function setCourseRate($fbID,$courseNum,$newValue){
		     //set this fb_user can't rate the course again
			 $parameterArray = array ('fb_ID' => $fbID);
			 $dataArray = array();
			 $dataArray = $this->DB->Select('user',$parameterArray);
			 if (strstr($dataArray['ratedCourses'],$courseNum)) 
				return "已經評比過該課程";
			 $newRatedCourses = array ('ratedCourses' => $dataArray['ratedCourses']. "," .$courseNum);
		     $this -> DB -> Update('user',$newRatedCourses,$parameterArray);

			 //count the rate avg and set the rate
		     $parameterArray = array ('courseNum' => $courseNum);
			 $dataArray = array();
			 $dataArray = $this->DB->Select('course_info',$parameterArray);
			 $newValue = ($dataArray["rating"]+$newValue)/($dataArray["rateCount"]+1);

			 //prepare var
			 $newRatingArray = array('rating' => $newValue, 'rateCount' => $dataArray["rateCount"]+1);
			 $conditionArray = array('courseNum' => $courseNum);
			 $this -> DB -> Update('course_info',$newRatingArray,$conditionArray);

		}

		function getCourseIDUseCN($courseNum){ //use courseNum to get the courseID
			 $parameterArray = array ('courseNum' => $courseNum);
			 $dataArray = array();
			 $dataArray = $this->DB->Select('course_info',$parameterArray);
			return $dataArray['course_ID'];
		}
		function getCourseID($courseName){
		     $parameterArray = array ('course_name' => $courseName);
			 $dataArray = array();
			 $dataArray = $this->DB->Select('course_info',$parameterArray);
			// print_r($dataArray);
			 $mutiDataArray = array();
			if (! is_array(reset($dataArray)) ) array_push($mutiDataArray,  array( 'course_ID' => $dataArray['course_ID'], 'courseNum' => $dataArray['courseNum'] , 'teacher' => $dataArray['teacher'], 'courseTime' => $dataArray['course_time']));
			else {
				foreach($dataArray as $row)  array_push($mutiDataArray, array( 'course_ID' => $row['course_ID'], 'courseNum' => $row['courseNum'] , 'teacher' => $row['teacher'], 'courseTime' => $row['course_time']));
			 }

			 return  json_encode($mutiDataArray,JSON_UNESCAPED_UNICODE);
		}

		function getCourseName($courseNum){
		     $parameterArray = array ('courseNum' => $courseNum);
			 $dataArray = array();
			 $dataArray = $this->DB->Select('course_info',$parameterArray); 
			 return $dataArray ["course_name"];
		}

		function getPersonalURLInEC ($postID){
			 $parameterArray = array ('PostID' => $postID);
			$dataArray = array();
			$dataArray = $this->DB->Select('current_posts',$parameterArray);
		    $post_person = $dataArray['fb_ID'];
			return 'http://www.facebook.com/profile.php?id='.$post_person;
		}

		function getPersonalURL ($postID, $want_person){
		    $parameterArray = array ('PostID' => $postID);
			$dataArray = array();
			$dataArray = $this->DB->Select('current_posts',$parameterArray);
		    $post_person = $dataArray['fb_ID'];
			if ($want_person == $post_person) return '參數不可相同';
		    // 查看post個人網址的人 權力點數需要-1
		    $parameterArray = array ('fb_ID' => $want_person);
			$dataArray = array();
			$dataArray = $this->DB->Select('user',$parameterArray);

			if ($dataArray['right_point']-1 < 0) return '權力點數不足';
			$decreasePointArray = array ('right_point' => $dataArray['right_point']-1);
			$decreaseConditionArray = array ('fb_ID' => $want_person);
			$this -> DB -> Update('user',$decreasePointArray,$decreaseConditionArray);

			$parameterArray = array ('fb_ID' => $post_person);
			$dataArray = array();
			$dataArray = $this->DB->Select('user',$parameterArray);
			$increasePointArray = array ('right_point' => $dataArray['right_point']+1);
			$this -> DB -> Update('user',$increasePointArray,$parameterArray);

			$dataArray = array();
			$dataArray = $this->DB->Select('user',$parameterArray);


			//echo 'http://www.facebook.com/profile.php?id='.$dataArray['fb_ID'];
			return 'http://www.facebook.com/profile.php?id='.$dataArray['fb_ID'];
		}

		function getRatedCourses($fbID){
			$parameterArray = array ('fb_ID' => $fbID);
			$dataArray = array();
			$dataArray = $this->DB->Select('user',$parameterArray);
			$ratedCourses_string = $dataArray["ratedCourses"];
			//print_r ($ratedCourses_string);
			$ratedCoursesArray = array();
			$token = strtok($ratedCourses_string,',');
			array_push($ratedCoursesArray,$token);
			while ( $token = strtok(','))    array_push($ratedCoursesArray,$token);
			//print_r ($ratedCoursesArray);
			return json_encode($ratedCoursesArray,JSON_UNESCAPED_UNICODE);
		}

		private function is_chinese($str) {
			return preg_match("/\p{Han}+/u", $str);
		}

		private function setupResultFromFuzzy($matchPIDArray){
		//print_r($matchPIDArray);
		$matchArray = array();
		  foreach ($matchPIDArray  as $PostID){
			$conditionArray = array ('PostID' => $PostID);
			$dataArray = $this -> DB -> Select('current_posts' , $conditionArray);
			//print_r ($dataArray);
			$this -> fixCurrentResultArray($dataArray);
			array_push($matchArray,$dataArray);
		 }
		  //print_r($matchArray);
		  return $matchArray;
		}

		private function singleWordFuzzySearch($fuzzySearch,$table ='course_info',$type = 'none'){
			if ($table != 'course_info' ){
					 if ($type == 'exchange') $conditionArray = array ('recieve_course_ID' => '<>none');
					else if ($type == 'transaction') $conditionArray =  array ('recieve_course_ID' => 'none');
			        $resultArray = array();
					$dataArray = $this -> DB -> Select($table,$conditionArray);
					if (is_array( reset($dataArray))){
						 foreach($dataArray as $row){
							$sendCourseName =  $this -> getCourseName( $row[ 'send_course_ID' ]);
							$recieveCourseName =  $this -> getCourseName( $row[ 'recieve_course_ID' ]);
							if (strstr($sendCourseName,$fuzzySearch) || strstr($recieveCourseName,$fuzzySearch)) {
								 array_push($resultArray,$row['PostID']);
								}
						}
						return $resultArray;
					}
					else {
							$sendCourseName =  $this -> getCourseName( $dataArray[ 'send_course_ID' ]);
							$recieveCourseName =  $this -> getCourseName( $dataArray[ 'recieve_course_ID' ]);
							if (strstr($sendCourseName,$fuzzySearch) || strstr($recieveCourseName,$fuzzySearch)) {
								 array_push($resultArray,$row['PostID']);
								}
							return $resultArray;
					}
			}

			$conditionArray = array ('course_name' => $fuzzySearch);
			$resultArray = $this -> DB -> Select($table,$conditionArray,'','',true);

			return $resultArray;

			}

		private function timeSearch($timeString){
		$matchArray = array();
		 $currentPost=  $this -> DB -> Select('current_posts',array('recieve_course_ID' => 'none'));
            if (is_array(reset($currentPost))){
				foreach($currentPost as $row){
					if ( strstr( $this->getCourseTime( $row['send_course_ID']) , $timeString) ) array_push($matchArray ,$row['PostID'] );
					else if ( strstr( $this->getCourseTime( $row['recieve_course_ID']) , $timeString) ) array_push($matchArray ,$row['PostID'] );
				}
			}
            else{
				if ( strstr( $this->getCourseTime( $currentPost['send_course_ID']) , $timeString) ) array_push($matchArray ,$currentPost['PostID'] );
					else if ( strstr( $this->getCourseTime( $currentPost['recieve_course_ID']) , $timeString) ) array_push($matchArray ,$currentPost['PostID'] );
			}			
            //print_r($matchArray);
			$resultArray = $this ->setupResultFromFuzzy($matchArray);
			//print_r ($resultArray);
			return $resultArray;
		}



		function fuzzySearch($fuzzyString , $place = 'course_info' , $type = 'none'){
		  $dataArray = array();  
		  $resultArray = array();

		  $dataArray = $this->DB->Select($place);

		  if (is_numeric($fuzzyString[0]) && mb_strlen($fuzzyString, 'utf-8') >= 3) {
			 $dataArray = $this -> timeSearch($fuzzyString);
			 $resultArray = array();
			 //print_r ($dataArray);
			 return  json_encode($dataArray,JSON_UNESCAPED_UNICODE);
		  }


		  if (mb_strlen($fuzzyString, 'utf-8') == 1 && $type != 'none') {
			 $dataArray =  $this -> singleWordFuzzySearch($fuzzyString,'current_posts',$type);
			$dataArray = $this -> setupResultFromFuzzy($dataArray);
		     return  json_encode($dataArray,JSON_UNESCAPED_UNICODE);
		  }


		  if (mb_strlen($fuzzyString, 'utf-8') == 1) {
		  $dataArray =  $this -> singleWordFuzzySearch($fuzzyString);
		  if (is_array( reset($dataArray)) ) {
			foreach  ($dataArray as $row){
			       // print_r($row['course_name']);
					array_push($resultArray, $row['course_name']);
					}
				//$resultArray = $this -> setupResultFromFuzzy($resultArray);
				$resultArray = array_unique($resultArray);
				return  json_encode($resultArray,JSON_UNESCAPED_UNICODE);
		  }

			array_push($resultArray,$dataArray['course_name']);
			return  json_encode($resultArray,JSON_UNESCAPED_UNICODE);
		  }

		  if ($place == 'course_info'){
			  foreach  ($dataArray as $row){
				$distance= $this->compareWithWord($row['course_name'],$fuzzyString);
				if (  $distance <= abs(mb_strlen($fuzzyString, 'utf-8') - mb_strlen($row['course_name'], 'utf-8') ) )
					array_push($resultArray, $row['course_name']);
				}
			//print_r($resultArray);
			$resultArray = array_unique($resultArray);
			return json_encode($resultArray,JSON_UNESCAPED_UNICODE);
		  }
		  else if ($place == 'current_posts' && $type == 'exchange'){
				 foreach  ($dataArray as $row){
						$sendCourseName =  $this -> getCourseName( $row[ 'send_course_ID' ]);
						$recieveCourseName =  $this -> getCourseName( $row[ 'recieve_course_ID' ]);
						$distance= $this->compareWithWord($sendCourseName,$fuzzyString) ; // sendCourseName distance
						if (  $distance <= abs(mb_strlen($fuzzyString, 'utf-8') - mb_strlen($sendCourseName, 'utf-8') ) )
							array_push($resultArray,$row[ 'PostID' ]);

						if ($recieveCourseName=="") continue;
						$distance= $this->compareWithWord($recieveCourseName,$fuzzyString); // recieveCourseName distance
						if (  $distance <= abs(mb_strlen($fuzzyString, 'utf-8') - mb_strlen($recieveCourseName, 'utf-8') ) )
							array_push($resultArray,$row[ 'PostID' ]);
						}
					$resultArray = $this -> setupResultFromFuzzy($resultArray);
					//echo  json_encode($resultArray,JSON_UNESCAPED_UNICODE);
					return json_encode($resultArray,JSON_UNESCAPED_UNICODE);
		  }

		  else if ($place == 'current_posts' && $type == 'transaction'){
					 foreach  ($dataArray as $row){
						$recieveCourseName =  $this -> getCourseName( $row[ 'recieve_course_ID' ]);
						if ($recieveCourseName!="") continue;
						$sendCourseName =  $this -> getCourseName( $row[ 'send_course_ID' ]);
						$distance= $this->compareWithWord($sendCourseName,$fuzzyString) ; // sendCourseName distance
						if (  $distance <= abs(mb_strlen($fuzzyString, 'utf-8') - mb_strlen($sendCourseName, 'utf-8') ))
							array_push($resultArray,$row[ 'PostID' ]);
						}

					$resultArray = $this -> setupResultFromFuzzy($resultArray);
					//echo  json_encode($resultArray,JSON_UNESCAPED_UNICODE);
					return json_encode($resultArray,JSON_UNESCAPED_UNICODE);

		  }

		}

		private function matchOnlyACH($stringA_arr,$stringB_arr , $stringA_len,$stringB_len){
			$check =0;
			for ($i = 0 ; $i < $stringA_len ; ++$i ){
					for ($j = 0 ; $j < $stringB_len ; ++$j ){
				if ($stringA_arr[$i] == $stringB_arr[$j] ) $check++;
			}
			}
			return $check == 1 ? true:false;
		}
		private function compareWithWord($stringA,$stringB){
			//prepare var
			$stringA_len = mb_strlen($stringA, 'utf-8');
			$stringB_len = mb_strlen($stringB, 'utf-8');
			$stringA_arr  = $this-> utf8_str_split ($stringA);
			$stringB_arr  = $this-> utf8_str_split ($stringB);
          /* print_r ($stringA_arr);
		   echo "</br>";
		   print_r ($stringB_arr);*/
		   // if (!$this -> is_chinese($stringB)) return;

			$distance_table = array();
		    //setup distance table
		    //start to count  Levenshtein_Distance
			if( $stringA_len>0 && $stringB_len>0 ) {
			    for ( $k = 0; $k < $stringA_len; $k++)  $distance_table[$k] = $k;
                for ( $k = 0; $k < $stringB_len; $k++ )  $distance_table[ $k * $stringA_len ] = $k;
        
				for ( $i = 1; $i < $stringA_len; $i++ )
					for ( $j = 1; $j < $stringB_len; $j++ ) {
                        if( $stringA_arr [ $i ]== $stringB_arr [  $j  ] )  {
						$cost = 0;
						}
						else {
						$cost = 1;
                        }
                         $distance_table[ $j * $stringA_len + $i ] =  $this->smallest(
						                           $distance_table[ ($j - 1) * $stringA_len + $i ] + 1,
												   $distance_table [ $j * $stringA_len + $i - 1 ] +  1,
												   $distance_table[ ($j - 1) * $stringA_len + $i -1 ] + $cost 
													);
	            }
	  /*  print_r($distance_table);
	    echo "</br>";
		echo $stringA.":".$stringA_len;
		echo "</br>";
		echo $stringB .":".$stringB_len;
		echo "</br>";
		echo "</br>";
		$distance = $distance_table[ $stringA_len * $stringB_len -1 ];
		 echo "dis : " . $distance;
		echo "</br>";*/
		$distance = $distance_table[ $stringA_len * $stringB_len -1 ];
		if($this -> matchOnlyACH($stringA_arr,$stringB_arr , $stringA_len,$stringB_len) ) ++$distance;
		/* echo "dis' : " . $distance;
		echo "</br>-----------------</br>";*/
		 return $distance;
		}
		return 0;
		}


		private function smallest($a,$b,$c){
			  $min = $a;
			  if ( $b < $min )
					$min = $b;
              if( $c < $min )
				$min = $c;
			return $min;
		}

	}

?>