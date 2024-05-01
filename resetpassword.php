<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: resetpassword.php -->
<!-- Page Description: This page is where the user can reset their password. Once the password is reset, they are redirected back to the login page.-->


<?php

session_start(); // start session

include 'includes/library.php'; // including library
$pdo = connectDB(); // connecting to the database

$errors = array(); //declare empty array to add errors too

// Storing the guid in the session 
if (isset($_GET['uuid'])) {
  $guid = $_SESSION['uuid'] = ($_GET['uuid']);
} else {
  $guid = $_SESSION['uuid'];
}

// Querying to make sure the guid corresponds to a user in the database
$query = "select * from `cois3420_assgn_users` where uuid=?";
$stmt = $pdo->prepare($query);
$stmt->execute([$guid]);

// If there is nothing selected, send the user back to the login page
if (!$row = $stmt->fetch()) {
  header("Location: login.php");
  exit();
}

// Defining the information as variables for readability later
$username = $row['username'];

// If submit was in the post array/the user attempted to reset their password
if (isset($_POST['submit'])) {

  // get data from post or set to NULL if it doesn't exist
  $password = $_POST['password'] ?? null;
  $verify = $_POST['passwordverify'] ?? null;

  // Validating to make sure the user has entered something for each input is unnessecary because all input fields are required

  // Verifying the password
  if (($password != $verify)) {
    $errors['notsame'] = true;
  }

  // Only reset the password if there weren't any errors
  if (count($errors) === 0) {
    //Hashing the user's password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Updating the table with user's new password
    $query = "UPDATE `cois3420_assgn_users` set password = ? where uuid = ?";
    $stmt1 = $pdo->prepare($query)->execute([$hash, $guid]);

    // destroying the session once the password has been reset
    session_destroy();

    // setting the guid to null once the password has been reset so that the link cannot be used again
    $query = "UPDATE `cois3420_assgn_users` set uuid = NULL where uuid = ?";
    $stmt1 = $pdo->prepare($query)->execute([$guid]);

    // redirecting the user to the login page.
    header("Location: login.php");
    exit();
  }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Reset Password</title>
  <?php include "./includes/metadata.php" ?> <!-- Including the metadata wrapper -->
</head>

<body>
  <?php include "./includes/header.php" ?> <!-- Including the header wrapper -->

  <main>


    <!--Section where the user resets their password.-->
    <section>
      <h2>Reset Your Password</h2> <!--User plus logo from FontAwesome-->

      <!--A form used to collect a user's new password. All input is required information.-->
      <form id="resetpassform" name="resetpassform" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="POST">

        <!--Getting the user's new password-->
        <div>
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required>
        </div>

        <!--Verifying the user's password-->
        <div>
          <label for="passwordverify">Verify Password:</label>
          <input type="password" id="passwordverify" name="passwordverify" required>
        </div>
        <!--Display an error if the passwords don't match -->
        <span class="error <?= !isset($errors['notsame']) ? 'hidden' : ""; ?>">Please enter the same password</span>

        <!--A submit button so the user can reset their password-->
        <div>
          <button id="submit" name="submit">Reset Password</button>
        </div>
      </form>

    </section>

  </main>

  <?php include "./includes/footer.php" ?> <!-- Including the footer wrapper -->
</body>

</html>