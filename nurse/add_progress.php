<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'nurse') {
    header("Location: ../login.php");
    exit();
}

$nurse_id = $_SESSION['staff_id'];
$error = '';
$edit_mode = false;
$edit_id = 0;

// Handle Delete
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $del_stmt = $conn->prepare("DELETE FROM patient_vitals WHERE id=? AND nurse_id=?");
    $del_stmt->bind_param("ii", $delete_id, $nurse_id);
    $del_stmt->execute();
    header("Location: add_progress.php");
    exit();
}

// Handle Edit Mode
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $edit_stmt = $conn->prepare("SELECT * FROM patient_vitals WHERE id=? AND nurse_id=?");
    $edit_stmt->bind_param("ii", $edit_id, $nurse_id);
    $edit_stmt->execute();
    $edit_res = $edit_stmt->get_result();
    if ($edit_res->num_rows === 1) {
        $edit_mode = true;
        $edit_data = $edit_res->fetch_assoc();
    } else {
        $error = "Record not found or unauthorized";
    }
}

// Handle Add / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = intval($_POST['patient_id']);
    $bp   = $_POST['blood_pressure'];
    $temp = $_POST['temperature'];
    $pulse = $_POST['pulse_rate'];
    $resp = $_POST['respiratory_rate'];
    $oxy  = $_POST['oxygen_saturation'];

    if (!empty($_POST['id'])) {
        // Update
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE patient_vitals SET patient_id=?, blood_pressure=?, temperature=?, pulse_rate=?, respiratory_rate=?, oxygen_saturation=? WHERE id=? AND nurse_id=?");
        $stmt->bind_param("isdiiiii", $patient_id, $bp, $temp, $pulse, $resp, $oxy, $id, $nurse_id);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO patient_vitals (patient_id, nurse_id, blood_pressure, temperature, pulse_rate, respiratory_rate, oxygen_saturation) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("iisdiis", $patient_id, $nurse_id, $bp, $temp, $pulse, $resp, $oxy);
    }

    if ($stmt->execute()) {
        header("Location: add_progress.php");
        exit();
    } else {
        $error = "Operation failed: " . $conn->error;
    }
}

// Fetch patients for dropdown
$patients = $conn->query("SELECT id, name FROM patients WHERE status='active' ORDER BY name ASC");

// Fetch all vitals added by this nurse
$vitals = $conn->query("SELECT pv.*, p.name AS patient_name FROM patient_vitals pv JOIN patients p ON pv.patient_id=p.id WHERE pv.nurse_id=$nurse_id ORDER BY pv.recorded_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Patient Progress Reports</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-4">

<h3>Patient Progress Reports</h3>

<?php if ($error) echo "<p class='text-danger'>$error</p>"; ?>

<div class="card mb-4">
<div class="card-body">
<h5><?= $edit_mode ? "Edit Progress Report" : "Add Progress Report" ?></h5>

<form method="POST">
    <?php if ($edit_mode): ?>
        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
    <?php endif; ?>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Patient</label>
            <select name="patient_id" class="form-control" required>
                <option value="">Select Patient</option>
                <?php while ($p = $patients->fetch_assoc()):
                    $sel = ($edit_mode && $edit_data['patient_id'] == $p['id']) ? 'selected' : '';
                ?>
                    <option value="<?= $p['id'] ?>" <?= $sel ?>><?= htmlspecialchars($p['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label>Blood Pressure</label>
            <input type="text" name="blood_pressure" class="form-control" value="<?= $edit_mode ? htmlspecialchars($edit_data['blood_pressure']) : '' ?>" placeholder="120/80">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-3">
            <label>Temperature (°C)</label>
            <input type="number" step="0.1" name="temperature" class="form-control" value="<?= $edit_mode ? $edit_data['temperature'] : '' ?>">
        </div>
        <div class="form-group col-md-3">
            <label>Pulse Rate</label>
            <input type="number" name="pulse_rate" class="form-control" value="<?= $edit_mode ? $edit_data['pulse_rate'] : '' ?>">
        </div>
        <div class="form-group col-md-3">
            <label>Respiratory Rate</label>
            <input type="number" name="respiratory_rate" class="form-control" value="<?= $edit_mode ? $edit_data['respiratory_rate'] : '' ?>">
        </div>
        <div class="form-group col-md-3">
            <label>Oxygen Saturation (%)</label>
            <input type="number" name="oxygen_saturation" class="form-control" value="<?= $edit_mode ? $edit_data['oxygen_saturation'] : '' ?>">
        </div>
    </div>

    <div class="text-right">
        <button class="btn btn-primary"><?= $edit_mode ? "Update" : "Save" ?></button>
        <?php if ($edit_mode): ?>
            <a href="add_progress.php" class="btn btn-secondary">Cancel</a>
        <?php endif; ?>
    </div>
</form>
</div>
</div>

<!-- Display Table -->
<div class="table-responsive">
<table class="table table-bordered table-hover">
<thead>
<tr>
<th>ID</th>
<th>Patient</th>
<th>Blood Pressure</th>
<th>Temp (°C)</th>
<th>Pulse</th>
<th>Respiratory</th>
<th>Oxygen</th>
<th>Recorded At</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php if ($vitals->num_rows > 0): ?>
    <?php while ($row = $vitals->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['patient_name']) ?></td>
            <td><?= htmlspecialchars($row['blood_pressure']) ?></td>
            <td><?= $row['temperature'] ?></td>
            <td><?= $row['pulse_rate'] ?></td>
            <td><?= $row['respiratory_rate'] ?></td>
            <td><?= $row['oxygen_saturation'] ?></td>
            <td><?= $row['recorded_at'] ?></td>
            <td>
                <a href="?edit_id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Delete this record?');" class="btn btn-danger btn-sm">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
<tr><td colspan="9" class="text-center">No progress reports found</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>

</div>

</body>
</html>
