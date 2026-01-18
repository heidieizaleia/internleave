<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'internleave');

//create database connection
function getDBConnection()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    //check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

try {
    $conn = getDBConnection();
    $conn->close();
} catch (Exception $e) {
    echo "Database connection error: " . $e->getMessage();
}
