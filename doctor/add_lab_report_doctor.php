<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// Fetch dropdown data
$patients = $conn->query("SELECT id, name FROM patients");
$lab_tests = $conn->query("SELECT id, test_name FROM lab_tests");
$procedures = $conn->query("SELECT id, procedure_name FROM procedures");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lab_test_id = $_POST['lab_test_id'] ?: NULL;
    $procedure_id = $_POST['procedure_id'] ?: NULL;
    $patient_id = $_POST['patient_id'];
    $file_type = $_POST['file_type'];
    $doctor_remarks = $_POST['doctor_remarks'];

    // File upload
    if (isset($_FILES['file_path']) && $_FILES['file_path']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../admin/uploads/";
        $file_name = time() . "_" . basename($_FILES["file_path"]["name"]);
        $full_path = $target_dir . $file_name;
        move_uploaded_file($_FILES['file_path']['tmp_name'], $full_path);

        // Save relative path in database
        $file_path = "uploads/" . $file_name;
    } else {
        $file_path = "";
    }

    $stmt = $conn->prepare("INSERT INTO lab_reports 
        (lab_test_id, procedure_id, patient_id, file_path, file_type, uploaded_by, doctor_remarks, doctor_approved) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("iiissis", $lab_test_id, $procedure_id, $patient_id, $file_path, $file_type, $doctor_id, $doctor_remarks);

    if ($stmt->execute()) {
        $success = "Report uploaded and approved successfully.";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Lab/Ultrasound Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
    <div class="card mx-auto" style="max-width:800px;">
        <div class="card-body">
            <h3>Upload Lab/Ultrasound Report</h3>
            <?php if(isset($success)) echo "<p class='text-success'>$success</p>"; ?>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Patient</label>
                        <select name="patient_id" class="form-control" required>
                            <option value="">Select Patient</option>
                            <?php while($p = $patients->fetch_assoc()) echo "<option value='{$p['id']}'>{$p['name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Lab Test</label>
                        <select name="lab_test_id" class="form-control">
                            <option value="">Select Lab Test</option>
                            <?php while($lt = $lab_tests->fetch_assoc()) echo "<option value='{$lt['id']}'>{$lt['test_name']}</option>"; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Procedure</label>
                        <select name="procedure_id" class="form-control">
                            <option value="">Select Procedure</option>
                            <?php while($pr = $procedures->fetch_assoc()) echo "<option value='{$pr['id']}'>{$pr['procedure_name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>File</label>
                        <input type="file" name="file_path" class="form-control-file" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>File Type</label>
                        <select name="file_type" class="form-control" required>
                            <option value="pdf">PDF</option>
                            <option value="image">Image</option>
                            <option value="text">Text</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Doctor Remarks</label>
                        <textarea name="doctor_remarks" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Upload & Approve</button>
                <a href="view_lab_reports_doctor.php" class="btn btn-dark">View Reports</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
