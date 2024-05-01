<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: header.php -->
<!-- Page Description: This is the php wrapper for the header.-->

<?php
$pages = array(); // declaring an array that will be used to determine which pages are shown

// If the user is not logged in, only show the login and register page in the navigation
if (!isset($_SESSION['username'])) {
  $pages['login'] = true;
}
?>

<header>
  <script defer src="scripts/header.js"></script> <!-- The script for header.js -->
  <!--Heading with website name-->
  <h1>BookLeaf <i class="fa-solid fa-leaf"></i></h1> <!--Leaf logo from FontAwesome-->

  <!--Navigation to move through the website -->
  <!--If the user is not logged in, hide certain pages from them-->
  <nav>
    <a href="index.php" <?= isset($pages['login']) ? 'hidden' : ""; ?>>Main Page</a>
    <a href="login.php" <?= !isset($pages['login']) ? 'hidden' : ""; ?>>Login</a>
    <a href="register.php" <?= !isset($pages['login']) ? 'hidden' : ""; ?>>Register</a>
    <a href="search.php" <?= isset($pages['login']) ? 'hidden' : ""; ?>>Search</a>
    <a href="addbook.php" <?= isset($pages['login']) ? 'hidden' : ""; ?>>Add Book</a>
    <a href="editaccount.php" <?= isset($pages['login']) ? 'hidden' : ""; ?>>Edit Account</a>
    <a href="logout.php" <?= isset($pages['login']) ? 'hidden' : ""; ?>>Logout</a>
  </nav>
</header>

<span class = "chevron"><i class="fa-solid fa-chevron-down fa-2x"></i><i class="fa-solid fa-chevron-up fa-2x"></i> <!--chevron icons from FontAwesome-- --></span>