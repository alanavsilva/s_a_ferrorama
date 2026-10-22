<?php

$host = 'localhost';
$username = 'root';
$pass = '';
$dbname = 'SA_FERRORAMA';

$conn = new mysqli($host, $username, $pass, $dbname, 3306);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

?>