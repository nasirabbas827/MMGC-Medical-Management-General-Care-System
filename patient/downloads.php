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
   FETCH LAB REPORTS
===================== */
$lab_reports_res = $conn->prepare("SELECT l.*, lt.test_name, d.full_name AS doctor_name
                                   FROM lab_reports l
                                   LEFT JOIN lab_tests lt ON l.lab_test_id = lt.id
                                   LEFT JOIN doctors d ON l.uploaded_by = d.id
                                   WHERE l.patient_id = ?
                                   ORDER BY l.uploaded_at DESC");
$lab_reports_res->bind_param("i", $patient_id);
$lab_reports_res->execute();
$lab_reports_result = $lab_reports_res->get_result();
$lab_reports = $lab_reports_result->fetch_all(MYSQLI_ASSOC);

/* =====================
   FETCH PROCEDURES
===================== */
$procedures_res = $conn->prepare("SELECT p.*, d.full_name AS doctor_name, n.full_name AS nurse_name
                                  FROM procedures p
                                  LEFT JOIN doctors d ON p.doctor_id = d.id
                                  LEFT JOIN staff n ON p.nurse_id = n.id
                                  WHERE p.patient_id=? ORDER BY p.created_at DESC");
$procedures_res->bind_param("i", $patient_id);
$procedures_res->execute();
$procedures_result = $procedures_res->get_result();
$procedures = $procedures_result->fetch_all(MYSQLI_ASSOC);

/* =====================
   FETCH INVOICES
===================== */
$invoices_res = $conn->prepare("SELECT * FROM invoices WHERE patient_id=? ORDER BY issued_date DESC");
$invoices_res->bind_param("i", $patient_id);
$invoices_res->execute();
$invoices_result = $invoices_res->get_result();
$invoices = $invoices_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Downloads</title>
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

    <!-- LAB REPORTS -->
    <h2 class="section-title">Lab Reports</h2>
    <input type="text" id="labSearch" class="form-control mb-3 w-50" placeholder="Search Lab Reports...">
    <div class="table-responsive table-wrapper">
        <table class="table table-bordered table-striped" id="labTable">
            <thead class="thead-dark">
                <tr>
                    <th>Test Name</th>
                    <th>Doctor</th>
                    <th>Type</th>
                    <th>Uploaded At</th>
                    <th>Doctor Approved</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($lab_reports as $lab): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($lab['test_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($lab['doctor_name'] ?? 'N/A'); ?></td>
                        <td><?php echo strtoupper($lab['file_type']); ?></td>
                        <td><?php echo $lab['uploaded_at']; ?></td>
                        <td><?php echo $lab['doctor_approved'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <?php if(file_exists('../admin/'.$lab['file_path'])): ?>
                                <a href="../admin/<?php echo $lab['file_path']; ?>" class="btn btn-sm btn-success" download>Download</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- PROCEDURE PRESCRIPTIONS -->
    <h2 class="section-title">Procedures & Prescriptions</h2>
    <input type="text" id="procSearch" class="form-control mb-3 w-50" placeholder="Search Procedures...">
    <div class="table-responsive table-wrapper">
        <table class="table table-bordered table-striped" id="procTable">
            <thead class="thead-dark">
                <tr>
                    <th>Procedure Name</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($procedures as $proc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($proc['procedure_name']); ?></td>
                        <td><?php echo htmlspecialchars($proc['procedure_type']); ?></td>
                        <td><?php echo $proc['procedure_date']; ?></td>
                        <td><?php echo ucfirst($proc['status']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" 
                                onclick='showProcedureModal(<?php echo json_encode($proc); ?>)'>
                                View Details
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- INVOICES -->
    <h2 class="section-title">Invoices</h2>
    <input type="text" id="invoiceSearch" class="form-control mb-3 w-50" placeholder="Search Invoices...">
    <div class="table-responsive table-wrapper">
        <table class="table table-bordered table-striped" id="invoiceTable">
            <thead class="thead-dark">
                <tr>
                    <th>Invoice #</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Total</th>
                    <th>Net Amount</th>
                    <th>Payment Status</th>
                    <th>Issued Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($invoices as $inv): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($inv['invoice_number']); ?></td>
                        <td><?php echo htmlspecialchars($inv['description']); ?></td>
                        <td><?php echo ucfirst($inv['type']); ?></td>
                        <td><?php echo number_format($inv['total_amount'],2); ?></td>
                        <td><?php echo number_format($inv['net_amount'],2); ?></td>
                        <td><?php echo ucfirst($inv['payment_status']); ?></td>
                        <td><?php echo $inv['issued_date']; ?></td>
                        <td>
                            <a href="generate_invoice.php?id=<?php echo $inv['id']; ?>" class="btn btn-sm btn-success" target="_blank">Download</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- PROCEDURE DETAILS MODAL -->
<div class="modal fade" id="procedureModal" tabindex="-1" role="dialog" aria-labelledby="procedureModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="procedureModalLabel">Procedure Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
            <tr><th>Procedure Name</th><td id="modalProcName"></td></tr>
            <tr><th>Type</th><td id="modalProcType"></td></tr>
            <tr><th>Doctor</th><td id="modalDoctor"></td></tr>
            <tr><th>Nurse</th><td id="modalNurse"></td></tr>
            <tr><th>Date</th><td id="modalDate"></td></tr>
            <tr><th>Time</th><td id="modalTime"></td></tr>
            <tr><th>Status</th><td id="modalStatus"></td></tr>
            <tr><th>Treatment Notes</th><td id="modalNotes"></td></tr>
            <tr><th>Prescription</th><td id="modalPrescription"></td></tr>
            <tr><th>Outcome</th><td id="modalOutcome"></td></tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <a id="modalPrescriptionDownload" onclick="window.print()" class="btn btn-success" download>Download Prescription</a>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
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
        $("#invoiceSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#invoiceTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    function showProcedureModal(proc) {
        $('#modalProcName').text(proc.procedure_name);
        $('#modalProcType').text(proc.procedure_type);
        $('#modalDoctor').text(proc.doctor_name || 'N/A');
        $('#modalNurse').text(proc.nurse_name || 'N/A');
        $('#modalDate').text(proc.procedure_date);
        $('#modalTime').text(proc.procedure_time || 'N/A');
        $('#modalStatus').text(proc.status);
        $('#modalNotes').text(proc.treatment_notes || 'N/A');
        $('#modalPrescription').text(proc.prescription ? proc.prescription.split('/').pop() : 'N/A');
        $('#modalOutcome').text(proc.outcome || 'N/A');

        if(proc.prescription && proc.prescription.length > 0) {
            $('#modalPrescriptionDownload').show();
        } else {
            $('#modalPrescriptionDownload').hide();
        }

        $('#procedureModal').modal('show');
    }
</script>
</body>
</html>
