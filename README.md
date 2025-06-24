# 💰 Gestor Doméstico MVP (Producto Mínimo Viable)

![Gestor Doméstico Logo Placeholder](https://via.placeholder.com/150/007bff/ffffff?text=GD+MVP)

Un **sistema de gestión financiera personal ligero, intuitivo y robusto**, diseñado para empoderarte en el control de tus ingresos y gastos de forma sencilla y segura. Desarrollado con PHP, SQLite y JavaScript, este proyecto integra **principios de seguridad OWASP**, un diseño responsive moderno y una experiencia de usuario fluida, haciendo que la administración de tus finanzas sea una tarea clara y sin complicaciones.

---

## 🎯 Propósito del Proyecto

El **Gestor Doméstico MVP** nace de la necesidad de ofrecer una herramienta accesible para el seguimiento financiero personal. Su objetivo principal es proporcionar una visión clara y en tiempo real de tu salud económica, facilitando la toma de decisiones informadas sobre tus hábitos de gasto y ahorro. Queremos que cualquier persona, sin conocimientos avanzados de contabilidad, pueda gestionar sus finanzas de manera efectiva.

---

## ✨ Características Principales

Hemos integrado funcionalidades clave para una gestión financiera eficiente:

* **Gestión Integral de Transacciones:** Registra, edita y elimina tus ingresos y gastos con detalles completos: monto, descripción, fecha y categoría. Incluye validación de datos para asegurar la integridad.
* **Resumen Financiero Interactivo:** Visualiza al instante tus ingresos totales, gastos totales y el balance actual, con filtros dinámicos por rango de fechas para análisis específicos.
* **Gestión de Categorías Personalizadas:** Crea y elimina categorías personalizadas (ej. "Salario", "Comida"). Ofrece autocompletado inteligente para facilitar el registro.
* **Interfaz de Usuario (UI) & Experiencia de Usuario (UX):** Diseño limpio, moderno y atractivo, enfocado en la usabilidad. Es totalmente **responsive**, adaptándose a cualquier dispositivo (escritorio, tablet, móvil).

---

## 🔒 Seguridad Reforzada (OWASP Compliance)

La seguridad es un pilar fundamental en este proyecto, siguiendo las directrices del **Open Web Application Security Project (OWASP)** para mitigar vulnerabilidades comunes:

* **Prevención de Inyección SQL:** Uso de **PDO y consultas parametrizadas** para evitar la inyección de código SQL malicioso.
* **Protección contra XSS (Cross-Site Scripting):** Saneamiento riguroso de los datos de usuario (`htmlspecialchars()`) en el backend y funciones de escape en el frontend.
* **Gestión Segura de Autenticación y Sesiones:**
    * Contraseñas almacenadas como **hashes seguros (`BCRYPT`)**.
    * **Regeneración de IDs de sesión** frecuente para prevenir secuestro y fijación de sesión.
    * Control de **tiempo de inactividad de sesión** y verificación de **User Agent**.
* **Validación y Saneamiento de Entrada:** Todos los datos de entrada del usuario son **validados y saneados** rigurosamente antes de su procesamiento.
* **Control de Acceso (Autorización):** Los usuarios solo pueden acceder y modificar sus propios datos financieros.
* **Mensajes de Error Generales:** Mensajes de error de autenticación son genéricos para evitar la enumeración de usuarios.

---

## 🚀 Cómo Empezar

### Requisitos del Sistema

* **Servidor Web:** Apache (recomendado) o Nginx.
* **PHP:** Versión 7.4 o superior (con extensión `pdo_sqlite` habilitada).
* **Navegador Web Moderno:** Compatible con HTML5, CSS3 y JavaScript.

### Instalación y Configuración Local (con Laragon)

Laragon es un excelente entorno de desarrollo local que simplifica la configuración.

1.  **Clona el repositorio:** Abre tu terminal y navega a `C:\laragon\www\`. Luego, ejecuta:
    ```bash
    git clone [https://github.com/Gestordomestico/GestorDomestico.git](https://github.com/Gestordomestico/GestorDomestico.git)
    ```
    Esto creará la carpeta `GestorDomestico`.

2.  **Inicia Laragon:** Asegúrate de que Apache y MySQL estén corriendo en Laragon. Laragon creará automáticamente un Virtual Host (ej. `http://gestordomestico.test`) o podrás acceder vía `http://localhost:8096/GestorDomestico`.

3.  **Configura `config.php`:** Abre `C:\laragon\www\GestorDomestico\config.php`. Asegúrate de que `define('BASE_URL', '/GestorDomestico/public');` **coincida exactamente** con el nombre de tu carpeta de proyecto. Confirma que la carpeta `data` exista en la raíz del proyecto.

4.  **Inicializa la Base de Datos:**
    * **¡Importante!** Borra cualquier `gestordomestico.sqlite` existente en `data/`.
    * Abre tu navegador y ve a: `http://localhost:8096/GestorDomestico/init_db.php`. Deberías ver un mensaje de éxito.
    * **¡ALERTA DE SEGURIDAD!** Después de inicializar, **ELIMINA O RENOMBRA INMEDIATAMENTE `init_db.php`** de tu servidor.

5.  **Accede a la Aplicación:** Abre tu navegador y ve a `http://localhost:8096/GestorDomestico/public/login`. Haz un "Hard Refresh" (Ctrl+F5).

6.  **¡Regístrate y Disfruta!** Crea una nueva cuenta y comienza a gestionar tus finanzas.

---

## 📂 Estructura del Proyecto

El proyecto está organizado de forma clara y lógica para facilitar la navegación:

* **`GestorDomestico/`**: La raíz del proyecto.
    * **Archivos de Configuración y Seguridad**: `config.php`, `functions.php`, `init_db.php` (¡eliminar post-instalación!), `.gitignore`, `LICENSE`, `README.md`.
    * **`data/`**: Contiene el archivo de la base de datos SQLite (`gestordomestico.sqlite`).
    * **`public/`**: El directorio principal del servidor web, donde todo el tráfico es dirigido.
        * **`index.php`**: El "Front Controller" que maneja todas las peticiones y el enrutamiento.
        * **`api/`**: Contiene los **endpoints RESTful** para la comunicación frontend/backend.
            * `auth.php`: Autenticación (registro, login, logout).
            * `categories.php`: Gestión de categorías.
            * `reports.php`: Generación de resúmenes financieros.
            * `transactions.php`: Gestión de ingresos y gastos (CRUD).
        * **`css/`**: Archivos de estilos (`style.css`).
        * **`js/`**: Archivos JavaScript para la interactividad.
            * `app.js`: Lógica del dashboard (transacciones, categorías).
            * `auth.js`: Lógica de login y registro.
        * **`views/`**: Las vistas HTML.
            * `dashboard.html`: Panel de control principal.
            * `login.html`: Página de inicio de sesión.
            * `register.html`: Página de registro.

---

## 🛠️ Tecnologías Utilizadas

* **Backend:** PHP 7.4+, SQLite3 (con PDO).
* **Frontend:** HTML5, CSS3, JavaScript (Vanilla JS).
* **Servidor Web:** Apache (vía Laragon).

---

## 🗺️ Roadmap Futuro

Ideas para futuras mejoras:

* **Exportación de Datos:** CSV, PDF.
* **Reportes y Gráficos Avanzados:** Gráficos visuales de gastos/ingresos.
* **Presupuestos Mensuales:** Establecer límites de gasto por categoría.
* **Notificaciones:** Alertas sobre tendencias financieras.
* **Soporte Multi-Usuario:** Compartir cuentas entre familiares.
* **Internacionalización (i18n):** Múltiples idiomas y monedas.
* **Optimización y Rendimiento:** Mejora continua del código.

---

## 🤝 Contribuciones

¡Las contribuciones son bienvenidas para fines **académicos y de mejora del código**!

1.  Haz un "fork" del repositorio.
2.  Crea una nueva rama (`git checkout -b feature/tu-funcionalidad`).
3.  Implementa tus cambios (código limpio y comentado, con pruebas si es posible).
4.  Haz un "commit" descriptivo (`git commit -m 'feat: Descripción de la nueva funcionalidad'`).
5.  Envía tus cambios a tu "fork" (`git push origin feature/tu-funcionalidad`).
6.  Abre un "Pull Request" (PR) al repositorio original.

---

## 📄 Licencia

Este proyecto está bajo una **variante modificada de la Licencia MIT**.

* El uso del software está **estrictamente restringido a fines académicos, de investigación y personales**.
* **Queda expresamente prohibido el uso comercial** de este software por parte de cualquier persona o entidad que no sea el autor original.
* El autor original (`GestorDomestico`) **se reserva todos los derechos para el uso comercial** del Software. Cualquier uso comercial por parte de terceros requiere una licencia explícita y por escrito del autor.

Para obtener todos los detalles legales y términos específicos, consulta el archivo `LICENSE` en la raíz de este repositorio.

---

## ✉️ Contacto

Para preguntas, sugerencias o soporte, contáctanos a través de:

* **Correo Electrónico:** gestordomestico0@gmail.com
* **Repositorio GitHub:** [https://github.com/Gestordomestico/GestorDomestico](https://github.com/Gestordomestico/GestorDomestico) (abre un "Issue").

---
