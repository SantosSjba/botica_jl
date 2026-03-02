<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Models\TipoDocumento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    /** Columnas permitidas para ordenar (request => tabla.columna) */
    protected array $sortColumns = [
        'nombres' => 'cliente.nombres',
        'direccion' => 'cliente.direccion',
        'nrodoc' => 'cliente.nrodoc',
        'tipo' => 'cliente.tipo',
        'tipo_documento' => 'tipo_documento.descripcion',
    ];

    public function index(Request $request): View
    {
        $data = $this->getData($request);
        $ajax = $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        if ($ajax) {
            return view('pages.mantenimiento.clientes._tabla-clientes', $data);
        }

        return view('pages.mantenimiento.clientes.index', array_merge($data, [
            'title' => 'Cliente / Laboratorio',
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

        $query = Cliente::query()
            ->join('tipo_documento', 'cliente.id_tipo_docu', '=', 'tipo_documento.idtipo_docu')
            ->where('cliente.nombres', '<>', 'publico en general')
            ->select('cliente.*', 'tipo_documento.descripcion as tipo_doc_descripcion');

        if ($buscar !== '') {
            $term = '%' . trim($buscar) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('cliente.nombres', 'like', $term)
                    ->orWhere('cliente.direccion', 'like', $term)
                    ->orWhere('cliente.nrodoc', 'like', $term)
                    ->orWhere('cliente.tipo', 'like', $term)
                    ->orWhere('tipo_documento.descripcion', 'like', $term);
            });
        }

        $clientes = $query->orderBy($orderColumn, $direction)->paginate(15)->withQueryString();

        return [
            'clientes' => $clientes,
            'sort' => $sort,
            'direction' => $direction,
            'buscar' => $buscar,
        ];
    }

    public function show(Cliente $cliente): RedirectResponse
    {
        return redirect()->route('mantenimiento.clientes.edit', $cliente);
    }

    public function create(): View
    {
        $tiposDocumento = TipoDocumento::orderBy('idtipo_docu')->get();
        return view('pages.mantenimiento.clientes.create', [
            'title' => 'Nuevo Cliente / Laboratorio',
            'tiposDocumento' => $tiposDocumento,
        ]);
    }

    public function store(StoreClienteRequest $request): JsonResponse|RedirectResponse
    {
        Cliente::create($request->validated());
        return $this->successRedirect('Registro grabado correctamente.', route('mantenimiento.clientes.index'));
    }

    public function edit(Cliente $cliente): View
    {
        $tiposDocumento = TipoDocumento::orderBy('idtipo_docu')->get();
        return view('pages.mantenimiento.clientes.edit', [
            'title' => 'Editar Cliente / Laboratorio',
            'cliente' => $cliente,
            'tiposDocumento' => $tiposDocumento,
        ]);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): JsonResponse|RedirectResponse
    {
        $cliente->update($request->validated());
        return $this->successRedirect('Registro actualizado correctamente.', route('mantenimiento.clientes.index'));
    }

    public function destroy(Cliente $cliente): JsonResponse|RedirectResponse
    {
        $route = route('mantenimiento.clientes.index');
        if (!$cliente->puedeEliminar()) {
            return $this->errorRedirect($cliente->mensajeNoEliminable(), $route);
        }
        $cliente->delete();
        return $this->successRedirect('Registro eliminado correctamente.', $route);
    }
}
