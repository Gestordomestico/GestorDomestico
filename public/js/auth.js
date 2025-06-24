// public/js/auth.js
// Ubicación: C:\laragon\www\gestor_domestico_mvp\public\js\auth.js

document.addEventListener('DOMContentLoaded', () => {
    // IMPORTANTE: Esta URL debe coincidir con la base_path de tu public/index.php y config.php
    // Asegúrate de que BASE_URL esté definido correctamente en tu config.php
    const BASE_URL = '/gestor_domestico_mvp/public';

    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const authMessageDiv = document.getElementById('auth-message');

    // Función para mostrar mensajes al usuario en los formularios de autenticación
    function displayMessage(message, type) {
        if (!authMessageDiv) {
            console.warn("Elemento 'auth-message' no encontrado.");
            return;
        }

        authMessageDiv.textContent = message;
        authMessageDiv.className = ''; // Limpiar clases anteriores

        if (type === 'success') {
            authMessageDiv.style.color = 'green';
            authMessageDiv.style.backgroundColor = '#ddffdd';
            authMessageDiv.style.border = '1px solid #aaffaa';
        } else if (type === 'error') {
            authMessageDiv.style.color = 'red';
            authMessageDiv.style.backgroundColor = '#ffdddd';
            authMessageDiv.style.border = '1px solid #ffaaaa';
        } else {
            // Estilos por defecto, si los hay, o limpiar
            authMessageDiv.style.cssText = ''; // Eliminar estilos inline
        }
        authMessageDiv.style.opacity = '1'; // Asegurarse de que sea visible

        setTimeout(() => {
            authMessageDiv.style.opacity = '0'; // Desvanecer
            setTimeout(() => {
                authMessageDiv.textContent = '';
                authMessageDiv.style.cssText = ''; // Eliminar estilos inline después de desvanecer
            }, 500); // Esperar a que la transición termine
        }, 5000); // Mostrar por 5 segundos
    }

    // Comprobar si hay un mensaje de sesión expirada o secuestro al cargar la página de login
    const sessionMessage = sessionStorage.getItem('login_message');
    if (sessionMessage) {
        displayMessage(sessionMessage, 'error');
        sessionStorage.removeItem('login_message'); // Limpiar el mensaje después de mostrarlo
    }


    // Manejar el formulario de Login
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const username = loginForm.username.value.trim();
            const password = loginForm.password.value;

            if (!username || !password) {
                displayMessage('Por favor, ingresa tu usuario y contraseña.', 'error');
                return;
            }

            try {
                const response = await fetch(`${BASE_URL}/api/auth`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'login', username, password })
                });

                const data = await response.json();

                if (data.success) {
                    displayMessage(data.message, 'success');
                    // Redirigir al dashboard después de un inicio de sesión exitoso
                    window.location.href = `${BASE_URL}/dashboard`;
                } else {
                    // Mostrar errores específicos si la API los devuelve
                    const errorMessage = data.message + (data.errors ? ' ' + data.errors.join(', ') : '');
                    displayMessage(errorMessage, 'error');
                }
            } catch (error) {
                console.error('Error en la solicitud de login:', error);
                displayMessage('Error de conexión con el servidor. Inténtalo de nuevo más tarde.', 'error');
            }
        });
    }

    // Manejar el formulario de Registro
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const username = registerForm.username.value.trim();
            const password = registerForm.password.value;

            if (!username || !password) {
                displayMessage('Por favor, completa todos los campos para el registro.', 'error');
                return;
            }

            try {
                const response = await fetch(`${BASE_URL}/api/auth`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'register', username, password })
                });

                const data = await response.json();

                if (data.success) {
                    displayMessage(data.message, 'success');
                    // Limpiar el formulario y animar al usuario a iniciar sesión
                    registerForm.reset();
                    // Opcional: Redirigir automáticamente al login después de un tiempo
                    // setTimeout(() => { window.location.href = `${BASE_URL}/login`; }, 3000);
                } else {
                    const errorMessage = data.message + (data.errors ? ' ' + data.errors.join(', ') : '');
                    displayMessage(errorMessage, 'error');
                }
            } catch (error) {
                console.error('Error en la solicitud de registro:', error);
                displayMessage('Error de conexión con el servidor. Inténtalo de nuevo más tarde.', 'error');
            }
        });
    }
});