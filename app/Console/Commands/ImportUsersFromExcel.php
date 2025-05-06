<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportUserData;

class ImportUsersFromExcel extends Command
{
    protected $signature = 'import:users {file}';
    protected $description = 'Import user data from Excel file';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return;
        }

        $this->info("Import started for file: $filePath");

        Excel::import(new ImportUserData, $filePath);

        $this->info("Import completed.");
    }
}