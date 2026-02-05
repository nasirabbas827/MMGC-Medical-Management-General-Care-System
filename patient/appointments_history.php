<?php
session_start();
include('../config.php');

/* =====================
   AUTH GUARD
   ===================== */
if (!isset($_SESSION['patient_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

/* =====================
   FETCH APPOINTMENTS
   ===================== */
$query = "
    SELECT a.*, d.full_name AS doctor_name, d.specialization
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.patient_id = ?
    ORDER BY a.appointment_date DESC, a.start_time DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Appointments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">
    <h3 class="text-center mb-4">My Appointments History</h3>

    <?php if (empty($appointments)): ?>
        <div class="alert alert-info text-center">
            You have no appointments yet.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Doctor</th>
                        <th>Specialization</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $index => $appt): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($appt['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($appt['specialization']); ?></td>
                            <td><?php echo date('d M Y', strtotime($appt['appointment_date'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($appt['start_time'])) . " - " . date('h:i A', strtotime($appt['end_time'])); ?></td>
                            <td><?php echo ucfirst($appt['appointment_type']); ?></td>
                            <td>
                                <?php
                                    $status_class = [
                                        'scheduled' => 'badge badge-warning',
                                        'confirmed' => 'badge badge-primary',
                                        'completed' => 'badge badge-success',
                                        'cancelled' => 'badge badge-danger',
                                        'no-show' => 'badge badge-secondary'
                                    ];
                                ?>
                                <span class="<?php echo $status_class[$appt['status']] ?? 'badge badge-light'; ?>">
                                    <?php echo ucfirst($appt['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($appt['notes']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
