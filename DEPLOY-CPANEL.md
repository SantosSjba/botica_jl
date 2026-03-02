# Despliegue en cPanel – Laravel + Vite

El build de Vite en este proyecto **no** genera una carpeta `dist` en la raíz. Los archivos compilados se generan en **`public/build/`**. Eso es lo que debes subir ya listo.

---

## 1. Preparar el build en tu PC (antes de subir)

1. Abre terminal en la **raíz del proyecto** (donde están `package.json`, `vite.config.js`, la carpeta `app/`, etc.).
2. Ejecuta:

```bash
npm install
npm run build
```

**En PowerShell (Windows)** si `&&` da error, usa punto y coma:
```powershell
npm install; npm run build
```

3. Revisa que exista la carpeta **`public/build/`** (no se llama `dist`). Dentro deberías ver:
   - `manifest.json`
   - carpeta `assets/` con archivos `.js` y `.css`

Si el build falla: revisa que tengas Node.js instalado (`node -v` y `npm -v`) y que no haya errores en rojo en la salida.

Eso crea (o actualiza) la carpeta **`public/build/`** con algo como:

- `manifest.json`
- `assets/app-XXXXX.js`
- `assets/app-XXXXX.css`

**No subas el proyecto a cPanel sin haber ejecutado `npm run build`.** Si no existe `public/build/`, los estilos y el JavaScript no cargarán en producción.

---

## 2. Qué subir a cPanel

Sube **todo el proyecto** (incluyendo `public/build/` ya generado), por ejemplo:

- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `public/`  ← **debe incluir la carpeta `public/build/`**
- `resources/`
- `routes/`
- `storage/`
- `vendor/` (o instálalo en el servidor con Composer)
- `.env` (crear/editar en el servidor con los datos de producción)
- `artisan`
- `composer.json`
- `composer.lock`

**No hace falta subir:**

- `node_modules/`
- `.env.example` (opcional)
- Carpeta `dist/` si existiera (en este proyecto no se usa)

---

## 3. Configuración en cPanel

1. **Document root:** que apunte a la carpeta **`public`** del proyecto, no a la raíz.  
   Ejemplo: si el proyecto está en `tu-dominio.com/sistema-farmacia`, el document root debe ser `sistema-farmacia/public`.

2. **PHP:** versión 8.1 o superior (recomendado 8.2+).

3. **`.env` en el servidor:**
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://tu-dominio.com`
   - Base de datos, correo, etc.

4. **Permisos:**  
   - `storage/` y `bootstrap/cache/` escribibles (755 o 775 según el servidor).

5. **Composer (si no subiste `vendor/`):**  
   Si tienes SSH en cPanel:
   ```bash
   cd /home/usuario/ruta-del-proyecto
   composer install --optimize-autoloader --no-dev
   ```

6. **Laravel (opcional pero recomendado):**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## 4. Resumen

| Paso | Acción |
|------|--------|
| En tu PC | `npm run build` → se genera **`public/build/`** |
| Subir | Todo el proyecto **con** `public/build/` incluido |
| cPanel | Document root = carpeta **`public`** |
| Servidor | `.env` de producción, permisos, `composer install` si aplica |

El “dist” listo para producción en este proyecto es la carpeta **`public/build/`**; no hay que subir una carpeta llamada `dist`.

**Nota:** `public/build/` está en `.gitignore`, así que si despliegas por Git no se sube. En ese caso: genera el build en tu PC (`npm run build`), crea un ZIP del proyecto (incluyendo `public/build/`) y súbelo por cPanel, o sube solo la carpeta `public/build/` después de clonar.
