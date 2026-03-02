<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Roles del sistema (igual que el sistema antiguo)
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'ADMINISTRADOR' => 'ADMINISTRADOR',
        'USUARIO'       => 'USUARIO',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rutas solo para ADMINISTRADOR
    | Aplicar middleware 'rol.administrador' a estas rutas cuando existan.
    |--------------------------------------------------------------------------
    */
    'rutas_solo_administrador' => [
        // Mantenimiento (excepto clientes: ADMIN y USUARIO ven Clientes en el menú antiguo)
        'mantenimiento.productos.*',
        'mantenimiento.categorias.*',
        'mantenimiento.presentaciones.*',
        'mantenimiento.sintomas.*',
        'mantenimiento.lotes.*',
        'mantenimiento.usuarios.*',
        // Compras
        'compras.*',
        'consulta-compras',
        // Reportes
        'reportes.*',
        // Otros
        'backup',
        'configuracion',
        'acerca',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ventas: solo visible cuando el usuario tiene caja abierta
    |--------------------------------------------------------------------------
    */
    'ventas_requiere_caja_abierta' => true,

    /*
    |--------------------------------------------------------------------------
    | Caja: subítems según estado
    | - Apertura: cuando no hay caja abierta
    | - Cierre: cuando hay caja abierta
    | - Seguimiento: ADMINISTRADOR siempre; USUARIO cuando tiene caja abierta o cerrada (hoy)
    |--------------------------------------------------------------------------
    */
    'caja_apertura_solo_si_cerrada' => true,
    'caja_cierre_solo_si_abierta'   => true,
];
