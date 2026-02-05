<?php
$id = $_GET['id'] ?? '';
$json_path = 'data/patients.json';

if (file_exists($json_path)) {
    $json = file_get_contents($json_path);
    $patients = json_decode($json, true);
}

if (!isset($patients) || !isset($patients[$id])) {
    header("Location: patients.php");
    exit;
}
$p = $patients[$id];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chart: <?php echo $p['name']; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="app-header">
        <div class="brand">
            <span class="brand-logo">ASCEND</span>
            <div class="brand-divider"></div>
            <span class="brand-context">Electronic Medical Record</span>
        </div>
        <nav class="nav-links">
            <a href="patients.php" class="nav-item">← Back to Cases</a>
        </nav>
    </header>

    <main class="container">
        <div class="card" style="margin-bottom: 2rem; border-left: 4px solid var(--primary);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="margin: 0; font-size: 1.5rem;"><?php echo $p['name']; ?></h1>
                    <span class="text-label">Age: <?php echo $p['age']; ?> | Vitals: <?php echo $p['vitals']; ?></span>
                </div>
                <a href="simulation.php?id=<?php echo $p['id']; ?>" class="btn btn-primary">Initiate Encounter</a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            
            <div class="card">
                <h3 style="margin-top: 0; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Clinical History</h3>
                
                <div style="margin-bottom: 1.5rem;">
                    <div class="text-label">HPI</div>
                    <div class="text-value"><?php echo $p['history']['HPI']; ?></div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <div class="text-label">Social History</div>
                    <div class="text-value"><?php echo $p['history']['Social']; ?></div>
                </div>
            </div>

            <div class="card">
                <h3 style="margin-top: 0; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Orders & Results</h3>
                
                <div style="margin-bottom: 1.5rem;">
                    <div class="text-label">Current Medications</div>
                    <div class="text-value"><?php echo $p['history']['Meds']; ?></div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <div class="text-label">Imaging / Labs</div>
                    <div class="text-value"><?php echo $p['history']['Imaging']; ?></div>
                    <div class="text-value" style="margin-top:0.5rem;"><?php echo $p['history']['Labs']; ?></div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>