<?php
	require 'databaseVars.php';
	session_start();
	$token = "dfdghh";
	$getToken = "fvdgs";
	
	if (isset($_SESSION['token']))
	{
		$token = $_SESSION['token'];
	}
	
	if (isset($_GET["CSRFToken"])) 
	{ //set the csrf token if its empty so it doesn't display errors
		$getToken = $_GET["CSRFToken"];
	}
	if($token == $getToken)
	{
		$conn = new mysqli($servername, $username, $password, $dbName);
		if (isset($_SESSION["userName"]))
		{
			$userName = $_SESSION["userName"];
		}
		if(isset($_GET['Oldpass']))
		{
			$result = getSaltAndPassword($userName,$conn);
			$salt = $result[0];#salt from database
			$hashedPass = $result[1];#salted and hashed password from database
			$password = $_GET['Oldpass'];
			
			$isCorrect = checkPassword($salt,$password,$hashedPass);
			if($isCorrect == 0)
			{
				$error = "<br>The old password is incorrect ";
				echo $error;
				header('refresh:3; changePassword.php');
				die();
			}
		}
		if (isset($_GET['pass']))
		{
			$newPassword = $_GET['pass'];
		}
		if(!isset($_GET['pass']) && !isset($_GET['Oldpass']))
		{
			header('Location:changePassword.php', true, 302);
		}
		$salt = bin2hex(openssl_random_pseudo_bytes(32));#https://stackoverflow.com/questions/4356289/php-random-string-generator
		$saltPlusPassword = $salt . $newPassword;
		$saltedAndHashedPassword =  md5($saltPlusPassword);
		$stmt = $conn->prepare("UPDATE securetable SET salt=?,hashedPassword=? WHERE userName=?");
		$stmt->bind_param("sss",$salt,$saltedAndHashedPassword,$userName);
		$stmt->execute();
		$stmt->close();
		$conn->close();
		echo "Password has been changed";
		header('refresh:3; changePassword.php');
	}
	else
	{
		session_destroy();
		header('Location:changePassword.php', true, 302);
		exit;
	}
	function getSaltAndPassword($name,$conn){
		$sql = "SELECT salt,hashedPassword FROM securetable WHERE userName=?"; 
		$stmt = $conn->prepare($sql); 
		$stmt->bind_param("s", $name);
		$stmt->execute();
		try
		{
			$result = $stmt->get_result(); // get the mysqli result
			$row = mysqli_fetch_array($result);
			return $row;
		}
		catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			return 0;
		}
	}
	function checkPassword($salt,$password,$hashedPass)
	{
		$isTrue = 1;
		$saltPlusPassword = $salt . $password;
		$saltedAndHashedPassword = md5($saltPlusPassword);
		
		for($i = 0; $i < strlen($hashedPass);$i++){
			if($hashedPass[$i] != $saltedAndHashedPassword[$i])
			{
				$isTrue = 0;
			} 
		}
		return $isTrue;
	}
?>		