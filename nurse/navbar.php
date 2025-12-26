<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="nurse_dashboard.php">
        MMGC – Nurse Panel
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarNav" aria-controls="navbarNav"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">

            <?php if (isset($_SESSION['staff_id']) && $_SESSION['staff_role'] === 'nurse'): ?>

                <li class="nav-item">
                    <a class="nav-link" href="nurse_dashboard.php">Dashboard</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="view_procedures.php">Manage Procedures</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="patients.php">Patient Progress</a>
                </li>

                

                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>

            <?php else: ?>

                <li class="nav-item">
                    <a class="nav-link" href="../login.php">Login</a>
                </li>

            <?php endif; ?>

        </ul>
    </div>
</nav>
