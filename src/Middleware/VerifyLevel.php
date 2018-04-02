<?php

namespace Larrock\ComponentUsers\Roles\Middleware;

use Cache;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

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
        if ($this->auth->check()) {
            $cache_key = sha1('userLevel'.$this->auth->id());
            $userLevel = Cache::rememberForever($cache_key, function () {
                return $this->auth->user()->level();
            });
            if ($userLevel >= $level) {
                return $next($request);
            }
        }

        \Session::push('message.danger', 'Не достаточно прав для выполнения операции');

        return redirect()->to('/user');
        //throw new LevelDeniedException($level);
    }
}
