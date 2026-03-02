<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePresentacionRequest;
use App\Http\Requests\UpdatePresentacionRequest;
use App\Models\Presentacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PresentacionController extends Controller
{
    protected array $sortColumns = [
        'presentacion' => 'presentacion.presentacion',
    ];

    public function index(Request $request): View
    {
        $data = $this->getData($request);
        $ajax = $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        if ($ajax) {
            return view('pages.mantenimiento.presentaciones._tabla-presentaciones', $data);
        }

        return view('pages.mantenimiento.presentaciones.index', array_merge($data, [
            'title' => 'Presentación',
        ]));
    }

    /** @return array<string, mixed> */
    protected function getData(Request $request): array
    {
        $buscar = $request->input('buscar', '');
        $sort = $request->input('sort', 'presentacion');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if (!array_key_exists($sort, $this->sortColumns)) {
            $sort = 'presentacion';
        }
        $orderColumn = $this->sortColumns[$sort];

        $query = Presentacion::query();
        if ($buscar !== '') {
            $term = '%' . trim($buscar) . '%';
            $query->where('presentacion', 'like', $term);
        }
        $presentaciones = $query->orderBy($orderColumn, $direction)->paginate(15)->withQueryString();

        return [
            'presentaciones' => $presentaciones,
            'sort' => $sort,
            'direction' => $direction,
            'buscar' => $buscar,
        ];
    }

    public function show(Presentacion $presentacion): RedirectResponse
    {
        return redirect()->route('mantenimiento.presentaciones.edit', $presentacion);
    }

    public function create(): View
    {
        return view('pages.mantenimiento.presentaciones.create', [
            'title' => 'Nueva Presentación',
        ]);
    }

    public function store(StorePresentacionRequest $request): JsonResponse|RedirectResponse
    {
        $data = array_merge($request->validated(), ['idsucu_c' => 1]);
        Presentacion::create($data);
        return $this->successRedirect('Registro grabado correctamente.', route('mantenimiento.presentaciones.index'));
    }

    public function edit(Presentacion $presentacion): View
    {
        return view('pages.mantenimiento.presentaciones.edit', [
            'title' => 'Editar Presentación',
            'presentacion' => $presentacion,
        ]);
    }

    public function update(UpdatePresentacionRequest $request, Presentacion $presentacion): JsonResponse|RedirectResponse
    {
        $presentacion->update($request->validated());
        return $this->successRedirect('Registro actualizado correctamente.', route('mantenimiento.presentaciones.index'));
    }

    public function destroy(Presentacion $presentacion): JsonResponse|RedirectResponse
    {
        $route = route('mantenimiento.presentaciones.index');
        if (!$presentacion->puedeEliminar()) {
            return $this->errorRedirect($presentacion->mensajeNoEliminable(), $route);
        }
        $presentacion->delete();
        return $this->successRedirect('Registro eliminado correctamente.', $route);
    }
}
