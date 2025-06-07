<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($category) ? __('Editar Categoría') : __('Añadir Nueva Categoría') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ isset($category) ? route('categories.update', $category) : route('categories.store') }}">
                        @csrf
                        @if(isset($category))
                            @method('PUT')
                        @endif

                        {{-- Nombre de la Categoría --}}
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag mr-2"></i> Nombre de la Categoría
                            </label>
                            <input type="text" class="form-input block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-lg p-3 @error('name') border-red-500 @enderror" id="name" name="name" value="{{ old('name', $category->name ?? '') }}" placeholder="Ej: Comida, Transporte, Salario" required autofocus>
                            @error('name')
                                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tipo de Categoría --}}
                        <div class="mb-8">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-exchange-alt mr-2"></i> Tipo de Categoría
                            </label>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" class="form-radio h-4 w-4 text-green-600 transition duration-150 ease-in-out" name="type" id="income_type" value="income" {{ (old('type', $category->type ?? '') == 'income') ? 'checked' : '' }} required>
                                    <span class="ml-2 text-gray-700"><i class="fas fa-arrow-up mr-1 text-green-500"></i> Ingreso</span>
                                </label>
                                <label class="flex items-center cursor-pointer ml-0 sm:ml-6">
                                    <input type="radio" class="form-radio h-4 w-4 text-red-600 transition duration-150 ease-in-out" name="type" id="expense_type" value="expense" {{ (old('type', $category->type ?? '') == 'expense') ? 'checked' : '' }} required>
                                    <span class="ml-2 text-gray-700"><i class="fas fa-arrow-down mr-1 text-red-500"></i> Gasto</span>
                                </label>
                            </div>
                            @error('type')
                                <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="flex items-center justify-end mt-4 gap-4">
                            <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-times-circle mr-2"></i> Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i> {{ isset($category) ? 'Actualizar Categoría' : 'Guardar Categoría' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>