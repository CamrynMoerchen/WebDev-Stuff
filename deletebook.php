<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: deletebook.php -->
<!-- Page Description: If the user chooses to be redirected to this page, the book that they clicked on will be deleted.-->

<?php
/****************************************
// ENSURES THE USER HAS ACTUALLY LOGGED IN
// IF NOT REDIRECT TO THE LOGIN PAGE HERE
 ******************************************/
session_start(); //start session

// If the user is not logged in, redirect to login page
if(!isset($_SESSION['username'])){
  //no user info, redirect
  header("Location: login.php");
  exit();
}

include 'includes/library.php'; //including library
$pdo = connectDB(); //connecting to the database

$userid = $_SESSION['userid']; // Getting the user's id
$bookid = $_GET['id']; // Getting the id of the book

// Making sure the user actually owns the book for security reasons
$query = "SELECT title FROM `cois3420_assgn_books` WHERE id = ? AND userid = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$bookid, $userid]); 
$row = $stmt->fetch();

// If the user doesn't own the book, redirect to the main page
if (!$stmt) {
  header("Location: index.php");
  exit();
}

// If the user owns the book, delete it's information from the database
$query = "DELETE FROM `cois3420_assgn_books` WHERE id = ? AND userid = ?";
$stmt = $pdo->prepare($query)->execute([$bookid, $userid]);
      
// redirect to the main page once the book is deleted      
header("Location: index.php");
exit();
  
?>