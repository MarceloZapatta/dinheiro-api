<?php

namespace App\Services\IA;

class ExtractedTransactionDTO
{
    public function __construct(
        public string $description,
        public float $value,
        public string $date,
        public ?int $installmentNumber = null,
        public ?int $totalInstallments = null,
        public ?string $refnum = null
    ) {}
}
