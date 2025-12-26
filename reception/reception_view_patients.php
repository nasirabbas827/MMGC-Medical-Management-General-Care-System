<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'reception') {
    header("Location: ../login.php");
    exit();
}

if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);
    $del = $conn->prepare("DELETE FROM patients WHERE id=?");
    $del->bind_param("i", $id);
    $del->execute();
    header("Location: reception_view_patients.php");
    exit();
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT * FROM patients";

if(!empty($search)){
    $query .= " WHERE mr_number LIKE '%$search%' OR name LIKE '%$search%' OR phone LIKE '%$search%'";
}

$query .= " ORDER BY id DESC";
$res = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Patients</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">
    <a href="reception_add_patient.php" class="btn btn-dark float-right">Add Patient</a>
    <h3>All Patients</h3>

    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search">
        <button class="btn btn-primary">Search</button>
        <a href="reception_view_patients.php" class="btn btn-secondary ml-2">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>MR Number</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if($res->num_rows > 0){
                while($row = $res->fetch_assoc()){
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['mr_number']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['status']}</td>
                        <td>
                            <a href='reception_edit_patient.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                            <a href='?delete_id={$row['id']}' onclick=\"return confirm('Delete this patient?');\" class='btn btn-danger btn-sm'>Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No patients found</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
