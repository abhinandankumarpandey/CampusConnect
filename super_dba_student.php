<?php
session_start();
include 'db.php'; 

$id = $_GET['delete_student'];

$query = "DELETE FROM users WHERE id = '$id'";

$result = mysqli_query($conn, $query);

header("Location: super_dba_student_control.php");

?>