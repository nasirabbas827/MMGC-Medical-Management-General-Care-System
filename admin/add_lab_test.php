<?php
session_start();
include('config.php');

if(!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin"){
    header("Location: admin_login.php");
    exit;
}

// Fetch patients, doctors, categories for dropdowns
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

    $stmt = $conn->prepare("INSERT INTO lab_tests (patient_id, doctor_id, category_id, test_name, price, lab_staff_id, sample_collected_at, report_status, is_urgent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisdissi", $patient_id, $doctor_id, $category_id, $test_name, $price, $lab_staff_id, $sample_collected_at, $report_status, $is_urgent);

    if($stmt->execute()){
        $success = "Lab test added successfully.";
    } else {
        $error = "Error: ".$conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Lab Test</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-4">
    <div class="card mx-auto" style="max-width:700px;">
        <div class="card-body">
            <h3>Add Lab Test</h3>
            <?php if(isset($success)) echo "<p class='text-success'>$success</p>"; ?>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Patient</label>
                        <select name="patient_id" class="form-control" required>
                            <option value="">Select Patient</option>
                            <?php while($p = $patients->fetch_assoc()) echo "<option value='{$p['id']}'>{$p['name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Doctor</label>
                        <select name="doctor_id" class="form-control" required>
                            <option value="">Select Doctor</option>
                            <?php while($d = $doctors->fetch_assoc()) echo "<option value='{$d['id']}'>{$d['full_name']}</option>"; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Category</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php while($c = $categories->fetch_assoc()) echo "<option value='{$c['id']}'>{$c['name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Test Name</label>
                        <input type="text" name="test_name" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Lab Staff</label>
                        <select name="lab_staff_id" class="form-control">
                            <option value="">Select Staff</option>
                            <?php while($s = $staff->fetch_assoc()) echo "<option value='{$s['id']}'>{$s['full_name']}</option>"; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Sample Collected At</label>
                        <input type="datetime-local" name="sample_collected_at" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Report Status</label>
                        <select name="report_status" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="ready">Ready</option>
                            <option value="delivered">Delivered</option>
                        </select>
                    </div>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="is_urgent" class="form-check-input" id="urgentCheck">
                    <label class="form-check-label" for="urgentCheck">Urgent</label>
                </div>

                <button type="submit" class="btn btn-primary">Add Lab Test</button>
                <a href="view_lab_tests.php" class="btn btn-dark">View Lab Tests</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
