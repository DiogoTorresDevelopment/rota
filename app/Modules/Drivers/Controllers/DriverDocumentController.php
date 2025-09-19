<?php

namespace App\Modules\Drivers\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Drivers\Services\DriverService;
use App\Modules\Drivers\Services\DriverDocumentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DriverDocumentController extends Controller
{
    public function __construct(
        private DriverService $driverService,
        private DriverDocumentService $documentService
    ) {}

    public function index(int $driverId): JsonResponse
    {
        $driver = $this->driverService->getDriverById($driverId);
        
        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Motorista não encontrado'
            ], 404);
        }

        $documents = $this->documentService->getDriverDocuments($driver);

        return response()->json([
            'success' => true,
            'documents' => $documents
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'files' => 'required|array',
                'files.*' => 'required|file|max:5120|mimes:pdf,png,jpg,jpeg', // 5MB max
                'driver_id' => 'required|exists:drivers,id'
            ]);

            $driver = $this->driverService->getDriverById($request->driver_id);
            
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Motorista não encontrado'
                ], 404);
            }

            $uploadedFiles = $this->documentService->uploadDocuments($driver, $request->file('files'));

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

    public function destroy(int $documentId): JsonResponse
    {
        try {
            $document = $this->documentService->getDocumentById($documentId);
            
            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento não encontrado'
                ], 404);
            }

            $this->documentService->deleteDocument($documentId);

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
}