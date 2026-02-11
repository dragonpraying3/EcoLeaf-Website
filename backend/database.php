<?php
//declare variables for database connection
$host="localhost";
$user="root";   
$password="";
$db="ecoleaf"; //database name

$conn=mysqli_connect($host,$user,$password,$db); //database connection

if ($conn->connect_error){ //if connection fails
    echo "Failed to connect Database:". $conn->connect_error;
}
?>