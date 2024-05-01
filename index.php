<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: index.php -->
<!-- Page Description: This page is the main page of the website. It displays a grid of books in the user's library.-->

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

$username = $_SESSION['username']; //Get the user's username to add the book to their library
$userid = $_SESSION['userid']; //Get the user's id to keep track of which books they own

// Getting the user's first name to display
$query = "select firstname from `cois3420_assgn_users` where username=?";
$stmt = $pdo->prepare($query);
$stmt->execute([$username]);
$row = $stmt->fetch();
$name = $row['firstname'];

// Getting the books from the user's library sorted by most recently added
$query = "SELECT id, title, author, covimgextension  FROM `cois3420_assgn_books` where userid = ? ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$userid]);

// Getting the book information as an array
$lists = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Main Page</title>
  <?php include "./includes/metadata.php" ?> <!-- Including the metadata wrapper -->
</head>

<body>
  <script defer src="scripts/deletebook.js"></script> <!-- The script for deletebook.js -->
  <script defer src="scripts/details.js"></script> <!-- The script for details.js -->
  <?php include "./includes/header.php" ?> <!-- Including the header wrapper -->

  <main>

    <!--Section where a page of the user's books are displayed.-->
    <section>
      <h2><?=$name?>'s Library <i class="fa-solid fa-house-user"></i></h2> <!--House logo from FontAwesome-->
      
      <!--Links for previous and next pages. These pages do not exist yet so they do not work.-->
      <div class="displaynav">
        <a href="previous.php">Previous</a>
        <a href="next.php">Next</a>
      </div>

      <div class="displaybooks">
        <!--Each <div> is the shown information for each book.-->
        
        <!-- Looping through the books the user owns. -->
            <?php for ($i = 0; $i < count($lists); $i++): ?>
              
              <div>
                  <!--Displaying the information for each book depending on the book's index in the list array -->
                  <img src=<?php echo "/~camrynmoerchen/www_data/cover".$lists[$i]['id'].".".$lists[$i]['covimgextension'] ?> alt="Book cover for <?=$lists[$i]['title']?>">
                  <span class="btitle"><?=$lists[$i]['title']?></span>
                  <span><?=$lists[$i]['author']?></span>

                    <!--Links for delete, edit, and details pages. The delete and edit pages do not exist yet so they do not work.-->
                  <div class="booklinks">
                    <a href= <?php echo "editbook.php?id=".$lists[$i]['id']?> class="button">Edit</a>
                    <a href= <?php echo "deletebook.php?id=".$lists[$i]['id']?> class="deletebook">Delete</a>
                    <a id = <?php echo $lists[$i]['id']  ?> class ="details">Details</a>

                  </div>
                </div>
              
            <?php endfor;?>

      </div>

      <div id="modalWindow" class="modal">
      
        <!-- Modal content -->
        <div class="modalContent">
          <span id="close">&times;</span>  <!-- X so that the user can close the window -->
            <div href="/"></a>
        </div>

      </div>

    </section>

  </main>

  <?php include "./includes/footer.php" ?> <!-- Including the footer wrapper -->
</body>

</html>