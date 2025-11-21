<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\OfxParseException;

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
}
