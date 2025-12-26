<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="lab_dashboard.php">
        MMGC – Lab Panel
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarNav" aria-controls="navbarNav"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">

            <?php if (isset($_SESSION['staff_id']) && $_SESSION['staff_role'] === 'lab'): ?>

                <li class="nav-item">
                    <a class="nav-link" href="lab_dashboard.php">Dashboard</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="view_lab_categories.php">Lab Categories</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="view_lab_tests.php">Lab Tests</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="view_lab_reports.php">Lab Reports</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>

            <?php else: ?>

                <li class="nav-item">
                    <a class="nav-link" href="../admin/admin_login.php">Admin Login</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="../login.php">Staff Login</a>
                </li>

            <?php endif; ?>

        </ul>
    </div>
</nav>
