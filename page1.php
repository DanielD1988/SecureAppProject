<?php
	session_start();
	if(isset($_SESSION["authenticated"]) && isset($_SESSION['userName']))
	{
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
		 <p>This is page 1 of the website.</p>
		 </br>
		 <a href="page2.php"><button>Page2</button></a>
		 </br>
		  <a href="menu.php"><button>Menu</button></a>
		 </br>
		  <a href="changePassword.php"><button>Change Password</button></a>
		 </br>
		 <form method="post">
		 <input type="submit" name="Logout" id="Logout" value="Logout">
		 </form>
   </body>   
</html>
	<?php
		function DestroySession(){
		   session_destroy();
		   header('Location:login.html', true, 302);
		   exit;
		}
	?>