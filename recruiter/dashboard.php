<?php
include "../config.php";

if(!isset($_SESSION['recruiter_id'])){
    header("Location: login.php");
    exit();
}

$recruiter_id = $_SESSION['recruiter_id'];

/* Get Recruiter Info */
$rec = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM recruiters WHERE id='$recruiter_id'"));
$event_id = $rec['event_id'];

/* ================= CALL STUDENT ================= */
if(isset($_GET['call'])){
    $student_id = intval($_GET['call']);

    mysqli_query($conn,"
        UPDATE applications 
        SET status='called' 
        WHERE student_id='$student_id' 
        AND recruiter_id='$recruiter_id'
    ");

    mysqli_query($conn,"
        INSERT INTO interview_calls(event_id,recruiter_id,student_id,status)
        VALUES('$event_id','$recruiter_id','$student_id','called')
    ");
}

/* ================= UPDATE INTERVIEW STATUS ================= */
if(isset($_GET['update_status'])){
    $call_id = intval($_GET['call_id']);
    $status = $_GET['update_status'];

    mysqli_query($conn,"
        UPDATE interview_calls 
        SET status='$status' 
        WHERE id='$call_id' 
        AND recruiter_id='$recruiter_id'
    ");
}

/* ================= FETCH PRESENT STUDENTS ================= */
$present_students = mysqli_query($conn,"
SELECT * FROM students 
WHERE event_id='$event_id' 
AND checkin_status='present'
");

$total_present = mysqli_num_rows($present_students);

/* ================= FETCH APPLICATIONS ================= */
$applications = mysqli_query($conn,"
SELECT applications.*, students.name, students.unique_id
FROM applications
JOIN students ON applications.student_id = students.id
WHERE applications.recruiter_id='$recruiter_id'
");

/* ================= FETCH INTERVIEW CALLS ================= */
$calls = mysqli_query($conn,"
SELECT ic.*, students.name 
FROM interview_calls ic
JOIN students ON ic.student_id = students.id
WHERE ic.recruiter_id='$recruiter_id'
ORDER BY ic.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Recruiter Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-warning">
<div class="container">
<span class="navbar-brand">Recruiter Panel</span>
<a href="logout.php" class="btn btn-danger">Logout</a>
</div>
</nav>

<div class="container mt-4">

<!-- ================= PRESENT STUDENTS ================= -->

<h4>Present Students (<?php echo $total_present; ?>)</h4>

<table class="table table-bordered">
<tr>
<th>Name</th>
<th>Unique ID</th>
<th>Email</th>
<th>Resume</th>
<th>Action</th>
</tr>

<?php 
mysqli_data_seek($present_students, 0);
while($s=mysqli_fetch_assoc($present_students)){ 
?>
<tr>
<td><?php echo $s['name']; ?></td>
<td><?php echo $s['unique_id']; ?></td>
<td><?php echo $s['email']; ?></td>
<td>
<a href="<?php echo $s['resume']; ?>" target="_blank" 
   class="btn btn-info btn-sm">View Resume</a>
</td>
<td>
<a href="?call=<?php echo $s['id']; ?>" 
   class="btn btn-primary btn-sm">
   Call
</a>
</td>
</tr>
<?php } ?>
</table>

<hr>

<!-- ================= APPLIED STUDENTS ================= -->

<h4>Applied Students</h4>

<table class="table table-bordered">
<tr>
<th>Name</th>
<th>Unique ID</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($a=mysqli_fetch_assoc($applications)){ ?>
<tr>
<td><?php echo $a['name']; ?></td>
<td><?php echo $a['unique_id']; ?></td>
<td><?php echo ucfirst($a['status']); ?></td>
<td>
<?php if($a['status']!='called'){ ?>
<a href="?call=<?php echo $a['student_id']; ?>" 
   class="btn btn-primary btn-sm">
   Call
</a>
<?php } else { ?>
<span class="badge bg-success">Called</span>
<?php } ?>
</td>
</tr>
<?php } ?>
</table>

<hr>

<!-- ================= INTERVIEW MANAGEMENT ================= -->

<h4>Interview Management</h4>

<table class="table table-bordered">
<tr>
<th>Student</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($c=mysqli_fetch_assoc($calls)){ ?>
<tr>
<td><?php echo $c['name']; ?></td>
<td>

<?php
if($c['status']=='called')
echo "<span class='badge bg-primary'>Called</span>";
elseif($c['status']=='interviewing')
echo "<span class='badge bg-warning'>Interviewing</span>";
elseif($c['status']=='selected')
echo "<span class='badge bg-success'>Selected</span>";
elseif($c['status']=='rejected')
echo "<span class='badge bg-danger'>Rejected</span>";
elseif($c['status']=='screened')
echo "<span class='badge bg-info'>Screened</span>";
?>

</td>

<td>
<a href="?update_status=interviewing&call_id=<?php echo $c['id']; ?>" 
   class="btn btn-warning btn-sm">Start</a>

<a href="?update_status=selected&call_id=<?php echo $c['id']; ?>" 
   class="btn btn-success btn-sm">Selected</a>

<a href="?update_status=rejected&call_id=<?php echo $c['id']; ?>" 
   class="btn btn-danger btn-sm">Rejected</a>

<a href="?update_status=screened&call_id=<?php echo $c['id']; ?>" 
   class="btn btn-info btn-sm">Screened</a>
</td>
</tr>
<?php } ?>
</table>

</div>
</body>
</html>