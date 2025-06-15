let formToSubmit = null;

// Override the button's onclick behavior
document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.querySelector('.form-submit-btn');
    
    if (submitBtn) {
        // Store reference to the form
        formToSubmit = submitBtn.closest('form');
        
        // Remove the existing onclick attribute
        submitBtn.removeAttribute('onclick');
        
        // Add new click event listener that shows modal first
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default form submission
            showUpdateConfirmation();
        });
    }
});

// Function to show the update confirmation modal
function showUpdateConfirmation() {
    const modal = document.getElementById('updateConfirmationModal');
    if (modal) {
        modal.style.display = 'block';
    }
}

// Function to close the modal
function closeUpdateModal() {
    const modal = document.getElementById('updateConfirmationModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Function to confirm the update and submit form
function confirmUpdate() {
    // Close the modal first
    closeUpdateModal();
    
    // Submit the form to save to database
    if (formToSubmit) {
        // Remove the onclick temporarily to avoid conflicts
        const submitBtn = formToSubmit.querySelector('.form-submit-btn');
        if (submitBtn) {
            submitBtn.onclick = null;
        }
        
        // Submit the form
        formToSubmit.submit();
    } else {
        // Fallback: redirect if no form found
        window.location.href = 'cprofile_page.php';
    }
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('updateConfirmationModal');
    if (event.target === modal) {
        closeUpdateModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeUpdateModal();
    }
});