<?php
session_start();
require_once('../config.php');

/* =====================
   AUTH GUARD
   ===================== */
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

/* =====================
   FETCH DOCTOR NAME FOR WELCOME
   ===================== */
$stmt = $conn->prepare("SELECT full_name FROM doctors WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$doctor_result = $stmt->get_result();
$doctor_name = $doctor_result->fetch_assoc()['full_name'] ?? 'Doctor';
$stmt->close();

/* =====================
   DASHBOARD COUNTS (Safe Queries)
   ===================== */

// Total Appointments
$total_appointments = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE doctor_id = $doctor_id")->fetch_assoc()['total'];

// Total Unique Patients
$total_patients = $conn->query("SELECT COUNT(DISTINCT patient_id) AS total FROM appointments WHERE doctor_id = $doctor_id")->fetch_assoc()['total'];

// Completed Procedures
$procedures_count = $conn->query("SELECT COUNT(*) AS total FROM procedures WHERE doctor_id = $doctor_id AND status = 'completed'")->fetch_assoc()['total'];

// Pending Lab Reports
$pending_reports = $conn->query("SELECT COUNT(*) AS total 
                                 FROM lab_reports lr
                                 JOIN lab_tests lt ON lt.id = lr.lab_test_id
                                 WHERE lt.doctor_id = $doctor_id AND lr.doctor_approved = 0")->fetch_assoc()['total'];

/* =====================
   TODAY'S APPOINTMENTS (Fixed & Secure)
   ===================== */
$today = date('Y-m-d'); // e.g., 2025-12-25

$stmt_today = $conn->prepare("SELECT a.start_time, a.end_time, a.status, a.appointment_type,
                                     p.name AS patient_name, p.mr_number
                              FROM appointments a
                              JOIN patients p ON a.patient_id = p.id
                              WHERE a.doctor_id = ? AND a.appointment_date = ?
                              ORDER BY a.start_time ASC");

$stmt_today->bind_param("is", $doctor_id, $today);
$stmt_today->execute();
$today_appointments = $stmt_today->get_result();

/* =====================
   UPCOMING PROCEDURES (Safe Query)
   ===================== */
$stmt_upcoming = $conn->prepare("SELECT pr.procedure_type, pr.procedure_name, pr.procedure_date, pr.status,
                                        p.name AS patient_name
                                 FROM procedures pr
                                 JOIN patients p ON pr.patient_id = p.id
                                 WHERE pr.doctor_id = ? AND pr.status != 'completed'
                                 ORDER BY pr.procedure_date ASC, pr.procedure_time ASC
                                 LIMIT 5");

$stmt_upcoming->bind_param("i", $doctor_id);
$stmt_upcoming->execute();
$upcoming_procedures = $stmt_upcoming->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard | HealthCareHub</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        .card { box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: none; }
        .border-left-info { border-left: 5px solid #17a2b8 !important; }
        .border-left-success { border-left: 5px solid #28a745 !important; }
        .border-left-warning { border-left: 5px solid #ffc107 !important; }
        .border-left-danger { border-left: 5px solid #dc3545 !important; }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container-fluid mt-4">
    <h3 class="mb-4">Welcome back, Dr. <?php echo htmlspecialchars($doctor_name); ?> 👋</h3>

    <!-- Overview Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center border-0 border-left-info">
                <div class="card-body">
                    <i class="fas fa-calendar-alt fa-2x mb-2 text-info"></i>
                    <h5 class="text-muted">Total Appointments</h5>
                    <h3><?php echo $total_appointments; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center border-0 border-left-success">
                <div class="card-body">
                    <i class="fas fa-user-injured fa-2x mb-2 text-success"></i>
                    <h5 class="text-muted">My Patients</h5>
                    <h3><?php echo $total_patients; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center border-0 border-left-warning">
                <div class="card-body">
                    <i class="fas fa-stethoscope fa-2x mb-2 text-warning"></i>
                    <h5 class="text-muted">Procedures Done</h5>
                    <h3><?php echo $procedures_count; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center border-0 border-left-danger">
                <div class="card-body">
                    <i class="fas fa-file-medical-alt fa-2x mb-2 text-danger"></i>
                    <h5 class="text-muted">Pending Lab Reports</h5>
                    <h3><?php echo $pending_reports; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Today's Appointments -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Today's Appointments (<?php echo date('d M Y'); ?>)</h5>
                    <a href="appointments.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if ($today_appointments->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Patient</th>
                                        <th>MR No.</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($app = $today_appointments->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($app['patient_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($app['mr_number']); ?></td>
                                        <td><?php echo date('h:i A', strtotime($app['start_time'])); ?> - <?php echo date('h:i A', strtotime($app['end_time'])); ?></td>
                                        <td>
                                            <span class="badge badge-pill badge-<?php 
                                                echo $app['status'] == 'confirmed' ? 'success' : 
                                                    ($app['status'] == 'completed' ? 'info' : 'secondary'); 
                                            ?>">
                                                <?php echo ucfirst(str_replace('-', ' ', $app['status'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="far fa-calendar-times fa-4x text-muted mb-3"></i>
                            <p class="text-muted">No appointments scheduled for today.<br>Enjoy your day!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Upcoming Procedures -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Upcoming Procedures</h5>
                    <a href="view_procedures.php" class="btn btn-sm btn-outline-warning">Manage</a>
                </div>
                <div class="card-body">
                    <?php if ($upcoming_procedures->num_rows > 0): ?>
                        <ul class="list-group list-group-flush">
                            <?php while ($proc = $upcoming_procedures->fetch_assoc()): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($proc['procedure_type']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($proc['procedure_name']); ?> - 
                                            Patient: <?php echo htmlspecialchars($proc['patient_name']); ?>
                                        </small>
                                    </div>
                                    <div class="text-right">
                                        <small class="d-block font-weight-bold">
                                            <?php echo date('d M Y', strtotime($proc['procedure_date'])); ?>
                                        </small>
                                        <span class="badge badge-pill badge-warning">
                                            <?php echo ucfirst($proc['status']); ?>
                                        </span>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-notes-medical fa-4x text-muted mb-3"></i>
                            <p class="text-muted">No upcoming procedures scheduled.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body d-flex flex-wrap justify-content-around text-center py-4">
                    <a href="view_lab_reports_doctor.php" class="text-decoration-none text-dark mx-4 my-2">
                        <i class="fas fa-file-signature fa-2x mb-2 text-primary"></i><br>
                        <span>Approve Lab Reports</span>
                    </a>
                    <a href="patients.php" class="text-decoration-none text-dark mx-4 my-2">
                        <i class="fas fa-users fa-2x mb-2 text-primary"></i><br>
                        <span>My Patients</span>
                    </a>
                    <a href="view_procedures.php" class="text-decoration-none text-dark mx-4 my-2">
                        <i class="fas fa-procedures fa-2x mb-2 text-primary"></i><br>
                        <span>Procedures</span>
                    </a>
                    <a href="profile.php" class="text-decoration-none text-dark mx-4 my-2">
                        <i class="fas fa-user-cog fa-2x mb-2 text-primary"></i><br>
                        <span>Profile Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>