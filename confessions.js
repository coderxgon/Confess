document.addEventListener('DOMContentLoaded', function () {
    // Open confession form popup
    function openForm() {
        document.getElementById("confessionForm").style.display = "block";
    }

    // Close confession form popup
    function closeForm() {
        document.getElementById("confessionForm").style.display = "none";
    }

    // Character count validation for confession message
    const confessionTextarea = document.getElementById('confession_message');
    const charCount = document.querySelector('.char-count');

    if (confessionTextarea && charCount) {
        confessionTextarea.addEventListener('input', function () {
            const length = confessionTextarea.value.length;
            charCount.textContent = `${length}/500`;

            // Check if confession message length is between 3 and 500
            if (length < 3 || length > 500) {
                confessionTextarea.setCustomValidity('Confession must be between 3 and 500 characters');
            } else {
                confessionTextarea.setCustomValidity('');
            }
        });
    }

    // Submit confession form
    function submitConfession() {
        const confessionMessage = confessionTextarea.value;
        if (confessionMessage.length < 3 || confessionMessage.length > 500) {
            alert('Confession message must be between 3 and 500 characters');
            return;
        }

        // Send confession data to PHP backend via AJAX
        fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `confession_message=${encodeURIComponent(confessionMessage)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the page or dynamically add the new confession to the list
                location.reload();
            } else {
                alert(data.message || 'Error submitting confession. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again later.');
        });
    }

    // Assign the submitConfession function to the submit button
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.addEventListener('click', submitConfession);
    }

    // Heart reaction button handler
    const heartBtns = document.querySelectorAll('.heart-btn');

    heartBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const confessionId = btn.getAttribute('data-id');

            // Send heart reaction update to the server via POST request (AJAX)
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `heart_id=${confessionId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the heart count dynamically on the page
                    const heartCountElement = btn.querySelector('span');
                    heartCountElement.textContent = data.newHeartCount;
                } else {
                    alert(data.message || 'Error updating reaction. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again later.');
            });
        });
    });

    // Expose functions for form opening and closing
    window.openForm = openForm;
    window.closeForm = closeForm;
});
