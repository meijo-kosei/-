<?php
    $dsn = 'mysql:dbname=********;host=localhost';
    $user = '********';
    $password = '********';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $sql = "CREATE TABLE IF NOT EXISTS tbtest7"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "post_id TEXT,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "reply_to INT"
    .");";
    
    //$sql = "TRUNCATE TABLE tbtest7";
    $stmt = $pdo->query($sql);
?>