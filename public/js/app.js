// public/js/app.js

document.addEventListener('DOMContentLoaded', () => {
    const API_BASE_URL = 'api/';

    const transactionForm = document.getElementById('transactionForm');
    const typeSelect = document.getElementById('type');
    const categorySelect = document.getElementById('category');
    const amountInput = document.getElementById('amount');
    const dateInput = document.getElementById('date');
    const descriptionInput = document.getElementById('description');
    const transactionsTableBody = document.getElementById('transactionsTableBody');
    const totalIncomeElem = document.getElementById('totalIncome');
    const totalExpenseElem = document.getElementById('totalExpense');
    const currentBalanceElem = document.getElementById('currentBalance');
    const expensesChartCanvas = document.getElementById('expensesChart');
    const incomeChartCanvas = document.getElementById('incomeChart');
    const noExpensesDataElem = document.getElementById('noExpensesData');
    const noIncomeDataElem = document.getElementById('noIncomeData');

    const dashboardSection = document.getElementById('dashboardSection');
    const categoriesSection = document.getElementById('categoriesSection');
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

    let expensesChartInstance = null;
    let incomeChartInstance = null;
    let allCategories = [];

    const formatCurrency = (amount) => `$${parseFloat(amount).toFixed(2)}`;
    const today = new Date().toISOString().split('T')[0];
    dateInput.value = today;

    const showSection = (sectionId) => {
        dashboardSection.classList.add('d-none');
        categoriesSection.classList.add('d-none');

        navLinks.forEach(link => link.classList.remove('active'));

        if (sectionId === 'dashboardSection') {
            dashboardSection.classList.remove('d-none');
            document.querySelector('a[href="#dashboardSection"]').classList.add('active');
            loadTransactionsAndSummary();
        } else if (sectionId === 'categoriesSection') {
            categoriesSection.classList.remove('d-none');
            document.querySelector('a[href="#categoriesSection"]').classList.add('active');
            if (typeof window.loadAndDisplayCategories === 'function') {
                window.loadAndDisplayCategories();
            }
        }
    };

    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const sectionId = e.target.getAttribute('href').substring(1);
            showSection(sectionId);
        });
    });

    fetch(`${API_BASE_URL}transactions.php`)
        .then(response => {
            if (response.status === 401) {
                window.location.href = 'login.html';
                return Promise.reject('No autenticado');
            }
            return response.json();
        })
        .then(data => {
            const loggedInUsername = localStorage.getItem('loggedInUsername');
            if (loggedInUsername) {
                document.getElementById('loggedInUser').textContent = loggedInUsername;
            } else {
                document.getElementById('loggedInUser').textContent = 'Usuario';
            }
            loadCategoriesForForm();
            loadTransactionsAndSummary();
            showSection('dashboardSection');
        })
        .catch(error => {
            console.error('Error de autenticación o carga inicial del dashboard:', error);
        });

    const loadCategoriesForForm = async (forceReload = false) => {
        if (allCategories.length === 0 || forceReload) {
            try {
                const response = await fetch(`${API_BASE_URL}categories.php`);
                if (response.status === 401) { window.location.href = 'login.html'; return; }
                allCategories = await response.json();
            } catch (error) {
                console.error('Error al cargar categorías para el formulario:', error);
                return;
            }
        }
        filterCategoriesForForm();
    };

    const filterCategoriesForForm = () => {
        const selectedType = typeSelect.value;
        categorySelect.innerHTML = '<option value="">Seleccione una categoría</option>';
        allCategories
            .filter(cat => cat.type === selectedType)
            .forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.name;
                option.textContent = cat.name;
                categorySelect.appendChild(option);
            });
        if (categorySelect.options.length > 1) {
            categorySelect.value = categorySelect.options[1].value;
        } else {
            categorySelect.value = '';
        }
    };

    typeSelect.addEventListener('change', filterCategoriesForForm);

    document.addEventListener('categoriesUpdated', () => {
        loadCategoriesForForm(true);
    });

    const loadTransactionsAndSummary = async () => {
        try {
            const transactionsResponse = await fetch(`${API_BASE_URL}transactions.php`);
            if (transactionsResponse.status === 401) { window.location.href = 'login.html'; return; }
            const transactions = await transactionsResponse.json();

            const reportsResponse = await fetch(`${API_BASE_URL}reports.php`);
            if (reportsResponse.status === 401) { window.location.href = 'login.html'; return; }
            const reportsData = await reportsResponse.json();

            transactionsTableBody.innerHTML = '';
            if (transactions.length === 0) {
                transactionsTableBody.innerHTML = '<tr><td colspan="5" class="text-center">No hay transacciones aún.</td></tr>';
            } else {
                transactions.forEach(t => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${t.date}</td>
                        <td><span class="badge bg-${t.type === 'income' ? 'success' : 'danger'}">${t.type === 'income' ? 'Ingreso' : 'Gasto'}</span></td>
                        <td>${t.category}</td>
                        <td class="${t.type === 'income' ? 'income-text' : 'expense-text'}">${formatCurrency(t.amount)}</td>
                        <td>${t.description || '-'}</td>
                    `;
                    transactionsTableBody.appendChild(row);
                });
            }

            totalIncomeElem.textContent = formatCurrency(reportsData.summary.total_income);
            totalExpenseElem.textContent = formatCurrency(reportsData.summary.total_expense);
            currentBalanceElem.textContent = formatCurrency(reportsData.summary.balance);
            currentBalanceElem.className = 'card-text balance-amount';
            if (reportsData.summary.balance >= 0) {
                currentBalanceElem.classList.add('balance-text-positive');
            } else {
                currentBalanceElem.classList.add('balance-text-negative');
            }

            renderCharts(reportsData.expense_by_category, reportsData.income_by_category);

        } catch (error) {
            console.error('Error al cargar datos del dashboard:', error);
        }
    };

    transactionForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!typeSelect.value || !categorySelect.value || !amountInput.value || !dateInput.value) {
            alert('Por favor, complete todos los campos obligatorios.');
            return;
        }
        if (parseFloat(amountInput.value) <= 0) {
            alert('El monto debe ser un número positivo.');
            return;
        }

        const newTransaction = {
            type: typeSelect.value,
            category: categorySelect.value,
            amount: parseFloat(amountInput.value),
            date: dateInput.value,
            description: descriptionInput.value
        };

        try {
            const response = await fetch(`${API_BASE_URL}transactions.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(newTransaction)
            });

            const result = await response.json();

            if (response.ok) {
                alert('Transacción guardada con éxito.');
                transactionForm.reset();
                dateInput.value = today;
                typeSelect.dispatchEvent(new Event('change'));
                loadTransactionsAndSummary();
            } else if (response.status === 401) {
                window.location.href = 'login.html';
            } else {
                alert(`Error al guardar transacción: ${result.error || 'Mensaje desconocido'}`);
            }
        } catch (error) {
            console.error('Error al enviar transacción:', error);
            alert('No se pudo guardar la transacción. Verifique la conexión.');
        }
    });

    const renderCharts = (expenseData, incomeData) => {
        if (expensesChartInstance) expensesChartInstance.destroy();
        if (incomeChartInstance) incomeChartInstance.destroy();

        if (expenseData && expenseData.length > 0) {
            noExpensesDataElem.style.display = 'none';
            expensesChartCanvas.style.display = 'block';
            expensesChartInstance = new Chart(expensesChartCanvas, {
                type: 'pie', data: {
                    labels: expenseData.map(d => d.category),
                    datasets: [{ data: expenseData.map(d => d.total), backgroundColor: ['#dc3545', '#ffc107', '#17a2b8', '#fd7e14', '#6f42c1', '#20c997', '#e83e8c', '#6c757d'], hoverOffset: 4 }]
                }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top', }, title: { display: true, text: 'Distribución de Gastos' } } }
            });
        } else { noExpensesDataElem.style.display = 'block'; expensesChartCanvas.style.display = 'none'; }

        if (incomeData && incomeData.length > 0) {
            noIncomeDataElem.style.display = 'none';
            incomeChartCanvas.style.display = 'block';
            incomeChartInstance = new Chart(incomeChartCanvas, {
                type: 'doughnut', data: {
                    labels: incomeData.map(d => d.category),
                    datasets: [{ data: incomeData.map(d => d.total), backgroundColor: ['#28a745', '#007bff', '#6610f2', '#ffc107', '#17a2b8'], hoverOffset: 4 }]
                }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top', }, title: { display: true, text: 'Distribución de Ingresos' } } }
            });
        } else { noIncomeDataElem.style.display = 'block'; incomeChartCanvas.style.display = 'none'; }
    };
});