<?php
session_start();
include('config.php');

if(!isset($_SESSION["usertype"]) || $_SESSION["usertype"]!=="admin"){
    header("Location: admin_login.php");
    exit;
}

if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);
    $res = $conn->query("SELECT file_path FROM lab_reports WHERE id=$id");
    $file_row = $res->fetch_assoc();
    if($file_row && file_exists($file_row['file_path'])) unlink($file_row['file_path']);

    $del = $conn->prepare("DELETE FROM lab_reports WHERE id=?");
    $del->bind_param("i",$id);
    if($del->execute()){
        echo "<script>alert('Report deleted.'); window.location='view_lab_reports.php';</script>";
    }
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT r.*, 
                 p.name AS patient_name, 
                 d1.full_name AS doctor_name, 
                 s.full_name AS staff_name 
          FROM lab_reports r
          LEFT JOIN patients p ON r.patient_id = p.id
          LEFT JOIN lab_tests lt ON r.lab_test_id = lt.id
          LEFT JOIN doctors d1 ON lt.doctor_id = d1.id
          LEFT JOIN staff s ON r.uploaded_by = s.id";


if(!empty($search)){
    $query .= " WHERE p.name LIKE '%$search%' OR d.full_name LIKE '%$search%' OR s.full_name LIKE '%$search%'";
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
<?php include('admin_navbar.php'); ?>
<div class="container mt-4">
    <a href="add_lab_report.php" class="btn btn-dark float-right">Add Report</a>
    <h3>Lab Reports</h3>

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
                    <th>Lab Test ID</th>
                    <th>Procedure ID</th>
                    <th>File</th>
                    <th>Type</th>
                    <th>Uploaded By</th>
                    <th>Approved</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($res->num_rows>0){
                    while($row=$res->fetch_assoc()){
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['patient_name']}</td>
                            <td>{$row['lab_test_id']}</td>
                            <td>{$row['procedure_id']}</td>
                            <td><a href='{$row['file_path']}' target='_blank'>View File</a></td>
                            <td>{$row['file_type']}</td>
                            <td>{$row['staff_name']}</td>
                            <td>".($row['doctor_approved']?'Yes':'No')."</td>
                            <td>
                                <a href='edit_lab_report.php?id={$row['id']}' class='m-2 btn btn-warning btn-sm'>Edit</a>
                                <a href='view_lab_reports.php?delete_id={$row['id']}' onclick=\"return confirm('Delete this report?');\" class='btn btn-danger btn-sm'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' class='text-center'>No lab reports found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
