<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Libraries\JSend;

use GenTux\Jwt\GetsJwtToken;

class ManagerAndAccessor
{
     use GetsJwtToken;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @throws \League\OAuth2\Server\Exception\AccessDeniedException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $payload                    = $this->jwtPayload();

        if (in_array($payload['context']['role'], ['store_manager', 'admin'])) 
        {
            return $next($request);
        }

        return response()->json( JSend::error(['Unautorized User'])->asArray());
    }
}

