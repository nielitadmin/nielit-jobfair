<?php
include "../config.php";
include "../includes/mailer.php";

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])){
    die("Invalid Event");
}

$event_id = intval($_GET['id']);
$center_id = $_SESSION['center_id'];

/* Verify event belongs to admin */
$event = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM events 
WHERE id='$event_id' 
AND center_id='$center_id'
"));

if(!$event){
    die("Unauthorized Access");
}

/* ================= APPROVE RECRUITER ================= */
if(isset($_GET['approve'])){

    $rid = intval($_GET['approve']);

    mysqli_query($conn,"UPDATE recruiters SET status='approved' WHERE id='$rid'");

    $r = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM recruiters WHERE id='$rid'"));

    if($r){

        $to = $r['email'];
        $subject = "NIELIT Job Fair - Recruiter Approval Confirmation";

        $body = "
        <h3>Congratulations! Your Registration is Approved.</h3>
        <p><strong>Event:</strong> {$event['title']}</p>
        <p><strong>Date:</strong> {$event['event_date']}</p>
        <p><strong>Venue:</strong> {$event['venue']}</p>
        <hr>
        <p><strong>Unique ID:</strong> {$r['unique_id']}</p>
        <p><strong>Username:</strong> {$r['username']}</p>
        <p><strong>Password:</strong> {$r['plain_password']}</p>
        <br>
        <p>Please login and participate in the job fair.</p>
        ";

        sendMail($to,$subject,$body);
    }

    header("Location: manage_event.php?id=$event_id");
    exit();
}

/* ================= REJECT RECRUITER ================= */
if(isset($_GET['reject'])){
    $rid = intval($_GET['reject']);
    mysqli_query($conn,"UPDATE recruiters SET status='rejected' WHERE id='$rid'");
    header("Location: manage_event.php?id=$event_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Event</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container mt-4">

<h3>Manage Event: <?php echo $event['title']; ?></h3>

<!-- ================= NEW CONTROL BUTTONS ADDED ================= -->
<div class="mb-4">
<a href="gate_checkin.php?event=<?php echo $event_id; ?>" 
   class="btn btn-success me-2">
   🛂 Gate Check-In
</a>

<a href="live_monitor.php?event=<?php echo $event_id; ?>" 
   class="btn btn-danger">
   🔴 Live Monitor
</a>
</div>
<!-- ============================================================= -->

<ul class="nav nav-tabs mt-4">
  <li class="nav-item">
    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#recruiters">Recruiters</button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#students">Students</button>
  </li>
</ul>

<div class="tab-content mt-3">

<!-- ================= RECRUITERS TAB ================= -->
<div class="tab-pane fade show active" id="recruiters">

<h5>Recruiters</h5>

<table class="table table-bordered">
<tr>
<th>Company</th>
<th>HR</th>
<th>Email</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php
$recs = mysqli_query($conn,"
SELECT * FROM recruiters 
WHERE event_id='$event_id'
");

while($r=mysqli_fetch_assoc($recs)){
?>

<tr>
<td><?php echo $r['company_name']; ?></td>
<td><?php echo $r['hr_name']; ?></td>
<td><?php echo $r['email']; ?></td>
<td>
<?php 
if($r['status']=='pending')
echo "<span class='badge bg-warning'>Pending</span>";
elseif($r['status']=='approved')
echo "<span class='badge bg-success'>Approved</span>";
else
echo "<span class='badge bg-danger'>Rejected</span>";
?>
</td>

<td>
<button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#recModal<?php echo $r['id']; ?>">View</button>

<?php if($r['status']=='pending'){ ?>
<a href="?id=<?php echo $event_id; ?>&approve=<?php echo $r['id']; ?>" class="btn btn-success btn-sm">Approve</a>
<a href="?id=<?php echo $event_id; ?>&reject=<?php echo $r['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
<?php } ?>
</td>
</tr>

<!-- Recruiter Modal -->
<div class="modal fade" id="recModal<?php echo $r['id']; ?>">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Recruiter Details</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<p><strong>Company:</strong> <?php echo $r['company_name']; ?></p>
<p><strong>HR Name:</strong> <?php echo $r['hr_name']; ?></p>
<p><strong>Email:</strong> <?php echo $r['email']; ?></p>
<p><strong>Phone:</strong> <?php echo $r['phone']; ?></p>
<p><strong>CIN:</strong> <?php echo $r['cin']; ?></p>
<p><strong>GST:</strong> <?php echo $r['gst']; ?></p>
<p><strong>Unique ID:</strong> <?php echo $r['unique_id']; ?></p>
<p><strong>Password:</strong> <?php echo $r['plain_password']; ?></p>
</div>
</div>
</div>
</div>

<?php } ?>
</table>

</div>

<!-- ================= STUDENTS TAB ================= -->
<div class="tab-pane fade" id="students">

<h5>Registered Students</h5>

<table class="table table-bordered">
<tr>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Unique ID</th>
<th>Action</th>
</tr>

<?php
$students = mysqli_query($conn,"
SELECT * FROM students 
WHERE event_id='$event_id'
");

while($s=mysqli_fetch_assoc($students)){
?>

<tr>
<td><?php echo $s['name']; ?></td>
<td><?php echo $s['email']; ?></td>
<td><?php echo $s['phone']; ?></td>
<td><?php echo $s['unique_id']; ?></td>
<td>
<button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#stuModal<?php echo $s['id']; ?>">View</button>
</td>
</tr>

<!-- Student Modal -->
<div class="modal fade" id="stuModal<?php echo $s['id']; ?>">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Student Details</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<p><strong>Name:</strong> <?php echo $s['name']; ?></p>
<p><strong>Email:</strong> <?php echo $s['email']; ?></p>
<p><strong>Phone:</strong> <?php echo $s['phone']; ?></p>
<p><strong>Unique ID:</strong> <?php echo $s['unique_id']; ?></p>
<p><strong>Password:</strong> <?php echo $s['plain_password']; ?></p>
<p><strong>Resume:</strong> 
<a href="<?php echo $s['resume']; ?>" target="_blank" class="btn btn-primary btn-sm">View Resume</a>
</p>
</div>
</div>
</div>
</div>

<?php } ?>
</table>

</div>

</div>
</div>

</body>
</html>