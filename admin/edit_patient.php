<?php
session_start();
include('config.php');

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['id'])) { echo "Invalid request."; exit; }
$id = intval($_GET['id']);

$q = $conn->prepare("SELECT * FROM patients WHERE id=?");
$q->bind_param("i",$id);
$q->execute();
$res = $q->get_result();

if ($res->num_rows !== 1) { echo "Patient not found."; exit; }
$patient = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $mr_number = trim($_POST['mr_number']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']); // plain text
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $address = trim($_POST['address']);
    $guardian_name = trim($_POST['guardian_name']);
    $guardian_phone = trim($_POST['guardian_phone']);
    $status = $_POST['status'];

    $u = $conn->prepare("UPDATE patients SET mr_number=?, name=?, email=?, phone=?, password=?, date_of_birth=?, gender=?, blood_group=?, address=?, guardian_name=?, guardian_phone=?, status=? WHERE id=?");
    $u->bind_param("ssssssssssssi", $mr_number, $name, $email, $phone, $password, $date_of_birth, $gender, $blood_group, $address, $guardian_name, $guardian_phone, $status, $id);

    if($u->execute()){
        echo "<script>alert('Patient updated successfully.'); window.location='view_patients.php';</script>";
    } else { $error="Update failed."; }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Patient</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-4">
    <div class="card mx-auto" style="max-width:700px;">
        <div class="card-body">
            <h3>Edit Patient</h3>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>MR Number</label>
                        <input type="text" name="mr_number" class="form-control" value="<?= $patient['mr_number'] ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="<?= $patient['name'] ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $patient['email'] ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= $patient['phone'] ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Password</label>
                        <input type="text" name="password" class="form-control" value="<?= $patient['password'] ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?= $patient['date_of_birth'] ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">Select</option>
                            <option value="male" <?= $patient['gender']=='male'?'selected':'' ?>>Male</option>
                            <option value="female" <?= $patient['gender']=='female'?'selected':'' ?>>Female</option>
                            <option value="other" <?= $patient['gender']=='other'?'selected':'' ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Blood Group</label>
                        <input type="text" name="blood_group" class="form-control" value="<?= $patient['blood_group'] ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="2"><?= $patient['address'] ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Guardian Name</label>
                        <input type="text" name="guardian_name" class="form-control" value="<?= $patient['guardian_name'] ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Guardian Phone</label>
                        <input type="text" name="guardian_phone" class="form-control" value="<?= $patient['guardian_phone'] ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?= $patient['status']=='active'?'selected':'' ?>>Active</option>
                        <option value="inactive" <?= $patient['status']=='inactive'?'selected':'' ?>>Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update Patient</button>
                <a href="view_patients.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
