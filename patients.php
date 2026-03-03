<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cases | ASCEND</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Specific Fixes for Patient Cards */
        .patient-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            align-items: stretch; /* Ensures all cards in a row are equal height */
        }
        
        .patient-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-sizing: border-box; /* Fixes padding calculations */
        }

        .patient-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .card-footer {
            margin-top: auto; /* Pushes the button to the bottom */
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }
    </style>
</head>
<body>

    <header class="app-header">
        <div class="brand">
            <span class="brand-logo">ASCEND</span>
            <div class="brand-divider"></div>
            <span class="brand-context">Clinical Simulation Portal</span>
        </div>
        <nav class="nav-links">
            <a href="index.php" class="nav-item">Dashboard</a>
            <a href="patients.php" class="nav-item active">Cases</a>
        </nav>
    </header>

    <main class="container">
        <div class="page-header">
            <h1 class="page-title">Patient Cases</h1>
            <p class="page-subtitle">Available scenarios for evaluation.</p>
        </div>

        <div class="patient-grid">
            <?php
            $json_path = 'data/patients.json';
            if (file_exists($json_path)) {
                $json = file_get_contents($json_path);
                $patients = json_decode($json, true);

                if (!empty($patients)) {
                    foreach ($patients as $patient) {
                        echo "
                        <div class='patient-card'>
                            <div style='display:flex; justify-content:space-between; margin-bottom:1rem;'>
                                <span class='text-label'>ID: {$patient['id']}</span>
                                <span class='text-label' style='color:var(--accent);'>Open</span>
                            </div>
                            
                            <h3 style='margin:0 0 0.75rem 0; color:var(--primary); font-size: 1.25rem;'>{$patient['name']}</h3>
                            
                            <p style='color:var(--text-muted); font-size:0.95rem; line-height: 1.5; margin-bottom: 1.5rem;'>
                                {$patient['summary']}
                            </p>
                            
                            <div class='card-footer'>
                                <a href='profile.php?id={$patient['id']}' class='btn btn-outline' style='width:100%; justify-content: center;'>View Chart</a>
                            </div>
                        </div>";
                    }
                } else {
                    echo "<div class='patient-card'>No active patient cases found.</div>";
                }
            } else {
                echo "<div class='patient-card' style='border-color: red; color: red;'>System Error: Data source unreachable.</div>";
            }
            ?>
        </div>
    </main>

</body>
</html>