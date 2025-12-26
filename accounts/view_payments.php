<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'accounts') {
    header("Location: ../login.php");
    exit();
}

if(isset($_GET['delete_id'])){
    $id=intval($_GET['delete_id']);
    $stmt=$conn->prepare("DELETE FROM payments WHERE id=?");
    $stmt->bind_param("i",$id);
    if($stmt->execute()){
        echo "<script>alert('Payment deleted.'); window.location='view_payments.php';</script>";
    }
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT p.*, pat.name AS patient_name, i.invoice_number FROM payments p 
          LEFT JOIN patients pat ON p.patient_id=pat.id
          LEFT JOIN invoices i ON p.invoice_id=i.id";

if(!empty($search)){
    $query .= " WHERE pat.name LIKE '%$search%' OR i.invoice_number LIKE '%$search%'";
}

$query .= " ORDER BY p.id DESC";
$res=$conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Payments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css"> 

</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
    <a href="add_payment.php" class="btn btn-dark float-right">Add Payment</a>
    <h3>Payments</h3>

    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary">Search</button>
        <a href="view_payments.php" class="btn btn-secondary ml-2">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Invoice</th>
                    <th>Patient</th>
                    <th>Amount</th>
                    <th>Payment Mode</th>
                    <th>Transaction Ref</th>
                    <th>Payment Date</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($res->num_rows>0){
                    while($row=$res->fetch_assoc()){
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['invoice_number']}</td>
                            <td>{$row['patient_name']}</td>
                            <td>{$row['amount']}</td>
                            <td>{$row['payment_mode']}</td>
                            <td>{$row['transaction_ref']}</td>
                            <td>{$row['payment_date']}</td>
                            <td>{$row['notes']}</td>
                            <td>
                                <a href='edit_payment.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='view_payments.php?delete_id={$row['id']}' onclick=\"return confirm('Delete this payment?');\" class='btn btn-danger btn-sm'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>No payments found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
