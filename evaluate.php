<?php
// evaluate.php

// 1. SILENCE OUTPUT & SETUP
ini_set('display_errors', 0); // Crucial: Don't let errors break JSON
error_reporting(0); 

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

// Handle Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

// 2. CONFIG
$apiKey = ''; // Check your key!
$modelName = 'gemini-2.5-flash';

// 3. GET INPUT
$inputJSON = file_get_contents('php://input');
$inputData = json_decode($inputJSON, true);
$history = $inputData['history'] ?? [];

// Default dummy history if empty
if (empty($history)) {
    echo json_encode(["overall_score" => 0, "summary" => "Error: No history.", "feedback" => []]);
    exit;
}

// 4. FORMAT TRANSCRIPT
$transcript = "";
foreach ($history as $msg) {
    $role = ($msg['role'] == 'user') ? "STUDENT" : "PATIENT";
    $text = $msg['parts'][0]['text'] ?? "";
    $transcript .= "$role: $text\n";
}

// 5. PROMPT
$systemPrompt = <<<EOT
You are a Medical Faculty Evaluator. Grade this transcript.
CRITERIA:
1. MEDICAL KNOWLEDGE (Pathophysiology, Goals, Risks)
2. RADIATION SAFETY (Time/Distance, Children/Pregnant, Hygiene)
3. PATIENT CARE (History, Screening, Prep)
4. COMMUNICATION (Rapport, Clear Language)

OUTPUT FORMAT:
Return ONLY valid JSON. 
{
  "overall_score": "Number 1-5",
  "summary": "1-2 sentence summary",
  "feedback": [
    {
      "domain": "Medical Knowledge",
      "items": [
        { "label": "Pathophysiology", "score": 3, "comment": "Feedback here" }
      ]
    }
  ]
}
EOT;

// 6. REQUEST TO GOOGLE
$url = "https://generativelanguage.googleapis.com/v1beta/models/$modelName:generateContent?key=" . $apiKey;
$postData = [
    "system_instruction" => [ "parts" => [ ["text" => $systemPrompt] ] ],
    "contents" => [ [ "role" => "user", "parts" => [ ["text" => $transcript] ] ] ],
    "generationConfig" => [ "responseMimeType" => "application/json" ]
];

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => json_encode($postData),
        'ignore_errors' => true
    ]
]);

$response = file_get_contents($url, false, $context);

// 7. ROBUST JSON EXTRACTION & REPAIR
if ($response === FALSE) {
    echo json_encode(["overall_score" => 0, "summary" => "Connection Failed", "feedback" => []]);
    exit;
}

$jsonResponse = json_decode($response, true);

if (isset($jsonResponse['candidates'][0]['content']['parts'][0]['text'])) {
    $rawText = $jsonResponse['candidates'][0]['content']['parts'][0]['text'];

    // STEP A: Extract content between first { and last }
    if (preg_match('/\{.*\}/s', $rawText, $matches)) {
        $candidateJSON = $matches[0];
    } else {
        $candidateJSON = $rawText;
    }

    // STEP B: Try to Decode in PHP
    $decoded = json_decode($candidateJSON, true);

    // STEP C: Return Clean JSON or Error
    if (json_last_error() === JSON_ERROR_NONE) {
        // Success! Re-encode to ensure perfect format for JS
        echo json_encode($decoded);
    } else {
        // Fallback: The AI returned bad JSON (e.g. unescaped newlines)
        // We will send a "Safety Report" so the user still gets something.
        echo json_encode([
            "overall_score" => 1,
            "summary" => "Grading Error: The AI generated an invalid report structure. Please try again.",
            "feedback" => []
        ]);
    }
} else {
    echo json_encode(["overall_score" => 0, "summary" => "AI Error", "feedback" => []]);
}
?>