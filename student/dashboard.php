<?php
include "../config.php";

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$event_id = $_SESSION['event_id'];

$student = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM students WHERE id='$student_id'"));

/* APPLY TO COMPANY */
if(isset($_GET['apply'])){
    $recruiter_id = $_GET['apply'];

    mysqli_query($conn,"INSERT INTO applications(student_id,recruiter_id)
    VALUES('$student_id','$recruiter_id')");
}

/* Fetch Recruiters */
$recruiters = mysqli_query($conn,"
SELECT * FROM recruiters 
WHERE event_id='$event_id' 
AND status='approved'
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-success">
<div class="container">
<span class="navbar-brand">
Welcome <?php echo $student['name']; ?> (<?php echo $student['unique_id']; ?>)
</span>
<a href="logout.php" class="btn btn-danger">Logout</a>
</div>
</nav>

<div class="container mt-4">

<h4>Available Recruiters</h4>

<table class="table table-bordered">
<tr>
<th>Company</th>
<th>HR</th>
<th>Action</th>
</tr>

<?php while($r=mysqli_fetch_assoc($recruiters)){ ?>
<tr>
<td><?php echo $r['company_name']; ?></td>
<td><?php echo $r['hr_name']; ?></td>
<td>
<a href="?apply=<?php echo $r['id']; ?>" class="btn btn-primary btn-sm">
Apply
</a>
</td>
</tr>
<?php } ?>
</table>

<h4 class="mt-4">My Applications</h4>

<table class="table table-striped">
<tr>
<th>Company</th>
<th>Status</th>
</tr>

<?php
$app = mysqli_query($conn,"
SELECT applications.*, recruiters.company_name 
FROM applications
JOIN recruiters ON applications.recruiter_id = recruiters.id
WHERE applications.student_id='$student_id'
");

while($a=mysqli_fetch_assoc($app)){
?>

<tr>
<td><?php echo $a['company_name']; ?></td>
<td>
<?php
$status = $a['status'];

if($status=='called') echo "<span class='badge bg-warning'>Called</span>";
elseif($status=='interviewing') echo "<span class='badge bg-info'>Interviewing</span>";
elseif($status=='selected') echo "<span class='badge bg-success'>Selected</span>";
elseif($status=='rejected') echo "<span class='badge bg-danger'>Rejected</span>";
else echo "<span class='badge bg-secondary'>Applied</span>";
?>
</td>
</tr>

<?php } ?>
</table>

</div>
</body>
</html>