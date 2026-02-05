<?php
session_start();
require_once('config.php');

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitize inputs
    $mr_number       = trim($_POST['mr_number']);
    $name            = trim($_POST['name']);
    $email           = trim($_POST['email']);
    $phone           = trim($_POST['phone']);
    $password        = trim($_POST['password']);
    $date_of_birth   = $_POST['date_of_birth'];
    $gender          = $_POST['gender'];
    $blood_group     = trim($_POST['blood_group']);
    $address         = trim($_POST['address']);
    $guardian_name   = trim($_POST['guardian_name']);
    $guardian_phone  = trim($_POST['guardian_phone']);
    $status          = 'active';

    // Basic validation
    if (empty($mr_number) || empty($name) || empty($phone) || empty($password)) {
        $errors[] = "Required fields are missing.";
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    // Check MR number uniqueness
    $check = $conn->prepare("SELECT id FROM patients WHERE mr_number = ?");
    $check->bind_param("s", $mr_number);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $errors[] = "MR Number already registered.";
    }
    $check->close();

    if (empty($errors)) {


        $query = "INSERT INTO patients 
            (mr_number, name, email, phone, password, date_of_birth, gender, blood_group, address, guardian_name, guardian_phone, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssssssssssss",
            $mr_number,
            $name,
            $email,
            $phone,
            $password,
            $date_of_birth,
            $gender,
            $blood_group,
            $address,
            $guardian_name,
            $guardian_phone,
            $status
        );

        if ($stmt->execute()) {
            $success = "Registration successful. You can now login.";
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Patient Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 720px;">
        <div class="card-body">
            <h3 class="mb-3">Patient Registration</h3>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $e) echo "<div>$e</div>"; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>MR Number *</label>
                        <input type="text" name="mr_number" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Full Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Phone *</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Password *</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Blood Group</label>
                        <input type="text" name="blood_group" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="2"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Guardian Name</label>
                        <input type="text" name="guardian_name" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Guardian Phone</label>
                        <input type="text" name="guardian_phone" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Register</button>
                <a href="login.php" class="btn btn-link">Already have an account?</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
