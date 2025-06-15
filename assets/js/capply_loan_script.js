document.addEventListener("DOMContentLoaded", () => {
  // For Loan Purpose
  const loanPurpose = document.getElementById("loanPurpose")
  const otherPurposeContainer = document.getElementById("otherPurposeContainer")
  const otherLoanPurpose = document.getElementById("otherLoanPurpose")

  loanPurpose.addEventListener("change", function () {
    if (this.value === "Others") {
      otherPurposeContainer.style.display = "block"
      otherLoanPurpose.required = true
    } else {
      otherPurposeContainer.style.display = "none"
      otherLoanPurpose.value = ""
      otherLoanPurpose.required = false
    }
  })

  // Check if modals should be shown
  const existingLoanModal = document.getElementById("existingLoanModal")
  const noProfileModal = document.getElementById("noProfileModal")
  const loanForm = document.getElementById("loanForm")

  // Disable form if modals are shown
  if (existingLoanModal && existingLoanModal.style.display === "block") {
    loanForm.classList.add("form-disabled")
  }

  if (noProfileModal && noProfileModal.style.display === "block") {
    loanForm.classList.add("form-disabled")
  }

  // Check for success parameter in URL
  const urlParams = new URLSearchParams(window.location.search)
  if (urlParams.get("success") === "true") {
    showSuccessModal()
  }
})

// Modal navigation functions
function goToLoanDetails() {
  window.location.href = "../pages/cloandetails_page.php"
}

function goToProfile() {
  window.location.href = "../pages/cprofile_page.php"
}

function goToDashboard() {
  window.location.href = "../pages/cdashboard_page.php"
}

function showSuccessModal() {
  const modal = document.getElementById("successModal")
  if (modal) {
    modal.style.display = "block"

    // Disable form
    const loanForm = document.getElementById("loanForm")
    if (loanForm) {
      loanForm.classList.add("form-disabled")
    }
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId)
  if (modal) {
    modal.style.display = "none"

    // Re-enable form if no other modals are shown
    const loanForm = document.getElementById("loanForm")
    const existingLoanModal = document.getElementById("existingLoanModal")
    const noProfileModal = document.getElementById("noProfileModal")

    if (
      loanForm &&
      (!existingLoanModal || existingLoanModal.style.display === "none") &&
      (!noProfileModal || noProfileModal.style.display === "none")
    ) {
      loanForm.classList.remove("form-disabled")
    }
  }
}

// Close modals when clicking outside
window.onclick = (event) => {
  const modals = ["existingLoanModal", "noProfileModal", "successModal"]

  modals.forEach((modalId) => {
    const modal = document.getElementById(modalId)
    if (modal && event.target === modal) {
      closeModal(modalId)
    }
  })
}