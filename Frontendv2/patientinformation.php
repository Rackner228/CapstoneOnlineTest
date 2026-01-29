<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ASCEND</title>
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="patientinformation.css">
    </head>
    <body>

        <header>
            <h1>ASCEND</h1>
            <p style="margin: 5px 0 0 0; color: var(--usu-gold); font-style: italic;">AI-Supported Cancer Education and Nuclear-Medicine Dashboard</p>
        </header>

        <nav>
            <a href="index.php">Home</a>
            <a href="patient.php">Simulation</a>
        </nav>

        <main>
            <h2>Patient Information: Jonathan Doe</h2>
            <div class="tab">
                <button class="tablinks" onclick="openTab(event, 'Name')" id="defaultOpen">Identification</button>
                <button class="tablinks" onclick="openTab(event, 'History')">Medical History</button>
                <button class="tablinks" onclick="openTab(event, 'Imaging')">Imaging</button>
            </div>
        </main>



    </body>
</html>