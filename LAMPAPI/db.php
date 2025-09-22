<?php

// API endpoint helper to establish database connection.

// Adjust credentials for our environment.

$DB_HOST = 'localhost';
$DB_USER = 'root';      
$DB_PASS = '';        
$DB_NAME = 'COP4331';


$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
?>
