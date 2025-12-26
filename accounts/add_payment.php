<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'accounts') {
    header("Location: ../login.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $invoice_id = $_POST['invoice_id'];
    $patient_id = $_POST['patient_id'];
    $amount = $_POST['amount'];
    $payment_mode = $_POST['payment_mode'];
    $transaction_ref = $_POST['transaction_ref'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO payments (invoice_id, patient_id, amount, payment_mode, transaction_ref, notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iidsss", $invoice_id, $patient_id, $amount, $payment_mode, $transaction_ref, $notes);

    if($stmt->execute()){
        $success = "Payment added successfully.";
    } else {
        $error = "Error: ".$conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Payment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css"> 

</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
    <div class="card mx-auto" style="max-width:600px;">
        <div class="card-body">
            <h3>Add Payment</h3>
            <?php if(isset($success)) echo "<p class='text-success'>$success</p>"; ?>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Invoice</label>
                        <select name="invoice_id" class="form-control" required>
                            <?php
                            $res = $conn->query("SELECT id, invoice_number FROM invoices ORDER BY id DESC");
                            while($row=$res->fetch_assoc()){
                                echo "<option value='{$row['id']}'>{$row['invoice_number']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient</label>
                        <select name="patient_id" class="form-control" required>
                            <?php
                            $res = $conn->query("SELECT id, name FROM patients ORDER BY id DESC");
                            while($row=$res->fetch_assoc()){
                                echo "<option value='{$row['id']}'>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Payment Mode</label>
                        <select name="payment_mode" class="form-control" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Transaction Ref</label>
                        <input type="text" name="transaction_ref" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Notes</label>
                        <input type="text" name="notes" class="form-control">
                    </div>
                </div>
                <button class="btn btn-primary">Add Payment</button>
                <a href="view_payments.php" class="btn btn-dark">View Payments</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
