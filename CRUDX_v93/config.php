<?php
// // 1. Kapcsolja ki az összes hibajelentést.  ---  Turn off error reporting.
// error_reporting(0);

// // 2. Futásidejű hibák jelentése.  ---  Report runtime errors.
// error_reporting(E_ERROR | E_WARNING | E_PARSE);

// // 3. Jelentse az összes hibát.   ---  Report all errors.
// error_reporting(E_ALL);

// // 4. Ugyanaz, mint a error_reporting(E_ALL);  ---  Same as error_reporting(E_ALL);
// ini_set("error_reporting", E_ALL);

// // 5. Jelentse az összes hibát, kivéve az E_NOTICE   ---  Report all errors except E_NOTICE 
// error_reporting(E_ALL & ~E_NOTICE);
$host = "localhost";
$dbname = "crudx";
$username = "root";
$password = "";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Adatbázis hiba: " . $e->getMessage());
}
