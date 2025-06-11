document.addEventListener("DOMContentLoaded", () => {
  const deleteConfirmation = document.getElementById("deleteConfirmation")
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn")
  const confirmationError = document.getElementById("confirmationError")
  const deleteForm = document.getElementById("deleteAccountForm")

  // Handle confirmation input
  if (deleteConfirmation) {
    deleteConfirmation.addEventListener("input", function () {
      const value = this.value.toUpperCase()

      if (value === "DELETE") {
        this.classList.remove("invalid")
        this.classList.add("valid")
        confirmDeleteBtn.disabled = false
        confirmationError.style.display = "none"
      } else {
        this.classList.remove("valid")
        if (value.length > 0) {
          this.classList.add("invalid")
        }
        confirmDeleteBtn.disabled = true
        if (value.length > 0 && value !== "DELETE") {
          confirmationError.style.display = "block"
        } else {
          confirmationError.style.display = "none"
        }
      }
    })

    // Convert to uppercase as user types
    deleteConfirmation.addEventListener("keyup", function () {
      this.value = this.value.toUpperCase()
    })
  }

  // Handle form submission
  if (deleteForm) {
    deleteForm.addEventListener("submit", function (e) {
      const confirmationValue = deleteConfirmation.value.toUpperCase()

      if (confirmationValue !== "DELETE") {
        e.preventDefault()
        confirmationError.textContent = "Please type DELETE exactly as shown to confirm."
        confirmationError.style.display = "block"
        return
      }

      // Final confirmation
      const finalConfirm = confirm(
        "This is your final warning!\n\n" +
          "Are you absolutely sure you want to delete your account?\n\n" +
          "This action CANNOT be undone and will permanently delete:\n" +
          "• All your personal information\n" +
          "• Employment details\n" +
          "• Credit card information\n" +
          "• All loan applications\n" +
          "• Your user account\n\n" +
          "Click OK to proceed with deletion or Cancel to abort.",
      )

      if (!finalConfirm) {
        e.preventDefault()
        return
      }

      // Show loading state
      confirmDeleteBtn.textContent = "Deleting Account..."
      confirmDeleteBtn.disabled = true

      // Disable all form elements
      const formElements = this.querySelectorAll("input, button")
      formElements.forEach((element) => {
        element.disabled = true
      })
    })
  }
})

// Utility function to show confirmation message
function showDeleteConfirmation(message, type = "info") {
  const messageDiv = document.createElement("div")
  messageDiv.className = `message message-${type}`
  messageDiv.textContent = message

  // Style the message
  messageDiv.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 6px;
    color: white;
    font-weight: 600;
    z-index: 1001;
    animation: slideInRight 0.3s ease;
    max-width: 400px;
  `

  // Set background color based on type
  switch (type) {
    case "success":
      messageDiv.style.backgroundColor = "#27ae60"
      break
    case "error":
      messageDiv.style.backgroundColor = "#e74c3c"
      break
    case "warning":
      messageDiv.style.backgroundColor = "#f39c12"
      break
    default:
      messageDiv.style.backgroundColor = "#3498db"
  }

  document.body.appendChild(messageDiv)

  // Remove message after 5 seconds
  setTimeout(() => {
    messageDiv.style.animation = "slideOutRight 0.3s ease"
    setTimeout(() => {
      if (messageDiv.parentNode) {
        messageDiv.parentNode.removeChild(messageDiv)
      }
    }, 300)
  }, 5000)
}

// Add CSS animations for messages
const style = document.createElement("style")
style.textContent = `
  @keyframes slideInRight {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes slideOutRight {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(100%);
      opacity: 0;
    }
  }
`
document.head.appendChild(style)
