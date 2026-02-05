<?php
session_start();
include('../config.php');

/* =====================
   AUTH GUARD
   ===================== */
if (!isset($_SESSION['patient_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

/* =====================
   FETCH PATIENT DATA
   ===================== */
$query = "SELECT * FROM patients WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    die("Patient record not found.");
}

/* =====================
   UPDATE PROFILE
   ===================== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name             = trim($_POST['name']);
    $phone            = trim($_POST['phone']);
    $date_of_birth    = $_POST['date_of_birth'];
    $gender           = $_POST['gender'];
    $blood_group      = trim($_POST['blood_group']);
    $address          = trim($_POST['address']);
    $guardian_name    = trim($_POST['guardian_name']);
    $guardian_phone   = trim($_POST['guardian_phone']);

    // Optional password update (plain text)
    $password_sql = "";
    if (!empty($_POST['password'])) {
        $password = trim($_POST['password']);
        $password_sql = ", password = '$password'";
    }

    $update_query = "
        UPDATE patients SET
            name = ?,
            phone = ?,
            date_of_birth = ?,
            gender = ?,
            blood_group = ?,
            address = ?,
            guardian_name = ?,
            guardian_phone = ?
            $password_sql
        WHERE id = ?
    ";

    $stmt = $conn->prepare($update_query);
    $stmt->bind_param(
        "ssssssssi",
        $name,
        $phone,
        $date_of_birth,
        $gender,
        $blood_group,
        $address,
        $guardian_name,
        $guardian_phone,
        $patient_id
    );

    if ($stmt->execute()) {
        $success = "Profile updated successfully.";

        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $patient = $stmt->get_result()->fetch_assoc();
    } else {
        $error = "Update failed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">
    <div class="card mx-auto" style="max-width:700px;">
        <div class="card-body">

            <h3 class="text-center">My Profile</h3>

            <?php if (isset($success)) echo "<p class='text-success text-center'>$success</p>"; ?>
            <?php if (isset($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>

            <form method="POST">

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Full Name</label>
                        <input type="text" name="name"
                               value="<?php echo htmlspecialchars($patient['name']); ?>"
                               class="form-control" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Email (Read Only)</label>
                        <input type="email"
                               value="<?php echo htmlspecialchars($patient['email']); ?>"
                               class="form-control" readonly>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Phone</label>
                        <input type="text" name="phone"
                               value="<?php echo htmlspecialchars($patient['phone']); ?>"
                               class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Status (Read Only)</label>
                        <input type="text"
                               value="<?php echo ucfirst($patient['status']); ?>"
                               class="form-control" readonly>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>New Password (Optional)</label>
                        <input type="text" name="password" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth"
                               value="<?php echo $patient['date_of_birth']; ?>"
                               class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">Select</option>
                            <option value="male" <?php if($patient['gender']=='male') echo 'selected'; ?>>Male</option>
                            <option value="female" <?php if($patient['gender']=='female') echo 'selected'; ?>>Female</option>
                            <option value="other" <?php if($patient['gender']=='other') echo 'selected'; ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Blood Group</label>
                        <input type="text" name="blood_group"
                               value="<?php echo htmlspecialchars($patient['blood_group']); ?>"
                               class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" rows="2" class="form-control"><?php
                        echo htmlspecialchars($patient['address']);
                    ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Guardian Name</label>
                        <input type="text" name="guardian_name"
                               value="<?php echo htmlspecialchars($patient['guardian_name']); ?>"
                               class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Guardian Phone</label>
                        <input type="text" name="guardian_phone"
                               value="<?php echo htmlspecialchars($patient['guardian_phone']); ?>"
                               class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Update Profile
                </button>

            </form>

        </div>
    </div>
</div>

</body>
</html>
