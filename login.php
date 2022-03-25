<?php
	require 'dbFunctions.php';
	require 'sanitze.php';
	 
	date_default_timezone_set('Europe/London');
	if(!isset($_POST['user']) && !isset($_POST['pass']))
	{
		header('refresh:0; login.html');
		die();
	}
	$ip = $_SERVER['REMOTE_ADDR'];
	if(isset($_SERVER['HTTP_USER_AGENT']))
	{
		$sanUserAgent = filterString($_SERVER['HTTP_USER_AGENT']);
	}
	else
	{
		$sanUserAgent = "No user agent found";
	}
	
	$sanName = filterString($_POST['user']);
	$password = $_POST['pass'];
	$sqlInsertIntoLoginAttempts = "INSERT INTO loginattempt (ipAddress,userAgent,lockoutAttempts) VALUES (?, ?, ?)";
	$sqlUpdateLoginAttempts = "UPDATE loginattempt SET lockoutAttempts=? WHERE ipAddress=? AND userAgent=?";
	$sqlSelectDate = "SELECT date FROM loginattempt WHERE ipAddress=? AND userAgent=?";
	$sqlDelete = "DELETE FROM loginattempt WHERE ipAddress=? AND userAgent=?";
	$lockOutNumber = 0;
	$sqlLoginAttempts = "SELECT lockoutAttempts FROM loginattempt WHERE ipAddress=? AND userAgent=?";
	$sqlSaltAndHashPass = "SELECT salt,hashedPassword FROM securetable WHERE userName=?"; 
	#Check login attempts
	$lockOutNumber = checkLoginOrRegisterAttempts($ip,$sanUserAgent,$sqlLoginAttempts);
	
	if($lockOutNumber == 5)
	{
		$canLogin = getDateByIpAddress($ip,$sanUserAgent,$sqlSelectDate);
		if($canLogin == 1)
		{
			deleteRecord($ip,$sanUserAgent,$sqlDelete);
			$lockOutNumber = 0;
		}
		else
		{
			echo "You haved been locked out from entering a password for three minutes";
			$conn->close();
			header('refresh:5; login.html');
			die();
		}
	}
	
	  // Check connection
	  if ($conn->connect_error) {
		die("Internet Connection failed: " . $conn->connect_error);
	  }
	  else{
			$result = getPasswordDetails($sanName,$sqlSaltAndHashPass);
			if($result == 0)#return failed message to user
			{
				$error = "<br>The username " . $sanName . " and " . " password could not be authenticated at the moment";
				echo $error;
				if($lockOutNumber < 5)
				{
					if($lockOutNumber == 0)
					{
						addToAttempts($ip,$sanUserAgent,$lockOutNumber+1,$sqlInsertIntoLoginAttempts);
					}
					else
					{
						updateAttempts($ip,$sanUserAgent,$lockOutNumber+1,$sqlUpdateLoginAttempts);
					}
					addLogRecord($ip,$sanUserAgent,"Login Form","Failed",$lockOutNumber,$sanName);
				}	
				
			}
			else
			{
				$salt = $result["salt"];#salt from database
				$hashedPass = $result["hashedPassword"];#salted and hashed password from database
				$isCorrect = checkPassword($salt,$password,$hashedPass);
				if($isCorrect != 0)
				{
					date_default_timezone_set('Europe/London');
					session_start();
					addLogRecord($ip,$sanUserAgent,"Login Form","Succeeded",$lockOutNumber,$sanName);
					deleteRecord($ip,$sanUserAgent,$sqlDelete);
					$_SESSION['userName'] = $sanName;
					$_SESSION['sessionDuration'] = date("Y-m-d H:i:s", strtotime('+1 hours'));
					$_SESSION['tenMinute'] = date("Y-m-d H:i:s");
					if($sanName != "ADMIN")
					{
						$_SESSION["authenticated"] = true;
						header("Location: menu.php");
					}
					else
					{
						$_SESSION["auth"] = true;
						header("Location: showLoginAttempts.php");
					}
				}
				else
				{
					if($lockOutNumber == 0)
					{
						addToAttempts($ip,$sanUserAgent,$lockOutNumber+1,$sqlInsertIntoLoginAttempts);
					}
					else
					{
						updateAttempts($ip,$sanUserAgent,$lockOutNumber+1,$sqlUpdateLoginAttempts);
					}
					addLogRecord($ip,$sanUserAgent,"Login Form","Failed",$lockOutNumber,$sanName);
					$error = "<br>The username " . $sanName . " and " . " password could not be authenticated at the moment";
					echo $error;
				}
			}
			$conn->close();
			header('refresh:5; login.html');
	  }
	
	function checkPassword($salt,$password,$hashedPass)
	{
		$isTrue = true;
		$saltPlusPassword = $salt . $password;
		$saltedAndHashedPassword = md5($saltPlusPassword);
		
		for($i = 0; $i < strlen($hashedPass);$i++){
			if($hashedPass[$i] != $saltedAndHashedPassword[$i])
			{
				$isTrue = false;
			} 
		}
		return $isTrue;
	}
?>