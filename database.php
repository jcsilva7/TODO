<?php
    require_once __DIR__ . '/vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $username = $_ENV['DBUSER_NAME'] ?? '';
    $password = $_ENV['DBUSER_PASS'] ?? '';
    $db_name = $_ENV['DB_NAME'] ?? '';
    $db_host = $_ENV['DB_HOST'] ?? '';

    if(!isset($conn)){
        $conn = new mysqli($db_host, $username, $password, $db_name);

        if($conn->connect_error){
            die("Connection failed: " . $conn->connect_error);
        }
    }