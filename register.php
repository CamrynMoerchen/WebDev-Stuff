<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: register.php -->
<!-- Page Description: This page is where the user can create an account. 
  It contains a form that takes the user's information (Email, username, first name, and password). If all the information is valid, the
form is then processed and the user is then added to the database. Once the user is added, they are redirected back to the login page.-->


<?php
include_once "includes/library.php"; // Including library

$pdo = connectDB(); // Making a connection to the database
$errors = array(); // Declaring an empty array to add errors too

// get data from post or set to NULL if doesn't exist
$username = $_POST['username'] ?? null;
$email = $_POST['email'] ?? null;
$name = $_POST['name'] ?? null;
$password = $_POST['password'] ?? null;
$verify = $_POST['passwordverify'] ?? null;

//If submit was in the post array/user attempts to create an accounr
if (isset($_POST['submit'])) {

  // Checking for errors
  // Validation about checking if the user entered in the information is not needed since all of the input on
  // the form is required.

  // Checking that the username is unique
  $query = "SELECT username FROM `cois3420_assgn_users` WHERE username=? ";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$username]);

  // If the username was found in the database/is not unique, display an error
  if ($stmt->fetch()) {
    $errors['uniqueuser'] = true;
  }

    // Checking that the email is unique
    // (The email must be unique for sending out password reset emails)
    $query = "SELECT email FROM `cois3420_assgn_users` WHERE email=? ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$email]);
  
    // If the username was found in the database/is not unique, display an error
    if ($stmt->fetch()) {
      $errors['uniqueemail'] = true;
    }

  // Verifying that the two passwords entered are the same. If they're not, display an error.
  if (($password != $verify)) {
    $errors['notsame'] = true;
  }

  // Only create an account if there weren't any errors
  if (count($errors) === 0) {
    //Hashing the user's password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Inserting the user's account information into the database
    $query = "INSERT INTO `cois3420_assgn_users` VALUES (NULL, ?, ?, ?, ?, TRUE, NULL)";
    $stmt = $pdo->prepare($query)->execute([$email, $username, $name, $hash]);


    // Redirect the user to the login page once the account is created.
    header("Location: login.php");
    exit();
  }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Create an Account</title>
  <?php include "./includes/metadata.php" ?> <!-- Including the metadata wrapper -->
</head>

<body>
<script defer src="scripts/register.js"></script> <!-- The script for register.js -->
  <?php include "./includes/header.php" ?> <!-- Including the header wrapper -->

  <main>


    <!--Section where the user creates an account.-->
    <section>
      <h2>Create an Account <i class="fa-solid fa-user-plus"></i></h2> <!--User plus logo from FontAwesome-->

      <!--A form used to collect a new user's account information. All input is required information.-->
      <form id="newaccountform" name="newaccountform" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="POST">

        <!--Getting the user's email-->
        <div>
          <label for="email">Email:</label>
          <input type="text" id="email" name="email" placeholder="Email Address" value="<?= $email ?>">
          <span class="error <?= !isset($errors['validemail']) ? 'hidden' : ""; ?>">Please enter a valid email</span>  <!-- Error if the email format is not valid-->
          <span class="error <?= !isset($errors['email']) ? 'hidden' : ""; ?>">Please enter an email</span> <!-- Error if the user doesn't enter an email-->
        </div>
        <!-- Displaying an error if the email entered is not unique -->
        <span class="error <?= !isset($errors['uniqueemail']) ? 'hidden' : ""; ?>">There is already an account with this email</span>

        <!--Getting the user's username-->
        <div>
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" placeholder="Username" value="<?= $username ?>">
          <span class="error <?= !isset($errors['username']) ? 'hidden' : ""; ?>">Please enter a username</span> <!-- Error if the user doesn't enter a username-->
        </div>
        <!-- Displaying an error if the username entered is not unique -->
        <span class="error <?= !isset($errors['uniqueuser']) ? 'hidden' : ""; ?>">This username is already taken</span>

        <!--Getting the user's first name so that the application can display it on other pages-->
        <div>
          <label for="name">First Name:</label>
          <input type="text" id="name" name="name" placeholder="First Name" value="<?= $name ?>">
          <span class="error <?= !isset($errors['name']) ? 'hidden' : ""; ?>">Please enter a name</span> <!-- Error if the user doesn't enter a name-->
        </div>

        <!--Getting the user's password-->
        <div>
          <label for="password">Password:</label>
          <input type="password" id="password" name="password">
          <span class="error <?= !isset($errors['password']) ? 'hidden' : ""; ?>">Please enter a password</span> <!-- Error if the user doesn't enter a password-->
        </div>

        <!--Verifying the user's password-->
        <div>
          <label for="passwordverify">Verify Password:</label>
          <input type="password" id="passwordverify" name="passwordverify">
          <span class="error <?= !isset($errors['verifypass']) ? 'hidden' : ""; ?>">Please re-enter the password</span> <!-- Error if the user doesn't enter the password again-->
        </div>
        <!-- Displaying an error if the two passwords are not the same -->
        <span class="error <?= !isset($errors['notsame']) ? 'hidden' : ""; ?>">Please enter the same password</span>

        <!--A submit button so the user can create their account-->
        <div>
          <button id="submit" name="submit">Create Account</button>
        </div>
      </form>

    </section>

  </main>

  <?php include "./includes/footer.php" ?> <!-- Including the footer wrapper -->
</body>

</html>