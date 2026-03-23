document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('churchForm');
    const feedback = document.getElementById('message');

    if (!form || !feedback) {
        return;
    }

    form.addEventListener('submit', function (event) {
        const messageInput = document.getElementById('messageInput');
        const selectedType = document.querySelector('input[name="member_visitor"]:checked');
        const errors = [];

        feedback.hidden = true;
        feedback.textContent = '';

        if (!selectedType) {
            errors.push('Please select whether you are a member, visitor, or other.');
        }

        if (!messageInput.value.trim()) {
            errors.push('Please enter your message before sending.');
        }

        if (errors.length > 0) {
            event.preventDefault();
            feedback.innerHTML = errors.join('<br>');
            feedback.hidden = false;
            feedback.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
