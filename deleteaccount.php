<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: deleteaccount.php -->
<!-- Page Description: On this page, the user can confirm that they want to delete their account. Once all of the books associated
with their account are deleted, the user's account itself is deleted. -->

<?php
/****************************************
// ENSURES THE USER HAS ACTUALLY LOGGED IN
// IF NOT REDIRECT TO THE LOGIN PAGE HERE
******************************************/
session_start(); //start session

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
  //no user info, redirect
  header("Location: login.php");
  exit();
}

include 'includes/library.php'; // Including library
$pdo = connectDB(); // Making a connection to the database
$username = $_SESSION['username']; //Get the user's username to add the book to their library
$userid = $_SESSION['userid']; //Get the user's id to keep track of which books they own



// Deleting all the books that belong to the user
$query = "DELETE FROM `cois3420_assgn_books` where userid = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userid]);

// Deleting the user from the database
$query = "DELETE FROM `cois3420_assgn_users` where id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userid]);

setcookie("mysitecookie", $_SESSION['username'], 1); //deleting the user's remember me cookie if it exists

//Destroying the session and redirecting the user to the login page
session_destroy();
header("Location: login.php");
exit();


?>