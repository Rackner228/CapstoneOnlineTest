<?php
// generate_audio.php

// 1. SETUP
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

// 2. GET INPUT
$inputData = json_decode(file_get_contents('php://input'), true);
$text = $inputData['text'] ?? '';

if (empty($text)) {
    echo json_encode(['error' => 'No text provided.']);
    exit;
}

// 3. DEEPGRAM INTEGRATION
$deepgramApiKey = getenv('deep_gram'); 
$ttsUrl = "https://api.deepgram.com/v1/speak?model=aura-athena-en&encoding=mp3";

$ttsData = ["text" => $text];

$ttsOptions = [
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/json\r\n" .
                     "Authorization: Token " . $deepgramApiKey . "\r\n",
        'content' => json_encode($ttsData),
        'ignore_errors' => true
    ],
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
    ]
];

$ttsContext = stream_context_create($ttsOptions);
$audioContent = file_get_contents($ttsUrl, false, $ttsContext);

// 4. RETURN AUDIO
if ($audioContent) {
    echo json_encode(['audioContent' => base64_encode($audioContent)]);
} else {
    echo json_encode(['error' => 'Failed to generate audio']);
}
?>