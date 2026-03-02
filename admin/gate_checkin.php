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

/* ================= HANDLE CHECK-IN ================= */

if(isset($_GET['checkin_student'])){
    $sid = intval($_GET['checkin_student']);
    mysqli_query($conn,"
        UPDATE students 
        SET checkin_status='present' 
        WHERE id='$sid' AND event_id='$event_id'
    ");
}

if(isset($_GET['checkin_recruiter'])){
    $rid = intval($_GET['checkin_recruiter']);
    mysqli_query($conn,"
        UPDATE recruiters 
        SET checkin_status='present' 
        WHERE id='$rid' AND event_id='$event_id'
    ");
}

/* ================= FETCH DATA ================= */

$students = mysqli_query($conn,"
SELECT * FROM students 
WHERE event_id='$event_id'
");

$recruiters = mysqli_query($conn,"
SELECT * FROM recruiters 
WHERE event_id='$event_id'
");

/* ================= STATS ================= */

$total_students = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM students WHERE event_id='$event_id'"));
$present_students = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM students WHERE event_id='$event_id' AND checkin_status='present'"));

$total_recruiters = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM recruiters WHERE event_id='$event_id'"));
$present_recruiters = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM recruiters WHERE event_id='$event_id' AND checkin_status='present'"));
?>

<!DOCTYPE html>
<html>
<head>
<title>Gate Check-In</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function confirmCheckin(type,id){
    if(confirm("Are you sure you want to mark this person as Present?")){
        window.location = "?event=<?php echo $event_id; ?>&checkin_"+type+"="+id;
    }
}
</script>

</head>
<body>

<div class="container mt-4">

<h3>🛂 Gate Check-In Panel</h3>

<!-- ================= STATS ================= -->

<div class="row mt-3">
<div class="col-md-6">
<div class="card p-3 bg-light">
<h5>Student Stats</h5>
Total: <?php echo $total_students; ?><br>
Present: <?php echo $present_students; ?><br>
Absent: <?php echo $total_students - $present_students; ?>
</div>
</div>

<div class="col-md-6">
<div class="card p-3 bg-light">
<h5>Recruiter Stats</h5>
Total: <?php echo $total_recruiters; ?><br>
Present: <?php echo $present_recruiters; ?><br>
Absent: <?php echo $total_recruiters - $present_recruiters; ?>
</div>
</div>
</div>

<hr>

<!-- ================= TABS ================= -->

<ul class="nav nav-tabs mt-4">
<li class="nav-item">
<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#students">
Students
</button>
</li>
<li class="nav-item">
<button class="nav-link" data-bs-toggle="tab" data-bs-target="#recruiters">
Recruiters
</button>
</li>
</ul>

<div class="tab-content mt-3">

<!-- ================= STUDENTS TAB ================= -->

<div class="tab-pane fade show active" id="students">

<input type="text" id="studentSearch" class="form-control mb-3" placeholder="Search Student..." onkeyup="filterTable('studentTable','studentSearch')">

<table class="table table-bordered" id="studentTable">
<tr>
<th>Name</th>
<th>Unique ID</th>
<th>Email</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($s=mysqli_fetch_assoc($students)){ ?>
<tr>
<td><?php echo $s['name']; ?></td>
<td><?php echo $s['unique_id']; ?></td>
<td><?php echo $s['email']; ?></td>
<td>
<?php
if($s['checkin_status']=='present')
echo "<span class='badge bg-success'>Present</span>";
else
echo "<span class='badge bg-danger'>Absent</span>";
?>
</td>
<td>
<?php if($s['checkin_status']!='present'){ ?>
<button class="btn btn-success btn-sm"
onclick="confirmCheckin('student',<?php echo $s['id']; ?>)">
Check-In
</button>
<?php } ?>
</td>
</tr>
<?php } ?>

</table>
</div>

<!-- ================= RECRUITERS TAB ================= -->

<div class="tab-pane fade" id="recruiters">

<input type="text" id="recruiterSearch" class="form-control mb-3" placeholder="Search Recruiter..." onkeyup="filterTable('recruiterTable','recruiterSearch')">

<table class="table table-bordered" id="recruiterTable">
<tr>
<th>Company</th>
<th>Unique ID</th>
<th>Email</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($r=mysqli_fetch_assoc($recruiters)){ ?>
<tr>
<td><?php echo $r['company_name']; ?></td>
<td><?php echo $r['unique_id']; ?></td>
<td><?php echo $r['email']; ?></td>
<td>
<?php
if($r['checkin_status']=='present')
echo "<span class='badge bg-success'>Present</span>";
else
echo "<span class='badge bg-danger'>Absent</span>";
?>
</td>
<td>
<?php if($r['checkin_status']!='present'){ ?>
<button class="btn btn-warning btn-sm"
onclick="confirmCheckin('recruiter',<?php echo $r['id']; ?>)">
Check-In
</button>
<?php } ?>
</td>
</tr>
<?php } ?>

</table>
</div>

</div>

</div>

<!-- ================= SEARCH SCRIPT ================= -->

<script>
function filterTable(tableId, inputId) {
    var input = document.getElementById(inputId);
    var filter = input.value.toUpperCase();
    var table = document.getElementById(tableId);
    var tr = table.getElementsByTagName("tr");

    for (var i = 1; i < tr.length; i++) {
        var txtValue = tr[i].textContent || tr[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}
</script>

</body>
</html>