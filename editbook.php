<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: editbook.php -->
<!-- Page Description: This page is where the user can edit a book within their library. It contains a form that gets the
information of a book from the user. If the information is valid, the book information will be updated in the database.-->


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

include 'includes/library.php'; // including library
$pdo = connectDB(); // connecting to the database

$username = $_SESSION['username']; //Get the user's username to add the book to their library
$userid = $_SESSION['userid']; // Getting the user's id

// Storing the book id in session for later use
if (isset($_GET['id'])) {
    $bookid = $_SESSION['bookid'] = ($_GET['id']); // Getting the id of the book
} else {
    $bookid = $_SESSION['bookid'];
}

// querying to get the book information from the database for display reasons
$query = "SELECT * FROM `cois3420_assgn_books` WHERE id = ? AND userid = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$bookid, $userid]);
$row = $stmt->fetch();

// If the user doesn't own the book, redirect to the main page
if (!$stmt) {
    header("Location: index.php");
    exit();
}

// Putting all of the book information into variables to prefill the form
$title = $row['title'] ?? null;
$author = $row['author'] ?? null;
$date = $row['pubdate'] ?? null;
$oldISBN = $row['ISBN'] ?? null;
$genre = $row['genre'] ?? "N/A";
$rating = $row['rating'] ?? "N/A";
$genre = $row['genre'] ?? "N/A";
$read = $row['readbook'] ?? "N/A";
$description = $row['description'] ?? "";
$formats = $row['formats'] ?? "";
$covimgurl = $row['covimgurl'] ?? "";
$covimgext = $row['covimgextension'] ?? "";

// Making the string of formats into an array so that it can be edited
$formats = explode(", ", $formats);

$errors = array(); //declare empty array to add errors to

// if submit is in the post array/if the user attempts to update the book information
if (isset($_POST['submit'])) {

    $username = $_SESSION['username']; //Get the user's username to add the book to their library
    $userid = $_SESSION['userid']; //Get the user's id to keep track of which books they own
    $bookid = $_SESSION['bookid']; // Getting the id of the book

    //Getting all the new values from POST
    $title = $_POST['title'] ?? null;
    $author = $_POST['author'] ?? null;
    $pubdate = $_POST['pubdate'] ?? null;
    $ISBN = $_POST['ISBN'] ?? $oldISBN;
    $genre = $_POST['genre'] ?? null;
    $rating = $_POST['rating'] ?? null;
    $description = $_POST['description'] ?? null;
    $read = $_POST['read'] ?? null;
    $formats = $_POST['formats'] ?? []; // if formats is empty, set it to an empty array
    $covimgurl = $_POST['covimgurl'] ?? null;

    // Checking for errors
    // Validation about checking if the user entered in the information is not needed since all of the input on
    // the form is either required or not required.

    // checking that the ISBN is unique
    // assuming that books can be uniquely identified by their ISBN number
    $query = "SELECT ISBN  FROM `cois3420_assgn_books` WHERE userid=? AND ISBN =?  ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userid, $ISBN]);

    // If the book is not unique, display an error
    if ($stmt->fetch() && $oldISBN != $ISBN) {
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

    // Only update the book information if there were no errors
    if (count($errors) === 0) {

        // Making the array of formats into a string so that it can be uploaded
        $formats = implode(", ", $formats);

        // Uploading the user's book information
        $query = "UPDATE `cois3420_assgn_books` SET title = ?, author = ?, pubdate = ?, ISBN = ?, rating = ?, description = ?, readbook = ?, formats = ?, genre=?, coverimageurl= ? WHERE userid = ? AND id = ? ";
        $stmt = $pdo->prepare($query)->execute([$title, $author, $pubdate, $ISBN, $rating, $description, $read, $formats, $genre, $covimgurl, $userid, $bookid]);

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
    <title>Edit Book</title>
    <?php include "./includes/metadata.php" ?> <!-- Including the metadata wrapper -->
</head>

<body>
    <?php include "./includes/header.php" ?> <!-- Including the header wrapper -->

    <main>


        <!--Section where the user edits a book in their library.-->
        <section>
            <!-- Changing the title depending on the book so the user knows what book they're editing -->
            <h2>Edit "<?= $title ?>"
            </h2> <!--User plus logo from FontAwesome-->

            <!--A form used to edit a book's information. -->
            <form id="addbookform" name="addbookform" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="POST"
                enctype="multipart/form-data">
                <div class="addbookinfo">
                    <!--Getting the book's title-->
                    <div>
                        <label for="title">Book Title:</label>
                        <input type="text" id="title" name="title" required value="<?= $title ?>">
                    </div>

                    <!--Getting the author's name-->
                    <div>
                        <label for="author">Author:</label>
                        <input type="text" id="author" name="author" placeholder="Author" required
                            value="<?= $author ?>">
                    </div>
   
                    <!--Getting the book's publication date-->
                    <div>
                        <label for="pubdate">Publication Date:</label>
                        <input type="date" id="pubdate" name="pubdate" required value=<?= $date ?>>
                    </div>
           
                    <!--Getting the book's ISBN-->
                    <div>
                        <label for="ISBN">ISBN:</label>
                        <input type="number" id="ISBN" name="ISBN" required value=<?= $oldISBN ?>>
                    </div>
                    <span class="error <?= !isset($errors['uniquebook']) ? 'hidden' : ""; ?>">You already have this book
                        in your library</span>

                    <!--Getting the book's genre -->
                    <div>
                        <label for="genre">Genre:</label>
                        <select name="genre" id="genre">
                            <option value="0" <?= $genre == "N/A" ? 'selected' : ""; ?>>Please Choose One</option> 
                            <option value="adventure" <?= $genre == "adventure" ? 'selected' : ""; ?>>Adventure</option>
                            <option value="horror" <?= $genre == "horror" ? 'selected' : ""; ?>>Horror</option>
                            <option value="fantasy" <?= $genre == "fantasy" ? 'selected' : ""; ?>>Fantasy</option>
                            <option value="romance" <?= $genre == "romance" ? 'selected' : ""; ?>>Romance</option>
                            <option value="mystery" <?= $genre == "mystery" ? 'selected' : ""; ?>>Mystery</option>
                            <option value="scifi" <?= $genre == "scifi" ? 'selected' : ""; ?>>Science Fiction</option>
                            <option value="hifi" <?= $genre == "hifi" ? 'selected' : ""; ?>>Historical Fiction</option>
                            <option value="cofi" <?= $genre == "cofi" ? 'selected' : ""; ?>>Contemporary Fiction</option>
                            <option value="graphnovel" <?= $genre == "graphnovel" ? 'selected' : ""; ?>>Graphic Novel
                            </option>
                            <!--Two more generic options so that user will always have a genre to choose from-->
                            <option value="nonfic" <?= $genre == "nonfic" ? 'selected' : ""; ?>>Non-Fiction</option>
                            <option value="fic" <?= $genre == "fic" ? 'selected' : ""; ?>>Fiction</option>
                        </select>
                    </div>
                </div>

                <div class="addbookinfo">
                    <!--Having the user rate the book out of "five stars"-->
                    <div>
                        <label for="rating">Rating:</label>
                        <input type="range" id="rating" name="rating" min="0" max="5" step="1" value=<?= $rating ?>>
                    </div>

                    <!--Getting the description for the book-->
                    <div>
                        <label for="description">Description:</label>
                        <textarea name="description" id="description" cols="35"
                            rows="10"><?= $description ?> </textarea>
                    </div>
                </div>

                <div class="addbookinfo">
                    <div>
                        <!--Asking the user if they have finished reading this book-->
                        <fieldset>
                            <legend>Have you finished reading this book?</legend>
                            <div>
                                <input type="radio" name="read" id="yes" value="y" <?= $read == "y" ? 'checked' : ""; ?> />
                                <label for="yes">Yes</label>
                            </div>
                            <div>
                                <input type="radio" name="read" id="no" value="no" <?= $read == "no" ? 'checked' : ""; ?> />
                                <label for="no">No</label>
                            </div>
                            <div>
                                <input type="radio" name="read" id="unknown" value="unknown" <?= $read == "unknown" ? 'checked' : ""; ?> />
                                <label for="unknown">I do not know</label>
                            </div>
                            <div>
                                <input type="radio" name="read" id="notsay" value="notsay" <?= $read == "notsay" ? 'checked' : ""; ?> />
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
                                <input type="checkbox" name="formats[]" id="hc" value="hc" <?= in_array('hc', $formats) ? 'checked' : ""; ?> />
                                <label for="hc">Hardcover</label>
                            </div>
                            <div>
                                <input type="checkbox" name="formats[]" id="pb" value="pb" <?= in_array('pb', $formats) ? 'checked' : ""; ?> />
                                <label for="pb">Paperback</label>
                            </div>
                            <div>
                                <input type="checkbox" name="formats[]" id="epub" value="epub" <?= in_array('epub', $formats) ? 'checked' : ""; ?> />
                                <label for="epub">EPub</label>
                            </div>
                            <div>
                                <input type="checkbox" name="formats[]" id="mobi" value="mobi" <?= in_array('mobi', $formats) ? 'checked' : ""; ?> />
                                <label for="mobi">Mobi</label>
                            </div>
                            <div>
                                <input type="checkbox" name="formats[]" id="PDF" value="PDF" <?= in_array('PDF', $formats) ? 'checked' : ""; ?> />
                                <label for="PDF">PDF</label>
                            </div>
                            <div>
                                <input type="checkbox" name="formats[]" id="audiob" value="audiob" <?= in_array('audiob', $formats) ? 'checked' : ""; ?> />
                                <label for="audiob">Audio Book</label>
                            </div>
                            <div>
                                <input type="checkbox" name="formats[]" id="other" value="other" <?= in_array('other', $formats) ? 'checked' : ""; ?> />
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
                    </div>

                </div>

                <!--A submit button so the user can update their book-->
                <div id="addbookbutton">
                    <button id="submit" name="submit">Update Book</button>
                </div>
            </form>

        </section>

    </main>

    <?php include "./includes/footer.php" ?> <!-- Including the footer wrapper -->
</body>

</html>