// public/js/auth.js

document.addEventListener('DOMContentLoaded', () => {
    const API_AUTH_URL = 'api/auth.php';

    const showMessage = (element, message, isError = false) => {
        element.textContent = message;
        element.className = `alert mt-3 ${isError ? 'alert-danger' : 'alert-success'}`;
        element.style.display = 'block';
        setTimeout(() => {
            element.style.display = 'none';
        }, 5000);
    };

    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        const registerMessage = document.getElementById('registerMessage');
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (password !== confirmPassword) {
                showMessage(registerMessage, 'Las contraseñas no coinciden.', true);
                return;
            }
            if (password.length < 6) {
                showMessage(registerMessage, 'La contraseña debe tener al menos 6 caracteres.', true);
                return;
            }
            if (username.length < 3) {
                showMessage(registerMessage, 'El nombre de usuario debe tener al menos 3 caracteres.', true);
                return;
            }

            try {
                const response = await fetch(`${API_AUTH_URL}?action=register`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });
                const result = await response.json();

                if (response.ok) {
                    showMessage(registerMessage, result.message, false);
                    registerForm.reset();
                    setTimeout(() => window.location.href = 'login.html', 2000);
                } else {
                    showMessage(registerMessage, result.error || 'Error en el registro.', true);
                }
            } catch (error) {
                console.error('Error de red al registrar:', error);
                showMessage(registerMessage, 'Error de conexión al servidor.', true);
            }
        });
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        const loginMessage = document.getElementById('loginMessage');
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            try {
                const response = await fetch(`${API_AUTH_URL}?action=login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });
                const result = await response.json();

                if (response.ok) {
                    showMessage(loginMessage, result.message, false);
                    localStorage.setItem('loggedInUsername', result.username);
                    setTimeout(() => window.location.href = 'index.html', 1000);
                } else {
                    showMessage(loginMessage, result.error || 'Credenciales inválidas.', true);
                }
            } catch (error) {
                console.error('Error de red al iniciar sesión:', error);
                showMessage(loginMessage, 'Error de conexión al servidor.', true);
            }
        });
    }

    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) {
        logoutButton.addEventListener('click', async () => {
            const confirmLogout = confirm('¿Estás seguro de que quieres cerrar sesión?');
            if (!confirmLogout) return;

            try {
                const response = await fetch(`${API_AUTH_URL}?action=logout`, { method: 'GET' });
                const result = await response.json();
                if (response.ok) {
                    alert(result.message);
                    localStorage.removeItem('loggedInUsername');
                    window.location.href = 'login.html';
                } else {
                    alert(`Error al cerrar sesión: ${result.error || 'Mensaje desconocido'}`);
                }
            } catch (error) {
                console.error('Error de red al cerrar sesión:', error);
                alert('Error de conexión al servidor.');
            }
        });
    }
});