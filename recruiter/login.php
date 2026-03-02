<?php
include "../config.php";

if(isset($_POST['login'])){
$username = clean_input($_POST['username']);
$password = $_POST['password'];

$query = mysqli_query($conn,"
SELECT * FROM recruiters 
WHERE username='$username' 
AND status='approved'
");

$row = mysqli_fetch_assoc($query);

if($row && password_verify($password,$row['password'])){
$_SESSION['recruiter_id'] = $row['id'];
$_SESSION['event_id'] = $row['event_id'];
header("Location: dashboard.php");
exit();
} else {
$error="Invalid Credentials or Not Approved";
}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Recruiter Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 col-md-4">
<div class="card shadow">
<div class="card-body">

<h4>Recruiter Login</h4>

<?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form method="POST">
<input name="username" class="form-control mb-2" placeholder="Username" required>
<input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
<button name="login" class="btn btn-warning w-100">Login</button>
</form>

</div>
</div>
</div>
</body>
</html>