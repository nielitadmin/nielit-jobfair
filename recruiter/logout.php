<?php
include "../config.php";

unset($_SESSION['recruiter_id']);
unset($_SESSION['event_id']);

header("Location: login.php");
exit();
?>