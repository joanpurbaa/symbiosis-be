<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
  public function index(Request $request)
  {
    $user = $request->user();

    if ($user->isAdmin()) {
      $documents = Document::with('user')->latest()->get();
    } else {
      $documents = Document::where('user_id', $user->id)->latest()->get();
    }

    return response()->json([
      'documents' => $documents
    ]);
  }

  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'document_type' => 'required|string|max:255',
      'file' => 'required|file|max:5120', // 5MB max
    ]);

    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->errors()
      ], 422);
    }

    $file = $request->file('file');
    $filePath = $file->store('documents', 'public');

    $document = Document::create([
      'name' => $request->name,
      'file_path' => $filePath,
      'file_type' => $file->getClientOriginalExtension(),
      'document_type' => $request->document_type,
      'size' => $this->formatFileSize($file->getSize()),
      'upload_date' => now()->toDateString(),
      'status' => 'completed',
      'user_id' => $request->user()->id,
    ]);

    return response()->json([
      'message' => 'Document uploaded successfully',
      'document' => $document->load('user')
    ], 201);
  }

  public function update(Request $request, $id)
  {
    $document = Document::findOrFail($id);

    // Check if user owns the document or is admin
    if ($request->user()->isUser() && $document->user_id !== $request->user()->id) {
      return response()->json([
        'message' => 'Unauthorized'
      ], 403);
    }

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'document_type' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->errors()
      ], 422);
    }

    $document->update([
      'name' => $request->name,
      'document_type' => $request->document_type,
    ]);

    return response()->json([
      'message' => 'Document updated successfully',
      'document' => $document->load('user')
    ]);
  }

  public function destroy(Request $request, $id)
  {
    $document = Document::findOrFail($id);

    // Check if user owns the document or is admin
    if ($request->user()->isUser() && $document->user_id !== $request->user()->id) {
      return response()->json([
        'message' => 'Unauthorized'
      ], 403);
    }

    // Delete file from storage
    Storage::disk('public')->delete($document->file_path);

    $document->delete();

    return response()->json([
      'message' => 'Document deleted successfully'
    ]);
  }

  public function download($id)
  {
    $document = Document::findOrFail($id);
    $filePath = storage_path('app/public/' . $document->file_path);

    if (!file_exists($filePath)) {
      return response()->json([
        'message' => 'File not found'
      ], 404);
    }

    return response()->download($filePath, $document->name);
  }

  private function formatFileSize($bytes)
  {
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes / 1024, 2) . ' KB';
    return round($bytes / 1048576, 2) . ' MB';
  }
}
