<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASCEND</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>ASCEND</h1>
        <p style="margin: 5px 0 0 0; color: var(--usu-gold); font-style: italic;">AI-Supported Cancer Education and Nuclear-Medicine Dashboard</p>
    </header>

    <nav>
        <a href="index.php">Home</a>
        <a href="patients.php">Patients</a>
    </nav>

    <?php

        foreach (glob("patients/*/data.json") as $filename) {
            
            // 1. Open this specific file
            $json = file_get_contents($filename);
            
            // Getting the json data
            $patientData = json_decode($json, true);
        
        }

    // --- CSS STYLES ---
    echo '
    <style>
        .homepage-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 20px;
        }
        .homepage-card {
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            background: #ffffff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .homepage-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
            background: #eee;
        }
        .patient-info {
            font-weight: bold;
            color: #333;
        }
    </style>

    <div class="homepage-grid">
    ';

    // --- RENDER GRID ---
    foreach ($cards as $card) {
        echo '<div class="homepage-card">';
        
        // Image check
        if (!empty($card['img']) && file_exists($card['img'])) {
            echo '<img src="' . htmlspecialchars($card['img']) . '" alt="Patient">';
        } else {
            echo '<div style="height:150px; background:#f0f0f0; border-radius:8px; margin-bottom:1rem; display:flex; align-items:center; justify-content:center; color:#999;">No Image</div>';
        }

        echo '<p class="patient-info">' . $card['text'] . '</p>';
        echo '</div>';
    }

    echo '</div>';
    ?>

</body>
</html>