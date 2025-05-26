<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\DriverService;
use App\Http\Requests\DriverRequest;
use App\Models\DriverDocument;
use Illuminate\Support\Str;

class DriverController extends Controller
{
    protected $driverService;

    public function __construct(DriverService $driverService)
    {
        $this->driverService = $driverService;
    }

    public function index()
    {
        $drivers = Driver::all();
        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(DriverRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Gera uma senha aleatória se não for fornecida
            if (!isset($data['password'])) {
                $data['password'] = Str::random(8);
            }
            
            $driver = $this->driverService->store($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Dados básicos salvos com sucesso!',
                'driver' => $driver,
                'password' => $data['password'] // Retorna a senha gerada
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar os dados',
                'errors' => ['general' => [$e->getMessage()]]
            ], 422);
        }
    }

    public function edit(Driver $driver)
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'cpf' => 'required|string|unique:drivers,cpf,'.$driver->id,
                'phone' => 'required|string',
                'email' => 'required|email|unique:drivers,email,'.$driver->id,
                'status' => 'boolean',
                'cep' => 'required|string',
                'state' => 'required|string',
                'city' => 'required|string',
                'street' => 'required|string',
                'number' => 'required|string',
                'district' => 'required|string',
                'password' => 'nullable|string|min:6',
            ]);

            // Se uma nova senha foi fornecida, atualiza
            if (isset($validated['password'])) {
                $driver->password = $validated['password'];
            }

            $driver->update($validated);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $type => $file) {
                    // Remove documento antigo se existir
                    $oldDoc = $driver->documents()->where('type', $type)->first();
                    if ($oldDoc) {
                        Storage::delete($oldDoc->file_path);
                        $oldDoc->delete();
                    }

                    // Salva novo documento
                    $path = $file->store('driver-documents');
                    $driver->documents()->create([
                        'type' => $type,
                        'file_path' => $path
                    ]);
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Motorista atualizado com sucesso!',
                    'driver' => $driver
                ]);
            }

            return redirect()->route('drivers.index')
                ->with('success', 'Motorista atualizado com sucesso!');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar os dados',
                    'errors' => ['general' => [$e->getMessage()]]
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar os dados: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            \Log::info('Iniciando exclusão do motorista: ' . $id);
            
            // Busca o motorista incluindo registros soft deleted
            $driver = Driver::withTrashed()->find($id);
            
            if (!$driver) {
                \Log::warning('Motorista não encontrado: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Motorista não encontrado.'
                ], 404);
            }
            
            // Remove documentos
            foreach ($driver->documents as $document) {
                Storage::delete($document->file_path);
            }
            
            // Usa o método delete() do SoftDeletes
            $driver->delete();
            
            \Log::info('Motorista excluído com sucesso: ' . $id);
            
            return response()->json([
                'success' => true,
                'message' => 'Motorista excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir motorista: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir motorista: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeDocument(Request $request)
    {
        try {
            $request->validate([
                'files' => 'required|array',
                'files.*' => 'required|file|max:5120|mimes:pdf,png,jpg,jpeg', // 5MB max
                'driver_id' => 'required|exists:drivers,id'
            ]);

            $driver = Driver::findOrFail($request->driver_id);
            $uploadedFiles = [];

            foreach ($request->file('files') as $file) {
                $path = $file->store('driver-documents', 'public'); // Especifica o disco 'public'
                
                $document = $driver->documents()->create([
                    'type' => $file->getClientOriginalExtension(),
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ]);

                $uploadedFiles[] = $document;
            }

            return response()->json([
                'success' => true,
                'message' => 'Documentos enviados com sucesso!',
                'documents' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar documentos: ' . $e->getMessage()
            ], 422);
        }
    }

    public function getDocuments(Driver $driver)
    {
        return response()->json([
            'success' => true,
            'documents' => $driver->documents
        ]);
    }

    public function deleteDocument($id)
    {
        try {
            $document = DriverDocument::findOrFail($id);
            
            // Remove o arquivo físico
            if (Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }
            
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Documento excluído com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir documento: ' . $e->getMessage()
            ], 422);
        }
    }

    public function apiProfile(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => 'driver',
                    'driver' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'status' => $user->status
                    ]
                ]
            ]
        ]);
    }

    public function apiUpdateProfile(Request $request)
    {
        $driver = $request->user()->driver;
        
        if (!$driver) {
            return response()->json([
                'message' => 'Perfil de motorista não encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'address' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:100',
            'state' => 'sometimes|required|string|max:2',
            'zip_code' => 'sometimes|required|string|max:9',
        ]);

        $driver->update($validated);

        return response()->json([
            'message' => 'Perfil atualizado com sucesso',
            'driver' => $driver->fresh()
        ]);
    }
} 