// public/js/categories.js

document.addEventListener('DOMContentLoaded', () => {
    const API_CATEGORIES_URL = 'api/categories.php';

    const addCategoryForm = document.getElementById('addCategoryForm');
    const newCategoryNameInput = document.getElementById('newCategoryName');
    const newCategoryTypeSelect = document.getElementById('newCategoryType');
    const addCategoryMessage = document.getElementById('addCategoryMessage');
    const categoriesListDiv = document.getElementById('categoriesList');
    const noCategoriesMessage = document.getElementById('noCategoriesMessage');

    const showMessage = (element, message, isError = false) => {
        element.textContent = message;
        element.className = `alert mt-3 ${isError ? 'alert-danger' : 'alert-success'}`;
        element.style.display = 'block';
        setTimeout(() => {
            element.style.display = 'none';
        }, 5000);
    };

    // --- Cargar y Mostrar Categorías Existentes ---
    const loadAndDisplayCategories = async () => {
        try {
            const response = await fetch(API_CATEGORIES_URL);
            if (response.status === 401) { window.location.href = 'login.html'; return; }
            const categories = await response.json();

            categoriesListDiv.innerHTML = '';
            if (categories.length === 0) {
                noCategoriesMessage.style.display = 'block';
            } else {
                noCategoriesMessage.style.display = 'none';
                categories.forEach(cat => {
                    const listItem = document.createElement('div');
                    listItem.className = 'category-list-item';
                    listItem.innerHTML = `
                        <span>${cat.name} <span class="badge bg-${cat.type === 'income' ? 'success' : 'secondary'}">${cat.type === 'income' ? 'Ingreso' : 'Gasto'}</span></span>
                        <button class="btn btn-sm btn-danger delete-category-btn" data-id="${cat.id}">Eliminar</button>
                    `;
                    categoriesListDiv.appendChild(listItem);
                });

                categoriesListDiv.querySelectorAll('.delete-category-btn').forEach(button => {
                    button.addEventListener('click', handleDeleteCategory);
                });
            }
            // Disparar un evento personalizado cuando las categorías se han actualizado
            document.dispatchEvent(new Event('categoriesUpdated'));

        } catch (error) {
            console.error('Error al cargar categorías para gestión:', error);
            showMessage(addCategoryMessage, 'Error al cargar categorías.', true);
        }
    };

    // --- Manejar el Envío del Formulario para Agregar Categoría ---
    if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const name = newCategoryNameInput.value.trim();
            const type = newCategoryTypeSelect.value;

            if (!name || !type) {
                showMessage(addCategoryMessage, 'Por favor, complete todos los campos.', true);
                return;
            }
            if (name.length < 2 || name.length > 50) {
                showMessage(addCategoryMessage, 'El nombre debe tener entre 2 y 50 caracteres.', true);
                return;
            }

            try {
                const response = await fetch(API_CATEGORIES_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, type })
                });
                const result = await response.json();

                if (response.ok) {
                    showMessage(addCategoryMessage, result.message, false);
                    addCategoryForm.reset();
                    loadAndDisplayCategories();
                } else {
                    showMessage(addCategoryMessage, result.error || 'Error al agregar categoría.', true);
                }
            } catch (error) {
                console.error('Error de red al agregar categoría:', error);
                showMessage(addCategoryMessage, 'Error de conexión al servidor.', true);
            }
        });
    }

    // --- Manejar la Eliminación de Categoría ---
    const handleDeleteCategory = async (e) => {
        const categoryId = e.target.dataset.id;
        if (!confirm('¿Estás seguro de que quieres eliminar esta categoría? Si está en uso en alguna transacción, no se eliminará.')) {
            return;
        }

        try {
            const response = await fetch(`${API_CATEGORIES_URL}?id=${categoryId}`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' }
            });
            const result = await response.json();

            if (response.ok) {
                alert(result.message);
                loadAndDisplayCategories();
            } else if (response.status === 401) {
                window.location.href = 'login.html';
            } else {
                alert(`Error al eliminar categoría: ${result.error || 'Mensaje desconocido'}`);
            }
        } catch (error) {
            console.error('Error de red al eliminar categoría:', error);
            alert('Error de conexión al servidor al intentar eliminar categoría.');
        }
    };

    // Exponer la función globalmente para que app.js pueda llamarla
    window.loadAndDisplayCategories = loadAndDisplayCategories;
});