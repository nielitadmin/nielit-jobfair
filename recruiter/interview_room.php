<?php
include "../config.php";

if(!isset($_SESSION['recruiter_id'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['call_id'])){
    die("Invalid Access");
}

$call_id = intval($_GET['call_id']);
$recruiter_id = $_SESSION['recruiter_id'];

$call = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT ic.*, students.name 
FROM interview_calls ic
JOIN students ON ic.student_id = students.id
WHERE ic.id='$call_id' 
AND ic.recruiter_id='$recruiter_id'
"));

if(!$call){
    die("Unauthorized");
}

/* HANDLE STATUS UPDATE */
if(isset($_POST['update_status'])){
    $status = $_POST['update_status'];
    mysqli_query($conn,"
        UPDATE interview_calls 
        SET status='$status'
        WHERE id='$call_id'
    ");
}

/* HANDLE OFFER LETTER UPLOAD */
if(isset($_POST['upload_letter'])){
    $file = $_FILES['offer_letter']['name'];
    $tmp = $_FILES['offer_letter']['tmp_name'];
    $path = "../offer_letters/".$call_id."_".$file;

    move_uploaded_file($tmp,$path);

    mysqli_query($conn,"
        UPDATE interview_calls 
        SET offer_letter='$path'
        WHERE id='$call_id'
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Interview Room</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

<div class="card shadow p-4">

<h3>Interview Room</h3>
<hr>

<p><strong>Student:</strong> <?php echo $call['name']; ?></p>
<p><strong>Status:</strong> <?php echo ucfirst($call['status']); ?></p>

<hr>

<form method="POST">
<button name="update_status" value="selected" class="btn btn-success">Selected</button>
<button name="update_status" value="rejected" class="btn btn-danger">Rejected</button>
<button name="update_status" value="screened" class="btn btn-warning">
More Rounds Pending with Company
</button>
</form>

<hr>

<h5>Offer Letter Upload (Optional)</h5>

<form method="POST" enctype="multipart/form-data">
<input type="file" name="offer_letter" class="form-control mb-2">
<button name="upload_letter" class="btn btn-primary">Upload Letter</button>
</form>

<?php if($call['offer_letter']){ ?>
<p class="mt-3">
<a href="<?php echo $call['offer_letter']; ?>" target="_blank">
View Uploaded Offer Letter
</a>
</p>
<?php } ?>

</div>

</div>

</body>
</html>