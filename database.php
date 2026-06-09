<?php

$hostname = "localhost";
$dbuser = "root";
$dbPassword = "";
$dbname = "university_portal";
$conn = mysqli_connect($hostname, $dbuser, $dbPassword, $dbname);
if (!$conn) {
    die("something went wrong;");
}
?>