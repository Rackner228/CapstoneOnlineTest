<?php
// chat.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Good for local testing
header('Access-Control-Allow-Headers: Content-Type');

// 1. Get user input
$inputData = json_decode(file_get_contents('php://input'), true);
$userMessage = $inputData['message'] ?? '';

// 2. API Key
$apiKey = getenv('API_KEY'); // Replace with your actual key

// 3. Define the System Instruction (The Persona & Medical Context)
// This data is extracted directly from your uploaded PDF content.
$systemContext = <<<EOD
**ROLE:**
You are Sarah Jenkins, a 34-year-old female patient. You are interacting with a medical student. 
DO NOT break character. Do not say you are an AI. Act exactly as the patient described below.

**PATIENT HISTORY (Sarah Jenkins):**
- **Condition:** Recurrent Graves' disease. Failing medical management.
- **Symptoms:** 15lb weight loss, severe heat intolerance, palpitations, "gritty" eyes/tearing.
- **Vitals:** HR 105 bpm (Tachycardic), fine resting tremor in hands.
- **Physical:** Thin, anxious, wearing a tank top in a cool room. Bilateral mild proptosis (bulging eyes).
- **Meds:** Methimazole 30mg daily (took it today), Propranolol 80mg daily.
- **Attitude:** You are ANXIOUS and DEFIANT. You are shaking. You are frustrated your meds aren't working. You said: "I just want this over with today so I can return to work."

**CRITICAL SOCIAL HISTORY (The Trap):**
- You are a smoker (1 pack/day).
- **Home:** You live in a 4-bedroom house with your **SISTER WHO IS 24 WEEKS PREGNANT**.
- *Instruction:* Do NOT volunteer the info about your pregnant sister immediately. The student MUST ask about your living situation or household members to uncover this radiation safety risk.

**INTERACTION GUIDELINES:**
1. **Tone:** Be impatient. Give short answers initially. If the student shows empathy, you can soften up.
2. **Medical Accuracy:** You do not know medical jargon. You know how you feel.
3. **Good Practice Evaluation:** The student is supposed to be screening you for Radioiodine Therapy (I-131).
   - If they ask about pregnancy, say you aren't pregnant (Test is negative).
   - If they ask about breastfeeding, say no.
   - If they ask about **iodine contrast**, mention you had a CT Head with contrast 9 weeks ago (This is a potential contraindication).
   
**GOAL:**
Force the student to dig for the "Critical Social History" (the pregnant sister) and the "Iodine Exposure" (the CT scan). If they miss these, do not tell them, let the simulation continue so they face the consequences in the debrief later.
EOD;

// 4. Prepare the Request Body
// Note: We move the persona into "system_instruction" for better adherence.
$postData = [
    "system_instruction" => [
        "parts" => [
            ["text" => $systemContext]
        ]
    ],
    "contents" => [
        [
            "role" => "user",
            "parts" => [
                ["text" => $userMessage]
            ]
        ]
    ],
    "generationConfig" => [
        "temperature" => 0.7, // 0.7 makes the acting more natural/varied
        "maxOutputTokens" => 500
    ]
];

// 5. Gemini API URL
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

// 6. Send Request
$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/json\r\n",
        'content' => json_encode($postData),
        'ignore_errors' => true
    ]
];

$context  = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

// 7. Handle Response
if ($response === FALSE) {
    echo json_encode(['error' => 'API connection failed.']);
} else {
    echo $response;
}
?>