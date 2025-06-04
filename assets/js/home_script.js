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

  // Add uppercase conversion to all input fields
  const inputs = document.querySelectorAll('input[type="text"], input[type="email"]')
  inputs.forEach((input) => {
    input.addEventListener("input", function () {
      this.value = this.value.toUpperCase()
    })
  })
})
