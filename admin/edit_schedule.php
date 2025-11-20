<?php
session_start();
include('config.php');

if(!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin"){
    header("Location: admin_login.php");
    exit;
}

if(!isset($_GET['id'])){ echo "Invalid request"; exit; }
$id=intval($_GET['id']);

// Fetch schedule
$q=$conn->prepare("SELECT * FROM doctor_schedules WHERE id=?");
$q->bind_param("i",$id);
$q->execute();
$res=$q->get_result();
if($res->num_rows!==1){ echo "Schedule not found."; exit; }
$schedule=$res->fetch_assoc();

// Fetch doctors
$doctors=$conn->query("SELECT id, full_name FROM doctors ORDER BY full_name ASC");

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $doctor_id = $_POST['doctor_id'];
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $slot_duration = $_POST['slot_duration'];

    $u=$conn->prepare("UPDATE doctor_schedules SET doctor_id=?, day_of_week=?, start_time=?, end_time=?, slot_duration_minutes=? WHERE id=?");
    $u->bind_param("isssii",$doctor_id,$day_of_week,$start_time,$end_time,$slot_duration,$id);

    if($u->execute()){
        echo "<script>alert('Schedule updated successfully'); window.location='view_schedules.php';</script>";
    }else{ $error="Update failed"; }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Doctor Schedule</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-4">
    <div class="card mx-auto" style="max-width:600px;">
        <div class="card-body">
            <h3>Edit Doctor Schedule</h3>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Doctor</label>
                        <select name="doctor_id" class="form-control" required>
                            <?php while($row=$doctors->fetch_assoc()){
                                $sel=($row['id']==$schedule['doctor_id'])?'selected':'';
                                echo "<option value='{$row['id']}' $sel>{$row['full_name']}</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Day of Week</label>
                        <select name="day_of_week" class="form-control" required>
                            <?php
                            $days=['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
                            foreach($days as $d){
                                $sel=($d==$schedule['day_of_week'])?'selected':'';
                                echo "<option value='$d' $sel>$d</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Start Time</label>
                        <input type="time" name="start_time" class="form-control" value="<?= $schedule['start_time'] ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>End Time</label>
                        <input type="time" name="end_time" class="form-control" value="<?= $schedule['end_time'] ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Slot Duration (minutes)</label>
                    <input type="number" name="slot_duration" class="form-control" value="<?= $schedule['slot_duration_minutes'] ?>" min="5" max="120">
                </div>

                <button type="submit" class="btn btn-primary">Update Schedule</button>
                <a href="view_schedules.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
