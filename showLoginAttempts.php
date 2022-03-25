<?php
	require 'databaseVars.php';
	session_start();
	if(isset($_SESSION["auth"]) && isset($_SESSION['userName']))
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
		
		$conn = new mysqli($servername, $username, $password, $dbName);
		$sql = "SELECT * FROM log"; 
		$stmt = $conn->prepare($sql); 
		$stmt->execute();
		
	  #https://www.siteground.com/tutorials/php-mysql/display-table-data/  //for the table below
	  echo '<table border="0" cellspacing="3" cellpadding="2">
      <tr> 
          <td> <font face="Arial">Ip Address</font> </td> 
		  <td> <font face="Arial">User Agent</font> </td> 
		  <td> <font face="Arial">Logged From</font> </td> 
		  <td> <font face="Arial">Is Logged In</font> </td> 
          <td> <font face="Arial">Login Attempts</font> </td> 
          <td> <font face="Arial">Date and Time</font> </td> 
      </tr>';

	if ($result = $stmt->get_result()) {
		while ($row = $result->fetch_array()) {
			$field1name = $row[0];
			$field2name = $row[1];
			$field3name = $row[2];
			$field4name = $row[3];
			$field5name = $row[4];
			$field6name = $row[5];

			echo '<tr> 
					  <td>'.$field1name.'</td> 
					  <td>'.$field2name.'</td> 
					  <td>'.$field3name.'</td>
					  <td>'.$field4name.'</td>  
					  <td>'.$field5name.'</td>  
					  <td>'.$field6name.'</td>  
				  </tr>';
		}
    $result->free();
	} 
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
		}
	?>