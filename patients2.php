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
    $cards = [];

    foreach (glob("patients/*/data.json") as $filename) {
        // Read and decode the JSON
        $json = file_get_contents($filename);
        $patientData = json_decode($json, true);
        $folderName = basename(dirname($filename));

        // Adding the cards to the site
        $cards[] = [
            'name' => $patientData['name'] ?? 'Unknown',
            'age'  => $patientData['age'] ?? 'N/A',
            'sex'  => $patientData['sex'] ?? 'N/A',
            'img'  => "images/" . $folderName . ".jpg" // Using folder name for image
        ];
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
            font-family: sans-serif;
        }
        .homepage-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
            background: #eee;
        }
        .patient-name {
            font-weight: bold;
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 5px;
        }
        .patient-meta {
            color: #666;
            font-size: 0.9rem;
        }
    </style>

    <div class="homepage-grid">
    ';

    // --- RENDER GRID ---
    foreach ($cards as $card) {
        echo '<div class="homepage-card">';
        
        // Image check (checks if the .jpg exists in your images folder)
        if (!empty($card['img']) && file_exists($card['img'])) {
            echo '<img src="' . htmlspecialchars($card['img']) . '" alt="Patient Image">';
        } else {
            echo '<div style="height:150px; background:#f0f0f0; border-radius:8px; margin-bottom:1rem; display:flex; align-items:center; justify-content:center; color:#999;">No Image</div>';
        }

        // Display the specific patient information
        echo '<div class="patient-name">' . htmlspecialchars($card['name']) . '</div>';
        echo '<div class="patient-meta">';
        echo 'ID: ' . htmlspecialchars($card['id']) . ' | ';
        echo htmlspecialchars($card['sex']) . ' | ';
        echo htmlspecialchars($card['age']) . ' yrs';
        echo '</div>';
        
        echo '</div>';
    }

    echo '</div>';
    ?>

</body>
</html>