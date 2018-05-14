<?php 

namespace Barryvdh\HttpCache\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\HttpCache\Esi;

class ParseEsi
{
    /**
     * Esi Middleware adds a Surrogate-Control HTTP header when the Response needs to be parsed for ESI.
     * @param Esi $esi
     */
    public function __construct(Esi $esi = null)
    {
        $this->esi = $esi;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( ! $request instanceof Request) {
            $request = Request::createFromBase($request);
        }

        $response = $next($request);

        if (!is_null($this->esi)) {
            $this->esi->addSurrogateControl($response);
        }

        return $response;
    }
}
