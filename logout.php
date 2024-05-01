<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: logout.php -->
<!-- Page Description: This page logs the user out of their session. -->

<?php
// Starting and destroying the session
session_start(); 
session_destroy();

// Redirecting the user to the login page
header("Location: login.php");
exit();
?>
