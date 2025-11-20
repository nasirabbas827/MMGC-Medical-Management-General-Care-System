<?php
session_start();
include('config.php');

if(!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin"){
    header("Location: admin_login.php");
    exit;
}

// Delete lab test
if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);
    $del = $conn->prepare("DELETE FROM lab_tests WHERE id=?");
    $del->bind_param("i", $id);
    if($del->execute()){
        echo "<script>alert('Lab test deleted.'); window.location='view_lab_tests.php';</script>";
    } else {
        echo "<script>alert('Error deleting lab test.');</script>";
    }
}

// Search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT l.*, p.name AS patient_name, d.full_name AS doctor_name, c.name AS category_name
          FROM lab_tests l
          LEFT JOIN patients p ON l.patient_id=p.id
          LEFT JOIN doctors d ON l.doctor_id=d.id
          LEFT JOIN lab_categories c ON l.category_id=c.id";

if(!empty($search)){
    $query .= " WHERE l.test_name LIKE '%$search%' OR p.name LIKE '%$search%' OR d.full_name LIKE '%$search%' OR c.name LIKE '%$search%'";
}

$query .= " ORDER BY l.id DESC";
$res = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Lab Tests</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-4">
    <a href="add_lab_test.php" class="btn btn-dark float-right">Add Lab Test</a>
    <h3>Lab Tests</h3>

    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" placeholder="Search" class="form-control mr-2" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary">Search</button>
        <a href="view_lab_tests.php" class="btn btn-secondary ml-2">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Category</th>
                    <th>Test Name</th>
                    <th>Price</th>
                    <th>Lab Staff</th>
                    <th>Status</th>
                    <th>Urgent</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($res->num_rows > 0){
                    while($row = $res->fetch_assoc()){
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['patient_name']}</td>
                            <td>{$row['doctor_name']}</td>
                            <td>{$row['category_name']}</td>
                            <td>{$row['test_name']}</td>
                            <td>{$row['price']}</td>
                            <td>{$row['lab_staff_id']}</td>
                            <td>{$row['report_status']}</td>
                            <td>".($row['is_urgent'] ? 'Yes' : 'No')."</td>
                            <td>
                                <a href='edit_lab_test.php?id={$row['id']}' class='m-2 btn btn-warning btn-sm'>Edit</a>
                                <a href='view_lab_tests.php?delete_id={$row['id']}' onclick=\"return confirm('Delete this lab test?');\" class='btn btn-danger btn-sm'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' class='text-center'>No lab tests found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
