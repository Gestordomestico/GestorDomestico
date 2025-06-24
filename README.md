# 💰 Gestor Doméstico MVP (Producto Mínimo Viable)

![Gestor Doméstico Logo Placeholder](https://via.placeholder.com/150/007bff/ffffff?text=GD+MVP)

Un **sistema de gestión financiera personal ligero, intuitivo y robusto**, diseñado para empoderarte en el control de tus ingresos y gastos de forma sencilla y segura. Desarrollado con PHP, SQLite y JavaScript, este proyecto integra **principios de seguridad OWASP**, un diseño responsive moderno y una experiencia de usuario fluida, haciendo que la administración de tus finanzas sea una tarea clara y sin complicaciones.

---

## 🎯 Propósito del Proyecto

El **Gestor Doméstico MVP** nace de la necesidad de ofrecer una herramienta accesible para el seguimiento financiero personal. Su objetivo principal es proporcionar una visión clara y en tiempo real de tu salud económica, facilitando la toma de decisiones informadas sobre tus hábitos de gasto y ahorro. Queremos que cualquier persona, sin conocimientos avanzados de contabilidad, pueda gestionar sus finanzas de manera efectiva.

---

## ✨ Características Principales

Hemos integrado funcionalidades clave para una gestión financiera eficiente:

* **Gestión Integral de Transacciones:**
    * Registra tus ingresos y gastos con **detalles completos**: monto, descripción, fecha y categoría.
    * **Edita y elimina** transacciones existentes con facilidad.
    * **Validación robusta** de datos para asegurar la integridad de la información financiera.
* **Resumen Financiero Interactivo:**
    * Visualiza al instante tus **ingresos totales, gastos totales y el balance actual**.
    * **Filtros dinámicos por rango de fechas** para analizar tus finanzas en periodos específicos (día, semana, mes, año, personalizado).
    * Representación clara de los datos para una comprensión rápida de tu situación económica.
* **Gestión de Categorías Personalizadas:**
    * Crea, edita y elimina **categorías de ingresos y gastos** (ej. "Salario", "Alquiler", "Comida", "Transporte", "Entretenimiento").
    * **Autocompletado inteligente de categorías** al añadir transacciones, acelerando el proceso y estandarizando tus registros.
* **Interfaz de Usuario (UI) & Experiencia de Usuario (UX):**
    * **Diseño limpio, moderno y atractivo**, enfocado en la usabilidad.
    * **Totalmente Responsive:** La interfaz se adapta perfectamente a cualquier tamaño de pantalla, desde ordenadores de escritorio hasta tablets y teléfonos móviles, garantizando una experiencia consistente.
    * Navegación intuitiva y flujos de trabajo simplificados para minimizar la curva de aprendizaje.

---

## 🔒 Seguridad Reforzada (OWASP Compliance)

La seguridad es un pilar fundamental en este proyecto, siguiendo las directrices del **Open Web Application Security Project (OWASP)** para mitigar vulnerabilidades comunes:

* **Prevención de Inyección SQL:** Todas las interacciones con la base de datos se realizan mediante **PDO (PHP Data Objects) y consultas parametrizadas**, eliminando el riesgo de inyección de código SQL malicioso.
* **Protección contra XSS (Cross-Site Scripting):**
    * Los datos proporcionados por el usuario son rigurosamente **saneados con `htmlspecialchars()`** en el backend antes de ser procesados y mostrados.
    * En el frontend, se utilizan funciones de escape (`escapeHtml()`) para asegurar que el contenido generado dinámicamente sea seguro.
* **Gestión Segura de Autenticación y Sesiones:**
    * **Contraseñas almacenadas de forma segura** utilizando la función `password_hash()` con el algoritmo **BCRYPT**, y verificadas con `password_verify()`, impidiendo la recuperación de contraseñas en texto plano.
    * **Regeneración de IDs de sesión** en cada inicio de sesión y periódicamente para prevenir ataques de fijación y secuestro de sesión.
    * Control de **tiempo de inactividad de sesión** con expiración automática para reducir el riesgo de acceso no autorizado en sesiones abandonadas.
    * Verificación del **User Agent** para detectar posibles secuestros de sesión y anomalías.
* **Validación y Saneamiento de Entrada:**
    * Todos los datos recibidos de las solicitudes del usuario (vía APIs) son **estrictamente validados** (tipo de dato, formato, longitud, rango) y saneados (eliminación de caracteres no deseados) antes de cualquier procesamiento o almacenamiento.
    * Esto incluye montos financieros, fechas, descripciones textuales e IDs.
* **Control de Acceso (Autorización):** Se implementa una lógica de autorización robusta para asegurar que los usuarios solo puedan acceder y modificar sus propias transacciones, categorías y datos financieros, impidiendo el acceso a información de otros usuarios.
* **Mensajes de Error Generales:** Los mensajes de error relacionados con la autenticación (ej. credenciales inválidas) son genéricos para evitar la enumeración de usuarios o la divulgación de información sensible.

---

## 🚀 Cómo Empezar

### Requisitos del Sistema

Asegúrate de tener instalado lo siguiente en tu entorno de desarrollo/servidor:

* **Servidor Web:** Apache (recomendado), Nginx o cualquier otro servidor compatible con PHP.
* **PHP:** Versión 7.4 o superior.
    * **Extensión `pdo_sqlite`:** Debe estar habilitada en tu configuración `php.ini`.
* **Navegador Web Moderno:** Compatible con HTML5, CSS3 y JavaScript (Chrome, Firefox, Edge, Safari, etc.).

### Instalación y Configuración Local (con Laragon)

Laragon es un excelente entorno de desarrollo local que simplifica la configuración de Apache y PHP.

1.  **Clona o descarga el repositorio:**
    Abre tu terminal o Git Bash y navega a tu directorio de Laragon `www` (normalmente `C:\laragon\www`). Luego, clona el proyecto:
    ```bash
    cd C:\laragon\www
    git clone [https://github.com/Gestordomestico/GestorDomestico.git](https://github.com/Gestordomestico/GestorDomestico.git)
    ```
    Esto creará una carpeta llamada `GestorDomestico` dentro de `C:\laragon\www`.

2.  **Inicia Laragon y Configura el Virtual Host:**
    * Abre Laragon y asegúrate de que **Apache y MySQL** (aunque SQLite no necesita MySQL, es una buena práctica tenerlo funcionando) estén iniciados.
    * Laragon detectará automáticamente la nueva carpeta `GestorDomestico` y creará un "Virtual Host" para ella. La URL local será `http://gestordomestico.test` (recomendado por Laragon) o `http://localhost:8096/GestorDomestico` si estás usando un puerto específico y no el virtual host.
    * Para esta guía, asumiremos `http://localhost:8096/GestorDomestico` para la ruta base de la aplicación.

3.  **Configuración de la Aplicación (`config.php`):**
    * Abre el archivo `config.php` ubicado en la raíz de tu proyecto (`C:\laragon\www\GestorDomestico\`).
    * Verifica la línea `define('BASE_URL', '/GestorDomestico/public');`.
        * **¡Es crucial!** Asegúrate de que `/GestorDomestico` **coincida exactamente** con el nombre de la carpeta de tu proyecto en `C:\laragon\www\`. Si clonaste el repositorio en una carpeta diferente, ajusta este valor.
    * Asegúrate de que la carpeta `data` exista en la raíz de tu proyecto (`C:\laragon\www\GestorDomestico\data\`). Si no, créala manualmente.

4.  **Inicializa la Base de Datos SQLite:**
    * **Paso Crítico de Seguridad:** Si ya existe un archivo `gestordomestico.sqlite` en la carpeta `data`, **borra o renombra ese archivo antes de continuar**. Esto asegura una inicialización limpia.
    * Abre tu navegador web y navega a la URL de inicialización: `http://localhost:8096/GestorDomestico/init_db.php`.
    * Deberías ver un mensaje de éxito como: "¡Base de datos inicializada con éxito! Tablas creadas y categorías por defecto insertadas."
    * **¡ALERTA DE SEGURIDAD!** Una vez que la base de datos se haya inicializado correctamente, por tu propia seguridad, **DEBES ELIMINAR O RENOMBRAR INMEDIATAMENTE el archivo `init_db.php`** de tu servidor (ej. a `init_db.php.bak`). Este archivo no debe ser accesible públicamente después de la instalación inicial.

5.  **Accede a la Aplicación:**
    * Abre tu navegador y ve a la URL principal de la aplicación: `http://localhost:8096/GestorDomestico/public/login`.
    * Para asegurar que se carguen las últimas versiones de los archivos CSS y JavaScript, es recomendable realizar un "Hard Refresh" en tu navegador (normalmente `Ctrl+F5` en Windows/Linux o `Cmd+Shift+R` en macOS).

6.  **¡Regístrate y Empieza a Gestionar tus Finanzas!**
    * En la página de inicio de sesión, haz clic en el enlace para registrarte.
    * Crea una nueva cuenta con tus credenciales preferidas.
    * Inicia sesión y explora el dashboard para comenzar a registrar tus ingresos y gastos.

---

## 📂 Estructura del Proyecto

La estructura del proyecto está diseñada para ser clara y modular:

GestorDomestico/
├── config.php                  # Configuración global de la aplicación (BD, URLs, etc.)
├── functions.php               # Biblioteca de funciones comunes (seguridad, validaciones, utilidades)
├── init_db.php                 # Script para la creación inicial de la base de datos (¡eliminar después de uso!)
├── .gitignore                  # Reglas para Git: qué archivos y directorios ignorar del control de versiones
├── LICENSE                     # Archivo de licencia del proyecto
├── README.md                   # Este documento
├── data/                       # Directorio para la base de datos SQLite (excluido de Git)
│   └── gestordomestico.sqlite  # Archivo de la base de datos SQLite
└── public/                     # Contenido públicamente accesible (punto de entrada del servidor web)
├── index.php               # Front Controller: gestiona todas las solicitudes, el enrutamiento y la sesión.
├── css/
│   └── style.css           # Hoja de estilos principal de la aplicación (incluye diseño responsive)
├── js/
│   ├── auth.js             # Lógica JavaScript para las funcionalidades de autenticación (login, registro)
│   └── app.js              # Lógica JavaScript para la interacción del dashboard (CRUD de transacciones, categorías, reportes)
└── api/                    # Directorio para las APIs RESTful (endpoints para el frontend)
├── auth.php            # API para la gestión de usuarios (registro, login, logout)
├── transactions.php    # API para las operaciones CRUD de transacciones
├── categories.php      # API para la gestión de categorías
└── reports.php         # API para generar y recuperar resúmenes financieros
└── views/                  # Vistas HTML (archivos estáticos o plantillas)
├── login.html          # Página de inicio de sesión
├── register.html       # Página de registro de nuevos usuarios
└── dashboard.html      # El panel de control principal de la aplicación


---

## 🛠️ Tecnologías Utilizadas

Este proyecto se basa en un stack de tecnologías robusto y ampliamente utilizado:

* **Backend:**
    * **PHP 7.4+:** Lenguaje de scripting del lado del servidor.
    * **SQLite3:** Base de datos ligera basada en archivos, ideal para proyectos MVP o aplicaciones con requisitos de escalabilidad moderados.
    * **PDO (PHP Data Objects):** Capa de abstracción de base de datos para una interacción segura y consistente con SQLite.
* **Frontend:**
    * **HTML5:** Lenguaje de marcado para la estructura de las páginas web.
    * **CSS3:** Hoja de estilos para el diseño y la presentación visual, incluyendo la adaptación responsive.
    * **JavaScript (Vanilla JS):** Lenguaje de scripting del lado del cliente para la interactividad dinámica y la comunicación con las APIs.
* **Servidor Web:**
    * **Apache (con Laragon):** Servidor HTTP de código abierto, configurado para servir la aplicación PHP.

---

## 🗺️ Roadmap Futuro

Este es un Producto Mínimo Viable (MVP), y tenemos muchas ideas para futuras mejoras y expansiones:

* **Exportación de Datos:** Funcionalidad para exportar transacciones a formatos como CSV o PDF.
* **Reportes y Gráficos Avanzados:** Gráficos visuales de gastos por categoría, tendencias mensuales, etc.
* **Presupuestos Mensuales:** Establecer límites de gasto para diferentes categorías.
* **Notificaciones:** Alertas sobre gastos excesivos o metas de ahorro.
* **Soporte Multi-Usuario:** Aunque actualmente cada usuario gestiona sus propias finanzas, se podría explorar la compartición de cuentas entre miembros de una familia.
* **Internacionalización (i18n):** Soporte para múltiples idiomas y formatos de moneda.
* **Integración con APIs Bancarias:** (Consideración a largo plazo, con énfasis en seguridad y privacidad).
* **Optimización y Rendimiento:** Mejora continua del código y la carga para una experiencia aún más fluida.

---

## 🤝 Contribuciones

Las contribuciones son **extremadamente bienvenidas y valoradas**, especialmente para fines académicos o para la mejora continua del código y las funcionalidades. Si estás interesado en contribuir:

1.  Haz un "fork" de este repositorio.
2.  Crea una nueva rama (`git checkout -b feature/nueva-funcionalidad`).
3.  Implementa tus cambios y asegúrate de que el código sea limpio y esté bien comentado.
4.  Escribe pruebas si es aplicable (aunque no se implementaron en este MVP inicial).
5.  Asegúrate de que tu código cumpla con los estándares de seguridad ya establecidos.
6.  Haz un "commit" de tus cambios (`git commit -m 'feat: Añadir nueva funcionalidad X'`).
7.  Envía tus cambios a tu "fork" (`git push origin feature/nueva-funcionalidad`).
8.  Abre un "Pull Request" (PR) al repositorio original.

¡Valoramos cualquier aporte que mejore el proyecto para la comunidad académica y de desarrollo!

---

## 📄 Licencia

Este proyecto está bajo una **variante modificada de la Licencia MIT**.

* El uso del software está **estrictamente restringido a fines académicos, de investigación y personales**.
* **Queda expresamente prohibido el uso comercial** de este software por parte de cualquier persona o entidad que no sea el autor original.
* El autor original (`GestorDomestico`) **se reserva todos los derechos para el uso comercial** del Software. Cualquier uso comercial por parte de terceros requiere una licencia explícita y por escrito del autor.

Para obtener todos los detalles legales y términos específicos, consulta el archivo `LICENSE` en la raíz de este repositorio.

---

## ✉️ Contacto

Para preguntas, sugerencias, reporte de errores o cualquier tipo de soporte relacionado con el proyecto, no dudes en contactarnos a través de:

* **Correo Electrónico:** gestordomestico0@gmail.com
* **Repositorio GitHub:** [https://github.com/Gestordomestico/GestorDomestico](https://github.com/Gestordomestico/GestorDomestico) (abre un "Issue" para problemas o ideas).

---
