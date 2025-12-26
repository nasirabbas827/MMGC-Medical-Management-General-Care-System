<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'accounts') {
    header("Location: ../login.php");
    exit();
}

// Date filter
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01'); // first day of current month
$to_date   = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');       // today

// Fetch invoices within date range
$stmt = $conn->prepare("SELECT i.*, p.name AS patient_name 
                        FROM invoices i 
                        LEFT JOIN patients p ON i.patient_id = p.id
                        WHERE i.issued_date BETWEEN ? AND ?
                        ORDER BY i.issued_date DESC");
$stmt->bind_param("ss", $from_date, $to_date);
$stmt->execute();
$result = $stmt->get_result();

// Summary totals
$total_invoiced = 0;
$total_paid = 0;
$total_pending = 0;
$summary_by_type = [];

$invoices = [];
while ($row = $result->fetch_assoc()) {
    $invoices[] = $row;
    $total_invoiced += $row['net_amount'];

    switch ($row['payment_status']) {
        case 'paid':
            $total_paid += $row['net_amount'];
            break;
        case 'partial':
            // fetch payments sum
            $pay_sum = $conn->query("SELECT SUM(amount) as paid FROM payments WHERE invoice_id={$row['id']}")->fetch_assoc()['paid'];
            $total_paid += $pay_sum;
            $total_pending += ($row['net_amount'] - $pay_sum);
            break;
        case 'pending':
        case 'refunded':
            $total_pending += $row['net_amount'];
            break;
    }

    // Summary by type
    if (!isset($summary_by_type[$row['type']])) {
        $summary_by_type[$row['type']] = 0;
    }
    $summary_by_type[$row['type']] += $row['net_amount'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Financial Reports</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">
    <h3>Financial Reports</h3>

    <form class="form-inline mb-3" method="GET">
        <label class="mr-2">From:</label>
        <input type="date" name="from_date" value="<?= $from_date ?>" class="form-control mr-3">

        <label class="mr-2">To:</label>
        <input type="date" name="to_date" value="<?= $to_date ?>" class="form-control mr-3">

        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="financial_reports.php" class="btn btn-secondary ml-2">Reset</a>
    </form>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5>Total Invoiced</h5>
                    <h3>RS: <?= number_format($total_invoiced,2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>Total Paid</h5>
                    <h3>RS: <?= number_format($total_paid,2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5>Total Pending</h5>
                    <h3>RS: <?= number_format($total_pending,2) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <h4>Summary by Type</h4>
    <table class="table table-bordered table-hover mb-4">
        <thead>
            <tr>
                <th>Type</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($summary_by_type as $type => $amount): ?>
            <tr>
                <td><?= ucfirst($type) ?></td>
                <td>RS: <?= number_format($amount,2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4>Invoices List</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Invoice Number</th>
                    <th>Patient</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Total</th>
                    <th>Discount</th>
                    <th>Net Amount</th>
                    <th>Payment Status</th>
                    <th>Issued Date</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($invoices) > 0): ?>
                    <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td><?= $inv['id'] ?></td>
                        <td><?= $inv['invoice_number'] ?></td>
                        <td><?= $inv['patient_name'] ?></td>
                        <td><?= $inv['description'] ?></td>
                        <td><?= ucfirst($inv['type']) ?></td>
                        <td>RS: <?= number_format($inv['total_amount'],2) ?></td>
                        <td>RS: <?= number_format($inv['discount'],2) ?></td>
                        <td>RS: <?= number_format($inv['net_amount'],2) ?></td>
                        <td><?= ucfirst($inv['payment_status']) ?></td>
                        <td><?= $inv['issued_date'] ?></td>
                        <td><?= $inv['due_date'] ?: '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="11" class="text-center">No invoices found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
