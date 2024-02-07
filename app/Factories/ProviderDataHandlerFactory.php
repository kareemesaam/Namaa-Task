<?php

namespace App\Factories;
use App\Contracts\DataProviderInterface;
use App\Services\DataProviderXService;
use App\Services\DataProviderYService;
class ProviderDataHandlerFactory
{
    public static function createHandler($provider = null): array {
        $handlers = [];

        if (null === $provider || $provider === 'DataProviderX') {
            $handlers['DataProviderX'] = new DataProviderXService();
        }
        if (null === $provider || $provider === 'DataProviderY') {
            $handlers['DataProviderY'] = new DataProviderYService();
        }
        // Add other conditions for additional providers

        return $handlers;
    }
}
