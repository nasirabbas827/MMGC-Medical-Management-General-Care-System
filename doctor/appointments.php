<?php
session_start();
include('../config.php');

/* =====================
   AUTH GUARD
   ===================== */
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

/* =====================
   FETCH DOCTOR APPOINTMENTS
   ===================== */
$query = "
    SELECT a.*, p.name AS patient_name, p.mr_number, p.phone AS patient_phone, p.email AS patient_email
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = ?
    ORDER BY a.appointment_date ASC, a.start_time ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Appointments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">
    <h3 class="mb-4 text-center">My Appointments</h3>

    <?php if (count($appointments) == 0): ?>
        <p class="text-center">No appointments found.</p>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Patient</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $index => $app): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo $app['appointment_date']; ?></td>
                        <td><?php echo $app['start_time'] . " - " . $app['end_time']; ?></td>
                        <td><?php echo htmlspecialchars($app['patient_name']); ?></td>
                        <td><?php echo ucfirst($app['appointment_type']); ?></td>
                        <td><?php echo ucfirst($app['status']); ?></td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#appointmentModal<?php echo $app['id']; ?>">
                                View Details
                            </button>
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="appointmentModal<?php echo $app['id']; ?>" tabindex="-1" aria-labelledby="appointmentModalLabel<?php echo $app['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="appointmentModalLabel<?php echo $app['id']; ?>">Appointment Details</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <h6>Appointment Info</h6>
                                    <p><strong>Date:</strong> <?php echo $app['appointment_date']; ?></p>
                                    <p><strong>Time:</strong> <?php echo $app['start_time'] . " - " . $app['end_time']; ?></p>
                                    <p><strong>Type:</strong> <?php echo ucfirst($app['appointment_type']); ?></p>
                                    <p><strong>Status:</strong> <?php echo ucfirst($app['status']); ?></p>
                                    <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($app['notes'])); ?></p>

                                    <hr>
                                    <h6>Patient Info</h6>
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($app['patient_name']); ?></p>
                                    <p><strong>MR Number:</strong> <?php echo $app['mr_number']; ?></p>
                                    <p><strong>Email:</strong> <?php echo $app['patient_email']; ?></p>
                                    <p><strong>Phone:</strong> <?php echo $app['patient_phone']; ?></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
