<?php
session_start();
include('config.php');

if(!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin"){
    header("Location: admin_login.php");
    exit;
}

if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);
    $del = $conn->prepare("DELETE FROM invoices WHERE id=?");
    $del->bind_param("i", $id);
    if($del->execute()){
        echo "<script>alert('Invoice deleted.'); window.location='view_invoices.php';</script>";
    }
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT i.*, p.name AS patient_name FROM invoices i LEFT JOIN patients p ON i.patient_id=p.id";

if(!empty($search)){
    $query .= " WHERE i.invoice_number LIKE '%$search%' OR p.name LIKE '%$search%'";
}

$query .= " ORDER BY i.id DESC";
$res = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Invoices</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-4">
    <a href="add_invoice.php" class="btn btn-dark float-right">Add Invoice</a>
    <h3>Invoices</h3>

    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary">Search</button>
        <a href="view_invoices.php" class="btn btn-secondary ml-2">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Invoice #</th>
                    <th>Patient</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Net</th>
                    <th>Status</th>
                    <th>Due</th>
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
                            <td>{$row['description']}</td>
                            <td>{$row['type']}</td>
                            <td>{$row['net_amount']}</td>
                            <td>{$row['payment_status']}</td>
                            <td>{$row['due_date']}</td>
                            <td>
                                <a href='edit_invoice.php?id={$row['id']}' class='m-2 btn btn-warning btn-sm'>Edit</a>
                                <a href='view_invoices.php?delete_id={$row['id']}' onclick=\"return confirm('Delete this invoice?');\" class='btn btn-danger btn-sm'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='12' class='text-center'>No invoices found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
