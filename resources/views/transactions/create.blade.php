<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($transaction) ? __('Editar Transacción') : __('Añadir Nueva Transacción') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ isset($transaction) ? route('transactions.update', $transaction) : route('transactions.store') }}">
                        @csrf
                        @if(isset($transaction))
                            @method('PUT')
                        @endif

                        {{-- Tipo de Transacción --}}
                        <div class="mb-6">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-random mr-2"></i> Tipo de Transacción
                            </label>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" class="form-radio h-4 w-4 text-green-600 transition duration-150 ease-in-out" name="type" id="income_type" value="income" {{ (old('type', $transaction->type ?? '') == 'income') ? 'checked' : '' }} required>
                                    <span class="ml-2 text-gray-700"><i class="fas fa-arrow-up mr-1 text-green-500"></i> Ingreso</span>
                                </label>
                                <label class="flex items-center cursor-pointer ml-0 sm:ml-6">
                                    <input type="radio" class="form-radio h-4 w-4 text-red-600 transition duration-150 ease-in-out" name="type" id="expense_type" value="expense" {{ (old('type', $transaction->type ?? '') == 'expense') ? 'checked' : '' }} required>
                                    <span class="ml-2 text-gray-700"><i class="fas fa-arrow-down mr-1 text-red-500"></i> Gasto</span>
                                </label>
                            </div>
                            @error('type')
                                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Categoría (filtrado dinámico) --}}
                        <div class="mb-6">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-list-alt mr-2"></i> Categoría
                            </label>
                            <select class="form-select block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-base p-3 @error('category_id') border-red-500 @enderror" id="category_id" name="category_id">
                                <option value="">Selecciona una categoría (Opcional)</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                            data-type="{{ $category->type }}" {{-- ¡Importante para el JS! --}}
                                            {{ (old('category_id', $transaction->category_id ?? '') == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ ucfirst($category->type) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Monto --}}
                        <div class="mb-6">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-dollar-sign mr-2"></i> Monto
                            </label>
                            <input type="number" step="0.01" min="0.01" class="form-input block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-lg p-3 @error('amount') border-red-500 @enderror" id="amount" name="amount" value="{{ old('amount', $transaction->amount ?? '') }}" placeholder="Ej: 50.00" required>
                            @error('amount')
                                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-info-circle mr-2"></i> Descripción (Opcional)
                            </label>
                            <textarea class="form-textarea block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-base p-3 @error('description') border-red-500 @enderror" id="description" name="description" rows="3" placeholder="Ej: Compra de comestibles, Pago de nómina">{{ old('description', $transaction->description ?? '') }}</textarea>
                            @error('description')
                                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Fecha --}}
                        <div class="mb-8">
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2"></i> Fecha
                            </label>
                            <input type="date" class="form-input block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-base p-3 @error('date') border-red-500 @enderror" id="date" name="date" value="{{ old('date', $transaction->date ?? \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                            @error('date')
                                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="flex items-center justify-end mt-4 gap-4">
                            <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-times-circle mr-2"></i> Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i> {{ isset($transaction) ? 'Actualizar Transacción' : 'Guardar Transacción' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script JavaScript para el filtrado dinámico de categorías --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeRadios = document.querySelectorAll('input[name="type"]');
            const categorySelect = document.getElementById('category_id');
            // Almacenar todas las opciones originales, excluyendo la primera "Selecciona..."
            const allCategoryOptions = Array.from(categorySelect.querySelectorAll('option:not([value=""])'));
            const defaultOption = categorySelect.querySelector('option[value=""]');


            function filterCategories() {
                const selectedType = document.querySelector('input[name="type"]:checked');
                const typeValue = selectedType ? selectedType.value : null;

                // Limpiar opciones actuales, excepto la predeterminada
                categorySelect.innerHTML = '';
                categorySelect.appendChild(defaultOption);

                let hasValidCategorySelected = false;
                const currentSelectedCategoryId = categorySelect.value; // Guardar la categoría seleccionada antes de filtrar

                allCategoryOptions.forEach(option => {
                    const optionType = option.dataset.type;
                    if (optionType === typeValue) {
                        categorySelect.appendChild(option); // Añadir de nuevo si coincide con el tipo
                        if (option.value === currentSelectedCategoryId) {
                            hasValidCategorySelected = true;
                        }
                    } else {
                        // Si no coincide, asegurar que no esté seleccionada
                        if (option.selected) {
                            option.selected = false;
                        }
                    }
                });

                // Si la categoría seleccionada no es válida para el nuevo tipo, o no hay ninguna seleccionada,
                // selecciona la primera opción válida si existe, o la opción por defecto.
                if (!hasValidCategorySelected) {
                    if (typeValue) { // Si hay un tipo seleccionado
                        // Intenta seleccionar la categoría previamente seleccionada si está en las opciones válidas
                        const previouslySelectedOption = categorySelect.querySelector(`option[value="${currentSelectedCategoryId}"]`);
                        if (previouslySelectedOption && previouslySelectedOption.dataset.type === typeValue) {
                            previouslySelectedOption.selected = true;
                        } else {
                            // Si no, selecciona la opción por defecto o la primera opción válida
                            categorySelect.value = ""; // Selecciona la opción "Selecciona una categoría"
                            const firstVisibleOption = categorySelect.querySelector('option:not([value=""]):not([style*="display: none"])');
                            if (firstVisibleOption) {
                                firstVisibleOption.selected = true;
                            }
                        }
                    } else { // Si no hay tipo seleccionado (ej. al cargar por primera vez)
                        categorySelect.value = ""; // Asegura que la opción "Selecciona una categoría" esté seleccionada
                    }
                }
            }

            // Añadir event listeners a los radio buttons de tipo
            typeRadios.forEach(radio => {
                radio.addEventListener('change', filterCategories);
            });

            // Ejecutar el filtro al cargar la página para aplicar el estado inicial
            filterCategories();
        });
    </script>
    @endpush
</x-app-layout>