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
   FETCH DOCTORS
===================== */
$search_name = $_GET['search_name'] ?? '';
$filter_specialization = $_GET['specialization'] ?? '';
$sort_order = $_GET['sort_order'] ?? 'ASC';

$query = "SELECT * FROM doctors WHERE status='active'";
$params = [];
$types = '';

if (!empty($search_name)) {
    $query .= " AND full_name LIKE ?";
    $params[] = "%$search_name%";
    $types .= 's';
}

if (!empty($filter_specialization)) {
    $query .= " AND specialization = ?";
    $params[] = $filter_specialization;
    $types .= 's';
}

$query .= " ORDER BY full_name $sort_order";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$doctors = $result->fetch_all(MYSQLI_ASSOC);

/* =====================
   FETCH SPECIALIZATIONS
===================== */
$spec_res = $conn->query("SELECT DISTINCT specialization FROM doctors WHERE status='active'");
$specializations = [];
while ($row = $spec_res->fetch_assoc()) {
    $specializations[] = $row['specialization'];
}

/* =====================
   FETCH DOCTOR SLOTS FUNCTION
===================== */
function get_available_slots($conn, $doctor_id) {
    $slots = [];
    $stmt = $conn->prepare("SELECT day_of_week, start_time, end_time, slot_duration_minutes 
                            FROM doctor_schedules WHERE doctor_id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $slots[$row['day_of_week']] = [
            'start_time' => $row['start_time'],
            'end_time' => $row['end_time'],
            'duration' => $row['slot_duration_minutes']
        ];
    }
    return $slots;
}

/* =====================
   BOOK APPOINTMENT HANDLER
===================== */
$booking_msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_appointment'])) {
    $doctor_id = intval($_POST['doctor_id']);
    $appointment_date = $_POST['appointment_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $appointment_type = $_POST['appointment_type'];
    $notes = $_POST['notes'];

    // Check if slot is already booked
    $stmt_check = $conn->prepare("SELECT * FROM appointments WHERE doctor_id=? AND appointment_date=? AND ((start_time BETWEEN ? AND ?) OR (end_time BETWEEN ? AND ?))");
    $stmt_check->bind_param("isssss", $doctor_id, $appointment_date, $start_time, $end_time, $start_time, $end_time);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if ($res_check->num_rows > 0) {
        $booking_msg = '<div class="alert alert-danger">This time slot is already booked. Please choose another slot.</div>';
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO appointments (doctor_id, patient_id, appointment_date, start_time, end_time, appointment_type, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("iisssss", $doctor_id, $patient_id, $appointment_date, $start_time, $end_time, $appointment_type, $notes);
        if ($stmt_insert->execute()) {
            $booking_msg = '<div class="alert alert-success">Appointment booked successfully!</div>';
        } else {
            $booking_msg = '<div class="alert alert-danger">Error booking appointment. Try again.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Find Your Specialist</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">

<style>
    :root { --primary-color: #4e73df; --secondary-color: #f8f9fc; --accent-color: #1cc88a; --text-main: #2e384d; }
    .section-title { font-weight:700; color:#4a4a4a; margin-bottom:30px; }
    .filter-wrapper { background:white; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.05); margin-bottom:40px; }
    .doctor-card { border:none; border-radius:15px; transition: transform 0.3s ease, box-shadow 0.3s ease; overflow:hidden; background:#fff; }
    .doctor-card:hover { transform:translateY(-5px); box-shadow:0 10px 20px rgba(0,0,0,0.1); }
    .doctor-card img { width:100%; height:220px; object-fit:cover; transition: transform 0.5s ease; }
    .doctor-card:hover img { transform:scale(1.05); }
    .specialty-badge { background: rgba(78,115,223,0.1); color:var(--primary-color); padding:4px 12px; border-radius:20px; font-size:0.85rem; font-weight:600; display:inline-block; margin-bottom:10px; }
    .card-body { padding:1.5rem; display:flex; flex-direction:column; }
    .btn-book { background-color:var(--accent-color); border:none; font-weight:600; border-radius:8px; color:white; }
    .btn-profile { background-color:transparent; border:1px solid #ddd; color:#666; border-radius:8px; font-weight:600; }
    .modal-content { border:none; border-radius:20px; }
    .modal-header { border-bottom:1px solid #f0f0f0; padding:1.5rem; }
</style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <?php if ($booking_msg) echo $booking_msg; ?>
    <div class="text-center">
        <h2 class="section-title">Consult with our Experts</h2>
        <p class="text-muted mb-5">Book an appointment with top-rated specialists in just a few clicks.</p>
    </div>

    <div class="filter-wrapper">
        <form method="GET" class="form-row align-items-center justify-content-center">
            <div class="col-md-3 my-1">
                <input type="text" name="search_name" class="form-control border-0 bg-light" placeholder="Search by name..." value="<?php echo htmlspecialchars($search_name); ?>">
            </div>
            <div class="col-md-3 my-1">
                <select name="specialization" class="form-control border-0 bg-light">
                    <option value="">All Specializations</option>
                    <?php foreach ($specializations as $spec): ?>
                        <option value="<?php echo htmlspecialchars($spec); ?>" <?php if($filter_specialization==$spec) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($spec); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 my-1">
                <select name="sort_order" class="form-control border-0 bg-light">
                    <option value="ASC" <?php if($sort_order=='ASC') echo 'selected'; ?>>Name: A-Z</option>
                    <option value="DESC" <?php if($sort_order=='DESC') echo 'selected'; ?>>Name: Z-A</option>
                </select>
            </div>
            <div class="col-md-2 my-1">
                <button class="btn btn-primary btn-block shadow-sm">Apply Filters</button>
            </div>
        </form>
    </div>

    <div class="row">
        <?php if (empty($doctors)): ?>
            <div class="col-12 text-center py-5">
                <img src="https://illustrations.popsy.co/gray/doctor.svg" alt="No data" style="width:200px;" class="mb-3">
                <p class="text-muted">No doctors found.</p>
            </div>
        <?php else: ?>
            <?php foreach ($doctors as $doc): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card doctor-card shadow-sm h-100">
                        <div class="img-container">
                            <img src="<?php echo !empty($doc['profile_image']) ? "../admin/".htmlspecialchars($doc['profile_image']) : "../uploads/default-doctor.png"; ?>" alt="Doctor">
                        </div>
                        <div class="card-body">
                            <span class="specialty-badge"><?php echo htmlspecialchars($doc['specialization']); ?></span>
                            <h5 class="font-weight-bold mb-1"><?php echo htmlspecialchars($doc['full_name']); ?></h5>
                            <p class="text-muted small mb-3"><i class="far fa-clock mr-1"></i> <?php echo $doc['experience_years']; ?>+ Years Experience</p>

                            <div class="mt-auto pt-3 border-top">
                                <div class="row no-gutters">
                                    <div class="col-6 pr-1">
                                        <button class="btn btn-profile btn-sm btn-block" data-toggle="modal" data-target="#profileModal<?php echo $doc['id']; ?>">Details</button>
                                    </div>
                                    <div class="col-6 pl-1">
                                        <button class="btn btn-book btn-sm btn-block" data-toggle="modal" data-target="#bookModal<?php echo $doc['id']; ?>">Book Now</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PROFILE MODAL -->
                <div class="modal fade" id="profileModal<?php echo $doc['id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?php echo htmlspecialchars($doc['full_name']); ?> - Profile</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="<?php echo !empty($doc['profile_image']) ? "../admin/".htmlspecialchars($doc['profile_image']) : "../uploads/default-doctor.png"; ?>" 
                                     class="rounded mb-3" style="width:150px;height:150px;object-fit:cover;">
                                <p><strong>Specialization:</strong> <?php echo htmlspecialchars($doc['specialization']); ?></p>
                                <p><strong>Experience:</strong> <?php echo $doc['experience_years']; ?> years</p>
                                <p><strong>Qualification:</strong> <?php echo htmlspecialchars($doc['qualification']); ?></p>
                                <p><strong>Bio:</strong> <?php echo htmlspecialchars($doc['bio']); ?></p>
                                <p><strong>Availability:</strong></p>
                                <ul class="list-unstyled">
                                    <?php 
                                    $slots = get_available_slots($conn, $doc['id']);
                                    foreach ($slots as $day => $slot): ?>
                                        <li><?php echo $day; ?>: <?php echo date('h:i A', strtotime($slot['start_time'])); ?> - <?php echo date('h:i A', strtotime($slot['end_time'])); ?> (<?php echo $slot['duration']; ?> min slots)</li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOOK MODAL -->
                <div class="modal fade" id="bookModal<?php echo $doc['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Book Appointment with <?php echo htmlspecialchars($doc['full_name']); ?></h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="doctor_id" value="<?php echo $doc['id']; ?>">
                                    <input type="hidden" name="book_appointment" value="1">

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>Date</label>
                                            <input type="date" name="appointment_date" class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Start Time</label>
                                            <input type="time" name="start_time" class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>End Time</label>
                                            <input type="time" name="end_time" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>Type</label>
                                            <select name="appointment_type" class="form-control">
                                                <option value="new">New</option>
                                                <option value="follow-up">Follow-up</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Notes</label>
                                            <input type="text" name="notes" class="form-control" placeholder="Optional">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Book Appointment</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
</body>
</html>
