# Reglas del proyecto: Sistema Farmacia (Laravel + TailAdmin)

Este documento define las reglas para **migrar** el Sistema Farmacia original (PHP procedural + MySQL) a **Laravel 12** con la plantilla **TailAdmin**, usando la base de datos existente, y **mejorando** seguridad, estructura y mantenibilidad. Se debe **reutilizar** lógica y criterios de negocio del código original cuando sea posible.

---

## 1. Contexto del proyecto

- **Código original:** `SISTEMA FARMACIA` (PHP con `clsConexion`, mysqli, sesiones, módulos por carpetas).
- **Destino:** `tailadmin-sistema-farmacia-migrado` (Laravel 12, Tailwind v4, Alpine.js, Blade).
- **Base de datos:** La misma que usa el sistema actual (p. ej. `factosys_boticajl`). No crear una BD nueva desde cero; **generar migraciones desde el esquema existente** y luego evolucionar con migraciones nuevas.
- **Objetivo:** Migración + mejora (seguridad, estándares Laravel, UX con TailAdmin).

---

## 2. Base de datos y migraciones

### 2.1 Generar migraciones desde la BD existente

- Usar el paquete **kitloong/laravel-migrations-generator** para obtener migraciones a partir del esquema actual:
  ```bash
  composer require --dev kitloong/laravel-migrations-generator
  php artisan migrate:generate
  ```
- Opciones útiles:
  - Tablas concretas: `--tables="venta,detalleventa,productos,cliente,..."`
  - Excluir tablas: `--ignore="carrito,carritoc"` si se van a reemplazar por sesión/caché.
- **No** ejecutar `migrate` contra la BD de producción hasta tener un entorno de pruebas; en desarrollo, se puede apuntar a una copia de la BD.

### 2.2 Después de generar

- Revisar migraciones generadas: nombres de tablas, tipos, índices y claves foráneas.
- Mantener nombres de tablas y columnas del sistema original para no romper datos ni integridad (p. ej. `venta`, `detalleventa`, `productos`, `cliente`, `caja_apertura`, `configuracion`, `usuario`, etc.).
- Nuevos cambios de esquema (columnas, índices, tablas nuevas) deben hacerse **siempre** con nuevas migraciones Laravel, no editando la BD a mano.
- Documentar en comentarios de migraciones cualquier decisión (p. ej. por qué se excluye una tabla o se añade un índice).

### 2.3 Credenciales

- **Nunca** commitear credenciales. Usar solo `.env` (y opcionalmente `config/database.php` leyendo de `env()`).
- El sistema original usa `conexion/db_config.php`; en Laravel todo debe venir de `DB_*` en `.env`.

---

## 3. Mapeo de módulos (original → Laravel)

Reutilizar la **lógica de negocio** del código original; reimplementar en Laravel con Eloquent, servicios y controladores.

| Original (SISTEMA FARMACIA) | Laravel (tailadmin) | Notas |
|-----------------------------|---------------------|--------|
| `venta/`                    | Módulo Ventas (controllers, services) | Transacciones, carrito → sesión o BD temporal, IGV, SUNAT |
| `compras/`                  | Módulo Compras      | Carrito compras, detallecompra, compra |
| `producto/`                 | Productos (CRUD + búsqueda) | Stock, stock mínimo, categoría, presentación, lote, laboratorio (cliente tipo laboratorio) |
| `cliente/`                  | Clientes/Laboratorios | tipo: cliente \| laboratorio, tipo doc, reutilizar validaciones |
| `usuario/`                  | Auth + usuarios      | Migrar a Laravel Auth; reemplazar MD5 por bcrypt (ver Mejoras) |
| `caja/`                     | Caja (apertura/cierre/movimientos) | caja_apertura, caja_cierre |
| `categoria/`, `presentacion/`, `laboratorio` (cliente), `lote/`, `sintomas/` | Catálogos (modelos + CRUD simple) | Reutilizar listados y relaciones del original |
| `reportes/`                 | Reportes (ventas, compras, caja) | Laravel + vistas TailAdmin, export PDF/Excel si aplica |
| `notacredito/`              | Notas de crédito    | Relación con venta, serie, SUNAT |
| `configuracion/`            | Configuración (empresa, SUNAT, logo) | configuracion (tabla), .env para secretos |
| `certificado/`              | Certificado SUNAT   | Almacenamiento seguro (storage), no en BD como archivo |
| `backup/`                   | Backups             | Comandos Artisan + jobs, no credenciales en BD sin cifrar |

---

## 4. Reutilización de código

- **Lógica de negocio:** Extraer cálculos (IGV, totales, descuentos, redondeo) del PHP original a **clases de servicio** (p. ej. `App\Services\VentaService`, `App\Services\IgvService`) o a **value objects**. No copiar/pegar SQL crudo; traducir a Eloquent o Query Builder.
- **Reglas de validación:** Las mismas que en el sistema original (tipos de documento, series, estados de venta, etc.) deben reflejarse en **Form Requests** y en reglas de negocio en servicios.
- **Constantes y enumerados:** Definir en `config/` o en clases (p. ej. tipos de comprobante, estados de venta, tipo cliente/laboratorio) y usar en todo el proyecto.
- **No reutilizar:** `clsConexion`, `db_config.php`, `seguridad.php` ni includes procedurales; reemplazar por Eloquent, Auth de Laravel y middleware.

---

## 5. Mejoras obligatorias (seguridad y estándares)

- **Contraseñas:** El sistema original usa MD5. En Laravel usar solo `Hash::make()` y `Hash::check()` (bcrypt). Planear migración de claves existentes (p. ej. primer login forzado para cambiar contraseña o script único de hash).
- **Autenticación:** Laravel Auth (session o Sanctum si se necesita API). Proteger todas las rutas de panel con `auth` y, si aplica, roles (p. ej. administrador, cajero).
- **CSRF:** Todos los formularios con `@csrf`; no desactivar protección CSRF global.
- **SQL:** Solo Eloquent o Query Builder con bindings; nada de concatenación de SQL con input del usuario.
- **XSS:** Escapar salida en Blade con `{{ }}`; usar `{!! !!}` solo cuando el contenido sea seguro y controlado.
- **Configuración y secretos:** SUNAT, BD, claves SOL, etc., desde `.env` y `config/`, nunca hardcodeados.

---

## 6. Estructura Laravel y TailAdmin

- **Controladores:** Por recurso (VentaController, ProductoController, ClienteController, etc.). Acciones pesadas (guardar venta, anular, enviar SUNAT) en **servicios** llamados desde el controlador.
- **Modelos Eloquent:** Un modelo por tabla principal; relaciones según el esquema (Venta → DetalleVenta, Producto → Categoria, Presentacion, Lote, Cliente [laboratorio], etc.). Usar nombres de tabla existentes con `$table` si no siguen convención Laravel (p. ej. `productos`, `caja_apertura`).
- **Vistas:** Blade en `resources/views/`, usando componentes y layouts de TailAdmin (sidebar, cabecera, tarjetas, tablas, formularios). Mantener Tailwind v4 y Alpine.js según la plantilla.
- **Rutas:** Agrupar por módulo en `routes/web.php`; usar nombres y, si aplica, `Route::resource`. Rutas de API (si se usan para carrito o búsquedas) en `routes/api.php` con Sanctum si hay autenticación.
- **Idioma:** Interfaz en **español** (etiquetas, mensajes, validación). Usar `lang/es/` y `__()`.

---

## 7. Loader en acciones

- **Toda acción** que provoque una petición al servidor (enviar formulario, guardar, eliminar, etc.) debe mostrar un **estado de carga** (loader) para indicar que la acción está en curso.
- **Implementación:** Usar el componente reutilizable `<x-ui.button-loader>` en botones de envío/acción. El loader va **dentro del botón** (spinner + texto "Cargando...").
- **Uso:** El formulario o contenedor debe tener `x-data="{ loading: false }"` y en el evento de envío `@submit="loading = true"` (o el evento que corresponda). El componente muestra el spinner y deshabilita el botón mientras `loading` sea true.
- **Ejemplo:** Ver `resources/views/pages/auth/signin.blade.php`. Para otras acciones (por ejemplo AJAX), exponer una variable `loading` en el scope Alpine y pasarla al botón o usar el mismo patrón.

### 7.1 Actualización de datos sin recargar página (AJAX)

- En **consultas y listados** (tablas con búsqueda, orden y paginación), la búsqueda y la navegación (orden por columna, paginación) deben **actualizar solo los datos** (el bloque de la tabla), no recargar toda la página (F5).
- **Patrón:** El controlador detecta peticiones AJAX (`$request->ajax()` o cabecera `X-Requested-With: XMLHttpRequest`) y devuelve solo el fragmento de vista con la tabla y la paginación (un partial). La vista principal incluye ese partial dentro de un contenedor con id; un script hace `fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })`, reemplaza el `innerHTML` del contenedor con la respuesta y actualiza la URL con `history.pushState` para que el estado quede reflejado en el navegador.
- **Referencia:** Consulta de productos farmacéuticos: `ConsultaProductosController`, vista `pages/consulta/productos.blade.php`, partial `_tabla-productos.blade.php` y script en `@push('scripts')`.

## 8. Convenciones de código

- **PHP:** PSR-12. Type hints en métodos y propiedades donde sea posible.
- **Nombres:** Inglés para código (clases, métodos, variables); español para textos al usuario y comentarios de negocio si se desea.
- **Blade:** Componentes reutilizables para formularios de alta/baja de ítems (carrito venta/compra), tablas de listado y modales; evitar duplicar bloques grandes de HTML.
- **Tests:** Al menos pruebas básicas para servicios críticos (cálculo de IGV, totales, descuentos) y para flujos de venta/compra si es posible.

---

## 9. Resumen de pasos recomendados

1. Configurar `.env` con la BD existente (o copia) y probar conexión.
2. Instalar y ejecutar `migrate:generate`; revisar y guardar migraciones en control de versiones.
3. Crear modelos Eloquent para las tablas principales y relaciones.
4. Implementar autenticación Laravel (login con tabla `usuario` o migrar a `users` con script); eliminar dependencia de MD5.
5. Migrar módulos en este orden sugerido: catálogos (categoría, presentación, lote, sintoma, tipo_documento, etc.) → Productos → Clientes → Caja → Ventas → Compras → Notas de crédito → Reportes → Configuración y certificados.
6. En cada módulo: reutilizar reglas de negocio del PHP original, exponer en servicios y controladores, y construir vistas con TailAdmin.

---

## 10. Referencias rápidas

- **Generar migraciones desde BD existente:** [kitloong/laravel-migrations-generator](https://github.com/kitloong/laravel-migrations-generator) — `composer require --dev kitloong/laravel-migrations-generator` y `php artisan migrate:generate`.
- **Mejoras ya identificadas en el sistema original:** Ver `SISTEMA FARMACIA/MEJORAS_SISTEMA.md` (SQL injection, XSS, transacciones, contraseñas, CSRF).
- **Esquema de BD de referencia:** `SISTEMA FARMACIA/clinaxkk_farmacia.sql` (tablas y datos de ejemplo).
- **TailAdmin Laravel:** Documentación y estructura en el README del proyecto y en [TailAdmin](https://tailadmin.com/docs).

---

*Estas reglas deben aplicarse en todo el desarrollo y en las respuestas de IA que trabajen en este repositorio.*
