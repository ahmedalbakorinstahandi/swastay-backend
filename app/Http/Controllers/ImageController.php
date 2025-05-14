<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use App\Services\ImageService;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'folder' => 'required|string|in:users,listings',
        ]);

        $imageName = ImageService::storeImage($request->image, $request->folder);

        return ResponseService::response([
            'success' => true,
            'data' => [
                'image_name' => $imageName,
                'image_url' => asset('storage/' . $imageName),
            ],
            'message' => 'messages.image.uploaded',
            'status' => 201,
        ]);
    }

    // file upload
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:20480',
            'folder' => 'required|string|in:users,listings',
        ]);

        $fileName = FileService::storeFile($request->file, $request->folder);

        return ResponseService::response([
            'success' => true,
            'data' => [
                'file_name' => $fileName,
                'file_url' => asset('storage/' . $fileName),
            ],
            'message' => 'messages.file.uploaded',
            'status' => 201,
        ]);
    }
}
