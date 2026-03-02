<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    /** Columnas permitidas para ordenar */
    protected array $sortColumns = [
        'forma_farmaceutica' => 'categoria.forma_farmaceutica',
        'ff_simplificada' => 'categoria.ff_simplificada',
    ];

    public function index(Request $request): View
    {
        $data = $this->getData($request);
        $ajax = $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        if ($ajax) {
            return view('pages.mantenimiento.categorias._tabla-categorias', $data);
        }

        return view('pages.mantenimiento.categorias.index', array_merge($data, [
            'title' => 'Forma farmacéutica',
        ]));
    }

    /** @return array<string, mixed> */
    protected function getData(Request $request): array
    {
        $buscar = $request->input('buscar', '');
        $sort = $request->input('sort', 'forma_farmaceutica');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        if (!array_key_exists($sort, $this->sortColumns)) {
            $sort = 'forma_farmaceutica';
        }
        $orderColumn = $this->sortColumns[$sort];

        $query = Categoria::query();

        if ($buscar !== '') {
            $term = '%' . trim($buscar) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('forma_farmaceutica', 'like', $term)
                    ->orWhere('ff_simplificada', 'like', $term);
            });
        }

        $categorias = $query->orderBy($orderColumn, $direction)->paginate(15)->withQueryString();

        return [
            'categorias' => $categorias,
            'sort' => $sort,
            'direction' => $direction,
            'buscar' => $buscar,
        ];
    }

    public function show(Categoria $categoria): RedirectResponse
    {
        return redirect()->route('mantenimiento.categorias.edit', $categoria);
    }

    public function create(): View
    {
        return view('pages.mantenimiento.categorias.create', [
            'title' => 'Nueva Forma farmacéutica',
        ]);
    }

    public function store(StoreCategoriaRequest $request): JsonResponse|RedirectResponse
    {
        Categoria::create($request->validated());
        return $this->successRedirect('Registro grabado correctamente.', route('mantenimiento.categorias.index'));
    }

    public function edit(Categoria $categoria): View
    {
        return view('pages.mantenimiento.categorias.edit', [
            'title' => 'Editar Forma farmacéutica',
            'categoria' => $categoria,
        ]);
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria): JsonResponse|RedirectResponse
    {
        $categoria->update($request->validated());
        return $this->successRedirect('Registro actualizado correctamente.', route('mantenimiento.categorias.index'));
    }

    public function destroy(Categoria $categoria): JsonResponse|RedirectResponse
    {
        $route = route('mantenimiento.categorias.index');
        if (!$categoria->puedeEliminar()) {
            return $this->errorRedirect($categoria->mensajeNoEliminable(), $route);
        }
        $categoria->delete();
        return $this->successRedirect('Registro eliminado correctamente.', $route);
    }
}
