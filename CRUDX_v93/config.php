<?php
// Ne jelenjenek meg hibák a felhasználónak, de naplózzuk őket
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error.log');

// Jelentsük az összes hibát (vagy igény szerint E_ALL & ~E_NOTICE)
error_reporting(E_ALL);

// Konfigurációk
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'crudx';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: ''; // XAMPP-ban üres
$BASE_URL = getenv('BASE_URL') ?: '/CRUDX_v93/';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Részletes hiba naplózása, felhasználónak csak általános üzenet
    error_log("Adatbázis hiba: " . $e->getMessage());
    die("Adatbázis kapcsolat sikertelen. Kérem próbálja később.");
}
?>
