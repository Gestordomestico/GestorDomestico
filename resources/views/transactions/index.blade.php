<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis Transacciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes de éxito/error --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">¡Éxito!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none';"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 2.65a1.2 1.2 0 1 1-1.697-1.697L8.303 10l-2.651-2.651a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-2.651a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">¡Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none';"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 2.65a1.2 1.2 0 1 1-1.697-1.697L8.303 10l-2.651-2.651a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-2.651a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-end mb-6">
                        <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md">
                            <i class="fas fa-plus mr-2"></i> Añadir Transacción
                        </a>
                    </div>

                    @if ($transactions->isEmpty())
                        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-8 rounded-lg text-center">
                            <i class="fas fa-money-bill-transfer fa-3x mb-4 text-blue-400"></i>
                            <h4 class="text-xl font-semibold mb-2">¡Aún no tienes transacciones registradas!</h4>
                            <p class="text-base text-blue-600 mb-4">Empieza a registrar tus ingresos y gastos para gestionar tus finanzas.</p>
                            <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-plus mr-2"></i> Registrar Transacción
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="py-3 px-6 whitespace-nowrap">Fecha <i class="fas fa-calendar-alt ml-1"></i></th>
                                        <th scope="col" class="py-3 px-6 whitespace-nowrap">Tipo <i class="fas fa-exchange-alt ml-1"></i></th>
                                        <th scope="col" class="py-3 px-6 whitespace-nowrap">Categoría <i class="fas fa-tag ml-1"></i></th>
                                        <th scope="col" class="py-3 px-6 whitespace-nowrap">Monto <i class="fas fa-dollar-sign ml-1"></i></th>
                                        <th scope="col" class="py-3 px-6">Descripción <i class="fas fa-info-circle ml-1"></i></th>
                                        <th scope="col" class="py-3 px-6 text-center whitespace-nowrap">Acciones <i class="fas fa-cogs ml-1"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="py-4 px-6 whitespace-nowrap">{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                                            <td class="py-4 px-6">
                                                @if ($transaction->type == 'income')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-arrow-up mr-1"></i> Ingreso
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-arrow-down mr-1"></i> Gasto
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6">
                                                {{ $transaction->category->name ?? 'Sin Categoría' }}
                                            </td>
                                            <td class="py-4 px-6 font-semibold {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                ${{ number_format($transaction->amount, 2, ',', '.') }}
                                            </td>
                                            <td class="py-4 px-6">
                                                {{ Str::limit($transaction->description, 70, '...') }}
                                                @if(strlen($transaction->description) > 70)
                                                    <span class="text-gray-500 text-xs">
                                                        (clic para ver más)
                                                    </span>
                                                @endif
                                            </td>
                                            {{-- Bloque de Acciones: Editar y Eliminar --}}
                                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                                <a href="{{ route('transactions.edit', $transaction) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 focus:ring-4 focus:outline-none focus:ring-yellow-300 mr-2">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300" onclick="return confirm('¿Estás seguro de que quieres eliminar esta transacción?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>