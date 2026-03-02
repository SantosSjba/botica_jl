<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ConfiguracionController extends Controller
{
    /** Ruta del disco para el logo (storage/app/public/config) */
    public const LOGO_DISK_PATH = 'config';

    /**
     * Formulario de configuración (empresa, SUNAT, logo).
     * Siempre edita el registro idconfi = 1.
     */
    public function index(): View
    {
        $config = Configuracion::first();
        if (!$config) {
            abort(404, 'No existe registro de configuración.');
        }

        $logoUrl = $this->logoUrl($config->logo);

        return view('pages.configuracion.index', [
            'title' => 'Configuración',
            'config' => $config,
            'logoUrl' => $logoUrl,
        ]);
    }

    /**
     * Actualizar configuración.
     * Clave SOL: solo se actualiza si se envía un valor no vacío.
     */
    public function update(Request $request): RedirectResponse
    {
        $config = Configuracion::first();
        if (!$config) {
            abort(404, 'No existe registro de configuración.');
        }

        $rules = [
            'razon_social' => 'required|string|max:255',
            'nombre_comercial' => 'required|string|max:255',
            'ruc' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'departamento' => 'required|string|max:100',
            'provincia' => 'required|string|max:100',
            'distrito' => 'required|string|max:100',
            'ubigeo' => 'required|string|size:6',
            'impuesto' => 'required|numeric|min:0|max:100',
            'simbolo_moneda' => 'required|string|max:10',
            'usuario_sol' => 'required|string|max:50',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        $validated = $request->validate($rules, [], [
            'razon_social' => 'razón social',
            'nombre_comercial' => 'nombre comercial',
            'usuario_sol' => 'usuario SOL',
        ]);

        $claveSol = $request->input('clave_sol');
        $data = [
            'razon_social' => $validated['razon_social'],
            'nombre_comercial' => $validated['nombre_comercial'],
            'ruc' => $validated['ruc'],
            'direccion' => $validated['direccion'],
            'pais' => 'PE',
            'departamento' => $validated['departamento'],
            'provincia' => $validated['provincia'],
            'distrito' => $validated['distrito'],
            'ubigeo' => $validated['ubigeo'],
            'impuesto' => (float) $validated['impuesto'],
            'simbolo_moneda' => $validated['simbolo_moneda'],
            'usuario_sol' => $validated['usuario_sol'],
            'tipodoc' => $config->tipodoc ?? '6',
        ];

        if ($claveSol !== null && $claveSol !== '') {
            $data['clave_sol'] = $claveSol;
        }

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $name = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs(self::LOGO_DISK_PATH, $name, 'public');
            $data['logo'] = $path;

            if ($config->logo && Storage::disk('public')->exists($config->logo)) {
                Storage::disk('public')->delete($config->logo);
            }
        }

        $config->update($data);

        return $this->successRedirect(__('Configuración guardada correctamente.'), route('configuracion.index'));
    }

    /**
     * URL del logo: storage público o fallback por nombre (legacy: images/logo o foto/).
     */
    protected function logoUrl(?string $logo): ?string
    {
        if (!$logo) {
            return null;
        }
        if (Storage::disk('public')->exists($logo)) {
            return Storage::disk('public')->url($logo);
        }
        $name = str_contains($logo, '/') ? basename($logo) : $logo;
        if (file_exists(public_path('images/logo/' . $name))) {
            return asset('images/logo/' . $name);
        }
        if (file_exists(public_path('foto/' . $name))) {
            return asset('foto/' . $name);
        }
        return null;
    }
}
