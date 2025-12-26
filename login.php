<?php
session_start();
require_once('config.php');

$email = $password = $role = "";
$email_err = $password_err = $role_err = $login_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty(trim($_POST['email']))) {
        $email_err = "Email is required";
    } else {
        $email = trim($_POST['email']);
    }

    if (empty(trim($_POST['password']))) {
        $password_err = "Password is required";
    } else {
        $password = trim($_POST['password']);
    }

    if (empty($_POST['role'])) {
        $role_err = "Please select role";
    } else {
        $role = $_POST['role'];
    }

    /* ================= DOCTOR LOGIN ================= */
    if (empty($email_err) && empty($password_err) && $role === 'doctor') {

        $stmt = $conn->prepare(
            "SELECT id, full_name, email, password, status 
             FROM doctors WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();

            if ($row['status'] !== 'active') {
                $login_msg = "<div class='alert alert-danger'>Account inactive</div>";
            } elseif ($password === $row['password']) {

                $_SESSION['doctor_id'] = $row['id'];
                $_SESSION['role'] = 'doctor';

                header("Location: doctor/doctor_dashboard.php");
                exit();
            } else {
                $password_err = "Invalid password";
            }
        } else {
            $email_err = "Doctor not found";
        }
        $stmt->close();
    }

    /* ================= STAFF LOGIN ================= */
    if (empty($email_err) && empty($password_err) && $role === 'staff') {

        $stmt = $conn->prepare(
            "SELECT id, full_name, email, password, staff_role, status 
             FROM staff WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();

            if ($row['status'] !== 'active') {
                $login_msg = "<div class='alert alert-danger'>Account inactive</div>";
            } elseif ($password === $row['password']) {

                $_SESSION['staff_id']   = $row['id'];
                $_SESSION['staff_role'] = $row['staff_role'];
                $_SESSION['role']       = 'staff';

                switch ($row['staff_role']) {
                    case 'nurse':
                        header("Location: nurse/nurse_dashboard.php");
                        break;
                    case 'reception':
                        header("Location: reception/reception_dashboard.php");
                        break;
                    case 'lab':
                        header("Location: lab/lab_dashboard.php");
                        break;
                    case 'accounts':
                        header("Location: accounts/accounts_dashboard.php");
                        break;
                }
                exit();
            } else {
                $password_err = "Invalid password";
            }
        } else {
            $email_err = "Staff not found";
        }
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="card mx-auto shadow-sm" style="max-width: 450px;">
        <div class="card-body">
            <h3 class="text-center mb-3">Login</h3>

            <?php echo $login_msg; ?>

<form method="post">
    <?php echo $login_msg; ?>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control">
        <small class="text-danger"><?php echo $email_err; ?></small>
    </div>

    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control">
        <small class="text-danger"><?php echo $password_err; ?></small>
    </div>

    <div class="form-group">
        <label>Login As</label>
        <select name="role" class="form-control">
            <option value="">Select</option>
            <option value="doctor">Doctor</option>
            <option value="staff">Staff</option>
        </select>
        <small class="text-danger"><?php echo $role_err; ?></small>
    </div>

    <button class="btn btn-primary btn-block">Login</button>
</form>


        </div>
    </div>
</div>

</body>
</html>

