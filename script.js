// script.js - Client-side validation
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("contactForm");
    const messageDiv = document.getElementById("message");
    
    form.addEventListener("submit", function(e) {
        // Clear previous messages
        messageDiv.style.display = "none";
        messageDiv.className = "message";
        
        // Get form values
        const name = document.querySelector("input[name='full_name']").value.trim();
        const email = document.querySelector("input[name='email']").value.trim();
        const message = document.querySelector("textarea[name='message']").value.trim();
        
        // Validation checks
        let errors = [];
        
        if (name.length < 2) {
            errors.push("Name must be at least 2 characters");
        }
        
        if (!validateEmail(email)) {
            errors.push("Please enter a valid email address");
        }
        
        if (message.length < 10) {
            errors.push("Message must be at least 10 characters");
        }
        
        // If there are errors, prevent form submission
        if (errors.length > 0) {
            e.preventDefault();
            messageDiv.innerHTML = errors.join("<br>");
            messageDiv.className = "message error";
            messageDiv.style.display = "block";
            
            // Scroll to message
            messageDiv.scrollIntoView({ behavior: 'smooth' });
        }
    });
    
    // Real-time email validation
    const emailInput = document.querySelector("input[name='email']");
    emailInput.addEventListener("blur", function() {
        if (emailInput.value.trim() && !validateEmail(emailInput.value)) {
            emailInput.style.borderColor = "#e74c3c";
        } else {
            emailInput.style.borderColor = "#e0e0e0";
        }
    });
    
    // Email validation function
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});