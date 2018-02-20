<?php

namespace Larrock\ComponentUsers\Roles\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Larrock\ComponentUsers\Roles\Exceptions\PermissionDeniedException;

class VerifyPermission
{
    /** @var Guard */
    protected $auth;

    /**
     * Create a new filter instance.
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     * @param Request $request
     * @param \Closure $next
     * @param int|string $permission
     * @return mixed
     * @throws \Larrock\ComponentUsers\Roles\Exceptions\PermissionDeniedException
     */
    public function handle($request, Closure $next, $permission)
    {
        if ($this->auth->check() && $this->auth->user()->hasPermission($permission)) {
            return $next($request);
        }

        \Session::push('message.danger', 'Не достаточно прав для выполнения операции');
        throw new PermissionDeniedException($permission);
    }
}