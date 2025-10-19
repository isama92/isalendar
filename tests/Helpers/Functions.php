<?php

use App\Enums\Role;
use App\Models\User;

function actingAsAdmin(?User $user = null): User
{
    $user ??= test()->user;
    $user->assignRole(Role::SUPER_ADMIN);
    test()->actingAs($user);
    test()->user = $user;

    return $user;
}
function actingAsUser(?User $user = null): User
{
    $user ??= test()->user;
    $user->assignRole(Role::USER);
    test()->actingAs($user);
    test()->user = $user;

    return $user;
}
