<?php
session_start();
include('../config.php');

/* =====================
   AUTH GUARD
   ===================== */
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

/* =====================
   FETCH DOCTOR DATA
   ===================== */
$query = "SELECT * FROM doctors WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if (!$doctor) {
    die("Doctor record not found.");
}

/* =====================
   UPDATE PROFILE
   ===================== */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $specialization = trim($_POST['specialization']);
    $qualification = trim($_POST['qualification']);
    $experience_years = $_POST['experience_years'];
    $consultation_fee = $_POST['consultation_fee'];
    $bio = trim($_POST['bio']);

    // Optional password update (plain text)
    $password_sql = "";
    if (!empty($_POST['password'])) {
        $password = trim($_POST['password']);
        $password_sql = ", password = '$password'";
    }

    // Profile image upload (EXACT SAME logic)
    $image_path = $doctor['profile_image'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $image_name = time() . "_" . basename($_FILES['profile_image']['name']);
        $target_dir = "../admin/uploads/";
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    $update_query = "
        UPDATE doctors SET
            full_name = ?,
            phone = ?,
            specialization = ?,
            qualification = ?,
            experience_years = ?,
            consultation_fee = ?,
            profile_image = ?,
            bio = ?
            $password_sql
        WHERE id = ?
    ";

    $stmt = $conn->prepare($update_query);
    $stmt->bind_param(
        "sssssdssi",
        $full_name,
        $phone,
        $specialization,
        $qualification,
        $experience_years,
        $consultation_fee,
        $image_path,
        $bio,
        $doctor_id
    );

    if ($stmt->execute()) {
        $success = "Profile updated successfully.";

        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $doctor = $stmt->get_result()->fetch_assoc();
    } else {
        $error = "Update failed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">
    <div class="card mx-auto" style="max-width:700px;">
        <div class="card-body">

            <!-- PROFILE IMAGE TOP -->
            <div class="text-center mb-3">
                <?php if (!empty($doctor['profile_image'])): ?>
                    <img src="../admin/<?php echo htmlspecialchars($doctor['profile_image']); ?>"
                         class="rounded-circle"
                         style="width:150px;height:150px;object-fit:cover;border:3px solid #007bff;">
                <?php else: ?>
                    <img src="../uploads/default-doctor.png"
                         class="rounded-circle"
                         style="width:150px;height:150px;object-fit:cover;border:3px solid #ccc;">
                <?php endif; ?>
            </div>

            <h3 class="text-center">My Profile</h3>

            <?php if (isset($success)) echo "<p class='text-success text-center'>$success</p>"; ?>
            <?php if (isset($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Full Name</label>
                        <input type="text" name="full_name"
                               value="<?php echo htmlspecialchars($doctor['full_name']); ?>"
                               class="form-control" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Email (Read Only)</label>
                        <input type="email"
                               value="<?php echo htmlspecialchars($doctor['email']); ?>"
                               class="form-control" readonly>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Phone</label>
                        <input type="text" name="phone"
                               value="<?php echo htmlspecialchars($doctor['phone']); ?>"
                               class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Status (Read Only)</label>
                        <input type="text"
                               value="<?php echo ucfirst($doctor['status']); ?>"
                               class="form-control" readonly>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>New Password (Optional)</label>
                        <input type="text" name="password" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Change Profile Image</label>
                        <input type="file" name="profile_image" class="form-control-file">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Specialization</label>
                        <input type="text" name="specialization"
                               value="<?php echo htmlspecialchars($doctor['specialization']); ?>"
                               class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Qualification</label>
                        <input type="text" name="qualification"
                               value="<?php echo htmlspecialchars($doctor['qualification']); ?>"
                               class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Experience Years</label>
                        <input type="number" name="experience_years"
                               value="<?php echo $doctor['experience_years']; ?>"
                               class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Consultation Fee</label>
                        <input type="number" step="0.01" name="consultation_fee"
                               value="<?php echo $doctor['consultation_fee']; ?>"
                               class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" rows="3" class="form-control"><?php
                        echo htmlspecialchars($doctor['bio']);
                    ?></textarea>
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
