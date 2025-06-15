// Form management functions
const FormManager = {
  containers: ["role-selection", "login", "register"],

  hideAll() {
    this.containers.forEach((id) => {
      document.getElementById(id).classList.remove("active")
    })
  },

  show(formId) {
    this.hideAll()
    document.getElementById(formId).classList.add("active")
  },
}

// Navigation functions
function showRoleSelection() {
  FormManager.show("role-selection")
}

function showLogin(role) {
  FormManager.show("login")

  // Update form for selected role
  const loginRole = document.getElementById("login-role")
  const loginTitle = document.getElementById("login-title")
  const registerLink = document.getElementById("register-link")

  loginRole.value = role
  loginTitle.textContent = `${role.charAt(0).toUpperCase() + role.slice(1)} Login`

  // Hide register link for admin
  registerLink.style.display = role === "admin" ? "none" : "block"
}

function showRegister() {
  FormManager.show("register")
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
  const role = document.body.getAttribute("data-login-role")
  if (role) {
    showLogin(role)
  }

    // Function to convert input to uppercase with exceptions
  function convertToUppercase(input) {
    // Skip certain input types that shouldn't be uppercase
    const skipTypes = ['email', 'password'];
    
    if (!skipTypes.includes(input.type.toLowerCase())) {
      input.addEventListener('input', function() {
        const cursorPosition = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(cursorPosition, cursorPosition);
      });
    }
  }

  // Convert all existing text inputs to uppercase
  const allInputs = document.querySelectorAll('input, textarea');
  allInputs.forEach(convertToUppercase);
})

function closeAccountDeletedModal() {
  const modal = document.getElementById("accountDeletedModal")
  if (modal) {
    modal.style.display = "none"

    // Remove the deleted parameter from URL
    const url = new URL(window.location)
    url.searchParams.delete("deleted")
    window.history.replaceState({}, document.title, url.pathname)
  }
}

// Close modal when clicking outside
window.onclick = (event) => {
  const modal = document.getElementById("accountDeletedModal")
  if (modal && event.target === modal) {
    closeAccountDeletedModal()
  }
}