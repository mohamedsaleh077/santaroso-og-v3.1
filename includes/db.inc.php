<?php
$host = "db";
$dbname = "santaroso";
$dbusername = "root"; // root
$dbpassword = "root"; // empty


$dsn = "mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4";

try{
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "connection failed: " . $e->getMessage();
}