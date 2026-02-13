<?php
// Általános konfigurációk és adatbázis kapcsolat létrehozása

// Időzóna beállítása a helyes időkezeléshez az adatbázisban. (NOW(), CURRENT_TIMESTAMP() függvények a DB-ben, date() a PHP-ben)
date_default_timezone_set('Europe/Budapest');
// Ne jelenjenek meg hibák a felhasználónak, de naplózzuk őket
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error.log');

// Jelentsük az összes hibát (vagy igény szerint E_ALL & ~E_NOTICE)
error_reporting(E_ALL);

// --- AUTOMATIKUS BASE_URL FELISMERÉS ---
// Ez a rész működik Dockerben (gyökér) és XAMPP-ban (almappa) is.
// Megnézi, hogy a jelenlegi script (pl. index.php) milyen mappában van a szerver gyökeréhez képest.
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
// Windowsos (\) perjelek cseréje és a végére / illesztése
$BASE_URL = rtrim(str_replace('\\', '/', $scriptPath), '/') . '/';

// Konfigurációk
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'crudx';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: ''; // XAMPP-ban üres

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Ezzel kényszerítjük az adatbázist, hogy a PHP-val azonos időt használja.
    $pdo->exec("SET time_zone = '" . date('P') . "'");
} catch (PDOException $e) {
    // Részletes hiba naplózása, felhasználónak csak általános üzenet
    error_log("Adatbázis hiba: " . $e->getMessage());
    die("Adatbázis kapcsolat sikertelen. Kérem próbálja később.");
}
?>