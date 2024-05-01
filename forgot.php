<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: forgot.php -->
<!-- Page Description: This page is where the user can reset their password if they forgot it. 
  It contains a form that takes the user's email. If there is an account with that email associated with it, an email with a link to the password reset
  page will be sent to it.-->

<?php
require_once "Mail.php"; // Including the pear SMTP mail library

include 'includes/library.php'; // Including library
$pdo = connectDB(); // Connecting to the database

$email = $_POST['email'] ?? ''; // Getting the entered email from POST, and setting it to false if it's not yet set

// Querying to check if there is a user that is associated with the entered email
$query = "select id, username from `cois3420_assgn_users` where email = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$email]);

// If submit was in the post array and the email is associated with an account, send an email to that user
if (isset($_POST['submit']) && ($row = $stmt->fetch())) {
  $guid = uniqid($row['username'], true); // Generating a guid for security reasons

  // Updating the database to include the guid
  $query = "UPDATE `cois3420_assgn_users` SET uuid = ? WHERE id = ? AND username = ?";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$guid, $row['id'], $row['username']]);

  // Creating the reset password URL with the guid
  $URL = "https://loki.trentu.ca/~camrynmoerchen/3420/assignments/assn3/resetpassword.php?uuid=" . $guid;

  // Creating and sending the email
  $from = "Bookleaf Password System Reset <camrynmoerchen@trentu.ca>"; // My email 
  $to = "<" . $email . ">"; // The user's email
  $subject = "Reset Your Bookleaf Account Password"; // Subject of the email
  $body = "Hello! Please use the following link to reset your Bookleaf account password: " . $URL; // Body of the email which includes the URL
  $host = "smtp.trentu.ca";
  $headers = array(
    'From' => $from,
    'To' => $to,
    'Subject' => $subject
  );
  $smtp = Mail::factory(
    'smtp',
    array('host' => $host)
  );

  $mail = $smtp->send($to, $headers, $body);

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Forgot Password</title>
  <?php include "./includes/metadata.php" ?> <!-- Including the metadata wrapper -->
</head>

<body>
  <?php include "./includes/header.php" ?> <!-- Including the header wrapper -->

  <main>

    <!--Section where the user enters their email to request a password reset.-->
    <section>
      <h2>Forgot Your Password?</h2>

      <!--Explaining the process to the user.-->
      <span> Please enter your email down below. If your email address is attached to an account, an email will be sent
        to
        it requesting you to reset your password.</span>

      <!--A form used to collect a user's email to send a request to reset their password. All input is required information.-->
      <form id="forgotpasswordform" name="forgotpasswordform" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>"
        method="post">

        <!--Getting the user's email-->
        <div>
          <label for="email">Email:</label>
          <input type="email" id="email" name="email" placeholder="Email Address" required value="<?= $email ?>">
        </div>

        <!--A submit button so the user can submit a request to change their password.-->
        <div>
          <button id="submit" name="submit">Send Email</button>
        </div>
      </form>

    </section>

  </main>

  <?php include "./includes/footer.php" ?> <!-- Including the footer wrapper -->
</body>

</html>