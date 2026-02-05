<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['patient_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

// 1. Fetch Patient Info & Latest Vitals
$stmt = $conn->prepare("
    SELECT p.*, v.blood_pressure, v.temperature, v.oxygen_saturation, v.pulse_rate, v.recorded_at 
    FROM patients p 
    LEFT JOIN patient_vitals v ON p.id = v.patient_id 
    WHERE p.id = ? 
    ORDER BY v.recorded_at DESC LIMIT 1
");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

// 2. Fetch Recent Appointments
$appointments = $conn->query("
    SELECT a.*, d.full_name as doctor_name, d.specialization 
    FROM appointments a 
    JOIN doctors d ON a.doctor_id = d.id 
    WHERE a.patient_id = $patient_id 
    ORDER BY a.appointment_date DESC LIMIT 5
");

// 3. Fetch Recent Procedures (C-Section, Delivery, etc.)
$procedures = $conn->query("
    SELECT * FROM procedures 
    WHERE patient_id = $patient_id 
    ORDER BY procedure_date DESC LIMIT 3
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard | MMGC Hospital</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">

    
    <style>
        
        /* Dashboard Header Cards */
        .vital-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            transition: transform 0.3s ease;
        }
        .vital-card:hover { transform: translateY(-5px); }
        .icon-box {
            width: 56px;
            height: 56px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
        }
        .bg-bp { background: #eef2ff; color: #4318ff; }
        .bg-temp { background: #fff5f5; color: #ff5b5b; }
        .bg-oxy { background: #e6fffa; color: #05cd99; }
        .bg-pulse { background: #fffaf0; color: #ffa800; }

        /* Content Sections */
        .glass-card {
            background: white;
            border-radius: 20px;
            border: none;
            padding: 25px;
            height: 100%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        }
        
        .table thead th {
            background: #f8f9fa;
            border: none;
            color: #a3aed0;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }
        
        .status-pill {
            padding: 6px 16px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
        }
        .status-scheduled { background: #e9edfe; color: #4318ff; }
        .status-completed { background: #e6fffa; color: #05cd99; }
        
        .section-title { font-weight: 700; font-size: 20px; margin-bottom: 20px; }
        
        .btn-action {
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            transition: 0.3s;
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container py-5">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h2 class="font-weight-bold mb-1">Welcome back, <?= htmlspecialchars($patient['name']) ?></h2>
            <p class="text-muted small">MR Number: <span class="font-weight-bold"><?= $patient['mr_number'] ?></span> | Last Checkup: <?= $patient['recorded_at'] ? date('d M, Y', strtotime($patient['recorded_at'])) : 'No data' ?></p>
        </div>
        <div class="col-md-4 text-md-right">
            <a href="my_bookings.php" class="btn btn-primary btn-action shadow-sm">
                <i class="fas fa-plus mr-2"></i> New Appointment
            </a>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-3 mb-3">
            <div class="vital-card">
                <div class="icon-box bg-bp"><i class="fas fa-heart"></i></div>
                <div>
                    <p class="text-muted mb-0 small">Blood Pressure</p>
                    <h4 class="font-weight-bold mb-0"><?= $patient['blood_pressure'] ?? '120/80' ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="vital-card">
                <div class="icon-box bg-temp"><i class="fas fa-thermometer-half"></i></div>
                <div>
                    <p class="text-muted mb-0 small">Body Temp</p>
                    <h4 class="font-weight-bold mb-0"><?= $patient['temperature'] ?? '98.6' ?>°F</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="vital-card">
                <div class="icon-box bg-oxy"><i class="fas fa-lungs"></i></div>
                <div>
                    <p class="text-muted mb-0 small">Oxygen Level</p>
                    <h4 class="font-weight-bold mb-0"><?= $patient['oxygen_saturation'] ?? '98' ?>%</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="vital-card">
                <div class="icon-box bg-pulse"><i class="fas fa-tint"></i></div>
                <div>
                    <p class="text-muted mb-0 small">Blood Group</p>
                    <h4 class="font-weight-bold mb-0"><?= $patient['blood_group'] ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="glass-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="section-title mb-0">Recent Appointments</h5>
                    <a href="appointments_history.php" class="btn btn-link btn-sm text-primary font-weight-bold">See All</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Date / Time</th>
                                <th>Status</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($appointments->num_rows > 0): ?>
                                <?php while($app = $appointments->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="font-weight-bold"><?= $app['doctor_name'] ?></div>
                                        <div class="text-muted small"><?= $app['specialization'] ?></div>
                                    </td>
                                    <td>
                                        <div class="small font-weight-bold"><?= date('D, d M Y', strtotime($app['appointment_date'])) ?></div>
                                        <div class="text-muted small"><?= date('h:i A', strtotime($app['start_time'])) ?></div>
                                    </td>
                                    <td>
                                        <span class="status-pill status-<?= $app['status'] ?>">
                                            <?= ucfirst($app['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small"><?= ucfirst($app['appointment_type']) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-4">No appointments found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="glass-card">
                <h5 class="section-title">Medical Procedures</h5>
                <p class="text-muted small mb-4">Your history of surgeries and hospital treatments.</p>
                
                <?php if($procedures->num_rows > 0): ?>
                    <?php while($proc = $procedures->fetch_assoc()): ?>
                    <div class="p-3 mb-3 border rounded" style="background: #fafbff;">
                        <h6 class="font-weight-bold mb-1 text-primary"><?= $proc['procedure_type'] ?></h6>
                        <div class="small text-dark mb-1 font-weight-bold"><?= $proc['procedure_name'] ?></div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="text-muted small"><i class="far fa-calendar-alt mr-1"></i> <?= date('d M, Y', strtotime($proc['procedure_date'])) ?></span>
                            <span class="badge badge-light border"><?= ucfirst($proc['status']) ?></span>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-notes-medical fa-3x text-light mb-3"></i>
                        <p class="text-muted small">No surgery or procedure history found.</p>
                    </div>
                <?php endif; ?>

                <hr>
                <a href="downloads.php" class="btn btn-outline-primary btn-block btn-action mt-3">
                    <i class="fas fa-download mr-2"></i> Download Reports
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>