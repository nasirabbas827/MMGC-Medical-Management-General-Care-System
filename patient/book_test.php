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
$booking_msg = '';

/* =====================
   FETCH DOCTORS (for procedures)
===================== */
$doctors_res = $conn->query("SELECT id, full_name, specialization FROM doctors WHERE status='active'");
$doctors = [];
while ($row = $doctors_res->fetch_assoc()) {
    $doctors[] = $row;
}

/* =====================
   FETCH LAB CATEGORIES
===================== */
$lab_cat_res = $conn->query("SELECT * FROM lab_categories ORDER BY name ASC");
$lab_categories = [];
while ($row = $lab_cat_res->fetch_assoc()) {
    $lab_categories[] = $row;
}

/* =====================
   FETCH PROCEDURES
===================== */
$procedures_res = $conn->query("SELECT p.*, d.full_name AS doctor_name 
                                FROM procedures p 
                                JOIN doctors d ON p.doctor_id=d.id 
                                ORDER BY p.created_at DESC");
$procedures = [];
while ($row = $procedures_res->fetch_assoc()) {
    $procedures[] = $row;
}

/* =====================
   HANDLE LAB TEST BOOKING
===================== */
if (isset($_POST['book_lab'])) {
    $category_id = intval($_POST['lab_category']);
    $test_name = trim($_POST['test_name']);
    $doctor_id = intval($_POST['doctor_id']); // optional
    $is_urgent = isset($_POST['is_urgent']) ? 1 : 0;

    // get default price
    $stmt = $conn->prepare("SELECT default_price FROM lab_categories WHERE id=?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $price = $res['default_price'] ?? 0;

    // insert lab test
    $stmt = $conn->prepare("INSERT INTO lab_tests (patient_id, doctor_id, category_id, test_name, price, is_urgent) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $patient_id, $doctor_id, $category_id, $test_name, $price, $is_urgent);
    if ($stmt->execute()) {
        $booking_msg = '<div class="alert alert-success">Lab test booked successfully!</div>';
    } else {
        $booking_msg = '<div class="alert alert-danger">Error booking lab test.</div>';
    }
}

/* =====================
   HANDLE PROCEDURE REQUEST
===================== */
if (isset($_POST['request_procedure'])) {
    $doctor_id = intval($_POST['procedure_doctor']);
    $procedure_type = $_POST['procedure_type'];
    $procedure_name = trim($_POST['procedure_name']);
    $procedure_date = $_POST['procedure_date'];
    $procedure_time = $_POST['procedure_time'] ?? null;

    // insert procedure request
    $stmt = $conn->prepare("INSERT INTO procedures (patient_id, doctor_id, procedure_type, procedure_name, procedure_date, procedure_time) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $patient_id, $doctor_id, $procedure_type, $procedure_name, $procedure_date, $procedure_time);
    if ($stmt->execute()) {
        $booking_msg = '<div class="alert alert-success">Procedure request submitted successfully!</div>';
    } else {
        $booking_msg = '<div class="alert alert-danger">Error submitting procedure request.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lab & Procedure Booking</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/style.css">
<style>
    .section-title { font-weight: 700; margin-bottom: 20px; }
    .table-wrapper { margin-bottom: 50px; }
</style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <?php if($booking_msg) echo $booking_msg; ?>

    <!-- LAB TESTS -->
    <div class="table-wrapper">
        <div class="d-flex justify-content-between mb-2">
            <h4 class="section-title">Laboratory Categories</h4>
            <input type="text" id="labSearch" class="form-control w-25" placeholder="Search Lab Categories...">
        </div>
        <table class="table table-bordered table-striped" id="labTable">
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Default Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($lab_categories as $lab): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($lab['name']); ?></td>
                        <td><?php echo htmlspecialchars($lab['description']); ?></td>
                        <td><?php echo number_format($lab['default_price'],2); ?></td>
                        <td>
                            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#labModal<?php echo $lab['id']; ?>">Book Now</button>
                        </td>
                    </tr>

                    <!-- LAB BOOKING MODAL -->
                    <div class="modal fade" id="labModal<?php echo $lab['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST">
                                    <input type="hidden" name="book_lab" value="1">
                                    <input type="hidden" name="lab_category" value="<?php echo $lab['id']; ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Book: <?php echo htmlspecialchars($lab['name']); ?></h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Test Name</label>
                                            <input type="text" name="test_name" class="form-control" value="<?php echo htmlspecialchars($lab['name']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Doctor (optional)</label>
                                            <select name="doctor_id" class="form-control">
                                                <option value="0">No doctor</option>
                                                <?php foreach($doctors as $doc): ?>
                                                    <option value="<?php echo $doc['id']; ?>"><?php echo htmlspecialchars($doc['full_name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="is_urgent" class="form-check-input" id="urgent<?php echo $lab['id']; ?>">
                                            <label class="form-check-label" for="urgent<?php echo $lab['id']; ?>">Urgent</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-success">Book Now</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- PROCEDURES -->
    <div class="table-wrapper">
        <div class="d-flex justify-content-between mb-2">
            <h4 class="section-title">Procedure Requests</h4>
            <input type="text" id="procSearch" class="form-control w-25" placeholder="Search Procedures...">
        </div>
        <table class="table table-bordered table-striped" id="procTable">
            <thead class="thead-dark">
                <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($procedures as $proc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($proc['procedure_type']); ?></td>
                        <td><?php echo htmlspecialchars($proc['procedure_name']); ?></td>
                        <td><?php echo htmlspecialchars($proc['doctor_name']); ?></td>
                        <td><?php echo $proc['procedure_date']; ?></td>
                        <td><?php echo $proc['procedure_time']; ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#procModal<?php echo $proc['id']; ?>">Request</button>
                        </td>
                    </tr>

                    <!-- PROCEDURE REQUEST MODAL -->
                    <div class="modal fade" id="procModal<?php echo $proc['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST">
                                    <input type="hidden" name="request_procedure" value="1">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Request Procedure</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Procedure Type</label>
                                            <select name="procedure_type" class="form-control" required>
                                                <option value="Normal Delivery">Normal Delivery</option>
                                                <option value="C-Section">C-Section</option>
                                                <option value="Ultrasound">Ultrasound</option>
                                                <option value="Gynaecology Treatment">Gynaecology Treatment</option>
                                                <option value="Surgery">Surgery</option>
                                                <option value="OPD Consultation">OPD Consultation</option>
                                                <option value="IPD Admission">IPD Admission</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Procedure Name</label>
                                            <input type="text" name="procedure_name" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Doctor</label>
                                            <select name="procedure_doctor" class="form-control" required>
                                                <option value="">Select Doctor</option>
                                                <?php foreach($doctors as $doc): ?>
                                                    <option value="<?php echo $doc['id']; ?>"><?php echo htmlspecialchars($doc['full_name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Date</label>
                                                <input type="date" name="procedure_date" class="form-control" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Time</label>
                                                <input type="time" name="procedure_time" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-warning">Request Procedure</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Table Search Script -->
<script>
    $(document).ready(function(){
        $("#labSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#labTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        $("#procSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#procTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
</body>
</html>
