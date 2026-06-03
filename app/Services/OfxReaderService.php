<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\OfxParseException;
use App\Services\IA\ExtractedTransactionDTO;
use Carbon\Carbon;

class OfxReaderService
{
    public function read(string $filePath): array
    {
        $ofxContent = file_get_contents($filePath);

        // Find the line where <OFX> starts
        $start = strpos($ofxContent, '<OFX>');
        if ($start === false) {
            throw new OfxParseException('No <OFX> tag found in file.');
        }

        // Grab everything from <OFX> to the end
        $ofxXml = substr($ofxContent, $start);

        // Try to parse as XML
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($ofxXml);
        if ($xml === false) {
            $errors = libxml_get_errors();
            $errorMsg = "Failed to parse OFX XML:\n";
            foreach ($errors as $error) {
                $errorMsg .= $error->message . "\n";
            }
            libxml_clear_errors();
            throw new OfxParseException($errorMsg);
        }
        libxml_clear_errors();

        // Convert SimpleXMLElement to array and return
        return json_decode(json_encode($xml), true);
    }

    public function extractTransactionsOfx(string $ofxFilePath): array
    {
        $content = $this->read($ofxFilePath);

        $transactions = $content['BANKMSGSRSV1']['STMTTRNRS']['STMTRS']['BANKTRANLIST']['STMTTRN'] ?? [];
        $extractedTransactions = [];

        foreach ($transactions as $transaction) {
            $value = isset($transaction['TRNAMT']) ? (float) $transaction['TRNAMT'] : 0.0;
            $description = $transaction['MEMO'] ?? '';
            $refnum = $transaction['REFNUM'] ?? null;

            if (empty($description)) {
                $description = $value > 0 ? 'Entrada/Resgate' : 'Despesa/Aplicação';
            }

            // Parse OFX date format: 20251014112549[-3:BRT]
            $dtPostedRaw = $transaction['DTPOSTED'] ?? null;
            $datePosted = null;

            if ($dtPostedRaw) {
                // Extract only the date/time part (first 14 digits)
                if (preg_match('/^(\d{14})/', $dtPostedRaw, $matches)) {
                    $datePosted = Carbon::createFromFormat('YmdHis', $matches[1]);
                }
            }

            $extractedTransactions[] = new ExtractedTransactionDTO(
                description: $description,
                value: $value,
                date: $datePosted?->toDateString() ?? null,
                refnum: $refnum
            );
        }

        return $extractedTransactions;
    }
}
