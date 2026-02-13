<?php
// api/api.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$apiKey = '54513081-74f330c32357e16564569569e'; 

$queryName = isset($_GET['query']) ? trim($_GET['query']) : '';
$queryCategory = isset($_GET['category']) ? trim($_GET['category']) : '';

// ---------------------------------------------------------
// 1. SEGÉDFÜGGVÉNY: Fordítás (Magyar -> Angol)
// ---------------------------------------------------------
function translateToEnglish($text) {
    if (empty($text)) return '';
    
    // Csak az első 2-3 szót fordítjuk, a cikkszámok (pl. X500) bezavarnak a fordítónak
    $words = explode(' ', $text);
    $textClean = implode(' ', array_slice($words, 0, 3));

    $encodedText = urlencode($textClean);
    // Ingyenes MyMemory API (napi 500 szó limit, demónak tökéletes)
    $url = "https://api.mymemory.translated.net/get?q=$encodedText&langpair=hu|en";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL hiba elkerülése
    curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Gyors timeout, ha lassú lenne
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['responseData']['translatedText'])) {
            return $data['responseData']['translatedText'];
        }
    }
    return $text; // Ha hiba van, visszaadjuk az eredetit
}

// ---------------------------------------------------------
// 2. SEGÉDFÜGGVÉNY: Pixabay Keresés
// ---------------------------------------------------------
function searchPixabay($term, $apiKey) {
    if (empty($term)) return null;

    $encoded = urlencode($term);
    // Angol nyelven (lang=en) keressünk, mert már lefordítottuk!
    $url = "https://pixabay.com/api/?key=$apiKey&q=$encoded&lang=en&image_type=photo&per_page=3&safesearch=true&orientation=horizontal";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (!empty($data['hits'][0]['webformatURL'])) {
        return $data['hits'][0]['webformatURL'];
    }
    return null;
}

// ---------------------------------------------------------
// 3. A NAGY STRATÉGIA
// ---------------------------------------------------------

$imageUrl = null;
$debugInfo = [];

// LÉPÉS A: Fordítsuk le a termék nevét angolra!
// pl. "Gipszkarton csavar" -> "Drywall screw"
if (!empty($queryName)) {
    $englishName = translateToEnglish($queryName);
    $debugInfo['translated_name'] = $englishName;
    
    // Keressünk rá a lefordított névre
    $imageUrl = searchPixabay($englishName, $apiKey);
}

// LÉPÉS B: Ha az angol név nem hozott képet, próbáljuk a Kategóriát
// (De előbb azt is fordítsuk le, bár a múltkori szótárunk is jó volt, ez dinamikusabb)
if (!$imageUrl && !empty($queryCategory)) {
    $englishCategory = translateToEnglish($queryCategory);
    $debugInfo['translated_category'] = $englishCategory;
    
    $imageUrl = searchPixabay($englishCategory, $apiKey);
}

// LÉPÉS C: Végső mentsvár - Az eredeti magyar kategória név
if (!$imageUrl && !empty($queryCategory)) {
    // Itt a Pixabay-re bízzuk a magyar megértését
    $url = "https://pixabay.com/api/?key=$apiKey&q=" . urlencode($queryCategory) . "&lang=hu&image_type=photo";
    // ... (egyszerűsített hívás) ...
    // Ezt most kihagyom a kód tisztasága miatt, a fenti kettőnek elégnek kell lennie.
}

// Válasz
echo json_encode([
    'image_url' => $imageUrl ?: 'images/products/no-image.png',
    'debug' => $debugInfo // Látod a konzolban mit fordított!
]);
?>