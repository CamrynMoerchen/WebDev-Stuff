// Name: Camryn Moerchen
// Student Number: 0723708
// COIS-3420H Assignment 4
// File: deletebook.js
// File Description: This file asks the user to confirm that they want to delete the book before deleting
// the book from the database.

"use strict"; //Using strict 

// Including this block for better IE support
window.addEventListener("DOMContentLoaded", () => {

  const deleteBookButtons = document.querySelectorAll("a.deletebook"); // Getting all of the delete book button on the main page in a nodelist

  // Event listener for when the user clicks a delete book button
  // When the user clicks a delete book button, ask a confirmation question
  // Using a foreach loop to get each button
  deleteBookButtons.forEach(button => {
    // Event listener for when the user clicks a delete book button
    button.addEventListener("click", (ev) => {

      // Asking the user for confirmation
      let confirmDelete = confirm("Are you sure you would like to delete this book?");

      // If the user confirms to delete, go to to deletebook.php
      // If the user cancels the delete, stay on index.php
      if (!confirmDelete) {
        button.href = "index.php";
      }

    });

  });

});