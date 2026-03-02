<?php
include "../config.php";

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$center_id = $_SESSION['center_id'];

/* ================= CREATE EVENT ================= */

if(isset($_POST['create_event'])){
    $title = clean_input($_POST['title']);
    $desc  = clean_input($_POST['description']);
    $venue = clean_input($_POST['venue']);
    $date  = $_POST['event_date'];
    $time  = clean_input($_POST['event_time']);
    $map   = clean_input($_POST['map_link']);

    mysqli_query($conn,"INSERT INTO events(center_id,title,description,venue,event_date,event_time,map_link)
    VALUES('$center_id','$title','$desc','$venue','$date','$time','$map')");
}

/* ================= DELETE EVENT ================= */

if(isset($_GET['delete_event'])){
    $id = $_GET['delete_event'];
    mysqli_query($conn,"DELETE FROM events WHERE id='$id' AND center_id='$center_id'");
    header("Location: dashboard.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-primary">
<div class="container">
<span class="navbar-brand">Center Admin Panel</span>
<a href="logout.php" class="btn btn-danger">Logout</a>
</div>
</nav>

<div class="container mt-4">

<!-- CREATE EVENT -->

<div class="card mb-4">
<div class="card-header bg-success text-white">Create Job Fair Event</div>
<div class="card-body">

<form method="POST">
<div class="row">
<div class="col-md-3"><input name="title" class="form-control" placeholder="Event Title" required></div>
<div class="col-md-3"><input name="venue" class="form-control" placeholder="Venue" required></div>
<div class="col-md-2"><input type="date" name="event_date" class="form-control" required></div>
<div class="col-md-2"><input name="event_time" class="form-control" placeholder="Time" required></div>
<div class="col-md-2"><button name="create_event" class="btn btn-success">Create</button></div>
</div>

<textarea name="description" class="form-control mt-2" placeholder="Event Description"></textarea>
<input name="map_link" class="form-control mt-2" placeholder="Google Map Link">

</form>

</div>
</div>

<!-- LIST EVENTS -->

<h4>Your Created Events</h4>

<table class="table table-bordered">
<tr>
<th>ID</th>
<th>Title</th>
<th>Date</th>
<th>Venue</th>
<th>Students</th>
<th>Recruiters</th>
<th>Action</th>
</tr>

<?php
$events = mysqli_query($conn,"SELECT * FROM events WHERE center_id='$center_id'");
while($e=mysqli_fetch_assoc($events)){

$eid = $e['id'];

$students = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM students WHERE event_id='$eid'"));
$recruiters = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM recruiters WHERE event_id='$eid'"));

?>

<tr>
<td><?php echo $eid; ?></td>
<td><?php echo $e['title']; ?></td>
<td><?php echo $e['event_date']; ?></td>
<td><?php echo $e['venue']; ?></td>
<td><?php echo $students; ?></td>
<td><?php echo $recruiters; ?></td>
<td>
<a href="?delete_event=<?php echo $eid; ?>" class="btn btn-danger btn-sm">Delete</a>
<a href="manage_event.php?id=<?php echo $eid; ?>" class="btn btn-info btn-sm">Manage</a>
</td>
</tr>

<?php } ?>
</table>

</div>
</body>
</html>