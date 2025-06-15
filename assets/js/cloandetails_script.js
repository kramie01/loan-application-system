// Loan Details JavaScript Functions
function openUpdateModal(loan_id, amount, term, purpose) {
  const modal = document.getElementById("updateModal")
  const form = document.getElementById("updateLoanForm")

  // Populate form fields
  document.getElementById("updateLoanId").value = loan_id
  document.getElementById("updateLoanAmount").value = amount
  document.getElementById("updatePaymentTerm").value = term
  document.getElementById("updateLoanPurpose").value = purpose

  // Handle "Others" option
  toggleOtherPurposeField()

  // Show modal
  modal.style.display = "block"

  // Focus on first input
  document.getElementById("updateLoanAmount").focus()
}

function closeUpdateModal() {
  const modal = document.getElementById("updateModal")
  modal.style.display = "none"

  // Reset form
  document.getElementById("updateLoanForm").reset()
  document.getElementById("otherPurposeGroup").style.display = "none"
}

function toggleOtherPurposeField() {
  const purposeSelect = document.getElementById("updateLoanPurpose")
  const otherGroup = document.getElementById("otherPurposeGroup")
  const otherInput = document.getElementById("updateOtherPurpose")

  if (purposeSelect.value === "Others") {
    otherGroup.style.display = "block"
    otherInput.required = true
  } else {
    otherGroup.style.display = "none"
    otherInput.required = false
    otherInput.value = ""
  }
}

function cancelLoan(loan_id) {
  const modal = document.getElementById("cancelModal")
  document.getElementById("cancelLoanId").value = loan_id
  modal.style.display = "block"
}

function closeCancelModal() {
  const modal = document.getElementById("cancelModal")
  modal.style.display = "none"
}

function payLoan(loan_id) {
  const modal = document.getElementById("paymentModal")
  document.getElementById("payLoanId").value = loan_id
  modal.style.display = "block"
}

function closePaymentModal() {
  const modal = document.getElementById("paymentModal")
  modal.style.display = "none"
}

// Event Listeners
document.addEventListener("DOMContentLoaded", () => {
  // Close modals when clicking outside
  window.onclick = (event) => {
    const updateModal = document.getElementById("updateModal")
    const paymentModal = document.getElementById("paymentModal")
    const cancelModal = document.getElementById("cancelModal")

    if (event.target === updateModal) {
      closeUpdateModal()
    }

    if (event.target === paymentModal) {
      closePaymentModal()
    }

    if (event.target === cancelModal) {
      closeCancelModal()
    }
  }

  // Handle loan purpose change
  const purposeSelect = document.getElementById("updateLoanPurpose")
  if (purposeSelect) {
    purposeSelect.addEventListener("change", toggleOtherPurposeField)
  }

  // Form validation for update form
  const updateForm = document.getElementById("updateLoanForm")
  if (updateForm) {
    updateForm.addEventListener("submit", (e) => {
      const amount = document.getElementById("updateLoanAmount").value
      const purpose = document.getElementById("updateLoanPurpose").value
      const otherPurpose = document.getElementById("updateOtherPurpose").value

      // Validate other purpose if selected
      if (purpose === "Others" && otherPurpose.trim() === "") {
        alert("Please specify the loan purpose")
        e.preventDefault()
        return
      }
    })
  }

  // Form validation for cancel form
  const cancelForm = document.getElementById("cancelLoanForm")
  if (cancelForm) {
    cancelForm.addEventListener("submit", (e) => {
      const submitBtn = cancelForm.querySelector(".btn-danger")
      submitBtn.textContent = "Cancelling..."
      submitBtn.disabled = true
    })
  }
})

// Function to show success/error messages
function showMessage(message, type = "info") {
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