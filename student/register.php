<?php
include "../config.php";
include "../includes/mailer.php";

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

    $name     = clean_input($_POST['name']);
    $email    = clean_input($_POST['email']);
    $phone    = clean_input($_POST['phone']);
    $username = clean_input($_POST['username']);
    $password_plain = $_POST['password'];
    $password = password_hash($password_plain, PASSWORD_DEFAULT);

    /* Check duplicate email */
    if(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM students WHERE email='$email'")) > 0){
        echo "<script>alert('Email already registered');</script>";
    }

    /* Check duplicate username */
    elseif(mysqli_num_rows(mysqli_query($conn,"SELECT id FROM students WHERE username='$username'")) > 0){
        echo "<script>alert('Username already taken');</script>";
    }

    else{

        /* Generate Unique ID */
        do {
            $unique_id = "NTST-" . str_pad(rand(0,99999), 5, '0', STR_PAD_LEFT);
            $check = mysqli_query($conn,"SELECT id FROM students WHERE unique_id='$unique_id'");
        } while(mysqli_num_rows($check) > 0);

        /* Resume Upload */
        $resume = $_FILES['resume'];
        $allowed = ['pdf','doc','docx'];
        $ext = strtolower(pathinfo($resume['name'], PATHINFO_EXTENSION));

        if(!in_array($ext,$allowed)){
            echo "<script>alert('Only PDF, DOC, DOCX allowed');</script>";
        }
        else{

            $resume_name = $unique_id . "_" . time() . "." . $ext;
            $resume_path = "../uploads/" . $resume_name;

            move_uploaded_file($resume['tmp_name'], $resume_path);

            /* Insert Student */
            mysqli_query($conn,"INSERT INTO students(
                event_id,
                unique_id,
                name,
                email,
                phone,
                username,
                password,
                plain_password,
                resume,
                status
            ) VALUES(
                '$event_id',
                '$unique_id',
                '$name',
                '$email',
                '$phone',
                '$username',
                '$password',
                '$password_plain',
                '$resume_path',
                'registered'
            )");

            /* Send Email */
            $subject = "NIELIT Job Fair Registration Successful";
            $body = "
            <h3>Registration Successful</h3>
            <p><strong>Event:</strong> $event_id</p>
            <hr>
            <p><strong>Unique ID:</strong> $unique_id</p>
            <p><strong>Username:</strong> $username</p>
            <p><strong>Password:</strong> $password_plain</p>
            <br>
            <p>Please keep this information safe.</p>
            ";

            sendMail($email,$subject,$body);

            echo "<script>
            alert('Registration Successful! Check Email.');
            window.location='../index.php';
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Registration</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 col-md-6">
<div class="card shadow">
<div class="card-body">

<h4 class="mb-3">Student Registration</h4>

<form method="POST" enctype="multipart/form-data">

<input name="name" class="form-control mb-2" placeholder="Full Name" required>

<input type="email" name="email" class="form-control mb-2" placeholder="Email" required>

<input name="phone" class="form-control mb-2" placeholder="Phone" required>

<input name="username" class="form-control mb-2" placeholder="Username" required>

<input type="password" name="password" class="form-control mb-2" placeholder="Password" required>

<input type="file" name="resume" class="form-control mb-2" required>

<button name="register" class="btn btn-success w-100">
Register
</button>

</form>

</div>
</div>
</div>

</body>
</html>