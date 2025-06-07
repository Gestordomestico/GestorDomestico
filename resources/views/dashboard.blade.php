<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Financiero') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Resumen Financiero --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 text-center">
                        <div class="bg-green-50 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-green-700 mb-2">Ingresos Totales</h3>
                            <p id="total-income-display" class="text-3xl font-bold text-green-600">${{ number_format($totalIncome, 2, ',', '.') }}</p>
                        </div>
                        <div class="bg-red-50 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-red-700 mb-2">Gastos Totales</h3>
                            <p id="total-expense-display" class="text-3xl font-bold text-red-600">${{ number_format($totalExpense, 2, ',', '.') }}</p>
                        </div>
                        <div class="bg-blue-50 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-blue-700 mb-2">Balance Actual</h3>
                            <p id="current-balance-display" class="text-3xl font-bold text-blue-600">${{ number_format($currentBalance, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    {{-- Filtros para Gráfico --}}
                    <div class="bg-gray-50 p-6 rounded-lg shadow mb-8">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800"><i class="fas fa-filter mr-2"></i>Filtrar Gráfico</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Desde Fecha:</label>
                                <input type="date" id="start_date" name="start_date" class="form-input w-full rounded-md shadow-sm border-gray-300" value="{{ $selectedStartDate }}">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Hasta Fecha:</label>
                                <input type="date" id="end_date" name="end_date" class="form-input w-full rounded-md shadow-sm border-gray-300" value="{{ $selectedEndDate }}">
                            </div>
                            <div>
                                <label for="category_filter" class="block text-sm font-medium text-gray-700 mb-1">Categoría:</label>
                                <select id="category_filter" name="category_id" class="form-select w-full rounded-md shadow-sm border-gray-300">
                                    <option value="all">Todas las Categorías</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $selectedCategoryId == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }} ({{ ucfirst($category->type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="type_filter" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Transacción:</label>
                                <select id="type_filter" name="type" class="form-select w-full rounded-md shadow-sm border-gray-300">
                                    <option value="all" {{ $selectedType == 'all' ? 'selected' : '' }}>Todos los Tipos</option>
                                    <option value="income" {{ $selectedType == 'income' ? 'selected' : '' }}>Ingresos</option>
                                    <option value="expense" {{ $selectedType == 'expense' ? 'selected' : '' }}>Gastos</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 text-right">
                            <button id="apply_filters" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-search mr-2"></i> Aplicar Filtros
                            </button>
                        </div>
                    </div>

                    {{-- Gráfico de Transacciones --}}
                    <div class="bg-white p-6 rounded-lg shadow mb-8">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800"><i class="fas fa-chart-line mr-2"></i>Historial de Transacciones Mensuales</h3>
                        <canvas id="transactionChart" class="w-full"></canvas>
                    </div>

                    {{-- Recomendaciones --}}
                    <div class="bg-yellow-50 p-6 rounded-lg shadow">
                        <h3 class="text-xl font-semibold mb-4 text-yellow-800"><i class="fas fa-lightbulb mr-2"></i>Recomendaciones Financieras</h3>
                        <ul id="recommendations-list" class="list-disc pl-5 text-yellow-900">
                            @forelse ($recommendations as $rec)
                                <li>{{ $rec }}</li>
                            @empty
                                <li>No hay recomendaciones disponibles por el momento.</li>
                            @endforelse
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.33/moment-timezone-with-data.min.js"></script>

    <script>
        // Configurar Chart.js para que use Moment.js
        Chart.defaults.font.family = "'figtree', sans-serif"; // Usar la fuente de Laravel Breeze

        let transactionChart; // Variable global para la instancia del gráfico

        function formatCurrency(amount) {
            return '$' + parseFloat(amount).toLocaleString('es-CO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Función para obtener y actualizar los datos del gráfico y el resumen
        async function updateDashboardData() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const categoryId = document.getElementById('category_filter').value;
            const type = document.getElementById('type_filter').value;

            // Construir la URL con los parámetros de filtro
            const url = new URL('{{ route('dashboard') }}');
            url.searchParams.append('ajax', 'true'); // Indicar al controlador que es una solicitud AJAX
            if (startDate) url.searchParams.append('start_date', startDate);
            if (endDate) url.searchParams.append('end_date', endDate);
            if (categoryId) url.searchParams.append('category_id', categoryId);
            if (type) url.searchParams.append('type', type);

            try {
                const response = await fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Laravel usa esto para detectar peticiones AJAX
                    }
                });
                const data = await response.json();

                // Destruir el gráfico existente si lo hay
                if (transactionChart) {
                    transactionChart.destroy();
                }

                // Actualizar los datos del gráfico
                const ctx = document.getElementById('transactionChart').getContext('2d');
                transactionChart = new Chart(ctx, {
                    type: 'bar', // Puedes cambiar a 'line' o 'doughnut'
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Ingresos',
                                data: data.incomeData,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Gastos',
                                data: data.expenseData,
                                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Balance',
                                data: data.balanceData,
                                type: 'line', // Línea para el balance
                                fill: false,
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 2,
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Permite que el gráfico se ajuste al contenedor
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return formatCurrency(value); // Formatear los valores del eje Y
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += formatCurrency(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });

                // Actualizar el resumen financiero
                document.getElementById('total-income-display').textContent = formatCurrency(data.totalIncome);
                document.getElementById('total-expense-display').textContent = formatCurrency(data.totalExpense);
                document.getElementById('current-balance-display').textContent = formatCurrency(data.currentBalance);

                // Actualizar recomendaciones
                const recommendationsList = document.getElementById('recommendations-list');
                recommendationsList.innerHTML = ''; // Limpiar lista existente
                if (data.recommendations && data.recommendations.length > 0) {
                    data.recommendations.forEach(rec => {
                        const li = document.createElement('li');
                        li.textContent = rec;
                        recommendationsList.appendChild(li);
                    });
                } else {
                    const li = document.createElement('li');
                    li.textContent = 'No hay recomendaciones disponibles por el momento.';
                    recommendationsList.appendChild(li);
                }


            } catch (error) {
                console.error('Error al obtener datos del dashboard:', error);
                // Mostrar un mensaje de error al usuario si la petición falla
                alert('Hubo un error al cargar los datos del dashboard. Por favor, inténtalo de nuevo.');
            }
        }

        // Event listener para el botón de aplicar filtros
        document.getElementById('apply_filters').addEventListener('click', updateDashboardData);

        // Opcional: También aplicar filtros al cambiar cualquier filtro directamente (sin botón)
        // document.getElementById('start_date').addEventListener('change', updateDashboardData);
        // document.getElementById('end_date').addEventListener('change', updateDashboardData);
        // document.getElementById('category_filter').addEventListener('change', updateDashboardData);
        // document.getElementById('type_filter').addEventListener('change', updateDashboardData);

        // Inicializar el gráfico al cargar la página (los datos iniciales ya vienen del controlador)
        // No necesitamos llamar updateDashboardData aquí, ya que los datos iniciales se pasan por Blade.
        // Pero el código de inicialización del gráfico sí debe ejecutarse al inicio.
        document.addEventListener('DOMContentLoaded', function() {
            const initialCtx = document.getElementById('transactionChart').getContext('2d');
            transactionChart = new Chart(initialCtx, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Ingresos',
                            data: @json($chartIncomeData),
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Gastos',
                            data: @json($chartExpenseData),
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Balance',
                            data: @json($chartBalanceData),
                            type: 'line',
                            fill: false,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += formatCurrency(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>