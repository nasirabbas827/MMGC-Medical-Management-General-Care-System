<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'reception') {
    header("Location: ../login.php");
    exit();
}

/* ====== DASHBOARD COUNTS ====== */

// Total Patients
$patients_count = $conn->query("SELECT COUNT(*) AS total FROM patients")->fetch_assoc()['total'];

// Today's Appointments
$today_appointments = $conn->query("
    SELECT COUNT(*) AS total 
    FROM appointments 
    WHERE appointment_date = CURDATE()
")->fetch_assoc()['total'];

// Pending Appointments
$pending_appointments = $conn->query("
    SELECT COUNT(*) AS total 
    FROM appointments 
    WHERE status IN ('scheduled','confirmed')
")->fetch_assoc()['total'];

// Total Doctors
$doctors_count = $conn->query("SELECT COUNT(*) AS total FROM doctors WHERE status='active'")->fetch_assoc()['total'];

// Unpaid Invoices
$pending_invoices = $conn->query("
    SELECT COUNT(*) AS total 
    FROM invoices 
    WHERE payment_status IN ('pending','partial')
")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reception Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">

    <h3 class="mb-4">Reception Dashboard</h3>

    <!-- ================= STAT CARDS ================= -->
    <div class="row">

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5>Total Patients</h5>
                    <h2><?= $patients_count ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>Today's Appointments</h5>
                    <h2><?= $today_appointments ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5>Pending Appointments</h5>
                    <h2><?= $pending_appointments ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h5>Active Doctors</h5>
                    <h2 class="text-white"><?= $doctors_count ?></h2>
                </div>
            </div>
        </div>

    </div>

    <!-- ================= QUICK ACTIONS ================= -->
    <div class="row mt-4">

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    Reception Tasks
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <a href="reception_view_patients.php">Register New Patient</a>
                    </li>
                    <li class="list-group-item">
                        <a href="reception_view_patients.php">View / Edit Patients</a>
                    </li>
                    <li class="list-group-item">
                        <a href="add_appointment.php">Schedule Appointment</a>
                    </li>
                    <li class="list-group-item">
                        <a href="view_appointments.php">View / Reschedule Appointments</a>
                    </li>
                    <li class="list-group-item">
                        <a href="view_appointments.php">Issue MR Number</a>
                    </li>
                </ul>
            </div>
        </div>

       
    </div>

    <!-- ================= TODAY APPOINTMENTS TABLE ================= -->
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            Today's Appointments
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $today_list = $conn->query("
                    SELECT a.*, p.name AS patient_name, d.full_name AS doctor_name
                    FROM appointments a
                    LEFT JOIN patients p ON a.patient_id = p.id
                    LEFT JOIN doctors d ON a.doctor_id = d.id
                    WHERE a.appointment_date = CURDATE()
                    ORDER BY a.start_time ASC
                ");

                if ($today_list->num_rows > 0):
                    while ($row = $today_list->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= $row['patient_name'] ?></td>
                        <td><?= $row['doctor_name'] ?></td>
                        <td><?= $row['start_time'] ?> - <?= $row['end_time'] ?></td>
                        <td><?= ucfirst($row['status']) ?></td>
                        <td><?= ucfirst($row['appointment_type']) ?></td>
                    </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="5" class="text-center">No appointments today</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
