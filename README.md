# GestorDoméstico

![GestorDoméstico Logo/Banner](https://via.placeholder.com/800x200/667EEA/FFFFFF?text=GestorDomestico+-+Tu+Control+Financiero)
**Slogan:** *Toma el control de tus finanzas personales con facilidad y claridad.*

## 🌟 Introducción

GestorDoméstico es una aplicación web intuitiva diseñada para ayudarte a gestionar tus finanzas personales de manera eficiente. Registra tus ingresos y gastos, categoriza tus transacciones y visualiza tu situación financiera a través de un dashboard interactivo con gráficos y filtros.

## ✨ Características Principales

* **Registro y Gestión de Transacciones:** Añade, edita y elimina ingresos y gastos con facilidad.
* **Categorización Personalizada:** Organiza tus movimientos financieros usando categorías definidas por ti (Ingresos, Gastos).
* **Dashboard Interactivo:**
    * Visualiza un gráfico de tus transacciones (ingresos, gastos, balance) a lo largo del tiempo.
    * Filtra los datos del gráfico por rango de fechas, categoría y tipo de transacción.
    * Obtén un resumen rápido de tus ingresos, gastos y balance actual.
* **Recomendaciones Financieras:** Recibe consejos básicos basados en tu patrón de gastos y balance.
* **Autenticación Segura:** Sistema de registro y login de usuarios protegido por Laravel Breeze.
* **Interfaz Responsiva:** Accede a tus finanzas desde cualquier dispositivo (desktop, tablet, móvil).

## 🚀 Tecnologías Utilizadas

* **Backend:**
    * PHP 8.3.16
    * Laravel 10.x / 11.x (compatible con la versión 12.17.0 que estás usando)
    * Composer
    * MySQL (Base de Datos)
* **Frontend:**
    * HTML, CSS, JavaScript
    * Tailwind CSS (para el diseño y la responsividad)
    * Chart.js (para la visualización de gráficos)
    * Moment.js (dependencia de Chart.js para manejo de fechas)
    * Axios (para peticiones AJAX en el dashboard)
* **Herramientas:**
    * Git (Control de Versiones)
    * NPM / Yarn (Manejo de paquetes frontend)
    * Vite (Bundler frontend)

## 📦 Instalación

Sigue estos pasos para configurar el proyecto localmente:

### Prerrequisitos

Asegúrate de tener instalado en tu sistema:

* PHP >= 8.2
* Composer
* Node.js & npm (o Yarn)
* Un servidor web (Apache, Nginx) o PHP's built-in server
* MySQL o una base de datos compatible

### Pasos

1.  **Clona el repositorio:**
    ```bash
    git clone [https://github.com/Gestordomestico/GestorDomestico.git](https://github.com/Gestordomestico/GestorDomestico.git)
    cd GestorDomestico
    ```
	
    

2.  **Instala las dependencias de Composer:**
    ```bash
    composer install
    ```

3.  **Copia el archivo de entorno:**
    ```bash
    cp .env.example .env
    ```

4.  **Genera la clave de aplicación:**
    ```bash
    php artisan key:generate
    ```

5.  **Configura la base de datos:**
    Abre el archivo `.env` y configura tus credenciales de base de datos (DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).

    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=gestordomestico_db # O el nombre de tu base de datos
    DB_USERNAME=root
    DB_PASSWORD= # Tu contraseña de MySQL, si tienes
    ```

6.  **Ejecuta las migraciones de la base de datos:**
    ```bash
    php artisan migrate
    ```

7.  **Instala las dependencias de Node.js:**
    ```bash
    npm install
    # o
    yarn install
    ```

8.  **Compila los assets frontend:**
    * Para desarrollo (con Hot Reload):
        ```bash
        npm run dev
        # o
        yarn dev
        ```
    * Para producción:
        ```bash
        npm run build
        # o
        yarn build
        ```

9.  **Inicia el servidor de desarrollo de Laravel:**
    ```bash
    php artisan serve
    ```

    Tu aplicación estará disponible en `http://127.0.0.1:8000` (o el puerto que te indique la consola).

## 💡 Uso

1.  **Regístrate:** Navega a `/register` y crea una nueva cuenta.
2.  **Inicia Sesión:** Una vez registrado, inicia sesión para acceder a tu dashboard personal.
3.  **Gestiona Transacciones:** Ve a la sección de "Transacciones" para añadir, ver, editar o eliminar tus ingresos y gastos.
4.  **Explora el Dashboard:** Utiliza los filtros de fecha, categoría y tipo en el dashboard para analizar tus finanzas y ver los gráficos actualizados.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Si deseas mejorar el proyecto, por favor, abre un "issue" o envía un "pull request".

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Consulta el archivo [LICENSE](LICENSE) para más detalles.