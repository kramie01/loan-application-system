// Profile View JavaScript Functions

// Open delete account modal
function openDeleteAccountModal() {
  const modal = document.getElementById("deleteAccountModal")
  modal.style.display = "block"

  // Reset form
  document.getElementById("deleteConfirmation").value = ""
  document.getElementById("confirmDeleteBtn").disabled = true
  document.getElementById("confirmationError").style.display = "none"

  // Focus on confirmation input
  setTimeout(() => {
    document.getElementById("deleteConfirmation").focus()
  }, 300)
}

// Close delete account modal
function closeDeleteAccountModal() {
  const modal = document.getElementById("deleteAccountModal")
  modal.style.display = "none"

  // Reset form
  document.getElementById("deleteConfirmation").value = ""
  document.getElementById("confirmDeleteBtn").disabled = true
  document.getElementById("confirmationError").style.display = "none"
}

// Close modal when clicking outside
window.onclick = (event) => {
  const modal = document.getElementById("deleteAccountModal")
  if (event.target === modal) {
    closeDeleteAccountModal()
  }
}

// Handle escape key
document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    closeDeleteAccountModal()
  }
})