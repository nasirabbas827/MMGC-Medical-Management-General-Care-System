<?php
session_start();
include('config.php');

// Fetch Stats for Public Display
$total_patients = $conn->query("SELECT COUNT(*) FROM patients WHERE status = 'active'")->fetch_row()[0];
$total_doctors = $conn->query("SELECT COUNT(*) FROM doctors WHERE status = 'active'")->fetch_row()[0];
$total_appointments = $conn->query("SELECT COUNT(*) FROM appointments")->fetch_row()[0];
$total_lab_tests = $conn->query("SELECT COUNT(*) FROM lab_tests")->fetch_row()[0];

// Today's Appointments Count
$today_appointments = $conn->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()")->fetch_row()[0];

// Recent Doctors for Showcase
$doctors = $conn->query("SELECT full_name, specialization, qualification, profile_image FROM doctors WHERE status = 'active' LIMIT 6");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediCare Hospital - Best Healthcare in Town</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        :root {
            --primary: #007bff;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --accent: #007bff;
            --highlight: #28a745;
        }

        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; }
        .hero-section {
            height: 90vh;
            min-height: 600px;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('./images/hotel.jpg') center/cover no-repeat;
            color: white;
            display: flex;
            align-items: center;
            text-align: center;
            border-bottom: 8px solid var(--highlight);
        }
        .hero-section h1 {
            font-size: 4.5rem;
            font-weight: 800;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.6);
        }
        .hero-section p.lead {
            font-size: 1.4rem;
            max-width: 800px;
            margin: 20px auto;
        }
        .btn-custom {
            padding: 14px 40px;
            font-size: 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-10px);
        }
        .stat-icon {
            font-size: 3.5rem;
            color: var(--accent);
            margin-bottom: 15px;
        }
        .section {
            padding: 100px 0;
        }
        .doctor-card img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 5px solid var(--light);
            margin: 0 auto 15px;
        }
        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        footer {
            background: var(--dark);
            color: white;
            padding: 60px 0 30px;
        }
        .feature-icon {
            font-size: 3.5rem;
            color: var(--highlight);
            margin-bottom: 20px;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<?php include('navbar.php'); ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1 class="display-3 text-white">Welcome to MediCare Hospital</h1>
        <p class="lead">Your Trusted Partner in Health & Wellness</p>
        <p>We provide world-class medical care with compassion, expertise, and cutting-edge technology.</p>
        
        <?php if (!isset($_SESSION['id'])): ?>
            <div class="mt-4">
                <a href="login.php " class="btn btn-primary btn-custom mr-3">
                    <i class="fas fa-user-injured"></i> Patient Login
                </a>
                <a href="login.php " class="btn btn-outline-light btn-custom">
                    <i class="fas fa-user-md"></i> Doctor Login
                </a>
            </div>
        <?php else: ?>
            <a href="login.php " class="btn btn-success btn-custom">
                <i class="fas fa-tachometer-alt"></i> Go to Dashboard
            </a>
        <?php endif; ?>
    </div>
</section>

<!-- Stats Section -->
<section class="section bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <i class="fas fa-user-injured stat-icon"></i>
                    <h3 class="text-primary"><?php echo number_format($total_patients); ?>+</h3>
                    <p class="text-muted">Happy Patients</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <i class="fas fa-user-md stat-icon"></i>
                    <h3 class="text-success"><?php echo $total_doctors; ?></h3>
                    <p class="text-muted">Expert Doctors</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <i class="fas fa-calendar-check stat-icon"></i>
                    <h3 class="text-info"><?php echo $today_appointments; ?></h3>
                    <p class="text-muted">Appointments Today</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <i class="fas fa-flask stat-icon"></i>
                    <h3 class="text-warning"><?php echo number_format($total_lab_tests); ?>+</h3>
                    <p class="text-muted">Lab Tests Conducted</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services / Features -->
<section class="section">
    <div class="container">
        <h2 class="text-center mb-5 section-title" style="font-weight:800; color:var(--dark);">Our Services</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card text-center p-4">
                    <i class="fas fa-stethoscope feature-icon"></i>
                    <h5>General Checkup</h5>
                    <p>Routine health checkups and preventive care for all ages.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center p-4">
                    <i class="fas fa-baby feature-icon"></i>
                    <h5>Maternity & Child Care</h5>
                    <p>Complete care for mothers and newborns with expert gynecologists.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center p-4">
                    <i class="fas fa-x-ray feature-icon"></i>
                    <h5>Diagnostic Services</h5>
                    <p>Advanced lab tests, ultrasound, X-ray, and instant reports.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center p-4">
                    <i class="fas fa-syringe feature-icon"></i>
                    <h5>Emergency Care</h5>
                    <p>24/7 emergency services with rapid response team.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center p-4">
                    <i class="fas fa-user-nurse feature-icon"></i>
                    <h5>In-Patient Care</h5>
                    <p>Comfortable wards and dedicated nursing staff.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center p-4">
                    <i class="fas fa-prescription-bottle-alt feature-icon"></i>
                    <h5>Pharmacy</h5>
                    <p>In-house pharmacy with genuine medicines at fair prices.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Doctors -->
<section class="section bg-light">
    <div class="container">
        <h2 class="text-center mb-5 section-title" style="font-weight:800; color:var(--dark);">Meet Our Doctors</h2>
        <div class="row">
            <?php while ($doc = $doctors->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card doctor-card text-center">
                    <?php if ($doc['profile_image'] && file_exists("admin/" . $doc['profile_image'])): ?>
                        <img src="admin/<?php echo $doc['profile_image']; ?>" class="rounded-circle" alt="<?php echo htmlspecialchars($doc['full_name']); ?>">
                    <?php else: ?>
                        <img src="./images/doctor-placeholder.jpg" class="rounded-circle" alt="Doctor">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($doc['full_name']); ?></h5>
                        <p class="text-primary"><strong><?php echo htmlspecialchars($doc['specialization']); ?></strong></p>
                        <p class="text-muted small"><?php echo htmlspecialchars($doc['qualification']); ?></p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center mt-4">
            <a href="" class="btn btn-outline-primary btn-lg">View All Doctors</a>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="section bg-primary text-white text-center">
    <div class="container">
        <h2>Book Your Appointment Today</h2>
        <p class="lead">Get expert care from the best doctors in town</p>
        <a href="" class="btn btn-light btn-lg btn-custom">
            <i class="fas fa-calendar-plus"></i> Book Appointment
        </a>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>MediCare Hospital</h5>
                <p>Providing quality healthcare since 2020.<br>Trusted by thousands of patients.</p>
            </div>
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white">About Us</a></li>
                    <li><a href="#" class="text-white">Services</a></li>
                    <li><a href="#" class="text-white">Doctors</a></li>
                    <li><a href="#" class="text-white">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contact Info</h5>
                <p><i class="fas fa-phone"></i> +92 300 1234567<br>
                   <i class="fas fa-envelope"></i> info@medicarehospital.com<br>
                   <i class="fas fa-map-marker-alt"></i> Main Boulevard, Lahore, Pakistan</p>
            </div>
        </div>
        <hr class="bg-white">
        <p class="text-center mb-0">&copy; 2025 MediCare Hospital. All Rights Reserved.</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>