<?php
	require 'databaseVars.php';
	function getDateByIpAddress($ip,$userAgent,$sql) 
	{
		global $conn;
		$stmt = $conn->prepare($sql); 
		$stmt->bind_param("ss", $ip,$userAgent);
		$stmt->execute();
		$timestamp = $stmt->get_result()->fetch_object()->date;
		
		#set time zone to our time
		date_default_timezone_set('Europe/London');
		
		#current date and time
		$currentDate = date('Y-m-d');
		$currentTime = date('H:i:s');
		
		#time and date from database time stamp
		$datetime = explode(" ",$timestamp);
		$dbTimeStampDate = $datetime[0];
		$dbTimeStampTime = $datetime[1];
		$timeStampDateTime = date('H:i:s', strtotime($dbTimeStampTime.'+3 minute'));

		$hasThreeMinutesPast = 0;
		
		if($currentDate > $dbTimeStampDate)
		{
			$hasThreeMinutesPast = 1;
		}
		elseif($currentTime >= $timeStampDateTime)
		{
			$hasThreeMinutesPast = 1;
		}
		return $hasThreeMinutesPast;
	}
	function addToAttempts($ip,$userAgent,$lockoutAttempts,$sql){
		global $conn;
		
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("ssi", $ip,$userAgent,$lockoutAttempts);
		$stmt->execute();
		$stmt->close();
	}
	function updateAttempts($ip,$userAgent,$lockoutAttempts,$sql){
		global $conn;
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('iss', $lockoutAttempts,$ip,$userAgent);
		$stmt->execute();
		$stmt->close();
	}
	function deleteRecord($ip,$userAgent,$sql){
		global $conn;
		
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("ss",$ip,$userAgent);
		$stmt->execute();
		$stmt->close();
	}
	function addLogRecord($ip,$userAgent,$loggedFrom,$isLoggedIn,$lockoutAttempts,$username){
		global $conn;
		$stmt = $conn->prepare("INSERT INTO log (ipAddress,userAgent,loggedFrom,loggedIn,lockoutAttempts,userName) VALUES (?,?,?,?,?,?)");
		$stmt->bind_param("ssssis",$ip,$userAgent,$loggedFrom,$isLoggedIn,$lockoutAttempts,$username);
		$stmt->execute();
		$stmt->close();
	}
	function checkLoginOrRegisterAttempts($ip,$useragent,$sqlLoginAttempts){
		global $conn;
		$stmt = $conn->prepare($sqlLoginAttempts); 
		$stmt->bind_param("ss", $ip,$useragent);
		$stmt->execute();
		$result = $stmt->get_result(); 
		$row = $result->fetch_assoc();
		if($row != null) {
			$stmt->close();
			return $row["lockoutAttempts"];
		}
		else {
			$stmt->close();
			return 0;
		} 
	}
	function checkUser($username){
		global $conn;
		
		$sql = "SELECT * FROM securetable WHERE userName=?"; // SQL with parameters
		$stmt = $conn->prepare($sql); 
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result(); 
		$row = $result->fetch_assoc();
		if($row != null) {
			$stmt->close();
			return 1;
		}
		else {
			$stmt->close();
			return 0;
		} 
	}
	function getPasswordDetails($username,$sql){
		global $conn;
		$stmt = $conn->prepare($sql); 
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result(); 
		$row = $result->fetch_assoc();
		if($row != null) {
			$stmt->close();
			return $row;
		}
		else {
			$stmt->close();
			return 0;
		} 
	}
	function registerUser($name,$salt,$saltedAndHashedPassword) {
	global $conn;
	$stmt = $conn->prepare("INSERT INTO securetable (userName, salt,hashedPassword) VALUES (?, ?, ?)");
	$stmt->bind_param("sss",$name,$salt, $saltedAndHashedPassword);
	$stmt->execute();
	$stmt->close();
  } 
?>