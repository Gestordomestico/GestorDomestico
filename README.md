
# GestorDoméstico: Tu Asistente de Finanzas Personales

-----

## 👋 ¡Hola a todos\!

¡Bienvenidos al repositorio de **GestorDoméstico**\! Este proyecto es una aplicación web que he desarrollado para la gestión de finanzas personales, y me entusiasma compartirlo con ustedes.

Actualmente, estoy trabajando en este **MVP (Producto Mínimo Viable)** para ofrecer una herramienta clara y efectiva que te ayude a controlar tus ingresos y gastos. Siempre estoy aprendiendo y buscando maneras de mejorar, así que cualquier comentario, sugerencia o contribución es siempre bienvenida. ¡Exploren el código y no duden en contactarme\!

-----

## Descripción del Proyecto

**GestorDoméstico** es una aplicación web intuitiva y robusta, diseñada para **empoderarte en el manejo eficiente de tus finanzas personales**. Su propósito principal es brindarte una plataforma fiable y organizada para registrar y analizar tus ingresos y gastos. Esto te proporcionará la base necesaria para alcanzar la solvencia económica y cumplir tus metas financieras.

Como **MVP (Producto Mínimo Viable)**, el proyecto se enfoca en las funcionalidades esenciales. Así, podrás empezar a controlar tu dinero de forma clara y visual, lo cual es fundamental para un análisis financiero sólido y una toma de decisiones informada.

-----

## Características Clave

  * **Registro y Autenticación Segura de Usuarios:** Te permite crear tu propia cuenta y gestionar tus finanzas de forma privada con sesiones seguras.
  * **Control Detallado de Transacciones:** Registra fácilmente tus **ingresos** (salarios, inversiones, regalos) y **gastos** (alimentos, transporte, vivienda), asignando cada transacción a una categoría específica.
  * **Gestión Personalizada de Categorías:** Tienes la flexibilidad de crear y eliminar categorías tanto para ingresos como para gastos. El formulario de transacciones filtra las categorías automáticamente según el tipo de operación.
  * **Análisis Financiero Visual:**
      * **Balance Actual:** Una visión clara de tu estado financiero actual (positivo o negativo).
      * **Totales de Ingresos y Gastos:** Un resumen global de tus flujos de dinero en un periodo.
      * **Reportes Gráficos:** Visualiza la distribución de tus gastos e ingresos por categoría mediante gráficos de pastel y dona (usando Chart.js). Esto facilita la identificación rápida de patrones de gasto y áreas de mejora.
  * **Historial Organizado:** Todas las transacciones se guardan con su fecha y una descripción opcional, permitiendo un historial organizado para futuras revisiones.

-----

## Tecnologías Utilizadas

Este proyecto ha sido construido utilizando una pila de tecnologías ligeras y eficientes, lo que garantiza un despliegue y mantenimiento sencillos:

  * **Backend:**
      * **PHP (Plano):** Se encarga de la lógica del servidor, la gestión de las APIs y el procesamiento de datos.
      * **SQLite:** Una base de datos embebida, ideal para un almacenamiento de datos ligero y portátil sin necesidad de un servidor de base de datos separado.
  * **Frontend:**
      * **HTML5 y CSS3:** Proporcionan la estructura y los estilos de la interfaz de usuario.
      * **Bootstrap 5:** Un framework CSS que asegura un diseño responsivo y moderno, adaptándose a diferentes tamaños de pantalla.
      * **JavaScript:** Implementa la lógica interactiva del lado del cliente, mejorando la experiencia del usuario.
      * **Chart.js:** Una potente biblioteca JavaScript utilizada para la creación de gráficos dinámicos y visualización de datos financieros.

-----

## Estructura del Proyecto

La organización de los archivos y directorios del proyecto es la siguiente:

```
gestor_domestico_mvp/
├── public/                       # Contiene todos los archivos accesibles desde el navegador (frontend y APIs PHP)
│   ├── api/                      # Endpoints de la API REST (PHP)
│   │   ├── auth.php              # Autenticación de usuarios (registro, login, logout)
│   │   ├── categories.php        # Gestión de categorías
│   │   ├── reports.php           # Generación de reportes y resúmenes financieros
│   │   └── transactions.php      # Gestión de transacciones
│   ├── css/
│   │   └── style.css             # Estilos CSS personalizados de la aplicación
│   ├── js/
│   │   ├── app.js                # Lógica principal del dashboard (manejo de transacciones, reportes)
│   │   ├── auth.js               # Lógica para la autenticación de usuarios (login, registro, logout)
│   │   └── categories.js         # Lógica para la gestión de categorías
│   ├── index.html                # El dashboard principal de la aplicación
│   ├── login.html                # Página para iniciar sesión
│   └── register.html             # Página para registrar nuevos usuarios
├── database/
│   └── gestordomestico.sqlite    # Archivo de la base de datos SQLite
├── .env                          # Archivo de variables de entorno (por ejemplo, la ruta a la base de datos) - ¡IGNORADO POR GIT!
├── .gitignore                    # Reglas para Git que especifican qué archivos y directorios deben ser ignorados y no subidos al repositorio
├── config.php                    # Archivo de configuración global (manejo de la conexión a la DB, carga de .env)
└── functions.php                 # Funciones PHP de utilidad (hashing de contraseñas, gestión de sesiones, etc.)
```

-----

## Requisitos

Para poder ejecutar este proyecto en tu máquina local, necesitarás un entorno de servidor web compatible con PHP. Algunas opciones populares incluyen:

  * **Apache o Nginx**
  * **PHP 7.4 o superior** (es crucial que la extensión `php_sqlite3` esté habilitada en tu configuración de PHP).
  * Un servidor local preconfigurado como **XAMPP, WAMP, MAMP, o Laragon**. (Personalmente, **recomiendo Laragon** por su facilidad de uso y su entorno ligero y rápido).

-----

## Instalación y Ejecución Local

Sigue estos pasos detallados para poner en marcha GestorDoméstico en tu entorno de desarrollo local:

1.  **Clonar el Repositorio:**
    Abre tu terminal (Git Bash, CMD, PowerShell) y clona este repositorio en el directorio de documentos web de tu servidor local (por ejemplo, `htdocs` si usas XAMPP, o `www` si usas Laragon):

    ```bash
    git clone https://github.com/Gestordomestico/GestorDomestico.git
    ```

    Una vez clonado, navega a la carpeta del proyecto:

    ```bash
    cd GestorDomestico
    ```

2.  **Configurar la Base de Datos SQLite:**

      * Dentro de la carpeta `database/` del proyecto (`GestorDomestico/database/`), crea un archivo vacío llamado `gestordomestico.sqlite`.

      * Utiliza una herramienta de gestión de bases de datos SQLite como [DB Browser for SQLite](https://sqlitebrowser.org/) (recomendado) para abrir el archivo `gestordomestico.sqlite` que acabas de crear.

      * En la interfaz de la herramienta, ejecuta el siguiente script SQL. Este script creará las tablas necesarias (`users`, `categories`, `transactions`) y poblará la tabla `categories` con datos iniciales:

        ```sql
        -- Tabla de Usuarios
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        -- Tabla de Categorías (para Ingresos y Gastos)
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            type TEXT NOT NULL
        );

        -- Tabla de Transacciones
        CREATE TABLE IF NOT EXISTS transactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            type TEXT NOT NULL,
            category TEXT NOT NULL,
            amount REAL NOT NULL,
            description TEXT,
            date TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );

        -- Datos iniciales de categorías
        INSERT OR IGNORE INTO categories (name, type) VALUES ('Salario', 'income');
        INSERT OR IGNORE INTO categories (name, type) VALUES ('Inversiones', 'income');
        INSERT OR IGNORE INTO categories (name, type) VALUES ('Regalo', 'income');
        INSERT OR IGNORE INTO categories (name, type) VALUES ('Alimentos', 'expense');
        INSERT OR IGNORE INTO categories (name, type) VALUES ('Transporte', 'expense');
        INSERT OR IGNORE INTO categories (name, type) VALUES ('Entretenimiento', 'expense');
        INSERT OR IGNORE INTO categories (name, type) VALUES ('Servicios', 'expense');
        INSERT OR IGNORE INTO categories (name, type) VALUES ('Vivienda', 'expense');
        INSERT OR IGNORE INTO categories (name, type) VALUES ('Educación', 'expense');
        INSERT OR IGNORE INTO categories (name, type) VALUES ('Salud', 'expense');
        INSERT OR IGNORE INTO categories (name, type) VALUES ('Compras', 'expense');
        ```

3.  **Configurar Variables de Entorno (`.env`):**

      * En la **raíz del proyecto** (`GestorDomestico/`), crea un archivo llamado `.env` (asegúrate de que no tenga ninguna extensión adicional como `.txt`).
      * Dentro de este archivo `.env`, añade la siguiente línea, que indica la ruta relativa a tu base de datos SQLite:
        ```dotenv
        DB_PATH=database/gestordomestico.sqlite
        ```
        **Nota:** Esta ruta `database/gestordomestico.sqlite` es relativa al archivo `config.php`, que es donde se lee esta variable.

4.  **Iniciar el Servidor Web:**

      * Inicia tu servidor web (Apache/Nginx) y el servicio de PHP a través del panel de control de tu entorno local (XAMPP, Laragon, etc.).

5.  **Acceder a la Aplicación:**

      * Abre tu navegador web y ve a la URL donde se aloja tu proyecto. Por ejemplo, si colocaste la carpeta `GestorDomestico` directamente en el directorio `www` de Laragon, la URL de acceso sería:
        `http://localhost/GestorDomestico/public/login.html`

-----

## Uso de la Aplicación

1.  **Registro:** Al acceder a la aplicación por primera vez (o si aún no tienes una cuenta), serás dirigido a la página de inicio de sesión. Haz clic en "Regístrate aquí" para crear una nueva cuenta de usuario.
2.  **Inicio de Sesión:** Ingresa tu nombre de usuario y contraseña registrados para acceder al dashboard principal de la aplicación.
3.  **Dashboard:** Esta es tu vista general. Aquí podrás ver un resumen de tus finanzas, los totales de ingresos y gastos, tu balance actual y gráficos de distribución que te ayudarán a entender tus hábitos financieros.
4.  **Registrar Transacciones:** Utiliza el formulario intuitivo en el dashboard para añadir nuevos ingresos o gastos. Simplemente selecciona el tipo (ingreso o gasto), la categoría, el monto, la fecha y añade una una descripción opcional.
5.  **Gestión de Categorías:** Navega a la sección "Categorías" para añadir nuevas categorías personalizadas que se adapten mejor a tus necesidades, o para eliminar categorías existentes (siempre que no estén siendo utilizadas en alguna transacción).
6.  **Cerrar Sesión:** Para salir de tu cuenta de forma segura, haz clic en el botón "Cerrar Sesión" ubicado en la barra de navegación.

-----

## Contribuciones

Este proyecto es un MVP y está diseñado para ser extensible. ¡Tu ayuda es bienvenida para mejorarlo\! Si tienes sugerencias, ideas para nuevas características, mejoras en el código o deseas contribuir de cualquier otra forma, no dudes en:

1.  Hacer un "fork" (bifurcación) de este repositorio.
2.  Crear una nueva rama para tu característica o corrección: `git checkout -b feature/nombre-de-tu-caracteristica`.
3.  Realizar tus cambios y hacer un commit con un mensaje descriptivo: `git commit -m 'Añadir nueva característica X / Corregir error Y'`.
4.  Subir tu rama a tu repositorio bifurcado: `git push origin feature/nombre-de-tu-caracteristica`.
5.  Abrir un "Pull Request" (Solicitud de Extracción) hacia la rama `main` de este repositorio.

-----

## Licencia

Este proyecto se distribuye bajo una licencia que permite su **uso, modificación y distribución con fines no comerciales**.

**El derecho exclusivo a la distribución y explotación comercial de GestorDoméstico recae únicamente en el autor original.**

Para cualquier consulta sobre licencias comerciales o usos que excedan los términos no comerciales, por favor, contacta directamente al autor.

-----
