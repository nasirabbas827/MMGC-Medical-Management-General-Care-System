<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

if (!isset($_GET['id'])) exit("Invalid request.");

$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM lab_reports WHERE id=$id AND uploaded_by=$doctor_id");
if ($res->num_rows != 1) exit("Report not found.");
$report = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_remarks = $_POST['doctor_remarks'];
    $doctor_approved = isset($_POST['doctor_approved']) ? 1 : 0;

    $file_path = $report['file_path'];
    if (isset($_FILES['file_path']) && $_FILES['file_path']['error'] === UPLOAD_ERR_OK) {
        // Remove old file if exists
        $old_file = "../admin/" . $file_path;
        if (file_exists($old_file)) unlink($old_file);

        // Upload new file
        $target_dir = "../admin/uploads/";
        $file_name = time() . "_" . basename($_FILES['file_path']['name']);
        move_uploaded_file($_FILES['file_path']['tmp_name'], $target_dir . $file_name);

        // Save relative path in DB
        $file_path = "uploads/" . $file_name;
    }

    $stmt = $conn->prepare("UPDATE lab_reports SET file_path=?, doctor_remarks=?, doctor_approved=? WHERE id=? AND uploaded_by=?");
    $stmt->bind_param("ssiii", $file_path, $doctor_remarks, $doctor_approved, $id, $doctor_id);

    if ($stmt->execute()) {
        echo "<script>alert('Report updated.'); window.location='view_lab_reports_doctor.php';</script>";
    } else {
        $error = "Update failed: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Lab/Ultrasound Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
    <div class="card mx-auto" style="max-width:700px;">
        <div class="card-body">
            <h3>Edit Lab/Ultrasound Report</h3>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>File</label>
                        <input type="file" name="file_path" class="form-control-file">
                        <small>Current: <a href="../admin/<?= $report['file_path'] ?>" target="_blank">View</a></small>
                    </div>
                    <div class="form-group col-md-6 form-check align-self-end">
                        <input type="checkbox" name="doctor_approved" class="form-check-input" id="approvedCheck" <?= $report['doctor_approved'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="approvedCheck">Approve Report</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Doctor Remarks</label>
                    <textarea name="doctor_remarks" class="form-control" rows="3"><?= htmlspecialchars($report['doctor_remarks']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update & Approve</button>
                <a href="view_lab_reports_doctor.php" class="btn btn-dark">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
