<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | ASCEND</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="app-header">
        <div class="brand">
            <span class="brand-logo">ASCEND</span>
            <div class="brand-divider"></div>
            <span class="brand-context">Clinical Simulation Portal</span>
        </div>
        <nav class="nav-links">
            <a href="index.php" class="nav-item active">Dashboard</a>
            <a href="patients.php" class="nav-item">Cases</a>
        </nav>
    </header>

    <main class="container">
        <div class="page-header">
            <h1 class="page-title">Welcome, Student</h1>
            <p class="page-subtitle">Select a module to begin your training.</p>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <div class="card">
                <h2 style="margin-top: 0; color: var(--primary);">Active Protocols</h2>
                <p style="color: var(--text-muted); margin-bottom: 2rem;">
                    Access high-fidelity simulated patient encounters for Nuclear Medicine training. 
                    Review patient charts, identify contraindications, and practice communication skills.
                </p>
                <a href="patients.php" class="btn btn-primary">Browse Patient Cases</a>
            </div>

            <div class="card" style="background-color: var(--primary); color: white;">
                <div class="text-label" style="color: var(--accent);">System Status</div>
                <div style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">Online</div>
                <p style="font-size: 0.875rem; opacity: 0.8;">
                    Gemini 2.5 Flash Model
                    <br>Latency: < 200ms
                </p>
            </div>
        </div>
    </main>

</body>
</html>