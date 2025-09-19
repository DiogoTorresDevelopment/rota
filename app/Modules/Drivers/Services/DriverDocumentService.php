<?php

namespace App\Modules\Drivers\Services;

use App\Models\Driver;
use App\Models\DriverDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class DriverDocumentService
{
    public function getDriverDocuments(Driver $driver): Collection
    {
        return $driver->documents;
    }

    public function uploadDocuments(Driver $driver, array $files): array
    {
        $uploadedFiles = [];

        foreach ($files as $file) {
            $document = $this->uploadSingleDocument($driver, $file);
            $uploadedFiles[] = $document;
        }

        return $uploadedFiles;
    }

    public function uploadSingleDocument(Driver $driver, UploadedFile $file): DriverDocument
    {
        $path = $file->store('driver-documents', 'public');
        
        return $driver->documents()->create([
            'type' => $file->getClientOriginalExtension(),
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName()
        ]);
    }

    public function deleteDocument(int $documentId): bool
    {
        $document = DriverDocument::findOrFail($documentId);
        
        // Remove physical file
        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        return $document->delete();
    }

    public function getDocumentById(int $documentId): ?DriverDocument
    {
        return DriverDocument::find($documentId);
    }
}