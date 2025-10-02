<?php
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "parqueadero";

$conn = mysqli_connect($servername, $username, $password, $db_name);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$conn->set_charset("utf8mb4");

$conn = new mysqli($servername, $username, $password, $db_name);

if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}
