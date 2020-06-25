<?php

$dsn = "mysql:host=localhost;dbname=taskdb";
$user = "leimk";
$passwd = "Yindi!@#";

$pdo = new PDO($dsn, $user, $passwd);

$id = 12;

$stm = $pdo->prepare("SELECT * FROM tbltasks");

$stm->execute();

$row = $stm->fetch(PDO::FETCH_ASSOC);

print_r($row);
