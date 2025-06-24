<?php 

$hostname = 'localhost';
$username = 'root';
$password = "";
$dbname = 'student_system';


$conn = new mysqli($hostname, $username, $password, $dbname);

if (!$conn){
    die("Error");
}
?>