<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'lab') {
    header("Location: ../login.php");
    exit();
}

if(!isset($_GET['id'])){
    echo "Invalid request.";
    exit;
}

$id = intval($_GET['id']);
$q = $conn->prepare("SELECT * FROM lab_categories WHERE id=?");
$q->bind_param("i",$id);
$q->execute();
$res = $q->get_result();
if($res->num_rows !== 1){
    echo "Category not found.";
    exit;
}
$category = $res->fetch_assoc();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $default_price = $_POST['default_price'];

    $u = $conn->prepare("UPDATE lab_categories SET name=?, description=?, default_price=? WHERE id=?");
    $u->bind_param("ssdi", $name, $description, $default_price, $id);

    if($u->execute()){
        echo "<script>alert('Category updated successfully.'); window.location='view_lab_categories.php';</script>";
    } else {
        $error = "Update failed.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Lab Category</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
    <div class="card mx-auto" style="max-width:600px;">
        <div class="card-body">
            <h3>Edit Lab Category</h3>
            <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="<?= $category['name'] ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Default Price</label>
                        <input type="number" step="0.01" name="default_price" class="form-control" value="<?= $category['default_price'] ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= $category['description'] ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="view_lab_categories.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
