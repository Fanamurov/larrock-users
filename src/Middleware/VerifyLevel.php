<?php

namespace Larrock\ComponentUsers\Roles\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class VerifyLevel
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
     * @param int $level
     * @return mixed
     */
    public function handle($request, Closure $next, $level)
    {
        if ($this->auth->check() && $this->auth->user()->level() >= $level) {
            return $next($request);
        }

        \Session::push('message.danger', 'Не достаточно прав для выполнения операции');
        return redirect()->to('/user');
        //throw new LevelDeniedException($level);
    }
}