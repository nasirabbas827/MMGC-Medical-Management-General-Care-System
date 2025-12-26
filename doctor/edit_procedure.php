<?php
session_start();
include('../config.php');

if(!isset($_SESSION['doctor_id']) || $_SESSION['role']!=='doctor'){
    header("Location: ../login.php");
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

if(!isset($_GET['id'])){
    echo "Invalid request."; exit;
}
$id=intval($_GET['id']);

$q=$conn->prepare("SELECT * FROM procedures WHERE id=? AND doctor_id=?");
$q->bind_param("ii",$id,$doctor_id);
$q->execute();
$res=$q->get_result();
if($res->num_rows!=1){ echo "Procedure not found."; exit; }
$proc=$res->fetch_assoc();

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

    $u=$conn->prepare("UPDATE procedures SET patient_id=?, procedure_type=?, procedure_name=?, nurse_id=?, procedure_date=?, procedure_time=?, status=?, treatment_notes=?, prescription=?, outcome=? WHERE id=? AND doctor_id=?");
    $u->bind_param("ississssssii",$patient_id,$procedure_type,$procedure_name,$nurse_id,$procedure_date,$procedure_time,$status,$treatment_notes,$prescription,$outcome,$id,$doctor_id);
    if($u->execute()){
        echo "<script>alert('Procedure updated successfully.'); window.location='view_procedures.php';</script>";
    }else{
        $error="Update failed: ".$conn->error;
    }
}

$patients = $conn->query("SELECT DISTINCT p.id, p.name 
                          FROM patients p 
                          JOIN appointments a ON p.id = a.patient_id 
                          WHERE a.doctor_id = $doctor_id 
                          ORDER BY p.name ASC");

$nurses = $conn->query("SELECT id, full_name FROM staff WHERE staff_role='nurse' ORDER BY full_name ASC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Procedure</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
<div class="card mx-auto" style="max-width:700px;">
<div class="card-body">
<h3>Edit Procedure</h3>
<?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

<form method="POST">
<div class="form-row">
    <div class="form-group col-md-6">
        <label>Patient</label>
        <select name="patient_id" class="form-control" required>
            <?php while($p=$patients->fetch_assoc()){ 
                $sel=$proc['patient_id']==$p['id']?'selected':''; 
                echo "<option value='{$p['id']}' $sel>{$p['name']}</option>"; 
            } ?>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label>Nurse</label>
        <select name="nurse_id" class="form-control">
            <option value="">Select Nurse (optional)</option>
            <?php while($n=$nurses->fetch_assoc()){
                $sel=$proc['nurse_id']==$n['id']?'selected':''; 
                echo "<option value='{$n['id']}' $sel>{$n['full_name']}</option>";
            } ?>
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>Procedure Type</label>
        <select name="procedure_type" class="form-control" required>
            <?php
            $types=['Normal Delivery','C-Section','Ultrasound','Gynaecology Treatment','Surgery','OPD Consultation','IPD Admission','Other'];
            foreach($types as $t){
                $sel=$proc['procedure_type']==$t?'selected':''; 
                echo "<option $sel>$t</option>";
            }
            ?>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label>Procedure Name</label>
        <input type="text" name="procedure_name" class="form-control" value="<?= $proc['procedure_name'] ?>" required>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-3">
        <label>Date</label>
        <input type="date" name="procedure_date" class="form-control" value="<?= $proc['procedure_date'] ?>" required>
    </div>
    <div class="form-group col-md-3">
        <label>Time</label>
        <input type="time" name="procedure_time" class="form-control" value="<?= $proc['procedure_time'] ?>">
    </div>
    <div class="form-group col-md-6">
        <label>Status</label>
        <select name="status" class="form-control">
            <?php
            $statuses=['planned','in-progress','completed','cancelled'];
            foreach($statuses as $s){
                $sel=$proc['status']==$s?'selected':''; 
                echo "<option $sel>$s</option>";
            }
            ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label>Treatment Notes</label>
    <textarea name="treatment_notes" class="form-control" rows="3"><?= $proc['treatment_notes'] ?></textarea>
</div>

<div class="form-group">
    <label>Prescription</label>
    <textarea name="prescription" class="form-control" rows="2"><?= $proc['prescription'] ?></textarea>
</div>

<div class="form-group">
    <label>Outcome</label>
    <input type="text" name="outcome" class="form-control" value="<?= $proc['outcome'] ?>">
</div>

<button type="submit" class="btn btn-primary">Update Procedure</button>
<a href="view_procedures.php" class="btn btn-dark">Cancel</a>
</form>
</div></div></div>
</body>
</html>
