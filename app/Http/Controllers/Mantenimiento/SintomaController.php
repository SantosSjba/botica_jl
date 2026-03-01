<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSintomaRequest;
use App\Http\Requests\UpdateSintomaRequest;
use App\Models\Sintoma;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SintomaController extends Controller
{
    protected array $sortColumns = [
        'sintoma' => 'sintoma.sintoma',
    ];

    public function index(Request $request): View
    {
        $data = $this->getData($request);
        $ajax = $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest';
        if ($ajax) {
            return view('pages.mantenimiento.sintomas._tabla-sintomas', $data);
        }
        return view('pages.mantenimiento.sintomas.index', array_merge($data, ['title' => 'Síntomas']));
    }

    protected function getData(Request $request): array
    {
        $buscar = $request->input('buscar', '');
        $sort = $request->input('sort', 'sintoma');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if (!array_key_exists($sort, $this->sortColumns)) {
            $sort = 'sintoma';
        }
        $query = Sintoma::query();
        if ($buscar !== '') {
            $query->where('sintoma', 'like', '%' . trim($buscar) . '%');
        }
        $sintomas = $query->orderBy($this->sortColumns[$sort], $direction)->paginate(15)->withQueryString();
        return ['sintomas' => $sintomas, 'sort' => $sort, 'direction' => $direction, 'buscar' => $buscar];
    }

    public function show(Sintoma $sintoma): RedirectResponse
    {
        return redirect()->route('mantenimiento.sintomas.edit', $sintoma);
    }

    public function create(): View
    {
        return view('pages.mantenimiento.sintomas.create', ['title' => 'Nuevo Síntoma']);
    }

    public function store(StoreSintomaRequest $request): RedirectResponse
    {
        Sintoma::create(array_merge($request->validated(), ['idsucu_c' => 1]));
        return redirect()->route('mantenimiento.sintomas.index')->with('success', 'Registro grabado correctamente.');
    }

    public function edit(Sintoma $sintoma): View
    {
        return view('pages.mantenimiento.sintomas.edit', ['title' => 'Editar Síntoma', 'sintoma' => $sintoma]);
    }

    public function update(UpdateSintomaRequest $request, Sintoma $sintoma): RedirectResponse
    {
        $sintoma->update($request->validated());
        return redirect()->route('mantenimiento.sintomas.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(Sintoma $sintoma): RedirectResponse
    {
        if (!$sintoma->puedeEliminar()) {
            return redirect()->route('mantenimiento.sintomas.index')->with('error', $sintoma->mensajeNoEliminable());
        }
        $sintoma->delete();
        return redirect()->route('mantenimiento.sintomas.index')->with('success', 'Registro eliminado correctamente.');
    }
}
