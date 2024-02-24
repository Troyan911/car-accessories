<?php

namespace Tests;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function getUser(Roles $role): User
    {
        return User::role($role->value)->firstOrFail();
    }

    protected function actingAsRole(Roles $role) {
        return $this->actingAs($this->getUser($role));
    }
}
