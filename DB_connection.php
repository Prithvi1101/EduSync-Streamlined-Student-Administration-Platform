<?php
	
	$host ="localhost";
	$user="root";
	$password="your mysql pw here";
	$db_name="your database name here";
	
	$con=mysqli_connect($host,$user,$password,$db_name);
	if(mysqli_connect_errno()){
		die("Failed to connect with MySQL:".mysqli_connect_error());
	}
	else
	{
		
	}
?>
