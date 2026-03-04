# Migración: fecha_emision de venta (DATE → DATETIME)

Esta guía explica cómo ejecutar la migración que cambia la columna `venta.fecha_emision` de **DATE** a **DATETIME** para guardar también la hora de emisión de cada venta, **sin perder datos**.

---

## ¿Qué hace la migración?

- **Antes:** `venta.fecha_emision` era tipo `date` (solo día, por eso en reportes salía siempre `00:00`).
- **Después:** pasa a `datetime`; los registros existentes se convierten a `YYYY-MM-DD 00:00:00` y las nuevas ventas guardan fecha y hora real.

**No se pierden datos:** MySQL convierte cada fecha existente al mismo día con hora `00:00:00`.

---

## Cómo ejecutar sin perder datos

### 1. Hacer backup de la base de datos

**Obligatorio** antes de correr migraciones en producción.

```bash
# Ejemplo MySQL/MariaDB (ajuste usuario, contraseña y nombre de BD)
mysqldump -u USUARIO -p NOMBRE_BD > backup_antes_fecha_emision_$(date +%Y%m%d_%H%M).sql
```

En Windows (PowerShell):

```powershell
mysqldump -u USUARIO -p NOMBRE_BD > backup_antes_fecha_emision_$(Get-Date -Format "yyyyMMdd_HHmm").sql
```

Sustituya `USUARIO` y `NOMBRE_BD` por los valores de su `.env` (`DB_USERNAME`, `DB_DATABASE`).

---

### 2. Revisar que la tabla venta existe

La migración solo altera la tabla si existe y tiene la columna `fecha_emision`. Si su BD viene del dump `factosys_boticajl.sql`, la tabla ya existe.

---

### 3. Ejecutar las migraciones

Desde la raíz del proyecto (tailadmin-sistema-farmacia-migrado):

```bash
php artisan migrate
```

Para ejecutar **solo** esta migración:

```bash
php artisan migrate --path=database/migrations/2026_03_04_000000_venta_fecha_emision_datetime.php
```

---

### 4. Comprobar después de migrar

En MySQL/MariaDB:

```sql
SHOW COLUMNS FROM venta LIKE 'fecha_emision';
```

Debe aparecer tipo `datetime`.

Comprobar que los datos siguen ahí:

```sql
SELECT idventa, fecha_emision FROM venta LIMIT 5;
```

Los valores antiguos deben verse como `YYYY-MM-DD 00:00:00`.

---

## Si algo sale mal (rollback)

Solo si es necesario revertir:

```bash
php artisan migrate:rollback --step=1
```

**Atención:** al revertir, la columna vuelve a `date` y la parte **hora** se pierde (quedan solo fechas con 00:00). Por eso es importante tener el backup antes de migrar.

---

## Resumen

| Paso | Acción |
|------|--------|
| 1 | Backup de la BD |
| 2 | `php artisan migrate` (o solo la migración indicada) |
| 3 | Verificar tipo y datos con `SHOW COLUMNS` y `SELECT` |

Después de esto, las nuevas ventas guardarán fecha y hora y en los reportes de ventas se mostrará la hora real de emisión.
