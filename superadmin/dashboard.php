<?php
include "../config.php";

if(!isset($_SESSION['superadmin_id'])){
    header("Location: login.php");
    exit();
}

/* ===============================
   ADD CENTER
=================================*/
if(isset($_POST['add_center'])){
    $name  = clean_input($_POST['center_name']);
    $code  = clean_input($_POST['center_code']);
    $city  = clean_input($_POST['city']);
    $state = clean_input($_POST['state']);

    mysqli_query($conn,"INSERT INTO centers (center_name,center_code,city,state)
    VALUES('$name','$code','$city','$state')");
}

/* ===============================
   ADD ADMIN UNDER CENTER
=================================*/
if(isset($_POST['add_admin'])){
    $center_id = $_POST['center_id'];
    $name      = clean_input($_POST['admin_name']);
    $email     = clean_input($_POST['admin_email']);
    $username  = clean_input($_POST['admin_username']);
    $password  = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);

    mysqli_query($conn,"INSERT INTO admins(center_id,name,email,username,password)
    VALUES('$center_id','$name','$email','$username','$password')");
}

/* ===============================
   DELETE ADMIN
=================================*/
if(isset($_GET['delete_admin'])){
    $id = $_GET['delete_admin'];
    mysqli_query($conn,"DELETE FROM admins WHERE id='$id'");
    header("Location: dashboard.php");
}

/* ===============================
   DELETE CENTER
=================================*/
if(isset($_GET['delete_center'])){
    $id = $_GET['delete_center'];
    mysqli_query($conn,"DELETE FROM centers WHERE id='$id'");
    header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Superadmin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
<div class="container">
<span class="navbar-brand">🇮🇳 NIELIT National Superadmin Panel</span>
<a href="logout.php" class="btn btn-danger">Logout</a>
</div>
</nav>

<div class="container mt-4">

<!-- ================= CENTER CREATION ================= -->

<div class="card mb-4">
<div class="card-header bg-primary text-white">Create Center</div>
<div class="card-body">

<form method="POST">
<div class="row">
<div class="col-md-3"><input name="center_name" class="form-control" placeholder="Center Name" required></div>
<div class="col-md-2"><input name="center_code" class="form-control" placeholder="Center Code" required></div>
<div class="col-md-2"><input name="city" class="form-control" placeholder="City"></div>
<div class="col-md-2"><input name="state" class="form-control" placeholder="State"></div>
<div class="col-md-2"><button name="add_center" class="btn btn-success">Add</button></div>
</div>
</form>

</div>
</div>

<!-- ================= ADMIN CREATION ================= -->

<div class="card mb-4">
<div class="card-header bg-success text-white">Create Admin Under Center</div>
<div class="card-body">

<form method="POST">
<div class="row">
<div class="col-md-2">
<select name="center_id" class="form-control" required>
<option value="">Select Center</option>
<?php
$centers = mysqli_query($conn,"SELECT * FROM centers");
while($c=mysqli_fetch_assoc($centers)){
echo "<option value='{$c['id']}'>{$c['center_name']}</option>";
}
?>
</select>
</div>

<div class="col-md-2"><input name="admin_name" class="form-control" placeholder="Admin Name" required></div>
<div class="col-md-2"><input name="admin_email" class="form-control" placeholder="Email" required></div>
<div class="col-md-2"><input name="admin_username" class="form-control" placeholder="Username" required></div>
<div class="col-md-2"><input name="admin_password" class="form-control" placeholder="Password" required></div>
<div class="col-md-2"><button name="add_admin" class="btn btn-primary">Create</button></div>
</div>
</form>

</div>
</div>

<!-- ================= CENTER LIST ================= -->

<h4>All Centers</h4>

<table class="table table-bordered">
<tr>
<th>ID</th>
<th>Center</th>
<th>City</th>
<th>State</th>
<th>Admins</th>
<th>Events</th>
<th>Students</th>
<th>Recruiters</th>
<th>Action</th>
</tr>

<?php
$centers = mysqli_query($conn,"SELECT * FROM centers");
while($c=mysqli_fetch_assoc($centers)){

$cid = $c['id'];

$admin_count = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM admins WHERE center_id='$cid'"));
$event_count = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM events WHERE center_id='$cid'"));

$student_count = mysqli_num_rows(mysqli_query($conn,"
SELECT students.id FROM students 
JOIN events ON students.event_id = events.id 
WHERE events.center_id='$cid'"));

$recruiter_count = mysqli_num_rows(mysqli_query($conn,"
SELECT recruiters.id FROM recruiters 
JOIN events ON recruiters.event_id = events.id 
WHERE events.center_id='$cid'"));
?>

<tr>
<td><?php echo $cid; ?></td>
<td><?php echo $c['center_name']; ?></td>
<td><?php echo $c['city']; ?></td>
<td><?php echo $c['state']; ?></td>
<td><?php echo $admin_count; ?></td>
<td><?php echo $event_count; ?></td>
<td><?php echo $student_count; ?></td>
<td><?php echo $recruiter_count; ?></td>
<td>
<a href="?delete_center=<?php echo $cid; ?>" class="btn btn-sm btn-danger">Delete</a>
</td>
</tr>

<?php } ?>
</table>

<!-- ================= ADMIN LIST ================= -->

<h4 class="mt-5">All Admins</h4>

<table class="table table-striped">
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Center</th>
<th>Action</th>
</tr>

<?php
$admins = mysqli_query($conn,"
SELECT admins.*, centers.center_name 
FROM admins 
JOIN centers ON admins.center_id = centers.id");

while($a=mysqli_fetch_assoc($admins)){
?>

<tr>
<td><?php echo $a['id']; ?></td>
<td><?php echo $a['name']; ?></td>
<td><?php echo $a['email']; ?></td>
<td><?php echo $a['center_name']; ?></td>
<td>
<a href="?delete_admin=<?php echo $a['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
</td>
</tr>

<?php } ?>
</table>

</div>
</body>
</html>