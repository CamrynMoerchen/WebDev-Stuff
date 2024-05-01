<!-- Name: Camryn Moerchen -->
<!-- Student Number: 0723708 -->
<!-- COIS-3420H Assignment 4 -->
<!-- Page: updatepassword.php -->
<!-- Page Description: This page allows a logged in user to update their password. -->


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

$errors = array(); //declare empty array to add errors too


// Getting the user's username and id from the session
$username = $_SESSION['username'];
$id = $_SESSION['userid'];

if (isset($_POST['submit'])) {

    //get data from post or set to NULL if doesn't exist
    $password = $_POST['password'] ?? null;
    $verify = $_POST['passwordverify'] ?? null;

    // Verifying the passwords match each other, if not display an error
    if ($password != $verify) {
        $errors['notsame'] = true;
    }

    // only reset the password if there weren't any errors
    if (count($errors) === 0) {
        //Hashing the user's password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Updating the user's password in the database
        $query = "UPDATE `cois3420_assgn_users` set password = ? where username = ? AND id = ?";
        $stmt1 = $pdo->prepare($query)->execute([$hash, $username, $id]);

        // Redirecting the user to the main page
        header("Location: index.php");
        exit();
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Update Password</title>
    <?php include "./includes/metadata.php" ?> <!-- Including the metadata wrapper -->
</head>

<body>
    <?php include "./includes/header.php" ?> <!-- Including the header wrapper -->

    <main>


        <!--Section where the user updates their password.-->
        <section>
            <h2>Update Your Password</h2>

            <!--A form used to user's new password. All input is required information.-->
            <form id="resetpassform" name="resetpassform" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>"
                method="POST">

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
                    <!-- Displaying an error if the passwords don't match-->
                    <span class="error <?= !isset($errors['notsame']) ? 'hidden' : ""; ?>">Please enter the same
                        password</span>

                    <!--A submit button so the user can update their password-->
                    <div>
                        <button id="submit" name="submit">Update Password</button>
                    </div>
            </form>

        </section>

    </main>

    <?php include "./includes/footer.php" ?> <!-- Including the footer wrapper -->
</body>

</html>