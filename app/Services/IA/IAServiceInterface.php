<?php

namespace App\Services\IA;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;

interface IAServiceInterface
{
    /**
     * Extract transactions from an image file returning in a structured format.
     *
     * @param UploadedFile $file
     * @return array<ExtractedTransactionDTO>
     */
    public function extractTransactionsFromImage(UploadedFile $file): array;
}
