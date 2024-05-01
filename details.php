<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: details.php -->
<!-- Page Description: This page dynamically displays the details of a book in a user's library.-->

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

include 'includes/library.php'; //including library
$pdo = connectDB(); //connecting to the database

$userid = $_SESSION['userid']; // Getting the user's id
$bookid = $_GET['id']; // Getting the id of the book

// Making sure the user actually owns the book for security reasons
$query = "SELECT * FROM `cois3420_assgn_books` WHERE id = ? AND userid = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$bookid, $userid]);
$row = $stmt->fetch();

// If the user doesn't own the book, redirect to the main page
if (!$stmt) {
  header("Location: index.php");
  exit();
}

// Getting the results of the query to display the information of the book
$title = $row['title'] ?? null;
$author = $row['author'] ?? null;
$date = $row['pubdate'] ?? null;
$ISBN = $row['ISBN'] ?? null;
$genre = $row['genre'] ?? "N/A";
$rating = $row['rating'] ?? 0;
$genre = $row['genre'] ?? "N/A";
$read = $row['readbook'] ?? "N/A";
$description = $row['description'] ?? "N/A";
$formats = $row['formats'] ?? "None";
$formats = explode(", ", $formats); // Turning the string into an array so that it can be formatted
$covimgurl = $row['covimgurl'] ?? "N/A";
$covimgext = $row['covimgextension'] ?? "N/A";
$ebookext = $row['ebookextension'] ?? "";


?>

<!--Title of book so the user knows what book they are looking at the details for.-->
<h2>
  <?= $title ?>
</h2>
<div id="bookdetails">

  <!--Image of the book cover.-->
  <img src=<?php echo "/~camrynmoerchen/www_data/cover" . $bookid . "." . $covimgext ?> alt="Book cover for <?= $title ?>"
    width=300 height=449>

  <!--List of all the book details.-->
  <ul>
    <li><span class="item">Author:</span>
      <?= $author ?>
    </li>
    <li><span class="item">Publication Date:</span>
      <?= $date ?>
    </li>
    <li><span class="item">ISBN:</span>
      <?= $ISBN ?>
    </li>
    <li><span class="item">Genre:</span>
      <?= $genre ?>
    </li>
    <li><span class="item">Rating:</span>

      <?php for ($i = 0; $i < $rating; $i++): //Loop to show the stars for the rating?>

        <i class="fa-solid fa-star"></i> <!-- Stars from FontAwesome -->

      <?php endfor; ?>
    </li>

    <li><span class="item">Have you finished reading this book?:</span>
      <?= $read ?>
    </li>
    <li><span class="item">Description:</span>
      <?= $description ?>
    </li>
    <li><!--Unordered list for listing all of the formats of the book.-->
      <span class="item">Formats of this book that you have:</span>

      <ul>
        <?php if (count($formats)): // only display if search done?>
          <?php foreach ($formats as $row): ?>

            <li>
              <?= $row ?>
            </li>

          <?php endforeach; ?>
        <?php endif // no results ?>
      </ul>

    </li>
    <li>
      <!--If the user has an ebook, link it here. If not, state that the user does not own this ebook.-->
      <?php if ($ebookext != ""): ?>
        <a href=<?php echo "/~camrynmoerchen/www_data/ebook" . $bookid . "." . $ebookext ?>>Link to ebook</a>
      <?php else: ?>
        <span>You do not own this ebook.</span>
      <?php endif ?>
    </li>

  </ul>
</div>