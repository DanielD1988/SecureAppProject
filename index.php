<?php

	$servername = "localhost";
	$username = "SADUSER";
	$password = "SADUSER";
	$dbName = "secureappproject";

	#create database called secureappproject
	$sql = "CREATE DATABASE secureappproject";
	$conn = new mysqli($servername, $username, $password);
	$conn->query($sql);
	$conn->close();
	
	#create users table called secure table
	$sql = "CREATE TABLE securetable ( 
	 userName VARCHAR(100) NOT NULL , 
	 salt VARCHAR(100) NOT NULL , 
	 hashedPassword VARCHAR(100) NOT NULL , 
	 id INT NOT NULL AUTO_INCREMENT PRIMARY KEY)";
	$co = new mysqli($servername, $username, $password,$dbName);
	$co->query($sql);
	$co->close();
	
	#create login attempts table
	$sql = "CREATE TABLE loginAttempt ( 
	 ipAddress VARCHAR(40) NOT NULL, 
	 userAgent VARCHAR(500) NOT NULL,
	 lockoutAttempts int NOT NULL , 
	 date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, 
	 id INT NOT NULL AUTO_INCREMENT PRIMARY KEY)";
	 $c = new mysqli($servername, $username, $password,$dbName);
	 $c->query($sql);
	 $c->close();
	 
	 #create register attempts table
	 $sql = "CREATE TABLE registerAttempt ( 
	 ipAddress VARCHAR(40) NOT NULL, 
	 userAgent VARCHAR(500) NOT NULL,
	 lockoutAttempts int NOT NULL , 
	 date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, 
	 id INT NOT NULL AUTO_INCREMENT PRIMARY KEY)";
	 $reg = new mysqli($servername, $username, $password,$dbName);
	 $reg->query($sql);
	 $reg->close();
	 
	 #create register attempts table
	 $sql = "CREATE TABLE log ( 
	 ipAddress VARCHAR(40) NOT NULL, 
	 userAgent VARCHAR(500) NOT NULL,
	 loggedFrom VARCHAR(30) NOT NULL,
	 loggedIn VARCHAR(40) NOT NULL,
	 userName VARCHAR(100) NOT NULL,
	 lockoutAttempts int NOT NULL , 
	 date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, 
	 id INT NOT NULL AUTO_INCREMENT PRIMARY KEY)";
	 $log = new mysqli($servername, $username, $password,$dbName);
	 $log->query($sql);
	 $log->close();
	 
	 #create a new ADMIN user for secure table
	 $in = new mysqli($servername, $username, $password,$dbName);
	 $password = "SaD_2021!";
	 $user = "ADMIN";
	 $index = 1;
	 $salt = bin2hex(openssl_random_pseudo_bytes(32));#https://stackoverflow.com/questions/4356289/php-random-string-generator
	 $saltPlusPassword = $salt . $password;
	 $saltedAndHashedPassword =  md5($saltPlusPassword);
	 $stmt = $in->prepare("INSERT INTO securetable (userName, salt,hashedPassword, id) VALUES (?, ?, ?,?)");
	 $stmt->bind_param("sssi",$user,$salt, $saltedAndHashedPassword,$index);
	 $stmt->execute();
	 $stmt->close();
	 $in->close();
	 header("Location: login.html");

?>