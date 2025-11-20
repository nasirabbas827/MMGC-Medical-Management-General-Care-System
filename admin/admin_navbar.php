<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
  <a class="navbar-brand" href="admin_home.php">Admin Dashboard</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">

      <li class="nav-item">
        <a class="nav-link" href="admin_home.php">Home</a>
      </li>

      <!-- Doctors & Staff Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="doctorsStaffDropdown" role="button" 
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Doctors & Staff
        </a>
        <div class="dropdown-menu" aria-labelledby="doctorsStaffDropdown">
          <a class="dropdown-item" href="view_doctors.php">Doctors</a>
          <a class="dropdown-item" href="view_staff.php">Staff</a>
        </div>
      </li>

      <!-- Patients & Appointments Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="patientsDropdown" role="button" 
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Patients & Appointments
        </a>
        <div class="dropdown-menu" aria-labelledby="patientsDropdown">
          <a class="dropdown-item" href="view_patients.php">Patients</a>
          <a class="dropdown-item" href="view_appointments.php">Appointments</a>
        </div>
      </li>

      <!-- Procedures & Lab Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="proceduresLabDropdown" role="button" 
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Procedures & Lab
        </a>
        <div class="dropdown-menu" aria-labelledby="proceduresLabDropdown">
          <a class="dropdown-item" href="view_procedures.php">Procedures</a>
          <a class="dropdown-item" href="view_lab_categories.php">Lab Test Categories</a>
          <a class="dropdown-item" href="view_lab_tests.php">Lab Tests</a>
          <a class="dropdown-item" href="view_lab_reports.php">Lab Reports</a>
        </div>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="view_invoices.php">Invoices</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="view_payments.php">Payments</a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="logout.php">Logout</a>
      </li>

    </ul>
  </div>
</nav>
<!-- jQuery (required for Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

<!-- Popper.js (required for dropdowns and tooltips) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
