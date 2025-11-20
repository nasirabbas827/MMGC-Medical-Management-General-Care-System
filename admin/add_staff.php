<?php
session_start();
include('config.php');

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']); // plain text password
    $staff_role = $_POST['staff_role'];
    $join_date = $_POST['join_date'];
    $shift = $_POST['shift'];
    $status = $_POST['status'];

    $query = "INSERT INTO staff (full_name, email, phone, password, staff_role, join_date, shift, status)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssss", $full_name, $email, $phone, $password, $staff_role, $join_date, $shift, $status);

    if ($stmt->execute()) {
        $success = "Staff member added successfully.";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Staff</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-4">
    <div class="card mx-auto" style="max-width:600px;">
        <div class="card-body">
            <h3>Add Staff Member</h3>
            <?php if(isset($success)) echo "<p class='text-success'>$success</p>"; ?>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Password</label>
                        <input type="text" name="password" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Role</label>
                        <select name="staff_role" class="form-control" required>
                            <option value="nurse">Nurse</option>
                            <option value="reception">Reception</option>
                            <option value="lab">Lab</option>
                            <option value="accounts">Accounts</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Join Date</label>
                        <input type="date" name="join_date" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Shift</label>
                        <input type="text" name="shift" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Add Staff</button>
                <a href="view_staff.php" class="btn btn-dark">View Staff</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
