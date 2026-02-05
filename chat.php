<?php
// chat.php

// 1. SETUP & DEBUGGING
// -------------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 2. CONFIGURATION
// -------------------------
// *** PASTE YOUR API KEY HERE ***
$apiKey = getenv('GEMINI_API_KEY'); // Check your key!
if (!$apiKey) {
    echo json_encode(["overall_score" => 0, "summary" => "Server Error: API Key missing.", "feedback" => []]);
    exit;
}
$modelName = 'gemini-2.5-flash'; 

// 3. GET USER INPUT
// -------------------------
$inputData = json_decode(file_get_contents('php://input'), true);
$userMessage = $inputData['message'] ?? '';
$patientID = $inputData['patient_id'] ?? 'sarah_jenkins';

// Quick browser test support: ?message=Hello
if (!$userMessage && isset($_GET['message'])) {
    $userMessage = $_GET['message'];
}

if (empty($userMessage)) {
    echo json_encode(['error' => 'No message provided.']);
    exit;
}

// 4. LOAD SYSTEM PROMPT
// -------------------------
$json_path = 'data/patients.json';
$systemContext = "You are a helpful AI assistant."; // Default

if (file_exists($json_path)) {
    $patients = json_decode(file_get_contents($json_path), true);
    if (isset($patients[$patientID]['system_prompt'])) {
        $systemContext = $patients[$patientID]['system_prompt'];
    }
}

// 5. PREPARE REQUEST
// -------------------------
$url = "https://generativelanguage.googleapis.com/v1beta/models/$modelName:generateContent?key=" . $apiKey;

$postData = [
    "system_instruction" => [ "parts" => [ ["text" => $systemContext] ] ],
    "contents" => [
        [ "role" => "user", "parts" => [ ["text" => $userMessage] ] ]
    ]
];

// 6. SEND REQUEST (Using file_get_contents instead of cURL)
// -------------------------
$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/json\r\n",
        'content' => json_encode($postData),
        'ignore_errors' => true // Allows us to read error responses from Google
    ]
];

$context  = stream_context_create($options);
$response = file_get_contents($url, false, $context);

// 7. HANDLE RESPONSE
// -------------------------
// Check the HTTP response header to see if it was 200 OK
$status_line = $http_response_header[0] ?? '';
preg_match('/([0-9]{3})/', $status_line, $matches);
$status_code = $matches[1] ?? 500;

if ($response === FALSE) {
    echo json_encode(['error' => 'Connection failed completely. Check internet/DNS.']);
} elseif ($status_code != 200) {
    // If Google returned an error (like 400 or 500), show it
    echo json_encode([
        'error' => 'API Error', 
        'http_code' => $status_code, 
        'details' => json_decode($response)
    ]);
} else {
    // Success
    echo $response;
}
?>