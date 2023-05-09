<?php
$servername = "sql107.epizy.com";
$dbname = "epiz_33957598_login";
$username = "epiz_33957598";
$password = "hGuGf2OigA";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>