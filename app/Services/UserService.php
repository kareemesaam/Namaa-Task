<?php

namespace App\Services;

use App\Factories\ProviderDataHandlerFactory;

class UserService
{
    public function getUsers($provider = null, $statusCode = null, $balanceMin = null, $balanceMax = null, $currency = null) {
        $filteredTransactions = [];

        // Create instances of the appropriate provider data handlers
        $handlers = ProviderDataHandlerFactory::createHandler($provider);

        foreach ($handlers as $handler) {
            // Read data from each provider and merge
            $providerTransactions = $handler->fetchData($statusCode, $balanceMin, $balanceMax, $currency);
            $filteredTransactions = array_merge($filteredTransactions, $providerTransactions);
        }

        return $filteredTransactions;
    }
}
