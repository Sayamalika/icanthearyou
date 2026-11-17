<?php

namespace App\Http\Middleware;

use App\Auth\DatabaseUser;
use Closure;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    private const ROLE_IDS = [
        'administrator' => 1,
        'cashier' => 2,
        'warehouse' => 3,
    ];

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowedRoleIds = [];

        foreach ($roles as $role) {
            if (! array_key_exists($role, self::ROLE_IDS)) {
                throw new InvalidArgumentException("Unknown role [{$role}] configured for route middleware.");
            }

            $allowedRoleIds[] = self::ROLE_IDS[$role];
        }

        $user = $request->user();

        abort_unless(
            $user instanceof DatabaseUser && in_array($user->idrole, $allowedRoleIds, true),
            403
        );

        return $next($request);
    }
}
