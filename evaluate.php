<?php
// 1. SETUP
ini_set('display_errors', 0); 
error_reporting(0); 

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

// 2. CONFIG - Use your Groq Key here
$apiKey = getenv('llama_API_KEY'); 
$modelName = 'llama-3.3-70b-versatile'; // Higher logic model for better grading

// 3. GET INPUT
$inputData = json_decode(file_get_contents('php://input'), true);
$history = $inputData['history'] ?? [];

if (empty($history)) {
    echo json_encode(["overall_score" => 0, "summary" => "Error: No history.", "domains" => []]);
    exit;
}

// 4. FORMAT TRANSCRIPT
$transcript = "";
foreach ($history as $msg) {
    $role = ($msg['role'] == 'user') ? "STUDENT" : "PATIENT";
    $text = $msg['parts'][0]['text'] ?? "";
    $transcript .= "$role: $text\n";
}

// 5. PROMPT (Optimized for Llama 3)
$systemPrompt = "You are a Medical Faculty Evaluator. Grade the following transcript. " .
"Return ONLY a JSON object with this structure: { \"overall_score\": 5, \"summary\": \"text\", \"domains\": [ { \"domain_name\": \"text\", \"items\": [ { \"label\": \"text\", \"score\": 5, \"reinforcement\": \"text\", \"improvement\": \"text\", \"suggested_phrasing\": \"text\", \"citation\": \"text\" } ] } ] } " .
"Evaluate based on ASCEND/EANM/ATA guidelines.";

// 6. REQUEST TO GROQ
$url = "https://api.groq.com/openai/v1/chat/completions";

$postData = [
    "model" => $modelName,
    "messages" => [
        ["role" => "system", "content" => $systemPrompt],
        ["role" => "user", "content" => "Here is the transcript:\n" . $transcript]
    ],
    "temperature" => 0.1, // Low temperature for consistent JSON
    "response_format" => ["type" => "json_object"] // Forces Groq to return valid JSON
];

$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/json\r\n" .
                     "Authorization: Bearer " . $apiKey . "\r\n",
        'content' => json_encode($postData),
        'ignore_errors' => true
    ]
];

$context  = stream_context_create($options);
$response = file_get_contents($url, false, $context);

// 7. HANDLE RESPONSE
$data = json_decode($response, true);

if (isset($data['choices'][0]['message']['content'])) {
    // Groq returns the JSON string inside this field
    $rawText = $data['choices'][0]['message']['content'];
    
    // Pass the AI's JSON directly to the frontend
    echo $rawText; 
} else {
    echo json_encode([
        "overall_score" => 0, 
        "summary" => "AI Grading Failed", 
        "details" => $data
    ]);
}
?>