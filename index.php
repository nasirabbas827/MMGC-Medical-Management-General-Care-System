<?php
session_start();
include('config.php');

// Stats
$total_patients     = $conn->query("SELECT COUNT(*) FROM patients WHERE status = 'active'")->fetch_row()[0];
$total_doctors      = $conn->query("SELECT COUNT(*) FROM doctors WHERE status = 'active'")->fetch_row()[0];
$total_appointments = $conn->query("SELECT COUNT(*) FROM appointments")->fetch_row()[0];
$total_lab_tests    = $conn->query("SELECT COUNT(*) FROM lab_tests")->fetch_row()[0];
$today_appointments = $conn->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()")->fetch_row()[0];

// Doctors (added id)
$doctors = $conn->query("SELECT id, full_name, specialization, qualification, profile_image, experience_years
                         FROM doctors WHERE status = 'active' LIMIT 6");

// Patient stories
$patient_stories = $conn->query("SELECT p.name, pr.procedure_type, pr.outcome, pr.created_at
                                 FROM procedures pr
                                 JOIN patients p ON pr.patient_id = p.id
                                 WHERE pr.outcome IS NOT NULL AND pr.outcome != ''
                                 ORDER BY pr.created_at DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>MMGC Hospital – Compassionate & Advanced Care in Karachi</title>
    <meta name="description" content="Trusted healthcare in Karachi since 2020. Expert doctors, maternity, diagnostics, emergency & more. Book your appointment today.">
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">

    
    <style>
        :root {
            --primary:    #2563eb;       /* modern blue */
            --primary-dark: #1d4ed8;
            --accent:     #10b981;       /* emerald green - calming & trust */
            --gray-dark:  #1f2937;
            --gray:       #4b5563;
            --light:      #f9fafb;
            --shadow-sm:  0 4px 12px rgba(0,0,0,0.08);
            --shadow:     0 10px 30px rgba(0,0,0,0.10);
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            color: #1f2937;
            background: var(--light);
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--gray-dark);
        }

        /* Hero */
        .hero {
            background: linear-gradient(rgba(37,99,235,0.88), rgba(29,78,216,0.92)),
                        url('./images/hotel.jpg') center/cover no-repeat;
            min-height: 90vh;
            color: white;
            position: relative;
        }
        .hero-content {
            max-width: 780px;
        }
        .hero-title {
            font-size: 3.8rem;
            font-weight: 800;
            line-height: 1.1;
            text-shadow: 0 3px 12px rgba(0,0,0,0.5);
        }
        .hero-subtitle {
            font-size: 1.4rem;
            font-weight: 400;
            max-width: 680px;
            opacity: 0.95;
        }
        .btn-hero {
            padding: 0.85rem 2.4rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50rem;
            transition: all 0.3s ease;
        }
        .btn-hero-primary {
            background: var(--accent);
            border: none;
        }
        .btn-hero-primary:hover {
            background: #059669;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16,185,129,0.35);
        }
        .btn-hero-outline {
            border: 2px solid white;
        }
        .btn-hero-outline:hover {
            background: white;
            color: var(--primary);
        }

        /* Cards */
        .card-modern {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.32s ease;
            background: white;
        }
        .card-modern:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.14);
        }
        .service-icon {
            font-size: 3.5rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .doctor-img-wrapper {
            position: relative;
            overflow: hidden;
            height: 260px;
        }
        .doctor-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.45s ease;
        }
        .card-modern:hover .doctor-img-wrapper img {
            transform: scale(1.08);
        }

        .story-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--accent);
        }

        /* Sections */
        section {
            padding: 7rem 0;
        }
        .section-title {
            font-size: 2.6rem;
            margin-bottom: 1.2rem;
        }
        .section-lead {
            font-size: 1.25rem;
            color: var(--gray);
            max-width: 720px;
            margin: 0 auto 3rem;
        }

        .bg-gradient-contact {
            background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%);
            color: white;
        }

        footer {
            background: var(--gray-dark);
            color: #d1d5db;
        }

        @media (max-width: 992px) {
            .hero-title { font-size: 3.1rem; }
        }
        @media (max-width: 768px) {
            .hero-title { font-size: 2.6rem; }
            .hero-subtitle { font-size: 1.2rem; }
            section { padding: 5rem 0; }
        }
        .filter-wrapper {
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: var(--shadow);
}

#teamSearchForm .form-control {
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.badge-primary { background-color: var(--primary); }
.badge-info { background-color: #17a2b8; }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<!-- Hero -->
<section class="hero d-flex align-items-center text-center">
    <div class="container">
        <div class="hero-content mx-auto">
            <h1 class="hero-title mb-4 text-white">MMGC Hospital</h1>
            <p class="hero-subtitle mb-5">Compassionate Care • Advanced Diagnostics • Trusted Healing Since 2020</p>
            <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                <a href="#contact" class="btn btn-hero btn-hero-primary text-white mr-2">
                    <i class="fas fa-calendar-check me-2 text-white"></i> Book Appointment
                </a>
                <a href="#services" class="btn btn-hero btn-hero-outline text-white mr-2">
                    <i class="fas fa-stethoscope me-2 text-white"></i> Our Services
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="bg-white">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3 col-6">
                <div class="card-modern p-4">
                    <i class="fas fa-users service-icon mb-3"></i>
                    <h3 class="fw-bold"><?= number_format($total_patients) ?>+</h3>
                    <p class="text-muted mb-0">Happy Patients</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-modern p-4">
                    <i class="fas fa-user-md service-icon mb-3"></i>
                    <h3 class="fw-bold"><?= $total_doctors ?></h3>
                    <p class="text-muted mb-0">Expert Doctors</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-modern p-4">
                    <i class="fas fa-calendar-day service-icon mb-3"></i>
                    <h3 class="fw-bold"><?= $today_appointments ?></h3>
                    <p class="text-muted mb-0">Today’s Appointments</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-modern p-4">
                    <i class="fas fa-flask service-icon mb-3"></i>
                    <h3 class="fw-bold"><?= number_format($total_lab_tests) ?>+</h3>
                    <p class="text-muted mb-0">Lab Tests</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services -->
<section id="services" class="bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Comprehensive Healthcare Services</h2>
            <p class="section-lead">Quality care across multiple specialties – all under one roof</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-modern text-center p-5">
                    <i class="fas fa-stethoscope service-icon mb-4"></i>
                    <h4>General & Specialist Consultations</h4>
                    <p class="text-muted">Expert diagnosis and personalized treatment plans</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-modern text-center p-5">
                    <i class="fas fa-baby service-icon mb-4"></i>
                    <h4>Maternity & Pediatric Care</h4>
                    <p class="text-muted">From prenatal support to child wellness programs</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-modern text-center p-5">
                    <i class="fas fa-x-ray service-icon mb-4"></i>
                    <h4>Advanced Diagnostics & Imaging</h4>
                    <p class="text-muted">Fast, accurate lab tests, ultrasound & X-ray services</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Doctors & Staff Search Section -->
<section id="doctors" class="bg-white py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Our Medical Team</h2>
            <p class="section-lead">Find experienced doctors and dedicated support staff</p>
        </div>

        <!-- Search & Filter Bar -->
        <div class="filter-wrapper mb-5">
            <form method="GET" class="form-row align-items-end justify-content-center">
                <div class="col-md-3 my-2">
                    <input type="text" name="search_name" class="form-control" 
                           placeholder="Search by name..." 
                           value="<?= htmlspecialchars($_GET['search_name'] ?? '') ?>">
                </div>
                <div class="col-md-3 my-2">
                    <select name="team_type" class="form-control">
                        <option value="all">All Team Members</option>
                        <option value="doctor" <?= ($_GET['team_type'] ?? '') === 'doctor' ? 'selected' : '' ?>>Doctors Only</option>
                        <option value="staff" <?= ($_GET['team_type'] ?? '') === 'staff' ? 'selected' : '' ?>>Staff Only</option>
                    </select>
                </div>
                <div class="col-md-3 my-2">
                    <select name="specialization" class="form-control">
                        <option value="">All Specializations / Roles</option>
                        <?php 
                        $spec_res = $conn->query("SELECT DISTINCT specialization FROM doctors WHERE status='active' ORDER BY specialization");
                        while ($spec = $spec_res->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($spec['specialization']) ?>" 
                                <?= ($_GET['specialization'] ?? '') === $spec['specialization'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($spec['specialization']) ?> (Doctor)
                            </option>
                        <?php endwhile; ?>

                        <?php 
                        $role_res = $conn->query("SELECT DISTINCT staff_role FROM staff WHERE status='active' ORDER BY staff_role");
                        while ($role = $role_res->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($role['staff_role']) ?>" 
                                <?= ($_GET['specialization'] ?? '') === $role['staff_role'] ? 'selected' : '' ?>>
                                <?= ucfirst(htmlspecialchars($role['staff_role'])) ?> (Staff)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2 my-2">
                    <button type="submit" class="btn btn-primary btn-block">Search Team</button>
                </div>
            </form>
        </div>

        <?php
        /* =====================
           FETCH DOCTORS & STAFF
        ===================== */
        $search_name = $_GET['search_name'] ?? '';
        $team_type   = $_GET['team_type'] ?? 'all';
        $filter_spec = $_GET['specialization'] ?? '';

        $doctors = [];
        $staff_members = [];

        // Fetch Doctors
        if ($team_type === 'all' || $team_type === 'doctor') {
            $doc_query = "SELECT id, full_name, specialization, qualification, profile_image, experience_years, 'doctor' as member_type 
                          FROM doctors WHERE status = 'active'";
            $params = [];
            $types = '';

            if (!empty($search_name)) {
                $doc_query .= " AND full_name LIKE ?";
                $params[] = "%$search_name%";
                $types .= 's';
            }
            if (!empty($filter_spec)) {
                $doc_query .= " AND specialization = ?";
                $params[] = $filter_spec;
                $types .= 's';
            }
            $doc_query .= " ORDER BY full_name ASC LIMIT 12";

            $stmt = $conn->prepare($doc_query);
            if (!empty($params)) $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $doctors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        // Fetch Staff
        if ($team_type === 'all' || $team_type === 'staff') {
            $staff_query = "SELECT id, full_name, staff_role as specialization, NULL as qualification, 
                                   NULL as profile_image, NULL as experience_years, 'staff' as member_type 
                            FROM staff WHERE status = 'active'";
            $params = [];
            $types = '';

            if (!empty($search_name)) {
                $staff_query .= " AND full_name LIKE ?";
                $params[] = "%$search_name%";
                $types .= 's';
            }
            if (!empty($filter_spec)) {
                $staff_query .= " AND staff_role = ?";
                $params[] = $filter_spec;
                $types .= 's';
            }
            $staff_query .= " ORDER BY full_name ASC LIMIT 12";

            $stmt = $conn->prepare($staff_query);
            if (!empty($params)) $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $staff_members = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        $all_team = array_merge($doctors, $staff_members);
        ?>

        <!-- Team Members Grid -->
        <div class="row g-4">
            <?php if (empty($all_team)): ?>
                <div class="col-12 text-center py-5">
                    <img src="https://illustrations.popsy.co/gray/doctor.svg" alt="No results" style="width:180px;" class="mb-3">
                    <p class="text-muted">No team members found matching your search.</p>
                </div>
            <?php else: ?>
                <?php foreach ($all_team as $member): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card-modern h-100">
                            <div class="doctor-img-wrapper">
                                <?php if ($member['member_type'] === 'doctor'): ?>
                                    <?php if (!empty($member['profile_image']) && file_exists("admin/" . $member['profile_image'])): ?>
                                        <img src="admin/<?= htmlspecialchars($member['profile_image']) ?>" 
                                             alt="<?= htmlspecialchars($member['full_name']) ?>">
                                    <?php else: ?>
                                        <img src="images/doctor-placeholder.jpg" 
                                             alt="<?= htmlspecialchars($member['full_name']) ?>">
                                    <?php endif; ?>
                                <?php else: ?>
                                    <!-- Your own default staff picture -->
                                    <img src="images/default.png" 
                                         alt="<?= htmlspecialchars($member['full_name']) ?>" 
                                         style="object-fit: cover; width: 100%; height: 100%;">
                                <?php endif; ?>
                            </div>
                            <div class="p-4 text-center">
                                <h5 class="mb-1"><?= htmlspecialchars($member['full_name']) ?></h5>
                                
                                <div class="text-primary fw-bold mb-2">
                                    <?= htmlspecialchars($member['specialization']) ?>
                                    <span class="badge badge-pill badge-<?= $member['member_type'] === 'doctor' ? 'primary' : 'info' ?> ml-1">
                                        <?= ucfirst($member['member_type']) ?>
                                    </span>
                                </div>

                                <?php if ($member['member_type'] === 'doctor'): ?>
                                    <div class="text-muted small mb-3">
                                        <?= $member['experience_years'] ? $member['experience_years'] . '+ years experience' : 'Experienced Specialist' ?>
                                    </div>
                                    <?php if (!empty($member['qualification'])): ?>
                                        <div class="text-muted small mb-3">
                                            <?= htmlspecialchars($member['qualification']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <a href="doctors.php" class="btn btn-outline-primary btn-block">
                                        View Profile & Book
                                    </a>
                                <?php else: ?>
                                    <div class="text-muted small mb-3">
                                        <?= ucfirst($member['specialization']) ?> • Hospital Staff
                                    </div>
                                    <a href="#" onclick="alert('Please contact reception at +92 300 1234567 to speak with our staff.'); return false;" 
                                       class="btn btn-outline-secondary btn-block">
                                        Contact Staff
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($all_team)): ?>
        <div class="text-center mt-5">
            <a href="doctors.php" class="btn btn-primary btn-lg px-5">View Complete Team</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Patient Stories -->
<section id="stories" class="bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Patient Success Stories</h2>
            <p class="section-lead">Real people. Real care. Real results.</p>
        </div>
        <div class="row g-4">
            <?php if ($patient_stories->num_rows > 0): ?>
                <?php while ($story = $patient_stories->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card-modern p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="story-avatar me-3">
                                <img src="images/patient.jpg" alt="Patient" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div>
                                <h5 class="mb-0"><?= htmlspecialchars($story['name']) ?></h5>
                                <small class="text-primary"><?= htmlspecialchars($story['procedure_type']) ?></small>
                            </div>
                        </div>
                        <p class="fst-italic text-muted mb-3">"<?= htmlspecialchars(substr($story['outcome'], 0, 140)) ?>…"</p>
                        <small class="text-muted">
                            <i class="far fa-calendar-alt me-1"></i>
                            <?= date('F j, Y', strtotime($story['created_at'])) ?>
                        </small>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="alert alert-info">Patient stories coming soon…</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Contact -->
<section id="contact" class="bg-gradient-contact">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <h2 class="mb-4 text-white">Get in Touch Today</h2>
                <p class="lead mb-4">We're ready to assist you 24/7.</p>
                <p><i class="fas fa-phone me-2"></i> +92 300 1234567</p>
                <p><i class="fas fa-envelope me-2"></i> info@mmgchospital.com</p>
                <p><i class="fas fa-map-marker-alt me-2"></i> Gulshan-e-Iqbal, Karachi, Pakistan</p>
                <p><i class="fas fa-clock me-2"></i> 24/7 Emergency Services Available</p>
            </div>
            <div class="col-lg-7">
                <div class="card-modern contact-form p-4 p-md-5">
                    <h3 class="mb-4 text-dark">Send Us a Message</h3>
                    <form action="send-message.php" method="post">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control form- m-2" placeholder="Full Name" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control form-control m-2" placeholder="Email Address" required>
                            </div>
                            <div class="col-12">
                                <input type="text" name="subject" class="form-control form-control m-2" placeholder="Subject" required>
                            </div>
                            <div class="col-12">
                                <textarea name="message" class="form-control form-control m-2" rows="5" placeholder="How can we help you?" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-hero btn-hero-primary btn-lg w-100">
                                    <i class="fas fa-paper-plane me-2"></i> Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h4 class="text-white mb-3">MMGC Hospital</h4>
                <p class="text-muted">Delivering compassionate, modern healthcare with trust and excellence.</p>
                <div class="mt-3">
                    <a href="#" class="text-white fs-4 me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white fs-4 me-3"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" class="text-white fs-4"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <h5 class="text-white mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="#services" class="text-muted text-decoration-none">Services</a></li>
                    <li><a href="#doctors" class="text-muted text-decoration-none">Doctors</a></li>
                    <li><a href="#stories" class="text-muted text-decoration-none">Stories</a></li>
                    <li><a href="#contact" class="text-muted text-decoration-none">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-4">
                <h5 class="text-white mb-3">Services</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-muted text-decoration-none">Emergency Care</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">Maternity</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">Diagnostics</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">General OPD</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-4">
                <h5 class="text-white mb-3">Contact</h5>
                <p class="mb-2"><i class="fas fa-phone me-2"></i> +92 300 1234567</p>
                <p class="mb-2"><i class="fas fa-envelope me-2"></i> info@mmgchospital.com</p>
                <p><i class="fas fa-map-marker-alt me-2"></i> Karachi, Pakistan</p>
            </div>
        </div>
        <div class="text-center mt-5 pt-4 border-top border-secondary">
            <p class="mb-0">© <?= date('Y') ?> MMGC Hospital. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href'))?.scrollIntoView({
            behavior: 'smooth'
        });
    });
});
</script>
</body>
</html>