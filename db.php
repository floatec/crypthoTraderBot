<?php
$servername = "localhost";
$username = "trade";
$password = "b00tc4mp!";
$dbname = "trade";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
