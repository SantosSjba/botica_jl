# Lista completa de alertas / notificaciones en el proyecto

Todas las notificaciones al usuario deben mostrarse como **toast** usando `window.showToast(mensaje, tipo)`. Tipos: `success`, `error`, `warning`, `info`.

---

## 1. Backend: redirect con flash (success / error)

Estos mensajes se muestran al cargar la página siguiente (ya sea con `<x-ui.alert>` en la vista o como **toast** vía `layouts/app.blade.php` → `.flash-toast`).

### Caja
| Acción | Controlador | Mensaje | Tipo |
|--------|-------------|---------|------|
| Aperturar caja | `CajaController` | Caja aperturada correctamente. | success |
| Aperturar caja | `CajaController` | No puede aperturar otra caja mientras tenga una caja abierta. | error |
| Aperturar caja | `CajaController` | Ya tiene una caja abierta. | error |
| Cerrar caja | `CajaController` | Caja cerrada correctamente. | success |
| Cerrar caja | `CajaController` | No tiene una caja abierta. | error |
| Cerrar caja | `CajaController` | La caja a cerrar no coincide. Recargue la página. | error |
| Seguimiento caja | `CajaController` | No tiene una caja abierta. Debe aperturar caja primero. | error |
| Seguimiento caja | `CajaController` | No tiene permiso para ver el seguimiento de caja. | error |

### Ventas
| Acción | Controlador | Mensaje | Tipo |
|--------|-------------|---------|------|
| Vaciar carrito | `VentaController` | Carrito vaciado. | success |

### Compras
| Acción | Controlador | Mensaje | Tipo |
|--------|-------------|---------|------|
| Registrar compra | `ComprasController` | Compra registrada correctamente. | success |
| Registrar compra | `ComprasController` | Debe agregar al menos un producto al carrito... | error |
| Registrar compra | `ComprasController` | Error al registrar la compra. Intente de nuevo. | error |
| Vaciar carrito compras | `ComprasController` | Carrito vaciado. | success |

### Configuración
| Acción | Controlador | Mensaje | Tipo |
|--------|-------------|---------|------|
| Guardar configuración | `ConfiguracionController` | Configuración guardada correctamente. | success |

### Mantenimiento – Usuarios
| Acción | Controlador | Mensaje | Tipo |
|--------|-------------|---------|------|
| Crear usuario | `UsuarioController` | Registro grabado correctamente. | success |
| Actualizar usuario | `UsuarioController` | Registro actualizado correctamente. | success |
| Eliminar usuario | `UsuarioController` | Registro eliminado correctamente. | success |

### Mantenimiento – Síntomas
| Acción | Controlador | Mensaje | Tipo |
|--------|-------------|---------|------|
| Crear síntoma | `SintomaController` | Registro grabado correctamente. | success |
| Actualizar síntoma | `SintomaController` | Registro actualizado correctamente. | success |
| Eliminar síntoma | `SintomaController` | Registro eliminado correctamente. | success |
| Eliminar síntoma (no permitido) | `SintomaController` | mensaje dinámico del modelo | error |

### Mantenimiento – Lotes
| Acción | Controlador | Mensaje | Tipo |
|--------|-------------|---------|------|
| Crear lote | `LoteController` | Registro grabado correctamente. | success |
| Actualizar lote | `LoteController` | Registro actualizado correctamente. | success |
| Eliminar lote | `LoteController` | Registro eliminado correctamente. | success |

### Mantenimiento – Presentaciones
| Acción | Controlador | Mensaje | Tipo |
|--------|-------------|---------|------|
| Crear presentación | `PresentacionController` | Registro grabado correctamente. | success |
| Actualizar presentación | `PresentacionController` | Registro actualizado correctamente. | success |
| Eliminar presentación | `PresentacionController` | Registro eliminado correctamente. | success |

### Mantenimiento – Categorías (formas farmacéuticas)
| Acción | Controlador | Mensaje | Tipo |
|--------|-------------|---------|------|
| Crear categoría | `CategoriaController` | Registro grabado correctamente. | success |
| Actualizar categoría | `CategoriaController` | Registro actualizado correctamente. | success |
| Eliminar categoría | `CategoriaController` | Registro eliminado correctamente. | success |

### Mantenimiento – Productos
| Acción | Controlador | Mensaje | Tipo |
|--------|-------------|---------|------|
| Crear producto | `ProductoController` | Producto registrado correctamente. | success |
| Actualizar producto | `ProductoController` | Producto actualizado correctamente. | success |
| Eliminar producto | `ProductoController` | Producto eliminado correctamente. | success |

### Mantenimiento – Clientes
| Acción | Controlador | Mensaje | Tipo |
|--------|-------------|---------|------|
| Crear cliente | `ClienteController` | Registro grabado correctamente. | success |
| Actualizar cliente | `ClienteController` | Registro actualizado correctamente. | success |
| Eliminar cliente | `ClienteController` | Registro eliminado correctamente. | success |

### Middleware
| Acción | Archivo | Mensaje | Tipo |
|--------|---------|---------|------|
| Sin permiso admin | `EnsureAdministrador` | No tiene permiso para acceder a esta sección. | error |

---

## 2. Backend: respuestas JSON (AJAX) – mensajes que el front debe mostrar con toast

El front ya usa `showToast` donde corresponde; aquí se listan los orígenes por controlador.

### VentaController (punto de venta)
| Acción | Mensaje típico | Tipo |
|--------|----------------|------|
| Código barras vacío | Ingrese el código. | error |
| Agregar al carrito | (según servicio) | success / warning |
| Actualizar cantidad | Actualizado / La cantidad debe ser al menos 1. | success / error |
| Actualizar precio | Actualizado / ID inválido. | success / error |
| Quitar del carrito | Producto quitado del carrito. | success |
| Registrar venta | (message del servicio) / Error al registrar la venta. | success / error |
| Validación formulario | Seleccione forma de pago o agregue al menos un pago. | error |

### NotaCreditoController
| Acción | Mensaje típico | Tipo |
|--------|----------------|------|
| Sin serie/correlativo | Indique serie y correlativo de referencia. | error |
| Comprobante no encontrado | No se encontró comprobante... / Ese comprobante ya está anulado. | error |
| Registrar | Nota de crédito registrada. Comprobante de referencia anulado. | success |
| Registrar error | Error al registrar: ... | error |
| Otros | Venta no encontrada, No se puede emitir para ticket, etc. | error |

### ConsultaTicketsController (anular ticket)
| Acción | Mensaje típico | Tipo |
|--------|----------------|------|
| Anular | Ticket anulado. Stock devuelto. | success |
| Error | ID no válido, Venta no encontrada, Solo tickets T001, Ya anulado, Error al anular. | error |

### ComprasController (registrar compra – carrito)
| Acción | Mensaje típico | Tipo |
|--------|----------------|------|
| Agregar producto | El producto ya está en el carrito. | error |
| Ítem no encontrado | Ítem no encontrado. | error |

---

## 3. Vistas: dónde se muestran session('success') y session('error')

Todas estas páginas reciben el flash; el **toast** se dispara desde el layout (`.flash-toast`). Algunas además muestran `<x-ui.alert>` en la página (se puede unificar solo con toast si se desea).

| Vista | Muestra session success | Muestra session error |
|-------|-------------------------|------------------------|
| `layouts/app.blade.php` | Toast (flash-toast) | Toast (flash-toast) |
| `ventas/index.blade.php` | — (solo toast vía layout) | — |
| `ventas/nota-credito/index.blade.php` | x-ui.alert | x-ui.alert |
| `compras/create.blade.php` | x-ui.alert | x-ui.alert |
| `ventas/tickets/index.blade.php` | — | — |
| `configuracion/index.blade.php` | x-ui.alert | x-ui.alert |
| `caja/seguimiento.blade.php` | x-ui.alert | x-ui.alert |
| `caja/cierre.blade.php` | — | x-ui.alert |
| `caja/apertura.blade.php` | — | x-ui.alert |
| `mantenimiento/usuarios/index.blade.php` | x-ui.alert | x-ui.alert |
| `mantenimiento/usuarios/create.blade.php` | — | (solo $errors) |
| `mantenimiento/usuarios/edit.blade.php` | — | (solo $errors) |
| `mantenimiento/sintomas/index.blade.php` | x-ui.alert | x-ui.alert |
| `mantenimiento/sintomas/create.blade.php` | — | (solo $errors) |
| `mantenimiento/sintomas/edit.blade.php` | — | (solo $errors) |
| `mantenimiento/lotes/index.blade.php` | x-ui.alert | x-ui.alert |
| `mantenimiento/lotes/create.blade.php` | — | (solo $errors) |
| `mantenimiento/lotes/edit.blade.php` | — | (solo $errors) |
| `mantenimiento/presentaciones/index.blade.php` | x-ui.alert | x-ui.alert |
| `mantenimiento/presentaciones/create.blade.php` | — | (solo $errors) |
| `mantenimiento/presentaciones/edit.blade.php` | — | (solo $errors) |
| `mantenimiento/categorias/index.blade.php` | x-ui.alert | x-ui.alert |
| `mantenimiento/categorias/create.blade.php` | — | (solo $errors) |
| `mantenimiento/categorias/edit.blade.php` | — | (solo $errors) |
| `mantenimiento/productos/index.blade.php` | x-ui.alert | x-ui.alert |

---

## 4. Vistas: errores de validación ($errors)

Estas vistas muestran errores de validación con `<x-ui.alert variant="error">`:

- `configuracion/index.blade.php`
- `caja/apertura.blade.php`
- `mantenimiento/usuarios/create.blade.php`, `edit.blade.php`
- `mantenimiento/lotes/create.blade.php`, `edit.blade.php`
- `mantenimiento/sintomas/create.blade.php`, `edit.blade.php`
- `mantenimiento/presentaciones/create.blade.php`, `edit.blade.php`
- `mantenimiento/categorias/create.blade.php`, `edit.blade.php`

*(Opcional: mostrar también `$errors` como toast al cargar la página, además del bloque actual.)*

---

## 5. Resumen por tipo de operación

| Operación | Dónde hay alerta (backend o front) |
|-----------|-------------------------------------|
| **Registro (crear)** | Usuarios, Síntomas, Lotes, Presentaciones, Categorías, Productos, Clientes, Compra, Nota de crédito, Apertura caja, Configuración |
| **Actualización (editar)** | Usuarios, Síntomas, Lotes, Presentaciones, Categorías, Productos, Clientes, Configuración |
| **Eliminación** | Usuarios, Síntomas, Lotes, Presentaciones, Categorías, Productos, Clientes (con mensaje si no se puede eliminar) |
| **Venta** | Registrar venta (toast + abrir ticket), carrito (agregar, quitar, cantidad, precio), vaciar carrito |
| **Compras** | Registrar compra, agregar al carrito, vaciar carrito |
| **Caja** | Aperturar, cerrar, seguimiento (errores de estado/permiso) |
| **Nota de crédito** | Registrar (éxito/error vía JSON + toast) |
| **Tickets** | Anular ticket (éxito/error vía JSON + toast) |
| **Permisos** | Middleware administrador, seguimiento caja |

---

## 6. Cómo usar toast en nuevo código

```javascript
// En cualquier script (Alpine, fetch, etc.)
if (typeof window.showToast === 'function') {
    window.showToast('Registro guardado correctamente.', 'success');
    window.showToast('Error al actualizar.', 'error');
    window.showToast('Complete los campos requeridos.', 'warning');
    window.showToast('Información.', 'info');
}
```

**Implementación:** `resources/js/toast.js`, animación en `resources/css/app.css`, divs `.flash-toast` en `resources/views/layouts/app.blade.php`.
