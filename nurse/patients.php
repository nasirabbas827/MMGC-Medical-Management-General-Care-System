<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'nurse') {
    header("Location: ../login.php");
    exit();
}

$result = $conn->query("
    SELECT id, mr_number, name, phone, gender, blood_group, status
    FROM patients
    WHERE status = 'active'
    ORDER BY name ASC
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Patients</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/style.css">

</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">

    <h4 class="mb-3">Patients List</h4>

        <div class="card-body table-responsive">

            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>MR #</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Blood Group</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['mr_number']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td><?= htmlspecialchars($row['blood_group']) ?></td>
                        <td>
                            <a href="add_progress.php?patient_id=<?= $row['id'] ?>"
                               class="btn btn-sm btn-primary">
                                Add Progress Report
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>

                </tbody>
            </table>

        </div>
    </div>

</div>

</body>
</html>
