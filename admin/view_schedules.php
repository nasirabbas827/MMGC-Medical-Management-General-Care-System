<?php
session_start();
include('config.php');

if(!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin"){
    header("Location: admin_login.php");
    exit;
}

if(isset($_GET['delete_id'])){
    $id=intval($_GET['delete_id']);
    $del=$conn->prepare("DELETE FROM doctor_schedules WHERE id=?");
    $del->bind_param("i",$id);
    if($del->execute()){
        echo "<script>alert('Schedule deleted.'); window.location='view_schedules.php';</script>";
    }else{
        echo "<script>alert('Error deleting schedule.');</script>";
    }
}

// Search functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$query = "SELECT s.*, d.full_name FROM doctor_schedules s LEFT JOIN doctors d ON s.doctor_id=d.id";

if(!empty($search)){
    $query .= " WHERE d.full_name LIKE '%$search%' OR s.day_of_week LIKE '%$search%'";
}

$query .= " ORDER BY s.id DESC";
$res=$conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Doctor Schedules</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-4">
    <a href="add_schedule.php" class="btn btn-dark float-right">Add Schedule</a>
    <h3>Doctor Schedules</h3>

    <!-- Search Form -->
    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" placeholder="Search by Doctor or Day" class="form-control mr-2">
        <button class="btn btn-primary">Search</button>
        <a href="view_schedules.php" class="btn btn-secondary ml-2">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Doctor</th>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Slot Duration</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($res->num_rows>0){
                    while($row=$res->fetch_assoc()){
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['full_name']}</td>
                            <td>{$row['day_of_week']}</td>
                            <td>{$row['start_time']}</td>
                            <td>{$row['end_time']}</td>
                            <td>{$row['slot_duration_minutes']} min</td>
                            <td>
                                <a href='edit_schedule.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='view_schedules.php?delete_id={$row['id']}' onclick=\"return confirm('Delete this schedule?');\" class='btn btn-danger btn-sm'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No schedules found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
