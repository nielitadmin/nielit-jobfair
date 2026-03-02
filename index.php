<?php 
include "config.php"; 

$events = mysqli_query($conn,"
SELECT events.*, centers.center_name 
FROM events 
JOIN centers ON events.center_id = centers.id
WHERE events.status='active'
ORDER BY event_date ASC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>NIELIT National Job Fair Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
<div class="container">
<h4 class="text-white">NIELIT National Virtual Job Fair Portal</h4>
<a href="login.php" class="btn btn-light">Login</a>
</div>
</nav>

<div class="container mt-5">

<div class="card shadow mb-4">
<div class="card-body text-center">
<h3>Welcome to NIELIT Virtual Job Fair System</h3>
<p>This system is designed for conducting National Level Virtual & Physical Job Fairs.</p>
</div>
</div>

<h4 class="mb-3">Upcoming Job Fairs</h4>

<?php 
if(mysqli_num_rows($events) > 0){
while($e=mysqli_fetch_assoc($events)){ 
?>

<div class="card mb-3 shadow">
<div class="card-body">
<h5>
<?php echo $e['title']; ?> 
<small class="text-muted">(<?php echo $e['center_name']; ?>)</small>
</h5>

<p><?php echo $e['description']; ?></p>

<p>
<strong>Date:</strong> <?php echo $e['event_date']; ?> |
<strong>Time:</strong> <?php echo $e['event_time']; ?> |
<strong>Venue:</strong> <?php echo $e['venue']; ?>
</p>

<a href="student/register.php?event=<?php echo $e['id']; ?>" 
class="btn btn-success">Register as Student</a>

<a href="recruiter/register.php?event=<?php echo $e['id']; ?>" 
class="btn btn-warning">Register as Recruiter</a>

</div>
</div>

<?php 
}
} else {
echo "<div class='alert alert-info'>No active job fairs available.</div>";
}
?>

</div>

</body>
</html>