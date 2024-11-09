<?php

$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'restaurant-management-system';

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
