<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'lab') {
    header("Location: ../login.php");
    exit();
}
$staff_id = $_SESSION['staff_id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lab Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">
    <h3>Lab Dashboard</h3>
    <div class="row mt-3">

        <!-- Upload Lab Reports -->
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Upload Lab Reports</h5>
                    <p class="card-text">Upload lab, ultrasound, or pathology reports and tag with patient & doctor.</p>
                    <a href="add_lab_report.php" class="btn btn-light btn-block">Go</a>
                </div>
            </div>
        </div>

        <!-- Pending Tests -->
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pending Tests</h5>
                    <p class="card-text">View and manage test samples that are pending or in progress.</p>
                    <a href="view_lab_tests.php" class="btn btn-light btn-block">Go</a>
                </div>
            </div>
        </div>

        <!-- Completed Tests -->
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Completed Tests</h5>
                    <p class="card-text">Check reports that have been processed and are ready for review/delivery.</p>
                    <a href="view_lab_reports.php" class="btn btn-light btn-block">Go</a>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-4">
        <!-- Book Sample / Lab Test -->
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Book Test Sample</h5>
                    <p class="card-text">Schedule and record new lab tests or procedures for patients.</p>
                    <a href="add_lab_test.php" class="btn btn-light btn-block">Go</a>
                </div>
            </div>
        </div>

        
    </div>

</div>

</body>
</html>
