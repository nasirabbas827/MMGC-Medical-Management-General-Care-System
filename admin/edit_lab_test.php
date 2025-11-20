<?php
session_start();
include('config.php');

if(!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin"){
    header("Location: admin_login.php");
    exit;
}

if(!isset($_GET['id'])){
    echo "Invalid request.";
    exit;
}

$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM lab_tests WHERE id=$id");
if($res->num_rows != 1){
    echo "Lab test not found.";
    exit;
}

$test = $res->fetch_assoc();

// Fetch dropdown data
$patients = $conn->query("SELECT id, name FROM patients");
$doctors = $conn->query("SELECT id, full_name FROM doctors");
$categories = $conn->query("SELECT id, name FROM lab_categories");
$staff = $conn->query("SELECT id, full_name FROM staff");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $category_id = $_POST['category_id'];
    $test_name = $_POST['test_name'];
    $price = $_POST['price'];
    $lab_staff_id = $_POST['lab_staff_id'];
    $sample_collected_at = $_POST['sample_collected_at'] ?: NULL;
    $report_status = $_POST['report_status'];
    $is_urgent = isset($_POST['is_urgent']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE lab_tests SET patient_id=?, doctor_id=?, category_id=?, test_name=?, price=?, lab_staff_id=?, sample_collected_at=?, report_status=?, is_urgent=? WHERE id=?");
    $stmt->bind_param("iiisdissii", $patient_id, $doctor_id, $category_id, $test_name, $price, $lab_staff_id, $sample_collected_at, $report_status, $is_urgent, $id);

    if($stmt->execute()){
        echo "<script>alert('Lab test updated successfully.'); window.location='view_lab_tests.php';</script>";
    } else {
        $error = "Update failed: ".$conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Lab Test</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-4">
    <div class="card mx-auto" style="max-width:700px;">
        <div class="card-body">
            <h3>Edit Lab Test</h3>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Patient</label>
                        <select name="patient_id" class="form-control" required>
                            <?php while($p = $patients->fetch_assoc()) 
                                echo "<option value='{$p['id']}' ".($test['patient_id']==$p['id']?'selected':'').">{$p['name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Doctor</label>
                        <select name="doctor_id" class="form-control" required>
                            <?php while($d = $doctors->fetch_assoc()) 
                                echo "<option value='{$d['id']}' ".($test['doctor_id']==$d['id']?'selected':'').">{$d['full_name']}</option>"; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Category</label>
                        <select name="category_id" class="form-control" required>
                            <?php while($c = $categories->fetch_assoc()) 
                                echo "<option value='{$c['id']}' ".($test['category_id']==$c['id']?'selected':'').">{$c['name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Test Name</label>
                        <input type="text" name="test_name" class="form-control" value="<?= htmlspecialchars($test['test_name']) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="<?= $test['price'] ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Lab Staff</label>
                        <select name="lab_staff_id" class="form-control">
                            <option value="">Select Staff</option>
                            <?php while($s = $staff->fetch_assoc()) 
                                echo "<option value='{$s['id']}' ".($test['lab_staff_id']==$s['id']?'selected':'').">{$s['full_name']}</option>"; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Sample Collected At</label>
                        <input type="datetime-local" name="sample_collected_at" class="form-control" value="<?= str_replace(' ', 'T', $test['sample_collected_at']) ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Report Status</label>
                        <select name="report_status" class="form-control">
                            <option value="pending" <?= $test['report_status']=='pending'?'selected':'' ?>>Pending</option>
                            <option value="processing" <?= $test['report_status']=='processing'?'selected':'' ?>>Processing</option>
                            <option value="ready" <?= $test['report_status']=='ready'?'selected':'' ?>>Ready</option>
                            <option value="delivered" <?= $test['report_status']=='delivered'?'selected':'' ?>>Delivered</option>
                        </select>
                    </div>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="is_urgent" class="form-check-input" id="urgentCheck" <?= $test['is_urgent'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="urgentCheck">Urgent</label>
                </div>

                <button type="submit" class="btn btn-primary">Update Lab Test</button>
                <a href="view_lab_tests.php" class="btn btn-dark">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
