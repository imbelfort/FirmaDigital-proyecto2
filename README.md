# GUIA

### 1. REQUISITOS PREVIOS (Descargar e Instalar):
- Git (https://git-scm.com/)
- PHP 8.2+ (extensiones `pdo_pgsql`, `openssl`, `fileinfo` activas en php.ini)
- Composer (https://getcomposer.org/)
- Node.js 18+ (https://nodejs.org/)
- PostgreSQL 14+ (crear base de datos llamada: `db_grupo26sa`)

---

### 2. CLONAR E INGRESAR AL PROYECTO:
```bash
git clone url del github
cd FirmaDigital
```

---

### 3. INSTALAR DEPENDENCIAS:
```bash
composer install
npm install
```

---

### 4. CONFIGURAR VARIABLES DE ENTORNO (.env):
```bash
copy .env.example .env
php artisan key:generate
```

Abre el archivo `.env` y ajusta las credenciales de PostgreSQL:
```ini
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=db_grupo26sa
DB_USERNAME=postgres
DB_PASSWORD=tu_password_aqui
```

---

### 5. BASE DE DATOS Y COMPILACIÓN:
```bash
php artisan migrate --seed
npm run build
```

---

### 6. EJECUTAR EL PROYECTO:
En la primera terminal (Backend):
```bash
php artisan serve
```

En la segunda terminal (Frontend Vite):
```bash
npm run dev
```

Acceder en el navegador:
`http://127.0.0.1:8000`

---

### USUARIOS DE PRUEBA:
- **Admin General:** Registro `1000` | Contraseña `1111111`
- **Encargado (Mesa de Entrada):** Registro `1002` | Contraseña `2345678`
- **Vicerrector:** Registro `1001` | Contraseña `1234567`
- **Estudiante (Alumno):** Registro `219064748` | Contraseña `4567890`
- **Docente:** Registro `1045` | Contraseña `3456789`
- **Seguimiento Público con PIN:** `PIN-9482`
