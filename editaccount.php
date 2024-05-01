<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: editaccount.php -->
<!-- Page Description: This page is where the user can edit their account. It contains a form that has the user's account information
that they can change to be updated. Once their account informationis updated, they're redirected to the main page.-->


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

// Getting the user's current account information from the database
$query = "select * from `cois3420_assgn_users` where username=?";
$stmt = $pdo->prepare($query);
$stmt->execute([$_SESSION['username']]);
$row = $stmt->fetch();

// Defining the information as variables for readability in later use
$email = $row['email'];
$username = $row['username'];
$name = $row['firstname'];
$userid = $row['id'];


$errors = array(); //declare empty array to add errors too

//If submit was in the post array
if (isset($_POST['submit'])) {

  //get data from post or set to NULL if doesn't exist
  $newusername = $_POST['username'] ?? null;
  $newemail = $_POST['email'] ?? null;
  $name = $_POST['name'] ?? null;
  $password = $_POST['password'] ?? null;
  $verify = $_POST['passwordverify'] ?? null;

  // Checking for errors
  // Validation about checking if the user entered in the information is not needed since all of the input on
  // the form is required.

  //checking that the username is unique
  $query = "SELECT username FROM `cois3420_assgn_users` WHERE username=? ";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$newusername]);
  $row = $stmt->fetch();

  // If the new username that the user entered is not unique, display an error
  if ($row && $newusername != $username) {
    $errors['uniqueuser'] = true;
  }

  // Checking that the email is unique
  // (The email must be unique for sending out password reset emails)
  $query = "SELECT email FROM `cois3420_assgn_users` WHERE email=? ";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$email]);

  // If the username was found in the database/is not unique, display an error
  if ($stmt->fetch() && $newemail != $email) {
    $errors['uniqueemail'] = true;
  }

  // Verifying the password
  if (($password != $verify)) {
    $errors['notsame'] = true;
  }

  // Making sure that the password associated with the original username is correct
  $query = "select password, username from `cois3420_assgn_users` where username=?";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$_SESSION['username']]);
  $row = $stmt->fetch();

  // If the password doesn't match the one in the database, display an error
  if (!password_verify($password, $row['password'])) {
    $errors['incorrectpassword'] = true;
  }

  //only create an account if there weren't any errors
  if (count($errors) === 0) {

    // Query to update the database with the new information abou the user
    $query = "UPDATE `cois3420_assgn_users` SET email = ?, username = ?, firstname = ? WHERE username = ?";
    $stmt1 = $pdo->prepare($query)->execute([$email, $newusername, $name, $username]);

    // 'Resetting' the session
    session_destroy(); // Destroy session before starting the new one
    session_start(); //Starting the session

    //Putting the username and id into the session array
    $_SESSION['username'] = $newusername;
    $_SESSION['userid'] = $userid;
    $_SESSION['id'] = session_id();

    //send the user to the main page.
    header("Location: index.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Edit Account</title>
  <?php include "./includes/metadata.php" ?> <!-- Including the metadata wrapper -->
</head>

<body>
  <script defer src="scripts/deleteaccount.js"></script> <!-- The script for deleteaccount.js -->
  <?php include "./includes/header.php" ?> <!-- Including the header wrapper -->

  <main>

    <!--Section where the user creates an account.-->
    <section>
      <h2>Edit Your Account <i class="fa-solid fa-user"></i></h2> <!--User plus logo from FontAwesome-->

      <!--A form used to collect a user's account information. All input is required information.-->
      <form id="editaccountform" name="editaccountform" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="POST">
        <!--Getting the user's email-->
        <div>
          <label for="email">Email:</label>
          <input type="email" id="email" name="email" placeholder="Email Address" required value="<?= $email ?>">
        </div>
        <!-- Displaying an error if the email entered is not unique -->
        <span class="error <?= !isset($errors['uniqueemail']) ? 'hidden' : ""; ?>">There is already an account with this
          email</span>

        <!--Getting the user's username-->
        <div>
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" placeholder="Username" required value="<?= $username ?>">
        </div>
        <!-- Displaying an error if the username entered is not unique -->
        <span class="error <?= !isset($errors['uniqueuser']) ? 'hidden' : ""; ?>">This username is already taken</span>

        <!--Getting the user's first name so that the application can display it on other pages-->
        <div>
          <label for="name">First Name:</label>
          <input type="text" id="name" name="name" placeholder="First Name" required value="<?= $name ?>">
        </div>

        <!--Getting the user's password-->
        <div>
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required>
        </div>

        <!--Verifying the user's password-->
        <div>
          <label for="passwordverify">Verify Password:</label>
          <input type="password" id="passwordverify" name="passwordverify" required>
        </div>
        <!-- Displaying an error if the two passwords entered are not the same -->
        <span class="error <?= !isset($errors['notsame']) ? 'hidden' : ""; ?>">Please enter the same password</span>
        <!-- Displaying an error if the password entered is not correct -->
        <span class="error <?= !isset($errors['incorrectpassword']) ? 'hidden' : ""; ?>">Incorrect password</span>


        <!--A submit button so the user can update their account-->
        <div>
          <button id="submit" name="submit">Update Account</button>
        </div>
      </form>

      <!-- Links to the delete your account and update password pages-->
      <a href="updatepassword.php" class="forgot">Update Your Password</a>
      <a href="deleteaccount.php" class="forgot" id = "deleteaccount" >Delete Your Account</a>

    </section>

  </main>

  <?php include "./includes/footer.php" ?> <!-- Including the footer wrapper -->
</body>

</html>