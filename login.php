<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: login.php -->
<!-- Page Description: This page is where the user can log into their account. It contains a form that asks for the user's login information
  as well as if the user would like their login information remembered. If the user's information matches with information in the database, they will
  be logged in. If not, errors will be displayed. This page also contains the link to the 'forgot password' page.-->

<?php

// If the user clicked remember me last time, autofill username and keep the remember me box checked
if (isset($_COOKIE['mysitecookie'])) {
  $username = $_COOKIE['mysitecookie']; // getting the username from the cookie
  $remember = "Y"; // Keep user remembered
}

// If there is no cookie, keep the form empty
else {
  $username = "";
  $remember = null;
}

// If submit was in the post array/the user made an attempt to login
if (isset($_POST['submit'])) {

  // get username and password from post or set to NULL if doesn't exist
  $username = $_POST['username'] ?? null;
  $password = $_POST['password'] ?? null;
  $remember = $_POST['rememberme'] ?? null;

  include 'includes/library.php'; // including the library file
  $pdo = connectDB(); // connecting to the database
  $errors = array(); // declaring an empty array to add errors to

  // querying to see if the user exists in the database, and if they do, getting their password and username
  $query = "select password, id from `cois3420_assgn_users` where username=?";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$username]);

  // if nothing is returned from the query, display an error
  if (!$row = $stmt->fetch()) {
    $errors['user'] = true;

  } 
  
  // if username is valid, continue
  else {

    // Verifying the password
    // If the password is valid, log the user in and store their username and userid in the session array
    if (password_verify($password, $row['password'])) {
      session_start(); //Starting the session

      //Putting the username and id into the session array
      $_SESSION['username'] = $username;
      $_SESSION['userid'] = $row['id'];
      $_SESSION['id'] = session_id();

      // If the user clicked remember me, create a cookie
      if (isset($_POST['rememberme'])) {
        setcookie("mysitecookie", $username, time() + 60 * 60 * 24 * 30 * 12);
      }

      // If not clicked, destroy the cookie
      else {
        setcookie("mysitecookie", $username, 1);
      }


      // Redirecting to the main page once the user is logged in
      header("Location: index.php");
      exit();
    } 
    // If the password is not valid, display an error
    else {
      $errors['password'] = true;
    }

  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Login</title>
  <?php include "./includes/metadata.php" ?> <!-- Including the metadata wrapper -->
</head>

<body>
  <script defer src="scripts/login.js"></script> <!-- The script for showpass.js -->
  <?php include "./includes/header.php" ?> <!-- Including the header wrapper -->

  <main>

    <section>
      <h2>Login <i class="fa-solid fa-user"></i></h2> <!--User logo from FontAwesome-->

      <!--A form used to collect the user's login information. All input is required information to log in. -->
      <form id="loginform" name="loginform" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="POST">

        <!--Getting the user's username and allowing the input to be sticky.-->
        <div>
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" placeholder="Username" value="<?= $username ?>" required>
          <span class="error <?= !isset($errors['nouser']) ? 'hidden' : ""; ?>">Please enter a username</span>
        </div>
        <!-- Error that will display if the username does not exist in the database-->
        <span class="error <?= !isset($errors['user']) ? 'hidden' : ""; ?>">That user doesn't exist</span>

        <!--Getting the user's password-->
        <div>
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required>
          <span class="error <?= !isset($errors['nopass']) ? 'hidden' : ""; ?>">Please enter a password</span>
        </div>

        <!--Used to allow the user to toggle the password's visibility-->
        <div>
        <label for="showpass">Show Password:</label>
          <input type="checkbox" name="showpass" id="showpass" value="N"/>
        </div>

        <!-- Error that will display if the password is incorrect-->
        <span class="error <?= !isset($errors['password']) ? 'hidden' : ""; ?>">Incorrect password</span>

        <!--Checking to see if the user would like the application to remember their login information.-->
        <!--Default value is no.-->
        <div>
          <label for="rememberme">Remember Me:</label>
          <input type="checkbox" name="rememberme" id="rememberme" value="Y" <?= $remember == "Y" ? 'checked' : ""; ?> />
        </div>

        <!--A submit button so the user can login-->
        <div>
          <button id="submit" name="submit">Login</button>
        </div>
      </form>

      <!--Link to the forgot your password page-->
      <a href="forgot.php" class="forgot">Forgot your password?</a>

    </section>

  </main>

  <?php include "./includes/footer.php" ?> <!-- Including the footer wrapper -->
</body>

</html>