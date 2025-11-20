<?php
session_start();
include('config.php');

if(!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin"){
    header("Location: admin_login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
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

    $stmt = $conn->prepare("INSERT INTO invoices 
        (invoice_number, patient_id, description, type, total_amount, discount, net_amount, payment_status, issued_date, due_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $invoice_number, $patient_id, $description, $type, $total_amount, $discount, $net_amount, $payment_status, $issued_date, $due_date);

    if($stmt->execute()){
        $success = "Invoice added successfully.";
    } else {
        $error = "Error: " . $conn->error;
    }
}
$patients = $conn->query("SELECT id, name FROM patients ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Invoice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-4">
    <div class="card mx-auto" style="max-width:700px;">
        <div class="card-body">
            <h3>Add Invoice</h3>
            <?php if(isset($success)) echo "<p class='text-success'>$success</p>"; ?>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Invoice Number</label>
                        <input type="text" name="invoice_number" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient</label>
                        <select name="patient_id" class="form-control" required>
                            <option value="">Select Patient</option>
                            <?php while($p=$patients->fetch_assoc()){ ?>
                                <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Type</label>
                        <select name="type" class="form-control">
                            <option value="appointment">Appointment</option>
                            <option value="procedure">Procedure</option>
                            <option value="lab_test">Lab Test</option>
                            <option value="consultation">Consultation</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Total Amount</label>
                        <input type="number" step="0.01" name="total_amount" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Discount</label>
                        <input type="number" step="0.01" name="discount" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Net Amount</label>
                        <input type="number" step="0.01" name="net_amount" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Payment Status</label>
                        <select name="payment_status" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="partial">Partial</option>
                            <option value="paid">Paid</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Issued Date</label>
                        <input type="date" name="issued_date" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Due Date</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Add Invoice</button>
                <a href="view_invoices.php" class="btn btn-dark">View Invoices</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
