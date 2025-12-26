<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'accounts') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Accounts Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">
    <h3>Accounts Dashboard</h3>
    <div class="row mt-3">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Invoices</h5>
                    <p class="card-text">Manage all invoices.</p>
                    <a href="view_invoices.php" class="btn btn-light btn-sm">Go</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Payments</h5>
                    <p class="card-text">Record and track payments.</p>
                    <a href="view_payments.php" class="btn btn-light btn-sm">Go</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Financial Reports</h5>
                    <p class="card-text">Generate detailed reports.</p>
                    <a href="financial_reports.php" class="btn btn-light btn-sm">Go</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
