// Name: Camryn Moerchen
// Student Number: 0723708
// COIS-3420H Assignment 4
// File: details.js
// File Description: This file is used to display a modal window containing the books details when the
// book details button is clicked. It uses a XMLHttpRequest object to display the details for the given book.

"use strict"; //Using strict 

// Including this block for better IE support
window.addEventListener("DOMContentLoaded", () => {

    // Get the modal
    const modalWindow = document.getElementById("modalWindow");

    // Get the details buttons that open the modal
    const detailsButtons = document.querySelectorAll("a.details");

    // Get the element (x) that closes the modal
    const closingX = document.getElementById("close");

    // Get the div where the details of the page will go
    const bookDetails = closingX.nextElementSibling;


    // Event listener for when the user clicks a details button
    // When the user clicks a details button, display the window
    // Using a foreach loop to get each button
    detailsButtons.forEach(button => {
        button.addEventListener("click", (ev) => {

            modalWindow.style.display = "block"; // changing the display style from none to block so that it's now visible
            var bookid = button.id; //getting the id of the book

            // Creating an XMLHttpRequest request object 
            const xhr = new XMLHttpRequest();

            // opening get connection and supplying the bookid to get the correct book details
            xhr.open("GET", "details.php?id=" + bookid);

            xhr.addEventListener("load", (ev) => {
                // If a successful response is recieved
                if (xhr.status == 200) {

                    let response = xhr.response; // getting the response of the request

                    // If there was a response, display the book details within the bookDetails div
                    if (response) {
                        bookDetails.innerHTML = response;
                    }

                    // If the user does not own the book, notify them
                    else {
                        bookDetails.innerHTML("<span class = 'ownError'>You do not own this</span>");
                    }

                }

                // If there was an error with loading the book detais, notify the user
                else {
                    bookDetails.innerHTML("<span class = 'loadError'>Something went wrong with loading this window</span>");
                }
            });

            // Sending the request
            xhr.send();

        })

    });


    // When the user clicks the X, close the modal window
    closingX.addEventListener("click", (ev) => {
        modalWindow.style.display = "none"; // changing the display style from block to none so that it 'disappears'

        // If the ownership error exists, remove it
        if (document.querySelector("span.ownerError")) {
            document.querySelector("span.ownerError").remove(); // Removing the ownership error from the modal window
        }

        // If the load error exists, remove it
        if (document.querySelector("span.loadError")) {
            document.querySelector("span.loadError").remove(); // Removing the load error from the modal window
        }

    });
});