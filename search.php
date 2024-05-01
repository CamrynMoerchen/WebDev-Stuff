<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: search.php -->
<!-- Page Description: This page is where the user can search for books within their library. -->

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

include 'includes/library.php';
$pdo = connectDB(); // Making a connection to the database
$username = $_SESSION['username']; //Get the user's username to add the book to their library
$userid = $_SESSION['userid']; //Get the user's id to keep track of which books they own
$search = $_POST['search'] ?? "";
$lists = [];

//if submit is in the post array / the user attempts to search something
if(isset($_POST['submit'])){

  $errors = array(); // declare an empty array to store errors

  //get and sanitize search term
  $search = filter_var($search, FILTER_SANITIZE_STRING);
  if ($search==""){ //if there isn't anything, display an error
    $errors['search'] = true;
  }

  // If there were no errors
  if((count($errors) === 0)){
    
    $title = $search . "%"; //Attaching the wildcard to the title/search variable
    // I saved it in a new variable so that I could still use the search variable to make the form sticky
    
    // Searching the database for matching books
    $query = "SELECT id, title, author, covimgextension  FROM `cois3420_assgn_books` where userid = ? AND title like ? ORDER BY id DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userid, $title]);

    // Getting a list of all of the books
    $lists = $stmt->fetchAll();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Search</title>
  <?php include "./includes/metadata.php" ?> <!-- Including the metadata wrapper -->
</head>

<body>
  <?php include "./includes/header.php" ?> <!-- Including the header wrapper -->


  <main>

    <!--Section that contains the search form.-->
    <section>
      <h2>Search <i class="fa-solid fa-magnifying-glass"></i></h2> <!--Magnifying glass logo from FontAwesome-->

      <!--A form used to collect what the user would like to search.-->
      <form id="searchform" name="searchform" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="post">
        <!--Getting what the user would like to search.-->
        <div>
          <label for="search">Search Camryn's Library:</label>
          <input type="text" id="search" name="search" value="<?=$search?>">
        </div>

        <!--Getting what number of books the user would like on a page.-->
        <!-- Unfortunately, I was not able to implement this in time. -->
        <div>
          <label for="perpage">Books per Page:</label>
          <input type="number" id="perpage" name="perpage" min="1" max="12" value="6">
        </div>

        <!--A submit button so that the user can search their library.-->
        <div>
          <button id="submit" name="submit">Search</button>
        </div>
      </form>


      <!--Section that contains the search results.-->
      <?php if(isset($_POST['submit']) && !sizeof($errors) ):   // only display if search done?>
      <h3>Search Results</h3>
      <div class="displaybooks">
        <!--Each <div> is the shown information for each book.-->
          <!-- LLooping through the list to display each book-->
        <?php for ($i = 0; $i < count($lists); $i++): ?>
              
              <div>
                <!-- The book information -->
                  <img src=<?php echo "/~camrynmoerchen/www_data/cover".$lists[$i]['id'].".".$lists[$i]['covimgextension'] ?> alt="Book cover for <?=$lists[$i]['title']?>">
                  <span class="btitle"><?=$lists[$i]['title']?></span>
                  <span><?=$lists[$i]['author']?></span>
                    <!--Links for delete, edit, and details pages. The delete and edit pages do not exist yet so they do not work.-->
                  <div class="booklinks">
                    <a href= <?php echo "editbook.php?id=".$lists[$i]['id']?> class="button">Edit</a>
                    <a href= <?php echo "deletebook.php?id=".$lists[$i]['id']?> class="button">Delete</a>
                    <a href= <?php echo "details.php?id=".$lists[$i]['id']?> class="button">Details</a>
                  </div>
                </div>
              
            <?php endfor;?>
      </div>
    <?php endif ?>
    </section>
  </main>

  <?php include "./includes/footer.php" ?> <!-- Including the footer wrapper -->
</body>

</html>