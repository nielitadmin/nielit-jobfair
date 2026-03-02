<?php
include "../config.php";

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['event'])){
    die("Invalid Event");
}

$event_id = intval($_GET['event']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Live Monitor</title>

<!-- Auto refresh every 5 seconds -->
<meta http-equiv="refresh" content="5">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#111; color:white; }
.table { color:white; }
.badge { font-size:14px; }
</style>

</head>
<body>

<div class="container mt-4">

<h2 class="text-center text-danger">🔴 LIVE INTERVIEW MONITOR</h2>

<table class="table table-bordered table-dark mt-4">
<tr>
<th>Company</th>
<th>Student</th>
<th>Status</th>
<th>Time</th>
</tr>

<?php
$calls = mysqli_query($conn,"
SELECT ic.*, r.company_name, s.name 
FROM interview_calls ic
JOIN recruiters r ON ic.recruiter_id=r.id
JOIN students s ON ic.student_id=s.id
WHERE ic.event_id='$event_id'
ORDER BY ic.created_at DESC
");

while($c=mysqli_fetch_assoc($calls)){
?>

<tr>
<td><?php echo $c['company_name']; ?></td>
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
<td><?php echo $c['created_at']; ?></td>
</tr>

<?php } ?>

</table>

</div>
</body>
</html>