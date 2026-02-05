<?php
session_start();
include('config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

$email = "";
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);

    if (empty($email)) {
        $msg = "<div class='alert alert-danger'>Please enter your email</div>";
    } else {
        // Check if patient exists
        $stmt = $conn->prepare("SELECT id, name FROM patients WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();
            $patient_id = $row['id'];

            // Generate a temporary password
            $tempPassword = bin2hex(random_bytes(4)); // 8 characters

            // Update patient password in DB
            $update = $conn->prepare("UPDATE patients SET password = ? WHERE id = ?");
            $update->bind_param("si", $tempPassword, $patient_id);
            $update->execute();
            $update->close();

            // Send temporary password via email
            $mail = new PHPMailer(true);
            try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nasiryt.827@gmail.com';  
        $mail->Password   = 'ddgc zhft gohg bjmz';    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('nasiryt.827@gmail.com', 'MMGC-Clinic');
                $mail->addAddress($email, $row['name']);

                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body    = "
                    <p>Hi {$row['name']},</p>
                    <p>Your temporary password is: <strong>$tempPassword</strong></p>
                    <p>Please login and change your password immediately.</p>
                ";

                $mail->send();
                $msg = "<div class='alert alert-success'>Temporary password sent to your email</div>";
            } catch (Exception $e) {
                $msg = "<div class='alert alert-danger'>Mailer Error: {$mail->ErrorInfo}</div>";
            }

        } else {
            $msg = "<div class='alert alert-danger'>Email not found in patient records</div>";
        }

        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Forgot Password</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="card mx-auto shadow-sm" style="max-width: 450px;">
        <div class="card-body">
            <h3 class="text-center mb-3">Patient Password Reset</h3>

            <p class="text-info">Only patients can reset their password using this form.</p>

            <?php echo $msg; ?>

            <form method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <button class="btn btn-primary btn-block">Send Reset Link</button>
                <a href="login.php" class="btn btn-secondary btn-block">Back to Login</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>

