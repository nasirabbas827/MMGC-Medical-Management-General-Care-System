<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'accounts') {
    header("Location: ../login.php");
    exit();
}

if(!isset($_GET['id'])){
    echo "Invalid request.";
    exit;
}

$id=intval($_GET['id']);
$q=$conn->prepare("SELECT * FROM payments WHERE id=?");
$q->bind_param("i",$id);
$q->execute();
$res=$q->get_result();

if($res->num_rows!==1){
    echo "Payment not found.";
    exit;
}

$payment=$res->fetch_assoc();

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $invoice_id=$_POST['invoice_id'];
    $patient_id=$_POST['patient_id'];
    $amount=$_POST['amount'];
    $payment_mode=$_POST['payment_mode'];
    $transaction_ref=$_POST['transaction_ref'];
    $notes=$_POST['notes'];

    $u=$conn->prepare("UPDATE payments SET invoice_id=?, patient_id=?, amount=?, payment_mode=?, transaction_ref=?, notes=? WHERE id=?");
    $u->bind_param("iidsssi",$invoice_id,$patient_id,$amount,$payment_mode,$transaction_ref,$notes,$id);

    if($u->execute()){
        echo "<script>alert('Payment updated successfully.'); window.location='view_payments.php';</script>";
    } else {
        $error="Update failed: ".$conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Payment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css"> 

</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
    <div class="card mx-auto" style="max-width:600px;">
        <div class="card-body">
            <h3>Edit Payment</h3>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Invoice</label>
                        <select name="invoice_id" class="form-control" required>
                            <?php
                            $res=$conn->query("SELECT id, invoice_number FROM invoices ORDER BY id DESC");
                            while($row=$res->fetch_assoc()){
                                $sel = $row['id']==$payment['invoice_id'] ? "selected" : "";
                                echo "<option value='{$row['id']}' $sel>{$row['invoice_number']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient</label>
                        <select name="patient_id" class="form-control" required>
                            <?php
                            $res=$conn->query("SELECT id, name FROM patients ORDER BY id DESC");
                            while($row=$res->fetch_assoc()){
                                $sel=$row['id']==$payment['patient_id'] ? "selected" : "";
                                echo "<option value='{$row['id']}' $sel>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="<?= $payment['amount'] ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Payment Mode</label>
                        <select name="payment_mode" class="form-control" required>
                            <option value="cash" <?= $payment['payment_mode']=='cash'?'selected':'' ?>>Cash</option>
                            <option value="bank_transfer" <?= $payment['payment_mode']=='bank_transfer'?'selected':'' ?>>Bank Transfer</option>
                            <option value="card" <?= $payment['payment_mode']=='card'?'selected':'' ?>>Card</option>
                            <option value="online" <?= $payment['payment_mode']=='online'?'selected':'' ?>>Online</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Transaction Ref</label>
                        <input type="text" name="transaction_ref" class="form-control" value="<?= $payment['transaction_ref'] ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Notes</label>
                        <input type="text" name="notes" class="form-control" value="<?= $payment['notes'] ?>">
                    </div>
                </div>
                <button class="btn btn-primary">Update Payment</button>
                <a href="view_payments.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
