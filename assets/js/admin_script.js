// Approve loan function
function approveLoan(loanId, applicantName) {
  const modal = document.getElementById("approveModal")
  const details = document.getElementById("approveDetails")

  document.getElementById("approveLoanId").value = loanId

  details.innerHTML = `
    <p><strong>Applicant:</strong> ${applicantName}</p>
    <p><strong>Loan ID:</strong> L${String(loanId).padStart(3, "0")}</p>
  `

  modal.style.display = "block"
}

// Close approve modal
function closeApproveModal() {
  document.getElementById("approveModal").style.display = "none"
}

// Handle form submission
document.addEventListener("DOMContentLoaded", () => {
  const approveForm = document.getElementById("approveLoanForm")
  if (approveForm) {
    approveForm.addEventListener("submit", function (e) {
      // Show loading state
      const submitBtn = this.querySelector(".btn-approve")
      submitBtn.textContent = "Approving..."
      submitBtn.disabled = true
    })
  }
})

// Close modal when clicking outside
window.onclick = (event) => {
  const modal = document.getElementById("approveModal")
  if (event.target === modal) {
    closeApproveModal()
  }
}

// Function to view applicant profile in modal
function viewProfile(applicantId, loanId) {
    const modal = document.getElementById('profileModal');
    const profileContent = document.getElementById('profileContent');
    
    // Show modal
    modal.style.display = 'block';
    
    // Fetch profile data using applicantID
    fetch(`../auth/get_profile.php?applicantId=${applicantId}&loanId=${loanId}`)
        .then(response => response.text())
        .then(data => {
            profileContent.innerHTML = data;
        })
        .catch(error => {
            profileContent.innerHTML = '<div class="error">Error loading profile data.</div>';
            console.error('Error:', error);
        });
}

// Function to close profile modal
function closeProfileModal() {
    document.getElementById('profileModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const approveModal = document.getElementById('approveModal');
    const profileModal = document.getElementById('profileModal');
    
    if (event.target == approveModal) {
        approveModal.style.display = 'none';
    }
    if (event.target == profileModal) {
        profileModal.style.display = 'none';
    }
}