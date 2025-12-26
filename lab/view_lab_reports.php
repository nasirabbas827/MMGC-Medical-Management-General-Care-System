<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'lab') {
    header("Location: ../login.php");
    exit();
}

$staff_id = $_SESSION['staff_id']; // Logged-in lab staff ID

// Handle deletion
if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);
    // Ensure staff can only delete their own report
    $res = $conn->query("SELECT file_path FROM lab_reports WHERE id=$id AND uploaded_by=$staff_id");
    $file_row = $res->fetch_assoc();
    if($file_row && file_exists("../admin/".$file_row['file_path'])) unlink("../admin/".$file_row['file_path']);

    $del = $conn->prepare("DELETE FROM lab_reports WHERE id=? AND uploaded_by=?");
    $del->bind_param("ii",$id,$staff_id);
    if($del->execute()){
        echo "<script>alert('Report deleted.'); window.location='view_lab_reports.php';</script>";
    }
}

// Search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Fetch reports uploaded by this staff only
$query = "SELECT r.*, 
                 p.name AS patient_name, 
                 lt.test_name AS lab_test_name, 
                 pr.procedure_name AS procedure_name
          FROM lab_reports r
          LEFT JOIN patients p ON r.patient_id = p.id
          LEFT JOIN lab_tests lt ON r.lab_test_id = lt.id
          LEFT JOIN procedures pr ON r.procedure_id = pr.id
          WHERE r.uploaded_by = $staff_id";

if(!empty($search)){
    $query .= " AND (p.name LIKE '%$search%' OR lt.test_name LIKE '%$search%' OR pr.procedure_name LIKE '%$search%')";
}

$query .= " ORDER BY r.id DESC";
$res = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Lab Reports</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
    <a href="add_lab_report.php" class="btn btn-dark float-right">Add Report</a>
    <h3>My Lab Reports</h3>

    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" placeholder="Search" class="form-control mr-2" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary">Search</button>
        <a href="view_lab_reports.php" class="btn btn-secondary ml-2">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Lab Test</th>
                    <th>Procedure</th>
                    <th>File</th>
                    <th>Type</th>
                    <th>Approved</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($res->num_rows>0){
                    while($row=$res->fetch_assoc()){
                        $file_link = "../admin/".$row['file_path'];
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['patient_name']}</td>
                            <td>{$row['lab_test_name']}</td>
                            <td>{$row['procedure_name']}</td>
                            <td><a href='{$file_link}' target='_blank'>View File</a></td>
                            <td>{$row['file_type']}</td>
                            <td>".($row['doctor_approved'] ? 'Yes' : 'No')."</td>
                            <td>
                                <a href='edit_lab_report.php?id={$row['id']}' class='m-2 btn btn-warning btn-sm'>Edit</a>
                                <a href='view_lab_reports.php?delete_id={$row['id']}' onclick=\"return confirm('Delete this report?');\" class='btn btn-danger btn-sm'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>No lab reports found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
