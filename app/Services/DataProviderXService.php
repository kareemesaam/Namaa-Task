<?php

namespace App\Services;

use App\Contracts\DataProviderInterface;
use App\Listeners\JsonStreamListener;
use JsonStreamingParser\Parser;

class DataProviderXService implements DataProviderInterface
{
    protected $filePath;

    public function __construct() {
        $this->filePath = storage_path('app/public/DataProviderX.json');
    }
    public function fetchData($statusCode = null, $balanceMin = null, $balanceMax = null, $currency = null): array {

        $stream = fopen($this->filePath, 'r');

        $listener = new JsonStreamListener(function($object) use ($statusCode, $balanceMin, $balanceMax, $currency) {
            if ($this->applyFilters($object, $statusCode, $balanceMin, $balanceMax, $currency)) {
                $this->filteredTransactions[] = $object;
            }
        });

        try {
            $parser = new Parser($stream, $listener);
            $parser->parse();
        } finally {
            fclose($stream);
        }

        return $this->filteredTransactions;
    }

    protected function readFileGenerator() {
        $jsonContents = file_get_contents($this->filePath);
        if ($jsonContents === false) {
            throw new \RuntimeException("Cannot read the file: {$this->filePath}");
        }

        $dataObjects = json_decode($jsonContents, true);
        if (!is_array($dataObjects)) {
            throw new \UnexpectedValueException("The decoded JSON structure is not an array.");
        }

        foreach ($dataObjects as $dataObject) {
            yield $dataObject;
        }
    }



    protected function processBuffer(&$buffer, array $filters, array &$results, $flush = false): string {
        // This is a simplified logic for demonstration purposes.
        // You'll need to implement JSON chunk processing based on your file's structure.
        // For a real application, consider using a streaming JSON parser.

        if ($flush) {
            // Assuming the remaining buffer contains a complete JSON object
            $data = json_decode('[' . $buffer . ']', true);
            if ($data) {
                // Apply filters to $data and add to $results
            }
            $buffer = '';
        }

        // Return the unprocessed part of the buffer or an empty string if everything was processed
        return $buffer;
    }

    protected function applyFilters($transaction, $statusCode, $balanceMin, $balanceMax, $currency) {
        $mappedStatusCode = $statusCode ? $this->mapStatusCode($statusCode) : null;

        if ($mappedStatusCode && $transaction['statusCode'] != $mappedStatusCode) return false;
        if ($currency && $transaction['Currency'] != $currency) return false;
        if ($balanceMin !== null && $transaction['parentAmount'] < $balanceMin) return false;
        if ($balanceMax !== null && $transaction['parentAmount'] > $balanceMax) return false;

        return true;
    }

    protected function mapStatusCode($statusCode) {
        $mapping = [
            'authorised' => 1,
            'decline' => 2,
            'refunded' => 3,
        ];
        return $mapping[$statusCode] ?? null;
    }
}
