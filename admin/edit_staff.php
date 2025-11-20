<?php
session_start();
include('config.php');

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit;
}

$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM staff WHERE id = $id");
if ($res->num_rows != 1) { echo "Staff not found."; exit; }
$staff = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password']; // plain text
    $staff_role = $_POST['staff_role'];
    $join_date = $_POST['join_date'];
    $shift = $_POST['shift'];
    $status = $_POST['status'];

    $update = $conn->prepare("UPDATE staff SET full_name=?, email=?, phone=?, password=?, staff_role=?, join_date=?, shift=?, status=? WHERE id=?");
    $update->bind_param("sssssssss", $full_name, $email, $phone, $password, $staff_role, $join_date, $shift, $status, $id);

    if ($update->execute()) {
        echo "<script>alert('Staff updated successfully.'); window.location='view_staff.php';</script>";
    } else {
        $error = "Update failed.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Staff</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-4">
    <div class="card mx-auto" style="max-width:600px;">
        <div class="card-body">
            <h3>Edit Staff Member</h3>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?= $staff['full_name'] ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $staff['email'] ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= $staff['phone'] ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Password</label>
                        <input type="text" name="password" class="form-control" value="<?= $staff['password'] ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Role</label>
                        <select name="staff_role" class="form-control" required>
                            <option value="nurse" <?= $staff['staff_role']=="nurse"?"selected":"" ?>>Nurse</option>
                            <option value="reception" <?= $staff['staff_role']=="reception"?"selected":"" ?>>Reception</option>
                            <option value="lab" <?= $staff['staff_role']=="lab"?"selected":"" ?>>Lab</option>
                            <option value="accounts" <?= $staff['staff_role']=="accounts"?"selected":"" ?>>Accounts</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Join Date</label>
                        <input type="date" name="join_date" class="form-control" value="<?= $staff['join_date'] ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Shift</label>
                        <input type="text" name="shift" class="form-control" value="<?= $staff['shift'] ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active" <?= $staff['status']=="active"?"selected":"" ?>>Active</option>
                            <option value="inactive" <?= $staff['status']=="inactive"?"selected":"" ?>>Inactive</option>
                            <option value="terminated" <?= $staff['status']=="terminated"?"selected":"" ?>>Terminated</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Staff</button>
                <a href="view_staff.php" class="btn btn-dark">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
