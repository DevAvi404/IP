<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$al = mysqli_connect("localhost", "root", "", "feedback");
if (!$al) {
    die("Connection failed: " . mysqli_connect_error());
}
?>