<?php
session_start();
include('config.php');

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if(!isset($_GET['id'])){
    exit("Invalid request.");
}

$patient_id = intval($_GET['id']);

// Fetch patient info
$patient = $conn->query("SELECT * FROM patients WHERE id=$patient_id")->fetch_assoc();

// Fetch appointments
$appointments = $conn->query("SELECT a.*, d.full_name AS doctor_name 
                              FROM appointments a 
                              LEFT JOIN doctors d ON a.doctor_id=d.id 
                              WHERE a.patient_id=$patient_id
                              ORDER BY a.appointment_date DESC");

// Fetch lab reports
$lab_reports = $conn->query("SELECT lr.*, lt.test_name, s.full_name AS uploaded_by
                             FROM lab_reports lr
                             LEFT JOIN lab_tests lt ON lr.lab_test_id=lt.id
                             LEFT JOIN staff s ON lr.uploaded_by=s.id
                             WHERE lr.patient_id=$patient_id
                             ORDER BY lr.uploaded_at DESC");

// Fetch procedures
$procedures = $conn->query("SELECT * FROM procedures WHERE patient_id=$patient_id ORDER BY procedure_date DESC");

// Fetch invoices/payments
$invoices = $conn->query("SELECT * FROM invoices WHERE patient_id=$patient_id ORDER BY created_at DESC");

// Fetch vitals
$vitals = $conn->query("SELECT pv.*, s.full_name AS nurse_name 
                        FROM patient_vitals pv 
                        LEFT JOIN staff s ON pv.nurse_id=s.id
                        WHERE pv.patient_id=$patient_id
                        ORDER BY recorded_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Patient Details & Progress</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-4">
    <h3>Patient: <?= htmlspecialchars($patient['name']) ?> (MR#: <?= $patient['mr_number'] ?>)</h3>
    <a href="view_patients.php" class="btn btn-dark mb-3">Back to Patients</a>

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
                    <tr><th>Date</th><th>Time</th><th>Doctor</th><th>Status</th><th>Notes</th></tr>
                </thead>
                <tbody>
                <?php if($appointments->num_rows>0){
                    while($a=$appointments->fetch_assoc()){
                        echo "<tr>
                            <td>{$a['appointment_date']}</td>
                            <td>{$a['start_time']} - {$a['end_time']}</td>
                            <td>{$a['doctor_name']}</td>
                            <td>{$a['status']}</td>
                            <td>{$a['notes']}</td>
                        </tr>";
                    }
                }else{
                    echo "<tr><td colspan='5'>No appointments found.</td></tr>";
                } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Lab Reports -->
    <div class="card mb-3">
        <div class="card-header">Lab Reports</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr><th>Test Name</th><th>Procedure</th><th>File</th><th>Type</th><th>Uploaded By</th><th>Approved</th></tr>
                </thead>
                <tbody>
                <?php if($lab_reports->num_rows>0){
                    while($lr=$lab_reports->fetch_assoc()){
                        echo "<tr>
                            <td>{$lr['test_name']}</td>
                            <td>{$lr['procedure_id']}</td>
                            <td><a href='{$lr['file_path']}' target='_blank'>View File</a></td>
                            <td>{$lr['file_type']}</td>
                            <td>{$lr['uploaded_by']}</td>
                            <td>".($lr['doctor_approved']?'Yes':'No')."</td>
                        </tr>";
                    }
                }else{
                    echo "<tr><td colspan='6'>No lab reports found.</td></tr>";
                } ?>
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
                    <tr><th>Procedure Type</th><th>Name</th><th>Doctor</th><th>Nurse</th><th>Date & Time</th><th>Status</th></tr>
                </thead>
                <tbody>
                <?php if($procedures->num_rows>0){
                    while($pr=$procedures->fetch_assoc()){
                        echo "<tr>
                            <td>{$pr['procedure_type']}</td>
                            <td>{$pr['procedure_name']}</td>
                            <td>{$pr['doctor_id']}</td>
                            <td>{$pr['nurse_id']}</td>
                            <td>{$pr['procedure_date']} {$pr['procedure_time']}</td>
                            <td>{$pr['status']}</td>
                        </tr>";
                    }
                }else{
                    echo "<tr><td colspan='6'>No procedures found.</td></tr>";
                } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Patient Vitals -->
    <div class="card mb-3">
        <div class="card-header">Vitals</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr><th>Recorded At</th><th>Nurse</th><th>BP</th><th>Temp</th><th>Pulse</th><th>Respiration</th><th>Oxygen</th></tr>
                </thead>
                <tbody>
                <?php if($vitals->num_rows>0){
                    while($v=$vitals->fetch_assoc()){
                        echo "<tr>
                            <td>{$v['recorded_at']}</td>
                            <td>{$v['nurse_name']}</td>
                            <td>{$v['blood_pressure']}</td>
                            <td>{$v['temperature']}</td>
                            <td>{$v['pulse_rate']}</td>
                            <td>{$v['respiratory_rate']}</td>
                            <td>{$v['oxygen_saturation']}</td>
                        </tr>";
                    }
                }else{
                    echo "<tr><td colspan='7'>No vitals recorded.</td></tr>";
                } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Invoices/Payments -->
    <div class="card mb-3">
        <div class="card-header">Invoices & Payments</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr><th>Invoice #</th><th>Description</th><th>Type</th><th>Total</th><th>Net Amount</th><th>Payment Status</th><th>Issued Date</th></tr>
                </thead>
                <tbody>
                <?php if($invoices->num_rows>0){
                    while($inv=$invoices->fetch_assoc()){
                        echo "<tr>
                            <td>{$inv['invoice_number']}</td>
                            <td>{$inv['description']}</td>
                            <td>{$inv['type']}</td>
                            <td>{$inv['total_amount']}</td>
                            <td>{$inv['net_amount']}</td>
                            <td>{$inv['payment_status']}</td>
                            <td>{$inv['issued_date']}</td>
                        </tr>";
                    }
                }else{
                    echo "<tr><td colspan='7'>No invoices found.</td></tr>";
                } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
