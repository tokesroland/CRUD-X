<?php

// Backend rendszer összes tesztelése eddig

echo "<pre>";

// 1. test: Config és db kapcsolat 
echo "Test 1: Adatbázis kapcsolat: ";
try {
    // Próbáljuk betölteni a configot
    if (!file_exists('config.php')) throw new Exception("Nincs config.php!");

    require 'config.php';
    
    // Egyszerű lekérdezés.
    $stmt = $pdo->query("SELECT * FROM users LIMIT 1");
    if ($stmt) echo "<span style='color:green'>[SIKERES]</span>\n";
    else {
        throw new Exception("A kapcsolat jó, de a lekérdezés nem működik.");
    }
} catch (Exception $e) {
    echo "<span style='color:red'>[HIBA]</span> - " . $e->getMessage() . "\n";
}

// 2. test: Időzóna 
echo "Test 2: Időzóna (Budapest): ";
$currentTime = date('Y-m-d H:i:s');
$timezone = date_default_timezone_get();
if ($timezone === 'Europe/Budapest') {
    echo "<span style='color:green'>[SIKERES]</span> (idő: $currentTime)\n";
} else {
    echo "<span style='color:red'>[HIBA]</span> - A beállított zóna: $timezone (Elvárt: Europe/Budapest)\n";
}

// // Pixabay API teszt - 
// Régebben működött a test. Hibát dob a kulcsra, úgy hogy a product.php-n működik(???)

echo "Test 3: Pixabay API elérés: (test inactive)\n";

//  $pixabayKey = getenv('54513081-74f330c32357e16564569569e'); 
//  if (!$pixabayKey) {
//      echo "<span style='color:red'>[HIBA]</span> - Nincs PIXABAY_KEY környezeti változó!\n";
//  } else {
//      $response = @file_get_contents("https:pixabay.com/api/?key=$pixabayKey&q=teszt&image_type=photo");
//      if ($response) {
//          $data = json_decode($response, true);
//          if (isset($data['hits'])) {
//              echo "<span style='color:green'>[SIKERES]</span> - Pixabay API elérhető, találatok száma: " . count($data['hits']) . "\n";
//          } else {
//              echo "<span style='color:red'>[HIBA]</span> - Pixabay API válasz nem tartalmaz 'hits' kulcsot.\n";
//          }
//      } else {
//          echo "<span style='color:red'>[HIBA]</span> - Nem sikerült elérni a Pixabay API-t. Ellenőrizze a kulcsot és az internetkapcsolatot.\n";
//      }
//  }

// 4. test: Összes Fájlok megléte
echo "Test 4: Fileok megléte.\n";
$files = [
    // Root files
    'admin.php',
    'config.php',
    'crudx.sql',
    'docker-compose.yml',
    'Dockerfile',
    'index.php',
    'inventory.php',
    'login.php',
    'owner.php',
    'product.php',
    'products.php',
    'reports.php',
    'test_backend.php',
    'transport.php',
    'transports.php',
    // API files
    'api/api.php',
    'api/v1/product_details.php',
    // Components files
    'components/admin_backend.php',
    'components/auth_check.php',
    'components/filter.php',
    'components/footer.php',
    'components/logout.php',
    'components/navbar.php',
    'components/reports_filter.php',
    'components/stats_query.php',
    'components/transports_backend.php',
    // Script files
    'script/admin.js',
    'script/owner.js',
    'script/script.js',
    'script/transports_filter.js',
    // Style files
    'style/admin.css',
    'style/index.css',
    'style/inventory.css',
    'style/owner.css',
    'style/product.css',
    'style/reports.css',
    'style/style.css',
    'style/transport.css',
    'style/transports.css'
];
foreach ($files as $file) {
    echo "   - $file: ";
    if (file_exists(__DIR__ . '/' . $file)) echo "<span style='color:green'>[VAN]</span>\n";
    else echo "<span style='color:red'>[HIÁNYZIK!]</span>\n";
}

// 5. test: Utolsó módosítás az adatbázisban csak az óra kell
echo "\nTest 5: Utolsó módosítás a táblákban:\n";
$stmt = $pdo->query("SELECT MAX(date) AS last_trans FROM transports");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "   - Utolsó szállítás: " . ($result['last_trans'] ?? 'N/A') . "\n";    

$stmt1 = $pdo->query("SELECT MAX(created_at) AS last_product FROM products");
$result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
echo "   - Utolsó termék létrehozva: " . ($result1['last_product'] ?? 'N/A') . "\n";

$stmt2 = $pdo->query("SELECT MAX(login_at) AS last_log FROM users");
$result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
echo "   - Utolsó felhasználói aktivitás: " . ($result2['last_log'] ?? 'N/A') . "\n";

echo "</pre>";
echo "<hr><a href='index.php'>Vissza a főoldalra</a>";

?>
