<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FileController extends Controller
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
    public function uploadChunk(Request $request)
    {
        $chunk = $request->file('file');
        $chunkNumber = $request->input('chunk');
        $uploadIdentifier = $request->input('upload_identifier'); // Get upload identifier
        $filename = $chunk->getClientOriginalName() . '-' . $chunkNumber;

        // Store the chunk in temporary directory with upload identifier
        $chunk->storeAs('temp/' . $uploadIdentifier, $filename);

        return response()->json(['success' => true]);
    }

    public function assembleFile(Request $request)
    {
        $filename = $request->input('filename');
        $uploadIdentifier = $request->input('upload_identifier'); // Get upload identifier
        $chunksPath = storage_path('app/temp/' . $uploadIdentifier); // Use upload identifier in path
        $finalDirectory = public_path('uploads');
        $finalPath = $finalDirectory . '/' . $filename;

        // Ensure the uploads directory exists
        if (!file_exists($finalDirectory)) {
            mkdir($finalDirectory, 0755, true);
        }

        $chunkFiles = scandir($chunksPath);
        $chunkFiles = array_diff($chunkFiles, array('.', '..'));
        $finalFile = fopen($finalPath, 'ab');

        // Concatenate chunks to create the final file
        foreach ($chunkFiles as $chunkFile) {
            $chunkContent = file_get_contents($chunksPath . '/' . $chunkFile);
            fwrite($finalFile, $chunkContent);
        }
        fclose($finalFile);

        // Delete temporary chunk files
        foreach ($chunkFiles as $chunkFile) {
            unlink($chunksPath . '/' . $chunkFile);
        }

        // // Optionally, remove the temporary directory
        rmdir($chunksPath);

        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     */
    public function show(file $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(file $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, file $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(file $file)
    {
        //
    }
}
