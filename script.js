// script.js - Client-side validation
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("churchForm"); // Correct ID
    const messageDiv = document.getElementById("message");
    
    form.addEventListener("submit", function(e) {
        messageDiv.style.display = "none";
        messageDiv.className = "message";

        const name = document.querySelector("#full_name").value.trim();
        const email = document.querySelector("#email").value.trim();
        const message = document.querySelector("#messageInput").value.trim();
        const errors = [];

        if (name.length < 2) errors.push("Name must be at least 2 characters");
        if (!validateEmail(email)) errors.push("Please enter a valid email address");
        if (message.length < 10) errors.push("Message must be at least 10 characters");

        if (errors.length > 0) {
            e.preventDefault();
            messageDiv.innerHTML = errors.join("<br>");
            messageDiv.className = "message error";
            messageDiv.style.display = "block";
            messageDiv.scrollIntoView({ behavior: 'smooth' });
        }
    });

    const emailInput = document.querySelector("#email");
    emailInput.addEventListener("blur", function() {
        if (emailInput.value.trim() && !validateEmail(emailInput.value)) {
            emailInput.style.borderColor = "#e74c3c";
        } else {
            emailInput.style.borderColor = "#e0e0e0";
        }
    });

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});