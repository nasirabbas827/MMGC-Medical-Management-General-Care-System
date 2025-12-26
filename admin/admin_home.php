<?php
include('config.php');
session_start();
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch Stats
$total_patients = $conn->query("SELECT COUNT(*) as total FROM patients")->fetch_assoc()['total'];
$total_doctors = $conn->query("SELECT COUNT(*) as total FROM doctors")->fetch_assoc()['total'];
$total_appointments = $conn->query("SELECT COUNT(*) as total FROM appointments")->fetch_assoc()['total'];
$total_revenue = $conn->query("SELECT SUM(net_amount) as total FROM invoices WHERE payment_status='paid'")->fetch_assoc()['total'];

// Fetch Recent Appointments
$recent_appointments = $conn->query("SELECT a.*, p.name as patient_name, d.full_name as doctor_name 
                                     FROM appointments a 
                                     JOIN patients p ON a.patient_id=p.id 
                                     JOIN doctors d ON a.doctor_id=d.id 
                                     ORDER BY a.appointment_date DESC, a.start_time DESC 
                                     LIMIT 5");

// Fetch Recent Patients
$recent_patients = $conn->query("SELECT * FROM patients ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | HealthCareHub</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container-fluid mt-4">
    <!-- Overview Cards -->
    <div class="row mb-4 rad">
        <div class="col-md-3 mb-3">
            <div class="card shadow text-center">
                <div class="card-body">
                    <i class="fas fa-user-injured fa-2x mb-2"></i>
                    <h5>Patients</h5>
                    <h3><?php echo $total_patients; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow text-center">
                <div class="card-body">
                    <i class="fas fa-user-md fa-2x mb-2"></i>
                    <h5>Doctors</h5>
                    <h3><?php echo $total_doctors; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow text-center">
                <div class="card-body">
                    <i class="fas fa-calendar-check fa-2x mb-2"></i>
                    <h5>Appointments</h5>
                    <h3><?php echo $total_appointments; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow text-center">
                <div class="card-body">
                    <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i>
                    <h5>Revenue</h5>
                    <h3>Rs: <?php echo number_format($total_revenue, 2); ?></h3>
                </div>
            </div>
        </div>
    </div>


    <!-- Daily Appointments -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header"><h5>Today's Appointments</h5></div>
                <div class="card-body">
                    <?php if($recent_appointments->num_rows > 0): ?>
                        <ul class="list-group">
                            <?php while($app = $recent_appointments->fetch_assoc()): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?php echo htmlspecialchars($app['patient_name']); ?> with Dr. <?php echo htmlspecialchars($app['doctor_name']); ?> (<?php echo ucfirst($app['status']); ?>)</span>
                                    <span><?php echo date('h:i A', strtotime($app['start_time'])); ?> - <?php echo date('h:i A', strtotime($app['end_time'])); ?></span>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-secondary text-center">No appointments today.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Patients -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header"><h5>Recent Patients</h5></div>
                <div class="card-body">
                    <?php if($recent_patients->num_rows > 0): ?>
                        <ul class="list-group">
                            <?php while($p = $recent_patients->fetch_assoc()): ?>
                                <li class="list-group-item">
                                    <?php echo htmlspecialchars($p['name']); ?> (<?php echo htmlspecialchars($p['mr_number']); ?>) - <?php echo ucfirst($p['status']); ?>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-secondary text-center">No recent patients.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

 
</body>
</html>

