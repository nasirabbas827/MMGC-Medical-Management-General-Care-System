<?php
session_start();
include('config.php');

if(!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin"){
    header("Location: admin_login.php");
    exit;
}

// Fetch doctors for dropdown
$doctors = $conn->query("SELECT id, full_name FROM doctors ORDER BY full_name ASC");

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $doctor_id = $_POST['doctor_id'];
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $slot_duration = $_POST['slot_duration'];

    $query = "INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time, slot_duration_minutes)
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssi", $doctor_id, $day_of_week, $start_time, $end_time, $slot_duration);

    if($stmt->execute()){
        $success="Schedule added successfully.";
    }else{
        $error="Error: ".$conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Doctor Schedule</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-4">
    <div class="card mx-auto" style="max-width:600px;">
        <div class="card-body">
            <h3>Add Doctor Schedule</h3>
            <?php if(isset($success)) echo "<p class='text-success'>$success</p>"; ?>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Doctor</label>
                        <select name="doctor_id" class="form-control" required>
                            <option value="">Select Doctor</option>
                            <?php while($row=$doctors->fetch_assoc()){
                                echo "<option value='{$row['id']}'>{$row['full_name']}</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Day of Week</label>
                        <select name="day_of_week" class="form-control" required>
                            <option value="">Select Day</option>
                            <option value="Mon">Mon</option>
                            <option value="Tue">Tue</option>
                            <option value="Wed">Wed</option>
                            <option value="Thu">Thu</option>
                            <option value="Fri">Fri</option>
                            <option value="Sat">Sat</option>
                            <option value="Sun">Sun</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Start Time</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>End Time</label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Slot Duration (minutes)</label>
                    <input type="number" name="slot_duration" class="form-control" value="15" min="5" max="120">
                </div>

                <button type="submit" class="btn btn-primary">Add Schedule</button>
                <a href="view_schedules.php" class="btn btn-dark">View Schedules</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
