<?php
session_start();
include('../config.php');

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

if(!isset($_GET['id'])){
    exit("Invalid request.");
}

$patient_id = intval($_GET['id']);

// Fetch patient info
$patient = $conn->query("SELECT * FROM patients WHERE id=$patient_id")->fetch_assoc();

// Fetch appointments
$appointments = $conn->query("SELECT * FROM appointments WHERE patient_id=$patient_id AND doctor_id=$doctor_id ORDER BY appointment_date DESC");

// Fetch lab reports
$lab_reports = $conn->query("
    SELECT lt.test_name, lt.price, lt.report_status, lr.file_path, lr.file_type, lr.doctor_remarks
    FROM lab_tests lt
    LEFT JOIN lab_reports lr ON lt.id = lr.lab_test_id
    WHERE lt.patient_id=$patient_id AND lt.doctor_id=$doctor_id
    ORDER BY lt.booked_at DESC
");

// Fetch procedures
$procedures = $conn->query("SELECT * FROM procedures WHERE patient_id=$patient_id AND doctor_id=$doctor_id ORDER BY procedure_date DESC");

// Fetch vitals
$vitals = $conn->query("
    SELECT pv.*, s.full_name AS nurse_name
    FROM patient_vitals pv
    LEFT JOIN staff s ON pv.nurse_id=s.id
    WHERE pv.patient_id=$patient_id
    ORDER BY recorded_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Patient Details & Progress</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-4">
    <h3>Patient: <?= htmlspecialchars($patient['name']) ?> (MR#: <?= $patient['mr_number'] ?>)</h3>
    <a href="patients.php" class="btn btn-dark mb-3">Back to My Patients</a>

    <!-- Patient Info -->
    <div class="card mb-3">
        <div class="card-header">Patient Information</div>
        <div class="card-body">
            <p><strong>Phone:</strong> <?= $patient['phone'] ?></p>
            <p><strong>Email:</strong> <?= $patient['email'] ?></p>
            <p><strong>Gender:</strong> <?= $patient['gender'] ?></p>
            <p><strong>Blood Group:</strong> <?= $patient['blood_group'] ?></p>
            <p><strong>Address:</strong> <?= $patient['address'] ?></p>
            <p><strong>Guardian:</strong> <?= $patient['guardian_name'] ?> (<?= $patient['guardian_phone'] ?>)</p>
            <p><strong>Registration Date:</strong> <?= $patient['registration_date'] ?></p>
        </div>
    </div>

    <!-- Appointments -->
    <div class="card mb-3">
        <div class="card-header">Appointments</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr><th>Date</th><th>Time</th><th>Status</th><th>Notes</th></tr>
                </thead>
                <tbody>
                <?php if($appointments->num_rows>0):
                    while($a=$appointments->fetch_assoc()): ?>
                        <tr>
                            <td><?= $a['appointment_date'] ?></td>
                            <td><?= $a['start_time'] ?> - <?= $a['end_time'] ?></td>
                            <td><?= ucfirst($a['status']) ?></td>
                            <td><?= htmlspecialchars($a['notes']) ?></td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr><td colspan="4">No appointments found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Lab Reports -->
    <div class="card mb-3">
        <div class="card-header">Lab Tests & Reports</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr><th>Test Name</th><th>Price</th><th>Status</th><th>Report</th><th>Doctor Remarks</th></tr>
                </thead>
                <tbody>
                <?php if($lab_reports->num_rows>0):
                    while($lr=$lab_reports->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($lr['test_name']) ?></td>
                            <td><?= $lr['price'] ?></td>
                            <td><?= ucfirst($lr['report_status']) ?></td>
                            <td><?php if($lr['file_path']): ?><a href="../<?= $lr['file_path'] ?>" target="_blank">View Report</a><?php endif; ?></td>
                            <td><?= htmlspecialchars($lr['doctor_remarks']) ?></td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr><td colspan="5">No lab tests found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Procedures -->
    <div class="card mb-3">
        <div class="card-header">Procedures</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr><th>Type</th><th>Name</th><th>Nurse</th><th>Date & Time</th><th>Status</th></tr>
                </thead>
                <tbody>
                <?php if($procedures->num_rows>0):
                    while($pr=$procedures->fetch_assoc()): ?>
                        <tr>
                            <td><?= $pr['procedure_type'] ?></td>
                            <td><?= htmlspecialchars($pr['procedure_name']) ?></td>
                            <td><?= $pr['nurse_id'] ?></td>
                            <td><?= $pr['procedure_date'] ?> <?= $pr['procedure_time'] ?></td>
                            <td><?= ucfirst($pr['status']) ?></td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr><td colspan="5">No procedures found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Vitals -->
    <div class="card mb-3">
        <div class="card-header">Patient Vitals</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr><th>Recorded At</th><th>Nurse</th><th>BP</th><th>Temp</th><th>Pulse</th><th>Respiration</th><th>Oxygen</th></tr>
                </thead>
                <tbody>
                <?php if($vitals->num_rows>0):
                    while($v=$vitals->fetch_assoc()): ?>
                        <tr>
                            <td><?= $v['recorded_at'] ?></td>
                            <td><?= $v['nurse_name'] ?></td>
                            <td><?= $v['blood_pressure'] ?></td>
                            <td><?= $v['temperature'] ?></td>
                            <td><?= $v['pulse_rate'] ?></td>
                            <td><?= $v['respiratory_rate'] ?></td>
                            <td><?= $v['oxygen_saturation'] ?></td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr><td colspan="7">No vitals recorded.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
