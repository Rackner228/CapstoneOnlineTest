<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Profile - ASCEND</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container { max-width: 800px; margin: 2rem auto; padding: 20px; background: white; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .tabs { display: flex; cursor: pointer; border-bottom: 2px solid #ddd; margin-bottom: 20px; }
        .tab { padding: 10px 20px; border: 1px solid transparent; margin-bottom: -2px; }
        .tab.active { border: 1px solid #ddd; border-bottom: 2px solid white; font-weight: bold; color: #00274c; }
        .tab-content { display: none; padding: 20px; }
        .tab-content.active { display: block; }
        .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; }
        .gallery img { width: 100%; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <?php
    $id = $_GET['id'] ?? '';
    $path = "patients/" . $id . "/";
    $jsonPath = $path . "data.json";

    if ($id && file_exists($jsonPath)) {
        $data = json_decode(file_get_contents($jsonPath), true);
        $images = glob($path . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
    ?>

        <h2>Patient Profile: <?php echo htmlspecialchars($data['name']); ?></h2>

        <div class="tabs">
            <div class="tab active" onclick="openTab(event, 'Info')">General Info</div>
            <div class="tab" onclick="openTab(event, 'Diagnosis')">Diagnosis</div>
            <div class="tab" onclick="openTab(event, 'Images')">Images (<?php echo count($images); ?>)</div>
        </div>

        <div id="Info" class="tab-content active">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($data['name']); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($data['age']); ?></p>
            <p><strong>Profile Created:</strong> <?php echo htmlspecialchars($data['created_at']); ?></p>
        </div>

        <div id="Diagnosis" class="tab-content">
            <h3>Medical Assessment</h3>
            <p><?php echo nl2br(htmlspecialchars($data['diagnosis'])); ?></p>
        </div>

        <div id="Images" class="tab-content">
            <div class="gallery">
                <?php foreach ($images as $img): ?>
                    <img src="<?php echo $img; ?>" onclick="window.open(this.src)">
                <?php endforeach; ?>
            </div>
        </div>

    <?php 
    } else {
        echo "<p>Patient not found.</p>";
    }
    ?>
    <br><a href="index.php">← Back to Dashboard</a>
</div>

<script>
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) { tabcontent[i].classList.remove("active"); }
    tablinks = document.getElementsByClassName("tab");
    for (i = 0; i < tablinks.length; i++) { tablinks[i].classList.remove("active"); }
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
}
</script>

</body>
</html>