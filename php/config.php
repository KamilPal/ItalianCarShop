<?php
//Połączenie z bazą danych
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projektpai";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
