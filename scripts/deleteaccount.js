// Name: Camryn Moerchen
// Student Number: 0723708
// COIS-3420H Assignment 4
// File: deleteaccount.js
// File Description: This file asks the user to confirm that they want to delete their account before deleting
// the account from the database.

"use strict"; //Using strict 

// Including this block for better IE support
window.addEventListener("DOMContentLoaded", () => {

  const deleteAccount = document.getElementById("deleteaccount"); // Getting the delete account button on the edit account page

  // Event listener for when the user clicks the delete account button
  deleteAccount.addEventListener("click", (ev) => {

    // Asking the user for confirmation
    let confirmDelete = confirm("Are you sure you would like to delete your account?");

    // If the user confirms to delete, go to to deleteaccount.php
    // If the user cancels the delete, stay on edditaccount.php
    if (!confirmDelete) {
      deleteAccount.href = "editaccount.php";
    }

  });
});