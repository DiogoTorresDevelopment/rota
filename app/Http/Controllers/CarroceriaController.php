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
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
        ]);

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
        ]);

        $this->carroceriaService->update($carroceria, $validated);

        return redirect()->route('carrocerias.index');
    }

    public function destroy(Carroceria $carroceria)
    {
        $carroceria->delete();
        return response()->json(['success' => true]);
    }
}
