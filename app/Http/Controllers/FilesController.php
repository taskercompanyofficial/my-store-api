<?php

namespace App\Http\Controllers;

use App\Models\Files;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Increase PHP memory limit and execution time for large files
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 minutes

            $request->validate([
                'file' => 'required|file|mimes:jpg,jpeg,png,gif|max:10240', // Max 10MB, only images
                'file_name' => 'required|string',
                'file_description' => 'nullable|string',
                'folder' => 'nullable|string'
            ]);

            $file = $request->file('file');
            $folder = $request->folder ?? 'uploads';

            // Ensure upload directory exists and is writable
            $uploadPath = Storage::disk('public')->path($folder);
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Store file with custom filename
            $extension = $file->getClientOriginalExtension();
            $originalFilename = str_replace(' ', '_', $request->file_name);
            $folder = str_replace(' ', '_', $folder);
            $counter = 1;

            // Check if file exists and increment counter until unique name is found
            do {
                $filename = $counter === 1 ?
                    $originalFilename . '.' . $extension :
                    $originalFilename . '-' . $counter . '.' . $extension;

                $exists = Files::where('file_path', $folder . '/' . $filename)->exists();
                $counter++;
            } while ($exists);

            // Store the file
            $file_path = $file->storeAs($folder, $filename, 'public');

            if (!$file_path) {
                throw new Exception('Failed to store file');
            }

            $fileModel = Files::create([
                'file_name' => pathinfo($filename, PATHINFO_FILENAME),
                'file_path' => $file_path,
                'file_size' => $file->getSize(),
                'file_mime_type' => $file->getMimeType(),
                'file_type' => $file->getClientOriginalExtension(),
                'file_extension' => $file->getClientOriginalExtension(),
                'file_status' => 'active',
                'file_description' => $request->file_description
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'File uploaded successfully',
                'file' => $fileModel
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Files $files)
    {
        if (!$files) {
            return response()->json([
                'message' => 'File not found'
            ], 404);
        }

        return response()->json([
            'file' => $files
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Files $files)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Files $files)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Files $files)
    {
        //
    }
}
