<?php

namespace App\Console\Commands;

use App\Models\TempFile;
use App\Services\FileService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DeleteTmpFiles extends Command
{
    protected $signature = 'tmpfiles:clear';
    protected $description = "clear temporary files from the tmp_files table";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(FileService $fileService)
    {
        // Define the expiration threshold (1 hour ago)
        $oneHourAgo = Carbon::now()->subHour();

        // Delete payment requests created more than 1 hour ago
        $files = TempFile::where('created_at', '<', $oneHourAgo)
            ->take(100)->get();

        $deletedCount = count($files);

        foreach ($files as $file){

            try {
                $fileService->deleteFile($file);

            }catch (\Exception $exception){
                $this->info("Error while delete file:".$exception->getMessage());
            }
        }

        if ($deletedCount > 0){
            // Output the result
            $this->info("Deleted files count: $deletedCount");
        }else{
            $this->info("Files not found");
        }
    }
}
