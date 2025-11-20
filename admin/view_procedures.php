<?php
session_start();
include('config.php');
if(!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin"){
    header("Location: admin_login.php");
    exit;
}

if(isset($_GET['delete_id'])){
    $id=intval($_GET['delete_id']);
    $del=$conn->prepare("DELETE FROM procedures WHERE id=?");
    $del->bind_param("i",$id);
    if($del->execute()){
        echo "<script>alert('Procedure deleted.'); window.location='view_procedures.php';</script>";
    }else{
        echo "<script>alert('Error deleting procedure.');</script>";
    }
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT p.*, pat.name AS patient_name, d.full_name AS doctor_name, s.full_name AS nurse_name
          FROM procedures p
          LEFT JOIN patients pat ON p.patient_id=pat.id
          LEFT JOIN doctors d ON p.doctor_id=d.id
          LEFT JOIN staff s ON p.nurse_id=s.id";

if(!empty($search)){
    $query.=" WHERE p.procedure_name LIKE '%$search%' OR pat.name LIKE '%$search%' OR d.full_name LIKE '%$search%'";
}

$query.=" ORDER BY p.id DESC";
$res=$conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
<title>View Procedures</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-4">
<h3>All Procedures</h3>
<a href="add_procedure.php" class="btn btn-dark float-right">Add Procedure</a>

<form method="GET" class="form-inline mb-3">
<input type="text" name="search" placeholder="Search" class="form-control mr-2">
<button class="btn btn-primary">Search</button>
<a href="view_procedures.php" class="btn btn-secondary ml-2">Reset</a>
</form>

<div class="table-responsive">
<table class="table table-bordered table-hover">
<thead>
<tr>
<th>ID</th>
<th>Patient</th>
<th>Doctor</th>
<th>Nurse</th>
<th>Type</th>
<th>Name</th>
<th>Date</th>
<th>Time</th>
<th>Status</th>
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
        <td>{$row['doctor_name']}</td>
        <td>{$row['nurse_name']}</td>
        <td>{$row['procedure_type']}</td>
        <td>{$row['procedure_name']}</td>
        <td>{$row['procedure_date']}</td>
        <td>{$row['procedure_time']}</td>
        <td>{$row['status']}</td>
        <td>
        <a href='edit_procedure.php?id={$row['id']}' class='m-2 btn btn-warning btn-sm'>Edit</a>
        <a href='view_procedures.php?delete_id={$row['id']}' onclick=\"return confirm('Delete this procedure?');\" class='btn btn-danger btn-sm'>Delete</a>
        </td>
        </tr>";
    }
}else{
    echo "<tr><td colspan='10' class='text-center'>No procedures found.</td></tr>";
}
?>
</tbody>
</table>
</div>
</div>
</body>
</html>
