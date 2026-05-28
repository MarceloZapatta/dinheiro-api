<?php

namespace App\Services\IA;

use App\Exceptions\IA\ExtractTransactionsFromImage;
use Gemini;
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class GeminiIAService implements IAServiceInterface
{
    public function extractTransactionsFromImage(UploadedFile $file): array
    {
        $inputText = mb_convert_encoding('Extract all transactions from the image.
                    Return JSON only.
                    Schema:
                    [
                        {
                            "date": "YYYY-MM-DD",
                            "value": number,
                            "description": string,
                            "invoice_number_current": number|null,
                            "invoice_number_total": number|null
                        }
                    ]
                    Rules:
                    - If invoice appears as "X/Y", map X to invoice_number_current and Y to invoice_number_total.
                    - If invoice appears as "Parcela X de Y", map X to invoice_number_current and Y to invoice_number_total.
                    - If the year of the transaction is missing, use YYYY at the current year, example: "YYYY-03-23".
                    - If invoice number is missing, use null.
                    - Do not include any text outside the JSON.
                    - Do not return with json markdown "```json" or any other markdown formatting, return only the JSON content as plain text minified.
                    - Do not invent data', 'UTF-8');

        $geminiAiApiKey = config('gemini.ai_api_key');

        $client = Gemini::client($geminiAiApiKey);

        $generateContentResponse = $client->generativeModel(config('gemini.generative_model'))
            ->generateContent([
                $inputText,
                new Blob(
                    mimeType: MimeType::tryFrom($file->getMimeType()) ?? MimeType::IMAGE_PNG,
                    data: base64_encode($file->getContent())
                )
            ]);

        $textResponse = $generateContentResponse->text();
        Log::debug('GeminiIAService - Resposta bruta da IA:', ['response' => $textResponse]);

        if (empty($textResponse)) {
            throw new ExtractTransactionsFromImage();
        }

        $jsonDecodedTransactions = json_decode($textResponse, true);
        Log::debug('GeminiIAService - Transações decodificadas da resposta da IA:', ['transactions' => $jsonDecodedTransactions]);

        if (empty($jsonDecodedTransactions) || !is_array($jsonDecodedTransactions)) {
            return [];
        }

        $extractedTransactions = [];

        foreach ($jsonDecodedTransactions as $transaction) {
            if ($this->validateExtractedTransaction($transaction) === false) {
                Log::error('Invalid transaction', ['transaction' => $transaction]);
                throw new ExtractTransactionsFromImage();
            }

            $extractedTransactions[] = new ExtractedTransactionDTO(
                description: $this->truncateBigDescriptions($transaction['description']),
                value: $this->formatCorrectValue($transaction['value']),
                date: $this->formatCorrectDateCurrentYear($transaction['date']),
                installmentNumber: $this->formatCorrectInvoiceNumber($transaction['invoice_number_current'] ?? null),
                totalInstallments: $this->formatCorrectInvoiceNumber($transaction['invoice_number_total'] ?? null)
            );
            Log::debug('GeminiIAService - Transação formatada e validada:', ['transaction' => end($extractedTransactions)]);
        }

        return $extractedTransactions;
    }

    private function validateExtractedTransaction(array $transaction): bool
    {
        if (!isset($transaction['date']) || !isset($transaction['value']) || !isset($transaction['description'])) {
            return false;
        }

        if (empty($transaction['value'])) {
            return false;
        }

        return true;
    }

    /**
     * Replace any invalid chars and defaults back to the current year due to IA reading problems
     * If date fails, it will fall back to today's date
     *
     * @param string $date
     * @return string
     */
    private function formatCorrectDateCurrentYear(string $date): string
    {
        $dateFormatted = date('Y-m-d');

        if (str_contains($date, 'YYYY')) {
            $currentYear = date('Y');
            $dateFormatted = str_replace('YYYY', $currentYear, $date);
        } else {
            try {
                $carbonDate = Carbon::parse($date);
                $carbonDate->setYear((int) date('Y'));
                $dateFormatted = $carbonDate->format('Y-m-d');
            } catch (\Throwable $th) {
                Log::error('Error in formatting date extracted from IA', ['date' => $date, 'error' => $th->getMessage()]);
                $dateFormatted = date('Y-m-d');
            }
        }

        return $dateFormatted;
    }

    /**
     * Format current value to return a valid float value
     *
     * @param mixed $value
     * @return float
     */
    private function formatCorrectValue(mixed $value): float
    {
        if (!is_numeric($value)) {
            return (float) 0;
        }
        return (float) $value;
    }

    /**
     * Format current invoice number to return a valid int value or null
     *
     * @param mixed $invoiceNumber
     * @return int|null
     */
    private function formatCorrectInvoiceNumber(mixed $invoiceNumber): ?int
    {
        if (!is_numeric($invoiceNumber)) {
            return null;
        }
        return (int) $invoiceNumber;
    }

    /**
     * Truncate big descriptions
     *
     * @param string $description
     * @return string
     */
    private function truncateBigDescriptions(string $description): string
    {
        $maxLength = 255;

        if (strlen($description) > $maxLength) {
            return substr($description, 0, $maxLength);
        }

        return $description;
    }
}
