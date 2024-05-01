<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: addbook.php -->
<!-- Page Description: This page is where the user can add another book to their library. It contains a form that gets the
information of a book from the user. If the information is valid, the book will be inserted into the database.-->

<?php
/****************************************
// ENSURES THE USER HAS ACTUALLY LOGGED IN
// IF NOT REDIRECT TO THE LOGIN PAGE HERE
******************************************/
session_start(); //start session

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

include 'includes/library.php'; //including library
$pdo = connectDB(); // Making a connection to the database
$errors = array(); //declare empty array to add errors too

$username = $_SESSION['username']; // Get the user's username to add the book to their library
$userid = $_SESSION['userid']; // Get the user's id to keep track of which books they own

//get data from post or set to NULL if doesn't exist
// if formats doesn't exist in post, set to an empty array
$title = $_POST['title'] ?? null;
$author = $_POST['author'] ?? null;
$pubdate = $_POST['pubdate'] ?? null;
$ISBN = $_POST['ISBN'] ?? null;
$genre = $_POST['genre'] ?? null;
$rating = $_POST['rating'] ?? null;
$description = $_POST['description'] ?? null;
$read = $_POST['read'] ?? null;
$formats = $_POST['formats'] ?? [];
$covimgurl = $_POST['covimgurl'] ?? null;


// do this if submit is in the post array/the user attempts to add a book
if (isset($_POST['submit'])) {

  // Checking for errors
  // Validation to see if the user has entered information is not needed because the inputs are either required
  // or not required

  // checking that the user doesn't already have this book in their library
  // assuming that books can be uniquely identified by their ISBN number
  $query = "SELECT ISBN  FROM `cois3420_assgn_books` WHERE userid=? AND ISBN =?  ";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$userid, $ISBN]);

  // If the book is not unique, display an error
  if ($stmt->fetch()) {
    $errors['uniquebook'] = true;
  }


  // Checking the cover image file for errors if it was uploaded
  if (is_uploaded_file($_FILES['covimgupload']['tmp_name'])) {

    $results = checkErrors('covimgupload', 102400); // checking for errors using the function defined in the library

    // If there were errors with uploading the coverimage, display an error
    if (strlen($results) > 0) {
      $errors['covimgupload'] = true;
    }
  }

  // Checking the ebook file for errors if it was uploaded
  if (is_uploaded_file($_FILES['ebookupload']['tmp_name'])) {

    $results = checkErrors('ebookupload', 2000000); // checking for errors using the function defined in the library

    // If there were errors with uploading the ebook, display an error
    if (strlen($results) > 0) {
      $errors['ebookupload'] = true;
    }
  }

  // Only add the book if there were no errors
  if (count($errors) === 0) {
    // Making the array of formats into a string so that it can be uploaded
    if ($formats != null) {
      $formats = implode(", ", $formats);
    }

    // Querying to insert the book information into the database
    $query = "INSERT INTO `cois3420_assgn_books` VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL)";
    $stmt = $pdo->prepare($query)->execute([$userid, $title, $author, $pubdate, $ISBN, $rating, $read, $description, $formats, $genre, $covimgurl]);

    // Moving the cover image file to the correct path and renaming it
    if (is_uploaded_file($_FILES['covimgupload']['tmp_name'])) {

      // Getting the database autonumber for the unique id
      $query = "SELECT id FROM `cois3420_assgn_books` WHERE userid = ? AND title = ?";
      $stmt = $pdo->prepare($query);
      $stmt->execute([$userid, $title]);
      $row = $stmt->fetch();
      $uniqueID = $row['id'];

      $path = WEBROOT . "www_data/"; //location file should go
      $fileroot = "cover"; //base filename, cover because it's a book cover

      // Creating a new name for the file using a function from the library
      $newname = createFilename('covimgupload', $path, $fileroot, $uniqueID);

      // Getting the extension of the image for displaying purposes
      $exts = explode(".", $newname);
      $ext = $exts[count($exts) - 1];

      // Uploading the file extension for displaying purposes
      $query = "UPDATE `cois3420_assgn_books` SET covimgextension = ? WHERE userid = ? AND title = ?";
      $stmt = $pdo->prepare($query);
      $stmt->execute([$ext, $userid, $title]);

      // Moving the uploaded file
      move_uploaded_file($_FILES['covimgupload']['tmp_name'], $newname);
    }

    // Moving the ebook file to the correct path and renaming it
    if (is_uploaded_file($_FILES['ebookupload']['tmp_name'])) {

      // Getting the database autonumber for the unique id
      $query = "SELECT id FROM `cois3420_assgn_books` WHERE userid = ? AND title = ?";
      $stmt = $pdo->prepare($query);
      $stmt->execute([$userid, $title]);
      $row = $stmt->fetch();
      $uniqueID = $row['id'];

      $path = WEBROOT . "www_data/"; //location file should go
      $fileroot = "ebook"; //base filename, ebook because it's an ebook

      // Creating a new name for the file using a function from the library
      $newname = createFilename('ebookupload', $path, $fileroot, $uniqueID);

      // Getting the extension of the ebook for links
      $exts = explode(".", $newname);
      $ext = $exts[count($exts) - 1];

      // Uploading the file extension
      $query = "UPDATE `cois3420_assgn_books` SET ebookextension = ? WHERE userid = ? AND title = ?";
      $stmt = $pdo->prepare($query);
      $stmt->execute([$ext, $userid, $title]);

      move_uploaded_file($_FILES['ebookupload']['tmp_name'], $newname);

    }

    // redirect the user to the main page to see their newly added book
    header("Location: index.php");
    exit();
  }


}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Add Book</title>
  <?php include "./includes/metadata.php" ?> <!-- Including the metadata wrapper -->
</head>

<body>
<script defer src="scripts/addbook.js"></script> <!-- The script for addbook.js -->
  <?php include "./includes/header.php" ?> <!-- Including the header wrapper -->

  <main>

    <!--Section where a page asks the user for information about the book they would like to add.-->
    <section>
      <h2>Add a Book <i class="fa-solid fa-book"></i></h2> <!--Book logo from FontAwesome-->


      <!--A form used to collect the information about the book the user would like to add. Only some of the input is required information, that way the user has some flexibility of the details they can include.-->
      <form id="addbookform" name="addbookform" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="post"
        enctype="multipart/form-data">

        <div class="addbookinfo">
          <!--Getting the book's title-->
          <div>
            <label for="title">Book Title:</label>
            <input type="text" id="title" name="title" placeholder="Title" value="<?= $title ?>">
            <span class="error <?= !isset($errors['title']) ? 'hidden' : ""; ?>">Please enter a book title</span>
          </div>

          <!--Getting the author's name-->
          <div>
            <label for="author">Author:</label>
            <input type="text" id="author" name="author" placeholder="Author" value="<?= $author ?>">
            <span class="error <?= !isset($errors['author']) ? 'hidden' : ""; ?>">Please enter an author</span>
          </div>

          <!--Getting the book's publication date-->
          <div>
            <label for="pubdate">Publication Date:</label>
            <input type="date" id="pubdate" name="pubdate" value="<?= $pubdate ?>">
            <!--Errors for publication date-->
            <span class="error <?=!isset($errors['pubdatevalid']) ? 'hidden' : "";?>">Please enter a valid publication date</span>
            <span class="error <?= !isset($errors['pubdate']) ? 'hidden' : ""; ?>">Please enter a publication date</span>
          </div>

          <!--Getting the book's ISBN-->
          <div>
            <label for="ISBN">ISBN:</label>
            <input type="number" id="ISBN" name="ISBN" value="<?= $ISBN ?>">
            <span class="error <?= !isset($errors['ISBNvalid']) ? 'hidden' : ""; ?>">Please enter a valid ISBN</span>
            <span class="error <?= !isset($errors['ISBNnone']) ? 'hidden' : ""; ?>">Please enter an ISBN</span>
          </div>
          <span class="error <?= !isset($errors['uniquebook']) ? 'hidden' : ""; ?>">You already have this book in your
            library</span>

          <!--Getting the book's genre-->
          <div>
            <label for="genre">Genre:</label>
            <select name="genre" id="genre">
              <option value="0">Please Choose One</option>
              <option value="adventure">Adventure</option>
              <option value="horror">Horror</option>
              <option value="fantasy">Fantasy</option>
              <option value="romance">Romance</option>
              <option value="mystery">Mystery</option>
              <option value="scifi">Science Fiction</option>
              <option value="hifi">Historical Fiction</option>
              <option value="cofi">Contemporary Fiction</option>
              <option value="graphnovel">Graphic Novel</option>
              <!--Two more generic options so that user will always have a genre to choose from-->
              <option value="nonfic">Non-Fiction</option>
              <option value="fic">Fiction</option>
            </select>
          </div>
        </div>

        <div class="addbookinfo">
          <!--Having the user rate the book out of "five stars"-->
          <div>
            <label for="rating">Rating:</label>
            <input type="range" id="rating" name="rating" min="0" max="5" step="1">
          </div>

          <!--Getting the description for the book-->
          <div>
            <label for="description">Description:</label>
            <textarea name="description" id="description" cols="35" rows="10" maxlength="2500"></textarea>
            <div id="charCount">2500 characters left</div>
          </div>
        </div>

        <div class="addbookinfo">
          <div>
            <!--Asking the user if they have finished reading this book-->
            <fieldset>
              <legend>Have you finished reading this book?</legend>
              <div>
                <input type="radio" name="read" id="yes" value="y" />
                <label for="yes">Yes</label>
              </div>
              <div>
                <input type="radio" name="read" id="no" value="no" />
                <label for="no">No</label>
              </div>
              <div>
                <input type="radio" name="read" id="unknown" value="unknown" />
                <label for="unknown">I do not know</label>
              </div>
              <div>
                <input type="radio" name="read" id="notsay" value="notsay" />
                <label for="notsay">Prefer not to say</label>
              </div>
            </fieldset>
          </div>
        </div>


        <!--Getting the formats that the user owns this book in-->
        <div class="addbookinfo">
          <div>
            <fieldset>
              <legend>Which formats do you have this book in?</legend>
              <div>
                <input type="checkbox" name="formats[]" id="hc" value="hc" />
                <label for="hc">Hardcover</label>
              </div>
              <div>
                <input type="checkbox" name="formats[]" id="pb" value="pb" />
                <label for="pb">Paperback</label>
              </div>
              <div>
                <input type="checkbox" name="formats[]" id="epub" value="epub" />
                <label for="epub">EPub</label>
              </div>
              <div>
                <input type="checkbox" name="formats[]" id="mobi" value="mobi" />
                <label for="mobi">Mobi</label>
              </div>
              <div>
                <input type="checkbox" name="formats[]" id="PDF" value="PDF" />
                <label for="PDF">PDF</label>
              </div>
              <div>
                <input type="checkbox" name="formats[]" id="audiob" value="audiob" />
                <label for="audiob">Audio Book</label>
              </div>
              <div>
                <input type="checkbox" name="formats[]" id="other" value="other" />
                <label for="other">Other</label>
              </div>
            </fieldset>
          </div>
        </div>

        <!--Getting the epubook from the user. With the value being 2000000 since that is about the average size of an ebook.-->
        <div class="addbookinfo">
          <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
          <label for="ebookupload">Upload Ebook:</label>
          <!--Accept attribute equal to type ".epub,.pdf" so that the user can only upload those formats-->
          <input type="file" name="ebookupload" id="ebookupload" accept=".epub,.pdf" />
          <span class="error <?= !isset($errors['ebookupload']) ? 'hidden' : ""; ?>">There was an error with the ebook
            you uploaded
          </span>
        </div>
        <div class="addbookinfo">
          <!--Getting the file of the cover image from the user-->
          <div>
            <input type="hidden" name="MAX_FILE_SIZE" value="102400" />
            <label for="covimgupload">Upload Cover Image File:</label>
            <!--Accept attribute equal to type "image/*" so that the user can only upload image formats-->
            <input type="file" name="covimgupload" id="covimgupload" accept="image/*" />
          </div>
          <span class="error <?= !isset($errors['covimgupload']) ? 'hidden' : ""; ?>">There was an error with the cover
            image you uploaded
          </span>

          <!--Getting the URL for the coverimage-->
          <div>
            <label for="covimgurl">URL to Cover Image:</label>
            <input type="url" id="covimgurl" name="covimgurl">
            <span class="error <?= !isset($errors['covimgurl']) ? 'hidden' : ""; ?>">Please enter a valid URL</span>
          </div>

        </div>

        <!--A submit button so the user can add a book-->
        <div id="addbookbutton">
          <button id="submit" name="submit">Add Book</button>
        </div>
      </form>
    </section>

  </main>

  <?php include "./includes/footer.php" ?> <!-- Including the footer wrapper -->
</body>

</html>