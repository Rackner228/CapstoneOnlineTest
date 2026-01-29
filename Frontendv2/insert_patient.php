<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Grabbing Patient Data
    if (isset($_POST['patient_name']) && $_POST['patient_name'] !== "") {
        $patientName = $_POST['patient_name'];
    } else {
        $patientName = 'Unknown_Patient';
    }

    // Grabbing the Age
    if (isset($_POST['age']) && $_POST['age'] !== "") {
        $age = $_POST['age'];
    } else {
        $age = 'N/A';
    }

    // Grabbing the Diagnosis
    if (isset($_POST['diagnosis']) && $_POST['diagnosis'] !== "") {
        $diagnosis = $_POST['diagnosis'];
    } else {
        $diagnosis = 'No Diagnosis Provided';
    }

    // Grabbing the sex
    if (isset($_POST['sex']) && $_POST['sex'] !== "") {
        $sex = $_POST['sex'];
    } else {
        $sex = 'ERROR';
    }

    // Sanitizing information
    $safeFolderName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $patientName);
    $targetDir = "patients/" . $safeFolderName . "/";

    // Creating the directory
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Save Patient Data to a JSON file (Added this line so the text data is actually saved)
    $patientData = [
        "name" => $patientName,
        "age" => $age,
        "sex" => $sex,
        "diagnosis" => $diagnosis,
        "created_at" => date("Y-m-d H:i:s")
    ];
    file_put_contents($targetDir . "data.json", json_encode($patientData, JSON_PRETTY_PRINT));

    // Handle Multiple Image Uploads
    if (isset($_FILES['patient_images'])) {
        $fileCount = count($_FILES['patient_images']['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['patient_images']['error'][$i] == 0) {
                $fileName = basename($_FILES['patient_images']['name'][$i]);
                $targetFilePath = $targetDir . $fileName;
                move_uploaded_file($_FILES['patient_images']['tmp_name'][$i], $targetFilePath);
            }
        }
    }

    // Javascript to make Success
    echo "<script>
            alert('Success: Patient profile and directory created.');
            window.location.href = 'Admin.php';
          </script>";
    } else {
        header("Location: Admin.php");
        exit();
    }
?>