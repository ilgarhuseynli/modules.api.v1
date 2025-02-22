<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    private const DEFAULT_MAX_SIZE = 51200; // // 50MB limit
    private const DEFAULT_DIMENSION = 5000; // pixels

    private array $validClasses = [
        'user' => User::class,
//        'order' => Order::class // Add actual class if exists
    ];

    public function store(Request $request,FileService $fileService)
    {
        set_time_limit(0);

        $this->validateUpload($request);

        $parent = $request->input('parent','user');
        $parentClass = $this->getParentClass($parent);

        if (!$parentClass ) {
            return response()->json(['message' => "Invalid type: {$parent}"], 402);
        }

        $file = $request->file('file');

        $tmpFile = $fileService->uploadTmpFile($file,$parentClass);

        return response()->json($tmpFile);
    }


    private function validateUpload(Request $request): void
    {
        $rules = [
            'file' => [
                'required',
                'file',
                'max:'.self::DEFAULT_MAX_SIZE,
            ],
        ];

        $mimes = [];

        //image
        $mimes[] = 'jpg,jpeg,png,gif,webp';

        //documents
        $mimes[] = 'pdf,doc,docx,xls,xlsx,csv';

        //video
        $mimes[] = 'mp4,mov,avi';

        //archive
//        $mimes[] = 'zip,rar,7z';

        $mimes = implode(',', $mimes);

        $rules['file'][] = 'mimes:'.$mimes;

        // Validate image dimensions if width or height is provided
        if ($request->hasAny(['width', 'height'])) {
            $rules['file'][] = sprintf(
                'image|dimensions:max_width=%s,max_height=%s',
                self::DEFAULT_DIMENSION,
                self::DEFAULT_DIMENSION
            );
        }

        $request->validate($rules);
    }


    private function getParentClass(string $parent): ?object
    {
        $classPath = $this->validClasses[$parent] ?? null;

        return $classPath && class_exists($classPath) ? new $classPath : null;
    }

}
