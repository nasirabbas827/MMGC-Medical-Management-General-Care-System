<?php
session_start();
include('config.php');

if(!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin"){
    header("Location: admin_login.php");
    exit;
}

if(!isset($_GET['id'])){
    echo "Invalid request."; exit;
}

$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM invoices WHERE id=$id");
if($res->num_rows!=1){ echo "Invoice not found."; exit; }
$invoice = $res->fetch_assoc();

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $invoice_number = trim($_POST['invoice_number']);
    $patient_id = $_POST['patient_id'];
    $description = trim($_POST['description']);
    $type = $_POST['type'];
    $total_amount = $_POST['total_amount'];
    $discount = $_POST['discount'];
    $net_amount = $_POST['net_amount'];
    $payment_status = $_POST['payment_status'];
    $issued_date = $_POST['issued_date'];
    $due_date = $_POST['due_date'];

    $stmt = $conn->prepare("UPDATE invoices SET 
        invoice_number=?, patient_id=?, description=?, type=?, total_amount=?, discount=?, net_amount=?, payment_status=?, issued_date=?, due_date=? 
        WHERE id=?");
    $stmt->bind_param("sssssssssss", $invoice_number, $patient_id, $description, $type, $total_amount, $discount, $net_amount, $payment_status, $issued_date, $due_date, $id);
    if($stmt->execute()){
        echo "<script>alert('Invoice updated.'); window.location='view_invoices.php';</script>";
    } else { $error="Update failed."; }
}

$patients = $conn->query("SELECT id,name FROM patients ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Invoice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-4">
    <div class="card mx-auto" style="max-width:700px;">
        <div class="card-body">
            <h3>Edit Invoice</h3>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Invoice Number</label>
                        <input type="text" name="invoice_number" class="form-control" value="<?= $invoice['invoice_number'] ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient</label>
                        <select name="patient_id" class="form-control" required>
                            <?php while($p=$patients->fetch_assoc()){ ?>
                                <option value="<?= $p['id'] ?>" <?= $p['id']==$invoice['patient_id']?'selected':'' ?>><?= $p['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control" value="<?= $invoice['description'] ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Type</label>
                        <select name="type" class="form-control">
                            <option value="appointment" <?= $invoice['type']=='appointment'?'selected':'' ?>>Appointment</option>
                            <option value="procedure" <?= $invoice['type']=='procedure'?'selected':'' ?>>Procedure</option>
                            <option value="lab_test" <?= $invoice['type']=='lab_test'?'selected':'' ?>>Lab Test</option>
                            <option value="consultation" <?= $invoice['type']=='consultation'?'selected':'' ?>>Consultation</option>
                            <option value="other" <?= $invoice['type']=='other'?'selected':'' ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Total Amount</label>
                        <input type="number" step="0.01" name="total_amount" class="form-control" value="<?= $invoice['total_amount'] ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Discount</label>
                        <input type="number" step="0.01" name="discount" class="form-control" value="<?= $invoice['discount'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Net Amount</label>
                        <input type="number" step="0.01" name="net_amount" class="form-control" value="<?= $invoice['net_amount'] ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Payment Status</label>
                        <select name="payment_status" class="form-control">
                            <option value="pending" <?= $invoice['payment_status']=='pending'?'selected':'' ?>>Pending</option>
                            <option value="partial" <?= $invoice['payment_status']=='partial'?'selected':'' ?>>Partial</option>
                            <option value="paid" <?= $invoice['payment_status']=='paid'?'selected':'' ?>>Paid</option>
                            <option value="refunded" <?= $invoice['payment_status']=='refunded'?'selected':'' ?>>Refunded</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Issued Date</label>
                        <input type="date" name="issued_date" class="form-control" value="<?= $invoice['issued_date'] ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Due Date</label>
                        <input type="date" name="due_date" class="form-control" value="<?= $invoice['due_date'] ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Invoice</button>
                <a href="view_invoices.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
