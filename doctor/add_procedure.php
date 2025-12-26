<?php
session_start();
include('../config.php');

/* =====================
   AUTH GUARD
   ===================== */
if(!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'doctor'){
    header("Location: ../login.php");
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

/* =====================
   ADD PROCEDURE
   ===================== */
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $patient_id = $_POST['patient_id'];
    $procedure_type = $_POST['procedure_type'];
    $procedure_name = $_POST['procedure_name'];
    $nurse_id = $_POST['nurse_id'] ?: NULL;
    $procedure_date = $_POST['procedure_date'];
    $procedure_time = $_POST['procedure_time'];
    $status = $_POST['status'];
    $treatment_notes = $_POST['treatment_notes'];
    $prescription = $_POST['prescription'];
    $outcome = $_POST['outcome'];

    $stmt = $conn->prepare("INSERT INTO procedures 
        (patient_id, procedure_type, procedure_name, doctor_id, nurse_id, procedure_date, procedure_time, status, treatment_notes, prescription, outcome)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ississsssss",$patient_id,$procedure_type,$procedure_name,$doctor_id,$nurse_id,$procedure_date,$procedure_time,$status,$treatment_notes,$prescription,$outcome);

    if($stmt->execute()){
        $success="Procedure added successfully.";
    } else {
        $error="Error: ".$conn->error;
    }
}

/* =====================
   FETCH PATIENTS ASSIGNED TO THIS DOCTOR
   ===================== */
$patients = $conn->query("SELECT DISTINCT p.id, p.name 
                          FROM patients p 
                          JOIN appointments a ON p.id = a.patient_id 
                          WHERE a.doctor_id = $doctor_id 
                          ORDER BY p.name ASC");

/* =====================
   FETCH NURSES
   ===================== */
$nurses = $conn->query("SELECT id, full_name FROM staff WHERE staff_role='nurse' ORDER BY full_name ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Procedure</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-4">
<div class="card mx-auto" style="max-width:700px;">
<div class="card-body">
<h3>Add Procedure</h3>
<?php if(isset($success)) echo "<p class='text-success'>$success</p>"; ?>
<?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

<form method="POST">
<div class="form-row">
    <div class="form-group col-md-6">
        <label>Patient</label>
        <select name="patient_id" class="form-control" required>
            <option value="">Select Patient</option>
            <?php while($p=$patients->fetch_assoc()){ echo "<option value='{$p['id']}'>{$p['name']}</option>"; } ?>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label>Nurse</label>
        <select name="nurse_id" class="form-control">
            <option value="">Select Nurse (optional)</option>
            <?php while($n=$nurses->fetch_assoc()){ echo "<option value='{$n['id']}'>{$n['full_name']}</option>"; } ?>
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>Procedure Type</label>
        <select name="procedure_type" class="form-control" required>
            <option value="">Select Type</option>
            <option>Normal Delivery</option>
            <option>C-Section</option>
            <option>Ultrasound</option>
            <option>Gynaecology Treatment</option>
            <option>Surgery</option>
            <option>OPD Consultation</option>
            <option>IPD Admission</option>
            <option>Other</option>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label>Procedure Name</label>
        <input type="text" name="procedure_name" class="form-control" required>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-3">
        <label>Date</label>
        <input type="date" name="procedure_date" class="form-control" required>
    </div>
    <div class="form-group col-md-3">
        <label>Time</label>
        <input type="time" name="procedure_time" class="form-control">
    </div>
    <div class="form-group col-md-6">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="planned">Planned</option>
            <option value="in-progress">In-progress</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label>Treatment Notes</label>
    <textarea name="treatment_notes" class="form-control" rows="3"></textarea>
</div>

<div class="form-group">
    <label>Prescription</label>
    <textarea name="prescription" class="form-control" rows="2"></textarea>
</div>

<div class="form-group">
    <label>Outcome</label>
    <input type="text" name="outcome" class="form-control">
</div>

<button type="submit" class="btn btn-primary">Add Procedure</button>
<a href="view_procedures.php" class="btn btn-dark">View Procedures</a>
</form>

</div></div></div>
</body>
</html>
