// Name: Camryn Moerchen
// Student Number: 0723708
// COIS-3420H Assignment 4
// File: register.js
// File Description: This file is used for form validation for the create account form. It includes email format validation,
// and validation that all required fields have been entered.

"use strict"; //Using strict 

// Including this block for better IE support
window.addEventListener("DOMContentLoaded", () => {

    // Function to check if the input is in valid email format
    // Original regular expression from: https://www.w3resource.com/javascript/form/email-validation.php
    function emailIsValid(string) {
        return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(string);
    }

    const addAccountForm = document.getElementById("newaccountform"); // Getting the add account form

    // Event listener for when the user submits the create account form
    // Contains javascript form validation
    addAccountForm.addEventListener("submit", (ev) => {

        //declare a boolean flag valid set to false for determining if there were any errors found below
        let error = false;

        const emailInput = document.getElementById("email"); // Getting the input for the email
        const emailValidError = emailInput.nextElementSibling; // Getting the email valid error
        const emailError = emailValidError.nextElementSibling; // Getting the email error

        const inputUsername = document.getElementById("username"); // Getting the username input box
        const usernameError = inputUsername.nextElementSibling; // Getting the username error

        const inputName = document.getElementById("name"); // Getting the name input box
        const nameError = inputName.nextElementSibling; // Getting the name error message

        const inputPassword = document.getElementById("password"); // Getting the password input box
        const passwordError = inputPassword.nextElementSibling; // Getting the password error
        
        const inputPasswordVerify = document.getElementById("passwordverify"); // Getting the password verify input box
        const passwordVerifyError = inputPasswordVerify.nextElementSibling; // Getting the password verify error

        // Checking if the email is valid if it is entered
        // If the date is not valid, display an error
        if (!emailIsValid(emailInput.value) && emailInput.value != "") {
            emailValidError.classList.remove("hidden");
            error = true;
        }
        else {
            emailValidError.classList.add("hidden");
        }

        // Checking that the email has been entered
        if (emailInput.value) {
            emailError.classList.add("hidden");
        } else {
            emailError.classList.remove("hidden");
            error = true;
        }

        // Checking that the username has been entered
        if (inputUsername.value) {
            usernameError.classList.add("hidden");
        } else {
            usernameError.classList.remove("hidden");
            error = true;
        }

        // Checking that the first name has been entered
        if (inputName.value) {
            nameError.classList.add("hidden");
        } else {
            nameError.classList.remove("hidden");
            error = true;
        }

        // Checking that the password has been entered
        if (inputPassword.value) {
            passwordError.classList.add("hidden");
        } else {
            passwordError.classList.remove("hidden");
            error = true;
        }

        // Checking that the verified password has been entered
        if (inputPasswordVerify.value) {
            passwordVerifyError.classList.add("hidden");
        } else {
            passwordVerifyError.classList.remove("hidden");
            error = true;
        }

        // Make this conditional on if there are errors.
        if (error) {
            ev.preventDefault(); //STOP FORM SUBMISSION IF THERE ARE ERRORS
        }

    });
});