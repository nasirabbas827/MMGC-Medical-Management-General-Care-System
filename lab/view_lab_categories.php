<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['staff_id']) || $_SESSION['staff_role'] !== 'lab') {
    header("Location: ../login.php");
    exit();
}

if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);
    $del = $conn->prepare("DELETE FROM lab_categories WHERE id=?");
    $del->bind_param("i",$id);
    if($del->execute()){
        echo "<script>alert('Category deleted.'); window.location='view_lab_categories.php';</script>";
    } else {
        echo "<script>alert('Error deleting category.');</script>";
    }
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT * FROM lab_categories";
if(!empty($search)){
    $query .= " WHERE name LIKE '%$search%'";
}
$query .= " ORDER BY id DESC";
$res = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Lab Categories</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
    <a href="add_lab_category.php" class="btn btn-dark float-right">Add Category</a>
    <h3>Lab Categories</h3>

    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" placeholder="Search" class="form-control mr-2">
        <button class="btn btn-primary">Search</button>
        <a href="view_lab_categories.php" class="btn btn-secondary ml-2">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Default Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($res->num_rows>0){
                    while($row=$res->fetch_assoc()){
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['description']}</td>
                            <td>{$row['default_price']}</td>
                            <td>
                                <a href='edit_lab_category.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='view_lab_categories.php?delete_id={$row['id']}' onclick=\"return confirm('Delete this category?');\" class='btn btn-danger btn-sm'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No categories found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
