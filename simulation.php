<?php
$id = $_GET['id'] ?? 'sarah_jenkins';
$json_path = 'data/patients.json';

if (file_exists($json_path)) {
    $json = file_get_contents($json_path);
    $patients = json_decode($json, true);
    $p = $patients[$id] ?? $patients['sarah_jenkins'];
    $patientGender = $p['gender'] ?? 'female'; 
} else {
    die("Error: Data unavailable.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Encounter: <?php echo $p['name']; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="simulation.css">
    <script src="[https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js](https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js)"></script>

    <style>
        /* Overlay Styles */
        #start-overlay, #loading-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 33, 71, 0.95);
            display: flex; justify-content: center; align-items: center;
            z-index: 1000; flex-direction: column; color: white;
        }
        #loading-overlay { display: none; background: rgba(255,255,255,0.95); color: #333; }
        
        .start-card { background: white; color: #333; padding: 40px; border-radius: 8px; text-align: center; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        
        /* Report Styles (Hidden until generation) */
        #report-container { display: none; width: 800px; padding: 40px; background: white; color: #333; font-family: 'Inter', sans-serif; }
        .report-header { border-bottom: 2px solid #002147; padding-bottom: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; }
        .score-box { background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; border: 1px solid #e0e0e0; margin-bottom: 30px; }
        .score-huge { font-size: 3rem; font-weight: 800; color: #002147; }
        .domain-section { margin-bottom: 25px; page-break-inside: avoid; }
        .domain-title { font-size: 1.2rem; font-weight: 700; color: #002147; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px; }
        .feedback-item { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; }
        .feedback-comment { font-style: italic; color: #666; font-size: 0.85rem; margin-left: 20px; display: block; margin-bottom: 15px; }
    </style>
</head>
<body style="height: 100vh; overflow: hidden; display: flex; flex-direction: column;">

    <div id="start-overlay">
        <div class="start-card">
            <h2 style="color: var(--primary); margin-top: 0;">Ready to Begin?</h2>
            <p>Patient: <strong><?php echo $p['name']; ?></strong></p>
            <p style="font-size: 0.9rem; color: #666; margin-bottom: 30px;">
                Type <strong>"stop simulation"</strong> when you are finished to generate your performance report.
            </p>
            <button onclick="startSimulation()" class="btn btn-primary" style="padding: 15px 40px; font-size: 1.1rem;">Enter Exam Room</button>
        </div>
    </div>

    <div id="loading-overlay">
        <h2>Analyzing Performance...</h2>
        <p>Consulting the Faculty Rubric based on ASCEND guidelines.</p>
        <div style="margin-top:20px;">Please wait...</div>
    </div>

    <header class="app-header">
        <div class="brand">
            <span class="brand-logo">ASCEND</span>
            <div class="brand-divider"></div>
            <span class="brand-context">Simulated Encounter</span>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="index.php" class="btn btn-outline" style="padding: 0.25rem 0.75rem;">Exit</a>
        </div>
    </header>

    <div class="simulation-layout">
        <div id="chat-interface">
            <div id="chat-window">
                <div class="message system-message" id="opening-msg">
                    <strong>Patient:</strong> <?php echo $p['opening_line']; ?>
                </div>
            </div>
            
            <div class="input-area">
                <input type="text" id="user-input" placeholder="Type here (or 'stop simulation')..." onkeypress="handleKeyPress(event)" autocomplete="off">
                <button onclick="sendMessage()" class="btn btn-primary">Send</button>
            </div>
        </div>

        <aside class="sidebar">
            <div class="card" style="height: 100%; border: none; box-shadow: none; padding: 0;">
                <h3 style="font-size: 1rem; color: var(--text-muted);">Patient Context</h3>
                <hr style="border: 0; border-top: 1px solid var(--border); margin: 1rem 0;">
                <div style="margin-bottom: 1.5rem;"><div class="text-label">Name</div><div><?php echo $p['name']; ?></div></div>
                <div style="margin-bottom: 1.5rem;"><div class="text-label">Vitals</div>
                <div style="font-family: monospace; background: #eee; padding: 0.5rem; border-radius: 4px;"><?php echo $p['vitals']; ?></div></div>
                <div style="margin-top: auto; padding-top: 20px; font-size: 0.8rem; color: #999;">
                    Type "stop simulation" to end.
                </div>
            </div>
        </aside>
    </div>

    <div id="report-container"></div>

    <script>
        const currentPatientID = "<?php echo $id; ?>";
        const patientGender = "<?php echo $patientGender; ?>";
        const openingLine = "<?php echo addslashes($p['opening_line']); ?>";
        
        // Chat History Storage
        let chatHistory = [];

        function startSimulation() {
            document.getElementById('start-overlay').style.display = 'none';
            // Add opening line to history
            chatHistory.push({ role: "model", parts: [{ text: openingLine }] });
            window.speechSynthesis.getVoices(); 
            setTimeout(() => speakText(openingLine), 500);
        }

        // --- SPEECH SYNTHESIS (Same as before) ---
        function speakText(text) {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(text);
                let voices = window.speechSynthesis.getVoices();
                let selectedVoice = null;
                if (patientGender === 'female') {
                     selectedVoice = voices.find(v => v.name.includes('Microsoft Aria')) || voices.find(v => v.name.includes('Google US English')) || voices.find(v => v.name.includes('Female'));
                } else {
                     selectedVoice = voices.find(v => v.name.includes('Microsoft Guy')) || voices.find(v => v.name.includes('Male'));
                }
                if (selectedVoice) utterance.voice = selectedVoice;
                utterance.rate = 0.9; 
                window.speechSynthesis.speak(utterance);
            }
        }

        // --- MAIN CHAT LOGIC ---
        async function sendMessage() {
            const inputField = document.getElementById('user-input');
            const message = inputField.value.trim();
            if (message === "") return;

            // 1. CHECK FOR STOP COMMAND
            if (message.toLowerCase().includes('stop simulation')) {
                generateReport();
                return;
            }

            // 2. Add User Message to UI & History
            const chatWindow = document.getElementById('chat-window');
            chatWindow.innerHTML += `<div class="message user-message"><strong>You:</strong> ${message}</div>`;
            inputField.value = '';
            chatWindow.scrollTop = chatWindow.scrollHeight;
            
            chatHistory.push({ role: "user", parts: [{ text: message }] });

            // 3. System Typing Indicator
            const typingId = "typing-" + Date.now();
            chatWindow.innerHTML += `<div class="message system-message" id="${typingId}"><em>Patient is thinking...</em></div>`;
            chatWindow.scrollTop = chatWindow.scrollHeight;

            // 4. Send to Backend
            try {
                const response = await fetch('chat.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        message: message, 
                        patient_id: currentPatientID,
                        history: chatHistory // Send Full History
                    })
                });
                const data = await response.json();

                if (data.candidates && data.candidates[0].content) {
                    const aiText = data.candidates[0].content.parts[0].text;
                    document.getElementById(typingId).innerHTML = `<strong>Patient:</strong> ${aiText}`;
                    chatHistory.push({ role: "model", parts: [{ text: aiText }] });
                    speakText(aiText);
                } else {
                    document.getElementById(typingId).innerHTML = "<em>[Connection Interrupted]</em>";
                }
            } catch (error) {
                document.getElementById(typingId).innerHTML = "<em>[Network Error]</em>";
            }
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter') sendMessage();
        }

        // --- REPORT GENERATION ---
        async function generateReport() {
            document.getElementById('loading-overlay').style.display = 'flex';
            
            try {
                const response = await fetch('evaluate.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ history: chatHistory })
                });
                
                // Get the raw text first to debug
                const rawText = await response.text();
                
                try {
                    // Try to parse it as JSON
                    const data = JSON.parse(rawText);
                    
                    // If successful, generate PDF
                    renderAndDownloadPDF(data);
                } catch (jsonError) {
                    // If JSON fails, SHOW THE RAW TEXT ERROR
                    document.getElementById('loading-overlay').style.display = 'none';
                    alert("SERVER ERROR:\n" + rawText.substring(0, 400)); // Show first 400 chars of error
                    console.error("Raw Server Response:", rawText);
                }

            } catch (networkError) {
                document.getElementById('loading-overlay').style.display = 'none';
                alert("NETWORK ERROR: " + networkError.message);
            }
        }

        function renderAndDownloadPDF(data) {
            // Build HTML for the PDF
            let domainsHtml = '';
            
            if (data.feedback) {
                data.feedback.forEach(domain => {
                    let itemsHtml = '';
                    domain.items.forEach(item => {
                        let color = item.score >= 4 ? '#2e7d32' : (item.score >= 3 ? '#ed6c02' : '#d32f2f');
                        itemsHtml += `
                            <div>
                                <div class="feedback-item">
                                    <span>${item.label}</span>
                                    <span style="font-weight:bold; color:${color}">${item.score}/5</span>
                                </div>
                                <span class="feedback-comment">${item.comment}</span>
                            </div>`;
                    });

                    domainsHtml += `
                        <div class="domain-section">
                            <div class="domain-title">${domain.domain}</div>
                            ${itemsHtml}
                        </div>`;
                });
            }

            const htmlContent = `
                <div class="report-header">
                    <div>
                        <h1 style="margin:0; color:#002147;">Performance Report</h1>
                        <div style="color:#666;">Student: <strong>Simulated User</strong></div>
                        <div style="color:#666;">Case: <strong>${'<?php echo $p['name']; ?>'}</strong></div>
                        <div style="color:#666;">Date: <strong>${new Date().toLocaleDateString()}</strong></div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:2rem; font-weight:bold; color:#002147;">ASCEND</div>
                        <div style="font-size:0.8rem; text-transform:uppercase; letter-spacing:1px;">Clinical Simulation</div>
                    </div>
                </div>

                <div class="score-box">
                    <div style="font-size:0.9rem; text-transform:uppercase; color:#666; letter-spacing:1px;">Overall Proficiency</div>
                    <div class="score-huge">${data.overall_score || '-'} / 5</div>
                    <p>${data.summary || 'No summary available.'}</p>
                </div>

                ${domainsHtml}
                
                <div style="margin-top: 30px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 0.7rem; color: #999; text-align: center;">
                    Generated by ASCEND AI Evaluation Engine • Based on EANM/ATA Guidelines
                </div>
            `;

            const container = document.getElementById('report-container');
            container.innerHTML = htmlContent;

            // Use html2pdf to download
            const opt = {
                margin:       0.5,
                filename:     `ASCEND_Report_${currentPatientID}.pdf`,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(container).save().then(() => {
                document.getElementById('loading-overlay').style.display = 'none';
                alert("Simulation Ended. Report downloaded.");
                window.location.href = 'index.php';
            });
        }
    </script>
</body>
</html>