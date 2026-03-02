<?php
include "../config.php";

if(isset($_POST['login'])){
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn,"SELECT * FROM superadmins WHERE username='$username'");
    $row = mysqli_fetch_assoc($query);

    if($row && password_verify($password, $row['password'])){
        $_SESSION['superadmin_id'] = $row['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid Credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Superadmin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="col-md-4 offset-md-4">

<div class="card shadow">
<div class="card-body">

<h4 class="text-center">Superadmin Login</h4>

<?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form method="POST">
<input type="text" name="username" class="form-control mb-2" placeholder="Username" required>

<div class="input-group mb-2">
<input type="password" name="password" id="pass" class="form-control" placeholder="Password" required>
<button type="button" class="btn btn-secondary" onclick="toggle()">👁</button>
</div>

<button name="login" class="btn btn-primary w-100">Login</button>
</form>

</div>
</div>

</div>
</div>

<script>
function toggle(){
    var x = document.getElementById("pass");
    x.type = (x.type === "password") ? "text" : "password";
}
</script>

</body>
</html>