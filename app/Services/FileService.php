<?php

namespace App\Services;

use App\Classes\Helpers;
use App\Models\TempFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Gif\Exceptions\NotReadableException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FileService
{
    // Define default image sizes if model doesn't specify
    protected array $defaultImageSizes = [
        'medium' => [600, 600],
//        'small' => [300, 300],
        'thumbnail' => [100, 100]
    ];

    protected string $disk = 'public';

    // Define allowed image types
    protected array $allowedImageTypes = [
        'image/jpg',    // JPEG
        'image/jpeg',   // JPEG
        'image/png',    // PNG
        'image/gif',    // GIF
        'image/webp',   // WebP
        'image/bmp',    // BMP
        'image/tiff',   // TIFF
        'image/svg+xml' // SVG
    ];


    public static function getResource($file)
    {
        if (!$file){
            return false;
        }

        return [
            'id' => $file->id,
            'name' => $file->name,
            'url' => $file->url,
            'mime_type' => $file->mime_type,
            'size' => $file->size,
            'created_at' => $file->created_at,
            'type' => $file->type ? : '',
            'sizes' => self::collectImageVariants($file),
        ];
    }


    public static function collectImageVariants($fileObj)
    {
        if (!$fileObj->sizes || count($fileObj->sizes) == 0) {
            return false;
        }

        $pathInfo = pathinfo($fileObj->url);

        $base = $pathInfo['dirname'] . '/' . $pathInfo['filename'];
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';

        $res = [];
        foreach ($fileObj->sizes as $size) {
            $res[$size] = "{$base}-{$size}{$extension}";
        }

        return $res;
    }


    private function generateFileName($file): string
    {
        // Get original filename without extension
        $fileName = pathinfo(strip_tags($file->getClientOriginalName()), PATHINFO_FILENAME);

        // Convert to a safe slug (removes spaces, special characters)
        $fileName = Str::slug($fileName);

        // If filename is too short, generate a random string
        if (strlen($fileName) < 5) {
            $fileName = Str::random(8);
        }

        // Append original file extension
        return $fileName . '.' . $file->getClientOriginalExtension();
    }


    private function imageUploadOperation($file,$parent)
    {
        $fileName = $this->generateFileName($file);

        $imageSizes = method_exists($parent, 'getImageSizes') ? $parent->getImageSizes() : $this->defaultImageSizes;

        $folderId = uniqid();

        // Store original image inside its unique folder
        $originalPath = $this->uploadPathNew($fileName,$parent,$folderId);

        $originalContent = $this->resizeImage($file, '1200', '1200');
//        $originalContent = file_get_contents($file);

        Storage::disk($this->disk)->put($originalPath, $originalContent, 'public');
        $originalFileUrl = Storage::disk($this->disk)->url($originalPath);

        // Store resized versions inside "conversions" subfolder
        foreach ($imageSizes as $size => [$width, $height]) {

            $currFileName = pathinfo($fileName, PATHINFO_FILENAME) . "-{$size}." . $file->getClientOriginalExtension();

            $currFilePath = $this->uploadPathNew($currFileName,$parent,$folderId);

            $sizeContent = $this->resizeImage($file, $width, $height);
            Storage::disk($this->disk)->put($currFilePath, $sizeContent, 'public');
        }

        $folderPath = $this->uploadPathNew('',$parent,$folderId);

        return [
            'file_name' => $fileName,
            'path' => $folderPath,
            'url' => $originalFileUrl,
            'sizes' => array_keys($imageSizes)
        ];
    }



    private function fileUploadOperation($file,$parent)
    {
        $fileName = $this->generateFileName($file);

        $folderId = uniqid();

        $newPath = $this->uploadPathNew($fileName,$parent,$folderId);
        $fileContent = file_get_contents($file);
        Storage::disk($this->disk)->put($newPath, $fileContent, 'public');
        $fileUrl = Storage::disk($this->disk)->url($newPath);

        $folderPath = $this->uploadPathNew('',$parent,$folderId);

        return [
            'file_name' => $fileName,
            'path' => $folderPath,
            'url' => $fileUrl,
        ];
    }



    public function storeFile($parent, $file, $type = false) {
        $mimeType = $file->getMimeType();

        $isImage = in_array($mimeType, $this->allowedImageTypes);

        if ($isImage) {
            $uploadRes = $this->imageUploadOperation($file,$parent);
        } else {
            $uploadRes = $this->fileUploadOperation($file,$parent);
        }

        // Determine the relation name dynamically
        $relationName = $type ?: 'files';

        // Check if the relation exists on the parent model
        if (!method_exists($parent, $relationName)) {
            throw new \Exception("Relation '{$relationName}' does not exist on " . get_class($parent));
        }

        // Create a new file record using the determined relation
        $fileData = $parent->{$relationName}()->create([
            'name' => $uploadRes['file_name'],
            'path' => $uploadRes['path'],
            'url' => $uploadRes['url'],
            'type' => $type ?: null,
            'size' => $file->getSize(),
            'mime_type' => $mimeType,
            'sizes' => @$uploadRes['sizes'] ?? null,
        ]);

        return $fileData;
    }



    //Move tmp file

    /**
     * @param $parent
     * @param $tmpFileId
     * @param $type
     * @return false|mixed
     * @throws \Exception
     */
    public function storeTmpFile($parent,$tmpFileId,$type = false)
    {

        $tmpFile = TempFile::where('id',(int)$tmpFileId)->first();

        if (!$tmpFile){
            throw new \Exception('Temp file not found');
        }

        // Determine the relation name dynamically
        $relationName = $type ? : 'files';

        // Check if the relation exists on the parent model
        if (!method_exists($parent, $relationName)) {
            throw new \Exception("Relation '{$relationName}' does not exist on " . get_class($parent));
        }


        $imageSizes = null;
        if (in_array($tmpFile->mime_type, $this->allowedImageTypes)){
            $imageSizes = method_exists($parent, 'getImageSizes') ? $parent->getImageSizes() : $this->defaultImageSizes;
            $imageSizes = array_keys($imageSizes);
        }

        // Create a new file record using the determined relation
        $fileData = $parent->{$relationName}()->create([
            'name' => $tmpFile->name,
            'path' => $tmpFile->path,
            'url' => $tmpFile->url,
            'type' => $type ?: null,
            'size' => $tmpFile->size,
            'mime_type' => $tmpFile->mime_type,
            'sizes' => $imageSizes,
        ]);

        $tmpFile->delete();

        return $fileData;
    }


    //Store tmp file
    public function uploadTmpFile($file,$parent){

        $mimeType = $file->getMimeType();

        $isImage = in_array($mimeType, $this->allowedImageTypes);

        if ($isImage) {
            $uploadRes = $this->imageUploadOperation($file,$parent);
        } else {
            $uploadRes = $this->fileUploadOperation($file,$parent);
        }

        $tmpFile = TempFile::create([
            'name' => $uploadRes['file_name'],
            'path' => $uploadRes['path'],
            'url' => $uploadRes['url'],
            'size' => $file->getSize(),
            'mime_type' => $mimeType,
            'sizes' => @$uploadRes['sizes'] ?? null,
        ]);

        return $tmpFile;
    }



    public function storeForwardedFile($parent,$fileUrl){

        $ch = curl_init($fileUrl);

        // Set options for cURL
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
        ]);

        // Execute the cURL session
        $imageData = curl_exec($ch);
        curl_close($ch);

        // Check if the request was successful
        if ($imageData !== false) {

            // Get the size of the image
            $size = strlen($imageData);

            // Get the mime type of the image
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($imageData);

            $fileName = uniqid() . '_' . trim(Str::slug(basename($fileUrl)));
            $newPath = $this->uploadPathNew($fileName,$parent);


            if(in_array($mime, $this->allowedImageTypes )){
                $fileContent = $this->resizeImage($imageData);
            }else{
                $fileContent = $imageData;
            }


            Storage::disk($this->disk)->put($newPath,$fileContent, 'public');

            $fileUrl = Storage::disk($this->disk)->url($newPath);

            $fileData = $parent->files()->create([
                'name' => $fileName,
                'path' => $newPath,
                'url' => $fileUrl,
                'size' => $size,
                'mime_type' => $mime,
            ]);

            return $fileData;

        } else {
            // Handle unsuccessful request
            return [
                'error' => 'Something went wrong while uploading the file.',
            ];
        }
    }



    public function resizeImage($savedObj, $width = 800, $height = 800)
    {
        $imageManager = new ImageManager(new Driver());

        try {
            $img = $imageManager->read($savedObj);
        } catch (NotReadableException $e) {
            throw new \Exception("The image could not be processed: " . $e->getMessage());
        }

        if ($img->height() > $height || $img->width() > $width) {
            $img = $img->scale(width: $width, height: $height);
        }

        return $img->encode();
    }


    public function resizeImageTwo($savedObj, $width = 800, $height = 800, $quality = 75)
    {
        $img = Image::make($savedObj);

        // Only resize if the image is larger than the defined dimensions
        if ($img->height() > $height || $img->width() > $width) {
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio(); // Maintain aspect ratio
                $constraint->upsize(); // Prevent upscaling
            });
        }

        // Convert PNG/JPEG to WebP for better compression (Optional)
        $format = $img->mime() === 'image/png' || $img->mime() === 'image/jpeg' ? 'webp' : $img->mime();

        // Compress the image and encode in WebP (or original format)
        $imgStream = $img->encode($format, $quality);

        return $imgStream->__toString();
    }


    public function uploadPathNew($filename,$parent,$folderId = false){

        $parentName = strtolower(class_basename($parent));

        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $folderId = $folderId ? : uniqid();

        return "uploads/{$parentName}/{$year}/{$month}/{$day}/{$folderId}/{$filename}";
    }


    public function deleteFile($file,$type = 'folder')
    {
        // Check if it's a file or folder and delete accordingly
        if ($type == 'folder') {
            $deleteRes = Storage::disk($this->disk)->deleteDirectory($file->path);  // For a folder
        }else{
            $deleteRes = Storage::disk($this->disk)->delete($file->path);
        }

        if (!$deleteRes){
            throw new \Exception('Cant delete file');
        }

        $file->delete();

        return $deleteRes;
    }


    public function tempFileUrl($fileName = false){
        $uniqueId = uniqid();

        return "tmp/uploads/{$uniqueId}/" . $fileName;
    }



}
