<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'lab') {
    header("Location: ../login.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $default_price = $_POST['default_price'];

    $stmt = $conn->prepare("INSERT INTO lab_categories (name, description, default_price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $description, $default_price);

    if($stmt->execute()){
        $success = "Lab category added successfully.";
    } else {
        $error = "Error: ".$conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Lab Category</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
    <div class="card mx-auto" style="max-width:600px;">
        <div class="card-body">
            <h3>Add Lab Category</h3>
            <?php if(isset($success)) echo "<p class='text-success'>$success</p>"; ?>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Default Price</label>
                        <input type="number" step="0.01" name="default_price" class="form-control" value="0.00">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Add Category</button>
                <a href="view_lab_categories.php" class="btn btn-dark">View Categories</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
