<?php 

namespace Barryvdh\HttpCache\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTtl
{
    /**
     * Set the Time-To-Live in seconds on the incoming request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int $seconds
     * @return mixed
     */
    public function handle($request, Closure $next, $seconds = 60)
    {
        $response = $next($request);

        if ($response instanceof Response && $request instanceof Request && $request->isMethodCacheable()) {
            $response->setTtl($seconds);
        }

        return $response;
    }
}
