<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoteRequest;
use App\Http\Requests\UpdateLoteRequest;
use App\Models\Lote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoteController extends Controller
{
    protected array $sortColumns = [
        'numero' => 'lote.numero',
        'fecha_vencimiento' => 'lote.fecha_vencimiento',
    ];

    public function index(Request $request): View
    {
        $data = $this->getData($request);
        $ajax = $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        if ($ajax) {
            return view('pages.mantenimiento.lotes._tabla-lotes', $data);
        }

        return view('pages.mantenimiento.lotes.index', array_merge($data, [
            'title' => 'Lote',
        ]));
    }

    /** @return array<string, mixed> */
    protected function getData(Request $request): array
    {
        $buscar = $request->input('buscar', '');
        $sort = $request->input('sort', 'numero');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if (!array_key_exists($sort, $this->sortColumns)) {
            $sort = 'numero';
        }
        $orderColumn = $this->sortColumns[$sort];

        $query = Lote::query();
        if ($buscar !== '') {
            $term = '%' . trim($buscar) . '%';
            $query->where('numero', 'like', $term);
        }
        $lotes = $query->orderBy($orderColumn, $direction)->paginate(15)->withQueryString();

        return [
            'lotes' => $lotes,
            'sort' => $sort,
            'direction' => $direction,
            'buscar' => $buscar,
        ];
    }

    public function show(Lote $lote): RedirectResponse
    {
        return redirect()->route('mantenimiento.lotes.edit', $lote);
    }

    public function create(): View
    {
        return view('pages.mantenimiento.lotes.create', [
            'title' => 'Nuevo Lote',
        ]);
    }

    public function store(StoreLoteRequest $request): RedirectResponse
    {
        $data = array_merge($request->validated(), ['idsucu_c' => 1]);
        Lote::create($data);
        return redirect()->route('mantenimiento.lotes.index')->with('success', 'Registro grabado correctamente.');
    }

    public function edit(Lote $lote): View
    {
        return view('pages.mantenimiento.lotes.edit', [
            'title' => 'Editar Lote',
            'lote' => $lote,
        ]);
    }

    public function update(UpdateLoteRequest $request, Lote $lote): RedirectResponse
    {
        $lote->update($request->validated());
        return redirect()->route('mantenimiento.lotes.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(Lote $lote): RedirectResponse
    {
        if (!$lote->puedeEliminar()) {
            return redirect()->route('mantenimiento.lotes.index')
                ->with('error', $lote->mensajeNoEliminable());
        }
        $lote->delete();
        return redirect()->route('mantenimiento.lotes.index')->with('success', 'Registro eliminado correctamente.');
    }
}
