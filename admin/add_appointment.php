<?php
session_start();
include('config.php');

if(!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin"){
    header("Location: admin_login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $status = $_POST['status'];
    $appointment_type = $_POST['appointment_type'];
    $notes = trim($_POST['notes']);

    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, start_time, end_time, status, appointment_type, notes) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("iissssss", $patient_id, $doctor_id, $appointment_date, $start_time, $end_time, $status, $appointment_type, $notes);

    if($stmt->execute()){
        $success = "Appointment added successfully.";
    } else {
        $error = "Error: ".$conn->error;
    }
}

// Fetch patients and doctors
$patients = $conn->query("SELECT id, name FROM patients ORDER BY name");
$doctors = $conn->query("SELECT id, full_name FROM doctors ORDER BY full_name");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Appointment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-4">
    <div class="card mx-auto" style="max-width:600px;">
        <div class="card-body">
            <h3>Add Appointment</h3>
            <?php if(isset($success)) echo "<p class='text-success'>$success</p>"; ?>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Patient</label>
                        <select name="patient_id" class="form-control" required>
                            <option value="">Select Patient</option>
                            <?php while($p=$patients->fetch_assoc()){ ?>
                                <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Doctor</label>
                        <select name="doctor_id" class="form-control" required>
                            <option value="">Select Doctor</option>
                            <?php while($d=$doctors->fetch_assoc()){ ?>
                                <option value="<?= $d['id'] ?>"><?= $d['full_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Appointment Date</label>
                        <input type="date" name="appointment_date" class="form-control" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Start Time</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>End Time</label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="scheduled">Scheduled</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="no-show">No-show</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Type</label>
                        <select name="appointment_type" class="form-control">
                            <option value="new">New</option>
                            <option value="follow-up">Follow-up</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Add Appointment</button>
                <a href="view_appointments.php" class="btn btn-dark">View Appointments</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
