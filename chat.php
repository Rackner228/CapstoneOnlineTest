<?php
// chat.php

// 1. SETUP
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

// 2. CONFIGURATION
// -------------------------
$apiKey = gen_env('llama_API_KEY');
$modelName = 'llama-3.1-8b-instant';

// 3. GET USER INPUT
$inputData = json_decode(file_get_contents('php://input'), true);
$userMessage = $inputData['message'] ?? '';
$patientID = $inputData['patient_id'] ?? 'sarah_jenkins';
$history = $inputData['history'] ?? [];

if (empty($userMessage)) {
    echo json_encode(['error' => 'No message provided.']);
    exit;
}

// 4. LOAD SYSTEM PROMPT
$json_path = 'data/patients.json';
$systemContext = "VERY IMPORTANT: Use realistic speech patterns. Do not use brackets, parentheses, or asterisks in your responses. Speak only in plain text. Speak only as the patient. Stay in character. Keep responses brief. Act as though you are an actual patient. IMPORTANT: Ensure that you act like a real person to give the most realistic interaction possible. This includes tone, body language, general behavior.";
if (file_exists($json_path)) {
    $patients = json_decode(file_get_contents($json_path), true);
    if (isset($patients[$patientID]['system_prompt'])) {
        $systemContext = $patients[$patientID]['system_prompt'];
    }
}

// 5. PREPARE GROQ MESSAGES (OpenAI Format)
// -------------------------
$messages = [
    ["role" => "system", "content" => $systemContext]
];

// Convert your existing JS history into Groq format
foreach ($history as $chat) {
    $role = ($chat['role'] === 'model') ? 'assistant' : 'user';
    $messages[] = ["role" => $role, "content" => $chat['parts'][0]['text']];
}

// Add the current message if it's not already in history
$messages[] = ["role" => "user", "content" => $userMessage];

$postData = [
    "model" => $modelName,
    "messages" => $messages,
    "temperature" => 0.7,
    "max_tokens" => 1024
];

// 6. SEND REQUEST TO GROQ
// -------------------------
$url = "https://api.groq.com/openai/v1/chat/completions";

$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/json\r\n" .
                     "Authorization: Bearer " . $apiKey . "\r\n",
        'content' => json_encode($postData),
        'ignore_errors' => true
    ],
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
    ]
];

$context  = stream_context_create($options);
$response = file_get_contents($url, false, $context);

// 7. HANDLE GROQ RESPONSE
// -------------------------
$data = json_decode($response, true);

if (isset($data['choices'][0]['message']['content'])) {
    $aiText = $data['choices'][0]['message']['content'];

    // Send ONLY the text back immediately
    echo json_encode([
        'candidates' => [
            ['content' => ['parts' => [['text' => $aiText]]]]
        ]
    ]);

} else {
    echo json_encode(['error' => 'Groq API Error', 'details' => $data]);
}
?>