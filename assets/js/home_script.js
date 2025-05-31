function showRoleSelection() {
  hideAll();
  document.getElementById('role-selection').classList.add('active');
}

function showLogin(role) {
  hideAll();
  document.getElementById('login').classList.add('active');

  // Set hidden role input
  document.getElementById('login-role').value = role;

  // Set login title dynamically
  const title = document.getElementById('login-title');
  title.textContent = role.charAt(0).toUpperCase() + role.slice(1) + ' Login';

  // Show or hide register link depending on role (hide if admin)
  const registerLink = document.getElementById('register-link');
  if (role === 'admin') {
    registerLink.style.display = 'none';
  } else {
    registerLink.style.display = 'block';
  }
}

function showRegister() {
  hideAll();
  document.getElementById('register').classList.add('active');
}

function hideAll() {
  document.getElementById('role-selection').classList.remove('active');
  document.getElementById('login').classList.remove('active');
  document.getElementById('register').classList.remove('active');
}

// On page load, restore state
window.onload = function () {
  const role = document.body.getAttribute('data-login-role');
  if (role) {
    showLogin(role);
  }
};