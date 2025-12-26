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
   FETCH PATIENT LIST
   ===================== */
$query = "
    SELECT DISTINCT p.id, p.mr_number, p.name, p.phone, p.email
    FROM patients p
    JOIN appointments a ON p.id = a.patient_id
    WHERE a.doctor_id = ?
    ORDER BY p.name ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$patients = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Patients</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">
    <h3 class="mb-4 text-center">My Patients</h3>

    <?php if (count($patients) == 0): ?>
        <p class="text-center">No patients found.</p>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>MR Number</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $index => $patient): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo $patient['mr_number']; ?></td>
                        <td><?php echo htmlspecialchars($patient['name']); ?></td>
                        <td><?php echo $patient['phone']; ?></td>
                        <td><?php echo $patient['email']; ?></td>
                        <td>
                            <a href="patient_details.php?id=<?php echo $patient['id']; ?>" class="btn btn-info btn-sm">
                                View Progress & Details
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
