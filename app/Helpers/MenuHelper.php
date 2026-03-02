<?php

namespace App\Helpers;

use App\Helpers\PermisosHelper;

class MenuHelper
{
    /**
     * Menú principal filtrado por rol y estado de caja (sistema antiguo).
     * - Inicio, Consultas: ambos roles.
     * - Mantenimiento: solo ADMINISTRADOR; USUARIO ve solo "Clientes" (un enlace).
     * - Ventas: solo si caja abierta.
     * - Caja: ambos; subítems según estado (Apertura/Cierre/Seguimiento).
     * - Compras, Reportes, Backup, Configuración: solo ADMINISTRADOR.
     */
    public static function getMainNavItems(): array
    {
        $enDesarrollo = route('en-desarrollo');
        $items = [];
        $routeCajaApertura = route('caja.apertura');
        $routeCajaCierre = route('caja.cierre');
        $routeCajaSeguimiento = route('caja.seguimiento');

        // Inicio — ambos
        $items[] = [
            'icon' => 'dashboard',
            'name' => 'Inicio',
            'path' => '/',
        ];

        // Consultas — ambos
        $items[] = [
            'name' => 'Consultas',
            'icon' => 'tables',
            'path' => '#',
            'subItems' => [
                ['name' => 'Productos farmacéuticos', 'path' => '/consulta/productos'],
            ],
        ];

        // Mantenimiento (admin) o Clientes (usuario)
        if (PermisosHelper::isAdministrador()) {
            $items[] = [
                'name' => 'Mantenimiento',
                'icon' => 'task',
                'path' => '#',
                'subItems' => [
                    ['name' => 'Cliente / Laboratorio', 'path' => route('mantenimiento.clientes.index')],
                    ['name' => 'Producto', 'path' => route('mantenimiento.productos.index')],
                    ['name' => 'Forma farmacéutica', 'path' => route('mantenimiento.categorias.index')],
                    ['name' => 'Presentación', 'path' => route('mantenimiento.presentaciones.index')],
                    ['name' => 'Usuario', 'path' => route('mantenimiento.usuarios.index')],
                    ['name' => 'Síntomas', 'path' => route('mantenimiento.sintomas.index')],
                    ['name' => 'Lote', 'path' => route('mantenimiento.lotes.index')],
                ],
            ];
        } else {
            $items[] = [
                'name' => 'Clientes',
                'icon' => 'user-profile',
                'path' => route('mantenimiento.clientes.index'),
            ];
        }

        // Ventas — solo si caja abierta
        if (PermisosHelper::puedeVerMenuVentas()) {
            $items[] = [
                'name' => 'Ventas',
                'icon' => 'ecommerce',
                'path' => '#',
                'subItems' => [
                    ['name' => 'Ventas', 'path' => route('ventas.index')],
                    ['name' => 'Consulta ventas', 'path' => $enDesarrollo],
                    ['name' => 'Consulta tickets', 'path' => $enDesarrollo],
                    ['name' => 'Nota de crédito', 'path' => $enDesarrollo],
                ],
            ];
        }

        // Caja — ambos; subítems según estado
        $cajaSubItems = [];
        if (PermisosHelper::puedeVerCajaApertura()) {
            $cajaSubItems[] = ['name' => 'Apertura de caja', 'path' => $routeCajaApertura];
        }
        if (PermisosHelper::puedeVerCajaCierre()) {
            $cajaSubItems[] = ['name' => 'Cierre de caja', 'path' => $routeCajaCierre];
        }
        if (PermisosHelper::puedeVerCajaSeguimiento()) {
            $cajaSubItems[] = ['name' => 'Seguimiento de caja', 'path' => $routeCajaSeguimiento];
        }
        if (!empty($cajaSubItems)) {
            $items[] = [
                'name' => 'Caja',
                'icon' => 'support-ticket',
                'path' => '#',
                'subItems' => $cajaSubItems,
            ];
        }

        // Compras, Reportes, Backup, Configuración — solo ADMINISTRADOR
        if (PermisosHelper::isAdministrador()) {
            $items[] = [
                'name' => 'Compras',
                'icon' => 'ecommerce',
                'path' => '#',
                'subItems' => [
                    ['name' => 'Compras', 'path' => route('compras.create')],
                    ['name' => 'Consulta compras', 'path' => route('compras.consulta.index')],
                ],
            ];
            $items[] = [
                'name' => 'Reportes',
                'icon' => 'charts',
                'path' => '#',
                'subItems' => [
                    ['name' => 'Rpt. Ventas', 'path' => route('reportes.ventas.rango')],
                    ['name' => 'Rpt. Ventas del día', 'path' => route('reportes.ventas.dia')],
                    ['name' => 'Rpt. Compras', 'path' => route('reportes.compras.rango')],
                    ['name' => 'Rpt. Compras del día', 'path' => route('reportes.compras.dia')],
                ],
            ];
            $items[] = ['icon' => 'task', 'name' => 'Backup', 'path' => route('backup.index')];
            $items[] = ['name' => 'Configuración', 'icon' => 'ui-elements', 'path' => route('configuracion.index')];
        }

        return $items;
    }

    public static function getOthersItems()
    {
        return [
            [
                'icon' => 'user-profile',
                'name' => 'Perfil',
                'path' => '/profile',
            ],
        ];
    }

    public static function getMenuGroups()
    {
        return [
            [
                'title' => 'Sistema Farmacia',
                'items' => self::getMainNavItems(),
            ],
            [
                'title' => 'Cuenta',
                'items' => self::getOthersItems(),
            ],
        ];
    }

    public static function isActive($path)
    {
        return request()->is(ltrim($path, '/'));
    }

    public static function getIconSvg($iconName)
    {
        $icons = [
            'dashboard' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z" fill="currentColor"></path></svg>',

            'ecommerce' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.31641 4H3.49696C4.24468 4 4.87822 4.55068 4.98234 5.29112L5.13429 6.37161M5.13429 6.37161L6.23641 14.2089C6.34053 14.9493 6.97407 15.5 7.72179 15.5L17.0833 15.5C17.6803 15.5 18.2205 15.146 18.4587 14.5986L21.126 8.47023C21.5572 7.4795 20.8312 6.37161 19.7507 6.37161H5.13429Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M7.7832 19.5H7.7932M16.3203 19.5H16.3303" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>',

            'user-profile' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM17.0246 18.8566V18.8455C17.0246 16.7744 15.3457 15.0955 13.2746 15.0955H10.7246C8.65354 15.0955 6.97461 16.7744 6.97461 18.8455V18.856C8.38223 19.8895 10.1198 20.5 12 20.5C13.8798 20.5 15.6171 19.8898 17.0246 18.8566ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12ZM11.9991 7.25C10.8847 7.25 9.98126 8.15342 9.98126 9.26784C9.98126 10.3823 10.8847 11.2857 11.9991 11.2857C13.1135 11.2857 14.0169 10.3823 14.0169 9.26784C14.0169 8.15342 13.1135 7.25 11.9991 7.25ZM8.48126 9.26784C8.48126 7.32499 10.0563 5.75 11.9991 5.75C13.9419 5.75 15.5169 7.32499 15.5169 9.26784C15.5169 11.2107 13.9419 12.7857 11.9991 12.7857C10.0563 12.7857 8.48126 11.2107 8.48126 9.26784Z" fill="currentColor"></path></svg>',

            'task' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.75586 5.50098C7.75586 5.08676 8.09165 4.75098 8.50586 4.75098H18.4985C18.9127 4.75098 19.2485 5.08676 19.2485 5.50098L19.2485 15.4956C19.2485 15.9098 18.9127 16.2456 18.4985 16.2456H8.50586C8.09165 16.2456 7.75586 15.9098 7.75586 15.4956V5.50098ZM8.50586 3.25098C7.26322 3.25098 6.25586 4.25834 6.25586 5.50098V6.26318H5.50195C4.25931 6.26318 3.25195 7.27054 3.25195 8.51318V18.4995C3.25195 19.7422 4.25931 20.7495 5.50195 20.7495H15.4883C16.7309 20.7495 17.7383 19.7421 17.7383 18.4995L17.7383 17.7456H18.4985C19.7411 17.7456 20.7485 16.7382 20.7485 15.4956L20.7485 5.50097C20.7485 4.25833 19.7411 3.25098 18.4985 3.25098H8.50586ZM16.2383 17.7456H8.50586C7.26322 17.7456 6.25586 16.7382 6.25586 15.4956V7.76318H5.50195C5.08774 7.76318 4.75195 8.09897 4.75195 8.51318V18.4995C4.75195 18.9137 5.08774 19.2495 5.50195 19.2495H15.4883C15.9025 19.2495 16.2383 18.9137 16.2383 18.4995L16.2383 17.7456Z" fill="currentColor"></path></svg>',

            'tables' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 5.5C3.25 4.25736 4.25736 3.25 5.5 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V18.5C20.75 19.7426 19.7426 20.75 18.5 20.75H5.5C4.25736 20.75 3.25 19.7426 3.25 18.5V5.5ZM5.5 4.75C5.08579 4.75 4.75 5.08579 4.75 5.5V8.58325L19.25 8.58325V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H5.5ZM19.25 10.0833H15.416V13.9165H19.25V10.0833ZM13.916 10.0833L10.083 10.0833V13.9165L13.916 13.9165V10.0833ZM8.58301 10.0833H4.75V13.9165H8.58301V10.0833ZM4.75 18.5V15.4165H8.58301V19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5ZM10.083 19.25V15.4165L13.916 15.4165V19.25H10.083ZM15.416 19.25V15.4165H19.25V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15.416Z" fill="currentColor"></path></svg>',

            'charts' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.00002 12.0957C4.00002 7.67742 7.58174 4.0957 12 4.0957C16.4183 4.0957 20 7.67742 20 12.0957C20 16.514 16.4183 20.0957 12 20.0957H5.06068L6.34317 18.8132C6.48382 18.6726 6.56284 18.4818 6.56284 18.2829C6.56284 18.084 6.48382 17.8932 6.34317 17.7526C4.89463 16.304 4.00002 14.305 4.00002 12.0957ZM12 2.5957C6.75332 2.5957 2.50002 6.849 2.50002 12.0957C2.50002 14.4488 3.35633 16.603 4.77303 18.262L2.71969 20.3154C2.50519 20.5299 2.44103 20.8525 2.55711 21.1327C2.6732 21.413 2.94668 21.5957 3.25002 21.5957H12C17.2467 21.5957 21.5 17.3424 21.5 12.0957C21.5 6.849 17.2467 2.5957 12 2.5957Z" fill="currentColor"></path></svg>',

            'ui-elements' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.665 3.75618C11.8762 3.65061 12.1247 3.65061 12.3358 3.75618L18.7807 6.97853L12.3358 10.2009C12.1247 10.3064 11.8762 10.3064 11.665 10.2009L5.22014 6.97853L11.665 3.75618ZM4.29297 8.19199V16.0946C4.29297 16.3787 4.45347 16.6384 4.70757 16.7654L11.25 20.0365V11.6512C11.1631 11.6205 11.0777 11.5843 10.9942 11.5425L4.29297 8.19199ZM12.75 20.037L19.2933 16.7654C19.5474 16.6384 19.7079 16.3787 19.7079 16.0946V8.19199L13.0066 11.5425C12.9229 11.5844 12.8372 11.6207 12.75 11.6515V20.037Z" fill="currentColor"></path></svg>',

            'pages' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.50391 4.25C8.50391 3.83579 8.83969 3.5 9.25391 3.5H15.2777C15.4766 3.5 15.6674 3.57902 15.8081 3.71967L18.2807 6.19234C18.4214 6.333 18.5004 6.52376 18.5004 6.72268V16.75C18.5004 17.1642 18.1646 17.5 17.7504 17.5H16.248V17.4993H14.748V17.5H9.25391C8.83969 17.5 8.50391 17.1642 8.50391 16.75V4.25Z" fill="currentColor"></path></svg>',

            'support-ticket' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 17.0518V12C20 7.58174 16.4183 4 12 4C7.58168 4 3.99994 7.58174 3.99994 12V17.0518M19.9998 14.041V19.75C19.9998 20.5784 19.3282 21.25 18.4998 21.25H13.9998M6.5 18.75H5.5C4.67157 18.75 4 18.0784 4 17.25V13.75C4 12.9216 4.67157 12.25 5.5 12.25H6.5C7.32843 12.25 8 12.9216 8 13.75V17.25C8 18.0784 7.32843 18.75 6.5 18.75Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>',
        ];

        return $icons[$iconName] ?? '<svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/></svg>';
    }
}
