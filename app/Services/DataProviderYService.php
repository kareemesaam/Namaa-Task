<?php

namespace App\Services;

use App\Contracts\DataProviderInterface;
use App\Listeners\JsonStreamListener;
use JsonStreamingParser\Parser;

class DataProviderYService implements DataProviderInterface
{
    protected $filePath;
    public function __construct() {
        $this->filePath = storage_path('app/public/DataProviderY.json');
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

    protected function applyFilters($transaction, $statusCode, $balanceMin, $balanceMax, $currency) {
        $mappedStatusCode = $statusCode ? $this->mapStatusCode($statusCode) : null;

        if ($mappedStatusCode && $transaction['status'] != $mappedStatusCode) return false;
        if ($currency && $transaction['currency'] != $currency) return false;
        if ($balanceMin !== null && $transaction['balance'] < $balanceMin) return false;
        if ($balanceMax !== null && $transaction['balance'] > $balanceMax) return false;

        return true;
    }
    protected function mapStatusCode($genericStatusCode) {
        $statusMap = [
            'authorised' => 100,
            'decline' => 200,
            'refunded' => 300,
        ];

        return $statusMap[$genericStatusCode] ?? null;
    }


}
