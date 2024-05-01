// Name: Camryn Moerchen
// Student Number: 0723708
// COIS-3420H Assignment 4
// File: login.js
// File Description: This file contains JavaScript validation for the login page. As well, it provides 
// functionality to the show password checkbox.

"use strict"; //Using strict 

// Including this block for better IE support
window.addEventListener("DOMContentLoaded", () => {

  const showPassword = document.getElementById("showpass"); // Getting the show password checkbox
  const inputUsername = document.getElementById("username"); // Getting the username input box
  const usernameError = inputUsername.nextElementSibling // Getting the username error
  const inputPassword = document.getElementById("password"); // Getting the password input box
  const passwordError = inputPassword.nextElementSibling // Getting the password error
  const loginForm = document.getElementById("loginform"); // Getting the login form


  // Event listener for when the user chooses to show the password
  showPassword.addEventListener("click", (ev) => {

    // if the checkbox is checked, change the type of the password input to text
    if (showPassword.checked) {
      inputPassword.type = "text";
    }

    // if the checkbox is unchecked, change the type of the input back to password
    else {
      inputPassword.type = "password";
    }

  });

  // Event listener for when the user attempt to submit the form
  // Validates that the username and password has been entered
  loginForm.addEventListener("submit", (ev) => {
    //declare a boolean flag valid set to false for determining if there were any errors found below
    let error = false;


    // Checking that the username has been entered
    if (inputUsername.value) {
      usernameError.classList.add("hidden");
    } else {
      usernameError.classList.remove("hidden");
      error = true;
    }

    // Checking that the password has been entered
    if (inputPassword.value) {
      passwordError.classList.add("hidden");
    } else {
      passwordError.classList.remove("hidden");
      error = true;
    }

    if (error) ev.preventDefault(); //STOP FORM SUBMISSION IF THERE ARE ERRORS
  });

});