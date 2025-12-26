<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'nurse') {
    header("Location: ../login.php");
    exit();
}

$nurse_id = $_SESSION['staff_id'];

$name = $conn->query(
    "SELECT full_name FROM staff WHERE id = $nurse_id"
)->fetch_assoc()['full_name'] ?? 'Nurse';
?>
<!DOCTYPE html>
<html>
<head>
<title>Nurse Dashboard</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">
    <h3>Welcome Nurse <?php echo htmlspecialchars($name); ?></h3>

    <div class="row mt-4">

        <!-- Assist Procedures Card -->
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h5>Assist Procedures</h5>
                    <p>View & update assigned procedures</p>
                    <a href="view_procedures.php" class="btn btn-dark btn-sm">Open</a>
                </div>
            </div>
        </div>

        <!-- Patients Card -->
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h5>Patients</h5>
                    <p>View all patients and add progress reports</p>
                    <a href="patients.php" class="btn btn-success btn-sm">Open</a>
                </div>
            </div>
        </div>

        <!-- Progress Reports Card -->
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h5>Progress Reports</h5>
                    <p>View and manage your submitted reports</p>
                    <a href="add_progress.php" class="btn btn-info btn-sm">Open</a>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-3">
        <!-- Additional Card: Vitals Monitoring -->
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h5>Vitals Monitoring</h5>
                    <p>Quick access to patient vitals</p>
                    <a href="add_progress.php" class="btn btn-warning btn-sm">Open</a>
                </div>
            </div>
        </div>

        

    </div>

</div>

</body>
</html>
