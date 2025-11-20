<?php
session_start();
include('config.php');

if(!isset($_SESSION["usertype"]) || $_SESSION["usertype"]!=="admin"){
    header("Location: admin_login.php"); exit;
}

if(isset($_GET['delete_id'])){
    $id=intval($_GET['delete_id']);
    $del=$conn->prepare("DELETE FROM appointments WHERE id=?");
    $del->bind_param("i",$id);
    if($del->execute()){
        echo "<script>alert('Appointment deleted.'); window.location='view_appointments.php';</script>";
    } else {
        echo "<script>alert('Error deleting appointment.');</script>";
    }
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$query = "SELECT a.*, p.name AS patient_name, d.full_name AS doctor_name
          FROM appointments a
          LEFT JOIN patients p ON a.patient_id=p.id
          LEFT JOIN doctors d ON a.doctor_id=d.id";

if(!empty($search)){
    $query .= " WHERE a.id LIKE '%$search%' OR p.name LIKE '%$search%' OR d.full_name LIKE '%$search%'";
}

$query .= " ORDER BY a.appointment_date DESC, a.start_time ASC";

$res = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Appointments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-4">
    <a href="add_appointment.php" class="btn btn-dark float-right">Add Appointment</a>
    <h3>Appointments</h3>

    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" placeholder="Search" class="form-control mr-2">
        <button class="btn btn-primary">Search</button>
        <a href="view_appointments.php" class="btn btn-secondary ml-2">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                    <th>Type</th>
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
                            <td>{$row['appointment_date']}</td>
                            <td>{$row['start_time']}</td>
                            <td>{$row['end_time']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['appointment_type']}</td>
                            <td>
                                <a href='edit_appointment.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='view_appointments.php?delete_id={$row['id']}' onclick=\"return confirm('Delete this appointment?');\" class='btn btn-danger btn-sm'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>No appointments found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
