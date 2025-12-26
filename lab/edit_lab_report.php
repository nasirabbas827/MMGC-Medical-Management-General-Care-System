<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'lab') {
    header("Location: ../login.php");
    exit();
}

$staff_id = $_SESSION['staff_id']; // Logged-in staff ID

if(!isset($_GET['id'])){
    echo "Invalid request.";
    exit;
}

$id = intval($_GET['id']);

// Fetch only reports uploaded by this staff
$res = $conn->query("SELECT * FROM lab_reports WHERE id=$id AND uploaded_by=$staff_id");
if($res->num_rows != 1) exit("Report not found or access denied.");
$report = $res->fetch_assoc();

// Dropdown data
$patients = $conn->query("SELECT id, name FROM patients");
$lab_tests = $conn->query("SELECT id, test_name FROM lab_tests");
$procedures = $conn->query("SELECT id, procedure_name FROM procedures");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $lab_test_id = $_POST['lab_test_id'] ?: NULL;
    $procedure_id = $_POST['procedure_id'] ?: NULL;
    $patient_id = $_POST['patient_id'];
    $file_type = $_POST['file_type'];
    $doctor_approved = isset($_POST['doctor_approved']) ? 1 : 0;
    $doctor_remarks = $_POST['doctor_remarks'];

    // File update
    $file_path = $report['file_path'];
    if(isset($_FILES['file_path']) && $_FILES['file_path']['error']===UPLOAD_ERR_OK){
        if(file_exists("../admin/".$file_path)) unlink("../admin/".$file_path);
        $target_dir="uploads/";
        $file_name=time()."_".basename($_FILES['file_path']['name']);
        $file_path = $target_dir.$file_name;
        move_uploaded_file($_FILES['file_path']['tmp_name'], "../admin/".$file_path);
    }

    $stmt = $conn->prepare("UPDATE lab_reports SET lab_test_id=?, procedure_id=?, patient_id=?, file_path=?, file_type=?, uploaded_by=?, doctor_approved=?, doctor_remarks=? WHERE id=? AND uploaded_by=?");
    $stmt->bind_param("iiissiisii", $lab_test_id, $procedure_id, $patient_id, $file_path, $file_type, $staff_id, $doctor_approved, $doctor_remarks, $id, $staff_id);

    if($stmt->execute()){
        echo "<script>alert('Lab report updated.'); window.location='view_lab_reports.php';</script>";
    } else {
        $error = "Update failed: ".$conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Lab Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
    <div class="card mx-auto" style="max-width:700px;">
        <div class="card-body">
            <h3>Edit Lab Report</h3>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Patient</label>
                        <select name="patient_id" class="form-control" required>
                            <?php while($p=$patients->fetch_assoc()) echo "<option value='{$p['id']}' ".($report['patient_id']==$p['id']?'selected':'').">{$p['name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Lab Test</label>
                        <select name="lab_test_id" class="form-control">
                            <option value="">Select Lab Test</option>
                            <?php while($lt=$lab_tests->fetch_assoc()) echo "<option value='{$lt['id']}' ".($report['lab_test_id']==$lt['id']?'selected':'').">{$lt['test_name']}</option>"; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Procedure</label>
                        <select name="procedure_id" class="form-control">
                            <option value="">Select Procedure</option>
                            <?php while($pr=$procedures->fetch_assoc()) echo "<option value='{$pr['id']}' ".($report['procedure_id']==$pr['id']?'selected':'').">{$pr['procedure_name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>File</label>
                        <input type="file" name="file_path" class="form-control-file">
                        <small>Current file: <a href="../admin/<?= $report['file_path'] ?>" target="_blank">View</a></small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>File Type</label>
                        <select name="file_type" class="form-control" required>
                            <option value="pdf" <?= $report['file_type']=='pdf'?'selected':'' ?>>PDF</option>
                            <option value="image" <?= $report['file_type']=='image'?'selected':'' ?>>Image</option>
                            <option value="text" <?= $report['file_type']=='text'?'selected':'' ?>>Text</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Uploaded By</label>
                        <input type="text" class="form-control" value="<?= $_SESSION['staff_id'] ?>" disabled>
                    </div>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="doctor_approved" class="form-check-input" id="approvedCheck" <?= $report['doctor_approved']?'checked':'' ?>>
                    <label class="form-check-label" for="approvedCheck">Doctor Approved</label>
                </div>

                <div class="form-group">
                    <label>Doctor Remarks</label>
                    <textarea name="doctor_remarks" class="form-control" rows="3"><?= htmlspecialchars($report['doctor_remarks']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update Report</button>
                <a href="view_lab_reports.php" class="btn btn-dark">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
