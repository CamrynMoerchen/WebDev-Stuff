// Name: Camryn Moerchen
// Student Number: 0723708
// COIS-3420H Assignment 4
// File: addbook.js
// File Description: This is a JavaScript file that includes input validation for the add book form. It checks
// that the publication date, ISBN number, and cover image url are in a valid format. As well, it checks to make sure that 
// any required inputs are inputted on form submission. As well, it also includes code that keeps track of the characters
// entered into the description textbox.

"use strict"; //Using strict 

// Including this block for better IE support
window.addEventListener("DOMContentLoaded", () => {

    // Function to check if a date is valid
    // Checks for valid format (yyyy-mm-dd) and that the date is not past the current year
    // Original regular expression from: https://stackoverflow.com/questions/15491894/regex-to-validate-date-formats-dd-mm-yyyy-dd-mm-yyyy-dd-mm-yyyy-dd-mmm-yyyy
    // Modified to only consider dates from 1000 to 2023 to be valid
    function dateIsValid(string) {

        // Building the regular expression
        // This could be done in fewer lines but I'm not too comfortable with regular expressions and feel
        // like this is easier for me to read
        var yearReg = '(1[0-9][0-9][0-9]|20[0-1][0-9]|202[0-3])'; // Allows a number between 1000 and 2023
        var monthReg = '(0[1-9]|1[0-2])';               // Allows a number between 00 and 12
        var dayReg = '(0[1-9]|1[0-9]|2[0-9]|3[0-1])';   // Allows a number between 00 and 31

        var reg = new RegExp('^' + yearReg + '-' + monthReg + '-' + dayReg + '$', 'g'); // Building the regular expression
        return reg.test(string);
    }

    // Function to check if an ISBN is valid
    function ISBNIsValid(string) {
        // Checking to make sure the ISBN number is either 10 or 13 digits long
        return ((string.length == 10) || (string.length == 13));
    }

    // Function to check if a url is valid
    // Original regular expression from: https://stackoverflow.com/questions/3809401/what-is-a-good-regular-expression-to-match-a-url
    function URLIsValid(string) {
        return /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/.test(string);
    }

    const addBookForm = document.getElementById("addbookform"); // Getting the add book form

    // Event listener for when the user submits the add book form
    // Contains javascript form validation
    addBookForm.addEventListener("submit", (ev) => {

        //declaring a boolean flag valid set to false for determining if there were any errors found below
        let error = false;

        const inputTitle = document.getElementById("title"); // Getting the title input box
        const titleError = inputTitle.nextElementSibling // Getting the title error

        const inputAuthor = document.getElementById("author"); // Getting the author input box
        const authorError = inputAuthor.nextElementSibling // Getting the author error

        const dateInput = document.getElementById("pubdate"); // Getting the input for the publication date
        const dateValidError = dateInput.nextElementSibling // Getting the pubdate validation error
        const dateError = dateValidError.nextElementSibling // Getting the pubdate error

        const ISBNInput = document.getElementById("ISBN"); // Getting the input for the ISBN
        const ISBNValidError = ISBNInput.nextElementSibling // Getting the ISBN validation error
        const ISBNError = ISBNValidError.nextElementSibling // Getting the ISBN error

        const URLInput = document.getElementById("covimgurl"); // Getting the input for the URL
        const URLError = URLInput.nextElementSibling // Getting the URL error

        // Checking that the title has been entered
        if (inputTitle.value) {
            titleError.classList.add("hidden"); // If the title is valid, hide the error
        } else {
            titleError.classList.remove("hidden"); // If the title is invalid, show the error
            error = true;
        }

        // Checking that the author has been entered
        if (inputAuthor.value) {
            authorError.classList.add("hidden");
        } else {
            authorError.classList.remove("hidden");
            error = true;
        }

        // Checking that the publication date has been entered
        if (dateInput.value) {
            dateError.classList.add("hidden");
        } else {
            dateError.classList.remove("hidden");
            error = true;
        }

        // Checking that the ISBN has been entered
        if (ISBNInput.value) {
            ISBNError.classList.add("hidden");
        } else {
            ISBNError.classList.remove("hidden");
            error = true;
        }

        // Checking if the date is valid if it's entered
        // If the date is not valid, display an error
        if (!dateIsValid(dateInput.value) && dateInput.value != "") {
            dateValidError.classList.remove("hidden");
            error = true;
        }
        else {
            dateValidError.classList.add("hidden");
        }

        // Checking if the ISBN is valid if it's entered
        // If the ISBN is not valid, display an error
        if (!ISBNIsValid(ISBNInput.value) && ISBNInput.value != "") {
            ISBNValidError.classList.remove("hidden");
            error = true;
        }
        else {
            ISBNValidError.classList.add("hidden");
        }

        // Checking if the URL is valid if it's entered
        // If the URL is not valid, display an error
        if (!URLIsValid(URLInput.value) && URLInput.value != "") {
            URLError.classList.remove("hidden");
            error = true;
        }
        else {
            URLError.classList.add("hidden");
        }

        // Make this conditional on if there are errors.
        if (error) {
            ev.preventDefault(); //STOP FORM SUBMISSION IF THERE ARE ERRORS
        }

    });

    const descripInput = document.getElementById("description"); // getting the description input
    const charCount = document.getElementById("charCount"); // getting the span that displays the character count
    const limit = 2500; // word limit

    // Event listener for the description textbox
    // When the user inputs/types something into the textbox, display how many characters are left of 
    // the character limit
    descripInput.addEventListener("input", function () {
        const remaining = limit - descripInput.value.length;
        charCount.textContent = remaining + ' characters left';
    });
});