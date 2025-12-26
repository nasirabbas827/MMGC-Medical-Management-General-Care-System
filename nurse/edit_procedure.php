<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'nurse') {
    header("Location: ../login.php");
    exit();
}

$nurse_id = $_SESSION['staff_id'];

if (!isset($_GET['id'])) {
    exit("Invalid request");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("
SELECT p.*, pat.name AS patient_name, d.full_name AS doctor_name
FROM procedures p
JOIN patients pat ON p.patient_id = pat.id
JOIN doctors d ON p.doctor_id = d.id
WHERE p.id = ? AND p.nurse_id = ?
");
$stmt->bind_param("ii", $id, $nurse_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    exit("Unauthorized access");
}

$proc = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $status = $_POST['status'];
    $treatment_notes = $_POST['treatment_notes'];
    $outcome = $_POST['outcome'];
    $prescription = $_POST['prescription'];

    $upd = $conn->prepare("
        UPDATE procedures 
        SET status=?, treatment_notes=?, outcome=?, prescription=?
        WHERE id=? AND nurse_id=?
    ");
    $upd->bind_param(
        "ssssii",
        $status,
        $treatment_notes,
        $outcome,
        $prescription,
        $id,
        $nurse_id
    );

    if ($upd->execute()) {
        header("Location: view_procedures.php");
        exit();
    } else {
        $error = "Update failed";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Assist Procedure</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">
<div class="card mx-auto" style="max-width:650px;">
<div class="card-body">

<h4>Assist Procedure</h4>

<?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

<p><strong>Patient:</strong> <?= htmlspecialchars($proc['patient_name']) ?></p>
<p><strong>Doctor:</strong> <?= htmlspecialchars($proc['doctor_name']) ?></p>
<p><strong>Procedure:</strong> <?= htmlspecialchars($proc['procedure_name']) ?></p>

<form method="POST">

    <div class="form-row">

        <div class="form-group col-md-6">
            <label>Status</label>
            <select name="status" class="form-control">
                <?php
                $statuses = ['planned','in-progress','completed','cancelled'];
                foreach ($statuses as $s) {
                    $sel = $proc['status'] === $s ? 'selected' : '';
                    echo "<option value='$s' $sel>$s</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group col-md-6">
            <label>Outcome</label>
            <input
                type="text"
                name="outcome"
                class="form-control"
                value="<?= htmlspecialchars($proc['outcome']) ?>">
        </div>

    </div>

    <div class="form-row">

        <div class="form-group col-md-6">
            <label>Treatment Notes</label>
            <textarea
                name="treatment_notes"
                class="form-control"
                rows="4"><?= htmlspecialchars($proc['treatment_notes']) ?></textarea>
        </div>

        <div class="form-group col-md-6">
            <label>Prescription</label>
            <textarea
                name="prescription"
                class="form-control"
                rows="4"><?= htmlspecialchars($proc['prescription']) ?></textarea>
        </div>

    </div>

    <div class="form-row mt-3">
        <div class="col-md-12 text-right">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="view_procedures.php" class="btn btn-secondary ml-2">Back</a>
        </div>
    </div>

</form>


</div>
</div>
</div>

</body>
</html>
