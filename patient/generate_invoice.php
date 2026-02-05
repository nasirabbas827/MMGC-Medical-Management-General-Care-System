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
   FETCH ALL PATIENT INVOICES
===================== */
$invoice_res = $conn->prepare("SELECT i.*, p.name AS patient_name, p.mr_number
                               FROM invoices i
                               LEFT JOIN patients p ON i.patient_id = p.id
                               WHERE i.patient_id = ?
                               ORDER BY i.issued_date DESC");
$invoice_res->bind_param("i", $patient_id);
$invoice_res->execute();
$invoices = $invoice_res->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Invoices & Downloads</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h3>My Invoices</h3>
    <table class="table table-bordered mt-3">
        <thead class="thead-light">
            <tr>
                <th>Invoice #</th>
                <th>Type</th>
                <th>Description</th>
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
                <td><?php echo htmlspecialchars($inv['type']); ?></td>
                <td><?php echo htmlspecialchars($inv['description']); ?></td>
                <td><?php echo number_format($inv['net_amount'],2); ?></td>
                <td><?php echo ucfirst($inv['payment_status']); ?></td>
                <td><?php echo $inv['issued_date']; ?></td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick='downloadInvoice(<?php echo json_encode($inv); ?>)'>Download</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
async function downloadInvoice(invoice) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.setFontSize(16);
    doc.text("Hospital Invoice", 105, 20, null, null, "center");

    doc.setFontSize(12);
    doc.text(`Patient Name: ${invoice.patient_name}`, 20, 40);
    doc.text(`MR Number: ${invoice.mr_number}`, 20, 50);
    doc.text(`Invoice Number: ${invoice.invoice_number}`, 20, 60);
    doc.text(`Issued Date: ${invoice.issued_date}`, 20, 70);

    doc.text("--------------------------------------------------", 20, 80);

    doc.text(`Description: ${invoice.description}`, 20, 90);
    doc.text(`Type: ${invoice.type}`, 20, 100);
    doc.text(`Total Amount: ${parseFloat(invoice.total_amount).toFixed(2)}`, 20, 110);
    doc.text(`Discount: ${parseFloat(invoice.discount).toFixed(2)}`, 20, 120);
    doc.text(`Net Amount: ${parseFloat(invoice.net_amount).toFixed(2)}`, 20, 130);

    doc.text("--------------------------------------------------", 20, 140);
    doc.text(`Payment Status: ${invoice.payment_status}`, 20, 150);

    doc.text("Thank you for choosing our hospital!", 105, 170, null, null, "center");

    // Save the PDF
    doc.save(`Invoice_${invoice.invoice_number}.pdf`);
}
</script>
</body>
</html>
