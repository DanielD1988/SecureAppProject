<?php
  require 'dbFunctions.php';
  require 'sanitze.php';
  $sanName = filterString($_POST['user']);
  
  $ip = $_SERVER['REMOTE_ADDR'];
  if(isset($_SERVER['HTTP_USER_AGENT']))
  {
	$sanUserAgent = filterString($_SERVER['HTTP_USER_AGENT']);
  }
  else
  {
	$sanUserAgent = "No user agent found";
  }
 
  $sqlInsertIntoRegisterAttempts = "INSERT INTO registerattempt (ipAddress,userAgent,lockoutAttempts) VALUES (?,?,?)";
  $sqlUpdateLoginAttempts = "UPDATE registerattempt SET lockoutAttempts=? WHERE ipAddress=? AND userAgent=?";
  $sqlRegisterAttempts = "SELECT lockoutAttempts FROM registerattempt WHERE ipAddress=? AND userAgent=?";
  $sqlInsertIntoLog = "INSERT INTO log (ipAddress,userAgent,loggedFrom,loggedIn,lockoutAttempts,userName,id) VALUES (?,?,?,?,?,?,?)";
  $sqlDelete = "DELETE FROM registerattempt WHERE ipAddress=? AND userAgent=?";
  $sqlGetDate = "SELECT date FROM registerattempt WHERE ipAddress=? AND userAgent=?";
  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbName);
  if ($conn->connect_error) 
  {
		
  }
  else
  {
	  
	$lockOutNumber = checkLoginOrRegisterAttempts($ip,$sanUserAgent,$sqlRegisterAttempts);
	if($lockOutNumber == 5)
	{
		$canLogin = getDateByIpAddress($ip,$sanUserAgent,$sqlGetDate);
		if($canLogin == 1)
		{
			deleteRecord($ip,$sanUserAgent,$sqlDelete);
			$lockOutNumber = 0;
		}
		else
		{
			echo "You haved been locked out from entering a userName for three minutes";
			$conn->close();
			header('refresh:5; register.html');
			die();
		}
	}  
	  
	$result = checkUser($sanName);
	if($result == 1)
	{
		if($lockOutNumber == 0)
		{
			addToAttempts($ip,$sanUserAgent,$lockOutNumber+1,$sqlInsertIntoRegisterAttempts);
		}
		else
		{
			updateAttempts($ip,$sanUserAgent,$lockOutNumber+1,$sqlUpdateLoginAttempts);
		}
		addLogRecord($ip,$sanUserAgent,"Register Form","Failed",$lockOutNumber,$sanName);
		echo "This username password combination could not be sent";
		$conn->close();
		header('refresh:3; register.html');
		die();
	}
	else
	{
		addLogRecord($ip,$sanUserAgent,"Register Form","Succeeded",$lockOutNumber,$sanName);
		deleteRecord($ip,$sanUserAgent,$sqlDelete);
		$lockOutNumber = 0;
		$salt = bin2hex(openssl_random_pseudo_bytes(32));#https://stackoverflow.com/questions/4356289/php-random-string-generator
		$password = $_POST['pass'];
		$saltPlusPassword = $salt . $password;
		$saltedAndHashedPassword =  md5($saltPlusPassword);
		$sql = "SELECT MAX(id) FROM securetable";
		registerUser($sanName,$salt,$saltedAndHashedPassword);
		echo "Registration Complete";
		$conn->close();
		header('refresh:3; login.html');
		die();
	}
  }
?>