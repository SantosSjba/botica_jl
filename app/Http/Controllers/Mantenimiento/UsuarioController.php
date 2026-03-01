<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    protected array $sortColumns = [
        'nombres' => 'usuario.nombres',
        'usuario' => 'usuario.usuario',
        'telefono' => 'usuario.telefono',
        'tipo' => 'usuario.tipo',
        'estado' => 'usuario.estado',
        'fechaingreso' => 'usuario.fechaingreso',
    ];

    public function index(Request $request): View
    {
        $data = $this->getData($request);
        $ajax = $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        if ($ajax) {
            return view('pages.mantenimiento.usuarios._tabla-usuarios', $data);
        }

        return view('pages.mantenimiento.usuarios.index', array_merge($data, [
            'title' => 'Usuario',
        ]));
    }

    /** @return array<string, mixed> */
    protected function getData(Request $request): array
    {
        $buscar = $request->input('buscar', '');
        $sort = $request->input('sort', 'nombres');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if (!array_key_exists($sort, $this->sortColumns)) {
            $sort = 'nombres';
        }
        $orderColumn = $this->sortColumns[$sort];

        $query = Usuario::query();
        if ($buscar !== '') {
            $term = '%' . trim($buscar) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nombres', 'like', $term)
                    ->orWhere('usuario', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('tipo', 'like', $term)
                    ->orWhere('estado', 'like', $term);
            });
        }
        $usuarios = $query->orderBy($orderColumn, $direction)->paginate(15)->withQueryString();

        return [
            'usuarios' => $usuarios,
            'sort' => $sort,
            'direction' => $direction,
            'buscar' => $buscar,
        ];
    }

    public function show(Usuario $usuario): RedirectResponse
    {
        return redirect()->route('mantenimiento.usuarios.edit', $usuario);
    }

    public function create(): View
    {
        return view('pages.mantenimiento.usuarios.create', [
            'title' => 'Nuevo Usuario',
        ]);
    }

    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        Usuario::create($request->validated());
        return redirect()->route('mantenimiento.usuarios.index')->with('success', 'Registro grabado correctamente.');
    }

    public function edit(Usuario $usuario): View
    {
        return view('pages.mantenimiento.usuarios.edit', [
            'title' => 'Editar Usuario',
            'usuario' => $usuario,
        ]);
    }

    public function update(UpdateUsuarioRequest $request, Usuario $usuario): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['clave'])) {
            unset($data['clave']);
        }
        $usuario->update($data);
        return redirect()->route('mantenimiento.usuarios.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(Usuario $usuario): RedirectResponse
    {
        if (!$usuario->puedeEliminar()) {
            return redirect()->route('mantenimiento.usuarios.index')
                ->with('error', $usuario->mensajeNoEliminable());
        }
        $usuario->delete();
        return redirect()->route('mantenimiento.usuarios.index')->with('success', 'Registro eliminado correctamente.');
    }
}
