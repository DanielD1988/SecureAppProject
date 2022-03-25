<?php
	session_start();
	if(isset($_SESSION["authenticated"]) && isset($_SESSION['userName']))
	{
		//used for CRSF token
		$token = md5(uniqid(rand(), TRUE));
		$_SESSION['token'] = $token;
		
		
		date_default_timezone_set('Europe/London');
		if(isset($_POST['Logout'])) {
			DestroySession();
		}
		$dateTime = date('Y-m-d H:i:s');
		
		if($_SESSION['sessionDuration'] < $dateTime)#logout if it goes over an hour
		{
			DestroySession();
		}
		if($_SESSION['tenMinute'] < $dateTime )
		{
			DestroySession();
		}
		$_SESSION['tenMinute'] = date("Y-m-d H:i:s", strtotime('+10 minutes'));#reset time on page if activity
	}
	else
	{
		
		DestroySession();
		header('Location:login.html', true, 302);
		exit;
	}
?>		

<html>   
   <head>
      
   </head>   
   <body>
		Welcome <input type="text" name="num" value="<?php echo $_SESSION["userName"];?>" disabled="disabled"/>
		 <p>This is the change password page of the website.</p>
		 <form action="updatePassword.php" method="Get">
		 <label for="pass">Old Password</label>
			<input type="password" name="Oldpass" pattern="^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$" minlength="8" title="Minimum eight characters, at least one uppercase letter, one lowercase letter, one number and one special character:" required ><br>
			<label for="pass">New Password</label>
			<input type="password" name="pass" pattern="^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$" minlength="8" title="Minimum eight characters, at least one uppercase letter, one lowercase letter, one number and one special character:" required ><br>
			<!-- pattern match taken from https://stackoverflow.com/questions/19605150/regex-for-password-must-contain-at-least-eight-characters-at-least-one-number-a/19605207-->
			<input type="hidden" name="CSRFToken" value="<?php echo $token; ?>"><!--Hidden field to help stop cross site scripting-->
			<input type="submit" name="changePassword" id="changePassword" value="Change Password">
		 </form>
		 <form method="post">
		 <input type="submit" name="Logout" id="Logout" value="Logout">
		 </form>
		 <a href="menu.php"><button>Menu</button></a>
   </body>   
</html>
	<?php
		function DestroySession(){
		   session_destroy();
		   header('Location:login.html', true, 302);
		   exit;
		}
	?>