<?php
// api/v1/product_details.php

// 1. Kényszerítsük a PHP-t, hogy a projekt gyökeréből dolgozzon
// Ez megoldja az elérési út problémákat a config.php-n belül
chdir(__DIR__ . '/../../'); 

require 'config.php'; 
session_start();

// JSON fejléc és hibakezelés
header('Content-Type: application/json; charset=utf-8');
error_reporting(0); 
ini_set('display_errors', 0);

// 1. Jogosultság ellenőrzés
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Nincs bejelentkezve']);
    exit;
}

// 2. Input validálás
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Érvénytelen ID: ' . $_GET['id']]);
    exit;
}

try {
    // 3. Termékadatok lekérése
    $stmt = $pdo->prepare("
        SELECT p.*, c.category_name
        FROM products p
        LEFT JOIN categories c ON c.ID = p.category_ID
        WHERE p.ID = :id
    ");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'A termék nem található az adatbázisban (ID: '.$id.')']);
        exit;
    }

    // 4. Készletadatok lekérése
    $stmtInv = $pdo->prepare("
        SELECT w.name AS warehouse, i.quantity
        FROM inventory i
        JOIN warehouses w ON w.ID = i.warehouse_ID
        WHERE i.product_ID = :id
        AND w.active = 1
    ");
    $stmtInv->execute(['id' => $id]);
    $inventory = $stmtInv->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => [
            'product' => $product,
            'inventory' => $inventory
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Adatbázis hiba történt']);
}