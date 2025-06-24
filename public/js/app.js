// public/js/app.js
// Ubicación: C:\laragon\www\gestor_domestico_mvp\public\js\app.js

document.addEventListener('DOMContentLoaded', () => {
    // IMPORTANTE: Esta URL debe coincidir con la base_path de tu public/index.php y config.php
    const BASE_URL = '/gestor_domestico_mvp/public';

    // Elementos del DOM para mensajes y listados
    const messageDiv = document.getElementById('dashboard-message');
    const transactionsList = document.getElementById('transactions-list');
    const addTransactionForm = document.getElementById('add-transaction-form');
    const logoutBtn = document.getElementById('logout-btn');

    // Elementos para resumen
    const totalIncomeSpan = document.getElementById('total-income');
    const totalExpenseSpan = document.getElementById('total-expense');
    const balanceSpan = document.getElementById('balance');
    const reportStartDateInput = document.getElementById('report-start-date');
    const reportEndDateInput = document.getElementById('report-end-date');
    const applyDateFilterBtn = document.getElementById('apply-date-filter');

    // Elementos para categorías y autocompletado
    const transactionCategoryInput = document.getElementById('transaction-category');
    const transactionTypeSelect = document.getElementById('transaction-type');
    const addCategoryForm = document.getElementById('add-category-form');
    const newCategoryNameInput = document.getElementById('new-category-name');
    const newCategoryTypeSelect = document.getElementById('new-category-type');
    const currentCategoriesList = document.getElementById('current-categories-list');

    // --- Función para manejar las respuestas de la API y posibles errores de autenticación ---
    async function handleApiResponse(response) {
        if (response.status === 401) { // Error de No Autorizado (ej. sesión expirada)
            const errorData = await response.json();
            // Usamos sessionStorage para pasar mensajes a la página de login
            sessionStorage.setItem('login_message', errorData.message || 'Tu sesión ha expirado o no estás autorizado. Por favor, inicia sesión de nuevo.');
            window.location.href = `${BASE_URL}/login`; // Redirigir a la URL de login enmascarada
            // Lanzar un error para detener la ejecución de las funciones que llamaron a handleApiResponse
            return Promise.reject('Unauthorized');
        }
        if (!response.ok) { // Otros errores HTTP (400, 500, etc.)
            const errorData = await response.json();
            // Lanzar un error con el mensaje de la API o un mensaje genérico
            throw new Error(errorData.message || 'Error en la solicitud API.');
        }
        return response.json(); // Devolver los datos JSON si la respuesta fue exitosa
    }

    // --- Funciones de Utilidad ---

    // Función para mostrar mensajes al usuario en el dashboard
    function displayMessage(message, type) { // 'success' o 'error'
        if (!messageDiv) {
            console.warn("Elemento 'dashboard-message' no encontrado.");
            return;
        }
        messageDiv.textContent = message;
        messageDiv.className = ''; // Limpiar clases anteriores

        if (type === 'success') {
            messageDiv.style.color = 'green';
            messageDiv.style.backgroundColor = '#ddffdd';
            messageDiv.style.border = '1px solid #aaffaa';
        } else if (type === 'error') {
            messageDiv.style.color = 'red';
            messageDiv.style.backgroundColor = '#ffdddd';
            messageDiv.style.border = '1px solid #ffaaaa';
        } else {
            messageDiv.style.cssText = ''; // Limpiar estilos si no hay tipo específico
        }
        messageDiv.style.opacity = '1'; // Asegurarse de que sea visible

        setTimeout(() => {
            messageDiv.style.opacity = '0'; // Desvanecer
            setTimeout(() => {
                messageDiv.textContent = '';
                messageDiv.style.cssText = ''; // Eliminar estilos inline después de desvanecer
            }, 500); // Esperar a que la transición termine
        }, 5000); // Mostrar por 5 segundos
    }

    // Función para escapar HTML para prevenir XSS al mostrar datos de usuario
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // --- Funciones de Transacciones ---

    // Obtener y mostrar transacciones
    async function fetchTransactions(startDate = '', endDate = '') {
        try {
            let url = `${BASE_URL}/api/transactions`;
            const params = new URLSearchParams();
            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            if (params.toString()) url += `?${params.toString()}`;

            const response = await fetch(url, {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            });
            const data = await handleApiResponse(response); // Esto manejará la redirección 401 si ocurre

            if (data.success) {
                renderTransactions(data.transactions);
                fetchSummary(startDate, endDate); // Actualizar resumen después de cargar transacciones
                fetchCategorySummary(startDate, endDate); // Actualizar resumen por categoría
            } else {
                displayMessage(data.message, 'error');
            }
        } catch (error) {
            if (error !== 'Unauthorized') { // Si el error no es por no autorizado (ya manejado)
                displayMessage(`Error al cargar transacciones: ${error.message}`, 'error');
                console.error('Error fetching transactions:', error);
            }
        }
    }

    // Renderizar transacciones en la lista
    function renderTransactions(transactions) {
        if (!transactionsList) return;

        transactionsList.innerHTML = ''; // Limpiar lista
        if (transactions.length === 0) {
            transactionsList.innerHTML = '<p class="info-message">No hay transacciones para mostrar en este rango de fechas.</p>';
            return;
        }

        transactions.forEach(t => {
            const li = document.createElement('li');
            li.className = `transaction-item ${escapeHtml(t.type)}`;
            li.dataset.id = escapeHtml(t.id); // Usar dataset para almacenar IDs

            li.innerHTML = `
                <div class="transaction-info">
                    <span class="transaction-date">${escapeHtml(t.date)}</span>
                    <span class="transaction-category">${escapeHtml(t.category)}</span>
                    <span class="transaction-description">${escapeHtml(t.description || 'Sin descripción')}</span>
                </div>
                <div class="transaction-amount">
                    ${t.type === 'income' ? '+' : '-'} $${parseFloat(t.amount).toFixed(2)}
                </div>
                <div class="transaction-actions">
                    <button class="delete-btn" data-id="${escapeHtml(t.id)}">Eliminar</button>
                </div>
            `;
            transactionsList.appendChild(li);
        });

        // Añadir listeners para los botones de eliminar después de renderizar
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                if (confirm('¿Estás seguro de que quieres eliminar esta transacción? Esta acción es irreversible.')) {
                    deleteTransaction(id);
                }
            });
        });
    }

    // Añadir una transacción
    async function addTransaction(transactionData) {
        try {
            const response = await fetch(`${BASE_URL}/api/transactions`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(transactionData)
            });
            const data = await handleApiResponse(response);

            if (data.success) {
                displayMessage(data.message, 'success');
                fetchTransactions(reportStartDateInput.value, reportEndDateInput.value); // Recargar con filtros actuales
                addTransactionForm.reset(); // Limpiar formulario
                // Restablecer la fecha actual para la siguiente transacción después del reset
                if (addTransactionForm && addTransactionForm['transaction-date']) {
                    addTransactionForm['transaction-date'].valueAsDate = new Date();
                }
            } else {
                displayMessage(`Error al añadir transacción: ${data.message}` + (data.errors ? ' ' + data.errors.join(', ') : ''), 'error');
            }
        } catch (error) {
            if (error !== 'Unauthorized') {
                displayMessage(`Error de conexión o API al añadir transacción: ${error.message}`, 'error');
                console.error('Error adding transaction:', error);
            }
        }
    }

    // Eliminar una transacción
    async function deleteTransaction(id) {
        try {
            const response = await fetch(`${BASE_URL}/api/transactions?id=${id}`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' }
            });
            const data = await handleApiResponse(response);

            if (data.success) {
                displayMessage(data.message, 'success');
                fetchTransactions(reportStartDateInput.value, reportEndDateInput.value); // Recargar con filtros actuales
            } else {
                displayMessage(`Error al eliminar transacción: ${data.message}`, 'error');
            }
        } catch (error) {
            if (error !== 'Unauthorized') {
                displayMessage(`Error de conexión o API al eliminar transacción: ${error.message}`, 'error');
                console.error('Error deleting transaction:', error);
            }
        }
    }

    // --- Funciones para Reportes / Resumen ---

    async function fetchSummary(startDate = '', endDate = '') {
        try {
            let url = `${BASE_URL}/api/reports?action=summary`;
            const params = new URLSearchParams();
            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            if (params.toString()) url += `&${params.toString()}`; // Usar '&' porque 'action' ya es un parámetro

            const response = await fetch(url, {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            });
            const data = await handleApiResponse(response);

            if (data.success) {
                if (totalIncomeSpan) totalIncomeSpan.textContent = parseFloat(data.summary.total_income).toFixed(2);
                if (totalExpenseSpan) totalExpenseSpan.textContent = parseFloat(data.summary.total_expense).toFixed(2);
                if (balanceSpan) {
                    const balance = parseFloat(data.summary.balance).toFixed(2);
                    balanceSpan.textContent = balance;
                    // Cambiar color del balance basado en si es positivo o negativo
                    balanceSpan.style.color = balance >= 0 ? '#34A853' : '#EA4335';
                }
            } else {
                displayMessage(`Error al cargar resumen: ${data.message}`, 'error');
            }
        } catch (error) {
            if (error !== 'Unauthorized') {
                displayMessage(`Error de conexión o API al cargar resumen: ${error.message}`, 'error');
                console.error('Error fetching summary:', error);
            }
        }
    }

    async function fetchCategorySummary(startDate = '', endDate = '') {
        try {
            const params = new URLSearchParams();
            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            const queryString = params.toString();

            // Fetch income categories summary
            let incomeUrl = `${BASE_URL}/api/reports?action=category_summary&type=income`;
            if (queryString) incomeUrl += `&${queryString}`;

            const incomeResponse = await fetch(incomeUrl);
            const incomeData = await handleApiResponse(incomeResponse);

            // Fetch expense categories summary
            let expenseUrl = `${BASE_URL}/api/reports?action=category_summary&type=expense`;
            if (queryString) expenseUrl += `&${queryString}`;

            const expenseResponse = await fetch(expenseUrl);
            const expenseData = await handleApiResponse(expenseResponse);

            const expensesByCategoryList = document.getElementById('expenses-by-category-list');
            const incomeByCategoryList = document.getElementById('income-by-category-list');

            if (expensesByCategoryList) {
                expensesByCategoryList.innerHTML = '';
                if (expenseData.success && expenseData.category_summary && expenseData.category_summary.length > 0) {
                    expenseData.category_summary.forEach(item => {
                        const li = document.createElement('li');
                        li.textContent = `${escapeHtml(item.category)}: $${parseFloat(item.total_amount).toFixed(2)}`;
                        expensesByCategoryList.appendChild(li);
                    });
                } else {
                    expensesByCategoryList.innerHTML = '<li class="info-message">No hay gastos por categoría en este rango.</li>';
                }
            }

            if (incomeByCategoryList) {
                incomeByCategoryList.innerHTML = '';
                if (incomeData.success && incomeData.category_summary && incomeData.category_summary.length > 0) {
                    incomeData.category_summary.forEach(item => {
                        const li = document.createElement('li');
                        li.textContent = `${escapeHtml(item.category)}: $${parseFloat(item.total_amount).toFixed(2)}`;
                        incomeByCategoryList.appendChild(li);
                    });
                } else {
                    incomeByCategoryList.innerHTML = '<li class="info-message">No hay ingresos por categoría en este rango.</li>';
                }
            }

        } catch (error) {
            if (error !== 'Unauthorized') {
                displayMessage(`Error al cargar resumen por categoría: ${error.message}`, 'error');
                console.error('Error fetching category summary:', error);
            }
        }
    }

    // --- Funciones para Categorías ---

    async function fetchCategories() {
        try {
            const response = await fetch(`${BASE_URL}/api/categories`, {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            });
            const data = await handleApiResponse(response);

            if (data.success) {
                populateCategoryDatalist(data.categories);
                renderCurrentCategories(data.categories); // Renderiza la lista para gestionar
            } else {
                displayMessage(`Error al cargar categorías: ${data.message}`, 'error');
            }
        } catch (error) {
            if (error !== 'Unauthorized') {
                displayMessage(`Error de conexión o API al cargar categorías: ${error.message}`, 'error');
                console.error('Error fetching categories:', error);
            }
        }
    }

    // Rellena el datalist para el input de categoría de transacciones
    function populateCategoryDatalist(categories) {
        if (!transactionCategoryInput || !transactionTypeSelect) return;

        let datalist = document.getElementById('categories-datalist');
        if (!datalist) {
            datalist = document.createElement('datalist');
            datalist.id = 'categories-datalist';
            transactionCategoryInput.setAttribute('list', 'categories-datalist');
            document.body.appendChild(datalist);
        }
        datalist.innerHTML = ''; // Limpiar opciones anteriores

        const selectedType = transactionTypeSelect.value;
        const filteredCategories = categories.filter(cat => cat.type === selectedType);

        filteredCategories.forEach(cat => {
            const option = document.createElement('option');
            option.value = escapeHtml(cat.name); // Escapar el nombre de la categoría
            datalist.appendChild(option);
        });
    }

    // Renderiza la lista de categorías existentes para gestión
    function renderCurrentCategories(categories) {
        if (!currentCategoriesList) return;
        currentCategoriesList.innerHTML = '';
        if (categories.length === 0) {
            currentCategoriesList.innerHTML = '<li class="info-message">No hay categorías definidas.</li>';
            return;
        }

        categories.forEach(cat => {
            const li = document.createElement('li');
            li.dataset.id = escapeHtml(cat.id);
            li.innerHTML = `
                <span>${escapeHtml(cat.name)} (${cat.type === 'income' ? 'Ingreso' : 'Gasto'})</span>
                <button class="delete-category-btn" data-id="${escapeHtml(cat.id)}">Eliminar</button>
            `;
            currentCategoriesList.appendChild(li);
        });

        // Añadir listeners para los botones de eliminar categoría
        document.querySelectorAll('.delete-category-btn').forEach(button => {
            button.addEventListener('click', async (e) => {
                const id = e.target.dataset.id;
                if (confirm('¿Estás seguro de que quieres eliminar esta categoría? Las transacciones asociadas también serán eliminadas.')) {
                    await deleteCategory(id);
                }
            });
        });
    }

    // Añadir una nueva categoría
    async function addCategory(name, type) {
        try {
            const response = await fetch(`${BASE_URL}/api/categories`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, type })
            });
            const data = await handleApiResponse(response);

            if (data.success) {
                displayMessage(data.message, 'success');
                newCategoryNameInput.value = ''; // Limpiar el campo
                fetchCategories(); // Recargar la lista de categorías y el datalist
            } else {
                displayMessage(`Error al añadir categoría: ${data.message}` + (data.errors ? ' ' + data.errors.join(', ') : ''), 'error');
            }
        } catch (error) {
            if (error !== 'Unauthorized') {
                displayMessage(`Error de conexión o API al añadir categoría: ${error.message}`, 'error');
                console.error('Error adding category:', error);
            }
        }
    }

    // Eliminar una categoría existente
    async function deleteCategory(id) {
        try {
            const response = await fetch(`${BASE_URL}/api/categories?id=${id}`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' }
            });
            const data = await handleApiResponse(response);

            if (data.success) {
                displayMessage(data.message, 'success');
                fetchCategories(); // Recargar la lista de categorías y el datalist
                fetchTransactions(reportStartDateInput.value, reportEndDateInput.value); // Recargar transacciones por si alguna fue eliminada
            } else {
                displayMessage(`Error al eliminar categoría: ${data.message}`, 'error');
            }
        } catch (error) {
            if (error !== 'Unauthorized') {
                displayMessage(`Error de conexión o API al eliminar categoría: ${error.message}`, 'error');
                console.error('Error deleting category:', error);
            }
        }
    }

    // --- Inicialización y Event Listeners ---

    // Inicializar al cargar la página (solo si es el dashboard)
    const isDashboardPage = window.location.pathname.includes('/dashboard') || window.location.pathname === `${BASE_URL}/` || window.location.pathname === `${BASE_URL}/index.php`;

    if (isDashboardPage) {
        fetchTransactions(); // Cargar transacciones iniciales y resumen
        fetchCategories(); // Cargar categorías para el formulario y gestión

        // Establecer la fecha actual por defecto al cargar el formulario de transacción
        if (addTransactionForm && addTransactionForm['transaction-date']) {
            addTransactionForm['transaction-date'].valueAsDate = new Date();
        }
    }

    // Listener para el formulario de añadir transacción
    if (addTransactionForm) {
        addTransactionForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const type = addTransactionForm['transaction-type'].value;
            const category = addTransactionForm['transaction-category'].value.trim(); // Trim para limpiar espacios
            const amount = addTransactionForm['transaction-amount'].value; // Deja como string para que el backend valide float
            const description = addTransactionForm['transaction-description'].value.trim();
            const date = addTransactionForm['transaction-date'].value;

            // Simple validación de frontend para una UX más rápida
            if (!type || !category || !amount || parseFloat(amount) <= 0 || !date) {
                displayMessage('Por favor, completa todos los campos requeridos para la transacción y asegúrate de que el monto sea un número positivo.', 'error');
                return;
            }

            const transactionData = { type, category, amount, description, date };
            await addTransaction(transactionData);
        });
    }

    // Listener para el botón de cierre de sesión
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
            if (!confirm('¿Estás seguro de que quieres cerrar tu sesión?')) {
                return;
            }
            try {
                const response = await fetch(`${BASE_URL}/api/auth`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'logout' })
                });
                const data = await handleApiResponse(response);

                if (data.success) {
                    // Si el logout fue exitoso, redirigir al login
                    window.location.href = `${BASE_URL}/login`;
                } else {
                    displayMessage(`Error al cerrar sesión: ${data.message}`, 'error');
                }
            } catch (error) {
                if (error !== 'Unauthorized') {
                    displayMessage(`Error de conexión al cerrar sesión: ${error.message}`, 'error');
                    console.error('Error logging out:', error);
                }
                // Si es un error de "Unauthorized", handleApiResponse ya redirigió
            }
        });
    }

    // Listener para el botón de aplicar filtro de fechas
    if (applyDateFilterBtn) {
        applyDateFilterBtn.addEventListener('click', () => {
            const startDate = reportStartDateInput.value;
            const endDate = reportEndDateInput.value;
            fetchTransactions(startDate, endDate); // Esto también refresca el summary y category summary
        });
    }

    // Event listener para cambiar las categorías del datalist cuando cambia el tipo de transacción
    if (transactionTypeSelect) {
        transactionTypeSelect.addEventListener('change', fetchCategories); // Vuelve a cargar y filtrar datalist
    }

    // Listener para el formulario de añadir categoría
    if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = newCategoryNameInput.value.trim();
            const type = newCategoryTypeSelect.value;

            if (!name || !type) {
                displayMessage('El nombre y el tipo de categoría son obligatorios.', 'error');
                return;
            }
            if (name.length < 2 || name.length > 50) {
                 displayMessage('El nombre de la categoría debe tener entre 2 y 50 caracteres.', 'error');
                 return;
            }

            await addCategory(name, type);
        });
    }
});