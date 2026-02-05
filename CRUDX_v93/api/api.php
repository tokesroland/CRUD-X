<?php
// api/api.php
header('Content-Type: application/json');

// It's a good idea to keep your API key in a variable
$apiKey = '54513081-74f330c32357e16564569569e'; 

if (!isset($_GET['query']) || empty($_GET['query'])) {
    echo json_encode(['image_url' => 'images/products/no-image.png']);
    exit;
}

// Use the full search term (do not truncate to the first two words)
$rawQuery = trim($_GET['query']);
$searchTerm = urlencode($rawQuery);

// Added &lang=hu to the URL
$apiUrl = "https://pixabay.com/api/?key=$apiKey&q=$searchTerm&lang=hu&safesearch=true&image_type=photo&per_page=3";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Adding a timeout so your page doesn't hang if Pixabay is slow
curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

// Selection logic
$finalImage = 'images/products/no-image.png';
if (!empty($data['hits'][0]['webformatURL'])) {
    $finalImage = $data['hits'][0]['webformatURL'];
}

echo json_encode([
    'source' => 'Pixabay REST API',
    'image_url' => $finalImage,
    'language' => 'hu',
    'query_used' => $rawQuery
]);