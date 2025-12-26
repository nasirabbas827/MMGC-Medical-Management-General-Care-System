<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'reception') {
    header("Location: ../login.php");
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';



if(!isset($_GET['id'])){
    echo "Invalid request."; 
    exit;
}

$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM appointments WHERE id=$id");
if($res->num_rows!=1){ 
    echo "Appointment not found."; 
    exit;
}

$app = $res->fetch_assoc();

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $status = $_POST['status'];
    $appointment_type = $_POST['appointment_type'];
    $notes = trim($_POST['notes']);

    $stmt = $conn->prepare("UPDATE appointments SET patient_id=?, doctor_id=?, appointment_date=?, start_time=?, end_time=?, status=?, appointment_type=?, notes=? WHERE id=?");
    $stmt->bind_param("iissssssi", $patient_id, $doctor_id, $appointment_date, $start_time, $end_time, $status, $appointment_type, $notes, $id);

    if($stmt->execute()){

        // Fetch updated patient info
        $p = $conn->query("SELECT name, email FROM patients WHERE id=$patient_id")->fetch_assoc();
        $patient_name = $p['name'];
        $patient_email = $p['email'];

        // Fetch updated doctor info
        $d = $conn->query("SELECT full_name, email FROM doctors WHERE id=$doctor_id")->fetch_assoc();
        $doctor_name = $d['full_name'];
        $doctor_email = $d['email'];

        // Email subject and body
        $subject = "Appointment Updated";

        $body = "
        Appointment Updated:
        -----------------------------------
        Patient: $patient_name
        Doctor: $doctor_name
        Appointment Date: $appointment_date
        Start Time: $start_time
        End Time: $end_time
        Status: $status
        Type: $appointment_type
        Notes: $notes
        -----------------------------------
        ";

        // Send email to patient and doctor
        sendMail($patient_email, $subject, $body);
        sendMail($doctor_email, $subject, $body);

        echo "<script>alert('Appointment updated successfully. Email notifications sent.'); window.location='view_appointments.php';</script>";
        exit;

    } else {
        $error="Update failed: ".$conn->error;
    }
}

$patients = $conn->query("SELECT id,name FROM patients ORDER BY name");
$doctors = $conn->query("SELECT id,full_name FROM doctors ORDER BY full_name");

function sendMail($to, $subject, $body){
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nasiryt.827@gmail.com';  
        $mail->Password   = 'ddgc zhft gohg bjmz';    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('nasiryt.827@gmail.com', 'MMGC-Clinic');
        $mail->addAddress($to);

        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();

    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
<div class="card mx-auto" style="max-width:600px;">
<div class="card-body">
<h3>Edit Appointment</h3>
<?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

<form method="POST">

<div class="form-row">
    <div class="form-group col-md-6">
        <label>Patient</label>
        <select name="patient_id" class="form-control" required>
            <?php while($p=$patients->fetch_assoc()){ ?>
                <option value="<?= $p['id'] ?>" <?= $app['patient_id']==$p['id']?'selected':'' ?>>
                    <?= $p['name'] ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>Doctor</label>
        <select name="doctor_id" class="form-control" required>
            <?php while($d=$doctors->fetch_assoc()){ ?>
                <option value="<?= $d['id'] ?>" <?= $app['doctor_id']==$d['id']?'selected':'' ?>>
                    <?= $d['full_name'] ?>
                </option>
            <?php } ?>
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label>Date</label>
        <input type="date" name="appointment_date" class="form-control" 
               value="<?= $app['appointment_date'] ?>" required>
    </div>
    <div class="form-group col-md-4">
        <label>Start Time</label>
        <input type="time" name="start_time" class="form-control" 
               value="<?= $app['start_time'] ?>" required>
    </div>
    <div class="form-group col-md-4">
        <label>End Time</label>
        <input type="time" name="end_time" class="form-control" 
               value="<?= $app['end_time'] ?>" required>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="scheduled" <?= $app['status']=='scheduled'?'selected':'' ?>>Scheduled</option>
            <option value="confirmed" <?= $app['status']=='confirmed'?'selected':'' ?>>Confirmed</option>
            <option value="completed" <?= $app['status']=='completed'?'selected':'' ?>>Completed</option>
            <option value="cancelled" <?= $app['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
            <option value="no-show" <?= $app['status']=='no-show'?'selected':'' ?>>No-show</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>Type</label>
        <select name="appointment_type" class="form-control">
            <option value="new" <?= $app['appointment_type']=='new'?'selected':'' ?>>New</option>
            <option value="follow-up" <?= $app['appointment_type']=='follow-up'?'selected':'' ?>>Follow-up</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label>Notes</label>
    <textarea name="notes" class="form-control" rows="3"><?= $app['notes'] ?></textarea>
</div>

<button type="submit" class="btn btn-primary">Update Appointment</button>
<a href="view_appointments.php" class="btn btn-dark">View Appointments</a>

</form>
</div>
</div>
</div>
</body>
</html>
