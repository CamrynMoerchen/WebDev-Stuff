// Name: Camryn Moerchen
// Student Number: 0723708
// COIS-3420H Assignment 4
// File: header.js
// File Description: This file allows the header to be collapsible using a chevron button. 

"use strict"; //Using strict 

// Including this block for better IE support
window.addEventListener("DOMContentLoaded", () => {

    const header = document.querySelector("header"); // Getting the header
    const chevronSpan = header.nextElementSibling; // Getting the chevron 'button'
    const chevronDown = chevronSpan.firstElementChild; // Getting the chevron that's facing up
    const chevronUp = chevronSpan.lastElementChild; // Getting the chevron that's facing down

    chevronDown.setAttribute("class", "hidden"); // Ensuring that the down chevron is hidden on page load

    // Event listener for when the user clicks the chevron
    chevronSpan.addEventListener("click", (ev) => {

        // If the header is already hidden, make it visible and change the direction of the chevron
        if (header.classList.contains("hidden")) {
            header.classList.remove("hidden"); // Making the header visible
            chevronDown.setAttribute("class", "hidden"); // Hiding the down chevron
            chevronUp.setAttribute("class", "fa-solid fa-chevron-up fa-2x"); // Making the up chevron visible
        }

        // If the header is not hidden yet, hide it and change the direction of the chevron
        else {
            header.classList.add("hidden"); // Hiding the header
            chevronUp.setAttribute("class", "fa-solid fa-chevron-down fa-2x"); // Making the up chevron visible
            chevronDown.setAttribute("class", "hidden"); // Hiding the down chevron
        }


    });
});