<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// Delete report
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $res_file = $conn->query("SELECT file_path FROM lab_reports WHERE id=$delete_id AND uploaded_by=$doctor_id");
    if ($res_file->num_rows == 1) {
        $file_row = $res_file->fetch_assoc();
        $file_path = "../admin/" . $file_row['file_path'];
        if (file_exists($file_path)) unlink($file_path);

        $stmt = $conn->prepare("DELETE FROM lab_reports WHERE id=? AND uploaded_by=?");
        $stmt->bind_param("ii", $delete_id, $doctor_id);
        if ($stmt->execute()) {
            echo "<script>alert('Report deleted successfully.'); window.location='view_lab_reports_doctor.php';</script>";
            exit();
        } else {
            $error = "Failed to delete report: " . $conn->error;
        }
    }
}

$query = "SELECT r.*, p.name AS patient_name 
          FROM lab_reports r 
          LEFT JOIN patients p ON r.patient_id = p.id 
          WHERE r.uploaded_by = $doctor_id 
          ORDER BY r.id DESC";
$res = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Lab Reports</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
    <h3>My Lab/Ultrasound Reports</h3>
    <a href="add_lab_report_doctor.php" class="btn btn-dark float-right mb-2">Upload Report</a>
    <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>File</th>
                    <th>Type</th>
                    <th>Approved</th>
                    <th>Doctor Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['patient_name']}</td>
                            <td><a href='../admin/{$row['file_path']}' target='_blank'>View File</a></td>
                            <td>{$row['file_type']}</td>
                            <td>".($row['doctor_approved']?'Yes':'No')."</td>
                            <td>".htmlspecialchars($row['doctor_remarks'])."</td>
                            <td>
                                <a href='edit_lab_report_doctor.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit/Approve</a>
                                <a href='view_lab_reports_doctor.php?delete_id={$row['id']}' 
                                   onclick=\"return confirm('Are you sure you want to delete this report?');\" 
                                   class='btn btn-danger btn-sm'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No reports found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
    </div>
</div>
</body>
</html>
