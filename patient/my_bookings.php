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
   FETCH PATIENT LAB TESTS
===================== */
$lab_tests_res = $conn->prepare("SELECT l.*, d.full_name AS doctor_name, c.name AS category_name 
                                 FROM lab_tests l 
                                 LEFT JOIN doctors d ON l.doctor_id = d.id 
                                 LEFT JOIN lab_categories c ON l.category_id = c.id
                                 WHERE l.patient_id=? 
                                 ORDER BY l.booked_at DESC");
$lab_tests_res->bind_param("i", $patient_id);
$lab_tests_res->execute();
$lab_tests_result = $lab_tests_res->get_result();
$lab_tests = $lab_tests_result->fetch_all(MYSQLI_ASSOC);

/* =====================
   FETCH PATIENT PROCEDURES
===================== */
$proc_res = $conn->prepare("SELECT p.*, d.full_name AS doctor_name 
                            FROM procedures p 
                            LEFT JOIN doctors d ON p.doctor_id=d.id 
                            WHERE p.patient_id=? 
                            ORDER BY p.created_at DESC");
$proc_res->bind_param("i", $patient_id);
$proc_res->execute();
$proc_result = $proc_res->get_result();
$procedures = $proc_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Bookings</title>
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
    <h2 class="section-title">My Lab Test Bookings</h2>
    <input type="text" id="labSearch" class="form-control mb-3 w-50" placeholder="Search Lab Tests...">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="labTable">
            <thead class="thead-dark">
                <tr>
                    <th>Test Name</th>
                    <th>Category</th>
                    <th>Doctor</th>
                    <th>Price</th>
                    <th>Urgent</th>
                    <th>Status</th>
                    <th>Booked At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($lab_tests as $lab): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($lab['test_name']); ?></td>
                        <td><?php echo htmlspecialchars($lab['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($lab['doctor_name'] ?? 'N/A'); ?></td>
                        <td><?php echo number_format($lab['price'],2); ?></td>
                        <td><?php echo $lab['is_urgent'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo ucfirst($lab['report_status']); ?></td>
                        <td><?php echo $lab['booked_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h2 class="section-title mt-5">My Procedure Requests</h2>
    <input type="text" id="procSearch" class="form-control mb-3 w-50" placeholder="Search Procedures...">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="procTable">
            <thead class="thead-dark">
                <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($procedures as $proc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($proc['procedure_type']); ?></td>
                        <td><?php echo htmlspecialchars($proc['procedure_name']); ?></td>
                        <td><?php echo htmlspecialchars($proc['doctor_name'] ?? 'N/A'); ?></td>
                        <td><?php echo $proc['procedure_date']; ?></td>
                        <td><?php echo $proc['procedure_time']; ?></td>
                        <td><?php echo ucfirst($proc['status']); ?></td>
                    </tr>
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
