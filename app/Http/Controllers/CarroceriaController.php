<?php

namespace App\Http\Controllers;

use App\Models\Carroceria;
use App\Services\CarroceriaService;
use Illuminate\Http\Request;

class CarroceriaController extends Controller
{
    protected $carroceriaService;

    public function __construct(CarroceriaService $carroceriaService)
    {
        $this->carroceriaService = $carroceriaService;
    }

    public function index()
    {
        $carrocerias = Carroceria::all();
        return view('carrocerias.index', compact('carrocerias'));
    }

    public function create()
    {
        return view('carrocerias.create');
    }

    public function store(Request $request)
    {

        $input = $request->all();
        // Normaliza o campo peso_suportado para float
        if (isset($input['peso_suportado'])) {
            $input['peso_suportado'] = str_replace(['.', ','], ['', '.'], $input['peso_suportado']);
            $input['peso_suportado'] = floatval($input['peso_suportado']);
        }
        $validated = \Validator::make($input, [
            'descricao' => 'required|string|max:255',
            'chassi' => 'required|string|max:255|unique:carrocerias,chassi',
            'placa' => 'required|string|max:8|unique:carrocerias,placa',
            'peso_suportado' => 'required|numeric',
            'status' => 'required|boolean'
        ]);
        ])->validate();

        $carroceria = $this->carroceriaService->store($validated);

        return redirect()->route('carrocerias.index');
    }

    public function edit(Carroceria $carroceria)
    {
        return view('carrocerias.edit', compact('carroceria'));
    }

    public function update(Request $request, Carroceria $carroceria)
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'chassi' => 'required|string|max:255|unique:carrocerias,chassi,' . $carroceria->id,
            'placa' => 'required|string|max:8|unique:carrocerias,placa,' . $carroceria->id,
            'peso_suportado' => 'required|numeric',
            'status' => 'required|boolean'
        ]);

        $this->carroceriaService->update($carroceria, $validated);

        return redirect()->route('carrocerias.index');
    }

    public function destroy(Carroceria $carroceria)
    {
        $carroceria->delete();
        return response()->json(['success' => true]);
    }

    public function show(Carroceria $carroceria)
    {
        return view('carrocerias.show', compact('carroceria'));
    }
}
