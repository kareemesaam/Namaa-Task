<?php

namespace App\Contracts;

interface DataProviderInterface
{
    public function fetchData(array $filters): array;
}
