<?php
include "../config.php";

if(!isset($_GET['event'])){
    die("Invalid Event");
}

$event_id = intval($_GET['event']);

/* Verify Event Exists */
$event_check = mysqli_query($conn,"SELECT id FROM events WHERE id='$event_id'");
if(mysqli_num_rows($event_check)==0){
    die("Event Not Found");
}

if(isset($_POST['register'])){

    $company  = clean_input($_POST['company_name']);
    $hr       = clean_input($_POST['hr_name']);
    $email    = clean_input($_POST['email']);
    $phone    = clean_input($_POST['phone']);
    $username = clean_input($_POST['username']);
    $password_plain = $_POST['password'];
    $password = password_hash($password_plain, PASSWORD_DEFAULT);
    $cin      = clean_input($_POST['cin']);
    $gst      = clean_input($_POST['gst']);

    /* Check duplicate email */
    $check_email = mysqli_query($conn,"SELECT id FROM recruiters WHERE email='$email'");
    if(mysqli_num_rows($check_email) > 0){
        echo "<script>alert('Email already registered');</script>";
    }

    /* Check duplicate username */
    elseif(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM recruiters WHERE username='$username'")) > 0){
        echo "<script>alert('Username already taken');</script>";
    }

    else{

        /* Generate Unique ID */
        do {
            $unique_id = "NTREC-" . str_pad(rand(0,9999), 4, '0', STR_PAD_LEFT);
            $check = mysqli_query($conn,"SELECT id FROM recruiters WHERE unique_id='$unique_id'");
        } while(mysqli_num_rows($check) > 0);

        /* Insert Data */
        mysqli_query($conn,"INSERT INTO recruiters(
            event_id,
            unique_id,
            company_name,
            hr_name,
            email,
            phone,
            username,
            password,
            plain_password,
            cin,
            gst,
            status
        ) VALUES(
            '$event_id',
            '$unique_id',
            '$company',
            '$hr',
            '$email',
            '$phone',
            '$username',
            '$password',
            '$password_plain',
            '$cin',
            '$gst',
            'pending'
        )");

        echo "<script>
        alert('Registration Submitted Successfully. Await Admin Approval.');
        window.location='../index.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Recruiter Registration</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 col-md-6">
<div class="card shadow">
<div class="card-body">

<h4 class="mb-3">Recruiter Registration</h4>

<form method="POST">

<input name="company_name" class="form-control mb-2" placeholder="Company Name" required>

<input name="hr_name" class="form-control mb-2" placeholder="HR Name" required>

<input type="email" name="email" class="form-control mb-2" placeholder="Email" required>

<input name="phone" class="form-control mb-2" placeholder="Phone" required>

<input name="username" class="form-control mb-2" placeholder="Username" required>

<input type="password" name="password" class="form-control mb-2" placeholder="Password" required>

<input name="cin" class="form-control mb-2" placeholder="CIN Number">

<input name="gst" class="form-control mb-2" placeholder="GST Number">

<button name="register" class="btn btn-warning w-100">
Submit for Approval
</button>

</form>

</div>
</div>
</div>

</body>
</html>