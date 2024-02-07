<?php

namespace Tests\Unit;

use App\Services\UserService;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    public function test_it_fetches_data_from_all_providers() {
        $userService = new UserService();

        $users = $userService->getUsers();

        $this->assertNotEmpty($users);
    }

    public function test_it_filters_data_by_provider() {
        $userService = new UserService();

        $users = $userService->getUsers($provider = 'DataProviderX');

        $this->assertNotEmpty($users);

    }


    public function test_it_returns_empty_array_for_invalid_provider() {
        $userService = new UserService();

        $users = $userService->getUsers($provider = 'InvalidProvider');

        $this->assertEmpty($users);
    }


    public function test_it_filters_data_with_all_inputs() {
        $userService = new UserService();

        // Define test parameters
        $provider = 'DataProviderX'; // Valid provider
        $statusCode = 'authorised'; // Valid status code
        $minAmount = 100; // Valid minimum amount
        $maxAmount = 500; // Valid maximum amount

        // Get users with all specified inputs
        $users = $userService->getUsers($provider, $statusCode, $minAmount, $maxAmount);

        // Check if users are not empty
        $this->assertNotEmpty($users);



    }



}
