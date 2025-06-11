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
