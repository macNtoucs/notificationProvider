<?php
header("Content-Type:text/html; charset=utf-8");
// MySQL Class v0.8
class MySQL {

	// Base variables
	var $lastError;					// Holds the last error
	var $lastQuery;					// Holds the last query
	var $result;						// Holds the MySQL query result
	var $records;						// Holds the total number of records returned
	var $affected;					// Holds the total number of records affected
	var $rawResults;				// Holds raw 'arrayed' results
	var $arrayedResult;			// Holds an array of the result

	var $hostname;	// MySQL Hostname
	var $username;	// MySQL Username
	var $password;	// MySQL Password
	var $database;	// MySQL Database

	var $databaseLink;		// Database Connection Link



	/* *******************
	 * Class Constructor *
	 * *******************/

	function MySQL($database, $username, $password, $hostname='localhost'){
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
		$this->hostname = $hostname;
        $this->Connect();
	}



	/* *******************
	 * Private Functions *
	 * *******************/

	// Connects class to database
	// $persistant (boolean) - Use persistant connection?
	private function Connect($persistant = false){
		if($this->databaseLink){
			mysql_close($this->databaseLink);
		}

		if($persistant){
			$this->databaseLink = mysql_pconnect($this->hostname, $this->username, $this->password);
		}else{
			$this->databaseLink = mysql_connect($this->hostname, $this->username, $this->password);
		}

		if(!$this->databaseLink){
   		$this->lastError = 'Could not connect to server: ' . mysql_error($this->databaseLink);
			return false;
		}

		if(!$this->UseDB()){
			$this->lastError = 'Could not connect to database: ' . mysql_error($this->databaseLink);
			return false;
		}
		return true;
	}


	// Select database to use
	private function UseDB(){
		if(!mysql_select_db($this->database, $this->databaseLink)){
			$this->lastError = 'Cannot select database: ' . mysql_error($this->databaseLink);
			return false;
		}else{
			return true;
		}
	}


	// Performs a 'mysql_real_escape_string' on the entire array/string



	/* ******************
	 * Public Functions *
	 * ******************/
	  function SecureData($data){
		if(is_array($data)){
			foreach($data as $key=>$val){
				if(!is_array($data[$key])){
					$data[$key] = mysql_real_escape_string($data[$key], $this->databaseLink);
				}
			}
		}else{
			$data = mysql_real_escape_string($data, $this->databaseLink);
		}
		return $data;
	}


	// Executes MySQL query
	function ExecuteSQL($query){
	  //echo $query;
		$this->lastQuery 	= $query;
		mysql_query("SET NAMES 'UTF8'");
		if($this->result 	= mysql_query($query, $this->databaseLink)){
			$this->records 	= @mysql_num_rows($this->result);
			$this->affected	= @mysql_affected_rows($this->databaseLink);

			if($this->records > 0){
				$this->ArrayResults();
				return $this->arrayedResult;
			}else{
				return true;
			}

		}else{
			$this->lastError = mysql_error($this->databaseLink);
			return false;
		}
	}


	// Adds a record to the database based on the array key names
	function Insert($vars, $table, $exclude = ''){

		// Catch Exclusions
		if($exclude == ''){
			$exclude = array();
		}

		array_push($exclude, 'MAX_FILE_SIZE'); // Automatically exclude this one

		// Prepare Variables
		$vars = $this->SecureData($vars);

		$query = "INSERT INTO {$table} SET ";
		foreach($vars as $key=>$value){
			if(in_array($key, $exclude)){
				continue;
			}
			//$query .= '`' . $key . '` = "' . $value . '", ';
			$query .= "`{$key}` = '{$value}', ";
		}

		$query = substr($query, 0, -2);

		return $this->ExecuteSQL($query);
	}

	// Deletes a record from the database
	function Delete($table, $where='', $limit='', $like=false){
		$query = "DELETE FROM `{$table}` WHERE ";
		if(is_array($where) && $where != ''){
			// Prepare Variables
			$where = $this->SecureData($where);

			foreach($where as $key=>$value){
				if($like){
					//$query .= '`' . $key . '` LIKE "%' . $value . '%" AND ';
					$query .= "`{$key}` LIKE '%{$value}%' AND ";
				}else{
					//$query .= '`' . $key . '` = "' . $value . '" AND ';
					$query .= "`{$key}` = '{$value}' AND ";
				}
			}

			$query = substr($query, 0, -5);
		}

		if($limit != ''){
			$query .= ' LIMIT ' . $limit;
		}
      
		return $this->ExecuteSQL($query);
	}


	// Gets a single row from $from where $where is true
	function Select($from, $where='', $orderBy='', $limit='', $like=false, $operand='AND'){
		// Catch Exceptions
		if(trim($from) == ''){
			return false;
		}

		$query = "SELECT * FROM {$from} WHERE ";

		if(is_array($where) && $where != ''){
			// Prepare Variables
			$where = $this->SecureData($where);

			foreach($where as $key=>$value){
				if($like){
					//$query .= '`' . $key . '` LIKE "%' . $value . '%" ' . $operand . ' ';
					$query .= "`{$key}` LIKE '%{$value}%' {$operand} ";
				}else if (strpos ($value, '>')){
					//$query .= '`' . $key . '` = "' . $value . '" ' . $operand . ' ';
					$value = substr($value,2,strlen($value)-2);
					$query .= "`{$key}` <>'{$value}' {$operand} ";
				}else{
					$query .= "`{$key}` = '{$value}' {$operand} ";
				}
			}

			$query = substr($query, 0, -(strlen($operand)+2));

		}else{
			$query = substr($query, 0, -7);
		}

		if($orderBy != ''){
			$query .= ' ORDER BY ' . $orderBy;
		}

		if($limit != ''){
			$query .= ' LIMIT ' . $limit;
		}
        //print_r($query);
		return $this->ExecuteSQL($query);

	}

	// Updates a record in the database based on WHERE
	function Update($table, $set, $where, $exclude = ''){
		// Catch Exceptions
		if(trim($table) == '' || !is_array($set) || !is_array($where)){
			return false;
		}
		if($exclude == ''){
			$exclude = array();
		}

		array_push($exclude, 'MAX_FILE_SIZE'); // Automatically exclude this one

		$set 		= $this->SecureData($set);
		$where 	= $this->SecureData($where);

		// SET

		$query = "UPDATE {$table} SET ";

		foreach($set as $key=>$value){
			if(in_array($key, $exclude)){
				continue;
			}
			$query .= "`{$key}` = '{$value}', ";
		}

		$query = substr($query, 0, -2);

		// WHERE

		$query .= ' WHERE ';

		foreach($where as $key=>$value){
			$query .= "`{$key}` = '{$value}' AND ";
		}
       
		$query = substr($query, 0, -5);
            //echo $query;
		return $this->ExecuteSQL($query);
	}

	// 'Arrays' a single result
	function ArrayResult(){
		$this->arrayedResult = mysql_fetch_assoc($this->result) or die (mysql_error($this->databaseLink));
		return $this->arrayedResult;
	}

	// 'Arrays' multiple result
	function ArrayResults(){

		if($this->records == 1){
			return $this->ArrayResult();
		}

		$this->arrayedResult = array();
		while ($data = mysql_fetch_assoc($this->result)){
			$this->arrayedResult[] = $data;
		}
		return $this->arrayedResult;
	}

	// 'Arrays' multiple results with a key
	function ArrayResultsWithKey($key='id'){
		if(isset($this->arrayedResult)){
			unset($this->arrayedResult);
		}
		$this->arrayedResult = array();
		while($row = mysql_fetch_assoc($this->result)){
			foreach($row as $theKey => $theValue){
				$this->arrayedResult[$row[$key]][$theKey] = $theValue;
			}
		}
		return $this->arrayedResult;
	}
}

?>
