# PromoCatálogo · Catálogo de Productos en Promoción (PHP MVC)

Aplicación web desarrollada con arquitectura **Modelo-Vista-Controlador (MVC)**, validación con persistencia, autocompletado inteligente de ubicaciones internacionales y pipeline de Integración Continua con **Git** y **Jenkins**.

---

## 1. Requerimientos de Código y Funcionalidad

### A. Validación y Persistencia en Formulario de Inscripción
- **Persistencia sin pérdida de datos:** Al diligenciar el formulario de registro (`?r=registro`), si ocurre algún error de validación (backend o frontend), la información ingresada por el usuario **no se borra** (se mantiene gracias a variables de sesión flash y atributos `value="<?= $valor(...) ?>"`). Por motivos de ciberseguridad, únicamente los campos de contraseña se limpian.
- **Señalizaciones claras y específicas:** Cada campo con error recibe la clase visual `.is-invalid` (borde rojo y señalización clara) junto con un mensaje de retroalimentación específico en un contenedor `.invalid-feedback` contiguo al campo fallido.
- **Resumen superior accesible:** Se presenta un mensaje general de alerta indicando el número exacto de campos que requieren corrección y enfocando automáticamente el primer elemento erróneo.

### B. Autocompletado de País por Ciudad (Colombia, Italia y Francia)
- **Catálogo ampliado:** Incorpora un catálogo robusto de ciudades y regiones en `app/models/Ubicacion.php` cubriendo **Colombia**, **Italia** y **Francia** (con soporte para nombres en español y nativos, como *Roma*, *Milán/Milano*, *Florencia/Firenze*, *París*, *Marsella/Marseille*, *Bogotá*, *Medellín*, etc.).
- **Autocompletado automático:**
  - Al tipear o seleccionar una ciudad conocida (ej. "Roma"), el campo del país se autocompleta inmediatamente con "Italia" y se bloquea visualmente con un candado de confirmación.
  - El botón "Cambiar ciudad" permite desbloquear la selección rápidamente.
  - Normalización avanzada (insensible a tildes, mayúsculas y espacios múltiples).
  - Reglas de documento dinámicas adaptadas según el país seleccionado (Cédula de Ciudadanía en Colombia, Codice Fiscale en Italia, Carte d'Identité en Francia).

### C. Corrección en "Ver detalles" del Catálogo
- **Modal interactivo con información completa:** Se corrigió el botón "Ver detalles" del catálogo (`?r=catalogo`) para que al ser presionado despliegue un modal emergente accesible con la información detallada del producto:
  - Título, categoría y badge de porcentaje de descuento (-% OFF).
  - Descripción técnica extendida.
  - Comparativa de precio promocional vs. precio regular.
  - Ahorro económico exacto y porcentaje de beneficio.
  - Fechas de vigencia de la promoción (inicio y fin) y contador de días restantes.
  - Garantía del producto y botón de acción directa.

---

## 2. Requerimientos DevOps: Git y Jenkins

### A. Gestión de Versiones (Git)
El historial del repositorio está estructurado en commits semánticos, claros y modulares:
1. `refactor: estructurar proyecto bajo arquitectura MVC y Front Controller`
2. `feat(registro): implementar persistencia de datos y mensajes de error específicos por campo`
3. `feat(ubicacion): catálogo de ciudades de Colombia, Italia y Francia con autocompletado automático de país`
4. `fix(catalogo): corregir botón 'Ver detalles' desplegando modal con información detallada del producto`
5. `ci(devops): agregar Jenkinsfile declarativo y suite de pruebas automatizadas para integración continua`

**Modificación visible verificable:** En el pie de página de la aplicación y en las vistas principales se añadió el distintivo visual `v1.2.0 CI/CD Jenkins` y las banderas activas de Colombia, Italia y Francia.

### B. Integración Continua (Jenkins CI)
El proyecto incluye un pipeline declarativo (`Jenkinsfile`) que ejecuta automáticamente en cada commit / push:
1. **Checkout & Entorno:** Detección de versión de PHP y Git.
2. **Linting de Sintaxis:** Verificación con `php -l` de todos los archivos PHP.
3. **Pruebas Automatizadas:** Ejecución de `php tests/run_tests.php` que valida:
   - Autocompletado de ciudades (Roma -> Italia, Bogotá -> Colombia, París -> Francia).
   - Persistencia de campos tras fallos de validación.
   - Mensajes específicos de error por campo.
   - Cálculos matemáticos de descuentos y promociones.
   - Generación de reportes JUnit XML (`tests/junit.xml`).
4. **Auditoría de Seguridad:** Verificación de protección `.htaccess` y configuraciones.
5. **Empaquetado y Artefacto:** Generación del archivo `.zip` para distribución.

---

## 3. Instalación Local (XAMPP / CLI)

### Opción 1: Servidor Integrado de PHP
```bash
cd catalogo_mvc
php -S localhost:8000
```
Abre en tu navegador: <http://localhost:8000>

### Opción 2: XAMPP
1. Copia la carpeta del proyecto dentro de `C:\xampp\htdocs\catalogo-ap`.
2. Inicia Apache y MySQL en el Panel de Control de XAMPP.
3. Importa `sql/database.sql` en phpMyAdmin (<http://localhost/phpmyadmin>).
4. Accede a <http://localhost/catalogo-ap/>.

### Credenciales de Prueba Demo
| Rol | Correo | Contraseña | País | Ciudad |
|---|---|---|---|---|
| **Administrador** | `admin@catalogo.com` | `Admin123*` | Colombia | Cali |
| **Usuario** | `usuario@catalogo.com` | `Usuario123*` | Italia | Roma |
