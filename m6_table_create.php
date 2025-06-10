<?php
    $dsn = 'mysql:dbname=*******;host=localhost';
    $user = '********';
    $password = '********';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $sql = "CREATE TABLE IF NOT EXISTS tbtest4"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "password TEXT,"
    . "date TEXT,"
    . "image CHAR(255),"
    . "place TEXT,"
    . "lost_date TEXT,"
    . "address TEXT"
    .");";
    
    $sql = "TRUNCATE TABLE tbtest4";
    $stmt = $pdo->query($sql);
?>