<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GestorDoméstico - Controla tus Finanzas</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta8FYb9F/gEDSuMVMs/JxxUgyox/qMzzK5HkXhZ2KzW9d1==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
        {{-- Navbar --}}
        <nav class="w-full bg-white shadow-sm py-4 px-6 md:px-12 flex justify-between items-center fixed top-0 z-50">
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="text-2xl font-bold text-indigo-600 flex items-center">
                    <i class="fas fa-wallet mr-2"></i> GestorDoméstico
                </a>
            </div>
            <div class="flex items-center space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold px-3 py-2 rounded-md transition duration-150 ease-in-out">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold px-3 py-2 rounded-md transition duration-150 ease-in-out">Iniciar Sesión</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 font-semibold transition duration-150 ease-in-out">Registrarse</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>

        {{-- Hero Section --}}
        <header class="w-full flex items-center justify-center bg-gradient-to-r from-indigo-600 to-purple-700 text-white py-24 px-6 md:px-12 text-center mt-16 md:mt-0" style="min-height: calc(100vh - 64px);"> {{-- Adjust min-height for navbar --}}
            <div class="max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-6">
                    <span class="block">GestorDoméstico</span>
                    <span class="block text-indigo-200 text-3xl md:text-5xl mt-2">Toma el control de tus finanzas personales</span>
                </h1>
                <p class="text-xl md:text-2xl text-indigo-100 mb-10">
                    La herramienta intuitiva que te ayuda a organizar tus ingresos y gastos para una vida financiera más inteligente.
                </p>
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('register') }}" class="bg-white text-indigo-700 px-8 py-4 rounded-full text-lg font-bold hover:bg-gray-100 transition duration-300 ease-in-out shadow-lg">
                        ¡Empieza Ahora!
                    </a>
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="border-2 border-white text-white px-8 py-4 rounded-full text-lg font-bold hover:bg-white hover:text-indigo-700 transition duration-300 ease-in-out shadow-lg">
                            Ya tengo cuenta
                        </a>
                    @endif
                </div>
                {{-- Placeholder para imagen/ilustración --}}
                <div class="mt-16">
                    <img src="https://via.placeholder.com/600x400/667EEA/FFFFFF?text=Tu+Economia+Organizada" alt="Finanzas Personales Organizadas" class="rounded-lg shadow-xl mx-auto max-w-full h-auto">
                    {{-- Si tienes una imagen real, reemplaza la URL de placeholder por la tuya. Por ejemplo: asset('images/finanzas_illustration.png') --}}
                    {{-- Puedes buscar ilustraciones gratuitas en Unsplash, Pexels, Freepik (vectores) o usar un icono grande de Font Awesome --}}
                </div>
            </div>
        </header>

        {{-- Features Section --}}
        <section class="w-full py-20 px-6 md:px-12 bg-white">
            <div class="max-w-6xl mx-auto text-center">
                <h2 class="text-3xl md:text-5xl font-extrabold text-gray-800 mb-12">
                    Funcionalidades Clave
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    {{-- Feature 1 --}}
                    <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300 ease-in-out transform hover:-translate-y-1">
                        <div class="text-5xl text-indigo-500 mb-6">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Registro Sencillo de Transacciones</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Registra tus ingresos y gastos diarios de forma rápida e intuitiva, asegurando que cada movimiento esté bajo control.
                        </p>
                    </div>
                    {{-- Feature 2 --}}
                    <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300 ease-in-out transform hover:-translate-y-1">
                        <div class="text-5xl text-indigo-500 mb-6">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Categorización Inteligente</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Organiza tus movimientos financieros con categorías personalizadas, obteniendo una visión detallada de dónde va tu dinero.
                        </p>
                    </div>
                    {{-- Feature 3 --}}
                    <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300 ease-in-out transform hover:-translate-y-1">
                        <div class="text-5xl text-indigo-500 mb-6">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Reportes y Análisis Visuales</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Visualiza tu salud financiera con reportes y gráficos claros que te ayudarán a tomar decisiones informadas.
                        </p>
                    </div>
                     {{-- Feature 4 (si planeas implementarlo o como visión futura) --}}
                    <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300 ease-in-out transform hover:-translate-y-1 md:col-span-full lg:col-span-1 lg:col-start-2">
                        <div class="text-5xl text-indigo-500 mb-6">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Acceso Responsivo en Cualquier Dispositivo</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Accede a tus finanzas desde cualquier lugar y dispositivo, ya sea desde tu computadora, tablet o smartphone.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Call to Action Section (repetido al final) --}}
        <section class="w-full py-16 px-6 md:px-12 bg-gray-50 text-center">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-8">
                    ¿Listo para transformar tus finanzas?
                </h2>
                <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-8 py-4 rounded-full text-xl font-bold hover:bg-indigo-700 transition duration-300 ease-in-out shadow-lg">
                    ¡Crea tu cuenta gratis!
                </a>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="w-full bg-gray-800 text-gray-300 py-8 px-6 md:px-12 text-center">
            <p>&copy; {{ date('Y') }} GestorDoméstico. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>