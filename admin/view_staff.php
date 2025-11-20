<?php
session_start();
include('config.php');

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Delete staff
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $del = $conn->prepare("DELETE FROM staff WHERE id=?");
    $del->bind_param("i", $id);

    if ($del->execute()) {
        echo "<script>alert('Staff member deleted.'); window.location='view_staff.php';</script>";
    } else {
        echo "<script>alert('Error deleting staff.');</script>";
    }
}

// Search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT * FROM staff";

if (!empty($search)) {
    $query .= " WHERE id LIKE '%$search%' OR full_name LIKE '%$search%' OR staff_role LIKE '%$search%'";
}

$query .= " ORDER BY id DESC";
$res = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Staff</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-4">
    <a href="add_staff.php" class="btn btn-dark float-right mb-3">Add Staff</a>
    <h3>All Staff Members</h3>

    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" placeholder="Search by ID, Name or Role" class="form-control mr-2" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary">Search</button>
        <a href="view_staff.php" class="btn btn-secondary ml-2">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Shift</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['full_name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['staff_role']}</td>
                            <td>{$row['shift']}</td>
                            <td>{$row['status']}</td>
                            <td>
                                <a href='edit_staff.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='view_staff.php?delete_id={$row['id']}' onclick=\"return confirm('Delete this staff member?');\" class='btn btn-danger btn-sm'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No staff members found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
