<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASCEND | Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>ASCEND</h1>
        <p style="margin: 5px 0 0 0; color: var(--usu-gold); font-style: italic;">AI-Supported Cancer Education and Nuclear-Medicine Dashboard</p>
    </header>

    <nav>
        <a href="index.php">Home</a>
        <a href="patient.php">Simulation</a>
        <a href="admin.php">Admin Panel</a>
    </nav>

    <main>
        <section>
            <h2>Add New Patient</h2>
            <p>Enter the details below to create a new AI patient persona for the simulation environment.</p>
            
            <!-- Inserting Patients Here -->
            <form action="insert_patient.php" method="POST" enctype="multipart/form-data">                <p>
                    <label> <strong>Patient Name</strong></label><br>
                    <input type="text" name="patient_name" style="width: 100%; padding: 10px; margin-top: 5px;" required>
                </p>

                <p>
                    <label><strong>Age</strong></label><br>
                    <input type="number" name="age" style="width: 100%; padding: 10px; margin-top: 5px;" required>
                </p>

                <p>
                    <label><strong>Sex</strong></label><br>
                    <select name="sex" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;" required>
                        <option value="" disabled selected>Select Sex</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </p>

                <p>
                    <label><strong>Diagnosis</strong></label><br>
                    <input type="text" name="diagnosis" style="width: 100%; padding: 10px; margin-top: 5px;" required>
                </p>

                <p>
                    <label><strong>Patient Medical Images</strong></label><br>
                    <span style="font-size: 0.85rem; color: var(--usu-dark-gray); font-style: italic;">
                        Upload DICOM, JPG, or PNG scans
                    </span>
                    <input type="file" name="patient_images[]" multiple 
                        style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; background: var(--usu-white);">
                </p>
                <br>
                <!-- End of inserting patient Data, Button to send data to insert_patient.php -->
                <button type="submit" class="btn-primary">Save Patient Persona</button>
            </form>
        </section>
    </main>

    <footer>
        <p>Uniformed Services University - ASCEND AI Project</p>
        <div class="footer-line">
            <p>&copy; 2026 USU Admin Portal</p>
        </div>
    </footer>

</body>
</html>