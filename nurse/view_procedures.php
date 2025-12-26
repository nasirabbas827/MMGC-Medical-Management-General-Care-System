<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'nurse') {
    header("Location: ../login.php");
    exit();
}

$nurse_id = $_SESSION['staff_id'];

$query = "
SELECT p.*, 
       pat.name AS patient_name,
       d.full_name AS doctor_name
FROM procedures p
JOIN patients pat ON p.patient_id = pat.id
JOIN doctors d ON p.doctor_id = d.id
WHERE p.nurse_id = ?
ORDER BY p.procedure_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $nurse_id);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<title>My Assisted Procedures</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">
<h3>My Assisted Procedures</h3>

<div class="table-responsive mt-3">
<table class="table table-bordered table-hover">
<thead>
<tr>
<th>ID</th>
<th>Patient</th>
<th>Doctor</th>
<th>Type</th>
<th>Name</th>
<th>Date</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php if ($res->num_rows > 0): ?>
<?php while ($row = $res->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['patient_name']) ?></td>
<td><?= htmlspecialchars($row['doctor_name']) ?></td>
<td><?= $row['procedure_type'] ?></td>
<td><?= $row['procedure_name'] ?></td>
<td><?= $row['procedure_date'] ?></td>
<td><?= ucfirst($row['status']) ?></td>
<td>
<a href="edit_procedure.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
Update
</a>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="8" class="text-center">No procedures assigned</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>

</div>
</body>
</html>
